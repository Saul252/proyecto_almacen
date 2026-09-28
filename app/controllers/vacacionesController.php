<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas de archivos base
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/trabajadores_model.php';

require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/vacacionesModel.php'; // Para selects de trabajadores si se requieren

protegerPagina('vacaciones'); // Asegúrate de que el permiso exista en auth.php

// Instanciación de modelos
$vacacionesModel   = new VacacionesModel($conexion);
$trabajadoresModel = new TrabajadorModel($conexion);
$almacenModel        = new AlmacenModel($conexion);
$paginaActual = 'vacaciones';
$almacenSesion = intval($_SESSION['almacen_id'] ?? 0); 
    $almacenes = $almacenModel->getAlmacenes($almacenSesion); // Para el select del modal
 
      $tipo=$almacenSesion==0?1:0;
// ==========================================
// 1. PETICIONES AJAX / API (GET Y POST)
// ==========================================

// --- LISTAR VACACIONES FILTRADAS ---
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $filtros = [
            'search'        => $_GET['f_search'] ?? '',
            'almacen'        => $_GET['f_almacen'] ?? '',
            'rango'         => $_GET['f_rango'] ?? 'todos',
            'inicio'        => $_GET['f_inicio'] ?? '',
            'fin'           => $_GET['f_fin'] ?? '',
            'id_trabajador' => $_GET['f_trabajador'] ?? 0,
            'monto'         => $_GET['f_monto'] ?? '',
        ];

        $rol_id     = $_SESSION['rol_id'] ?? 2;
        $id_usuario = $_SESSION['usuario_id'] ?? 0;

        $data = $vacacionesModel->obtenerVacacionesFiltradas($filtros, $rol_id, $id_usuario);
        echo json_encode($data);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- OBTENER DETALLE DE UN REGISTRO DE VACACIONES ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            http_response_code(400);
            throw new Exception("El ID de vacaciones proporcionado no es válido.");
        }

        $data = $vacacionesModel->obtenerPorId($id);
        
        if (!$data) {
            http_response_code(404);
            throw new Exception("No se encontró el registro de vacaciones.");
        }

        echo json_encode([
            'status' => 'success',
            'data'   => $data
        ]);

    } catch (Throwable $e) {
        if (http_response_code() === 200) {
            http_response_code(400);
        }
        
        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// --- GUARDAR / ACTUALIZAR VACACIONES ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Mexico_City');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!$input) {
            throw new Exception("No se recibieron datos.");
        }

        $id = intval($input['id'] ?? 0);
        $id_trabajador = intval($input['id_trabajador'] ?? 0);

        if ($id_trabajador <= 0) {
            throw new Exception("Selecciona un trabajador válido.");
        }

        $datos = [
            'id_trabajador'    => $id_trabajador,
            'fecha'            => $input['fecha'] ?? date('Y-m-d'),
            'dias_disponibles' => intval($input['dias_disponibles'] ?? 0),
            'dias_a_tomar'     => intval($input['dias_a_tomar'] ?? 0),
            'monto_restante'   => floatval($input['monto_restante'] ?? 0),
            'retenciones'      => floatval($input['retenciones'] ?? 0)
        ];

        if ($id > 0) {
            // Actualizar existente
            $ok = $vacacionesModel->editar($id, $datos);
            $msg = '¡Registro de vacaciones actualizado con éxito!';
        } else {
            // Insertar nuevo
            $ok = $vacacionesModel->insertar($datos);
            $msg = '¡Registro de vacaciones guardado con éxito!';
        }
       
        if ($ok) {
            echo json_encode([
                'status'  => 'success',
                'message' => $msg
            ]);
        } else {
            throw new Exception("Error al guardar en la base de datos.");
        }
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// --- ELIMINAR REGISTRO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'eliminar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
   
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($_POST['id'] ?? $input['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("Elemento inválido.");
        }

        $ok = $vacacionesModel->eliminar($id);

        if (!$ok) {
            throw new Exception("Error al eliminar de la base de datos.");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Registro eliminado con éxito'
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
try {
    $trabajadores = $trabajadoresModel->listarTrabajadores($_SESSION['almacen_id']);
     
    $tituloPagina = "vacaciones";
    $vistaRuta    = __DIR__ . '/../views/vacaciones_view.php';
    
    if (file_exists($vistaRuta)) {
        require_once $vistaRuta;
    } else {
        throw new Exception("La vista 'vacaciones_view.php' no existe.");
    }
} catch (Exception $e) {
    die("Error al cargar el módulo de vacaciones: " . $e->getMessage());
}