<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Pagos | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

   <style>
    /* Variables de tema dinámicas y adaptables */
    :root {
        --sidebar-width: 250px;
        --card-bg: var(--bs-body-bg, #ffffff);
        --card-border: var(--bs-border-color-translucent, rgba(0, 0, 0, 0.125));
        --table-header-bg: #1e293b;
        --table-header-color: #f8fafc;
        --accent-glow: rgba(99, 102, 241, 0.15);
    }

    /* Soporte para Tema Oscuro Automático o Manual (Bootstrap 5 dark mode) */
    [data-bs-theme="dark"] {
        --table-header-bg: #0f172a;
        --table-header-color: #f1f5f9;
        --accent-glow: rgba(129, 140, 248, 0.25);
    }

    body {
      
        color: var(--bs-body-color);
        overflow-x: hidden;
        padding-top: 20px;
        text-transform: uppercase !important;
    }

    .main-content {
        
        padding: 2rem;
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    /* Cards y Contenedores */
    .filter-card {
        background-color: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(8px);
    }

    .scroll-table {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        overflow: hidden;
    }

    /* Tablas Modernas y Oscuras */
    .table {
        --bs-table-bg: transparent;
        color: var(--bs-body-color);
    }

    .table thead th {
        background-color: var(--table-header-bg) !important;
        color: var(--table-header-color) !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 14px 12px;
        
    }

    .table-hover tbody tr:hover {
        background-color: var(--accent-glow) !important;
    }

    /* Modales */
    .modal-content {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--card-border);
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background-color: var(--table-header-bg) !important;
        color: var(--table-header-color);
        border-bottom: 1px solid var(--card-border);
    }

    /* Botón Animado Neón (Adaptado a Modo Oscuro) */
    .btn-animado-entrega {
        position: relative;
        overflow: hidden;
        color: #fff !important;
        font-weight: 600;
        letter-spacing: .5px;
        
        transition: all .25s ease;
        background: linear-gradient(270deg, #6366f1, #ec4899, #f97316, #3b82f6, #6366f1);
        background-size: 400% 400%;
        animation: moverGradiente 8s ease infinite;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
    }

    .btn-animado-entrega:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(236, 72, 153, 0.45);
    }

    .btn-animado-entrega:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .btn-animado-entrega::before {
        content: '';
        position: absolute;
        top: 0;
        left: -120%;
        width: 80%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .4), transparent);
        animation: brillo 3s linear infinite;
    }

    .btn-action {
        background-color: #6366f1;
        color: #ffffff;
        
        border-radius: 8px;
        transition: background-color 0.2s;
    }

    .btn-action:hover {
        background-color: #4f46e5;
        color: #ffffff;
    }

    .input-entrega {
        border: 2px solid #10b981 !important;
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        max-width: 90px;
        text-align: center;
        font-weight: bold;
        border-radius: 6px;
    }

    @keyframes moverGradiente {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes brillo {
        0% { left: -120%; }
        100% { left: 140%; }
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }
    }

    .swal2-container {
        z-index: 9999 !important;
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
                <h3 class="fw-bold card-title-text m-0">Historial de Pagos</h3>
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
                       
                        <div class="col-md-2">
                            <label for="select-usuarios"
                                class="form-label fw-bold small text-body-secondary text-uppercase">Vendedor</label>
                            <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id"
                                onchange="getVentas()">
                                <option value=""> Seleccione receptor</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Cliente</label>
                            <select id="cliente_id" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                               
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
                                <option value="">Todas</option>
                                <?php foreach($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"
                                    <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                    <?= $a['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                       
                    </div>
                </div>
            </div>

            <div class="scroll-table shadow-sm">
                <div class="table-responsive" style="max-height: 60vh;">
                    <table class="table table-hover align-middle mb-0" id="tablaVentas">
                        <thead>
                            <tr>

<th>ALMACEN</th>
                                <th class="ps-3">Fecha</th>
                                <th>ID PAGO</th>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Receptor</th>
                                <th>METODO PAGO</th>
                                <th>REFERENCIA</th>
                                <th>Total RECIBIDO</th>
                                <th>Total Compra</th>
                               
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="modal fade" id="modalDetalle" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Gestión de Venta: <span id="spanFolio"></span></h6>
                        <span id="IdFolio" style="visibility: hidden;"></span>
                        <span id="Almacen_id" style="visibility: hidden;"></span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="row g-0">
                            <div class="col-md-3  border-end p-4">
                                <div class="d-flex flex-column gap-2 mb-4">

                                    <!-- Cliente -->
                                    <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                        <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                            style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                            <i class="bi bi-person me-1 text-primary"></i> Cliente
                                        </small>
                                        <span id="detCliente"
                                            class="fw-bold card-title-text small d-block text-truncate">--</span>
                                    </div>

                                    <!-- Almacén -->
                                    <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                        <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                            style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                            <i class="bi bi-box-seam me-1 text-primary"></i> Almacén
                                        </small>
                                        <span id="detAlmacen"
                                            class="fw-bold card-title-text small d-block text-truncate">--</span>
                                    </div>

                                    <!-- Vendedor -->
                                    <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                        <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                            style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                            <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                                        </small>
                                        <span id="detVendedor"
                                            class="fw-bold card-title-text small d-block text-truncate">--</span>
                                    </div>

                                    <!-- Folio Factura -->
                                    <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                        <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                            style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                            <i class="bi bi-receipt me-1 text-primary"></i> Folio / Factura
                                        </small>
                                        <span id="folioFactura"
                                            class="fw-bold card-title-text small d-block text-truncate">--</span>
                                    </div>

                                </div>



                                <div class="mb-4 p-2  border rounded shadow-sm text-center">
                                    <div class="mb-2 pb-2 border-bottom">
                                        <span class="d-block small text-body-secondary text-uppercase fw-bold">Total de
                                            Venta</span>
                                        <span id="detTotalLabel" class="h6 fw-bold card-title-text">$0.00</span>
                                    </div>

                                    <div>
                                        <span class="d-block small text-body-secondary text-uppercase fw-bold">Saldo
                                            Pendiente</span>
                                        <span id="detSaldoLabel" class="h5 fw-bold text-danger">$0.00</span>
                                    </div>
                                </div>

                                <?php if($_SESSION['rol_id']==1||$_SESSION['rol_id']==2): ?>
                                <div id="contenedorBoton">
                                    <button id="btnHabilitar" class="btn btn-action w-100 mb-2 py-2 fw-bold" onclick="abrirModalDespachoVentaTotal(
            $('#Almacen_id').text(),
            $('#IdFolio').text()
        )">
                                        Nueva Entrega
                                    </button>
                                </div>
                                <?php endif; ?>
                                <!-- <button id="btnAbonar" class="btn btn-primary w-100 mb-2 py-2 fw-bold"
                                    onclick="abrirFlujoAbono()">
                                    <i class="bi bi-cash"></i> Registrar Abono
                                </button> -->


                                <div class="text-end pe-3">



                                </div>



                            </div>
                            <div class="col-md-9 p-4">
                                <div class="table-responsive border rounded mb-3" style="max-height: 180px;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-uppercase">
                                                <th>Producto</th>
                                                <th class="text-center">Venta</th>
                                                <th class="text-center">Surtido</th>

                                                <th class="text-center text-danger">Falta</th>
                                                <th class="text-center col-input d-none">Entrega</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyDetalle" class="small"></tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="small fw-bold text-uppercase text-body-secondary"><i class="bi bi-truck"></i>
                                            Historial de Entregas</h6>
                                        <div class="table-responsive border rounded" style="max-height: 180px;">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="small text-uppercase">
                                                        <th>Fecha</th>
                                                        <th>Responsable</th>
                                                        <th>Producto</th>
                                                        <th class="text-center">Cant</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyHistorial" class="small"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="small fw-bold text-uppercase text-body-secondary"><i
                                                class="bi bi-cash-stack"></i>
                                            Historial de Pagos</h6>
                                        <div class="table-responsive border rounded" style="max-height: 180px;">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="small text-uppercase">
                                                        <th>Fecha</th>
                                                        <th>Monto</th>
                                                        <th>Método</th>
                                                        <th>Referencia</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyPagos" class="small"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <h6 class="small fw-bold text-uppercase text-body-secondary">
                                            <i class="bi bi-map"></i>
                                            Repartos
                                        </h6>

                                        <div class="table-responsive border rounded" style="max-height: 220px;">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="small text-uppercase">
                                                        <th># Reparto</th>
                                                        <th>Fecha Entrega</th>
                                                        <th>Direccion</th>
                                                        <th class="text-center">Ruta</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="tbodyRepartos" class="small">

                                                    <!-- ejemplo -->



                                                </tbody>
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
       
        <div class="modal fade" id="modalCancelarVenta" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Cancelar Venta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="cancelar_id_venta">

                        <div class="mb-3">
                            <label class="form-label">Motivo de la cancelación</label>
                            <textarea id="cancelar_motivo" class="form-control text-uppercase" rows="4"
                                placeholder="Escriba el motivo..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="procesarCancelacion(true)">
                            Con Saldo a Favor
                        </button>

                        <button class="btn btn-danger" onclick="procesarCancelacion(false)">
                            Sin Saldo
                        </button>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Regresar
                        </button>
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
        <?php require_once __DIR__ . '/ventasHistorialModales/registarAbono.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/editarVentaModal.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/modalImprimirRuta.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/modalSolicitudCancelacion.php'; ?>
        <?php require_once __DIR__ . '/entregasComponets/modalEntregaVentas.php'; ?>

        <script>
        let modalCancelarVenta;

        document.addEventListener('DOMContentLoaded', () => {
            modalCancelarVenta = new bootstrap.Modal(
                document.getElementById('modalCancelarVenta')
            );
        });
        async function cargarClientes() {
    console.log("cargo clientes");
    
    // Obtenemos el ID del almacén actual
    const almacenId = $('#f_almacen').val()??0;
    const select = document.getElementById('cliente_id');
    if (!select) return;

    // Limpiamos el select antes de poblarlo
    select.innerHTML = '<option value="">-- Seleccione un cliente --</option>';

    try {
         const url = `/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${almacenId}`;
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();
        console.log(resultado);

        if (resultado.success && Array.isArray(resultado.data)) {
            
            // FILTRADO APLICADO SOBRE resultado.data:
            // 1. Conserva clientes cuyo nombre NO contenga "público en general".
            // 2. Si contiene "público en general", solo lo conserva si su almacen_id coincide con el actual.
            const clientesFiltrados = resultado.data.filter(cliente => {
                const nombre = (cliente.nombre_comercial || '').toLowerCase();
                const esPublicoGeneral = nombre.includes('público en general') || nombre.includes('publico en general');

                if (!esPublicoGeneral) {
                    return true;
                }
                
                // Asumiendo que el objeto cliente tiene la propiedad 'almacen_id'
                return String(cliente.almacen_id) === String(almacenId);
            });
            

            // Llenamos el select con la lista filtrada
            (resultado.data).forEach(cliente => {
                const opcion = document.createElement('option');
                opcion.value = cliente.id;
                opcion.textContent = `${cliente.nombre_comercial}`;
                select.appendChild(opcion);
            });

          

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los clientes</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarClientes:', error);
    }
}
        function abrirModalCancelacion(idVenta, folio) {

            document.getElementById('cancelar_id_venta').value = idVenta;
            document.getElementById('cancelar_motivo').value = '';

            document.querySelector('#modalCancelarVenta .modal-title').innerHTML =
                `Cancelar Venta ${folio}`;

            modalCancelarVenta.show();
        }
        async function procesarCancelacion(conSaldo) {

            const idVenta = document.getElementById('cancelar_id_venta').value;
            const motivo = document.getElementById('cancelar_motivo').value.trim();

            if (!motivo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo requerido',
                    text: 'Debe capturar el motivo de la cancelación'
                });
                return;
            }

            modalCancelarVenta.hide();

            const accion = conSaldo ?
                'cancelarVenta' :
                'cancelarVentaSinSaldo';

            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {

                const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_venta: idVenta,
                        motivo: motivo
                    })
                });

                const res = await response.json();

                if (res.status === 'success') {

                    Swal.fire({
                        icon: 'success',
                        title: 'Venta cancelada',
                        text: res.message
                    });

                    getVentas();

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message
                    });

                }

            } catch (error) {

                console.error(error);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo conectar con el servidor'
                });
            }
        }
        // PASO 1: Esta función se dispara al dar click al botón de la tabla (abre el modal)
     
        // Tu// Función final encargada del backend
        async function agregarFactura(id, folio) {
            console.log(`Guardando en BD -> ID: ${id}, Folio Factura: ${folio}`);

            // 1. Creamos el objeto FormData y le inyectamos los datos que necesita el controlador PHP
            const data = new FormData();
            data.append('venta_id', id);
            data.append('factura', folio);

            try {
                // Asumiendo que URL_CONTROLLER es tu constante global (ej: '../controllers/ventasController.php')
                const res = await fetch(
                    `/myvet/app/controllers/ventasHistorialController.php?action=guardarFactura`, {
                        method: 'POST',
                        body: data // Enviamos el FormData con los valores
                    });

                // Verificamos si la respuesta del servidor es un JSON válido
                const result = await res.json();

                if (result.status === 'success') {

                    // Ojo: Si usaste la instancia limpia que te pasé en el paso anterior, 
                    // puedes cerrar el modal de Bootstrap 5 así si no tienes 'modalObj' global:
                    const modalElement = document.getElementById('modalAgregarFactura');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) modalInstance.hide();

                    // Recargamos la tabla principal de ventas
                    if (typeof getVentas === 'function') getVentas();

                    // Alerta de éxito con SweetAlert2
                    Swal.fire({
                        title: '¡Listo!',
                        text: 'Factura guardada correctamente',
                        icon: 'success',
                        timer: 1000, // Subí a 1000ms (1 segundo) para que el usuario alcance a notar la palomita de éxito
                        showConfirmButton: false
                    });

                    // 🔥 Volver a abrir automáticamente el detalle si es necesario
                    setTimeout(() => {
                        // Usamos el 'id' que entró originalmente por parámetro a esta función
                        if (typeof verDetalle === 'function') {
                            verDetalle(id);
                        }
                    }, 1005);

                } else {
                    // Aquí manejamos errores devueltos por el backend (Excepciones del try/catch de tu PHP)
                    Swal.fire('No se pudo guardar', result.message || 'Error desconocido', 'error');
                }

            } catch (e) {
                console.error("Error al procesar la factura:", e);
                Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
            }
        }

        // Esta es la función que necesitas que se ejecute:


        cargarUsuariosSelect();
        async function cargarUsuariosSelect() {
            const almacen = $('#f_almacen').val();
            const select = document.getElementById('select-usuarios');
            if (!select) return; // Seguridad por si el select no está en la vista actual

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacen}`;
        const respuesta = await fetch(url);

               
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
        const URL_CONTROLLER = '/myvet/app/controllers/historialPagosController.php';

        async function getVentas() {
            cargarClientes();
            $('#loader').removeClass('d-none');


            const params = new URLSearchParams({
                action: 'listar',
                // <--- Nuevo parámetro para el ID de venta
                f_search: $('#f_search').val(),
                f_rango: $('#f_rango').val(),
                f_inicio: $('#f_ini').val(),
                f_fin: $('#f_fin').val(),
                f_almacen: $('#f_almacen').val(),
                
                f_cliente: $('#cliente_id').val(),
                f_vendedor: $('#select-usuarios').val() ?? '',
               


            });

            try {
                const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
                const data = await res.json();
                console.log(data);
                //<td class="ps-3 small">${v.id}</td>
                let totalVendido = 0;
                let deuda = 0;

               let totalMontoCobrado = 0;

// Generar las filas de la tabla de pagos
let filasHtml = data.map(p => {
    let monto = parseFloat(p.monto) || 0;
    let ventaTotal = parseFloat(p.venta_total) || 0;
    
    // Acumulador de totales
    totalMontoCobrado += monto;

    // Formato de referencia y badge de método de pago
    let referencia = p.referencia ? `<span class="fw-bold">${p.referencia}</span>` : '<span class="text-muted small">N/A</span>';
    
    let badgeMetodo = `<span class="badge ${p.metodo_pago === 'Efectivo' ? 'bg-success' : 'bg-info'} text-dark">
        ${p.metodo_pago}
    </span>`;

    return `<tr>
      <td class="ps-3 small">${p.almacen_nombre}</td>
        <td class="ps-3 small">${p.fecha}</td>
        <td class="fw-bold">#${p.id}</td>
        <td class="fw-bold text-primary">${p.folio}</td>
       
        <td><div class="small">${p.cliente}</div></td>
        <td><div class="small">${p.nombre}</div></td>
         <td>${badgeMetodo}</td>
        <td><div class="small">${referencia}</div></td>
         <td class="fw-bold text-success">$${monto.toFixed(2)}</td>
        <td class="fw-bold text-secondary">$${ventaTotal.toFixed(2)}</td>
       

        <td class="text-end pe-3">
            <button type="button" 
                    class="btn btn-sm btn-outline-primary rounded-3 " 
                    onclick="verDetalle(${p.venta_id})" 
                    data-bs-toggle="tooltip" 
                    title="Ver detalle de la venta">
                <i class="bi bi-eye-fill fs-6"></i>
            </button>
        </td>
    </tr>`;
}).join('');

// Insertar las filas principales
$('#tablaVentas tbody').html(filasHtml);

// Generar e insertar la fila de totales
let filaTotales = `<tr class="table-light fw-bold border-top border-dark">
    <td class="ps-3" colspan="6">TOTAL COBRADO EN HISTORIAL</td>
    <td class="text-success">$${totalMontoCobrado.toFixed(2)}</td>
    <td></td>
