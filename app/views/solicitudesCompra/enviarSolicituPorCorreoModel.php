<div class="modal fade" id="modalEnviarCorreo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <div class="modal-header text-white border-0 py-3"
                style="background: linear-gradient(135deg, #0f766e 0%, #10b981 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-paper-fill fs-4"></i>
                    <h5 class="fw-bold mb-0">Enviar por correo</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <label for="envio-correo" class="form-label fw-semibold text-secondary mb-1">
                    Correo destinatario
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input type="email" class="form-control border-start-0 ps-0" id="envio-correo"
                        placeholder="correo@ejemplo.com" autocomplete="off" value="">
                </div>
                <div class="text-danger small mt-1" id="envio-correo-error" style="display:none;"></div>
            </div>

            <div class="modal-footer border-top-0 px-4 py-3 justify-content-end gap-2" style="background: #f8fafc;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                    data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn rounded-pill px-4 fw-semibold text-white border-0"
                    id="btn-confirmar-envio" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="bi bi-send-fill me-2"></i>Enviar
                </button>
            </div>

        </div>
    </div>
</div>
<script>
    // ============================================
    // ABRIR MODAL DE ENVÍO POR CORREO
    // ============================================
    function abrirModalEnviar() {
        const modalEl = document.getElementById('modalEnviarCorreo');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        const correo = $('#dp_correo').val();
        $('#envio-correo').val(correo);
        console.log(correo);
        // Clonar botón para evitar listeners duplicados
        const btnViejo = document.getElementById('btn-confirmar-envio');
        const btnNuevo = btnViejo.cloneNode(true);
        btnViejo.parentNode.replaceChild(btnNuevo, btnViejo);

        btnNuevo.addEventListener('click', () => enviarDesdeModal(modal));

        // Enter en el input dispara el envío
        $('#envio-correo').off('keypress').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                btnNuevo.click();
            }
        });
    }

    // ============================================
    // ENVIAR EL HTML DEL MODAL DE IMPRESIÓN
    // ============================================
    async function enviarDesdeModal(modal) {
        const correo = $('#envio-correo').val();
        $('#envio-correo').val(correo);
        console.log(correo);

        if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            $('#envio-correo-error').text('Ingresa un correo válido').show();
            return;
        }
        $('#envio-correo-error').hide();
        modal.hide();

        Swal.fire({
            title: 'Enviando...',
            html: 'Por favor espera ⏳',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const $cont = $('#areaImpresion');
            if (!$cont.length) throw new Error('No se encontró el área de impresión');

            // 1) Clonar con valores de inputs
            const $clon = $cont.clone();
            $cont.find('input, textarea, select').each(function (i) {
                const $c = $clon.find('input, textarea, select').eq(i);
                if (!$c.length) return;
                $c.attr('value', $(this).val());
                $c.val($(this).val());
            });

            // 2) 🎯 REEMPLAZO QUIRÚRGICO: bloque proveedor → <table> real
            const $bloqueProv = $clon.find('.border.rounded.p-2.mb-3').first();
            if ($bloqueProv.length) {
                const nombre = $clon.find('#print-proveedor').text() || 'No especificado';
                const rfc = $clon.find('#print-rfc').text() || 'No especificado';
                const tel = $clon.find('#print-telefono').text() || '0';
                const tel2 = $clon.find('#print-telefono2').text() || '';
                const ext = $clon.find('#print-extencion').text() || '';
                const direccion = $clon.find('#print-direccion').text() || 'No especificado';
                const recogida = $clon.find('#print-direccion-recogida').text() || '';

                const tablaProv = `
                <table style="width:100%;border-collapse:collapse;border:1px solid #cbd5e1;margin-bottom:12px;font-family:Arial,sans-serif;font-size:11px;">
                    <tbody>
                        <tr>
                            <td colspan="4" style="padding:6px 10px;vertical-align:top;">
                                <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">Proveedor</div>
                                <div style="font-weight:700;text-transform:uppercase;">${nombre}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:50%;padding:6px 10px;border-top:1px solid #e5e7eb;vertical-align:top;">
                                <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">RFC</div>
                                <div style="font-weight:700;text-transform:uppercase;">${rfc}</div>
                            </td>
                            <td style="width:50%;padding:6px 10px;border-top:1px solid #e5e7eb;vertical-align:top;">
                                <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">Teléfono</div>
                                <div style="font-weight:700;text-transform:uppercase;">${tel}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="width:68%;padding:6px 10px;border-top:1px solid #e5e7eb;vertical-align:top;">
                                <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">Dirección Fiscal</div>
                                <div style="font-weight:700;text-transform:uppercase;">${direccion}</div>
                            </td>
                            <td style="width:16%;padding:6px 10px;border-top:1px solid #e5e7eb;vertical-align:top;">
                                <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">Tel. 2</div>
                                <div style="font-weight:700;text-transform:uppercase;">${tel2}</div>
                            </td>
                            <td style="width:16%;padding:6px 10px;border-top:1px solid #e5e7eb;vertical-align:top;">
                                <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">Ext.</div>
                                <div style="font-weight:700;text-transform:uppercase;">${ext}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding:6px 10px;border-top:1px solid #e5e7eb;vertical-align:top;">
                                <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">Dirección de recolección</div>
                                <div style="font-weight:700;text-transform:uppercase;">${recogida}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
                $bloqueProv.replaceWith(tablaProv);
            }

            // 3) 🎯 REEMPLAZO QUIRÚRGICO: cabecera → <table> real
            const $header = $clon.find('.d-flex.justify-content-between.align-items-start').first();
            if ($header.length) {
                const titulo = $header.find('.fw-bold.text-uppercase').first().text().trim();
                const folioTxt = $clon.find('#print-folio').text().trim();
                const fechaTxt = $clon.find('#print-fecha').text().trim();
                const metaRfc = $header.find('.tk-meta, [style*="letter-spacing"]').eq(0).text().trim();

                // Extrae manualmente los datos del header original
                const rfcLine = $clon.find('spam.fw-bold').filter(function () {
                    return $(this).text().trim().toUpperCase() === 'RFC:';
                }).parent().text().trim();

                const domLine = $clon.find('spam.fw-bold').filter(function () {
                    return $(this).text().trim().toUpperCase() === 'DOMICILIO:';
                }).parent().text().trim();

                const tablaHeader = `
                <table style="width:100%;border-collapse:collapse;border-bottom:1px solid #cbd5e1;margin-bottom:12px;font-family:Arial,sans-serif;font-size:11px;">
                    <tbody>
                        <tr>
                            <td style="width:55%;padding:4px 0;vertical-align:top;">
                                <div style="font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:13px;">${titulo}</div>
                            </td>
                            <td style="width:45%;padding:4px 0;vertical-align:top;text-align:right;">
                                <div style="font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:13px;">Solicitud de Compra ${folioTxt}</div>
                                <div style="font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;margin-top:4px;">Fecha: ${fechaTxt}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding:4px 0;vertical-align:top;font-size:11px;letter-spacing:.5px;line-height:1.4;">
                                <div>${rfcLine}</div>
                                <div>${domLine}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
                $header.replaceWith(tablaHeader);
            }

            const folio = $('#print-folio').text().trim() || 'Solicitud';
            const folioLimpio = folio.replace(/\D/g, '') || 'doc';

            // 4) CSS mínimo (ya no importan las clases del bloque, porque lo reemplazamos)
            const CSS_PDF = `
            * { box-sizing: border-box; }
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #1f2937;
                background: #fff;
                margin: 0;
                padding: 12px;
            }
            table { border-collapse: collapse; width: 100%; }

            /* Tabla de productos */
            .table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 12px; }
            .table th, .table td {
                padding: 5px 6px;
                border: 1px solid #000;
                vertical-align: top;
            }
            .table thead th {
                background: #1f2a37 !important;
                color: #fff !important;
                font-weight: 700;
                text-align: left;
            }
            .table thead th.text-center { text-align: center !important; }
            .table thead th.text-end { text-align: right !important; }
            .table tbody td { text-align: left; }
            .table tbody td.text-center { text-align: center !important; }
            .table tbody td.text-end { text-align: right !important; }

            /* Total */
            .d-flex.justify-content-end { text-align: right; margin-bottom: 16px; }
            .d-flex.justify-content-end table { width: 220px; margin-left: auto; }
            .d-flex.justify-content-end td { padding: 4px 6px; }
            .fw-bold { font-weight: 700 !important; }
            .fs-6 { font-size: 14px !important; }
            .text-end { text-align: right !important; }

            /* Firmas */
            .mt-5 { margin-top: 50px; }
            .row { display: table; width: 100%; }
            .row > .col-4 {
                display: table-cell;
                width: 33.333%;
                text-align: center;
                padding: 0 10px;
                vertical-align: top;
            }
            .row > .col-4 > div {
                border-top: 1px solid #444 !important;
                padding-top: 6px !important;
                font-size: 11px;
                text-transform: uppercase;
                font-weight: 600;
            }

            @page { margin: 10mm; }
        `;

            const html = `
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <style>${CSS_PDF}</style>
            </head>
            <body>${$clon.prop('outerHTML')}</body>
            </html>
        `;

            const resp = await fetch('/cfsistem/app/controllers/correoController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    modo: 'archivos',
                    para: correo,
                    asunto: `Solicitud ${folio} - CF System`,
                    contenido: `<p>Buenas tardes,</p><p>Adjunto encontrará la solicitud <strong>${folio}</strong>.</p><p>Gracias por su preferencia.</p>`,
                    adjuntos: [{ html: html, nombre: `Solicitud_${folioLimpio}.pdf` }]
                })
            });

            const data = await resp.json();
            if (!data.ok) throw new Error(data.error || 'Error al enviar');

            Swal.fire({
                icon: 'success',
                title: '¡Enviado!',
                html: `<p>📬 <strong>${correo}</strong></p>`,
                confirmButtonText: '👍 Aceptar',
                timer: 4000,
                timerProgressBar: true
            });

        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error al enviar',
                html: `<p style="color:#555;">${err.message}</p>`,
                confirmButtonText: 'Cerrar'
            });
        }
    }
</script>