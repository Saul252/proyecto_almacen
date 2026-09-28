<div class="modal fade" id="modalCorteCaja" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 15px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white  py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-cash-register text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Finalizar Corte de Caja</h5>
                        <small class="text-light opacity-75">Confirma los totales antes de guardar</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4 bg-light">

                <form id="formGuardarCorte">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">FECHA</label>
                            <input type="date" class="form-control form-control-sm"
                                   name="fecha_corte"
                                   value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ALMACÉN</label>
                            <select id="almacen_id_modal" class="form-select form-select-sm">
                                <?php foreach ($listaAlmacenes as $almacen): ?>
                                    <option value="<?= $almacen['id'] ?>">
                                        <?= $almacen['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- RESUMEN -->
                    <div class="bg-white p-3 rounded shadow-sm mb-3">

                      
<div id="contenedor-egresos"></div>
                        <hr>

                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">TOTAL</span>
                            <strong class="text-primary" id="modal-total-txt">$0.00</strong>
                        </div>

                    </div>

                    <textarea name="observaciones" class="form-control" rows="2"
                              placeholder="Observaciones..."></textarea>

                    <input type="hidden" name="accion" value="guardarCorte">

                </form>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer ">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnConfirmarCorte" class="btn btn-primary">
                    Guardar Corte
                </button>
            </div>

        </div>
    </div>
</div>
<script>
const url = "/myvet/app/controllers/corteCajaController.php";

document.addEventListener('DOMContentLoaded', function() {

    const modalElement = document.getElementById('modalCorteCaja');
    const myModal      = new bootstrap.Modal(modalElement);
    const btnGuardar   = document.getElementById('btnConfirmarCorte');

    const selectPrincipal = document.getElementById('almacen_id');
    const selectModal     = document.getElementById('almacen_id_modal');

    // ==========================================
    // 🔥 VARIABLES GLOBALES (Scope del archivo)
    // ==========================================
    let _efectivo = 0, _tarjeta = 0, _transferencia = 0;
    let _gastoEfectivo = 0, _gastoTarjeta = 0, _gastoTransferencia = 0;
    let _compras = 0;
    let _saldoInicialEfectivo = 0, _saldoInicialTarjeta = 0, _saldoInicialTransferencia = 0;
    let _saldoTotal = 0;

    const limpiarNumero = (id) => {
        const el = document.getElementById(id);
        if (!el) return 0;
        return parseFloat((el.innerText || '0').replace(/[^0-9.-]+/g, "")) || 0;
    };
      window._efectivo = 0;
    window._tarjeta = 0;
    window._transferencia = 0;

    window._saldoInicialEfectivo = 0;
    window._saldoInicialTarjeta = 0;
    window._saldoInicialTransferencia = 0;

    // ==========================================
    // 🔥 FUNCIÓN PRINCIPAL DE CÁLCULO
    // ==========================================
    function actualizarModal() {
        // --- 1. RECUPERACIÓN DE DATOS (Variables Globales window) ---
        const saldoI = window._saldoInicialDesglose || { efectivo: 0, tarjeta: 0, transferencia: 0, total: 0 };
        const gastos = window._gastosMetodo || { EFECTIVO: 0, TARJETA: 0, TRANSFERENCIA: 0 };
        const compras = window._comprasMetodo || { EFECTIVO: 0, TARJETA: 0, TRANSFERENCIA: 0 };
        // Asignación a variables globales del script
        _saldoInicialEfectivo      = saldoI.efectivo;
        _saldoInicialTarjeta       = saldoI.tarjeta;
        _saldoInicialTransferencia = saldoI.transferencia;
        _saldoTotal                = saldoI.total;

        _gastoEfectivo      = parseFloat(gastos.EFECTIVO || 0);
        _gastoTarjeta       = parseFloat(gastos.TARJETA || 0);
        _gastoTransferencia = parseFloat(gastos.TRANSFERENCIA || 0);

        _comprasEfectivo      = parseFloat(compras.EFECTIVO || 0);
        _comprasTarjeta       = parseFloat(compras.TARJETA || 0);
        _comprasTransferencia = parseFloat(compras.TRANSFERENCIA || 0);

        // Ventas y Abonos (desde el HTML de la interfaz principal)
        const vEfec = limpiarNumero('res-v-efectivo');
        const aEfec = limpiarNumero('res-a-efectivo');
        const vTarj = limpiarNumero('res-v-tarjeta');
        const aTarj = limpiarNumero('res-a-tarjeta');
        const vTran = limpiarNumero('res-v-trans');
        const aTran = limpiarNumero('res-a-trans');
        const saldofavor = limpiarNumero('res-saldo-favor');

        // Totales calculados para envío (Ingresos brutos del turno)
        _efectivo      = vEfec + aEfec;
        _tarjeta       = vTarj + aTarj;
        _transferencia = vTran + aTran;

        const totalGastos = _gastoEfectivo + _gastoTarjeta + _gastoTransferencia;
        const totalCompras = _comprasEfectivo + _comprasTarjeta + _comprasTransferencia;

        // --- 2. CÁLCULOS DE BALANCE (Neto Final) ---
        const balanceEfectivo = (_saldoInicialEfectivo + _efectivo) -( _gastoEfectivo + _comprasEfectivo);
        const balanceTarjeta  = (_saldoInicialTarjeta + _tarjeta) - (_gastoTarjeta + _comprasTarjeta);
        const balanceTrans    = (_saldoInicialTransferencia + _transferencia) - (_gastoTransferencia + _comprasTransferencia ) ;
        const totalFinal      = balanceEfectivo + balanceTarjeta + balanceTrans;

        // ==================================================
        // 🔥 BLOQUE DE CONSOLA (AUDITORÍA TÉCNICA)
        // ==================================================
        console.group("%c📊 REPORTE TÉCNICO DE CORTE", "background: #222; color: #bada55; padding: 5px; border-radius: 5px;");
        
        console.log("%c[1] APERTURA:", "font-weight: bold; color: #5856d6;");
        console.table({
            "Efectivo Inicial": { Monto: _saldoInicialEfectivo },
            "Tarjeta Inicial":  { Monto: _saldoInicialTarjeta },
            "Transf. Inicial":  { Monto: _saldoInicialTransferencia },
            "TOTAL APERTURA":   { Monto: _saldoTotal }
        });

        console.log("%c[2] ENTRADAS (DETALLE):", "font-weight: bold; color: #28a745;");
        console.table({
            "Venta Efec": { Monto: vEfec },
            "Abono Efec": { Monto: aEfec },
            "Venta Tarj": { Monto: vTarj },
            "Abono Tarj": { Monto: aTarj },
            "Venta Tran": { Monto: vTran },
            "Abono Tran": { Monto: aTran },
             "saldo favor": { Monto: saldofavor },
            "TOTAL ENVIAR": { Monto: (_efectivo + _tarjeta + _transferencia) }
        });

        console.log("%c[3] SALIDAS:", "font-weight: bold; color: #ff3b30;");
        console.table({
            "Gastos (Efec)":  { Monto: _gastoEfectivo },
            "Gastos (Tarj)":  { Monto: _gastoTarjeta },
            "Gastos (Tran)":  { Monto: _gastoTransferencia },
           
            "TOTAL SALIDAS":  { Monto: totalGastos  }
        });
        console.log("%c[3] COMPRAS", "font-weight: bold; color: #ff3b30;");
        console.table({
            "Compras (Efec)":  { Monto: _comprasEfectivo },
            "Compras (Tarj)":  { Monto: _comprasTarjeta },
            "Compras (Tran)":  { Monto: _comprasTransferencia },
            "Compras":        { Monto: _compras },
            "TOTAL SALIDAS":  { Monto: totalCompras}
        });

        console.log("%c[4] RESULTADO FINAL:", "font-weight: bold; color: #ff9500;");
        console.log(`%cTOTAL NETO EN CAJA: $${totalFinal.toFixed(2)}`, "font-size: 14px; font-weight: bold; color: #fff; background: #007aff; padding: 2px 5px;");
        console.groupEnd();

        // --- 3. ACTUALIZAR INTERFAZ DEL MODAL ---
        const safeSetText = (id, text) => {
            const el = document.getElementById(id);
            if (el) el.innerText = text;
        };

        safeSetText('modal-efectivo-txt', '$' + balanceEfectivo.toLocaleString('es-MX', {minimumFractionDigits:2}));
        safeSetText('modal-tarjeta-txt',  '$' + balanceTarjeta.toLocaleString('es-MX', {minimumFractionDigits:2}));
        safeSetText('modal-transferencia-txt', '$' + balanceTrans.toLocaleString('es-MX', {minimumFractionDigits:2}));
        safeSetText('modal-total-txt',    '$' + totalFinal.toLocaleString('es-MX', {minimumFractionDigits:2}));

        const $egresoBox = document.getElementById('contenedor-egresos');
        if ($egresoBox) {
            $egresoBox.innerHTML = `
                <div class="p-2 mb-2 border-bottom">
                    <small class="text-uppercase fw-bold text-secondary" style="font-size: 10px;">1. Saldo Inicial (Apertura)</small>
                    <div class="d-flex justify-content-between"><span>Efectivo Inicial</span><span class="fw-bold">$${_saldoInicialEfectivo.toLocaleString('es-MX')}</span></div>
                    <div class="d-flex justify-content-between"><span>Tarjeta Inicial</span><span class="fw-bold">$${_saldoInicialTarjeta.toLocaleString('es-MX')}</span></div>
                    <div class="d-flex justify-content-between"><span>Trans Inicial</span><span class="fw-bold">$${_saldoInicialTransferencia.toLocaleString('es-MX')}</span></div>
                </div>

                <div class="p-2 mb-2 border-bottom">
                    <small class="text-uppercase fw-bold text-success" style="font-size: 10px;">2. Entradas (Ventas + Abonos)</small>
                    <div class="d-flex justify-content-between small text-body-secondary"><span>Total Efectivo</span><span>+$${_efectivo.toLocaleString('es-MX')}</span></div>
                    <div class="d-flex justify-content-between small text-body-secondary"><span>Total Tarjeta</span><span>+$${_tarjeta.toLocaleString('es-MX')}</span></div>
                    <div class="d-flex justify-content-between small text-body-secondary"><span>Total Transf.</span><span>+$${_transferencia.toLocaleString('es-MX')}</span></div>
                </div>

                <div class="p-2 mb-2 border-bottom bg-danger bg-opacity-10 rounded text-danger">
                    <small class="text-uppercase fw-bold" style="font-size: 10px;">3. Salidas (Gastos + Compras)</small>
                    <div class="d-flex justify-content-between small"><span>Gastos Totales</span><span>-$${totalGastos.toLocaleString('es-MX')}</span></div>
                    <div class="d-flex justify-content-between small"><span>Compras</span><span>-$${totalCompras.toLocaleString('es-MX')}</span></div>
                </div>

                <div class="p-2 bg-dark text-white rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small fw-bold">FINAL EN CAJA:</span>
                        <h4 class="m-0 fw-bold text-warning">$${totalFinal.toLocaleString('es-MX')}</h4>
                    </div>
                </div>
                
                
            `;
        }
    }

    // ==========================================
    // 🔥 EVENTOS DEL MODAL
    // ==========================================
    modalElement.addEventListener('show.bs.modal', function () {
        selectModal.value = selectPrincipal.value;
        actualizarModal();
    });

    selectModal.addEventListener('change', function() {
        selectPrincipal.value = this.value;
        $('#almacen_id').trigger('change');
        setTimeout(() => actualizarModal(), 600);
    });

    // ==========================================
    // 🔥 GUARDAR CORTE (ENVÍO AL PHP)
    // ==========================================
    btnGuardar.addEventListener('click', function() {
        actualizarModal(); 

        const formElement = document.getElementById('formGuardarCorte');
        const formData = new FormData(formElement);

        // Seteamos los valores calculados
        formData.set('accion', 'guardarCorte');
        formData.set('almacen_id', selectModal.value);
        formData.set('total_efectivo', _efectivo);
        formData.set('total_tarjeta', _tarjeta);
        formData.set('total_transferencia', _transferencia);
        formData.set('abono_efectivo', limpiarNumero('res-a-efectivo'));
        formData.set('abono_tarjeta', limpiarNumero('res-a-tarjeta'));
        formData.set('abono_transferencia', limpiarNumero('res-a-trans'));
        formData.set('saldo_inicial_efectivo', _saldoInicialEfectivo);
        formData.set('saldo_inicial_tarjeta', _saldoInicialTarjeta);
        formData.set('saldo_inicial_transferencia', _saldoInicialTransferencia);
        formData.set('gasto_efectivo', _gastoEfectivo);
        formData.set('gasto_tarjeta', _gastoTarjeta);
        formData.set('gasto_transferencia', _gastoTransferencia);
        formData.set('compras_efectivo', _comprasEfectivo);
        formData.set('compras_tarjeta', _comprasTarjeta);
        formData.set('compras_transferencia', _comprasTransferencia);
        formData.set('deuda_pendiente', limpiarNumero('res-deuda'));
        formData.set('saldo_favor', limpiarNumero('res-saldo-favor'));

        // ==================================================
        // 📤 LOG 1: VER QUÉ ESTÁ SALIENDO (CLIENTE)
        // ==================================================
        console.group("%c📤 ENVIANDO DATOS AL CONTROLADOR", "color: #fff; background: #007aff; padding: 5px;");
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: %c${value}`, "font-weight: bold; color: #000;");
        }
        console.groupEnd();

        btnGuardar.disabled = true;
        btnGuardar.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Guardando...`;

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Verificamos si la respuesta del servidor es OK (200)
            console.log(`%cStatus de Red: ${response.status} ${response.statusText}`, "color: gray; italic;");
            return response.json();
        })
        .then(data => {
            // ==================================================
            // 📥 LOG 2: VER QUÉ RESPONDIÓ PHP (SERVIDOR)
            // ==================================================
            console.group("%c📥 RESPUESTA RECIBIDA DEL SERVIDOR", "color: #fff; background: #28a745; padding: 5px;");
            console.log("Datos crudos retornados:", data);
            console.groupEnd();

            if (data.status === 'success') {
                Swal.fire("¡Éxito!", "El corte de caja se ha guardado correctamente", "success")
                .then(() => location.reload());
            } else {
                // Si el status es error, el log de arriba nos dirá por qué según PHP
                throw new Error(data.message || "Error en el servidor");
            }
        })
        .catch(err => {
            console.error("%c❌ ERROR EN LA PETICIÓN:", "font-weight: bold; color: red;", err);
            Swal.fire("Error", err.message, "error");
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = "Confirmar y Guardar Corte";
        });
    });
});
</script>