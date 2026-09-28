<?php
class MermasModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function registrarMerma($datos) {
    $this->db->begin_transaction();
    try {
        // 1. INSERTAR EN MOVIMIENTOS (Historial / Kardex)
        $sqlMov = "INSERT INTO movimientos 
                   (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, origen_movimiento, observaciones) 
                   VALUES (?, 'Merma', ?, ?, ?, ?, ?)";
        $stmtMov = $this->db->prepare($sqlMov);
        $stmtMov->bind_param("idiiss", 
            $datos['producto_id'], 
            $datos['cantidad'], 
            $datos['almacen_id'], 
            $datos['usuario_id'], 
            $datos['responsable'], 
            $datos['motivo']
        );
        $stmtMov->execute();
        $movimiento_id = $this->db->insert_id;

        // 2. INSERTAR EN MERMAS (Detalle de la pérdida)
        $sqlMerma = "INSERT INTO mermas 
                     (movimiento_id, almacen_id, producto_id, lote_id, cantidad, tipo_merma, responsable_declaracion, descripcion_suceso) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtMerma = $this->db->prepare($sqlMerma);
        $stmtMerma->bind_param("iiiidsss", 
            $movimiento_id, $datos['almacen_id'], $datos['producto_id'], $datos['lote_id'], 
            $datos['cantidad'], $datos['tipo_merma'], $datos['responsable'], $datos['motivo']
        );
        $stmtMerma->execute();

        // 3. ACTUALIZAR STOCK ESPECÍFICO (Lotes)
        $sqlLote = "UPDATE lotes_stock 
                    SET cantidad_actual = cantidad_actual - ?, 
                        estado_lote = IF(cantidad_actual - ? <= 0, 'agotado', estado_lote)
                    WHERE id = ? AND almacen_id = ?";
        $stmtLote = $this->db->prepare($sqlLote);
        // Usamos la cantidad dos veces (una para restar y otra para el IF del estado)
        $stmtLote->bind_param("ddii", $datos['cantidad'], $datos['cantidad'], $datos['lote_id'], $datos['almacen_id']);
        $stmtLote->execute();

        // 4. ACTUALIZAR STOCK GLOBAL (Tabla Inventario)
        $sqlInv = "UPDATE inventario 
                   SET stock = stock - ? 
                   WHERE almacen_id = ? AND producto_id = ?";
        $stmtInv = $this->db->prepare($sqlInv);
        $stmtInv->bind_param("dii", $datos['cantidad'], $datos['almacen_id'], $datos['producto_id']);
        $stmtInv->execute();

        // Verificamos que se haya actualizado el stock global
        if ($stmtInv->affected_rows === 0) {
            throw new Exception("Error: El producto no tiene un registro inicial en la tabla de inventario para este almacén.");
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollback();
        return $e->getMessage();
    }
}

