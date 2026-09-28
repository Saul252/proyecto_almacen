<div class="modal fade" id="modalAbono" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg " style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title">Registrar Abono</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_favor_disponible">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Monto a Recibir</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0">$</span>
                        <input type="number" id="inputMontoAbono" class="form-control border-start-0 ps-0 fw-bold" step="any">
                    </div>
                    <div id="infoSaldo" class="badge bg-light text-dark border w-100 mt-2 py-2 text-wrap"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Método de Pago</label>
                    <select id="selectMetodoPago" class="form-select fw-bold" onchange="verificarMetodoPago(this.value)">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option id="optionSaldoFavor" value="Saldo a Favor" style="display:none; background-color: #e3f2fd; color: #0d6efd;"></option>
                    </select>
                </div>
                              <div class="mb-3" id="contenedorReferencia" style="display:none;">
    <label class="form-label small fw-bold text-secondary text-uppercase">
        Referencia
    </label>
    <input 
        type="text" 
        id="inputReferencia" 
        class="form-control"
        placeholder="Ingrese referencia"
    >
</div>

                <hr class="my-3 opacity-10">

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="checkFechaPersonalizada" onchange="toggleFechaAbono(this.checked)">
                    <label class="form-check-label small fw-bold text-primary" for="checkFechaPersonalizada">Fecha personalizada</label>
                </div>

                <div id="containerFechaAbono" style="display: none;" class="animate__animated animate__fadeIn">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Fecha y Hora del Pago</label>
                        <input type="datetime-local" id="inputFechaAbono" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="guardarAbonoModal()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
    /**
 * Abre el flujo de abono inyectando los datos de ventaActual (el $detalle de PHP)
 */
function abrirFlujoAbono() {
    // 1. Cálculos base desde ventaActual (tu objeto $detalle)
    const info = ventaActual.info;
    const estatus = info.estatus_cliente; // Datos inyectados por el controlador
    
    const totalVenta = parseFloat(info.total || 0);
    const pagado = parseFloat(info.total_pagado || 0);
    const saldoPendiente = parseFloat((totalVenta - pagado).toFixed(2));

    if (saldoPendiente <= 0) {
        Swal.fire('Venta Liquidada', 'Sin saldo pendiente.', 'success');
        return;
    }

    // 2. Extraer Saldo a Favor del cliente
    const favorDisponible = parseFloat(estatus?.saldo_a_favor || 0);
    $('#modal_favor_disponible').val(favorDisponible);

    // 3. Configurar opción de Saldo a Favor en el Select
    const optFavor = $('#optionSaldoFavor');
    if (favorDisponible > 0.01) {
        optFavor.show().text(`🌟 Usar Saldo a Favor ($${favorDisponible.toFixed(2)})`);
    } else {
        optFavor.hide();
    }

    // 4. Llenar interfaz del modal
    $('#inputMontoAbono').val(saldoPendiente.toFixed(2)).removeClass('is-invalid text-danger');
    $('#infoSaldo').text('Saldo máximo: $' + saldoPendiente.toFixed(2))
                  .removeClass('bg-danger text-white bg-info').addClass('bg-light text-dark');
    
    $('#selectMetodoPago').val('Efectivo');
    $('.modal-title').text('Abonar a Folio: ' + (info.folio || info.id));

    // 5. Mostrar modal y dar foco
    modalAbonoObj.show();
    document.getElementById('modalAbono').addEventListener('shown.bs.modal', () => {
        document.getElementById('inputMontoAbono').focus();
        document.getElementById('inputMontoAbono').select();
    }, { once: true });
}

/**
 * Lógica para auto-completar monto si elige Saldo a Favor
 */
function verificarMetodoPago(metodo) {
    const favorDisp = parseFloat($('#modal_favor_disponible').val()) || 0;
    const totalVenta = parseFloat(ventaActual.info.total || 0);
    const pagado = parseFloat(ventaActual.info.total_pagado || 0);
    const saldoPendiente = parseFloat((totalVenta - pagado).toFixed(2));
      const contenedor = document.getElementById('contenedorReferencia');
    const input = document.getElementById('inputReferencia');

    if (metodo === 'Saldo a Favor') {
        const montoAuto = Math.min(favorDisp, saldoPendiente);
        $('#inputMontoAbono').val(montoAuto.toFixed(2));
        $('#infoSaldo').html(`<i class="bi bi-info-circle-fill"></i> Disponible a favor: $${favorDisp.toFixed(2)}`)
                      .removeClass('bg-light text-dark').addClass('bg-info text-white');
    } else {
           if (metodo === 'Tarjeta' || metodo === 'Transferencia') {
        contenedor.style.display = 'block';
        input.required = true;
    } else {
        contenedor.style.display = 'none';
        input.required = false;
        input.value = '';
    }

        $('#inputMontoAbono').val(saldoPendiente.toFixed(2));
        $('#infoSaldo').text('Saldo máximo: $' + saldoPendiente.toFixed(2))
                      .removeClass('bg-info text-white').addClass('bg-light text-dark');
    }
}

