<?php
/**
 * clientesEstatus_view.php
 * Vista maestra optimizada: Manejo de clientes sin historial y diseño responsivo.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $paginaActual?> | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        :root { --sidebar-width: 0px; --navbar-height: 65px; }
       
        .main-content { 
             
            padding: 40px; 
           
            transition: all 0.3s ease; 
        }
        .card-table {  border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .bg-gradient-dark { background: linear-gradient(45deg, #212529, #343a40); }
        .text-warning-dark { color: #856404; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } }
    </style>
</head>
<body>

    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>

    <main class="main-content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-dark text-white  shadow-lg">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1"><i class="bi bi-person-badge me-2"></i>Estatus Maestro de Clientes</h2>
                                <p class="mb-0 opacity-75">Control de cuentas por cobrar y compromisos de entrega.</p>
                            </div>
                            <button onclick="loadEstatus()" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar Reporte
                            </button>
                        </div>
                    </div>
       <?php if($_SESSION['almacen_id']==0): ?>            
    <div class="card card-expediente p-3 mb-4">
        <div class="row align-items-center">
           <div class="mb-3">
    <label class="form-label fw-bold">Filtrar por Almacén:</label>
    <select id="filtroAlmacen" class="form-select border-primary">
        <option value="0">Cargando almacenes...</option>
    </select>
</div>
        </div>
    </div>
    <?php endif ?>

                </div>
            </div>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaEstatus" class="form-control border-start-0" placeholder="Buscar cliente por nombre o RFC...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaEstatus" class="table table-hover align-middle w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">Cliente / RFC</th>
                            <th class="text-center">Situación de Pago</th>
                            <th class="text-center">Situación de Entrega</th>
                           
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaEstatus">
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-body-secondary">Consultando movimientos y saldos...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let tablaEstatus;

        $(document).ready(function() {
            loadEstatus();
        });

        function loadEstatus() {
            // Destruir tabla si ya existe para evitar errores de inicialización
            if ($.fn.DataTable.isDataTable('#tablaEstatus')) {
                $('#tablaEstatus').DataTable().destroy();
            }

            $.ajax({
                url: '/myvet/app/controllers/clientesEstatusController.php',
                type: 'GET',
                // Usamos 'action' como definimos en el switch del controlador
                data: { action: 'listar' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        let html = '';
                        console.log(res.data,res.admin);
                        const clientes = res.data.filter(c => {

    const excluir =
        c.nombre === 'PÚBLICO EN GENERAL' &&
        c.rfc === "XAXX010101000" &&
        res.admin == false &&
        c.almacen_id != res.sesion;

    if (excluir) {
        console.log("j");
    }

    return !excluir;
});
console.log(clientes); 
                        clientes.forEach(c => {
                           
                           
                        
                            const sDeuda = parseFloat(c.saldo_deuda || 0);
                            const pEntregas = parseInt(c.entregas_pendientes || 0);
                            const tieneVentas = parseInt(c.total_ventas || 0);
                            let saldoFavor=parseFloat(c.saldo_a_favor);
                            let saldoEnContra= parseFloat(c.saldo_en_contra);
                           
                            

                            let badgePago, badgeEntrega, btnAccion;

                            // Lógica para detectar si tiene compras o no
                            if (tieneVentas === 0) {
                                badgePago = '<span class="badge bg-light text-body-secondary border rounded-pill px-3">SIN COMPRAS</span>';
                                badgeEntrega = '<span class="badge bg-light text-body-secondary border rounded-pill px-3">-</span>';
                                btnAccion = `<button class="btn btn-sm btn-light disabled rounded-pill px-3">
                                                <i class="bi bi-eye-slash"></i>
                                             </button>`;
                            } else {
                               if (sDeuda > 0) {
    badgePago = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">CON DEUDA</span>';
} else if (sDeuda < 0) {
    badgePago = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">SALDO A FAVOR</span>';
} else {
    badgePago = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">AL CORRIENTE</span>';
}
                                badgeEntrega = pEntregas > 0 
                                    ? `<span class="badge bg-warning-subtle text-warning-dark border border-warning-subtle rounded-pill px-3">PENDIENTE (${pEntregas})</span>` 
                                    : '<span class="badge  text-body-secondary border rounded-pill px-3 text-uppercase small">Completo</span>';
                                
                                // CORRECCIÓN AQUÍ: Apuntamos al controlador del expediente con el ID del cliente
    btnAccion = `<a href="/myvet/app/controllers/clienteExpedienteController.php?id=${c.id}" 
                    class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                    <i class="bi bi-graph-up-arrow me-1"></i> Analizar
                 </a>`;
                            }

                            html += `
                            <tr>
                                <td>
                                    <div class="fw-bold card-title-text">${c.nombre}</div>
                                    <small class="text-body-secondary font-monospace">${c.rfc}</small>
                                </td>
                                <td class="text-center">${badgePago}</td>
                                <td class="text-center">${badgeEntrega}</td>
                                
                               
                                <td class="text-center">${btnAccion}</td>
                            </tr>`;
                        });

                        $('#listaEstatus').html(html);
                        
                        // Inicializar DataTable tras inyectar el HTML
                        tablaEstatus = $('#tablaEstatus').DataTable({
                            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                            "dom": 'rtp',
                            "pageLength": 15,
                            "order": [[3, "desc"]] // Ordenar por mayor saldo pendiente
                        });

                        // Buscador reactivo
                        $('#busquedaEstatus').on('keyup', function() {
                            tablaEstatus.search(this.value).draw();
                        });

                    } else {
                        Swal.fire('Atención', res.message, 'warning');
                    }
                },
                error: function(xhr) {
                    $('#listaEstatus').html(`<tr><td colspan="5" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle me-1"></i> Error al cargar datos del servidor.
                    </td></tr>`);
                }
            });
        }
    </script>

    <script>
       $(document).ready(function() {
    // Solo si eres admin cargamos los almacenes
    const miAlmacen = "<?= $_SESSION['almacen_id'] ?>";
    
    if (miAlmacen == "0") {
        cargarComboAlmacenes();
    }
});

function cargarComboAlmacenes() {
    $.ajax({
        // Usamos la ruta completa para evitar errores de carpetas
        url: '/myvet/app/controllers/accesoController.php?action=getAlmacenesJSON',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log("Almacenes recibidos:", data); // Esto debe salir en tu consola (F12)
            
            let html = '<option value="0">--- Todos los Almacenes ---</option>';
            
            // Validamos que data sea un array
            if (Array.isArray(data)) {
                data.forEach(function(alm) {
                    html += `<option value="${alm.id}">${alm.nombre}</option>`;
                });
            }
            
            // Inyectamos el HTML en el select
            $('#filtroAlmacen').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar almacenes:", error);
            $('#filtroAlmacen').html('<option value="0">Error al cargar datos</option>');
        }
    });
}
   </script>
</body>
</html>