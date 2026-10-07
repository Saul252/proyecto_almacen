<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impresión de Ticket</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
    <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">

    <!-- Librería para generación de PDF en dispositivos móviles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm;
            margin: 0 auto;
            padding: 6px 4px;
            color: #000;
            font-size: 11.5px;
            text-transform: uppercase;
            background-color: #fff;
            line-height: 1.35;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        /* ===== ENCABEZADO ELEGANTE ===== */
        .ticket-header {
            text-align: center;
            padding-bottom: 6px;
        }

        .ticket-header .logo-wrap {
            display: inline-block;
            padding: 4px 8px;
            border: 2px solid #000;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .ticket-header .logo-wrap img {
            display: block;
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .ticket-header .almacen {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 3px 0 2px;
        }

        .ticket-header .direccion {
            font-size: 10.5px;
            line-height: 1.3;
        }

        .ticket-header .titulo-ticket {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 12px;
            border: 1.5px solid #000;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* ===== DIVISORES ===== */
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .divider-double {
            border-top: 3px double #000;
            margin: 6px 0;
        }

        /* ===== INFO BLOQUE ===== */
        .info-block {
            font-size: 11px;
            padding: 2px 0;
        }

        .info-block .row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
            padding: 1.5px 0;
        }

        .info-block .label {
            font-weight: bold;
            white-space: nowrap;
        }

        .info-block .value {
            text-align: right;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ===== TABLA DE ITEMS ===== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            font-size: 10.5px;
            letter-spacing: 0.5px;
            padding: 3px 0;
            border-bottom: 1.5px solid #000;
            border-top: 1.5px solid #000;
        }

        .item-row td {
            padding: 5px 0;
            vertical-align: top;
            border-bottom: 1px dotted #999;
        }

        .item-row:last-child td {
            border-bottom: none;
        }

        .item-name {
            font-weight: bold;
            font-size: 11.5px;
            line-height: 1.3;
            word-break: break-word;
        }

        .item-qty {
            font-size: 10.5px;
            margin-top: 2px;
        }

        .item-qty .qty-badge {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 1px 6px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 10px;
        }

        .item-price {
            text-align: right;
            font-weight: bold;
            font-size: 11.5px;
            white-space: nowrap;
        }

        .item-price .unit {
            display: block;
            font-size: 9.5px;
            font-weight: normal;
            color: #333;
            margin-top: 1px;
        }

        /* ===== TOTALES ===== */
        .totales-box {
            margin-top: 6px;
            padding: 6px 8px;
            border: 2px solid #000;
            border-radius: 6px;
            background: #f5f5f5;
        }

        .totales-box .total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .totales-box .estado {
            text-align: right;
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        /* ===== PAGOS ===== */
        .pagos-wrap {
            margin-top: 8px;
            font-size: 10.5px;
        }

        .pago-card {
            border: 1px solid #000;
            border-radius: 5px;
            padding: 5px 7px;
            margin-bottom: 5px;
            background: #fafafa;
        }

        .pago-card .pago-titulo {
            font-weight: bold;
            font-size: 10px;
            letter-spacing: 1px;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }

        .pago-card .pago-linea {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
        }

        .pago-card .pago-linea .lbl {
            font-weight: bold;
        }

        .pago-card .pago-linea .val {
            text-align: right;
        }

        /* ===== FIRMA ===== */
        .firma {
            margin-top: 22px;
            text-align: center;
            font-size: 10px;
        }

        .firma .linea {
            border-top: 1px solid #000;
            width: 75%;
            margin: 0 auto 3px;
        }

        /* ===== PIE ===== */
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10.5px;
        }

        .footer .gracias {
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 1.5px;
            margin-top: 4px;
        }

        .footer .vendedor {
            margin-top: 6px;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }

        /* ===== BOTONES ===== */
        .btn-imprimir {
            padding: 12px;
            width: 100%;
            background-color: #000;
            color: #fff;
            border: none;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            transition: opacity 0.2s;
        }

        .btn-imprimir:hover {
            opacity: 0.85;
        }

        #cargando {
            text-align: center;
            padding: 20px;
            font-weight: bold;
        }

        /* Marca de agua */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 180px;
            opacity: 0.06;
            z-index: 0;
            pointer-events: none;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                width: 72mm;
            }
        }
    </style>
