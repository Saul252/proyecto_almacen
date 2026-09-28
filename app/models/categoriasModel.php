<?php
class CategoriasModel {
    
    /**
     * READ: Obtener todas las categorías
     */
    public static function listar($conexion) {
        $sql = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
        return $conexion->query($sql);
    }

    /**
     * READ: Obtener una categoría por ID (para editar)
     */
    public static function obtenerPorId($conexion, $id) {
        $stmt = $conexion->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * CREATE: Guardar nueva categoría
     */
    public static function guardar($conexion, $nombre) {
        $stmt = $conexion->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        return $stmt->execute();
    }

    /**
     * UPDATE: Actualizar nombre de categoría
     */
    public static function actualizar($conexion, $id, $nombre) {
        $stmt = $conexion->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre, $id);
        return $stmt->execute();
    }

    /**
     * DELETE: Eliminar categoría
     * NOTA: Primero verifica si hay productos usando esta categoría.
     */
    public static function eliminar($conexion, $id) {
        // 1. Verificar si existen productos vinculados
        $check = $conexion->prepare("SELECT id FROM productos WHERE categoria_id = ? LIMIT 1");
        $check->bind_param("i", $id);
        $check->execute();
        $resultado = $check->get_result();

        if ($resultado->num_rows > 0) {
            // No se puede eliminar porque tiene productos
            return ['status' => 'error', 'message' => 'No se puede eliminar: Hay productos asociados a esta categoría.'];
        }

        // 2. Si está limpia, proceder a borrar
        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Categoría eliminada correctamente.'];
        } else {
            return ['status' => 'error', 'message' => 'Error al intentar eliminar la categoría.'];
        }
    }
}