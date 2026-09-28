<?php

class PrestamosModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /* =========================
        CREAR PRÉSTAMO
    ========================= */
    public function crearPrestamo($data) {
        $sql = "INSERT INTO prestamos 
                (trabajador_id,gasto_id, almacen_id, monto_total, descripcion, estado, fecha_registro)
                VALUES (?, ?,?, ?, ?,?, NOW())";

        $stmt = $this->db->prepare($sql);
     return $stmt->execute([
    $data['trabajador_id'],
    $data['gasto_id'],
    $data['almacen_id'],
    $data['monto_total'],
    $data['descripcion'],
    $data['estado']
]);
    }

public function registrarAbono($data) {

    // obtener número de pago automático
    $sqlNum = "SELECT COUNT(*) + 1 as numero 
               FROM prestamos_abonos 
               WHERE prestamo_id = ?";

    $stmtNum = $this->db->prepare($sqlNum);
    $stmtNum->bind_param("i", $data['prestamo_id']);
    $stmtNum->execute();

    $resNum = $stmtNum->get_result();
    $rowNum = $resNum->fetch_assoc();
    $numero = $rowNum['numero'] ?? 1;

    $sql = "INSERT INTO prestamos_abonos
            (prestamo_id, monto_abono, numero_pago, metodo_pago, usuario_registro_id, fecha_abono, observaciones)
            VALUES (?, ?, ?, ?, ?, NOW(), ?)";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param(
        "idisis",
        $data['prestamo_id'],
        $data['monto_abono'],
        $numero,
        $data['metodo_pago'],
        $data['usuario_id'],
        $data['observaciones']
    );

    return $stmt->execute();
}
public function obtenerPrestamo($id) {

    $sql = "SELECT 
            p.*,
            t.nombre AS trabajador,
            COALESCE(SUM(pa.monto_abono), 0) AS total_abonado,
            (p.monto_total - COALESCE(SUM(pa.monto_abono),0)) AS saldo_pendiente
        FROM prestamos p
        LEFT JOIN trabajadores t ON t.id = p.trabajador_id
        LEFT JOIN prestamos_abonos pa ON pa.prestamo_id = p.id
        WHERE p.id = ?
        GROUP BY p.id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    return $res->fetch_assoc();
}
public function listarPrestamos($almacen_id = 0, $f_inicio = null, $f_fin = null) {

    $filtro = "";
    $params = [];

    // =========================
    // FILTRO ALMACÉN
    // =========================
    if ($almacen_id != 0) {
        $filtro = "WHERE p.almacen_id = ?";
        $params[] = $almacen_id;
    } else {
        $filtro = "WHERE 1=1";
    }

    // =========================
    // FILTRO FECHAS
    // =========================
    if (!empty($f_inicio) && !empty($f_fin)) {
        $filtro .= " AND DATE(p.fecha_registro) BETWEEN ? AND ?";
        $params[] = $f_inicio;
        $params[] = $f_fin;
    }

    $sql = "        SELECT 
            p.*,
            a.nombre as nombreAlmacen,
            t.nombre AS trabajador,
            COALESCE(SUM(pa.monto_abono), 0) AS total_abonado,
            (p.monto_total - COALESCE(SUM(pa.monto_abono),0)) AS saldo_pendiente
        FROM prestamos p
        LEFT JOIN trabajadores t ON t.id = p.trabajador_id
        LEFT JOIN prestamos_abonos pa ON pa.prestamo_id = p.id
        left join almacenes a on a.id=p.almacen_id 
        $filtro
        GROUP BY p.id
        ORDER BY p.fecha_registro DESC
    ";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return [];

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
public function listarAbonos($prestamo_id) {

    $sql = "
        SELECT *
        FROM prestamos_abonos
        WHERE prestamo_id = ?
        ORDER BY numero_pago ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();

    $res = $stmt->get_result();
    return $res->fetch_all(MYSQLI_ASSOC);
}
public function cerrarPrestamoSiPagado($prestamo_id) {

    $sql = "        SELECT 
            p.monto_total,
            COALESCE(SUM(pa.monto_abono),0) as abonado
        FROM prestamos p
        LEFT JOIN prestamos_abonos pa ON pa.prestamo_id = p.id
        WHERE p.id = ?
        GROUP BY p.id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();

    $res = $stmt->get_result();
    $data = $res->fetch_assoc();

    if (!$data) return false;

    if ($data['abonado'] >= $data['monto_total']) {

        $update = $this->db->prepare("UPDATE prestamos SET estado = 'pagado' WHERE id = ?");
        $update->bind_param("i", $prestamo_id);

        return $update->execute();
    }

    return true;
}

public function obtenerTotalDeuda($almacen_id = 0, $f_inicio = null, $f_fin = null) {

    $filtro = "";
    $params = [];

    if ($almacen_id != 0) {
        $filtro = "WHERE p.almacen_id = ?";
        $params[] = $almacen_id;
    } else {
        $filtro = "WHERE 1=1";
    }

    if (!empty($f_inicio) && !empty($f_fin)) {
        $filtro .= " AND DATE(p.fecha_registro) BETWEEN ? AND ?";
        $params[] = $f_inicio;
        $params[] = $f_fin;
    }

    $sql = "SELECT 
                SUM(
                    p.monto_total - IFNULL(pa.total_abonado, 0)
                ) AS deuda_total
            FROM prestamos p
            LEFT JOIN (
                SELECT prestamo_id, SUM(monto_abono) AS total_abonado
                FROM prestamos_abonos
                GROUP BY prestamo_id
            ) pa ON pa.prestamo_id = p.id
            $filtro";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return ['deuda_total' => 0];

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    return [
        'deuda_total' => floatval($res['deuda_total'] ?? 0)
    ];
}
public function obtenerDeudaTrabajadores($almacen_id = 0, $f_inicio = null, $f_fin = null) {

    $filtro = "";
    $params = [];

    if ($almacen_id != 0) {
        $filtro = "WHERE p.almacen_id = ?";
        $params[] = $almacen_id;
    } else {
        $filtro = "WHERE 1=1";
    }

    if (!empty($f_inicio) && !empty($f_fin)) {
        $filtro .= " AND DATE(p.fecha_registro) BETWEEN ? AND ?";
        $params[] = $f_inicio;
        $params[] = $f_fin;
    }

    $sql = "SELECT 
                t.id AS trabajador_id,
                t.nombre AS trabajador,

                SUM(p.monto_total) AS total_prestado,
                IFNULL(SUM(pa.total_abonado), 0) AS total_abonado,

                (SUM(p.monto_total) - IFNULL(SUM(pa.total_abonado), 0)) AS deuda_total

            FROM trabajadores t
            INNER JOIN prestamos p ON p.trabajador_id = t.id

            LEFT JOIN (
                SELECT prestamo_id, SUM(monto_abono) AS total_abonado
                FROM prestamos_abonos
                GROUP BY prestamo_id
            ) pa ON pa.prestamo_id = p.id

            $filtro

            GROUP BY t.id
            HAVING deuda_total > 0
            ORDER BY deuda_total DESC";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return [];

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
public function obtenerDeudaTrabajador($trabajador_id) {

    $sql = "SELECT 
                t.id AS trabajador_id,
                t.nombre AS trabajador,

                SUM(p.monto_total) AS total_prestado,

                IFNULL(SUM(pa.total_abonado), 0) AS total_abonado,

                (SUM(p.monto_total) - IFNULL(SUM(pa.total_abonado), 0)) AS deuda_total

            FROM trabajadores t
            INNER JOIN prestamos p 
                ON p.trabajador_id = t.id

            LEFT JOIN (
                SELECT 
                    prestamo_id,
                    SUM(monto_abono) AS total_abonado
                FROM prestamos_abonos
                GROUP BY prestamo_id
            ) pa 
                ON pa.prestamo_id = p.id

            WHERE t.id = ?

            GROUP BY t.id, t.nombre";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return [];

    // 🔥 FALTABA ESTO
    $stmt->bind_param("i", $trabajador_id);

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_assoc() : [];
}
public function eliminarPrestamo($id) {

    // 🔒 1. Verificar si tiene abonos
    $sqlCheck = "
        SELECT COUNT(*) AS total
        FROM prestamos_abonos
        WHERE prestamo_id = ?
    ";

    $stmt = $this->db->prepare($sqlCheck);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (intval($res['total']) > 0) {
        return [
            'success' => false,
            'message' => 'No se puede eliminar: el préstamo ya tiene abonos registrados.'
        ];
    }

    // 🧨 2. Eliminar préstamo
    $sql = "DELETE FROM prestamos WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Préstamo eliminado correctamente.'
        ];
    }

    return [
        'success' => false,
        'message' => 'Error al eliminar el préstamo.'
    ];
}
public function tieneAbonos($prestamo_id) {

    $sql = "
        SELECT 1
        FROM prestamos_abonos
        WHERE prestamo_id = ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();

    $res = $stmt->get_result();

    return $res->num_rows > 0;
}
public function eliminarGastoPorPrestamo($prestamo_id) {

    // 1. Obtener el folio (gasto_id)
    $sql = "SELECT gasto_id FROM prestamos WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();

    $res = $stmt->get_result()->fetch_assoc();

    if (!$res || empty($res['gasto_id'])) {
        throw new Exception("No existe folio de gasto");
    }

    $folio = $res['gasto_id'];

    // 2. Obtener ID REAL del gasto usando el folio
    $sqlGasto = "SELECT id FROM gastos WHERE folio = ?";
    $stmtGasto = $this->db->prepare($sqlGasto);
    $stmtGasto->bind_param("s", $folio);
    $stmtGasto->execute();

    $gasto = $stmtGasto->get_result()->fetch_assoc();

    if (!$gasto) {
        throw new Exception("Gasto no encontrado con ese folio");
    }

    $gasto_id_real = intval($gasto['id']);

    // 3. Eliminar detalle del gasto
    $sqlDetalle = "DELETE FROM detalle_gasto WHERE gasto_id = ?";
    $stmtDetalle = $this->db->prepare($sqlDetalle);
    $stmtDetalle->bind_param("i", $gasto_id_real);
    $stmtDetalle->execute();

    // 4. Eliminar gasto
    $sqlDelete = "DELETE FROM gastos WHERE id = ?";
    $stmtDelete = $this->db->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $gasto_id_real);
    $stmtDelete->execute();

    return true;
}
}