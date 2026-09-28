<div id="compraDetalle_seccionImpresion">
<div class="modal fade" id="compraDetalle_modalPrincipal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl"> 
        <div class="modal-content  shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-body p-0" id="compraDetalle_contenedorContenido"></div>
            <div class="modal-footer   justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill" onclick="compraDetalle_ejecutarImpresion()">
                    <i class="bi bi-printer me-2"></i>IMPRIMIR COMPRA
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
</div>

<style>
/* Estilos formato Premium para compras */
.compra-invoice-box {
    max-width: 950px;
    margin: auto;
    padding: 20px;
    background: #fff;
    font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
    color: #334155;
}
.compra-table-layout {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
.compra-logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
}
.compra-brand-title {
    font-size: 16pt;
    font-weight: 800;
    color: #1e3a8a;
    line-height: 1.1;
    letter-spacing: -0.5px;
}
.compra-company-address {
    font-size: 8.5pt;
    color: #64748b;
    line-height: 1.4;
    text-align: center;
}
.compra-remision-badge {
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
.compra-remision-badge span {
    display: block;
    font-size: 13pt;
    font-weight: 800;
    color: #38bdf8;
    margin-top: 2px;
}
.compra-date-tile {
    width: 140px;
    float: right;
    margin-top: 5px;
    border-collapse: collapse;
    font-size: 8.5pt;
}
.compra-date-tile td {
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
}
.compra-date-tile .title-td {
    background: #f1f5f9;
    color: #64748b;
    font-weight: 600;
}
.compra-card-info {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 12px;
    min-height: 90px;
    background-color: #ffffff;
}
.compra-card-title {
    font-size: 7.5pt;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 3px;
    text-transform: uppercase;
}
.compra-items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 8.5pt;
}
.compra-items-table th {
    background: #0f172a;
    color: #fff;
    font-weight: 600;
    font-size: 7.5pt;
    text-transform: uppercase;
    padding: 7px 8px;
    letter-spacing: 0.5px;
}
.compra-items-table td {
    padding: 8px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: top;
}
.compra-items-table tr:nth-child(even) {
    background-color: #f8fafc;
}
.compra-items-table .total-row td {
    border-bottom: none;
    padding-top: 12px;
}
.compra-total-highlight {
    font-size: 13pt;
    font-weight: 800;
    color: #1e3a8a;
    background: #eff6ff;
    padding: 6px 12px !important;
    border-radius: 4px;
    border: 1px solid #bfdbfe !important;
}
.compra-card-obs {
    margin-top: 15px;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 8pt;
    background: #fdfdfd;
    line-height: 1.5;
}
.compra-signatures {
    margin-top: 30px;
    padding-top: 10px;
    text-align: center;
}
.compra-signature-line {
    border-top: 2px solid #334155;
    margin: 0 15px;
    padding-top: 5px;
    font-size: 7.5pt;
    font-weight: 700;
    color: #334155;
}

/* Impresión por CSS */
@media print {
    body * {
        visibility: hidden;
    }
    #compraDetalle_seccionImpresion, 
    #compraDetalle_seccionImpresion * {
        visibility: visible !important;
    }
    #compraDetalle_seccionImpresion {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .modal-backdrop, .modal-footer {
        display: none !important;
    }
}
</style>

