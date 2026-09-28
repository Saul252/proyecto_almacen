<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas de archivos base
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/almacen_model.php'; // Necesario para el selector de admin

protegerPagina('vehiculos'); 

$vehiculoModel = new VehiculoModel($conexion);
$almacenModel = new AlmacenModel($conexion); // Instanciamos para obtener la lista de sucursales
$paginaActual = 'vehiculos';

// --- MANEJO DE PETICIONES POST (AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'];

        if ($action === 'guardar') {
            // Lógica de "Selector Inteligente" para el Almacén
            $datos = $_POST;
            $almacenSesion = intval($_SESSION['almacen_id'] ?? 0);

            // Si no es admin global, forzamos su almacen_id de sesión por seguridad
            if ($almacenSesion !== 0) {
                $datos['almacen_id'] = $almacenSesion;
            }

            $res = $vehiculoModel->guardar($datos);
            echo json_encode(['status' => $res ? 'success' : 'error']);
            
        } elseif ($action === 'eliminar') {
            $id = intval($_POST['id'] ?? 0);
            $res = $vehiculoModel->eliminar($id);
            echo json_encode(['status' => $res ? 'success' : 'error']);
        }

    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'listar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    
        $lista = $vehiculoModel->listar();
        
        // Estructura consistente: success + data
        echo json_encode([
            "success" => true,
            "data" => $lista ?: []
        ]);
   
    
    exit; // El exit DEBE ir afuera del try/catch para asegurar que detenga todo siempre
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'subirDocumento') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {

        $id = intval($_POST['vehiculo_id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("Vehículo inválido");
        }

        $documento = $_FILES['documento'] ?? null;

        if (!$documento || $documento['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir archivo");
        }

        // Carpeta destino
        $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/uploads/vehiculos_document/";

        if (!is_dir($ruta_carpeta)) {
            mkdir($ruta_carpeta, 0777, true);
        }

        if (!is_writable($ruta_carpeta)) {
            throw new Exception("La carpeta no tiene permisos de escritura");
        }

        // Nombre del archivo
        $ext = strtolower(pathinfo($documento['name'], PATHINFO_EXTENSION));
        $base = pathinfo($documento['name'], PATHINFO_FILENAME);

        $nombre = "vehiculo_" .
            preg_replace('/[^a-zA-Z0-9]/', '_', $base) .
            "_" .
            time() .
            "." .
            $ext;

        $destino = $ruta_carpeta . $nombre;

        if (!move_uploaded_file($documento['tmp_name'], $destino)) {
            throw new Exception("No se pudo guardar el archivo");
        }

        // Ruta que se guarda en BD
        $documento_url = "uploads/vehiculos_document/" . $nombre;

        // Guardar en BD
        $resultado = $vehiculoModel->subirDocumentoCompra(
            $id,
            $documento['name'],
            $documento_url
        );

        echo json_encode([
            'success' => true,
            'url' => $documento_url,
            'documento_id' => $resultado['documento_id'] ?? 0
        ]);

    } catch (Throwable $e) {

        error_log("ERROR SUBIR DOCUMENTO VEHICULO: " . $e->getMessage());

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'eliminarDocumento') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {

        
        
        $id = intval($_POST['id'] ?? 0);
        

        if ($id <= 0) {
            throw new Exception("Elemento inválida");
        }

       

        // 🔥 ELIMINAR EN BD
        $ok = $vehiculoModel->eliminarDocumento($id);

        if (!$ok) {
            throw new Exception("Error al guardar en BD");
        }

        echo json_encode([
            'success' => true,
            'url' => $id
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// --- CARGA DE LA VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $almacenSesion = intval($_SESSION['almacen_id'] ?? 0);

        // Si es admin global (0), ve todos. Si no, solo los de su sucursal.
        if ($almacenSesion === 0) {
            $vehiculos = $vehiculoModel->listar();
            $almacenes = $almacenModel->getAlmacenes($almacenSesion); // Para el select del modal
        } else {
            $vehiculos = $vehiculoModel->listarPorAlmacen($almacenSesion);
            $almacenes = $almacenModel->getAlmacenes($almacenSesion); // No lo necesita porque se le asigna el suyo automáticamente
        }

        $tituloPagina = "Control de Flota";
        $vistaRuta = __DIR__ . '/../views/vehiculos_view.php';
        
        if (file_exists($vistaRuta)) {
            require_once $vistaRuta;
        } else {
            throw new Exception("La vista 'vehiculos_view.php' no existe.");
        }
    } catch (Exception $e) {
        die("Error al cargar la flota: " . $e->getMessage());
    }
}