<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/myvet';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';

echo '<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: sans-serif; }</style>
</head>
<body>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Captura de datos básicos
    $sku = $_POST['sku'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['description'] ?? ''; 
    $categoria_id = !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null;
    $unidad_medida = $_POST['unidad_medida'] ?? '';
    
    // 2. Captura de datos de conversión
    $unidad_reporte = $_POST['unidad_reporte'] ?? null;
    $factor_conversion = !empty($_POST['factor_conversion']) ? floatval($_POST['factor_conversion']) : 1.00;

    // 3. Datos fiscales y otros
    $precio_adquisicion = $_POST['precio_adquisicion'] ?? 0;
    $fiscal_clave_prod = $_POST['fiscal_clave_prod'] ?? null;
    $fiscal_clave_unit = $_POST['fiscal_clave_unit'] ?? null;
    $impuesto_iva = $_POST['impuesto_iva'] ?? 16.00;

    $conexion->begin_transaction();

    try {
        // Validar SKU único
        $checkSku = $conexion->prepare("SELECT id FROM productos WHERE sku = ?");
        $checkSku->bind_param("s", $sku);
        $checkSku->execute();
        if ($checkSku->get_result()->num_rows > 0) {
            throw new Exception("El SKU '$sku' ya está registrado.");
        }

        // Insertar Producto con TODAS tus columnas originales
        $stmtProd = $conexion->prepare("INSERT INTO productos 
            (sku, nombre, descripcion, unidad_medida, unidad_reporte, factor_conversion, fiscal_clave_prod, fiscal_clave_unidad, precio_adquisicion, impuesto_iva, categoria_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmtProd->bind_param("sssssdsssdd", 
            $sku, $nombre, $descripcion, $unidad_medida, 
            $unidad_reporte, $factor_conversion, $fiscal_clave_prod, 
            $fiscal_clave_unit, $precio_adquisicion, $impuesto_iva, $categoria_id
        );
        $stmtProd->execute();
        $producto_id = $conexion->insert_id;

        // Procesar Almacenes
        if (isset($_POST['almacenes']) && is_array($_POST['almacenes'])) {
            foreach ($_POST['almacenes'] as $almacen_id => $datos) {
                
                $stock = !empty($datos['stock']) ? floatval($datos['stock']) : 0;

                // AJUSTE SOLICITADO: Solo procesar si el stock es mayor a cero
                if ($stock > 0) {
                    $min = !empty($datos['stock_minimo']) ? floatval($datos['stock_minimo']) : 0;
                    $p_minorista = !empty($datos['precio_minorista']) ? floatval($datos['precio_minorista']) : 0;
                    $p_mayorista = !empty($datos['precio_mayorista']) ? floatval($datos['precio_mayorista']) : 0;
                    $p_distribuidor = !empty($datos['precio_distribuidor']) ? floatval($datos['precio_distribuidor']) : 0;

                    // Insertar en Inventario
                    $stmtInv = $conexion->prepare("INSERT INTO inventario (almacen_id, producto_id, stock, stock_minimo) VALUES (?, ?, ?, ?)");
                    $stmtInv->bind_param("iidd", $almacen_id, $producto_id, $stock, $min);
                    $stmtInv->execute();

                    // --- INICIO CÓDIGO AGREGADO: CREACIÓN DE LOTE ---
                    $precioIndividal=$precio_adquisicion/$stock;
                    $codigo_lote = "L-" . $sku . "-" . date('His');
                    $stmtLote = $conexion->prepare("INSERT INTO lotes_stock (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) VALUES (?, ?, ?, ?, ?, ?, 'activo')");
                    $stmtLote->bind_param("iisddd", $producto_id, $almacen_id, $codigo_lote, $stock, $stock, $precioIndividal);
                    $stmtLote->execute();
                    // --- FIN CÓDIGO AGREGADO ---

                    // Insertar Precios
                    $stmtPre = $conexion->prepare("INSERT INTO precios_producto (producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor) VALUES (?, ?, ?, ?, ?)");
                    $stmtPre->bind_param("iiddd", $producto_id, $almacen_id, $p_minorista, $p_mayorista, $p_distribuidor);
                    $stmtPre->execute();

                    // Registrar Movimiento de entrada
                    $usuario_id = $_SESSION['usuario_id'] ?? 1;
                    $obs = "Carga inicial mediante nuevo producto (Lote: $codigo_lote)";
                    $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, observaciones) VALUES (?, 'entrada', ?, ?, ?, ?)");
                    $stmtMov->bind_param("idiis", $producto_id, $stock, $almacen_id, $usuario_id, $obs);
                    $stmtMov->execute();
                }
            }
        }

        $conexion->commit();
        echo "<script>
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Producto guardado correctamente en los almacenes seleccionados.', confirmButtonColor: '#198754' })
            .then(() => { window.location.href = '/myvet/app/controllers/almacenes.php'; }); 
        </script>";

    } catch (Exception $e) {
        $conexion->rollback();
        $msg = addslashes($e->getMessage());
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Error', text: '$msg', confirmButtonColor: '#d33' })
            .then(() => { window.history.back(); });
        </script>";
    }
}
echo '</body></html>';