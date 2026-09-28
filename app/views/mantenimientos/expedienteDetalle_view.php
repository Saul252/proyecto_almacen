<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Insumos:  | CF System</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  
    <style>
    :root {
        --bs-primary: #007aff;
        --bs-info: #3abaf4;
        --bs-success: #1cc88a;
        --bs-danger: #e74a3b;
        --bs-warning: #f6c23e;
        --bg-light: #f8f9fc;
    }

    body {
        background-color: var(--bg-light);
        font-family: 'Inter', sans-serif;
        color: #4e73df;
    }

    .header-expediente {
        background: white;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 2rem;
    }

    .kpi-widget {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        
        border-left: 4px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        height: 100%;
    }

    .kpi-label {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .kpi-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #5a5c69;
    }

    .border-left-primary {
        border-left-color: var(--bs-primary) !important;
    }

    .border-left-success {
        border-left-color: var(--bs-success) !important;
    }

    .border-left-danger {
        border-left-color: var(--bs-danger) !important;
    }

    .border-left-info {
        border-left-color: var(--bs-info) !important;
    }

    .folio-container {
        background: white;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .folio-debe {
        border-left: 5px solid var(--bs-danger);
    }

    .folio-liquidado {
        border-left: 5px solid var(--bs-success);
        background-color: #f6fff9;
    }

    .folio-favor {
        border-left: 5px solid var(--bs-info);
        background-color: #f0f7ff;
    }

    .folio-cancelado {
        border-left: 5px solid #858796;
        background-color: #f8f9fc;
    }

    .folio-header {
        background-color: rgba(0, 0, 0, 0.02);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .col-pagos {
        background-color: #fafbfc;
        border-left: 1px solid #e3e6f0;
        padding: 1.5rem;
    }

    .payment-pill {
        background: white;
        border: 1px solid #e3e6f0;
        border-left: 4px solid var(--bs-success);
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
    }
    @media (min-width: 992px) {
    .w-lg-60 {
        width: 60% !important;
    }
}
    </style>
</head>

<body>

    <header class="header-expediente shadow-sm mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
      
            <div style="display:flex; gap:10px; align-items:end; margin-bottom:15px; flex-wrap:wrap;">

             
  
   <?php
   date_default_timezone_set('America/Mexico_City');
$fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fechaFin    = $_GET['fecha_fin'] ?? date('Y-m-t');
?>

<div>
    <label style="font-size:12px;">Fecha inicio</label>
    <input
        type="date"
        id="fecha_inicio"
        class="form-control"
        value="<?= htmlspecialchars($fechaInicio) ?>">
</div>

<div>
    <label style="font-size:12px;">Fecha fin</label>
    <input
        type="date"
        id="fecha_fin"
        class="form-control"
        value="<?= htmlspecialchars($fechaFin) ?>">
</div>
 <div>

                <label class="form-label fw-semibold small">
                    Almacén
                </label>

               <select
    id="f_almacen"
    class="form-select"
    onchange="filtrarExpediente()">

    <?php if ($tipo == 1): ?>
        <option value="">Todos</option>
    <?php endif; ?>

    <?php foreach($almacenes as $a): ?>
        <option
            value="<?= $a['id'] ?>"
            <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($a['nombre']) ?>
        </option>
    <?php endforeach; ?>

</select>

            </div>

                <button class="btn btn-primary" onclick="filtrarExpediente()">
                    Filtrar
                </button>


 <button class="btn btn-dark btn-sm" onclick="imprimirEstadoCuenta()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            </div>
           
        </div>
    </header>


    <div class="container-fluid px-4">

      


<div class="d-flex justify-content-center px-2">
    <div class="card  shadow-sm mb-3 " style="max-width: 1200px;">
<h5 class="fw-bold mb-3 text-dark">Folios Detallados</h5>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <tr>
                    <th scope="col" class="ps-3">Fecha</th>
                    <th scope="col">Folio Interno</th>
                     <th scope="col" class="text-end">Mantenimiento</th>
                    <th scope="col" class="text-end">vehiculo</th>
                    
                    <th scope="col" class="text-end">Placas</th>
                   
                    <th scope="col" class="text-end">Insumo</th>
                    <th scope="col" class="text-center">Cantidad</th>
                    <th scope="col" class="text-center pe-3">Costo Unitario</th>
                     <th scope="col" class="text-center pe-3">Costo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expediente as $v):
                  
                ?>
                <tr>
                    <td class="ps-3">
                     
                        <small class="text-body-secondary"><?= date('d/m/Y', strtotime($v['fecha'])) ?></small>
                    </td>

                    <td>
                        <span class="badge bg-light text-dark border">
                            <?= htmlspecialchars($v['folio']) ?>
                        </span>
                    </td>
                      <td class="text-end fw-semibold text-dark">
                        <?= $v['mantenimiento']?? '-'?>
                    </td>

                    <td class="text-end fw-semibold text-dark">
                        <?= $v['vehiculo'] ?>
                    </td>
 

                    <td class="text-end fw-semibold text-success">
                        <?= $v['placas'] ?>
                    </td>
                  
                     <td class="text-end fw-semibold text-success">
                        <?= $v['insumo'] ?>
                    </td>
                    <td> 
                    
                        <?= number_format($v['cantidad'], 2) ?>
                    </td>
                    <td class="text-end fw-bold">
                        $<?= number_format($v['costo'], 2) ?>
                    </td><td class="text-end fw-bold">
                        $<?= number_format($v['costo']*$v['cantidad'], 2) ?>
                    </td>
                    <td>
                         <button class="btn btn-sm btn-dark" 
        onclick="abrirDetalleSalida(<?= intval($v['folio']) ?>)" 
        title="Ver Detalle">
    <i class="bi bi-eye-fill"></i>
</button>
                    </td>

                    

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalDetalleSalidaInsumo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content  shadow-lg" style="border-radius:18px;">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-box-seam me-2"></i>
                    Detalle de Salida de Insumos
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3">

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Folio</label>
                        <input id="d_folio" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha</label>
                        <input id="d_fecha" class="form-control" readonly>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Usuario</label>
                        <input id="d_usuario" class="form-control" readonly>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Vehículo ID</label>
                        <input id="d_vehiculo_id" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Mantenimiento</label>
                        <input id="d_mantenimiento" class="form-control" readonly>
                    </div>

                </div>

                <hr>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Vehículo</label>
                        <input id="d_vehiculo" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Placas</label>
                        <input id="d_placas" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Costo Total</label>
                        <input id="d_total" class="form-control text-end fw-bold text-success" readonly>
                    </div>

                </div>

                <hr>

                <h6 class="fw-bold mb-3">
                    <i class="bi bi-list-check me-2"></i>
                    Insumos Utilizados
                </h6>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                        <tr>
                            <th>Detalle</th>
                            <th>Compra</th>
                            <th>Insumo ID</th>
                            <th>Insumo</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Costo Unitario</th>
                            <th class="text-end">Importe</th>
                        </tr>

                        </thead>

                        <tbody id="tbodyDetalleSalida">

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cerrar
                </button>

            </div>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php require_once __DIR__ . '/../ventasHistorialModales/registarAbonoCliente.php' ?>
     

    <script>
          const modalNuevoAbonoObj = new bootstrap.Modal('#modalNuevoAbono');

         
    let ventaActual = null;
    // La ruta al controlador (ajusta si el nombre del archivo varía)
    const URL_CONTROLLER = '/myvet/app/controllers/mantenimientosController.php';
    $(document).ready(function() {
        renderCharts();
    });

    function renderCharts() {
        const ctx = document.getElementById('chartDona');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [
                        <?= isset($resumen['total_pagado']) ? floatval($resumen['total_pagado']) : 0 ?>,
                        <?= isset($resumen['saldo_total']) ? max(0, floatval($resumen['saldo_total'])) : 0 ?>
                    ],
                    backgroundColor: ['#1cc88a', '#e74a3b'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

      </script>
   <script>async function imprimirEstadoCuenta() {
    try {

        const fechaInicio = document.getElementById('fecha_inicio')?.value || '';
        const fechaFin = document.getElementById('fecha_fin')?.value || '';

        const res = await fetch(
            `/myvet/app/controllers/historialInsumosController.php?action=getEstadoCuentaCliente&fecha_inicio=${encodeURIComponent(fechaInicio)}&fecha_fin=${encodeURIComponent(fechaFin)}`
        );

        const texto = await res.text();

        let data;
        try {
            data = JSON.parse(texto);
        } catch (e) {
            console.error(texto);
            throw new Error("La respuesta del servidor no es un JSON válido.");
        }

        if (data.status !== 'success') {
            return Swal.fire(
                'Error',
                data.message || 'No se pudo cargar la información.',
                'error'
            );
        }

        const expediente = data.expediente || [];

        const w = window.open('', '_blank', 'width=1200,height=700');

        if (!w) {
            return Swal.fire(
                'Error',
                'El navegador bloqueó la ventana emergente.',
                'error'
            );
        }

        const formatoFecha = (fecha) => {
            if (!fecha) return '-';
            return new Date(fecha).toLocaleDateString('es-MX');
        };

        const formatoMoneda = (valor) => {
            return '$' + Number(valor || 0).toLocaleString('es-MX', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        const filas = expediente.map(v => `
            <tr>
                <td>${formatoFecha(v.fecha)}</td>
                <td>${v.folio ?? '-'}</td>
                 <td>${v.mantenimiento ?? '-'}</td>
                <td>${v.vehiculo ?? v.nombre ?? '-'}</td>
                
                <td>${v.placas ?? '-'}</td>
               
                <td>${v.insumo ?? '-'}</td>
                <td style="text-align:center">${Number(v.cantidad).toFixed(2)}</td>
                <td style="text-align:right">${formatoMoneda(v.costo)}</td>
                <td style="text-align:right">${formatoMoneda((v.costo || 0) * (v.cantidad || 0))}</td>
            </tr>
        `).join('');

        const doc = `
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
<title>Historial de Insumos</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


<style>

body{
    font-family:Arial,Helvetica,sans-serif;
    margin:20px;
    font-size:12px;
}

h2{
    text-align:center;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid #ddd;
    padding:8px;
    font-size:11px;
}

th{
    background:#0d6efd;
    color:white;
}

tbody tr:nth-child(even){
    background:#f8f9fa;
}

.text-right{
    text-align:right;
}

.text-center{
    text-align:center;
}

</style>

</head>

<body>

<h2>Historial de Salidas de Insumos</h2>

<table>

<thead>

<tr>
    <th>Fecha</th>
    <th>Folio</th>
     <th>Mantenimiento</th>
    <th>Vehículo</th>
   
    <th>Placas</th>
    
    <th>Insumo</th>
    <th>Cantidad</th>
    <th>Costo Unitario</th>
    <th>Total</th>
</tr>

</thead>

<tbody>

${filas}

</tbody>

</table>

</body>

</html>
`;

        w.document.open();
        w.document.write(doc);
        w.document.close();

        w.onload = () => {
            w.focus();
            w.print();
        };

    } catch (error) {

        console.error(error);

        Swal.fire(
            'Error',
            error.message,
            'error'
        );

    }
}
</script>
    <script>
    function filtrarExpediente() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        const urlParams = new URLSearchParams(window.location.search);
     
        // REDIRECCIÓN
        window.location.href =
            `/myvet/app/controllers/historialInsumosController.php?&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;


    }
    </script>
    <script>
     function togglePerso() {
        $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
        getVentas();
    }
    function imprimirContenidoModal() {
    // 1. Obtener los elementos clave del modal actual
    const folio = $('#spanFolio').text();
    const cliente = $('#detCliente').text();
    const almacen = $('#detAlmacen').text();
    
    // 2. Clonar las tablas de datos para no alterar el modal visual
    const tablaProductos = $('#tbodyDetalle').html();
    const tablaEntregas = $('#tbodyHistorial').html();
    const tablaPagos = $('#tbodyPagos').html();
    
    const total = $('#detTotalLabel').text();
    const saldo = $('#detSaldoLabel').text();

    // 3. Crear una nueva ventana temporal en el navegador
    const ventanaImpresion = window.open('', '_blank');

    // 4. Inyectar el HTML estructurado con estilos limpios y profesionales
    ventanaImpresion.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Venta - Folio ${folio}</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                body {font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding: 30px; color: #333; }
                .ticket-header { border-bottom: 2px solid #007aff; padding-bottom: 15px; margin-bottom: 20px; }
                .meta-box { background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
                .section-title { font-size: 0.85rem; font-weight: bold; text-transform: uppercase; color: #666; margin-top: 25px; margin-bottom: 10px; letter-spacing: 0.5px; }
                .table-responsive { max-height: none !important; overflow: visible !important; }
                .d-none { display: none !important; } /* Oculta columnas de inputs si están activas */
                @media print {
                    body { padding: 20px;  }
                    .btn-imprimir { display: none; }
                }
                     @page { 
                        margin: 0; /* Esto elimina el título de arriba y la fecha/hora de abajo */
                    }
            </style>
        </head>
        <body>
         <div id="areaImpresion" class="text-uppercase  bg-white" style="min-height: 650px; font-size: 0.95rem;">
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
                        <!-- ENCABEZADO -->
                        
<div class=" ">

    <!-- Logo + Título -->
    <div class="">

        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
             alt="Logo"
             width="55"
             height="55"
             class="me-3">

         <div class="ticket-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold m-0">CF SYSTEM</h4>
                    <small class="text-body-secondary">Reporte de Operación de Venta</small>
                </div>
                <div class="text-end">
                    <h5 class="text-primary fw-bold m-0">Folio: ${folio}</h5>
                </div>
            </div>

            
    </div>

  


                        </div>
                        <div class="row g-3">
                <div class="col-6">
                    <div class="meta-box">
                        <small class="text-body-secondary d-block text-uppercase fw-semibold" style="font-size:0.7rem;">Cliente</small>
                        <span class="fw-bold">${cliente}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="meta-box">
                        <small class="text-body-secondary d-block text-uppercase fw-semibold" style="font-size:0.7rem;">Almacén Origen</small>
                        <span class="fw-bold">${almacen}</span>
                    </div>
                </div>
            </div>
<div class="section-title">📦 Productos</div>
            <div class="table-responsive" style="max-height: 180px;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                            <th>Producto</th>
                                            <th class="text-center">Venta</th>
                                            <th class="text-center">Surtido</th>
                                            <th class="text-center text-danger">Falta</th>
                                            <th class="text-center d-none">Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody >${tablaProductos}</tbody>
                                </table>
                            </div>
                        <div class="row g-3 py-5">

                            <div class="col-12">
                                <div class="card  shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                        Historial de Pagos
                                    </div>
                                    <div class="table-responsive" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Monto</th>
                                                    <th>Método</th>
                                                    <th>REFERENCIA</th>
                                                </tr>
                                            </thead>
                                            <tbody>${tablaPagos}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12  d-none">
                           
                                <div class="card  shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                        Historial de Entregas
                                    </div>
                                    <div class="table-responsive" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Responsable</th>
                                                    <th>Producto</th>
                                                    <th class="text-center">Cant</th>
                                                </tr>
                                            </thead>
                                            <tbody ">${tablaEntregas}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
            
            

            <div class="row justify-content-end mt-4">
                <div class="col-5">
                    <table class="table table-sm table-borderless border-top pt-2">
                        <tr>
                            <td class="text-end text-body-secondary">Total Venta:</td>
                            <td class="text-end fw-bold">${total}</td>
                        </tr>
                        <tr>
                            <td class="text-end text-body-secondary">Saldo Pendiente:</td>
                            <td class="text-end fw-bold text-danger">${saldo}</td>
                        </tr>
                    </table>
                </div>
            </div>


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
                    filename:     'expediente_${folio}.pdf',
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

    ventanaImpresion.document.close();
}
async function abrirDetalleSalida(id){

        const res = await fetch(
            `/myvet/app/controllers/historialInsumosController.php?action=getInsumo&id=${id}`
        );

        const dato = await res.json();

        
        console.log(dato);
        let dat=dato.entregaInsumo[0];
        let datos=dato.entregaInsumo;
        

    document.getElementById("d_folio").value = dat.folio;
    document.getElementById("d_fecha").value = dat.fecha;
    document.getElementById("d_usuario").value = dat.usuario;
    document.getElementById("d_vehiculo_id").value = dat.vehiculo_id;
    document.getElementById("d_mantenimiento").value = dat.mantenimiento ?? "-";

    document.getElementById("d_vehiculo").value = dat.vehiculo;
    document.getElementById("d_placas").value = dat.placas;

    let total = 0;

    const tbody = document.getElementById("tbodyDetalleSalida");

    tbody.innerHTML = "";

    datos.forEach(det=>{

        const importe = Number(det.cantidad) * Number(det.costo);

        total += importe;

        tbody.innerHTML += `
        <tr>

            <td>${det.detalle_id}</td>

            <td>${det.compra_id}</td>

            <td>${det.insumo_id}</td>

            <td>${det.insumo}</td>

            <td class="text-center">
                ${Number(det.cantidad).toFixed(2)}
            </td>

            <td class="text-end">
                $${Number(det.costo).toFixed(2)}
            </td>

            <td class="text-end fw-bold">
                $${importe.toFixed(2)}
            </td>

        </tr>`;
    });

    document.getElementById("d_total").value =
        "$"+total.toFixed(2);

    new bootstrap.Modal(
        document.getElementById("modalDetalleSalidaInsumo")
    ).show();

}
    </script>

</body>

</html>