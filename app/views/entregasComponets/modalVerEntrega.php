<div class="modal fade" id="modalVerEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 28px;">
            <div class="modal-header  pt-4 px-4 pb-2">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-primary-subtle text-primary  px-3 py-2" id="v_folio_ticket" style="border-radius: 12px; font-weight: 800;">FOLIO: ---</span>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark" id="v_producto_nombre">Cargando...</h4>
                    <div id="v_cliente_final" class="text-secondary fw-medium" style="font-size: 0.85rem;">
                        <i class="bi bi-person me-1"></i> Cargando datos...
                    </div>
                </div>
            </div>
            <div class="modal-body p-4" id="contenedor_despacho">
                </div>
            <div class="modal-footer  justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-5 fw-bold text-body-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>window.verEntrega = async function(movimientoId) {
    if (!movimientoId) return;
    
    // 1. Reset visual estilo iOS
    $('#v_folio_ticket').text('CARGANDO...');
    $('#v_producto_nombre').text('Buscando...');
    $('#v_cliente_final').html('<span class="spinner-border spinner-border-sm text-primary"></span> Obteniendo datos...');
    $('#contenedor_despacho').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>');
    
    $('#modalVerEntrega').modal('show'); 

    try {
        const response = await fetch(`/myvet/app/controllers/repartosController.php?action=get_resumen_despacho&id=${movimientoId}`);
        const res = await response.json();
        
        if (res.success && res.data) {
            const d = res.data;
            console.log("hola",res.data)
             const cantidadIni=d.cantidad/d.fc;
                const cantidad =cantidadIni>=1?cantidadIni:d.cantidad;
                const unidad =cantidadIni>=1?d.ur:d.um;

            // 2. CABECERA
            $('#v_folio_ticket').text(`TICKET: ${d.folio_venta || 'S/N'}`);
            $('#v_producto_nombre').text(`${d.producto_nombre || 'Producto'} - ${cantidad || '0'}  ${unidad || 'pza'}`);
            
            const nombreCliente = d.cliente ? d.cliente.toUpperCase() : 'VENTA GENERAL';
            $('#v_cliente_final').html(`<i class="bi bi-person-check-fill text-primary me-1"></i> ${nombreCliente}`);

            let htmlBody = '';
            
            // Lógica de detección: ID 999 o palabra "Mostrador" en vehículo/dirección
            const esMostrador = (d.vehiculo && (d.vehiculo.includes('999') || d.vehiculo.toUpperCase().includes('MOSTRADOR'))) || 
                                (d.direccion_entrega && d.direccion_entrega.toUpperCase().includes('MOSTRADOR'));

            if (d.reparto_id && !esMostrador) {
                // --- DISEÑO A: ENTREGA EN RUTA (LOGÍSTICA) ---
                const listaTripulantes = (d.tripulantes && d.tripulantes.length > 0)
                    ? d.tripulantes.map(t => `<span class="badge bg-white text-dark border shadow-sm me-1" style="font-size:0.7rem;">${t.nombre}</span>`).join('')
                    : '<span class="text-body-secondary small italic">Sin ayudantes asignados</span>';

                htmlBody = `
                    <div class="card  shadow-sm animate__animated animate__fadeIn" style="border-radius: 24px; background: linear-gradient(145deg, #f0f7ff, #ffffff);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                        <i class="bi bi-truck fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-0">Entrega en Ruta</h6>
                                        <span class="badge bg-primary-subtle text-primary  rounded-pill" style="font-size:0.65rem;">Viaje: ${d.viaje_folio || 'S/F'}</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <label class="text-body-secondary d-block mb-0" style="font-size: 0.6rem; font-weight: 700;">CAPTURÓ</label>
                                    <span class="fw-bold text-primary small">${d.usuario_asigno_sistema || 'Admin'}</span>
                                </div>
                            </div>
                            
                           <div class="p-3 bg-white rounded-4 border border-light shadow-sm mb-3">
    <div class="row align-items-center">
        <div class="col-7">
            <div class="d-flex mb-2">
                <div class="me-3">
                    <label class="text-body-secondary small d-block mb-0" style="font-weight: 600; font-size: 0.7rem;">
                        <i class="bi bi-calendar-check text-primary me-1"></i>SALIDA
                    </label>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                        ${d.fecha_patio || d.fecha_movimiento}
                    </span>
                </div>
                <div class="ps-3 border-start">
                    <label class="text-body-secondary small d-block mb-0" style="font-weight: 600; font-size: 0.7rem;">
                        <i class="bi bi-flag-fill text-success me-1"></i>LLEGADA
                    </label>
                    <span class="fw-bold ${d.fecha_llegada ? 'text-dark' : 'text-primary'}" style="font-size: 0.85rem;">
                        ${d.fecha_llegada ? d.fecha_llegada : '<span class="badge rounded-pill bg-light text-primary border border-primary-subtle" style="font-size: 0.65rem;">EN TRÁNSITO</span>'}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-5 border-start">
            <div class="ps-2">
                <label class="text-body-secondary small d-block mb-0" style="font-weight: 600; font-size: 0.7rem;">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>DESTINO FINAL
                </label>
                <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="${d.direccion_entrega || 'Entrega en Sucursal'}">
                    ${d.direccion_entrega || 'Sucursal'}
                </div>
            </div>
        </div>
    </div>
</div>

                            <div class="row g-2 mb-3">
                                <div class="col-7">
                                    <label class="text-body-secondary d-block small mb-0">Chofer:</label>
                                    <span class="fw-bold text-dark text-uppercase small">${d.trabajador_entrega_ruta || 'No asignado'}</span>
                                </div>
                                <div class="col-5 text-end">
                                    <label class="text-body-secondary d-block small mb-0">Unidad:</label>
                                    <span class="d-block fw-bold small text-dark">${d.vehiculo || 'S/V'}</span>
                                    <span class="badge bg-dark text-white font-monospace" style="font-size: 0.6rem;">${d.placas || '---'}</span>
                                </div>
                            </div>

                            <div class="pt-3 border-top">
                                <label class="text-body-secondary small d-block mb-2" style="font-weight: 600;">Equipo de Apoyo:</label>
                                <div class="d-flex flex-wrap gap-1">${listaTripulantes}</div>
                            </div>
                        </div>
                    </div>`;

            } else {
                // --- DISEÑO: ENTREGA EN MOSTRADOR / PATIO ---
                htmlBody = `
                    <div class="card  shadow-sm animate__animated animate__fadeIn" style="border-radius: 24px; background: linear-gradient(145deg, #f2fdf5, #ffffff);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success text-white rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                        <i class="bi bi-shop fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-0">Entrega en Mostrador</h6>
                                        <small class="text-body-secondary fw-bold" style="font-size:0.7rem;">Despacho Directo</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <label class="text-body-secondary d-block mb-0" style="font-size: 0.6rem; font-weight: 700;">VALIDÓ</label>
                                    <span class="fw-bold text-success small">${d.usuario_valida_patio || 'Admin'}</span>
                                </div>
                            </div>

                            <div class="p-3 bg-white rounded-4 border border-light shadow-sm mb-3">
                                <label class="text-body-secondary d-block small mb-1" style="font-weight: 600;"><i class="bi bi-clock-history text-success me-1"></i>Fecha de Salida</label>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">${d.fecha_patio || d.fecha_movimiento}</span>
                            </div>

                            <div class="p-3 bg-white rounded-4 border border-light shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="text-body-secondary d-block small mb-0">Encargado de Entrega:</label>
                                        <span class="fw-bold text-dark text-uppercase">${d.trabajador_despacho_patio || d.trabajador_entrega_ruta || 'Personal de Patio'}</span>
                                    </div>
                                    <i class="bi bi-patch-check-fill text-success fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }

            $('#contenedor_despacho').html(htmlBody);

        } else {
            throw new Error(res.message || "No se encontraron datos");
        }
    } catch (error) {
        console.error("Error modal:", error);
        $('#v_cliente_final').text("Error de carga");
        $('#contenedor_despacho').html(`<div class="alert alert-danger mx-3 small">${error.message}</div>`);
    }
};</script>