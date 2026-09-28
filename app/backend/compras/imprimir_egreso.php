<?php
// 1. Configuración y conexión
require_once __DIR__ . '/../../../config/conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

if ($id <= 0 || empty($tipo)) die("Datos insuficientes.");

try {
    // --- REUTILIZAMOS TU LÓGICA DE CONSULTA ---
    if ($tipo === 'compra') {
        $sqlC = "SELECT c.*, u.nombre as usuario, a.nombre as almacen_nombre 
                 FROM compras c 
                 LEFT JOIN usuarios u ON c.usuario_registra_id = u.id 
                 LEFT JOIN almacenes a ON c.almacen_id = a.id
                 WHERE c.id = ?";
        $stmtC = $conexion->prepare($sqlC);
        $stmtC->bind_param("i", $id);
        $stmtC->execute();
        $datos = $stmtC->get_result()->fetch_assoc();
        
        $sqlD = "SELECT d.*, p.nombre as producto_nombre, p.sku 
                 FROM detalle_compra d 
                 JOIN productos p ON d.producto_id = p.id 
                 WHERE d.compra_id = ?";
        $stmtD = $conexion->prepare($sqlD);
        $stmtD->bind_param("i", $id);
        $stmtD->execute();
        $detalle = $stmtD->get_result();
    } else {
        $sqlG = "SELECT g.*, u.nombre as usuario FROM gastos g 
                 LEFT JOIN usuarios u ON g.usuario_registra_id = u.id WHERE g.id = ?";
        $stmtG = $conexion->prepare($sqlG);
        $stmtG->bind_param("i", $id);
        $stmtG->execute();
        $datos = $stmtG->get_result()->fetch_assoc();

        $sqlDG = "SELECT * FROM detalle_gasto WHERE gasto_id = ?";
        $stmtDG = $conexion->prepare($sqlDG);
        $stmtDG->bind_param("i", $id);
        $stmtDG->execute();
        $detalle = $stmtDG->get_result();
    }
} catch (Exception $e) { die($e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir <?= strtoupper($tipo) ?> - <?= $datos['folio'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff; font-size: 12px; }
        .ticket-header { border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
        .table thead { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; }
        .badge-tipo { border: 1px solid #000; padding: 5px 10px; font-weight: bold; text-transform: uppercase; }
        
        /* Estilos de Impresión */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
            .container { max-width: 100% !important; width: 100% !important; }
            .table { width: 100% !important; }
            @page { margin: 0.5cm; }
        }

        .firma-box { border-top: 1px solid #000; width: 200px; margin-top: 50px; text-align: center; padding-top: 5px; }
    </style>
</head>
<body onload="window.print();">

<div class="container my-4">
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Confirmar Impresión</button>
        <button onclick="window.close()" class="btn btn-secondary">Cerrar</button>
    </div>

    <div class="ticket-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0">COMPROBANTE DE <?= strtoupper($tipo) ?></h3>
            <p class="mb-0 text-body-secondary">Sistema de Gestión de Almacén</p>
        </div>
        <div class="text-end">
            <div class="badge-tipo">FOLIO: <?= $datos['folio'] ?></div>
            <div class="mt-1 small">Fecha: <?= date('d/m/Y H:i', strtotime($datos['fecha_compra'] ?? $datos['fecha_gasto'])) ?></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <table class="table table-sm table-borderless">
                <tr><th width="120">Entidad/Prov:</th><td><?= htmlspecialchars($datos['proveedor'] ?? $datos['beneficiario']) ?></td></tr>
                <?php if($tipo === 'compra'): ?>
                <tr><th>Almacén:</th><td><?= htmlspecialchars($datos['almacen_nombre'] ?? 'General') ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="col-6">
            <table class="table table-sm table-borderless">
                <tr><th width="120">Registrado por:</th><td><?= htmlspecialchars($datos['usuario']) ?></td></tr>
                <tr><th>Estado:</th><td><?= ($datos['tiene_faltantes'] ?? 0) ? 'PENDIENTE DE AJUSTE' : 'COMPLETO' ?></td></tr>
            </table>
        </div>
    </div>

    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>Descripción / SKU</th>
                <th class="text-center">Cant.</th>
                <?php if($tipo === 'compra'): ?>
                <th class="text-center">Recibido (PZ)</th>
                <?php endif; ?>
                <th class="text-end">Precio U.</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $detalle->fetch_assoc()): ?>
            <tr>
                <td>
                    <b><?= htmlspecialchars($item['producto_nombre'] ?? $item['descripcion']) ?></b>
                    <?php if(isset($item['sku'])): ?><br><small>SKU: <?= $item['sku'] ?></small><?php endif; ?>
                </td>
                <td class="text-center"><?= number_format($item['cantidad'], 2) ?></td>
                <?php if($tipo === 'compra'): 
                    // Suma real igual que en tu ver detalle
                    $sqlS = "SELECT SUM(cantidad) as t FROM movimientos WHERE referencia_id = ? AND producto_id = ? AND tipo = 'entrada'";
                    $stS = $conexion->prepare($sqlS);
                    $stS->bind_param("ii", $id, $item['producto_id']);
                    $stS->execute();
                    $rS = $stS->get_result()->fetch_assoc();
                ?>
                <td class="text-center"><?= number_format($rS['t'] ?? 0, 2) ?> PZ</td>
                <?php endif; ?>
                <td class="text-end">$<?= number_format($item['precio_unitario'], 2) ?></td>
                <td class="text-end">$<?= number_format($item['subtotal'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="<?= ($tipo === 'compra' ? 4 : 3) ?>" class="text-end">TOTAL:</th>
                <th class="text-end text-primary h5">$<?= number_format($datos['total'], 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="d-flex justify-content-around mt-5 pt-4">
        <div class="firma-box">
            <small>Firma Responsable</small><br>
            <b><?= htmlspecialchars($datos['usuario']) ?></b>
        </div>
        <div class="firma-box">
            <small>Firma Proveedor/Recibe</small>
        </div>
    </div>

    <div class="text-center mt-5 text-body-secondary small">
        <p>Documento generado internamente. Fecha de impresión: <?= date('d/m/Y H:i') ?></p>
    </div>
</div>

</body>
</html>