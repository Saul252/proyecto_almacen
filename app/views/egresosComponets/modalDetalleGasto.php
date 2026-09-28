<div id="gastoDetalle_seccionImpresion">
    <div class="modal fade" id="gastoDetalle_modalPrincipal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl"> 
            <div class="modal-content  shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-body p-0" id="gastoDetalle_contenedorContenido"></div>
                <div class="modal-footer  bg-light justify-content-center">
                    <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill" onclick="gastoDetalle_ejecutarImpresion()">
                        <i class="bi bi-printer me-2"></i>IMPRIMIR GASTO 
                    </button> <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill" onclick="gastoDetalle_ejecutarImpresionIVA()">
                        <i class="bi bi-printer me-2"></i>IMPRIMIR GASTO CON IVA 
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos replicados del formato Premium */
.gasto-invoice-box {
    max-width: 950px;
    margin: auto;
    padding: 20px;
    background: #fff;
    font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
    color: #334155;
    box-sizing: border-box;
}
.gasto-table-layout {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
.gasto-logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
}
.gasto-brand-title {
    font-size: 16pt;
    font-weight: 800;
    color: #1e3a8a;
    line-height: 1.1;
    letter-spacing: -0.5px;
}
.gasto-company-address {
    font-size: 8.5pt;
    color: #64748b;
    line-height: 1.4;
    text-align: center;
}
.gasto-remision-badge {
    background: #0f172a;
    color: #fff;
    padding: 6px 12px;
    text-align: center;
    font-size: 8pt;
    font-weight: 700;
    letter-spacing: 1px;
    border-radius: 4px;
    float: right;
    min-width: 140px;
}
.gasto-remision-badge span {
    display: block;
    font-size: 13pt;
    font-weight: 800;
    color: #38bdf8;
    margin-top: 2px;
}
.gasto-date-tile {
    width: 140px;
    float: right;
    margin-top: 5px;
    border-collapse: collapse;
    font-size: 8.5pt;
}
.gasto-date-tile td {
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
}
.gasto-date-tile .title-td {
    background: #f1f5f9;
    color: #64748b;
    font-weight: 600;
}
.gasto-card-info {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 12px;
    min-height: 90px;
    background-color: #ffffff;
}
.gasto-card-title {
    font-size: 7.5pt;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 3px;
    text-transform: uppercase;
}
.gasto-items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 9pt;
}
.gasto-items-table th {
    background: #0f172a;
    color: #fff;
    font-weight: 600;
    font-size: 8pt;
    text-transform: uppercase;
    padding: 7px 10px;
    letter-spacing: 0.5px;
}
.gasto-items-table td {
    padding: 8px 10px;
    border-bottom: 1px solid #e2e8f0;
}
.gasto-items-table tr:nth-child(even) {
    background-color: #f8fafc;
}
.gasto-items-table .total-row td {
    border-bottom: none;
    padding-top: 12px;
}
.gasto-total-highlight {
    font-size: 13pt;
    font-weight: 800;
    color: #1e3a8a;
    background: #eff6ff;
    padding: 6px 12px !important;
    border-radius: 4px;
    border: 1px solid #bfdbfe !important;
}
.gasto-card-obs {
    margin-top: 15px;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 8pt;
    background: #fdfdfd;
    line-height: 1.5;
}

/* ==========================================================================
   IMPRESIÓN EXCLUSIVA DEL CONTENIDO INTERNO (DESMONTA EL MARCO DEL MODAL)
   ========================================================================== */
@media print {
    @page {
        size: auto;
        margin: 5mm; /* Margen básico de seguridad */
    }

    /* Ocultar el resto del sitio */
    body * {
        visibility: hidden !important;
    }

    /* Ocultar elementos de UI del Modal */
    .modal-backdrop,
    .modal-header,
    .modal-footer,
    .btn,
    .btn-close {
        display: none !important;
    }

    /* Desarmar la estructura visual del modal Bootstrap */
    #gastoDetalle_modalPrincipal,
    .modal-dialog,
    .modal-content,
    .modal-body {
        position: static !important;
        display: block !important;
        visibility: visible !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow: visible !important;
    }

    /* Hacer visible ÚNICAMENTE el contenedor interno del ticket */
    #gastoDetalle_areaCapturaPDF,
    #gastoDetalle_areaCapturaPDF * {
        visibility: visible !important;
    }

    /* Posicionar el ticket en la esquina superior izquierda de la página */
    #gastoDetalle_areaCapturaPDF {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 10px !important;
        background: #ffffff !important;
    }

    /* Asegurar renderizado correcto de tablas */
    .gasto-table-layout, 
    .gasto-items-table,
    .gasto-date-tile {
        display: table !important;
        width: 100% !important;
    }

    tr { page-break-inside: avoid !important; }
}
</style>

