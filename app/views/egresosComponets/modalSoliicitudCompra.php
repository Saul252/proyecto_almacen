<script>
// PHP le pasa estos valores a JS una sola vez al cargar la página
const USER_ALMACEN_ID = <?= json_encode($_SESSION['almacen_id']) ?>;
const ES_ADMIN = <?= ($_SESSION['rol_id'] == 1) ? 'true' : 'false' ?>;
</script>
<link href="/myvet/css/modalCompras.css" rel="stylesheet">
<div class="modal fade" id="modalNuevaCompra" tabindex="-1" aria-labelledby="modalNuevaCompraLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content  shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNuevaCompraLabel">
                    <i class="bi bi-box-seam-fill me-2"></i> Registrar Compra / Entrada de Inventario 2
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="formNuevaCompra" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body bg-light">
                    <div class="card mb-4  shadow-sm">
                      <div class="card-body">

    <div class="row g-3">

        <!-- PROVEEDOR -->
        <div class="col-md-5">
            <label class="form-label small fw-bold">Proveedor</label>

            <div class="input-group shadow-sm">
                <select name="proveedor" id="select_proveedor" class="form-select" required>
                    <option value="">Seleccione un proveedor...</option>
                    <?php foreach($proveedores as $p): ?>
                    <option value="<?= $p['id'] ?>" data-deuda="<?= $p['total_deuda'] ?>">
                        <?= $p['nombre_comercial'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <button class="btn btn-outline-success" type="button"
                    onclick="abrirModalNuevoProveedor()">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>

            <!-- DEUDA -->
            <div id="deuda_proveedor" class="small fw-semibold text-danger mt-2">
                Deuda: $0.00
            </div>
        </div>

        <!-- FOLIO -->
        <div class="col-md-2">
            <label class="form-label small fw-bold">Folio</label>
            <input type="text" id="folio_compra" name="folio"
                class="form-control shadow-sm"
                placeholder="Cargando..." readonly required>
        </div>

        <!-- ALMACEN -->
        <div class="col-md-5">
            <label class="form-label small fw-bold">
                <i class="bi bi-box-seam"></i> Almacén de Cargo
            </label>

            <?php $es_admin = ($_SESSION['rol_id'] == 1); ?>

            <div class="input-group shadow-sm">
                <select id="almacen_id_cabecera_visual"
                    class="form-select <?= $es_admin ? '' : 'bg-light' ?>"
                    <?= !$es_admin ? 'disabled' : 'name="almacen_id_cabecera"' ?> required>

                    <?php if ($es_admin): ?>
                    <option value="">Seleccionar ubicación...</option>
                    <?php endif; ?>

                    <?php foreach($almacenes as $a): ?>
                    <option value="<?= $a['id'] ?>"
                        <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                        <?= $a['nombre'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!$es_admin): ?>
                <span class="input-group-text bg-light text-body-secondary">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <?php endif; ?>
            </div>

            <?php if (!$es_admin): ?>
            <input type="hidden" name="almacen_id_cabecera"
                value="<?= $_SESSION['almacen_id'] ?>">
            <small class="text-body-secondary">
                Privilegios de sede actual
            </small>
            <?php endif; ?>
        </div>

        <!-- EVIDENCIA -->
        <div class="col-md-4">
            <label class="form-label small fw-bold">Evidencia</label>
            <input type="file"
                name="evidencia_compra"
                class="form-control shadow-sm"
                accept="image/*,.pdf">
        </div>

        <!-- METODO -->
        <div class="col-md-3">
            <label class="form-label small fw-bold">Método de pago</label>
            <select name="metodo_pago" id="metodo_pago"
                class="form-select shadow-sm" required>
                <option value="">Seleccione...</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Tarjeta">Tarjeta</option>
            </select>
        </div>

        <!-- PAGO DE DEUDA -->
      <div class="col-md-2">
    <label class="form-label small fw-bold text-primary">
        <i class="bi bi-cash-coin"></i> Pagar deuda
    </label>
    <input type="number"
        id="input_pagar_deuda"
        name="saldo_a_pagar"
        class="form-control shadow-sm border-primary"
        value="0" min="0" step="0.1"
        placeholder="0.0"
        disabled>
</div>

        <!-- TOTAL -->
        <div class="col-md-3">
            <label class="form-label small fw-bold text-body-secondary">TOTAL FACTURA</label>
            <div class="bg-light border rounded-3 p-2 text-center shadow-sm">
                <span class="h4 text-success fw-bold m-0" id="granTotalCompra">$ 0.00</span>
            </div>
        </div>

    </div>
</div>
                    </div>

                    <h6 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list-check me-2"></i>Detalle de Productos</span>
                        <span class="badge bg-dark" id="conteoItems">0 Productos</span>
                    </h6>


                    <div id="contenedorItemsCompra"></div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4"
                            onclick="agregarFilaCompra()">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Producto a la Lista
                        </button>
                    </div>
                </div>

                <div class="modal-footer bg-white shadow-sm">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success px-5" id="btnGuardarCompra"
                        onclick="procesarGuardadoCompra(); return false;">
                        <i class="bi bi-save me-2"></i> Guardar Compra e Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/agregarPoductoModal.php'; ?>
<?php require_once __DIR__ . '/modalProveedoresCompra.php'; ?>

<script>
/**
 * LÓGICA DE COMPRAS - CF SISTEM
 */
function abrirModalCompra() {
    $('#select_proveedor').select2('destroy');
    // Resetear el formulario pero mantener el almacén seleccionado por PHP
    const almacenPreseleccionado = $('#almacen_id_cabecera').val();
    $('#formNuevaCompra')[0].reset();
    $('#almacen_id_cabecera').val(almacenPreseleccionado); // Restauramos lo que PHP eligió

    $('#contenedorItemsCompra').empty();
    $('#granTotalCompra').text('$ 0.00');

    if (ES_ADMIN) {
        setTimeout(() => {
            $('.select2-cabecera').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalNuevaCompra .modal-content')
            });
        }, 100);
    }

    agregarFilaCompra();
    $('#modalNuevaCompra').modal('show');
}
// --- PEGA ESTO DENTRO DE TU ETIQUETA <script> ---

