<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0);
$paginaActual = $paginaActual ?? 'prestamos';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos Trabajadores | cfsistem</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
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
                <h2 class="fw-bold m-0">Préstamos a Trabajadores</h2>
                <small class="text-body-secondary" id="status-almacen">Mostrando datos de:
                    <?= $almacen_usuario == 0 ? 'Todas las sucursales' : 'Sucursal Actual' ?></small>
            </div>

            <button class="btn btn-primary rounded-pill px-4" onclick="nuevoPrestamo()">
                <i class="bi bi-cash-stack me-2"></i> Nuevo Préstamo
            </button>
        </div>
        <div class="col-md-4">
            <div class="glass-card deuda-widget position-relative overflow-hidden animate__animated animate__fadeInUp">

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-circle bg-danger-subtle text-danger">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <div class="small text-body-secondary">Deuda Total</div>
                            <div id="deudaTotal" class="fw-bold fs-4">$0.00</div>
                        </div>
                    </div>

                    <span class="badge bg-danger-subtle text-danger small">
                        Activa
                    </span>
                </div>

                <!-- Barra visual -->
                <div class="progress mt-2" style="height: 6px;">
                    <div id="barraDeuda" class="progress-bar bg-danger" style="width: 0%"></div>
                </div>

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
                <table id="tablaPrestamos" class="table align-middle w-100">
                    <thead>
                        <tr>
                            <th>Almacen</th>
                            <th>Trabajador</th>
                            <th>Descripción</th>
                            <th>Total</th>
                            <th>Abonado</th>
                            <th>Saldo</th>
                            <th>Progreso</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPrestamos">
                        <?php foreach($prestamos as $p): 
                        $abonado = $p['total_abonado'] ?? 0;
                        $saldo_p = $p['saldo_pendiente'] ?? ($p['monto_total'] - $abonado);
                        $porcentaje = $p['monto_total'] > 0 ? ($abonado / $p['monto_total']) * 100 : 0;
                    ?>
                        <tr data-almacen="<?= $p['almacen_id'] ?>">
                            <td><?= $p['almacen_id'] ?></td>
                            <td class="fw-semibold"><?= $p['trabajador'] ?></td>
                            <td class="small text-body-secondary"><?= $p['descripcion'] ?></td>
                            <td class="fw-bold">$<?= number_format($p['monto_total'],2) ?></td>
                            <td class="text-success fw-bold">$<?= number_format($abonado,2) ?></td>
                            <td class="<?= $saldo_p > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' ?>">
                                $<?= number_format($saldo_p,2) ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $porcentaje ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-estado <?= $saldo_p > 0 ? 'estado-activo' : 'estado-liquidado' ?>">
                                    <?= $saldo_p > 0 ? 'PENDIENTE' : 'LIQUIDADO' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                                                           <button class="btn btn-sm btn-danger"
    onclick="eliminarPrestamo(<?= $p['id'] ?>)">
    <i class="bi bi-trash"></i>
