<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

require_once __DIR__ . '/../models/logisticaModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
$almacenModel = new AlmacenModel($conexion);

$paginaActual = 'viajesTrabajadores';
protegerPagina('viajesTrabajadores');

$logisticaModel = new LogisticaModel($conexion);

// 🔥 Acción global
$action = $_GET['action'] ?? $_POST['action'] ?? '';


// ==========================================================
// 🚚 1. OBTENER VIAJES CON FILTROS
// ==========================================================
if ($action === 'listarViajes') {

    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    try {

        $data = $logisticaModel->obtenerViajesFiltradosPago(
            $_GET['almacen'] ?? 0,
            $_GET['fecha_inicio'] ?? null,
            $_GET['fecha_fin'] ?? null,
            $_GET['chofer'] ?? 0,
            $_GET['ayudante'] ?? '',
            $_GET['estado'] ?? ''
        );

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'aplicarPagoPorViaje') {

    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    try {

        $viaje_id  = intval($_POST['id'] ?? 0);
        $chofer_id = intval($_POST['chofer_id'] ?? 0);
        $monto     = floatval($_POST['monto'] ?? 0);
        $fecha     =  date('y-m-d');

        if ($viaje_id <= 0) {
            throw new Exception("ID del viaje no válido.");
        }

        if ($chofer_id <= 0) {
            throw new Exception("Chofer no válido.");
        }

        if ($monto <= 0) {
            throw new Exception("El monto debe ser mayor a cero.");
        }

        $logisticaModel->aplicarPagoPorViaje($viaje_id, $chofer_id, $monto,$fecha);

        echo json_encode([
            'success' => true,
            'message' => 'Pago registrado correctamente.'
        ]);

    } catch (Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'eliminarPagoPorViaje') {

    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    try {

        $viaje_id  = intval($_POST['id'] ?? 0);
        
        if ($viaje_id <= 0) {
            throw new Exception("ID del viaje no válido.");
        }

        $logisticaModel->eliminarPagoPorViaje($viaje_id);

        echo json_encode([
            'success' => true,
            'message' => 'Pago eliminado correctamente.'
        ]);

    } catch (Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// ==========================================================
// 📊 2. ESTADÍSTICAS (CHOFER + AYUDANTE)
// ==========================================================
if ($action === 'getEstadisticas') {

    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    try {

        

        $data = $logisticaModel->contarViajesPorPersona( 
            $_GET['almacen'] ?? 0,
            $_GET['fecha_inicio'] ?? null,
            $_GET['fecha_fin'] ?? null,
            $_GET['chofer'] ?? '',
            $_GET['ayudante'] ?? '',
            $_GET['estado'] ?? ''
           );

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}


// ==========================================================
// 📄 CARGA DE VISTA
// ==========================================================
 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($action)) {
    $almacen_sesion=$_SESSION['almacen_id'];
    $listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 

    require_once __DIR__ . '/../views/pagos_viajes.php';
    exit;
}