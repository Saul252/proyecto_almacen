<?php
/**
 * trabajadores_view.php
 * Vista de administración de personal: Filtros, CRUD por Modales y AJAX.
 */
$rolesEnum = ['administrador', 'vendedor', 'chofer', 'almacenista', 'cargador'];
$estadosEnum = ['activo', 'inactivo', 'vacaciones', 'en_ruta'];
$paginaActual = 'trabajadores';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal | Sistema</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php'; ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

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
            background: #ffffff !important;
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
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px; width: 100%;">
            <div>
                <h2 class="fw-bold m-0 text-uppercase" style="letter-spacing: -0.02em; ">Gestión de Salarios</h2>
            </div>

            <div style="min-width: 200px;">
                <label for="semana" class="form-label text-body-secondary small m-0 fw-semibold">Semana</label>
                <input type="week" id="semana" class="form-control form-control-sm">
            </div>

            <div class="d-flex align-items-center" style="gap: 12px;">
                <div class="ios-micro-card">
                    <p class="ios-m-label">Staff Total</p>
                    <div class="ios-m-value" id="conteoTrabajadores">0</div>
                </div>

                <button class="btn btn-primary rounded-pill px-3 shadow-sm" onclick="nuevoTrabajador()"
                    style="height: 34px; font-weight: 600; font-size: 0.85rem;">
                    <i class="bi bi-person-plus-fill me-1"></i> Agregar
                </button>
                <button class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" onclick="imprimirContenidoModal()"
                    style="height: 34px; font-weight: 600; font-size: 0.85rem;">
                    <i class="bi bi-printer-fill me-1"></i> Imprimir Nómina
                </button>
            </div>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4 g-3">

                <div class="col-md-6">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-box-seam me-1 text-primary"></i> Buscar
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaTrabajador" class="form-control border-start-0"
                            placeholder="Buscar por nombre o teléfono...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                    </label>
                    <select name="almacen_id_editar" id="almacen_id_editar"
                        class="form-select  shadow-sm rounded-3 py-2" onchange="cargarTrabajadores()" required>
                        <?php if ($_SESSION['almacen_id'] == 0): ?>

                        <?php endif; ?>
                        <?php foreach ($almacenes as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-body-secondary fw-semibold small mb-1">
                        <i class="bi bi-box-seam me-1 text-primary"></i>Filtrar por rol
                    </label>
                    <select id="filtroRol" class="form-select">
                        <option value="">Todos los Roles</option>
                        <?php foreach ($rolesEnum as $rol): ?>
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
                            <th>Salario</th>
                            <th>Bonos</th>
                            <th>Préstamos Activos</th>
                            <th>Total Nómina</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTrabajadores">
                        <tr>
                            <td colspan="9" class="text-center py-4 text-body-secondary">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Formulario Trabajador -->
    <div class="modal fade" id="modalTrabajador" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content  shadow-lg">
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
                                <input type="text" name="telefono" id="t_telefono" class="form-control" maxlength="10"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Puesto / Rol</label>
                                <select name="rol" id="t_rol" class="form-select" required>
                                    <?php foreach ($rolesEnum as $rol): ?>
                                        <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Almacén / Sucursal</label>
                                <?php if (isset($_SESSION['almacen_id']) && $_SESSION['almacen_id'] == 0): ?>
                                    <select name="almacen_id" id="t_almacen_id" class="form-select" required>
                                        <option value="">Seleccionar Almacén...</option>
                                        <?php foreach (($listaAlmacenes ?? []) as $alm): ?>
                                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control bg-light" value="Asignación Automática" readonly>
                                    <input type="hidden" name="almacen_id" id="t_almacen_id"
                                        value="<?= $_SESSION['almacen_id'] ?? 1 ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Estado Laboral</label>
                                <select name="estado" id="t_estado" class="form-select">
                                    <?php foreach ($estadosEnum as $est): ?>
                                        <option value="<?= $est ?>"><?= ucfirst($est) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Salario</label>
                                <input type="number" step="0.01" name="salario" id="t_salario" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-12 center">
                                <label class="form-label fw-bold small">fecha_ingreso</label>
                                <input type="date" name="fecha_ingreso" id="t_fecha_ingreso" class="form-control"
                                    maxlength="10" required>
                            </div>
                            <div class="col-md-12 center">
                                <label class="form-label fw-bold small">Complemento</label>
                                <input type="money" name="complemento" id="t_complemento" class="form-control"
                                    maxlength="10" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer  ">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalBono" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content  shadow rounded-4">

                <!-- HEADER -->
                <div class="modal-header bg-success bg-gradient text-white  py-4">
                    <div>
                        <h4 class="modal-title fw-bold mb-1">
                            <i class="bi bi-award-fill me-2"></i>
                            Registrar Bono
                        </h4>
                        <small class="opacity-75">
                            Asigna una bonificación al trabajador.
                        </small>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                    </button>
                </div>

                <form id="formBono">

                    <div class="modal-body p-4">

                        <div class="row g-4">

                            <!-- Trabajador -->
                            <div class="col-md-6">

                                <div class="card   h-100">
                                    <div class="card-body">

                                        <label class="form-label fw-semibold text-secondary">
                                            <i class="bi bi-person-fill me-1 text-success"></i>
                                            Trabajador
                                        </label>

                                        <input type="hidden" id="modal_bono_trabajador_id"
                                            name="trabajador_id_crear_bono">

                                        <input type="text" id="trabajador_nombre" name="trabajador_nombre"
                                            class="form-control form-control-lg" readonly>

                                    </div>
                                </div>

                            </div>

                            <!-- Monto -->
                            <div class="col-md-6">

                                <div class="card   h-100">
                                    <div class="card-body">

                                        <label class="form-label fw-semibold text-secondary">
                                            <i class="bi bi-cash-stack me-1 text-success"></i>
                                            Monto del Bono
                                        </label>

                                        <div class="input-group input-group-lg">

                                            <span class="input-group-text fw-bold">
                                                $
                                            </span>

                                            <input type="number" step="0.01" min="0" id="monto_bono" name="monto_bono"
                                                class="form-control" placeholder="0.00" required>

                                        </div>

                                    </div>
                                </div>

                            </div>

                            <!-- Fecha -->
                            <div class="col-md-6">

                                <div class="card  ">
                                    <div class="card-body">

                                        <label class="form-label fw-semibold text-secondary">
                                            <i class="bi bi-calendar-event me-1 text-success"></i>
                                            Fecha
                                        </label>

                                        <input type="date" id="fecha_bono" name="fecha"
                                            class="form-control form-control-lg" value="<?= date('Y-m-d') ?>">

                                    </div>
                                </div>

                            </div>

                            <!-- Vista previa -->


                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer   py-3 px-4">

                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">

                            <i class="bi bi-x-circle me-1"></i>
                            Cancelar

                        </button>

                        <button onclick="guardarBono()" class="btn btn-success rounded-pill px-4 shadow">

                            <i class="bi bi-check-circle-fill me-1"></i>
                            Registrar Bono

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
        let tabla = null;

        $(document).ready(function () {
            // Inicializar selector de semana con la semana ISO actual
            $('#semana').val(obtenerSemanaActual());

            // Cargar trabajadores por primera vez
            cargarTrabajadores();

            // Escuchar cambios en la fecha de la semana
            $('#semana').on('change', cargarTrabajadores);

            // Búsqueda y Filtro rápido
            $('#busquedaTrabajador').on('keyup', function () {
                if (tabla) tabla.search(this.value).draw();
            });

            $('#filtroRol').on('change', function () {
                const val = $(this).val();
                if (tabla) tabla.column(2).search(val ? `^${val}$` : '', true, false).draw();
            });

            // Convertir automáticamente textos a MAYÚSCULAS
            $(document).on('input', 'input[type="text"], textarea', function () {
                this.value = this.value.toUpperCase();
            });
        });
        $('#formBono').on('submit', async function (e) {
            e.preventDefault();

            const trabajador_id = $('#modal_bono_trabajador_id').val();
            const monto = document.getElementById('monto_bono').value;
            const fecha = document.getElementById('fecha_bono').value;


            if (!trabajador_id) {
                throw new Error("Selecciona un trabajador.");
            }

            if (!monto || parseFloat(monto) <= 0) {
                throw new Error("Ingresa un monto válido.");
            }

            if (!fecha) {
                throw new Error("Selecciona una fecha.");
            }

            const fd = new FormData();
            fd.append("trabajador_id", trabajador_id);
            fd.append("monto", monto);
            fd.append("fecha", fecha);

            try {
                const resp = await fetch(`/myvet/app/controllers/nominaController.php?action=crearBono`, {
                    method: 'POST',
                    body: fd
                });

                const res = await resp.json();
                if (res.success) {
                    Swal.fire('Éxito', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message || 'Error desconocido', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Error en la petición', 'error');
            }
        });
        async function cargarTrabajadores() {
            const valor = $('#semana').val();
            if (!valor) return;

            const [anio, semana] = valor.split('-W');
            const lunes = obtenerLunesISO(parseInt(anio), parseInt(semana));
            const domingo = new Date(lunes);
            domingo.setDate(lunes.getDate() + 6);

            const inicio = formatearFecha(lunes);
            const fin = formatearFecha(domingo);
            const almacen = $('#almacen_id_editar').val();
            console.log(almacen);


            try {
                const res = await fetch(
                    `/myvet/app/controllers/nominaController.php?action=listar&fecha_inicio=${inicio}&fecha_fin=${fin}&almacen=${almacen}`
                );
                const response = await res.json();

                if (!response.success) {
                    throw new Error(response.message || 'Error al obtener nómina');
                }

                const data = response.data;
                console.log(data);

                // Destruir la instancia actual de DataTable si existe
                if ($.fn.DataTable.isDataTable('#tablaTrabajadores')) {
                    $('#tablaTrabajadores').DataTable().destroy();
                }

                let html = '';
                if (data.length === 0) {
                    html =
                        `<tr><td colspan="9" class="text-center py-4 text-body-secondary">No se encontraron trabajadores en este rango.</td></tr>`;
                } else {
                    data.forEach(t => {
                        let claseEstado = 'bg-danger';
                        if (t.estado === 'activo') claseEstado = 'bg-success';
                        else if (t.estado === 'vacaciones') claseEstado = 'bg-warning text-dark';
                        else if (t.estado === 'en_ruta') claseEstado = 'bg-info text-dark';

                        // Convertir objeto entero a JSON escapando comillas para evitar errores JS en línea
                        const tJson = JSON.stringify(t).replace(/'/g, "&#39;");
                        let total = t.total_nomina + t.total_vacaciones;
                        html += `
                    <tr>
                        <td class="fw-semibold">${t.nombre}</td>
                        <td>
                            <a href="https://wa.me/52${t.telefono}" target="_blank" class="text-decoration-none">
                                <i class="bi bi-whatsapp text-success me-1"></i>${t.telefono}
                            </a>
                        </td>
                        <td><span class="badge bg-light text-dark border text-uppercase">${t.rol}</span></td>
                        <td><i class="bi bi-geo-alt text-danger me-1"></i>${t.nombreAlmacen || 'N/A'}</td>
                        <td class="fw-bold text-primary">$${parseFloat(t.salario || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                       <td class="fw-bold text-primary">$${parseFloat(t.total_bonos || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                        <td class="text-danger fw-semibold">$${parseFloat(t.total_prestamos_pendientes || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                        <td>
                            <div class="small">
                                <div><span class="text-danger">Faltas:</span> $${parseFloat(t.total_faltas || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</div>
                                <div><span class="text-success">Viajes:</span> $${parseFloat(t.total_viajes || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</div>
                               <div><span class="text-success">Bonos:</span> $${parseFloat(t.total_bonos || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</div>
                                <div><span class="text-warning">Abonos:</span> $${parseFloat(t.total_abonos || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</div>
                               <div><span class="text-success">Vacaciones:</span> $${parseFloat(t.total_vacaciones || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</div>
                                <hr class="my-1">
                                <div class="fw-bold fs-6 text-primary">$${parseFloat(total || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</div>
                            </div>
                        </td>
                        <td><span class="badge rounded-pill ${claseEstado}">${t.estado ? t.estado.toUpperCase() : ''}</span></td>
                        <td class="text-end">
                                   <button class="btn btn-success btn-sm rounded-pill px-3 me-1" 
        onclick="imprimirNominaTrabajador(${t.id})"
        title="Imprimir cheque de nómina">
    <i class="bi bi-printer-fill me-1"></i> Imprimir cheque
</button>
                         <button class="btn btn-primary rounded-pill px-4" onclick='nuevoBono(${tJson})'>
                <i class="bi bi-cash-stack me-2"></i> Crear Bono
            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick='editarTrabajador(${tJson})'>
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick='eliminarTrabajador(${t.id})'>
                                <i class="bi bi-trash"></i>
                            </button>

                        </td>
                    </tr>`;
                    });
                }

                $('#bodyTrabajadores').html(html);
                $('#conteoTrabajadores').text(data.length);

                // Re-inicializar DataTable con los nuevos elementos
                tabla = $('#tablaTrabajadores').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    },
                    "dom": 'rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "pageLength": 10,
                    "order": [
                        [0, 'asc']
                    ]
                });

            } catch (err) {
                console.error(err);
                Swal.fire('Error', err.message || 'Error al cargar listado de trabajadores', 'error');
            }
        }

        function nuevoTrabajador() {
            $('#formTrabajador')[0].reset();
            $('#trabajador_id').val('0');
            if ($('#t_almacen_id').is('select')) $('#t_almacen_id').val('');
            $('#modalTitulo').text('Nuevo Trabajador');
            $('#modalTrabajador').modal('show');
        }

        function editarTrabajador(t) {
            console.log(t);
            $('#modalTitulo').text('Editar Trabajador');
            $('#trabajador_id').val(t.id);
            $('#t_nombre').val(t.nombre);
            $('#t_telefono').val(t.telefono);
            $('#t_rol').val(t.rol);
            $('#t_estado').val(t.estado);
            $('#t_salario').val((t.salario - t.complemento_pago));
            $('#t_complemento').val(t.complemento_pago);
            $('#fecha_ingreso').val(t.fecha_ingreso);
            if ($('#t_almacen_id').is('select')) {
                $('#t_almacen_id').val(t.almacen_id);
            }
            $('#modalTrabajador').modal('show');
        }

        $('#formTrabajador').on('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const resp = await fetch('/myvet/app/controllers/trabajadoresController.php', {
                    method: 'POST',
                    body: formData
                });
                const res = await resp.json();
                if (res.status === 'success' || res.success) {
                    $('#modalTrabajador').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        showConfirmButton: false,
                        timer: 1200
                    });
                    cargarTrabajadores();
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar los cambios', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
            }
        });

        function nuevoBono(t) {
            $('#formBono')[0].reset();
            const modal = new bootstrap.Modal(document.getElementById('modalBono'));
            $('#modal_bono_trabajador_id').val(t.id);
            $('#trabajador_nombre').val(t.nombre);
            $('#fecha__bono').val(<?= date('Y-m-d') ?>);

            modal.show();
            if ($('#modal_bono_almacen_id').val()) {
                $('#modal_bono_almacen_id').trigger('change');
            }
        }
        async function eliminarTrabajador(id) {
            const result = await Swal.fire({
                title: '¿Eliminar trabajador?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
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
                    if (res.status === 'success' || res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            timer: 1200,
                            showConfirmButton: false
                        });
                        cargarTrabajadores();
                    } else {
                        Swal.fire('Error', res.message || 'Error al eliminar', 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'No se pudo completar la solicitud', 'error');
                }
            }
        }

        function limpiarFiltros() {
            $('#busquedaTrabajador').val('');
            $('#filtroRol').val('');
            if (tabla) tabla.search('').column(2).search('').draw();
        }

        function imprimirContenidoModal() {
            window.print();
        }

        /* Auxiliares Fecha / Semana */
        function obtenerSemanaActual() {
            const hoy = new Date();
            const jueves = new Date(hoy);
            jueves.setDate(hoy.getDate() + 4 - (hoy.getDay() || 7));
            const inicioAnio = new Date(jueves.getFullYear(), 0, 1);
            const numeroSemana = Math.ceil((((jueves - inicioAnio) / 86400000) + 1) / 7);
            return `${jueves.getFullYear()}-W${String(numeroSemana).padStart(2, '0')}`;
        }

        function obtenerLunesISO(anio, semana) {
            const simple = new Date(anio, 0, 1 + (semana - 1) * 7);
            const dia = simple.getDay();
            const lunes = new Date(simple);
            if (dia <= 4) lunes.setDate(simple.getDate() - simple.getDay() + 1);
            else lunes.setDate(simple.getDate() + 8 - simple.getDay());
            return lunes;
        }

        function formatearFecha(fecha) {
            const y = fecha.getFullYear();
            const m = String(fecha.getMonth() + 1).padStart(2, '0');
            const d = String(fecha.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }
        async function imprimirContenidoModal() {
            // 1. Obtener la semana seleccionada
            const valorSemana = $('#semana').val();
            let queryParams = '';

            if (valorSemana) {
                const [anio, semana] = valorSemana.split('-W');
                const lunes = obtenerLunesISO(parseInt(anio), parseInt(semana));
                const domingo = new Date(lunes);
                domingo.setDate(lunes.getDate() + 6);

                const inicio = formatearFecha(lunes);
                const fin = formatearFecha(domingo);
                const almacen = $('#almacen_id_editar').val();
                queryParams = `&fecha_inicio=${inicio}&fecha_fin=${fin}&almacen=${almacen}`;
            }

            try {
                // 2. Obtener los datos de la nómina
                const res = await fetch(`/myvet/app/controllers/nominaController.php?action=listar${queryParams}`);
                const response = await res.json();

                if (!response.success) {
                    throw new Error(response.message || 'No se pudieron obtener los datos de la nómina.');
                }

                const data = response.data || [];

                if (data.length === 0) {
                    Swal.fire('Atención', 'No hay registros de nómina para imprimir en esta semana.', 'info');
                    return;
                }

                // 3. Generar las tarjetas/cheques individuales recortables
                let chequesHTML = '';

                data.forEach((t, index) => {

                    const prestamos = parseFloat(t.total_prestamos_pendientes || 0);
                    const faltas = parseFloat(t.total_faltas || 0);
                    const viajes = parseFloat(t.total_viajes || 0);
                    const abonos = parseFloat(t.total_abonos || 0);
                    const bonos = parseFloat(t.total_bonos || 0);
                    const vacaciones = parseFloat(t.total_vacaciones || 0);
                    const retenciones = parseFloat(t.total_retenciones || 0);
                    const salario = parseFloat(t.salario || 0) + bonos;

                    const totalNomina = parseFloat(t.total_nomina || 0) + vacaciones;


                    chequesHTML += `
            <div class="cheque-contenedor">
                <!-- Marca de Agua Individual -->
                <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" class="watermark-cheque" alt="Logo">

                <!-- Encabezado del Cheque -->
                <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom border-2 border-primary">
                    <div class="d-flex align-items-center">
                        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" width="28" height="28" class="me-2" alt="Logo">
                        <div>
                            <h6 class="fw-bold m-0 text-uppercase" style="font-size: 0.8rem;">CF SYSTEM - RECIBO DE NÓMINA</h6>
                            <small class="text-body-secondary" style="font-size: 0.6rem;">COMPROBANTE DE PAGO DE SALARIO</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-dark text-white font-monospace" style="font-size: 0.65rem;">SEMANA: ${valorSemana || 'N/A'}</span>
                        <small class="d-block text-body-secondary" style="font-size: 0.58rem;">Emisión: ${new Date().toLocaleDateString('es-MX')}</small>
                    </div>
                </div>

                <!-- Datos del Trabajador y Salario Total -->
                <div class="row g-1 mb-1 bg-light p-1 rounded align-items-center border">
                    <div class="col-7">
                        <div class="text-uppercase fw-bold text-dark" style="font-size: 0.8rem;">${t.nombre}</div>
                        <small class="text-body-secondary d-block" style="font-size: 0.65rem;">
                            <strong>Puesto:</strong> ${t.rol.toUpperCase()} | <strong>Almacén:</strong> ${t.nombreAlmacen || 'N/A'}
                        </small>
                    </div>
                    <div class="col-5 text-end">
                        <small class="text-uppercase text-body-secondary d-block" style="font-size: 0.58rem; font-weight: 700;">Neto a Recibir</small>
                        <span class="fs-6 fw-bold text-success font-monospace">$${totalNomina.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</span>
                    </div>
                </div>

                <!-- Desglose de Percepciones y Deducciones -->
                <table class="table table-sm table-bordered text-center mb-1" style="font-size: 0.65rem;">
                    <thead class="table-secondary text-uppercase">
                        <tr>
                            <th>Salario Base</th>
                            <th>(+) Viajes</th>
                            <th>(-) Faltas</th>
                            <th>(-) Abonos Préstamo</th>
                            <th>(+) Prima Vacacional</th>
                            <th>Saldo Préstamo Act.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$${salario.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-success">+$${viajes.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-danger">-$${faltas.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-warning text-dark">-$${abonos.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-body-secondary">$${vacaciones.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
               
                            <td class="text-body-secondary">$${prestamos.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                                     </tr>
                    </tbody>
                </table>

                <!-- Leyenda y Zona de Firmas -->
                <p class="text-body-secondary text-justify mb-1" style="font-size: 0.55rem; line-height: 1.05;">
                    Recibí a mi entera satisfacción la cantidad neta descrita en este documento por concepto de pago de mis salarios y prestaciones correspondientes al período indicado, no adeudándome cantidad alguna.
                </p>

                <div class="row pt-2 text-center" style="font-size: 0.65rem;">
                    <div class="col-6">
                        <div class="border-top border-dark mx-2 pt-1 fw-bold text-uppercase">
                            Firma del Trabajador
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border-top border-dark mx-2 pt-1 fw-bold text-uppercase">
                            Conformidad / Empresa
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea de corte para tijeras -->
            <div class="corte-linea">
                <span><i class="bi bi-scissors"></i> CORTE AQUÍ</span>
            </div>
            `;
                });

                // 4. Inyectar en la ventana de impresión
                const ventanaImpresion = window.open('', '_blank');

                ventanaImpresion.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cheques de Nómina - ${valorSemana || 'Semanal'}</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
            <style>
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
                    padding: 8px; 
                    background: #f4f4f4;
                    color: #222;
                }
                .cheque-contenedor {
                    background: #ffffff;
                    border: 1.5px dashed #007aff;
                    border-radius: 6px;
                    padding: 8px 12px;
                    position: relative;
                    overflow: hidden;
                    page-break-inside: avoid;
                    margin-bottom: 2px;
                    height: 5.9cm; /* Altura máxima controlada para forzar 4 por página */
                }
                .watermark-cheque {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 110px;
                    opacity: 0.04;
                    pointer-events: none;
                    z-index: 0;
                }
                .corte-linea {
                    text-align: center;
                    border-bottom: 1px dashed #999;
                    line-height: 0.1em;
                    margin: 6px 0 8px 0;
                    page-break-inside: avoid;
                }
                .corte-linea span {
                    background: #f4f4f4;
                    padding: 0 6px;
                    font-size: 0.6rem;
                    color: #666;
                    font-weight: bold;
                    letter-spacing: 0.5px;
                }
                @media print {
                    body { background: #ffffff; padding: 0; }
                    .cheque-contenedor { border: 1px solid #333; }
                    .corte-linea span { background: #ffffff; }
                }
                @page { 
                    margin: 0.4cm; 
                }
            </style>
        </head>
        <body>
            <div id="areaImpresion">
                ${chequesHTML}
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                    setTimeout(() => {
                        if (esMovil) {
                            const elementoParaConvertir = document.getElementById('areaImpresion');
                            const opciones = {
                                margin: 0.3,
                                filename: 'Recibos_Nomina_${valorSemana || 'Semana'}.pdf',
                                image: { type: 'jpeg', quality: 0.98 },
                                html2canvas: { scale: 2, useCORS: true },
                                jsPDF: { unit: 'cm', format: 'letter', orientation: 'portrait' }
                            };
                            html2pdf().set(opciones).from(elementoParaConvertir).save();
                        } else {
                            window.print();
                        }
                    }, 800);
                });
            <\/script>
        </body>
        </html>
    `);

                ventanaImpresion.document.close();

            } catch (err) {
                console.error("Error al generar recibos de nómina:", err);
                Swal.fire('Error', err.message || 'No se pudieron generar los recibos.', 'error');
            }
        } </script>
    <script>
        async function imprimirNominaTrabajador(trabajadorId) {
            // 1. Obtener la semana seleccionada
            const valorSemana = $('#semana').val();
            let queryParams = '';

            if (valorSemana) {
                const [anio, semana] = valorSemana.split('-W');
                const lunes = obtenerLunesISO(parseInt(anio), parseInt(semana));
                const domingo = new Date(lunes);
                domingo.setDate(lunes.getDate() + 6);

                const inicio = formatearFecha(lunes);
                const fin = formatearFecha(domingo);
                const trabajador = trabajadorId;
                queryParams = `&fecha_inicio=${inicio}&fecha_fin=${fin}&trabajador=${trabajador}`;
            }

            try {
                // 2. Obtener los datos de la nómina
                const res = await fetch(`/myvet/app/controllers/nominaController.php?action=verNomina${queryParams}`);
                const response = await res.json();

                if (!response.success) {
                    throw new Error(response.message || 'No se pudieron obtener los datos de la nómina.');
                }

                const data = response.data || [];

                if (data.length === 0) {
                    Swal.fire('Atención', 'No hay registros de nómina para imprimir en esta semana.', 'info');
                    return;
                }

                // 3. Generar las tarjetas/cheques individuales recortables
                let chequesHTML = '';

                data.forEach((t, index) => {

                    const prestamos = parseFloat(t.total_prestamos_pendientes || 0);
                    const faltas = parseFloat(t.total_faltas || 0);
                    const viajes = parseFloat(t.total_viajes || 0);
                    const abonos = parseFloat(t.total_abonos || 0);
                    const bonos = parseFloat(t.total_bonos || 0);
                    const vacaciones = parseFloat(t.total_vacaciones || 0);
                    const retenciones = parseFloat(t.total_retenciones || 0);
                    const salario = parseFloat(t.salario || 0) + bonos;

                    const totalNomina = parseFloat(t.total_nomina || 0) + vacaciones;


                    chequesHTML += `
            <div class="cheque-contenedor">
                <!-- Marca de Agua Individual -->
                <img src="/cfsistem/public/assets/logo.ico" class="watermark-cheque" alt="Logo">

                <!-- Encabezado del Cheque -->
                <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom border-2 border-primary">
                    <div class="d-flex align-items-center">
                        <img src="/cfsistem/public/assets/logo.ico" width="28" height="28" class="me-2" alt="Logo">
                        <div>
                            <h6 class="fw-bold m-0 text-uppercase" style="font-size: 0.8rem;">CF SYSTEM - RECIBO DE NÓMINA</h6>
                            <small class="text-body-secondary" style="font-size: 0.6rem;">COMPROBANTE DE PAGO DE SALARIO</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-dark text-white font-monospace" style="font-size: 0.65rem;">SEMANA: ${valorSemana || 'N/A'}</span>
                        <small class="d-block text-body-secondary" style="font-size: 0.58rem;">Emisión: ${new Date().toLocaleDateString('es-MX')}</small>
                    </div>
                </div>

                <!-- Datos del Trabajador y Salario Total -->
                <div class="row g-1 mb-1 bg-light p-1 rounded align-items-center border">
                    <div class="col-7">
                        <div class="text-uppercase fw-bold text-dark" style="font-size: 0.8rem;">${t.nombre}</div>
                        <small class="text-body-secondary d-block" style="font-size: 0.65rem;">
                            <strong>Puesto:</strong> ${t.rol.toUpperCase()} | <strong>Almacén:</strong> ${t.nombreAlmacen || 'N/A'}
                        </small>
                    </div>
                    <div class="col-5 text-end">
                        <small class="text-uppercase text-body-secondary d-block" style="font-size: 0.58rem; font-weight: 700;">Neto a Recibir</small>
                        <span class="fs-6 fw-bold text-success font-monospace">$${totalNomina.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</span>
                    </div>
                </div>

                <!-- Desglose de Percepciones y Deducciones -->
                <table class="table table-sm table-bordered text-center mb-1" style="font-size: 0.65rem;">
                    <thead class="table-secondary text-uppercase">
                        <tr>
                            <th>Salario Base</th>
                            <th>(+) Viajes</th>
                            <th>(-) Faltas</th>
                            <th>(-) Abonos Préstamo</th>
                            <th>(+) Prima Vacacional</th>
                            <th>Saldo Préstamo Act.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$${salario.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-success">+$${viajes.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-danger">-$${faltas.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-warning text-dark">-$${abonos.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                            <td class="text-body-secondary">$${vacaciones.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
               
                            <td class="text-body-secondary">$${prestamos.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                                     </tr>
                    </tbody>
                </table>

                <!-- Leyenda y Zona de Firmas -->
                <p class="text-body-secondary text-justify mb-1" style="font-size: 0.55rem; line-height: 1.05;">
                    Recibí a mi entera satisfacción la cantidad neta descrita en este documento por concepto de pago de mis salarios y prestaciones correspondientes al período indicado, no adeudándome cantidad alguna.
                </p>

                <div class="row pt-2 text-center" style="font-size: 0.65rem;">
                    <div class="col-6">
                        <div class="border-top border-dark mx-2 pt-1 fw-bold text-uppercase">
                            Firma del Trabajador
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border-top border-dark mx-2 pt-1 fw-bold text-uppercase">
                            Conformidad / Empresa
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea de corte para tijeras -->
            <div class="corte-linea">
                <span><i class="bi bi-scissors"></i> CORTE AQUÍ</span>
            </div>
            `;
                });

                // 4. Inyectar en la ventana de impresión
                const ventanaImpresion = window.open('', '_blank');

                ventanaImpresion.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cheques de Nómina - ${valorSemana || 'Semanal'}</title>
      <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
            <style>
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
                    padding: 8px; 
                    background: #f4f4f4;
                    color: #222;
                }
                .cheque-contenedor {
                    background: #ffffff;
                    border: 1.5px dashed #007aff;
                    border-radius: 6px;
                    padding: 8px 12px;
                    position: relative;
                    overflow: hidden;
                    page-break-inside: avoid;
                    margin-bottom: 2px;
                    height: 5.9cm; /* Altura máxima controlada para forzar 4 por página */
                }
                .watermark-cheque {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 110px;
                    opacity: 0.04;
                    pointer-events: none;
                    z-index: 0;
                }
                .corte-linea {
                    text-align: center;
                    border-bottom: 1px dashed #999;
                    line-height: 0.1em;
                    margin: 6px 0 8px 0;
                    page-break-inside: avoid;
                }
                .corte-linea span {
                    background: #f4f4f4;
                    padding: 0 6px;
                    font-size: 0.6rem;
                    color: #666;
                    font-weight: bold;
                    letter-spacing: 0.5px;
                }
                @media print {
                    body { background: #ffffff; padding: 0; }
                    .cheque-contenedor { border: 1px solid #333; }
                    .corte-linea span { background: #ffffff; }
                }
                @page { 
                    margin: 0.4cm; 
                }
            </style>
        </head>
        <body>
            <div id="areaImpresion">
                ${chequesHTML}
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                    setTimeout(() => {
                        if (esMovil) {
                            const elementoParaConvertir = document.getElementById('areaImpresion');
                            const opciones = {
                                margin: 0.3,
                                filename: 'Recibos_Nomina_${valorSemana || 'Semana'}.pdf',
                                image: { type: 'jpeg', quality: 0.98 },
                                html2canvas: { scale: 2, useCORS: true },
                                jsPDF: { unit: 'cm', format: 'letter', orientation: 'portrait' }
                            };
                            html2pdf().set(opciones).from(elementoParaConvertir).save();
                        } else {
                            window.print();
                        }
                    }, 800);
                });
            <\/script>
        </body>
        </html>
    `);

                ventanaImpresion.document.close();

            } catch (err) {
                console.error("Error al generar recibos de nómina:", err);
                Swal.fire('Error', err.message || 'No se pudieron generar los recibos.', 'error');
            }
        }
    </script>

</body>

</html>