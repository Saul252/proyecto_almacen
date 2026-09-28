<?php

class tesoreriaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * CREATE: Registra un nuevo movimiento de capital con desgloses y destinos.
     */
    public function registrarMovimiento($datos) {
        try {
            $this->db->beginTransaction();

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
                        metodo_pago, 
                        usuario_registro_id, 
                        usuario_autoriza_id,
                        concepto, 
                        referencia_folio, 
                        fecha_movimiento
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);

            // Validar destinos para enviar NULL si están vacíos
            $almDestino   = !empty($datos['almacen_destino_id']) ? $datos['almacen_destino_id'] : null;
            $cajaFuerte   = !empty($datos['caja_fuerte_id']) ? $datos['caja_fuerte_id'] : null;
            $bancoDestino = !empty($datos['banco_id']) ? $datos['banco_id'] : null;

            $stmt->execute([
                $datos['categoria_id'],
                $datos['almacen_id'],
                $almDestino,
                $cajaFuerte,
                $bancoDestino,
                $datos['monto'],
                $datos['monto_efectivo'] ?? 0,
                $datos['monto_tarjeta'] ?? 0,
                $datos['monto_transferencia'] ?? 0,
                $datos['metodo_pago'] ?? 'Efectivo',
                $datos['usuario_id'],
                $datos['autoriza_id'] ?? null,
                $datos['concepto'],
                $datos['referencia'] ?? null
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en tesoreriaModel::registrarMovimiento -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * READ: Lista los movimientos con filtros de almacén y fecha.
     */
   

    /**
     * UPDATE: Cancela un movimiento (Estatus 0) en lugar de eliminarlo.
     */
    public function cancelarMovimiento($id, $usuario_id) {
        try {
            $sql = "UPDATE historial_capital SET estatus = 0 WHERE id = ?";
            return $this->db->prepare($sql)->execute([$id]);
        } catch (Exception $e) {
            error_log("Error al cancelar movimiento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * SALDO: Obtiene el saldo real actual de un almacén.
     */
    public function obtenerSaldoActual($almacen_id) {
        $sql = "SELECT 
                    SUM(CASE WHEN c.tipo_operacion = 'entrada' THEN h.monto ELSE 0 END) - 
                    SUM(CASE WHEN c.tipo_operacion = 'salida' THEN h.monto ELSE 0 END) as saldo_real
                FROM historial_capital h
                INNER JOIN capital_categorias c ON h.categoria_id = c.id
                WHERE h.almacen_origen_id = ? AND h.estatus = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$almacen_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['saldo_real'] ?? 0;
    }

    /**
     * CATÁLOGOS: Obtener categorías, cajas fuertes y bancos para formularios.
     */
    public function getCategorias() {
    // Usando la lógica de MySQLi para ser compatible con el resto de tu código
    $sql = "SELECT * FROM capital_categorias WHERE estatus = 1";
    $result = $this->db->query($sql);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    return [];
}
/**
 * Obtiene las cajas fuertes. Si se pasa un almacen_id, filtra por este.
 * Útil para que un almacén solo vea sus propias cajas.
 */
/**
 * Obtiene las cajas fuertes vinculadas forzosamente al almacén seleccionado.
 * @param int $almacen_id ID del almacén (0 para ver todas las de la empresa)
 */
public function getCajasFuertes($almacen_id) {
    // Forzamos el casteo a entero para seguridad
    $id = intval($almacen_id);
    
    $sql = "SELECT id, nombre, almacen_id FROM cajas_fuertes WHERE estatus = 1";
    
    // Si el almacén es mayor a 0, filtramos estrictamente. 
    // Si es 0, el admin puede ver todas las cajas de todos los almacenes.
    if ($id > 0) {
        $sql .= " AND almacen_id = $id";
    }
    
    $result = $this->db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Obtiene las cuentas bancarias vinculadas forzosamente al almacén seleccionado.
 * @param int $almacen_id ID del almacén (0 para ver todos los bancos de la empresa)
 */
public function getCuentasBancarias($almacen_id) {
    $id = intval($almacen_id);
    
    $sql = "SELECT id_cuenta, nombre_cuenta, id_almacen 
            FROM cuentas_bancarias 
            WHERE estatus = 1 AND tipo_cuenta = 'Banco'";
    
    // Aplicamos la misma lógica: 0 = Global/Admin, >0 = Sucursal específica
    if ($id > 0) {
        $sql .= " AND id_almacen = $id";
    }
    
    $result = $this->db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

    /**
     * APERTURA: Registro automático desde el corte.
     */
    public function registrarAperturaDesdeCierre($almacen_id, $usuario_id, $desglose, $fecha_corte) {
        $monto_total = array_sum($desglose);
        $fecha_apertura = date('Y-m-d', strtotime($fecha_corte . ' +1 day')) . ' 00:00:01';
        $concepto = "Saldo inicial automático (Corte: " . $fecha_corte . ")";

        $sql = "INSERT INTO historial_capital (
                    categoria_id, almacen_origen_id, monto, monto_efectivo, 
                    monto_tarjeta, monto_transferencia, usuario_registro_id, 
                    concepto, fecha_movimiento
                ) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->prepare($sql)->execute([
            $almacen_id, $monto_total, $desglose['efectivo'], 
            $desglose['tarjeta'], $desglose['transferencia'], 
            $usuario_id, $concepto, $fecha_apertura
        ]);
    }
}