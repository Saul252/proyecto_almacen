<style>
    /* Modal Despacho Masivo por Venta */
    #modalDespachoVentaTotal {
        z-index: 10110 !important;
    }

    /* El contenedor raíz de SweetAlert2 */
    .swal2-container {
        z-index: 99999 !important;
    }

    /* El backdrop de SweetAlert */
    .swal2-container.swal2-backdrop-show {
        z-index: 99999 !important;
    }

    /* El popup en sí */
    .swal2-popup {
        z-index: 100000 !important;
        position: relative;
    }

    /* El toast (notificaciones sin backdrop) */
    .swal2-toast-shown .swal2-container {
        z-index: 99999 !important;
    }

        {
        z-index: 1090 !important;
    }

    /* El backdrop que Bootstrap inyecta al body */
    .modal-backdrop.show {
        z-index: 1089 !important;
    }
</style>

<div class="modal fade" id="modalDespachoVentaTotal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border border-subtle" style="border-radius: 25px 25px 0 0; padding: 1.5rem 2rem;">
                <div class="bg-success text-white rounded p-2 d-flex align-items-center justify-content-center me-3"
                    style="width: 45px; height: 45px; border-radius: 12px !important;">
                    <i class="bi bi-box-seam-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="modal-title fw-bold mb-0">Despacho Masivo Por Venta</h5>
                    <span class="bg-light card-title-text border mt-1" id="txtFolioVenta">Cargando...</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Disponible</th>
                                <th>Comprada</th>
                                <th>Restante</th>
                                <th>Entregar</th>
                                <th>Lote</th>
                            </tr>
                        </thead>
                        <tbody id="listaItemsDespacho"></tbody>
                    </table>
                </div>

                <div id="seccionLogisticaMasiva" class="d-none animate__animated animate__fadeIn">
                    <hr class="my-4 opacity-10">

                    <div class="p-3 border rounded-4 border border-subtle shadow-sm mb-3">
                        <label class="text-uppercase fw-bold text-primary mb-2 d-block"
                            style="font-size: 0.7rem; letter-spacing: 1.2px;">Método de Salida</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optPatio" value="patio"
                                checked onchange="toggleFormRuta(false)">
                            <label class="btn btn-outline-success rounded-start-pill py-2 fw-bold" for="optPatio">
                                <i class="bi bi-box-seam me-2"></i>ENTREGA EN PATIO
                            </label>

                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optRuta" value="ruta"
                                onchange="toggleFormRuta(true)">
                            <label class="btn btn-outline-primary rounded-end-pill py-2 fw-bold" for="optRuta">
                                <i class="bi bi-truck me-2"></i>ASIGNAR RUTA
                            </label>
                        </div>
                    </div>

                    <div id="formRutaIntegrado" class="animate__animated animate__fadeInUp">
                        <div id="wrapperLogistica" class="p-4 rounded-4 shadow-sm"
                            style="transition: all 0.3s ease; border: 1px solid;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div id="contenedorDireccion"
                                        class="p-3 rounded-4 mb-1 border border-subtle shadow-sm">
                                        <label id="lblDinamicoPrincipal" class="small fw-bold text-body-secondary mb-1"
                                            style="font-size: 0.65rem;">PUNTO DE ENTREGA / OBRA</label>
                                        <div class="input-group">
                                            <span class="input-group-text border border-subtle">
                                                <i id="iconDinamico" class="bi bi-geo-alt-fill text-danger"></i>
                                            </span>
                                            <textarea id="mv_direccion" class="form-control p-2 text-uppercase" rows="2"
                                                style="font-size: 0.9rem; resize: none;"
                                                placeholder="Dirección exacta..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="colVehiculo">
                                    <label class="small fw-bold text-body-secondary mb-1">UNIDAD / VEHÍCULO</label>
                                    <select id="mv_vehiculo_id"
                                        class="form-select shadow-sm rounded-3 p-3 border border-subtle"></select>
                                </div>
                                <div class="col-md-6">
                                    <label id="lblPersonal" class="small fw-bold text-body-secondary mb-1">CHOFER
                                        RESPONSABLE</label>
                                    <select id="mv_chofer_id"
                                        class="form-select shadow-sm rounded-3 p-3 border border-subtle"></select>
                                </div>

                                <div class="col-12">
                                    <label class="small fw-bold text-body-secondary mb-1">AYUDANTES DE CARGA
                                        (OPCIONAL)</label>
                                    <select id="mv_tripulantes"
                                        class="form-select shadow-sm rounded-3 p-2 border border-subtle"
                                        style="font-size: 0.85rem;"></select>
                                    <small class="text-body-secondary mt-2 d-block" style="font-size: 0.6rem;">* Mantén
                                        presionada la tecla <b>Ctrl</b> para elegir varios.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border border-subtle py-3 px-4" style="border-radius: 0 0 25px 25px;">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEjecutarDespachoMasivo"
                    class="btn btn-success rounded-pill px-5 fw-bold shadow" disabled>
                    <i class="bi bi-check-circle me-2"></i>Confirmar Despacho
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });
</script>
<script>
    function toggleFormRuta(esRuta) {
        const btnConfirmar = $('#btnEjecutarDespachoMasivo');

        if (esRuta) {
            $('#lblDinamicoPrincipal').text('PUNTO DE ENTREGA / OBRA (EDITABLE)');
            $('#iconDinamico').removeClass('bi-person--fill text-success').addClass('bi-geo-alt-fill text-danger');
            $('#mv_direccion').attr('placeholder', 'Dirección exacta de entrega...');
            $('#lblPersonal').text('CHOFER RESPONSABLE');
            $('#colVehiculo').fadeIn();
            btnConfirmar.removeClass('btn-success').addClass('btn-primary');
        } else {
            $('#lblDinamicoPrincipal').text('NOTAS / QUIÉN RECIBE (OPCIONAL)');
            $('#iconDinamico').removeClass('bi-geo-alt-fill text-danger').addClass('bi-person--fill text-success');
            $('#mv_direccion').attr('placeholder', 'Ej. Se lo lleva el cliente en su camioneta...');
            $('#lblPersonal').text('DESPACHADOR RESPONSABLE (PATIO)');
            $('#colVehiculo').hide();
            btnConfirmar.removeClass('btn-primary').addClass('btn-success');
        }
    }

    let sim;
    window.carrito = 0;

    async function lotes(producto_id, almacenId) {
        const resp = await fetch(
            `/myvet/app/controllers/entregasController.php?ajax=despachar&prodId=${producto_id}&almacen=${almacenId}`
        );
        let data = await resp.json();
        console.log('lotes', data);
        return data;
    }

    let modal = null;

    async function abrirModalDespachoVentaTotal(almacenId, ventaId) {
        console.log(almacenId, ventaId);
        const URL_ENTREGAS = '/myvet/app/controllers/entregasController.php';
        const modalElement = document.getElementById('modalDespachoVentaTotal');
        modal = new bootstrap.Modal(modalElement);
        const contenedor = document.getElementById('listaItemsDespacho');
        const txtFolio = document.getElementById('txtFolioVenta');
        const btnConfirmar = document.getElementById('btnEjecutarDespachoMasivo');
        const logSection = document.getElementById('seccionLogisticaMasiva');

        txtFolio.innerHTML = `<span class="opacity-50 small">Sincronizando...</span>`;
        contenedor.innerHTML =
            `<div class="text-center py-4"><div class="spinner-border text-success opacity-25"></div></div>`;
        logSection.classList.add('d-none');
        $('#optPatio').prop('checked', true);
        toggleFormRuta(false);
        btnConfirmar.disabled = true;
        modal.show();

        try {
            const respIds = await fetch(`${URL_ENTREGAS}?ajax=get_productos_para_despacho&venta_id=${ventaId}`);
            const dataIds = await respIds.json();
            console.log('data', dataIds);

            const idsParaProcesar = dataIds.ids;
            console.log("ids", idsParaProcesar);
            const primerId = almacenId;

            window.carrito = [];
            const todos_los_lotes = await Promise.all(
                idsParaProcesar.map(item => lotes(item.producto_id, almacenId))
            );

            contenedor.innerHTML = '';

            idsParaProcesar.forEach((item, index) => {
                console.log(item);

                const cantidadRealFaltante = (item.cantidad) - (item.cantidad_entregada);
                let cantidad = 0;
                if (cantidadRealFaltante > 0) {
                    const cantidad_maxima = item.disponible < cantidadRealFaltante ? cantidadRealFaltante : item
                        .disponible;
                    let unidad = item.nombre;
                    let multiplicador = 1;
                    let factor = item.factor_conversion;
                    cantidad = item.cantidad;
                    if ((cantidadRealFaltante / factor) >= 1) {
                        unidad = item.unidad_reporte
                        multiplicador = factor;
                    }
                    const aplicaConversion = (item.disponible / factor) > 1;
                    const stockDisponible = aplicaConversion ? (item.disponible / factor) : item.disponible;
                    const unidadTexto = aplicaConversion ? item.unidad_reporte : item.unidad_medida;

                    const tr = document.createElement("tr");
                    tr.innerHTML = `
    <td>
        <span class="fw-bold card-title-text">
            <i class="bi bi-box-seam me-1 text-success"></i>
            ${item.producto}
        </span>
    </td>
    <td>
        <span class="bg-info-subtle card-title-text">
    ${stockDisponible} ${unidadTexto}
</span>
    </td>
    <td>
        <span class=" bg-primary-subtle card-title-text">
            ${(cantidad / ((1 / item.equivalencia)) >= 1) ? (cantidad / ((1 / item.equivalencia)).toFixed(3)) : (cantidad)}
         ${item.nombre}
        </span>
    </td>
    <td>
        <span class=" bg-warning-subtle card-title-text">
            ${(cantidadRealFaltante / ((1 / item.equivalencia)) >= 1) ? (cantidadRealFaltante / ((1 / item.equivalencia)).toFixed(3)) : (cantidad)}
        ${unidad}
            </span>
    </td>
    <td style="width:140px;">
        <input type="number"
               step="0.01"
               id="cantidad_despacho_${index}1"
               max="${cantidad_maxima}"
               class="form-control form-control-sm input-entrega1"
               data-factor="${(cantidadRealFaltante / factor >= 1 && (item.disponible / factor >= 1)) ? factor : (item.cantidad / ((1 / item.equivalencia))) >= 1 ? (1 / item.equivalencia) : 1}"
               data-dvid="${item.dvid}"
               data-id="${item.producto_id}">
        <input type="hidden"
               id="cantidad_despacho_${index}"
               class="input-entrega"
               data-dvid="${item.dvid}"
               data-id="${item.producto_id}">
    </td>
    <td style="min-width:220px;">
        <select id="merma_lote_${index}"
                name="merma_lote_${index}"
                class="form-select form-select-sm" required>
            <option value="">Seleccione lote</option>
        </select>
    </td>
`;

                    const select = tr.querySelector('select');
                    const lotesData = todos_los_lotes[index]?.data || [];

                    lotesData.forEach(lote => {
                        const option = document.createElement('option');
                        option.value = lote.id;
                        option.textContent =
                            `${lote.codigo_lote} (${lote.cantidad_actual / factor} ${item.nombre})`;
                        select.appendChild(option);
                    });

                    contenedor.appendChild(tr);
                }
            });

            const [respDetalle, respRecursos] = await Promise.all([
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_reparto&id=${primerId}`),
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${almacenId}`)
            ]);

            const resDetalle = await respDetalle.json();
            const resRecursos = await respRecursos.json();

            if (resDetalle.success && resRecursos.success) {
                $('#mv_direccion').val(resDetalle.data.entrega.cliente_direccion_fiscal || '');

                const selectV = $('#mv_vehiculo_id')
                    .empty()
                    .append('<option value="">Seleccione unidad...</option>');

                (resRecursos.unidades || []).forEach(u => {
                    selectV.append(
                        `<option value="${String(u.id)}">${u.nombre} [${u.placas || 'S/P'}]</option>`);
                });

                const selectC = $('#mv_chofer_id')
                    .empty()
                    .append('<option value="">Seleccione responsable...</option>');

                const selectT = $('#mv_tripulantes')
                    .empty()
                    .append('<option value="">Seleccione tripulante...</option>');

                (resRecursos.choferes || []).forEach(c => {
                    const id = String(c.id);
                    const opt = `<option value="${id}">${c.nombre}</option>`;
                    selectC.append(opt);
                    selectT.append(opt);
                });

                $('#mv_vehiculo_id')
                    .off('change')
                    .on('change', function () {
                        const vehiculo_id = $(this).val();
                        if (!vehiculo_id) return;

                        fetch(`${URL_ENTREGAS}?ajax=get_datos_vehiculo&vehiculo_id=${vehiculo_id}`)
                            .then(r => r.json())
                            .then(res => {
                                console.log("Respuesta servidor:", res);
                                $('#mv_chofer_id').val('');
                                $('#mv_tripulantes').val('');

                                if (res.success && res.data &&
                                    (res.data.encargado || (res.data.tripulantes && res.data.tripulantes.length))
                                ) {
                                    const data = res.data;

                                    if (data.encargado && data.encargado.id) {
                                        const idChofer = String(data.encargado.id);
                                        if ($(`#mv_chofer_id option[value="${idChofer}"]`).length) {
                                            $('#mv_chofer_id').val(idChofer).trigger('change');
                                            $('#mv_chofer_id').prop('disabled', true);
                                        }
                                    }

                                    if (Array.isArray(data.tripulantes) && data.tripulantes.length > 0) {
                                        const idPrimerTripulante = String(data.tripulantes[0].id);
                                        if ($(`#mv_tripulantes option[value="${idPrimerTripulante}"]`).length) {
                                            $('#mv_tripulantes').val(idPrimerTripulante).trigger('change');
                                        }
                                    }

                                    console.log('✔ Datos cargados desde vehículo');
                                } else {
                                    console.log(resRecursos.trabajadoresDisponibles);
                                    console.log('⚠ Sin datos en vehículo → usando trabajadores disponibles');

                                    $('#mv_chofer_id').prop('disabled', false);

                                    const selectC = $('#mv_chofer_id')
                                        .empty()
                                        .append('<option value="">Seleccione responsable...</option>');

                                    const selectT = $('#mv_tripulantes')
                                        .empty()
                                        .append('<option value="">Seleccione tripulante...</option>');

                                    (resRecursos.trabajadoresDisponibles || []).forEach(t => {
                                        const id = String(t.id);
                                        const opt = `<option value="${id}">${t.nombre}</option>`;
                                        selectC.append(opt);
                                        selectT.append(opt);
                                    });

                                    $('#mv_chofer_id').val('');
                                    $('#mv_tripulantes').val('');
                                }
                            })
                            .catch(err => console.error('Error fetch:', err));
                    });

                $('#mv_chofer_id')
                    .off('change')
                    .on('change', function () {
                        const sel = String($(this).val());

                        $('#mv_tripulantes option').each(function () {
                            const val = String($(this).val());
                            if (!val) return;

                            if (sel && val === sel) {
                                $(this).prop('disabled', true).prop('selected', false).hide();
                                if ($('#mv_tripulantes').val() === sel) {
                                    $('#mv_tripulantes').val('');
                                }
                            } else {
                                $(this).prop('disabled', false).show();
                            }
                        });

                        $('#mv_tripulantes').trigger('change');
                    });

                logSection.classList.remove('d-none');
                btnConfirmar.disabled = false;
                btnConfirmar.onclick = async () => {
                    const entregaExitosa = await procesarEntregaFinal();
                }
            }
        } catch (err) {
            contenedor.innerHTML = `<div class="alert alert-danger mx-2 small">${err.message}</div>`;
        }
    }

    async function procesarEntregaFinal() {
        const fd = new FormData();
        let hayCantidadValida = false;

        $('.input-entrega').each(function () {
            const cant = parseFloat($(this).val());

            if (cant > 0) {
                hayCantidadValida = true;
                const dvid = $(this).data('dvid');
                const index = $(this).attr('id').split('_').pop();
                const loteSeleccionado = $(`#merma_lote_${index}`).val();

                fd.append(`productos[${dvid}]`, cant);
                fd.append(`lotes[${dvid}]`, loteSeleccionado || '');
            }
        });

        if (!hayCantidadValida) {
            Swal.fire('Atención', 'Indique al menos una cantidad válida para entregar', 'warning');
            return false;
        }

        const tipo = $('input[name="tipo_entrega_masiva"]:checked').val();
        const vehiculoId = $('#mv_vehiculo_id').val();
        const choferId = $('#mv_chofer_id').val();
        const tripulanteId = $('#mv_tripulantes').val();
        const direccion = $('#mv_direccion').val() ? $('#mv_direccion').val().trim() : '';

        if (tipo === 'ruta') {
            if (!vehiculoId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos incompletos',
                    text: 'Por favor, selecciona un vehículo.',
                    confirmButtonColor: '#d33'
                });
                return false;
            }

            if (!choferId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos incompletos',
                    text: 'Por favor, selecciona un chofer.',
                    confirmButtonColor: '#d33'
                });
                return false;
            }
        }

        if (!direccion) {
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor, escribe una dirección.',
                confirmButtonColor: '#d33'
            });
            return false;
        }

        fd.append('tipo_logistica', tipo);
        fd.append('vehiculo_id', tipo === 'ruta' ? vehiculoId : 0);
        fd.append('chofer_id', choferId || '');
        fd.append('tripulante_id', tripulanteId || '');
        fd.append('direccion', direccion);
        fd.append('venta_id', ventaActual.info.id);

        console.log('Datos enviados:', Object.fromEntries(fd));

        try {
            const res = await fetch(`${URL_CONTROLLER}?action=guardarEntregaMasiva`, {
                method: 'POST',
                body: fd
            });

            const result = await res.json();
            console.log("Respuesta servidor:", result);

            if (result.status === 'success') {
                modalObj.hide();
                getVentas();

                Swal.fire({
                    title: '¡Listo!',
                    text: 'Entrega guardada correctamente',
                    icon: 'success',
                    showConfirmButton: true
                });

                console.log('ids', result.ids);
                ejecutarSalidaMasivaFinal(result.ids);
                return true;
            } else {
                Swal.fire('No se pudo entregar', result.message || 'Error desconocido', 'error');
                return false;
            }
        } catch (e) {
            console.error("Error al procesar entrega:", e);
            Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
            return false;
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target.matches('select[name^="merma_lote_"]')) {
            const index = e.target.name.split('_').pop();
            const item = window.carrito[index];
            const loteId = e.target.value;
            const optionSeleccionada = e.target.options[e.target.selectedIndex];
            const stockLote = parseFloat(optionSeleccionada.dataset.stock || 0);

            console.log('Índice:', index);
            console.log('Producto:', item.nombre);
            console.log('Lote seleccionado:', loteId);
            console.log('Stock lote:', stockLote);
            item.lote = loteId
            item.loteNombre = item.nombre;

            const inputEntrega = document.querySelector(
                `.input-entrega-modal[data-index="${index}"]`
            );
        }
    });

    async function ejecutarSalidaMasivaFinal(result_ids, loteSeleccionado) {
        const tipo = $('input[name="tipo_entrega_masiva"]:checked').val();

        if (!$('#mv_chofer_id').val()) return Swal.fire('Atención', 'Seleccione un responsable.', 'warning');
        if (tipo === 'ruta' && !$('#mv_vehiculo_id').val()) return Swal.fire('Atención', 'Seleccione una unidad.',
            'warning');

        try {
            const formData = new FormData();

            formData.append('ajax', 'despachar_venta_completaConLotes');
            formData.append('tipo_logistica', tipo);
            formData.append('vehiculo_id', tipo === 'ruta' ? $('#mv_vehiculo_id').val() : 0);
            formData.append('chofer_id', $('#mv_chofer_id').val());
            formData.append('direccion', $('#mv_direccion').val() || '');

            const tripulante = $('#mv_tripulantes').val() || 0;
            formData.append('tripulante_id', tripulante);
            console.log(tripulante);

            result_ids.forEach((id, index) => {
                formData.append('ids_movimientos[]', id.movimiento_id);
                formData.append('lotes[]', $(`#merma_lote_${index}`).val());
            });
            console.log('fate', Object.fromEntries(formData));

            const resp = await fetch('/myvet/app/controllers/entregasController.php', {
                method: 'POST',
                body: formData
            });
            const res = await resp.json();
            if (res.success) {
                modalObj.hide();
                modal.hide();
                getVentas();

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: res.ids,
                    timer: 150000,
                    showConfirmButton: true
                });
                setTimeout(() => {
                    cargarRepartos2(ventaActual.info.id);
                }, 501);
            } else {
                throw new Error(res.message);
            }
        } catch (e) {
            Swal.fire('Error de Despacho', e.message, 'error');
        }
    }

    async function cargarRepartos2(idVenta) {
        try {
            const resp = await fetch(
                `/myvet/app/controllers/repartosController.php?action=get_repartos_entrega&id=${idVenta}`
            );

            const repartoViaje = await resp.json();

            let repartos = repartoViaje.data || [];
            console.log(idVenta);

            if (repartos.length > 0) {
                const ultimoReparto = repartos[repartos.length - 1];
                console.log('Último reparto:', ultimoReparto.entrega_id);
                imprimirRuta(ultimoReparto.entrega_id, ultimoReparto.folio)
            } else {
                console.log('No hay repartos');
            }
        } catch (error) {
            console.error('Error al cargar repartos:', error);
        }
    }

    async function lotesporPropducto(producto_id, almacen_id, index) {
        const loteSelect = document.querySelector(`[name="merma_lote_${index}"]`);

        if (!loteSelect) {
            console.error(`No existe merma_lote_${index}`);
            return;
        }

        try {
            const response = await fetch(
                `/myvet/app/controllers/mermasController.php?action=obtenerLotes&producto_id=${producto_id}&almacen_id=${almacen_id}`
            );

            if (!response.ok) {
                throw new Error('Error HTTP: ' + response.status);
            }

            const lotes = await response.json();
            console.log('Lotes:', lotes);
            loteSelect.innerHTML = '<option value="">Seleccione lote</option>';

            if (!Array.isArray(lotes)) {
                throw new Error('La respuesta no es un array');
            }

            lotes.forEach(l => {
                const option = document.createElement('option');
                option.value = l.id;
                option.textContent = `${l.codigo_lote} (Disp: ${l.cantidad_actual})`;
                option.dataset.stock = l.cantidad_actual;
                loteSelect.appendChild(option);
            });

            loteSelect.disabled = false;
        } catch (e) {
            console.error('Error cargando lotes:', e);
            loteSelect.innerHTML = '<option value="">Error al cargar</option>';
        }
    }
</script>