/**
 * Función para obtener el folio desde el servidor
 */
function asignarSiguienteFolioCompra() {
    const inputFolio = document.getElementById('folio_compra');
    if (!inputFolio) return;

    inputFolio.value = "Cargando...";

    fetch('/myvet/app/controllers/egresosController.php?action=getSiguienteFolio')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                inputFolio.value = data.folio;
            } else {
                inputFolio.value = "";
                inputFolio.readOnly = false; // Si falla, dejamos que el usuario escriba
            }
        })
        .catch(err => {
            console.error("Error al obtener folio:", err);
            inputFolio.readOnly = false;
        });
}

// MODIFICACIÓN: Agregamos la carga del folio a tu función de abrir modal
// Busca tu función abrirModalCompra() y asegúrate de que llame a asignarSiguienteFolioCompra()
const originalAbrirModal = window.abrirModalCompra;
window.abrirModalCompra = function() {
    // Llamamos a la lógica original que ya tenías
    originalAbrirModal();

    // Disparamos la carga del folio automático
    asignarSiguienteFolioCompra();
};

/**
 * Listener para asegurar que si abres el modal por otros medios (como data-bs-toggle), 
 * también se cargue el folio.
 */
window.addEventListener('load', function() {
    if (window.jQuery) {
        $(document).on('show.bs.modal', '#modalNuevaCompra', function() {
            asignarSiguienteFolioCompra();
        });
    }
});

