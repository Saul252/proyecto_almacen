<?php
// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Iniciar almacenamiento en búfer para evitar espacios o advertencias antes del JSON
ob_start();

// Configuración de blindaje: Registrar errores en logs de servidor pero desactivar impresión en pantalla
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Requerir dependencias principales y middlewares
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/mermasModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// Validar que el usuario tenga permisos para acceder al módulo de Mermas
protegerPagina('Mermas'); 

// Validar presencia de sesión de usuario activa
if (!isset($_SESSION['id']) && !isset($_SESSION['usuario_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => '❌ Sesión requerida']);
        exit;
    }
    header('Location: /myvet/login.php');
    exit;
}

$paginaActual = 'Mermas';

// Instanciar modelos
$mermasModel  = new MermasModel($conexion);
$almacenModel = new AlmacenModel($conexion);

// Detectar la acción enviada por GET (por defecto 'index')
$action = $_GET['action'] ?? 'index';

// =========================================================================
// 🔍 ACCIÓN AJAX: LISTAR MERMAS CON FILTROS (Para DataTables / Fetch)
// =========================================================================
if ($action === 'listar') {
    ob_clean(); // Limpiar cualquier salida previa del búfer
    header('Content-Type: application/json'); // Respuesta JSON estricta
    
    try {
        // Capturar filtros enviados por la petición GET
        $filtros = [
            'search'     => $_GET['f_search']     ?? $_GET['search'] ?? '',
            'producto'   => $_GET['f_producto']   ?? $_GET['producto'] ?? 0,
            'almacen'    => $_GET['f_almacen']    ?? $_GET['almacen_id'] ?? 0,
            'tipo_merma' => $_GET['f_tipo_merma'] ?? $_GET['tipo_merma'] ?? '',
            'rango'      => $_GET['f_rango']      ?? $_GET['rango'] ?? 'todos',
            'inicio'     => $_GET['f_inicio']     ?? $_GET['inicio'] ?? '',
            'fin'        => $_GET['f_fin']        ?? $_GET['fin'] ?? ''
        ];
        
        // Obtener credenciales de la sesión
        $rol_id = $_SESSION['rol_id'] ?? 2;
        $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        // Si es Admin (1) o Supervisor (3), permitimos consultar almacén 0 (todos)
        if (isset($_SESSION['rol_id']) && ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 3)) {
            $id_almacen_usuario = 0; 
            $rol_id = 1;
        }

        // Consultar los datos filtrados en el modelo
        $data = $mermasModel->obtenerMermasFiltradas($filtros, $rol_id, $id_almacen_usuario);
        
        // Devolver respuesta como arreglo JSON
        echo json_encode($data);

    } catch (Throwable $e) {
        // En caso de error inesperado de PHP 7+, devolver objeto con mensaje de error
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// =========================================================================
// 🔍 ACCIÓN AJAX: OBTENER PRODUCTOS POR ALMACÉN
// =========================================================================
if ($action === 'obtenerProductosAlmacen') {
    ob_clean();
    header('Content-Type: application/json');
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    $productos = $almacenModel->getInventarioConId($almacen_id);
    echo json_encode($productos ?: []);
    exit;
}

// =========================================================================
// 🔍 ACCIÓN AJAX: OBTENER LOTES POR PRODUCTO Y ALMACÉN
// =========================================================================
if ($action === 'obtenerLotes') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    $lotes = ($producto_id > 0 && $almacen_id > 0) 
        ? $mermasModel->getLotesPorProducto($almacen_id, $producto_id) 
        : [];
    echo json_encode($lotes);
    exit;
}

// =========================================================================
// 💾 ACCIÓN POST: REGISTRAR NUEVA MERMA
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'guardarMerma') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'];
        $responsable = $_SESSION['nombre'] ?? 'Usuario #' . $usuario_id;

        // Sanitización y parseo de datos enviados por POST
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $almacen_id  = intval($_POST['almacen_id'] ?? 0);
        $lote_id     = intval($_POST['lote_id'] ?? 0);
        $cantidad    = floatval($_POST['cantidad'] ?? 0);
        $tipo_merma  = trim($_POST['tipo_merma'] ?? 'otro');
        $motivo      = trim($_POST['observaciones'] ?? '');

        // Validar requerimientos obligatorios
        if ($producto_id <= 0) throw new Exception("Producto inválido (ID: $producto_id)");
        if ($almacen_id <= 0) throw new Exception("Almacén inválido (ID: $almacen_id)");
        if ($lote_id <= 0) throw new Exception("Lote inválido (ID: $lote_id)");
        if ($cantidad <= 0) throw new Exception("Cantidad inválida ($cantidad)");
        if (!in_array($tipo_merma, ['daño','robo','caducidad','otro'])) {
            throw new Exception("Tipo de merma inválido: $tipo_merma");
        }

        // Verificar disponibilidad en el lote
        $stmt = $conexion->prepare("SELECT cantidad_actual FROM lotes_stock WHERE id = ?");
        $stmt->bind_param("i", $lote_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        if (!$resultado) throw new Exception("Lote no encontrado (ID: $lote_id)");
        if ($cantidad > $resultado['cantidad_actual']) {
            throw new Exception("Stock insuficiente. Disponible: " . $resultado['cantidad_actual']);
        }

        // Preparar arreglo de datos para inserción en DB
        $datos = [
            'producto_id' => $producto_id,
            'almacen_id'  => $almacen_id,
            'lote_id'     => $lote_id,
            'cantidad'    => $cantidad,
            'tipo_merma'  => $tipo_merma,
            'motivo'      => $motivo,
            'usuario_id'  => $usuario_id,
            'responsable' => $responsable
        ];

        // Ejecutar inserción en el modelo
        $resultado = $mermasModel->registrarMerma($datos);

        if ($resultado === true) {
            echo json_encode([
                'status'  => 'success', 
                'message' => '✅ Merma registrada correctamente'
            ]);
        } else {
            throw new Exception("Error en modelo: " . $resultado);
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error', 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================================
// 📄 VISTA PRINCIPAL (Renderizado HTML inicial)
// =========================================================================
try {
    $almacen_sesion = $_SESSION['almacen_id'] ?? 0;
    $rol_id = $_SESSION['rol_id'] ?? 2;

    if (isset($_SESSION['rol_id']) && ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 3)) {
        $almacen_sesion = 0;
        $rol_id = 1;
    }
    
    // Cargar la lista de almacenes permitidos para los selectores de la vista
    $almacenes = $almacenModel->getAlmacenes($almacen_sesion);
    
    // Cargar el historial inicial respetando la seguridad del almacén del usuario
    $filtrosIniciales = ['rango' => 'todos'];
    $mermas = $mermasModel->obtenerMermasFiltradas($filtrosIniciales, $rol_id, $almacen_sesion);

    // Incluir la vista HTML
    include __DIR__ . '/../views/mermas_view.php';
} catch (Exception $e) {
    die("Error crítico en el sistema de mermas: " . $e->getMessage());
}
?>