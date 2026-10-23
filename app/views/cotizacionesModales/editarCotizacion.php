<div class="modal fade" id="modalEditarCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="formEditarSolicitud">
                <input type="hidden" id="cotE_cotizacion_id" name="cotizacion_id" value="">

                <!-- Encabezado -->
                <div class="modal-header border-0 pt-4 px-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning card-title-text rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Editar Cotización</h4>
                            <p class="text-body-secondary small mb-0">Modifique los datos de la cotización existente</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <!-- Fila de Controles -->
                    <div class="row g-3 mb-4 p-4 rounded-4 shadow-sm align-items-end border">

                        <!-- 1. Almacén de Cargo -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cotE_almacen_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select name="almacen_id_editar" id="cotE_almacen_id"
                                class="form-select border-slate-200 control-fixed-height w-100 shadow-none" required>
                                <option value="">Seleccionar ubicación...</option>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Cliente -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cotE_cliente_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person me-1 text-primary"></i> Cliente
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select name="cliente_id_editar" id="cotE_cliente_id"
                                    class="form-select select2-cotE border-slate-200" required>
                                    <option value="">Seleccionar cliente...</option>
                                </select>
                                <button
                                    class="btn btn-outline-primary px-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    type="button" onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Vendedor -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cotE_vendedor"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                            </label>
                            <select name="select-vendedor1" id="cotE_vendedor"
                                class="form-select select2-cotE border-slate-200 w-100" required>
                                <option value="">Seleccionar vendedor...</option>
                            </select>
                        </div>

                        <!-- 4. Añadir Producto -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cotE_buscadorProductos"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="cotE_buscadorProductos" class="form-select select2-cotE border-slate-200">
                                    <option value="">Escribe SKU o nombre...</option>
                                </select>
                                <button type="button"
                                    class="btn btn-primary d-flex align-items-center justify-content-center px-3 flex-shrink-0"
                                    onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    <span class="fw-medium">Nuevo</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive border rounded-4">
                        <table class="table align-middle mb-0" id="cotE_tablaDetalle">
                            <thead>
                                <tr class="text-body-secondary small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 25%;">Presentación / Unidad</th>
                                    <th style="width: 25%;">Tipo de precio</th>
                                    <th class="ps-4">Precio Unitario</th>
                                    <th style="width: 50%;">TOTAL</th>
                                    <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <div id="cotE_emptyState" class="text-center py-5 text-body-secondary">
                            <div class="mb-3">
                                <i class="bi bi-cart-plus opacity-25" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-medium">La lista está vacía</p>
                            <small>Utiliza el buscador de arriba para añadir artículos</small>
                        </div>
                    </div>

                    <!-- Resumen del Total -->
                    <div class="d-flex justify-content-end align-items-center mt-4">
                        <div class="bg-dark bg-gradient text-white p-3 rounded-4 shadow-sm text-end px-4 min-w-200">
                            <small class="d-block text-white-50 fw-bold text-uppercase tracking-wider mb-1"
                                style="font-size: 0.65rem; letter-spacing: 0.8px;">
                                Total Cotización
                            </small>
                            <div id="cotE_costoTotal" class="fw-bolder text-warning"
                                style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                            <input type="hidden" id="cotE_totalCotizacion" name="totalCotizacionEditar">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2 align-items-center">
                        <button type="button"
                            class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center"
                            onclick="procederPago($('#cotE_totalCotizacion').val(), $('#cotE_cotizacion_id').val())">
                            <i class="bi bi-cart-check me-2"></i> Convertir a Venta
                        </button>

                        <button type="submit"
                            class="btn btn-warning rounded-pill px-4 py-2.5 fw-bold shadow-sm card-title-text d-flex align-items-center">
                            <i class="bi bi-check2-circle me-2"></i> Actualizar Cotización
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media (min-width: 1200px) {
        .modal-dialog-custom {
            max-width: 92% !important;
        }
    }

    :root {
        --control-height: 42px;
        --border-color: #dee2e6;
        --bg-input: #ffffff;
    }

    .min-w-0 {
        min-width: 0 !important;
    }

    .min-w-200 {
        min-width: 200px;
    }

    .form-label-custom {
        font-size: 0.72rem !important;
        font-weight: 600 !important;
        color: #6c757d !important;
        letter-spacing: 0.5px !important;
    }

    .input-group-fixed {
        width: 100% !important;
        max-width: 100% !important;
    }

    .input-group-fixed .select2-container {
        flex: 1 1 auto !important;
        width: 1% !important;
        min-width: 0 !important;
    }

    .control-fixed-height,
    .modal-body .form-select,
    .modal-body .input-group-fixed .btn {
        height: var(--control-height) !important;
    }

    .modal-body .select2-container--bootstrap-5 .select2-selection,
    .modal-body .select2-container .select2-selection--single {
        height: var(--control-height) !important;
        background-color: var(--bg-input) !important;
        border-color: var(--border-color) !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
        font-size: 0.875rem !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .modal-body .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: calc(var(--control-height) - 2px) !important;
        color: #212529 !important;
        padding-left: 0.75rem !important;
        padding-right: 1.75rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        width: 100% !important;
    }

    .table-fixed {
        table-layout: fixed !important;
        width: 100% !important;
    }
