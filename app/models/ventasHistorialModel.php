<?php
/**
 * ventasHistorialModel.php
 * Lógica de base de datos para historial de ventas, entregas y pagos.
 */

class VentaHistorialModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerVentasFiltradas($filtros, $rol_id, $almacen_sesion) {
        $where = " WHERE v.id>1";
        
        // Seguridad por Almacén
        if ($rol_id != 1) { 
            $where .= " AND v.almacen_id = $almacen_sesion "; 
        } elseif (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }

        // Buscador (Folio o Cliente)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (c.nombre_comercial LIKE '%$s%' OR v.folio LIKE '%$s%'OR v.id LIKE '%$s%' OR v.factura LIKE '%$s%') ";
        }

        // Estatus Entrega
        if (!empty($filtros['status'])) {
            $st = $this->db->real_escape_string($filtros['status']);
            $where .= " AND v.estado_entrega = '$st' ";
        }
         if (!empty($filtros['factura'])) {
            $st = $this->db->real_escape_string($filtros['factura']);
            if($st>0)
                {
                    $where .= " AND v.factura != 0 ";
                }
                else{
                      $where .= " AND v.factura <1";

                }
          
        }
          // Estatus Entrega
        if (!empty($filtros['vendedor'])) {
            $st = $this->db->real_escape_string($filtros['vendedor']);
            $where .= " AND v.vendedor_id = '$st' ";
        }

        // Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros);
        }

        // Filtro por Estado de Pago (Saldo)
        $having = "";
        if (!empty($filtros['pago'])) {
            $having = ($filtros['pago'] == 'deuda') 
                ? " HAVING (v.total - pagado) > 0.01 " 
                : " HAVING (v.total - pagado) <= 0.01 ";
        }

        $sql = "SELECT v.*, c.nombre_comercial as cliente, a.nombre as almacen_nombre,u.nombre as vendedor,
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as pagado
                FROM ventas v
                join usuarios u on u.id=v.vendedor_id 
                JOIN clientes c ON v.id_cliente = c.id 
                JOIN almacenes a ON v.almacen_id = a.id 
                $where $having ORDER BY v.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function obtenerPagos($filtros, $rol_id = null, $almacen_sesion = null) {
    $where = " WHERE 1=1 ";
if ($rol_id != 1) { 
            $where .= " AND v.almacen_id = $almacen_sesion "; 
        } elseif (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }
    // Buscador general (Folio de venta, Referencia, ID de pago o ID de venta)
    if (!empty($filtros['search'])) {
        $s = $this->db->real_escape_string($filtros['search']);
        $where .= " AND (v.folio LIKE '%$s%' OR hit.referencia LIKE '%$s%' OR hit.id LIKE '%$s%' OR hit.venta_id LIKE '%$s%') ";
    }

    // Filtro por Método de Pago
    if (!empty($filtros['metodo_pago'])) {
        $mp = $this->db->real_escape_string($filtros['metodo_pago']);
        $where .= " AND hit.metodo_pago = '$mp' ";
    }

    // Filtro por Usuario que registró el pago
    if (!empty($filtros['vendedor'])) {
        $uid = intval($filtros['vendedor']);
        $where .= " AND hit.usuario_id = $uid ";
    }
     if (!empty($filtros['cliente'])) {
        $cid = intval($filtros['cliente']);
        $where .= " AND v.id_cliente = $cid";
    }

    // Rango de Fechas (Asegúrate de que 'construirFiltroFecha' evalúe 'hit.fecha')
    if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
        $where .= $this->construirFiltroFecha($filtros);
    }

    // Consulta limpia: Únicamente historial_pagos y ventas
    $sql = "SELECT hit.*, 
                   v.folio, 
                   c.nombre_comercial as cliente,
                   v.total as venta_total,
                   u.nombre,
                   a.nombre as almacen_nombre
            FROM historial_pagos hit
            JOIN ventas v ON v.id = hit.venta_id
            join clientes c on c.id=v.id_cliente
             JOIN almacenes a ON v.almacen_id = a.id 
            join usuarios u on u.id=hit.usuario_id
            $where 
            ORDER BY hit.fecha DESC, hit.id DESC";

    $res = $this->db->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

    public function obtenerVentasDeuda($filtros, $rol_id, $almacen_sesion) {
        $where = " WHERE v.id>1";
        
        // Seguridad por Almacén
        if ($rol_id != 1) { 
            $where .= " AND v.almacen_id = $almacen_sesion "; 
        } elseif (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }

        // Buscador (Folio o Cliente)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (c.nombre_comercial LIKE '%$s%' OR v.folio LIKE '%$s%'OR v.id LIKE '%$s%' OR v.factura LIKE '%$s%') ";
        }

        // Estatus Entrega
        if (!empty($filtros['status'])) {
            $st = $this->db->real_escape_string($filtros['status']);
            $where .= " AND v.estado_entrega = '$st' ";
        }
         if (!empty($filtros['factura'])) {
            $st = $this->db->real_escape_string($filtros['factura']);
            if($st>0)
                {
                    $where .= " AND v.factura != 0 ";
                }
                else{
                      $where .= " AND v.factura <1";

                }
          
        }
          // Estatus Entrega
        if (!empty($filtros['cliente'])) {
            $st = $this->db->real_escape_string($filtros['cliente']);
            $where .= " AND v.estado_entrega!='cancelado' AND v.id_cliente = '$st' ";
        }

        // Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros);
        }

        // Filtro por Estado de Pago (Saldo)
        $having = "";
        if (!empty($filtros['pago'])) {
            $having = ($filtros['pago'] == 'deuda') 
                ? " HAVING (v.total - pagado) > 0.01 " 
                : " HAVING (v.total - pagado) <= 0.01 ";
        }

        $sql = "SELECT v.*, c.nombre_comercial as cliente, a.nombre as almacen_nombre,u.nombre as vendedor,
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as pagado
                FROM ventas v
                join usuarios u on u.id=v.vendedor_id 
                JOIN clientes c ON v.id_cliente = c.id 
                JOIN almacenes a ON v.almacen_id = a.id 
                $where $having ORDER BY v.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerVentasFiltradasVendedor($filtros, $rol_id, $usuario) {
        $where = " WHERE v.id > '0' ";
        
        // Seguridad por Almacén
        if ($rol_id >3) { 
            $where .= " AND v.vendedor_id = $usuario "; 
        } 
        if (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }

        // Buscador (Folio o Cliente)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (c.nombre_comercial LIKE '%$s%' OR v.folio LIKE '%$s%'OR v.id LIKE '%$s%') ";
        }

        // Estatus Entrega
        if (!empty($filtros['status'])) {
            $st = $this->db->real_escape_string($filtros['status']);
            $where .= " AND v.estado_entrega = '$st' ";
        }
          // Estatus Entrega
        if (!empty($filtros['vendedor'])) {
            $st = $this->db->real_escape_string($filtros['vendedor']);
            $where .= " AND v.vendedor_id = '$st' ";
        }

        // Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros);
        }

        // Filtro por Estado de Pago (Saldo)
        $having = "";
        if (!empty($filtros['pago'])) {
            $having = ($filtros['pago'] == 'deuda') 
                ? " HAVING (v.total - pagado) > 0.01 " 
                : " HAVING (v.total - pagado) <= 0.01 ";
        }

        $sql = "SELECT v.*, c.nombre_comercial as cliente, a.nombre as almacen_nombre,u.nombre as vendedor,
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as pagado
                FROM ventas v
                join usuarios u on u.id=v.vendedor_id 
                JOIN clientes c ON v.id_cliente = c.id 
                JOIN almacenes a ON v.almacen_id = a.id 
                $where $having ORDER BY v.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
