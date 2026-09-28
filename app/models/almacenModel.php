<?php
class CategoriaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function existe($nombre) {
        $stmt = $this->db->prepare("SELECT id FROM categorias WHERE nombre = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function guardar($nombre) {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
}