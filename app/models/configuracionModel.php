<?php

class ConfiguracionModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // --- MÉTODOS DE ROLES ---

    public function obtenerRoles() {
        return $this->db->query("SELECT * FROM roles ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function guardarRol($nombre, $id = null) {
        if ($id) {
            $stmt = $this->db->prepare("UPDATE roles SET nombre = ? WHERE id = ?");
            $stmt->bind_param("si", $nombre, $id);
        } else {
            $stmt = $this->db->prepare("INSERT INTO roles (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombre);
        }
        return $stmt->execute();
    }

    public function eliminarRol($id) {
        // La restricción ON DELETE CASCADE en tu DB se encarga de permisos_roles,
        // pero lo hacemos manual por seguridad si fuera necesario.
        $this->db->query("DELETE FROM permisos_roles WHERE rol_id = $id");
        return $this->db->query("DELETE FROM roles WHERE id = $id");
    }

    // --- MÉTODOS DE MÓDULOS ---

    public function obtenerModulos() {
        // Basado en tu estructura: inicio, ventas, compras, etc.
        return $this->db->query("SELECT * FROM modulos WHERE activo = 1 ORDER BY orden ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function guardarModulo($data) {
        $nombre = $data['nombre'];
        $identificador = $data['identificador'];
        $icono = $data['icono'] ?? 'bi bi-app';
        $orden = $data['orden'] ?? 0;
        $id = !empty($data['id']) ? intval($data['id']) : null;

        if ($id) {
            $stmt = $this->db->prepare("UPDATE modulos SET nombre=?, identificador=?, icono=?, orden=? WHERE id=?");
            $stmt->bind_param("sssii", $nombre, $identificador, $icono, $orden, $id);
        } else {
            $stmt = $this->db->prepare("INSERT INTO modulos (nombre, identificador, icono, orden, activo) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param("sssi", $nombre, $identificador, $icono, $orden);
        }
        return $stmt->execute();
    }

    public function eliminarModulo($id) {
        // Marcado lógico para no romper historiales
        return $this->db->query("UPDATE modulos SET activo = 0 WHERE id = $id");
    }

    // --- MÉTODOS DE PERMISOS (MATRIZ) ---
    
    public function verificarPermiso($rolId, $moduloIdent) {
        $stmt = $this->db->prepare("SELECT id FROM permisos_roles WHERE rol_id = ? AND modulo = ?");
        $stmt->bind_param("is", $rolId, $moduloIdent);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Sincroniza los permisos de la matriz.
     * @param array $permisos Estructura: [rol_id => [modulo1, modulo2, ...]]
     */
    public function actualizarMatrizPermisos($permisos) {
        try {
            $this->db->begin_transaction();

            // 1. Identificar qué roles vienen en el envío para no borrar permisos de otros roles
            $rolesIds = array_keys($permisos);

            if (!empty($rolesIds)) {
                // Convertimos a lista de enteros para la consulta
                $idsParaLimpiar = implode(',', array_map('intval', $rolesIds));
                
                // Borramos solo los permisos de los roles afectados en este POST
                $this->db->query("DELETE FROM permisos_roles WHERE rol_id IN ($idsParaLimpiar)");

                // 2. Preparar inserción
                $stmt = $this->db->prepare("INSERT INTO permisos_roles (rol_id, modulo) VALUES (?, ?)");

                foreach ($permisos as $rolId => $modulos) {
                    // array_unique previene errores de "Duplicate entry" por la UNIQUE KEY rol_modulo
                    $modulosUnicos = array_unique($modulos);
                    
                    foreach ($modulosUnicos as $moduloIdentificador) {
                        $rolIdInt = intval($rolId);
                        $stmt->bind_param("is", $rolIdInt, $moduloIdentificador);
                        $stmt->execute();
                    }
                }
            } else {
                // Si el array de permisos llega vacío, significa que desmarcaron TODO.
                // Aquí podrías decidir si borrar todos los permisos o no hacer nada.
                // Por seguridad del sistema, si no llega nada, no borramos masivamente.
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en ConfiguracionModel::actualizarMatrizPermisos -> " . $e->getMessage());
            return false;
        }
    }
}