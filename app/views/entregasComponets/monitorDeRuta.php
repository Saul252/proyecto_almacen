<style>
    /* Estilos adaptables para el Monitor de Viajes (Línea iOS Glassmorphism) */
    .card-monitor {
        background: var(--bs-body-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--bs-border-color-translucent);
        border-radius: 22px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .header-monitor {
        background: #1d1d1f; 
        color: #ffffff;
        padding: 1.2rem 1.5rem;
        
    }

    /* Select de Almacenes en Header */
    #filtroAlmacenMonitor {
        background-color: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    #filtroAlmacenMonitor:hover {
        background-color: rgba(255, 255, 255, 0.25);
    }
    #filtroAlmacenMonitor option {
        color: var(--bs-body-color);
        background-color: var(--bs-body-bg);
    }

    .table-monitor thead th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--bs-secondary-color);
        font-weight: 600;
        padding: 1.2rem;
        border-bottom: 2px solid var(--bs-border-color);
        background: transparent;
    }

    .table-monitor tbody tr {
        transition: all 0.2s ease;
    }

    .table-monitor tbody tr:hover {
        background-color: var(--bs-tertiary-bg);
    }

    .badge-folio {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        font-family: 'SF Mono', SFMono-Regular, ui-monospace, monospace;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        display: inline-block;
    }

    .carga-scroll {
        background: var(--bs-tertiary-bg);
        border-radius: 12px;
        padding: 12px;
        font-size: 0.85rem;
        color: var(--bs-body-color);
        max-height: 100px;
        overflow-y: auto;
        border: 1px solid var(--bs-border-color-translucent);
    }

    .avatar-chofer {
        width: 36px;
        height: 36px;
        background: #0d6efd;
        color: #ffffff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }

    .btn-finish {
        background: #198754;
        color: #ffffff;
        
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-finish:hover {
        background: #157347;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }
</style>

<div class="main-content">
    <div class="card card-monitor animate__animated animate__fadeIn">
        <div class="header-monitor d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-white d-flex align-items-center">
                <i class="bi bi-geo-fill me-2 text-primary"></i> Monitor de Viajes Activos
            </h5>

            <button class="btn btn-sm btn-outline-light rounded-pill px-3 border-opacity-25" onclick="cargarMonitorViajes()">
                <i class="bi bi-arrow-repeat me-1"></i> Actualizar
            </button>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-monitor align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Unidad / Folio Ruta</th>
                            <th>Chofer Responsable</th>
                            <th>Tripulación</th>
                            <th>Carga Consolidada</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="bodyMonitorViajes">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.cargarMonitorViajes = async function() {
    const body = $('#bodyMonitorViajes');
    
    // Obtenemos el almacén si el select existe (Admin), si no mandamos vacío
    const selectAlm = document.getElementById('filtroAlmacenMonitor');
    const almacenId = selectAlm ? selectAlm.value : '';

    try {
        body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div><div class="mt-2 text-body-secondary small">Consultando rutas...</div></td></tr>');
        
        const resp = await fetch(`/myvet/app/controllers/repartosController.php?action=listar_viajes_activos&almacen_id=${almacenId}`);
        const result = await resp.json();
        
        const data = result.data || result; 

        if (result.success === false) {
             body.html(`<tr><td colspan="5" class="text-center py-5 text-danger">${result.message}</td></tr>`);
             return;
        }

        if (!data || data.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-5 text-body-secondary"><i class="bi bi-geo-alt fs-2 d-block mb-2 opacity-25"></i> No hay unidades en ruta actualmente</td></tr>');
            return;
        }

        body.empty();
        data.forEach(v => {
            const listaAyudantes = v.tripulantes 
                ? `<div class="small text-body-secondary fw-medium"><i class="bi bi-people-fill me-1 text-primary"></i> ${v.tripulantes}</div>`
                : `<span class="badge bg-light text-secondary fw-normal border" style="font-size:0.65rem;">Solo Chofer</span>`;

            body.append(`
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <div class="fw-bold text-dark" style="font-size:0.95rem;">${v.unidad}</div>
                        <div class="badge-folio mt-1"><i class="bi bi-hash"></i>${v.viaje_folio}</div>
                        <div class="small text-body-secondary mt-1" style="font-size:0.7rem;">📍 ${v.almacen_nombre || 'N/A'}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-chofer me-3">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size: 0.8rem; color:#1d1d1f;">${v.chofer}</div>
                                <small class="text-body-secondary">Conductor</small>
                            </div>
                        </div>
                    </td>
                    <td>${listaAyudantes}</td>
                    <td>
                        <div class="carga-scroll">
                            ${v.detalles_carga}
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-finish btn-sm" 
                                onclick="finalizarViaje(${v.vehiculo_id}, '${v.viaje_folio}')">
                            <i class="bi bi-check-all me-1"></i> FINALIZAR
                        </button>
                    </td>
                </tr>
            `);
        });
    } catch (e) { 
        console.error("Error al cargar monitor:", e); 
        body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error de comunicación con el controlador</td></tr>');
    }
};

$(document).ready(() => {
    setTimeout(cargarMonitorViajes, 300);
});

window.finalizarViaje = async function(vehiculoId, folioRuta) {
    if (!confirm(`¿Confirmas la llegada de la unidad y entrega de todos los materiales?\nFolio: ${folioRuta}`)) return;

    try {
        const formData = new FormData();
        formData.append('vehiculo_id', vehiculoId);
        formData.append('viaje_folio', folioRuta);

        const resp = await fetch(`/myvet/app/controllers/repartosController.php?action=finalizar_viaje`, {
            method: 'POST',
            body: formData
        });

        const res = await resp.json();
        
        if (res.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¡Viaje Finalizado!',
                    text: res.message,
                    icon: 'success',
                    confirmButtonColor: '#007aff'
                });
            } else {
                alert(res.message);
            }
            cargarMonitorViajes(); 
        } else {
            throw new Error(res.message || "Error desconocido");
        }
    } catch (e) {
        alert('Error: ' + e.message);
    }
};
</script>