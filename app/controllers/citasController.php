<?php
/**
 * citasController.php 
 * Controlador para la gestión e interacción AJAX de Citas Médicas
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/citasModel.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('citasDentales');

$citasModel = new CitasModel($conexion);
$clientesModel = new ClientesModel($conexion);
$almacenModel = new AlmacenModel($conexion);

$paginaActual = 'citas';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

// --- ACCIÓN: GUARDAR / ACTUALIZAR CITA (AJAX) ---```php
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = intval($_POST['cita_id'] ?? 0);

        $datos = [
            'almacen' => intval($_POST['almacen_id'] ?? $almacen_usuario),
            'paciente_id' => intval($_POST['paciente_id'] ?? 0),
            'fecha' => trim($_POST['fecha'] ?? ''),
            'tipo' => trim($_POST['tipoCita'] ?? ''),
            'detalles' => trim($_POST['detalles'] ?? ''),
            'atendera' => intval($_POST['atendera'] ?? 0)
        ];

        // Validar datos obligatorios
        if (
            $datos['paciente_id'] <= 0 ||
            empty($datos['fecha']) ||
            $datos['atendera'] <= 0
        ) {
            throw new Exception(
                "El paciente, la fecha/hora y el médico/personal que atenderá son campos obligatorios."
            );
        }

        // Obtener IP del cliente
        $ipReal = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ipReal = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipReal = trim($ips[0]);
        }

        $ipReal = substr($ipReal, 0, 45);

        $userAgent = substr(
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            0,
            500
        );

        // Datos del usuario autenticado
        $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
        $username = (string) ($_SESSION['nombre'] ?? 'Usuario desconocido');

        // Crear o actualizar cita
        if ($id > 0) {

            $resultado = $citasModel->actualizar(
                $id,
                $datos,
                $almacen_usuario
            );

            if (!$resultado) {
                throw new Exception(
                    "No se pudo actualizar la cita o no tiene permisos."
                );
            }

            $citaId = $id;
            $mensaje = "Cita médica actualizada correctamente.";
            $motivo = "Actualizó la cita dental ID: " . $citaId;

        } else {

            $resultado = $citasModel->guardar($datos);

            if (!$resultado || empty($resultado['id'])) {
                throw new Exception(
                    "No se pudo agendar la cita. Intente de nuevo."
                );
            }

            $citaId = (int) $resultado['id'];
            $mensaje = "Cita médica programada correctamente.";
            $motivo = "Registró una nueva cita dental ID: " . $citaId;
        }

        // Registrar auditoría
        try {

            $citasModel->registrarEntrada(
                $usuarioId > 0 ? $usuarioId : null,
                $username,
                $ipReal,
                $userAgent,
                true,
                $motivo,
                'dental'
            );

        } catch (Throwable $e) {
            error_log('[audit_cita_dental] ' . $e->getMessage());
        }

        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'id' => $citaId
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = intval($_POST['id'] ?? 0);
        $estado = trim($_POST['estado'] ?? '');

        // Estados permitidos
        $estadosPermitidos = [
            'pendiente',
            'completada',
            'cancelada'
        ];

        if (
            $id <= 0 ||
            !in_array($estado, $estadosPermitidos, true)
        ) {
            throw new Exception(
                "Parámetros no válidos para actualizar el estado."
            );
        }

        // Obtener IP del cliente
        $ipReal = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ipReal = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipReal = trim($ips[0]);
        }

        $ipReal = substr($ipReal, 0, 45);

        $userAgent = substr(
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            0,
            500
        );

        // Usuario autenticado
        $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
        $username = (string) ($_SESSION['nombre'] ?? 'Usuario desconocido');

        // Actualizar estado
        $resultado = $citasModel->cambiarEstado(
            $id,
            $estado,
            $almacen_usuario
        );

        if (!$resultado) {
            throw new Exception(
                "No se pudo actualizar el estado o no tiene permisos."
            );
        }

        // Registrar auditoría
        try {

            $citasModel->registrarEntrada(
                $usuarioId > 0 ? $usuarioId : null,
                $username,
                $ipReal,
                $userAgent,
                true,
                'cambio_estado_cita_dental_' . $estado . 'cita id' . $id,
                'dental'
            );

        } catch (Throwable $e) {
            error_log('[audit_login] ' . $e->getMessage());
        }

        echo json_encode([
            'success' => true,
            'message' => 'Estado de la cita actualizado a: ' . ucfirst($estado)
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}



// --- ACCIÓN: OBTENER DATOS POR ID (Editar - AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? 0);

        $cita = $citasModel->obtenerPorId($id);

        if ($cita) {
            echo json_encode(['success' => true, 'data' => $cita]);
        } else {
            throw new Exception('Cita no encontrada o acceso denegado.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIÓN: LISTADO AJAX (Con filtros) ---
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $filtros = [
            'search' => $_GET['f_search'] ?? '',
            'fecha' => $_GET['f_fecha'] ?? '',     // Espera YYYY-MM-DD
            'almacen' => $_GET['f_almacen'] ?? 0,
            'atendera' => $_GET['f_atendera'] ?? 0,
            'tipo' => $_GET['f_tipo'] ?? ''   // tipo de consulta
        ];

        $usuario_id = $_SESSION['usuario_id'] ?? 0;

        $data = $citasModel->listarCitasFiltros($filtros, $usuario_id);
        echo json_encode($data);

    } catch (Throwable $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {


        // Cargar catálogo de clientes (pacientes) para el selector modal


        $tituloPagina = "Gestión de Citas Médicas";
        require_once __DIR__ . '/../views/citas_dentales_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista de citas: " . $e->getMessage());
    }
}