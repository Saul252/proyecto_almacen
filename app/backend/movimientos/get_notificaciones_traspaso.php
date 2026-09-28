<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';
session_start();
header('Content-Type: application/json');

$usuario_id = $_SESSION['usuario_id'] ?? 0;
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$rol_id = $_SESSION['rol_id'] ?? 0;

if (!$usuario_id) { echo json_encode(['cantidad' => 0]); exit; }

$esAdmin = ($rol_id == 1);
$whereAlmacen = $esAdmin ? "" : " AND m.almacen_destino_id = $almacen_sesion";

// Consulta mejorada
$sql = "SELECT m.id, p.nombre as prod, m.cantidad as cant_total, 
               p.factor_conversion, p.unidad_reporte,
               u.nombre as emisor, ao.nombre as origen,
               DATE_FORMAT(m.fecha, '%H:%i') as hora
        FROM movimientos m
        INNER JOIN productos p ON m.producto_id = p.id
        LEFT JOIN almacenes ao ON m.almacen_origen_id = ao.id
        LEFT JOIN usuarios u ON m.usuario_registra_id = u.id
        WHERE m.tipo = 'traspaso' AND m.usuario_recibe_id IS NULL $whereAlmacen
        ORDER BY m.fecha DESC";

$res = $conexion->query($sql);
$items = [];

while($row = $res->fetch_assoc()) {
    $cant = (float)$row['cant_total'];
    $factor = (float)$row['factor_conversion'] > 0 ? (float)$row['factor_conversion'] : 1;
    $unidad = !empty($row['unidad_reporte']) ? $row['unidad_reporte'] : 'MLL';

    // LÓGICA INTELIGENTE DE UNIDADES
    if ($cant < $factor) {
        // Si no llega al factor (ej: 1 tabique), mostramos solo piezas
        $texto_cantidad = number_format($cant, 0) . " PZA";
    } else {
        // Si completa el factor, calculamos enteros y sobrante
        $enteros = floor($cant / $factor);
        $sobrante = $cant % $factor;
        
        $texto_cantidad = $enteros . " " . $unidad;
        if ($sobrante > 0) {
            $texto_cantidad .= " + " . number_format($sobrante, 0) . " PZA";
        }
    }

    $items[] = [
        'id' => $row['id'],
        'producto' => $row['prod'],
        'emisor' => $row['emisor'] ?? 'Sistema',
        'cantidad_texto' => $texto_cantidad,
        'origen' => $row['origen'] ?? 'Almacén',
        'hora' => $row['hora']
    ];
}

echo json_encode([
    'cantidad' => count($items),
    'items' => $items
]);