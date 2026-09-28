<div class="modal fade" id="modalNuevoProveedorRapido" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg rounded-4">

            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2"></i>Nuevo Proveedor Directo
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body ">
                <form id="formProvRapido">
                    <div class="row g-3">

                        <!-- 🔥 NOMBRE COMERCIAL (FALTABA) -->
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nombre Comercial *</label>
                            <input type="text" name="nombre_comercial" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <select name="almacen_id" id="almacen_id"
                                class="form-select <?= $_SESSION['almacen_id']==0 ? '' : '' ?>"
                                <?= $_SESSION['almacen_id'] != 0 ? 'disabled' : '' ?> required>

                                <?php if ($_SESSION['almacen_id']==0): ?>
                                <option value="">Seleccionar ubicación...</option>
                                <?php endif; ?>

                                <?php foreach($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"
                                    <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                    <?= $a['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Razón Social (Opcional)</label>
                            <input type="text" name="razon_social" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">RFC</label>
                            <input type="text" name="rfc" class="form-control" maxlength="13">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contacto interno</label>
                            <input type="text" name="contacto" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono secundario</label>
                            <input type="tel" name="telefono2" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Extencion</label>
                            <input type="tel" name="extencion" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Dirección</label>
                            <textarea class="form-control text-uppercase" id="direccion" name="direccion"></textarea>
                        </div>
                           <div class="col-md-6">
                            <label class="form-label small fw-bold">Numero Exterior</label>
                            <input type="tel" name="numeroext" class="form-control text-uppercase">
                        </div>
                           <div class="col-md-6">
                            <label class="form-label small fw-bold">Numero Interior</label>
                            <input type="tel" name="numeroint" class="form-control text-uppercase">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Colonia</label>
                            <input type="text" class="form-control text-uppercase" id="colonia" name="colonia">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ciudad</label>
                            <input type="text" class="form-control text-uppercase" id="ciudad" name="ciudad">
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer ">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>

                <!-- 🔥 PASAMOS EVENT -->
                <button type="button" class="btn btn-primary px-4" onclick="guardarProvRapido(event)">
                    <i class="bi bi-save me-2"></i>Registrar y Seleccionar
                </button>
            </div>

        </div>
    </div>
</div>
<script>
function abrirModalNuevoProveedor() {
    const modal = new bootstrap.Modal(document.getElementById('modalNuevoProveedorRapido'));
    modal.show();

    // 🔥 focus automático elegante
    setTimeout(() => {
        document.querySelector('#formProvRapido input[name="nombre_comercial"]').focus();
    }, 300);
}


function guardarProvRapido(e) {

    const form = document.getElementById('formProvRapido');
    const btn = e.target;

    // 🔥 VALIDACIÓN REAL
    const nombre = form.nombre_comercial.value.trim();
    if (!nombre) {
        Swal.fire('Atención', 'El nombre comercial es obligatorio', 'warning');
        return;
    }

    const formData = new FormData(form);

    // 🔥 UI loading
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    fetch('/myvet/app/controllers/egresosController.php?action=guardarProveedor', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error("Respuesta inválida del servidor");
            return res.json();
        })
        .then(data => {

            if (data.success) {

                Swal.fire({
                    icon: 'success',
                    title: 'Proveedor registrado',
                    text: 'Se agregó y seleccionó automáticamente',
                    timer: 1800,
                    showConfirmButton: false
                })// 🔄 recarga automática
  const select_proveedor = document.getElementById('proveedor_id');
            
            // --- LÓGICA DE ACTUALIZACIÓN DINÁMICA ---
            if (select_proveedor) {
       
               
                    const nombre = formData.get('nombre_comercial');
                    const option = new Option(nombre, data.id, true, true);
                    
                    // Inyectar metadatos (VITAL para facturación)
                    option.setAttribute('data-rfc', formData.get('rfc'));
                    option.setAttribute('data-rs', formData.get('razon_social'));
                    option.setAttribute('data-cp', formData.get('codigo_postal'));
                    option.setAttribute('data-regimen', formData.get('regimen_fiscal'));
                    
                  $('#proveedor_id')
    .append(option)
    .val(data.id)
    .trigger('change');
              
            }
 bootstrap.Modal.getInstance(document.getElementById('modalNuevoProveedorRapido')).hide();
            form.reset();
                // 🔥 actualizar select
            actualizarListaProveedores(nombre);
            if (typeof cargarProveedoresSelect === 'function') {
    cargarProveedoresSelect(); // Se ejecuta si existe
} 

            } else {
                Swal.fire('Error', data.message || 'No se pudo guardar', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', err.message || 'Fallo de conexión', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-2"></i>Registrar y Seleccionar';
        });
}
</script>
<script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>