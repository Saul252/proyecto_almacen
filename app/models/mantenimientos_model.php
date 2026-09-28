<?php
class MantenimientosModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar todos (Para Admin Global)
    public function listar() {
        $sql = "SELECT tvh.*,
       ( SELECT GROUP_CONCAT(
        CONCAT(
            IFNULL(nombre, ''),
            '|||',
            IFNULL(direccion, ''),
            '|||',
            IFNULL(id, '')
        )
        SEPARATOR ';;;'
    )
    FROM documentos_vehiculos dvh
    WHERE dvh.vehiculo_id = tvh.id and dvh.activo=1
) AS documentos_url
FROM transporte_vehiculos tvh
                WHERE activo = 1 
                ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }  
  public function listarProximoMantenimiento($almacen_id = 0) {
    $sql = "SELECT
                m.id_mantenimiento,
                m.id_vehiculo,
                v.nombre AS vehiculo,
                v.placas,
                m.tipo_mantenimiento,
                m.razon,
                m.fecha,
                m.kilometraje,
                m.fecha_proximo_mantenimiento,
                DATEDIFF(m.fecha_proximo_mantenimiento, CURDATE()) AS dias_restantes,
                CASE
                    WHEN DATEDIFF(m.fecha_proximo_mantenimiento, CURDATE()) < 0 THEN
                        CONCAT('Vencido hace ', ABS(DATEDIFF(m.fecha_proximo_mantenimiento, CURDATE())), ' días')
                    WHEN DATEDIFF(m.fecha_proximo_mantenimiento, CURDATE()) = 0 THEN
                        'Vence hoy'
                    WHEN DATEDIFF(m.fecha_proximo_mantenimiento, CURDATE()) = 1 THEN
                        'Vence mañana'
                    ELSE
                        CONCAT('Faltan ', DATEDIFF(m.fecha_proximo_mantenimiento, CURDATE()), ' días')
                END AS estado
            FROM mantenimientos m
            INNER JOIN (
                SELECT
                    id_vehiculo,
                    MAX(id_mantenimiento) AS ultimo_id
                FROM mantenimientos
                GROUP BY id_vehiculo
            ) ult ON ult.ultimo_id = m.id_mantenimiento
            INNER JOIN transporte_vehiculos v ON v.id = m.id_vehiculo
            WHERE m.fecha_proximo_mantenimiento <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";

    $params = [];
    $types = "";

    // Si $almacen_id es mayor a 0, aplica el filtro; si es 0 trae todos
    if (!empty($almacen_id) && $almacen_id > 0) {
        $sql .= " AND m.almacen_id = ?";
        $params[] = (int)$almacen_id;
        $types .= "i";
    }

    $sql .= " ORDER BY m.fecha_proximo_mantenimiento ASC";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [];
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
    public function subirDocumentoCompra($id, $nombre_evidencia, $documento_url)
{
    $sql = "INSERT INTO documentos_vehiculos
            (vehiculo_id, nombre, direccion)
            VALUES (?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $this->db->error);
    }

    $stmt->bind_param(
        "iss",
        $id,
        $nombre_evidencia,
        $documento_url
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar documento: " . $stmt->error);
    }

    $documento_id = $stmt->insert_id;

    $stmt->close();

    return [
        'success' => true,
        'documento_id' => $documento_id,
        'message' => 'Documento guardado correctamente'
    ];
}
public function eliminarDocumento( $id_documento) {

    $sql = "UPDATE documentos_vehiculos
            SET activo = 0
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("i", $id_documento);

    return $stmt->execute();
}
    // NUEVO: Listar vehículos por almacén específico
    public function listarPorAlmacen($almacen_id) {
        $id = intval($almacen_id);
        $sql = "SELECT tvh.*,
       ( SELECT GROUP_CONCAT(
        CONCAT(
            IFNULL(nombre, ''),
            '|||',
            IFNULL(direccion, ''),
            '|||',
            IFNULL(id, '')
        )
        SEPARATOR ';;;'
    )
    FROM documentos_vehiculos dvh
    WHERE dvh.vehiculo_id = tvh.id and dvh.activo=1
) AS documentos_url
FROM transporte_vehiculos tvh
                WHERE activo = 1 AND almacen_id = $id
                ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

   public function guardar(
    $almacen,
    $usuario_id,
    $vehiculo_id, 
    $monto, 
    $referencia, 
    $fecha_mantenimiento, 
    $fecha_proximo, 
    $metodo,
    $razon,
    $taller,
    $tipo,
    $kilometraje
) {
    // NOTA: Asegúrate de si 'fecha_proximo_mantenimiento' está bien escrito en tu BD.
    // En tu código original pusiste 'fecha_proximo_manteniemto' (con la 'e' cambiada de lugar).
    $sql = "INSERT INTO mantenimientos (
                id_vehiculo, 
                tipo_mantenimiento, 
                razon, 
                fecha, 
                kilometraje, 
                fecha_proximo_mantenimiento, 
                creado_en, 
                id_usuario
            ) VALUES (
                ?,?,?,?,?,?,NOW(),?
            )";

    
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssssi",$vehiculo_id, 
                $tipo, 
                $razon, 
                $fecha_mantenimiento, 
                $kilometraje, 
                $fecha_proximo,                
                $usuario_id);
        return $stmt->execute();
}

    public function eliminar($id) {
        $id = intval($id);
        return $this->db->query("UPDATE transporte_vehiculos SET activo = 0 WHERE id = $id");
    }

    // Ajustado para el módulo de Repartos filtrando por sucursal
    public function listarDisponiblesRuta($almacen_id = 0) {
        $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = " . intval($almacen_id) : "";
        
        $sql = "SELECT id, nombre, placas, capacidad_carga_kg 
                FROM transporte_vehiculos 
                WHERE activo = 1 
                AND estado_unidad = 'disponible'
                $whereAlmacen
                ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function actualizarEstado($id, $nuevo_estado) {
        $id = intval($id);
        $estadosPermitidos = ['disponible', 'en_ruta', 'mantenimiento', 'fuera_servicio'];
        if (!in_array($nuevo_estado, $estadosPermitidos)) return false;

        $sql = "UPDATE transporte_vehiculos SET estado_unidad = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    public function obtenerEstado($id) {
        $sql = "SELECT estado_unidad FROM transporte_vehiculos WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['estado_unidad'] : null;
    }
     public function obtenerMantenimientosFiltrados($filtros, $rol_id=1, $almacen_sesion=0) {
        $where = " WHERE 1=1";
        
        // Seguridad por Almacén
        $rol_id=1;
        if ($rol_id != 1) { 
            $where .= " AND v.almacen_id = $almacen_sesion "; 
        } elseif (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }
       // Buscador (Folio o Cliente)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (v.nombre LIKE '%$s%' OR v.id LIKE '%$s%'OR v.placas LIKE '%$s%') ";
        }
        // Estatus Entrega      
         
          // Estatus Entrega
        if (!empty($filtros['vehiculo'])) {
            $st = $this->db->real_escape_string($filtros['vehiculo']);
            $where .= " AND v.vehiculo_id = '$st' ";
        }

        // Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros);
        }

        // Filtro por Estado de Pago (Saldo)
        

        $sql = "SELECT m.*,a.nombre as almacen ,v.nombre as vehiculo, v.id as id_v,v.placas as placas,v.modelo_año as modelo
                 FROM mantenimientos m
                join transporte_vehiculos v on v.id=m.id_vehiculo
                JOIN almacenes a ON v.almacen_id = a.id 
                $where ORDER BY m.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } 
  
     public function obtenerMantenimiento($id=1) {
        
        // Filtro por Estado de Pago (Saldo)
        

        $sql = "SELECT m.*,a.nombre as almacen ,v.nombre as vehiculo, v.id as id_v,v.placas as placas,v.modelo_año as modelo
                 FROM mantenimientos m
                join transporte_vehiculos v on v.id=m.id_vehiculo
                JOIN almacenes a ON v.almacen_id = a.id 
               where m.id_mantenimiento = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res: null;
    } 
       private function construirFiltroFecha($f) {
        switch($f['rango']) {
            case 'hoy': return " AND DATE(m.fecha) = CURDATE() ";
            case 'ayer': return " AND DATE(m.fecha) = SUBDATE(CURDATE(),1) ";
            case 'semana': return " AND YEARWEEK(m.fecha, 1) = YEARWEEK(CURDATE(), 1) ";
            case 'mes': return " AND MONTH(m.fecha) = MONTH(CURDATE()) AND YEAR(m.fecha) = YEAR(CURDATE()) ";
            case 'personalizado':
                $ini = $this->db->real_escape_string($f['inicio']);
                $fin = $this->db->real_escape_string($f['fin']);
                return " AND DATE(m.fecha) BETWEEN '$ini' AND '$fin' ";
            default: return "";
        }
    }
    public function registrarSalidaMantenimientoPEPS($usuario_id, $items_insumos, $cantidades, $vehiculo_id,$mantenimiento_id) {
    // 1. Iniciar transacción atómica
    $this->db->begin_transaction();
    
    try {
        date_default_timezone_set('America/Mexico_City');

        // 2. Insertar Cabecera de la Salida
        $sqlCabecera = "INSERT INTO salida_insumo (fecha, usuario, vehiculo_id) VALUES (NOW(), ?, ?)";
        $stmtC = $this->db->prepare($sqlCabecera);
        if (!$stmtC) throw new Exception("Error al preparar cabecera de salida: " . $this->db->error);
        
        $stmtC->bind_param("ii", $usuario_id, $vehiculo_id);
        
        if (!$stmtC->execute()) {
            throw new Exception("Error al ejecutar cabecera de salida: " . $stmtC->error);
        }
        
        $salida_insumo_id = $this->db->insert_id;

        // 3. Preparamos los statements reutilizables para el bucle (Eficiencia del Servidor)
        $sqlDetalle = "INSERT INTO detalle_salida_insumo (insumo_id, cantidad, compra_id,mantenimiento_id,salida_id) VALUES (?, ?, ?, ?,?)";
        $sqlUpdateLote = "UPDATE compras_insumos SET existencias_lote = existencias_lote - ? WHERE id = ?";
        $sqlUpdateStock = "UPDATE insumos_stock SET existencias = existencias - ? WHERE id_insumo = ?";

        $stmtD = $this->db->prepare($sqlDetalle);
        $stmtUL = $this->db->prepare($sqlUpdateLote);
        $stmtUS = $this->db->prepare($sqlUpdateStock);

        if (!$stmtD || !$stmtUL || !$stmtUS) {
            throw new Exception("Error en preparación de operaciones internas de inventario: " . $this->db->error);
        }

        // 4. Recorrer y procesar de forma dinámica cada insumo de la tabla
        foreach ($items_insumos as $i => $insumo_id) {
            $insumo_id = intval($insumo_id);
            $cantidad_solicitada = floatval($cantidades[$i] ?? 0);

            if ($insumo_id <= 0 || $cantidad_solicitada <= 0) continue;

            $cantidad_pendiente = $cantidad_solicitada;

            // 🔍 ALGORITMO PEPS: Buscar lotes del insumo con existencias (Fecha más antigua primero)
            $sqlLotes = "SELECT id, existencias_lote FROM compras_insumos 
                         WHERE id_insumo = ? AND existencias_lote > 0 
                         ORDER BY fecha ASC";
            
            $stmtL = $this->db->prepare($sqlLotes);
            $stmtL->bind_param("i", $insumo_id);
            $stmtL->execute();
            $resLotes = $stmtL->get_result();

            while ($lote = $resLotes->fetch_assoc()) {
                if ($cantidad_pendiente <= 0) break;

                $lote_id = intval($lote['id']);
                $lote_stock = floatval($lote['existencias_lote']);

                // Evaluar la cantidad a extraer de este lote específico
                if ($lote_stock >= $cantidad_pendiente) {
                    $cantidad_a_descontar = $cantidad_pendiente;
                    $cantidad_pendiente = 0;
                } else {
                    $cantidad_a_descontar = $lote_stock;
                    $cantidad_pendiente -= $lote_stock;
                }

                // A. Descontar stock del lote de compra ("di" -> double, entero)
                $stmtUL->bind_param("di", $cantidad_a_descontar, $lote_id);
                if (!$stmtUL->execute()) {
                    throw new Exception("Error al restar stock del lote ID $lote_id en la fila " . ($i + 1));
                }

                // B. Insertar en el detalle de la salida
                // ✅ CORRECCIÓN: Se cambió $cabecera['vehiculo_id'] por el argumento directo $vehiculo_id
                $stmtD->bind_param("iiiii", 
                   
                    $insumo_id, 
                    $cantidad_a_descontar, 
                    $lote_id, 
                    $mantenimiento_id,
                    $salida_insumo_id
                   
                );
                if (!$stmtD->execute()) {
                    throw new Exception("Error al guardar el detalle de salida en la fila " . ($i + 1));
                }
            }

            // Si al terminar las compras queda cantidad pendiente -> Faltó Stock Real
            if ($cantidad_pendiente > 0) {
                throw new Exception("Stock insuficiente en los lotes para el insumo ID: $insumo_id. Faltaron $cantidad_pendiente unidades.");
            }

            // C. Descontar del acumulado en insumos_stock ("di" -> double, entero)
            $stmtUS->bind_param("di", $cantidad_solicitada, $insumo_id);
            if (!$stmtUS->execute()) {
                throw new Exception("Error al actualizar el acumulado global del insumo ID: $insumo_id");
            }
        }

        // 5. Si todo el bucle multiproducto fue exitoso, guardamos cambios permanentes
        if ($this->db->commit()) {
            return ['success' => true, 'id' => $salida_insumo_id];
        } else {
            throw new Exception("Error al ejecutar Commit de la transacción de despacho.");
        }

    } catch (Exception $e) {
        // Si falla un solo producto de la lista o hay una variable nula, se cancela todo
        $this->db->rollback();
        throw $e;
    }
}

}