<div class="modal fade" id="modalEditarCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: 20px;  overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <form id="formEditarSolicitud">
                <input type="hidden" id="editar_cotizacion_id" name="cotizacion_id" value="">

                <div class="modal-header bg-white  pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-dark rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Editar Cotizacion</h4>
                            <p class="text-body-secondary small mb-0">Modifique los datos de la cotización existente</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="row g-3 mb-4 p-3 rounded-4 bg-light shadow-sm align-items-end">

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">
                                <i class="bi bi-box-seam"></i> Almacén de Cargo
                            </label>
                            <div class="input-group shadow-sm">
                                <select 
                                    name="almacen_id_editar" 
                                    id="almacen_id_editar"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Seleccionar ubicación...</option>
                                    <?php foreach($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>">
                                            <?= $a['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">
                                <label class="form-label small fw-bold text-body-secondary text-uppercase">Cliente</label>
                                <select name="cliente_id_editar" id="cliente_id_editar" class="form-select select2-modal-editar" required>
                                    <option value="">Seleccionar cliente...</option>
                                    <?php foreach($clientes as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline-primary flex-shrink-0" type="button"
                                    onclick="abrirModalNuevoCliente()" style="border-radius:10px;">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-body-secondary text-uppercase">Añadir Producto (SKU o Nombre)</label>
                            <div class="input-group">
                                <select id="buscadorProductosEditar" class="form-select select2-modal-editar border-start-0">
                                    <option value="">Escribe para buscar...</option>
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
                                <button type="button" class="btn btn-primary d-flex align-items-center"
                                    onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    <span class="d-none d-xl-inline">Nuevo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive border rounded-4 bg-white">
                        <table class="table align-middle mb-0" id="tablaDetalleEditar">
                            <thead class="bg-light">
                                <tr class="text-body-secondary small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 25%;">Presentación / Unidad</th>
                                    <th style="width: 25%;">Tipo de precio</th>
                                    <th class="ps-4">Precio Unitario</th>
                                    <th style="width: 50%;">Precio</th>
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

                    <div class="text-end mt-3">
                        <small class="d-block text-body-secondary fw-semibold mb-1" style="letter-spacing:.5px;">
                            COSTO TOTAL DE COMPRA (EDICIÓN)
                        </small>
                        <div id="costoTotalCompraEditar" class="fw-bold text-success" style="font-size:2rem; line-height:1;">
                            $0.00
                        </div>
                        <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar">
                    </div>
                </div>

               <div class="modal-footer  p-4" id="modal-footer">
    <button type="button" class="btn btn-link text-decoration-none text-body-secondary fw-bold" data-bs-dismiss="modal">
        Cancelar
    </button>
    
    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow" 
        onclick="procederPago($('#totalCotizacionEditar').val(), $('#editar_cotizacion_id').val())">
        <i class="bi bi-cart-check me-2"></i> Convertir a Venta
    </button>

    <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow text-dark">
        <i class="bi bi-check2-circle me-2"></i> Actualizar Cotización
    </button>
</div>
            </form>
        </div>
    </div>
</div>

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
        let id=document.getElementById('editar_cotizacion_id').value;
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
        const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=obtenerProductos`);
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
                class="form-control precio-total-editar fw-bold text-success bg-light"
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

    const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
    const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

    const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
    const precioBase = Number(tipoOption?.dataset.precio || 0);

    const sugerido = precioBase / equivalencia;
    inputPrecio.placeholder = sugerido.toFixed(4);
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
// =====================================================
function editarCotizacion(datosCotizacion, itemsCotizacion) {
    // 1. Resetear formulario
    $('#formEditarSolicitud')[0].reset();
    $('#tablaDetalleEditar tbody').empty();
    
    // 2. Setear Cabecera
    $('#editar_cotizacion_id').val(datosCotizacion.id);
    $('#almacen_id_editar').val(datosCotizacion.almacen_id);
    $('#cliente_id_editar').val(datosCotizacion.cliente_id).trigger('change.select2');

    // 3. Evaluar Estado de Vacío
    if(itemsCotizacion.length > 0) {
        $('#emptyStateEditar').addClass('d-none');
    } else {
        $('#emptyStateEditar').removeClass('d-none');
    }

    // 4. Poblar las filas del detalle recuperado (Aquí deberás mapear tu JSON de BD al HTML de la fila)
    // Nota: El proceso de inyección de filas guardadas sigue la estructura de filas generada arriba.

    $('#modalEditarCotizacion').modal('show');
    recargarProductosEditar();
}
</script>