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

            /* 🟢 VALORES POR DEFECTO: TEMA CLARO (BLANCO) */
            --main-bg: #f8fafc;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-hover-bg: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --input-bg: #f1f5f9;
            --hover-row: #f8fafc;
            --accent-glow: rgba(59, 130, 246, 0.08);
        }

        /* 🌙 MODO OSCURO (Se activa con data-bs-theme="dark") */
        [data-bs-theme="dark"] {
            --main-bg: #0f172a;
            --card-bg: #1e293b;
            --card-border: #334155;
            --card-hover-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: #0f172a;
            --hover-row: rgba(255, 255, 255, 0.02);
            --accent-glow: rgba(59, 130, 246, 0.15);
        }

        body {
            background-color: var(--main-bg);
            color: var(--text-main);
            font-family: var(--app-font);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Card Contenedora Eleganza */
        .main-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(12px);
            overflow: hidden;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        [data-bs-theme="dark"] .main-card {
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
        }

        /* Header Filtros Contenedor */
        .filter-box {
            background-color: var(--input-bg);
            border-radius: 12px;
            padding: 6px 14px;
            border: 1px solid var(--card-border);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .filter-box:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .filter-box input[type="date"] {
            color: var(--text-main) !important;
            border: none;
            outline: none;
            font-size: 0.85rem;
            background: transparent;
        }

        .filter-box input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.2);
            cursor: pointer;
        }

        [data-bs-theme="dark"] .filter-box input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.8);
        }

        /* Tabla Estilizada */
        .table-custom {
            margin-bottom: 0;
            color: var(--text-main);
        }

        .table-custom thead th {
            background-color: var(--card-bg);
            color: var(--text-muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--card-border);
        }

        .table-custom tbody td {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--card-border);
            font-size: 0.875rem;
            vertical-align: middle;
        }

        .table-custom tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-custom tbody tr:hover {
            background-color: var(--hover-row);
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges y Elementos UI Modernos */
        .badge-id {
            background-color: var(--input-bg);
            color: var(--text-muted);
            border: 1px solid var(--card-border);
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35em 0.7em;
            border-radius: 8px;
            font-family: monospace;
        }

        .badge-motivo {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            font-weight: 500;
            padding: 0.4em 0.8em;
            border-radius: 8px;
        }

        [data-bs-theme="dark"] .badge-motivo {
            background-color: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.25);
        }

        /* Dropdown Personalizado */
        .dropdown-menu-custom {
            min-width: 320px;
            border-radius: 14px;
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        [data-bs-theme="dark"] .dropdown-menu-custom {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
        }

        .doc-item {
            transition: background-color 0.2s ease;
            border-radius: 8px;
        }

        .doc-item:hover {
            background-color: var(--hover-row);
        }

        /* Botón de Retorno con Efecto */
        .btn-back {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: var(--input-bg);
            color: #3b82f6;
            border-color: #3b82f6;
            transform: translateX(-2px);
        }

        /* Modales SweetAlert2 adaptables */
        .swal2-popup {
            background: var(--card-bg) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 18px !important;
        }
    </style>
</head>

<body>

    <?php
    date_default_timezone_set('America/Mexico_City');
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
    ?>

    <!-- Encabezado Principal -->
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual ?? '');
    } ?>

    <div class="container-fluid" style="padding-top: 50px;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3">

            <!-- Título e Información Básica -->
            <div class="d-flex align-items-center gap-3">
                <a href="/myvet/misPacientes" class="btn-back" title="Volver al Listado">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0 text-capitalize">Expediente Clínico:
                        <?= htmlspecialchars($cliente['nombre_comercial'] ?? 'Cliente') ?>
                    </h5>
                    <small class="text-muted">Historial de Consultas y Registro Médico Integral</small>
                </div>
            </div>

            <!-- Filtros por Fecha y Acciones -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="filter-box d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <label for="fecha_inicio" class="text-muted fw-semibold mb-0"
                            style="font-size: 0.75rem;">Desde:</label>
                        <input type="date" id="fecha_inicio" class="bg-transparent p-0 shadow-none fw-semibold"
                            value="<?= htmlspecialchars($fechaInicio) ?>">
                    </div>
                    <span class="text-muted opacity-25">|</span>
                    <div class="d-flex align-items-center gap-2">
                        <label for="fecha_fin" class="text-muted fw-semibold mb-0"
                            style="font-size: 0.75rem;">Hasta:</label>
                        <input type="date" id="fecha_fin" class="bg-transparent p-0 shadow-none fw-semibold"
                            value="<?= htmlspecialchars($fechaFin) ?>">
                    </div>
                </div>

                <button
                    class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm fw-medium"
                    onclick="filtrarExpediente()">
                    <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
            </div>

        </div>
    </div>

    <!-- Contenido Principal -->
    <main class="container-fluid px-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                <div class="main-card">
                    <!-- Título de la Card -->
                    <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between"
                        style="border-color: var(--card-border) !important;">
                        <h6 class="fw-bold mb-0 text-main d-flex align-items-center gap-2">
                            <i class="bi bi-journal-medical text-primary fs-5"></i>
                            Historial de Consultas
                        </h6>
                        <span
                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                            Total: <?= count($expediente ?? []) ?> registro(s)
                        </span>
                        <a href="/myvet/consultaDental?id=<?= $idex ?>"
                            class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm rounded-pill px-3 py-1.5 transition-all"
                            title="Abrir Expediente Dental">
                            <i class="bi bi-tooth fs-6"></i>
                            <span class="fw-semibold">Nueva consulta</span>
                        </a>
                    </div>

                    <!-- Tabla de Consultas -->
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>

                                    <th scope="col" style="width: 140px;">Fecha</th>
                                    <th scope="col" style="width: 220px;">Motivo Consulta</th>
                                    <th scope="col">Diagnóstico</th>
                                    <th scope="col" class="text-center" style="width: 160px;">Documentos y evidencias
                                    </th>
                                    <th scope="col" class="text-end" style="width: 120px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($expediente)): ?>
                                    <?php foreach ($expediente as $ex): ?>
                                        <tr>
                                            <!-- ID -->

                                            <!-- FECHA -->
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-calendar-event text-muted"></i>
                                                    <span class="fw-medium">
                                                        <?= date('d/m/Y', strtotime($ex['fecha_consulta'])) ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- MOTIVO DE CONSULTA -->
                                            <td>
                                                <span class="badge-motivo small">
                                                    <?= htmlspecialchars($ex['motivo_consulta']) ?>
                                                </span>
                                            </td>

                                            <!-- DIAGNÓSTICO -->
                                            <td>
                                                <p class="mb-0 text-muted lh-sm text-truncate"
                                                    title="<?= htmlspecialchars($ex['diagnostico']) ?>">
                                                    <?= htmlspecialchars($ex['diagnostico']) ?>
                                                </p>
                                            </td>

                                            <!-- DOCUMENTOS -->
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

                                            <!-- ACCIONES -->
                                            <td class="text-end">
                                                <button
                                                    class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1 shadow-sm"
                                                    onclick="cargarDetalleHistorial(<?= $ex['id'] ?>)">
                                                    <i class="bi bi-eye"></i> Ver
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                            No se encontraron consultas registradas en este periodo.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>

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
        let idc = 0;
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            idc = id;

            // Si existen parámetros en la URL, los limpiamos
            if (window.location.search) {
                const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
            }
        });
        function filtrarExpediente() {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;



            window.location.href = `/myvet/app/controllers/historialDentalController.php?id=${idc}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        }

        async function cargarDetalleHistorial(historialId) {
            try {
                const response = await fetch(`/myvet/app/controllers/historialDentalController.php?action=obtenerHistorialDetalle&id=${historialId}`);
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

        // Modal interactivo SweetAlert2 para subir archivos
        function subirDocumentoHistorial(pacienteId) {
            if (!pacienteId || pacienteId <= 0) {
                Swal.fire('Error', 'Identificador de paciente no válido.', 'error');
                return;
            }

            Swal.fire({
                title: 'Subir Archivo Adjunto Dental',
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
                    const urlParams = new URLSearchParams(window.location.search);
                    const id = urlParams.get('id');

                    const formData = new FormData();
                    formData.append('pacienteId', id);
                    formData.append('consulta_id', pacienteId);
                    formData.append('documento', file);

                    try {
                        const response = await fetch('/myvet/app/controllers/historialDentalController.php?action=subirDocumento', {
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

                        return data; // Se envía a result.value en .then()
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
                    fetch(`/myvet/app/controllers/historialExpedienteController.php?action=eliminarDocumento&id=${idDoc}`, {
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
    </script>
</body>

</html>