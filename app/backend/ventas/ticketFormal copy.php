<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mostrar_precios = isset($_GET['precios']) ? intval($_GET['precios']) : 1;

if ($id_venta <= 0) die("Error: ID de venta no válido.");

// 1. Datos de la Venta (Cabecera)
$sqlVenta = "SELECT v.*, c.nombre_comercial, c.rfc, c.direccion,c.telefono as telefono, u.nombre as nombre_vendedor, u2.nombre as vendedor,
                    a.nombre as nombre_almacen, a.ubicacion as direccion_almacen
             FROM ventas v
             JOIN clientes c ON v.id_cliente = c.id
             JOIN usuarios u2 ON u2.id=v.vendedor_id
             JOIN usuarios u ON v.usuario_id = u.id
             JOIN almacenes a ON v.almacen_id = a.id
             WHERE v.id = ?";
$stmt = $conexion->prepare($sqlVenta);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

if (!$venta) die("Error: Venta no encontrada.");

// 2. Detalle de Venta
$sqlDetalle = "SELECT dv.*, p.nombre as producto_nombre, p.sku, 
                      p.factor_conversion as factor, p.unidad_reporte, p.unidad_medida, odma.id as odmaIdunidadMedida, odma.nombre as odmaNombre, odma.equivalencia as odmaEquivalencia 
               FROM detalle_venta dv 
               JOIN opciones_de_medida_adicional odma ON odma.id = dv.unidadMedida
               JOIN productos p ON dv.producto_id = p.id 
               WHERE dv.venta_id = ?";
$stmtD = $conexion->prepare($sqlDetalle);
$stmtD->bind_param("i", $id_venta);
$stmtD->execute();
$detalles = $stmtD->get_result();

