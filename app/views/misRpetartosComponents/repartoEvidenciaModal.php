<div class="modal fade animate__animated animate__fadeIn" id="modalEvidenciasRuta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content  shadow-lg" style="border-radius: 20px; background: #f5f5f7;">
            <div class="modal-header  bg-white p-4" style="border-radius: 20px 20px 0 0;">
                <div>
                    <h5 class="modal-title fw-bold  mb-0">Evidencias del Reparto</h5>
                    <small id="txtFolioRuta" class="text-body-secondary fw-bold"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" id="contenedorEvidenciasRuta">
            </div>

            <div class="modal-footer  bg-white justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .entrega-item-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .img-evidencia-thumb {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        cursor: pointer;
        transition: transform 0.2s ease;
        border: 1px solid #eee;
    }

    .img-evidencia-thumb:hover {
        transform: scale(1.03);
    }

    .label-foto {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #8e8e93;
        display: block;
        margin-bottom: 4px;
        text-align: center;
    }

    .badge-estado-entrega {
        font-size: 0.6rem;
        padding: 4px 10px;
        border-radius: 50px;
    }
</style>
<script>
    /**
 * Función principal para ver evidencias de un viaje específico
 * @param {string} viajeFolio - El folio de la ruta (Ej: RUT-2026-0203)
 */
    let datosTemporales = [];
    function verEvidenciasPorFolio(viajeFolio) {
        // 1. Preparar el modal y la interfaz
        $('#txtFolioRuta').text(viajeFolio);
        const contenedor = $('#contenedorEvidenciasRuta');

        contenedor.html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p class="text-body-secondary small">Obteniendo reportes de entrega...</p>
        </div>
    `);

        const modalElement = document.getElementById('modalEvidenciasRuta');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();

        // 2. Petición AJAX al controlador
        $.ajax({
            url: '/myvet/app/controllers/misRepartosController.php',
            type: 'GET',
            data: {
                action: 'get_evidencias_por_venta',
                folio: viajeFolio
            },
            dataType: 'json',
            success: function (response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    console.log(response.data);
                    response.data.forEach((entrega, index) => {

                        const entregaJson = JSON.stringify(entrega)
                            .replace(/\\n/g, ' ')
                            .replace(/\\r/g, ' ')
                            .replace(/"/g, '&quot;'); // Protege las comillas dobles

                        // Verificamos si existe la evidencia
                        // Si alguna de las dos fotos tiene contenido, consideramos que ya existe evidencia
                        const existeEvidencia = (entrega.foto_registrada || entrega.nota_registrada);
                        console.log(existeEvidencia);
                        const cantidadIni = entrega.totalCantidad / entrega.fc;
                        const cantidad = cantidadIni >= 1 ? cantidadIni : entrega.totalCantidad;
                        const unidad = cantidadIni >= 1 ? entrega.ur : entrega.um;
                        html += `
                    <div class="entrega-item-card animate__animated animate__fadeInUp mb-4 p-3 border rounded-4 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-0" style="color: #1d1d1f;">${entrega.cliente}</h6>
                                <p class="text-body-secondary mb-0" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> ${entrega.direccion_entrega}
                                </p>
                                 <h6 class="fw-bold mb-0" style="color: #1d1d1f;">${entrega.productos}</h6>
                              
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                <span class="badge rounded-pill ${existeEvidencia ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'} border px-3" style="font-size: 0.65rem;">
                                    ${entrega.estatus_evidencia || 'PENDIENTE'}
                                </span>
                                ${esSupervisor ? `
                                    <div class="btn-group mt-1">
                                        ${existeEvidencia ? `
                                            <button class="btn btn-sm btn-outline-primary  p-1" 
                                                onclick="abrirModalPorIndex(${index}, '${entregaJson}','${viajeFolio}')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            <button class="btn btn-sm btn-outline-danger  p-1" onclick="confirmarEliminarEvidencia(${entrega.id_venta})" title="Eliminar evidencia">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        ` : `
                                            <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" style="font-size: 0.6rem;"
                                                onclick="abrirModalPorIndex(${index}, '${entregaJson}','${viajeFolio}')">
                                                <i class="bi bi-plus-circle me-1"></i> SUBIR
                                            </button>
                                        `}
                                    </div>
                                ` : ''}
                            </div>
                        </div>


                        <div class="my-3 p-2 rounded-3 " style="border-left: 3px solid #007aff;">
                            <p class="mb-0  italic" style="font-size: 0.8rem;">
                                <i class="bi bi-chat-left-text me-1 text-body-secondary"></i> "${entrega.comentario || 'Sin comentarios'}"
                            </p>
                        </div>

                        <div class="row g-2">
                            ${entrega.foto_registrada ? `
                                <div class="col-6">
                                    <span class="d-block mb-1 text-body-secondary fw-bold" style="font-size: 0.6rem; text-transform: uppercase;">Material</span>
                                    <img src="${entrega.foto_registrada}" class="img-fluid rounded-3 shadow-sm border" style="height: 100px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                </div>` : ''}
                            
                            ${entrega.nota_registrada ? `
                                <div class="col-6">
                                    <span class="d-block mb-1 text-body-secondary fw-bold" style="font-size: 0.6rem; text-transform: uppercase;">Nota</span>
                                    <img src="${entrega.nota_registrada}" class="img-fluid rounded-3 shadow-sm border" style="height: 100px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                </div>` : ''}
                        </div>

                        <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                            
                            <small class="fw-bold text-primary" style="font-size: 0.7rem;">
                                Venta: #${entrega.id_venta || 'S/F'}
                            </small>
                        </div>
                    </div>`;
                    });
                    contenedor.html(html);
                } else {
                    contenedor.html(`
                    <div class="text-center py-5">
                        <i class="bi bi-camera-video-off display-4 text-body-secondary mb-3"></i>
                        <p class="text-body-secondary">No se encontraron evidencias cargadas para esta ruta.</p>
                    </div>
                `);

                }
            }
        });
    }
    /**
     * Funciones de Acción para el Supervisor
     */
    /**
     * Confirma y elimina una evidencia usando el ID del movimiento (punto de ruta)
     * @param {number} id - El id_movimiento (proviene de transporte_rutas_puntos)
     */
    function confirmarEliminarEvidencia(id) {
        // 1. Validamos que el ID sea correcto
        console.log("ID de movimiento a eliminar:", id);

        if (!id || id === 0) {
            Swal.fire('Error', 'No se identificó un movimiento válido para eliminar.', 'error');
            return;
        }

        // 2. Rescatamos el folio de la ruta desde el texto del modal para poder recargar después
        const folioRuta = $('#txtFolioRuta').text();

        // 3. Alerta de confirmación
        Swal.fire({
            title: '¿Eliminar evidencia?',
            text: "Esta acción borrará las fotos y el registro. El punto de entrega volverá a quedar como 'PENDIENTE'.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {

                // Mostramos estado de carga
                Swal.fire({
                    title: 'Procesando...',
                    didOpen: () => { Swal.showLoading(); }
                });

                // 4. Petición AJAX al controlador
                $.ajax({
                    url: '/myvet/app/controllers/misRepartosController.php',
                    type: 'POST',
                    data: {
                        action: 'eliminar_evidencia',
                        id_movimiento: id // Enviamos el ID que recibiste por parámetro
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // 5. RECARGA AUTOMÁTICA: 
                            // Volvemos a llamar a la función que pinta el listado 
                            // para que el botón cambie de "Editar" a "SUBIR"
                            verEvidenciasPorFolio(folioRuta);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo conectar con el servidor para eliminar el registro.', 'error');
                    }
                });
            }
        });
    }
</script>
<?php require_once __DIR__ . '/editarEvidenciaModal.php' ?>