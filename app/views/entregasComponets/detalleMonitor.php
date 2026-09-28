<div class="modal fade" id="modalVerDetalle" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 30px; background-color: #f8f9fa; overflow: hidden;">
            
            <div class="modal-header  pt-4 px-4 pb-0">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span id="v_badge_tipo" class="badge rounded-pill px-3 py-2" style="font-weight: 800; font-size: 0.6rem;">---</span>
                        <button type="button" class="btn-close shadow-none bg-white rounded-circle p-2" data-bs-dismiss="modal"></button>
                    </div>
                    <h4 class="fw-bold text-dark mb-0" id="v_titulo_modal" style="letter-spacing: -0.5px;">Detalle de Salida</h4>
                </div>
            </div>

            <div class="modal-body p-4" id="contenedor_detalle_ios"></div>

            <div class="modal-footer  justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-white border shadow-sm rounded-pill px-5 fw-bold text-body-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
async function verDetalleEntrega(tipo, id) {
    if (!id || id === 0) return;

    // Primero mostramos el modal, luego el spinner
    const modalEl = document.getElementById('modalVerDetalle');
    const modalInstancia = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInstancia.show();

    const contenedor = $('#contenedor_detalle_ios');
    contenedor.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2 small fw-bold text-body-secondary">Cargando...</p></div>');

    try {
        const url = `/myvet/app/controllers/repartosController.php?action=get_detalle_trazabilidad&tipo=${tipo}&id=${id}`;
        const response = await fetch(url);
        const res = await response.json();

        if (!res.success) throw new Error(res.message);

        pintarHTMLDetalle(tipo, res.data);

    } catch (error) {
        contenedor.html(`<div class="alert alert-danger rounded-4  text-center small fw-bold">${error.message}</div>`);
    }
}

