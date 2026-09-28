<div class="modal fade" id="modalDespachoVentaTotal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 25px; background: #f8f9fa;">
            <div class="modal-header  bg-white" style="border-radius: 25px 25px 0 0; padding: 1.5rem 2rem;">
                <div class="bg-success text-white rounded p-2 d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px !important;">
                    <i class="bi bi-box-seam-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="modal-title fw-bold mb-0">Despacho Masivo Por Venta</h5>
                    <span class="badge bg-light text-dark border mt-1" id="txtFolioVenta">Cargando...</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                <div id="listaItemsDespacho" class="pe-2 mb-2" style="max-height: 200px; overflow-y: auto;"></div>

                <div id="seccionLogisticaMasiva" class="d-none animate__animated animate__fadeIn">
                    <hr class="my-4 opacity-10">
                    
                    <div class="p-3 border rounded-4 bg-white shadow-sm mb-3">
                        <label class="text-uppercase fw-bold text-primary mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 1.2px;">Método de Salida</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optPatio" value="patio" checked onchange="toggleFormRuta(false)">
                            <label class="btn btn-outline-success rounded-start-pill py-2 fw-bold" for="optPatio">
                                <i class="bi bi-box-seam me-2"></i>ENTREGA EN PATIO
                            </label>
                            
                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optRuta" value="ruta" onchange="toggleFormRuta(true)">
                            <label class="btn btn-outline-primary rounded-end-pill py-2 fw-bold" for="optRuta">
                                <i class="bi bi-truck me-2"></i>ASIGNAR RUTA
                            </label>
                        </div>
                    </div>

                    <div id="formRutaIntegrado" class="animate__animated animate__fadeInUp">
                        <div id="wrapperLogistica" class="p-4 rounded-4 shadow-sm" style="transition: all 0.3s ease; border: 1px solid;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div id="contenedorDireccion" class="p-3 rounded-4 mb-1 border bg-white shadow-sm">
                                        <label id="lblDinamicoPrincipal" class="small fw-bold text-body-secondary mb-1" style="font-size: 0.65rem;">PUNTO DE ENTREGA / OBRA</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white ">
                                                <i id="iconDinamico" class="bi bi-geo-alt-fill text-danger"></i>
                                            </span>
                                            <textarea id="mv_direccion" class="form-control text-uppercase  p-2" rows="2" style="font-size: 0.9rem; resize: none;" placeholder="Dirección exacta..."></textarea>
                                        </div>
                                    </div>
                                </div>
 <div class="col-md-6" id="colVehiculo">
                                    <label class="small fw-bold text-body-secondary mb-1">UNIDAD / VEHÍCULO</label>
                                    <select id="mv_vehiculo_id" class="form-select  shadow-sm rounded-3 p-3 bg-white"></select>
                                </div>
                                <div class="col-md-6">
                                    <label id="lblPersonal" class="small fw-bold text-body-secondary mb-1">CHOFER RESPONSABLE</label>
                                    <select id="mv_chofer_id" class="form-select  shadow-sm rounded-3 p-3 bg-white"></select>
                                </div>

                               

                                <div class="col-12">
                                    <label class="small fw-bold text-body-secondary mb-1">AYUDANTES DE CARGA (OPCIONAL)</label>
                                    <select id="mv_tripulantes" class="form-select  shadow-sm rounded-3 p-2 bg-white" multiple size="3" style="font-size: 0.85rem;"></select>
                                    <small class="text-body-secondary mt-2 d-block" style="font-size: 0.6rem;">* Mantén presionada la tecla <b>Ctrl</b> para elegir varios.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer  bg-white py-3 px-4" style="border-radius: 0 0 25px 25px;">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEjecutarDespachoMasivo" class="btn btn-success rounded-pill px-5 fw-bold shadow" disabled>
                    <i class="bi bi-check-circle me-2"></i>Confirmar Despacho
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Alterna la "Personalidad" del formulario entre Modo Patio y Modo Ruta
 */
function toggleFormRuta(esRuta) {
    const wrapper = $('#wrapperLogistica');
    const btnConfirmar = $('#btnEjecutarDespachoMasivo');

    if (esRuta) {
        // --- ESTILO RUTA (AZUL) ---
        wrapper.css({'background': '#eef6ff', 'border-color': '#cfe2ff'});
        $('#lblDinamicoPrincipal').text('PUNTO DE ENTREGA / OBRA (EDITABLE)');
        $('#iconDinamico').removeClass('bi-person-badge-fill text-success').addClass('bi-geo-alt-fill text-danger');
        $('#mv_direccion').attr('placeholder', 'Dirección exacta de entrega...');
        $('#lblPersonal').text('CHOFER RESPONSABLE');
        $('#colVehiculo').fadeIn(); 
        btnConfirmar.removeClass('btn-success').addClass('btn-primary');
    } else {
        // --- ESTILO PATIO (VERDE) ---
        wrapper.css({'background': '#f6fff8', 'border-color': '#c1e7c1'});
        $('#lblDinamicoPrincipal').text('NOTAS / QUIÉN RECIBE (OPCIONAL)');
        $('#iconDinamico').removeClass('bi-geo-alt-fill text-danger').addClass('bi-person-badge-fill text-success');
        $('#mv_direccion').attr('placeholder', 'Ej. Se lo lleva el cliente en su camioneta...');
        $('#lblPersonal').text('DESPACHADOR RESPONSABLE (PATIO)');
        $('#colVehiculo').hide(); 
        btnConfirmar.removeClass('btn-primary').addClass('btn-success');
    }
}

