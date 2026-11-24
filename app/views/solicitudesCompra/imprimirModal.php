<style>
    .doc-solicitud {
        --doc-border: rgba(128, 128, 128, 0.28);
        --doc-surface: rgba(128, 128, 128, 0.05);
        --doc-head-bg: rgba(31, 42, 55, 0.88);
        --doc-head-fg: #ffffff;
        min-height: 650px;
        font-size: 12px;
        color: inherit;
    }

    .doc-solicitud__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid var(--doc-border);
    }

    .doc-solicitud__meta-right {
        text-align: right;
        flex-shrink: 0;
    }

    .doc-solicitud__folio {
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
    }

    .doc-solicitud__label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        opacity: 0.72;
        margin-bottom: 2px;
    }

    .doc-solicitud__box {
        border: 1px solid var(--doc-border);
        background-color: var(--doc-surface);
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 14px;
    }

    .doc-solicitud__prov-top {
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px solid var(--doc-border);
    }

    .doc-solicitud__table thead th {
        background-color: var(--doc-head-bg);
        color: var(--doc-head-fg);
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .doc-solicitud__table th,
    .doc-solicitud__table td {
        border-color: var(--doc-border);
        vertical-align: middle;
    }

    .doc-solicitud__total-box {
        min-width: 230px;
        border: 1px solid var(--doc-border);
        background-color: var(--doc-surface);
        border-radius: 8px;
        padding: 8px 12px;
    }

    .doc-solicitud__firma {
        border-top: 1px solid var(--doc-border);
        padding-top: 6px;
        margin-top: 44px;
        text-align: center;
    }
</style>

<div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header text-white border-0"
                style="background: linear-gradient(135deg, #1f2a37 0%, #334155 100%);">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-printer me-2"></i>Vista de Impresión
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-3">
                <div id="areaImpresion" class="doc-solicitud p-2">

                    <!-- ENCABEZADO -->
                    <div class="doc-solicitud__header">
                        <div class="pe-2">
                            <div class="fw-bold text-uppercase mb-1" id="print-almacen"
                                style="font-size: 14px; letter-spacing: 0.5px;">
                                <?= htmlspecialchars($_SESSION['nombre_almacen'] ?? 'JSEA SYSTEM') ?>
                            </div>
                            <div class="mb-1" style="font-size: 11px;">
                                <span class="fw-semibold text-uppercase">RFC:</span>
                                <span><?= htmlspecialchars($_SESSION['rfc'] ?? 'ATSE') ?></span>
                            </div>
                            <div style="font-size: 11px;">
                                <span class="fw-semibold text-uppercase">DOMICILIO:</span>
                                <span
                                    class="text-uppercase"><?= htmlspecialchars($_SESSION['direccion'] ?? 'LA CIMA 11') ?></span>
                            </div>
                            <input type="hidden" id="dp_correo">
                        </div>

                        <div class="doc-solicitud__meta-right">
                            <div class="doc-solicitud__label mb-0">Solicitud de Compra</div>
                            <div class="doc-solicitud__folio" id="print-folio"></div>
                            <div style="font-size: 11px; opacity: 0.8;" id="print-fecha"></div>
                        </div>
                    </div>

                    <!-- DATOS PROVEEDOR -->
                    <div class="doc-solicitud__box">
                        <div class="doc-solicitud__prov-top">
                            <span class="doc-solicitud__label">Proveedor</span>
                            <span class="fw-bold text-uppercase" style="font-size: 12px;" id="print-proveedor"></span>
                        </div>

                        <div class="row g-2">
                            <div class="col-sm-4">
                                <span class="doc-solicitud__label">RFC</span>
                                <span class="text-uppercase" style="font-size: 11px;" id="print-rfc"></span>
                            </div>

                            <div class="col-sm-4">
                                <span class="doc-solicitud__label">Teléfono</span>
                                <span class="text-uppercase" style="font-size: 11px;" id="print-telefono"></span>
                            </div>

                            <div class="col-6 col-sm-2">
                                <span class="doc-solicitud__label">Tel. 2</span>
                                <span class="text-uppercase" style="font-size: 11px;" id="print-telefono2"></span>
                            </div>

                            <div class="col-6 col-sm-2">
                                <span class="doc-solicitud__label">Ext.</span>
                                <span class="text-uppercase" style="font-size: 11px;" id="print-extencion"></span>
                            </div>

                            <div class="col-md-6 pt-1">
                                <span class="doc-solicitud__label">Dirección Fiscal</span>
                                <span class="text-uppercase" style="font-size: 11px;" id="print-direccion"></span>
                            </div>

                            <div class="col-md-6 pt-1">
                                <span class="doc-solicitud__label">Dirección de Recolección</span>
                                <span class="fw-semibold text-uppercase" style="font-size: 11px;"
                                    id="print-direccion-recoleccion"></span>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA -->
                    <table class="table table-bordered table-sm doc-solicitud__table mb-3" style="font-size: 11px;">
                        <thead>
                            <tr>
                                <th style="width: 48%;">DESCRIPCIÓN</th>
                                <th style="width: 14%;" class="text-center">CANT.</th>
                                <th style="width: 19%;" class="text-end">P. UNIT.</th>
                                <th style="width: 19%;" class="text-end">IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody id="print-tabla-cuerpo"></tbody>
                    </table>

                    <!-- TOTAL -->
                    <div class="d-flex justify-content-end mb-4">
                        <div class="doc-solicitud__total-box">
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold" style="font-size: 15px;" id="costo_total"></span>
                            </div>
                        </div>
                    </div>

                    <!-- FIRMAS -->
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="doc-solicitud__firma">
                                <small class="text-uppercase fw-semibold" style="font-size: 10px;">Solicita</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="doc-solicitud__firma">
                                <small class="text-uppercase fw-semibold" style="font-size: 10px;">Autoriza</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="doc-solicitud__firma">
                                <small class="text-uppercase fw-semibold" style="font-size: 10px;">Recibe</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4" onclick="abrirModalEnviar()">
                    <i class="bi bi-envelope me-1"></i>Enviar por correo
                </button>
                <button type="button" class="btn btn-dark rounded-pill px-4"
                    style="background: linear-gradient(135deg,#1f2a37,#334155); border:0;"
                    onclick="ejecutarImpresion()">
                    <i class="bi bi-printer me-1"></i>Imprimir
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html3pdf@0.12.1/dist/html3pdf.bundle.min.js"></script>
<script>
    function prepararImpresion(id) {
        fetch(`/myvet/app/controllers/solicitudesCompraController.php?action=obtenerDetalle&id=${id}`)
            .then(async (res) => {
                const text = await res.text();
                console.log('RAW TEXT RESPONSE:', text);

                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('ERROR JSON:', e);
                    throw new Error('Respuesta no es JSON válido');
                }
            })
            .then(res => {
                console.log('RAW RESPONSE OBJ:', res);
                console.log('STATUS:', res.status);
                console.log('DATA:', res.data);
                console.log('COSTO:', res.costo);

                if (res.status !== 'success' || !Array.isArray(res.data) || !res.data.length) return;

                const data = res.data;
                const infoBase = data[0];
                const costoTotal = parseFloat(res.costo || 0);

                if (infoBase.ubicacion_destino > 0 && typeof cargarUbicacionParaEditar === 'function') {
                    cargarUbicacionParaEditar(infoBase.ubicacion_destino);
                }

                const partesDireccion = [
                    infoBase.dp_direccion,
                    infoBase.dp_numeroInt,
                    infoBase.dp_colonia,
                    infoBase.dp_ciudad,
                    infoBase.dp_codigo_postal,
                ]
                    .filter(v => v != null && String(v).trim() !== '' && String(v).trim() !== '0')
                    .map(v => String(v).trim());

                const direccionProveedor = partesDireccion.join(', ');

                $('#print-folio').text(`#${String(id).padStart(5, '0')}`);
                $('#print-fecha').text(`Fecha: ${new Date().toLocaleDateString('es-MX')}`);
                if (infoBase.almacen_nombre) {
                    $('#print-almacen').text(infoBase.almacen_nombre);
                }
                $('#print-proveedor').text(infoBase.proveedor_nombre || 'No especificado');
                $('#print-direccion').text(direccionProveedor || 'No especificado');
                $('#print-rfc').text(infoBase.dp_rfc || 'No especificado');
                $('#print-telefono').text(infoBase.dp_telefono || 'No especificado');
                $('#print-telefono2').text(infoBase.dp_telefono2 || '—');
                $('#print-extencion').text(infoBase.dp_extension || infoBase.dp_extencion || '—');
                $('#print-direccion-recoleccion').text(infoBase.ubicacion_destino || 'No especificado');
                $('#dp_correo').val(infoBase.dp_correo || '');

                $('#costo_total').text(
                    costoTotal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })
                );

                let html = '';

                data.forEach(i => {
                    const uRep = i.unidad_reporte || i.unidad_medida || 'pzas';
                    const costo = parseFloat(i.costo) || 0;
                    const cantidad = parseFloat(i.cantidad) || 0;
                    const costoUnitario = cantidad > 0 ? (costo / cantidad) : 0;

                    html += `
                        <tr>
                            <td class="fw-bold">${i.producto_nombre || ''}</td>
                            <td class="text-center">${cantidad} ${uRep}</td>
                            <td class="text-end">${costoUnitario.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}</td>
                            <td class="text-end fw-semibold">${costo.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}</td>
                        </tr>`;
                });

                $('#print-tabla-cuerpo').html(html);

                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalImprimirSolicitud')).show();
            })
            .catch(err => {
                console.error('FETCH ERROR:', err);
            });
    }

    function ejecutarImpresion() {
        const esMovil = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent) ||
            (navigator.maxTouchPoints && navigator.maxTouchPoints > 2 && /MacIntel/.test(navigator.platform));

        const elementoAImprimir = document.getElementById('areaImpresion');
        const folio = $('#print-folio').text().trim() || 'solicitud';

        if (esMovil) {
            const opciones = {
                margin: [10, 10, 10, 10],
                filename: `Solicitud_${folio}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, letterRendering: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html3pdf()
                .set(opciones)
                .from(elementoAImprimir)
                .save()
                .then(() => {
                    console.log('PDF generado y descargado correctamente.');
                })
                .catch((err) => {
                    console.error('Error al generar el PDF:', err);
                    alert('No se pudo generar el PDF. Intenta de nuevo.');
                });

            return;
        }

        const contenido = elementoAImprimir.innerHTML;

        const iframe = document.createElement('iframe');
        iframe.setAttribute('aria-hidden', 'true');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.style.visibility = 'hidden';
        document.body.appendChild(iframe);

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
        <html>
            <head>
                <title>Imprimir ${folio}</title>
                <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
                <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Inter', sans-serif; padding: 30px; color: #111827; background: #ffffff; }
                    .doc-solicitud { min-height: auto !important; }
                    .doc-solicitud__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding-bottom: 12px; margin-bottom: 14px; border-bottom: 1px solid #444; }
                    .doc-solicitud__meta-right { text-align: right; }
                    .doc-solicitud__folio { font-size: 16px; font-weight: 700; }
                    .doc-solicitud__label { display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; opacity: 0.75; margin-bottom: 2px; }
                    .doc-solicitud__box { border: 1px solid #444; border-radius: 8px; padding: 10px 12px; margin-bottom: 14px; background: transparent; }
                    .doc-solicitud__prov-top { padding-bottom: 6px; margin-bottom: 8px; border-bottom: 1px solid #ccc; }
                    .doc-solicitud__table thead th { background-color: #1f2a37 !important; color: #ffffff !important; }
                    .table { background-color: transparent !important; }
                    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
                    .doc-solicitud__total-box { min-width: 220px; border: 1px solid #444; border-radius: 6px; padding: 8px 12px; }
                    .doc-solicitud__firma { border-top: 1px solid #444; padding-top: 6px; margin-top: 48px; text-align: center; }
                    .fw-bold { font-weight: bold !important; }
                    @media print {
                        .no-print { display: none; }
                        @page { margin: 0 !important; }
                    }
                </style>
            </head>
            <body>
                <img
                    src="/myvet/public/assets/logo.ico"
                    style="
                        position: fixed;
                        top: 19.5%;
                        left: 50%;
                        transform: translate(-70%, -70%);
                        width: 180px;
                        opacity: 0.08;
                        z-index: -1;
                    "
                >
                ${contenido}
            </body>
        </html>
    `);
        doc.close();

        const win = iframe.contentWindow;

        let limpiado = false;
        const limpiar = () => {
            if (limpiado) return;
            limpiado = true;
            if (iframe && iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }
        };

        win.onafterprint = limpiar;

        setTimeout(() => {
            try {
                win.focus();
                win.print();
            } catch (e) {
                console.error('Error al imprimir:', e);
                limpiar();
            }
            setTimeout(limpiar, 2000);
        }, 500);
    }
</script>