<!DOCTYPE html>
<html lang="es">
<?php
// PHP recibe el ID directamente de la URL al cargar la página
$idex = $_GET['id'] ?? null;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente: <?= htmlspecialchars($cliente['nombre_comercial'] ?? 'Paciente') ?> | CF System</title>
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --app-font: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;

            /* 🟢 TEMA CLARO CLÍNICO */
            --main-bg: #f8fafc;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-border-subtle: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --input-bg: #f8fafc;
            --hover-row: #f8fafc;
            --accent-blue: #0284c7;
            --accent-blue-subtle: #e0f2fe;
            --accent-glow: rgba(2, 132, 199, 0.12);
            --tag-bg: #f1f5f9;
            --cal-bg: #f1f5f9;
        }

        /* 🌙 TEMA OSCURO MÉDICO */
        [data-bs-theme="dark"] {
            --main-bg: #0b1120;
            --card-bg: #151f32;
            --card-border: #24324d;
            --card-border-subtle: #1c283f;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: #0b1120;
            --hover-row: rgba(255, 255, 255, 0.02);
            --accent-blue: #38bdf8;
            --accent-blue-subtle: rgba(56, 189, 248, 0.12);
            --accent-glow: rgba(56, 189, 248, 0.18);
            --tag-bg: #1e293b;
            --cal-bg: #1e293b;
        }

        body {
            background-color: var(--main-bg);
            color: var(--text-main);
            font-family: var(--app-font);
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* --- DOSSIER HEADER DEL PACIENTE --- */
        .patient-hero-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.04);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .patient-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #6366f1 100%);
        }

        .patient-avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
            flex-shrink: 0;
        }

        .patient-badge-chip {
            font-size: 0.72rem;
            font-weight: 600;
            background-color: var(--tag-bg);
            color: var(--text-muted);
            border: 1px solid var(--card-border);
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* --- FILTRO DE FECHAS ESTILO PÍLDORA iOS --- */
        .filter-pill-container {
            background-color: var(--input-bg);
            border-radius: 9999px;
            padding: 4px 14px;
            border: 1px solid var(--card-border);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .filter-pill-container:focus-within {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .filter-pill-container input[type="date"] {
            color: var(--text-main) !important;
            border: none;
            outline: none;
            font-size: 0.82rem;
            background: transparent;
            font-weight: 600;
        }

        .filter-pill-container input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.6;
            cursor: pointer;
        }

        /* --- TARJETA PRINCIPAL DEL EXPEDIENTE --- */
        .main-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 10px 30px -8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* --- TABLA CLÍNICA --- */
        .table-custom {
            margin-bottom: 0;
            color: var(--text-main);
        }

        .table-custom thead th {
            background-color: var(--card-bg);
            color: var(--text-muted);
            font-size: 0.70rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--card-border);
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--card-border-subtle);
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .table-custom tbody tr {
            transition: background-color 0.15s ease;
        }

        .table-custom tbody tr:hover {
            background-color: var(--hover-row);
        }

        /* --- CALENDARIO CLÍNICO DE CADA CONSULTA --- */
        .cal-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--cal-bg);
            border: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            line-height: 1;
        }

        .cal-badge .cal-day {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .cal-badge .cal-month {
            font-size: 0.60rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--accent-blue);
            letter-spacing: 0.5px;
            margin-top: 1px;
        }

        /* --- CHIP DEL MÉDICO / ATENDIÓ --- */
        .doctor-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            background: var(--tag-bg);
            border: 1px solid var(--card-border);
            font-size: 0.80rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .doctor-avatar-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--accent-blue-subtle);
            color: var(--accent-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
        }

        /* --- PÍLDORA DE MOTIVO DE CONSULTA --- */
        .triage-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background-color: var(--accent-blue-subtle);
            color: var(--accent-blue);
            font-weight: 600;
            font-size: 0.76rem;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
        }

        /* --- CONTENEDOR DE DIAGNÓSTICO CLÍNICO --- */
        .diag-box {
            border-left: 3px solid var(--accent-blue);
            padding-left: 8px;
            max-width: 280px;
        }

        .diag-text {
            color: var(--text-main);
            font-weight: 600;
            font-size: 0.82rem;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* --- DROPDOWN DE DOCUMENTOS --- */
        .dropdown-menu-custom {
            min-width: 300px;
            border-radius: 16px;
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.15);
        }

        .doc-item {
            border-radius: 10px;
            transition: background-color 0.15s ease;
        }

        .doc-item:hover {
            background-color: var(--hover-row);
        }

        .btn-action-view {
            background: var(--tag-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .btn-action-view:hover {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--accent-glow);
        }
    </style>

    <?php
    date_default_timezone_set('America/Mexico_City');
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');

    // Iniciales del paciente para el avatar
    $nombrePaciente = $cliente['nombre_comercial'] ?? 'Cliente';
    $iniciales = mb_strtoupper(mb_substr($nombrePaciente, 0, 2, 'UTF-8'));
    ?>

    <!-- Render Layout Superior -->
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual ?? '');
    } ?>

    <style>
        /* ========================================================= */
        /*  REGLAS DE OPTIMIZACIÓN MÓVIL Y RESPONSIVA                */
        /* ========================================================= */

        /* En computadoras y tablets grandes (>= 768px) */

        .mobile-card-actions {
            text-align: right;

        }

        /* En teléfonos móviles (< 768px) */
    </style>

    <div class="container-fluid px-2 px-sm-3 px-md-4" style="padding-top: 20px;">

        <!-- ========================================== -->
        <!--  1. FICHA SUPERIOR DEL PACIENTE (HERO DOSSIER) -->
        <!-- ========================================== -->
        <div class="patient-hero-card">
            <div
                class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">

                <!-- Datos del Paciente -->
                <div class="d-flex align-items-center gap-2.5 gap-sm-3 w-100 w-lg-auto">
                    <div class="patient-avatar flex-shrink-0">
                        <?= $iniciales ?>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center gap-1.5 gap-sm-2 mb-1 flex-wrap">
                            <h4 class="fw-bold mb-0 text-capitalize patient-title-text text-truncate"
                                style="letter-spacing: -0.3px; max-width: 240px;">
                                <?= htmlspecialchars($nombrePaciente) ?>
                            </h4>
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-bold"
                                style="font-size: 0.68rem;">
                                <i class="bi bi-shield-check me-1"></i> Activo
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="patient-badge-chip">
                                <i class="bi bi-telephone text-primary"></i>
                                <?= htmlspecialchars($cliente['telefono'] ?? 'Sin teléfono') ?>
                            </span>
                            <?php if (!empty($cliente['rfc'])): ?>
                                <span class="patient-badge-chip d-none d-sm-inline-flex">
                                    <i class="bi bi-person-vcard text-secondary"></i>
                                    <?= htmlspecialchars($cliente['rfc']) ?>
                                </span>
                            <?php endif; ?>
                            <span class="patient-badge-chip">
                                <i class="bi bi-journal-medical text-info"></i> ID: #
                                <?= str_pad($cliente['id'] ?? 1, 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Filtro de Fechas y Botón (100% Adaptable en Celular) -->
                <div
                    class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-lg-auto">
                    <div class="filter-pill-container shadow-sm">
                        <span class="text-muted fw-bold me-1" style="font-size: 0.68rem;">RANGO:</span>
                        <input type="date" id="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>">
                        <span class="text-muted opacity-25 px-1">—</span>
                        <input type="date" id="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>">
                        <button class="btn btn-sm btn-link text-primary p-0 ms-1 text-decoration-none"
                            onclick="filtrarExpediente()" title="Aplicar Filtro">
                            <i class="bi bi-arrow-clockwise fs-6"></i>
                        </button>
                    </div>

                    <button
                        class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm fw-semibold btn-filter-mobile"
                        onclick="filtrarExpediente()">
                        <i class="bi bi-funnel-fill"></i> Filtrar
                    </button>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!--  2. REGISTRO / EXPEDIENTE DE CONSULTAS     -->
        <!-- ========================================== -->
        <main class="mb-5">
            <div class="main-card">

                <!-- Barra de Control Superior -->
                <div class="px-3 px-md-4 py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2"
                    style="border-color: var(--card-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-1.5 p-md-2 rounded-circle"
                            style="background: var(--accent-blue-subtle); color: var(--accent-blue);">
                            <i class="bi bi-clipboard2-pulse-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-main" style="font-size: 0.92rem;">Historial Clínico</h6>
                            <small class="text-muted d-none d-sm-block" style="font-size: 0.72rem;">Registro secuencial
                                de exploraciones y diagnósticos</small>
                        </div>
                    </div>

                    <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold"
                        style="background: var(--tag-bg); color: var(--text-main); border: 1px solid var(--card-border); font-size: 0.72rem;">
                        <i class="bi bi-collection me-1 text-primary"></i>
                        <?= count($historialCompleto ?? []) ?> consulta(s)
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">

                    <!-- Filtro por tipo -->
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2" type="button"
                            data-bs-toggle="dropdown" style="
                background: var(--tag-bg);
                color: var(--text-main);
                border: 1px solid var(--card-border);
            " id="btnFiltroTipo">

                            <i class="bi bi-funnel text-primary"></i>

                            <span id="textoFiltroTipo">
                                Todas
                            </span>

                            <i class="bi bi-chevron-down small text-muted"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="min-width: 190px;">

                            <li>
                                <button type="button" class="dropdown-item filtro-tipo active" data-tipo="todos">

                                    <i class="bi bi-collection me-2"></i>
                                    Todas las consultas

                                </button>
                            </li>

                            <li>
                                <button type="button" class="dropdown-item filtro-tipo" data-tipo="medica">

                                    <i class="bi bi-heart-pulse me-2"></i>
                                    Consultas médicas

                                </button>
                            </li>

                            <li>
                                <button type="button" class="dropdown-item filtro-tipo" data-tipo="dental">

                                    <i class="bi bi-emoji-smile me-2"></i>
                                    Consultas dentales

                                </button>
                            </li>

                        </ul>
                    </div>

                </div>
                <!-- Tabla en Computadora / Lista de Tarjetas en Móvil -->
                <div class="table-responsive p-2 p-md-0">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 170px;">Fecha y Hora</th>
                                <th scope="col" style="width: 190px;">Tipo</th>
                                <th scope="col" style="width: 190px;">Médico Tratante</th>
                                <th scope="col" style="width: 220px;">Motivo Clínico</th>
                                <th scope="col">Diagnóstico Principal</th>
                                <th scope="col" class="text-center" style="width: 140px;">Evidencias</th>
                                <th scope="col" class="text-end" style="width: 110px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($historialCompleto)): ?>

                                <?php foreach ($historialCompleto as $ex): ?>

                                    <?php
                                    $timeObj = strtotime($ex['fecha_consulta']);

                                    $dia = date('d', $timeObj);
                                    $mes = date('M', $timeObj);
                                    $anio = date('Y', $timeObj);
                                    $hora = date('H:i', $timeObj);

                                    $tipo = $ex['_tipo'];

                                    $esDental = ($tipo === 'dental');

                                    $funcionDetalle = $esDental
                                        ? 'cargarDetalleHistorialDental'
                                        : 'cargarDetalleHistorial';
                                    ?>

                                    <tr data-tipo="<?= htmlspecialchars(strtolower(trim($ex['tipo'] ?? ''))) ?>">

                                        <!-- FECHA -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="cal-badge">
                                                    <span class="cal-day">
                                                        <?= $dia ?>
                                                    </span>
                                                    <span class="cal-month">
                                                        <?= $mes ?>
                                                    </span>
                                                </div>

                                                <div>
                                                    <span class="fw-bold text-main d-block" style="font-size:.82rem;">
                                                        <?= $anio ?>
                                                    </span>

                                                    <span class="text-muted font-monospace" style="font-size:.71rem;">
                                                        <i class="bi bi-clock me-1"></i>
                                                        <?= $hora ?> hrs
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <!--TIPO -->
                                        <td>
                                            <div class="doctor-chip">

                                                <div class="doctor-avatar-circle">
                                                    <i class="bi bi-clipboard2-pulse-fill" style="font-size: .85rem;"></i>
                                                </div>

                                                <span class="text-truncate" style="max-width:160px;"
                                                    title="<?= htmlspecialchars($ex['tipo'] ?? '') ?>">

                                                    <?= htmlspecialchars($ex['tipo'] ?? 'Sin asignar') ?>

                                                </span>

                                            </div>
                                        </td>
                                        <!-- MÉDICO -->
                                        <td>
                                            <div class="doctor-chip">

                                                <div class="doctor-avatar-circle">
                                                    <i class="bi bi-heart-pulse-fill"></i>
                                                </div>

                                                <span class="text-truncate" style="max-width:160px;"
                                                    title="<?= htmlspecialchars($ex['atendio'] ?? '') ?>">

                                                    <?= htmlspecialchars($ex['atendio'] ?? 'Sin asignar') ?>

                                                </span>

                                            </div>
                                        </td>


                                        <!-- MOTIVO -->
                                        <td>
                                            <span class="triage-chip">

                                                <i class="bi bi-tag-fill" style="font-size:.65rem;"></i>

                                                <?= htmlspecialchars(
                                                    $ex['motivo_consulta'] ?? 'Sin motivo registrado'
                                                ) ?>

                                            </span>
                                        </td>

                                        <!-- DIAGNÓSTICO -->
                                        <td>

                                            <div class="diag-box" title="<?= htmlspecialchars($ex['diagnostico'] ?? '') ?>">

                                                <span class="diag-text">

                                                    <?= htmlspecialchars(
                                                        $ex['diagnostico']
                                                        ?: 'Sin diagnóstico asentado'
                                                    ) ?>

                                                </span>

                                            </div>

                                        </td>

                                        <!-- DOCUMENTOS -->

                                        <td class="text-start text-md-center">

                                            <?php if (!empty($ex['documentos_url'])): ?>

                                                <?php
                                                $documentos = explode(';;;', $ex['documentos_url']);
                                                ?>

                                                <div class="dropdown d-inline-block">

                                                    <button
                                                        class="btn btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-none"
                                                        type="button" data-bs-toggle="dropdown">

                                                        <i class="bi bi-paperclip text-primary"></i>

                                                        <span class="small fw-bold">
                                                            <?= count($documentos) ?>
                                                        </span>

                                                        <span class="small text-muted" style="font-size:.7rem;">
                                                            archivos
                                                        </span>

                                                    </button>

                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end dropdown-menu-custom shadow-lg p-2 border">

                                                        <!-- ENCABEZADO CON BOTÓN ZIP -->
                                                        <li
                                                            class="px-2 py-2 border-bottom mb-2 d-flex align-items-center justify-content-between gap-2">

                                                            <span class="small fw-bold text-muted text-uppercase"
                                                                style="font-size:.68rem;">

                                                                <i class="bi bi-folder2-open me-1"></i>
                                                                Evidencias adjuntas

                                                            </span>

                                                            <button class="btn btn-xs btn-success rounded-pill py-0 px-2"
                                                                style="font-size:.68rem;"
                                                                title="Descargar todos los archivos en ZIP"
                                                                data-expediente-id="<?= $ex['id'] ?>"
                                                                data-documentos="<?= htmlspecialchars($ex['documentos_url'], ENT_QUOTES, 'UTF-8') ?>"
                                                                onclick="descargarTodosDocumentos(this)">
                                                                <i class="bi bi-file-earmark-zip me-1"></i>ZIP
                                                            </button>

                                                        </li>

                                                        <?php foreach ($documentos as $doc): ?>

                                                            <?php
                                                            $partes = explode('|||', $doc);

                                                            $nombre = $partes[0] ?? '';
                                                            $direccion = $partes[1] ?? '';

                                                            if (empty($direccion)) {
                                                                continue;
                                                            }
                                                            ?>

                                                            <li>

                                                                <a href="../../<?= htmlspecialchars($direccion) ?>" target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    class="doc-item d-flex align-items-center p-2 text-decoration-none text-main">

                                                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>

                                                                    <span class="fw-medium text-truncate" style="font-size:.78rem;">

                                                                        <?= htmlspecialchars($nombre) ?>

                                                                    </span>

                                                                </a>

                                                            </li>

                                                        <?php endforeach; ?>

                                                    </ul>

                                                </div>

                                            <?php else: ?>

                                                <span class="text-muted small">
                                                    <i class="bi bi-dash-circle me-1"></i>
                                                    Sin archivos
                                                </span>

                                            <?php endif; ?>

                                        </td>
                                        <!-- ACCIÓN -->
                                        <td class="mobile-card-actions">

                                            <button
                                                class="btn btn-sm btn-action-view rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1"
                                                onclick="<?= $funcionDetalle ?>(<?= (int) $ex['id'] ?>)"
                                                title="Ver consulta detallada">

                                                <i class="bi bi-eye"></i>

                                                <span style="font-size:.78rem;">
                                                    Ver
                                                </span>

                                            </button>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">

                                        <div class="py-4">

                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-40"></i>

                                            <h6 class="fw-semibold text-main mb-1">
                                                Sin consultas registradas
                                            </h6>

                                            <p class="small text-muted mb-0">
                                                No se encontraron expedientes en el rango de fechas seleccionado.
                                            </p>

                                        </div>

                                    </td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>

    </div>


    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script>/**
* Descarga todos los documentos del expediente en un ZIP.
* Se llama desde el botón "ZIP" del dropdown.
*/
        async function descargarTodosDocumentos(boton) {

            const expedienteId = boton.dataset.expedienteId;
            const documentosRaw = boton.dataset.documentos;

            if (!documentosRaw) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin documentos',
                    text: 'Este expediente no tiene archivos adjuntos.'
                });
                return;
            }

            // ============================================================
            // 1. Parsear la cadena documento_url
            // ============================================================
            const documentos = documentosRaw.split(';;;').map(item => {
                const partes = item.split('|||');
                return {
                    nombre: partes[0] || 'Documento',
                    ruta: partes[1] || '',
                    id: partes[2] || ''
                };
            }).filter(d => d.ruta);

            if (documentos.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin documentos',
                    text: 'No hay archivos válidos para descargar.'
                });
                return;
            }

            // ============================================================
            // 2. Bloquear botón y mostrar progreso
            // ============================================================
            const textoOriginal = boton.innerHTML;
            boton.disabled = true;
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Toast de progreso (opcional)
            Swal.fire({
                title: 'Preparando ZIP...',
                html: 'Iniciando descarga de <b>0</b> de <b>' + documentos.length + '</b> archivos...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                // ============================================================
                // 3. Crear ZIP y descargar cada archivo
                // ============================================================
                const zip = new JSZip();
                const carpetaAdjuntos = zip.folder('adjuntos');

                let hechos = 0;
                const total = documentos.length;

                for (const doc of documentos) {
                    try {
                        // Detectar URL base (según cómo lo tienes: ../../uploads/...)
                        const url = new URL('../../' + doc.ruta, window.location.href).href;

                        const response = await fetch(url, {
                            method: 'GET',
                            credentials: 'include' // por si requiere sesión PHP
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const blob = await response.blob();

                        // Nombre limpio para dentro del ZIP
                        const nombreLimpio = sanitizarNombre(doc.nombre);
                        carpetaAdjuntos.file(nombreLimpio, blob);

                    } catch (err) {
                        console.error(`Error con ${doc.nombre}:`, err);

                        // Dejar constancia del error dentro del ZIP
                        carpetaAdjuntos.file(
                            `ERROR_${sanitizarNombre(doc.nombre)}.txt`,
                            `No se pudo descargar.\nNombre: ${doc.nombre}\nRuta: ${doc.ruta}\nMotivo: ${err.message}`
                        );
                    }

                    hechos++;

                    // Actualizar progreso
                    Swal.update({
                        html: `Descargando <b>${hechos}</b> de <b>${total}</b> archivos...<br>
                       <small class="text-muted">${sanitizarNombre(doc.nombre)}</small>`
                    });
                }

                // ============================================================
                // 4. Generar ZIP
                // ============================================================
                const contenidoZip = await zip.generateAsync(
                    {
                        type: 'blob',
                        compression: 'DEFLATE',
                        compressionOptions: { level: 6 }
                    },
                    (metadata) => {
                        // Progreso de compresión (opcional)
                        Swal.update({
                            html: `Comprimiendo ZIP... <b>${metadata.percent.toFixed(0)}%</b>`
                        });
                    }
                );

                // ============================================================
                // 5. Descargar el ZIP
                // ============================================================
                const nombreZip = `Expediente_${expedienteId}_${new Date().toISOString().slice(0, 10)}.zip`;
                descargarBlobJSZip(contenidoZip, nombreZip);

                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: `Se descargaron ${total} archivo(s) en el ZIP.`,
                    timer: 2500,
                    showConfirmButton: false
                });

            } catch (err) {
                console.error('Error general:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'No se pudo generar el ZIP.'
                });

            } finally {
                // Restaurar botón
                boton.disabled = false;
                boton.innerHTML = textoOriginal;
            }
        }


        // ============================================================
        // Utilidades
        // ============================================================

        /**
         * Descarga un Blob como archivo.
         */
        function descargarBlobJSZip(blob, nombreArchivo) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = nombreArchivo;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }


        /**
         * Sanitiza un nombre de archivo para que sea seguro dentro del ZIP.
         */
        function sanitizarNombre(nombre) {
            return String(nombre)
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')       // quitar acentos
                .replace(/[^a-zA-Z0-9._-]/g, '_')      // reemplazar caracteres raros
                .replace(/_+/g, '_')                   // evitar múltiples _
                .substring(0, 100);                    // limitar longitud
        }
    </script>
    <script>
        console.log(<?= json_encode($expediente) ?>);
    </script>
    <script>
        document.querySelectorAll('.filtro-tipo').forEach(boton => {

            boton.addEventListener('click', function () {

                const tipoSeleccionado = this.dataset.tipo;

                // Cambiar texto del botón
                const texto = this.textContent.trim();

                document.getElementById('textoFiltroTipo').textContent =
                    tipoSeleccionado === 'todos'
                        ? 'Todas'
                        : texto.replace('Consultas ', '');

                // Marcar opción activa
                document.querySelectorAll('.filtro-tipo').forEach(item => {
                    item.classList.remove('active');
                });

                this.classList.add('active');

                // Filtrar filas
                document.querySelectorAll('table tbody tr[data-tipo]').forEach(fila => {

                    const tipoFila = fila.dataset.tipo;

                    if (
                        tipoSeleccionado === 'todos' ||
                        tipoFila === tipoSeleccionado
                    ) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }

                });

            });

        });
        function verDocumentoSeguro(rutaRelativa, nombreArchivo) {
            // Construyes la ruta completa de forma interna en JavaScript
            const urlCompleta = "../../" + rutaRelativa;

            // Abre el archivo en una pestaña nueva sin revelar la ruta al pasar el cursor por el enlace
            window.open(urlCompleta, '_blank');
        }
        const urlParams = new URLSearchParams(window.location.search);

        // Extraer el valor de 'api_token'
        const AccesoToken = urlParams.get('api_token');

        console.log("API Token obtenido:", AccesoToken);

        // Ejemplo de uso para tus peticiones (por ejemplo, para cargar el detalle o filtrar)
        if (AccesoToken) {
            // Puedes guardarlo en una constante global o usarlo en tus funciones fetch
            window.currentAccesoToken = AccesoToken;
        } else {
            console.warn("No se encontró el api_token en la URL.");
        }
        // Obtenemos el ID de forma segura desde PHP para evitar perderlo al limpiar la URL
        const idc = <?= json_encode($idex) ?>;

        // document.addEventListener('DOMContentLoaded', function () {
        //     // Limpiamos los parámetros largos de la URL de forma limpia al iniciar
        //     if (window.location.search && window.location.search.includes('fecha_')) {
        //         const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (idc ? `?id=${idc}` : '');
        //         window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        //     }
        // });

        function filtrarExpediente() {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;

            window.location.href = `/myvet/app/controllers/consultaHIstorialClienteController.php?api_token=${AccesoToken}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        }

        async function cargarDetalleHistorial(historialId) {
            try {
                const response = await fetch(`/myvet/app/controllers/consultaHIstorialClienteController.php?action=obtenerHistorialDetalle&id=${historialId}&api_token=${AccesoToken}`);
                const resultado = await response.json();
                console.log(resultado);

                if (!response.ok || !resultado.success) {
                    throw new Error(resultado.message || 'Error al obtener los datos.');
                }

                ejecutarImpresionExpediente(resultado.data);

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        } async function cargarDetalleHistorialDental(historialId) {
            try {
                const response = await fetch(`/myvet/app/controllers/consultaHIstorialClienteController.php?action=obtenerHistorialDetalleDental&id=${historialId}&api_token=${AccesoToken}`);
                const resultado = await response.json();
                console.log(resultado);

                if (!response.ok || !resultado.success) {
                    throw new Error(resultado.message || 'Error al obtener los datos.');
                }

                ejecutarImpresionExpedienteDental(resultado.data);

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        }
        function ejecutarImpresionExpedienteDental(data) {
            const ventana = window.open('', '_blank', 'height=750,width=900');

            const folioFormateado = String(data.id || '0').padStart(5, '0');

            let fechaConsulta = 'N/A';
            if (data.fecha_consulta) {
                const f = new Date(data.fecha_consulta.replace(/-/g, '/'));
                fechaConsulta = f.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' }) +
                    ' ' + f.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
            }

            const costoFormateado = parseFloat(data.costo || 0).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });

            ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>HISTORIAL_DENTAL_${folioFormateado}</title>
            <style>
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                    font-family: Arial, Helvetica, sans-serif;
                }
                body { 
                    padding: 0.8cm;
                    background-color: #ffffff;
                    color: #1e293b;
                    font-size: 11px;
                    line-height: 1.3;
                }
                .marca-agua {
                    position: fixed;
                    top: 45%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 220px;
                    opacity: 0.03;
                    z-index: 0;
                    pointer-events: none;
                }
                .contenido-principal {
                    position: relative;
                    z-index: 1;
                }
                
                /* Layout compacto estilo reporte */
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 2px solid #0f172a;
                    padding-bottom: 8px;
                    margin-bottom: 10px;
                }
                .header-left {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }
                .logo {
                    width: 48px;
                    height: 48px;
                    object-fit: contain;
                }
                .titulo-reporte {
                    font-size: 16px;
                    font-weight: bold;
                    color: #0f172a;
                    letter-spacing: 0.5px;
                }
                .subtitulo-reporte {
                    font-size: 10px;
                    color: #64748b;
                    margin-top: 2px;
                }
                .badge-estado {
                    display: inline-block;
                    background-color: #e2e8f0;
                    color: #0f172a;
                    font-size: 9px;
                    font-weight: bold;
                    padding: 2px 6px;
                    border-radius: 3px;
                    margin-left: 6px;
                }

                .grid-2 {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 8px;
                    margin-bottom: 8px;
                }
                .grid-3 {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 8px;
                    margin-bottom: 8px;
                }
                .grid-4 {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 8px;
                    margin-bottom: 8px;
                }

                .card-block {
                    border: 1px solid #cbd5e1;
                    border-radius: 4px;
                    padding: 6px 8px;
                    background-color: #ffffff;
                }
                .card-block.bg-light {
                    background-color: #f8fafc;
                }

                .seccion-label {
                    font-size: 9px;
                    font-weight: bold;
                    color: #475569;
                    text-transform: uppercase;
                    border-bottom: 1px solid #e2e8f0;
                    padding-bottom: 2px;
                    margin-bottom: 4px;
                    display: block;
                }
                
                .data-label {
                    font-size: 9px;
                    color: #64748b;
                    display: block;
                }
                .data-value {
                    font-size: 11px;
                    font-weight: 600;
                    color: #0f172a;
                }
                .data-value-highlight {
                    font-size: 12px;
                    font-weight: bold;
                    color: #1e3a8a;
                }

                .box-evaluacion {
                    background-color: #f1f5f9;
                    border: 1px solid #94a3b8;
                    border-radius: 4px;
                    padding: 6px;
                    margin-bottom: 8px;
                    display: flex;
                    justify-content: space-around;
                    text-align: center;
                }
                .box-evaluacion-item {
                    flex: 1;
                }
                .box-evaluacion-item:not(:last-child) {
                    border-right: 1px solid #cbd5e1;
                }

                .text-block {
                    min-height: 28px;
                    font-size: 10px;
                    color: #334155;
                    word-wrap: break-word;
                }

                .footer-costo {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-top: 1.5px solid #0f172a;
                    padding-top: 6px;
                    margin-top: 10px;
                }

                .firma-container {
                    margin-top: 30px;
                    display: flex;
                    justify-content: center;
                }
                .firma-box {
                    border-top: 1px solid #0f172a;
                    width: 200px;
                    text-align: center;
                    padding-top: 4px;
                    font-size: 10px;
                }

                @page { 
                    size: letter portrait;
                    margin: 0; 
                }
                @media print {
                    body { padding: 0.8cm; }
                }
            </style>
        </head>
        <body>

            <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" class="marca-agua" alt="Watermark">


            <div id="areaImpresion" class="contenido-principal">

                <!-- ENCABEZADO -->
                <div class="header">
                    <div class="header-left">
                       <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" alt="Logo" width="55" height="55" class="me-3">
                             <div>
                            <div class="titulo-reporte">HISTORIAL CLÍNICO DENTAL</div>
                            <div class="subtitulo-reporte">
                                FOLIO: <strong>#${folioFormateado}</strong>
                                <span class="badge-estado">${(data.estado || 'COMPLETADA').toUpperCase()}</span>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; font-size: 12px;">CLÍNICA DENTAL</div>
                        <div style="font-size: 10px; color: #64748b;">${fechaConsulta}</div>
                    </div>
                </div>

                <!-- DATOS DEL PACIENTE -->
                <div class="card-block bg-light" style="margin-bottom: 8px;">
                    <span class="seccion-label">DATOS DEL PACIENTE</span>
                    <div class="grid-3">
                        <div>
                            <span class="data-label">Nombre / Razón Social</span>
                            <span class="data-value">${data.cliente_nombre || data.razon_social || 'PÚBLICO EN GENERAL'}</span>
                        </div>
                        <div>
                            <span class="data-label">RFC</span>
                            <span class="data-value">${data.rfc || 'N/A'}</span>
                        </div>
                        <div>
                            <span class="data-label">Teléfono</span>
                            <span class="data-value">${data.telefono || 'Sin registrar'}</span>
                        </div>
                    </div>
                </div>

                <!-- EVALUACIÓN RÁPIDA DENTAL -->
                <div class="box-evaluacion">
                    <div class="box-evaluacion-item">
                        <span class="data-label">PRESIÓN ARTERIAL</span>
                        <span class="data-value-highlight">${data.presion_arterial || 'N/R'}</span>
                    </div>
                    <div class="box-evaluacion-item">
                        <span class="data-label">PIEZAS DENTALES AFECTADAS</span>
                        <span class="data-value-highlight" style="color: #b91c1c;">${data.piezas_dentales || 'N/R'}</span>
                    </div>
                </div>

                <!-- ANTECEDENTES -->
                <div class="grid-2">
                    <div class="card-block">
                        <span class="seccion-label">Antecedentes Médicos</span>
                        <div class="text-block">${data.antecedentes_medicos || 'Ninguno'}</div>
                    </div>
                    <div class="card-block">
                        <span class="seccion-label">Antecedentes Dentales</span>
                        <div class="text-block">${data.antecedentes_dentales || 'Ninguno'}</div>
                    </div>
                </div>

                <!-- SÍNTOMAS Y MOTIVO -->
                <div class="grid-2">
                    <div class="card-block">
                        <span class="seccion-label">Motivo de Consulta</span>
                        <div class="text-block">${data.motivo_consulta || 'Sin especificar.'}</div>
                    </div>
                    <div class="card-block">
                        <span class="seccion-label">Síntomas Reportados</span>
                        <div class="text-block">${data.sintomas || 'Sin registrar.'}</div>
                    </div>
                </div>

                <!-- DIAGNÓSTICO -->
                <div class="card-block" style="margin-bottom: 8px;">
                    <span class="seccion-label">Diagnóstico Clínico</span>
                    <div class="text-block" style="font-weight: bold; text-transform: uppercase;">${data.diagnostico || 'Pendiente.'}</div>
                </div>

                <!-- PROCEDIMIENTO REALIZADO -->
                <div class="card-block" style="margin-bottom: 8px; background-color: #f8fafc;">
                    <span class="seccion-label">Procedimiento Realizado</span>
                    <div class="text-block" style="font-weight: bold; text-transform: uppercase;">${data.procedimiento_realizado || 'Ninguno'}</div>
                </div>

                <!-- PLAN DE TRATAMIENTO -->
                <div class="card-block" style="margin-bottom: 8px;">
                    <span class="seccion-label">Plan de Tratamiento / Indicaciones</span>
                    <div class="text-block">${data.plan_tratamiento || 'Sin tratamiento prescrito.'}</div>
                </div>

                <!-- OBSERVACIONES -->
                ${data.observaciones && data.observaciones !== 'ninguna' ? `
                <div class="card-block" style="margin-bottom: 8px;">
                    <span class="seccion-label">Observaciones Adicionales</span>
                    <div class="text-block">${data.observaciones}</div>
                </div>
                ` : ''}

                <!-- TOTAL Y COSTO -->
                <div class="footer-costo">
                    <span style="font-weight: bold; font-size: 10px; color: #475569; text-transform: uppercase;">Costo del Servicio / Procedimiento</span>
                    <span style="font-size: 14px; font-weight: bold; color: #0f172a;">${costoFormateado} MXN</span>
                </div>

                <!-- FIRMA -->
                <div class="firma-container">
                    <div class="firma-box">
                        <strong style="display: block; text-transform: uppercase;">Cirujano Dentista</strong>
                        <span style="color: #64748b; font-size: 8px;">Firma y Cédula Profesional</span>
                    </div>
                </div>

            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script>

            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                    setTimeout(() => {
                        if (esMovil) {
                            const elemento = document.getElementById('areaImpresion');
                            const opciones = {
                                margin:       0.3,
                                filename:     'Historial_Dental_Folio_${folioFormateado}.pdf',
                                image:        { type: 'jpeg', quality: 0.98 },
                                html2canvas:  { scale: 2, useCORS: true },
                                jsPDF:        { unit: 'cm', format: 'letter', orientation: 'portrait' }
                            };

                            html2pdf().set(opciones).from(elemento).save();
                        } else {
                            window.print();
                        }
                    }, 600);
                });
            <\/script>
        </body>
        </html>
    `);

            ventana.document.close();
        }



        function ejecutarImpresionExpediente(data) {
            const ventana = window.open('', '_blank', 'height=750,width=900');

            const folioFormateado = String(data.id || '0').padStart(5, '0');

            let fechaConsulta = 'N/A';
            if (data.fecha_consulta) {
                const f = new Date(data.fecha_consulta.replace(/-/g, '/'));
                fechaConsulta = f.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' }) +
                    ' ' + f.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
            }

            const costoFormateado = parseFloat(data.costo || 0).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });

            ventana.document.write(`
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EXPEDIENTE_CLINICO_${folioFormateado}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "SF Pro", "Helvetica Neue", Helvetica, Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body { 
            padding: 0.8cm 1cm;
            background-color: #ffffff;
            color: #1d1d1f;
            font-size: 9.5px;
            line-height: 1.35;
        }

        .marca-agua {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 260px;
            opacity: 0.022;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%);
        }

        .contenido-principal {
            position: relative;
            z-index: 1;
        }

        /* --- ENCABEZADO ESTILO APPLE --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ededf2;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-frame {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e5e5ea;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .titulo-reporte {
            font-size: 16px;
            font-weight: 800;
            color: #1d1d1f;
            letter-spacing: -0.4px;
            line-height: 1.1;
        }

        .subtitulo-reporte {
            font-size: 9px;
            color: #86868b;
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .badge-folio {
            background: #f2f2f7;
            color: #1d1d1f;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 6px;
            font-size: 8.5px;
        }

        .badge-estado {
            background: rgba(52, 199, 89, 0.12);
            color: #248a3d;
            font-size: 8px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 9999px;
            letter-spacing: 0.2px;
        }

        .header-right {
            text-align: right;
        }

        .chip-oficial {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(0, 113, 227, 0.08);
            color: #0071e3;
            font-size: 8px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .fecha-tag {
            font-size: 8.5px;
            color: #86868b;
            font-weight: 500;
        }

        /* --- CONTENEDORES Y TARJETAS INSET GROUPED --- */
        .card-ios {
            background: #ffffff;
            border: 1px solid #ededf2;
            border-radius: 12px;
            padding: 7px 10px;
            margin-bottom: 7px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.015);
        }

        .card-ios-tint {
            background: #fbfbfd;
            border: 1px solid #ededf2;
            border-radius: 12px;
            padding: 7px 10px;
            margin-bottom: 7px;
        }

        .seccion-header {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 4px;
        }

        .icon-badge {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .seccion-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- GRILLAS --- */
        .grid-paciente {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr;
            gap: 8px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
            margin-bottom: 7px;
        }

        .grid-vitals {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 7px;
            margin-bottom: 7px;
        }

        .data-label {
            font-size: 7.5px;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 1px;
            display: block;
        }

        .data-value {
            font-size: 10px;
            font-weight: 600;
            color: #1d1d1f;
        }

        /* --- APPLE HEALTH VITAL WIDGETS --- */
        .vital-card {
            background: #f9f9fb;
            border: 1px solid #ededf2;
            border-radius: 11px;
            padding: 6px 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vital-icon-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .vital-info {
            display: flex;
            flex-direction: column;
        }

        .vital-title {
            font-size: 7px;
            font-weight: 700;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1;
            margin-bottom: 2px;
        }

        .vital-num {
            font-size: 12px;
            font-weight: 800;
            color: #1d1d1f;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        /* --- TARJETA DE DIAGNÓSTICO DESTACADA (APPLE INSIGHT) --- */
        .card-diagnostico {
            background: linear-gradient(135deg, #f0f7ff 0%, #f5f8ff 100%);
            border: 1px solid #cfe2ff;
            border-radius: 12px;
            padding: 7px 11px;
            margin-bottom: 7px;
            position: relative;
        }

        .diagnostico-valor {
            font-size: 10.5px;
            font-weight: 800;
            color: #004085;
            letter-spacing: -0.1px;
        }

        /* --- BLOQUE DE TEXTO CLÍNICO --- */
        .text-content {
            font-size: 9.5px;
            color: #2c2c2e;
            line-height: 1.36;
            word-wrap: break-word;
        }

        /* --- FOOTER: TOTAL & FIRMA MÉDICA --- */
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #d1d1d6;
        }

        .costo-pill {
            display: inline-flex;
            flex-direction: column;
            background: #f2f2f7;
            padding: 5px 12px;
            border-radius: 9px;
            border: 1px solid #e5e5ea;
        }

        .costo-label {
            font-size: 7px;
            font-weight: 700;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .costo-val {
            font-size: 13px;
            font-weight: 800;
            color: #1d1d1f;
            letter-spacing: -0.4px;
        }

        .firma-area {
            text-align: center;
            padding-left: 20px;
        }

        .firma-line {
            width: 180px;
            border-top: 1px solid #aeaeb2;
            margin: 0 auto 3px auto;
        }

        .firma-nombre {
            font-size: 9px;
            font-weight: 700;
            color: #1d1d1f;
            text-transform: uppercase;
        }

        .firma-sub {
            font-size: 7.5px;
            color: #86868b;
        }

        @page { 
            size: letter portrait;
            margin: 0; 
        }

        @media print {
            body { padding: 0.8cm 1cm; }
        }
    </style>
</head>
<body>

    <img src="/myvet/${escapeHtmlUserSession(data.ico || 'public/assets/logo.ico')}" class="marca-agua" alt="Watermark">

    <div id="areaImpresion" class="contenido-principal">

        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                <div class="logo-frame">
                    <img src="/myvet/${escapeHtmlUserSession(data.ico || 'public/assets/logo.ico')}" alt="Logo" class="logo">
                </div>
                <div>
                    <div class="titulo-reporte">Historial Clínico</div>
                    <div class="subtitulo-reporte">
                        <span>FOLIO</span> <span class="badge-folio">#${folioFormateado}</span>
                        <span class="badge-estado">${(data.estado || 'COMPLETADA').toUpperCase()}</span>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <span class="chip-oficial">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Documento Oficial
                </span>
                <div class="fecha-tag">${fechaConsulta}</div>
            </div>
        </div>

        <!-- 1. DATOS DEL PACIENTE -->
        <div class="card-ios">
            <div class="seccion-header">
                <span class="icon-badge" style="background: rgba(0, 113, 227, 0.1); color: #0071e3;">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </span>
                <span class="seccion-label">Datos Generales del Paciente</span>
            </div>
            <div class="grid-paciente">
                <div>
                    <span class="data-label">Nombre del Paciente</span>
                    <span class="data-value">${data.paciente_nombre || data.nombre || 'PÚBLICO EN GENERAL'}</span>
                </div>
                <div>
                    <span class="data-label">Teléfono de Contacto</span>
                    <span class="data-value">${data.telefono || 'Sin registrar'}</span>
                </div>
                <div>
                    <span class="data-label">Correo Electrónico</span>
                    <span class="data-value">${data.email || 'N/A'}</span>
                </div>
            </div>
        </div>

        <!-- 2. SIGNOS VITALES (APPLE HEALTH WIDGETS) -->
        <div class="grid-vitals">
            <!-- Presión -->
            <div class="vital-card">
                <div class="vital-icon-circle" style="background: #ffe5e8; color: #ff2d55;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
                <div class="vital-info">
                    <span class="vital-title">Presión</span>
                    <span class="vital-num">${data.presion_arterial || 'N/R'}</span>
                </div>
            </div>

            <!-- Temperatura -->
            <div class="vital-card">
                <div class="vital-icon-circle" style="background: #fff0d4; color: #ff9500;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path></svg>
                </div>
                <div class="vital-info">
                    <span class="vital-title">Temperatura</span>
                    <span class="vital-num">${data.temperatura ? data.temperatura + ' °C' : 'N/R'}</span>
                </div>
            </div>

            <!-- Estatura -->
            <div class="vital-card">
                <div class="vital-icon-circle" style="background: #f1edff; color: #5856d6;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.3 8.7 8.7 21.3c-1 1-2.5 1-3.4 0l-2.6-2.6c-1-1-1-2.5 0-3.4L15.3 2.7c1-1 2.5-1 3.4 0l2.6 2.6c1 1 1 2.5 0 3.4Z"/><path d="m14.5 5.5-2.5 2.5"/><path d="m11.5 8.5-1.5 1.5"/><path d="m8.5 11.5-2.5 2.5"/></svg>
                </div>
                <div class="vital-info">
                    <span class="vital-title">Estatura</span>
                    <span class="vital-num">${data.estatura ? data.estatura + ' m' : 'N/R'}</span>
                </div>
            </div>

            <!-- Peso -->
            <div class="vital-card">
                <div class="vital-icon-circle" style="background: #e1fbf2; color: #00c7be;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M3 12h18"/><circle cx="12" cy="12" r="9"/></svg>
                </div>
                <div class="vital-info">
                    <span class="vital-title">Peso</span>
                    <span class="vital-num">${data.peso ? data.peso + ' kg' : 'N/R'}</span>
                </div>
            </div>
        </div>

        <!-- 3. MOTIVO Y SÍNTOMAS -->
        <div class="grid-2">
            <div class="card-ios">
                <div class="seccion-header">
                    <span class="icon-badge" style="background: #f2f2f7; color: #1d1d1f;">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    </span>
                    <span class="seccion-label">Motivo de Consulta</span>
                </div>
                <div class="text-content">${data.motivo_consulta || 'Sin especificar.'}</div>
            </div>

            <div class="card-ios">
                <div class="seccion-header">
                    <span class="icon-badge" style="background: #f2f2f7; color: #1d1d1f;">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </span>
                    <span class="seccion-label">Síntomas Reportados</span>
                </div>
                <div class="text-content">${data.sintomas || 'Sin registrar.'}</div>
            </div>
        </div>

        <!-- 4. ANTECEDENTES Y AVANCES -->
        <div class="grid-2">
            <div class="card-ios">
                <div class="seccion-header">
                    <span class="icon-badge" style="background: #f2f2f7; color: #86868b;">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </span>
                    <span class="seccion-label">Antecedentes Médicos</span>
                </div>
                <div class="text-content">${data.antecedentes_medicos || 'Ninguno reportado.'}</div>
            </div>

            <div class="card-ios">
                <div class="seccion-header">
                    <span class="icon-badge" style="background: #f2f2f7; color: #86868b;">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    </span>
                    <span class="seccion-label">Avances y Notas Clínicas</span>
                </div>
                <div class="text-content">${data.avances_notas || 'Ninguno registrado.'}</div>
            </div>
        </div>

        <!-- 5. DIAGNÓSTICO CLÍNICO (APPLE INSIGHT CARD) -->
        <div class="card-diagnostico">
            <div class="seccion-header">
                <span class="icon-badge" style="background: #0071e3; color: #ffffff;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </span>
                <span class="seccion-label" style="color: #0071e3;">Diagnóstico Clínico Principal</span>
            </div>
            <div class="diagnostico-valor">${data.diagnostico || 'Pendiente de diagnóstico.'}</div>
        </div>

        <!-- 6. PROCEDIMIENTO REALIZADO -->
        <div class="card-ios-tint">
            <div class="seccion-header">
                <span class="icon-badge" style="background: rgba(88, 86, 214, 0.1); color: #5856d6;">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </span>
                <span class="seccion-label" style="color: #5856d6;">Procedimiento Realizado</span>
            </div>
            <div class="text-content" style="font-weight: 600;">${data.procedimiento_realizado || 'Ninguno aplicado.'}</div>
        </div>

        <!-- 7. PLAN DE TRATAMIENTO E INDICACIONES -->
        <div class="card-ios">
            <div class="seccion-header">
                <span class="icon-badge" style="background: rgba(52, 199, 89, 0.1); color: #34c759;">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.5 20.5 3 13l7.5-7.5"/><path d="m13.5 3.5 7.5 7.5-7.5 7.5"/></svg>
                </span>
                <span class="seccion-label" style="color: #248a3d;">Tratamiento e Indicaciones Médicas</span>
            </div>
            <div class="text-content">${data.plan_tratamiento || 'Sin tratamiento prescrito.'}</div>
        </div>

        <!-- OBSERVACIONES ADICIONALES (SI EXISTEN) -->
        ${data.observaciones && data.observaciones !== 'ninguna' ? `
        <div class="card-ios">
            <div class="seccion-header">
                <span class="icon-badge" style="background: #f2f2f7; color: #86868b;">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </span>
                <span class="seccion-label">Observaciones Adicionales</span>
            </div>
            <div class="text-content">${data.observaciones}</div>
        </div>
        ` : ''}

        <!-- FOOTER: COSTO Y FIRMA -->
        <div class="footer-grid">
            <div>
                <div class="costo-pill">
                    <span class="costo-label">Costo Total Consulta</span>
                    <span class="costo-val">${costoFormateado} MXN</span>
                </div>
            </div>
            <div class="firma-area">
                <div class="firma-line"></div>
                <div class="firma-nombre">Dr(a). ${data.medico || 'Personal Médico'}</div>
                <div class="firma-sub">Firma y Cédula Profesional</div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\\/script>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            setTimeout(() => {
                if (esMovil) {
                    const elemento = document.getElementById('areaImpresion');
                    const opciones = {
                        margin:      0.25,
                        filename:    'Expediente_Clinico_Folio_${folioFormateado}.pdf',
                        image:       { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2.2, useCORS: true },
                        jsPDF:       { unit: 'cm', format: 'letter', orientation: 'portrait' }
                    };

                    html2pdf().set(opciones).from(elemento).save();
                } else {
                    window.print();
                }
            }, 500);
        });
    <\\/script>
</body>
</html>
`);

            ventana.document.close();
        }

        // Función auxiliar preventiva si necesitas escapar sesiones en JS de forma limpia
        function escapeHtmlUserSession(str) {
            return String(str).replace(/"/g, '&quot;');
        }
    </script>


    </body>

</html>