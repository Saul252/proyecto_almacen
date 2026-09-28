<?php
/**
 * consulta_dental_view.php
 * Vista para el registro de consultas odontológicas / dentales.
 * Trabaja directamente con la tabla `clientes` mediante consultaDentalController.php
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Dental | Sistema Odontológico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #d1d1d6;
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
        renderizarLayout($paginaActual ?? '');
    } ?>

    <main class="main-content">
        <!-- Encabezado -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="text-uppercase fw-semibold tracking-wider text-secondary"
                    style="font-size: 0.75rem;">Módulo Clínico - Odontología</span>
                <h3 class="fw-bold m-0" style="letter-spacing: -0.5px;">Nueva Consulta Dental</h3>
            </div>
            <button type="button" class="btn btn-light rounded-pill px-3 py-2 btn-sm text-secondary"
                onclick="window.history.back();">
                <i class="bi bi-x-lg me-1"></i> Cancelar
            </button>
        </div>

        <!-- Select de almacén oculto/controlado si se requiere para la lógica de JS -->
        <div class="d-none">
            <select name="almacen_id_editar" id="almacen_id_editar" class="form-select">
                <?php if (!empty($almacenes)):
                    foreach ($almacenes as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= ($a['id'] == $almacen_usuario) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nombre']) ?></option>
                    <?php endforeach; endif; ?>
            </select>
        </div>

        <form id="formConsulta" action="/myvet/app/controllers/consultaDentalController.php?action=guardarConsulta"
            method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); enviarDatos();">

            <!-- 1. INFORMACIÓN DEL CLIENTE (PACIENTE) -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px; height:42px; background:linear-gradient(135deg,#eaf2ff,#f3f7ff); color:#356ae6;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Información del Paciente / Cliente</h6>
                            <small class="text-secondary">Selección y datos generales del cliente</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- CLIENTE -->
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label small fw-semibold text-secondary">Paciente
                                (Cliente)</label>
                            <select class="form-select ios-input rounded-3 shadow-none" id="cliente_id"
                                name="cliente_id" required onchange="cargarDatosCliente(this.value)">
                                <option value="">Seleccionar paciente...</option>
                            </select>
                        </div>

                        <!-- Campos ocultos requeridos por JavaScript para evitar errores de referencias nulas -->
                        <input type="hidden" id="rfc" name="rfc">
                        <input type="hidden" id="telefono" name="telefono">
                        <input type="hidden" id="email" name="email">
                    </div>
                </div>
            </div>

            <!-- 2. CONSULTA Y SIGNOS VITALES -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px; height:42px; background:linear-gradient(135deg,#f3f5ff,#f8f9ff); color:#5967d9;">
                            <i class="bi bi-clipboard2-pulse-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Consulta dental y signos vitales</h6>
                            <small class="text-secondary">Evaluación inicial y toma de signos</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="motivo_consulta" class="form-label small fw-semibold text-secondary">Motivo de
                                la consulta dental</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="motivo_consulta"
                                name="motivo_consulta" rows="2"
                                placeholder="¿Por qué se presenta el paciente a consulta odontológica?"
                                required></textarea>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="d-flex align-items-center">
                                <span class="small fw-bold text-dark">Signos Vitales</span>
                                <div class="flex-grow-1 ms-3" style="height:1px;background:#eef0f4;"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-6">
                            <label for="presion_arterial" class="form-label small fw-semibold text-secondary">Presión
                                Arterial</label>
                            <div class="input-group">
                                <input type="text" class="form-control ios-input rounded-start-3 shadow-none"
                                    id="presion_arterial" name="presion_arterial" placeholder="120/80">
                                <span class="input-group-text text-secondary">mmHg</span>
                            </div>
                        </div>

                        <div class="col-md-4 col-6">
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

            <!-- 3. ANAMNESIS Y DIAGNÓSTICO -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px; height:42px; background:linear-gradient(135deg,#fff7e8,#fffaf1); color:#d99418;">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Anamnesis y diagnóstico dental</h6>
                            <small class="text-secondary">Antecedentes, hallazgos clínicos y piezas afectadas</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="antecedentes_medicos"
                                class="form-label small fw-semibold text-secondary">Antecedentes Médicos</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="antecedentes_medicos"
                                name="antecedentes_medicos" rows="2"
                                placeholder="Enfermedades crónicas, alergias a medicamentos..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="antecedentes_dentales"
                                class="form-label small fw-semibold text-secondary">Antecedentes Dentales</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="antecedentes_dentales"
                                name="antecedentes_dentales" rows="2"
                                placeholder="Tratamientos previos, ortodoncia, cirugías bucales..."></textarea>
                        </div>

                        <div class="col-12">
                            <label for="sintomas" class="form-label small fw-semibold text-secondary">Síntomas
                                Reportados / Hallazgos</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="sintomas" name="sintomas"
                                rows="2" placeholder="Dolor localizado, sangrado gingival, sensibilidad..."
                                required></textarea>
                        </div>

                        <div class="col-12">
                            <label for="piezas_dentales" class="form-label small fw-semibold text-secondary">Piezas
                                Dentales Afectadas</label>
                            <input type="text" class="form-control ios-input rounded-3 shadow-none" id="piezas_dentales"
                                name="piezas_dentales" placeholder="Ej. 18, 21, 46...">
                        </div>

                        <div class="col-12">
                            <label for="diagnostico" class="form-label small fw-semibold text-secondary">Diagnóstico
                                Dental</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="diagnostico"
                                name="diagnostico" rows="3"
                                placeholder="Caries de 3er grado, gingivitis, periodontitis..." required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. TRATAMIENTO Y PLAN -->
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width:42px; height:42px; background:linear-gradient(135deg,#eafbee,#f4fcf6); color:#28a745;">
                            <i class="bi bi-capsule"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Procedimiento y plan de tratamiento</h6>
                            <small class="text-secondary">Procedimientos realizados y evolución</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="procedimiento_realizado"
                                class="form-label small fw-semibold text-secondary">Procedimiento Realizado</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="procedimiento_realizado"
                                name="procedimiento_realizado" rows="3"
                                placeholder="Limpieza, endodoncia, resina, extracción..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="plan_tratamiento" class="form-label small fw-semibold text-secondary">Plan de
                                Tratamiento / Siguientes Pasos</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="plan_tratamiento"
                                name="plan_tratamiento" rows="3"
                                placeholder="Plan para citas posteriores..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="observaciones" class="form-label small fw-semibold text-secondary">Observaciones
                                Adicionales</label>
                            <textarea class="form-control ios-input rounded-3 shadow-none" id="observaciones"
                                name="observaciones" rows="3"
                                placeholder="Notas de seguimiento o recomendaciones al paciente..."></textarea>
                        </div>

                        <div class="col-12">
                            <label for="evidencias" class="form-label small fw-semibold text-secondary">Evidencias
                                (Fotos / Radiografías)</label>

                            <div class="rounded-4 p-4 text-center position-relative mb-3"
                                style="background:#f8fafc; border:1px dashed #d8dee8; transition:all .2s ease;">
                                <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    id="evidencias" name="evidencias[]" accept=".jpg,.jpeg,.png,.pdf" multiple
                                    style="cursor:pointer; z-index: 2;">

                                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                                    style="width:48px; height:48px; background:#edf3ff; color:#4775d1;">
                                    <i class="bi bi-cloud-arrow-up fs-5"></i>
                                </div>

                                <div class="fw-semibold small mb-1">Agregar archivos</div>
                                <div class="small text-secondary">Arrastra radiografías o fotografías dentales aquí o
                                    haz clic</div>
                                <div class="mt-2 text-muted" style="font-size:.72rem;">JPG, PNG o PDF</div>
                            </div>

                            <div id="lista_evidencias" class="row g-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-2 mt-4 mb-4">
                <button type="button" class="btn btn-light border rounded-pill px-4 py-2 shadow-none"
                    onclick="window.history.back();">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </button>

                <button type="submit" class="btn rounded-pill px-4 py-2 fw-semibold shadow-sm"
                    style="background:linear-gradient(135deg,#376bd8,#2855b8); color:#fff; border:0;">
                    <i class="bi bi-check-lg me-1"></i> Guardar consulta dental
                </button>
            </div>

        </form>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let archivosSeleccionados = [];

        document.addEventListener('DOMContentLoaded', async function () {
            const inputEvidencias = document.getElementById('evidencias');

            if (inputEvidencias) {
                inputEvidencias.addEventListener('change', function (e) {
                    const nuevosArchivos = Array.from(e.target.files);

                    nuevosArchivos.forEach(archivo => {
                        const existe = archivosSeleccionados.some(a => a.name === archivo.name && a.size === archivo.size);
                        if (!existe) {
                            archivosSeleccionados.push(archivo);
                        }
                    });

                    inputEvidencias.value = '';
                    renderizarPrevisualizaciones();
                });
            }

            const parametros = new URLSearchParams(window.location.search);
            const clienteIdUrl = parametros.get('id') ?? 0;
            const razon = parametros.get('razon') ?? '';
            $('#motivo_consulta').val(razon);

            await cargarClientes(clienteIdUrl);

            if (window.location.search) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        function renderizarPrevisualizaciones() {
            const contenedor = document.getElementById('lista_evidencias');
            if (!contenedor) return;

            contenedor.innerHTML = '';

            archivosSeleccionados.forEach((archivo, index) => {
                const esImagen = archivo.type.startsWith('image/');
                const esPdf = archivo.type === 'application/pdf';

                const col = document.createElement('div');
                col.className = 'col-6 col-md-3';

                let thumbnailHtml = '';

                if (esImagen) {
                    const urlImagen = URL.createObjectURL(archivo);
                    thumbnailHtml = `<img src="${urlImagen}" class="rounded-3 mb-2" style="width: 100%; height: 90px; object-fit: cover;">`;
                } else if (esPdf) {
                    thumbnailHtml = `
                    <div class="rounded-3 mb-2 d-flex align-items-center justify-content-center bg-light text-danger" style="height: 90px;">
                        <i class="bi bi-file-earmark-pdf-fill fs-1"></i>
                    </div>`;
                } else {
                    thumbnailHtml = `
                    <div class="rounded-3 mb-2 d-flex align-items-center justify-content-center bg-light text-secondary" style="height: 90px;">
                        <i class="bi bi-file-earmark-text-fill fs-1"></i>
                    </div>`;
                }

                col.innerHTML = `
                <div class="card h-100 shadow-sm border-0 p-2 position-relative" style="background: #ffffff; border-radius: 12px; z-index: 3;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center" 
                            style="width: 22px; height: 22px; z-index: 10;" onclick="eliminarArchivo(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                    ${thumbnailHtml}
                    <div class="text-truncate small fw-semibold text-dark px-1" title="${archivo.name}">${archivo.name}</div>
                    <div class="text-muted px-1" style="font-size: 0.7rem;">${(archivo.size / 1024).toFixed(1)} KB</div>
                </div>
            `;

                contenedor.appendChild(col);
            });
        }

        function eliminarArchivo(index) {
            archivosSeleccionados.splice(index, 1);
            renderizarPrevisualizaciones();
        }

        let listaClientes = [];

        async function cargarClientes(clienteIdSeleccionar = null) {
            const almacenId = $('#almacen_id_editar').val();
            const select = document.getElementById('cliente_id');
            if (!select) return;

            select.innerHTML = '<option value="">-- Seleccione un cliente --</option>';

            try {
                const url = `/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${almacenId}`;
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();

                if (resultado.success && Array.isArray(resultado.data)) {
                    listaClientes = resultado.data;

                    listaClientes.forEach(cliente => {
                        const opcion = document.createElement('option');
                        opcion.value = Number(cliente.id);
                        opcion.textContent = cliente.nombre_comercial;
                        select.appendChild(opcion);
                    });

                    if (clienteIdSeleccionar !== null && clienteIdSeleccionar !== '') {
                        const idBuscado = parseInt(clienteIdSeleccionar, 10);

                        if (!isNaN(idBuscado)) {
                            const coincidentes = listaClientes.filter(c => Number(c.id) === idBuscado);

                            if (coincidentes.length > 0) {
                                const clienteEncontrado = coincidentes[0];
                                select.value = idBuscado;
                                cargarDatosCliente(idBuscado);
                            }
                        }
                    }
                } else {
                    select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                }
            } catch (error) {
                select.innerHTML = '<option value="">Error al cargar la lista</option>';
                console.error('Error al ejecutar cargarClientes:', error);
            }
        }

        function cargarDatosCliente(id) {
            const cliente = listaClientes.find(c => String(c.id) === String(id));
            const elemRfc = document.getElementById('rfc');
            const elemTel = document.getElementById('telefono');
            const elemEmail = document.getElementById('email');

            if (elemRfc) elemRfc.value = cliente ? (cliente.rfc || '') : '';
            if (elemTel) elemTel.value = cliente ? (cliente.telefono || '') : '';
            if (elemEmail) elemEmail.value = cliente ? (cliente.email || '') : '';
        }

        async function guardarEvidenciaSilenciosa(pacienteId, consultaId, archivo) {
            const formData = new FormData();
            formData.append('pacienteId', pacienteId);
            formData.append('consulta_id', consultaId);
            formData.append('documento', archivo);

            const response = await fetch('/myvet/app/controllers/historialDentalController.php?action=subirDocumento', {
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

        async function enviarDatos() {
            try {
                const clienteId = document.getElementById('cliente_id')?.value;

                if (!clienteId) {
                    Swal.fire({ icon: 'warning', title: 'Atención', text: 'Por favor seleccione un paciente/cliente.' });
                    return;
                }

                const datos = new FormData();
                datos.append('paciente_id', clienteId);
                datos.append('motivo_consulta', document.getElementById('motivo_consulta')?.value || '');
                datos.append('antecedentes_medicos', document.getElementById('antecedentes_medicos')?.value || '');
                datos.append('antecedentes_dentales', document.getElementById('antecedentes_dentales')?.value || '');
                datos.append('sintomas', document.getElementById('sintomas')?.value || '');
                datos.append('piezas_dentales', document.getElementById('piezas_dentales')?.value || '');
                datos.append('diagnostico', document.getElementById('diagnostico')?.value || '');
                datos.append('procedimiento_realizado', document.getElementById('procedimiento_realizado')?.value || '');
                datos.append('plan_tratamiento', document.getElementById('plan_tratamiento')?.value || '');
                datos.append('observaciones', document.getElementById('observaciones')?.value || '');
                datos.append('presion_arterial', document.getElementById('presion_arterial')?.value || '');
                datos.append('costo', document.getElementById('costo')?.value || '0');

                Swal.fire({
                    title: 'Guardando consulta dental...',
                    text: 'Por favor espere un momento.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const response = await fetch('/myvet/app/controllers/consultaDentalController.php?action=guardarConsulta', {
                    method: 'POST',
                    body: datos
                });

                const resultado = await response.json();

                if (!response.ok || !resultado.success) {
                    throw new Error(resultado.message || `Error en el servidor: ${response.status}`);
                }

                const consultaId = resultado.id;

                if (typeof archivosSeleccionados !== 'undefined' && archivosSeleccionados.length > 0) {
                    for (let i = 0; i < archivosSeleccionados.length; i++) {
                        await guardarEvidenciaSilenciosa(clienteId, consultaId, archivosSeleccionados[i]);
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: '¡Consulta guardada!',
                    text: resultado.message || 'La consulta dental y sus evidencias se registraron correctamente.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.history.back();
                });

            } catch (error) {
                console.error('Error al guardar consulta dental:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al guardar',
                    text: error.message || 'Ocurrió un problema desconocido.'
                });
            }
        }
    </script>
</body>

</html>