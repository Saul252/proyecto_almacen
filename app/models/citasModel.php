<?php
/**
 * citasModel.php
 * Modelo para la gestión de citas médicas veterinarias
 */

class CitasModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Listar citas filtradas por almacén/sucursal con JOIN a clientes (pacientes)
     */
    public function listarTodas($almacen_id = 0)
    {
        $sql = "SELECT 
                    c.id, 
                    c.almacen, 
                    c.paciente_id, 
                    c.fecha, 
                    c.detalles, 
                    c.atendera, 
                    c.estado,
                    c.fecha_creacion,
                    cl.nombre_comercial AS paciente_nombre,
                    cl.contacto AS dueno_contacto,
                    cl.telefono AS paciente_telefono
                FROM citas_medicas c
                LEFT JOIN clientes cl ON c.paciente_id = cl.id";

        if ($almacen_id > 0) {
            $sql .= " WHERE c.almacen = " . intval($almacen_id);
        }

        $sql .= " ORDER BY c.fecha ASC";

        $resultado = $this->conexion->query($sql);
        $citas = [];

        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                $citas[] = $row;
            }
        }

        return $citas;
    }
    public function listarCitasFiltros($filtros = [], $usuario_id = 0)
    {
        $where = " WHERE c.id > 0 ";

        // Filtro por Almacén
        if (!empty($filtros['almacen'])) {
            $where .= " AND c.almacen = " . intval($filtros['almacen']);
        }

        // Filtro explícito por usuario que atenderá
        if (!empty($filtros['atendera']) && intval($filtros['atendera']) > 0) {
            $where .= " AND c.atendera = " . intval($filtros['atendera']);
        }

        // Filtro por Tipo (Escapado y entre comillas para evitar errores de sintaxis / SQL Injection)
        if (!empty($filtros['tipo'])) {
            $tipo = $this->conexion->real_escape_string($filtros['tipo']);
            $where .= " AND c.tipo = '$tipo'";
        }

        // Filtro por Fecha específica (Compara solo el día YYYY-MM-DD)
        if (!empty($filtros['fecha'])) {
            $fecha = $this->conexion->real_escape_string($filtros['fecha']);
            $where .= " AND DATE(c.fecha) = '$fecha' ";
        }

        // Buscador general (Paciente o Detalles)
        if (!empty($filtros['search'])) {
            $s = $this->conexion->real_escape_string($filtros['search']);
            $where .= " AND (cl.nombre_comercial LIKE '%$s%' OR c.detalles LIKE '%$s%') ";
        }

        $sql = "SELECT 
                c.id, 
                c.almacen, 
                c.paciente_id, 
                u.nombre as doctor,
                c.fecha, 
                c.tipo,
                c.detalles, 
                c.atendera, 
                c.estado,
                c.fecha_creacion,
                cl.nombre_comercial AS paciente_nombre,
                cl.contacto AS dueno_contacto,
                cl.telefono AS paciente_telefono
            FROM citas_medicas c
            LEFT JOIN usuarios u ON u.id = c.atendera
            LEFT JOIN clientes cl ON c.paciente_id = cl.id
            $where
            ORDER BY c.fecha ASC";

        $resultado = $this->conexion->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function listarCitasFiltrosOriginal($filtros = [], $usuario_id = 0)
    {
        $where = " WHERE c.id > 0 ";

        // Filtro por Almacén
        if (!empty($filtros['almacen'])) {
            $where .= " AND c.almacen = " . intval($filtros['almacen']);
        }

        // Filtro explícito por usuario que atenderá (campo c.atendera)
        if (($filtros['atendera']) > 0) {
            $where .= " AND c.atendera = " . intval($filtros['atendera']);
        }

        // Filtro por Fecha específica (Compara solo el día YYYY-MM-DD sobre un campo DATETIME/TIMESTAMP)
        if (!empty($filtros['fecha'])) {
            $fecha = $this->conexion->real_escape_string($filtros['fecha']);
            $where .= " AND DATE(c.fecha) = '$fecha' ";
        }

        // Buscador general (Paciente o Detalles)
        if (!empty($filtros['search'])) {
            $s = $this->conexion->real_escape_string($filtros['search']);
            $where .= " AND (cl.nombre_comercial LIKE '%$s%' OR c.detalles LIKE '%$s%') ";
        }

        $sql = "SELECT 
                c.id, 
                c.almacen, 
                c.paciente_id, 
                 u.nombre as doctor,
                c.fecha, 
                c.detalles, 
                c.atendera, 
                c.estado,
                c.fecha_creacion,
                cl.nombre_comercial AS paciente_nombre,
                cl.contacto AS dueno_contacto,
                cl.telefono AS paciente_telefono
            FROM citas_medicas c
            join usuarios u on u.id=c.atendera
            LEFT JOIN clientes cl ON c.paciente_id = cl.id
            $where
            ORDER BY c.fecha ASC";

        $resultado = $this->conexion->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function listarCitasFiltrosDental($filtros = [], $usuario_id = 0, $tipo = 'dental')
    {
        $where = " WHERE c.id > 0  AND c.tipo='dental'";

        // Filtro por Almacén
        if (!empty($filtros['almacen'])) {
            $where .= " AND c.almacen = " . intval($filtros['almacen']);
        }

        // Filtro explícito por usuario que atenderá (campo c.atendera)
        if (($filtros['atendera']) > 0) {
            $where .= " AND c.atendera = " . intval($filtros['atendera']);
        }

        // Filtro por Fecha específica (Compara solo el día YYYY-MM-DD sobre un campo DATETIME/TIMESTAMP)
        if (!empty($filtros['fecha'])) {
            $fecha = $this->conexion->real_escape_string($filtros['fecha']);
            $where .= " AND DATE(c.fecha) = '$fecha' ";
        }

        // Buscador general (Paciente o Detalles)
        if (!empty($filtros['search'])) {
            $s = $this->conexion->real_escape_string($filtros['search']);
            $where .= " AND (cl.nombre_comercial LIKE '%$s%' OR c.detalles LIKE '%$s%') ";
        }

        $sql = "SELECT 
                c.id, 
                c.almacen, 
                c.paciente_id, 
                 u.nombre as doctor,
                c.fecha, 
                c.detalles, 
                c.atendera, 
                c.estado,
                c.fecha_creacion,
                cl.nombre_comercial AS paciente_nombre,
                cl.contacto AS dueno_contacto,
                cl.telefono AS paciente_telefono
            FROM citas_medicas c
            join usuarios u on u.id=c.atendera
            LEFT JOIN clientes cl ON c.paciente_id = cl.id
            $where
            ORDER BY c.fecha ASC";

        $resultado = $this->conexion->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function obtenerPorId($id)
    {
        $id = intval($id);
        $sql = "SELECT 
                    c.id, 
                    c.almacen, 
                    c.paciente_id, 
                    c.fecha, 
                    u.nombre as doctor,
                    c.detalles, 
                    c.atendera, 
                    c.estado,
                    c.fecha_creacion,
                    cl.nombre_comercial AS paciente_nombre,
                    cl.contacto AS dueno_contacto,
                    cl.telefono AS paciente_telefono
                FROM citas_medicas c
                join usuarios u on u.id=c.atendera
                LEFT JOIN clientes cl ON c.paciente_id = cl.id
                WHERE c.id = $id
                LIMIT 1";

        $resultado = $this->conexion->query($sql);

        if ($resultado && $row = $resultado->fetch_assoc()) {
            return $row;
        }

        return null;
    }
    /**
     * Guardar una nueva cita
     */
    public function guardar($datos)
    {
        $sql = "INSERT INTO citas_medicas (almacen, paciente_id, fecha, detalles, atendera, estado, fecha_creacion, tipo) 
            VALUES (?, ?, ?, ?, ?, 'pendiente', NOW(), ?)";

        $stmt = $this->conexion->prepare($sql);

        // Corregido a "iisiss" (atendera es 'i' porque es entero)
        $stmt->bind_param(
            "iissis",
            $datos['almacen'],
            $datos['paciente_id'],
            $datos['fecha'],
            $datos['detalles'],
            $datos['atendera'],
            $datos['tipo']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $stmt->insert_id];
        }

        return false;
    }
    /**
     * Actualizar información de una cita existente
     */
    public function actualizar($id, $datos, $almacen_id = 0)
    {
        $sql = "UPDATE citas_medicas 
                SET paciente_id = ?, fecha = ?, detalles = ?, atendera = ?
                WHERE id = ?";

        if ($almacen_id > 0) {
            $sql .= " AND almacen = " . intval($almacen_id);
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "isssi",
            $datos['paciente_id'],
            $datos['fecha'],
            $datos['detalles'],
            $datos['atendera'],
            $id
        );

        return $stmt->execute();
    }

    /**
     * Cambiar estado de la cita (Ej: pendiente, completada, cancelada)
     */
    public function cambiarEstado($id, $estado, $almacen_id = 0)
    {
        $sql = "UPDATE citas_medicas SET estado = ? WHERE id = ?";

        if ($almacen_id > 0) {
            $sql .= " AND almacen = " . intval($almacen_id);
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $estado, $id);

        return $stmt->execute();
    }

    /**
     * Resumen/Métricas rápidas de las citas para tarjetas del dashboard
     */
    public function getResumenCitas($almacen_id = 0)
    {
        $where = ($almacen_id > 0) ? " WHERE almacen = " . intval($almacen_id) : "";

        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN DATE(fecha) = CURDATE() THEN 1 ELSE 0 END) AS para_hoy,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) AS completadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) AS canceladas
                FROM citas_medicas $where";

        $res = $this->conexion->query($sql);
        return $res ? $res->fetch_assoc() : [
            'total' => 0,
            'para_hoy' => 0,
            'pendientes' => 0,
            'completadas' => 0,
            'canceladas' => 0
        ];
    }
}