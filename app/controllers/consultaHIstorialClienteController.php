<?php
/**
 * Controlador de Ultra-Seguridad para Consulta de Historial Médico y Expedientes
 * Cumplimiento con estándares de protección de datos clínicos y prevención de ataques.
 */

// Iniciar sesión de forma segura si no está activa
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); // Evita robo de sesión por XSS
    ini_set('session.use_only_cookies', 1);
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/consultaClienteHistorialModel.php';
require_once __DIR__ . '/../models/clientesModel.php';

// =========================================================================
// 1. CABECERAS DE SEGURIDAD ESTRICTAS (HTTP Security Headers)
// =========================================================================
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

// IMPORTANTE: Datos médicos NUNCA deben guardarse en caché pública ni de navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$clienteHistorialModel = new ConsultaClienteHistorialModel($conexion);
$clientesModel = new ClientesModel($conexion);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// =========================================================================
// 2. ENDPOINT PÚBLICO: BÚSQUEDA DEL CLIENTE (CON ANTI-FUERZA BRUTA)
// =========================================================================
if ($action === 'obtenerClientePorDatos') {
    header('Content-Type: application/json; charset=utf-8');

    // 2.1 Forzar uso estricto de POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido. Utilice POST.']);
        exit;
    }

    // 2.2 Sistema Anti-Fuerza Bruta / Rate Limiting (Máx. 5 intentos cada 5 min)
    $ahora = time();
    if (!isset($_SESSION['intentos_consulta'])) {
        $_SESSION['intentos_consulta'] = ['fallos' => 0, 'bloqueado_hasta' => 0];
    }

    // Si está bloqueado temporalmente
    if ($ahora < $_SESSION['intentos_consulta']['bloqueado_hasta']) {
        $minutosRestantes = ceil(($_SESSION['intentos_consulta']['bloqueado_hasta'] - $ahora) / 60);
        http_response_code(429); // Too Many Requests
        echo json_encode([
            'success' => false,
            'message' => "Demasiados intentos fallidos por seguridad. Inténtalo nuevamente en {$minutosRestantes} minuto(s)."
        ]);
        exit;
    }

    try {
        // 2.3 Sanitización contra caracteres nulos e inyecciones
        $nombre_comercial = trim(str_replace(chr(0), '', $_POST['nombre_comercial'] ?? ''));
        $fecha_nacimiento = trim(str_replace(chr(0), '', $_POST['fecha_nacimiento'] ?? ''));
        $telefono = trim(str_replace(chr(0), '', $_POST['telefono'] ?? ''));

        // Validaciones de presencia y longitud
        if (empty($nombre_comercial) || mb_strlen($nombre_comercial, 'UTF-8') < 2 || mb_strlen($nombre_comercial, 'UTF-8') > 150) {
            throw new Exception('El nombre comercial o titular es inválido.');
        }

        if (empty($fecha_nacimiento) || strlen($fecha_nacimiento) > 20) {
            throw new Exception('La fecha de nacimiento es obligatoria.');
        }

        if (empty($telefono) || strlen($telefono) > 20) {
            throw new Exception('El número telefónico es obligatorio.');
        }

        // 2.4 Llamada segura: Detecta automáticamente en cuál de los dos modelos está el método
        if (method_exists($clientesModel, 'obtenerPorDatos')) {
            $cliente = $clientesModel->obtenerPorDatos($nombre_comercial, $fecha_nacimiento, $telefono);
        } elseif (method_exists($clienteHistorialModel, 'obtenerPorDatos')) {
            $cliente = $clienteHistorialModel->obtenerPorDatos($nombre_comercial, $fecha_nacimiento, $telefono);
        } else {
            error_log("Error de configuración: El método 'obtenerPorDatos' no existe en ninguno de los modelos.");
            throw new Exception('Error interno del sistema médico. Inténtalo más tarde.');
        }

        // Si no se encuentra el cliente
        if (!$cliente) {
            // Registrar intento fallido
            $_SESSION['intentos_consulta']['fallos']++;
            if ($_SESSION['intentos_consulta']['fallos'] >= 5) {
                $_SESSION['intentos_consulta']['bloqueado_hasta'] = $ahora + 300; // Bloqueo de 5 min
            }

            // Pequeño retardo aleatorio (100-250ms) para neutralizar ataques de tiempo (Timing Attacks)
            usleep(random_int(100000, 250000));

            throw new Exception('No se encontró ningún expediente con los datos proporcionados. Verifica tu información.');
        }

        // Validar si el cliente está activo
        if (isset($cliente['activo']) && intval($cliente['activo']) === 0) {
            throw new Exception('El expediente se encuentra suspendido. Comunícate con la clínica.');
        }

        // Si tuvo éxito, reiniciamos el contador de fallos
        unset($_SESSION['intentos_consulta']);

        // 2.5 Devolver solo los datos necesarios (Whitelist de seguridad)
        $datosSeguros = [
            'id' => $cliente['id'],
            'nombre_comercial' => $cliente['nombre_comercial'],
            'contacto' => $cliente['contacto'] ?? '',
            'telefono' => $cliente['telefono'] ?? '',
            'correo' => $cliente['correo'] ?? '',
            'api_token' => $cliente['api_token']
        ];

        echo json_encode([
            'success' => true,
            'data' => $datosSeguros,
            'redirect' => '/myvet/app/controllers/consultaHIstorialClienteController.php?api_token=' . urlencode($cliente['api_token'])
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================================
// 3. CONTROL DE ACCESO MEDIANTE API TOKEN (Para Expediente y Consultas)
// =========================================================================
$raw_token = $_GET['api_token'] ?? $_POST['api_token'] ?? '';
$api_token = preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($raw_token));
$paginaActual = 'clienteHistorial';

// Validación estricta del token (debe tener entre 16 y 64 caracteres)
if (empty($api_token) || strlen($api_token) < 16 || strlen($api_token) > 64) {
    if (!empty($action)) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: Token de acceso ausente o con formato no válido.']);
        exit;
    }
    http_response_code(403);
    die("Acceso denegado: Se requiere un token de acceso válido.");
}

// Validar que el cliente exista y esté activo ligado a dicho token
$cliente = $clienteHistorialModel->obtenerDatosBasicosPorToken($api_token);

if (!$cliente || intval($cliente['activo'] ?? 1) === 0) {
    if (!empty($action)) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado: El expediente no existe o fue dado de baja.']);
        exit;
    }
    http_response_code(403);
    die("Acceso denegado: El expediente no existe o fue dado de baja.");
}

