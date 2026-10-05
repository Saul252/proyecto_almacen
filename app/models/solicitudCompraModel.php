<?php
class SolicitudCompra
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function crear($data, $items)
    {
        try {
            $this->db->begin_transaction();
            $sqlMax = "SELECT COALESCE(MAX(folio), 0) AS max_folio 
               FROM solicitudes_compra 
               WHERE almacen_id = ?";

            $stmtMax = $this->db->prepare($sqlMax);
            $stmtMax->bind_param("i", $data['almacen_id']);
            $stmtMax->execute();
            $rowMax = $stmtMax->get_result()->fetch_assoc();

            // Asignar el nuevo folio consecutivo
            $nuevo_folio = $rowMax['max_folio'] + 1;
            // 🔹 CABECERA
            $sqlCab = "INSERT INTO solicitudes_compra 
                   (administrador_id, almacen_id, proveedor_id, estado,folio) 
                   VALUES (?, ?, ?, 'pendiente',?)";

            $stmt = $this->db->prepare($sqlCab);
            $stmt->bind_param("iiii", $data['usuario_id'], $data['almacen_id'], $data['proveedor_id'], $nuevo_folio);

            if (!$stmt->execute()) {
                throw new Exception("Error al insertar cabecera: " . $stmt->error);
            }

            $solicitud_id = $this->db->insert_id;

            // 🔹 DETALLE (con costo)
            $sqlDet = "INSERT INTO detalle_solicitud_compra 
                   (solicitud_id, producto_id, cantidad, costo) 
                   VALUES (?, ?, ?, ?)";

            $stmtDet = $this->db->prepare($sqlDet);

            foreach ($items as $id_producto => $item) {

                $id_prod = intval($id_producto);

                // 🔥 tomar datos correctamente
                $cant = floatval($item['cantidad'] ?? 0);
                $costo = floatval($item['costo'] ?? 0);

                $stmtDet->bind_param("iidd", $solicitud_id, $id_prod, $cant, $costo);

                if (!$stmtDet->execute()) {
                    throw new Exception("Error al insertar detalle: " . $stmtDet->error);
                }
            }

            $this->db->commit();
            return $solicitud_id;

        } catch (Throwable $e) {
            $this->db->rollback();
            return $e->getMessage();
        }
    }
    public function listar($es_admin, $almacen_id)
    {
        $sql = "SELECT s.*,c.tiene_faltantes as faltantes, p.nombre_comercial as proveedor_nombre, a.nombre as almacen_nombre, u.nombre as admin_nombre
                FROM solicitudes_compra s
                LEFT JOIN proveedores p ON s.proveedor_id = p.id
                left join compras c on s.compra_id_final=c.id
                LEFT JOIN almacenes a ON s.almacen_id = a.id
                LEFT JOIN usuarios u ON s.administrador_id = u.id";

        if (!$es_admin) {
            $sql .= " WHERE s.almacen_id = " . intval($almacen_id);
        }

        $sql .= " ORDER BY s.fecha_creacion DESC";

        $result = $this->db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function listarPorFechas(
        $es_admin = 0,
        $almacen_id = null,
        $fecha_inicio = null,
        $fecha_fin = null,
        $estado = null,
        $buscador = null
    ) {
        // 1. Agregamos "WHERE 1=1" al final de la consulta base
        $sql = "SELECT s.*, c.tiene_faltantes as faltantes, p.nombre_comercial as proveedor_nombre, 
                   a.nombre as almacen_nombre, u.nombre as admin_nombre
            FROM solicitudes_compra s
            LEFT JOIN proveedores p ON s.proveedor_id = p.id
            LEFT JOIN compras c ON s.compra_id_final = c.id
            LEFT JOIN almacenes a ON s.almacen_id = a.id
            LEFT JOIN usuarios u ON s.administrador_id = u.id
            WHERE 1=1"; // <-- Clave para que los "AND" funcionen directamente

        $params = [];
        $types = "";

        // Lógica de seguridad (opcional, por si quieres usar el $es_admin):
        // Si no es administrador, forzar a que solo vea el almacén asignado
        /*
        if ($es_admin == 0) {
            $almacen_id = $_SESSION['almacen_id']; // O como manejes el ID del almacén del usuario
        }
        */

        // 2. Cambiamos !empty por validaciones más precisas para IDs numéricos
        if (isset($almacen_id) && $almacen_id !== '' && $almacen_id !== 0) {
            $sql .= " AND s.almacen_id = ?";
            $types .= "i";
            $params[] = $almacen_id;
        }

        if (!empty($fecha_inicio)) {
            $sql .= " AND DATE(s.fecha_creacion) >= ?";
            $types .= "s";
            $params[] = $fecha_inicio;
        }

        if (!empty($fecha_fin)) {
            $sql .= " AND DATE(s.fecha_creacion) <= ?";
            $types .= "s";
            $params[] = $fecha_fin;
        }

        if (!empty($estado)) {
            $sql .= " AND s.estado = ?";
            $types .= "s";
            $params[] = $estado;
        }

        if (!empty($buscador)) {
            $sql .= " AND (
                    p.nombre_comercial LIKE ?
                    OR s.id LIKE ?
                  )";
            $types .= "ss";

            $like = "%{$buscador}%";
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY s.fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function obtenerDetalle($id)
    {
        $sql = "SELECT 
                d.*, 
                p.nombre AS producto_nombre, 
                p.sku, 
                p.unidad_medida, 
                p.unidad_reporte, 
                p.factor_conversion, 
                s.almacen_id AS almacen_origen_id,
                s.folio AS folio_actual,
                a.nombre AS almacen_nombre,
                prov.id AS proveedor_id, 
                prov.nombre_comercial AS proveedor_nombre,

                /* 🔑 Próximo folio: MAX(folio)+1 filtrado por el mismo almacén */
                (
                    SELECT IFNULL(MAX(s2.folio), 0) + 1
                    FROM solicitudes_compra s2
                    WHERE s2.almacen_id = s.almacen_id
                ) AS proximo_folio,

                CONCAT(
                    prov.direccion, ' ',
                    prov.numeroExt, ' ',
                    IFNULL(prov.numeroInt, ''), ', ',
                    prov.colonia, ', ',
                    prov.ciudad
                ) AS dp_direccion,

                prov.rfc        AS dp_rfc,
                prov.correo     AS dp_correo,
                prov.telefono   AS dp_telefono,
                prov.telefono2  AS dp_telefono2,
                prov.extencion  AS dp_extencion,
                prov.direccion  AS dp_direccion_fiscal,
                prov.colonia    AS dp_colonia,
                prov.ciudad     AS dp_ciudad,
                prov.numeroExt  AS dp_numeroExt,
                prov.numeroInt  AS dp_numeroInt,
                prov.activo     AS dp_activo,
                prov.creado_at  AS dp_creado_at

            FROM detalle_solicitud_compra d
            INNER JOIN productos p ON d.producto_id = p.id
            INNER JOIN solicitudes_compra s ON d.solicitud_id = s.id
            INNER JOIN almacenes a ON s.almacen_id = a.id
            LEFT JOIN proveedores prov ON s.proveedor_id = prov.id
            WHERE d.solicitud_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function cancelarOrden($id)
    {
        try {
            // Verificar estado actual
            $stmtCheck = $this->db->prepare("
            SELECT estado 
            FROM solicitudes_compra 
            WHERE id = ?
        ");

            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();

            $res = $stmtCheck->get_result()->fetch_assoc();

            // Solo permitir cancelar si está pendiente
            if (!$res || $res['estado'] !== 'pendiente') {
                return false;
            }

            // Cambiar estado a cancelado
            $stmt = $this->db->prepare("
            UPDATE solicitudes_compra 
            SET estado = 'cancelado' 
            WHERE id = ?
        ");

            $stmt->bind_param("i", $id);

            return $stmt->execute();

        } catch (Exception $e) {
            return false;
        }
    }
    public function eliminar($id)
    {
        try {
            // Verificar estado con MySQLi
            $stmtCheck = $this->db->prepare("SELECT estado FROM solicitudes_compra WHERE id = ?");
            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            $res = $stmtCheck->get_result()->fetch_assoc();

            if (!$res || $res['estado'] !== 'pendiente') {
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM solicitudes_compra WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    public function obtenerCostoTotal($id)
    {

        $sql = "SELECT SUM(d.costo) AS costo_total
            FROM detalle_solicitud_compra d
            WHERE d.solicitud_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return (float) ($row['costo_total'] ?? 0);
    }
    public function actualizarEstado($id, $almacen_id, $nuevoEstado, $compra_id = null)
    {

        // Forzamos minúsculas para coincidir con ENUM
        $nuevoEstado = strtolower($nuevoEstado);

        $sql = "        UPDATE solicitudes_compra 
        SET 
            estado = ?, 
            almacen_id = ?, 
            compra_id_final = ?
        WHERE id = ?
    ";

        $stmt = $this->db->prepare($sql);

        // s = string
        // i = integer
        $stmt->bind_param(
            "siii",
            $nuevoEstado,
            $almacen_id,
            $compra_id,
            $id
        );

        return $stmt->execute();
    }
}
