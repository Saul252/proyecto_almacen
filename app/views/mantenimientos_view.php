<?php
/**
 * vehiculos_view.php 
 * Versión ajustada para segmentación por Almacén - cfsistem
 */
$estadosUnidad = [
    'disponible'     => ['label' => 'Disponible', 'class' => 'st-disponible', 'dot' => '#28a745'],
    'en_ruta'        => ['label' => 'En Ruta', 'class' => 'st-ruta', 'dot' => '#007aff'],
    'mantenimiento'  => ['label' => 'Taller', 'class' => 'st-taller', 'dot' => '#ff9500'],
    'fuera_servicio' => ['label' => 'Fuera de Servicio', 'class' => 'st-fuera', 'dot' => '#ff3b30']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte | cfsistem</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
  
    <style>
        :root { 
            --sidebar-width: 0px; 
            --navbar-height: 65px;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --accent-color: #007aff;
        }

        body { 
         
            min-height: 100vh;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .main-content { 
             
            padding: 40px; 
           
            transition: all 0.3s ease;
        }

        .card-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            padding: 30px;
        }

        .ios-micro-card {
            background: white;
            border-radius: 18px;
            padding: 12px 20px;
            
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s;
        }
        .ios-micro-card:hover { transform: translateY(-3px); }
        .ios-icon-circle {
            width: 40px; height: 40px;
            background: #eef6ff;
            color: var(--accent-color);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }

        .badge-premium {
            padding: 8px 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .st-disponible { background: #e6ffed; color: #1e7e34; }
        .st-ruta { background: #e8f4ff; color: #007aff; }
        .st-taller { background: #fff8e6; color: #d97706; }
        .st-fuera { background: #fff0f0; color: #d11a2a; }

        .table thead th {
            background: transparent;
            color: #8e8e93;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            
            padding-bottom: 20px;
        }
        .table tbody tr {
            border-bottom: 1px solid #f2f2f7;
            transition: background 0.2s;
        }
        .table tbody tr:hover { background: rgba(0,122,255,0.02); }

        .btn-gradient {
            background: linear-gradient(135deg, #007aff 0%, #0056b3 100%);
            color: white;  border-radius: 12px;
            padding: 10px 25px; font-weight: 600;
            box-shadow: 0 8px 20px rgba(0,122,255,0.25);
        }
        .btn-gradient:hover { color: white; opacity: 0.9; transform: translateY(-1px); }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 100px; } }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
 <!-- ===================== ENCABEZADO ===================== -->
<div class="card  shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">

            <div class="col-lg-6">
                <h2 class="fw-bold  mb-1">
                    <i class="bi bi-tools text-primary me-2"></i>
                    Mantenimiento
                </h2>
                <p class="text-body-secondary mb-0">
                    Administración y seguimiento de mantenimientos de vehículos
                </p>
            </div>

            <div class="col-lg-6">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2 mt-3 mt-lg-0">

                    <button class="btn btn-primary rounded-pill px-4 shadow-sm"
                        onclick="nuevoMantenimiento()">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nuevo mantenimiento
                    </button>

                    <button class="btn btn-success rounded-pill px-4 shadow-sm"
                        onclick="abrirModalGasto()">
                        <i class="bi bi-box-seam me-2"></i>
                        Suministros
                    </button>

                    <button
                        class="btn btn-warning  rounded-pill px-4 shadow-sm fw-semibold"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAsignarInsumoMantenimiento">

                        <i class="bi bi-tools me-2"></i>
                        Asignar Insumos
                    </button>

                    <a href="/myvet/app/controllers/historialInsumosController.php"
                        class="btn btn-info rounded-pill px-4 shadow-sm text-white">

                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Uso de insumos
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- ===================== FILTROS ===================== -->

<div class="card  shadow-sm mb-4">
    <div class="card-header  ">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-funnel-fill text-primary me-2"></i>
            Filtros de búsqueda
        </h6>
    </div>

    <div class="card-body">

        <div class="row g-3 align-items-end">

            <div class="col-lg-3">
                <label class="form-label fw-semibold small">
                    Buscar
                </label>

                <div class="input-group">
                    <span class="input-group-text ">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        id="f_search"
                        class="form-control"
                        placeholder="ID, nombre, placas..."
                        onkeyup="getMantenimientos()">
                </div>
            </div>


            <div class="col-lg-2">
                <label class="form-label fw-semibold small">
                    Vehículo
                </label>

                <select
                    class="form-select"
                    id="select-vehiculos"
                    onchange="getMantenimientos()">

                    <option value="">Todos</option>

                    <?php foreach($vehiculos as $ve): ?>

                    <option value="<?= $ve['id'] ?>">
                        <?= $ve['nombre'] ?>
                    </option>

                    <?php endforeach; ?>

                </select>
            </div>


            <div class="col-lg-2">

                <label class="form-label fw-semibold small">
                    Periodo
                </label>

                <select
                    id="f_rango"
                    class="form-select"
                    onchange="togglePerso()">

                    <option value="hoy">Hoy</option>
                    <option value="ayer">Ayer</option>
                    <option value="semana" selected>Semana</option>
                    <option value="mes">Mes</option>
                    <option value="todos">Historial Completo</option>
                    <option value="personalizado">Personalizado</option>

                </select>

            </div>


            <div class="col-lg-3 d-none" id="div_p">

                <label class="form-label fw-semibold small">
                    Rango de fechas
                </label>

                <div class="input-group">

                    <input
                        type="date"
                        id="f_ini"
                        class="form-control"
                        onchange="getMantenimientos()">

                    <span class="input-group-text">
                        a
                    </span>

                    <input
                        type="date"
                        id="f_fin"
                        class="form-control"
                        onchange="getMantenimientos()">

                </div>

            </div>


            <div class="col-lg-2">

                <label class="form-label fw-semibold small">
                    Almacén
                </label>

               <select
    id="f_almacen"
    class="form-select"
    onchange="getMantenimientos()">

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

        </div>

    </div>

</div>



<!-- ===================== TABLA ===================== -->

<div class="card  shadow-sm">

    <div class="card-header ">

        <div class="d-flex justify-content-between align-items-center">

            <h6 class="fw-bold mb-0">
                <i class="bi bi-table text-primary me-2"></i>
                Lista de mantenimientos
            </h6>

            <span class="badge bg-primary fs-6">
                Historial
            </span>

        </div>

    </div>

    <div class="table-responsive" style="max-height:65vh;">

        <table class="table table-hover table-striped align-middle mb-0"
            id="tablaMantenimientos">

            <thead class="table-dark sticky-top">

                <tr>

                    <th>Fecha</th>
                    <th>ID</th>
                    <th>Almacén</th>
                    <th>Vehículo</th>
                     <th class="text-center">Placas</th>
                    <th>Tipo</th>
                    <th>Razón</th>
                   
                    <th class="text-center">Kilometraje</th>
                    <th class="text-center">Acciones</th>

                </tr>

            </thead>

            <tbody></tbody>

        </table>

    </div>

</div>       
    </main>

    
<div class="modal fade" id="modalDetalleMantenimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;  overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.25);">
            
            <div class="modal-header bg-dark text-white p-4 ">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-info-circle fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-white">Detalle de Mantenimiento</h4>
                        <p class="text-white-50 small mb-0">ID Registro: #<span id="det-id_mantenimiento"></span></p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 ">
                
                <div class="card  shadow-sm rounded-4 mb-3 p-3 text-center ">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <span id="det-badge-tipo" class="badge px-4 py-2 fs-6 rounded-pill"></span>
                    </div>
                    <h5 class="fw-bold k text-uppercase tracking-wide mb-1" id="det-razon"></h5>
                    <p class="text-body-secondary small mb-0">Registrado en sistema el: <span id="det-creado_en"></span></p>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card  shadow-sm rounded-4 p-3 h-100 ">
                            <h6 class="fw-bold text-secondary mb-3 small text-uppercase">
                                <i class="bi bi-car-front me-2 text-primary"></i> Datos del Vehículo
                            </h6>
                            <div class="mb-2">
                                <small class="text-body-secondary d-block">Marca / Modelo</small>
                                <span class="fw-bold k fs-5" id="det-vehiculo"></span> 
                                <span class="badge bg-light text-dark border ms-1" id="det-modelo"></span>
                            </div>
                            <div class="row pt-2 border-top">
                                <div class="col-6">
                                    <small class="text-body-secondary d-block">Placas</small>
                                    <span class="fw-bold tracking-wider text-primary" id="det-placas"></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-body-secondary d-block">Kilometraje</small>
                                    <span class="fw-bold k" id="det-kilometraje"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card  shadow-sm rounded-4 p-3 h-100 ">
                            <h6 class="fw-bold text-secondary mb-3 small text-uppercase">
                                <i class="bi bi-calendar3 me-2 text-success"></i> Fechas e Infraestructura
                            </h6>
                            <div class="mb-2">
                                <small class="text-body-secondary d-block">Ubicación / Almacén de Cargo</small>
                                <span class="fw-bold k" id="det-almacen"></span>
                            </div>
                            <div class="row pt-2 border-top">
                                <div class="col-6 border-end">
                                    <small class="text-body-secondary d-block">Fecha Efectuada</small>
                                    <span class="fw-bold k" id="det-fecha"></span>
                                </div>
                                <div class="col-6 text-end text-md-start ps-md-3">
                                    <small class="text-body-secondary d-block">Próxima Cita</small>
                                    <span class="fw-bold text-danger" id="det-fecha_proximo"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer   p-4 pt-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i> Cerrar Vista
                </button>
            </div>

        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php require_once __DIR__ . '/mantenimientos/modalUsoInsumo.php'; ?>
<?php require_once __DIR__ . '/mantenimientos/modalMantenimiento.php'; ?>
     <?php require_once __DIR__ . '/egresosComponets/modalInsumos.php'; ?>
<script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>
   <script>
    let mantenimientosActuales='';
  $(document).ready(function() {
        // 1. Carga inicial de datos
        getMantenimientos();

        // 2. Escuchadores para filtros (opcional, pero recomendado para centralizar)
        $('#f_rango').on('change', togglePerso);
        // getMantenimientos ya se llama mediante onchange/onkeyup en tu HTML, lo cual está bien.

        console.log("Sistema de historial listo.");
    });
    async function getMantenimientos() {
        $('#loader').removeClass('d-none');


        const params = new URLSearchParams({
            action: 'listar',
            // <--- Nuevo parámetro para el ID de venta
            f_search: $('#f_search').val(),
            f_rango: $('#f_rango').val(),
            f_inicio: $('#f_ini').val(),
            f_fin: $('#f_fin').val(),
            f_almacen: $('#f_almacen').val(),
          
            
          
            
            f_vehiculo:$('#select-vehiculos').val() ?? '',
            
            
            

        });

        try {
            const res = await fetch(`/myvet/app/controllers/mantenimientosController.php?${params.toString()}`);
            const data = await res.json();https://modems-worthy-stay-submit.trycloudflare.com/myvet/
            mantenimientosActuales=data;
            console.log(data);
           
$('#tablaMantenimientos tbody').html(data.map(m => {
    

    



    return `<tr>
        <td class="ps-3 small">${m.creado_en}</td>
        <td class="fw-bold">${m.id_vehiculo}</td>
        <td><span class="badge bg-light text-dark border fw-normal">${m.almacen}</span></td>
        <td><div class="small fw-bold">${m.vehiculo}</div></td>
         <td><div class="small fw-bold">${m.placas}</div></td>
        <td><div class="small fw-bold">${m.tipo_mantenimiento}</div></td>
        <td class="fw-bold k">${m.razon}</td>
         
           <td><div class="small fw-bold">${m.kilometraje}</div></td>
        
        <td class="text-end pe-3">
            <div class="btn-group  rounded-3 shadow-sm border p-1" role="group" aria-label="Acciones de venta">
                <button type="button" class="btn btn-link k btn-sm px-3 " 
                        onclick="verDetalle(${m.id_mantenimiento})" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="ver">
                    <i class="bi bi-eye fs-5"></i>
                </button>
             
            </div>
        </td>
    </tr>`;
}).join(''));

        } catch (e) {
            console.error("Error al cargar mantenimientos:", e);
        } finally {
            $('#loader').addClass('d-none');
        }
    }
   
let tabla;

$(document).ready(function() {
    tabla = $('#tablaVehiculos').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        "dom": 'rt<"d-flex justify-content-between align-items-center mt-4 px-2"ip>',
        "pageLength": 8,
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": <?php echo ($_SESSION['almacen_id'] == 0) ? '5' : '4'; ?> }
        ]
    });

    $('#busquedaVehiculo').on('keyup', function() { tabla.search(this.value).draw(); });

    $('#filtroEstado').on('change', function() {
        const val = $(this).val();
        $.fn.dataTable.ext.search.pop();
        if (val !== "") {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const rowEstado = $(tabla.row(dataIndex).node()).attr('data-estado');
                return rowEstado === val;
            });
        }
        tabla.draw();
    });
});
function togglePerso() {
        $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
        getMantenimientos();
    }
