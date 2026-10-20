<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Medica | Sistema Medico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php
    if (function_exists('cargarEstilos')) {
        cargarEstilos();
    }
    ?>
    <style>
        :root {
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
        }

        .main-content {
            padding: 40px;
            padding-top: calc(var(--navbar-height) + 20px);
        }

        .card-premium {
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
        }

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #d1d1d6;
        }

        /* ===== LOADER OVERLAY ===== */
        #loaderOverlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: rgba(245, 245, 247, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: opacity 0.45s ease, visibility 0.45s ease;
        }

        #loaderOverlay.oculto {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-logo {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #376bd8, #2855b8);
            color: #fff;
            font-size: 1.9rem;
            box-shadow: 0 12px 30px rgba(55, 107, 216, 0.35);
            animation: pulseLogo 1.6s ease-in-out infinite;
        }

        .loader-spinner {
            margin-top: 22px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 3px solid rgba(55, 107, 216, 0.15);
            border-top-color: #376bd8;
            animation: girar 0.85s linear infinite;
        }

        .loader-texto {
            margin-top: 18px;
            font-weight: 600;
            color: #3a3a3c;
            letter-spacing: 0.02em;
            font-size: 0.95rem;
            animation: fadeText 1.6s ease-in-out infinite;
        }

        .loader-subtexto {
            margin-top: 4px;
            font-size: 0.78rem;
            color: #8e8e93;
        }

        @keyframes girar {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulseLogo {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 12px 30px rgba(55, 107, 216, 0.35);
            }

            50% {
                transform: scale(1.06);
                box-shadow: 0 16px 40px rgba(55, 107, 216, 0.5);
            }
        }

        @keyframes fadeText {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }
        }

        /* ===== SKELETON (mientras carga el contenido interno) ===== */
        .skeleton {
            background: linear-gradient(90deg, #ececf0 25%, #f6f6f8 50%, #ececf0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 8px;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .formulario-cargando .card,
        .formulario-cargando .main-content>* {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
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
    <?php
    if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual ?? '');
    }
    ?>
    <main class="main-content">
        <!-- ===== OVERLAY DE CARGA ===== -->
        <div id="loaderOverlay">
            <div class="loader-logo">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <div class="loader-spinner"></div>
            <div class="loader-texto" id="loaderTexto">Cargando información del paciente…</div>
            <div class="loader-subtexto" id="loaderSubtexto">Preparando el formulario</div>
        </div>
        <!-- ENCABEZADO -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="text-uppercase fw-semibold tracking-wider text-secondary" style="font-size: 0.75rem;">
                    Módulo Clínico
                </span>
                <h3 class="fw-bold m-0" style="letter-spacing: -0.5px;">
                    Nueva Consulta Medica
                </h3>
            </div>
            <button type="button" class="btn btn-light rounded-pill px-3 py-2 btn-sm text-secondary"
                onclick="window.history.back();">
                <i class="bi bi-x-lg me-1"></i>
                Cancelar
            </button>
        </div>

        <!-- ALMACÉN -->
        <div class="mb-4">
            <label for="almacen_id_editar" class="form-label small fw-semibold text-secondary">
                Almacén
            </label>
            <select name="almacen_id_editar" id="almacen_id_editar" class="form-select shadow-sm rounded-3 py-2"
                required onchange="cargarClientes()">
                <?php foreach ($almacenes as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($a['id'] == $almacen_usuario) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <form id="formConsulta" action="/myvet/app/controllers/consultaMedicaController.php?action=guardarConsulta"
            method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); enviarDatos();">

            <!-- ========================================================= -->
            <!-- 1. INFORMACIÓN DEL PACIENTE -->
            <!-- ========================================================= -->
            <div class="card shadow-sm rounded-4 mb-4 border-0">
                <div class="card-body p-4">

                    <!-- Encabezado -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3 bg-primary-subtle text-primary-emphasis"
                            style="width: 42px; height: 42px;">
                            <i class="bi bi-person-badge-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold text-body">Información del Paciente</h6>
                            <small class="text-body-secondary">Selección y datos generales del paciente</small>
                        </div>
                    </div>

                    <!-- Fila de campos -->
                    <div class="row g-3">

                        <!-- Paciente -->
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label small fw-semibold text-body-secondary mb-1">
                                Paciente
                            </label>

                            <div class="d-flex gap-2 align-items-stretch">

                                <!-- Select con icono -->
                                <div class="input-group flex-grow-1">
                                    <span class="input-group-text bg-body text-body-secondary border-end-0">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <select class="form-select border-start-0 shadow-none" id="cliente_id"
                                        name="cliente_id" required onchange="cargarDatosCliente(this.value)">
                                        <option value="">Seleccionar paciente…</option>
                                    </select>
                                </div>

                                <!-- Botón nuevo paciente -->
                                <button type="button"
                                    class="btn btn-primary bg-gradient d-inline-flex align-items-center gap-2 fw-semibold shadow-sm border-0"
                                    onclick="nuevoCliente()" title="Registrar nuevo paciente">
                                    <i class="bi bi-person-plus-fill"></i>
                                    <span class="d-none d-sm-inline">Nuevo</span>
                                </button>

                            </div>
                        </div>

                        <!-- Sexo -->
                        <div class="col-md-3 col-6">
                            <label for="sexoC"
                                class="form-label small fw-semibold text-body-secondary mb-1">Sexo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body text-body-secondary border-end-0">
                                    <i class="bi bi-gender-ambiguous"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0 shadow-none" id="sexoC"
                                    name="sexoC" readonly placeholder="—">
                            </div>
                        </div>

                        <!-- Edad -->
                        <div class="col-md-3 col-6">
                            <label for="edad" class="form-label small fw-semibold text-body-secondary mb-1">Edad</label>
                            <div class="input-group">
                                <input type="number" class="form-control shadow-none" id="edad" name="edad" step="0.01"
                                    min="0" placeholder="0.00">
                                <span class="input-group-text bg-body text-body-secondary">años</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- 2. CONSULTA Y SIGNOS VITALES -->
            <!-- ========================================================= -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px; height:42px; background:linear-gradient(135deg,#f3f5ff,#f8f9ff); color:#5967d9;">
                            <i class="bi bi-clipboard2-pulse-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">
                                Consulta medica y signos vitales
                            </h6>
                            <small class="text-secondary">
                                Evaluación inicial y toma de signos
                            </small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- MOTIVO -->
                        <div class="col-12">
                            <label for="motivo_consulta" class="form-label small fw-semibold text-secondary">
                                Motivo de la consulta medica
                            </label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="motivo_consulta"
                                name="motivo_consulta" rows="2"
                                placeholder="¿Por qué se presenta el paciente a consulta?" required></textarea>
                        </div>

                        <!-- SEPARADOR -->
                        <div class="col-12 mt-3">
                            <div class="d-flex align-items-center">
                                <span class="small fw-bold text-dark">
                                    Signos Vitales
                                </span>
                                <div class="flex-grow-1 ms-3" style="height:1px;background:#eef0f4;">
                                </div>
                            </div>
                        </div>

                        <!-- PRESIÓN -->
                        <div class="col-md-3 col-6">
                            <label for="presion_arterial" class="form-label small fw-semibold text-secondary">
                                Presión Arterial
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="presion_arterial" name="presion_arterial" placeholder="120/80">
                                <span class="input-group-text text-secondary">
                                    mmHg
                                </span>
                            </div>
                        </div>

                        <!-- TEMPERATURA -->
                        <div class="col-md-3 col-6">
                            <label for="temperatura" class="form-label small fw-semibold text-secondary">
                                Temperatura
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="temperatura" name="temperatura" step="0.1" min="0" placeholder="36.5">
                                <span class="input-group-text text-secondary">
                                    °C
                                </span>
                            </div>
                        </div>

                        <!-- ESTATURA -->
                        <div class="col-md-3 col-6">
                            <label for="estatura" class="form-label small fw-semibold text-secondary">
                                Estatura
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="estatura" name="estatura" step="0.01" min="0" placeholder="1.70">
                                <span class="input-group-text text-secondary">
                                    m
                                </span>
                            </div>
                        </div>

                        <!-- PESO -->
                        <div class="col-md-3 col-6">
                            <label for="peso" class="form-label small fw-semibold text-secondary">
                                Peso
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="peso" name="peso" step="0.01" min="0" placeholder="70.00">
                                <span class="input-group-text text-secondary">
                                    kg
                                </span>
                            </div>
                        </div>


                        <!-- COSTO -->
                        <div class="col-md-4 col-6">
                            <label for="costo" class="form-label small fw-semibold text-secondary">
                                Costo de consulta
                            </label>
                            <div class="input-group">
                                <span class="input-group-text text-secondary">
                                    $
                                </span>
                                <input type="number" class="form-control ios-input rounded-end-3 shadow-none" id="costo"
                                    name="costo" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- 3. ANAMNESIS Y DIAGNÓSTICO -->
            <!-- ========================================================= -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px; height:42px; background:linear-gradient(135deg,#fff7e8,#fffaf1); color:#d99418;">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">
                                Anamnesis y diagnóstico medico
                            </h6>
                            <small class="text-secondary">
                                Antecedentes, hallazgos clínicos
                            </small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- ANTECEDENTES MÉDICOS -->
                        <div class="col-md-6">
                            <label for="antecedentes_medicos" class="form-label small fw-semibold text-secondary">
                                Antecedentes Médicos
                            </label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="antecedentes_medicos"
                                name="antecedentes_medicos" rows="2"
                                placeholder="Enfermedades crónicas, alergias a medicamentos..."></textarea>
                        </div>

                        <!-- SÍNTOMAS -->
                        <div class="col-md-6">
                            <label for="sintomas" class="form-label small fw-semibold text-secondary">
                                Síntomas Reportados / Hallazgos
                            </label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="sintomas" name="sintomas"
                                rows="2" placeholder="Dolor localizado, sangrado gingival, sensibilidad..."
                                required></textarea>
                        </div>

                        <!-- DIAGNÓSTICO -->
                        <div class="col-12">
                            <label for="diagnostico" class="form-label small fw-semibold text-secondary">
                                Diagnóstico medico
                            </label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="diagnostico"
                                name="diagnostico" rows="3" placeholder="Infeccion, traumatismo" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- 4. TRATAMIENTO Y PLAN -->
            <!-- ========================================================= -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px; height:42px; background:linear-gradient(135deg,#eafbee,#f4fcf6); color:#28a745;">
                            <i class="bi bi-capsule"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">
                                Procedimiento y plan de tratamiento
                            </h6>
                            <small class="text-secondary">
                                Procedimientos realizados y evolución
                            </small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- PROCEDIMIENTO -->
                        <div class="col-12">
                            <label for="procedimiento_realizado" class="form-label small fw-semibold text-secondary">
                                Procedimiento Realizado
                            </label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="procedimiento_realizado"
                                name="procedimiento_realizado" rows="3" placeholder="Revision, estudio, placa"
                                required></textarea>
                        </div>

                        <!-- PLAN -->
                        <div class="col-md-6">
                            <label for="plan_tratamiento" class="form-label small fw-semibold text-secondary">
                                Plan de Tratamiento / Siguientes Pasos
                            </label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="plan_tratamiento"
                                name="plan_tratamiento" rows="3"
                                placeholder="Plan para citas posteriores..."></textarea>
                        </div>

                        <!-- OBSERVACIONES -->
                        <div class="col-md-6">
                            <label for="observaciones" class="form-label small fw-semibold text-secondary">
                                Observaciones Adicionales
                            </label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="observaciones"
                                name="observaciones" rows="3"
                                placeholder="Notas de seguimiento o recomendaciones al paciente..."></textarea>
                        </div>

                        <!-- EVIDENCIAS -->
                        <div class="col-12">
                            <label for="evidencias" class="form-label small fw-semibold text-secondary">
                                Evidencias (Fotos / Radiografías)
                            </label>
                            <div class="rounded-4 p-4 text-center position-relative mb-3"
                                style=" transition:all .2s ease;">
                                <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    id="evidencias" name="evidencias[]" accept=".jpg,.jpeg,.png,.pdf" multiple
                                    style="cursor:pointer; z-index:2;">
                                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                                    style="width:48px; height:48px; background:#edf3ff; color:#4775d1;">
                                    <i class="bi bi-cloud-arrow-up fs-5"></i>
                                </div>
                                <div class="fw-semibold small mb-1">
                                    Agregar archivos
                                </div>
                                <div class="small text-secondary">
                                    Arrastra radiografías o fotografías medicas aquí o haz clic
                                </div>
                                <div class="mt-2 text-muted" style="font-size:.72rem;">
                                    JPG, PNG o PDF
                                </div>
                            </div>

                            <div id="lista_evidencias" class="row g-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- BOTONES -->
            <!-- ========================================================= -->
            <div class="d-flex justify-content-end align-items-center gap-2 mt-4 mb-4">
                <button type="button" class="btn btn-light border rounded-pill px-4 py-2 shadow-none"
                    onclick="window.history.back();">
                    <i class="bi bi-x-lg me-1"></i>
                    Cancelar
                </button>

                <button type="submit" class="btn rounded-pill px-4 py-2 fw-semibold shadow-sm"
                    style="background:linear-gradient(135deg,#376bd8,#2855b8); color:#fff; border:0;">
                    <i class="bi bi-check-lg me-1"></i>
                    Guardar consulta medica
                </button>
            </div>
        </form>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once __DIR__ . '/clientes/clientesMedic.php'; ?>

    <script>
        /* =========================================================
           ESTADO GLOBAL
           ========================================================= */
        let archivosSeleccionados = [];
        let listaClientes = [];

        /* =========================================================
           CONTROL DEL LOADER
           ========================================================= */
        function mostrarLoader(texto = 'Cargando información…', subtexto = 'Por favor espera') {
            const overlay = document.getElementById('loaderOverlay');
            if (!overlay) return;
            const t = document.getElementById('loaderTexto');
            const s = document.getElementById('loaderSubtexto');
            if (t) t.textContent = texto;
            if (s) s.textContent = subtexto;
            overlay.classList.remove('oculto');
        }

        function ocultarLoader() {
            const overlay = document.getElementById('loaderOverlay');
            if (overlay) overlay.classList.add('oculto');
        }

        /* =========================================================
           INICIALIZACIÓN
           ========================================================= */
        document.addEventListener('DOMContentLoaded', async () => {
            /* --- Input de evidencias --- */
            const inputEvidencias = document.getElementById('evidencias');
            if (inputEvidencias) {
                inputEvidencias.addEventListener('change', (e) => {
                    Array.from(e.target.files).forEach((archivo) => {
                        const existe = archivosSeleccionados.some(
                            (a) => a.name === archivo.name && a.size === archivo.size
                        );
                        if (!existe) archivosSeleccionados.push(archivo);
                    });
                    inputEvidencias.value = '';
                    renderizarPrevisualizaciones();
                });
            }

            /* --- Bloquear visualmente el formulario mientras carga --- */
            document.body.classList.add('formulario-cargando');
            mostrarLoader('Cargando información…', 'Obteniendo datos del sistema');

            /* --- Parámetros de URL --- */
            const params = new URLSearchParams(window.location.search);
            const clienteIdUrl = parseInt(params.get('id') ?? '0', 10) || 0;
            const razon = params.get('razon') ?? '';

            if (razon) {
                const motivo = document.getElementById('motivo_consulta');
                if (motivo) motivo.value = razon;
            }

            /* --- Carga de clientes y datos del paciente --- */
            try {
                mostrarLoader('Cargando lista de pacientes…', 'Consultando almacén');

                const idPreseleccionado = clienteIdUrl || null;

                if (idPreseleccionado) {
                    mostrarLoader('Cargando datos del paciente…', 'Calculando edad y sexo');
                }

                await cargarClientes(idPreseleccionado);

                // Pequeña pausa para que la animación se aprecie (opcional)
                await new Promise((r) => setTimeout(r, 350));

            } catch (error) {
                console.error('[init] Error en la carga inicial:', error);
                Swal.fire({
                    icon: 'warning',
                    title: 'No se pudo cargar toda la información',
                    text: error.message || 'Verifica tu conexión e intenta recargar.',
                    confirmButtonText: 'Entendido'
                });
            } finally {
                ocultarLoader();
                document.body.classList.remove('formulario-cargando');
            }
        });

        /* =========================================================
           PREVISUALIZACIÓN DE EVIDENCIAS
           ========================================================= */
        function renderizarPrevisualizaciones() {
            const contenedor = document.getElementById('lista_evidencias');
            if (!contenedor) return;

            // Revocar URLs anteriores antes de vaciar (evita memory leak)
            if (contenedor._urlsPrevias) {
                contenedor._urlsPrevias.forEach((u) => URL.revokeObjectURL(u));
            }
            contenedor._urlsPrevias = [];
            contenedor.innerHTML = '';

            archivosSeleccionados.forEach((archivo, index) => {
                const esImagen = archivo.type.startsWith('image/');
                const esPdf = archivo.type === 'application/pdf';
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3';

                let thumbnailHtml = '';

                if (esImagen) {
                    const urlImagen = URL.createObjectURL(archivo);
                    contenedor._urlsPrevias.push(urlImagen);
                    thumbnailHtml = `
                <img src="${urlImagen}"
                    class="rounded-3 mb-2"
                    style="width:100%;height:90px;object-fit:cover;">
            `;
                } else if (esPdf) {
                    thumbnailHtml = `
                <div class="rounded-3 mb-2 d-flex align-items-center justify-content-center bg-light text-danger"
                    style="height:90px;">
                    <i class="bi bi-file-earmark-pdf-fill fs-1"></i>
                </div>
            `;
                } else {
                    thumbnailHtml = `
                <div class="rounded-3 mb-2 d-flex align-items-center justify-content-center bg-light text-secondary"
                    style="height:90px;">
                    <i class="bi bi-file-earmark-text-fill fs-1"></i>
                </div>
            `;
                }

                col.innerHTML = `
            <div class="card h-100 shadow-sm border-0 p-2 position-relative"
                style="background:#ffffff;border-radius:12px;z-index:3;">
                <button type="button"
                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center"
                    style="width:22px;height:22px;z-index:10;"
                    onclick="eliminarArchivo(${index})">
                    <i class="bi bi-x"></i>
                </button>
                ${thumbnailHtml}
                <div class="text-truncate small fw-semibold text-dark px-1"
                    title="${escapeHtml(archivo.name)}">
                    ${escapeHtml(archivo.name)}
                </div>
                <div class="text-muted px-1" style="font-size:.7rem;">
                    ${(archivo.size / 1024).toFixed(1)} KB
                </div>
            </div>
        `;

                contenedor.appendChild(col);
            });
        }

        function eliminarArchivo(index) {
            archivosSeleccionados.splice(index, 1);
            renderizarPrevisualizaciones();
        }

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        /* =========================================================
           CLIENTES
           ========================================================= */
        async function cargarClientes(clienteIdSeleccionar = null) {
            const select = document.getElementById('cliente_id');
            if (!select) return;

            select.innerHTML = '<option value="">-- Seleccione un paciente --</option>';

            const almacenId =
                document.getElementById('almacen_id_editar')?.value ||
                document.getElementById('almacen_id')?.value ||
                '';

            // Normalizar el id buscado UNA sola vez, como string limpio
            const idBuscado =
                clienteIdSeleccionar !== null && clienteIdSeleccionar !== undefined && clienteIdSeleccionar !== ''
                    ? String(clienteIdSeleccionar).trim()
                    : '';

            try {
                const url =
                    '/myvet/app/controllers/accesoController.php?action=obtenerClientes' +
                    '&almacen_id=' + encodeURIComponent(almacenId);

                const respuesta = await fetch(url);
                if (!respuesta.ok) throw new Error('HTTP ' + respuesta.status);

                const resultado = await respuesta.json();
                if (!resultado.success || !Array.isArray(resultado.data)) {
                    throw new Error(resultado.message || 'Respuesta inválida al cargar clientes.');
                }

                listaClientes = resultado.data;
                const fragment = document.createDocumentFragment();

                listaClientes.forEach((cliente) => {
                    const opcion = document.createElement('option');
                    opcion.value = String(cliente.id ?? '').trim();
                    opcion.textContent =
                        cliente.nombre_comercial || cliente.nombre || '(sin nombre)';
                    fragment.appendChild(opcion);
                });

                select.appendChild(fragment);

                // Preselección usando el id de la URL
                if (idBuscado !== '') {
                    const existe = listaClientes.some(
                        (c) => String(c.id).trim() === idBuscado
                    );

                    if (existe) {
                        select.value = idBuscado;

                        // ⬇️ AQUÍ ESPERAMOS a que terminen de cargar los datos del cliente
                        //    antes de devolver el control (así el loader se cierra al final).
                        try {
                            await cargarDatosCliente(idBuscado);
                        } catch (err) {
                            console.warn('[cargarClientes] Error al precargar datos del cliente:', err);
                        }
                    } else {
                        console.warn(
                            '[cargarClientes] El id de la URL no coincide con ningún cliente cargado:',
                            idBuscado
                        );
                    }
                }

            } catch (error) {
                console.error('Error al ejecutar cargarClientes:', error);
                select.innerHTML = '<option value="">Error al cargar la lista de clientes</option>';
                throw error; // relanzamos para que el init lo capture
            }
        }

        /* =========================================================
           DATOS DEL CLIENTE (sexo + edad)
           ========================================================= */
        async function cargarDatosCliente(clienteId) {
            try {
                const url = `/myvet/app/controllers/consultaMedicaController.php?action=obtenerPorId&id=${clienteId}`;

                const respuesta = await fetch(url);

                if (!respuesta.ok) {
                    throw new Error(`Error HTTP: ${respuesta.status}`);
                }

                const data = await respuesta.json();

                if (data.success && data.data && data.data.fecha_nacimiento) {
                    const fechaNacimientoString = data.data.fecha_nacimiento;
                    $('#sexoC').val(data.data.sexo ?? '');

                    const edad = calcularEdad(fechaNacimientoString);
                    const inputEdad = document.getElementById('edad');

                    if (inputEdad) {
                        inputEdad.value = edad >= 0 ? edad : 0;
                    } else {
                        console.warn('No se encontró el input con id="edad"');
                    }
                } else {
                    console.warn('La respuesta no contiene una fecha de nacimiento válida');
                }

            } catch (error) {
                console.error('Error al ejecutar cargarDatosCliente:', error);
                throw error; // relanzamos para que cargarClientes lo maneje
            }
        }

        // Función auxiliar para calcular la edad exacta
        function calcularEdad(fechaNacimiento) {
            const nacimiento = new Date(fechaNacimiento);
            const hoy = new Date();

            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();

            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }

            return edad;
        }

        /* =========================================================
           GUARDAR CONSULTA + EVIDENCIAS
           ========================================================= */
        async function enviarDatos() {
            try {
                const clienteId = document.getElementById('cliente_id').value;

                if (!clienteId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Por favor seleccione un paciente/cliente.'
                    });
                    return;
                }

                // 1) Guardar la consulta
                const datos = new FormData();
                datos.append('paciente_id', clienteId);
                datos.append('motivo_consulta', document.getElementById('motivo_consulta').value);
                datos.append('antecedentes_medicos', document.getElementById('antecedentes_medicos').value);
                datos.append('sintomas', document.getElementById('sintomas').value);
                datos.append('diagnostico', document.getElementById('diagnostico').value);
                datos.append('procedimiento_realizado', document.getElementById('procedimiento_realizado').value);
                datos.append('plan_tratamiento', document.getElementById('plan_tratamiento').value);
                datos.append('observaciones', document.getElementById('observaciones').value);
                datos.append('presion_arterial', document.getElementById('presion_arterial').value);
                datos.append('temperatura', document.getElementById('temperatura').value);
                datos.append('estatura', document.getElementById('estatura').value);
                datos.append('peso', document.getElementById('peso').value);
                datos.append('costo', document.getElementById('costo').value);

                Swal.fire({
                    title: 'Guardando consulta médica...',
                    text: 'Por favor espere un momento.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const response = await fetch(
                    '/myvet/app/controllers/consultaMedicaController.php?action=guardarConsulta',
                    { method: 'POST', body: datos }
                );

                const texto = await response.text();
                let resultado;
                try {
                    resultado = JSON.parse(texto);
                } catch (e) {
                    console.error('[guardarConsulta] Respuesta no-JSON:\n', texto);
                    throw new Error('Respuesta inválida del servidor al guardar la consulta.');
                }

                if (!response.ok || !resultado.success) {
                    throw new Error(resultado.message || 'Error al guardar la consulta.');
                }

                const consultaId = resultado.id;

                // 2) Subir evidencias (recolectando errores sin abortar el resto)
                const erroresEvidencias = [];

                for (const archivo of archivosSeleccionados) {
                    try {
                        const fd = new FormData();
                        fd.append('pacienteId', clienteId);
                        fd.append('consulta_id', consultaId);
                        fd.append('documento', archivo);

                        const r = await fetch(
                            '/myvet/app/controllers/historialmedicaController.php?action=subirDocumento',
                            { method: 'POST', body: fd }
                        );

                        const t = await r.text();
                        let j;
                        try { j = JSON.parse(t); } catch (_) { j = null; }

                        if (!r.ok || !j || j.success === false) {
                            erroresEvidencias.push({
                                archivo: archivo.name,
                                motivo: (j && j.message) || ('HTTP ' + r.status)
                            });
                        }
                    } catch (err) {
                        erroresEvidencias.push({ archivo: archivo.name, motivo: err.message });
                    }
                }

                // 3) Feedback final
                if (erroresEvidencias.length === 0) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Consulta médica guardada correctamente.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Consulta guardada, pero hubo problemas con evidencias',
                        html: erroresEvidencias
                            .map((e) => `<div class="small">• <b>${escapeHtml(e.archivo)}</b>: ${escapeHtml(e.motivo)}</div>`)
                            .join(''),
                    });
                }

                window.location.href = '/myvet/app/controllers/consultaMedicaController.php';

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error inesperado.'
                });
            }
        }
    </script>
</body>

</html>