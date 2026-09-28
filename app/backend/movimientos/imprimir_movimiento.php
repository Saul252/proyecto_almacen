<?php
date_default_timezone_set('America/Mexico_City');
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID de movimiento no proporcionado.");

// Consulta actualizada con factor de conversión y unidad de reporte
$sql = "SELECT m.*, p.nombre as prod, p.sku, p.unidad_medida, p.unidad_reporte, p.factor_conversion,
               a1.nombre as origen, a2.nombre as destino,
               u1.nombre as usuario_reg, u2.nombre as usuario_env, u3.nombre as usuario_rec
        FROM movimientos m
        INNER JOIN productos p ON m.producto_id = p.id
        LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
        LEFT JOIN almacenes a2 ON m.almacen_destino_id = a2.id
        LEFT JOIN usuarios u1 ON m.usuario_registra_id = u1.id
        LEFT JOIN usuarios u2 ON m.usuario_envia_id = u2.id
        LEFT JOIN usuarios u3 ON m.usuario_recibe_id = u3.id
        WHERE m.id = $id";

$res = $conexion->query($sql);
$m = $res->fetch_assoc();

if (!$m) die("Movimiento no encontrado.");

// Lógica de desglosado por factor de conversión
$cantidad = floatval($m['cantidad']);
$factor = floatval($m['factor_conversion'] ?: 1);
$unidad_base = $m['unidad_medida'] ?: 'Pza';
$unidad_mayor = $m['unidad_reporte'] ?: 'Bulto';