function nuevoVehiculo() {
    $('#formVehiculo')[0].reset();
    $('#v_id').val('0');
    if ($('#v_almacen_id').is('select')) $('#v_almacen_id').val('');
    $('#modalTitulo').html('<i class="bi bi-plus-circle me-2"></i>Añadir Nueva Unidad');
    $('#modalVehiculo').modal('show');
}

function editarVehiculo(v) {
    $('#modalTitulo').html('<i class="bi bi-pencil-square me-2"></i>Gestionar Unidad');
    $('#v_id').val(v.id);
    $('#v_nombre').val(v.nombre);
    $('#v_placas').val(v.placas);
    $('#v_modelo').val(v.modelo_año);
    $('#v_capacidad').val(v.capacidad_carga_kg);
    $('#v_estado').val(v.estado_unidad);
     $('#v_tipo').val(v.tipo);
    $('#v_vin').val(v.serie_vin);
    if ($('#v_almacen_id').is('select')) $('#v_almacen_id').val(v.almacen_id);
    $('#modalVehiculo').modal('show');
}

$('#formVehiculo').on('submit', async function(e) {
    e.preventDefault();
    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const formData = new FormData(this);
        const resp = await fetch('/myvet/app/controllers/vehiculosController.php', { method: 'POST', body: formData });
        const res = await resp.json();
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: '¡Éxito!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
        } else {
            Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
        }
    } catch (error) { Swal.fire('Error', 'Error de Conexión', 'error'); }
});

