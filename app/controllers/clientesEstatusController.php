<?php
/**
 * clientesEstatusController.php
 * Controlador optimizado para manejo de lista maestra y expediente de cliente.
 */

// 1. Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Buffer de salida
ob_start(); 

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php'; 
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/clientesEstatusModel.php';
protegerPagina('clientesEstatus'); 
$db = $conexion; 
$clientesEstatusModel = new ClientesEstatusModel($db);
$paginaActual = 'clientesEstatus';

// --- ACCIONES AJAX ---
if (isset($_GET['action'])) {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        switch ($_GET['action']) {
           case 'listar':
    // 1. Identificamos el almacén del usuario logueado
    $almacen_sesion = $_SESSION['almacen_id'] ?? 0;

    // 2. Si el usuario es Admin (0) y envió un filtro específico por GET, lo tomamos.
    // Si no es admin o no envió filtro, usamos su almacen_sesion.
    $almacen_a_consultar = ($almacen_sesion == 0 && isset($_GET['almacen_id'])) 
                           ? intval($_GET['almacen_id']) 
                           : 0;
//    $almacen_a_consultar = ($almacen_sesion == 0 && isset($_GET['almacen_id'])) 
//                            ? intval($_GET['almacen_id']) 
//                            : $almacen_sesion;
    // 3. Pasamos el ID a la función (que ya ajustamos con el WHERE ? = 0 OR c.almacen_id = ?)
    $data = $clientesEstatusModel->listarResumenClientes($almacen_sesion);
    
    echo json_encode(['success' => true, 'data' => $data,'sesion'=>$_SESSION['almacen_id'],'admin'=>$_SESSION['almacen_id']==0 ?true:false]);
    break;
           case 'obtenerDetalle':
    // Información completa para el expediente individual
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) throw new Exception("ID de cliente no válido.");
    
    // USAMOS LA FUNCIÓN MAESTRA que ya creamos en el modelo
    // Esta función ya incluye productos y pagos dentro de cada venta
    $expediente = $clientesEstatusModel->obtenerExpedienteCompleto($id);
    
    echo json_encode([
        'success' => true, 
        'data'    => $expediente // El JS espera esto para recorrer ventas -> productos
    ]);
    break;
            default:
                throw new Exception("Acción no válida.");
        }
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage(),
            'debug'   => ['archivo' => $e->getFile(), 'linea' => $e->getLine()]
        ]);
    }
    exit;
}

// --- CARGA DE VISTAS (Peticiones estándar del navegador) ---
try {
    // Caso A: Ver Expediente de un Cliente específico
    if (isset($_GET['id']) && intval($_GET['id']) > 0) {
        $id_cliente = intval($_GET['id']);
        $infoCliente = $clientesEstatusModel->obtenerDatosBasicos($id_cliente);
        
        if (!$infoCliente) {
            throw new Exception("El cliente solicitado no existe o fue dado de baja.");
        }

        $tituloPagina = "Expediente: " . $infoCliente['nombre_comercial'];
        require_once __DIR__ . '/../views/clienteEstatus/clientesEstatusDetalle_view.php';
    } 
    // Caso B: Ver Lista Maestra (Estatus Global)
    else {
        $tituloPagina = "Estatus Maestro de Clientes";
        require_once __DIR__ . '/../views/clientesEstatus_view.php';
    }
} catch (Exception $e) {
    die("<div style='padding:20px; color:red; font-family:sans-serif; border:1px solid red; margin:20px; border-radius:10px;'>
            <h3 style='margin-top:0;'>⚠️ Error de Sistema</h3>
            <p>{$e->getMessage()}</p>
            <hr>
            <a href='clientesEstatusController.php' style='color:blue; text-decoration:none;'>« Volver al listado</a>
         </div>");
}