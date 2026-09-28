<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas de archivos base
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mantenimientos_model.php';
require_once __DIR__ . '/../models/verificaciones_model.php';
require_once __DIR__ . '/../models/egresos/insumosModel.php';

protegerPagina('verificaciones'); // Asegúrate que el permiso en auth coincida (o 'mantenimientos')

// Instanciación de modelos
$vehiculoModel       = new VehiculoModel($conexion);
$mantenimientosModel = new MantenimientosModel($conexion);
$verificacionesModel = new VerificacionesModel($conexion);
$insumosModel        = new InsumosModel($conexion);
$almacenModel        = new AlmacenModel($conexion);

$paginaActual = 'verificaciones';

// ==========================================
// 1. PETICIONES AJAX / API (GET Y POST)
// ==========================================

// --- LISTAR VERIFICACIONES FILTRADAS ---
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $filtros = [
            'search'   => $_GET['f_search'] ?? '',
            'rango'    => $_GET['f_rango'] ?? 'todos',
            'inicio'   => $_GET['f_inicio'] ?? '',
            'fin'      => $_GET['f_fin'] ?? '',
            'almacen'  => $_GET['f_almacen'] ?? 0,
            'vehiculo' => $_GET['f_vehiculo'] ?? 0,
        ];

        $rol_id = $_SESSION['rol_id'] ?? 2;
        $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        if ($rol_id == 1 || $rol_id == 3) {
            $id_almacen_usuario = 0;
            $rol_id = 1;
        }

        $data = $verificacionesModel->obtenerVerificacionesFiltradas($filtros, $rol_id, $id_almacen_usuario);
        echo json_encode($data);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

// // --- OBTENER DETALLE DE UNA VERIFICACIÓN ---
// if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {
//     if (ob_get_level()) ob_clean(); 
//     header('Content-Type: application/json');
    
//     try {
//         $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
//         if ($id <= 0) {
//             http_response_code(400);
//             throw new Exception("El ID de verificación proporcionado no es válido.");
//         }

//         // Se consulta la verificación en el modelo correspondiente
//         $data = $verificacionesModel->obtenerMantenimiento($id);
        
//         if (!$data) {
//             http_response_code(404);
//             throw new Exception("No se encontró la verificación solicitada.");
//         }

//         echo json_encode([
//             'status' => 'success',
//             'data'   => $data
//         ]);

//     } catch (Throwable $e) {
//         if (http_response_code() === 200) {
//             http_response_code(400);
//         }
        
//         echo json_encode([
//             'status'  => 'error',
//             'message' => $e->getMessage()
//         ]);
//     }
//     exit;
// }

// --- GUARDAR NUEVA VERIFICACIÓN ---
// CORREGIDO: Soporta tanto POST como GET en la url con action=guardar
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Mexico_City');
    
    try {
        // CORREGIDO: Si no lee JSON raw, busca en $_POST como fallback
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!$input) {
            throw new Exception("No se recibieron datos.");
        }

        $vehiculo_id = intval($input['vehiculo_id'] ?? 0);
        if ($vehiculo_id <= 0) {
            throw new Exception("Selecciona un vehículo válido.");
        }

        $fecha = $input['fecha_verificacion'] ?? date('Y-m-d');
        $proxima_verificacion = $input['fecha_proxima_verificacion'] ?? date('Y-m-d');

        // Guardar registro mediante el modelo
        $id = $verificacionesModel->guardar($vehiculo_id, $fecha, $proxima_verificacion);
       
        if ($id && $id > 0) {
            echo json_encode([
                'status'  => 'success',
                'message' => '¡Verificación guardada con éxito!',
                'id'      => $id
            ]);
        } else {
            throw new Exception("Error al guardar la verificación en la base de datos.");
        }
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}// --- LISTAR PRÓXIMAS VERIFICACIONES (NOTIFICACIONES / DROPDOWN) ---
if (isset($_GET['action']) && $_GET['action'] === 'listarProximaVerificacion') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    
    try {
          $almacenSesion = intval($_SESSION['almacen_id'] ?? 0);
        $data = $verificacionesModel->listarProximasVerificaciones($almacenSesion);
        echo json_encode($data);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'eliminar') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
   
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($_POST['id'] ?? $input['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("Elemento inválido");
        }

        // 🔥 ELIMINAR EN BD
        $ok = $verificacionesModel->eliminar($id);

        if (!$ok) {
            throw new Exception("Error al eliminar de la BD");
        }

        echo json_encode([
            'success' => true,
            'url' => $id
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

// ==========================================
// 2. CARGA DE LA VISTA (RENDERIZADO GET)
// ==========================================
// CORREGIDO: Condición 'else' directa. Si llega a este punto, no se ejecutó ningún 'action' AJAX
try {
    $vehiculos = $vehiculoModel->listar();
    // Carga necesaria para el select de la vista
    $almacenSesion = intval($_SESSION['almacen_id'] ?? 0); 
    $almacenes = $almacenModel->getAlmacenes($almacenSesion); // Para el select del modal
 
      $tipo=$almacenSesion==0?1:0;
    $tituloPagina = "mantenimientos";
    $vistaRuta = __DIR__ . '/../views/verificaciones_view.php';
    
    if (file_exists($vistaRuta)) {
        require_once $vistaRuta;
    } else {
        throw new Exception("La vista 'verificaciones_view.php' no existe.");
    }
} catch (Exception $e) {
    die("Error al cargar el módulo de verificaciones: " . $e->getMessage());
}