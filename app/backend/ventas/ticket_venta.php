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

    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm;
            margin: 0 auto;
            padding: 5px;
            color: #000;
            font-size: 12px;
            text-transform: uppercase;
            background-color: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-row td {
            padding: 5px 0;
            vertical-align: top;
        }

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
        }

        #cargando {
            text-align: center;
            padding: 20px;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print text-center" style="margin-bottom: 20px; padding: 10px; background-color: #eee;">
        <button class="btn-imprimir" onclick="procesarImpresion()">IMPRIMIR TICKET / GENERAR PDF</button>
    </div>

    <!-- Indicador de Carga -->
    <div id="cargando">CARGANDO DATOS DEL TICKET...</div>

    <!-- Contenedor Principal del Ticket -->
    <div id="contenedor-ticket" style="display: none;">
        <div class="text-center">
            <span class="bold" style="font-size: 14px;" id="almacen-nombre"></span><br>
            <span id="almacen-direccion"></span><br>
            <span class="bold" id="ticket-titulo">TICKET DE VENTA</span>
        </div>

        <div class="divider"></div>

        <table>
            <tr>
                <td>FOLIO: <span id="ticket-folio"></span></td>
            </tr>
            <tr>
                <td>FECHA: <span id="ticket-fecha"></span></td>
            </tr>
            <tr>
                <td>CLIENTE: <span id="ticket-cliente"></span></td>
            </tr>
            <tr>
                <td>NOTAS: <span id="ticket-notas"></span></td>
            </tr>
        </table>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th align="left">DESC.</th>
                    <th align="right" class="col-subtotal">SUBT.</th>
                </tr>
            </thead>
            <tbody id="tabla-detalles">
                <!-- Marca de Agua -->
                <img src="/myvet/public/assets/logo.ico" style="
                        position: fixed;
                        top: 19.5%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 180px;
                        opacity: 0.08;
                        z-index: -1;
                    ">
            </tbody>
        </table>

        <div class="divider"></div>

        <div id="seccion-totales">
            <table style="font-size: 14px;">
                <tr class="bold">
                    <td align="right">TOTAL:</td>
                    <td align="right" style="width: 60%;" id="ticket-total"></td>
                </tr>
            </table>

            <table style="font-size:14px; width:100%;" id="tabla-pagos">
                <!-- Contenido de pagos generado dinámicamente -->
            </table>
        </div>

        <div style="margin-top: 30px;" class="text-center">
            <br> __________________________
            <br> FIRMA DE RECIBIDO
        </div>

        <div class="text-center" style="margin-top: 15px;">
            <p>Vendedor: <span id="ticket-vendedor"></span></p>
            <p class="bold">¡GRACIAS POR SU COMPRA!</p>
        </div>
    </div>

    <script>
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
                const cantidadOriginal = parseFloat(item.cantidad) || 0;
                const equiv = 1 / parseFloat(item.odmaEquivalencia) || 0;
                let cantidadReal = cantidadOriginal;
                console.log(cantidadOriginal, equiv);

                if (equiv > 0) {
                    // Realiza la división según la equivalencia recibida y redondea
                    cantidadReal = Math.round(cantidadOriginal / equiv);
                }

                let rowHtml = `
                    <tr class="item-row">
                        <td>
                            <div class="bold" style="font-size:13px;">${item.producto_nombre}</div>
                            <div style="margin-top:3px;font-size:12px;">
                                Cantidad: <span class="bold">${cantidadReal} ${item.odmaNombre || ''}</span>
                            </div>
                        </td>
                `;

                if (mostrarPrecios) {
                    rowHtml += `
                        <td align="right" class="bold">
                            $${parseFloat(item.subtotal || 0).toFixed(2)}<br>
                            ( $${parseFloat(item.precio_unitario || 0).toFixed(2)} X ${item.odmaNombre || ''} )
                        </td>
                    `;
                }

                rowHtml += `</tr>`;
                tbody.insertAdjacentHTML('beforeend', rowHtml);
            });

            if (mostrarPrecios) {
                document.getElementById('ticket-total').innerText = `$${parseFloat(venta.total || venta.subtotal || 0).toFixed(2)} (${venta.estado_pago || ''})`;

                const tablaPagos = document.getElementById('tabla-pagos');
                tablaPagos.innerHTML = '';

                pagos.forEach(pago => {
                    let pagoHtml = `
                        <tr><td colspan="4" style="border-top:1px dashed #000; padding-top:6px;"></td></tr>
                        <tr>
                            <td class="bold" style="padding:4px 0;">Método de pago:</td>
                            <td colspan="3" style="padding:4px 0;">${pago.metodo_pago}</td>
                        </tr>
                        <tr>
                            <td class="bold" style="padding:4px 0;">Total pagado:</td>
                            <td colspan="3" style="padding:4px 0;">$${parseFloat(pago.monto || 0).toFixed(2)}</td>
                        </tr>
                    `;

                    if ((pago.metodo_pago || '').toLowerCase() === 'efectivo' && parseFloat(pago.efectivoPagado) > 0) {
                        const efectivo = parseFloat(pago.efectivoPagado);
                        const monto = parseFloat(pago.monto);
                        const cambio = efectivo - monto;

                        pagoHtml += `
                            <tr>
                                <td class="bold" style="padding:4px 0;">Caja:</td>
                                <td colspan="3" style="padding:4px 0;">Caja Rápida</td>
                            </tr>
                            <tr>
                                <td class="bold" style="padding:4px 0;">Efectivo recibido:</td>
                                <td colspan="3" style="padding:4px 0;">$${efectivo.toFixed(2)}</td>
                            </tr>
                            <tr>
                                <td class="bold" style="padding:4px 0;">Cambio:</td>
                                <td colspan="3" style="padding:4px 0;">$${cambio.toFixed(2)}</td>
                            </tr>
                        `;
                    }

                    pagoHtml += `<tr><td colspan="4" style="border-bottom:1px dashed #000; padding-bottom:6px;"></td></tr>`;
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
    </script>
</body>

</html>