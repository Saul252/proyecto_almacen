<?php
class EgresoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }
public function obtenerAlmacenesActivos() {
    $sql = "SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC";
    $res = $this->db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}
    /**
     * 1. OBTIENE TODO EL FLUJO (COMPRAS + GASTOS)
     * Usa un UNION para juntar ambas tablas en una sola lista para la tabla principal
     */
public function obtenerTodosLosEgresosFiltros(
    $desde,
    $hasta,
    $almacen_id = 0,
    $tipo_filtro = 'todos',
    $categoria_gasto_id = 0,
    $deuda_filtro = 'todos',
    $metodo_filtro = 'todos'
) {
    $parts = [];
    $params = [];
    $types = "";

    // Formatear fechas para cubrir el día completo
    $desde_f = $desde . " 00:00:00";
    $hasta_f = $hasta . " 23:59:59";

    /* --- BLOQUE 1: COMPRAS --- */
    // Solo entra si no filtramos específicamente por categorías de gastos
    if (($tipo_filtro === 'todos' || $tipo_filtro === 'compra') && $categoria_gasto_id == 0) {
        $whereAlmacen = ($almacen_id > 0) ? " AND c.almacen_id = ?" : "";
        $whereMetodo  = ($metodo_filtro !== 'todos') ? " AND LOWER(COALESCE(c.metodo_pago,'efectivo')) LIKE ?" : "";
        $whereDeuda   = "";
        
        if ($deuda_filtro === '1') $whereDeuda = " AND cpp.id IS NOT NULL AND cpp.estado != 'pagado'";
        elseif ($deuda_filtro === '0') $whereDeuda = " AND (cpp.id IS NULL OR cpp.estado = 'pagado')";

       $parts[] = "(SELECT 
            c.id, 
            c.folio, 
            c.fecha_compra AS fecha, 
            COALESCE(pro.nombre_comercial, c.proveedor) AS entidad,
            c.total, 
            COALESCE(c.metodo_pago, 'efectivo') AS metodo_pago,
            'compra' AS tipo, 
           (
    SELECT GROUP_CONCAT(
        CONCAT(
            IFNULL(nombre, ''),
            '|||',
            IFNULL(direccion, ''),
            '|||',
            IFNULL(id, '')
        )
        SEPARATOR ';;;'
    )
    FROM documentos_egresos de
    WHERE de.compra_id = c.id and de.activo=1
) AS documento_url, 
            0 AS categoria_id,
            IFNULL((SELECT SUM(cantidad_pendiente) FROM faltantes_ingreso WHERE compra_id = c.id), 0) AS piezas_faltantes,
            a.nombre AS almacen_nombre, 
            c.estado,
            CASE WHEN cpp.id IS NOT NULL AND cpp.estado != 'pagado' THEN 1 ELSE 0 END AS tiene_deuda,
            CASE WHEN cpp.id IS NOT NULL AND cpp.estado = 'pagado' THEN 1 ELSE 0 END AS pagado_cpp
        FROM compras c
        JOIN almacenes a ON c.almacen_id = a.id
        LEFT JOIN cuentas_por_pagar cpp ON cpp.id_referencia_origen = c.id
        LEFT JOIN proveedores pro ON c.proveedor = pro.id
       
        WHERE (c.fecha_compra BETWEEN ? AND ?) 
        AND c.estado != 'cancelada'
        $whereAlmacen $whereMetodo $whereDeuda)";

        $types .= "ss";
        $params[] = $desde_f; $params[] = $hasta_f;
        if ($almacen_id > 0) { $types .= "i"; $params[] = $almacen_id; }
        if ($metodo_filtro !== 'todos') { $types .= "s"; $params[] = "%".$metodo_filtro."%"; }
    }

    /* --- BLOQUE 2: GASTOS --- */
    // Solo entra si no estamos buscando específicamente deudas de compras
    if (($tipo_filtro === 'todos' || $tipo_filtro === 'gasto') && $deuda_filtro === 'todos') {
        $whereAlmacen = ($almacen_id > 0) ? " AND g.almacen_id = ?" : "";
        $whereCat     = ($categoria_gasto_id > 0) ? " AND g.categoria_id = ?" : "";
        $whereMetodo  = ($metodo_filtro !== 'todos') ? " AND LOWER(COALESCE(g.metodo_pago,'efectivo')) LIKE ?" : "";

        $parts[] = "(SELECT 
            g.id, g.folio, g.fecha_gasto AS fecha, g.beneficiario AS entidad,
            g.total, COALESCE(g.metodo_pago, 'efectivo') AS metodo_pago,
            'gasto' AS tipo, (
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
    FROM documentos_egresos de
    WHERE de.gasto_id = g.id and de.activo=1
) AS documento_url, g.categoria_id,
            0 AS piezas_faltantes, a.nombre AS almacen_nombre, g.estado,
            0 AS tiene_deuda, 0 AS pagado_cpp
        FROM gastos g
        JOIN almacenes a ON g.almacen_id = a.id
        
        WHERE (g.fecha_gasto BETWEEN ? AND ?) AND g.estado != 'cancelado'
        $whereAlmacen $whereCat $whereMetodo)";

        $types .= "ss";
        $params[] = $desde_f; $params[] = $hasta_f;
        if ($almacen_id > 0) { $types .= "i"; $params[] = $almacen_id; }
        if ($categoria_gasto_id > 0) { $types .= "i"; $params[] = $categoria_gasto_id; }
        if ($metodo_filtro !== 'todos') { $types .= "s"; $params[] = "%".$metodo_filtro."%"; }
    }

    /* --- BLOQUE 3: PAGOS DE DEUDAS --- */
    // Solo entra si no hay filtros específicos de otros tipos
    if (($tipo_filtro === 'todos' || $tipo_filtro === 'pago_deuda') && $categoria_gasto_id == 0 && $deuda_filtro === 'todos') {
        $whereAlmacen = ($almacen_id > 0) ? " AND p.almacen_id = ?" : "";
        $whereMetodo  = ($metodo_filtro !== 'todos') ? " AND LOWER(COALESCE(p.metodo_pago,'efectivo')) LIKE ?" : "";

        $parts[] = "(SELECT 
            p.id, p.referencia_pago AS folio, p.fecha_pago AS fecha, 
            p.observaciones AS entidad,
            p.monto AS total, COALESCE(p.metodo_pago, 'efectivo') AS metodo_pago,
            'pago_deuda' AS tipo, NULL AS documento_url, 0 AS categoria_id,
            0 AS piezas_faltantes, a.nombre AS almacen_nombre, 'confirmado' AS estado,
            0 AS tiene_deuda, 1 AS pagado_cpp
        FROM pagos_cuentas_por_pagar p
        JOIN almacenes a ON p.almacen_id = a.id
        WHERE (p.fecha_pago BETWEEN ? AND ?)
        $whereAlmacen $whereMetodo)";

        $types .= "ss";
        $params[] = $desde_f; $params[] = $hasta_f;
        if ($almacen_id > 0) { $types .= "i"; $params[] = $almacen_id; }
        if ($metodo_filtro !== 'todos') { $types .= "s"; $params[] = "%".$metodo_filtro."%"; }
    }

    if (empty($parts)) return [];

    // Unir todo con UNION ALL
    $sql = implode(" UNION ALL ", $parts) . " ORDER BY fecha DESC, id DESC";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return [];

    // Vinculación dinámica
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    /**
     * 2. REGISTRA UN GASTO (CON EVIDENCIA Y DESCRIPCIÓN)
     * Según tu tabla 'gastos' y 'detalle_gasto'
     */