// =========================================================================
// 4. ACCIONES AJAX AUTORIZADAS (Requieren Token Válido)
// =========================================================================
if (!empty($action)) {
    header('Content-Type: application/json; charset=utf-8');

    switch ($action) {
        case 'obtenerHistorialDetalle':
            try {
                $historial_id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

                if ($historial_id <= 0) {
                    throw new Exception('ID de historial no válido.');
                }

                // La consulta liga estrictamente el historial al token para evitar IDOR (Insecure Direct Object Reference)
                $detalle = $clienteHistorialModel->obtenerHistorialPorIdYToken($historial_id, $api_token);

                if (!$detalle) {
                    throw new Exception('No se encontró el registro de historial o no pertenece a este expediente.');
                }

                echo json_encode([
                    'success' => true,
                    'data' => $detalle
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
            break;
        case 'obtenerHistorialDetalleDental':
            try {
                $historial_id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

                if ($historial_id <= 0) {
                    throw new Exception('ID de historial no válido.');
                }

                // La consulta liga estrictamente el historial al token para evitar IDOR (Insecure Direct Object Reference)
                $detalle = $clienteHistorialModel->obtenerHistorialDentalPorIsYtoken($historial_id, $api_token);

                if (!$detalle) {
                    throw new Exception('No se encontró el registro de historial o no pertenece a este expediente.');
                }

                echo json_encode([
                    'success' => true,
                    'data' => $detalle
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
            break;

        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida o no soportada en la API.'
            ]);
            exit;
            break;
    }
}

// =========================================================================
// 5. CARGA SEGURA DE LA VISTA DEL EXPEDIENTE (HTML)
// =========================================================================

// Validación estricta de fechas (formato exacto YYYY-MM-DD)
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) {
    $fecha_inicio = date('Y-m-01');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
    $fecha_fin = date('Y-m-t');
}

// Obtener expediente ligado estrictamente al token validado
$expediente = $clienteHistorialModel->obtenerExpedienteCompletoPorToken(
    $api_token,
    $fecha_inicio,
    $fecha_fin
);
$dental = $clienteHistorialModel->obtenerExpedienteDentalCompletoFecha(
    $api_token,
    $fecha_inicio,
    $fecha_fin
);
$historialCompleto = [];

// Consultas médicas
if (!empty($expediente)) {
    foreach ($expediente as $consulta) {
        $consulta['_tipo'] = 'medica';
        $historialCompleto[] = $consulta;
    }
}

// Consultas dentales
if (!empty($dental)) {
    foreach ($dental as $consulta) {
        $consulta['_tipo'] = 'dental';
        $historialCompleto[] = $consulta;
    }
}

// Ordenar todo por fecha, más reciente primero
usort($historialCompleto, function ($a, $b) {
    return strtotime($b['fecha_consulta']) <=> strtotime($a['fecha_consulta']);
});
$resumen = [
    'total_comprado' => array_sum(array_column($expediente ?: [], 'costo')),
    'total_pagado' => 0,
];
$resumen['saldo_total'] = $resumen['total_comprado'] - $resumen['total_pagado'];

// Cargar la vista médica del paciente
require_once __DIR__ . '/../views/historialPacienteView.php';