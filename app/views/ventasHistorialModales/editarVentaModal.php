 <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
     rel="stylesheet" />
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<div class="modal fade" id="modalEditarCotizacion" tabindex="-1" aria-hidden="true">
    <!-- modal-fullscreen-xl-down o max-width amplia el modal en pantallas grandes -->
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content modal-content-custom   shadow-lg rounded-4 overflow-hidden">
            <form id="formEditarSolicitud">
                <input type="hidden" id="editar_venta_id" name="cotizacion_id" value="">

                <!-- Encabezado del Modal -->
                <div class="modal-header modal-header-gradient px-4 py-3  align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-box-header me-3 shadow-sm">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-white">Editar Venta #<span id="venta_id_titulo"></span></h4>
                            <p class="text-indigo-200 small mb-0 opacity-75">Modifique los datos y productos de la orden existente</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-4">
                    <!-- Controles / Filtros de Edición -->
                    <div class="card-filter-box p-4 mb-4 rounded-4  shadow-sm ">
                        <div class="row g-3 align-items-end">

                            <!-- 1. Almacén de Cargo -->
                            <div class="col-md-6 col-lg-3 min-w-0">
                                <label class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                    <i class="bi bi-box-seam me-1 text-indigo"></i> Almacén
                                </label>
                                <select name="almacen_id_editar" id="almacen_id_editar"
                                    class="form-select border-slate-200 rounded-3 shadow-none control-fixed-height w-100" required>
                                    <option value="">Seleccionar ubicación...</option>
                                    <?php foreach($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- 2. Cliente -->
                            <div class="col-md-6 col-lg-3 min-w-0">
                                <label class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                    <i class="bi bi-person me-1 text-indigo"></i> Cliente
                                </label>
                                <div class="input-group input-group-fixed flex-nowrap w-100">
                                    <select name="cliente_id_editar" id="cliente_id_editar"
                                        class="form-select select2-modal-editar border-slate-200" required>
                                        <option value="">Seleccionar cliente...</option>
                                        <?php foreach($clientes as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-action-secondary px-3 d-flex align-items-center justify-content-center flex-shrink-0" type="button"
                                        onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Vendedor -->
                            <div class="col-md-6 col-lg-3 min-w-0">
                                <div class="d-flex flex-column w-100">
    <label class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
        <i class="bi bi-person-badge me-1 text-indigo"></i> Vendedor
    </label>
    <select name="select-vendedor1" id="select-vendedor1"
        class="form-select select2-modal-editar border-slate-200 w-100" required>
        <option value="">Seleccionar vendedor...</option>
    </select>
</div>
                            </div>

                            <!-- 4. Añadir Producto -->
                            <div class="col-md-6 col-lg-3 min-w-0">
                                <label class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                    <i class="bi bi-search me-1 text-indigo"></i> Añadir Producto
                                </label>
                                <div class="input-group input-group-fixed flex-nowrap w-100">
                                    <select id="buscadorProductosEditar"
                                        class="form-select select2-modal-editar border-slate-200">
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
                                    <button type="button" class="btn btn-action-primary d-flex align-items-center justify-content-center px-3 flex-shrink-0"
                                        onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                        <i class="bi bi-plus-lg me-1"></i>
                                        <span class="fw-medium">Nuevo</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Tabla de Artículos -->
                    <div class="table-responsive border rounded-4  shadow-sm" style="max-height: 380px; overflow-y: auto;">
                        <table class="table align-middle mb-0 table-fixed" id="tablaDetalleEditar">
                            <thead>
                                <tr class="table-custom-header text-uppercase">
                                    <th class="ps-4" style="width: 28%;">Producto</th>
                                    <th style="width: 12%;">Cantidad</th>
                                    <th style="width: 18%;">Presentación</th>
                                    <th style="width: 18%;">Tipo Precio</th>
                                    <th style="width: 12%;">P. Unit</th>
                                    <th style="width: 12%;">TOTAL</th>
                                    <th style="width: 5%;" class="text-center pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            </tbody>
                        </table>

                        <!-- Estado Vacío -->
                        <div id="emptyStateEditar" class="text-center py-5 text-body-secondary">
                            <div class="mb-3">
                                <i class="bi bi-cart-x text-slate-300 opacity-50" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-semibold text-slate-600 mb-1">La lista está vacía</p>
                            <small class="text-slate-400">Utiliza el buscador superior para agregar productos a esta cotización</small>
                        </div>
                    </div>

                    <!-- Resumen del Total -->
                    <div class="d-flex justify-content-end align-items-center mt-4">
                        <div class="bg-dark bg-gradient text-white p-3 rounded-4 shadow-sm text-end px-4 min-w-200">
                            <small class="d-block text-white-50 fw-bold text-uppercase tracking-wider mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                                Total de Venta
                            </small>
                            <div id="costoTotalCompraEditar" class="fw-bolder text-warning" style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                            <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar">
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="modal-footer  p-4 bg-slate-50 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-action-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-check2-circle fs-5 me-2"></i> Actualizar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* 1. Ampliar el modal para aprovechar el ancho de pantalla */
@media (min-width: 1200px) {
    .modal-dialog-custom {
        max-width: 92% !important;
        min-height: 92% !important;
         /* Expande el modal horizontalmente */
    }
}

/* 2. Control de Altura Fija y Bloqueo de Ancho */
:root {
    --control-height: 42px;
    --border-color: #e2e8f0;
    --bg-input: #f8fafc;
    --primary-indigo: #4f46e5;
    --primary-indigo-hover: #4338ca;
}

.min-w-0 {
    min-width: 0 !important;
}

.input-group-fixed {
    width: 100% !important;
    max-width: 100% !important;
}

.input-group-fixed .select2-container {
    flex: 1 1 auto !important;
    width: 1% !important; /* Truco vital para que Flexbox congelé el ancho en Input Groups */
    min-width: 0 !important;
}

/* Tipografía de Labels */
.form-label-custom {
    font-size: 0.72rem !important;
    font-weight: 600 !important;
    color: #64748b !important;
    letter-spacing: 0.5px !important;
}

/* Control de Altura Fija Universal */
.control-fixed-height,
.card-filter-box .form-select,
.card-filter-box .input-group-fixed .btn {
    height: var(--control-height) !important;
}

.card-filter-box .form-select {
    background-color: var(--bg-input);
    border-color: var(--border-color);
    font-size: 0.875rem;
    color: #1e293b;
    max-width: 100% !important;
}

/* Select2 Fijo, Recortado con Ellipsis (...) */
.card-filter-box .select2-container--bootstrap-5 .select2-selection,
.card-filter-box .select2-container .select2-selection--single {
    height: var(--control-height) !important;
    background-color: var(--bg-input) !important;
    border-color: var(--border-color) !important;
    border-radius: 0.5rem !important;
    display: flex !important;
    align-items: center !important;
    font-size: 0.875rem !important;
    width: 100% !important;
    max-width: 100% !important;
}

.card-filter-box .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: calc(var(--control-height) - 2px) !important;
    color: #1e293b !important;
    padding-left: 0.75rem !important;
    padding-right: 1.75rem !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    width: 100% !important;
}

/* Bloqueo de Tabla para evitar desbordamientos */
.table-fixed {
    table-layout: fixed !important;
    width: 100% !important;
}

/* Botones */
.btn-action-primary {
    background-color: var(--primary-indigo);
    color: #ffffff;
    
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    font-size: 0.85rem;
}

.btn-action-primary:hover {
    background-color: var(--primary-indigo-hover);
    color: #ffffff;
}

.btn-action-secondary {
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid var(--border-color);
    border-left: none;
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.btn-action-secondary:hover {
    background-color: #e2e8f0;
    color: #1e293b;
}

.text-indigo {
    color: var(--primary-indigo) !important;
}
</style>
 <div class="modal fade" id="modalSaldoFavor" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content  shadow">

             <div class="modal-header ">
                 <h5 class="modal-title">
                     <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                     Se detectó una diferencia en el total de la venta
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <div class="modal-body">

                 <div class="alert alert-warning mb-4">
                     <strong>Total original:</strong>
                     <span id="txtTotalOriginal"></span><br>

                     <strong>Nuevo total:</strong>
                     <span id="txtNuevoTotal"></span><br>

                     <strong>Diferencia:</strong>
                     <span class="fw-bold text-success" id="txtDiferencia"></span>
                 </div>

                 <p class="mb-3">
                     ¿Qué deseas hacer con la diferencia generada?
                 </p>

                 <div class="list-group">

                     <button class="list-group-item list-group-item-action py-3" id="btnSaldoFavor">
                         <div class="d-flex align-items-center">
                             <i class="bi bi-piggy-bank-fill fs-2 text-success me-3"></i>

                             <div>
                                 <h6 class="mb-1">
                                     Aplicar como saldo a favor
                                 </h6>

                                 <small class="text-body-secondary">
                                     El importe quedará disponible para futuras compras del cliente.
                                 </small>
                             </div>
                         </div>
                     </button>

                     <button class="list-group-item list-group-item-action py-3" id="btnSoloEditar">
                         <div class="d-flex align-items-center">
                             <i class="bi bi-pencil-square fs-2 text-primary me-3"></i>

                             <div>
                                 <h6 class="mb-1">
                                     Actualizar la venta
                                 </h6>

                                 <small class="text-body-secondary">
                                     Se modifica la venta se regresara el monto excedente si lo hubiera o se
                                     incrementara la deuda si fuera el caso.
                                 </small>
                             </div>
                         </div>
                     </button>



                 </div>

             </div>

         </div>
     </div>
 </div>
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 <style>
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
    background: #212529;
    color: #fff;
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
    border-color: #212529 transparent transparent transparent;
}

.entregado-tooltip:hover .tooltip-custom {
    opacity: 1;
    visibility: visible;
}
 </style>
 <script>
const URL_CONTROLADOR_EDITAR = '/myvet/app/controllers/cotizacionesController.php';
let total_inicial = 0;
// =====================================================
// SELECT2 EDITAR
// =====================================================
$('.select2-modal-editar').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#modalEditarCotizacion')
});

