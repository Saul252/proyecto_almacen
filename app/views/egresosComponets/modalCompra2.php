<div class="modal fade" id="modalCompra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content  shadow-lg rounded-4 overflow-hidden">
            <form id="mc_formCompra">
                <!-- Encabezado -->
                <div class="modal-header  pt-4 px-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-cart-plus-fill fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Nueva Orden de Compra</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para registrar la entrada de materiales</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <!-- Fila de Controles -->
                    <div class="row g-3 mb-4 p-4 rounded-4 shadow-sm align-items-end border">
                        
                        <!-- 1. Almacén de Cargo -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="mc_almacen_id" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select name="mc_almacen_id" id="mc_almacen_id" 
                                class="form-select border-slate-200 control-fixed-height w-100 shadow-none" required>
                                <option value="">Seleccionar ubicación...</option>
                                <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Cliente / Proveedor -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="mc_cliente_id" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person me-1 text-primary"></i> Cliente / Proveedor
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select name="mc_cliente_id" id="mc_cliente_id" 
                                    class="form-select mc-select2 border-slate-200" required>
                                    <option value="">Seleccionar cliente...</option>
                                </select>
                                <button class="btn btn-outline-primary px-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                                    type="button" onclick="mc_abrirModalNuevoCliente()" title="Nuevo Cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Vendedor / Comprador -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="mc_vendedor_id" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor / Usuario
                            </label>
                            <select class="form-select mc-select2 border-slate-200 w-100" id="mc_vendedor_id" name="mc_vendedor_id" required>
                                <option value="">Seleccione vendedor</option>
                            </select>
                        </div>

                        <!-- 4. Añadir Producto -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="mc_buscadorProductos" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="mc_buscadorProductos" name="mc_buscadorProductos" class="form-select mc-select2 border-slate-200">
                                    <option value="">Escribe SKU o nombre...</option>
                                    <?php foreach($listaProductos as $pr): ?>
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
                                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center px-3 flex-shrink-0" 
                                    onclick="mc_abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    <span class="fw-medium">Nuevo</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Tabla de Detalle -->
                    <div class="table-responsive border rounded-4">
                        <table class="table align-middle mb-0" id="mc_tablaDetalle">
                            <thead>
                                <tr class="text-body-secondary small uppercase">
                                    <th class="ps-4" style="width: 35%;">Producto</th>
                                    <th style="width: 15%;">Cantidad</th>
                                    <th style="width: 20%;">Presentación / Unidad</th>
                                    <th style="width: 20%;">Tipo de precio</th>
                                    <th class="ps-4" style="width: 15%;">Precio Unitario</th>
                                    <th style="width: 20%;">TOTAL</th>
                                    <th style="width: 5%;" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <div id="mc_emptyState" class="text-center py-5 text-body-secondary">
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
                            <small class="d-block text-white-50 fw-bold text-uppercase tracking-wider mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                                Costo Total de Compra
                            </small>
                            <div id="mc_costoTotalCompra" class="fw-bolder text-success" style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                            <input type="hidden" id="mc_totalCompra" name="mc_totalCompra" value="0">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer  p-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-check2-circle fs-5 me-2"></i> Confirmar Compra
                    </button>
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
const MC_URL_CONTROLADOR = '/myvet/app/controllers/cotizacionesController.php';

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2 en el modal
    $('.mc-select2').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalCompra')
    });

    const selectAlmacen = document.getElementById('mc_almacen_id');
    if (selectAlmacen) {
        selectAlmacen.addEventListener('change', function() {
            const almacenId = this.value;
            if (almacenId) {
                mc_cargarClientes();
            }
        });
    }

    mc_cargarClientes();
});

// =====================================================
// CARGA DE DATOS (CLIENTES / VENDEDORES / PRODUCTOS)
// =====================================================

async function mc_cargarClientes() {
    const almacenId = $('#mc_almacen_id').val();
    const select = document.getElementById('mc_cliente_id');
    if (!select) return;

    select.innerHTML = '<option value="">-- Seleccione cliente --</option>';

    try {
        const url = '/myvet/app/controllers/accesoController.php?action=obtenerClientes';
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();

        if (resultado.success && Array.isArray(resultado.data)) {
            const clientesFiltrados = resultado.data.filter(cliente => {
                const nombreNorm = cliente.nombre_comercial.toLowerCase().trim();
                const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                if (esPublicoGeneral) {
                    return cliente.almacen_id == almacenId;
                }
                return true;
            });

            clientesFiltrados.forEach(cliente => {
                const opcion = document.createElement('option');
                opcion.value = cliente.id;
                opcion.textContent = `${cliente.nombre_comercial}`;
                select.appendChild(opcion);
            });

            $('#mc_cliente_id').trigger('change.select2');
        } else {
            select.innerHTML = '<option value="">No se pudieron cargar clientes</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar mc_cargarClientes:', error);
    }
}

