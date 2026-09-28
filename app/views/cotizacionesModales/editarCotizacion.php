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
                            <label for="almacen_id_editar" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select name="almacen_id_editar" id="almacen_id_editar"
                                class="form-select border-slate-200 control-fixed-height w-100 shadow-none" required>
                                <option value="">Seleccionar ubicación...</option>
                                <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Cliente -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cliente_id_editar" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person me-1 text-primary"></i> Cliente
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select name="cliente_id_editar" id="cliente_id_editar"
                                    class="form-select select2-modal-editar border-slate-200" required>
                                    <option value="">Seleccionar cliente...</option>
                                  
                                </select>
                                <button class="btn btn-outline-primary px-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    type="button" onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Vendedor -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="select-vendedor1" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                            </label>
                            <select name="select-vendedor1" id="select-vendedor1"
                                class="form-select select2-modal-editar border-slate-200 w-100" required>
                                <option value="">Seleccionar vendedor...</option>
                            </select>
                        </div>

                        <!-- 4. Añadir Producto -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="buscadorProductosEditar" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="buscadorProductosEditar" class="form-select select2-modal-editar border-slate-200">
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
                            <small class="d-block text-white-50 fw-bold text-uppercase tracking-wider mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                                Total Cotización
                            </small>
                            <div id="costoTotalCompraEditar" class="fw-bolder text-warning" style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                            <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 p-4  d-flex justify-content-between align-items-center" id="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center"
                            onclick="procederPago($('#totalCotizacionEditar').val(), $('#editar_cotizacion_id').val())">
                            <i class="bi bi-cart-check me-2"></i> Convertir a Venta
                        </button>

                        <button type="submit" class="btn btn-warning rounded-pill px-4 py-2.5 fw-bold shadow-sm card-title-text d-flex align-items-center">
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
 document.addEventListener('DOMContentLoaded', function() {
        const selectAlmacen = document.getElementById('almacen_id_editar');

        if (selectAlmacen) {
            selectAlmacen.addEventListener('change', function(e) {
                const almacenId = this.value; // ID del almacén seleccionado
                const textoSeleccionado = this.options[this.selectedIndex].text; // Nombre del almacén

                if (almacenId) {
                    console.log(`Almacén cambiado a ID: ${almacenId} - ${textoSeleccionado}`);
                    recargarProductosEditar();
                    const id = $('#almacen_id_editar').val();
                   

                    cargarClienteseditar();

                    // 🚀 Coloca aquí la función o lógica que deseas ejecutar
                    // Ejemplo: cargarProductosPorAlmacen(almacenId);
                } else {
                    console.log('Se deseleccionó el almacén');
                     cargarClienteseditar();
                }
            });
        }
    });
 
      
async function cargarClienteseditar() {
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

            // Llenamos el select únicamente con la lista filtrada
            clientesFiltrados.forEach(cliente => {
                const opcion = document.createElement('option');
                opcion.value = cliente.id;
                opcion.textContent = `${cliente.nombre_comercial}`;
                select.appendChild(opcion);
            });
$('#cliente_id_editar').val(cliente_id_edi).trigger('change.select2');;
        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarClientes:', error);
    }
}
  document.addEventListener('DOMContentLoaded', () => {
       
        cargarClienteseditar();
    });

