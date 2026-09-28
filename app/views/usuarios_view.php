<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

  
    <style>
        :root { --sidebar-width: 0px; --navbar-height: 20px; }
        
        
        .main-content {
            
            padding: 40px;
            margin-top: var(--navbar-height);
            transition: all 0.3s;
        }

        .card-table {  border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .badge-role { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; display: inline-block; }
        
        /* Colores de Roles dinámicos */
        .role-1 { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; } /* Admin */
        .role-2 { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; } /* Almacén */
        .role-3 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; } /* Vendedor */

        .avatar-circle { font-weight: bold; text-transform: uppercase; flex-shrink: 0; }
        
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0 ">Personal del Sistema</h2>
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
                        <i class="bi bi-box-seam me-1 text-primary"></i> Buscar Usuario
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-body-secondary"></i></span>
                        <input type="text" id="busquedaReal" class="form-control border-start-0" placeholder="Buscar usuario o nombre...">
                    </div>
                </div>
                 <div class="col-md-4">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-box-seam me-1 text-primary"></i> Almacén 
                    </label>
                    <select name="almacen_id" id="almacen_id" class="form-select  shadow-sm rounded-3 py-2" required>
                         <?php if ($tipo == 1): ?>
        <option value="">Todos</option>
    <?php endif; ?>
                    <?php foreach($almacenes as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                 <div class="col-md-4">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-box-seam me-1 text-primary"></i> Rol
                    </label>
                    <select name="" id="rol" class="form-select  shadow-sm rounded-3 py-2" required>
                        
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
                <th>Usuario</th>
                <th>Rol</th>
                <th>Ubicación</th>
                <th class="text-center">Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody id="listaUsuarios">
            <!-- Se llena dinámicamente con cargarUsuarios() -->
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">Cargando usuarios...</td>
            </tr>
        </tbody>
    </table>
</div>

           
        </div>
    </main>

    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formUsuario" class="modal-content  shadow-lg">
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
                            <input type="text" name="nombre" id="userName" class="form-control text-uppercase " placeholder="Ej. Juan Pérez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre de Usuario</label>
                            <input type="text" name="username" id="userLogin" class="form-control " placeholder="juan.perez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contraseña</label>
                            <input type="password" name="password" id="userPass" class="form-control " placeholder="••••••••">
                            <small class="text-body-secondary d-block mt-1" id="passNote" style="display:none;">Vacío para no cambiar</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Rol de Acceso</label>
                            <select name="rol_id" id="userRol" class="form-select " required>
                                <option value="">Seleccione...</option>
                                <?php foreach($rolesArray as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Almacén Asignado</label>
                            <select name="almacen_id" id="userAlmacen" class="form-select ">
                                <option value="">Acceso Global</option>
                                <?php foreach($almacenesArray as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer  p-3">
                    <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
     $(document).ready(async function() {
        try {

            await cargarUsuarios();
        } catch (error) {
            console.error("Error en inicialización:", error);
        }
    });
    async function cargarUsuarios() {
    const tbody = document.getElementById('listaUsuarios');
    const selectAlmacen = document.getElementById('almacen_id');
    
    if (!tbody) return;
    tbody.innerHTML='';

    // Obtener almacen_id si existe el select, si no tomarlo directo de una variable global o dejarlo vacío
    const almacenId = selectAlmacen ? selectAlmacen.value : '';

    try {
        const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacenId}`;
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();
        console.log(resultado);

        if (resultado.success && Array.isArray(resultado.data) && resultado.data.length > 0) {
            tbody.innerHTML = resultado.data.map(u => {
                // Escapar comillas dobles para evitar romper el JSON en el evento onclick
                const usuarioJson = JSON.stringify(u).replace(/"/g, '&quot;');
                const inicial = (u.nombre || '').charAt(0).toUpperCase();

                return `
                    <tr class="user-row">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px;">
                                    ${inicial}
                                </div>
                                <span class="fw-bold">${escapeHtml(u.nombre)}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge text-body-secondary border">@${escapeHtml(u.username)}</span>
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
                                <input class="form-check-input" type="checkbox" role="switch" ${u.activo == 1 ? 'checked' : ''} onchange="eliminarUsuario(${u.id})">
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border" onclick="editarUsuario(${usuarioJson})">
                                <i class="bi bi-pencil-square text-primary"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No hay usuarios registrados</td></tr>';
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error al cargar los usuarios</td></tr>';
        console.error('Error al ejecutar cargarUsuarios:', error);
    }
}

// Función auxiliar para sanitizar cadenas en JS
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
        const modalEl = new bootstrap.Modal('#modalUsuario');

        // Buscador Instantáneo
        $("#busquedaReal").on("keyup", function() {
            let value = $(this).val().toLowerCase();
            $("#listaUsuarios tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
$("#rol").on("change", function() {
    let rolSeleccionado = $(this).val().toLowerCase();
    
    $("#listaUsuarios tr").filter(function() {
        // Si no selecciona nada (opción "Todos"), muestra la fila; si selecciona un rol, busca coincidencia en el texto de la fila
        let coincide = (rolSeleccionado === "") || $(this).text().toLowerCase().indexOf(rolSeleccionado) > -1;
        $(this).toggle(coincide);
    });
});
        function nuevoUsuario() {
            $('#formUsuario')[0].reset();
            $('#userId').val(0);
            $('#modalTitle').text('Registrar Nuevo Colaborador');
            $('#userPass').prop('required', true);
            $('#passNote').hide();
            modalEl.show();
        }

        function editarUsuario(u) {
            $('#userId').val(u.id);
            $('#userName').val(u.nombre);
            $('#userLogin').val(u.username);
            $('#userRol').val(u.rol_id);
            $('#userAlmacen').val(u.almacen_id);
            $('#userPass').prop('required', false).val('');
            $('#modalTitle').text('Actualizar Usuario');
            $('#passNote').show();
            modalEl.show();
        }

        // Envío de Formulario
        $('#formUsuario').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/myvet/app/backend/usuarios/crud_usuarios.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    Swal.fire({ icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        });

        // Activar/Desactivar
        function eliminarUsuario(id) {
            const formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('id', id);

            fetch('/myvet/app/backend/usuarios/crud_usuarios.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.status !== 'success') {
                    Swal.fire('Error', res.message, 'error').then(() => location.reload());
                }
            });
        }
    </script>
</body>
</html>