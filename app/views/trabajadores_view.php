<?php
/**
 * trabajadores_view.php
 * Vista de administración de personal: Filtros, CRUD por Modales y AJAX.
 */
$rolesEnum = ['administrador', 'vendedor', 'chofer', 'almacenista', 'cargador'];
$estadosEnum = ['activo', 'inactivo', 'vacaciones', 'en_ruta'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal | Sistema</title>
    
    <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php'; ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        :root {
            --sidebar-width: 0px;
            --navbar-height: 65px;
        }

        

        .main-content {
            
            padding: 40px;
            padding-top: calc(var(--navbar-height) + 20px);
        }

        .card-table {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        /* Estilo Micro-Widget iOS */
        .ios-micro-card {
            border-radius: 12px !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            padding: 4px 10px !important;
            min-width: 85px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            border-left: 3px solid #34c759 !important;
        }

        .ios-m-label {
            color: #8e8e93;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1.1;
            margin: 0;
        }

        .ios-m-value {
            color: #1c1c1e;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1;
            margin-top: 1px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
                padding-top: 90px;
            }
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3 w-100">
            <div class="flex-grow-1" style="min-width: 200px;">
                <h2 class="fw-bold m-0" style="letter-spacing: -0.02em;">Gestión de Personal</h2>
                <p class="text-body-secondary mb-0" style="font-size: 0.85rem;">Control de trabajadores, roles y disponibilidad</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="ios-micro-card">
                    <p class="ios-m-label">Staff Total</p>
                    <div class="ios-m-value" id="conteoTrabajadores">
                        <?= count($trabajadores) ?>
                    </div>
                </div>

                <button class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center" onclick="nuevoTrabajador()" style="height: 34px; font-weight: 600; font-size: 0.85rem;">
                    <i class="bi bi-person-plus-fill me-1"></i> Agregar
                </button>
            </div>
        </div>

        <!-- Tabla & Filtros -->
        <div class="card card-table p-4">
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaTrabajador" class="form-control border-start-0" placeholder="Buscar por nombre o teléfono...">
                    </div>
                </div>
               <div class="col-md-3">
    <select id="filtroAlmacen" class="form-select">
        <?php if ($_SESSION['almacen_id']==0): ?>
                                                <option value="">Seleccionar ubicación...</option>
                                                <?php endif; ?>
        <?php foreach($listaAlmacenes as $almacen): ?>
            <option value="<?= htmlspecialchars($almacen['nombre']) ?>"><?= htmlspecialchars($almacen['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="col-md-3">
                    <select id="filtroRol" class="form-select">
                        <option value="">Todos los Roles</option>
                        <?php foreach($rolesEnum as $rol): ?>
                            <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaTrabajadores" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Rol / Puesto</th>
                            <th>Almacén</th>
                            <th class="text-center">Documentos</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trabajadores as $t): ?>
                        <tr class="fila-trabajador" data-rol="<?= $t['rol'] ?>">
                            <td><strong><?= htmlspecialchars($t['nombre']) ?></strong></td>
                            <td>
                                <a href="https://wa.me/52<?= $t['telefono'] ?>" target="_blank" class="text-decoration-none small">
                                    <i class="bi bi-whatsapp text-success me-1"></i>
                                    <?= htmlspecialchars($t['telefono']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal text-uppercase" style="font-size: 0.7rem;">
                                    <?= $t['rol'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="small text-body-secondary  text-uppercase">
                                   <?= ($t['nombreAlmacen']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <?php if (!empty($t['documentos_url'])): 
                                        $documentos = explode(';;;', $t['documentos_url']);
                                    ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border position-relative" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-folder2-open"></i>
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                    <?= count($documentos) ?>
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:320px;">
                                                <li>
                                                    <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                                        <span><i class="bi bi-files me-1"></i> Documentos</span>
                                                        <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="subirDocumentoCompra(<?= $t['id'] ?>)">
                                                            Cargar <i class="bi bi-upload"></i>
                                                        </button>
                                                    </h6>
                                                </li>
                                                <?php foreach ($documentos as $doc): 
                                                    $partes = explode('|||', $doc);
                                                    $nombre = $partes[0] ?? '';
                                                    $direccion = $partes[1] ?? '';
                                                    $idDoc = $partes[2] ?? 0;
                                                    if (empty($direccion)) continue;
                                                ?>
                                                    <li>
                                                        <div class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                                            <a href="../../<?= htmlspecialchars($direccion) ?>" target="_blank" class="text-decoration-none flex-grow-1 text-truncate me-2">
                                                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                                                <span class="small"><?= htmlspecialchars($nombre) ?></span>
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-danger" title="Eliminar documento" onclick="eliminarDocumento(<?= $idDoc ?>)">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="subirDocumentoCompra(<?= $t['id'] ?>)">
                                            Agregar <i class="bi bi-upload"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $claseEstado = match($t['estado']) {
                                        'activo' => 'bg-success',
                                        'vacaciones' => 'bg-warning text-dark',
                                        default => 'bg-danger'
                                    };
                                ?>
                                <span class="badge rounded-pill <?= $claseEstado ?>" style="font-size: 0.7rem;">
                                    <?= strtoupper($t['estado']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editarTrabajador(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarTrabajador(<?= $t['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Formulario Trabajador -->
    <div class="modal fade" id="modalTrabajador" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <form id="formTrabajador">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalTitulo">Nuevo Trabajador</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="trabajador_id" value="0">
                        <input type="hidden" name="action" value="guardar">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Nombre Completo</label>
                                <input type="text" name="nombre" id="t_nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Teléfono</label>
                                <input type="text" name="telefono" id="t_telefono" class="form-control" maxlength="10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Puesto / Rol</label>
                                <select name="rol" id="t_rol" class="form-select" required>
                                    <?php foreach($rolesEnum as $rol): ?>
                                        <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Almacén / Sucursal</label>
                                <?php if ($_SESSION['almacen_id'] == 0): ?>
                                    <select name="almacen_id" id="t_almacen_id" class="form-select" required>
                                        <option value="">Seleccionar Almacén...</option>
                                        <?php foreach($listaAlmacenes as $alm): ?>
                                            <option value="<?= $alm['nombre'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control" value="Asignación Automática" readonly>
                                    <input type="hidden" name="almacen_id" id="t_almacen_id" value="<?= $_SESSION['almacen_id'] ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Estado Laboral</label>
                                <select name="estado" id="t_estado" class="form-select">
                                    <?php foreach($estadosEnum as $est): ?>
                                        <option value="<?= $est ?>"><?= ucfirst($est) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Salario</label>
                                <input type="number" step="0.01" name="salario" id="t_salario" class="form-control" required>
                            </div> 
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Fecha de Ingreso</label>
                                <input type="date" name="fecha_ingreso" id="t_fecha_ingreso" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Complemento</label>
                                <input type="number" step="0.01" name="complemento" id="t_complemento" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
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
    let tabla;

    $(document).ready(function() {
        // Mayúsculas automáticas en inputs
        $(document).on('input', 'input[type="text"], textarea', function() {
            this.value = this.value.toUpperCase();
        });

        // DataTable Initialization
        tabla = $('#tablaTrabajadores').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "dom": 'rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 10,
            "order": [[0, 'asc']]
        });

        $('#busquedaTrabajador').on('keyup', function() {
            tabla.search(this.value).draw();
        });

        $('#filtroRol').on('change', function() {
    const val = $(this).val();
    
    // ^\s* y \s*$ toleran los espacios y saltos de línea del <td>
    // Parámetros: search(pattern, regex, smart, caseInsen)
    tabla.column(2)
         .search(val ? `^\\s*${val}\\s*$` : '', true, false, true)
         .draw();
}); 
$('#filtroAlmacen').on('change', function() {
    const val = $(this).val();

    if (!val) {
        tabla.column(3).search('').draw();
    } else {
        const valEscapado = $.fn.dataTable.util.escapeRegex(val);
        // Busca el nombre del almacén tolerando el texto de la celda
        tabla.column(3).search(valEscapado, true, false, true).draw();
    }
});
    });

    function nuevoTrabajador() {
        $('#formTrabajador')[0].reset();
        $('#trabajador_id').val('0');
        if ($('#t_almacen_id').is('select')) $('#t_almacen_id').val('');
        $('#modalTitulo').text('Nuevo Trabajador');
        $('#modalTrabajador').modal('show');
    }

    function editarTrabajador(t) {
        $('#modalTitulo').text('Editar Trabajador');
        $('#trabajador_id').val(t.id);
        $('#t_nombre').val(t.nombre);
        $('#t_telefono').val(t.telefono);
        $('#t_rol').val(t.rol);
        $('#t_estado').val(t.estado);
        $('#t_salario').val(t.salario);
        $('#t_complemento').val(t.complemento_pago);
        $('#t_fecha_ingreso').val(t.fecha_ingreso);
        
        if ($('#t_almacen_id').is('select')) {
            $('#t_almacen_id').val(t.almacen_id);
        }
        $('#modalTrabajador').modal('show');
    }

    $('#formTrabajador').on('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const resp = await fetch('/myvet/app/controllers/trabajadoresController.php', {
                method: 'POST',
                body: formData
            });
            const res = await resp.json();
            
            if (res.status === 'success') {
                await Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    showConfirmButton: false,
                    timer: 1000
                });
                location.reload();
            } else {
                Swal.fire('Error', res.message || 'No se pudo procesar la solicitud', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Error de conexión con el servidor', 'error');
        }
    });

    async function eliminarTrabajador(id) {
        const result = await Swal.fire({
            title: '¿Eliminar trabajador?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        });

        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'eliminar');
            fd.append('id', id);

            try {
                const resp = await fetch('/myvet/app/controllers/trabajadoresController.php', {
                    method: 'POST',
                    body: fd
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    location.reload();
                } else {
                    Swal.fire('Error', res.message || 'No se pudo eliminar', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Error en el servidor', 'error');
            }
        }
    }

    function limpiarFiltros() {
        $('#busquedaTrabajador').val('');
        $('#filtroRol').val('');
        tabla.search('').column(2).search('').draw();
    }

    async function subirDocumentoCompra(trabajador_id) {
        const result = await Swal.fire({
            title: 'Documento del Trabajador',
            html: `
                <div class="text-start">
                    <label class="fw-bold small mb-2">Subir archivo (PDF o Imagen)</label>
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
                    Swal.showValidationMessage('Selecciona un archivo antes de continuar');
                    return false;
                }

                const formData = new FormData();
                formData.append('trabajador_id', trabajador_id);
                formData.append('documento', file);

                try {
                    const response = await fetch('/myvet/app/controllers/trabajadoresController.php?action=subirDocumento', {
                        method: 'POST',
                        body: formData
                    });

                    const text = await response.text();
                    let res;
                    try {
                        res = JSON.parse(text);
                    } catch {
                        throw new Error('Respuesta inválida del servidor');
                    }

                    if (!response.ok || !res.success) {
                        throw new Error(res.message || 'Error al subir archivo');
                    }

                    return res;
                } catch (err) {
                    Swal.showValidationMessage(err.message);
                    return false;
                }
            }
        });

        if (result.isConfirmed && result.value) {
            await Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Documento subido correctamente',
                timer: 1500,
                showConfirmButton: false
            });
            location.reload();
        }
    }

    async function eliminarDocumento(id) {
        const result = await Swal.fire({
            title: '¿Eliminar Documento?',
            text: "El archivo será removido permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            confirmButtonColor: '#ed0909',
            focusConfirm: false,
            preConfirm: async () => {
                const formData = new FormData();
                formData.append('id', id);

                try {
                    const response = await fetch('/myvet/app/controllers/trabajadoresController.php?action=eliminarDocumento', {
                        method: 'POST',
                        body: formData
                    });

                    const text = await response.text();
                    let res;
                    try {
                        res = JSON.parse(text);
                    } catch {
                        throw new Error('Respuesta inválida del servidor');
                    }

                    if (!res.success) {
                        throw new Error(res.message || 'Error al eliminar');
                    }

                    return res;
                } catch (err) {
                    Swal.showValidationMessage(err.message);
                    return false;
                }
            }
        });

        if (result.isConfirmed && result.value) {
            await Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: 'Documento eliminado correctamente',
                timer: 1500,
                showConfirmButton: false
            });
            location.reload();
        }
    }
    </script>
</body>

</html>