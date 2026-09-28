<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mermas | Control de Inventario</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
    <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">

    <!-- CSS Frameworks & Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- CSS DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
            --sidebar-width: 260px; 
            --navbar-height: 65px;
            --apple-gray: #f5f5f7;
            --apple-blue: #0071e3;
            --apple-red: #ff3b30;
        }

        body { 
            background-color: var(--apple-gray); 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .main-content { 
           
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
        }

        /* Estilo Tarjeta Mac */
        .card-apple { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 4px 24px rgba(0,0,0,0.04); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .header-title {
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        /* Inputs Estilo iOS/macOS */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #d2d2d7;
            padding: 0.6rem 1rem;
            background-color: rgba(255, 255, 255, 0.5);
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
            background-color: #fff;
        }

        .btn-apple-danger {
            background-color: var(--apple-red);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 500;
            transition: transform 0.1s ease;
        }

        .btn-apple-danger:hover {
            background-color: #e32d24;
            transform: scale(1.02);
        }

        .stock-badge {
            background: #e8e8ed;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #424245;
        }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) renderizarLayout('Mermas'); ?>

    <div class="main-content">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i> Merma registrada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex align-items-center mb-4">
            <div class="bg-danger text-white rounded-3 p-3 me-3 shadow-sm">
                <i class="fas fa-box-open fa-lg"></i>
            </div>
            <div>
                <h2 class="header-title mb-0">Gestión de Mermas</h2>
                <p class="text-body-secondary mb-0">Registra pérdidas o bajas de inventario</p>
            </div>
        </div>

        <!-- FORMULARIO NUEVA MERMA -->
        <div class="card card-apple mb-4">
            <div class="card-body p-4 p-md-5">
                <form id="formMerma" action="/myvet/app/controllers/mermasController.php?action=guardarMerma" method="POST">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase fw-bold text-body-secondary">Almacén de Origen</label>
                            <select name="almacen_id" id="merma_almacen" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase fw-bold text-body-secondary">Producto</label>
                            <select name="producto_id" id="merma_producto" class="form-select" disabled required>
                                <option value="">Seleccione almacén</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase fw-bold text-body-secondary">Lote Específico</label>
                            <select name="lote_id" id="merma_lote" class="form-select" disabled required>
                                <option value="">Seleccione producto</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-uppercase fw-bold text-body-secondary">Cantidad a Retirar</label>
                            <input type="number" step="0.01" min="0.01" name="cantidad" id="merma_cantidad" class="form-control form-control-lg" placeholder="0.00" required>
                            <div class="mt-2">
                                <span class="stock-badge">Disponible: <strong id="stock_disponible">0</strong></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase fw-bold text-body-secondary">Motivo de Merma</label>
                            <select name="tipo_merma" class="form-select form-select-lg" required>
                                <option value="daño">📦 Daño / Rotura</option>
                                <option value="robo">⚠️ Robo / Extravío</option>
                                <option value="caducidad">⌛ Caducidad</option>
                                <option value="otro">🔍 Otro motivo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase fw-bold text-body-secondary">Observaciones</label>
                            <textarea name="observaciones" class="form-control text-uppercase" rows="1" placeholder="Detalles adicionales..."></textarea>
                        </div>
                    </div>

                    <div class="mt-5 border-top pt-4 text-end">
                        <button type="submit" class="btn btn-apple-danger btn-lg text-white">
                            <i class="fas fa-minus-circle me-2"></i> Confirmar Registro de Merma
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TARJETA HISTORIAL CON FILTROS -->
        <div class="card card-apple shadow-sm border-0 rounded-4">
            <!-- CABECERA CON BARRA DE FILTROS -->
            <div class="p-3 border-bottom bg-white rounded-top-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                    <h6 class="m-0 font-weight-bold text-dark me-auto">
                        <i class="fas fa-trash-alt me-2 text-danger"></i>Historial de Mermas
                    </h6>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btnLimpiarFiltros">
                        <i class="fas fa-sync-alt me-1"></i> Limpiar Filtros
                    </button>
                </div>

                <div class="row g-2 align-items-end pt-2">
                    <!-- BUSCADOR GENERAL -->
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-semibold text-secondary mb-1">Buscar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="f_search" class="form-control form-control-sm bg-light border-0" placeholder="Producto, lote, motivo...">
                        </div>
                    </div>

                    <!-- FILTRO ALMACÉN -->
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-semibold text-secondary mb-1">Almacén</label>
                        <select id="f_almacen" class="form-select form-select-sm bg-light border-0">
                            <option value="0">Todos los almacenes</option>
                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- FILTRO TIPO MERMA -->
                    <div class="col-md-2">
                        <label class="form-label text-xs fw-semibold text-secondary mb-1">Tipo Merma</label>
                        <select id="f_tipo_merma" class="form-select form-select-sm bg-light border-0">
                            <option value="">Todos los tipos</option>
                            <option value="daño">Daño</option>
                            <option value="robo">Robo</option>
                            <option value="caducidad">Caducidad</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- FILTRO RANGO FECHA -->
                    <div class="col-md-2">
                        <label class="form-label text-xs fw-semibold text-secondary mb-1">Rango Fecha</label>
                        <select id="f_rango" class="form-select form-select-sm bg-light border-0">
                            <option value="todos">Todas</option>
                            <option value="hoy">Hoy</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Esta Semana</option>
                            <option value="mes">Este Mes</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>

                    <!-- FECHAS PERSONALIZADAS -->
                    <div class="col-md-2 d-none" id="box_fechas_custom">
                        <div class="row g-1">
                            <div class="col-6">
                                <input type="date" id="f_inicio" class="form-control form-control-sm bg-light border-0" title="Desde">
                            </div>
                            <div class="col-6">
                                <input type="date" id="f_fin" class="form-control form-control-sm bg-light border-0" title="Hasta">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLA MERMAS -->
            <div class="table-responsive p-0" id="contenedor-tabla">
                <table class="table table-hover align-middle mb-0" id="tablaMermas" style="font-size: 0.95rem; width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0 py-3 text-uppercase small fw-bold text-body-secondary">Fecha</th>
                            <th class="border-0 py-3 text-uppercase small fw-bold text-body-secondary">Producto / Lote</th>
                            <th class="border-0 py-3 text-uppercase small fw-bold text-body-secondary">Almacén</th>
                            <th class="border-0 py-3 text-uppercase small fw-bold text-body-secondary text-center">Cantidad</th>
                            <th class="border-0 py-3 text-uppercase small fw-bold text-body-secondary text-center">Tipo</th>
                         <th class="border-0 py-3 text-uppercase small fw-bold text-body-secondary text-center">Motivo</th>
                            <th class="pe-4 border-0 py-3 text-uppercase small fw-bold text-body-secondary text-end">Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Carga dinámica vía DataTables / Ajax -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- LIBRERÍAS JAVASCRIPT REQUERIDAS (ORDEN CRÍTICO) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SCRIPT PRINCIPAL UNIFICADO -->
    <script>
    const baseUrlMermas = '/myvet/app/controllers/mermasController.php';
    let tablaMermas;

    $(document).ready(function() {
        // =========================================================================
        // 1. INICIALIZACIÓN DE DATATABLES
        // =========================================================================
        tablaMermas = $('#tablaMermas').DataTable({
        "destroy": true, 
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "ajax": {
                "url": baseUrlMermas + "?action=listar",
                "type": "GET",
                "dataSrc": "",
                "data": function(d) {
                    d.f_search     = $('#f_search').val();
                    d.f_almacen    = $('#f_almacen').val();
                    d.f_tipo_merma = $('#f_tipo_merma').val();
                    d.f_rango      = $('#f_rango').val();
                    d.f_inicio     = $('#f_inicio').val();
                    d.f_fin        = $('#f_fin').val();
                }
            },
            "columns": [
                { 
                    "data": "fecha_reporte",
                    "className": "ps-4",
                    "render": function(data) {
                        if (!data) return 'N/A';
                        const d = new Date(data);
                        if (isNaN(d.getTime())) return data;
                        
                        const dia = d.toLocaleDateString('es-ES', {day:'2-digit', month:'2-digit', year:'numeric'});
                        const hora = d.toLocaleTimeString('es-ES', {hour:'2-digit', minute:'2-digit'});
                        
                        return `
                            <div class="fw-bold">${dia}</div>
                            <div class="small text-body-secondary opacity-75">${hora} h</div>
                        `;
                    }
                },
                { 
                    "data": null,
                    "render": function(data, type, row) {
                        const producto = row.producto_nombre || 'N/A';
                        const lote = row.codigo_lote || 'N/A';
                        return `
                            <div class="fw-bold text-dark">${producto}</div>
                            <div class="small text-body-secondary text-uppercase" style="font-size: 0.75rem;">
                                LOTE: ${lote}
                            </div>
                        `;
                    }
                },
                { 
                    "data": "almacen_nombre",
                    "render": function(data) {
                        return `
                            <span class="text-body-secondary small">
                                <i class="fas fa-warehouse me-1 text-secondary opacity-50"></i> 
                                ${data || 'N/A'}
                            </span>
                        `;
                    }
                },
                { 
                    "data": null,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        const cant = parseFloat(row.cantidad || 0).toFixed(2);
                        return `<span class="fw-bold text-danger">-${cant}</span>`;
                    }
                },
                { 
                    "data": "tipo_merma",
                    "className": "text-center",
                    "render": function(data) {
                        const tipo = (data || 'otro').toLowerCase();
                        let badgeClass = 'bg-secondary text-white';

                        if (tipo === 'daño') badgeClass = 'bg-warning text-dark';
                        else if (tipo === 'robo') badgeClass = 'bg-danger text-white';
                        else if (tipo === 'caducidad') badgeClass = 'bg-info text-dark';

                        const texto = tipo.charAt(0).toUpperCase() + tipo.slice(1);
                        return `
                            <span class="badge rounded-pill ${badgeClass} px-3 py-1 fw-medium shadow-sm" style="font-size: 0.75rem;">
                                ${texto}
                            </span>
                        `;
                    }
                },
                   { 
                    "data": "descripcion_suceso",
                    "render": function(data) {
                        return `
                            <span class="text-body-secondary small">
                                <i class="fas fa-warehouse me-1 text-secondary opacity-50"></i> 
                                ${data || 'N/A'}
                            </span>
                        `;
                    }
                },
                { 
                    "data": "responsable",
                    "className": "pe-4 text-end",
                    "render": function(data) {
                        return `<div class="small fw-medium card-title-text">${data || 'S/R'}</div>`;
                    }
                }
            ],
            "order": [[ 0, "desc" ]],
            "pageLength": 10,
            "responsive": true,
            "dom": '<"d-flex justify-content-between p-3"f>rt<"d-flex justify-content-between p-3 align-items-center"ip>',
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm mb-0');
            }
        });

        // =========================================================================
        // 2. RECARGA DINÁMICA AL CAMBIAR FILTROS
        // =========================================================================
        $('#f_almacen, #f_tipo_merma, #f_rango, #f_inicio, #f_fin').on('change', function() {
            tablaMermas.ajax.reload();
        });

        let searchTimeout;
        $('#f_search').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                tablaMermas.ajax.reload();
            }, 300);
        });

        $('#f_rango').on('change', function() {
            if ($(this).val() === 'personalizado') {
                $('#box_fechas_custom').removeClass('d-none');
            } else {
                $('#box_fechas_custom').addClass('d-none');
                $('#f_inicio, #f_fin').val('');
            }
        });

        $('#btnLimpiarFiltros').on('click', function() {
            $('#f_search').val('');
            $('#f_almacen').val('0');
            $('#f_tipo_merma').val('');
            $('#f_rango').val('todos').trigger('change');
            $('#f_inicio, #f_fin').val('');
            tablaMermas.ajax.reload();
        });

        // =========================================================================
        // 3. SELECTS EN CASCADA
        // =========================================================================
        const almacenSelect  = document.getElementById('merma_almacen');
        const productoSelect = document.getElementById('merma_producto');
        const loteSelect     = document.getElementById('merma_lote');
        const cantidadInput  = document.getElementById('merma_cantidad');
        const stockSpan      = document.getElementById('stock_disponible');
        const form           = document.getElementById('formMerma');

        if (almacenSelect) {
            almacenSelect.addEventListener('change', async function() {
                const almacenId = this.value;
                if (!almacenId) return resetForm();
                productoSelect.innerHTML = '<option>Cargando...</option>';
                productoSelect.disabled = true;

                try {
                    const response = await fetch(`${baseUrlMermas}?action=obtenerProductosAlmacen&almacen_id=${almacenId}`);
                    const productos = await response.json();
                    productoSelect.innerHTML = '<option value="">Seleccione producto</option>';
                    productos.forEach(p => {
                        const option = new Option(`${p.sku} - ${p.nombre} (Stock: ${p.stock})`, p.id);
                        productoSelect.appendChild(option);
                    });
                    productoSelect.disabled = false;
                } catch (e) {
                    productoSelect.innerHTML = '<option>Error al cargar</option>';
                }
            });
        }

        if (productoSelect) {
            productoSelect.addEventListener('change', async function() {
                const productoId = this.value;
                const almacenId = almacenSelect.value;
                if (!productoId || !almacenId) return resetLotes();
                loteSelect.innerHTML = '<option>Cargando...</option>';
                loteSelect.disabled = true;

                try {
                    const response = await fetch(`${baseUrlMermas}?action=obtenerLotes&producto_id=${productoId}&almacen_id=${almacenId}`);
                    const lotes = await response.json();
                    loteSelect.innerHTML = '<option value="">Seleccione lote</option>';
                    lotes.forEach(l => {
                        const option = new Option(`${l.codigo_lote} (Disp: ${l.cantidad_actual})`, l.id);
                        option.dataset.stock = l.cantidad_actual;
                        loteSelect.appendChild(option);
                    });
                    loteSelect.disabled = false;
                } catch (e) {
                    loteSelect.innerHTML = '<option>Error al cargar</option>';
                }
            });
        }

        if (loteSelect) {
            loteSelect.addEventListener('change', function() {
                const selectedOption = this.selectedOptions[0];
                const stock = parseFloat(selectedOption?.dataset.stock || 0);
                stockSpan.textContent = stock.toLocaleString('es-MX', { minimumFractionDigits: 2 });
                cantidadInput.max = stock;
            });
        }

        function resetForm() {
            if (productoSelect) {
                productoSelect.innerHTML = '<option value="">Seleccione almacén</option>';
                productoSelect.disabled = true;
            }
            resetLotes();
            if (stockSpan) stockSpan.textContent = '0';
        }

        function resetLotes() {
            if (loteSelect) {
                loteSelect.innerHTML = '<option value="">Seleccione producto</option>';
                loteSelect.disabled = true;
            }
        }

        // =========================================================================
        // 4. GUARDAR MERMA VÍA AJAX Y ACTUALIZAR DATATABLES
        // =========================================================================
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const stock = parseFloat(stockSpan.textContent.replace(/,/g, '')) || 0;
                const cantidad = parseFloat(cantidadInput.value) || 0;

                if (cantidad > stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Insuficiente',
                        text: `No puedes retirar ${cantidad} si solo hay ${stock} disponibles.`,
                        confirmButtonColor: '#0071e3'
                    });
                    return;
                }

                const confirmacion = await Swal.fire({
                    title: '¿Registrar Merma?',
                    text: "Esta acción descontará el stock de forma permanente.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, registrar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ff3b30'
                });

                if (confirmacion.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        didOpen: () => { Swal.showLoading(); },
                        allowOutsideClick: false
                    });

                    try {
                        const formData = new FormData(form);
                        const response = await fetch(`${baseUrlMermas}?action=guardarMerma`, {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();

                        if (result.status === 'success') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Completado',
                                text: result.message || 'La merma ha sido registrada.',
                                timer: 1800,
                                showConfirmButton: false
                            });

                            form.reset();
                            resetForm();
                            tablaMermas.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'No se pudo procesar la solicitud.'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'Hubo un problema al contactar con el servidor.'
                        });
                    }
                }
            });
        }
    });
    </script>
</body>
</html>