public function obtenerVentasFiltradasVendedorTotal($filtros, $rol_id, $usuario) {
        $where = " WHERE v.id > '0' ";
        
        // Seguridad por Almacén / Vendedor
        if ($rol_id > 3) { 
            $where .= " AND v.vendedor_id = " . intval($usuario); 
        } 
        if (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }

        // Buscador (Folio o Cliente)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            // Nota: Al agrupar por cliente, buscar por folio individual puede ser menos común, 
            // pero se mantiene por si buscas clientes que tengan un folio específico.
            $where .= " AND (c.nombre_comercial LIKE '%$s%' OR v.folio LIKE '%$s%' OR v.id LIKE '%$s%') ";
        }

        // Estatus Entrega
        if (!empty($filtros['status'])) {
            $st = $this->db->real_escape_string($filtros['status']);
            $where .= " AND v.estado_entrega = '$st' ";
        }

        // Estatus Vendedor (Filtro explícito)
        if (!empty($filtros['vendedor'])) {
            $st = $this->db->real_escape_string($filtros['vendedor']);
            $where .= " AND v.vendedor_id = '$st' ";
        }

        // Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros);
        }

        // Filtro por Estado de Pago (Saldo del Cliente)
        $having = "";
        if (!empty($filtros['pago'])) {
            $having = ($filtros['pago'] == 'deuda') 
                ? " HAVING total_debe > 0.01 " 
                : " HAVING total_debe <= 0.01 ";
        }

        // Nueva SQL: Agrupada por cliente con sumatorias correspondientes
        $sql = "SELECT 
                    v.id_cliente,
                    c.nombre_comercial as cliente,
                    SUM(v.total) as total_compro,
                    (SUM(v.total) -  (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id)
                   ) as total_debe
                   
             
                FROM ventas v
                JOIN usuarios u ON u.id = v.vendedor_id 
                JOIN clientes c ON v.id_cliente = c.id 
                JOIN almacenes a ON v.almacen_id = a.id 
                -- Subconsulta para traer los pagos agrupados por cliente y evitar duplicplicación de SUM()
                
                $where 
                GROUP BY v.id_cliente, c.nombre_comercial
                $having 
                ORDER BY cliente ASC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    private function construirFiltroFecha($f) {
        switch($f['rango']) {
            case 'hoy': return " AND DATE(v.fecha) = CURDATE() ";
            case 'ayer': return " AND DATE(v.fecha) = SUBDATE(CURDATE(),1) ";
            case 'semana': return " AND YEARWEEK(v.fecha, 1) = YEARWEEK(CURDATE(), 1) ";
            case 'mes': return " AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE()) ";
            case 'personalizado':
                $ini = $this->db->real_escape_string($f['inicio']);
                $fin = $this->db->real_escape_string($f['fin']);
                return " AND DATE(v.fecha) BETWEEN '$ini' AND '$fin' ";
            default: return "";
        }
    }

