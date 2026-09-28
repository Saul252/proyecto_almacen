<?php
class CorteCajaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }



    // FUNCIÓN 1: TABLA DETALLADA
public function obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $almacen_id) {
    date_default_timezone_set('America/Mexico_City');
    $target = intval($almacen_id);
    $inicio = $f_inicio;
    $fin = $f_fin;

    if ($periodo !== 'personalizado') {
        $hoy = date('Y-m-d');
        switch ($periodo) {
            case 'ayer': $inicio = $fin = date('Y-m-d', strtotime('-1 day')); break;
            case 'semana': $inicio = date('Y-m-d', strtotime('-7 days')); $fin = $hoy; break;
            case 'mes': $inicio = date('Y-m-01'); $fin = $hoy; break;
            default: $inicio = $fin = $hoy; break;
        }
    }

    $filtroAlmacen  = ($target > 0) ? " AND v.almacen_id = $target " : "";
    $filtroAlmacen2 = ($target > 0) ? " AND v.almacen_id = $target " : "";

    $sql = "SELECT * FROM (

        -- PARTE A: Ventas del día (agrupadas por venta, productos en JSON)
        SELECT 
            v.id,
            v.folio,
            a.nombre AS almacen,
            v.fecha AS fecha_movimiento,
            c.nombre_comercial AS cliente,
            u_vende.nombre AS vendedor,
            -- Productos agrupados en JSON
            CONCAT('[', GROUP_CONCAT(
                JSON_OBJECT(
                    'producto', p.nombre,
                    'cantidad', CONCAT(
                        FLOOR(dv.cantidad / IF(p.factor_conversion > 0, p.factor_conversion, 1)),
                        ' ', IFNULL(p.unidad_reporte, 'Unid.'),
                        IF(MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)) > 0,
                           CONCAT(' + ', MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)), ' pzas'), '')
                    )
                ) SEPARATOR ','
            ), ']') AS productos,
            (SELECT u2.nombre FROM historial_pagos hp2 
             JOIN usuarios u2 ON hp2.usuario_id = u2.id 
             WHERE hp2.venta_id = v.id ORDER BY hp2.id DESC LIMIT 1) AS quien_recibio,
            (SELECT GROUP_CONCAT(DISTINCT hp.metodo_pago SEPARATOR ' + ') 
             FROM historial_pagos hp WHERE hp.venta_id = v.id) AS metodo_pago,
            (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id AND hp.metodo_pago = 'Efectivo') AS efectivo,
            (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id AND hp.metodo_pago IN ('Tarjeta', 'Transferencia')) AS tarjeta_transferencia,
            (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id) AS dinero_real,
            (SELECT IFNULL(SUM(hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id) AS uso_saldo_favor,
            0 AS monto_abono,
            GREATEST(0, v.total - (SELECT IFNULL(SUM(hp.monto), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id)) AS deuda_viva,
            'VENTA DÍA' AS tipo
        FROM ventas v
        INNER JOIN detalle_venta dv ON v.id = dv.venta_id
        INNER JOIN productos p ON dv.producto_id = p.id
        INNER JOIN usuarios u_vende ON v.usuario_id = u_vende.id
        INNER JOIN almacenes a ON v.almacen_id = a.id
        INNER JOIN clientes c ON v.id_cliente = c.id
        WHERE DATE(v.fecha) BETWEEN '$inicio' AND '$fin'
          AND v.estado_general = 'activa'
          $filtroAlmacen
        GROUP BY v.id, v.folio, a.nombre, v.fecha, c.nombre_comercial, u_vende.nombre

        UNION ALL

        -- PARTE B: Abonos de deudas anteriores (agrupados por venta + pago)
        SELECT 
            v.id,
            v.folio,
            a.nombre AS almacen,
            hp_pago.fecha AS fecha_movimiento,
            c.nombre_comercial AS cliente,
            u_vende.nombre AS vendedor,
            -- Productos agrupados en JSON
            CONCAT('[', GROUP_CONCAT(
                JSON_OBJECT(
                    'producto', p.nombre,
                    'cantidad', CONCAT(
                        FLOOR(dv.cantidad / IF(p.factor_conversion > 0, p.factor_conversion, 1)),
                        ' ', IFNULL(p.unidad_reporte, 'Unid.'),
                        IF(MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)) > 0,
                           CONCAT(' + ', MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)), ' pzas'), '')
                    )
                ) SEPARATOR ','
            ), ']') AS productos,
            u_cajero.nombre AS quien_recibio,
            hp_pago.metodo_pago,
            IF(hp_pago.metodo_pago = 'Efectivo', (hp_pago.monto - hp_pago.saldo_favor), 0) AS efectivo,
            IF(hp_pago.metodo_pago IN ('Tarjeta', 'Transferencia'), (hp_pago.monto - hp_pago.saldo_favor), 0) AS tarjeta_transferencia,
            (hp_pago.monto - hp_pago.saldo_favor) AS dinero_real,
            hp_pago.saldo_favor AS uso_saldo_favor,
            hp_pago.monto AS monto_abono,
            GREATEST(0, v.total - (SELECT IFNULL(SUM(monto), 0) 
             FROM historial_pagos WHERE venta_id = v.id)) AS deuda_viva,
            'ABONO DEUDA' AS tipo
        FROM historial_pagos hp_pago
        INNER JOIN ventas v ON hp_pago.venta_id = v.id
            AND v.estado_general = 'activa'
        INNER JOIN detalle_venta dv ON v.id = dv.venta_id
        INNER JOIN productos p ON dv.producto_id = p.id
        INNER JOIN almacenes a ON v.almacen_id = a.id
        INNER JOIN clientes c ON v.id_cliente = c.id
        INNER JOIN usuarios u_cajero ON hp_pago.usuario_id = u_cajero.id
        INNER JOIN usuarios u_vende ON v.usuario_id = u_vende.id
        WHERE DATE(hp_pago.fecha) BETWEEN '$inicio' AND '$fin'
          AND DATE(v.fecha) < '$inicio'
          $filtroAlmacen2
        GROUP BY v.id, v.folio, a.nombre, hp_pago.fecha, c.nombre_comercial, 
                 u_vende.nombre, u_cajero.nombre, hp_pago.metodo_pago,
                 hp_pago.monto, hp_pago.saldo_favor, v.total

    ) AS reporte_final ORDER BY fecha_movimiento DESC";

    $res = $this->db->query($sql);
    $data = [];
    while ($res && $row = $res->fetch_assoc()) {
        // Decodificar el JSON de productos
        $row['productos'] = json_decode($row['productos'], true) ?? [];
        $data[] = $row;
    }
    return $data;
}
    // FUNCIÓN 2: SUMAS TOTALES
