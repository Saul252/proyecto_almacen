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

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            throw new Exception("Método no permitido.");
        }

        $usuario  = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($usuario === '' || $password === '') {
            throw new Exception("Por favor, completa todos los campos.");
        }

        // 🔹 1. Buscar usuario
        $row = $loginModel->obtenerPorUsername($usuario);

        if (!$row) {
            throw new Exception("El usuario ingresado no existe.");
        }

        // 🔹 2. Validar estado activo
        if ((int)$row['activo'] === 0) {
            echo json_encode([
                'status'  => 'warning',
                'message' => 'Tu usuario está deshabilitado. Contacta al administrador.'
            ]);
            exit;
        }

        // 🔹 3. Verificar Contraseña
        if (!password_verify($password, $row['password'])) {
            throw new Exception("La contraseña es incorrecta.");
        }

        // 🔹 4. Iniciar Sesión y regenerar ID por seguridad
        session_regenerate_id(true);

        // Determinación del estado de pago: si almacen_id == 0 es súper admin/libre, de lo contrario evalúa la columna pago
        $almacenId  = $row['almacen_id'] ?? 0;
        $estadoPago = ($almacenId == 0) ? 1 : intval($row['pago'] ?? 0);

        // Registro de datos en la sesión (ESCRITURA)
        $_SESSION['usuario_id']     = $row['id'];
        $_SESSION['username']       = ($estadoPago === 0) ? 0 : $row['username'];
        $_SESSION['nombre']         = $row['nombre'];
        $_SESSION['nombre_almacen'] = $row['nombre_almacen'];
        $_SESSION['rol_id']         = $row['rol_id'];
        $_SESSION['rol']            = $row['rol'];
        $_SESSION['almacen_id']     = $almacenId;
        $_SESSION['plan']           = $row['plan'] ?? 0;
        $_SESSION['ico']            = $row['ico'];
        $_SESSION['logo']           = $row['logo'];
        $_SESSION['pago']           = $estadoPago;
        $_SESSION['login']          = true;

        // 🔹 5. Configuración de Almacén y Hora de Cierre
        $hora_cierre_config = "11:58";
        if ($almacenId > 0) {
            $hora_cierre_config = $loginModel->obtenerHoraCierreAlmacen($almacenId);
        }
        $_SESSION['hora_cierre'] = $hora_cierre_config;

        // 🔹 6. Vinculación automática de perfil Trabajador
        if (strpos($row['username'], 'Trabajador') !== false) {
            $nombreLimpio = str_replace('Trabajador', '', $row['username']);
            $resT = $loginModel->buscarTrabajadorPorNombre($nombreLimpio);

            if ($resT) {
                $_SESSION['trabajador_id'] = $resT['id'];
                if (!empty($resT['almacen_id'])) {
                    $_SESSION['almacen_id'] = $resT['almacen_id'];
                }
            } else {
                $_SESSION['trabajador_id'] = 0;
            }
        } else {
            $_SESSION['trabajador_id'] = 0;
        }

        // ==========================================================
        // 🔓 GUARDADO COMPLETO Y LIBERACIÓN DEL CANDADO DE SESIÓN
        // ==========================================================
        // Una vez asignadas todas las variables a $_SESSION, forzamos la escritura en disco
        // y cerramos el archivo de sesión para desbloquear la navegación del usuario.
        session_write_close();

        // 🔹 7. Redirección condicional según estado de Pago
        if ($estadoPago === 0) {
            echo json_encode([
                'status'      => 'info',
                'message'     => 'Cuenta suspendida por falta de pago.',
                'hora_cierre' => $hora_cierre_config,
                'redirect'    => '/myvet/bloqueo.php?almacen=' . $almacenId
            ]);
            exit;
        }

        // Respuesta JSON exitosa para usuarios al corriente
        echo json_encode([
            'status'      => 'success',
            'message'     => '¡Bienvenido, ' . $row['nombre'] . '!',
            'hora_cierre' => $hora_cierre_config,
            'redirect'    => '/myvet/inicio'
        ]);

    } catch (Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}