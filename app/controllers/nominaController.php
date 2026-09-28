<?php
/**
 * TrabajadorController.php 
 * Ajustado para integrarse con TrabajadorModel
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/nominaModel.php';
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/almacen_model.php';
// Protegemos la página
protegerPagina('trabajadores');

$nominaModel = new NominaModel($conexion);
$trabajadorModel = new TrabajadorModel($conexion);

$almacenesModel = new AlmacenModel($conexion);
$paginaActual = 'trabajadores';

// --- ACCIÓN: GUARDAR / ACTUALIZAR TRABAJADOR (AJAX) ---
// --- ACCIÓN: GUARDAR / ACTUALIZAR TRABAJADOR (AJAX) ---
if (isset($_POST['action']) && $_POST['action'] === 'guardar') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $datos = [
            'id' => intval($_POST['id'] ?? 0),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'rol' => $_POST['rol'] ?? 'vendedor',
            'estado' => $_POST['estado'] ?? 'activo',
            'salario' => $_POST['salario'] ?? '0',
            // Si el usuario es admin (0), toma el del select; si no, toma el de su sesión
            'almacen_id' => ($_SESSION['almacen_id'] == 0) ? intval($_POST['almacen_id'] ?? 0) : intval($_SESSION['almacen_id'])
        ];

        if (empty($datos['nombre']) || empty($datos['telefono'])) {
            throw new Exception("El nombre y el teléfono son obligatorios.");
        }

        if ($datos['almacen_id'] <= 0) {
            throw new Exception("Debes asignar un almacén válido al trabajador.");
        }

        $resultado = $trabajadorModel->guardar($datos);

        echo json_encode([
            'status' => 'success',
            'message' => "Operación exitosa.",
            'id' => ($datos['id'] > 0) ? $datos['id'] : $conexion->insert_id
        ]);

    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'subirDocumento') {

    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {

        $id = intval($_POST['trabajador_id'] ?? 0);

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
        $resultado = $trabajadorModel->subirDocumentoCompra(
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

    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {



        $id = intval($_POST['id'] ?? 0);


        if ($id <= 0) {
            throw new Exception("Elemento inválida");
        }



        // 🔥 ELIMINAR EN BD
        $ok = $trabajadorModel->eliminarDocumento($id);

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
// --- CARGA DE VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $almacenusu = $_SESSION['almacen_id'];

        // Si es admin (0), listamos todos; si no, solo los de su almacén
        $trabajadores = $nominaModel->listarTrabajadores($almacenusu);
        $almacenes = $almacenesModel->getAlmacenes($almacenusu);
        $tituloPagina = "Gestión de Personal";
        require_once __DIR__ . '/../views/nomina_view.php';

    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }
}
// --- ACCIÓN: ELIMINAR TRABAJADOR (AJAX) ---
if (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0)
            throw new Exception("ID no válido.");

        $resultado = $trabajadorModel->eliminar($id);

        if ($resultado) {
            echo json_encode(['status' => 'success', 'message' => 'Trabajador eliminado.']);
        } else {
            throw new Exception("No se pudo eliminar el registro.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'crearBono') {

    while (ob_get_level())
        ob_end_clean();
    header('Content-Type: application/json');

    try {

        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $trabajador_id = intval($_POST['trabajador_id'] ?? 0);
        $monto = floatval($_POST['monto'] ?? 0);

        if ($trabajador_id <= 0) {
            throw new Exception("Seleccione un trabajador.");
        }

        if ($monto <= 0) {
            throw new Exception("El monto debe ser mayor a cero.");
        }

        if (empty($fecha)) {
            throw new Exception("Seleccione una fecha.");
        }

        $data = [
            'trabajador_id' => $trabajador_id,
            'fecha' => $fecha,
            'monto' => $monto
        ];

        $resultado = $nominaModel->crearBono($data);

        if (!$resultado) {
            throw new Exception("Error al registrar el bono.");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Bono registrado correctamente.'
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
if (isset($_GET['action']) && $_GET['action'] === 'listar') {

    while (ob_get_level())
        ob_end_clean();
    header('Content-Type: application/json');

    try {

        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('monday this week'));
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $almacen = $_GET['almacen'] ?? $_SESSION['almacen_id'];

        $trabajadores = $nominaModel->listarNominaSemanal(
            $fechaInicio,
            $fechaFin,
            $almacen
        );

        echo json_encode([
            'success' => true,
            'data' => $trabajadores
        ]);

    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}