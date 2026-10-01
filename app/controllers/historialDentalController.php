<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';

require_once __DIR__ . '/LayoutController.php';
require_once __DIR__ . '/../models/clientesEstatusModel.php';
require_once __DIR__ . '/../models/pacientesModel.php';
require_once __DIR__ . '/../models/clientesModel.php';

$pacientesModel = new PacientesModel($conexion);
$model = new ClientesEstatusModel($conexion);
$clientesModel = new ClientesModel($conexion);

$id_paciente = intval($_GET['id'] ?? $_POST['id_paciente'] ?? $_POST['paciente_id'] ?? $_POST['pacienteId'] ?? 0);

$paginaActual = 'clientesPacientes';
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');

// ==========================================
// SECCIÓN DE MANEJO DE PETICIONES AJAX (JSON)
// ==========================================
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($action !== null) {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        switch ($action) {

            // 1. SUBIR DOCUMENTO DENTAL

            case 'subirDocumento':
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    throw new Exception("Método no permitido para esta acción.");
                }

                // Evaluar múltiples nombres de parámetro posibles para evitar fallos de ID
                $paciente_id = intval($_POST['pacienteId'] ?? $_POST['paciente_id'] ?? $_POST['id_paciente'] ?? 0);

                $consulta_id = intval($_POST['consulta_id'] ?? 0);

                if ($paciente_id <= 0) {
                    throw new Exception("Identificador de paciente inválido.");
                }

                $documento = $_FILES['documento'] ?? null;

                if (!$documento || $documento['error'] !== UPLOAD_ERR_OK) {
                    $errCode = $documento['error'] ?? 'SIN_ARCHIVO';
                    throw new Exception("Error al recibir el archivo subido (Código: {$errCode}).");
                }

                // ✅ LÍMITE DE 3 MB
                $max_size = 3 * 1024 * 1024; // 3 MB en bytes
                if ($documento['size'] > $max_size) {
                    throw new Exception("El archivo supera el límite permitido de 3 MB.");
                }

                $extensiones_permitidas = ['pdf', 'png', 'jpg', 'jpeg', 'webp'];
                $ext = strtolower(pathinfo($documento['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $extensiones_permitidas, true)) {
                    throw new Exception("Formato de archivo no permitido (.$ext).");
                }

                $subcarpeta = "uploads/dental_document/";
                $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/" . $subcarpeta;

                if (!is_dir($ruta_carpeta)) {
                    if (!mkdir($ruta_carpeta, 0755, true) && !is_dir($ruta_carpeta)) {
                        throw new Exception("No se pudo crear el directorio de destino.");
                    }
                }

                if (!is_writable($ruta_carpeta)) {
                    throw new Exception("La carpeta de destino no tiene permisos de escritura.");
                }

                $base = pathinfo($documento['name'], PATHINFO_FILENAME);
                $nombre_limpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
                $nombre_base = "dental_" . $paciente_id . "_" . $nombre_limpio . "_" . time();

                // Detectar tipo real del archivo
                $mime = mime_content_type($documento['tmp_name']);
                $extensiones_imagen = ['png', 'jpg', 'jpeg', 'webp'];
                $es_imagen = in_array($ext, $extensiones_imagen, true) && strpos($mime, 'image/') === 0;

                // ============================================
                // SI ES IMAGEN → convertir a WebP optimizado
                // ============================================
                if ($es_imagen && function_exists('imagewebp')) {

                    // Cargar imagen según su tipo real
                    switch ($mime) {
                        case 'image/jpeg':
                        case 'image/jpg':
                            $img = @imagecreatefromjpeg($documento['tmp_name']);
                            break;
                        case 'image/png':
                            $img = @imagecreatefrompng($documento['tmp_name']);
                            break;
                        case 'image/webp':
                            $img = @imagecreatefromwebp($documento['tmp_name']);
                            break;
                        default:
                            $img = false;
                    }

                    if ($img === false) {
                        throw new Exception("No se pudo procesar la imagen.");
                    }

                    // Preservar transparencia
                    imagepalettetotruecolor($img);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);

                    // Redimensionar si excede 1920px de ancho
                    $ancho_original = imagesx($img);
                    $alto_original = imagesy($img);
                    $max_ancho = 1920;

                    if ($ancho_original > $max_ancho) {
                        $nuevo_ancho = $max_ancho;
                        $nuevo_alto = intval($alto_original * ($max_ancho / $ancho_original));

                        $img_redim = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
                        imagealphablending($img_redim, false);
                        imagesavealpha($img_redim, true);
                        imagecopyresampled(
                            $img_redim,
                            $img,
                            0,
                            0,
                            0,
                            0,
                            $nuevo_ancho,
                            $nuevo_alto,
                            $ancho_original,
                            $alto_original
                        );
                        imagedestroy($img);
                        $img = $img_redim;
                    }

                    // Guardar como WebP con calidad adaptativa
                    $nombre_archivo = $nombre_base . ".webp";
                    $destino = $ruta_carpeta . $nombre_archivo;

                    $calidades = [75, 65, 55, 45];
                    $guardado = false;

                    foreach ($calidades as $q) {
                        if (imagewebp($img, $destino, $q)) {
                            $guardado = true;
                            // Si ya pesa menos de 1 MB, dejamos de bajar calidad
                            if (filesize($destino) <= 1024 * 1024) {
                                break;
                            }
                        }
                    }

                    imagedestroy($img);

                    if (!$guardado) {
                        throw new Exception("No se pudo convertir la imagen a WebP.");
                    }

                } else {
                    // ============================================
                    // NO ES IMAGEN (PDF u otros) → mover tal cual
                    // ============================================
                    $nombre_archivo = $nombre_base . "." . $ext;
                    $destino = $ruta_carpeta . $nombre_archivo;

                    if (!move_uploaded_file($documento['tmp_name'], $destino)) {
                        throw new Exception("No se pudo guardar el archivo en el servidor.");
                    }
                }

                $documento_url = $subcarpeta . $nombre_archivo;
                $tipo = 'dental';

                $resultado = $pacientesModel->subirDocumentoConsulta(
                    $paciente_id,
                    $documento['name'],
                    $documento_url,
                    $tipo,
                    $consulta_id
                );

                echo json_encode([
                    'success' => true,
                    'url' => $documento_url,
                    'documento_id' => $resultado['documento_id'] ?? 0,
                    'peso' => round(filesize($destino) / 1024, 2) . ' KB',
                    'message' => 'Documento guardado correctamente.'
                ]);
                break;
            // 2. ELIMINAR DOCUMENTO DENTAL
            case 'eliminarDocumento':
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    throw new Exception("Método no permitido.");
                }

                $id = intval($_POST['id'] ?? 0);
                if ($id <= 0) {
                    throw new Exception("Elemento inválido.");
                }

                $ok = $pacientesModel->eliminarDocumento($id);
                if (!$ok) {
                    throw new Exception("Error al eliminar de la base de datos.");
                }

                echo json_encode([
                    'success' => true,
                    'id' => $id
                ]);
                break;

            // 3. OBTENER DETALLE DEL HISTORIAL
            case 'obtenerHistorialDetalle':
                $historial_id = intval($_GET['id'] ?? 0);

                if ($historial_id <= 0) {
                    throw new Exception('ID de historial no válido.');
                }

                $detalle = $pacientesModel->obtenerHistorialPorId($historial_id);

                if (!$detalle) {
                    throw new Exception('No se encontró el registro de historial.');
                }

                echo json_encode([
                    'success' => true,
                    'data' => $detalle
                ]);
                break;

            // 4. OBTENER ESTADO DE CUENTA Y EXPEDIENTE


            default:
                throw new Exception("Acción no válida o no definida.");
        }

    } catch (Throwable $e) {
        error_log("Error en Controlador Dental (Acción: {$action}): " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit; // Termina la ejecución AJAX
}

// ==========================================
// SECCIÓN DE CARGA DE LA VISTA (HTML)
// ==========================================
$paciente = $model->obtenerDatosBasicos($id_paciente);

if (!$paciente) {
    die("El paciente solicitado no existe o fue dado de baja.");
}

$clinica_sesion = $_SESSION['clinica_id'] ?? $_SESSION['almacen_id'] ?? 0;
if ($clinica_sesion > 0 && ($paciente['clinica_id'] ?? $paciente['almacen_id'] ?? 0) != $clinica_sesion) {
    die("No tiene permisos para consultar la información de este paciente.");
}

$expediente = $pacientesModel->obtenerExpedienteCompletoFecha(
    $id_paciente,
    $fecha_inicio,
    $fecha_fin
);

$resumen = [
    'total_comprado' => array_sum(array_column($expediente, 'total')),
    'total_pagado' => array_sum(array_column($expediente, 'total_pagado')),
];
$resumen['saldo_total'] = $resumen['total_comprado'] - $resumen['total_pagado'];

require_once __DIR__ . '/../views/historial_dental_view.php';