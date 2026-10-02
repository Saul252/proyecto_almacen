<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0);
$paginaActual = $paginaActual ?? 'prestamos';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faltas y retardos de Trabajadores | cfsistem</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>
    <style>
        :root {
            --accent: #007aff;
            --bg: #f5f5f7;
        }

        body {

            font-family: -apple-system, sans-serif;
        }

        .main-content {
            margin-left: 0px;
            padding: 40px;
        }

        .card-ui {

            border-radius: 18px;

            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .estado-activo {
            background: #fff3e0;
            color: #ef6c00;
        }

        .estado-liquidado {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .progress {
            height: 6px;
            border-radius: 10px;
        }

        .d-none {
            display: none !important;
        }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0">Faltas y retardos de Trabajadores</h2>
                <small class="text-body-secondary" id="status-almacen">Mostrando datos de:
                    <?= $almacen_usuario == 0 ? 'Todas las sucursales' : 'Sucursal Actual' ?></small>
            </div>

            <button class="btn btn-primary rounded-pill px-4" onclick="nuevoPrestamo()">
                <i class="bi bi-cash-stack me-2"></i> Nueva falta/retardo
            </button>
        </div>
        <div class="col-md-4">
            <div class="glass-card deuda-widget position-relative overflow-hidden animate__animated animate__fadeInUp">


                <!-- Barra visual -->

            </div>
        </div>
        <div class="card card-ui p-4">
            <div class="row mb-4 g-3">
                <div class="col-md-5">
                    <div class="input-group  rounded-3 p-1">
                        <span class="input-group-text bg-transparent "><i class="bi bi-search"></i></span>
                        <input type="text" id="busqueda" class="form-control  bg-transparent"
                            placeholder="Buscar trabajador...">
                    </div>
                </div>
                <div class="row mb-4 g-3 align-items-end">

                    <!-- 📅 PERIODO -->
                    <div class="col-md-2">
                        <label class="form-label text-body-secondary small">Periodo</label>
                        <select id="periodo" class="form-select   rounded-3">
                            <option value="hoy">Hoy</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Últimos 7 días</option>
                            <option value="mes">Este mes</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>

                    <!-- 📆 FECHA INICIO -->
                    <div class="col-md-2">
                        <label class="form-label text-body-secondary small">Desde</label>
                        <input type="date" id="f_inicio" class="form-control   rounded-3">
                    </div>

                    <!-- 📆 FECHA FIN -->
                    <div class="col-md-2">
                        <label class="form-label text-body-secondary small">Hasta</label>
                        <input type="date" id="f_fin" class="form-control   rounded-3">
                    </div>

                    <!-- 🏢 SUCURSAL (solo si aplica) -->
                    <?php if ($almacen_usuario == 0): ?>
                        <div class="col-md-3">
                            <label class="form-label text-body-secondary small">Sucursal</label>
                            <select id="filtroSucursal" class="form-select   rounded-3">
                                <option value="0">🌐 Todas</option>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <div class="table-responsive">

                <table id="tablaPrestamos" class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th><i class="bi bi-calendar3 me-1 text-primary"></i>Fecha</th>
                            <th><i class="bi bi-person-fill me-1 text-primary"></i>Trabajador</th>
                            <th><i class="bi bi-card-text me-1 text-primary"></i>Tipo</th>
                            <th><i class="bi bi-card-text me-1 text-primary"></i>Descripción</th>
                            <th class="text-end">
                                <i class="bi bi-cash-stack me-1 text-success"></i>Monto
                            </th>
                            <th class="text-center" width="120">
                                <i class="bi bi-gear-fill me-1 text-secondary"></i>Acciones
                            </th>
                        </tr>

                    </thead>

                    <tbody id="bodyPrestamos">

                        <?php foreach ($prestamos as $p): ?>

                            <tr>

                                <td class="text-nowrap">
                                    <?= date('d/m/Y', strtotime($p['fecha'])) ?>
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        <i class="bi bi-person-circle text-secondary me-1"></i>
                                        <?= $p['trabajador'] ?>
                                    </span>
                                </td>

                                <td>
                                    <small class="fw-semibold">
                                        <?= $p['tipo'] ?>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-body-secondary">
                                        <?= $p['descripcion'] ?>
                                    </small>
                                </td>

                                <td class="text-end">
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        $<?= number_format($p['monto'], 2) ?>
                                    </span>
                                </td>

                                <td class="text-center">

                                    <button class="btn btn-outline-warning btn-sm rounded-pill px-3"
                                        onclick='imprimirComprobanteFalta(this)' title="Imprimir comprobante de falta">
                                        <i class="bi bi-file-earmark-text me-1"></i>
                                        Comprobante
                                    </button>

                                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                        onclick="eliminarPrestamo(<?= $p['id'] ?>)" title="Eliminar préstamo">

                                        <i class="bi bi-trash3 me-1"></i>
                                        Eliminar

                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>
        </div>

        <div class="modal fade" id="modalPrestamo" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content  shadow-lg" style="border-radius: 22px; overflow: hidden;">

                    <!-- HEADER -->
                    <div class="modal-header bg-dark text-white py-3">
                        <div>
                            <h5 class="modal-title mb-0 fw-semibold">
                                <i class="bi bi-cash-stack me-2"></i>
                                Registrar Falta o retardo
                            </h5>
                            <small class="text-white-50">Gestión de asistencia</small>
                        </div>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="formPrestamo">

                        <!-- BODY -->
                        <div class="modal-body p-4">

                            <div class="row g-3">

                                <!-- ALMACÉN -->


                                <!-- TRABAJADOR -->
                                <div class="col-md-6">
                                    <label class="form-label text-body-secondary small">Trabajador</label>
                                    <select name="trabajador_id" id="modal_trabajador_id"
                                        class="form-select form-select-lg rounded-3" required>
                                        <option value="0">Seleccione trabajador</option>
                                        <?php foreach ($trabajadores as $t): ?>
                                            <option value="<?= $t['id'] ?>">
                                                <?= $t['nombre'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- WIDGET DEUDA -->
                                <div class="col-md-6">
                                    <label class="form-label text-body-secondary small">Tipo </label>
                                    <select name="tipo" id="tipo" class="form-select form-select-lg rounded-3" required>
                                        <option value="falta">Seleccione Tipo</option>

                                        <option value="falta">
                                            Falta
                                        </option>
                                        <option value="retardo">
                                            Retardo
                                        </option>

                                    </select>
                                </div>


                                <!-- MONTO -->
                                <div class="col-md-6">
                                    <label class="form-label text-body-secondary small">Monto </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="monto" class="form-control"
                                            placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-body-secondary small">Hasta</label>
                                    <input type="date" name="fecha" id="fecha" class="form-control   rounded-3">
                                </div>
                                <!-- MÉTODO -->

                                <!-- DESCRIPCIÓN -->
                                <div class="col-12">
                                    <label class="form-label text-body-secondary small">Motivo / Descripción</label>
                                    <textarea name="descripcion" class="form-control text-uppercase rounded-3" rows="3"
                                        placeholder="Retardo, Falta, etc"></textarea>
                                </div>

                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="modal-footer   px-4 py-3">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                                Cancelar
                            </button>

                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                Confirmar préstamo
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalMovimientoDinero" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content " style="border-radius:20px;">
                    <form id="formMovimientoDinero">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-arrow-left-right me-2"></i>
                                Movimiento de Dinero (Abono)
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Movimiento</label>
                                    <input type="hidden" name="prestamo_id" id="mov_prestamo_id">
                                    <div class="form-control ">
                                        ID: <span id="mov_prestamo_id_text"></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Saldo</label>
                                    <div class="form-control ">
                                        Deuda: <span id="mov_saldo_text"></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Almacén</label>
                                    <select name="almacen_id" id="mov_almacen_id" class="form-select" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach ($almacenes as $a): ?>
                                            <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Monto del Abono</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="monto_abono" class="form-control"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Destino del Dinero</label>
                                    <select name="tipo_destino" id="tipo_destino" class="form-select" required>
                                        <option value="">-- Seleccionar --</option>
                                        <option value="caja_fuerte">Caja Fuerte</option>
                                        <option value="banco">Banco</option>
                                        <option value="saldo_inicial">Caja del Día (Saldo Inicial)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 d-none" id="wrap_caja">
                                    <label class="form-label">Seleccionar Caja Fuerte</label>
                                    <select name="caja_fuerte_id" id="select_caja_fuerte" class="form-select">
                                        <option value="">-- Seleccionar Caja --</option>
                                        <?php foreach ($cajasFuertes as $c): ?>
                                            <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 d-none" id="wrap_banco">
                                    <label class="form-label">Seleccionar Banco</label>
                                    <select name="banco_id" id="select_banco" class="form-select">
                                        <option value="">-- Seleccionar Banco --</option>
                                        <?php foreach ($bancos as $b): ?>
                                            <option value="<?= $b['id'] ?>"><?= $b['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control text-uppercase"
                                        rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary px-4">Confirmar Abono</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade" id="modalPrestamoDetalleUnique" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content  shadow rounded-4">

                <div class="modal-header ">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cash-coin me-2"></i>Detalle del Préstamo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3 mb-3">

                        <div class="col-md-6">
                            <small class="text-body-secondary">Estado</small>
                            <div id="mp_estado_u"></div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-body-secondary">Monto</small>
                            <div id="mp_monto_u" class="fw-bold"></div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-body-secondary">Abonado</small>
                            <div id="mp_abonado_u" class="fw-bold text-primary"></div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-body-secondary">Saldo</small>
                            <div id="mp_saldo_u" class="fw-bold text-danger"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-body-secondary">Descripción</small>
                        <div id="mp_desc_u" class="p-2  rounded-3 small"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Fecha</th>
                                    <th>Obs</th>
                                </tr>
                            </thead>
                            <tbody id="tablaAbonosPrestamoUnique"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let tabla;
        const CONTROLLER = '/myvet/app/controllers/faltasController.php';

        $(document).ready(function () {

            // =========================
            // DATATABLE
            // =========================
            tabla = $('#tablaPrestamos').DataTable({
                pageLength: 15,
                dom: 'rtp',
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });

            // =========================
            // BUSCADOR
            // =========================
            $('#busqueda').on('keyup', function () {
                tabla.search(this.value).draw();
            });

            // =========================
            // FILTRO SUCURSAL
            // =========================
            $('#filtroSucursal').on('change', function () {
                const id = $(this).val();
                cargarDatosAlmacen(id);
            });

            // =========================
            // 🔥 FILTROS DE FECHA (AGREGADO)
            // =========================
            function recargarFiltros() {
                const id = $('#filtroSucursal').val() || 0;
                cargarDatosAlmacen(id);
            }

            $('#periodo').on('change', function () {
                recargarFiltros();
            });

            $('#f_inicio, #f_fin').on('change', function () {
                $('#periodo').val('personalizado');
                recargarFiltros();
            });

            // =========================
            // CAMBIO ALMACÉN MODAL
            // =========================
            $('#modal_almacen_id').on('change', function () {
                cargarDatosAlmacen(this.value, true);
            });

            // =====================================================
            // 🔥 LÓGICA DE DESTINO (Caja Fuerte / Banco)
            // =====================================================
            $(document).on('change', '#tipo_destino', function () {
                const destino = $(this).val();
                $('#wrap_caja, #wrap_banco').addClass('d-none');
                $('#select_caja_fuerte, #select_banco').val('').removeAttr('required');

                if (destino === 'caja_fuerte') {
                    $('#wrap_caja').removeClass('d-none');
                    $('#select_caja_fuerte').attr('required', true);
                } else if (destino === 'banco') {
                    $('#wrap_banco').removeClass('d-none');
                    $('#select_banco').attr('required', true);
                }
            });

            // =========================
            // FORM PRESTAMO (NUEVO)
            // =========================
            $('#formPrestamo').on('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                try {
                    const resp = await fetch(`${CONTROLLER}?action=crearFalta`, {
                        method: 'POST',
                        body: formData
                    });

                    const res = await resp.json();
                    if (res.success) {
                        Swal.fire('Éxito', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message || 'Error desconocido', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'Error en la petición', 'error');
                }
            });

            // =========================
            // FORM ABONO
            // =========================
            $('#formMovimientoDinero').on('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                try {
                    const resp = await fetch(`${CONTROLLER}?action=abonar`, {
                        method: 'POST',
                        body: formData
                    });

                    const res = await resp.json();
                    if (res.success) {
                        Swal.fire('Éxito', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message || 'Error', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'Error en la petición', 'error');
                }
            });
        });

        // =====================================================
        // FUNCIONES DE CARGA Y MODALES
        // =====================================================
        async function cargarDatosAlmacen(almacenId, esParaModal = false) {

            const periodo = $('#periodo').val() || 'hoy';
            const f_inicio = $('#f_inicio').val() || '';
            const f_fin = $('#f_fin').val() || '';

            try {
                const resp = await fetch(
                    `/myvet/app/controllers/faltasController.php?action=ajax&almacen_id=${almacenId}&periodo=${periodo}&f_inicio=${f_inicio}&f_fin=${f_fin}`
                );

                const data = await resp.json();

                if (data.status === 'success') {

                    // =========================
                    // 🔥 WIDGET DEUDA
                    // =========================


                    // =========================
                    // MODAL
                    // =========================
                    if (esParaModal) {
                        let htmlT = '<option value="0">Seleccione un trabajador</option>';
                        data.trabajadores.forEach(t => {
                            htmlT += `<option value="${t.id}">${t.nombre}</option>`;
                        });
                        $('#modal_trabajador_id').html(htmlT);
                    }

                    // =========================
                    // TABLA
                    // =========================
                    else {
                        tabla.clear();

                        data.prestamos.forEach(p => {

                            const montoTotal = parseFloat(p.monto || 0);
                            const rowNode = tabla.row.add([
                                `<span >${p.fecha ?? ''}</span>`,

                                ` <span class="fw-semibold">
                        <i class="bi bi-person-circle text-secondary me-1"></i>
                        ${p.trabajador}
                    </span>`,
                                ` <small class="fw-semibold">
                       ${p.tipo}
                    </small>`, ` <small class="text-body-secondary">
                       ${p.descripcion}
                    </small>`,
                                `  <span class="text-end badge bg-success fs-6 px-3 py-2">
                        $${p.monto}
                    </span>`,

                                `<div class="text-end">
             
                       <button
                        class="btn btn-outline-warning btn-sm rounded-pill px-3"
                        onclick="imprimirComprobanteFalta(this)"
                        title="Imprimir comprobante">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Comprobante
                    </button>
                            <button
                        class="btn btn-outline-danger btn-sm rounded-pill px-3"
                        onclick="eliminarPrestamo(${p.id})"
                        title="Eliminar préstamo">

                        <i class="bi bi-trash3 me-1"></i>
                        Eliminar

                    </button>
                            
                                
                               
                            
                        </div>`
                            ]).node();

                            $(rowNode).attr('data-almacen', p.almacen_id);
                        });

                        tabla.draw();

                        $('#status-almacen').text(
                            `Mostrando datos de: ${almacenId == 0 ? 'Todas las sucursales' : 'Sucursal seleccionada'}`
                        );
                    }
                }

            } catch (e) {
                console.error("Error cargando datos:", e);
            }
        }

        function nuevoPrestamo() {
            $('#formPrestamo')[0].reset();
            const modal = new bootstrap.Modal(document.getElementById('modalPrestamo'));
            modal.show();
            if ($('#modal_almacen_id').val()) {
                $('#modal_almacen_id').trigger('change');
            }
        }

        function modalAbonar(id, nombre, saldo, almacen_id) {
            const form = $('#formMovimientoDinero')[0];
            if (form) form.reset();

            $('#wrap_caja, #wrap_banco').addClass('d-none');
            $('#mov_prestamo_id').val(id);
            $('#mov_prestamo_id_text').text(id);
            $('#mov_saldo_text').text(saldo.toFixed(2));
            $('#mov_almacen_id').val(almacen_id);

            const modalEl = document.getElementById('modalMovimientoDinero');
            let modal = bootstrap.Modal.getInstance(modalEl);
            if (!modal) modal = new bootstrap.Modal(modalEl);
            modal.show();
        }





        $(document).on('change', '#modal_trabajador_id', function () {

            const trabajador_id = $(this).val();

            const $widget = $('.deuda-value'); // 👈 AQUÍ está la clave

            console.log("WIDGET ENCONTRADO:", $widget.length);

            if (!trabajador_id || trabajador_id == 0) {
                $widget.text('$0.00');
                return;
            }

            $.ajax({
                url: CONTROLLER,
                type: 'GET',
                data: {
                    action: 'deudaTrabajador',
                    trabajador_id: trabajador_id
                },
                dataType: 'json',

                success: function (res) {

                    console.log("RESPUESTA:", res);

                    if (res.status === 'success') {

                        const deuda = parseFloat(res.deuda?.deuda_total ?? 0);

                        const formatted = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(deuda);

                        console.log("DEUDA:", formatted);

                        $widget
                            .css('background', 'yellow')
                            .text(formatted);

                    } else {
                        $widget.text('$0.00');
                    }
                },

                error: function (xhr) {
                    console.error(xhr.responseText);
                    $widget.text('$0.00');
                }
            });
        });



        // function verPrestamo(id) {

        //     $.ajax({
        //         url: CONTROLLER,
        //         type: 'GET',
        //         data: {
        //             action: 'detalle',
        //             id: id
        //         },
        //         dataType: 'json',

        //         success: function(res) {
        //             if (!res.success) return;

        //             const p = res.prestamo;
        //             const abonos = res.abonos;

        //             const f = new Intl.NumberFormat('es-MX', {
        //                 style: 'currency',
        //                 currency: 'MXN'
        //             });

        //             // Datos
        //             $('#mp_trabajador_u').text(p.trabajador);
        //             $('#mp_monto_u').text(f.format(p.monto_total));
        //             $('#mp_abonado_u').text(f.format(p.total_abonado));
        //             $('#mp_saldo_u').text(f.format(p.saldo_pendiente));
        //             $('#mp_desc_u').text(p.descripcion || '-');

        //             // Estado
        //             let estado = `<span class="badge bg-secondary">${p.estado}</span>`;
        //             if (p.estado === 'pagado') {
        //                 estado = `<span class="badge bg-success">Pagado</span>`;
        //             } else if (p.estado === 'pendiente') {
        //                 estado = `<span class="badge bg-warning text-dark">Pendiente</span>`;
        //             }

        //             $('#mp_estado_u').html(estado);

        //             // Tabla
        //             let html = '';
        //             if (abonos.length) {
        //                 abonos.forEach(a => {
        //                     html += `
        //                     <tr>
        //                         <td>${a.numero_pago}</td>
        //                         <td class="text-success fw-semibold">${f.format(a.monto_abono)}</td>
        //                         <td>${a.metodo_pago}</td>
        //                         <td class="small">${a.fecha_abono}</td>
        //                         <td class="small">${a.observaciones || '-'}</td>
        //                     </tr>
        //                 `;
        //                 });
        //             } else {
        //                 html = `<tr><td colspan="5" class="text-center text-body-secondary">Sin abonos</td></tr>`;
        //             }

        //             $('#tablaAbonosPrestamoUnique').html(html);

        //             // Mostrar modal (IMPORTANTE: ID único)
        //             const modal = new bootstrap.Modal(
        //                 document.getElementById('modalPrestamoDetalleUnique')
        //             );
        //             modal.show();
        //         }
        //     });
        // }
        // </script>
    <script>
        function eliminarPrestamo(id) {

            Swal.fire({
                title: '¿Eliminar falta?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {

                if (!result.isConfirmed) return;

                try {

                    const resp = await fetch(`${CONTROLLER}?action=eliminarFalta&id=${id}`, {
                        method: 'GET'
                    });

                    const res = await resp.json();

                    if (res.success) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: res.message || 'Falda eliminada',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // recargar datos o tabla
                        setTimeout(() => location.reload(), 1000);

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'No se puede eliminar',
                            text: res.message || 'Tiene abonos registrados'
                        });
                    }

                } catch (err) {
                    console.error(err);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error en la petición'
                    });
                }
            });
        }
        /**
               * Lee los datos de la fila y genera un comprobante de
               * FALTA o RETARDO con sanción, listo para imprimir y firmar.
               */
        function imprimirComprobanteFalta(boton) {

            // ============================================================
            // 1. Obtener la fila y sus celdas
            // ============================================================
            const fila = boton.closest('tr');
            if (!fila) return;

            const celdas = fila.querySelectorAll('td');

            const fecha = celdas[0]?.textContent.trim() || '';
            const trabajador = celdas[1]?.textContent.trim() || '';
            const tipo = celdas[2]?.textContent.trim() || '';
            const descripcion = celdas[3]?.textContent.trim() || '';
            const montoRaw = celdas[4]?.textContent.trim() || '$0.00';
            const monto = montoRaw.replace(/[^\d.,]/g, '');

            // ============================================================
            // 2. Determinar si es FALTA o RETARDO
            // ============================================================
            const tipoLower = tipo.toLowerCase();
            const esRetardo = tipoLower.includes('retardo') || tipoLower.includes('retardo');
            const esFalta = tipoLower.includes('falta') || tipoLower.includes('ausencia');

            let tituloDoc = 'Comprobante de Falta';
            let tipoSancion = 'Falta';
            let textoDeclaracion = '';

            if (esRetardo) {
                tituloDoc = 'Comprobante de Retardo con Sanción';
                tipoSancion = 'Retardo';
                textoDeclaracion = `
            Se hace constar que el trabajador <strong>${trabajador}</strong> presentó un
            <strong>retardo</strong> el día <strong>${fecha}</strong>, motivo por el cual
            se aplica una sanción económica de <strong>$${monto}</strong> conforme al
            reglamento interno de trabajo.
        `;
            } else {
                // Por defecto FALTA
                tituloDoc = 'Comprobante de Falta';
                tipoSancion = 'Falta';
                textoDeclaracion = `
            Se hace constar que el trabajador <strong>${trabajador}</strong> incurrió en
            una <strong>falta</strong> el día <strong>${fecha}</strong>, motivo por el cual
            se aplica el descuento correspondiente a <strong>un día de trabajo</strong>,
            equivalente a <strong>$${monto}</strong>.
        `;
            }

            // ============================================================
            // 3. Datos generales
            // ============================================================
            const fechaHoy = new Date().toLocaleDateString('es-MX', {
                day: '2-digit', month: 'short', year: 'numeric'
            });

            const folio = (esRetardo ? 'RET-' : 'FAL-') + Date.now().toString().slice(-8);

            // ============================================================
            // 4. Abrir ventana
            // ============================================================
            const ventana = window.open('', '_blank', 'width=800,height=600');
            if (!ventana) {
                alert('Permite las ventanas emergentes para imprimir el comprobante.');
                return;
            }

            // ============================================================
            // 5. HTML del comprobante
            // ============================================================
            const html = `
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>${tituloDoc} ${folio}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f7;
            padding: 15px;
            color: #222;
            font-size: 12px;
        }

        .comprobante {
            width: 19.5cm;
            min-height: 12cm;
            margin: 0 auto;
            background: #fff;
            padding: 18px 22px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .comprobante::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d99418, #f0b04d);
            border-radius: 8px 8px 0 0;
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #d99418;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-izq {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-izq .titulo-doc {
            font-size: 1rem;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-izq .titulo-doc small {
            display: block;
            font-size: 0.6rem;
            font-weight: normal;
            color: #888;
            text-transform: none;
            letter-spacing: 0;
            margin-top: 2px;
        }
        .header-der {
            text-align: right;
            font-size: 0.7rem;
            color: #666;
        }
        .header-der .fecha {
            font-weight: bold;
            color: #333;
        }

        /* ===== BADGE TIPO ===== */
        .badge-tipo {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .badge-falta {
            background: #fff5f5;
            color: #c82333;
            border: 1px solid #ffb8b8;
        }
        .badge-retardo {
            background: #fff8e8;
            color: #b8860b;
            border: 1px solid #f0d9a0;
        }

        /* ===== TRABAJADOR ===== */
        .trabajador {
            background: #f8fafc;
            border-left: 3px solid #d99418;
            padding: 7px 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .trabajador .label {
            font-size: 0.62rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .trabajador .valor {
            font-weight: bold;
            font-size: 0.9rem;
            color: #222;
        }

        /* ===== MONTO DESTACADO ===== */
        .monto-card {
            border-radius: 6px;
            padding: 10px 14px;
            text-align: center;
            margin-bottom: 12px;
        }
        .monto-card.falta {
            background: linear-gradient(135deg, #fff5f5, #ffe0e0);
            border: 1px solid #ffb8b8;
        }
        .monto-card.falta .label { color: #991b1b; }
        .monto-card.falta .valor { color: #c82333; }

        .monto-card.retardo {
            background: linear-gradient(135deg, #fff8e8, #ffefc8);
            border: 1px solid #f0d9a0;
        }
        .monto-card.retardo .label { color: #8a6100; }
        .monto-card.retardo .valor { color: #b8860b; }

        .monto-card .label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        .monto-card .valor {
            font-size: 1.4rem;
            font-weight: bold;
            margin-top: 2px;
        }

        /* ===== FILAS DE DATOS ===== */
        .fila {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #e0e0e0;
            font-size: 0.78rem;
        }
        .fila .label { color: #666; }
        .fila .valor { font-weight: 600; text-align: right; }

        /* ===== DECLARACIÓN ===== */
        .declaracion {
            font-size: 0.72rem;
            line-height: 1.5;
            text-align: justify;
            color: #444;
            margin-top: 12px;
            padding: 8px 10px;
            background: #fafbfc;
            border-radius: 4px;
            border-left: 3px solid #d99418;
        }
        .declaracion strong { color: #b8860b; }

        /* ===== FIRMAS ===== */
        .firmas {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            gap: 30px;
        }
        .firma { text-align: center; flex: 1; }
        .firma-linea {
            border-top: 1px solid #333;
            margin-bottom: 4px;
        }
        .firma-nombre {
            font-size: 0.72rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .firma-rol {
            font-size: 0.6rem;
            color: #888;
        }

        /* ===== FOOTER ===== */
        .footer-nota {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            font-size: 0.58rem;
            color: #999;
            text-align: center;
            font-style: italic;
        }

        /* ===== BOTONES ===== */
        .no-print { text-align: center; margin: 15px 0; }
        .btn-print {
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
            background: #d99418;
            color: white;
            border: none;
            border-radius: 6px;
            margin-right: 8px;
        }
        .btn-close {
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
            background: #e0e0e0;
            border: none;
            border-radius: 6px;
        }

        /* ===== IMPRESIÓN ===== */
        @page {
            size: 21.59cm 13.97cm;
            margin: 0.5cm;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .comprobante {
                box-shadow: none;
                border: none;
                padding: 10px 15px;
                width: 100%;
                min-height: auto;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>
        <button class="btn-close" onclick="window.close()">Cerrar</button>
    </div>

    <div class="comprobante">

        <!-- HEADER: TÍTULO + FECHA AL LADO -->
        <div class="header">
            <div class="header-izq">
                <div class="titulo-doc">
                    ${tituloDoc}
                   
                </div>
            </div>
            <div class="header-der">
                <div class="fecha">${fechaHoy}</div>
               
            </div>
        </div>

        <!-- BADGE TIPO -->
        <div class="badge-tipo ${esRetardo ? 'badge-retardo' : 'badge-falta'}">
            ${esRetardo ? '⚠ Retardo con sanción' : '✖ Falta de asistencia'}
        </div>

        <!-- TRABAJADOR -->
        <div class="trabajador">
            <span class="label">Trabajador</span>
            <span class="valor">${trabajador}</span>
        </div>

        <!-- MONTO DESTACADO -->
        <div class="monto-card ${esRetardo ? 'retardo' : 'falta'}">
            <div class="label">${esRetardo ? 'Monto de la sanción' : 'Descuento por falta'}</div>
            <div class="valor">$${monto}</div>
        </div>

        <!-- DATOS -->
        <div class="fila">
            <span class="label">Fecha del incidente:</span>
            <span class="valor">${fecha}</span>
        </div>
        <div class="fila">
            <span class="label">Tipo:</span>
            <span class="valor">${tipo}</span>
        </div>
        <div class="fila" style="border-bottom: none;">
            <span class="label">Observaciones:</span>
            <span class="valor" style="font-weight: 400; max-width: 60%;">${descripcion || 'Sin observaciones'}</span>
        </div>

        <!-- DECLARACIÓN -->
        <div class="declaracion">
            ${textoDeclaracion}
        </div>

        <!-- FIRMAS -->
        <div class="firmas">
            <div class="firma">
                <div class="firma-linea"></div>
                <div class="firma-nombre">${trabajador}</div>
                <div class="firma-rol">Trabajador (Enterado)</div>
            </div>
            <div class="firma">
                <div class="firma-linea"></div>
                <div class="firma-nombre">Administración</div>
                <div class="firma-rol">Autorizó / Aplicó sanción</div>
            </div>
        </div>

        <div class="footer-nota">
            Documento generado el ${fechaHoy}. Este comprobante es válido como constancia de la sanción aplicada.
        </div>

    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 400);
        });
    <\/script>

</body>
</html>
    `;

            ventana.document.open();
            ventana.document.write(html);
            ventana.document.close();
        }
    </script>
</body>

</html>