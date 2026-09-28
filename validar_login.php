<?php
session_start();
require_once __DIR__ . '/config/conexion.php';

// Indicamos que la respuesta será JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit();
}

$usuario  = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($usuario === '' || $password === '') {
    echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos"]);
    exit();
}

// 1. Buscamos al usuario (quitamos activo=1 de la consulta para validar el estado después)
$sql = "SELECT u.id, u.nombre, u.username, u.password, u.rol_id, u.almacen_id, u.activo, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE u.username = ? 
        LIMIT 1";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Error interno en el servidor"]);
    exit();
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
    $row = $resultado->fetch_assoc();

    // 2. ¿El usuario existe pero está deshabilitado?
    if ($row['activo'] == 0) {
        echo json_encode(["status" => "warning", "message" => "Tu usuario está deshabilitado. Contacta al administrador."]);
        exit();
    }

    // 3. Verificar Contraseña
    if (password_verify($password, $row['password'])) {
    session_regenerate_id(true);

    // Guardamos las variables globales en la sesión
    $_SESSION['usuario_id'] = $row['id'];
    $_SESSION['username']   = $row['username']; // Ej: "ManuelTrabajador"
    $_SESSION['nombre']     = $row['nombre'];
    $_SESSION['rol_id']     = $row['rol_id'];
    $_SESSION['rol']        = $row['rol'];
    $_SESSION['almacen_id'] = $row['almacen_id'] ?? 0;
    $_SESSION['login']      = true;
    $id_almacen_usuario = $row['almacen_id'] ?? 0;
    $hora_cierre_config = "11:58"; // Valor por defecto
    if ($id_almacen_usuario > 0) {
        $sqlA = "SELECT hora_cierre_programada FROM almacenes WHERE id = ? LIMIT 1";
        $stmtA = $conexion->prepare($sqlA);
        $stmtA->bind_param("i", $id_almacen_usuario);
        $stmtA->execute();
        $resA = $stmtA->get_result()->fetch_assoc();
        
        if ($resA) {
            // Guardamos la hora en sesión (ej: "16:30:00")
            $hora_cierre_config = $resA['hora_cierre_programada'];
        }
    }
    $_SESSION['hora_cierre'] = $hora_cierre_config;
    $urlRedireccion = 'app/views/inicio.php';

    /**
     * ── VINCULACIÓN AUTOMÁTICA DE PERFIL TRABAJADOR ──
     * Condición: El username debe contener la palabra "Trabajador"
     */
    if (strpos($_SESSION['username'], 'Trabajador') !== false) {
        
        // 1. Extraemos el nombre limpio (quitamos "Trabajador")
        // ManuelTrabajador -> Manuel
        $nombreLimpio = str_replace('Trabajador', '', $_SESSION['username']);

        // 2. Buscamos en la tabla trabajadores por coincidencia de nombre
        $sqlT = "SELECT id, almacen_id FROM trabajadores WHERE nombre LIKE ? LIMIT 1";
        $stmtT = $conexion->prepare($sqlT);
        
        // Usamos el nombre limpio para buscar (ej. "Manuel")
        $busqueda = $nombreLimpio . "%"; 
        $stmtT->bind_param("s", $busqueda);
        $stmtT->execute();
        $resT = $stmtT->get_result()->fetch_assoc();

        if ($resT) {
            // Si lo encuentra, guardamos su ID de la tabla trabajadores
            $_SESSION['trabajador_id'] = $resT['id'];
            
            // Opcional: Si el trabajador tiene un almacén asignado distinto, lo actualizamos
            if (!empty($resT['almacen_id'])) {
                $_SESSION['almacen_id'] = $resT['almacen_id'];
            }
        } else {
            // Si el nombre de usuario dice "Trabajador" pero no existe en la tabla
            $_SESSION['trabajador_id'] = 0;
        }
    } else {
        // Para administradores o vendedores que no usan el sufijo
        $_SESSION['trabajador_id'] = 0;
    }
     echo json_encode([
        "status" => "success", 
        "message" => "¡Bienvenido, " . $row['nombre'] . "!",
        "hora_cierre" => $_SESSION['hora_cierre'], // La hora que ya extrajimos
        "redirect" => $urlRedireccion           // <--- NUEVA DIRECCIÓN
    ]);
    } else {
        echo json_encode(["status" => "error", "message" => "La contraseña es incorrecta"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "El usuario ingresado no existe"]);
}
exit();