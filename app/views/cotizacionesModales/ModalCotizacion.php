<div class="modal fade" id="modalCotizacion" tabindex="-1" aria-hidden="true">
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
                            <h4 class="fw-bold mb-0">Nueva Cotización</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para requerir materiales al almacén</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <!-- Fila de Controles -->
                    <div class="row g-3 mb-4 p-4 rounded-4  shadow-sm align-items-end border">
                        
                        <!-- 1. Almacén de Cargo -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="almacen_id" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select name="almacen_id" id="almacen_id" 
                                class="form-select border-slate-200 control-fixed-height w-100 shadow-none" required onchange="recargarProductos()">
                               
                                <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Cliente -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cliente_id" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person me-1 text-primary"></i> Cliente
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select name="cliente_id" id="cliente_id" 
                                    class="form-select select2-modal border-slate-200" required>
                                  
                                   
                                </select>
                                <button class="btn btn-outline-primary px-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                                    type="button" onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Vendedor -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="vendedor-select" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                            </label>
                            <select class="form-select select2-modal border-slate-200 w-100" id="vendedor-select" name="usuario_id3" required>
                                <option value="">Seleccione vendedor</option>
                            </select>
                        </div>

                        <!-- 4. Añadir Producto -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="buscadorProductos" class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="buscadorProductos" class="form-select select2-modal border-slate-200">
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

                    <!-- Tabla de Detalle -->
                   <div class="table-responsive border rounded-4 ">
                        <table class="table align-middle mb-0" id="tablaDetalle">
                            <thead class="">
                                <tr class="text-body-secondary small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 25%;">Presentación / Unidad</th>
                                    <th style="width: 25%;">Tipo de precio</th>
                                    <th  class="ps-4">Precio Unitario</th>
                                    <th style="width: 50%";>TOTAL</th>
                                    <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>

                        <div id="emptyState" class="text-center py-5 text-body-secondary">
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
                            <div id="costoTotalCompra" class="fw-bolder text-success" style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                            <input type="hidden" id="totalCotizacion" name="totalCotizacion">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer  p-4  d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm d-flex align-items-center">
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

/* Forzar anchos de Input Group y Select2 */
.input-group-fixed {
    width: 100% !important;
    max-width: 100% !important;
}

.input-group-fixed .select2-container {
    flex: 1 1 auto !important;
    width: 1% !important;
    min-width: 0 !important;
}

/* Control de altura universal */
.control-fixed-height,
.modal-body .form-select,
.modal-body .input-group-fixed .btn {
    height: var(--control-height) !important;
}

/* Reglas estrictas Select2 para evitar desbordamientos */
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

/* Tabla Fija */
.table-fixed {
    table-layout: fixed !important;
    width: 100% !important;
}
</style>

<script>
const URL_CONTROLADOR = '/myvet/app/controllers/cotizacionesController.php';
   document.addEventListener('DOMContentLoaded', function() {
        const selectAlmacen = document.getElementById('almacen_id');
         recargarProductos();

        if (selectAlmacen) {
            selectAlmacen.addEventListener('change', function(e) {
                const almacenId = this.value; // ID del almacén seleccionado
                const textoSeleccionado = this.options[this.selectedIndex].text; // Nombre del almacén

                if (almacenId) {
                    console.log(`Almacén cambiado a ID: ${almacenId} - ${textoSeleccionado}`);
                    
                    const id = $('#almacen_id').val();
                   recargarProductos();

                    cargarClientes();

                    // 🚀 Coloca aquí la función o lógica que deseas ejecutar
                    // Ejemplo: cargarProductosPorAlmacen(almacenId);
                } else {
                    console.log('Se deseleccionó el almacén');
                     recargarProductos();
                }

            });
        }
    });
 
        async function cargarClientes() {
    console.log("cargo clientes");
    
    // Obtenemos el ID del almacén actual
    const almacenId = $('#almacen_id').val();
    const select = document.getElementById('cliente_id');
    if (!select) return;

    // Limpiamos el select antes de poblarlo
    select.innerHTML = '';

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

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarClientes:', error);
    }
}
  document.addEventListener('DOMContentLoaded', () => {
       
        cargarClientes();
    });

// =====================================================
// SELECT2
// =====================================================

$('.select2-modal').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#modalCotizacion')
});

