<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Sistema</title>

    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos'))
        cargarEstilos(); ?>

    <style>
        :root {
            --sidebar-width: 0px;
            --navbar-height: 20px;
        }

        .main-content {
            padding: 40px;
            margin-top: var(--navbar-height);
            transition: all 0.3s;
        }

        .card-table {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: var(--bs-body-bg);
        }

        .badge-role {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
        }

        .role-1 {
            background: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .role-2 {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .role-3 {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        [data-bs-theme="dark"] .role-1 {
            background: rgba(99, 102, 241, .2);
            color: #a5b4fc;
            border-color: rgba(99, 102, 241, .3);
        }

        [data-bs-theme="dark"] .role-2 {
            background: rgba(34, 197, 94, .2);
            color: #86efac;
            border-color: rgba(34, 197, 94, .3);
        }

        [data-bs-theme="dark"] .role-3 {
            background: rgba(245, 158, 11, .2);
            color: #fcd34d;
            border-color: rgba(245, 158, 11, .3);
        }

        .avatar-circle {
            font-weight: bold;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border-radius: 0.5rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 2.2;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0">Personal del Sistema</h2>
                <p class="text-body-secondary">Gestión de cuentas, roles y sucursales asignadas</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="nuevoUsuario()">
                <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
            </button>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4">

                <div class="col-md-4">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-search me-1 text-primary"></i> Buscar Usuario
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bi bi-search text-body-secondary"></i>
                        </span>
                        <input type="text" id="busquedaReal" class="form-control border-start-0"
                            placeholder="Buscar usuario o nombre...">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-box-seam me-1 text-primary"></i> Almacén
                    </label>
                    <select name="almacen_id" id="almacen_id" class="form-select shadow-sm rounded-3 py-2">
                        <?php if ($tipo == 1): ?>
                            <option value="">Todos</option>
                        <?php endif; ?>
                        <?php foreach ($almacenes as $a): ?>
                            <option value="<?= $a['id'] ?>">
                                [<?= $a['id'] ?>] <?= htmlspecialchars($a['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-person-badge me-1 text-primary"></i> Rol
                    </label>
                    <select id="rol" class="form-select shadow-sm rounded-3 py-2">
                        <option value="">Todos</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Gestor">Gestor</option>
                        <option value="Vendedor">Vendedor</option>
                        <option value="Trabajador">Trabajador</option>
                    </select>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre del Colaborador</th>
                            <th>Correo Electrónico</th>
                            <th>Rol</th>
                            <th>Ubicación</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaUsuarios">
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Cargando usuarios...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ============================================================= -->
    <!-- MODAL USUARIO                                                 -->
    <!-- ============================================================= -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formUsuario" class="modal-content shadow-lg">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="userId">
                    <input type="hidden" name="accion" value="guardar">

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label small fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre" id="userName" class="form-control text-uppercase"
                                placeholder="Ej. Juan Pérez" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Correo Electrónico</label>
                            <input type="email" name="username" id="userLogin" class="form-control"
                                placeholder="juan.perez@correo.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono</label>
                            <input type="text" name="telefono" id="userTelefono" class="form-control"
                                placeholder="+52 123 456 7890" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contraseña</label>
                            <input type="password" name="password" id="userPass" class="form-control"
                                placeholder="••••••••">
                            <small class="text-body-secondary d-block mt-1" id="passNote" style="display:none;">
                                Vacío para no cambiar
                            </small>
                        </div>

                        <?php $soloLectura = ($_SESSION['almacen_id'] ?? 0) != 0; ?>

                        <!-- Rol -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Rol de Acceso</label>

                            <?php if ($soloLectura): ?>
                                <?php
                                $rolActualNombre = '';
                                foreach ($rolesArray as $r) {
                                    if (($usuarioEdit['rol_id'] ?? 0) == $r['id']) {
                                        $rolActualNombre = $r['nombre'];
                                        break;
                                    }
                                }
                                ?>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($rolActualNombre ?: '—') ?>" readonly disabled>
                                <input type="hidden" name="rol_id" id="userRolHidden"
                                    value="<?= $usuarioEdit['rol_id'] ?? '' ?>">
                            <?php else: ?>
                                <select name="rol_id" id="userRol" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($rolesArray as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= (($usuarioEdit['rol_id'] ?? 0) == $r['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($r['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- Almacén (CORREGIDO: antes estaba suelto, sin label ni col) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Almacén</label>
                            <select name="almacen_id" id="userAlmacen" class="form-select">
                                <?= ($_SESSION['almacen_id'] ?? null) === 0
                                    ? '<option value="">Acceso Global</option>'
                                    : '' ?>
                                <?php foreach ($almacenesArray as $a): ?>
                                    <option value="<?= $a['id'] ?>">
                                        <?= htmlspecialchars($a['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        /* ============================================================
           VARIABLES GLOBALES
           ============================================================ */
        const ROLES_VALIDOS = ['Administrador', 'Gestor', 'Vendedor', 'Trabajador'];
        let USUARIOS_CACHE = [];
        let modalEl;

        /* ============================================================
           UTILIDADES
           ============================================================ */
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        /* ============================================================
           CARGAR USUARIOS
           ============================================================ */
        async function cargarUsuarios() {
            const $tbody = $('#listaUsuarios');
            const almacenId = $('#almacen_id').val() || '';

            $tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">Cargando usuarios...</td></tr>');

            try {
                const url = `/myvet/app/controllers/usuariosController.php?action=obtenerUsuarios&almacen_id=${encodeURIComponent(almacenId)}`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);

                const resultado = await respuesta.json();

                if (!resultado.success || !Array.isArray(resultado.data)) {
                    throw new Error(resultado.message || 'Respuesta inválida');
                }

                USUARIOS_CACHE = resultado.data;

                if (resultado.data.length === 0) {
                    $tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados</td></tr>');
                    return;
                }

                $tbody.html(resultado.data.map(u => {
                    const inicial = (u.nombre || '?').charAt(0).toUpperCase();
                    const id = Number(u.id);

                    return `
                        <tr class="user-row" data-rol="${escapeHtml(u.rol_nombre).toLowerCase()}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle"
                                        style="width:38px;height:38px;">
                                        ${inicial}
                                    </div>
                                    <span class="fw-bold">${escapeHtml(u.nombre)}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge text-body-secondary border">${escapeHtml(u.username)}</span>
                            </td>
                            <td>
                                <span class="badge-role role-${u.rol_id}">
                                    ${escapeHtml(u.rol_nombre)}
                                </span>
                            </td>
                            <td>
                                <small class="text-body-secondary">
                                    <i class="bi bi-geo-alt me-1"></i>${escapeHtml(u.almacen_nombre)}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        ${u.activo == 1 ? 'checked' : ''}
                                        onchange="toggleUsuario(${id}, this)">
                                </div>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light border"
                                    onclick="editarUsuarioPorId(${id})">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join(''));

                aplicarFiltroRol();

            } catch (error) {
                console.error('[cargarUsuarios]', error);
                $tbody.html('<tr><td colspan="6" class="text-center py-4 text-danger">Error al cargar los usuarios</td></tr>');
            }
        }

        /* ============================================================
           FILTRO LOCAL POR ROL
           ============================================================ */
        function aplicarFiltroRol() {
            const rol = ($('#rol').val() || '').toLowerCase();
            $('#listaUsuarios tr.user-row').each(function () {
                const coincide = rol === '' || $(this).data('rol') === rol;
                $(this).toggle(coincide);
            });
        }

        /* ============================================================
           MODAL
           ============================================================ */
        function nuevoUsuario() {
            const form = document.getElementById('formUsuario');
            if (form) form.reset();

            $('#userId').val(0);
            $('#modalTitle').text('Registrar Nuevo Colaborador');
            $('#userPass').prop('required', true).val('');
            $('#passNote').hide();

            // Select2: resetear si existen
            if ($('#userRol').length) {
                $('#userRol').val('').trigger('change');
            }
            if ($('#userRolHidden').length) {
                $('#userRolHidden').val('');
            }
            if ($('#userAlmacen').length) {
                $('#userAlmacen').val('').trigger('change');
            }

            modalEl.show();
        }

        function editarUsuarioPorId(id) {
            const u = USUARIOS_CACHE.find(x => Number(x.id) === Number(id));
            if (!u) return;
            editarUsuario(u);
        }

        function editarUsuario(u) {
            $('#userId').val(u.id);
            $('#userName').val(u.nombre);
            $('#userLogin').val(u.username);
            $('#userTelefono').val(u.telefono);
            $('#userPass').prop('required', false).val('');
            $('#modalTitle').text('Actualizar Usuario');
            $('#passNote').show();

            // Rol: puede ser <select> o <input hidden> según rol del usuario
            if ($('#userRol').length) {
                $('#userRol').val(u.rol_id).trigger('change');
            } else if ($('#userRolHidden').length) {
                $('#userRolHidden').val(u.rol_id);
            }

            // Almacén
            if ($('#userAlmacen').length) {
                $('#userAlmacen').val(u.almacen_id).trigger('change');
            }

            modalEl.show();
        }

        /* ============================================================
           SUBMIT
           ============================================================ */
        $('#formUsuario').on('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const $btn = $(this).find('button[type="submit"]');
            const htmlOriginal = $btn.html();

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

            try {
                const resp = await fetch('/myvet/app/controllers/usuariosController.php', {
                    method: 'POST',
                    body: formData
                });
                const res = await resp.json();

                if (res.status === 'success') {
                    modalEl.hide();
                    await Swal.fire({
                        icon: 'success',
                        title: res.message,
                        timer: 1400,
                        showConfirmButton: false
                    });
                    await cargarUsuarios();
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
                }
            } catch (err) {
                console.error('[formUsuario]', err);
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            } finally {
                $btn.prop('disabled', false).html(htmlOriginal);
            }
        });

        /* ============================================================
           TOGGLE ACTIVAR / DESACTIVAR
           ============================================================ */
        async function toggleUsuario(id, checkbox) {
            const estabaActivo = !checkbox.checked; // si ahora está unchecked, antes estaba activo
            const accion = estabaActivo ? 'eliminar' : 'activar';

            try {
                const formData = new FormData();
                formData.append('accion', accion);
                formData.append('id', id);

                const resp = await fetch('/myvet/app/controllers/usuariosController.php', {
                    method: 'POST',
                    body: formData
                });
                const res = await resp.json();

                if (res.status !== 'success') {
                    checkbox.checked = !checkbox.checked; // revertir
                    Swal.fire('Error', res.message || 'No se pudo actualizar', 'error');
                }
            } catch (err) {
                console.error('[toggleUsuario]', err);
                checkbox.checked = !checkbox.checked;
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }

        // Compatibilidad: el nombre original era eliminarUsuario
        function eliminarUsuario(id) {
            const input = $(`tr:has(button[onclick*="toggleUsuario(${id}"]) .form-check-input`)[0];
            if (input) toggleUsuario(id, input);
        }

        /* ============================================================
           INICIALIZACIÓN
           ============================================================ */
        $(document).ready(function () {

            modalEl = new bootstrap.Modal('#modalUsuario');

            /* --- Select2: Filtro Almacén --- */
            $('#almacen_id').select2({
                theme: 'bootstrap-5',
                placeholder: '<?= $tipo == 1 ? "Todos" : "Selecciona un almacén" ?>',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: () => 'No se encontraron resultados',
                    searching: () => 'Buscando...'
                }
            }).on('change', cargarUsuarios);

            /* --- Select2: Filtro Rol --- */
            $('#rol').select2({
                theme: 'bootstrap-5',
                placeholder: 'Todos',
                allowClear: true,
                width: '100%'
            }).on('change', aplicarFiltroRol);

            /* --- Select2: Selects del modal --- */
            if ($('#userRol').length) {
                $('#userRol').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalUsuario'),
                    width: '100%',
                    placeholder: 'Seleccione...'
                });
            }

            if ($('#userAlmacen').length) {
                $('#userAlmacen').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalUsuario'),
                    width: '100%',
                    placeholder: '',
                    allowClear: true
                });
            }

            /* --- Buscador instantáneo --- */
            $('#busquedaReal').on('keyup', function () {
                const value = ($(this).val() || '').toLowerCase();
                $('#listaUsuarios tr.user-row').each(function () {
                    const coincide = $(this).text().toLowerCase().indexOf(value) > -1;
                    $(this).toggle(coincide);
                });
            });

            /* --- Carga inicial SIEMPRE --- */
            cargarUsuarios();
        });
    </script>
</body>

</html>