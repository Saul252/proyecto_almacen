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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
  
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
           
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
        }

        body { 
            background-color: var(--apple-bg); 
            font-family: 'SF Pro Display', -apple-system, sans-serif;
            color: #1d1d1f;
        }

        .main-content { 
           
            padding: 40px; 
           
        }

        .card-premium { 
             
            border-radius: 20px; 
            box-shadow: 0 8px 30px rgba(0,0,0,0.04); 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .badge-ubicacion { 
            background-color: #f2f2f7; 
            color: #1d1d1f; 
            border: 1px solid #d1d1d6; 
            padding: 0.4rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* DataTables Custom */
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            border-radius: 8px;
        }

        .table thead th {
            background: #fbfbfd;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #86868b;
            border-bottom: 1px solid #d1d1d6;
        }

        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } 
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
    <div class="container py-4" style="max-width: 900px;">
    <!-- Encabezado de la Consulta -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.75rem;">Módulo Clínico</span>
            <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Nueva Consulta Médica</h3>
        </div>
        <button type="button" class="btn btn-light rounded-pill  px-3 py-2 btn-sm text-secondary" onclick="window.history.back();">
            <i class="bi bi-x-lg me-1"></i> Cancelar
        </button>
    </div>

    <form action="guardar_consulta.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        
        <!-- BLOQUE 1: DATOS DEL PACIENTE Y PROPIETARIO -->
        <div class="card  shadow-sm rounded-4 mb-4" style="background-color: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-heart-pulse-fill fs-6"></i>
                    </div>
                    <h6 class="m-0 fw-bold text-dark">Información del Paciente y Dueño</h6>
                </div>
                
                <div class="row g-3">
                    <!-- Dueño -->
                    <div class="col-md-6">
                        <label for="dueno" class="form-label small fw-medium text-secondary">Dueño / Propietario</label>
                        <input type="text" class="form-control ios-input rounded-3 shadow-none bg-light " id="dueno" name="dueno" placeholder="Nombre completo del propietario" required>
                    </div>

                    <!-- Nombre Paciente -->
                    <div class="col-md-6">
                        <label for="nombre_paciente" class="form-label small fw-medium text-secondary">Nombre del Paciente</label>
                        <input type="text" class="form-control ios-input rounded-3 shadow-none bg-light " id="nombre_paciente" name="nombre" placeholder="Ej. Max, Luna..." required>
                    </div>

                    <!-- Especie -->
                    <div class="col-md-3 col-6">
                        <label for="especie" class="form-label small fw-medium text-secondary">Especie</label>
                        <select class="form-select ios-input rounded-3 shadow-none bg-light " id="especie" name="especie" required>
                            <option value="" selected disabled>Seleccionar...</option>
                            <option value="Canino">Canino</option>
                            <option value="Felino">Felino</option>
                            <option value="Ave">Ave</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <!-- Raza -->
                    <div class="col-md-3 col-6">
                        <label for="raza" class="form-label small fw-medium text-secondary">Raza</label>
                        <input type="text" class="form-control ios-input rounded-3 shadow-none bg-light " id="raza" name="raza" placeholder="Ej. Golden, Siamés" required>
                    </div>

                    <!-- Tamaño -->
                    <div class="col-md-3 col-6">
                        <label for="tamano" class="form-label small fw-medium text-secondary">Tamaño / Peso</label>
                        <input type="text" class="form-control ios-input rounded-3 shadow-none bg-light " id="tamano" name="tamano" placeholder="Ej. 12 kg, Grande" required>
                    </div>

                    <!-- Edad -->
                    <div class="col-md-3 col-6">
                        <label for="edad" class="form-label small fw-medium text-secondary">Edad</label>
                        <input type="text" class="form-control ios-input rounded-3 shadow-none bg-light " id="edad" name="edad" placeholder="Ej. 3 años, 5 meses" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOQUE 2: DETALLES DE LA CONSULTA -->
        <div class="card  shadow-sm rounded-4 mb-4" style="background-color: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-file-earmark-medical-fill fs-6"></i>
                    </div>
                    <h6 class="m-0 fw-bold text-dark">Anamnesis y Diagnóstico</h6>
                </div>

                <div class="row g-3">
                    <!-- Síntomas -->
                    <div class="col-12">
                        <label for="sintomas" class="form-label small fw-medium text-secondary">Síntomas Reportados</label>
                        <textarea class="form-control ios-input rounded-3 shadow-none bg-light " id="sintomas" name="sintomas" rows="2" placeholder="Describa los signos clínicos que presenta el paciente..." required></textarea>
                    </div>

                    <!-- Explicación Médica -->
                    <div class="col-12">
                        <label for="explicacion" class="form-label small fw-medium text-secondary">Explicación / Diagnóstico Presuntivo</label>
                        <textarea class="form-control ios-input rounded-3 shadow-none bg-light " id="explicacion" name="explicacion" rows="3" placeholder="Evaluación médica, observaciones del examen físico o notas diagnósticas..." required></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOQUE 3: EVIDENCIAS Y TRATAMIENTO -->
        <div class="card  shadow-sm rounded-4 mb-4" style="background-color: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-capsule fs-6"></i>
                    </div>
                    <h6 class="m-0 fw-bold text-dark">Evidencias y Plan de Tratamiento</h6>
                </div>

                <div class="row g-3">
                    <!-- Tratamiento -->
                    <div class="col-12">
                        <label for="tratamiento" class="form-label small fw-medium text-secondary">Tratamiento / Receta</label>
                        <textarea class="form-control ios-input rounded-3 shadow-none bg-light " id="tratamiento" name="tratamiento" rows="3" placeholder="Medicamentos, dosis, frecuencia y duración del tratamiento..." required></textarea>
                    </div>

                    <!-- Evidencias (Archivos / Fotos) -->
                    <div class="col-12">
                        <label for="evidencias" class="form-label small fw-medium text-secondary">Evidencias (Fotos, Estudios, Rayos X)</label>
                        <div class="border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative" style="border-color: rgba(0,0,0,0.08) !important;">
                            <input type="file" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" id="evidencias" name="evidencias[]" multiple style="cursor: pointer;">
                            <i class="bi bi-cloud-upload-fill text-secondary fs-3 mb-1 d-block"></i>
                            <span class="small text-secondary d-block fw-medium">Arrastra tus archivos aquí o haz clic para buscar</span>
                            <span class="text-muted" style="font-size: 0.7rem;">Formatos permitidos: JPG, PNG, PDF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACCIONES DEL FORMULARIO -->
        <div class="d-flex align-items-center justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-medium shadow-none" style="font-size: 0.95rem;">
                <i class="bi bi-check-lg me-1"></i> Guardar Registro
            </button>
        </div>
    </form>
