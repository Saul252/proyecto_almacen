<?php
require_once __DIR__ . '/../config/conexion.php';

function puedeVerModulo(string $modulo): bool
{
    // Usamos el rol_id que ya tienes en tu tabla usuarios
    if (!isset($_SESSION['rol_id'])) {
        return false;
    }

    $rol_id = $_SESSION['rol_id'];
    global $conexion;

    // Cache para no saturar la base de datos en cada carga de página
    if (!isset($_SESSION['permisos_cache'])) {
        $sql = "SELECT modulo FROM permisos_roles WHERE rol_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $rol_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $permitidos = [];
        while($row = $res->fetch_assoc()) {
            $permitidos[] = $row['modulo'];
        }
        $_SESSION['permisos_cache'] = $permitidos;
    }

    return in_array($modulo, $_SESSION['permisos_cache']);
}