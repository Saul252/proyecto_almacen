<!-- ═══════════════════════════════════════════════════════════
     MODAL GESTIONAR / CONVERTIR SOLICITUD (namespace: uni-)
     Separado de php.txt para mantenerlo limpio.
     Depende de: URL_CONTROLADOR_SOLICITUD (definida en php.txt)
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalGestionSolicitud" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        style="max-width: 90vw; width: 90vw; max-height: 85vh;">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 16px;">

            <form id="formConvertirCompra" enctype="multipart/form-data" class="d-flex flex-column h-100 m-0">

                <!-- ═══════ HEADER ═══════ -->
                <div class="modal-header border-bottom px-3 py-2 flex-shrink-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success-subtle text-success-emphasis rounded-2 d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-box-arrow-in-down" style="font-size: .8rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold mb-0">
                                Convertir solicitud
                                <span name="uni-folio" id="uni-folio" class="text-body-secondary fw-normal"></span>
                            </div>
                            <small class="text-body-secondary" style="font-size: .65rem;">
                                Generación de compra e inventario
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <input type="hidden" name="solicitud_id" id="uni-solicitud-id">

                <!-- ═══════ BODY — bg-body-secondary es la clave ═══════ -->
                <div class="modal-body bg-body-secondary px-3 py-2 flex-grow-1 overflow-y-auto">

                    <!-- ── CARD 1: Información ── -->
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body p-2">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-cart-check text-success"></i>
                                    <span class="text-uppercase fw-semibold text-body-secondary"
                                        style="font-size: .62rem; letter-spacing: .5px;">Información de
                                        compra</span>
                                </div>
                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill fw-medium"
                                    style="font-size: .6rem;">
                                    <i class="bi bi-circle-fill me-1"
                                        style="font-size: .3rem; vertical-align: middle;"></i>En proceso
                                </span>
                            </div>

                            <!-- campos -->
                            <div class="row g-2 mb-2">
                                <div class="col-md-3">
                                    <label class="form-label text-uppercase fw-medium text-body-secondary mb-1"
                                        style="font-size: .6rem; letter-spacing: .4px;">Almacén destino</label>
                                    <?php if (isset($es_admin) && $es_admin): ?>
                                        <select id="almacen_id2" name="almacen_id2"
                                            class="form-select form-select-sm rounded-2" required>
                                            <option value="">-- Seleccionar --</option>
                                            <?php foreach ($almacenes as $alm): ?>
                                                <option value="<?= $alm['id'] ?>">
                                                    <?= htmlspecialchars($alm['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" class="form-control form-control-sm rounded-2"
                                            value="<?= htmlspecialchars($almacenes[0]['nombre'] ?? 'Almacén Asignado') ?>"
                                            readonly>
                                        <input type="hidden" id="almacen_id2" name="almacen_id2"
                                            value="<?= $almacen_usuario ?? ($almacenes[0]['id'] ?? '') ?>">
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label text-uppercase fw-medium text-body-secondary mb-1"
                                        style="font-size: .6rem; letter-spacing: .4px;">Proveedor</label>
                                    <input type="text" id="uni-proveedor" class="form-control form-control-sm rounded-2"
                                        readonly>
                                    <input type="hidden" name="proveedor" id="uni-proveedor-nombre">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-uppercase fw-medium text-body-secondary mb-1"
                                        style="font-size: .6rem; letter-spacing: .4px;">Folio factura</label>
                                    <input type="text" name="folio" class="form-control form-control-sm rounded-2"
                                        placeholder="FAC-000" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-uppercase fw-medium text-body-secondary mb-1"
                                        style="font-size: .6rem; letter-spacing: .4px;">Método pago</label>
                                    <select name="metodo_pago" id="metodo_pago"
                                        class="form-select form-select-sm rounded-2" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-uppercase fw-medium text-body-secondary mb-1"
                                        style="font-size: .6rem; letter-spacing: .4px;">Evidencia</label>
                                    <input type="file" name="evidencia_compra"
                                        class="form-control form-control-sm rounded-2" accept="image/*,.pdf">
                                </div>
                            </div>

                            <!-- chips económicos con pares subtle/emphasis -->
                            <div class="row g-2">

                                <div class="col-md-4">
                                    <div class="bg-danger-subtle border border-danger-subtle rounded-2 px-2 py-1 h-100">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-exclamation-circle text-danger-emphasis"
                                                style="font-size: .8rem;"></i>
                                            <span class="text-uppercase text-danger-emphasis fw-semibold flex-grow-1"
                                                style="font-size: .6rem; letter-spacing: .3px;">Deuda</span>
                                            <input type="text" name="deudaProveedor" id="uni-proveedor-deuda"
                                                class="border-0 bg-transparent p-0 text-danger-emphasis text-end fw-semibold shadow-none"
                                                style="font-size: .78rem; height: auto; width: auto;" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div
                                        class="bg-primary-subtle border border-primary-subtle rounded-2 px-2 py-1 h-100">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-cash-coin text-primary-emphasis"
                                                style="font-size: .8rem;"></i>
                                            <span class="text-uppercase text-primary-emphasis fw-semibold flex-grow-1"
                                                style="font-size: .6rem; letter-spacing: .3px;">Abono</span>
                                            <input type="number" id="input_pagar_deuda" name="saldo_a_pagar"
                                                class="border-0 bg-transparent p-0 text-primary-emphasis text-end fw-semibold shadow-none"
                                                value="0" min="0" step="0.1"
                                                style="font-size: .8rem; height: auto; max-width: 80px;">
                                        </div>
                                        <small class="label-abono-info text-body-secondary d-block mt-1"
                                            style="font-size: .6rem;"></small>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div
                                        class="bg-success-subtle border border-success-subtle rounded-2 px-2 py-1 h-100 d-flex align-items-center justify-content-between">
                                        <span class="text-uppercase text-success-emphasis fw-semibold"
                                            style="font-size: .6rem; letter-spacing: .3px;">Total compra</span>
                                        <span class="fw-semibold text-success-emphasis" id="uni-gran-total"
                                            style="font-size: 1.05rem;">$ 0.00</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ── CARD 2: Tabla ── -->
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam text-success"></i>
                                <span class="text-uppercase fw-semibold text-body-secondary"
                                    style="font-size: .62rem; letter-spacing: .5px;">Productos</span>
                            </div>
                            <small class="text-body-secondary" style="font-size: .62rem;">
                                Conversión de unidades y costos
                            </small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" id="tablaConversion">
                                <thead class="small text-body-secondary text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-2 fw-medium border-0">Producto</th>
                                        <th class="py-2 fw-medium border-0">Cant. solicitada</th>
                                        <th class="py-2 fw-medium border-0">Llegado</th>
                                        <th class="py-2 fw-medium border-0">Costo</th>
                                        <th class="text-end pe-3 py-2 fw-medium border-0">Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- ═══════ FOOTER ═══════ -->
                <div class="modal-footer border-top px-3 py-2 flex-shrink-0">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none px-2 py-1"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-medium">
                        <i class="bi bi-check2 me-1"></i> Confirmar compra
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    /* ═══════════════════════════════════════════════════════════
       GESTIONAR / CONVERTIR SOLICITUD — Namespace uni-
       Depende de: URL_CONTROLADOR_SOLICITUD (definida en php.txt)
    ═══════════════════════════════════════════════════════════ */
    (function () {
        'use strict';

        let solicitudIDI = 0;

        async function gestionarSolicitud(id) {
            try {
                $('#tablaConversion tbody').empty();
                console.log(id);
                solicitudIDI = id;

                const resp = await fetch(`${URL_CONTROLADOR_SOLICITUD}?action=obtenerDetalle&id=${id}`);

                if (!resp.ok) throw new Error(`Error de servidor: ${resp.status}`);

                const res = await resp.json();

                if (res.status !== 'success') {
                    throw new Error(res.message || 'Error al obtener datos');
                }

                console.log(res);

                const items = res.data || [];

                if (items.length === 0) {
                    Swal.fire('Info', 'La solicitud no tiene productos.', 'info');
                    return;
                }

                // Deuda proveedor
                const deuda = Number(res?.deuda?.[0]?.pendiente) || 0;

                // Cabecera
                $('#uni-solicitud-id').val(id);
                $('#uni-folio').text(`#${id.toString().padStart(5, '0')}`);
                $('#folio_proxima_factura').val(items[0].proximo_folio);
                $('#uni-proveedor').val(items[0].proveedor_nombre || 'Sin Proveedor');
                $('#uni-proveedor-nombre').val(items[0].proveedor_nombre || '');

                $('#uni-proveedor-deuda').val(deuda);
                $('#input_pagar_deuda').val(0);
                $('#input_pagar_deuda').attr('max', deuda);
                $('.label-abono-info').text(`Máximo: ${deuda}`);

                if (deuda <= 0) {
                    $('#input_pagar_deuda').prop('disabled', true);
                } else {
                    $('#input_pagar_deuda').prop('disabled', false);
                }

                const almacenInput = document.getElementById('almacen_id2');
                const almacen_id2 = almacenInput ? almacenInput.value : 0;

                console.log('ALMACEN:', almacen_id2);

                let html = '';

                items.forEach((i, index) => {

                    const factor = parseFloat(i.factor_conversion) || 1;
                    const uBase = i.unidad_medida || 'pzas';
                    const uRep = i.unidad_reporte || 'Mayoreo';
                    const costo = i.costo;
                    const cantidadSolicitada = parseFloat(i.cantidad) || 0;
                    const cantidad = parseFloat(i.cantidad);

                    const cantMayoreo = Math.floor(cantidad);
                    const cantSueltas = ((cantidad - cantMayoreo) * factor);

                    html += `
            <tr class="fila-item" data-index="${index}">
                <td>
                    <input type="hidden" name="items[${index}][producto_id]" value="${i.producto_id}">
                    <input type="hidden" class="h-factor" value="${factor}">
                    <div class="fw-bold text-dark">${i.producto_nombre} </div>
                     <small class="text-body-secondary d-block">1 ${uRep} = ${factor} ${uBase}</small>
                </td>

                <td>
                 <div class="fw-bold ">
                    ${cantMayoreo}.${cantSueltas} ${uRep}
                </div>
                    <input type="hidden" class="form-control form-control-sm i-mayoreo border-success"
                        value="${cantMayoreo}" step=".01" oninput="recalcularFila(${index})" readonly>

                    <input type="hidden" class="form-control form-control-sm i-sueltas border-primary"
                        value="${cantSueltas}" step="0.01" oninput="recalcularFila(${index})" readonly>
                </td>

          <td>
    <label class="form-label small text-success fw-semibold mb-1">Llegado</label>

    <input type="number"
        id="llegado_${index}"
        class="form-control form-control-sm border-success shadow-sm i-llegado"
        name="items[${index}][cantidad_llegado]"
        value="${cantidadSolicitada}"
        step=".01"
        min="0"
        oninput="recalcularFila(${index})">

    <input type="hidden"
        id="faltante_${index}"
        class="form-control form-control-sm border-danger shadow-sm i-faltante"
        name="items[${index}][cantidad_faltante]"
        value="0"
        step=".01"
        min="0"
        >

    <input type="hidden"
        name="items[${index}][cantidad_excedente]"
        class="form-control form-control-sm border-success shadow-sm i-excedente"
        value="0"
        min="0"
        step="0.01"
        >
</td>

                <td>
                    <label class="small text-body-secondary fw-bold">Costo Total Renglón</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text ">$</span>
                        <input type="number" step="0.01" class="form-control i-costo-total"
                            placeholder="0.00" required value="${costo}"
                            oninput="recalcularFila(${index})">
                    </div>

                    <input type="hidden" class="h-precio-lote">

                    <div class="mt-1" style="font-size:0.75rem">
                        Cost. Unit:
                        <span class="s-precio-lote fw-bold text-secondary">$ 0.00</span>
                    </div>
                </td>

    <input
        type="hidden"
        value="${almacen_id2}"
        class="form-control i-almacen-id"
    >

                <td class="text-end bg-light-subtle">
                    <div class="h5 mb-0 fw-bold text-primary s-total-piezas">0</div>

                    <small class="text-body-secondary umedida">${cantMayoreo >= 1 ? uRep : uBase}</small>
                    <div>                    <small class=" mb-0 text-danger s-faltantes-piezas"></small>
                    <small class=" mb-0  text-success s-exedentes-piezas"></small>
                    <small class=" mb-0  text-dark s-unidad-piezas"></small>
                    <input type="hidden" class="h-total-piezas">
                    </div>
                </td>
            </tr>`;
                });

                $('#tablaConversion tbody').html(html);

                $('.fila-item').each(function (idx) {
                    recalcularFila(idx);
                });

                $('#modalGestionSolicitud').removeAttr('aria-hidden').modal('show');

            } catch (e) {
                console.error(e);
                Swal.fire('Error', e.message, 'error');
            }
        }

        function recalcularFila(index) {

            const fila = $(`.fila-item[data-index="${index}"]`);

            const factor = parseFloat(fila.find('.h-factor').val()) || 1;
            const mayoreo = parseFloat(fila.find('.i-mayoreo').val()) || 0;
            const sueltas = parseFloat(fila.find('.i-sueltas').val()) || 0;
            const llegado = parseFloat((fila.find('.i-llegado').val()) || 0) * factor;
            const unidadTexto = fila.find('.umedida').text().trim();

            let faltante = (parseFloat(fila.find('.i-faltante').val()) || 0) * factor;
            let excedente = (parseFloat(fila.find('.i-excedente').val()) || 0) * factor;

            const costoTotalRenglon = parseFloat(fila.find('.i-costo-total').val()) || 0;

            const totalBase = (mayoreo * factor) + sueltas;

            if (llegado > totalBase) {
                let total = llegado - totalBase;
                fila.find('.i-excedente').val(total / factor);
                fila.find('.i-faltante').val(0);
                fila.find('.s-faltantes-piezas').text('');
                fila.find('.s-exedentes-piezas').text(total / factor);
                fila.find('.s-unidad-piezas').text(unidadTexto);
                excedente = total;
                faltante = 0;
            }

            if (llegado < totalBase) {
                let total = totalBase - llegado;
                fila.find('.i-faltante').val(total / factor);
                fila.find('.i-excedente').val(0);
                fila.find('.s-exedentes-piezas').text();

                fila.find('.s-faltantes-piezas').text(total / factor);
                fila.find('.s-unidad-piezas').text(unidadTexto);

                faltante = total;
                excedente = 0;
            }

            if (llegado == totalBase) {
                fila.find('.s-faltantes-piezas').text('');
                fila.find('.s-exedentes-piezas').text('');
                fila.find('.i-faltante').val(0);
                fila.find('.i-excedente').val(0);
                faltante = 0;
                excedente = 0;
            }

            const totalPiezasFinal = totalBase - faltante + excedente;

            mayoreo >= 1
                ? fila.find('.s-total-piezas').text(
                    Number.isInteger(totalPiezasFinal / factor)
                        ? totalPiezasFinal / factor
                        : (totalPiezasFinal / factor).toFixed(2)
                )
                : fila.find('.s-total-piezas').text(
                    Number.isInteger(totalPiezasFinal)
                        ? totalPiezasFinal
                        : (totalPiezasFinal).toFixed(2)
                );

            fila.find('.h-total-piezas').val(totalPiezasFinal);

            let precioUnitario = totalBase > 0
                ? costoTotalRenglon / totalBase
                : 0;

            fila.find('.h-precio-lote').val(precioUnitario.toFixed(4));

            fila.find('.s-precio-lote').text(
                '$ ' + precioUnitario.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 4
                })
            );

            actualizarGranTotal();
        }

        function actualizarGranTotal() {
            let granTotal = 0;
            $('.i-costo-total').each(function () {
                granTotal += parseFloat($(this).val()) || 0;
            });

            $('#uni-gran-total').text('$ ' + granTotal.toLocaleString(undefined, {
                minimumFractionDigits: 2
            }));
        }

        /* Submit del form: convertir solicitud → compra */
        $(document).ready(function () {
            $('#formConvertirCompra').off('submit').on('submit', function (e) {
                e.preventDefault();

                const detalle = [];

                $('.fila-item').each(function () {
                    const fila = $(this);
                    const index = fila.data('index');

                    const almId = $('#almacen_id2').val();
                    const cantTotal = parseFloat(fila.find('.h-total-piezas').val()) || 0;
                    const costoTotal = parseFloat(fila.find('.i-costo-total').val()) || 0;
                    const excedente = parseFloat(fila.find('.i-excedente').val()) || 0;
                    const faltante = parseFloat(fila.find('.i-faltante').val()) || 0;

                    const productoId = fila.find(
                        `input[name="items[${index}][producto_id]"]`
                    ).val();

                    if (!productoId) {
                        console.warn('Producto inválido en fila:', index);
                        return;
                    }

                    const factor = parseFloat(fila.find('.h-factor').val()) || 1;

                    detalle.push({
                        producto_id: productoId,
                        input_mayoreo: parseFloat(fila.find('.i-mayoreo').val()) || 0,
                        input_sueltas: parseFloat(fila.find('.i-sueltas').val()) || 0,
                        cantidad_excedente: (excedente * factor),
                        cantidad_faltante: (faltante * factor),
                        total_item: costoTotal,
                        precio_lote: parseFloat(fila.find('.h-precio-lote').val()) || 0,
                        hidden_factor: parseFloat(fila.find('.h-factor').val()) || 1,
                        almacenes: {
                            [almId]: {
                                activo: 'on',
                                cantidad: cantTotal
                            }
                        }
                    });
                });

                console.log('DETALLE A ENVIAR:', detalle);

                if (detalle.length === 0) {
                    Swal.fire('Atención', 'No hay productos para guardar.', 'warning');
                    return;
                }

                const costoInvalido = detalle.some(item =>
                    parseFloat(item.total_item) <= 0
                );

                if (costoInvalido) {
                    Swal.fire('Atención', 'Todos los productos deben tener un costo mayor a 0.', 'warning');
                    return;
                }

                const formData = new FormData(this);
                formData.append('action', 'guardarCompraCompleta');
                formData.append('items', JSON.stringify(detalle));
                formData.append('solicitud_id', $('#uni-solicitud-id').val());
                formData.append('almacen_id', $('#almacen_id2').val());
                formData.append('proveedor', $('#uni-proveedor').val());

                Swal.fire({
                    title: '¿Confirmar Ingreso?',
                    text: 'Se registrará la entrada en inventario y se cerrará la solicitud.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Procesando...',
                        html: 'Guardando datos y generando lotes',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: URL_CONTROLADOR_SOLICITUD,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',

                        success: function (res) {
                            console.log('RESPUESTA:', res);

                            if (res.success) {
                                Swal.fire('¡Éxito!', res.message, 'success')
                                    .then(() => {
                                        $('#modalGestionSolicitud').modal('hide');
                                        if (typeof cargarSolicitudes === 'function') cargarSolicitudes();
                                    });
                            } else {
                                Swal.fire('Error de negocio', res.message || 'Error desconocido', 'error');
                            }
                        },

                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('AJAX ERROR:', textStatus);
                            console.error('ERROR THROWN:', errorThrown);
                            console.error('RESPUESTA:', jqXHR.responseText);

                            Swal.fire({
                                icon: 'error',
                                title: 'Error del Servidor',
                                html: `
                                    <div style="
                                        text-align:left;
                                        font-size:11px;
                                        background:#eee;
                                        padding:10px;
                                        max-height:250px;
                                        overflow:auto;
                                        border-radius:6px;
                                    ">
                                        ${jqXHR.responseText || 'Error desconocido (posible 500)'}
                                    </div>`,
                                footer: 'Revisa la pestaña Network en F12 para más detalles.'
                            });
                        }
                    });
                });
            });
        });

        /* Exponer al scope global (lo invocan los botones de la tabla) */
        window.gestionarSolicitud = gestionarSolicitud;
        window.recalcularFila = recalcularFila;
        window.actualizarGranTotal = actualizarGranTotal;
        window.getSolicitudIDI = () => solicitudIDI;

    })();
</script>