<script>
    let observaciones='';
    let filasHtmlGastoIVA = '';
function gastoDetalle_cargarVista(gastoTipo, gastoId) {
    if (gastoTipo === 'compra') return;

    $.get(`/myvet/app/controllers/egresosController.php?action=obtenerDetalleMovimiento&tipo=${gastoTipo}&id=${gastoId}`, function(responseDetalle) {
        if (!responseDetalle.success) return Swal.fire('Error', responseDetalle.message, 'error');

        const cabeceraGasto = responseDetalle.cabecera;
        let filasHtmlGasto = '';

        responseDetalle.items.forEach(itemGasto => {
            filasHtmlGasto += `
                <tr>
                    <td style="font-family: monospace; color: #64748b; font-size: 9pt;">${itemGasto.sku || 'N/A'}</td>
                    <td class="fw-bold" style="color: #475569; text-transform: uppercase;">UNIDAD</td>
                    <td class="fw-bold" style="color: #0f172a;">${itemGasto.descripcion}</td>
                    <td class="text-end fw-bold" style="color: #0f172a;">${parseFloat(itemGasto.cantidad || 0).toFixed(4)}</td>
                    <td class="text-end" style="color: #475569;">$${parseFloat(itemGasto.precio_unitario).toFixed(2)}</td>
                    <td class="text-end fw-bold" style="color: #1e3a8a;">$${parseFloat(itemGasto.subtotal).toFixed(2)}</td>
                </tr>`;
                 filasHtmlGastoIVA += `
                <tr>
                    <td style="font-family: monospace; color: #64748b; font-size: 9pt;">${itemGasto.sku || 'N/A'}</td>
                    <td class="fw-bold" style="color: #475569; text-transform: uppercase;">UNIDAD</td>
                    <td class="fw-bold" style="color: #0f172a;">${itemGasto.descripcion}</td>
                    <td class="text-end fw-bold" style="color: #0f172a;">${parseFloat(itemGasto.cantidad || 0).toFixed(4)}</td>
                    <td class="text-end" style="color: #475569;">$${parseFloat((itemGasto.precio_unitario)*(.84)).toFixed(2)}</td>
                     <td class="text-end" style="color: #475569;">$${parseFloat((itemGasto.precio_unitario)*(.16)).toFixed(2)}</td>
                    <td class="text-end fw-bold" style="color: #1e3a8a;">$${parseFloat(itemGasto.subtotal).toFixed(2)}</td>
                </tr>`;
        });

        let htmlCategoriaGasto = '';
        if (cabeceraGasto.categoria_nombre) {
            htmlCategoriaGasto = `
                <br><strong>Categoría:</strong> <span style="color:#0284c7; font-weight:600;">${cabeceraGasto.categoria_nombre.toUpperCase()}</span>`;
        }
        observaciones=`<div id="datos" class="gasto-card-obs">
        <strong>Observaciones:</strong>
         ${cabeceraGasto.observaciones ? `
                        <div style="margin-top: 3px; border-top: 1px solid #e2e8f0; padding-top: 2px;">
                             <span style="color:#1e293b;">${cabeceraGasto.observaciones}</span>
                        </div>
                    ` : ' Sin observaciones registradas.'}
                    <div style="font-weight: 700; color: #334155; margin-bottom: 2px; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.3px;">Validación de Operación</div>
                    <strong>Método de Pago:</strong> ${cabeceraGasto.metodo_pago || 'N/A'} &nbsp;|&nbsp; 
                    <strong>Control Interno:</strong> Sistema Egresos Premium<br>
                    
                   
                </div>`;

        const estructuraTicketHTML = `
            <div class="gasto-invoice-box" id="gastoDetalle_areaCapturaPDF">
                <table class="gasto-table-layout">
                    <tr>
                        <td style="width: 32%;">
                            <div class="gasto-logo-container">
                                <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" style="width: 38px; height: auto;" alt="Logo">
                                <div class="gasto-brand-title"><?= $_SESSION['nombre_almacen'] ?> <br><span style="font-size:12pt; font-weight:600; color:#0284c7;">CENTRO</span></div>
                            </div>
                        </td>
                        
                        <td style="width: 38%;" class="gasto-company-address">
                            <span style="font-weight: 600; color: #1e293b;">${cabeceraGasto.almacen_nombre || 'ALMACÉN GENERAL'}</span><br>
                            Control de Egresos Interno<br>
                            <span style="font-size: 7.5pt; color: #94a3b8;">Gestión de Inventarios y Egresos</span>
                        </td>
                        
                        <td style="width: 30%;">
                            <div class="gasto-remision-badge">
                                N° ${gastoTipo.toUpperCase()}
                                <span>${cabeceraGasto.folio}</span>
                            </div>
                            <div style="clear: both;"></div>
                            <table class="gasto-date-tile">
                                <tr>
                                    <td class="title-td">Fecha</td>
                                    <td class="fw-bold" style="color: #334155;">${cabeceraGasto.fecha_registro || cabeceraGasto.fecha_gasto}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table class="gasto-table-layout" style="margin-top: 4px;">
                    <tr>
                        <td style="width: 70%; padding-right: 6px;">
                            <div class="gasto-card-info">
                                <div class="gasto-card-title">BENEFICIARIO DE LA OPERACIÓN</div>
                                <table style="width:100%; border-collapse:collapse; font-size: 8.5pt;">
                                   <tr>
                                        <td style="color:#64748b; width: 18%;"><strong>Nombre:</strong></td>
                                        <td class="fw-bold" style="color:#1e3a8a; font-size:9.5pt;">${cabeceraGasto.beneficiario || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#64748b;"><strong>Usuario:</strong></td>
                                        <td style="color:#475569; font-size:8pt;">${cabeceraGasto.usuario_nombre}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        
                        <td style="width: 30%;">
                            <div class="gasto-card-info" style="background-color: #f8fafc;">
                                <div class="gasto-card-title" style="color:#0284c7;">Información</div>
                                <div style="line-height: 1.4; color:#64748b; font-size: 8.5pt;">
                                    <strong>Estado:</strong> <span class="badge ${cabeceraGasto.estado === 'confirmada' || cabeceraGasto.estado === 'pagado' ? 'bg-success' : 'bg-warning'}">${cabeceraGasto.estado.toUpperCase()}</span>
                                    ${htmlCategoriaGasto}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="gasto-items-table">
                    <thead>
                        <tr>
                            <th style="width: 12%;">CÓDIGO</th>
                            <th style="width: 15%;">UNIDAD</th>
                            <th style="width: 43%;">DESCRIPCIÓN DEL GASTO</th>
                            <th class="text-right" style="width: 10%;">CANTIDAD</th>
                            <th class="text-right" style="width: 10%;">PRECIO U.</th>
                            <th class="text-right" style="width: 10%;">IMPORTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasHtmlGasto}
                        <tr class="total-row">
                            <td colspan="4"></td>
                            <td class="text-right" style="color: #475569; font-size: 10pt; font-weight: 600; vertical-align: middle;">TOTAL MXN</td>
                            <td class="text-right gasto-total-highlight">$${parseFloat(cabeceraGasto.total).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                        </tr>
                    </tbody>
                </table>

                ${observaciones}
            </div>`;

        $('#gastoDetalle_contenedorContenido').html(estructuraTicketHTML);
        const refModalGasto = document.getElementById('gastoDetalle_modalPrincipal');
        const instanciaModalGasto = bootstrap.Modal.getOrCreateInstance(refModalGasto);
        instanciaModalGasto.show();
    });
}
function gastoDetalle_ejecutarImpresion() {
    // 1. Extraer datos actuales dinámicamente del modal o respuesta
    const folio = document.querySelector('.gasto-remision-badge span')?.innerText || '000';
    const fecha = document.querySelector('.gasto-date-tile td.fw-bold')?.innerText || new Date().toLocaleDateString('es-MX');
    const beneficiario = document.querySelector('.gasto-card-info td.fw-bold')?.innerText || 'N/A';
    const usuario = document.querySelector('.gasto-card-info td[style*="color:#475569"]')?.innerText || 'N/A';
    const totalMxn = document.querySelector('.gasto-total-highlight')?.innerText || '$0.00';
   
    
    // Extraer filas de la tabla de productos/servicios
    const filasTabla = document.querySelector('.gasto-items-table tbody')?.innerHTML || '';
    const observaciones2=observaciones;
    
    // Detectar si el dispositivo es móvil para usar el visor nativo o PDF
    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (esMovil) {
        const elemento = document.getElementById('gastoDetalle_areaCapturaPDF');
        const opciones = {
            margin:       [8, 8, 8, 8],
            filename:     `Comprobante_Gasto_${folio}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
            jsPDF:        { unit: 'mm', format: 'a5', orientation: 'landscape' }
        };
        html2pdf().set(opciones).from(elemento).save();
        return;
    }

    // 2. Abrir ventana emergente dedicada para impresión limpia
    const ventana = window.open('', '_blank', 'height=700,width=900');

    ventana.document.write(`
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
            <title>COMPROBANTE DE GASTO - ${folio}</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @page { 
                    margin: 0; /* Elimina encabezados/pies nativos del navegador */
                }
                body { 
                    font-family: 'Inter', system-ui, -apple-system, sans-serif;
                    padding: 1.2cm;
                    background-color: #ffffff;
                    color: #1e293b;
                }
                .table-bordered th, .table-bordered td { 
                    border: 1px solid #cbd5e1 !important; 
                }
                .marca-agua {
                    position: fixed;
                    top: 35%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 240px;
                    opacity: 0.06;
                    z-index: -1;
                    pointer-events: none;
                }
                .firma-linea {
                    border-top: 1px solid #000;
                    margin-top: 50px;
                    text-align: center;
                    padding-top: 5px;
                    font-size: 11px;
                }
            </style>
        </head>
        <body>
            <!-- Marca de agua -->
            <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" class="marca-agua" alt="Watermark">

            <div id="areaImpresion" class="text-uppercase bg-white" style="font-size: 0.9rem;">

                <!-- ENCABEZADO -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" alt="Logo" width="50" height="50" class="me-3">
                        <div>
                            <h3 class="fw-bold text-uppercase mb-0" style="color:#0f172a; letter-spacing:1px;">
                                COMPROBANTE DE GASTO
                            </h3>
                            <div class="text-body-secondary small mt-1 text-uppercase">
                                Folio: <span class="fw-bold text-dark">${folio}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-end text-uppercase">
                        <div class="fw-bold fs-5" style="color:#1e3a8a;">
                            <?= $_SESSION['nombre_almacen'] ?> 
                        </div>
                        <div class="text-body-secondary small">
                            Fecha: ${fecha}
                        </div>
                    </div>
                </div>

                <!-- DATOS DEL BENEFICIARIO Y USUARIO -->
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">BENEFICIARIO DE LA OPERACIÓN</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${beneficiario}</div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">REGISTRADO POR</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${usuario}</div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE DETALLES -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle text-uppercase" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 15%;">CÓDIGO</th>
                                <th style="width: 15%;">UNIDAD</th>
                                <th>DESCRIPCIÓN</th>
                                <th class="text-end" style="width: 12%;">CANT.</th>
                                <th class="text-end" style="width: 15%;">P. UNITARIO</th>
                               
                                <th class="text-end" style="width: 15%;">IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filasTabla}
                        </tbody>
                    </table>
                </div>
${observaciones2}
                <!-- VALIDEZ Y FIRMAS -->
                <div class="row mt-4 pt-3">
                    <div class="col-6">
                        <div class="firma-linea">
                            FIRMA / CONFORMIDAD DE ENTREGA
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="firma-linea">
                            AUTORIZADO POR CONTROL INTERNO
                        </div>
                    </div>
                </div>

            </div>

            <script>
                // Disparar la impresión nativa cuando todo cargue
                window.onload = function() {
                    window.print();
                    // Cerrar la ventana secundaria tras imprimir o cancelar
                    window.onafterprint = function() {
                        window.close();
                    };
                };
            <\/script>
        </body>
    </html>
    `);

    ventana.document.close();
} 

function gastoDetalle_ejecutarImpresionIVA() {
    // 1. Extraer datos actuales dinámicamente del modal o respuesta
    const folio = document.querySelector('.gasto-remision-badge span')?.innerText || '000';
    const fecha = document.querySelector('.gasto-date-tile td.fw-bold')?.innerText || new Date().toLocaleDateString('es-MX');
    const beneficiario = document.querySelector('.gasto-card-info td.fw-bold')?.innerText || 'N/A';
    const usuario = document.querySelector('.gasto-card-info td[style*="color:#475569"]')?.innerText || 'N/A';
    const totalMxn = document.querySelector('.gasto-total-highlight')?.innerText || '$0.00';
   
    
    // Extraer filas de la tabla de productos/servicios
    const filasTabla = document.querySelector('.gasto-items-table tbody')?.innerHTML || '';
    const observaciones2=observaciones;
    
    // Detectar si el dispositivo es móvil para usar el visor nativo o PDF
    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (esMovil) {
        const elemento = document.getElementById('gastoDetalle_areaCapturaPDF');
        const opciones = {
            margin:       [8, 8, 8, 8],
            filename:     `Comprobante_Gasto_${folio}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
            jsPDF:        { unit: 'mm', format: 'a5', orientation: 'landscape' }
        };
        html2pdf().set(opciones).from(elemento).save();
        return;
    }

    // 2. Abrir ventana emergente dedicada para impresión limpia
    const ventana = window.open('', '_blank', 'height=700,width=900');

    ventana.document.write(`
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
            <title>COMPROBANTE DE GASTO - ${folio}</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @page { 
                    margin: 0; /* Elimina encabezados/pies nativos del navegador */
                }
                body { 
                    font-family: 'Inter', system-ui, -apple-system, sans-serif;
                    padding: 1.2cm;
                    background-color: #ffffff;
                    color: #1e293b;
                }
                .table-bordered th, .table-bordered td { 
                    border: 1px solid #cbd5e1 !important; 
                }
                .marca-agua {
                    position: fixed;
                    top: 35%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 240px;
                    opacity: 0.06;
                    z-index: -1;
                    pointer-events: none;
                }
                .firma-linea {
                    border-top: 1px solid #000;
                    margin-top: 50px;
                    text-align: center;
                    padding-top: 5px;
                    font-size: 11px;
                }
            </style>
        </head>
        <body>
            <!-- Marca de agua -->
            <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" class="marca-agua" alt="Watermark">

            <div id="areaImpresion" class="text-uppercase bg-white" style="font-size: 0.9rem;">

                <!-- ENCABEZADO -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" alt="Logo" width="50" height="50" class="me-3">
                        <div>
                            <h3 class="fw-bold text-uppercase mb-0" style="color:#0f172a; letter-spacing:1px;">
                                COMPROBANTE DE GASTO
                            </h3>
                            <div class="text-body-secondary small mt-1 text-uppercase">
                                Folio: <span class="fw-bold text-dark">${folio}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-end text-uppercase">
                        <div class="fw-bold fs-5" style="color:#1e3a8a;">
                            <?= $_SESSION['nombre_almacen'] ?> 
                        </div>
                        <div class="text-body-secondary small">
                            Fecha: ${fecha}
                        </div>
                    </div>
                </div>

                <!-- DATOS DEL BENEFICIARIO Y USUARIO -->
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">BENEFICIARIO DE LA OPERACIÓN</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${beneficiario}</div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">REGISTRADO POR</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${usuario}</div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE DETALLES -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle text-uppercase" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 15%;">CÓDIGO</th>
                                <th style="width: 15%;">UNIDAD</th>
                                <th>DESCRIPCIÓN</th>
                                <th class="text-end" style="width: 12%;">CANT.</th>
                                <th class="text-end" style="width: 15%;">P. UNITARIO</th>
                                <th class="text-end" style="width: 15%;">IVA</th>
                                <th class="text-end" style="width: 15%;">IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filasHtmlGastoIVA}
                        </tbody>
                    </table>
                </div>
${observaciones2}
                <!-- VALIDEZ Y FIRMAS -->
                <div class="row mt-4 pt-3">
                    <div class="col-6">
                        <div class="firma-linea">
                            FIRMA / CONFORMIDAD DE ENTREGA
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="firma-linea">
                            AUTORIZADO POR CONTROL INTERNO
                        </div>
                    </div>
                </div>

            </div>

            <script>
                // Disparar la impresión nativa cuando todo cargue
                window.onload = function() {
                    window.print();
                    // Cerrar la ventana secundaria tras imprimir o cancelar
                    window.onafterprint = function() {
                        window.close();
                    };
                };
            <\/script>
        </body>
    </html>
    `);

    ventana.document.close();
} 
</script>