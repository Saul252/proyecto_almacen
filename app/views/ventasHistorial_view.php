<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas | Sistema</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        .btn-animado-entrega {
            position: relative;
            overflow: hidden;
            color: #fff;
            font-weight: 600;
            letter-spacing: .3px;
            transition: all .25s ease;

            background: linear-gradient(270deg,
                    #7c3aed,
                    #ec4899,
                    #f97316,
                    #3b82f6,
                    #7c3aed);

            background-size: 600% 600%;
            animation: moverGradiente 8s ease infinite;

            box-shadow:
                0 4px 18px rgba(124, 58, 237, .35),
                0 2px 8px rgba(236, 72, 153, .25);
        }

        .btn-animado-entrega:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow:
                0 8px 24px rgba(124, 58, 237, .45),
                0 4px 14px rgba(236, 72, 153, .35);
        }

        .btn-animado-entrega:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        .btn-animado-entrega::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 80%;
            height: 100%;

            background: linear-gradient(120deg,
                    transparent,
                    rgba(255, 255, 255, .35),
                    transparent);

            animation: brillo 2.8s linear infinite;
        }

        @keyframes moverGradiente {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes brillo {
            0% {
                left: -120%;
            }

            100% {
                left: 140%;
            }
        }

        :root {
            --sidebar-width: 250px;
            --primary-dark: #2c3e50;
            --accent-color: #000000;
            --bg-body: #067df4;
        }

        body {

            overflow-x: hidden;
            padding-top: 20px;
            text-transform: uppercase !important;
        }

        .main-content {

            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .scroll-table {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--accent-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 12px;

        }

        .btn-action {
            background-color: var(--accent-color);
            color: white;

        }

        .btn-action:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .filter-card {

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .modal-header {
            background-color: var(--accent-color) !important;
            color: white;

        }

        .input-entrega {
            border: 2px solid #28a745 !important;
            max-width: 90px;
            text-align: center;
            font-weight: bold;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        /* Esto asegura que SweetAlert siempre esté por encima del modal de Bootstrap */
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold card-title-text m-0">Historial de Ventas</h3>
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
            </div>
            <div class="d-flex justify-content-end align-items-center gap-2 w-100">
                <!-- DROPDOWN MIS REPARTOS -->
                <div class="dropdown">
                    <button class="btn btn-add dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" style="border-radius: 10px; background: #123e77; color: #ffffff;">
                        <i class="bi bi-gear me-2"></i> Mis repartos
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow rounded-3">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2"
                                href="/myvet/app/controllers/misRepartosController.php">
                                <i class="bi bi-list-ul text-primary"></i>
                                Gestionar mis repartos
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- BOTÓN EXPORTAR EXCEL -->
                <button type="button"
                    class="btn btn-success rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm"
                    onclick="exportarVentasCSV()">
                    <i class="bi bi-file-earmark-excel-fill fs-5"></i> Exportar a Excel (CSV)
                </button>
            </div>
            <div class="card filter-card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscador</label>
                            <input type="text" id="f_search" class="form-control form-control-sm"
                                placeholder="Folio o Cliente..." onkeyup="getVentas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Entrega</label>
                            <select id="f_status" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="select-usuarios"
                                class="form-label fw-bold small text-body-secondary text-uppercase">Vendedor</label>
                            <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id"
                                onchange="getVentas()">
                                <option value=""> Seleccione vendedor</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Pago</label>
                            <select id="f_pago" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="deuda">Con Deuda</option>
                                <option value="pagado">Pagados</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Periodo</label>
                            <select id="f_rango" class="form-select form-select-sm" onchange="togglePerso()">
                                <option value="semana">Semana</option>
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
                                <option value="todos">Historial Completo</option>
                                <option value="personalizado">Rango...</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-none" id="div_p">
                            <label class="form-label small fw-bold">Fechas</label>
                            <div class="input-group input-group-sm">
                                <input type="date" id="f_ini" class="form-control" value="<?= date('Y-m-d') ?>"
                                    onchange="getVentas()">
                                <input type="date" id="f_fin" class="form-control" value="<?= date('Y-m-d') ?>"
                                    onchange="getVentas()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Ubicación</label>
                            <select id="f_almacen" class="form-select form-select-sm" onchange="getVentas()">
                                <?php if ($esadmin): ?>
                                    <option value="0">Todos</option><?php endif; ?>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>" <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                        <?= $a['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Factura</label>
                            <select id="estado_factura" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="1">Facturada</option>
                                <option value="0">No factuarada</option>

                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="scroll-table shadow-sm">
                <div class="table-responsive" style="max-height: 60vh;">
                    <table class="table table-hover align-middle mb-0" id="tablaVentas">
                        <thead>
                            <tr>

                                <th class="ps-3">Fecha</th>
                                <th>Folio</th>
                                <th>Almacén</th>
                                <th>Vendedor</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Saldo Cobro</th>
                                <th>Facturada</th>
                                <th class="text-center">Estado Entrega</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
            <!-- Modal expandido a 85vw para dar espacio a la nueva distribución -->
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 85vw;">
                <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                    <!-- HEADER DEL MODAL -->
                    <div class="modal-header border-bottom px-4 py-3 bg-body-tertiary">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                <i class="bi bi-bag-check-fill fs-5"></i>
                            </div>
                            <div>
                                <span class="text-uppercase text-white-secondary fw-semibold font-monospace"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px;">Gestión de Venta</span>
                                <h5 class="modal-title fw-bold text-white m-0 d-flex align-items-center gap-2">
                                    Folio: <span id="spanFolio" class="text-primary font-monospace">--</span>
                                </h5>
                            </div>
                        </div>

                        <!-- IDs Ocultos -->
                        <span id="IdFolio" class="d-none"></span>
                        <span id="Almacen_id" class="d-none"></span>

                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close">X</button>
                    </div>

                    <div class="modal-body p-4 bg-body">

                        <!-- BARRA SUPERIOR: KPI METRICS (4 Columnas) -->
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="p-3 rounded-3 h-100 bg-body-tertiary border border-body-subtle">
                                    <small class="d-block text-body-secondary text-uppercase fw-semibold mb-1"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-person me-1 text-primary"></i> Cliente
                                    </small>
                                    <span id="detCliente"
                                        class="fw-bold text-body small d-block text-truncate">--</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="p-3 rounded-3 h-100 bg-body-tertiary border border-body-subtle">
                                    <small class="d-block text-body-secondary text-uppercase fw-semibold mb-1"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-box-seam me-1 text-primary"></i> Almacén
                                    </small>
                                    <span id="detAlmacen"
                                        class="fw-bold text-body small d-block text-truncate">--</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="p-3 rounded-3 h-100 bg-body-tertiary border border-body-subtle">
                                    <small class="d-block text-body-secondary text-uppercase fw-semibold mb-1"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                                    </small>
                                    <span id="detVendedor"
                                        class="fw-bold text-body small d-block text-truncate">--</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="p-3 rounded-3 h-100 bg-body-tertiary border border-body-subtle">
                                    <small class="d-block text-body-secondary text-uppercase fw-semibold mb-1"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-receipt me-1 text-primary"></i> Folio / Factura
                                    </small>
                                    <span id="folioFactura"
                                        class="fw-bold text-body small d-block text-truncate">--</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">

                            <!-- PANEL IZQUIERDO: RESUMEN FINANCIERO Y BOTÓN DE ACCIÓN -->
                            <div class="col-12 col-lg-3 d-flex flex-column justify-content-between">

                                <div class="d-flex flex-column gap-3 mb-3">
                                    <!-- Total Venta -->
                                    <div class="p-3 rounded-3 bg-body-tertiary border border-body-subtle">
                                        <span class="d-block small text-body-secondary text-uppercase fw-bold mb-1"
                                            style="font-size: 0.7rem;">Total de Venta</span>
                                        <h3 id="detTotalLabel" class="fw-bold text-body m-0">$0.00</h3>
                                    </div>

                                    <!-- Saldo Pendiente -->
                                    <div
                                        class="p-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
                                        <span class="d-block small text-secondary text-uppercase fw-bold mb-1"
                                            style="font-size: 0.7rem;">
                                            <i class="bi bi-exclamation-circle me-1"></i> Saldo Pendiente
                                        </span>
                                        <h3 id="detSaldoLabel" class="fw-bold text-danger m-0">$0.00</h3>
                                    </div>
                                </div>

                                <!-- Botón de Acción Principal -->
                                <?php if ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 2): ?>
                                    <div id="contenedorBoton">
                                        <button id="btnHabilitar" class="btn btn-success w-100 mb-2 py-2 fw-bold"
                                            onclick="abrirModalDespachoVentaTotal($('#Almacen_id').text(), $('#IdFolio').text())">
                                            Nueva Entrega
                                        </button>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <!-- PANEL DERECHO: DETALLES E HISTORIALES -->
                            <div class="col-12 col-lg-9">

                                <!-- TABLA PRINCIPAL DE PRODUCTOS -->
                                <div class="mb-4">
                                    <h6 class="small fw-bold text-uppercase text-body-secondary mb-2">
                                        <i class="bi bi-cart-check me-1 text-primary"></i> Detalle de Productos
                                    </h6>

                                    <div class="table-responsive rounded-3 border border-body-subtle"
                                        style="max-height: 200px;">
                                        <table class="table table-hover table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase font-monospace text-body-secondary">
                                                    <th class="py-2 px-3">Producto</th>
                                                    <th class="text-center py-2">Venta</th>
                                                    <th class="text-center py-2">Surtido</th>
                                                    <th class="text-center py-2 text-danger">Falta</th>
                                                    <th class="text-center py-2 col-input d-none">Entrega</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyDetalle" class="small"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- SECCIÓN INFERIOR: HISTORIALES EN 2 COLUMNAS + REPARTOS EN BASE -->
                                <div class="row g-3">

                                    <!-- Historial de Entregas -->
                                    <div class="col-12 col-md-6">
                                        <h6 class="small fw-bold text-uppercase text-body-secondary mb-2">
                                            <i class="bi bi-truck me-1 text-primary"></i> Historial de Entregas
                                        </h6>
                                        <div class="table-responsive rounded-3 border border-body-subtle"
                                            style="max-height: 170px;">
                                            <table class="table table-hover table-sm align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="small text-uppercase font-monospace text-body-secondary">
                                                        <th class="py-2 px-2">Fecha</th>
                                                        <th class="py-2">Responsable</th>
                                                        <th class="py-2">Producto</th>
                                                        <th class="text-center py-2">Cant</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyHistorial" class="small"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Historial de Pagos -->
                                    <div class="col-12 col-md-6">
                                        <h6 class="small fw-bold text-uppercase text-body-secondary mb-2">
                                            <i class="bi bi-cash-stack me-1 text-success"></i> Historial de Pagos
                                        </h6>
                                        <div class="table-responsive rounded-3 border border-body-subtle"
                                            style="max-height: 170px;">
                                            <table class="table table-hover table-sm align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="small text-uppercase font-monospace text-body-secondary">
                                                        <th class="py-2 px-2">Fecha</th>
                                                        <th class="py-2">Monto</th>
                                                        <th class="py-2">Método</th>
                                                        <th class="py-2">Referencia</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyPagos" class="small"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Tabla de Repartos -->
                                    <div class="col-12 mt-3">
                                        <h6 class="small fw-bold text-uppercase text-body-secondary mb-2">
                                            <i class="bi bi-map me-1 text-warning"></i> Repartos
                                        </h6>
                                        <div class="table-responsive rounded-3 border border-body-subtle"
                                            style="max-height: 180px;">
                                            <table class="table table-hover table-sm align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="small text-uppercase font-monospace text-body-secondary">
                                                        <th class="py-2 px-3"># Reparto</th>
                                                        <th class="py-2 px-3">Folio de viaje</th>
                                                        <th class="py-2">Fecha Entrega</th>
                                                        <th class="py-2">Dirección</th>
                                                        <th class="text-center py-2">Ruta</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyRepartos" class="small"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                                <!-- Alerta de Cancelación -->
                                <h4 id="cancelado" class="fw-bold text-danger mt-3 text-center"></h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalAgregarFactura" tabindex="-1" aria-labelledby="modalAgregarFacturaLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-3  shadow">

                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold card-title-text fs-5" id="modalAgregarFacturaLabel">
                            <i class="bi bi-file-earmark-plus text-primary me-2"></i>Nueva Factura
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-3">
                        <form id="formFactura" onsubmit="event.preventDefault(); ">
                            <div class="mb-2">
                                <input type="hidden"
                                    class="form-control rounded-pill border-secondary border-opacity-25"
                                    id="id_venta_factura">

                                <label for="folio-factura"
                                    class="form-label fw-bold small text-body-secondary text-uppercase ls-wide">
                                    Folio o Número de Factura
                                </label>
                                <input type="text" class="form-control rounded-pill border-secondary border-opacity-25"
                                    id="folio-factura" placeholder="Ej. FACT-12345" required autocomplete="off">
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer border-top-0 pt-0 d-flex gap-2">
                        <button type="button"
                            class="btn btn-sm btn-light rounded-pill flex-grow-1 fw-bold text-body-secondary"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill flex-grow-1 fw-bold"
                            onclick="agregarFactura ($('#id_venta_factura').val(),$('#folio-factura').val())">
                            Guardar
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="modalCancelarVenta" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Cancelar Venta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="cancelar_id_venta">

                        <div class="mb-3">
                            <label class="form-label">Motivo de la cancelación</label>
                            <textarea id="cancelar_motivo" class="form-control text-uppercase" rows="4"
                                placeholder="Escriba el motivo..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="procesarCancelacion(true)">
                            Con Saldo a Favor
                        </button>

                        <button class="btn btn-danger" onclick="procesarCancelacion(false)">
                            Sin Saldo
                        </button>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <?php require_once __DIR__ . '/ventasHistorialModales/registarAbono.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/evidenciasEntregaModel.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/editarVentaModal.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/modalImprimirRuta.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/modalSolicitudCancelacion.php'; ?>
        <?php require_once __DIR__ . '/entregasComponets/modalEntregaVentas.php'; ?>

        <script>
            let modalCancelarVenta;

            document.addEventListener('DOMContentLoaded', () => {
                modalCancelarVenta = new bootstrap.Modal(
                    document.getElementById('modalCancelarVenta')
                );
            });

            function abrirModalCancelacion(idVenta, folio) {

                document.getElementById('cancelar_id_venta').value = idVenta;
                document.getElementById('cancelar_motivo').value = '';

                document.querySelector('#modalCancelarVenta .modal-title').innerHTML =
                    `Cancelar Venta ${folio}`;

                modalCancelarVenta.show();
            }
            async function procesarCancelacion(conSaldo) {

                const idVenta = document.getElementById('cancelar_id_venta').value;
                const motivo = document.getElementById('cancelar_motivo').value.trim();

                if (!motivo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Motivo requerido',
                        text: 'Debe capturar el motivo de la cancelación'
                    });
                    return;
                }

                modalCancelarVenta.hide();

                const accion = conSaldo ?
                    'cancelarVenta' :
                    'cancelarVentaSinSaldo';

                Swal.fire({
                    title: 'Procesando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {

                    const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_venta: idVenta,
                            motivo: motivo
                        })
                    });

                    const res = await response.json();

                    if (res.status === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Venta cancelada',
                            text: res.message
                        });

                        getVentas();

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });

                    }

                } catch (error) {

                    console.error(error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor'
                    });
                }
            }
            // PASO 1: Esta función se dispara al dar click al botón de la tabla (abre el modal)
            function modalFactura(id, factura) {
                const modalElement = document.getElementById('modalAgregarFactura');
                const folioInput = document.getElementById("folio-factura");

                // Limpiamos el input y errores previos por si acaso
                folioInput.value = factura;
                folioInput.classList.remove("is-invalid");

                // Guardamos el ID de la venta/viaje en el modal para no perderlo
                modalElement.setAttribute('data-id-actual', id);
                document.getElementById('id_venta_factura').value = id;

                // Abrimos el modal programáticamente con Bootstrap
                const modalInstance = new bootstrap.Modal(modalElement);
                modalInstance.show();
            }

            // PASO 2: Esta función se dispara al dar click en "Guardar" dentro del modal


            // Tu// Función final encargada del backend
            async function agregarFactura(id, folio) {
                console.log(`Guardando en BD -> ID: ${id}, Folio Factura: ${folio}`);

                // 1. Creamos el objeto FormData y le inyectamos los datos que necesita el controlador PHP
                const data = new FormData();
                data.append('venta_id', id);
                data.append('factura', folio);

                try {
                    // Asumiendo que URL_CONTROLLER es tu constante global (ej: '../controllers/ventasController.php')
                    const res = await fetch(
                        `/myvet/app/controllers/ventasHistorialController.php?action=guardarFactura`, {
                        method: 'POST',
                        body: data // Enviamos el FormData con los valores
                    });

                    // Verificamos si la respuesta del servidor es un JSON válido
                    const result = await res.json();

                    if (result.status === 'success') {

                        // Ojo: Si usaste la instancia limpia que te pasé en el paso anterior, 
                        // puedes cerrar el modal de Bootstrap 5 así si no tienes 'modalObj' global:
                        const modalElement = document.getElementById('modalAgregarFactura');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) modalInstance.hide();

                        // Recargamos la tabla principal de ventas
                        if (typeof getVentas === 'function') getVentas();

                        // Alerta de éxito con SweetAlert2
                        Swal.fire({
                            title: '¡Listo!',
                            text: 'Factura guardada correctamente',
                            icon: 'success',
                            timer: 1000, // Subí a 1000ms (1 segundo) para que el usuario alcance a notar la palomita de éxito
                            showConfirmButton: false
                        });

                        // 🔥 Volver a abrir automáticamente el detalle si es necesario
                        setTimeout(() => {
                            // Usamos el 'id' que entró originalmente por parámetro a esta función
                            if (typeof verDetalle === 'function') {
                                verDetalle(id);
                            }
                        }, 1005);

                    } else {
                        // Aquí manejamos errores devueltos por el backend (Excepciones del try/catch de tu PHP)
                        Swal.fire('No se pudo guardar', result.message || 'Error desconocido', 'error');
                    }

                } catch (e) {
                    console.error("Error al procesar la factura:", e);
                    Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
                }
            }

            // Esta es la función que necesitas que se ejecute:


            cargarUsuariosSelect();
            async function cargarUsuariosSelect() {
                const select = document.getElementById('select-usuarios');
                if (!select) return; // Seguridad por si el select no está en la vista actual

                try {
                    // 1. Realizar la petición a tu controlador de Cf System
                    const url = '/myvet/app/controllers/ventasHistorialController.php?action=obtenerUsuarios';
                    const respuesta = await fetch(url);

                    if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                    const resultado = await respuesta.json();

                    // 2. Verificar que la respuesta sea exitosa y contenga los datos
                    if (resultado.success && Array.isArray(resultado.data)) {

                        // Limpiamos el select y dejamos una opción inicial neutra
                        // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

                        // 3. Recorrer los usuarios y crear las opciones
                        resultado.data.forEach(usuario => {
                            const opcion = document.createElement('option');
                            opcion.value = usuario.id; // El ID que se enviará en el formulario

                            // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                            const almacen = usuario.almacen_nombre || 'Sin Almacén';
                            opcion.textContent = `${usuario.nombre}`;

                            // Agregamos la opción al select
                            select.appendChild(opcion);
                        });

                    } else {
                        select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                        console.error('El backend no devolvió success:true o la estructura cambió');
                    }

                } catch (error) {
                    select.innerHTML = '<option value="">Error al cargar la lista</option>';
                    console.error('Error al ejecutar cargarUsuariosSelect:', error);
                }
            }

            const modalObj = new bootstrap.Modal('#modalDetalle');
            let ventaActual = null;
            // La ruta al controlador (ajusta si el nombre del archivo varía)
            const URL_CONTROLLER = '/myvet/app/controllers/ventasHistorialController.php';

            async function getVentas() {
                $('#loader').removeClass('d-none');


                const params = new URLSearchParams({
                    action: 'listar',
                    // <--- Nuevo parámetro para el ID de venta
                    f_search: $('#f_search').val(),
                    f_rango: $('#f_rango').val(),
                    f_inicio: $('#f_ini').val(),
                    f_fin: $('#f_fin').val(),
                    f_almacen: $('#f_almacen').val(),
                    f_status: $('#f_status').val(),
                    f_pago: $('#f_pago').val(),
                    f_vendedor: $('#select-usuarios').val() ?? '',
                    f_factura: $('#estado_factura').val() ?? ''


                });

                try {
                    const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
                    const data = await res.json();
                    //<td class="ps-3 small">${v.id}</td>
                    let totalVendido = 0;
                    let deuda = 0;

                    $('#tablaVentas tbody').html(data.map(v => {
                        let total = 0;
                        let pagado = 0;
                        if (v.estado_general != 'cancelada') {
                            total = parseFloat(v.total) || 0;
                            pagado = parseFloat(v.pagado) || 0;
                        }
                        let saldo = total - pagado;

                        if (v.estado_general == 'activa') {
                            totalVendido += total;
                            deuda += (total - pagado);
                        }

                        let badgeCobro = (saldo <= 0) ?
                            '<span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Pagado</span>' :
                            `<span class="text-danger small fw-bold">Debe: $${saldo.toFixed(2)}</span>`;

                        let entrega = (v.estado_general == 'activa') ?
                            `<span class="badge ${v.estado_entrega == 'entregado' ? 'bg-success' : (v.estado_entrega == 'parcial' ? 'bg-warning card-title-text' : 'bg-danger')}">
            ${v.estado_entrega.toUpperCase()}
        </span>` :
                            '<span class="text-danger small fw-bold"><i class="bi bi-check-circle"></i> Cancelado</span>';

                        let factura = (v.estado_general == 'activa') ?
                            `${v.factura}
        <button type="button" class="btn btn-link text-primary p-1 " onclick="modalFactura(${v.id},${v.factura})" title="Agregar Factura">
            <i class="bi bi-pencil-square me-2"></i>
        </button>` : '';
                        let rolAct = <?= $rol ?>;
                        let botonCancelar = rolAct <= 2 ? `<button type="button" 
        class="btn btn-glass-danger rounded-3  d-inline-flex align-items-center justify-content-center" 
        onclick="abrirModalCancelacion('${v.id}','${v.folio}')" 
        data-bs-toggle="tooltip" 
        data-bs-placement="top" 
        title="Cancelar Venta">
    <i class="bi bi-x-circle-fill fs-6"></i>
</button>

<style>
.btn-glass-danger {
    width: 36px;
    height: 36px;
    background-color: rgba(255, 255, 255, 0.1);
    color: #090356;
    transition: all 0.2s ease-in-out;
}

.btn-glass-danger:hover {
    background-color: rgba(220, 53, 69, 0.25);
    color: #2a54b0;
    transform: scale(1.08);
}
</style>` : `<button type="button" 
        class="btn btn-glass-danger rounded-3  d-inline-flex align-items-center justify-content-center" 
        onclick="abrirModalSolicitudCancelacion('${v.id}')" 
        data-bs-toggle="tooltip" 
        data-bs-placement="top" 
        title="Cancelar Venta">
    <i class="bi bi-x-circle-fill fs-6"></i>
</button>

<style>
.btn-glass-danger {
    width: 36px;
    height: 36px;
    background-color: rgba(255, 255, 255, 0.1);
    color: #dc3545;
    transition: all 0.2s ease-in-out;
}

.btn-glass-danger:hover {
    background-color: rgba(220, 53, 69, 0.25);
    color: #b02a37;
    transform: scale(1.08);
}
</style>`;
                        let cancelada = (v.estado_general == 'activa') ? `
        

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-link text-secondary btn-sm px-3  dropdown-toggle remove-caret" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                     title="Más opciones">
                <i class="bi bi-three-dots fs-5"></i>
            </button>
           
                <ul class="dropdown-menu dropdown-menu-end shadow-lg  rounded-4 p-2 mt-2 animated--fade-in" style="min-width: 220px;">
    <li>
        <button class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center fw-semibold card-title-text hover-bg-light" 
                onclick="gestionarSolicitud(${v.id})">
            <i class="bi bi-pencil-square me-2 text-primary fs-6"></i> Editar
        </button>
    </li>
    
    <li><hr class="dropdown-divider my-2 opacity-25"></li>
    
    <li>
        <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center text-secondary hover-primary" 
           href="/myvet/app/backend/ventas/ticket_venta.php?id=${v.id}" target="_blank">
            <i class="bi bi-receipt me-2 text-primary"></i> Imprimir Ticket
        </a>
    </li>
    
    <li>
        <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center text-secondary hover-info" 
           href="/myvet/app/backend/ventas/ticketFormal.php?id=${v.id}" target="_blank">
            <i class="bi bi-file-earmark-check me-2 card-title-text"></i> Ticket Formal
        </a>
    </li>

            </ul>
        </div>${botonCancelar}` : ``;

                        return `<tr>
        <td class="ps-3 small">${v.fecha}</td>
        <td class="fw-bold">${v.folio}</td>
        <td><span class="badge bg-light text-dark border fw-normal">${v.almacen_nombre}</span></td>
        <td><div class="small fw-bold">${v.vendedor}</div></td>
        <td><div class="small fw-bold">${v.cliente}</div></td>
        <td class="fw-bold card-title-text">$${total.toFixed(2)}</td>
        <td>${v.estado_general == 'activa' ? badgeCobro : '<span class="text-danger small fw-bold"><i class="bi bi-check-circle"></i> Cancelado</span>'}</td>
        <td><div class="small fw-bold">${factura}</div></td>
        <td class="text-center">${entrega}</td>
        <td class="text-end pe-3">
            <div class="btn-group bg-white rounded-3 shadow-sm border p-1" role="group" aria-label="Acciones de venta">
                <button type="button" 
        class="btn btn-glass-eye rounded-3  d-inline-flex align-items-center justify-content-center" 
        onclick="verDetalle(${v.id})" 
        data-bs-toggle="tooltip" 
        data-bs-placement="top" 
        title="Gestionar Venta">
    <i class="bi bi-eye fs-5"></i>
</button>

<style>
.btn-glass-eye {
    width: 36px;
    height: 36px;
    background-color: rgba(13, 110, 253, 0.08);
    color: #0d6efd;
    transition: all 0.2s ease-in-out;
}

.btn-glass-eye:hover {
    background-color: rgba(13, 110, 253, 0.2);
    color: #0a58ca;
    transform: scale(1.08);
}
</style>
                ${cancelada}
            </div>
        </td>
    </tr>`;
                    }).join(''));

                    // Fila de totales corregida (Sin 'v.almacen_nombre' para evitar errores)
                    let totales = `<tr class="table-light fw-bold border-top border-dark">
    <td class="ps-3 small"></td>
    <td class="fw-bold">TOTALES</td>
    <td></td>
    <td></td>
    <td></td>
    <td class="card-title-text">Total: $${totalVendido.toFixed(2)}</td>
    <td class="text-success">Cobrado: $${(totalVendido - deuda).toFixed(2)}</td>
    <td class="text-danger">Por Cobrar: $${deuda.toFixed(2)}</td>
    <td></td>
    <td></td>
</tr>`;



                    // CORRECCIÓN AQUÍ: Agregamos la fila al final del tbody usando .append() sin .join()
                    $('#tablaVentas tbody').append(totales);
                } catch (e) {
                    console.error("Error al cargar ventas:", e);
                } finally {
                    $('#loader').addClass('d-none');
                }
            }
            async function verDetalle(id) {
                try {
                    // 🔥 OBTENER IDS PENDIENTES
                    const respIds = await fetch(
                        `/myvet/app/controllers/entregasController.php?ajax=get_ids_pendientes_venta&venta_id=${id}`
                    );
                    const resNAlmacen = await fetch(
                        `/myvet/app/controllers/entregasController.php?ajax=obtener_id_almacen&id=${id}`
                    );

                    const dataAlmacen = await resNAlmacen.json();
                    const almacen_id_conseguido = dataAlmacen.almacen.almacen_id;
                    console.log(dataAlmacen.almacen.almacen_id);

                    const dataIds = await respIds.json();

                    console.log(dataIds.ids);

                    // =====================================================
                    // 🔥 HABILITAR / DESHABILITAR BOTÓN
                    // =====================================================

                    if (
                        Array.isArray(dataIds.ids) &&
                        dataIds.ids.length > 0

                    ) {


                    } else {

                        $('#btnGestionVenta')
                            .addClass('d-none')
                            .prop('disabled', true)
                            .removeAttr('onclick');

                    }
                    const res = await fetch(`${URL_CONTROLLER}?action=obtenerDetalle&id=${id}`);
                    cargarRepartos(id);
                    const data = await res.json();
                    console.log(data);


                    ventaActual = data;
                    $('#folioFactura').text(data.info.factura);
                    if (data.info.estado_general === 'cancelada') {
                        $('#btnGestionVenta')
                            .addClass('d-none')
                            .prop('disabled', true)
                            .removeAttr('onclick');
                        $('#btnAbonar')
                            .addClass('d-none')
                            .prop('disabled', true)
                            .removeAttr('onclick');
                        $('#btnHabilitar')
                            .addClass('d-none')
                            .prop('disabled', true)
                            .removeAttr('onclick');
                        $('#cancelado').text(`Cancelada por: ${data.info.observaciones}`);
                    } else {
                        $('#cancelado').text('');
                    }
                    $('#spanFolio').text(data.info.folio);
                    $('#IdFolio').text(data.info.id);
                    $('#Almacen_id').text(data.info.almacen_id);

                    $('#detCliente').text(data.info.nombre_comercial);
                    $('#detAlmacen').text(data.info.almacen);
                    $('#detVendedor').text(data.info.vendedor);

                    const total = parseFloat(data.info.total) || 0;
                    const pagado = parseFloat(data.info.total_pagado) || 0;
                    const deuda = total - pagado;
                    $('#detTotalLabel').text('$' + total.toFixed(2));

                    if (deuda <= 0) {
                        $('#detSaldoLabel').text('LIQUIDADO').removeClass('text-danger').addClass('text-success');
                        $('#btnAbonar').addClass('d-none');
                    } else {
                        $('#detSaldoLabel').text('$' + deuda.toFixed(2)).removeClass('text-success').addClass(
                            'text-danger');
                        $('#btnAbonar').removeClass('d-none');
                    }

                    // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
                    // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
                    $('#tbodyDetalle').html(data.productos.map(p => {
                        console.log(p);
                        let cant = parseFloat(p.cantidad) || 0;
                        let pendiente = (cant - (parseFloat(p.cantidad_entregada) || 0)).toFixed(3);

                        let factor = parseFloat(p.factor_conversion) || 1;
                        let cantPendiente = pendiente / factor;

                        let pen = Number(pendiente / (1 / p.equivalencia));
                        let pendi = Number(cantPendiente);
                        let disponible = (p.disponible / factor);
                        console.log(disponible);
                        let entregada = p.cantidad_entregada / factor;

                        console.log({
                            pen,
                            tipo: typeof pen,
                            comparacion: pen > 0
                        });
                        // 1. Definimos qué se verá en la columna "Venta"
                        let visualizacionVenta = "";
                        let infoEquivalenciaSub = "";
                        let unm = (parseFloat(p.cantidad_entregada) / (1 / parseFloat(p.equivalencia)));
                        console.log(unm);
                        unm = unm % 1 !== 0 ? unm.toFixed(0) : unm;
                        if (factor > 1 && cant >= factor) {
                            // Si alcanza el factor (Ej: 20 bultos >= 20 factor)
                            let unidadesMayores = (cant / factor);
                            // Formateamos para que si es entero no muestre .00 (Ej: 1 en vez de 1.00)
                            let totalUnidadesStr = Number.isInteger(unidadesMayores) ? unidadesMayores :
                                unidadesMayores.toFixed(2);


                            // Lo que se verá grande en la celda
                            visualizacionVenta =
                                `<span class="fw-bold">${totalUnidadesStr} ${p.unidad_reporte}</span> <br> <small class="text-body-secondary">(${cant} ${p.unidad_medida})</small>`;

                            // Leyenda pequeña debajo del nombre del producto (opcional, para referencia)
                            infoEquivalenciaSub =
                                `<div class="text-body-secondary small" style="font-size: 0.65rem;">1 ${p.unidad_reporte} = ${factor} ${p.unidad_medida}</div>`;
                        } else {
                            // Si no llega al factor (Ej: 10 bultos) mostramos la unidad normal
                            //agregar observaciones en ticket 
                            visualizacionVenta = `<span>${cant} ${p.unidad_medida}</span>`;
                        }


                        return `<tr>
        <td>
            <div class="fw-bold card-title-text">${p.producto}</div>
            ${infoEquivalenciaSub}
        </td>
        <td class="text-center">
        ${p.equivalencia >= 1 ? cant / (1 / p.equivalencia).toFixed(2) : (cant * (p.equivalencia)).toFixed(2)} ${p.nombre}
        
      
        (${cant} ${p.unidad_medida})
            
        </td>
        <td class="text-center">
        
      
        ${entregada > 1 ? entregada + ' ' + p.unidad_reporte :
                                (p.cantidad_entregada / (1 / p.equivalencia)) >= 1 ? (p.cantidad_entregada / (1 / p.equivalencia)).toFixed(3) + ' ' + p.nombre :
                                    p.cantidad_entregada + ' ' + p.unidad_medida}</td>
        
        <td class="text-center text-danger fw-bold">${(cantPendiente >= 1 ? cantPendiente.toFixed(3) : pen.toFixed(3))} ${cantPendiente >= 1 ? p.unidad_reporte : p.cantidad / (1 / p.equivalencia) > 1 ? p.nombre : p.unidad_medida}</td>
         <td class="text-center col-input d-none">
            ${pen.toFixed(4) > 0 ?
                                `<input type="number"
    class="form-control form-control-sm input-entrega2 mx-auto"
    max="${pen <= p.disponible ? (pendi >= 1 ? pendi : pen) : (disponible > 1 ? disponible : p.disponible)}"
    min="0"
    step="0.01"
    value="0.00"
    data-dvid="${p.dvid}"
    data-id="${p.producto_id}"
    data-factor="${(pendi >= 1 && disponible >= 1) ? factor : 1}"
    style="width:70px">
                   <input type="hidden" class="form-control form-control-sm input-entrega0 mx-auto" 
                    value="0"data-dvid=${p.dvid} data-id="${p.producto_id}" style="width:70px"step="0.01" min="0">
                     <span class="badge bg-success">${(pendi >= 1 && disponible >= 1) ? p.unidad_reporte : p.unidad_medida}</span>`

                                : '<span class="badge bg-success">Completo</span>'}
        </td>
    </tr>`;
                    }).join(''));
                    // ... (dentro de verDetalle, después de renderizar historial de entregas)
                    $('#tbodyHistorial').html(data.historial.length > 0 ? data.historial.map(h => {
                        // 1. Extraemos los valores del historial
                        // Si salen vacíos o undefined, es que el PHP no los está mandando en el JSON de historial
                        let cantH = parseFloat(h.cantidad) || 0;
                        let factorH = parseFloat(h.factor_conversion) || 1;
                        let uReporteH = h.unidad_reporte || '';
                        let uMedidaH = h.unidad_medida || '';

                        let visualizacionHistorial = "";
                        console.log((h.cantidad / (1 / h.equivalencia)) >= 1 ? (h.cantidad / (1 / h
                            .equivalencia)).toFixed(3) : cantH);
                        // 2. Aplicamos la misma lógica que usas arriba

                        // Aquí verás si unidad_medida viene vacío desde la base de datos
                        visualizacionHistorial =
                            `<span>${(h.cantidad / (1 / h.equivalencia)) >= 1 ? (h.cantidad / (1 / h.equivalencia)).toFixed(3) : cantH} ${(h.cantidad / (1 / h.equivalencia)) >= 1 ? (h.nombre) : uMedidaH}</span>`;

                        return `
    <tr>
        <td class="small">${h.fecha}</td>
        <td class="small">${h.usuario_nombre}</td>
        <td>
            <div class="fw-bold" style="font-size:0.85rem;">${h.producto}</div>
        </td>
        <td class="text-center">
            ${visualizacionHistorial}
        </td>
    </tr>`;
                    }).join('') :
                        '<tr><td colspan="4" class="text-center text-body-secondary p-3">No hay entregas registradas</td></tr>'
                    );


                    // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
                    if (data.pagos && data.pagos.length > 0) {
                        $('#tbodyPagos').html(data.pagos.map(p => `
        <tr>
            <td class="small">${p.fecha}</td>
            <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
            <td>
                <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago} </span>
               
                <div class="text-body-secondary" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
            </td>
            <td>
            <span>
    ${(p.referencia ?? '')

                            }
</span> 
            </td>
        </tr>
    `).join(''));
                    } else {
                        $('#tbodyPagos').html(
                            '<tr><td colspan="3" class="text-center text-body-secondary p-3">No hay abonos registrados</td></tr>'
                        );
                    }
                    alternarModo(false);
                    modalObj.show();
                } catch (error) {
                    console.error("Error al obtener detalle:", error);
                }
            }
            document.addEventListener('input', e => {

                if (e.target.classList.contains('input-entrega1')) {

                    const max = parseFloat(e.target.max) || 0;
                    const min = parseFloat(e.target.min) || 0;
                    const factor = parseFloat(e.target.dataset.factor) || 1;

                    let value = e.target.value;

                    // 👉 PERMITIR BORRADO COMPLETO
                    if (value === "") {
                        const contenedor = e.target.parentElement;
                        const inputEntrega = contenedor.querySelector('.input-entrega');

                        if (inputEntrega) {
                            inputEntrega.value = "";
                        }
                        return; // 🔥 importante: no seguir procesando
                    }

                    value = parseFloat(value);

                    if (isNaN(value)) return;

                    if (value > max) value = max;
                    if (value < min) value = min;

                    e.target.value = value;

                    const contenedor = e.target.parentElement;
                    const inputEntrega = contenedor.querySelector('.input-entrega');

                    if (inputEntrega) {
                        inputEntrega.value = (value * factor).toFixed(2);
                    }
                }
            });
            async function cargarRepartos(idVenta) {

                const resp = await fetch(
                    `/myvet/app/controllers/repartosController.php?action=get_repartos_entrega&id=${idVenta}`
                );


                const repartoViaje = await resp.json();
                let repartos = repartoViaje.data;
                console.log(repartoViaje);

                const tbody = document.getElementById('tbodyRepartos');
                tbody.innerHTML = '';

                if (!repartoViaje.success) return;

                // ================================
                // AGRUPAR POR FOLIO VIAJE
                // ================================

                // ================================
                // RENDER TABLA
                // ================================
                repartos.forEach(g => {

                    const estadoClass =
                        g.estatus_logistico === 'completado' ?
                            'bg-success' :
                            'bg-warning card-title-text';

                    const tr = `
            <tr>

                <td class="fw-bold">
                    ${g.entrega_id}
                </td>
                <td class="fw-bold">
                    ${g.viaje_folio}
                </td>

                <td>
                    ${g.fecha}
                </td>

                <td>
                    <span >
                        ${g.direccion_entrega}
                    </span>
                </td>

 <td class="text-center align-middle py-2">
    <div class="d-inline-flex gap-1 bg-light p-1 rounded-pill border border-translucent shadow-xs">
    <button type="button" 
                class="btn btn-xs btn-success rounded-pill px-2.5 py-1 d-inline-flex align-items-center border-0 fw-semibold" 
                style="font-size: 0.75rem; transition: all 0.2s;"
                onclick="imprimirRuta('${g.entrega_id}','${g.folio}')">
            <i class="bi bi-truck me-1" style="font-size: 0.85rem;"></i>
            <span>Ver Reparto</span>
        </button>

        <button type="button" 
                class="btn btn-xs btn-success rounded-pill px-2.5 py-1 d-inline-flex align-items-center border-0 fw-semibold" 
                style="font-size: 0.75rem; transition: all 0.2s;"
                onclick="abrirModalEvidencias(${g.entrega_id})">
            <i class="bi bi-images me-1" style="font-size: 0.85rem;"></i>
            <span>Evidencias</span>
        </button>
    </div>
</td>
            </tr>
        `;

                    tbody.insertAdjacentHTML('beforeend', tr);
                });
            }
            async function procesarEntrega() {
                const fd = new FormData();
                let ok = false;

                $('.input-entrega').each(function () {

                    const cant = parseFloat($(this).val());

                    console.log($(this).data('dvid'), cant);

                    if (cant > 0) {

                        fd.append(
                            `productos[${$(this).data('dvid')}]`,
                            cant
                        );

                        ok = true;
                    }
                });

                if (!ok) return Swal.fire('Atención', 'Indique al menos una cantidad válida para entregar', 'warning');

                fd.append('venta_id', ventaActual.info.id);

                try {
                    const res = await fetch(`${URL_CONTROLLER}?action=guardarEntrega`, {
                        method: 'POST',
                        body: fd
                    });

                    // Verificamos si la respuesta del servidor es un JSON válido
                    const result = await res.json();

                    if (result.status === 'success') {

                        modalObj.hide();

                        getVentas();

                        Swal.fire({
                            title: '¡Listo!',
                            text: 'Entrega guardada correctamente',
                            icon: 'success',
                            timer: 500,
                            showConfirmButton: false
                        });

                        // 🔥 volver a abrir automáticamente
                        setTimeout(() => {

                            verDetalle(ventaActual.info.id);

                        }, 501);

                    } else {
                        // AQUÍ MANEJAMOS EL ERROR DE STOCK (o cualquier otro error del Model)
                        // Usamos result.message que es el que trae "Stock insuficiente en almacén..."
                        Swal.fire('No se pudo entregar', result.message || 'Error desconocido', 'error');
                    }

                } catch (e) {
                    console.error("Error al procesar entrega:", e);
                    Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
                }
            } // Instanciamos el nuevo modal
            const modalAbonoObj = new bootstrap.Modal('#modalAbono');



            function togglePerso() {
                $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
                getVentas();
            }

            function alternarModo(e) {
                $('.col-input').toggleClass('d-none', !e);
                $('#btnHabilitar').toggle(!e && ventaActual.info.estado_entrega !== 'entregado');
                $('#controlesGuardar').toggleClass('d-none', !e);
            }

            $(document).ready(function () {
                // 1. Carga inicial de datos
                getVentas();

                // 2. Escuchadores para filtros (opcional, pero recomendado para centralizar)
                $('#f_rango').on('change', togglePerso);
                // getVentas ya se llama mediante onchange/onkeyup en tu HTML, lo cual está bien.

                console.log("Sistema de historial listo.");
            });
        </script>
        <script>
            async function confirmarCancelacion(idVenta, folio, total, pagado) {

                // 1. Lanzamos el SweetAlert con las 3 opciones
                const result = await Swal.fire({
                    title: `¿Cancelar Venta ${folio}?`,
                    text: "Selecciona si deseas reintegrar el dinero al saldo del cliente o solo anular la venta.",
                    icon: 'warning',
                    input: 'text',
                    inputLabel: 'Motivo de la cancelación',
                    inputPlaceholder: 'Escriba por qué se cancela...',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonColor: '#28a745', // Verde -> Con Saldo
                    denyButtonColor: '#d33', // Rojo -> Sin Saldo
                    cancelButtonColor: '#6c757d', // Gris -> Regresar
                    confirmButtonText: '<i class="bi bi-cash-stack"></i> Con Saldo a Favor',
                    denyButtonText: '<i class="bi bi-x-circle"></i> Sin Saldo',
                    cancelButtonText: 'Regresar',
                    inputValidator: (value) => {
                        if (!value) return '¡El motivo es obligatorio!';
                    }
                });

                // 2. Si se presionó cualquiera de los dos botones de ejecución (Confirmar o Denegar)
                if (result.isConfirmed || result.isDenied) {
                    // IMPORTANTE: Capturamos el motivo desde result.value
                    const motivo = 'cancelacion';

                    // Elegimos la ruta del controlador según el botón
                    const accion = result.isConfirmed ? 'cancelarVenta' : 'cancelarVentaSinSaldo';

                    Swal.fire({
                        title: 'Procesando...',
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        allowOutsideClick: false
                    });

                    try {
                        const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id_venta: idVenta,
                                motivo: motivo
                            })
                        });

                        const res = await response.json();

                        if (res.status === 'success') {
                            await Swal.fire({
                                title: '¡Venta Cancelada!',
                                text: res.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Refrescamos la tabla de ventas
                            if (typeof getVentas === 'function') getVentas();

                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    } catch (error) {
                        console.error("Error en la petición:", error);
                        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                    }
                }
            }
        </script>
        <script>
            // Selecciona todos los inputs de texto y también los textareas
            document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
                elemento.addEventListener('input', function () {
                    // Convierte el valor a mayúsculas en tiempo real
                    this.value = this.value.toUpperCase();
                });
            });
        </script>
        <script>
            async function exportarVentasCSV() {
                $('#loader').removeClass('d-none');

                // Mismo objeto de parámetros que getVentas() para mantener los filtros aplicados
                const params = new URLSearchParams({
                    action: 'listar',
                    f_search: $('#f_search').val(),
                    f_rango: $('#f_rango').val(),
                    f_inicio: $('#f_ini').val(),
                    f_fin: $('#f_fin').val(),
                    f_almacen: $('#f_almacen').val(),
                    f_status: $('#f_status').val(),
                    f_pago: $('#f_pago').val(),
                    f_vendedor: $('#select-usuarios').val() ?? '',
                    f_factura: $('#estado_factura').val() ?? ''
                });

                try {
                    const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
                    const data = await res.json();

                    if (!data || data.length === 0) {
                        alert("No hay registros de ventas para exportar con los filtros seleccionados.");
                        return;
                    }

                    // Encabezados del CSV (1 fila por venta)
                    const headers = [
                        "ID Venta",
                        "Fecha",
                        "Folio",
                        "Almacén",
                        "Vendedor",
                        "Cliente",
                        "Monto Total ($)",
                        "Monto Pagado ($)",
                        "Saldo Pendiente ($)",
                        "Estado Cobro",
                        "Estado Entrega",
                        "Estado General",
                        "Factura"
                    ];

                    // Construir filas sanitizando comas y comillas para compatibilidad CSV
                    const rows = data.map(v => {
                        let total = 0;
                        let pagado = 0;

                        if (v.estado_general !== 'cancelada') {
                            total = parseFloat(v.total) || 0;
                            pagado = parseFloat(v.pagado) || 0;
                        }

                        let saldo = total - pagado;
                        let estadoCobro = v.estado_general === 'cancelada'
                            ? 'Cancelado'
                            : (saldo <= 0 ? 'Pagado' : `Debe ($${saldo.toFixed(2)})`);

                        // Función auxiliar para sanitizar cadenas en formato CSV
                        const escapeCSV = (str) => {
                            if (str === null || str === undefined) return '""';
                            let val = String(str).replace(/"/g, '""'); // Escapar comillas dobles
                            return `"${val}"`;
                        };

                        return [
                            escapeCSV(v.id),
                            escapeCSV(v.fecha),
                            escapeCSV(v.folio),
                            escapeCSV(v.almacen_nombre),
                            escapeCSV(v.vendedor),
                            escapeCSV(v.cliente),
                            total.toFixed(2),
                            pagado.toFixed(2),
                            saldo.toFixed(2),
                            escapeCSV(estadoCobro),
                            escapeCSV(v.estado_entrega ? v.estado_entrega.toUpperCase() : 'N/A'),
                            escapeCSV(v.estado_general ? v.estado_general.toUpperCase() : 'N/A'),
                            escapeCSV(v.factura || 'Sin Factura')
                        ].join(',');
                    });

                    // Ensamblar contenido del archivo CSV con BOM de UTF-8
                    const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');

                    // Generar Blob y descargar
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');

                    // Formatear nombre con la fecha actual de descarga
                    const hoy = new Date().toISOString().split('T')[0];
                    link.setAttribute('href', url);
                    link.setAttribute('download', `Reporte_Ventas_MYVET_${hoy}.csv`);
                    document.body.appendChild(link);

                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);

                } catch (e) {
                    console.error("Error al exportar ventas a CSV:", e);
                    alert("Ocurrió un error al generar la exportación.");
                } finally {
                    $('#loader').addClass('d-none');
                }
            }
        </script>
</body>

</html>