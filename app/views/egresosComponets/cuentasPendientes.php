<style>
    .swal2-container {
    z-index: 15000 !important;
}
</style>
<div class="modal fade" id="modalExcesoCompra" tabindex="-1" aria-hidden="true"
     style=" z-index:10000; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg"
             style="border-radius: 30px; background: rgba(255,255,255,0.95);">

            <div class="modal-header  pt-4 px-4">
                <h5 class="fw-bold text-dark">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>
                    Nueva Obligación por Exceso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formCuentasPorPagar" novalidate>

                <div class="modal-body p-4">

                    <div class="p-3 mb-4"
                         style="background:#f5f5f7;border-radius:20px;border:1px solid #e5e5e7;">
                        
                        <small class="text-body-secondary fw-bold d-block mb-1">
                            Producto / Concepto Original
                        </small>

                        <span id="txtProductoNombre"
                              class="fw-bold d-block mb-2">-</span>

                        <div class="d-flex justify-content-between">
                            <span id="txtUnidadBase"
                                  class="badge bg-white text-secondary border">
                                UNIDAD: -
                            </span>

                            <div class="text-end">
                                <small class="text-body-secondary d-block">
                                    PRECIO UNITARIO BASE
                                </small>
                                <div class="fw-bold text-primary"
                                     id="displayPrecio">$0.00</div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id_referencia_origen" id="id_referencia_hidden">
                    <input type="hidden" name="origen_tipo" id="origen_tipo_hidden">
                    <input type="hidden" name="id_proveedor" id="id_prov_hidden"">
                    <input type="hidden" name="id_almacen" id="id_almacen_hidden">
                    <input type="hidden" id="precio_unitario_input" value="0">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">
                            Beneficiario / Cobrador
                        </label>
                        <input type="text" name="beneficiario" id="beneficiario"
                               class="form-control bg-light " required>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-primary">
                                Cantidad de Más
                            </label>
                            <input type="number" step="any" id="cantidad_exceso"
                                   class="form-control bg-light "
                                   oninput="calcularDeudaOperativa()" required>
                        </div>

                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">
                                Monto
                            </label>
                            <input type="number" step="any"
                                   name="monto_total"
                                   id="monto_total"
                                   class="form-control bg-primary-subtle text-primary fw-bold"
                                   readonly value="0.00">
                        </div>
                    </div>

                </div>

                <div class="modal-footer  p-4">
                    <button type="button"
                            id="btnGuardarDeuda"
                            class="btn btn-primary w-100 fw-bold">
                        Confirmar y Registrar Deuda
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- 🔥 SWEET ALERT AUTO LOAD -->
<!-- ========================================================= -->
<script>
(function () {

    function cargarSweetAlert() {
        return new Promise((resolve) => {

            if (typeof Swal !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            script.async = true;

            script.onload = () => resolve();
            script.onerror = () => console.error('Error cargando SweetAlert');

            document.head.appendChild(script);
        });
    }

    window.ensureSwal = cargarSweetAlert;

})();
</script>

<!-- ========================================================= -->
<!-- 🔥 LÓGICA -->
<!-- ========================================================= -->
<script>

/* =========================
   CALCULO (SIN jQuery)
========================= */
function calcularDeudaOperativa() {
    const cant   = parseFloat(document.getElementById('cantidad_exceso').value) || 0;
    const precio = parseFloat(document.getElementById('precio_unitario_input').value) || 0;
    document.getElementById('monto_total').value = (cant * precio).toFixed(2);
}

/* =========================
   ABRIR MODAL (GLOBAL)
========================= */
window.abrirModalExceso = function(tipo, id) {

    fetch(`../controllers/egresosController.php?action=obtenerDetalleMovimientoConProveedores&tipo=${tipo}&id=${id}`)
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            ensureSwal().then(() => {
                Swal.fire('Error', data.message || 'No se pudo cargar', 'error');
            });
            return;
        }

        const cab = data.cabecera;
        const itm = data.items[0];

        document.getElementById('id_referencia_hidden').value = id;
        document.getElementById('origen_tipo_hidden').value = tipo;
        document.getElementById('id_almacen_hidden').value = cab.almacen_id || cab.id_almacen;
       
        document.getElementById('id_prov_hidden').value = cab.pid ?? '';
        document.getElementById('beneficiario').value =
            (tipo === 'compra') ? (cab.proveedor || '') : (cab.beneficiario || '');

        document.getElementById('txtProductoNombre').textContent =
            (tipo === 'compra') ? itm.producto_nombre : itm.descripcion;

        document.getElementById('txtUnidadBase').textContent =
            `UNIDAD: ${itm.unidad_medida || 'N/A'}`;

        const precioBase = parseFloat(itm.precio_unitario || 0);

        document.getElementById('precio_unitario_input').value = precioBase;
        document.getElementById('displayPrecio').textContent = `$${precioBase.toFixed(2)}`;

        document.getElementById('cantidad_exceso').value = '';
        document.getElementById('monto_total').value = '0.00';

        new bootstrap.Modal(document.getElementById('modalExcesoCompra')).show();
    })
    .catch(() => {
        ensureSwal().then(() => {
            Swal.fire('Error', 'Error de red', 'error');
        });
    });
};

/* =========================
   SERIALIZAR FORM (SIN jQuery)
========================= */
function serializeForm(form) {
    const formData = new FormData(form);
    return new URLSearchParams(formData).toString();
    
}

/* =========================
   CLICK GUARDAR (FIX REAL)
========================= */
document.addEventListener('click', async function(e) {

    if (e.target && e.target.id === 'btnGuardarDeuda') {

        e.preventDefault();

        console.log("CLICK OK");

        await ensureSwal();

        const form = document.getElementById('formCuentasPorPagar');
        const btn  = document.getElementById('btnGuardarDeuda');
        const montoTotal = parseFloat(document.getElementById('monto_total').value);

        if (isNaN(montoTotal) || montoTotal <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debes ingresar un monto válido'
            });
            return;
        }

        Swal.fire({
            title: '¿Confirmar?',
            text: `Total: $${montoTotal.toFixed(2)}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,

            preConfirm: () => {

                btn.disabled = true;

                return fetch('/myvet/app/controllers/egresosController.php?action=registrarDeudaPorExceso', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: serializeForm(form)
                })
              .then(res => res.json())
.catch((error) => {
    console.error('ERROR FETCH:', error);

    Swal.showValidationMessage(
        'Error de conexión o servidor'
    );

    btn.disabled = false;
});
            },

            allowOutsideClick: () => !Swal.isLoading()

        }).then((result) => {

            btn.disabled = false;

            if (!result.value) return;

            const res = result.value;

            if (res.success) {

                Swal.fire({
                    icon: 'success',
                    title: 'Guardado correctamente',
                    timer: 1500,
                    showConfirmButton: false
                });

                const modalEl = document.getElementById('modalExcesoCompra');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (typeof tableEgresos !== 'undefined') {
                    tableEgresos.ajax.reload(null, false);
                }

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Error desconocido'
                });
            }
        });
    }

});
</script>