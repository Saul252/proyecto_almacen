<?php
/**
 * pedidosVendedor_view.php
 * Vista completa con gestión de pedidos, filtrado por DB y modales.
 * Optimizada para móvil y escritorio.
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos de Vendedor | CF System</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
<style>
        :root {
            --sidebar-width: 0px;
            --navbar-height: 65px;
        }

        body {
           
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        /* ── Layout ── */
        .main-content {
            
            padding: 24px 28px;
            padding-top: calc(var(--navbar-height) + 20px);
            min-height: 100vh;
        }

        /* ── Cards ── */
        .glass-card {
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        /* ── Tabla en escritorio ── */
        .tabla-scroll { max-height: 45vh; overflow-y: auto; }

        /* ── Badges de estado ── */
        .badge-pendiente { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-cubierto  { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }

        /* ── Select2 ── */
        .select2-container--bootstrap-5 .select2-selection { border-radius: 10px; }

        /* ── Filtros ── */
        #div_personalizado { display: none; }

        /* ─────────────────────────────────────────────────────────
           CARDS DE PEDIDO PARA MÓVIL
           ───────────────────────────────────────────────────────── */
        .pedido-card {
            background: white;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #0d6efd;
        }

        .pedido-card.cubierto { border-left-color: #198754; }

        .pedido-card .folio {
            font-size: 0.7rem;
            font-weight: 700;
            color: #0d6efd;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .pedido-card .cliente {
            font-size: 1rem;
            font-weight: 700;
            color: #1d1d1f;
            line-height: 1.2;
            margin: 2px 0;
        }

        .pedido-card .meta {
            font-size: 0.72rem;
            color: #86868b;
        }

        .pedido-card .acciones {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .pedido-card .acciones .btn {
            flex: 1;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
            padding: 7px 0;
        }

        /* Botón flotante móvil */
        .fab-nuevo {
            display: none;
            position: fixed;
            bottom: 24px;
            right: 20px;
            z-index: 1050;
            width: 56px; height: 56px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            
            font-size: 1.5rem;
            box-shadow: 0 6px 20px rgba(13,110,253,0.4);
            align-items: center;
            justify-content: center;
        }

        /* Filtros en móvil: scroll horizontal */
        .filtros-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .filtros-scroll::-webkit-scrollbar { display: none; }

        .chip-filtro {
            flex-shrink: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #495057;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s;
        }

        .chip-filtro.active {
            background: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
                padding-top: calc(var(--navbar-height) + 12px);
            }

            /* Ocultar tabla, mostrar cards */
            .tabla-desktop { display: none !important; }
            .cards-mobile  { display: block !important; }

            /* Mostrar FAB, ocultar botón de cabecera */
            .btn-nuevo-desktop { display: none !important; }
            .fab-nuevo { display: flex !important; }

            /* Filtros: ocultar form completo, mostrar chips + select simple */
            .filtros-desktop { display: none !important; }
            .filtros-mobile  { display: block !important; }

            /* Header más compacto */
            .page-header h2 { font-size: 1.4rem; }
        }

        @media (min-width: 992px) {
            .cards-mobile   { display: none !important; }
            .filtros-mobile { display: none !important; }
        }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">

        <!-- ── Page Header ── -->
        <div class="row mb-3 align-items-center page-header">
            <div class="col">
                <h2 class="fw-bold text-dark mb-0">
                    <i class="bi bi-cart-plus-fill me-2 text-primary"></i>Pedidos
                </h2>
                <p class="text-body-secondary small mb-0">
                    <span class="badge bg-info text-dark"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                    <span class="badge bg-secondary ms-1"><?= htmlspecialchars($_SESSION['almacen_nombre'] ?? 'General') ?></span>
                </p>
            </div>
            <div class="col-auto btn-nuevo-desktop">
                <button class="btn btn-primary rounded-pill px-4 shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalNuevoPedido">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Pedido
                </button>
            </div>
        </div>

        <!-- ── Filtros DESKTOP ── -->
        <div class="card glass-card p-3 mb-4  filtros-desktop">
            <form id="formFiltros" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-body-secondary">Período</label>
                    <select id="filtro_rango" class="form-select form-select-sm rounded-3" onchange="toggleFechas()">
                        <option value="hoy" selected>Hoy</option>
                        <option value="ayer">Ayer</option>
                        <option value="semana">Esta Semana</option>
                        <option value="mes">Este Mes</option>
                        <option value="personalizado">Personalizado...</option>
                    </select>
                </div>

                <div class="col-md-3" id="div_personalizado">
                    <div class="row g-1">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-body-secondary">Desde</label>
                            <input type="date" id="filtro_desde" class="form-control form-control-sm rounded-3">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-body-secondary">Hasta</label>
                            <input type="date" id="filtro_hasta" class="form-control form-control-sm rounded-3">
                        </div>
                    </div>
                </div>

                <?php if ($_SESSION['almacen_id'] == 0): ?>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-body-secondary">Sucursal</label>
                    <select id="filtro_almacen" class="form-select form-select-sm select2-modal">
                        <option value="0">Todas</option>
                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                    <input type="hidden" id="filtro_almacen" value="<?= $_SESSION['almacen_id'] ?>">
                <?php endif; ?>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-body-secondary">Estatus</label>
                    <select id="filtro_estatus" class="form-select form-select-sm rounded-3">
                        <option value="todos">Todos</option>
                        <option value="1" selected>Pendientes</option>
                        <option value="0">Cubiertos</option>
                    </select>
                </div>

                <div class="col-md-3 text-md-end">
                    <button type="button" onclick="loadPedidos()" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                    <button type="button" onclick="limpiarFiltros()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- ── Filtros MÓVIL ── -->
        <div class="filtros-mobile mb-3">
            <!-- Chips de período -->
            <div class="filtros-scroll mb-2">
                <span class="chip-filtro active" data-rango="hoy" onclick="seleccionarChip(this, 'hoy')">Hoy</span>
                <span class="chip-filtro" data-rango="ayer" onclick="seleccionarChip(this, 'ayer')">Ayer</span>
                <span class="chip-filtro" data-rango="semana" onclick="seleccionarChip(this, 'semana')">Semana</span>
                <span class="chip-filtro" data-rango="mes" onclick="seleccionarChip(this, 'mes')">Mes</span>
                <span class="chip-filtro" data-estado="todos"  onclick="seleccionarEstadoChip(this, 'todos')">Todos</span>
                <span class="chip-filtro active" data-estado="1" onclick="seleccionarEstadoChip(this, '1')">Pendientes</span>
                <span class="chip-filtro" data-estado="0" onclick="seleccionarEstadoChip(this, '0')">Cubiertos</span>
            </div>
        </div>

        <!-- ── Tabla DESKTOP ── -->
        <div class="card glass-card p-3  tabla-desktop">
            <div class="table-responsive tabla-scroll">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-body-secondary small text-uppercase">
                        <tr>
                            <th width="120">Folio</th>
                            <th>Cliente / Almacén</th>
                            <th>Vendedor</th>
                            <th>Fecha</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaPedidos"></tbody>
                </table>
            </div>
        </div>

        <!-- ── Cards MÓVIL ── -->
        <div class="cards-mobile" id="listaPedidosMobile"></div>

    </div>

    <!-- ── FAB Móvil ── -->
    <button class="fab-nuevo" data-bs-toggle="modal" data-bs-target="#modalNuevoPedido">
        <i class="bi bi-plus-lg"></i>
    </button>

    <!-- ══════════════════════════════════════════════════════════
         MODAL NUEVO PEDIDO
    ══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalNuevoPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 25px; overflow: hidden;">
            
            <div class="modal-header  shadow-sm" style="background: linear-gradient(135deg, #212529 0%, #343a40 100%); padding: 1.5rem 2rem;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-bag-plus-fill fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Levantar Nuevo Pedido</h5>
                        <small class="text-white-50">Complete los datos para la orden de despacho</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <form id="formPedido">
                    <div class="p-4 border-bottom bg-light bg-opacity-50">
                        <div class="row g-3">
                            <?php if ($_SESSION['almacen_id'] == 0): ?>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-body-secondary small text-uppercase">Sucursal Destino</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt text-danger"></i></span>
                                        <select name="almacen_id" id="almacen_id" class="form-select border-start-0 ps-0" required style="border-radius: 0 10px 10px 0;">
                                            <option value="">Seleccione Almacén...</option>
                                            <?php foreach($almacenes as $alm): ?>
                                                <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="almacen_id" id="almacen_id" value="<?= $_SESSION['almacen_id'] ?>">
                            <?php endif; ?>

                            <div class="<?= ($_SESSION['almacen_id'] == 0) ? 'col-md-5' : 'col-md-9' ?>">
                                <label class="form-label fw-bold text-body-secondary small text-uppercase">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-select select2-modal" required>
                                    <option value="">Seleccione un cliente...</option>
                                    <?php foreach($clientes as $c): ?>
                                        <option value="<?= $c['id'] ?>">
                                            <?= htmlspecialchars($c['nombre_comercial'] ?? $c['nombre'] ?? 'S/N') ?> (<?= $c['rfc'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-body-secondary small text-uppercase">Prioridad</label>
                                <select name="prioridad" class="form-select shadow-sm  bg-white" style="border-radius: 10px;">
                                    <option value="Baja">🟢 Baja</option>
                                    <option value="Media" selected>🟡 Media</option>
                                    <option value="Alta">🔴 Alta</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="d-flex align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark">Materiales del Pedido</h6>
                            <div class="ms-auto col-md-5">
                                <select id="buscador_productos" class="form-select select2-modal">
                                    <option value="">🔍 SKU o Nombre del material...</option>
                                    <?php foreach($productos as $p): 
                                        $tieneFactor = (!empty($p['unidad_reporte']) && $p['factor_conversion'] > 1);
                                    ?>
                                        <option value="<?= $p['id'] ?? $p['producto_id'] ?>" 
                                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>" 
                                                data-sku="<?= $p['sku'] ?>"
                                                data-factor="<?= $p['factor_conversion'] ?>"
                                                data-um="<?= $p['unidad_medida'] ?? 'PZA' ?>"
                                                data-ur="<?= $p['unidad_reporte'] ?? '' ?>"
                                                data-precio="<?= $p['precio'] ?? '' ?>"
                                                data-tiene-factor="<?= $tieneFactor ? '1' : '0' ?>">
                                            [<?= $p['sku'] ?>] <?= htmlspecialchars($p['nombre']) ?> 
                                            <?= $tieneFactor ? "({$p['unidad_reporte']})" : "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-4 bg-white shadow-sm overflow-hidden mb-4">
                            <table class="table table-hover align-middle mb-0" id="tablaDetallePedido">
                                <thead class="bg-light">
                                    <tr class="text-body-secondary small text-uppercase">
                                        <th class="ps-4 py-3">Producto</th>
                                        <th width="180">Venta por</th>
                                        <th width="150">Cantidad</th>
                                        <th>Notas / Medidas</th>
                                        <th width="60" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating shadow-sm">
                                    <textarea name="observaciones" class="form-control  bg-light" placeholder="Indicaciones..." id="obsText" style="height: 80px; border-radius: 12px;"></textarea>
                                    <label for="obsText" class="text-body-secondary">Observaciones de Entrega</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer  bg-white p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 text-body-secondary fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                <button type="button" onclick="guardarPedido()" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg " style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> REGISTRAR PEDIDO
                </button>
            </div>
        </div>
    </div>
</div>
    <!-- ══════════════════════════════════════════════════════════
         MODAL VER PEDIDO
    ══════════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="modalVerPedido" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content  shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-primary text-white py-3" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Pedido: <span id="view_folio" class="fw-bold"></span></h5>
                    <div id="view_estatus_modal" class="ms-auto me-3"></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <div class="row g-2 mb-3 p-3 bg-light rounded-4 border">
                        <div class="col-12 col-md-6 border-md-end">
                            <label class="text-body-secondary small d-block text-uppercase fw-bold">Cliente</label>
                            <div id="view_cliente" class="fs-5 fw-bold text-dark"></div>
                            <div id="view_almacen" class="small text-body-secondary"></div>
                        </div>
                        <div class="col-12 col-md-6 text-md-end">
                            <label class="text-body-secondary small d-block text-uppercase fw-bold mt-2 mt-md-0">Registro</label>
                            <div id="view_vendedor" class="text-dark small fw-bold"></div>
                            <div id="view_fecha" class="text-secondary small"></div>
                            <div id="view_prioridad" class="mt-1"></div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-body-secondary small text-uppercase mb-2">Materiales</h6>
                    <div class="border rounded-3 mb-3 bg-white" style="max-height: 40vh; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="tablaVerDetalles">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th>Descripción</th>
                                    <th class="text-center" width="100">Cant.</th>
                                    <th class="d-none d-md-table-cell">Notas</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-primary bg-opacity-10 border-start border-4 border-primary rounded-end">
                        <label class="text-body-secondary small d-block fw-bold text-uppercase mb-1">Notas:</label>
                        <p id="view_observaciones" class="mb-0 text-dark small"></p>
                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php cargarScripts(); ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    /* ── Variables de estado de filtros móvil ── */
    let _rangoMobile  = 'hoy';
    let _estadoMobile = '1';

    $(document).ready(function() {
        initSelect2();
        loadPedidos();
    });

    function initSelect2() {
        $('.select2-modal').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : null
            });
        });
    }

    /* ── Chips de filtro en móvil ── */
    function seleccionarChip(el, rango) {
        document.querySelectorAll('[data-rango]').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        _rangoMobile = rango;
        loadPedidos();
    }

    function seleccionarEstadoChip(el, estado) {
        document.querySelectorAll('[data-estado]').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        _estadoMobile = estado;
        loadPedidos();
    }

    function toggleFechas() {
        if ($('#filtro_rango').val() === 'personalizado') {
            $('#div_personalizado').show();
        } else {
            $('#div_personalizado').hide();
            loadPedidos();
        }
    }

    function getFiltros() {
        const esMobile = window.innerWidth < 992;
        const hoy = new Date();
        const fmt = (f) => f.toISOString().split('T')[0];

        const rango   = esMobile ? _rangoMobile  : $('#filtro_rango').val();
        const estatus = esMobile ? _estadoMobile : $('#filtro_estatus').val();

        let desde = '', hasta = '';

        if (rango === 'personalizado') {
            desde = $('#filtro_desde').val();
            hasta = $('#filtro_hasta').val();
        } else {
            switch (rango) {
                case 'hoy':   desde = hasta = fmt(hoy); break;
                case 'ayer':
                    let ay = new Date(); ay.setDate(hoy.getDate() - 1);
                    desde = hasta = fmt(ay); break;
                case 'semana':
                    let lu = new Date(hoy);
                    const d = hoy.getDay(), di = hoy.getDate() - d + (d == 0 ? -6 : 1);
                    lu.setDate(di);
                    desde = fmt(lu); hasta = fmt(hoy); break;
                case 'mes':
                    desde = fmt(new Date(hoy.getFullYear(), hoy.getMonth(), 1));
                    hasta = fmt(new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0)); break;
            }
        }

        return {
            action: 'listar',
            desde, hasta,
            almacen_id: $('#filtro_almacen').val() || '0',
            estatus
        };
    }

    function loadPedidos() {
        const filters = getFiltros();
        if (!filters.desde || !filters.hasta) return;

        $('#listaPedidos').html('<tr><td colspan="6" class="text-center p-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');
        $('#listaPedidosMobile').html('<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>');

        $.get('../controllers/pedidosVendedorController.php', filters, function(res) {
            if (!res.success) return;

            let htmlDesktop = '', htmlMobile = '';

            if (res.data.length === 0) {
                htmlDesktop = '<tr><td colspan="6" class="text-center p-5 text-body-secondary">No se encontraron registros.</td></tr>';
                htmlMobile  = '<p class="text-center text-body-secondary py-4">No se encontraron registros.</p>';
            } else {
                res.data.forEach(p => {
                    const esCubierto = p.estatus != 1;
                    const bEstatus = esCubierto
                        ? `<span class="badge badge-cubierto">Cubierto</span>`
                        : `<span class="badge badge-pendiente">Pendiente</span>`;

                    // Desktop row
                    htmlDesktop += `
                    <tr>
                        <td class="fw-bold text-primary">${p.folio}</td>
                        <td>
                            <div class="fw-bold">${p.cliente}</div>
                            <div class="text-body-secondary" style="font-size:11px;"><i class="bi bi-house"></i> ${p.almacen_nombre || ''}</div>
                        </td>
                        <td class="small">${p.vendedor}</td>
                        <td class="small text-body-secondary">${p.fecha_solicitud}</td>
                        <td class="text-center">${bEstatus}</td>
                        <td class="text-end">
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                <button onclick="verPedido(${p.id})" class="btn btn-sm btn-white border-end"><i class="bi bi-eye-fill text-primary"></i></button>
                                <button onclick="marcarCubierto(${p.id})" class="btn btn-sm btn-white" ${esCubierto ? 'disabled' : ''}><i class="bi bi-check-lg text-success"></i></button>
                            </div>
                        </td>
                    </tr>`;

                    // Mobile card
                    htmlMobile += `
                    <div class="pedido-card ${esCubierto ? 'cubierto' : ''}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="folio">${p.folio}</div>
                                <div class="cliente">${p.cliente}</div>
                                <div class="meta"><i class="bi bi-house"></i> ${p.almacen_nombre || ''} · ${p.fecha_solicitud}</div>
                                <div class="meta"><i class="bi bi-person"></i> ${p.vendedor}</div>
                            </div>
                            <div>${bEstatus}</div>
                        </div>
                        <div class="acciones">
                            <button onclick="verPedido(${p.id})" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i> Ver
                            </button>
                            ${!esCubierto ? `
                            <button onclick="marcarCubierto(${p.id})" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-check-lg me-1"></i> Cubierto
                            </button>` : ''}
                        </div>
                    </div>`;
                });
            }

            $('#listaPedidos').html(htmlDesktop);
            $('#listaPedidosMobile').html(htmlMobile);
        });
    }

    function limpiarFiltros() {
        $('#filtro_rango').val('hoy');
        $('#filtro_almacen').val('0').trigger('change');
        $('#filtro_estatus').val('1');
        $('#div_personalizado').hide();
        _rangoMobile = 'hoy';
        _estadoMobile = '1';
        document.querySelectorAll('[data-rango]').forEach(c => c.classList.toggle('active', c.dataset.rango === 'hoy'));
        document.querySelectorAll('[data-estado]').forEach(c => c.classList.toggle('active', c.dataset.estado === '1'));
        loadPedidos();
    }

    /* ── Captura de productos ── */
    $('#buscador_productos').on('change', function() {
        const opt = $(this).find(':selected');
        if (!opt.val()) return;

        const id = opt.val(), nombre = opt.data('nombre'), factor = parseFloat(opt.data('factor')) || 1;
        const um = opt.data('um'), ur = opt.data('ur'), tieneFactor = opt.data('tiene-factor') == '1';

        let selectorMedida = `<span class="text-body-secondary small">${um}</span>`;
        if (tieneFactor) {
            selectorMedida = `
            <select class="form-select form-select-sm select-modo-venta" onchange="recalcularFila(this)">
                <option value="individual" data-factor="1">${um}</option>
                <option value="referencia" data-factor="${factor}">${ur}</option>
            </select>`;
        }

        const fila = `
        <tr data-id="${id}" data-factor="${factor}" data-um="${um}">
            <td class="ps-2">
                <div class="fw-bold small">${nombre}</div>
                <div class="d-md-none">${selectorMedida}</div>
            </td>
            <td class="d-none d-md-table-cell">${selectorMedida}</td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control cantidad" value="1" min="0.1" step="any" oninput="recalcularFila(this)">
                    <span class="input-group-text bg-white small text-total-piezas" style="font-size:10px">1 ${um}</span>
                </div>
            </td>
            <td class="d-none d-md-table-cell">
                <input type="text" class="form-control form-control-sm input-notas" placeholder="...">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0" onclick="$(this).closest('tr').remove()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </td>
        </tr>`;

        $('#tablaDetallePedido tbody').append(fila);
        $(this).val(null).trigger('change');
    });

    function recalcularFila(el) {
        const fila = $(el).closest('tr');
        const modo = fila.find('.select-modo-venta').val() || 'individual';
        const factor = parseFloat(fila.data('factor')) || 1;
        const cant = parseFloat(fila.find('.cantidad').val()) || 0;
        fila.find('.text-total-piezas').text(`${(modo === 'referencia' ? cant * factor : cant).toFixed(2)} ${fila.data('um')}`);
    }

    /* ── Guardar pedido ── */
    function guardarPedido() {
        const cliente = $('#cliente_id').val(), almacen = $('#almacen_id').val();
        if (!almacen || !cliente) return Swal.fire('Error', 'Complete cliente y sucursal.', 'warning');

        const items = [];
        $('#tablaDetallePedido tbody tr').each(function() {
            const fila = $(this);
            const modo = fila.find('.select-modo-venta').val() || 'individual';
            const factor = parseFloat(fila.data('factor')) || 1;
            const cant = parseFloat(fila.find('.cantidad').val()) || 0;
            items.push({
                producto_id: fila.data('id'),
                cantidad: (modo === 'referencia') ? (cant * factor) : cant,
                notas: fila.find('.input-notas').val()
            });
        });

        if (items.length === 0) return Swal.fire('Vacío', 'Agregue productos.', 'warning');

        $.post('../controllers/pedidosVendedorController.php', {
            action: 'guardar',
            almacen_id: almacen,
            cliente_id: cliente,
            prioridad: $('#prioridad').val(),
            observaciones: $('#observaciones').val(),
            items: JSON.stringify(items)
        }, function(res) {
           if (res.success) {
        Swal.fire({
            title: '¡Éxito!',
            text: res.message,
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Esto recarga la página completa
                location.reload();
            }
        });
        
        // Mantener esto por si el usuario cierra el modal antes de dar OK
        $('#modalNuevoPedido').modal('hide');
    }else { Swal.fire('Error', res.message, 'error'); }
        });
    }

    /* ── Ver pedido ── */
    function verPedido(id) {
        $.get('../controllers/pedidosVendedorController.php', { action: 'ver_detalle', id: id }, function(res) {
            if (res.success) {
                const p = res.pedido;
                $('#view_folio').text(p.folio);
                $('#view_cliente').text(p.cliente);
                $('#view_almacen').html(`<i class="bi bi-geo-alt"></i> ${p.almacen_nombre}`);
                $('#view_vendedor').html(`<i class="bi bi-person"></i> ${p.vendedor_nombre}`);
                $('#view_fecha').text(p.fecha_solicitud);
                $('#view_prioridad').html(`<span class="badge bg-dark">${p.prioridad}</span>`);
                $('#view_estatus_modal').html(p.estatus == 1
                    ? '<span class="badge bg-warning text-dark">Pendiente</span>'
                    : '<span class="badge bg-success">Cubierto</span>');
                $('#view_observaciones').text(p.observaciones || 'Sin notas adicionales.');

                let html = '';
                res.detalles.forEach(d => {
                    html += `<tr>
                        <td><div class="fw-bold">${d.producto_nombre}</div></td>
                        <td class="text-center fw-bold">${d.cantidad} ${d.unidad_medida}</td>
                        <td class="small text-body-secondary d-none d-md-table-cell">${d.notas_producto || '-'}</td>
                    </tr>`;
                });
                $('#tablaVerDetalles tbody').html(html);
                $('#modalVerPedido').modal('show');
            }
        });
    }

    /* ── Marcar cubierto ── */
    function marcarCubierto(id) {
        Swal.fire({
            title: '¿Marcar como cubierto?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controllers/pedidosVendedorController.php', { action: 'cubrir', id: id }, function(res) {
                    if (res.success) loadPedidos();
                });
            }
        });
    }
    </script>
</body>
</html>