/**
 * Abre el modal y carga los datos de la venta y recursos de la sucursal
 */
async function abrirModalDespachoVentaTotal(ventaId, almacenId) {
    const URL_ENTREGAS = '/myvet/app/controllers/entregasController.php'; 
    const modalElement = document.getElementById('modalDespachoVentaTotal');
    const modal = new bootstrap.Modal(modalElement);
    const contenedor = document.getElementById('listaItemsDespacho');
    const txtFolio = document.getElementById('txtFolioVenta');
    const btnConfirmar = document.getElementById('btnEjecutarDespachoMasivo');
    const logSection = document.getElementById('seccionLogisticaMasiva');

    // Reset UI inicial
    txtFolio.innerHTML = `<span class="opacity-50 small">Sincronizando...</span>`;
    contenedor.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-success opacity-25"></div></div>`;
    logSection.classList.add('d-none');
    $('#optPatio').prop('checked', true);
    toggleFormRuta(false); // Iniciar en modo patio
    btnConfirmar.disabled = true;
    modal.show();

    try {
        // 1. Obtener items pendientes
        const respIds = await fetch(`${URL_ENTREGAS}?ajax=get_ids_pendientes_venta&venta_id=${ventaId}`);
        const dataIds = await respIds.json();
        if (!dataIds.success || !dataIds.ids?.length) throw new Error("No hay productos pendientes para despacho.");

        const idsParaProcesar = dataIds.ids;
        const primerId = idsParaProcesar[0];
        const paramsLotes = idsParaProcesar.map(id => `ids[]=${id}`).join('&');
        
        // 2. Simular Stock para el listado
        const respSim = await fetch(`${URL_ENTREGAS}?ajax=simular_masivo&${paramsLotes}`);
        const sim = await respSim.json();
        if (!sim.success) throw new Error(sim.message);

        txtFolio.innerHTML = `<i class="bi bi-hash opacity-50"></i>${ventaId}`;
        
        let htmlItems = '';
        sim.data.forEach(item => {
            htmlItems += `
                <div class="mb-2 p-2 bg-white rounded-3 border shadow-sm small d-flex justify-content-between align-items-center">
                    <div class="fw-bold text-dark"><i class="bi bi-caret-right-fill text-success me-1"></i>${item.producto}</div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">${parseFloat(item.total_solicitado)} PZA</span>
                </div>`;
        });
        contenedor.innerHTML = htmlItems;

        // 3. Cargar Recursos (Choferes, Vehículos)
        const [respDetalle, respRecursos] = await Promise.all([
            fetch(`${URL_ENTREGAS}?ajax=get_recursos_reparto&id=${primerId}`),
            fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${almacenId}`)
        ]);

        const resDetalle = await respDetalle.json();
        const resRecursos = await respRecursos.json();
 
   if (resDetalle.success && resRecursos.success) {

    // 1. Dirección
    $('#mv_direccion').val(resDetalle.data.entrega.cliente_direccion_fiscal || '');

    // 2. VEHÍCULOS
    const selectV = $('#mv_vehiculo_id')
        .empty()
        .append('<option value="">Seleccione unidad...</option>');

    (resRecursos.unidades || []).forEach(u => {
        selectV.append(`<option value="${String(u.id)}">${u.nombre} [${u.placas || 'S/P'}]</option>`);
    });

    // 3. PERSONAL BASE (CHOFERES)
    const selectC = $('#mv_chofer_id')
        .empty()
        .append('<option value="">Seleccione responsable...</option>');

    const selectT = $('#mv_tripulantes').empty();

    (resRecursos.choferes || []).forEach(c => {
        const id = String(c.id);
        const opt = `<option value="${id}">${c.nombre}</option>`;
        selectC.append(opt);
        selectT.append(opt);
    });

    // =========================================
    // 🚛 CAMBIO DE VEHÍCULO (CON FALLBACK)
    // =========================================
    $('#mv_vehiculo_id')
        .off('change')
        .on('change', function () {

            const vehiculo_id = $(this).val();
            if (!vehiculo_id) return;

            fetch(`${URL_ENTREGAS}?ajax=get_datos_vehiculo&vehiculo_id=${vehiculo_id}`)
                .then(r => r.json())
                .then(res => {

                    console.log("Respuesta servidor:", res);

                    // LIMPIAR SIEMPRE
                    $('#mv_chofer_id').val('');
                    $('#mv_tripulantes').val([]);

                    // =========================
                    // 🟢 CASO 1: TIENE DATOS
                    // =========================
                    if (res.success && res.data &&
                        (res.data.encargado || (res.data.tripulantes && res.data.tripulantes.length))
                    ) {

                        const data = res.data;

                        // 👤 CHOFER
                        if (data.encargado && data.encargado.id) {
                            const idChofer = String(data.encargado.id);

                            if ($(`#mv_chofer_id option[value="${idChofer}"]`).length) {
                                $('#mv_chofer_id').val(idChofer).trigger('change');
                            }
                        }

                        // 👥 TRIPULANTES
                        if (Array.isArray(data.tripulantes)) {
                            const ids = data.tripulantes.map(t => String(t.id));

                            const validos = ids.filter(id =>
                                $(`#mv_tripulantes option[value="${id}"]`).length
                            );

                            $('#mv_tripulantes').val(validos).trigger('change');
                        }

                        console.log('✔ Datos cargados desde vehículo');

                    } 
                    // =========================
                    // 🔵 CASO 2: FALLBACK
                    // =========================
                    else {
                        console.log(resRecursos.trabajadoresDisponibles);

                        console.log('⚠ Sin datos en vehículo → usando trabajadores disponibles');

                        // 🔥 REEMPLAZAR OPTIONS CON DISPONIBLES
                        const selectC = $('#mv_chofer_id')
                            .empty()
                            .append('<option value="">Seleccione responsable...</option>');

                        const selectT = $('#mv_tripulantes').empty();

                        (resRecursos.trabajadoresDisponibles || []).forEach(t => {
                            const id = String(t.id);
                            const opt = `<option value="${id}">${t.nombre}</option>`;
                            selectC.append(opt);
                            selectT.append(opt);
                        });

                        // reset visual
                        $('#mv_chofer_id').val('');
                        $('#mv_tripulantes').val([]);
                    }
                })
                .catch(err => console.error('Error fetch:', err));
        });

    // =========================================
    // 🔒 EXCLUSIÓN CHOFER → TRIPULANTES
    // =========================================
    $('#mv_chofer_id')
        .off('change')
        .on('change', function () {

            const sel = String($(this).val());

            $('#mv_tripulantes option').each(function () {

                const val = String($(this).val());

                if (sel && val === sel) {
                    $(this)
                        .prop('disabled', true)
                        .prop('selected', false)
                        .hide();
                } else {
                    $(this)
                        .prop('disabled', false)
                        .show();
                }
            });

            $('#mv_tripulantes').trigger('change');
        });

    // =========================================
    // UI FINAL
    // =========================================
    logSection.classList.remove('d-none');
    btnConfirmar.disabled = false;
    btnConfirmar.onclick = () => ejecutarSalidaMasivaFinal(idsParaProcesar, btnConfirmar);
}
}catch (err) {
        contenedor.innerHTML = `<div class="alert alert-danger mx-2 small">${err.message}</div>`;
    }
}

