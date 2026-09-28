<?php

date_default_timezone_set('America/Mexico_City');
// El controlador ya definió $almacen_usuario y $conexion

class MovimientoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Obtiene el historial basándose en la presencia de usuario_recibe_id para determinar el estado
     */
    public function obtenerHistorial($filtros, $almacen_usuario_sesion) {
        $periodo = $filtros['periodo'] ?? 'hoy';
        $tipo = $filtros['tipo'] ?? '';
        $f_inicio_user = $filtros['f_inicio'] ?? '';
        $f_fin_user = $filtros['f_fin'] ?? '';
        
        $almacen_filtro = intval($filtros['almacen_id'] ?? 0);
        $target_almacen = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_filtro;

        $hoy = date('Y-m-d');
        $inicio = $hoy; $fin = $hoy;

        if ($periodo !== 'personalizado') {
            switch ($periodo) {
                case 'ayer': $inicio = date('Y-m-d', strtotime('-1 day')); $fin = $inicio; break;
                case 'semana': $inicio = date('Y-m-d', strtotime('-7 days')); break;
                case 'mes': $inicio = date('Y-m-01'); break;
            }
        } else {
            $inicio = !empty($f_inicio_user) ? $f_inicio_user : $hoy;
            $fin = !empty($f_fin_user) ? $f_fin_user : $hoy;
        }

        $where = "WHERE DATE(m.fecha) BETWEEN '$inicio' AND '$fin'";
        
        if (!empty($tipo)) {
            $where .= " AND m.tipo = '" . $this->db->real_escape_string($tipo) . "'";
        }

        if ($target_almacen > 0) {
            $where .= " AND (m.almacen_origen_id = $target_almacen OR m.almacen_destino_id = $target_almacen)";
        }

        // Consulta usando tus nombres de columna actuales
        $sql = "SELECT 
                    m.*, 
                    p.nombre as prod_nombre, p.sku, p.factor_conversion, p.unidad_reporte,p.unidad_medida,
                    a1.nombre as origen_nombre, a2.nombre as destino_nombre, 
                    u1.nombre as usuario_nombre, u3.nombre as usuario_recibe_nombre
                FROM movimientos m 
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
                LEFT JOIN almacenes a2 ON m.almacen_destino_id = a2.id
                LEFT JOIN usuarios u1 ON m.usuario_registra_id = u1.id
                LEFT JOIN usuarios u3 ON m.usuario_recibe_id = u3.id
                $where 
                ORDER BY m.fecha DESC";

        $resultado = $this->db->query($sql);
        $data = [];

        $config_estilos = [
            'entrada'  => ['color' => 'success', 'label' => 'Entrada'],
            'salida'   => ['color' => 'danger',  'label' => 'Salida'],
            'traspaso' => ['color' => 'primary', 'label' => 'Traspaso'],
            'ajuste'   => ['color' => 'warning', 'label' => 'Ajuste']
        ];

        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                $tipo_key = strtolower($row['tipo']);
                
                // LÓGICA SIN TABLA MODIFICADA:
                // Si el tipo es traspaso y usuario_recibe_id es nulo/vacío, está pendiente.
                $es_traspaso = ($tipo_key === 'traspaso');
                $esta_pendiente = ($es_traspaso && (empty($row['usuario_recibe_id']) || $row['usuario_recibe_id'] == 0));

                $color = $config_estilos[$tipo_key]['color'] ?? 'secondary';
                $label = $config_estilos[$tipo_key]['label'] ?? $row['tipo'];

                if ($esta_pendiente) {
                    $color = 'info';
                    $label = 'Traspaso (En tránsito)';
                }

                $data[] = [
                    'id'                => $row['id'],
                    'fecha_format'      => date('d/m/Y H:i', strtotime($row['fecha'])),
                    'producto'          => $row['prod_nombre'],
                    'sku'               => $row['sku'],
                    'tipo'              => $label,
                    'color'             => $color,
                    'es_pendiente'      => $esta_pendiente,
                    'cantidad'          => $row['cantidad'],
                    'factor_conversion' => $row['factor_conversion'] ?? 1,
                    'unidad_reporte'    => $row['unidad_reporte'] ?? 'PZA',
                    'unidad_medida'     =>$row['unidad_medida'] ?? 'PZA',
                    'origen'            => $row['origen_nombre'] ?? '---',
                    'destino'           => $row['destino_nombre'] ?? '---',
                    'almacen_origen_id' => $row['almacen_origen_id'],
                    'almacen_destino_id'=> $row['almacen_destino_id'],
                    'u_reg'             => $row['usuario_nombre'] ?? 'Sist.',
                    'u_rec'             => $row['usuario_recibe_nombre'] ?? '---',
                    'obs'               => $row['observaciones'] ?? ''
                ];
            }
        }
        return $data;
    }

    /**
     * Procesa la aceptación usando el campo usuario_recibe_id como bandera de "completado"
     */
    public function confirmarRecepcionTraspaso($idMovimiento, $idUsuario) {
        $this->db->begin_transaction();

        try {
            // 1. Validar que el movimiento existe y NO tiene receptor aún
            $sqlMov = "SELECT producto_id, almacen_destino_id, cantidad, usuario_recibe_id 
                       FROM movimientos WHERE id = $idMovimiento FOR UPDATE";
            $resMov = $this->db->query($sqlMov);
            $mov = $resMov->fetch_assoc();

            if (!$mov) {
                throw new Exception("El movimiento no existe.");
            }
            if (!empty($mov['usuario_recibe_id']) && $mov['usuario_recibe_id'] > 0) {
                throw new Exception("Este traspaso ya fue recibido anteriormente.");
            }

            $producto_id = $mov['producto_id'];
            $almacen_id  = $mov['almacen_destino_id'];
            $cantidad    = $mov['cantidad'];

            // 2. Actualizar stock en el almacén destino
            // Usamos tu lógica de ON DUPLICATE KEY para asegurar que el registro exista
            $sqlStock = "INSERT INTO stock_almacen (producto_id, almacen_id, cantidad) 
                         VALUES ($producto_id, $almacen_id, $cantidad) 
                         ON DUPLICATE KEY UPDATE cantidad = cantidad + $cantidad";
            
            if (!$this->db->query($sqlStock)) {
                throw new Exception("Error al actualizar el inventario de destino.");
            }

            // 3. Registrar quién recibe y cuándo (esto "cierra" el movimiento según nuestra lógica)
            $ahora = date('Y-m-d H:i:s');
            $sqlUpdate = "UPDATE movimientos SET 
                            usuario_recibe_id = $idUsuario,
                            fecha_recepcion = '$ahora' 
                          WHERE id = $idMovimiento";
            
            if (!$this->db->query($sqlUpdate)) {
                throw new Exception("Error al registrar la recepción del movimiento.");
            }

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function obtenerIdMovimientoPorVenta($venta_id) {
    $ids = []; // Array para almacenar los movimientos
    
    $sql = "SELECT m.id 
            FROM movimientos m 
            INNER JOIN ventas v ON m.referencia_id = v.id 
            WHERE v.id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $venta_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Recorremos todos los resultados, no solo el primero
    while ($fila = $resultado->fetch_assoc()) {
        $ids[] = $fila['id'];
    }
    
    return $ids; // Ahora regresa un array, ej: [45, 46, 47]
}
public function obtenerMovimientosPorRol($usuario_id, $rol_id, $almacen_id = null)
{
    try {

        $response = [
            'arribos' => [],
            'envios'  => []
        ];

        // 🔥 ADMIN = almacen_id 0
        $esAdmin = (intval($almacen_id) === 0);

        // 🔥 SI NO ES ADMIN Y NO TIENE ALMACÉN
        if (!$esAdmin && !$almacen_id) {

            return [
                'status' => true,
                'data' => $response
            ];
        }

        $id_almacen = intval($almacen_id);

        // =====================================================
        // 🔹 ARRIBOS
        // =====================================================

        $whereArribos = "";

        if (!$esAdmin) {

            $whereArribos = "
                AND m.almacen_destino_id = $id_almacen
            ";
        }

        $sqlArribos = "
            SELECT 
                m.id, 
                m.fecha, 
                p.nombre AS producto, 
                p.sku,
                p.factor_conversion, 
                p.unidad_reporte, 
                m.cantidad, 
                ao.nombre AS origen, 
                u.nombre AS enviado_por,
                m.referencia_id

            FROM movimientos m

            JOIN productos p 
                ON m.producto_id = p.id

            JOIN almacenes ao 
                ON m.almacen_origen_id = ao.id

            JOIN usuarios u 
                ON m.usuario_envia_id = u.id

            WHERE m.tipo = 'traspaso' 
            AND m.usuario_recibe_id IS NULL

            $whereArribos

            ORDER BY m.fecha DESC
        ";

        $resA = $this->db->query($sqlArribos);

        if ($resA) {

            while ($row = $resA->fetch_assoc()) {

                $row['cantidad'] = (float)$row['cantidad'];

                $row['factor_conversion'] = (float)$row['factor_conversion'];

                $response['arribos'][] = $row;
            }
        }

        // =====================================================
        // 🔹 ENVÍOS
        // =====================================================

        $whereEnvios = "";

        if (!$esAdmin) {

            $whereEnvios = "
                AND m.almacen_origen_id = $id_almacen
            ";
        }

        $sqlEnvios = "
            SELECT 
                m.id, 
                m.fecha, 
                p.nombre AS producto,
                p.factor_conversion, 
                p.unidad_reporte, 
                m.cantidad, 
                ad.nombre AS destino, 
                m.usuario_recibe_id

            FROM movimientos m

            JOIN productos p 
                ON m.producto_id = p.id

            JOIN almacenes ad 
                ON m.almacen_destino_id = ad.id

            WHERE m.tipo = 'traspaso'

            $whereEnvios

            ORDER BY m.fecha DESC

            LIMIT 20
        ";

        $resE = $this->db->query($sqlEnvios);

        if ($resE) {

            while ($row = $resE->fetch_assoc()) {

                $row['cantidad'] = (float)$row['cantidad'];

                $row['factor_conversion'] = (float)$row['factor_conversion'];

                $row['estado'] = ($row['usuario_recibe_id'])
                    ? 'Completado'
                    : 'En Tránsito';

                $response['envios'][] = $row;
            }
        }

        return [
            'status' => true,
            'data' => $response
        ];

    } catch (Exception $e) {

        error_log(
            "ERROR obtenerMovimientosPorRol: " .
            $e->getMessage()
        );

        return [
            'status' => false,
            'msg' => 'Error al obtener movimientos'
        ];
    }
}
public function registrarTraspaso($producto_id, $origen_id, $destino_id, $cantidad, $usuario_id, $obs = '')
{
    try {

        $this->db->begin_transaction();

        // 🔹 1. Verificar stock (bloqueo FOR UPDATE)
        $stmtStock = $this->db->prepare("
            SELECT stock 
            FROM inventario 
            WHERE producto_id = ? 
            AND almacen_id = ? 
            FOR UPDATE
        ");

        $stmtStock->bind_param("ii", $producto_id, $origen_id);
        $stmtStock->execute();
        $resStock = $stmtStock->get_result()->fetch_assoc();

        if (!$resStock || $resStock['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente en el almacén de origen.");
        }

        // 🔹 2. Restar stock origen
        $stmtOut = $this->db->prepare("
            UPDATE inventario 
            SET stock = stock - ? 
            WHERE producto_id = ? 
            AND almacen_id = ?
        ");

        $stmtOut->bind_param("dii", $cantidad, $producto_id, $origen_id);
        $stmtOut->execute();

        // 🔹 3. Registrar movimiento
        $tipo = 'traspaso';

        $stmtMov = $this->db->prepare("
            INSERT INTO movimientos 
            (producto_id, tipo, cantidad, almacen_origen_id, almacen_destino_id, usuario_registra_id, usuario_envia_id, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtMov->bind_param(
            "isdiiiis",
            $producto_id,
            $tipo,
            $cantidad,
            $origen_id,
            $destino_id,
            $usuario_id,
            $usuario_id,
            $obs
        );

        $stmtMov->execute();

        // 🔹 4. Commit
        $this->db->commit();

        return [
            'status' => true,
            'msg' => 'Envío registrado correctamente'
        ];

    } catch (Exception $e) {

        $this->db->rollback();

        error_log("ERROR registrarTraspaso: " . $e->getMessage());

        return [
            'status' => false,
            'msg' => $e->getMessage()
        ];
    }
}
public function recibirTraspaso($movimiento_id, $usuario_id, $rol_id)
{
    try {

        $this->db->begin_transaction();

        // 🔹 1. Obtener movimiento
        $stmt = $this->db->prepare("
            SELECT producto_id, cantidad, almacen_origen_id, almacen_destino_id, usuario_recibe_id, referencia_id
            FROM movimientos 
            WHERE id = ? 
            FOR UPDATE
        ");
        $stmt->bind_param("i", $movimiento_id);
        $stmt->execute();

        $mov = $stmt->get_result()->fetch_assoc();

        if (!$mov) {
            throw new Exception("El movimiento no existe.");
        }

        if ($mov['usuario_recibe_id'] !== null) {
            throw new Exception("Este traspaso ya fue recibido.");
        }

        $p_id     = (int)$mov['producto_id'];
        $dest_id  = (int)$mov['almacen_destino_id'];
        $orig_id  = (int)$mov['almacen_origen_id'];
        $cantidad = (float)$mov['cantidad']; 
        $loteSeleccionado = (float)$mov['referencia_id'];

        // =========================================
        // 🔻 PEPS: DESCONTAR DE LOTES ORIGEN
        // =========================================
       if ($loteSeleccionado > 0) {
            $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario
                         FROM lotes_stock
                         WHERE id = ?
                           AND producto_id = ?
                           AND almacen_id = ?
                           AND cantidad_actual > 0";
            
            $stmtL = $this->db->prepare($sqlLotes);
            $stmtL->bind_param("iii", $loteSeleccionado, $p_id, $orig_id);
        } else {
            $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario
                         FROM lotes_stock
                         WHERE producto_id = ?
                           AND almacen_id = ?
                           AND cantidad_actual > 0
                           AND estado_lote = 'activo'
                         ORDER BY fecha_ingreso ASC, id ASC";
            
            $stmtL = $this->db->prepare($sqlLotes);
            $stmtL->bind_param("ii", $p_id, $orig_id);
        }

        $stmtL->execute();
        $resLotes = $stmtL->get_result();

        $porRestar = $cantidad;
        $precio_historico = 0;
        $lotesAfectados = [];

        while ($lote = $resLotes->fetch_assoc()) {

            if ($porRestar <= 0) break;

            $idLote = (int)$lote['id'];
            $actual = (float)$lote['cantidad_actual'];
            $precio_historico = (float)$lote['precio_compra_unitario'];

            $aQuitar = ($actual <= $porRestar) ? $actual : $porRestar;
            $nuevoStock = $actual - $aQuitar;
            $nuevoEstado = ($nuevoStock <= 0) ? 'agotado' : 'activo';

            $up = $this->db->prepare("
                UPDATE lotes_stock 
                SET cantidad_actual = ?, estado_lote = ? 
                WHERE id = ?
            ");
            $up->bind_param("dsi", $nuevoStock, $nuevoEstado, $idLote);
            $up->execute();

            $porRestar -= $aQuitar;

            $lotesAfectados[] = [
                'lote_id' => $idLote,
                'cantidad' => $aQuitar
            ];
        }

        if ($porRestar > 0) {
            throw new Exception("Stock insuficiente en lotes del almacén origen.");
        }

        // =========================================
        // 🔻 CREAR LOTE EN DESTINO
        // =========================================
        $codigo_lote = "L-TR-" . $movimiento_id . "-" . date('His');
        $precio_final = ($precio_historico > 0) ? $precio_historico : 0;

        $stmtNewLote = $this->db->prepare("
            INSERT INTO lotes_stock (
                producto_id, almacen_id, codigo_lote,
                cantidad_inicial, cantidad_actual,
                precio_compra_unitario, estado_lote
            ) VALUES (?, ?, ?, ?, ?, ?, 'activo')
        ");

        $stmtNewLote->bind_param(
            "iisddd",
            $p_id,
            $dest_id,
            $codigo_lote,
            $cantidad,
            $cantidad,
            $precio_final
        );
        $stmtNewLote->execute();

        $idLoteNuevo = $this->db->insert_id;

        // =========================================
        // 🔻 KARDEX
        // =========================================
        $observaciones = 'Arribo autorizado';

        $stmtKardex = $this->db->prepare("
            INSERT INTO kardex_movimientos_lotes (
                movimiento_id,
                lote_origen_id,
                lote_destino_id,
                producto_id,
                cantidad,
                usuario_id,
                observaciones
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($lotesAfectados as $lote) {

            $loteOrigen = (int)$lote['lote_id'];
            $cantidadLote = (float)$lote['cantidad'];

            $stmtKardex->bind_param(
                "iiiidis",
                $movimiento_id,
                $loteOrigen,
                $idLoteNuevo,
                $p_id,
                $cantidadLote,
                $usuario_id,
                $observaciones
            );

            $stmtKardex->execute();
        }

        // =========================================
        // 🔻 INVENTARIO DESTINO
        // =========================================
        $stmtInv = $this->db->prepare("
            INSERT INTO inventario (almacen_id, producto_id, stock, stock_minimo, stock_maximo)
            VALUES (?, ?, ?, 0, 0)
            ON DUPLICATE KEY UPDATE stock = stock + ?
        ");

        $stmtInv->bind_param("iidd", $dest_id, $p_id, $cantidad, $cantidad);
        $stmtInv->execute();

        // =========================================
        // 🔻 PRECIOS (SI NO EXISTEN)
        // =========================================
        $check = $this->db->prepare("
            SELECT id 
            FROM precios_producto 
            WHERE producto_id = ? AND almacen_id = ?
        ");
        $check->bind_param("ii", $p_id, $dest_id);
        $check->execute();

        if ($check->get_result()->num_rows === 0) {

            $copy = $this->db->prepare("
                INSERT INTO precios_producto (
                    producto_id, almacen_id, 
                    precio_minorista, precio_mayorista, precio_distribuidor
                )
                SELECT 
                    producto_id, ?, 
                    precio_minorista, precio_mayorista, precio_distribuidor
                FROM precios_producto 
                WHERE producto_id = ? AND almacen_id = ?
                LIMIT 1
            ");

            $copy->bind_param("iii", $dest_id, $p_id, $orig_id);
            $copy->execute();
        }

        // =========================================
        // 🔻 FINALIZAR MOVIMIENTO
        // =========================================
        if ($rol_id == 1) {
            $stmtFin = $this->db->prepare("
                UPDATE movimientos 
                SET usuario_recibe_id = ?, usuario_autoriza_id = ?, fecha = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmtFin->bind_param("iii", $usuario_id, $usuario_id, $movimiento_id);
        } else {
            $stmtFin = $this->db->prepare("
                UPDATE movimientos 
                SET usuario_recibe_id = ?, fecha = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmtFin->bind_param("ii", $usuario_id, $movimiento_id);
        }

        $stmtFin->execute();

        // 🔹 COMMIT
        $this->db->commit();

        return [
            'status' => true,
            'message' => "Material recibido correctamente (Lote: $codigo_lote)"
        ];

    } catch (Exception $e) {

        $this->db->rollback();

        error_log("ERROR recibirTraspaso: " . $e->getMessage());

        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}
}