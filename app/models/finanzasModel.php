<?php
class FinanzasModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function getUsuariosActivos($almacen_id = null) {
        $sql = "SELECT nombre, username FROM usuarios WHERE activo = 1";
        
        if (!empty($almacen_id)) {
            $almacen_id = (int)$almacen_id;
            $sql .= " AND almacen_id = {$almacen_id}";
        }
        
        $sql .= " LIMIT 6";
        return $this->db->query($sql);
    }

    public function getKPIs($almacen_id = null, $fecha_inicio = null, $fecha_fin = null) {
        // Condiciones base
        $condVentas = ["estado_general = 'activa'"];
        $condCompras = ["estado = 'confirmada'"];
        $condGastos = ["estado = 'pagado'"];

        // Filtro de almacén
        if (!empty($almacen_id)) {
            $id = (int)$almacen_id;
            $condVentas[] = "almacen_id = {$id}";
            $condCompras[] = "almacen_id = {$id}";
            $condGastos[] = "almacen_id = {$id}";
        }

        // Filtro de fechas (Si no se envían, toma el mes y año actual)
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $f_ini = $this->db->real_escape_string($fecha_inicio);
            $f_fin = $this->db->real_escape_string($fecha_fin);

            $condVentas[] = "fecha BETWEEN '{$f_ini} 00:00:00' AND '{$f_fin} 23:59:59'";
            $condCompras[] = "fecha_compra BETWEEN '{$f_ini} 00:00:00' AND '{$f_fin} 23:59:59'";
            $condGastos[] = "fecha_gasto BETWEEN '{$f_ini} 00:00:00' AND '{$f_fin} 23:59:59'";
        } else {
            $condVentas[] = "MONTH(fecha) = MONTH(CURRENT_DATE) AND YEAR(fecha) = YEAR(CURRENT_DATE)";
            $condCompras[] = "MONTH(fecha_compra) = MONTH(CURRENT_DATE) AND YEAR(fecha_compra) = YEAR(CURRENT_DATE)";
            $condGastos[] = "MONTH(fecha_gasto) = MONTH(CURRENT_DATE) AND YEAR(fecha_gasto) = YEAR(CURRENT_DATE)";
        }

        $sqlVentas = "SELECT COALESCE(SUM(total), 0) FROM ventas WHERE " . implode(" AND ", $condVentas);
        $sqlCompras = "SELECT COALESCE(SUM(total), 0) FROM compras WHERE " . implode(" AND ", $condCompras);
        $sqlGastos = "SELECT COALESCE(SUM(total), 0) FROM gastos WHERE " . implode(" AND ", $condGastos);

        $query = "SELECT 
            ({$sqlVentas}) as ventas_mes,
            ({$sqlCompras}) as compras_mes,
            ({$sqlGastos}) as gastos_mes";

        return $this->db->query($query)->fetch_assoc();
    }

    public function getStockAlmacenes($almacen_id = null) {
        $where = "WHERE a.activo = 1";
        
        if (!empty($almacen_id)) {
            $id = (int)$almacen_id;
            $where .= " AND a.id = {$id}";
        }

        $sql = "SELECT a.nombre, SUM(i.stock) as total_stock, SUM(i.stock * p.precio_adquisicion) as valor_total
                FROM almacenes a
                LEFT JOIN inventario i ON a.id = i.almacen_id
                LEFT JOIN productos p ON i.producto_id = p.id
                {$where}
                GROUP BY a.id";
                
        return $this->db->query($sql);
    }

    public function getTopProductos($almacen_id = null, $fecha_inicio = null, $fecha_fin = null) {
        $where = ["1=1"];

        if (!empty($almacen_id)) {
            $where[] = "v.almacen_id = " . (int)$almacen_id;
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $f_ini = $this->db->real_escape_string($fecha_inicio);
            $f_fin = $this->db->real_escape_string($fecha_fin);
            $where[] = "v.fecha BETWEEN '{$f_ini} 00:00:00' AND '{$f_fin} 23:59:59'";
        }

        $whereClause = implode(" AND ", $where);

        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido
                FROM detalle_venta dv
                JOIN ventas v ON dv.venta_id = v.id
                JOIN productos p ON dv.producto_id = p.id
                WHERE {$whereClause}
                GROUP BY p.id 
                ORDER BY total_vendido DESC 
                LIMIT 5";

        return $this->db->query($sql);
    }

    public function getStockCritico($almacen_id = null) {
        $where = ["i.stock <= i.stock_minimo", "a.activo = 1"];

        if (!empty($almacen_id)) {
            $where[] = "i.almacen_id = " . (int)$almacen_id;
        }

        $whereClause = implode(" AND ", $where);

        $sql = "SELECT p.nombre as producto, i.stock, i.stock_minimo, a.nombre as almacen 
                FROM inventario i 
                JOIN productos p ON i.producto_id = p.id 
                JOIN almacenes a ON i.almacen_id = a.id 
                WHERE {$whereClause}
                ORDER BY i.stock ASC 
                LIMIT 5";

        return $this->db->query($sql);
    }

    public function getPendientes($almacen_id = null, $fecha_inicio = null, $fecha_fin = null) {
        $whereCompras = ["estado = 'pendiente'"];
        $whereTraspasos = ["estado = 'en_transito'"];

        if (!empty($almacen_id)) {
            $id = (int)$almacen_id;
            $whereCompras[] = "almacen_id = {$id}";
            $whereTraspasos[] = "(almacen_origen_id = {$id} OR almacen_destino_id = {$id})";
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $f_ini = $this->db->real_escape_string($fecha_inicio);
            $f_fin = $this->db->real_escape_string($fecha_fin);
            $whereCompras[] = "fecha_compra BETWEEN '{$f_ini} 00:00:00' AND '{$f_fin} 23:59:59'";
            }

        $sqlCompras = "SELECT COUNT(*) as total FROM compras WHERE " . implode(" AND ", $whereCompras);
        

        return [
            'compras' => $this->db->query($sqlCompras)->fetch_assoc()['total'],
            'traspasos' =>[]
        ];
    }
}