public function historialPagos($id) {
    $id = intval($id);
    // 4. Historial de Pagos
    $historialPagos = [];
    $sqlPagos = "SELECT histpa.fecha, histpa.monto, histpa.metodo_pago, histpa.referencia, u.nombre as usuario_nombre, venta.total as total
                 FROM historial_pagos histpa
                 JOIN usuarios u ON histpa.usuario_id = u.id 
                 join ventas venta on venta.id=histpa.venta_id
                 WHERE histpa.venta_id = $id 
                 ORDER BY histpa.fecha DESC";
    $resPagos = $this->db->query($sqlPagos);
    while($pago = $resPagos->fetch_assoc()){ 
        $historialPagos[] = $pago; 
    }
}
public function faltantePago($venta_id)
{
    $venta_id = intval($venta_id);

    $sql = "SELECT 
                COALESCE(SUM(histpa.monto), 0) AS total_pagado,
                v.total
            FROM ventas v
            LEFT JOIN historial_pagos histpa 
                ON histpa.venta_id = v.id
            WHERE v.id = $venta_id
            GROUP BY v.total
            LIMIT 1";

    $res = $this->db->query($sql);
    return $res ? $res->fetch_assoc() : [
        'total_pagado' => 0,
        'total' => 0
    ];
}
    public function obtenerDetalleCompleto($id) {
    $id = intval($id);
    
    // 1. Info de la venta (Cabecera)
    $sqlI = "SELECT v.*, c.nombre_comercial, a.nombre as almacen ,u.nombre as vendedor,
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado 
             FROM ventas v 
             join usuarios u on u.id=v.vendedor_id
             JOIN clientes c ON v.id_cliente = c.id 
             JOIN almacenes a ON v.almacen_id = a.id 
             
             WHERE v.id = $id";
    $info = $this->db->query($sqlI)->fetch_assoc();
    
    
    // 2. Productos con FACTOR DE CONVERSIÓN (Esta estaba bien)
    $prods = [];
    $sqlP = "SELECT 
    dv.*, dv.id as dvid, 
    odma.*,
    p.nombre AS producto, 
    p.sku,
    COALESCE(i.stock,0) AS disponible,

    p.unidad_medida,
    p.unidad_reporte,
    p.factor_conversion

FROM detalle_venta dv

INNER JOIN ventas v
    ON v.id = dv.venta_id
join opciones_de_medida_adicional odma on odma.id= dv.unidadMedida

INNER JOIN productos p
    ON p.id = dv.producto_id

LEFT JOIN inventario i
    ON i.producto_id = dv.producto_id
    AND i.almacen_id = v.almacen_id

WHERE dv.venta_id = $id";
    $resP = $this->db->query($sqlP);
    while($p = $resP->fetch_assoc()){ 
        $prods[] = $p; 
    }
    
    // 3. Historial de entregas (AQUÍ AGREGAMOS LAS COLUMNAS QUE FALTABAN)
    $historialEntregas = [];
    $sqlH = "SELECT ev.fecha, p.nombre as producto, de.cantidad, u.nombre as usuario_nombre,
                    p.unidad_medida, p.unidad_reporte, p.factor_conversion,odma.* 
             FROM entregas_venta ev 
             JOIN detalle_entrega de ON ev.id = de.entrega_id 
             JOIN detalle_venta dv ON de.detalle_venta_id = dv.id 

join opciones_de_medida_adicional odma on odma.id= dv.unidadMedida

             JOIN productos p ON dv.producto_id = p.id 
             JOIN usuarios u ON ev.usuario_id = u.id 
             WHERE ev.venta_id = $id ORDER BY ev.fecha DESC";
    $resH = $this->db->query($sqlH);
    while($h = $resH->fetch_assoc()){ 
        $historialEntregas[] = $h; 
    }

    // 4. Historial de Pagos
    $historialPagos = [];
    $sqlPagos = "SELECT hp.fecha, hp.monto, hp.metodo_pago, hp.referencia, u.nombre as usuario_nombre 
                 FROM historial_pagos hp
                 JOIN usuarios u ON hp.usuario_id = u.id 
                 WHERE hp.venta_id = $id 
                 ORDER BY hp.fecha DESC";
    $resPagos = $this->db->query($sqlPagos);
    while($pago = $resPagos->fetch_assoc()){ 
        $historialPagos[] = $pago; 
    }
    
    return [
        'info' => $info, 
        'productos' => $prods, 
        'historial' => $historialEntregas,
        'pagos' => $historialPagos
    ];
}
public function registrarSolicitudCancelacion($id_venta, $id_usuario, $razon) {
    $id_venta  = intval($id_venta);
    $id_usuario = intval($id_usuario);
    $razon     = trim($razon);
    $estado    = 1; // Estado por defecto

    // Validar datos mínimos
    if ($id_venta <= 0 || $id_usuario <= 0 || empty($razon)) {
        return [
            'status' => false,
            'message' => 'Datos incompletos o inválidos.'
        ];
    }

    $sql = "INSERT INTO solicitudes_cancelacion_ventas (id_venta, Id_usuario, razon, estado) VALUES (?, ?, ?, ?)";
    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status' => false,
            'error'   => $this->db->error,
            'message' => 'Error al preparar la consulta SQL.'
        ];
    }

    // "iisi" -> integer, integer, string, integer
    $stmt->bind_param("iisi", $id_venta, $id_usuario, $razon, $estado);

    if ($stmt->execute()) {
        $insert_id = $stmt->insert_id;
        $stmt->close();

        return [
            'status' => true,
            'insert_id' => $insert_id,
            'message' => 'Solicitud de cancelación registrada correctamente.'
        ];
    } else {
        $error = $stmt->error;
        $stmt->close();

        return [
            'status' => false,
            'error'   => $error,
            'message' => 'Error al guardar en la base de datos.'
        ];
    }
}
/**
 * Elimina físicamente un registro de la tabla solicitudes_cancelacion_ventas por su ID
 * @param int $id
 * @return array
 */
