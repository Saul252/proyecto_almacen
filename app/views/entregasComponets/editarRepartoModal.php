<div class="modal fade" id="modalEditarViaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 22px; background: rgba(245, 245, 247, 0.95); backdrop-filter: blur(20px);">
            
            <div class="modal-header bg-white border-bottom-0 pb-3" style="border-radius: 22px 22px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 shadow-sm" style="background: #007aff; padding: 12px; border-radius: 14px;">
                        <i class="bi bi-pencil-square text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-dark" id="editFolioTitle">Ajustar Logística</h5>
                        <small class="text-primary fw-bold" id="editUnidadSubtitle" style="font-size: 0.75rem; letter-spacing: 0.5px;"></small>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <form id="formEditarViaje">
                    <input type="hidden" id="edit_viaje_folio" name="viaje_folio">
                    <input type="hidden" id="edit_vehiculo_id" name="vehiculo_id">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-group p-3 bg-white" style="border-radius: 15px; border: 1px solid #e5e5ea;">
                                <label class="form-label small fw-bold text-body-secondary text-uppercase mb-2" style="font-size: 0.65rem;">Responsable (Chofer)</label>
                                <select id="edit_chofer_id" name="chofer_id" class="form-select  bg-light fw-bold" style="border-radius: 10px; padding: 10px;"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group p-3 bg-white" style="border-radius: 15px; border: 1px solid #e5e5ea;">
                                <label class="form-label small fw-bold text-body-secondary text-uppercase mb-2" style="font-size: 0.65rem;">Tripulación (Ayudantes)</label>
                                <select id="edit_tripulantes" name="tripulantes" class="form-select  bg-light"></select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase mb-0" style="font-size: 0.65rem; margin-left: 10px;">Manifiesto de Carga y Clientes</label>
                        <span class="badge bg-dark rounded-pill" style="font-size: 0.6rem;">EDITABLE</span>
                    </div>
                    
                    <div id="listaMaterialesEdit" class="list-group shadow-sm " style="border-radius: 18px; overflow: hidden;">
                        </div>
                </form>
            </div>

            <div class="modal-footer  bg-white p-3" style="border-radius: 0 0 22px 22px;">
                <button type="button" class="btn btn-link text-secondary fw-bold text-decoration-none" data-bs-dismiss="modal" style="font-size: 0.9rem;">Cancelar</button>
                <button type="button" onclick="guardarCambiosViaje()" class="btn btn-primary fw-bold shadow-sm" style="border-radius: 12px; padding: 12px 30px; background: #007aff; ">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
<script>
/**
 * CF SYSTEM - Módulo de Logística (iOS Style)
 * Gestión de Rutas y Consolidación
 */

// Configuración de Notificaciones rápidas (estilo iOS)
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true
});

/**
 * Abre el modal y carga toda la info del viaje, choferes y materiales
 */
