<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/myvet';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';
session_start();

// Validar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // Limpieza de datos básicos
        $sku = mysqli_real_escape_string($conexion, $_POST['sku']);
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $categoria_id = intval($_POST['categoria_id']);
        $unidad_medida = mysqli_real_escape_string($conexion, $_POST['unidad_medida']);
        $precio_adquisicion = floatval($_POST['precio_adquisicion']);

        // 1. Verificar si el SKU ya existe para evitar errores
        $checkSku = $conexion->query("SELECT id FROM productos WHERE sku = '$sku'");
        if ($checkSku->num_rows > 0) {
            throw new Exception("El SKU '$sku' ya está registrado.");
        }

        // 2. Insertar el producto (Sin stock, ya que el stock se agregará al guardar la compra)
        $sql = "INSERT INTO productos (
                    sku, 
                    nombre, 
                    categoria_id, 
                    unidad_medida, 
                    precio_adquisicion, 
                    activo, 
                    fecha_creacion
                ) VALUES (?, ?, ?, ?, ?, 1, NOW())";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssisd", $sku, $nombre, $categoria_id, $unidad_medida, $precio_adquisicion);

        if ($stmt->execute()) {
            $nuevo_id = $conexion->insert_id;
            
            // Devolvemos éxito y los datos necesarios para que JS lo seleccione en el modal de compra
            echo json_encode([
                "status" => "success",
                "id" => $nuevo_id,
                "nombre" => $nombre . " (" . $sku . ")",
                "precio" => $precio_adquisicion,
                "message" => "Producto registrado correctamente"
            ]);
        } else {
            throw new Exception("Error al insertar en la base de datos.");
        }

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Acceso no autorizado"]);
}