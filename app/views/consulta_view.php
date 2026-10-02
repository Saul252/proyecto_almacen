<?php
/**
 * consulta_view.php
 * Vista de nueva consulta médica con carga de paciente y evidencias.
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Consulta | myvet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>

    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

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

        .badge-ubicacion {
            border: 1px solid #d1d1d6;
            padding: 0.4rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #d1d1d6;
        }

        /* ============================================
           Preview de evidencias
           ============================================ */
        .preview-card {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #f9f9f9;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-card .pdf-icon {
            font-size: 40px;
            color: #e74c3c;
        }

        .preview-card .btn-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            line-height: 1;
            transition: transform 0.2s;
            z-index: 2;
        }

        .preview-card .btn-remove:hover {
            transform: scale(1.15);
            background: #c82333;
        }

        .preview-card .file-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.65);
            color: white;
            font-size: 10px;
            padding: 3px 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .drop-zone {
            height: 100px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .drop-zone.drag-over {
            border-color: #356ae6;
            background: #f0f6ff;
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
        <!-- Encabezado -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="text-uppercase fw-semibold tracking-wider" style="font-size: 0.75rem;">Módulo
                    Clínico</span>
                <h3 class="fw-bold m-0" style="letter-spacing: -0.5px;">Nueva Consulta Médica</h3>
            </div>
            <button type="button" class="btn btn-light rounded-pill px-3 py-2 btn-sm text-secondary"
                onclick="window.history.back();">
                <i class="bi bi-x-lg me-1"></i> Cancelar
            </button>
        </div>

        <form id="formConsulta" onsubmit="return false;">

            <!-- ============================================ -->
            <!-- 1. INFO DEL PACIENTE -->
            <!-- ============================================ -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#eaf2ff,#f3f7ff);color:#356ae6;">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Información del paciente</h6>
                            <small class="text-secondary">Datos generales del paciente y propietario</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="dueno" class="form-label small fw-semibold text-secondary">Dueño /
                                Propietario</label>
                            <select class="form-select ios-input rounded-3 shadow-none" id="dueno" name="dueno"
                                required>
                                <option value="">Seleccionar propietario...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="paciente" class="form-label small fw-semibold text-secondary">Paciente</label>
                            <select class="form-select ios-input rounded-3 shadow-none" id="paciente" name="paciente"
                                required>
                                <option value="">Seleccionar paciente...</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="especie" class="form-label small fw-semibold text-secondary">Especie</label>
                            <select class="form-select ios-input rounded-3 shadow-none" id="especie" name="especie"
                                required>
                                <option value="">Seleccionar...</option>
                                <option value="Canino">Canino</option>
                                <option value="Felino">Felino</option>
                                <option value="Ave">Ave</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="raza" class="form-label small fw-semibold text-secondary">Raza</label>
                            <input type="text" class="form-control ios-input rounded-3 shadow-none" id="raza"
                                name="raza" placeholder="Ej. Siamés" required>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="peso_kg" class="form-label small fw-semibold text-secondary">Peso actual</label>
                            <div class="input-group">
                                <input type="number" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="peso_kg" name="peso_kg" step="0.01" min="0" placeholder="3.80">
                                <span class="input-group-text text-secondary">kg</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="edad" class="form-label small fw-semibold text-secondary">Edad</label>
                            <input type="text" class="form-control ios-input rounded-3 shadow-none" id="edad"
                                name="edad" placeholder="Ej. 3 años" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 2. CONSULTA Y SIGNOS VITALES -->
            <!-- ============================================ -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#f3f5ff,#f8f9ff);color:#5967d9;">
                            <i class="bi bi-clipboard2-pulse-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Consulta y signos vitales</h6>
                            <small class="text-secondary">Información clínica obtenida durante la consulta</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="motivo_consulta" class="form-label small fw-semibold text-secondary">Motivo de
                                la consulta</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="motivo_consulta"
                                name="motivo_consulta" rows="2"
                                placeholder="¿Por qué se presenta el paciente a consulta?" required></textarea>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="d-flex align-items-center">
                                <span class="small fw-bold">Signos vitales</span>
                                <div class="flex-grow-1 ms-3" style="height:1px;background:#eef0f4;"></div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="temperatura_c"
                                class="form-label small fw-semibold text-secondary">Temperatura</label>
                            <div class="input-group">
                                <input type="number" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="temperatura_c" name="temperatura_c" step="0.1" min="20" max="50"
                                    placeholder="38.5">
                                <span class="input-group-text text-secondary">°C</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="frecuencia_cardiaca"
                                class="form-label small fw-semibold text-secondary">Frecuencia cardíaca</label>
                            <div class="input-group">
                                <input type="number" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="frecuencia_cardiaca" name="frecuencia_cardiaca" min="0" placeholder="90">
                                <span class="input-group-text text-secondary">lpm</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="frecuencia_respiratoria"
                                class="form-label small fw-semibold text-secondary">Frecuencia respiratoria</label>
                            <div class="input-group">
                                <input type="number" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="frecuencia_respiratoria" name="frecuencia_respiratoria" min="0"
                                    placeholder="25">
                                <span class="input-group-text text-secondary">rpm</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="costo" class="form-label small fw-semibold text-secondary">Costo de
                                consulta</label>
                            <div class="input-group">
                                <span class="input-group-text text-secondary">$</span>
                                <input type="number" class="form-control ios-input rounded-end-3 shadow-none" id="costo"
                                    name="costo" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 3. ANAMNESIS Y DIAGNÓSTICO -->
            <!-- ============================================ -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#fff7e8,#fffaf1);color:#d99418;">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Anamnesis y diagnóstico</h6>
                            <small class="text-secondary">Evaluación clínica y hallazgos del paciente</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="sintomas" class="form-label small fw-semibold text-secondary">Síntomas
                                reportados</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="sintomas" name="sintomas"
                                rows="3" placeholder="Describa los signos o síntomas que presenta el paciente..."
                                required></textarea>
                        </div>

                        <div class="col-12">
                            <label for="explicacion"
                                class="form-label small fw-semibold text-secondary">Diagnóstico</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="explicacion"
                                name="explicacion" rows="3"
                                placeholder="Evaluación médica, diagnóstico presuntivo o definitivo..."
                                required></textarea>
                        </div>

                        <div class="col-12">
                            <label for="observaciones"
                                class="form-label small fw-semibold text-secondary">Observaciones</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="observaciones"
                                name="observaciones" rows="2"
                                placeholder="Observaciones adicionales de la consulta..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 4. TRATAMIENTO Y EVIDENCIAS -->
            <!-- ============================================ -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#e8fff2,#f3fff8);color:#28a745;">
                            <i class="bi bi-capsule"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Tratamiento y evidencias</h6>
                            <small class="text-secondary">Plan de tratamiento y documentación clínica</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="tratamiento" class="form-label small fw-semibold text-secondary">Tratamiento /
                                Receta</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="tratamiento"
                                name="tratamiento" rows="4"
                                placeholder="Medicamentos, dosis, frecuencia y duración del tratamiento..."
                                required></textarea>
                        </div>

                        <!-- EVIDENCIAS -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Evidencias clínicas</label>

                            <div class="evidencias-wrapper">
                                <div class="drop-zone position-relative" id="dropZone">
                                    <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                        id="evidencias" name="evidencias[]" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple
                                        style="cursor:pointer;">

                                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                        <span class="text-muted mt-1">Haz clic o arrastra archivos aquí</span>
                                        <small class="text-muted">JPG, PNG, WebP, PDF — máx. 3 MB cada uno</small>
                                    </div>
                                </div>

                                <div id="previewEvidencias" class="row g-2 mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="d-flex justify-content-end align-items-center gap-2 mt-4 mb-4">
                <button type="button" class="btn btn-light border rounded-pill px-4 py-2 shadow-none"
                    onclick="window.history.back();">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </button>

                <button type="button" id="btnGuardarConsulta" class="btn rounded-pill px-4 py-2 fw-semibold shadow-sm"
                    style="background:linear-gradient(135deg,#376bd8,#2855b8);color:#fff;border:0;">
                    <i class="bi bi-check-lg me-1"></i> Guardar consulta
                </button>
            </div>

        </form>
    </main>

    <!-- Scripts base -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script de la vista -->
    <script>
        /* ============================================================
   consulta.js
   Lógica de la vista de Nueva Consulta Médica
   ============================================================ */

        // ============================================================
        // CONFIGURACIÓN GLOBAL
        // ============================================================
        const CONFIG = {
            URL_CONSULTA: '/myvet/app/controllers/consultaController.php?action=guardarConsulta',
            URL_PACIENTES: '/myvet/app/controllers/consultaController.php?action=pacientes',
            URL_SUBIR_DOCUMENTO: '/myvet/app/controllers/historialExpedienteController.php?action=subirDocumento',
            MAX_SIZE: 3 * 1024 * 1024 // 3 MB
        };

        // ============================================================
        // ESTADO GLOBAL
        // ============================================================
        let archivosEvidencias = [];
        let enviando = false;


        // ============================================================
        // 1. CARGA INICIAL DE DATOS DEL PACIENTE
        // ============================================================
        document.addEventListener('DOMContentLoaded', async function () {

            const params = new URLSearchParams(window.location.search);
            const mascotaId = params.get('id');

            if (!mascotaId) {
                console.warn('No se recibió ID de mascota en la URL.');
                return;
            }

            const selectDueno = document.getElementById('dueno');
            const selectPaciente = document.getElementById('paciente');
            const selectEspecie = document.getElementById('especie');
            const inputRaza = document.getElementById('raza');
            const inputPeso = document.getElementById('peso_kg');
            const inputEdad = document.getElementById('edad');

            try {
                const response = await fetch(CONFIG.URL_PACIENTES, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'No se pudieron obtener los pacientes.');
                }

                const mascota = data.pacientes.find(p => String(p.id) === String(mascotaId));

                if (!mascota) {
                    throw new Error('No se encontró la mascota con el ID ' + mascotaId);
                }

                const propietario = data.propietarios.find(c => String(c.id) === String(mascota.cliente_id));

                // --- Dueño ---
                selectDueno.innerHTML = '';
                const optionDueno = document.createElement('option');
                if (propietario) {
                    optionDueno.value = propietario.id;
                    optionDueno.textContent = propietario.nombre_comercial
                        || propietario.razon_social
                        || 'Sin nombre';
                } else {
                    optionDueno.value = mascota.cliente_id;
                    optionDueno.textContent = mascota.propietario_nombre || 'Propietario no encontrado';
                }
                optionDueno.selected = true;
                selectDueno.appendChild(optionDueno);

                // --- Paciente ---
                selectPaciente.innerHTML = '';
                const optionPaciente = document.createElement('option');
                optionPaciente.value = mascota.id;
                optionPaciente.textContent = mascota.nombre || `${mascota.especie} - ${mascota.raza}`;
                optionPaciente.selected = true;
                selectPaciente.appendChild(optionPaciente);

                // --- Especie, Raza, Peso, Edad ---
                selectEspecie.value = mascota.especie || '';
                inputRaza.value = mascota.raza || '';
                inputPeso.value = mascota.peso || '';
                inputEdad.value = calcularEdad(mascota.fecha_nacimiento);

            } catch (error) {
                console.error('Error al cargar información:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo cargar la mascota',
                    text: error.message
                });
            }
        });


        // ============================================================
        // 2. UTILIDADES
        // ============================================================
        function calcularEdad(fechaNacimiento) {
            if (!fechaNacimiento) return '';

            const nacimiento = new Date(fechaNacimiento + 'T00:00:00');
            const hoy = new Date();

            let años = hoy.getFullYear() - nacimiento.getFullYear();
            let meses = hoy.getMonth() - nacimiento.getMonth();
            let dias = hoy.getDate() - nacimiento.getDate();

            if (dias < 0) meses--;
            if (meses < 0) { años--; meses += 12; }

            if (años === 0) {
                if (meses === 0) return 'Menos de 1 mes';
                return meses === 1 ? '1 mes' : `${meses} meses`;
            }

            if (meses === 0) {
                return años === 1 ? '1 año' : `${años} años`;
            }

            return `${años} años, ${meses} meses`;
        }


        // ============================================================
        // 3. SISTEMA DE EVIDENCIAS
        // ============================================================
        const inputEvidencias = document.getElementById('evidencias');
        const previewEvidencias = document.getElementById('previewEvidencias');
        const dropZone = document.getElementById('dropZone');


        // -------- 3.1 Selección de archivos --------
        inputEvidencias.addEventListener('change', function (e) {
            agregarArchivos(Array.from(e.target.files));
        });


        // -------- 3.2 Drag & Drop --------
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            agregarArchivos(Array.from(e.dataTransfer.files));
        });


        // -------- 3.3 Agregar archivos --------
        function agregarArchivos(nuevos) {
            nuevos.forEach(file => {

                if (file.size > CONFIG.MAX_SIZE) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Archivo muy grande',
                        text: `"${file.name}" supera los 3 MB.`
                    });
                    return;
                }

                const tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
                if (!tiposPermitidos.includes(file.type)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Formato no permitido',
                        text: `"${file.name}" no es un formato válido.`
                    });
                    return;
                }

                const duplicado = archivosEvidencias.some(f =>
                    f.name === file.name && f.size === file.size
                );
                if (duplicado) return;

                archivosEvidencias.push(file);
            });

            sincronizarInput();
            renderPreview();
        }


        // -------- 3.4 Sincronizar input --------
        function sincronizarInput() {
            const dt = new DataTransfer();
            archivosEvidencias.forEach(f => dt.items.add(f));
            inputEvidencias.files = dt.files;
        }


        // -------- 3.5 Renderizar previews --------
        function renderPreview() {
            previewEvidencias.innerHTML = '';

            archivosEvidencias.forEach((file, index) => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 col-lg-2';

                const card = document.createElement('div');
                card.className = 'preview-card';

                const btnRemove = document.createElement('button');
                btnRemove.type = 'button';
                btnRemove.className = 'btn-remove';
                btnRemove.innerHTML = '&times;';
                btnRemove.title = 'Eliminar';
                btnRemove.onclick = () => eliminarArchivo(index);

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    const reader = new FileReader();
                    reader.onload = e => img.src = e.target.result;
                    reader.readAsDataURL(file);
                    card.appendChild(img);
                } else if (file.type === 'application/pdf') {
                    const icon = document.createElement('div');
                    icon.className = 'pdf-icon';
                    icon.innerHTML = '<i class="fas fa-file-pdf"></i>';
                    card.appendChild(icon);
                }

                const info = document.createElement('div');
                info.className = 'file-info';
                info.textContent = `${file.name} (${(file.size / 1024).toFixed(0)} KB)`;
                info.title = file.name;

                card.appendChild(btnRemove);
                card.appendChild(info);
                col.appendChild(card);
                previewEvidencias.appendChild(col);
            });
        }


        // -------- 3.6 Eliminar archivo --------
        function eliminarArchivo(index) {
            archivosEvidencias.splice(index, 1);
            sincronizarInput();
            renderPreview();
        }


        // ============================================================
        // 4. SUBIDA SECUENCIAL DE ARCHIVOS
        // ============================================================
        async function guardarEvidenciasMultiples(pacienteId, consultaId, archivos, onProgreso = null) {

            if (!archivos || archivos.length === 0) {
                return { exitosos: [], fallidos: [] };
            }

            const exitosos = [];
            const fallidos = [];
            const total = archivos.length;

            for (let i = 0; i < total; i++) {
                const archivo = archivos[i];

                try {
                    const data = await guardarEvidenciaSilenciosa(pacienteId, consultaId, archivo);
                    exitosos.push({ archivo: archivo.name, data });

                    if (typeof onProgreso === 'function') {
                        onProgreso(i + 1, total, archivo.name, true);
                    }
                } catch (err) {
                    fallidos.push({ archivo: archivo.name, error: err.message });

                    if (typeof onProgreso === 'function') {
                        onProgreso(i + 1, total, archivo.name, false);
                    }
                }
            }

            return { exitosos, fallidos };
        }


        async function guardarEvidenciaSilenciosa(pacienteId, consultaId, archivo) {
            const formData = new FormData();
            formData.append('pacienteId', pacienteId);
            formData.append('consulta_id', consultaId);
            formData.append('documento', archivo);

            const response = await fetch(CONFIG.URL_SUBIR_DOCUMENTO, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Error subiendo evidencia (${response.status})`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Error al procesar la evidencia.');
            }

            return data;
        }


        // ============================================================
        // 5. GUARDAR CONSULTA + SUBIR EVIDENCIAS
        // ============================================================
        document.getElementById('btnGuardarConsulta').addEventListener('click', enviarDatos);


        async function enviarDatos() {

            if (enviando) return;
            enviando = true;

            const btn = document.getElementById('btnGuardarConsulta');
            const btnOriginal = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

            try {
                // ----- 5.1 FormData -----
                const datos = new FormData();
                datos.append('paciente', document.getElementById('paciente').value);
                datos.append('motivo_consulta', document.getElementById('motivo_consulta').value);
                datos.append('sintomas', document.getElementById('sintomas').value);
                datos.append('explicacion', document.getElementById('explicacion').value);
                datos.append('tratamiento', document.getElementById('tratamiento').value);
                datos.append('peso_kg', document.getElementById('peso_kg').value);
                datos.append('temperatura_c', document.getElementById('temperatura_c').value);
                datos.append('frecuencia_cardiaca', document.getElementById('frecuencia_cardiaca').value);
                datos.append('frecuencia_respiratoria', document.getElementById('frecuencia_respiratoria').value);
                datos.append('observaciones', document.getElementById('observaciones').value);
                datos.append('costo', document.getElementById('costo').value);

                // ----- 5.2 Enviar consulta -----
                const response = await fetch(CONFIG.URL_CONSULTA, {
                    method: 'POST',
                    body: datos
                });

                const resultado = await response.json();

                if (!response.ok || !resultado.success) {
                    throw new Error(resultado.message || 'Error al guardar la consulta.');
                }

                // ----- 5.3 ID de la consulta -----
                const consultaId = resultado.id
                    ?? resultado.consulta_id
                    ?? resultado.data?.id
                    ?? 0;

                const pacienteId = document.getElementById('paciente').value;

                if (!consultaId) {
                    throw new Error('El servidor no devolvió el ID de la consulta.');
                }

                // ----- 5.4 Subir evidencias -----
                if (archivosEvidencias.length > 0) {

                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Subiendo archivos...';

                    const { exitosos, fallidos } = await guardarEvidenciasMultiples(
                        pacienteId,
                        consultaId,
                        archivosEvidencias,
                        (hechos, total) => {
                            btn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Subiendo ${hechos}/${total}...`;
                        }
                    );

                    // ----- 5.5 Feedback -----
                    if (fallidos.length === 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Consulta guardada',
                            text: `Se subieron ${exitosos.length} archivo(s) correctamente.`,
                            timer: 2500,
                            showConfirmButton: false
                        });

                        archivosEvidencias = [];
                        sincronizarInput();
                        renderPreview();

                        setTimeout(() => window.history.back(), 2500);

                    } else if (exitosos.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'La consulta se guardó, pero fallaron los archivos',
                            html: fallidos.map(f => `• <b>${f.archivo}</b>: ${f.error}`).join('<br>')
                        });

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Subida parcial',
                            html: `${exitosos.length} subido(s), ${fallidos.length} fallaron:<br>` +
                                fallidos.map(f => `• <b>${f.archivo}</b>: ${f.error}`).join('<br>')
                        });

                        const nombresFallidos = fallidos.map(f => f.archivo);
                        archivosEvidencias = archivosEvidencias.filter(f =>
                            nombresFallidos.includes(f.name)
                        );
                        sincronizarInput();
                        renderPreview();
                    }

                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Consulta guardada',
                        text: 'No se adjuntaron archivos.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(() => window.history.back(), 2000);
                }

            } catch (error) {
                console.error('Error al enviar la consulta:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });

            } finally {
                enviando = false;
                btn.disabled = false;
                btn.innerHTML = btnOriginal;
            }
        }
    </script>
</body>

</html>