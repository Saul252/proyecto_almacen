<?php 
    // Variables que vienen de tu controller
    $favor = $estatusCliente['saldo_a_favor'] ?? 0;
    $contra = $estatusCliente['saldo_en_contra'] ?? 0;
?>

<div class="modal fade" id="modalAbono" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg ">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title">Registrar Abono</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_id_venta" name="id_venta" value="">
                <input type="hidden" id="modal_saldo_max">
                <input type="hidden" id="modal_cliente_id">
                
                <input type="hidden" id="modal_favor_disponible" value="<?= $favor ?>">

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
                        
                        <?php if ($favor > 0.01): ?>
                            <option id="optionSaldoFavor" value="Saldo a Favor" style="background-color: #e3f2fd; color: #0d6efd;">
                                Usar Saldo a Favor ($<?= number_format($favor, 2) ?>)
                            </option>
                        <?php endif; ?>
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

                <div id="containerFechaAbono" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Fecha y Hora</label>
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
let modalAbonoObj;

$(document).ready(function() {
    modalAbonoObj = new bootstrap.Modal(document.getElementById('modalAbono'));
});

/**
 * Lógica para auto-completar el monto si elige Saldo a Favor
 */
function verificarMetodoPago(metodo) {
    const favorDisponible = parseFloat($('#modal_favor_disponible').val()) || 0;
    const saldoPendienteVenta = parseFloat($('#modal_saldo_max').val()) || 0;
     const contenedor = document.getElementById('contenedorReferencia');
    const input = document.getElementById('inputReferencia');

    if (metodo === 'Saldo a Favor') {
        // Ponemos el menor entre lo que debe y lo que tiene a favor
        const montoAuto = Math.min(favorDisponible, saldoPendienteVenta);
        
        $('#inputMontoAbono').val(montoAuto.toFixed(2));
        $('#infoSaldo').html(`<i class="bi bi-info-circle-fill"></i> Máximo disponible: $${favorDisponible.toFixed(2)}`)
                      .removeClass('bg-light text-dark').addClass('bg-info text-white');
    } else {
        // Resetear al saldo total de la venta
        $('#inputMontoAbono').val(saldoPendienteVenta.toFixed(2));
        $('#infoSaldo').text('Saldo máximo: $' + saldoPendienteVenta.toFixed(2))
                      .removeClass('bg-info text-white').addClass('bg-light text-dark');
    }
    if (metodo === 'Tarjeta' || metodo === 'Transferencia') {
        contenedor.style.display = 'block';
        input.required = true;
    } else {
        contenedor.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

/**
 * Abre el modal y configura los límites
 */
function abrirFlujoAbono(idVenta, cliente_id, folio, saldoPendiente) {
    if (saldoPendiente <= 0) {
        Swal.fire('Venta Liquidada', 'Sin saldo pendiente.', 'success');
        return;
    }

    // Seteamos valores de la venta
    $('#modal_id_venta').val(idVenta);
    $('#modal_cliente_id').val(cliente_id);
    $('#modal_saldo_max').val(saldoPendiente);
    
    // UI
    $('.modal-title').text('Abonar a Folio: ' + folio);
    $('#inputMontoAbono').val(saldoPendiente.toFixed(2));
    $('#infoSaldo').text('Saldo máximo: $' + saldoPendiente.toFixed(2))
                  .removeClass('bg-danger text-white bg-info').addClass('bg-light text-dark');
    $('#selectMetodoPago').val('Efectivo'); 

    modalAbonoObj.show();

    const modalEl = document.getElementById('modalAbono');
    modalEl.addEventListener('shown.bs.modal', () => {
        document.getElementById('inputMontoAbono').focus();
        document.getElementById('inputMontoAbono').select();
    }, { once: true });
}

/**
 * Validación en tiempo real (No permite exceder saldo a favor si está seleccionado)
 */
$(document).on('input', '#inputMontoAbono', function() {
    const saldoMax = parseFloat($('#modal_saldo_max').val()) || 0;
    const favorDisp = parseFloat($('#modal_favor_disponible').val()) || 0;
    const montoIngresado = parseFloat($(this).val()) || 0;
    const metodo = $('#selectMetodoPago').val();

    let limiteReal = (metodo === 'Saldo a Favor') ? Math.min(saldoMax, favorDisp) : saldoMax;

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
 * Función Guardar (Sin cambios, solo usa los nuevos valores)
 */
async function guardarAbonoModal() {
    const idVenta = $('#modal_id_venta').val();
    const idCliente = $('#modal_cliente_id').val();
    const monto = parseFloat($('#inputMontoAbono').val());
    const metodo = $('#selectMetodoPago').val();
     const refrencia = $('#inputReferencia').val()??'';
    const favorDisp = parseFloat($('#modal_favor_disponible').val()) || 0;
    const saldoMax = parseFloat($('#modal_saldo_max').val());
    
    if (!monto || monto <= 0) return Swal.fire('Error', 'Monto inválido', 'warning');

    if (metodo === 'Saldo a Favor' && monto > (favorDisp + 0.01)) {
        return Swal.fire('Saldo Insuficiente', `Solo tienes $${favorDisp.toFixed(2)} a favor.`, 'error');
    }

    const checkFechaManual = document.getElementById('checkFechaPersonalizada');
    const inputFechaManual = document.getElementById('inputFechaAbono');
    let fechaFinal = "";
    if (checkFechaManual && checkFechaManual.checked && inputFechaManual.value) {
        fechaFinal = inputFechaManual.value.replace('T', ' ') + ':00';
    }

    const fd = new FormData();
    fd.append('venta_id', idVenta);
    fd.append('cliente_id', idCliente);
    fd.append('monto', monto);
    fd.append('metodo_pago', metodo);
    fd.append('fecha_pago', fechaFinal);
    fd.append('referencia', refrencia);

    try {
        Swal.fire({ title: 'Procesando...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });
        const res = await fetch('/myvet/app/controllers/clienteExpedienteController.php?action=guardarAbono', {
            method: 'POST',
            body: fd
        });
        const text = await res.text();
        const data = JSON.parse(text);
        if (data.status === 'success' || data.success) {
            modalAbonoObj.hide();
            Swal.fire('¡Éxito!', data.message || 'Abono registrado', 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message || 'Error al guardar', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Error técnico en el servidor.', 'error');
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