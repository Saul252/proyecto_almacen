<div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
    <div class="mi-modal-overlay">
        <div class="mi-modal">
            <!-- HEADER -->
            <div class="mi-modal-header">
                <div class="mi-modal-header-left">
                    <div class="mi-modal-icon">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 1h5l3 3v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                            <path d="M9 1v3h3" />
                            <path d="M5 8h6M5 10h6M5 12h4" />
                        </svg>
                    </div>
                    <div>
                        <h6 class="mi-modal-title">Vista previa de cotización</h6>
                        <small class="mi-modal-subtitle">JSEA · Documento comercial</small>
                    </div>
                </div>
                <button type="button" class="mi-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">×</button>
            </div>

            <div class="mi-modal-body">
                <div id="areaImpresion" class="mi-documento">

                    <!-- ENCABEZADO -->
                    <div class="mi-doc-header">
                        <div class="mi-doc-header-left">
                            <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
                                alt="Logo" class="mi-doc-logo">
                            <div>
                                <div class="mi-doc-eyebrow">Documento comercial</div>
                                <h2 class="mi-doc-titulo">Cotización</h2>
                                <div class="mi-doc-folio">Folio <span id="print-folio">#00000</span></div>
                            </div>
                        </div>
                        <div class="mi-doc-header-right">
                            <div class="mi-doc-empresa">JSEA</div>
                            <div class="mi-doc-fecha" id="print-fecha">—</div>
                            <div class="mi-doc-estado">Vigente</div>
                        </div>
                    </div>

                    <div class="mi-doc-hairline"></div>

                    <!-- INFO COMPACTA -->
                    <div class="mi-doc-info">
                        <div class="mi-doc-card">
                            <div class="mi-doc-label">Vendedor</div>
                            <div class="mi-doc-valor" id="vendedor">—</div>
                        </div>
                        <div class="mi-doc-card">
                            <div class="mi-doc-label">Cliente</div>
                            <div class="mi-doc-valor" id="clienteData">—</div>
                        </div>
                    </div>

                    <!-- TABLA CON BORDES -->
                    <div class="mi-doc-tabla-wrap">
                        <table class="mi-doc-tabla">
                            <thead>
                                <tr>
                                    <th class="mi-col-num">#</th>
                                    <th class="mi-col-producto">Producto</th>
                                    <th class="mi-col-unidad">Unidad</th>
                                    <th class="mi-col-cantidad">Cant.</th>
                                    <th class="mi-col-precio">P. Unit.</th>
                                    <th class="mi-col-subtotal">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="print-tabla-cuerpo"></tbody>
                        </table>
                    </div>

                    <!-- TOTAL -->
                    <div class="mi-doc-total-wrap">
                        <div class="mi-doc-total">
                            <div class="mi-doc-total-label">Total</div>
                            <div class="mi-doc-total-valor" id="costo_total">$0.00</div>
                        </div>
                    </div>

                    <!-- FIRMA -->
                    <div class="mi-doc-firma">
                        <div class="mi-doc-firma-line"></div>
                        <div class="mi-doc-firma-label">Firma autorizada · JSEA</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* FORZAR COLORES AL IMPRIMIR */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    /* MODAL */
    #modalImprimirSolicitud {
        position: fixed;
        inset: 0;
        z-index: 1055;
        display: none;
        overflow-y: auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        -webkit-font-smoothing: antialiased;
        color: #111827;
    }

    #modalImprimirSolicitud.mi-abierto {
        display: block;
    }

    .mi-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.45);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .mi-modal {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        width: 100%;
        max-width: 820px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .mi-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #fafbfc;
        border-bottom: 1px solid #eef0f3;
        flex-shrink: 0;
    }

    .mi-modal-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mi-modal-icon {
        width: 28px;
        height: 28px;
        background: #eef4ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
    }

    .mi-modal-icon svg {
        width: 15px;
        height: 15px;
    }

    .mi-modal-title {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1f2937;
        letter-spacing: -0.1px;
    }

    .mi-modal-subtitle {
        display: block;
        font-size: 0.7rem;
        color: #9ca3af;
        margin-top: 1px;
    }

    .mi-modal-close {
        background: none;
        border: none;
        font-size: 1.4rem;
        line-height: 1;
        color: #9ca3af;
        cursor: pointer;
        padding: 0 4px;
        opacity: 0.7;
        transition: opacity 0.15s;
    }

    .mi-modal-close:hover {
        opacity: 1;
    }

    .mi-modal-body {
        background: #f8f9fb;
        overflow-y: auto;
        flex: 1;
    }

    /* DOCUMENTO */
    .mi-documento {
        background: #ffffff;
        padding: 40px 40px;
        min-height: 620px;
        font-size: 0.85rem;
        color: #111827;
    }

    /* ENCABEZADO */
    .mi-doc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 22px;
    }

    .mi-doc-header-left {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .mi-doc-logo {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 3px;
        background: #ffffff;
    }

    .mi-doc-eyebrow {
        font-size: 0.58rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #9ca3af;
        margin-bottom: 1px;
    }

    .mi-doc-titulo {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
        color: #000000;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }

    .mi-doc-folio {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 3px;
    }

    .mi-doc-folio span {
        color: #000000;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .mi-doc-header-right {
        text-align: right;
    }

    .mi-doc-empresa {
        font-size: 0.9rem;
        font-weight: 700;
        color: #000000;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .mi-doc-fecha {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 1px;
        font-variant-numeric: tabular-nums;
    }

    .mi-doc-estado {
        display: inline-block;
        margin-top: 5px;
        font-size: 0.58rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #111827;
        background: #f3f4f6;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
    }

    .mi-doc-hairline {
        height: 1px;
        background: #e5e7eb;
        margin: 0 0 18px 0;
    }

    /* INFO COMPACTA */
    .mi-doc-info {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 10px;
        margin-bottom: 20px;
    }

    .mi-doc-card {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 9px 12px;
        background: #ffffff;
    }

    .mi-doc-label {
        font-size: 0.55rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        color: #9ca3af;
        margin-bottom: 3px;
    }

    .mi-doc-valor {
        font-size: 0.8rem;
        color: #111827;
        font-weight: 500;
        line-height: 1.4;
    }

    .mi-doc-valor>div {
        font-weight: 500;
    }

    .mi-doc-valor div[style*="color:#6b7280"] {
        font-weight: 400;
    }

    /* TABLA CON BORDES */
    .mi-doc-tabla-wrap {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 18px;
    }

    .mi-doc-tabla {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
        font-variant-numeric: tabular-nums;
    }

    .mi-doc-tabla thead {
        background: #111827;
    }

    .mi-doc-tabla th {
        color: #ffffff;
        font-size: 0.58rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        padding: 8px 10px;
        text-align: left;
        white-space: nowrap;
        border-right: 1px solid #374151;
    }

    .mi-doc-tabla th:last-child {
        border-right: none;
    }

    .mi-doc-tabla td {
        color: #111827;
        padding: 7px 10px;
        border-bottom: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .mi-doc-tabla td:last-child {
        border-right: none;
    }

    .mi-doc-tabla tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    .mi-doc-tabla tbody tr:last-child td {
        border-bottom: none;
    }

    .mi-col-num {
        width: 6%;
        text-align: center;
    }

    .mi-col-producto {
        width: 34%;
    }

    .mi-col-unidad {
        width: 14%;
        text-align: center;
    }

    .mi-col-cantidad {
        width: 10%;
        text-align: center;
    }

    .mi-col-precio {
        width: 17%;
        text-align: right;
    }

    .mi-col-subtotal {
        width: 19%;
        text-align: right;
    }

    .mi-doc-tabla th.mi-col-num,
    .mi-doc-tabla th.mi-col-unidad,
    .mi-doc-tabla th.mi-col-cantidad {
        text-align: center;
    }

    .mi-doc-tabla th.mi-col-precio,
    .mi-doc-tabla th.mi-col-subtotal {
        text-align: right;
    }

    .mi-doc-tabla td.mi-col-num {
        text-align: center;
        color: #9ca3af;
        font-size: 0.68rem;
        font-weight: 500;
    }

    .mi-doc-tabla td.mi-col-unidad,
    .mi-doc-tabla td.mi-col-cantidad {
        text-align: center;
        color: #4b5563;
    }

    .mi-doc-tabla td.mi-col-precio {
        text-align: right;
        color: #4b5563;
    }

    .mi-doc-tabla td.mi-col-subtotal {
        text-align: right;
        font-weight: 600;
        color: #000000;
    }

    /* TOTAL */
    .mi-doc-total-wrap {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 30px;
    }

    .mi-doc-total {
        background: #000000;
        border-radius: 6px;
        padding: 10px 20px;
        min-width: 220px;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 20px;
    }

    .mi-doc-total-label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.58rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .mi-doc-total-valor {
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.3px;
        line-height: 1.2;
        font-variant-numeric: tabular-nums;
    }

    /* FIRMA */
    .mi-doc-firma {
        margin-top: 40px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .mi-doc-firma-line {
        width: 180px;
        height: 1px;
        background: #111827;
        margin-bottom: 6px;
    }

    .mi-doc-firma-label {
        font-size: 0.6rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 500;
    }

    /* RESPONSIVE */
    @media (max-width: 640px) {
        .mi-documento {
            padding: 24px 20px;
        }

        .mi-doc-info {
            grid-template-columns: 1fr;
        }

        .mi-doc-tabla th,
        .mi-doc-tabla td {
            padding: 6px 5px;
            font-size: 0.68rem;
        }

        .mi-doc-tabla th {
            font-size: 0.52rem;
        }

        .mi-doc-total {
            min-width: 160px;
        }

        .mi-doc-firma-line {
            width: 130px;
        }
    }

    /* PRINT — forzar colores y bordes */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff !important;
        }

        .mi-doc-tabla thead {
            background: #111827 !important;
        }

        .mi-doc-tabla th {
            background: #111827 !important;
            color: #ffffff !important;
        }

        .mi-doc-tabla tbody tr:nth-child(even) {
            background: #f9fafb !important;
        }

        .mi-doc-total {
            background: #000000 !important;
            color: #ffffff !important;
        }

        .mi-doc-total-valor {
            color: #ffffff !important;
        }

        .mi-doc-tabla td {
            border-bottom: 1px solid #e5e7eb !important;
            border-right: 1px solid #e5e7eb !important;
        }
    }
</style>

<script>
    async function prepararImpresion(id) {
        try {
            const $tbody = document.getElementById('print-tabla-cuerpo');
            $tbody.innerHTML = '';

            const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);

            const datos = await resp.json();
            const data = datos.data;

            if (!Array.isArray(data) || data.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin datos',
                    text: 'No se encontró el detalle de la cotización.',
                    confirmButtonColor: '#111827'
                });
                return;
            }

            const infoBase = data[0];
            const folio = String(id).padStart(5, '0');
            const fecha = new Date().toLocaleDateString('es-MX', {
                day: '2-digit', month: 'short', year: 'numeric'
            });

            document.getElementById('print-folio').textContent = `#${folio}`;
            document.getElementById('print-fecha').textContent = fecha;

            // Cliente
            const clienteHtml = `
            <div style="font-weight:500;color:#111827;">${infoBase.cliente_nombre || 'No especificado'}</div>
            ${infoBase.direccion ? `<div style="color:#6b7280;font-size:0.72rem;font-weight:400;">${infoBase.direccion}</div>` : ''}
            ${infoBase.telefono ? `<div style="color:#6b7280;font-size:0.72rem;font-weight:400;">${infoBase.telefono}</div>` : ''}
        `;
            document.getElementById('clienteData').innerHTML = clienteHtml;

            // Vendedor
            const vendedorHtml = `
            <div style="display:flex;align-items:center;gap:5px;">
                <svg viewBox="0 0 16 16" width="12" height="12" fill="none" stroke="#111827" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="8" cy="5" r="3"/>
                    <path d="M2 14c0-3 3-5 6-5s6 2 6 5"/>
                </svg>
                <span>${infoBase.nombreVendedor || '—'}</span>
            </div>
        `;
            document.getElementById('vendedor').innerHTML = vendedorHtml;

            // Filas
            let totalGeneral = 0;
            let html = '';
            let n = 1;

            data.forEach(i => {
                const cantidad = parseFloat(i.cantidad) || 0;
                const precioUnitario = parseFloat(i.precio_unitario) || 0;
                const subtotal = parseFloat(i.subtotal) || 0;

                totalGeneral += subtotal;

                html += `
                <tr>
                    <td class="mi-col-num">${String(n).padStart(2, '0')}</td>
                    <td class="mi-col-producto">${i.producto_nombre || ''}</td>
                    <td class="mi-col-unidad">${i.nombre || ''}</td>
                    <td class="mi-col-cantidad">${cantidad}</td>
                    <td class="mi-col-precio">${precioUnitario.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}</td>
                    <td class="mi-col-subtotal">${subtotal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}</td>
                </tr>`;
                n++;
            });

            $tbody.innerHTML = html;
            document.getElementById('costo_total').textContent = totalGeneral.toLocaleString('es-MX', {
                style: 'currency', currency: 'MXN'
            });

            ejecutarImpresion(clienteHtml, vendedorHtml, html, id, totalGeneral, folio, fecha);

        } catch (e) {
            console.error('[IMPRIMIR] Error:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo preparar la cotización: ' + e.message,
                confirmButtonColor: '#111827'
            });
        }
    }
    function ejecutarImpresion(cliente, vendedor, html, id, totalGeneral, folio, fecha) {
        const ventana = window.open('', '_blank', 'height=700,width=900');
        if (!ventana) {
            Swal.fire({
                icon: 'warning',
                title: 'Ventana bloqueada',
                text: 'Permite las ventanas emergentes para imprimir.',
                confirmButtonColor: '#111827'
            });
            return;
        }

        const totalFormateado = totalGeneral.toLocaleString('es-MX', {
            style: 'currency', currency: 'MXN'
        });

        ventana.document.write(`
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Cotización #${folio} — JSEA</title>
        <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
        <style>
            @page { margin: 0; }
            * {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                box-sizing: border-box;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
                padding: 1.3cm;
                color: #111827;
                background: #ffffff;
                margin: 0;
                font-size: 0.85rem;
            }

            /* ENCABEZADO */
            .doc-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 22px;
            }
            .doc-header-left { display: flex; align-items: flex-start; gap: 12px; }
            .doc-logo {
                width: 42px; height: 42px;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                padding: 3px;
                background: #ffffff;
            }
            .doc-eyebrow {
                font-size: 0.58rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1.2px;
                color: #9ca3af;
                margin-bottom: 1px;
            }
            .doc-titulo {
                margin: 0;
                font-size: 1.4rem;
                font-weight: 700;
                color: #000000;
                letter-spacing: -0.5px;
                line-height: 1.1;
            }
            .doc-folio {
                font-size: 0.7rem;
                color: #6b7280;
                margin-top: 3px;
            }
            .doc-folio span {
                color: #000000;
                font-weight: 600;
                font-variant-numeric: tabular-nums;
            }
            .doc-header-right { text-align: right; }
            .doc-empresa {
                font-size: 0.9rem;
                font-weight: 700;
                color: #000000;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }
            .doc-fecha {
                font-size: 0.7rem;
                color: #6b7280;
                margin-top: 1px;
                font-variant-numeric: tabular-nums;
            }
            .doc-estado {
                display: inline-block;
                margin-top: 5px;
                font-size: 0.58rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                color: #111827;
                background: #f3f4f6;
                padding: 2px 7px;
                border-radius: 4px;
                border: 1px solid #e5e7eb;
            }

            .doc-hairline {
                height: 1px;
                background: #e5e7eb;
                margin: 0 0 18px 0;
            }

            /* INFO COMPACTA */
            .doc-info {
                display: grid;
                grid-template-columns: 1fr 1.6fr;
                gap: 10px;
                margin-bottom: 20px;
            }
            .doc-card {
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 9px 12px;
                background: #ffffff;
            }
            .doc-label {
                font-size: 0.55rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.9px;
                color: #9ca3af;
                margin-bottom: 3px;
            }
            .doc-valor {
                font-size: 0.8rem;
                color: #111827;
                font-weight: 500;
                line-height: 1.4;
            }

            /* TABLA CON BORDES */
            .doc-tabla-wrap {
                border: 1px solid #d1d5db;
                border-radius: 6px;
                overflow: hidden;
                margin-bottom: 18px;
            }
            .doc-tabla {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.78rem;
                font-variant-numeric: tabular-nums;
            }
            .doc-tabla thead { background: #111827; }
            .doc-tabla th {
                color: #ffffff;
                font-size: 0.58rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.9px;
                padding: 8px 10px;
                text-align: left;
                white-space: nowrap;
                border-right: 1px solid #374151;
            }
            .doc-tabla th:last-child { border-right: none; }

            .doc-tabla td {
                color: #111827;
                padding: 7px 10px;
                border-bottom: 1px solid #e5e7eb;
                border-right: 1px solid #e5e7eb;
                vertical-align: middle;
            }
            .doc-tabla td:last-child { border-right: none; }
            .doc-tabla tbody tr:nth-child(even) { background: #f9fafb; }
            .doc-tabla tbody tr:last-child td { border-bottom: none; }

            .col-num      { width: 6%;  text-align: center; }
            .col-producto { width: 34%; }
            .col-unidad   { width: 14%; text-align: center; }
            .col-cantidad { width: 10%; text-align: center; }
            .col-precio   { width: 17%; text-align: right; }
            .col-subtotal { width: 19%; text-align: right; }

            .doc-tabla th.col-num,
            .doc-tabla th.col-unidad,
            .doc-tabla th.col-cantidad { text-align: center; }
            .doc-tabla th.col-precio,
            .doc-tabla th.col-subtotal { text-align: right; }

            .doc-tabla td.col-num {
                text-align: center;
                color: #9ca3af;
                font-size: 0.68rem;
                font-weight: 500;
            }
            .doc-tabla td.col-unidad,
            .doc-tabla td.col-cantidad { text-align: center; color: #4b5563; }
            .doc-tabla td.col-precio { text-align: right; color: #4b5563; }
            .doc-tabla td.col-subtotal {
                text-align: right;
                font-weight: 600;
                color: #000000;
            }

            /* TOTAL */
            .doc-total-wrap {
                display: flex;
                justify-content: flex-end;
                margin-bottom: 30px;
            }
            .doc-total {
                background: #000000;
                border-radius: 6px;
                padding: 10px 20px;
                min-width: 220px;
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                gap: 20px;
            }
            .doc-total-label {
                color: rgba(255, 255, 255, 0.6);
                font-size: 0.58rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .doc-total-valor {
                color: #ffffff;
                font-size: 1.25rem;
                font-weight: 700;
                letter-spacing: -0.3px;
                line-height: 1.2;
                font-variant-numeric: tabular-nums;
            }

            /* FIRMA */
            .doc-firma {
                margin-top: 40px;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
            }
            .doc-firma-line {
                width: 180px;
                height: 1px;
                background: #111827;
                margin-bottom: 6px;
            }
            .doc-firma-label {
                font-size: 0.6rem;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 500;
            }

            @media print {
                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }
                body { padding: 1cm; }
                .doc-tabla thead { background: #111827 !important; }
                .doc-tabla th { background: #111827 !important; color: #ffffff !important; }
                .doc-tabla tbody tr:nth-child(even) { background: #f9fafb !important; }
                .doc-tabla td { border-bottom: 1px solid #e5e7eb !important; border-right: 1px solid #e5e7eb !important; }
                .doc-total { background: #000000 !important; }
                .doc-total-valor { color: #ffffff !important; }
            }
        </style>
    </head>
    <body>
        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
            style="position: fixed; top: 40%; left: 50%; transform: translate(-50%, -50%);
                   width: 200px; opacity: 0.03; z-index: -1; pointer-events: none;">

        <div id="areaImpresion" style="min-height: 620px;">

            <!-- ENCABEZADO -->
            <div class="doc-header">
                <div class="doc-header-left">
                    <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
                        alt="Logo" class="doc-logo">
                    <div>
                        <div class="doc-eyebrow">Documento comercial</div>
                        <h2 class="doc-titulo">Cotización</h2>
                        <div class="doc-folio">Folio <span>#${folio}</span></div>
                    </div>
                </div>
                <div class="doc-header-right">
                    <div class="doc-empresa">JSEA</div>
                    <div class="doc-fecha">${fecha}</div>
                    <div class="doc-estado">Vigente</div>
                </div>
            </div>

            <div class="doc-hairline"></div>

            <!-- INFO COMPACTA -->
            <div class="doc-info">
                <div class="doc-card">
                    <div class="doc-label">Vendedor</div>
                    <div class="doc-valor">${vendedor}</div>
                </div>
                <div class="doc-card">
                    <div class="doc-label">Cliente</div>
                    <div class="doc-valor">${cliente}</div>
                </div>
            </div>

            <!-- TABLA CON BORDES -->
            <div class="doc-tabla-wrap">
                <table class="doc-tabla">
                    <thead>
                        <tr>
                            <th class="col-num">#</th>
                            <th class="col-producto">Producto</th>
                            <th class="col-unidad">Unidad</th>
                            <th class="col-cantidad">Cant.</th>
                            <th class="col-precio">P. Unit.</th>
                            <th class="col-subtotal">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>${html}</tbody>
                </table>
            </div>

            <!-- TOTAL -->
            <div class="doc-total-wrap">
                <div class="doc-total">
                    <div class="doc-total-label">Total</div>
                    <div class="doc-total-valor">${totalFormateado}</div>
                </div>
            </div>

            <!-- FIRMA -->
            <div class="doc-firma">
                <div class="doc-firma-line"></div>
                <div class="doc-firma-label">Firma autorizada · JSEA</div>
            </div>

        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                setTimeout(() => {
                    if (esMovil) {
                        const elemento = document.getElementById('areaImpresion');
                        html2pdf().set({
                            margin: 1,
                            filename: 'Cotizacion_${folio}_JSEA.pdf',
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2, useCORS: true },
                            jsPDF: { unit: 'cm', format: 'letter', orientation: 'portrait' }
                        }).from(elemento).save();
                    } else {
                        window.print();
                    }
                }, 1000);
            });
        <\/script>
    </body>
    </html>
    `);

        ventana.document.close();
    }
</script>