function agregarFilaCompra() {
    const idUnico = Date.now();

    let opcionesProd = '<option value="">-- Buscar Producto --</option>';
    DATA_COMPRAS.productos.forEach(p => {
        opcionesProd +=
            `<option value="${p.id}" data-factor="${p.factor_conversion}" data-ubase="${p.unidad_medida}" data-urep="${p.unidad_reporte}">${p.nombre} (${p.sku})</option>`;
    });

    let filasAlmacenes = '';
    const almacenesAMostrar = ES_ADMIN ?
        DATA_COMPRAS.almacenes :
        DATA_COMPRAS.almacenes.filter(alm => alm.id == USER_ALMACEN_ID);

    almacenesAMostrar.forEach(alm => {
        const inputBloqueado = !ES_ADMIN ? 'onclick="return false;" style="opacity: 0.7;"' : '';
        const filaResaltada = alm.id == USER_ALMACEN_ID ? 'table-info' : '';

        filasAlmacenes += `
        <tr class="${filaResaltada}">
            <td class="text-center align-middle">
                <input type="checkbox" name="items[${idUnico}][almacenes][${alm.id}][activo]" 
                       class="form-check-input check-activo" checked ${inputBloqueado} 
                       onchange="recalcularTotales(${idUnico})">
            </td>
            <td class="small align-middle fw-bold">${alm.nombre}</td>
            <td>
                <input type="number" name="items[${idUnico}][almacenes][${alm.id}][cantidad]" 
                       class="form-control form-control-sm input-reparto border-primary" 
                       placeholder="0.00" min="0" step="0.01" 
                       oninput="validarReparto(${idUnico})">
            </td>
        </tr>`;
    });

   const html = `
<div class="card mb-4  shadow-sm rounded-4 item-compra" id="card_item_${idUnico}">
    <div class="card-body p-3">

        <div class="row g-3 mb-3">

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-body-secondary mb-1">
                    <i class="bi bi-search me-1"></i>Producto
                </label>
                <select name="items[${idUnico}][producto_id]" 
                    class="form-select form-select-sm shadow-sm select2-compra"
                    onchange="actualizarLabelsUnidad(${idUnico}, this)" required>
                    ${opcionesProd}
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label small text-body-secondary mb-1 label-urep">Mayoreo</label>
                <input type="number" class="form-control form-control-sm shadow-sm input-mayoreo"
                    value="0" min="0" oninput="recalcularTotales(${idUnico})">
            </div>

            <div class="col-md-1">
                <label class="form-label small text-body-secondary mb-1 label-ubase">Sueltas</label>
                <input type="number" class="form-control form-control-sm shadow-sm input-sueltas"
                    value="0" min="0" oninput="recalcularTotales(${idUnico})">
            </div>

            <div class="col-md-2">
                <label class="form-label small text-danger fw-semibold mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" class="form-check-input me-1"
                        onchange="toggleFaltante(${idUnico}, this)">
                    Faltante
                </label>
                <input type="number"
                    class="form-control form-control-sm border-danger shadow-sm input-faltante"
                    value="0" min="0" disabled
                    oninput="recalcularTotales(${idUnico})">
                <input type="hidden" id="faltante_${idUnico}"  name="items[${idUnico}][cantidad_faltante]" class="hidden-faltante" value="0">
            </div>

            <div class="col-md-2">
                <label class="form-label small text-success fw-semibold mb-1">
                    <i class="bi bi-plus-circle-fill me-1"></i>Excedente
                </label>
                <input type="number"
                    name="items[${idUnico}][cantidad_excedente]"
                    class="form-control form-control-sm border-success shadow-sm input-excedente"
                    value="0" min="0" step="0.01"
                    oninput="recalcularTotales(${idUnico})"
                    placeholder="0.00">
            </div>

         

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-body-secondary mb-1">Costo total</label>
                <div class="input-group input-group-sm shadow-sm">
                    <span class="input-group-text">$</span>
                    <input type="number"
                        name="items[${idUnico}][total_item]"
                        class="form-control input-costo-total"
                        value="0" step="0.01"
                        oninput="calcularPrecioUnitarioLote(${idUnico})"
                        required>
                </div>
                <small class="text-body-secondary d-block mt-1" style="font-size: 0.7rem;">
                    Costo lote: <span class="span-precio-lote fw-semibold">$0.00</span>
                </small>
                <input type="hidden" name="items[${idUnico}][precio_lote]" class="hidden-precio-lote" value="0">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="button"
                    class="btn btn-sm btn-outline-danger w-100 rounded-3"
                    onclick="$('#card_item_${idUnico}').remove(); actualizarGranTotal();">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

        </div>

        <div class="row g-3 mb-2">
            <div class="col-md-4">
                <div class="p-3 bg-dark text-white rounded-3 text-center shadow-sm">
                    <small class="d-block opacity-75 mb-1">STOCK TOTAL</small>
                    <span class="h5 fw-bold span-total-base">0</span>
                    <small class="label-ubase-text">pzas</small>
                    <input type="hidden" class="hidden-factor" value="1">
                    <input type="hidden" class="hidden-total-piezas" name="items[${idUnico}][cantidad_total_piezas]" value="0">
                </div>
            </div>

            <div class="col-md-8">
                <div class="alert alert-info d-flex justify-content-between align-items-center h-100 py-2 px-3 small rounded-3 shadow-sm">
                    <span>
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Distribuye el total en almacenes
                    </span>
                    <span class="badge bg-danger" id="error_reparto_${idUnico}" style="display:none;">
                        Error
                    </span>
                </div>
            </div>
        </div>

        <hr class="my-3">

        <table class="table table-sm align-middle mb-0">
            <thead class="text-body-secondary small">
                <tr>
                    <th class="text-center">Usar</th>
                    <th>Almacén</th>
                    <th>Piezas</th>
                </tr>
            </thead>
            <tbody>${filasAlmacenes}</tbody>
        </table>

    </div>
</div>
`;
    $('#contenedorItemsCompra').append(html);
    setTimeout(() => {
        $(`#card_item_${idUnico} .select2-compra`).select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalNuevaCompra .modal-content')
        });
    }, 50);
    actualizarConteo();
}