async function eliminarVehiculo(id) {
    const confirmacion = await Swal.fire({
        title: '¿Confirmar Baja?',
        text: "La unidad se marcará como inactiva.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3b30',
        confirmButtonText: 'Sí, dar de baja'
    });
    if (confirmacion.isConfirmed) {
        const fd = new FormData();
        fd.append('action', 'eliminar');
        fd.append('id', id);
        const resp = await fetch('/myvet/app/controllers/vehiculosController.php', { method: 'POST', body: fd });
        const res = await resp.json();
        if (res.status === 'success') location.reload();
    }
}

function limpiarFiltros() {
    $('#busquedaVehiculo').val('');
    $('#filtroEstado').val('');
    $.fn.dataTable.ext.search.pop();
    tabla.search('').draw();
}

function subirDocumentoCompra(vehiculo_id) {
    
                
           

    Swal.fire({
        title: 'Documento de Vehiculo',
        html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar documento</label>
                <input type="file" id="swal_file_doc" class="form-control mb-2" accept=".pdf,image/*">
                
                
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#198754',
        focusConfirm: false,

        preConfirm: async () => {

            const fileInput = document.getElementById('swal_file_doc');
            const file = fileInput?.files[0];

            if (!file) {
                Swal.showValidationMessage('Selecciona un archivo');
                return false;
            }

            const formData = new FormData();
            
            formData.append('vehiculo_id', vehiculo_id);
           
            formData.append('documento', file);
            console.log(file,vehiculo_id);
            
             

            try {

    const response = await fetch(
        '/myvet/app/controllers/vehiculosController.php?action=subirDocumento',
        {
            method: 'POST',
            body: formData
        }
    );

    console.log('Status:', response.status);

    const text = await response.text();

    console.log('Respuesta completa:', text);

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }

    let res;

    try {
        res = JSON.parse(text);
    } catch {
        throw new Error('El servidor devolvió HTML o texto inválido');
    }

    if (!res.success) {
        throw new Error(res.message || 'Error al subir archivo');
    }

    return res;

} catch (err) {
    console.error(err);
    Swal.showValidationMessage(err.message);
    return false;
}
        }

    }).then(result => {

        if (!result.isConfirmed || !result.value) return;

       Swal.fire({
    icon: 'success',
    title: 'Guardado',
    text: 'Documento actualizado correctamente',
    timer: 1800,
    showConfirmButton: false
}).then(() => {
    location.reload();
});
       
    });
}