// =====================================================
// CALCULAR TOTAL EDITAR
// =====================================================
let recalculandoFilaEditar = false;

async function cargarVendedores3(vendedor_id) {
    const select = document.getElementById('select-vendedor1');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {

            // Limpiamos el select y dejamos una opción inicial neutra
            // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario

                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;

                // Agregamos la opción al select
                select.appendChild(opcion);
            });
            $('#select-vendedor1').val(vendedor_id).trigger('change.select2');

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}

function calcularTotalSolEditar(input) {
    if (recalculandoFilaEditar) return;
    recalculandoFilaEditar = true;

    try {
        const fila = input.closest('tr');
        const cantidad = parseFloat(fila.querySelector('.cantidad-editar').value) || 0;
        const precioUnitarioOriginal = parseFloat(fila.querySelector('.precio-unitario-editar').value) || 0;
        const selectUnidad = fila.querySelector('.unidad-select-editar');
        const equivalencia = parseFloat(selectUnidad?.selectedOptions[0]?.dataset?.equivalencia) || 0;

        const precioUnitarioAjustado = precioUnitarioOriginal;
        const precioTotal = cantidad * precioUnitarioAjustado;

        fila.querySelector('.precio-total-editar').value = precioTotal.toFixed(2);
        let id = document.getElementById('editar_venta_id').value;
        console.log(id);

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

    } finally {
        recalculandoFilaEditar = false;
    }
}

