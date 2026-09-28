<?php
/**
 * ClientesEstatusModel.php
 * Versión optimizada para el esquema cfsistem (Marzo 2026)
 */

class ClientesEstatusModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Lista todos los clientes activos con el resumen financiero y logístico real.
     * Basado en tablas: ventas, historial_pagos y detalle_venta.
     */
   public function listarResumenClientes($almacen_id) {
    // Si es 0 (Admin), el WHERE siempre será verdadero (1=1)
    // Si no es 0, filtrará por el ID correspondiente.
    $sql = "SELECT 
            c.id, 
            c.nombre_comercial AS nombre, 
            c.rfc,
            c.almacen_id, 
            IFNULL(cp.saldo_en_contra, 0) AS saldo_en_contra,
            IFNULL(cp.saldo_a_favor, 0) AS saldo_a_favor,
            -- Subconsulta 1: Total de ventas activas
            (SELECT COUNT(*) 
             FROM ventas v_count 
             WHERE v_count.id_cliente = c.id 
               AND v_count.estado_general = 'activa') AS total_ventas,
            -- Subconsulta 2: Deuda total (Total ventas - Pagos realizados)
            (SELECT IFNULL(SUM(v_pago.total), 0) - 
                    IFNULL((SELECT SUM(hp.monto) 
                            FROM historial_pagos hp 
                            INNER JOIN ventas v_hp ON hp.venta_id = v_hp.id 
                            WHERE v_hp.id_cliente = c.id 
                              AND v_hp.estado_general = 'activa'), 0) 
             FROM ventas v_pago 
             WHERE v_pago.id_cliente = c.id 
               AND v_pago.estado_general = 'activa') AS saldo_deuda,
            -- Subconsulta 3: Entregas pendientes
            (SELECT COUNT(*) 
             FROM detalle_venta dv 
             INNER JOIN ventas v_entrega ON dv.venta_id = v_entrega.id 
             WHERE v_entrega.id_cliente = c.id 
               AND v_entrega.estado_general = 'activa' 
               AND (dv.cantidad - dv.cantidad_entregada) > 0.01) AS entregas_pendientes
        FROM clientes c
        -- El JOIN debe ir después del FROM y antes del WHERE
        LEFT JOIN clientes_saldos cp ON c.id = cp.cliente_id 
        WHERE c.activo = 1 
          AND (? = 0 OR c.almacen_id = ?) 
        ORDER BY total_ventas DESC, saldo_deuda DESC, nombre ASC";
    
    $stmt = $this->db->prepare($sql);
    // Pasamos el ID dos veces para la lógica del WHERE (? = 0 OR almacen_id = ?)
    $stmt->bind_param("ii", $almacen_id, $almacen_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    /**
     * Detalle específico de folios para el expediente del cliente
     */
    public function obtenerDetalleFinanciero($id_cliente) {
    $sql = "SELECT 
                v.id as venta_id,
                v.folio, 
                v.fecha, 
                v.total, 
                v.estado_pago,
                v.estado_entrega,
                /* Sumamos los abonos que pertenecen a esta venta */
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado,
                /* Calculamos la diferencia en tiempo real */
                (v.total - (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id)) as saldo_folio
            FROM ventas v
            WHERE v.id_cliente = ? 
            AND v.estado_general = 'activa'
            ORDER BY v.fecha DESC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerHistorialPagosCompleto($id_cliente) {
    $sql = "SELECT 
                hp.monto, 
                hp.fecha, 
                v.folio,
                u.nombre as usuario_recibio
            FROM historial_pagos hp
            INNER JOIN ventas v ON hp.venta_id = v.id
            INNER JOIN usuarios u ON hp.usuario_id = u.id
            WHERE v.id_cliente = ?
            ORDER BY hp.fecha DESC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerProductosPendientes($id_cliente) {
        $sql = "SELECT 
                    p.nombre as producto,
                    p.sku,
                    dv.cantidad as cantidad_total,
                    dv.cantidad_entregada,
                    (dv.cantidad - dv.cantidad_entregada) as faltante,
                    v.folio as folio_venta
                FROM detalle_venta dv
                INNER JOIN ventas v ON dv.venta_id = v.id
                INNER JOIN productos p ON dv.producto_id = p.id
                WHERE v.id_cliente = ? 
                AND v.estado_general = 'activa'
                AND (dv.cantidad - dv.cantidad_entregada) > 0.01"; // <--- Filtro de precisión
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
/**
 * Obtiene el expediente 360: Ventas -> Productos (con Lotes y Costos) + Pagos (con Usuario)
 */


/**
 * Función mejorada para obtener datos del cliente (incluye estatus)
 */
public function obtenerDatosBasicos($id) {
    $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
public function registrarAbono($venta_id, $monto, $usuario_id) {
    $this->db->begin_transaction();
    $metodo_pago="Saldo a Favor";
    $referencia="";
    

    try {
        // 1. Insertar el pago
        $stmt = $this->db->prepare("INSERT INTO historial_pagos (venta_id, usuario_id, monto, saldo_favor, metodo_pago, referencia) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiddss", $venta_id, $usuario_id, $monto, $monto, $metodo_pago, $referencia
      );
        $stmt->execute();

        // 2. Verificar si la venta se liquidó para cambiar estatus
        $sqlVerif = "SELECT total, (SELECT SUM(monto) FROM historial_pagos WHERE venta_id = ?) as pagado FROM ventas WHERE id = ?";
        $st = $this->db->prepare($sqlVerif);
        $st->bind_param("ii", $venta_id, $venta_id);
        $st->execute();
        $v = $st->get_result()->fetch_assoc();

        $nuevoEstado = ($v['pagado'] >= $v['total']) ? 'pagada' : 'parcial';
        
        $upd = $this->db->prepare("UPDATE ventas SET estado_pago = ? WHERE id = ?");
        $upd->bind_param("si", $nuevoEstado, $venta_id);
        $upd->execute();

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollback();
        throw $e;
    }
}
/**
 * Obtiene el comparativo mensual de Ventas vs Pagos de los últimos 6 meses
 * Ideal para alimentar gráficas de barras o líneas.
 */
public function obtenerExpedienteCompletoFecha($id_cliente, $fecha_inicio = null, $fecha_fin = null)
{
    // 1. QUERY PRINCIPAL DE VENTAS (con filtros opcionales)
    $sqlVentas = "SELECT v.id as venta_id, v.folio, v.fecha, v.total, v.estado_pago, v.id_cliente as cliente_id, v.estado_general,
                         (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado
                  FROM ventas v
                  WHERE v.id_cliente = ? AND v.estado_general = 'activa'";

    $params = [$id_cliente];
    $types = "i";

    // 🆕 FILTRO FECHA INICIO
    if (!empty($fecha_inicio)) {
        $sqlVentas .= " AND v.fecha >= ?";
        $params[] = $fecha_inicio;
        $types .= "s";
    }

    // 🆕 FILTRO FECHA FIN
    if (!empty($fecha_fin)) {
        $sqlVentas .= " AND v.fecha <= ?";
        $params[] = $fecha_fin;
        $types .= "s";
    }

    $sqlVentas .= " ORDER BY v.fecha DESC";

    $stmtVentas = $this->db->prepare($sqlVentas);
    $stmtVentas->bind_param($types, ...$params);
    $stmtVentas->execute();
    $ventas = $stmtVentas->get_result()->fetch_all(MYSQLI_ASSOC);

    $expediente = [];

    foreach ($ventas as $venta) {

        $v_id = $venta['venta_id'];

        // 2. PRODUCTOS
        $sqlDetalle = "SELECT 
                            IFNULL(lms.cantidad_salida, dv.cantidad) as cantidad, 
                            dv.cantidad_entregada,
                            dv.precio_unitario as precio_venta,
                            p.nombre as producto, 
                            p.sku,
                            IFNULL(ls.codigo_lote, 'S/L') as lote_codigo
                       FROM detalle_venta dv
                       INNER JOIN productos p ON dv.producto_id = p.id
                       LEFT JOIN lotes_movimientos_salida lms ON dv.id = lms.detalle_venta_id
                       LEFT JOIN lotes_stock ls ON lms.lote_id = ls.id
                       WHERE dv.venta_id = ?";

        $stmtDet = $this->db->prepare($sqlDetalle);
        $stmtDet->bind_param("i", $v_id);
        $stmtDet->execute();
        $venta['productos'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);

        // 3. PAGOS
        $sqlPagos = "SELECT 
                        hp.monto, hp.fecha, u.nombre as usuario_recibio, hp.metodo_pago, hp.saldo_favor
                     FROM historial_pagos hp
                     INNER JOIN usuarios u ON hp.usuario_id = u.id
                     WHERE hp.venta_id = ?
                     ORDER BY hp.fecha ASC";

        $stmtPagos = $this->db->prepare($sqlPagos);
        $stmtPagos->bind_param("i", $v_id);
        $stmtPagos->execute();
        $venta['pagos'] = $stmtPagos->get_result()->fetch_all(MYSQLI_ASSOC);

        $expediente[] = $venta;
    }

    return $expediente;
}
public function obtenerExpedienteCompleto($id_cliente) {
    // 1. Obtenemos todas las ventas (Mantenemos tu alias venta_id igual)
    $sqlVentas = "SELECT v.id as venta_id, v.folio, v.fecha, v.total, v.estado_pago, v.id_cliente as cliente_id, v.estado_general,
                         (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado
                  FROM ventas v
                  WHERE v.id_cliente = ? AND v.estado_general = 'activa'
                  ORDER BY v.fecha DESC";
    
    $stmtVentas = $this->db->prepare($sqlVentas);
    $stmtVentas->bind_param("i", $id_cliente);
    $stmtVentas->execute();
    $ventas = $stmtVentas->get_result()->fetch_all(MYSQLI_ASSOC);

    $expediente = [];

    foreach ($ventas as $venta) {
        // --- ESTA ES LA CLAVE ---
        // Como en el SELECT pediste "v.id as venta_id", en el array la llave es 'venta_id'
        $v_id_para_querys = $venta['venta_id']; 

        // 2. Detalle de productos (Usa la variable corregida)
        $sqlDetalle = "SELECT 
                            IFNULL(lms.cantidad_salida, dv.cantidad) as cantidad, 
                            dv.cantidad_entregada,
                            dv.precio_unitario as precio_venta,
                            p.nombre as producto, 
                            p.sku,
                            IFNULL(ls.codigo_lote, 'S/L') as lote_codigo
                       FROM detalle_venta dv
                       INNER JOIN productos p ON dv.producto_id = p.id
                       LEFT JOIN lotes_movimientos_salida lms ON dv.id = lms.detalle_venta_id
                       LEFT JOIN lotes_stock ls ON lms.lote_id = ls.id
                       WHERE dv.venta_id = ?";
        
        $stmtDet = $this->db->prepare($sqlDetalle);
        $stmtDet->bind_param("i", $v_id_para_querys); // <--- Aquí ya no es null
        $stmtDet->execute();
        $venta['productos'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);

        // 3. Detalle de pagos (Usa la variable corregida)
        $sqlPagos = "SELECT 
                        hp.monto, hp.fecha, u.nombre as usuario_recibio, hp.metodo_pago,hp.saldo_favor
                     FROM historial_pagos hp
                     INNER JOIN usuarios u ON hp.usuario_id = u.id
                     WHERE hp.venta_id = ?
                     ORDER BY hp.fecha ASC";
        
        $stmtPagos = $this->db->prepare($sqlPagos);
        $stmtPagos->bind_param("i", $v_id_para_querys); // <--- Aquí ya no es null
        $stmtPagos->execute();
        $venta['pagos'] = $stmtPagos->get_result()->fetch_all(MYSQLI_ASSOC);

        $expediente[] = $venta;
    }

    return $expediente;
}
public function obtenerEstadisticasMensuales($id_cliente) {
    $sql = "SELECT 
                MESES.mes_nombre,
                IFNULL(SUM(V.total), 0) AS total_ventas,
                IFNULL((
                    SELECT SUM(hp.monto) 
                    FROM historial_pagos hp 
                    INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                    WHERE v2.id_cliente = ? 
                    AND MONTH(hp.fecha) = MESES.mes_num
                    AND YEAR(hp.fecha) = YEAR(CURDATE())
                ), 0) AS total_pagos
            FROM (
                SELECT 1 AS mes_num, 'Ene' AS mes_nombre UNION SELECT 2, 'Feb' UNION 
                SELECT 3, 'Mar' UNION SELECT 4, 'Abr' UNION SELECT 5, 'May' UNION 
                SELECT 6, 'Jun' UNION SELECT 7, 'Jul' UNION SELECT 8, 'Ago' UNION 
                SELECT 9, 'Sep' UNION SELECT 10, 'Oct' UNION SELECT 11, 'Nov' UNION 
                SELECT 12, 'Dic'
            ) AS MESES
            LEFT JOIN ventas V ON MONTH(V.fecha) = MESES.mes_num 
                AND V.id_cliente = ? 
                AND V.estado_general = 'activa'
                AND YEAR(V.fecha) = YEAR(CURDATE())
            GROUP BY MESES.mes_num
            ORDER BY MESES.mes_num ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $id_cliente, $id_cliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
}