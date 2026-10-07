<?php
/**
 * registroController.php
 * Controlador público para el registro de nuevos almacenes / negocios vía AJAX
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/controlAlmacenesModel.php';

$almacenesModel = new controlAlmacenesModel($conexion);

// --- ACCIÓN: REGISTRAR NUEVO ALMACÉN (AJAX) ---
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'registrar') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');

    // ============================================
    // ANTI-FUERZA BRUTA (por IP, 5 intentos / 10 min)
    // ============================================
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $rateFile = sys_get_temp_dir() . '/reg_' . md5($ip) . '.json';
    $now = time();
    $intentos = [];
    if (is_file($rateFile)) {
        $intentos = json_decode(@file_get_contents($rateFile), true) ?: [];
        $intentos = array_filter($intentos, fn($t) => ($now - $t) < 600);
    }
    if (count($intentos) >= 5) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Demasiados intentos. Intenta más tarde.']);
        exit;
    }
    $intentos[] = $now;
    @file_put_contents($rateFile, json_encode($intentos), LOCK_EX);

    try {
        // ============================================
        // SANITIZACIÓN + VALIDACIÓN ESTRICTA
        // ============================================
        $clean = function (string $v, int $max): string {
            $v = trim($v);
            // elimina bytes nulos y caracteres de control
            $v = preg_replace('/[\x00-\x1F\x7F]/u', '', $v);
            if (mb_strlen($v) > $max) {
                throw new Exception("Campo demasiado largo (máx {$max}).");
            }
            return $v;
        };

        $nombreResponsable = $clean($_POST['nombre_responsable'] ?? '', 100);
        $nombreNegocio = $clean($_POST['nombre'] ?? '', 100);
        $ubicacion = $clean($_POST['ubicacion'] ?? '', 150);
        $horaCierre = $clean($_POST['hora_cierre_programada'] ?? '22:00', 8);
        $correo = $clean($_POST['correo'] ?? '', 150);
        $telefono = $clean($_POST['telefono'] ?? '', 20);

        $tipoPlanRaw = $_POST['tipo_plan'] ?? '0';
        if (!ctype_digit((string) $tipoPlanRaw)) {
            throw new Exception("Plan inválido.");
        }
        $tipoPlan = (int) $tipoPlanRaw;

        // ============================================
        // VALIDACIONES
        // ============================================
        if ($nombreResponsable === '')
            throw new Exception("El nombre del responsable es obligatorio.");
        if ($nombreNegocio === '')
            throw new Exception("El nombre del negocio es obligatorio.");

        // Solo letras, números, espacios y . , ' - & (evita payloads raros)
        if (!preg_match('/^[\p{L}\p{N} .,\'\-&]+$/u', $nombreNegocio)) {
            throw new Exception("El nombre del negocio contiene caracteres no permitidos.");
        }
        if (!preg_match('/^[\p{L}\p{N} .,\'\-&]+$/u', $nombreResponsable)) {
            throw new Exception("El nombre del responsable contiene caracteres no permitidos.");
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 150) {
            throw new Exception("Correo electrónico inválido.");
        }

        // Solo dígitos, +, -, espacios, paréntesis; 7–15 dígitos reales
        if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $telefono)) {
            throw new Exception("Teléfono inválido.");
        }
        $soloDigitos = preg_replace('/\D/', '', $telefono);
        if (strlen($soloDigitos) < 7 || strlen($soloDigitos) > 15) {
            throw new Exception("Teléfono inválido.");
        }

        if ($tipoPlan < 1 || $tipoPlan > 4) {
            throw new Exception("Plan de suscripción inválido.");
        }

        // Hora
        if (strlen($horaCierre) === 5)
            $horaCierre .= ':00';
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $horaCierre)) {
            $horaCierre = '22:00:00';
        }

        // ============================================
        // CÓDIGO: 2 letras + 6 dígitos + unicidad + CSPRNG
        // ============================================
        // ============================================
// GENERACIÓN AUTOMÁTICA DEL CÓDIGO
// Formato: 2 letras del negocio + DDMMAA
// Ej: "Helados Manuel" registrado el 07/10/2026 → HM071026
// ============================================

        // 1) Prefijo: 2 primeras letras del nombre (solo A-Z)
        $letras = preg_replace('/[^A-Za-z]/', '', $nombreNegocio);
        $prefijo = strtoupper(substr($letras, 0, 2));

        // Si no hay 2 letras, rellenar con 'X'
        if (strlen($prefijo) < 2) {
            $prefijo = str_pad($prefijo, 2, 'X');
        }

        // 2) Fecha actual en formato DDMMAA
        $fecha = date('dmy'); // ej: 071026

        // 3) Código final
        $codigo = $prefijo . $fecha; // ej: HM071026

        // ============================================
        // ESTRUCTURA
        // ============================================
        $datos = [
            'codigo' => $codigo,
            'nombre' => mb_strtoupper($nombreNegocio, 'UTF-8'),
            'hora_cierre_programada' => $horaCierre,
            'ubicacion' => mb_strtoupper($ubicacion, 'UTF-8'),
            'tipo_plan' => $tipoPlan,
            'pago' => 0,
            'correo' => mb_strtolower($correo, 'UTF-8'),
            'telefono' => $telefono,
        ];

        $id = $almacenesModel->guardar($datos);

        // ============================================
        // RESPUESTA (escapada contra XSS en el cliente)
        // ============================================
        echo json_encode([
            'success' => true,
            'message' => '¡Registro recibido con éxito!',
            'id_almacen' => (int) $id,
            'nombre_responsable' => htmlspecialchars($nombreResponsable, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'nombre_negocio' => htmlspecialchars($nombreNegocio, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        ], JSON_UNESCAPED_UNICODE);

    } catch (Throwable $e) {
        error_log('[registrar] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}