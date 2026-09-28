<?php
/**
 * pedidosVendedorController.php
 * Gestión de pedidos con filtrado por Base de Datos y Roles.
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

require_once __DIR__ . '/../models/pedidosVendedorModel.php';
require_once __DIR__ . '/../models/clientesEstatusModel.php';
require_once __DIR__ . '/../models/productosModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

$almacenModel = new AlmacenModel($conexion);
protegerPagina('pedidosVendedor');

$db = $conexion;
$pedidosModel = new PedidosVendedorModel($db);
$clientesModel = new ClientesEstatusModel($db);
$productosModel = new ProductosModel($db);

$paginaActual = 'pedidosVendedor';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$usuario_id = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['rol'] ?? ''; // Asumimos que el rol viene en la sesión

$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

if (isset($_GET['action']) || isset($_POST['action'])) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $action = $_GET['action'] ?? $_POST['action'];

        switch ($action) {
            case 'listar':
                // 1. Manejo de Fechas (Default: HOY)
                $desde = !empty($_GET['desde']) ? $_GET['desde'] : date('Y-m-d');
                $hasta = !empty($_GET['hasta']) ? $_GET['hasta'] : date('Y-m-d');
                $estatus = $_GET['estatus'] ?? 'todos';
                
                // 2. Determinar Filtros de Seguridad por Rol
                $filtro_almacen = null;
                $filtro_vendedor = null;

                if ($almacen_usuario != 0) {
                    // Si no es Admin Global, filtramos por su almacén
                    $filtro_almacen = $almacen_usuario;
                    
                    // Si es Vendedor, restringimos SOLO a sus folios
                    if ($rol_usuario === 'Vendedor') {
                        $filtro_vendedor = $usuario_id;
                    }
                    // Si es "Normal", $filtro_vendedor queda null y ve todo el almacén
                } else {
                    // Es Admin Global: puede filtrar por el almacén que elija en el select
                    $filtro_almacen = ($_GET['almacen_id'] != 0) ? intval($_GET['almacen_id']) : null;
                }

                // 3. Llamada al modelo con todos los parámetros
                // Debes actualizar tu Modelo para que acepte estos argumentos
                $data = $pedidosModel->listarConFiltros([
                    'desde'      => $desde,
                    'hasta'      => $hasta,
                    'almacen_id' => $filtro_almacen,
                    'vendedor_id'=> $filtro_vendedor,
                    'estatus'    => $estatus
                ]);

                echo json_encode(['success' => true, 'data' => $data]);
                break;

            case 'guardar':
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception("Método no permitido");
                
                $items = json_decode($_POST['items'] ?? '[]', true);
                if (empty($items)) throw new Exception("El pedido no tiene productos.");

                $almacen_destino = !empty($_POST['almacen_id']) ? intval($_POST['almacen_id']) : ($almacen_usuario ?? 0);

                if ($almacen_destino === 0) {
                    throw new Exception("Error: No se especificó una sucursal válida.");
                }

                $data = [
                    'vendedor_id'  => $usuario_id,
                    'cliente_id'   => intval($_POST['cliente_id'] ?? 0),
                    'almacen_id'   => $almacen_destino,
                    'prioridad'    => $_POST['prioridad'] ?? 'Media',
                    'observaciones'=> $_POST['observaciones'] ?? ''
                ];

                if ($data['cliente_id'] === 0) throw new Exception("Debe seleccionar un cliente.");

                $id_generado = $pedidosModel->crearPedido($data, $items);
                
                if ($id_generado) {
                    echo json_encode([
                        'success' => true, 
                        'message' => "Pedido guardado con éxito.", 
                        'id' => $id_generado
                    ]);
                } else {
                    throw new Exception("No se pudo guardar el pedido.");
                }
                break;

            case 'ver_detalle':
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                if ($id === 0) throw new Exception("ID no válido.");

                $pedido = $pedidosModel->obtenerPedidoPorId($id);
                if (!$pedido) throw new Exception("El pedido no existe.");

                $detalles = $pedidosModel->listarDetallesPorPedido($id);

                echo json_encode([
                    'success' => true,
                    'pedido'  => $pedido,
                    'detalles' => $detalles
                ]);
                break;

            case 'cubrir':
                $id = intval($_POST['id'] ?? 0);
                if ($id === 0) throw new Exception("ID no válido.");

                if ($pedidosModel->marcarComoCubierto($id)) {
                    echo json_encode(['success' => true, 'message' => "Pedido surtido."]);
                } else {
                    throw new Exception("Error al actualizar estatus.");
                }
                break;

            default:
                throw new Exception("Acción desconocida");
        }
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Carga de vista
try {
    $clientes = $clientesModel->listarResumenClientes($almacen_usuario);
    $productos = $productosModel->listarTodo(); 
    $tituloPagina = "Pedidos de Vendedor";
    require_once __DIR__ . '/../views/pedidosVendedor_view.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}