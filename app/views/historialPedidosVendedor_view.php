<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas Vendedor | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>


    <style>
        :root {
    --primary: #1f2937;
    --secondary: #374151;
    --bg: #4f4f50;
    --card: #686868;
    --accent: #3b82f6;
}



/* Contenedor tipo card moderno */
.scroll-table {
    background: var(--card);
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    overflow: hidden;
    
}

/* Scroll interno más limpio */
.table-responsive {
    max-height: 60vh;
}

/* Tabla base */
.table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

/* Header sticky */
.table thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: var(--primary);
    color: #fff;
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 14px;
    
}.table-clientes thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #000000 !important;
    color: #fff;
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 14px;
    
}

/* Celdas */
.table tbody td {
    padding: 14px;
    border-bottom: 1px solid #eef0f3;
    font-size: 0.9rem;
 
}
.table-clientes tbody td {
    padding: 14px;
    border-bottom: 1px solid #eef0f3;
    font-size: 0.9rem;
  
}

/* Hover tipo “card highlight” */
.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background: #f9fafb;
    transform: scale(1.002);
    box-shadow: inset 4px 0 0 var(--accent);
}

/* Botones más modernos */
.btn-action {
    background: var(--accent);
    color: white;
    border-radius: 8px;
    padding: 6px 10px;
    
    font-size: 0.8rem;
    transition: 0.2s;
}

.btn-action:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

/* Inputs destacados */
.input-entrega {
    border: 2px solid #22c55e !important;
    border-radius: 8px;
    max-width: 90px;
    text-align: center;
    font-weight: 600;
    background: #f0fdf4;
}

/* Responsive */
@media (max-width: 992px) {
    .main-content {
        margin-left: 0;
        padding: 1rem;
    }
}
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold card-title-text m-0">Historial Ventas Vendedor</h3>
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
            </div>

            <div class="card filter-card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscador</label>
                            <input type="text" id="f_search" class="form-control form-control-sm"
                                placeholder="Folio o Cliente..." onkeyup="getVentas()">
                        </div>
                        <div class="col-md-2" style="display:none">
                            <label class="form-label small fw-bold">Estatus Entrega</label>
                            <select id="f_status" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>
                       <?php if ($puede == true): ?>
<div class="col-md-2">
    <label for="select-usuarios" class="form-label fw-bold small card-title-text text-uppercase">Vendedor</label>
    <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id" onchange="getVentas()">
       <option value="">Seleccione vendedor</option>
    </select>