// =====================================================
// RECARGAR PRODUCTOS EDITAR
// =====================================================
async function recargarProductosEditar() {
    try {
        const resp = await fetch(`/myvet/app/controllers/cotizacionesController.php?action=obtenerProductos`);
        const res = await resp.json();

        if (!res.success) {
            throw new Error(res.message);
        }

        const select = document.getElementById('buscadorProductosEditar');
        select.innerHTML = `<option value="">Escribe para buscar...</option>`;

        res.data.forEach(pr => {
            const option = document.createElement('option');
            option.value = pr.producto_id;
            option.dataset.nombre = pr.nombre;
            option.dataset.medidas = JSON.stringify(pr.medidas_adicionales || []);
            option.dataset.sku = pr.sku;
            option.dataset.um = pr.unidad_medida;
            option.dataset.ur = pr.unidad_reporte;
            option.dataset.preMin = pr.precio_minorista;
            option.dataset.preMat = pr.precio_mayorista;
            option.dataset.preDis = pr.precio_distribuidor;
            option.dataset.factor = pr.factor_conversion || 1;

            option.textContent = `[${pr.sku}] ${pr.nombre}`;
            select.appendChild(option);
        });

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'No se pudo actualizar la lista de productos', 'error');
    }
    $('#buscadorProductosEditar').trigger('change.select2');
}

