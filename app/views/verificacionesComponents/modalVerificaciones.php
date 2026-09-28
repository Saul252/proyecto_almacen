<div class="modal fade" id="modalVerificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;  width: 95%; max-width: 1140px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <form id="formSolicitud" novalidate> <div class="modal-header   pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-wrench-adjustable fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Nuevo Registro de Verificacion</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para registrar el movimiento en el sistema</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-3">
                    <div class="row g-3 p-4 rounded-4  border align-items-end mb-2">

                       

                        <div class="col-md-4">
                            <label for="vehiculo_id" class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-car-front me-1"></i> Vehículo
                            </label>
                            <select class="form-select select2-modal" id="vehiculo_id" name="vehiculo_id" required>
                                <option value="">Seleccione vehículo...</option>
                                <?php foreach($vehiculos as $ve): ?>
                                <option value="<?= $ve['id'] ?>" data-almacen-id="<?= $ve['almacen_id'] ?>">
            <?= htmlspecialchars($ve['nombre']) ?> (<?= htmlspecialchars($ve['placas']) ?>) (<?= htmlspecialchars($ve['tipo']) ?>)
        </option>
          <?php endforeach; ?>
                            </select>
                        </div>

                       

                       

                        

                      

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-calendar3-event me-1"></i> Fecha verificacion
                            </label>
                            <input type="date" id="fecha_verificacion" name="fecha_verificacion" class="form-control" required>
                        </div><div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-calendar3-event me-1"></i> Fecha proxima verificacion
                            </label>
                            <input type="date" id="fecha_proxima_verificacion" name="fecha_proxima_verificacion" class="form-control" required>
                        </div>

                       

                        
                     
                    </div>
                </div>

                <div class="modal-footer  p-4 pt-2">
                    <button type="button" class="btn btn-light text-body-secondary fw-bold rounded-pill px-4 me-2" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Guardar Mantenimiento
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>

    $(document.ready).ready(function() {
    // Guardamos una copia de todas las opciones originales de vehículos
    const $vehiculoSelect = $('#vehiculo_id');
    const $vehiculoOptions = $vehiculoSelect.find('option').clone();

    $('#f_almacen').on('change', function() {
        const almacenId = $(this).val();

        // Limpiar el select de vehículos
        $vehiculoSelect.empty();

        if (almacenId) {
            // Filtrar y agregar solo la opción por defecto y las que coincidan con el almacén
            $vehiculoOptions.each(function() {
                const optionAlmacenId = $(this).data('almacen-id');
                
                // Incluye la opción inicial ("Seleccione vehículo...") o las que coincidan
                if (!optionAlmacenId || optionAlmacenId == almacenId) {
                    $vehiculoSelect.append($(this).clone());
                }
            });
        } else {
            // Si no hay almacén seleccionado, mostrar solo la opción por defecto
            $vehiculoSelect.append($vehiculoOptions.first().clone());
        }

        // Reinicializar Select2 para refrescar la lista visible
        $vehiculoSelect.val('').trigger('change.select2');
    });
});
$(document).ready(function() {
   
    // Escuchar el evento submit del formulario
    $('#formSolicitud').on('submit', async function(e) {
        e.preventDefault();

        // VALIDACIÓN MANUAL (Evita el bug de Select2 + HTML5 required)
       
        // Si pasa la validación, armamos el JSON
        const payload = {
           
            vehiculo_id: $('#vehiculo_id').val(),
           
            fecha_verificacion: $('#fecha_verificacion').val(),
            fecha_proxima_verificacion: $('#fecha_proxima_verificacion').val(),
            
        };

        console.log('JSON ENVIADO:', payload);

        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const resp = await fetch(`/myvet/app/controllers/verificacionesController.php?action=guardar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json; charset=utf-8'
                },
                body: JSON.stringify(payload)
            });

            const res = await resp.json().catch(() => ({ status: 'error', message: 'Error al interpretar respuesta del servidor.' }));
            console.log('RESPUESTA SERVIDOR:', res);

            if (res.status === 'success') {
                await Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                location.reload();
            } else {
                Swal.fire('Error', res.message, 'error');
            }

        } catch (error) {
            console.error('Error detectado:', error);
            Swal.fire('Error', 'Fallo de conexión o error crítico en el servidor', 'error');
        }
    });
});

// Función global para disparar el modal de forma limpia
function nuevoModalVerificacion() {
    $('#formSolicitud')[0].reset();
    $('.is-invalid').removeClass('is-invalid'); // Limpia errores visuales antiguos
    $('.select2-modal').val('').trigger('change'); // Limpia los Select2
    $('#modalVerificacion').modal('show');
}
</script>