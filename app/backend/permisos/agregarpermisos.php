<?php
require_once __DIR__ . '/../../../config/conexion.php';

$nombre = $_POST['nombre_modulo'] ?? '';

if (!empty($nombre)) {
    // Si no tienes tabla de modulos, puedes crearla rápido con:
    // CREATE TABLE modulos_sistema (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50) UNIQUE);
    $stmt = $conexion->prepare("INSERT IGNORE INTO modulos_sistema (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
}