</div>
<?php endif; ?>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Pago</label>
                            <select id="f_pago" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="deuda">Con Deuda</option>
                                <option value="pagado">Pagados</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Periodo</label>
                            <select id="f_rango" class="form-select form-select-sm" onchange="togglePerso()">
  <option value="semana">Semana</option>
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
                                <option value="todos">Historial Completo</option>
                                <option value="personalizado">Rango...</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-none" id="div_p">
                            <label class="form-label small fw-bold">Fechas</label>
                            <div class="input-group input-group-sm">
                                <input type="date" id="f_ini" class="form-control" value="<?= date('Y-m-d')?>" onchange="getVentas()">
                                <input type="date" id="f_fin" class="form-control" value="<?= date('Y-m-d')?>" onchange="getVentas()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Ubicación</label>
                            <select id="f_almacen" class="form-select form-select-sm" onchange="getVentas()">
                                  <?php if($rol_id==1||$rol_id>=3):?> <option value="">Seleccione ubicacion</option><?php endif;?>
                               <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"
                                        <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                        <?= $a['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                            </select>
                        </div><div class="col-md-12"> <!-- Ampliado a col-md-3 para que respire mejor el dinero -->
   <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <!-- Encabezado del Widget -->
    <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0 d-flex align-items-center justify-content-between">
        <span class="text-uppercase fw-bold text-secondary small tracking-wider">
            <i class="bi bi-pie-chart-fill me-1 text-primary"></i> Estado Financiero
        </span>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small" style="font-size: 0.7rem;">
            En tiempo real
        </span>
    </div>

    <!-- Cuerpo del Widget -->
    <div class="card-body p-3">
        <div class="row g-2">
            <!-- Bloque 1: Total Venta -->
            <div class="col-6">
                <div class="p-3 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-primary fw-bold small tracking-wider" style="font-size: 0.7rem;">
                            Total Venta
                        </span>
                        <div class="bg-primary text-white rounded-2 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px;">
                            <i class="bi bi-cart-check fs-6"></i>
                        </div>
                    </div>
                    <span id="venta" class="fs-4 fw-black text-primary d-block lh-1">
                        $0.00
                    </span>
                </div>
            </div>

            <!-- Bloque 2: Por Cobrar -->
            <div class="col-6">
                <div class="p-3 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-danger fw-bold small tracking-wider" style="font-size: 0.7rem;">
                            Por Cobrar
                        </span>
                        <div class="bg-danger text-white rounded-2 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px;">
                            <i class="bi bi-exclamation-circle fs-6"></i>
                        </div>
                    </div>
                    <span id="deuda" class="fs-4 fw-black text-danger d-block lh-1">
                        $0.00
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
                    </div>
                </div>
            </div>

            <div class="card  shadow-sm rounded-4 mb-4 overflow-hidden">
              
    <div class="table-responsive" style="max-height: 60vh; min-height: 30vh; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" id="tablaVentas">
            <thead class="table-light sticky-top top-0 card-title-text fw-semibold uppercase-tracking">
                <tr>
                    <th class="ps-4 py-3">Fecha</th>
                    <th class="py-3">Folio</th>
                    <th class="py-3">Almacén</th>
                    <th class="py-3">Vendedor</th>
                    <th class="py-3">Cliente</th>
                    <th class="py-3">Factura</th>
                    <th class="text-end py-3">Total</th>
                    <th class="text-end py-3">Saldo Cobro</th>
                    <th class="text-end pe-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class=" font-table-body">
                </tbody>
        </table>
    </div>
</div>

<div class="card  shadow-sm rounded-4 overflow-hidden py-4">
      <span id="" class="fs-4 fw-black text-primary d-block mt-1">
                Total Por Cliente
            </span>
    <div class="table-responsive" style="max-height: 60vh;  overflow-y: auto;">
        <table class="table table-clientes table-hover align-middle mb-0" id="tablaClientes">
            <thead class="table table-clientes sticky-top top-0 card-title-text fw-semibold uppercase-tracking">
                <tr>
                    <th class="ps-4 py-3">ID</th>
                    <th class="py-3">Cliente</th>
                    <th class="text-center py-3">Total Vendido</th>
                    <th class="text-center py-3">Total Cobrado</th>
                    <th class="text-center pe-4 py-3">Saldo Por Cobrar</th>
                </tr>
            </thead>
            <tbody class=" font-table-body">
                </tbody>
        </table>
    </div>
</div>
        </div>
    </div>

    <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content  shadow">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title fw-bold">
                    Información Venta: <span id="spanFolio" class="text-warning"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- SIDEBAR -->
                    <div class="col-md-3 border-end p-4">

                        <div class="mb-3">
                            <small class="text-uppercase card-title-text fw-bold">Cliente</small>
                            <div id="detCliente" class="fw-semibold"></div>
                        </div>

                        <div class="mb-4">
                            <small class="text-uppercase card-title-text fw-bold">Almacén</small>
                            <div id="detAlmacen" class="fw-semibold"></div>
                        </div>

                        <!-- RESUMEN -->
                        <div class="card  shadow-sm mb-3">
                            <div class="card-body text-center">

                                <div class="mb-3">
                                    <small class="text-uppercase card-title-text fw-bold d-block">
                                        Total de Venta
                                    </small>
                                    <span id="detTotalLabel" class="fs-5 fw-bold text-primary">
                                        $0.00
                                    </span>
                                </div>

                                <hr>

                                <div>
                                    <small class="text-uppercase card-title-text fw-bold d-block">
                                        Saldo Pendiente
                                    </small>
                                    <span id="detSaldoLabel" class="fs-4 fw-bold text-danger">
                                        $0.00
                                    </span>
                                </div>

                            </div>
                        </div>

                        <!-- BOTÓN -->
                       <div id="boton"></div>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="col-md-9 p-4">

                        <!-- DETALLE PRODUCTOS -->
                        <div class="card  shadow-sm mb-3">
                            <div class="card-header fw-bold small text-uppercase card-title-text">
                                Productos
                            </div>
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
                                    <tbody id="tbodyDetalle"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row g-3">

                            <!-- HISTORIAL PAGOS -->
                            <div class="col-12">
                                <div class="card  shadow-sm">
                                    <div class="card-header  fw-bold small text-uppercase card-title-text">
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
                                            <tbody id="tbodyPagos"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- HISTORIAL ENTREGAS (OCULTO LO RESPETÉ) -->
                            <div class="col-12 d-none">
                                <div class="card  shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase card-title-text">
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
                                            <tbody id="tbodyHistorial"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- REPARTOS (OCULTO) -->
                            <div class="col-12 d-none">
                                <div class="card  shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase card-title-text">
                                        Repartos
                                    </div>
                                    <div class="table-responsive" style="max-height: 220px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th># Reparto</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Estado</th>
                                                    <th class="text-center">Ruta</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyRepartos"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
 <h4 id="cancelado" class="fw-bold text-danger padding-top-3 mb-3"></h4>
                            
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
     <?php require_once __DIR__ . '/ventasHistorialModales/registarNuevoAbono.php'; ?>
    
    <script>


     cargarUsuariosSelect();
    async function cargarUsuariosSelect() {
    const select = document.getElementById('select-usuarios');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);
        
        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
        
        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {
            
            // Limpiamos el select y dejamos una opción inicial neutra
           // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario
                
                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;
                
                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}
    const modalObj = new bootstrap.Modal('#modalDetalle');
    let ventaActual = null;
    // La ruta al controlador (ajusta si el nombre del archivo varía)
    const URL_CONTROLLER = '/myvet/app/controllers/historialPedidosVendedorController.php';

    async function getVentas() {
        $('#loader').removeClass('d-none');

        const params = new URLSearchParams({
            action: 'listar',
            // <--- Nuevo parámetro para el ID de venta
            f_search: $('#f_search').val(),
            f_rango: $('#f_rango').val(),
            f_inicio: $('#f_ini').val(),
            f_fin: $('#f_fin').val(),
            f_almacen: $('#f_almacen').val(),
            f_status: $('#f_status').val(),
            f_pago: $('#f_pago').val(),
               f_vendedor:$('#select-usuarios').val() ?? ''
        });

        try {
            const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
            const data = await res.json();
            console.log(data.data);
            let deuda=0;
            let totalVendido=0;


            $('#tablaVentas tbody').html(data.data.map(v => {
                let total = 0;
                let pagado =  0;
                if(v.estado_general!='cancelada')
                {
                total = parseFloat(v.total) || 0;
                pagado = parseFloat(v.pagado) || 0;}              
                let saldo = total - pagado;
                deuda+=saldo;
                totalVendido+=total;
                let factura = (v.estado_general == 'activa') ?
                   `${v.factura!=0?v.factura:'Sin Factura'}
                `  :
                    '<span class="text-danger small fw-bold">----</span>';
                let badgeCobro = (saldo <= 0) ?
                    '<span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Pagado</span>' :
                    `<span class="text-danger small fw-bold">Debe: $${saldo.toFixed(2)}</span>`;
let agregarPago = (saldo <= 0) ?
                    '' :
                    `<button class="btn btn-sm btn-success shadow-sm" onclick="abrirNuevoAbono(${v.id})">
                            </i> Nuevo abono
                        </button>`;

                return `<tr>
    <td class="ps-3 small align-middle">${v.fecha}</td>
    <td class="fw-bold align-middle">${v.folio}</td>
    <td class="align-middle"><span class="badge bg-light text-primary  border fw-normal">${v.almacen_nombre}</span></td>
    <td class="align-middle"><div class="small fw-bold">${v.vendedor}</div></td>
    <td class="align-middle"><div class="small fw-bold">${v.cliente}</div></td>
    <td class="fw-bold card-title-text align-middle">${factura}</td>
    <td class="fw-bold card-title-text align-middle">$${total.toFixed(2)}</td>
    <td class="align-middle">
        ${v.estado_general == 'activa' ? badgeCobro : '<span class="text-danger small fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Cancelado</span>'}
    </td>
    <td class="text-end pe-3 align-middle">
        <div class="d-inline-flex align-items-center gap-1">
            <button class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 shadow-sm" onclick="verDetalle(${v.id})">
                <i class="bi bi-eye-fill"></i> Ver
            </button>
            
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-link text-secondary btn-sm px-2  remove-caret js-tooltip" 
                        data-bs-toggle="dropdown" 
                        data-bs-boundary="viewport" 
                        aria-expanded="false"
                        data-bs-placement="top" 
                        title="Más opciones">
                    <i class="bi bi-three-dots fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow  rounded-3 mt-1" style="position: fixed !important; z-index: 1060 !important;">
                    <li>
                        <a class="dropdown-item py-2 text-primary d-flex align-items-center" href="/myvet/app/backend/ventas/ticket_venta.php?id=${v.id}" target="_blank">
                            <i class="bi bi-receipt me-2 fs-5"></i> Imprimir Ticket
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 text-info d-flex align-items-center" href="/myvet/app/backend/ventas/ticket_sin_precio.php?id=${v.id}" target="_blank">
                            <i class="bi bi-file-earmark-text me-2 fs-5"></i> Imprimir Ticket sin precio
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 text-info d-flex align-items-center" href="/myvet/app/backend/ventas/ticketFormal.php?id=${v.id}" target="_blank">
                            <i class="bi bi-file-earmark-post me-2 fs-5"></i> Imprimir Ticket Formal
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </td>
</tr>`;
            }).join(''));$('#deuda').text(deuda.toFixed(2))
            $('#venta').text(totalVendido.toFixed(2))
            console.log(deuda);
            
const resultado = agruparVentasPorCliente(data.data);

            $('#tablaClientes tbody').html(resultado.map(v => {
               
               

                return `<tr>
    
    <td class="fw-bold align-middle">${v.id_cliente}</td>
    <td class="align-middle"><span class="badge bg-light text-dark border fw-normal">${v.cliente}</span></td>
    <td class="align-middle"><div class="small text-center fw-bold">${v.total_compro}</div></td>
    <td class="align-middle"><div class="small text-center fw-bold">${v.total_cobrado}</div></td>
    <td class="fw-bold card-title-text align-middle text-center">${(v.total_debe)}</td>
    
  
    
</tr>`;
            }).join(''));
            
        } catch (e) {
            console.error("Error al cargar ventas:", e);
        } finally {
            $('#loader').addClass('d-none');
        }
    }
    // Función para procesar y agrupar las ventas por cliente
function agruparVentasPorCliente(ventas) {
    
    // 1. Agrupar y sumar usando reduce
    const agrupado = ventas.reduce((acumulador, venta) => {
       
       
        const idCliente = venta.id_cliente;
        const totalVenta = parseFloat(venta.estado_general!='cancelada'?venta.total:0) || 0;
        const totalPagado = parseFloat(venta.estado_general!='cancelada'?venta.pagado:0) || 0;

        // Si el cliente no existe en el acumulador, lo inicializamos
        if (!acumulador[idCliente]) {
            acumulador[idCliente] = {
                id_cliente: idCliente,
                cliente: venta.cliente,
                total_compro: 0,
                total_cobrado: 0,
                total_debe: 0
            };
        }

        // Acumulamos los valores
       

        acumulador[idCliente].total_compro += totalVenta;
        acumulador[idCliente].total_cobrado += totalPagado;
        return acumulador;

        
        
    }, {});

    // 2. Convertir el objeto a un Arreglo, calcular saldos y redondear decimales
    const resultadoFinal = Object.values(agrupado).map(cliente => {
        // Calcular lo que debe: Compras - Cobrado
        let debe = cliente.total_compro - cliente.total_cobrado;

        return {
            id_cliente: cliente.id_cliente,
            cliente: cliente.cliente,
            total_compro: parseFloat(cliente.total_compro.toFixed(2)),
            total_cobrado: parseFloat(cliente.total_cobrado.toFixed(2)),
            total_debe: parseFloat(debe.toFixed(2))
        };
    });

    // 3. Ordenar alfabéticamente por nombre de cliente
    return resultadoFinal.sort((a, b) => a.cliente.localeCompare(b.cliente));
}

// ========================================
// EJECUCIÓN CON TUS DATOS
// ========================================


    async function verDetalle(id) {
        try {
            // 🔥 OBTENER IDS PENDIENTES
            const respIds = await fetch(
                `/myvet/app/controllers/accesoController.php?action=get_ids_pendientes_venta&venta_id=${id}`
            );
            const resNAlmacen = await fetch(
                `/myvet/app/controllers/accesoController.php?action=obtener_id_almacen&id=${id}`
            );

            const dataAlmacen = await resNAlmacen.json();
            const almacen_id_conseguido = dataAlmacen.almacen.almacen_id;
            console.log(dataAlmacen.almacen.almacen_id);

            const dataIds = await respIds.json();

            console.log(dataIds.ids);

            // =====================================================
            // 🔥 HABILITAR / DESHABILITAR BOTÓN
            // =============function agruparVentasPorCliente(ventas) {========================================

            if (
                Array.isArray(dataIds.ids) &&
                dataIds.ids.length > 0

            ) {
                console.log('hola');
                $('#btnGestionVenta')
                    .removeClass('d-none')
                    .prop('disabled', false)
                    .attr(
                        'onclick',
                        `abrirModalDespachoVentaTotal(${id}, ${almacen_id_conseguido})`
                    );

            } else {

                $('#btnGestionVenta')
                    .addClass('d-none')
                    .prop('disabled', true)
                    .removeAttr('onclick');

            }
            const res = await fetch(`${URL_CONTROLLER}?action=obtenerDetalle&id=${id}`);
           cargarRepartos(id);
            const data = await res.json();
           
            
            ventaActual = data;
            

            $('#spanFolio').text(data.info.folio);
            $('#detCliente').text(data.info.nombre_comercial);
            $('#detAlmacen').text(data.info.almacen);

            const total = parseFloat(data.info.total) || 0;
            const pagado = parseFloat(data.info.total_pagado) || 0;
            const deuda = total - pagado;
            $('#detTotalLabel').text('$' + total.toFixed(2));

            if (deuda <= 0) {
                $('#detSaldoLabel').text('LIQUIDADO').removeClass('text-danger').addClass('text-success');
                $('#btnAbonar').addClass('d-none');
            } else {
                $('#detSaldoLabel').text('$' + deuda.toFixed(2)).removeClass('text-success').addClass(
                    'text-danger');
                $('#btnAbonar').removeClass('d-none');
            }
            $('#folioFactura').text(data.info.factura);
if (data.info.estado_general === 'cancelada') {
    
                    $('#btnAbonar')
                    .addClass('d-none')
                    .prop('disabled', true)
                    .removeAttr('onclick'); 
    $('#cancelado').text(`Cancelada por: ${data.info.observaciones}`);
} else {
    $('#cancelado').text('');
   
}
            // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
            // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
            $('#tbodyDetalle').html(data.productos.map(p => {
                let cant = parseFloat(p.cantidad) || 0;
                let pendiente = (cant - (parseFloat(p.cantidad_entregada) || 0)).toFixed(3);

                let factor = parseFloat(p.factor_conversion) || 1;
                let cantPendiente = pendiente / factor;

                let pen = Number(pendiente);
                let pendi = Number(cantPendiente);
                let disponible = (p.disponible / factor);
                console.log(disponible);
                let entregada = p.cantidad_entregada / factor;

                console.log({
                    pen,
                    tipo: typeof pen,
                    comparacion: pen > 0
                });
                // 1. Definimos qué se verá en la columna "Venta"
                let visualizacionVenta = "";
                let infoEquivalenciaSub = "";
                let unm = (parseFloat(p.cantidad_entregada) / (1 / parseFloat(p.equivalencia)));
                console.log(unm);
                unm = unm % 1 !== 0 ? unm.toFixed(0) : unm;
                if (factor > 1 && cant >= factor) {
                    // Si alcanza el factor (Ej: 20 bultos >= 20 factor)
                    let unidadesMayores = (cant / factor);
                    // Formateamos para que si es entero no muestre .00 (Ej: 1 en vez de 1.00)
                    let totalUnidadesStr = Number.isInteger(unidadesMayores) ? unidadesMayores :
                        unidadesMayores.toFixed(2);


                    // Lo que se verá grande en la celda
                    visualizacionVenta =
                        `<span class="fw-bold">${totalUnidadesStr} ${p.unidad_reporte}</span> <br> <small class="card-title-text">(${cant} ${p.unidad_medida})</small>`;

                    // Leyenda pequeña debajo del nombre del producto (opcional, para referencia)
                    infoEquivalenciaSub =
                        `<div class="card-title-text small" style="font-size: 0.65rem;">1 ${p.unidad_reporte} = ${factor} ${p.unidad_medida}</div>`;
                } else {
                    // Si no llega al factor (Ej: 10 bultos) mostramos la unidad normal
                    //agregar observaciones en ticket 
                    visualizacionVenta = `<span>${cant} ${p.unidad_medida}</span>`;
                }


                return `<tr>
        <td>
            <div class="fw-bold card-title-text">${p.producto}</div>
            ${infoEquivalenciaSub}
        </td>
        <td class="text-center">
        ${cant} ${cant/factor>=1?p.unidad_reporte:p.unidad_medida} 
      
        (${ p.equivalencia>=1?cant/(1/p.equivalencia).toFixed(2):(cant*(p.equivalencia)).toFixed(2)} ${p.nombre})
            
        </td>
        <td class="text-center">${entregada>1?entregada+ p.unidad_reporte:p.cantidad_entregada +p.unidad_medida}</td>
        
        <td class="text-center text-danger fw-bold">${(cantPendiente>=1?cantPendiente.toFixed(3):pen)} ${cantPendiente>=1?p.unidad_reporte:p.unidad_medida}</td>
         <td class="text-center col-input d-none">
            ${pen.toFixed(4) > 0 ? 
                `<input type="number"
    class="form-control form-control-sm input-entrega1 mx-auto"
    max="${pen<=p.disponible ? (pendi>=1 ? pendi : pen) : (disponible>1 ? disponible : p.disponible)}"
    min="0"
    step="0.01"
    value="0.00"
    data-dvid="${p.dvid}"
    data-id="${p.producto_id}"
    data-factor="${(pendi>=1 && disponible>=1) ? factor : 1}"
    style="width:70px">
                   <input type="hidden" class="form-control form-control-sm input-entrega mx-auto" 
                    value="0"data-dvid=${p.dvid} data-id="${p.producto_id}" style="width:70px"step="0.01" min="0">
                     <span class="badge bg-success">${
                    (pendi>=1&& disponible>=1)?p.unidad_reporte:p.unidad_medida}</span>` 
                     
                : '<span class="badge bg-success">Completo</span>'}
        </td>
    </tr>`;
            }).join(''));
            // ... (dentro de verDetalle, después de renderizar historial de entregas)
            $('#tbodyHistorial').html(data.historial.length > 0 ? data.historial.map(h => {
                    // 1. Extraemos los valores del historial
                    // Si salen vacíos o undefined, es que el PHP no los está mandando en el JSON de historial
                    let cantH = parseFloat(h.cantidad) || 0;
                    let factorH = parseFloat(h.factor_conversion) || 1;
                    let uReporteH = h.unidad_reporte || '';
                    let uMedidaH = h.unidad_medida || '';

                    let visualizacionHistorial = "";

                    // 2. Aplicamos la misma lógica que usas arriba
                    if (factorH > 1 && cantH >= factorH) {
                        let unidadesMayoresH = (cantH / factorH);
                        let totalUnidadesStrH = Number.isInteger(unidadesMayoresH) ?
                            unidadesMayoresH :
                            unidadesMayoresH.toFixed(2);

                        visualizacionHistorial = `
            <span class="fw-bold text-primary">${totalUnidadesStrH} ${uReporteH}</span> 
            <br> <small class="card-title-text">(${cantH} ${uMedidaH})</small>
        `;
                    } else {
                        // Aquí verás si unidad_medida viene vacío desde la base de datos
                        visualizacionHistorial = `<span>${cantH} ${uMedidaH}</span>`;
                    }

                    return `
    <tr>
        <td class="small">${h.fecha}</td>
        <td class="small">${h.usuario_nombre}</td>
        <td>
            <div class="fw-bold" style="font-size:0.85rem;">${h.producto}</div>
        </td>
        <td class="text-center">
            ${visualizacionHistorial}
        </td>
    </tr>`;
                }).join('') :
                '<tr><td colspan="4" class="text-center card-title-text p-3">No hay entregas registradas</td></tr>');


            // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
            if (data.pagos && data.pagos.length > 0) {
                $('#tbodyPagos').html(data.pagos.map(p => `
        <tr>
            <td class="small">${p.fecha}</td>
            <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
            <td>
                <span class="badge bg-light  border fw-normal">${p.metodo_pago}</span>
                <div class="card-title-text" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
            </td>
             <td>
            <span>
    ${
       
             (p.referencia ?? '')
           
    }
</span> 
            </td>
        </tr>
    `).join(''));
            } else {
                $('#tbodyPagos').html(
                    '<tr><td colspan="3" class="text-center card-title-text p-3">No hay abonos registrados</td></tr>'
                );
            }
            alternarModo(false);
            modalObj.show();
        } catch (error) {
            console.error("Error al obtener detalle:", error);
        }
    }
    document.addEventListener('input', e => {

      if (e.target.classList.contains('input-entrega1')) {

    const max = parseFloat(e.target.max) || 0;
    const min = parseFloat(e.target.min) || 0;
    const factor = parseFloat(e.target.dataset.factor) || 1;

    let value = e.target.value;

    // 👉 PERMITIR BORRADO COMPLETO
    if (value === "") {
        const contenedor = e.target.parentElement;
        const inputEntrega = contenedor.querySelector('.input-entrega');

        if (inputEntrega) {
            inputEntrega.value = "";
        }
        return; // 🔥 importante: no seguir procesando
    }

    value = parseFloat(value);

    if (isNaN(value)) return;

    if (value > max) value = max;
    if (value < min) value = min;

    e.target.value = value;

    const contenedor = e.target.parentElement;
    const inputEntrega = contenedor.querySelector('.input-entrega');

    if (inputEntrega) {
        inputEntrega.value = (value * factor).toFixed(2);
    }
}
    });
    async function cargarRepartos(idVenta) {

    const resp = await fetch(
        `/myvet/app/controllers/repartosController.php?action=get_repartos_venta&id=${idVenta}`
    );

    const repartoViaje = await resp.json();

    const tbody = document.getElementById('tbodyRepartos');
    tbody.innerHTML = '';

    if (!repartoViaje.success) return;

    // ================================
    // AGRUPAR POR FOLIO VIAJE
    // ================================
    const grupos = {};
   
    repartoViaje.data.forEach(item => {

        if (!grupos[item.folio_viaje]) {

            grupos[item.folio_viaje] = {
                folio_viaje: item.folio_viaje,
                fecha_viaje: item.fecha_viaje,
                estatus_logistico: item.estatus_logistico,
                productos: [],
                clientes: new Set()
            };
        }

        grupos[item.folio_viaje].productos.push(item.productos);
        grupos[item.folio_viaje].clientes.add(item.cliente);
    });

    // ================================
    // RENDER TABLA
    // ================================
    Object.values(grupos).forEach(g => {

        const estadoClass =
            g.estatus_logistico === 'completado'
                ? 'bg-success'
                : 'bg-warning ';

        const tr = `
            <tr>

                <td class="fw-bold">
                    ${g.folio_viaje}
                </td>

                <td>
                    ${g.fecha_viaje}
                </td>

                <td>
                    <span class="badge ${estadoClass}">
                        ${g.estatus_logistico}
                    </span>
                </td>

                <td class="text-center">

                    <button class="btn btn-sm btn-outline-primary"
                      onclick="imprimirRuta('${idVenta}','${g.folio_viaje}')">

                      
                        Ver Reparto 
                    </button>

                </td>

            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', tr);
    });
}
    
    const modalNuevoAbonoObj = new bootstrap.Modal('#modalNuevoAbono');



    function togglePerso() {
        $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
        getVentas();
    }

    function alternarModo(e) {
        $('.col-input').toggleClass('d-none', !e);
        $('#btnHabilitar').toggle(!e && ventaActual.info.estado_entrega !== 'entregado');
        $('#controlesGuardar').toggleClass('d-none', !e);
    }

    $(document).ready(function() {
        // 1. Carga inicial de datos
        getVentas();

        // 2. Escuchadores para filtros (opcional, pero recomendado para centralizar)
        $('#f_rango').on('change', togglePerso);
        // getVentas ya se llama mediante onchange/onkeyup en tu HTML, lo cual está bien.

        console.log("Sistema de historial listo.");
    });
    </script>
    <script>

   </script>
</body>

</html>