</button>
                                <button class="btn btn-sm btn-light" onclick="verPrestamo(<?= $p['id'] ?>)"><i
                                        class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-success"
                                    onclick="modalAbonar(<?= $p['id'] ?>, '<?= addslashes($p['trabajador']) ?>', <?= $saldo_p ?>,<?= $p['almacen_id']?>)"><i
                                        class="bi bi-cash"></i></button>
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
                                Registrar Préstamo
                            </h5>
                            <small class="text-white-50">Gestión de créditos a trabajadores</small>
                        </div>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="formPrestamo">

                        <!-- BODY -->
                        <div class="modal-body p-4">

                            <div class="row g-3">

                                <!-- ALMACÉN -->
                                <div class="col-md-6">
                                    <label class="form-label text-body-secondary small">Almacén Origen</label>
                                    <select name="almacen_id" id="modal_almacen_id"
                                        class="form-select form-select-lg rounded-3" required>
                                       <?php if ($_SESSION['almacen_id']==0): ?>
                                                <option value="">Seleccionar ubicación...</option>
                                                <?php endif; ?>
                                        <?php foreach($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>"
                                            <?= ($almacen_usuario == $a['id']) ? 'selected' : '' ?>>
                                            <?= $a['nombre'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- TRABAJADOR -->
                                <div class="col-md-6">
                                    <label class="form-label text-body-secondary small">Trabajador</label>
                                    <select name="trabajador_id" id="modal_trabajador_id"
                                        class="form-select form-select-lg rounded-3" required>
                                        <option value="0">Seleccione trabajador</option>
                                        <?php foreach($trabajadores as $t): ?>
                                        <option value="<?= $t['id'] ?>">
                                            <?= $t['nombre'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- WIDGET DEUDA -->
                                <div class="col-12">
                                    <div id="widget-deuda-trabajador"
                                        class="d-flex justify-content-between align-items-center p-3 rounded-3 border ">

                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                                            <div class="small text-body-secondary">Deuda actual del trabajador</div>
                                        </div>

                                        <div class="deuda-value fw-bold fs-5 text-danger">
                                            $0.00
                                        </div>
                                    </div>
                                </div>

                                <!-- MONTO -->
                                <div class="col-md-6">
                                    <label class="form-label text-body-secondary small">Monto a prestar</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="monto_total" class="form-control"
                                            placeholder="0.00" required>
                                    </div>
                                </div>

                                <!-- MÉTODO -->
                                <div class="col-md-6">
                                    <label class="form-label text-body-secondary small">Método de pago</label>
                                    <select name="metodo_pago" class="form-select form-select-lg" required>
                                        <option value="efectivo">💵 Efectivo</option>
                                        <option value="tarjeta">💳 Tarjeta</option>
                                        <option value="transferencia">🏛️ Transferencia</option>
                                    </select>
                                </div>

                                <!-- DESCRIPCIÓN -->
                                <div class="col-12">
                                    <label class="form-label text-body-secondary small">Motivo / Descripción</label>
                                    <textarea name="descripcion" class="form-control text-uppercase rounded-3" rows="3"
                                        placeholder="Ej. Adelanto de quincena, emergencia, etc."></textarea>
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
                                      <?php if ($_SESSION['almacen_id']==0): ?>
                                                <option value="">Seleccionar ubicación...</option>
                                                <?php endif; ?>
                                        <?php foreach($almacenes as $a): ?>
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
                                        <?php foreach($cajasFuertes as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 d-none" id="wrap_banco">
                                    <label class="form-label">Seleccionar Banco</label>
                                    <select name="banco_id" id="select_banco" class="form-select">
                                        <option value="">-- Seleccionar Banco --</option>
                                        <?php foreach($bancos as $b): ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control text-uppercase" rows="2"></textarea>
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
        <div class="modal-content shadow rounded-4 border-0">

            <!-- HEADER -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                        style="width: 46px; height: 46px; background: linear-gradient(135deg, #eaf2ff, #dbe8ff); color: #2855b8;">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Detalle del Préstamo</h5>
                        <small class="text-body-secondary">Información del préstamo y sus abonos</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body pt-4">

                <!-- TRABAJADOR -->
                <div class="mb-3">
                    <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                        style="background: linear-gradient(135deg, #f8fafc, #eef4ff); border: 1px solid #e5ecf6;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px; background: #2855b8; color: #fff;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-body-secondary d-block" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Trabajador
                            </small>
                            <div id="mp_trabajadorNombre_u" class="fw-bold fs-6"></div>
                        </div>
                    </div>
                </div>

                <!-- ESTADO + MONTOS -->
                <div class="row g-3 mb-3">

                    <!-- ESTADO -->
                    <div class="col-12">
                        <small class="text-body-secondary d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            Estado
                        </small>
                        <div id="mp_estado_u"></div>
                    </div>

                    <!-- MONTO -->
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 h-100"
                            style="background: #f8fafc; border: 1px solid #e5ecf6;">
                            <small class="text-body-secondary d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-cash me-1"></i>Monto
                            </small>
                            <div id="mp_monto_u" class="fw-bold fs-5 text-dark"></div>
                        </div>
                    </div>

                    <!-- ABONADO -->
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 h-100"
                            style="background: #f0f9ff; border: 1px solid #cfe8ff;">
                            <small class="text-body-secondary d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-check-circle me-1"></i>Abonado
                            </small>
                            <div id="mp_abonado_u" class="fw-bold fs-5 text-primary"></div>
                        </div>
                    </div>

                    <!-- SALDO -->
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 h-100"
                            style="background: #fff5f5; border: 1px solid #ffd9d9;">
                            <small class="text-body-secondary d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-exclamation-circle me-1"></i>Saldo
                            </small>
                            <div id="mp_saldo_u" class="fw-bold fs-5 text-danger"></div>
                        </div>
                    </div>

                </div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-4">
                    <small class="text-body-secondary d-block mb-2" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="bi bi-chat-left-text me-1"></i>Descripción
                    </small>
                    <div id="mp_desc_u" class="p-3 rounded-3 small"
                        style="background: #f8fafc; border: 1px solid #e5ecf6; color: #4b5563;">
                    </div>
                </div>

                <!-- HISTORIAL DE ABONOS -->
                <div class="mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="text-body-secondary" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-clock-history me-1"></i>Historial de abonos
                        </small>
                    </div>

                    <div class="table-responsive rounded-3 overflow-hidden" style="border: 1px solid #e5ecf6;">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                                    <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Monto</th>
                                    <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Método</th>
                                    <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Fecha</th>
                                    <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Obs</th>
                                </tr>
                            </thead>
                            <tbody id="tablaAbonosPrestamoUnique"></tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0 d-flex justify-content-between pt-0">
                <button type="button" class="btn btn-light border rounded-pill px-4"
                        data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cerrar
                </button>

                <button type="button" class="btn rounded-pill px-4 fw-semibold shadow-sm"
                        id="btnImprimirComprobantePrestamo"
                        onclick="imprimirComprobantePrestamo()"
                        style="background: linear-gradient(135deg, #28a745, #1e7e34); color: #fff; border: 0;">
                    <i class="bi bi-printer me-1"></i> Imprimir comprobante
                </button>
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
    const CONTROLLER = '/myvet/app/controllers/prestamosController.php';

    $(document).ready(function() {

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
        $('#busqueda').on('keyup', function() {
            tabla.search(this.value).draw();
        });

        // =========================
        // FILTRO SUCURSAL
        // =========================
        $('#filtroSucursal').on('change', function() {
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

        $('#periodo').on('change', function() {
            recargarFiltros();
        });

        $('#f_inicio, #f_fin').on('change', function() {
            $('#periodo').val('personalizado');
            recargarFiltros();
        });

        // =========================
        // CAMBIO ALMACÉN MODAL
        // =========================
        $('#modal_almacen_id').on('change', function() {
            cargarDatosAlmacen(this.value, true);
        });

        // =====================================================
        // 🔥 LÓGICA DE DESTINO (Caja Fuerte / Banco)
        // =====================================================
        $(document).on('change', '#tipo_destino', function() {
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
        $('#formPrestamo').on('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const resp = await fetch(`${CONTROLLER}?action=crear`, {
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
        $('#formMovimientoDinero').on('submit', async function(e) {
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
                `${CONTROLLER}?action=ajax&almacen_id=${almacenId}&periodo=${periodo}&f_inicio=${f_inicio}&f_fin=${f_fin}`
            );

            const data = await resp.json();

            if (data.status === 'success') {

                // =========================
                // 🔥 WIDGET DEUDA
                // =========================
                if (data.deuda && typeof data.deuda.deuda_total !== 'undefined') {

                    const deuda = parseFloat(data.deuda.deuda_total || 0);

                    const formatter = new Intl.NumberFormat('es-MX', {
                        style: 'currency',
                        currency: 'MXN'
                    });

                    $('#deudaTotal').text(formatter.format(deuda));

                    // barra visual (ajusta el max si quieres)
                    const max = 10000;
                    const porcentaje = Math.min((deuda / max) * 100, 100);

                    $('#barraDeuda').css('width', porcentaje + '%');
                }

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
                        const abonado = parseFloat(p.total_abonado || 0);
                        const montoTotal = parseFloat(p.monto_total || 0);
                        const saldoP = parseFloat(p.saldo_pendiente || (montoTotal - abonado));
                        const porcentaje = montoTotal > 0 ? (abonado / montoTotal) * 100 : 0;

                        const rowNode = tabla.row.add([
                            p.nombreAlmacen,
                            `<span class="fw-semibold">${p.trabajador}</span>`,
                            `<span class="small text-body-secondary">${p.descripcion || ''}</span>`,
                            `$${montoTotal.toFixed(2)}`,
                            `<span class="text-success fw-bold">$${abonado.toFixed(2)}</span>`,
                            `<span class="${saldoP > 0 ? 'text-danger' : 'text-success'} fw-bold">$${saldoP.toFixed(2)}</span>`,
                            `<div class="progress"><div class="progress-bar" style="width: ${porcentaje}%"></div></div>`,
                            `<span class="badge-estado ${saldoP > 0 ? 'estado-activo' : 'estado-liquidado'}">
                            ${saldoP > 0 ? 'PENDIENTE' : 'LIQUIDADO'}
                        </span>`,
                            `<div class="text-end">
             
                      
                            <button class="btn btn-sm btn-light" onclick="verPrestamo(${p.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                                           <button class="btn btn-sm btn-danger"
    onclick="eliminarPrestamo( ${p.id})">
    <i class="bi bi-trash"></i>
</button>
                            ${saldoP > 0 
                                ? `<button class="btn btn-sm btn-success"
                                        onclick="modalAbonar(${p.id}, '${p.trabajador.replace(/'/g, "\\'")}', ${saldoP}, ${p.almacen_id})">
                                        <i class="bi bi-cash"></i>
                                   </button>` 
                                : '' 
                            }
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





    $(document).on('change', '#modal_trabajador_id', function() {

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

            success: function(res) {

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

            error: function(xhr) {
                console.error(xhr.responseText);
                $widget.text('$0.00');
            }
        });
    });



    function verPrestamo(id) {

        $.ajax({
            url: CONTROLLER,
            type: 'GET',
            data: {
                action: 'detalle',
                id: id
            },
            dataType: 'json',

            success: function(res) {
                if (!res.success) return;

                const p = res.prestamo;
                const abonos = res.abonos;

                const f = new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });

                // Datos
                $('#mp_trabajador_u').text(p.trabajador);
                $('#mp_monto_u').text(f.format(p.monto_total));
                $('#mp_abonado_u').text(f.format(p.total_abonado));
                $('#mp_saldo_u').text(f.format(p.saldo_pendiente));
                $('#mp_desc_u').text(p.descripcion || '-');

                // Estado
                let estado = `<span class="badge bg-secondary">${p.estado}</span>`;
                if (p.estado === 'pagado') {
                    estado = `<span class="badge bg-success">Pagado</span>`;
                } else if (p.estado === 'pendiente') {
                    estado = `<span class="badge bg-warning text-dark">Pendiente</span>`;
                }

                $('#mp_estado_u').html(estado);

                // Tabla
                let html = '';
                if (abonos.length) {
                    abonos.forEach(a => {
                        html += `
                        <tr>
                            <td>${a.numero_pago}</td>
                            <td class="text-success fw-semibold">${f.format(a.monto_abono)}</td>
                            <td>${a.metodo_pago}</td>
                            <td class="small">${a.fecha_abono}</td>
                            <td class="small">${a.observaciones || '-'}</td>
                        </tr>
                    `;
                    });
                } else {
                    html = `<tr><td colspan="5" class="text-center text-body-secondary">Sin abonos</td></tr>`;
                }

                $('#tablaAbonosPrestamoUnique').html(html);

                // Mostrar modal (IMPORTANTE: ID único)
                const modal = new bootstrap.Modal(
                    document.getElementById('modalPrestamoDetalleUnique')
                );
                modal.show();
            }
        });
    }
    </script>
<script>
    function eliminarPrestamo(id) {

    Swal.fire({
        title: '¿Eliminar préstamo?',
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

            const resp = await fetch(`${CONTROLLER}?action=eliminarPrestamo&id=${id}`, {
                method: 'GET'
            });

            const res = await resp.json();

            if (res.success) {

                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: res.message || 'Préstamo eliminado',
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


function imprimirComprobantePrestamo() {

    // ============================================================
    // 1. Leer datos del DOM del modal
    // ============================================================
    const estado      = document.getElementById('mp_estado_u')?.textContent.trim()             || 'N/R';
    const monto       = document.getElementById('mp_monto_u')?.textContent.trim()              || '$ 0.00';
    const abonado     = document.getElementById('mp_abonado_u')?.textContent.trim()            || '$ 0.00';
    const saldo       = document.getElementById('mp_saldo_u')?.textContent.trim()              || '$ 0.00';
    const descripcion = document.getElementById('mp_desc_u')?.textContent.trim()               || 'Sin descripción';
    const trabajador  = document.getElementById('mp_trabajadorNombre_u')?.textContent.trim()  || '';

    // Leer tabla de abonos
    const abonos = [];
    document.querySelectorAll('#tablaAbonosPrestamoUnique tr').forEach(tr => {
        const tds = tr.querySelectorAll('td');
        if (tds.length >= 5) {
            abonos.push({
                num:    tds[0].textContent.trim(),
                monto:  tds[1].textContent.trim(),
                metodo: tds[2].textContent.trim(),
                fecha:  tds[3].textContent.trim(),
                obs:    tds[4].textContent.trim()
            });
        }
    });

    // ============================================================
    // 2. Fecha y folio
    // ============================================================
    const fechaHoy = new Date().toLocaleDateString('es-MX', {
        day: '2-digit', month: 'short', year: 'numeric'
    });
    const folio = 'PR-' + Date.now().toString().slice(-8);

    // ============================================================
    // 3. Abrir ventana
    // ============================================================
    const ventana = window.open('', '_blank', 'width=800,height=600');
    if (!ventana) {
        alert('Permite las ventanas emergentes para imprimir el comprobante.');
        return;
    }

    // ============================================================
    // 4. HTML del comprobante (media hoja)
    // ============================================================
    const html = `
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante ${folio}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f7;
            padding: 15px;
            color: #222;
            font-size: 12px;
        }

        /* Media carta = 21.59 x 13.97 cm aprox */
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
            background: linear-gradient(90deg, #2855b8, #4a90e2);
            border-radius: 8px 8px 0 0;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #2855b8;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .logo h1 {
            color: #2855b8;
            font-size: 1.3rem;
            letter-spacing: 1px;
            line-height: 1;
        }
        .logo small {
            color: #888;
            font-size: 0.6rem;
        }
        .folio { text-align: right; font-size: 0.7rem; }
        .folio .numero {
            font-weight: bold;
            color: #2855b8;
            font-size: 0.85rem;
        }
        .folio .fecha { color: #888; }

        /* TÍTULO */
        .titulo {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.75rem;
            font-weight: bold;
            color: #2855b8;
            margin: 8px 0 12px;
            padding: 6px;
            background: #f0f4fa;
            border-radius: 4px;
        }

        /* TRABAJADOR */
        .trabajador {
            background: #f8fafc;
            border-left: 3px solid #2855b8;
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

        /* GRID MONTOS */
        .montos {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 10px;
        }
        .monto-card {
            padding: 7px 10px;
            border-radius: 5px;
            border: 1px solid #e5ecf6;
            background: #fafbfc;
        }
        .monto-card .label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: #888;
            letter-spacing: 0.5px;
        }
        .monto-card .valor {
            font-weight: bold;
            font-size: 0.95rem;
            margin-top: 2px;
        }
        .monto-card.destacado {
            background: linear-gradient(135deg, #eaf7ee, #d4efe0);
            border-color: #b8e0c5;
        }
        .monto-card.destacado .valor { color: #0d6832; }
        .monto-card.abonado .valor    { color: #2855b8; }
        .monto-card.saldo .valor      { color: #c82333; }

        /* CAMPOS SIMPLES */
        .fila {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #e0e0e0;
            font-size: 0.78rem;
        }
        .fila .label { color: #666; }
        .fila .valor { font-weight: 600; text-align: right; }

        /* TABLA ABONOS */
        .tabla-wrap {
            margin-top: 8px;
            border: 1px solid #e5ecf6;
            border-radius: 5px;
            overflow: hidden;
        }
        table.abonos {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.7rem;
        }
        table.abonos th {
            background: #f0f4fa;
            color: #2855b8;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 7px;
            text-align: left;
            border-bottom: 1px solid #d5e0f0;
        }
        table.abonos td {
            padding: 4px 7px;
            border-bottom: 1px solid #f0f0f0;
        }
        table.abonos tr:last-child td { border-bottom: none; }

        /* SECCIÓN TÍTULO */
        .seccion-titulo {
            font-size: 0.65rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2855b8;
            margin: 10px 0 5px;
            padding-bottom: 2px;
            border-bottom: 1px solid #e5ecf6;
        }

        /* DECLARACIÓN */
        .declaracion {
            font-size: 0.72rem;
            line-height: 1.5;
            text-align: justify;
            color: #444;
            margin-top: 8px;
        }
        .declaracion strong { color: #2855b8; }

        /* FIRMAS */
        .firmas {
            display: flex;
            justify-content: space-around;
            margin-top: 25px;
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

        /* FOOTER */
        .footer-nota {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            font-size: 0.58rem;
            color: #999;
            text-align: center;
            font-style: italic;
        }

        /* BOTONES (no se imprimen) */
        .no-print {
            text-align: center;
            margin: 15px 0;
        }
        .btn-print {
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
            background: #2855b8;
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

        /* IMPRESIÓN: media carta */
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

        <!-- HEADER -->
        <div class="header">
            <div class="logo">
                
             
            </div>
            <div class="folio">
                <div class="numero">#${folio}</div>
                <div class="fecha">${fechaHoy}</div>
            </div>
        </div>

        <div class="titulo">Comprobante de Préstamo</div>

        <!-- TRABAJADOR -->
        ${trabajador ? `
        <div class="trabajador">
            <span class="label">Trabajador</span>
            <span class="valor">${trabajador}</span>
        </div>
        ` : ''}

        <!-- MONTOS EN 3 COLUMNAS -->
        <div class="montos">
            <div class="monto-card destacado">
                <div class="label">Monto</div>
                <div class="valor">${monto}</div>
            </div>
            <div class="monto-card abonado">
                <div class="label">Abonado</div>
                <div class="valor">${abonado}</div>
            </div>
            <div class="monto-card saldo">
                <div class="label">Saldo</div>
                <div class="valor">${saldo}</div>
            </div>
        </div>

        <!-- ESTADO -->
        <div class="fila">
            <span class="label">Estado:</span>
            <span class="valor">${estado}</span>
        </div>

        <!-- DESCRIPCIÓN -->
        ${descripcion && descripcion !== 'Sin descripción' ? `
        <div class="fila" style="border-bottom: none;">
            <span class="label">Descripción:</span>
            <span class="valor" style="font-weight: 400; text-align: right; max-width: 60%;">${descripcion}</span>
        </div>
        ` : ''}

        <!-- ABONOS -->
        ${abonos.length > 0 ? `
        <div class="seccion-titulo">Historial de abonos (${abonos.length})</div>
        <div class="tabla-wrap">
            <table class="abonos">
                <thead>
                    <tr>
                        <th style="width: 30px;">#</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Fecha</th>
                        <th>Obs.</th>
                    </tr>
                </thead>
                <tbody>
                    ${abonos.map(a => `
                        <tr>
                            <td>${a.num}</td>
                            <td>${a.monto}</td>
                            <td>${a.metodo}</td>
                            <td>${a.fecha}</td>
                            <td>${a.obs}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}

        <!-- DECLARACIÓN -->
        <div class="declaracion">
            El trabajador declara haber recibido la cantidad de <strong>${monto}</strong>
            en concepto de préstamo, comprometiéndose a cubrir el saldo pendiente conforme
            a los abonos registrados.
        </div>

        <!-- FIRMAS -->
        <div class="firmas">
            <div class="firma">
                <div class="firma-linea"></div>
                <div class="firma-nombre">${trabajador || 'Trabajador'}</div>
                <div class="firma-rol">Recibí conforme</div>
            </div>
            <div class="firma">
                <div class="firma-linea"></div>
                <div class="firma-nombre">Administración</div>
                <div class="firma-rol">Autorizó / Entregó</div>
            </div>
        </div>

        <div class="footer-nota">
            Documento generado por MyVet el ${fechaHoy}
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