function actualizarLabelsUnidad(id, select) {
    const opt = $(select).find(':selected');
    const factor = opt.data('factor') || 1;
    const uBase = opt.data('ubase') || 'Piezas';
    const uRep = opt.data('urep') || 'Mayoreo';
    const card = $(`#card_item_${id}`);
    card.find('.hidden-factor').val(factor);
    card.find('.label-urep').text(uRep);
    card.find('.label-ubase').text(uBase);
    card.find('.label-ubase-text').text(uBase);
    recalcularTotales(id);
}

function recalcularTotales(id) {
    const card = $(`#card_item_${id}`);
    const factor = parseFloat(card.find('.hidden-factor').val()) || 0;
    const inputFaltante = card.find('.input-faltante');
    const inputExcedente = card.find('.input-excedente'); // <--- NUEVO

    // 1. Cantidad según factura (Mayoreo * Factor + Sueltas)
    const cantidadFacturada = (parseFloat(card.find('.input-mayoreo').val()) || 0) * factor +
        (parseFloat(card.find('.input-sueltas').val()) || 0);

    // 2. Faltante: Si está habilitado, restamos.
    const faltante = inputFaltante.is(':disabled') ? 0 : (parseFloat(inputFaltante.val()) || 0);

    // 3. Excedente: Si está habilitado, SUMAMOS (Lo que llegó de más) <--- NUEVO
    const excedente = inputExcedente.is(':disabled') ? 0 : (parseFloat(inputExcedente.val()) || 0);

    // 4. Lo que realmente entró físicamente al almacén
    const totalReal = cantidadFacturada - faltante + excedente;

    // Actualizar labels y campos ocultos
    card.find('.span-total-base').text(totalReal.toLocaleString());
    card.find('.hidden-total-piezas').val(totalReal);
    card.find('.hidden-faltante').val(faltante);

    // Si tienes un input oculto para excedente, actualízalo aquí:
    // card.find('.hidden-excedente').val(excedente); 

    calcularPrecioUnitarioLote(id);
    validarReparto(id);
    actualizarGranTotal();
}

function validarReparto(id) {
    const card = $(`#card_item_${id}`);
    const total = parseFloat(card.find('.hidden-total-piezas').val()) || 0;
    let suma = 0;
    card.find('.input-reparto').each(function() {
        if ($(this).closest('tr').find('.check-activo').is(':checked')) suma += parseFloat($(this).val()) || 0;
    });
    const error = $(`#error_reparto_${id}`);
    if (Math.abs(suma - total) > 0.001 && total > 0) {
        card.find('.alert').addClass('alert-danger text-danger').removeClass('alert-info text-dark');
        error.show().text(`Diferencia: ${(total - suma).toFixed(2)}`);
    } else {
        card.find('.alert').addClass('alert-info text-dark').removeClass('alert-danger text-danger');
        error.hide();
    }
}

