<?php
date_default_timezone_set('America/Mexico_City');

class EntregaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // MANTIENE TU FUNCIÓN ORIGINAL DE LISTADO
public function listarSalidasPendientes($filtros, $almacen_usuario_sesion, $es_admin) {
    // 1. Configurar Zona Horaria de Ciudad de México
    date_default_timezone_set('America/Mexico_City');

    $periodo = $filtros['periodo'] ?? 'semana';
    $f_inicio_user = $filtros['f_inicio'] ?? '';
    $f_fin_user = $filtros['f_fin'] ?? '';
    
    // Obtenemos la fecha actual con la zona horaria ya aplicada
    $hoy = date('Y-m-d');
    $inicio = $hoy; 
    $fin = $hoy;

    // 2. Lógica de Periodos corregida
    if ($periodo !== 'personalizado') {
        switch ($periodo) {
            case 'hoy':
                $inicio = $hoy;
                $fin = $hoy;
                break;
            case 'ayer':
                $inicio = date('Y-m-d', strtotime('-1 day'));
                $fin = $inicio;
                break;
            case 'semana':
                $inicio = date('Y-m-d', strtotime('-7 days'));
                $fin = $hoy;
                break;
            case 'mes':
                $inicio = date('Y-m-01');
                $fin = date('Y-m-t'); 
                break;
            default:
                $inicio = date('Y-m-d', strtotime('-7 days'));
                $fin = $hoy;
                break;
        }
    } else {
        // Validación de fechas personalizadas
        $inicio = !empty($f_inicio_user) ? date('Y-m-d', strtotime($f_inicio_user)) : $hoy;
        $fin = !empty($f_fin_user) ? date('Y-m-d', strtotime($f_fin_user)) : $hoy;
    }

    $almacen_filtro = intval($filtros['almacen_id'] ?? 0);
    $target_almacen = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_filtro;

    // 3. Consulta SQL con rango completo de horas (00:00:00 a 23:59:59)
    $where = "WHERE m.tipo = 'salida' 
              AND (m.usuario_recibe_id IS NULL OR m.usuario_recibe_id = 0)
              AND m.fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
              AND (v.id IS NULL OR v.estado_general = 'activa')
              AND td.id IS NULL"; 
    
    if ($target_almacen > 0) { 
        $where .= " AND m.almacen_origen_id = $target_almacen"; 
    }

    $sql = "SELECT 
                m.*, 
                v.id as venta_id,
                c.nombre_comercial as cliente,
                v.folio as folio_venta,
                p.nombre as prod_nombre, p.sku, p.factor_conversion, p.unidad_reporte,p.unidad_medida ,

                a1.nombre as origen_nombre,
                u1.nombre as usuario_nombre,
                IF(rsl.id IS NOT NULL, 1, 0) as ya_despachado,
                IFNULL(trm.estado_reparto, 'pendiente') as estado_reparto
            FROM movimientos m 
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
            LEFT JOIN usuarios u1 ON m.usuario_registra_id = u1.id
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN transmutacion_detalle td ON m.id = td.movimiento_id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            
            $where 
            ORDER BY m.fecha DESC"; // Ordenar por fecha (más reciente arriba)

    $resultado = $this->db->query($sql);
    $data = [];

    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $data[] = [
                'cliente'  => $row['cliente'],
                'venta_id'          => $row['venta_id'],
                'id'                => $row['id'], 
                'almacen_origen_id' => $row['almacen_origen_id'],
                'folio_venta'       => $row['folio_venta'] ?? '---',
                'fecha_raw'         => $row['fecha'], // Para que el JS pueda comparar fechas puras
                'fecha_format'      => date('d/m/Y H:i', strtotime($row['fecha'])),
                'producto'          => $row['prod_nombre'],
                'sku'               => $row['sku'],
                'cantidad'          => $row['cantidad'],
                'factor_conversion' => $row['factor_conversion'],
                'unidad_medida'    => $row['unidad_medida'] ?? 'PZA',
                'unidad_reporte'    => $row['unidad_reporte'] ?? 'PZA',
                'origen'            => $row['origen_nombre'] ?? '---',
                'u_reg'             => $row['usuario_nombre'] ?? 'Sist.',
                'ya_despachado'     => intval($row['ya_despachado']),
                'estado_reparto'    => $row['estado_reparto']
            ];
        }
    }
    return $data;
}// MANTIENE TU FUNCIÓN ORIGINAL DE PROCESO DE STOCK
public function obtenerTotalesSalidas($filtros, $almacen_usuario_sesion, $es_admin) {
    date_default_timezone_set('America/Mexico_City');

    $periodo = $filtros['periodo'] ?? 'semana';
    $f_inicio_user = $filtros['f_inicio'] ?? '';
    $f_fin_user = $filtros['f_fin'] ?? '';

    $hoy = date('Y-m-d');
    $inicio = $hoy;
    $fin = $hoy;

    if ($periodo !== 'personalizado') {
        switch ($periodo) {
            case 'hoy':
                break;
            case 'ayer':
                $inicio = date('Y-m-d', strtotime('-1 day'));
                $fin = $inicio;
                break;
            case 'semana':
                $inicio = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'mes':
                $inicio = date('Y-m-01');
                $fin = date('Y-m-t');
                break;
            default:
                $inicio = date('Y-m-d', strtotime('-7 days'));
                break;
        }
    } else {
        $inicio = !empty($f_inicio_user) ? date('Y-m-d', strtotime($f_inicio_user)) : $hoy;
        $fin = !empty($f_fin_user) ? date('Y-m-d', strtotime($f_fin_user)) : $hoy;
    }

    $almacen_filtro = intval($filtros['almacen_id'] ?? 0);
    $target_almacen = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_filtro;

    $where = "WHERE m.tipo = 'salida'
              AND trm.estado_reparto = 'completado'
              AND m.fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'";

    if ($target_almacen > 0) {
        $where .= " AND m.almacen_origen_id = $target_almacen";
    }

    $sql = "SELECT 
                SUM(lms.cantidad_salida) AS total_unidades,

                SUM(lms.costo_compra_historico * lms.cantidad_salida) AS costo_total,

                SUM(lms.precio_venta_pactado * lms.cantidad_salida) AS total_venta,

                SUM(
                    (lms.precio_venta_pactado - lms.costo_compra_historico) 
                    * lms.cantidad_salida
                ) AS ganancia_total

            FROM movimientos m

            INNER JOIN detalle_venta dv 
                ON dv.venta_id = m.referencia_id 
                AND dv.producto_id = m.producto_id

            INNER JOIN entregas_venta ev 
                ON ev.venta_id = dv.venta_id

            INNER JOIN lotes_movimientos_salida lms 
                ON lms.detalle_venta_id = dv.id 
                AND lms.entrega_venta_id = ev.id

            LEFT JOIN transporte_repartos_maestro trm 
                ON m.id = trm.entrega_venta_id

            $where";

    $res = $this->db->query($sql);
    $row = $res->fetch_assoc();

    return [
        'total_unidades' => floatval($row['total_unidades'] ?? 0),
        'costo_total'    => floatval($row['costo_total'] ?? 0),
        'total_venta'    => floatval($row['total_venta'] ?? 0),
        'ganancia_total' => floatval($row['ganancia_total'] ?? 0),
    ];
}
    public function procesarDespachoFisico($idMovimiento) {
        $this->db->begin_transaction();

        try {
            $sqlMov = "SELECT m.producto_id, m.almacen_origen_id, m.cantidad, m.referencia_id,
                              dv.id as det_venta_id, dv.precio_unitario as precio_pactado,
                              ev.id as entrega_id
                       FROM movimientos m
                       LEFT JOIN detalle_venta dv ON m.referencia_id = dv.venta_id AND m.producto_id = dv.producto_id
                       LEFT JOIN entregas_venta ev ON dv.venta_id = ev.venta_id
                       WHERE m.id = $idMovimiento";
            
            $resMov = $this->db->query($sqlMov);
            $mov = $resMov->fetch_assoc();

            if (!$mov) throw new Exception("Movimiento no encontrado.");

            $prod_id = $mov['producto_id'];
            $alm_id  = $mov['almacen_origen_id'];
            $cantidad_restante = floatval($mov['cantidad']);
            
            $entrega_venta_id = intval($mov['entrega_id'] ?? 0);
            $detalle_venta_id = intval($mov['det_venta_id'] ?? 0);
            $precio_pactado   = floatval($mov['precio_pactado'] ?? 0);

            $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario 
                         FROM lotes_stock 
                         WHERE producto_id = $prod_id 
                           AND almacen_id = $alm_id 
                           AND cantidad_actual > 0 
                           AND estado_lote = 'activo'
                         ORDER BY fecha_ingreso ASC";
            
            $resLotes = $this->db->query($sqlLotes);

            if ($resLotes->num_rows == 0) {
                throw new Exception("No hay lotes disponibles en este almacén.");
            }

            while ($cantidad_restante > 0 && $lote = $resLotes->fetch_assoc()) {
                $lote_id = $lote['id'];
                $stock_lote = floatval($lote['cantidad_actual']);
                $costo_historico = $lote['precio_compra_unitario'];

                $a_tomar = min($cantidad_restante, $stock_lote);
                $nuevo_stock_lote = $stock_lote - $a_tomar;
                $nuevo_estado = ($nuevo_stock_lote <= 0) ? 'agotado' : 'activo';

                $this->db->query("UPDATE lotes_stock SET cantidad_actual = $nuevo_stock_lote, estado_lote = '$nuevo_estado' WHERE id = $lote_id");

                $sqlSalida = "INSERT INTO lotes_movimientos_salida (lote_id, entrega_venta_id, detalle_venta_id, cantidad_salida, costo_compra_historico, precio_venta_pactado) 
                              VALUES ($lote_id, $entrega_venta_id, $detalle_venta_id, $a_tomar, $costo_historico, $precio_pactado)";
                
                if (!$this->db->query($sqlSalida)) { throw new Exception("Error al insertar salida de lote."); }

                $cantidad_restante -= $a_tomar;
            }

            $id_usuario =  $_SESSION['usuario_id'] ?? 0;
            if ($id_usuario <= 0) { throw new Exception("Error: Sesión de usuario no válida."); }

            $sqlPuente = "INSERT INTO registro_salida_lotes (movimiento_id, usuario_patio_id, usuario_despacho_id) 
                          VALUES ($idMovimiento, $id_usuario, $id_usuario)";

            if (!$this->db->query($sqlPuente)) { throw new Exception("Error en registro físico: " . $this->db->error); }

            $this->db->commit();
            return ['success' => true, 'message' => 'Despacho procesado y registrado correctamente.'];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // AJUSTE: SIMULACIÓN CON FACTORES
    public function simularDespachoLotes($idMovimiento) {
        try {
            $resMov = $this->db->query("
                SELECT m.producto_id, m.almacen_origen_id, m.cantidad, 
                       p.factor_conversion, p.unidad_reporte,p.unidad_medida
                FROM movimientos m 
                INNER JOIN productos p ON m.producto_id = p.id 
                WHERE m.id = $idMovimiento");
            
            $mov = $resMov->fetch_assoc();
            if (!$mov) throw new Exception("Movimiento no encontrado.");

            $prod_id = $mov['producto_id'];
            $alm_id  = $mov['almacen_origen_id'];
            $restante = floatval($mov['cantidad']);
            $factor = floatval($mov['factor_conversion'] ?: 1);

            $sql = "SELECT id, codigo_lote, cantidad_actual, fecha_ingreso 
                    FROM lotes_stock 
                    WHERE producto_id = $prod_id AND almacen_id = $alm_id 
                    AND cantidad_actual > 0 AND estado_lote = 'activo' 
                    ORDER BY fecha_ingreso ASC";
            
            $resLotes = $this->db->query($sql);
            $simulacion = [];

            while ($restante > 0 && $lote = $resLotes->fetch_assoc()) {
                $tomar = min($restante, floatval($lote['cantidad_actual']));
                $simulacion[] = [
                    'lote_id' => $lote['id'],
                    'codigo' => $lote['codigo_lote'],
                    'fecha_entrada' => date('d/m/Y', strtotime($lote['fecha_ingreso'])),
                    'cantidad_en_lote' => $lote['cantidad_actual'],
                    'cantidad_a_extraer' => $tomar,
                    'saldo_final' => $lote['cantidad_actual'] - $tomar
                ];
                $restante -= $tomar;
            }

            return [
                'success' => true, 
                'lotes' => $simulacion, 
                'total_solicitado' => $mov['cantidad'],
                'unidad_reporte' => $mov['unidad_reporte'],
                'factor_conversion' => $factor,
                'pendiente' => $restante
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // AJUSTE: IMPRESIÓN CON FACTORES
    public function obtenerDatosImpresion($idMovimiento) {
    $idMovimiento = intval($idMovimiento);
    $sql = "SELECT 
                m.id as movimiento_id,
                m.fecha as fecha_solicitud,
                m.cantidad as cantidad_total,
                p.nombre as producto,
                p.sku,
                p.unidad_reporte,
                p.unidad_medida,
                p.factor_conversion,
                a_orig.nombre as almacen_origen,
                u_patio.nombre as usuario_despacho,
                rsl.fecha_despacho,
                /* Obtenemos los lotes usando la relación lms -> entregas_venta -> movimientos */
                (SELECT GROUP_CONCAT(CONCAT(ls.codigo_lote, ' (', lms.cantidad_salida, ' pzas)') SEPARATOR '\n')
                 FROM lotes_movimientos_salida lms
                 INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                 INNER JOIN entregas_venta ev ON lms.entrega_venta_id = ev.id
                 WHERE ev.venta_id = m.referencia_id 
                 AND lms.detalle_venta_id IN (
                     SELECT dv.id FROM detalle_venta dv 
                     WHERE dv.venta_id = m.referencia_id AND dv.producto_id = m.producto_id
                 )
                 /* Filtramos para que solo muestre lo del despacho vinculado a este movimiento */
                 AND ev.fecha >= DATE_SUB(rsl.fecha_despacho, INTERVAL 1 MINUTE)
                ) as detalle_lotes
            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN almacenes a_orig ON m.almacen_origen_id = a_orig.id
            INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
            WHERE m.id = $idMovimiento";

    $res = $this->db->query($sql);
    $data = $res->fetch_assoc();

    if ($data) {
        $cant = floatval($data['cantidad_total']);
        $factor = floatval($data['factor_conversion'] ?: 1);
        
        if ($factor > 1 && $cant >= $factor) {
            $unidades = floor($cant / $factor);
            $resto = round($cant % $factor, 2);
            $data['cantidad_convertida'] = "$unidades " . $data['unidad_reporte'] . ($resto > 0 ? " + $resto " .$data['unidad_medida']  : "");
        } else {
            $data['cantidad_convertida'] = "$cant ".$data['unidad_medida'];
        }
    }
    return $data;
}
public function obtenerDatosVentaGananciaImpresion($idMovimiento) {
    $idMovimiento = intval($idMovimiento);

    $sql = "SELECT 
                m.id as movimiento_id,
                m.fecha as fecha_solicitud,
                m.cantidad as cantidad_total,
                p.nombre as producto,
                p.sku,
                p.unidad_reporte,
                p.factor_conversion,
                a_orig.nombre as almacen_origen,
                u_patio.nombre as usuario_despacho,
                rsl.fecha_despacho,
                
                (SELECT GROUP_CONCAT(
                    CONCAT(
                        ls.codigo_lote, '|', 
                        lms.cantidad_salida, '|', 
                        lms.costo_compra_historico, '|', 
                        lms.precio_venta_pactado
                    ) SEPARATOR '___'
                )
                 FROM lotes_movimientos_salida lms
                 INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                 INNER JOIN entregas_venta ev ON lms.entrega_venta_id = ev.id
                 WHERE ev.venta_id = m.referencia_id 
                 AND lms.detalle_venta_id IN (
                     SELECT dv.id FROM detalle_venta dv 
                     WHERE dv.venta_id = m.referencia_id AND dv.producto_id = m.producto_id
                 )
                ) as detalle_financiero,

                /* Totales */
                (SELECT SUM(lms.costo_compra_historico * lms.cantidad_salida) 
                 FROM lotes_movimientos_salida lms
                 INNER JOIN entregas_venta ev ON lms.entrega_venta_id = ev.id
                 WHERE ev.venta_id = m.referencia_id 
                 AND lms.detalle_venta_id IN (
                     SELECT dv.id FROM detalle_venta dv 
                     WHERE dv.venta_id = m.referencia_id AND dv.producto_id = m.producto_id
                 )
                ) as total_costo,

                (SELECT SUM(lms.precio_venta_pactado * lms.cantidad_salida) 
                 FROM lotes_movimientos_salida lms
                 INNER JOIN entregas_venta ev ON lms.entrega_venta_id = ev.id
                 WHERE ev.venta_id = m.referencia_id 
                 AND lms.detalle_venta_id IN (
                     SELECT dv.id FROM detalle_venta dv 
                     WHERE dv.venta_id = m.referencia_id AND dv.producto_id = m.producto_id
                 )
                ) as total_venta

            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN almacenes a_orig ON m.almacen_origen_id = a_orig.id
            INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
            WHERE m.id = $idMovimiento";

    $res = $this->db->query($sql);
    $data = $res->fetch_assoc();

    if ($data) {
        $cant = floatval($data['cantidad_total']);
        $factor = floatval($data['factor_conversion'] ?: 1);
        
        if ($factor > 1 && $cant >= $factor) {
            $unidades = floor($cant / $factor);
            $resto = round($cant % $factor, 2);
            $data['cantidad_convertida'] = "$unidades " . $data['unidad_reporte'] . ($resto > 0 ? " + $resto pzas" : "");
        } else {
            $data['cantidad_convertida'] = "$cant pzas";
        }

        $total_c = floatval($data['total_costo'] ?? 0);
        $total_v = floatval($data['total_venta'] ?? 0);
        $data['ganancia_neta'] = round($total_v - $total_c, 2);
    }
    return $data;
}
public function listarSoloDespachadosPatio($almacen_id = 0) {
    $sql = "SELECT 
                m.id as movimiento_id,
                v.folio as folio_venta,
                m.fecha as fecha_movimiento,
                c.nombre_comercial as cliente,
                p.nombre as producto,
                p.sku,
                p.unidad_medida,       -- Agregado: necesario para el cálculo
                p.unidad_reporte,
                p.factor_conversion,   -- Agregado: necesario para el cálculo
                m.almacen_origen_id,
                m.cantidad,
                a.nombre as almacen_origen,
                rsl.fecha_despacho, 
                u.nombre as despacho_por,
                1 as ya_despachado,
                IFNULL(trm.estado_reparto, 'pendiente') as estado_reparto
            FROM movimientos m
            INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON  v.id_cliente =c.id
            LEFT JOIN almacenes a ON m.almacen_origen_id = a.id
            LEFT JOIN usuarios u ON rsl.usuario_patio_id = u.id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            LEFT JOIN transmutacion_detalle td ON m.id = td.movimiento_id
            WHERE m.tipo = 'salida' 
              AND td.id IS NULL
              AND (v.id IS NULL OR v.estado_general = 'activa')
              AND (trm.estado_reparto IS NULL OR trm.estado_reparto != 'cancelado')";

    if (intval($almacen_id) > 0) {
        $sql .= " AND m.almacen_origen_id = " . intval($almacen_id);
    }

    $sql .= " ORDER BY rsl.fecha_despacho DESC";

    $res = $this->db->query($sql);
    $data = [];

    if($res) {
        while ($row = $res->fetch_assoc()) {
            $row['fecha_format'] = !empty($row['fecha_despacho']) 
                ? date('d/m/Y H:i', strtotime($row['fecha_despacho'])) 
                : 'S/F';

            // --- Lógica de Desglose ---
            $cantidad = floatval($row['cantidad']);
            $factor   = floatval($row['factor_conversion'] ?? 1);
            $u_rep    = $row['unidad_reporte'] ?: 'Unid.';
            $u_med    = $row['unidad_medida'] ?: 'Pz';

            if ($factor > 1) {
                $enteros   = (int) floor($cantidad / $factor);
                $sobrantes = fmod($cantidad, $factor);

                if ($sobrantes > 0) {
                    $row['cantidad_display'] = "{$enteros} {$u_rep} + {$sobrantes} {$u_med}";
                } else {
                    $row['cantidad_display'] = "{$enteros} {$u_rep}";
                }
            } else {
                $row['cantidad_display'] = "{$cantidad} {$u_med}";
            }

            $data[] = $row;
        }
    }
    return $data;
}
public function listarSoloDespachadosPatioFecha($almacen_id = 0, $fecha_inicio = null, $fecha_fin = null) {

    $sql = "SELECT 
                m.id as movimiento_id,
                v.folio as folio_venta,
                m.fecha as fecha_movimiento,
                c.nombre_comercial as cliente,
                p.nombre as producto,
                p.sku,
                p.unidad_medida,
                p.unidad_reporte,
                p.factor_conversion,
                m.almacen_origen_id,
                m.cantidad,
                a.nombre as almacen_origen,
                rsl.fecha_despacho,
                u.nombre as despacho_por,
                1 as ya_despachado,
                IFNULL(trm.estado_reparto, 'pendiente') as estado_reparto
            FROM movimientos m
            INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN almacenes a ON m.almacen_origen_id = a.id
            LEFT JOIN usuarios u ON rsl.usuario_patio_id = u.id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            LEFT JOIN transmutacion_detalle td ON m.id = td.movimiento_id
            WHERE m.tipo = 'salida'
              AND td.id IS NULL
              AND (v.id IS NULL OR v.estado_general = 'activa')
              AND (trm.estado_reparto IS NULL OR trm.estado_reparto != 'cancelado')";

    if (intval($almacen_id) > 0) {
        $sql .= " AND m.almacen_origen_id = " . intval($almacen_id);
    }

    if (!empty($fecha_inicio)) {
        $sql .= " AND DATE(rsl.fecha_despacho) >= '" . $this->db->real_escape_string($fecha_inicio) . "'";
    }

    if (!empty($fecha_fin)) {
        $sql .= " AND DATE(rsl.fecha_despacho) <= '" . $this->db->real_escape_string($fecha_fin) . "'";
    }

    $sql .= " ORDER BY rsl.fecha_despacho DESC";

    $res = $this->db->query($sql);
    $data = [];

    if ($res) {
        while ($row = $res->fetch_assoc()) {

            $row['fecha_format'] = !empty($row['fecha_despacho'])
                ? date('d/m/Y H:i', strtotime($row['fecha_despacho']))
                : 'S/F';

            $cantidad = floatval($row['cantidad']);
            $factor   = floatval($row['factor_conversion'] ?? 1);
            $u_rep    = $row['unidad_reporte'] ?: 'Unid.';
            $u_med    = $row['unidad_medida'] ?: 'Pz';

            if ($factor > 1) {
                $enteros   = (int) floor($cantidad / $factor);
                $sobrantes = fmod($cantidad, $factor);

                if ($sobrantes > 0) {
                    $row['cantidad_display'] = "{$enteros} {$u_rep} + {$sobrantes} {$u_med}";
                } else {
                    $row['cantidad_display'] = "{$enteros} {$u_rep}";
                }
            } else {
                $row['cantidad_display'] = "{$cantidad} {$u_med}";
            }

            $data[] = $row;
        }
    }

    return $data;
}
public function getDetalleParaDespacho($movimiento_id) {
    $sql = "SELECT 
                m.id AS movimiento_id,
                m.cantidad,
                p.nombre AS producto_nombre,
                p.unidad_reporte,
                p.unidad_medida,       -- <--- Agregado: Vital para el desglose en JS
                p.factor_conversion,
                v.folio AS folio_venta,
                c.nombre_comercial AS cliente_nombre,
                c.direccion AS cliente_direccion_fiscal,
                c.telefono AS cliente_telefono
            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            WHERE m.id = ? 
            LIMIT 1";
            
    $stmt = $this->db->prepare($sql);
    
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    // Pequeña validación de seguridad para el factor
    if ($resultado) {
        $resultado['factor_conversion'] = floatval($resultado['factor_conversion'] ?? 1);
        $resultado['cantidad'] = floatval($resultado['cantidad'] ?? 0);
    }

    return $resultado;
}

 public function iniciarRepartoPatio($datos) {
    try {
        $vehiculo_id   = intval($datos['vehiculo_id']);
        $chofer_id     = intval($datos['chofer_id']);
        $movimiento_id = intval($datos['movimiento_id']);
        $direccion     = !empty($datos['direccion_entrega']) ? $datos['direccion_entrega'] : 'Entrega en Obra';
        
        // 🔹 CORRECCIÓN: Verifica 'tripulante_id' primero (enviado por el controlador) y 'tripulantes' como respaldo
       
        // Recuperamos el folio que viene desde el controlador
        $folio_viaje   = $datos['folio_viaje'] ?? ''; 

        // --- VALIDACIÓN DE INTEGRIDAD EN TRANSPORTE ---
        $sqlCheck = "SELECT rp.id FROM transporte_rutas_puntos rp
                     INNER JOIN transporte_repartos_maestro trm ON rp.reparto_id = trm.id
                     WHERE trm.entrega_venta_id = ? 
                     AND trm.estado_reparto != 'cancelado' LIMIT 1";
        
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $movimiento_id);
        $stmtCheck->execute();
        
        if ($stmtCheck->get_result()->num_rows > 0) {
            throw new Exception("Ya existe una ruta programada para este despacho.");
        }

        $this->db->begin_transaction();

        // 1. Crear el Maestro del Reparto
        $sqlM = "INSERT INTO transporte_repartos_maestro (
                    vehiculo_id, 
                    usuario_encargado_id, 
                    entrega_venta_id, 
                    fecha_programada, 
                    estado_reparto
                ) VALUES (?, ?, ?, CURDATE(), 'completado')";
        
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param("iii", $vehiculo_id, $chofer_id, $movimiento_id);
        $stmtM->execute();
        $reparto_id = $this->db->insert_id;

        // Obtener el entrega_id correspondiente al movimiento
        $sqlmov = "SELECT entrega_id
                   FROM movimientos m
                   WHERE m.id = ?
                   LIMIT 1";

        $stmtmov = $this->db->prepare($sqlmov);
        $stmtmov->bind_param("i", $movimiento_id);
        $stmtmov->execute();

        $result = $stmtmov->get_result();
        $row = $result->fetch_assoc();
        $entrega_id = $row['entrega_id'] ?? null;

        // 1.1 Registro en la tabla de consolidación
        $sqlC = "INSERT INTO transporte_consolidacion (
                    viaje_folio, 
                    vehiculo_id, 
                    reparto_id, 
                    estatus_consolidado,
                    entrega_id
                ) VALUES (?, ?, ?, 'cerrado', ?)";

        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param("siii", $folio_viaje, $vehiculo_id, $reparto_id, $entrega_id);
        $stmtC->execute();

        // 2. Insertar el Punto de Ruta
        $sqlP = "INSERT INTO transporte_rutas_puntos (
                    reparto_id, 
                    orden_visita, 
                    descripcion_punto, 
                    estado_punto,
                    entrega_id
                ) VALUES (?, 1, ?, 'visitado', ?)";

        $stmtP = $this->db->prepare($sqlP);
        $stmtP->bind_param("isi", $reparto_id, $direccion, $entrega_id);
        $stmtP->execute();

        // 3. Registrar Tripulación (Solo 1 tripulante válido y distinto al chofer)
        

        $this->db->commit();
        return $reparto_id;

    } catch (Exception $e) {
        if (isset($this->db)) {
            try { $this->db->rollback(); } catch (Throwable $t) {}
        }
        throw $e;
    }
}
public function cajaRapidaEntregarEnPatioCliente($datos) {

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {

        $vehiculo_virtual_id = 999;

        $movimiento_id       = intval($datos['movimiento_id'] ?? 0);
        $chofer_id           = intval($datos['chofer_id'] ?? 0);
        $usuario_operador_id = intval($datos['usuario_sistema_id'] ?? 0);

        $observaciones = !empty($datos['observaciones'])
            ? trim($datos['observaciones'])
            : 'Entrega Directa en Patio';

        if ($movimiento_id <= 0) {
            throw new Exception("Movimiento inválido.");
        }

        if ($usuario_operador_id <= 0) {
            throw new Exception("Usuario operador inválido.");
        }

        // =========================================================
        // VALIDAR VEHÍCULO
        // =========================================================

        $sqlVeh = "SELECT id FROM transporte_vehiculos WHERE id = ? LIMIT 1";

        $stmtVeh = $this->db->prepare($sqlVeh);
        $stmtVeh->bind_param("i", $vehiculo_virtual_id);
        $stmtVeh->execute();

        $stmtVeh->store_result();

        if ($stmtVeh->num_rows <= 0) {
            throw new Exception("El vehículo virtual ID {$vehiculo_virtual_id} no existe.");
        }

        // =========================================================
        // ARMAR TRIPULACIÓN
        // =========================================================

        $tripulantes = (
            isset($datos['tripulantes']) &&
            is_array($datos['tripulantes'])
        ) ? $datos['tripulantes'] : [];

        if ($chofer_id > 0) {
            array_unshift($tripulantes, $chofer_id);
        }

        $tripulantes = array_unique(
            array_map('intval', $tripulantes)
        );

        // =========================================================
        // VALIDAR DUPLICADO
        // =========================================================

        $sqlCheck = "
            SELECT id
            FROM transporte_repartos_maestro
            WHERE entrega_venta_id = ?
            AND estado_reparto != 'cancelado'
            LIMIT 1
        ";

        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $movimiento_id);
        $stmtCheck->execute();

        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {

            return [
                'success' => false,
                'message' => 'Movimiento ya procesado anteriormente.'
            ];
        }

        // =========================================================
        // INICIAR TRANSACCIÓN
        // =========================================================

        $this->db->begin_transaction();

        // =========================================================
        // INSERT MAESTRO
        // =========================================================

        $sqlM = "
            INSERT INTO transporte_repartos_maestro (
                vehiculo_id,
                usuario_encargado_id,
                entrega_venta_id,
                fecha_programada,
                estado_reparto,
                hora_llegada_real
            )
            VALUES (
                ?,
                ?,
                ?,
                CURDATE(),
                'completado',
                NOW()
            )
        ";

        $stmtM = $this->db->prepare($sqlM);

        if (!$stmtM) {
            throw new Exception(
                "Error prepare maestro: " . $this->db->error
            );
        }

        $stmtM->bind_param(
            "iii",
            $vehiculo_virtual_id,
            $usuario_operador_id,
            $movimiento_id
        );

        if (!$stmtM->execute()) {
            throw new Exception(
                "Error execute maestro: " . $stmtM->error
            );
        }

        $reparto_id = intval($this->db->insert_id);

        if ($reparto_id <= 0) {
            throw new Exception("No se generó reparto_id.");
        }

        // =========================================================
        // INSERT PUNTO RUTA
        // =========================================================

        $sqlP = "
            INSERT INTO transporte_rutas_puntos (
                reparto_id,
                orden_visita,
                descripcion_punto,
                estado_punto
            )
            VALUES (?, 1, ?, 'visitado')
        ";

        $descripcion = "PATIO: " . $observaciones;

        $stmtP = $this->db->prepare($sqlP);

        $stmtP->bind_param(
            "is",
            $reparto_id,
            $descripcion
        );

        if (!$stmtP->execute()) {
            throw new Exception(
                "Error punto ruta: " . $stmtP->error
            );
        }

        // =========================================================
        // INSERT TRIPULANTES
        // =========================================================

        if (!empty($tripulantes)) {

            $sqlT = "
                INSERT INTO transporte_tripulantes_detalle (
                    reparto_id,
                    usuario_id
                )
                VALUES (?, ?)
            ";

            $stmtT = $this->db->prepare($sqlT);

            foreach ($tripulantes as $uid) {

                $uid = intval($uid);

                if ($uid <= 0) {
                    continue;
                }

                $stmtT->bind_param(
                    "ii",
                    $reparto_id,
                    $uid
                );

                if (!$stmtT->execute()) {
                    throw new Exception(
                        "Error tripulante {$uid}: " . $stmtT->error
                    );
                }
            }
        }

        // =========================================================
        // LIBERAR VEHÍCULO
        // =========================================================

        $sqlVehiculo = "
            UPDATE transporte_vehiculos
            SET estado_unidad = 'disponible'
            WHERE id = ?
        ";

        $stmtVU = $this->db->prepare($sqlVehiculo);
        $stmtVU->bind_param("i", $vehiculo_virtual_id);

        if (!$stmtVU->execute()) {
            throw new Exception(
                "Error actualizando vehículo: " . $stmtVU->error
            );
        }

        // =========================================================
        // COMMIT
        // =========================================================

        $this->db->commit();

        return [
            'success'   => true,
            'reparto_id'=> $reparto_id,
            'message'   => 'Entrega en patio registrada correctamente.'
        ];

    } catch (Exception $e) {

        if ($this->db && $this->db->errno == 0) {
            $this->db->rollback();
        }

        error_log(
            "ERROR cajaRapidaEntregarEnPatioCliente: " .
            $e->getMessage()
        );

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
public function listarIdsPendientesPorVenta($venta_id) {
    $venta_id = intval($venta_id);

    // Solo pedimos m.id para optimizar la consulta
    $sql = "SELECT m.id 
            FROM movimientos m
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            WHERE m.referencia_id = $venta_id 
              AND m.tipo = 'salida'
              AND rsl.id IS NULL
              AND (trm.id IS NULL OR trm.estado_reparto = 'cancelado')
            ORDER BY m.id ASC";

    $resultado = $this->db->query($sql);
    $ids = [];

    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            // Guardamos solo el ID como entero
            $ids[] = intval($row['id']);
        }
    }
    
    // Retorna algo como: [193, 194, 198]
    return $ids;
}
public function simularDespachoLotesMasivo($idsMovimientos) {
    try {
        if (empty($idsMovimientos)) throw new Exception("No hay IDs para procesar.");
        
        // Limpiamos los IDs para evitar inyecciones
        $idsClean = array_map('intval', $idsMovimientos);
        $idsString = implode(',', $idsClean);

        // 1. Obtenemos todos los movimientos de una sola vez
        $sqlMovs = "SELECT m.id, m.producto_id, m.almacen_origen_id, m.cantidad, 
                           p.nombre as prod_nombre, p.factor_conversion, p.unidad_reporte 
                    FROM movimientos m 
                    INNER JOIN productos p ON m.producto_id = p.id 
                    WHERE m.id IN ($idsString)";
        
        $resMovs = $this->db->query($sqlMovs);
        $resultados = [];

        while ($mov = $resMovs->fetch_assoc()) {
            $movId    = $mov['id'];
            $prodId   = $mov['producto_id'];
            $almId    = $mov['almacen_origen_id'];
            $restante = floatval($mov['cantidad']);
            $factor   = floatval($mov['factor_conversion'] ?: 1);

            // 2. Buscamos lotes para ESTE producto y almacén (FIFO)
            $sqlLotes = "SELECT id, codigo_lote, cantidad_actual, fecha_ingreso 
                         FROM lotes_stock 
                         WHERE producto_id = $prodId AND almacen_id = $almId 
                         AND cantidad_actual > 0 AND estado_lote = 'activo' 
                         ORDER BY fecha_ingreso ASC";
            
            $resLotes = $this->db->query($sqlLotes);
            $lotesParaEsteMov = [];

            while ($restante > 0 && $lote = $resLotes->fetch_assoc()) {
                $cantidadEnLote = floatval($lote['cantidad_actual']);
                $tomar = min($restante, $cantidadEnLote);

                $lotesParaEsteMov[] = [
                    'lote_id'            => $lote['id'],
                    'codigo'             => $lote['codigo_lote'],
                    'fecha_entrada'      => date('d/m/Y', strtotime($lote['fecha_ingreso'])),
                    'cantidad_en_lote'   => $cantidadEnLote,
                    'cantidad_a_extraer' => $tomar,
                    'saldo_final'        => $cantidadEnLote - $tomar
                ];
                $restante -= $tomar;
            }

            // 3. Agrupamos el resultado por ID de movimiento
            $resultados[] = [
                'movimiento_id'     => $movId,
                'producto'          => $mov['prod_nombre'],
                'total_solicitado'  => $mov['cantidad'],
                'unidad_reporte'    => $mov['unidad_reporte'],
                'factor_conversion' => $factor,
                'lotes'             => $lotesParaEsteMov,
                'pendiente'         => $restante // Si es > 0, falta stock
            ];
        }

        return [
            'success' => true,
            'data'    => $resultados
        ];

    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
public function getDatosPorVehiculo($vehiculo_id)
{
    $sql = "SELECT 
                rm.id AS reparto_id,
                rm.vehiculo_id,
                rm.usuario_encargado_id,

                u_enc.nombre AS encargado_nombre,

                td.usuario_id AS tripulante_id,
                t_tri.nombre AS tripulante_nombre,
                td.rol_secundario

            FROM transporte_repartos_maestro rm

            LEFT JOIN trabajadores u_enc 
                ON u_enc.id = rm.usuario_encargado_id

            LEFT JOIN transporte_tripulantes_detalle td 
                ON td.reparto_id = rm.id

            LEFT JOIN trabajadores t_tri 
                ON t_tri.id = td.usuario_id

            WHERE rm.vehiculo_id = ?
            and rm.estado_reparto='en_transito'
            ORDER BY rm.id DESC";
    $stmt = $this->db->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("i", $vehiculo_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) return false;

    $encargado = null;
    $tripulantes = [];

    while ($row = $result->fetch_assoc()) {

        // 👤 encargado (solo una vez)
        if (!$encargado && $row['usuario_encargado_id']) {
            $encargado = [
                "id" => $row['usuario_encargado_id'],
                "nombre" => $row['encargado_nombre']
            ];
        }

        // 👥 tripulantes
        if ($row['tripulante_id']) {
            $tripulantes[] = [
                "id" => $row['tripulante_id'],
                "nombre" => $row['tripulante_nombre'],
                "rol" => $row['rol_secundario']
            ];
        }
    }

    return [
        "encargado" => $encargado,
        "tripulantes" => $tripulantes
    ];
}
   public function obtener_almecen_id($id) {

    $sql = "SELECT almacen_id 
            FROM ventas
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    return $stmt
        ->get_result()
        ->fetch_assoc();
}

public function procesarDespachoFisicoMasivo($idsMovimientos) {
    if (empty($idsMovimientos)) {
        return ['success' => false, 'message' => 'No se proporcionaron IDs para procesar.'];
    }

    $this->db->begin_transaction();

    try {
        $id_usuario = $_SESSION['usuario_id'] ?? 0;
        if ($id_usuario <= 0) throw new Exception("Error: Sesión de usuario no válida.");

        foreach ($idsMovimientos as $idMovimiento) {
            $idMovimiento = intval($idMovimiento);

            // 1. Obtener info del movimiento y su relación con la venta
            $sqlMov = "SELECT m.id, m.producto_id, m.almacen_origen_id, m.cantidad,
                              dv.id as det_venta_id, dv.precio_unitario as precio_pactado,
                              ev.id as entrega_id
                       FROM movimientos m
                       LEFT JOIN detalle_venta dv ON m.referencia_id = dv.venta_id AND m.producto_id = dv.producto_id
                       LEFT JOIN entregas_venta ev ON dv.venta_id = ev.venta_id
                       WHERE m.id = $idMovimiento LIMIT 1";
            
            $resMov = $this->db->query($sqlMov);
            $mov = $resMov->fetch_assoc();

            if (!$mov) throw new Exception("Movimiento ID $idMovimiento no encontrado.");

            $prod_id           = $mov['producto_id'];
            $alm_id            = $mov['almacen_origen_id'];
            $cantidad_restante = floatval($mov['cantidad']);
            $entrega_id        = intval($mov['entrega_id'] ?? 0);
            $det_venta_id      = intval($mov['det_venta_id'] ?? 0);
            $precio_pactado    = floatval($mov['precio_pactado'] ?? 0);

            // 2. Buscar lotes disponibles (FIFO: El más viejo primero)
            $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario 
                         FROM lotes_stock 
                         WHERE producto_id = $prod_id 
                           AND almacen_id = $alm_id 
                           AND cantidad_actual > 0 
                           AND estado_lote = 'activo'
                         ORDER BY fecha_ingreso ASC, id ASC";
            
            $resLotes = $this->db->query($sqlLotes);

            if ($resLotes->num_rows == 0 && $cantidad_restante > 0) {
                throw new Exception("Sin stock en lotes para el producto ID: $prod_id");
            }

            // 3. Descontar de lotes hasta agotar la cantidad del movimiento
            while ($cantidad_restante > 0 && $lote = $resLotes->fetch_assoc()) {
                $lote_id         = $lote['id'];
                $stock_lote      = floatval($lote['cantidad_actual']);
                $costo_historico = $lote['precio_compra_unitario'];

                $a_tomar          = min($cantidad_restante, $stock_lote);
                $nuevo_stock_lote = $stock_lote - $a_tomar;
                $nuevo_estado     = ($nuevo_stock_lote <= 0) ? 'agotado' : 'activo';

                // Actualizar el lote
                $this->db->query("UPDATE lotes_stock 
                                 SET cantidad_actual = $nuevo_stock_lote, estado_lote = '$nuevo_estado' 
                                 WHERE id = $lote_id");

                // Registrar la salida específica del lote
                $sqlSalida = "INSERT INTO lotes_movimientos_salida 
                              (lote_id, entrega_venta_id, detalle_venta_id, cantidad_salida, costo_compra_historico, precio_venta_pactado) 
                              VALUES ($lote_id, $entrega_id, $det_venta_id, $a_tomar, $costo_historico, $precio_pactado)";
                
                if (!$this->db->query($sqlSalida)) throw new Exception("Error al insertar salida de lote.");

                $cantidad_restante -= $a_tomar;
            }

            // 4. Insertar en el "Puente" para marcar que el bodeguero ya lo entregó físicamente
            // Esto es lo que hace que desaparezca de la lista de pendientes
            $sqlPuente = "INSERT INTO registro_salida_lotes (movimiento_id, usuario_patio_id, usuario_despacho_id) 
                          VALUES ($idMovimiento, $id_usuario, $id_usuario)";

            if (!$this->db->query($sqlPuente)) throw new Exception("Error en registro físico: " . $this->db->error);
        }

        $this->db->commit();
        return ['success' => true, 'message' => count($idsMovimientos) . ' productos despachados correctamente.'];

    } catch (Exception $e) {
        $this->db->rollback();
        return ['success' => false, 'message' => "Error: " . $e->getMessage()];
    }
}
public function obtenerDatosVentaCompletaImpresion($idVenta) {
    $idVenta = intval($idVenta);
    $sql = "SELECT 
                v.id as venta_id,
                v.folio,
                v.fecha as fecha_venta,
                dv.id as detalle_id,
                p.nombre as producto,
                p.sku,
                p.unidad_reporte,
                p.unidad_medida,
                p.factor_conversion,
                dv.cantidad as cantidad_pactada,
                /* Obtenemos los lotes vinculados a este detalle de venta específico */
                (SELECT GROUP_CONCAT(CONCAT(ls.codigo_lote, ' (', lms.cantidad_salida, ' pzas)') SEPARATOR '\n')
                 FROM lotes_movimientos_salida lms
                 INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                 WHERE lms.detalle_venta_id = dv.id
                ) as detalle_lotes
            FROM ventas v
            INNER JOIN detalle_venta dv ON v.id = dv.venta_id
            INNER JOIN productos p ON dv.producto_id = p.id
            WHERE v.id = $idVenta";

    $res = $this->db->query($sql);
    $productos = [];

    while ($row = $res->fetch_assoc()) {
        $cant = floatval($row['cantidad_pactada']);
        $factor = floatval($row['factor_conversion'] ?: 1);
        
        // Formateo de cantidad estilo "10 Bultos + 2 pzas"
        if ($factor > 1 && $cant >= $factor) {

    $unidades = floor($cant / $factor);
    $resto = round($cant % $factor, 2);

    $row['cantidad_convertida'] =
        "$unidades " . $row['unidad_reporte'] .
        ($resto > 0
            ? " + " . $resto . " " . $row['unidad_medida']
            : "");

} else {
            $row['cantidad_convertida'] = "$cant pzas";
        }
        $productos[] = $row;
    }
    return $productos;
}
public function obtenerAuditoriaFinancieraVenta($idVenta) {
    $idVenta = intval($idVenta);
    $sql = "SELECT 
                p.id as producto_id,
                p.nombre as producto,
                p.sku,
                p.factor_conversion,
                dv.cantidad as cantidad_total,
                p.unidad_reporte,
                p.unidad_medida,
                p.factor_conversion,
                /* Detalle de lotes financiero */
                (SELECT GROUP_CONCAT(
                    CONCAT(ls.codigo_lote, '|', lms.cantidad_salida, '|', lms.costo_compra_historico, '|', lms.precio_venta_pactado, '|', lms.fecha_movimiento) 
                    SEPARATOR '___')
                 FROM lotes_movimientos_salida lms
                 INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                 WHERE lms.detalle_venta_id = dv.id
                ) as detalle_financiero,
                /* Totales por producto */
                (SELECT SUM(lms.costo_compra_historico * lms.cantidad_salida) 
                 FROM lotes_movimientos_salida lms WHERE lms.detalle_venta_id = dv.id) as costo_total_prod,
                (SELECT SUM(lms.precio_venta_pactado * lms.cantidad_salida) 
                 FROM lotes_movimientos_salida lms WHERE lms.detalle_venta_id = dv.id) as venta_total_prod
            FROM detalle_venta dv
            INNER JOIN productos p ON dv.producto_id = p.id
            WHERE dv.venta_id = $idVenta";

    $res = $this->db->query($sql);
    $dataReporte = [
        'productos' => [],
        'gran_total_costo' => 0,
        'gran_total_venta' => 0,
        'ganancia_neta_total' => 0
    ];

    while ($row = $res->fetch_assoc()) {
        $costoProd = floatval($row['costo_total_prod'] ?? 0);
        $ventaProd = floatval($row['venta_total_prod'] ?? 0);
        
        $row['ganancia_prod'] = round($ventaProd - $costoProd, 2);
        
        // Sumamos al global de la venta
        $dataReporte['gran_total_costo'] += $costoProd;
        $dataReporte['gran_total_venta'] += $ventaProd;
        
        $dataReporte['productos'][] = $row;
    }

    $dataReporte['ganancia_neta_total'] = round($dataReporte['gran_total_venta'] - $dataReporte['gran_total_costo'], 2);
    
    return $dataReporte;
}
public function cancelarDespachoFisico($idMovimiento) {
    $this->db->begin_transaction();
    $idMov = intval($idMovimiento);

    try {
        // Buscamos los lotes que salieron asociados a este movimiento específico
        // a través de la tabla registro_salida_lotes
        $sqlLotes = "SELECT lms.id, lms.lote_id, lms.cantidad_salida 
                     FROM lotes_movimientos_salida lms
                     INNER JOIN registro_salida_lotes rsl ON lms.entrega_venta_id = (
                         SELECT ev.id FROM entregas_venta ev 
                         INNER JOIN movimientos m ON ev.venta_id = m.referencia_id 
                         WHERE m.id = $idMov LIMIT 1
                     )
                     WHERE rsl.movimiento_id = $idMov";
        
        $res = $this->db->query($sqlLotes);

        while ($row = $res->fetch_assoc()) {
            // 1. Devolver cantidad al stock real
            $this->db->query("UPDATE lotes_stock 
                             SET cantidad_actual = cantidad_actual + {$row['cantidad_salida']}, 
                                 estado_lote = 'activo' 
                             WHERE id = {$row['lote_id']}");

            // 2. Eliminar el desglose de salida de ese lote
            $this->db->query("DELETE FROM lotes_movimientos_salida WHERE id = {$row['id']}");
        }

        // 3. Eliminar el registro que confirma que el despacho se hizo
        $this->db->query("DELETE FROM registro_salida_lotes WHERE movimiento_id = $idMov");

        $this->db->commit();
        return ['success' => true, 'message' => 'Inventario restaurado con éxito.'];

    } catch (Exception $e) {
        $this->db->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
}