<?php
/**
 * almacenes_view.php
 * Vista de administración de almacenes: Vista simplificada, Modales independientes (Crear / Editar todo) y Soporte para Modo Oscuro.
 */
$estadosPago = ['al_dia' => 'Al día', 'pendiente' => 'Pendiente', 'vencido' => 'Vencido'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 es Admin Global
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenes | cfsistem</title>
    <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
            --accent-blue: #007aff;
        }

        .main-content { 
            padding: 40px; 
        }

        .card-premium { 
            border-radius: 20px; 
            box-shadow: 0 8px 30px rgba(0,0,0,0.06); 
        }

        .badge-ubicacion { 
            padding: 0.4rem 0.7rem;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* DataTables Dynamic Dark Mode Support */
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #fff !important;
            border-radius: 8px;
        }

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } 
        }
    </style>
</head>
<body class="bg-body">
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold m-0 text-body" style="font-size: 2rem; letter-spacing: -0.03em;">Gestión de Almacenes</h2>
                <p class="text-body-secondary mb-0">Resumen general y administración de sucursales</p>
            </div>
             <button class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center" 
                    onclick="abrirModalCrear()" style="background: var(--accent-blue); border:none; height: 42px; font-weight: 600;">
                <i class="bi bi-plus-circle-fill me-2 fs-5"></i> NUEVO ALMACÉN
            </button>
            <?php if ($almacen_usuario == 0): ?>
           
            <?php endif; ?>
        </div>

        <div class="card card-premium p-4 bg-body-tertiary border-subtle animate__animated animate__fadeInUp">
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="input-group border border-subtle rounded-3 p-1 bg-body">
                        <span class="input-group-text bg-transparent border-0 text-body-secondary"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaAlmacen" class="form-control bg-transparent border-0 shadow-none text-body" placeholder="Buscar por código, nombre o ubicación...">
                    </div>
                </div>

                <div class="col-md-4">
                    <select id="filtroPlan" class="form-select border-subtle rounded-3 h-100 shadow-none bg-body text-body">
                        <option value="">🌐 Todos los Planes</option>
                         <?php if (!empty($planes) && is_array($planes)): ?>
        <?php foreach ($planes as $k): ?>
            <option value="<?= $k['id'] ?>">
                <?= htmlspecialchars($k['nombre']) ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100 rounded-3 fw-bold" onclick="limpiarFiltros()">
                        <i class="bi bi-arrow-clockwise me-1"></i> RESET
                    </button>
                </div>
            </div>

            <!-- TABLA: SOLO ESTADO GENERAL -->
            <div class="table-responsive">
                <table id="tablaAlmacenes" class="table table-hover align-middle w-100 text-body">
                    <thead class="table-light-subtle">
                        <tr>
                            <th class="ps-3">Almacén</th>
                            <th>Ubicación</th>
                            <th>Estado de Pago</th>
                            <th>Estado Cuenta</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($almacenes as $a): ?>
                        <tr class="fila-almacen" data-plan="<?= strtoupper($a['tipo_plan']) ?>">
                            <td class="ps-3">
                                <div class="fw-bold text-body fs-6"><?= htmlspecialchars($a['nombre']) ?></div>
                                <span class="badge bg-body-secondary text-body border border-subtle px-2 py-1 fw-bold" style="font-family: monospace;">
                                    <?= htmlspecialchars($a['codigo']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-ubicacion bg-body-secondary text-body border border-subtle">
                                    <i class="bi bi-geo-alt me-1 text-primary"></i>
                                    <?= htmlspecialchars($a['ubicacion'] ?? 'Sin Ubicación') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $a['pago'] === 'al_dia' ? 'success' : ($a['pago'] === 'pendiente' ? 'warning text-dark' : 'danger') ?> px-2 py-1">
                                    <?= ucfirst(htmlspecialchars($a['pago'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" <?= $a['activo'] ? 'checked' : '' ?> 
                                           onchange="cambiarEstado(<?= $a['id'] ?>, this.checked ? 1 : 0)" id="switch_<?= $a['id'] ?>">
                                    <label class="form-check-label small fw-medium text-body" for="switch_<?= $a['id'] ?>">
                                        <?= $a['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </label>
                                </div>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" onclick="editarAlmacen(<?= $a['id'] ?>)">
                                    <i class="bi bi-sliders me-1"></i> Gestionar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL CREAR ALMACÉN -->
    <div class="modal fade" id="modalCrearAlmacen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg bg-body border-subtle" style="border-radius: 24px; overflow: hidden;">
                <form id="formCrearAlmacen">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold px-2"><i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Almacén</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-body-secondary">CÓDIGO *</label>
                                <input type="text" name="codigo" class="form-control bg-body text-body border-subtle text-uppercase rounded-3" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-body-secondary">NOMBRE DEL ALMACÉN *</label>
                                <input type="text" name="nombre" class="form-control bg-body text-body border-subtle text-uppercase rounded-3" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-body-secondary">UBICACIÓN / DIRECCIÓN</label>
                                <input type="text" name="ubicacion" class="form-control bg-body text-body border-subtle text-uppercase rounded-3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-body-secondary">HORA CIERRE</label>
                                <input type="time" name="hora_cierre_programada" class="form-control bg-body text-body border-subtle rounded-3" value="22:00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">TIPO DE PLAN</label>
                               <select name="tipo_plan" id="tipo_plan" class="form-select bg-body text-body border-subtle rounded-3">
    <option value="">-- Selecciona un plan --</option>
    <?php if (!empty($planes) && is_array($planes)): ?>
        <?php foreach ($planes as $k): ?>
            <option value="<?= $k['id'] ?>">
                <?= htmlspecialchars($k['nombre']) ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">ESTADO DE PAGO</label>
                                <select name="pago" class="form-select bg-body text-body border-subtle rounded-3">
                                   
                                        <option value="1">Pagado</option>
                                          <option value="0">Deuda</option>
                                   
                                </select>
                            </div>
                            <div class="col-12">
                                <hr class="border-subtle my-2">
                                <label class="form-label small fw-bold text-body-secondary">CONTRASEÑA INICIAL DE ACCESO</label>
                                <input type="password" name="password" class="form-control bg-body text-body border-subtle rounded-3" placeholder="Opcional (mínimo 6 caracteres)" minlength="6">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-body-tertiary border-top border-subtle px-4 py-3">
                        <button type="button" class="btn btn-link text-body-secondary fw-bold text-decoration-none" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow">CREAR ALMACÉN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR COMPLETO (EDITAR TODO) -->
    <div class="modal fade" id="modalEditarAlmacen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg bg-body border-subtle" style="border-radius: 24px; overflow: hidden;">
                <form id="formEditarAlmacen">
                    <input type="hidden" name="almacen_id" id="edit_almacen_id" value="0">
                    
                    <div class="modal-header bg-dark text-white py-3">
                        <h5 class="modal-title fw-bold px-2"><i class="bi bi-pencil-square me-2"></i>Editar Información General</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-body-secondary">CÓDIGO *</label>
                                <input type="text" name="codigo" id="edit_codigo" class="form-control bg-body text-body border-subtle text-uppercase rounded-3" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-body-secondary">NOMBRE DEL ALMACÉN *</label>
                                <input type="text" name="nombre" id="edit_nombre" class="form-control bg-body text-body border-subtle text-uppercase rounded-3" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-body-secondary">UBICACIÓN / DIRECCIÓN</label>
                                <input type="text" name="ubicacion" id="edit_ubicacion" class="form-control bg-body text-body border-subtle text-uppercase rounded-3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-body-secondary">HORA CIERRE</label>
                                <input type="time" name="hora_cierre_programada" id="edit_hora_cierre" class="form-control bg-body text-body border-subtle rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">TIPO DE PLAN</label>
                                  <select id="filtroPlan" class="form-select">
                        

                        <?php foreach($planes as $plan): ?>
                        <option value="<?= $plan['id'] ?>" >
                            <?= htmlspecialchars($plan['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">ESTADO DE PAGO</label>
                                <select name="pago" id="edit_pago" class="form-select bg-body text-body border-subtle rounded-3">
                                    
                                        <option value="1">Pagado</option>
                                          <option value="0">Deuda</option>
                                   
                                </select>
                            </div>

                            <!-- SECCIÓN PARA CAMBIAR CONTRASEÑA EN EL MISMO MODAL -->
                            
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-body-tertiary border-top border-subtle px-4 py-3">
                        <button type="button" class="btn btn-link text-body-secondary fw-bold text-decoration-none" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-success px-5 rounded-pill fw-bold shadow">GUARDAR CAMBIOS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    let tabla;

    $(document).ready(function() {
        console.log(<?= $planes ?> );
        tabla = $('#tablaAlmacenes').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            "dom": 'rt<"row mt-3 px-3"<"col-sm-12 col-md-5 small text-body-secondary"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 15,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "targets": [4], "orderable": false }
            ]
        });

        $('#busquedaAlmacen').on('keyup', function() { tabla.search(this.value).draw(); });

        $('#filtroPlan').on('change', function() {
            const val = $(this).val();
            $.fn.dataTable.ext.search.pop();
            if (val !== "") {
                $.fn.dataTable.ext.search.push(function(s, d, i) {
                    return $(tabla.row(i).node()).attr('data-plan') === val;
                });
            }
            tabla.draw();
        });
    });

    function limpiarFiltros() {
        $('#busquedaAlmacen').val('');
        $('#filtroPlan').val('');
        $.fn.dataTable.ext.search.pop();
        tabla.search('').draw();
    }

    function abrirModalCrear() {
        $('#formCrearAlmacen')[0].reset();
        $('#modalCrearAlmacen').modal('show');
    }

    async function editarAlmacen(id) {
        try {
            const resp = await fetch(`/myvet/app/controllers/controlAlmacenesController.php?action=obtenerPorId&id=${id}`);
            const res = await resp.json();
            if (res.success) {
                const a = res.data;
                $('#formEditarAlmacen')[0].reset();
                $('#edit_almacen_id').val(a.id);
                $('#edit_codigo').val(a.codigo);
                $('#edit_nombre').val(a.nombre);
                $('#edit_ubicacion').val(a.ubicacion);
                $('#edit_hora_cierre').val(a.hora_cierre_programada || '22:00');
                $('#edit_tipo_plan').val(a.tipo_plan);
                $('#edit_pago').val(a.pago);
                $('#modalEditarAlmacen').modal('show');
            } else {
                Swal.fire('Error', res.message || 'No se pudieron obtener los datos', 'error');
            }
        } catch (e) { 
            console.error(e);
            Swal.fire('Error', 'Ocurrió un error al consultar la información', 'error');
        }
    }

    // Submit para Crear Almacén
    $('#formCrearAlmacen').on('submit', async function(e) {
        e.preventDefault();
        try {
            const resp = await fetch('/myvet/app/controllers/controlAlmacenesController.php?action=guardar', {
                method: 'POST',
                body: new FormData(this)
            });
            const res = await resp.json();
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { console.error(e); }
    });

    // Submit para Editar Todo el Almacén
    $('#formEditarAlmacen').on('submit', async function(e) {
        e.preventDefault();
        try {
            const resp = await fetch('/myvet/app/controllers/controlAlmacenesController.php?action=guardar', {
                method: 'POST',
                body: new FormData(this)
            });
            const res = await resp.json();
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Actualizado', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { console.error(e); }
    });

    // Cambiar estado activo/inactivo mediante switch
    async function cambiarEstado(id, estado) {
        const fd = new FormData();
        fd.append('id', id); 
        fd.append('estado', estado);
        try {
            const resp = await fetch('/myvet/app/controllers/controlAlmacenesController.php?action=cambiarEstado', { method: 'POST', body: fd });
            const res = await resp.json();
            if(!res.success) {
                Swal.fire('Error', res.message, 'error');
                location.reload();
            }
        } catch (e) { console.error(e); }
    }

    // Mayúsculas en tiempo real para text e inputs
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
    </script>
</body>
</html>