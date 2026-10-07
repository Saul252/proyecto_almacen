<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'Gestión de Citas Médicas') ?></title>

    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>
    <style>
        :root {
            --primary-color: #0284c7;
            --bg-color: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {}

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            text-align: center;
        }

        .day-header {
            font-size: 0.75rem;
            font-weight: bold;
            color: #64748b;
            padding: 4px 0;
        }

        .day-cell {
            padding: 8px 0;
            font-size: 0.85rem;
            border-radius: 6px;
            cursor: pointer;

            transition: background 0.15s;
        }

        .day-cell:hover:not(.empty) {
            background-color: #0d8713;
        }

        .day-cell.selected {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
        }

        .day-cell.empty {
            background: transparent;
            cursor: default;
        }

        .badge-pendiente {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .badge-completada {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-cancelada {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Estilos de Impresión */
        @media print {

            .no-print,
            header,
            nav,
            .sidebar,
            .btn,
            form {
                display: none !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
            }

            .container-fluid {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .table th {
                background-color: #eee !important;
                color: #000 !important;
            }
        }
    </style>
</head>

<body>
    <?php renderizarLayout($tituloPagina); ?>
    <div class="main-content container-fluid my-4 px-4">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center  p-3 rounded border mb-4 shadow-sm">
            <div>
                <h3 class="m-0 fw-bold"><i
                        class="fa-solid fa-calendar-check text-primary me-2"></i><?= htmlspecialchars($tituloPagina) ?>
                </h3>
                <small class="text-muted">Control de agenda, filtrado por fecha e impresión</small>
            </div>
            <div class="no-print d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="abrirModalCrear()">
                    <i class="fa-solid fa-plus me-1"></i> Nueva Cita
                </button>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Imprimir Citas del Día
                </button>
            </div>
        </div>



        <div class="row g-4">
            <!-- Panel Izquierdo: Filtro Mes y Calendario -->
            <div class="col-lg-4 no-print">
                <div class="card shadow-sm  p-3">
                    <h5 class="fw-bold mb-3">Filtrar por Fecha</h5>

                    <div class="mb-3">
                        <label for="monthSelector" class="form-label small fw-semibold text-muted">Seleccionar
                            Mes:</label>
                        <input type="month" id="monthSelector" class="form-control"
                            onchange="actualizarMes(this.value)">
                    </div>

                    <div class="text-center fw-bold text-primary mb-2" id="calendarTitle"></div>
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
            </div>

            <!-- Panel Derecho: Listado de Citas -->
            <div class="col-lg-8">
                <div class="card shadow-sm  p-4">
                    <!-- Cabecera principal -->
                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold m-0 " id="tituloFecha">Citas del día</h5>
                            <span class="badge  text-secondary border mt-1 no-print px-2 py-1" id="contadorCitas">0
                                citas</span>
                        </div>

                        <!-- Controles de filtrado organizados horizontalmente -->
                        <div class="d-flex flex-wrap align-items-end gap-3">
                            <!-- Almacén -->
                            <div style="min-width: 180px;">
                                <label class="form-label text-body-secondary fw-semibold small mb-1">
                                    <i class="bi bi-box-seam me-1 text-primary"></i> Consultorio
                                </label>
                                <select name="almacen_id_editar" id="almacen_id_editar"
                                    class="form-select form-select-sm shadow-sm rounded-3 py-2" required>
                                    <?php foreach ($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Doctor -->
                            <div style="min-width: 180px;">
                                <label class="form-label text-body-secondary fw-semibold small mb-1">
                                    <i class="bi bi-person-badge me-1 text-primary"></i> Doctor
                                </label>
                                <select name="doctor" id="doctor"
                                    class="form-select form-select-sm shadow-sm rounded-3 py-2 select2-pagina" required>
                                    <option value="">Seleccionar...</option>
                                </select>
                            </div>

                            <!-- Buscador -->
                            <div style="min-width: 160px;">
                                <label class="form-label text-body-secondary fw-semibold small mb-1">
                                    <i class="bi bi-search me-1 text-primary"></i> Buscar
                                </label>
                                <input type="text" id="search"
                                    class="form-control form-control-sm shadow-sm rounded-3 py-2"
                                    placeholder="Paciente...">
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive rounded-3 border" style="min-height: 300px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-7">
                                <tr>
                                    <th class="py-3 ps-3">Hora</th>
                                    <th class="py-3">Paciente</th>
                                    <th class="py-3">Atenderá</th>
                                    <th class="py-3">Detalles / Servicio</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3 no-print text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="citasTableBody">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registrar / Editar Cita -->
    <div class="modal fade no-print" id="modalCita" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalCitaLabel">Agendar Cita Médica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formCita" onsubmit="guardarCita(event)">
                    <div class="modal-body">
                        <input type="hidden" id="cita_id" name="cita_id" value="0">
                        <select name="almacen_id" id="almacen_id"
                            class="form-select form-select-sm shadow-sm rounded-3 py-2" required>
                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="tipoCita" name="tipoCita" value="medica">

                        <div class="mb-3">
                            <label class="form-label text-body-secondary fw-semibold small mb-1">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Paciente
                            </label>
                            <select name="paciente_id" id="paciente_id"
                                class="form-select  shadow-sm rounded-3 py-2 select2-pagina" required>
                                <option value="">Seleccionar Paciente...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fecha" class="form-label fw-semibold">Fecha y Hora *</label>
                            <input type="datetime-local" class="form-control" id="fecha" name="fecha" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-body-secondary fw-semibold small mb-1">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Doctor que atendera
                            </label>
                            <select name="atendera" id="atendera"
                                class="form-select  shadow-sm rounded-3 py-2 select2-pagina" required>
                                <option value="">Seleccionar Doctor...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="detalles" class="form-label fw-semibold">Detalles / Motivo</label>
                            <textarea class="form-control" id="detalles" name="detalles" rows="3"
                                placeholder="Motivo de la consulta o notas adicionaes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Cita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>
    <script>
        // Colección de citas inyectada desde PHP
        const todasLasCitas = <?= json_encode($citas ?? []) ?>;

        // Objeto Modal Bootstrap
        let modalCitaBS = null;
        let fechaActual = 0;
        let fechaSeleccionada = new Date().toISOString().substring(0, 10);
        $(document).ready(function () {
            // Disparar cuando cambie el select de almacén
            $('#almacen_id_editar').on('change', function () {
                // Opcional: Primero actualiza los doctores de ese almacén si lo necesitas
                cargarDoctores().then(() => {
                    // Reemplaza con tu selector de fecha activo
                    if (fechaActual) {
                        filtrarYMostrarCitas(fechaActual);
                    }
                });
            });

            // Disparar cuando cambie el select del doctor/atenderá o el filtro de doctor
            $('#doctor').on('change', function () {
                // Reemplaza con tu selector de fecha activo
                console.log("diltro doctor");
                if (fechaActual) {
                    filtrarYMostrarCitas(fechaActual);
                }
            });

            // Disparar cuando escribas en el buscador (con un pequeño retraso opcional o directo)
            $('#search').on('input', function () {
                // Reemplaza con tu selector de fecha activo
                if (fechaActual) {
                    filtrarYMostrarCitas(fechaActual);
                }
            });
        });
        document.addEventListener('DOMContentLoaded', () => {
            cargarDoctoresFiltro();
            modalCitaBS = new bootstrap.Modal(document.getElementById('modalCita'));

            const mesActual = fechaSeleccionada.substring(0, 7);
            document.getElementById('monthSelector').value = mesActual;

            renderizarCalendario(mesActual);
            filtrarYMostrarCitas(fechaSeleccionada);
            fechaActual = fechaSeleccionada;
        });

        function actualizarMes(anioMes) {
            if (!anioMes) return;
            renderizarCalendario(anioMes);
        }

        function seleccionarDia(fechaStr) {
            fechaSeleccionada = fechaStr;

            const anioMes = fechaStr.substring(0, 7);
            renderizarCalendario(anioMes);
            fechaActual = fechaStr;
            filtrarYMostrarCitas(fechaStr);
        }

        function renderizarCalendario(anioMes) {
            const [year, month] = anioMes.split('-').map(Number);
            const primerDia = new Date(year, month - 1, 1);
            const ultimoDia = new Date(year, month, 0);

            const nombresMeses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            document.getElementById('calendarTitle').innerText = `${nombresMeses[month - 1]} ${year}`;

            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';

            const diasSemana = ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"];
            diasSemana.forEach(d => {
                const el = document.createElement('div');
                el.className = 'day-header';
                el.innerText = d;
                grid.appendChild(el);
            });

            for (let i = 0; i < primerDia.getDay(); i++) {
                const empty = document.createElement('div');
                empty.className = 'day-cell empty';
                grid.appendChild(empty);
            }

            for (let day = 1; day <= ultimoDia.getDate(); day++) {
                const cell = document.createElement('div');
                const diaFormatted = String(day).padStart(2, '0');
                const fechaStr = `${year}-${String(month).padStart(2, '0')}-${diaFormatted}`;

                cell.className = 'day-cell';
                cell.innerText = day;

                if (fechaStr === fechaSeleccionada) {
                    cell.classList.add('selected');
                }

                cell.onclick = () => seleccionarDia(fechaStr);
                grid.appendChild(cell);
            }
        }

        function filtrarYMostrarCitas(fechaStr) {
            document.getElementById('tituloFecha').innerText = `Citas del día: ${fechaStr}`;
            const tbody = document.getElementById('citasTableBody');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><i class="fa-solid fa-spinner fa-spin"></i> Cargando citas...</td></tr>';

            // Obtener valores de los filtros
            const almacenId = $('#almacen_id_editar').val() || 0;
            const doctor = $('#doctor').val() || 0;
            const search = $('#search').val() || '';
            fechaActual = fechaStr;

            // Parámetros de consulta
            const params = new URLSearchParams({
                action: 'listar',
                f_fecha: fechaStr,
                f_almacen: 0,
                f_atendera: doctor,
                f_search: search,
                f_tipo: 'medica'
            });

            // Petición a la URL específica del controlador
            fetch(`/myvet/app/controllers/citasMedicasController.php?${params.toString()}`)
                .then(res => res.json())
                .then(citasFiltradas => {
                    tbody.innerHTML = '';
                    document.getElementById('contadorCitas').innerText = `${citasFiltradas.length} cita(s)`;

                    if (!Array.isArray(citasFiltradas) || citasFiltradas.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No hay citas programadas para esta fecha.</td></tr>`;
                        return;
                    }

                    citasFiltradas.forEach(c => {
                        const horaFormatted = (c.fecha && c.fecha.length > 10) ? c.fecha.substring(11, 16) : '--:--';
                        const estado = c.estado || 'pendiente';

                        // Configuración de estilos y bordes sutiles para los estados
                        const estadosConfig = {
                            'pendiente': { class: 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', icon: 'bi-clock-history' },
                            'completada': { class: 'bg-success-subtle text-success-emphasis border border-success-subtle', icon: 'bi-check-circle' },
                            'cancelada': { class: 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', icon: 'bi-x-circle' }
                        };
                        const currentEstado = estadosConfig[estado.toLowerCase()] || estadosConfig['pendiente'];
                        const irConsulta = c.estado == 'completada' ? ` <!-- Botón de Expediente / Consulta (Destacado y más elegante) -->
                <a href="/myvet/consultaDental?id=${c.id}&razon=${c.detalles}" 
                   class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm rounded-pill px-3 py-1.5 transition-all" 
                   title="Abrir Expediente Dental">
                    <i class="bi bi-tooth fs-6"></i>
                    <span class="fw-semibold">Ir a consulta</span>
                </a>`: '';

                        const tr = document.createElement('tr');
                        tr.className = "align-middle"; // Asegura alineación vertical perfecta en Bootstrap
                        tr.innerHTML = `
        <td class="ps-4 py-3">
            <div class="d-flex align-items-center">
                <div class=" text-primary rounded-3 p-2 me-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 38px; height: 38px;">
                    <i class="bi bi-clock fs-6"></i>
                </div>
                <span class="fw-bold  fs-6">${horaFormatted}</span>
            </div>
        </td>
        <td class="py-3">
            <div class="fw-bold  mb-0.5">${escapeHtml(c.paciente_nombre || c.paciente || 'Paciente N/A')}</div>
            ${c.paciente_telefono ? `<div class="text-muted small d-flex align-items-center gap-1"><i class="bi bi-telephone text-secondary"></i><span>${escapeHtml(c.paciente_telefono)}</span></div>` : ''}
        </td>
        <td class="py-3">
            <div class=" fw-medium d-flex align-items-center gap-1.5">
                <i class="bi bi-person-badge text-muted"></i>
                <span>${escapeHtml(c.doctor || 'No asignado')}</span>
            </div>
        </td>
        <td class="py-3">
            <span class=" small d-inline-block text-truncate" style="max-width: 200px;" title="${escapeHtml(c.detalles || 'Sin detalles')}">
                ${escapeHtml(c.detalles || 'Sin detalles')}
            </span>
        </td>
        <td class="py-3">
            <span class="badge ${currentEstado.class} text-uppercase px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1.5 fw-semibold shadow-xs">
                <i class="bi ${currentEstado.icon} fs-7"></i>
                <span>${estado}</span>
            </span>
        </td>
        <td class="no-print text-end pe-4 py-3">
          <!-- Botón Editar con estilo limpio -->
                <button class="btn btn-sm btn-light border text-dark shadow-sm rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" onclick="editarCita(${c.id})" title="Editar">
                    <i class="bi bi-pencil-square text-warning"></i>
                   
                </button>

                <!-- Botón para imprimir PDF -->
<button class="btn btn-sm btn-light border text-danger shadow-sm rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" onclick="imprimirCitaPDF(${c.id})" title="Imprimir PDF">
    <i class="bi bi-file-earmark-pdf"></i>
    <span class="small fw-medium">PDF</span>
</button>
                 <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-light border text-secondary shadow-sm rounded-pill px-2.5 py-1.5 dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                        <span class="small fw-medium">Estado</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg  py-2 rounded-3">
                        <li><h6 class="dropdown-header text-uppercase fs-8 text-muted fw-bold">Cambiar estado</h6></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="cambiarEstado(${c.id}, 'pendiente')"><i class="bi bi-clock-history text-warning fs-6"></i> Pendiente</a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="cambiarEstado(${c.id}, 'completada')"><i class="bi bi-check-circle text-success fs-6"></i> Completada</a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="cambiarEstado(${c.id}, 'cancelada')"><i class="bi bi-x-circle text-danger fs-6"></i> Cancelada</a></li>
                    </ul>
                </div>
            <div class="d-inline-flex align-items-center gap-2">
            ${irConsulta}
               

              

                <!-- Menú Desplegable de Estado -->
               
            </div>
        </td>
    `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Error al cargar las citas: ${err}</td></tr>`;
                });
        }

        // Modal Crear Cita
        function abrirModalCrear() {
            document.getElementById('formCita').reset();
            document.getElementById('cita_id').value = '0';
            document.getElementById('modalCitaLabel').innerText = 'Agendar Cita Médica';
            modalCitaBS.show();
            cargarDoctores();
            cargarPacientes();
        }

        // Modal Editar - Carga AJAX a action=obtenerPorId
        function editarCita(id) {
            cargarDoctores();
            cargarPacientes();
            fetch(`/myvet/app/controllers/citasMedicasController.php?action=obtenerPorId&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const data = res.data;
                        document.getElementById('cita_id').value = data.id;

                        // Seleccionar el paciente asignándole el valor numérico y disparando el evento para Select2
                        const $pacienteSelect = $('#paciente_id');
                        if ($pacienteSelect.find(`option[value="${data.paciente_id}"]`).length) {
                            $pacienteSelect.val(data.paciente_id).trigger('change');
                        } else {
                            // Si el paciente no está en las opciones cargadas actualmente, lo agregamos de manera dinámica
                            let newOption = new Option(data.paciente_nombre, data.paciente_id, true, true);
                            $pacienteSelect.append(newOption).trigger('change');
                        }

                        // Ajustar formato datetime-local (YYYY-MM-DDTHH:MM)
                        let fechaFormatted = data.fecha.replace(' ', 'T');
                        document.getElementById('fecha').value = fechaFormatted.substring(0, 16);

                        // Seleccionar el doctor (atenderá) asignándole el valor numérico y disparando el evento para Select2
                        const $atenderaSelect = $('#atendera');
                        if ($atenderaSelect.find(`option[value="${data.atendera}"]`).length) {
                            $atenderaSelect.val(data.atendera).trigger('change');
                        } else {
                            $atenderaSelect.val(data.atendera).trigger('change');
                        }

                        document.getElementById('detalles').value = data.detalles;

                        document.getElementById('modalCitaLabel').innerText = 'Editar Cita Médica';
                        modalCitaBS.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'Error al obtener datos de la cita.'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'Error en la petición: ' + err
                    });
                });
        }
        function imprimirCitaPDF(id) {
            fetch(`/myvet/app/controllers/citasMedicasController.php?action=obtenerPorId&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const c = res.data;

                        // Formatear fecha y hora
                        const fechaObj = new Date(c.fecha);
                        const fechaFormateada = !isNaN(fechaObj) ? fechaObj.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : c.fecha;
                        const horaFormateada = (c.fecha && c.fecha.length > 10) ? c.fecha.substring(11, 16) : '--:--';

                        // Abrir la nueva ventana en blanco inmediatamente
                        const ventanaImpresion = window.open('', '_blank');

                        ventanaImpresion.document.write(`
                    <!DOCTYPE html>
                    <html lang="es">
                    <head>
                        <meta charset="UTF-8">
                        <title>Comprobante de Cita #${c.id}</title>
                        <style>
                            body {
                                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                                background-color: #ffffff;
                                color: #333333;
                                margin: 0;
                                padding: 40px;
                                display: flex;
                                justify-content: center;
                            }
                            .ticket-container {
                                width: 100%;
                                max-width: 600px;
                                border: 1px solid #e0e0e0;
                                padding: 30px;
                                border-radius: 8px;
                                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                                background: #ffffff;
                            }
                            .header {
                                text-align: center;
                                border-bottom: 2px solid #007bff;
                                padding-bottom: 20px;
                                margin-bottom: 25px;
                            }
                            .header h2 {
                                margin: 0 0 5px 0;
                                color: #007bff;
                                font-size: 22px;
                            }
                            .header p {
                                margin: 0;
                                color: #666666;
                                font-size: 13px;
                            }
                            .info-row {
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 20px;
                                border-bottom: 1px solid #f0f0f0;
                                padding-bottom: 15px;
                            }
                            .info-group {
                                flex: 1;
                            }
                            .info-group label {
                                display: block;
                                font-size: 11px;
                                text-transform: uppercase;
                                color: #888888;
                                margin-bottom: 4px;
                                font-weight: bold;
                            }
                            .info-group span {
                                font-size: 15px;
                                color: #222222;
                                font-weight: 500;
                            }
                            .badge {
                                display: inline-block;
                                padding: 4px 10px;
                                font-size: 12px;
                                font-weight: bold;
                                text-transform: uppercase;
                                border-radius: 4px;
                                background-color: #e9ecef;
                                color: #495057;
                            }
                            .details-box {
                                background-color: #f8f9fa;
                                border: 1px solid #e9ecef;
                                padding: 15px;
                                border-radius: 6px;
                                margin-bottom: 25px;
                            }
                            .details-box label {
                                display: block;
                                font-size: 11px;
                                text-transform: uppercase;
                                color: #888888;
                                margin-bottom: 6px;
                                font-weight: bold;
                            }
                            .details-box p {
                                margin: 0;
                                font-size: 14px;
                                color: #444444;
                                line-height: 1.5;
                            }
                            .footer {
                                text-align: center;
                                border-top: 1px solid #f0f0f0;
                                padding-top: 15px;
                                font-size: 12px;
                                color: #777777;
                            }
                            .no-print {
                                text-align: center;
                                margin-top: 30px;
                            }
                            .btn-print {
                                background-color: #007bff;
                                color: white;
                                border: none;
                                padding: 10px 25px;
                                font-size: 14px;
                                font-weight: bold;
                                border-radius: 20px;
                                cursor: pointer;
                                transition: background 0.2s;
                            }
                            .btn-print:hover {
                                background-color: #0056b3;
                            }
                            @media print {
                                body {
                                    padding: 0;
                                }
                                .ticket-container {
                                    border: none;
                                    box-shadow: none;
                                    padding: 0;
                                    max-width: 100%;
                                }
                                .no-print {
                                    display: none;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="ticket-container">
                            <div class="header">
                                <h2>MyVet - Comprobante de Cita Médica</h2>
                                <p>Registro oficial de consulta</p>
                            </div>

                            <div class="info-row">
                               
                                <div class="info-group" >
                                    <label>Estado Actual</label>
                                    <span class="badge">${c.estado || 'pendiente'}</span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-group">
                                    <label>Paciente</label>
                                    <span style="font-size: 16px; font-weight: bold;">${c.paciente_nombre || 'N/A'}</span>
                                    ${c.paciente_telefono ? `<br><small style="color: #666;">Tel: ${c.paciente_telefono}</small>` : ''}
                                </div>
                                <div class="info-group" style="text-align: right;">
                                    <label>Doctor / Atenderá</label>
                                    <span> ${c.doctor}</span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-group">
                                    <label>Fecha y Hora</label>
                                    <span>${fechaFormateada} a las ${horaFormateada} hrs</span>
                                </div>
                            </div>

                            <div class="details-box">
                                <label>Detalles / Motivo de Consulta</label>
                                <p>${c.detalles || 'Sin detalles especificados.'}</p>
                            </div>

                            <div class="footer">
                                <p style="margin: 0 0 5px 0;">Gracias por su confianza. Por favor llegue 10 minutos antes.</p>
                                <small>Impreso el: ${new Date().toLocaleString()}</small>
                            </div>

                            <div class="no-print">
                                <button class="btn-print" onclick="window.print();">Imprimir / Guardar como PDF</button>
                            </div>
                        </div>
                    </body>
                    </html>
                `);

                        ventanaImpresion.document.close();
                    } else {
                        alert('No se pudieron obtener los datos para la impresión.');
                    }
                })
                .catch(err => {
                    alert('Error de red al intentar generar el documento.');
                });
        }
        // Guardar / Actualizar - AJAX POST a action=guardar
        function guardarCita(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('formCita'));

            fetch('/myvet/app/controllers/citasMedicasController.php?action=guardar', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: res.message || 'Error al guardar la cita.'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la solicitud: ' + err
                    });
                });
        }

        // Cambiar Estado - AJAX POST a action=cambiarEstado
        function cambiarEstado(id, nuevoEstado) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('estado', nuevoEstado);

            fetch('/myvet/app/controllers/citasMedicasController.php?action=cambiarEstado', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado actualizado',
                            text: res.message || 'El estado de la cita ha sido modificado.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'Error al cambiar estado.'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cambiar el estado: ' + err
                    });
                });
        }
        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    </script>
    <script>
        async function cargarDoctores(vendedor_id = null) {
            const almacenId = $('#almacen_id_editar').val();
            const $select = $('#atendera');

            if (!$select.length) return;

            // Limpia las opciones directamente con jQuery para mantener sincronizado Select2
            $select.empty();

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacenId}`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();
                $select.empty();
                if (resultado.success && Array.isArray(resultado.data)) {
                    // Opción por defecto
                    $select.append(new Option('-- Seleccionar Doctor --', ''));

                    resultado.data.forEach(usuario => {
                        const opcion = new Option(usuario.nombre, usuario.id, false, false);
                        $select.append(opcion);
                    });

                    // Asigna el valor y notifica a Select2 del cambio
                    if (vendedor_id) {
                        $select.val(vendedor_id);
                    }
                    $select.trigger('change.select2');

                } else {
                    $select.append(new Option('No se pudieron cargar los usuarios', ''));
                }
            } catch (error) {
                $select.append(new Option('Error al cargar la lista', ''));
                console.error('Error al ejecutar cargarVendedores3:', error);
            }
        } async function cargarDoctoresFiltro(vendedor_id = null) {
            const almacenId = $('#almacen_id_editar').val();
            const $select = $('#doctor');

            if (!$select.length) return;

            // Limpia las opciones directamente con jQuery para mantener sincronizado Select2
            $select.empty();

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacenId}`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();
                $select.empty();
                if (resultado.success && Array.isArray(resultado.data)) {
                    // Opción por defecto
                    $select.append(new Option('-- Seleccionar Doctor --', ''));

                    resultado.data.forEach(usuario => {
                        const opcion = new Option(usuario.nombre, usuario.id, false, false);
                        $select.append(opcion);
                    });

                    // Asigna el valor y notifica a Select2 del cambio
                    if (vendedor_id) {
                        $select.val(vendedor_id);
                    }
                    $select.trigger('change.select2');

                } else {
                    $select.append(new Option('No se pudieron cargar los usuarios', ''));
                }
            } catch (error) {
                $select.append(new Option('Error al cargar la lista', ''));
                console.error('Error al ejecutar cargarVendedores3:', error);
            }
        }

        async function cargarPacientes() {
            console.log("cargo clientes");

            // Obtenemos el ID del almacén actual
            const almacenId = $('#almacen_id_editar').val();
            const select = document.getElementById('paciente_id');
            if (!select) return;

            // Limpiamos el select antes de poblarlo
            select.innerHTML = '<option value="">-- Seleccione un Paciente --</option>';

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${almacenId}`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();
                console.log(resultado);

                if (resultado.success && Array.isArray(resultado.data)) {

                    // FILTRADO: 
                    // 1. Conserva clientes cuyo nombre NO contenga "público en general" (clientes normales).
                    // 2. Para "público en general", solo conserva el que coincida con el almacenId actual.
                    const clientesFiltrados = resultado.data.filter(cliente => {
                        const nombreNorm = cliente.nombre_comercial.toLowerCase().trim();
                        const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                        if (esPublicoGeneral) {
                            // Revisa que coincida el ID del almacén (compara tanto número como string)
                            return cliente.almacen_id == almacenId;
                        }

                        // Si es un cliente regular, se muestra siempre
                        return true;
                    });
                    select.innerHTML = '<option value="">-- Seleccione un Paciente --</option>';
                    // Llenamos el select únicamente con la lista filtrada
                    clientesFiltrados.forEach(cliente => {
                        const opcion = document.createElement('option');
                        opcion.value = cliente.id;
                        opcion.textContent = `${cliente.nombre_comercial}`;
                        select.appendChild(opcion);
                    });

                } else {
                    select.innerHTML = '<option value="">No se pudieron cargar pacientes</option>';
                }
            } catch (error) {
                select.innerHTML = '<option value="">Error al cargar la lista</option>';
                console.error('Error al ejecutar cargar Pacientes:', error);
            }
        }
        function abrirConsultaDental(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/myvet/consultaDental';

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.value = id;

            form.appendChild(inputId);
            document.body.appendChild(form);

            // Al hacer submit, el navegador redirige a la nueva página en la misma pestaña
            form.submit();
        }
    </script>
</body>

</html>