window.abrirModalEdicionViaje = async function(folio, vehiculoId) {
    // Reset de campos básicos
    $('#edit_viaje_folio').val(folio);
    $('#editFolioTitle').text('Ruta ' + folio);
    $('#listaMaterialesEdit').html('<div class="p-4 text-center"><div class="spinner-border text-primary"></div></div>');

    try {
        // 1. Obtener detalles del viaje (Materiales, Chofer actual, etc)
        const respViaje = await fetch(`/myvet/app/controllers/repartosController.php?action=get_detalles_viaje&folio=${folio}`);
        const viaje = await respViaje.json();
        
        if (!viaje.success) throw new Error(viaje.message);
        const info = viaje.data;
        console.log(viaje.data);

        // Seteo de vehículo
        $('#edit_vehiculo_id').val(info.vehiculo_id);
        $('#editUnidadSubtitle').html(`
            <i class="fas fa-truck me-1"></i> ${info.unidad_nombre} 
            <span class="badge bg-light text-dark ms-2" style="font-weight: 500;">${info.unidad_placas}</span>
        `);

        // 2. Obtener catálogo de Choferes/Ayudantes del almacén correspondiente
        const respRecursos = await fetch(`/myvet/app/controllers/repartosController.php?action=get_recursos_sucursal&almacen_id=${info.almacen_id}`);
        const recursos = await respRecursos.json();

        // Llenar Select de Choferes
        let hChofer = '<option value="">Seleccione chofer...</option>';
        recursos.choferes.forEach(c => {
            hChofer += `<option value="${c.id}" ${c.id == info.chofer_id ? 'selected' : ''}>${c.nombre}</option>`;
        });
        $('#edit_chofer_id').html(hChofer);

        // Llenar Select de Ayudantes (Tripulantes)
        let hTrip = '';
        const idsActuales = (info.tripulantes_ids || []).map(id => id.toString());
        recursos.choferes.forEach(a => {
            const isSel = idsActuales.includes(a.id.toString()) ? 'selected' : '';
            hTrip += `<option value="${a.id}" ${isSel}>${a.nombre}</option>`;
        });
        $('#edit_tripulantes').html(hTrip);

        // 3. Renderizar Lista de Materiales (Entregas)
        let hMat = '';
        if(info.materiales && info.materiales.length > 0) {
            info.materiales.forEach(m => {
                const cantidadIni=m.cantidad/m.fc;
                const cantidad =cantidadIni>=1?cantidadIni:m.cantidad;
                const unidad =cantidadIni>=1?m.ur:m.um;
                
                hMat += `
                    <div class="list-group-item  border-bottom p-3 bg-white item-entrega" id="item_mov_${m.movimiento_id}" data-movid="${m.movimiento_id}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-primary mb-1" style="font-size: 0.65rem;">SKU: ${m.sku || 'N/A'}</span>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">${m.producto}</div>
                                <small class="text-body-secondary fw-bold">Cant: ${parseFloat(cantidad).toFixed(2)} ${unidad|| ''}</small>
                            </div>
                            <div class="text-end">
                                <span class="d-block fw-bold text-primary small" style="font-size: 0.75rem;">${m.cliente || 'Público General'}</span>
                                <button type="button" class="btn btn-sm btn-light text-danger rounded-circle mt-1 btn-quitar-item" 
                                        onclick="quitarEntregaVisual(${m.movimiento_id})" title="Quitar de esta ruta">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="input-group mt-2" style="border-radius: 8px; overflow: hidden; border: 1px solid #f0f0f5;">
                            <span class="input-group-text  bg-light"><i class="fas fa-map-marker-alt text-secondary" style="font-size: 0.8rem;"></i></span>
                            <input type="text" class="form-control form-control-sm  bg-light destino-input" 
                                   data-movid="${m.movimiento_id}" value="${m.destino || 'Entrega en Obra'}" 
                                   placeholder="Dirección o punto de entrega" style="font-size: 0.8rem;">
                        </div>
                    </div>`;
            });
        } else {
            hMat = '<div class="p-4 text-center text-body-secondary">No hay productos en esta ruta</div>';
        }
        $('#listaMaterialesEdit').html(hMat);

        // Ajustar visualización de ayudantes según chofer seleccionado
        setTimeout(() => { actualizarListaAyudantes($('#edit_chofer_id').val()); }, 100);

        $('#modalEditarViaje').modal('show');

    } catch (e) {
        console.error("Error al abrir modal:", e);
        Swal.fire('Atención', e.message, 'warning');
    }
}

/**
 * Quita el item visualmente del modal. 
 * La eliminación real en BD ocurre hasta dar click en "Guardar Cambios".
 */
window.quitarEntregaVisual = function(movimientoId) {
    $(`#item_mov_${movimientoId}`).fadeOut(300, function() {
        $(this).remove();
        if ($('.item-entrega').length === 0) {
            $('#listaMaterialesEdit').html('<div class="p-4 text-center text-body-secondary">No hay productos en esta ruta</div>');
        }
        Toast.fire({ icon: 'info', title: 'Removido de la lista' });
    });
}

