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

// ==========================================================
// 🔓 LECTURA Y CIERRE INMEDIATO DE SESIÓN
// ==========================================================
// Extraemos los datos necesarios de $_SESSION a variables locales
$session_rol_id = intval($_SESSION['rol_id'] ?? 0);
$session_usuario_id = intval($_SESSION['usuario_id'] ?? 0);
$session_almacen_id = intval($_SESSION['almacen_id'] ?? 0);

// Liberamos el candado del archivo de sesión para peticiones concurrentes
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

// Instancias de Modelos
$sendModelo = new EntregaModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$modelo = new UsuarioModel($conexion);
$ventasModel = new VentaHistorialModel($conexion);
$clientesModel = new ClientesModel($conexion);
$repartosModel = new RepartoModel($conexion);

$productosModel = new ProductoModel($conexion);

// ==========================================
// ACCIÓN: Obtener Usuarios
// ==========================================

if (isset($_GET['action']) && $_GET['action'] === 'obtenerUsuarios') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $rol = $session_rol_id;
        $id = $session_usuario_id;
        $almacen = intval($_GET['almacen_id'] ?? $session_almacen_id);
        $usuarios = '';

        if ($rol >= 3) {
            $usuarios = $modelo->listarUsuariosPorId($id);
        } else {
            if ($almacen == 0) {
                $usuarios = $modelo->listarTodosUsuarios(0);
            } else {
                $usuarios = $modelo->listarTodosUsuarios($almacen);
                $usuarioAdmin = $modelo->listarTodosUsuariosAdmin(0);

                if ($session_almacen_id == 0) {
                    // Fusionar agregando los admins al array principal de usuarios
                    $usuarios = array_merge($usuarios, $usuarioAdmin);
                }
            }
        }

        if ($usuarios) {
            echo json_encode(['success' => true, 'data' => $usuarios]);
        } else {
            throw new Exception('Usuarios no encontrados.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getAlmacenesJSON') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $almacen_usuario = $session_almacen_id;
        $almacenes = $almacenModel->getAlmacenes($almacen_usuario);

        if (!$almacenes) {
            echo json_encode([]);
        } else {
            echo json_encode($almacenes);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'obtenerClientes') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $rol = $session_rol_id;
        $id = intval($_GET['almacen_id'] ?? $session_almacen_id);

        $clientes_res = $clientesModel->listarTodos($id);
        $clientes = ($clientes_res) ? $clientes_res->fetch_all(MYSQLI_ASSOC) : [];

        if ($clientes) {
            echo json_encode(['success' => true, 'data' => $clientes]);
        } else {
            throw new Exception('Usuarios no encontrados.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ==========================================
// ACCIÓN: Obtener IDs Pendientes Venta
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'get_ids_pendientes_venta') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $venta_id = intval($_GET['venta_id'] ?? 0);
        if ($venta_id <= 0) {
            throw new Exception('ID de venta no válido.');
        }

        $ids = $repartosModel->listarIdsPendientesPorVenta($venta_id);

        echo json_encode(['success' => true, 'ids' => $ids ?? []]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ==========================================
// ACCIÓN: Obtener ID Almacén
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'obtener_id_almacen') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception('ID no válido.');
        }

        $almacen = $sendModelo->obtener_almecen_id($id);

        echo json_encode([
            "success" => true,
            "almacen" => $almacen
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getAlmacenesUsuario') {
    // Asegúrate de que la sesión esté iniciada si usas $_SESSION
    // session_start();

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {
        $id = intval($_SESSION['almacen_id'] ?? 0);

        // Llamamos a tu modelo
        $almacenes = $almacenModel->getAlmacenes($id);

        if (!$almacenes) {
            echo json_encode([]);
        } else {
            echo json_encode($almacenes);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    // Terminamos la ejecución para evitar que se renderice HTML adicional
    exit;
}


if (isset($_GET['action']) && $_GET['action'] === 'obtenerProductos') {
    header('Content-Type: application/json');

    $productos = $productosModel->obtenerTodosProductos(0);
    $medidasAdicionales = $productosModel->obtenerMedidas();

    $medidasPorProducto = [];

    foreach ($medidasAdicionales as $medida) {
        $producto_id = $medida['producto_id'];

        if (!isset($medidasPorProducto[$producto_id])) {
            $medidasPorProducto[$producto_id] = [];
        }

        $medidasPorProducto[$producto_id][] = $medida;
    }

    foreach ($productos as &$producto) {
        $idProducto = $producto['producto_id'];
        $producto['medidas_adicionales'] = $medidasPorProducto[$idProducto] ?? [];
    }

    unset($producto);

    echo json_encode([
        'success' => true,
        'data' => $productos
    ]);

    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'obtenerProductosAlmacen') {
    header('Content-Type: application/json');

    $almacen_usuario = !empty($_GET['id'])
        ? intval($_GET['id'])
        : $session_almacen_id;

    $productos = $productosModel->obtenerTodosProductosAlmacen($almacen_usuario);
    $medidasAdicionales = $productosModel->obtenerMedidas();

    $medidasPorProducto = [];

    foreach ($medidasAdicionales as $medida) {
        $producto_id = $medida['producto_id'];

        if (!isset($medidasPorProducto[$producto_id])) {
            $medidasPorProducto[$producto_id] = [];
        }

        $medidasPorProducto[$producto_id][] = $medida;
    }

    foreach ($productos as &$producto) {
        $idProducto = $producto['producto_id'];
        $producto['medidas_adicionales'] = $medidasPorProducto[$idProducto] ?? [];
    }

    unset($producto);

    echo json_encode([
        'success' => true,
        'data' => $productos
    ]);

    exit;
}