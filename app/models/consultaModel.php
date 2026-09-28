<?php

class ConsultaModel {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Registra una nueva consulta médica en la base de datos
     */
    public function guardar(array $data): int {
        $this->conexion->begin_transaction();

        try {
            // Sanitización y preparación de datos
            $mascota_id  = intval($data['paciente'] ?? 0);
            $usuario_id  = intval($data['usuario_id'] ?? 1);
            $dueno       = trim(strval($data['dueno'] ?? ''));
            $especie     = trim(strval($data['especie'] ?? ''));
            $raza        = trim(strval($data['raza'] ?? ''));
            $tamano      = trim(strval($data['tamano'] ?? ''));
            $edad        = trim(strval($data['edad'] ?? ''));
            
            $sintomas    = trim(strval($data['sintomas'] ?? ''));
            $diagnostico = trim(strval($data['explicacion'] ?? ''));
            $tratamiento = trim(strval($data['tratamiento'] ?? ''));

            if ($mascota_id <= 0) {
                throw new Exception("Debe seleccionar un paciente válido.");
            }

            // Extraer peso numérico de 'tamano'
            preg_match('/[0-9]+(\.[0-9]+)?/', $tamano, $coincidencias);
            $peso_kg = isset($coincidencias[0]) ? floatval($coincidencias[0]) : 0.00;

            $motivo_consulta = !empty($sintomas) ? mb_strimwidth($sintomas, 0, 255, '...') : 'Consulta General';
            $observaciones   = "Propietario: $dueno | Especie: $especie | Raza: $raza | Edad: $edad | Tamaño/Peso: $tamano";

            // Insertar en la tabla historial
            $sql = "INSERT INTO historial 
                    (mascota_id, usuario_id, fecha_consulta, motivo_consulta, sintomas, diagnostico, tratamiento, peso_kg, observaciones, costo, estado) 
                    VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, 0.00, 'completada')";

            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL: " . $this->conexion->error);
            }

            $stmt->bind_param("iisssds", 
                $mascota_id, 
                $usuario_id, 
                $motivo_consulta, 
                $sintomas, 
                $diagnostico, 
                $tratamiento, 
                $peso_kg, 
                $observaciones
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
    public function guardarEvidencia(int $consultaId, string $rutaArchivo, string $nombreOriginal): bool {
        $sql = "INSERT INTO evidencias_consulta (consulta_id, archivo_ruta, nombre_original) VALUES (?, ?, ?)";
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
     * Obtiene los datos de una consulta específica por ID
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT h.*, m.nombre AS nombre_mascota, m.especie, m.raza 
                FROM historial h
                INNER JOIN mascotas m ON h.mascota_id = m.id
                WHERE h.id = ? LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $consulta = $resultado->fetch_assoc();
        $stmt->close();

        return $consulta ?: null;
    }

    /**
     * Lista el historial de consultas de un consultorio/almacén
     */
    public function listarPorConsultorio(int $consultorioId): array {
        $sql = "SELECT h.id, h.fecha_consulta, h.motivo_consulta, h.diagnostico, m.nombre AS nombre_mascota
                FROM historial h
                INNER JOIN mascotas m ON h.mascota_id = m.id
                WHERE m.almacen_id = ?
                ORDER BY h.fecha_consulta DESC";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) return [];

        $stmt->bind_param("i", $consultorioId);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $consultas = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $consultas;
    }
}