function pintarHTMLDetalle(tipo, d) {
    const contenedor = $('#contenedor_detalle_ios');
    const esRuta = (tipo === 'RUTA');

    if (esRuta) {
        $('#v_badge_tipo')
            .removeClass('bg-success-subtle text-success')
            .addClass('bg-primary text-white')
            .text(`VIAJE: ${d.viaje_folio || 'S/F'}`);
        $('#v_titulo_modal').text('Hoja de Ruta');
    } else {
        $('#v_badge_tipo')
            .removeClass('bg-primary text-white')
            .addClass('bg-success-subtle text-success')
            .text('SALIDA DIRECTA');
        $('#v_titulo_modal').text('Salida de Patio');
    }

    const nombreResponsable = esRuta
        ? (d.chofer       || 'SIN CHOFER ASIGNADO')
        : (d.usuario_patio || 'PERSONAL DE PATIO');

    let html = `
    <div class="card  shadow-sm p-3 mb-3" style="border-radius: 20px; background: white;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary-subtle p-2 me-3 text-primary shadow-sm">
                <i class="bi ${esRuta ? 'bi-truck' : 'bi-person-badge'}" style="font-size: 1.2rem;"></i>
            </div>
            <div>
                <label class="text-body-secondary d-block mb-0" style="font-size: 0.55rem; font-weight: 800;">
                    ${esRuta ? 'CHOFER / RESPONSABLE' : 'RESPONSABLE DE PATIO'}
                </label>
                <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">${nombreResponsable}</span>
                ${esRuta && d.vehiculo
                    ? `<small class="text-body-secondary" style="font-size:0.65rem;">${d.vehiculo} &bull; <b>${d.placas || ''}</b></small>`
                    : ''}
            </div>
        </div>
    </div>`;

    if (d.usuario_asigno_sistema) {
        html += `
        <div class="d-flex align-items-center bg-white rounded-4 px-3 py-2 mb-3 shadow-sm">
            <i class="bi bi-person-check text-secondary me-2" style="font-size:0.85rem;"></i>
            <div>
                <label class="text-body-secondary d-block" style="font-size: 0.5rem; font-weight: 800;">REGISTRADO EN SISTEMA POR</label>
                <span class="fw-bold text-dark" style="font-size: 0.75rem;">${d.usuario_asigno_sistema}</span>
            </div>
        </div>`;
    }

    if (esRuta) {
        const productos = d.lista_productos || [];

        if (productos.length > 0) {
            html += `<label class="text-body-secondary d-block mb-2 px-1" style="font-size: 0.6rem; font-weight: 800;">ENTREGAS EN ESTE VIAJE</label>`;

            productos.forEach(item => {
                html += `
                <div class="bg-white rounded-4 p-3 mb-3 shadow-sm border-start border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-start mb-2 border-bottom pb-2">
                        <div>
                            <span class="badge bg-light text-primary mb-1" style="font-size: 0.55rem;">
                                TICKET #${item.ticket || 'S/F'}
                            </span>
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                ${item.cliente_destino || 'CLIENTE GENERAL'}
                            </div>
                        </div>
                        <div class="text-end ps-2">
                            <span class="fw-bold text-dark" style="font-size: 1rem;">${item.cantidad}</span>
                            <div class="text-body-secondary" style="font-size: 0.5rem; font-weight: 800;">CANT.</div>
                        </div>
                    </div>
                    <div>
                        <label class="text-body-secondary d-block" style="font-size: 0.5rem; font-weight: 800;">PRODUCTO</label>
                        <span class="text-dark fw-bold" style="font-size: 0.75rem;">${item.producto}</span>
                    </div>
                </div>`;
            });
        } else {
            html += `<p class="text-center text-body-secondary small py-3">Sin productos registrados en este viaje.</p>`;
        }

        if (d.tripulantes && d.tripulantes.length > 0) {
            const chips = d.tripulantes.map(t => `
                <div class="bg-white border rounded-pill px-3 py-1 me-2 mb-2 shadow-xs d-flex align-items-center">
                    <div class="bg-primary rounded-circle me-2" style="width:5px;height:5px;"></div>
                    <span style="font-size:0.65rem;font-weight:700;">${t.nombre}</span>
                </div>`).join('');

            html += `
            <div class="mt-2">
                <label class="text-body-secondary d-block mb-2 px-1" style="font-size: 0.6rem; font-weight: 800;">AYUDANTES / TRIPULACIÓN</label>
                <div class="d-flex flex-wrap">${chips}</div>
            </div>`;
        }

    } else {
        html += `
        <div class="bg-white rounded-4 p-3 shadow-sm mb-3">
            <label class="text-body-secondary d-block mb-1" style="font-size: 0.55rem; font-weight: 800;">CLIENTE</label>
            <span class="fw-bold text-dark" style="font-size: 0.9rem;">${d.cliente || 'VENTA GENERAL'}</span>
            ${d.folio_venta ? `<span class="badge bg-light text-secondary ms-2" style="font-size:0.55rem;">FOLIO #${d.folio_venta}</span>` : ''}
        </div>

        <div class="bg-white rounded-4 p-3 shadow-sm mb-3 d-flex justify-content-between align-items-center">
            <div>
                <label class="text-body-secondary d-block" style="font-size: 0.5rem; font-weight: 800;">PRODUCTO</label>
                <span class="fw-bold text-dark" style="font-size: 0.8rem;">${d.producto_nombre || d.producto || '—'}</span>
            </div>
            <div class="text-end">
                <span class="fw-bold text-dark" style="font-size: 1.1rem;">${d.cantidad}</span>
                <div class="text-body-secondary" style="font-size: 0.5rem; font-weight: 800;">CANT.</div>
            </div>
        </div>

        ${d.fecha_patio ? `
        <div class="d-flex align-items-center bg-white rounded-4 px-3 py-2 shadow-sm">
            <i class="bi bi-clock-history text-secondary me-2" style="font-size:0.85rem;"></i>
            <div>
                <label class="text-body-secondary d-block" style="font-size: 0.5rem; font-weight: 800;">FECHA DE DESPACHO EN PATIO</label>
                <span class="fw-bold text-dark" style="font-size: 0.75rem;">${d.fecha_patio}</span>
            </div>
        </div>` : ''}`;
    }

    contenedor.html(html);
}
</script>
