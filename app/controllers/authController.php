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
        $ipKey = hash('sha256', $ip);
        $rateFile = sys_get_temp_dir() . '/login_' . $ipKey . '.json';
        $maxIntentos = 5;
        $ventana = 600;

        $ahora = time();
        $registro = ['intentos' => [], 'bloqueado_hasta' => 0];
        if (is_file($rateFile)) {
            $tmp = json_decode(@file_get_contents($rateFile), true);
            if (is_array($tmp))
                $registro = array_merge($registro, $tmp);
        }

        if ($registro['bloqueado_hasta'] > $ahora) {
            $restante = $registro['bloqueado_hasta'] - $ahora;
            http_response_code(429);
            echo json_encode([
                'status' => 'error',
                'message' => 'Demasiados intentos fallidos. Intenta de nuevo en ' . ceil($restante / 60) . ' minuto(s).'
            ]);
            exit;
        }

        $registro['intentos'] = array_values(array_filter(
            $registro['intentos'],
            fn($t) => ($ahora - $t) < $ventana
        ));

        // ============================================
        // SANITIZACIÓN DE ENTRADA
        // ============================================
        $limpiar = function (string $v, int $max): string {
            $v = trim($v);
            $v = preg_replace('/[\x00-\x1F\x7F]/u', '', $v);
            if (mb_strlen($v) > $max) {
                throw new Exception("Datos inválidos.");
            }
            return $v;
        };

        $usuario = $limpiar($_POST['usuario'] ?? '', 60);
        $password = $_POST['password'] ?? '';
        if (strlen($password) > 200) {
            throw new Exception("Datos inválidos.");
        }

        if ($usuario === '' || $password === '') {
            throw new Exception("Por favor, completa todos los campos.");
        }

        $registrarFallo = function () use (&$registro, $rateFile, $ahora, $maxIntentos, $ventana) {
            $registro['intentos'][] = $ahora;
            if (count($registro['intentos']) >= $maxIntentos) {
                $registro['bloqueado_hasta'] = $ahora + $ventana;
            }
            @file_put_contents($rateFile, json_encode($registro), LOCK_EX);
        };

        $limpiarFallos = function () use ($rateFile) {
            @unlink($rateFile);
        };

        // ============================================
        // BUSCAR USUARIO
        // ============================================
        $row = $loginModel->obtenerPorUsername($usuario);

        $credencialesInvalidas = function () use ($registrarFallo) {
            $registrarFallo();
            throw new Exception("Usuario o contraseña incorrectos.");
        };

        if (!$row) {
            password_verify($password, '$2y$10$usesomesillystringforsalt0123456789012345678901');
            $credencialesInvalidas();
        }

        if ((int) ($row['activo'] ?? 0) === 0) {
            http_response_code(403);
            echo json_encode([
                'status' => 'warning',
                'message' => 'Tu usuario está deshabilitado. Contacta al administrador.'
            ]);
            exit;
        }

        if (!password_verify($password, $row['password'] ?? '')) {
            $credencialesInvalidas();
        }

        if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
            $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
            $loginModel->actualizarPassword((int) $row['id'], $nuevoHash);
        }

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

        $hora_cierre_config = "11:58";
        if ($almacenId > 0) {
            $tmpHora = $loginModel->obtenerHoraCierreAlmacen($almacenId);
            if (is_string($tmpHora) && preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $tmpHora)) {
                $hora_cierre_config = $tmpHora;
            }
        }
        $_SESSION['hora_cierre'] = $hora_cierre_config;

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

        $ipReal = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ipReal = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipReal = trim($ips[0]);
        }
        $ipReal = substr($ipReal, 0, 45);

        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

        try {
            $loginModel->registrarEntrada(
                (int) $row['id'],
                $row['username'],
                $ipReal,
                $userAgent,
                true,
                'login_exitoso'
            );
        } catch (Throwable $e) {
            error_log('[audit_login] ' . $e->getMessage());
        }

        session_write_close();

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
// ================================================================
// --- ACCIÓN: RECUPERAR CONTRASEÑA (AJAX POST) ---
// ================================================================
// ================================================================
// --- ACCIÓN: RECUPERAR CONTRASEÑA (AJAX POST) ---  [MODO TEST]
// ================================================================
// ================================================================
// --- ACCIÓN: RECUPERAR CONTRASEÑA (AJAX POST) ---  [MODO TEST]
// ================================================================
// ================================================================
// --- ACCIÓN: RECUPERAR CONTRASEÑA (AJAX POST) ---  [MODO TEST]
// ================================================================
// ================================================================
// --- ACCIÓN: RECUPERAR CONTRASEÑA (AJAX POST) ---
// ================================================================
// ================================================================
// --- ACCIÓN: RECUPERAR CONTRASEÑA (AJAX POST) ---
// ================================================================
if (($_GET['action'] ?? '') === 'recuperarPassword') {

    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            throw new Exception("Método no permitido.");
        }

        // ============================================
        // SANITIZACIÓN
        // ============================================
        $limpiar = function (string $v, int $max): string {
            $v = trim($v);
            $v = preg_replace('/[\x00-\x1F\x7F]/u', '', $v);
            if (mb_strlen($v) > $max) {
                throw new Exception("Datos inválidos.");
            }
            return $v;
        };

        // ❌ INCORRECTO
        $telefono = ('+525511512854');
        $username = ('casa');
        $correo = ('saulenriquealbatapia253@yahoo.com');

        // ✅ CORRECTO
        $telefono = $limpiar($_POST['telefono'] ?? '', 30);
        $username = $limpiar($_POST['username'] ?? '', 60);
        $correo = $limpiar($_POST['correo'] ?? '', 150);
        // ============================================
        // VALIDACIONES
        // ============================================

        if ($telefono === '' || $username === '' || $correo === '') {
            throw new Exception("Todos los campos son obligatorios.");
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El correo electrónico no es válido.");
        }

        if (!preg_match('/^[+\d\s()\-]{8,30}$/', $telefono)) {
            throw new Exception("El teléfono no tiene un formato válido.");
        }

        // ✅ Normalizar teléfono: solo dígitos
        $telefono = preg_replace('/\D/', '', $telefono);

        if (strlen($telefono) < 8) {
            throw new Exception("El teléfono debe tener al menos 8 dígitos.");
        }

        // ============================================
        // LLAMAR AL MODELO
        // ============================================
        $resultado = $loginModel->recuperarPassword($telefono, $username, $correo);

        // ============================================
        // RESULTADO
        // ============================================
        if (!$resultado['success']) {
            throw new Exception(
                $resultado['message'] ?? 'Los datos no coinciden con ningún usuario registrado.'
            );
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Contraseña restablecida correctamente.',
            'password_nueva' => $resultado['password_nueva'],
            'usuario' => [
                'id' => $resultado['usuario']['id'],
                'nombre' => $resultado['usuario']['nombre'],
                'username' => $resultado['usuario']['username'],
                'correo' => $resultado['usuario']['correo'],
                'telefono' => $resultado['usuario']['telefono']
            ]
        ]);

    } catch (Throwable $e) {
        if (http_response_code() === 200) {
            http_response_code(400);
        }
        error_log('[recuperarPassword] ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}