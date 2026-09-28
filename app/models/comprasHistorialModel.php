<?php

class HistorialComprasModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // =====================================================
    // 🔥 TOTALES LOTES
    // =====================================================
    public function obtenerTotalesLotes($producto_id, $almacen_id, $fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT 
                    IFNULL(SUM(cantidad_inicial), 0) AS total_cantidad_inicial,
                    IFNULL(SUM(cantidad_actual), 0) AS total_cantidad_actual
                FROM lotes_stock
                WHERE producto_id = ?
                AND almacen_id = ?
                AND fecha_ingreso BETWEEN ? AND ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiss", $producto_id, $almacen_id, $fecha_inicio, $fecha_fin);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // =====================================================
    // 🔥 LISTADO DE LOTES (CON FECHA)
    // =====================================================
    public function obtenerVentasCompra($compra_id) {

    $sql = "SELECT 
        tipo_movimiento,
        almacen_id,
        producto_id,
        nombre,
        producto,
        documento,
        cliente_proveedor,
        codigo_lote,
        fecha_lote,
        fecha_movimiento,
        cantidad_inicial,

       -- 1. Cantidad disponible ANTES del movimiento actual (Filas anteriores del mismo lote)
        cantidad_inicial - COALESCE(SUM(cantidad_salida) OVER (
            PARTITION BY codigo_lote -- <--- REINICIA EL CÁLCULO POR CADA LOTE
            ORDER BY fecha_movimiento, movimiento_id
            ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING
        ), 0) AS cantidad_actual,

        -- 2. La salida física de este registro
        cantidad_salida,

        -- 3. Saldo remanente DESPUÉS del movimiento actual (Incluye la fila presente)
        cantidad_inicial - SUM(cantidad_salida) OVER (
            PARTITION BY codigo_lote -- <--- REINICIA EL CÁLCULO POR CADA LOTE
            ORDER BY fecha_movimiento, movimiento_id
        ) AS saldo_final,

        costo_unitario,
        precio_venta,
        ganancia,
        referencia_extra,
        almacen_destino,
        lote_destino_traspaso,
        unidad_medida,
        unidad_reporte,
        factor,
        subtotal

    FROM (

        -- VENTAS
        SELECT 
            'VENTA' AS tipo_movimiento,
            a.nombre,
            a.id as almacen_id,
            pro.nombre AS producto,
            pro.id as producto_id,
            v.folio AS documento,
            c.nombre_comercial AS cliente_proveedor,
            lt.codigo_lote,
            lt.fecha_ingreso AS fecha_lote,
            lms.id AS movimiento_id,
            lms.fecha_movimiento,
            lt.cantidad_inicial,
            lms.cantidad_salida,
            lms.costo_compra_historico AS costo_unitario,
            lms.precio_venta_pactado AS precio_venta,
            (lms.precio_venta_pactado - lms.costo_compra_historico) * lms.cantidad_salida AS ganancia,
            '-' AS referencia_extra,
            '-' as  almacen_destino,
            '-' as lote_destino_traspaso,
            pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            dv.subtotal as subtotal


        FROM lotes_movimientos_salida lms
        INNER JOIN lotes_stock lt ON lt.id = lms.lote_id
        INNER JOIN detalle_venta dv ON dv.id = lms.detalle_venta_id
        INNER JOIN ventas v ON v.id = dv.venta_id
        INNER JOIN clientes c ON c.id = v.id_cliente
        INNER JOIN lotes_ingresos_detalle lid ON lid.lote_id = lt.id
        INNER JOIN detalle_compra dc ON dc.id = lid.detalle_compra_id
        INNER JOIN almacenes a ON a.id = v.almacen_id
        INNER JOIN productos pro ON pro.id = dc.producto_id
        WHERE dc.compra_id = ?

        UNION ALL

        -- TRASPASOS
        SELECT 
            'TRASPASO',
            a.nombre,
             a.id as almacen_id,
            pro.nombre,
             pro.id as producto_id,
            COALESCE(m.referencia_id,'-'),
            a2.nombre,
            lt.codigo_lote,
            lt.fecha_ingreso,
            m.id,
            m.fecha,
            lt.cantidad_inicial,
            km.cantidad,
            lt.precio_compra_unitario as costo_unitario,
            0,
            0,
           km.lote_destino_id,
           a2.id as  almacen_destino,
           lt2.codigo_lote as lote_destino_traspaso,
           pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            0


        FROM movimientos m
        JOIN kardex_movimientos_lotes km ON km.movimiento_id = m.id
        JOIN lotes_stock lt ON km.lote_origen_id = lt.id
        join lotes_stock lt2 on km.lote_destino_id=lt2.id
        join almacenes a2 on a2.id= lt2.almacen_id
        INNER JOIN lotes_ingresos_detalle lid ON lid.lote_id = lt.id
        INNER JOIN detalle_compra dc ON dc.id = lid.detalle_compra_id
        INNER JOIN productos pro ON pro.id = dc.producto_id
        INNER JOIN almacenes a ON a.id = m.almacen_origen_id
        WHERE dc.compra_id = ?

        UNION ALL

        -- AJUSTES
        SELECT 
            'AJUSTE',
            a.nombre,
             a.id as almacen_id,
            pro.nombre,
             pro.id as producto_id,
            '-',
            '-',
            lt.codigo_lote,
            lt.fecha_ingreso,
            m.id,
            m.fecha,
            lt.cantidad_inicial,
            m.cantidad,
            0,
            0,
            0,
            COALESCE(m.observaciones, '-'),
            0,
            0,
            pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            0


        FROM movimientos m
        JOIN transmutacion_detalle td ON m.id = td.movimiento_id
        JOIN lotes_stock lt ON td.lote_id = lt.id
        INNER JOIN lotes_ingresos_detalle lid ON lid.lote_id = lt.id
        INNER JOIN detalle_compra dc ON dc.id = lid.detalle_compra_id
        INNER JOIN productos pro ON pro.id = dc.producto_id
        INNER JOIN almacenes a ON a.id = m.almacen_origen_id
        WHERE dc.compra_id = ?

        UNION ALL

        -- MERMAS
        SELECT 
            'MERMA',
            a.nombre,
             a.id as almacen_id,
            pro.nombre,
             pro.id as producto_id,
            '-',
            '-',
            lt.codigo_lote,
            lt.fecha_ingreso,
            m.id,
            m.fecha,
            lt.cantidad_inicial,
            m.cantidad,
            0,
            0,
            0,
            COALESCE(m.observaciones, '-'),
            0,
            0,
            pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            0


        FROM movimientos m
        JOIN mermas merma ON m.id = merma.movimiento_id
        JOIN lotes_stock lt ON merma.lote_id = lt.id
        INNER JOIN lotes_ingresos_detalle lid ON lid.lote_id = lt.id
        INNER JOIN detalle_compra dc ON dc.id = lid.detalle_compra_id
        INNER JOIN productos pro ON pro.id = dc.producto_id
        INNER JOIN almacenes a ON a.id = m.almacen_origen_id
        WHERE dc.compra_id = ?

    ) AS t

    ORDER BY nombre, fecha_movimiento, movimiento_id";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception($this->db->error);
    }

    $stmt->bind_param(
        "iiii",
        $compra_id,
        $compra_id,
        $compra_id,
        $compra_id
    );

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerDistribucionCompra($compra_id, $producto_id) {

    $sql = "SELECT 
                a.id AS almacen_id,
                a.nombre AS almacen,
                lt.id AS lote_id,
                lt.codigo_lote,
                lt.id as lote_id,
                lt.cantidad_inicial,
                p.nombre AS producto,
                p.factor_conversion,
                p.unidad_medida,
                p.unidad_reporte,
                lt.fecha_ingreso,
                lt.precio_compra_unitario as costo_unitario,
                pro.nombre_comercial

            FROM lotes_ingresos_detalle lid

            INNER JOIN detalle_compra dc 
                ON lid.detalle_compra_id = dc.id
                inner join compras c on dc.compra_id=c.id
                inner join proveedores pro on pro.id=c.proveedor

            INNER JOIN lotes_stock lt 
                ON lt.id = lid.lote_id

            INNER JOIN almacenes a 
                ON a.id = lt.almacen_id

            INNER JOIN productos p
                ON p.id = dc.producto_id

            WHERE dc.compra_id = ?
              AND dc.producto_id = ?

            ORDER BY a.nombre, lt.id";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception($this->db->error);
    }

    $stmt->bind_param("ii", $compra_id, $producto_id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

       public function obtenerLotes($almacen_id, $fecha_inicio, $fecha_fin) {

    $sql = "SELECT 
    c.id,
    c.folio,
    c.fecha_compra,
    c.total,
    c.estado,
    dc.cantidad_faltante as faltante,
    dc.cantidad_excedente sobrante,
    a.nombre AS almacen,
    pro.nombre_comercial AS proveedor,
    prod.id as producto_id,
    prod.factor_conversion,
    prod.unidad_medida,
    prod.unidad_reporte,

   
        prod.nombre  AS productos,
        dc.cantidad as cantidadProd 

FROM compras c
JOIN detalle_compra dc ON dc.compra_id = c.id
JOIN almacenes a ON a.id = c.almacen_id
JOIN proveedores pro ON pro.id = c.proveedor
JOIN productos prod ON prod.id = dc.producto_id

    WHERE c.fecha_compra BETWEEN ? AND ?";

    // Solo filtrar almacén si es distinto de 0
    if ($almacen_id != 0) {
        $sql .= " AND c.almacen_id = ?";
    }

    $sql .= " GROUP BY 
        c.id,
        c.folio,
        a.nombre,
        pro.nombre_comercial,
        prod.factor_conversion,
        prod.unidad_medida,
        prod.unidad_reporte

    ORDER BY c.id DESC";

    $stmt = $this->db->prepare($sql);

    if ($almacen_id != 0) {
        $stmt->bind_param("ssi", $fecha_inicio, $fecha_fin, $almacen_id);
    } else {
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    // =====================================================
    // 🧾 VENTAS POR LOTE (SIN CAMBIO)
    // =====================================================
   public function obtenerVentasLote($lote_id) {

   $sql = "SELECT 
        tipo_movimiento,
        almacen_id,
        producto_id,
        nombre,
        producto,
        documento,
        cliente_proveedor,
        codigo_lote,
        fecha_lote,
        fecha_movimiento,
        cantidad_inicial,

        -- 1. Cantidad disponible ANTES del movimiento actual
        cantidad_inicial - COALESCE(SUM(cantidad_salida) OVER (
            PARTITION BY codigo_lote 
            ORDER BY fecha_movimiento, orden_registro -- <--- Usamos un criterio de orden único y limpio
            ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING
        ), 0) AS cantidad_actual,

        -- 2. La salida física de este registro
        cantidad_salida,

        -- 3. Saldo remanente DESPUÉS del movimiento actual
        cantidad_inicial - SUM(cantidad_salida) OVER (
            PARTITION BY codigo_lote 
            ORDER BY fecha_movimiento, orden_registro
        ) AS saldo_final,

        costo_unitario,
        precio_venta,
        ganancia,
        referencia_extra,
        almacen_destino,
        lote_destino_traspaso,
        unidad_medida,
        unidad_reporte,
        factor,
        subtotal

    FROM (

        -- VENTAS
        SELECT 
            'VENTA' AS tipo_movimiento,
            a.nombre,
            a.id as almacen_id,
            pro.nombre AS producto,
            pro.id as producto_id,
            v.folio AS documento,
            c.nombre_comercial AS cliente_proveedor,
            lt.codigo_lote,
            lt.fecha_ingreso AS fecha_lote,
            lms.id AS orden_registro, -- ID único para ordenar este bloque
            lms.fecha_movimiento,
            lt.cantidad_inicial,
            lms.cantidad_salida,
            lms.costo_compra_historico AS costo_unitario,
            lms.precio_venta_pactado AS precio_venta,
            (lms.precio_venta_pactado - lms.costo_compra_historico) * lms.cantidad_salida AS ganancia,
            '-' AS referencia_extra,
            '-' as almacen_destino,
            '-' as lote_destino_traspaso,
            pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            dv.subtotal as subtotal
        FROM lotes_movimientos_salida lms
        INNER JOIN lotes_stock lt ON lt.id = lms.lote_id
        INNER JOIN detalle_venta dv ON dv.id = lms.detalle_venta_id
        INNER JOIN ventas v ON v.id = dv.venta_id
        INNER JOIN clientes c ON c.id = v.id_cliente
        INNER JOIN almacenes a ON a.id = v.almacen_id
        INNER JOIN productos pro ON pro.id = lt.producto_id -- CORRECCIÓN: Directo desde el lote para evitar duplicados de compra
        WHERE lt.id = ?

        UNION ALL

        -- TRASPASOS
        SELECT 
            'TRASPASO',
            a.nombre,
            a.id as almacen_id,
            pro.nombre,
            pro.id as producto_id,
            COALESCE(m.referencia_id,'-'),
            a2.nombre,
            lt.codigo_lote,
            lt.fecha_ingreso,
            m.id AS orden_registro,
            m.fecha,
            lt.cantidad_inicial,
            km.cantidad,
            lt.precio_compra_unitario as costo_unitario,
            0,
            0,
            km.lote_destino_id,
            a2.id as almacen_destino,
            lt2.codigo_lote as lote_destino_traspaso,
            pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            0
        FROM movimientos m
        JOIN kardex_movimientos_lotes km ON km.movimiento_id = m.id
        JOIN lotes_stock lt ON km.lote_origen_id = lt.id
        JOIN lotes_stock lt2 on km.lote_destino_id = lt2.id
        JOIN almacenes a2 on a2.id = lt2.almacen_id
        INNER JOIN productos pro ON pro.id = lt.producto_id -- CORRECCIÓN: Directo desde el lote
        INNER JOIN almacenes a ON a.id = m.almacen_origen_id
        WHERE lt.id = ?

        UNION ALL

        -- AJUSTES
        SELECT 
            'AJUSTE',
            a.nombre,
            a.id as almacen_id,
            pro.nombre,
            pro.id as producto_id,
            '-',
            '-',
            lt.codigo_lote,
            lt.fecha_ingreso,
            m.id AS orden_registro,
            m.fecha,
            lt.cantidad_inicial,
            m.cantidad,
            0,
            0,
            0,
            COALESCE(m.observaciones, '-'),
            0,
            0,
            pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            0
        FROM movimientos m
        JOIN transmutacion_detalle td ON m.id = td.movimiento_id
        JOIN lotes_stock lt ON td.lote_id = lt.id
        INNER JOIN productos pro ON pro.id = lt.producto_id -- CORRECCIÓN: Directo desde el lote
        INNER JOIN almacenes a ON a.id = m.almacen_origen_id
        WHERE lt.id = ?

        UNION ALL

        -- MERMAS
        SELECT 
            'MERMA',
            a.nombre,
            a.id as almacen_id,
            pro.nombre,
            pro.id as producto_id,
            '-',
            '-',
            lt.codigo_lote,
            lt.fecha_ingreso,
            m.id AS orden_registro,
            m.fecha,
            lt.cantidad_inicial,
            m.cantidad,
            0,
            0,
            0,
            COALESCE(m.observaciones, '-'),
            0,
            0,
            pro.unidad_medida,
            pro.unidad_reporte,
            pro.factor_conversion as factor,
            0
        FROM movimientos m
        JOIN mermas merma ON m.id = merma.movimiento_id
        JOIN lotes_stock lt ON merma.lote_id = lt.id
        INNER JOIN productos pro ON pro.id = lt.producto_id -- CORRECCIÓN: Directo desde el lote
        INNER JOIN almacenes a ON a.id = m.almacen_origen_id
        WHERE lt.id = ?

    ) AS t

    -- CORRECCIÓN: El ordenamiento final debe ser estrictamente por fecha para no quebrar los saldos acumulados
    ORDER BY fecha_movimiento, orden_registro";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception($this->db->error);
    }

    $stmt->bind_param("iiii", $lote_id, $lote_id, $lote_id, $lote_id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // 📊 CONSUMO POR PRODUCTO (CON FECHAS CORREGIDO)
    // =====================================================
  public function obtenerConsumoLotesPorProducto($producto_id, $almacen_id = 0, $fecha_inicio = null, $fecha_fin = null) {
    try {

        date_default_timezone_set('America/Mexico_City');

        if (empty($fecha_inicio)) $fecha_inicio = date('Y-m-d');
        if (empty($fecha_fin)) $fecha_fin = date('Y-m-d');

        $f_inicio_full = $fecha_inicio . " 00:00:00";
        $f_fin_full    = $fecha_fin . " 23:59:59";

        $sql = "SELECT 
            v.id AS venta_id,
            v.folio,
            c.nombre_comercial AS cliente,

            lt.id AS lote_id,
            lt.codigo_lote,
            lt.fecha_ingreso AS fecha_de_ingreso,

            lms.id AS movimiento_id,
            lms.fecha_movimiento,

            -- 🔥 1. CANTIDAD INICIAL
            lt.cantidad_inicial AS cantidad_inicial,

            -- 🔥 2. SALDO ANTES DEL MOVIMIENTO
            lt.cantidad_inicial - COALESCE((
                SELECT SUM(lms2.cantidad_salida)
                FROM lotes_movimientos_salida lms2
                WHERE lms2.lote_id = lms.lote_id
                AND lms2.id < lms.id
            ),0) AS cantidad_actual,

            -- 🔥 3. SALIDA DEL MOVIMIENTO
            lms.cantidad_salida,

            -- 🔥 4. SALDO FINAL
            lt.cantidad_inicial - COALESCE((
                SELECT SUM(lms2.cantidad_salida)
                FROM lotes_movimientos_salida lms2
                WHERE lms2.lote_id = lms.lote_id
                AND lms2.id <= lms.id
            ),0) AS saldo_final

        FROM lotes_movimientos_salida lms

        INNER JOIN lotes_stock lt 
            ON lt.id = lms.lote_id

        INNER JOIN detalle_venta dv 
            ON dv.id = lms.detalle_venta_id

        INNER JOIN ventas v 
            ON v.id = dv.venta_id

        INNER JOIN clientes c 
            ON c.id = v.id_cliente

        WHERE dv.producto_id = ?
        AND lms.fecha_movimiento BETWEEN ? AND ?";

        // 🔥 filtro opcional de almacén
        if ($almacen_id != 0) {
            $sql .= " AND lt.almacen_id = ?";
        }

        $sql .= " ORDER BY lt.id ASC, lms.id ASC";

        $stmt = $this->db->prepare($sql);

        if ($almacen_id != 0) {
            $stmt->bind_param(
                "issi",
                $producto_id,
                $f_inicio_full,
                $f_fin_full,
                $almacen_id
            );
        } else {
            $stmt->bind_param(
                "iss",
                $producto_id,
                $f_inicio_full,
                $f_fin_full
            );
        }

        $stmt->execute();

        return [
            'success' => true,
            'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)
        ];

    } catch (Throwable $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
    // =====================================================
    // 🔁 TRASPASOS (CORREGIDO SQL)
    // =====================================================
 public function obtenerTraspasos($lote_id, $fecha_inicio = null, $fecha_fin = null) {

    $sql = "SELECT        
        m.id AS movimiento_id,

        lt.codigo_lote AS codigo_lote_origen,
        ltd.codigo_lote AS codigo_lote_destino,

        km.lote_origen_id,
        km.lote_destino_id,

        m.fecha,
        m.tipo,
        m.producto_id,
        m.cantidad,
        m.almacen_origen_id,
        a.nombre as nombreOrigen,
        a2.nombre as nombreDestino,
        m.almacen_destino_id,
        m.referencia_id,
        m.observaciones

    FROM movimientos m

    JOIN kardex_movimientos_lotes km 
        ON m.id = km.movimiento_id

    JOIN almacenes a 
        ON m.almacen_origen_id = a.id

    JOIN almacenes a2 
        ON m.almacen_destino_id = a2.id

    JOIN lotes_stock lt 
        ON km.lote_origen_id = lt.id

    LEFT JOIN lotes_stock ltd 
        ON km.lote_destino_id = ltd.id

    WHERE km.lote_origen_id = ?
    AND m.tipo = 'traspaso'";

    // 🔥 FECHAS
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $sql .= " AND DATE(m.fecha) BETWEEN ? AND ?";
    }

    $sql .= " ORDER BY m.fecha ASC";

    $stmt = $this->db->prepare($sql);

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $stmt->bind_param("iss", $lote_id, $fecha_inicio, $fecha_fin);
    } else {
        $stmt->bind_param("i", $lote_id);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // ⚙️ AJUSTES
    // =====================================================
   public function obtenerAjustes($producto_id, $almacen_id, $fecha_inicio = null, $fecha_fin = null) {

    $sql = "SELECT 
        'AJUSTE' AS tipo_movimiento,
        m.id,
        m.fecha,
        m.producto_id,
        m.cantidad,
        m.observaciones

    FROM movimientos m

    WHERE m.producto_id = ?
  AND m.observaciones LIKE '%Salida Transmutación%'
    AND m.almacen_origen_id = ?";

    // 🔥 filtro opcional por fecha
    if ($fecha_inicio && $fecha_fin) {
        $sql .= " AND m.fecha BETWEEN ? AND ?";
    }

    $sql .= " ORDER BY m.fecha ASC";

    $stmt = $this->db->prepare($sql);

    // 🔥 binding dinámico
    if ($fecha_inicio && $fecha_fin) {
        $stmt->bind_param("iiss", $producto_id, $almacen_id, $fecha_inicio, $fecha_fin);
    } else {
        $stmt->bind_param("ii", $producto_id, $almacen_id);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // 📦 ENTRADAS
    // =====================================================
    public function obtenerEntradasLote($lote_id, $fecha_inicio = null, $fecha_fin = null) {

    $sql = "SELECT 
        'ENTRADA' AS tipo_movimiento,
        li.lote_id,
        li.fecha_registro,
        li.cantidad_recibida,
        li.costo_aplicado,
        dc.compra_id

    FROM lotes_ingresos_detalle li

    INNER JOIN detalle_compra dc 
        ON dc.id = li.detalle_compra_id

    WHERE li.lote_id = ?";

    // 🔥 filtro opcional por fecha
    if ($fecha_inicio && $fecha_fin) {
        $sql .= " AND li.fecha_registro BETWEEN ? AND ?";
    }

    $sql .= " ORDER BY li.fecha_registro ASC";

    $stmt = $this->db->prepare($sql);

    // 🔥 binding dinámico
    if ($fecha_inicio && $fecha_fin) {
        $stmt->bind_param("iss", $lote_id, $fecha_inicio, $fecha_fin);
    } else {
        $stmt->bind_param("i", $lote_id);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // 🔥 HISTORIAL COMPLETO
    // =====================================================
    public function obtenerHistorialCompleto($producto_id, $almacen_id, $lote_id) {

        return [
            'ventas'    => $this->obtenerVentasLote($lote_id),
            'traspasos' => $this->obtenerTraspasos($producto_id, $almacen_id),
            'ajustes'   => $this->obtenerAjustes($producto_id, $almacen_id),
            'entradas'  => $this->obtenerEntradasLote($lote_id)
        ];
    }
}