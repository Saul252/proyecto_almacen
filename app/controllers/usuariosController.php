<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/usuariosModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('usuarios'); 

$modelo = new UsuarioModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$usuario = $_SESSION['usuario_id'] ?? 0;
$tipo=$almacen_usuario==0?1:0;
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);
if (isset($_GET['action']) && $_GET['action'] === 'obtenerUsuarios') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        $usuarios = $modelo->listarUsuarios();
        
        if ($usuarios) {
            echo json_encode(['success' => true, 'data' => $usuarios]);
        } else {
            throw new Exception('Usuarios no encontrado.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// Variables para la vista
$paginaActual = 'Usuarios';
$usuarios = $modelo->listarUsuarios();
$rolesArray = $modelo->getRoles();
$almacenesArray = $modelo->getAlmacenes();

// Cargar la vista (archivo HTML)
include __DIR__ . '/../views/usuarios_view.php';