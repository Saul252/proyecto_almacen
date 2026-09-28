<?php
class InsumosModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }


    /**
     * Obtiene productos activos para el selector de compras
     * @param string $termino Buscador opcional para Select2 o filtros
     */public function subirDocumentoCompra($tipo, $id, $nombre_evidencia, $documento_url)
{
    $tipo_doc = '';
    $compra_id = 0;
    $gasto_id = 0;

    if ($tipo === 'gasto') {
        $tipo_doc = 'gasto';
        $gasto_id = (int)$id;
    } else {
        $tipo_doc = 'compra';
        $compra_id = (int)$id;
    }

    $sqldoc = "INSERT INTO documentos_egresos
        (tipo, compra_id, gasto_id, nombre, direccion)
        VALUES (?, ?, ?, ?, ?)";

    $stmtdoc = $this->db->prepare($sqldoc);

    if (!$stmtdoc) {
        throw new Exception("Error al preparar consulta: " . $this->db->error);
    }

    $stmtdoc->bind_param(
        "siiss",
        $tipo_doc,
        $compra_id,
        $gasto_id,
        $nombre_evidencia,
        $documento_url
    );

    if (!$stmtdoc->execute()) {
        throw new Exception("Error al guardar documento: " . $stmtdoc->error);
    }

    $documento_id = $stmtdoc->insert_id;

    $stmtdoc->close();

    return [
        'success' => true,
        'documento_id' => $documento_id,
        'message' => 'Documento guardado correctamente.'
    ];
}
public function eliminarDocumento( $id_documento) {

    $sql = "UPDATE documentos_egresos
            SET activo = 0
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("i", $id_documento);

    return $stmt->execute();
}
public function listarTodo($almacen_id = null) {
    // 1. Estructura base de la consulta
    $sql = "SELECT 
                i.*, 
                COALESCE(SUM(it.existencias), 0) AS total_existencias 
            FROM insumos i
            LEFT JOIN insumos_stock it ON it.id_insumo = i.id";
    
    // 2. Si se proporciona un almacén, agregamos el filtro WHERE
    if (!empty($almacen_id)) {
        $sql .= " WHERE it.almacen_id = ?";
    }
    
    $sql .= " GROUP BY i.id";

    // 3. Preparar la sentencia
    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        return [];
    }

    // 4. Vincular parámetros de forma segura si hay filtro de almacén
    if (!empty($almacen_id)) {
        // Se asume que $almacen_id es un entero ('i'). Cambia a 's' si es un string/UUID.
        $stmt->bind_param("i", $almacen_id);
    }

    // 5. Ejecutar y obtener resultados
    $stmt->execute();
    $result = $stmt->get_result();

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
public function obtenerExpedienteCompletoFecha($fecha_inicio = null, $fecha_fin = null,$almacen=null)
{
    $sql = "SELECT
                n.*,
                detalle.salida_id as folio,
                detalle.id AS detalle_id,
                detalle.insumo_id as insumo_id,
                detalle.cantidad as cantidad,
                detalle.compra_id as compra_id,
                tv.nombre as vehiculo,
                tv.placas as placas,
                ino.nombre AS insumo,
                ci.costo as costo,
                ma.id_mantenimiento as mantenimiento
            FROM salida_insumo n
            INNER JOIN detalle_salida_insumo detalle
                ON detalle.salida_id = n.id
            INNER JOIN insumos ino
                ON ino.id = detalle.insumo_id
            LEFT JOIN transporte_vehiculos tv
                ON tv.id = n.vehiculo_id
                 LEFT JOIN mantenimientos ma
                ON ma.id_mantenimiento =detalle.mantenimiento_id
            INNER JOIN compras_insumos ci
                ON ci.id = detalle.compra_id
            WHERE 1";

    $params = [];
    $types = "";

    if (!empty($almacen)) {
        $sql .= " AND  tv.almacen_id =?";
        $params[] = $almacen;
        $types .= "i";
    
    } if (!empty($fecha_inicio)) {
        $sql .= " AND DATE(n.fecha) >= ?";
        $params[] = $fecha_inicio;
        $types .= "s";
    }

    if (!empty($fecha_fin)) {
        $sql .= " AND DATE(n.fecha) <= ?";
        $params[] = $fecha_fin;
        $types .= "s";
    }

    $sql .= " ORDER BY n.fecha DESC";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception($this->db->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerEntregaInsumo($id)
{
    $sql = "SELECT
                n.id,
                n.fecha,
                n.usuario,
                n.vehiculo_id,

                detalle.salida_id AS folio,
                detalle.id AS detalle_id,
                detalle.compra_id,
                detalle.insumo_id,
                detalle.cantidad,
                detalle.mantenimiento_id,

                tv.nombre AS vehiculo,
                tv.placas,

                ino.nombre AS insumo,

                ci.costo,
                (ci.costo * detalle.cantidad) AS total

            FROM salida_insumo n

            INNER JOIN detalle_salida_insumo detalle
                ON detalle.salida_id = n.id

            INNER JOIN compras_insumos ci
                ON ci.id = detalle.compra_id

            INNER JOIN insumos ino
                ON ino.id = detalle.insumo_id

            LEFT JOIN transporte_vehiculos tv
                ON tv.id = n.vehiculo_id

            WHERE n.id = ?";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception($this->db->error);
    }

    $stmt->bind_param("i", $id);

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function guardarCompraCompleta($items, $folio, $proveedor, $evidencia, $almacen_id, $user_id, $metodo_pago) {
    $this->db->begin_transaction();
    try {
        // --- 1. Gestión de Evidencia ---
        $documento_url = null;
        $nombre_evidencia='';
       


        // --- 2. Totales iniciales ---
        $total_final = 0;
        $tiene_faltantes_global = 0;
        $monto_acumulado_excedentes = 0;

        foreach ($items as $item) {
            $total_final += floatval($item['total_item']);
            if (floatval($item['cantidad_faltante'] ?? 0) > 0) $tiene_faltantes_global = 1;
        }
        $tipo='compra';

        // --- 3. Insertar Cabecera de Compra ---
         $sqlC = "INSERT INTO compras 
        (folio, proveedor, fecha_compra, almacen_id, total, metodo_pago, estado, usuario_registra_id, documento_url, tiene_faltantes) 
        VALUES (?, ?, NOW(), ?, ?, ?, 'confirmada', ?, ?, ?)";

        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param("ssidsisi", $folio, $proveedor, $almacen_id, $total_final, $metodo_pago, $user_id, $documento_url, $tiene_faltantes_global);
 if (!$stmtC->execute()) { throw new Exception("Error en cabecera: " . $stmtC->error); }
        $compra_id = $stmtC->insert_id;
         if ($evidencia && $evidencia['error'] === UPLOAD_ERR_OK) {
            $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/uploads/compras/";
            if (!is_dir($ruta_carpeta)) { mkdir($ruta_carpeta, 0777, true); }
            $extension = pathinfo($evidencia['name'], PATHINFO_EXTENSION);
            $nombre_evidencia=$evidencia['name'];
            $nombre_archivo = "compra_" . preg_replace('/[^a-zA-Z0-9]/', '_', $folio) . "_" . time() . "." . $extension;
            $ruta_destino = $ruta_carpeta . $nombre_archivo;
            if (move_uploaded_file($evidencia['tmp_name'], $ruta_destino)) {
                $documento_url = "uploads/compras/" . $nombre_archivo;
            }
        }
      if ($documento_url) {

    $tipo_doc = 'compra';
    $gasto_id = 0;

    $sqldoc = "INSERT INTO documentos_egresos
    (tipo, compra_id, gasto_id, nombre, direccion)
    VALUES (?, ?, ?, ?, ?)";

    $stmtdoc = $this->db->prepare($sqldoc);

    $stmtdoc->bind_param(
        "siiss",
        $tipo_doc,
        $compra_id,
        $gasto_id,
        $nombre_evidencia,
        $documento_url
    );

    if (!$stmtdoc->execute()) {
        throw new Exception("Error al guardar documento: " . $stmtdoc->error);
    }
}
        // --- 4. Procesar Items ---
        foreach ($items as $item) {

            $p_id = intval($item['producto_id']);
            $factor = floatval($item['hidden_factor'] ?? 1);
            $cant_fac = (floatval($item['input_mayoreo'] ?? 0) * $factor) + floatval($item['input_sueltas'] ?? 0);
            $cant_fal = floatval($item['cantidad_faltante'] ?? 0);
            $cant_exe = floatval($item['cantidad_excedente'] ?? 0); 
            
            $subtotal = floatval($item['total_item']);
            $precio_lote = floatval($item['precio_lote'] ?? 0); 
           
            $estado_e = ($cant_fal > 0) ? 'incompleto' : (($cant_exe > 0) ? 'excedente' : 'completo');

            $sumaTotal = 0;
            foreach ($item['almacenes'] as $dist) {
                $sumaTotal += floatval($dist['cantidad']);
            }
            $cantidad_real2=($sumaTotal - $cant_exe);

            $cantidad_real = ($sumaTotal - $cant_exe)+$cant_fal;
            if ($cant_exe > $sumaTotal) {
    error_log("Excedente inválido en producto $p_id");
}
           $unitary_price=floatval( $precio_lote/$cantidad_real);
           // Validaciones seguras
$unidad_compra = $item['unidad_compra'] ?? 'PZA';
$factor_conversion = floatval($item['hidden_factor'] ?? 1);

// 🔥 Cálculo correcto (evita división por 0)
$cantidad_real = $sumaTotal - $cant_exe;
if ($cantidad_real <= 0) {
    $cantidad_real = $sumaTotal; // fallback seguro
}

// 🔥 Precio unitario REAL correcto
$precio_unitario = ($cantidad_real > 0) 
    ? ($subtotal / ($cantidad_real+$cant_fal)) 
    : 0;

            // 🔥 AJUSTE AQUÍ (único cambio real)
            if ($cant_exe > 0) {
                
                $monto_acumulado_excedentes += ($cant_exe * $precio_unitario);
            }

         // --- 5. Insertar Detalle Histórico ---
$sqlD = "INSERT INTO detalle_compra 
(
    compra_id, 
    producto_id, 
    cantidad, 
    unidad_compra, 
    factor_conversion, 
    cantidad_faltante, 
    cantidad_excedente, 
    precio_unitario, 
    estado_entrega, 
    subtotal
) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmtD = $this->db->prepare($sqlD);


$cantidad_total=($cantidad_real+$cant_fal);
$stmtD->bind_param(
    "iidsddddsd",
    $compra_id,
    $p_id,
    $cantidad_total,
    $unidad_compra,
    $factor_conversion,
    $cant_fal,
    $cant_exe,
    $precio_unitario,
    $estado_e,
    $subtotal
);

$stmtD->execute();
$detalle_id = $stmtD->insert_id;

            // --- 6. Registrar Faltante Pendiente ---
            if ($cant_fal > 0) {
                $sqlF = "INSERT INTO faltantes_ingreso (compra_id, producto_id, cantidad_pendiente) VALUES (?, ?, ?)";
                $stmtF = $this->db->prepare($sqlF);
                $stmtF->bind_param("iid", $compra_id, $p_id, $cant_fal);
                $stmtF->execute();
            }
           

            // --- 7. Inventario, MOVIMIENTOS Y LOTES ---
           if (isset($item['almacenes'])) {
                foreach ($item['almacenes'] as $id_alm_dest => $dist) {
                    if (isset($dist['activo']) && $dist['activo'] === 'on') {
                        $cant_reparto = floatval($dist['cantidad']);
                        if ($cant_reparto <= 0) continue;

                        $sqlI = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                                 VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                        $stmtI = $this->db->prepare($sqlI);
                        $stmtI->bind_param("iid", $id_alm_dest, $p_id, $cant_reparto);
                        $stmtI->execute();

                        $codigo_lote = "LOTE-" . $compra_id . "-" . $p_id . "-" . $id_alm_dest;
                        $sqlL = "INSERT INTO lotes_stock (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                                 VALUES (?, ?, ?, ?, ?, ?, 'activo')";
                        $stmtL = $this->db->prepare($sqlL);
                        $stmtL->bind_param("iisddd", $p_id, $id_alm_dest, $codigo_lote, $cant_reparto, $cant_reparto, $precio_unitario);
                        $stmtL->execute();
                        $lote_id = $stmtL->insert_id;

                        $sqlLI = "INSERT INTO lotes_ingresos_detalle 
                                  (lote_id, detalle_compra_id, cantidad_recibida, costo_aplicado) 
                                  VALUES (?, ?, ?, ?)";
                        $stmtLI = $this->db->prepare($sqlLI);
                        $stmtLI->bind_param("iidd", $lote_id, $detalle_id, $cant_reparto, $subtotal);
                        $stmtLI->execute();

                        $sqlM = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                                 VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
                        $stmtM = $this->db->prepare($sqlM);
                        $obs = "Compra Folio: $folio (Lote: $codigo_lote)";
                        $stmtM->bind_param("idiiis", $p_id, $cant_reparto, $id_alm_dest, $user_id, $compra_id, $obs);
                        $stmtM->execute();
                    }
                }
            }
             if ($cant_exe > 0) {

            $dataObligacion = [
                'id_almacen'           => $almacen_id,
                'id_proveedor'         => $proveedor,
                'beneficiario'         => "Proveedor ID: " . $proveedor,
                'id_referencia_origen' => $compra_id,
                'monto_total'          => $monto_acumulado_excedentes,
                'tipo_deuda'           => 'excedente_compra',
                'notas'                => "Deuda generada por material excedente en Compra Folio: $folio"
            ];

            $resObligacion = $this->registrarObligacionFinanciera($dataObligacion);

            if (!$resObligacion['success']) {
                throw new Exception("Compra guardada pero falló obligación: " . $resObligacion['message']);
            }
        }
        }

        // --- REGISTRAR OBLIGACIÓN ---
       

        $this->db->commit();
        return ['success' => true, 'message' => 'Compra procesada y deuda por excedente registrada.'];

    } catch (Exception $e) {
        $this->db->rollback();
        if (isset($ruta_destino) && file_exists($ruta_destino)) { unlink($ruta_destino); }
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

}