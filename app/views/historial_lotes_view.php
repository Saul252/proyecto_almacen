<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 = admin
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Lotes</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>

    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
    :root {
        --sidebar-width: 0px;
        --navbar-height: 65px;
        --apple-bg: #f5f5f7;
        --accent-blue: #007aff;
    }

    body {
        
        font-family: 'SF Pro Display', -apple-system, sans-serif;
        color: #1d1d1f;
    }

    .main-content {
        
        padding: 40px;
        padding-top: calc(var(--navbar-height) + 20px);
    }

    .card-premium {
        
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
            padding-top: 90px;
        }
    }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <h3 class="mb-3">📦 Historial de Lotes</h3>

        <div class="card p-3 mb-3">
            <div class="row g-3">
                <?php if ($almacen_usuario == 0): ?>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Almacén</label>
                    <select id="filtroAlmacen" class="form-select">
                        <option value="0">Todos</option>
                        <?php foreach ($almacenes as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

<div class="col-md-3">
    <label class="form-label small fw-bold">Producto</label>
    <select id="filtroProducto" class="form-select select2">
        <option value="">Selecciona producto</option>
        <?php foreach ($productos as $p): ?>
        <option value="<?= $p['id'] ?>">
            <?= htmlspecialchars($p['nombre']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" id="fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" id="fecha_fin" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>

                <div class="col-md-2 d-grid">
                    <label class="invisible">.</label>
                    <button class="btn btn-dark" onclick="cargarHistorial()">
                        Consultar
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm  rounded-4 p-3">
                    <div class="text-body-secondary small">Cantidad Inicial</div>
                    <h3 class="fw-bold mb-0" id="total_inicial">0</h3>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm  rounded-4 p-3">
                    <div class="text-body-secondary small">Cantidad Actual</div>
                    <h3 class="fw-bold mb-0" id="total_actual">0</h3>
                </div>
            </div>
        </div>

        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Compra</th>
                            <th>Lote</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th>Fecha</th>
                            <th>Inicial</th>
                            <th>Actual</th>
                            <th>Costo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaHistorial">
                        <tr>
                            <td colspan="10" class="text-center text-body-secondary">Selecciona un producto</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm  mt-4">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0">🧾 Movimientos del Lote</h6>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-sm table-hover text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Doc</th>
                                <th>Cliente</th>
                                <th>Lote</th>
                                <th>F. Lote</th>
                                <th>F. Mov</th>
                                <th>Cant. Inicial</th>
                                <th>Cant. Actual</th>
                                <th>Salida</th>
                                <th>Saldo</th>
                                <th>Costo</th>
                                <th>Precio</th>
                                <th>Ganancia</th>
                                <th>Ref</th>
                            </tr>
                        </thead>
                        <tbody id="tablaMovimientosLote"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card p-3 mt-4">
            <h5 class="mb-3">📊 Traspasos del lote</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>ID Mov</th>
                            
                            <th>Alm. Origen</th>
                            <th>Lote Origen</th>
                            <th>Alm. Destino</th>
                            <th>Lote Destino</th>
                            <th>Cant</th>
                        </tr>
                    </thead>
                    <tbody id="tablaTraspasosLote"></tbody>
                </table>
            </div>
        </div>

        <!-- <div class="card shadow-sm  mt-3 mb-5">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0">📦 Consumo de Lotes</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Venta</th>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Lote</th>
                            <th>Ingreso</th>
                            <th>Movimiento</th>
                            <th>C. Inicial</th>
                            <th>C. Actual</th>
                            <th>Salida</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaConsumoLotes"></tbody>
                </table>
            </div>
        </div> -->
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    function cargarHistorial() {
        const producto = $('#filtroProducto').val();
        const almacen = $('#filtroAlmacen').val() ?? 0;
        // NUEVO: Captura de fechas
        const f_ini = $('#fecha_inicio').val();
        const f_fin = $('#fecha_fin').val();
        //cargarTraspasos(producto, almacen);



        if (!producto) {
            Swal.fire("Aviso", "Selecciona un producto", "warning");
            return;
        }

        //cargarTraspasos(producto, almacen);
       // cargarConsumoLotes(producto, almacen);

        $.ajax({
            url: '/myvet/app/controllers/lotesHistorialController.php',
            type: 'GET',
            data: {
                action: 'obtenerLotes',
                producto_id: producto,
                almacen_id: almacen,
                periodo: 'personalizado', // Enviamos el periodo para que el controller sepa usar f_inicio
                f_inicio: f_ini,
                f_fin: f_fin
            },
            dataType: 'json',
            success: function(res) {
                $('#total_inicial').text(res.totales.total_cantidad_inicial || 0);
                $('#total_actual').text(res.totales.total_cantidad_actual || 0);

                let html = '';
                if (!res.success || !res.data.length) {
                    $('#tablaHistorial').html(
                        '<tr><td colspan="10" class="text-center">Sin datos</td></tr>');
                    return;
                }

                res.data.forEach(lote => {
                    let color = lote.estado_lote === 'activo' ? 'primary' : (lote.estado_lote ===
                        'agotado' ? 'danger' : 'secondary');
                    html += `
                    <tr>
                        <td>
    ${((lote.folio_compra ?? '').trim() || 'TRASPASO')}
    ${lote.lote_destino ? '· ' + lote.lote_destino : ''}
</td>
                        <td>${lote.codigo_lote}</td>
                        <td>${lote.producto_nombre}</td>
                        <td>${lote.almacen_nombre}</td>
                        <td>${lote.fecha_compra}</td>
                        <td>${lote.cantidad_inicial}</td>
                        <td>${lote.cantidad_actual}</td>
                        <td>$${parseFloat(lote.precio_compra_unitario || 0).toFixed(2)}</td>
                        <td><span class="badge bg-${color}">${lote.estado_lote}</span></td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick="verMovimientos(${lote.lote_id})">Ver</button></td>
                    </tr>`;
                });
                $('#tablaHistorial').html(html);
            }
        });
    }


    // function cargarConsumoLotes(producto, almacen) {
    //     const f_ini = $('#fecha_inicio').val();
    //     const f_fin = $('#fecha_fin').val();

    //     $.ajax({
    //         url: '/myvet/app/controllers/lotesHistorialController.php',
    //         type: 'GET',
    //         data: {
    //             action: 'obtenerConsumoLotes',
    //             producto_id: producto,
    //             almacen_id: almacen,
    //             fecha_inicio: f_ini,
    //             fecha_fin: f_fin
    //         },
    //         dataType: 'json',
    //         success: function(res) {
    //             let html = '';
    //             if (res.length > 0) {
    //                 res.forEach(row => {
    //                     html += `<tr>
    //                     <td><span class="badge bg-primary">${row.venta_id}</span></td>
    //                     <td>${row.folio}</td><td>${row.cliente}</td><td>${row.codigo_lote}</td>
    //                     <td>${row.fecha_ingreso}</td><td>${row.fecha_movimiento}</td>
    //                     <td class="text-center">${row.cantidad_inicial}</td><td class="text-center">${row.cantidad_actual}</td>
    //                     <td class="text-center text-danger">-${row.cantidad_salida}</td>
    //                     <td class="text-center fw-bold">${row.saldo_final}</td>
    //                 </tr>`;
    //                 });
    //             } else {
    //                 html = '<tr><td colspan="10" class="text-center">Sin datos</td></tr>';
    //             }
    //             $('#tablaConsumoLotes').html(html);
    //         }
    //     });
    // }

    function verMovimientos(lote_id) {
        cargarTraspasos(lote_id);
        //cargarConsumoLotes(producto, almacen);

        $.ajax({
            url: '/myvet/app/controllers/lotesHistorialController.php',
            type: 'GET',
            data: {
                action: 'obtenerVentasLote',
                lote_id: lote_id
            },
            dataType: 'json',
            success: function(res) {
                let html = '';
                if (res.success && res.data.length > 0) {
                    res.data.forEach(mov => {
                        let ganancia = parseFloat(mov.ganancia || 0);
                        html += `<tr>
                        <td>${mov.tipo_movimiento}</td><td>${mov.documento}</td><td>${mov.cliente_proveedor}</td>
                        <td>${mov.codigo_lote}</td><td>${mov.fecha_lote}</td><td>${mov.fecha_movimiento}</td>
                        <td class="text-end">${mov.cantidad_inicial}</td><td class="text-end">${mov.cantidad_actual}</td>
                        <td class="text-end">${mov.cantidad_salida}</td><td class="text-end fw-bold">${mov.saldo_final}</td>
                        <td class="text-end">$${mov.costo_unitario}</td><td class="text-end">$${mov.precio_venta}</td>
                        <td class="text-end ${ganancia >= 0 ? 'text-success' : 'text-danger'}">$${ganancia.toFixed(2)}</td>
                        <td>${mov.referencia_extra}</td>
                    </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="14" class="text-center">Sin movimientos</td></tr>';
                }
                $('#tablaMovimientosLote').html(html);
            }
        });
    }

    function cargarTraspasos(lote_id) {
        const f_ini = $('#fecha_inicio').val();
        const f_fin = $('#fecha_fin').val();

        $.ajax({
            url: '/myvet/app/controllers/lotesHistorialController.php',
            type: 'GET',
            data: {
                action: 'obtenerTraspasos',
                lote_id: lote_id,

                f_inicio: f_ini,
                f_fin: f_fin
            },
            dataType: 'json',
            success: function(res) {
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(t => {
                        html +=
                            `<tr><td>TRASPASO</td><td>${t.fecha}</td><td>${t.movimiento_id}</td><td>${t.nombreOrigen}</td><td>${t.codigo_lote_origen}</td><td>${t.nombreDestino}</td><td>${t.codigo_lote_destino}</td><td>${t.cantidad}</td></tr>`;
                    });
                } else {
                    html = '<tr><td colspan="9" class="text-center">Sin traspasos</td></tr>';
                }
                $('#tablaTraspasosLote').html(html);
            }
        });
    }

    $('#filtroAlmacen').on('change', function() {
        verMovimientos(0);
        let almacen = $(this).val();
        $.ajax({
            url: '/myvet/app/controllers/lotesHistorialController.php',
            type: 'GET',
            data: {
                action: 'productos',
                almacen_id: almacen
            },
            dataType: 'json',
            success: function(res) {
                let html = '<option value="">Selecciona producto</option>';
                if (res.success) {
                    res.data.forEach(p => {
                        html += `<option value="${p.id}">${p.nombre}</option>`;
                    });
                }
                $('#filtroProducto').html(html);
            }
        });
    });
    $(document).ready(function () {
    $('#filtroProducto').select2({
        placeholder: 'Buscar producto...',
        allowClear: true,
        width: '100%'
    });
});
    </script>
</body>

</html>