// =====================================================
// EVENTO SELECT2: AGREGAR PRODUCTO A EDICIÓN
// =====================================================
$('#buscadorProductosEditar').on('select2:select', function(e) {
    const d = e.params.data.element.dataset;
    const id = $(this).val();
    console.log(d);

    // VALIDAR DUPLICADO EN TABLA DE EDICIÓN
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

    // AGREGAR FILA A TABLA EDITAR
    $('#tablaDetalleEditar tbody').append(`
    <tr id="filaEditar-${id}">
        <td class="ps-4">
            <b>${d.nombre}</b><br>
            <small class="text-body-secondary">${d.sku}</small>
        </td>

        <td>
            <input 
                type="number"
                name="itemsEditar[${id}][cant]"
                class="form-control cantidad-editar"
                step="0.01"
                value="0"
                min="0.01"
                required
               oninput="actualizarEquivalencia(this);calcularTotalSolEditar(this)">
                 <input 
                type="hidden"
                name="itemsEditar[${id}][equivalencia]"
                class="form-control equivalencia"
                step="0.01"
                value="0"
               
                >
        </td>

        <td>
            <select 
                name="itemsEditar[${id}][unidad]" 
                class="form-select unidad-select-editar"
                onchange="actualizarEquivalencia(this);calcularPrecioSugeridoEditar(this)">
                <option value="0" data-equivalencia="0" data-medida-id="0">Seleccione</option>
                ${opcionesUnidad}
            </select>
        </td>
        
        <td>
            <select 
                name="itemsEditar[${id}][tipoPrecio]" 
                class="form-select tipoPrecio-select-editar"
                onchange="calcularPrecioSugeridoEditar(this)">
                <option value="seleccionar" data-precio="0">seleccione</option>
                <option value="minorista" data-precio="${d.preMin}">Min ${d.preMin * d.factor} x ${d.ur}</option>
                <option value="mayorista" data-precio="${d.preMat}">May ${d.preMat * d.factor} x ${d.ur}</option>
                <option value="distribuidor" data-precio="${d.preDis}">Dis ${d.preDis * d.factor} x ${d.ur}</option>
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
                oninput="calcularTotalSolEditar(this)"
            >
        </td>

        <td style="min-width:160px;">
            <input 
                type="number"
                lang="en-US"
                name="itemsEditar[${id}][precio]"
                class="form-control precio-total-editar fw-bold text-success "
                step="0.01"
                min="0"
                placeholder="0.00"
                oninput="calcularTotalSolEditar(this)"
                style="font-size:1.1rem; height:45px; min-width:140px;"
            >
        </td>

        <td>
            <button type="button" class="btn btn-link text-danger" onclick="quitarFilaEditar(${id})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
    `);

    $(this).val(null).trigger('change');
});
function actualizarEquivalencia(input) {

    const fila = $(input).closest('tr');

    const cantidad = parseFloat(
        fila.find('.cantidad-editar').val()
    ) || 0;

    const equivalencia = parseFloat(
        fila.find('.unidad-select-editar option:selected').data('equivalencia')
    ) || 0;
    let cantidadTotal=(cantidad / equivalencia).toFixed(2);
fila.find('.equivalencia').val(cantidadTotal);
   
}
// =====================================================
// CALCULAR PRECIO SUGERIDO EDITAR
// =====================================================
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

    const sugerido = precioBase / equivalencia;
    inputPrecio.value = sugerido.toFixed(2);
    inputtotal.value = sugerido.toFixed(2);
    let totalCompraEditar = 0;
    document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
        totalCompraEditar += parseFloat(el.value) || 0;
    });

    document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });
    document.getElementById('totalCotizacionEditar').value = totalCompraEditar;

}

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('precio-unitario-editar')) {
        e.target.dataset.editado = "1";
    }
});

