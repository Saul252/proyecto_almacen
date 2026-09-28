<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja | Cf System</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
    :root {
        --apple-bg: #f5f5f7;
        --apple-blue: #007aff;
    }

    body {
  
        font-family: -apple-system, sans-serif;
    }

    .main-content {
        margin-left: 0px;
        padding: 80px 20px;
        transition: 0.3s;
    }

    .glass-card {
        background:var(--bg-gradient)!important;
        backdrop-filter: blur(15px);
        border-radius: 20px;
        
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .ios-input {
        
        background:var(--bg-gradient)!important;
        border-radius: 10px;
        padding: 10px;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
       background:var(--bg-gradient)!important;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        z-index: 10;
    }

    .table thead th {
       background:var(--bg-gradient)!important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #8e8e93;
        font-size: 10px;
        
    }

    .badge-metodo {
        font-size: 10px;
        padding: 5px 10px;
        border-radius: 8px;
        font-weight: 600;
    }

    .origen-tag {
        font-size: 9px;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        margin-top: 4px;
        display: inline-block;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
        }
    }

    .glass-card {
       background:var(--bg-gradient)!important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
        border: 1px solid #eef0f2;
    }

    .glass-card:hover {
        transform: translateY(-5px);
    }

    .icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
    }

    .text-xs {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .bg-soft-success {
     background:var(--bg-gradient)!important;
        color: #2e7d32;
    }

    .bg-soft-primary {
      background:var(--bg-gradient)!important;
        color: #1565c0;
    }

    .bg-soft-warning {
       background:var(--bg-gradient)!important;
        color: #ef6c00;
    }

    .bg-soft-danger {
      background:var(--bg-gradient)!important;
        color: #c62828;
    }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h1 class="fw-bold m-0" style="letter-spacing: -1px;">Monitor de Caja</h1>
                    <p class="text-secondary m-0">Detalle de ingresos y movimientos operativos.</p>
                </div>
            </div>
            <script>
            console.log("pagos", <?= json_encode($pagos) ?>);
            </script>

            <div class="glass-card p-4 mb-4">
                <form id="formFiltros" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold  text-uppercase text-xs">Periodo</label>
                        <select id="periodo" class="form-select ">
                            <option value="hoy" selected>Hoy</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Última Semana</option>
                            <option value="mes">Este Mes</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>

                    <div id="div-fechas" class="col-md-4 d-none">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small fw-bold ">INICIO</label>
                                <input type="date" id="f_inicio" class="form-control "
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold ">FIN</label>
                                <input type="date" id="f_fin" class="form-control "
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="small fw-bold text-uppercase">Almacén / Sucursal</label>
                        <select id="almacen_id" class="form-select "
                            <?= ($almacen_sesion != 0) ? 'disabled' : '' ?>>
                            <?php if ($almacen_sesion == 0): ?>
                            <option value="0">🌐 Todas las Sucursales</option>
                            <?php endif; ?>

                            <?php if(isset($listaAlmacenes)) foreach($listaAlmacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>" <?= ($almacen_sesion == $alm['id']) ? 'selected' : '' ?>>
                                📍 <?= $alm['nombre'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-uppercase text-xs">Método de Pago</label>
                        <select id="metodo_pago_filtro" class="form-select ">
                            <option value="todos">💳 Todos</option>
                            <option value="EFECTIVO">💵 Efectivo</option>
                            <option value="TARJETA">💳 Tarjeta</option>
                            <option value="TRANSFERENCIA">🏦 Transferencia</option>
                            <option value="Saldo a Favor"> Saldo a Favor</option>
                            <option value="Null">Deuda</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="AppCaja.update()"
                            class="btn btn-primary w-100 fw-bold rounded-3 py-2" style="background: var(--apple-blue);">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>


            <div class="row mb-4 g-3">
                <!-- CONTENEDOR DEL SALDO INICIAL: empieza visible, JS lo controla -->
                <div id="contenedor-saldo-inicial" class="mb-4 animate__animated animate__fadeIn">
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="card  shadow-sm bg-dark text-white p-4" style="border-radius: 15px;">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center border-end border-secondary">
                                    <small class="text-uppercase opacity-75 fw-bold">Venta Bruta Total</small>
                                    <h2 class="display-6 fw-bold mb-0" id="res-venta-bruta">$0.00</h2>
                                </div>
                                <div class="col-md-4 text-center border-end border-secondary">
                                    <small class="text-uppercase opacity-75 fw-bold text-danger">Pendiente de
                                        Cobro</small>
                                    <h2 class="display-6 fw-bold mb-0 text-danger" id="res-deuda">$0.00</h2>
                                </div>
                                <div class="col-md-4 text-center">
                                    <small class="text-uppercase opacity-75 fw-bold text-warning">Saldo Favor
                                        Usado</small>
                                    <h2 class="display-6 fw-bold mb-0 text-warning" id="res-saldo-favor">$0.00</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card  shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4"><i
                                        class="fas fa-money-bill-wave me-2"></i>FLUJO DE DINERO (HOY)</h6>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-secondary">Efectivo en Caja</span>
                                    <h4 class="fw-bold mb-0 text-success" id="res-total-efectivo">$0.00</h4>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-secondary">Terminal Tarjeta</span>
                                    <h4 class="fw-bold mb-0 text-primary" id="res-total-tarjeta">$0.00</h4>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary">Transferencias Bancarias</span>
                                    <h4 class="fw-bold mb-0 text-info" id="res-total-trans">$0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card  shadow-sm h-100 " style="border-radius: 15px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 text-body-secondary"><i class="fas fa-vault me-2"></i>CAJA FINAL
                                    (+ INICIAL)</h6>
                                <div
                                    class="p-3  rounded-3 shadow-sm mb-2 d-flex justify-content-between align-items-center">
                                    <small class="fw-bold text-body-secondary">EFECTIVO TOTAL</small>
                                    <span class="h5 fw-bold mb-0" id="res-total-efectivoMasSaldo">$0.00</span>
                                </div>
                                <div
                                    class="p-3  rounded-3 shadow-sm mb-2 d-flex justify-content-between align-items-center">
                                    <small class="fw-bold text-body-secondary">TARJETA TOTAL</small>
                                    <span class="h5 fw-bold mb-0" id="res-total-tarjetaMasSaldo">$0.00</span>
                                </div>
                                <div
                                    class="p-3  rounded-3 shadow-sm d-flex justify-content-between align-items-center">
                                    <small class="fw-bold text-body-secondary">TRANSF. TOTAL</small>
                                    <span class="h5 fw-bold mb-0" id="res-total-transMasSaldo">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="card  shadow-sm" style="border-radius: 15px;">
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0 text-center">
                                    <thead class="">
                                        <tr>
                                            <th class="py-3  text-body-secondary small">CONCEPTO</th>
                                            <th class="py-3  text-body-secondary small">EFECTIVO</th>
                                            <th class="py-3  text-body-secondary small">TARJETA</th>
                                            <th class="py-3  text-body-secondary small">TRANSFERENCIA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="py-3 text-start ps-4 fw-bold text-secondary">Ventas de Hoy</td>
                                            <td class="py-3" id="res-v-efectivo">$0.00</td>
                                            <td class="py-3" id="res-v-tarjeta">$0.00</td>
                                            <td class="py-3" id="res-v-trans">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-start ps-4 fw-bold text-secondary">Abonos Recibidos
                                            </td>
                                            <td class="py-3" id="res-a-efectivo">$0.00</td>
                                            <td class="py-3" id="res-a-tarjeta">$0.00</td>
                                            <td class="py-3" id="res-a-trans">$0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="contenedor-egresos-gastos" style="display:none;"></div>

                <div id="contenedor-egresosCompras" style="display:none;"></div>


            </div>
            <div id="corte"></div>

            <div class="glass-card position-relative overflow-hidden mt-4">
                <div id="tabla-loader" class="loading-overlay">
                    <div class="spinner-border text-primary"></div>
                </div>

                <div class="table-responsive">
                    <table id="tablaDetalles" class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">FOLIO / ORIGEN</th>
                                <th>CLIENTE / PERSONAL</th>
                                <th>DETALLE VENTA</th>
                                <th>MÉTODO / RECIBIÓ</th>
                                <th class="text-end">INGRESO REAL</th>
                                <th class="text-end">SALDO FAVOR</th>
                                <th class="text-end pe-4">DEUDA VIVA</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (function_exists('cargarScripts')) { cargarScripts(); } ?>

    <script>
    /**
     * Determina si el periodo seleccionado requiere mostrar el saldo inicial.
     * Solo se muestra para "hoy" y "ayer".
     */
    function periodoRequiereSaldo(periodo) {
        return periodo === 'hoy' || periodo === 'ayer';

    }
    const AppCaja = {
        config: {
            url: '/myvet/app/controllers/corteCajaController.php'
        },

        init: function() {
            this.bindEvents();
            this.update();
            if ($("#periodo").val() === "hoy" || $("#periodo").val() === "ayer") {
                $("#corte").html(`
        <button type="button" 
                class="btn btn-dark shadow-sm px-4 py-2 d-flex align-items-center" 
                style="border-radius: 10px;"
                data-bs-toggle="modal" 
                data-bs-target="#modalCorteCaja">
            <i class="fas fa-cash-register me-2"></i>
            <span class="fw-bold">FINALIZAR CORTE DE CAJA</span>
        </button>
    `);
            } else {
                $("#corte").html("");
            }
        },


        bindEvents: function() {
            const self = this;

            $('#periodo').on('change', function() {
                const periodo = $(this).val();
                if ($("#periodo").val() === "hoy" || $("#periodo").val() === "ayer") {
                    $("#corte").html(`
        <button type="button" 
                class="btn btn-dark shadow-sm px-4 py-2 d-flex align-items-center" 
                style="border-radius: 10px;"
                data-bs-toggle="modal" 
                data-bs-target="#modalCorteCaja">
            <i class="fas fa-cash-register me-2"></i>
            <span class="fw-bold">FINALIZAR CORTE DE CAJA</span>
        </button>
    `);
                } else {
                    $("#corte").html("");
                }
                if (!periodoRequiereSaldo(periodo)) {
                    $('#contenedor-saldo-inicial').empty().hide();
                }

                if (periodo === 'personalizado') {
                    $('#div-fechas').removeClass('d-none').addClass(
                    'animate__animated animate__fadeIn');
                } else {
                    $('#div-fechas').addClass('d-none');
                    self.update();
                }
            });

            $('#almacen_id').on('change', function() {
                self.update();
            });

            // 🔥 filtro por método de pago
            $('#metodo_pago_filtro').on('change', function() {
                self.renderTabla(self.lastData || []);
            });
        },

        update: function() {
            $('#tabla-loader').css('display', 'flex');

            const params = {
                ajax: 1,
                periodo: $('#periodo').val(),
                f_inicio: $('#f_inicio').val(),
                f_fin: $('#f_fin').val(),
                almacen_id: $('#almacen_id').val()
            };

            $.getJSON(this.config.url, params, (res) => {

                if (res.status === 'success' || res.totales) {

                    const mostrar = periodoRequiereSaldo(params.periodo);
                    console.log("compras");
                    console.log(res.comprasMetodo)

                    // 1. Saldos e ingresos
                    this.renderSaldoInicial(res.saldo_inicial, res.es_lista, mostrar);
                    this.renderTotales(res.totales, res.saldo_inicial, res.es_lista, mostrar);

                    // 2. Totales egresos (cards generales)
                    if (this.renderEgresosTotales) {
                        this.renderEgresosTotales(
                            res.gastosTotales || 0,
                            res.comprasTotales || 0
                        );
                    }

                    // 3. Tabla principal
                    this.lastData = res.detalles;
                    this.renderTabla(res.detalles);

                    // 4. 🔥 DESGLOSE GASTOS POR MÉTODO
                    this.renderGastosPorMetodo(res.gastosMetodo || {
                        EFECTIVO: 0,
                        TARJETA: 0,
                        TRANSFERENCIA: 0
                    });
                    this.renderComprasPorMetodo(res.comprasMetodo || {
                        EFECTIVO: 0,
                        TARJETA: 0,
                        TRANSFERENCIA: 0
                    });

                    // 5. 🔥 DESGLOSE COMPRAS (si también lo tienes por método o lista)

                    // 🔥 GUARDAR GASTOS Y COMPRAS GLOBALMENTE
                    window._gastosMetodo = res.gastosMetodo || {
                        EFECTIVO: 0,
                        TARJETA: 0,
                        TRANSFERENCIA: 0
                    };
                    window._comprasMetodo = res.comprasMetodo || {
                        EFECTIVO: 0,
                        TARJETA: 0,
                        TRANSFERENCIA: 0
                    };



                    console.log("🔥 DATOS CARGADOS:", window._gastosMetodo, window._comprasTotales);
                    // ... dentro de $.getJSON(this.config.url, params, (res) => { ...


                    // Agrega esto para capturar el desglose del saldo inicial:
                    window._saldoInicialDesglose = {
                        efectivo: parseFloat(res.saldo_inicial?.monto_efectivo || 0),
                        tarjeta: parseFloat(res.saldo_inicial?.monto_tarjeta || 0),
                        transferencia: parseFloat(res.saldo_inicial?.monto_transferencia || 0),
                        total: parseFloat(res.saldo_inicial?.monto || 0)
                    };

                    console.log("🔥 DATOS CARGADOS:", window._gastosMetodo, window
                        ._saldoInicialDesglose);

                }
            }).fail((error) => {
                console.error("Error al actualizar la caja:", error);
            }).always(() => {
                $('#tabla-loader').hide();
            });
        },
        renderGastosPorMetodo: function(data) {

            const efec = parseFloat(data.EFECTIVO || 0);
            const tar = parseFloat(data.TARJETA || 0);
            const tra = parseFloat(data.TRANSFERENCIA || 0);

            const total = efec + tar + tra;

            $('#contenedor-egresos-gastos').html(`
        <div class="card shadow-sm  mb-3 animate__animated animate__fadeIn" style="border-radius:15px;">
            <div class="card-header bg-danger text-white">
                <strong>Gastos por Método</strong>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Efectivo</span>
                    <strong>${this.formatMoney(efec)}</strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Tarjeta</span>
                    <strong>${this.formatMoney(tar)}</strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Transferencia</span>
                    <strong>${this.formatMoney(tra)}</strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span>${this.formatMoney(total)}</span>
                </div>
            </div>
        </div>
    `).show();
        },
        renderComprasPorMetodo: function(data) {

            const efec = parseFloat(data.EFECTIVO || 0);
            const tar = parseFloat(data.TARJETA || 0);
            const tra = parseFloat(data.TRANSFERENCIA || 0);

            const total = efec + tar + tra;

            $('#contenedor-egresosCompras').html(`
        <div class="card shadow-sm  mb-3 animate__animated animate__fadeIn" style="border-radius:15px;">
            <div class="card-header bg-warning text-dark">
                <strong>Compras por Método</strong>
            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <span>Efectivo</span>
                    <strong>${this.formatMoney(efec)}</strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Tarjeta</span>
                    <strong>${this.formatMoney(tar)}</strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Transferencia</span>
                    <strong>${this.formatMoney(tra)}</strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span>${this.formatMoney(total)}</span>
                </div>

            </div>
        </div>
    `).show();
        },
        renderSaldoInicial: function(data, esLista, mostrar) {
            const $contenedor = $('#contenedor-saldo-inicial');
            if (!$contenedor.length) return;

            if (!mostrar) {
                $contenedor.empty().hide();
                return;
            }

            if (esLista) {
                let filas = (data && data.length > 0) ?
                    data.map(s => `
                    <tr>
                        <td class="ps-4 fw-bold text-secondary small text-uppercase">${s.almacen}</td>
                        <td class="text-end fw-semibold  small">${this.formatMoney(s?.monto_efectivo)}</td>
                        <td class="text-end fw-semibold  small">${this.formatMoney(s?.monto_tarjeta)}</td>
                        <td class="text-end fw-semibold  small">${this.formatMoney(s?.monto_transferencia)}</td>
                        <td class="text-end pe-4 fw-bold text-primary">${this.formatMoney(s?.monto)}</td>
                    </tr>
                `).join('') :
                    '<tr><td colspan="5" class="text-center py-3 text-body-secondary small">Sin registros</td></tr>';

                $contenedor.html(`
                <div class="glass-card overflow-hidden animate__animated animate__fadeIn">
                    <div class="p-3 border-bottom bg-opacity-50">
                        <h6 class="m-0 fw-bold  text-xs text-uppercase">
                            <i class="bi bi-houses-fill me-2 text-primary"></i> Apertura Global por Sucursal
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="">
                                <tr class="text-body-secondary" style="font-size: 10px;">
                                    <th class="ps-4">ALMACÉN</th>
                                    <th class="text-end">EFECTIVO</th>
                                    <th class="text-end">TARJETA</th>
                                    <th class="text-end">TRANSF.</th>
                                    <th class="text-end pe-4">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                </div>
            `).show();

            } else {
                const d = data || {};
                const total = this.formatMoney(d.monto || 0);
                const efec = this.formatMoney(d.monto_efectivo || 0);
                const tarj = this.formatMoney(d.monto_tarjeta || 0);
                const tran = this.formatMoney(d.monto_transferencia || 0);

                $contenedor.html(`
                <div class="card  shadow-sm text-white animate__animated animate__fadeInRight" 
                     style="background: linear-gradient(90deg, #007aff, #00c6ff); border-radius:15px;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-white-50 fw-bold d-block text-xs text-uppercase">Saldo Inicial (Total)</small>
                                <h2 class="fw-bold mb-0">${total}</h2>
                            </div>
                            <i class="bi bi-safe2 fs-4 bg-white bg-opacity-20 p-2 rounded-circle"></i>
                        </div>
                        <div class="row g-0 pt-2 border-top border-white border-opacity-10 text-center">
                            <div class="col-4 border-end border-white border-opacity-10">
                                <small class="d-block text-white-50 text-xxs">EFECTIVO</small>
                                <span class="fw-bold small">${efec}</span>
                            </div>
                            <div class="col-4 border-end border-white border-opacity-10">
                                <small class="d-block text-white-50 text-xxs">TARJETA</small>
                                <span class="fw-bold small">${tarj}</span>
                            </div>
                            <div class="col-4">
                                <small class="d-block text-white-50 text-xxs">TRANSF.</small>
                                <span class="fw-bold small">${tran}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).show();
            }
        },

        renderTotales: function(s, saldoIni, esLista, mostrar) {
            if (!s) return;
            const dIni = saldoIni || {};

            const iniEfec = (mostrar && !esLista) ? parseFloat(dIni.monto_efectivo || 0) : 0;
            const iniTar = (mostrar && !esLista) ? parseFloat(dIni.monto_tarjeta || 0) : 0;
            const iniTra = (mostrar && !esLista) ? parseFloat(dIni.monto_transferencia || 0) : 0;

            $('#res-venta-bruta').text(this.formatMoney(s.venta_bruta || 0));
            $('#res-deuda').text(this.formatMoney(s.deuda_pendiente || 0));
            $('#res-saldo-favor').text(this.formatMoney(s.saldo_favor_usado || 0));

            $('#res-total-efectivo').text(this.formatMoney(parseFloat(s.ingreso_total_efectivo || 0)));
            $('#res-total-tarjeta').text(this.formatMoney(parseFloat(s.ingreso_total_tarjeta || 0)));
            $('#res-total-trans').text(this.formatMoney(parseFloat(s.ingreso_total_transfer || 0)));

            $('#res-total-efectivoMasSaldo').text(this.formatMoney(iniEfec + parseFloat(s
                .ingreso_total_efectivo || 0)));
            $('#res-total-tarjetaMasSaldo').text(this.formatMoney(iniTar + parseFloat(s.ingreso_total_tarjeta ||
                0)));
            $('#res-total-transMasSaldo').text(this.formatMoney(iniTra + parseFloat(s.ingreso_total_transfer ||
                0)));

            $('#res-v-efectivo').text(this.formatMoney(parseFloat(s.solo_venta_efectivo || 0)));
            $('#res-v-tarjeta').text(this.formatMoney(parseFloat(s.solo_venta_tarjeta || 0)));
            $('#res-v-trans').text(this.formatMoney(parseFloat(s.solo_venta_transfer || 0)));

            $('#res-a-efectivo').text(this.formatMoney(parseFloat(s.abono_efectivo || 0)));
            $('#res-a-tarjeta').text(this.formatMoney(parseFloat(s.abono_tarjeta || 0)));
            $('#res-a-trans').text(this.formatMoney(parseFloat(s.abono_transferencia || 0)));
        },

        renderTabla: function(data) {
            let html = '';
            const metodoFiltro = $('#metodo_pago_filtro').val();

            if (data && data.length > 0) {

                const dataFiltrada = data.filter(v => {

                    const metodo = (v.metodo_pago || '').toUpperCase();
                    const deuda = parseFloat(v.deuda_viva || 0);
                    const saldo = parseFloat(v.uso_saldo_favor || 0);

                    if (metodoFiltro === 'todos') return true;

                    if (metodoFiltro === 'EFECTIVO') return metodo === 'EFECTIVO';
                    if (metodoFiltro === 'TARJETA') return metodo === 'TARJETA';
                    if (metodoFiltro === 'TRANSFERENCIA') return metodo === 'TRANSFERENCIA';

                    if (metodoFiltro === 'Saldo a Favor') {
                        return metodo.includes('SALDO') || saldo > 0;
                    }

                    if (metodoFiltro === 'Null') {
                        return deuda > 0;
                    }

                    return true;
                });

                if (dataFiltrada.length === 0) {
                    html =
                        '<tr><td colspan="7" class="text-center py-5 text-body-secondary">No hay resultados para este filtro</td></tr>';
                } else {

                    dataFiltrada.forEach(v => {
                        const dReal = parseFloat(v.dinero_real || 0);
                        const sFavor = parseFloat(v.uso_saldo_favor || 0);
                        const dViva = parseFloat(v.deuda_viva || 0);

                        let productosHtml = '';
                        if (v.productos && Array.isArray(v.productos)) {
                            v.productos.forEach(p => {
                                productosHtml += `
                                <div class="d-flex flex-column mb-1">
                                    <span class="small fw-semibold  text-truncate" style="max-width:180px">${p.producto}</span>
                                    <small class="text-body-secondary" style="font-size:9px;">Cant: ${p.cantidad}</small>
                                </div>`;
                            });
                        }

                        html += `
                        <tr class="border-bottom animate__animated animate__fadeInUp">
                            <td class="ps-4">
                                <span class="fw-bold d-block ">${v.folio || 'S/F'}</span>
                                <span class="origen-tag ${v.tipo === 'VENTA DÍA' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning'}">${v.tipo}</span>
                            </td>
                            <td><span class="fw-semibold d-block small">${v.cliente || 'Público General'}</span></td>
                            <td>${productosHtml}</td>
                            <td><span class="badge bg-white text-dark border shadow-sm badge-metodo">${v.metodo_pago}</span></td>
                            <td class="text-end fw-bold text-success">${this.formatMoney(dReal)}</td>
                            <td class="text-end fw-semibold text-warning">${sFavor > 0 ? this.formatMoney(sFavor) : '-'}</td>
                            <td class="text-end pe-4 ${dViva > 0 ? 'text-danger fw-bold' : 'text-body-secondary small'}">
                                ${dViva > 0 ? this.formatMoney(dViva) : '<i class="bi bi-check-circle-fill text-success me-1"></i>PAGADO'}
                            </td>
                        </tr>`;
                    });

                }

            } else {
                html =
                    '<tr><td colspan="7" class="text-center py-5 text-body-secondary">No se encontraron movimientos</td></tr>';
            }

            $('#tablaDetalles tbody').html(html);
        },

        formatMoney: function(amount) {
            return '$' + parseFloat(amount || 0).toLocaleString('es-MX', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    $(document).ready(() => AppCaja.init());
    </script>

    <?php require_once __DIR__ . '/corteCaja/guardarCorteDeCaja.php'; ?>
</body>

</html>