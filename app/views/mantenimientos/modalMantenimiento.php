<div class="modal fade" id="modalMantenimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;  width: 95%; max-width: 1140px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <form id="formSolicitud" novalidate> <div class="modal-header   pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-wrench-adjustable fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 ">Nuevo Registro de Mantenimiento</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para registrar el movimiento en el sistema</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-3">
                    <div class="row g-3 p-4 rounded-4  border align-items-end mb-2">

                        <div class="col-md-4">
    <label class="form-label small fw-bold text-secondary mb-2">
        <i class="bi bi-geo-alt me-1"></i> Almacén de Cargo
    </label>
    <select name="almacen" id="almacen" class="form-select select2-modal" required>
        <option value="">Seleccionar ubicación...</option>
        <?php foreach($almacenes as $a): ?>
        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-4">
    <label for="vehiculo_id" class="form-label small fw-bold text-secondary mb-2">
        <i class="bi bi-car-front me-1"></i> Vehículo
    </label>
    <select class="form-select select2-modal" id="vehiculo_id" name="vehiculo_id" required>
        <option value="">Seleccione vehículo...</option>
        <?php foreach($vehiculos as $ve): ?>
        <!-- Se agrega el atributo data-almacen-id -->
        <option value="<?= $ve['id'] ?>" data-almacen-id="<?= $ve['almacen_id'] ?>">
            <?= htmlspecialchars($ve['nombre']) ?> (<?= htmlspecialchars($ve['placas']) ?>) (<?= htmlspecialchars($ve['tipo']) ?>)
        </option>
        <?php endforeach; ?>
    </select>
</div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-gear me-1"></i> Tipo Mantenimiento
                            </label>
                            <select name="tipo_mantenimiento" id="tipo_mantenimiento" class="form-select select2-modal" required>
                                <option value="PREVENTIVO">PREVENTIVO</option>
                                <option value="CORRECTIVO">CORRECTIVO</option>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-speedometer me-1"></i> Kilometraje
                            </label>
                            <input type="number" value="0" id="kilometraje" name="kilometraje" class="form-control fw-bold text-dark" required>
                        </div>

                        <div class="col-md-8 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-chat-left-text me-1"></i> Razón / Motivo
                            </label>
                            <input type="text" placeholder="Ej. cambio de bujías...." id="razon" name="razon" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-calendar3 me-1"></i> Fecha Mantenimiento
                            </label>
                            <input type="date" id="fecha_mantenimiento" value="<?= date('Y-m-d') ?>" name="fecha_mantenimiento" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-calendar3-event me-1"></i> Fecha Próximo Mantenimiento
                            </label>
                            <input type="date" id="fecha_proximo_mantenimiento"value="<?= date('Y-m-d', strtotime('+1 month')) ?>" name="fecha_proximo_mantenimiento" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-currency-dollar me-1"></i> Monto Costo
                            </label>
                            <div class="input-group">
                                <span class="input-group-text  text-success fw-bold">$</span>
                                <input type="number" step="0.01" value="0.00" id="monto_depositado" name="monto_depositado" class="form-control fw-bold text-dark" required>
                            </div>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-credit-card me-1"></i> Método de Pago
                            </label>
                            <select id="metodo_pago_m" name="metodo_pago_m" class="form-select fw-bold">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-hash me-1"></i> Referencia / Concepto
                            </label>
                            <input type="text" placeholder="Ej. Factura A-123...." id="referencia" name="referencia" class="form-control">
                        </div> 

                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-shop me-1"></i> Taller
                            </label>
                            <input type="text" placeholder="Ej. TALLER MANUEL...." id="taller" name="taller" class="form-control">
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

    $('#almacen').on('change', function() {
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
        let formValido = true;
        const camposObligatorios = [
            '#almacen', '#vehiculo_id', '#tipo_mantenimiento', 
            '#kilometraje', '#razon', '#fecha_mantenimiento', 
            '#fecha_proximo_mantenimiento', '#monto_depositado'
        ];

        camposObligatorios.forEach(selector => {
            const el = $(selector);
            if (!el.val() || el.val().toString().trim() === '') {
                el.addClass('is-invalid');
                formValido = false;
            } else {
                el.removeClass('is-invalid');
            }
        });

        if (!formValido) {
            Swal.fire('Atención', 'Por favor, complete todos los campos obligatorios marcados en rojo.', 'warning');
            return; // Detiene la ejecución aquí
        }

        // Si pasa la validación, armamos el JSON
        const payload = {
            almacen_id: $('#almacen').val(),
            vehiculo_id: $('#vehiculo_id').val(),
            monto_depositado: $('#monto_depositado').val(),
            referencia: $('#referencia').val(),
            razon: $('#razon').val(),
            fecha_mantenimiento: $('#fecha_mantenimiento').val(),
            fecha_proximo_mantenimiento: $('#fecha_proximo_mantenimiento').val(),
            metodo: $('#metodo_pago_m').val(),
            taller: $('#taller').val(),
            tipo: $('#tipo_mantenimiento').val(),
            kilometraje: $('#kilometraje').val()
        };

        console.log('JSON ENVIADO:', payload);

        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const resp = await fetch(`/myvet/app/controllers/mantenimientosController.php?action=guardar`, {
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
function nuevoMantenimiento() {
    $('#formSolicitud')[0].reset();
    $('.is-invalid').removeClass('is-invalid'); // Limpia errores visuales antiguos
    $('.select2-modal').val('').trigger('change'); // Limpia los Select2
    $('#modalMantenimiento').modal('show');
}
</script>