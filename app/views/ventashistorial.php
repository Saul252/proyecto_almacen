<?php
// 1. INCLUSIONES Y SEGURIDAD
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina('Entregas');
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../config/conexion.php';

session_start();
$paginaActual = 'Entregas'; 

// --- PROCESAR GUARDADO DE ENTREGA ---
if (isset($_POST['accion']) && $_POST['accion'] == 'guardar_entrega') {
    header('Content-Type: application/json');
    $venta_id = intval($_POST['venta_id']);
    $productos = $_POST['productos'] ?? [];
    $usuario_id = $_SESSION['usuario_id'] ?? 1;
    $rol_id_sesion = $_SESSION['rol_id'] ?? 2;
    $almacen_sesion = $_SESSION['almacen_id'] ?? 0;

    $conexion->begin_transaction();
    try {
        $vta_info = $conexion->query("SELECT almacen_id, folio FROM ventas WHERE id = $venta_id")->fetch_assoc();
        if ($rol_id_sesion != 1 && $vta_info['almacen_id'] != $almacen_sesion) {
            throw new Exception("No tiene permiso para realizar entregas en este almacén.");
        }

        $stmt = $conexion->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $venta_id, $usuario_id);
        $stmt->execute();
        $entrega_id = $conexion->insert_id;

        foreach ($productos as $dv_id => $cant) {
            $cant = floatval($cant);
            if ($cant <= 0) continue;
            $res_v = $conexion->query("SELECT (cantidad - cantidad_entregada) as pendiente, producto_id FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
            if ($cant > $res_v['pendiente']) throw new Exception("Cantidad excede el pendiente.");
            $conexion->query("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES ($entrega_id, $dv_id, $cant)");
            $conexion->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $cant WHERE id = $dv_id");
            $almacen_a_descontar = $vta_info['almacen_id'];
            $conexion->query("UPDATE inventario SET stock = stock - $cant WHERE producto_id = {$res_v['producto_id']} AND almacen_id = $almacen_a_descontar");
            $mov_obs = "Salida por entrega parcial. Folio: " . $vta_info['folio'];
            $conexion->query("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                             VALUES ({$res_v['producto_id']}, 'salida', $cant, $almacen_a_descontar, $usuario_id, $venta_id, '$mov_obs')");
        }
        $check = $conexion->query("SELECT SUM(cantidad - cantidad_entregada) as deuda FROM detalle_venta WHERE venta_id = $venta_id")->fetch_assoc();
        $st = ($check['deuda'] <= 0) ? 'entregado' : 'parcial';
        $conexion->query("UPDATE ventas SET estado_entrega = '$st' WHERE id = $venta_id");
        $conexion->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- PROCESAR GUARDADO DE ABONO ---
if (isset($_POST['accion']) && $_POST['accion'] == 'guardar_abono') {
    header('Content-Type: application/json');
    $venta_id = intval($_POST['venta_id']);
    $monto = floatval($_POST['monto']);
    $usuario_id = $_SESSION['usuario_id'] ?? 1;
    try {
        $stmt = $conexion->prepare("INSERT INTO historial_pagos (venta_id, monto, fecha, usuario_id) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("idi", $venta_id, $monto, $usuario_id);
        $stmt->execute();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- API LISTADO ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $rol_id = $_SESSION['rol_id'] ?? 2;
    $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;
    
    // 1. Filtros base (WHERE)
    $where = " WHERE v.estado_general = 'activa' ";
    
    // Filtro por Almacén (Seguridad de Rol)
    if ($rol_id != 1) { 
        $where .= " AND v.almacen_id = $id_almacen_usuario "; 
    } elseif (!empty($_GET['f_almacen'])) { 
        $where .= " AND v.almacen_id = " . intval($_GET['f_almacen']); 
    }

    // Filtro por Buscador (Folio o Cliente)
    if (!empty($_GET['f_search'])) {
        $s = $conexion->real_escape_string($_GET['f_search']);
        $where .= " AND (c.nombre_comercial LIKE '%$s%' OR v.folio LIKE '%$s%') ";
    }

    // Filtro por Estatus de Entrega
    if (!empty($_GET['f_status'])) {
        $st = $conexion->real_escape_string($_GET['f_status']);
        $where .= " AND v.estado_entrega = '$st' ";
    }

    // Filtros de Fecha
    if (!empty($_GET['f_rango'])) {
        $r = $_GET['f_rango'];
        if($r == 'hoy') $where .= " AND DATE(v.fecha) = CURDATE() ";
        if($r == 'ayer') $where .= " AND DATE(v.fecha) = SUBDATE(CURDATE(),1) ";
        if($r == 'semana') $where .= " AND YEARWEEK(v.fecha, 1) = YEARWEEK(CURDATE(), 1) ";
        if($r == 'mes') $where .= " AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE()) ";
        if($r == 'personalizado' && !empty($_GET['f_inicio']) && !empty($_GET['f_fin'])) {
            $ini = $conexion->real_escape_string($_GET['f_inicio']);
            $fin = $conexion->real_escape_string($_GET['f_fin']);
            $where .= " AND DATE(v.fecha) BETWEEN '$ini' AND '$fin' ";
        }
    }

    // 2. Filtros sobre cálculos (HAVING)
    // Se usa HAVING porque "pagado" no es una columna real, sino un cálculo de subquery
    $having = "";
    if (!empty($_GET['f_pago'])) {
        if ($_GET['f_pago'] == 'deuda') {
            $having = " HAVING (v.total - pagado) > 0.01 "; // Mayor a 1 centavo para evitar errores de redondeo
        } elseif ($_GET['f_pago'] == 'pagado') {
            $having = " HAVING (v.total - pagado) <= 0.01 ";
        }
    }

    $sql = "SELECT v.*, c.nombre_comercial as cliente, a.nombre as almacen_nombre,
            (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as pagado
            FROM ventas v 
            JOIN clientes c ON v.id_cliente = c.id 
            JOIN almacenes a ON v.almacen_id = a.id 
            $where 
            $having 
            ORDER BY v.fecha DESC";

    $res = $conexion->query($sql);
    $data = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) { 
            $data[] = $row; 
        }
    }
    
    echo json_encode($data);
    exit;
}

// --- API DETALLE (ÚNICA Y CORREGIDA) ---
if (isset($_GET['detalle_id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['detalle_id']);
    
    // Traemos info de venta incluyendo el total_pagado
    $sqlI = "SELECT v.*, c.nombre_comercial, a.nombre as almacen, 
            (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado 
            FROM ventas v JOIN clientes c ON v.id_cliente = c.id 
            JOIN almacenes a ON v.almacen_id = a.id WHERE v.id = $id";
    $info = $conexion->query($sqlI)->fetch_assoc();
    
    $prods = [];
    $resP = $conexion->query("SELECT dv.*, p.nombre as producto FROM detalle_venta dv JOIN productos p ON dv.producto_id = p.id WHERE dv.venta_id = $id");
    while($p = $resP->fetch_assoc()){ $prods[] = $p; }
    
    $historial = [];
    $sqlH = "SELECT ev.fecha, p.nombre as producto, de.cantidad, u.nombre as usuario_nombre FROM entregas_venta ev JOIN detalle_entrega de ON ev.id = de.entrega_id JOIN detalle_venta dv ON de.detalle_venta_id = dv.id JOIN productos p ON dv.producto_id = p.id JOIN usuarios u ON ev.usuario_id = u.id WHERE ev.venta_id = $id ORDER BY ev.fecha DESC";
    $resH = $conexion->query($sqlH);
    while($h = $resH->fetch_assoc()){ $historial[] = $h; }
    
    echo json_encode(['info' => $info, 'productos' => $prods, 'historial' => $historial]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-dark: #2c3e50;
            --accent-color: #34495e;
            --bg-body: #f8f9fa;
        }
       
        .main-content {  padding: 2rem; min-height: 100vh; transition: all 0.3s; }
        .scroll-table { background: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
        .table thead th { background-color: var(--primary-dark); color: white; font-weight: 500; text-transform: uppercase; font-size: 0.75rem; padding: 12px;  }
        .btn-action { background-color: var(--accent-color); color: white;  }
        .btn-action:hover { background-color: var(--primary-dark); color: white; }
        .filter-card {  box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 10px; }
        .modal-header { background-color: var(--primary-dark); color: white;  }
        .input-entrega { border: 2px solid #28a745 !important; max-width: 90px; text-align: center; font-weight: bold; }
        @media (max-width: 992px) { .main-content { margin-left: 0; padding: 1rem; } }
    </style>
</head>
<body>
     <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold card-title-text m-0">Control de Entregas</h3>
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
            </div>

            <div class="card filter-card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscador</label>
                            <input type="text" id="f_search" class="form-control form-control-sm" placeholder="Folio o Cliente..." onkeyup="getVentas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Entrega</label>
                            <select id="f_status" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>
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
                                <option value="todos">Historial Completo</option>
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
                                <option value="personalizado">Rango...</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-none" id="div_p">
                            <label class="form-label small fw-bold">Fechas</label>
                            <div class="input-group input-group-sm">
                                <input type="date" id="f_ini" class="form-control" onchange="getVentas()">
                                <input type="date" id="f_fin" class="form-control" onchange="getVentas()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Ubicación</label>
                            <select id="f_almacen" class="form-select form-select-sm" onchange="getVentas()" <?= ($_SESSION['rol_id'] != 1 ? 'disabled':'') ?>>
                                <option value="">Todas</option>
                                <?php 
                                $alms = $conexion->query("SELECT id, nombre FROM almacenes");
                                while($a = $alms->fetch_assoc()){
                                    $sel = ($_SESSION['rol_id'] != 1 && $_SESSION['almacen_id'] == $a['id']) ? 'selected':'';
                                    echo "<option value='{$a['id']}' $sel>{$a['nombre']}</option>";
                                }
                                ?>
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
                                <th class="ps-3">Fecha</th>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Saldo Cobro</th>
                                <th class="text-center">Estado Entrega</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

 <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content ">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Gestión de Venta: <span id="spanFolio"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3  border-end p-4">
                        <p id="detCliente" class="fw-bold small mb-1"></p>
                        <p id="detAlmacen" class="fw-bold small mb-3"></p>
                        
                        <div class="mb-4 p-2  border rounded shadow-sm text-center">
                            <span class="d-block small text-body-secondary text-uppercase fw-bold">Saldo Pendiente</span>
                            <span id="detSaldoLabel" class="h5 fw-bold text-danger">$0.00</span>
                        </div>

                        <div id="contenedorBoton">
                            <button id="btnHabilitar" class="btn btn-action w-100 mb-2 py-2 fw-bold" onclick="alternarModo(true)">Nueva Entrega</button>
                            
                            <button id="btnAbonar" class="btn btn-primary w-100 mb-2 py-2 fw-bold" onclick="abrirFlujoAbono()">
                                <i class="bi bi-cash"></i> Registrar Abono
                            </button>
                        </div>
                        
                        <div id="controlesGuardar" class="d-none">
                            <button class="btn btn-success w-100 mb-2 py-2 fw-bold" onclick="procesarEntrega()">Guardar Cambios</button>
                            <button class="btn btn-link text-secondary w-100 btn-sm" onclick="alternarModo(false)">Cancelar</button>
                        </div>
                    </div>
                    <div class="col-md-9 p-4">
                        <div class="table-responsive border rounded mb-4" style="max-height: 200px;">
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
                        <div class="table-responsive border rounded" style="max-height: 180px;">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-uppercase"><th>Fecha</th><th>Responsable</th><th>Producto</th><th class="text-center">Cant</th></tr>
                                </thead>
                                <tbody id="tbodyHistorial" class="small"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    const modalObj = new bootstrap.Modal('#modalDetalle');
    let ventaActual = null;

    async function getVentas() {
        $('#loader').removeClass('d-none');
        // Capturamos el nuevo filtro f_pago
    const s = $('#f_search').val(), 
          r = $('#f_rango').val(), 
          i = $('#f_ini').val(), 
          f = $('#f_fin').val(), 
          a = $('#f_almacen').val(), 
          st = $('#f_status').val(),
          pago = $('#f_pago').val(); // <--- Nueva variable

    try {
        // Añadimos &f_pago=${pago} a la URL
        const res = await fetch(`?ajax=1&f_search=${s}&f_rango=${r}&f_inicio=${i}&f_fin=${f}&f_almacen=${a}&f_status=${st}&f_pago=${pago}`);
        const data = await res.json();  
        $('#tablaVentas tbody').html(data.map(v => {
                // Cálculo de deuda
                let total = parseFloat(v.total);
                let pagado = parseFloat(v.pagado);
                let saldo = total - pagado;
                let badgeCobro = '';

                if(saldo <= 0) {
                    badgeCobro = '<span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Pagado</span>';
                } else {
                    badgeCobro = `<span class="text-danger small fw-bold">Debe: $${saldo.toFixed(2)}</span>`;
                }

                return `<tr>
                    <td class="ps-3 small">${v.fecha}</td>
                    <td class="fw-bold">${v.folio}</td>
                    <td><div class="small fw-bold">${v.cliente}</div></td>
                    <td>${badgeCobro}</td>
                    <td class="text-center"><span class="badge ${v.estado_entrega=='entregado'?'bg-success':(v.estado_entrega=='parcial'?'bg-warning card-title-text':'bg-danger')}">${v.estado_entrega.toUpperCase()}</span></td>
                    <td class="text-end pe-3">
    <div class="btn-group" role="group" aria-label="Acciones de venta">
        <button class="btn btn-sm btn-dark shadow-sm" onclick="verDetalle(${v.id})" title="Ver Detalles">
            <i class="bi bi-gear-fill"></i> Gestionar
        </button>

        <a class="btn btn-sm btn-primary shadow-sm" href="/myvet/app/backend/ventas/ticket_venta.php?id=${v.id}" target="_blank" title="Imprimir Ticket con Precios">
            <i class="bi bi-currency-dollar"></i> Ticket
        </a>

        <a class="btn btn-sm btn-info text-white shadow-sm" href="/myvet/app/backend/ventas/ticket_sin_precio.php?id=${v.id}" target="_blank" title="Imprimir Remisión sin Precios">
            <i class="bi bi-file-earmark-text"></i> Remisión
        </a>
    </div>
</td>
                </tr>`;
            }).join(''));
        } catch (e) { console.error(e); }
        $('#loader').addClass('d-none');
    }
async function verDetalle(id) {
    try {
        const res = await fetch('?detalle_id=' + id);
        const data = await res.json();
        ventaActual = data; 

        $('#spanFolio').text(data.info.folio);
        $('#detCliente').text(data.info.nombre_comercial);
        $('#detAlmacen').text(data.info.almacen);
        
        // --- LÓGICA DE DEUDA ---
        const total = parseFloat(data.info.total) || 0;
        const pagado = parseFloat(data.info.total_pagado) || 0;
        const deuda = total - pagado;

        if (deuda <= 0) {
            $('#detSaldoLabel').text('LIQUIDADO').removeClass('text-danger').addClass('text-success');
            $('#btnAbonar').addClass('d-none'); // Usamos d-none de bootstrap para asegurar que desaparezca
        } else {
            $('#detSaldoLabel').text('$' + deuda.toFixed(2)).removeClass('text-success').addClass('text-danger');
            $('#btnAbonar').removeClass('d-none');
        }

        // Llenar tabla productos
        $('#tbodyDetalle').html(data.productos.map(p => {
            let pen = parseFloat(p.cantidad) - parseFloat(p.cantidad_entregada);
            return `<tr>
                <td class="card-title-text">${p.producto}</td>
                <td class="text-center">${p.cantidad}</td>
                <td class="text-center">${p.cantidad_entregada}1234567</td>
                <td class="text-center text-danger fw-bold">${pen}</td>
                <td class="text-center col-input d-none">
                    ${pen > 0 ? `<input type="number" class="form-control form-control-sm input-entrega mx-auto" max="${pen}" min="0" value="0" data-id="${p.id}" style="width:70px">` : '<span class="badge bg-success">Completo</span>'}
                </td>
            </tr>`;
        }).join(''));

        // Llenar historial
        $('#tbodyHistorial').html(data.historial.length > 0 ? data.historial.map(h => `
            <tr><td>${h.fecha}</td><td>${h.usuario_nombre}</td><td>${h.producto}</td><td class="text-center fw-bold">${h.cantidad}</td></tr>
        `).join('') : '<tr><td colspan="4" class="text-center text-body-secondary">No hay entregas registradas</td></tr>');

        alternarModo(false);
        modalObj.show();
    } catch (error) {
        console.error(error);
    }
}
   function togglePerso() { $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado'); getVentas(); }
    function alternarModo(e) { $('.col-input').toggleClass('d-none', !e); $('#btnHabilitar').toggle(!e && ventaActual.info.estado_entrega !== 'entregado'); $('#controlesGuardar').toggleClass('d-none', !e); }

    async function procesarEntrega() {
        const fd = new FormData();
        fd.append('accion', 'guardar_entrega');
        fd.append('venta_id', ventaActual.info.id);
        let ok = false;
        $('.input-entrega').each(function() { if($(this).val() > 0) { fd.append(`productos[${$(this).data('id')}]`, $(this).val()); ok = true; } });
        if(!ok) return Swal.fire('Error', 'Indique cantidades', 'warning');
        const res = await fetch(window.location.href, { method: 'POST', body: fd });
        if((await res.json()).status == 'success') { modalObj.hide(); getVentas(); Swal.fire('Listo', 'Entrega guardada', 'success'); }
    }

    $(document).ready(getVentas);
    </script>
    <script>// Función para abrir el cobro usando SweetAlert
async function abrirFlujoAbono() {
    // Calculamos el saldo basado en la data actual de ventaActual
    const totalVenta = parseFloat(ventaActual.info.total || 0);
    const pagado = parseFloat(ventaActual.info.total_pagado || 0);
    const saldoPendiente = totalVenta - pagado;

    if (saldoPendiente <= 0) {
        return Swal.fire('Venta Liquidada', 'Esta venta ya no tiene saldo pendiente.', 'success');
    }

    const { value: monto } = await Swal.fire({
        title: 'Registrar Abono',
        input: 'number',
        inputLabel: 'Monto a recibir (Saldo: $' + saldoPendiente.toFixed(2) + ')',
        inputValue: saldoPendiente.toFixed(2),
        showCancelButton: true,
        inputAttributes: { min: 0.01, step: 0.01 },
        confirmButtonText: 'Guardar Abono',
        cancelButtonText: 'Cerrar'
    });

    if (monto && monto > 0) {
        const fd = new FormData();
        fd.append('accion', 'guardar_abono');
        fd.append('venta_id', ventaActual.info.id);
        fd.append('monto', monto);

        try {
            const res = await fetch(window.location.href, { method: 'POST', body: fd });
            const data = await res.json();
            
            if (data.status === 'success') {
                Swal.fire('Éxito', 'Abono guardado', 'success');
                // Actualizamos todo en tiempo real
                await verDetalle(ventaActual.info.id); // Recarga el modal
                getVentas(); // Recarga la tabla principal (fondo)
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (e) {
            console.error("Error al abonar:", e);
        }
    }
}

// MODIFICACIÓN IMPORTANTE: 
// Asegúrate de que tu función verDetalle actualice el label del saldo:
// Busca dentro de tu función verDetalle(id) existente y agrega esta línea:
// $('#detSaldoLabel').text('$' + (parseFloat(data.info.total) - parseFloat(data.info.total_pagado || 0)).toFixed(2));</script>
</body>
</html>