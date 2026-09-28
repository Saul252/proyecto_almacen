 <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h6 class="modal-title">Nueva Categoría</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formRapidoCategoria">
                        <div class="mb-3">
                            <select name="almacen_id_compra" id="almacen_id_compra"
                                    class="form-select  shadow-sm rounded-3 py-2" required>
                                   
                                      <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?><?= $a['id'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <label class="form-label small">Nombre de la Categoría</label>
                            <input type="text" id="nombre_cat_rapida" class="form-control"
                                placeholder="Ej: Herramientas" required>
                        </div>
                        <button type="button" onclick="guardarCategoriaRapida()" class="btn btn-success w-100">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    function abrirSubModalCategoria() {
        // Simplemente abrimos el modal de categoría sin cerrar el anterior
        const myModal = new bootstrap.Modal(document.getElementById('modalNuevaCategoria'), {
            backdrop: 'static', // Evita que se cierre el de atrás si haces clic fuera
            keyboard: false
        });
        myModal.show();
    }

  function guardarCategoriaRapida() {

    const input = document.getElementById('nombre_cat_rapida');
    const nombre = input.value.trim();
     const inputAlmacen = document.getElementById('almacen_id_compra');
    const almacen = inputAlmacen.value;

    if (!nombre) {
        return Swal.fire('Error', 'Escribe un nombre', 'error');
    }

    // 🔥 USAR FORMDATA (compatible con PHP $_POST)
    const formData = new FormData();
    formData.append('nombre', nombre);
formData.append('almacen', almacen);

    fetch('/myvet/app/controllers/egresosController.php?action=guardarCategoria', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        console.log(data);

        if (data.status === 'success') {

            const id = data.id; // 🔥 CORRECTO

            // 🔥 ACTUALIZAR TODOS LOS SELECTS
            document.querySelectorAll('select[name="categoria_id"]').forEach(select => {

                // evitar duplicados
                const existe = Array.from(select.options)
                    .some(opt => opt.value == id);

                if (!existe) {
                    const nuevaOpcion = new Option(data.nombre, id);
                    select.add(nuevaOpcion);
                }

                // seleccionar nueva categoría
                select.value = String(id);
            });

            // 🔥 CERRAR MODAL
            const modal = bootstrap.Modal.getOrCreateInstance(
                document.getElementById('modalNuevaCategoria')
            );
            modal.hide();

            // 🔥 limpiar input
            input.value = '';

            // 🔥 asegurar scroll del modal padre
            setTimeout(() => {
                if (document.querySelectorAll('.modal.show').length > 0) {
                    document.body.classList.add('modal-open');
                }
            }, 300);

            // 🔥 mensaje
            Swal.fire({
                title: '¡Éxito!',
                text: 'Categoría guardada y seleccionada.',
                icon: 'success',
                timer: 1200,
                showConfirmButton: false
            });

        } else {
            Swal.fire('Error', data.message || 'Error desconocido', 'error');
        }
    })
    .catch(error => {
        console.error(error);
        Swal.fire('Error', 'No se pudo procesar la categoría', 'error');
    });
}
   </script>