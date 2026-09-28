<?php
require_once __DIR__ . '/../../includes/auth.php';


require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/movimientosModel.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
protegerPagina('movimientos'); 
$modelo = new MovimientoModel($conexion);
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

// Si la petición es AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    try {
        $datos = $modelo->obtenerHistorial($_GET, $almacen_usuario);
        echo json_encode(['data' => $datos]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Carga normal

$paginaActual = 'movimientos';
require_once __DIR__ . '/../views/movimientos_view.php';