<div class="modal fade" id="modalEntregaPatio" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 20px;">
            <div class="modal-header  pt-4 px-4">
                <h5 class="fw-bold m-0 text-dark">
                    <i class="bi bi-box-seam me-2 text-success"></i>Entrega Directa en Patio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEntregaPatio">
                <div class="modal-body p-4">
                    <div class="p-3 rounded-4 mb-3" style="background: #f8f9fa; border: 1px dashed #dee2e6;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.65rem;">MATERIAL</small>
                                <div id="patio_producto_info" class="fw-bold text-dark">---</div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.65rem;">CANTIDAD</small>
                                <div id="patio_cantidad_info" class="fw-bold text-success">---</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-4 mb-4" style="background: #f6fff8; border: 1px solid #c1e7c1;">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.65rem;">CLIENTE QUE RECOGE</small>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-badge text-success me-2"></i>
                            <span id="patio_cliente_nombre" class="fw-bold text-dark small">---</span>
                        </div>
                    </div>

                    <input type="hidden" name="movimiento_id" id="patio_movimiento_id">
                    <input type="hidden" name="almacen_id" id="patio_almacen_id">
                    <input type="hidden" name="vehiculo_id" value="0"> 

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">DESPACHADOR RESPONSABLE (PATIO)</label>
                            <select name="chofer_id" id="patio_chofer_id" class="form-select  bg-light p-3 rounded-3 shadow-sm" required>
                                <option value="">Seleccione encargado...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">AYUDANTES DE CARGA (OPCIONAL)</label>
                            <select name="tripulantes[]" id="patio_tripulantes" class="form-select  bg-light p-2 rounded-3 shadow-sm" style="font-size: 0.85rem;" multiple size="3">
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">NOTAS / QUIÉN RECIBE</label>
                            <textarea name="observaciones" class="form-control text-uppercase  bg-light rounded-3 shadow-sm" rows="2" placeholder="Ej. Se lo lleva en camioneta propia..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer  pb-4 px-4">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarPatio" class="btn btn-success px-4 py-2 shadow rounded-pill">
                        <i class="bi bi-check2-all me-2"></i>Finalizar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>$(document).ready(function() {
    const URL_ENTREGAS = '/myvet/app/controllers/entregasController.php';

    /**
     * LÓGICA DE EXCLUSIÓN PARA PATIO:
     * Evita que el Despachador Responsable sea seleccionado como Ayudante.
     */
    $('#patio_chofer_id').on('change', function() {
        const selectedEncargado = $(this).val();
        
        $('#patio_tripulantes option').each(function() {
            const val = $(this).val();
            if (selectedEncargado && val === selectedEncargado) {
                $(this).prop('disabled', true).hide();
                // Si ya estaba seleccionado como ayudante, lo quitamos
                if ($(this).is(':selected')) {
                    $(this).prop('selected', false);
                }
            } else {
                $(this).prop('disabled', false).show();
            }
        });
    });

    /**
     * FUNCIÓN DE FORMATEO: 
     * Limpia los ceros para que no aparezca "0 Toneladas + 5 Pz".
     */
    function formatUnitsClean(cantidad, factor, uReporte, uMedida) {
        const qty = parseFloat(cantidad) || 0;
        const f = parseFloat(factor) || 1;
        const unitRep = uReporte || 'Unid.';
        const unitMed = uMedida || 'Pz';

        if (f > 1) {
            const enteros = Math.floor(qty / f);
            const sobrantes = qty % f;

            let partes = [];
            if (enteros > 0) {
                partes.push(`<span class="fw-bold text-success">${enteros}</span> ${unitRep}`);
            }
            if (sobrantes > 0) {
                partes.push(`<span class="fw-bold text-success">${sobrantes}</span> ${unitMed}`);
            }

            return partes.length > 0 ? partes.join(' + ') : `0 ${unitMed}`;
        }
        
        return `<span class="fw-bold text-success">${qty}</span> ${unitMed}`;
    }

    /**
     * FUNCIÓN B: Preparar y abrir modal de Patio
     */
    window.prepararModalPatio = async function(movimientoId, almacenId) {
        if(typeof Swal !== 'undefined') {
            Swal.fire({ 
                title: 'Cargando recursos...', 
                allowOutsideClick: false, 
                didOpen: () => Swal.showLoading() 
            });
        }

        try {
            const [respDetalle, respRecursos] = await Promise.all([
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_reparto&id=${movimientoId}`),
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${almacenId}`)
            ]);

            const resDetalle = await respDetalle.json();
            const resRecursos = await respRecursos.json();

            if (resDetalle.success && resRecursos.success) {
                const e = resDetalle.data.entrega;

                // Llenar IDs y textos básicos
                $('#patio_movimiento_id').val(movimientoId);
                $('#patio_almacen_id').val(almacenId);
                $('#patio_producto_info').text(e.producto_nombre || e.producto);
                $('#patio_cliente_nombre').text(e.cliente_nombre || 'Venta Mostrador');

                // --- APLICAR LÓGICA DE UNIDADES LIMPIAS ---
                const htmlCantidad = formatUnitsClean(
                    e.cantidad, 
                    e.factor_conversion, 
                    e.unidad_reporte, 
                    e.unidad_medida
                );
                $('#patio_cantidad_info').html(htmlCantidad);

                // Llenar responsables y ayudantes
                const selectC = $('#patio_chofer_id').empty().append('<option value="">Seleccione encargado...</option>');
                const selectT = $('#patio_tripulantes').empty();
                
                if(resRecursos.choferes && resRecursos.choferes.length > 0) {
                    resRecursos.choferes.forEach(c => {
                        selectC.append(`<option value="${c.id}">${c.nombre}</option>`);
                        selectT.append(`<option value="${c.id}">${c.nombre}</option>`);
                    });
                } else {
                    selectC.append('<option disabled>❌ Sin personal en esta sucursal</option>');
                }

                // Sincronizar exclusión inmediatamente después de cargar la lista
                $('#patio_chofer_id').trigger('change');

                if(typeof Swal !== 'undefined') Swal.close();
                $('#modalEntregaPatio').modal('show');
            } else {
                throw new Error("Error al obtener datos");
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'No se pudo conectar con el almacén para obtener los recursos.', 'error');
        }
    };

    /**
     * FUNCIÓN C: Envío del formulario
     */
    $('#formEntregaPatio').on('submit', async function(e) {
        e.preventDefault();
        const btn = $('#btnGuardarPatio');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Finalizando...');

        try {
            const formData = new FormData(this);
            formData.append('ajax', 'entregar_en_patio'); 

            const resp = await fetch(URL_ENTREGAS, { 
                method: 'POST', 
                body: formData 
            });
            const res = await resp.json();

            if (res.success) {
                $('#modalEntregaPatio').modal('hide');
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Entrega Exitosa', 
                    text: 'El ciclo de mercancía se ha cerrado correctamente.', 
                    timer: 2000 
                });
                
                if (window.renderTable) window.renderTable();
                if (window.cargarPendientes) window.cargarPendientes();
            } else {
                Swal.fire('Atención', res.message, 'warning');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Error crítico al procesar la entrega en patio.', 'error');
        } finally {
            btn.prop('disabled', false).html(originalHtml);
        }
    });
});</script>