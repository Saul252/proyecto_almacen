<?php
class CategoriasGasto
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function listarTodas()
    {
        $sql = "SELECT * FROM gastos_categorias WHERE activo = 1 ORDER BY nombre ASC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function guardar($nombre, $descripcion)
    {
        $sql = "INSERT INTO gastos_categorias (nombre, descripcion) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nombre, $descripcion);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    public function guardarInsumo($nombre, $descripcion, $uma, $umi, $factor, $almacen_id)
    {
        $sql = "INSERT INTO insumos (nombre, descripcion,u_ma,u_mi,factor,almacen_id) VALUES (?, ?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssi", $nombre, $descripcion, $uma, $umi, $factor, $almacen_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function actualizar($id, $nombre, $descripcion)
    {
        $sql = "UPDATE gastos_categorias SET nombre = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
        return $stmt->execute();
    }
    public function actualizarInsumo($id, $nombre, $descripcion, $uma, $umi, $factor)
    {
        $sql = "UPDATE insumos 
            SET nombre = ?, 
                descripcion = ?, 
                u_ma = ?, 
                u_mi = ?, 
                factor = ? 
            WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar UPDATE: " . $this->db->error);
        }

        $stmt->bind_param("sssssi", $nombre, $descripcion, $uma, $umi, $factor, $id);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar UPDATE: " . $stmt->error);
        }

        // 🔥 affected_rows = 0 significa que no se modificó nada (mismos datos o ID inexistente)
        if ($stmt->affected_rows === 0) {
            // Verificar si el ID existe
            $check = $this->db->prepare("SELECT id FROM insumos WHERE id = ?");
            $check->bind_param("i", $id);
            $check->execute();
            $existe = $check->get_result()->num_rows > 0;
            $check->close();

            if (!$existe) {
                throw new Exception("El insumo con ID {$id} no existe.");
            }

            // El ID existe pero los datos son idénticos
            return 'sin_cambios';
        }

        return true;
    }

    public function eliminar($id)
    {
        // Hacemos borrado lógico para no romper historial de gastos viejos
        $sql = "UPDATE gastos_categorias SET activo = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}