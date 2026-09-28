<div class="modal fade" id="modalAsignarInsumoMantenimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg  rounded-4 bg-body text-body">
            <form id="formAsignarInsumoMantenimiento" enctype="multipart/form-data">
                
                <input type="hidden" name="action" value="asignarInsumos">
                <input type="hidden" id="msign_almacen_id" name="almacen_id" value="1"> 

                <!-- Header -->
                <div class="modal-header border-bottom border-translucent pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-tools fs-5"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0" style="letter-spacing: -0.3px;">
                            Asignar Insumos a Mantenimiento
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body px-4 py-4">
                    <!-- Fila 1: Selectores Principales -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold text-body-secondary mb-1">
                                <i class="bi bi-wrench me-1"></i>Seleccionar Mantenimiento
                            </label>
                            <select id="msign_mantenimiento_id" name="mantenimiento_id" class="form-select rounded-3 py-2 shadow-sm" required>
                                <option value="">Cargando mantenimientos...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold text-body-secondary mb-1">
                                <i class="bi bi-truck me-1"></i>Vehículo / Carro
                            </label>
                            <select id="msign_carro_id" name="carro_id" class="form-select rounded-3 py-2 shadow-sm" required>
                                <option value="">Seleccione uno...</option>  
                                <?php foreach($vehiculos as $ve): ?>
                                    <option value="<?= $ve['id'] ?>"data-almacen-id="<?= $ve['almacen_id'] ?>"><?= $ve['nombre'] ?>  (<?=  $ve['placas']  ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Sección Header Insumos -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0" style="letter-spacing: -0.3px;">Insumos a Utilizar</h6>
                            <small class="text-body-secondary">Agregue los componentes a extraer del almacén</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" onclick="msign_agregarFilaInsumo()">
                            <i class="bi bi-plus-lg fs-6"></i> Agregar Insumo
                        </button>
                    </div>

                    <!-- Tabla de Insumos -->
                    <div class="table-responsive rounded-3 border bg-body-tertiary p-2 mb-4">
                        <table class="table table-borderless align-middle mb-0" id="msign_tablaInsumosAsignados">
                            <thead>
                                <tr class="text-body-secondary border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <th class="fw-bold py-2">INSUMO DISPONIBLE</th>
                                    <th width="170" class="text-center fw-bold py-2">CANT. DISPONIBLE</th>
                                    <th width="200" class="text-center fw-bold py-2">CANT. A RETIRAR</th>
                                    <th width="40" class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="msign_items[]" class="form-select form-select-sm msign_items msign_select_item_dinamico rounded-2" onchange="msign_manejarCambioInsumo(this)" required>
                                            <option value="">Seleccione insumo...</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="msign_cantdisponible[]" class="form-control form-control-sm text-center rounded-2 bg-body-secondary" readonly placeholder="-">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="msign_cant[]" class="form-control msign_cant text-center rounded-start-2" value="1" min="0.01" step="any" required>
                                            <input type="text" name="unidad[]" class="form-control msign_cant text-center bg-body-secondary rounded-end-2 px-1" readonly style="max-width: 70px;" placeholder="U.M.">
                                        </div>
                                    </td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Notas / Observaciones -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fs-7 fw-semibold text-body-secondary mb-1">
                                <i class="bi bi-pencil-square me-1"></i>Notas u Observaciones
                            </label>
                            <textarea id="msign_observaciones" name="observaciones" class="form-control text-uppercase rounded-3" rows="2" placeholder="Detalles o justificación de la asignación..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-top border-translucent px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-3 fw-semibold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="msign_btnGuardar" class="btn btn-primary rounded-3 fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Asignar Recursos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cache local para optimizar
let msign_listaInsumosCache = [];


    $(document.ready).ready(function() {
    // Guardamos una copia de todas las opciones originales de vehículos
    const $vehiculoSelect2 = $('#msign_carro_id');
    const $vehiculoOptions2 = $vehiculoSelect2.find('option').clone();

    $('#f_almacen').on('change', function() {
        const almacenId2 = $(this).val();

        // Limpiar el select de vehículos
        $vehiculoSelect2.empty();

        if (almacenId2) {
            // Filtrar y agregar solo la opción por defecto y las que coincidan con el almacén
            $vehiculoOptions2.each(function() {
                const optionAlmacenId2 = $(this).data('almacen-id');
                
                // Incluye la opción inicial ("Seleccione vehículo...") o las que coincidan
                if (!optionAlmacenId2 || optionAlmacenId2 == almacenId2) {
                    $vehiculoSelect2.append($(this).clone());
                }
            });
        } else {
             $vehiculoOptions2.each(function() {
                const optionAlmacenId2 = $(this).data('almacen-id');
                
                // Incluye la opción inicial ("Seleccione vehículo...") o las que coincidan
               
                    $vehiculoSelect2.append($(this).clone());
                
            });
            // Si no hay almacén seleccionado, mostrar solo la opción por defecto
            $vehiculoSelect2.append($vehiculoOptions2.first().clone());
        }

        // Reinicializar Select2 para refrescar la lista visible
        $vehiculoSelect2.val('').trigger('change.select2');
    });
});
// ==================== PETICIONES ASÍNCRONAS ====================

async function msign_cargarInsumosBase() {
    try {
        let almacen=$('#f_almacen').val();
        console.log(almacen);
        const resp = await fetch(`/myvet/app/controllers/mantenimientosController.php?action=obtenerInsumosSelect&almacen=${almacen}`);
        const resultado = await resp.json();
        
        if (resultado && Array.isArray(resultado.data)) {
            msign_listaInsumosCache = resultado.data;
            const primerSelect = document.querySelector('.msign_select_item_dinamico');
            if (primerSelect) {
                  console.log(resultado);
                msign_inyectarOpciones(primerSelect);
            }
        }
    } catch (e) {
        console.error("Error al cargar el catálogo de insumos inicial:", e);
    }
}

function msign_inyectarOpciones(selectElement) {
    selectElement.innerHTML = '<option value="">Seleccione insumo...</option>';
    msign_listaInsumosCache.forEach(insumo => {
        const opcion = document.createElement('option');
        opcion.value = insumo.id;
        opcion.setAttribute('data-total', insumo.total_existencias || 0);
        opcion.setAttribute('data-uma', insumo.u_ma || '');
        opcion.setAttribute('data-umi', insumo.u_mi || '');
        opcion.setAttribute('data-factor', insumo.factor || '');
        opcion.textContent = `${insumo.nombre} (${insumo.total_existencias} en existencia)`;
        selectElement.appendChild(opcion);
    });
}
async function msign_cargarMantenimientos() {
    try {
        $('#loader').removeClass('d-none');
        const params = new URLSearchParams({
            action: 'listar',
            f_search: $('#f_search').val() || '',
            f_rango: $('#f_rango').val() || '',
            f_inicio: $('#f_ini').val() || '',
            f_fin: $('#f_fin').val() || '',
            f_almacen: $('#f_almacen').val() || '',
            f_vehiculo: $('#select-vehiculos').val() || ''
        });

        const res = await fetch(`/myvet/app/controllers/mantenimientosController.php?${params.toString()}`);
        const data = await res.json();
         
        let html = '<option value="">Seleccione mantenimiento...</option>';
        data.forEach(m => {
            // Se agrega el atributo data-vehiculo con el id_v
            const idVehiculo = m.id_v || m.id_vehiculo || '';
            html += `<option value="${m.id_mantenimiento}" data-vehiculo="${idVehiculo}">FOLIO: ${m.id_mantenimiento} - ${m.razon || m.tipo_mantenimiento}</option>`;
        });
       
        document.getElementById('msign_mantenimiento_id').innerHTML = html;
    } catch (e) { 
        console.error("Error al cargar mantenimientos:", e); 
    } finally {
        $('#loader').addClass('d-none');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const modalAsignar = document.getElementById('modalAsignarInsumoMantenimiento');
    const formAsignar = document.getElementById('formAsignarInsumoMantenimiento');
    const selectMantenimiento = document.getElementById('msign_mantenimiento_id');
    const selectVehiculo = document.getElementById('msign_carro_id');

    if (!modalAsignar || !formAsignar) return;

    modalAsignar.addEventListener('show.bs.modal', async function() {
        formAsignar.reset();
        msign_limpiarTabla();
        
        await Promise.all([
            msign_cargarMantenimientos(),
            msign_cargarInsumosBase()
        ]);
    });

    // AUTO-SELECCIÓN DE VEHÍCULO AL CAMBIAR MANTENIMIENTO
    if (selectMantenimiento && selectVehiculo) {
        selectMantenimiento.addEventListener('change', function() {
            const optionSeleccionada = this.options[this.selectedIndex];
            const idVehiculo = optionSeleccionada ? optionSeleccionada.getAttribute('data-vehiculo') : '';

            // Asigna el valor del vehículo si existe, de lo contrario reinicia el select
            selectVehiculo.value = idVehiculo || '';
        });
    }

    formAsignar.addEventListener('submit', function(e) {
        e.preventDefault();
        msign_guardarAsignacion();
    });
});
// ==================== GESTIÓN DE FILAS DINÁMICAS ====================

function msign_manejarCambioInsumo(selectElement) {
    const idInsumoSeleccionado = selectElement.value;
    const $filaActual = $(selectElement).closest('tr');
    const $inputCantidad = $filaActual.find('input[name="msign_cantdisponible[]"]');
    const $inputunidad = $filaActual.find('input[name="unidad[]"]');
    
    if (idInsumoSeleccionado === "") {
        $inputCantidad.val(''); 
        $inputunidad.val('');
        return;
    }

    const opcionSeleccionada = selectElement.options[selectElement.selectedIndex];
    const total = opcionSeleccionada.getAttribute('data-total') || 0;
    const factor = opcionSeleccionada.getAttribute('data-factor') || 1;
    const unidad = total / factor;
    let unidadMayor = (unidad >= 1 ? unidad + ' ' + opcionSeleccionada.getAttribute('data-uma') : '');
    
    $inputunidad.val(opcionSeleccionada.getAttribute('data-umi'));
    $inputCantidad.val( unidadMayor);
}

function msign_agregarFilaInsumo() {
    const tbody = document.querySelector('#msign_tablaInsumosAsignados tbody');
    const fila = document.createElement('tr');
    
    fila.innerHTML = `
        <td>
            <select name="msign_items[]" class="form-select form-select-sm msign_select_item_dinamico msign_items rounded-2" onchange="msign_manejarCambioInsumo(this)" required>
            </select>
        </td>
        <td>
            <input type="text" name="msign_cantdisponible[]" class="form-control form-control-sm text-center rounded-2 bg-body-secondary" readonly placeholder="-">
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" name="msign_cant[]" class="form-control msign_cant text-center rounded-start-2" value="1" min="0.01" step="any" required>
                <input type="text" name="unidad[]" class="form-control msign_cant text-center bg-body-secondary rounded-end-2 px-1" readonly style="max-width: 70px;" placeholder="U.M.">
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-link text-danger p-0 shadow-none" onclick="this.closest('tr').remove();">
                <i class="bi bi-trash fs-6"></i>
            </button>
        </td>`;
        
    tbody.appendChild(fila);
    msign_inyectarOpciones(fila.querySelector('.msign_select_item_dinamico'));
}

function msign_limpiarTabla() {
    document.querySelectorAll('#msign_tablaInsumosAsignados tbody tr:not(:first-child)').forEach(f => f.remove());
    const primeraFilaSelect = document.querySelector('.msign_select_item_dinamico');
    if (primeraFilaSelect) {
        primeraFilaSelect.innerHTML = '<option value="">Seleccione insumo...</option>';
    }
}

// ==================== ENVÍO AL CONTROLADOR (POST) ====================
function msign_guardarAsignacion() {
    const form = document.getElementById('formAsignarInsumoMantenimiento');
    if (!form) return;

    const formData = new FormData(form);
    const btn = document.getElementById('msign_btnGuardar');
    if (!btn) return;

    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';

    fetch('/myvet/app/controllers/mantenimientosController.php?action=insumok', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('Error en la respuesta del servidor.');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Asignado!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                const modalEl = document.getElementById('modalAsignarInsumoMantenimiento');
                if (modalEl) {
                    const inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.hide();
                }
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Error procesando la asignación.');
        }
    })
    .catch(err => {
        console.error('❌ Error en asignación:', err);
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: err.message,
            confirmButtonColor: '#0d6efd'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
    });
}
</script>