/**
 * Acepta una solicitud de cancelación cambiando su estado a 2
 * @param int $id ID de la solicitud
 * @return array
 */
public function aceptarSolicitudCancelacion($id) {
    $id = intval($id);
    $nuevo_estado = 2; // Estado Aceptado / Aprobado

    if ($id <= 0) {
        return [
            'status'  => false,
            'message' => 'ID de solicitud no válido.'
        ];
    }

   $sql = "UPDATE solicitudes_cancelacion_ventas 
        SET estado = ?, 
            fecha_eliminacion = NOW() 
        WHERE id = ? 
          AND estado = 1";
          $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status'  => false,
            'error'   => $this->db->error,
            'message' => 'Error al preparar la consulta SQL.'
        ];
    }

    // "ii" -> integer (nuevo_estado), integer (id)
    $stmt->bind_param("ii", $nuevo_estado, $id);

    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        $stmt->close();

        if ($affected_rows > 0) {
            return [
                'status'  => true,
                'message' => 'Solicitud de cancelación aceptada correctamente.'
            ];
        } else {
            return [
                'status'  => false,
                'message' => 'No se pudo actualizar. La solicitud no existe o ya no estaba pendiente.'
            ];
        }
    } else {
        $error = $stmt->error;
        $stmt->close();

        return [
            'status'  => false,
            'error'   => $error,
            'message' => 'Error al actualizar el estado en la base de datos.'
        ];
    }
}public function obtenerCancelacionesRecientes($almacen_id = 0) {
    date_default_timezone_set('America/Mexico_City');
    $solicitudes = [];
    $estado = 2;
    $almacen_id = intval($almacen_id);

    // Consulta base
    $sql = "SELECT 
                sc.id,
                sc.id_venta,
                sc.Id_usuario,
                sc.razon,
                sc.estado,
                sc.fecha_solicitud,
                sc.fecha_eliminacion,
                u.nombre AS usuario_nombre,
                v.total AS venta_total,
                v.folio as folio,
                v.id as idVenta
            FROM solicitudes_cancelacion_ventas sc
            LEFT JOIN usuarios u ON u.id = sc.Id_usuario
            LEFT JOIN ventas v ON v.id = sc.id_venta
            WHERE sc.estado = ?
              AND sc.fecha_eliminacion >= NOW() - INTERVAL 5 MINUTE";

    // Si almacen_id es diferente de 0, agregamos el filtro por almacén
    if ($almacen_id > 0) {
        $sql .= " AND v.almacen_id = ?";
    }

    $sql .= " ORDER BY sc.fecha_eliminacion DESC";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status'  => false,
            'error'   => $this->db->error,
            'message' => 'Error al preparar la consulta SQL.'
        ];
    }

    // Vincular parámetros según la presencia de almacen_id
    if ($almacen_id > 0) {
        $stmt->bind_param("ii", $estado, $almacen_id);
    } else {
        $stmt->bind_param("i", $estado);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $solicitudes[] = $row;
    }

    $stmt->close();

    return [
        'status' => true,
        'data'   => $solicitudes
    ];
}
public function eliminarSolicitudCancelacion($id) {
    $id = intval($id);

    if ($id <= 0) {
        return [
            'status' => false,
            'message' => 'ID no válido.'
        ];
    }

    $sql = "DELETE FROM solicitudes_cancelacion_ventas WHERE id = ?";
    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status'  => false,
            'error'   => $this->db->error,
            'message' => 'Error al preparar la consulta SQL.'
        ];
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        $stmt->close();

        if ($affected_rows > 0) {
            return [
                'status'  => true,
                'message' => 'Registro eliminado de la base de datos correctamente.'
            ];
        } else {
            return [
                'status'  => false,
                'message' => 'No se encontró la solicitud con el ID especificado.'
            ];
        }
    } else {
        $error = $stmt->error;
        $stmt->close();

        return [
            'status'  => false,
            'error'   => $error,
            'message' => 'Error al ejecutar la eliminación en la base de datos.'
        ];
    }
}
/**
 * Obtiene todas las solicitudes de cancelación pendientes (estado = 1)
 * @return array
 */
