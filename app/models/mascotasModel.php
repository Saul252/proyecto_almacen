<?php
class MascotasModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Lista todas las mascotas activas.
     * Si se pasa un $cliente_id > 0, filtra solo las mascotas de ese cliente.
     */
    public function guardarConsulta($datos)
    {
        $sql = "INSERT INTO historial (
                    mascota_id,
                    usuario_id,
                    fecha_consulta,
                    motivo_consulta,
                    sintomas,
                    diagnostico,
                    tratamiento,
                    peso_kg,
                    temperatura_c,
                    frecuencia_cardiaca,
                    frecuencia_respiratoria,
                    observaciones,
                    costo,
                    estado,
                    created_at,
                    updated_at
                ) VALUES (
                    ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                )";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar el registro: " . $this->db->error
            );
        }

        $stmt->bind_param(
            "iissssddiisdi",
            $datos['mascota_id'],
            $datos['usuario_id'],
            $datos['motivo_consulta'],
            $datos['sintomas'],
            $datos['diagnostico'],
            $datos['tratamiento'],
            $datos['peso_kg'],
            $datos['temperatura_c'],
            $datos['frecuencia_cardiaca'],
            $datos['frecuencia_respiratoria'],
            $datos['observaciones'],
            $datos['costo'],
            $datos['estado']
        );

        if (!$stmt->execute()) {
            throw new Exception(
                "Error al guardar la consulta: " . $stmt->error
            );
        }

        return $this->db->insert_id;
    }
    public function listarTodos($consultorio=0 , $cliente_id=0 )
{
    $sql = "SELECT 
                m.*,
                c.nombre_comercial AS propietario_nombre
            FROM mascotas m
            INNER JOIN clientes c ON m.cliente_id = c.id
            WHERE m.activo = 1";

    $params = [];
    $types  = "";

    // 1. Filtro dinámico por almacén / consultorio
    if ($consultorio > 0) {
        $sql .= " AND c.almacen_id = ?";
        $params[] = (int)$consultorio;
        $types   .= "i";
    }

    // 2. Filtro dinámico por cliente
    if ($cliente_id > 0) {
        $sql .= " AND m.cliente_id = ?";
        $params[] = (int)$cliente_id;
        $types   .= "i";
    }

    $sql .= " ORDER BY m.nombre ASC";

    // 3. Ejecución dinámica con Prepared Statements
    if (!empty($params)) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }

        // El operador ... (splat) pasa los elementos del array como argumentos individuales
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar consulta: " . $stmt->error);
        }

        $resultado = $stmt->get_result();
    } else {
        // Si no hay parámetros de filtro, ejecuta la consulta normal
        $resultado = $this->db->query($sql);
        if (!$resultado) {
            throw new Exception("Error en consulta: " . $this->db->error);
        }
    }

    // Devuelve un array asociativo directamente, listo para json_encode
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

    /**
     * Obtiene los datos de una mascota específica por su ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT m.*, c.nombre_comercial AS propietario_nombre 
                FROM mascotas m
                INNER JOIN clientes c ON m.cliente_id = c.id
                WHERE m.id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function obtenerTodos() {
        

        // Filtro por Estado de Pago (Saldo)
        

        $sql = "SELECT m.*, c.nombre_comercial AS propietario_nombre 
                FROM mascotas m
                INNER JOIN clientes c ON m.cliente_id = c.id
               ";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
  

        
    }
  public function obtenerExpedientePorId($id) {
    $sql = "SELECT h.*, c.nombre_comercial AS propietario_nombre,
     (
    SELECT GROUP_CONCAT(
        CONCAT(
            IFNULL(nombre, ''),
            '|||',
            IFNULL(direccion, ''),
            '|||',
            IFNULL(id, '')
        )
        SEPARATOR ';;;'
    )
    FROM expedientes_documentos ed
    WHERE ed.historial_id = h.id
) AS documento_url
            FROM historial h
            JOIN mascotas m ON h.mascota_id = m.id
            INNER JOIN clientes c ON m.cliente_id = c.id
            WHERE m.id = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result();
}
 public function obtenerHistorialPorId(int $id)
{
    $sql = "SELECT h.*, c.nombre_comercial AS propietario_nombre,m.id, m.cliente_id, m.nombre, m.especie, m.raza, m.fecha_nacimiento, m.sexo, m.peso, m.color, m.senas_particulares, m.fotografia, m.activo, m.fecha_registro,

            (
                SELECT GROUP_CONCAT(
                    CONCAT(
                        IFNULL(nombre, ''),
                        '|||',
                        IFNULL(direccion, ''),
                        '|||',
                        IFNULL(id, '')
                    )
                    SEPARATOR ';;;'
                )
                FROM expedientes_documentos ed
                WHERE ed.historial_id = h.id
            ) AS documento_url
            FROM historial h
            JOIN mascotas m ON h.mascota_id = m.id
            INNER JOIN clientes c ON m.cliente_id = c.id
            WHERE h.id = ?"; // Se corrigió m.id por h.id

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        // Descomponer la cadena del GROUP_CONCAT en un arreglo de objetos
        $documentos = [];
        if (!empty($row['documento_url'])) {
            $docsArray = explode(';;;', $row['documento_url']);
            foreach ($docsArray as $doc) {
                $partes = explode('|||', $doc);
                $documentos[] = [
                    'nombre'    => $partes[0] ?? '',
                    'direccion' => $partes[1] ?? '',
                    'id'        => (int)($partes[2] ?? 0)
                ];
            }
        }
        
        $row['documentos'] = $documentos;
        unset($row['documento_url']); // Elimina la cadena cruda

        return $row;
    }

    return null;
}
    /**
     * Inserta una nueva mascota en la base de datos
     */
    public function guardar($datos) {
        // 1. Limpieza y validación de datos
        $cliente_id         = intval($datos['cliente_id']);
        $nombre             = $datos['nombre'] ?? '';
        $especie            = $datos['especie'] ?? '';
        $raza               = !empty($datos['raza']) ? $datos['raza'] : null;
        $fecha_nacimiento   = !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null;
        $sexo               = $datos['sexo'] ?? 'Desconocido';
        $peso               = !empty($datos['peso']) ? floatval($datos['peso']) : null;
        $color              = !empty($datos['color']) ? $datos['color'] : null;
        $senas_particulares = !empty($datos['senas_particulares']) ? $datos['senas_particulares'] : null;
        $fotografia         = !empty($datos['fotografia']) ? $datos['fotografia'] : null;
        $activo             = 1;

        if (empty($cliente_id) || empty($nombre) || empty($especie)) {
            throw new Exception("El cliente, nombre y especie son obligatorios.");
        }

        // 2. Insertar
        $sql = "INSERT INTO mascotas (
                    cliente_id, nombre, especie, raza, fecha_nacimiento, 
                    sexo, peso, color, senas_particulares, fotografia, activo
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar INSERT: " . $this->db->error);
        }

        // issssssdssi = integer, string(5), double, string(2), integer
        $stmt->bind_param(
            "issssssdssi",
            $cliente_id,
            $nombre,
            $especie,
            $raza,
            $fecha_nacimiento,
            $sexo,
            $peso,
            $color,
            $senas_particulares,
            $fotografia,
            $activo
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar la mascota: " . $stmt->error);
        }

        return [
            'success' => true,
            'id'      => $this->db->insert_id,
            'message' => 'Mascota guardada correctamente'
        ];
    }

    /**
     * Actualiza los datos de una mascota existente
     */
    public function actualizar($id, $datos) {
        // Limpieza de datos
        $propietario            = $datos['cliente_id'] ?? 1;
        $nombre             = $datos['nombre'] ?? '';
        $especie            = $datos['especie'] ?? '';
        $raza               = !empty($datos['raza']) ? $datos['raza'] : null;
        $fecha_nacimiento   = !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null;
        $sexo               = $datos['sexo'] ?? 'Desconocido';
        $peso               = !empty($datos['peso']) ? floatval($datos['peso']) : null;
        $color              = !empty($datos['color']) ? $datos['color'] : null;
        $senas_particulares = !empty($datos['senas_particulares']) ? $datos['senas_particulares'] : null;

        // Campos base a actualizar
        $campos = [
            "cliente_id=?",
            "nombre = ?",
            "especie = ?",
            "raza = ?",
            "fecha_nacimiento = ?",
            "sexo = ?",
            "peso = ?",
            "color = ?",
            "senas_particulares = ?"
        ];

        $params = [
            $propietario,
            $nombre,
            $especie,
            $raza,
            $fecha_nacimiento,
            $sexo,
            $peso,
            $color,
            $senas_particulares
        ];

        $tipos = "isssssdss"; // s=string, d=double

        // Si se subió una nueva fotografía, la actualizamos. Si no, conservamos la que ya tiene.
        if (isset($datos['fotografia'])) {
            $campos[] = "fotografia = ?";
            $params[] = $datos['fotografia'];
            $tipos .= "s";
        }

        // Ensamblar la consulta
        $sql = "UPDATE mascotas SET " . implode(", ", $campos) . " WHERE id = ?";
        
        $params[] = $id;
        $tipos .= "i"; // El id del final

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar UPDATE: " . $this->db->error);
        }

        $stmt->bind_param($tipos, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar la mascota: " . $stmt->error);
        }

        return [
            'success' => true,
            'message' => 'Mascota actualizada correctamente'
        ];
    }

    /**
     * Da de baja o reactiva una mascota (Soft Delete)
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE mascotas SET activo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar DELETE lógico: " . $this->db->error);
        }

        $stmt->bind_param("ii", $estado, $id);
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        }
        return false;
    }

    /**
     * Obtiene métricas rápidas de las mascotas registradas (Opcional, para el dashboard)
     */
    public function getResumenMascotas($cliente_id = 0) {
        if ($cliente_id > 0) {
            $sql = "SELECT COUNT(*) as total FROM mascotas WHERE activo = 1 AND cliente_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        } else {
            $sql = "SELECT COUNT(*) as total FROM mascotas WHERE activo = 1";
            $query = $this->db->query($sql);
            $total = ($query) ? intval($query->fetch_assoc()['total']) : 0;
        }

        return [
            "total_mascotas" => $total
        ];
    }
}
?>