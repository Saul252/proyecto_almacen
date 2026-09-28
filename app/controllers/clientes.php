<?php
/**
 * clientesController.php
 * Controlador para la gestión de Clientes (CRUD y Estado)
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/clientesModel.php';

$clientesModel = new ClientesModel($conexion);
$paginaActual = 'clientes';

// --- ACCIÓN: GUARDAR / ACTUALIZAR CLIENTE (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['cliente_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? 1;

        $datos = [
            'nombre_comercial' => trim($_POST['nombre_comercial'] ?? ''),
            'razon_social'     => trim($_POST['razon_social'] ?? ''),
            'rfc'              => strtoupper(trim($_POST['rfc'] ?? '')),
            'regimen_fiscal'   => $_POST['regimen_fiscal'] ?? '',
            'codigo_postal'    => $_POST['codigo_postal'] ?? '',
            'correo'           => $_POST['correo'] ?? '',
            'telefono'         => $_POST['telefono'] ?? '',
            'direccion'        => $_POST['direccion'] ?? '',
            'uso_cfdi'         => $_POST['uso_cfdi'] ?? 'G03'
        ];

        if (empty($datos['nombre_comercial']) || empty($datos['rfc'])) {
            throw new Exception("Nombre comercial y RFC son campos obligatorios.");
        }

        if ($id > 0) {
            $resultado = $clientesModel->actualizar($id, $datos);
            $mensaje = "Cliente actualizado correctamente.";
        } else {
            $resultado = $clientesModel->guardar($datos);
            $mensaje = "Cliente registrado correctamente.";
        }

        echo json_encode(['success' => true, 'message' => $mensaje]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: CAMBIAR ESTADO ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        $estado = intval($_POST['estado'] ?? 0);

        if ($id <= 0) throw new Exception("ID de cliente no válido.");

        $resultado = $clientesModel->cambiarEstado($id, $estado);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado con éxito.']);
        } else {
            throw new Exception("No se pudo actualizar el estado.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: OBTENER DATOS POR ID (Editar) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        $cliente = $clientesModel->obtenerPorId($id);
        
        if ($cliente) {
            echo json_encode(['success' => true, 'data' => $cliente]);
        } else {
            throw new Exception('Cliente no encontrado.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        // 1. Capturamos el almacén del usuario logueado (0 si es Admin)
        $almacen_sesion = $_SESSION['almacen_id'] ?? 0;

        // 2. Pasamos el ID a la función para que filtre automáticamente
        $clientes = $clientesModel->listarTodos($almacen_sesion);
        
        $tituloPagina = "Administración de Clientes";
        require_once __DIR__ . '/../views/clientes_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }

}