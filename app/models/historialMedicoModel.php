<?php

class PacientesHistorialMedicoModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Sube un documento asociado a una consulta o expediente.
     */
    public function subirDocumentoConsulta($paciente_id, $nombre, $documento_url, $tipo = 'medico', $consulta_id = null)
    {
        $sql = "INSERT INTO documentos (paciente_id, nombre, direccion, tipo, consulta_id)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }

        $stmt->bind_param(
            "isssi",
            $paciente_id,
            $nombre,
            $documento_url,
            $tipo,
            $consulta_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar documento: " . $stmt->error);
        }

        $documento_id = $stmt->insert_id;
        $stmt->close();

        return [
            'success' => true,
            'documento_id' => $documento_id,
            'message' => 'Documento guardado correctamente'
        ];
    }

    public function eliminarDocumento($id_documento)
    {
        $sql = "UPDATE documentos_vehiculos
                SET activo = 0
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            return false;

        $stmt->bind_param("i", $id_documento);

        return $stmt->execute();
    }

    public function listarTodos($almacen_id = 0, $cliente_id = 0)
    {
        $sql = "SELECT 
                    c.id,
                    c.razon_social,
                    c.nombre_comercial AS cliente_nombre,
                    c.rfc,
                    c.telefono,
                    c.almacen_id,
                    c.activo
                FROM clientes c
                WHERE c.activo = 1";

        $params = [];
        $types = "";

        if ($almacen_id > 0) {
            $sql .= " AND c.almacen_id = ?";
            $params[] = (int) $almacen_id;
            $types .= "i";
        }

        if ($cliente_id > 0) {
            $sql .= " AND c.id = ?";
            $params[] = (int) $cliente_id;
            $types .= "i";
        }

        $sql .= " ORDER BY c.nombre_comercial ASC, c.razon_social ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene los datos completos de un cliente por su ID.
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT c.*, c.nombre_comercial AS cliente_nombre 
                FROM clientes c 
                WHERE c.id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Obtiene el listado general de todos los clientes registrados sin restricciones de activos.
     */
    public function obtenerTodos()
    {
        $sql = "SELECT c.*, c.nombre_comercial AS cliente_nombre 
                FROM clientes c 
                ORDER BY c.nombre_comercial ASC";

        $res = $this->db->query($sql);
        if (!$res) {
            throw new Exception("Error en la consulta: " . $this->db->error);
        }
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerExpedienteCompletoFecha($id_paciente, $fecha_inicio = null, $fecha_fin = null)
    {
        $sql = "SELECT 
                hc.id,
                hc.paciente_id,
                hc.usuario_id,
                hc.fecha_consulta,
                hc.motivo_consulta,
                hc.usuario_id,
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
        WHERE doc.paciente_id = c.id AND doc.consulta_id = hc.id and doc.tipo='medico'
    ) AS documentos_url
            FROM consulta_medica hc
            INNER JOIN clientes c ON hc.paciente_id = c.id
            left join usuarios u ON u.id = hc.usuario_id
            WHERE hc.paciente_id = ? ";

        $params = [$id_paciente];
        $types = "i";

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

    /**
     * Obtiene todo el historial de consultas de un paciente por su ID.
     */
    public function obtenerExpedientePorId($id)
    {
        $sql = "SELECT 
                    h.*, 
                    c.razon_social,
                    c.nombre_comercial AS cliente_nombre,
                    (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', ed.id,
                                'nombre', ed.nombre,
                                'direccion', ed.direccion
                            )
                        )
                        FROM expedientes_documentos ed
                        WHERE ed.historial_id = h.id
                    ) AS documentos_json
                FROM consulta_medica h
                INNER JOIN clientes c ON h.paciente_id = c.id
                WHERE c.id = ?
                ORDER BY h.fecha_consulta DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar expediente: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($resultado as &$row) {
            $row['documentos'] = !empty($row['documentos_json'])
                ? json_decode($row['documentos_json'], true)
                : [];
            unset($row['documentos_json']);
        }

        return $resultado;
    }

    /**
     * Obtiene el detalle de un registro de consulta médica junto con sus documentos adjuntos.
     */
    public function obtenerHistorialPorId(int $id)
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
                   left join usuarios u ON u.id = h.usuario_id
                WHERE h.id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
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
     * Inserta un nuevo cliente en la base de datos.
     */
    public function guardar($datos)
    {
        $razon_social = trim($datos['razon_social'] ?? '');
        $nombre_comercial = trim($datos['nombre_comercial'] ?? '');
        $rfc = !empty($datos['rfc']) ? trim($datos['rfc']) : 'XAXX010101000';
        $email = !empty($datos['email']) ? trim($datos['email']) : null;
        $telefono = !empty($datos['telefono']) ? trim($datos['telefono']) : null;
        $almacen_id = intval($datos['almacen_id'] ?? 1);
        $activo = 1;

        if (empty($razon_social) && empty($nombre_comercial)) {
            throw new Exception("Debe especificar al menos la Razón Social o el Nombre Comercial del cliente.");
        }

        $sql = "INSERT INTO clientes (
                    razon_social, nombre_comercial, rfc, email, telefono, almacen_id, activo, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                )";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar el registro del cliente: " . $this->db->error);
        }

        $stmt->bind_param(
            "sssssii",
            $razon_social,
            $nombre_comercial,
            $rfc,
            $email,
            $telefono,
            $almacen_id,
            $activo
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar el cliente: " . $stmt->error);
        }

        return [
            'success' => true,
            'id' => $this->db->insert_id,
            'message' => 'Cliente guardado correctamente'
        ];
    }

    /**
     * Actualiza los datos de un cliente.
     */
    public function actualizar($id, $datos)
    {
        $razon_social = trim($datos['razon_social'] ?? '');
        $nombre_comercial = trim($datos['nombre_comercial'] ?? '');
        $rfc = !empty($datos['rfc']) ? trim($datos['rfc']) : null;
        $email = !empty($datos['email']) ? trim($datos['email']) : null;
        $telefono = !empty($datos['telefono']) ? trim($datos['telefono']) : null;

        $campos = [
            "razon_social = ?",
            "nombre_comercial = ?",
            "rfc = ?",
            "email = ?",
            "telefono = ?",
            "updated_at = NOW()"
        ];

        $params = [
            $razon_social,
            $nombre_comercial,
            $rfc,
            $email,
            $telefono
        ];

        $tipos = "sssss";

        $sql = "UPDATE clientes SET " . implode(", ", $campos) . " WHERE id = ?";

        $params[] = (int) $id;
        $tipos .= "i";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar actualización: " . $this->db->error);
        }

        $stmt->bind_param($tipos, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar el cliente: " . $stmt->error);
        }

        return [
            'success' => true,
            'message' => 'Cliente actualizado correctamente'
        ];
    }

    /**
     * Da de baja o reactiva a un cliente (Soft Delete).
     */
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE clientes SET activo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar cambio de estado: " . $this->db->error);
        }

        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    /**
     * Métricas generales de clientes para el Dashboard.
     */
    public function getResumenPacientes($almacen_id = 0)
    {
        if ($almacen_id > 0) {
            $sql = "SELECT COUNT(*) as total FROM clientes WHERE activo = 1 AND almacen_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $almacen_id);
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        } else {
            $sql = "SELECT COUNT(*) as total FROM clientes WHERE activo = 1";
            $query = $this->db->query($sql);
            $total = ($query) ? (int) $query->fetch_assoc()['total'] : 0;
        }

        return [
            "total_pacientes" => $total
        ];
    }





}