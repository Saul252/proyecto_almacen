<?php
class VentasTicketModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Obtiene la información general de la venta (Cabecera)
     */
    public function obtenerVentaPorId($id_venta)
    {
        $sql = "SELECT v.*, c.nombre_comercial, c.rfc, c.direccion, 
                       u.nombre as nombre_vendedor, u2.nombre as vendedor,
                       a.nombre as nombre_almacen, a.ubicacion as direccion_almacen
                FROM ventas v
                JOIN clientes c ON v.id_cliente = c.id
                JOIN usuarios u2 ON u2.id = v.vendedor_id
                JOIN usuarios u ON v.usuario_id = u.id
                JOIN almacenes a ON v.almacen_id = a.id
                WHERE v.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_venta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene el detalle de los productos vendidos con sus unidades de medida en array
     */
    public function obtenerDetallesVenta($id_venta)
    {
        $sql = "SELECT dv.*, p.nombre as producto_nombre, p.sku, 
                       p.factor_conversion as factor, p.unidad_reporte, p.unidad_medida, 
                       odma.id as odmaIdunidadMedida, odma.nombre as odmaNombre, odma.equivalencia as odmaEquivalencia 
                FROM detalle_venta dv 
                JOIN opciones_de_medida_adicional odma ON odma.id = dv.unidadMedida
                JOIN productos p ON dv.producto_id = p.id 
                WHERE dv.venta_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_venta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene los pagos registrados para la venta en array
     */
    public function obtenerPagosVenta($id_venta)
    {
        $sql = "SELECT h.* FROM historial_pagos h WHERE h.venta_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_venta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}