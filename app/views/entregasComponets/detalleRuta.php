<style>
@media print {
    /* Reset completo para impresión */
    * {
        visibility: hidden;
        color: #000 !important;
        background: white !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        backdrop-filter: none !important;
        text-shadow: none !important;
    }
    
   
    /* Mostrar SOLO el contenido del modal */
    #modalDetalleViaje, #modalDetalleViaje * {
        visibility: visible;
    }

    /* Posicionar perfectamente */
    #modalDetalleViaje {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .modal-dialog {
        margin: 0 !important;
        max-width: none !important;
        width: 100% !important;
    }

    .modal-content {
        border: 2px solid #333 !important;
        box-shadow: none !important;
        background: white !important;
        padding: 20px !important;
        margin: 0 !important;
        max-height: none !important;
    }

    /* HEADER - Título principal */
    .modal-header {
        border-bottom: 3px solid #1a73e8 !important;
        background: #f8f9fa !important;
        padding-bottom: 15px !important;
        margin: -20px -20px 20px -20px !important;
        padding: 20px 20px 15px 20px !important;
    }

    .modal-title {
        font-size: 22pt !important;
        font-weight: bold !important;
        color: #1a73e8 !important;
        margin: 0 !important;
    }

    /* INFO CARDS - Más elegantes */
    .row.g-3 > div[class*="col"] {
        margin-bottom: 15px !important;
        padding: 0 !important;
    }

    .row.g-3 > div[class*="col"] > div {
        background: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        border-left: 4px solid #1a73e8 !important;
        padding: 15px !important;
        margin: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .row.g-3 small {
        font-size: 9pt !important;
        font-weight: bold !important;
        color: #495057 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    .row.g-3 span.fw-bold {
        font-size: 14pt !important;
        color: #000 !important;
    }

    .badge {
        background: #1a73e8 !important;
        color: white !important;
        font-size: 10pt !important;
        padding: 4px 8px !important;
    }

    /* TABLA - Súper profesional */
    .table-responsive {
        border: 1px solid #dee2e6 !important;
        margin: 0 !important;
        border-radius: 0 !important;
        background: white !important;
        box-shadow: none !important;
    }

    .table {
        margin: 0 !important;
        font-size: 11pt !important;
        border-collapse: collapse !important;
    }

    .table thead th {
        background: #1a73e8 !important;
        color: white !important;
        font-weight: bold !important;
        font-size: 10pt !important;
        padding: 12px 8px !important;
        border: none !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    .table tbody td {
        padding: 12px 8px !important;
        border-bottom: 1px solid #dee2e6 !important;
        vertical-align: middle !important;
    }

    .table tbody tr:nth-child(even) {
        background: #f8f9fa !important;
    }

    .table tbody tr:hover {
        background: #e3f2fd !important;
    }

    /* Estilos específicos de contenido */
    .text-dark, .fw-bold {
        color: #000 !important;
        font-weight: bold !important;
    }

    code {
        background: #e3f2fd !important;
        color: #1a73e8 !important;
        padding: 2px 6px !important;
        border-radius: 3px !important;
        font-size: 10pt !important;
    }

    small {
        font-size: 9pt !important;
        color: #6c757d !important;
    }

    /* Ocultar elementos no necesarios */
    .modal-footer,
    .btn-close,
    a[href*="google.com/maps"] {
        display: none !important;
    }

    /* Footer de página */
    @page {
        margin: 1.5cm;
        @bottom-center {
            content: "Hoja de Ruta - " counter(page) " / " counter(pages);
            font-size: 10pt;
            color: #666;
        }
    }
}
</style>
<div class="modal fade" id="modalDetalleViaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 20px;  overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white" style="">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-route me-2"></i>
                    Hoja de Ruta: <span id="txtFolioViaje" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body" style="background: #f6f8fa;">

                <!-- INFO CARDS -->
                <div class="row g-3 mb-4">

                    <!-- UNIDAD -->
                    <div class="col-md-3">
                        <div class="p-3 h-100 bg-white rounded-4 shadow-sm">
                            <small class="text-body-secondary text-uppercase fw-semibold" style="font-size: 0.65rem;">Unidad</small>
                            <div id="txtUnidad" class="fw-bold fs-6 text-dark mt-1"></div>
                            <span id="txtPlacas" class="badge bg-secondary-subtle text-dark mt-2"></span>
                        </div>
                    </div>

                    <!-- FECHAS -->
                    <div class="col-md-3">
                        <div class="p-3 h-100 bg-white rounded-4 shadow-sm">
                            <small class="text-body-secondary text-uppercase fw-semibold" style="font-size: 0.65rem;">Fechas</small>
                            
                            <div class="mt-2">
                                <small class="text-body-secondary">Inicio</small>
                                <div id="txtFInicio" class="fw-bold text-dark"></div>
                            </div>

                            <div class="mt-2">
                                <small class="text-body-secondary">Final</small>
                                <div id="txtFFinal" class="fw-bold text-success"></div>
                            </div>
                        </div>
                    </div>

                    <!-- EQUIPO -->
                    <div class="col-md-3">
                        <div class="p-3 h-100 bg-white rounded-4 shadow-sm">
                            <small class="text-body-secondary text-uppercase fw-semibold" style="font-size: 0.65rem;">Equipo</small>
                            <div class="mt-2">
                                <strong>Chofer:</strong>
                                <div id="txtChofer" class="fw-semibold"></div>
                            </div>
                            <small id="txtAyudantes" class="text-body-secondary d-block mt-1"></small>
                        </div>
                    </div>

                    <!-- ESTATUS -->
                    <div class="col-md-3">
                        <div class="p-3 h-100 bg-white rounded-4 shadow-sm">
                            <small class="text-body-secondary text-uppercase fw-semibold" style="font-size: 0.65rem;">Estatus</small>
                            <div id="txtEstatusLogistico" class="mt-2"></div>
                        </div>
                    </div>

                </div>

                <!-- TABLA -->
                <div class="table-responsive bg-white rounded-4 shadow-sm">
                    <table class="table align-middle mb-0">
                        <thead style="background: #f1f3f5;">
                            <tr class="text-body-secondary">
                                <th class="ps-3" style="font-size: 0.75rem;">#</th>
                                <th style="font-size: 0.75rem;">Cliente / Destino</th>
                                <th style="font-size: 0.75rem;">Ticket</th>
                                <th style="font-size: 0.75rem;">Producto</th>
                                <th class="text-center" style="font-size: 0.75rem;">Cantidad</th>
                                <th class="text-center pe-3" style="font-size: 0.75rem;">Mapa</th>
                            </tr>
                        </thead>
                        <tbody id="bodyDetalleViaje" style="font-size: 0.85rem;">
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-white" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-dark rounded-3 px-4" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimir
                </button>
            </div>

        </div>
    </div>
</div>
<script>
function verDetalleViaje(folio) {
    console.log("1. Folio enviado hola:", folio);
    if (!folio) return;

    fetch('/myvet/app/controllers/repartosController.php?action=get_detalle_ruta_completa&id=' + folio)
        .then(res => res.json()) // PASO 1: Convertir a JSON
        .then(response => {      // PASO 2: Trabajar con los datos
            console.log("2. Respuesta JSON completa:", response);

            if (!response.success || !response.data || response.data.length === 0) {
                // Aquí verás el error de SQL si PHP lo manda
                console.error("Error reportado por PHP:", response.message);
                return;
            }

            const registros = response.data;
            const v = registros[0];
const inicio = document.getElementById('txtFInicio');
const final = document.getElementById('txtFFinal');

if (inicio) inicio.innerText = v.fecha_viaje || 'N/A';
if (final) final.innerText = v.fecha_llegada || 'En ruta';
            // Llenar cabecera
            document.getElementById('txtFolioViaje').innerText = v.folio_viaje || 'S/N';
            document.getElementById('txtUnidad').innerText = v.unidad_nombre || 'N/A';
           
            document.getElementById('txtPlacas').innerText = v.unidad_placas || '';
            document.getElementById('txtChofer').innerText = v.nombre_chofer || 'No asignado';
            document.getElementById('txtAyudantes').innerText = v.ayudantes ? `Ayudantes: ${v.ayudantes}` : 'Sin ayudantes';

            // Badge de estatus
            const estatus = (v.estatus_logistico || 'pendiente').toLowerCase();
            const badgeColor = estatus === 'completado' ? 'success' : (estatus === 'en_transito' ? 'primary' : 'warning');
            document.getElementById('txtEstatusLogistico').innerHTML = `<span class="badge rounded-pill bg-${badgeColor}">${estatus.toUpperCase()}</span>`;

            // Llenar tabla
            let html = '';
           let contador = 1;

registros.forEach(item => {
    let cantidad=item.cantidad/item.fcr;

    let cantidadUnidad=cantidad>=1? cantidad +' '+ item.urr:item.cantidad+' '+ item.um;

    const mapsUrl = (item.latitud && item.longitud) 
        ? `<a href="https://www.google.com/maps?q=${item.latitud},${item.longitud}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 10px;"><i class="fas fa-map-marker-alt"></i></a>`
        : `<span class="text-body-secondary small">N/A</span>`;

    html += `
    <tr>
        <td class="ps-3 text-center">
            <span class="badge bg-dark rounded-circle">${contador}</span>
        </td>
        <td>
            <strong class="text-dark">${item.cliente}</strong>
            <small class="d-block text-secondary" style="font-size: 0.7rem;">${item.direccion_entrega}</small>
        </td>
        <td><code class="text-primary">${item.folio_venta}</code></td>
        <td>${item.producto_nombre} <br> <small class="text-body-secondary">${item.um || ''}</small></td>
        <td class="text-center fw-bold">${cantidadUnidad}</td>
        <td class="text-center">${mapsUrl}</td>
    </tr>`;

    contador++;
});
            document.getElementById('bodyDetalleViaje').innerHTML = html;

            // Abrir modal
            const modalEl = document.getElementById('modalDetalleViaje');
            modalEl.removeAttribute('aria-hidden'); 
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        })
        .catch(err => {
            console.error("3. Error fatal en fetch o proceso de datos:", err);
        });
}
function imprimirHojaRuta() {
    // Pequeño truco para asegurar que el scroll no afecte la captura
    window.scrollTo(0,0);
    window.print();
}
</script>