</style>

<script>
    (function () {
        'use strict';

        const COTE_URL = '/myvet/app/controllers/cotizacionesController.php';
        let cotERecalculando = false;
        let cotEClienteId = 0;

        function cotELog(...args) {
            console.log('[COT-EDIT]', ...args);
        }

        // =====================================================
        // SELECT2 (propio, con destroy)
        // =====================================================
        $(document).on('shown.bs.modal', '#modalEditarCotizacion', function () {
            ['#cotE_cliente_id', '#cotE_vendedor', '#cotE_buscadorProductos'].forEach(sel => {
                if ($(sel).hasClass('select2-hidden-accessible')) {
                    $(sel).select2('destroy');
                }
            });

            $('#cotE_cliente_id').select2({ theme: 'bootstrap-5', dropdownParent: $('#modalEditarCotizacion') });
            $('#cotE_vendedor').select2({ theme: 'bootstrap-5', dropdownParent: $('#modalEditarCotizacion') });
            $('#cotE_buscadorProductos').select2({ theme: 'bootstrap-5', dropdownParent: $('#modalEditarCotizacion') });

            cotELog('Select2 inicializado');
        });

        // =====================================================
        // CARGAR CLIENTES
        // =====================================================
        async function cotECargarClientes() {
            const almacenId = $('#cotE_almacen_id').val();
            const select = document.getElementById('cotE_cliente_id');
            if (!select) return;

            select.innerHTML = '<option value="">Cargando clientes...</option>';

            try {
                const resp = await fetch(`/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${almacenId}`);
                const resultado = await resp.json();

                if (!resultado.success || !Array.isArray(resultado.data)) {
                    throw new Error('Respuesta inválida');
                }

                const filtrados = resultado.data.filter(c => {
                    const n = (c.nombre_comercial || '').toLowerCase().trim();
                    const esPublico = n.includes('publico en general') || n.includes('público en general');
                    return esPublico ? c.almacen_id == almacenId : true;
                });

                select.innerHTML = '<option value="">Seleccione un cliente...</option>';
                filtrados.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.nombre_comercial;
                    select.appendChild(opt);
                });

                if (cotEClienteId) {
                    $('#cotE_cliente_id').val(cotEClienteId).trigger('change.select2');
                }

                cotELog('Clientes cargados:', filtrados.length);

            } catch (e) {
                console.error('[COT-EDIT] Error clientes:', e);
                select.innerHTML = '<option value="">Error al cargar clientes</option>';
            }
        }

        // =====================================================
        // CARGAR VENDEDORES
        // =====================================================
        async function cotECargarVendedores(vendedorId = null) {
            const almacenId = $('#cotE_almacen_id').val();
            const select = document.getElementById('cotE_vendedor');
            if (!select) return;

            select.innerHTML = '<option value="">Cargando vendedores...</option>';

            try {
                const resp = await fetch(`/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacenId}`);
                const resultado = await resp.json();

                if (!resultado.success || !Array.isArray(resultado.data)) {
                    throw new Error('Respuesta inválida');
                }

                select.innerHTML = '<option value="">Seleccionar vendedor...</option>';
                resultado.data.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = u.nombre;
                    select.appendChild(opt);
                });

                if (vendedorId) {
                    $('#cotE_vendedor').val(vendedorId).trigger('change.select2');
                }

                cotELog('Vendedores cargados:', resultado.data.length);

            } catch (e) {
                console.error('[COT-EDIT] Error vendedores:', e);
                select.innerHTML = '<option value="">Error al cargar vendedores</option>';
            }
        }

        // =====================================================
        // CARGAR PRODUCTOS
        // =====================================================
        async function cotERecargarProductos() {
            const id = $('#cotE_almacen_id').val();
            const $select = $('#cotE_buscadorProductos');

            if (!id) return;

            try {
                const resp = await fetch(`/myvet/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${id}`);
                const texto = await resp.text();

                let res;
                try {
                    res = JSON.parse(texto);
                } catch (e) {
                    console.error('[COT-EDIT] JSON inválido:', texto.substring(0, 300));
                    throw new Error('El servidor no devolvió JSON válido');
                }

                if (!res.success) throw new Error(res.message);

                $select.empty().append(new Option('Escribe SKU o nombre...', '', true, true));

                (res.data || []).forEach(pr => {
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
                        'data-stock': pr.stock || 1
                    });
                    $select.append(option);
                });

                $select.trigger('change.select2');
                cotELog('Productos cargados:', (res.data || []).length);

            } catch (e) {
                console.error('[COT-EDIT] Error productos:', e);
                Swal.fire('Error', 'No se pudieron cargar los productos', 'error');
            }
        }

        // =====================================================
        // CALCULAR TOTAL
        // =====================================================
        function cotECalcularTotal(input) {
            if (cotERecalculando) return;
            cotERecalculando = true;

            try {
                const fila = input.closest('tr');
                const cantidad = parseFloat(fila.querySelector('.cotE-cantidad').value) || 0;
                const precioUnitario = parseFloat(fila.querySelector('.cotE-precio-unitario').value) || 0;

                fila.querySelector('.cotE-precio-total').value = (cantidad * precioUnitario).toFixed(2);

                let total = 0;
                document.querySelectorAll('#cotE_tablaDetalle .cotE-precio-total').forEach(el => {
                    total += parseFloat(el.value) || 0;
                });

                const cT = document.getElementById('cotE_costoTotal');
                const hidden = document.getElementById('cotE_totalCotizacion');
                if (cT) cT.textContent = total.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                if (hidden) hidden.value = total.toFixed(2);

            } finally {
                cotERecalculando = false;
            }
        }

        // =====================================================
        // CALCULAR PRECIO SUGERIDO (corregido: multiplica por cantidad)
        // =====================================================
        function cotECalcularPrecioSugerido(select) {
            const fila = select.closest('tr');
            const inputPrecio = fila.querySelector('.cotE-precio-unitario');
            const inputTotal = fila.querySelector('.cotE-precio-total');
            const unidadSelect = fila.querySelector('.cotE-unidad-select');
            const tipoSelect = fila.querySelector('.cotE-tipoPrecio-select');

            const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
            const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

            const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
            const precioBase = Number(tipoOption?.dataset.precio || 0);
            const cantidad = parseFloat(fila.querySelector('.cotE-cantidad').value) || 0;

            const sugerido = equivalencia > 0 ? (precioBase / equivalencia) : precioBase;
            inputPrecio.value = sugerido.toFixed(2);
            inputTotal.value = (sugerido * cantidad).toFixed(2);

            cotECalcularTotal(inputPrecio);
        }

        // =====================================================
        // EVENTO: AGREGAR PRODUCTO
        // =====================================================
        $('#cotE_buscadorProductos').on('select2:select', function () {
            const d = this.options[this.selectedIndex].dataset;
            const id = $(this).val();

            if ($(`#cotE_fila-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            $('#cotE_emptyState').addClass('d-none');

            const medidas = JSON.parse(d.medidas || '[]');
            let opcionesUnidad = '<option value="0" data-equivalencia="0" data-medida-id="0">Seleccione</option>';
            medidas.forEach(m => {
                opcionesUnidad += `<option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">${m.nombre}</option>`;
            });

            const preMin = parseFloat(d.premin) || 0;
            const preMat = parseFloat(d.premat) || 0;
            const preDis = parseFloat(d.predis) || 0;
            const factor = parseFloat(d.factor) || 1;

            $('#cotE_tablaDetalle tbody').append(`
        <tr id="cotE_fila-${id}">
            <td class="ps-4">
                <b>${d.nombre}</b><br>
                <small class="text-body-secondary">${d.sku}</small>
            </td>
            <td>
                <input type="number" name="itemsEditar[${id}][cant]" class="form-control cotE-cantidad"
                    step="0.01" value="1" min="0.01" required oninput="cotECalcularTotal(this)">
            </td>
            <td>
                <select name="itemsEditar[${id}][unidad]" class="form-select cotE-unidad-select" onchange="cotECalcularPrecioSugerido(this)">
                    ${opcionesUnidad}
                </select>
            </td>
            <td>
                <select name="itemsEditar[${id}][tipoPrecio]" class="form-select cotE-tipoPrecio-select" onchange="cotECalcularPrecioSugerido(this)">
                    <option value="seleccionar" data-precio="0">Seleccione</option>
                    <option value="minorista" data-precio="${preMin}">Min $${(preMin * factor).toFixed(2)} x ${d.ur}</option>
                    <option value="mayorista" data-precio="${preMat}">May $${(preMat * factor).toFixed(2)} x ${d.ur}</option>
                    <option value="distribuidor" data-precio="${preDis}">Dis $${(preDis * factor).toFixed(2)} x ${d.ur}</option>
                </select>
            </td>
            <td>
                <input type="number" name="itemsEditar[${id}][precioUnitario]" class="form-control cotE-precio-unitario"
                    step="0.01" min="0" placeholder="0.00" required
                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#cotE_buscadorProductos').select2('open'); }"
                    oninput="cotECalcularTotal(this)">
            </td>
            <td style="min-width:160px;">
                <input type="number" name="itemsEditar[${id}][precio]" class="form-control cotE-precio-total fw-bold text-success"
                    step="0.01" min="0" placeholder="0.00"
                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#cotE_buscadorProductos').select2('open'); }"
                    oninput="cotECalcularTotal(this)"
                    style="font-size:1.1rem; height:45px; min-width:140px;">
            </td>
            <td>
                <button type="button" class="btn btn-link text-danger" onclick="cotEQuitarFila(${id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        `);

            $(this).val(null).trigger('change');
        });

        // =====================================================
        // QUITAR FILA
        // =====================================================
        function cotEQuitarFila(id) {
            $(`#cotE_fila-${id}`).remove();

            let total = 0;
            document.querySelectorAll('#cotE_tablaDetalle .cotE-precio-total').forEach(el => {
                total += parseFloat(el.value) || 0;
            });

            const cT = document.getElementById('cotE_costoTotal');
            const hidden = document.getElementById('cotE_totalCotizacion');
            if (cT) cT.textContent = total.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
            if (hidden) hidden.value = total.toFixed(2);

            if (!$('#cotE_tablaDetalle tbody tr').length) {
                $('#cotE_emptyState').removeClass('d-none');
            }
        }

        // =====================================================
        // GUARDAR ACTUALIZACIÓN
        // =====================================================
        $('#formEditarSolicitud').on('submit', async function (e) {
            e.preventDefault();

            if (!$('#cotE_tablaDetalle tbody tr').length) {
                Swal.fire('Error', 'Agregue productos', 'warning');
                return;
            }

            const payload = {
                cotizacion_id: $('#cotE_cotizacion_id').val(),
                almacen_id: $('#cotE_almacen_id').val(),
                cliente_id: $('#cotE_cliente_id').val(),
                vendedor: $('#cotE_vendedor').val(),
                totalCotizacion: $('#cotE_totalCotizacion').val(),
                items: []
            };

            $('#cotE_tablaDetalle tbody tr').each(function () {
                const fila = $(this);
                const id = fila.attr('id').replace('cotE_fila-', '');
                const unidadSelect = fila.find('.cotE-unidad-select option:selected');
                const tipoPrecioSelect = fila.find('.cotE-tipoPrecio-select option:selected');

                payload.items.push({
                    producto_id: id,
                    cantidad: fila.find('.cotE-cantidad').val(),
                    unidad: unidadSelect.val(),
                    unidad_id: unidadSelect.data('medida-id'),
                    equivalencia: unidadSelect.data('equivalencia'),
                    tipoPrecio: tipoPrecioSelect.val(),
                    precioUnitario: fila.find('.cotE-precio-unitario').val(),
                    precio: fila.find('.cotE-precio-total').val()
                });
            });

            cotELog('Payload actualizar:', payload);

            Swal.fire({ title: 'Actualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const resp = await fetch(`${COTE_URL}?action=actualizar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const res = await resp.json();

                if (res.status === 'success') {
                    await Swal.fire({ icon: 'success', title: '¡Actualizado!', text: res.message, timer: 1500, showConfirmButton: false });
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }

            } catch (e) {
                console.error('[COT-EDIT] Error actualizar:', e);
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });

        // =====================================================
        // INICIALIZAR MODAL CON DATOS DE LA DB
        // =====================================================
        async function gestionarSolicitud(id) {
            try {
                $('#cotE_tablaDetalle tbody').empty();
                $('#formEditarSolicitud')[0].reset();
                $('#cotE_costoTotal').text('$0.00');
                $('#cotE_totalCotizacion').val('');
                cotEClienteId = 0;

                cotELog('Cargando cotización ID:', id);

                const resp = await fetch(`${COTE_URL}?action=obtenerDetalle&id=${id}`);
                const datos = await resp.json();
                const data = datos.data;

                if (!Array.isArray(data) || data.length === 0) {
                    Swal.fire('Error', 'No se encontraron productos en esta cotización', 'warning');
                    return;
                }

                const infoBase = data[0];

                $('#cotE_cotizacion_id').val(infoBase.cotizacion_id);
                $('#cotE_almacen_id').val(infoBase.almacen_origen_id);
                cotEClienteId = infoBase.cliente_id;

                // Cargar select de clientes y vendedores
                await cotECargarClientes();
                await cotECargarVendedores(infoBase.vendedor_id);

                // Cargar productos del almacén
                await cotERecargarProductos();

                $('#cotE_emptyState').addClass('d-none');

                data.forEach(i => {
                    const prodId = i.producto_id;

                    let opcionesUnidadHtml = `
                <option value="${i.unidadMedida}" data-equivalencia="${i.equivalencia}" data-medida-id="${i.unidadMedida}" selected>
                    ${i.nombre}
                </option>`;

                    const nuevaFilaHtml = `
            <tr id="cotE_fila-${prodId}">
                <td class="ps-4">
                    <b>${i.producto_nombre}</b><br>
                    <small class="text-body-secondary">${i.sku}</small>
                </td>
                <td>
                    <input type="number" name="itemsEditar[${prodId}][cant]" class="form-control cotE-cantidad"
                        step="0.01" value="${parseFloat(i.cantidad)}" min="0.01" required oninput="cotECalcularTotal(this)">
                </td>
                <td>
                    <select name="itemsEditar[${prodId}][unidad]" class="form-select cotE-unidad-select" onchange="cotECalcularPrecioSugerido(this)">
                        ${opcionesUnidadHtml}
                    </select>
                </td>
                <td>
                    <select name="itemsEditar[${prodId}][tipoPrecio]" class="form-select cotE-tipoPrecio-select"
                        id="cotE_tipoPrecio_${prodId}" onchange="cotECalcularPrecioSugerido(this)">
                        <option value="seleccionar" data-precio="0">Seleccione</option>
                        <option value="minorista" data-precio="${i.precio_unitario}">
                            Min ${(parseFloat(i.precio_unitario) * parseFloat(i.factor_conversion || 1)).toFixed(2)} x ${i.unidad_reporte}
                        </option>
                    </select>
                </td>
                <td>
                    <input type="number" name="itemsEditar[${prodId}][precioUnitario]" class="form-control cotE-precio-unitario"
                        step="0.01" min="0" value="${parseFloat(i.precio_unitario).toFixed(2)}" required
                        oninput="cotECalcularTotal(this)">
                </td>
                <td style="min-width:160px;">
                    <input type="number" name="itemsEditar[${prodId}][precio]" class="form-control cotE-precio-total fw-bold text-success"
                        step="0.01" min="0" value="${parseFloat(i.subtotal).toFixed(2)}" readonly
                        style="font-size:1.1rem; height:45px; min-width:140px;">
                </td>
                <td>
                    <button type="button" class="btn btn-link text-danger" onclick="cotEQuitarFila(${prodId})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;

                    $('#cotE_tablaDetalle tbody').append(nuevaFilaHtml);
                    $(`#cotE_tipoPrecio_${prodId}`).val(i.tipo_precio);
                });

                // Recalcular total
                let total = 0;
                document.querySelectorAll('#cotE_tablaDetalle .cotE-precio-total').forEach(el => {
                    total += parseFloat(el.value) || 0;
                });
                document.getElementById('cotE_costoTotal').textContent = total.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                document.getElementById('cotE_totalCotizacion').value = total.toFixed(2);

                new bootstrap.Modal(document.getElementById('modalEditarCotizacion')).show();

            } catch (e) {
                console.error('[COT-EDIT] Error gestionar:', e);
                Swal.fire('Error', 'No se pudo estructurar el editor de la cotización', 'error');
            }
        }

        // =====================================================
        // EVENTO: cambio de almacén (un solo listener)
        // =====================================================
        $(document).on('change', '#cotE_almacen_id', function () {
            cotECargarClientes();
            cotECargarVendedores();
            cotERecargarProductos();
        });

        // =====================================================
        // EXPONER
        // =====================================================
        window.gestionarSolicitud = gestionarSolicitud;
        window.cotECalcularTotal = cotECalcularTotal;
        window.cotECalcularPrecioSugerido = cotECalcularPrecioSugerido;
        window.cotEQuitarFila = cotEQuitarFila;
        window.cotECargarClientes = cotECargarClientes;
        window.cotECargarVendedores = cotECargarVendedores;
        window.cotERecargarProductos = cotERecargarProductos;

    })();
</script>