async function mc_cargarVendedores() {
    const select = document.getElementById('mc_vendedor_id');
    if (!select) return;

    try {
        const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();

        if (resultado.success && Array.isArray(resultado.data)) {
            select.innerHTML = '<option value="">Seleccione vendedor</option>';
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id;
                opcion.textContent = `${usuario.nombre}`;
                select.appendChild(opcion);
            });
            $('#mc_vendedor_id').trigger('change.select2');
        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar mc_cargarVendedores:', error);
    }
}

async function mc_recargarProductos() {
    try {
        const resp = await fetch(`${MC_URL_CONTROLADOR}?action=obtenerProductos`);
        const res = await resp.json();

        if (!res.success) throw new Error(res.message);

        const select = document.getElementById('mc_buscadorProductos');
        select.innerHTML = `<option value="">Escribe SKU o nombre...</option>`;

        res.data.forEach(pr => {
            const option = document.createElement('option');
            option.value = pr.producto_id;
            option.dataset.nombre = pr.nombre || '';
            option.dataset.medidas = JSON.stringify(pr.medidas_adicionales || []);
            option.dataset.sku = pr.sku || '';
            option.dataset.um = pr.unidad_medida || '';
            option.dataset.ur = pr.unidad_reporte || '';
            option.dataset.premin = pr.precio_minorista || 0;
            option.dataset.premat = pr.precio_mayorista || 0;
            option.dataset.predis = pr.precio_distribuidor || 0;
            option.dataset.factor = pr.factor_conversion || 1;

            option.textContent = `[${pr.sku}] ${pr.nombre}`;
            select.appendChild(option);
        });

        $('#mc_buscadorProductos').trigger('change.select2');

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'No se pudo actualizar la lista de productos', 'error');
    }
}

// =====================================================
// EVENTO: AGREGAR PRODUCTO A LA TABLA
// =====================================================

$('#mc_buscadorProductos').on('select2:select', function(e) {
    const id = $(this).val();
    if (!id) return;

    // Obtener la opción seleccionada directamente desde el DOM para evitar que Select2 omita data-attributes
    const selectedOption = $(this).find('option:selected');
    const d = selectedOption.data();

    // Validar duplicados en la tabla
    if ($(`#mc_fila-${id}`).length) {
        Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
        $(this).val(null).trigger('change');
        return;
    }

    $('#mc_emptyState').addClass('d-none');

    let medidas = [];
    try {
        medidas = typeof d.medidas === 'string' ? JSON.parse(d.medidas) : (d.medidas || []);
    } catch (err) {
        medidas = [];
    }

    let opcionesUnidad = '';
    medidas.forEach(m => {
        opcionesUnidad += `
            <option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">
                ${m.nombre}
            </option>
        `;
    });

    const preMin = d.premin || 0;
    const preMat = d.premat || 0;
    const preDis = d.predis || 0;
    const factor = d.factor || 1;
    const ur = d.ur || '';

    $('#mc_tablaDetalle tbody').append(`
        <tr id="mc_fila-${id}">
            <!-- PRODUCTO -->
            <td class="ps-4">
                <b>${d.nombre}</b><br>
                <small class="text-body-secondary">${d.sku}</small>
            </td>

            <!-- CANTIDAD -->
            <td>
                <input 
                    type="number"
                    name="items[${id}][cant]"
                    class="form-control cantidad"
                    step="0.01"
                    value="1"
                    min="0.01"
                    required
                    oninput="mc_calcularTotalFila(this)">
            </td>

            <!-- UNIDAD -->
            <td>
                <select 
                    name="items[${id}][unidad]" 
                    class="form-select unidad-select unidad"
                    onchange="mc_calcularPrecioSugerido(this)">
                    <option value="0" data-equivalencia="1" data-medida-id="0">Seleccione</option>
                    ${opcionesUnidad}
                </select>
            </td>
            
            <!-- TIPO DE PRECIO -->
            <td>
                <select 
                    name="items[${id}][tipoPrecio]" 
                    class="form-select tipoPrecio-select tipoPrecio"
                    onchange="mc_calcularPrecioSugerido(this)">
                    <option value="seleccionar" data-precio="0">Seleccione</option>
                    <option value="minorista" data-precio="${preMin}">Min ${preMin * factor} x ${ur}</option>
                    <option value="mayorista" data-precio="${preMat}">May ${preMat * factor} x ${ur}</option>
                    <option value="distribuidor" data-precio="${preDis}">Dis ${preDis * factor} x ${ur}</option>
                </select>
            </td>

            <!-- COSTO UNITARIO -->
            <td>
                <input 
                    type="number"
                    lang="en-US"
                    name="items[${id}][precioUnitario]"
                    class="form-control precio-unitario"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    required
                    oninput="mc_calcularTotalFila(this)"
                >
            </td>

            <!-- COSTO TOTAL -->
            <td style="min-width:140px;">
                <input 
                    type="number"
                    lang="en-US"
                    name="items[${id}][precio]"
                    class="form-control precio-total fw-bold text-success"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    oninput="mc_calcularTotalFila(this)"
                    style="font-size:1.1rem; height:45px;"
                >
            </td>

            <!-- ELIMINAR -->
            <td class="text-end pe-4">
                <button 
                    type="button"
                    class="btn btn-link text-danger p-0"
                    onclick="mc_quitarFila('${id}')"
                >
                    <i class="bi bi-trash fs-5"></i>
                </button>
            </td>
        </tr>
    `);

    // Resetear el buscador de Select2
    $(this).val(null).trigger('change');
});

