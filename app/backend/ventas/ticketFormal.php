<?php
session_start();
$nombreAlmacen = $_SESSION['nombre_almacen'] ?? 'Mi Sistema';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión / Ticket de Venta</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
    <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">

    <!-- Hoja de estilos externa -->
    <link rel="stylesheet" href="remision.css">

    <!-- Librería para generación de PDF en dispositivos móviles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body>
    <style>
        /* ============================================
   REMISIÓN — ESTILO GRIS PREMIUM
   ============================================ */

        @page {
            margin: 6mm 8mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Inter, Helvetica, Arial, sans-serif;
            color: #2b2f36;
            background: #ffffff;
            font-size: 9pt;
            line-height: 1.4;
            text-transform: uppercase;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ---------- Barra de control ---------- */
        .no-print {
            background: linear-gradient(135deg, #2b2f36 0%, #1a1d22 100%);
            padding: 16px;
            text-align: center;
            border-bottom: 3px solid #8b929c;
            margin-bottom: 18px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }

        .btn-print {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: #fff;
            border: none;
            padding: 12px 32px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.2px;
            border-radius: 4px;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 3px 10px rgba(107, 114, 128, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(107, 114, 128, 0.55);
        }

        /* ---------- Contenedor principal ---------- */
        .invoice-box {
            max-width: 100%;
            margin: auto;
            position: relative;
        }

        /* ---------- Cabecera gris ---------- */
        .head {
            display: flex;
            align-items: stretch;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-left: 6px solid #4b5563;
            border-radius: 4px;
            padding: 14px 18px;
            gap: 18px;
            margin-bottom: 14px;
        }

        .head-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            min-width: 68px;
        }

        .head-logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            display: block;
        }

        .head-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .head-title {
            font-size: 15pt;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: 1.5px;
            line-height: 1.1;
        }

        .head-subtitle {
            font-size: 8pt;
            color: #6b7280;
            letter-spacing: 3px;
            font-weight: 600;
            margin-top: 3px;
        }

        .head-meta {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: right;
            min-width: 190px;
            gap: 4px;
        }

        .meta-folio {
            background: #1f2937;
            color: #f9fafb;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
            letter-spacing: 1.5px;
        }

        .meta-folio .num {
            display: block;
            font-size: 13pt;
            font-weight: 900;
            color: #ffffff;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        .meta-fecha {
            font-size: 8pt;
            color: #4b5563;
            font-weight: 700;
            letter-spacing: 0.8px;
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #ffffff;
        }

        /* ---------- Grid de paneles ---------- */
        .grid-2 {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .panel {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 12px 14px;
            background: #ffffff;
            position: relative;
        }

        .panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #6b7280;
            border-radius: 4px 0 0 4px;
        }

        .panel-title {
            font-size: 8pt;
            font-weight: 800;
            color: #374151;
            letter-spacing: 2px;
            padding-left: 10px;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #d1d5db;
        }

        .panel-row {
            display: flex;
            padding: 3px 0 3px 10px;
            font-size: 8.5pt;
            line-height: 1.5;
        }

        .panel-row .k {
            color: #6b7280;
            font-weight: 600;
            min-width: 70px;
            letter-spacing: 0.5px;
        }

        .panel-row .v {
            color: #1f2937;
            font-weight: 700;
            flex: 1;
            word-break: break-word;
        }

        .panel-row .v.strong {
            color: #111827;
            font-size: 9.5pt;
        }

        /* ---------- Tabla de productos ---------- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #9ca3af;
            margin-bottom: 12px;
        }

        .items-table thead th {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            color: #f9fafb;
            font-weight: 700;
            padding: 9px 10px;
            font-size: 8pt;
            text-align: left;
            letter-spacing: 1.2px;
        }

        .items-table tbody td {
            padding: 8px 10px;
            font-size: 8.5pt;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .total-row td {
            padding: 10px;
            font-size: 10pt;
            font-weight: 800;
            background: #f3f4f6;
            border-top: 2px solid #4b5563;
            border-bottom: none !important;
        }

        .total-highlight {
            background: #1f2937 !important;
            color: #ffffff !important;
            border-radius: 4px;
            padding: 8px 14px !important;
            font-size: 12pt;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        /* ---------- Sección validación ---------- */
        .card-obs {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: linear-gradient(135deg, #fafafa 0%, #f3f4f6 100%);
            padding: 12px 16px;
            position: relative;
        }

        .card-obs::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #9ca3af;
            border-radius: 4px 0 0 4px;
        }

        .card-obs .obs-title {
            font-size: 8pt;
            font-weight: 800;
            color: #374151;
            letter-spacing: 2px;
            padding-left: 10px;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #cbd5e1;
        }

        .card-obs .obs-body {
            padding-left: 10px;
            font-size: 8pt;
            color: #4b5563;
            line-height: 1.6;
        }

        .card-obs .obs-body strong {
            color: #6b7280;
            letter-spacing: 0.5px;
        }

        .card-obs .obs-body span {
            color: #1f2937;
            font-weight: 700;
        }

        .card-obs .obs-notas {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            color: #374151;
            line-height: 1.5;
        }

        /* ---------- Loader ---------- */
        #cargando {
            text-align: center;
            padding: 50px 20px;
            font-weight: 700;
            font-size: 13px;
            color: #4b5563;
            letter-spacing: 2px;
        }

        /* ---------- Utilidades ---------- */
        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .bold {
            font-weight: bold;
        }

        /* ---------- Impresión ---------- */
        @media print {
            .no-print {
                display: none !important;
            }

            .head {
                background: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table thead th {
                background: #374151 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .total-highlight,
            .meta-folio {
                background: #1f2937 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* ============================================
   LEYENDAS LEGALES
   ============================================ */
        .legal-block {
            margin-top: 14px;
            padding: 12px 16px;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-left: 4px solid #6b7280;
            border-radius: 4px;
            font-size: 7.5pt;
            color: #4b5563;
            line-height: 1.55;
            text-transform: none;
        }

        .legal-title {
            font-size: 7.5pt;
            font-weight: 800;
            color: #374151;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #cbd5e1;
        }

        .legal-text {
            margin: 4px 0;
            text-align: justify;
        }

        /* ============================================
   BLOQUE DE FIRMAS
   ============================================ */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 28px;
            padding: 0 20px;
        }

        .sign-block {
            text-align: center;
        }

        .sign-line {
            border-top: 1.5px solid #374151;
            margin-bottom: 6px;
        }

        .sign-label {
            font-size: 8pt;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .sign-sub {
            font-size: 7pt;
            color: #9ca3af;
            letter-spacing: 1px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        /* ============================================
   IMPRESIÓN — firma siempre visible
   ============================================ */
        @media print {
            .legal-block {
                background: #f9fafb !important;
                border-left: 4px solid #6b7280 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>


    <div class="no-print">
        <button class="btn-print" onclick="procesarImpresion()">IMPRIMIR REMISIÓN</button>
    </div>

    <!-- Indicador de Carga -->
    <div id="cargando">CARGANDO DATOS DE LA REMISIÓN...</div>

    <!-- Contenedor Principal -->
    <div id="contenedor-remision" style="display: none;">
        <div class="invoice-box">

            <!-- CABECERA -->
            <header class="head">
                <div class="head-logo">
                    <img src="/myvet/public/assets/logo.ico" alt="Logo">
                </div>

                <div class="head-info">
                    <div class="head-title">
                        <?= htmlspecialchars($nombreAlmacen) ?>
                    </div>
                    <div class="head-subtitle">CENTRO DE DISTRIBUCIÓN</div>
                </div>

                <div class="head-meta">
                    <div class="meta-folio">
                        <span id="label-tipo-documento">N° REMISIÓN</span>
                        <span class="num" id="ticket-folio"></span>
                    </div>
                    <div class="meta-fecha" id="ticket-fecha"></div>
                </div>
            </header>

            <!-- CLIENTE + REPARTO -->
            <section class="grid-2">
                <div class="panel">
                    <div class="panel-title">Datos del Cliente</div>
                    <div class="panel-row">
                        <span class="k">Nombre:</span>
                        <span class="v strong" id="ticket-cliente"></span>
                    </div>
                    <div class="panel-row">
                        <span class="k">Dirección:</span>
                        <span class="v" id="ticket-direccion"></span>
                    </div>
                    <div class="panel-row">
                        <span class="k">Teléfono:</span>
                        <span class="v" id="ticket-telefono"></span>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-title">Información Reparto</div>
                    <div class="panel-row">
                        <span class="k">Estado:</span>
                        <span class="v strong" id="ticket-estado-entrega"></span>
                    </div>
                    <div class="panel-row">
                        <span class="k">Almacén:</span>
                        <span class="v" id="almacen-nombre"></span>
                    </div>
                </div>
            </section>

            <!-- TABLA DE PRODUCTOS -->
            <table class="items-table">
                <thead>
                    <tr id="encabezado-tabla">
                        <th style="width: 12%;">CÓDIGO</th>
                        <th style="width: 15%;">UNIDAD</th>
                        <th style="width: 43%;">DESCRIPCIÓN DEL PRODUCTO</th>
                        <th class="text-right" style="width: 10%;">CANTIDAD</th>
                        <th class="text-right col-precios" style="width: 10%;">PRECIO U.</th>
                        <th class="text-right col-precios" style="width: 10%;">IMPORTE</th>
                    </tr>
                </thead>
                <tbody id="tabla-detalles">
                    <!-- Filas generadas dinámicamente -->
                </tbody>
            </table>

            <!-- VALIDACIÓN DE OPERACIÓN -->
            <div class="card-obs">
                <div class="obs-title">VALIDACIÓN DE OPERACIÓN</div>
                <div class="obs-body">
                    <strong>CAJERO EMISOR:</strong> <span id="ticket-vendedor-emisor"></span>
                    &nbsp;|&nbsp;
                    <strong>EJECUTIVO:</strong> <span id="ticket-vendedor"></span>
                    <div class="obs-notas">
                        <strong>OBSERVACIONES:</strong>
                        <span id="ticket-notas"></span>
                    </div>
                </div>
            </div>

            <!-- ============================================
                 LEYENDAS LEGALES
                 ============================================ -->

            <!-- ============================================
     LEYENDAS LEGALES
     ============================================ -->
            <div class="legal-block">
                <div class="legal-title">AVISO IMPORTANTE</div>

                <p class="legal-text">
                    El presente documento es únicamente una <strong>nota de compra</strong> y
                    hace constar la adquisición del material aquí descrito. El material podrá
                    ser entregado al momento, de forma posterior o por partes, según lo acordado
                    directamente con el emisor.
                </p>

                <p class="legal-text">
                    Las condiciones de entrega, tiempos y modalidad serán gestionadas de palabra
                    y de común acuerdo entre el emisor y el receptor, quienes quedan enterados
                    del contenido y alcance de esta operación.
                </p>

                <p class="legal-text">
                    Una vez recibido el material, <strong>no se aceptan cambios ni devoluciones</strong>.
                    El receptor se compromete a validar cualquier detalle directamente con el
                    emisor al momento de la entrega.
                </p>
            </div>
            <!-- ============================================
                 BLOQUE DE FIRMAS
                 ============================================ -->
            <div class="signatures">
                <div class="sign-block">
                    <div class="sign-line"></div>
                    <div class="sign-label">Emisor</div>
                    <div class="sign-sub">Nombre y Firma</div>
                </div>

                <div class="sign-block">
                    <div class="sign-line"></div>
                    <div class="sign-label">Receptor</div>
                    <div class="sign-sub">Nombre y Firma</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // ============================================
        // CONFIGURACIÓN
        // ============================================
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
            const { venta, detalles } = data;

            // Cabecera e información general
            document.getElementById('almacen-nombre').innerText = (venta.nombre_almacen || '').toUpperCase();
            document.getElementById('label-tipo-documento').innerText = mostrarPrecios ? 'N° REMISIÓN' : 'VALE DE ENTREGA';
            document.getElementById('ticket-folio').innerText = venta.folio || '';
            document.getElementById('ticket-fecha').innerText = 'Fecha: ' + formatearFecha(venta.fecha);

            // Cliente y reparto
            document.getElementById('ticket-cliente').innerText = (venta.nombre_comercial || '').toUpperCase();
            document.getElementById('ticket-direccion').innerText = (venta.direccion || '').toUpperCase();
            document.getElementById('ticket-telefono').innerText = venta.telefono ? `#${venta.telefono}` : 'N/A';
            document.getElementById('ticket-estado-entrega').innerText = (venta.estado_entrega || 'PENDIENTE').toUpperCase();

            // Control inferior
            document.getElementById('ticket-vendedor-emisor').innerText = venta.nombre_vendedor || 'SISTEMA';
            document.getElementById('ticket-vendedor').innerText = venta.vendedor || venta.nombre_vendedor || 'N/A';
            document.getElementById('ticket-notas').innerText = venta.observaciones || 'SIN OBSERVACIONES';

            // Ocultar columnas de precios si aplica
            if (!mostrarPrecios) {
                document.querySelectorAll('.col-precios').forEach(el => el.style.display = 'none');
            }

            // ============================================
            // DETALLES — LÓGICA DE EQUIVALENCIA INTACTA
            // ============================================
            const tbody = document.getElementById('tabla-detalles');
            tbody.innerHTML = '';

            detalles.forEach(item => {
                const equiv = Math.round(parseFloat(item.odmaEquivalencia) || 1);
                const cantidadReal = Math.round(item.cantidad * equiv);
                const sku = item.sku ? item.sku : ('06020' + item.producto_id);
                const precioUnitario = parseFloat(item.precio_unitario || 0);
                const importe = precioUnitario * cantidadReal;

                let rowHtml = `
                    <tr>
                        <td style="font-family: monospace; color: #64748b; font-size: 9pt;">${sku}</td>
                        <td class="bold" style="color: #475569;">${(item.odmaNombre || '').toUpperCase()}</td>
                        <td class="bold" style="color: #0f172a;">${item.producto_nombre}</td>
                        <td class="text-right bold" style="color: #0f172a;">${cantidadReal.toFixed(4)}</td>
                `;

                if (mostrarPrecios) {
                    rowHtml += `
                        <td class="text-right" style="color: #475569;">$${precioUnitario.toFixed(2)}</td>
                        <td class="text-right bold" style="color: #1e3a8a;">$${importe.toFixed(2)}</td>
                    `;
                }

                rowHtml += `</tr>`;
                tbody.insertAdjacentHTML('beforeend', rowHtml);
            });

            // Fila de total
            if (mostrarPrecios) {
                const totalVenta = parseFloat(venta.total || venta.subtotal || 0);
                const totalHtml = `
                    <tr class="total-row">
                        <td colspan="4"></td>
                        <td class="text-right" style="color: #475569; font-size: 10pt;">TOTAL MXN</td>
                        <td class="text-right total-highlight">$${totalVenta.toFixed(2)}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', totalHtml);
            }

            document.getElementById('cargando').style.display = 'none';
            document.getElementById('contenedor-remision').style.display = 'block';

            setTimeout(procesarImpresion, 600);
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
            const elemento = document.getElementById('contenedor-remision');
            const folio = document.getElementById('ticket-folio').innerText || idVenta;

            if (esMovil) {
                const opciones = {
                    margin: [8, 8, 8, 8],
                    filename: `Remision_${folio}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, letterRendering: true },
                    jsPDF: { unit: 'mm', format: 'a5', orientation: 'landscape' }
                };

                const controlBoton = document.querySelector('.no-print');
                if (controlBoton) controlBoton.style.display = 'none';

                html2pdf().set(opciones).from(elemento).save().then(() => {
                    if (controlBoton) controlBoton.style.display = 'block';
                });
            } else {
                window.print();
            }
        }
    </script>
</body>

</html>