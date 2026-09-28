<?php
/**
 * almacenesModel.php
 * Modelo para la gestión de Almacenes / Licencias / Cuentas
 */

class controlAlmacenesModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Listar almacenes filtrados por sesión o criterio
     */
  /**
     * Listar todos los almacenes incluyendo la relación con planes
     */
    public function listarTodos($almacen_sesion = 0) {
        $where = " WHERE 1=1 ";
        if ($almacen_sesion > 0) {
            $where .= " AND a.id = " . intval($almacen_sesion) . " ";
        }

        $sql = "SELECT a.`id`, a.`codigo`, a.`nombre`, a.`hora_cierre_programada`, a.`ubicacion`, 
                       a.`activo`, a.`fecha_creacion`, a.`tipo_plan`, a.`pago`, p.nombre AS plan
                FROM `almacenes` a
                LEFT JOIN `planes` p ON p.id = a.tipo_plan
                $where 
                ORDER BY a.`id` DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
public function planes($almacen_id=0 ) {
        $sql = "SELECT * FROM planes WHERE 1 = 1";
        if ($almacen_id > 0) $sql .= " AND id = " . intval($almacen_id);
        $sql .= " ORDER BY nombre ASC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener almacén por ID incluyendo la relación con planes
     */
    public function obtenerPorId($id) {
        $sql = "SELECT a.`id`, a.`codigo`, a.`nombre`, a.`hora_cierre_programada`, a.`ubicacion`, 
                       a.`activo`, a.`fecha_creacion`, a.`tipo_plan`, a.`pago`, p.nombre AS plan
                FROM `almacenes` a
                LEFT JOIN `planes` p ON p.id = a.tipo_plan
                WHERE a.`id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    /**
     * Registrar un NUEVO almacén (incluyendo su contraseña encriptada)
     */
    public function guardar($datos) {
    // 1. Columnas encerradas con backticks (`) y no comillas simples (')
    $sql = "INSERT INTO `almacenes` 
            (`codigo`, `nombre`, `hora_cierre_programada`, `ubicacion`, `activo`, `tipo_plan`, `pago`, `logo`, `ico`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $this->db->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $this->db->error);
    }
    
    $activo = 0;
    $tipo_plan = isset($datos['tipo_plan']) ? intval($datos['tipo_plan']) : 1;
    $logo = $datos['logo'] ?? '';
    $ico = $datos['ico'] ?? '';
    
    // 2. Definición exacta de 9 tipos para 9 parámetros: s s s s i i s s s
    $stmt->bind_param("ssssiisss", 
        $datos['codigo'], 
        $datos['nombre'], 
        $datos['hora_cierre_programada'], 
        $datos['ubicacion'], 
        $activo, 
        $tipo_plan, 
        $datos['pago'],
        $logo,
        $ico
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar almacén: " . $stmt->error);
    }
    
    return $this->db->insert_id;
}

    /**
     * Actualizar la información completa de un almacén existente (sin alterar password)
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE `almacenes` 
                SET `codigo` = ?, 
                    `nombre` = ?, 
                    `hora_cierre_programada` = ?, 
                    `ubicacion` = ?, 
                    `tipo_plan` = ?, 
                    `pago` = ? 
                WHERE `id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssssi", 
            $datos['codigo'], 
            $datos['nombre'], 
            $datos['hora_cierre_programada'], 
            $datos['ubicacion'], 
            $datos['tipo_plan'], 
            $datos['pago'], 
            $id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar almacén: " . $stmt->error);
        }
        return true;
    }

    /**
     * Cambiar estado activo/inactivo (Activar o desactivar cuenta)
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE `almacenes` SET `activo` = ? WHERE `id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $estado, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al cambiar estado: " . $stmt->error);
        }
        return true;
    }

    /**
     * Cambiar estado de pago de forma independiente (al_dia, pendiente, vencido)
     */
    public function cambiarEstadoPago($id, $pago) {
        $sql = "UPDATE `almacenes` SET `pago` = ? WHERE `id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $pago, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar el estado de pago: " . $stmt->error);
        }
        return true;
    }

    /**
     * Cambiar el tipo de plan de forma independiente (basico, pro, enterprise)
     */
    public function cambiarTipoPlan($id, $tipo_plan) {
        $sql = "UPDATE `almacenes` SET `tipo_plan` = ? WHERE `id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $tipo_plan, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar el tipo de plan: " . $stmt->error);
        }
        return true;
    }

    /**
     * Cambiar el nombre del almacén de forma independiente
     */
    public function cambiarNombre($id, $nombre) {
        $sql = "UPDATE `almacenes` SET `nombre` = ? WHERE `id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nombre, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar el nombre: " . $stmt->error);
        }
        return true;
    }

    /**
     * Actualizar contraseña/clave de acceso del almacén o la cuenta asociada
     */
    public function actualizarPassword($id, $passwordHash) {
        $sql = "UPDATE `almacenes` SET `password` = ? WHERE `id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $passwordHash, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar la contraseña: " . $stmt->error);
        }
        return true;
    }
}