<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

// Iniciamos una transacción para que si algo falla, no se rompa la base de datos
$conexion->begin_transaction();

try {
    // 1. Recoger datos del POST (Aseguramos que coincidan con los 'name' del modal)
    $id             = $_POST['producto_id'] ?? null;
    $almacen_id     = $_POST['almacen_actual_id'] ?? null;
    $sku            = $_POST['sku'] ?? '';
    $nombre         = $_POST['nombre'] ?? '';
    $descripcion    = $_POST['descripcion'] ?? ''; // <-- REVISA QUE SEA 'descripcion'
    $categoria_id   = $_POST['categoria_id'] ?? null;
    
    // Fiscal
    $f_prod         = $_POST['fiscal_clave_prod'] ?? '';
    $f_unit         = $_POST['fiscal_clave_unidad'] ?? '';
    $iva            = $_POST['impuesto_iva'] ?? 0;

    // Logística
    $u_reporte      = $_POST['unidad_reporte'] ?? '';
    $factor         = $_POST['factor_conversion'] ?? 1;
    $u_medida       = $_POST['unidad_medida'] ?? '';

    // Precios e Inventario
    $p_min          = $_POST['precio_minorista'] ?? 0;
    $p_may          = $_POST['precio_mayorista'] ?? 0;
    $p_dist         = $_POST['precio_distribuidor'] ?? 0;
    $stock          = $_POST['stock'] ?? 0;
    $s_min          = $_POST['stock_minimo'] ?? 0;
    
    $aplicar_global = isset($_POST['aplicar_global']);

    if (!$id) throw new Exception("ID de producto no recibido.");

    // 2. ACTUALIZAR TABLA 'productos' (Datos Generales)
    $sql_prod = "UPDATE productos SET 
                    sku = ?, 
                    nombre = ?, 
                    descripcion = ?, 
                    categoria_id = ?, 
                    unidad_medida = ?, 
                    unidad_reporte = ?, 
                    factor_conversion = ?, 
                    fiscal_clave_prod = ?, 
                    fiscal_clave_unidad = ?, 
                    impuesto_iva = ? 
                WHERE id = ?";
    
    $stmt1 = $conexion->prepare($sql_prod);
    $stmt1->bind_param("sssisssdssi", 
        $sku, $nombre, $descripcion, $categoria_id, 
        $u_medida, $u_reporte, $factor, 
        $f_prod, $f_unit, $iva, $id
    );
    $stmt1->execute();

    // 3. ACTUALIZAR TABLA 'precios_producto'
    if ($aplicar_global) {
        // Si el switch está activo, actualiza el precio en todos los almacenes para ese producto
        $sql_precios = "UPDATE precios_producto SET 
                            precio_minorista = ?, 
                            precio_mayorista = ?, 
                            precio_distribuidor = ? 
                        WHERE producto_id = ?";
        $stmt2 = $conexion->prepare($sql_precios);
        $stmt2->bind_param("dddi", $p_min, $p_may, $p_dist, $id);
    } else {
        // Solo en el almacén actual
        $sql_precios = "UPDATE precios_producto SET 
                            precio_minorista = ?, 
                            precio_mayorista = ?, 
                            precio_distribuidor = ? 
                        WHERE producto_id = ? AND almacen_id = ?";
        $stmt2 = $conexion->prepare($sql_precios);
        $stmt2->bind_param("dddii", $p_min, $p_may, $p_dist, $id, $almacen_id);
    }
    $stmt2->execute();

    // 4. ACTUALIZAR TABLA 'inventario' (Solo local)
    $sql_inv = "UPDATE inventario SET stock = ?, stock_minimo = ? 
                WHERE producto_id = ? AND almacen_id = ?";
    $stmt3 = $conexion->prepare($sql_inv);
    $stmt3->bind_param("ddii", $stock, $s_min, $id, $almacen_id);
    $stmt3->execute();

    // Si todo salió bien, guardamos cambios
    $conexion->commit();
    echo json_encode(['status' => 'success', 'message' => 'Producto y descripción actualizados correctamente']);

} catch (Exception $e) {
    // Si algo falló, deshacemos todo para no dejar datos corruptos
    $conexion->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}