<?php
class RepartoVenta {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

   /**
 * Procesa la asignación de una ruta de logística completa.
 * @param array $datos Provienen directamente del $_POST del formulario.
 * @return int ID del reparto generado.
 * @throws Exception Si ocurre un error en la base de datos.
 */
public function repartosPorVenta($folio_viaje = null) {
    try {
        // Ruta base para tus imágenes (ajusta según tu carpeta real)
        $base_path = "/myvet/"; 

        $sql = "SELECT 
                    trp.id AS id_movimiento,
                    v.id AS id_venta,
                    trm.vehiculo_id,
                    tc.viaje_folio AS folio_viaje,
                    trp.descripcion_punto AS direccion_entrega,
                    trp.estado_punto AS estatus_parada,
                    v.folio AS folio_venta,
                    c.nombre_comercial AS cliente,
                    p.nombre AS producto_nombre,
                   
                   
                    crv.id AS id_evidencia,
                    IF(crv.id IS NOT NULL, 1, 0) AS ya_entregado,
                    -- Concatenamos la ruta si existe la foto
                    IF(crv.fotografia_entrega IS NOT NULL AND crv.fotografia_entrega != '', CONCAT('$base_path', crv.fotografia_entrega), NULL) AS foto_registrada,
                    IF(crv.fotografia_nota IS NOT NULL AND crv.fotografia_nota != '', CONCAT('$base_path', crv.fotografia_nota), NULL) AS nota_registrada,
                    crv.estatus AS estatus_evidencia,
                    crv.comentario AS comentario_evidencia
                FROM transporte_consolidacion tc
                INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                INNER JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id 
                INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN ventas v ON m.referencia_id = v.id
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN confirmacion_reparto_por_venta crpv ON trp.id = crv.id_movimiento";

        if (!empty($folio_viaje)) {
            $sql .= " WHERE tc.viaje_folio = ?";
        }

        $sql .= " GROUP BY trp.id ORDER BY trp.orden_visita ASC";

        $stmt = $this->db->prepare($sql);
        if (!empty($folio_viaje)) {
            $stmt->bind_param("s", $folio_viaje);
        }

        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        if (ob_get_level()) ob_clean();
        return $res;
    } catch (Exception $e) {
        error_log("Error CF System: " . $e->getMessage());
        return [];
    }
}

public function registrarEntregaMovimiento($datos) {
    try {
        $this->db->begin_transaction();

        $id_mov   = intval($datos['id_movimiento']);
        $id_ven   = intval($datos['id_venta']);
        $id_tra   = intval($datos['trabajador_id'] ?? 0);
        $id_veh   = intval($datos['vehiculo_id']);
        $foto_ent = !empty($datos['fotografia_entrega']) ? $datos['fotografia_entrega'] : null;
        $foto_not = !empty($datos['fotografia_nota']) ? $datos['fotografia_nota'] : null;
        $estatus  = $datos['estatus_entrega']; 
        $coment   = $datos['comentario'] ?? '';

        // CONSTRUCCIÓN DINÁMICA DEL UPDATE
        // Solo actualizamos las fotos si el string NO está vacío.
        $updateFields = [
            "estatus = VALUES(estatus)",
            "comentario = VALUES(comentario)",
            "hora = CURTIME()"
        ];

        if ($foto_ent !== null) {
            $updateFields[] = "fotografia_entrega = VALUES(fotografia_entrega)";
        }
        if ($foto_not !== null) {
            $updateFields[] = "fotografia_nota = VALUES(fotografia_nota)";
        }
       $result_entrega = $this->db->query("SELECT entrega_id FROM movimientos m WHERE m.id = $id_mov");
$row_entrega = $result_entrega->fetch_assoc();
// Si encuentra el registro toma el ID, si no, asigna 0 o null según permita tu base de datos
$entrega_id = $row_entrega ? intval($row_entrega['entrega_id']) : 0;   
           

$sqlEv = "INSERT INTO confirmacion_reparto_viaje (
            id_movimiento, id_venta, trabajador_id, vehiculo_id, 
            fecha, hora, fotografia_entrega, fotografia_nota, estatus, comentario, entrega_id
        ) VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE " . implode(", ", $updateFields);

$stmtEv = $this->db->prepare($sqlEv);

// Ahora $entrega_id sí es un número entero válido para la "i" final
$stmtEv->bind_param("iiiissssi", 
    $id_mov, $id_ven, $id_tra, $id_veh, $foto_ent, $foto_not, $estatus, $coment, $entrega_id
        );
        
        if (!$stmtEv->execute()) throw new Exception("Error al guardar evidencia: " . $stmtEv->error);

        // Actualizar estatus del punto de ruta a visitado
        $sqlPunto = "UPDATE transporte_rutas_puntos SET estado_punto = 'visitado', llegada_real = NOW() WHERE id = ?";
        $stmtP = $this->db->prepare($sqlPunto);
        $stmtP->bind_param("i", $id_mov);
        $stmtP->execute();

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        if ($this->db->in_transaction) $this->db->rollback();
        error_log("Error registrarEntregaMovimiento: " . $e->getMessage());
        throw new Exception($e->getMessage());
    }
}
}