</head>

<body>

    <!-- Marca de agua global -->
    <img src="/myvet/public/assets/logo.ico" class="watermark" alt="">

    <div class="no-print text-center"
        style="margin-bottom: 20px; padding: 10px; background-color: #eee; position: relative; z-index: 10;">
        <button class="btn-imprimir" onclick="procesarImpresion()">🖨️ IMPRIMIR TICKET / GENERAR PDF</button>
        <button class="btn-imprimir" style="margin-top:10px; background-color:#0d6efd;"
            onclick="enviarTicketPorCorreo()" id="btn-enviar-correo">
            📧 ENVIAR POR CORREO
        </button>
    </div>

    <!-- Indicador de Carga -->
    <div id="cargando">CARGANDO DATOS DEL TICKET...</div>

    <!-- Contenedor Principal del Ticket -->
    <div id="contenedor-ticket" style="display: none; position: relative; z-index: 1;">

        <!-- ENCABEZADO -->
        <div class="ticket-header">
            <div class="logo-wrap">
                <img src="/myvet/public/assets/logo.ico" alt="Logo">
            </div>
            <div class="almacen" id="almacen-nombre"></div>
            <div class="direccion" id="almacen-direccion"></div>
            <div class="titulo-ticket" id="ticket-titulo">TICKET DE VENTA</div>
        </div>

        <div class="divider-double"></div>

        <!-- INFO GENERAL -->
        <div class="info-block">
            <div class="row">
                <span class="label">FOLIO:</span>
                <span class="value" id="ticket-folio"></span>
            </div>
            <div class="row">
                <span class="label">FECHA:</span>
                <span class="value" id="ticket-fecha"></span>
            </div>
            <div class="row">
                <span class="label">CLIENTE:</span>
                <span class="value" id="ticket-cliente"></span>
            </div>
            <div class="row">
                <span class="label">NOTAS:</span>
                <span class="value" id="ticket-notas"></span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- TABLA DE ITEMS -->
        <table>
            <thead>
                <tr>
                    <th align="left">DESCRIPCIÓN</th>
                    <th align="right" class="col-subtotal">IMPORTE</th>
                </tr>
            </thead>
            <tbody id="tabla-detalles">
                <!-- Contenido dinámico -->
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- TOTALES -->
        <div id="seccion-totales">
            <div class="totales-box">
                <div class="total-row">
                    <span>TOTAL</span>
                    <span id="ticket-total"></span>
                </div>
            </div>

            <div class="pagos-wrap" id="tabla-pagos">
                <!-- Contenido de pagos generado dinámicamente -->
            </div>
        </div>

        <!-- FIRMA -->
        <div class="firma">
            <div class="linea"></div>
            FIRMA DE RECIBIDO
        </div>

        <!-- PIE -->
        <div class="footer">
            <div class="vendedor">
                Vendedor: <span id="ticket-vendedor" class="bold"></span>
            </div>
            <div class="gracias">¡GRACIAS POR SU COMPRA!</div>
        </div>
    </div>

    <script>
        // ============================================
        // CONFIGURACIÓN GLOBAL DE SWEETALERT
        // ============================================
        const swalCF = Swal.mixin({
            customClass: {
                popup: 'swal-cf-popup',
                confirmButton: 'swal-cf-confirm',
                cancelButton: 'swal-cf-cancel'
            },
            buttonsStyling: false
        });

        const urlParams = new URLSearchParams(window.location.search);
        const idVenta = urlParams.get('id') || urlParams.get('id_venta') || 0;
        const mostrarPrecios = urlParams.get('precios') !== '0';

        document.addEventListener('DOMContentLoaded', () => {
            if (!idVenta || idVenta === '0') {
                document.getElementById('cargando').innerText = 'ERROR: ID DE VENTA NO PROPORCIONADO EN LA URL.';
                return;
            }
            cargarDatosTicket();
        });

        async function cargarDatosTicket() {
            try {
                const response = await fetch(`/myvet/app/controllers/ticketController.php?action=obtenerTicketData&id_venta=${idVenta}`);

                if (!response.ok) {
                    throw new Error(`Error en el servidor (${response.status})`);
                }

                const res = await response.json();

                if (!res.success) {
                    throw new Error(res.message || 'Respuesta no válida del servidor');
                }

                renderizarTicket(res.data);
            } catch (error) {
                document.getElementById('cargando').innerText = 'ERROR: ' + error.message;
            }
        }

        function renderizarTicket(data) {
            const { venta, detalles, pagos } = data;

            document.getElementById('almacen-nombre').innerText = (venta.nombre_almacen || '').toUpperCase();
            document.getElementById('almacen-direccion').innerText = venta.direccion_almacen || '';
            document.getElementById('ticket-titulo').innerText = mostrarPrecios ? 'TICKET DE VENTA' : 'VALE DE ENTREGA';
            document.getElementById('ticket-folio').innerText = venta.folio || '';
            document.getElementById('ticket-fecha').innerText = formatearFecha(venta.fecha);
            document.getElementById('ticket-cliente').innerText = (venta.nombre_comercial || '').substring(0, 30);
            document.getElementById('ticket-notas').innerText = (venta.observaciones || '').substring(0, 30);
            document.getElementById('ticket-vendedor').innerText = venta.vendedor || venta.nombre_vendedor || '';

            if (!mostrarPrecios) {
                document.querySelectorAll('.col-subtotal').forEach(el => el.style.display = 'none');
                document.getElementById('seccion-totales').style.display = 'none';
            }

            const tbody = document.getElementById('tabla-detalles');
            tbody.innerHTML = '';

            detalles.forEach(item => {
                // ============================================
                // CÁLCULO DE EQUIVALENCIA (INTACTO)
                // ============================================
                const cantidadOriginal = parseFloat(item.cantidad) || 0;
                const equiv = 1 / parseFloat(item.odmaEquivalencia) || 0;
                let cantidadReal = cantidadOriginal;

                if (equiv > 0) {
                    cantidadReal = Math.round(cantidadOriginal / equiv);
                }

                let rowHtml = `
                    <tr class="item-row">
                        <td>
                            <div class="item-name">${item.producto_nombre}</div>
                            <div class="item-qty">
                                Cant: <span class="qty-badge">${cantidadReal} ${item.odmaNombre || ''}</span>
                            </div>
                        </td>
                `;

                if (mostrarPrecios) {
                    rowHtml += `
                        <td class="item-price">
                            $${parseFloat(item.subtotal || 0).toFixed(2)}
                            <span class="unit">$${parseFloat(item.precio_unitario || 0).toFixed(2)} c/u</span>
                        </td>
                    `;
                }

                rowHtml += `</tr>`;
                tbody.insertAdjacentHTML('beforeend', rowHtml);
            });

            if (mostrarPrecios) {
                // Total
                document.getElementById('ticket-total').innerText =
                    `$${parseFloat(venta.total || venta.subtotal || 0).toFixed(2)}`;

                // Pagos
                const tablaPagos = document.getElementById('tabla-pagos');
                tablaPagos.innerHTML = '';

                pagos.forEach((pago, idx) => {
                    let pagoHtml = `
                        <div class="pago-card">
                            <div class="pago-titulo">
                                <span>PAGO ${idx + 1}</span>
                                <span>${pago.metodo_pago || ''}</span>
                            </div>
                            <div class="pago-linea">
                                <span class="lbl">Monto:</span>
                                <span class="val">$${parseFloat(pago.monto || 0).toFixed(2)}</span>
                            </div>
                    `;

                    if ((pago.metodo_pago || '').toLowerCase() === 'efectivo' && parseFloat(pago.efectivoPagado) > 0) {
                        const efectivo = parseFloat(pago.efectivoPagado);
                        const monto = parseFloat(pago.monto);
                        const cambio = efectivo - monto;

                        pagoHtml += `
                            <div class="pago-linea">
                                <span class="lbl">Caja:</span>
                                <span class="val">Caja Rápida</span>
                            </div>
                            <div class="pago-linea">
                                <span class="lbl">Efectivo recibido:</span>
                                <span class="val">$${efectivo.toFixed(2)}</span>
                            </div>
                            <div class="pago-linea">
                                <span class="lbl">Cambio:</span>
                                <span class="val">$${cambio.toFixed(2)}</span>
                            </div>
                        `;
                    }

                    pagoHtml += `</div>`;
                    tablaPagos.insertAdjacentHTML('beforeend', pagoHtml);
                });
            }

            document.getElementById('cargando').style.display = 'none';
            document.getElementById('contenedor-ticket').style.display = 'block';

            setTimeout(procesarImpresion, 500);
        }

        function formatearFecha(fechaCadena) {
            if (!fechaCadena) return '';
            const fecha = new Date(fechaCadena);
            if (isNaN(fecha.getTime())) return fechaCadena;
            const d = String(fecha.getDate()).padStart(2, '0');
            const m = String(fecha.getMonth() + 1).padStart(2, '0');
            const y = fecha.getFullYear();
            const h = String(fecha.getHours()).padStart(2, '0');
            const min = String(fecha.getMinutes()).padStart(2, '0');
            return `${d}/${m}/${y} ${h}:${min}`;
        }

        function procesarImpresion() {
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const elemento = document.getElementById('contenedor-ticket');

            if (esMovil) {
                const opciones = {
                    margin: [4, 4, 4, 4],
                    filename: `Ticket_${idVenta}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 3, useCORS: true, letterRendering: true },
                    jsPDF: { unit: 'mm', format: [80, 297], orientation: 'portrait' }
                };

                const botonControl = document.querySelector('.no-print');
                if (botonControl) botonControl.style.display = 'none';

                html2pdf().set(opciones).from(elemento).save().then(() => {
                    if (botonControl) botonControl.style.display = 'block';
                });
            } else {
                window.print();
            }
        }

        // ============================================
        // ENVIAR TICKET POR CORREO (con SweetAlert)
        // ============================================
        async function enviarTicketPorCorreo() {
            const contenedor = document.getElementById('contenedor-ticket');
            if (!contenedor || contenedor.style.display === 'none') {
                swalCF.fire({
                    icon: 'warning',
                    title: 'Ticket no listo',
                    text: 'Espera a que el ticket termine de cargar antes de enviarlo.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            const folio = document.getElementById('ticket-folio').innerText || 'S/N';
            const cliente = document.getElementById('ticket-cliente').innerText || 'Cliente';
            const total = document.getElementById('ticket-total').innerText || '';
            const fecha = document.getElementById('ticket-fecha').innerText || '';
            const almacen = document.getElementById('almacen-nombre').innerText || 'CF System';

            const correoPorDefecto = 'saulenriquealbatapia252@gmail.com';

            const { value: correoDestino } = await swalCF.fire({
                title: '📧 Enviar ticket por correo',
                html: `
            <div style="text-align:left; font-size:13px; line-height:1.7; color:#475569; margin-bottom:14px;">
                <p style="margin:0 0 4px;">🎫 <strong>Folio:</strong> ${folio}</p>
                <p style="margin:0 0 4px;">👤 <strong>Cliente:</strong> ${cliente}</p>
                <p style="margin:0 0 4px;">🏬 <strong>Almacén:</strong> ${almacen}</p>
                ${total ? `<p style="margin:0;">💰 <strong>Total:</strong> ${total}</p>` : ''}
            </div>
            <input id="swal-correo" class="swal2-input" type="email"
                   placeholder="correo@ejemplo.com"
                   value="${correoPorDefecto}"
                   style="width:90%; font-size:14px; margin:0 auto;">
        `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '📨 Enviar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    const input = document.getElementById('swal-correo');
                    input.focus();
                    input.select();
                },
                preConfirm: () => {
                    const valor = document.getElementById('swal-correo').value.trim();
                    if (!valor || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) {
                        Swal.showValidationMessage('Ingresa un correo válido');
                        return false;
                    }
                    return valor;
                }
            });

            if (!correoDestino) return;

            swalCF.fire({
                title: 'Enviando correo...',
                html: 'Por favor espera un momento ⏳',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const titulo = `Ticket ${folio} - ${almacen}`;
                const descripcion =
                    `Buenas tardes ${cliente},\n\n` +
                    `Por este medio le enviamos su ticket de compra con folio ${folio} ` +
                    `con fecha ${fecha}.\n\n` +
                    (total ? `Total: ${total}\n\n` : '') +
                    `Gracias por su preferencia.`;

                const htmlTicket = `
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <style>
                    @page { margin: 0; }
                    * { box-sizing: border-box; }
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        width: 72mm;
                        margin: 0 auto;
                        padding: 6px 4px;
                        color: #000;
                        font-size: 11.5px;
                        text-transform: uppercase;
                        background: #fff;
                        line-height: 1.35;
                    }
                    .text-center { text-align: center; }
                    .text-right  { text-align: right; }
                    .bold        { font-weight: bold; }
                    .ticket-header { text-align: center; padding-bottom: 6px; }
                    .ticket-header .logo-wrap {
                        display: inline-block; padding: 4px 8px;
                        border: 2px solid #000; border-radius: 6px; margin-bottom: 5px;
                    }
                    .ticket-header .logo-wrap img {
                        display: block; width: 42px; height: 42px; object-fit: contain;
                    }
                    .ticket-header .almacen {
                        font-size: 14px; font-weight: bold; letter-spacing: 1px; margin: 3px 0 2px;
                    }
                    .ticket-header .direccion { font-size: 10.5px; line-height: 1.3; }
                    .ticket-header .titulo-ticket {
                        display: inline-block; margin-top: 6px; padding: 3px 12px;
                        border: 1.5px solid #000; border-radius: 20px;
                        font-size: 10.5px; font-weight: bold; letter-spacing: 2px;
                    }
                    .divider { border-top: 1px dashed #000; margin: 6px 0; }
                    .divider-double { border-top: 3px double #000; margin: 6px 0; }
                    .info-block { font-size: 11px; padding: 2px 0; }
                    .info-block .row {
                        display: flex; justify-content: space-between; gap: 6px; padding: 1.5px 0;
                    }
                    .info-block .label { font-weight: bold; white-space: nowrap; }
                    .info-block .value {
                        text-align: right; flex: 1; overflow: hidden;
                        text-overflow: ellipsis; white-space: nowrap;
                    }
                    table { width: 100%; border-collapse: collapse; }
                    thead th {
                        font-size: 10.5px; letter-spacing: 0.5px; padding: 3px 0;
                        border-bottom: 1.5px solid #000; border-top: 1.5px solid #000;
                    }
                    .item-row td {
                        padding: 5px 0; vertical-align: top; border-bottom: 1px dotted #999;
                    }
                    .item-row:last-child td { border-bottom: none; }
                    .item-name { font-weight: bold; font-size: 11.5px; line-height: 1.3; word-break: break-word; }
                    .item-qty { font-size: 10.5px; margin-top: 2px; }
                    .item-qty .qty-badge {
                        display: inline-block; background: #000; color: #fff;
                        padding: 1px 6px; border-radius: 8px; font-weight: bold; font-size: 10px;
                    }
                    .item-price {
                        text-align: right; font-weight: bold; font-size: 11.5px; white-space: nowrap;
                    }
                    .item-price .unit {
                        display: block; font-size: 9.5px; font-weight: normal; color: #333; margin-top: 1px;
                    }
                    .totales-box {
                        margin-top: 6px; padding: 6px 8px; border: 2px solid #000;
                        border-radius: 6px; background: #f5f5f5;
                    }
                    .totales-box .total-row {
                        display: flex; justify-content: space-between; align-items: baseline;
                        font-size: 15px; font-weight: bold; letter-spacing: 1px;
                    }
                    .pagos-wrap { margin-top: 8px; font-size: 10.5px; }
                    .pago-card {
                        border: 1px solid #000; border-radius: 5px; padding: 5px 7px;
                        margin-bottom: 5px; background: #fafafa;
                    }
                    .pago-card .pago-titulo {
                        font-weight: bold; font-size: 10px; letter-spacing: 1px;
                        border-bottom: 1px dashed #000; padding-bottom: 2px;
                        margin-bottom: 3px; display: flex; justify-content: space-between;
                    }
                    .pago-card .pago-linea {
                        display: flex; justify-content: space-between; padding: 1px 0;
                    }
                    .pago-card .pago-linea .lbl { font-weight: bold; }
                    .pago-card .pago-linea .val { text-align: right; }
                    .firma { margin-top: 22px; text-align: center; font-size: 10px; }
                    .firma .linea { border-top: 1px solid #000; width: 75%; margin: 0 auto 3px; }
                    .footer { margin-top: 10px; text-align: center; font-size: 10.5px; }
                    .footer .gracias { font-weight: bold; font-size: 12px; letter-spacing: 1.5px; margin-top: 4px; }
                    .footer .vendedor { margin-top: 6px; padding-top: 5px; border-top: 1px dashed #000; }
                </style>
            </head>
            <body>${contenedor.innerHTML}</body>
            </html>
        `;

                const resultado = await enviarCorreo({
                    correo: correoDestino,
                    titulo: titulo,
                    descripcion: descripcion,
                    nombreDocumento: `Ticket_${folio}.pdf`,
                    htmlDocumento: htmlTicket,
                    urlBackend: '/myvet/app/controllers/correoController.php'
                });

                swalCF.fire({
                    icon: 'success',
                    title: '¡Correo enviado!',
                    html: `
                <div style="text-align:left; font-size:14px; line-height:1.8;">
                    <p>📬 <strong>Destinatario:</strong><br>${correoDestino}</p>
                    <p>📎 <strong>Adjunto:</strong> ${resultado.conAdjunto ? 'Sí (' + folio + '.pdf)' : 'No'}</p>
                    <p style="color:#16a34a; font-weight:600; margin-top:10px;">
                        ${resultado.mensaje}
                    </p>
                </div>
            `,
                    confirmButtonText: '👍 Aceptar',
                    timer: 5000,
                    timerProgressBar: true
                });

            } catch (err) {
                swalCF.fire({
                    icon: 'error',
                    title: 'Error al enviar',
                    html: `
                <p style="color:#555; font-size:14px;">
                    No se pudo enviar el correo.<br>
                    <strong style="color:#dc2626;">${err.message}</strong>
                </p>
            `,
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

            let htmlFinal = htmlDocumento;
            if (!htmlFinal) {
                const contenedor = document.getElementById('documento-terminos');
                if (contenedor && contenedor.innerHTML.trim().length > 0) {
                    htmlFinal = contenedor.innerHTML;
                }
            }
            const documentoExiste = typeof htmlFinal === 'string' && htmlFinal.trim().length > 0;

            const cuerpoHtml = `
                <div style="font-family: Arial, sans-serif; color:#333; max-width:600px; margin:auto;">
                    <div style="background:#1e293b; color:#fff; padding:20px; text-align:center; border-radius:8px 8px 0 0;">
                        <h2 style="margin:0;">${escaparHtml(titulo)}</h2>
                    </div>
                    <div style="padding:20px; background:#f8f9fa; border:1px solid #e5e7eb; border-top:none; border-radius:0 0 8px 8px;">
                        <p style="white-space:pre-line; line-height:1.6;">${escaparHtml(descripcion)}</p>
                        ${documentoExiste
                    ? `<p style="margin-top:20px; color:#0d6efd;">
                                   📎 Se adjunta: <strong>${escaparHtml(nombreDocumento)}</strong>
                               </p>`
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
                    ? [{ html: htmlFinal, nombre: nombreDocumento }]
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

        function escaparHtml(texto) {
            return String(texto)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>

    <!-- Estilos personalizados para SweetAlert2 -->
    <style>
        .swal-cf-popup {
            font-family: 'Segoe UI', Arial, sans-serif;
            border-radius: 14px !important;
            padding: 24px !important;
        }

        .swal-cf-confirm {
            background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 22px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin: 0 6px !important;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .swal-cf-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
        }

        .swal-cf-cancel {
            background: #e5e7eb !important;
            color: #374151 !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 22px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin: 0 6px !important;
            cursor: pointer;
            transition: background 0.15s;
        }

        .swal-cf-cancel:hover {
            background: #d1d5db !important;
        }

        .swal2-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }

        .swal2-html-container {
            font-size: 14px !important;
        }

        .swal2-input {
            border-radius: 8px !important;
            border: 2px solid #e5e7eb !important;
            font-size: 14px !important;
            padding: 10px 12px !important;
        }

        .swal2-input:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
            outline: none !important;
        }
    </style>
</body>

</html>