</tr>`;

$('#tablaVentas tbody').append(filaTotales);
            } catch (e) {
                console.error("Error al cargar ventas:", e);
            } finally {
                $('#loader').addClass('d-none');
            }
        }
        async function verDetalle(id) {
            try {
                // 🔥 OBTENER IDS PENDIENTES
                const respIds = await fetch(
                    `/myvet/app/controllers/entregasController.php?ajax=get_ids_pendientes_venta&venta_id=${id}`
                );
                const resNAlmacen = await fetch(
                    `/myvet/app/controllers/entregasController.php?ajax=obtener_id_almacen&id=${id}`
                );

                const dataAlmacen = await resNAlmacen.json();
                const almacen_id_conseguido = dataAlmacen.almacen.almacen_id;
                console.log(dataAlmacen.almacen.almacen_id);

                const dataIds = await respIds.json();

                console.log(dataIds.ids);

                // =====================================================
                // 🔥 HABILITAR / DESHABILITAR BOTÓN
                // =====================================================

                if (
                    Array.isArray(dataIds.ids) &&
                    dataIds.ids.length > 0

                ) {


                } else {

                    $('#btnGestionVenta')
                        .addClass('d-none')
                        .prop('disabled', true)
                        .removeAttr('onclick');

                }
                const res = await fetch(`${URL_CONTROLLER}?action=obtenerDetalle&id=${id}`);
                cargarRepartos(id);
                const data = await res.json();
                console.log(data);


                ventaActual = data;
                $('#folioFactura').text(data.info.factura);
                if (data.info.estado_general === 'cancelada') {
                    $('#btnGestionVenta')
                        .addClass('d-none')
                        .prop('disabled', true)
                        .removeAttr('onclick');
                    $('#btnAbonar')
                        .addClass('d-none')
                        .prop('disabled', true)
                        .removeAttr('onclick');
                    $('#btnHabilitar')
                        .addClass('d-none')
                        .prop('disabled', true)
                        .removeAttr('onclick');
                    $('#cancelado').text(`Cancelada por: ${data.info.observaciones}`);
                } else {
                    $('#cancelado').text('');
                }
                $('#spanFolio').text(data.info.folio);
                $('#IdFolio').text(data.info.id);
                $('#Almacen_id').text(data.info.almacen_id);

                $('#detCliente').text(data.info.nombre_comercial);
                $('#detAlmacen').text(data.info.almacen);
                $('#detVendedor').text(data.info.vendedor);

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

                // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
                // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
                $('#tbodyDetalle').html(data.productos.map(p => {
                    console.log(p);
                    let cant = parseFloat(p.cantidad) || 0;
                    let pendiente = (cant - (parseFloat(p.cantidad_entregada) || 0)).toFixed(3);

                    let factor = parseFloat(p.factor_conversion) || 1;
                    let cantPendiente = pendiente / factor;

                    let pen = Number(pendiente / (1 / p.equivalencia));
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
                            `<span class="fw-bold">${totalUnidadesStr} ${p.unidad_reporte}</span> <br> <small class="text-body-secondary">(${cant} ${p.unidad_medida})</small>`;

                        // Leyenda pequeña debajo del nombre del producto (opcional, para referencia)
                        infoEquivalenciaSub =
                            `<div class="text-body-secondary small" style="font-size: 0.65rem;">1 ${p.unidad_reporte} = ${factor} ${p.unidad_medida}</div>`;
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
        ${ p.equivalencia>=1?cant/(1/p.equivalencia).toFixed(2):(cant*(p.equivalencia)).toFixed(2)} ${p.nombre}
        
      
        (${cant} ${p.unidad_medida})
            
        </td>
        <td class="text-center">
        
      
        ${entregada>1?entregada+' '+ p.unidad_reporte:
        (p.cantidad_entregada/(1/p.equivalencia))>=1?(p.cantidad_entregada/(1/p.equivalencia)).toFixed(3) +' '+ p.nombre:
        p.cantidad_entregada +' '+p.unidad_medida}</td>
        
        <td class="text-center text-danger fw-bold">${(cantPendiente>=1?cantPendiente.toFixed(3):pen.toFixed(3))} ${cantPendiente>=1? p.unidad_reporte:p.cantidad/(1/p.equivalencia)>1?p.nombre:p.unidad_medida}</td>
         <td class="text-center col-input d-none">
            ${pen.toFixed(4) > 0 ? 
                `<input type="number"
    class="form-control form-control-sm input-entrega2 mx-auto"
    max="${pen<=p.disponible ? (pendi>=1 ? pendi : pen) : (disponible>1 ? disponible : p.disponible)}"
    min="0"
    step="0.01"
    value="0.00"
    data-dvid="${p.dvid}"
    data-id="${p.producto_id}"
    data-factor="${(pendi>=1 && disponible>=1) ? factor : 1}"
    style="width:70px">
                   <input type="hidden" class="form-control form-control-sm input-entrega0 mx-auto" 
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
                        console.log((h.cantidad / (1 / h.equivalencia)) >= 1 ? (h.cantidad / (1 / h
                            .equivalencia)).toFixed(3) : cantH);
                        // 2. Aplicamos la misma lógica que usas arriba

                        // Aquí verás si unidad_medida viene vacío desde la base de datos
                        visualizacionHistorial =
                            `<span>${(h.cantidad/(1/h.equivalencia))>=1?(h.cantidad/(1/h.equivalencia)).toFixed(3):cantH} ${(h.cantidad/(1/h.equivalencia))>=1?(h.nombre):uMedidaH}</span>`;

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
                    '<tr><td colspan="4" class="text-center text-body-secondary p-3">No hay entregas registradas</td></tr>'
                    );


                // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
                if (data.pagos && data.pagos.length > 0) {
                    $('#tbodyPagos').html(data.pagos.map(p => `
        <tr>
            <td class="small">${p.fecha}</td>
            <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
            <td>
                <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago} </span>
               
                <div class="text-body-secondary" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
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
                        '<tr><td colspan="3" class="text-center text-body-secondary p-3">No hay abonos registrados</td></tr>'
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
                `/myvet/app/controllers/repartosController.php?action=get_repartos_entrega&id=${idVenta}`
            );


            const repartoViaje = await resp.json();
            let repartos = repartoViaje.data;
            console.log(repartoViaje);

            const tbody = document.getElementById('tbodyRepartos');
            tbody.innerHTML = '';

            if (!repartoViaje.success) return;

            // ================================
            // AGRUPAR POR FOLIO VIAJE
            // ================================

            // ================================
            // RENDER TABLA
            // ================================
            repartos.forEach(g => {

                const estadoClass =
                    g.estatus_logistico === 'completado' ?
                    'bg-success' :
                    'bg-warning card-title-text';

                const tr = `
            <tr>

                <td class="fw-bold">
                    ${g.entrega_id}
                </td>

                <td>
                    ${g.fecha}
                </td>

                <td>
                    <span >
                        ${g.direccion_entrega}
                    </span>
                </td>

                <td class="text-center">

                    <button class="btn btn-sm btn-outline-primary"
                      onclick="imprimirRuta('${g.entrega_id}','${g.folio}')">

                      
                        Ver Reparto 
                    </button>

                </td>

            </tr>
        `;

                tbody.insertAdjacentHTML('beforeend', tr);
            });
        }
        async function procesarEntrega() {
            const fd = new FormData();
            let ok = false;

            $('.input-entrega').each(function() {

                const cant = parseFloat($(this).val());

                console.log($(this).data('dvid'), cant);

                if (cant > 0) {

                    fd.append(
                        `productos[${$(this).data('dvid')}]`,
                        cant
                    );

                    ok = true;
                }
            });

            if (!ok) return Swal.fire('Atención', 'Indique al menos una cantidad válida para entregar', 'warning');

            fd.append('venta_id', ventaActual.info.id);

            try {
                const res = await fetch(`${URL_CONTROLLER}?action=guardarEntrega`, {
                    method: 'POST',
                    body: fd
                });

                // Verificamos si la respuesta del servidor es un JSON válido
                const result = await res.json();

                if (result.status === 'success') {

                    modalObj.hide();

                    getVentas();

                    Swal.fire({
                        title: '¡Listo!',
                        text: 'Entrega guardada correctamente',
                        icon: 'success',
                        timer: 500,
                        showConfirmButton: false
                    });

                    // 🔥 volver a abrir automáticamente
                    setTimeout(() => {

                        verDetalle(ventaActual.info.id);

                    }, 501);

                } else {
                    // AQUÍ MANEJAMOS EL ERROR DE STOCK (o cualquier otro error del Model)
                    // Usamos result.message que es el que trae "Stock insuficiente en almacén..."
                    Swal.fire('No se pudo entregar', result.message || 'Error desconocido', 'error');
                }

            } catch (e) {
                console.error("Error al procesar entrega:", e);
                Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
            }
        } // Instanciamos el nuevo modal
        const modalAbonoObj = new bootstrap.Modal('#modalAbono');



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
            cargarClientes();

            // 2. Escuchadores para filtros (opcional, pero recomendado para centralizar)
            $('#f_rango').on('change', togglePerso);
            // getVentas ya se llama mediante onchange/onkeyup en tu HTML, lo cual está bien.

            console.log("Sistema de historial listo.");
        });
        </script>
        <script>
        async function confirmarCancelacion(idVenta, folio, total, pagado) {

            // 1. Lanzamos el SweetAlert con las 3 opciones
            const result = await Swal.fire({
                title: `¿Cancelar Venta ${folio}?`,
                text: "Selecciona si deseas reintegrar el dinero al saldo del cliente o solo anular la venta.",
                icon: 'warning',
                input: 'text',
                inputLabel: 'Motivo de la cancelación',
                inputPlaceholder: 'Escriba por qué se cancela...',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#28a745', // Verde -> Con Saldo
                denyButtonColor: '#d33', // Rojo -> Sin Saldo
                cancelButtonColor: '#6c757d', // Gris -> Regresar
                confirmButtonText: '<i class="bi bi-cash-stack"></i> Con Saldo a Favor',
                denyButtonText: '<i class="bi bi-x-circle"></i> Sin Saldo',
                cancelButtonText: 'Regresar',
                inputValidator: (value) => {
                    if (!value) return '¡El motivo es obligatorio!';
                }
            });

            // 2. Si se presionó cualquiera de los dos botones de ejecución (Confirmar o Denegar)
            if (result.isConfirmed || result.isDenied) {
                // IMPORTANTE: Capturamos el motivo desde result.value
                const motivo = 'cancelacion';

                // Elegimos la ruta del controlador según el botón
                const accion = result.isConfirmed ? 'cancelarVenta' : 'cancelarVentaSinSaldo';

                Swal.fire({
                    title: 'Procesando...',
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false
                });

                try {
                    const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_venta: idVenta,
                            motivo: motivo
                        })
                    });

                    const res = await response.json();

                    if (res.status === 'success') {
                        await Swal.fire({
                            title: '¡Venta Cancelada!',
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Refrescamos la tabla de ventas
                        if (typeof getVentas === 'function') getVentas();

                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                } catch (error) {
                    console.error("Error en la petición:", error);
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                }
            }
        }
        </script>
        <script>
        // Selecciona todos los inputs de texto y también los textareas
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function() {
                // Convierte el valor a mayúsculas en tiempo real
                this.value = this.value.toUpperCase();
            });
        });
        </script>
</body>

</html>