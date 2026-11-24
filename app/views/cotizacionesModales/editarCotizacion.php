<style>
    /* Contenedor con scroll para que max-height recorte y desplace sin romper table-responsive */
    #tablaDetalleEditar {
        max-height: 250px;
    }

    .table-responsive:has(#tablaDetalleEditar) {
        max-height: 250px;
        overflow-y: auto;
        overflow-x: auto;
    }

    #tablaDetalleEditar thead th {
        position: sticky;
        top: 0;
        z-index: 2;

    }
</style>
<div class="modal fade" id="modalEditarCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="formEditarSolicitud">
                <input type="hidden" id="editar_cotizacion_id" name="cotizacion_id" value="">

                <!-- Encabezado -->
                <div class="modal-header  border-0 pt-4 px-4 align-items-center">
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
                    <div class="row g-3 mb-4 p-4 rounded-4  shadow-sm align-items-end border">

                        <!-- 1. Almacén de Cargo -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="almacen_id_editar"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select name="almacen_id_editar" id="almacen_id_editar"
                                class="form-select border-slate-200 control-fixed-height w-100 shadow-none" required>
                                <option value="">Seleccionar ubicación...</option>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Cliente -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cliente_id_editar"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person me-1 text-primary"></i> Cliente
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select name="cliente_id_editar" id="cliente_id_editar"
                                    class="form-select select2-modal-editar border-slate-200" required>
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
                            <label for="select-vendedor1"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                            </label>
                            <select name="select-vendedor1" id="select-vendedor1"
                                class="form-select select2-modal-editar border-slate-200 w-100" required>
                                <option value="">Seleccionar vendedor...</option>
                            </select>
                        </div>

                        <!-- 4. Añadir Producto -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="buscadorProductosEditar"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="buscadorProductosEditar"
                                    class="form-select select2-modal-editar border-slate-200">
                                    <option value="">Escribe SKU o nombre...</option>
                                    <?php foreach ($listaProductos as $pr): ?>
                                        <option value="<?= $pr['producto_id'] ?>"
                                            data-nombre="<?= htmlspecialchars($pr['nombre']) ?>"
                                            data-sku="<?= htmlspecialchars($pr['sku']) ?>"
                                            data-um="<?= htmlspecialchars($pr['unidad_medida']) ?>"
                                            data-ur="<?= htmlspecialchars($pr['unidad_reporte']) ?>"
                                            data-factor="<?= $pr['factor_conversion'] ?? 1 ?>">
                                            [<?= $pr['sku'] ?>] <?= $pr['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
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

                    <div class="table-responsive border rounded-4 ">
                        <table class="table align-middle mb-0" id="tablaDetalleEditar">
                            <thead class="">
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
                            <tbody>
                            </tbody>
                        </table>

                        <div id="emptyStateEditar" class="text-center py-5 text-body-secondary">
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
                            <div id="costoTotalCompraEditar" class="fw-bolder text-warning"
                                style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                            <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 p-4  d-flex justify-content-between align-items-center"
                    id="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2 align-items-center">
                        <button type="button"
                            class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center"
                            onclick="procederPago($('#totalCotizacionEditar').val(), $('#editar_cotizacion_id').val())">
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
    /* Ampliación del Modal en Pantallas Grandes */
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

    /* Congelar anchos en Input Group y Select2 */
    .input-group-fixed {
        width: 100% !important;
        max-width: 100% !important;
    }

    .input-group-fixed .select2-container {
        flex: 1 1 auto !important;
        width: 1% !important;
        min-width: 0 !important;
    }

    /* Altura uniforme */
    .control-fixed-height,
    .modal-body .form-select,
    .modal-body .input-group-fixed .btn {
        height: var(--control-height) !important;
    }

    /* Reglas Select2 para prevenir desbordamientos */
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

    /* Tabla de Ancho Fijo */
    .table-fixed {
        table-layout: fixed !important;
        width: 100% !important;
    }
</style>
<script>
    const URL_CONTROLADOR_EDITAR = '/myvet/app/controllers/cotizacionesController.php';

    let recalculandoFilaEditar = false;
    let cliente_id_edi = 0;

    /* =========================================================
       INICIALIZACIÓN
       ========================================================= */
    $(function () {

        // 1. Al abrir el modal, inicializar Select2 con templates + dropdownParent
        $('#modalEditarCotizacion').on('shown.bs.modal', function () {

            // Destruir Select2 previo si existe (evita duplicados)
            ['#cliente_id_editar', '#select-vendedor1', '#buscadorProductosEditar'].forEach(sel => {
                if ($(sel).hasClass('select2-hidden-accessible')) {
                    $(sel).select2('destroy');
                }
            });

            // 🔥 Cliente con templates + matcher + dropdownParent al modal
            $('#cliente_id_editar').select2({
                theme: 'bootstrap-5',
                width: 'resolve',
                dropdownParent: $('#modalEditarCotizacion'),
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

                    if (!razonSocial && !contacto) {
                        return $(`<span class="fw-semibold">${nombre.toUpperCase()}</span>`);
                    }

                    return $(`
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${nombre.toUpperCase()}</span>
                            <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                                <small class="text-truncate" style="max-width:65%;">${razonSocial.toUpperCase()}</small>
                                <small class="fw-semibold text-nowrap">${contacto ? `👤 ${contacto.toUpperCase()}` : ''}</small>
                            </div>
                        </div>
                    `);
                },
                templateSelection: function (state) {
                    if (!state.id) return state.text;
                    const nombre = String($(state.element).data('nombre') || state.text || '');
                    return nombre.toUpperCase();
                }
            });

            // Vendedor
            $('#select-vendedor1').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalEditarCotizacion')
            });

            // Buscador de productos
            $('#buscadorProductosEditar').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalEditarCotizacion')
            });
        });

        // 2. Cambio de almacén → recargar productos y clientes
        $('#almacen_id_editar').on('change', function () {
            const almacenId = this.value;
            if (!almacenId) return;

            console.log(`Almacén cambiado a ID: ${almacenId} - ${this.options[this.selectedIndex].text}`);
            recargarProductosEditar();
            cargarClienteseditar();
        });

        // 3. Carga inicial de clientes (sin tocar Select2)
        cargarClienteseditar();

        // 4. Producto seleccionado → agregar fila
        $('#buscadorProductosEditar').on('select2:select', function (e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();

            if ($(`#filaEditar-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            $('#emptyStateEditar').addClass('d-none');

            const medidas = JSON.parse(d.medidas || '[]');
            const opcionesUnidad = medidas.map(m =>
                `<option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">${m.nombre}</option>`
            ).join('');

            const preMin = parseFloat(d.preMin) || 0;
            const preMat = parseFloat(d.preMat) || 0;
            const preDis = parseFloat(d.preDis) || 0;
            const factor = parseFloat(d.factor) || 1;

            $('#tablaDetalleEditar tbody').append(`
                <tr id="filaEditar-${id}">
                    <td class="ps-4">
                        <b>${d.nombre}</b><br>
                        <small class="text-body-secondary">${d.sku}</small>
                    </td>
                    <td>
                        <input type="number" name="itemsEditar[${id}][cant]" class="form-control cantidad-editar"
                            step="0.01" value="1" min="0.01" required oninput="calcularTotalSolEditar(this)">
                    </td>
                    <td>
                        <select name="itemsEditar[${id}][unidad]" class="form-select unidad-select-editar"
                            onchange="calcularPrecioSugeridoEditar(this)">
                          
                            ${opcionesUnidad}
                        </select>
                    </td>
                    <td>
                        <select name="itemsEditar[${id}][tipoPrecio]" class="form-select tipoPrecio-select-editar"
                            onchange="calcularPrecioSugeridoEditar(this)">
                            <option value="seleccionar" data-precio="0">Seleccione</option>
                            <option value="minorista" data-precio="${preMin}">Min $${(preMin * factor).toFixed(2)} x ${d.ur}</option>
                            <option value="mayorista" data-precio="${preMat}">May $${(preMat * factor).toFixed(2)} x ${d.ur}</option>
                            <option value="distribuidor" data-precio="${preDis}">Dis $${(preDis * factor).toFixed(2)} x ${d.ur}</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" lang="en-US" name="itemsEditar[${id}][precioUnitario]"
                            class="form-control precio-unitario-editar"
                            step="0.01" min="0" placeholder="0.00" required
                            onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductosEditar').select2('open'); }"
                            oninput="calcularTotalSolEditar(this)">
                    </td>
                    <td style="min-width:160px;">
                        <input type="number" lang="en-US" name="itemsEditar[${id}][precio]"
                            class="form-control precio-total-editar fw-bold text-success"
                            step="0.01" min="0" placeholder="0.00"
                            onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductosEditar').select2('open'); }"
                            oninput="calcularTotalSolEditar(this)"
                            style="font-size:1.1rem; height:45px; min-width:140px;">
                    </td>
                    <td>
                        <button type="button" class="btn btn-link text-danger" onclick="quitarFilaEditar(${id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

            // 🔥 Enfocar y seleccionar el input de cantidad de la fila recién creada
            setTimeout(() => {
                const $fila = $(`#filaEditar-${id}`);
                const $cantidad = $fila.find('.cantidad-editar');

                if ($cantidad.length) {
                    const inputCantidad = $cantidad[0];
                    inputCantidad.focus();
                    inputCantidad.select();

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
            }, 30);

            $(this).val(null).trigger('change');
        });

        // 5. Submit
        $('#formEditarSolicitud').on('submit', async function (e) {
            e.preventDefault();

            if (!$('#tablaDetalleEditar tbody tr').length) {
                Swal.fire('Error', 'Agregue productos', 'warning');
                return;
            }

            const payload = {
                cotizacion_id: $('#editar_cotizacion_id').val(),
                almacen_id: $('#almacen_id_editar').val(),
                cliente_id: $('#cliente_id_editar').val(),
                vendedor: $('#select-vendedor1').val(),
                totalCotizacion: $('#totalCotizacionEditar').val(),
                items: []
            };

            $('#tablaDetalleEditar tbody tr').each(function () {
                const fila = $(this);
                const id = fila.attr('id').replace('filaEditar-', '');

                const unidadSelect = fila.find('.unidad-select-editar option:selected');
                const tipoPrecioSelect = fila.find('.tipoPrecio-select-editar option:selected');

                payload.items.push({
                    producto_id: id,
                    cantidad: fila.find('.cantidad-editar').val(),
                    unidad: unidadSelect.val(),
                    unidad_id: unidadSelect.data('medida-id'),
                    equivalencia: unidadSelect.data('equivalencia'),
                    tipoPrecio: tipoPrecioSelect.val(),
                    precioUnitario: fila.find('.precio-unitario-editar').val(),
                    precio: fila.find('.precio-total-editar').val()
                });
            });

            Swal.fire({ title: 'Actualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=actualizar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const res = await resp.json();

                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });

        // 6. Input precio unitario → marcar como editado
        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('precio-unitario-editar')) {
                e.target.dataset.editado = '1';
            }
        });

        // 7. Método de pago
        document.getElementById('metodoPago')?.addEventListener('change', function () {
            const requiere = ['transferencia', 'tarjeta', 'deposito'].includes(this.value);

            if ($(this).find(':selected').data('metodo') === 'credito') {
                $('#montoPago').val(0);
                $('#montoPago').prop('disabled', true);
            } else {
                $('#montoPago').prop('disabled', false);
            }

            document.getElementById('refBox')?.classList.toggle('d-none', !requiere);
        });
    });

    /* =========================================================
       CARGAR CLIENTES (solo llena <option>, NO toca Select2)
       ========================================================= */
    async function cargarClienteseditar() {
        console.log("cargo clientes editar");

        const almacenId = $('#almacen_id_editar').val();
        const select = document.getElementById('cliente_id_editar');
        if (!select) return;

        try {
            const url = '/myvet/app/controllers/accesoController.php?action=obtenerClientes';
            const respuesta = await fetch(url);

            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

            const resultado = await respuesta.json();
            console.log(resultado);

            if (resultado.success && Array.isArray(resultado.data)) {

                // FILTRADO
                const clientesFiltrados = resultado.data.filter(cliente => {
                    const nombreNorm = String(cliente.nombre_comercial || '').toLowerCase().trim();
                    const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                    if (esPublicoGeneral) {
                        return cliente.almacen_id == almacenId;
                    }

                    return true;
                });

                // 🔥 Construir opciones con data-* forzados a string
                const fragment = document.createDocumentFragment();

                const first = document.createElement('option');
                first.value = '';
                first.textContent = '-- Seleccione un cliente --';
                fragment.appendChild(first);

                clientesFiltrados.forEach(cliente => {
                    const opcion = document.createElement('option');
                    opcion.value = cliente.id;
                    opcion.textContent = String(cliente.nombre_comercial || '');

                    opcion.dataset.nombre = String(cliente.nombre_comercial || '');
                    opcion.dataset.razonSocial = String(cliente.razon_social || '');
                    opcion.dataset.contacto = String(cliente.contacto || '');

                    fragment.appendChild(opcion);
                });

                select.replaceChildren(fragment);

                // 🔥 SOLO refrescar Select2 (NO destruir, NO re-inicializar)
                $(select).trigger('change.select2');

                // Autoselección del cliente que venía de la BD
                if (cliente_id_edi) {
                    $(select).val(cliente_id_edi).trigger('change');
                } else if (clientesFiltrados.length > 0) {
                    $(select).val(clientesFiltrados[0].id).trigger('change');
                } else {
                    $(select).val(null).trigger('change');
                }

            } else {
                select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                $(select).trigger('change.select2');
            }
        } catch (error) {
            select.innerHTML = '<option value="">Error al cargar la lista</option>';
            $(select).trigger('change.select2');
            console.error('Error al ejecutar cargarClienteseditar:', error);
        }
    }

    /* =========================================================
       CARGAR VENDEDORES
       ========================================================= */
    async function cargarVendedores3(vendedor_id = null) {
        const select = document.getElementById('select-vendedor1');
        if (!select) return;

        const $select = $(select);
        $select.empty().val(null).trigger('change.select2');

        try {
            const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
            const respuesta = await fetch(url);

            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

            const resultado = await respuesta.json();

            if (resultado.success && Array.isArray(resultado.data)) {
                const fragment = document.createDocumentFragment();
                resultado.data.forEach(usuario => {
                    const opcion = document.createElement('option');
                    opcion.value = usuario.id;
                    opcion.textContent = String(usuario.nombre || '').toUpperCase();
                    fragment.appendChild(opcion);
                });
                select.appendChild(fragment);
                $select.trigger('change.select2');

                if (vendedor_id) {
                    $select.val(String(vendedor_id)).trigger('change.select2');
                }
            } else {
                $select.empty().append('<option value="">No se pudieron cargar los usuarios</option>').trigger('change.select2');
            }

        } catch (error) {
            $select.empty().append('<option value="">Error al cargar la lista</option>').trigger('change.select2');
            console.error('Error al ejecutar cargarVendedores3:', error);
        }
    }

    /* =========================================================
       RECARGAR PRODUCTOS
       ========================================================= */
    async function recargarProductosEditar() {
        try {
            const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=obtenerProductos`);
            const res = await resp.json();

            if (!res.success) throw new Error(res.message);

            const select = document.getElementById('buscadorProductosEditar');

            const fragment = document.createDocumentFragment();
            fragment.appendChild(new Option('Escribe para buscar...', '', true, true));

            (res.data || []).forEach(pr => {
                const option = document.createElement('option');
                option.value = pr.producto_id;
                option.dataset.nombre = String(pr.nombre || '');
                option.dataset.medidas = JSON.stringify(pr.medidas_adicionales || []);
                option.dataset.sku = String(pr.sku || '');
                option.dataset.um = String(pr.unidad_medida || '');
                option.dataset.ur = String(pr.unidad_reporte || '');
                option.dataset.preMin = pr.precio_minorista || 0;
                option.dataset.preMat = pr.precio_mayorista || 0;
                option.dataset.preDis = pr.precio_distribuidor || 0;
                option.dataset.factor = pr.factor_conversion || 1;
                option.textContent = `[${pr.sku}] ${pr.nombre}`;
                fragment.appendChild(option);
            });

            select.replaceChildren(fragment);
            $('#buscadorProductosEditar').trigger('change.select2');

        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'No se pudo actualizar la lista de productos', 'error');
        }
    }

    /* =========================================================
       CÁLCULOS
       ========================================================= */
    function calcularTotalSolEditar(input) {
        if (recalculandoFilaEditar) return;
        recalculandoFilaEditar = true;

        try {
            const fila = input ? input.closest('tr') : null;
            if (fila) {
                const cantidad = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
                const precioUnitario = parseFloat(fila.querySelector('.precio-unitario-editar')?.value) || 0;
                fila.querySelector('.precio-total-editar').value = (cantidad * precioUnitario).toFixed(2);
            }

            let totalCompraEditar = 0;
            document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
                totalCompraEditar += parseFloat(el.value) || 0;
            });

            document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
                style: 'currency', currency: 'MXN'
            });
            document.getElementById('totalCotizacionEditar').value = totalCompraEditar;

        } finally {
            recalculandoFilaEditar = false;
        }
    }

    function calcularPrecioSugeridoEditar(select) {
        const fila = select.closest('tr');
        const inputPrecio = fila.querySelector('.precio-unitario-editar');
        const inputTotal = fila.querySelector('.precio-total-editar');

        if (inputPrecio.dataset.editado === '1') return;

        const unidadSelect = fila.querySelector('.unidad-select-editar');
        const tipoSelect = fila.querySelector('.tipoPrecio-select-editar');

        const eq = Number(unidadSelect.options[unidadSelect.selectedIndex]?.dataset.equivalencia || 1);
        const base = Number(tipoSelect.options[tipoSelect.selectedIndex]?.dataset.precio || 0);
        const sug = eq > 0 ? base / eq : base;

        inputPrecio.value = sug.toFixed(2);

        const cant = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
        inputTotal.value = (sug * cant).toFixed(2);

        calcularTotalSolEditar(null);
    }

    function quitarFilaEditar(id) {
        $(`#filaEditar-${id}`).remove();

        if (!$('#tablaDetalleEditar tbody tr').length) {
            $('#emptyStateEditar').removeClass('d-none');
        }

        let totalCompraEditar = 0;
        document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
            totalCompraEditar += parseFloat(el.value) || 0;
        });
        document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
            style: 'currency', currency: 'MXN'
        });
        document.getElementById('totalCotizacionEditar').value = totalCompraEditar;
    }

    /* =========================================================
       GESTIONAR SOLICITUD (cargar datos en el modal)
       ========================================================= */
    async function gestionarSolicitud(id) {
        try {
            $('#tablaDetalleEditar tbody').empty();
            $('#formEditarSolicitud')[0].reset();

            console.log('Cargando cotización ID:', id);

            const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=obtenerDetalle&id=${id}`);
            const datos = await resp.json();
            const data = datos.data;

            console.log('DATOS RECUPERADOS:', data);

            if (!Array.isArray(data) || data.length === 0) {
                Swal.fire('Error', 'No se encontraron productos en esta cotización', 'warning');
                return;
            }

            const infoBase = data[0];

            $('#editar_cotizacion_id').val(infoBase.cotizacion_id);
            $('#almacen_id_editar').val(infoBase.almacen_origen_id);

            cliente_id_edi = infoBase.cliente_id;

            await Promise.allSettled([
                cargarClienteseditar(),
                cargarVendedores3(infoBase.vendedor_id),
                recargarProductosEditar()
            ]);

            $('#emptyStateEditar').addClass('d-none');

            data.forEach(i => {
                const prodId = i.producto_id;

                // ── Leer del catálogo (dataset → kebab-case) ──
                const optEl = document.querySelector(
                    `#buscadorProductosEditar option[value="${prodId}"]`
                );
                const ds = optEl?.dataset || {};

                const ur = ds.ur || i.unidad_reporte || '';
                const factor = parseFloat(ds.factor) || parseFloat(i.factor_conversion) || 1;

                const preMinCat = parseFloat(ds.preMin) || 0;
                const preMatCat = parseFloat(ds.preMat) || 0;
                const preDisCat = parseFloat(ds.preDis) || 0;

                // ── Precio y tipo de la cotización (lo que "ya viene") ──
                const precioCot = parseFloat(i.precio_unitario) || 0;
                const tipoCot = String(i.tipo_precio || '').toLowerCase();

                // Debug útil
                console.log(`[gestionarSolicitud] Prod ${prodId}`, {
                    ur, factor,
                    catalogo: { preMinCat, preMatCat, preDisCat },
                    cotizado: { precioCot, tipoCot },
                    datasetRaw: { ...ds }
                });

                // ── Construye la opción: prioriza precio cotizado para su tipo ──
                const buildPrecioOption = (tipo, label, precioCatalogo) => {
                    const esCotizado = tipo === tipoCot && precioCot > 0;

                    if (esCotizado) {
                        // El que ya viene → se muestra tal cual (sin × factor)
                        return `<option value="${tipo}" data-precio="${precioCot}">
                                ${label} $${precioCot.toFixed(2)} x ${ur}
                            </option>`;
                    }

                    if (precioCatalogo <= 0) {
                        // Sin precio de catálogo → mostramos guion
                        return `<option value="${tipo}" data-precio="0">${label} —</option>`;
                    }

                    // Catálogo → precio × factor (como el flujo original)
                    return `<option value="${tipo}" data-precio="${precioCatalogo}">
                            ${label} $${(precioCatalogo * factor).toFixed(2)} x ${ur}
                        </option>`;
                };

                const opcionesPrecioHtml = `
                <option value="seleccionar" data-precio="0">Seleccione</option>
                ${buildPrecioOption('minorista', 'Min', preMinCat)}
                ${buildPrecioOption('mayorista', 'May', preMatCat)}
                ${buildPrecioOption('distribuidor', 'Dis', preDisCat)}
            `;

                // ── Unidades: todas las del producto ──
                let medidas = [];
                try { medidas = JSON.parse(ds.medidas || '[]'); }
                catch (err) { medidas = []; }

                if (!medidas.some(m => String(m.id) === String(i.unidadMedida)) && i.unidadMedida) {
                    medidas = [{
                        id: i.unidadMedida,
                        equivalencia: i.equivalencia,
                        nombre: i.nombre || String(i.unidadMedida)
                    }, ...medidas];
                }

                const opcionesUnidadHtml = medidas.map(m => {
                    const sel = String(m.id) === String(i.unidadMedida) ? 'selected' : '';
                    return `<option value="${m.id}"
                                data-equivalencia="${m.equivalencia}"
                                data-medida-id="${m.id}" ${sel}>${m.nombre}</option>`;
                }).join('');

                const nuevaFilaHtml = `
                <tr id="filaEditar-${prodId}">
                    <td class="ps-4">
                        <b>${i.producto_nombre}</b><br>
                        <small class="text-body-secondary">${i.sku}</small>
                    </td>
                    <td>
                        <input type="number" name="itemsEditar[${prodId}][cant]" class="form-control cantidad-editar"
                            step="0.01" value="${parseFloat(i.cantidad)}" min="0.01" required
                            oninput="calcularTotalSolEditar(this)">
                    </td>
                    <td>
                        <select name="itemsEditar[${prodId}][unidad]" class="form-select unidad-select-editar"
                            onchange="calcularPrecioSugeridoEditar(this)">
                            ${opcionesUnidadHtml}
                        </select>
                    </td>
                    <td>
                        <select name="itemsEditar[${prodId}][tipoPrecio]" class="form-select tipoPrecio-select-editar"
                            id="tipoPrecio_editar_${prodId}" onchange="calcularPrecioSugeridoEditar(this)">
                            ${opcionesPrecioHtml}
                        </select>
                    </td>
                    <td>
                        <input type="number" lang="en-US" name="itemsEditar[${prodId}][precioUnitario]"
                            class="form-control precio-unitario-editar"
                            step="0.01" min="0" value="${parseFloat(i.precio_unitario).toFixed(2)}" required
                            onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductosEditar').select2('open'); }"
                            oninput="calcularTotalSolEditar(this)">
                    </td>
                    <td style="min-width:160px;">
                        <input type="number" lang="en-US" name="itemsEditar[${prodId}][precio]"
                            class="form-control precio-total-editar fw-bold text-success"
                            step="0.01" min="0" value="${parseFloat(i.subtotal).toFixed(2)}" readonly
                            onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductosEditar').select2('open'); }"
                            style="font-size:1.1rem; height:45px; min-width:140px;">
                    </td>
                    <td>
                        <button type="button" class="btn btn-link text-danger" onclick="quitarFilaEditar(${prodId})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

                $('#tablaDetalleEditar tbody').append(nuevaFilaHtml);

                // Preseleccionar el tipo que ya trae la cotización
                const $tipo = $(`#tipoPrecio_editar_${prodId}`);
                if (i.tipo_precio && $tipo.find(`option[value="${i.tipo_precio}"]`).length) {
                    $tipo.val(i.tipo_precio);
                }
            });

            let totalCompraEditar = 0;
            document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
                totalCompraEditar += parseFloat(el.value) || 0;
            });

            document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
                style: 'currency', currency: 'MXN'
            });
            document.getElementById('totalCotizacionEditar').value = totalCompraEditar;

            new bootstrap.Modal(document.getElementById('modalEditarCotizacion')).show();

        } catch (e) {
            console.error('Error al gestionar/mapear la solicitud:', e);
            Swal.fire('Error', 'No se pudo estructurar el editor de la cotización', 'error');
        }
    }
    /* =========================================================
 PROCEDER PAGO
 ========================================================= */
    async function procederPago(total, id) {
        cargarUsuariosSelectPago();
        totalGlobalPago = total;

        const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=obtenerDetalle&id=${id}`);
        const datos = await resp.json();
        let data = datos.data;

        let html = '';

        data.forEach((i, index) => {
            const cantidad = parseFloat(i.cantidad) || 0;
            const canti = cantidad / i.equivalencia;
            data[index].cantidadR = parseFloat(canti);
            data[index].entrega_hoy = 0;
            document.getElementById('montoPago').value = data[index].total;
            datost = data;

            html += `
                <tr>
                    <td class="text-center">
                        <input type="hidden" step="0.01" value="0" max="${i.entregar_hoy}"
                            class="form-control entrega-hoy" data-index="${index}">
                    </td>
                </tr>`;
        });

        $('#print-productos').html(html);

        document.querySelectorAll('.entrega-hoy').forEach(input => {
            input.addEventListener('input', function () {
                const index = this.dataset.index;
                data[index].entrega_hoy = parseFloat(this.value * (1 / data[index].equivalencia)) || 0;
                data[index].monto_pagado = parseFloat(document.getElementById('montoPago').value) || 0;
                datost = data;
            });
        });

        document.getElementById('montoPago').addEventListener('input', function () {
            const monto = parseFloat(this.value) || 0;
            data.forEach(item => { item.monto_pagado = monto; });
            datost = data;
        });

        const htmlboton = `
            <button class="btn btn-light w-50 rounded-3" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn w-50 text-white rounded-3" style="background: #334155;"
                onclick='convertirToCompra(${JSON.stringify(datost)}, ${id})'>Confirmar</button>`;

        document.getElementById('pagoTotal').textContent = total.toLocaleString('es-MX', {
            style: 'currency', currency: 'MXN'
        });

        document.getElementById('montoPago').value = total;
        document.getElementById('idC').value = id;

        window.detallePago = data;

        const modal = new bootstrap.Modal(document.getElementById('modalPago'));
        $('#boton').html(htmlboton);
        modal.show();
    }

    /* =========================================================
       CONVERTIR A COMPRA
       ========================================================= */
    async function convertirToCompra(data, id) {
        try {
            const payload = {
                accion: 'guardar_venta',
                data: datost,
                monto_pagado: parseFloat($('#montoPago').val()) || 0,
                metodo_pago: $('#metodoPago').val() || 'Efectivo',
                referencia: $('#referenciaPago').val() || '',
                vendedor: $('#select-vendedor1').val() || 1,
                idCotizacion: id,
                descuento: 0,
                observaciones: ''
            };

            const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=guardar_venta&id=${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const datos = await resp.json();

            if (datos.status === 'success') {
                // id de venta recién creada (ajusta la propiedad según tu backend)
                const idVenta = datos.id_venta ?? datos.venta_id ?? datos.id ?? null;

                Swal.fire({
                    icon: 'success',
                    title: 'Venta guardada',
                    text: `Folio: ${datos.folio || ''}`,
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Ticket simple',
                    denyButtonText: 'Ticket formal',
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#198754',
                    denyButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    let url = null;

                    if (result.isConfirmed) {
                        url = `/myvet/app/backend/ventas/ticket_venta.php?id=${idVenta}`;
                    } else if (result.isDenied) {
                        url = `/myvet/app/backend/ventas/ticketFormal.php?id=${idVenta}`;
                    }

                    // Si eligió un ticket → abrir en pestaña nueva
                    if (url) {
                        window.open(url, '_blank');
                    }

                    // Recargar siempre al cerrar el alert (eligió o canceló)
                    setTimeout(() => location.reload(), 400);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al guardar',
                    text: datos.message || 'Ocurrió un error inesperado',
                    confirmButtonText: 'Cerrar'
                });
            }

        } catch (e) {
            console.error(e);
        }
    }</script>