<?php
ob_start();
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

$id = $_GET['id'] ?? null;
$alm_id = $_GET['almacen_id'] ?? null;

$response = ['status' => 'error', 'message' => 'Faltan parámetros'];

if ($id && $alm_id) {
    try {
        $sql = "SELECT 
                    p.id, p.sku, p.nombre, p.descripcion, p.categoria_id, 
                    p.unidad_medida, p.unidad_reporte, p.factor_conversion,
                    p.fiscal_clave_prod, p.fiscal_clave_unidad, p.impuesto_iva,
                    i.stock, i.stock_minimo, 
                    a.nombre as almacen_nombre,
                    pp.precio_minorista, pp.precio_mayorista, pp.precio_distribuidor
                FROM productos p
                INNER JOIN inventario i ON p.id = i.producto_id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN precios_producto pp ON p.id = pp.producto_id AND pp.almacen_id = a.id
                WHERE p.id = ? AND i.almacen_id = ?
                LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $id, $alm_id);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();

        if ($producto) {
            $response = ['status' => 'success', 'producto' => $producto];
        } else {
            $response['message'] = 'Producto no encontrado en este almacén';
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}
ob_end_clean();
echo json_encode($response);