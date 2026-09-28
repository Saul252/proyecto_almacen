<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mostrar_precios = isset($_GET['precios']) ? intval($_GET['precios']) : 1;

if ($id_venta <= 0)
    die("Error: ID de venta no válido.");

// 1. Datos de la Venta (Cabecera)
$sqlVenta = "SELECT v.*, c.nombre_comercial, c.rfc, c.direccion, u.nombre as nombre_vendedor, u2.nombre as vendedor,
                    a.nombre as nombre_almacen, a.ubicacion as direccion_almacen
             FROM ventas v
             JOIN clientes c ON v.id_cliente = c.id
             join usuarios u2 on u2.id=v.vendedor_id
             JOIN usuarios u ON v.usuario_id = u.id
             JOIN almacenes a ON v.almacen_id = a.id
             WHERE v.id =?";
$stmt = $conexion->prepare($sqlVenta);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

if (!$venta)
    die("Error: Venta no encontrada.");

// 2. Detalle de Venta (Traemos Factor y Unidad de la tabla Productos)
$sqlDetalle = "SELECT dv.*, p.nombre as producto_nombre, p.sku, 
                      p.factor_conversion as factor, p.unidad_reporte,p.unidad_medida,   odma.id as odmaIdunidadMedida, odma.nombre as odmaNombre, odma.equivalencia as odmaEquivalencia 
               FROM detalle_venta dv 
               
join opciones_de_medida_adicional odma on odma.id= dv.unidadMedida

               JOIN productos p ON dv.producto_id = p.id 
               WHERE dv.venta_id = ?";
$stmtD = $conexion->prepare($sqlDetalle);
$stmtD->bind_param("i", $id_venta);
$stmtD->execute();
$detalles = $stmtD->get_result();

$sqlPago = "SELECT h.* FROM historial_pagos h 
               WHERE h.venta_id = ?";
