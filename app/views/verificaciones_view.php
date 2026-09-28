<?php
/**
 * verificaciones_view.php 
 * Vista de Control y Gestión de Verificaciones Vehiculares - cfsistem
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificaciones | cfsistem</title>
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
            --glass-bg: rgba(255, 255, 255, 0.90);
            --accent-color: #0d6efd;
        }

        body { 
            min-height: 100vh;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .main-content { 
             
            padding: 35px; 
           
            transition: all 0.3s ease;
        }

        .card-premium {
          
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }

        .table thead th {
            background: #000000;
            color: #f8fafc;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            
            padding: 14px 12px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover { 
            background: rgba(13, 110, 253, 0.03) !important; 
        }

        .btn-action {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) { 
            .main-content { 
                margin-left: 0; 
                padding: 15px; 
                padding-top: 90px; 
            } 
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">

        <!-- ===================== ENCABEZADO ===================== -->
        <div class="card card-premium  mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="bi bi-shield-check fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold  mb-1">Verificaciones Vehiculares</h3>
                                <p class=" mb-0 small">
                                    Control de emisión, cumplimiento normativo y calendario de vigencias
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mt-3 mt-lg-0">
                        <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                            <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-semibold"
                                onclick="nuevoModalVerificacion()">
                                <i class="bi bi-patch-plus me-2"></i>
                                Nueva Verificación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== FILTROS DE BÚSQUEDA ===================== -->
        <div class="card card-premium  mb-4">
            <div class="card-header bg-transparent  pt-3 px-4 pb-0">
                <h6 class="mb-0 fw-bold ">
                    <i class="bi bi-funnel-fill text-primary me-2"></i>Filtros de Búsqueda
                </h6>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 align-items-end">

                    <!-- Buscar por texto -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold small ">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text  border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text"
                                id="f_search"
                                class="form-control border-start-0 ps-0"
                                placeholder="ID, vehículo, placas..."
                                onkeyup="getverificaciones()">
                        </div>
                    </div>

                    <!-- Vehículo -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold small ">Vehículo</label>
                        <select class="form-select" id="select-vehiculos" onchange="getverificaciones()">
                            <option value="">Todos</option>
                            <?php if(!empty($vehiculos)): foreach($vehiculos as $ve): ?>
                              <option value="<?= $ve['id'] ?>" data-almacen-id="<?= $ve['almacen_id'] ?>">
            <?= htmlspecialchars($ve['nombre']) ?> (<?= htmlspecialchars($ve['placas']) ?>) (<?= htmlspecialchars($ve['tipo']) ?>)
        </option>
         <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <!-- Periodo -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold small ">Periodo</label>
                        <select id="f_rango" class="form-select" onchange="togglePerso()">
                            <option value="hoy">Hoy</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Esta Semana</option>
                            <option value="mes">Este Mes</option>
                            <option value="todos" selected>Historial Completo</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>

                    <!-- Rango Fechas (Oculto por defecto) -->
                    <div class="col-lg-3 col-md-6 d-none" id="div_p">
                        <label class="form-label fw-semibold small ">Rango de fechas</label>
                        <div class="input-group">
                            <input type="date" id="f_ini" class="form-control" onchange="getverificaciones()">
                            <span class="input-group-text ">a</span>
                            <input type="date" id="f_fin" class="form-control" onchange="getverificaciones()">
                        </div>
                    </div>

                    <!-- Almacén -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold small ">Almacén</label>
                        <select id="f_almacen" class="form-select" onchange="getverificaciones()">
                         <?php if ($_SESSION['almacen_id']==0): ?>
                                                <option value="">Seleccionar ubicación...</option>
                                                <?php endif; ?>
                            <?php if(!empty($almacenes)): foreach($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= (isset($_SESSION['almacen_id']) && $a['id'] == $_SESSION['almacen_id']) ? 'selected':'' ?>>
                                    <?= htmlspecialchars($a['nombre']) ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        <!-- ===================== TABLA DE HISTORIAL ===================== -->
        <div class="card card-premium ">
            <div class="card-header bg-transparent  p-4 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-list-check text-primary me-2"></i>
                        Registro de Verificaciones
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 rounded-pill px-3 py-2">
                        Historial Oficial
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 65vh;">
                    <table class="table table-hover align-middle mb-0" id="tablaMantenimientos">
                        <thead>
                            <tr>
                                <th>Folio ID</th>
                                <th class="ps-4">Fecha Trámite</th>                               
                                <th>Almacén</th>
                                <th>Vehículo</th>
                                <th class="text-center">Próxima Verificación</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Carga mediante AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>       

    </main>

    <!-- ===================== MODAL DETALLE DE VERIFICACIÓN ===================== -->
    <div class="modal fade" id="modalDetalleMantenimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 24px; overflow: hidden;">
                
                <div class="modal-header bg-dark text-white p-4 ">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-file-earmark-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">Detalle de Verificación</h5>
                            <small class="text-white-50">Registro Folio: #<span id="det-id_mantenimiento"></span></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 ">
                    
                    <!-- Vehículo -->
                    <div class="card  shadow-sm rounded-4 p-3 mb-3 ">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.7rem;">Unidad Vehicular</small>
                                <h5 class="fw-bold  mb-0" id="det-vehiculo">---</h5>
                                <small class="" id="det-modelo">---</small>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-2 fw-bold" id="det-placas">
                                ---
                            </span>
                        </div>
                    </div>

                    <!-- Fechas y Almacén -->
                    <div class="card  shadow-sm rounded-4 p-3 ">
                        <div class="mb-3 border-bottom pb-2">
                            <small class="text-muted d-block">Almacén / Sucursal</small>
                            <span class="fw-semibold text-dark" id="det-almacen">---</span>
                        </div>
                        <div class="row text-center g-2">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block mb-1">Fecha Realizada</small>
                                <span class="fw-bold text-dark" id="det-fecha">--/--/----</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Próxima Verificación</small>
                                <span class="fw-bold text-danger" id="det-fecha_proximo">--/--/----</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer   p-3 justify-content-center">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                        Cerrar Vista
                    </button>
                </div>

            </div>
        </div>
    </div>
  <!-- <button type="button" class="btn btn-action btn-light border text-primary" 
                                    onclick="verDetalle(${m.id})" 
                                    title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </button> -->
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php require_once __DIR__ . '/verificacionesComponents/modalVerificaciones.php'; ?>

    <script>
        // Convertir automáticamente a mayúsculas
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });

        $(document).ready(function() {
            getverificaciones();
        });

        async function getverificaciones() {
            const params = new URLSearchParams({
                action: 'listar',
                f_search: $('#f_search').val(),
                f_rango: $('#f_rango').val(),
                f_inicio: $('#f_ini').val(),
                f_fin: $('#f_fin').val(),
                f_almacen: $('#f_almacen').val(),
                f_vehiculo: $('#select-vehiculos').val() ?? ''
            });

            try {
                const res = await fetch(`/myvet/app/controllers/verificacionesController.php?${params.toString()}`);
                const data = await res.json();
                
                if (!Array.isArray(data)) {
                    $('#tablaMantenimientos tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron registros</td></tr>');
                    return;
                }

                $('#tablaMantenimientos tbody').html(data.map(m => {
                    return `<tr>
                       
                        <td class="fw-bold ">#${m.id}</td>
                         <td class="ps-4 small ">${m.fecha}</td>
                        <td><span class="badge bg-light text-dark border font-normal">${m.almacen}</span></td>
                        <td><div class="fw-semibold ">${m.vehiculo} (${m.placas})</div></td>
                        <td class="text-center">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-3 py-2">
                                <i class="bi bi-calendar-event me-1"></i>${m.proxima_verificacion}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                          
                            <button type="button" class="btn btn-action btn-light border text-danger" 
            onclick="eliminarVerificacion(${m.id})" 
            title="Eliminar Verificación">
        <i class="bi bi-trash"></i>
    </button>
                        </td>
                    </tr>`;
                }).join(''));

            } catch (e) {
                console.error("Error al cargar verificaciones:", e);
                $('#tablaMantenimientos tbody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Error al cargar datos.</td></tr>');
            }
        }

        function togglePerso() {
            $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
            getverificaciones();
        }

        async function verDetalle(id) {
            try { 
                const resp = await fetch(`/myvet/app/controllers/verificacionesController.php?action=obtenerDetalle&id=${id}`);
                const data = await resp.json();
                verDetalleMantenimiento(data);
            } catch (error) {
                console.error("Error al obtener detalle:", error);
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        }

        function verDetalleMantenimiento(response) {
            if (response.status !== 'success' || !response.data) {
                Swal.fire('Error', 'No se encontró la información solicitada.', 'error');
                return;
            }

            const info = response.data;

            $('#det-id_mantenimiento').text(info.id || info.id_verificacion || 'N/A');
            $('#det-vehiculo').text(info.vehiculo || info.nombre_vehiculo || 'N/D');
            $('#det-modelo').text(info.modelo || '');
            $('#det-placas').text(info.placas || 'SIN PLACAS');
            $('#det-almacen').text(info.almacen || 'General');
            $('#det-fecha').text(info.fecha || info.fecha_verificacion);
            $('#det-fecha_proximo').text(info.proxima_verificacion || info.fecha_proxima_verificacion);

            $('#modalDetalleMantenimiento').modal('show');
        }
     function eliminarVerificacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Se eliminará el registro de verificación con Folio #${id}. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4  shadow-lg',
            confirmButton: 'rounded-pill px-4',
            cancelButton: 'rounded-pill px-4'
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera un momento.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData = new FormData();
                formData.append('id', id);

                // CORREGIDO: Se añade ?action=eliminar a la URL
                const response = await fetch('/myvet/app/controllers/verificacionesController.php?action=eliminar', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success' || data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: data.message || 'La verificación ha sido eliminada correctamente.',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4  shadow-lg' }
                    });
                    
                    getverificaciones();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el registro.',
                        customClass: { popup: 'rounded-4  shadow-lg' }
                    });
                }

            } catch (error) {
                console.error("Error al eliminar verificación:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de servidor',
                    text: 'Ocurrió un fallo al comunicarse con el servidor.',
                    customClass: { popup: 'rounded-4  shadow-lg' }
                });
            }
        }
    });
}
    </script>
</body>
</html>