function toggleFaltante(id, checkbox) {

    const container = $(checkbox).closest('.col-md-2');
    const inputFaltante = container.find('.input-faltante');

    if (checkbox.checked) {
        inputFaltante.prop('disabled', false).focus();
    } else {
        inputFaltante.prop('disabled', true).val(0);
        recalcularTotales(id);
    }
}

function actualizarGranTotal() {
    let granTotal = 0;
    $('.input-costo-total').each(function() {
        granTotal += parseFloat($(this).val()) || 0;
    });
    $('#granTotalCompra').text('$ ' + granTotal.toLocaleString(undefined, {
        minimumFractionDigits: 2
    }));
    actualizarConteo();
}

function actualizarConteo() {
    const n = $('.item-compra').length;
    $('#conteoItems').text(`${n} Producto${n !== 1 ? 's' : ''}`);
}

function refrescarListaProductosCompra(nuevoIdSeleccionar = null) {
    $.get('/myvet/app/controllers/almacenes.php?action=getListaProductosJson', function(data) {
        // VALIDACIÓN CRÍTICA: Asegurarnos de que 'data' sea un array
        let productos = [];
        if (Array.isArray(data)) {
            productos = data;
        } else if (data && typeof data === 'object' && data.status === 'success') {
            // Por si tu controlador devuelve {status: success, data: [...]}
            productos = data.data;
        }

        if (productos.length === 0) {
            console.warn("No se recibieron productos o el formato es incorrecto", data);
            return;
        }

        // Actualizamos DATA_COMPRAS
        if (typeof DATA_COMPRAS !== 'undefined') {
            DATA_COMPRAS.productos = productos;
        }

        $('.select2-compra').each(function() {
            const select = $(this);
            const valorActual = select.val();

            let html = '<option value="">-- Buscar Producto --</option>';
            productos.forEach(p => {
                html += `<option value="${p.id}" 
                            data-factor="${p.factor_conversion}" 
                            data-ubase="${p.unidad_medida}" 
                            data-urep="${p.unidad_reporte}">${p.nombre} (${p.sku})</option>`;
            });

            select.html(html).val(valorActual).trigger('change.select2');
        });

        if (nuevoIdSeleccionar && window.ultimaFilaEditada) {
            const filaSelect = $(`#card_item_${window.ultimaFilaEditada} .select2-compra`);
            filaSelect.val(nuevoIdSeleccionar).trigger('change');
            window.ultimaFilaEditada = null;
        }
    }, 'json').fail(function(e) {
        console.error("Error al obtener productos:", e.responseText);
    });
}

function calcularPrecioUnitarioLote(id) {

    const card = $(`#card_item_${id}`);

    const costoTotalRenglon = parseFloat(card.find('.input-costo-total').val()) || 0;

    const piezasReales = parseFloat(card.find('.input-cantidad-recibida').val()) || 0;
    const excedente   = parseFloat(card.find('.input-excedente').val()) || 0;

    // 🔥 calcular base correctamente
    let piezasBase = piezasReales - excedente;

    // 🛑 evitar negativos o 0
    if (piezasBase <= 0) {
        piezasBase = 0;
    }

    let precioUnitario = 0;

    if (piezasBase > 0) {
        precioUnitario = costoTotalRenglon / piezasBase;
    }

    // 🛡️ blindaje
    if (!isFinite(precioUnitario)) {
        precioUnitario = 0;
    }

    card.find('.hidden-precio-lote').val(precioUnitario.toFixed(4));

    card.find('.span-precio-lote').text(
        '$ ' + precioUnitario.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 4
        })
    );

    actualizarGranTotal();
}/**
 * MANEJO DEL SUBMIT (BLINDADO)
 */
