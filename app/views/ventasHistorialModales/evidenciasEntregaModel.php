<!-- Modal de Evidencias de Entrega -->
<div class="modal fade" id="modalEvidenciasEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-dark text-white p-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-card-checklist me-2 text-warning"></i> Evidencias de Entrega
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 ">
                <!-- Spinner de Carga -->
                <div id="cargandoEvidencias" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted fw-bold">Obteniendo información del reparto...</p>
                </div>

                <!-- Contenedor Principal de Resultados -->
                <div id="contenidoEvidencias" class="d-none"></div>
            </div>

            <div class="modal-footer  border-top-0" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal"
                    style="border-radius: 10px;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Abre el modal y consulta las evidencias pasando el ID de entrega (folio)
     * @param {string|number} entregaId ID o Folio de entrega (Ej: 847)
     */
    let entregaNumeroId = 0;
    async function abrirModalEvidencias(entregaId) {
        entregaNumeroId = entregaId;
        const modalEl = document.getElementById('modalEvidenciasEntrega');
        const modalBs = new bootstrap.Modal(modalEl);
        const divCargando = document.getElementById('cargandoEvidencias');
        const divContenido = document.getElementById('contenidoEvidencias');

        // Mostrar modal y resetear estados
        divCargando.classList.remove('d-none');
        divContenido.classList.add('d-none');
        divContenido.innerHTML = '';
        modalBs.show();

        try {
            const url = `/myvet/app/controllers/misRepartosController.php?action=get_evidencias_por_entrega_id&folio=${entregaId}`;
            const res = await fetch(url);


            if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);

            const respuesta = await res.json();
            console.log(respuesta);
            if (respuesta.success && Array.isArray(respuesta.data) && respuesta.data.length > 0) {
                renderizarEvidencias(respuesta.data);
                divCargando.classList.add('d-none');
                divContenido.classList.remove('d-none');
            } else {
                divContenido.innerHTML = `
                <div class="alert alert-warning text-center rounded-4 shadow-sm my-3 p-4">
                    <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold">Sin evidencias registradas</h6>
                    <p class="mb-0 small">No se encontraron registros de entregas ni evidencias asociadas a este folio (${entregaId}).</p>
                </div>`;
                divCargando.classList.add('d-none');
                divContenido.classList.remove('d-none');
            }

        } catch (error) {
            console.error('Error al cargar evidencias:', error);
            divContenido.innerHTML = `
            <div class="alert alert-danger text-center rounded-4 shadow-sm my-3 p-4">
                <i class="bi bi-x-circle fs-1 d-block mb-2"></i>
                <h6 class="fw-bold">Error de Conexión</h6>
                <p class="mb-0 small">No se pudo consultar la información. Intente nuevamente.</p>
            </div>`;
            divCargando.classList.add('d-none');
            divContenido.classList.remove('d-none');
        }
    }

    /**
     * Renderiza dinámicamente las tarjetas de venta y evidencias
     * @param {Array} listaVentas 
     */
    function renderizarEvidencias(listaVentas) {
        const contenedor = document.getElementById('contenidoEvidencias');
        let html = '';

        listaVentas.forEach((v, index) => {
            const badgeEntregado = v.ya_entregado == 1
                ? '<span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Entregado</span>'
                : '<span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill"><i class="bi bi-clock-fill me-1"></i> Pendiente</span>';

            const clienteNombre = v.cliente ? v.cliente : 'VENTA MOSTRADOR / GENERAL';

            // Imagen Foto Entrega
            // Imagen Foto de Entrega
            const imgFotoHtml = v.foto_registrada
                ? `<div class="col-md-6 text-center mb-3">
        <label class="form-label micro-text fw-bold text-secondary d-block">FOTO DE ENTREGA</label>
        <a href="${v.foto_registrada}" target="_blank">
            <img src="${v.foto_registrada}" class="img-fluid rounded-3 border shadow-sm style-img-evidencia" style="max-height: 220px; object-fit: cover; width: 100%;">
        </a>
         <button type="button" class="btn btn-outline-success btn-sm" onclick="subirFotografiaEntrega(${v.id_movimiento}, ${v.id_venta}, ${v.vehiculo_id})">
                <i class="bi bi-upload me-1"></i> Actualizar Fotografía
            </button>
       </div>`
                : `<div class="col-md-6 text-center mb-3">
        <label class="form-label micro-text fw-bold text-secondary d-block">FOTO DE ENTREGA</label>
        <div class="p-3 bg-body-tertiary rounded-3 border text-muted small d-flex flex-column align-items-center justify-content-center" style="min-height: 160px;">
            <span class="mb-2">Sin fotografía de entrega</span>
            <button type="button" class="btn btn-outline-success btn-sm" onclick="subirFotografiaEntrega(${v.id_movimiento}, ${v.id_venta}, ${v.vehiculo_id})">
                <i class="bi bi-upload me-1"></i> Subir Fotografía
            </button>
        </div>
       </div>`;

            // Imagen Foto Nota / Comprobante
            const imgNotaHtml = v.nota_registrada
                ? `<div class="col-md-6 text-center mb-3">
        <label class="form-label micro-text fw-bold text-secondary d-block">NOTA / COMPROBANTE</label>
        <a href="${v.nota_registrada}" target="_blank">
            <img src="${v.nota_registrada}" class="img-fluid rounded-3 border shadow-sm style-img-evidencia" style="max-height: 220px; object-fit: cover; width: 100%;">
        </a>
        <button type="button" class="btn btn-outline-success btn-sm" onclick="subirNotaEntrega(${v.id_movimiento}, ${v.id_venta}, ${v.vehiculo_id})">
                <i class="bi bi-upload me-1"></i> Actualizar Nota
            </button>
       </div>`
                : `<div class="col-md-6 text-center mb-3">
        <label class="form-label micro-text fw-bold text-secondary d-block">NOTA / COMPROBANTE</label>
        <div class="p-3 bg-body-tertiary rounded-3 border text-muted small d-flex flex-column align-items-center justify-content-center" style="min-height: 160px;">
            <span class="mb-2">Sin foto de nota registrada</span>
            <button type="button" class="btn btn-outline-success btn-sm" onclick="subirNotaEntrega(${v.id_movimiento}, ${v.id_venta}, ${v.vehiculo_id})">
                <i class="bi bi-upload me-1"></i> Subir Nota
            </button>
        </div>
       </div>`;
            html += `
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header  border-bottom p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-receipt me-1"></i> Venta: ${v.folio_venta} 
                        <span class="text-muted small ms-2">| Viaje: ${v.folio_viaje}</span>
                    </h6>
                    <small class="text-secondary fw-semibold">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> ${v.direccion_entrega}
                    </small>
                </div>
                <div>${badgeEntregado}</div>
            </div>
            
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3  border rounded-3 h-100">
                            <span class="micro-text fw-bold text-muted d-block text-uppercase">Cliente</span>
                            <span class="fw-bold ">${clienteNombre}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3  border rounded-3 h-100">
                            <span class="micro-text fw-bold text-muted d-block text-uppercase">Comentario de Evidencia</span>
                            <span class="fw-semibold text-secondary">${v.comentario_evidencia ? v.comentario_evidencia : 'Sin observaciones'}</span>
                        </div>
                    </div>
                </div>

                <div class="p-3  border rounded-3 mb-3">
                    <span class="micro-text fw-bold text-muted d-block text-uppercase mb-2">Resumen de Productos</span>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold  small">${v.productos}</span>
                       
                    </div>
                   
                </div>

                <div class="row pt-2">
                    ${imgFotoHtml}
                    ${imgNotaHtml}
                </div>
            </div>
        </div>`;
        });

        contenedor.innerHTML = html;
    }
