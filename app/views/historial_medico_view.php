<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
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

            /* 🟢 MODO CLÍNICO CLARO (Alta Fidelidad / Médico) */
            --main-bg: #f6f8fb;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-border-subtle: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --input-bg: #f8fafc;
            --hover-row: #f8fafc;
            --clinical-blue: #0284c7;
            --clinical-blue-dark: #0369a1;
            --clinical-blue-subtle: #e0f2fe;
            --tag-bg: #f1f5f9;
            --cal-bg: #f8fafc;
            --cal-border: #e2e8f0;
            --accent-glow: rgba(2, 132, 199, 0.12);
        }

        /* 🌙 MODO CLÍNICO OSCURO */
        [data-bs-theme="dark"] {
            --main-bg: #0b1120;
            --card-bg: #141d30;
            --card-border: #24324d;
            --card-border-subtle: #1c283f;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: #0b1120;
            --hover-row: rgba(255, 255, 255, 0.025);
            --clinical-blue: #38bdf8;
            --clinical-blue-dark: #0284c7;
            --clinical-blue-subtle: rgba(56, 189, 248, 0.12);
            --tag-bg: #1c263b;
            --cal-bg: #1c263b;
            --cal-border: #283854;
            --accent-glow: rgba(56, 189, 248, 0.18);
        }

        body {
            background-color: var(--main-bg);
            color: var(--text-main);
            font-family: var(--app-font);
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* --- DOSSIER CLÍNICO SUPERIOR (ESTACIÓN DEL MÉDICO) --- */
        .doctor-dossier-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.04);
            padding: 1.2rem 1.5rem;
            margin-bottom: 1.3rem;
            border-top: 3.5px solid var(--clinical-blue);
            position: relative;
        }

        .doctor-avatar-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0f172a 0%, #0369a1 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.18);
            flex-shrink: 0;
        }

        .exp-id-chip {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.76rem;
            font-weight: 700;
            background: var(--tag-bg);
            color: var(--clinical-blue);
            border: 1px solid var(--card-border);
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
        }

        .clinical-meta-pill {
            font-size: 0.74rem;
            font-weight: 600;
            background-color: var(--tag-bg);
            color: var(--text-muted);
            border: 1px solid var(--card-border);
            padding: 0.22rem 0.65rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* --- FILTRO CLÍNICO EN PÍLDORA SLIM --- */
        .clinical-filter-pill {
            background-color: var(--input-bg);
            border-radius: 10px;
            padding: 4px 12px;
            border: 1px solid var(--card-border);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .clinical-filter-pill:focus-within {
            border-color: var(--clinical-blue);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .clinical-filter-pill input[type="date"] {
            color: var(--text-main) !important;
            border: none;
            outline: none;
            font-size: 0.80rem;
            background: transparent;
            font-weight: 600;
        }

        .clinical-filter-pill input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.55;
            cursor: pointer;
        }

        /* --- TARJETA PRINCIPAL DEL EXPEDIENTE --- */
        .main-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 10px 30px -8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .emr-header-bar {
            background: var(--card-bg);
            padding: 0.95rem 1.4rem;
            border-bottom: 1.5px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        /* --- TABLA CLÍNICA DE ALTA DENSIDAD --- */
        .table-custom {
            margin-bottom: 0;
            color: var(--text-main);
        }

        .table-custom thead th {
            background-color: var(--card-bg);
            color: var(--text-muted);
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.95rem 1.2rem;
            border-bottom: 1.5px solid var(--card-border);
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 0.95rem 1.2rem;
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

        /* --- CALENDARIO CLÍNICO MODERNO --- */
        .cal-badge-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--cal-bg);
            border: 1px solid var(--cal-border);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            line-height: 1;
        }

        .cal-badge-box .cal-day {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .cal-badge-box .cal-month {
            font-size: 0.58rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--clinical-blue);
            letter-spacing: 0.4px;
            margin-top: 1px;
        }

        /* --- BADGES MÉDICOS --- */
        .doctor-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            background: var(--tag-bg);
            border: 1px solid var(--card-border);
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .badge-triage {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background-color: var(--clinical-blue-subtle);
            color: var(--clinical-blue-dark);
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.32rem 0.65rem;
            border-radius: 6px;
            border: 1px solid rgba(2, 132, 199, 0.2);
        }

        [data-bs-theme="dark"] .badge-triage {
            color: var(--clinical-blue);
            border-color: rgba(56, 189, 248, 0.25);
        }

        /* --- DIAGNÓSTICO CLÍNICO DESTACADO --- */
        .diag-clinical-box {
            border-left: 3px solid var(--clinical-blue);
            padding-left: 9px;
            max-width: 300px;
        }

        .diag-title-text {
            color: var(--text-main);
            font-weight: 700;
            font-size: 0.84rem;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* --- DROPDOWN PERSONALIZADO --- */
        .dropdown-menu-custom {
            min-width: 290px;
            border-radius: 14px;
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.15);
        }

        .doc-item {
            border-radius: 8px;
            transition: background-color 0.15s ease;
        }

        .doc-item:hover {
            background-color: var(--hover-row);
        }

        /* --- BOTONES DE ACCIÓN MÉDICA --- */
        .btn-emr-action {
            background: var(--tag-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            font-weight: 700;
            font-size: 0.78rem;
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.15s ease;
        }

        .btn-emr-action:hover {
            background: var(--clinical-blue);
            border-color: var(--clinical-blue);
            color: #ffffff;
            box-shadow: 0 3px 10px rgba(2, 132, 199, 0.25);
        }

        /* Modales SweetAlert2 */
        .swal2-popup {
            background: var(--card-bg) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 16px !important;
        }
    </style>

    <?php
    date_default_timezone_set('America/Mexico_City');
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');

    // Datos y cálculo demográfico para el médico
    $nombrePaciente = $cliente['nombre_comercial'] ?? 'Paciente no registrado';
    $idExpediente = str_pad($cliente['id'] ?? 1, 5, '0', STR_PAD_LEFT);
    $iniciales = mb_strtoupper(mb_substr($nombrePaciente, 0, 2, 'UTF-8'));
    $sexo = $cliente['sexo'] ?? 'No especificado';
    $telefono = $cliente['telefono'] ?? 'Sin contacto';
    $rfc = $cliente['rfc'] ?? 'S/R';
    $totalConsultas = count($expediente ?? []);

    // Cálculo automático de edad para el médico
    $fechaNac = $cliente['fecha_nacimiento'] ?? '';
    $edadTexto = 'Edad N/R';
    if (!empty($fechaNac)) {
        try {
            $nacObj = new DateTime($fechaNac);
            $hoyObj = new DateTime();
            $diff = $hoyObj->diff($nacObj);
            $edadTexto = $diff->y > 0 ? $diff->y . ' años' : ($diff->m > 0 ? $diff->m . ' meses' : $diff->d . ' días');
        } catch (Exception $e) {
            $edadTexto = 'Edad N/R';
        }
    }
    ?>

    <!-- Encabezado Principal del Layout -->
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual ?? '');
    } ?>

    <div class="container-fluid px-3 px-md-4" style="padding-top: 25px;">

        <!-- ========================================================= -->
        <!--  1. FICHA CLÍNICA DE TRIAGE SUPERIOR (PARA EL MÉDICO)    -->
        <!-- ========================================================= -->
        <!--  1. FICHA CLÍNICA SUPERIOR (EXPANDIDA A ANCHO COMPLETO)   -->
        <!-- ========================================================= -->
        <div class="doctor-dossier-card" style="padding-top: 55px;">
            <div class="row align-items-center g-3 w-100 m-0">
                <!-- LADO DERECHO: Acciones y Filtros (Pegados totalmente a la derecha) -->
                <div class="col-12 ms-auto d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 mt-xl-0">

                    <!-- Filtro de Periodo -->
                    <div class="clinical-filter-pill shadow-sm">
                        <span class="text-muted fw-bold" style="font-size: 0.70rem;">PERIODO:</span>
                        <input type="date" id="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>"
                            title="Fecha Inicio">
                        <span class="text-muted opacity-25">|</span>
                        <input type="date" id="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" title="Fecha Fin">
                        <button class="btn btn-sm btn-link text-primary p-0 text-decoration-none"
                            onclick="filtrarExpediente()" title="Actualizar">
                            <i class="bi bi-arrow-repeat fs-6"></i>
                        </button>
                    </div>

                    <!-- Botón Filtrar -->
                    <button
                        class="btn btn-primary btn-sm px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm fw-bold"
                        style="background: var(--clinical-blue); border-color: var(--clinical-blue);"
                        onclick="filtrarExpediente()">
                        <i class="bi bi-funnel-fill"></i> Filtrar
                    </button>

                    <!-- Botón de Nueva Consulta Médico -->
                    <a href="/myvet/consultaMedica?id=<?= $idex ?? '' ?>"
                        class="btn btn-dark btn-sm px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm fw-bold"
                        title="Abrir Consulta Clínica">
                        <i class="bi bi-plus-circle-fill fs-6 text-info"></i>
                        <span>Nueva Consulta</span>
                    </a>

                </div>
                <!-- LADO IZQUIERDO: Identificación Médica del Paciente (Expandido) -->
                <div class="col-12 ">

                    <!-- 1. IDENTIFICACIÓN MÉDICA DEL PACIENTE (Bootstrap Flex) -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <!-- Avatar con clases Bootstrap -->
                        <div class="rounded-3 bg-primary bg-gradient text-white d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
                            style="width: 52px; height: 52px; font-size: 1.25rem;">
                            <?= $iniciales ?>
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span
                                    class="badge bg-secondary-subtle text-secondary-emphasis border font-monospace px-2 py-1">
                                    EXP-<?= $idExpediente ?>
                                </span>
                                <h4 class="fw-bold mb-0 text-body text-capitalize">
                                    <?= htmlspecialchars($paciente['nombre_comercial']) ?>
                                </h4>
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold">
                                    <i class="bi bi-shield-fill-check me-1"></i> ACTIVO
                                </span>
                            </div>
                            <small class="text-muted d-block text-truncate">
                                <i class="bi bi-journal-medical text-primary me-1"></i> Expediente Clínico Digital
                            </small>
                        </div>
                    </div>

                    <!-- 2. REJILLA BOOTSTRAP: SE EXPANDE Y DISTRIBUYE EN TODO EL ANCHO -->
                    <div class="row g-2">

                        <!-- Edad Biológica -->
                        <div class="col-6 col-sm-4 col-md-auto flex-grow-1">
                            <div class="p-2 px-3 border rounded-3 bg-body-tertiary h-100 shadow-sm">
                                <small class="text-muted text-uppercase fw-bold d-block"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">Fecha nacimiento</small>
                                <span class="fw-bold text-body small d-flex align-items-center mt-1">
                                    <i class="bi bi-hourglass-split text-primary me-1.5"></i>
                                    <?= $paciente['fecha_nacimiento'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- Sexo / Especie -->
                        <div class="col-6 col-sm-4 col-md-auto flex-grow-1">
                            <div class="p-2 px-3 border rounded-3 bg-body-tertiary h-100 shadow-sm">
                                <small class="text-muted text-uppercase fw-bold d-block"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">Sexo</small>
                                <span class="fw-bold text-body small d-flex align-items-center mt-1">
                                    <i class="bi bi-gender-ambiguous text-info me-1.5"></i>
                                    <?= htmlspecialchars($paciente['sexo']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Teléfono de Contacto -->
                        <div class="col-6 col-sm-4 col-md-auto flex-grow-1">
                            <div class="p-2 px-3 border rounded-3 bg-body-tertiary h-100 shadow-sm">
                                <small class="text-muted text-uppercase fw-bold d-block"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">Contacto</small>
                                <span class="fw-bold text-body small d-flex align-items-center mt-1">
                                    <i class="bi bi-telephone text-secondary me-1.5"></i>
                                    <?= htmlspecialchars($paciente['telefono']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- RFC (si existe) -->
                        <?php if (!empty($rfc) && $rfc !== 'S/R'): ?>
                            <div class="col-6 col-sm-4 col-md-auto flex-grow-1">
                                <div class="p-2 px-3 border rounded-3 bg-body-tertiary h-100 shadow-sm">
                                    <small class="text-muted text-uppercase fw-bold d-block"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px;">ID Fiscal</small>
                                    <span class="fw-bold text-body small d-flex align-items-center mt-1">
                                        <i class="bi bi-person-vcard text-dark me-1.5"></i> <?= htmlspecialchars($rfc) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>



                    </div>

                </div>

                <!-- LADO DERECHO: Acciones y Filtros (Alineados al extremo derecho) -->


            </div>
        </div>

        <!-- ========================================================= -->
        <!--  2. REGISTRO CLÍNICO Y CONSULTAS (EMR DE ALTA GAMA)       -->
        <!-- ========================================================= -->
        <main class="mb-5">
            <div class="main-card">

                <!-- Barra Superior de la Tabla -->
                <div class="emr-header-bar">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-3"
                            style="background: var(--clinical-blue-subtle); color: var(--clinical-blue-dark);">
                            <i class="bi bi-journal-medical fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-main" style="font-size: 0.95rem;">Historial de Evolución y
                                Consultas</h6>
                            <small class="text-muted" style="font-size: 0.73rem;">Registro cronológico de diagnósticos y
                                exploraciones</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold"
                            style="background: var(--tag-bg); color: var(--text-main); border: 1px solid var(--card-border); font-size: 0.74rem;">
                            <i class="bi bi-folder2-open me-1 text-primary"></i> <?= $totalConsultas ?> consulta(s)
                        </span>
                        <button class="btn btn-sm btn-emr-action shadow-none" onclick="window.print()"
                            title="Imprimir Expediente">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <!-- Tabla Médica de Consultas -->
                <div class="table-responsive">
                    <table class="table table-custom align-middle" style="min-height: 200px;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 150px;">Fecha / Hora</th>
                                <th scope="col" style="width: 180px;">Médico Tratante</th>
                                <th scope="col" style="width: 200px;">Motivo Clínico</th>
                                <th scope="col">Diagnóstico Principal</th>
                                <th scope="col" class="text-center" style="width: 150px;">Estudios & Rx</th>
                                <th scope="col" class="text-end" style="width: 110px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($expediente)): ?>
                                <?php foreach ($expediente as $ex): ?>
                                    <?php
                                    $timeObj = strtotime($ex['fecha_consulta']);
                                    $dia = date('d', $timeObj);
                                    $mes = date('M', $timeObj);
                                    $anio = date('Y', $timeObj);
                                    $hora = date('H:i', $timeObj);
                                    ?>
                                    <tr>
                                        <!-- FECHA Y HORA CON SELLO CLÍNICO -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="cal-badge-box">
                                                    <span class="cal-day"><?= $dia ?></span>
                                                    <span class="cal-month"><?= $mes ?></span>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-main d-block"
                                                        style="font-size: 0.82rem;"><?= $anio ?></span>
                                                    <span class="text-muted font-monospace" style="font-size: 0.71rem;">
                                                        <i class="bi bi-clock me-1"></i><?= $hora ?> hrs
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- MÉDICO QUE ATENDIÓ -->
                                        <td>
                                            <div class="doctor-badge" title="Profesional a cargo">
                                                <i class="bi bi-person-fill-check text-primary"></i>
                                                <span class="text-truncate" style="max-width: 140px;">
                                                    <?= htmlspecialchars($ex['atendio']) ?>
                                                </span>
                                            </div>
                                        </td>

                                        <!-- MOTIVO DE CONSULTA / TRIAJE -->
                                        <td>
                                            <span class="badge-triage" title="<?= htmlspecialchars($ex['motivo_consulta']) ?>">
                                                <i class="bi bi-stethoscope"></i>
                                                <span class="text-truncate" style="max-width: 160px;">
                                                    <?= htmlspecialchars($ex['motivo_consulta']) ?>
                                                </span>
                                            </span>
                                        </td>

                                        <!-- DIAGNÓSTICO CLÍNICO -->
                                        <td>
                                            <div class="diag-clinical-box" title="<?= htmlspecialchars($ex['diagnostico']) ?>">
                                                <span class="diag-title-text">
                                                    <?= htmlspecialchars($ex['diagnostico'] ?: 'Sin diagnóstico asentado') ?>
                                                </span>
                                            </div>
                                        </td>

                                        <!-- DOCUMENTOS, ESTUDIOS Y RX -->
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <?php if (!empty($ex['documentos_url'])): ?>
                                                    <?php $documentos = explode(';;;', $ex['documentos_url']); ?>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-emr-action dropdown-toggle shadow-none"
                                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-paperclip text-primary"></i>
                                                            <span><?= count($documentos) ?> archivo(s)</span>
                                                        </button>

                                                        <ul
                                                            class="dropdown-menu dropdown-menu-end dropdown-menu-custom shadow-lg p-2">
                                                            <li class="px-2 py-1.5 border-bottom mb-2 d-flex align-items-center justify-content-between"
                                                                style="border-color: var(--card-border) !important;">
                                                                <span class="small fw-bold text-muted text-uppercase"
                                                                    style="font-size: 0.68rem;">
                                                                    <i class="bi bi-images me-1"></i> Evidencias Adjuntas
                                                                </span>

                                                                <div class="d-flex gap-1">
                                                                    <!-- NUEVO -->
                                                                    <button
                                                                        class="btn btn-xs btn-primary rounded-2 py-0.5 px-2 fw-bold"
                                                                        style="font-size: 0.68rem;"
                                                                        onclick="subirDocumentoHistorial(<?= $ex['id'] ?>)">
                                                                        <i class="bi bi-plus-lg me-1"></i>Nuevo
                                                                    </button>

                                                                    <!-- DESCARGAR ZIP — CORREGIDO -->
                                                                    <button
                                                                        class="btn btn-xs btn-success rounded-2 py-0.5 px-2 fw-bold"
                                                                        style="font-size: 0.68rem;"
                                                                        title="Descargar todos los archivos en ZIP"
                                                                        data-expediente-id="<?= $ex['id'] ?>"
                                                                        data-documentos="<?= htmlspecialchars($ex['documentos_url'], ENT_QUOTES, 'UTF-8') ?>"
                                                                        onclick="descargarTodosDocumentos(this)">
                                                                        <i class="bi bi-file-earmark-zip me-1"></i>ZIP
                                                                    </button>
                                                                </div>
                                                            </li>

                                                            <?php foreach ($documentos as $doc): ?>
                                                                <?php
                                                                $partes = explode('|||', $doc);
                                                                $nombre = $partes[0] ?? '';
                                                                $direccion = $partes[1] ?? '';
                                                                $idDoc = $partes[2] ?? 0;
                                                                if (empty($direccion))
                                                                    continue;
                                                                ?>
                                                                <li>
                                                                    <div
                                                                        class="doc-item d-flex justify-content-between align-items-center p-1.5">
                                                                        <a href="../../<?= htmlspecialchars($direccion) ?>"
                                                                            target="_blank"
                                                                            class="text-decoration-none text-main flex-grow-1 text-truncate pe-2"
                                                                            style="font-size: 0.78rem;">
                                                                            <i
                                                                                class="bi bi-file-earmark-medical-fill text-danger me-1"></i>
                                                                            <span
                                                                                class="fw-medium"><?= htmlspecialchars($nombre) ?></span>
                                                                        </a>
                                                                        <button class="btn btn-sm text-danger border-0 p-0 px-1"
                                                                            title="Eliminar documento"
                                                                            onclick="eliminarDocumento(<?= $idDoc ?>)">
                                                                            <i class="bi bi-x-circle"></i>
                                                                        </button>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php else: ?>
                                                    <button class="btn btn-sm border-0 p-1 px-2 text-muted fw-bold"
                                                        style="font-size: 0.74rem;"
                                                        onclick="subirDocumentoHistorial(<?= $ex['id'] ?>)"
                                                        title="Adjuntar Radiografía o Análisis">
                                                        <i class="bi bi-cloud-arrow-up text-primary fs-6"></i> Adjuntar
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- ACCIÓN CLÍNICA -->
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-emr-action"
                                                onclick="cargarDetalleHistorial(<?= $ex['id'] ?>)" title="Examinar Consulta">
                                                <i class="bi bi-eye-fill"></i>
                                                <span>Ver</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="bi bi-clipboard2-x fs-1 d-block mb-2 opacity-40"></i>
                                            <h6 class="fw-bold text-main mb-1">Sin Consultas Registradas</h6>
                                            <p class="small text-muted mb-3">No existen atenciones médicas registradas en el
                                                rango seleccionado.</p>
                                            <a href="/myvet/consultaMedica?id=<?= $idex ?? '' ?>"
                                                class="btn btn-sm btn-primary rounded-3 px-3 py-1.5 fw-bold">
                                                <i class="bi bi-plus-lg me-1"></i> Iniciar Primera Consulta
                                            </a>
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
        // Obtenemos el ID de forma segura desde PHP para evitar perderlo al limpiar la URL
        const idc = <?= json_encode($idex) ?>;

        document.addEventListener('DOMContentLoaded', function () {
            // Limpiamos los parámetros largos de la URL de forma limpia al iniciar
            if (window.location.search && window.location.search.includes('fecha_')) {
                const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (idc ? `?id=${idc}` : '');
                window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
            }
        });

        function filtrarExpediente() {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;

            window.location.href = `/myvet/app/controllers/historialMedicoController.php?id=${idc}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        }

        async function cargarDetalleHistorial(historialId) {
            try {
                const response = await fetch(`/myvet/app/controllers/historialMedicoController.php?action=obtenerHistorialDetalle&id=${historialId}`);
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
        }

        // Modal interactivo SweetAlert2 para subir archivos
        function subirDocumentoHistorial(pacienteId) {
            if (!pacienteId || pacienteId <= 0) {
                Swal.fire('Error', 'Identificador de paciente no válido.', 'error');
                return;
            }

            Swal.fire({
                title: 'Subir Archivo Adjunto ',
                html: `
            <input type="file" id="swal_archivo" class="form-control mb-2 bg-dark text-white border-secondary" accept=".pdf,.png,.jpg,.jpeg,.webp">
            <small class="text-muted">Formatos permitidos: PDF, JPG, PNG, WEBP.</small>
        `,
                showCancelButton: true,
                confirmButtonText: 'Subir',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    const fileInput = document.getElementById('swal_archivo');
                    const file = fileInput.files[0];

                    if (!file) {
                        Swal.showValidationMessage('Por favor selecciona un archivo');
                        return false;
                    }

                    const formData = new FormData();
                    formData.append('pacienteId', idc);
                    formData.append('consulta_id', pacienteId);
                    formData.append('documento', file);

                    try {
                        const response = await fetch('/myvet/app/controllers/historialMedicoController.php?action=subirDocumento', {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) {
                            throw new Error(`Error en el servidor (${response.status} ${response.statusText})`);
                        }

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || 'Error desconocido al subir el archivo.');
                        }

                        return data;
                    } catch (error) {
                        Swal.showValidationMessage(error.message);
                        return false;
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value && result.value.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: result.value.message || 'El documento se subió correctamente.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        function eliminarDocumento(idDoc) {
            Swal.fire({
                title: '¿Eliminar documento?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Corregido al controlador correcto: historialMedicoController.php
                    fetch(`/myvet/app/controllers/historialMedicoController.php?action=eliminarDocumento&id=${idDoc}`, {
                        method: 'DELETE'
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Eliminado', 'El documento ha sido borrado.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                            }
                        });
                }
            });
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
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body { 
            padding: 1cm 1.2cm;
            background-color: #ffffff;
            color: #0f172a;
            font-size: 9.5px;
            line-height: 1.4;
            font-feature-settings: "tnum" 1;
        }

        /* --- MARCA DE AGUA INSTITUCIONAL --- */
        .marca-agua {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 280px;
            opacity: 0.02;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%);
        }

        .documento-wrapper {
            position: relative;
            z-index: 1;
        }

        /* --- BANDA SUPERIOR DE PRESTIGIO --- */
        .top-accent-bar {
            height: 3px;
            background: linear-gradient(90deg, #0f172a 0%, #0284c7 50%, #0f172a 100%);
            border-radius: 2px;
            margin-bottom: 14px;
        }

        /* --- ENCABEZADO INSTITUCIONAL --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-container {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3px;
        }

        .logo {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .brand-titles h1 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            text-transform: uppercase;
        }

        .brand-titles p {
            font-size: 8.5px;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .header-meta {
            text-align: right;
        }

        .meta-folio-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 3px 9px;
            border-radius: 6px;
            margin-bottom: 3px;
        }

        .meta-folio-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-folio-value {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            font-family: monospace;
        }

        .meta-fecha {
            font-size: 8.5px;
            font-weight: 500;
            color: #64748b;
        }

        /* --- DOSSIER DEL PACIENTE (BARRA EJECUTIVA) --- */
        .dossier-paciente {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 9px;
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 0.8fr;
            gap: 12px;
            align-items: center;
        }

        .field-group {
            display: flex;
            flex-direction: column;
        }

        .field-label {
            font-size: 7px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 1px;
        }

        .field-value {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge-status {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            font-size: 8px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
            text-align: center;
            letter-spacing: 0.4px;
        }

        /* --- CINTA CLÍNICA DE SIGNOS VITALES --- */
        .vitales-ribbon {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            margin-bottom: 9px;
            overflow: hidden;
        }

        .vital-cell {
            padding: 7px 10px;
            text-align: center;
            position: relative;
        }

        .vital-cell:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background-color: #e2e8f0;
        }

        .vital-tag {
            font-size: 7px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .vital-metric {
            font-size: 13px;
            font-weight: 800;
            color: #0284c7;
            letter-spacing: -0.3px;
        }

        /* --- SECCIONES CLÍNICAS --- */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }

        .panel-clinico {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 10px;
        }

        .panel-header {
            font-size: 7.5px;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .panel-header::before {
            content: '';
            width: 4px;
            height: 4px;
            background: #0284c7;
            border-radius: 50%;
            display: inline-block;
        }

        .panel-body {
            font-size: 9.5px;
            color: #334155;
            line-height: 1.38;
            word-wrap: break-word;
        }

        /* --- CUADRO DE DIAGNÓSTICO MAESTRO --- */
        .diagnostico-master {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #16a34a;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }

        .diagnostico-titulo {
            font-size: 7.5px;
            font-weight: 800;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 2px;
        }

        .diagnostico-contenido {
            font-size: 11px;
            font-weight: 800;
            color: #14532d;
            letter-spacing: -0.1px;
        }

        /* --- PLAN TERAPÉUTICO (ESTILO PRESCRIPCIÓN) --- */
        .panel-tratamiento {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }

        .tratamiento-header {
            font-size: 7.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }

        .tratamiento-body {
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.4;
            font-weight: 500;
        }

        /* --- CIERRE FORMAL, TOTAL Y FIRMA --- */
        .footer-documento {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            align-items: center;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .tarifa-box {
            display: inline-flex;
            flex-direction: column;
        }

        .tarifa-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tarifa-monto {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.4px;
        }

        .firma-block {
            text-align: center;
            padding-left: 20px;
        }

        .firma-linea {
            width: 200px;
            border-top: 1px solid #0f172a;
            margin: 0 auto 4px auto;
        }

        .firma-medico {
            font-size: 9.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .firma-cargo {
            font-size: 7.5px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .legal-notice {
            font-size: 7px;
            color: #94a3b8;
            text-align: center;
            margin-top: 10px;
            letter-spacing: 0.2px;
        }

        @page { 
            size: letter portrait;
            margin: 0; 
        }

        @media print {
            body { padding: 1cm 1.2cm; }
        }
    </style>
</head>
<body>

    <img src="/myvet/${escapeHtmlUserSession(data.ico || 'public/assets/logo.ico')}" class="marca-agua" alt="Sello de Agua">

    <div id="areaImpresion" class="documento-wrapper">

        <!-- LÍNEA SUPERIOR DE DISTINCIÓN -->
        <div class="top-accent-bar"></div>

        <!-- HEADER INSTITUCIONAL -->
        <div class="header">
            <div class="header-brand">
                <div class="logo-container">
                    <img src="/myvet/${escapeHtmlUserSession(data.ico || 'public/assets/logo.ico')}" alt="Logo" class="logo">
                </div>
                <div class="brand-titles">
                    <h1>Expediente Clínico Oficial</h1>
                    <p>Departamento de Consulta y Procedimientos Médicos</p>
                </div>
            </div>
            <div class="header-meta">
                <div class="meta-folio-pill">
                    <span class="meta-folio-label">FOLIO:</span>
                    <span class="meta-folio-value">#${folioFormateado}</span>
                </div>
                <div class="meta-fecha">${fechaConsulta}</div>
            </div>
        </div>

        <!-- DOSSIER DEL PACIENTE -->
        <div class="dossier-paciente">
            <div class="field-group">
                <span class="field-label">Paciente</span>
                <span class="field-value">${data.paciente_nombre || data.nombre || 'PÚBLICO EN GENERAL'}</span>
            </div>
            <div class="field-group">
                <span class="field-label">Teléfono de Contacto</span>
                <span class="field-value">${data.telefono || 'Sin registrar'}</span>
            </div>
            <div class="field-group">
                <span class="field-label">Correo Electrónico</span>
                <span class="field-value">${data.email || 'N/A'}</span>
            </div>
            <div class="field-group" style="text-align: right;">
                <span class="field-label">Estado</span>
                <div>
                    <span class="badge-status">${(data.estado || 'COMPLETADA').toUpperCase()}</span>
                </div>
            </div>
        </div>

        <!-- CINTA DE PARÁMETROS VITALES -->
        <div class="vitales-ribbon">
            <div class="vital-cell">
                <span class="vital-tag">Presión Arterial</span>
                <span class="vital-metric">${data.presion_arterial || 'N/R'}</span>
            </div>
            <div class="vital-cell">
                <span class="vital-tag">Temperatura</span>
                <span class="vital-metric">${data.temperatura ? data.temperatura + ' °C' : 'N/R'}</span>
            </div>
            <div class="vital-cell">
                <span class="vital-tag">Estatura</span>
                <span class="vital-metric">${data.estatura ? data.estatura + ' m' : 'N/R'}</span>
            </div>
            <div class="vital-cell">
                <span class="vital-tag">Peso Corporal</span>
                <span class="vital-metric">${data.peso ? data.peso + ' kg' : 'N/R'}</span>
            </div>
        </div>

        <!-- MOTIVO Y SÍNTOMAS -->
        <div class="grid-2">
            <div class="panel-clinico">
                <div class="panel-header">Motivo de la Consulta</div>
                <div class="panel-body">${data.motivo_consulta || 'Sin especificar.'}</div>
            </div>
            <div class="panel-clinico">
                <div class="panel-header">Cuadro Clínico / Síntomas</div>
                <div class="panel-body">${data.sintomas || 'Sin registrar.'}</div>
            </div>
        </div>

        <!-- ANTECEDENTES Y EVOLUCIÓN -->
        <div class="grid-2">
            <div class="panel-clinico">
                <div class="panel-header">Antecedentes Médicos</div>
                <div class="panel-body">${data.antecedentes_medicos || 'Sin antecedentes reportados.'}</div>
            </div>
            <div class="panel-clinico">
                <div class="panel-header">Evolución y Notas de Seguimiento</div>
                <div class="panel-body">${data.avances_notas || 'Sin notas adicionales.'}</div>
            </div>
        </div>

        <!-- DIAGNÓSTICO MAESTRO -->
        <div class="diagnostico-master">
            <div class="diagnostico-titulo">Diagnóstico Clínico Concluyente</div>
            <div class="diagnostico-contenido">${data.diagnostico || 'Pendiente de confirmación diagnóstica.'}</div>
        </div>

        <!-- PROCEDIMIENTO REALIZADO -->
        <div class="panel-clinico" style="margin-bottom: 8px;">
            <div class="panel-header">Procedimiento Quirúrgico / Intervención Aplicada</div>
            <div class="panel-body" style="font-weight: 600;">${data.procedimiento_realizado || 'Ningún procedimiento invasivo o terapéutico aplicado.'}</div>
        </div>

        <!-- PLAN TERAPÉUTICO Y PRESCRIPCIÓN -->
        <div class="panel-tratamiento">
            <div class="tratamiento-header">
                <span>Plan Terapéutico y Prescripción Médica</span>
                <span style="font-size: 7.5px; color: #64748b;">R<sub style="font-size: 6px;">x</sub></span>
            </div>
            <div class="tratamiento-body">${data.plan_tratamiento || 'Sin prescripción médica activa.'}</div>
        </div>

        <!-- OBSERVACIONES ADICIONALES (SI EXISTEN) -->
        ${data.observaciones && data.observaciones !== 'ninguna' ? `
        <div class="panel-clinico" style="margin-bottom: 8px;">
            <div class="panel-header">Observaciones Médicas Complementarias</div>
            <div class="panel-body">${data.observaciones}</div>
        </div>
        ` : ''}

        <!-- CIERRE: COSTO Y FIRMA PROFESIONAL -->
        <div class="footer-documento">
            <div>
                <div class="tarifa-box">
                    <span class="tarifa-label">Honorarios por Atención Médica</span>
                    <span class="tarifa-monto">${costoFormateado} MXN</span>
                </div>
            </div>
            <div class="firma-block">
                <div class="firma-linea"></div>
                <div class="firma-medico">Dr(a). ${data.medico || 'Médico Especialista'}</div>
                <div class="firma-cargo">Firma y Cédula Profesional Autorizada</div>
            </div>
        </div>

        <div class="legal-notice">
            Este documento constituye un resumen clínico formal emitido por el sistema médico autorizado. Válido sin tachaduras ni enmendaduras.
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
                        filename:    'Expediente_Clinico_${folioFormateado}.pdf',
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