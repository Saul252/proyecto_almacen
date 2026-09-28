<?php

class ConsultaMedicaModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Registra una nueva consulta médica en la base de datos
     */
    public function guardar(array $data): int
    {
        $this->conexion->begin_transaction();

        try {
            $paciente_id = intval($data['paciente_id'] ?? $data['paciente'] ?? 0);
            $usuario_id = intval($data['usuario_id'] ?? 1);
            $fecha_consulta = trim(strval($data['fecha_consulta'] ?? ''));
            $motivo_consulta = trim(strval($data['motivo_consulta'] ?? ''));
            $antecedentes_medicos = trim(strval($data['antecedentes_medicos'] ?? ''));
            $sintomas = trim(strval($data['sintomas'] ?? ''));
            $diagnostico = trim(strval($data['diagnostico'] ?? ''));
            $procedimiento_realizado = trim(strval($data['procedimiento_realizado'] ?? ''));
            $avances_notas = trim(strval($data['avances_notas'] ?? ''));
            $plan_tratamiento = trim(strval($data['plan_tratamiento'] ?? ''));
            $observaciones = trim(strval($data['observaciones'] ?? ''));
            $presion_arterial = trim(strval($data['presion_arterial'] ?? ''));

            $temperatura = isset($data['temperatura']) && $data['temperatura'] !== ''
                ? floatval($data['temperatura'])
                : null;

            $estatura = isset($data['estatura']) && $data['estatura'] !== ''
                ? floatval($data['estatura'])
                : null;

            $peso = isset($data['peso']) && $data['peso'] !== ''
                ? floatval($data['peso'])
                : null;

            $costo = isset($data['costo']) && $data['costo'] !== ''
                ? floatval($data['costo'])
                : 0.00;

            $estado = isset($data['estado']) && $data['estado'] !== ''
                ? intval($data['estado'])
                : 1;

            if ($paciente_id <= 0) {
                throw new Exception("Debe seleccionar un paciente válido.");
            }

            if ($fecha_consulta === '') {
                $fecha_consulta = date('Y-m-d H:i:s');
            }

            if ($motivo_consulta === '') {
                $motivo_consulta = !empty($sintomas)
                    ? mb_strimwidth($sintomas, 0, 255, '...')
                    : 'Consulta General';
            }

            $sql = "INSERT INTO consulta_medica
                    (
                        paciente_id,
                        usuario_id,
                        fecha_consulta,
                        motivo_consulta,
                        antecedentes_medicos,
                        sintomas,
                        diagnostico,
                        procedimiento_realizado,
                        avances_notas,
                        plan_tratamiento,
                        observaciones,
                        presion_arterial,
                        temperatura,
                        estatura,
                        peso,
                        costo,
                        estado
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL: " . $this->conexion->error);
            }

            // CORREGIDO: Se ajustó la cadena de tipos a 17 caracteres ("iisssssssssssdddi")
            $stmt->bind_param(
                "iisssssssssssdddi",
                $paciente_id,
                $usuario_id,
                $fecha_consulta,
                $motivo_consulta,
                $antecedentes_medicos,
                $sintomas,
                $diagnostico,
                $procedimiento_realizado,
                $avances_notas,
                $plan_tratamiento,
                $observaciones,
                $presion_arterial,
                $temperatura,
                $estatura,
                $peso,
                $costo,
                $estado
            );

            if (!$stmt->execute()) {
                throw new Exception("Error al guardar la consulta: " . $stmt->error);
            }

            $id_consulta = $this->conexion->insert_id;
            $stmt->close();
            $this->conexion->commit();

            return $id_consulta;

        } catch (Throwable $e) {
            $this->conexion->rollback();
            throw $e;
        }
    }

    /**
     * Asocia la ruta de un archivo/evidencia cargado a una consulta
     */
    public function guardarEvidencia(
        int $consultaId,
        string $rutaArchivo,
        string $nombreOriginal
    ): bool {
        $sql = "INSERT INTO evidencias_consulta
                (
                    consulta_id,
                    archivo_ruta,
                    nombre_original
                )
                VALUES (?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iss", $consultaId, $rutaArchivo, $nombreOriginal);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    /**
     * Obtiene una consulta específica por ID
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM consulta_medica WHERE id = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $consulta = $resultado->fetch_assoc();
        $stmt->close();

        return $consulta ?: null;
    }

    /**
     * Lista las consultas de un paciente específico
     */
    public function listarPorPaciente(int $pacienteId): array
    {
        $sql = "SELECT * FROM consulta_medica WHERE paciente_id = ? ORDER BY fecha_consulta DESC";
        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $pacienteId);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $consultas = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $consultas;
    }
}