// 3. Historial de Pagos
$sqlPago = "SELECT h.* FROM historial_pagos h WHERE h.venta_id = ?";
$stmtPago = $conexion->prepare($sqlPago);
$stmtPago->bind_param("i", $id_venta);
$stmtPago->execute();
$detallesPago = $stmtPago->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión Elegante #<?php echo $venta['folio']; ?></title>
      <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* Configuración de Impresión Media Hoja (A5 Horizontal) */
        @page {
           
            margin: 6mm 8mm;
        }
        
        body { 
            text-transform: uppercase!important;
            font-family: 'Segoe UI', Inter, Helvetica, Arial, sans-serif; 
            color: #1e293b; 
            font-size: 9pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        /* Barra de control superior estilizada */
        .no-print {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 14px;
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        .btn-print {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
        }

        .invoice-box {
            max-width: 100%;
            margin: auto;
            position: relative;
        }

        /* Layout Base */
        .table-layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .table-layout td {
            vertical-align: top;
        }

        /* Cabecera Estilo Corporativo */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-title {
            font-size: 16pt;
            font-weight: 800;
            line-height: 1.1;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }
        .company-address {
            font-size: 8pt;
            color: #64748b;
            text-align: center;
            padding: 0 10px;
            line-height: 1.4;
        }

        /* Bloque Destacado de Folio / Fecha */
        .remision-badge {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border-radius: 6px;
            text-align: center;
            padding: 4px;
            font-weight: 700;
            font-size: 9pt;
            width: 150px;
            float: right;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.2);
        }
        .remision-badge span {
            display: block;
            font-size: 12pt;
            font-weight: 800;
            margin-top: 2px;
            color: #f8fafc;
        }
        
        .date-tile {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 100%;
            border-collapse: separate;
            background-color: #f8fafc;
            overflow: hidden;
            margin-top: 4px;
        }
        .date-tile td {
            padding: 6px;
            font-size: 8.5pt;
            text-align: center;
        }
        .date-tile .title-td {
            font-weight: 700;
            background: #e2e8f0;
            color: #334155;
            width: 30%;
        }

        /* Tarjetas de Información */
        .card-info {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 8px;
            padding: 8px;
            min-height: 72px;
            font-size: 8.5pt;
        }
        .card-title {
            font-weight: 700;
            font-size: 8.5pt;
            color: #1e3a8a;
            margin-bottom: 5px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 3px;
            letter-spacing: 0.5px;
        }

        /* Tabla de Contenido Premium */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .items-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 6px 8px;
            font-size: 9pt;
        }
        .items-table td {
            padding: 6px 8px;
            font-size: 8.5pt;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Hilera de Totales */
        .total-row td {
            font-size: 11pt;
            font-weight: 700;
            padding: 8px;
            border-bottom: none;
        }
        .total-highlight {
            background: #f1f5f9;
            color: #1e3a8a;
            border-radius: 4px;
            font-size: 12pt;
            font-weight: 800;
        }

        /* Sección Inferior de Control */
        .card-obs {
            border: 1px solid #e2e8f0;
            background: #fafafa;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 8pt;
            width: 55%;
            margin-top: 10px;
            color: #475569;
        }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .bold { font-weight: bold; }
        
        @media print { 
            .no-print { display: none !important; } 
            .card-info { border: 1px solid #cbd5e1; }
            .items-table { border: 1px solid #cbd5e1; }
        }
    </style>
</head>
<body>

    <div id="contenedor-remision">
        
        <div class="no-print">
            <button class="btn-print" onclick="procesarImpresion()">IMPRIMIR REMISIÓN PREMIUM</button>
        </div>

        <div class="invoice-box">
            
            <table class="table-layout">
                <tr>
                    <td style="width: 32%;">
                        <div class="logo-container">
                            <img src="/cfsistem/public/assets/logo.ico" style="width: 38px; height: auto;" alt="Logo">
                            <div class="brand-title">FORTALEZA<br><span style="font-size:12pt; font-weight:600; color:#0284c7;">CENTRO</span></div>
                        </div>
                    </td>
                    
                    <td style="width: 38%;" class="company-address">
                        <span style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($venta['nombre_almacen']); ?></span><br>
                        <?php echo $venta['direccion_almacen']; ?><br>
                        <span style="font-size: 7.5pt; color: #94a3b8;">Control de Distribución Interna</span>
                    </td>
                    
                    <td style="width: 30%;">
                        <div class="remision-badge">
                            N° REMISIÓN
                            <span><?php echo $venta['folio']; ?></span>
                        </div>
                        <div style="clear: both;"></div>
                        <table class="date-tile">
                            <tr>
                                <td class="title-td">Fecha</td>
                                <td class="bold" style="color: #334155;"><?php echo($venta['fecha']); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="table-layout" style="margin-top: 4px;">
                <tr>
                    <td style="width: 70%; padding-right: 6px;">
                        <div class="card-info">
                            <div class="card-title">VENDIDO A</div>
                            <table style="width:100%; border-collapse:collapse; font-size: 8.5pt;">
                               <tr>
                                    <td style="color:#64748b;"><strong>Nombre:</strong></td>
                                    <td class="bold" style="color:#1e3a8a; font-size:9.5pt;"><?php echo htmlspecialchars($venta['nombre_comercial']); ?></td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;"><strong>Dirección:</strong></td>
                                    <td style="color:#475569; font-size:8pt;"><?php echo htmlspecialchars($venta['direccion']); ?></td>
                                </tr>
                                 <tr>
                                    <td style="width: 18%; color:#64748b;"><strong>Telefono:</strong></td>
                                    <td>#<?php echo $venta['telefono']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    
                    <td style="width: 30%;">
                        <div class="card-info" style="background-color: #f8fafc;">
                            <div class="card-title" style="color:#0284c7;">Informacion reparto</div>
                            <div style="line-height: 1.5; color:#64748b;">
                                <strong>Estado:</strong><?php echo $venta['estado_entrega'] ?><br>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">CÓDIGO</th>
                        <th style="width: 15%;">UNIDAD</th>
                        <th style="width: 43%;">DESCRIPCIÓN DEL PRODUCTO</th>
                        <th class="text-right" style="width: 10%;">CANTIDAD</th>
                        <?php if($mostrar_precios): ?>
                            <th class="text-right" style="width: 10%;">PRECIO U.</th>
                            <th class="text-right" style="width: 10%;">IMPORTE</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($item = $detalles->fetch_assoc()): 
                        $equiv = floatval($item['odmaEquivalencia'] ?? 1);
                        $equiv2 = $equiv >= 1 ? 1 : $equiv;
                        $cantidadReal = $item['cantidad'] * $equiv2;
                    ?>
                    <tr>
                        <td style="font-family: monospace; color: #64748b; font-size: 9pt;"><?php echo !empty($item['sku']) ? htmlspecialchars($item['sku']) : '06020' . $item['producto_id']; ?></td>
                        <td class="bold" style="color: #475569;"><?php echo strtoupper(htmlspecialchars($item['odmaNombre'])); ?></td>
                        <td class="bold" style="color: #0f172a;"><?php echo htmlspecialchars($item['producto_nombre']); ?></td>
                        <td class="text-right bold" style="color: #0f172a;"><?php echo number_format($cantidadReal, 4); ?></td>
                        <?php if($mostrar_precios): ?>
                            <td class="text-right" style="color: #475569;">$<?php echo number_format(($item['precio_unitario']), 2); ?></td>
                            <td class="text-right bold" style="color: #1e3a8a;">$<?php echo number_format(($item['precio_unitario']*$cantidadReal),2); ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                    
                    <?php if($mostrar_precios): ?>
                    <tr class="total-row">
                        <td colspan="4"></td>
                        <td class="text-right" style="color: #475569; font-size: 10pt;">TOTAL MXN</td>
                        <td class="text-right total-highlight">$<?php echo number_format($venta['total'], 2); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="card-obs">
                <div style="font-weight: 700; color: #334155; margin-bottom: 2px; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.3px;">Validación de Operación</div>
                <strong>Cajero Emisor:</strong> <?php echo htmlspecialchars($venta['nombre_vendedor'] ?? 'Alejandro Casales R.'); ?> &nbsp;|&nbsp; 
                <strong>Ejecutivo:</strong> <?php echo htmlspecialchars($venta['vendedor']); ?><br>
                <strong>Observaciones:</strong>
                <?php if(!empty($venta['observaciones'])): ?>
                    <div style="margin-top: 3px; border-top: 1px solid #e2e8f0; padding-top: 2px;">
                         <span style="color:#1e293b;"><?php echo htmlspecialchars($venta['observaciones']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script>
        function procesarImpresion() {
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const elemento = document.getElementById('contenedor-remision');
            const folio = "<?php echo $venta['folio']; ?>";

            if (esMovil) {
                // Configuración para Media Hoja Horizontal (A5 Landscape) en móviles
                const opciones = {
                    margin:       [8, 8, 8, 8],
                    filename:     `Remision_Premium_${folio}.pdf`,
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
                    jsPDF:        { unit: 'mm', format: 'a5', orientation: 'landscape' } // Layout horizontal
                };

                // Ocultar botón de control para que no salga en el PDF
                const controlBoton = elemento.querySelector('.no-print');
                if(controlBoton) controlBoton.style.display = 'none';

                html2pdf().set(opciones).from(elemento).save().then(() => {
                    // Re-mostrar botón de impresión tras guardar
                    if(controlBoton) controlBoton.style.display = 'block';
                });
            } else {
                // Computadora de escritorio: Diálogo de impresión nativo del navegador
                window.print();
            }
        }

        // Activación automática controlada tras renderizar el DOM
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(procesarImpresion, 800);
        });
    </script>
</body>
</html>