// =====================================================
// CALCULAR TOTAL
// =====================================================

    async function cargarVendedores() {
        const almacen = document.getElementById('almacen_id').value;
    const select = document.getElementById('vendedor-select');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacen}`;
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

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}

// 🔥 EVITAR LOOPS
let recalculandoFila = false;
let totaLCompra;

function calcularTotalSol(input) {

    if (recalculandoFila) return;

    recalculandoFila = true;

    try {

        const fila = input.closest('tr');

        const cantidad = parseFloat(
            fila.querySelector('.cantidad').value
        ) || 0;

        const precioUnitarioOriginal = parseFloat(
            fila.querySelector('.precio-unitario').value
        ) || 0;

        // ✅ obtener select correcto
        const selectUnidad = fila.querySelector('.unidad-select');

        // ✅ obtener equivalencia del option seleccionado
        const equivalencia = parseFloat(
            selectUnidad?.selectedOptions[0]?.dataset?.equivalencia
        ) || 0;

        console.log('equivalencia:', equivalencia);

        // 🔥 APLICAR equivalencia al precio
        const precioUnitarioAjustado =
            precioUnitarioOriginal;

        // 🔥 TOTAL
        const precioTotal = (cantidad) * precioUnitarioAjustado;

        console.log('precio ajustado:', precioUnitarioAjustado);
        console.log('total:', precioTotal);

        fila.querySelector('.precio-total').value =
            precioTotal.toFixed(2);

        // =====================================
        // SUMA GENERAL
        // =====================================

        let totaLCompra = 0;

        document.querySelectorAll('.precio-total')
            .forEach(el => {
                totaLCompra += parseFloat(el.value) || 0;
            });

        document.getElementById('costoTotalCompra')
            .textContent = totaLCompra.toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });
             document.getElementById('totalCotizacion').value=totaLCompra;

    } finally {
        recalculandoFila = false;
    }
}

// =====================================================
// AGREGAR PRODUCTO
// =====================================================
 async function recargarProductos() {
        const id = $('#almacen_id').val();
        const $select = $('#buscadorProductos');

        if (!id) return;

        const url = `/myvet/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${id}`;
        console.log("Consultando URL:", url);

        // 1. Hacemos la petición
        const resp = await fetch(url);

        // 2. Leemos la respuesta como TEXTO plano primero para ver si hay errores de PHP
        const textoServidor = await resp.text();
        console.log("=== RESPUESTA CRUDA DEL SERVIDOR ===");
        console.log(resp);

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
  $('#buscadorProductos').on('select2:select', function(e) {

    const d = e.params.data.element.dataset;

    const id = $(this).val();

    // VALIDAR DUPLICADO
    if ($(`#fila-${id}`).length) {

        Swal.fire(
            'Aviso',
            'El producto ya está en la lista',
            'info'
        );

        return;
    }

    $('#emptyState').addClass('d-none');
    const medidas = JSON.parse(d.medidas || '[]');

    let opcionesUnidad = ``;
    console.log(medidas);
    medidas.forEach(m => {

        opcionesUnidad += `
   
    <option 
        value="${m.id}"
        data-equivalencia="${m.equivalencia}"
        data-medida-id="${m.id}"
    >
        ${m.nombre}
    </option>

    `;
    });

    // AGREGAR FILA
    $('#tablaDetalle tbody').append(`

<tr id="fila-${id}">

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
            value="0"
            min="0.01"
            required
            oninput="calcularTotalSol(this)">
        
    </td>

    <!-- UNIDAD -->
    <td>
        <select 
            name="items[${id}][unidad]" 
            class="form-select unidad-select unidad"
            onchange="calcularPreciosugerido(this)">
           <option 
    value="0"
    data-equivalencia="0"
    data-medida-id="0">
    Seleccione
    </option>
            ${opcionesUnidad}
        </select>
        
    </td>
    
<td>
    <select 
        name="items[${id}][tipoPrecio]" 
        class="form-select tipoPrecio-select tipoPrecio"
        onchange="calcularPreciosugerido(this)"
    > <option value="seleccionar" data-precio="0">
          seleccione
        </option>
        <option value="minorista" data-precio="${d.preMin }">
            Min ${d.preMin * d.factor} x ${d.ur}
        </option>

        <option value="mayorista" data-precio="${d.preMat }">
            May ${d.preMat* d.factor} x ${d.ur}
        </option>

        <option value="distribuidor" data-precio="${d.preDis }">
            Dis ${d.preDis* d.factor} x ${d.ur}
        </option>
    </select>
</td>
    <!-- COSTO UNITARIO -->
    <td>
        <input 
            type="number"
            lang="en-US"
            name="items[${id}][precioUnitario]"
            class="form-control precio-unitario precio_unitario"
            step="0.01"
            
            min="0"
            placeholder="0.00"
            required
            oninput="calcularTotalSol(this)"
        >
    </td>

    <!-- COSTO TOTAL -->
    <td style="min-width:160px;">
        <input 
            type="number"
            lang="en-US"
            name="items[${id}][precio]"
            class="form-control precio-total fw-bold text-success "
            step="0.01"
            min="0"
            placeholder="0.00"
            oninput="calcularTotalSol(this)"
            style="
                font-size:1.1rem;
                height:45px;
                min-width:140px;
            "
        >
    </td>

    <!-- ELIMINAR -->
    <td>
        <button 
            type="button"
            class="btn btn-link text-danger"
            onclick="quitarFila(${id})"
        >
            <i class="bi bi-trash"></i>
        </button>
    </td>

</tr>
`);
    // LIMPIAR SELECT
    $(this).val(null).trigger('change');
});

