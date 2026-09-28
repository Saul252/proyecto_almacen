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
        document.addEventListener('DOMContentLoaded', function () {
            recargarProductosEditar();
            const selectAlmacen = document.getElementById('almacen_id_editar');

            if (selectAlmacen) {
                selectAlmacen.addEventListener('change', function (e) {
                    const almacenId = this.value; // ID del almacén seleccionado
                    const textoSeleccionado = this.options[this.selectedIndex].text; // Nombre del almacén

                    if (almacenId) {
                        console.log(`Almacén cambiado a ID: ${almacenId} - ${textoSeleccionado}`);
                        recargarProductosEditar();
                        vaciarTablaEditar();



                        // 🚀 Coloca aquí la función o lógica que deseas ejecutar
                        // Ejemplo: cargarProductosPorAlmacen(almacenId);
                    } else {
                        console.log('Se deseleccionó el almacén');
                    }
                });
            }
        });

        let totalCalculo = 0;
        async function recargarProductosEditar() {
            const id = $('#almacen_id_editar').val();
            const $select = $('#buscadorProductosEditar');

            if (!id) return;

            const url = `/myvet/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${id}`;
            console.log("Consultando URL:", url);

            // 1. Hacemos la petición
            const resp = await fetch(url);

            // 2. Leemos la respuesta como TEXTO plano primero para ver si hay errores de PHP
            const textoServidor = await resp.text();
            console.log("=== RESPUESTA CRUDA DEL SERVIDOR ===");
            console.log(textoServidor);

            // 3. Intentamos convertir a JSON
            let res;
            try {
                res = JSON.parse(textoServidor);
            } catch (errJson) {
                console.error("❌ EL SERVIDOR NO DEVOLVIÓ JSON VÁLIDO. Mira el texto arriba.");
                return;
            }

            console.log("=== JSON DECODIFICADO ===", res);

            if (!res.success) {
                console.error("❌ El PHP devolvió success: false ->", res.message);
                return;
            }

            // 4. Si todo está bien, actualizamos el Select2
            $select.empty();
            $select.append(new Option("Escribe SKU o nombre...", "", true, true));

            if (Array.isArray(res.data)) {
                $select.empty();
                $select.append(new Option("Escribe SKU o nombre...", "", true, true));
                res.data.forEach(pr => {
                    const option = new Option(`[${pr.sku}] ${pr.nombre}`, pr.producto_id, false, false);

                    $(option).attr({
                        'data-nombre': pr.nombre || '',
                        'data-medidas': JSON.stringify(pr.medidas_adicionales || []),
                        'data-sku': pr.sku || '',
                        'data-um': pr.unidad_medida || '',
                        'data-ur': pr.unidad_reporte || '',
                        'data-premin': pr.precio_minorista || 0,
                        'data-premat': pr.precio_mayorista || 0,
                        'data-predis': pr.precio_distribuidor || 0,
                        'data-factor': pr.factor_conversion || 1,
                        'data-stock': (pr.stock) || 1
                    });

                    $select.append(option);
                });
            }

            // Notificar a Select2 del cambio
            $select.trigger('change.select2');
            cargarClientes();
        }
        const URL_CONTROLADOR_EDITAR = '/myvet/app/controllers/cotizacionesController.php';
        let total_inicial = 0;
        let cliente_nombre = '';
        let dataEdicion = null;
        let recalculandoFilaEditar = false;

        // Inicializar Select2 en la página
        $('.select2-pagina').select2({
            theme: 'bootstrap-5'
        });

        $(document).ready(async function () {
            try {

                await cargarVendedores3();
            } catch (error) {
                console.error("Error en inicialización:", error);
            }
        });

        // =====================================================
        // CARGAR VENDEDORES
        // =====================================================
        async function cargarVendedores3(vendedor_id = null) {
            const almacenId = $('#almacen_id_editar').val();
            const $select = $('#select-vendedor1');

            if (!$select.length) return;

            // ID del usuario desde la sesión de PHP
            const idUsuarioSesion = "<?= $_SESSION['usuario_id'] ?? '' ?>";

            // Limpia las opciones directamente con jQuery para mantener sincronizado Select2
            $select.empty();

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacenId}`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();

                if (resultado.success && Array.isArray(resultado.data)) {
                    // Opción por defecto (vacía para permitir la selección)
                    $select.append(new Option('-- Seleccionar Vendedor --', ''));

                    resultado.data.forEach(usuario => {
                        const opcion = new Option(usuario.nombre, usuario.id, false, false);
                        $select.append(opcion);
                    });

                    // Prioridad: 
                    // 1. vendedor_id (si se pasa al llamar a la función)
                    // 2. idUsuarioSesion (usuario logueado en la sesión de PHP)
                    const valorASeleccionar = vendedor_id || idUsuarioSesion;

                    if (valorASeleccionar) {
                        $select.val(valorASeleccionar);
                    }

                    // Notifica a Select2 del cambio de manera segura
                    $select.trigger('change.select2');

                } else {
                    $select.append(new Option('No se pudieron cargar los usuarios', ''));
                }
            } catch (error) {
                $select.append(new Option('Error al cargar la lista', ''));
                console.error('Error al ejecutar cargarVendedores3:', error);
            }
        }
        async function cargarClientes() {
            console.log("cargo clientes");

            // Obtenemos el ID del almacén actual
            const almacenId = $('#almacen_id_editar').val();
            const select = document.getElementById('cliente_id_editar');
            if (!select) return;

            // Limpiamos el select antes de poblarlo
            select.innerHTML = '<option value="">-- Seleccione un cliente --</option>';

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${almacenId}`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();
                console.log(resultado);

                if (resultado.success && Array.isArray(resultado.data)) {

                    // FILTRADO: 
                    // 1. Conserva clientes cuyo nombre NO contenga "público en general" (clientes normales).
                    // 2. Para "público en general", solo conserva el que coincida con el almacenId actual.
                    const clientesFiltrados = resultado.data.filter(cliente => {
                        const nombreNorm = cliente.nombre_comercial.toLowerCase().trim();
                        const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                        if (esPublicoGeneral) {
                            // Revisa que coincida el ID del almacén (compara tanto número como string)
                            return cliente.almacen_id == almacenId;
                        }

                        // Si es un cliente regular, se muestra siempre
                        return true;
                    });
                    select.innerHTML = '<option value="">-- Seleccione un cliente --</option>';
                    // Llenamos el select únicamente con la lista filtrada
                    clientesFiltrados.forEach(cliente => {
                        const opcion = document.createElement('option');
                        opcion.value = cliente.id;
                        opcion.textContent = `${cliente.nombre_comercial}`;
                        select.appendChild(opcion);
                    });
                    if (clientesFiltrados.length > 0) {
                        const primerClienteId = clientesFiltrados[0].id;

                        // Soporte nativo y compatibilidad con Select2/jQuery
                        $('#cliente_id_editar').val(primerClienteId).trigger('change');
                    }

                } else {
                    select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                }
            } catch (error) {
                select.innerHTML = '<option value="">Error al cargar la lista</option>';
                console.error('Error al ejecutar cargarClientes:', error);
            }
        }

        // =====================================================
        // CALCULAR TOTAL EDITAR
        // =====================================================
        function calcularTotalSolEditar(input) {
            if (recalculandoFilaEditar) return;
            recalculandoFilaEditar = true;

            try {
                const fila = input ? input.closest('tr') : null;
                if (fila) {
                    const cantidad = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
                    const precioUnitarioOriginal = parseFloat(fila.querySelector('.precio-unitario-editar')?.value) || 0;

                    const precioTotal = cantidad * precioUnitarioOriginal;
                    fila.querySelector('.precio-total-editar').value = precioTotal.toFixed(2);
                }

                // SUMA GENERAL
                let totalCompraEditar = 0;
                document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
                    totalCompraEditar += parseFloat(el.value) || 0;
                });

                document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });

                document.getElementById('totalCotizacionEditar').value = totalCompraEditar;
                totalCalculo = totalCompraEditar;
                $('#saldoAFavorInput').val(0);

            } finally {
                recalculandoFilaEditar = false;
            }
        }

        // =====================================================
        // RECARGAR PRODUCTOS
        // =====================================================
        // =====================================================
        // EVENTO SELECT2: AGREGAR PRODUCTO
        // =====================================================
        $('#buscadorProductosEditar').on('select2:select', function (e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();

            if ($(`#filaEditar-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            $('#emptyStateEditar').addClass('d-none');
            const medidas = JSON.parse(d.medidas || '[]');

            let opcionesUnidad = ``;
            medidas.forEach(m => {
                opcionesUnidad += `
                <option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">
                    ${m.nombre}
                </option>`;
            });

            const preMin = parseFloat(d.premin) || 0;
            const preMat = parseFloat(d.premat) || 0;
            const preDis = parseFloat(d.predis) || 0;
            const factor = parseFloat(d.factor) || 1;

            $('#tablaDetalleEditar tbody').append(`
            <tr id="filaEditar-${id}">
            
                <td >
                    <b>${d.nombre}</b><br>
                    <small class="text-body-secondary">${d.sku}</small>
                </td>
<td class="ps-4">
<input 
                        type="hidden"
                        name="itemsEditar[${id}][exis]"
                        class=" cantidad-exis" 
                      
                        value="${d.stock}"
                        min="0.01"
                        
                        >
<spam   
                     
                      
                       >${d.stock / d.factor}</spam>
                   <b> ${d.ur}</b>
                    
                </td>
                <td>
                    <input 
                        type="number"
                        name="itemsEditar[${id}][cant]"
                        class="form-control cantidad-editar"
                        step="0.01"
                        value="1"
                        min="0.01"
                        required
                        oninput="actualizarEquivalencia(this);calcularTotalSolEditar(this)">
                    <input 
                        type="hidden"
                        name="itemsEditar[${id}][equivalencia]"
                        class="form-control equivalencia"
                        value="1">
                </td>

                <td>
                    <select 
                        name="itemsEditar[${id}][unidad]" 
                        class="form-select unidad-select-editar"
                        onchange="actualizarEquivalencia(this);calcularPrecioSugeridoEditar(this)">
                        <option value="" data-equivalencia="" data-medida-id="">Seleccione</option>
                        ${opcionesUnidad}
                    </select>
                </td>
                
                <td>
                    <select 
                        name="itemsEditar[${id}][tipoPrecio]" 
                        class="form-select tipoPrecio-select-editar"
                        onchange="calcularPrecioSugeridoEditar(this)">
                        <option value="seleccionar" data-precio="0">Seleccione</option>
                        <option value="minorista" data-precio="${preMin}">Min $${(preMin * factor).toFixed(2)} x ${d.ur}</option>
                        <option value="mayorista" data-precio="${preMat}">May $${(preMat * factor).toFixed(2)} x ${d.ur}</option>
                        <option value="distribuidor" data-precio="${preDis}">Dis $${(preDis * factor).toFixed(2)} x ${d.ur}</option>
                    </select>
                </td>

                <td>
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${id}][precioUnitario]"
                        class="form-control precio-unitario-editar"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        required
                        oninput="calcularTotalSolEditar(this)">
                </td>

                <td style="min-width:140px;">
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${id}][precio]"
                        class="form-control precio-total-editar fw-bold text-success "
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        oninput="calcularTotalSolEditar(this)"
                        style="font-size:1.1rem; height:40px;">
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger p-0" onclick="quitarFilaEditar('${id}')">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </td>
            </tr>
            `);

            $(this).val(null).trigger('change');
        });

        function validarExistencias(fila, cantidad, equivalencia, existencias) {

            const maximoPermitido = existencias / equivalencia;
            const soli = cantidad * equivalencia;
            console.log(cantidad, equivalencia, existencias);
            console.log(soli, maximoPermitido);

            if (soli > existencias) {

                fila.find('.cantidad-editar').val(maximoPermitido.toFixed(4));

                Swal.fire({
                    icon: 'warning',
                    title: '¿Deseas continiar Existencias insuficientes?',
                    text: `Solo hay ${maximoPermitido.toFixed(4)} unidades disponibles.`
                });

                return cantidad;
            }

            return cantidad;
        }

        function actualizarEquivalencia(input) {

            const fila = $(input).closest('tr');

            const cantidad = parseFloat(fila.find('.cantidad-editar').val()) || 0;
            const existencias = parseFloat(fila.find('.cantidad-exis').val()) || 0;

            const unidad = fila.find('.unidad-select-editar').val();

            // Todavía no se eligió una unidad
            if (!unidad || unidad == "0") {
                return;
            }

            let equivalencia = parseFloat(
                fila.find('.unidad-select-editar option:selected').data('equivalencia')
            );

            if (isNaN(equivalencia) || equivalencia <= 0) {
                return;
            }

            // Obtener la equivalencia inversa

            equivalencia = 1 / equivalencia;

            // Solo redondear si es >= 1 y está muy cerca de un entero
            if (
                equivalencia >= 1 &&
                Math.abs(equivalencia - Math.round(equivalencia)) <= 0.00001
            ) {
                equivalencia = Math.round(equivalencia);
            }

            // Si está prácticamente en un entero, redondearlo

            // Guardar el valor que se enviará
            fila.find('.equivalencia').val(equivalencia);



            calcularTotalSolEditar(input);
        }

        function calcularPrecioSugeridoEditar(select) {
            const fila = select.closest('tr');
            const inputPrecio = fila.querySelector('.precio-unitario-editar');
            const unidadSelect = fila.querySelector('.unidad-select-editar');
            const tipoSelect = fila.querySelector('.tipoPrecio-select-editar');
            const inputtotal = fila.querySelector('.precio-total-editar');

            const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
            const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

            const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
            const precioBase = Number(tipoOption?.dataset.precio || 0);

            const sugerido = equivalencia > 0 ? (precioBase / equivalencia) : precioBase;
            inputPrecio.value = sugerido.toFixed(2);

            const cantidad = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
            inputtotal.value = (sugerido * cantidad).toFixed(2);

            calcularTotalSolEditar(null);
        }

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('precio-unitario-editar')) {
                e.target.dataset.editado = "1";
            }
        });
        document.addEventListener('DOMContentLoaded', () => {
            cargarVendedores3();
            cargarClientes();
        });

        function verificarMetodoPago(metodo) {
            const contenedor = document.getElementById('contenedorReferencia');
            const input = document.getElementById('inputReferencia');
            const $seccionSaldo = $('#seccionSaldo');

            if (!contenedor || !input) return;

            if (metodo === 'saldoAfavor') {
                contenedor.style.display = 'none';
                input.required = false;
                input.value = '';

                $seccionSaldo.removeClass('d-none');
            }
            else if (metodo === 'Tarjeta' || metodo === 'Transferencia') {
                contenedor.style.display = 'block';
                input.required = true;
                $('#saldoAFavorInput').val(0);
                document.getElementById('costoTotalCompraEditar').textContent = totalCalculo.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });

                document.getElementById('totalCotizacionEditar').value = totalCalculo;

                $seccionSaldo.addClass('d-none');
            }
            else {
                // Para 'Efectivo' u otros métodos
                contenedor.style.display = 'none';
                input.required = false;
                input.value = '';
                $('#saldoAFavorInput').val(0);
                $seccionSaldo.addClass('d-none');
                document.getElementById('costoTotalCompraEditar').textContent = totalCalculo.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });

                document.getElementById('totalCotizacionEditar').value = totalCalculo;


            }
        }
        // =====================================================
        // GUARDAR ACTUALIZACIÓN (SUBMIT FORM)
        // =====================================================
        // =====================================================
        $('#formEditarSolicitud').on('submit', async function (e) {
            e.preventDefault();

            if (!$('#tablaDetalleEditar tbody tr').length) {
                Swal.fire('Error', 'Agregue al menos un producto a la venta', 'warning');
                return;
            }

            const $btnFinalizar = $(this).find('button[type="submit"]');
            $btnFinalizar.prop('disabled', true);

            const totalVenta = parseFloat($('#totalCotizacionEditar').val()) || 0;
            const montoPagado = $('#monto_pagar').val(); // Dinero real recibido

            const payload = {
                accion: 'guardar_venta',
                id_cliente: $('#cliente_id_editar').val(),
                id_vendedor: $('#select-vendedor1').val(),
                monto_pagado: montoPagado,
                monto_usado_favor: $('#saldoAFavorInput').val(),
                subtotal: totalVenta,
                almacen_id: $('#almacen_id_editar').val(),
                metodo_pago: $('#metodo_pago').val(),
                referencia: $('#inputReferencia').val(),
                observaciones: $('#obsVenta').val() ?? '   ',
                usar_saldo_favor: $('#saldoAFavorInput').val() > 0 ? 1 : 0,
                carrito: []
            };

            $('#tablaDetalleEditar tbody tr').each(function () {
                const fila = $(this);
                const id = fila.attr('id').replace('filaEditar-', '');

                const unidadSelect = fila.find('.unidad-select-editar option:selected');
                const tipoPrecioSelect = fila.find('.tipoPrecio-select-editar option:selected');
                let cantidadInicial = parseFloat(fila.find('.cantidad-editar').val()) || 0;
                let equivalencia = parseFloat(fila.find('.equivalencia').val()) || 1;

                let cantidadT = (cantidadInicial * equivalencia);
                let cantidadTotal = (cantidadT % 1 > 0) ? cantidadT.toFixed(2) : cantidadT;

                payload.carrito.push({
                    producto_id: id,
                    almacen_id: $('#almacen_id_editar').val(),
                    cantidad: cantidadTotal,
                    unidad_base: unidadSelect.val(),

                    noEliminar: fila.find('.noEliminar').val(),
                    idunidadMedida: unidadSelect.data('medida-id'),
                    unidadEquivalencia: unidadSelect.data('equivalencia'),
                    tipo_precio: tipoPrecioSelect.val(),
                    precio_unitario: fila.find('.precio-unitario-editar').val(),
                    subtotal: fila.find('.precio-total-editar').val()
                });
            });
            console.log(payload);

            Swal.fire({
                title: 'Actualizando venta...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const resp = await fetch(`/myvet/app/controllers/ventasController.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const res = await resp.json();

                if (res.status === 'success') {

                    const tieneDeuda = payload.monto_pagado < payload.total_venta;
                    const esEntregaTotal = (res.total_entregado ?? 0) >= (res.total_pedido ?? 0);
                    const iconoFinal = esEntregaTotal ? 'success' : 'warning';

                    // 🎨 Ajustado para modo oscuro: 'bg-body-secondary text-body border-secondary-subtle'
                    let htmlExtra =
                        `<p class="mb-2">Folio: <span class="badge bg-body-secondary text-body border border-secondary-subtle">${res.folio || 'N/A'}</span></p>`;

                    if (tieneDeuda) {
                        // 🎨 Ajustado con utilidades de Bootstrap 5 para soportar temas claros y oscuros
                        htmlExtra += `
        <div class="alert bg-danger-subtle text-danger-emphasis py-1 px-2  mb-2 rounded-3" style="font-size:0.75rem;">
            <i class="bi bi-exclamation-circle-fill me-1"></i> Saldo pendiente registrado en cuenta
        </div>`;
                    }

                    Swal.fire({
                        title: esEntregaTotal ? '¡Venta Exitosa!' : 'Entrega Parcial Registrada',
                        html: `
            <!-- 🎨 Se remueve style="background:#f2f2f7" en favor de 'bg-body-secondary' -->
            <div class="alert bg-body-secondary text-body  small text-start py-2 mb-3 rounded-3">
                ${res.message || 'Operación realizada correctamente.'}
            </div>
            ${htmlExtra}
            <p class="text-body-secondary small mb-0">¿Deseas imprimir el comprobante?</p>
        `,
                        icon: iconoFinal,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-receipt"></i> Con Precios',
                        denyButtonText: '<i class="bi bi-receipt"></i> Ticket Formal',
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#34c759',
                        denyButtonColor: '#5856d6',
                        customClass: {
                            popup: 'rounded-4  shadow-lg bg-body text-body'
                        }
                    }).then((result) => {
                        let url = '';
                        if (result.isConfirmed) {
                            url = `/myvet/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                        } else if (result.isDenied) {
                            url = `/myvet/app/backend/ventas/ticketFormal.php?id=${res.id_venta}`;
                        }

                        if (url !== '') window.open(url, '_blank');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res.message || 'Error desconocido',
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-4 bg-body text-body'
                        }
                    });
                    $btnFinalizar.prop('disabled', false);
                }

            } catch (e) {
                console.error(e);
                Swal.fire({
                    title: 'Error',
                    text: 'Fallo de conexión con el servidor',
                    icon: 'error',
                    customClass: {
                        popup: 'rounded-4 bg-body text-body'
                    }
                });
                $btnFinalizar.prop('disabled', false);
            }
        });
        // ELIMINAR FILA
        // =====================================================
        function quitarFilaEditar(id) {
            $(`#filaEditar-${id}`).remove();

            if (!$('#tablaDetalleEditar tbody tr').length) {
                $('#emptyStateEditar').removeClass('d-none');
            }

            calcularTotalSolEditar(null);
        }
        function vaciarTablaEditar() {
            // 1. Remueve todas las filas del tbody
            $('#tablaDetalleEditar tbody').empty();

            // 2. Muestra el estado vacío (empty state)
            $('#emptyStateEditar').removeClass('d-none');

            // 3. Recalcula el total enviando null
            calcularTotalSolEditar(null);
        }

        // =====================================================
        // GESTIÓN DE SALDOS Y EGRESOS
        // =====================================================

        async function guardarComoGastoSalidadeDinero() {
            try {
                const respFolio = await fetch(
                    '/myvet/app/controllers/egresosController.php?action=getSiguienteFolioGasto');
                const folioRes = await respFolio.json();

                if (!folioRes.success) {
                    throw new Error("No fue posible obtener el folio.");
                }

                const folio = folioRes.folio;
                const id = $('#editar_venta_id').val();
                const total = parseFloat($('#totalCotizacionEditar').val()) || 0;
                const diferencia = total_inicial - total;
                const observaciones = `SALIDA DE DINERO POR EDICIÓN DE VENTA ${id}`;
                const fecha = new Date().toISOString().split('T')[0];

                const fd = new FormData();
                fd.append('folio', folio);
                fd.append('monto', diferencia);
                fd.append('fecha', fecha);
                fd.append('observaciones', observaciones);
                fd.append('venta_id', id);

                const resp = await fetch('/myvet/app/controllers/egresosController.php?action=guardarSalidaDinero', {
                    method: 'POST',
                    body: fd
                });

                const res = await resp.json();

                if (res.success || res.status === 'success') {
                    await Swal.fire('Éxito', 'Salida de dinero registrada correctamente.', 'success');
                    window.location.reload();
                } else {
                    throw new Error(res.message || 'Error al procesar el egreso');
                }

            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
        $('#cliente_id_editar').on('select2:select', function (e) {
            consultarEstatusFinanciero();
        });


        function consultarEstatusFinanciero() {

            let id = $('#cliente_id_editar').val();
            console.log(id);
            fetch(`/myvet/app/controllers/ventasController.php?action=obtenerEstatusCliente&id=${id}`, {

            })
                .then(r => r.json())
                .then(data => {


                    const res = data;
                    const saldo = parseFloat(res.saldo_neto || 0);
                    const condicion = res.estatus_financiero || 'AL DIA';
                    console.log(res.saldo_a_favor);

                    // --- 1. LÓGICA DEL SWITCH DE SALDO A FAVOR ---
                    $('#saldoFavorTotal').val(res.saldo_a_favor);
                    const saldoAFavor = 10;
                    if (saldoAFavor > 0) {
                        $('#saldoAFavorSelect').removeClass('d-none');
                        $('#saldoAFavorInput').removeClass('d-none');
                    } else {
                        $('#saldoAFavorSelect').addClass('d-none');
                        $('#saldoAFavorInput').addClass('d-none');
                    }



                })
                .catch(err => {

                    if (err.name !== 'AbortError') {
                        console.error("Error:", err);

                    }
                });
        }

        $('#saldoAFavorInput').on('input change', function () {
            // 1. Obtener valores actualizados
            let saldoIngresado = parseFloat($(this).val()) || 0;
            let saldoDisponibleMax = parseFloat($('#saldoFavorTotal').val()) || 0;

            // IMPORTANTE: Guarda el precio original de la venta en un atributo data-precio-base
            // Si no existe, tomamos el valor actual y lo guardamos
            if (!$('#totalCotizacionEditar').data('precio-base')) {
                $('#totalCotizacionEditar').data('precio-base', parseFloat($('#totalCotizacionEditar').val()) || 0);
            }

            // Obtenemos SIEMPRE el precio base original
            let ventaBase = parseFloat($('#totalCotizacionEditar').data('precio-base')) || 0;

            // 2. Controlar que no ingrese un número negativo
            if (saldoIngresado < 0) {
                saldoIngresado = 0;
                $(this).val(0);
            }

            // 3. Validar si el saldo ingresado supera el disponible
            if (saldoIngresado > saldoDisponibleMax) {
                saldoIngresado = saldoDisponibleMax;
                $(this).val(saldoDisponibleMax); // Forzar al input a no exceder el máximo disponible
            }

            // 4. Calcular nuevo total restando el saldo ingresado al precio base original
            let nuevoTotal = ventaBase - saldoIngresado;

            // Si el total queda negativo (por ejemplo, el saldo a favor es mayor que la venta)
            if (nuevoTotal < 0) {
                nuevoTotal = 0;
            }

            // 5. Actualizar los elementos en pantalla
            $('#totalCotizacionEditar').val(nuevoTotal.toFixed(2));
            $('#costoTotalCompraEditar').text(nuevoTotal.toFixed(2));
        });


    </script>

</body>

</html>