public function obtenerSolicitudesPendientes() {
    $solicitudes = [];
    $estado = 1;

    $sql = "SELECT 
                sc.id,
                sc.id_venta,
                sc.Id_usuario,
                sc.razon,
                sc.estado,
                -- O la columna de fecha que tengas
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as pagado,
                u.nombre AS usuario_nombre,
                v.total AS venta_total,
                v.folio as folio,
                v.id as idVenta
            FROM solicitudes_cancelacion_ventas sc
            INNER JOIN usuarios u ON u.id = sc.Id_usuario
            INNER JOIN ventas v ON v.id = sc.id_venta
            WHERE sc.estado = ?
            ORDER BY sc.id DESC";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status' => false,
            'error'   => $this->db->error,
            'message' => 'Error al preparar la consulta SQL.'
        ];
    }

    $stmt->bind_param("i", $estado);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $solicitudes[] = $row;
    }

    $stmt->close();

    return [
        'status' => true,
        'data'   => $solicitudes
    ];
}
   public function procesarEntrega($venta_id, $productos, $usuario_id) {
    $this->db->begin_transaction();
    try {
        // Obtener almacén y folio de la venta
        $vta_info = $this->db->query("SELECT almacen_id, folio FROM ventas WHERE id = $venta_id")->fetch_assoc();
        if (!$vta_info) throw new Exception("Venta no encontrada.");
        
        $almacen_id = $vta_info['almacen_id'];

        // 1. Crear cabecera de entrega
        $stmt = $this->db->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $venta_id, $usuario_id);
        $stmt->execute();
        $entrega_id = $this->db->insert_id;

        foreach ($productos as $dv_id => $cant) {
            $dv_id = intval($dv_id);
            $cant = floatval($cant);
            if ($cant <= 0) continue;

            // --- VALIDACIÓN A: Pendiente por entregar en la venta ---
            $res_v = $this->db->query("SELECT (cantidad - cantidad_entregada) as pendiente, producto_id, (SELECT nombre FROM productos WHERE id = producto_id) as nombre_prod 
                                       FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
            
            if ($cant > $res_v['pendiente']) {
                throw new Exception("La cantidad ({$cant}) excede lo pendiente para: {$res_v['nombre_prod']}");
            }

            // --- VALIDACIÓN B: Stock real en el almacén de la venta ---
            $stock_res = $this->db->query("SELECT stock FROM inventario 
                                           WHERE producto_id = {$res_v['producto_id']} 
                                           AND almacen_id = $almacen_id")->fetch_assoc();
            
            $stock_actual = ($stock_res) ? floatval($stock_res['stock']) : 0;

            if ($stock_actual < $cant) {
                throw new Exception("Stock insuficiente en almacén para {$res_v['nombre_prod']}. Disponible: {$stock_actual}, Requerido: {$cant}");
            }

            // 2. Registrar detalle de entrega y actualizar detalle_venta
            $this->db->query("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES ($entrega_id, $dv_id, $cant)");
            $this->db->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $cant WHERE id = $dv_id");

            // 3. Descontar Stock e Insertar Movimiento
            $this->db->query("UPDATE inventario SET stock = stock - $cant 
                             WHERE producto_id = {$res_v['producto_id']} AND almacen_id = $almacen_id");
            
            $mov_obs = "Salida por entrega parcial. Folio Venta: " . $vta_info['folio'];
            $this->db->query("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones,entrega_id) 
                             VALUES ({$res_v['producto_id']}, 'salida', $cant, $almacen_id, $usuario_id, $venta_id, '$mov_obs',$entrega_id)");
        }

        // 4. Actualizar estado_entrega general de la venta
        $check = $this->db->query("SELECT SUM(cantidad - cantidad_entregada) as deuda FROM detalle_venta WHERE venta_id = $venta_id")->fetch_assoc();
        $st = ($check['deuda'] <= 0) ? 'entregado' : 'parcial';
        $this->db->query("UPDATE ventas SET estado_entrega = '$st' WHERE id = $venta_id");

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollback();
        throw $e; // El controlador capturará el mensaje de "Stock insuficiente"
    }
}


   public function procesarEntregaMasiva($venta_id, $productos, $usuario_id) {
    $this->db->begin_transaction();
    try {
        // Obtener almacén y folio de la venta
        $vta_info = $this->db->query("SELECT almacen_id, folio FROM ventas WHERE id = $venta_id")->fetch_assoc();
        if (!$vta_info) throw new Exception("Venta no encontrada.");
        
        $almacen_id = $vta_info['almacen_id'];

        // 1. Crear cabecera de entrega
        $stmt = $this->db->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $venta_id, $usuario_id);
        $stmt->execute();
        $entrega_id = $this->db->insert_id;

        // 🔥 Inicializamos el arreglo donde guardaremos los datos de retorno
        $resultados_movimientos = [];

        foreach ($productos as $dv_id => $cant) {
            $dv_id = intval($dv_id);
            $cant = floatval($cant);
            if ($cant <= 0) continue;

            // --- VALIDACIÓN A: Pendiente por entregar en la venta ---
            $res_v = $this->db->query("SELECT (cantidad - cantidad_entregada) as pendiente, producto_id, (SELECT nombre FROM productos WHERE id = producto_id) as nombre_prod 
                                       FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
            
            if ($cant > $res_v['pendiente']) {
                throw new Exception("La cantidad ({$cant}) excede lo pendiente para: {$res_v['nombre_prod']}");
            }

            // --- VALIDACIÓN B: Stock real en el almacén de la venta ---
            $stock_res = $this->db->query("SELECT stock FROM inventario 
                                           WHERE producto_id = {$res_v['producto_id']} 
                                           AND almacen_id = $almacen_id")->fetch_assoc();
            
            $stock_actual = ($stock_res) ? floatval($stock_res['stock']) : 0;

            if ($stock_actual < $cant) {
                throw new Exception("Stock insuficiente en almacén para {$res_v['nombre_prod']}. Disponible: {$stock_actual}, Requerido: {$cant}");
            }

            // 2. Registrar detalle de entrega y actualizar detalle_venta
            $this->db->query("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES ($entrega_id, $dv_id, $cant)");
            $this->db->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $cant WHERE id = $dv_id");

            // 3. Descontar Stock e Insertar Movimiento
            $this->db->query("UPDATE inventario SET stock = stock - $cant 
                             WHERE producto_id = {$res_v['producto_id']} AND almacen_id = $almacen_id");
            
            $mov_obs = "Salida por entrega parcial. Folio Venta: " . $vta_info['folio'];
            $this->db->query("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones, entrega_id) 
                             VALUES ({$res_v['producto_id']}, 'salida', $cant, $almacen_id, $usuario_id, $venta_id, '$mov_obs', $entrega_id)");
            
            // 🔥 Capturamos el ID del movimiento que se acaba de insertar en la línea anterior
            $movimiento_id = $this->db->insert_id;

            // 🔥 Guardamos los IDs requeridos en nuestro arreglo
            $resultados_movimientos[] = [
                'venta_id'      => $venta_id,
                'producto_id'   => $res_v['producto_id'],
                'movimiento_id' => $movimiento_id
            ];
        }

        // 4. Actualizar estado_entrega general de la venta
        $check = $this->db->query("SELECT SUM(cantidad - cantidad_entregada) as deuda FROM detalle_venta WHERE venta_id = $venta_id")->fetch_assoc();
        $st = ($check['deuda'] <= 0) ? 'entregado' : 'parcial';
        $this->db->query("UPDATE ventas SET estado_entrega = '$st' WHERE id = $venta_id");

        $this->db->commit();
        
        // 🔥 Retornamos el arreglo con toda la información en lugar de true
        return $resultados_movimientos;
        
    } catch (Exception $e) {
        $this->db->rollback();
        throw $e; 
    }
}
public function actualizarFactura($venta_id, $factura) {
    $this->db->begin_transaction();
    try {
        // 1. Preparar la consulta. 
        // ¡MUY IMPORTANTE!: Asegúrate de que la columna se llame 'factura' en tu tabla. 
        // Si la columna en tu base de datos se llama diferente (ej: 'folio_factura' o 'num_factura'), cámbiala aquí abajo:
        $stmt = $this->db->prepare("UPDATE ventas SET factura = ? WHERE id = ?");
        
        if (!$stmt) {
            // Nota la barra invertida '\' antes de Exception para que PHP use la global del sistema
            throw new \Exception("Error al preparar la consulta: " . $this->db->error);
        }

        // 2. Vincular los parámetros (s = string, i = integer)
        $stmt->bind_param("si", $factura, $venta_id);

        // 3. Ejecutar
        if (!$stmt->execute()) {
            throw new \Exception("Error al ejecutar la actualización: " . $stmt->error);
        }

        // 4. Cerrar la sentencia preparada
        $stmt->close();

        $this->db->commit();
        return true;

    } catch (\Exception $e) { // 🔥 AQUÍ ESTABA EL ERROR: Agregamos '\' antes de Exception
        $this->db->rollback();
        throw $e; // Ahora el controlador sí capturará el mensaje limpiamente
    }
}

public function registrarAbono($venta_id, $monto, $usuario_id, $metodo_pago, $fecha_pago, $referencia ) {
    // 1. Lógica para la referencia: cadena vacía por defecto
    $ref_final= $referencia;
    $efectivoPagado=0;

    // 2. Lógica para saldo_favor: 
    // Si es "Saldo a Favor", copiamos el monto. Si no, 0.00.
    $saldo_favor_valor = ($metodo_pago === 'Saldo a Favor') ? floatval($monto) : 0.00;

    // 3. Definimos el INSERT ( columnas)
    $sql = "INSERT INTO historial_pagos (venta_id, monto, saldo_favor, fecha, usuario_id, metodo_pago, EfectivoPagado, referencia) 
            VALUES (?, ?, ?, ?, ?,?, ?, ?)";
    
    $stmt = $this->db->prepare($sql);
    
    // 4. BIND_PARAM CORREGIDO ("iddsiss")
    /**
     * EXPLICACIÓN DEL ORDEN:
     * 1. venta_id      -> (i) Integer
     * 2. monto         -> (d) Double
     * 3. saldo_favor   -> (d) Double
     * 4. fecha         -> (s) String
     * 5. usuario_id    -> (i) Integer
     * 6. metodo_pago   -> (s) String
     * 7. EfectivoPagado-> (d) Double
     * 8. referencia    -> (s) String
     */
    $stmt->bind_param("iddsisds", 
        $venta_id,          // 1
        $monto,             // 2
        $saldo_favor_valor, // 3
        $fecha_pago,        // 4
        $usuario_id,        // 5
        $metodo_pago,       // 6
        $efectivoPagado,    // 7
        $ref_final          // 8
    );
    
    $resultado = $stmt->execute();
    
    if (!$resultado) {
        error_log("Error en registrarAbono: " . $stmt->error);
    }

    $stmt->close();
    return $resultado;
}
public static function obtenerClientePorVenta($conexion,$venta_id) {
    try {
        $sql = "SELECT id_cliente FROM ventas WHERE id = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $venta_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        return $resultado ? intval($resultado['id_cliente']) : false;
    } catch (Exception $e) {
        error_log("Error al obtener cliente de la venta $venta_id: " . $e->getMessage());
        return false;
    }
}

}