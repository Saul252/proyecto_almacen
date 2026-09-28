<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Accesos | cfsistem</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
   
     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        :root { --sidebar-width: 0px; --navbar-height: 20px; }
       
        .main-content {  padding: 40px; margin-top: var(--navbar-height); transition: all 0.3s; }
        .card-custom {  border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; overflow: hidden; }
        .table thead th { background-color: #f8fafc; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #64748b; padding: 15px 20px; border-bottom: 2px solid #edf2f7; }
        .module-name { font-weight: 700; color: #1e293b; font-size: 0.9rem; }
        .role-header { font-weight: 800; color: #4338ca; position: relative; min-width: 120px; }
        .btn-delete-role { font-size: 0.6rem; padding: 2px 5px; position: absolute; top: -5px; right: 0; opacity: 0; transition: 0.2s; }
        .role-header:hover .btn-delete-role { opacity: 1; }
        .form-check-input:checked { background-color: #4338ca; border-color: #4338ca; }
        .permiso-row:hover { background-color: #f8fafc; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0 text-dark">Matriz de Seguridad</h2>
                <p class="text-body-secondary">Gestión de roles, módulos y permisos del sistema</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="nuevoRol()">
                    <i class="bi bi-person-plus me-2"></i> Nuevo Rol
                </button>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="nuevoModulo()">
                    <i class="bi bi-plus-lg me-2"></i> Nuevo Módulo
                </button>
                <button type="button" id="btnGuardarPermisos" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-shield-check me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body p-0">
                <form id="formPermisos">
                    <input type="hidden" name="accion" value="guardar_permisos">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Módulo / Sección</th>
                                    <?php foreach($roles as $r): ?>
                                        <th class="text-center role-header">
                                            <span onclick="editarRol(<?= $r['id'] ?>, '<?= htmlspecialchars($r['nombre']) ?>')" style="cursor:pointer" title="Click para editar nombre">
                                                <?= htmlspecialchars($r['nombre']) ?>
                                            </span>
                                            <?php if($r['id'] != 1): ?>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-role rounded-circle" onclick="eliminarRol(<?= $r['id'] ?>)">×</button>
                                            <?php endif; ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($modulosData as $m): ?>
                                <tr class="permiso-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded-3 me-3 text-primary">
                                                <i class="<?= !empty($m['icono']) ? $m['icono'] : 'bi bi-box' ?>"></i>
                                            </div>
                                            <span class="module-name"><?= htmlspecialchars($m['nombre']) ?></span>
                                        </div>
                                    </td>
                                    <?php foreach($roles as $r): 
                                        $hasPerm = $this->model->verificarPermiso($r['id'], $m['identificador']) ? 'checked' : '';
                                    ?>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                       name="permisos[<?= $r['id'] ?>][]" 
                                                       value="<?= htmlspecialchars($m['identificador']) ?>" <?= $hasPerm ?>>
                                            </div>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light text-primary" onclick='editarModulo(<?= json_encode($m) ?>)'>
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light text-danger" onclick="eliminarModulo(<?= $m['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalRol" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header  pb-0 px-4 pt-4">
                    <h5 class="fw-bold" id="tituloRol">Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRol">
                    <input type="hidden" name="accion" value="guardar_rol">
                    <input type="hidden" name="id_rol" id="rol_id">
                    <div class="modal-body px-4">
                        <input type="text" class="form-control" name="nombre_rol" id="rol_nombre" placeholder="Ej: Vendedor" required>
                    </div>
                    <div class="modal-footer  px-4 pb-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalModulo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; ">
                <div class="modal-header  pt-4 px-4">
                    <h5 class="fw-bold" id="modalTitulo">Nuevo Módulo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formModulo">
                    <input type="hidden" name="accion" value="guardar_modulo">
                    <input type="hidden" name="id" id="mod_id">
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-body-secondary">Nombre Visible</label>
                            <input type="text" class="form-control" name="nombre" id="mod_nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-body-secondary">Identificador (ID Sistema)</label>
                            <input type="text" class="form-control" name="identificador" id="mod_ident" required>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-body-secondary">Icono (Bootstrap Icon)</label>
                                <input type="text" class="form-control" name="icono" id="mod_icono" placeholder="bi bi-box">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-body-secondary">Orden</label>
                                <input type="number" class="form-control" name="orden" id="mod_orden" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer  pb-4 px-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Módulo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    const AJAX_URL = '/myvet/app/controllers/configuracionController.php';

    // --- MANEJO DE ROLES ---
    function nuevoRol() {
        $('#formRol')[0].reset(); $('#rol_id').val('');
        $('#tituloRol').text('Nuevo Rol'); $('#modalRol').modal('show');
    }

    function editarRol(id, nombre) {
        $('#rol_id').val(id); $('#rol_nombre').val(nombre);
        $('#tituloRol').text('Editar Rol'); $('#modalRol').modal('show');
    }

    function eliminarRol(id) {
        Swal.fire({
            title: '¿Eliminar este rol?',
            text: "Se perderán todos los permisos asociados a este rol.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(AJAX_URL, { accion: 'eliminar_rol', id: id }, function(res) {
                    if(res.status === 'success') location.reload();
                    else Swal.fire('Error', res.message, 'error');
                }, 'json');
            }
        });
    }

    // --- MANEJO DE MÓDULOS ---
    function nuevoModulo() {
        $('#formModulo')[0].reset(); $('#mod_id').val('');
        $('#modalTitulo').text('Nuevo Módulo'); $('#modalModulo').modal('show');
    }

    function editarModulo(data) {
        $('#mod_id').val(data.id);
        $('#mod_nombre').val(data.nombre);
        $('#mod_ident').val(data.identificador);
        $('#mod_icono').val(data.icono);
        $('#mod_orden').val(data.orden);
        $('#modalTitulo').text('Editar Módulo');
        $('#modalModulo').modal('show');
    }

    function eliminarModulo(id) {
        Swal.fire({
            title: '¿Desactivar módulo?',
            text: "El módulo dejará de ser visible en la matriz y menús.",
            icon: 'warning',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(AJAX_URL, { accion: 'eliminar_modulo', id: id }, function(res) {
                    if(res.status === 'success') location.reload();
                    else Swal.fire('Error', res.message, 'error');
                }, 'json');
            }
        });
    }

    // --- ENVÍO DE FORMULARIOS (AJAX) ---
    $('#formRol, #formModulo').on('submit', function(e) {
        e.preventDefault();
        $.post(AJAX_URL, $(this).serialize(), function(res) {
            if(res.status === 'success') location.reload();
            else Swal.fire('Error', res.message, 'error');
        }, 'json');
    });

    // --- GUARDADO DE LA MATRIZ ---
    $('#btnGuardarPermisos').on('click', function() {
        Swal.fire({
            title: 'Guardando cambios',
            text: 'Actualizando matriz de seguridad...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const datos = $('#formPermisos').serialize();

        $.ajax({
            url: AJAX_URL,
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Los permisos se han guardado correctamente.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar la configuración', 'error');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error crítico', 'Error de comunicación con el servidor. Revisa la consola.', 'error');
            }
        });
    });
    </script>
</body>
</html>