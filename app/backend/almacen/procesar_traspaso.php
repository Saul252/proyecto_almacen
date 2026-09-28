<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/myvet';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'] ?? null;
    $origen_id   = $_POST['almacen_origen_id'] ?? null;
    $destino_id  = $_POST['almacen_destino_id'] ?? null;
    $cantidad    = $_POST['cantidad'] ?? 0;
    $obs         = $_POST['observaciones'] ?? '';
    $usuario_id  = $_SESSION['usuario_id'] ?? 1; 
    $lote  = $_POST['lote_id'] ?? '';

    if (!$producto_id || !$origen_id || !$destino_id || $cantidad <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos para el traspaso.']);
        exit;
    }

    $conexion->begin_transaction();

    try {
        // 1. Verificar stock actual en origen
        $stmtStock = $conexion->prepare("SELECT stock FROM inventario WHERE producto_id = ? AND almacen_id = ? FOR UPDATE");
        $stmtStock->bind_param("ii", $producto_id, $origen_id);
        $stmtStock->execute();
        $resStock = $stmtStock->get_result()->fetch_assoc();

        if (!$resStock || $resStock['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente en el almacén de origen.");
        }

        // 2. Restar stock del almacén de origen (Sale de su control)
        $stmtOut = $conexion->prepare("UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?");
        $stmtOut->bind_param("dii", $cantidad, $producto_id, $origen_id);
        $stmtOut->execute();

        // 3. Registrar en la tabla movimientos
        // Nota: usuario_envia_id se llena ahora. usuario_recibe_id queda NULL.
        $tipo = 'traspaso';
        $stmtMov = $conexion->prepare("INSERT INTO movimientos 
            (producto_id, tipo, cantidad, almacen_origen_id, almacen_destino_id, usuario_registra_id, usuario_envia_id, observaciones,referencia_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
        
        $stmtMov->bind_param("isdiiiisi", 
            $producto_id, $tipo, $cantidad, $origen_id, $destino_id, $usuario_id, $usuario_id, $obs,$lote
        );
        $stmtMov->execute();

        $conexion->commit();
        echo json_encode(['status' => 'success', 'message' => 'Envío registrado. Pendiente de recepción en destino.']);

    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}