/**
 * Envía todos los cambios al controlador (Sincronización Total)
 */
window.guardarCambiosViaje = async function() {
    const btnGuardar = document.querySelector('#modalEditarViaje .btn-primary');
    
    // 1. Recopilar direcciones de los items que quedaron físicamente en el modal
    const destinosData = {};
    const inputs = document.querySelectorAll('.destino-input');
    
    // Si el usuario quitó todos los materiales visualmente
    if (inputs.length === 0) {
        return Swal.fire('Atención', 'No puedes dejar una ruta vacía. Si deseas cancelarla, usa la opción correspondiente.', 'warning');
    }

    inputs.forEach(input => {
        destinosData[input.dataset.movid] = input.value.trim();
    });

    // 2. Preparar FormData desde el formulario
    const formElement = document.getElementById('formEditarViaje');
    const formData = new FormData(formElement);
    
    // Agregamos la acción y el JSON de destinos
    formData.append('action', 'guardar_cambios_viaje');
    formData.append('destinos_data', JSON.stringify(destinosData));
console.log(Object.fromEntries(formData));

    try {
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Aplicando...';

        const resp = await fetch(`/myvet/app/controllers/repartosController.php`, {
            method: 'POST',
            body: formData
        });

        // LEER COMO TEXTO PRIMERO (Para depuración)
        const rawResponse = await resp.text();
        let res;
        
        try {
            res = JSON.parse(rawResponse);
        } catch (e) {
            console.error("Respuesta del servidor no es JSON:", rawResponse);
            throw new Error("El servidor devolvió un error interno. Revisa la consola.");
        }

        if (res.success) {
            // EL SWEET ALERT DE ÉXITO
            await Swal.fire({
                icon: 'success',
                title: '¡Logística Actualizada!',
                text: res.message || 'Los cambios se aplicaron correctamente',
                timer: 2000,
                showConfirmButton: false
            });
            
            $('#modalEditarViaje').modal('hide');
            
            // Refrescar vista
            if (typeof listarViajesActivos === 'function') {
                listarViajesActivos();
            } else {
                location.reload();
            }
        } else {
            throw new Error(res.message || "Error desconocido en el servidor");
        }

    } catch (err) {
        console.error("Error en guardarCambiosViaje:", err);
        Swal.fire('Error al guardar', err.message, 'error');
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = 'Guardar Cambios';
    }
}
/**
 * Evita que el chofer sea seleccionado como su propio ayudante
 */
function actualizarListaAyudantes(choferId) {
    const $selectAyudantes = $('#edit_tripulantes');

    // Convertimos el ID del chofer a String para comparar sin fallos
    const choferIdStr = (choferId !== null && choferId !== undefined) ? String(choferId) : "";

    // 1. Recorrer las opciones del select comparando por ID
    $selectAyudantes.find('option').each(function () {
        const $opt = $(this);
        const optionIdStr = String($opt.val());

        // Si la opción tiene el mismo ID que el chofer
        if (optionIdStr === choferIdStr && choferIdStr !== "") {
            $opt.prop('disabled', true).hide();
        } else {
            $opt.prop('disabled', false).show();
        }
    });

    // 2. Si el ayudante actualmente seleccionado tiene el mismo ID que el chofer, deseleccionarlo
    if (String($selectAyudantes.val()) === choferIdStr) {
        $selectAyudantes.val(''); // Resetea la selección a opción vacía/default
    }

    // 3. Notificar a Select2 que las opciones cambiaron para re-renderizar
    if ($selectAyudantes.hasClass('select2-hidden-accessible')) {
        $selectAyudantes.trigger('change.select2');
    }
}

// Evento: Al cambiar chofer, limpiar lista de ayudantes
$('#edit_chofer_id').on('change', function() {
    actualizarListaAyudantes($(this).val());
});

</script>