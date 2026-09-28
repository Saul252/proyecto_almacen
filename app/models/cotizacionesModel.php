<?php
class cotizacionesModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
public function crear($data, $items) {
    try {

        $this->db->begin_transaction();

        // =========================
        // CABECERA
        // =========================
        $sqlCab = "INSERT INTO cotizaciones 
        (
            cliente_id,
            almacen_id,
            usuario_id,
            fecha,
            descuento,
            total,
            observaciones,
            estado,
            vendedor_id
            
        ) 
        VALUES (?, ?, ?, NOW(), ?, ?, ?, 'pendiente',?)";

        $stmt = $this->db->prepare($sqlCab);

        $descuento = $data['descuento'] ?? 0;
        $totalCotizacion = $data['totalCotizacion'] ?? 0;
        $observaciones = $data['observaciones'] ?? '';

        $stmt->bind_param(
            "iiiddsi",
            $data['cliente_id'],
            $data['almacen_id'],
            $data['usuario_id'],
            $descuento,
            $totalCotizacion,
            $observaciones,
             $data['vendedor']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al insertar cabecera: " . $stmt->error);
        }

        $cotizacion_id = $this->db->insert_id;

        // =========================
        // DETALLE
        // =========================
        $sqlDet = "INSERT INTO detalle_cotizacion
        (
            cotizacion_id,
            producto_id,
            cantidad,
            unidadMedida,
            precio_unitario,
            tipo_precio,
            subtotal
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmtDet = $this->db->prepare($sqlDet);

        foreach ($items as $id_producto => $item) {

            $id_prod = intval($id_producto);
            $cant = floatval($item['cantidad']);
            $unidadMedida = floatval($item['unidad']);
            $precio_unitario = floatval($item['precio_unitario']);
            $tipo_precio = $item['tipo_precio']??'minorista';
            $subtotal = floatval($item['subtotal']);

 $stmtDet->bind_param(
    "iidddsd",
    $cotizacion_id,
    $id_prod,
    $cant,
    $unidadMedida,
    $precio_unitario,
    $tipo_precio,
    $subtotal
);
            if (!$stmtDet->execute()) {
                throw new Exception("Error al insertar detalle: " . $stmtDet->error);
            }
        }

        $this->db->commit();
        return true;

    } catch (Throwable $e) {

        $this->db->rollback();
        return $e->getMessage();
    }
}
public function actualizar($data, $items) {
    try {
        // Iniciamos la transacción para asegurar la integridad de los datos
        $this->db->begin_transaction();

        // =====================================================
        // 1. ACTUALIZAR CABECERA
        // =====================================================
        $sqlCab = "UPDATE cotizaciones 
                   SET cliente_id = ?, 
                       almacen_id = ?, 
                       descuento = ?, 
                       total = ?, 
                       observaciones = ?,
                       vendedor_id =?
                   WHERE id = ?";

        $stmt = $this->db->prepare($sqlCab);

        $vendedor = intval($data['vendedor_id']??0);
        $cotizacion_id = intval($data['cotizacion_id']); // El ID enviado desde el input oculto del modal editar
        $descuento = $data['descuento'] ?? 0;
        $totalCotizacion = $data['totalCotizacion'] ?? 0;
        $observaciones = $data['observaciones'] ?? '';

        $stmt->bind_param(
            "iiddsii",
            $data['cliente_id'],
            $data['almacen_id'],
            $descuento,
            $totalCotizacion,
            $observaciones,
            $vendedor,
            $cotizacion_id
            

        );

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar la cabecera: " . $stmt->error);
        }

        // =====================================================
        // 2. LIMPIAR DETALLE ANTERIOR
        // =====================================================
        // Eliminamos el detalle viejo para escribir el nuevo sin duplicar llaves o arrastrar productos eliminados
        $sqlDel = "DELETE FROM detalle_cotizacion WHERE cotizacion_id = ?";
        $stmtDel = $this->db->prepare($sqlDel);
        $stmtDel->bind_param("i", $cotizacion_id);
        
        if (!$stmtDel->execute()) {
            throw new Exception("Error al limpiar el detalle anterior: " . $stmtDel->error);
        }

        // =====================================================
        // 3. INSERTAR NUEVO DETALLE REFACTORIZADO
        // =====================================================
        $sqlDet = "INSERT INTO detalle_cotizacion
        (
            cotizacion_id,
            producto_id,
            cantidad,
            unidadMedida,
            precio_unitario,
            tipo_precio,
            subtotal
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmtDet = $this->db->prepare($sqlDet);

        foreach ($items as $item) {
            // Nota: Mapeamos las llaves exactamente como las envía el payload JSON de tu formulario de edición de JS
            $id_prod = intval($item['producto_id']);
            $cant = floatval($item['cantidad']);
            $unidadMedida = intval($item['unidad']); // Convertido a intval ya que suele ser un ID de la tabla unidades/medidas
            $precio_unitario = floatval($item['precioUnitario']);
            $tipo_precio = $item['tipoPrecio'] ?? 'minorista';
            $subtotal = floatval($item['precio']); // En tu JSON del submit, 'precio' es el subtotal acumulado de la fila

            $stmtDet->bind_param(
                "iidddsd",
                $cotizacion_id,
                $id_prod,
                $cant,
                $unidadMedida,
                $precio_unitario,
                $tipo_precio,
                $subtotal
            );

            if (!$stmtDet->execute()) {
                throw new Exception("Error al insertar el nuevo detalle en la edición: " . $stmtDet->error);
            }
        }

        // Si todo salió bien, guardamos cambios de forma persistente
        $this->db->commit();
        return true;

    } catch (Throwable $e) {
        // Si algo falla, deshacemos absolutamente todo lo ejecutado en este bloque
        $this->db->rollback();
        return $e->getMessage();
    }
}
    public function listar($es_admin, $almacen_id) {
        $sql = "SELECT co.*, c.nombre_comercial as cliente_nombre, a.nombre as almacen_nombre, u.nombre as admin_nombre
                FROM cotizaciones co
                LEFT JOIN clientes c ON co.cliente_id =c.id
                LEFT JOIN almacenes a ON co.almacen_id = a.id
                LEFT JOIN usuarios u ON co.usuario_id = u.id";
        
        if (!$es_admin) {
            $sql .= " WHERE co.almacen_id = " . intval($almacen_id);
        }
        
        $sql .= " ORDER BY co.fecha DESC";
        
        $result = $this->db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    
    
    
    
    public function listarPorFechas(
    $es_admin=0,
    $almacen_id=null,
    $fecha_inicio = null,
    $fecha_fin = null,
    $estado = null,
    $buscador = null,
    $vendedor=null
)
 {

    $sql = "SELECT
                co.*,
                c.nombre_comercial AS cliente_nombre,
                a.nombre AS almacen_nombre,
                u.nombre AS admin_nombre,
                u2.nombre as vendedor
            FROM cotizaciones co
            LEFT JOIN clientes c ON co.cliente_id = c.id
            LEFT JOIN almacenes a ON co.almacen_id = a.id
            LEFT JOIN usuarios u ON co.usuario_id = u.id
            left join usuarios u2 on u2.id=co.vendedor_id
            WHERE 1=1";

    $params = [];
    $types = "";

    if (!empty($almacen_id)) {
        $sql .= " AND co.almacen_id = ?";
        $types .= "i";
        $params[] = $almacen_id;
    }
     if (!empty($vendedor)) {
        $sql .= " AND co.vendedor_id = ?";
        $types .= "i";
        $params[] = $vendedor;
    }

    if (!empty($fecha_inicio)) {
        $sql .= " AND DATE(co.fecha) >= ?";
        $types .= "s";
        $params[] = $fecha_inicio;
    }

    if (!empty($fecha_fin)) {
        $sql .= " AND DATE(co.fecha) <= ?";
        $types .= "s";
        $params[] = $fecha_fin;
    }

    if (!empty($estado)) {
        $sql .= " AND co.estado = ?";
        $types .= "s";
        $params[] = $estado;
    }

    if (!empty($buscador)) {

        $sql .= " AND (
                    c.nombre_comercial LIKE ?
                    OR co.id LIKE ?
                  )";

        $types .= "ss";

        $like = "%{$buscador}%";

        $params[] = $like;
        $params[] = $like;
    }

    $sql .= " ORDER BY co.fecha DESC";

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
public function obtenerDetalle($id) {
    $sql = "SELECT d.*,co.vendedor_id as vendedor_id,co.total,co.observaciones, p.nombre as producto_nombre, p.sku, p.unidad_medida, o.nombre,o.equivalencia,
                   p.unidad_reporte, p.factor_conversion, co.almacen_id as almacen_origen_id,
                   a.nombre as almacen_nombre,c.id as cliente_id, c.nombre_comercial as cliente_nombre,c.direccion, c.telefono,c.rfc,user.id, user.nombre as nombreVendedor
                   
               

    

                      
                               FROM detalle_cotizacion d
            INNER JOIN productos p ON d.producto_id = p.id
            INNER JOIN cotizaciones co ON d.cotizacion_id = co.id
            INNER JOIN almacenes a ON co.almacen_id = a.id
            INNER JOIN opciones_de_medida_adicional o on d.unidadMedida =o.id
            LEFT JOIN clientes c ON co.cliente_id = c.id
            left join usuarios user on co.vendedor_id = user.id
            WHERE d.cotizacion_id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // IMPORTANTE: Aquí es donde entregamos los datos al controlador
    return $result->fetch_all(MYSQLI_ASSOC); 
}
public function obtenerDetalleCo($id) {
    $sql = "SELECT co.cliente_id,co.almacen_id  FROM cotizaciones co 
            
            WHERE co.id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // IMPORTANTE: Aquí es donde entregamos los datos al controlador
    return $result->fetch_all(MYSQLI_ASSOC); 
}
public function obtenerDetalleSolo($id) {
    $sql = "SELECT d.*  FROM detalle_cotizacion d
            
            WHERE d.cotizacion_id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // IMPORTANTE: Aquí es donde entregamos los datos al controlador
    return $result->fetch_all(MYSQLI_ASSOC); 
}
public function cancelarOrden($id) {
    try {
        // Verificar estado actual
        $stmtCheck = $this->db->prepare("
            SELECT estado 
            FROM cotizaciones 
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
            UPDATE cotizaciones 
            SET estado = 'cancelado' 
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        return $stmt->execute();

    } catch (Exception $e) {
        return false;
    }
}

public function completarC($id) {
    try {
     
       

        // Cambiar estado a cancelado
        $stmt = $this->db->prepare("
            UPDATE cotizaciones 
            SET estado = 'completado' 
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        return $stmt->execute();

    } catch (Exception $e) {
        return false;
    }
}
    public function eliminar($id) {
        try {
            // Verificar estado con MySQLi
            $stmtCheck = $this->db->prepare("SELECT estado FROM cotizaciones WHERE id = ?");
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
    public function obtenerCostoTotal($id) {

    $sql = "SELECT SUM(d.costo) AS costo_total
            FROM detalle_solicitud_compra d
            WHERE d.solicitud_id = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    return (float)($row['costo_total'] ?? 0);
}
public function actualizarEstado($id, $almacen_id, $nuevoEstado, $compra_id = null) {

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
