<?php
// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

// Blindaje contra errores: Loguear todo pero no mostrar en pantalla para no romper el JSON
error_reporting(E_ALL);
ini_set('display_errors', 0); 

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/transmutacionesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mermasModel.php'; 
require_once __DIR__ . '/../models/almacen/productosModel.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

protegerPagina('transmutaciones'); 
$paginaActual = 'transmutaciones';
$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? null;

if (!$usuario_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => '❌ Sesión expirada']);
        exit;
    }
    header('Location: /myvet/login.php');
    exit;
}

$transModel   = new TransmutacionesModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$mermasModel  = new MermasModel($conexion);
$productoModel = new ProductoModel($conexion);
        
$action = $_GET['action'] ?? 'index';

// ============================================
// 💾 POST: GUARDAR TRANSMUTACIÓN
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'guardar') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $responsable = $_SESSION['nombre'] ?? 'Usuario #' . $usuario_id;

        $datos = [
            'almacen_id'          => intval($_POST['almacen_id'] ?? 0),
            'producto_origen_id'  => intval($_POST['producto_origen_id'] ?? 0),
            'lote_origen_id'      => intval($_POST['lote_origen_id'] ?? 0),
            'cant_origen'         => floatval($_POST['cantidad_origen'] ?? 0),
            'producto_destino_id' => intval($_POST['producto_destino_id'] ?? 0),
            'lote_destino_id'     => intval($_POST['lote_destino_id'] ?? 0),
            'cant_destino'        => floatval($_POST['cantidad_destino'] ?? 0),
            'observaciones'       => trim($_POST['observaciones'] ?? ''),
            'usuario_id'          => $usuario_id,
            'responsable'         => $responsable
        ];

        if ($datos['almacen_id'] <= 0) throw new Exception("Error: Almacén no seleccionado.");
        if ($datos['producto_origen_id'] <= 0 || $datos['producto_destino_id'] <= 0) throw new Exception("Error: Productos no válidos.");
        if ($datos['cant_origen'] <= 0) throw new Exception("Error: Cantidad de origen insuficiente.");

        $resultado = $transModel->registrarTransmutacion($datos);
        echo json_encode($resultado);
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// 🔍 AJAX: LISTAR HISTORIAL CON FILTROS (Para DataTables / Fetch)
// ============================================
if ($action === 'listar') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $filtros = [
            'search'   => $_GET['f_search']   ?? $_GET['search'] ?? '',
            'producto' => $_GET['f_producto'] ?? $_GET['producto'] ?? 0,
            'almacen'  => $_GET['f_almacen']  ?? $_GET['almacen_id'] ?? 0,
            'rango'    => $_GET['f_rango']    ?? $_GET['rango'] ?? 'todos',
            'inicio'   => $_GET['f_inicio']   ?? $_GET['inicio'] ?? '',
            'fin'      => $_GET['f_fin']      ?? $_GET['fin'] ?? ''
        ];
        
        $rol_id = $_SESSION['rol_id'] ?? 2;
        $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        if (isset($_SESSION['rol_id']) && ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 3)) {
            $id_almacen_usuario = 0; // Rol 1 y 3 ven todos los almacenes
            $rol_id = 1;
        }

        $data = $transModel->listarTransmutaciones($filtros, $rol_id, $id_almacen_usuario);
        echo json_encode($data);

    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// 🔍 AJAX: OBTENER LOTES / DESTINOS
// ============================================
if ($action === 'obtenerLotes') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    echo json_encode(($producto_id > 0 && $almacen_id > 0) ? $mermasModel->getLotesPorProducto($almacen_id, $producto_id) : []);
    exit;
}

if ($action === 'obtenerDestinosCompatibles') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    echo json_encode($producto_id > 0 ? $transModel->obtenerDestinosCompatibles($producto_id) : []);
    exit;
}

// ============================================
// 💾 POST: GUARDAR EQUIVALENCIA
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'guardarEquivalencia') {
    ob_clean();
    header('Content-Type: application/json');
    try {
        $almacen_sesion = intval($_SESSION['almacen_id'] ?? 0);
        $almacen_id = ($almacen_sesion === 0) ? intval($_POST['almacen_id'] ?? 0) : $almacen_sesion;

        if ($almacen_id === 0) throw new Exception("Almacén obligatorio.");

        $p_origen  = intval($_POST['p_origen'] ?? 0);
        $p_destino = intval($_POST['p_destino'] ?? 0);
        $factor    = floatval($_POST['factor'] ?? 0);
        $notas     = trim($_POST['notas'] ?? '');

        $res = $transModel->agregarConfiguracion($almacen_id, $p_origen, $p_destino, $factor, $usuario_id, $notas);
        echo json_encode(['status' => 'success', 'message' => '✅ Regla guardada correctamente']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// 📄 VISTA PRINCIPAL
// ============================================
if ($action === 'index') {
    try {
        $almacen_sesion = intval($_SESSION['almacen_id'] ?? 0);
        $rol_id = $_SESSION['rol_id'] ?? 2;

        if (isset($_SESSION['rol_id']) && ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 3)) {
            $almacen_sesion = 0;
            $rol_id = 1;
        }
        
        $almacenes = $almacenModel->getAlmacenes($almacen_sesion);
        $todosLosProductos = $productoModel->getProductos() ?: []; 
        
        // Carga inicial (trae el historial con filtro por defecto 'todos')
        $filtrosIniciales = ['rango' => 'todos'];
        $historial = $transModel->listarTransmutaciones($filtrosIniciales, $rol_id, $almacen_sesion);
        
        $almacen_param = (int)($_SESSION['almacen_id'] ?? 0);
        $listaConversiones = $transModel->listarConfiguraciones($almacen_param);

        include __DIR__ . '/../views/transmutaciones.php';
    } catch (Exception $e) {
        die("Error en el controlador: " . $e->getMessage());
    }
    exit;
}