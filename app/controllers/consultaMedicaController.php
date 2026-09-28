<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/LayoutController.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mascotasModel.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/consultaMedicaModel.php';

$almacenModel = new AlmacenModel($conexion);
$clientesModel = new ClientesModel($conexion);
$mascotasModel = new MascotasModel($conexion);
$consultaMedicaModel = new ConsultaMedicaModel($conexion);
$paginaActual = 'clientesPacientes';

// =========================================================
// ACCIÓN: GUARDAR / ACTUALIZAR CLIENTE
// =========================================================
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método de solicitud no permitido.');
        }
        $id = intval($_POST['cliente_id'] ?? 0);
        $datos = [
            'nombre_comercial' => trim($_POST['nombre_comercial'] ?? ''),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'rfc' => strtoupper(trim($_POST['rfc'] ?? '')),
            'regimen_fiscal' => $_POST['regimen_fiscal'] ?? '',
            'codigo_postal' => $_POST['codigo_postal'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'uso_cfdi' => $_POST['uso_cfdi'] ?? 'G03'
        ];
        if (
            empty($datos['nombre_comercial']) ||
            empty($datos['rfc'])
        ) {
            throw new Exception(
                "Nombre comercial y RFC son campos obligatorios."
            );
        }
        if ($id > 0) {
            $resultado = $clientesModel->actualizar(
                $id,
                $datos
            );
            $mensaje = "Cliente actualizado correctamente.";
        } else {
            $resultado = $clientesModel->guardar($datos);
            $mensaje = "Cliente registrado correctamente.";
        }
        echo json_encode([
            'success' => true,
            'message' => $mensaje
        ]);
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================
// ACCIÓN: GUARDAR CONSULTA MÉDICA
// =========================================================
if (isset($_GET['action']) && $_GET['action'] === 'guardarConsulta') {
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido.');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario_id = (int) (
            $_SESSION['usuario_id'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0
        );
        if ($usuario_id <= 0) {
            throw new Exception('No se pudo identificar al usuario de la sesión. Inicia sesión nuevamente.');
        }

        $paciente_id = (int) ($_POST['paciente_id'] ?? $_POST['paciente'] ?? 0);
        $fecha_consulta = trim($_POST['fecha_consulta'] ?? date('Y-m-d H:i:s'));
        $motivo_consulta = trim($_POST['motivo_consulta'] ?? '');
        $antecedentes_medicos = trim($_POST['antecedentes_medicos'] ?? '');
        $sintomas = trim($_POST['sintomas'] ?? '');
        $diagnostico = trim($_POST['diagnostico'] ?? $_POST['explicacion'] ?? '');
        $procedimiento_realizado = trim($_POST['procedimiento_realizado'] ?? '');
        $avances_notas = trim($_POST['avances_notas'] ?? '');
        $plan_tratamiento = trim($_POST['plan_tratamiento'] ?? $_POST['tratamiento'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');

        $presion_arterial = trim($_POST['presion_arterial'] ?? '');
        $temperatura = (isset($_POST['temperatura']) && $_POST['temperatura'] !== '') ? (float) $_POST['temperatura'] : null;
        $estatura = (isset($_POST['estatura']) && $_POST['estatura'] !== '') ? (float) $_POST['estatura'] : null;
        $peso = (isset($_POST['peso']) && $_POST['peso'] !== '') ? (float) $_POST['peso'] : null;

        $costo = (isset($_POST['costo']) && $_POST['costo'] !== '') ? (float) $_POST['costo'] : 0.00;
        $estado = (isset($_POST['estado']) && $_POST['estado'] !== '') ? (int) $_POST['estado'] : 1;

        if ($paciente_id <= 0) {
            throw new Exception('No se recibió un paciente válido.');
        }
        if ($motivo_consulta === '') {
            throw new Exception('El motivo de consulta es obligatorio.');
        }
        if ($sintomas === '') {
            throw new Exception('Los síntomas son obligatorios.');
        }
        if ($diagnostico === '') {
            throw new Exception('El diagnóstico es obligatorio.');
        }
        if ($plan_tratamiento === '') {
            throw new Exception('El plan de tratamiento es obligatorio.');
        }

        $datos = [
            'paciente_id' => $paciente_id,
            'usuario_id' => $usuario_id,
            'fecha_consulta' => $fecha_consulta,
            'motivo_consulta' => $motivo_consulta,
            'antecedentes_medicos' => $antecedentes_medicos,
            'sintomas' => $sintomas,
            'diagnostico' => $diagnostico,
            'procedimiento_realizado' => $procedimiento_realizado,
            'avances_notas' => $avances_notas,
            'plan_tratamiento' => $plan_tratamiento,
            'observaciones' => $observaciones,
            'presion_arterial' => $presion_arterial,
            'temperatura' => $temperatura,
            'estatura' => $estatura,
            'peso' => $peso,
            'costo' => $costo,
            'estado' => $estado
        ];

        if (!isset($consultaMedicaModel) || !method_exists($consultaMedicaModel, 'guardar')) {
            throw new Exception('Error interno: El modelo de consulta médica no está inicializado.');
        }

        $idConsulta = $consultaMedicaModel->guardar($datos);

        if (!$idConsulta) {
            throw new Exception('No se pudo guardar la consulta en la base de datos.');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Consulta médica registrada correctamente.',
            'id' => $idConsulta
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

// =========================================================
// ACCIÓN: CAMBIAR ESTADO
// =========================================================
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    try {
        $id = intval($_POST['id'] ?? 0);
        $estado = intval($_POST['estado'] ?? 0);
        if ($id <= 0) {
            throw new Exception("ID de cliente no válido.");
        }
        $resultado = $clientesModel->cambiarEstado(
            $id,
            $estado
        );
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Estado actualizado con éxito.'
            ]);
        } else {
            throw new Exception("No se pudo actualizar el estado.");
        }
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================
// ACCIÓN: OBTENER DATOS POR ID
// =========================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    try {
        $id = intval($_GET['id'] ?? 0);
        $cliente = $clientesModel->obtenerPorId($id);
        if ($cliente) {
            echo json_encode([
                'success' => true,
                'data' => $cliente
            ]);
        } else {
            throw new Exception(
                'Cliente no encontrado.'
            );
        }
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================
// CARGAR VISTA
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $almacen_sesion = $_SESSION['almacen_id'] ?? 0;
    $almacenes = $almacenModel->getAlmacenes($almacen_sesion);
    try {
        $almacen_sesion = $_SESSION['almacen_id'] ?? 0;

        $clientes = $clientesModel->listarTodos($almacen_sesion);

        $tituloPagina =
            "Administración de Consultas Médicas";

        require_once __DIR__ .
            '/../views/consulta_medica_view.php';
    } catch (Exception $e) {
        die(
            "Error al cargar la vista: " .
            $e->getMessage()
        );
    }
}