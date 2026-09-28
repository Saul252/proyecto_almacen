<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/movimientosModel.php';

protegerPagina('movimientos'); // o quítalo si será público con token

$modelo = new MovimientoModel($conexion);

// 🔹 Limpiar salida SIEMPRE
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

$usuario_id = $_SESSION['usuario_id'] ?? 0;
$rol_id     = $_SESSION['rol_id'] ?? 0;

try {

    switch ($action) {

        /**
         * =========================================
         * 🔹 RESUMEN (ARRIBOS + ENVÍOS)
         * =========================================
         * URL:
         * /api/movimientosApiController.php?action=resumen&almacen_id=1
         */
        case 'resumen':

            $almacen_id = $_GET['almacen_id'] ?? 0;

            $resultado = $modelo->obtenerMovimientosPorRol(
                $usuario_id,
                $rol_id,
                $almacen_id
            );

            if ($resultado['status']) {
                echo json_encode($resultado['data']);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $resultado['msg']
                ]);
            }

        break;
        case 'traspasar':

    // 🔹 Datos POST
    $producto_id = $_POST['producto_id'] ?? 0;
    $origen_id   = $_POST['almacen_origen_id'] ?? 0;
    $destino_id  = $_POST['almacen_destino_id'] ?? 0;
    $cantidad    = $_POST['cantidad'] ?? 0;
    $obs         = $_POST['observaciones'] ?? '';
    $usuario_id  = $_SESSION['usuario_id'] ?? 0;

    // 🔹 Validación
    if (!$producto_id || !$origen_id || !$destino_id || $cantidad <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Datos incompletos para el traspaso'
        ]);
        break;
    }

    if ($origen_id == $destino_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Origen y destino no pueden ser iguales'
        ]);
        break;
    }

    // 🔹 Llamar al modelo
    $resultado = $modelo->registrarTraspaso(
        $producto_id,
        $origen_id,
        $destino_id,
        $cantidad,
        $usuario_id,
        $obs
    );

    // 🔹 Respuesta
    if ($resultado['status']) {
        echo json_encode([
            'status' => 'success',
            'message' => $resultado['msg']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => $resultado['msg']
        ]);
    }

break;


        /**
         * =========================================
         * 🔹 FUTURO: DETALLE DE MOVIMIENTO
         * =========================================
         */
        case 'detalle':
            echo json_encode([
                'status' => 'error',
                'message' => 'Endpoint no implementado aún'
            ]);
        break;

case 'recibirTraspaso':

    // 🔹 Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ]);
        exit;
    }

    $movimiento_id = $_POST['id'] ?? 0;
   
    if (!$movimiento_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de movimiento requerido'
        ]);
        exit;
    }

    // 🔹 Ejecutar lógica en el modelo
    $resultado = $modelo->recibirTraspaso(
        intval($movimiento_id),
        intval($usuario_id),
        intval($rol_id),
        
    );

    // 🔹 Respuesta JSON estándar
    if ($resultado['status']) {
        echo json_encode([
            'status' => 'success',
            'message' => $resultado['message']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => $resultado['message']
        ]);
    }

break;
        /**
         * =========================================
         * 🔹 DEFAULT
         * =========================================
         */
        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Acción no válida'
            ]);
        break;
    }

} catch (Exception $e) {

    error_log("API Movimientos ERROR: " . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ]);
}

exit;