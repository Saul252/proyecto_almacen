<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';
session_start();

header('Content-Type: application/json');

$usuario_id = $_SESSION['usuario_id'] ?? 0;
$rol_id     = $_SESSION['rol_id'] ?? 0;
$almacen_id = $_GET['almacen_id'] ?? null; // Almacén seleccionado en el select del Admin

// Construir la condición de filtrado según el Rol
// Si es Admin y eligió un almacén, filtramos por ese. 
// Si no es admin, deberíamos filtrar por el almacén asignado al usuario (ajusta según tu lógica de sesión)
$filtro_arribos = "";
$filtro_envios  = "";

if ($rol_id == 1) { // ADMINISTRADOR
    if ($almacen_id) {
        $filtro_arribos = "AND m.almacen_destino_id = $almacen_id";
        $filtro_envios  = "AND m.almacen_origen_id = $almacen_id";
    } else {
        // Si el admin no elige nada, no mostramos datos para evitar sobrecarga
        echo json_encode(['arribos' => [], 'envios' => []]);
        exit;
    }
} else {
    // ALMACENISTA: Aquí asumo que m.usuario_envia_id o una relación usuario-almacén define qué ve.
    // Por ahora, usaremos el almacén destino/origen donde el usuario está involucrado.
    $filtro_arribos = "AND m.almacen_destino_id IN (SELECT id FROM almacenes)"; // Ajustar a su sucursal real
    $filtro_envios  = "AND m.usuario_envia_id = $usuario_id";
}

$response = ['arribos' => [], 'envios' => []];

try {
    // 1. CONSULTA DE ARRIBOS (Pendientes de recibir)
    $sqlArribos = "SELECT m.id, m.fecha, p.nombre as producto, p.sku,p.factor_conversion, p.unidad_reporte, m.cantidad, 
                          ao.nombre as origen, u.nombre as enviado_por
                   FROM movimientos m
                   JOIN productos p ON m.producto_id = p.id
                   JOIN almacenes ao ON m.almacen_origen_id = ao.id
                   JOIN usuarios u ON m.usuario_envia_id = u.id
                   WHERE m.tipo = 'traspaso' 
                   AND m.usuario_recibe_id IS NULL 
                   $filtro_arribos 
                   ORDER BY m.fecha DESC";
    
    $resA = $conexion->query($sqlArribos);
    while($row = $resA->fetch_assoc()) {
        $response['arribos'][] = $row;
    }

    // 2. CONSULTA DE ENVÍOS (Realizados por este almacén/usuario)
    $sqlEnvios = "SELECT m.id, m.fecha, p.nombre as producto,p.factor_conversion, p.unidad_reporte, m.cantidad, 
                         ad.nombre as destino, m.usuario_recibe_id
                  FROM movimientos m
                  JOIN productos p ON m.producto_id = p.id
                  JOIN almacenes ad ON m.almacen_destino_id = ad.id
                  WHERE m.tipo = 'traspaso' 
                  $filtro_envios 
                  ORDER BY m.fecha DESC LIMIT 20";
    
    $resE = $conexion->query($sqlEnvios);
    while($row = $resE->fetch_assoc()) {
        // Estado visual: Si tiene usuario_recibe_id es que ya llegó
        $row['estado'] = ($row['usuario_recibe_id']) ? 'Completado' : 'En Tránsito';
        $response['envios'][] = $row;
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}