<?php
class VacacionesModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Listar todos los registros de vacaciones
    public function listar() {
        $sql = "SELECT v.*, t.nombre AS trabajador 
                FROM vacaciones v 
                INNER JOIN trabajadores t ON v.id_trabajador = t.id 
                ORDER BY v.fecha DESC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 2. Obtener un registro específico por su ID
    public function obtenerPorId($id) {
    $id = intval($id);
    $sql = "SELECT v.*, t.nombre AS nombre
            FROM vacaciones v 
            JOIN trabajadores t ON v.id_trabajador = t.id 
            WHERE v.id = $id 
            LIMIT 1";
            
    $res = $this->db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

    // 3. Insertar un nuevo registro de vacaciones
    public function insertar($datos) {
        $id_trabajador    = intval($datos['id_trabajador']);
        $fecha            = $this->db->real_escape_string($datos['fecha']);
        $dias_disponibles = intval($datos['dias_disponibles']);
        $dias_a_tomar     = intval($datos['dias_a_tomar']);
        $monto_restante   = floatval($datos['monto_restante']);
        $retenciones      = floatval($datos['retenciones']);

        $sql = "INSERT INTO vacaciones (id_trabajador, fecha, dias_disponibles, dias_a_tomar, monto_restante, retenciones) 
                VALUES ($id_trabajador, '$fecha', $dias_disponibles, $dias_a_tomar, $monto_restante, $retenciones)";

        return $this->db->query($sql);
    }

    // 4. Editar / Actualizar un registro existente
    public function editar($id, $datos) {
        $id               = intval($id);
        $id_trabajador    = intval($datos['id_trabajador']);
        $fecha            = $this->db->real_escape_string($datos['fecha']);
        $dias_disponibles = intval($datos['dias_disponibles']);
        $dias_a_tomar     = intval($datos['dias_a_tomar']);
        $monto_restante   = floatval($datos['monto_restante']);
        $retenciones      = floatval($datos['retenciones']);

        $sql = "UPDATE vacaciones 
                SET id_trabajador = $id_trabajador, 
                    fecha = '$fecha', 
                    dias_disponibles = $dias_disponibles, 
                    dias_a_tomar = $dias_a_tomar, 
                    monto_restante = $monto_restante, 
                    retenciones = $retenciones 
                WHERE id = $id";

        return $this->db->query($sql);
    }
    /**
     * Obtener registros de vacaciones filtrados con soporte de fechas y permisos
     */
    public function obtenerVacacionesFiltradas($filtros = [], $rol_id = 1, $usuario = 0) {
        $where = " WHERE v.id > 0 ";

        // Seguridad / Filtro por trabajador (Si no es admin o si se especifica en filtros)
        if ($rol_id > 3) {
            $where .= " AND v.id_trabajador = " . intval($usuario);
        } elseif (!empty($filtros['id_trabajador'])) {
            $where .= " AND v.id_trabajador = " . intval($filtros['id_trabajador']);
        }
if (!empty($filtros['almacen'])) {
            $where .= " AND t.almacen_id = " . intval($filtros['almacen']);
        }
        // Buscador (Nombre del Trabajador o ID de la solicitud)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (t.nombre LIKE '%$s%' OR v.id LIKE '%$s%') ";
        }
       

        // Filtro por Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFechaVacaciones($filtros);
        }

        // Filtro por Estado de Días / Saldo (ejemplo: si aún le quedan días o monto)
        $having = "";
        if (!empty($filtros['monto'])) {
            $having = ($filtros['monto'] == 'pendiente') 
                ? " HAVING v.monto_restante > 0.01 " 
                : " HAVING v.monto_restante <= 0.01 ";
        }

        $sql = "SELECT 
                    v.id,
                    v.id_trabajador,
                    t.nombre AS trabajador,
                    v.fecha,
                    v.dias_disponibles,
                    v.dias_a_tomar,
                    v.monto_restante,
                    v.retenciones
                FROM vacaciones v
                INNER JOIN trabajadores t ON v.id_trabajador = t.id
                $where 
                $having
                ORDER BY v.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Helper para la construcción dinámica del WHERE para las fechas de vacaciones
     */
    private function construirFiltroFechaVacaciones($f) {
        switch($f['rango']) {
            case 'hoy': 
                return " AND DATE(v.fecha) = CURDATE() ";
            case 'ayer': 
                return " AND DATE(v.fecha) = SUBDATE(CURDATE(), 1) ";
            case 'semana': 
                return " AND YEARWEEK(v.fecha, 1) = YEARWEEK(CURDATE(), 1) ";
            case 'mes': 
                return " AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE()) ";
            case 'personalizado':
                if (!empty($f['inicio']) && !empty($f['fin'])) {
                    $ini = $this->db->real_escape_string($f['inicio']);
                    $fin = $this->db->real_escape_string($f['fin']);
                    return " AND DATE(v.fecha) BETWEEN '$ini' AND '$fin' ";
                }
                return "";
            default: 
                return "";
        }
    }

    // 5. Eliminar un registro de vacaciones
    public function eliminar($id) {
        $id = intval($id);
        $sql = "DELETE FROM vacaciones WHERE id = $id";
        return $this->db->query($sql);
    }
}