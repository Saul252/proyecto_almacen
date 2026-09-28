<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegúrate de que estas rutas sean correctas según tu estructura
require_once __DIR__ . '/../../../config/conexion.php'; 
require_once __DIR__ . '/../../../includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenemos los permisos (si no viene nada, es un array vacío)
    $permisos_post = $_POST['permisos'] ?? [];

    // Iniciamos transacción para asegurar que no se borren permisos si algo falla al insertar
    $conexion->begin_transaction();

    try {
        // 1. Limpiar todos los permisos actuales
        $conexion->query("DELETE FROM permisos_roles");

        // 2. Preparar la inserción
        $stmt = $conexion->prepare("INSERT INTO permisos_roles (rol_id, modulo) VALUES (?, ?)");

        foreach ($permisos_post as $rol_id => $modulos) {
            foreach ($modulos as $modulo) {
                $stmt->bind_param("is", $rol_id, $modulo);
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar permiso para el rol $rol_id");
                }
            }
        }

        // Si todo salió bien, guardamos
        $conexion->commit();

        // Limpiar la caché de la sesión para que el cambio se note al instante
        unset($_SESSION['permisos_cache']);

        echo json_encode([
            'status' => 'success',
            'message' => 'Matriz de seguridad actualizada correctamente'
        ]);

    } catch (Exception $e) {
        // Si algo falla, revertimos al estado anterior (Rollback)
        $conexion->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la transacción: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ]);
}