// CONSULTA 1: Obtener productos para el buscador
    public function buscarProductos($termino) {
        $query = "SELECT 
                    id, 
                    nombre, 
                    sku, 
                    unidad_medida,    -- Ej: 'Pieza'
                    unidad_reporte,   -- Ej: 'Millar'
                    factor_conversion -- Ej: 1000
                  FROM productos 
                  WHERE (nombre LIKE ? OR sku LIKE ?) 
                  AND estado = 1 
                  ";
                  
        $stmt = $this->db->prepare($query);
        $likeTerm = "%$termino%";
        $stmt->bind_param("ss", $likeTerm, $likeTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    /**
 * Obtiene todos los productos activos para llenar el selector del modal
 */
public function listarProductos() {
    $query = "SELECT 
                id, 
                nombre, 
                sku, 
                unidad_medida, 
                unidad_reporte, 
                factor_conversion 
              FROM productos 
              WHERE estado = 1 
              ORDER BY nombre ASC";
              
    $res = $this->db->query($query);
    if (!$res) return [];
    return $res->fetch_all(MYSQLI_ASSOC);
}
public function registrarGasto($cabecera, $descripciones, $cantidades, $precios) {
    // 1. Iniciar transacción
    $this->db->begin_transaction();
    
    try {
        // 2. Insertar Cabecera
        $sql = "INSERT INTO gastos 
                (folio, fecha_gasto, almacen_id, categoria_id, usuario_registra_id, beneficiario, metodo_pago, total, documento_url, observaciones, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pagado')";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception("Error en Prepare Cabecera: " . $this->db->error);
        date_default_timezone_set('America/Mexico_City');
       

        // ✅ CORRECCIÓN AQUÍ (tipos correctos)
        $stmt->bind_param("ssiiissdss", 
            $cabecera['folio'], 
             $cabecera['fecha'], 
            $cabecera['almacen_id'],
            $cabecera['categoria_id'],
            $cabecera['usuario_id'], 
            $cabecera['beneficiario'], 
            $cabecera['metodo_pago'], 
            $cabecera['total'], 
            $cabecera['documento_url'], 
            $cabecera['observaciones']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar Cabecera: " . $stmt->error);
        }

        $gasto_id = $this->db->insert_id;

        // 3. Insertar Detalles
        $sqlDet = "INSERT INTO detalle_gasto (gasto_id, descripcion, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmtD = $this->db->prepare($sqlDet);
        if (!$stmtD) throw new Exception("Error en Prepare Detalle: " . $this->db->error);

        foreach ($descripciones as $i => $desc) {
            if (empty($desc)) continue;
            
            $cant = floatval($cantidades[$i]);
            $prec = floatval($precios[$i]);
            $subt = $cant * $prec;

            $stmtD->bind_param("isddd", $gasto_id, $desc, $cant, $prec, $subt);
            if (!$stmtD->execute()) {
                throw new Exception("Error al ejecutar Detalle en fila $i: " . $stmtD->error);
            }
        }

        // 4. Commit
        if ($this->db->commit()) {
            return ['success' => true, 'id' => $gasto_id];
        } else {
            throw new Exception("Error al hacer Commit.");
        }

    } catch (Exception $e) {
        $this->db->rollback();
        throw $e;
    }
}
public function registrarGastoInsumo($cabecera, $descripciones, $cantidades, $precios, $items) {
    // 1. Iniciar transacción
    $this->db->begin_transaction();
    
    try {
        // Establecer zona horaria antes de cualquier operación
        date_default_timezone_set('America/Mexico_City');

        // 2. Insertar Cabecera
        $sql = "INSERT INTO gastos 
                (folio, fecha_gasto, almacen_id, categoria_id, usuario_registra_id, beneficiario, metodo_pago, total, documento_url, observaciones, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pagado')";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception("Error en Prepare Cabecera: " . $this->db->error);
       
        $stmt->bind_param("ssiiissdss", 
            $cabecera['folio'], 
            $cabecera['fecha'], 
            $cabecera['almacen_id'],
            $cabecera['categoria_id'],
            $cabecera['usuario_id'], 
            $cabecera['beneficiario'], 
            $cabecera['metodo_pago'], 
            $cabecera['total'], 
            $cabecera['documento_url'], 
            $cabecera['observaciones']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar Cabecera: " . $stmt->error);
        }
        

        $gasto_id = $this->db->insert_id;

        // 3. Preparar Consultas de Detalles e Insumos
        $sqlDet = "INSERT INTO detalle_gasto (gasto_id, descripcion, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $insumo = "INSERT INTO compras_insumos (id_insumo, costo, cantidad, proveedor, fecha, existencias_lote) VALUES (?, ?, ?, ?, NOW(),?)";
        
        // CORRECCIÓN STOCK: Si ya existe el insumo, suma la cantidad al stock actual
        $insumo_stock = "INSERT INTO insumos_stock (id_insumo, existencias) VALUES (?, ?) 
                         ON DUPLICATE KEY UPDATE existencias = existencias + VALUES(existencias)";
                         // Obtener el nombre del insumo
$sqlinfo = "SELECT nombre FROM insumos WHERE id = ?";



        $stmtD = $this->db->prepare($sqlDet);
        $stmtI = $this->db->prepare($insumo);
        $stmtstock = $this->db->prepare($insumo_stock);

        if (!$stmtD || !$stmtI || !$stmtstock) {
            throw new Exception("Error en Prepare de detalles/insumos: " . $this->db->error);
        }
        
        // 4. Recorrer y procesar filas
        foreach ($descripciones as $i => $desc) {
            if (empty($desc)) continue;
            
            $cant = floatval($cantidades[$i]);
            $prec = floatval($precios[$i]);
            $subt = $cant * $prec;
            $id_insumo = intval($items[$i] ?? 0);
             
$nombreInsumo='';
            // Si está vinculado a un insumo, registrar en inventario
            if ($id_insumo > 0) {
               
                // "iddsd" -> entero, double, double, string (proveedor)
                $stmtI->bind_param(
    "iddsd",
    $id_insumo,
    $prec,
    $cant,
    $cabecera['beneficiario'],
    $cant 
);
$stmt = $this->db->prepare($sqlinfo);
$stmt->bind_param("i", $id_insumo);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $nombreInsumo = $row['nombre'];
} else {
    $nombreInsumo = '';
}
                if (!$stmtI->execute()) {
                    throw new Exception("Error al registrar compra de insumo en fila $i: " . $stmtI->error);
                }

                // "id" -> entero, double (por si manejas existencias con decimales, si es entero usa "ii")
                $stmtstock->bind_param("id", $id_insumo, $cant);
                if (!$stmtstock->execute()) {
                    throw new Exception("Error al actualizar stock en fila $i: " . $stmtstock->error);
                }
            }
          $razon= $desc . ' articulo comprado: ' . $nombreInsumo;

            // Registrar el detalle general del gasto
            $stmtD->bind_param("isddd", $gasto_id, $razon, $cant, $prec, $subt);
            if (!$stmtD->execute()) {
                throw new Exception("Error al ejecutar Detalle en fila $i: " . $stmtD->error);
            }
        }

        // 5. Confirmar Transacción
        if ($this->db->commit()) {
            return ['success' => true, 'id' => $gasto_id];
        } else {
            throw new Exception("Error al hacer Commit.");
        }

    } catch (Exception $e) {
        $this->db->rollback();
        throw $e;
    }
}
    public function registrarCompra($cabecera, $productos) {
        $this->db->begin_transaction();
        try {
            // 1. Insertar Cabecera Compra
            $sqlCompra = "INSERT INTO compras 
(folio, proveedor, fecha_compra, almacen_id, total, metodo_pago, usuario_registra_id, estado) 
VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmada')";

$stmt = $this->db->prepare($sqlCompra);

$stmt->bind_param(
    "sssdisi",
    $cabecera['folio'],
    $cabecera['proveedor'],
    $cabecera['fecha'],
    $cabecera['almacen_id'],
    $cabecera['total'],
    $cabecera['metodo_pago'],
    $cabecera['usuario_id']
);

$stmt->execute();
$compra_id = $this->db->insert_id;

            // 2. Detalle y Actualización de Stock
            $sqlDetalle = "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
            $sqlStock = "UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?";
            
            $stmtD = $this->db->prepare($sqlDetalle);
            $stmtS = $this->db->prepare($sqlStock);

            foreach ($productos as $p) {
                // Guardar detalle
                $stmtD->bind_param("iiddd", $compra_id, $p['id'], $p['cantidad'], $p['precio'], $p['subtotal']);
                $stmtD->execute();

                // Afectar Inventario (Kardex/Stock)
                $stmtS->bind_param("dii", $p['cantidad'], $p['id'], $cabecera['almacen_id']);
                $stmtS->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
public function obtenerDetalleCompleto($tipo, $id) {
    $response = ['tipo_documento' => $tipo];

    if ($tipo === 'compra') {

        // CABECERA COMPRAS
        $sql = "SELECT c.*, a.nombre as almacen_nombre, u.nombre as usuario_nombre, p.nombre_comercial as proveedorNombre
                FROM compras c 
                JOIN almacenes a ON c.almacen_id = a.id 
                JOIN proveedores p ON c.proveedor = p.id 
                JOIN usuarios u ON c.usuario_registra_id = u.id 
                WHERE c.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response['cabecera'] = $stmt->get_result()->fetch_assoc();

        // DETALLE COMPRAS
        $sqlDet = "SELECT 
                        dc.*, 
                        p.sku, 
                        p.nombre as producto_nombre, 
                        p.unidad_medida, 
                        p.unidad_reporte, 
                        p.factor_conversion as factor_prod,
                        p.id as producto_id,

                        (SELECT GROUP_CONCAT(
                            CONCAT(a.nombre, ' [', m.cantidad, ']') 
                            SEPARATOR '||'
                        )
                         FROM movimientos m 
                         JOIN almacenes a ON m.almacen_destino_id = a.id
                         WHERE m.referencia_id = dc.compra_id 
                           AND m.producto_id = dc.producto_id 
                           AND m.tipo = 'entrada'
                        ) as desglose_movimientos,

                        (SELECT IFNULL(SUM(m.cantidad), 0) 
                         FROM movimientos m 
                         WHERE m.referencia_id = dc.compra_id 
                           AND m.producto_id = dc.producto_id 
                           AND m.tipo = 'entrada'
                        ) as cantidad_recibida

                   FROM detalle_compra dc
                   JOIN productos p ON dc.producto_id = p.id
                   WHERE dc.compra_id = ?";

        $stmtDet = $this->db->prepare($sqlDet);
        $stmtDet->bind_param("i", $id);
        $stmtDet->execute();

        $response['items'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);

        // AGREGAR DISTRIBUCIÓN POR PRODUCTO
        foreach ($response['items'] as &$item) {
            $item['distribucion'] = $this->obtenerDistribucionCompra(
                $id,
                $item['producto_id']
            );
        }

    } else {

        // CABECERA GASTOS
        $sql = "SELECT g.*, a.nombre as almacen_nombre, u.nombre as usuario_nombre, 
                       gc.nombre as categoria_nombre 
                FROM gastos g 
                JOIN almacenes a ON g.almacen_id = a.id 
                JOIN usuarios u ON g.usuario_registra_id = u.id 
                LEFT JOIN gastos_categorias gc ON g.categoria_id = gc.id 
                WHERE g.id = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en SQL Gastos: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response['cabecera'] = $stmt->get_result()->fetch_assoc();

        // DETALLE GASTOS
        $sqlDet = "SELECT * FROM detalle_gasto WHERE gasto_id = ?";
        $stmtDet = $this->db->prepare($sqlDet);
        $stmtDet->bind_param("i", $id);
        $stmtDet->execute();

        $response['items'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    return $response;
}
public function obtenerDetalleCompletoPago($id) {
    

   
        // CABECERA COMPRAS
        $sql = "SELECT 
         c.id, 
           
            pcpp.monto as monto_pagado,
            pcpp.fecha_pago as fecha,
            pcpp.observaciones as observaciones,

            pcpp.metodo_pago as metodo_pago, 
            a.nombre AS almacen_nombre, 
            u.nombre AS usuario_nombre, 
            p.nombre_comercial AS proveedorNombre,
            dc.cantidad_excedente as cantidad_excedente,
            dc.precio_unitario as precio_unitario,
            pro.nombre  as producto_nombre,
            pro.unidad_medida as unidad_medida
            

            
        FROM pagos_cuentas_por_pagar pcpp

        JOIN compras c ON c.id = pcpp.compra_id
        JOIN almacenes a ON c.almacen_id = a.id 
        JOIN proveedores p ON p.id = pcpp.proveedor_id 
        JOIN detalle_compra dc ON dc.compra_id = pcpp.compra_id
        join productos pro on pro.id=dc.producto_id
        JOIN usuarios u ON u.id = pcpp.usuario_id 
        

        

        WHERE pcpp.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response= $stmt->get_result()->fetch_assoc();

       

     
    return $response;
}
public function obtenerDistribucionCompra($id, $pro_id) {

    $sql = "SELECT 
                a.nombre,
                lt.almacen_id,
                lid.lote_id,
                lt.cantidad_inicial
            FROM lotes_ingresos_detalle lid
            JOIN detalle_compra dc ON lid.detalle_compra_id = dc.id
            JOIN lotes_stock lt ON lt.id = lid.lote_id
            JOIN almacenes a ON a.id = lt.almacen_id
            WHERE dc.compra_id = ?
              AND dc.producto_id = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $id, $pro_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $response = [];

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }

    return $response;
}
public function obtenerSumaEgresos($desde, $hasta, $almacen_id = 0, $tipo_filtro = 'todos') {

    // 🔥 Formatear fechas correctamente (incluye todo el día)
    $desde_f = $desde . " 00:00:00";
    $hasta_f = date('Y-m-d', strtotime($hasta )) . " 00:00:00";

    // 🔥 Filtro almacén
    $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = ?" : "";

    // 🔥 Query unificada (compras + pagos)
    $sql = "
        SELECT SUM(sub.monto_total) AS total FROM (
            
            SELECT IFNULL(SUM(c.total), 0) AS monto_total
            FROM compras c
            WHERE c.fecha_compra >= ?
              AND c.fecha_compra < ?
              AND c.estado != 'cancelada'
              $whereAlmacen

            UNION ALL 

            SELECT IFNULL(SUM(p.monto), 0) AS monto_total
            FROM pagos_cuentas_por_pagar p
            WHERE p.fecha_pago >= ?
              AND p.fecha_pago < ?
              $whereAlmacen

        ) AS sub
    ";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error en prepare(): " . $this->db->error);
    }

    // 🔥 Parámetros (IMPORTANTE ORDEN)
    $params = [$desde_f, $hasta_f, $desde_f, $hasta_f];
    $types  = "ssss";

    if ($almacen_id > 0) {
        // Se repite 2 veces porque se usa en compras y pagos
        $types .= "ii";
        $params[] = $almacen_id;
        $params[] = $almacen_id;
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return floatval($row['total'] ?? 0);
}

public function obtenerGastosPorMetodo($desde, $hasta, $almacen_id = 0) {

    $data = [
        'EFECTIVO'      => 0,
        'TARJETA'       => 0,
        'TRANSFERENCIA' => 0
    ];

    // ✅ MISMA LÓGICA DE FECHAS
    $desde_f = $desde . " 00:00:00";
    $hasta_f = date('Y-m-d', strtotime($hasta . ' +1 day')) . " 00:00:00";

    $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = ?" : "";

    $sql = "
        SELECT metodo_pago, total
        FROM gastos
        WHERE fecha_gasto >= ?
          AND fecha_gasto < ?
          AND estado != 'cancelado'
          $whereAlmacen
    ";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        error_log("SQL ERROR: " . $this->db->error);
        return $data;
    }

    $types  = "ss";
    $params = [$desde_f, $hasta_f];

    if ($almacen_id > 0) {
        $types .= "i";
        $params[] = $almacen_id;
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $metodo = strtoupper(trim($row['metodo_pago'] ?? 'EFECTIVO'));
        $total  = (float)$row['total'];

        if (str_contains($metodo, 'EFECT')) {
            $data['EFECTIVO'] += $total;

        } elseif (str_contains($metodo, 'TARJ')) {
            $data['TARJETA'] += $total;

        } elseif (str_contains($metodo, 'TRANS')) {
            $data['TRANSFERENCIA'] += $total;

        } else {
            $data['EFECTIVO'] += $total;
        }
    }

    $result->free();
    $stmt->close();

    return $data;
}
public function obtenerComprasPorMetodo($desde, $hasta, $almacen_id = 0) {

    // 🔥 Estructura base
    $data = [
        'EFECTIVO'      => 0,
        'TARJETA'       => 0,
        'TRANSFERENCIA' => 0
    ];

    // ✅ FECHAS CORRECTAS (CLAVE)
    $desde_f = $desde . " 00:00:00";
    $hasta_f = date('Y-m-d', strtotime($hasta . ' +1 day')) . " 00:00:00";

    $whereAlmacenC = ($almacen_id > 0) ? " AND c.almacen_id = ?" : "";
    $whereAlmacenP = ($almacen_id > 0) ? " AND p.almacen_id = ?" : "";

    /* =========================
       🧾 COMPRAS
    ========================= */
    $sqlCompras = "
        SELECT c.metodo_pago, c.total AS monto
        FROM compras c
        WHERE c.fecha_compra >= ?
          AND c.fecha_compra < ?
          AND c.estado != 'cancelada'
          $whereAlmacenC
    ";

    $stmtC = $this->db->prepare($sqlCompras);

    if ($stmtC) {

        $types  = "ss";
        $params = [$desde_f, $hasta_f];

        if ($almacen_id > 0) {
            $types .= "i";
            $params[] = $almacen_id;
        }

        $stmtC->bind_param($types, ...$params);
        $stmtC->execute();

        $result = $stmtC->get_result();

        while ($row = $result->fetch_assoc()) {

            $metodo = strtoupper(trim($row['metodo_pago'] ?? 'EFECTIVO'));
            $monto  = (float)$row['monto'];

            if (str_contains($metodo, 'EFECT')) {
                $data['EFECTIVO'] += $monto;

            } elseif (str_contains($metodo, 'TARJ')) {
                $data['TARJETA'] += $monto;

            } elseif (str_contains($metodo, 'TRANS')) {
                $data['TRANSFERENCIA'] += $monto;

            } else {
                $data['EFECTIVO'] += $monto;
            }
        }

        $stmtC->close();
    }

    /* =========================
       🏦 PAGOS DE DEUDA
    ========================= */
    $sqlPagos = "
        SELECT p.metodo_pago, p.monto
        FROM pagos_cuentas_por_pagar p
        WHERE p.fecha_pago >= ?
          AND p.fecha_pago < ?
          $whereAlmacenP
    ";

    $stmtP = $this->db->prepare($sqlPagos);

    if ($stmtP) {

        $types  = "ss";
        $params = [$desde_f, $hasta_f];

        if ($almacen_id > 0) {
            $types .= "i";
            $params[] = $almacen_id;
        }

        $stmtP->bind_param($types, ...$params);
        $stmtP->execute();

        $result = $stmtP->get_result();

        while ($row = $result->fetch_assoc()) {

            $metodo = strtoupper(trim($row['metodo_pago'] ?? 'EFECTIVO'));
            $monto  = (float)$row['monto'];

            if (str_contains($metodo, 'EFECT')) {
                $data['EFECTIVO'] += $monto;

            } elseif (str_contains($metodo, 'TARJ')) {
                $data['TARJETA'] += $monto;

            } elseif (str_contains($metodo, 'TRANS')) {
                $data['TRANSFERENCIA'] += $monto;

            } else {
                $data['EFECTIVO'] += $monto;
            }
        }

        $stmtP->close();
    }

    return $data;
}
/**
 * Inserta una nueva obligación financiera vinculada a un almacén y operación específica.
 */
public function registrarObligacionFinanciera($data) {
    try {

        $sql = "INSERT INTO cuentas_por_pagar (
                    id_almacen,
                    id_proveedor,
                    beneficiario,
                    id_referencia_origen,
                    monto_total,
                    monto_pagado,
                    tipo_deuda,
                    estado,
                    fecha_vencimiento,
                    notas,
                    fecha_registro
                ) VALUES (?, ?, ?, ?, ?, 0.00, ?, 'pendiente', NULL, ?, NOW())";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en prepare: " . $this->db->error);
        }

        // Manejo correcto de NULL en proveedor
        $id_proveedor = !empty($data['id_proveedor']) ? intval($data['id_proveedor']) : null;

        // 🔥 TIPOS CORRECTOS
        // i = int
        // s = string
        // d = double
        $tipos = "iisidss";

        $stmt->bind_param(
            $tipos,
            $data['id_almacen'],            // i
            $id_proveedor,                  // i (puede ser null)
            $data['beneficiario'],          // s
            $data['id_referencia_origen'],  // i
            $data['monto_total'],           // d ✅
            $data['tipo_deuda'],            // s ✅ (VARCHAR ahora)
            $data['notas']                  // s
        );

        if (!$stmt->execute()) {
            throw new Exception("Error execute: " . $stmt->error);
        }

        return [
            "success" => true,
            "id" => $this->db->insert_id
        ];

    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    }
}
public function obtenerDetalleCompletoConProveedores($tipo, $id) {
    $response = ['tipo_documento' => $tipo];

    if ($tipo === 'compra') {
        // CABECERA COMPRAS
        $sql = "SELECT c.*, p.id AS pid, a.nombre as almacen_nombre, u.nombre as usuario_nombre 
                FROM compras c 
                JOIN almacenes a ON c.almacen_id = a.id 
                JOIN usuarios u ON c.usuario_registra_id = u.id 
                JOIN proveedores p 
    ON TRIM(LOWER(p.nombre_comercial)) = TRIM(LOWER(c.proveedor))
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response['cabecera'] = $stmt->get_result()->fetch_assoc();

        // DETALLE COMPRAS CON TRAZABILIDAD (9 y 9 en Rancho...)
        $sqlDet = "SELECT dc.*, p.sku, p.nombre as producto_nombre, p.unidad_medida, p.unidad_reporte, p.factor_conversion as factor_prod,
                    (SELECT GROUP_CONCAT(CONCAT(a.nombre, ' [', m.cantidad, ']') SEPARATOR '||')
                     FROM movimientos m 
                     JOIN almacenes a ON m.almacen_destino_id = a.id
                     WHERE m.referencia_id = dc.compra_id AND m.producto_id = dc.producto_id AND m.tipo = 'entrada') as desglose_movimientos,
                    (SELECT IFNULL(SUM(m.cantidad), 0) FROM movimientos m 
                     WHERE m.referencia_id = dc.compra_id AND m.producto_id = dc.producto_id AND m.tipo = 'entrada') as cantidad_recibida
                   FROM detalle_compra dc
                   JOIN productos p ON dc.producto_id = p.id
                   WHERE dc.compra_id = ?";
        $stmtDet = $this->db->prepare($sqlDet);
        $stmtDet->bind_param("i", $id);
        $stmtDet->execute();
        $response['items'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);

    } else {
       // --- MEJORA EN CABECERA GASTOS ---
        // Agregamos JOIN a la tabla de categorías para obtener el nombre
       $sql = "SELECT g.*, a.nombre as almacen_nombre, u.nombre as usuario_nombre, 
               gc.nombre as categoria_nombre 
        FROM gastos g 
        JOIN almacenes a ON g.almacen_id = a.id 
        JOIN usuarios u ON g.usuario_registra_id = u.id 
        LEFT JOIN gastos_categorias gc ON g.categoria_id = gc.id 
        WHERE g.id = ?";

$stmt = $this->db->prepare($sql);
if (!$stmt) throw new Exception("Error en SQL Gastos: " . $this->db->error);

$stmt->bind_param("i", $id);
$stmt->execute();
$response['cabecera'] = $stmt->get_result()->fetch_assoc();

// DETALLE GASTOS
$sqlDet = "SELECT * FROM detalle_gasto WHERE gasto_id = ?";
$stmtDet = $this->db->prepare($sqlDet);
$stmtDet->bind_param("i", $id);
$stmtDet->execute();
$response['items'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    return $response;
}
public function listarCuentasPorPagar($filtros) {

    $where = [];
    $params = [];
    $types = "";

    // 🔎 BUSCADOR
    if (!empty($filtros['busqueda'])) {
        $where[] = "(beneficiario LIKE ? OR notas LIKE ?)";
        $like = "%" . $filtros['busqueda'] . "%";
        $params[] = $like;
        $params[] = $like;
        $types .= "ss";
    }

    // 📅 FILTROS DE FECHA
    if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
        $where[] = "DATE(fecha_registro) BETWEEN ? AND ?";
        $params[] = $filtros['fecha_inicio'];
        $params[] = $filtros['fecha_fin'];
        $types .= "ss";
    }

    // 🔥 SOLO PENDIENTES
    $where[] = "estado = 'pendiente'";

    $whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

    // 📄 PAGINACIÓN
    $limit  = intval($filtros['limit'] ?? 10);
    $offset = intval($filtros['offset'] ?? 0);

    $sql = "SELECT * FROM cuentas_por_pagar
            $whereSQL
            ORDER BY fecha_registro DESC
            LIMIT ? OFFSET ?";

    $stmt = $this->db->prepare($sql);

    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 🔢 TOTAL PARA PAGINACIÓN
    $sqlTotal = "SELECT COUNT(*) as total FROM cuentas_por_pagar $whereSQL";
    $stmtTotal = $this->db->prepare($sqlTotal);

    if ($types !== "ii") {
        $stmtTotal->bind_param(substr($types, 0, -2), ...array_slice($params, 0, -2));
    }

    $stmtTotal->execute();
    $total = $stmtTotal->get_result()->fetch_assoc()['total'];

    return [
        "data" => $data,
        "total" => $total
    ];
}
public function obtenerDeudaPorCompra($id_compra)
{
    try {

        $sql = "SELECT 
                    cpp.id,
                    cpp.id_almacen,
                    p.nombre_comercial as beneficiario2,
                    cpp.beneficiario,
                    cpp.id_referencia_origen,
                    cpp.monto_total,
                    cpp.monto_pagado,
                    (cpp.monto_total - cpp.monto_pagado) AS saldo_pendiente,
                    cpp.tipo_deuda,
                    cpp.estado,
                    cpp.fecha_vencimiento,
                    cpp.notas,
                    cpp.fecha_registro,

                    a.nombre AS almacen_nombre,
                    c.folio,
                    c.fecha_compra,
                    c.total AS total_compra

                FROM cuentas_por_pagar cpp

                INNER JOIN compras c 
                    ON c.id = cpp.id_referencia_origen
                     INNER JOIN proveedores p
                    ON p.id = cpp.id_proveedor

                LEFT JOIN almacenes a 
                    ON a.id = cpp.id_almacen

                WHERE cpp.id_referencia_origen = ?
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en prepare: " . $this->db->error);
        }

        $stmt->bind_param("i", $id_compra);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        // 🔥 Si no existe deuda
        if (!$data) {
            return [
                "success" => false,
                "message" => "No existe deuda para esta compra"
            ];
        }

        return [
            "success" => true,
            "data" => $data
        ];

    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    }
}
public function pagarDeudaCompra($cuenta_id, $monto)
{
    // 1. Obtener cuenta actual
    $stmt = $this->db->prepare("
        SELECT monto_total, monto_pagado, estado
        FROM cuentas_por_pagar
        WHERE id_referencia_origen = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $cuenta_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) {
        return ['success' => false, 'message' => 'Cuenta no encontrada'];
    }

    if ($res['estado'] !== 'pendiente') {
        return ['success' => false, 'message' => 'La deuda ya está pagada'];
    }

    $nuevoPagado = $res['monto_pagado'] + $monto;
    $saldo = $res['monto_total'] - $nuevoPagado;

    // 2. Determinar estado
    $estado = ($saldo <= 0) ? 'pagado' : 'pendiente';

    // 3. Actualizar deuda
    $stmt = $this->db->prepare("
        UPDATE cuentas_por_pagar
        SET monto_pagado = ?,
            estado = ?
        WHERE id_referencia_origen = ?
    ");

    $stmt->bind_param("dsi", $nuevoPagado, $estado, $cuenta_id);

    if (!$stmt->execute()) {
        return ['success' => false, 'message' => 'Error al actualizar deuda'];
    }

    return [
        'success' => true,
        'saldo_restante' => max($saldo, 0)
    ];
}
public function registrarPagoCuentaPorPagar(
    $almacen_id,
    $proveedor_id,
    $compra_id,
    $monto,
    $metodo,
    $referencia,
    $usuario_id,
    $observaciones
) {
    try {

        $sql = "INSERT INTO pagos_cuentas_por_pagar
        (almacen_id, proveedor_id, compra_id, monto, metodo_pago, referencia_pago, fecha_pago, usuario_id, observaciones)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en prepare: " . $this->db->error);
        }

        // 🔥 valores por defecto seguros
        $referencia = !empty($referencia) ? $referencia : 'PAGO-' . time();
        $observaciones = !empty($observaciones) 
            ? $observaciones 
            : 'Pago de deuda compra #' . $compra_id . ' por $' . number_format($monto, 2);

        // 🔥 casteo seguro
        $almacen_id   = (int)$almacen_id;
        $proveedor_id = (int)$proveedor_id;
        $compra_id    = (int)$compra_id;
        $monto        = (float)$monto;
        $usuario_id   = (int)$usuario_id;

        $stmt->bind_param(
            "iiidssis",
            $almacen_id,
            $proveedor_id,
            $compra_id,
            $monto,
            $metodo,
            $referencia,
            $usuario_id,
            $observaciones
        );

        if (!$stmt->execute()) {
            throw new Exception("Error en execute: " . $stmt->error);
        }

        return [
            'success' => true,
            'pago_id' => $stmt->insert_id
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
public function cancelarDeuda($id) {

    $sql = "UPDATE cuentas_por_pagar
            SET estado = 'cancelado'
            WHERE id_referencia_origen = ?";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar query: " . $this->db->error);
    }

    $stmt->bind_param("i", $id);

    return $stmt->execute();
}
}