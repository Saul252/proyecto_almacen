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

protegerPagina('citasMedicas');

$citasModel = new CitasModel($conexion);
$clientesModel = new ClientesModel($conexion);
$almacenModel = new AlmacenModel($conexion);

$paginaActual = 'citas';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

// --- ACCIÓN: GUARDAR / ACTUALIZAR CITA (AJAX) ---

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
            'detalles' => trim($_POST['detalles'] ?? ''),
            'atendera' => trim($_POST['atendera'] ?? ''),
            'tipo' => 'medica'
        ];

        // Validar campos obligatorios
        if (
            $datos['paciente_id'] <= 0 ||
            empty($datos['fecha']) ||
            empty($datos['atendera'])
        ) {
            throw new Exception(
                "El paciente, la fecha/hora y el médico/personal que atenderá son campos obligatorios."
            );
        }

        // Detectar IP
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

        // Determinar la operación
        if ($id > 0) {

            $resultado = $citasModel->actualizar(
                $id,
                $datos,
                $almacen_usuario
            );

            $mensaje = "Cita médica actualizada correctamente.";
            $motivo = "Actualizó la cita médica ID: " . $id;
            $citaId = $id;

        } else {

            $resultado = $citasModel->guardar($datos);

            if (!$resultado || empty($resultado['id'])) {
                throw new Exception(
                    "No se pudo agendar la cita. Intente de nuevo."
                );
            }

            $citaId = (int) $resultado['id'];
            $mensaje = "Cita médica programada correctamente.";
            $motivo = "Registró una nueva cita médica ID: " . $citaId;
        }

        // Registrar auditoría después de guardar la cita
        try {

            $citasModel->registrarEntrada(
                $usuarioId > 0 ? $usuarioId : null,
                $username,
                $ipReal,
                $userAgent,
                true,
                $motivo,
                'medica'
            );

        } catch (Throwable $e) {
            error_log('[audit_login] ' . $e->getMessage());
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
// --- ACCIÓN: CAMBIAR ESTADO DE LA CITA (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {

    // 1) Limpiar TODOS los niveles de output buffering (no solo el superior)
    //    Esto evita que warnings/notices/BOM/includes dejen basura antes del JSON.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // 2) No mostrar errores al cliente, pero sí loguearlos
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);

    // 3) Headers JSON
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');

    // 4) Buffer limpio para detectar cualquier salida accidental
    ob_start();

    $respuesta = ['success' => false, 'message' => 'Error desconocido'];

    try {
        $id = intval($_POST['id'] ?? 0);
        $estado = trim((string) ($_POST['estado'] ?? ''));

        // Estados permitidos
        $estadosPermitidos = ['pendiente', 'completada', 'cancelada'];
        if ($id <= 0 || !in_array($estado, $estadosPermitidos, true)) {
            throw new Exception('Parámetros no válidos para actualizar el estado.');
        }

        // ============================================
        // IP real y User-Agent
        // ============================================
        $ipReal = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ipReal = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipReal = trim($ips[0]);
        }
        $ipReal = substr($ipReal, 0, 45);
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

        // ============================================
        // Cambio de estado en el modelo
        // ============================================
        $resultado = $citasModel->cambiarEstado($id, $estado, $almacen_usuario);

        if (!$resultado) {
            throw new Exception('No se pudo actualizar el estado o no tiene permisos.');
        }

        // ============================================
        // REGISTRAR ENTRADA EN AUDITORÍA
        // ============================================
        try {
            $citasModel->registrarEntrada(
                $usuarioId > 0 ? $usuarioId : null,
                $username,
                $ipReal,
                $userAgent,
                true,
                'actualizacion de cita ' . $id . ' estado ' . $estado,
                'medica'
            );
        } catch (Throwable $e) {
            error_log('[audit_citas] ' . $e->getMessage());
            // No relanzamos: el cambio de estado ya se hizo y no queremos fallar por auditoría.
        }

        $respuesta = [
            'success' => true,
            'message' => 'Estado de la cita actualizado a: ' . ucfirst($estado),
            'data' => [
                'id' => $id,
                'estado' => $estado,
            ],
        ];

    } catch (Throwable $e) {
        $respuesta = [
            'success' => false,
            'message' => $e->getMessage(),
        ];
        error_log('[cambiarEstado] ' . $e->getMessage());
    }

    // 5) Detectar salida accidental (warnings, echos, BOM) que se coló dentro del buffer
    $salidaAccidental = ob_get_clean();
    if ($salidaAccidental !== '' && $salidaAccidental !== false) {
        error_log('[cambiarEstado salida inesperada] ' . substr($salidaAccidental, 0, 1000));
    }

    // 6) Emitir el JSON limpio y salir
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
// --- ACCIÓN: OBTENER DATOS POR ID (Editar - AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? 0);

        $cita = $citasModel->obtenerPorId($id, $almacen_usuario);

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
            'almacen' => $_GET['f_almacen'] ?? $almacen_usuario,
            'atendera' => $_GET['f_atendera'] ?? 0   // ID del usuario que atiende
        ];

        $usuario_id = $_SESSION['usuario_id'] ?? 0;

        $data = $citasModel->listarCitasFiltrosMedico($filtros, $usuario_id);
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
        require_once __DIR__ . '/../views/citas_medicas_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista de citas: " . $e->getMessage());
    }
}