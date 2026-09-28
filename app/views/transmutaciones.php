<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transmutaciones | JSEA Sistem</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
    <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">

    <!-- Frameworks & Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>   
    <link href="/myvet/css/transmutaciones.css" rel="stylesheet">

    <style>
        /* Estilos de optimización Select2 para Bootstrap 5 y Modo Oscuro */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 5px 8px;
            background-color: var(--bs-body-bg, #ffffff) !important;
            border: 1px solid var(--bs-border-color, #dee2e6) !important;
            border-radius: var(--bs-border-radius, 0.375rem) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--bs-body-color, #212529) !important;
            line-height: 26px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            background-color: var(--bs-body-bg, #ffffff) !important;
            border-color: var(--bs-border-color, #dee2e6) !important;
            color: var(--bs-body-color, #212529) !important;
            z-index: 1060 !important; /* Compatibilidad dentro de Modales */
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: var(--bs-tertiary-bg, #f8f9fa) !important;
            color: var(--bs-body-color, #212529) !important;
            border: 1px solid var(--bs-border-color, #dee2e6) !important;
            border-radius: 6px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--bs-primary, #0d6efd) !important;
            color: #ffffff !important;
        }
        .select2-container--default.select2-container--disabled .select2-selection--single {
            background-color: var(--bs-secondary-bg, #e9ecef) !important;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) renderizarLayout('Mermas'); ?>
<div class="main-content">
    <div class="container-fluid">
        
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fas fa-random text-primary me-2"></i>Transmutación de Productos</h1>
                <small class="text-body-secondary">Procesa la transformación de materiales e insumos</small>
            </div>
        </div>

        <div class="row align-items-stretch">
            <div class="col-lg-4">
                <div class="card card-custom mb-4 h-100">
                    <div class="card-header-custom">
                        <h6 class="m-0 font-weight-bold text-dark">
                            <i class="fas fa-book me-2"></i>Guía de Equivalencias Activas
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEquivalencia">
                                <i class="fas fa-cog me-1"></i> Nueva Regla
                            </button>
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <?php require_once __DIR__ . '/transmutaciones/reglasConversion.php' ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom mb-4 h-100">
                    <div class="card-header-custom">
                        <h6 class="m-0 font-weight-bold" style="color: var(--primary-color);">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Operación de Transformación
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form id="formTransmutacion">
                            <div class="row align-items-stretch">
                                <div class="col-xl-5">
                                    <div class="section-box box-origen">
                                        <div class="section-title text-danger">
                                            <i class="fas fa-minus-circle me-2"></i>Producto Origen (Salida)
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs">Almacén de Trabajo</label>
                                            <select name="almacen_id" id="trans_almacen" class="form-select select2-general shadow-sm" required style="width: 100%;">
                                                <option value="">Seleccione Almacén...</option>
                                                <?php foreach ($almacenes as $a): ?>
                                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs">Producto a Transformar</label>
                                            <select name="producto_origen_id" id="trans_producto_origen" class="form-select select2-general" disabled required style="width: 100%;">
                                                <option value="">Seleccione almacén primero</option>
                                            </select>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-7">
                                                <label class="form-label text-xs">Lote Origen</label>
                                                <select name="lote_origen_id" id="trans_lote_origen" class="form-select select2-general" disabled required style="width: 100%;"></select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label text-xs">Cant. Salida</label>
                                                <input type="number" step="0.01" name="cantidad_origen" id="trans_cant_origen" class="form-control" required>
                                                <div class="small mt-1 text-body-secondary">Stock: <span id="trans_stock_disp" class="fw-bold text-danger">0</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-2 conversion-arrow d-flex align-items-center justify-content-center">
                                    <i class="fas fa-arrow-right fa-2x d-none d-xl-block"></i>
                                    <i class="fas fa-arrow-down fa-2x d-xl-none my-3"></i>
                                </div>

                                <div class="col-xl-5">
                                    <div class="section-box box-destino">
                                        <div class="section-title text-success">
                                            <i class="fas fa-plus-circle me-2"></i>Producto Destino (Entrada)
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs">Convertir a:</label>
                                            <select name="producto_destino_id" id="trans_producto_destino" class="form-select select2-general" disabled required style="width: 100%;">
                                                <option value="">Seleccione origen primero</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs">Lote Destino</label>
                                            <select name="lote_destino_id" id="trans_lote_destino" class="form-select select2-general" disabled style="width: 100%;">
                                                <option value="0">-- Crear Lote Nuevo --</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs">Cant. Obtenida (Real)</label>
                                            <input type="number" step="0.01" name="cantidad_destino" id="trans_cant_destino" class="form-control" style="border-color: #68d391;" required>
                                            <div id="info_conversion" class="small mt-1 fw-bold text-primary"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <textarea name="observaciones" class="form-control text-uppercase form-control-sm" rows="2" placeholder="Notas del proceso..."></textarea>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-light btn-sm me-2">Limpiar</button>
                                <button type="submit" class="btn btn-primary btn-sm px-4 shadow">
                                    <i class="fas fa-check-circle me-1"></i> Procesar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       <div class="card card-custom">
    <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h6 class="m-0 font-weight-bold text-dark me-auto">
            <i class="fas fa-history me-2"></i>Historial de Movimientos
        </h6>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLimpiarFiltros">
            <i class="fas fa-sync-alt me-1"></i> Limpiar Filtros
        </button>
    </div>
    <div class="card-body">
        <!-- BARRA DE FILTROS -->
        <div class="row g-2 mb-3 align-items-end p-3 bg-light rounded border">
            <div class="col-md-3">
                <label class="form-label text-xs fw-bold text-secondary mb-1">Buscar</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="f_search" class="form-control form-control-sm" placeholder="ID, producto, nota...">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label text-xs fw-bold text-secondary mb-1">Almacén</label>
                <select id="f_almacen" class="form-select form-select-sm">
                    <option value="0">Todos los almacenes</option>
                    <?php foreach ($almacenes as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label text-xs fw-bold text-secondary mb-1">Rango de Fecha</label>
                <select id="f_rango" class="form-select form-select-sm">
                    <option value="todos">Todas las fechas</option>
                    <option value="hoy">Hoy</option>
                    <option value="ayer">Ayer</option>
                    <option value="semana">Esta Semana</option>
                    <option value="mes">Este Mes</option>
                    <option value="personalizado">Personalizado</option>
                </select>
            </div>

            <div class="col-md-3 d-none" id="box_fechas_custom">
                <div class="row g-1">
                    <div class="col-6">
                        <label class="form-label text-xs mb-1">Desde</label>
                        <input type="date" id="f_inicio" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xs mb-1">Hasta</label>
                        <input type="date" id="f_fin" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA HISTORIAL -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaHistorial" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th width="60px">ID</th>
                        <th>Fecha</th>
                        <th>Almacén</th>
                        <th>Origen (Sale)</th>
                        <th>Cant.</th>
                        <th>Destino (Entra)</th>
                        <th>Cant.</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llena dinámicamente vía DataTables / Ajax -->
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
</div>

    <!-- MODAL EQUIVALENCIAS -->
    <div class="modal fade" id="modalEquivalencia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-cog me-2 text-primary"></i>Configurar Equivalencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevaEquivalencia">
                    <div class="modal-body p-4">
                        <div class="alert alert-primary border-0 shadow-sm small d-flex align-items-center" style="border-radius: 12px;">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>Define cuántas unidades del producto destino se obtienen por cada unidad del producto origen.</div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Almacén de Aplicación</label>
                                <?php 
                                $idSesion = (int)($_SESSION['almacen_id'] ?? 0);
                                $esAdmin = ($idSesion === 0);
                                ?>

                                <?php if ($esAdmin): ?>
                                    <select name="almacen_id" class="form-select select2-modal shadow-sm border-primary" required style="width: 100%;">
                                        <option value="">-- Seleccione Almacén --</option>
                                        <?php foreach ($almacenes as $a): ?>
                                            <?php if($a['id'] > 0): ?>
                                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <?php 
                                    $nombreAlmacen = $_SESSION['almacen_nombre'] ?? 'Almacén Asignado';
                                    foreach ($almacenes as $a) {
                                        if ((int)$a['id'] === $idSesion) {
                                            $nombreAlmacen = $a['nombre'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-body-secondary"></i></span>
                                        <input type="text" class="form-control bg-light border-0 fw-bold" value="<?= htmlspecialchars($nombreAlmacen) ?>" readonly>
                                    </div>
                                    <input type="hidden" name="almacen_id" value="<?= $idSesion ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-danger">Producto Origen (Sale)</label>
                                <select name="p_origen" class="form-select border-danger-subtle select2-modal" required style="width: 100%;">
                                    <option value="">Buscar producto...</option>
                                    <?php foreach($todosLosProductos as $p): ?>
                                        <option value="<?= $p['id'] ?>"
                                                data-unidad="<?= htmlspecialchars($p['unidad_medida']) ?>"
                                                data-sku="<?= htmlspecialchars($p['sku']) ?>">
                                            <?= htmlspecialchars($p['sku'] . " - " . $p['nombre']) ?> (<?= htmlspecialchars($p['unidad_medida']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-success">Producto Destino (Entra)</label>
                                <select name="p_destino" class="form-select border-success-subtle select2-modal" required style="width: 100%;">
                                    <option value="">Buscar producto...</option>
                                    <?php foreach($todosLosProductos as $p): ?>
                                        <option value="<?= $p['id'] ?>"
                                                data-unidad="<?= htmlspecialchars($p['unidad_medida']) ?>"
                                                data-sku="<?= htmlspecialchars($p['sku']) ?>">
                                            <?= htmlspecialchars($p['sku'] . " - " . $p['nombre']) ?> (<?= htmlspecialchars($p['unidad_medida']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label d-block text-center mb-3">Factor de Rendimiento</label>
                                    <div class="input-group input-group-lg">
                                        <div class="badge bg-danger-subtle text-danger">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>
                                            <span>Origen</span>
                                            <input type="text" id="unidadOrigen" readonly class="bg-transparent border-0 text-danger fw-bold text-center" style="width: 70px;">
                                        </div>
                                        <input type="number" step="0.0001" name="factor" class="form-control text-center fw-bold text-primary" placeholder="0.00" required>
                                        <div class="badge bg-success-subtle text-success">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>
                                            <span>Destino</span>
                                            <input type="text" id="unidadDestino" readonly class="bg-transparent border-0 text-success fw-bold text-center" style="width: 70px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts de Librerías Corregidos -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const baseUrl = '/myvet/app/controllers/transmutacionesController.php';
let tablaHistorial;

$(document).ready(function() {
    // Inicializar DataTable con AJAX dinámico y Filtros
    tablaHistorial = $('#tablaHistorial').DataTable({
    "destroy": true, 
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "ajax": {
            "url": baseUrl + "?action=listar",
            "type": "GET",
            "dataSrc": "",
            "data": function(d) {
                d.f_search  = $('#f_search').val();
                d.f_almacen = $('#f_almacen').val();
                d.f_rango   = $('#f_rango').val();
                d.f_inicio  = $('#f_inicio').val();
                d.f_fin     = $('#f_fin').val();
            }
        },
        "columns": [
            { 
                "data": "id",
                "render": function(data) {
                    return `<span class="badge bg-light text-dark border">#${data}</span>`;
                }
            },
            { 
                "data": "fecha_registro",
                "render": function(data) {
                    if (!data) return 'N/A';
                    const d = new Date(data);
                    if (isNaN(d.getTime())) return data;
                    return d.toLocaleDateString('es-ES', {day:'2-digit', month:'2-digit', year:'numeric'}) + ' ' +
                           d.toLocaleTimeString('es-ES', {hour:'2-digit', minute:'2-digit'});
                }
            },
            { 
                "data": "almacen",
                "render": function(data) {
                    return `<i class="fas fa-warehouse me-1 text-secondary"></i>${data || 'N/A'}`;
                }
            },
            { 
                "data": "producto_origen",
                "render": function(data) {
                    return `<i class="fas fa-minus-circle text-danger me-1"></i>${data || 'N/A'}`;
                }
            },
            { 
                "data": null,
                "render": function(data, type, row) {
                    const cant = parseFloat(row.cant_origen || 0).toFixed(2);
                    const uni = row.unidad_origen || '';
                    return `<span class="fw-bold text-danger">-${cant} ${uni}</span>`;
                }
            },
            { 
                "data": "producto_destino",
                "render": function(data) {
                    return `<i class="fas fa-plus-circle text-success me-1"></i>${data || 'N/A'}`;
                }
            },
            { 
                "data": null,
                "render": function(data, type, row) {
                    const cant = parseFloat(row.cant_destino || 0).toFixed(2);
                    const uni = row.unidad_destino || '';
                    return `<span class="fw-bold text-success">+${cant} ${uni}</span>`;
                }
            },
            { 
                "data": "usuario_nombre",
                "render": function(data) {
                    return `<i class="fas fa-user-circle me-1 text-body-secondary"></i><small>${data || 'Sistema'}</small>`;
                }
            }
        ],
        "order": [[ 0, "desc" ]],
        "pageLength": 10,
        "responsive": true,
        "dom": '<"d-flex justify-content-between"f>rt<"d-flex justify-content-between"ip>',
        "drawCallback": function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-sm');
        }
    });

    // Eventos para recargar la tabla dinámicamente al cambiar Selects o Fechas
    $('#f_almacen, #f_rango, #f_inicio, #f_fin').on('change', function() {
        tablaHistorial.ajax.reload();
    });

    // Evento con debounce para el campo de Búsqueda por texto
    let searchTimeout;
    $('#f_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            tablaHistorial.ajax.reload();
        }, 300);
    });

    // Mostrar / Ocultar rango de fechas personalizado
    $('#f_rango').on('change', function() {
        if ($(this).val() === 'personalizado') {
            $('#box_fechas_custom').removeClass('d-none');
        } else {
            $('#box_fechas_custom').addClass('d-none');
            $('#f_inicio, #f_fin').val('');
        }
    });

    // Botón Limpiar Filtros
    $('#btnLimpiarFiltros').on('click', function() {
        $('#f_search').val('');
        $('#f_almacen').val('0');
        $('#f_rango').val('todos').trigger('change');
        $('#f_inicio, #f_fin').val('');
        tablaHistorial.ajax.reload();
    });
});
    $(document).ready(function() {
        // Inicialización general de Select2
        $('.select2-general').select2({
            width: '100%',
            placeholder: "Seleccione una opción..."
        });

        // Inicialización de Select2 dentro de Modal
        $('.select2-modal').select2({
            dropdownParent: $('#modalEquivalencia'),
            width: '100%',
            placeholder: "Seleccione una opción..."
        });

        // Inicializar DataTable
        $('#tablaHistorial').DataTable({
        "destroy": true, 
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[ 0, "desc" ]],
            "pageLength": 10,
            "responsive": true,
            "dom": '<"d-flex justify-content-between"f>rt<"d-flex justify-content-between"ip>',
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            }
        });
    });

    // Eventos jQuery para actualizar unidades al seleccionar en Modal
    $(document).on('change', 'select[name="p_origen"]', function() {
        const option = $(this).find(':selected');
        const org = option.data('unidad') || '';
        $('#unidadOrigen').val(org);
    });

    $(document).on('change', 'select[name="p_destino"]', function() {
        const option = $(this).find(':selected');
        const dest = option.data('unidad') || '';
        $('#unidadDestino').val(dest);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '/myvet/app/controllers/transmutacionesController.php';
        
        const transAlmacen = document.getElementById('trans_almacen');
        const transProdOrigen = document.getElementById('trans_producto_origen');
        const transLoteOrigen = document.getElementById('trans_lote_origen');
        const transProdDestino = document.getElementById('trans_producto_destino');
        const transLoteDestino = document.getElementById('trans_lote_destino');
        const transCantOrigen = document.getElementById('trans_cant_origen');
        const transCantDestino = document.getElementById('trans_cant_destino');
        const infoConversion = document.getElementById('info_conversion');
        const stockSpan = document.getElementById('trans_stock_disp');

        // 1. Al cambiar Almacén -> Cargar Productos Origen
        $(transAlmacen).on('change', async function() {
            const id = this.value;
            if(!id) return;
            
            try {
                const response = await fetch(`${baseUrl.replace('transmutaciones','mermas')}?action=obtenerProductosAlmacen&almacen_id=${id}`);
                const productos = await response.json();

                transProdOrigen.innerHTML = '<option value="">Seleccione Origen...</option>';

                productos.forEach(p => {
                    const option = new Option(
                        `${p.sku} - ${p.nombre} (${p.unidad_medida})`,
                        p.id
                    );
                    option.dataset.unidad = p.unidad_medida;
                    transProdOrigen.add(option);
                });

                $(transProdOrigen).prop('disabled', false).trigger('change');
            } catch (e) { console.error("Error cargando productos", e); }
        });

        // 2. Al cambiar Producto Origen -> Lotes y Destinos
        $(transProdOrigen).on('change', async function() {
            const pId = this.value;
            const aId = transAlmacen.value;
            if(!pId) return;

            // Cargar Lotes
            const resLotes = await fetch(`${baseUrl}?action=obtenerLotes&producto_id=${pId}&almacen_id=${aId}`);
            const lotes = await resLotes.json();
            transLoteOrigen.innerHTML = '<option value="">Seleccione Lote...</option>';
            lotes.forEach(l => {
                const opt = new Option(`${l.codigo_lote} (Disp: ${l.cantidad_actual})`, l.id);
                opt.dataset.stock = l.cantidad_actual;
                transLoteOrigen.add(opt);
            });
            $(transLoteOrigen).prop('disabled', false).trigger('change');

            // Cargar Destinos Compatibles
            const resDest = await fetch(`${baseUrl}?action=obtenerDestinosCompatibles&producto_id=${pId}`);
            const destinos = await resDest.json();
            transProdDestino.innerHTML = '<option value="">Seleccione Destino...</option>';
            destinos.forEach(d => {
                const opt = new Option(`${d.sku} - ${d.nombre} (${d.unidad_medida})`, d.id);
                opt.dataset.factor = d.rendimiento_teorico;
                opt.dataset.unidad = d.unidad_medida;
                transProdDestino.add(opt);
            });
            $(transProdDestino).prop('disabled', false).trigger('change');
        });

        // 3. Al cambiar Lote Origen -> Actualizar Stock Disponible
        $(transLoteOrigen).on('change', function() {
            const stock = parseFloat(this.selectedOptions[0]?.dataset.stock || 0);
            stockSpan.textContent = stock.toFixed(2);
            transCantOrigen.max = stock;
        });

        // 4. Al cambiar Producto Destino -> Lotes Destino
        $(transProdDestino).on('change', async function() {
            const pId = this.value;
            const aId = transAlmacen.value;
            if(!pId) return;

            const res = await fetch(`${baseUrl}?action=obtenerLotes&producto_id=${pId}&almacen_id=${aId}`);
            const lotes = await res.json();
            transLoteDestino.innerHTML = '<option value="0">-- Crear Lote Nuevo --</option>';
            lotes.forEach(l => {
                transLoteDestino.add(new Option(`Sumar a: ${l.codigo_lote} (Disp: ${l.cantidad_actual})`, l.id));
            });
            $(transLoteDestino).prop('disabled', false).trigger('change');
            calcularTeorico();
        });

        function calcularTeorico() {
            const factor = parseFloat(transProdDestino.selectedOptions[0]?.dataset.factor || 0);
            const cant = parseFloat(transCantOrigen.value || 0);
            if(factor && cant) {
                const sugerido = (factor * cant).toFixed(2);
                infoConversion.innerHTML = `<i class="fas fa-magic me-1"></i> Rendimiento esperado: ${sugerido}`;
                transCantDestino.placeholder = sugerido;
            } else {
                infoConversion.innerHTML = "";
            }
        }

        transCantOrigen.addEventListener('input', calcularTeorico);

        // 5. Submit Formulario Transmutación
        document.getElementById('formTransmutacion').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            if(parseFloat(transCantOrigen.value) > parseFloat(stockSpan.textContent)) {
                alert("⚠️ Cantidad insuficiente en el lote de origen.");
                return;
            }

            try {
                const res = await fetch(`${baseUrl}?action=guardar`, { method: 'POST', body: formData });
                const result = await res.json();
                
                if(result.status === 'success') {
                    alert("✅ Transmutación registrada correctamente.");
                    location.reload();
                } else {
                    alert("❌ Error: " + result.message);
                }
            } catch (e) { alert("Error de conexión con el servidor."); }
        });

        // 6. Submit Nueva Equivalencia
        document.getElementById('formNuevaEquivalencia').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            
            try {
                const formData = new FormData(this);
                const res = await fetch(`${baseUrl}?action=guardarEquivalencia`, { method: 'POST', body: formData });
                const result = await res.json();
                
                if(result.status === 'success') {
                    alert(result.message);
                    location.reload();
                } else {
                    alert("❌ " + result.message);
                }
            } catch (error) {
                alert("❌ Error de red");
            } finally { btn.disabled = false; }
        });
    });
    </script>
</body>
</html>