function procesarGuardadoCompra(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    console.log("Iniciando proceso de guardado...");

    // 1. VALIDACIÓN: Comparamos lo repartido vs lo que FÍSICAMENTE llegó
    let inconsistencias = 0;
    let mensajeDetalle = "";

    $('.item-compra').each(function(index) {
        // 'hidden-total-piezas' ya tiene restado el faltante por la función recalcularTotales
        const totalFisicoReal = parseFloat($(this).find('.hidden-total-piezas').val()) || 0;
        const nombreProd = $(this).find('.select2-compra option:selected').text() || "Producto " + (index + 1);

        let sumaAlmacenes = 0;
        $(this).find('.input-reparto').each(function() {
            if ($(this).closest('tr').find('.check-activo').is(':checked')) {
                sumaAlmacenes += parseFloat($(this).val()) || 0;
            }
        });

        if (Math.abs(totalFisicoReal - sumaAlmacenes) > 0.01) {
            inconsistencias++;
            mensajeDetalle += `\n- ${nombreProd}: Debes repartir ${totalFisicoReal} (llevas ${sumaAlmacenes})`;
        }
    });

    if (inconsistencias > 0) {
        Swal.fire('Atención', 'La distribución en almacenes no coincide con lo recibido físicamente:' + mensajeDetalle,
            'warning');
        return false;
    }

    // 2. DETECTAR SI HAY FALTANTES PARA EL CONFIRM
    let hayFaltantes = false;
    $('.hidden-faltante').each(function() {
        if (parseFloat($(this).val()) > 0) hayFaltantes = true;
    });

    // 3. CONFIRMACIÓN Y ENVÍO AJAX (Tu bloque original)
    Swal.fire({
        title: hayFaltantes ? '¿Registrar con Faltantes?' : '¿Confirmar Registro?',
        text: hayFaltantes ?
            "La mercancía incompleta se guardará como pendiente." :
            "Se actualizará el stock y se registrará el gasto.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formElement = document.getElementById('formNuevaCompra');
            const formData = new FormData(formElement);

            // Importante: Aseguramos que el controlador reciba si hay faltantes
            formData.append('tiene_faltantes', hayFaltantes ? 1 : 0);

            $.ajax({
                url: '/myvet/app/controllers/egresosController.php?action=guardarCompraInventario',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $('#btnGuardarCompra').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span> Guardando...');
                },
                success: function(res) {
                    try {
                        const data = typeof res === 'string' ? JSON.parse(res) : res;
                        if (data.success) {
                            Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                            $('#btnGuardarCompra').prop('disabled', false).html(
                                '<i class="bi bi-save me-2"></i> Guardar Compra e Inventario');
                        }
                    } catch (err) {
                        console.error("Error parseo JSON:", res);
                        Swal.fire('Error Crítico', 'Respuesta no válida del servidor.', 'error');
                        $('#btnGuardarCompra').prop('disabled', false).html('Guardar');
                    }
                },
                error: function(xhr) {
                    console.error("Error 500:", xhr.responseText);
                    Swal.fire('Error de Servidor', 'El controlador falló (500).', 'error');
                    $('#btnGuardarCompra').prop('disabled', false).html('Guardar');
                }
            });
        }
    });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('select_proveedor');
    const box = document.getElementById('deuda_proveedor');
    const input = document.getElementById('input_pagar_deuda');

 // Limpia formato ($, comas, etc.)
    
    select.addEventListener('change', function () {

        const option = this.options[this.selectedIndex];

        const deuda = parseFloat(option.dataset.deuda || 0);

        box.innerText = "Deuda: $" + deuda.toLocaleString('es-MX', {
            minimumFractionDigits: 2
        });

        // 🔥 opcional: cambiar color si debe o no
        if (deuda > 0) {
            box.classList.remove('text-success');
            box.classList.add('text-danger');
             input.disabled = false;
        } else {
            box.classList.remove('text-danger');
            box.classList.add('text-success');
            input.disabled = true;
        input.value = 0;
        }

    });

});
</script>
<script>
// Función para refrescar la lista de proveedores sin recargar página
function actualizarListaProveedores() {
    fetch('/myvet/app/controllers/egresosController.php?action=getProveedoresJSON')
        .then(res => res.json())
        .then(data => {
            let $select = $('#select_proveedor');
            $select.empty().append('<option value="">Seleccione o busque un proveedor...</option>');

            data.forEach(p => {
                $select.append(new Option(p.nombre_comercial, p.nombre_comercial));
            });

            $select.trigger('change');
        });
}
</script>