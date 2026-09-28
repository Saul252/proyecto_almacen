<!-- Modal Solicitud Cancelación -->
<div class="modal fade" id="modalSolicitudCancelacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg">

            <!-- Encabezado -->
            <div class="modal-header bg-body-tertiary align-items-center py-3">
                <h6 class="modal-title fw-bold m-0">
                    Solicitud de Cancelación
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Formulario -->
            <form id="formSolicitudCancelacion" onsubmit="enviarSolicitudCancelacion(event)">
                
                <div class="modal-body p-4">
                    <!-- Campo ID Venta (Envía 'id_venta' como entero) -->
                    <div class="mb-3">
                        <label for="id_venta" class="form-label text-secondary small fw-semibold text-uppercase">ID Venta</label>
                        <input type="text" id="id_venta" name="id_venta" class="form-control " readonly>
                    </div>

                    <!-- Campo Razón (Visible Text) -->
                    <div class="mb-3">
                        <label for="cancel_razon" class="form-label text-secondary small fw-semibold text-uppercase">Razón de Cancelación</label>
                        <input type="text" id="cancel_razon" name="razon" class="form-control" placeholder="Escribe el motivo..." required autocomplete="off">
                    </div>
                </div>

                <!-- Pie de modal / Botones -->
                <div class="modal-footer bg-body-tertiary border-top-0 py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Volver</button>
                    <button type="submit" class="btn btn-dark btn-sm px-3">Enviar Solicitud</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
/**
 * Abre el modal de cancelación e inyecta los datos
 * @param {string|number} ventaId - ID numérico de la venta
 */
function abrirModalSolicitudCancelacion(ventaId) {
    // 1. Asignar ID de la venta
    document.getElementById('id_venta').value = ventaId ?? '';
  
    // 2. Limpiar el campo de razón
    document.getElementById('cancel_razon').value = '';

    // 3. Abrir el modal usando Bootstrap
    const modalElement = document.getElementById('modalSolicitudCancelacion');
    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalInstance.show();
}

/**
 * Cierra el modal de cancelación
 */
function cerrarModalCancelacion() {
    const modalElement = document.getElementById('modalSolicitudCancelacion');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }
}

/**
 * Maneja el envío del formulario mediante AJAX/Fetch
 */async function enviarSolicitudCancelacion(event) {
    event.preventDefault();

    // FormData envía los inputs directamente a $_POST en PHP
    const formData = new FormData(event.target);
try {
        const response = await fetch('/myvet/app/controllers/ventasHistorialController.php?action=solicitarCancelacion', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        // Detectar si el tema actual es oscuro
        const esModoOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.body.classList.contains('dark-mode');

        // Configuración de colores para SweetAlert
        const swalConfig = {
            background: esModoOscuro ? '#1e293b' : '#ffffff',
            color: esModoOscuro ? '#f8fafc' : '#1e2022',
            confirmButtonColor: esModoOscuro ? '#3b82f6' : '#1e2022'
        };

        if (data.status === 'success') {
            cerrarModalCancelacion();

            Swal.fire({
                ...swalConfig,
                icon: 'success',
                title: '¡Solicitud enviada!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // location.reload(); // Opcional
            });

        } else {
            Swal.fire({
                ...swalConfig,
                icon: 'warning',
                title: 'Atención',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Error al procesar:', error);

        const esModoOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.body.classList.contains('dark-mode');

        Swal.fire({
            background: esModoOscuro ? '#1e293b' : '#ffffff',
            color: esModoOscuro ? '#f8fafc' : '#1e2022',
            confirmButtonColor: esModoOscuro ? '#3b82f6' : '#1e2022',
            icon: 'error',
            title: 'Error de conexión',
            text: 'Ocurrió un error al comunicarse con el servidor.'
        });
    }
}
</script>