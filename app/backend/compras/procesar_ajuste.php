<?php
// Evitar que cualquier error previo ensucie la salida JSON
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Ajusta la ruta de conexión según tu estructura probada
require_once __DIR__ . '/../../../config/conexion.php';

// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario_id = $_SESSION['id_usuario'] ?? 1; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Iniciar Transacción
$conexion->begin_transaction();

try {
    $compra_id = isset($_POST['ajuste_compra_id']) ? intval($_POST['ajuste_compra_id']) : 0;
    $ajustes = $_POST['ajuste'] ?? [];

    if ($compra_id <= 0 || empty($ajustes)) {
        throw new Exception("Datos insuficientes o formulario vacío.");
    }

    foreach ($ajustes as $id_detalle_loop => $datos) {
        $detalle_id = intval($id_detalle_loop);
        $cantidad_recibida = floatval($datos['cantidad']);
        $producto_id = intval($datos['producto_id']);
        $almacen_id = intval($datos['almacen_id']);

        // Saltar si la cantidad es cero o negativa
        if ($cantidad_recibida <= 0) continue;

        // 1. ACTUALIZAR DETALLE_COMPRA
        // Importante: Primero restamos y luego evaluamos el estado
        $sqlUpdDetalle = "UPDATE detalle_compra 
                          SET cantidad_faltante = cantidad_faltante - ?,
                              estado_entrega = IF((cantidad_faltante - ?) <= 0, 'completo', 'ajustado')
                          WHERE id = ?";
        $stmtDet = $conexion->prepare($sqlUpdDetalle);
        $stmtDet->bind_param("ddi", $cantidad_recibida, $cantidad_recibida, $detalle_id);
        if (!$stmtDet->execute()) throw new Exception("Error actualizando detalle: " . $conexion->error);

        // 2. ACTUALIZAR FALTANTES_INGRESO
        $sqlUpdFaltante = "UPDATE faltantes_ingreso 
                           SET cantidad_pendiente = cantidad_pendiente - ? 
                           WHERE compra_id = ? AND producto_id = ?";
        $stmtFal = $conexion->prepare($sqlUpdFaltante);
        $stmtFal->bind_param("dii", $cantidad_recibida, $compra_id, $producto_id);
        $stmtFal->execute();

        // 3. ACTUALIZAR INVENTARIO (Usando la estructura de tu DB)
        // Usamos stock = stock + valor para ser precisos
        $sqlInv = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                   VALUES (?, ?, ?) 
                   ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
        $stmtInv = $conexion->prepare($sqlInv);
        $stmtInv->bind_param("iid", $almacen_id, $producto_id, $cantidad_recibida);
        if (!$stmtInv->execute()) throw new Exception("Error en inventario: " . $conexion->error);

        // 4. REGISTRAR MOVIMIENTO (Kardex)
        $obs = "Ajuste de faltante. Compra Ref: " . $compra_id;
        $tipo_mov = 'entrada';
        $sqlMov = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtMov = $conexion->prepare($sqlMov);
        $stmtMov->bind_param("isdiiss", $producto_id, $tipo_mov, $cantidad_recibida, $almacen_id, $usuario_id, $compra_id, $obs);
        $stmtMov->execute();
    }

    // Limpieza de tabla auxiliar
    $conexion->query("DELETE FROM faltantes_ingreso WHERE cantidad_pendiente <= 0");

    // 5. VERIFICAR SI QUEDAN FALTANTES TOTALES EN LA COMPRA
    $sqlCheck = "SELECT SUM(cantidad_faltante) as total_restante FROM detalle_compra WHERE compra_id = ?";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $compra_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result()->fetch_assoc();

    if (floatval($resCheck['total_restante']) <= 0) {
        $conexion->query("UPDATE compras SET tiene_faltantes = 0, estado = 'confirmada' WHERE id = $compra_id");
    }

    $conexion->commit();
    
    // Limpiar cualquier salida accidental y enviar JSON
    ob_clean();
    echo json_encode(['status' => 'success', 'message' => 'Entrada registrada y stock actualizado.']);

} catch (Exception $e) {
    $conexion->rollback();
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}