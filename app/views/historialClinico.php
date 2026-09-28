<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente: <?= htmlspecialchars($cliente['nombre_comercial']) ?> | CF System</title>
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
            --bs-body-bg: #f8fafc;
            --bs-body-font-family: 'Plus Jakarta Sans', sans-serif;
            --card-border-color: #e2e8f0;
        }

        body {
            background-color: var(--bs-body-bg);
            color: #334155;
        }

        /* Header Estilizado */
        .header-expediente {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border-color);
            padding: 1.25rem 2rem;
        }

        .filter-box {
            background-color: #f1f5f9;
            border-radius: 10px;
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
        }

        /* Card Contenedora */
        .main-card {
            background: #ffffff;
            border: 1px solid var(--card-border-color);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Tabla Estilizada */
        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--card-border-color);
        }

        .table-custom tbody td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Elementos UI */
        .badge-id {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }

        .dropdown-menu-custom {
            min-width: 320px;
            border-radius: 12px;
            border: 1px solid var(--card-border-color);
        }

        .doc-item {
            transition: background-color 0.15s ease;
            border-radius: 8px;
        }

        .doc-item:hover {
            background-color: #f1f5f9;
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
        renderizarLayout($paginaActual);
    } ?>

    <div class="container-fluid " style="padding-top:60px ;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-10">

            <!-- Título e Información Básica -->
            <div class="d-flex align-items-center gap-3">
                <a href="/myvet/app/controllers/pacientesController.php"
                    class="btn btn-icon btn-outline-secondary rounded-circle  d-inline-flex align-items-center justify-content-center"
                    title="Volver al Listado">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>

                    <small class="text-secondary">Expediente Clínico e Historial de Consultas</small>
                </div>
            </div>

            <!-- Filtros por Fecha y Acciones -->
            <div class="d-flex flex-wrap align-items-center gap-2">

                <div class="filter-box d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-1">
                        <label for="fecha_inicio" class="text-secondary fw-semibold"
                            style="font-size: 0.75rem;">Desde:</label>
                        <input type="date" id="fecha_inicio"
                            class="form-control form-control-sm  bg-transparent p-0 shadow-none fw-semibold"
                            value="<?= htmlspecialchars($fechaInicio) ?>">
                    </div>
                    <span class="text-muted">|</span>
                    <div class="d-flex align-items-center gap-1">
                        <label for="fecha_fin" class="text-secondary fw-semibold"
                            style="font-size: 0.75rem;">Hasta:</label>
                        <input type="date" id="fecha_fin"
                            class="form-control form-control-sm  bg-transparent p-0 shadow-none fw-semibold"
                            value="<?= htmlspecialchars($fechaFin) ?>">
                    </div>
                </div>

                <button class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm"
                    onclick="filtrarExpediente()">
                    <i class="bi bi-filter"></i> Filtrar
                </button>

                <button class="btn btn-outline-dark btn-sm rounded-pill px-3 d-flex align-items-center gap-1"
                    onclick="imprimirEstadoCuenta()">
                    <i class="bi bi-printer"></i> Imprimir
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
                    <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between bg-white">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-journal-medical text-primary fs-5"></i>
                            Historial de Consultas
                        </h6>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                            Total: <?= count($expediente) ?> registro(s)
                        </span>
                    </div>

                    <!-- Tabla de Consultas -->
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 80px;">ID</th>
                                    <th scope="col" style="width: 130px;">Fecha</th>
                                    <th scope="col" style="width: 130px;">Atendio</th>
                                    <th scope="col" style="width: 220px;">Motivo Consulta</th>
                                    <th scope="col">Diagnóstico</th>
                                    <th scope="col" class="text-center" style="width: 160px;">Documentos</th>
                                    <th scope="col" class="text-end" style="width: 120px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expediente as $ex):
                                    $idActual = $ex['id'] ?? $ex['id'];
                                    ?>
                                    <tr>
                                        <!-- ID -->
                                        <td>
                                            <span class="badge-id">#<?= $ex['id'] ?></span>
                                        </td>

                                        <!-- FECHA -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-calendar3 text-muted"></i>
                                                <span class="fw-medium text-dark">
                                                    <?= date('d/m/Y', strtotime($ex['fecha_consulta'])) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <!-- MOTIVO DE CONSULTA -->
                                        <td>
                                            <span class="badge-motivo small">
                                                <?= htmlspecialchars($ex['atendio']) ?>
                                            </span>
                                        </td> <!-- MOTIVO DE CONSULTA -->
                                        <!-- MOTIVO DE CONSULTA -->
                                        <td>
                                            <span
                                                class="badge bg-body-tertiary text-dark border fw-normal px-2 py-1 rounded-2">
                                                <?= htmlspecialchars($ex['motivo_consulta']) ?>
                                            </span>
                                        </td>

                                        <!-- DIAGNÓSTICO -->
                                        <td>
                                            <p class="mb-0 text-secondary lh-sm text-truncate" style="max-width: 350px;"
                                                title="<?= htmlspecialchars($ex['diagnostico']) ?>">
                                                <?= htmlspecialchars($ex['diagnostico']) ?>
                                            </p>
                                        </td>

                                        <!-- DOCUMENTOS -->
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">

                                                <?php if (!empty($ex['documento_url'])): ?>
                                                    <?php $documentos = explode(';;;', $ex['documento_url']); ?>

                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-sm btn-light border rounded-pill px-3 d-inline-flex align-items-center gap-2 shadow-sm position-relative"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="bi bi-folder2-open text-primary"></i>
                                                            <span class="small fw-semibold">Archivos</span>
                                                            <span class="badge rounded-pill bg-primary">
                                                                <?= count($documentos) ?>
                                                            </span>
                                                        </button>

                                                        <ul
                                                            class="dropdown-menu dropdown-menu-end dropdown-menu-custom shadow-lg p-2 ">
                                                            <li
                                                                class="px-2 py-1 border-bottom mb-2 d-flex align-items-center justify-content-between">
                                                                <span class="small fw-bold text-muted uppercase">
                                                                    <i class="bi bi-paperclip me-1"></i> Adjuntos
                                                                </span>
                                                                <button
                                                                    class="btn btn-xs btn-primary rounded-pill py-0 px-2 style-btn-add"
                                                                    style="font-size: 0.75rem;"
                                                                    onclick="subirDocumentoCompra(<?= $ex['id'] ?>)">
                                                                    <i class="bi bi-plus-lg me-1"></i>Nuevo
                                                                </button>
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
                                                                        class="doc-item d-flex justify-content-between align-items-center p-2">
                                                                        <a href="../../<?= $direccion ?>" target="_blank"
                                                                            class="text-decoration-none text-dark flex-grow-1 text-truncate pe-2">
                                                                            <i
                                                                                class="bi bi-file-earmark-pdf-fill text-danger me-2 fs-6"></i>
                                                                            <span
                                                                                class="small fw-medium"><?= htmlspecialchars($nombre) ?></span>
                                                                        </a>
                                                                        <button
                                                                            class="btn btn-sm btn-outline-danger  rounded-circle p-1 d-inline-flex align-items-center justify-content-center"
                                                                            style="width: 28px; height: 28px;"
                                                                            title="Eliminar documento"
                                                                            onclick="eliminarDocumento(<?= $idDoc ?>)">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php else: ?>
                                                    <button
                                                        class="btn btn-sm btn-outline-primary border-dashed rounded-pill px-3 d-inline-flex align-items-center gap-1"
                                                        onclick="subirDocumentoCompra(<?= $ex['id'] ?>)">
                                                        <i class="bi bi-cloud-upload"></i> Subir
                                                    </button>
                                                <?php endif; ?>

                                            </div>
                                        </td>

                                        <!-- ACCIONES -->
                                        <td class="text-end">
                                            <button
                                                class="btn btn-sm btn-primary rounded-pill px-3 d-inline-flex align-items-center gap-1"
                                                onclick="cargarDetalleHistorial(<?= $ex['id'] ?>)">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        const modalNuevoAbonoObj = new bootstrap.Modal('#modalNuevoAbono');


        let ventaActual = null;
        // La ruta al controlador (ajusta si el nombre del archivo varía)
        const URL_CONTROLLER = '/myvet/app/controllers/ventasHistorialController.php';
        $(document).ready(function () {
            renderCharts();
        });

        function renderCharts() {
            const ctx = document.getElementById('chartDona');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [
                            <?= isset($resumen['total_pagado']) ? floatval($resumen['total_pagado']) : 0 ?>,
                            <?= isset($resumen['saldo_total']) ? max(0, floatval($resumen['saldo_total'])) : 0 ?>
                        ],
                        backgroundColor: ['#1cc88a', '#e74a3b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

    </script>
    <script></script>
    <script>
        function filtrarExpediente() {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;

            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');

            console.log("ID:", id);

            // REDIRECCIÓN
            window.location.href =
                `/myvet/app/controllers/clienteExpedienteController.php?id=${id}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;


        }
    </script>
    <script>
        async function cargarDetalleHistorial(historialId) {
            try {
                const response = await fetch(`/myvet/app/controllers/historialExpedienteController.php?action=obtenerHistorialDetalle&id=${historialId}`);
                const resultado = await response.json();

                if (!response.ok || !resultado.success) {
                    throw new Error(resultado.message || 'Error al obtener los datos.');
                }

                console.log('Datos del historial:', resultado.data);
                ejecutarImpresionExpediente(resultado.data);
                console.log('Documentos adjuntos:', resultado.data.documentos);

            } catch (error) {
                console.error('Error:', error.message);
            }
        }
        /**
         * Consulta la API para obtener el expediente clínico y prepara la vista.
         * @param {number|string} idConsulta - ID del historial / consulta
         */


        /**
         * Genera la ventana emergente con el diseño del Expediente Clínico de MyVet.
         */
        function ejecutarImpresionExpediente(data) {
            const ventana = window.open('', '_blank', 'height=750,width=900');

            // Formateo de Folio, Fechas y Costos
            const folioFormateado = String(data.id || '0').padStart(5, '0');

            // Formatear Fecha de Consulta (AAA-MM-DD HH:mm:ss a DD/MM/AAAA)
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

            // Calcular edad estimada a partir de fecha_nacimiento
            let edadMascota = 'N/E';
            if (data.fecha_nacimiento) {
                const nacimiento = new Date(data.fecha_nacimiento);
                const hoy = new Date();
                let edad = hoy.getFullYear() - nacimiento.getFullYear();
                edadMascota = `${edad} años`;
            }

            // Ruta de fotografía o imagen por defecto
            const fotoMascota = data.fotografia ? `/${data.fotografia}` : '';

            ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>EXPEDIENTE CLINICO #${folioFormateado}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
            <style>
                body { 
                    font-family: 'Inter', system-ui, -apple-system, sans-serif;
                    padding: 1.5cm;
                    background-color: #ffffff;
                    color: #1f2a37;
                }
                .marca-agua {
                    position: fixed;
                    top: 40%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 250px;
                    opacity: 0.05;
                    z-index: 0;
                    pointer-events: none;
                }
                .contenido-principal {
                    position: relative;
                    z-index: 1;
                }
                .seccion-titulo {
                    font-size: 0.72rem;
                    font-weight: bold;
                    letter-spacing: 0.5px;
                    color: #4b5563;
                    text-transform: uppercase;
                    border-bottom: 1px solid #e5e7eb;
                    padding-bottom: 3px;
                    margin-bottom: 6px;
                }
                .vitales-box {
                    background-color: #f8fafc !important;
                    border: 1px solid #e2e8f0 !important;
                    border-radius: 8px;
                }
                .img-mascota {
                    width: 75px;
                    height: 75px;
                    object-fit: cover;
                    border-radius: 8px;
                    border: 1px solid #cbd5e1;
                }
                .badge-estado {
                    font-size: 0.7rem;
                    padding: 4px 8px;
                    border-radius: 4px;
                }
                .firma-linea {
                    border-top: 1px solid #000;
                    width: 220px;
                    margin: 40px auto 0 auto;
                    text-align: center;
                    padding-top: 5px;
                    font-size: 0.8rem;
                }
                @page { 
                    margin: 0; 
                }
                @media print {
                    body { padding: 1.5cm; }
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>

            <!-- Marca de agua -->
            <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" class="marca-agua" alt="Watermark">

            <div id="areaImpresion" class="contenido-principal">

                <!-- ENCABEZADO -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <img src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" alt="Logo" width="55" height="55" class="me-3">
                        <div>
                            <h2 class="fw-bold text-uppercase mb-0" style="color:#2855b8; letter-spacing:1px;">
                                HISTORIAL CLÍNICO
                            </h2>
                            <div class="text-body-secondary small mt-1 text-uppercase">
                                Folio Consulta: <span class="fw-semibold">#${folioFormateado}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle ms-2 badge-estado text-uppercase">
                                    ${data.estado || 'Completada'}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-end text-uppercase">
                        <div class="fw-bold fs-5" style="color:#1f2a37;">
                            MyVet
                        </div>
                        <div class="text-body-secondary small">
                            Fecha: ${fechaConsulta}
                        </div>
                    </div>
                </div>

                <!-- INFO PACIENTE -->
                <div class="row g-2 mb-3">
                    
                    <!-- PROPIETARIO -->
                    <div class="col-5">
                        <div class="border rounded-3 bg-light p-3 shadow-sm h-100">
                            <div class="seccion-titulo"><i class="bi bi-person-fill me-1"></i> Propietario / Cliente</div>
                            <div class="fw-bold text-dark fs-6">${data.propietario_nombre || 'PÚBLICO EN GENERAL'}</div>
                            <div class="text-secondary small mt-1">
                                ID Cliente: #${String(data.cliente_id || '1').padStart(4, '0')}
                            </div>
                        </div>
                    </div>

                    <!-- PACIENTE (MASCOTA) -->
                    <div class="col-7">
                        <div class="border rounded-3 bg-light p-3 shadow-sm h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="w-100">
                                    <div class="seccion-titulo"><i class="bi bi-heart-pulse-fill me-1"></i> Paciente / Mascota</div>
                                    <div class="fw-bold text-dark fs-6 text-uppercase">${data.nombre || 'Sin nombre'}</div>
                                    
                                    <div class="row g-1 mt-1 text-secondary small">
                                        <div class="col-6"><strong>Especie:</strong> ${data.especie || 'N/E'}</div>
                                        <div class="col-6"><strong>Raza:</strong> ${data.raza || 'N/E'}</div>
                                        <div class="col-6"><strong>Sexo:</strong> ${data.sexo || 'N/E'}</div>
                                        <div class="col-6"><strong>Edad:</strong> ${edadMascota}</div>
                                        <div class="col-12"><strong>Señas:</strong> ${data.senas_particulares || 'Ninguna'}</div>
                                    </div>
                                </div>

                                ${fotoMascota ? `
                                    <img src="/myvet/${fotoMascota}" class="img-mascota ms-2" alt="${data.nombre}">
                                ` : ''}
                            </div>
                        </div>
                    </div>

                </div>

                <!-- SIGNOS VITALES -->
                <div class="vitales-box p-3 mb-3 shadow-sm">
                    <div class="seccion-titulo mb-2">Toma de Signos Vitales</div>
                    <div class="row text-center">
                        <div class="col-3 border-end">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem;">Peso</small>
                            <span class="fw-bold fs-6">${data.peso_kg ? data.peso_kg + ' kg' : 'N/R'}</span>
                        </div>
                        <div class="col-3 border-end">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem;">Temperatura</small>
                            <span class="fw-bold fs-6">${data.temperatura_c ? data.temperatura_c + ' °C' : 'N/R'}</span>
                        </div>
                        <div class="col-3 border-end">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem;">F. Cardíaca</small>
                            <span class="fw-bold fs-6">${data.frecuencia_cardiaca ? data.frecuencia_cardiaca + ' bpm' : 'N/R'}</span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem;">F. Resp.</small>
                            <span class="fw-bold fs-6">${data.frecuencia_respiratoria ? data.frecuencia_respiratoria + ' rpm' : 'N/R'}</span>
                        </div>
                    </div>
                </div>

                <!-- DETALLE MÉDICO -->
                <div class="mb-2">
                    <div class="seccion-titulo">Motivo de Consulta</div>
                    <div class="text-dark small">${data.motivo_consulta || 'Sin especificar.'}</div>
                </div>

                <div class="mb-2">
                    <div class="seccion-titulo">Síntomas Reportados</div>
                    <div class="text-dark small">${data.sintomas || 'Sin registrar.'}</div>
                </div>

                <div class="mb-2">
                    <div class="seccion-titulo">Diagnóstico</div>
                    <div class="text-dark fw-bold small text-uppercase">${data.diagnostico || 'Pendiente.'}</div>
                </div>

                <div class="mb-2">
                    <div class="seccion-titulo">Tratamiento e Indicaciones Médicas</div>
                    <div class="p-2 bg-light border rounded-3 text-primary-emphasis fw-semibold small">
                        ${data.tratamiento || 'Sin tratamiento prescrito.'}
                    </div>
                </div>

                ${data.observaciones ? `
                <div class="mb-2">
                    <div class="seccion-titulo">Observaciones / Notas de Expediente</div>
                    <div class="text-muted small">${data.observaciones}</div>
                </div>
                ` : ''}

                <!-- COSTO CONSULTA -->
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="fw-bold text-uppercase text-secondary" style="font-size: 0.8rem;">Costo del Servicio / Consulta</span>
                    <span class="fs-5 fw-bold text-dark">${costoFormateado} MXN</span>
                </div>

                <!-- FIRMA -->
                <div class="mt-4 pt-2">
                    <div class="firma-linea">
                        <span class="fw-bold d-block text-uppercase">Médico:${data.medico}</span>
                        <span class="text-muted" style="font-size: 0.75rem;">Firma y Cédula Profesional</span>
                    </div>
                </div>

            </div>

            <!-- LIBRERÍA PDF MÓVIL -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script>

            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                    setTimeout(() => {
                        if (esMovil) {
                            const elemento = document.getElementById('areaImpresion');
                            const opciones = {
                                margin:       0.5,
                                filename:     'Expediente_${data.nombre}_Folio_${folioFormateado}.pdf',
                                image:        { type: 'jpeg', quality: 0.98 },
                                html2canvas:  { scale: 2, useCORS: true },
                                jsPDF:        { unit: 'cm', format: 'letter', orientation: 'portrait' }
                            };

                            html2pdf().set(opciones).from(elemento).save();
                        } else {
                            window.print();
                        }
                    }, 800);
                });
            <\/script>
        </body>
        </html>
    `);

            ventana.document.close();
        }
        function subirDocumentoCompra(historial_id) {

            console.log('gasto');


            Swal.fire({
                title: 'Documento de Compra',
                html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar documento</label>
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
                        Swal.showValidationMessage('Selecciona un archivo');
                        return false;
                    }

                    const formData = new FormData();
                    formData.append('action', 'subirDocumento');
                    formData.append('historial_id', historial_id);

                    formData.append('documento', file);



                    try {
                        const response = await fetch('/myvet/app/controllers/egresosController.php?action=subirDocumento', {
                            method: 'POST',
                            body: formData
                        });

                        // 🔥 LEEMOS COMO TEXTO PRIMERO (ANTI "Unexpected token <")
                        const text = await response.text();
                        console.log('RESPUESTA CRUDA:', text);

                        let res;
                        try {
                            res = JSON.parse(text);
                        } catch (e) {
                            throw new Error('El servidor no devolvió JSON válido');
                        }

                        if (!res.success) {
                            throw new Error(res.message || 'Error al subir archivo');
                        }

                        return res;

                    } catch (err) {
                        Swal.showValidationMessage(err.message);
                        return false;
                    }
                }

            }).then(result => {

                if (!result.isConfirmed || !result.value) return;

                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: 'Documento actualizado correctamente',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
                if (typeof cargarCompras === 'function') {
                    cargarCompras();
                }
            });
        }

        function eliminarDocumento(id) {

            console.log('gasto');


            Swal.fire({
                title: 'Eliminar Documento',

                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                confirmButtonColor: '#ed0909',
                focusConfirm: false,

                preConfirm: async () => {



                    const formData = new FormData();

                    formData.append('id', id);


                    try {
                        const response = await fetch('/myvet/app/controllers/egresosController.php?action=eliminarDocumento', {
                            method: 'POST',
                            body: formData
                        });

                        // 🔥 LEEMOS COMO TEXTO PRIMERO (ANTI "Unexpected token <")
                        const text = await response.text();
                        console.log('RESPUESTA CRUDA:', text);

                        let res;
                        try {
                            res = JSON.parse(text);
                        } catch (e) {
                            throw new Error('El servidor no devolvió JSON válido');
                        }

                        if (!res.success) {
                            throw new Error(res.message || 'Error al subir archivo');
                        }

                        return res;

                    } catch (err) {
                        Swal.showValidationMessage(err.message);
                        return false;
                    }
                }

            }).then(result => {

                if (!result.isConfirmed || !result.value) return;

                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Documento eliminado correctamente',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
                if (typeof cargarCompras === 'function') {
                    cargarCompras();
                }
            });
        }

    </script>

</body>

</html>