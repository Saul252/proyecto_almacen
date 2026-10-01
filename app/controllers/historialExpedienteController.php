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
if (isset($_GET['action']) && $_GET['action'] === 'subirDocumento') {

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {

        // Validar método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Método no permitido para esta acción.");
        }

        // Obtener ID del paciente
        $paciente_id = intval(
            $_POST['pacienteId']
            ?? $_POST['paciente_id']
            ?? $_POST['id_paciente']
            ?? 0
        );

        // Obtener ID de la consulta
        $consulta_id = intval($_POST['consulta_id'] ?? 0);

        if ($paciente_id <= 0) {
            throw new Exception("Identificador de paciente inválido.");
        }

        if ($consulta_id <= 0) {
            throw new Exception("Identificador de consulta inválido.");
        }

        // Validar archivo
        $documento = $_FILES['documento'] ?? null;

        if (!$documento) {
            throw new Exception("No se recibió ningún archivo.");
        }

        if ($documento['error'] !== UPLOAD_ERR_OK) {
            throw new Exception(
                "Error al recibir el archivo subido. Código: " . $documento['error']
            );
        }

        // Extensiones permitidas
        $extensiones_permitidas = [
            'pdf',
            'png',
            'jpg',
            'jpeg',
            'webp'
        ];

        $ext = strtolower(
            pathinfo($documento['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($ext, $extensiones_permitidas, true)) {
            throw new Exception(
                "Formato de archivo no permitido (.$ext)."
            );
        }

        // Carpeta de documentos médicos
        $subcarpeta = "uploads/medical_document/";
        $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/" . $subcarpeta;

        // Crear carpeta si no existe
        if (!is_dir($ruta_carpeta)) {

            if (!mkdir($ruta_carpeta, 0755, true) && !is_dir($ruta_carpeta)) {
                throw new Exception(
                    "No se pudo crear el directorio de destino."
                );
            }
        }

        // Validar permisos
        if (!is_writable($ruta_carpeta)) {
            throw new Exception(
                "La carpeta de destino no tiene permisos de escritura."
            );
        }

        // Limpiar nombre original
        $base = pathinfo(
            $documento['name'],
            PATHINFO_FILENAME
        );

        $nombre_limpio = preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '_',
            $base
        );

        // Nombre único
        $nombre_archivo =
            "vet_" .
            $paciente_id . "_" .
            $nombre_limpio . "_" .
            time() . "." .
            $ext;

        $destino = $ruta_carpeta . $nombre_archivo;

        // Mover archivo
        if (!move_uploaded_file(
            $documento['tmp_name'],
            $destino
        )) {
            throw new Exception(
                "No se pudo guardar el archivo en el servidor."
            );
        }

        // URL relativa para guardar en BD
        $documento_url = $subcarpeta . $nombre_archivo;

        // Tipo de documento
        $tipo = 'vet';

        // Guardar registro en BD
        $resultado = $pacientesModel->subirDocumentoConsulta(
            $paciente_id,
            $documento['name'],
            $documento_url,
            $tipo,
            $consulta_id
        );

        if (!$resultado) {
            // Si falló la BD, eliminar archivo que ya se había subido
            if (file_exists($destino)) {
                unlink($destino);
            }

            throw new Exception(
                "No se pudo guardar el registro del documento."
            );
        }

        echo json_encode([
            'success' => true,
            'url' => $documento_url,
            'documento_id' => $resultado['documento_id'] ?? 0,
            'message' => 'Documento guardado correctamente.'
        ]);

    } catch (Exception $e) {

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
    
