<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (empty($data['nombre_comercial']) || empty($data['rfc'])) {
        throw new Exception('Nombre y RFC son obligatorios.');
    }

    // Validar RFC único antes de insertar
    $check = $conexion->prepare("SELECT id FROM clientes WHERE rfc = ?");
    $check->bind_param("s", $data['rfc']);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        throw new Exception('El RFC ya se encuentra registrado.');
    }

    $sql = "INSERT INTO clientes (
                nombre_comercial, razon_social, rfc, regimen_fiscal, 
                codigo_postal, correo, telefono, direccion, uso_cfdi
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conexion->prepare($sql);
    
    // Todos son strings según tu estructura SQL
    $stmt->bind_param("sssssssss", 
        $data['nombre_comercial'],
        $data['razon_social'],
        $data['rfc'],
        $data['regimen_fiscal'],
        $data['codigo_postal'],
        $data['correo'],
        $data['telefono'],
        $data['direccion'],
        $data['uso_cfdi']
    );

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'id_cliente' => $conexion->insert_id
        ]);
    } else {
        throw new Exception('Error al insertar en la base de datos.');
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}