function eliminarDocumento(id) {
    
                console.log('gasto');
           

    Swal.fire({
        title: 'Eliminar Documento',
        
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#ed0909',
        focusConfirm: false,

        preConfirm: async () => {

         

            const formData = new FormData();
            
             formData.append('id', id);
             

            try {
                const response = await fetch('/myvet/app/controllers/vehiculosController.php?action=eliminarDocumento', {
                    method: 'POST',
                    body: formData
                });

                // 🔥 LEEMOS COMO TEXTO PRIMERO (ANTI "Unexpected token <")
                const text = await response.text();
                console.log('RESPUESTA CRUDA:', text);

                let res;
                try {
                    res = JSON.parse(text);
                } catch (e) {
                    throw new Error('El servidor no devolvió JSON válido');
                }

                if (!res.success) {
                    throw new Error(res.message || 'Error al subir archivo');
                }

                return res;

            } catch (err) {
                Swal.showValidationMessage(err.message);
                return false;
            }
        }

    }).then(result => {

        if (!result.isConfirmed || !result.value) return;

       Swal.fire({
    icon: 'success',
    title: 'Eliminado',
    text: 'Documento eliminado correctamente',
    timer: 1800,
    showConfirmButton: false
}).then(() => {
    location.reload();
});
        if (typeof cargarCompras === 'function') {
            cargarCompras();
        }
    });
}
async function verDetalle(id) {
        try { 
            // 🔥 OBTENER IDS PENDIENTES
            const respIds = await fetch(
                `/myvet/app/controllers/mantenimientosController.php?action=obtenerDetalle&id=${id}`
            );
            let data=await respIds.json();
            console.log(data);
            verDetalleMantenimiento(data)
           
        } catch (error) {
            console.error("Error al obtener detalle:", error);
        }
    }
    function verDetalleMantenimiento(response) {
    if (response.status !== 'success' || !response.data) {
        Swal.fire('Error', 'No se pudo leer la información del mantenimiento.', 'error');
        return;
    }

    const info = response.data;

    // Inyectar datos planos de texto en el modal
    $('#det-id_mantenimiento').text(info.id_mantenimiento);
    $('#det-razon').text(info.razon || 'MANTENIMIENTO GENERAL');
    $('#det-creado_en').text(info.creado_en);
    $('#det-vehiculo').text(info.vehiculo);
    $('#det-modelo').text(info.modelo);
    $('#det-placas').text(info.placas);
    $('#det-almacen').text(info.almacen);
    $('#det-fecha').text(info.fecha);
    $('#det-fecha_proximo').text(info.fecha_proximo_mantenimiento);

    // Darle un formato elegante al kilometraje (ej: 100,000 Km)
    if (info.kilometraje) {
        const kmFormateado = Number(info.kilometraje).toLocaleString('es-MX') + ' Km';
        $('#det-kilometraje').text(kmFormateado);
    } else {
        $('#det-kilometraje').text('N/D');
    }

    // Configurar estéticamente el Badge según el tipo de mantenimiento
    const badgeTipo = $('#det-badge-tipo');
    badgeTipo.text(info.tipo_mantenimiento);

    if (info.tipo_mantenimiento === 'PREVENTIVO') {
        badgeTipo.removeClass('bg-danger bg-warning').addClass('bg-success-subtle text-success border border-success-subtle');
    } else if (info.tipo_mantenimiento === 'CORRECTIVO') {
        badgeTipo.removeClass('bg-success bg-warning').addClass('bg-danger-subtle text-danger border border-danger-subtle');
    } else {
        badgeTipo.removeClass('bg-success bg-danger').addClass('bg-warning-subtle text-warning border border-warning-subtle');
    }

    // Desplegar el modal en pantalla
    $('#modalDetalleMantenimiento').modal('show');
}
   
</script>
</body>
</html>