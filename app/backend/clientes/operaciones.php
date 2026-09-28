<?php
require_once __DIR__ . '/../../../config/conexion.php';
header('Content-Type: application/json');

$accion = $_REQUEST['accion'] ?? '';

switch($accion) {
    case 'listar':
        // Traemos todos (activos e inactivos) ordenando por estado para que los activos salgan primero
        $res = $conexion->query("SELECT * FROM clientes ORDER BY activo DESC, nombre_comercial ASC");
        $data = [];
        while($row = $res->fetch_assoc()) { 
            $data[] = $row; 
        }
        echo json_encode(["data" => $data]);
        break;

    case 'obtener':
        $id = intval($_GET['id']);
        $res = $conexion->query("SELECT * FROM clientes WHERE id = $id");
        echo json_encode(["status" => "success", "data" => $res->fetch_assoc()]);
        break;

    case 'guardar':
    case 'editar':
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_comercial']);
        $razon  = mysqli_real_escape_string($conexion, $_POST['razon_social']);
        $rfc    = strtoupper(mysqli_real_escape_string($conexion, $_POST['rfc']));
        $cp     = mysqli_real_escape_string($conexion, $_POST['codigo_postal']);
        $reg    = mysqli_real_escape_string($conexion, $_POST['regimen_fiscal']);
        $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
        $tel    = mysqli_real_escape_string($conexion, $_POST['telefono']);
        $dir    = mysqli_real_escape_string($conexion, $_POST['direccion']);
        $uso    = mysqli_real_escape_string($conexion, $_POST['uso_cfdi']);

        if($accion === 'guardar') {
            $sql = "INSERT INTO clientes (nombre_comercial, razon_social, rfc, codigo_postal, regimen_fiscal, correo, telefono, direccion, uso_cfdi, activo) 
                    VALUES ('$nombre', '$razon', '$rfc', '$cp', '$reg', '$correo', '$tel', '$dir', '$uso', 1)";
        } else {
            $id = intval($_POST['id']);
            $sql = "UPDATE clientes SET nombre_comercial='$nombre', razon_social='$razon', rfc='$rfc', 
                    codigo_postal='$cp', regimen_fiscal='$reg', correo='$correo', telefono='$tel', direccion='$dir', uso_cfdi='$uso'
                    WHERE id = $id";
        }

        if($conexion->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Datos guardados correctamente"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conexion->error]);
        }
        break;

    case 'eliminar':
        $id = intval($_POST['id']);
        // LOGICA DE INTERRUPTOR: Si es 1 lo hace 0, si es 0 lo hace 1
        $sql = "UPDATE clientes SET activo = 1 - activo WHERE id = $id";
        if($conexion->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Estado actualizado"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo cambiar el estado"]);
        }
        break;
}