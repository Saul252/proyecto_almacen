<?php

class LogisticaModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * 🔹 Obtener viajes con filtros
     */
public function obtenerViajesFiltrados(
    $almacen = 0, 
    $fecha_inicio = null, 
    $fecha_fin = null,
    $chofer = '', 
    $ayudantes = '', 
    $estado = ''
) {

    $where = [];
    $params = [];
    $types = '';

    // 🔹 FILTRO POR ALMACÉN
    if (!empty($almacen) && $almacen > 0) {
        $where[] = "a.id = ?";
        $params[] = $almacen;
        $types .= 'i';
    }

    // 🔹 FILTRO POR RANGO DE FECHAS
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $where[] = "DATE(tc.fecha_creacion) BETWEEN ? AND ?";
        $params[] = $fecha_inicio;
        $params[] = $fecha_fin;
        $types .= 'ss';
    } elseif (!empty($fecha_inicio)) {
        $where[] = "DATE(tc.fecha_creacion) >= ?";
        $params[] = $fecha_inicio;
        $types .= 's';
    }

    // 🔹 FILTRO CHOFER
    if (!empty($chofer)) {
        $where[] = "u_chofer.nombre LIKE ?";
        $params[] = "%$chofer%";
        $types .= 's';
    }

    // 🔹 FILTRO AYUDANTES
    if (!empty($ayudantes)) {
        $where[] = "u_ayu.nombre LIKE ?";
        $params[] = "%$ayudantes%";
        $types .= 's';
    }

    // 🔹 FILTRO ESTADO
    if (!empty($estado)) {
        $where[] = "trm.estado_reparto = ?";
        $params[] = $estado;
        $types .= 's';
    }

    // 🔥 SI NO HAY FILTROS → NO PONE WHERE (TRAE TODO)
    $where_sql = (!empty($where)) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "SELECT 
        a.nombre as almacenOrigen,
        a.id,
        tc.viaje_folio AS folio_viaje,
        tc.fecha_creacion AS fecha_viaje,
        MAX(trm.hora_llegada_real) AS fecha_llegada,
        trm.estado_reparto AS estatus_logistico,
        tv.nombre AS unidad_nombre,
        u_chofer.nombre AS nombre_chofer,
         trp.descripcion_punto as direccion,
        GROUP_CONCAT(DISTINCT u_ayu.nombre SEPARATOR ' / ') AS ayudantes

    FROM transporte_consolidacion tc

    LEFT JOIN transporte_repartos_maestro trm 
        ON tc.reparto_id = trm.id
        LEFT JOIN transporte_rutas_puntos trp
        ON trp.reparto_id=trm.id

    LEFT JOIN transporte_vehiculos tv 
        ON tc.vehiculo_id = tv.id

    LEFT JOIN transporte_tripulantes_detalle ttd 
        ON trm.id = ttd.reparto_id

    LEFT JOIN trabajadores u_ayu 
        ON ttd.usuario_id = u_ayu.id

    LEFT JOIN trabajadores u_chofer 
        ON trm.usuario_encargado_id = u_chofer.id

    LEFT JOIN almacenes a 
        ON u_chofer.almacen_id = a.id

    $where_sql

    GROUP BY 
        tc.viaje_folio,
        a.nombre,
        a.id,
        tv.nombre,
        u_chofer.nombre,
        trm.estado_reparto

    ORDER BY 
        tc.fecha_creacion DESC,
        tc.viaje_folio ASC";

    $stmt = $this->db->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}public function obtenerViajesFiltradosPago(
    $almacen = 0, 
    $fecha_inicio = null, 
    $fecha_fin = null,
    $chofer = '', 
    $ayudantes = '', 
    $estado = ''
  
) {
      $vehiculo = 999;
    $where = [];
    $params = [];
    $types = '';

    // 🔹 FILTRO POR VEHÍCULO EXCLUIDO
    if (!empty($vehiculo)) {
        $where[] = "tv.id != ?";
        $params[] = $vehiculo; // Corregido: se usaba $almacen por error
        $types .= 'i';
    }

    // 🔹 FILTRO POR ALMACÉN
    if (!empty($almacen) && $almacen > 0) {
        $where[] = "a.id = ?";
        $params[] = $almacen;
        $types .= 'i';
    }

    // 🔹 FILTRO POR RANGO DE FECHAS
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $where[] = "DATE(tc.fecha_creacion) BETWEEN ? AND ?";
        $params[] = $fecha_inicio;
        $params[] = $fecha_fin;
        $types .= 'ss';
    } elseif (!empty($fecha_inicio)) {
        $where[] = "DATE(tc.fecha_creacion) >= ?";
        $params[] = $fecha_inicio;
        $types .= 's';
    }

    // 🔹 FILTRO CHOFER
    if (!empty($chofer) && $chofer > 0) {
        $where[] = "u_chofer.id = ?";
        $params[] = $chofer;
        $types .= 'i';
    }

    // 🔹 FILTRO AYUDANTES
    if (!empty($ayudantes)) {
        $where[] = "u_ayu.nombre LIKE ?";
        $params[] = "%$ayudantes%";
        $types .= 's';
    }

    // 🔹 FILTRO ESTADO
    if (!empty($estado)) {
        $where[] = "trm.estado_reparto = ?";
        $params[] = $estado;
        $types .= 's';
    }

    // SI NO HAY FILTROS → NO PONE WHERE
    $where_sql = (!empty($where)) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "SELECT 
        a.nombre AS almacenOrigen,
        a.id,
        tc.viaje_folio AS folio_viaje,
        tc.fecha_creacion AS fecha_viaje,
        MAX(trm.hora_llegada_real) AS fecha_llegada,
        trm.estado_reparto AS estatus_logistico,
        tv.nombre AS unidad_nombre,
        u_chofer.nombre AS nombre_chofer,
        u_chofer.id AS chofer_id,
        MAX(trp.descripcion_punto) AS direccion,
        trp.reparto_id AS viaje_id,
        MAX(pv.monto) AS monto,
        GROUP_CONCAT(DISTINCT u_ayu.nombre SEPARATOR ' / ') AS ayudantes

    FROM transporte_consolidacion tc

    LEFT JOIN transporte_repartos_maestro trm 
        ON tc.reparto_id = trm.id

    LEFT JOIN transporte_rutas_puntos trp
        ON trp.reparto_id = trm.id

    LEFT JOIN transporte_vehiculos tv 
        ON tc.vehiculo_id = tv.id

    LEFT JOIN transporte_tripulantes_detalle ttd 
        ON trm.id = ttd.reparto_id

    LEFT JOIN trabajadores u_ayu 
        ON ttd.usuario_id = u_ayu.id

    LEFT JOIN pagos_viaje pv
        ON trp.reparto_id = pv.id_viaje

    LEFT JOIN trabajadores u_chofer 
        ON trm.usuario_encargado_id = u_chofer.id

    LEFT JOIN almacenes a 
        ON u_chofer.almacen_id = a.id

    $where_sql

    GROUP BY 
        tc.viaje_folio,
        tc.fecha_creacion,
        a.nombre,
        a.id,
        tv.nombre,
        u_chofer.nombre,
        u_chofer.id,
        trm.estado_reparto,
        trp.reparto_id

    ORDER BY 
        tc.fecha_creacion DESC,
        tc.viaje_folio ASC";

    $stmt = $this->db->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    /**
     * 🔹 Contar viajes por persona (chofer + ayudantes)
     */
  public function contarViajesPorPersona(
    $almacen = 0, 
    $fecha_inicio = null, 
    $fecha_fin = null,
    $chofer = '', 
    $ayudantes = '', 
    $estado = ''
) {

    // 🔹 FILTROS SEPARADOS
    $where_chofer = [];
    $where_ayu = [];

    $params_chofer = [];
    $params_ayu = [];

    $types_chofer = '';
    $types_ayu = '';

    // 🔹 ALMACÉN
    if (!empty($almacen) && $almacen > 0) {
        $where_chofer[] = "a.id = ?";
        $where_ayu[] = "a.id = ?";

        $params_chofer[] = $almacen;
        $params_ayu[] = $almacen;

        $types_chofer .= 'i';
        $types_ayu .= 'i';
    }

    // 🔹 FECHAS
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $where_chofer[] = "DATE(tc.fecha_creacion) BETWEEN ? AND ?";
        $where_ayu[] = "DATE(tc.fecha_creacion) BETWEEN ? AND ?";

        $params_chofer[] = $fecha_inicio;
        $params_chofer[] = $fecha_fin;

        $params_ayu[] = $fecha_inicio;
        $params_ayu[] = $fecha_fin;

        $types_chofer .= 'ss';
        $types_ayu .= 'ss';

    } elseif (!empty($fecha_inicio)) {
        $where_chofer[] = "DATE(tc.fecha_creacion) >= ?";
        $where_ayu[] = "DATE(tc.fecha_creacion) >= ?";

        $params_chofer[] = $fecha_inicio;
        $params_ayu[] = $fecha_inicio;

        $types_chofer .= 's';
        $types_ayu .= 's';
    }

    // 🔹 CHOFER (solo chofer)
    if (!empty($chofer)) {
        $where_chofer[] = "u_chofer.nombre LIKE ?";
        $params_chofer[] = "%$chofer%";
        $types_chofer .= 's';
    }

    // 🔹 AYUDANTES (solo ayudantes)
    if (!empty($ayudantes)) {
        $where_ayu[] = "u_ayu.nombre LIKE ?";
        $params_ayu[] = "%$ayudantes%";
        $types_ayu .= 's';
    }

    // 🔹 ESTADO
    if (!empty($estado)) {
        $where_chofer[] = "trm.estado_reparto = ?";
        $where_ayu[] = "trm.estado_reparto = ?";

        $params_chofer[] = $estado;
        $params_ayu[] = $estado;

        $types_chofer .= 's';
        $types_ayu .= 's';
    }

    // 🔹 SQL WHERE
    $where_sql_chofer = (!empty($where_chofer)) ? "WHERE " . implode(" AND ", $where_chofer) : "";
    $where_sql_ayu = (!empty($where_ayu)) ? "WHERE " . implode(" AND ", $where_ayu) : "";

    // 🔥 SQL PRINCIPAL
    $sql = "
        SELECT 
            persona_id,
            nombre,
            GROUP_CONCAT(DISTINCT nombreAlmacen SEPARATOR ' / ') AS almacenes,
            COUNT(DISTINCT viaje_folio) AS total_viajes
        FROM (

            -- 🔹 CHOFERES
            SELECT 
                u_chofer.id AS persona_id,
                u_chofer.nombre AS nombre,
                tc.viaje_folio,
                a.nombre AS nombreAlmacen
            FROM transporte_consolidacion tc
            LEFT JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            LEFT JOIN trabajadores u_chofer ON trm.usuario_encargado_id = u_chofer.id
            LEFT JOIN almacenes a ON u_chofer.almacen_id = a.id
            $where_sql_chofer

            UNION ALL

            -- 🔹 AYUDANTES
            SELECT 
                u_ayu.id AS persona_id,
                u_ayu.nombre AS nombre,
                tc.viaje_folio,
                a.nombre AS nombreAlmacen
            FROM transporte_consolidacion tc
            LEFT JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            LEFT JOIN transporte_tripulantes_detalle ttd ON trm.id = ttd.reparto_id
            LEFT JOIN trabajadores u_ayu ON ttd.usuario_id = u_ayu.id
            LEFT JOIN almacenes a ON u_ayu.almacen_id = a.id
            $where_sql_ayu

        ) AS base

        WHERE persona_id IS NOT NULL

        GROUP BY persona_id, nombre
        ORDER BY total_viajes DESC
    ";

    $stmt = $this->db->prepare($sql);

    // 🔥 PARAMS CORRECTOS
    $params_final = array_merge($params_chofer, $params_ayu);
    $types_final = $types_chofer . $types_ayu;

    if (!empty($params_final)) {
        $stmt->bind_param($types_final, ...$params_final);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    public function aplicarPagoPorViaje($idViaje, $idChofer, $monto,$fecha)
{
    $sql = "INSERT INTO pagos_viaje (
                id_viaje,
                id_chofer,
                monto,
                fecha
            ) VALUES (?, ?, ?,?)";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $this->db->error);
    }

    $stmt->bind_param("iids", $idViaje, $idChofer, $monto,$fecha);

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar el pago: " . $stmt->error);
    }

    return [
        'success' => true,
        'message' => 'Pago registrado correctamente.',
        'id' => $stmt->insert_id
    ];
}
public function eliminarPagoPorViaje($idViaje)
{
    $sql = "DELETE FROM pagos_viaje WHERE id_viaje = ?";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $this->db->error);
    }

    $stmt->bind_param("i", $idViaje);

    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el pago: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No se encontró un pago para el viaje indicado.");
    }

    return [
        'success' => true,
        'message' => 'Pago eliminado correctamente.'
    ];
}
}