</script>
<script>
    // 1. Función para la Fotografía de Entrega
    function subirFotografiaEntrega(reparto_id, venta_id, vehiculo_id) {
        Swal.fire({
            title: 'Fotografía de Entrega',
            html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar fotografía</label>
                <input type="file" id="swal_file_foto" class="form-control mb-2" accept="image/*">
            </div>
        `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            confirmButtonColor: '#198754',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,

            preConfirm: async () => {
                const fileInput = document.getElementById('swal_file_foto');
                const file = fileInput?.files[0];

                if (!file) {
                    Swal.showValidationMessage('Selecciona una fotografía');
                    return false;
                }

                const formData = new FormData();
                formData.append('vehiculo_id', vehiculo_id);
                formData.append('id_venta', venta_id);
                formData.append('estatus_entrega', "Entregado");
                formData.append('id_movimiento', entregaNumeroId);
                formData.append('folio', reparto_id);
                formData.append('evidencia_foto', file);
                formData.append('action', 'subir_evidencia_reparto');

                try {
                    const response = await fetch(
                        '/myvet/app/controllers/misRepartosController.php',
                        {
                            method: 'POST',
                            body: formData
                        }
                    );

                    const text = await response.text();
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    let res;
                    try {
                        res = JSON.parse(text);
                    } catch {
                        throw new Error('El servidor devolvió HTML o texto inválido');
                    }

                    if (!res.success) {
                        throw new Error(res.message || 'Error al subir la fotografía');
                    }

                    return res;

                } catch (err) {
                    console.error(err);
                    Swal.showValidationMessage(err.message);
                    return false;
                }
            }
        }).then(result => {
            // Si el usuario canceló o hubo error en preConfirm, no hacemos nada
            if (!result.isConfirmed || !result.value) return;

            // Mostramos el aviso de éxito y al terminar abrimos de nuevo tu modal
            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Fotografía actualizada correctamente',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                // Reabrimos tu modal principal pasándole el ID correspondiente
                abrirModalEvidencias(entregaNumeroId);
            });
        });
    }


    // 2. Función para la Nota de Entrega
    function subirNotaEntrega(reparto_id, venta_id, vehiculo_id) {
        Swal.fire({
            title: 'Nota de Entrega',
            html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar nota de entrega</label>
                <input type="file" id="swal_file_nota" class="form-control mb-2" accept="image/*,.pdf">
            </div>
        `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            confirmButtonColor: '#198754',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,

            preConfirm: async () => {
                const fileInput = document.getElementById('swal_file_nota');
                const file = fileInput?.files[0];

                if (!file) {
                    Swal.showValidationMessage('Selecciona el archivo de la nota');
                    return false;
                }

                const formData = new FormData();
                formData.append('vehiculo_id', vehiculo_id);
                formData.append('id_venta', venta_id);
                formData.append('estatus_entrega', "Entregado");
                formData.append('id_movimiento', entregaNumeroId);
                formData.append('folio', reparto_id);
                formData.append('evidencia_nota', file);
                formData.append('action', 'subir_evidencia_reparto');

                try {
                    const response = await fetch(
                        '/myvet/app/controllers/misRepartosController.php',
                        {
                            method: 'POST',
                            body: formData
                        }
                    );

                    const text = await response.text();
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    let res;
                    try {
                        res = JSON.parse(text);
                    } catch {
                        throw new Error('El servidor devolvió HTML o texto inválido');
                    }

                    if (!res.success) {
                        throw new Error(res.message || 'Error al subir la nota');
                    }

                    return res;

                } catch (err) {
                    console.error(err);
                    Swal.showValidationMessage(err.message);
                    return false;
                }
            }
        }).then(result => {
            // Si el usuario canceló o hubo error en preConfirm, no hacemos nada
            if (!result.isConfirmed || !result.value) return;

            // Mostramos el aviso de éxito y al terminar abrimos de nuevo tu modal
            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Nota actualizada correctamente',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                // Reabrimos tu modal principal pasándole el ID correspondiente
                abrirModalEvidencias(entregaNumeroId);
            });
        });
    }
</script>

<style>
    .micro-text {
        font-size: 0.725rem;
        letter-spacing: 0.5px;
    }

    .style-img-evidencia {
        transition: transform 0.2s ease-in-out;
    }

    .style-img-evidencia:hover {
        transform: scale(1.02);
    }
</style>