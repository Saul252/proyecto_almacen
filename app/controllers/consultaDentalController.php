<?php
/**
 * pacientesController.php
 * Controlador para la gestión de Pacientes y Consultas Odontológicas
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/LayoutController.php';
require_once __DIR__ . '/../models/pacientesModel.php';
require_once __DIR__ . '/../models/clientesModel.php';

require_once __DIR__ . '/../models/almacen_model.php';

$clientesModel = new ClientesModel($conexion);

$almacenModel = new AlmacenModel($conexion);
$pacientesModel = new PacientesModel($conexion);

$paginaActual = 'clientesPacientes';

// ============================================================================
// ACCIÓN: GUARDAR CONSULTA ODONTOLÓGICA (AJAX)
// ============================================================================
if (isset($_GET['action']) && $_GET['action'] === 'guardarConsulta') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido.');
        }

        // Identification del usuario en sesión
        $usuario_id = (int) (
            $_SESSION['usuario_id']
            ?? $_SESSION['user_id']
            ?? $_SESSION['id']
            ?? 0
        );

        if ($usuario_id <= 0) {
            throw new Exception('No se pudo identificar al usuario de la sesión.');
        }

        // Mapeo de parámetros del paciente y consulta
        $paciente_id = (int) ($_POST['paciente_id'] ?? $_POST['paciente'] ?? 0);
        $motivo_consulta = trim($_POST['motivo_consulta'] ?? '');
        $antecedentes_medicos = trim($_POST['antecedentes_medicos'] ?? '');
        $antecedentes_dentales = trim($_POST['antecedentes_dentales'] ?? '');
        $sintomas = trim($_POST['sintomas'] ?? '');
        $diagnostico = trim($_POST['diagnostico'] ?? $_POST['explicacion'] ?? '');
        $piezas_dentales = trim($_POST['piezas_dentales'] ?? '');
        $procedimiento_realizado = trim($_POST['procedimiento_realizado'] ?? $_POST['tratamiento'] ?? '');
        $avances_notas = trim($_POST['avances_notas'] ?? '');
        $plan_tratamiento = trim($_POST['plan_tratamiento'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');
        $presion_arterial = trim($_POST['presion_arterial'] ?? '');
        $costo = isset($_POST['costo']) && $_POST['costo'] !== '' ? (float) $_POST['costo'] : 0.0;
        $estado = $_POST['estado'] ?? 'Completada';

        // Validaciones obligatorias
        if ($paciente_id <= 0) {
            throw new Exception('Debe seleccionar un paciente válido.');
        }
        if (empty($motivo_consulta)) {
            throw new Exception('El motivo de consulta es obligatorio.');
        }
        if (empty($procedimiento_realizado)) {
            throw new Exception('El procedimiento realizado es obligatorio.');
        }

        // Estructuración de datos para el modelo
        $datos = [
            'paciente_id' => $paciente_id,
            'usuario_id' => $usuario_id,
            'motivo_consulta' => $motivo_consulta,
            'antecedentes_medicos' => $antecedentes_medicos,
            'antecedentes_dentales' => $antecedentes_dentales,
            'sintomas' => $sintomas,
            'diagnostico' => $diagnostico,
            'piezas_dentales' => $piezas_dentales,
            'procedimiento_realizado' => $procedimiento_realizado,
            'avances_notas' => $avances_notas,
            'plan_tratamiento' => $plan_tratamiento,
            'observaciones' => $observaciones,
            'presion_arterial' => $presion_arterial,
            'costo' => $costo,
            'estado' => $estado
        ];

        $idHistorial = $pacientesModel->guardarConsulta($datos);

        echo json_encode([
            'success' => true,
            'message' => 'Consulta odontológica registrada correctamente.',
            'id' => $idHistorial
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

// ============================================================================
// ACCIÓN: GUARDAR / ACTUALIZAR PACIENTE (AJAX)
// ============================================================================
if (isset($_GET['action']) && $_GET['action'] === 'guardarPaciente') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido.');
        }

        $id = (int) ($_POST['paciente_id'] ?? 0);

        $datos = [
            'cliente_id' => (int) ($_POST['cliente_id'] ?? 0),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
            'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
            'sexo' => $_POST['sexo'] ?? 'Otro',
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];

        if ($id > 0) {
            $resultado = $pacientesModel->actualizar($id, $datos);
            $mensaje = "Paciente actualizado correctamente.";
        } else {
            $resultado = $pacientesModel->guardar($datos);
            $mensaje = "Paciente registrado correctamente.";
        }

        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'data' => $resultado
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

// ============================================================================
// ACCIÓN: OBTENER LISTA DE PACIENTES Y CLIENTES (AJAX)
// ============================================================================
if (isset($_GET['action']) && $_GET['action'] === 'pacientes') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        $consultorio = $_GET['consultorio'] ?? $_GET['almacen_id'] ?? $_SESSION['almacen_id'] ?? 0;
        $cliente_id = isset($_GET['cliente_id']) ? (int) $_GET['cliente_id'] : 0;

        $pacientes = $pacientesModel->listarTodos((int) $consultorio, (int) $cliente_id);
        $resClientes = $clientesModel->listarTodos(0);

        $clientes = is_a($resClientes, 'mysqli_result') ? $resClientes->fetch_all(MYSQLI_ASSOC) : ($resClientes ?? []);

        echo json_encode([
            'success' => true,
            'pacientes' => array_values($pacientes),
            'propietarios' => array_values($clientes)
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

// ============================================================================
// ACCIÓN: CAMBIAR ESTADO DE PACIENTE (AJAX)
// ============================================================================
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        $id = (int) ($_POST['id'] ?? 0);
        $estado = (int) ($_POST['estado'] ?? 0);

        if ($id <= 0)
            throw new Exception("ID de paciente no válido.");

        $resultado = $pacientesModel->cambiarEstado($id, $estado);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado con éxito.']);
        } else {
            throw new Exception("No se pudo actualizar el estado del paciente.");
        }
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// ACCIÓN: OBTENER PACIENTE POR ID (AJAX)
// ============================================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        $id = (int) ($_GET['id'] ?? 0);
        $paciente = $pacientesModel->obtenerPorId($id);

        if ($paciente) {
            echo json_encode(['success' => true, 'data' => $paciente]);
        } else {
            throw new Exception('Paciente no encontrado.');
        }
    } catch (Throwable $e) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// ACCIÓN PRINCIPAL (RENDEREAR VISTA)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $almacen_sesion = $_SESSION['almacen_id'] ?? 0;

        $almacenes = $almacenModel->getAlmacenes($almacen_sesion);

        $pacientes = $pacientesModel->listarTodos($almacen_sesion);

        $tituloPagina = "Atención Odontológica - Expedientes";
        require_once __DIR__ . '/../views/consulta_dental_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista de consultas: " . $e->getMessage());
    }
}