$stmtPago = $conexion->prepare($sqlPago);
$stmtPago->bind_param("i", $id_venta);
$stmtPago->execute();
$detallesPago = $stmtPago->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo $venta['folio']; ?></title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">


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

        .aclaracion-factor {
            font-size: 11px;
            margin-top: 2px;
            padding-left: 5px;
            border-left: 2px solid #000;
            display: block;
        }

        /* Botón de acción móvil compatible */
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

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div id="contenedor-ticket">

        <div class="no-print text-center" style="margin-bottom: 20px; padding: 10px; background-color: #eee;">
            <button class="btn-imprimir" onclick="procesarImpresion()">IMPRIMIR TICKET / GENERAR</button>
        </div>

        <div class="text-center">
            <span class="bold" style="font-size: 14px;"><?php echo strtoupper($venta['nombre_almacen']); ?></span><br>
            <?php echo $venta['direccion_almacen']; ?><br>
            <span class="bold"><?php echo ($mostrar_precios) ? 'TICKET DE VENTA' : 'VALE DE ENTREGA'; ?></span>
        </div>

        <div class="divider"></div>

        <table>
            <tr>
                <td>FOLIO: <?php echo $venta['folio']; ?></td>
            </tr>
            <tr>
                <td>FECHA: <?php echo date("d/m/Y H:i", strtotime($venta['fecha'])); ?></td>
            </tr>
            <tr>
                <td>CLIENTE: <?php echo substr($venta['nombre_comercial'], 0, 30); ?></td>
            </tr>
            <tr>
                <td>Notas: <?php echo substr($venta['observaciones'], 0, 30); ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th align="left">DESC.</th>
                    <?php if ($mostrar_precios): ?>
                        <th align="right">SUBT.</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <img src="/cfsistem/public/assets/logo.ico" style="
                        position: fixed;
                        top: 19.5%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 180px;
                        opacity: 0.08;
                        z-index: -1;
                    ">
                <?php while ($item = $detalles->fetch_assoc()):
                    $f = ($item['factor'] > 0) ? $item['factor'] : 1;
                    $unidad = $item['unidad_reporte'] ? $item['unidad_reporte'] : $item['unidad_medida'];
                    $equiv = floatval($item['odmaEquivalencia'] ?? 1);
                    $nombreMedida = $item['odmaNombre'];
                    $cantEntera = floor($item['cantidad'] / $f);
                    $cantResto = round(fmod($item['cantidad'], $f), 2);

                    $nombreMedida = $item['odmaNombre'];
                    $cantidadMostrar = $item['cantidad'];
                    $unidadMostrar = $item['unidad_medida'];
                    $equiv = floatval($item['odmaEquivalencia'] ?? 0);

                    if (isset($item['unidadMedida']) && $equiv > 0) {
                        $cantidadConvertida = $item['cantidad'] * $equiv;
                        if ($cantidadConvertida <= 1) {
                            $cantidadMostrar = number_format($cantidadConvertida, 3);
                            $unidadMostrar = $item['odmaNombre'];
                        }
                    }
                    $cantidadMostrarFormateada = number_format($cantidadMostrar, 1);
                    $equiv2 = $equiv >= 1 ? 1 : $equiv;
                    $cantidadReal = $item['cantidad'] * $equiv2;
                    ?>
                    <tr class="item-row">
                        <td>
                            <div class="bold" style="font-size:13px;">
                                <?php echo $item['producto_nombre']; ?>
                            </div>
                            <div style="margin-top:3px;font-size:12px;">
                                Cantidad:
                                <span class="bold">
                                    <?php echo number_format($cantidadReal, 3) . '  ' . $item['odmaNombre'] ?>
                                </span>
                            </div>
                        </td>
                        <?php if ($mostrar_precios): ?>
                            <td align="right" class="bold">
                                $<?php echo number_format($item['subtotal'], 2); ?>
                                <br>
                                <?php echo '( $' . number_format(($item['precio_unitario'])) . ' X ' . $item['odmaNombre'] . ' )'; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <?php if ($mostrar_precios): ?>
            <table style="font-size: 14px;">
                <tr class="bold">
                    <td align="right">TOTAL:</td>
                    <td align="right" style="width: 60%;">
                        $<?php echo number_format($venta['subtotal'], 2) . ' (' . $venta['estado_pago'] . ')'; ?> </td>
                </tr>
            </table>

            <table style="font-size:14px; width:100%;">
                <?php while ($pago = $detallesPago->fetch_assoc()): ?>
                    <tr>
                        <td colspan="4" style="border-top:1px dashed #000; padding-top:6px;"></td>
                    </tr>
                    <tr>
                        <td class="bold" style="padding:4px 0;">Método de pago:</td>
                        <td colspan="3" style="padding:4px 0;"><?php echo htmlspecialchars($pago['metodo_pago']); ?></td>
                    </tr>
                    <tr>
                        <td class="bold" style="padding:4px 0;">Total pagado:</td>
                        <td colspan="3" style="padding:4px 0;">$<?php echo number_format($pago['monto'], 2); ?></td>
                    </tr>

                    <?php if ($pago['metodo_pago'] == 'Efectivo'): ?>
                        <?php if ($pago['efectivoPagado'] > 0): ?>
                            <tr>
                                <td class="bold" style="padding:4px 0;">Caja</td>
                                <td colspan="3" style="padding:4px 0;">Caja Rapida</td>
                            </tr>
                            <tr>
                                <td class="bold" style="padding:4px 0;">Efectivo recibido :</td>
                                <td colspan="3" style="padding:4px 0;">$<?php echo number_format($pago['efectivoPagado'], 2); ?></td>
                            </tr>
                            <tr>
                                <td class="bold" style="padding:4px 0;">Cambio:</td>
                                <td colspan="3" style="padding:4px 0;">
                                    $<?php echo number_format(($pago['efectivoPagado'] - $pago['monto']), 2); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>

                    <tr>
                        <td colspan="4" style="border-bottom:1px dashed #000; padding-bottom:6px;"></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>

        <div style="margin-top: 30px;" class="text-center">
            <br> __________________________
            <br> FIRMA DE RECIBIDO
        </div>

        <div class="text-center" style="margin-top: 15px;">
            <p>Vendedor: <?php echo $venta['vendedor']; ?></p>
            <p class="bold">¡GRACIAS POR SU COMPRA!</p>
        </div>
    </div>

    <script>
        function procesarImpresion() {
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const elemento = document.getElementById('contenedor-ticket');
            const folio = "<?php echo $venta['folio']; ?>";

            if (esMovil) {
                // Configuración optimizada para tiqueteras POS de 80mm desde celulares
                const opciones = {
                    margin: [4, 4, 4, 4],
                    filename: `Ticket_${folio}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 3, useCORS: true, letterRendering: true },
                    jsPDF: { unit: 'mm', format: [80, 297], orientation: 'portrait' }
                };

                // Ocultar la barra del botón antes del render
                const botonControl = elemento.querySelector('.no-print');
                if (botonControl) botonControl.style.display = 'none';

                // Genera la descarga asíncrona directamente en el navegador del teléfono
                html2pdf().set(opciones).from(elemento).save().then(() => {
                    if (botonControl) botonControl.style.display = 'block';
                });
            } else {
                // Computadoras: Despliegue del cuadro tradicional nativo
                window.print();
            }
        }

        // Ejecución automatizada en cascada al terminar el DOM
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(procesarImpresion, 800);
        });
    </script>
</body>

</html>