// =====================================================
// CÁLCULOS MATEMÁTICOS
// =====================================================

function mc_calcularPrecioSugerido(select) {
    const fila = select.closest('tr');

    const inputPrecio = fila.querySelector('.precio-unitario');
    const inputTotal = fila.querySelector('.precio-total');

    const unidadSelect = fila.querySelector('.unidad-select');
    const tipoSelect = fila.querySelector('.tipoPrecio-select');

    const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
    const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

    const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
    const precioBase = Number(tipoOption?.dataset.precio || 0);

    const sugerido = equivalencia > 0 ? (precioBase / equivalencia) : 0;

    inputPrecio.value = sugerido.toFixed(2);
    mc_calcularTotalFila(inputPrecio);
}

let mc_recalculandoFila = false;

function mc_calcularTotalFila(input) {
    if (mc_recalculandoFila) return;
    mc_recalculandoFila = true;

    try {
        const fila = input.closest('tr');
        const cantidad = parseFloat(fila.querySelector('.cantidad').value) || 0;
        const precioUnitario = parseFloat(fila.querySelector('.precio-unitario').value) || 0;

        const precioTotal = cantidad * precioUnitario;
        fila.querySelector('.precio-total').value = precioTotal.toFixed(2);

        mc_actualizarGranTotal();
    } finally {
        mc_recalculandoFila = false;
    }
}

function mc_actualizarGranTotal() {
    let totalAcumulado = 0;

    document.querySelectorAll('#mc_tablaDetalle .precio-total').forEach(el => {
        totalAcumulado += parseFloat(el.value) || 0;
    });

    document.getElementById('mc_costoTotalCompra').textContent = totalAcumulado.toLocaleString('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });
    document.getElementById('mc_totalCompra').value = totalAcumulado.toFixed(2);
}

function mc_quitarFila(id) {
    $(`#mc_fila-${id}`).remove();
    mc_actualizarGranTotal();

    if (!$('#mc_tablaDetalle tbody tr').length) {
        $('#mc_emptyState').removeClass('d-none');
    }
}

// =====================================================
// GUARDAR Y ABRIR MODAL
// =====================================================

$('#mc_formCompra').on('submit', async function(e) {
    e.preventDefault();

    if (!$('#mc_tablaDetalle tbody tr').length) {
        Swal.fire('Error', 'Agregue al menos un producto a la lista', 'warning');
        return;
    }

    const payload = {
        almacen_id: $('#mc_almacen_id').val(),
        cliente_id: $('#mc_cliente_id').val(),
        totalCompra: $('#mc_totalCompra').val(),
        vendedor_id: $('#mc_vendedor_id').val(),
        items: []
    };

    $('#mc_tablaDetalle tbody tr').each(function() {
        const fila = $(this);
        const id = fila.attr('id').replace('mc_fila-', '');

        const unidadSelect = fila.find('.unidad-select option:selected');
        const tipoPrecioSelect = fila.find('.tipoPrecio-select option:selected');

        payload.items.push({
            producto_id: id,
            cantidad: fila.find('.cantidad').val(),
            unidad: unidadSelect.val(),
            unidad_id: unidadSelect.data('medida-id'),
            equivalencia: unidadSelect.data('equivalencia'),
            tipoPrecio: tipoPrecioSelect.val(),
            precioUnitario: fila.find('.precio-unitario').val(),
            precioTotal: fila.find('.precio-total').val()
        });
    });

    Swal.fire({
        title: 'Procesando Compra...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const resp = await fetch(`${MC_URL_CONTROLADOR}?action=guardarCompra`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const res = await resp.json();

        if (res.status === 'success' || res.success) {
            await Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: res.message || 'Compra registrada correctamente',
                timer: 1500,
                showConfirmButton: false
            });
            location.reload();
        } else {
            Swal.fire('Error', res.message || 'No se pudo completar la transacción', 'error');
        }

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión con el servidor', 'error');
    }
});

function mc_crearNuevaCompra() {
    $('#mc_formCompra')[0].reset();
    $('.mc-select2').val(null).trigger('change');
    $('#mc_tablaDetalle tbody').empty();
    $('#mc_emptyState').removeClass('d-none');
    mc_actualizarGranTotal();

    $('#modalCompra').modal('show');
    
    mc_recargarProductos();
    mc_cargarVendedores();
}

function mc_abrirModalNuevoCliente() {
    console.log("Abrir modal de cliente");
}

function mc_abrirModalProducto() {
    console.log("Abrir modal de producto");
}
</script>