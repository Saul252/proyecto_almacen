<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

// Capturamos el ID.
$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Forzamos mostrar_precios a 0 para este archivo de Remisión
$mostrar_precios = 0; 

if ($id_venta <= 0) die("Error: ID de venta no válido.");

// 1. Consulta de Venta con Relaciones
$sqlVenta = "SELECT v.*, c.nombre_comercial, c.rfc, c.direccion, u.nombre as nombre_vendedor, u2.nombre as vendedor,
                    a.nombre as nombre_almacen, a.ubicacion as direccion_almacen
             FROM ventas v
             JOIN clientes c ON v.id_cliente = c.id
             join usuarios u2 on u2.id=v.vendedor_id
             JOIN usuarios u ON v.usuario_id = u.id
             JOIN almacenes a ON v.almacen_id = a.id
             WHERE v.id = ?";
$stmt = $conexion->prepare($sqlVenta);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

// 2. Consulta de Detalles (Añadimos factor_conversion y unidad_reporte)
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="text-uppercase">Remisión #<?php echo $venta['folio']; ?></title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        @page { size: 80mm auto; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 72mm; margin: 0 auto; padding: 5mm; color: #000;
            background-color: #fff;
            line-height: 1.2;
            text-transform: uppercase !important;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .header-title { font-size: 14px; margin-bottom: 5px; text-transform: uppercase; }
        
        .ticket-type { 
            border: 1px solid #000; padding: 3px 10px; 
            display: inline-block; font-size: 12px; margin: 10px 0;
            background-color: #000; color: #fff;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { font-size: 11px; border-bottom: 1px dashed #000; padding: 5px 0; text-align: left; }
        td { padding: 5px 0; font-size: 11px; vertical-align: top; }
        
        .conversion-box {
            margin-top: 3px;
            padding-left: 5px;
            border-left: 2px solid #000;
            font-size: 10px;
        }

        .divider { border-top: 1px double #000; margin: 10px 0; }
        .footer { font-size: 10px; margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px;}

        .firmas-container { margin-top: 30px; font-size: 10px; width: 100%; }
        .firma-box { text-align: center; margin-bottom: 25px; }
        .linea-firma { width: 85%; border-top: 1px solid #000; margin: 0 auto 5px; }

        /* Botón estilizado y adaptable */
        .btn-imprimir {
            padding: 12px 24px; 
            cursor: pointer; 
            font-weight: bold; 
            background-color: #222; 
            color: #fff; 
             
            border-radius: 8px;
            font-size: 14px;
            width: 90%;
            max-width: 300px;
        }

        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div id="contenedor-remision">
         <img
        src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        style="
            position: fixed;
            top: 19.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 180px;
            opacity: 0.08;
            z-index: -1;
        "
    >

        <div class="no-print" style="text-align:center; padding:15px; background:#f4f4f4; margin-bottom:15px; border-radius: 8px;">
            <button class="btn-imprimir" onclick="procesarImpresion()">🖨️ ACCIÓN DE IMPRESIÓN</button>
        </div>

        <div class="text-center text-uppercase">
            <div class="text-uppercase bold header-title"><?php echo strtoupper($venta['nombre_almacen']); ?></div>
            <div class="text-uppercase" style="font-size: 10px;"><?php echo $venta['direccion_almacen']; ?></div>
            <div class="text-uppercase ticket-type bold">TICKET</div>
            <div style="font-size: 9px; color: #444;">GUÍA DE DESPACHO SIN PRECIOS</div>
        </div>

        <div class="divider"></div>

        <div style="font-size: 11px;">
            <div class="text-uppercase"><b>FOLIO:</b> <?php echo $venta['folio']; ?></div>
            <div class="text-uppercase"><b>FECHA:</b> <?php echo date("d/m/Y H:i", strtotime($venta['fecha'])); ?></div>
            <div class="text-uppercase"><b>CLIENTE:</b> <?php echo strtoupper($venta['nombre_comercial']); ?></div>
            <div class="text-uppercase"><b>VENDEDOR:</b> <?php echo $venta['vendedor']; ?></div>
            <div class="text-uppercase" style="margin-top: 3px;"><b>Notas:</b> <?php echo substr($venta['observaciones'], 0, 30); ?></div>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Producto</th>
                    <th>DESCRIPCIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $detalles->fetch_assoc()): 
                    $f = ($item['factor'] > 0) ? $item['factor'] : 1;
                    $unidad = $item['unidad_reporte'] ?: 'Unid.';
                    $cantEntera = floor($item['cantidad'] / $f);
                    $cantResto = round(fmod($item['cantidad'], $f), 8);
                    $equiv = floatval($item['odmaEquivalencia'] ?? 0);
                    $nombreMedida=$item['odmaNombre'];
                    $unidadMedida= $item['unidad_medida'];
                ?>
                <tr>
                    <td>
                        <b class="text-uppercase"><?php echo $item['producto_nombre']; ?></b><br>
                        <small>SKU: <?php echo $item['sku']; ?></small>
                    </td>
                    <td>
                        <div class="text-uppercase conversion-box">
                            Entrega: <b><?php echo $cantEntera>0?$cantEntera.' '.$unidad:''; ?></b>
                            
                            <?php if((number_format($cantResto,8)) > 0.00000000) {
                                if($cantEntera>0) { echo ' + '; }
                                if((1/$equiv)<=1 ) {
                                    $cantidadMedida=$cantResto/(number_format((1/$equiv),2));
                                    echo $cantidadMedida .' '.$nombreMedida;
                                }
                            }
                            ?>
                            <br>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="firmas-container">
            <div class="firma-box">
                <div class="linea-firma"></div>
                <div class="bold">ENTREGADO POR</div>
                <div>ALMACÉN / DESPACHO</div>
            </div>

            <div class="firma-box" style="margin-top: 40px;">
                <div class="linea-firma"></div>
                <div class="bold">RECIBIDO POR</div>
                <div>NOMBRE Y FIRMA CLIENTE</div>
            </div>
            
            <div class="text-uppercase" style="font-size: 8px; text-align: center; margin-top: 15px; font-style: italic;">
                "Mercancía recibida en buen estado y a entera satisfacción."
            </div>
        </div>

        <div class="footer text-center">
            <p class="bold">¡GRACIAS POR SU PREFERENCIA!</p>
            <p style="font-size: 8px;">ID: #<?php echo $venta['id']; ?> | <?php echo date("d/m/Y H:i"); ?></p>
            <p style="font-size: 8px;">Software: cfsistem.v1</p>
        </div>
    </div>

    <script>
        function procesarImpresion() {
            // 1. Detectamos si es un dispositivo móvil
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const elemento = document.getElementById('contenedor-remision');
            const folio = "<?php echo $venta['folio']; ?>";

            if (esMovil) {
                // Configuración exacta para formato Ticket POS de 80mm en celulares
                const opciones = {
                    margin:       [5, 5, 5, 5], // Márgenes milimétricos limpios
                    filename:     `Remision_${folio}.pdf`,
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 3, useCORS: true, letterRendering: true }, // Mayor escala para legibilidad del texto
                    jsPDF:        { unit: 'mm', format: [80, 297], orientation: 'portrait' } // Forzado a ancho físico de 80mm
                };

                // Ocultar botón temporalmente antes de capturar el HTML
                const boton = elemento.querySelector('.no-print');
                if(boton) boton.style.display = 'none';

                // Renderizar y descargar directamente
                html2pdf().set(opciones).from(elemento).save().then(() => {
                    // Restaurar botón tras la descarga
                    if(boton) boton.style.display = 'block';
                });

            } else {
                // Comportamiento regular nativo para PCs de escritorio
                window.print();
            }
        }

        // Ejecutar automáticamente al terminar de cargar la vista
        window.addEventListener('DOMContentLoaded', () => {
            // Un pequeño retraso de seguridad para que se rendericen estilos e imágenes
            setTimeout(procesarImpresion, 800);
        });
    </script>
</body>
</html>