<?php
class AuthModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los datos del usuario por username uniendo con la tabla roles
     */
    public function obtenerPorUsername(string $username): ?array
    {
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
    public function obtenerHoraCierreAlmacen(int $almacenId): string
    {
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
    public function buscarTrabajadorPorNombre(string $nombreLimpio): ?array
    {
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
    /**
     * Registra una entrada exitosa al sistema en la tabla de auditoría.
     *
     * @param int    $usuarioId  ID del usuario que inicia sesión
     * @param string $username   Nombre de usuario
     * @param int    $rolId      ID del rol
     * @param string $rol        Nombre del rol
     * @param int    $almacenId  ID del almacén activo
     * @param string $ip         IP real del cliente
     * @param string $userAgent  User-Agent del navegador
     * @return bool              true si el INSERT fue exitoso
     */

    public function registrarEntrada(
        ?int $usuarioId,
        string $usernameIntentado,
        string $ip,
        string $userAgent,
        bool $exito,
        string $motivo
    ): bool {

        $sql = "INSERT INTO audit_login (
                usuario_id,
                username_intentado,
                exito,
                ip,
                user_agent,
                fecha_hora,
                motivo
            ) VALUES (?, ?, ?, ?, ?, NOW(), ?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar auditoría de login: " .
                $this->conexion->error
            );
        }

        $exitoInt = $exito ? 1 : 0;

        $stmt->bind_param(
            "isisss",
            $usuarioId,
            $usernameIntentado,
            $exitoInt,
            $ip,
            $userAgent,
            $motivo
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al registrar auditoría de login: " . $error
            );
        }

        $stmt->close();

        return true;
    }

}