async function cargarVendedores3(vendedor_id) {
      const id_almacen = $('#almacen_id_editar').val();
    const select = document.getElementById('select-vendedor1');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${id_almacen}`;
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
        let id = document.getElementById('editar_cotizacion_id').value;
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
       

        // 3. Intentamos convertir a JSON
        let res;
        try {
            res = JSON.parse(textoServidor);
            console.log(res);
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
   
// =====================================================
// EVENTO SELECT2: AGREGAR PRODUCTO A EDICIÓN
// =====================================================
$('#buscadorProductosEditar').on('select2:select', function(e) {
    const d = e.params.data.element.dataset;
    const id = $(this).val();

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
                oninput="calcularTotalSolEditar(this)">
        </td>

        <td>
            <select 
                name="itemsEditar[${id}][unidad]" 
                class="form-select unidad-select-editar"
                onchange="calcularPrecioSugeridoEditar(this)">
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
        cotizacion_id: $('#editar_cotizacion_id').val(), // ID de la fila a actualizar
        almacen_id: $('#almacen_id_editar').val(),
        cliente_id: $('#cliente_id_editar').val(),
        vendedor: $('#select-vendedor1').val(),
        totalCotizacion: $('#totalCotizacionEditar').val(),
        items: []
    };

    $('#tablaDetalleEditar tbody tr').each(function() {
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

    console.log('JSON ENVIADO EDICIÓN:', payload);

    Swal.fire({
        title: 'Actualizando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // Se envía a la acción de actualizar/editar en tu controlador
        const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=actualizar`, {
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
            location.reload();
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
let cliente_id_edi=0
async function gestionarSolicitud(id) {
    try {
        // 1. Limpiar la tabla de edición por si tenía datos anteriores
        $('#tablaDetalleEditar tbody').empty();
        $('#formEditarSolicitud')[0].reset();

        console.log('Cargando cotización ID:', id);

        // 2. Consultar el detalle al controlador
        const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
        const datos = await resp.json();
        const data = datos.data;

        console.log('DATOS RECUPERADOS:', data);

        if (!Array.isArray(data) || data.length === 0) {
            Swal.fire('Error', 'No se encontraron productos en esta cotización', 'warning');
            return;
        }

        // 3. Setear datos de cabecera usando el primer elemento del array
        const infoBase = data[0];

        $('#editar_cotizacion_id').val(infoBase.cotizacion_id);
        $('#almacen_id_editar').val(infoBase.almacen_origen_id);
        cliente_id_edi=infoBase.cliente_id;
        $('#cliente_id_editar').val(infoBase.cliente_id).trigger('change.select2');
        cargarVendedores3(infoBase.vendedor_id);
        cargarClienteseditar();

        // Ocultar el estado vacío porque vamos a meter filas
        $('#emptyStateEditar').addClass('d-none');

        // 4. Recorrer los productos e inyectarlos como filas interactivas
        data.forEach(i => {
            const prodId = i.producto_id;

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
                    <b>${i.producto_nombre}</b><br>
                    <small class="text-body-secondary">${i.sku}</small>
                </td>

                <td>
                    <input 
                        type="number"
                        name="itemsEditar[${prodId}][cant]"
                        class="form-control cantidad-editar"
                        step="0.01"
                        value="${parseFloat(i.cantidad)}"
                        min="0.01"
                        required
                        oninput="calcularTotalSolEditar(this)">
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
                    <button type="button" class="btn btn-link text-danger" onclick="quitarFilaEditar(${prodId})">
                        <i class="bi bi-trash"></i>
                    </button>
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

async function procederPago(total, id) {
    cargarUsuariosSelectPago();
    let nuevo = [];

    totalGlobalPago = total;

    const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
    const datos = await resp.json();

    let data = datos.data;

    let html = '';

    data.forEach((i, index) => {

        const cantidad = parseFloat(i.cantidad) || 0;
        canti = cantidad / i.equivalencia;
        data[index].cantidadR = parseFloat(canti);
        data[index].entrega_hoy = 0;
        document.getElementById('montoPago').value = data[index].total;
        datost = data;



        html += `
        <tr>
          

           

            <td class="text-center">
                <input 
                    type="hidden"
                    step="0.01"
                    value="0"
                    max="${i.entregar_hoy}"
                    class="form-control entrega-hoy"
                    data-index="${index}"
                >
            </td>
        </tr>`;
    });

    $('#print-productos').html(html);

    // actualizar entrega_hoy
    document.querySelectorAll('.entrega-hoy').forEach(input => {

        input.addEventListener('input', function() {

            const index = this.dataset.index;



            data[index].entrega_hoy = parseFloat((this.value * (1 / data[index].equivalencia))) ||
            0;
            data[index].monto_pagado = parseFloat(
                document.getElementById('montoPago').value
            ) || 0;

            datost = data;
            console.log("datos", datost);
            console.log(data);
        });

    });

    // actualizar monto pagado en TODO el arreglo
    document.getElementById('montoPago').addEventListener('input', function() {

        const monto = parseFloat(this.value) || 0;

        data.forEach(item => {
            item.monto_pagado = monto;
        });
        datos = data;

        console.log(datost);
    });




    const htmlboton = `<button class="btn btn-light w-50 rounded-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button class="btn w-50 text-white rounded-3" style="background: #334155;"
                      onclick='convertirToCompra(${JSON.stringify(datost)}, ${id})'">
                        Confirmar
                    </button>;`
    document.getElementById('pagoTotal').textContent =
        total.toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
        });

    document.getElementById('montoPago').value = total;
    document.getElementById('idC').value = id;

    // opcional: guardarlo para usarlo al enviar pago
    window.detallePago = data;

    const modal = new bootstrap.Modal(
        document.getElementById('modalPago')
    );
    $('#boton').html(htmlboton);
    modal.show();
}

document.getElementById('metodoPago').addEventListener('change', function() {
    // 1. Validamos si el método seleccionado requiere caja de referencia
    const requiere = ['transferencia', 'tarjeta', 'deposito'].includes(this.value);

    // 2. Corregido: Condicional IF correcto usando el valor de 'this.value'
    if ($(this).find(':selected').data('metodo') === 'credito') {
        $('#montoPago').val(0);
        $('#montoPago').prop('disabled', true);
    } else {
        // Buena práctica: Si cambian de opinión y eligen otro método, 
        // volvemos a habilitar el campo de monto.
        $('#montoPago').prop('disabled', false);
    }

    // 3. Mostrar u ocultar la caja de referencia (utiliza vanilla JS como tu listener)
    document.getElementById('refBox').classList.toggle('d-none', !requiere);
});
async function convertirToCompra(data, id) {
    try {
        console.log('data 1', datost);

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

        const resp = await fetch(`${URL_CONTROLADOR}?action=guardar_venta&id=${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const datos = await resp.json();
        console.log(datos);

        if (datos.status === 'success') {

            Swal.fire({
                icon: 'success',
                title: 'Venta guardada',
                text: `Folio: ${datos.folio || ''}`,
                confirmButtonText: 'OK'
            }).then(() => location.reload());

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
}
</script>