<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegúrate de que estas rutas sean correctas según tu estructura
require_once __DIR__ . '/../../../config/conexion.php'; 
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

// Verificación de sesión básica para evitar el bloqueo total en pruebas


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'guardar') {
        $id = intval($_POST['id'] ?? 0);
        $nombre = $_POST['nombre'] ?? '';
        $username = $_POST['username'] ?? '';
        $rol_id = intval($_POST['rol_id'] ?? 0);
        $almacen_id = (!empty($_POST['almacen_id'])) ? intval($_POST['almacen_id']) : null;
        $password = $_POST['password'] ?? '';

        if ($id === 0) {
            // --- NUEVO USUARIO ---
            // 1. Validar que el usuario no exista
            $check = $conexion->prepare("SELECT id FROM usuarios WHERE username = ?");
            $check->bind_param("s", $username);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario ya está en uso']);
                exit;
            }

            // 2. Insertar
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, username, password, rol_id, almacen_id, activo) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssii", $nombre, $username, $passHash, $rol_id, $almacen_id);
        } else {
            // --- EDITAR USUARIO ---
            if (!empty($password)) {
                $passHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("UPDATE usuarios SET nombre=?, username=?, password=?, rol_id=?, almacen_id=? WHERE id=?");
                $stmt->bind_param("sssiii", $nombre, $username, $passHash, $rol_id, $almacen_id, $id);
            } else {
                $stmt = $conexion->prepare("UPDATE usuarios SET nombre=?, username=?, rol_id=?, almacen_id=? WHERE id=?");
                $stmt->bind_param("ssiii", $nombre, $username, $rol_id, $almacen_id, $id);
            }
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => '¡Logrado! Usuario guardado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error de BD: ' . $conexion->error]);
        }
        exit;
    }
    
    // Acción Eliminar (Switch de activo/inactivo)
    if ($accion === 'eliminar') {
        $id = intval($_POST['id']);
        $stmt = $conexion->prepare("UPDATE usuarios SET activo = (CASE WHEN activo = 1 THEN 0 ELSE 1 END) WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
        }
        exit;
    }
}