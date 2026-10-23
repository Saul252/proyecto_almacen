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
    <title>Cotizaciones | cfsistem</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>
    <link href="/myvet/css/solicitudesCompra.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>

    <!-- 🔥 FIX: estilos movidos al <head> -->
    <style>
        #modalImprimirSolicitud .modal-content {
            border-radius: 10px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            background: #fff;
        }

        #modalImprimirSolicitud .modal-header {
            background: #ffffff02;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
            padding: 1.2rem 1.5rem;
        }

        #modalImprimirSolicitud .modal-footer {
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
        }

        #areaImpresion {
            padding: 2rem;
            background: #ffffff;
            color: #111827;
            font-family: "Segoe UI", system-ui, sans-serif;
        }

        #areaImpresion h2 {
            font-weight: 700;
            font-size: 1.6rem;
            color: #1f2937;
            margin-bottom: 0.3rem;
        }

        #areaImpresion h5 {
            font-weight: 500;
            color: #4b5563;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: none;
            background: #ffffff;
        }

        .card:hover {
            transform: none;
            box-shadow: none;
        }

        .table {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: #f3f4f6;
            color: #111827;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px;
        }

        .table tbody td {
            border-color: #0000006b;
            padding: 10px;
            font-size: 0.95rem;
        }

        .table tbody tr:nth-child(even) {
            background: #03030392;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 1.5rem 0;
        }

        .signature-line {
            width: 180px;
            height: 1px;
            background: #111827;
            margin-bottom: 6px;
        }

        .signature-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
        }

        .btn-primary {
            background: #2563eb;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #modalImprimirSolicitud,
            #modalImprimirSolicitud * {
                visibility: visible;
            }

            #modalImprimirSolicitud {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .modal-header,
            .modal-footer {
                display: none !important;
            }

            .modal-content {
                box-shadow: none !important;
                border: none !important;
            }

            #areaImpresion {
                padding: 1rem;
            }

            .table thead th {
                background: #f0f0f0 !important;
                color: #000 !important;
            }

            .table tbody tr:nth-child(even) {
                background: #fff !important;
            }
        }
    </style>
</head>

