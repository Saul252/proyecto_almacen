<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

date_default_timezone_set('America/Mexico_City');
$path_conexion = $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

if (!file_exists($path_conexion)) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Ruta de conexion invalida']);
    exit;
}

require_once $path_conexion;
session_start();

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos JSON']);
    exit;
}


$conexion->begin_transaction();

try {
    $id_usuario   = $_SESSION['usuario_id'] ?? 1;
    $id_cliente   = intval($data['id_cliente']);
    $descuento    = floatval($data['descuento']);
    $obs          = $data['observaciones'] ?? '';
    $carrito      = $data['carrito'];
    
    $monto_pagado = floatval($data['monto_pagado']);
    $metodo_pago  = $data['metodo_pago'] ?? 'Efectivo';

    // 1. PRIMERA PASADA: Validación de Stock y Cálculo de Totales Reales
    $subtotal = 0;
    $total_vendido_global = 0;
    $total_entregado_global = 0;

    foreach ($carrito as $key => $item) {
        $p_id = intval($item['producto_id']);
        $alm_id = intval($item['almacen_id']);
        $entrega_solicitada = floatval($item['entrega_hoy']);

        // Consultar stock real disponible (bloqueo preventivo)
        $stmtS = $conexion->prepare("SELECT stock FROM inventario WHERE producto_id = ? AND almacen_id = ? FOR UPDATE");
        $stmtS->bind_param("ii", $p_id, $alm_id);
        $stmtS->execute();
        $stockActual = floatval($stmtS->get_result()->fetch_assoc()['stock'] ?? 0);

        // AJUSTE CRÍTICO: Si no hay suficiente, entregamos solo lo que hay
        if ($entrega_solicitada > $stockActual) {
            $carrito[$key]['entrega_hoy'] = $stockActual;
        }

        $subtotal += floatval($item['subtotal']);
        $total_vendido_global += floatval($item['cantidad']);
        $total_entregado_global += $carrito[$key]['entrega_hoy'];
    }
    $saldo_favor=0;
    $ref="";

    $total = $subtotal - $descuento;
    // Obtener el último ID para el folio (V01, V02, etc.)
$resFolio = $conexion->query("SELECT MAX(id) as ultimo_id FROM ventas");
$filaFolio = $resFolio->fetch_assoc();
$proximo_id = ($filaFolio['ultimo_id'] ?? 0) + 1;

// Generamos el folio: V + el ID con ceros a la izquierda (ejemplo: V01, V0012)
// str_pad le agrega ceros para que siempre tenga al menos 2 dígitos
$folio = "V-" . str_pad($proximo_id, 2, "0", STR_PAD_LEFT);
    $id_almacen_vta = intval($carrito[0]['almacen_id']);

    // Determinar estado de entrega global (Igual que en tu función procesarEntrega)
    $estado_entrega_vta = ($total_entregado_global >= $total_vendido_global) ? 'entregado' : (($total_entregado_global > 0) ? 'parcial' : 'pendiente');
    $estado_pago = ($monto_pagado >= $total) ? 'pagado' : (($monto_pagado > 0) ? 'parcial' : 'pendiente');

    // 2. INSERTAR CABECERA DE VENTA
    $sqlV = "INSERT INTO ventas (folio, id_cliente, almacen_id, usuario_id, subtotal, descuento, total, estado_pago, estado_entrega, estado_general, observaciones) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa', ?)";
    $stmtV = $conexion->prepare($sqlV);
    $stmtV->bind_param("siiidddsss", $folio, $id_cliente, $id_almacen_vta, $id_usuario, $subtotal, $descuento, $total, $estado_pago, $estado_entrega_vta, $obs);
    $stmtV->execute();
    $id_venta = $conexion->insert_id;

    // 3. REGISTRAR PAGO (Si existe)
    $saldo_favor=0;
    $efectivoPagado=0;
    $ref="";
    if ($monto_pagado > 0) {
        $stmtP = $conexion->prepare("INSERT INTO historial_pagos (venta_id, usuario_id, monto, saldo_favor, metodo_pago,efectivoPagado, referencia) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtP->bind_param("iidsd", $id_venta, $id_usuario, $monto_pagado,$saldo_favor, $metodo_pago,$efectivoPagado,$ref);
        $stmtP->execute();
    }

    // 4. CREAR CABECERA DE ENTREGA (Solo si se entregará algo hoy)
    $id_entrega_maestro = null;
    if ($total_entregado_global > 0) {
        $stmtE = $conexion->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha, observaciones) VALUES (?, ?, NOW(), ?)");
        $obs_e = "Entrega inicial generada en venta. Folio: $folio";
        $stmtE->bind_param("iis", $id_venta, $id_usuario, $obs_e);
        $stmtE->execute();
        $id_entrega_maestro = $conexion->insert_id;
    }

    // 5. PROCESAR CADA PRODUCTO (Detalle Venta + Detalle Entrega + Stock + Movimiento)
    foreach ($carrito as $item) {
        $p_id      = intval($item['producto_id']);
        $alm_id    = intval($item['almacen_id']);
        $cant_ped  = floatval($item['cantidad']);
        $cant_real = floatval($item['entrega_hoy']); // YA VALIDADA CONTRA STOCK
        $prec      = floatval($item['precio_unitario']);
        $subt      = floatval($item['subtotal']);
        
        // Estado por línea
        $st_fila = ($cant_real >= $cant_ped) ? 'entregado' : (($cant_real > 0) ? 'parcial' : 'pendiente');
        
        // Tipo de precio (limpieza de string)
        $raw_tp = strtolower($item['tipo_precio']);
        $tp = (strpos($raw_tp, 'dist') !== false) ? 'distribuidor' : ((strpos($raw_tp, 'may') !== false) ? 'mayorista' : 'minorista');

        // A. Insertar Detalle Venta (Aquí queda la "deuda" si cant_real < cant_ped)
        $sqlD = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, cantidad_entregada, precio_unitario, tipo_precio, subtotal, estado_entrega) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtD = $conexion->prepare($sqlD);
        $stmtD->bind_param("iiddssds", $id_venta, $p_id, $cant_ped, $cant_real, $prec, $tp, $subt, $st_fila);
        $stmtD->execute();
        $id_detalle_venta = $conexion->insert_id;

        // B. Si hay entrega física, afectar el resto de tablas (Como tu función procesarEntrega)
        if ($cant_real > 0 && $id_entrega_maestro) {
            
            // 1. Detalle de entrega física
            $stmtDE = $conexion->prepare("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)");
            $stmtDE->bind_param("iid", $id_entrega_maestro, $id_detalle_venta, $cant_real);
            $stmtDE->execute();

            // 2. Descontar Inventario
            $stmtInv = $conexion->prepare("UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?");
            $stmtInv->bind_param("dii", $cant_real, $p_id, $alm_id);
            $stmtInv->execute();
            
            // 3. Movimiento de Kardex
            $mov_obs = "Salida por venta folio: $folio. Entregado real: $cant_real de $cant_ped";
            $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                           VALUES (?, 'salida', ?, ?, ?, ?, ?)");
            $stmtMov->bind_param("idiiss", $p_id, $cant_real, $alm_id, $id_usuario, $id_venta, $mov_obs);
            $stmtMov->execute();
        }
    }
