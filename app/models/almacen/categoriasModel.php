<?php
class CategoriaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

   public function existe($nombre, $almacen_id) {
    $stmt = $this->db->prepare("SELECT id FROM categorias WHERE nombre = ? AND almacen_id = ?");
    $stmt->bind_param("si", $nombre, $almacen_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

public function guardar($nombre, $almacen_id) {
    // Se corrigen las columnas (almacen_id) y los placeholders (?, ?)
    $stmt = $this->db->prepare("INSERT INTO categorias (nombre, almacen_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $almacen_id);
    
    if ($stmt->execute()) {
        return $this->db->insert_id;
    }
    return false;
}
}