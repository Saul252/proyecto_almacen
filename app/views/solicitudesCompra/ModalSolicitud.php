<div class="modal fade" id="modalSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content  shadow-lg rounded-4 overflow-hidden">
            <form id="formSolicitud">
                <!-- Encabezado -->
                <div class="modal-header   pt-4 px-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-file-earmark-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Nueva Solicitud de Compra</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para requerir materiales al
                                almacén</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <!-- Fila de Controles -->
                    <div class="row g-3 mb-4 p-4 rounded-4  shadow-sm align-items-end border">

                        <!-- 1. Almacén de Cargo -->
                        <div class="col-md-6 col-lg-4 min-w-0">
                            <label for="almacen_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>

                            <?php $es_admin = ($_SESSION['rol_id'] == 1); ?>

                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="almacen_id" onchange="recargarProductos()"
                                    name="<?= $es_admin ? 'almacen_id' : 'almacen_id_cabecera_visual' ?>"
                                    class="form-select border-slate-200 control-fixed-height <?= !$es_admin ? '' : '' ?>"
                                    <?= !$es_admin ? 'disabled' : '' ?> required>



                                    <?php foreach ($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>" <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                            <?= $a['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <?php if (!$es_admin): ?>
                                    <span
                                        class="input-group-text  text-body-secondary border-slate-200 control-fixed-height flex-shrink-0">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                <?php endif; ?>
                            </div>


                        </div>

                        <!-- 2. Proveedor Sugerido -->
                        <div class="col-md-6 col-lg-4 min-w-0">
                            <label for="proveedor_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-truck me-1 text-primary"></i> Proveedor Sugerido
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select name="proveedor_id" id="proveedor_id"
                                    class="form-select select2-modal border-slate-200" required>
                                    <option value="">Seleccionar proveedor...</option>

                                </select>
                                <button
                                    class="btn btn-outline-success px-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    type="button" onclick="abrirModalNuevoProveedor()" title="Nuevo Proveedor">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Añadir Producto -->
                        <div class="col-md-12 col-lg-4 min-w-0">
                            <label for="buscadorProductos"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="buscadorProductos" class="form-select select2-modal border-slate-200">
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

                    <!-- Tabla de Detalle -->
                    <div class="table-responsive border rounded-4 ">
                        <table class="table align-middle mb-0" id="tablaDetalle">
                            <thead class="">
                                <tr class="text-body-secondary small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 25%;">Presentación / Unidad</th>
                                    <th style="width: 25%;">PrecioUnitario</th>
                                    <th style="width: 50%;">Precio</th>
                                    <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>

                        <div id="emptyState" class="text-center py-5 ">
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
                                Costo Total de Compra
                            </small>
                            <div id="costoTotalCompra" class="fw-bolder text-success"
                                style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer  p-4  d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-check2-circle fs-5 me-2"></i> Confirmar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Ampliación del Modal */
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

    /* Forzar límites en Input Groups */
    .input-group-fixed {
        width: 100% !important;
        max-width: 100% !important;
    }

    .input-group-fixed .select2-container {
        flex: 1 1 auto !important;
        width: 1% !important;
        min-width: 0 !important;
    }

    /* Alturas homogéneas */
    .control-fixed-height,
    .modal-body .form-select,
    .modal-body .input-group-fixed .btn {
        height: var(--control-height) !important;
    }

    /* Reglas estrictas Select2 */
    .modal-body .select2-container--bootstrap-5 .select2-selection,
    .modal-body .select2-container .select2-selection--single {
        height: var(--control-height) !important;

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

    /* Tabla Fija */
    .table-fixed {
        table-layout: fixed !important;
        width: 100% !important;
    }
</style>


<script>
    (function () {
        'use strict';

        const URL_CONTROLADOR = '/myvet/app/controllers/solicitudesCompraController.php';
        const URL_ACCESO = '/myvet/app/controllers/accesoController.php';
        const URL_EGRESOS = '/myvet/app/controllers/egresosController.php';

        let recalculandoFila = false;

        function log(...args) {
            console.log('[SOL-COMPRA]', ...args);
        }

        // =====================================================
        // SELECT2 (inicialización única en shown.bs.modal)
        // =====================================================
        $(document).on('shown.bs.modal', '#modalSolicitud', function () {

            // Destruir Select2 previo si existe
            ['#proveedor_id', '#buscadorProductos'].forEach(sel => {
                if ($(sel).hasClass('select2-hidden-accessible')) {
                    $(sel).select2('destroy');
                }
            });

            $('#proveedor_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalSolicitud')
            });

            $('#buscadorProductos').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalSolicitud')
            });

            log('Select2 inicializado');
        });

        // =====================================================
        // CARGAR PROVEEDORES
        // =====================================================
        function refrescarListaProveedores() {
            const almacen = $('#almacen_id').val();
            const $proveedorSelect = $('#proveedor_id');

            if (!almacen) return;

            $.get(`${URL_EGRESOS}?action=getProveedoresJSONAlmacen&almacen=${almacen}`, function (data) {
                const proveedores = Array.isArray(data) ? data : (data.data || []);

                const fragment = document.createDocumentFragment();
                fragment.appendChild(new Option('Seleccione un proveedor', '', true, true));

                proveedores.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nombre_comercial;
                    fragment.appendChild(opt);
                });

                $proveedorSelect.empty().append(fragment).trigger('change.select2');
                log('Proveedores cargados:', proveedores.length);
            }, 'json').fail(function (e) {
                console.error('[SOL-COMPRA] Error proveedores:', e.responseText);
            });
        }

        // =====================================================
        // CALCULAR TOTAL
        // =====================================================
        function calcularTotalSol(input) {
            if (recalculandoFila) return;
            recalculandoFila = true;

            try {
                const fila = input.closest('tr');
                const cantidad = parseFloat(fila.querySelector('.cantidad')?.value) || 0;
                const precioUnitario = parseFloat(fila.querySelector('.precio-unitario')?.value) || 0;

                fila.querySelector('.precio-total').value = (cantidad * precioUnitario).toFixed(2);

                let totalCompra = 0;
                document.querySelectorAll('#tablaDetalle .precio-total').forEach(el => {
                    totalCompra += parseFloat(el.value) || 0;
                });

                const cT = document.getElementById('costoTotalCompra');
                if (cT) {
                    cT.textContent = totalCompra.toLocaleString('es-MX', {
                        style: 'currency',
                        currency: 'MXN'
                    });
                }
            } finally {
                recalculandoFila = false;
            }
        }

        // =====================================================
        // CARGAR PRODUCTOS
        // =====================================================
        async function recargarProductos() {
            const id = document.getElementById('almacen_id')?.value;
            if (!id) return;

            try {
                const resp = await fetch(`${URL_ACCESO}?action=obtenerProductosAlmacen&id=${id}`);
                const res = await resp.json();

                if (!res.success) throw new Error(res.message);

                const select = document.getElementById('buscadorProductos');

                const fragment = document.createDocumentFragment();
                fragment.appendChild(new Option('Escribe para buscar...', '', true, true));

                (res.data || []).forEach(pr => {
                    const option = document.createElement('option');
                    option.value = pr.producto_id;
                    option.dataset.nombre = String(pr.nombre || '');
                    option.dataset.sku = String(pr.sku || '');
                    option.dataset.um = String(pr.unidad_medida || '');
                    option.dataset.ur = String(pr.unidad_reporte || '');
                    option.dataset.factor = pr.factor_conversion || 1;
                    option.textContent = `[${pr.sku}] ${pr.nombre}`;
                    fragment.appendChild(option);
                });

                select.replaceChildren(fragment);
                $('#buscadorProductos').trigger('change.select2');

                log('Productos cargados:', (res.data || []).length);

            } catch (e) {
                console.error('[SOL-COMPRA] Error productos:', e);
                Swal.fire('Error', 'No se pudo actualizar la lista de productos', 'error');
            }
        }

        // =====================================================
        // AGREGAR PRODUCTO A LA TABLA
        // =====================================================
        $(document).on('select2:select', '#buscadorProductos', function (e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();

            if ($(`#fila-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            $('#emptyState').addClass('d-none');

            $('#tablaDetalle tbody').append(`
                <tr id="fila-${id}">
                    <td class="ps-4">
                        <b>${d.nombre}</b><br>
                        <small class="text-body-secondary">${d.sku}</small>
                    </td>
                    <td>
                        <input type="number" name="items[${id}][cant]" class="form-control cantidad"
                            step="0.01" value="1" min="0.01" required oninput="calcularTotalSol(this)">
                    </td>
                    <td>
                        <select name="items[${id}][unidad]" class="form-select unidad-select" onchange="calcularTotalSol(this)">
                            <option value="1">(${d.ur})</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" lang="en-US" name="items[${id}][precioUnitario]"
                            class="form-control precio-unitario"
                            step="0.01" min="0" placeholder="0.00" required
                            onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductos').select2('open'); }"
                            oninput="calcularTotalSol(this)">
                    </td>
                    <td style="min-width:160px;">
                        <input type="number" lang="en-US" name="items[${id}][precio]"
                            class="form-control precio-total fw-bold text-success"
                            step="0.01" min="0" placeholder="0.00"
                            oninput="calcularTotalSol(this)"
                            onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductos').select2('open'); }"
                            style="font-size:1.1rem; height:45px; min-width:140px;">
                    </td>
                    <td>
                        <button type="button" class="btn btn-link text-danger" onclick="quitarFila(${id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

            // 🔥 Enfocar y seleccionar el input de cantidad de la nueva fila
            setTimeout(() => {
                const $fila = $(`#fila-${id}`);
                const $cantidad = $fila.find('.cantidad');

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

        // =====================================================
        // QUITAR FILA
        // =====================================================
        function quitarFila(id) {
            $(`#fila-${id}`).remove();

            if (!$('#tablaDetalle tbody tr').length) {
                $('#emptyState').removeClass('d-none');
            }

            // Recalcular total
            let totalCompra = 0;
            document.querySelectorAll('#tablaDetalle .precio-total').forEach(el => {
                totalCompra += parseFloat(el.value) || 0;
            });

            const cT = document.getElementById('costoTotalCompra');
            if (cT) {
                cT.textContent = totalCompra.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });
            }
        }

        // =====================================================
        // GUARDAR SOLICITUD
        // =====================================================
        $(document).on('submit', '#formSolicitud', async function (e) {
            e.preventDefault();

            if (!$('#tablaDetalle tbody tr').length) {
                Swal.fire('Error', 'Agregue productos', 'warning');
                return;
            }

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
                    method: 'POST',
                    body: new FormData(this)
                });

                const res = await resp.json();

                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: res.message,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'IMPRIMIR',
                        denyButtonText: 'SALIR',
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#34c759',
                        denyButtonColor: '#5856d6',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then((result) => {
                        let url = '';

                        if (result.isConfirmed) {
                            $('#modalSolicitud').modal('hide');
                            if (typeof prepararImpresion === 'function') prepararImpresion(res.id);

                            setTimeout(() => {
                                if (typeof ejecutarImpresion === 'function') ejecutarImpresion();
                                if (typeof cargarSolicitudes === 'function') cargarSolicitudes();
                            }, 500);
                        } else if (result.isDenied) {
                            url = `/myvet/app/controllers/solicitudesCompraController.php`;
                        }

                        if (url !== '') window.open(url, '_blank');
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }

            } catch (e) {
                console.error('[SOL-COMPRA] Error guardar:', e);
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });

        // =====================================================
        // NUEVA SOLICITUD
        // =====================================================
        function nuevaSolicitud() {
            $('#formSolicitud')[0].reset();
            $('#tablaDetalle tbody').empty();
            $('#emptyState').removeClass('d-none');
            $('#costoTotalCompra').text('$0.00');

            $('#modalSolicitud').modal('show');

            // Cargar datos después de que el modal se abra
            $('#modalSolicitud').one('shown.bs.modal', function () {
                recargarProductos();
                refrescarListaProveedores();
            });
        }

        // =====================================================
        // EXPONER FUNCIONES
        // =====================================================
        window.refrescarListaProveedores = refrescarListaProveedores;
        window.calcularTotalSol = calcularTotalSol;
        window.recargarProductos = recargarProductos;
        window.quitarFila = quitarFila;
        window.nuevaSolicitud = nuevaSolicitud;

    })();
</script>