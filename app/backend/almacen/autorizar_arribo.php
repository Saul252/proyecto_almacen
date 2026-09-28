
   
<?php
ob_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$movimiento_id = $_POST['id'] ?? null;
$usuario_id    = $_SESSION['usuario_id'] ?? null;
$rol_id        = $_SESSION['rol_id'] ?? null;

if (!$movimiento_id || !$usuario_id) {
    ob_end_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesión o ID inválido'
    ]);
    exit;
}

$conexion->begin_transaction();

try {

    $observaciones = 'Arribo autorizado';

    // 1. Obtener movimiento
    $stmt = $conexion->prepare("
        SELECT producto_id, cantidad, almacen_origen_id, almacen_destino_id, usuario_recibe_id,referencia_id
        FROM movimientos
        WHERE id = ?
        FOR UPDATE
    ");

    if (!$stmt) throw new Exception($conexion->error);

    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();

    $mov = $stmt->get_result()->fetch_assoc();

    if (!$mov) {
        throw new Exception("El movimiento no existe.");
    }

    if ($mov['usuario_recibe_id'] !== null) {
        throw new Exception("Este traspaso ya fue recibido previamente.");
    }

    $p_id     = $mov['producto_id']??890;
    $dest_id  = $mov['almacen_destino_id']??980;
    $orig_id  = $mov['almacen_origen_id']??981;
    $cantidad = $mov['cantidad']??0;
$loteSeleccionado = $mov['referencia_id']??0;

    
    $porRestar = $cantidad;
    $precio_historico = 0;
    $lotesAfectados = [];
     if ($loteSeleccionado > 0) {
                $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario
                             FROM lotes_stock
                             WHERE id = $loteSeleccionado
                               AND producto_id = $p_id
                               AND almacen_id = $orig_id
                               AND cantidad_actual > 0";
                
            } else {
                $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario
                             FROM lotes_stock
                             WHERE producto_id = $p_id
                               AND almacen_id = $orig_id
                               AND cantidad_actual > 0
                               AND estado_lote = 'activo'
                             ORDER BY fecha_ingreso ASC, id ASC";
            }
$resLotes = $this->db->query($sqlLotes);
    while ($lote = $resLotes->fetch_assoc()) {

        if ($porRestar <= 0) break;

        $idLote = $lote['id'];
        $actual = $lote['cantidad_actual'];
        $precio_historico = $lote['precio_compra_unitario'];

        $aQuitar = ($actual <= $porRestar) ? $actual : $porRestar;

        $nuevoStock = $actual - $aQuitar;
        $nuevoEstado = ($nuevoStock <= 0) ? 'agotado' : 'activo';

        $upL = $conexion->prepare("
            UPDATE lotes_stock
            SET cantidad_actual = ?, estado_lote = ?
            WHERE id = ?
        ");

        if (!$upL) throw new Exception($conexion->error);

        $upL->bind_param("dsi", $nuevoStock, $nuevoEstado, $idLote);
        $upL->execute();

        $porRestar -= $aQuitar;

        $lotesAfectados[] = [
            'lote_id' => $idLote,
            'cantidad' => $aQuitar
        ];
    }

    if ($porRestar > 0) {
        throw new Exception("No hay suficiente stock en los lotes del almacén de origen.");
    }

    // 3. Crear lote destino
    $nomLote = "L-TR-" . $movimiento_id . "-" . date('His');
    $precio_final = ($precio_historico > 0) ? $precio_historico : 0;

    $insLote = $conexion->prepare("
        INSERT INTO lotes_stock (
            producto_id,
            almacen_id,
            codigo_lote,
            cantidad_inicial,
            cantidad_actual,
            precio_compra_unitario,
            estado_lote
        )
        VALUES (?, ?, ?, ?, ?, ?, 'activo')
    ");

    if (!$insLote) throw new Exception($conexion->error);

    $insLote->bind_param(
        "iisddd",
        $p_id,
        $dest_id,
        $nomLote,
        $cantidad,
        $cantidad,
        $precio_final
    );

    $insLote->execute();

    $idLoteNuevo = $conexion->insert_id;

    // 4. Inventario
    $stmtInv = $conexion->prepare("
        INSERT INTO inventario (
            almacen_id,
            producto_id,
            stock,
            stock_minimo,
            stock_maximo
        )
        VALUES (?, ?, ?, 0, 0)
        ON DUPLICATE KEY UPDATE stock = stock + ?
    ");

    if (!$stmtInv) throw new Exception($conexion->error);

    $stmtInv->bind_param("iidd", $dest_id, $p_id, $cantidad, $cantidad);
    $stmtInv->execute();

    // 5. Copiar precios
    $checkPrecios = $conexion->prepare("
        SELECT id
        FROM precios_producto
        WHERE producto_id = ?
        AND almacen_id = ?
    ");

    if (!$checkPrecios) throw new Exception($conexion->error);

    $checkPrecios->bind_param("ii", $p_id, $dest_id);
    $checkPrecios->execute();

    if ($checkPrecios->get_result()->num_rows === 0) {

        $copyPrecios = $conexion->prepare("
            INSERT INTO precios_producto (
                producto_id,
                almacen_id,
                precio_minorista,
                precio_mayorista,
                precio_distribuidor
            )
            SELECT
                producto_id,
                ?,
                precio_minorista,
                precio_mayorista,
                precio_distribuidor
            FROM precios_producto
            WHERE producto_id = ?
            AND almacen_id = ?
            LIMIT 1
        ");

        if (!$copyPrecios) throw new Exception($conexion->error);

        $copyPrecios->bind_param("iii", $dest_id, $p_id, $orig_id);
        $copyPrecios->execute();
    }

    // 6. Kardex
    $stmtKardex = $conexion->prepare("
        INSERT INTO kardex_movimientos_lotes (
            movimiento_id,
            lote_origen_id,
            lote_destino_id,
            producto_id,
            cantidad,
            usuario_id,
            observaciones
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmtKardex) throw new Exception($conexion->error);

    foreach ($lotesAfectados as $lote) {

        $lote_id = $lote['lote_id']??001;
        $cantidad_lote = $lote['cantidad'];

        $stmtKardex->bind_param(
            "iiiidis",
            $movimiento_id,
            $lote_id,
            $idLoteNuevo,
            $p_id,
            $cantidad_lote,
            $usuario_id,
            $observaciones
        );

        $stmtKardex->execute();
    }

    // 7. Actualizar movimiento
    $col_autoriza = ($rol_id == 1) ? ", usuario_autoriza_id = ?" : "";

    $sqlFinal = "
        UPDATE movimientos
        SET usuario_recibe_id = ?,
            origen_movimiento = ?,
            fecha = CURRENT_TIMESTAMP
            $col_autoriza
        WHERE id = ?
    ";

    $stmtFinal = $conexion->prepare($sqlFinal);

    if (!$stmtFinal) throw new Exception($conexion->error);

    if ($rol_id == 1) {

        $stmtFinal->bind_param(
            "iiii",
            $usuario_id,
            $idLoteNuevo,
            $usuario_id,
            $movimiento_id
        );

    } else {

        $stmtFinal->bind_param(
            "iii",
            $usuario_id,
            $idLoteNuevo,
            $movimiento_id
        );
    }

    $stmtFinal->execute();

    $conexion->commit();

    ob_end_clean();

    echo json_encode([
        'status' => 'success',
        'message' => "Material agregado a nuevo lote: $nomLote"
    ]);

} catch (Exception $e) {

    if ($conexion && !$conexion->connect_errno) {
        $conexion->rollback();
    }

    ob_end_clean();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit;