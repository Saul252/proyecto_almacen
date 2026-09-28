<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mascotas | myvet</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
            --navbar-height: 65px;
            --apple-bg: #f4f5f8;
            --accent-blue: #0071e3;
            --accent-blue-hover: #005bb5;
            --card-bg: rgba(255, 255, 255, 0.85);
            --border-radius-lg: 20px;
        }

        body { 
           
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
           
            -webkit-font-smoothing: antialiased;
        }

        .main-content { 
            padding: 30px; 
            padding-top: calc(var(--navbar-height) + 15px); 
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Tarjeta Estilo Glassmorphism */
        .card-premium { 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            border-radius: var(--border-radius-lg); 
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.04); 
          
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Avatar de Mascota Mejora */
        .avatar-mascota-wrapper {
            position: relative;
            display: inline-block;
        }

        .avatar-mascota {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
        }

        .avatar-mascota:hover {
            transform: scale(1.08);
        }

        .avatar-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 14px;
          
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Badges estilizados */
        .badge-especie { 
            background: rgba(0, 113, 227, 0.08); 
            color: var(--accent-blue); 
            border: 1px solid rgba(0, 113, 227, 0.15); 
            padding: 0.35rem 0.65rem;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-sexo {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
        }
        .badge-macho { background-color: #e3f2fd; color: #0d6efd; }
        .badge-hembra { background-color: #fce4ec; color: #d63384; }

        /* Estilos de Tabla */
        
        .table thead th {
           
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            
            font-weight: 700;
            border-bottom: 1px solid #e5e5ea;
            padding: 14px 16px;
        }

        .table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(229, 229, 234, 0.6);
        }

        

        
        /* Botones Acción */
       
        
       

       

      
        .action-btn-group {
           
            border: 1px solid #e5e5ea;
            border-radius: 30px;
            padding: 3px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .action-btn-group .btn {
            
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 20px;
            padding: 5px 12px;
        }

        /* Custom Switch */
        .form-switch .form-check-input {
            width: 2.3em;
            height: 1.3em;
            cursor: pointer;
        }

        @media (max-width: 768px) { 
            .main-content { padding: 15px; padding-top: 80px; } 
        }
        
    </style>
</head><body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        
        <!-- HEADER PRINCIPAL -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold m-0" style="font-size: 1.8rem; letter-spacing: -0.02em;">Directorio de Mascotas</h2>
                <p class=" mb-0 small">Gestión integral de expedientes clínicos y pacientes</p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-apple-secondary px-3 py-2 d-flex align-items-center gap-2" 
                        type="button" onclick="abrirModalNuevoCliente()" title="Nuevo Propietario">
                    <i class="bi bi-person-plus-fill text-primary"></i>
                    <span class="d-none d-sm-inline">Nuevo Propietario</span>
                </button>
                
                <button class="btn btn-apple-primary px-4 py-2 d-flex align-items-center gap-2 shadow-sm" 
                        onclick="nuevaMascota()">
                    <i class="bi bi-plus-lg fs-6"></i>
                    <span>Nueva Paciente</span>
                </button>
            </div>
        </div>

        <!-- TARJETA CONTENEDORA DE TABLA Y FILTROS -->
        <div class="card card-premium p-3 p-md-4 animate__animated animate__fadeInUp">
            
            <!-- BARRA DE BÚSQUEDA Y FILTROS EN TIEMPO REAL -->
            <div class="row mb-4 g-3 align-items-center">
                <!-- Búsqueda General -->
                <div class="col-12 col-md-5 col-lg-5">
                    <div class="input-group  rounded-3 border p-1 shadow-sm">
                        <span class="input-group-text bg-transparent  text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaMascota" class="form-control  bg-transparent shadow-none" placeholder="Buscar por nombre, especie, raza o propietario...">
                    </div>
                </div>
                 <select name="almacen_id" id="almacen_id"
                                    class="form-select  shadow-sm rounded-3 py-2" required>

                                    <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>

                <!-- Filtro por Propietario -->
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="input-group  rounded-3 border p-1 shadow-sm">
                        <span class="input-group-text bg-transparent  text-muted"><i class="bi bi-filter-circle"></i></span>
                        <select id="filtroCliente" class="form-select  bg-transparent shadow-none" onchange="obtenerMascotasAjax()">
                            <option value="">-- Todos los Propietarios --</option>
                        </select>
                    </div>
                </div>

                <!-- Botón Resetear -->
                <div class="col-12 col-md-3 col-lg-3 text-end ms-auto">
                    <button class="btn btn-apple-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2" onclick="limpiarFiltros()">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Resetear</span>
                    </button>
                </div>
            </div>

            <!-- TABLA DE MASCOTAS RESPONSIVE -->
            <div class="table-responsive">
                <table id="tablaMascotas" class="table align-middle w-100">
                    <thead>
                        <tr>
                            <th class="ps-3 text-center" style="width: 70px;">Paciente</th>
                            <th>Información General</th>
                            <th>Especie / Raza</th>
                            <th>Propietario</th>
                            <th>Estado</th>
                            <th class="text-end pe-3" style="width: 250px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Carga dinámica vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL MASCOTA REGISTRO / EDICIÓN -->
    <div class="modal fade" id="modalMascota" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <form id="formMascota" enctype="multipart/form-data">
                    <div class="modal-header bg-dark text-white  py-3 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-paw-fill text-primary fs-5"></i>
                            <h5 class="modal-title fw-bold m-0" id="modalTitulo">Nueva Mascota</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <input type="hidden" name="mascota_id" id="mascota_id" value="0">
                        
                        <div class="row g-3">
                            <!-- SELECTOR DE PROPIETARIO -->
                            <div class="col-12">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-4 border border-primary border-opacity-20">
                                    <label class="form-label small fw-bold text-primary mb-1"><i class="bi bi-person-circle me-1"></i> PROPIETARIO *</label>
                                    <select name="cliente_id" id="cliente_id" class="form-select border-primary shadow-none" required>
                                        <option value="">-- Seleccionar Propietario --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- INFORMACIÓN DEL PACIENTE -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">NOMBRE DEL PACIENTE *</label>
                                <input type="text" name="nombre" id="nombre" class="form-control rounded-3" placeholder="Ej. Firulais" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">ESPECIE *</label>
                                <select name="especie" id="especie" class="form-select rounded-3" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Perro">Perro</option>
                                    <option value="Gato">Gato</option>
                                    <option value="Ave">Ave</option>
                                    <option value="Roedor">Roedor</option>
                                    <option value="Reptil">Reptil</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">RAZA</label>
                                <input type="text" name="raza" id="raza" class="form-control rounded-3" placeholder="Ej. Poodle, Mestizo...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">FECHA DE NACIMIENTO</label>
                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control rounded-3">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">SEXO</label>
                                <select name="sexo" id="sexo" class="form-select rounded-3">
                                    <option value="Desconocido">Desconocido</option>
                                    <option value="Macho">Macho</option>
                                    <option value="Hembra">Hembra</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">PESO (KG)</label>
                                <input type="number" step="0.01" name="peso" id="peso" class="form-control rounded-3" placeholder="0.00">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">COLOR</label>
                                <input type="text" name="color" id="color" class="form-control rounded-3" placeholder="Ej. Marrón / Blanco">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">SEÑAS PARTICULARES / NOTAS</label>
                                <textarea name="senas_particulares" id="senas_particulares" class="form-control rounded-3" rows="2" placeholder="Cicatrices, comportamiento, alergias conocidas..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">FOTOGRAFÍA</label>
                                <input type="file" name="fotografia" id="fotografia" class="form-control rounded-3" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer   px-4 py-3">
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-apple-primary px-4 py-2 shadow-sm">GUARDAR EXPEDIENTE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once __DIR__ . '/clientes/clientesModal.php'; ?>

    <script>
    let tabla;

    $(document).ready(function() {
        // Inicializar DataTables
        tabla = $('#tablaMascotas').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            "dom": 'rt<"row mt-3 px-3"<"col-sm-12 col-md-5 small text-muted"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 15,
            "order": [[1, 'asc']],
            "columnDefs": [
                { "targets": [0, 5], "orderable": false }
            ]
        });

        // Evento de búsqueda instantánea por teclado
        $('#busquedaMascota').on('keyup', function() { 
            tabla.search(this.value).draw(); 
        });

        // Cargar combos e inicializar lista de mascotas vía AJAX
        cargarClientes();
        obtenerMascotasAjax();
    });

    /**
     * Obtiene y renderiza el listado de mascotas mediante AJAX
     */
    async function obtenerMascotasAjax() {
        const clienteId = $('#filtroCliente').val() || '';
         const almacenId = $('#almacen_id').val()>0?$('#almacen_id').val():0 || '';

        try {
            const url = `/myvet/app/controllers/pacientesController.php?action=filtrar_mascotas&cliente_id=${clienteId}&consultorio=${almacenId}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            if (resultado.status && Array.isArray(resultado.data)) {
                renderizarTablaMascotas(resultado.data);
            } else {
                tabla.clear().draw();
            }
        } catch (error) {
            console.error('Error al actualizar mascotas por AJAX:', error);
        }
    }

    /**
     * Reconstruye dinámicamente las filas de DataTables preservando el estilo
     */
    function renderizarTablaMascotas(mascotas) {
        tabla.clear();

        mascotas.forEach(m => {
            // Manejo de Foto
            let fotoHtml = `<div class="avatar-placeholder"><i class="bi bi-paw-fill"></i></div>`;
            if (m.fotografia) {
                fotoHtml = `<img src="/myvet/${m.fotografia}" alt="Foto" class="avatar-mascota">`;
            }

            // Sexo Badge
            let sexoClass = ' text-muted';
            if (m.sexo === 'Macho') sexoClass = 'badge-macho';
            if (m.sexo === 'Hembra') sexoClass = 'badge-hembra';

            const filaFoto = `<div class="avatar-mascota-wrapper">${fotoHtml}</div>`;

            const filaGeneral = `
                <div class="fw-bold  fs-6 mb-1">${m.nombre}</div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-sexo ${sexoClass}">${m.sexo || 'N/E'}</span>
                    <span class="text-muted small"><i class="bi bi-speedometer2 me-1"></i>${m.peso || '0'} kg</span>
                </div>
            `;

            const filaEspecie = `
                <span class="badge-especie d-inline-block mb-1">${m.especie}</span>
                ${m.raza ? `<div class="text-secondary small fw-medium">${m.raza}</div>` : ''}
            `;

            const filaPropietario = `
                <div class="fw-semibold  d-flex align-items-center gap-2">
                    <div class="rounded-circle  d-flex align-items-center justify-content-center text-primary" style="width:28px; height:28px; font-size: 0.8rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <span>${m.propietario_nombre || m.nombre_comercial || 'N/A'}</span>
                </div>
            `;

            const isChecked = parseInt(m.activo) === 1 ? 'checked' : '';
            const statusText = parseInt(m.activo) === 1 ? 'Activo' : 'Inactivo';
            const statusClass = parseInt(m.activo) === 1 ? 'text-success' : 'text-muted';

            const filaEstado = `
                <div class="form-check form-switch d-flex align-items-center gap-2 m-0">
                    <input class="form-check-input" type="checkbox" role="switch" ${isChecked} 
                           onchange="cambiarEstado(${m.id}, this.checked ? 1 : 0)" id="switch_${m.id}">
                    <label class="form-check-label small fw-semibold ${statusClass}" for="switch_${m.id}">
                        ${statusText}
                    </label>
                </div>
            `;

            const filaAcciones = `
                <div class="d-inline-flex action-btn-group">
                    <a href="/myvet/app/controllers/consultaController.php?id=${m.id}" 
                       class="btn btn-sm text-primary hover-bg" title="Nueva Consulta">
                       <i class="bi bi-stethoscope me-1"></i>Consulta
                    </a>
                    <a href="/myvet/app/controllers/historialExpedienteController.php?id=${m.id}" 
                       class="btn btn-sm text-info hover-bg" title="Historial">
                       <i class="bi bi-journal-medical me-1"></i>Expediente
                    </a>
                    <button class="btn btn-sm text-secondary hover-bg" onclick="editarMascota(${m.id})" title="Editar Datos">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                </div>
            `;

            // Insertar fila en DataTables
            tabla.row.add([
                filaFoto,
                filaGeneral,
                filaEspecie,
                filaPropietario,
                filaEstado,
                filaAcciones
            ]);
        });

        tabla.draw(false);
    }

    function limpiarFiltros() {
        $('#busquedaMascota').val('');
        $('#filtroCliente').val('');
        tabla.search('');
        obtenerMascotasAjax();
    }

    function nuevaMascota() {
        $('#formMascota')[0].reset();
        $('#mascota_id').val('0');
        $('#modalTitulo').text('Nuevo Registro de Mascota');
        $('#modalMascota').modal('show');
    }

    async function cargarClientes() {
        const almacenId = $('#almacen_id').val();
        const selectModal = document.getElementById('cliente_id');
        const selectFiltro = document.getElementById('filtroCliente');

        try {
            const url = '/myvet/app/controllers/accesoController.php?action=obtenerClientes';
            const respuesta = await fetch(url);
            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

            const resultado = await respuesta.json();

            if (resultado.success && Array.isArray(resultado.data)) {
                const clientesFiltrados = resultado.data.filter(cliente => {
                    const nombreNorm = (cliente.nombre_comercial || '').toLowerCase().trim();
                    const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');
                    if (esPublicoGeneral) return cliente.almacen_id == almacenId;
                    return true;
                });

                // Llenar select del modal
                if (selectModal) {
                    selectModal.innerHTML = '<option value="">-- Seleccione un cliente --</option>';
                    clientesFiltrados.forEach(c => {
                        selectModal.innerHTML += `<option value="${c.id}">${c.nombre_comercial}</option>`;
                    });
                }

                // Llenar select del filtro superior
                if (selectFiltro) {
                    selectFiltro.innerHTML = '<option value="">-- Todos los Propietarios --</option>';
                    clientesFiltrados.forEach(c => {
                        selectFiltro.innerHTML += `<option value="${c.id}">${c.nombre_comercial}</option>`;
                    });
                }
            }
        } catch (error) {
            console.error('Error al cargar clientes:', error);
        }
    }

    async function editarMascota(id) {
        try {
            const resp = await fetch(`/myvet/app/controllers/pacientesController.php?action=obtenerPorId&id=${id}`);
            const res = await resp.json();
            if (res.success) {
                const m = res.data;
                $('#modalTitulo').text('Actualizar Mascota');
                $('#mascota_id').val(m.id);
                $('#cliente_id').val(m.cliente_id);
                $('#nombre').val(m.nombre);
                $('#especie').val(m.especie);
                $('#raza').val(m.raza);
                $('#fecha_nacimiento').val(m.fecha_nacimiento);
                $('#sexo').val(m.sexo);
                $('#peso').val(m.peso);
                $('#color').val(m.color);
                $('#senas_particulares').val(m.senas_particulares);
                $('#fotografia').val(''); 

                $('#modalMascota').modal('show');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { console.error(e); }
    }

    // Guardado de mascota con actualización en tiempo real por AJAX
    $('#formMascota').on('submit', async function(e) {
        e.preventDefault();
        try {
            const resp = await fetch('/myvet/app/controllers/pacientesController.php?action=guardar', {
                method: 'POST',
                body: new FormData(this)
            });
            const res = await resp.json();
            
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, timer: 1500, showConfirmButton: false });
                $('#modalMascota').modal('hide');
                obtenerMascotasAjax(); // Actualización en tiempo real sin recargar página
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { console.error(e); }
    });

    async function cambiarEstado(id, estado) {
        const fd = new FormData();
        fd.append('id', id); 
        fd.append('estado', estado);
        
        try {
            await fetch('/myvet/app/controllers/pacientesController.php?action=cambiarEstado', { method: 'POST', body: fd });
            // Actualizar etiqueta sin refrescar página
            const label = $(`#switch_${id}`).next('label');
            if (estado === 1) {
                label.removeClass('text-muted').addClass('text-success').text('Activo');
            } else {
                label.removeClass('text-success').addClass('text-muted').text('Inactivo');
            }
        } catch(e) {
            console.error(e);
        }
    }
 
async function verExpediente(id) {
    console.log(id);
    const w = window.open('', '_blank');
    try {

      

        const res = await fetch(
            `/myvet/app/controllers/pacientesController.php?action=obtenerExpediente&id=${id}`
        );

        const data = await res.json();
        console.log(data);

        if (data.data==null) {
            return Swal.fire(
                'Error',
                'No se pudo cargar el estado de cuenta',
                'error'
            );
        }

        
        // SOLO FILAS
      const filas = data.data.map(p => {

    const documentos = p.documento_url
        ? p.documento_url.split(';;;')
        : [];

    const htmlDocumentos = documentos.map(doc => {

        const partes = doc.split('|||');

        const nombre = partes[0] || '';
        const direccion = partes[1] || '';

        if (!direccion) return '';

        return `
            <div style="margin-bottom:4px;">
                <a href="../../${direccion}" target="_blank">
                    ${nombre}
                </a>
            </div>
        `;
    }).join('');

    return `
        <tr>

            <td>${p.id}</td>

            <td>
                ${p.fecha_consulta}
            </td>

            <td>
                ${p.motivo_consulta}
            </td>
             <td>
                ${p.diagnostico ?? ''}
            </td>

            <td>
                ${htmlDocumentos || 'Sin documentos'}
            </td>
            <td><a href="/myvet/app/controllers/historialExpedienteController.php?id=${p.id}" 
                    class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                    <i class="bi bi-graph-up-arrow me-1"></i> Analizar
                 </a></td>

           

        </tr>
    `;

}).join('');
        const doc = `
        <html>
        <head>
            <title>Estado de Cuenta</title>
            <style>
                body{
                    font-family:Arial,sans-serif;
                    font-size:10px;
                    padding:20px;
                }

                table{
                    border-collapse:collapse;
                    width:100%;
                }

                th,td{
                    border:1px solid #ccc;
                    padding:8px;
                    font-size:8px;
                }

                th{
                    background:#f3f4f6;
                }
            </style>
        </head>

        <body>
          <img
    src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
    style="
        position: fixed;
        top: 19.5%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 180px;
        opacity: 0.08;
        z-index: -1;
    "
>
      
            <div style="
                border-bottom:2px solid #007aff;
                padding-bottom:12px;
                margin-bottom:20px;
            ">
                <h2 style="margin:0;color:#1f2937;">
                  
                </h2>

                <p style="margin:4px 0;color:#6b7280;">
                    RFC:
                    <b></b>
                </p>

                <p style="margin:4px 0;color:#6b7280;">
                    Dirección:
                    <b></b>
                </p>
            </div>

            <div style="
                display:flex;
                gap:10px;
                margin-bottom:20px;
            ">

                <div style="
                    flex:1;
                    background:#f3f4f6;
                    border-left:4px solid #3b82f6;
                    padding:10px;
                    border-radius:8px;
                ">
                    <div style="font-size:10px;color:#6b7280;">
                        TOTAL COMPRADO
                    </div>

                    <div style="font-size:16px;font-weight:bold;">
                       
                    </div>
                </div>

                <div style="
                    flex:1;
                    background:#f3f4f6;
                    border-left:4px solid #10b981;
                    padding:10px;
                    border-radius:8px;
                ">
                    <div style="font-size:10px;color:#6b7280;">
                        TOTAL PAGADO
                    </div>

                    <div style="font-size:16px;font-weight:bold;">
                       
                    </div>
                </div>

                <div style="
                    flex:1;
                    background:#f3f4f6;
                    border-left:4px solid #ef4444;
                    padding:10px;
                    border-radius:8px;
                ">
                    <div style="font-size:10px;color:#6b7280;">
                        SALDO
                    </div>

                    <div style="font-size:16px;font-weight:bold;">
                       
                    </div>
                </div>

            </div>

            <div style="
            
                border:1px solid #e5e7eb;
                border-radius:10px;
                overflow:hidden;
                background:#fff;
            ">

                <table cellpadding="6" cellspacing="0" style="width:60%; margin:0 auto;">
                    <thead>
                        <tr>
                       
                            <th style="text-align:left;">id</th>
                            <th style="text-align:right;">Fecha</th>
                            <th style="text-align:right;">Razon Consulta</th>
                              <th style="text-align:right;">Diagnostico</th>
                            
                            <th style="text-align:right;">Documentos</th>
                            <th style="text-align:right;">Acciones</th>
                           
                           
                           
                        </tr>
                    </thead>

                    <tbody>
                        ${filas}
                    </tbody>
                </table>

            </div>

        </body>
        </html>
        `;

        w.document.write(doc);
        w.document.close();

        w.onload = () => {
            w.print();
        };

    } catch (error) {
        console.error(error);

        Swal.fire(
            'Error',
            'Ocurrió un error al generar el estado de cuenta',
            'error'
        );
    }
}

    </script>
</body>
</html>