<script>
function verDetalle(tipo, id) {
    if (tipo !== 'compra') return;

    $.get(`/myvet/app/controllers/egresosController.php?action=obtenerDetalleMovimiento&tipo=${tipo}&id=${id}`, function(data) {
        if (!data.success) return Swal.fire('Error', data.message, 'error');

        const c = data.cabecera;
        let filasHtml = '';

        data.items.forEach(item => {
            let nombre = item.producto_nombre;
            let conversionInfo = '';
            let detalleMovimientos = '';

            const factor = parseFloat(item.factor_prod || 1);
            let totalCant = parseFloat(item.cantidad_recibida || 0);

            if (factor && factor > 0) {
                conversionInfo = `<div class="text-primary fw-bold" style="font-size: 0.75rem;">
                    (1 ${item.unidad_reporte} = ${factor} ${item.unidad_medida})
                </div>`;
            }

            if (item.desglose_movimientos) {
                const movimientos = item.desglose_movimientos.split('||');
                detalleMovimientos = '<div class="mt-1 text-uppercase text-body-secondary fw-bold" style="font-size: 0.6rem;">Rastreo de Entradas:</div>';
                movimientos.forEach(mov => {
                    detalleMovimientos += `
                        <div class="small p-1 mb-1 bg-white border-start border-success border-3 shadow-sm" style="font-size: 0.7rem;">
                            <i class="bi bi-arrow-right text-success"></i>${item.producto_id} ${mov} ${item.unidad_medida}
                        </div>`;
                });
            }

            let unidadc = parseFloat(item.cantidad_pedida || item.cantidad || 0);
            let unidadr = parseFloat(item.cantidad_recibida || 0);

            const totalComprado = (unidadc / factor).toFixed(2);
            const totale = (parseFloat(item.cantidad_excedente || 0) / factor).toFixed(2);
            const totalr = (unidadr / factor).toFixed(2);
            const totalf = (totalComprado - totalr).toFixed(2);

            filasHtml += `
                <tr>
                    <td style="font-family: monospace; color: #64748b;">${item.sku || 'N/A'}</td>
                    <td>
                        <div class="fw-bold text-dark">${nombre}</div>
                        ${conversionInfo}
                        ${detalleMovimientos}
                    </td>
                    <td class="text-center bg-light">
                        <span class="d-block fw-bold">${totalComprado} ${item.unidad_reporte}</span>
                        <small class="text-body-secondary">(${unidadc} ${item.unidad_medida})</small>
                    </td>
                    <td class="text-center text-success fw-bold bg-light">
                        <span class="d-block fw-bold">${totalr} ${item.unidad_reporte}</span>
                        <small class="text-body-secondary">(${unidadr} ${item.unidad_medida})</small>
                    </td>
                    <td class="text-center text-success fw-bold bg-light">
                        ${item.cantidad_excedente > 0 ? `
                            <span class="d-block fw-bold">${totale} ${item.unidad_reporte}</span>
                            <small class="text-body-secondary">${item.cantidad_excedente} ${item.unidad_medida}</small>
                        ` : '0'}
                    </td>
                    <td class="text-center bg-light">
                        ${item.cantidad_faltante > 0 ? `
                            <span class="d-block fw-bold">${totalf} ${item.unidad_reporte}</span>
                            <small class="text-body-secondary">${item.cantidad_faltante} ${item.unidad_medida}</small>
                        ` : '0'}
                    </td>
                    <td class="text-end">$${unidadr >= 1 ? parseFloat(item.precio_unitario * factor).toFixed(2) + " x " + item.unidad_reporte : parseFloat(item.precio_unitario).toFixed(2) + " x " + item.unidad_medida}</td>
                    <td class="text-end fw-bold" style="color: #1e3a8a;">$${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>`;
        });

        const docHTML = `
            <div class="compra-invoice-box" id="compraDetalle_areaCapturaPDF">
                <table class="compra-table-layout">
                    <tr>
                        <td style="width: 32%;">
                            <div class="compra-logo-container">
                                <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" style="width: 38px; height: auto;" alt="Logo">
                                <div class="compra-brand-title">CF SISTEM<br><span style="font-size:10pt; font-weight:600; color:#0284c7;">INVENTARIOS</span></div>
                            </div>
                        </td>
                        <td style="width: 38%;" class="compra-company-address">
                            <span style="font-weight: 600; color: #1e293b;">${c.almacen_nombre || 'ALMACÉN GENERAL'}</span><br>
                            Gestión de Inventarios y Egresos<br>
                            <span style="font-size: 7.5pt; color: #94a3b8;">Control de Entradas / Compras</span>
                        </td>
                        <td style="width: 30%;">
                            <div class="compra-remision-badge">
                                COMPRA
                                <span>${c.folio}</span>
                            </div>
                            <div style="clear: both;"></div>
                            <table class="compra-date-tile">
                                <tr>
                                    <td class="title-td">Fecha</td>
                                    <td class="fw-bold" style="color: #334155;">${c.fecha_registro || c.fecha_gasto}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table class="compra-table-layout" style="margin-top: 4px;">
                    <tr>
                        <td style="width: 65%; padding-right: 6px;">
                            <div class="compra-card-info">
                                <div class="compra-card-title">PROVEEDOR</div>
                                <table style="width:100%; border-collapse:collapse; font-size: 8.5pt;">
                                    <tr>
                                        <td style="color:#64748b; width: 22%;"><strong>Nombre:</strong></td>
                                        <td class="fw-bold" style="color:#1e3a8a; font-size:9.5pt;">${c.proveedorNombre || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#64748b;"><strong>Usuario:</strong></td>
                                        <td style="color:#475569; font-size:8pt;">${c.usuario_nombre}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div class="compra-card-info" style="background-color: #f8fafc;">
                                <div class="compra-card-title" style="color:#0284c7;">INFORMACIÓN DE PAGO</div>
                                <div style="line-height: 1.4; color:#64748b; font-size: 8.5pt;">
                                    <strong>Estado:</strong> <span class="badge ${c.estado === 'confirmada' || c.estado === 'pagado' ? 'bg-success' : 'bg-warning'}">${c.estado.toUpperCase()}</span><br>
                                    <strong>Método:</strong> ${c.metodo_pago || 'N/A'}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="compra-items-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">SKU</th>
                            <th style="width: 28%;">DESCRIPCIÓN</th>
                            <th class="text-center" style="width: 12%;">CANT. COMPRADA</th>
                            <th class="text-center" style="width: 12%;">RECIBIDO</th>
                            <th class="text-center" style="width: 12%;">EXCEDENTE</th>
                            <th class="text-center" style="width: 8%;">PEND.</th>
                            <th class="text-end" style="width: 9%;">P. UNIT</th>
                            <th class="text-end" style="width: 9%;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasHtml}
                        <tr class="total-row">
                            <td colspan="6"></td>
                            <td class="text-end" style="color: #475569; font-size: 10pt; font-weight: 600; vertical-align: middle;">TOTAL NETO</td>
                            <td class="text-end compra-total-highlight">$${parseFloat(c.total).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="compra-card-obs">
                    <div style="font-weight: 700; color: #334155; margin-bottom: 2px; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.3px;">Observaciones</div>
                    ${c.observaciones ? `<span style="color:#1e293b;">${c.observaciones}</span>` : 'Sin observaciones registradas.'}
                </div>

                <div class="row compra-signatures">
                    <div class="col-4"><div class="compra-signature-line">SOLICITADO POR</div></div>
                    <div class="col-4"><div class="compra-signature-line">ALMACÉN / RECIBO</div></div>
                    <div class="col-4"><div class="compra-signature-line">AUTORIZACIÓN</div></div>
                </div>
            </div>`;

        $('#compraDetalle_contenedorContenido').html(docHTML);
        const modalEl = document.getElementById('compraDetalle_modalPrincipal');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    });
}
function compraDetalle_ejecutarImpresion() {
    const elemento = document.getElementById('compraDetalle_areaCapturaPDF');
    if (!elemento) return;

    // Extraer folio para el título de la ventana / archivo
    const folioEl = document.querySelector('.compra-remision-badge span');
    const folio = folioEl ? folioEl.innerText.trim() : '000';

    // Detección de dispositivos móviles para exportar PDF directamente
    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    if (esMovil) {
        const opciones = {
            margin:       [8, 8, 8, 8],
            filename:     `Compra_${folio}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
            jsPDF:        { unit: 'mm', format: 'a5', orientation: 'landscape' }
        };
        html2pdf().set(opciones).from(elemento).save();
        return;
    }

    // 1. Crear ventana emergente independiente
    const ventana = window.open('', '_blank', 'height=750,width=950');

    // 2. Inyectar estructura completa HTML + CSS
    ventana.document.write(`
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
            <title>COMPRA - ${folio}</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <style>
                @page { 
                    margin: 0; /* Elimina encabezados y pies de página predeterminados del navegador */
                }
                body { 
                    font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
                    padding: 10px;
                    background-color: #ffffff;
                    color: #334155;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                
                /* Estilos adaptados para la impresión del documento */
                .compra-invoice-box {
                    width: 100%;
                    margin: auto;
                    background: #fff;
                }
                .compra-table-layout {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                }
                .compra-logo-container {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .compra-brand-title {
                    font-size: 16pt;
                    font-weight: 800;
                    color: #1e3a8a;
                    line-height: 1.1;
                    letter-spacing: -0.5px;
                }
                .compra-company-address {
                    font-size: 8.5pt;
                    color: #64748b;
                    line-height: 1.4;
                    text-align: center;
                }
                .compra-remision-badge {
                    background: #0f172a !important;
                    color: #fff !important;
                    padding: 6px 12px;
                    text-align: center;
                    font-size: 8pt;
                    font-weight: 700;
                    letter-spacing: 1px;
                    border-radius: 4px;
                    float: right;
                    min-width: 140px;
                }
                .compra-remision-badge span {
                    display: block;
                    font-size: 13pt;
                    font-weight: 800;
                    color: #38bdf8 !important;
                    margin-top: 2px;
                }
                .compra-date-tile {
                    width: 140px;
                    float: right;
                    margin-top: 5px;
                    border-collapse: collapse;
                    font-size: 8.5pt;
                }
                .compra-date-tile td {
                    padding: 4px 8px;
                    border: 1px solid #e2e8f0;
                }
                .compra-date-tile .title-td {
                    background: #f1f5f9 !important;
                    color: #64748b;
                    font-weight: 600;
                }
                .compra-card-info {
                    border: 1px solid #e2e8f0;
                    border-radius: 6px;
                    padding: 10px 12px;
                    min-height: 90px;
                    background-color: #ffffff;
                }
                .compra-card-title {
                    font-size: 7.5pt;
                    font-weight: 800;
                    color: #64748b;
                    letter-spacing: 0.5px;
                    margin-bottom: 6px;
                    border-bottom: 1px solid #f1f5f9;
                    padding-bottom: 3px;
                    text-transform: uppercase;
                }
                .compra-items-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                    font-size: 8.5pt;
                }
                .compra-items-table th {
                    background: #0f172a !important;
                    color: #fff !important;
                    font-weight: 600;
                    font-size: 7.5pt;
                    text-transform: uppercase;
                    padding: 7px 8px;
                    letter-spacing: 0.5px;
                }
                .compra-items-table td {
                    padding: 8px;
                    border-bottom: 1px solid #e2e8f0;
                    vertical-align: top;
                }
                .compra-items-table tr:nth-child(even) {
                    background-color: #f8fafc !important;
                }
                .compra-items-table .total-row td {
                    border-bottom: none;
                    padding-top: 12px;
                }
                .compra-total-highlight {
                    font-size: 13pt;
                    font-weight: 800;
                    color: #1e3a8a !important;
                    background: #eff6ff !important;
                    padding: 6px 12px !important;
                    border-radius: 4px;
                    border: 1px solid #bfdbfe !important;
                }
                .compra-card-obs {
                    margin-top: 15px;
                    border: 1px dashed #cbd5e1;
                    border-radius: 6px;
                    padding: 10px 12px;
                    font-size: 8pt;
                    background: #fdfdfd;
                    line-height: 1.5;
                }
                .compra-signatures {
                    margin-top: 40px;
                    padding-top: 10px;
                    text-align: center;
                }
                .compra-signature-line {
                    border-top: 2px solid #334155;
                    margin: 0 15px;
                    padding-top: 5px;
                    font-size: 7.5pt;
                    font-weight: 700;
                    color: #334155;
                }
            </style>
        </head>
        <body>
            ${elemento.outerHTML}

            <script>
                window.onload = function() {
                    window.print();
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