// =====================================================
// GUARDAR ACTUALIZACIÓN (SUBMIT FORM)
// =====================================================
$('#formEditarSolicitud').on('submit', async function(e) {
    e.preventDefault();

    if (!$('#tablaDetalleEditar tbody tr').length) {
        Swal.fire('Error', 'Agregue productos', 'warning');
        return;
    }

    const payload = {
        venta_id: $('#editar_venta_id').val(), // ID de la fila a actualizar
        almacen_id: $('#almacen_id_editar').val(),
        id_cliente: $('#cliente_id_editar').val(),
        vendedor: $('#select-vendedor1').val(),
        nuevo_total: $('#totalCotizacionEditar').val(),
        productos: []
    };

    $('#tablaDetalleEditar tbody tr').each(function() {
        const fila = $(this);
        const id = fila.attr('id').replace('filaEditar-', '');


        const unidadSelect = fila.find('.unidad-select-editar option:selected');
        const tipoPrecioSelect = fila.find('.tipoPrecio-select-editar option:selected');
        let cantidadInicial = fila.find('.cantidad-editar').val();
        let equivalencia = fila.find('.equivalencia').val();
        let cantidadTotal=0;
        let cantidadT=(cantidadInicial * equivalencia);
        if (cantidadT % 1 > 0) {
  cantidadTotal=cantidadT.toFixed(2);
} else {
    cantidadTotal=cantidadT;
}
       

        payload.productos.push({
            producto_id: id,

            cantidad:cantidadTotal,
            unidad: unidadSelect.val(),
            noEliminar: fila.find('.noEliminar').val(),
            unidad_id: unidadSelect.data('medida-id'),
            equivalencia: unidadSelect.data('equivalencia'),
            tipoPrecio: tipoPrecioSelect.val(),
            precio_unitario: fila.find('.precio-unitario-editar').val(),
            precio: fila.find('.precio-total-editar').val()
        });
        console.log(fila.find('.noEliminar').val());
    });

    console.log('JSON ENVIADO EDICIÓN:', payload);

    Swal.fire({
        title: 'Actualizando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {

        // Se envía a la acción de actualizar/editar en tu controlador
        const resp = await fetch(
            `/myvet/app/controllers/editarVentaController.php?action=guardarEdicion`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
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
            getVentas();
             
            console.log($('#totalCotizacionEditar').val());
            let nuevoTotal=$('#totalCotizacionEditar').val();
            if(total_inicial>nuevoTotal)
            {
            mostrarModalSaldo();
}
           bootstrap.Modal.getInstance(document.getElementById('modalEditarCotizacion')).hide();
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión', 'error');
    }
});


// =====================================================
// ELIMINAR FILA EDITAR
// =====================================================
function quitarFilaEditar(id) {
    $(`#filaEditar-${id}`).remove();

    if (!$('#tablaDetalleEditar tbody tr').length) {
        $('#emptyStateEditar').removeClass('d-none');
    }

    // Forzar el recalculo global tras eliminar fila
    let totalCompraEditar = 0;
    document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
        totalCompraEditar += parseFloat(el.value) || 0;
    });
    document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });
    document.getElementById('totalCotizacionEditar').value = totalCompraEditar;
}

