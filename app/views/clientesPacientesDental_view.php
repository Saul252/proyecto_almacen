<?php
/**
 * clientes_view.php
 * Vista de administración de pacientes/clientes: Filtros, CRUD por Modales y AJAX.
 * Estilo: Dental / Clínico Híbrido (Claro por defecto / Oscuro dinámico)
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 es Admin
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes y Clientes | Clínica Dental</title>

    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        /* ==========================================
            VARIABLES MODO CLARO (ESTILO APPLE / NEUMÓRFICO SUTIL)
            ========================================== */
        :root {
            --bg-main: #f5f5f7;
            --bg-card: rgba(255, 255, 255, 0.82);
            --bg-card-header: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(245, 245, 247, 0.8) 100%);
            --bg-input: rgba(255, 255, 255, 0.9);
            --bg-table-header: rgba(242, 242, 247, 0.7);
            --bg-modal-header: linear-gradient(135deg, #e5e5ea 0%, #d1d1d6 100%);
            --bg-modal-footer: #f2f2f7;

            --border-color: rgba(0, 0, 0, 0.06);
            --border-color-hover: rgba(0, 113, 227, 0.4);

            --text-main: #1d1d1f;
            --text-title: #000000;
            --text-muted: #86868b;

            --dental-accent: #0071e3;
            --dental-accent-hover: #0077ed;
            --dental-mint: #34c759;
            --dental-badge-bg: rgba(0, 113, 227, 0.06);
            --dental-btn-gradient: linear-gradient(135deg, #0071e3 0%, #409cff 100%);
            --dental-btn-action-bg: rgba(0, 113, 227, 0.05);

            --shadow-card: 0 20px 40px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            --shadow-header: 0 10px 30px rgba(0, 0, 0, 0.03);
            --shadow-btn: 0 4px 14px rgba(0, 113, 227, 0.25);

            --backdrop-blur: blur(20px);
        }

        /* ==========================================
            VARIABLES MODO OSCURO (ESTILO APPLE DARK)
            ========================================== */
        body.dark-theme,
        body.dark-mode,
        [data-bs-theme="dark"] body,
        html[data-bs-theme="dark"] body {
            --bg-main: #000000;
            --bg-card: rgba(28, 28, 30, 0.75);
            --bg-card-header: linear-gradient(135deg, rgba(44, 44, 46, 0.8) 0%, rgba(28, 28, 30, 0.8) 100%);
            --bg-input: rgba(44, 44, 46, 0.85);
            --bg-table-header: rgba(44, 44, 46, 0.6);
            --bg-modal-header: linear-gradient(135deg, #1c1c1e 0%, #2c2c2e 100%);
            --bg-modal-footer: #1c1c1e;

            --border-color: rgba(255, 255, 255, 0.1);
            --border-color-hover: rgba(10, 132, 255, 0.4);

            --text-main: #f5f5f7;
            --text-title: #ffffff;
            --text-muted: #98989d;

            --dental-accent: #0a84ff;
            --dental-accent-hover: #409cff;
            --dental-mint: #30d158;
            --dental-badge-bg: rgba(10, 132, 255, 0.12);
            --dental-btn-gradient: linear-gradient(135deg, #0a84ff 0%, #0062d2 100%);
            --dental-btn-action-bg: rgba(10, 132, 255, 0.12);

            --shadow-card: 0 20px 40px rgba(0, 0, 0, 0.4);
            --shadow-header: 0 10px 30px rgba(0, 0, 0, 0.5);
            --shadow-btn: 0 4px 14px rgba(10, 132, 255, 0.3);

            --backdrop-blur: blur(25px);
        }

        /* Detección automática de preferencia del sistema */
        @media (prefers-color-scheme: dark) {
            :root:not([data-bs-theme="light"]) {
                --bg-main: #000000;
                --bg-card: rgba(28, 28, 30, 0.75);
                --bg-card-header: linear-gradient(135deg, rgba(44, 44, 46, 0.8) 0%, rgba(28, 28, 30, 0.8) 100%);
                --bg-input: rgba(44, 44, 46, 0.85);
                --bg-table-header: rgba(44, 44, 46, 0.6);
                --bg-modal-header: linear-gradient(135deg, #1c1c1e 0%, #2c2c2e 100%);
                --bg-modal-footer: #1c1c1e;
                --border-color: rgba(255, 255, 255, 0.1);
                --text-main: #f5f5f7;
                --text-title: #ffffff;
                --text-muted: #98989d;
                --dental-accent: #0a84ff;
                --dental-badge-bg: rgba(10, 132, 255, 0.12);
            }
        }

        /* Base Body con tipografía limpia estilo SF Pro / Inter */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1), color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            -webkit-font-smoothing: antialiased;
        }

        .main-content {
            padding: 40px 24px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Encabezado Clínico con Efecto Glassmorphism */
        .clinic-header {
            background: var(--bg-card-header);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px 30px;
            box-shadow: var(--shadow-header);
        }

        .clinic-badge {
            background: var(--dental-badge-bg);
            color: var(--dental-accent);
            border: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 0.72rem;
            letter-spacing: 0.06em;
            padding: 6px 12px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Tarjeta Contenedora Principal con Transparencia */
        .card-dental {
            background: var(--bg-card);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--border-color);
            border-radius: 22px;
            box-shadow: var(--shadow-card);
        }

        /* Form Controls Estilo Apple */
        .form-control-dental,
        .form-select-dental {
            background-color: var(--bg-input);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 0.9rem;
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        .form-control-dental:focus,
        .form-select-dental:focus {
            background-color: var(--bg-card);
            border-color: var(--dental-accent);
            box-shadow: 0 0 0 4px var(--dental-badge-bg);
            color: var(--text-main);
            outline: none;
        }

        /* Botones Pulidos estilo iOS / macOS */
        .btn-dental-primary {
            background: var(--dental-btn-gradient);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 22px;
            box-shadow: var(--shadow-btn);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-dental-primary:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            color: #ffffff;
        }

        .btn-dental-action {
            background: var(--dental-btn-action-bg);
            color: var(--dental-accent);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-dental-action:hover {
            background: var(--dental-accent);
            color: #ffffff;
            border-color: var(--dental-accent);
        }

        .btn-dental-excel {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 18px;
            transition: all 0.2s ease;
        }

        .btn-dental-excel:hover {
            background: rgba(52, 199, 89, 0.12);
            color: var(--dental-mint);
            border-color: rgba(52, 199, 89, 0.3);
        }

        /* Tablas con Transparencias Limpias */
        .table-dental {
            margin-bottom: 0;
            color: var(--text-main);
        }

        .table-dental thead th {
            background: var(--bg-table-header);
            backdrop-filter: blur(10px);
            color: var(--text-muted);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-dental tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.9rem;
            background: transparent;
        }

        .badge-rfc {
            background: var(--bg-input);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.78rem;
        }

        .badge-sucursal {
            background: var(--dental-badge-bg);
            color: var(--dental-accent);
            border: 1px solid var(--border-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .form-check-input:checked {
            background-color: var(--dental-mint);
            border-color: var(--dental-mint);
        }

        /* DataTables Personalización */
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--dental-accent);
            border-color: var(--dental-accent);
            color: #ffffff;
            border-radius: 8px;
        }

        .dataTables_wrapper .pagination .page-link {
            background-color: var(--bg-input);
            border-color: var(--border-color);
            border-radius: 8px;
            margin: 0 2px;
            color: var(--text-main);
        }

        .dataTables_wrapper .dataTables_info {
            color: var(--text-muted) !important;
        }

        /* Modales Estilo Sheet / Apple */
        .modal-dental-content {
            background-color: var(--bg-card);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-radius: 22px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }

        .modal-dental-header {
            background: var(--bg-modal-header);
            color: var(--text-title);
            padding: 20px 28px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer-dental {
            background-color: var(--bg-modal-footer);
            border-top: 1px solid var(--border-color);
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>

    <main class="main-content">

        <!-- Header Principal de la Clínica -->
        <div
            class="clinic-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate__animated animate__fadeIn">
            <div>
                <div class="clinic-badge mb-2">
                    <i class="bi bi-hospital-fill"></i> MÓDULO DENTAL & CLÍNICO
                </div>
                <h2 class="fw-bold m-0" style="color: var(--text-title); letter-spacing: -0.02em;">Gestión de Pacientes
                </h2>
                <p class="text-muted mb-0 small">Directorio de expedientes, información fiscal y contactos de atención
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Botón de Conmutación Manual de Tema (Opcional) -->
                <button type="button" class="btn btn-dental-excel d-inline-flex align-items-center gap-2"
                    onclick="toggleTheme()" title="Cambiar Tema">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>

                <button type="button" class="btn btn-dental-excel d-inline-flex align-items-center gap-2 shadow-sm"
                    onclick="exportarClientesCSV()">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-success fs-5"></i>
                    <span>Exportar Data</span>
                </button>
                <button class="btn btn-dental-primary d-inline-flex align-items-center gap-2" onclick="nuevoCliente()">
                    <i class="bi bi-person-plus-fill fs-5"></i>
                    <span>NUEVO PACIENTE</span>
                </button>
            </div>
        </div>

        <!-- Tarjeta de Contenido y Tablas -->
        <div class="card card-dental p-4 animate__animated animate__fadeInUp">

            <!-- Barra de Herramientas / Filtros -->
            <div class="row mb-4 g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text border-end-0 rounded-start-3 text-muted ps-3"
                            style="background-color: var(--bg-input); border-color: var(--border-color);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="busquedaCliente"
                            class="form-control form-control-dental border-start-0 rounded-start-0"
                            placeholder="Buscar paciente por nombre, RFC o correo...">
                    </div>
                </div>

                <?php if ($almacen_usuario == 0): ?>
                    <div class="col-md-4">
                        <select id="filtroAlmacenVista" class="form-select form-select-dental">
                            <option value="">🌐 Todas las Clínicas / Sucursales</option>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>">📍 <?= htmlspecialchars($alm['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button
                            class="btn btn-outline-secondary w-100 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2"
                            style="border-color: var(--border-color); color: var(--text-muted);" onclick="limpiarFiltros()">
                            <i class="bi bi-arrow-counterclockwise"></i> REINICIAR FILTROS
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tabla de Clientes / Pacientes -->
            <div class="table-responsive">
                <table id="tablaClientes" class="table table-dental align-middle w-100">
                    <thead>
                        <tr>
                            <th class="ps-3">Paciente / Razón Social</th>
                            <th>Identificación (RFC)</th>
                            <th>Clínica</th>
                            <th>Estatus</th>
                            <th class="text-end pe-3">Expediente & Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                            <tr class="fila-cliente" data-almacen-id="<?= $c['almacen_id'] ?>">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 40px; height: 40px; font-size: 0.9rem; background-color: var(--bg-input); color: var(--dental-accent); border: 1px solid var(--border-color);">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: var(--text-title);">
                                                <?= htmlspecialchars($c['nombre_comercial']) ?>
                                            </div>
                                            <?php if (!empty($c['razon_social'])): ?>
                                                <div class="text-muted small"><?= htmlspecialchars($c['razon_social']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-rfc">
                                        <?= htmlspecialchars($c['rfc']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-sucursal">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        <?php
                                        $nombreAlmacen = 'Global';
                                        foreach ($almacenes as $alm) {
                                            if ($alm['id'] == $c['almacen_id']) {
                                                $nombreAlmacen = $alm['nombre'];
                                                break;
                                            }
                                        }
                                        echo htmlspecialchars($nombreAlmacen);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" <?= $c['activo'] ? 'checked' : '' ?>
                                            onchange="cambiarEstado(<?= $c['id'] ?>, this.checked ? 1 : 0)"
                                            id="switch_<?= $c['id'] ?>">
                                        <label class="form-check-label small fw-medium text-muted"
                                            for="switch_<?= $c['id'] ?>">
                                            <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </label>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="/myvet/consultaDental?id=<?= $c['id'] ?>"
                                        class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm rounded-pill px-3 py-1"
                                        title="Abrir Expediente Dental">
                                        <i class="bi bi-tooth fs-6"></i>
                                        <span class="fw-medium">Nueva consulta</span>
                                    </a>
                                    <div class="d-inline-flex gap-2">
                                        <a href="/myvet/app/controllers/historialDentalController.php?id=<?= $c['id'] ?>&fecha_inicio=<?= date('Y-m-d', strtotime('-1 month')) ?>&fecha_fin=<?= date('Y-m-d') ?>"
                                            class="btn-dental-action" title="Abrir Expediente Dental">
                                            <i class="bi bi-journal-medical"></i>
                                            <span>EXPEDIENTE</span>
                                        </a>

                                        <button class="btn btn-outline-secondary btn-sm rounded-2 p-2"
                                            style="border-color: var(--border-color); color: var(--dental-accent);"
                                            onclick="editarCliente(<?= $c['id'] ?>)" title="Editar Datos">
                                            <i class="bi bi-pencil-square"></i>
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

    <!-- Modal CRUD Paciente -->

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php require_once __DIR__ . '/clientes/clientesMedic.php'; ?>

    <script>
        let tabla;

        // Función manual para alternar tema (Claro / Oscuro)
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('themeIcon');

            if (body.classList.contains('dark-theme') || document.documentElement.getAttribute('data-bs-theme') === 'dark') {
                body.classList.remove('dark-theme');
                document.documentElement.setAttribute('data-bs-theme', 'light');
                icon.className = 'bi bi-moon-stars-fill';
            } else {
                body.classList.add('dark-theme');
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                icon.className = 'bi bi-sun-fill';
            }
        }

        $(document).ready(function () {
            tabla = $('#tablaClientes').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                "dom": 'rt<"row mt-3 px-3 align-items-center"<"col-sm-12 col-md-5 small text-muted"i><"col-sm-12 col-md-7"p>>',
                "pageLength": 15,
                "order": [[0, 'asc']],
                "columnDefs": [
                    { "targets": [4], "orderable": false },
                    { "targets": [2], "visible": <?= ($almacen_usuario == 0) ? 'true' : 'false' ?> }
                ]
            });

            $('#busquedaCliente').on('keyup', function () { tabla.search(this.value).draw(); });

            $('#filtroAlmacenVista').on('change', function () {
                const val = $(this).val();
                $.fn.dataTable.ext.search.pop();
                if (val !== "") {
                    $.fn.dataTable.ext.search.push(function (s, d, i) {
                        return $(tabla.row(i).node()).attr('data-almacen-id') == val;
                    });
                }
                tabla.draw();
            });
        });

        function abrirEnlaceOdontologico(clienteId) {
            const urlOdontologica = `/myvet/app/controllers/odontogramaController.php?action=expediente&cliente_id=${clienteId}`;
            window.open(urlOdontologica, '_blank');
        }

        function limpiarFiltros() {
            $('#busquedaCliente').val('');
            $('#filtroAlmacenVista').val('');
            $.fn.dataTable.ext.search.pop();
            tabla.search('').draw();
        }


        async function cambiarEstado(id, estado) {
            const fd = new FormData();
            fd.append('id', id);
            fd.append('estado', estado);
            fetch('/myvet/app/controllers/clientesPacientesDental.php?action=cambiarEstado', { method: 'POST', body: fd });
        }

        async function exportarClientesCSV() {
            const almacenId = $('#filtroAlmacenVista').val();

            try {
                const url2 = `/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${almacenId}`;
                const respuesta = await fetch(url2);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();

                if (!resultado.success || !Array.isArray(resultado.data) || resultado.data.length === 0) {
                    alert("No hay registros de clientes para exportar.");
                    return;
                }

                const clientesFiltrados = resultado.data.filter(cliente => {
                    const nombreNorm = (cliente.nombre_comercial || '').toLowerCase().trim();
                    const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                    if (esPublicoGeneral) {
                        return cliente.almacen_id == almacenId;
                    }

                    return true;
                });

                if (clientesFiltrados.length === 0) {
                    alert("No se encontraron clientes disponibles para exportar con las condiciones actuales.");
                    return;
                }

                const headers = [
                    "ID Cliente",
                    "Nombre Comercial",
                    "Razón Social",
                    "RFC",
                    "Teléfono",
                    "Email",
                    "Almacén ID"
                ];

                const escapeCSV = (str) => {
                    if (str === null || str === undefined) return '""';
                    let val = String(str).replace(/"/g, '""');
                    return `"${val}"`;
                };

                const rows = clientesFiltrados.map(c => [
                    escapeCSV(c.id),
                    escapeCSV(c.nombre_comercial || 'Sin nombre'),
                    escapeCSV(c.razon_social || c.nombre_comercial || ''),
                    escapeCSV(c.rfc || 'XAXX010101000'),
                    escapeCSV(c.telefono || 'N/A'),
                    escapeCSV(c.email || 'N/A'),
                    escapeCSV(c.almacen_id || '')
                ].join(','));

                const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url3 = URL.createObjectURL(blob);
                const link = document.createElement('a');

                const hoy = new Date().toISOString().split('T')[0];
                link.setAttribute('href', url3);
                link.setAttribute('download', `Reporte_Pacientes_${hoy}.csv`);
                document.body.appendChild(link);

                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url3);

            } catch (error) {
                console.error('Error al exportar clientes a CSV:', error);
                alert('Ocurrió un error al procesar la exportación de clientes.');
            }
        }

        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>

</html>