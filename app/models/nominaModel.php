<?php
class NominaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar todos (Solo para Admin Global)
    public function listar() {
        $sql = "SELECT * FROM trabajadores ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
     public function subirDocumentoCompra($id, $nombre_evidencia, $documento_url)
{
    $sql = "INSERT INTO documentos_trabajadores
            (trabajador_id, nombre, direccion)
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
    // NUEVO: Listar por almacén específico
    public function listarPorAlmacen($almacen_id) {
        $id = intval($almacen_id);
        $sql = "SELECT * FROM trabajadores WHERE almacen_id  = $id AND rol!='Administrador'ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
     public function listarPorAlmacenEncargado($almacen_id) {
        $id = intval($almacen_id);
        $sql = "SELECT * FROM trabajadores WHERE almacen_id  = $id AND rol='Administrador'ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function crearBono($data) {
        $sql = "INSERT INTO bonos 
                (trabajador_id,fecha,monto)
                VALUES (?, ?,?)";

        $stmt = $this->db->prepare($sql);
     return $stmt->execute([
    $data['trabajador_id'],
     $data['fecha'],
    $data['monto'],
     
   
   
]);
    }
    public function listarNominaSemanal($fechaInicio, $fechaFin, $almacen_id = 0)
{
    if ($almacen_id == 0) {

        // 🔥 ADMIN → Todos los almacenes
        $sql = "SELECT
                    t.id,
                    t.nombre,
                    t.telefono,
                    t.rol,
                    t.estado,
                    t.almacen_id,
                    t.fecha_registro,
                    t.complemento_pago,
                    (t.salario + t.complemento_pago) AS salario,

                    a.nombre AS nombreAlmacen,

                    COALESCE(f.total_faltas,0) AS total_faltas,
                    COALESCE(v.total_viajes,0) AS total_viajes,
                    COALESCE(ab.total_abonos,0) AS total_abonos,
                    COALESCE(vaca.monto_restante,0) AS total_vacaciones,
                    COALESCE(vaca.retenciones,0) AS total_retenciones,
                       COALESCE(bo.total_bonos,0)  AS total_bonos,

                    (
                       ( t.salario + t.complemento_pago)
                        - COALESCE(f.total_faltas,0)
                        + COALESCE(v.total_viajes,0)
                        + COALESCE(bo.total_bonos,0)

                        - COALESCE(ab.total_abonos,0)
                    ) AS total_nomina,

                    (
                        SELECT
                            COALESCE(SUM(pre.monto_total - COALESCE(pa.total_abonado,0)),0)
                        FROM prestamos pre
                        LEFT JOIN (
                            SELECT
                                prestamo_id,
                                SUM(monto_abono) total_abonado
                            FROM prestamos_abonos
                            GROUP BY prestamo_id
                        ) pa
                        ON pa.prestamo_id = pre.id
                        WHERE pre.trabajador_id = t.id
                    ) AS total_prestamos_pendientes,

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
                        WHERE dt.trabajador_id=t.id
                        AND dt.activo=1
                    ) AS documentos_url

                FROM trabajadores t

                INNER JOIN almacenes a
                    ON a.id=t.almacen_id

                /* ===== FALTAS ===== */
                LEFT JOIN(
                    SELECT
                        trabajador_id,
                        SUM(monto) total_faltas
                    FROM faltas
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY trabajador_id
                ) f
                    ON f.trabajador_id=t.id
                    LEFT JOIN(
                     /* ===== BONOS ===== */
                    SELECT
                        trabajador_id,
                        SUM(monto) total_bonos
                    FROM bonos
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY trabajador_id
                ) bo
                    ON bo.trabajador_id=t.id
                     /* ===== VACACIONES ===== */
                    LEFT JOIN(
                    SELECT
                        id_trabajador,
                        monto_restante,
                        retenciones
                        
                    FROM vacaciones
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY id_trabajador
                ) vaca
                    ON vaca.id_trabajador=t.id

                /* ===== VIAJES ===== */
                LEFT JOIN(
                    SELECT
                        id_chofer,
                        SUM(monto) total_viajes
                    FROM pagos_viaje
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY id_chofer
                ) v
                    ON v.id_chofer=t.id

                /* ===== ABONOS DE PRÉSTAMOS ===== */
                LEFT JOIN(
                    SELECT
                        p.trabajador_id,
                        SUM(pa.monto_abono) total_abonos
                    FROM prestamos_abonos pa
                    INNER JOIN prestamos p
                        ON p.id=pa.prestamo_id
                    WHERE pa.fecha_abono BETWEEN ? AND ?
                    GROUP BY p.trabajador_id
                ) ab
                    ON ab.trabajador_id=t.id

                ORDER BY t.nombre ASC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->db->error);
        }

        $stmt->bind_param(
            "ssssssssss",
            $fechaInicio,
            $fechaFin,
            $fechaInicio,
            $fechaFin,
            $fechaInicio,
            $fechaFin,
            $fechaInicio,
            $fechaFin,$fechaInicio,
            $fechaFin
        );

    } else {

        // 🔒 Solo un almacén
        $sql = "SELECT
                    t.id,
                    t.nombre,
                    t.telefono,
                    t.rol,
                    t.estado,
                    t.almacen_id,
                    t.fecha_registro,
                   (t.salario + t.complemento_pago) AS salario,
                    a.nombre AS nombreAlmacen,

                    COALESCE(f.total_faltas,0) AS total_faltas,
                    COALESCE(v.total_viajes,0) AS total_viajes,
                    COALESCE(ab.total_abonos,0) AS total_abonos,
                      COALESCE(vaca.monto_restante,0) AS total_vacaciones,
                    COALESCE(vaca.retenciones,0) AS total_retenciones,

                    COALESCE(bo.total_bonos,0)  AS total_bonos,

                    (
                       ( t.salario + t.complemento_pago)
                        - COALESCE(f.total_faltas,0)
                        + COALESCE(v.total_viajes,0)
                        + COALESCE(bo.total_bonos,0)

                        - COALESCE(ab.total_abonos,0)
                    ) AS total_nomina,

                    (
                        SELECT
                            COALESCE(SUM(pre.monto_total - COALESCE(pa.total_abonado,0)),0)
                        FROM prestamos pre
                        LEFT JOIN (
                            SELECT
                                prestamo_id,
                                SUM(monto_abono) total_abonado
                            FROM prestamos_abonos
                            GROUP BY prestamo_id
                        ) pa
                        ON pa.prestamo_id = pre.id
                        WHERE pre.trabajador_id = t.id
                    ) AS total_prestamos_pendientes,

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
                        WHERE dt.trabajador_id=t.id
                        AND dt.activo=1
                    ) AS documentos_url

                FROM trabajadores t

                INNER JOIN almacenes a
                    ON a.id=t.almacen_id

                LEFT JOIN(
                    SELECT
                        trabajador_id,
                        SUM(monto) total_faltas
                    FROM faltas
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY trabajador_id
                ) f
                    ON f.trabajador_id=t.id
                    LEFT JOIN(
                    SELECT
                        trabajador_id,
                        SUM(monto) total_bonos
                    FROM bonos
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY trabajador_id
                ) bo
                    ON bo.trabajador_id=t.id
                      /* ===== VACACIONES ===== */
                    LEFT JOIN(
                    SELECT
                        id_trabajador,
                        monto_restante,
                        retenciones
                        
                    FROM vacaciones
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY id_trabajador
                ) vaca
                    ON vaca.id_trabajador=t.id


                LEFT JOIN(
                    SELECT
                        id_chofer,
                        SUM(monto) total_viajes
                    FROM pagos_viaje
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY id_chofer
                ) v
                    ON v.id_chofer=t.id

                LEFT JOIN(
                    SELECT
                        p.trabajador_id,
                        SUM(pa.monto_abono) total_abonos
                    FROM prestamos_abonos pa
                    INNER JOIN prestamos p
                        ON p.id=pa.prestamo_id
                    WHERE pa.fecha_abono BETWEEN ? AND ?
                    GROUP BY p.trabajador_id
                ) ab
                    ON ab.trabajador_id=t.id

                WHERE t.almacen_id=?

                ORDER BY t.nombre ASC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->db->error);
        }

        $stmt->bind_param(
            "ssssssssssi",
            $fechaInicio,
            $fechaFin,
            $fechaInicio,
            $fechaFin,
            $fechaInicio,
            $fechaFin,
            $fechaInicio,
            $fechaFin,
             $fechaInicio,
            $fechaFin,
            $almacen_id
        );
    }

    $stmt->execute();

    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
 public function listarTrabajadoresDisponiblesPorAlmacen($almacen_id) {
    $id = intval($almacen_id);

    $sql = "        SELECT t.*
        FROM trabajadores t
        WHERE t.almacen_id = $id
        AND rol!='Administrador'
        AND t.id NOT IN (

            -- Encargados en rutas activas
            SELECT rm.usuario_encargado_id
            FROM transporte_repartos_maestro rm
            WHERE rm.estado_reparto = 'en_transito'
            AND rm.usuario_encargado_id IS NOT NULL

            UNION

            -- Tripulantes en rutas activas
            SELECT td.usuario_id
            FROM transporte_tripulantes_detalle td
            INNER JOIN transporte_repartos_maestro rm2 
                ON rm2.id = td.reparto_id
            WHERE rm2.estado_reparto = 'en_transito'
        )
        ORDER BY t.nombre ASC
    ";

    $res = $this->db->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
    public function guardar($d) {
        $nombre = $this->db->real_escape_string($d['nombre']);
        $tel    = $this->db->real_escape_string($d['telefono']);
        $rol    = $this->db->real_escape_string($d['rol']);
        $estado = $this->db->real_escape_string($d['estado']); 
        $salario = $this->db->real_escape_string($d['salario']);
        $alm_id = intval($d['almacen_id']); // Nueva columna crítica

        if (!empty($d['id'])) {
            // EDITAR: Incluimos almacen_id por si el admin global lo mueve de sucursal
            $id = intval($d['id']);
            $sql = "UPDATE trabajadores 
                    SET nombre='$nombre', telefono='$tel', rol='$rol', estado='$estado', almacen_id=$alm_id ,salario='$salario'
                    WHERE id=$id";
        } else {
            // INSERTAR: Obligatorio asignar el almacén desde el inicio
            $sql = "INSERT INTO trabajadores (nombre, telefono, rol, estado, almacen_id,salario) 
                    VALUES ('$nombre', '$tel', '$rol', '$estado', $alm_id,$salario)";
        }
        return $this->db->query($sql);
    }

    public function eliminar($id) {
        $id = intval($id);
        return $this->db->query("DELETE FROM trabajadores WHERE id = $id");
    }
       public function listarPersonal($almacen_id = 0) {
        // Si mandas 0, busca en todos (opcional), si no, filtra por sucursal
        $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = " . intval($almacen_id) : "";
        
        $sql = "SELECT id, nombre, rol 
                FROM trabajadores 
                WHERE estado = 'activo' 
              
                $whereAlmacen
                ORDER BY nombre ASC";
                
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Ajustado para logística filtrando por almacén
    public function listarPersonalLogistica($almacen_id = 0) {
        // Si mandas 0, busca en todos (opcional), si no, filtra por sucursal
        $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = " . intval($almacen_id) : "";
        
        $sql = "SELECT id, nombre, rol 
                FROM trabajadores 
                WHERE estado = 'activo' 
                AND rol IN ('chofer', 'cargador') 
                $whereAlmacen
                ORDER BY nombre ASC";
                
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
public function nombreTrabajador($id)
{
    $sql = "SELECT nombre FROM trabajadores WHERE id = ?";
    $stmt = $this->db->prepare($sql);

    if (!$stmt) return null;

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    return $row['nombre'] ?? null;
}

public function eliminarDocumento( $id_documento) {

    $sql = "UPDATE documentos_trabajadores
            SET activo = 0
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("i", $id_documento);

    return $stmt->execute();
}
    // NUEVO: Listar vehículos por almacén específico
}