public function obtenerSumasCorte($periodo, $f_inicio, $f_fin, $almacen_id)
{
    date_default_timezone_set('America/Mexico_City');
    $target = intval($almacen_id);
    $hoy    = date('Y-m-d');

    // --- 1. CONFIGURACIÓN DE TIEMPO ---
    $inicio = $hoy . ' 00:00:00';
    $fin    = $hoy . ' 23:59:59';

    switch ($periodo) {
        case 'personalizado':
            $inicio = date('Y-m-d', strtotime($f_inicio)) . ' 00:00:00';
            $fin    = date('Y-m-d', strtotime($f_fin))    . ' 23:59:59';
            break;
        case 'ayer':
            $inicio = date('Y-m-d', strtotime('-1 day')) . ' 00:00:00';
            $fin    = date('Y-m-d', strtotime('-1 day')) . ' 23:59:59';
            break;
        case 'semana':
            $inicio = date('Y-m-d', strtotime('-7 days')) . ' 00:00:00';
            $fin    = $hoy . ' 23:59:59';
            break;
        case 'mes':
            $inicio = date('Y-m-01') . ' 00:00:00';
            $fin    = $hoy . ' 23:59:59';
            break;
        default:
            $inicio = $hoy . ' 00:00:00';
            $fin    = $hoy . ' 23:59:59';
            break;
    }

    $filtroV  = ($target > 0) ? " AND v.almacen_id  = $target" : "";
    $filtroHP = ($target > 0) ? " AND v2.almacen_id = $target" : "";
    $filtroG  = ($target > 0) ? " AND almacen_id    = $target" : "";

    $sql = "SELECT 
                base.*,
                (base.venta_bruta_periodo - base.pagos_realizados_de_ventas_periodo) AS deuda_pendiente,
                (base.ingreso_total_efectivo + base.ingreso_total_tarjeta + base.ingreso_total_transferencia) AS gran_total_ingresos
            FROM (
                SELECT
                    -- 1. SUMA TOTAL DE VENTAS (Lo que se vendió, paguen o deban)
                    (SELECT IFNULL(SUM(total), 0) FROM ventas v 
                     WHERE v.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v.estado_general = 'activa' $filtroV) AS venta_bruta_periodo,

                    -- Subconsulta Crítica: ¿Cuánto se ha pagado (dinero + saldo favor) de las ventas de ESTE periodo?
                    (SELECT IFNULL(SUM(hp.monto), 0) FROM historial_pagos hp 
                     INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE v2.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' $filtroHP) AS pagos_realizados_de_ventas_periodo,

                    -- 2. INGRESO TOTAL (Ventas de hoy + Abonos de deudas anteriores)
                    (SELECT IFNULL(SUM(monto - saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Efectivo' $filtroHP) AS ingreso_total_efectivo,

                    (SELECT IFNULL(SUM(monto - saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Tarjeta' $filtroHP) AS ingreso_total_tarjeta,

                    (SELECT IFNULL(SUM(monto - saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Transferencia' $filtroHP) AS ingreso_total_transferencia,

                    -- 3. SOLO VENTAS DEL DÍA (Dinero que entró de ventas creadas hoy)
                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Efectivo' $filtroHP) AS solo_venta_efectivo,

                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Tarjeta' $filtroHP) AS solo_venta_tarjeta,

                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Transferencia' $filtroHP) AS solo_venta_transferencia,

                    -- 4. SOLO ABONOS (Dinero de ventas anteriores que pagaron hoy)
                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha < '$inicio' 
                       AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Efectivo' $filtroHP) AS abono_efectivo,

                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha < '$inicio' 
                       AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Tarjeta' $filtroHP) AS abono_tarjeta,

                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha < '$inicio' 
                       AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Transferencia' $filtroHP) AS abono_transferencia,

                    -- 5. SALDO A FAVOR (Aparte)
                    (SELECT IFNULL(SUM(saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.estado_general = 'activa' $filtroHP) AS saldo_favor_usado,

                    -- Extras
                    (SELECT IFNULL(SUM(total), 0) FROM gastos 
                     WHERE fecha_registro BETWEEN '$inicio' AND '$fin' AND estado = 'pagado' $filtroG) AS gastos_totales,

                    (SELECT IFNULL(SUM(total), 0) FROM compras 
                     WHERE fecha_registro BETWEEN '$inicio' AND '$fin' AND estado = 'confirmada' $filtroG) AS compras_totales
            ) AS base";

    $res = $this->db->query($sql);
    $row = $res->fetch_assoc();

    return [
        'venta_bruta'          => (float)$row['venta_bruta_periodo'],
        'ingreso_total_efectivo' => (float)$row['ingreso_total_efectivo'],
        'ingreso_total_tarjeta'  => (float)$row['ingreso_total_tarjeta'],
        'ingreso_total_transfer' => (float)$row['ingreso_total_transferencia'],
        'gran_total_ingresos'    => (float)$row['gran_total_ingresos'],
        'solo_venta_efectivo'    => (float)$row['solo_venta_efectivo'],
        'solo_venta_tarjeta'     => (float)$row['solo_venta_tarjeta'],
        'solo_venta_transfer'    => (float)$row['solo_venta_transferencia'],
        'abono_efectivo'         => (float)$row['abono_efectivo'],
        'abono_tarjeta'          => (float)$row['abono_tarjeta'],
        'abono_transferencia'    => (float)$row['abono_transferencia'],
        'saldo_favor_usado'      => (float)$row['saldo_favor_usado'],
        'deuda_pendiente'        => (float)$row['deuda_pendiente'], // Ya regresa con el valor correcto
        'gastos_totales'         => (float)$row['gastos_totales'],
        'compras_totales'        => (float)$row['compras_totales'],
        'diadehoy'               => $hoy,
    ];
}

public function obtenerSumasCorteCaja($filtros, $almacen_id_target) {
    date_default_timezone_set('America/Mexico_City');
    $target = intval($almacen_id_target);
    $ahora = date('Y-m-d H:i:s');

    // --- 1. CONFIGURACIÓN DE TIEMPO ---
    $periodo = $filtros['periodo'] ?? 'hoy';
    $inicioFiltro = date('Y-m-d 00:00:00'); 
    $fin = $ahora;

    if ($periodo !== 'personalizado') {
        switch ($periodo) {
            case 'ayer': 
                $inicioFiltro = date('Y-m-d 00:00:00', strtotime('-1 day'));
                $fin = date('Y-m-d 23:59:59', strtotime('-1 day'));
                break;
            case 'semana': 
                $inicioFiltro = date('Y-m-d 00:00:00', strtotime('-7 days')); 
                break;
            case 'mes': 
                $inicioFiltro = date('Y-m-01 00:00:00'); 
                break;
        }
    } else {
        $inicioFiltro = $filtros['f_inicio'] . " 00:00:00";
        $fin = $filtros['f_fin'] . " 23:59:59";
    }

    // --- 2. DETERMINAR INICIO REAL (DESDE ÚLTIMO CORTE) ---
    $sqlUltimo = "SELECT CONCAT(fecha_corte, ' ', hora_cierre) as ultimo_cierre 
                  FROM corte_de_caja 
                  WHERE almacen_id = $target 
                  ORDER BY id DESC LIMIT 1";
    $resUltimo = $this->db->query($sqlUltimo);
    $inicioReal = $inicioFiltro;

    if ($resUltimo && $resUltimo->num_rows > 0) {
        $datoCorte = $resUltimo->fetch_assoc();
        $fechaUltimoCorte = $datoCorte['ultimo_cierre'];
        if ($fechaUltimoCorte > $inicioFiltro) {
            $inicioReal = $fechaUltimoCorte;
        }
    }

    // --- 3. CONDICIONES ESTRICTAS ---
    $filtroAlmacenV  = ($target > 0) ? " AND v.almacen_id = $target"  : "";
    $filtroAlmacenV2 = ($target > 0) ? " AND v2.almacen_id = $target" : "";

    // El estado 'activa' va en el ON del JOIN para excluir ventas canceladas desde el inicio
    $condicionPagosBase = "INNER JOIN ventas v2 
                               ON hp.venta_id = v2.id 
                              AND v2.estado_general = 'activa'
                              $filtroAlmacenV2
                           WHERE hp.fecha > '$inicioReal' 
                             AND hp.fecha <= '$fin'";

    // --- 4. CONSULTA MAESTRA ---
    $sql = "SELECT 
                -- Venta Bruta (Solo activas)
                (SELECT IFNULL(SUM(v.total), 0) 
                 FROM ventas v 
                 WHERE v.fecha > '$inicioReal' 
                   AND v.fecha <= '$fin' 
                   AND v.estado_general = 'activa' 
                   $filtroAlmacenV) AS venta_bruta_total,

                -- Favor Usado (Solo de ventas activas)
                (SELECT IFNULL(SUM(hp.saldo_favor), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS favor_usado,
                
                -- Ingresos Líquidos por método (Solo de ventas activas)
                (SELECT IFNULL(SUM(CASE WHEN UPPER(hp.metodo_pago) = 'EFECTIVO' 
                               THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS efec_puro,

                (SELECT IFNULL(SUM(CASE WHEN UPPER(hp.metodo_pago) = 'TRANSFERENCIA' 
                               THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS trans_puro,

                (SELECT IFNULL(SUM(CASE WHEN UPPER(hp.metodo_pago) = 'TARJETA' 
                               THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS tarj_puro,
                
                -- Egresos
                (SELECT IFNULL(SUM(total), 0) FROM gastos 
                 WHERE estado = 'pagado' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin' 
                   AND metodo_pago = 'Efectivo') AS g_efec,

                (SELECT IFNULL(SUM(total), 0) FROM gastos 
                 WHERE estado = 'pagado' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin' 
                   AND metodo_pago = 'Transferencia') AS g_trans,

                (SELECT IFNULL(SUM(total), 0) FROM gastos 
                 WHERE estado = 'pagado' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin' 
                   AND metodo_pago = 'Tarjeta') AS g_tarj,

                (SELECT IFNULL(SUM(total), 0) FROM compras 
                 WHERE estado = 'confirmada' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin') AS compras_total";

    $res = $this->db->query($sql);

    if (!$res) {
        return ['error' => $this->db->error];
    }

    $row = $res->fetch_assoc();

    // --- 5. CÁLCULOS FINALES ---
    $bruta  = floatval($row['venta_bruta_total']);
    $favor  = floatval($row['favor_usado']);

    $efectivo_real = floatval($row['efec_puro']) - floatval($row['g_efec']) - floatval($row['compras_total']);
    $trans_real    = floatval($row['trans_puro']) - floatval($row['g_trans']);
    $tarjeta_real  = floatval($row['tarj_puro'])  - floatval($row['g_tarj']);

    $ingresos_liquidos = floatval($row['efec_puro']) + floatval($row['trans_puro']) + floatval($row['tarj_puro']);
    $cobradoTotal      = $ingresos_liquidos + $favor;

    return [
        'venta_bruta'       => $bruta,
        'efectivo_real'     => $efectivo_real,
        'transferencia'     => $trans_real,
        'tarjeta'           => $tarjeta_real,
        'saldo_favor_usado' => $favor,
        'cobrado_total'     => $cobradoTotal,
        'deuda_pendiente'   => max(0, $bruta - $cobradoTotal),
        'metadata'          => [
            'inicio' => $inicioReal,
            'fin'    => $fin
        ]
    ];
}
// 2. REGISTRAR UN SOLO ALMACÉN
// public function registrarCortePorAlmacen($id_almacen) {
//     date_default_timezone_set('America/Mexico_City');
//     $fecha_dia = date('Y-m-d');
//     $hora_cierre = date('H:i:s'); 

//     $filtros = ['periodo' => 'personalizado', 'f_inicio' => $fecha_dia, 'f_fin' => $fecha_dia];
//     $totales = $this->obtenerSumasCorte($filtros, $id_almacen);
    
//     // Asegurar que el usuario_id sea un entero
//     $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 1;

//     $sql = "INSERT INTO corte_de_caja (fecha_corte, hora_cierre, almacen_id, venta_bruta, efectivo_real, transferencia, tarjeta, saldo_favor_usado, cobrado_total, deuda_pendiente, usuario_id) 
//             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
//             ON DUPLICATE KEY UPDATE 
//                 hora_cierre = VALUES(hora_cierre),
//                 venta_bruta = VALUES(venta_bruta),
//                 efectivo_real = VALUES(efectivo_real),
//                 transferencia = VALUES(transferencia),
//                 tarjeta = VALUES(tarjeta),
//                 saldo_favor_usado = VALUES(saldo_favor_usado),
//                 cobrado_total = VALUES(cobrado_total),
//                 deuda_pendiente = VALUES(deuda_pendiente),
//                 usuario_id = VALUES(usuario_id)";

//     $stmt = $this->db->prepare($sql);
//     if (!$stmt) {
//         throw new Exception("Error en prepare: " . $this->db->error);
//     }

//     // "ssi" -> fecha (s), hora (s), almacen_id (i)
//     // "ddddddd" -> los 7 montos decimales (double)
//     // "i" -> usuario_id (i)
//     $stmt->bind_param("ssidddddddi", 
//         $fecha_dia, 
//         $hora_cierre, 
//         $id_almacen, 
//         $totales['venta_bruta'], 
//         $totales['efectivo_real'], 
//         $totales['transferencia'], 
//         $totales['tarjeta'], 
//         $totales['saldo_favor'], 
//         $totales['cobrado_total'], 
//         $totales['deuda_pendiente'], 
//         $usuario_id
//     );

//     if ($stmt->execute()) {
//         return ['status' => 'success', 'data' => $totales];
//     } else {
//         throw new Exception("Error al ejecutar el corte: " . $stmt->error);
//     }
// }
/**
 * Verifica si ya existe un registro de corte para un almacén y fecha específicos.
 * * @param string $fecha Formato 'YYYY-MM-DD'
 * @param int $id_almacen El ID del almacén a consultar
 * @return bool True si ya existe, False si no
 */
public function existeCorte($fecha, $id_almacen) {
    // Es vital filtrar por ambos: fecha Y almacén
    $sql = "SELECT id FROM corte_de_caja 
            WHERE fecha_corte = ? 
            AND almacen_id = ? 
            LIMIT 1";

    try {
        $stmt = $this->db->prepare($sql);
        
        // "s" para la fecha (string), "i" para el almacén (int)
        $stmt->bind_param("si", $fecha, $id_almacen);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        
        // Retorna true si encontró una fila, false si está vacío
        return $resultado->num_rows > 0;

    } catch (Exception $e) {
        // En caso de error de SQL, logueamos y retornamos false 
        // para permitir que el flujo intente el registro
        error_log("Error en existeCorte del Modelo: " . $e->getMessage());
        return false;
    }
}
 public function obtenerAlmacenesPendientes($id_almacen_sesion, $es_admin, $fecha) {
    if ($es_admin) {
        // AUTOMATIZACIÓN ADMIN: Busca almacenes con ventas que NO han cerrado hoy
        $sql = "SELECT DISTINCT a.id, a.nombre 
                FROM almacenes a
                INNER JOIN ventas v ON v.almacen_id = a.id
                WHERE a.activo = 1 
                AND DATE(v.fecha) = ?
                AND v.estado_general = 'activa'
                AND NOT EXISTS (
                    SELECT 1 FROM corte_de_caja c 
                    WHERE c.almacen_id = a.id AND c.fecha_corte = ?
                ) LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $fecha, $fecha);
    } else {
        // AUTOMATIZACIÓN USUARIO: Revisa si SU almacén ya cerró hoy
        $sql = "SELECT id, nombre FROM almacenes 
                WHERE id = ? 
                AND activo = 1 
                AND NOT EXISTS (
                    SELECT 1 FROM corte_de_caja 
                    WHERE almacen_id = ? AND fecha_corte = ?
                ) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $id_almacen_sesion, $id_almacen_sesion, $fecha);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function agregarCorteManual($datos) {
   date_default_timezone_set('America/Mexico_City');

    $fecha_corte     = $datos['fecha_corte'] ?? date('Y-m-d');
    $hora_cierre     = date('H:i:s');
    $almacen_id      = intval($datos['almacen_id']);
    $usuario_id      = intval($datos['usuario_id']);

    // ===============================
    // 🔥 INGRESOS YA PROCESADOS (NO RECALCULAR)
    // ===============================
    $venta_bruta         = floatval($datos['venta_bruta']);

    $efectivo_real       = floatval($datos['total_efectivo']);
    $transferencia       = floatval($datos['total_transferencia']);
    $tarjeta             = floatval($datos['total_tarjeta']);

    // ===============================
    // 🔥 ABONOS
    // ===============================
    $abono_efectivo      = floatval($datos['abono_efectivo']);
    $abono_tarjeta       = floatval($datos['abono_tarjeta']);
    $abono_transferencia = floatval($datos['abono_transferencia']);
    $abonos_totales      = floatval($datos['abonos_totales']);

    // ===============================
    // 🔥 OTROS MOVIMIENTOS
    // ===============================
    $deuda_pendiente     = floatval($datos['deuda_pendiente']);
    $saldo_favor_usado   = floatval($datos['saldo_favor'] ?? 0);
    $cobrado_total       = floatval($datos['cobrado_total']);

    // ===============================
    // 🔥 EGRESOS
    // ===============================
    $gastos_totales      = floatval($datos['gastos_totales']);
    $compras_totales     = floatval($datos['compras_totales']);
    $gran_total_ingresos = floatval($datos['gran_total_ingresos']);

    $observaciones       = $datos['observaciones'] ?? '';
    $created_at          = date('Y-m-d H:i:s');

    // Usamos ON DUPLICATE KEY UPDATE para actualizar si la dupla fecha/almacen ya existe
    $sql = "INSERT INTO `corte_de_caja` (
                `fecha_corte`, `hora_cierre`, `almacen_id`, `venta_bruta`, `efectivo_real`, 
                `transferencia`, `tarjeta`, `abono_efectivo`, `abono_tarjeta`, 
                `abono_transferencia`, `abonos_totales`, `saldo_favor_usado`, 
                `cobrado_total`, `gastos_totales`, `compras_totales`, 
                `gran_total_ingresos`, `deuda_pendiente`, `usuario_id`, 
                `observaciones`, `created_at`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                `hora_cierre` = VALUES(`hora_cierre`),
                `venta_bruta` = VALUES(`venta_bruta`),
                `efectivo_real` = VALUES(`efectivo_real`),
                `transferencia` = VALUES(`transferencia`),
                `tarjeta` = VALUES(`tarjeta`),
                `abono_efectivo` = VALUES(`abono_efectivo`),
                `abono_tarjeta` = VALUES(`abono_tarjeta`),
                `abono_transferencia` = VALUES(`abono_transferencia`),
                `abonos_totales` = VALUES(`abonos_totales`),
                `saldo_favor_usado` = VALUES(`saldo_favor_usado`),
                `cobrado_total` = VALUES(`cobrado_total`),
                `gastos_totales` = VALUES(`gastos_totales`),
                `compras_totales` = VALUES(`compras_totales`),
                `gran_total_ingresos` = VALUES(`gran_total_ingresos`),
                `deuda_pendiente` = VALUES(`deuda_pendiente`),
                `usuario_id` = VALUES(`usuario_id`),
                `observaciones` = VALUES(`observaciones`),
                `updated_at` = NOW()";

    $stmt = $this->db->prepare($sql);
    $tipos = "ssiddddddddddddddiss";

    $stmt->bind_param(
        $tipos, 
        $fecha_corte, $hora_cierre, $almacen_id, $venta_bruta, $efectivo_real,
        $transferencia, $tarjeta, $abono_efectivo, $abono_tarjeta, $abono_transferencia,
        $abonos_totales, $saldo_favor_usado, $cobrado_total, $gastos_totales, $compras_totales,
        $gran_total_ingresos, $deuda_pendiente, $usuario_id, $observaciones, $created_at
    );

    if ($stmt->execute()) {
        // insert_id devolverá el ID del registro creado o actualizado
        $final_id = ($stmt->insert_id > 0) ? $stmt->insert_id : "Actualizado";
        return ['status' => 'success', 'id' => $final_id];
    } else {
        return ['status' => 'error', 'message' => $this->db->error];
    }
}
public function registrarAperturaDesdeCierre($almacen_id, $usuario_id, $desglose, $fecha_corte) {
    /**
     * $desglose es un array esperado: 
     * ['efectivo' => 0.00, 'tarjeta' => 0.00, 'transferencia' => 0.00]
     */
    
    // Calculamos el monto total
    $monto_total = array_sum($desglose);
    
    // Definimos la fecha contable (mañana al primer segundo)
    $fecha_apertura = date('Y-m-d', strtotime($fecha_corte . ' +1 day')) . ' 00:00:01';
    
    $concepto = "Saldo inicial automático (Corte: " . $fecha_corte . ")";

    // Agregamos las columnas de destino aunque vayan como NULL 
    // para que coincida con la estructura actual de historial_capital
    $sql = "INSERT INTO historial_capital (
                categoria_id, 
                almacen_origen_id, 
                almacen_destino_id, 
                caja_fuerte_destino_id,
                banco_destino_id,
                monto, 
                monto_efectivo,
                monto_tarjeta,
                monto_transferencia,
                usuario_registro_id, 
                concepto, 
                fecha_movimiento
            ) VALUES (1, ?, NULL, NULL, NULL, ?, ?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $almacen_id, 
            $monto_total,
            $desglose['efectivo'] ?? 0,
            $desglose['tarjeta'] ?? 0,
            $desglose['transferencia'] ?? 0,
            $usuario_id, 
            $concepto, 
            $fecha_apertura
        ]);
    } catch (Exception $e) {
        error_log("Error en registrarAperturaDesdeCierre: " . $e->getMessage());
        return false;
    }
}
public function registrarAperturaDesdeCierreConcepto($data) {
     date_default_timezone_set('America/Mexico_City');
    // 1. Datos base
    $almacen_id   = intval($data['almacen_id'] ?? 0);
    $usuario_id   = intval($data['usuario_id'] ?? 0);
    $categoria_id = intval($data['categoria_id'] ?? 1);
    $monto_mov    = floatval($data['monto'] ?? 0);
    $tipo_op      = $data['tipo_operacion'] ?? 'entrada';
    $metodo       = $data['metodo_pago'] ?? 'efectivo'; // Importante para el destino
    
    // 2. Lógica para Caja/Banco (Inversa)
    if ($tipo_op === 'salida' || $tipo_op === 'traspaso') {
        $operador = 1; // Sube el saldo en Caja/Banco
    } else {
        $operador = -1; // Baja el saldo en Caja/Banco
    }
    $ajuste_saldo = $monto_mov * $operador;

    // 3. Preparación de variables
    $efectivo      = floatval($data['monto_efectivo'] ?? 0);
    $tarjeta       = floatval($data['monto_tarjeta'] ?? 0);
    $transferencia = floatval($data['monto_transferencia'] ?? 0);
    $monto=$efectivo+ $tarjeta+$transferencia;
    
    $fecha_base     = $data['fecha_movimiento'] ?? date('Y-m-d');
   
// Comparamos solo la fecha (Año-mes-día)
if (date('Y-m-d', strtotime($fecha_base)) == date('Y-m-d')) {
    // Si es hoy, usamos la hora, minuto y segundo actual
    $fecha_apertura = date('Y-m-d H:i:s');
} else {
    // Si es una fecha distinta (pasada o futura), usamos el primer segundo del día
    $fecha_apertura = date('Y-m-d', strtotime($fecha_base)) . ' 00:00:01';
}
   $concepto_final = "Movimiento de " . $tipo_op . ": " . ($data['concepto'] ?? '') . " Monto :".$monto_mov." Fecha: " . date('Y-m-d H:i:s');
    try {
        $this->db->begin_transaction();

        // --- PASO A: Registro del ORIGEN (El movimiento actual) ---
        $sql = "INSERT INTO historial_capital (
                    categoria_id, almacen_origen_id, almacen_destino_id, 
                    caja_fuerte_destino_id, banco_destino_id, monto, 
                    monto_efectivo, monto_tarjeta, monto_transferencia,
                    usuario_registro_id, concepto, fecha_movimiento
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $categoria_id, $almacen_id, 
            $data['almacen_destino_id'] ?: null,
            $data['caja_fuerte_id'] ?: null,
            $data['banco_id'] ?: null,
            $monto, $efectivo, $tarjeta, $transferencia,
            $usuario_id, $concepto_final, $fecha_apertura
        ]);

        // --- PASO B: Lógica de Traspaso entre ALMACENES ---
        // Si hay un almacén destino, creamos la "Entrada" automática para ese almacén
        $almacen_dest_id = intval($data['almacen_destino_id'] ?? 0);
        if ($tipo_op === 'traspaso' && $almacen_dest_id > 0) {
            
            // Consultamos el saldo actual del almacén DESTINO para sumarle el dinero
            $saldos_dest = $this->obtenerSaldoInicialMonitor($almacen_dest_id, '2000-01-01', $fecha_base);
            
            $nuevo_desglose_dest = [
                'efectivo'      => floatval($saldos_dest['monto_efectivo']),
                'tarjeta'       => floatval($saldos_dest['monto_tarjeta']),
                'transferencia' => floatval($saldos_dest['monto_transferencia'])
            ];

            // Sumamos el monto al método correspondiente en el destino
            if (isset($nuevo_desglose_dest[$metodo])) {
                $nuevo_desglose_dest[$metodo] += $monto_mov;
            }

            // Insertamos el registro de entrada en el almacén destino
            $stmt->execute([
    $categoria_id, 
    $almacen_dest_id, 
    null, null, null,
    $monto_mov,
    $nuevo_desglose_dest['efectivo'],
    $nuevo_desglose_dest['tarjeta'],
    $nuevo_desglose_dest['transferencia'],
    $usuario_id,
    // Añadimos un espacio o separador antes de la fecha
    "Entrada por traspaso desde Almacén ID: " . $almacen_id ." Monto ".$monto_mov. " | " . date('Y-m-d H:i:s'),
    $fecha_apertura
]);
        }

        // --- PASO C: Afectar Caja Fuerte / Banco ---
        if (!empty($data['caja_fuerte_id']) && $data['caja_fuerte_id'] > 0) {
            $this->actualizarSaldoCajaFuerte($data['caja_fuerte_id'], $ajuste_saldo);
        }
        if (!empty($data['banco_id']) && $data['banco_id'] > 0) {
            $this->actualizarSaldoBanco($data['banco_id'], $ajuste_saldo);
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Error en flujo de fondos: " . $e->getMessage());
        return false;
    }
}
public function registrarAperturaDesdeCierreConceptoAbono($data) {

    date_default_timezone_set('America/Mexico_City');

    // 1. Datos base
    $almacen_id   = intval($data['almacen_id'] ?? 0);
    $usuario_id   = intval($data['usuario_id'] ?? 0);
    $categoria_id = intval($data['categoria_id'] ?? 1);

    $monto_mov    = floatval($data['monto'] ?? 0);
    $tipo_op      = $data['tipo_operacion'] ?? 'entrada';
    $metodo       = $data['metodo_pago'] ?? 'efectivo';

    $efectivo      = floatval($data['monto_efectivo'] ?? 0);
    $tarjeta       = floatval($data['monto_tarjeta'] ?? 0);
    $transferencia = floatval($data['monto_transferencia'] ?? 0);

    $fecha_base = $data['fecha_movimiento'] ?? date('Y-m-d');

    if (date('Y-m-d', strtotime($fecha_base)) == date('Y-m-d')) {
        $fecha_apertura = date('Y-m-d H:i:s');
    } else {
        $fecha_apertura = date('Y-m-d', strtotime($fecha_base)) . ' 00:00:01';
    }

    $concepto_final = "Movimiento de " . $tipo_op . ": " . ($data['concepto'] ?? '') ." Monto ".$monto_mov. " : " . date('Y-m-d H:i:s');

    // 2. Caja/Banco lógica
    $operador = ($tipo_op === 'salida' || $tipo_op === 'traspaso') ? 1 : -1;
    $ajuste_saldo = $monto_mov * $operador;

    try {
        $this->db->begin_transaction();

        // ======================================================
        // 🔥 PASO 1: CONSULTAR SALDO ACTUAL DEL ALMACÉN
        // ======================================================
        $saldos_actuales = $this->obtenerSaldoInicialMonitor(
            $almacen_id,
            '2000-01-01',
            $fecha_base
        );

        $saldo_efectivo_actual      = floatval($saldos_actuales['monto_efectivo'] ?? 0);
        $saldo_tarjeta_actual       = floatval($saldos_actuales['monto_tarjeta'] ?? 0);
        $saldo_transferencia_actual = floatval($saldos_actuales['monto_transferencia'] ?? 0);

        // ======================================================
        // 🔥 PASO 2: SUMAR LO NUEVO
        // ======================================================
        $nuevo_efectivo      = $saldo_efectivo_actual + $efectivo;
        $nuevo_tarjeta       = $saldo_tarjeta_actual + $tarjeta;
        $nuevo_transferencia = $saldo_transferencia_actual + $transferencia;

        $monto = $nuevo_efectivo + $nuevo_tarjeta + $nuevo_transferencia;

        // ======================================================
        // 🔥 PASO 3: INSERT MOVIMIENTO PRINCIPAL
        // ======================================================
        $sql = "INSERT INTO historial_capital (
                    categoria_id,
                    almacen_origen_id,
                    almacen_destino_id,
                    caja_fuerte_destino_id,
                    banco_destino_id,
                    monto,
                    monto_efectivo,
                    monto_tarjeta,
                    monto_transferencia,
                    usuario_registro_id,
                    concepto,
                    fecha_movimiento
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $categoria_id,
            $almacen_id,
            $data['almacen_destino_id'] ?: null,
            $data['caja_fuerte_id'] ?: null,
            $data['banco_id'] ?: null,
            $monto,
            $nuevo_efectivo,
            $nuevo_tarjeta,
            $nuevo_transferencia,
            $usuario_id,
            $concepto_final,
            $fecha_apertura
        ]);

        // ======================================================
        // 🔥 PASO 4: TRASPASO A OTRO ALMACÉN
        // ======================================================
        $almacen_dest_id = intval($data['almacen_destino_id'] ?? 0);

        if ($tipo_op === 'traspaso' && $almacen_dest_id > 0) {

            $saldos_dest = $this->obtenerSaldoInicialMonitor(
                $almacen_dest_id,
                '2000-01-01',
                $fecha_base
            );

            $nuevo_dest_efectivo      = floatval($saldos_dest['monto_efectivo'] ?? 0);
            $nuevo_dest_tarjeta       = floatval($saldos_dest['monto_tarjeta'] ?? 0);
            $nuevo_dest_transferencia = floatval($saldos_dest['monto_transferencia'] ?? 0);

            if (isset([
                'efectivo' => $nuevo_dest_efectivo,
                'tarjeta' => $nuevo_dest_tarjeta,
                'transferencia' => $nuevo_dest_transferencia
            ][$metodo])) {
                if ($metodo === 'efectivo') $nuevo_dest_efectivo += $monto_mov;
                if ($metodo === 'tarjeta') $nuevo_dest_tarjeta += $monto_mov;
                if ($metodo === 'transferencia') $nuevo_dest_transferencia += $monto_mov;
            }

            $stmt->execute([
                $categoria_id,
                $almacen_dest_id,
                null,
                null,
                null,
                $monto_mov,
                $nuevo_dest_efectivo,
                $nuevo_dest_tarjeta,
                $nuevo_dest_transferencia,
                $usuario_id,
                "Entrada por traspaso desde Almacén ID: " . $almacen_id ." Monto ".$monto_mov. " | " . date('Y-m-d H:i:s'),
                $fecha_apertura
            ]);
        }

        // ======================================================
        // 🔥 PASO 5: CAJA FUERTE / BANCO
        // ======================================================
        if (!empty($data['caja_fuerte_id'])) {
            $this->actualizarSaldoCajaFuerte($data['caja_fuerte_id'], $ajuste_saldo);
        }

        if (!empty($data['banco_id'])) {
            $this->actualizarSaldoBanco($data['banco_id'], $ajuste_saldo);
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Error en flujo de fondos: " . $e->getMessage());
        return false;
    }
}
/**
 * Métodos auxiliares para la actualización de saldos reales
 */
public function actualizarSaldoCajaFuerte($id, $monto) {
    $sql = "UPDATE cajas_fuertes SET Saldo = Saldo + ? WHERE id = ?";
    return $this->db->prepare($sql)->execute([$monto, $id]);
}

public function actualizarSaldoBanco($id, $monto) {
    $sql = "UPDATE bancos SET saldo = saldo + ? WHERE id_cuenta = ?"; 
    return $this->db->prepare($sql)->execute([$monto, $id]);
}
/**
 * Obtiene el saldo inicial basándose en el nivel de acceso.
 * Si $almacen_id es 0, actúa como Admin y devuelve un array de todos los almacenes.
 * Si $almacen_id > 0, devuelve el monto único de esa sucursal.
 */
public function obtenerSaldoInicialMonitor($almacen_id, $f_inicio, $f_fin) {
    // Solo necesitamos la fecha final del rango
    $fecha_corte = $f_fin . ' 23:59:59';

    if ($almacen_id == 0) {
        /**
         * VISTA ADMIN:
         * Trae todas las sucursales y su último saldo registrado
         * ANTES o igual a la fecha seleccionada
         */
        $sql = "SELECT 
        a.id as idAlmacen,
                    a.nombre AS almacen, 
                    IFNULL(h.monto, 0.00) AS monto, 
                    IFNULL(h.monto_efectivo, 0.00) AS monto_efectivo, 
                    IFNULL(h.monto_tarjeta, 0.00) AS monto_tarjeta, 
                    IFNULL(h.monto_transferencia, 0.00) AS monto_transferencia
                FROM almacenes a
                LEFT JOIN historial_capital h ON h.id = (
                    SELECT id 
                    FROM historial_capital 
                    WHERE almacen_origen_id = a.id 
                      AND fecha_movimiento <= ?
                    ORDER BY id DESC 
                    LIMIT 1
                )
                WHERE a.activo = 1 
                ORDER BY a.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $fecha_corte);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // 🔥 Asegurar estructura consistente (por si acaso)
        foreach ($result as &$r) {
              $r['almacen'] = ($r['almacen'] ?? '');
              $r['idAlmacen'] = ($r['idAlmacen'] ?? 0);
            $r['monto'] = floatval($r['monto'] ?? 0);
            $r['monto_efectivo'] = floatval($r['monto_efectivo'] ?? 0);
            $r['monto_tarjeta'] = floatval($r['monto_tarjeta'] ?? 0);
            $r['monto_transferencia'] = floatval($r['monto_transferencia'] ?? 0);
        }

        return $result;

    } else {
        /**
         * VISTA SUCURSAL:
         * Último saldo antes o igual a la fecha
         */
        $sql = "SELECT 

                    IFNULL(monto, 0.00) as monto, 
                    IFNULL(monto_efectivo, 0.00) as monto_efectivo, 
                    IFNULL(monto_tarjeta, 0.00) as monto_tarjeta, 
                    IFNULL(monto_transferencia, 0.00) as monto_transferencia
                FROM historial_capital 
                WHERE  almacen_origen_id = ? 
                  AND fecha_movimiento <= ?
                ORDER BY id DESC 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $almacen_id, $fecha_corte);
        $stmt->execute();

        $res = $stmt->get_result()->fetch_assoc();

        // ✅ Si no hay historial → regresar ceros
        return $res ?: [
            'monto' => 0.00, 
            'monto_efectivo' => 0.00, 
            'monto_tarjeta' => 0.00, 
            'monto_transferencia' => 0.00
        ];
    }
}
public function obtenerSaldoInicialMonitorTabla($almacen_id, $f_inicio, $f_fin) {
    // Definimos el rango completo de tiempo
    $fecha_inicio = $f_inicio . ' 00:00:00';
    $fecha_final  = $f_fin . ' 23:59:59';

    if ($almacen_id == 0) {
        /**
         * VISTA ADMIN:
         * Trae TODOS los movimientos de todas las sucursales en el rango
         */
        $sql = "SELECT 
                    a.nombre AS almacen, 
                    h.monto, 
                    h.monto_efectivo, 
                    h.monto_tarjeta, 
                    h.monto_transferencia,
                    h.fecha_movimiento,
                    h.concepto -- O la columna que uses para describir el movimiento
                FROM historial_capital h
                INNER JOIN almacenes a ON h.almacen_origen_id = a.id
                WHERE h.fecha_movimiento BETWEEN ? AND ?
                ORDER BY h.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $fecha_inicio, $fecha_final);
    } else {
        /**
         * VISTA SUCURSAL:
         * Trae todos los movimientos de UNA sucursal en el rango
         */
        $sql = "SELECT 
            a.nombre AS almacen, -- Faltaba la coma aquí
            h.monto, 
            h.monto_efectivo, 
            h.monto_tarjeta, 
            h.monto_transferencia,
            h.fecha_movimiento,
            h.concepto
        FROM historial_capital h -- Debes poner el alias 'h' aquí
        INNER JOIN almacenes a ON h.almacen_origen_id = a.id
        WHERE h.almacen_origen_id = ? 
          AND h.fecha_movimiento BETWEEN ? AND ?
        ORDER BY h.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iss", $almacen_id, $fecha_inicio, $fecha_final);
    }

    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Formatear para que el JS reciba números y no strings
    foreach ($result as &$r) {
        $r['monto'] = (float)$r['monto'];
        $r['monto_efectivo'] = (float)$r['monto_efectivo'];
        $r['monto_tarjeta'] = (float)$r['monto_tarjeta'];
        $r['monto_transferencia'] = (float)$r['monto_transferencia'];
    }

    return $result;
}
public function saldoCajaFuerte($almacen_id = 0) {

    if ($almacen_id == 0) {

        $sql = "SELECT 
                    cf.nombre, cf.saldo,
                    a.nombre AS almacen
                FROM cajas_fuertes cf
                INNER JOIN almacenes a ON a.id = cf.almacen_id
                WHERE cf.estatus = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

    } else {

        $sql = "SELECT 
                    cf.nombre, cf.saldo,
                    a.nombre AS almacen
                FROM cajas_fuertes cf
                INNER JOIN almacenes a ON a.id = cf.almacen_id
                WHERE cf.almacen_id = ? 
                AND cf.estatus = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
    }

    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
public function saldoCuentasBancarias($almacen_id = 0) {

    if ($almacen_id == 0) {

        $sql = "SELECT 
                    cb.nombre_cuenta AS nombre,
                    cb.saldo AS saldo,
                    a.nombre AS almacen
                FROM cuentas_bancarias cb
                INNER JOIN almacenes a ON a.id = cb.id_almacen
                WHERE cb.estatus = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

    } else {

        $sql = "SELECT 
                    cb.nombre_cuenta AS nombre,
                    cb.saldo AS saldo,
                    a.nombre AS almacen
                FROM cuentas_bancarias cb
                INNER JOIN almacenes a ON a.id = cb.id_almacen
                WHERE cb.id_almacen = ? 
                AND cb.estatus = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
    }

    // 🔥 FIX IMPORTANTE
    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Error get_result(): " . $this->db->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
}