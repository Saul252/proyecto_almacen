<?php
/**
 * miaccesoController.php
 * Controlador para la configuración del almacén / datos de acceso de la sucursal
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/miAccesoModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('ventas');

$almacen_id = $_SESSION['almacen_id'] ?? 0;
$miAccesoModel = new MiAccesoModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_id);

// --- ACCIÓN: OBTENER DATOS DEL ALMACÉN (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDatos') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? $almacen_id);

        if ($id <= 0) throw new Exception("ID de almacén no válido.");

        $datosAlmacen = $miAccesoModel->obtenerPorId($id);

        if ($datosAlmacen) {
            echo json_encode(['success' => true, 'data' => $datosAlmacen]);
        } else {
            throw new Exception("No se encontraron datos para el almacén especificado.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIÓN: GUARDAR / ACTUALIZAR DATOS DE ALMACÉN Y LOGO (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_POST['almacen_id'] ?? $almacen_id);

        if ($id <= 0) throw new Exception("Identificador de almacén no válido.");
        
        // 1. Guardar en sesión solo si viene un color válido y sanitizado
        if (!empty($_POST['colorId'])) {
            // Elimina caracteres no válidos para expresiones de color en CSS (Previene XSS)
            $colorSanitizado = preg_replace('/[^a-zA-Z0-9\s,\.\(\)\%#\-]/', '', $_POST['colorId']);
            $_SESSION['sidebar_bg'] = trim($colorSanitizado);
        }

        $datos = [
            'nombre'                 => trim($_POST['nombre'] ?? ''),
            'hora_cierre_programada' => $_POST['hora_cierre_programada'] ?? null,
            'ubicacion'              => trim($_POST['ubicacion'] ?? ''),
            'logo_actual'            => $_POST['logo_actual'] ?? null,
            'ico_actual'             => $_POST['ico_actual'] ?? null
        ];

        if (empty($datos['nombre'])) {
            throw new Exception("El nombre del almacén es un campo obligatorio.");
        }

        $archivoLogo = $_FILES['logo'] ?? null;
        $archivoIco  = $_FILES['ico'] ?? null;

        $resultado = $miAccesoModel->actualizar($id, $datos, $archivoLogo, $archivoIco);

        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => "Información del almacén actualizada correctamente.",
                'id' => $id,
                'color' => $_SESSION['sidebar_bg'] ?? null // Opcional: retornas el color guardado
            ]);
        } else {
            throw new Exception("Error al actualizar la información en la base de datos.");
        }

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- CARGA DE VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $datosAlmacen = $miAccesoModel->obtenerPorId($almacen_id);
        $paginaActual = 'miacceso';
        $tituloPagina = "Configuración de Mi Acceso / Almacén";

        require_once __DIR__ . '/../views/miacceso_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista de Mi Acceso: " . $e->getMessage());
    }
}