/**
 * Validación en tiempo real (considerando límites de saldo a favor)
 */
$(document).on('input', '#inputMontoAbono', function() {
    const totalVenta = parseFloat(ventaActual.info.total || 0);
    const pagado = parseFloat(ventaActual.info.total_pagado || 0);
    const saldoPendiente = parseFloat((totalVenta - pagado).toFixed(2));
    
    const favorDisp = parseFloat($('#modal_favor_disponible').val()) || 0;
    const montoIngresado = parseFloat($(this).val()) || 0;
    const metodo = $('#selectMetodoPago').val();

    // El límite real depende del método
    let limiteReal = (metodo === 'Saldo a Favor') ? Math.min(saldoPendiente, favorDisp) : saldoPendiente;

    if (montoIngresado > (limiteReal + 0.01) || montoIngresado <= 0) {
        $(this).addClass('is-invalid text-danger');
        $('#infoSaldo').removeClass('bg-light text-dark bg-info').addClass('bg-danger text-white');
    } else {
        $(this).removeClass('is-invalid text-danger');
        if (metodo === 'Saldo a Favor') {
            $('#infoSaldo').removeClass('bg-danger text-white bg-light text-dark').addClass('bg-info text-white');
        } else {
            $('#infoSaldo').removeClass('bg-danger text-white bg-info').addClass('bg-light text-dark');
        }
    }
});

/**
 * Guarda el abono enviando los datos al controlador original
 */
async function guardarAbonoModal() {
    const monto = parseFloat($('#inputMontoAbono').val());
    const metodo = $('#selectMetodoPago').val();
    const favorDisp = parseFloat($('#modal_favor_disponible').val()) || 0;
      const refrencia = $('#inputReferencia').val()??'';
    
    // Validación de Saldo a Favor
    if (metodo === 'Saldo a Favor' && monto > (favorDisp + 0.01)) {
        return Swal.fire('Error', 'No tiene suficiente saldo a favor.', 'error');
    }

    if (!monto || monto <= 0) return Swal.fire('Error', 'Ingrese un monto válido', 'warning');

    // Lógica de fecha (manteniendo tu código original)
    const checkFechaManual = document.getElementById('checkFechaPersonalizada');
    const inputFechaManual = document.getElementById('inputFechaAbono');
    let fechaFinal = "";

    if (checkFechaManual && checkFechaManual.checked && inputFechaManual.value) {
        fechaFinal = inputFechaManual.value.replace('T', ' ') + ':00';
    }

    const fd = new FormData();
    fd.append('venta_id', ventaActual.info.id);
    fd.append('monto', monto);
    fd.append('metodo_pago', metodo);
    fd.append('fecha_pago', fechaFinal);
     fd.append('referencia', refrencia);


    try {
        Swal.fire({ title: 'Guardando...', didOpen: () => Swal.showLoading() });
        const res = await fetch(`${URL_CONTROLLER}?action=guardarAbono`, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.status === 'success') {
            modalAbonoObj.hide();
            Swal.fire('Éxito', 'Abono guardado', 'success');
            await verDetalle(ventaActual.info.id); // Recarga el detalle actual
            if (typeof getVentas === "function") getVentas(); // Recarga lista general
        } else {
            Swal.fire('Error', data.message || 'Error al guardar', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Error en el servidor', 'error');
    }
}

function toggleFechaAbono(show) {
    const container = document.getElementById('containerFechaAbono');
    const inputFecha = document.getElementById('inputFechaAbono');
    if (show) {
        container.style.display = 'block';
        if (!inputFecha.value) {
            const ahora = new Date();
            ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
            inputFecha.value = ahora.toISOString().slice(0, 16);
        }
    } else {
        container.style.display = 'none';
        inputFecha.value = '';
    }
}
</script>