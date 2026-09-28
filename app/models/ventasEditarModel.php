<?php
class VentaHistorialModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * LOGICA 3: CONSULTAR TODO EL DETALLE 
     * Ajustada para traer los 3 precios (minorista, mayorista, distribuidor)
     */
    public function obtenerDetalleCompleto($id) {
        $id = intval($id);
        
        // 1. Cabecera con Cliente y Almacén
        $sqlVenta = "SELECT v.*, c.nombre_comercial, a.nombre as almacen 
                     FROM ventas v 
                     INNER JOIN clientes c ON v.id_cliente = c.id 
                     INNER JOIN almacenes a ON v.almacen_id = a.id 
                     WHERE v.id = $id";
        
        $resVenta = $this->db->query($sqlVenta);
        if (!$resVenta || $resVenta->num_rows === 0) {
            throw new Exception("Venta no encontrada.");
        }
        $info = $resVenta->fetch_assoc();
        $almacen_id = intval($info['almacen_id']);

        // Obtener total pagado desde historial_pagos
        $resPagos = $this->db->query("SELECT SUM(monto) as pagado FROM historial_pagos WHERE venta_id = $id");
        $pagoRow = $resPagos->fetch_assoc();
        $info['total_pagado'] = $pagoRow['pagado'] ?? 0;

        // 2. Detalle de productos incluyendo STOCK y los 3 PRECIOS del almacén
        $productos = [];
        $sqlProd = "SELECT 
                        dv.*, 
                        p.nombre as producto, 
                        p.sku,
                     o.nombre,o.equivalencia,
                        p.factor_conversion, 
                        p.unidad_medida as u_mayor, 
                        p.unidad_reporte as u_menor,
                        COALESCE(inv.stock, 0) as stock_actual,
                        pp.precio_minorista,
                        pp.precio_mayorista,
                        pp.precio_distribuidor
                    FROM detalle_venta dv 
                    INNER JOIN productos p ON dv.producto_id = p.id 
                      LEFT JOIN opciones_de_medida_adicional o
    ON dv.unidadMedida = o.id
                    LEFT JOIN precios_producto pp ON p.id = pp.producto_id AND pp.almacen_id = $almacen_id
                   
                    LEFT JOIN inventario inv ON p.id = inv.producto_id AND inv.almacen_id = $almacen_id
                    WHERE dv.venta_id = $id";

        $resProd = $this->db->query($sqlProd);
        if ($resProd) {
            while ($row = $resProd->fetch_assoc()) {
                $productos[] = $row;
            }
        }

        // 3. Historial de entregas
        $historial = [];
        $sqlHis = "SELECT de.cantidad, e.fecha, u.nombre as usuario_nombre, p.nombre as producto
                   FROM detalle_entrega de
                   INNER JOIN entregas_venta e ON de.entrega_id = e.id
                   INNER JOIN detalle_venta dv ON de.detalle_venta_id = dv.id
                   INNER JOIN productos p ON dv.producto_id = p.id
                   
                   INNER JOIN usuarios u ON e.usuario_id = u.id
                   WHERE e.venta_id = $id
                   ORDER BY e.fecha DESC";
        
        $resHis = $this->db->query($sqlHis);
        if ($resHis) {
            while ($row = $resHis->fetch_assoc()) {
                $historial[] = $row;
            }
        }

        return [
            "status" => "success",
            "info" => $info,
            "productos" => $productos,
            "historial" => $historial
        ];
    }

    // --- FUNCIONES ORIGINALES SIN CAMBIOS ---

    

   
    public function registrarAbono($venta_id, $monto, $usuario_id,$referencia) {
         $metodo_pago="";
   
        $stmt = $this->db->prepare("INSERT INTO historial_pagos (venta_id, usuario_id, monto, saldo_favor, metodo_pago, referencia) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiddss", $venta_id, $usuario_id, $monto, $monto, $metodo_pago, $referencia);
        if ($stmt->execute()) {
            $v = $this->db->query("SELECT total FROM ventas WHERE id = $venta_id")->fetch_assoc();
            $p = $this->db->query("SELECT SUM(monto) as pagado FROM historial_pagos WHERE venta_id = $venta_id")->fetch_assoc();
            $nuevo_estado = ($p['pagado'] >= $v['total']) ? 'pagado' : 'parcial';
            $this->db->query("UPDATE ventas SET estado_pago = '$nuevo_estado' WHERE id = $venta_id");
            return ["status" => "success"];
        }
        return ["status" => "error", "message" => "Fallo al registrar abono"];
    }

    public function procesarEntregaParcial($venta_id, $productos_a_entregar, $usuario_id) {
        $this->db->begin_transaction();
        try {
            $v_info = $this->db->query("SELECT almacen_id, folio FROM ventas WHERE id = $venta_id")->fetch_assoc();
            $almacen_id = $v_info['almacen_id'];
            $stmtE = $this->db->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha) VALUES (?, ?, NOW())");
            $stmtE->bind_param("ii", $venta_id, $usuario_id);
            $stmtE->execute();
            $entrega_id = $this->db->insert_id;

            foreach ($productos_a_entregar as $p) {
                $dv_id = intval($p['detalle_venta_id']);
                $cant_hoy = floatval($p['cantidad_a_entregar']);
                if ($cant_hoy <= 0) continue;
                $res_v = $this->db->query("SELECT producto_id, (cantidad - cantidad_entregada) as pendiente FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
                if ($cant_hoy > $res_v['pendiente']) throw new Exception("Cantidad excede el pendiente.");
                
                $stmtDE = $this->db->prepare("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)");
                $stmtDE->bind_param("iid", $entrega_id, $dv_id, $cant_hoy);
                $stmtDE->execute();

                $this->db->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $cant_hoy WHERE id = $dv_id");
                $p_id = $res_v['producto_id'];
                $this->db->query("UPDATE inventario SET stock = stock - $cant_hoy WHERE producto_id = $p_id AND almacen_id = $almacen_id");
                $this->registrarMovimiento($p_id, 'salida', $cant_hoy, $almacen_id, $usuario_id, $venta_id, "Entrega parcial Folio: " . $v_info['folio']);
            }
            $this->sincronizarEstadosEntrega($venta_id);
            $this->db->commit();
            return ["status" => "success"];
        } catch (Exception $e) {
            $this->db->rollback();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    private function sincronizarEstadosEntrega($venta_id) {
        $this->db->query("UPDATE detalle_venta SET estado_entrega = CASE WHEN cantidad_entregada >= cantidad THEN 'entregado' WHEN cantidad_entregada > 0 THEN 'parcial' ELSE 'pendiente' END WHERE venta_id = $venta_id");
        $res = $this->db->query("SELECT SUM(cantidad) as t, SUM(cantidad_entregada) as e FROM detalle_venta WHERE venta_id = $venta_id")->fetch_assoc();
        $st_g = ($res['e'] >= $res['t']) ? 'entregado' : ($res['e'] > 0 ? 'parcial' : 'pendiente');
        $this->db->query("UPDATE ventas SET estado_entrega = '$st_g' WHERE id = $venta_id");
    }

    private function registrarMovimiento($p_id, $tipo, $cant, $alm, $user, $ref, $obs) {
        $stmt = $this->db->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdiiss", $p_id, $tipo, $cant, $alm, $user, $ref, $obs);
        $stmt->execute();
    }

    public function obtenerProductosAlmacen($almacen_id) {
        $almacen_id = intval($almacen_id);
        $sql = "SELECT p.id, p.sku, p.nombre, p.unidad_medida, p.unidad_reporte, p.factor_conversion,
                       pp.precio_minorista, pp.precio_mayorista, pp.precio_distribuidor,
                       IFNULL(i.stock, 0) as stock
                FROM productos p
                INNER JOIN precios_producto pp ON p.id = pp.producto_id
                LEFT JOIN inventario i ON p.id = i.producto_id AND i.almacen_id = $almacen_id
                WHERE pp.almacen_id = $almacen_id AND p.activo = 1";
        $res = $this->db->query($sql);
        $productos = [];
        while ($row = $res->fetch_assoc()) { $productos[] = $row; }
        return $productos;
    }

    /**
     * RECALCULAR Y EDITAR VENTA
     * Ajustada para actualizar tipo_precio y precio_unitario correctamente.
     */
    public function recalcularYEditarVenta($data) {
        $this->db->begin_transaction();
        try {
            $v_id = intval($data['venta_id']);
            $u_id = intval($data['usuario_id']);
            $almacen_id = intval($data['almacen_id']);
// --- BLOQUE NUEVO: PROCESAR ELIMINACIONES ---
        if (!empty($data['eliminados'])) {
            foreach ($data['eliminados'] as $dv_id) {
                $dv_id = intval($dv_id);

                // 1. Obtener info del producto y cantidad entregada antes de borrar
                $sqlInfo = "SELECT producto_id, cantidad_entregada FROM detalle_venta WHERE id = ?";
                $stmtInfo = $this->db->prepare($sqlInfo);
                $stmtInfo->bind_param("i", $dv_id);
                $stmtInfo->execute();
                $resDetalle = $stmtInfo->get_result()->fetch_assoc();

                if ($resDetalle) {
                    $p_id = $resDetalle['producto_id'];
                    $cant_entregada = floatval($resDetalle['cantidad_entregada']);

                    // A. ELIMINAR DEPENDENCIAS (Evita el Foreign Key Error)
                    // Borramos los registros de salida física vinculados a esta fila
                    $this->db->query("DELETE FROM detalle_entrega WHERE detalle_venta_id = $dv_id");

                    // B. DEVOLVER STOCK (Si ya se había entregado algo)
                    if ($cant_entregada > 0) {
                        $this->db->query("UPDATE inventario SET stock = stock + $cant_entregada 
                                         WHERE producto_id = $p_id AND almacen_id = $almacen_id");

                        // C. REGISTRAR EN KARDEX
                        $obs = "ELIMINACIÓN DE PRODUCTO EN EDICIÓN - Venta ID: $v_id";
                        $stmtK = $this->db->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
                        $stmtK->bind_param("idiiss", $p_id, $cant_entregada, $almacen_id, $u_id, $v_id, $obs);
                        $stmtK->execute();
                    }

                    // D. BORRAR LA FILA DEL DETALLE DE VENTA
                    $this->db->query("DELETE FROM detalle_venta WHERE id = $dv_id");
                }
            }
        }
            // --- CAPTURAMOS EL TOTAL ANTERIOR ANTES DE ACTUALIZAR ---
            $v_prev = $this->db->query("SELECT folio, total, id_cliente FROM ventas WHERE id = $v_id")->fetch_assoc();
            $total_anterior = floatval($v_prev['total']);
            $cliente_id = intval($v_prev['id_cliente']);

            // 1. ELIMINACIÓN de productos quitados
            $ids_enviados = array_filter(array_column($data['productos'], 'detalle_id'));
            if (!empty($ids_enviados)) {
                $ids_string = implode(',', $ids_enviados);
                $this->db->query("DELETE FROM detalle_venta 
                                 WHERE venta_id = $v_id 
                                 AND id NOT IN ($ids_string)
                                 AND cantidad_entregada = 0");
            }

            // 2. ACTUALIZAR CABECERA
            $stmtV = $this->db->prepare("UPDATE ventas SET id_cliente = ?, subtotal = ?, total = ? WHERE id = ?");
            $stmtV->bind_param("iddi", $data['id_cliente'], $data['nuevo_total'], $data['nuevo_total'], $v_id);
            $stmtV->execute();

            // 3. REGISTRO DE ENTREGA SI CORRESPONDE
            $entrega_id = 0;
            $tiene_entregas_hoy = array_sum(array_column($data['productos'], 'entrega_hoy')) > 0;
            if ($tiene_entregas_hoy) {
                $stmtE = $this->db->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha, observaciones) VALUES (?, ?, NOW(), 'Entrega desde edición')");
                $stmtE->bind_param("ii", $v_id, $u_id);
                $stmtE->execute();
                $entrega_id = $this->db->insert_id;
            }

            // 4. PROCESAR PRODUCTOS (Con soporte para tipo_precio)
            foreach ($data['productos'] as $prod) {
                $dv_id = intval($prod['detalle_id']);
                $p_id = intval($prod['producto_id']);
                $n_cant = floatval($prod['nueva_cantidad']);
                $ent_hoy = floatval($prod['entrega_hoy'] ?? 0);
                $precio = floatval($prod['precio_unitario']);
                $tipo_p = $prod['tipo_precio'] ?? 'minorista';
                $subtotal_fila = $n_cant * $precio;

                if ($dv_id == 0) {
                    // Nuevo Producto
                    $stmtIns = $this->db->prepare("INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal, tipo_precio, estado_entrega) VALUES (?, ?, ?, ?, ?, ?, 'pendiente')");
                    $stmtIns->bind_param("iiddss", $v_id, $p_id, $n_cant, $precio, $subtotal_fila, $tipo_p);
                    $stmtIns->execute();
                    $dv_id = $this->db->insert_id;
                } else {
                    // 1. Consultar estado actual antes de cualquier cambio
$resActual = $this->db->query("SELECT producto_id, cantidad_entregada FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();

if ($resActual) {
    $p_id = $resActual['producto_id'];
    $cant_entregada_db = floatval($resActual['cantidad_entregada']);

    // --- CASO A: ELIMINACIÓN AUTOMÁTICA (Si la nueva cantidad es 0) ---
    if (floatval($n_cant) <= 0) {
        
        // A.1 Eliminar dependencias de entrega (Evita el Foreign Key Error)
        $this->db->query("DELETE FROM detalle_entrega WHERE detalle_venta_id = $dv_id");

        // A.2 Si hubo entregas previas, devolver TODO al stock
        if ($cant_entregada_db > 0) {
            // Devolver al Stock
            $this->db->query("UPDATE inventario SET stock = stock + $cant_entregada_db WHERE producto_id = $p_id AND almacen_id = $almacen_id");

            // Registrar en Kardex
            $obs = "ELIMINACIÓN POR AJUSTE A CERO - Venta ID: $v_id";
            $stmtK = $this->db->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
            $stmtK->bind_param("idiiss", $p_id, $cant_entregada_db, $almacen_id, $u_id, $v_id, $obs);
            $stmtK->execute();
        }

        // A.3 Borrar la fila del detalle definitivamente
        $this->db->query("DELETE FROM detalle_venta WHERE id = $dv_id");

    } 
    // --- CASO B: ACTUALIZACIÓN / AJUSTE (Si la nueva cantidad es > 0) ---
    else {
        // LÓGICA DE REINGRESO PARCIAL: Si la nueva cantidad es MENOR a lo entregado
        if ($n_cant < $cant_entregada_db) {
            $diferencia_a_devolver = $cant_entregada_db - $n_cant;

            // 1. Devolver la diferencia al Stock
            $stmtInv = $this->db->prepare("UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?");
            $stmtInv->bind_param("dii", $diferencia_a_devolver, $p_id, $almacen_id);
            $stmtInv->execute();

            // 2. Registrar en Kardex
            $obs = "AJUSTE EDICIÓN (REDUCCIÓN): Devolución de $diferencia_a_devolver unidades (Venta $v_id)";
            $stmtMov = $this->db->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
            $stmtMov->bind_param("idiiss", $p_id, $diferencia_a_devolver, $almacen_id, $u_id, $v_id, $obs);
            $stmtMov->execute();

            // 3. Igualar la entrega a la nueva cantidad total
            $this->db->query("UPDATE detalle_venta SET cantidad_entregada = $n_cant WHERE id = $dv_id");
        }

        // Finalmente, actualizar datos generales (Precio, Subtotal, Cantidad Total)
        $stmtUpd = $this->db->prepare("UPDATE detalle_venta SET cantidad = ?, precio_unitario = ?, subtotal = ?, tipo_precio = ? WHERE id = ?");
        $stmtUpd->bind_param("ddssi", $n_cant, $precio, $subtotal_fila, $tipo_p, $dv_id);
        $stmtUpd->execute();
    }
}
                }

                // 5. LÓGICA DE STOCK Y ENTREGAS HOY
                if ($ent_hoy > 0) {
                    $stmtDE = $this->db->prepare("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)");
                    $stmtDE->bind_param("iid", $entrega_id, $dv_id, $ent_hoy);
                    $stmtDE->execute();

                    $this->db->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $ent_hoy WHERE id = $dv_id");
                    $this->db->query("UPDATE inventario SET stock = stock - $ent_hoy WHERE producto_id = $p_id AND almacen_id = $almacen_id");
                    
                    $obs = "Entrega parcial - Folio: " . $v_prev['folio'];
                    $this->registrarMovimiento($p_id, 'salida', $ent_hoy, $almacen_id, $u_id, $v_id, $obs);
                }
            }

            // 6. RECALCULAR ESTADO DE PAGO
            $resPagos = $this->db->query("SELECT SUM(monto) as pagado FROM historial_pagos WHERE venta_id = $v_id");
            $pagado = $resPagos->fetch_assoc()['pagado'] ?? 0;
            $nuevo_st_pago = ($pagado >= $data['nuevo_total']) ? 'pagado' : ($pagado > 0 ? 'parcial' : 'pendiente');
            $this->db->query("UPDATE ventas SET estado_pago = '$nuevo_st_pago' WHERE id = $v_id");

            // 7. SINCRONIZAR ESTADOS DE ENTREGA
            $this->sincronizarEstadosEntrega($v_id);

            // --- CALCULAMOS LA DIFERENCIA PARA EL CONTROLLER ---
            $diferencia = floatval($data['nuevo_total']) - $total_anterior;

            $this->db->commit();
            return [
                "status" => "success",
                "financiero" => [
                    "diferencia" => $diferencia,
                    "id_cliente" => $cliente_id,
                    "venta_id"   => $v_id
                ]
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function recalcularYEditarVenta2($data) {
    try {
        $this->db->begin_transaction();

        $v_id        = intval($data['venta_id']);
        $u_id        = intval($data['usuario_id'] ?? 0);
        $almacen_id  = intval($data['almacen_id']);
        $cliente_id  = intval($data['id_cliente']);
        $vendedor    = intval($data['vendedor'] ?? 0);
        $nuevo_total = floatval($data['nuevo_total']);

        // -----------------------------------------------------------------
        // A. CAPTURAR DATOS ANTERIORES (Usando Sentencias Preparadas)
        // -----------------------------------------------------------------
        $stmtPrev = $this->db->prepare("SELECT folio, total, id_cliente FROM ventas WHERE id = ?");
        $stmtPrev->bind_param("i", $v_id);
        $stmtPrev->execute();
        $v_prev = $stmtPrev->get_result()->fetch_assoc();
        $stmtPrev->close();

        if (!$v_prev) {
            throw new Exception("La venta no fue encontrada.");
        }
        $total_anterior  = floatval($v_prev['total']);
        $cliente_id_prev = intval($v_prev['id_cliente']);

        // -----------------------------------------------------------------
        // 1. ACTUALIZAR CABECERA DE LA VENTA
        // -----------------------------------------------------------------
        $sqlCab = "UPDATE ventas 
                   SET id_cliente = ?, 
                       subtotal = ?, 
                       total = ?, 
                       vendedor_id = ? 
                   WHERE id = ?";
        $stmtV = $this->db->prepare($sqlCab);
        $stmtV->bind_param("idddi", $cliente_id, $nuevo_total, $nuevo_total, $vendedor, $v_id);

        if (!$stmtV->execute()) {
            throw new Exception("Error al actualizar la cabecera de la venta: " . $stmtV->error);
        }
        $stmtV->close();

        // -----------------------------------------------------------------
        // 2. ELIMINAR DETALLES NO INCLUIDOS EN "noEliminar" Y REVERTIR STOCK
        // -----------------------------------------------------------------
        $ids_a_conservar = [];
        if (!empty($data['productos'])) {
            foreach ($data['productos'] as $p) {
                if (!empty($p['noEliminar'])) {
                    $ids_a_conservar[] = intval($p['noEliminar']);
                }
            }
        }

        if (!empty($ids_a_conservar)) {
            $in_ids = implode(',', $ids_a_conservar);
            $sqlAEliminar = "SELECT id, producto_id, cantidad_entregada 
                             FROM detalle_venta 
                             WHERE venta_id = ? AND id NOT IN ($in_ids)";
        } else {
            $sqlAEliminar = "SELECT id, producto_id, cantidad_entregada 
                             FROM detalle_venta 
                             WHERE venta_id = ?";
        }

        $stmtElim = $this->db->prepare($sqlAEliminar);
        $stmtElim->bind_param("i", $v_id);
        $stmtElim->execute();
        $resElim = $stmtElim->get_result();

        while ($detDel = $resElim->fetch_assoc()) {
            $dv_id          = intval($detDel['id']);
            $p_id           = intval($detDel['producto_id']);
            $cant_entregada = floatval($detDel['cantidad_entregada']);

            // Eliminar entregas previas de este ítem
            $stmtDelEntrega = $this->db->prepare("DELETE FROM detalle_entrega WHERE detalle_venta_id = ?");
            $stmtDelEntrega->bind_param("i", $dv_id);
            $stmtDelEntrega->execute();
            $stmtDelEntrega->close();

            if ($cant_entregada > 0) {
                // Revertir Stock en inventario
                $stmtStock = $this->db->prepare("UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?");
                $stmtStock->bind_param("dii", $cant_entregada, $p_id, $almacen_id);
                $stmtStock->execute();
                $stmtStock->close();

                // Registrar Kardex / Movimiento
                $obs = "ELIMINACIÓN DE PRODUCTO EN EDICIÓN - Venta ID: $v_id";
                $stmtK = $this->db->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
                $stmtK->bind_param("idiiss", $p_id, $cant_entregada, $almacen_id, $u_id, $v_id, $obs);
                $stmtK->execute();
                $stmtK->close();
            }

            // Eliminar ítem del detalle
            $stmtDelDV = $this->db->prepare("DELETE FROM detalle_venta WHERE id = ?");
            $stmtDelDV->bind_param("i", $dv_id);
            $stmtDelDV->execute();
            $stmtDelDV->close();
        }
        $stmtElim->close();

        // -----------------------------------------------------------------
        // 3. REGISTRAR O ACTUALIZAR PRODUCTOS
        // -----------------------------------------------------------------
        if (!empty($data['productos'])) {
            foreach ($data['productos'] as $prod) {
                $dv_id           = intval($prod['noEliminar'] ?? 0);
                $p_id            = intval($prod['producto_id']);
                $n_cant          = floatval($prod['cantidad']);
                $unidad_medida   = intval($prod['unidad'] ?? 0);
                $precio_unitario = floatval($prod['precio_unitario']);
                $tipo_p          = $prod['tipoPrecio'] ?? 'minorista';
                $subtotal_fila   = floatval($prod['precio']);

                if ($dv_id == 0) {
                    // Producto Nuevo
                    $sqlIns = "INSERT INTO detalle_venta 
                               (venta_id, producto_id, cantidad, unidadMedida, cantidad_entregada, precio_unitario, subtotal, tipo_precio, estado_entrega) 
                               VALUES (?, ?, ?, ?, 0, ?, ?, ?, 'pendiente')";
                    $stmtIns = $this->db->prepare($sqlIns);
                    // Tipos: i (venta_id), i (producto_id), d (cantidad), i (unidadMedida), d (precio_unitario), d (subtotal), s (tipo_precio)
                    $stmtIns->bind_param("iidddss", $v_id, $p_id, $n_cant, $unidad_medida, $precio_unitario, $subtotal_fila, $tipo_p);
                    
                    if (!$stmtIns->execute()) {
                        throw new Exception("Error al insertar producto nuevo: " . $stmtIns->error);
                    }
                    $stmtIns->close();
                } else {
                    // Actualizar Producto existente
                    $sqlUpd = "UPDATE detalle_venta 
                               SET cantidad = ?, unidadMedida = ?, precio_unitario = ?, subtotal = ?, tipo_precio = ? 
                               WHERE id = ?";
                    $stmtUpd = $this->db->prepare($sqlUpd);
                    // Tipos: d (cantidad), i (unidadMedida), d (precio_unitario), d (subtotal), s (tipo_precio), i (id)
                    $stmtUpd->bind_param("didssi", $n_cant, $unidad_medida, $precio_unitario, $subtotal_fila, $tipo_p, $dv_id);
                    
                    if (!$stmtUpd->execute()) {
                        throw new Exception("Error al actualizar producto: " . $stmtUpd->error);
                    }
                    $stmtUpd->close();
                }
            }
        }

        // -----------------------------------------------------------------
        // 4. CÁLCULO FINANCIERO Y ESTADO DE PAGO
        // -----------------------------------------------------------------
        $stmtPagos = $this->db->prepare("SELECT SUM(monto) as pagado FROM historial_pagos WHERE venta_id = ?");
        $stmtPagos->bind_param("i", $v_id);
        $stmtPagos->execute();
        $resPagos = $stmtPagos->get_result()->fetch_assoc();
        $stmtPagos->close();

        $pagado = floatval($resPagos['pagado'] ?? 0);
        $nuevo_st_pago = ($pagado >= $nuevo_total) ? 'pagado' : ($pagado > 0 ? 'parcial' : 'pendiente');
        
        $stmtSt = $this->db->prepare("UPDATE ventas SET estado_pago = ? WHERE id = ?");
        $stmtSt->bind_param("si", $nuevo_st_pago, $v_id);
        $stmtSt->execute();
        $stmtSt->close();

        if (method_exists($this, 'sincronizarEstadosEntrega')) {
            $this->sincronizarEstadosEntrega($v_id);
        }

        $diferencia = $nuevo_total - $total_anterior;

        $this->db->commit();

        return [
            "status" => "success",
            "financiero" => [
                "diferencia" => $diferencia,
                "id_cliente" => $cliente_id_prev,
                "venta_id"   => $v_id
            ]
        ];

    } catch (Throwable $e) {
        $this->db->rollback();
        return [
            "status" => "error", 
            "message" => $e->getMessage()
        ];
    }
}

}