// =====================================================
// INICIALIZAR MODAL EDITAR CON DATOS DE LA DB
// ========
let cliente_nombre = '';
async function gestionarSolicitud(id) {
    try {
        // 1. Limpiar la tabla de edición por si tenía datos anteriores
        $('#tablaDetalleEditar tbody').empty();
        $('#formEditarSolicitud')[0].reset();

        console.log('Cargando cotización ID:', id);

        // 2. Consultar el detalle al controlador
        const resp = await fetch(
            `/myvet/app/controllers/editarVentaController.php?action=obtenerDetalle&id=${id}`);
        const datos = await resp.json();
        const data = datos.info;
        const dataProductos = datos.productos;

        console.log('DATOS RECUPERADOS:', datos);



        // 3. Setear datos de cabecera usando el primer elemento del array
        const infoBase = data;

        $('#editar_venta_id').val(infoBase.id);
        $('#venta_id_titulo').text(infoBase.id);
        $('#almacen_id_editar').val(infoBase.almacen_id);
        $('#cliente_id_editar').val(infoBase.id_cliente).trigger('change.select2');
        cliente_nombre = infoBase.nombre_comercial;
        cargarVendedores3(infoBase.vendedor_id);
        total_inicial = infoBase.total_pagado;

        // Ocultar el estado vacío porque vamos a meter filas
        $('#emptyStateEditar').addClass('d-none');

        // 4. Recorrer los productos e inyectarlos como filas interactivas
        dataProductos.forEach(i => {
            let cantidadTotal=0;
        let cantidadT=((i.cantidad)*i.equivalencia);
        if (cantidadT % 1 > 0) {
  cantidadTotal=cantidadT.toFixed(2);
} else {
    cantidadTotal=cantidadT;
}
let minimo=(i.cantidad_entregada*i.equivalencia).toFixed(2);
            const prodId = i.producto_id;
            let quitar = i.cantidad_entregada > 0 ?
                `<span class="entregado-tooltip">
            <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
            <span class="tooltip-custom">
                Ya se entregaron <strong>${i.cantidad_entregada * i.equivalencia}</strong> unidades.
                No es posible eliminar este producto.
            </span>
       </span>` :
                `<button type="button"
            class="btn btn-link text-danger p-0"
            onclick="quitarFilaEditar(${prodId})">
            <i class="bi bi-trash fs-5"></i>
       </button>`;

            // Reconstruimos las opciones de unidad/medida basándonos en la actual de la BD
            // Nota: Si manejas más medidas adicionales dinámicas, aquí puedes añadir la lógica de tu data-medidas.
            let opcionesUnidadHtml = `
                <option 
                    value="${i.unidadMedida}" 
                    data-equivalencia="${i.equivalencia}" 
                    data-medida-id="${i.unidadMedida}" 
                    selected>
                    ${i.nombre}
                </option>
            `;

            // Estructura HTML idéntica a la generada por el Select2 de búsqueda
            const nuevaFilaHtml = `
            <tr id="filaEditar-${prodId}">
                <td class="ps-4">
                    <b>${i.producto}</b><br>
                    <small class="text-body-secondary">${i.sku}</small>
                </td>

                <td>
                    <input 
                        type="number"
                        name="itemsEditar[${prodId}][cant]"
                        class="form-control cantidad-editar"
                        step="0.01"
                        value="${parseFloat(cantidadTotal)}"
                        min="${minimo}"
                        required
                        oninput="calcularTotalSolEditar(this)">

                        <input 
                        type="hidden"
                        name="itemsEditar[${prodId}][noEliminar]"
                        class="form-control noEliminar"
                     
                        value="${i.cantidad_entregada>0?i.id:0}"
                      
                        required
                       >
                       <input 
                type="hidden"
                name="itemsEditar[${id}][equivalencia]"
                class="form-control equivalencia"
                step="0.01"
                value="${1/(i.equivalencia)}"
                min="0.01"
                required
                >
                </td>

                <td>
                    <select 
                        name="itemsEditar[${prodId}][unidad]" 
                        class="form-select unidad-select-editar"
                        onchange="calcularPrecioSugeridoEditar(this)">
                        ${opcionesUnidadHtml}
                    </select>
                </td>
                
                <td>
                    <select 
                        name="itemsEditar[${prodId}][tipoPrecio]" 
                        class="form-select tipoPrecio-select-editar"
                        id="tipoPrecio_editar_${prodId}"
                        onchange="calcularPrecioSugeridoEditar(this)">
                        <option value="seleccionar" data-precio="0">seleccione</option>
                        
                        <option value="minorista" data-precio="${i.precio_unitario}">
                            Min ${parseFloat(i.precio_unitario) * parseFloat(i.factor_conversion || 1)} x ${i.unidad_reporte}
                        </option>
                    </select>
                </td>

                <td>
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${prodId}][precioUnitario]"
                        class="form-control precio-unitario-editar"
                        step="0.01"
                        min="0"
                        value="${parseFloat(i.precio_unitario).toFixed(2)}"
                        required
                        oninput="calcularTotalSolEditar(this)"
                    >
                </td>

                <td style="min-width:160px;">
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${prodId}][precio]"
                        class="form-control precio-total-editar fw-bold text-success "
                        step="0.01"
                        min="0"
                        value="${parseFloat(i.subtotal).toFixed(2)}"
                        readonly
                        style="font-size:1.1rem; height:45px; min-width:140px;"
                    >
                </td>

                <td>
                   ${quitar}
                </td>
            </tr>
            `;

            // Insertar la fila en el tbody de edición
            $('#tablaDetalleEditar tbody').append(nuevaFilaHtml);

            // Pre-seleccionar el tipo de precio guardado en la Base de Datos ('minorista', 'mayorista', etc.)
            $(`#tipoPrecio_editar_${prodId}`).val(i.tipo_precio);
        });

        // 5. Forzar el recálculo general del costo total de compra basándonos en las nuevas filas
        let totalCompraEditar = 0;
        document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
            totalCompraEditar += parseFloat(el.value) || 0;
        });

        document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
        });
        document.getElementById('totalCotizacionEditar').value = totalCompraEditar;

        // 6. Cargar buscador interno del modal y finalmente abrir el modal de edición independiente
        await recargarProductosEditar();

        new bootstrap.Modal(
            document.getElementById('modalEditarCotizacion')
        ).show();

    } catch (e) {
        console.error('Error al gestionar/mapear la solicitud:', e);
        Swal.fire('Error', 'No se pudo estructurar el editor de la cotización', 'error');
    }
}
let dataEdicion = null;

