<div class="modal fade" id="modalVerDeudaCompra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content  shadow-lg" style="border-radius:20px;">

            <div class="modal-header bg-dark text-white ">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-cash-stack me-2"></i>
                    Detalle de Deuda Pendiente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="p-3 mb-3  rounded-3 border">
                    <div class="fw-bold text-primary" id="deuda_folio" style="font-size: 1.1rem;">-</div>
                  <div class="text-body-secondary small" id="proveedor">-</div>
                    <div class="text-body-secondary small" id="deuda_fecha">-</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold ">TOTAL ORIGINAL COMPRA</label>
                        <input type="text" id="deuda_total" class="form-control " readonly style="font-weight: 600;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-danger">SALDO ACTUAL PENDIENTE</label>
                        <input type="text" id="deuda_pendiente" class="form-control fw-bold text-danger bg-danger-subtle" readonly>
                    </div>
                </div>

                <input type="hidden" id="deuda_compra_id">
                <input type="hidden" id="deuda_cuenta_id">

                <div  class="mb-3 p-3 rounded-3" style="display:none; background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                    <label class="form-label fw-bold">Monto a Liquidar / Abonar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white ">$</span>
                        <input type="hidden" id="pago_monto" class="form-control form-control-lg border-success" placeholder="0.00" step="any">
                    </div>
                    <small class="text-body-secondary mt-2 d-block">* Al saldar, se generará un registro automático en el historial de egresos.</small>
                </div>
            </div>

            <!-- <div class="modal-footer ">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success px-4 fw-bold" onclick="pagarDeudaCompra()">
                    <i class="bi bi-check-circle me-1"></i> Aplicar Pago
                </button> cargarCompras();
            </div> -->

        </div>
    </div>
</div>

<script>
    const URL_EGRESOS_BASE = "/myvet/app/controllers/egresosController.php";

/* =========================
   SAFE SET
========================= */
function setVal(id, value) {
    const el = document.getElementById(id);
    if (!el) {
        console.warn("Elemento no encontrado:", id);
        return;
    }
    el.value = value ?? '';
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (!el) {
        console.warn("Elemento no encontrado:", id);
        return;
    }
    el.textContent = value ?? '-';
}

/* =========================
   ABRIR MODAL DEUDA COMPRA
========================= */
window.abrirDeudaCompra = async function (compra_id) {

 

    try {
        const res = await fetch(`${URL_EGRESOS_BASE}?action=obtenerDeudaCompra&id=${compra_id}`);
        const json = await res.json();

        console.log("RESPUESTA:", json);

        if (!json.success || !json.data) {
            await ensureSwal();
            return Swal.fire('Sin datos', 'No hay deuda activa', 'info');
        }

        const d = json.data.data;
        console.log(d);

        // esperar DOM por seguridad
        await new Promise(r => setTimeout(r, 50));

        const folioEl = document.getElementById('deuda_folio');
        const provEl  = document.getElementById('proveedor');
        const fechaEl = document.getElementById('deuda_fecha');

        const totalEl = document.getElementById('deuda_total');
        const pendEl  = document.getElementById('deuda_pendiente');

        const idCompraEl = document.getElementById('deuda_compra_id');
        const idCuentaEl = document.getElementById('deuda_cuenta_id');

       

        if (!folioEl || !provEl || !fechaEl) {
            console.error("❌ Elementos del modal no existen o IDs duplicados");
            return;
        }

        // llenar datos
        folioEl.textContent = `Folio: ${d.folio ?? '-'}`;
        provEl.textContent  = d.beneficiario2 ?? '';
        fechaEl.textContent = d.fecha_compra ?? '-';

        totalEl.value = `$${parseFloat(d.total_compra || 0).toFixed(2)}`;
        pendEl.value  = `$${parseFloat(d.saldo_pendiente || 0).toFixed(2)}`;

        idCompraEl.value = d.id_referencia_origen ?? '';
        idCuentaEl.value = d.id ?? '';

        document.getElementById('pago_monto').value = '';

        // abrir modal seguro
        const modalEl = document.getElementById('modalVerDeudaCompra');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

    } catch (err) {
        console.error("ERROR:", err);
        await ensureSwal();
        Swal.fire('Error', 'No se pudo cargar la deuda', 'error');
    }
};
/* =========================
   PAGAR DEUDA
========================= */
window.pagarDeudaCompra = async function () {

    const cuenta_id = document.getElementById('deuda_cuenta_id')?.value;
    const monto = parseFloat(document.getElementById('pago_monto')?.value || 0);

    if (!monto || monto <= 0) {
        await ensureSwal();
        return Swal.fire('Error', 'Monto inválido', 'warning');
    }

    try {
        const res = await fetch(`${URL_EGRESOS_BASE}?action=pagarDeudaCompra`, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `cuenta_id=${cuenta_id}&monto=${monto}`
        });

        const json = await res.json();
if (json.success) {

    await ensureSwal();

    await Swal.fire({
        icon: 'success',
        title: 'Pago aplicado',
        timer: 1200,
        showConfirmButton: false
    });

    location.reload();

            const modalEl = document.getElementById('modalVerDeudaCompra');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

        } else {
            Swal.fire('Error', json.message || 'Error al pagar', 'error');
        }

    } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Fallo de red', 'error');
    }
};
</script>