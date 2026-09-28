<?php
// 1. Configuración de cabeceras y conexión
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../../config/conexion.php';

// 2. Validar parámetros
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

if ($id <= 0 || empty($tipo)) {
    echo '<div class="alert alert-warning">Datos insuficientes para mostrar el detalle.</div>';
    exit;
}

// --- BOTÓN DE IMPRESIÓN ---
echo '<div class="d-flex justify-content-end mb-3">';
echo '  <a href="/myvet/app/backend/compras/imprimir_egreso.php?id='.$id.'&tipo='.$tipo.'" 
           target="_blank" 
           class="btn btn-primary btn-sm shadow-sm px-3">
           <i class="bi bi-printer-fill me-2"></i>IMPRIMIR COMPROBANTE
        </a>';
echo '</div>';

try {
    if ($tipo === 'compra') {
        // --- LÓGICA PARA COMPRAS ---
        $sqlC = "SELECT c.*, u.nombre as usuario, a.nombre as almacen_nombre 
                 FROM compras c 
                 LEFT JOIN usuarios u ON c.usuario_registra_id = u.id 
                 LEFT JOIN almacenes a ON c.almacen_id = a.id
                 WHERE c.id = ?";
        $stmtC = $conexion->prepare($sqlC);
        $stmtC->bind_param("i", $id);
        $stmtC->execute();
        $compra = $stmtC->get_result()->fetch_assoc();

        if (!$compra) throw new Exception("Compra no encontrada.");

        $sqlD = "SELECT d.*, p.nombre as producto_nombre, p.sku 
                 FROM detalle_compra d 
                 JOIN productos p ON d.producto_id = p.id 
                 WHERE d.compra_id = ?";
        $stmtD = $conexion->prepare($sqlD);
        $stmtD->bind_param("i", $id);
        $stmtD->execute();
        $detalle = $stmtD->get_result();

        // Cabecera visual
        echo '<div class="row mb-3 shadow-sm p-3 bg-light rounded border-start border-primary border-4 mx-0">';
        echo '  <div class="col-md-4"><b>Folio:</b> <span class="text-primary">' . $compra['folio'] . '</span></div>';
        echo '  <div class="col-md-4 text-center"><b>Almacén Principal:</b> ' . htmlspecialchars($compra['almacen_nombre'] ?? 'N/A') . '</div>';
        echo '  <div class="col-md-4 text-end"><b>Fecha:</b> ' . date('d/m/Y', strtotime($compra['fecha_compra'])) . '</div>';
        echo '  <div class="col-md-12 mt-2"><b>Proveedor:</b> ' . htmlspecialchars($compra['proveedor']) . '</div>';
        echo '</div>';

        echo '<div class="table-responsive">
                <table class="table table-sm table-hover border">
                   <thead class="table-dark">
                        <tr>
                            <th>Producto / SKU</th>
                            <th class="text-center">Cant. Facturada</th>
                            <th class="text-center">U. en Almacén</th> 
                            <th class="text-center">Faltante Factura</th>
                            <th class="text-center">Esperado (PZ)</th> 
                            <th class="text-end">Precio U.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        while ($item = $detalle->fetch_assoc()) {
            $factor = $item['factor_conversion'] ?? 1;
            
            // 1. Cálculo de lo que la factura dice que llegó (en piezas)
            $recibido_factura_unidades = $item['cantidad'] - $item['cantidad_faltante'];
            $esperado_pz = $recibido_factura_unidades * $factor;

            // 2. Consulta de lo que REALMENTE se registró en la tabla movimientos
            $sqlSuma = "SELECT SUM(cantidad) as total_piezas FROM movimientos 
                        WHERE referencia_id = ? AND producto_id = ? AND tipo = 'entrada'";
            $stmtSuma = $conexion->prepare($sqlSuma);
            $stmtSuma->bind_param("ii", $id, $item['producto_id']);
            $stmtSuma->execute();
            $resSuma = $stmtSuma->get_result()->fetch_assoc();
            $sumaRealPZ = $resSuma['total_piezas'] ?? 0;

            // 3. Lógica de alertas
            // Si hay menos en movimientos que lo esperado por factura, es un error de ubicación manual
            $dif_ubicacion = $esperado_pz - $sumaRealPZ;
            $claseAlmacen = ($dif_ubicacion > 0.01) ? 'text-danger fw-bold' : 'text-primary fw-bold';
            $claseFaltanteFactura = ($item['cantidad_faltante'] > 0) ? 'table-warning' : '';
            
            echo "<tr class='{$claseFaltanteFactura}'>
                    <td>" . htmlspecialchars($item['producto_nombre']) . "<br><small class='text-body-secondary'>SKU: {$item['sku']}</small></td>
                    <td class='text-center'>" . number_format($item['cantidad'], 2) . "</td>
                    
                    <td class='text-center {$claseAlmacen}'>
                        " . number_format($sumaRealPZ, 2) . " PZ
                        " . ($dif_ubicacion > 0.01 ? "<br><small style='font-size:0.7rem'>(Faltan $dif_ubicacion por ubicar)</small>" : "") . "
                    </td>
                    
                    <td class='text-center text-danger'>" . number_format($item['cantidad_faltante'], 2) . "</td>
                    <td class='text-center text-success fw-bold'>" . number_format($esperado_pz, 2) . " </td>
                    <td class='text-end'>$" . number_format($item['precio_unitario'], 2) . "</td>
                    <td class='text-end'>$" . number_format($item['subtotal'], 2) . "</td>
                  </tr>";

            // --- DESGLOSE DETALLADO (Sub-filas) ---
            $sqlDest = "SELECT m.cantidad, a.nombre as almacen_n 
                        FROM movimientos m 
                        JOIN almacenes a ON m.almacen_destino_id = a.id 
                        WHERE m.referencia_id = ? AND m.producto_id = ? AND m.tipo = 'entrada'";
            $stmtDest = $conexion->prepare($sqlDest);
            $stmtDest->bind_param("ii", $id, $item['producto_id']);
            $stmtDest->execute();
            $resDest = $stmtDest->get_result();

            if($resDest->num_rows > 0) {
                echo "<tr><td colspan='7' class='p-0 '><div class='bg-light px-4 py-1 small border-bottom shadow-sm text-body-secondary' style='font-size: 0.75rem;'>";
                while($d = $resDest->fetch_assoc()) {
                    echo "<span class='me-4'><i class='bi bi-geo-alt-fill text-secondary'></i> 
                          <b>" . number_format($d['cantidad'], 2) . " PZ</b> -> " . htmlspecialchars($d['almacen_n']) . "</span>";
                }
                echo "</div></td></tr>";
            }
        }
        
        echo '    </tbody>
                  <tfoot>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end h5">TOTAL FACTURA:</th>
                        <th class="text-end text-primary h5">$' . number_format($compra['total'], 2) . '</th>
                    </tr>
                  </tfoot>
                </table>
              </div>';

        // --- SECCIÓN DE FALTANTES DE PROVEEDOR ---
        $sqlF = "SELECT f.*, p.nombre as producto_nombre, p.sku, dc.unidad_compra 
                 FROM faltantes_ingreso f
                 JOIN productos p ON f.producto_id = p.id
                 JOIN detalle_compra dc ON f.compra_id = dc.compra_id AND f.producto_id = dc.producto_id
                 WHERE f.compra_id = ?";
        $stmtF = $conexion->prepare($sqlF);
        $stmtF->bind_param("i", $id);
        $stmtF->execute();
        $faltantes = $stmtF->get_result();

        if ($faltantes->num_rows > 0) {
            echo '<div class="alert alert-danger mt-4 py-2 small"><i class="bi bi-exclamation-octagon-fill"></i> <b>Faltantes de Proveedor (No llegaron):</b></div>';
            echo '<div class="table-responsive">
                    <table class="table table-sm table-bordered border-danger" style="font-size: 0.85rem;">
                        <thead class="table-danger">
                            <tr><th>SKU</th><th>Producto</th><th class="text-center">Pendiente</th><th>Fecha</th></tr>
                        </thead>
                        <tbody>';
            while ($f = $faltantes->fetch_assoc()) {
               echo "<tr>
                        <td>{$f['sku']}</td>
                        <td>" . htmlspecialchars($f['producto_nombre']) . "</td>
                        <td class='text-center fw-bold text-danger'>".number_format($f['cantidad_pendiente'], 2)." {$f['unidad_compra']}</td>
                        <td>".date('d/m/Y', strtotime($f['fecha_registro']))."</td>
                     </tr>";
            }
            echo '  </tbody></table></div>';
        }

    } elseif ($tipo === 'gasto') {
        // ... (Se mantiene lógica de gastos igual) ...
        $sqlG = "SELECT g.*, u.nombre as usuario, a.nombre as almacen_nombre 
                 FROM gastos g 
                 LEFT JOIN usuarios u ON g.usuario_registra_id = u.id 
                 LEFT JOIN almacenes a ON g.almacen_id = a.id
                 WHERE g.id = ?";
        $stmtG = $conexion->prepare($sqlG);
        $stmtG->bind_param("i", $id);
        $stmtG->execute();
        $gasto = $stmtG->get_result()->fetch_assoc();

        if (!$gasto) throw new Exception("Gasto no encontrado.");

        $sqlDG = "SELECT * FROM detalle_gasto WHERE gasto_id = ?";
        $stmtDG = $conexion->prepare($sqlDG);
        $stmtDG->bind_param("i", $id);
        $stmtDG->execute();
        $detalleG = $stmtDG->get_result();

        echo '<div class="text-center mb-4"><h4 class="mb-0">Detalle de Gasto Operativo</h4><span class="badge bg-secondary">' . $gasto['folio'] . '</span></div>';
        echo '<div class="row g-3 mb-3 border-bottom pb-3">
                <div class="col-6"><b>Beneficiario:</b><br>' . htmlspecialchars($gasto['beneficiario']) . '</div>
                <div class="col-6 text-end"><b>Fecha Gasto:</b><br>' . date('d/m/Y', strtotime($gasto['fecha_gasto'])) . '</div>
              </div>';

        echo '<table class="table table-sm table-bordered">
                <thead class="table-warning">
                    <tr><th>Descripción</th><th class="text-center">Cant.</th><th class="text-end">Precio U.</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>';
        while ($dg = $detalleG->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($dg['descripcion']) . "</td>
                    <td class='text-center'>".number_format($dg['cantidad'], 2)."</td>
                    <td class='text-end'>$" . number_format($dg['precio_unitario'], 2) . "</td>
                    <td class='text-end'>$" . number_format($dg['subtotal'], 2) . "</td>
                  </tr>";
        }
        echo '</tbody><tfoot><tr class="table-light"><th colspan="3" class="text-end">TOTAL:</th><th class="text-end text-danger">$' . number_format($gasto['total'], 2) . '</th></tr></tfoot></table>';
    }

} catch (Exception $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';}?>