function calcularPreciosugerido(select) {

    const fila = select.closest('tr');

    const inputPrecio = fila.querySelector('.precio-unitario');
const inputtotal = fila.querySelector('.precio-total');

    const unidadSelect = fila.querySelector('.unidad-select');
    const tipoSelect = fila.querySelector('.tipoPrecio-select');

    const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
    const tipoOption = tipoSelect.options[tipoSelect.selectedIndex]??0;

    const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
    const precioBase = Number(tipoOption?.dataset.precio || 0)??0;

    const sugerido = (precioBase / equivalencia)??0;

    // SOLO PLACEHOLDER
    inputPrecio.value = sugerido.toFixed(2);
    inputtotal.value = sugerido.toFixed(2);
    let totaLCompra = 0;

        document.querySelectorAll('.precio-total')
            .forEach(el => {
                totaLCompra += parseFloat(el.value) || 0;
            });

        document.getElementById('costoTotalCompra')
            .textContent = totaLCompra.toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });
             document.getElementById('totalCotizacion').value=totaLCompra;

}
document.addEventListener('input', function(e) {

    if (e.target.classList.contains('precio-unitario')) {
        e.target.dataset.editado = "1";
    }
});
// =====================================================
// GUARDAR SOLICITUD
// =====================================================
// // =====================================================
// CONVERTIR A COMPRA
// =====================================================

$('#formSolicitud').on('submit', async function(e) {

    e.preventDefault();

    if (!$('#tablaDetalle tbody tr').length) {
        Swal.fire('Error', 'Agregue productos', 'warning');
        return;
    }

   const payload = {
    almacen_id: $('#almacen_id').val(),
    cliente_id: $('#cliente_id').val(),
    totalCotizacion: $('#totalCotizacion').val(),
    vendedor: $('#vendedor-select').val(),
    items: []
};
    $('#tablaDetalle tbody tr').each(function() {

        const fila = $(this);
        const id = fila.attr('id').replace('fila-', '');

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

            precio: fila.find('.precio-total').val()
        });
    });

    console.log('JSON ENVIADO:', payload);

    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {

        const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const res = await resp.json();

        console.log('RESPUESTA:', res);

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
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión', 'error');
    }
});
function quitarFila(id) {

    $(`#fila-${id}`).remove();
    let totaLCompra = 0;

        document.querySelectorAll('.precio-total')
            .forEach(el => {
                totaLCompra += parseFloat(el.value) || 0;
            });

        document.getElementById('costoTotalCompra')
            .textContent = totaLCompra.toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });
             document.getElementById('totalCotizacion').value=totaLCompra;


    if (!$('#tablaDetalle tbody tr').length) {

        $('#emptyState').removeClass('d-none');
    }
}

// =====================================================
// NUEVA SOLICITUD
// =====================================================

function nuevaCotizacion() {

    $('#formSolicitud')[0].reset();

    $('#tablaDetalle tbody').empty();

    $('#emptyState').removeClass('d-none');

    $('#modalCotizacion').modal('show');
   
    cargarVendedores();
     recargarProductos();
}
</script>