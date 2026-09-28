<div class="modal fade" id="modalAgregarCategoria" tabindex="-1" aria-hidden="true" >
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white py-2" style="border-radius: 15px 15px 0 0;">
                <h6 class="modal-title small fw-bold">
                    <i class="bi bi-tag-fill me-2"></i>Nueva Categoría
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="formNuevaCategoria">
                    <div class="mac-select-container">
                        <label class="mac-label">Nombre</label>
                        <input type="text" id="inputNombreCategoria" class="form-control  bg-transparent p-0" 
                               placeholder="Ej. Herramientas" required autofocus>
                    </div>
                </form>
            </div>
            <div class="modal-footer  p-2">
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" onclick="ejecutarGuardarCategoria()">
                    <i class="bi bi-check-lg"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Ajuste para el oscurecimiento de fondo cuando hay múltiples modales */
#modalAgregarCategoria {
    background: rgba(0, 0, 0, 0.2); /* Oscurece un poco más el modal de abajo */
}
/* Evita que el modal de abajo se mueva al abrir este */
body.modal-open {
    overflow: hidden !important;
    padding-right: 0 !important;
}
</style>
<script>
    /**
 * Función principal para guardar la categoría desde el modal
 */
function ejecutarGuardarCategoria() {
    const nombre = $('#inputNombreCategoria').val().trim();
    const btn = $('#modalAgregarCategoria .btn-primary');

    if (nombre === "") {
        Swal.fire({
            icon: 'warning',
            title: 'Campo vacío',
            text: 'Por favor, escribe un nombre para la categoría.',
            target: document.getElementById('modalAgregarCategoria') // Importante para que el alert salga arriba
        });
        return;
    }

    // Bloquear botón para evitar doble envío
    const originalHtml = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        url: 'app/controllers/AlmacenController.php?action=guardarCategoria',
        type: 'POST',
        data: { nombre: nombre },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // 1. Agregar la nueva categoría al SELECT del modal de producto
                // Suponiendo que tu select de categorías tiene id="select_categoria_id"
                const nuevoOption = new Option(response.nombre, response.id, true, true);
                $('#select_categoria_id').append(nuevoOption).trigger('change');

                // 2. Cerrar solo este modal
                $('#modalAgregarCategoria').modal('hide');
                $('#formNuevaCategoria')[0].reset();

                // 3. Notificación rápida tipo Toast
                Swal.fire({
                    icon: 'success',
                    title: 'Categoría guardada',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    target: document.getElementById('modalAgregarCategoria')
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        },
        complete: function() {
            btn.prop('disabled', false).html(originalHtml);
        }
    });
}

// Permitir guardar al presionar "Enter" en el input

</script>
<script>
// Usamos JavaScript puro (document) para esperar a que la página cargue
document.addEventListener("DOMContentLoaded", function() {
    
    // Una vez que el DOM está listo, jQuery ($) ya existe
    $(document).on('keypress', '#inputNombreCategoria', function(e) {
        if(e.which === 13) {
            e.preventDefault();
            
            // Verificamos que la función exista antes de ejecutarla
            if (typeof ejecutarGuardarCategoria === "function") {
                ejecutarGuardarCategoria();
            }
        }
    });

});
</script>
