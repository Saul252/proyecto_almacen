<?php
/**
 * ventasHistorialController.php
 * Controlador para la gestión de Entregas y Abonos (Historial de Ventas)
 */

require_once __DIR__ . '/../../includes/auth.php'; // Tu función de seguridad
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventasHistorialModel.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/RepartosModel.php';
require_once __DIR__ . '/../models/usuariosModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/entregasModel.php';

require_once __DIR__ . '/../models/almacen/productosModel.php';
// En los require_once al inicio:
require_once __DIR__ . '/../models/ticketModel.php';

// En la sección de Instancias de Modelos:
$ticketModel = new VentasTicketModel($conexion);
// Instancias de Modelos
$sendModelo = new EntregaModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$modelo = new UsuarioModel($conexion);
$ventasModel = new VentaHistorialModel($conexion);
$clientesModel = new ClientesModel($conexion);
$repartosModel = new RepartoModel($conexion); // <-- Nombre correcto e inicializado

$productosModel = new ProductoModel($conexion);

if (isset($_GET['action']) && $_GET['action'] === 'obtenerTicketData') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id_venta = intval($_GET['id_venta'] ?? $_GET['id'] ?? 0);
        if ($id_venta <= 0) {
            throw new Exception('ID de venta no válido.');
        }

        $venta = $ticketModel->obtenerVentaPorId($id_venta);
        if (!$venta) {
            throw new Exception('Venta no encontrada.');
        }

        // El modelo ya devuelve directamente un array asociativo (gracias al fetch_all interno)
        $detalles = $ticketModel->obtenerDetallesVenta($id_venta);
        $pagos = $ticketModel->obtenerPagosVenta($id_venta);

        echo json_encode([
            'success' => true,
            'data' => [
                'venta' => $venta,
                'detalles' => $detalles ?: [],
                'pagos' => $pagos ?: []
            ]
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}