<div class="modal fade" id="modalCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: 20px;  width: 95%; max-width: 1140px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <form id="formSolicitud">

                <div class="modal-header   pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-3 p-2 me-3 shadow-sm d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bi bi-file-earmark-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 ">Nuevo Comprobante / Depósito</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para registrar el movimiento en
                                el
                                sistema</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-3">
                    <div class="row g-3 p-4 rounded-4  border align-items-end mb-2">

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-box-seam me-1"></i> Almacén de Cargo
                            </label>
                            <div class="input-group">
                                <span class="input-group-text  border-end-0 text-body-secondary"><i
                                        class="bi bi-geo-alt"></i></span>
                                <select name="almacen_id" id="almacen_id" class="form-select border-start-0 ps-0"
                                    required>

                                    <?php foreach ($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>">
                                            <?= htmlspecialchars($a['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-person me-1"></i> Cliente
                            </label>
                            <div class="input-group">
                                <select name="cliente_id" id="cliente_id" class="form-select select2-modal" required>



                                </select>
                                <button class="btn btn-primary" type="button" onclick="abrirModalNuevoCliente()"
                                    title="Registrar nuevo cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-8 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-bookmark me-1"></i> Numero(s) de ordenes
                            </label>
                            <input type="text" placeholder="Ej. Pago compra orden #123...." id="numero_venta"
                                name="numero_venta" class="form-control">
                        </div>

                        <?php if ($rolAct == 1): ?>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-2">
                                    <i class="bi bi-calendar3 me-1"></i> Fecha de Depósito
                                </label>
                                <input type="date" id="fecha_deposito" value="<?= date('Y-m-d') ?>" name="fecha_deposito"
                                    class="form-control" required>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-currency-dollar me-1"></i> Monto
                            </label>
                            <div class="input-group">
                                <span class="input-group-text  text-success fw-bold">$</span>
                                <input type="number" step="0.01" placeholder="0.00" id="monto_depositado"
                                    name="monto_depositado" class="form-control fw-bold " required>
                            </div>
                        </div>
                        <div class="col-5">
                            <select id="metodo_pago_m" name="metodo_pago_m" class="form-select fw-bold">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>

                            </select>
                        </div>

                        <div class="col-md-8 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-bookmark me-1"></i> Referencia / Concepto
                            </label>
                            <input type="text" placeholder="Ej. Pago compra orden #123...." id="referencia"
                                name="referencia" class="form-control">
                        </div>

                    </div>
                </div>

                <div class="modal-footer  p-4 pt-2">
                    <button type="button" class="btn btn-light text-body-secondary fw-bold rounded-pill px-4 me-2"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Crear Comprobante
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
    (function () {
        'use strict';

        const URL_CONTROLADOR = '/myvet/app/controllers/comprobantesPagoController.php';
        const URL_ACCESO = '/myvet/app/controllers/accesoController.php';

        function log(...args) {
            console.log('[COMPROBANTE]', ...args);
        }

        // =====================================================
        // CARGAR CLIENTES (solo llena <option>, NO toca Select2)
        // =====================================================
        async function cargarClientes() {
            log("cargando clientes");

            const almacenId = $('#almacen_id').val();
            const select = document.getElementById('cliente_id');
            if (!select) return;

            try {
                const url = `${URL_ACCESO}?action=obtenerClientes`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();
                log(resultado);

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

                    // Autoselección del primer cliente
                    if (clientesFiltrados.length > 0) {
                        $('#cliente_id').val(clientesFiltrados[0].id).trigger('change');
                    } else {
                        $('#cliente_id').val(null).trigger('change');
                    }

                    log('Clientes cargados:', clientesFiltrados.length);

                } else {
                    select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                    $(select).trigger('change.select2');
                }
            } catch (error) {
                select.innerHTML = '<option value="">Error al cargar la lista</option>';
                $(select).trigger('change.select2');
                console.error('[COMPROBANTE] Error cargarClientes:', error);
            }
        }

        // =====================================================
        // INICIALIZACIÓN ÚNICA
        // =====================================================
        $(function () {

            // 1. Al abrir el modal, inicializar Select2 con templates + dropdownParent
            $('#modalCotizacion').on('shown.bs.modal', function () {

                // Destruir Select2 previo si existe
                ['#cliente_id', '#metodo_pago_m'].forEach(sel => {
                    if ($(sel).hasClass('select2-hidden-accessible')) {
                        $(sel).select2('destroy');
                    }
                });

                // 🔥 Cliente con templates + matcher + dropdownParent
                $('#cliente_id').select2({
                    theme: 'bootstrap-5',
                    width: 'resolve',
                    dropdownParent: $('#modalCotizacion'),
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

                // Otros .select2-modal (excluyendo #cliente_id)
                $('.select2-modal').not('#cliente_id').each(function () {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#modalCotizacion')
                    });
                });

                log('Select2 inicializado');
            });

            // 2. Cambio de almacén → recargar clientes
            $('#almacen_id').on('change', function () {
                const almacenId = this.value;
                if (!almacenId) {
                    log('Se deseleccionó el almacén');
                } else {
                    log(`Almacén cambiado a ID: ${almacenId} - ${this.options[this.selectedIndex].text}`);
                }
                cargarClientes();
            });

            // 3. Carga inicial de clientes (sin tocar Select2)
            cargarClientes();

            // 4. Submit
            $('#formSolicitud').on('submit', async function (e) {
                e.preventDefault();

                const payload = {
                    almacen_id: $('#almacen_id').val(),
                    cliente_id: $('#cliente_id').val(),
                    monto_depositado: $('#monto_depositado').val(),
                    referencia: $('#referencia').val(),
                    fecha: $('#fecha_deposito').val(),
                    metodo: $('#metodo_pago_m').val(),
                    numeroventa: $('#numero_venta').val()
                };

                log('JSON ENVIADO:', payload);

                Swal.fire({
                    title: 'Guardando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    const res = await resp.json();
                    log('RESPUESTA:', res);

                    if (res.status === 'success') {
                        await Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        location.reload();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }

                } catch (e) {
                    console.error('[COMPROBANTE] Error submit:', e);
                    Swal.fire('Error', 'Fallo de conexión o error en el servidor', 'error');
                }
            });

            // 5. Nueva cotización → abrir modal y cargar clientes
            window.nuevaCotizacion = function () {
                $('#formSolicitud')[0].reset();
                $('#modalCotizacion').modal('show');

                // Cargar clientes después de que el modal se abra
                $('#modalCotizacion').one('shown.bs.modal', function () {
                    cargarClientes();
                });
            };

            // 6. Exponer funciones globales
            window.cargarClientes = cargarClientes;
            window.quitarFila = function (id) {
                $(`#fila-${id}`).remove();
                if (!$('#tablaDetalle tbody tr').length) {
                    $('#emptyState').removeClass('d-none');
                }
            };
        });
    })();
</script>