<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">

        <div class="glass-card p-4 mb-4">

            <div class="row align-items-center ">
                <div class="col-md-8">
                    <h1 class="h3 fw-bold mb-1 card-title-text">Cotizaciones</h1>
                    <p class="text-body-secondary small card-title-text">Gestión de cotizaciones de materiales</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-add" onclick="nuevaCotizacion()">
                        <i class="bi bi-plus-lg me-2"></i> Crear Cotizacion
                    </button>
                    <button class="btn btn-outline-secondary" onclick="descargarProductos()"
                        title="Descargar Excel de Productos">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>

            <div class="glass-card p-4 mb-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 card-title-text">
                            <i class="bi bi-funnel-fill text-primary me-2"></i>
                            Filtros de búsqueda
                        </h5>
                        <small class="text-body-secondary card-title-text">
                            Filtra las cotizaciones por fecha, almacén, estado o cliente.
                        </small>
                    </div>
                </div>

                <div class="row g-3 align-items-end">

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Inicio</label>
                        <input type="date" id="fechaInicio" value="<?= date('Y-m-01') ?>" class="form-control shadow-sm"
                            style="border-radius:12px;">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Fin</label>
                        <input type="date" id="fechaFin" value="<?= date('Y-m-d') ?>" class="form-control shadow-sm"
                            style="border-radius:12px;">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Almacén</label>
                        <select id="filtroAlmacen" class="form-select shadow-sm" style="border-radius:12px;">
                            <?php if (isset($es_admin) && $es_admin): ?>
                                <option value="">Todos los almacenes</option>
                            <?php endif; ?>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?= htmlspecialchars($alm['id']) ?>">
                                    <?= htmlspecialchars($alm['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Estado</label>
                        <select id="filtroEstado" class="form-select shadow-sm" style="border-radius:12px;">
                            <option value="">Todos</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="COMPLETADO">Completado</option>
                            <option value="CANCELADO">Cancelado</option>
                        </select>
                    </div>
                    <?php if ($puede == true): ?>
                        <div class="col-md-2">
                            <label for="select-usuarios"
                                class="form-label fw-bold small text-body-secondary text-uppercase">Vendedor</label>
                            <!-- 🔥 FIX: se quitó onchange="getVentas()" (roto, ya hay listener jQuery) -->
                            <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id">
                                <option value=""> Seleccione vendedor</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="col-lg-3 col-md-12">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Buscar</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text">
                                <i class="bi bi-search text-secondary"></i>
                            </span>
                            <input type="text" id="buscadorGeneral" class="form-control" placeholder="Folio o Cliente">
                        </div>
                    </div>

                </div>

            </div>

            <div class="glass-card p-4">
                <div class="table-responsive">
                    <!-- 🔥 FIX: se quitó id="tablaSolicitudes" porque DataTables no se inicializaba -->
                    <table class="table align-middle w-100">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Almacén</th>
                                <th>Vendedor</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCotizacionesListar">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4">

                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2a37, #334155);">
                    <h6 class="modal-title fw-semibold">
                        <i class="bi bi-wallet2 me-2"></i> Registrar pago
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <input type="hidden" id="idC" name="idC">

                <div class="modal-body p-4">
                    <div class="table-responsive mb-3">
                        <table class="table align-middle">
                            <thead></thead>
                            <tbody id="print-productos"></tbody>
                        </table>
                    </div>

                    <div class="text-center mb-4">
                        <small class="text-body-secondary d-block">Total a pagar</small>
                        <h3 id="pagoTotal" class="fw-bold card-title-text m-0">$0.00</h3>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-body-secondary">Monto recibido</label>
                        <!-- 🔥 FIX: min=0, step=0.01 -->
                        <input type="number" id="montoPago" class="form-control form-control-lg rounded-3"
                            placeholder="0.00" min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-body-secondary">Método de pago</label>
                        <!-- 🔥 FIX: se agregó required y value="credito" en lugar de value="" -->
                        <select id="metodoPago" class="form-select form-select-lg rounded-3" required>
                            <option value="">Seleccione</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="deposito">Depósito</option>
                            <option data-metodo="credito" value="credito">Compra a credito</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="refBox">
                        <label class="form-label small text-body-secondary">Referencia</label>
                        <input type="text" id="referenciaPago" class="form-control form-control-lg rounded-3"
                            placeholder="Número de referencia">
                    </div>
                </div>

                <div class="modal-footer px-4 pb-4 pt-0" id="boton">
                    <!-- 🔥 FIX: botón cancelar por defecto para poder cerrar aunque falle procederPago -->
                    <button type="button" class="btn btn-light w-50 rounded-3" data-bs-dismiss="modal">Cancelar</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php require_once __DIR__ . '/cotizacionesModales/editarCotizacion.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/ModalCotizacion.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/nuevoClienteModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalProveedoresCompra.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/imprimirCotizacion.php'; ?>

    <script>
        // 🔥 FIX: constantes con prefijo para no chocar con otros archivos
        const COT_PAGE_URL = '/myvet/app/controllers/cotizacionesController.php';
        const URL_CONTROLADOR = '/myvet/app/controllers/cotizacionesController.php';
        const COT_ROL_USUARIO = <?= isset($_SESSION['rol_id']) ? (int) $_SESSION['rol_id'] : 0 ?>;
        // 🔥 FIX: datost ahora es local (window por compatibilidad con editarCotizacion.php)
        window.totalGlobalPago = 0;
        window.datost = null;

        $(document).ready(function () {
            // 🔥 FIX: se quitó el doble cargarCotizaciones()
            cargarCotizaciones();
            cargarUsuariosSelect();
        });

        // 🔥 FIX: listener con debounce para el buscador
        let cotBuscarTimer = null;

        async function cargarUsuariosSelect() {
            const select = document.getElementById('select-usuarios');
            if (!select) return;

            try {
                const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();

                if (resultado.success && Array.isArray(resultado.data)) {
                    // 🔥 FIX: limpiar antes de poblar
                    select.innerHTML = '<option value="">Seleccione vendedor</option>';
                    resultado.data.forEach(usuario => {
                        const opcion = document.createElement('option');
                        opcion.value = usuario.id;
                        opcion.textContent = `${usuario.nombre}`;
                        select.appendChild(opcion);
                    });
                } else {
                    select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                    console.error('El backend no devolvió success:true o la estructura cambió');
                }
            } catch (error) {
                select.innerHTML = '<option value="">Error al cargar la lista</option>';
                console.error('Error al ejecutar cargarUsuariosSelect:', error);
            }
        }

        // 🔥 FIX: cargarUsuariosSelectPago se mantiene por compatibilidad, pero ya no se usa
        async function cargarUsuariosSelectPago() {
            const select = document.getElementById('select-usuarios2');
            if (!select) return;

            try {
                const url = '/myvet/app/controllers/accesoController.php?action=obtenerUsuarios';
                const respuesta = await fetch(url);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();

                if (resultado.success && Array.isArray(resultado.data)) {
                    select.innerHTML = '<option value="">Seleccione vendedor</option>';
                    resultado.data.forEach(usuario => {
                        const opcion = document.createElement('option');
                        opcion.value = usuario.id;
                        opcion.textContent = `${usuario.nombre}`;
                        select.appendChild(opcion);
                    });
                } else {
                    select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                }
            } catch (error) {
                select.innerHTML = '<option value="">Error al cargar la lista</option>';
                console.error('Error al ejecutar cargarUsuariosSelectPago:', error);
            }
        }

        async function descargarProductos() {
            try {
                const resp = await fetch(`/myvet/app/controllers/cotizacionesController.php?action=obtenerProductos`);
                const res = await resp.json();

                // 🔥 FIX: validar res.data
                if (!res.success || !Array.isArray(res.data)) {
                    throw new Error(res.message || 'No se pudieron obtener los productos');
                }

                await exportarProductosAExcel(res.data);
            } catch (e) {
                console.error('Error al descargar productos:', e);
                Swal.fire('Error', e.message || 'No se pudieron descargar los productos', 'error');
            }
        }

        async function exportarProductosAExcel(productos) {
            try {
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Inventario Productos');

                worksheet.columns = [
                    { header: 'ID Producto', key: 'producto_id', width: 12 },
                    { header: 'SKU', key: 'sku', width: 15 },
                    { header: 'Nombre del Producto', key: 'nombre', width: 30 },
                    { header: 'U. Medida', key: 'unidad_medida', width: 12 },
                    { header: 'U. Reporte', key: 'unidad_reporte', width: 12 },
                    { header: 'Factor Conv.', key: 'factor_conversion', width: 15 },
                    { header: 'ID Cat.', key: 'categoria_id', width: 10 },
                    { header: 'P. Minorista', key: 'precio_minorista', width: 15 },
                    { header: 'P. Mayorista', key: 'precio_mayorista', width: 15 },
                    { header: 'P. Distribuidor', key: 'precio_distribuidor', width: 15 },
                    { header: 'Medidas Adicionales (Nombre: Equiv.)', key: 'medidas_adicionales', width: 40 }
                ];

                const headerRow = worksheet.getRow(1);
                headerRow.font = { name: 'Segoe UI', size: 11, bold: true, color: { argb: 'FFFFFF' } };
                headerRow.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: '2E5B82' }
                };
                headerRow.alignment = { vertical: 'middle', horizontal: 'center' };
                headerRow.height = 25;

                productos.forEach((prod, index) => {
                    const medidasTexto = prod.medidas_adicionales
                        ? prod.medidas_adicionales.map(m => `${m.nombre}: ${parseFloat(m.equivalencia).toFixed(4)}`).join(' | ')
                        : 'Ninguna';

                    const row = worksheet.addRow({
                        producto_id: prod.producto_id,
                        sku: prod.sku,
                        nombre: prod.nombre,
                        unidad_medida: prod.unidad_medida,
                        unidad_reporte: prod.unidad_reporte,
                        factor_conversion: parseFloat(prod.factor_conversion || 0),
                        categoria_id: prod.categoria_id,
                        precio_minorista: parseFloat(prod.precio_minorista || 0),
                        precio_mayorista: parseFloat(prod.precio_mayorista || 0),
                        precio_distribuidor: parseFloat(prod.precio_distribuidor || 0),
                        medidas_adicionales: medidasTexto
                    });

                    row.getCell('producto_id').alignment = { horizontal: 'center' };
                    row.getCell('categoria_id').alignment = { horizontal: 'center' };
                    row.getCell('sku').alignment = { horizontal: 'center' };
                    row.getCell('unidad_medida').alignment = { horizontal: 'center' };
                    row.getCell('unidad_reporte').alignment = { horizontal: 'center' };

                    row.getCell('factor_conversion').numFmt = '#,##0.00';
                    row.getCell('precio_minorista').numFmt = '$#,##0.00';
                    row.getCell('precio_mayorista').numFmt = '$#,##0.00';
                    row.getCell('precio_distribuidor').numFmt = '$#,##0.00';

                    row.eachCell((cell) => {
                        cell.font = { name: 'Segoe UI', size: 10 };
                        cell.border = {
                            top: { style: 'thin', color: { argb: 'E0E0E0' } },
                            bottom: { style: 'thin', color: { argb: 'E0E0E0' } },
                            left: { style: 'thin', color: { argb: 'E0E0E0' } },
                            right: { style: 'thin', color: { argb: 'E0E0E0' } }
                        };
                        if (index % 2 !== 0) {
                            cell.fill = {
                                type: 'pattern',
                                pattern: 'solid',
                                fgColor: { argb: 'F9FAFB' }
                            };
                        }
                    });

                    row.height = 20;
                });

                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'Reporte_Productos_Inventario.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(link.href);
            } catch (e) {
                console.error('Error al exportar Excel:', e);
                Swal.fire('Error', 'No se pudo generar el Excel', 'error');
            }
        }

        async function cargarCotizaciones() {
            try {
                const params = new URLSearchParams({
                    action: 'listarCotizaciones',
                    almacen: $('#filtroAlmacen').val() || '',
                    fechaInicio: $('#fechaInicio').val() || '',
                    fechaFin: $('#fechaFin').val() || '',
                    estado: $('#filtroEstado').val() || '',
                    buscador: $('#buscadorGeneral').val() || '',
                    vendedor: $('#select-usuarios').val() || ''
                });

                const res = await fetch(`${COT_PAGE_URL}?${params.toString()}`);

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                // 🔥 FIX: leer como texto primero para diagnosticar
                const texto = await res.text();
                let data;
                try {
                    data = JSON.parse(texto);
                } catch (e) {
                    console.error('[COT] Respuesta no JSON:', texto.substring(0, 500));
                    throw new Error('El servidor no devolvió JSON válido');
                }

                // 🔥 FIX: tolerante a múltiples formatos
                let lista = null;
                if (Array.isArray(data)) {
                    lista = data;
                } else if (data && Array.isArray(data.data)) {
                    lista = data.data;
                } else if (data && Array.isArray(data.cotizaciones)) {
                    lista = data.cotizaciones;
                } else if (data && Array.isArray(data.rows)) {
                    lista = data.rows;
                }

                // Si tiene success:false explícito, es error
                if (data && data.success === false) {
                    throw new Error(data.message || 'El servidor devolvió success:false');
                }

                // Si no encontramos array, error
                if (!lista) {
                    console.error('[COT] Estructura no reconocida:', data);
                    throw new Error('Respuesta del servidor no reconocida (no hay array de datos)');
                }

                let tablahtml = '';

                if (lista.length === 0) {
                    tablahtml = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-body-secondary">
                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                        No se encontraron cotizaciones con los filtros seleccionados.
                    </td>
                </tr>`;
                } else {
                    lista.forEach(s => {
                        const id = String(s.id).padStart(5, '0');

                        const fecha = s.fecha ? new Date(s.fecha) : null;
                        const fechaFormateada = (fecha && !isNaN(fecha))
                            ? fecha.toLocaleDateString('es-MX') + ' ' +
                            fecha.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })
                            : 'Sin fecha';

                        const status = (s.estado || 'PENDIENTE').toUpperCase();

                        let clase = 'bg-secondary text-white';
                        switch (status) {
                            case 'PENDIENTE': clase = 'bg-warning card-title-text'; break;
                            case 'COMPLETADO': clase = 'bg-primary text-white'; break;
                            case 'CANCELADO': clase = 'bg-danger text-white'; break;
                        }

                        const clienteNombre = s.cliente_nombre || 'Sin asignar';
                        const almacenNombre = s.almacen_nombre || 'Sin almacén';
                        const vendedorNombre = s.vendedor || 'Sin asignar';

                        tablahtml += `
                    <tr>
                        <td><span class="card-title-text fw-bold">#${id}</span></td>
                        <td class="text-body-secondary small">${fechaFormateada}</td>
                        <td class="fw-medium">${clienteNombre}</td>
                        <td><span class="card-title-text border">${almacenNombre}</span></td>
                        <td><span class="card-title-text border">${vendedorNombre}</span></td>
                        <td>
                            <span class="badge badge-status ${clase} rounded-pill">
                                ${status}
                            </span>
                        </td>
                        <td class="text-end">`;

                        if (status === 'PENDIENTE' && COT_ROL_USUARIO < 3) {
                            tablahtml += `
                        <button class="btn btn-sm btn-white border shadow-sm"
                            onclick="gestionarSolicitud(${s.id})" title="Gestionar">
                            <i class="bi bi-eye text-primary"></i> Gestionar
                        </button>
                        <button class="btn btn-sm btn-white border shadow-sm"
                            onclick="eliminarSolicitud(${s.id})" title="Eliminar">
                            <i class="bi bi-trash text-danger"></i>
                        </button>`;
                        }

                        if (status === 'COMPLETADO' && COT_ROL_USUARIO < 3) {
                            tablahtml += `
                        <button class="btn btn-sm btn-white border shadow-sm"
                            onclick="gestionarSolicitud(${s.id})" title="Reutilizar">
                            <i class="bi bi-eye text-primary"></i> REUTILIZAR
                        </button>`;
                        }

                        tablahtml += `
                        <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3"
                            onclick="prepararImpresion(${s.id})"
                            title="Imprimir solicitud">
                            <i class="bi bi-printer text-primary me-1"></i>
                            <span class="card-title-text fw-medium">Imprimir</span>
                        </button>
                    </td>
                </tr>`;
                    });
                }

                $('#tablaCotizacionesListar').html(tablahtml);

            } catch (e) {
                console.error('[COT] Error al cargar cotizaciones:', e);
                $('#tablaCotizacionesListar').html(`
            <tr>
                <td colspan="7" class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                    Error al cargar las cotizaciones: ${e.message}
                    <br><small class="text-muted">Revisa la consola (F12) para más detalles.</small>
                </td>
            </tr>`);
            }
        }
        $(document).ready(function () {
            // 🔥 FIX: debounce en el buscador
            $('#buscadorGeneral').on('keyup', function () {
                clearTimeout(cotBuscarTimer);
                cotBuscarTimer = setTimeout(cargarCotizaciones, 300);
            });
            $('#filtroAlmacen').on('change', cargarCotizaciones);
            $('#filtroEstado').on('change', cargarCotizaciones);
            $('#fechaInicio').on('change', cargarCotizaciones);
            $('#fechaFin').on('change', cargarCotizaciones);
            $('#select-usuarios').on('change', cargarCotizaciones);
        });

        async function eliminarSolicitud(id) {
            const r = await Swal.fire({
                title: '¿Eliminar?',
                text: 'No podrás revertir esto',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, borrar'
            });
            if (r.isConfirmed) {
                try {
                    Swal.fire({ title: 'Eliminando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    const fd = new FormData();
                    fd.append('id', id);
                    const resp = await fetch(`${COT_PAGE_URL}?action=eliminar`, {
                        method: 'POST',
                        body: fd
                    });
                    const res = await resp.json();

                    if (res.status === 'success') {
                        // 🔥 FIX: recargar solo la tabla en lugar de toda la página
                        await Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1200, showConfirmButton: false });
                        cargarCotizaciones();
                    } else {
                        Swal.fire('Error', res.message || 'No se pudo eliminar', 'error');
                    }
                } catch (e) {
                    console.error('Error al eliminar:', e);
                    Swal.fire('Error', 'Fallo de conexión', 'error');
                }
            }
        }
    </script>

    <script>
        // 🔥 FIX: se ejecuta solo al cargar; afecta inputs existentes
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>

</html>