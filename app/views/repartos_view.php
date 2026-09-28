<?php
/**
 * repartos_view.php 
 * Gestión de logística: Monitor de Viajes y Órdenes de Entrega
 */
$mi_almacen = intval($_SESSION['almacen_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logística | cfsistem</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
     <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
       <link href="/myvet/css/repartos.css" rel="stylesheet">
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        
        <div class="d-flex justify-content-between align-items-end mb-4 animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold m-0" style="font-size: 2.2rem; letter-spacing: -0.04em;">Centro de Logística</h2>
                <p class="text-body-secondary mb-0" style="font-size: 1.1rem;">Supervisión de entregas y control de flota en tiempo real.</p>
            </div>
            <div class=" rounded-4 p-3 shadow-sm border d-flex align-items-center gap-3" style="min-width: 200px;">
                <div class="text-primary fs-3"><i class="bi bi-truck-flatbed"></i></div>
                <div>
                    <small class="text-body-secondary fw-bold d-block" style="font-size: 0.6rem; letter-spacing: 0.05em;">ÓRDENES DE ENTREGA</small>
                    <span class="fs-4 fw-bold" id="count_pendientes">0</span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4 animate__animated animate__fadeInUp">
            <div class="col-md-5">
                <div class="card-premium p-2 px-3 mb-0 d-flex align-items-center shadow-sm" style="border-radius: 16px;">
                    <i class="bi bi-search text-body-secondary me-3 fs-5"></i>
                    <input type="text" id="buscarSalida" class="form-control  bg-transparent py-2 shadow-none" placeholder="Buscar por folio, cliente o producto...">
                </div>
            </div>
            
            <div class="col-md-4 d-flex align-items-center gap-2">
                <label class="form-label mb-0 fw-bold text-body-secondary small">Rango:</label>
                <input type="date" id="inicio" class="form-control shadow-sm" style="width:auto; border-radius: 12px;" value="<?=date('Y-m-d') ?>">
                <span class="text-body-secondary">-</span>
               <input type="date" id="fin" class="form-control shadow-sm" style="width:auto; border-radius: 12px;" value="<?= date('Y-m-d', strtotime('-1 month')) ?>"></div>

            <div class="col-md-3">
                <select id="filtroAlmacen" class="form-select-ios h-100 w-100 shadow-sm" onchange="cargarPendientes(); cargarMonitorViajes();cargarPendientes()" style="border-radius: 12px;">
                    <?php if ($es_admin): ?>
                        <option value="0">🌐 Todas las Sucursales</option>
                    <?php endif;?>
                    <?php if(isset($listaAlmacenes)) foreach($listaAlmacenes as $alm): ?>
                        <option value="<?= $alm['id'] ?>">📍 <?= $alm['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card card-premium card-monitor animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="header-monitor d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-uppercase small">
                    <i class="bi bi-broadcast me-2 text-primary animate-pulse-soft"></i> Monitor de Unidades en Tránsito
                </h6>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3 border-opacity-25" onclick="cargarMonitorViajes()">
                    <i class="bi bi-arrow-repeat me-1"></i> Actualizar Monitor
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-monitor align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Unidad / Folio Ruta</th>
                                <th>Chofer Responsable</th>
                                <th>Tripulación</th>
                                <th>Carga Consolidada</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="bodyMonitorViajes"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-premium animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card-header-ios d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-uppercase small" style="color: #424245;">
                    <i class="bi bi-stack me-2 text-primary"></i> Órdenes de Entrega y Patio
                </h6>
                <button class="btn btn-sm btn-light border rounded-pill px-3" onclick="cargarPendientes()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Sincronizar
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Folio / Fecha</th>
                            <th>Cliente</th>
                            <th>Producto / Detalle</th>
                            <th>Almacén Origen</th>
                            <th class="text-center">Estatus</th> 
                            <th class="text-end pe-4">Gestión</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPendientes"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top -subtle">
                <div class="small text-body-secondary fw-bold" id="pageIndicatorText" style="font-size: 0.7rem; letter-spacing: 0.05em;"></div>
                <nav><ul class="pagination pagination-sm mb-0" id="paginationBootstrap"></ul></nav>
            </div>
        </div>
         <?php require_once __DIR__ . '/entregasComponets/repartoModal.php'; ?>
    <?php require_once __DIR__ . '/entregasComponets/editarRepartoModal.php'; ?>
    <?php require_once __DIR__ . '/entregasComponets/minitordeHistorialDeReparto.php'; ?>
    <?php require_once __DIR__ . '/entregasComponets/modalVerEntrega.php'; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
   

    <script>
    window.cargarMonitorViajes = async function() {
        // ... (Tu código actual de cargarMonitorViajes no cambia) ...
        const body = $('#bodyMonitorViajes');
        const almacenId = $('#filtroAlmacen').val() || 0;
        try {
            body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div><div class="mt-2 text-body-secondary small">Consultando satélite...</div></td></tr>');
            const resp = await fetch(`/myvet/app/controllers/repartosController.php?action=listar_viajes_activos&almacen_id=${almacenId}`);
            const result = await resp.json();
            const data = result.data || result; 

            if (!data || data.length === 0) {
                body.html('<tr><td colspan="5" class="text-center py-5 text-body-secondary opacity-50"><i class="bi bi-geo-alt fs-2 d-block mb-2"></i> No hay unidades activas en ruta</td></tr>');
                return;
            }
            body.empty();
            data.forEach(v => {
                const listaAyudantes = v.tripulantes ? `<div class="small text-body-secondary fw-medium"><i class="bi bi-people-fill me-1 text-primary"></i> ${v.tripulantes}</div>` : `<span class="badge bg-light text-secondary fw-normal border" style="font-size:0.6rem;">Solo Conductor</span>`;
                body.append(`
                    <tr class="animate__animated animate__fadeIn border-bottom" style="border-color: #f2f2f7 !important;">
                        <td class="ps-4">
                            <div class="fw-bold " style="font-size:0.95rem; letter-spacing:-0.01em;">${v.unidad}</div>
                            <div class="badge-folio mt-1"><i class="bi bi-hash"></i>${v.viaje_folio}</div>
                            <div class="small  mt-1" style="font-size:0.7rem;">📍 ${v.almacen_nombre || 'N/A'}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-chofer me-3"><i class="bi bi-person-badge"></i></div>
                                <div>
                                    <div class="fw-bold text-uppercase" style="font-size: 0.72rem;  letter-spacing:0.02em;">${v.chofer}</div>
                                    <small class="" style="font-size: 0.62rem;">Operador Logístico</small>
                                </div>
                            </div>
                        </td>
                        <td>${listaAyudantes}</td>
                        <td><div class="carga-scroll" style="font-size:0.75rem; color:#424245;">${v.detalles_carga}</div></td>
                        <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light " onclick="abrirModalEdicionViaje('${v.viaje_folio}', ${v.vehiculo_id}, ${v.chofer_id})" style="border-radius: 10px; color: #007aff; background: #f2f2f7;"><i class="bi bi-pencil-square"></i></button>
                            <div class="d-flex justify-content-end mt-1" style="gap: 8px;">
                                <button class="btn btn-sm d-flex align-items-center justify-content-center" onclick="confirmarCancelacionViaje(${v.vehiculo_id}, '${v.viaje_folio}')" style="background: #fff; color: #ff3b30; border: 1px solid #ff3b30; border-radius: 10px; padding: 6px 12px; font-weight: 600; font-size: 0.68rem; transition: all 0.3s ease;"><i class="bi bi-x-circle me-1"></i> CANCELAR</button>
                                <button class="btn btn-finish btn-sm d-flex align-items-center justify-content-center" onclick="finalizarViaje(${v.vehiculo_id}, '${v.viaje_folio}')" style="background: #14c41d; color: #fff;  border-radius: 10px; padding: 6px 14px; font-weight: 600; font-size: 0.68rem;"><i class="bi bi-check2-all me-1"></i> FINALIZAR</button>
                            </div>
                        </td>
                    </tr>
                `);
            });
        } catch (e) { body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error de conexión</td></tr>'); }
    };

    window.confirmarCancelacionViaje = function(vehiculoId, folioViaje) {
        Swal.fire({
            title: '¿Anular este viaje?',
            text: `Se cancelarán todas las entregas asociadas al folio ${folioViaje} y los materiales volverán a estar disponibles.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3b30',
            cancelButtonColor: '#8e8e93',
            confirmButtonText: 'Sí, cancelar ruta',
            cancelButtonText: 'Mantener activo',
            customClass: { popup: 'rounded-4 shadow', confirmButton: 'rounded-3 px-4', cancelButton: 'rounded-3 px-4' }
        }).then((result) => {
            if (result.isConfirmed) cancelarTodoElViaje(vehiculoId, folioViaje);
        });
    };

    async function cancelarTodoElViaje(vehiculoId, folioViaje) {
        try {
            $('#loader').removeClass('d-none');
            const resp = await fetch(`/myvet/app/controllers/repartosController.php?action=cancelar_viaje_completo&folio=${folioViaje}&vehiculo_id=${vehiculoId}`);
            const res = await resp.json();
            if (res.success) {
                Swal.fire({ title: 'Ruta Anulada', text: res.message, icon: 'success', confirmButtonColor: '#1c1c1e', customClass: { popup: 'rounded-4' } }).then(() => location.reload());
            } else {
                Swal.fire('Error', res.message || 'No se pudo anular', 'error');
            }
        } catch (e) {
            Swal.fire('Error de sistema', 'Ocurrió un error al conectar con el satélite.', 'error');
        } finally {
            $('#loader').addClass('d-none');
        }
    }

    window.CONTROLLER = '/myvet/app/controllers/repartosController.php';
    let allData = [];
    let filteredData = [];
    let currentPage = 1;
    const rowsPerPage = 10;

    window.cargarPendientes = async function() {
        const body = $('#bodyPendientes');
        const idAlmacen = $('#filtroAlmacen').val();
        
        // CORRECCIÓN: Los valores de fecha ya se toman correctamente de los inputs
        const fechaInicio = document.getElementById('inicio').value;
        const fechaFin = document.getElementById('fin').value;

        try {
            body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div></td></tr>');
            // Las fechas se envían correctamente en la URL aquí:
            const resp = await fetch(`${window.CONTROLLER}?action=listar_pendientes_ruta&almacen_id=${idAlmacen}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
            const res = await resp.json();
            
            allData = res.success ? res.data : [];
            filteredData = [...allData];
            $('#count_pendientes').text(allData.length);
            currentPage = 1;
            renderTable();
        } catch (e) { console.error(e); }


    offsetActual = 0;
    const idAlmacen2 = $('#filtroAlmacen').val();
    $('#tbodyMonitor').html('<tr><td colspan="8" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.ajax({
        url: '/myvet/app/controllers/repartosController.php',
        type: 'GET',
        data: { action: 'get_monitor_entregas', almacen_id: idAlmacen2, inicio: offsetActual, limite: limiteCarga ,fecha_inicio:fechaInicio,fecha_fin:fechaFin},
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) { 
                renderizarFilas(response.data, false); 
            }
            else { 
                $('#tbodyMonitor').html('<tr><td colspan="8" class="text-center text-body-secondary py-5"><i class="bi bi-patch-check d-block fs-2 mb-2"></i>No hay movimientos pendientes.</td></tr>'); 
                $('#btnCargarMas').hide();
            }
        }
    });

    };
    </script>
    <script>
    function renderTable() {
        const body = $('#bodyPendientes');
        body.empty();
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const items = filteredData.slice(start, end);

        if (items.length === 0) {
            body.html('<tr><td colspan="6" class="text-center py-5 text-body-secondary">Bandeja de entrada vacía o sin resultados para el filtro seleccionado</td></tr>');
            return;
        }

        items.forEach(item => {
            let cantidad = parseFloat(item.cantidad) || 0;
            let factor = parseFloat(item.factor_conversion) || 1;
            let uReporte = item.unidad_reporte || 'Unid.';
            let uMedida = item.unidad_medida || 'Pz';
            let displayEntrega = "";

            if (factor > 1) {
                let enteros = Math.floor(cantidad / factor);
                let sobrantes = cantidad % factor;
                let partes = [];
                if (enteros > 0) partes.push(`<strong>${enteros}</strong> ${uReporte}`);
                if (sobrantes > 0) partes.push(`<strong>${sobrantes}</strong> ${uMedida}`);
                displayEntrega = partes.length > 0 ? partes.join(' + ') : `0 ${uMedida}`;
            } else {
                displayEntrega = `<strong>${cantidad}</strong> ${uMedida}`;
            }

            let badge = '';
            let btnAccion = '';
            const estado = (item.estado_reparto || '').toLowerCase().trim();

            if (estado === 'completado') {
                badge = '<span class="badge-premium st-completado"><i class="bi bi-check-circle-fill"></i> Entregado</span>';
                btnAccion = `<button class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="verEntrega(${item.movimiento_id})"><i class="bi bi-eye"></i></button>`;
            } else if (estado === 'en_transito') {
                badge = '<span class="badge-premium st-ruta"><i class="bi bi-truck animate-pulse-soft"></i> En Tránsito</span>';
                btnAccion = `<button class="btn btn-light btn-sm rounded-pill border shadow-sm px-3" onclick="verEntrega(${item.movimiento_id})"><i class="bi-truck"></i></button>`;
            } else {
                badge = '<span class="badge-premium st-disponible"><i class="bi bi-house"></i> En Patio</span>';
                btnAccion = `<button class="btn btn-gradient btn-sm px-3" onclick="prepararModalReparto(${item.movimiento_id}, ${item.almacen_origen_id})">ASIGNAR RUTA</button>`;
            }

            body.append(`
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <div class="fw-bold " style="font-size: 0.9rem;">#${item.folio_venta || 'S/F'}</div>
                        <div class="text-body-secondary" style="font-size: 0.75rem;">${item.fecha_format || ''}</div>
                    </td>
                    <td>
                        <div class="fw-bold " style="font-size: 0.9rem;">${item.cliente || 'S/F'}</div>
                    </td>
                    <td>
                        <div class="fw-bold " style="font-size: 0.85rem;">${item.producto}</div>
                        <div class="text-body-secondary small">${displayEntrega}</div>
                    </td>
                    <td><span class="small text-body-secondary fw-bold">📍 ${item.almacen_origen}</span></td>
                    <td class="text-center">${badge}</td>
                    <td class="text-end pe-4">${btnAccion}</td>
                </tr>
            `);
        });
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / rowsPerPage);
        const container = $('#paginationBootstrap');
        container.empty();
        $('#pageIndicatorText').text(`VISUALIZANDO PÁGINA ${currentPage} DE ${totalPages || 1}`);

        container.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">Anterior</a></li>`);
        for (let i = 1; i <= totalPages; i++) {
            if(i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a></li>`);
            }
        }
        container.append(`<li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">Siguiente</a></li>`);
    }

    window.changePage = function(p) { currentPage = p; renderTable(); };

    window.finalizarViaje = async function(vehiculoId, folioRuta) {
        if (!confirm(`¿Confirmar llegada de la unidad ${folioRuta}?`)) return;
        try {
            const formData = new FormData();
            formData.append('vehiculo_id', vehiculoId);
            formData.append('viaje_folio', folioRuta);
            const resp = await fetch(`/myvet/app/controllers/repartosController.php?action=finalizar_viaje`, { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success');
                cargarMonitorViajes();
                cargarPendientes();
            }
        } catch (e) { console.error(e); }
    };

    $(document).ready(function() {
        cargarPendientes();
        cargarMonitorViajes();

        $("#buscarSalida").on("keyup", function() {
            const val = $(this).val().toLowerCase();
            filteredData = allData.filter(i => `${i.folio_venta}${i.cliente} ${i.producto} ${i.almacen_origen}`.toLowerCase().includes(val));
            currentPage = 1;
            renderTable();
        });

        // CORRECCIÓN: Escuchar el evento 'change' en AMBOS inputs de fecha
        // Ya no necesitas una función "recargar", porque cargarPendientes() hace la misma llamada HTTP con las fechas.
        $("#inicio, #fin").on("change", function() {
            cargarPendientes();
            
        });
    });
    </script>
</body>
</html>