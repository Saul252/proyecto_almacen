<div class="modal fade" id="modalImprimirRuta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-body-tertiary align-items-center py-3">
               <h6 class="modal-title fw-bold">
                    <i class="bi bi-receipt text-primary"></i>
                    Ruta de Reparto: <span id="folioRutaPrint" class="text-primary"></span>
                </h6>

                <div class="d-flex gap-2 ms-auto me-2">
                    <button class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-1" onclick="imprimirModalRuta()">
                        <span>🖨</span> Imprimir
                    </button>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0" id="contenidoRutaPrint">
                <!-- AQUÍ SE RENDERIZA TODO EL CONTENIDO DINÁMICO -->
            </div>

        </div>
    </div>
</div>

<script>
async function imprimirRuta(entrega_ida, folioViaje) {

    document.getElementById('folioRutaPrint').textContent = entrega_ida;

    const respuesta = await fetch(
        `/myvet/app/controllers/repartosController.php?action=get_ruta_entrega_por_despacho&entrega_id=${entrega_ida}&id=${encodeURIComponent(folioViaje)}`
    );

    const data = await respuesta.json();

    console.log(data);

    if (!data.success) return;

    const cont = document.getElementById('contenidoRutaPrint');
    const datos = data.data;

    // =========================================
    // DETECTAR PREFIJO 'EN_PAT' DENTRO DEL FOLIO
    // =========================================
    const stringEntrega = String(entrega_ida || '').toUpperCase();
    const stringFolio = String(datos[0]?.folio_viaje || '').toUpperCase();
    
    // Evalúa si alguno de los folios contiene el prefijo EN_PAT (ej: EN_PAT26082, EN_PAT1, etc.)
    const esEntregaPatio = stringEntrega.includes('EN_PAT') || stringFolio.includes('EN_PAT');

    // =========================================
    // AGRUPAR PRODUCTOS
    // =========================================
    const productosAgrupados = {};

    datos.forEach(item => {
        const key = item.nombreProducto;
        if (!productosAgrupados[key]) {
            productosAgrupados[key] = {
                nombreProducto: item.nombreProducto,
                totalCantidad: 0,
                unidadMedida: item.unidadMedida,
                unidadReporte: item.unidadReporte,
                factor: item.factor
            };
        }
        productosAgrupados[key].totalCantidad += parseFloat(item.totalCantidad || 0);
    });

    // =========================================
    // GENERAR FILAS
    // =========================================
    let filas = '';

    datos.forEach((prod, i) => {
        const total = prod.totalCantidad / prod.factor;
        const totalCantidad = prod.totalCantidad;
        const unidad = total >= 1 ? prod.unidadReporte : (totalCantidad/(1/prod.equi))>=1?prod.nombreEqui:prod.unidadMedida;
        let cantidad=totalCantidad/(1/prod.equi);
        let badgeColor = 'bg-warning text-dark';
        if (prod.estatus_logistico === 'completado') badgeColor = 'bg-success text-white';
        if (prod.estatus_logistico === 'en_transito') badgeColor = 'bg-primary text-white';

        filas += `
            <tr>
                <td class="text-body-secondary fw-semibold">${i + 1}</td>
                <td style="max-width:350px;" class="fw-medium text-body">${prod.nombreProducto}</td>
                <td style="max-width:250px;" class="fw-bold text-primary">
                    ${parseFloat(cantidad).toFixed(2)} <span class="text-body-secondary fw-normal small">${unidad}</span>
                </td>
                <td style="max-width:250px;" class="text-body-secondary small">${prod.direccion_entrega ?? '-'}</td>
                <td class="text-center">
                    <span class="badge ${badgeColor} text-uppercase font-monospace">${prod.estatus_logistico}</span>
                </td>
            </tr>
        `;
    });

    // =========================================
    // SECCIONES ADAPTATIVAS SEGÚN EL TIPO
    // =========================================
    const tituloDoc = esEntregaPatio ? '🏬 Venta ' + datos[0].folio_venta + ': Vales / Entrega en Patio' : '🚚 Venta ' + datos[0].folio_venta + ': Hoja de Ruta';

    const bloqueOperador = esEntregaPatio ? `
        <div class="info-card">
            <div class="info-card-body">
                <span class="info-label">Punto de Entrega</span>
                <div class="info-value-main">ENTREGA EN PATIO / MOSTRADOR</div>
                <div class="info-meta">
                    <span class="meta-title">Entrego :</span>
                    <span class="meta-value">${datos[0].nombre_chofer ?? '-'}</span>
                </div>
            </div>
        </div>
    ` : `
        <div class="info-card">
            <div class="info-card-body info-split">
                <div class="info-col">
                    <span class="info-label">Operador / Chofer</span>
                    <div class="info-value">${datos[0].nombre_chofer ?? '-'}</div>
                    <div class="info-meta">
                        <span class="meta-tag">Asignado de ruta</span>
                    </div>
                </div>
                <div class="info-divider-v"></div>
                <div class="info-col">
                    <span class="info-label">Unidad de Transporte</span>
                    <div class="info-value">${datos[0].unidad_nombre ?? '-'}</div>
                    <div class="info-meta">
                        <span class="meta-title">Placas</span>
                        <span class="meta-badge">${datos[0].unidad_placas ?? '-'}</span>
                    </div>
                </div>
            </div>
        </div>
    `;

    const fechasHeader = esEntregaPatio ? `
        <div class="small text-body-secondary mb-1">Fecha Despacho:____________________</div>
        <div class="small text-body-secondary mb-1">Hora Entrega:____________________</div>
    ` : `
        <div class="small text-body-secondary mb-1">Fecha de Salida:____________________</div>
        <div class="small text-body-secondary mb-1">Fecha de llegada:____________________</div>
    `;

    const firmasSeccion = esEntregaPatio ? `
        <div class="row g-5">
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre">Firma Despachador / Patio</div>
                    <div class="text-body-secondary small">Entrega Material</div>
                </div>
            </div>
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre">Firma Cliente / Recibe</div>
                    <div class="text-body-secondary small">Sello y Firma de conformidad</div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box">
                    <div class="info-sub mt-1">Observaciones / Check Patio:</div>
                </div>
            </div>
        </div>
    ` : `
        <div class="row g-5">
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre">Firma Chofer / Transportista</div>
                    <div class="text-body-secondary small">Nombre y Fecha</div>
                </div>
            </div>
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre">Firma Cliente / Recibe</div>
                    <div class="text-body-secondary small">Sello y Firma de conformidad</div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box">
                    <div class="info-sub mt-1">Observaciones y Comentarios:</div>
                </div>
            </div>
        </div>
    `;

    // =========================================
    // HTML GENERADO CON VARIABLES DE TEMA
    // =========================================
    let html = `
        <div class="hoja-ruta-container p-4">

            <!-- HEADER INTERNO -->
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h4 class="fw-bold text-body m-0 d-flex align-items-center gap-2">
                        ${tituloDoc}
                    </h4>

                    <div class="text-body-secondary small mt-1">
                        Folio de viaje / Folio despacho: <span class="fw-bold text-body font-monospace">${datos[0].folio_viaje}</span>
                    </div>
                    <div class="text-body-secondary small mt-1">
                        Registro: <span class="fw-bold text-body font-monospace">${datos[0].fecha_viaje ?? '-'}</span>
                    </div>
                </div>

                <div class="text-end">
                    ${fechasHeader}
                </div>
            </div>

            <style>
                * {
                    text-transform: uppercase !important;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 12px;
                    width: 100%;
                }

                .info-box {
                    border: 1px solid var(--bs-border-color);
                    border-radius: 8px;
                    padding: 10px 12px;
                    background: var(--bs-tertiary-bg);
                    min-width: 0;
                }

                .info-title {
                    font-size: 10.5px;
                    color: var(--bs-secondary-color);
                    text-transform: uppercase;
                    letter-spacing: .5px;
                    margin-bottom: 4px;
                }

                .info-value {
                    font-size: 13px;
                    font-weight: 600;
                    line-height: 1.2;
                    color: var(--bs-body-color);
                    white-space: normal;
                    word-break: break-word;
                }

                .info-sub {
                    font-size: 11.5px;
                    color: var(--bs-secondary-color);
                }

                .firma-linea {
                    width: 75%;
                    margin: 35px auto 5px auto;
                    border-top: 1px solid var(--bs-border-color);
                }

                .firma-nombre {
                    font-size: 11px;
                    font-weight: 600;
                    color: var(--bs-body-color);
                }

                :root {
                    --card-bg: #ffffff;
                    --text-primary: #1e2022;
                    --text-secondary: #8c98a4;
                    --text-muted: #b0b7c0;
                    --border-color: rgba(0, 0, 0, 0.05);
                    --accent-bg: #f8fafc;
                }

                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                    gap: 1.5rem;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    -webkit-font-smoothing: antialiased;
                }

                .info-card {
                    background: var(--card-bg);
                    border-radius: 16px;
                    border: 1px solid var(--border-color);
                    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04), 
                                0 4px 12px -2px rgba(0, 0, 0, 0.02);
                    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                    backdrop-filter: blur(10px);
                }

                .info-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.07);
                }

                .info-card-body {
                    padding: 1.5rem 1.75rem;
                }

                .info-split {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }

                .info-col {
                    flex: 1;
                }

                .info-divider-v {
                    width: 1px;
                    height: 48px;
                    background: linear-gradient(
                        180deg, 
                        rgba(0,0,0,0) 0%, 
                        rgba(0,0,0,0.07) 50%, 
                        rgba(0,0,0,0) 100%
                    );
                    margin: 0 1.5rem;
                }

                .info-label {
                    display: block;
                    font-size: 0.6875rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                    color: var(--text-secondary);
                    margin-bottom: 0.35rem;
                }

                .info-value-main {
                    font-size: 1.25rem;
                    font-weight: 400;
                    color: var(--text-primary);
                    letter-spacing: -0.01em;
                    line-height: 1.2;
                }

                .info-value {
                    font-size: 1rem;
                    font-weight: 500;
                    color: var(--text-primary);
                    letter-spacing: -0.01em;
                    line-height: 1.2;
                }

                .info-meta {
                    margin-top: 0.6rem;
                    display: flex;
                    align-items: center;
                    gap: 0.4rem;
                    font-size: 0.8125rem;
                }

                .meta-title {
                    color: var(--text-muted);
                    font-weight: 400;
                }

                .meta-value {
                    color: var(--text-primary);
                    font-weight: 500;
                }

                .meta-tag {
                    color: var(--text-secondary);
                    font-size: 0.75rem;
                    font-weight: 400;
                    font-style: italic;
                }

                .meta-badge {
                    color: #475569;
                    background: var(--accent-bg);
                    border: 1px solid rgba(0, 0, 0, 0.04);
                    padding: 0.15rem 0.5rem;
                    border-radius: 6px;
                    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                    font-size: 0.75rem;
                    letter-spacing: 0.05em;
                    font-weight: 600;
                }
            </style>

            <!-- BLOQUES DE INFORMACIÓN PRINCIPAL -->
            <div class="info-grid">
                <!-- Tarjeta 1: Cliente -->
                <div class="info-card">
                    <div class="info-card-body">
                        <span class="info-label">Cliente Destino</span>
                        <div class="info-value-main">${datos[0].cliente ?? '-'}</div>
                        <div class="info-meta">
                            <span class="meta-title">Teléfono</span>
                            <span class="meta-value">${datos[0].tel_cliente ?? 'Sin teléfono'}</span>
                        </div>
                    </div>
                </div>
               
                <!-- Tarjeta 2: Dinámica (Chofer o Patio) -->
                ${bloqueOperador}
            </div>

            <!-- SECCIÓN DE DETALLES / TABLA -->
            <div class="table-responsive border rounded mb-4 mt-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 40%">Producto descripción</th>
                            <th style="width: 20%">Cantidad total</th>
                            <th style="width: 23%">Ubicación / Dirección</th>
                            <th style="width: 12%" class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filas}
                    </tbody>
                </table>
            </div>

            <!-- ÁREA DE FIRMAS FORMALIZADA -->
            <div class="firmas-container pt-4">
                ${firmasSeccion}
            </div>

        </div>
    `;

    cont.innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('modalImprimirRuta'));
    modal.show();
}

