<?php
/**
 * mascotasController.php
 * Controlador para la gestión de Mascotas (CRUD, Estado y Subida de Imágenes)
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/LayoutController.php';
require_once __DIR__ . '/../models/mascotasModel.php';
require_once __DIR__ . '/../models/clientesModel.php';

$clientesModel = new ClientesModel($conexion);

$mascotasModel = new MascotasModel($conexion);
$paginaActual = 'historialEx';
if (isset($_GET['action']) && $_GET['action'] === 'obtenerHistorialDetalle') {

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new Exception('Método no permitido.');
        }

        // Obtener el id desde $_GET['id']
        $historial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($historial_id <= 0) {
            throw new Exception('ID de historial no válido.');
        }

        // Consultar la base de datos a través del modelo
        $detalle = $mascotasModel->obtenerHistorialPorId($historial_id);

        if (!$detalle) {
            throw new Exception('No se encontró el registro de historial solicitado.');
        }

        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'data'    => $detalle
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
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        if (ob_get_level()) ob_clean();
          $id = intval($_GET['id'] ?? 0);
    
    $tituloPagina = "Administración de Mascotas";
    
    try {
        $id = intval($_GET['id'] ?? 0);
        $mascota = $mascotasModel->obtenerExpedientePorId($id);
       

$expediente = [];

while ($row = $mascota->fetch_assoc()) {
    $expediente[] = $row;
}


      
        require_once __DIR__ . '/../views/historialClinico.php';
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
  
    
}
    