$conexion->commit();

    // Determinamos el mensaje personalizado
    $entrega_perfecta = ($total_entregado_global >= $total_vendido_global);
    
    if ($entrega_perfecta) {
        $mensaje = "✅ Venta completada: Se entregó la totalidad de los productos solicitados.";
    } else {
        // Calculamos cuánto quedó pendiente globalmente para el mensaje
        $pendiente_total = $total_vendido_global - $total_entregado_global;
        $mensaje = "⚠️ Venta procesada: Se entregaron solo " . number_format($total_entregado_global, 2) . " productos porque el stock estaba limitado. ";
        $mensaje .= "Quedan " . number_format($pendiente_total, 2) . " unidades pendientes. Favor de realizar una entrega posterior o hablar con su administrador.";
    }

    ob_clean();
    echo json_encode([
        'status'         => 'success', 
        'id_venta'       => $id_venta, 
        'folio'          => $folio,
        'entregado_total' => $entrega_perfecta,
        'message'        => $mensaje,
        // Opcional: enviamos el detalle para que el JS pueda pintar alertas rojas en productos específicos
        'resumen'        => [
            'pedido'    => $total_vendido_global,
            'entregado' => $total_entregado_global
        ]
    ]);

} catch (Exception $e) {
    if (isset($conexion)) $conexion->rollback();
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