    // Método para cargar lotes dinámicamente
    public function getLotesPorProducto($almacen_id, $producto_id) {
        $sql = "SELECT id, codigo_lote, cantidad_actual, precio_compra_unitario 
                FROM lotes_stock 
                WHERE almacen_id = ? AND producto_id = ? AND estado_lote = 'activo' AND cantidad_actual > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $almacen_id, $producto_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
  public function obtenerMermasPaginadas($almacen_id, $limit = 10, $offset = 0) {
    // Si el almacen_id es 0, no filtramos (Admin)
    $filtroAlmacen = ($almacen_id > 0) ? "WHERE m.almacen_id = ?" : "";
    
    $sql = "SELECT 
                m.id,
                m.fecha_reporte, -- <--- Nombre corregido según tu DB
                m.cantidad,
                m.tipo_merma,
                mov.origen_movimiento as responsable, -- Tomamos el responsable del movimiento
                a.nombre as almacen_nombre,
                p.nombre as producto_nombre,
                l.codigo_lote
            FROM mermas m
            JOIN movimientos mov ON m.movimiento_id = mov.id
            JOIN almacenes a ON m.almacen_id = a.id
            JOIN productos p ON m.producto_id = p.id
            LEFT JOIN lotes_stock l ON m.lote_id = l.id
            $filtroAlmacen
            ORDER BY m.fecha_reporte DESC
            LIMIT ? OFFSET ?";
            
    $stmt = $this->db->prepare($sql);

    if ($almacen_id > 0) {
        $stmt->bind_param("iii", $almacen_id, $limit, $offset);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerMermasFiltradas($filtros = [], $rol_id = 1, $almacen_sesion = 0) {
    $where = " WHERE 1=1 ";

    // 1. Seguridad por Almacén / Filtro por Almacén
    if ($rol_id != 1) { 
        $where .= " AND m.almacen_id = " . intval($almacen_sesion) . " "; 
    } elseif (!empty($filtros['almacen'])) { 
        $where .= " AND m.almacen_id = " . intval($filtros['almacen']) . " "; 
    }

    // 2. Filtro por Producto
    if (!empty($filtros['producto'])) {
        $prod_id = intval($filtros['producto']);
        $where .= " AND m.producto_id = $prod_id ";
    }

    // 3. Filtro por Tipo de Merma
    if (!empty($filtros['tipo_merma'])) {
        $tipo = $this->db->real_escape_string($filtros['tipo_merma']);
        $where .= " AND m.tipo_merma = '$tipo' ";
    }

    // 4. Buscador General (ID, producto, código de lote, tipo merma o responsable)
    if (!empty($filtros['search'])) {
        $s = $this->db->real_escape_string($filtros['search']);
        $where .= " AND (m.id LIKE '%$s%' 
                    OR p.nombre LIKE '%$s%' 
                    OR l.codigo_lote LIKE '%$s%' 
                    OR m.tipo_merma LIKE '%$s%' 
                    OR mov.origen_movimiento LIKE '%$s%') ";
    }

    // 5. Rango de Fechas (Evaluado sobre m.fecha_reporte)
    if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
        $where .= $this->construirFiltroFecha($filtros, 'm.fecha_reporte');
    }

    $sql = "SELECT 
                m.id,
                m.fecha_reporte,
                m.cantidad,
                m.tipo_merma,
                m.descripcion_suceso,
                mov.origen_movimiento as responsable,
                a.nombre as almacen_nombre,
                p.nombre as producto_nombre,
                p.unidad_medida,
                l.codigo_lote
            FROM mermas m
            JOIN movimientos mov ON m.movimiento_id = mov.id
            JOIN almacenes a ON m.almacen_id = a.id
            JOIN productos p ON m.producto_id = p.id
            LEFT JOIN lotes_stock l ON m.lote_id = l.id
            $where
            ORDER BY m.fecha_reporte DESC, m.id DESC";

    $res = $this->db->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
private function construirFiltroFecha($f, $campoFecha = 'm.fecha_reporte') {
    if (empty($f['rango']) || $f['rango'] === 'todos') {
        return "";
    }
    
    switch ($f['rango']) {
        case 'hoy': 
            return " AND DATE($campoFecha) = CURDATE() ";
        case 'ayer': 
            return " AND DATE($campoFecha) = SUBDATE(CURDATE(), 1) ";
        case 'semana': 
            return " AND YEARWEEK($campoFecha, 1) = YEARWEEK(CURDATE(), 1) ";
        case 'mes': 
            return " AND MONTH($campoFecha) = MONTH(CURDATE()) AND YEAR($campoFecha) = YEAR(CURDATE()) ";
        case 'personalizado':
            if (!empty($f['inicio']) && !empty($f['fin'])) {
                $ini = $this->db->real_escape_string($f['inicio']);
                $fin = $this->db->real_escape_string($f['fin']);
                return " AND DATE($campoFecha) BETWEEN '$ini' AND '$fin' ";
            }
            return "";
        default: 
            return "";
    }
}
// Método auxiliar para contar el total y saber cuántas páginas hay
public function contarTotalMermas($almacen_id) {
    $sql = "SELECT COUNT(*) as total FROM mermas";
    if ($almacen_id > 0) {
        $sql .= " WHERE almacen_id = " . intval($almacen_id);
    }
    $result = $this->db->query($sql);
    return $result->fetch_assoc()['total'];
}
}