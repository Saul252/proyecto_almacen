<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/autentificacionModel.php';

$loginModel = new AuthModel($conexion);

// --- ACCIÓN: LOGIN (AJAX POST) ---
if (isset($_GET['action']) && $_GET['action'] === 'login') {

    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');

    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            throw new Exception("Método no permitido.");
        }

        // ============================================
        // ANTI-FUERZA BRUTA POR IP
        // 5 intentos fallidos → bloqueo 10 minutos
        // ============================================
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ipKey = hash('sha256', $ip); // no exponer la IP en el nombre del archivo
        $rateFile = sys_get_temp_dir() . '/login_' . $ipKey . '.json';
        $maxIntentos = 5;
        $ventana = 600; // 10 minutos

        $ahora = time();
        $registro = ['intentos' => [], 'bloqueado_hasta' => 0];
        if (is_file($rateFile)) {
            $tmp = json_decode(@file_get_contents($rateFile), true);
            if (is_array($tmp))
                $registro = array_merge($registro, $tmp);
        }

        // ¿Está bloqueada la IP?
        if ($registro['bloqueado_hasta'] > $ahora) {
            $restante = $registro['bloqueado_hasta'] - $ahora;
            http_response_code(429);
            echo json_encode([
                'status' => 'error',
                'message' => 'Demasiados intentos fallidos. Intenta de nuevo en ' . ceil($restante / 60) . ' minuto(s).'
            ]);
            exit;
        }

        // Limpiar intentos viejos (fuera de la ventana)
        $registro['intentos'] = array_values(array_filter(
            $registro['intentos'],
            fn($t) => ($ahora - $t) < $ventana
        ));

        // ============================================
        // SANITIZACIÓN DE ENTRADA
        // ============================================
        $limpiar = function (string $v, int $max): string {
            $v = trim($v);
            $v = preg_replace('/[\x00-\x1F\x7F]/u', '', $v); // bytes de control
            if (mb_strlen($v) > $max) {
                throw new Exception("Datos inválidos.");
            }
            return $v;
        };

        $usuario = $limpiar($_POST['usuario'] ?? '', 60);
        $password = $_POST['password'] ?? ''; // ← NO aplicar trim/regex a la contraseña
        if (strlen($password) > 200) {
            throw new Exception("Datos inválidos.");
        }

        if ($usuario === '' || $password === '') {
            throw new Exception("Por favor, completa todos los campos.");
        }

        // ============================================
        // FUNCIÓN PARA REGISTRAR FALLO + GUARDAR
        // ============================================
        $registrarFallo = function () use (&$registro, $rateFile, $ahora, $maxIntentos, $ventana) {
            $registro['intentos'][] = $ahora;
            if (count($registro['intentos']) >= $maxIntentos) {
                $registro['bloqueado_hasta'] = $ahora + $ventana;
            }
            @file_put_contents($rateFile, json_encode($registro), LOCK_EX);
        };

        $limpiarFallos = function () use ($rateFile) {
            @unlink($rateFile); // login exitoso → resetear contador
        };

        // ============================================
        // BUSCAR USUARIO
        // ============================================
        $row = $loginModel->obtenerPorUsername($usuario);

        // ⚠️ Mensaje genérico: NO revelar si el usuario existe o no
        $credencialesInvalidas = function () use ($registrarFallo) {
            $registrarFallo();
            throw new Exception("Usuario o contraseña incorrectos.");
        };

        if (!$row) {
            // Simula un hash para igualar tiempos (evita timing attack)
            password_verify($password, '$2y$10$usesomesillystringforsalt0123456789012345678901');
            $credencialesInvalidas();
        }

        // ============================================
        // ESTADO ACTIVO
        // ============================================
        if ((int) ($row['activo'] ?? 0) === 0) {
            // No registrar como fallo (no es intento de hackeo), pero sí mensaje claro
            http_response_code(403);
            echo json_encode([
                'status' => 'warning',
                'message' => 'Tu usuario está deshabilitado. Contacta al administrador.'
            ]);
            exit;
        }

        // ============================================
        // VERIFICAR CONTRASEÑA
        // ============================================
        if (!password_verify($password, $row['password'] ?? '')) {
            $credencialesInvalidas();
        }

        // Rehash automático si el coste cambió
        if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
            $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
            $loginModel->actualizarPassword((int) $row['id'], $nuevoHash); // opcional
        }

        // Login OK → limpiar contador de esa IP
        $limpiarFallos();

        // ============================================
        // INICIAR SESIÓN
        // ============================================
        session_regenerate_id(true);

        $almacenId = (int) ($row['almacen_id'] ?? 0);
        $estadoPago = ($almacenId === 0) ? 1 : (int) ($row['pago'] ?? 0);

        $_SESSION['usuario_id'] = (int) $row['id'];
        $_SESSION['username'] = ($estadoPago === 0) ? 0 : $row['username'];
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['nombre_almacen'] = $row['nombre_almacen'];
        $_SESSION['rol_id'] = (int) $row['rol_id'];
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['almacen_id'] = $almacenId;
        $_SESSION['plan'] = (int) ($row['plan'] ?? 0);
        $_SESSION['ico'] = $row['ico'];
        $_SESSION['logo'] = $row['logo'];
        $_SESSION['pago'] = $estadoPago;
        $_SESSION['login'] = true;

        // ============================================
        // HORA DE CIERRE (validada)
        // ============================================
        $hora_cierre_config = "11:58";
        if ($almacenId > 0) {
            $tmpHora = $loginModel->obtenerHoraCierreAlmacen($almacenId);
            if (is_string($tmpHora) && preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $tmpHora)) {
                $hora_cierre_config = $tmpHora;
            }
        }
        $_SESSION['hora_cierre'] = $hora_cierre_config;

        // ============================================
        // VINCULACIÓN TRABAJADOR
        // ============================================
        if (str_starts_with($row['username'], 'Trabajador')) {
            $nombreLimpio = trim(substr($row['username'], strlen('Trabajador')));
            $resT = $loginModel->buscarTrabajadorPorNombre($nombreLimpio);
            if ($resT) {
                $_SESSION['trabajador_id'] = (int) $resT['id'];
                if (!empty($resT['almacen_id'])) {
                    $_SESSION['almacen_id'] = (int) $resT['almacen_id'];
                }
            } else {
                $_SESSION['trabajador_id'] = 0;
            }
        } else {
            $_SESSION['trabajador_id'] = 0;
        }

        // Escribir sesión y liberar el lock
        session_write_close();

        // ============================================
        // REDIRECCIÓN SEGÚN ESTADO DE PAGO
        // ============================================
        if ($estadoPago === 0) {
            echo json_encode([
                'status' => 'info',
                'message' => 'Cuenta suspendida por falta de pago.',
                'hora_cierre' => $hora_cierre_config,
                'redirect' => '/myvet/bloqueo.php?almacen=' . $almacenId
            ]);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'message' => '¡Bienvenido, ' . htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') . '!',
            'hora_cierre' => $hora_cierre_config,
            'redirect' => '/myvet/inicio'
        ]);

    } catch (Throwable $e) {
        if (http_response_code() === 200) {
            http_response_code(400);
        }
        error_log('[login] ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}