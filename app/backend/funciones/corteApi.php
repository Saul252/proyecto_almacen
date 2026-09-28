<?php
ob_start();
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../../includes/auth.php';
    require_once __DIR__ . '/../../../config/conexion.php';
    require_once __DIR__ . '/../../models/corteCajaModel.php';

    date_default_timezone_set('America/Mexico_City');
    $corteModel = new CorteCajaModel($conexion);
    
    $hoy = date('Y-m-d');
    $hora_actual = date('H:i');
    $action = $_GET['action'] ?? 'check_sistema';
    $es_forzado = ($action === 'forzar_corte');

    $almacen_sesion = isset($_SESSION['almacen_id']) ? intval($_SESSION['almacen_id']) : 0;
    $es_admin = ($almacen_sesion === 0);
    $hora_cierre_config = $es_admin ? '23:59' : ($_SESSION['hora_cierre'] ?? '18:00');
    error_log("proximo guardao automatico");

    // if ($es_forzado || $hora_actual >= $hora_cierre_config) {
        
    //     $almacenesAProcesar = [];
    //     if ($es_forzado && !$es_admin) {
    //         $almacenesAProcesar = [['id' => $almacen_sesion, 'nombre' => 'Tu Almacén']];
    //     } else {
    //         // Buscamos almacenes que tengan movimientos y no hayan cerrado hoy
    //         $almacenesAProcesar = $corteModel->obtenerAlmacenesPendientes($almacen_sesion, $es_admin, $hoy);
    //     }

    //     $procesados = [];
    //     $errores = [];

    //     foreach ($almacenesAProcesar as $alm) {
    //         try {
    //             // registrarCortePorAlmacen usará obtenerSumasCorte con todas las restas de saldo a favor
    //             $corteModel->registrarCortePorAlmacen($alm['id']);
    //             $procesados[] = $alm['nombre'];
    //         } catch (Exception $innerEx) {
    //             $errores[] = "Error en {$alm['nombre']}: " . $innerEx->getMessage();
    //         }
    //     }

    //     ob_clean();
    //     echo json_encode([
    //         'status' => (count($procesados) > 0) ? 'success' : 'idle',
    //         'procesados' => $procesados,
    //         'errores' => $errores,
    //         'mensaje' => $es_forzado ? 'Corte actualizado correctamente' : (count($procesados) > 0 ? 'Cierre de día completado' : 'No hay movimientos pendientes de cierre')
    //     ]);

    // } else {
    //     ob_clean();
    //     echo json_encode([
    //         'status' => 'idle', 
    //         'mensaje' => 'Esperando hora de cierre: ' . $hora_cierre_config
    //     ]);
    // }

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
exit;