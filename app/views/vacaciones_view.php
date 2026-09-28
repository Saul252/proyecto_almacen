<?php
/**
 * vacaciones_view.php 
 * Vista de Control y Gestión de Vacaciones de Trabajadores - cfsistem
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacaciones | cfsistem</title>
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
            background: #1e293b;
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
                                <i class="bi bi-calendar2-check fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1">Gestión de Vacaciones</h3>
                                <p class="mb-0 small">
                                    Control de días solicitados, saldos restantes y retenciones
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mt-3 mt-lg-0">
                        <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                            <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-semibold"
                                onclick="nuevoModalVacaciones()">
                                <i class="bi bi-plus-circle me-2"></i>
                                Registrar Vacaciones
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== FILTROS DE BÚSQUEDA ===================== -->
        <div class="card card-premium  mb-4">
            <div class="card-header bg-transparent  pt-3 px-4 pb-0">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-funnel-fill text-primary me-2"></i>Filtros de Búsqueda
                </h6>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 align-items-end">

                    <!-- Buscar por texto -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold small">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text"
                                id="f_search"
                                class="form-control border-start-0 ps-0"
                                placeholder="Folio, trabajador..."
                                onkeyup="getVacaciones()">
                        </div>
                    </div>
 <div class="col-lg-2">

                <label class="form-label fw-semibold small">
                    Almacén
                </label>

               <select
    id="f_almacen"
    class="form-select"
   >

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
                    <!-- Trabajador -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold small">Trabajador</label>
                        <select class="form-select" id="select-trabajadores" onchange="getVacaciones()">
                            <option value="">Todos</option>
                            <?php if(!empty($trabajadores)): foreach($trabajadores as $t): ?>
                                <option value="<?= $t['id'] ?>"data-almacen-id="<?= $t['almacen_id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <!-- Periodo -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold small">Periodo</label>
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
                    <div class="col-lg-4 col-md-6 d-none" id="div_p">
                        <label class="form-label fw-semibold small">Rango de fechas</label>
                        <div class="input-group">
                            <input type="date" id="f_ini" class="form-control" onchange="getVacaciones()">
                            <span class="input-group-text">a</span>
                            <input type="date" id="f_fin" class="form-control" onchange="getVacaciones()">
                        </div>
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
                        Registro de Solicitudes
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 rounded-pill px-3 py-2">
                        Historial General
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 65vh;">
                    <table class="table table-hover align-middle mb-0" id="tablaVacaciones">
                        <thead>
                            <tr>
                                <th class="ps-4">Fecha</th>
                                <th>Folio ID</th>
                                <th>Trabajador</th>
                                <th class="text-center">Días de vacaciones disponibles</th>
                                <th class="text-center">Días a Tomar</th>
                                <th class="text-center">Días por pagar</th>
                                <th class="text-end">Monto Restante</th>
                                <th class="text-end">Retenciones</th>
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

    <!-- ===================== MODAL DETALLE DE VACACIONES ===================== -->
    <div class="modal fade" id="modalDetalleVacaciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 24px; overflow: hidden;">
                
                <div class="modal-header bg-dark text-white p-4 ">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-card-checklist fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">Detalle de Vacaciones</h5>
                            <small class="text-white-50">Folio Registro: #<span id="det-id_vacaciones"></span></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    
                    <!-- Trabajador -->
                    <div class="card  shadow-sm rounded-4 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.7rem;">Trabajador / Empleado</small>
                                <h5 class="fw-bold mb-0" id="det-trabajador">---</h5>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-2 fw-bold" id="det-fecha">
                                --/--/----
                            </span>
                        </div>
                    </div>

                    <!-- Desglose de Días y Montos -->
                    <div class="card  shadow-sm rounded-4 p-3">
                        <div class="row text-center g-2 border-bottom pb-3 mb-3">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block mb-1">Días Disponibles</small>
                                <span class="fw-bold text-dark fs-5" id="det-dias_disponibles">0</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Días a Tomar</small>
                                <span class="fw-bold text-primary fs-5" id="det-dias_a_tomar">0</span>
                            </div>
                        </div>

                        <div class="row text-center g-2">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block mb-1">Monto Restante</small>
                                <span class="fw-bold text-success fs-6" id="det-monto_restante">$0.00</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Retenciones</small>
                                <span class="fw-bold text-danger fs-6" id="det-retenciones">$0.00</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer  p-3 justify-content-center">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                        Cerrar Vista
                    </button>
                </div>

            </div>
        </div>
    </div>
<!-- ===================== MODAL AGREGAR / EDITAR VACACIONES ===================== -->
<div class="modal fade" id="modalVacaciones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 24px; overflow: hidden;">
            
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-dark text-white p-4 ">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-calendar-plus fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white" id="modalVacacionesLabel">Registrar Vacaciones</h5>
                        <small class="text-white-50">Captura de días y montos correspondientes</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Formulario -->
            <form id="formVacaciones" onsubmit="guardarVacaciones(event)">
                <div class="modal-body p-4">
                    
                    <!-- Campo oculto para ID (en caso de edición) -->
                    <input type="hidden" id="v_id" name="id" value="0">

                    <!-- Selección de Trabajador -->
                    <div class="mb-3">
                        <label for="v_id_trabajador" class="form-label fw-semibold small text-secondary">
                            Trabajador <span class="text-danger">*</span>
                        </label>
                        <select class="form-select rounded-3" id="v_id_trabajador" name="id_trabajador" required>
                            <option value="" disabled selected>-- Selecciona un trabajador --</option>
                            <?php if(!empty($trabajadores)): foreach($trabajadores as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label for="v_fecha" class="form-label fw-semibold small text-secondary">
                            Fecha de Registro / Trámite <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control rounded-3" id="v_fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <!-- Días Disponibles y Días a Tomar -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="v_dias_disponibles" class="form-label fw-semibold small text-secondary">Días Disponibles</label>
                            <input type="number" class="form-control rounded-3" id="v_dias_disponibles" name="dias_disponibles" min="0" value="0" required>
                        </div>
                        <div class="col-6">
                            <label for="v_dias_a_tomar" class="form-label fw-semibold small text-secondary">Días a Tomar</label>
                            <input type="number" class="form-control rounded-3" id="v_dias_a_tomar" name="dias_a_tomar" min="0" value="0" required>
                        </div>
                    </div>

                    <!-- Monto Restante y Retenciones -->
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <label for="v_monto_restante" class="form-label fw-semibold small text-secondary">Monto Restante ($)</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="v_monto_restante" name="monto_restante" min="0" value="0.00" required>
                        </div>
                        <div class="col-6">
                            <label for="v_retenciones" class="form-label fw-semibold small text-secondary">Retenciones ($)</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="v_retenciones" name="retenciones" min="0" value="0.00" required>
                        </div>
                    </div>

                </div>

                <!-- Botones de Acción -->
                <div class="modal-footer  p-3 justify-content-end bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="btnGuardarVacaciones">
                        <i class="bi bi-save me-1"></i> Guardar Registro
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   

<script>

    $(document.ready).ready(function() {
    // Guardamos una copia de todas las opciones originales de vehículos
    const $trabajadorSelect = $('#select-trabajadores');
    const $trabajadorSelectModal = $('#v_id_trabajador');
    const $vehiculoOptions = $trabajadorSelect.find('option').clone();

    $('#f_almacen').on('change', function() {
        const almacenId = $(this).val();

        // Limpiar el select de vehículos
        $trabajadorSelect.empty();
        $trabajadorSelectModal.empty();

        if (almacenId) {
            // Filtrar y agregar solo la opción por defecto y las que coincidan con el almacén
            $vehiculoOptions.each(function() {
                const optionAlmacenId = $(this).data('almacen-id');
                
                // Incluye la opción inicial ("Seleccione vehículo...") o las que coincidan
                if (!optionAlmacenId || optionAlmacenId == almacenId) {
                    $trabajadorSelect.append($(this).clone());
                     $trabajadorSelectModal.append($(this).clone());
                }
            });
        } else {
            // Si no hay almacén seleccionado, mostrar solo la opción por defecto
            $trabajadorSelect.append($vehiculoOptions.first().clone());
             $vehiculoOptions.each(function() {
                const optionAlmacenId = $(this).data('almacen-id');
                
                // Incluye la opción inicial ("Seleccione vehículo...") o las que coincidan
                
                    $trabajadorSelect.append($(this).clone());
                     $trabajadorSelectModal.append($(this).clone());
              
            });
        }

        // Reinicializar Select2 para refrescar la lista visible
        $trabajadorSelect.val('').trigger('change.select2');
    });
});
    // Abrir modal para NUEVO registro
    function nuevoModalVacaciones() {
        $('#formVacaciones')[0].reset();
        $('#v_id').val(0);
        $('#v_fecha').val(new Date().toISOString().split('T')[0]);
        $('#modalVacacionesLabel').text('Registrar Vacaciones');
        $('#modalVacaciones').modal('show');
    }

    // Abrir modal para EDITAR registro
    function editarModalVacaciones(data) {
        $('#formVacaciones')[0].reset();
        $('#v_id').val(data.id || 0);
        $('#v_id_trabajador').val(data.id_trabajador || '');
        $('#v_fecha').val(data.fecha || new Date().toISOString().split('T')[0]);
        $('#v_dias_disponibles').val(data.dias_disponibles || 0);
        $('#v_dias_a_tomar').val(data.dias_a_tomar || 0);
        $('#v_monto_restante').val(data.monto_restante || 0.00);
        $('#v_retenciones').val(data.retenciones || 0.00);
        
        $('#modalVacacionesLabel').text('Editar Registro de Vacaciones');
        $('#modalVacaciones').modal('show');
    }

    // Enviar datos vía AJAX / Fetch al controlador
    async function guardarVacaciones(e) {
        e.preventDefault();

        const btnGuardar = $('#btnGuardarVacaciones');
        btnGuardar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Guardando...');

        const formData = new FormData(document.getElementById('formVacaciones'));
        const dataJson = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/myvet/app/controllers/vacacionesController.php?action=guardar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dataJson)
            });

            const result = await response.json();

            if (result.status === 'success' || result.success) {
                $('#modalVacaciones').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: result.message || 'El registro se ha guardado correctamente.',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4  shadow-lg' }
                });

                // Recargar tabla de datos si existe la función en la vista
                if (typeof getVacaciones === 'function') {
                    getVacaciones();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al Guardar',
                    text: result.message || 'Ocurrió un inconveniente al procesar la solicitud.',
                    customClass: { popup: 'rounded-4  shadow-lg' }
                });
            }
        } catch (error) {
            console.error('Error en guardarVacaciones:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Red',
                text: 'No se pudo establecer comunicación con el servidor.',
                customClass: { popup: 'rounded-4  shadow-lg' }
            });
        } finally {
            btnGuardar.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar Registro');
        }
    }

        // Convertir automáticamente a mayúsculas en campos de texto
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });

        $(document).ready(function() {
            getVacaciones();
        });

        async function getVacaciones() {
            const params = new URLSearchParams({
                action: 'listar',
                f_search: $('#f_search').val(),
                f_almacen: $('#f_almacen').val(),
                f_rango: $('#f_rango').val(),
                f_inicio: $('#f_ini').val(),
                f_fin: $('#f_fin').val(),
                f_trabajador: $('#select-trabajadores').val() ?? ''
            });

            try {
                const res = await fetch(`/myvet/app/controllers/vacacionesController.php?${params.toString()}`);
                const data = await res.json();
                
                if (!Array.isArray(data) || data.length === 0) {
                    $('#tablaVacaciones tbody').html('<tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron registros de vacaciones</td></tr>');
                    return;
                }

                $('#tablaVacaciones tbody').html(data.map(v => {
                    return `<tr>
                        <td class="ps-4 small">${v.fecha}</td>
                        <td class="fw-bold">#${v.id}</td>
                        <td><div class="fw-semibold">${v.trabajador || 'N/D'}</div></td>
                        <td class="text-center"><span class="badge bg-light text-dark border">${v.dias_disponibles} días</span></td>
                        <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1 fw-bold">${v.dias_a_tomar} días</span></td>
                          <td class="text-center"><span class="badge bg-light text-dark border">${(v.dias_disponibles)-(v.dias_a_tomar)} días</span></td>
                        <td class="text-end fw-semibold text-success">$${parseFloat(v.monto_restante).toFixed(2)}</td>
                        <td class="text-end fw-semibold text-danger">$${parseFloat(v.retenciones).toFixed(2)}</td>
                        <td class="text-center pe-4">
                            <button type="button" class="btn btn-action btn-light border text-primary" 
                                    onclick="verDetalle(${v.id})" 
                                    title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-action btn-light border text-danger" 
                                    onclick="eliminarVacaciones(${v.id})" 
                                    title="Eliminar Registro">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                }).join(''));

            } catch (e) {
                console.error("Error al cargar vacaciones:", e);
                $('#tablaVacaciones tbody').html('<tr><td colspan="8" class="text-center py-4 text-danger">Error al cargar datos.</td></tr>');
            }
        }

        function togglePerso() {
            $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
            getVacaciones();
        }

        async function verDetalle(id) {
            try { 
                const resp = await fetch(`/myvet/app/controllers/vacacionesController.php?action=obtenerDetalle&id=${id}`);
                const data = await resp.json();
                verDetalleVacaciones(data);
            } catch (error) {
                console.error("Error al obtener detalle:", error);
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        }

        function verDetalleVacaciones(response) {
            if (response.status !== 'success' || !response.data) {
                Swal.fire('Error', 'No se encontró la información solicitada.', 'error');
                return;
            }

            const info = response.data;
            console.log(info);

            $('#det-id_vacaciones').text(info.id || 'N/A');
            $('#det-trabajador').text( info.nombre|| 'N/D');
            $('#det-fecha').text(info.fecha || '--/--/----');
            $('#det-dias_disponibles').text(info.dias_disponibles || 0);
            $('#det-dias_a_tomar').text(info.dias_a_tomar || 0);
            $('#det-monto_restante').text('$' + parseFloat(info.monto_restante || 0).toFixed(2));
            $('#det-retenciones').text('$' + parseFloat(info.retenciones || 0).toFixed(2));

            $('#modalDetalleVacaciones').modal('show');
        }

        function eliminarVacaciones(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Se eliminará el registro de vacaciones con Folio #${id}. Esta acción no se puede deshacer.`,
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

                        const response = await fetch('/myvet/app/controllers/vacacionesController.php?action=eliminar', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (data.status === 'success' || data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: data.message || 'El registro ha sido eliminado correctamente.',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-4  shadow-lg' }
                            });
                            
                            getVacaciones();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo eliminar el registro.',
                                customClass: { popup: 'rounded-4  shadow-lg' }
                            });
                        }

                    } catch (error) {
                        console.error("Error al eliminar vacaciones:", error);
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