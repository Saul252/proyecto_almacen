<?php

class TransmutacionesModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    // --- MÉTODOS DE APOYO (REVISADOS) ---

public function registrarTransmutacion($datos) {
        $this->db->begin_transaction();
        try {
            // 0. Obtener costo base del lote de origen
            $costo_origen = $this->obtenerCostoLote($datos['lote_origen_id']);

            // 1. INSERTAR CABECERA
            $sqlC = "INSERT INTO `transmutaciones` (`usuario_id`, `almacen_id`, `observaciones`) VALUES (?, ?, ?)";
            $stmtC = $this->db->prepare($sqlC);
            $stmtC->bind_param("iis", $datos['usuario_id'], $datos['almacen_id'], $datos['observaciones']);
            $stmtC->execute();
            $t_id = $this->db->insert_id;

            // 2. PROCESAR SALIDA (Materia Prima)
            $m_salida = $this->registrarMovimientoKardex(
                $datos['producto_origen_id'], 
                'salida', 
                $datos['cant_origen'], 
                $datos['almacen_id'], 
                $datos['usuario_id'], 
                $datos['responsable'], 
                "Salida Transmutación #$t_id"
            );
            
            $this->insertarDetalle($t_id, $m_salida, 'salida', $datos['producto_origen_id'], $datos['lote_origen_id'], $datos['cant_origen'], $costo_origen);
            $this->actualizarStockSustractivo($datos['producto_origen_id'], $datos['almacen_id'], $datos['lote_origen_id'], $datos['cant_origen']);

            // 3. DETERMINAR COSTO Y LOTE DE ENTRADA (Producto Terminado)
            if (!empty($datos['lote_destino_id']) && $datos['lote_destino_id'] > 0) {
                $costo_destino = $this->obtenerCostoLote($datos['lote_destino_id']);
                $l_dest_id = $datos['lote_destino_id'];
            } else {
                // Prorrateo de costo: (Cantidad Origen * Costo Origen) / Cantidad Destino
                $costo_destino = ($datos['cant_destino'] > 0) ? (($datos['cant_origen'] * $costo_origen) / $datos['cant_destino']) : 0;
                $l_dest_id = $this->obtenerOCrearLoteDestino($datos, $costo_destino);
            }

            // 4. PROCESAR ENTRADA
            $m_entrada = $this->registrarMovimientoKardex(
                $datos['producto_destino_id'], 
                'entrada', 
                $datos['cant_destino'], 
                $datos['almacen_id'], 
                $datos['usuario_id'], 
                $datos['responsable'], 
                "Entrada Transmutación #$t_id"
            );

            $this->insertarDetalle($t_id, $m_entrada, 'entrada', $datos['producto_destino_id'], $l_dest_id, $datos['cant_destino'], $costo_destino);
            
            // Si el lote ya existía, sumamos. Si fue nuevo, el método crearLote ya puso el stock inicial.
            if (!empty($datos['lote_destino_id']) && $datos['lote_destino_id'] > 0) {
                $this->actualizarStockAditivo($datos['producto_destino_id'], $datos['almacen_id'], $l_dest_id, $datos['cant_destino']);
            } else {
                // Solo sumamos al inventario general porque el lote nuevo nace con su stock
                $this->actualizarInventarioGeneral($datos['producto_destino_id'], $datos['almacen_id'], $datos['cant_destino'], 'suma');
            }

            $this->db->commit();
            return ['status' => 'success', 't_id' => $t_id];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // --- MÉTODOS DE ESCRITURA ---

    private function registrarMovimientoKardex($p_id, $tipo, $cant, $a_id, $u_id, $resp, $obs) {
        $colAlmacen = ($tipo === 'salida') ? 'almacen_origen_id' : 'almacen_destino_id';
        $sql = "INSERT INTO `movimientos` 
                (`producto_id`, `tipo`, `cantidad`, `$colAlmacen`, `usuario_registra_id`, `origen_movimiento`, `observaciones`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isdiiss", $p_id, $tipo, $cant, $a_id, $u_id, $resp, $obs);
        if(!$stmt->execute()) throw new Exception("Error en Kardex: " . $stmt->error);
        return $this->db->insert_id;
    }

    private function insertarDetalle($t_id, $m_id, $tipo, $p_id, $l_id, $cant, $costo) {
        // Se inserta en ambas columnas de costo para evitar errores de restricción NOT NULL
        $sql = "INSERT INTO `transmutacion_detalle` 
                (`transmutacion_id`, `movimiento_id`, `tipo`, `producto_id`, `lote_id`, `cantidad`, `costo_unitario`, `costo_unitario_historico`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisiiddd", $t_id, $m_id, $tipo, $p_id, $l_id, $cant, $costo, $costo);
        if(!$stmt->execute()) throw new Exception("Error en Detalle: " . $stmt->error);
    }

    private function actualizarStockSustractivo($p_id, $a_id, $l_id, $cant) {
        $stmt = $this->db->prepare("UPDATE lotes_stock SET cantidad_actual = cantidad_actual - ? WHERE id = ?");
        $stmt->bind_param("di", $cant, $l_id);
        $stmt->execute();

        $this->actualizarInventarioGeneral($p_id, $a_id, $cant, 'resta');
        $this->db->query("UPDATE lotes_stock SET estado_lote = 'agotado' WHERE id = $l_id AND cantidad_actual <= 0.001");
    }

    private function actualizarStockAditivo($p_id, $a_id, $l_id, $cant) {
        $stmt = $this->db->prepare("UPDATE lotes_stock SET cantidad_actual = cantidad_actual + ?, estado_lote = 'activo' WHERE id = ?");
        $stmt->bind_param("di", $cant, $l_id);
        $stmt->execute();

        $this->actualizarInventarioGeneral($p_id, $a_id, $cant, 'suma');
    }

    private function actualizarInventarioGeneral($p_id, $a_id, $cant, $operacion) {
        $signo = ($operacion === 'suma') ? "+" : "-";
        $sql = "UPDATE inventario SET stock = stock $signo ? WHERE almacen_id = ? AND producto_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("dii", $cant, $a_id, $p_id);
        if(!$stmt->execute()) throw new Exception("Error al actualizar inventario global");
    }

    private function obtenerOCrearLoteDestino($datos, $costo) {
        $nuevoCod = "TR-" . date('ymd') . "-" . strtoupper(substr(uniqid(), -4));
        $sql = "INSERT INTO lotes_stock (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisddd", $datos['producto_destino_id'], $datos['almacen_id'], $nuevoCod, $datos['cant_destino'], $datos['cant_destino'], $costo);
        if(!$stmt->execute()) throw new Exception("Error al crear Lote: " . $stmt->error);
        return $this->db->insert_id;
    }

    // --- MÉTODOS DE CONSULTA ---

  
    public function obtenerCostoLote($l_id) {
    $sql = "SELECT `precio_compra_unitario` FROM `lotes_stock` WHERE `id` = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $l_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    
    return (float)($res['precio_compra_unitario'] ?? 0);
}
    public function obtenerDestinosCompatibles($producto_origen_id) {
        $sql = "SELECT p.id, p.nombre,p.unidad_medida, p.sku, c.rendimiento_teorico 
                FROM config_transmutaciones c
                INNER JOIN productos p ON c.producto_destino_id = p.id
                WHERE c.producto_origen_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $producto_origen_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function agregarConfiguracion($almacen_id, $origen, $destino, $factor, $usuario_id, $notas) {
    // IMPORTANTE: Asegúrate de que tu tabla tenga el campo usuario_id y almacen_id
    $sql = "INSERT INTO config_transmutaciones 
            (almacen_id, producto_origen_id, producto_destino_id, rendimiento_teorico, usuario_id, notas) 
            VALUES (?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            rendimiento_teorico = ?, 
            usuario_id = ?, 
            notas = ?";
    
    $stmt = $this->db->prepare($sql);
    
    // Tipos de datos: i (int), i (int), i (int), d (decimal), i (int), s (string)
    // Luego para el Update: d, i, s
    $stmt->bind_param("iiididsis", 
        $almacen_id, $origen, $destino, $factor, $usuario_id, $notas,
        $factor, $usuario_id, $notas
    );
    
    return $stmt->execute();

}
/**
     * Obtiene el historial de transmutaciones con nombres de productos
     */
// public function listarTransmutaciones($almacen_id) {
//     // Si el almacen_id es 0, es admin y ve todo (1=1)
//     $where = ($almacen_id > 0) ? "WHERE t.almacen_id = ?" : "WHERE 1=1";
    
//     $sql = "SELECT 
//                 t.id, 
//                 t.fecha as fecha_registro,
//                 t.observaciones,
//                 u.nombre as usuario_nombre,
                
//                 -- Producto de salida (origen)
//                 p1.nombre as producto_origen,
//                 p1.unidad_medida as unidad_origen,
//                 td1.cantidad as cant_origen,
                
//                 -- Producto de entrada (destino)
//                 p2.nombre as producto_destino,
//                 p2.unidad_medida as unidad_destino,
//                 td2.cantidad as cant_destino,
//                 a.nombre as almacen

//             FROM transmutaciones t
//             LEFT JOIN usuarios u ON t.usuario_id = u.id
            
//             -- Unión para obtener el producto que SALE (origen)
//             LEFT JOIN transmutacion_detalle td1 ON t.id = td1.transmutacion_id AND td1.tipo = 'salida'
//             LEFT JOIN productos p1 ON td1.producto_id = p1.id
            
//             -- Unión para obtener el producto que ENTRA (destino)
//             LEFT JOIN transmutacion_detalle td2 ON t.id = td2.transmutacion_id AND td2.tipo = 'entrada'
//             LEFT JOIN productos p2 ON td2.producto_id = p2.id
//             join almacenes a on a.id=t.almacen_id
            
//             $where
            
//             -- Agrupamos por el ID de transmutación para asegurar una fila por registro
//             GROUP BY t.id
//             ORDER BY t.fecha DESC 
//             LIMIT 100";
    
//     $stmt = $this->db->prepare($sql);
    
//     if ($almacen_id > 0) {
//         $stmt->bind_param("i", $almacen_id);
//     }
    
//     $stmt->execute();
//     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// }
public function listarTransmutaciones($filtros = [], $rol_id = 1, $almacen_sesion = 0) {
    $where = " WHERE 1=1 ";

    // 1. Seguridad por Almacén / Filtro de Almacén
    if ($rol_id != 1) { 
        // Si no es admin, restringe al almacén de su sesión
        $where .= " AND t.almacen_id = " . intval($almacen_sesion) . " "; 
    } elseif (!empty($filtros['almacen'])) { 
        // Si es admin y seleccionó un almacén específico
        $where .= " AND t.almacen_id = " . intval($filtros['almacen']) . " "; 
    }

    // 2. Filtro por Producto (ID de Producto en Origen o Destino)
    if (!empty($filtros['producto'])) {
        $prod_id = intval($filtros['producto']);
        $where .= " AND (td1.producto_id = $prod_id OR td2.producto_id = $prod_id) ";
    }

    // 3. Buscador General (ID, Observaciones o Nombres de Productos)
    if (!empty($filtros['search'])) {
        $s = $this->db->real_escape_string($filtros['search']);
        $where .= " AND (t.id LIKE '%$s%' 
                    OR t.observaciones LIKE '%$s%' 
                    OR p1.nombre LIKE '%$s%' 
                    OR p2.nombre LIKE '%$s%') ";
    }

    // 4. Rango de Fechas
    if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
        $where .= $this->construirFiltroFecha($filtros, 't.fecha');
    }

    $sql = "SELECT 
                t.id, 
                t.fecha as fecha_registro,
                t.observaciones,
                u.nombre as usuario_nombre,
                
                -- Producto de salida (origen)
                p1.nombre as producto_origen,
                p1.unidad_medida as unidad_origen,
                td1.cantidad as cant_origen,
                
                -- Producto de entrada (destino)
                p2.nombre as producto_destino,
                p2.unidad_medida as unidad_destino,
                td2.cantidad as cant_destino,
                a.nombre as almacen

            FROM transmutaciones t
            LEFT JOIN usuarios u ON t.usuario_id = u.id
            
            -- Unión para obtener el producto que SALE (origen)
            LEFT JOIN transmutacion_detalle td1 ON t.id = td1.transmutacion_id AND td1.tipo = 'salida'
            LEFT JOIN productos p1 ON td1.producto_id = p1.id
            
            -- Unión para obtener el producto que ENTRA (destino)
            LEFT JOIN transmutacion_detalle td2 ON t.id = td2.transmutacion_id AND td2.tipo = 'entrada'
            LEFT JOIN productos p2 ON td2.producto_id = p2.id
            JOIN almacenes a ON a.id = t.almacen_id
            
            $where
            
            GROUP BY t.id
            ORDER BY t.fecha DESC";

    $res = $this->db->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
private function construirFiltroFecha($f, $campoFecha = 't.fecha') {
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
/**
 * Obtiene las reglas de transmutación configuradas.
 * @param int $almacen_id Si es 0, actúa como SuperAdmin y trae todas.
 * @return array Lista de reglas con nombres de productos y almacenes.
 */
public function listarConfiguraciones($almacen_id = 0) {
    try {
        // Añadimos ct.almacen_id para que el PHP pueda agrupar
        $sql = "SELECT 
                    ct.id,
                    ct.almacen_id, 
                    a.nombre AS almacen,
                    p1.sku AS sku_origen,
                    p1.nombre AS producto_origen,
                    p2.sku AS sku_destino,
                    p2.nombre AS producto_destino,
                    ct.rendimiento_teorico,
                    p1.unidad_medida AS unidad,
                    ct.notas
                FROM config_transmutaciones ct
                JOIN almacenes a ON ct.almacen_id = a.id
                JOIN productos p1 ON ct.producto_origen_id = p1.id
                JOIN productos p2 ON ct.producto_destino_id = p2.id";

        if ($almacen_id > 0) {
            $sql .= " WHERE ct.almacen_id = ?";
        }

        $sql .= " ORDER BY a.nombre ASC";

        $stmt = $this->db->prepare($sql);

        if ($almacen_id > 0) {
            $stmt->bind_param("i", $almacen_id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {
        error_log("Error en listarConfiguraciones: " . $e->getMessage());
        return [];
    }
}
}