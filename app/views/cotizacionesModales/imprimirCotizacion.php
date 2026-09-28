 <div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 16px; overflow: hidden;">

                <!-- HEADER -->
                <div class="modal-header text-white "
                    style="background: linear-gradient(135deg, #1f2a37 0%, #334155 100%);">
                    <h5 class="fw-bold mb-0">
                        Vista de Cotizacion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">

                    <div id="areaImpresion" class="p-5 bg-white" style="min-height: 650px; font-size: 0.95rem;">

                        <!-- ENCABEZADO -->
                        
<div class="d-flex justify-content-between align-items-center mb-4">

    <!-- Logo + Título -->
    <div class="d-flex align-items-center">

        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
             alt="Logo"
             width="55"
             height="55"
             class="me-3">

        <div>
            <h2 class="fw-bold text-uppercase mb-0" style="color:#1f2a37; letter-spacing:1px;">
                COTIZACIÓN
            </h2>

            <div class="text-body-secondary small mt-1" id="print-folio">
                Folio: <span class="fw-semibold">#00000</span>
            </div>
        </div>

    </div>

    <!-- Empresa -->
    <div class="text-end">
        <div class="fw-bold fs-5" style="color:#1f2a37;">
            CFSistem
        </div>

        <div class="text-body-secondary small" id="print-fecha">
            Fecha: --/--/----
        </div>
    </div>

</div>
                       

                        <!-- INFO -->
<div class="row p-3"style="background: #ffffff02 !important;">
    <div style="background: #ffffff02 !important;"class="col-4 border rounded-3 bg-light p-3 shadow-sm">
        <div class="  ">
            <small class="text-uppercase text-body-secondary fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                Vendedor:
            </small>
            <div class="fw-bold text-black" id="vendedor" style="background: #ffffff02 !important;font-size: 0.9rem;">---</div>
        </div>
    </div>
    
    <div style="background: #ffffff02 !important;" class="col-8 border rounded-3 bg-light p-3 shadow-sm">
        
            <small class="text-uppercase text-body-secondary fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                Cliente:
            </small>
            <div class="row fw-bold text-black" id="clienteData"style="background: #ffffff02 !important;font-size: 0.9rem;">---</div>
       
    </div>
    </div>


                        <!-- TABLA -->
                        <div class="table-responsive mb-3">
                            <table class="table align-middle">

                                <thead style="background: #ffffff02 !important; color:#fff;">
                                    <tr style="background: #ffffff02 !important;">
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Costo Unitario</th>
                                        <th class="text-center">Costo Total</th>
                                    </tr>
                                </thead>

                                <tbody id="print-tabla-cuerpo"></tbody>

                            </table>
                        </div>

                        <!-- TOTAL -->
                        <div class="text-end mb-4">
                            <div class="fw-bold fs-5">
                                Total: <span id="costo_total"></span>
                            </div>
                        </div>

                        <!-- FIRMAS -->
                        

                    </div>
                </div>

                <!-- FOOTER -->


            </div>
        </div>
    </div>
