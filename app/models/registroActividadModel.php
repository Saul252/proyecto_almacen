<?php
class registroActividadModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los datos del usuario por username uniendo con la tabla roles
     */

    public function registrarMovimientoMedico(
        ?int $usuarioId,
        string $usernameIntentado,
        string $ip,
        string $userAgent,
        bool $exito,
        string $motivo,
        string $tipo
    ): bool {

        $sql = "INSERT INTO audit_mov_medic (
            usuario_id,
            username_intentado,
            exito,
            ip,
            user_agent,
            fecha_hora,
            motivo,
            tipo
        ) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar auditoría: " . $this->conexion->error
            );
        }

        $exitoInt = $exito ? 1 : 0;

        $stmt->bind_param(
            "isissss",
            $usuarioId,
            $usernameIntentado,
            $exitoInt,
            $ip,
            $userAgent,
            $motivo,
            $tipo
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al registrar auditoría: " . $error
            );
        }

        $stmt->close();

        return true;
    }
}
