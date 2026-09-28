<?php
class PedidosVendedorModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * MÉTODO CLAVE: Listar con filtros dinámicos (Fecha, Almacén, Vendedor, Estatus)
     */
    public function listarConFiltros($filtros) {
        $sql = "SELECT p.*, 
                       c.nombre_comercial as cliente, 
                       u.nombre as vendedor, 
                       a.nombre as almacen_nombre
                FROM pedidos_vendedores p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN usuarios u ON p.vendedor_id = u.id
                LEFT JOIN almacenes a ON p.almacen_id = a.id
                WHERE p.fecha_solicitud BETWEEN ? AND ?";

        $params = [$filtros['desde'] . " 00:00:00", $filtros['hasta'] . " 23:59:59"];
        $types = "ss";

        // Filtro por Almacén (Si no es Admin Global)
        if (!empty($filtros['almacen_id'])) {
            $sql .= " AND p.almacen_id = ?";
            $params[] = $filtros['almacen_id'];
            $types .= "i";
        }

        // Filtro por Vendedor (Si el rol es Vendedor, solo ve sus IDs)
        if (!empty($filtros['vendedor_id'])) {
            $sql .= " AND p.vendedor_id = ?";
            $params[] = $filtros['vendedor_id'];
            $types .= "i";
        }

        // Filtro por Estatus
        if ($filtros['estatus'] !== 'todos') {
            $sql .= " AND p.estatus = ?";
            $params[] = $filtros['estatus'];
            $types .= "i";
        }

        $sql .= " ORDER BY p.fecha_solicitud DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function crearPedido($data, $items) {
        $this->db->begin_transaction();
        try {
            // 1. Generar Folio
            $res = $this->db->query("SELECT IFNULL(MAX(id), 0) + 1 as siguiente FROM pedidos_vendedores");
            $next = $res->fetch_assoc()['siguiente'];
            $folio = "PED-" . str_pad($next, 5, "0", STR_PAD_LEFT);

            // 2. INSERTAR EN pedidos_vendedores
            $sqlA = "INSERT INTO pedidos_vendedores (folio, vendedor_id, cliente_id, almacen_id, prioridad, observaciones, estatus) 
                     VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmtA = $this->db->prepare($sqlA);
            $stmtA->bind_param("siiiss", $folio, $data['vendedor_id'], $data['cliente_id'], $data['almacen_id'], $data['prioridad'], $data['observaciones']);
            $stmtA->execute();
            $pedido_id = $this->db->insert_id;

            // 3. INSERTAR EN solicitudes_pedidos (Sincronización con tabla B)
            $sqlB = "INSERT INTO solicitudes_pedidos (vendedor_id, cliente_id, estado, observaciones) 
                     VALUES (?, ?, 'pendiente', ?)";
            $stmtB = $this->db->prepare($sqlB);
            $stmtB->bind_param("iis", $data['vendedor_id'], $data['cliente_id'], $data['observaciones']);
            $stmtB->execute();

            // 4. INSERTAR DETALLES
            $sqlDetalle = "INSERT INTO detalle_pedido_vendedor (pedido_id, producto_id, cantidad, notas_producto) VALUES (?, ?, ?, ?)";
            $stmtDet = $this->db->prepare($sqlDetalle);

            foreach ($items as $item) {
                $stmtDet->bind_param("iids", $pedido_id, $item['producto_id'], $item['cantidad'], $item['notas']);
                $stmtDet->execute();
            }

            $this->db->commit();
            return $pedido_id;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function marcarComoCubierto($id) {
        $sql = "UPDATE pedidos_vendedores SET estatus = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerPedidoPorId($id) {
        $sql = "SELECT p.folio, p.fecha_solicitud, p.observaciones, p.prioridad, p.estatus,
                       c.nombre_comercial as cliente, 
                       u.nombre as vendedor_nombre, 
                       a.nombre as almacen_nombre
                FROM pedidos_vendedores p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN usuarios u ON p.vendedor_id = u.id
                LEFT JOIN almacenes a ON p.almacen_id = a.id
                WHERE p.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function listarDetallesPorPedido($pedido_id) {
        $sql = "SELECT d.cantidad, d.notas_producto, 
                       p.nombre as producto_nombre, p.sku, p.unidad_medida
                FROM detalle_pedido_vendedor d
                INNER JOIN productos p ON d.producto_id = p.id
                WHERE d.pedido_id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}