function imprimirModalRuta() {
    const contenido = document.getElementById('contenidoRutaPrint').innerHTML;
    const ventana = window.open('', '_blank', 'width=950,height=650');

    ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Hoja de Entrega / Ruta</title>
            <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
            <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    background: #f8f9fa;
                    color: #333;
                    padding: 15px;
                    font-size: 11.5px;
                }

                .ticket {
                    width: 100%;
                    max-width: 820px;
                    margin: auto;
                    background: #fff;
                    border: 1px solid #e0e0e0;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                    overflow: hidden;
                }

                .hoja-ruta-container {
                    padding: 20px !important;
                }

                .info-box {
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    padding: 10px 12px;
                    background: #f8fafc;
                    height: 100%;
                }

                .info-title {
                    font-size: 10px;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: .6px;
                    font-weight: 700;
                    margin-bottom: 3px;
                }

                .info-value {
                    font-size: 12.5px;
                    font-weight: 600;
                    color: #1e293b;
                    line-height: 1.2;
                }

                .info-sub {
                    font-size: 11px;
                    color: #64748b;
                }

                table thead th {
                    background-color: #f1f5f9 !important;
                    color: #475569 !important;
                    font-size: 10px;
                    text-transform: uppercase;
                    letter-spacing: .5px;
                    font-weight: 700;
                    padding: 8px 10px !important;
                    border-bottom: 2px solid #e2e8f0 !important;
                }

                table tbody td {
                    font-size: 11.5px;
                    padding: 2px 6px !important;
                    margin: 0 !important;
                    line-height: 1.1 !important;
                    vertical-align: middle !important;
                }

                .firmas-container {
                    page-break-inside: avoid;
                }

                .firma-box {
                    text-align: center;
                    padding: 5px;
                }

                .firma-linea {
                    width: 75%;
                    margin: 35px auto 5px auto;
                    border-top: 1px solid #94a3b8;
                }

                .firma-nombre {
                    font-size: 11px;
                    font-weight: 600;
                    color: #1e293b;
                }

                @media print {
                    .info-grid {
                        display: grid !important;
                        grid-template-columns: repeat(2, 1fr) !important;
                        gap: 10px !important;
                    }
                    body {
                        background: #ffffff !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        padding: 0 !important;
                    }
                    .ticket {
                        width: 100% !important;
                        max-width: 100% !important;
                        background: #ffffff !important;
                        border: 1px solid #e0e0e0 !important;
                        border-radius: 12px !important;
                        box-shadow: none !important;
                        margin: 0 auto !important;
                    }
                    .no-print {
                        display: none !important;
                    }
                    tr { 
                        page-break-inside: avoid !important; 
                    }
                    .info-box {
                        background: #f8fafc !important;
                        border: 1px solid #e2e8f0 !important;
                    }
                    table thead th {
                        background-color: #f1f5f9 !important;
                    }
                }

                table {
                    border-collapse: collapse !important;
                }

                table p {
                    margin: 0 !important;
                    padding: 0 !important;
                }

                table br {
                    display: none !important;
                }
            </style>
        </head>
        <body>
            <img src="/myvet/public/assets/logo.ico" style="position: fixed; top: 22.5%; left: 50%; transform: translate(-50%, -50%); width: 300px; opacity: 0.08; z-index: -1;">

            <div class="text-end mb-3 no-print" style="max-width: 850px; margin: auto;">
                <button class="btn btn-dark px-4 shadow-sm fw-semibold" onclick="window.print()">
                    🖨 Enviar a Impresora
                </button>
            </div>

            <div class="ticket">
                <div class="hoja-ruta-container">
                    ${contenido}
                </div>
            </div>

            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 300);
                };
            <\/script>
        </body>
        </html>
    `);

    ventana.document.close();
    ventana.focus();
}
</script>