<script>

 async function prepararImpresion(id) {
        try {
            
            $('#tablaConversion tbody').empty();

            console.log(id);

            const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);

            const datos = await resp.json(); // 👈 AQUÍ
            const data = datos.data;

            console.log('DATA REAL:', data);


            if (!Array.isArray(data) || data.length === 0) {
                console.error('Sin datos');
                return;
            }

            const infoBase = data[0];

            $('#print-folio').text(`FOLIO: #${id.toString().padStart(5, '0')}`);
            $('#print-fecha').text(`Fecha: ${new Date().toLocaleDateString()}`);
            $('#print-almacen').text(infoBase.almacen_nombre);
            $('#print-proveedor').text(infoBase.cliente_nombre || 'No especificado');
let cliente=`
    
        <div class="col-6">
            <small class="text-body-secondary d-block" style="font-size: 0.75rem;">CLIENTE</small>
             
            <div>
                 <span id="print-cliente-nombre" class="fw-bold text-dark">${infoBase.cliente_nombre}</span>
            </div>
        </div>
        
        <div class="col-6">
           
            <div>
                <small class="text-body-secondary d-block" style="font-size: 0.75rem;">Dirección</small>
                <span id="print-cliente-direccion" class="text-secondary small">${infoBase.direccion ?? ''}</span>
            </div>
        </div>

        <div class="col-6">
             <div>
                <small class="text-body-secondary d-block" style="font-size: 0.75rem;">Teléfono</small>
                <span id="print-cliente-telefono" class="text-secondary small">${infoBase.telefono ?? '#'}</span>
            </div>
        </div>
    
`;
$('#clienteData').html(cliente);

let vendedor=`<div class="card  bg-light">
    <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-fill text-secondary fs-5"></i>
            <div>
                <small class="text-body-secondary d-block" style="font-size: 0.75rem;">Vendedor</small>
                <span id="print-cliente-nombre" class="fw-bold text-dark">${infoBase.nombreVendedor}</span>
            </div>
        </div>
        
        
    </div>
</div>`;
$('#vendedor').html(vendedor);

            let totalGeneral = 0;
            let html = '';

            data.forEach(i => {

                const cantidad = parseFloat(i.cantidad) || 0;
                const precioUnitario = parseFloat(i.precio_unitario) || 0;
                const subtotal = parseFloat(i.subtotal) || 0;

                totalGeneral += subtotal;

                html += `
                <tr>
                    <td class="text-uppercase text-center" style="font-size: 0.7rem;">
                        ${cantidad} 
                    </td> <td class="text-uppercase text-center" style="font-size: 0.7rem;">
                         ${i.nombre || ''}
                    </td>
 <td class="text-uppercase fw-bold"style="font-size: 0.7rem;">${i.producto_nombre}</td>

                   
                    <td class="text-uppercase text-center" style="font-size: 0.7rem;">
                        ${precioUnitario.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>

                    <td class="text-uppercase text-center" style="font-size: 0.7rem;">
                        ${subtotal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>
                </tr>`;
            });

            $('#costo_total').text(
                totalGeneral.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                })
            );

            $('#print-tabla-cuerpo').html(html);

            ejecutarImpresion(vendedor,cliente,html,id,totalGeneral);

        } catch (e) {
            console.error(e);
        }
    }
 function ejecutarImpresion(vendedor,cliente,html,id,totalGeneral) {
        // 1. Obtener el contenido del área de impresión
        

        // 2. Crear una ventana nueva
        const ventana = window.open('', '_blank', 'height=600,width=800');
let fecha = "<?= date('d/m/Y') ?>"; // Resultado: 01/07/2026
       
          
        ventana.document.write(`
        <html>
            <head>
                <title>COTIZACION </title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Inter', sans-serif;  }
                    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
                    .fw-bold { font-weight: bold !important; }
                    @media print {
                        .no-print { display: none; }
                        
                    }
                        @page { 
                        margin: 0; /* Esto elimina el título de arriba y la fecha/hora de abajo */
                    }
                    
                    body { 
                        padding: 1.5cm; /* Le da margen al contenido para que no se pegue al borde del papel */
                    }
                    .firma-linea { border-top: 1px solid #000; margin-top: 50px; text-align: center; padding-top: 5px; font-size: 12px; }
                </style>
               </head>
            <body>
   <img
    src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
    style="
        position: fixed;
        top: 30%;                  /* Centro vertical */
        left: 50%;                 /* Centro horizontal */
        transform: translate(-50%, -50%); /* Compensa el propio tamaño de la imagen */
        width: 240px;
        opacity: 0.08;
        z-index: 1;               /* Cambiado a -1 para que quede detrás del texto y no tape los clics */
        pointer-events: none;      /* Evita que interfiera si alguien intenta hacer clic sobre ella */
    "
>
<div id="areaImpresion" class="text-uppercase  bg-white" style="min-height: 650px; font-size: 0.95rem;">

                        <!-- ENCABEZADO -->
                        
<div class="d-flex justify-content-between align-items-center mb-4">

    <!-- Logo + Título -->
    <div class="d-flex align-items-center">

        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
             alt="Logo"
             width="55"
             height="55"
             class="me-3">

        <div>
            <h2 class="fw-bold text-uppercase mb-0" style="color:#1f2a37; letter-spacing:1px;">
                COTIZACIÓN
            </h2>

            <div class="text-body-secondary small mt-1 text-uppercase" id="print-folio">
                Folio: <span class="fw-semibold">${id}</span>
            </div>
        </div>

    </div>

    <!-- Empresa -->
    <div class="text-end text-uppercase">
        <div class="fw-bold fs-5" style="color:#1f2a37;">
            CFSistem
        </div>

        <div class="text-uppercase text-body-secondary small" id="print-fecha">
            Fecha: ${fecha}
        </div>
    </div>

</div>
                       

                        <!-- INFO -->
<div class="row p-3"style="background: #ffffff02 !important;">
    <div style="background: #ffffff02 !important;"class="col-5 border rounded-3 bg-light  shadow-sm">
        <div class="  ">
            <small class="text-uppercase text-body-secondary fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                
            </small>
            <div class="text-uppercase fw-bold text-black" id="vende" style="background: #ffffff02 !important;font-size: 0.9rem;">${vendedor}</div>
        </div>
    </div>
    
    <div style="background: #ffffff02 !important;" class="col-7 border rounded-3 bg-light  shadow-sm">
        
            <small class="text-uppercase text-body-secondary fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
              
            </small>
            <div class="row fw-bold text-blackcard  bg-light " id="clienteD"style="background: #ffffff02 !important;font-size: 0.9rem;">${cliente}</div>
         
    </div>
    </div>


                        <!-- TABLA -->
                        <div class="table-responsive mb-3">
                            <table class="table align-middle">

                                <thead style="background: #ffffff02 !important; color:#fff;">
                                    <tr style="background: #ffffff02 !important;">
                                         <th class="text-uppercase text-center">Cantidad</th>
                                        <th class="text-uppercase text-center">UNIDAD</th>
                                         <th>Producto</th>
                                      
                                        <th class="text-uppercase text-center">Costo Unitario</th>
                                        <th class="text-uppercase text-center">Costo Total</th>
                                    </tr>
                                </thead>

                                <tbody id="print-tabla"></tbody>
                               
${html}
                            </table>
                        </div>

                        <!-- TOTAL -->
                        <div class="text-end mb-4">
                            <div class="fw-bold fs-5">
                                Total: <span id="costo_total">${totalGeneral.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                })}</span>
                

                            </div>
                        </div>

                       <div class="mt-4 p-3 bg-light border rounded-3 shadow-sm" style="background-color: #f8f9fa !important; border-color: #dee2e6 !important;">
    <div class="fw-bold mb-2 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        Notas y Condiciones de Venta:
    </div>
    <ul class="mb-0 text-body-secondary" style="font-size: 0.85rem; padding-left: 1.2rem; line-height: 1.5;">
        <li>Los precios están sujetos a cambios sin previo aviso.</li>
        <li>Los precios ya incluyen IVA.</li>
        <li>Las descargas son a pie de carro.</li>
        <li>Para programar un pedido se debe liquidar primero y debe estar en firme.</li>
        <li>No hay cambios ni devoluciones una vez descargado.</li>
        <li>Una vez liquidado se entregará de 1 a 2 días hábiles.</li>
    </ul>
</div>
                        

                    </div>
           <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script> 
                <script>
   window.addEventListener('DOMContentLoaded', () => {
        // 1. Detectar si el usuario está en un dispositivo móvil
        const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // 2. Esperar 1 segundo a que carguen estilos, fuentes e imágenes
        setTimeout(() => {
            if (esMovil) {
                // --- COMPORTAMIENTO EN CELULARES: DESCARGA DE PDF AUTOMÁTICA ---
                const elementoParaConvertir = document.getElementById('areaImpresion');

                const opciones = {
                    margin:       1,
                    filename:     'Cotizacion_${id}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true }, // Mayor calidad visual
                    jsPDF:        { unit: 'cm', format: 'letter', orientation: 'portrait' }
                };

                // Generar y descargar el PDF directamente
                html2pdf().set(opciones).from(elementoParaConvertir).save();
                
            } else {
                // --- COMPORTAMIENTO EN COMPUTADORAS: DIÁLOGO NATIVO DE IMPRESIÓN ---
                window.print();
            }
        }, 1000); // 1000 milisegundos = 1 segundo de espera
    });
 <\/script>
            </body>
        </html>
    `);

        ventana.document.close();
    }

</script>