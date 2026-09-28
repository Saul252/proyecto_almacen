<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente Detallado | CF System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
      
        .card-venta { border-radius: 15px;  box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .header-venta { background-color: #fff; border-bottom: 1px solid #eee; padding: 15px 20px; border-radius: 15px 15px 0 0; }
        .detalle-productos { background-color: #fafafa; border-radius: 10px; padding: 15px; }
        .historial-pagos { border-left: 3px solid #007aff; padding-left: 15px; margin-top: 10px; }
        .badge-abono { font-size: 0.75rem; background-color: #e8f4ff; color: #007aff; border: 1px solid #007aff; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold"><i class="bi bi-person-vcard me-2"></i><?= $cliente['nombre_comercial'] ?></h3>
            <div class="text-end">
                <span class="text-muted d-block">Deuda Total Acumulada</span>
                <h2 class="text-danger fw-bold">$ <?= number_format($resumen['saldo_total'], 2) ?></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h5 class="mb-3 text-secondary text-uppercase small fw-bold">Historial de Operaciones</h5>
            
            <?php foreach ($expediente as $v): 
                $saldoIndividual = $v['total'] - $v['total_pagado'];
            ?>
            <div class="card card-venta">
                <div class="header-venta d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-dark mb-1">Folio: #<?= $v['folio'] ?></span>
                        <span class="text-muted ms-2 small"><i class="bi bi-calendar3"></i> <?= date('d/m/Y', strtotime($v['fecha'])) ?></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-end me-4">
                            <small class="text-muted d-block">Saldo Pendiente</small>
                            <span class="fw-bold <?= $saldoIndividual > 0 ? 'text-danger' : 'text-success' ?>">
                                $ <?= number_format($saldoIndividual, 2) ?>
                            </span>
                        </div>
                        <?php if ($saldoIndividual > 0.01): ?>
                            <button class="btn btn-primary rounded-pill btn-sm px-4" 
                                    onclick="registrarAbono(<?= $v['id'] ?>, '<?= $v['folio'] ?>', <?= $saldoIndividual ?>)">
                                <i class="bi bi-cash-coin"></i> Abonar
                            </button>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success border border-success px-3">LIQUIDADA</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <p class="small fw-bold text-muted mb-2"><i class="bi bi-box-seam"></i> PRODUCTOS EN ESTA VENTA</p>
                            <div class="detalle-productos">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted border-bottom" style="font-size: 0.8rem;">
                                            <th>Producto</th>
                                            <th class="text-center">Cant.</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($v['productos'] as $p): ?>
                                        <tr style="font-size: 0.9rem;">
                                            <td><?= $p['producto'] ?> <br><small class="text-muted">SKU: <?= $p['sku'] ?> | Lote: <?= $p['lote_codigo'] ?></small></td>
                                            <td class="text-center"><?= number_format($p['cantidad'], 2) ?></td>
                                            <td class="text-end">$ <?= number_format($p['precio_venta'], 2) ?></td>
                                            <td class="text-end fw-bold">$ <?= number_format($p['cantidad'] * $p['precio_venta'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="border-top">
                                            <td colspan="3" class="text-end text-muted">Total Venta:</td>
                                            <td class="text-end fw-bold text-dark">$ <?= number_format($v['total'], 2) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <p class="small fw-bold text-muted mb-2"><i class="bi bi-clock-history"></i> HISTORIAL DE PAGOS</p>
                            <?php if (empty($v['pagos'])): ?>
                                <div class="alert alert-light border text-center py-2">
                                    <small class="text-muted">No se han registrado abonos aún.</small>
                                </div>
                            <?php else: ?>
                                <div class="historial-pagos">
                                    <?php foreach ($v['pagos'] as $pago): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white border rounded shadow-sm">
                                        <div>
                                            <span class="d-block fw-bold text-success">$ <?= number_format($pago['monto'], 2) ?></span>
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                <?= date('d/m/Y H:i', strtotime($pago['fecha'])) ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge badge-abono">Recibió: <?= explode(' ', $pago['usuario_recibio'])[0] ?></span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <div class="mt-2 text-end">
                                        <small class="text-muted">Total Abonado: </small>
                                        <span class="fw-bold text-success">$ <?= number_format($v['total_pagado'], 2) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function registrarAbono(ventaId, folio, saldoMax) {
    Swal.fire({
        title: 'Abonar a Folio ' + folio,
        html: `Saldo pendiente: <b class="text-danger">$ ${saldoMax.toLocaleString()}</b>`,
        input: 'number',
        inputAttributes: { min: 0.01, max: saldoMax, step: 0.01 },
        showCancelButton: true,
        confirmButtonText: 'Registrar Pago',
        confirmButtonColor: '#0d6efd',
        preConfirm: (monto) => {
            if (!monto || monto <= 0 || monto > saldoMax) {
                Swal.showValidationMessage('El monto debe ser mayor a 0 y no exceder el saldo.');
                return false;
            }
            return $.post('clientesEstatusController.php', {
                action: 'registrar_abono',
                venta_id: ventaId,
                monto: monto,
                id_cliente: <?= $id_cliente ?>
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
            Swal.fire('¡Pago Exitoso!', '', 'success').then(() => location.reload());
        }
    });
}
</script>

</body>
</html>