/**
 * Envío final de los datos al servidor
 */
async function ejecutarSalidaMasivaFinal(ids, boton) {
    const tipo = $('input[name="tipo_entrega_masiva"]:checked').val();
    
    // Validaciones
    if (!$('#mv_chofer_id').val()) return Swal.fire('Atención', 'Seleccione un responsable.', 'warning');
    if (tipo === 'ruta' && !$('#mv_vehiculo_id').val()) return Swal.fire('Atención', 'Seleccione una unidad.', 'warning');

    boton.disabled = true;
    boton.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Procesando...`;

    try {
        const formData = new FormData();
        formData.append('ajax', 'despachar_venta_completa'); 
        formData.append('tipo_logistica', tipo);
        formData.append('vehiculo_id', tipo === 'ruta' ? $('#mv_vehiculo_id').val() : 999);
        formData.append('chofer_id', $('#mv_chofer_id').val());
        formData.append('direccion', $('#mv_direccion').val() || '');
        
        const tripulantes = $('#mv_tripulantes').val() || [];
        tripulantes.forEach(tId => formData.append('tripulantes[]', tId));
        ids.forEach(id => formData.append('ids_movimientos[]', id));

        const resp = await fetch('/myvet/app/controllers/entregasController.php', { method: 'POST', body: formData });
        const res = await resp.json();

        if (res.success) {
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: res.message, timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
        } else {
            throw new Error(res.message);
        }
    } catch (e) {
        Swal.fire('Error de Despacho', e.message, 'error');
        boton.disabled = false;
        boton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Confirmar Despacho';
    }
}
</script>