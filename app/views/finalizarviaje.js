
el salario es fijo pero necesito que por semana empezando el lunes tome lo que es de cada semana y si hay faltas pues reste lo de las faltas(sumadas por trabjador) y las reste al trabajador, lo mismo para lo de pagos viajes, y tabien soo se resta lo que se pago de prestamos por semana
SELECT `id`, `trabajador_id`, `fecha`, `monto`, `tipo`, `descripcion` FROM `faltas` WHERE 1
SELECT `id`, `id_viaje`, `id_chofer`, `monto`,`fecha` FROM `pagos_viaje` WHERE 1
public function listarTrabajadores($almacen_id = 0) {

    if ($almacen_id == 0) {
        // 🔥 ADMIN → todos
        $sql = "SELECT
    t.id,
    t.nombre,
    t.telefono,
    t.rol,
    t.estado,
    t.almacen_id,
    t.fecha_registro,
    t.salario,

    (
        t.salario -
        COALESCE(SUM(pre.monto_total - COALESCE(ab.total_abonado,0)),0)
    ) AS salario_disponible,

    COALESCE(SUM(pre.monto_total - COALESCE(ab.total_abonado,0)),0) AS total_prestamos_pendientes,

    a.nombre AS nombreAlmacen,

    (
        SELECT GROUP_CONCAT(
            CONCAT(
                IFNULL(nombre,''),
                '|||',
                IFNULL(direccion,''),
                '|||',
                IFNULL(id,'')
            )
            SEPARATOR ';;;'
        )
        FROM documentos_trabajadores dt
        WHERE dt.trabajador_id = t.id
        AND dt.activo = 1
    ) AS documentos_url

FROM trabajadores t

JOIN almacenes a
    ON a.id = t.almacen_id

LEFT JOIN prestamos pre
    ON pre.trabajador_id = t.id

LEFT JOIN (
    SELECT
        prestamo_id,
        SUM(monto_abono) AS total_abonado
    FROM prestamos_abonos
    GROUP BY prestamo_id
) ab
    ON ab.prestamo_id = pre.id

GROUP BY t.id

ORDER BY t.nombre ASC;";
        $stmt = $this->db->prepare($sql);
    } else {
        // 🔒 SUCURSAL → solo su almacén
        $sql = "SELECT
    t.id,
    t.nombre,
    t.telefono,
    t.rol,
    t.estado,
    t.almacen_id,
    t.fecha_registro,
    t.salario,

    (
        t.salario -
        COALESCE(SUM(pre.monto_total - COALESCE(ab.total_abonado,0)),0)
    ) AS salario_disponible,

    COALESCE(SUM(pre.monto_total - COALESCE(ab.total_abonado,0)),0) AS total_prestamos_pendientes,

    a.nombre AS nombreAlmacen,

    (
        SELECT GROUP_CONCAT(
            CONCAT(
                IFNULL(nombre,''),
                '|||',
                IFNULL(direccion,''),
                '|||',
                IFNULL(id,'')
            )
            SEPARATOR ';;;'
        )
        FROM documentos_trabajadores dt
        WHERE dt.trabajador_id = t.id
        AND dt.activo = 1
    ) AS documentos_url

FROM trabajadores t

JOIN almacenes a
    ON a.id = t.almacen_id

LEFT JOIN prestamos pre
    ON pre.trabajador_id = t.id

LEFT JOIN (
    SELECT
        prestamo_id,
        SUM(monto_abono) AS total_abonado
    FROM prestamos_abonos
    GROUP BY prestamo_id
) ab
    ON ab.prestamo_id = pre.id
WHERE t.almacen_id = ?
GROUP BY t.id



ORDER BY nombre ASC;";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}