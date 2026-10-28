<?php
require_once __DIR__ . '/../../includes/auth.php';

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/movimientosModel.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

protegerPagina('movimientos');

$modelo = new MovimientoModel($conexion);
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

/* =========================================================
   COMPROBANTE DE IMPRESIÓN DE MOVIMIENTO
   GET: ?action=imprimir_movimiento&id=123
   Devuelve JSON: { success: true, html: "..." }
   ========================================================= */
/* =========================================================
   DATOS DEL MOVIMIENTO PARA IMPRESIÓN (JSON)
   GET: ?action=imprimir_movimiento&id=123
   Devuelve SOLO los datos. El HTML lo construye el JS.
   ========================================================= */
if (isset($_GET['action']) && $_GET['action'] === 'imprimir_movimiento') {
    header('Content-Type: application/json; charset=utf-8');

    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de movimiento no proporcionado.']);
        exit;
    }

    try {
        $movimiento = $modelo->obtenerMovimientoParaImpresion($id);

        if (!$movimiento) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Movimiento no encontrado.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $movimiento
        ]);
        exit;

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener el movimiento: ' . $e->getMessage()
        ]);
        exit;
    }
}

/* =========================================================
   PETICIÓN AJAX (historial)
   GET: ?ajax=1&...
   ========================================================= */
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $datos = $modelo->obtenerHistorial($_GET, $almacen_usuario);
        echo json_encode(['data' => $datos]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

/* =========================================================
   CARGA NORMAL DE LA VISTA
   ========================================================= */
$paginaActual = 'movimientos';
require_once __DIR__ . '/../views/movimientos_view.php';