$visual_detalle = "";
if ($factor > 1 && $cantidad >= $factor) {
    $bultos = floor($cantidad / $factor);
    $piezas = $cantidad % $factor;
    $visual_detalle = "<strong>$bultos $unidad_mayor</strong>";
    if ($piezas > 0) {
        $visual_detalle .= " con <strong>" . number_format($piezas, 2) . " $unidad_base</strong>";
    }
} else {
    $visual_detalle = "<strong>" . number_format($cantidad, 2) . " $unidad_base</strong>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimiento_#<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #e9ecef; font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #333; }
        .ticket { 
            background: white; 
            width: 21cm; 
            min-height: 27cm; 
            margin: 1.5cm auto; 
            padding: 1.5cm; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 4px;
            position: relative;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem;
            color: rgba(0,0,0,0.03);
            font-weight: 900;
            pointer-events: none;
            z-index: 0;
        }
        .header-title { border-left: 5px solid #0d6efd; padding-left: 15px; }
        .info-box { background: #fdfdfd; border: 1px solid #eee; padding: 15px; border-radius: 6px; }
        .label-sm { font-size: 0.7rem; text-transform: uppercase; color: #888; font-weight: 700; letter-spacing: 0.5px; }
        .table-custom thead { background: #f8f9fa; }
        .table-custom th { font-size: 0.8rem; text-transform: uppercase; color: #555; }
        .badge-type { font-size: 1rem; padding: 8px 15px; border-radius: 50px; }
        
        @media print {
            body { background: white; margin: 0; }
            .ticket { box-shadow: none; margin: 0; width: 100%; padding: 1cm; }
            .no-print { display: none !important; }
            .watermark { display: block; }
        }
    </style>
</head>
<body>

    <div class="container no-print mt-4 mb-4 text-center">
        <div class="btn-group shadow-sm">
            <button onclick="window.print();" class="btn btn-dark btn-lg px-4">
                <i class="bi bi-printer-fill me-2"></i> Imprimir Comprobante
            </button>
            <a href="javascript:window.close();" class="btn btn-outline-dark btn-lg px-4">Cerrar</a>
        </div>
    </div>

    <div class="ticket">
        <div class="watermark"><?= strtoupper($m['tipo']) ?></div>

        <div class="row align-items-center mb-4" style="z-index: 1; position: relative;">
            <div class="col-7 header-title">
                <h2 class="fw-black mb-0 text-dark">CF <span class="text-primary">SISTEM</span></h2>
                <p class="text-body-secondary small mb-0">Comprobante Interno de Control de Inventarios</p>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-2 badge-type">
                    <?= strtoupper($m['tipo']) ?>
                </span>
            </div>
            <div class="col-5 text-end">
                <div class="mb-1 label-sm text-dark">Folio del Movimiento</div>
                <h3 class="fw-bold mb-0">#<?= str_pad($m['id'], 6, "0", STR_PAD_LEFT) ?></h3>
                <p class="small text-body-secondary mb-0">Impreso: <?= date('d/m/Y H:i') ?></p>
            </div>
        </div>

        <hr>

        <div class="row info-box g-3 mb-4">
            <div class="col-4 border-end">
                <div class="label-sm"><i class="bi bi-calendar-event me-1"></i> Fecha de Registro</div>
                <div class="fw-bold"><?= date('d/m/Y h:i A', strtotime($m['fecha'])) ?></div>
            </div>
            <div class="col-4 border-end">
                <div class="label-sm"><i class="bi bi-person-badge me-1"></i> Responsable</div>
                <div class="fw-bold"><?= $m['usuario_reg'] ?></div>
            </div>
            <div class="col-4">
                <div class="label-sm"><i class="bi bi-info-circle me-1"></i> Referencia</div>
                <div class="fw-bold text-truncate"><?= $m['referencia_id'] ? 'REF-'.$m['referencia_id'] : 'Sin Referencia' ?></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle table-custom">
                <thead>
                    <tr>
                        <th width="150">Código / SKU</th>
                        <th>Descripción del Producto</th>
                        <th class="text-center" width="250">Cantidad Desglosada</th>
                        <th class="text-center">Total Unidades</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="height: 100px;">
                        <td class="fw-bold text-primary"><?= $m['sku'] ?></td>
                        <td>
                            <div class="fw-bold"><?= $m['prod'] ?></div>
                            <small class="text-body-secondary">Unidad Base: <?= $unidad_base ?></small>
                        </td>
                        <td class="text-center text-dark">
                            <span class="fs-5"><?= $visual_detalle ?></span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold fs-5"><?= number_format($cantidad, 2) ?></span><br>
                            <small class="text-body-secondary"><?= strtoupper($unidad_base) ?></small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row mt-4 g-4">
            <div class="col-6">
                <div class="p-3 border rounded shadow-sm bg-light-subtle h-100">
                    <div class="label-sm mb-2 text-danger"><i class="bi bi-box-arrow-left"></i> Almacén Origen</div>
                    <div class="h5 fw-bold mb-0"><?= $m['origen'] ?? '<span class="text-body-secondary fw-normal">N/A</span>' ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 border rounded shadow-sm bg-light-subtle h-100">
                    <div class="label-sm mb-2 text-success"><i class="bi bi-box-arrow-in-right"></i> Almacén Destino</div>
                    <div class="h5 fw-bold mb-0"><?= $m['destino'] ?? '<span class="text-body-secondary fw-normal">N/A</span>' ?></div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="p-3 border rounded bg-light" style="min-height: 80px;">
                <div class="label-sm mb-1">Observaciones / Motivo del Movimiento</div>
                <div class="small italic text-secondary">
                    <?= $m['observaciones'] ?: 'El usuario no registró observaciones para este movimiento.' ?>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-4 text-center">
            <div class="col-4">
                <div class="mx-3" style="border-top: 1.5px solid #333; padding-top: 10px;">
                    <div class="label-sm">Registró / Autorizó</div>
                    <div class="fw-bold small"><?= $m['usuario_reg'] ?></div>
                </div>
            </div>
            <div class="col-4">
                <div class="mx-3" style="border-top: 1.5px solid #333; padding-top: 10px;">
                    <div class="label-sm">Envió (Firma)</div>
                    <div class="fw-bold small"><?= $m['usuario_env'] ?: '---' ?></div>
                </div>
            </div>
            <div class="col-4">
                <div class="mx-3" style="border-top: 1.5px solid #333; padding-top: 10px;">
                    <div class="label-sm">Recibió (Firma)</div>
                    <div class="fw-bold small"><?= $m['usuario_rec'] ?: '---' ?></div>
                </div>
            </div>
        </div>

        <footer class="mt-5 pt-5 text-center">
            <div class="label-sm border-top pt-3">
                CF SISTEM - Software de Gestión de Almacén <br>
                <span class="fw-normal lowercase text-lowercase">Generado automáticamente por el módulo de logística</span>
            </div>
        </footer>
    </div>

</body>
</html>