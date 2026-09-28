<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

try {
    // Recibir datos JSON
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (empty($data['nombre'])) {
        throw new Exception('El nombre de la categoría es obligatorio.');
    }

    $nombre = trim($data['nombre']);

    // 1. Verificar si la categoría ya existe (para evitar duplicados)
    $check = $conexion->prepare("SELECT id FROM categorias WHERE nombre = ?");
    $check->bind_param("s", $nombre);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        throw new Exception('Esta categoría ya existe.');
    }

    // 2. Insertar
    $stmt = $conexion->prepare("INSERT INTO categorias (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'id_categoria' => $conexion->insert_id
        ]);
    } else {
        throw new Exception('Error al guardar en la base de datos: ' . $conexion->error);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}