<div class="modal fade" id="modalEnviarCorreo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <div class="modal-header text-white border-0 py-3"
                style="background: linear-gradient(135deg, #0f766e 0%, #10b981 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-paper-fill fs-4"></i>
                    <h5 class="fw-bold mb-0">Enviar comprobante por correo</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">

                <!-- Resumen del comprobante -->
                <div class="mb-3 p-3 rounded-3"
                    style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13px; line-height: 1.8; color: #475569;">
                    <p class="mb-1">🎫 <strong>Folio:</strong> <span id="envio-folio">---</span></p>
                    <p class="mb-1">👤 <strong>Cliente:</strong> <span id="envio-cliente">---</span></p>
                    <p class="mb-1">💰 <strong>Monto:</strong> <span id="envio-monto">---</span></p>
                    <p class="mb-0">💳 <strong>Método:</strong> <span id="envio-metodo">---</span></p>
                </div>

                <!-- Input de correo -->
                <label for="envio-correo" class="form-label fw-semibold text-secondary mb-1">
                    Correo destinatario
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input type="email" class="form-control border-start-0 ps-0" id="envio-correo"
                        placeholder="correo@ejemplo.com" autocomplete="off">
                </div>
                <div class="invalid-feedback d-block mt-1" id="envio-correo-error" style="display:none !important;">
                </div>
            </div>

            <div class="modal-footer border-top-0 px-4 py-3 d-flex justify-content-end gap-2"
                style="background: #f8fafc;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                    data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn rounded-pill px-4 fw-semibold text-white border-0"
                    id="btn-confirmar-envio" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                           box-shadow: 0 4px 12px rgba(16,185,129,.35);">
                    <i class="bi bi-send-fill me-2"></i>Enviar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    // ============================================
    // ENVIAR COMPROBANTE POR CORREO
    // ============================================
    async function enviarComprobantePorCorreo(id) {
        try {
            // 1) Cargar datos del comprobante
            const resp = await fetch(`/myvet/app/controllers/comprobantesPagoController.php?action=obtenerDetalle&id=${id}`);
            const datos = await resp.json();

            if (datos.status !== 'success') {
                Swal.fire('Error', datos.message || 'No se encontraron datos', 'error');
                return;
            }

            const data = datos.data;
            const montoFormateado = parseFloat(data.monto || 0).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });

            const folio = `#${String(data.id).padStart(5, '0')}`;

            // 2) Inyectar datos en el modal de impresión
            $('#print-folio').text(folio);
            $('#print-cliente').text(data.nombre_comercial || '');
            $('#print-almacen').text(data.nombre_almacen || '');
            $('#print-usuario').text(data.usuario || '');
            $('#print-referencia').text(data.referencia || '');
            $('#print-fecha_dep').text(data.fecha || '');
            $('#costo_total').text(montoFormateado);
            $('#metodo_pago_dep').text(data.metodo_pago || '');
            $('#numero_venta').text(data.numero_ventas || '');

            // 3) Rellenar el modal de envío
            $('#envio-folio').text(folio);
            $('#envio-cliente').text(data.nombre_comercial || '---');
            $('#envio-monto').text(montoFormateado);
            $('#envio-metodo').text(data.metodo_pago || '---');

            // Correo por defecto
            const correoPorDefecto = (data.correo && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.correo))
                ? data.correo
                : '';

            $('#envio-correo').val(correoPorDefecto);
            $('#envio-correo-error').hide();
            $('#envio-correo').removeClass('is-invalid');

            // 🔑 Resetear el botón ANTES de mostrar el modal
            const btnOriginal = document.getElementById('btn-confirmar-envio');
            btnOriginal.disabled = false;
            btnOriginal.innerHTML = '<i class="bi bi-send-fill me-2"></i>Enviar';

            // 4) Abrir el modal
            const modalEl = document.getElementById('modalEnviarCorreo');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // 5) Enfocar input al abrir
            modalEl.addEventListener('shown.bs.modal', function onShown() {
                modalEl.removeEventListener('shown.bs.modal', onShown);
                setTimeout(() => {
                    const input = document.getElementById('envio-correo');
                    input.focus();
                    input.select();
                }, 100);
            });

            // 6) Clonar botón para no acumular listeners
            const btnViejo = document.getElementById('btn-confirmar-envio');
            const btnNuevo = btnViejo.cloneNode(true);
            btnViejo.parentNode.replaceChild(btnNuevo, btnViejo);

            // 7) Listener del botón
            btnNuevo.addEventListener('click', async () => {
                const correoDestino = $('#envio-correo').val().trim();

                if (!correoDestino || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correoDestino)) {
                    $('#envio-correo-error').text('Ingresa un correo válido').show();
                    $('#envio-correo').addClass('is-invalid').focus();
                    return;
                }
                $('#envio-correo-error').hide();
                $('#envio-correo').removeClass('is-invalid');

                // Deshabilitar botón y mostrar spinner
                btnNuevo.disabled = true;
                btnNuevo.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

                try {
                    // 8) Clonar el HTML del comprobante
                    const $contenedor = $('#areaImpresion');
                    if (!$contenedor.length) throw new Error('No se encontró el área de impresión');

                    const $clon = $contenedor.clone();
                    $contenedor.find('input, textarea, select').each(function (i) {
                        const $copia = $clon.find('input, textarea, select').eq(i);
                        if (!$copia.length) return;
                        $copia.attr('value', $(this).val());
                        $copia.val($(this).val());
                    });

                    const htmlComprobante = `
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <style>
                                body {
                                    font-family: 'Courier New', monospace;
                                    width: 80mm; margin: 0 auto; padding: 5mm;
                                    color: #000; background: #fff;
                                }
                                table { width: 100%; }
                                img { max-width: 100%; }
                            </style>
                        </head>
                        <body>${$clon.prop('outerHTML')}</body>
                        </html>
                    `;

                    // 9) Enviar al backend
                    const resultado = await enviarCorreo({
                        correo: correoDestino,
                        titulo: `Comprobante de Pago ${folio} - CF System`,
                        descripcion:
                            `Buenas tardes ${data.nombre_comercial},\n\n` +
                            `Por este medio le enviamos su comprobante de pago con folio ${folio} ` +
                            `con fecha ${data.fecha}.\n\n` +
                            `Monto: ${montoFormateado}\n` +
                            `Método de pago: ${data.metodo_pago}\n\n` +
                            `Gracias por su preferencia.`,
                        nombreDocumento: `Comprobante_${String(data.id).padStart(5, '0')}.pdf`,
                        htmlDocumento: htmlComprobante,
                        urlBackend: '/myvet/app/controllers/correoController.php'
                    });

                    // 10) Cerrar modal
                    modal.hide();

                    // 11) Éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Comprobante enviado!',
                        html: `
                            <div style="text-align:left; font-size:14px; line-height:1.8;">
                                <p>📬 <strong>Destinatario:</strong><br>${correoDestino}</p>
                                <p>📎 <strong>Adjunto:</strong> ${resultado.conAdjunto ? 'Sí' : 'No'}</p>
                            </div>
                        `,
                        confirmButtonText: '👍 Aceptar',
                        timer: 5000,
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
                } finally {
                    // 🔑 SIEMPRE restaurar el botón
                    btnNuevo.disabled = false;
                    btnNuevo.innerHTML = '<i class="bi bi-send-fill me-2"></i>Enviar';
                }
            });

            // 12) Enter en el input dispara el envío
            $('#envio-correo').off('keypress').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    btnNuevo.click();
                }
            });

        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `<p style="color:#555;">${err.message}</p>`,
                confirmButtonText: 'Cerrar'
            });
        }
    }

    // ============================================
    // FUNCIÓN GENÉRICA PARA ENVIAR CORREO
    // ============================================
    async function enviarCorreo({
        correo,
        titulo,
        descripcion,
        nombreDocumento = 'Documento.pdf',
        htmlDocumento = null,
        urlBackend = '/myvet/app/controllers/correoController.php',
        remitente = 'CF System'
    }) {
        if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            throw new Error('Correo inválido');
        }
        if (!titulo || !titulo.trim()) {
            throw new Error('El título es obligatorio');
        }
        if (!descripcion || !descripcion.trim()) {
            throw new Error('La descripción es obligatoria');
        }

        const documentoExiste = typeof htmlDocumento === 'string' && htmlDocumento.trim().length > 0;

        const cuerpoHtml = `
            <div style="font-family: Arial, sans-serif; color:#333; max-width:600px; margin:auto;">
                <div style="background:#1e293b; color:#fff; padding:20px; text-align:center; border-radius:8px 8px 0 0;">
                    <h2 style="margin:0;">${escaparHtml(titulo)}</h2>
                </div>
                <div style="padding:20px; background:#f8f9fa; border:1px solid #e5e7eb; border-top:none; border-radius:0 0 8px 8px;">
                    <p style="white-space:pre-line; line-height:1.6;">${escaparHtml(descripcion)}</p>
                    ${documentoExiste
                ? `<p style="margin-top:20px; color:#0d6efd;">📎 Se adjunta: <strong>${escaparHtml(nombreDocumento)}</strong></p>`
                : ''
            }
                    <hr style="margin:25px 0; border:none; border-top:1px solid #ddd;">
                    <p style="font-size:12px; color:#888; text-align:center;">
                        ${escaparHtml(remitente)} &copy; ${new Date().getFullYear()}
                    </p>
                </div>
            </div>
        `;

        const datos = {
            modo: 'archivos',
            para: correo,
            asunto: titulo,
            contenido: cuerpoHtml,
            adjuntos: documentoExiste
                ? [{ html: htmlDocumento, nombre: nombreDocumento }]
                : []
        };

        const respuesta = await fetch(urlBackend, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const data = await respuesta.json();

        if (!data.ok) {
            throw new Error(data.error || 'Error al enviar el correo');
        }

        return {
            enviado: true,
            conAdjunto: documentoExiste,
            mensaje: data.mensaje || 'Correo enviado correctamente'
        };
    }

    // ============================================
    // UTILIDAD ANTI-INYECCIÓN HTML
    // ============================================
    function escaparHtml(texto) {
        return String(texto)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>