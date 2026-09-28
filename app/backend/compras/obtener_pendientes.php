<?php
// Desactivar visualización de errores de texto que rompen el JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Ajusta esta ruta: debe ser la misma que usas en el archivo que SÍ funciona
require_once __DIR__ . '/../../../config/conexion.php';

$response = [];

try {
    if (!isset($_GET['id'])) {
        throw new Exception("ID no recibido");
    }

    $id = intval($_GET['id']);

    // Consulta simplificada para probar conexión
    $sql = "SELECT d.id, d.producto_id, d.cantidad_faltante, p.nombre, p.sku, c.almacen_id
            FROM detalle_compra d 
            JOIN productos p ON d.producto_id = p.id 
            JOIN compras c ON d.compra_id = c.id
            WHERE d.compra_id = ? AND d.cantidad_faltante > 0";

    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error SQL: " . $conexion->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = [
            "id" => (int)$row['id'],
            "producto_id" => (int)$row['producto_id'],
            "cantidad_faltante" => floatval($row['cantidad_faltante']),
            "nombre" => $row['nombre'],
            "sku" => $row['sku'],
            "almacen_original" => (int)$row['almacen_id']
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
exit;