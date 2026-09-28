<?php
// Limpiar cualquier salida previa para evitar errores de JSON
ob_start();

// Configuración de errores (solo para el log, no para la pantalla)
ini_set('display_errors', 0); 
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$movimiento_id = $_POST['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;
$rol_id = $_SESSION['rol_id'] ?? null;

if (!$movimiento_id || !$usuario_id) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Sesión o ID inválido']);
    exit;
}

$conexion->begin_transaction();

try {
    // 1. Obtener datos del movimiento
    $stmt = $conexion->prepare("SELECT producto_id, cantidad, almacen_origen_id, almacen_destino_id, usuario_recibe_id 
                                FROM movimientos WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $mov = $stmt->get_result()->fetch_assoc();

    if (!$mov) throw new Exception("El movimiento no existe.");
    if ($mov['usuario_recibe_id'] !== null) throw new Exception("Este traspaso ya fue recibido previamente.");

    $p_id = $mov['producto_id'];
    $dest_id = $mov['almacen_destino_id'];
    $orig_id = $mov['almacen_origen_id'];
    $cantidad = $mov['cantidad'];

    // --- BLOQUE PEPS: DESCUENTO EN ORIGEN ---
    // Usamos 'fecha_ingreso' y 'estado_lote' según tu SQL
    $sqlLotesOrig = "SELECT id, cantidad_actual, precio_compra_unitario FROM lotes_stock 
                     WHERE producto_id = ? AND almacen_id = ? AND estado_lote = 'activo' 
                     ORDER BY fecha_ingreso ASC FOR UPDATE";
    $stmtLO = $conexion->prepare($sqlLotesOrig);
    $stmtLO->bind_param("ii", $p_id, $orig_id);
    $stmtLO->execute();
    $resLotes = $stmtLO->get_result();

    $porRestar = $cantidad;
    $precio_historico = 0; // Para arrastrar el costo al nuevo lote

    while ($lote = $resLotes->fetch_assoc()) {
        if ($porRestar <= 0) break;
        
        $idLote = $lote['id'];
        $actual = $lote['cantidad_actual'];
        $precio_historico = $lote['precio_compra_unitario']; // Tomamos el precio del último lote afectado
        
        $aQuitar = ($actual <= $porRestar) ? $actual : $porRestar;
        $nuevoStock = $actual - $aQuitar;
        $nuevoEstado = ($nuevoStock <= 0) ? 'agotado' : 'activo';

        $upL = $conexion->prepare("UPDATE lotes_stock SET cantidad_actual = ?, estado_lote = ? WHERE id = ?");
        $upL->bind_param("dsi", $nuevoStock, $nuevoEstado, $idLote);
        $upL->execute();
        
        $porRestar -= $aQuitar;
    }

    if ($porRestar > 0) throw new Exception("No hay suficiente stock en los lotes del almacén de origen.");

    // --- BLOQUE: CREAR NUEVO LOTE EN DESTINO ---
    $nomLote = "L-TR-" . $movimiento_id . "-" . date('His');
    // Si no encontró lotes previos (por algún error de datos), usamos 0 en precio
    $precio_final = ($precio_historico > 0) ? $precio_historico : 0;

    $insLote = $conexion->prepare("INSERT INTO lotes_stock (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                                   VALUES (?, ?, ?, ?, ?, ?, 'activo')");
    $insLote->bind_param("iisddd", $p_id, $dest_id, $nomLote, $cantidad, $cantidad, $precio_final);
    $insLote->execute();

    // 2. Lógica de Inventario
    $stmtInv = $conexion->prepare("INSERT INTO inventario (almacen_id, producto_id, stock, stock_minimo, stock_maximo) 
                                   VALUES (?, ?, ?, 0, 0) 
                                   ON DUPLICATE KEY UPDATE stock = stock + ?");
    $stmtInv->bind_param("iidd", $dest_id, $p_id, $cantidad, $cantidad);
    $stmtInv->execute();

    // 3. Lógica de Precios
    $checkPrecios = $conexion->prepare("SELECT id FROM precios_producto WHERE producto_id = ? AND almacen_id = ?");
    $checkPrecios->bind_param("ii", $p_id, $dest_id);
    $checkPrecios->execute();
    
    if ($checkPrecios->get_result()->num_rows === 0) {
        $copyPrecios = $conexion->prepare("INSERT INTO precios_producto (producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor)
                                           SELECT producto_id, ?, precio_minorista, precio_mayorista, precio_distribuidor 
                                           FROM precios_producto WHERE producto_id = ? AND almacen_id = ? LIMIT 1");
        $copyPrecios->bind_param("iii", $dest_id, $p_id, $orig_id);
        $copyPrecios->execute();
    }

    // 4. Actualizar Movimiento
    $col_autoriza = ($rol_id == 1) ? ", usuario_autoriza_id = ?" : "";
    $sqlFinal = "UPDATE movimientos SET usuario_recibe_id = ?, fecha = CURRENT_TIMESTAMP $col_autoriza WHERE id = ?";
    
    $stmtFinal = $conexion->prepare($sqlFinal);
    if ($rol_id == 1) {
        $stmtFinal->bind_param("iii", $usuario_id, $usuario_id, $movimiento_id);
    } else {
        $stmtFinal->bind_param("ii", $usuario_id, $movimiento_id);
    }
    $stmtFinal->execute();

    $conexion->commit();
    
    ob_end_clean();
    echo json_encode(['status' => 'success', 'message' => "Material agregado a nuevo lote: $nomLote"]);

} catch (Exception $e) {
    if ($conexion->connect_errno === 0) { $conexion->rollback(); }
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}