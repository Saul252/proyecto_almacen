<?php

class consultaClienteHistorialModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }
    public function obtenerExpedienteCompletoPorToken($api_token, $fecha_inicio = null, $fecha_fin = null)
    {
        $sql = "SELECT 
            hc.id,
            hc.paciente_id,
            hc.usuario_id,
            hc.fecha_consulta,
            hc.motivo_consulta,
            u.nombre as atendio,
            hc.antecedentes_medicos,
            hc.sintomas,
            hc.diagnostico,
            hc.procedimiento_realizado,
            hc.avances_notas,
            hc.plan_tratamiento,
            hc.observaciones,
            hc.presion_arterial,
            hc.temperatura,
            hc.estatura,
            hc.peso,
            hc.costo,
            hc.estado,
            hc.created_at,
            hc.updated_at,
            c.nombre_comercial AS nombre_cliente,
            c.telefono AS telefono_cliente,
           (SELECT GROUP_CONCAT(
            CONCAT(
                IFNULL(nombre, ''),
                '|||',
                IFNULL(direccion, ''),
                '|||',
                IFNULL(id, '')
            )
            SEPARATOR ';;;'
        )
        FROM documentos doc
        WHERE doc.paciente_id = c.id AND doc.consulta_id = hc.id
    ) AS documentos_url
            FROM consulta_medica hc
            INNER JOIN clientes c ON hc.paciente_id = c.id
            LEFT JOIN usuarios u ON u.id = hc.usuario_id
            WHERE c.api_token = ? ";

        // El primer parámetro ahora es el api_token (string)
        $params = [$api_token];
        $types = "s";

        if (!empty($fecha_inicio) && trim($fecha_inicio) !== "''") {
            $f_inicio_clean = substr(trim($fecha_inicio), 0, 10);
            $sql .= " AND hc.fecha_consulta >= ?";
            $params[] = $f_inicio_clean . ' 00:00:00';
            $types .= "s";
        }

        if (!empty($fecha_fin) && trim($fecha_fin) !== "''") {
            $f_fin_clean = substr(trim($fecha_fin), 0, 10);
            $sql .= " AND hc.fecha_consulta <= ?";
            $params[] = $f_fin_clean . ' 23:59:59';
            $types .= "s";
        }

        $sql .= " ORDER BY hc.fecha_consulta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function obtenerDatosBasicosPorToken(string $api_token)
    {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE api_token = ? AND activo = 1 LIMIT 1");
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $api_token);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
    /**
     * Obtiene la información completa de un cliente validando
     * nombre_comercial, fecha_nacimiento y telefono con ultra-seguridad.
     *
     * @param string $nombre_comercial
     * @param string $fecha_nacimiento (Formato: YYYY-MM-DD)
     * @param string $telefono
     * @return array|null Retorna los datos del cliente o null si no coincide/falla validación.
     */
    public function obtenerPorDatos($nombre_comercial, $fecha_nacimiento, $telefono)
    {
        // =========================================================================
        // 1. SANITIZACIÓN Y NORMALIZACIÓN
        // =========================================================================

        $nombre = trim((string) $nombre_comercial);
        $fechaInput = trim((string) $fecha_nacimiento);
        $telOriginal = trim((string) $telefono);

        // Extraer solo dígitos numéricos del teléfono
        $telSoloNumeros = preg_replace('/[^0-9]/', '', $telOriginal);

        // 1.1 Validar longitud del nombre
        if (mb_strlen($nombre, 'UTF-8') < 2 || mb_strlen($nombre, 'UTF-8') > 150) {
            return null;
        }

        // 1.2 Normalizar fecha: Acepta tanto 'YYYY-MM-DD' como 'DD/MM/YYYY' o 'DD-MM-YYYY'
        $fechaValida = null;
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $formato) {
            $d = DateTime::createFromFormat($formato, $fechaInput);
            if ($d && $d->format($formato) === $fechaInput) {
                $fechaValida = $d->format('Y-m-d');
                break;
            }
        }

        // Si la fecha no es válida en ningún formato
        if (!$fechaValida) {
            return null;
        }

        // 1.3 Validar longitud del teléfono (7 a 15 dígitos)
        if (strlen($telSoloNumeros) < 7 || strlen($telSoloNumeros) > 15) {
            return null;
        }

        // =========================================================================
        // 2. CONSULTA SQL (Usando DATE() para ignorar horas/minutos/segundos)
        // =========================================================================

        $sql = "SELECT 
            `id`, 
            `nombre_comercial`, 
            `fecha_nacimiento`, 
            `sexo`, 
            `razon_social`, 
            `contacto`, 
            `rfc`, 
            `regimen_fiscal`, 
            `codigo_postal`, 
            `correo`, 
            `telefono`, 
            `direccion`, 
            `uso_cfdi`, 
            `activo`, 
            `fecha_registro`, 
            `almacen_id`, 
            `api_token` 
        FROM `clientes` 
        WHERE LOWER(TRIM(`nombre_comercial`)) = LOWER(?) 
          AND DATE(`fecha_nacimiento`) = ? 
          AND (
              TRIM(`telefono`) = ? 
              OR TRIM(`telefono`) = ?
              OR REPLACE(REPLACE(REPLACE(REPLACE(`telefono`, '-', ''), ' ', ''), '(', ''), ')', '') = ?
          )
        LIMIT 1";

        // =========================================================================
        // 3. EJECUCIÓN SEGURA
        // =========================================================================
        try {
            // Aseguramos compatibilidad si la propiedad de conexión se llama $this->db o $this->conexion
            $db = $this->db ?? $this->conexion ?? null;

            if (!$db) {
                error_log("Error: No se encontró la conexión a la base de datos en ClientesModel.");
                return null;
            }

            $stmt = $db->prepare($sql);
            if (!$stmt) {
                error_log("Error al preparar la consulta: " . $db->error);
                return null;
            }

            // Se pasa la fecha normalizada Y-m-d (ej: 2026-09-18)
            $stmt->bind_param("sssss", $nombre, $fechaValida, $telOriginal, $telSoloNumeros, $telSoloNumeros);
            $stmt->execute();

            $resultado = $stmt->get_result();
            $cliente = $resultado->fetch_assoc();

            $stmt->close();

            return $cliente ? $cliente : null;

        } catch (Throwable $e) {
            error_log("Excepción en obtenerPorDatos: " . $e->getMessage());
            return null;
        }
    }
    public function obtenerHistorialPorIdYToken(int $id, string $api_token)
    {
        $sql = "SELECT 
                    h.*, 
                    c.id AS cliente_id,
                    c.razon_social,
                    c.nombre_comercial AS cliente_nombre,
                    c.rfc,
                    c.telefono,
                    u.nombre as medico
                FROM consulta_medica h
                INNER JOIN clientes c ON h.paciente_id = c.id
                LEFT JOIN usuarios u ON u.id = h.usuario_id
                WHERE h.id = ? AND c.api_token = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }

        // Parámetros: "i" para entero ($id) y "s" para cadena ($api_token)
        $stmt->bind_param("is", $id, $api_token);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $row['documentos'] = !empty($row['documentos_json'])
                ? json_decode($row['documentos_json'], true)
                : [];

            unset($row['documentos_json']);
            return $row;
        }

        return null;
    }

    /**
     * Sube un documento asociado a una consulta o expediente.
     */
}