</div>
    </main>

    <div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <form id="formCliente">
                    <div class="modal-header bg-dark text-white  py-3">
                        <h5 class="modal-title fw-bold px-2" id="modalTitulo">Nuevo Cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="cliente_id" id="cliente_id" value="0">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">NOMBRE COMERCIAL *</label>
                                <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">RAZÓN SOCIAL</label>
                                <input type="text" name="razon_social" id="razon_social" class="form-control rounded-3">
                            </div>

 <div class="col-md-12">
                                <label class="form-label fw-bold">Contacto *</label>
                                <input type="text" name="contacto" id="contacto"class="form-control"
                                    placeholder="Contacto" >
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">TELEFONO</label>
                                <input type="text" name="telefono" id="telefono" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">RFC *</label>
                                <input type="text" name="rfc" id="rfc" class="form-control text-uppercase rounded-3" maxlength="13" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Calle</label>
                                <textarea name="calle" id="calle"class="form-control" rows="2"
                                    placeholder="Calle, número, colonia..."></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Colonia</label>
                                <textarea name="colonia" id="colonia" class="form-control" rows="2"
                                    placeholder="Colonia"></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Pueblo</label>
                                <textarea name="pueblo" id="pueblo" class="form-control" rows="2"
                                    placeholder="Pueblo"></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Ciudad</label>
                                <textarea name="ciudad" id="ciudad" class="form-control" rows="2"
                                    placeholder="Calle, número, colonia..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">CÓDIGO POSTAL</label>
                                <input type="text" name="codigo_postal" id="codigo_postal" class="form-control rounded-3" maxlength="5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">USO CFDI</label>
                                <select name="uso_cfdi" id="uso_cfdi" class="form-select rounded-3">
                                    <?php foreach($usosCFDI as $key => $val): ?>
                                        <option value="<?= $key ?>"><?= $key ?> - <?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">CORREO</label>
                                <input type="email" name="correo" id="correo" class="form-control rounded-3">
                            </div>

                            <?php if ($almacen_usuario == 0): ?>
                            <div class="col-md-12">
                                <div class="bg-light p-3 rounded-4 mt-2 border border-dashed">
                                    <label class="form-label small fw-bold text-primary">ASIGNAR A SUCURSAL *</label>
                                    <select name="almacen_id" id="almacen_id_modal" class="form-select border-primary shadow-none" required>
                                        <option value="">-- Seleccionar Almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php else: ?>
                                <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer bg-light  px-4 pb-4">
                        <button type="button" class="btn btn-link text-secondary fw-bold text-decoration-none" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow">GUARDAR DATOS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    let tabla;

    $(document).ready(function() {
        tabla = $('#tablaClientes').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            "dom": 'rt<"row mt-3 px-3"<"col-sm-12 col-md-5 small text-muted"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 15,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "targets": [4], "orderable": false },
                { "targets": [2], "visible": <?= ($almacen_usuario == 0) ? 'true' : 'false' ?> }
            ]
        });

        $('#busquedaCliente').on('keyup', function() { tabla.search(this.value).draw(); });

        $('#filtroAlmacenVista').on('change', function() {
            const val = $(this).val();
            $.fn.dataTable.ext.search.pop();
            if (val !== "") {
                $.fn.dataTable.ext.search.push(function(s, d, i) {
                    return $(tabla.row(i).node()).attr('data-almacen-id') == val;
                });
            }
            tabla.draw();
        });
    });

    function limpiarFiltros() {
        $('#busquedaCliente').val('');
        $('#filtroAlmacenVista').val('');
        $.fn.dataTable.ext.search.pop();
        tabla.search('').draw();
    }

    function nuevoCliente() {
        $('#formCliente')[0].reset();
        $('#cliente_id').val('0');
        $('#modalTitulo').text('Nuevo Registro de Cliente');
        
        // Auto-seleccionar almacén si hay filtro activo
        const filtro = $('#filtroAlmacenVista').val();
        if(filtro) $('#almacen_id_modal').val(filtro);
        
        $('#modalCliente').modal('show');
    }

    async function editarCliente(id) {
        try {
            const resp = await fetch(`clientesController.php?action=obtenerPorId&id=${id}`);
            const res = await resp.json();
            if (res.success) {
                const c = res.data;
                $('#modalTitulo').text('Actualizar Cliente');
                $('#cliente_id').val(c.id);
                 $('#contacto').val(c.contacto);
                $('#nombre_comercial').val(c.nombre_comercial);
                $('#razon_social').val(c.razon_social);
                $('#rfc').val(c.rfc);
                $('#telefono').val(c.telefono);
                 const direccion = c.direccion || '';

const calle = (direccion.match(/calle\s(.*?)(?=,\scol|$)/i) || [,''])[1];
const colonia = (direccion.match(/col\s(.*?)(?=,\spueblo|$)/i) || [,''])[1];
const pueblo = (direccion.match(/pueblo\s(.*?)(?=,\sciudad|$)/i) || [,''])[1];
const ciudad = (direccion.match(/ciudad\s(.*)$/i) || [,''])[1];

$('#calle').val(calle.trim());
$('#colonia').val(colonia.trim());
$('#pueblo').val(pueblo.trim());
$('#ciudad').val(ciudad.trim());
                $('#correo').val(c.correo);
                $('#codigo_postal').val(c.codigo_postal);
                $('#almacen_id_modal').val(c.almacen_id);
                $('#modalCliente').modal('show');
            }
        } catch (e) { console.error(e); }
    }

    $('#formCliente').on('submit', async function(e) {
        e.preventDefault();
        try {
            const resp = await fetch('clientesController.php?action=guardar', {
                method: 'POST',
                body: new FormData(this)
            });
            const res = await resp.json();
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { console.error(e); }
    });

    async function cambiarEstado(id, estado) {
        const fd = new FormData();
        fd.append('id', id); fd.append('estado', estado);
        fetch('clientesController.php?action=cambiarEstado', { method: 'POST', body: fd });
    }

    function verDetalles(id) {
        window.location.href = `clientesController.php?action=detalles&id=${id}`;
    }
    </script>
</body>
</html>