function mostrarModalSaldo() {
    let id = $('#editar_venta_id').val();
    let total = $('#totalCotizacionEditar').val();


    const diferencia = total_inicial - total;

    $('#txtTotalOriginal').text(total_inicial);
    $('#txtNuevoTotal').text(total);
    $('#txtDiferencia').text(diferencia);

    new bootstrap.Modal(document.getElementById('modalSaldoFavor')).show();
}
$('#btnSaldoFavor').click(async function () {

    bootstrap.Modal.getInstance(document.getElementById('modalSaldoFavor')).hide();

    try {

        const id = $('#editar_venta_id').val();
        const cliente = $('#cliente_id_editar').val();
        const total = parseFloat($('#totalCotizacionEditar').val());

        const diferencia = total_inicial - total;

        const fd = new FormData();
        fd.append('venta_id', id);
        fd.append('cliente_id', cliente);
        fd.append('diferencia', diferencia);

        const resp = await fetch(
            '/myvet/app/controllers/editarVentaController.php?action=guardarComoABono',
            {
                method: 'POST',
                body: fd
            }
        );

        const res = await resp.json();

        if (!res.success && res.status !== 'success') {
            throw new Error(res.message);
        }

        Swal.fire({
            icon: 'success',
            title: 'Saldo aplicado',
            text: res.message || 'El saldo a favor fue registrado correctamente.'
        });

    } catch (e) {

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: e.message
        });

    }

});
async function guardarComoGastoSalidadeDinero() {

    try {

        const respFolio = await fetch('/myvet/app/controllers/egresosController.php?action=getSiguienteFolioGasto');
        const folioRes = await respFolio.json();

        if (!folioRes.success) {
            throw new Error("No fue posible obtener el folio.");
        }

        const folio = folioRes.folio;

        const id = $('#editar_venta_id').val();
        const total = parseFloat($('#totalCotizacionEditar').val());
        const diferencia = total_inicial - total;

        const observaciones = `SALIDA DE DINERO POR EDICIÓN DE VENTA ${id}`;

        const fecha = new Date().toISOString().split('T')[0];

        const fd = new FormData();

        fd.append('folio', folio);
        fd.append('fecha', fecha);
        fd.append('categoria_id', 2);
        fd.append('beneficiario', cliente_nombre);
        fd.append('metodo_pago', 'Efectivo');
        fd.append('total_final', diferencia);
        fd.append('observaciones', observaciones);

        if (typeof documento !== "undefined" && documento) {
            fd.append('documento', documento);
        }

        const resp = await fetch(
            '/myvet/app/controllers/egresosController.php?action=guardarGasto',
            {
                method: 'POST',
                body: fd
            }
        );

        const res = await resp.json();

        if (!res.success) {
            throw new Error(res.message);
        }

        Swal.fire({
            icon: 'success',
            title: 'Gasto registrado',
            text: res.message
        });

    } catch (e) {

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: e.message
        });

    }

}
$('#btnSoloEditar').click(async function () {

    bootstrap.Modal.getInstance(document.getElementById('modalSaldoFavor')).hide();

    const total = parseFloat($('#totalCotizacionEditar').val());
    const diferencia = total_inicial - total;

    if (diferencia > 0) {

        const confirmar = await Swal.fire({
            icon: 'warning',
            title: '¿Registrar salida de dinero?',
            text: `Se registrará un gasto por $${diferencia.toFixed(2)}.`,
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'No'
        });

        if (confirmar.isConfirmed) {
            await guardarComoGastoSalidadeDinero();
        }

    }

  

});
 </script>