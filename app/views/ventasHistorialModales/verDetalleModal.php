<style>
    /* ═══ Z-INDEX ═══ */
    #modalDetalle {
        z-index: 10050 !important;
    }

    /* El contenedor raíz de SweetAlert2 */
    .swal2-container {
        z-index: 99999 !important;
    }

    /* El backdrop de SweetAlert */
    .swal2-container.swal2-backdrop-show {
        z-index: 99999 !important;
    }

    /* El popup en sí */
    .swal2-popup {
        z-index: 100000 !important;
        position: relative;
    }

    /* El toast (notificaciones sin backdrop) */
    .swal2-toast-shown .swal2-container {
        z-index: 99999 !important;
    }
</style>
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <!-- Modal expandido a 85vw para dar espacio a la nueva distribución -->
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 85vw;">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

            <!-- HEADER DEL MODAL -->
            <div class="modal-header border-bottom px-4 py-3 ">
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

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
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
                            <span id="detCliente" class="fw-bold text-body small d-block text-truncate">--</span>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 h-100 bg-body-tertiary border border-body-subtle">
                            <small class="d-block text-body-secondary text-uppercase fw-semibold mb-1"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén
                            </small>
                            <span id="detAlmacen" class="fw-bold text-body small d-block text-truncate">--</span>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 h-100 bg-body-tertiary border border-body-subtle">
                            <small class="d-block text-body-secondary text-uppercase fw-semibold mb-1"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                            </small>
                            <span id="detVendedor" class="fw-bold text-body small d-block text-truncate">--</span>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="p-3 rounded-3 h-100 bg-body-tertiary border border-body-subtle">
                            <small class="d-block text-body-secondary text-uppercase fw-semibold mb-1"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <i class="bi bi-receipt me-1 text-primary"></i> Folio / Factura
                            </small>
                            <span id="folioFactura" class="fw-bold text-body small d-block text-truncate">--</span>
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
                            <div class="p-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
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
<script>
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


</script>