<?php
class CategoriasGasto {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT * FROM gastos_categorias WHERE activo = 1 ORDER BY nombre ASC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function guardar($nombre, $descripcion) {
        $sql = "INSERT INTO gastos_categorias (nombre, descripcion) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nombre, $descripcion);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
public function guardarInsumo($nombre, $descripcion,$uma,$umi,$factor) {
        $sql = "INSERT INTO insumos (nombre, descripcion,u_ma,u_mi,factor) VALUES (?, ?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $descripcion,$uma,$umi,$factor);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function actualizar($id, $nombre, $descripcion) {
        $sql = "UPDATE gastos_categorias SET nombre = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Hacemos borrado lógico para no romper historial de gastos viejos
        $sql = "UPDATE gastos_categorias SET activo = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}