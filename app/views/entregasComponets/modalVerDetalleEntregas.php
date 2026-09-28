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
                <button type="button" class="btn btn-light rounded-pill px-5 fw-bold text-muted" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
window.verEntrega = async function(movimientoId) {
    if (!movimientoId) return;
    
    // 1. Reset visual
    $('#v_folio_ticket').text('CARGANDO...');
    $('#v_producto_nombre').text('Buscando...');
    $('#v_cliente_final').html('<span class="spinner-border spinner-border-sm text-primary"></span> Obteniendo datos...');
    $('#contenedor_despacho').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>');
    
    $('#modalVerEntrega').modal('show'); 

    try {
        // Apuntamos al controlador de entregas que es el que procesa la acción
        const url = `/myvet/app/controllers/entregasController.php?ajax=get_resumen_despacho&id=${movimientoId}`;
        const response = await fetch(url);
        
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        // Limpieza de respuesta para evitar errores de JSON.parse por espacios o HTML
        const rawText = await response.text();
        const cleanText = rawText.trim();
        
        // Validamos si la respuesta empieza con un error de PHP (típico <!DOCTYPE)
        if (cleanText.startsWith('<')) {
            console.error("Respuesta inesperada:", cleanText);
            throw new Error("El servidor respondió con HTML en lugar de JSON. Revisa el exit; en PHP.");
        }
        

        const res = JSON.parse(cleanText);
        
        if (res.success && res.data) {
            const d = res.data;
console.log(d);
            // 2. Llenado de Cabecera
            $('#v_folio_ticket').text(`TICKET: ${d.folio_venta || 'S/N'}`);
            $('#v_producto_nombre').text(`${d.producto_nombre || 'Producto'} - ${d.cantidad || '0'} pzas`);
            
            const nombreCliente = d.cliente ? d.cliente.toUpperCase() : 'VENTA GENERAL';
            $('#v_cliente_final').html(`<i class="bi bi-person-check-fill text-primary me-1"></i> ${nombreCliente}`);

            let htmlBody = '';
            
            // Lógica: Si el vehículo es ID 999 o contiene "Mostrador" o no hay reparto_id
            const esMostrador = (d.vehiculo && (d.vehiculo.includes('999') || d.vehiculo.toUpperCase().includes('MOSTRADOR'))) || 
                                (d.direccion_entrega && d.direccion_entrega.toUpperCase().includes('MOSTRADOR'));

            if (d.reparto_id && !esMostrador) {
                // --- VISTA: REPARTO EN RUTA ---
                const listaTripulantes = (d.tripulantes && d.tripulantes.length > 0)
                    ? d.tripulantes.map(t => `<span class="badge bg-white text-dark border shadow-sm me-1" style="font-size:0.7rem;">${t.nombre}</span>`).join('')
                    : '<span class="text-muted small italic">Sin ayudantes asignados</span>';

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
                                    <label class="text-muted d-block mb-0" style="font-size: 0.6rem; font-weight: 700;">CAPTURÓ</label>
                                    <span class="fw-bold text-primary small">${d.usuario_asigno_sistema || 'Admin'}</span>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-white rounded-4 border border-light shadow-sm mb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="text-muted small d-block mb-1" style="font-weight: 600;"><i class="bi bi-calendar-event text-primary me-1"></i>Fecha Salida</label>
                                        <span class="fw-bold text-dark" style="font-size: 0.8rem;">${d.fecha_patio || d.fecha_movimiento}</span>
                                    </div>
                                    <div class="col-6 border-start">
                                        <label class="text-muted small d-block mb-1" style="font-weight: 600;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Destino</label>
                                        <span class="fw-bold text-dark text-truncate d-block" style="font-size: 0.8rem;">${d.direccion_entrega || 'Sucursal'}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-7">
                                    <label class="text-muted d-block small mb-0">Chofer:</label>
                                    <span class="fw-bold text-dark text-uppercase small">${d.trabajador_entrega_ruta || 'No asignado'}</span>
                                </div>
                                <div class="col-5 text-end">
                                    <label class="text-muted d-block small mb-0">Unidad:</label>
                                    <span class="d-block fw-bold small text-dark">${d.vehiculo || 'S/V'}</span>
                                    <span class="badge bg-dark text-white font-monospace" style="font-size: 0.6rem;">${d.placas || '---'}</span>
                                </div>
                            </div>

                            <div class="pt-3 border-top">
                                <label class="text-muted small d-block mb-2" style="font-weight: 600;">Ayudantes:</label>
                                <div class="d-flex flex-wrap gap-1">${listaTripulantes}</div>
                            </div>
                        </div>
                    </div>`;

            } else {
                // --- VISTA: MOSTRADOR / PATIO ---
                htmlBody = `
                    <div class="card  shadow-sm animate__animated animate__fadeIn" style="border-radius: 24px; background: linear-gradient(145deg, #f2fdf5, #ffffff);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success text-white rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                        <i class="bi bi-shop fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-0">Entrega Mostrador</h6>
                                        <small class="text-muted fw-bold" style="font-size:0.7rem;">Despacho Directo</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <label class="text-muted d-block mb-0" style="font-size: 0.6rem; font-weight: 700;">VALIDÓ</label>
                                    <span class="fw-bold text-success small">${d.usuario_valida_patio || 'Admin'}</span>
                                </div>
                            </div>

                            <div class="p-3 bg-white rounded-4 border border-light shadow-sm mb-3">
                                <label class="text-muted d-block small mb-1" style="font-weight: 600;"><i class="bi bi-clock-history text-success me-1"></i>Fecha Despacho</label>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">${d.fecha_patio || d.fecha_movimiento}</span>
                            </div>

                            <div class="p-3 bg-white rounded-4 border border-light shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="text-muted d-block small mb-0">Encargado de Entrega:</label>
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
            throw new Error(res.message || "Sin registros de salida.");
        }
    } catch (error) {
        console.error("Error en verEntrega:", error);
        $('#v_cliente_final').html(`<span class="text-danger">Error de datos</span>`);
        $('#contenedor_despacho').html(`
            <div class="alert alert-warning  rounded-4 p-4 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-3 d-block mb-2"></i>
                <h6 class="fw-bold">No se puede mostrar el detalle</h6>
                <p class="small mb-0">${error.message}</p>
            </div>
        `);
    }
};
</script>