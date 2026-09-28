<?php
class RepartoModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Procesa la asignación de una ruta de logística completa.
     * @param array $datos Provienen directamente del $_POST del formulario.
     * @return int ID del reparto generado.
     * @throws Exception Si ocurre un error en la base de datos.
     */
    public function iniciarReparto($datos)
    {
        try {
            $vehiculo_id = intval($datos['vehiculo_id']);
            $chofer_id = intval($datos['chofer_id']);
            $movimiento_id = intval($datos['movimiento_id']);
            $direccion = !empty($datos['direccion_entrega']) ? $datos['direccion_entrega'] : 'Entrega en Obra';

            // 🔹 CORRECCIÓN: Verifica 'tripulante_id' primero (enviado por el controlador) y 'tripulantes' como respaldo

            // Recuperamos el folio que viene desde el controlador
            $folio_viaje = $datos['folio_viaje'] ?? '';

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
                ) VALUES (?, ?, ?, CURDATE(), 'en_transito')";

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
                ) VALUES (?, ?, ?, 'abierto', ?)";

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
                ) VALUES (?, 1, ?, 'pendiente', ?)";

            $stmtP = $this->db->prepare($sqlP);
            $stmtP->bind_param("isi", $reparto_id, $direccion, $entrega_id);
            $stmtP->execute();

            // 3. Registrar Tripulación (Solo 1 tripulante válido y distinto al chofer)


            $this->db->commit();
            return $reparto_id;

        } catch (Exception $e) {
            if (isset($this->db)) {
                try {
                    $this->db->rollback();
                } catch (Throwable $t) {
                }
            }
            throw $e;
        }
    }
    /**
     * Guarda un tripulante asignado a un reparto específico.
     * 
     * @param int $reparto_id ID del reparto maestro
     * @param int $usuario_id ID del usuario/tripulante
     * @param int $chofer_id  (Opcional) ID del chofer para evitar duplicidad de rol
     * @return bool Devuelve true si se insertó con éxito, false si no
     */

    public function iniciarRepartoPatio($datos)
    {
        try {
            $vehiculo_id = intval($datos['vehiculo_id']);
            $chofer_id = intval($datos['chofer_id']);
            $movimiento_id = intval($datos['movimiento_id']);
            $direccion = !empty($datos['direccion_entrega']) ? $datos['direccion_entrega'] : 'Entrega en Obra';

            // 🔹 CORRECCIÓN: Verifica 'tripulante_id' primero (enviado por el controlador) y 'tripulantes' como respaldo

            // Recuperamos el folio que viene desde el controlador
            $folio_viaje = $datos['folio_viaje'] ?? '';

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
                try {
                    $this->db->rollback();
                } catch (Throwable $t) {
                }
            }
            throw $e;
        }
    }
    /**
     * Guarda un tripulante asignado a un reparto específico.
     * 
     * @param int $reparto_id ID del reparto maestro
     * @param int $usuario_id ID del usuario/tripulante
     * @param int $chofer_id  (Opcional) ID del chofer para evitar duplicidad de rol
     * @return bool Devuelve true si se insertó con éxito, false si no
     */
    public function guardarTripulante($reparto_id, $usuario_id, $chofer_id = 0)
    {
        // Validar que ambos IDs sean enteros válidos, mayores a 0 y que no sea el mismo chofer
        if (intval($reparto_id) <= 0 || intval($usuario_id) <= 0 || intval($usuario_id) === intval($chofer_id)) {
            return false;
        }

        try {
            // Usamos INSERT IGNORE por si ya existe el registro no rompa la ejecución
            $sqlT = "INSERT IGNORE INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)";
            $stmtT = $this->db->prepare($sqlT);

            if (!$stmtT) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            // "ii" -> ambos son enteros (reparto_id, usuario_id)
            $stmtT->bind_param("ii", $reparto_id, $usuario_id);

            return $stmtT->execute();

        } catch (Exception $e) {
            error_log("Error al guardar tripulante (Reparto: {$reparto_id}, Usuario: {$usuario_id}): " . $e->getMessage());
            return false;
        }
    }
    public function entregarEnPatioCliente($datos)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $vehiculo_virtual_id = 999;

            $movimiento_id = intval($datos['movimiento_id'] ?? 0);
            $trabajador_id = intval($datos['chofer_id'] ?? 0);
            $usuario_operador_id = intval($datos['usuario_sistema_id'] ?? 0);

            $observaciones = !empty($datos['observaciones'])
                ? trim($datos['observaciones'])
                : 'Entrega Directa en Patio';

            $tripulantes = (
                isset($datos['tripulantes']) &&
                is_array($datos['tripulantes'])
            ) ? $datos['tripulantes'] : [];

            if ($movimiento_id <= 0) {
                throw new Exception("Movimiento inválido.");
            }

            if ($usuario_operador_id <= 0) {
                throw new Exception("Usuario operador inválido.");
            }

            // =====================================================
            // VALIDAR VEHÍCULO
            // =====================================================
            $sqlVeh = "SELECT id FROM transporte_vehiculos WHERE id = ? LIMIT 1";
            $stmtVeh = $this->db->prepare($sqlVeh);
            if (!$stmtVeh) {
                throw new Exception("Error prepare vehículo: " . $this->db->error);
            }
            $stmtVeh->bind_param("i", $vehiculo_virtual_id);
            $stmtVeh->execute();
            $stmtVeh->store_result();

            if ($stmtVeh->num_rows <= 0) {
                throw new Exception("El vehículo virtual {$vehiculo_virtual_id} no existe.");
            }
            $stmtVeh->close();

            // =====================================================
            // VALIDAR DUPLICADOS
            // =====================================================
            $sqlCheck = "
            SELECT rp.id
            FROM transporte_rutas_puntos rp
            INNER JOIN transporte_repartos_maestro trm
                ON rp.reparto_id = trm.id
            WHERE trm.entrega_venta_id = ?
            AND trm.estado_reparto != 'cancelado'
            LIMIT 1
        ";
            $stmtCheck = $this->db->prepare($sqlCheck);
            if (!$stmtCheck) {
                throw new Exception("Error prepare check: " . $this->db->error);
            }
            $stmtCheck->bind_param("i", $movimiento_id);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows > 0) {
                throw new Exception("Ya existe un proceso de entrega activo para este despacho.");
            }
            $stmtCheck->close();

            // =====================================================
            // ARMAR TRIPULACIÓN
            // =====================================================
            if ($trabajador_id > 0) {
                array_unshift($tripulantes, $trabajador_id);
            }

            $tripulantes = array_unique(
                array_map('intval', $tripulantes)
            );

            // =====================================================
            // INICIAR TRANSACCIÓN
            // =====================================================
            $this->db->begin_transaction();

            // =====================================================
            // INSERT MAESTRO
            // =====================================================
            $estado_maestro = 'completado';
            $sqlM = "
            INSERT INTO transporte_repartos_maestro (
                vehiculo_id, usuario_encargado_id, entrega_venta_id, fecha_programada, estado_reparto, hora_llegada_real
            ) VALUES (?, ?, ?, CURDATE(), ?, NOW())
        ";
            $stmtM = $this->db->prepare($sqlM);
            if (!$stmtM) {
                throw new Exception("Error prepare maestro: " . $this->db->error);
            }
            $stmtM->bind_param("iiis", $vehiculo_virtual_id, $usuario_operador_id, $movimiento_id, $estado_maestro);
            $stmtM->execute();

            $reparto_id = intval($this->db->insert_id);
            if ($reparto_id <= 0) {
                throw new Exception("No se generó reparto_id.");
            }
            $stmtM->close();

            // =====================================================
            // INSERT PUNTO RUTA
            // =====================================================
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
            $estado_punto = 'visitado';
            $descripcion = "ENTREGA EN PATIO: " . $observaciones;
            $sqlP = "
            INSERT INTO transporte_rutas_puntos (
                reparto_id, orden_visita, descripcion_punto, estado_punto, entrega_id
            ) VALUES (?, 1, ?, ?, ?)
        ";
            $stmtP = $this->db->prepare($sqlP);
            if (!$stmtP) {
                throw new Exception("Error prepare punto: " . $this->db->error);
            }
            $stmtP->bind_param("issi", $reparto_id, $descripcion, $estado_punto, $entrega_id);
            $stmtP->execute();
            $stmtP->close();

            // =====================================================
            // INSERT TRIPULANTES
            // =====================================================
            if (!empty($tripulantes)) {
                $sqlT = "INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)";
                $stmtT = $this->db->prepare($sqlT);
                if (!$stmtT) {
                    throw new Exception("Error prepare tripulantes: " . $this->db->error);
                }

                foreach ($tripulantes as $uid) {
                    $uid = intval($uid);
                    if ($uid <= 0)
                        continue;

                    $stmtT->bind_param("ii", $reparto_id, $uid);
                    $stmtT->execute();
                }
                $stmtT->close();
            }

            // =====================================================
            // LIBERAR VEHÍCULO
            // =====================================================
            $sqlV = "UPDATE transporte_vehiculos SET estado_unidad = 'disponible' WHERE id = ?";
            $stmtV = $this->db->prepare($sqlV);
            if (!$stmtV) {
                throw new Exception("Error prepare vehículo update: " . $this->db->error);
            }
            $stmtV->bind_param("i", $vehiculo_virtual_id);
            $stmtV->execute();
            $stmtV->close();

            // =====================================================
            // COMMIT
            // =====================================================
            $this->db->commit();

            return [
                'success' => true,
                'reparto_id' => $reparto_id,
                'message' => 'Entrega finalizada correctamente.'
            ];

        } catch (Exception $e) {
            // Rollback seguro comprobando únicamente si la transacción está activa
            if (isset($this->db) && method_exists($this->db, 'rollback')) {
                @$this->db->rollback();
            }

            error_log("ERROR entregarEnPatioCliente: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    // Función auxiliar para el controlador
    public function buscarRutaAbierta($vehiculo_id)
    {
        $sql = "SELECT viaje_folio FROM transporte_consolidacion 
            WHERE vehiculo_id = ? AND estatus_consolidado = 'abierto' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $vehiculo_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function listarViajesActivos($almacen_id = 0)
    {
        $almacen_id = intval($almacen_id);

        $sql = "SELECT 
                tc.viaje_folio,
                tc.vehiculo_id,
                tv.nombre as unidad,
                tv.placas,
                tc.entrega_id,
                -- Obtenemos el nombre del chofer desde trabajadores (usuario_encargado_id)
                (SELECT nombre FROM trabajadores WHERE id = trm.usuario_encargado_id LIMIT 1) as chofer,
                -- Concatenamos los tripulantes
                (SELECT GROUP_CONCAT(tr.nombre SEPARATOR ', ') 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN trabajadores tr ON ttd.usuario_id = tr.id
                 WHERE ttd.reparto_id = trm.id) as tripulantes,
                -- Detalles de lo que lleva el camión
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        '• <b>[', COALESCE(v.folio, 'S/F'), ']</b> ',
                        m.cantidad, ' ', p.unidad_medida,
                        ' - ', p.nombre
                    ) 
                    SEPARATOR '<br>'
                ) as detalles_carga
            FROM transporte_consolidacion tc
            INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            INNER JOIN transporte_vehiculos tv ON tc.vehiculo_id = tv.id
            LEFT JOIN movimientos m ON trm.entrega_venta_id = m.id
            LEFT JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id -- Unimos con ventas para sacar el almacén
            WHERE tc.estatus_consolidado = 'abierto'";

        // FILTRO DINÁMICO POR ALMACÉN
        if ($almacen_id > 0) {
            // Filtramos por el almacén de la venta original
            $sql .= " AND v.almacen_id = $almacen_id";
        }

        $sql .= " GROUP BY tc.viaje_folio, tc.vehiculo_id, tv.nombre, tv.placas";
        $sql .= " ORDER BY tc.viaje_folio DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function listarViajesActivosRepartos($almacen_id = 0, $fecha_inicio = null, $fecha_fin = null)
    {

        $almacen_id = intval($almacen_id);

        $sql = "SELECT 
                tc.viaje_folio,
                tc.vehiculo_id,
                tv.nombre as unidad,
                tv.placas,
                (SELECT nombre 
                 FROM trabajadores 
                 WHERE id = trm.usuario_encargado_id 
                 LIMIT 1) as chofer,

                (SELECT GROUP_CONCAT(tr.nombre SEPARATOR ', ') 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN trabajadores tr ON ttd.usuario_id = tr.id
                 WHERE ttd.reparto_id = trm.id) as tripulantes,

                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        '• <b>[', COALESCE(v.folio, 'S/F'), ']</b> ',
                        m.cantidad, ' ', p.unidad_medida,
                        ' - ', p.nombre
                    ) 
                    SEPARATOR '<br>'
                ) as detalles_carga

            FROM transporte_consolidacion tc
            INNER JOIN transporte_repartos_maestro trm 
                ON tc.reparto_id = trm.id
            INNER JOIN transporte_vehiculos tv 
                ON tc.vehiculo_id = tv.id
            LEFT JOIN movimientos m 
                ON trm.entrega_venta_id = m.id
            LEFT JOIN productos p 
                ON m.producto_id = p.id
            LEFT JOIN ventas v 
                ON m.referencia_id = v.id

            WHERE tc.estatus_consolidado = 'abierto'";

        $params = [];
        $types = '';

        if ($almacen_id > 0) {
            $sql .= " AND v.almacen_id = ?";
            $params[] = $almacen_id;
            $types .= 'i';
        }

        if (!empty($fecha_inicio)) {
            $sql .= " AND DATE(tc.fecha_creacion) >= ?";
            $params[] = $fecha_inicio;
            $types .= 's';
        }

        if (!empty($fecha_fin)) {
            $sql .= " AND DATE(tc.fecha_creacion) <= ?";
            $params[] = $fecha_fin;
            $types .= 's';
        }

        $sql .= " GROUP BY tc.viaje_folio, tc.vehiculo_id, tv.nombre, tv.placas";
        $sql .= " ORDER BY tc.viaje_folio DESC";

        $stmt = $this->db->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function listarHistorialDeRepartos($almacen_id = 0)
    {
        $almacen_id = intval($almacen_id);

        $sql = "SELECT 
                tc.viaje_folio,
                tc.vehiculo_id,
                tc.estatus_consolidado AS estado_final,
                tv.nombre as unidad,
                tv.placas,
                -- 1. Chofer: Tomamos el encargado del primer reparto del viaje
                (SELECT t.nombre FROM trabajadores t 
                 INNER JOIN transporte_repartos_maestro trm2 ON t.id = trm2.usuario_encargado_id 
                 WHERE trm2.id = tc.reparto_id LIMIT 1) as chofer,
                 
                -- 2. Tripulantes: Concatenamos todos los ayudantes de los repartos del viaje
                (SELECT GROUP_CONCAT(DISTINCT tr.nombre SEPARATOR ', ') 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN trabajadores tr ON ttd.usuario_id = tr.id
                 INNER JOIN transporte_consolidacion tc2 ON ttd.reparto_id = tc2.reparto_id
                 WHERE tc2.viaje_folio = tc.viaje_folio) as tripulantes,
                 
                -- 3. DESTINOS CORREGIDOS: Busca todos los puntos de todos los repartos del mismo folio
                (SELECT GROUP_CONCAT(DISTINCT COALESCE(rp.descripcion_punto, 'Entrega en Obra') SEPARATOR '<br>')
                 FROM transporte_rutas_puntos rp 
                 INNER JOIN transporte_consolidacion tc3 ON rp.reparto_id = tc3.reparto_id
                 WHERE tc3.viaje_folio = tc.viaje_folio) as ruta_destinos,
                 
                -- 4. Detalles de carga consolidada
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        '• <b>[', COALESCE(v.folio, 'S/F'), ']</b> ',
                        m.cantidad, ' ', p.unidad_medida,
                        ' - ', p.nombre
                    ) 
                    SEPARATOR '<br>'
                ) as detalles_carga
                
            FROM transporte_consolidacion tc
            INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            INNER JOIN transporte_vehiculos tv ON tc.vehiculo_id = tv.id
            LEFT JOIN movimientos m ON trm.entrega_venta_id = m.id
            LEFT JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id 
            WHERE tc.estatus_consolidado != 'abierto'";

        if ($almacen_id > 0) {
            $sql .= " AND v.almacen_id = $almacen_id";
        }

        $sql .= " GROUP BY tc.viaje_folio, tc.vehiculo_id, tv.nombre, tv.placas, tc.estatus_consolidado";
        $sql .= " ORDER BY tc.viaje_folio DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function finalizarViajeVehiculo($vehiculo_id)
    {
        try {
            $this->db->begin_transaction();

            // 1. Actualizar Maestro de Repartos
            $sqlM = "UPDATE transporte_repartos_maestro 
                 SET estado_reparto = 'completado', 
                     fecha_entrega = NOW() 
                 WHERE vehiculo_id = ? AND estado_reparto = 'en_transito'";
            $stmtM = $this->db->prepare($sqlM);
            $stmtM->bind_param("i", $vehiculo_id);
            $stmtM->execute();

            // 2. Actualizar Puntos de Ruta (Opcional, para que queden como completados)
            $sqlP = "UPDATE transporte_rutas_puntos rp
                 INNER JOIN transporte_repartos_maestro trm ON rp.reparto_id = trm.id
                 SET rp.estado_punto = 'visitado'
                 WHERE trm.vehiculo_id = ? AND trm.estado_reparto = 'completado'";
            $stmtP = $this->db->prepare($sqlP);
            $stmtP->bind_param("i", $vehiculo_id);
            $stmtP->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    public function finalizarViajeLogistica($vehiculo_id, $viaje_folio)
    {
        try {
            $this->db->begin_transaction();

            // 1. Cerramos la tabla de consolidación (la que creamos para agrupar)
            $sqlC = "UPDATE transporte_consolidacion 
                 SET estatus_consolidado = 'cerrado' 
                 WHERE viaje_folio = ? AND vehiculo_id = ?";
            $stmtC = $this->db->prepare($sqlC);
            $stmtC->bind_param("si", $viaje_folio, $vehiculo_id);
            $stmtC->execute();

            // 2. Actualizamos los repartos maestros
            // Usamos 'completado' (que es el valor de tu ENUM) y 'hora_llegada_real' (que sí existe)
            $sqlR = "UPDATE transporte_repartos_maestro trm
                 INNER JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
                 SET trm.estado_reparto = 'completado', 
                     trm.hora_llegada_real = NOW() 
                 WHERE tc.viaje_folio = ?";

            $stmtR = $this->db->prepare($sqlR);
            $stmtR->bind_param("s", $viaje_folio);
            $stmtR->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    public function cancelarViajeCompleto($folio_viaje, $vehiculo_id, $usuario)
    {
        try {
            // 1. Mapear qué entregas existen en este folio de viaje
            $sqlActuales = "SELECT trm.entrega_venta_id, trm.id as reparto_id 
                        FROM transporte_consolidacion tc
                        JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                        WHERE tc.viaje_folio = ?";
            $stmtA = $this->db->prepare($sqlActuales);
            $stmtA->bind_param("s", $folio_viaje);
            $stmtA->execute();
            $resA = $stmtA->get_result();

            $mapeo_bd = [];
            while ($row = $resA->fetch_assoc()) {
                $mapeo_bd[$row['entrega_venta_id']] = $row['reparto_id'];
            }

            // 2. Sincronización: Quitar los que ya no vienen en el JSON
            foreach ($mapeo_bd as $mov_id_bd => $reparto_id) {

                $this->quitarEntregaDeRuta($mov_id_bd, $mov_id_bd, $usuario);



            }

            $vehiculo_id = intval($vehiculo_id);

            // 1. Buscamos todos los repartos asociados a este folio y vehículo
            $sqlBusqueda = "SELECT reparto_id FROM transporte_consolidacion 
                        WHERE viaje_folio = ? AND vehiculo_id = ?";

            $stmtB = $this->db->prepare($sqlBusqueda);
            $stmtB->bind_param("si", $folio_viaje, $vehiculo_id);
            $stmtB->execute();
            $resB = $stmtB->get_result();
            return true;


        } catch (Exception $e) {
            if (isset($this->db) && $this->db->in_transaction)
                $this->db->rollback();
            throw $e;
        }
    }
    public function cancelarEntregaIndividual()
    {

    }
    public function actualizarLogisticaCompleta($datos)
    {
        try {
            $this->db->begin_transaction();

            $folio_viaje = $datos['viaje_folio'];
            $vehiculo_id = intval($datos['vehiculo_id']);
            $nuevo_chofer_id = intval($datos['chofer_id']);
            $nuevos_trip = isset($datos['tripulantes']) ? $datos['tripulantes'] : [];
            // 'destinos' debe ser un array: [ ['movimiento_id' => 10, 'destino' => 'Calle Falsa 123'], ... ]
            $destinos_editados = isset($datos['destinos']) ? $datos['destinos'] : [];

            // 1. ACTUALIZAR CHOFER (Responsable)
            $sqlU = "UPDATE transporte_repartos_maestro trm
                 INNER JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
                 SET trm.usuario_encargado_id = ?
                 WHERE tc.viaje_folio = ? AND tc.vehiculo_id = ?";
            $stmtU = $this->db->prepare($sqlU);
            $stmtU->bind_param("isi", $nuevo_chofer_id, $folio_viaje, $vehiculo_id);
            $stmtU->execute();

            // 2. OBTENER REPARTOS PARA TRIPULACIÓN Y DESTINOS
            $sqlR = "SELECT tc.reparto_id, trm.entrega_venta_id 
                 FROM transporte_consolidacion tc
                 INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                 WHERE tc.viaje_folio = ?";
            $stmtR = $this->db->prepare($sqlR);
            $stmtR->bind_param("s", $folio_viaje);
            $stmtR->execute();
            $repartos = $stmtR->get_result()->fetch_all(MYSQLI_ASSOC);

            foreach ($repartos as $r) {
                $rid = $r['reparto_id'];
                $mov_id = $r['entrega_venta_id'];

                // 2.1 REFRESCAR TRIPULACIÓN (Ayudantes)
                $this->db->query("DELETE FROM transporte_tripulantes_detalle WHERE reparto_id = $rid");
                if (!empty($nuevos_trip)) {
                    $stmtT = $this->db->prepare("INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)");
                    foreach ($nuevos_trip as $u_id) {
                        if (intval($u_id) === $nuevo_chofer_id)
                            continue;
                        $uid = intval($u_id);
                        $stmtT->bind_param("ii", $rid, $uid);
                        $stmtT->execute();
                    }
                }

                // 2.2 ACTUALIZAR DESTINO INDIVIDUAL
                // Buscamos si en los datos recibidos hay un nuevo destino para este movimiento específico
                foreach ($destinos_editados as $edit) {
                    if (intval($edit['movimiento_id']) === intval($mov_id)) {
                        $nuevo_destino = $edit['destino'];
                        $stmtD = $this->db->prepare("UPDATE transporte_rutas_puntos SET descripcion_punto = ? WHERE reparto_id = ?");
                        $stmtD->bind_param("si", $nuevo_destino, $rid);
                        $stmtD->execute();
                        break;
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->in_transaction)
                $this->db->rollback();
            throw $e;
        }
    }
    public function getDetallesViaje($folio_viaje)
    {
        // 1. Cabecera Detallada (Unidad, Chofer y Almacén)
        $sqlHeader = "SELECT 
                    tc.viaje_folio,
                    tc.vehiculo_id,
                    tv.nombre AS unidad_nombre,
                    tv.placas AS unidad_placas,
                    trm.usuario_encargado_id AS chofer_id,
                    t_chofer.nombre AS nombre_chofer,
                    trm.estado_reparto AS estatus_logistico,
                    v.almacen_id
                  FROM transporte_consolidacion tc
                  INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                  INNER JOIN transporte_vehiculos tv ON tc.vehiculo_id = tv.id
                  LEFT JOIN trabajadores t_chofer ON trm.usuario_encargado_id = t_chofer.id
                  LEFT JOIN movimientos m ON trm.entrega_venta_id = m.id
                  LEFT JOIN ventas v ON m.referencia_id = v.id
                  WHERE tc.viaje_folio = ? 
                  LIMIT 1";

        $stmtH = $this->db->prepare($sqlHeader);
        $stmtH->bind_param("s", $folio_viaje);
        $stmtH->execute();
        $header = $stmtH->get_result()->fetch_assoc();

        if (!$header)
            return null;

        // 2. IDs de Tripulantes (Para el Select2 o múltiple en el formulario)
        $sqlT = "SELECT DISTINCT ttd.usuario_id 
         FROM transporte_tripulantes_detalle ttd
         INNER JOIN transporte_consolidacion tc ON ttd.reparto_id = tc.reparto_id
         WHERE tc.viaje_folio = ?";
        $stmtT = $this->db->prepare($sqlT);
        $stmtT->bind_param("s", $folio_viaje); // Ajustado para usar el mismo parámetro
        $stmtT->execute();
        $resT = $stmtT->get_result();
        $header['tripulantes_ids'] = [];
        while ($r = $resT->fetch_assoc()) {
            $header['tripulantes_ids'][] = $r['usuario_id'];
        }

        // 3. Materiales, Destinos y Especificaciones Técnicas
        $sqlMat = "SELECT 
                    m.id AS movimiento_id, 
                    p.nombre AS producto, 
                    p.sku AS sku,
                    p.unidad_medida AS um,
                    p.unidad_reporte as ur,
                    p.factor_conversion as fc,
                    m.cantidad, 
                    v.folio AS folio_venta, 
                    c.nombre_comercial AS cliente,
                    rp.descripcion_punto AS destino,
                    rp.orden_visita
               FROM transporte_consolidacion tc
               INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
               INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
               INNER JOIN productos p ON m.producto_id = p.id
               LEFT JOIN ventas v ON m.referencia_id = v.id
               LEFT JOIN clientes c ON v.id_cliente = c.id
               LEFT JOIN transporte_rutas_puntos rp ON trm.id = rp.reparto_id
               WHERE tc.viaje_folio = ?
               ORDER BY rp.orden_visita ASC";

        $stmtM = $this->db->prepare($sqlMat);
        $stmtM->bind_param("s", $folio_viaje);
        $stmtM->execute();
        $header['materiales'] = $stmtM->get_result()->fetch_all(MYSQLI_ASSOC);

        return $header;
    }
    public function getResumenDespacho($movimiento_id)
    {
        $sql = "SELECT 
                m.id as movimiento_id,
                m.cantidad,
                p.nombre as producto_nombre,
                v.folio as folio_venta,
                c.nombre_comercial as cliente,
                
                -- Usuario que registró el movimiento
                u_mov.nombre as administrador_sistema,
                
                -- Logística
                trm.id as reparto_id,
                trm.estado_reparto,
                tv.nombre as vehiculo,
                tv.placas,
                u_chofer.nombre as chofer_nombre,
                tc.viaje_folio,
                
                -- Patio
                rsl.fecha_despacho as fecha_patio,
                u_patio.nombre as despachador_patio,
                u_despacho.nombre as administrador_patio
                
            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN usuarios u_mov ON m.usuario_registra_id = u_mov.id
            
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            LEFT JOIN transporte_vehiculos tv ON trm.vehiculo_id = tv.id
            LEFT JOIN usuarios u_chofer ON trm.usuario_encargado_id = u_chofer.id
            LEFT JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
            
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
            LEFT JOIN usuarios u_despacho ON rsl.usuario_despacho_id = u_despacho.id
            
            WHERE m.id = ? LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $movimiento_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res)
            return null;

        // Tripulantes: transporte_tripulantes_detalle.usuario_id -> usuarios
        $res['tripulantes'] = [];
        if ($res['reparto_id']) {
            $sqlT = "SELECT t.nombre 
                 FROM transporte_tripulantes_detalle ttd
                LEFT JOIN trabajadores t ON tttd.usuario_encargado_id = t.id
                 WHERE ttd.reparto_id = ?";
            $stmtT = $this->db->prepare($sqlT);
            $stmtT->bind_param("i", $res['reparto_id']);
            $stmtT->execute();
            $res['tripulantes'] = $stmtT->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $res;
    }

    public function obtenerHistorialFisico($movimiento_id)
    {
        $sql = "SELECT 
                m.id as movimiento_id,
                m.cantidad,
                m.fecha as fecha_movimiento,
                p.nombre as producto_nombre,
                p.factor_conversion as fc,
                p.unidad_medida as um,
                p.unidad_reporte as ur,
                v.folio as folio_venta,
                c.nombre_comercial as cliente,
                
                -- DIRECCIÓN DE ENTREGA (Desde los puntos de ruta)
                trp.descripcion_punto as direccion_entrega,
                
                -- TRAZABILIDAD DE SISTEMA (Usuarios que operan el software)
                u_asigna.nombre as usuario_asigno_sistema,    -- El que capturó el movimiento/reparto
                u_valida.nombre as usuario_valida_patio,    -- El que dio salida oficial en el sistema
                
                -- TRAZABILIDAD FÍSICA (Trabajadores que mueven el material)
                t_chofer.nombre as trabajador_entrega_ruta,  -- Chofer asignado
                t_patio.nombre as trabajador_despacho_patio, -- Almacenista que cargó
                
                -- DATOS DE LOGÍSTICA
                trm.id as reparto_id,
                trm.estado_reparto,
                tv.nombre as vehiculo,
                 trm.hora_llegada_real AS fecha_llegada,
                tv.placas,
                tc.viaje_folio,
                
                -- DATOS DE PATIO
                rsl.fecha_despacho as fecha_patio
                
            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            
            -- ¿Quién asignó/creó el movimiento en el sistema?
            LEFT JOIN usuarios u_asigna ON m.usuario_registra_id = u_asigna.id
            
            -- LOGÍSTICA: Relación con Repartos
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            LEFT JOIN transporte_vehiculos tv ON trm.vehiculo_id = tv.id
            LEFT JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
            -- El chofer es un trabajador
            LEFT JOIN trabajadores t_chofer ON trm.usuario_encargado_id = t_chofer.id
            -- Dirección del primer punto de la ruta (donde se entrega)
            LEFT JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id AND trp.orden_visita = 1
            
            -- PATIO: Registro físico de salida
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            -- El despachador físico es un trabajador
            LEFT JOIN trabajadores t_patio ON rsl.usuario_patio_id = t_patio.id
            -- El que valida la salida en el sistema es un usuario (Administrativo de patio)
            LEFT JOIN usuarios u_valida ON rsl.usuario_despacho_id = u_valida.id
            
            WHERE m.id = ? LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $movimiento_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res)
            return null;

        // Tripulantes/Ayudantes (Siempre de la tabla trabajadores)
        $res['tripulantes'] = [];
        if ($res['reparto_id']) {
            $sqlT = "SELECT t.nombre 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN trabajadores t ON ttd.usuario_id = t.id
                 WHERE ttd.reparto_id = ?";
            $stmtT = $this->db->prepare($sqlT);
            $stmtT->bind_param("i", $res['reparto_id']);
            $stmtT->execute();
            $res['tripulantes'] = $stmtT->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $res;
    }

    public function getTripulantesPorReparto($reparto_id)
    {
        $sql = "SELECT t.nombre 
            FROM transporte_tripulantes_detalle ttd
            LEFT JOIN trabajadores t ON ttd.usuario_id = t.id
            WHERE ttd.reparto_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $reparto_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getMonitorEntregas($almacen_id = 0, $inicio = 0, $limite = 25, $fecha_inicio = null, $fecha_fin = null)
    {
        if (ob_get_level())
            ob_clean();

        // ✅ 2. Asignamos las fechas dinámicas dentro de la función si no se enviaron
        if (!$fecha_inicio) {
            $fecha_inicio = date('Y-m-01'); // 'Y' mayúscula para 4 dígitos (Ej. 2026-06-01)
        }
        if (!$fecha_fin) {
            $fecha_fin = date('Y-m-t'); // 'Y' mayúscula para 4 dígitos (Ej. 2026-06-30)
        }
        if (ob_get_level())
            ob_clean();

        $where_almacen = ($almacen_id > 0) ? " AND m.almacen_origen_id = ? " : "";

        $sql = "SELECT 
                m.id AS movimiento_id, 
                trm.id AS reparto_id,
                IFNULL(tc.viaje_folio, CONCAT('MOV-', m.id)) AS grupo_id,
                v.id AS venta_id,
                tc.viaje_folio AS numero_ruta,
                IF(tc.viaje_folio IS NOT NULL, 'RUTA', 'MOSTRADOR') AS tipo_salida,
                COALESCE(tc.viaje_folio, v.folio) AS identificador_visual,
                CASE 
                    WHEN tc.viaje_folio IS NOT NULL THEN 'VARIOS CLIENTES (RUTA)'
                    ELSE c.nombre_comercial
                END AS cliente_display,
                CASE 
                    WHEN tc.viaje_folio IS NOT NULL THEN 'MATERIALES DIVERSOS (CARGA CONSOLIDADA)'
                    ELSE p.nombre 
                END AS producto_nombre,
                m.cantidad as total_bultos,
                p.unidad_reporte,
                p.unidad_medida,
                p.factor_conversion,
                CASE 
                    WHEN tc.viaje_folio IS NOT NULL THEN IFNULL(tv.nombre, 'POR ASIGNAR') 
                    ELSE 'RECOLECCIÓN PROPIA' 
                END AS vehiculo,
                COALESCE(t_chofer.nombre, t_patio.nombre, u_reg.nombre, 'POR ASIGNAR') AS responsable,
                (SELECT GROUP_CONCAT(DISTINCT ls.codigo_lote SEPARATOR ', ')
                 FROM lotes_movimientos_salida lms
                 INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                 WHERE lms.entrega_venta_id = m.id 
                 OR (lms.detalle_venta_id > 0 AND lms.detalle_venta_id IN (
                     SELECT dv.id FROM detalle_venta dv WHERE dv.venta_id = v.id
                 ))
                ) AS lotes_involucrados,
                DATE_FORMAT(MAX(IFNULL(rsl.fecha_despacho, m.fecha)), '%d/%m/%Y %H:%i') AS fecha_evento

            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            INNER JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN usuarios u_reg ON m.usuario_registra_id = u_reg.id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id AND trm.estado_reparto != 'cancelado'
            LEFT JOIN transporte_vehiculos tv ON trm.vehiculo_id = tv.id
            LEFT JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
            LEFT JOIN trabajadores t_chofer ON trm.usuario_encargado_id = t_chofer.id
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN trabajadores t_patio ON rsl.usuario_despacho_id = t_patio.id 

            WHERE m.tipo = 'salida' 
            -- CORRECCIÓN: Filtramos por la fecha del movimiento para no matar el LEFT JOIN, y usamos ?
            AND DATE(m.fecha) >= ? AND DATE(m.fecha) <= ?
            $where_almacen

            GROUP BY grupo_id
            ORDER BY MAX(m.fecha) DESC 
            LIMIT ?, ?";

        $stmt = $this->db->prepare($sql);

        // CORRECCIÓN: Agregar las fechas al bind_param de forma dinámica
        if ($almacen_id > 0) {
            // "ssiii" -> String (fecha_ini), String (fecha_fin), Int (almacen), Int (inicio), Int (limite)
            $stmt->bind_param("ssiii", $fecha_inicio, $fecha_fin, $almacen_id, $inicio, $limite);
        } else {
            // "ssii" -> String (fecha_ini), String (fecha_fin), Int (inicio), Int (limite)
            $stmt->bind_param("ssii", $fecha_inicio, $fecha_fin, $inicio, $limite);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['numero_ruta'] != null) {
                $row['lectura_fisica'] = "CARGA CONSOLIDADA";
            } else {
                $txt = "";
                $f = (float) $row['factor_conversion'];
                $val = (float) $row['total_bultos'];

                if ($f > 1) {
                    $entero = floor($val / $f);
                    $resto = fmod($val, $f);
                    if ($entero > 0)
                        $txt .= (int) $entero . " " . $row['unidad_reporte'];
                    if ($resto > 0)
                        $txt .= ($txt !== "" ? " y " : "") . $resto . " " . $row['unidad_medida'];
                } else {
                    $txt = $val . " " . $row['unidad_medida'];
                }
                $row['lectura_fisica'] = ($txt === "") ? "0" : $txt;
            }

            $row['lotes_involucrados'] = $row['lotes_involucrados'] ?? 'SIN LOTE';
            $data[] = $row;
        }

        return $data;
    }
    /**
     * Detalle de un movimiento simple (sin ruta/reparto)
     * Trae: cliente, producto, cantidad, quién despachó en sistema,
     * quién entregó físicamente, cuándo se entregó.
     */
    public function getDetalleMovimientoNormal($movimiento_id)
    {
        $sql = "SELECT 
                m.id AS movimiento_id,
                m.cantidad,
                p.nombre AS producto,
                p.unidad_medida,
                p.unidad_reporte,
                p.factor_conversion,
                DATE_FORMAT(m.fecha, '%d/%m/%Y %H:%i') AS fecha_salida,

                -- Venta y Cliente
                v.folio AS folio_venta,
                c.nombre_comercial AS cliente,
                c.telefono AS cliente_telefono,
                c.direccion AS cliente_direccion,

                -- Quién registró el movimiento en el sistema
                u_asigna.nombre AS usuario_asigno_sistema,

                -- Quién validó la salida en el sistema (admin de patio)
                u_despacho.nombre AS usuario_valida_patio,

                -- Quién entregó físicamente (trabajador de patio)
                u_patio.nombre AS trabajador_despacho_patio,

                -- Cuándo salió físicamente
                DATE_FORMAT(rsl.fecha_despacho, '%d/%m/%Y %H:%i') AS fecha_despacho

            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN usuarios u_asigna ON m.usuario_registra_id = u_asigna.id
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
            LEFT JOIN usuarios u_despacho ON rsl.usuario_despacho_id = u_despacho.id
            WHERE m.id = ?
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $movimiento_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res)
            return null;

        // Formateo de cantidad con factor de conversión
        $cantidad = floatval($res['cantidad']);
        $factor = floatval($res['factor_conversion'] ?? 1);

        if ($factor > 1) {
            $enteros = (int) floor($cantidad / $factor);
            $sobrantes = fmod($cantidad, $factor);
            $res['cantidad_display'] = $sobrantes > 0
                ? "{$enteros} {$res['unidad_reporte']} + {$sobrantes} {$res['unidad_medida']}"
                : "{$enteros} {$res['unidad_reporte']}";
        } else {
            $res['cantidad_display'] = "{$cantidad} {$res['unidad_medida']}";
        }

        return $res;
    }

    /**
     * Obtiene el listado completo de viajes con detalle de rutas, 
     * productos, conductores y ayudantes.
     * * @param PDO $db Conexión a la base de datos sistema_almacenes
     * @return array Lista de movimientos logísticos
     */
    /**
     * Obtiene el reporte de viajes. 
     * Si se envía un folio, filtra por ese específico; si no, trae todos.
     */
    /**
     * Obtiene el reporte detallado de un viaje por su folio o el listado general.
     * Adaptado para el sistema cfsistem.
     * * @param string|null $folio_folio El folio del viaje (ej: RUT-260324-02-25)
     * @return array Arreglo asociativo con los datos para el modal
     */
    public function obtenerViajesLogistica($folio_folio = null)
    {
        try {
            $sql = "SELECT 
                    tc.viaje_folio AS folio_viaje,
                    tc.fecha_creacion AS fecha_viaje,
                    trm.hora_llegada_real AS fecha_llegada,
                    trm.estado_reparto AS estatus_logistico,
                    tv.nombre AS unidad_nombre,
                    tv.placas AS unidad_placas,
                    u_chofer.nombre AS nombre_chofer,
                    (SELECT GROUP_CONCAT(u_ayu.nombre SEPARATOR ' / ') 
                     FROM transporte_tripulantes_detalle ttd
                     INNER JOIN trabajadores u_ayu ON ttd.usuario_id = u_ayu.id
                     WHERE ttd.reparto_id = tc.reparto_id) AS ayudantes,
                    trp.orden_visita,
                    trp.descripcion_punto AS direccion_entrega,
                    trp.estado_punto AS estatus_parada,
                    trp.latitud, 
                    trp.longitud,
                    v.id as numeroVenta,
                    v.folio AS folio_venta,
                    c.nombre_comercial AS cliente,
                    c.telefono AS tel_cliente,
                    p.nombre AS producto_nombre,
                    p.factor_conversion as fcr,
                    
                    m.cantidad,
                    p.unidad_medida AS um,
                    p.unidad_reporte as urr,
                    p.sku AS SKU
                FROM transporte_consolidacion tc
                INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                INNER JOIN transporte_vehiculos tv ON tc.vehiculo_id = tv.id
                INNER JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id 
                INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN ventas v ON m.referencia_id = v.id
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN trabajadores u_chofer ON trm.usuario_encargado_id = u_chofer.id";

            if (!empty($folio_folio)) {
                $sql .= " WHERE tc.viaje_folio = ?";
            }

            $sql .= " ORDER BY tc.fecha_creacion DESC, tc.viaje_folio ASC, trp.orden_visita ASC";

            $stmt = $this->db->prepare($sql);

            if (!empty($folio_folio)) {
                $stmt->bind_param("s", $folio_folio);
            }

            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }
    public function obtenerEntregasPorVenta($idVenta)
    {

        $sql = "SELECT 
                tc.viaje_folio AS folio_viaje,
                tc.fecha_creacion AS fecha_viaje,
                trm.hora_llegada_real AS fecha_llegada,
                trm.estado_reparto AS estatus_logistico,
                tv.nombre AS unidad_nombre,
                tv.placas AS unidad_placas,
                u_chofer.nombre AS nombre_chofer,

                (
                    SELECT GROUP_CONCAT(u_ayu.nombre SEPARATOR ' / ') 
                    FROM transporte_tripulantes_detalle ttd
                    INNER JOIN trabajadores u_ayu 
                        ON ttd.usuario_id = u_ayu.id
                    WHERE ttd.reparto_id = tc.reparto_id
                ) AS ayudantes,

                trp.orden_visita,
                trp.descripcion_punto AS direccion_entrega,
                trp.estado_punto AS estatus_parada,
                trp.latitud, 
                trp.longitud,
                  v.id as numeroVenta,

                v.folio AS folio_venta,
                c.nombre_comercial AS cliente,
                c.telefono AS tel_cliente,

                GROUP_CONCAT(
                    CONCAT(
                        p.nombre,
                        ' (',
                        m.cantidad,
                        ' ',
                        p.unidad_medida,
                        ')'
                    )
                    SEPARATOR ', '
                ) AS productos

            FROM transporte_consolidacion tc

            INNER JOIN transporte_repartos_maestro trm 
                ON tc.reparto_id = trm.id

            INNER JOIN transporte_vehiculos tv 
                ON tc.vehiculo_id = tv.id

            INNER JOIN transporte_rutas_puntos trp 
                ON trm.id = trp.reparto_id 

            INNER JOIN movimientos m 
                ON trm.entrega_venta_id = m.id

            INNER JOIN productos p 
                ON m.producto_id = p.id

            LEFT JOIN ventas v 
                ON m.referencia_id = v.id

            LEFT JOIN clientes c 
                ON v.id_cliente = c.id

            LEFT JOIN trabajadores u_chofer 
                ON trm.usuario_encargado_id = u_chofer.id

            WHERE v.id = ?

            GROUP BY tc.viaje_folio

            ORDER BY tc.fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $idVenta);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $data = [];

        while ($row = $resultado->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    public function obtenerEntregas($idVenta)
    {
        // Añadimos GROUP BY en.id para que no se dupliquen por los productos
        $sql = "SELECT 
    ROW_NUMBER() OVER (ORDER BY en.id ASC) AS num_registro,
    en.id AS entrega_id,
    en.venta_id,
    en.usuario_id,
    en.fecha,tc.id as folio,
     MAX(trp.descripcion_punto) AS direccion_entrega,
    -- Traemos el folio del viaje real de ese reparto
    IFNULL(tc.viaje_folio, 'Sin Viaje Asignado') AS viaje_folio
FROM entregas_venta en
-- 1. Vamos directo a los repartos que se hicieron para esta venta
INNER JOIN transporte_repartos_maestro trm 
    ON trm.id = (
        -- Subconsulta quirúrgica: Busca el reparto exacto donde se involucró 
        -- el movimiento de ESTA entrega en específico
        SELECT trm_sub.id 
        FROM transporte_repartos_maestro trm_sub
        INNER JOIN movimientos m_sub ON trm_sub.entrega_venta_id = m_sub.id
        INNER JOIN detalle_entrega de_sub ON de_sub.entrega_id = en.id
        INNER JOIN detalle_venta dv_sub ON dv_sub.id = de_sub.detalle_venta_id
        WHERE m_sub.referencia_id = en.venta_id 
        AND m_sub.producto_id = dv_sub.producto_id
        LIMIT 1
    )
-- 2. Obtenemos el folio del viaje consolidado de ese camión
INNER JOIN transporte_consolidacion tc 
    ON tc.reparto_id = trm.id
     INNER JOIN transporte_rutas_puntos trp on trp.entrega_id=en.id
WHERE en.venta_id = ?
GROUP BY en.id";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $data = [];
        while ($row = $resultado->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    public function obtenerRutaDeEntregaDeVenta($idVenta, $idRuta)
    {

        $sql = "SELECT 
    t.folio_viaje,
    t.fecha_viaje,
    t.fecha_llegada,
    t.estatus_logistico,
    t.unidad_nombre,
    t.unidad_placas,
    t.nombre_chofer,
    t.ayudantes,
    t.orden_visita,
    t.direccion_entrega,
    t.estatus_parada,
    t.latitud,
    t.longitud,
    t.numeroVenta,
    t.folio_venta,
    t.cliente,
    t.tel_cliente,

    t.nombreProducto,
    t.totalCantidad,
    t.unidadMedida,
    t.factor,
    t.unidadReporte

FROM (

    SELECT 
        tc.viaje_folio AS folio_viaje,
        tc.fecha_creacion AS fecha_viaje,
        trm.hora_llegada_real AS fecha_llegada,
        trm.estado_reparto AS estatus_logistico,
        tv.nombre AS unidad_nombre,
        tv.placas AS unidad_placas,
        u_chofer.nombre AS nombre_chofer,

        (
            SELECT GROUP_CONCAT(u_ayu.nombre SEPARATOR ' / ') 
            FROM transporte_tripulantes_detalle ttd
            INNER JOIN trabajadores u_ayu 
                ON ttd.usuario_id = u_ayu.id
            WHERE ttd.reparto_id = tc.reparto_id
        ) AS ayudantes,

        trp.orden_visita,
        trp.descripcion_punto AS direccion_entrega,
        trp.estado_punto AS estatus_parada,
        trp.latitud, 
        trp.longitud,

        v.id AS numeroVenta,
        v.folio AS folio_venta,

        c.nombre_comercial AS cliente,
        c.telefono AS tel_cliente,

        p.nombre AS nombreProducto,
        p.factor_conversion AS factor,
        p.unidad_reporte AS unidadReporte,

        SUM(m.cantidad) AS totalCantidad,

        p.unidad_medida AS unidadMedida

    FROM transporte_consolidacion tc

    INNER JOIN transporte_repartos_maestro trm 
        ON tc.reparto_id = trm.id

        

    INNER JOIN transporte_vehiculos tv 
        ON tc.vehiculo_id = tv.id

    INNER JOIN transporte_rutas_puntos trp 
        ON trm.id = trp.reparto_id 

    INNER JOIN movimientos m 
        ON trm.entrega_venta_id = m.id

    INNER JOIN productos p 
        ON m.producto_id = p.id

    LEFT JOIN ventas v 
        ON m.referencia_id = v.id

    LEFT JOIN clientes c 
        ON v.id_cliente = c.id

    LEFT JOIN trabajadores u_chofer 
        ON trm.usuario_encargado_id = u_chofer.id
   
        JOIN entregas_venta en on en.venta_id =v.id

    WHERE en.id = ?
    
    AND tc.viaje_folio = ?

    GROUP BY 
        tc.viaje_folio,
        trp.descripcion_punto,
        p.id

) t

ORDER BY t.fecha_viaje DESC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            die($this->db->error);
        }

        $stmt->bind_param("is", $idVenta, $idRuta);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $data = [];

        while ($row = $resultado->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
    public function obtenerRutaDeEntregaPorEntrega($entrega_id, $idRuta)
    {

        $sql = "SELECT 
    t.folio_viaje,
    t.fecha_viaje,
    t.fecha_llegada,
    t.estatus_logistico,
    t.unidad_nombre,
    t.unidad_placas,
    t.nombre_chofer,
    t.ayudantes,
    t.orden_visita,
    t.direccion_entrega,
    t.estatus_parada,
    t.latitud,
    t.longitud,
    t.numeroVenta,
    t.folio_venta,
    t.cliente,
    t.tel_cliente,
    t.nombreProducto,
    t.totalCantidad,
    t.unidadMedida,
    t.factor,
    t.unidadReporte,
    t.equi,
    t.nombreEqui

FROM (

    SELECT 
        tc.viaje_folio AS folio_viaje,
        tc.fecha_creacion AS fecha_viaje,
        trm.hora_llegada_real AS fecha_llegada,
        trm.estado_reparto AS estatus_logistico,
        tv.nombre AS unidad_nombre,
        tv.placas AS unidad_placas,
        u_chofer.nombre AS nombre_chofer,

        (
            SELECT GROUP_CONCAT(u_ayu.nombre SEPARATOR ' / ') 
            FROM transporte_tripulantes_detalle ttd
            INNER JOIN trabajadores u_ayu 
                ON ttd.usuario_id = u_ayu.id
            WHERE ttd.reparto_id = tc.reparto_id
        ) AS ayudantes,

        trp.orden_visita,
        trp.descripcion_punto AS direccion_entrega,
        trp.estado_punto AS estatus_parada,
        trp.latitud, 
        trp.longitud,

        v.id AS numeroVenta,
        v.folio AS folio_venta,

        c.nombre_comercial AS cliente,
        c.telefono AS tel_cliente,

        p.nombre AS nombreProducto,
        p.factor_conversion AS factor,
        p.unidad_reporte AS unidadReporte,
        vd.equivalencia AS equi,
        vd.nombre_odma AS nombreEqui,

        m.cantidad AS totalCantidad,
        p.unidad_medida AS unidadMedida

    FROM transporte_consolidacion tc

    INNER JOIN transporte_repartos_maestro trm 
        ON tc.reparto_id = trm.id

    INNER JOIN transporte_vehiculos tv 
        ON tc.vehiculo_id = tv.id

    INNER JOIN transporte_rutas_puntos trp 
        ON trm.id = trp.reparto_id 

    INNER JOIN movimientos m 
        ON trm.entrega_venta_id = m.id

    INNER JOIN productos p 
        ON m.producto_id = p.id
       
    LEFT JOIN ventas v 
        ON m.referencia_id = v.id

    -- Subconsulta limpia para separar odma y detalle_venta
    LEFT JOIN (
        SELECT 
            dv.venta_id,
            dv.producto_id,
            odma.equivalencia,
            odma.nombre AS nombre_odma
        FROM detalle_venta dv
        INNER JOIN opciones_de_medida_adicional odma 
            ON odma.id = dv.unidadMedida
    ) vd ON vd.venta_id = v.id AND vd.producto_id = p.id

    LEFT JOIN clientes c 
        ON v.id_cliente = c.id

    LEFT JOIN trabajadores u_chofer 
        ON trm.usuario_encargado_id = u_chofer.id
   
    JOIN entregas_venta en 
        ON en.venta_id = v.id

    WHERE en.id = ?
    AND tc.entrega_id = ?

    GROUP BY 
        tc.viaje_folio,
        trp.descripcion_punto,
        p.id

) t

ORDER BY t.fecha_viaje DESC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            die($this->db->error);
        }

        $stmt->bind_param("ii", $entrega_id, $entrega_id);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $data = [];

        while ($row = $resultado->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
    public function quitarEntregaDeRuta($entrega_venta_id, $movimiento_id = 0, $id_usuario = 0)
    {
        try {
            // Iniciamos la transacción para asegurar que si algo falla, no se borre nada a medias
            $this->db->begin_transaction();

            // ==========================================
            // PARTE 1. LOGÍSTICA DE TRANSPORTE Y REPARTOS
            // ==========================================
            $motivo = "CANCELACIÓN DE RUTA";
            // 1. Buscamos el reparto_id en el maestro de transporte
            $sqlTrans = "SELECT id FROM transporte_repartos_maestro WHERE entrega_venta_id = ? LIMIT 1";
            $stmtTrans = $this->db->prepare($sqlTrans);
            $stmtTrans->bind_param("i", $entrega_venta_id);
            $stmtTrans->execute();
            $resTrans = $stmtTrans->get_result()->fetch_assoc();
            $stmtTrans->close();
            $this->cancelarDespachoFisico($movimiento_id);

            if ($resTrans) {
                $rid = $resTrans['id'];

                // Limpieza física de las tablas de logística asociadas al reparto
                $this->db->query("DELETE FROM transporte_rutas_puntos WHERE reparto_id = $rid");
                $this->db->query("DELETE FROM transporte_tripulantes_detalle WHERE reparto_id = $rid");
                $this->db->query("DELETE FROM transporte_consolidacion WHERE reparto_id = $rid");

                // Borramos el maestro de reparto de transporte
                $this->db->query("DELETE FROM transporte_repartos_maestro WHERE id = $rid");
            }


            // ==========================================
            // PARTE 2. CONTROL DE ENTREGAS, KARDEX E INVENTARIO
            // ==========================================

            // 2. Extraemos los datos del movimiento origen (Cambié m.id a dinámico si lo requieres, o lo dejas en 1200)
            $sqlMov = "SELECT 
                        m.producto_id AS p_id, 
                        m.cantidad AS cantidad_entregada, 
                        m.referencia_id AS id_venta, 
                        m.almacen_origen_id AS id_almacen,
                        m.entrega_id AS entrega_id
                   FROM movimientos m 
                   WHERE m.id = ?";

            $stmtMovData = $this->db->prepare($sqlMov);
            $stmtMovData->bind_param("i", $movimiento_id);
            $stmtMovData->execute();
            $movimientoData = $stmtMovData->get_result()->fetch_assoc();
            $stmtMovData->close();

            if ($movimientoData) {
                $p_id = $movimientoData['p_id'];
                $cant_entregada = $movimientoData['cantidad_entregada'];
                $id_venta = $movimientoData['id_venta'];
                $id_almacen = $movimientoData['id_almacen'];
                $id_entrega = $movimientoData['entrega_id'];

                // Evaluamos el detalle de la entrega vinculada para saber si eliminamos cabecera o sólo el ítem
                $sqlCant = "SELECT ie.id, ie.detalle_venta_id FROM detalle_entrega ie WHERE ie.entrega_id = ?";
                $stmtCant = $this->db->prepare($sqlCant);
                $stmtCant->bind_param("i", $id_entrega);
                $stmtCant->execute();
                $resentrega = $stmtCant->get_result();
                $detalleData = $resentrega->fetch_assoc();

                if ($detalleData) {
                    $detalle_venta_id = $detalleData['detalle_venta_id'];

                    // ACTUALIZACIÓN EN DETALLE_VENTA: Restamos la cantidad del movimiento cancelado
                    // (Ajusta el nombre de la columna 'cantidad_entregada' si en tu tabla se llama distinto)
                    $sqlUpdVenta = "UPDATE detalle_venta 
                                SET cantidad_entregada = cantidad_entregada - ? 
                                WHERE id = ?";
                    $stmtUpdVenta = $this->db->prepare($sqlUpdVenta);
                    $stmtUpdVenta->bind_param("di", $cant_entregada, $detalle_venta_id);
                    $stmtUpdVenta->execute();
                    $stmtUpdVenta->close();
                    $sqlUpdRealVenta = "UPDATE ventas 
                                SET estado_entrega = 'parcial' 
                                WHERE id = ?";
                    $stmtUpdRealVenta = $this->db->prepare($sqlUpdRealVenta);
                    $stmtUpdRealVenta->bind_param("i", $id_venta);
                    $stmtUpdRealVenta->execute();
                    $stmtUpdRealVenta->close();
                }
                if ($resentrega->num_rows <= 1) {
                    // Si sólo queda este registro o ninguno, eliminamos tanto cabecera como el detalle
                    if ($id_entrega) {
                        $stmtDelCab = $this->db->prepare("DELETE FROM entregas_venta WHERE id = ?");
                        $stmtDelCab->bind_param("i", $id_entrega);
                        $stmtDelCab->execute();
                        $stmtDelCab->close();
                    }
                    if ($detalleData) {
                        $stmtDelDet = $this->db->prepare("DELETE FROM detalle_entrega WHERE id = ?");
                        $stmtDelDet->bind_param("i", $detalleData['id']);
                        $stmtDelDet->execute();
                        $stmtDelDet->close();
                    }
                } else {
                    // Si la entrega contiene más artículos, únicamente removemos el renglón correspondiente
                    if ($detalleData) {
                        $stmtDelDet = $this->db->prepare("DELETE FROM detalle_entrega WHERE id = ?");
                        $stmtDelDet->bind_param("i", $detalleData['id']);
                        $stmtDelDet->execute();
                        $stmtDelDet->close();
                    }
                }
                $stmtCant->close();

                // 3. Reingreso físico de las existencias recuperadas al Inventario por Almacén
                $stmtInv = $this->db->prepare("UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?");
                $stmtInv->bind_param("dii", $cant_entregada, $p_id, $id_almacen);
                $stmtInv->execute();
                $stmtInv->close();

                // 4. Registro de reversión en el Kardex de Movimientos
                $mov_obs = "REINGRESO POR CANCELACIÓN - FOLIO: $id_venta. MOTIVO: $motivo";
                $stmtKardex = $this->db->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                           VALUES (?, 'ENTRADA', ?, ?, ?, ?, ?)");
                $stmtKardex->bind_param("idiiss", $p_id, $cant_entregada, $id_almacen, $id_usuario, $id_venta, $mov_obs);
                $stmtKardex->execute();
                $stmtKardex->close();
            }

            // Si todos los bloques de código SQL corrieron con éxito, guardamos definitivamente
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // En caso de fallas de red, FK o bloqueos, hacemos Rollback inmediato para proteger los almacenes
            if ($this->db->connect_errno == 0 && $this->db->ping()) {
                $this->db->rollback();
            }
            throw $e;
        }
    }
    public function guardarCambiosViaje($datos)
    {
        try {
            $this->db->begin_transaction();

            $folio = $datos['viaje_folio'];
            $chofer_id = intval($datos['chofer_id']);
            $usuario = intval($datos['usuario']);
            $vehiculo_id = intval($datos['vehiculo_id']);
            $tripulantes = intval($datos['tripulantes']) ?? 0;
            $destinos = isset($datos['destinos']) ? $datos['destinos'] : [];

            // 1. Mapear qué entregas existen en este folio de viaje
            $sqlActuales = "SELECT trm.entrega_venta_id, trm.id as reparto_id 
                        FROM transporte_consolidacion tc
                        JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                        WHERE tc.viaje_folio = ?";
            $stmtA = $this->db->prepare($sqlActuales);
            $stmtA->bind_param("s", $folio);
            $stmtA->execute();
            $resA = $stmtA->get_result();

            $mapeo_bd = [];
            while ($row = $resA->fetch_assoc()) {
                $mapeo_bd[$row['entrega_venta_id']] = $row['reparto_id'];
            }

            // 2. Sincronización: Quitar los que ya no vienen en el JSON
            foreach ($mapeo_bd as $mov_id_bd => $reparto_id) {
                if (!isset($destinos[$mov_id_bd])) {
                    $this->quitarEntregaDeRuta($mov_id_bd, $mov_id_bd, $usuario);
                }
            }

            // 3. Actualizar los que permanecen en la ruta
            $ids_vivos = [];
            foreach ($destinos as $mov_id => $dir) {
                if (isset($mapeo_bd[$mov_id])) {
                    $ids_vivos[] = $mapeo_bd[$mov_id];
                }
            }

            if (!empty($ids_vivos)) {
                $in_repartos = implode(',', array_map('intval', $ids_vivos));

                // A. Actualizar Maestro (Chofer y Vehículo)
                // Usamos los campos correctos: usuario_encargado_id y vehiculo_id
                $sqlM = "UPDATE transporte_repartos_maestro 
                     SET usuario_encargado_id = ?, vehiculo_id = ? 
                     WHERE id IN ($in_repartos)";
                $stmtM = $this->db->prepare($sqlM);
                $stmtM->bind_param("ii", $chofer_id, $vehiculo_id);
                $stmtM->execute();

                // B. Actualizar Ayudantes
                $this->db->query("DELETE FROM transporte_tripulantes_detalle WHERE reparto_id IN ($in_repartos)");

                $sqlT = "INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)";
                $stmtT = $this->db->prepare($sqlT);

                foreach ($ids_vivos as $rid) {


                    if ($tripulantes != $chofer_id && $tripulantes > 0)
                        $stmtT->bind_param("ii", $rid, $tripulantes);
                    $stmtT->execute();

                }

                // C. Actualizar Direcciones (Puntos de Ruta)
                $sqlP = "UPDATE transporte_rutas_puntos SET descripcion_punto = ? WHERE reparto_id = ?";
                $stmtP = $this->db->prepare($sqlP);
                foreach ($destinos as $mov_id => $dir) {
                    if (isset($mapeo_bd[$mov_id])) {
                        $dir_txt = substr($dir, 0, 255);
                        $stmtP->bind_param("si", $dir_txt, $mapeo_bd[$mov_id]);
                        $stmtP->execute();
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function cancelarDespachoFisico($idMovimiento)
    {
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
    public function procesarDespachoFisicoMasivo($idsMovimientos)
    {
        if (empty($idsMovimientos)) {
            return ['success' => false, 'message' => 'No se proporcionaron IDs para procesar.'];
        }

        $this->db->begin_transaction();

        try {
            $id_usuario = $_SESSION['usuario_id'] ?? 0;
            if ($id_usuario <= 0)
                throw new Exception("Error: Sesión de usuario no válida.");

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

                if (!$mov)
                    throw new Exception("Movimiento ID $idMovimiento no encontrado.");

                $prod_id = $mov['producto_id'];
                $alm_id = $mov['almacen_origen_id'];
                $cantidad_restante = floatval($mov['cantidad']);
                $entrega_id = intval($mov['entrega_id'] ?? 0);
                $det_venta_id = intval($mov['det_venta_id'] ?? 0);
                $precio_pactado = floatval($mov['precio_pactado'] ?? 0);

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
                    $lote_id = $lote['id'];
                    $stock_lote = floatval($lote['cantidad_actual']);
                    $costo_historico = $lote['precio_compra_unitario'];

                    $a_tomar = min($cantidad_restante, $stock_lote);
                    $nuevo_stock_lote = $stock_lote - $a_tomar;
                    $nuevo_estado = ($nuevo_stock_lote <= 0) ? 'agotado' : 'activo';

                    // Actualizar el lote
                    $this->db->query("UPDATE lotes_stock 
                                 SET cantidad_actual = $nuevo_stock_lote, estado_lote = '$nuevo_estado' 
                                 WHERE id = $lote_id");

                    // Registrar la salida específica del lote
                    $sqlSalida = "INSERT INTO lotes_movimientos_salida 
                              (lote_id, entrega_venta_id, detalle_venta_id, cantidad_salida, costo_compra_historico, precio_venta_pactado) 
                              VALUES ($lote_id, $entrega_id, $det_venta_id, $a_tomar, $costo_historico, $precio_pactado)";

                    if (!$this->db->query($sqlSalida))
                        throw new Exception("Error al insertar salida de lote.");

                    $cantidad_restante -= $a_tomar;
                }

                // 4. Insertar en el "Puente" para marcar que el bodeguero ya lo entregó físicamente
                // Esto es lo que hace que desaparezca de la lista de pendientes
                $sqlPuente = "INSERT INTO registro_salida_lotes (movimiento_id, usuario_patio_id, usuario_despacho_id) 
                          VALUES ($idMovimiento, $id_usuario, $id_usuario)";

                if (!$this->db->query($sqlPuente))
                    throw new Exception("Error en registro físico: " . $this->db->error);
            }

            $this->db->commit();
            return ['success' => true, 'message' => count($idsMovimientos) . ' productos despachados correctamente.'];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => "Error: " . $e->getMessage()];
        }
    }
    public function procesarDespachoFisicoMasivoConlotes($idsMovimientos, $lotes)
    {
        if (empty($idsMovimientos)) {
            return ['success' => false, 'message' => 'No se proporcionaron IDs para procesar.'];
        }

        $this->db->begin_transaction();

        try {
            $id_usuario = $_SESSION['usuario_id'] ?? 0;
            if ($id_usuario <= 0) {
                throw new Exception("Error: Sesión de usuario no válida.");
            }

            foreach ($idsMovimientos as $index => $idMovimiento) {
                $idMovimiento = intval($idMovimiento);

                // 1. Obtener movimiento
                $sqlMov = "SELECT m.id, m.producto_id, m.almacen_origen_id, m.cantidad,
                              dv.id as det_venta_id, dv.precio_unitario as precio_pactado,
                              ev.id as entrega_id
                       FROM movimientos m
                       LEFT JOIN detalle_venta dv 
                            ON m.referencia_id = dv.venta_id 
                           AND m.producto_id = dv.producto_id
                       LEFT JOIN entregas_venta ev 
                            ON dv.venta_id = ev.venta_id
                       WHERE m.id = $idMovimiento 
                       LIMIT 1";

                $resMov = $this->db->query($sqlMov);
                $mov = $resMov->fetch_assoc();

                if (!$mov) {
                    throw new Exception("Movimiento ID $idMovimiento no encontrado.");
                }

                $prod_id = intval($mov['producto_id']);
                $alm_id = intval($mov['almacen_origen_id']);
                $cantidad_restante = floatval($mov['cantidad']);
                $entrega_id = intval($mov['entrega_id'] ?? 0);
                $det_venta_id = intval($mov['det_venta_id'] ?? 0);
                $precio_pactado = floatval($mov['precio_pactado'] ?? 0);

                // ===================================================
                // SI EXISTE LOTE ESPECÍFICO -> USAR ESE
                // SI NO -> FIFO NORMAL
                // ===================================================
                $loteSeleccionado = isset($lotes[$index]) ? intval($lotes[$index]) : 0;

                if ($loteSeleccionado > 0) {
                    $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario
                             FROM lotes_stock
                             WHERE id = $loteSeleccionado
                               AND producto_id = $prod_id
                               AND almacen_id = $alm_id
                               AND cantidad_actual > 0";
                } else {
                    $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario
                             FROM lotes_stock
                             WHERE producto_id = $prod_id
                               AND almacen_id = $alm_id
                               AND cantidad_actual > 0
                               AND estado_lote = 'activo'
                             ORDER BY fecha_ingreso ASC, id ASC";
                }

                $resLotes = $this->db->query($sqlLotes);

                if ($resLotes->num_rows == 0 && $cantidad_restante > 0) {
                    throw new Exception("Sin stock en lotes para producto ID: $prod_id");
                }

                // 3. Descontar
                while ($cantidad_restante > 0 && $lote = $resLotes->fetch_assoc()) {

                    $lote_id = intval($lote['id']);
                    $stock_lote = floatval($lote['cantidad_actual']);
                    $costo_historico = floatval($lote['precio_compra_unitario']);

                    $a_tomar = min($cantidad_restante, $stock_lote);
                    $nuevo_stock_lote = $stock_lote - $a_tomar;
                    $nuevo_estado = ($nuevo_stock_lote <= 0) ? 'agotado' : 'activo';

                    $this->db->query("
                    UPDATE lotes_stock
                    SET cantidad_actual = $nuevo_stock_lote,
                        estado_lote = '$nuevo_estado'
                    WHERE id = $lote_id
                ");

                    $sqlSalida = "INSERT INTO lotes_movimientos_salida
                              (lote_id, entrega_venta_id, detalle_venta_id,
                               cantidad_salida, costo_compra_historico, precio_venta_pactado)
                              VALUES
                              ($lote_id, $entrega_id, $det_venta_id,
                               $a_tomar, $costo_historico, $precio_pactado)";

                    if (!$this->db->query($sqlSalida)) {
                        throw new Exception("Error al insertar salida de lote.");
                    }

                    $cantidad_restante -= $a_tomar;
                }

                if ($cantidad_restante > 0) {
                    throw new Exception("Stock insuficiente para movimiento $idMovimiento");
                }

                // 4. Registrar salida física
                $sqlPuente = "INSERT INTO registro_salida_lotes
                         (movimiento_id, usuario_patio_id, usuario_despacho_id)
                         VALUES
                         ($idMovimiento, $id_usuario, $id_usuario)";

                if (!$this->db->query($sqlPuente)) {
                    throw new Exception("Error en registro físico: " . $this->db->error);
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => count($idsMovimientos) . ' productos despachados correctamente.'
            ];

        } catch (Exception $e) {
            $this->db->rollback();

            return [
                'success' => false,
                'message' => "Error: " . $e->getMessage()
            ];
        }
    }
    public function listarIdsPendientesPorVenta($venta_id)
    {
        $venta_id = intval($venta_id);

        // Solo pedimos m.id para optimizar la consulta
        $sql = "SELECT m.id 
            FROM movimientos m
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            WHERE m.referencia_id = $venta_id 
              AND m.tipo = 'salida'
             
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

    public function listarProductos($venta_id)
    {
        $venta_id = intval($venta_id);

        $sqlP = "
    SELECT 
        dv.*,
        dv.id AS dvid,
        odma.*,
        p.id as producto_id,
        p.nombre AS producto,
        p.sku,
        COALESCE(i.stock,0) AS disponible,

        p.unidad_medida,
        p.unidad_reporte,
        p.factor_conversion,

        COALESCE(ent.total_entregado,0) AS entregado,

        (dv.cantidad - COALESCE(ent.total_entregado,0)) AS pendiente

    FROM detalle_venta dv

    INNER JOIN ventas v
        ON v.id = dv.venta_id

    JOIN opciones_de_medida_adicional odma
        ON odma.id = dv.unidadMedida

    INNER JOIN productos p
        ON p.id = dv.producto_id

    LEFT JOIN inventario i
        ON i.producto_id = dv.producto_id
        AND i.almacen_id = v.almacen_id

    LEFT JOIN (
        SELECT
            detalle_venta_id,
            SUM(cantidad) AS total_entregado
        FROM detalle_entrega
        GROUP BY detalle_venta_id
    ) ent
        ON ent.detalle_venta_id = dv.id

    WHERE dv.venta_id = $venta_id
    ";

        $res = $this->db->query($sqlP);

        $productos = [];

        while ($row = $res->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }
    public function contarEntregasActivasPorVenta($venta_id)
    {
        // Forzamos entero para seguridad extra
        $venta_id = intval($venta_id);

        $sql = "SELECT COUNT(DISTINCT m.id) AS total_entregas_activas
            FROM movimientos m
            INNER JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            WHERE m.referencia_id = ? 
              AND m.tipo = 'salida'
              -- Filtramos solo los que tienen transporte real y NO están cancelados
              AND trm.id IS NOT NULL 
              AND trm.estado_reparto != 'cancelado'";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $venta_id);
            $stmt->execute();
            $resultado = $stmt->get_result()->fetch_assoc();

            return intval($resultado['total_entregas_activas'] ?? 0);

        } catch (Exception $e) {
            error_log("CF_SYSTEM_LOG: Error al contar entregas activas: " . $e->getMessage());
            return 0;
        }
    }
    public function simularDespachoLotesMasivo($idsMovimientos)
    {
        try {
            if (empty($idsMovimientos))
                throw new Exception("No hay IDs para procesar.");

            // Limpiamos los IDs para evitar inyecciones
            $idsClean = array_map('intval', $idsMovimientos);
            $idsString = implode(',', $idsClean);

            // 1. Obtenemos todos los movimientos de una sola vez
            $sqlMovs = "SELECT m.id, m.producto_id, m.almacen_origen_id, m.cantidad, p.id as producto_id,
                           p.nombre as prod_nombre, p.factor_conversion, p.unidad_reporte 
                    FROM movimientos m 
                    INNER JOIN productos p ON m.producto_id = p.id 
                    WHERE m.id IN ($idsString)";

            $resMovs = $this->db->query($sqlMovs);
            $resultados = [];

            while ($mov = $resMovs->fetch_assoc()) {
                $movId = $mov['id'];
                $prodId = $mov['producto_id'];
                $almId = $mov['almacen_origen_id'];
                $restante = floatval($mov['cantidad']);
                $factor = floatval($mov['factor_conversion'] ?: 1);

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
                        'lote_id' => $lote['id'],
                        'codigo' => $lote['codigo_lote'],
                        'fecha_entrada' => date('d/m/Y', strtotime($lote['fecha_ingreso'])),
                        'cantidad_en_lote' => $cantidadEnLote,
                        'cantidad_a_extraer' => $tomar,
                        'saldo_final' => $cantidadEnLote - $tomar
                    ];
                    $restante -= $tomar;
                }

                // 3. Agrupamos el resultado por ID de movimiento
                $resultados[] = [
                    'movimiento_id' => $movId,
                    'producto_id' => $mov['producto_id'],
                    'almacen_id' => $mov['almacen_origen_id'],
                    'producto' => $mov['prod_nombre'],
                    'total_solicitado' => $mov['cantidad'],
                    'unidad_reporte' => $mov['unidad_reporte'],
                    'factor_conversion' => $factor,
                    'lotes' => $lotesParaEsteMov,
                    'pendiente' => $restante // Si es > 0, falta stock
                ];
            }

            return [
                'success' => true,
                'data' => $resultados
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function DespachoLotesMasivo($prodId, $almId)
    {
        try {

            $prodId = intval($prodId);
            $almId = intval($almId);

            $sqlLotes = "
            SELECT id, codigo_lote, cantidad_actual, fecha_ingreso,producto_id
            FROM lotes_stock 
            WHERE producto_id = $prodId
              AND almacen_id = $almId
              AND cantidad_actual > 0
              AND estado_lote = 'activo'
            ORDER BY fecha_ingreso ASC
        ";

            $resLotes = $this->db->query($sqlLotes);

            $lotes = [];

            while ($row = $resLotes->fetch_assoc()) {
                $lotes[] = $row;
            }

            return [
                'success' => true,
                'data' => $lotes
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    public function listarDespachosPendientesPorVenta($venta_id)
    {
        $venta_id = intval($venta_id);

        $sql = "SELECT m.id 
FROM movimientos m
-- Unimos con patio (rsl) para saber que YA salieron de bodega
INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
-- Unimos con transporte (trm) para filtrar los que ya tienen ruta
LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
WHERE m.referencia_id = $venta_id
  AND m.tipo = 'salida'
  -- CLAVE: Que NO tenga registro en transporte (trm.id es NULL)
  AND trm.id IS NULL
ORDER BY m.id ASC;";

        $resultado = $this->db->query($sql);
        $pendientes = [];

        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                $pendientes[] = $row;
            }
        }

        return $pendientes;
    }
    public function getViajesLogistica($trabajador_id = null)
    {
        // Si hay ID, filtramos. Si no (Admin), 1=1 para traer todo.
        $where = ($trabajador_id !== null)
            ? "WHERE (rm.usuario_encargado_id = ? OR td.usuario_id = ?)"
            : "WHERE 1=1";

        $sql = "SELECT 
                rm.id, 
                tc.viaje_folio, 
                rm.fecha_programada, 
                rm.estado_reparto,
                v.nombre AS vehiculo,
                v.placas,
                CASE 
                    WHEN rm.usuario_encargado_id = ? THEN 'Chofer'
                    WHEN td.usuario_id = ? THEN 'Ayudante'
                    ELSE 'Supervisor'
                END as rol_en_viaje
            FROM transporte_repartos_maestro rm
            INNER JOIN transporte_vehiculos v ON rm.vehiculo_id = v.id
            LEFT JOIN transporte_consolidacion tc ON rm.id = tc.reparto_id
            LEFT JOIN transporte_tripulantes_detalle td ON rm.id = td.reparto_id
            $where
            GROUP BY rm.id
            ORDER BY rm.fecha_programada DESC, rm.id DESC";

        $stmt = $this->db->prepare($sql);

        // Pasamos los parámetros para el CASE y el WHERE
        if ($trabajador_id !== null) {
            $stmt->bind_param("iiii", $trabajador_id, $trabajador_id, $trabajador_id, $trabajador_id);
        } else {
            // Para Admin, solo pasamos ceros o valores nulos al CASE
            $zero = 0;
            $stmt->bind_param("ii", $zero, $zero);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getCargaPendienteChofer($trabajador_id = null, $reparto_id_especifico = null)
    {
        // Filtro dinámico: Por trabajador (Chofer/Ayudante) o por ID de viaje directo (Admin)
        $where = "WHERE tc.estatus_consolidado = 'abierto'";

        if ($reparto_id_especifico) {
            $where .= " AND rm.id = " . intval($reparto_id_especifico);
        } elseif ($trabajador_id) {
            $where .= " AND (rm.usuario_encargado_id = $trabajador_id OR td.usuario_id = $trabajador_id)";
        }

        $sql = "SELECT 
                tc.viaje_folio,
                rm.id AS reparto_id,
                v.id AS venta_id,
                v.folio AS folio_venta,
                m.id AS movimiento_id,
                m.cantidad,
                p.nombre AS producto_nombre,
                p.unidad_medida,
                c.nombre_comercial AS cliente,
                rp.descripcion_punto AS destino,
                rp.estado_punto
            FROM transporte_consolidacion tc
            INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
            INNER JOIN productos p ON m.producto_id = p.id
            INNER JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN transporte_rutas_puntos rp ON trm.id = rp.reparto_id
            LEFT JOIN transporte_tripulantes_detalle td ON trm.id = td.reparto_id
            $where
            AND rp.estado_punto = 'pendiente'
            GROUP BY m.id
            ORDER BY rp.orden_visita ASC";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    public function registrarEntregaMovimiento($datos)
    {
        try {

            $this->db->begin_transaction();

            $id_mov = intval($datos['id_movimiento']);

            $id_ven = intval($datos['id_venta']);
            $id_tra = intval($datos['trabajador_id'] ?? 0);
            $id_veh = intval($datos['vehiculo_id']);

            $foto_ent = !empty($datos['fotografia_entrega'])
                ? $datos['fotografia_entrega']
                : null;

            $foto_not = !empty($datos['fotografia_nota'])
                ? $datos['fotografia_nota']
                : null;

            $estatus = $datos['estatus_entrega'];
            $coment = $datos['comentario'] ?? '';
            $folio = $datos['folio'] ?? '';

            // ==========================================
            // VERIFICAR SI YA EXISTE
            // ==========================================

            $sqlR = "
            SELECT id
            FROM confirmacion_reparto_viaje
            WHERE id_venta = ?
            AND reparto_folio = ?
            LIMIT 1
        ";

            $stmtR = $this->db->prepare($sqlR);
            $stmtR->bind_param("is", $id_ven, $folio);
            $stmtR->execute();

            $registro = $stmtR->get_result()->fetch_assoc();

            // ==========================================
            // UPDATE
            // ==========================================

            if ($registro) {

                $sqlUP = "
                UPDATE confirmacion_reparto_viaje
                SET
                    comentario = ?,
                    estatus = ?,
                    hora = CURTIME()
            ";

                $params = [$coment, $estatus];
                $types = "ss";

                if ($foto_ent !== null) {
                    $sqlUP .= ", fotografia_entrega = ?";
                    $params[] = $foto_ent;
                    $types .= "s";
                }

                if ($foto_not !== null) {
                    $sqlUP .= ", fotografia_nota = ?";
                    $params[] = $foto_not;
                    $types .= "s";
                }

                $sqlUP .= "
                WHERE id_venta = ?
                AND reparto_folio = ?
            ";

                $params[] = $id_ven;
                $params[] = $folio;
                $types .= "is";

                $stmtUP = $this->db->prepare($sqlUP);

                if (!$stmtUP) {
                    throw new Exception($this->db->error);
                }

                $stmtUP->bind_param($types, ...$params);

                if (!$stmtUP->execute()) {
                    throw new Exception($stmtUP->error);
                }

            } else {
                $result_entrega = $this->db->query("SELECT entrega_id FROM movimientos m WHERE m.id = $id_mov");
                $row_entrega = $result_entrega->fetch_assoc();
                // Si encuentra el registro toma el ID, si no, asigna 0 o null según permita tu base de datos
                $entrega_id = intval($datos['id_movimiento']);



                // ==========================================
                // INSERT
                // ==========================================

                $sqlEv = "
                INSERT INTO confirmacion_reparto_viaje (
                    id_movimiento,
                    id_venta,
                    reparto_folio,
                    trabajador_id,
                    vehiculo_id,
                    fecha,
                    hora,
                    fotografia_entrega,
                    fotografia_nota,
                    estatus,
                    comentario,
                    entrega_id
                )
                VALUES (
                    ?, ?, ?, ?, ?,
                    CURDATE(),
                    CURTIME(),
                    ?, ?, ?, ?,?
                )
            ";

                $stmtEv = $this->db->prepare($sqlEv);

                if (!$stmtEv) {
                    throw new Exception($this->db->error);
                }

                $stmtEv->bind_param(
                    "iisiissssi",
                    $id_mov,
                    $id_ven,
                    $folio,
                    $id_tra,
                    $id_veh,
                    $foto_ent,
                    $foto_not,
                    $estatus,
                    $coment,
                    $entrega_id
                );

                if (!$stmtEv->execute()) {
                    throw new Exception("Error al guardar evidencia: " . $stmtEv->error);
                }
            }

            // ==========================================
            // ACTUALIZAR PUNTO DE RUTA
            // ==========================================

            $sqlPunto = "
            UPDATE transporte_rutas_puntos
            SET estado_punto = 'visitado',
                llegada_real = NOW()
            WHERE id = ?
        ";

            $stmtP = $this->db->prepare($sqlPunto);
            $stmtP->bind_param("i", $id_mov);

            if (!$stmtP->execute()) {
                throw new Exception($stmtP->error);
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            $this->db->rollback();

            error_log(
                "Error registrarEntregaMovimiento: " .
                $e->getMessage()
            );

            throw $e;
        }
    }
    public function getMonitorEntregasRuta(
        $almacen_id = 0,
        $fecha_inicio = null,
        $fecha_fin = null,
        $inicio = 0,
        $limite = 25
    ) {

        $where = [];
        $params = [];
        $types = '';

        // 🔹 BASE
        $where[] = "m.tipo = 'salida'";
        $where[] = "tc.viaje_folio IS NOT NULL";
        $where[] = "trm.estado_reparto != 'cancelado'";

        // 🔹 ALMACÉN
        if (!empty($almacen_id) && $almacen_id > 0) {
            $where[] = "m.almacen_origen_id = ?";
            $params[] = $almacen_id;
            $types .= 'i';
        }

        // 🔥 FECHAS (LÓGICA NUEVA)
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {

            // 👉 RANGO COMPLETO
            $where[] = "DATE(m.fecha) BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types .= 'ss';

        } else {

            // 👉 SI NO VIENEN O VIENE SOLO UNA → HOY
            $where[] = "DATE(m.fecha) = CURDATE()";
        }

        // 🔹 ARMAR WHERE
        $where_sql = "WHERE " . implode(" AND ", $where);

        // 🔥 SQL
        $sql = "SELECT 
                m.id AS movimiento_id, 
                trm.id AS reparto_id,
                trm.estado_reparto AS estado_reparto,
                tc.viaje_folio AS grupo_id,
                tc.entrega_id,
                tc.viaje_folio AS numero_ruta,
                'RUTA' AS tipo_salida,
                'VARIOS CLIENTES (RUTA)' AS cliente_display,
                'MATERIALES DIVERSOS' AS producto_nombre,
                SUM(m.cantidad) as total_bultos,
                COALESCE(t_chofer.nombre, 'POR ASIGNAR') AS responsable,
                DATE_FORMAT(MAX(IFNULL(rsl.fecha_despacho, m.fecha)), '%d/%m/%Y %H:%i') AS fecha_evento
            FROM movimientos m
            INNER JOIN transporte_repartos_maestro trm 
                ON m.id = trm.entrega_venta_id 
            INNER JOIN transporte_consolidacion tc 
                ON trm.id = tc.reparto_id
            LEFT JOIN trabajadores t_chofer 
                ON trm.usuario_encargado_id = t_chofer.id
            LEFT JOIN registro_salida_lotes rsl 
                ON m.id = rsl.movimiento_id
            $where_sql
            GROUP BY tc.viaje_folio
            ORDER BY 
                (CASE WHEN trm.estado_reparto = 'en_ruta' THEN 1 ELSE 2 END) ASC, 
                MAX(m.fecha) DESC
            LIMIT ?, ?";

        $stmt = $this->db->prepare($sql);

        // 🔥 LIMIT siempre al final
        $params[] = $inicio;
        $params[] = $limite;
        $types .= 'ii';

        $stmt->bind_param($types, ...$params);

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function contarTotalEntregasRuta($almacen_id = 0, $fecha_inicio = null, $fecha_fin = null)
    {

        $where = " WHERE m.tipo = 'salida' AND tc.viaje_folio IS NOT NULL ";
        $params = [];
        $types = '';

        // 🔹 FILTRO ALMACÉN
        if ($almacen_id > 0) {
            $where .= " AND m.almacen_origen_id = ? ";
            $params[] = $almacen_id;
            $types .= 'i';
        }

        // 🔹 FILTRO FECHAS
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $where .= " AND DATE(m.fecha) BETWEEN ? AND ? ";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types .= 'ss';

        } elseif (!empty($fecha_inicio)) {
            $where .= " AND DATE(m.fecha) >= ? ";
            $params[] = $fecha_inicio;
            $types .= 's';

        } elseif (!empty($fecha_fin)) {
            $where .= " AND DATE(m.fecha) <= ? ";
            $params[] = $fecha_fin;
            $types .= 's';

        } else {
            // 🔥 SI NO HAY FECHAS → SOLO HOY
            $where .= " AND DATE(m.fecha) = CURDATE() ";
        }

        // 🔹 QUERY
        $sql = "SELECT COUNT(DISTINCT tc.viaje_folio) as total 
            FROM movimientos m
            INNER JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id 
            INNER JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
            $where
            AND trm.estado_reparto != 'cancelado'";

        $stmt = $this->db->prepare($sql);

        // 🔹 BIND DINÁMICO
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return intval($result['total'] ?? 0);

    }
    /**
     * Obtiene la información detallada de una entrega específica para el reparto
     *
     * @param int $entrega_id ID de la entrega a consultar
     * @param string $base_path Ruta base para la construcción de URLs de imágenes (opcional)
     * @return array|object|null Devuelve el registro resultante o null si no existe
     *//**
     * Obtiene la información detallada de los viajes/repartos para una entrega específica
     *
     * @param int $entrega_id ID de la entrega a consultar
     * @param string $base_path Ruta base para la construcción de URLs de imágenes (opcional)
     * @return array Devuelve un listado (array de arrays asociativos) o un array vacío si no hay registros
     */
    public function obtenerViajesLogisticaParaEntrega($folioViaje, $base_path = '')
    {
        $sql = "SELECT
                ROW_NUMBER() OVER (ORDER BY en.id) AS num_registro,
                MAX(v.folio) AS folio_venta,
                en.id AS entrega_id,
                en.venta_id,
                en.usuario_id,
                en.fecha,

                MAX(tc.id) AS folio,
                IFNULL(MAX(tc.viaje_folio), 'Sin Viaje Asignado') AS viaje_folio,

                -- Evidencias e imágenes
                MAX(crv.id) AS id_evidencia,
                MAX(crv.estatus) AS estatus_evidencia,
                MAX(crv.comentario) AS comentario_evidencia,
                MAX(IF(crv.id IS NOT NULL, 1, 0)) AS ya_entregado,

                MAX(IF(crv.fotografia_entrega IS NOT NULL AND crv.fotografia_entrega != '', 
                    CONCAT(crv.fotografia_entrega), NULL)) AS foto_registrada,

                MAX(IF(crv.fotografia_nota IS NOT NULL AND crv.fotografia_nota != '', 
                    CONCAT(crv.fotografia_nota), NULL)) AS nota_registrada,

                MAX(trp.descripcion_punto) AS direccion_entrega,
                MAX(c.nombre_comercial) AS cliente,
                MAX(trm.vehiculo_id) AS vehiculo_id,

                GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ', ') AS productos,

                GROUP_CONCAT(
                    DISTINCT CONCAT(m.cantidad, ' ', p.unidad_medida)
                    ORDER BY p.nombre
                    SEPARATOR ', '
                ) AS cantidades_detalladas,

                SUM(m.cantidad) AS total_piezas_venta

            FROM entregas_venta en

            INNER JOIN movimientos m
                ON m.entrega_id = en.id

            INNER JOIN transporte_repartos_maestro trm
                ON trm.entrega_venta_id = m.id
             
            LEFT JOIN transporte_consolidacion tc
                ON tc.reparto_id = trm.id

            LEFT JOIN confirmacion_reparto_viaje crv
                ON crv.entrega_id = en.id

            LEFT JOIN transporte_rutas_puntos trp
                ON trp.reparto_id = trm.id

            LEFT JOIN productos p
                ON p.id = m.producto_id

            LEFT JOIN ventas v
                ON v.id = en.venta_id

            LEFT JOIN clientes c
                ON c.id = v.id_cliente

            WHERE tc.viaje_folio = ?

            GROUP BY
                en.id,
                en.venta_id,
                en.usuario_id,
                en.fecha,
                trp.descripcion_punto";

        $stmt = $this->db->prepare($sql);

        // Corregido: Solo 1 parámetro tipo string ('s')
        $types = 's';
        $stmt->bind_param($types, $folioViaje);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function a($entrega_id = null)
    {
        try {
            // Ruta base para tus imágenes
            $base_path = "/myvet/";

            // 1. Definimos la base del SELECT (SIN el GROUP BY al final)
            $sql = "SELECT
    ROW_NUMBER() OVER (ORDER BY en.id) AS num_registro,
    MAX(v.folio) AS folio_venta,
    en.id AS entrega_id,
    en.venta_id,
    en.usuario_id,
    en.fecha,

    MAX(tc.id) AS folio,
    MAX(tc.viaje_folio) AS viaje_folio,

    MAX(trp.descripcion_punto) AS direccion_entrega,
    MAX(c.nombre_comercial) AS cliente,
    MAX(trm.vehiculo_id) AS vehiculo_id,

    GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ', ') AS productos,

    GROUP_CONCAT(
        DISTINCT CONCAT(m.cantidad,' ',p.unidad_medida)
        ORDER BY p.nombre
        SEPARATOR ', '
    ) AS cantidades_detalladas,

    SUM(m.cantidad) AS total_piezas_venta

FROM entregas_venta en

INNER JOIN movimientos m
    ON m.entrega_id = en.id

INNER JOIN transporte_repartos_maestro trm
    ON trm.entrega_venta_id = m.id

LEFT JOIN transporte_consolidacion tc
    ON tc.reparto_id = trm.id

LEFT JOIN transporte_rutas_puntos trp
    ON trp.reparto_id = trm.id

LEFT JOIN productos p
    ON p.id = m.producto_id

LEFT JOIN ventas v
    ON v.id = en.venta_id

LEFT JOIN clientes c
    ON c.id = v.id_cliente
";
            // Inicializamos los parámetros para los 3 '?' del SELECT
            $types = "iii";
            $params = [$entrega_id, $entrega_id, $entrega_id];

            // Si el filtro de búsqueda no está vacío, agregamos el WHERE antes del GROUP BY
            if (!empty($entrega_id)) {
                $sql .= " WHERE tc.entrega_id";
                $types .= "i";             // Agrega un tipo 'string' más
                $params[] = $entrega_id;  // Agrega el valor para el WHERE
            }

            // Cerramos con el agrupamiento obligatorio
            $sql .= " GROUP BY
    en.id,
    trp.descripcion_punto";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error en la preparación: " . $this->db->error);
            }

            // Desempaquetamos los parámetros dinámicamente con ...
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            return $res;
        } catch (Exception $e) {
            error_log("Error CF System: " . $e->getMessage());
            return [];
        }
    }
    public function evidenciaEntregaVenta($folio_viaje = null)
    {
        try {
            // Ruta base para tus imágenes
            $base_path = "/myvet/";

            // 1. Definimos la base del SELECT con agrupaciones estrictas
            $sql = "SELECT 
                    v.id AS id_venta,
                    MAX(v.folio) AS folio_venta,
                    MAX(c.nombre_comercial) AS cliente,
                    MAX(trm.vehiculo_id) AS vehiculo_id,
                    MAX(tc.viaje_folio) AS folio_viaje,
                     MAX(tc.entrega_id) AS entrega_id,

                    MAX(trp.descripcion_punto) AS direccion_entrega,
                    MAX(trp.estado_punto) AS estatus_parada,
                    MAX(crv.id) AS id_evidencia,
                    MAX(crv.estatus) AS estatus_evidencia,
                    MAX(crv.comentario) AS comentario_evidencia,
                     MAX(IF(crv.id IS NOT NULL AND crv.reparto_folio=? , 1, 0)) AS ya_entregado,
                    
                    -- Trae el último ID de movimiento de esta venta (evita desgloses)
                    MAX(trp.id) AS ids_movimientos_grupo,
                    
                    -- Evidencias fotográficas concatenando la variable PHP correctamente
                    MAX(IF(crv.fotografia_entrega IS NOT NULL AND crv.reparto_folio=? AND crv.fotografia_entrega != '', CONCAT('" . $base_path . "', crv.fotografia_entrega), NULL)) AS foto_registrada,
                    MAX(IF(crv.fotografia_nota IS NOT NULL  AND crv.reparto_folio=? AND crv.fotografia_nota != '', CONCAT('" . $base_path . "', crv.fotografia_nota), NULL)) AS nota_registrada,
                    
                    -- 🔥 Agrupamos los productos de la misma venta en una sola celda
                    GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos,
                    GROUP_CONCAT(CONCAT(m.cantidad, ' ', p.unidad_medida) SEPARATOR ', ') AS cantidades_detalladas,
                    
                    -- Sumamos las cantidades de los productos que integran esta venta
                    SUM(m.cantidad) AS total_piezas_venta
                FROM transporte_consolidacion tc
                INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                INNER JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id 
                INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN ventas v ON m.referencia_id = v.id
                LEFT JOIN clientes c ON v.id_cliente = c.id
               LEFT JOIN confirmacion_reparto_viaje crv
    ON v.id = crv.id_venta
    AND crv.reparto_folio = tc.viaje_folio";

            // 2. El WHERE siempre debe ir ANTES del GROUP BY en SQL
            if (!empty($folio_viaje)) {
                $sql .= " WHERE tc.viaje_folio = ?";
            }

            // 3. Añadimos el agrupamiento por venta al final con espacio preventivo
            $sql .= " GROUP BY v.id";

            $stmt = $this->db->prepare($sql);

            if (!empty($folio_viaje)) {
                $stmt->bind_param("ssss", $folio_viaje, $folio_viaje, $folio_viaje, $folio_viaje);
            }

            $stmt->execute();
            $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            if (ob_get_level())
                ob_clean();
            return $res;

        } catch (Exception $e) {
            error_log("Error CF System: " . $e->getMessage());
            return [];
        }
    }
    public function evidenciaEntregaPorid($entrega_id = null)
    {
        try {
            $base_path = "/myvet/";

            $sql = "SELECT 
                    v.id AS id_venta,
                    MAX(v.folio) AS folio_venta,
                    MAX(c.nombre_comercial) AS cliente,
                    MAX(trm.vehiculo_id) AS vehiculo_id,
                    MAX(tc.viaje_folio) AS folio_viaje,
                    MAX(trp.descripcion_punto) AS direccion_entrega,
                    MAX(trp.estado_punto) AS estatus_parada,
                    MAX(crv.id) AS id_evidencia,
                    MAX(crv.estatus) AS estatus_evidencia,
                    MAX(crv.comentario) AS comentario_evidencia,
                    IF(MAX(crv.id) IS NOT NULL, 1, 0) AS ya_entregado,
                    
                    MAX(trp.id) AS ids_movimientos_grupo,
                    
                    MAX(IF(crv.fotografia_entrega IS NOT NULL AND crv.fotografia_entrega != '', CONCAT('" . $base_path . "', crv.fotografia_entrega), NULL)) AS foto_registrada,
                    MAX(IF(crv.fotografia_nota IS NOT NULL AND crv.fotografia_nota != '', CONCAT('" . $base_path . "', crv.fotografia_nota), NULL)) AS nota_registrada,
                    
                    GROUP_CONCAT(DISTINCT p.nombre SEPARATOR ', ') AS productos,
                    GROUP_CONCAT(CONCAT(m.cantidad, ' ', p.unidad_medida) SEPARATOR ', ') AS cantidades_detalladas,
                    SUM(m.cantidad) AS total_piezas_venta

                FROM transporte_consolidacion tc
                INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                INNER JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id 
                INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN ventas v ON m.referencia_id = v.id
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN confirmacion_reparto_viaje crv ON tc.entrega_id = crv.entrega_id";

            // 1. Filtrado en WHERE antes del GROUP BY
            if (!empty($entrega_id)) {
                $sql .= " WHERE tc.entrega_id = ?";
            }

            // 2. Agrupamiento
            $sql .= " GROUP BY v.id, tc.entrega_id";

            $stmt = $this->db->prepare($sql);

            // 3. Solo necesitamos enlazar 1 solo parámetro en el WHERE
            if (!empty($entrega_id)) {
                $stmt->bind_param("s", $entrega_id);
            }

            $stmt->execute();
            $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            if (ob_get_level())
                ob_clean();
            return $res;

        } catch (Exception $e) {
            error_log("Error CF System: " . $e->getMessage());
            return [];
        }
    }
    public function getEvidenciasPorFolioRuta($idRuta)
    {
        $sql = "        SELECT 
            t.folio_viaje,
            t.fecha_viaje,
            t.fecha_llegada,
            t.estatus_logistico,
            t.unidad_nombre,
            t.unidad_placas,
            t.nombre_chofer,
            t.ayudantes,
            t.orden_visita,
            t.direccion_entrega,
            t.estatus_parada,
            t.latitud,
            t.longitud,
            t.numeroVenta,
            t.folio_venta,
            t.cliente,
            t.tel_cliente,
            t.vehiculo_id,
        

            t.nombreProducto,
            t.totalCantidad,
            t.um,
            t.fc,
            t.ur,
            t.mid,

            t.foto_1,
            t.foto_2,
            t.estatus_entrega,
            t.fecha,
            t.hora,
            t.comentario

        FROM (

            SELECT 
                tc.viaje_folio AS folio_viaje,
                tc.fecha_creacion AS fecha_viaje,

                trm.hora_llegada_real AS fecha_llegada,
                trm.estado_reparto AS estatus_logistico,

                tv.nombre AS unidad_nombre,
                tv.placas AS unidad_placas,

                u_chofer.nombre AS nombre_chofer,

                (
                    SELECT GROUP_CONCAT(u_ayu.nombre SEPARATOR ' / ') 
                    FROM transporte_tripulantes_detalle ttd
                    INNER JOIN trabajadores u_ayu 
                        ON ttd.usuario_id = u_ayu.id
                    WHERE ttd.reparto_id = tc.reparto_id
                ) AS ayudantes,

                trp.id AS id_movimiento,
                m.id as mid,
                trp.orden_visita,
                trp.descripcion_punto AS direccion_entrega,
                trp.estado_punto AS estatus_parada,
                trp.latitud, 
                trp.longitud,
                 tv.id as vehiculo_id,

                v.id AS numeroVenta,
                v.folio AS folio_venta,

                c.nombre_comercial AS cliente,
                c.telefono AS tel_cliente,

                p.nombre AS nombreProducto,
                p.factor_conversion AS fc,
                p.unidad_reporte AS ur,

                SUM(m.cantidad) AS totalCantidad,

                p.unidad_medida AS um,

                IFNULL(
                    CONCAT('/myvet/', NULLIF(crv.fotografia_entrega, '')),
                    NULL
                ) AS foto_1,

                IFNULL(
                    CONCAT('/myvet/', NULLIF(crv.fotografia_nota, '')),
                    NULL
                ) AS foto_2,

                crv.estatus AS estatus_entrega,
                crv.fecha,
                crv.hora,
                crv.comentario

            FROM transporte_consolidacion tc

            INNER JOIN transporte_repartos_maestro trm 
                ON tc.reparto_id = trm.id

            INNER JOIN transporte_vehiculos tv 
                ON tc.vehiculo_id = tv.id

            INNER JOIN transporte_rutas_puntos trp 
                ON trm.id = trp.reparto_id 

            INNER JOIN movimientos m 
                ON trm.entrega_venta_id = m.id

            LEFT JOIN confirmacion_reparto_viaje crv
                ON crv.id_movimiento = m.id


            INNER JOIN productos p 
                ON m.producto_id = p.id

            LEFT JOIN ventas v 
                ON m.referencia_id = v.id

            LEFT JOIN clientes c 
                ON v.id_cliente = c.id

            LEFT JOIN trabajadores u_chofer 
                ON trm.usuario_encargado_id = u_chofer.id

            WHERE  tc.viaje_folio = ?

            GROUP BY 
                trp.id,
                p.id

        ) t

        GROUP BY 
            t.id_movimiento,
            t.nombreProducto

        ORDER BY t.orden_visita ASC
    ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            die($this->db->error);
        }

        $stmt->bind_param("s", $idRuta);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $data = [];

        while ($row = $resultado->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
    public function actualizarEvidencia($id, $comentario, $foto_entrega = null, $foto_nota = null)
    {
        $set = "comentario = ?";
        $params = [$comentario];
        $types = "s";

        if ($foto_entrega) {
            $set .= ", fotografia_entrega = ?";
            $params[] = $foto_entrega;
            $types .= "s";
        }
        if ($foto_nota) {
            $set .= ", fotografia_nota = ?";
            $params[] = $foto_nota;
            $types .= "s";
        }

        $params[] = $id;
        $types .= "i";

        $sql = "UPDATE confirmacion_reparto_viaje SET $set WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }
    public function eliminarEvidencia($id_movimiento)
    { // Cambiamos el nombre conceptual de la variable
        try {
            $this->db->begin_transaction();

            // 1. Opcional: Borrar archivos físicos antes de borrar el registro
            $sqlFotos = "SELECT fotografia_entrega, fotografia_nota FROM confirmacion_reparto_viaje WHERE id_movimiento = ?";
            $stmtF = $this->db->prepare($sqlFotos);
            $stmtF->bind_param("i", $id_movimiento);
            $stmtF->execute();
            $fotos = $stmtF->get_result()->fetch_assoc();

            if ($fotos) {
                $base = $_SERVER['DOCUMENT_ROOT'] . "/myvet/";
                if (!empty($fotos['fotografia_entrega']))
                    @unlink($base . $fotos['fotografia_entrega']);
                if (!empty($fotos['fotografia_nota']))
                    @unlink($base . $fotos['fotografia_nota']);
            }

            // 2. Borramos la evidencia usando el id_movimiento
            $sqlDel = "DELETE FROM confirmacion_reparto_viaje WHERE id_movimiento = ?";
            $stmtDel = $this->db->prepare($sqlDel);
            $stmtDel->bind_param("i", $id_movimiento);
            $stmtDel->execute();

            // 3. Regresamos el punto de ruta a 'pendiente'
            // Esto es lo que habilitará el botón "SUBIR" nuevamente
            $sqlPunto = "UPDATE transporte_rutas_puntos 
                     SET estado_punto = 'pendiente', 
                         llegada_real = NULL 
                     WHERE id = ?";
            $stmtP = $this->db->prepare($sqlPunto);
            $stmtP->bind_param("i", $id_movimiento);
            $stmtP->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->connect_errno) { // Verificación de conexión
                error_log("Error de conexión: " . $this->db->connect_error);
            }
            if ($this->db->in_transaction)
                $this->db->rollback();
            error_log("Error al eliminar evidencia: " . $e->getMessage());
            return false;
        }
    }
}