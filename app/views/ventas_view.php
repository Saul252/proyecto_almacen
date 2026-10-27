<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | Sistema</title>
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        /* Variables de color para Modo Claro (por defecto) */
        :root {

            --card-bg: #ffffff;
            --card-shadow: rgba(15, 23, 42, 0.08);
            --filter-box-bg: #f8fafc;
            --filter-box-border: #e2e8f0;
            --form-label-color: #64748b;
            --table-header-bg: #0f172a;
            --table-header-text: #94a3b8;
            --tooltip-bg: #212529;
            --tooltip-text: #ffffff;
        }

        /* Sobrescritura de variables cuando se activa el Modo Oscuro */
        [data-bs-theme="dark"] {

            --card-bg: #1e293b;
            --card-shadow: rgba(0, 0, 0, 0.35);
            --filter-box-bg: #0f172a;
            --filter-box-border: #334155;
            --form-label-color: #94a3b8;
            --table-header-bg: #020617;
            --table-header-text: #cbd5e1;
            --tooltip-bg: #020617;
            --tooltip-text: #f8fafc;
        }

        body {
            background-color: var(--bg-main);
            padding-bottom: 40px;
            transition: background-color 0.3s ease;
        }


        .main-card {
            background: var(--card-bg);
            border-radius: 20px;

            box-shadow: 0 10px 30px var(--card-shadow);
            overflow: hidden;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .page-header-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 1.5rem 2rem;
        }

        .card-filter-box {
            background-color: var(--filter-box-bg);
            border: 1px solid var(--filter-box-border);
            border-radius: 18px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: var(--form-label-color);
            text-transform: uppercase;
        }

        .btn-action-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .btn-action-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            color: #ffffff;
        }

        .table-custom-header {
            background-color: var(--table-header-bg) !important;
            color: var(--table-header-text) !important;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .table-custom-header th {
            background-color: transparent !important;
            color: var(--table-header-text) !important;
            border-bottom: none !important;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .total-badge-card {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 14px 28px;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .entregado-tooltip {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: help;
        }

        .tooltip-custom {
            position: absolute;
            bottom: 130%;
            left: 50%;
            transform: translateX(-50%);
            min-width: 240px;
            max-width: 300px;
            padding: .6rem .8rem;
            background: var(--tooltip-bg);
            color: var(--tooltip-text);
            border-radius: .6rem;
            font-size: .82rem;
            line-height: 1.3;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: .2s ease;
            z-index: 9999;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .25);
        }

        .tooltip-custom::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: var(--tooltip-bg) transparent transparent transparent;
        }

        .entregado-tooltip:hover .tooltip-custom {
            opacity: 1;
            visibility: visible;
        }

        .fixed-top {
            z-index: 1050;
        }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">
        <div class="main-card">
            <form id="formEditarSolicitud">
                <input type="hidden" id="editar_venta_id" name="cotizacion_id" value="">

                <!-- Encabezado de la Página -->
                <div class="page-header-gradient d-flex justify-content-between align-items-center p-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-white d-flex align-items-center">
                            <i class="bi bi-cart-check-fill me-2 fs-3"></i> Módulo de Ventas
                        </h4>

                    </div>
                </div>

                <div class="p-4">
                    <div class="row g-4">
                        <!-- COLUMNA IZQUIERDA: Añadir Producto, Tabla de Detalles y Datos de Pago -->
                        <div class="col-lg-9">
                            <!-- Añadir Producto -->
                            <div class="card-filter-box p-4 mb-4 shadow-sm  rounded-4">
                                <label class="form-label text-body-secondary fw-semibold small mb-1">
                                    <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                                </label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <select id="buscadorProductosEditar"
                                        class="form-select select2-pagina border-slate-300">
                                        <option value="">Escribe SKU o nombre...</option>
                                    </select>
                                    <button type="button" class="btn btn-primary d-flex align-items-center px-3 "
                                        onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tabla de Artículos -->
                            <div class="card  shadow-sm rounded-4 overflow-hidden mb-4">
                                <div class="table-responsive" style="min-height:300px !important; max-height: 340px;">
                                    <table class="table table-hover align-middle mb-0" id="tablaDetalleEditar">
                                        <thead class="border-bottom">
                                            <tr class="text-secondary small fw-bold text-uppercase tracking-wider">
                                                <th class="ps-4 py-3" style="width: 30%;">Producto</th>
                                                <th style="width: 12%;">Existencias</th>
                                                <th style="width: 12%;">Cantidad</th>
                                                <th style="width: 18%;">Presentación</th>
                                                <th style="width: 18%;">Tipo Precio</th>
                                                <th style="width: 12%;">Precio Unit.</th>
                                                <th style="width: 15%;">Total</th>
                                                <th style="width: 5%;" class="text-center pe-4">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <!-- Filas dinámicas -->
                                        </tbody>
                                    </table>

                                    <!-- Estado Vacío -->
                                    <div id="emptyStateEditar" class="text-center py-5">
                                        <div class="avatar-icon-box text-body-secondary mx-auto mb-3">
                                            <i class="bi bi-cart-x fs-1 text-secondary opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">La lista está vacía</h6>
                                        <p class="text-body-secondary small mb-0">Busca e incluye artículos a esta venta
                                            desde el panel superior</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Formas de Pago y Observaciones -->
                            <div class="card  shadow-sm rounded-4 p-4">
                                <h6 class="fw-bold  d-flex align-items-center">
                                    <i class="bi bi-credit-card-2-front text-primary me-2"></i> Datos del Pago y
                                    Observaciones
                                </h6>

                                <div class="row g-3 ">
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold text-body-secondary">Monto
                                            Pagado</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-success-subtle text-success  fw-bold">$</span>
                                            <input type="number" id="monto_pagar"
                                                class="form-control border-light-subtle fw-bold fs-5 text-success"
                                                value="0" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold text-body-secondary">Método de
                                            Pago</label>
                                        <select id="metodo_pago" class="form-select border-light-subtle fw-semibold"
                                            onchange="verificarMetodoPago(this.value)">
                                            <option value="Efectivo">💵 Efectivo</option>
                                            <option value="Transferencia">🏦 Transferencia</option>
                                            <option value="Tarjeta">💳 Tarjeta</option>
                                            <option id="saldoAFavorSelect" class="d-none" value="saldoAfavor">Saldo a
                                                Favor</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="seccionSaldo" class="d-none">
                                    <div
                                        class="p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 my-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-success text-white rounded-circle p-1 me-2 d-flex align-items-center justify-content-center"
                                                style="width: 24px; height: 24px;">
                                                <i class="bi bi-wallet2 small"></i>
                                            </div>
                                            <span class="fw-bold text-success text-uppercase small tracking-wider">Saldo
                                                a Favor del Cliente</span>
                                        </div>

                                        <div class="row g-2 align-items-center">
                                            <div class="col-6">
                                                <label for="saldoFavorTotal"
                                                    class="form-label text-body-secondary small fw-semibold mb-1"
                                                    style="font-size: 0.75rem;">Disponible</label>
                                                <div class="input-group input-group-sm">
                                                    <span
                                                        class="input-group-text text-success fw-bold border-end-0">$</span>
                                                    <input type="number" id="saldoFavorTotal"
                                                        class="form-control text-success fw-bold border-start-0 shadow-none"
                                                        value="0.00" step="0.01" min="0" readonly>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="saldoAFavorInput"
                                                    class="form-label text-success small fw-bold mb-1"
                                                    style="font-size: 0.75rem;">A Aplicar</label>
                                                <div class="input-group input-group-sm shadow-sm">
                                                    <span
                                                        class="input-group-text bg-success text-white border-success">$</span>
                                                    <input type="number" id="saldoAFavorInput"
                                                        class="form-control border-success text-success fw-bold"
                                                        value="0" step="0.01" min="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Referencia condicional -->
                                <div class="mb-3" id="contenedorReferencia" style="display:none;">
                                    <label class="form-label small fw-bold text-secondary text-uppercase">Referencia de
                                        Transacción</label>
                                    <input type="text" id="inputReferencia" class="form-control border-light-subtle"
                                        placeholder="Ej. N° de Rastreo / Voucher">
                                </div>

                                <div id="pago_aviso" class="small mb-3 fw-semibold"></div>

                                <div>
                                    <label class="form-label small fw-semibold text-body-secondary">Notas /
                                        Observaciones</label>
                                    <input type="text" id="obsVenta" value=" "
                                        class="form-control border-light-subtle rounded-3"
                                        placeholder="Agrega detalles o instrucciones sobre esta orden...">
                                </div>
                            </div>
                        </div>

                        <!-- SIDEBAR DERECHO: Almacén, Cliente, Vendedor y Resumen Final -->
                        <div class="col-lg-3 d-flex flex-column gap-4">
                            <!-- Card Parámetros de la Venta -->
                            <div class="card  shadow-sm rounded-4 p-4">
                                <h6 class="fw-bold mb-3 d-flex align-items-center">
                                    <i class="bi bi-sliders text-primary me-2"></i> Datos de la Venta
                                </h6>
                                <div class="d-flex flex-column gap-3">
                                    <!-- 1. Almacén -->
                                    <div>
                                        <label class="form-label text-body-secondary fw-semibold small mb-1">
                                            <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                                        </label>
                                        <select name="almacen_id_editar" id="almacen_id_editar"
                                            class="form-select  shadow-sm rounded-3 py-2" required>
                                            <?php foreach ($almacenes as $a): ?>
                                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- 2. Cliente -->
                                    <div>
                                        <label class="form-label text-body-secondary fw-semibold small mb-1">
                                            <i class="bi bi-person me-1 text-primary"></i> Cliente
                                        </label>
                                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                            <select name="cliente_id_editar" id="cliente_id_editar"
                                                class="form-select  select2-pagina" required>
                                                <option value="">Seleccionar cliente...</option>
                                            </select>
                                            <button class="btn btn-primary-light  px-3" type="button"
                                                onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                                <i class="bi bi-person-plus-fill"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- 3. Vendedor -->
                                    <div>
                                        <label class="form-label text-body-secondary fw-semibold small mb-1">
                                            <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                                        </label>
                                        <select name="select-vendedor1" id="select-vendedor1"
                                            class="form-select  shadow-sm rounded-3 py-2 select2-pagina" required>
                                            <option value="">Seleccionar vendedor...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen Total y Finalización -->
                            <div
                                class="card  shadow-sm rounded-4 p-4 bg-gradient-dark d-flex flex-column justify-content-between flex-grow-1">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small"
                                            style="font-size: 0.75rem;">
                                            <i class="bi bi-receipt me-1"></i> Resumen Final
                                        </span>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                            Orden Lista
                                        </span>
                                    </div>

                                    <p class="text-success-50 small mb-1 text-uppercase fw-semibold tracking-wider">
                                        Monto Total de la Operación
                                    </p>

                                    <div id="costoTotalCompraEditar"
                                        class="display-4 fw-black text-success mb-2 tracking-tight">
                                        $0.00
                                    </div>

                                    <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar"
                                        value="0">
                                </div>

                                <!-- Botones -->
                                <div class="pt-4 mt-3 border-top border-white-10 d-flex gap-2 align-items-center">
                                    <button type="button" onclick="location.reload()"
                                        class="btn btn-outline-dark rounded-pill px-3 py-2.5 fw-semibold border-white-10 btn-hover-light d-flex align-items-center justify-content-center">
                                        <i class="bi bi-arrow-clockwise me-1 fs-6"></i>
                                    </button>

                                    <button type="submit"
                                        class="btn btn-success rounded-pill px-3 py-2 fw-bold w-100 shadow d-flex align-items-center justify-content-center">
                                        <i class="bi bi-check2-circle me-2 fs-5"></i>
                                        <span>Finalizar Venta</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/clientes/clientesModal.php'; ?>
    <script>
        // =====================================================
        // ENFOCAR SELECTOR DE PRODUCTOS AL CARGAR
        // =====================================================
        function enfocarBuscadorProducto(intentos = 0) {
            const $buscador = $('#buscadorProductosEditar');

            if (!$buscador.length) return;

            if ($buscador.hasClass('select2-hidden-accessible')) {
                const $selection = $buscador.next('.select2-container').find('.select2-selection');

                if ($selection.length) {
                    $selection[0].focus();
                    console.log('[FOCUS] Foco en .select2-selection:', document.activeElement);

                    // 🔥 Resaltado marcado: borde grueso + glow + pulso
                    $selection.css({
                        'border-color': '#2563eb',
                        'border-width': '2px',
                        'background-color': 'rgba(37, 99, 235, 0.08)',
                        'box-shadow': '0 0 0 4px rgba(37, 99, 235, 0.35), 0 0 25px rgba(37, 99, 235, 0.55)',
                        'transition': 'all 0.3s ease',
                        'animation': 'focusPulse 1.2s ease-in-out infinite'
                    });

                    // Quitar el resaltado a los 2s
                    setTimeout(() => {
                        $selection.css({
                            'border-color': '',
                            'border-width': '',
                            'background-color': '',
                            'box-shadow': '',
                            'transition': 'all 0.3s ease',
                            'animation': ''
                        });
                    }, 2000);

                    return;
                }
            }

            if (intentos < 40) {
                setTimeout(() => enfocarBuscadorProducto(intentos + 1), 50);
            } else {
                console.warn('[FOCUS] Select2 no se inicializó después de 2s');
            }
        }
        // Llamar al cargar la página
        $(document).ready(function () {
            enfocarBuscadorProducto();
        });
        /* =========================================================
           CONFIG
           ========================================================= */
        const DEBUG = false;
        const log = (...a) => DEBUG && console.log(...a);
        const warn = (...a) => DEBUG && console.warn(...a);
        const err = (...a) => console.error(...a);

        const URL_ACCESO = '/myvet/app/controllers/accesoController.php';
        const URL_VENTAS = '/myvet/app/controllers/ventasController.php';
        const URL_EGRESOS = '/myvet/app/controllers/egresosController.php';

        /* =========================================================
           ESTADO
           ========================================================= */
        let totalCalculo = 0;
        let recalculandoFilaEditar = false;

        /* =========================================================
           HELPERS
           ========================================================= */
        const $almacen = () => $('#almacen_id_editar');
        const $cliente = () => $('#cliente_id_editar');
        const $tabla = () => $('#tablaDetalleEditar tbody');
        const $buscador = () => $('#buscadorProductosEditar');

        const getAlmacenId = () => $almacen().val();

        function formatMoney(n) {
            return Number(n || 0).toLocaleString('es-MX', {
                style: 'currency', currency: 'MXN'
            });
        }

        /* =========================================================
           INICIALIZACIÓN ÚNICA
           ========================================================= */
        $(async function init() {
            // Inicializar Select2 en la página
            // 1. Buscador de productos (sin dropdownParent por el overflow-hidden)
            $('#buscadorProductosEditar').select2({
                theme: 'bootstrap-5'
            });

            // 2. Select de vendedor
            $('#select-vendedor1').select2({
                theme: 'bootstrap-5'
            });

            // 3. Cualquier OTRO .select2-pagina que NO sean los dos anteriores
            $('.select2-pagina').not('#buscadorProductosEditar, #select-vendedor1').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5'
                });
            });

            const almacenId = getAlmacenId();

            await Promise.allSettled([
                cargarVendedores3(),
                almacenId ? recargarProductosEditar() : Promise.resolve(),
                cargarClientes()
            ]);

            // Almacén → recargar todo
            $almacen().on('change', function () {
                const id = this.value;
                if (!id) { warn('Almacén deseleccionado'); return; }
                log(`Almacén → ${id}`);
                vaciarTablaEditar();
                recargarProductosEditar();
                cargarClientes();
            });

            // Cliente → estatus financiero
            $cliente().on('select2:select', consultarEstatusFinanciero);

            // Delegación de eventos en la tabla
            $tabla().on('input', '.precio-unitario-editar', function () {
                this.dataset.editado = '1';
            });

            $tabla().on('input', '.cantidad-editar, .precio-unitario-editar', function () {
                calcularTotalSolEditar(this);
            });

            $tabla().on('change', '.unidad-select-editar', function () {
                actualizarEquivalencia(this);
                calcularPrecioSugeridoEditar(this);
            });

            $tabla().on('change', '.tipoPrecio-select-editar', function () {
                calcularPrecioSugeridoEditar(this);
            });

            $tabla().on('click', '[data-accion="quitar"]', function () {
                quitarFilaEditar($(this).data('id'));
            });

            // Saldo a favor
            $('#saldoAFavorInput').on('input change', manejarSaldoAFavor);
        });

        /* =========================================================
           VENDEDORES
           ========================================================= */
        async function cargarVendedores3(vendedor_id = null) {
            const select = document.getElementById('select-vendedor1');
            if (!select) return;

            const $select = $(select);
            const almacenId = getAlmacenId();
            const idSesion = "<?= $_SESSION['usuario_id'] ?? '' ?>";

            $select.empty().val(null).trigger('change.select2');

            try {
                const r = await fetch(
                    `${URL_ACCESO}?action=obtenerUsuarios&almacen_id=${encodeURIComponent(almacenId)}`
                );
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                const data = await r.json();

                if (data.success && Array.isArray(data.data)) {
                    const fragment = document.createDocumentFragment();
                    data.data.forEach(u => {
                        const opt = document.createElement('option');
                        opt.value = u.id;
                        opt.textContent = u.nombre;
                        fragment.appendChild(opt);
                    });
                    select.appendChild(fragment);
                    $select.trigger('change.select2');

                    const elegido = vendedor_id || idSesion;
                    if (elegido) $select.val(String(elegido)).trigger('change.select2');
                } else {
                    $select.empty()
                        .append('<option value="">No se pudieron cargar los usuarios</option>')
                        .trigger('change.select2');
                }
            } catch (e) {
                err('[cargarVendedores3]', e);
                $select.empty()
                    .append('<option value="">Error al cargar la lista</option>')
                    .trigger('change.select2');
            }
        }

        /* =========================================================
           CLIENTES
           ========================================================= */
        async function cargarClientes() {
            const select = document.getElementById('cliente_id_editar');
            if (!select) return;

            const almacenId = getAlmacenId();

            try {
                const r = await fetch(
                    `${URL_ACCESO}?action=obtenerClientes&almacen_id=${encodeURIComponent(almacenId)}`
                );
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                const data = await r.json();

                if (!data.success || !Array.isArray(data.data)) {
                    throw new Error(data.message || 'Respuesta inválida');
                }

                const filtrados = data.data.filter(c => {
                    const nombre = String(c.nombre_comercial || '').toLowerCase().trim();
                    const esPublico = nombre.includes('publico en general') ||
                        nombre.includes('público en general');
                    return esPublico ? String(c.almacen_id) === String(almacenId) : true;
                });

                // 1. Construir opciones con data-*
                const fragment = document.createDocumentFragment();

                const first = document.createElement('option');
                first.value = '';
                first.textContent = '-- Seleccione un cliente --';
                fragment.appendChild(first);

                filtrados.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = String(c.nombre_comercial || '');
                    opt.dataset.nombre = String(c.nombre_comercial || '');
                    opt.dataset.razonSocial = String(c.razon_social || '');
                    opt.dataset.contacto = String(c.contacto || '');
                    fragment.appendChild(opt);
                });

                select.replaceChildren(fragment);

                // 2. Destruir Select2 previo si existe
                if ($(select).hasClass('select2-hidden-accessible')) {
                    $(select).select2('destroy');
                }

                // 3. Inicializar Select2 con width: 'resolve' + matcher personalizado
                $(select).select2({
                    theme: 'bootstrap-5',
                    width: 'resolve',
                    matcher: function (params, data) {
                        if (!params.term || params.term.trim() === '') return data;

                        const term = String(params.term).toLowerCase().trim();
                        const text = String(data.text || '').toLowerCase();

                        const $el = $(data.element);
                        const razonSocial = String($el.data('razonSocial') || '').toLowerCase();
                        const contacto = String($el.data('contacto') || '').toLowerCase();

                        if (text.includes(term) || razonSocial.includes(term) || contacto.includes(term)) {
                            return data;
                        }

                        return null;
                    },
                    templateResult: function (state) {
                        if (!state.id) return state.text;

                        const $opt = $(state.element);
                        const nombre = String($opt.data('nombre') || state.text || '');
                        const razonSocial = String($opt.data('razonSocial') || '');
                        const contacto = String($opt.data('contacto') || '');

                        // Si no hay datos extra, solo el nombre
                        if (!razonSocial && !contacto) {
                            return $(`<span class="fw-semibold">${nombre.toUpperCase()}</span>`);
                        }

                        // Con datos extra: nombre arriba, razón + contacto abajo
                        return $(`
                    <div class="d-flex flex-column">
                        <span class="fw-semibold">${nombre.toUpperCase()}</span>
                        <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                            <small class="text-truncate" style="max-width:65%;">
                                ${razonSocial.toUpperCase()}
                            </small>
                            <small class="fw-semibold text-nowrap">
                                ${contacto ? `👤 ${contacto.toUpperCase()}` : ''}
                            </small>
                        </div>
                    </div>
                `);
                    },
                    templateSelection: function (state) {
                        if (!state.id) return state.text;

                        // 🔥 Solo el nombre en el select cerrado
                        const $opt = $(state.element);
                        return String($opt.data('nombre') || state.text || '').toUpperCase();
                    }
                });

                // 4. Autoseleccionar el primero
                if (filtrados.length) {
                    $cliente().val(filtrados[0].id).trigger('change');
                } else {
                    $cliente().val(null).trigger('change');
                }

            } catch (e) {
                console.error('[cargarClientes]', e);
                select.innerHTML = '<option value="">Error al cargar clientes</option>';
                $(select).trigger('change.select2');
            }
        }
        async function recargarProductosEditar() {
            const almacenId = getAlmacenId();
            if (!almacenId) return;

            const $select = $buscador();

            try {
                const r = await fetch(
                    `${URL_ACCESO}?action=obtenerProductosAlmacen&id=${encodeURIComponent(almacenId)}`
                );
                if (!r.ok) throw new Error(`HTTP ${r.status}`);

                const texto = await r.text();
                let data;
                try {
                    data = JSON.parse(texto);
                } catch {
                    err('[recargarProductos] JSON inválido. Servidor devolvió:', texto.slice(0, 500));
                    return;
                }

                if (!data.success) {
                    err('[recargarProductos] Error:', data.message);
                    return;
                }

                const fragment = document.createDocumentFragment();
                fragment.appendChild(new Option('Escribe SKU o nombre...', '', true, true));

                if (Array.isArray(data.data)) {
                    data.data.forEach(pr => {
                        const opt = new Option(`[${pr.sku}] ${pr.nombre}`, pr.producto_id, false, false);
                        $(opt).attr({
                            'data-nombre': pr.nombre || '',
                            'data-medidas': JSON.stringify(pr.medidas_adicionales || []),
                            'data-sku': pr.sku || '',
                            'data-um': pr.unidad_medida || '',
                            'data-ur': pr.unidad_reporte || '',
                            'data-premin': pr.precio_minorista || 0,
                            'data-premat': pr.precio_mayorista || 0,
                            'data-predis': pr.precio_distribuidor || 0,
                            'data-factor': pr.factor_conversion || 1,
                            'data-stock': pr.stock || 0
                        });
                        fragment.appendChild(opt);
                    });
                }

                $select.empty().append(fragment).trigger('change.select2');

            } catch (e) {
                err('[recargarProductosEditar]', e);
            }
        }

        /* =========================================================
           AGREGAR PRODUCTO (Select2)
           ========================================================= */
        $buscador().on('select2:select', function (e) {
            const d = e.params.data.element?.dataset;
            if (!d) return;

            const id = $(this).val();
            if ($(`#filaEditar-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            $('#emptyStateEditar').addClass('d-none');

            const medidas = JSON.parse(d.medidas || '[]');
            const opcionesUnidad = medidas.map(m =>
                `<option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">
            ${m.nombre}
        </option>`).join('');

            const preMin = parseFloat(d.premin) || 0;
            const preMat = parseFloat(d.premat) || 0;
            const preDis = parseFloat(d.predis) || 0;
            const factor = parseFloat(d.factor) || 1;

            $tabla().append(`
        <tr id="filaEditar-${id}">
            <td>
                <b>${d.nombre}</b><br>
                <small class="text-body-secondary">${d.sku}</small>
            </td>
            <td class="ps-4">
                <input type="hidden"
                    name="itemsEditar[${id}][exis]"
                    class="cantidad-exis"
                    value="${d.stock}" min="0.01">
                <span>${d.stock / d.factor}</span>
                <b> ${d.ur}</b>
            </td>
            <td>
                <input type="number"
                    name="itemsEditar[${id}][cant]"
                    class="form-control cantidad-editar"
                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductosEditar').select2('open'); }"
                   
                    step="0.01" value="1" min="0.01" required>
                <input type="hidden"
                    name="itemsEditar[${id}][equivalencia]"
                    class="form-control equivalencia" value="1">
            </td>
            <td>
                <select name="itemsEditar[${id}][unidad]"
                    class="form-select unidad-select-editar">
                    ${opcionesUnidad}
                </select>
            </td>
            <td>
                <select name="itemsEditar[${id}][tipoPrecio]"
                    class="form-select tipoPrecio-select-editar">
                     <option value="seleccionar" data-precio="0">Seleccione</option>
                    <option value="minorista"    data-precio="${preMin}">Min $${(preMin * factor).toFixed(2)} x ${d.ur}</option>
                    <option value="mayorista"    data-precio="${preMat}">May $${(preMat * factor).toFixed(2)} x ${d.ur}</option>
                    <option value="distribuidor" data-precio="${preDis}">Dis $${(preDis * factor).toFixed(2)} x ${d.ur}</option>
                </select>
            </td>
            <td>
                <input type="number" lang="en-US"
                    name="itemsEditar[${id}][precioUnitario]"
                    class="form-control precio-unitario-editar"
                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductosEditar').select2('open'); }"
                    step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td style="min-width:140px;">
                <input type="number" lang="en-US"
                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductosEditar').select2('open'); }"
                    name="itemsEditar[${id}][precio]"
                    class="form-control precio-total-editar fw-bold text-success"
                    step="0.01" min="0" placeholder="0.00"
                    style="font-size:1.1rem;height:40px;">
            </td>
            <td class="text-center">
                <button type="button"
                    class="btn btn-link text-danger p-0"
                    data-accion="quitar" data-id="${id}">
                    <i class="bi bi-trash fs-5"></i>
                </button>
            </td>
        </tr>
    `);

            // 🔥 Enfocar el input de cantidad de ESTA fila
            const $fila = $(`#filaEditar-${id}`);
            const $cantidad = $fila.find('.cantidad-editar');

            if ($cantidad.length) {
                const inputCantidad = $cantidad[0];

                // Enfocar
                inputCantidad.focus();

                // Seleccionar el contenido para que el usuario escriba directamente
                inputCantidad.select();

                // Resaltar visualmente para que el usuario sepa dónde está
                $cantidad.css({
                    'box-shadow': '0 0 0 3px rgba(37, 99, 235, 0.25)',
                    'border-color': '#2563eb',
                    'transition': 'all 0.3s'
                });

                setTimeout(() => {
                    $cantidad.css({
                        'box-shadow': '',
                        'border-color': '',
                        'transition': 'all 0.3s'
                    });
                }, 1500);
            }

            $(this).val(null).trigger('change');
        });  /* =========================================================
           CÁLCULOS
           ========================================================= */
        function calcularTotalSolEditar(input) {
            if (recalculandoFilaEditar) return;
            recalculandoFilaEditar = true;

            try {
                if (input) {
                    const fila = input.closest('tr');
                    if (fila) {
                        const cant = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
                        const precio = parseFloat(fila.querySelector('.precio-unitario-editar')?.value) || 0;
                        fila.querySelector('.precio-total-editar').value = (cant * precio).toFixed(2);
                    }
                }

                let total = 0;
                $tabla().find('.precio-total-editar').each(function () {
                    total += parseFloat(this.value) || 0;
                });

                document.getElementById('costoTotalCompraEditar').textContent = formatMoney(total);
                document.getElementById('totalCotizacionEditar').value = total;
                totalCalculo = total;
                $('#saldoAFavorInput').val(0);

            } finally {
                recalculandoFilaEditar = false;
            }
        }

        function actualizarEquivalencia(input) {
            const fila = $(input).closest('tr');
            const unidad = fila.find('.unidad-select-editar').val();
            if (!unidad || unidad === '0') return;

            let eq = parseFloat(
                fila.find('.unidad-select-editar option:selected').data('equivalencia')
            );
            if (isNaN(eq) || eq <= 0) return;

            eq = 1 / eq;
            if (eq >= 1 && Math.abs(eq - Math.round(eq)) <= 0.00001) {
                eq = Math.round(eq);
            }

            fila.find('.equivalencia').val(eq);
            calcularTotalSolEditar(input);
        }

        function calcularPrecioSugeridoEditar(select) {
            const fila = select.closest('tr');
            const inputPrecio = fila.querySelector('.precio-unitario-editar');

            // Respetar precio editado manualmente
            if (inputPrecio.dataset.editado === '1') return;

            const unidadSelect = fila.querySelector('.unidad-select-editar');
            const tipoSelect = fila.querySelector('.tipoPrecio-select-editar');

            const eq = Number(unidadSelect.options[unidadSelect.selectedIndex]?.dataset.equivalencia || 1);
            const base = Number(tipoSelect.options[tipoSelect.selectedIndex]?.dataset.precio || 0);
            const sug = eq > 0 ? base / eq : base;

            inputPrecio.value = sug.toFixed(2);

            const cant = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
            fila.querySelector('.precio-total-editar').value = (sug * cant).toFixed(2);

            calcularTotalSolEditar(null);
        }

        function quitarFilaEditar(id) {
            $(`#filaEditar-${id}`).remove();
            if (!$tabla().children('tr').length) {
                $('#emptyStateEditar').removeClass('d-none');
            }
            calcularTotalSolEditar(null);
        }

        function vaciarTablaEditar() {
            $tabla().empty();
            $('#emptyStateEditar').removeClass('d-none');
            calcularTotalSolEditar(null);
        }

        /* =========================================================
           ESTATUS FINANCIERO
           ========================================================= */
        function consultarEstatusFinanciero() {
            const id = $cliente().val();
            if (!id) return;

            fetch(`${URL_VENTAS}?action=obtenerEstatusCliente&id=${encodeURIComponent(id)}`)
                .then(r => r.json())
                .then(res => {
                    const saldoAFavor = parseFloat(res.saldo_a_favor || 0);
                    $('#saldoFavorTotal').val(saldoAFavor);

                    const tieneSaldo = saldoAFavor > 0;
                    $('#saldoAFavorSelect').toggleClass('d-none', !tieneSaldo);
                    $('#saldoAFavorInput').toggleClass('d-none', !tieneSaldo);
                })
                .catch(e => err('[consultarEstatusFinanciero]', e));
        }

        /* =========================================================
           SALDO A FAVOR
           ========================================================= */
        function manejarSaldoAFavor() {
            let ingresado = parseFloat(this.value) || 0;
            const max = parseFloat($('#saldoFavorTotal').val()) || 0;

            const $total = $('#totalCotizacionEditar');
            if (!$total.data('precio-base')) {
                $total.data('precio-base', parseFloat($total.val()) || 0);
            }
            const base = parseFloat($total.data('precio-base')) || 0;

            if (ingresado < 0) { ingresado = 0; $(this).val(0); }
            if (ingresado > max) { ingresado = max; $(this).val(max); }

            const nuevo = Math.max(0, base - ingresado);
            $total.val(nuevo.toFixed(2));
            $('#costoTotalCompraEditar').text(nuevo.toFixed(2));
        }

        /* =========================================================
           MÉTODO DE PAGO
           ========================================================= */
        function verificarMetodoPago(metodo) {
            const contenedor = document.getElementById('contenedorReferencia');
            const input = document.getElementById('inputReferencia');
            if (!contenedor || !input) return;

            const $seccionSaldo = $('#seccionSaldo');
            const resetSaldo = () => {
                $('#saldoAFavorInput').val(0);
                document.getElementById('costoTotalCompraEditar').textContent = formatMoney(totalCalculo);
                document.getElementById('totalCotizacionEditar').value = totalCalculo;
            };

            switch (metodo) {
                case 'saldoAfavor':
                    contenedor.style.display = 'none';
                    input.required = false;
                    input.value = '';
                    $seccionSaldo.removeClass('d-none');
                    break;

                case 'Tarjeta':
                case 'Transferencia':
                    contenedor.style.display = 'block';
                    input.required = true;
                    $seccionSaldo.addClass('d-none');
                    resetSaldo();
                    break;

                default:
                    contenedor.style.display = 'none';
                    input.required = false;
                    input.value = '';
                    $seccionSaldo.addClass('d-none');
                    resetSaldo();
            }
        }

        /* =========================================================
           SUBMIT: GUARDAR VENTA
           ========================================================= */
        $('#formEditarSolicitud').on('submit', async function (e) {
            e.preventDefault();

            if (!$tabla().children('tr').length) {
                Swal.fire('Error', 'Agregue al menos un producto a la venta', 'warning');
                return;
            }

            const $btn = $(this).find('button[type="submit"]').prop('disabled', true);

            const totalVenta = parseFloat($('#totalCotizacionEditar').val()) || 0;
            const montoPagado = parseFloat($('#monto_pagar').val()) || 0;
            const almacenId = getAlmacenId();

            const payload = {
                accion: 'guardar_venta',
                id_cliente: $cliente().val(),
                id_vendedor: $('#select-vendedor1').val(),
                monto_pagado: montoPagado,
                monto_usado_favor: $('#saldoAFavorInput').val(),
                subtotal: totalVenta,
                total_venta: totalVenta,
                almacen_id: almacenId,
                metodo_pago: $('#metodo_pago').val(),
                referencia: $('#inputReferencia').val(),
                observaciones: $('#obsVenta').val() || '',
                usar_saldo_favor: Number($('#saldoAFavorInput').val()) > 0 ? 1 : 0,
                carrito: []
            };

            $tabla().children('tr').each(function () {
                const $f = $(this);
                const id = this.id.replace('filaEditar-', '');
                const unidadSel = $f.find('.unidad-select-editar option:selected');
                const tipoSel = $f.find('.tipoPrecio-select-editar option:selected');

                const cantIni = parseFloat($f.find('.cantidad-editar').val()) || 0;
                const eq = parseFloat($f.find('.equivalencia').val()) || 1;
                const cantT = cantIni * eq;
                const cantFinal = (cantT % 1 > 0) ? cantT.toFixed(2) : cantT;

                payload.carrito.push({
                    producto_id: id,
                    almacen_id: almacenId,
                    cantidad: cantFinal,
                    unidad_base: unidadSel.val(),
                    noEliminar: $f.find('.noEliminar').val(),
                    idunidadMedida: unidadSel.data('medida-id'),
                    unidadEquivalencia: unidadSel.data('equivalencia'),
                    tipo_precio: tipoSel.val(),
                    precio_unitario: $f.find('.precio-unitario-editar').val(),
                    subtotal: $f.find('.precio-total-editar').val()
                });
            });

            Swal.fire({ title: 'Actualizando venta...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const resp = await fetch(URL_VENTAS, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const res = await resp.json();

                if (res.status !== 'success') {
                    throw new Error(res.message || 'Error desconocido');
                }

                const tieneDeuda = montoPagado < totalVenta;
                const esTotal = (res.total_entregado ?? 0) >= (res.total_pedido ?? 0);

                let htmlExtra = `<p class="mb-2">Folio:
            <span class="badge bg-body-secondary text-body border border-secondary-subtle">${res.folio || 'N/A'}</span>
        </p>`;

                if (tieneDeuda) {
                    htmlExtra += `<div class="alert bg-danger-subtle text-danger-emphasis py-1 px-2 mb-2 rounded-3 small">
                <i class="bi bi-exclamation-circle-fill me-1"></i> Saldo pendiente registrado en cuenta
            </div>`;
                }

                const r = await Swal.fire({
                    title: esTotal ? '¡Venta Exitosa!' : 'Entrega Parcial Registrada',
                    html: `
                <div class="alert bg-body-secondary text-body small text-start py-2 mb-3 rounded-3">
                    ${res.message || 'Operación realizada correctamente.'}
                </div>
                ${htmlExtra}
                <p class="text-body-secondary small mb-0">¿Deseas imprimir el comprobante?</p>
            `,
                    icon: esTotal ? 'success' : 'warning',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-receipt"></i> Con Precios',
                    denyButtonText: '<i class="bi bi-receipt"></i> Ticket Formal',
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#34c759',
                    denyButtonColor: '#5856d6',
                    customClass: { popup: 'rounded-4 shadow-lg bg-body text-body' }
                });

                let url = '';
                if (r.isConfirmed) url = `/myvet/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                if (r.isDenied) url = `/myvet/app/backend/ventas/ticketFormal.php?id=${res.id_venta}`;
                if (url) window.open(url, '_blank');
                location.reload();

            } catch (e) {
                err('[submit guardar_venta]', e);
                Swal.fire({
                    title: 'Error',
                    text: e.message || 'Fallo de conexión con el servidor',
                    icon: 'error',
                    customClass: { popup: 'rounded-4 bg-body text-body' }
                });
                $btn.prop('disabled', false);
            }
        });

        /* =========================================================
           GASTO / SALIDA DE DINERO
           ========================================================= */
        async function guardarComoGastoSalidadeDinero() {
            try {
                const rFolio = await fetch(`${URL_EGRESOS}?action=getSiguienteFolioGasto`);
                const dFolio = await rFolio.json();
                if (!dFolio.success) throw new Error('No se pudo obtener el folio.');

                const fd = new FormData();
                fd.append('folio', dFolio.folio);
                fd.append('monto', parseFloat($('#totalCotizacionEditar').val()) || 0);
                fd.append('fecha', new Date().toISOString().split('T')[0]);
                fd.append('observaciones', `SALIDA DE DINERO POR EDICIÓN DE VENTA ${$('#editar_venta_id').val()}`);
                fd.append('venta_id', $('#editar_venta_id').val());

                const r = await fetch(`${URL_EGRESOS}?action=guardarSalidaDinero`, { method: 'POST', body: fd });
                const d = await r.json();

                if (d.success || d.status === 'success') {
                    await Swal.fire('Éxito', 'Salida de dinero registrada correctamente.', 'success');
                    location.reload();
                } else {
                    throw new Error(d.message || 'Error al procesar el egreso');
                }
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    </script>
</body>

</html>