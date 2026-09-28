<?php
class CompraModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

 public function obtenerProductosAlmacen( $almacen_id = 0) {
    // 1. Consulta base con JOIN a la tabla almacenes
    $sql = "SELECT 
                p.id, 
                p.sku, 
                p.nombre, 
                p.unidad_medida,
                p.unidad_reporte, 
                p.factor_conversion, 
                p.precio_adquisicion AS precio,
                p.almacen_id,
                a.nombre AS nombre_almacen
            FROM productos p
            LEFT JOIN almacenes a ON p.almacen_id = a.id
            WHERE p.activo = 1";
    
    $params = [];
    $types = "";

    // 2. Filtro dinámico por almacén (si es mayor a 0)
    if ($almacen_id > 0) {
        $sql .= " AND p.almacen_id = ?";
        $params[] = (int)$almacen_id;
        $types .= "i";
    }

    // 3. Filtro dinámico por término de búsqueda (Select2 / Búsqueda rápida)
  

    // 4. Preparación y ejecución dinámica según los parámetros enviados
    if (!empty($params)) {
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $this->db->query($sql);
    }

    // 5. Retorno de resultados
    $productos = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    
    return $productos;
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
/**
 * Actualiza únicamente la cantidad excedente de un producto en el detalle de una compra.
 *
 * @param int   $compra_id   ID de la compra.
 * @param int   $producto_id ID del producto.
 * @param float $excedente   Nueva cantidad excedente a registrar.
 * @return bool              true en caso de éxito, false en caso de falla.
 */public function actualizarExcedenteProducto($compra_id, $producto_id, $excedente, $user_id = 1) {
    try {
        // 1. Iniciar transacción
        $this->db->begin_transaction();

        // 2. Obtener datos clave de la compra y del detalle del producto
        $sqlInfo = "SELECT 
                        c.folio, 
                        c.proveedor, 
                        c.almacen_id AS almacen_compra,
                        dc.id AS detalle_compra_id, 
                        dc.precio_unitario, 
                        dc.subtotal
                    FROM compras c
                    INNER JOIN detalle_compra dc ON dc.compra_id = c.id
                    WHERE c.id = ? AND dc.producto_id = ?
                    LIMIT 1";

        $stmtInfo = $this->db->prepare($sqlInfo);
        if (!$stmtInfo) {
            throw new Exception("Error al preparar la consulta de compra: " . $this->db->error);
        }

        $stmtInfo->bind_param("ii", $compra_id, $producto_id);
        $stmtInfo->execute();
        $resInfo = $stmtInfo->get_result();

        if ($resInfo->num_rows === 0) {
            $stmtInfo->close();
            throw new Exception("No se encontró el registro de la compra {$compra_id} o el producto {$producto_id}.");
        }

        $info = $resInfo->fetch_assoc();
        $stmtInfo->close();

        // Asignación de variables necesarias
        $alm_id          =  intval($info['almacen_compra']);
        $p_id            = intval($producto_id);
        $cantidad        = floatval($excedente);
        $id_dc           = intval($info['detalle_compra_id']);
        $precio_unitario = floatval($info['precio_unitario']);
        $subtotal        = floatval($info['subtotal']);
        $folio           = $info['folio'];
        $proveedor       = $info['proveedor'];

        // 3. Actualizar la cantidad excedente en el detalle de la compra
        $sqlDetalle = "UPDATE detalle_compra 
                       SET cantidad_excedente = ?
                       WHERE id = ?";

        $stmtDetalle = $this->db->prepare($sqlDetalle);
        if (!$stmtDetalle) {
            throw new Exception("Error al preparar UPDATE de excedente: " . $this->db->error);
        }

        $stmtDetalle->bind_param("di", $cantidad, $id_dc);
        if (!$stmtDetalle->execute()) {
            $stmtDetalle->close();
            throw new Exception("Error al actualizar la cantidad excedente.");
        }
        $stmtDetalle->close();

        // A. Actualizar Inventario (Suma en el almacén destino)
        $sqlInv = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                   VALUES (?, ?, ?) 
                   ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
        $stmtInv = $this->db->prepare($sqlInv);
        if (!$stmtInv) {
            throw new Exception("Error al preparar la actualización de inventario: " . $this->db->error);
        }
        $stmtInv->bind_param("iid", $alm_id, $p_id, $cantidad);
        if (!$stmtInv->execute()) {
            $stmtInv->close();
            throw new Exception("Error al impactar el stock en inventario.");
        }
        $stmtInv->close();

        // Crear registro en Lotes Stock
        $codigo_lote = "LOTE-EX-" . $compra_id . "-" . $p_id . "-" . $alm_id;

        $sqlL = "INSERT INTO lotes_stock 
                (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo')";

        $stmtL = $this->db->prepare($sqlL);
        if (!$stmtL) {
            throw new Exception("Error al preparar la creación de lote: " . $this->db->error);
        }
        $stmtL->bind_param("iisddd", $p_id, $alm_id, $codigo_lote, $cantidad, $cantidad, $precio_unitario);
        if (!$stmtL->execute()) {
            $stmtL->close();
            throw new Exception("Error al insertar en lotes_stock.");
        }

        $lote_id = $stmtL->insert_id;
        $stmtL->close();

        // Relación lote-detalle
        $sqlLI = "INSERT INTO lotes_ingresos_detalle 
                  (lote_id, detalle_compra_id, cantidad_recibida, costo_aplicado) 
                  VALUES (?, ?, ?, ?)";
        $stmtLI = $this->db->prepare($sqlLI);
        if (!$stmtLI) {
            throw new Exception("Error al preparar detalle de lote ingreso: " . $this->db->error);
        }
        $stmtLI->bind_param("iidd", $lote_id, $id_dc, $cantidad, $subtotal);
        if (!$stmtLI->execute()) {
            $stmtLI->close();
            throw new Exception("Error al registrar relacion lote-ingreso.");
        }
        $stmtLI->close();

        // B. Registrar Movimiento (Kardex)
        $obs = "Entrada Excedente (Compra: {$folio})";
        $sqlK = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                 VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
        $stmtK = $this->db->prepare($sqlK);
        if (!$stmtK) {
            throw new Exception("Error al preparar registro en movimientos: " . $this->db->error);
        }
        $stmtK->bind_param("idiiis", $p_id, $cantidad, $alm_id, $user_id, $compra_id, $obs);
        if (!$stmtK->execute()) {
            $stmtK->close();
            throw new Exception("Error al registrar el movimiento en Kardex.");
        }
        $stmtK->close();

        // C. Generar la obligación financiera por el excedente
        $dataObligacion = [
            'id_almacen'           => $alm_id,
            'id_proveedor'         => $proveedor,
            'beneficiario'         => "Proveedor ID: " . $proveedor,
            'id_referencia_origen' => $compra_id,
            'monto_total'          => ($cantidad * $precio_unitario),
            'tipo_deuda'           => 'excedente_compra',
            'notas'                => "Deuda generada por material excedente en Compra Folio: " . $folio
        ];

        if (method_exists($this, 'registrarObligacionFinanciera')) {
            $resObligacion = $this->registrarObligacionFinanciera($dataObligacion);
            if (!$resObligacion || empty($resObligacion['success'])) {
                $msg = $resObligacion['message'] ?? 'Error al registrar la obligación financiera.';
                throw new Exception($msg);
            }
        }

        // Confirmar transacción
        $this->db->commit();
        return true;

    } catch (Exception $e) {
        // Revertir ante cualquier fallo
        $this->db->rollback();
        error_log("Error en actualizarExcedenteProducto: " . $e->getMessage());
        return false;
    }
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
public function guardarCompraCompletaDesdeCompras($items, $folio, $proveedor, $evidencia, $almacen_id, $user_id, $metodo_pago) {
    $this->db->begin_transaction();
    try {

        // --- 1. Evidencia ---
        $documento_url = null;
        if ($evidencia && $evidencia['error'] === UPLOAD_ERR_OK) {
            $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/uploads/compras/";
            if (!is_dir($ruta_carpeta)) { mkdir($ruta_carpeta, 0777, true); }

            $extension = pathinfo($evidencia['name'], PATHINFO_EXTENSION);
            $nombre_archivo = "compra_" . preg_replace('/[^a-zA-Z0-9]/', '_', $folio) . "_" . time() . "." . $extension;
            $ruta_destino = $ruta_carpeta . $nombre_archivo;

            if (move_uploaded_file($evidencia['tmp_name'], $ruta_destino)) {
                $documento_url = "uploads/compras/" . $nombre_archivo;
            }
        }

        // --- 2. Totales ---
        $total_final = 0;
        $tiene_faltantes_global = 0;
        $monto_acumulado_excedentes = 0;

        foreach ($items as $item) {
            $total_final += floatval($item['total_item']);
            if (floatval($item['cantidad_faltante'] ?? 0) > 0) {
                $tiene_faltantes_global = 1;
            }
        }

        // --- 3. Cabecera ---
        $sqlC = "INSERT INTO compras 
        (folio, proveedor, fecha_compra, almacen_id, total, metodo_pago, estado, usuario_registra_id, documento_url, tiene_faltantes) 
        VALUES (?, ?, NOW(), ?, ?, ?, 'confirmada', ?, ?, ?)";

        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param("ssidsisi", $folio, $proveedor, $almacen_id, $total_final, $metodo_pago, $user_id, $documento_url, $tiene_faltantes_global);

        if (!$stmtC->execute()) {
            throw new Exception("Error en cabecera: " . $stmtC->error);
        }

        $compra_id = $stmtC->insert_id;

        // --- 4. Items ---
        foreach ($items as $item) {

            $p_id = intval($item['producto_id']);
            $factor = floatval($item['hidden_factor'] ?? 1);

            $cant_fac = (floatval($item['input_mayoreo'] ?? 0) * $factor) + floatval($item['input_sueltas'] ?? 0);
            $cant_fal = floatval($item['cantidad_faltante'] ?? 0);
            $cant_exe = floatval($item['cantidad_excedente'] ?? 0);

            $subtotal = floatval($item['total_item']);

            $estado_e = ($cant_fal > 0)
                ? 'incompleto'
                : (($cant_exe > 0) ? 'excedente' : 'completo');

            // 🔹 TOTAL REAL (NO restar excedente)
            $sumaTotal = 0;
            foreach ($item['almacenes'] as $dist) {
                $sumaTotal += floatval($dist['cantidad']);
            }

            $cantidad_real = $sumaTotal;

            // 🔹 Precio unitario correcto
            $precio_unitario = ($cantidad_real > 0)
                ? ($subtotal / $cantidad_real)
                : 0;

            // 🔹 Registrar costo de excedente
            if ($cant_exe > 0) {
                $monto_acumulado_excedentes += ($cant_exe * $precio_unitario);
            }

            // --- 5. Detalle ---
            $sqlD = "INSERT INTO detalle_compra 
            (compra_id, producto_id, cantidad, unidad_compra, factor_conversion, cantidad_faltante, cantidad_excedente, precio_unitario, estado_entrega, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmtD = $this->db->prepare($sqlD);

            $unidad_compra = $item['unidad_compra'] ?? 'PZA';
            $factor_conversion = $factor;

            $stmtD->bind_param(
                "iidsddddsd",
                $compra_id,
                $p_id,
                $cantidad_real,
                $unidad_compra,
                $factor_conversion,
                $cant_fal,
                $cant_exe,
                $precio_unitario,
                $estado_e,
                $subtotal
            );

            if (!$stmtD->execute()) {
                throw new Exception("Error en detalle: " . $stmtD->error);
            }

            $detalle_id = $stmtD->insert_id;

            // --- 6. Faltantes ---
            if ($cant_fal > 0) {
                $sqlF = "INSERT INTO faltantes_ingreso (compra_id, producto_id, cantidad_pendiente) VALUES (?, ?, ?)";
                $stmtF = $this->db->prepare($sqlF);
                $stmtF->bind_param("iid", $compra_id, $p_id, $cant_fal);
                $stmtF->execute();
            }

            // --- 7. Inventario ---
            if (isset($item['almacenes'])) {
                foreach ($item['almacenes'] as $id_alm_dest => $dist) {
                    if (isset($dist['activo']) && $dist['activo'] === 'on') {

                        $cant_reparto = floatval($dist['cantidad']);
                        if ($cant_reparto <= 0) continue;

                        // Inventario
                        $sqlI = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                                 VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                        $stmtI = $this->db->prepare($sqlI);
                        $stmtI->bind_param("iid", $id_alm_dest, $p_id, $cant_reparto);
                        $stmtI->execute();

                        // Lotes
                        $codigo_lote = "LOTE-" . $compra_id . "-" . $p_id . "-" . $id_alm_dest;

                        $sqlL = "INSERT INTO lotes_stock 
                        (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                        VALUES (?, ?, ?, ?, ?, ?, 'activo')";

                        $stmtL = $this->db->prepare($sqlL);
                        $stmtL->bind_param("iisddd", $p_id, $id_alm_dest, $codigo_lote, $cant_reparto, $cant_reparto, $precio_unitario);
                        $stmtL->execute();

                        $lote_id = $stmtL->insert_id;

                        // Relación lote-detalle
                        $sqlLI = "INSERT INTO lotes_ingresos_detalle 
                                  (lote_id, detalle_compra_id, cantidad_recibida, costo_aplicado) 
                                  VALUES (?, ?, ?, ?)";
                        $stmtLI = $this->db->prepare($sqlLI);
                        $stmtLI->bind_param("iidd", $lote_id, $detalle_id, $cant_reparto, $subtotal);
                        $stmtLI->execute();

                        // Movimiento
                        $sqlM = "INSERT INTO movimientos 
                        (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                        VALUES (?, 'entrada', ?, ?, ?, ?, ?)";

                        $stmtM = $this->db->prepare($sqlM);
                        $obs = "Compra Folio: $folio (Lote: $codigo_lote)";
                        $stmtM->bind_param("idiiis", $p_id, $cant_reparto, $id_alm_dest, $user_id, $compra_id, $obs);
                        $stmtM->execute();
                    }
                }
            }
        }

        // --- 8. Obligación por excedente ---
        if ($monto_acumulado_excedentes > 0) {

            $dataObligacion = [
                'id_almacen' => $almacen_id,
                'id_proveedor' => $proveedor,
                'beneficiario' => "Proveedor ID: " . $proveedor,
                'id_referencia_origen' => $compra_id,
                'monto_total' => $monto_acumulado_excedentes,
                'tipo_deuda' => 'excedente_compra',
                'notas' => "Deuda generada por excedente en Compra Folio: $folio"
            ];

            $resObligacion = $this->registrarObligacionFinanciera($dataObligacion);

            if (!$resObligacion['success']) {
                throw new Exception("Error en obligación: " . $resObligacion['message']);
            }
        }

        $this->db->commit();

        return [
            'success' => true,
            'message' => 'Compra procesada correctamente.'
        ];

    } catch (Exception $e) {
        $this->db->rollback();

        if (isset($ruta_destino) && file_exists($ruta_destino)) {
            unlink($ruta_destino);
        }

        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

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
   public function obtenerProductos($termino = '') {
        $sql = "SELECT 
                    id, 
                    sku, 
                    nombre, 
                    unidad_medida,
                    unidad_reporte, 
                    factor_conversion, 
                    precio_adquisicion as precio 
                FROM productos 
                WHERE activo = 1";
        
        // Si hay un término de búsqueda (útil para Select2)
        if (!empty($termino)) {
            $sql .= " AND (nombre LIKE ? OR sku LIKE ?)";
            $stmt = $this->db->prepare($sql);
            $busqueda = "%$termino%";
            $stmt->bind_param("ss", $busqueda, $busqueda);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        return $productos;
    }
public function aplicarFaltantesCompra($compra_id) {
    $this->db->begin_transaction();

    try {
        /**
         * PASO 1: AJUSTE FÍSICO (CANTIDADES)
         * Restamos el faltante a la cantidad original para dejar el dato real.
         */
        $sql1 = "
            UPDATE detalle_compra 
            SET cantidad = (cantidad - cantidad_faltante)
            WHERE compra_id = ? AND cantidad_faltante > 0
        ";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->bind_param("i", $compra_id);
        $stmt1->execute();


        /**
         * PASO 2: AJUSTE FINANCIERO Y ESTADOS
         * Ahora que 'cantidad' ya es la correcta, actualizamos el dinero.
         */
        $sql2 = "
            UPDATE detalle_compra 
            SET 
                subtotal = cantidad * precio_unitario,
                cantidad_faltante = 0,
                estado_entrega = 'completo'
            WHERE compra_id = ?
        ";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->bind_param("i", $compra_id);
        $stmt2->execute();


        /**
         * PASO 3: SINCRONIZACIÓN DE CABECERA
         * Llevamos la suma de subtotales a la tabla 'compras'.
         */
        $sql3 = "
            UPDATE compras c
            SET c.total = (
                SELECT IFNULL(SUM(subtotal), 0)
                FROM detalle_compra 
                WHERE compra_id = c.id
            )
            WHERE c.id = ?
        ";
        $stmt3 = $this->db->prepare($sql3);
        $stmt3->bind_param("i", $compra_id);
        $stmt3->execute();


        /**
         * PASO 4: LIMPIEZA FINAL
         */
        $sql4 = "DELETE FROM faltantes_ingreso WHERE compra_id = ?";
        $stmt4 = $this->db->prepare($sql4);
        $stmt4->bind_param("i", $compra_id);
        $stmt4->execute();


        $this->db->commit();

        return [
            "success" => true,
            "message" => "Proceso completado en dos pasos: Inventario y Finanzas sincronizados."
        ];

    } catch (Exception $e) {
        $this->db->rollback();
        return [
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ];
    }
}
    public function obtenerDetalleFaltantes($compra_id) {
    $sql = "SELECT 
                f.producto_id, 
                f.cantidad_pendiente, 
                p.nombre,p.factor_conversion,p.unidad_medida, p.unidad_reporte
            FROM faltantes_ingreso f
            INNER JOIN productos p ON f.producto_id = p.id
            WHERE f.compra_id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $compra_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
/**
 * Procesa el ingreso físico de productos que estaban marcados como faltantes.
 * Realiza la afectación triple: Inventario, Pendientes y Auditoría de Compra.
 */
public function procesarAjusteFaltante($compra_id, $distribucion, $user_id) {
    $this->db->begin_transaction();
    try {
        // 1. Obtener Folio para el historial (Kardex)
        $sqlC = "SELECT folio FROM compras WHERE id = ?";
        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param("i", $compra_id);
        $stmtC->execute();
         $resC = $stmtC->get_result()->fetch_assoc();
        $folio = $resC['folio'] ?? 'S/F';
        

        foreach ($distribucion as $p_id => $almacenes) {
             $sqldc = "SELECT id, precio_unitario ,subtotal
              FROM detalle_compra 
              WHERE compra_id = ? AND producto_id = ?";

    $stmtdc = $this->db->prepare($sqldc);
    $stmtdc->bind_param("ii", $compra_id, $p_id);
    $stmtdc->execute();

    $resdc = $stmtdc->get_result()->fetch_assoc();

    $id_dc = $resdc['id'] ?? 0;
    $precio_unitario = $resdc['precio_unitario'] ?? 0;

    $subtotal = $resdc['subtotal'] ?? 0;

    $total_recibido_producto = 0;

            foreach ($almacenes as $alm_id => $cantidad) {
                $cantidad = floatval($cantidad);
                if ($cantidad <= 0) continue; // Si el switch estaba ON pero la cantidad es 0, ignoramos

                $total_recibido_producto += $cantidad;


                // A. Actualizar Inventario (Suma en el almacén destino seleccionado)
                $sqlInv = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                           VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                $stmtInv = $this->db->prepare($sqlInv);
                $stmtInv->bind_param("iid", $alm_id, $p_id, $cantidad);

                        $codigo_lote = "LOTE-A-" . $compra_id . "-" . $p_id . "-" . $alm_id;

                        $sqlL = "INSERT INTO lotes_stock 
                        (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                        VALUES (?, ?, ?, ?, ?, ?, 'activo')";

                        $stmtL = $this->db->prepare($sqlL);
                        $stmtL->bind_param("iisddd", $p_id, $alm_id, $codigo_lote, $cantidad, $cantidad, $precio_unitario);
                        $stmtL->execute();

                        $lote_id = $stmtL->insert_id;

                        // Relación lote-detalle
                        $sqlLI = "INSERT INTO lotes_ingresos_detalle 
                                  (lote_id, detalle_compra_id, cantidad_recibida, costo_aplicado) 
                                  VALUES (?, ?, ?, ?)";
                        $stmtLI = $this->db->prepare($sqlLI);
                        $stmtLI->bind_param("iidd", $lote_id, $id_dc, $cantidad, $subtotal);
                        $stmtLI->execute();

                $stmtInv->execute();

                // B. Registrar Movimiento (Kardex)
                $obs = "Entrada Faltante (Compra: $folio)";
                $sqlK = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                         VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
                $stmtK = $this->db->prepare($sqlK);
                $stmtK->bind_param("idiiis", $p_id, $cantidad, $alm_id, $user_id, $compra_id, $obs);
                $stmtK->execute();
            }

            // C. Si hubo ingresos para este producto, descontamos de los saldos pendientes
            if ($total_recibido_producto > 0) {
                // Descontar de la tabla operativa de faltantes
                $this->db->query("UPDATE faltantes_ingreso 
                                  SET cantidad_pendiente = cantidad_pendiente - $total_recibido_producto 
                                  WHERE compra_id = $compra_id AND producto_id = $p_id");

                // Descontar del detalle histórico de la compra
                $this->db->query("UPDATE detalle_compra 
                                  SET cantidad_faltante = cantidad_faltante - $total_recibido_producto 
                                  WHERE compra_id = $compra_id AND producto_id = $p_id");
            }
        }

        // 3. LIMPIEZA: Eliminar registros de pendientes que ya llegaron a 0
        $this->db->query("DELETE FROM faltantes_ingreso WHERE cantidad_pendiente <= 0");

        // 4. ACTUALIZAR CABECERA: Si ya no queda NADA pendiente de esta compra, marcar como finalizada
        $check = $this->db->query("SELECT COUNT(*) as total FROM faltantes_ingreso WHERE compra_id = $compra_id");
        if ($check->fetch_assoc()['total'] == 0) {
            $this->db->query("UPDATE compras SET tiene_faltantes = 0 WHERE id = $compra_id");
        }

        $this->db->commit();
        return ['success' => true, 'message' => 'Distribución de faltantes procesada con éxito.'];

    } catch (Exception $e) {
        $this->db->rollback();
        return ['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()];
    }
}
public function generarSiguienteFolio($almacen=1) {
    // 1. Buscamos el valor máximo convirtiendo el texto a número
    // Usamos COALESCE para que si la tabla está vacía, nos devuelva 0
    $sql = "SELECT COALESCE(MAX(CAST(folio AS UNSIGNED)), 0) AS ultimo_folio FROM compras where almacen_id=$almacen";
    
    $resultado = $this->db->query($sql);
    
    if ($resultado) {
        $fila = $resultado->fetch_assoc();
        $ultimoId = intval($fila['ultimo_folio']);
        
        // 2. Retornamos el siguiente número
        return $ultimoId + 1;
    }
    
    return 1; // Por si ocurre un error de conexión, empezamos en 1
}
public function cancelarCompra($id_compra, $id_usuario) {
    $this->db->begin_transaction();
    try {
        // 1. Obtener datos básicos de la compra para el Kardex
        $q = $this->db->prepare("SELECT folio, almacen_id FROM compras WHERE id = ? FOR UPDATE");
        $q->bind_param("i", $id_compra);
        $q->execute();
        $compra = $q->get_result()->fetch_assoc();
        
        if (!$compra) throw new Exception("Compra no encontrada.");

        // 2. OBTENER INFORMACIÓN DE LOS LOTES ANTES DE ELIMINARLOS
        // Buscamos cuánto stock real hay en los lotes que generó esta compra
        $sqlLotes = "SELECT ls.producto_id, ls.almacen_id, ls.cantidad_actual 
                     FROM lotes_stock ls
                     INNER JOIN lotes_ingresos_detalle lid ON ls.id = lid.lote_id
                     INNER JOIN detalle_compra dc ON lid.detalle_compra_id = dc.id
                     WHERE dc.compra_id = ?";
        
        $stmtLotes = $this->db->prepare($sqlLotes);
        $stmtLotes->bind_param("i", $id_compra);
        $stmtLotes->execute();
        $resLotes = $stmtLotes->get_result();

        // 3. PROCESAR EL DESCUENTO BASADO EN LOS DATOS EXTRAÍDOS
        while ($lote = $resLotes->fetch_assoc()) {
            $p_id = $lote['producto_id'];
            $a_id = $lote['almacen_id'];
            $cantidad_revertir = $lote['cantidad_actual'];

            // A. Descontar del inventario usando los IDs exactos que venían en el lote
            $upd = $this->db->prepare("UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?");
            $upd->bind_param("dii", $cantidad_revertir, $p_id, $a_id);
            $upd->execute();

            // B. Registrar Movimiento en Kardex
            $sqlMov = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                       VALUES (?, 'ajuste', ?, ?, ?, ?, ?)";
            $stmtMov = $this->db->prepare($sqlMov);
            $obs = "REVERSIÓN POR CANCELACIÓN - COMPRA: " . $compra['folio'];
            $stmtMov->bind_param("idiiis", $p_id, $cantidad_revertir, $a_id, $id_usuario, $id_compra, $obs);
            $stmtMov->execute();
        }

        // 4. LIMPIEZA FINAL: Ahora sí borramos los lotes y cambiamos el estado
        // Borramos los lotes asociados
        $this->db->query("DELETE FROM lotes_stock WHERE id IN (
            SELECT lote_id FROM lotes_ingresos_detalle lid 
            JOIN detalle_compra dc ON lid.detalle_compra_id = dc.id 
            WHERE dc.compra_id = $id_compra
        )");

        // Cambiamos el estado de la compra
        $updEstado = $this->db->prepare("UPDATE compras SET estado = 'cancelada' WHERE id = ?");
        $updEstado->bind_param("i", $id_compra);
        $updEstado->execute();

        $this->db->commit();
        return ['success' => true, 'message' => "Stock sincronizado y compra anulada correctamente."];

    } catch (Exception $e) {
        $this->db->rollback();
        return ['success' => false, 'message' => "Error al cancelar: " . $e->getMessage()];
    }
}
}