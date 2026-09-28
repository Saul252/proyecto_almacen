<?php
/**
 * clientes_view.php
 * Vista de administración de clientes: Filtros, CRUD por Modales y AJAX.
 * Lógica de permisos: Admin global vs Usuario de sucursal.
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 es Admin
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | myvet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>

    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

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

    /* DataTables Custom */
    

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
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>


    <main class="main-content">
        <!-- Encabezado de la Consulta -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class=" text-uppercase fw-semibold tracking-wider" style="font-size: 0.75rem;">Módulo
                    Clínico</span>
                <h3 class="fw-bold  m-0" style="letter-spacing: -0.5px;">Nueva Consulta Médica</h3>
            </div>
            <button type="button" class="btn btn-light rounded-pill  px-3 py-2 btn-sm text-secondary"
                onclick="window.history.back();">
                <i class="bi bi-x-lg me-1"></i> Cancelar
            </button>
        </div>

        <form>

            <div class="card  shadow-sm rounded-4 mb-4" >

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-4">

                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="
                        width:42px;
                        height:42px;
                        background:linear-gradient(135deg,#eaf2ff,#f3f7ff);
                        color:#356ae6;
                     ">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 fw-bold ">
                                Información del paciente
                            </h6>

                            <small class="text-secondary">
                                Datos generales del paciente y propietario
                            </small>
                        </div>

                    </div>


                    <div class="row g-3">

                        <!-- PROPIETARIO -->
                        <div class="col-md-6">

                            <label for="dueno" class="form-label small fw-semibold text-secondary">

                                Dueño / Propietario

                            </label>

                            <select class="form-select ios-input rounded-3 shadow-none  " id="dueno"
                                name="dueno" required>

                                <option value="">
                                    Seleccionar propietario...
                                </option>

                            </select>

                        </div>


                        <!-- PACIENTE -->
                        <div class="col-md-6">

                            <label for="paciente" class="form-label small fw-semibold text-secondary">

                                Paciente

                            </label>

                            <select class="form-select ios-input rounded-3 shadow-none  " id="paciente"
                                name="paciente" required>

                                <option value="">
                                    Seleccionar paciente...
                                </option>

                            </select>

                        </div>


                        <!-- ESPECIE -->
                        <div class="col-md-3 col-6">

                            <label for="especie" class="form-label small fw-semibold text-secondary">

                                Especie

                            </label>

                            <select class="form-select ios-input rounded-3 shadow-none  " id="especie"
                                name="especie" required>

                                <option value="">
                                    Seleccionar...
                                </option>

                                <option value="Canino">
                                    Canino
                                </option>

                                <option value="Felino">
                                    Felino
                                </option>

                                <option value="Ave">
                                    Ave
                                </option>

                                <option value="Otro">
                                    Otro
                                </option>

                            </select>

                        </div>
                        <div class="col-md-3 col-6">

                            <label for="raza" class="form-label small fw-semibold text-secondary">

                                Raza

                            </label>

                            <input type="text" class="form-control ios-input rounded-3 shadow-none  "
                                id="raza" name="raza" placeholder="Ej. Siamés" required>

                        </div>
                        <div class="col-md-3 col-6">

                            <label for="peso_kg" class="form-label small fw-semibold text-secondary">

                                Peso actual

                            </label>

                            <div class="input-group">

                                <input type="number"
                                    class="form-control ios-input rounded-start-3 shadow-none  "
                                    id="peso_kg" name="peso_kg" step="0.01" min="0" placeholder="3.80">

                                <span class="input-group-text   text-secondary">
                                    kg
                                </span>

                            </div>

                        </div>


                        <!-- EDAD -->
                        <div class="col-md-3 col-6">

                            <label for="edad" class="form-label small fw-semibold text-secondary">

                                Edad

                            </label>

                            <input type="text" class="form-control ios-input rounded-3 shadow-none  "
                                id="edad" name="edad" placeholder="Ej. 3 años" readonly>

                        </div>

                    </div>

                </div>

            </div>
             <div class="card  shadow-sm rounded-4 mb-4" >

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-4">

                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="
                        width:42px;
                        height:42px;
                        background:linear-gradient(135deg,#f3f5ff,#f8f9ff);
                        color:#5967d9;
                     ">

                            <i class="bi bi-clipboard2-pulse-fill"></i>

                        </div>

                        <div>

                            <h6 class="mb-1 fw-bold ">
                                Consulta y signos vitales
                            </h6>

                            <small class="text-secondary">
                                Información clínica obtenida durante la consulta
                            </small>

                        </div>

                    </div>


                    <div class="row g-3">

                        <!-- MOTIVO -->
                        <div class="col-12">

                            <label for="motivo_consulta" class="form-label small fw-semibold text-secondary">

                                Motivo de la consulta

                            </label>

                            <textarea class="form-control ios-input rounded-3 shadow-none  "
                                id="motivo_consulta" name="motivo_consulta" rows="2"
                                placeholder="¿Por qué se presenta el paciente a consulta?" required></textarea>

                        </div>


                        <!-- SEPARADOR -->
                        <div class="col-12 mt-3">

                            <div class="d-flex align-items-center">

                                <span class="small fw-bold ">
                                    Signos vitales
                                </span>

                                <div class="flex-grow-1 ms-3" style="height:1px;background:#eef0f4;">
                                </div>

                            </div>

                        </div>


                        <!-- TEMPERATURA -->
                        <div class="col-md-3 col-6">

                            <label for="temperatura_c" class="form-label small fw-semibold text-secondary">

                                Temperatura

                            </label>

                            <div class="input-group">

                                <input type="number"
                                    class="form-control ios-input rounded-start-3 shadow-none  "
                                    id="temperatura_c" name="temperatura_c" step="0.1" min="20" max="50"
                                    placeholder="38.5">

                                <span class="input-group-text   text-secondary">
                                    °C
                                </span>

                            </div>

                        </div>


                        <!-- CARDIACA -->
                        <div class="col-md-3 col-6">

                            <label for="frecuencia_cardiaca" class="form-label small fw-semibold text-secondary">

                                Frecuencia cardíaca

                            </label>

                            <div class="input-group">

                                <input type="number"
                                    class="form-control ios-input rounded-start-3 shadow-none  "
                                    id="frecuencia_cardiaca" name="frecuencia_cardiaca" min="0" placeholder="90">

                                <span class="input-group-text   text-secondary">
                                    lpm
                                </span>

                            </div>

                        </div>


                        <!-- RESPIRATORIA -->
                        <div class="col-md-3 col-6">

                            <label for="frecuencia_respiratoria" class="form-label small fw-semibold text-secondary">

                                Frecuencia respiratoria

                            </label>

                            <div class="input-group">

                                <input type="number"
                                    class="form-control ios-input rounded-start-3 shadow-none  "
                                    id="frecuencia_respiratoria" name="frecuencia_respiratoria" min="0"
                                    placeholder="25">

                                <span class="input-group-text   text-secondary">
                                    rpm
                                </span>

                            </div>

                        </div>


                        <!-- COSTO -->
                        <div class="col-md-3 col-6">

                            <label for="costo" class="form-label small fw-semibold text-secondary">

                                Costo de consulta

                            </label>

                            <div class="input-group">

                                <span class="input-group-text   text-secondary">
                                    $
                                </span>

                                <input type="number"
                                    class="form-control ios-input rounded-end-3 shadow-none  "
                                    id="costo" name="costo" step="0.01" min="0" placeholder="0.00">

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- 3. ANAMNESIS Y DIAGNÓSTICO -->
            <!-- ===================================================== -->

            <div class="card  shadow-sm rounded-4 mb-4" >

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-4">

                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="
                        width:42px;
                        height:42px;
                        background:linear-gradient(135deg,#fff7e8,#fffaf1);
                        color:#d99418;
                     ">

                            <i class="bi bi-file-earmark-medical-fill"></i>

                        </div>

                        <div>

                            <h6 class="mb-1 fw-bold ">
                                Anamnesis y diagnóstico
                            </h6>

                            <small class="text-secondary">
                                Evaluación clínica y hallazgos del paciente
                            </small>

                        </div>

                    </div>


                    <div class="row g-3">

                        <!-- SÍNTOMAS -->
                        <div class="col-12">

                            <label for="sintomas" class="form-label small fw-semibold text-secondary">

                                Síntomas reportados

                            </label>

                            <textarea class="form-control ios-input rounded-3 shadow-none  "
                                id="sintomas" name="sintomas" rows="3"
                                placeholder="Describa los signos o síntomas que presenta el paciente..."
                                required></textarea>

                        </div>


                        <!-- DIAGNÓSTICO -->
                        <div class="col-12">

                            <label for="explicacion" class="form-label small fw-semibold text-secondary">

                                Diagnóstico

                            </label>

                            <textarea class="form-control ios-input rounded-3 shadow-none  "
                                id="explicacion" name="explicacion" rows="3"
                                placeholder="Evaluación médica, diagnóstico presuntivo o definitivo..."
                                required></textarea>

                        </div>


                        <!-- OBSERVACIONES -->
                        <div class="col-12">

                            <label for="observaciones" class="form-label small fw-semibold text-secondary">

                                Observaciones

                            </label>

                            <textarea class="form-control ios-input rounded-3 shadow-none  "
                                id="observaciones" name="observaciones" rows="2"
                                placeholder="Observaciones adicionales de la consulta..."></textarea>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card  shadow-sm rounded-4 mb-4" >

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-4">

                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="
                        width:42px;
                        height:42px;
                        
                     ">

                            <i class="bi bi-capsule"></i>

                        </div>

                        <div>

                            <h6 class="mb-1 fw-bold ">
                                Tratamiento y evidencias
                            </h6>

                            <small class="text-secondary">
                                Plan de tratamiento y documentación clínica
                            </small>

                        </div>

                    </div>


                    <div class="row g-3">

                        <!-- TRATAMIENTO -->
                        <div class="col-12">

                            <label for="tratamiento" class="form-label small fw-semibold text-secondary">

                                Tratamiento / Receta

                            </label>

                            <textarea class="form-control ios-input rounded-3 shadow-none  "
                                id="tratamiento" name="tratamiento" rows="4"
                                placeholder="Medicamentos, dosis, frecuencia y duración del tratamiento..."
                                required></textarea>

                        </div>


                        <!-- EVIDENCIAS -->
                        <div class="col-12">

                            <label for="evidencias" class="form-label small fw-semibold text-secondary">

                                Evidencias

                            </label>

                            <div class="rounded-4 p-4 text-center position-relative" style="
                            background:#f8fafc;
                            border:1px dashed #d8dee8;
                            transition:all .2s ease;
                         ">

                                <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    id="evidencias" name="evidencias[]" accept=".jpg,.jpeg,.png,.pdf" multiple
                                    style="cursor:pointer;">

                                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                                    style="
                                width:48px;
                                height:48px;
                                background:#edf3ff;
                                color:#4775d1;
                             ">

                                    <i class="bi bi-cloud-arrow-up fs-5"></i>

                                </div>

                                <div class="fw-semibold small  mb-1">
                                    Agregar archivos
                                </div>

                                <div class="small text-secondary">
                                    Arrastra tus archivos aquí o haz clic para seleccionarlos
                                </div>

                                <div class="mt-2 text-muted" style="font-size:.72rem;">

                                    JPG, PNG o PDF

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="d-flex justify-content-end align-items-center gap-2 mt-4 mb-4">

                <button type="button" class="btn btn-light border rounded-pill px-4 py-2 shadow-none">

                    <i class="bi bi-x-lg me-1"></i>
                    Cancelar

                </button>


                <button onclick="enviarDatos()" class="btn rounded-pill px-4 py-2 fw-semibold shadow-sm" style="
                background:linear-gradient(135deg,#376bd8,#2855b8);
                color:#fff;
                border:0;
            ">

                    <i class="bi bi-check-lg me-1"></i>
                    Guardar consulta

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
    let tabla;




    function nuevoCliente() {
        $('#formCliente')[0].reset();
        $('#cliente_id').val('0');
        $('#modalTitulo').text('Nuevo Registro de Cliente');

        // Auto-seleccionar almacén si hay filtro activo
        const filtro = $('#filtroAlmacenVista').val();
        if (filtro) $('#almacen_id_modal').val(filtro);

        $('#modalCliente').modal('show');
    }


    document.addEventListener('DOMContentLoaded', async function() {

        // ==========================================
        // 1. OBTENER ID DE LA MASCOTA DESDE LA URL
        // ==========================================

        const parametros = new URLSearchParams(window.location.search);
        const mascotaId = parametros.get('id');

        console.log('ID de mascota recibido:', mascotaId);

        if (!mascotaId) {
            console.warn('No se recibió un ID de mascota en la URL.');
            return;
        }


        // ==========================================
        // 2. REFERENCIAS A LOS CAMPOS
        // ==========================================

        const selectDueno = document.getElementById('dueno');
        const selectPaciente = document.getElementById('paciente');
        const selectEspecie = document.getElementById('especie');
        const inputRaza = document.getElementById('raza');
        const inputTamano = document.getElementById('peso_kg');
        const inputEdad = document.getElementById('edad');


        try {

            // ==========================================
            // 3. CONSULTAR MASCOTAS Y CLIENTES
            // ==========================================

            const response = await fetch(
                '/myvet/app/controllers/consultaController.php?action=pacientes', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            if (!response.ok) {
                throw new Error(
                    `Error HTTP ${response.status}: ${response.statusText}`
                );
            }

            const data = await response.json();

            console.log('Respuesta del servidor:', data);


            if (!data.success) {
                throw new Error(
                    data.message || 'No se pudieron obtener los pacientes.'
                );
            }


            // ==========================================
            // 4. BUSCAR LA MASCOTA POR ID
            // ==========================================

            const mascota = data.pacientes.find(
                p => String(p.id) === String(mascotaId)
            );


            if (!mascota) {
                throw new Error(
                    'No se encontró la mascota con el ID ' + mascotaId
                );
            }


            console.log('Mascota encontrada:', mascota);


            // ==========================================
            // 5. BUSCAR AL PROPIETARIO
            // ==========================================

            const propietario = data.propietarios.find(
                c => String(c.id) === String(mascota.cliente_id)
            );


            console.log('Propietario encontrado:', propietario);


            // ==========================================
            // 6. LLENAR DUEÑO
            // ==========================================

            selectDueno.innerHTML = '';

            if (propietario) {

                const optionDueno = document.createElement('option');

                optionDueno.value = propietario.id;

                optionDueno.textContent =
                    propietario.nombre_comercial ||
                    propietario.razon_social ||
                    'Sin nombre';

                optionDueno.selected = true;

                selectDueno.appendChild(optionDueno);

            } else {

                const optionDueno = document.createElement('option');

                optionDueno.value = mascota.cliente_id;
                optionDueno.textContent =
                    mascota.propietario_nombre || 'Propietario no encontrado';

                optionDueno.selected = true;

                selectDueno.appendChild(optionDueno);
            }


            // ==========================================
            // 7. LLENAR PACIENTE
            // ==========================================

            selectPaciente.innerHTML = '';

            const optionPaciente = document.createElement('option');

            optionPaciente.value = mascota.id;

            optionPaciente.textContent =
                mascota.nombre ||
                `${mascota.especie} - ${mascota.raza}`;

            optionPaciente.selected = true;

            selectPaciente.appendChild(optionPaciente);


            // ==========================================
            // 8. LLENAR ESPECIE
            // ==========================================

            selectEspecie.value = mascota.especie || '';


            // ==========================================
            // 9. LLENAR RAZA
            // ==========================================

            inputRaza.value = mascota.raza || '';


            // ==========================================
            // 10. LLENAR PESO
            // ==========================================

            if (mascota.peso) {
                inputTamano.value = mascota.peso + ' kg';
            } else {
                inputTamano.value = '';
            }


            // ==========================================
            // 11. CALCULAR EDAD
            // ==========================================

            inputEdad.value = calcularEdad(mascota.fecha_nacimiento);


            // ==========================================
            // 12. MOSTRAR INFORMACIÓN EN CONSOLA
            // ==========================================

            console.log('Datos cargados correctamente');
            console.log({
                mascota: mascota,
                propietario: propietario,
                especie: mascota.especie,
                raza: mascota.raza,
                peso: mascota.peso,
                edad: inputEdad.value
            });

        } catch (error) {

            console.error('Error al cargar información:', error);

            Swal.fire({
                icon: 'error',
                title: 'No se pudo cargar la mascota',
                text: error.message
            });
        }


        // ==========================================
        // FUNCIÓN PARA CALCULAR EDAD
        // ==========================================

        function calcularEdad(fechaNacimiento) {

            if (!fechaNacimiento) {
                return '';
            }

            const nacimiento = new Date(fechaNacimiento + 'T00:00:00');
            const hoy = new Date();

            let años = hoy.getFullYear() - nacimiento.getFullYear();

            let meses =
                hoy.getMonth() - nacimiento.getMonth();

            let dias =
                hoy.getDate() - nacimiento.getDate();


            if (dias < 0) {
                meses--;
            }

            if (meses < 0) {
                años--;
                meses += 12;
            }


            // Menor de un año
            if (años === 0) {

                if (meses === 0) {
                    return 'Menos de 1 mes';
                }

                return meses === 1 ?
                    '1 mes' :
                    `${meses} meses`;
            }


            // Un año o más
            if (meses === 0) {

                return años === 1 ?
                    '1 año' :
                    `${años} años`;
            }


            return `${años} años, ${meses} meses`;
        }

    });
    </script>
    <script>
   async function enviarDatos() {
    try {
        // 1. Obtener referencia al formulario si existe
        const formulario = document.getElementById('formConsulta');

        // 2. Crear FormData y agregar campos por su ID
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

        // Adjuntar archivos si el input existe
        const inputEvidencias = document.getElementById('evidencias');
        if (inputEvidencias && inputEvidencias.files.length > 0) {
            for (let i = 0; i < inputEvidencias.files.length; i++) {
                datos.append('evidencias[]', inputEvidencias.files[i]);
            }
        }

        // 3. Determinar la URL y el método
        const urlTarget = formulario 
            ? formulario.action 
            : '/myvet/app/controllers/consultaController.php?action=guardarConsulta';
            
        const metodo = formulario ? formulario.method : 'POST';

        // 4. Enviar datos (Sin headers manuales de Content-Type)
        const response = await fetch(urlTarget, {
            method: metodo,
            body: datos
        });

        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'Error al guardar la consulta.');
        }

        alert('Consulta guardada exitosamente');

    } catch (error) {
        console.error('Error al enviar la consulta:', error);
        alert(error.message);
    }
}  </script>

</body>

</html>