<?php

    // ... (los demás métodos para categorías y clientes se mantienen igual)
    class VentasModel {
    public static function obtenerProductos($conexion, $almacen_id = 0) {
        // SQL Robusto: Une Inventario para stock y Precios_Producto para el costo actual en ESE almacén
        $sql = "SELECT 
                    p.id, 
                    p.sku, 
                    p.nombre, 
                    p.unidad_medida, 
                    p.unidad_reporte, 
                    p.factor_conversion, 
                    p.categoria_id,
                    i.stock, 
                    i.almacen_id, 
                    a.nombre AS almacen_nombre,
                    pp.precio_minorista, 
                    pp.precio_mayorista, 
                    pp.precio_distribuidor
                FROM productos p
                INNER JOIN inventario i ON p.id = i.producto_id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN precios_producto pp ON (p.id = pp.producto_id AND i.almacen_id = pp.almacen_id)
                WHERE p.activo = 1";

        if ($almacen_id > 0) {
            $sql .= " AND i.almacen_id = " . intval($almacen_id);
        }

        $sql .= " ORDER BY a.nombre ASC, p.nombre ASC";
        
        return $conexion->query($sql);
    }   
     public static function obtenerProductosVendedor($conexion) {
        // SQL Robusto: Une Inventario para stock y Precios_Producto para el costo actual en ESE almacén
        $sql = "SELECT p.id, p.sku, p.nombre, p.unidad_medida, p.unidad_reporte, p.factor_conversion, p.categoria_id, pp.precio_minorista, pp.precio_mayorista, pp.precio_distribuidor FROM productos p LEFT JOIN precios_producto pp ON (p.id =pp.producto_id ) WHERE p.activo = 1 GROUP by p.id;
";

      
        $sql .= " ORDER BY p.nombre ASC";
        
        return $conexion->query($sql);
    }
      public static function obtenerProductosFiltrados($conexion, $almacen_id = 0,$categoria=null) {
      
        // SQL Robusto: Une Inventario para stock y Precios_Producto para el costo actual en ESE almacén
        $sql = "SELECT 
                    p.id, 
                    p.sku, 
                    p.nombre, 
                    p.unidad_medida, 
                    p.unidad_reporte, 
                    p.factor_conversion, 
                    p.categoria_id,
                    i.stock, 
                    i.almacen_id, 
                    a.nombre AS almacen_nombre,
                    pp.precio_minorista, 
                    pp.precio_mayorista, 
                    pp.precio_distribuidor
                FROM productos p
                INNER JOIN inventario i ON p.id = i.producto_id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN precios_producto pp ON (p.id = pp.producto_id AND i.almacen_id = pp.almacen_id)
                WHERE p.activo = 1";
$params = [];
    $types = "";
        if ($almacen_id > 0) {
            $sql .= " AND i.almacen_id = " . intval($almacen_id);
        }
          if (!empty($categoria)) {
        $sql .= " AND p.categoria_id = ?";
        $types .= "i";
        $params[] = $categoria;
    }

        $sql .= " ORDER BY a.nombre ASC, p.nombre ASC";
        
        return $conexion->query($sql);
    }
public static function procesarVenta($conexion, $data, $id_usuario) {
    $conexion->begin_transaction();

    try {
        $id_cliente   = intval($data['id_cliente']);
        $vendedor_id  = intval($data['id_vendedor'] ?? 0);
        $descuento    = floatval($data['descuento'] ?? 0);
        $obs          = ($data['observaciones'] ?? 0);
        $carrito      = $data['carrito'] ?? [];
        $monto_pagado = floatval($data['monto_pagado'] ?? 0);

        // 1. Manejo seguro de subtotal cuando viene en Array
        $raw_subtotal = $data['subtotal'] ?? 0;
        $subtotal = is_array($raw_subtotal) ? floatval($raw_subtotal[0] ?? 0) : floatval($raw_subtotal);

        $monto_favor = floatval($data['monto_usado_favor'] ?? 0);

        if ($monto_favor > 0 && $monto_favor == $monto_pagado) {
            $metodo_pago = 'Saldo a Favor';
            $efectivoPagado = 0;
        } else {
            $metodo_pago = $data['metodo_pago'] ?? 'Efectivo';
            $efectivoPagado = floatval($data['efectivoPagado'] ?? 0);
        }

        // VALIDACIÓN DE STOCK Y CÁLCULO DE TOTALES
        $total_vendido_global = 0;
        $total_entregado_global = 0;

        foreach ($carrito as $key => $item) {
            $p_id = intval($item['producto_id']);
            $alm_id = intval($item['almacen_id']);
            $entrega_solicitada = floatval($item['entrega_hoy'] ?? 0);

            $stmtS = $conexion->prepare("SELECT stock FROM inventario WHERE producto_id = ? AND almacen_id = ? FOR UPDATE");
            $stmtS->bind_param("ii", $p_id, $alm_id);
            $stmtS->execute();
            $stockActual = floatval($stmtS->get_result()->fetch_assoc()['stock'] ?? 0);

            if ($entrega_solicitada > $stockActual) {
                $carrito[$key]['entrega_hoy'] = $stockActual;
            }

            $total_vendido_global += floatval($item['cantidad']);
            $total_entregado_global =0;
        }

        $total = $subtotal - $descuento;
  $id_almacen_vta = intval($carrito[0]['almacen_id'] ?? 0);
        // GENERAR FOLIO DINÁMICO
        $resFolio = $conexion->query("SELECT MAX(id) as ultimo_id FROM ventas where almacen_id=$id_almacen_vta ");
        $filaFolio = $resFolio->fetch_assoc();
        $proximo_id = ($filaFolio['ultimo_id'] ?? 0) + 1;

        if ($monto_pagado == 0) {
            $folio = "VR-" . str_pad($proximo_id, 2, "0", STR_PAD_LEFT);
        } else {
            $folio = "V-" . str_pad($proximo_id, 2, "0", STR_PAD_LEFT);
        }

       
$estado_entrega_vta = ($total_entregado_global >= $total_vendido_global) ? 'entregado' : (($total_entregado_global > 0) ? 'parcial' : 'pendiente');
$estado_pago = ($monto_pagado >= floatval(number_format($total, 2, '.', ''))) ? 'pagado' : (($monto_pagado > 0) ? 'parcial' : 'pendiente');

// --- SANITIZACIÓN DE VARIABLES (Evita el "Array to string conversion") ---
$folio        = is_array($folio) ? implode('', $folio) : strval($folio ?? '');
$id_cliente   = is_array($id_cliente) ? intval($id_cliente[0] ?? 0) : intval($id_cliente ?? 0);
$id_usuario   = is_array($id_usuario) ? intval($id_usuario[0] ?? 0) : intval($id_usuario ?? 0);
$subtotal     = floatval($subtotal ?? 0);
$descuento    = floatval($descuento ?? 0);
$total        = floatval($total ?? 0);

// Si $obs es un array (ej. $_POST['observaciones']), extraemos su texto o lo convertimos a string
if (is_array($obs)) {
    $obs = isset($obs['observaciones']) ? strval($obs['observaciones']) : implode(' ', $obs);
} else {
    $obs = strval($obs ?? '');
}

$vendedor_id = is_array($vendedor_id) ? intval($vendedor_id[0] ?? 0) : intval($vendedor_id ?? 0);


// INSERTAR CABECERA DE VENTA
$sqlV = "INSERT INTO ventas (folio, id_cliente, almacen_id, usuario_id, subtotal, descuento, total, estado_pago, estado_entrega, estado_general, observaciones, vendedor_id) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa', ?, ?)";

$stmtV = $conexion->prepare($sqlV);

if (!$stmtV) {
    die("Error en la preparación del SQL: " . $conexion->error);
}

$stmtV->bind_param(
    "siiidddsssi", 
    $folio, 
    $id_cliente, 
    $id_almacen_vta, 
    $id_usuario, 
    $subtotal, 
    $descuento, 
    $total, 
    $estado_pago, 
    $estado_entrega_vta, 
    $obs, 
    $vendedor_id
);

$stmtV->execute();
$id_venta = $conexion->insert_id;
        // REGISTRAR PAGO
        if ($monto_pagado > 0) {
            $monto_favor_valor = $monto_favor;
            $referencia = $data['referencia'] ?? '';

            $stmtP = $conexion->prepare("INSERT INTO historial_pagos (venta_id, usuario_id, monto, saldo_favor, metodo_pago, efectivoPagado, referencia) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtP->bind_param("iiddsds", $id_venta, $id_usuario, $monto_pagado, $monto_favor_valor, $metodo_pago, $efectivoPagado, $referencia);
            
            if (!$stmtP->execute()) {
                error_log("Error en historial_pagos: " . $stmtP->error);
            }
        }

        // PROCESAR ENTREGAS FÍSICAS E INVENTARIO
        $id_entrega_maestro = null;
        if ($total_entregado_global > 0) {
            $stmtE = $conexion->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha, observaciones) VALUES (?, ?, NOW(), ?)");
            $obs_e = "Entrega inicial. Folio: $folio";
            $stmtE->bind_param("iis", $id_venta, $id_usuario, $obs_e);
            $stmtE->execute();
            $id_entrega_maestro = $conexion->insert_id;
        }

        foreach ($carrito as $item) {
            $p_id            = intval($item['producto_id']);
            $alm_id          = intval($item['almacen_id']);
            $cant_ped        = floatval($item['cantidad']);
            $idunidadMedida  = floatval($item['idunidadMedida'] ?? 0);
            $cant_real       = floatval($item['entrega_hoy'] ?? 0); 
            $prec            = floatval($item['precio_unitario'] ?? 0);
            $subt            = floatval($item['subtotal'] ?? 0);
            $lote_id         = intval($item['lote_id'] ?? 0);
            
            $st_fila = ($cant_real >= $cant_ped) ? 'entregado' : (($cant_real > 0) ? 'parcial' : 'pendiente');
            
            $sqlD = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, unidadMedida, cantidad_entregada, precio_unitario, subtotal, estado_entrega) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtD = $conexion->prepare($sqlD);
            $stmtD->bind_param("iiddddds", $id_venta, $p_id, $cant_ped, $idunidadMedida, $cant_real, $prec, $subt, $st_fila);
            $stmtD->execute();
            $id_detalle_venta = $conexion->insert_id;

            $estado = 'reservado';
            if ($lote_id != 0) {
                $stmtLote = $conexion->prepare("UPDATE lotes_stock SET estado_lote = ? WHERE id = ?");
                $stmtLote->bind_param("si", $estado, $lote_id);
                $stmtLote->execute();

                $sqlR = "INSERT INTO lotes_reservados (lote_id, venta_id) VALUES (?, ?)";
                $stmtR = $conexion->prepare($sqlR);

                if (!$stmtR) {
                    throw new Exception("Error preparando reserva: " . $conexion->error);
                }

                $stmtR->bind_param("ii", $lote_id, $id_venta);

                if (!$stmtR->execute()) {
                    throw new Exception("Error guardando reserva: " . $stmtR->error);
                }
            }

            if ($cant_real > 0 && $id_entrega_maestro) {
                // Detalle entrega
                $stmtDE = $conexion->prepare("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)");
                $stmtDE->bind_param("iid", $id_entrega_maestro, $id_detalle_venta, $cant_real);
                $stmtDE->execute();

                // Actualizar Inventario
                $stmtInv = $conexion->prepare("UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?");
                $stmtInv->bind_param("dii", $cant_real, $p_id, $alm_id);
                $stmtInv->execute();
                
                // Kardex
                $mov_obs = "Salida Venta: $folio. Entregado: $cant_real / $cant_ped";
                $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                               VALUES (?, 'salida', ?, ?, ?, ?, ?)");
                $stmtMov->bind_param("idiiss", $p_id, $cant_real, $alm_id, $id_usuario, $id_venta, $mov_obs);
                $stmtMov->execute();
            }
        }

        $conexion->commit();
        return [
            'status' => 'success', 
            'id_venta' => $id_venta, 
            'folio' => $folio, 
            'total_pedido' => $total_vendido_global, 
            'total_entregado' => $total_entregado_global
        ];

    } catch (Exception $e) {
        $conexion->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public static function procesarVentaDesdeCotizacion($conexion, $data, $id_usuario)
{
    $conexion->begin_transaction();

    try {

        // =========================
        // DATOS GENERALES
        // =========================
        $descuento     = floatval($data['descuento'] ?? 0);
        $obs           = $data['observaciones'] ?? '';
        $monto_pagado  = floatval($data['monto_pagado'] ?? 0);
        $monto_favor   = floatval($data['monto_usado_favor'] ?? 0);
        $efectivoPagado = floatval($data['efectivoPagado'] ?? 0);
        $vendedor = floatval($data['vendedor'] ?? 0);

        // ahora el carrito ES el mismo array
        $carrito = array_filter($data, 'is_array');

        if (empty($carrito)) {
            throw new Exception("No hay productos para procesar");
        }

        // =========================
        // MÉTODO DE PAGO
        // =========================
        if ($monto_favor > 0 && $monto_favor == $monto_pagado) {
            $metodo_pago = 'Saldo a Favor';
        } else {
            $metodo_pago = $data['metodo_pago'] ?? 'Efectivo';
        }

        $subtotal = 0;
        $total_vendido_global = 0;
        $total_entregado_global = 0;

        $cliente_id = 0;
        $usuario_id = $id_usuario;
       

        // =========================
        // VALIDAR STOCK
        // =========================
        foreach ($carrito as $key => $item) {

            $p_id = intval($item['producto_id'] ?? 0);
            $alm_id = intval($item['almacen_origen_id'] ?? 0);
            $entrega_solicitada = floatval($item['entrega_hoy'] ?? 0);

            $cliente_id = intval($item['cliente_id'] ?? 0); 
          

            $stmtS = $conexion->prepare("
                SELECT stock 
                FROM inventario 
                WHERE producto_id = ? AND almacen_id = ?
                FOR UPDATE
            ");

            $stmtS->bind_param("ii", $p_id, $alm_id);
            $stmtS->execute();

            $stockActual = floatval(
                $stmtS->get_result()->fetch_assoc()['stock'] ?? 0
            );

            if ($entrega_solicitada > $stockActual) {
                $carrito[$key]['entrega_hoy'] = $stockActual;
            }

            $subtotal += floatval($item['subtotal'] ?? 0);
            $total_vendido_global += floatval($item['cantidad'] ?? 0);
            $total_entregado_global += floatval($carrito[$key]['entrega_hoy'] ?? 0);
        }

        $total = $subtotal - $descuento;

        // =========================
        // FOLIO
        // =========================
        $resFolio = $conexion->query("SELECT MAX(id) as ultimo_id FROM ventas");
        $filaFolio = $resFolio->fetch_assoc();

        $proximo_id = intval($filaFolio['ultimo_id'] ?? 0) + 1;
        $folio = "VC-" . str_pad($proximo_id, 5, "0", STR_PAD_LEFT);

        $id_almacen_vta = intval($carrito[0]['almacen_origen_id'] ?? 0);

        $estado_entrega_vta =
            ($total_entregado_global >= $total_vendido_global)
                ? 'entregado'
                : (($total_entregado_global > 0) ? 'parcial' : 'pendiente');

        $estado_pago =
            ($monto_pagado >= $total)
                ? 'pagado'
                : (($monto_pagado > 0) ? 'parcial' : 'pendiente');

        // =========================
        // CABECERA VENTA
        // =========================
        $sqlV = "
            INSERT INTO ventas (
                folio,
                id_cliente,
                almacen_id,
                usuario_id,
                subtotal,
                descuento,
                total,
                estado_pago,
                estado_entrega,
                estado_general,
                observaciones,
                vendedor_id
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa', ?,?)
        ";

        $stmtV = $conexion->prepare($sqlV);

        $stmtV->bind_param(
            "siiidddsssi",
            $folio,
            $cliente_id,
            $id_almacen_vta,
            $usuario_id,
            $subtotal,
            $descuento,
            $total,
            $estado_pago,
            $estado_entrega_vta,
            $obs,
            $vendedor
        );

        $stmtV->execute();

        $id_venta = $conexion->insert_id;

        // =========================
        // REGISTRAR PAGO
        // =========================
        if ($monto_pagado > 0) {

            $referencia = $data['referencia'] ?? '';

            $stmtP = $conexion->prepare("
                INSERT INTO historial_pagos
                (
                    venta_id,
                    usuario_id,
                    monto,
                    saldo_favor,
                    metodo_pago,
                    efectivoPagado,
                    referencia
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmtP->bind_param(
                "iiddsds",
                $id_venta,
                $id_usuario,
                $monto_pagado,
                $monto_favor,
                $metodo_pago,
                $efectivoPagado,
                $referencia
            );

            $stmtP->execute();
        }

        // =========================
        // ENTREGA
        // =========================
        $id_entrega_maestro = null;

        if ($total_entregado_global > 0) {

            $obs_e = "Entrega inicial. Folio: $folio";

            $stmtE = $conexion->prepare("
                INSERT INTO entregas_venta
                (venta_id, usuario_id, fecha, observaciones)
                VALUES (?, ?, NOW(), ?)
            ");

            $stmtE->bind_param("iis", $id_venta, $id_usuario, $obs_e);
            $stmtE->execute();

            $id_entrega_maestro = $conexion->insert_id;
        }

        // =========================
        // DETALLE
        // =========================
        foreach ($carrito as $item) {

            $p_id = intval($item['producto_id'] ?? 0);
            $alm_id = intval($item['almacen_origen_id'] ?? 0);

            $cant_ped = floatval($item['cantidad'] ?? 0);
            $cant_real = floatval($item['entrega_hoy'] ?? 0);

            $idunidadMedida = intval($item['unidadMedida'] ?? 0);

            $prec = floatval($item['precio_unitario'] ?? 0);
            $subt = floatval($item['subtotal'] ?? 0);

            $st_fila =
                ($cant_real >= $cant_ped)
                    ? 'entregado'
                    : (($cant_real > 0) ? 'parcial' : 'pendiente');

            $sqlD = "
                INSERT INTO detalle_venta
                (
                    venta_id,
                    producto_id,
                    cantidad,
                    unidadMedida,
                    cantidad_entregada,
                    precio_unitario,
                    subtotal,
                    estado_entrega
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmtD = $conexion->prepare($sqlD);

            $stmtD->bind_param(
                "iiddddds",
                $id_venta,
                $p_id,
                $cant_ped,
                $idunidadMedida,
                $cant_real,
                $prec,
                $subt,
                $st_fila
            );

            $stmtD->execute();

            $id_detalle_venta = $conexion->insert_id;

            if ($cant_real > 0 && $id_entrega_maestro) {

                $stmtDE = $conexion->prepare("
                    INSERT INTO detalle_entrega
                    (entrega_id, detalle_venta_id, cantidad)
                    VALUES (?, ?, ?)
                ");

                $stmtDE->bind_param(
                    "iid",
                    $id_entrega_maestro,
                    $id_detalle_venta,
                    $cant_real
                );

                $stmtDE->execute();

                $stmtInv = $conexion->prepare("
                    UPDATE inventario
                    SET stock = stock - ?
                    WHERE producto_id = ? AND almacen_id = ?
                ");

                $stmtInv->bind_param("dii", $cant_real, $p_id, $alm_id);
                $stmtInv->execute();
            }
        }

        $conexion->commit();

        return [
            'status' => 'success',
            'id_venta' => $id_venta,
            'folio' => $folio
        ];

    } catch (Exception $e) {

        $conexion->rollback();

        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
public static function actualizarEntregasCompletas($conexion, $id_venta)
{

    // 🔥 Traer todos los detalles de la venta
    $stmtDV = $conexion->prepare("
        SELECT id, cantidad
        FROM detalle_venta
        WHERE venta_id = ?
    ");

    $stmtDV->bind_param("i", $id_venta);

    $stmtDV->execute();

    $resDV = $stmtDV->get_result();

    while ($dv = $resDV->fetch_assoc()) {

        $dvid = intval($dv['id']);

        $cantidadVendida =
            floatval($dv['cantidad']);

        // =========================================
        // SUMAR ENTREGAS DE ESE DETALLE
        // =========================================

        $stmtSUM = $conexion->prepare("
            SELECT IFNULL(SUM(cantidad),0) AS total
            FROM detalle_entrega
            WHERE detalle_venta_id = ?
        ");

        $stmtSUM->bind_param("i", $dvid);

        $stmtSUM->execute();

        $rowSUM =
            $stmtSUM->get_result()->fetch_assoc();

        $totalEntregado =
            floatval($rowSUM['total']);

        // =========================================
        // REVISAR DIFERENCIA
        // =========================================

        $diferencia =
            abs($cantidadVendida - $totalEntregado);

        // =========================================
        // SI YA ESTÁ COMPLETO
        // =========================================

        if ($diferencia < 0.01) {

            $stmtUPD = $conexion->prepare("
                UPDATE detalle_venta
                SET
                    estado_entrega = 'entregado',
                    cantidad_entregada = ?
                WHERE id = ?
            ");

            // 🔥 guarda exacta la cantidad vendida
            $stmtUPD->bind_param(
                "di",
                $cantidadVendida,
                $dvid
            );

            $stmtUPD->execute();
        }
    }
}
   public static function cancelarVenta($conexion, $id_venta, $id_usuario, $motivo = 'Cancelación de venta') {
    $conexion->begin_transaction();

    try {
 
        // 1. Obtener datos de la venta y bloquear fila
        $stmtV = $conexion->prepare("SELECT estado_general, folio, almacen_id FROM ventas WHERE id = ? FOR UPDATE");
        $stmtV->bind_param("i", $id_venta);
        $stmtV->execute();
        $venta = $stmtV->get_result()->fetch_assoc();

        if (!$venta) throw new Exception("La venta no existe.");
        if ($venta['estado_general'] === 'cancelada') throw new Exception("Esta venta ya ha sido cancelada.");

        $folio = $venta['folio'];
        $id_almacen = $venta['almacen_id'];

        // 2. Consultar el detalle para devolver stock
        $stmtD = $conexion->prepare("SELECT producto_id, cantidad_entregada FROM detalle_venta WHERE venta_id = ?");
        $stmtD->bind_param("i", $id_venta);
        $stmtD->execute();
        $detalles = $stmtD->get_result();
                       

        while ($item = $detalles->fetch_assoc()) {
            $p_id = $item['producto_id'];
            $cant_entregada = floatval($item['cantidad_entregada']);

            if ($cant_entregada > 0) {
                // A. Reingreso al inventario
                $stmtInv = $conexion->prepare("UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?");
                $stmtInv->bind_param("dii", $cant_entregada, $p_id, $id_almacen);
                $stmtInv->execute();


                // B. Registro en Movimientos (Kardex) - El ENUM 'entrada' sí existe en tu tabla movimientos
                $mov_obs = "REINGRESO POR CANCELACIÓN - Folio: $id_venta. Motivo: $motivo";
                $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                               VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
                $stmtMov->bind_param("idiiss", $p_id, $cant_entregada, $id_almacen, $id_usuario, $id_venta, $mov_obs);
                $stmtMov->execute();
            }
        }

        // 3. Actualizar la cabecera (SOLO valores permitidos por tus ENUM)
        // estado_general permite 'cancelada'
        // NO tocamos estado_pago ni estado_entrega para evitar el error 'Data truncated'
        $stmtUpd = $conexion->prepare("UPDATE ventas SET estado_general = 'cancelada',  observaciones =? WHERE id = ?");
        $stmtUpd->bind_param("si",$motivo, $id_venta);
        $stmtUpd->execute();

        // 4. Limpiamos historial de pagos (opcional, pero recomendado para saldos)
        // Como tu tabla historial_pagos tiene ON DELETE CASCADE, si quisiéramos borrar:
        // $conexion->query("DELETE FROM historial_pagos WHERE venta_id = $id_venta");
        // O simplemente los dejamos ahí ya que la venta ya no es 'activa'.

        $conexion->commit();
        return ['status' => 'success', 'message' => "Venta $folio cancelada correctamente $motivo."];

    } catch (Exception $e) {
        $conexion->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
} 
public static function confirmarCancelacion($conexion, $id_venta, $motivo = 'Cancelación de venta') {
    $conexion->begin_transaction();

    try {
 
        $stmtUpd = $conexion->prepare("UPDATE ventas SET  observaciones = ? WHERE id = ?");
        $stmtUpd->bind_param("si",$motivo, $id_venta);
        $stmtUpd->execute();

        // 4. Limpiamos historial de pagos (opcional, pero recomendado para saldos)
        // Como tu tabla historial_pagos tiene ON DELETE CASCADE, si quisiéramos borrar:
        // $conexion->query("DELETE FROM historial_pagos WHERE venta_id = $id_venta");
        // O simplemente los dejamos ahí ya que la venta ya no es 'activa'.

        $conexion->commit();
        return ['status' => 'success', 'message' => "Venta  cancelada correctamente $motivo."];

    } catch (Exception $e) {
        $conexion->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
public static function cancelarEntregaProductos($conexion, $id_venta, $productos, $id_usuario, $motivo = 'Devolución parcial') {
    $conexion->begin_transaction();

    try {
        // 1. Obtener datos de la venta (Almacén y Folio)
        $stmtV = $conexion->prepare("SELECT folio, almacen_id, estado_general FROM ventas WHERE id = ? FOR UPDATE");
        $stmtV->bind_param("i", $id_venta);
        $stmtV->execute();
        $venta = $stmtV->get_result()->fetch_assoc();

        if (!$venta) throw new Exception("La venta no existe.");
        if ($venta['estado_general'] === 'cancelada') throw new Exception("No se puede ajustar una venta ya cancelada.");

        $id_almacen = $venta['almacen_id'];
        $folio = $venta['folio'];

        // 2. Procesar el array de productos a devolver
        // Estructura esperada: $productos = [ ['id' => 5, 'cant' => 2], ['id' => 8, 'cant' => 1] ]
        foreach ($productos as $item) {
            $p_id = intval($item['id']);
            $cant_a_devolver = floatval($item['cant']);

            if ($cant_a_devolver <= 0) continue;

            // A. Verificar cuánto se ha entregado realmente en detalle_venta
            $stmtD = $conexion->prepare("SELECT cantidad_entregada FROM detalle_venta WHERE venta_id = ? AND producto_id = ?");
            $stmtD->bind_param("ii", $id_venta, $p_id);
            $stmtD->execute();
            $detalle = $stmtD->get_result()->fetch_assoc();

            if (!$detalle) throw new Exception("Producto ID $p_id no encontrado en el detalle de esta venta.");
            
            $entregado_actual = floatval($detalle['cantidad_entregada']);

            // Validación crítica: No devolver más de lo que salió
            if ($cant_a_devolver > $entregado_actual) {
                throw new Exception("Error: Intentas devolver $cant_a_devolver del producto $p_id, pero solo se entregaron $entregado_actual.");
            }

            // B. Restar de cantidad_entregada en el detalle
            $stmtUpdDet = $conexion->prepare("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada - ? WHERE venta_id = ? AND producto_id = ?");
            $stmtUpdDet->bind_param("dii", $cant_a_devolver, $id_venta, $p_id);
            $stmtUpdDet->execute();

            // C. Reingreso al Inventario
            $stmtInv = $conexion->prepare("UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?");
            $stmtInv->bind_param("dii", $cant_a_devolver, $p_id, $id_almacen);
            $stmtInv->execute();

            // D. Registro en Movimientos (Kardex)
            $mov_obs = "DEVOLUCIÓN PARCIAL - Folio: $folio. Motivo: $motivo";
            $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                           VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
            $stmtMov->bind_param("idiiss", $p_id, $cant_a_devolver, $id_almacen, $id_usuario, $id_venta, $mov_obs);
            $stmtMov->execute();
        }

        // 3. Actualizar el estado de entrega de la venta (Opcional)
        // Esto es para que si devolviste todo, la venta pase a 'pendiente' o 'parcial'
        // Pero como mencionaste que hay ENUMS que dan error, solo lo haremos si es necesario.

        $conexion->commit();
        return ['status' => 'success', 'message' => "Se ajustaron las entregas del folio $folio correctamente."];

    } catch (Exception $e) {
        $conexion->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

}