<?php
/**
 * clientes_view.php
 * Vista de pacientes/clientes cargada vía AJAX desde action=listar.
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 = Admin global
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

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
       VARIABLES
       ========================================== */
        :root {
            --bg-main: #f0f2f5;
            --bg-card: rgba(255, 255, 255, 0.65);
            --bg-card-header: linear-gradient(135deg, rgba(255, 255, 255, .8) 0%, rgba(255, 255, 255, .4) 100%);
            --bg-input: rgba(255, 255, 255, 0.75);
            --bg-table-header: rgba(255, 255, 255, 0.5);
            --bg-modal-header: linear-gradient(135deg, rgba(255, 255, 255, .9) 0%, rgba(240, 242, 245, .8) 100%);
            --bg-modal-footer: rgba(245, 247, 250, 0.8);

            --border-color: rgba(0, 0, 0, 0.08);
            --border-color-soft: rgba(0, 0, 0, 0.05);
            --border-color-hover: rgba(0, 113, 227, 0.6);

            --text-main: #1d1d1f;
            --text-title: #000000;
            --text-muted: #6e6e73;

            --dental-accent: #0071e3;
            --dental-accent-hover: #0077ed;
            --dental-mint: #34c759;
            --dental-badge-bg: rgba(0, 113, 227, 0.08);
            --dental-btn-gradient: linear-gradient(135deg, rgba(0, 113, 227, .9) 0%, rgba(64, 156, 255, .9) 100%);
            --dental-btn-action-bg: rgba(0, 113, 227, 0.06);

            --shadow-card: 0 20px 40px rgba(0, 0, 0, 0.06), inset 0 0 0 1px rgba(255, 255, 255, 0.6);
            --shadow-header: 0 10px 30px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            --shadow-btn: 0 4px 14px rgba(0, 113, 227, 0.25);

            --backdrop-blur-heavy: blur(35px);
        }

        body.dark-theme,
        body.dark-mode,
        [data-bs-theme="dark"] body,
        html[data-bs-theme="dark"] body {
            --bg-main: #0b0b0d;
            --bg-card: rgba(30, 30, 35, 0.55);
            --bg-card-header: linear-gradient(135deg, rgba(45, 45, 52, .6) 0%, rgba(30, 30, 35, .6) 100%);
            --bg-input: rgba(45, 45, 52, 0.65);
            --bg-table-header: rgba(40, 40, 48, 0.5);
            --bg-modal-header: linear-gradient(135deg, rgba(38, 38, 45, .8) 0%, rgba(25, 25, 30, .8) 100%);
            --bg-modal-footer: rgba(22, 22, 26, 0.8);

            --border-color: rgba(255, 255, 255, 0.08);
            --border-color-soft: rgba(255, 255, 255, 0.05);
            --border-color-hover: rgba(10, 132, 255, 0.5);

            --text-main: #f5f5f7;
            --text-title: #ffffff;
            --text-muted: #98989d;

            --dental-accent: #0a84ff;
            --dental-accent-hover: #409cff;
            --dental-mint: #30d158;
            --dental-badge-bg: rgba(10, 132, 255, 0.15);
            --dental-btn-gradient: linear-gradient(135deg, rgba(10, 132, 255, .9) 0%, rgba(0, 98, 210, .9) 100%);
            --dental-btn-action-bg: rgba(10, 132, 255, 0.15);

            --shadow-card: 0 20px 40px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            --shadow-header: 0 10px 30px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            --shadow-btn: 0 4px 14px rgba(10, 132, 255, 0.35);
        }

        @media (prefers-color-scheme: dark) {
            :root:not([data-bs-theme="light"]) {
                --bg-main: #0b0b0d;
                --bg-card: rgba(30, 30, 35, 0.55);
                --bg-input: rgba(45, 45, 52, 0.65);
                --bg-table-header: rgba(40, 40, 48, 0.5);
                --border-color: rgba(255, 255, 255, 0.08);
                --border-color-soft: rgba(255, 255, 255, 0.05);
                --text-main: #f5f5f7;
                --text-title: #ffffff;
                --text-muted: #98989d;
                --dental-accent: #0a84ff;
            }
        }

        /* ==========================================
       BASE
       ========================================== */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Helvetica Neue", Helvetica, Arial, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: background-color .3s cubic-bezier(.4, 0, .2, 1), color .3s cubic-bezier(.4, 0, .2, 1);
            -webkit-font-smoothing: antialiased;
            background-image:
                radial-gradient(at 10% 20%, rgba(0, 113, 227, 0.08) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(52, 199, 89, 0.06) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .main-content {
            padding: 40px 24px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* ==========================================
       HEADER
       ========================================== */
        .clinic-header {
            background: var(--bg-card-header);
            backdrop-filter: var(--backdrop-blur-heavy);
            -webkit-backdrop-filter: var(--backdrop-blur-heavy);
            border: 1px solid var(--border-color);
            border-radius: 24px;
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

        /* ==========================================
       CARD
       ========================================== */
        .card-dental {
            background: var(--bg-card);
            backdrop-filter: var(--backdrop-blur-heavy);
            -webkit-backdrop-filter: var(--backdrop-blur-heavy);
            border: 1px solid var(--border-color) !important;
            border-radius: 24px;
            box-shadow: var(--shadow-card);
        }

        /* ==========================================
       INPUTS
       ========================================== */
        .form-control-dental,
        .form-select-dental {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color) !important;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 0.9rem;
            color: var(--text-main);
            transition: all .25s ease;
            box-shadow: none !important;
        }

        .form-control-dental:focus,
        .form-select-dental:focus {
            background-color: var(--bg-card);
            border-color: var(--dental-accent) !important;
            box-shadow: 0 0 0 4px var(--dental-badge-bg) !important;
            color: var(--text-main);
            outline: none;
        }

        .input-group-text {
            background-color: var(--bg-input) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-muted) !important;
        }

        .input-group>.input-group-text:first-child {
            border-right: 0 !important;
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }

        .input-group>.form-control-dental:last-child {
            border-left: 0 !important;
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }

        /* ==========================================
       BOTONES
       ========================================== */
        .btn-dental-primary {
            background: var(--dental-btn-gradient);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 22px;
            box-shadow: var(--shadow-btn);
            transition: all .2s ease;
        }

        .btn-dental-primary:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
            color: #fff;
        }

        .btn-dental-action {
            background: var(--dental-btn-action-bg);
            color: var(--dental-accent);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-dental-action:hover {
            background: var(--dental-accent);
            color: #fff;
            border-color: var(--dental-accent);
            box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
        }

        .btn-dental-excel {
            background: var(--bg-card);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 18px;
            transition: all .2s ease;
        }

        .btn-dental-excel:hover {
            background: rgba(52, 199, 89, 0.15);
            color: var(--dental-mint);
            border-color: rgba(52, 199, 89, 0.4);
        }

        /* ==========================================
       TABLA  ← AQUÍ ESTABA EL PROBLEMA
       ========================================== */
        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: transparent;
        }

        .table-dental {
            margin-bottom: 0 !important;
            color: var(--text-main);
            background: transparent;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table-dental thead th {
            background: var(--bg-table-header);
            color: var(--text-muted);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            padding: 16px;
            border-bottom: 1px solid var(--border-color) !important;
            border-top: 0 !important;
            border-left: 0 !important;
            border-right: 0 !important;
            white-space: nowrap;
        }

        .table-dental tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color-soft) !important;
            border-top: 0 !important;
            border-left: 0 !important;
            border-right: 0 !important;
            color: var(--text-main);
            font-size: 0.9rem;
            background: transparent;
        }

        .table-dental tbody tr:last-child td {
            border-bottom: 0 !important;
        }

        .table-dental tbody tr {
            transition: background-color .15s ease;
        }

        .table-dental tbody tr:hover {
            background-color: var(--dental-badge-bg);
        }

        /* Badges */
        .badge-rfc {
            background: var(--bg-input);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.78rem;
            display: inline-block;
        }

        .badge-sucursal {
            background: var(--dental-badge-bg);
            color: var(--dental-accent);
            border: 1px solid var(--border-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .form-check-input:checked {
            background-color: var(--dental-mint);
            border-color: var(--dental-mint);
        }

        /* ==========================================
       DATATABLES
       ========================================== */
        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--dental-accent);
            border-color: var(--dental-accent);
            color: #fff;
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

        /* ==========================================
       MODALES
       ========================================== */
        .modal-content {
            background-color: var(--bg-card) !important;
            backdrop-filter: var(--backdrop-blur-heavy);
            -webkit-backdrop-filter: var(--backdrop-blur-heavy);
            border: 1px solid var(--border-color) !important;
            border-radius: 20px !important;
            box-shadow: var(--shadow-card);
            color: var(--text-main);
            overflow: hidden;
        }

        .modal-header {
            background: var(--bg-modal-header);
            border-bottom: 1px solid var(--border-color) !important;
            padding: 18px 24px;
            color: var(--text-title);
        }

        .modal-title {
            font-weight: 700;
            color: var(--text-title);
        }

        .modal-body {
            padding: 24px;
            color: var(--text-main);
        }

        .modal-footer {
            background: var(--bg-modal-footer);
            border-top: 1px solid var(--border-color) !important;
            padding: 16px 24px;
            gap: 8px;
        }

        .modal-footer>* {
            margin: 0;
        }

        .modal-body .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .modal-body .form-control,
        .modal-body .form-select {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 10px;
            padding: 10px 14px;
        }

        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            background-color: var(--bg-card);
            border-color: var(--dental-accent);
            box-shadow: 0 0 0 4px var(--dental-badge-bg);
            color: var(--text-main);
        }

        /* ==========================================
       LOADER
       ========================================== */
        #loadingClientes {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        #loadingClientes .spinner {
            width: 42px;
            height: 42px;
            border: 3px solid var(--border-color);
            border-top-color: var(--dental-accent);
            border-radius: 50%;
            animation: spin .8s linear infinite;
            margin-bottom: 14px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>

    <main class="main-content">

        <!-- Header Principal -->
        <div
            class="clinic-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate__animated animate__fadeIn">
            <div>
                <div class="clinic-badge mb-2">
                    <i class="bi bi-hospital-fill"></i> MÓDULO CLÍNICO
                </div>
                <h2 class="fw-bold m-0" style="color: var(--text-title); letter-spacing: -0.02em;">Gestión de Pacientes
                </h2>
                <p class="text-muted mb-0 small">Directorio de expedientes, información fiscal y contactos de atención
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
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

        <!-- Tarjeta de Contenido -->
        <div class="card card-dental p-4 animate__animated animate__fadeInUp">

            <!-- Barra de Filtros -->
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
                            <?php foreach (($almacenes ?? []) as $alm): ?>
                                <option value="<?= (int) $alm['id'] ?>">📍 <?= htmlspecialchars($alm['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button
                            class="btn btn-dental-excel w-100 fw-semibold d-flex align-items-center justify-content-center gap-2"
                            onclick="limpiarFiltros()">
                            <i class="bi bi-arrow-counterclockwise"></i> REINICIAR FILTROS
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tabla con estado de carga -->
            <div id="loadingClientes">
                <div class="spinner"></div>
                <span>Cargando pacientes...</span>
            </div>

            <div class="table-responsive d-none" id="contenedorTabla">
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
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php require_once __DIR__ . '/clientes/clientesMedic.php'; ?>

    <script>
        // =========================================================
        // CONFIGURACIÓN INYECTADA DESDE PHP
        // =========================================================
        const ENDPOINT_LISTAR = '/myvet/misPacientesMedicina?action=listar';
        const ES_ADMIN_GLOBAL = <?= ($almacen_usuario == 0) ? 'true' : 'false' ?>;
        const ALMACEN_SESION = <?= (int) $almacen_usuario ?>;
        const MAPA_ALMACENES = <?= json_encode(
            array_column($almacenes ?? [], 'nombre', 'id'),
            JSON_UNESCAPED_UNICODE
        ) ?>;

        let tabla = null;
        let clientesCache = [];

        // Escapado seguro para HTML (previene XSS)
        const esc = (s) => $('<div>').text(s ?? '').html();

        // =========================================================
        // TOGGLE TEMA
        // =========================================================
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

        // =========================================================
        // RENDER DE CADA FILA
        // =========================================================
        function renderPaciente(row) {
            const nombre = esc(row.nombre_comercial || '');
            const razon = row.razon_social ? `<div class="text-muted small">${esc(row.razon_social)}</div>` : '';
            return `
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                        style="width: 40px; height: 40px; font-size: 0.9rem; background-color: var(--bg-input);
                               color: var(--dental-accent);">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color: var(--text-title);">${nombre}</div>
                        ${razon}
                    </div>
                </div>
            `;
        }

        function renderRFC(row) {
            return `<span class="badge-rfc">${esc(row.rfc || '')}</span>`;
        }

        function renderClinica(row) {
            const nombre = MAPA_ALMACENES[row.almacen_id] ?? 'Global';
            return `<span class="badge-sucursal"><i class="bi bi-geo-alt-fill me-1"></i>${esc(nombre)}</span>`;
        }

        function renderEstatus(row) {
            const id = parseInt(row.id, 10);
            const activo = parseInt(row.activo, 10) === 1;
            return `
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" ${activo ? 'checked' : ''}
                        onchange="cambiarEstado(${id}, this.checked ? 1 : 0)" id="switch_${id}">
                    <label class="form-check-label small fw-medium text-muted" for="switch_${id}">
                        ${activo ? 'Activo' : 'Inactivo'}
                    </label>
                </div>
            `;
        }

        function renderAcciones(row) {
            const id = parseInt(row.id, 10);
            const hoy = new Date();
            const hace1mes = new Date();
            hace1mes.setMonth(hace1mes.getMonth() - 1);
            const fmt = (d) => d.toISOString().split('T')[0];

            return `
                <div class="d-inline-flex align-items-center gap-2">
                    <a href="/myvet/consultaMedica?id=${id}" class="btn btn-dental-action" title="Nueva consulta">
                        <i class="bi bi-tooth"></i><span>Nueva consulta</span>
                    </a>
                    <a href="/myvet/app/controllers/historialMedicoController.php?id=${id}&fecha_inicio=${fmt(hace1mes)}&fecha_fin=${fmt(hoy)}"
                        class="btn btn-dental-action" title="Expediente Médico">
                        <i class="bi bi-journal-medical"></i><span>EXPEDIENTE</span>
                    </a>
                    <button class="btn btn-dental-action" onclick="editarCliente(${id})" title="Editar">
                        <i class="bi bi-pencil-square"></i><span>Editar</span>
                    </button>
                </div>
            `;
        }

        // =========================================================
        // INICIALIZAR DATATABLE + CARGA AJAX
        // =========================================================
        $(document).ready(function () {

            // -------- DataTable con filas vacías al inicio --------
            tabla = $('#tablaClientes').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                "dom": 'rt<"row mt-3 px-3 align-items-center"<"col-sm-12 col-md-5 small text-muted"i><"col-sm-12 col-md-7"p>>',
                "pageLength": 15,
                "order": [[0, 'asc']],
                "columns": [
                    { "data": null, "render": (d, t, row) => renderPaciente(row) },
                    { "data": null, "render": (d, t, row) => renderRFC(row) },
                    { "data": null, "visible": ES_ADMIN_GLOBAL, "render": (d, t, row) => renderClinica(row) },
                    { "data": null, "orderable": false, "render": (d, t, row) => renderEstatus(row) },
                    { "data": null, "orderable": false, "render": (d, t, row) => renderAcciones(row) }
                ],
                "columnDefs": [
                    { "targets": [4], "orderable": false },
                    { "targets": [2], "visible": ES_ADMIN_GLOBAL }
                ]
            });

            // -------- Cargar datos vía AJAX --------
            cargarClientes();

            // -------- Búsqueda global --------
            $('#busquedaCliente').on('keyup', function () { tabla.search(this.value).draw(); });

            // -------- Filtro por almacén (solo admin) --------
            $('#filtroAlmacenVista').on('change', function () {
                const val = $(this).val();
                $.fn.dataTable.ext.search.pop();
                if (val !== "") {
                    $.fn.dataTable.ext.search.push(function (s, d, i) {
                        return String(clientesCache[i]?.almacen_id) === String(val);
                    });
                }
                tabla.draw();
            });
        });

        // =========================================================
        // PETICIÓN AL CONTROLLER
        // =========================================================
        async function cargarClientes() {
            const $loading = $('#loadingClientes');
            const $tabla = $('#contenedorTabla');

            $loading.removeClass('d-none');
            $tabla.addClass('d-none');

            try {
                // Construimos la URL
                let url = ENDPOINT_LISTAR;

                // Si el admin tiene un filtro seleccionado, lo mandamos como ?id=
                const filtroAlmacen = $('#filtroAlmacenVista').val();
                if (ES_ADMIN_GLOBAL && filtroAlmacen) {
                    url += '&id=' + encodeURIComponent(filtroAlmacen);
                }

                const respuesta = await fetch(url, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });

                if (!respuesta.ok) {
                    throw new Error(`Error HTTP ${respuesta.status}`);
                }

                const json = await respuesta.json();

                if (json.status !== 'success' || !Array.isArray(json.clientes)) {
                    throw new Error(json.message || 'Respuesta inválida del servidor');
                }

                clientesCache = json.clientes;

                // Limpiar y llenar DataTable
                tabla.clear();
                if (clientesCache.length > 0) {
                    tabla.rows.add(clientesCache).draw();
                } else {
                    tabla.draw();
                }

                // Mostrar tabla, ocultar loader
                $loading.addClass('d-none');
                $tabla.removeClass('d-none');

            } catch (err) {
                console.error('[cargarClientes]', err);
                $loading.html(`
                    <div class="text-center">
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-danger d-block mb-2"></i>
                        <div class="fw-semibold">No se pudieron cargar los pacientes</div>
                        <div class="small text-muted mb-3">${esc(err.message)}</div>
                        <button class="btn btn-dental-primary" onclick="cargarClientes()">
                            <i class="bi bi-arrow-clockwise"></i> Reintentar
                        </button>
                    </div>
                `);
            }
        }

        // =========================================================
        // FILTROS
        // =========================================================
        function limpiarFiltros() {
            $('#busquedaCliente').val('');
            $('#filtroAlmacenVista').val('');
            $.fn.dataTable.ext.search.pop();
            tabla.search('').draw();
        }

        function abrirEnlaceOdontologico(clienteId) {
            const url = `/myvet/app/controllers/odontogramaController.php?action=expediente&cliente_id=${clienteId}`;
            window.open(url, '_blank');
        }

        // =========================================================
        // EXPORTAR CSV
        // =========================================================
        async function exportarClientesCSV() {
            const almacenId = $('#filtroAlmacenVista').val();

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${encodeURIComponent(almacenId)}`;
                const respuesta = await fetch(url);
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

                const headers = ["ID Cliente", "Nombre Comercial", "Razón Social", "RFC", "Teléfono", "Email", "Almacén ID"];
                const escapeCSV = (str) => {
                    if (str === null || str === undefined) return '""';
                    const val = String(str).replace(/"/g, '""');
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
                const urlBlob = URL.createObjectURL(blob);
                const link = document.createElement('a');
                const hoy = new Date().toISOString().split('T')[0];
                link.setAttribute('href', urlBlob);
                link.setAttribute('download', `Reporte_Pacientes_${hoy}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(urlBlob);

            } catch (error) {
                console.error('Error al exportar clientes a CSV:', error);
                alert('Ocurrió un error al procesar la exportación de clientes.');
            }
        }

        // Forzar mayúsculas en inputs de texto
        document.querySelectorAll('input[type="text"], textarea').forEach(el => {
            el.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>

</html>