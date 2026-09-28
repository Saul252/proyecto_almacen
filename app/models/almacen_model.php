<?php
class AlmacenModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function getCategorias()
    {
        return $this->db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }
    public function getCategoriasAlmacen($almacen_id)
    {
        // 1. Estructura base limpia con el WHERE primero
        $sql = "SELECT id, nombre FROM categorias WHERE 1 = 1";

        // 2. Agregar la condición si el almacén es válido
        if ($almacen_id > 0) {
            // Asegúrate de que 'almacen_id' sea el nombre real de la columna en tu tabla
            $sql .= " AND almacen_id = " . intval($almacen_id);
        }

        // 3. El ORDER BY siempre va al final
        $sql .= " ORDER BY nombre ASC";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    public function getUnidadesMedida()
    {
        return $this->db->query("SELECT id, nombre, clave FROM unidades_medida ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function getAlmacenes($almacen_id)
    {
        $sql = "SELECT * FROM almacenes WHERE activo = 1";
        if ($almacen_id > 0)
            $sql .= " AND id = " . intval($almacen_id);
        $sql .= " ORDER BY nombre ASC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    public function inversion($almacen_id)
    {
        try {
            // Consulta base limpia
            $sql = "SELECT 
                    SUM(`cantidad_actual` * `precio_compra_unitario`) AS `valor_total_inventario`
                FROM 
                    `lotes_stock`
                WHERE 
                    `estado_lote` = 'activo' 
                    AND `cantidad_actual` > 0";

            // CORRECCIÓN 1: Filtrar por la columna correcta 'almacen_id'
            if ($almacen_id > 0) {
                $sql .= " AND `almacen_id` = " . intval($almacen_id);
            }

            // CORRECCIÓN 2: Eliminado el ORDER BY que rompía la consulta
            $result = $this->db->query($sql);

            if (!$result) {
                return 0;
            }

            // CORRECCIÓN 3: fetch_assoc porque es una sola fila de totales
            $fila = $result->fetch_assoc();

            // Retornamos el número directo (convertido a float) o 0 si es null
            return isset($fila['valor_total_inventario']) ? floatval($fila['valor_total_inventario']) : 0.00;

        } catch (Exception $e) {
            return 0;
        }
    }
    public function getAlmacenesDestino($almacen_id = 0)
    {
        // Iniciamos la consulta básica
        $sql = "SELECT id, nombre FROM almacenes WHERE activo = 1";

        // Si el almacén es mayor a cero, agregamos la exclusión
        if ($almacen_id > 0) {
            $sql .= " AND id != " . intval($almacen_id);
        }

        $sql .= " ORDER BY nombre ASC";

        $res = $this->db->query($sql);
        return ($res) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getInventario($almacen_id = 0)
    {
        $sql = "SELECT p.id, p.sku, p.nombre,p.descripcion, p.categoria_id, p.factor_conversion, p.unidad_reporte,p.unidad_medida,c.nombre AS categoria_nombre,

                       i.stock, i.almacen_id, a.nombre AS almacen_nombre
                FROM inventario i
                INNER JOIN productos p ON i.producto_id = p.id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1";

        if ($almacen_id > 0)
            $sql .= " AND i.almacen_id = " . intval($almacen_id);
        $sql .= " ORDER BY p.nombre ASC";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getInventarioConId($almacen_id = 0)
    {
        $sql = "SELECT 
                p.id, 
                p.sku, 
                p.nombre, 
                p.unidad_medida,
                i.stock, 
                i.almacen_id, 
                a.nombre AS almacen_nombre
            FROM inventario i
            INNER JOIN productos p ON i.producto_id = p.id
            INNER JOIN almacenes a ON i.almacen_id = a.id
            WHERE p.activo = 1";

        // Si se pasa un almacén específico, filtramos
        if ($almacen_id > 0) {
            $sql .= " AND i.almacen_id = " . intval($almacen_id);
        }

        $sql .= " AND i.stock > 0"; // Opcional: solo mostrar lo que tiene existencias
        $sql .= " ORDER BY p.nombre ASC";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getResumenStock($almacen_id)
    {
        // 1. Conteo global de productos únicos ACTIVOS en el catálogo maestro
        $sqlGlobal = "SELECT COUNT(*) as total FROM productos WHERE activo = 1";
        $resGlobal = $this->db->query($sqlGlobal)->fetch_assoc();
        $totalRegistrados = intval($resGlobal['total'] ?? 0);

        $id = intval($almacen_id);

        if ($id > 0) {
            // --- CASO VENDEDOR: Conteo de lo que tiene sucursal vs el total ---
            $sqlAlmacen = "SELECT 
                            (SELECT COUNT(DISTINCT producto_id) FROM inventario WHERE almacen_id = $id) as total_en_almacen,
                            nombre as nombre_almacen
                        FROM almacenes WHERE id = $id";
            $res = $this->db->query($sqlAlmacen)->fetch_assoc();

            return [
                "tipo" => "vendedor",
                "nombre" => $res['nombre_almacen'] ?? 'Almacén No Identificado',
                "mis_productos" => intval($res['total_en_almacen'] ?? 0),
                "total_sistema" => $totalRegistrados
            ];
        } else {
            // --- CASO ADMINISTRADOR: Control Total del Sistema ---
            // Al ser admin, sus "productos" son el 100% del catálogo activo
            return [
                "tipo" => "admin",
                "nombre" => "Sede Central (Global)",
                "mis_productos" => $totalRegistrados, // Aquí forzamos el 100% de cobertura
                "total_sistema" => $totalRegistrados
            ];
        }
    }
}