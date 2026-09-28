<?php
class AuthModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los datos del usuario por username uniendo con la tabla roles
     */
    public function obtenerPorUsername(string $username): ?array {
        $sql = "SELECT u.id, u.nombre, u.username, u.password, u.rol_id, u.almacen_id, u.activo, r.nombre AS rol, almacen.tipo_plan as plan, almacen.logo as logo, almacen.ico as ico,almacen.pago as pago, almacen.nombre as nombre_almacen
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                left join almacenes almacen on almacen.id=u.almacen_id
                WHERE u.username = ? 
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return ($resultado && $resultado->num_rows === 1) ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene la hora de cierre de un almacén específico
     */
    public function obtenerHoraCierreAlmacen(int $almacenId): string {
        $sql = "SELECT hora_cierre_programada FROM almacenes WHERE id = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return "11:58";
        }

        $stmt->bind_param("i", $almacenId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        return $res['hora_cierre_programada'] ?? "11:58";
    }

    /**
     * Busca la coincidencia de un trabajador por su nombre limpio
     */
    public function buscarTrabajadorPorNombre(string $nombreLimpio): ?array {
        $sql = "SELECT id, almacen_id FROM trabajadores WHERE nombre LIKE ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $busqueda = $nombreLimpio . "%";
        $stmt->bind_param("s", $busqueda);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        return $res ?: null;
    }
}