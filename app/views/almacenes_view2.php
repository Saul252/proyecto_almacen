<!DOCTYPE html>
<html lang="es">

<head>
    <!-- ✅ Meta viewport corregido -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenes | Sistema</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <!-- =========================================================
         1. Bootstrap CSS (primero)
         ========================================================= -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- =========================================================
         2. Select2 CSS + Tema Bootstrap 5 (después de Bootstrap)
         ========================================================= -->
    <link href="https://unpkg.com/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
    <!-- =========================================================
         3. Iconos y estilos propios
         ========================================================= -->
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <link href="/myvet/css/almacenes.css" rel="stylesheet">
    <?php
    if (function_exists('cargarEstilos')) {
        cargarEstilos();
    }
    ?>

    <!-- ✅ Estilos para evitar desplazamiento del contenido -->
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s ease;
            z-index: 1040;
        }

        .sidebar-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .main-content {
            width: 100%;
            padding: 1rem;
            transition: padding-left .3s ease;
        }
    </style>
</head>

<body>

    <!-- ✅ Overlay (debe estar ANTES del sidebar y del contenido) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <?php renderizarLayout($paginaActual); ?>

    <script>
        const productosInventario = <?= json_encode($productos) ?>;
    </script>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h2 class="fw-bold mb-0 card-title-text" style="letter-spacing: -0.02em;">
                <i class="bi bi-box-seam text-primary"></i> Módulo de Almacén
            </h2>

            <?php
            $rData = $resumenData ?? ['tipo' => 'error', 'nombre' => 'No disponible', 'mis_productos' => 0, 'total_sistema' => 0];
            $cant_prod = $rData['mis_productos'];
            $total_cat = $rData['total_sistema'];
            $cobertura = ($total_cat > 0) ? round(($cant_prod / $total_cat) * 100, 1) : 0;
            ?>

            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <div class="ios-micro-card border-blue">
                    <span class="ios-micro-label">Total de innversion</span>
                    <div class="ios-micro-value">$
                        <?= number_format($inversion, 2, '.', ',') ?>
                    </div>
                </div>



                <div class="ios-micro-card border-purple">
                    <span class="ios-micro-label">Catálogo</span>
                    <div class="ios-micro-value">
                        <?= number_format($total_cat) ?>
                    </div>
                    <div class="ios-micro-footer">Items</div>
                </div>



                <div class="d-flex justify-content-end align-items-center mb-3">
                    <button type="button"
                        class="btn btn-success rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm"
                        onclick="exportarProductosCSV()">
                        <i class="bi bi-file-earmark-excel-fill fs-5"></i> Exportar Productos (CSV)
                    </button>
                </div>
            </div>
        </div>

        <div class="card p-3 shadow-sm">
            <div class="row mb-4 g-2 g-md-3 align-items-center">

                <!-- Filtros: categoría + almacén -->
                <div class="col-12 col-md-auto d-flex gap-2">
                    <select id="filtroCategoria"
                        class="form-select form-select-sm rounded-3 shadow-none flex-fill bg-primary-subtle text-primary-emphasis border-primary-subtle fw-semibold">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="filtroAlmacen"
                        class="form-select form-select-sm rounded-3 shadow-none flex-fill bg-info-subtle text-info-emphasis border-info-subtle fw-semibold"
                        <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                        <?php if ($almacen_usuario == 0): ?>
                            <option value="0">Todos los Almacenes</option>
                        <?php endif; ?>
                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>" <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($alm['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Buscador -->
                <div class="col-12 col-md flex-grow-1">
                    <div class="input-group input-group-sm">
                        <span
                            class="input-group-text bg-primary-subtle text-primary-emphasis border-primary-subtle rounded-start-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="buscador"
                            class="form-control bg-primary-subtle text-body border-primary-subtle ps-1 shadow-none rounded-end-3"
                            placeholder="Buscar...">
                    </div>
                </div>

                <!-- Acciones -->
                <div class="col-12 col-md-auto ms-md-auto">
                    <div class="d-flex gap-2 align-items-center flex-wrap flex-md-nowrap justify-content-end">

                        <!-- Agregar Producto -->
                        <button type="button"
                            class="btn btn-sm bg-primary-subtle text-primary-emphasis border border-primary-subtle px-3 rounded-pill fw-semibold d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                            <i class="bi bi-plus-lg"></i> Agregar Producto
                        </button>

                        <!-- Herramientas -->
                        <div class="dropdown position-relative flex-grow-1 flex-md-grow-0">
                            <button type="button"
                                class="btn btn-sm bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 dropdown-toggle w-100"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-tools"></i> Herramientas
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
                                        onclick="abrirModalInventarioInicial()">
                                        <i class="bi bi-sliders text-warning"></i> Cargar Inventario por producto
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2"
                                        onclick="descargarProductos()">
                                        <i class="bi bi-download text-success"></i> Descargar Excel para carga masiva
                                    </button>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2"
                                        onclick="abrirModalCargarCSV()">
                                        <i class="bi bi-filetype-csv text-danger"></i> Cargar Inventario masivo
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Traspasos -->
                        <div class="dropdown position-relative flex-grow-1 flex-md-grow-0">
                            <button type="button"
                                class="btn btn-sm bg-purple-subtle text-purple-emphasis border border-purple-subtle px-3 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 dropdown-toggle w-100"
                                style="--bs-purple-rgb: 139,92,246;" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-arrow-left-right"></i> Traspasos
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
                                        data-bs-target="#modalTraspaso">
                                        <i class="bi bi-plus-circle text-primary"></i> Nuevo Traspaso
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
                                        data-bs-target="#modalTraspasosGestion" onclick="cargarTraspasos()">
                                        <i class="bi bi-check2-circle text-success"></i> Autorizar Traspasos
                                    </button>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

            <div class="table-responsive tabla-scroll">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark sticky-header">
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th>Descripccion</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Almacén</th>
                            <th width="60">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <tr data-categoria="<?= $p['categoria_id'] ?>" data-almacen="<?= $p['almacen_id'] ?>">
                                <td class="fw-bold">
                                    <?= $p['sku'] ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['descripcion'] ?? 'sin descripcion') ?>
                                </td>

                                <td><span style="
    --bs-badge-padding-x: 0.65em;
    --bs-badge-padding-y: 0.35em;
    --bs-badge-font-size: 0.75em;
    --bs-badge-font-weight: 700;
    --bs-badge-border-radius: var(--bs-border-radius);
    display: inline-block;
    padding: var(--bs-badge-padding-y) var(--bs-badge-padding-x);
    font-size: var(--bs-badge-font-size);
    font-weight: var(--bs-badge-font-weight);
    line-height: 1;
    color: var(--bs-badge-color);
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: var(--bs-badge-border-radius);" class="card-title-text">
                                        <?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin Categoría') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $cantidad = $p['stock'] / $p['factor_conversion'];

                                    if ($cantidad <= 0) {
                                        $color = 'bg-danger';
                                    } elseif ($cantidad <= 5) {
                                        $color = 'bg-warning text-dark';
                                    } elseif ($cantidad <= 20) {
                                        $color = 'bg-info text-dark';
                                    } else {
                                        $color = 'bg-success';
                                    }
                                    ?>

                                    <span class="badge <?= $color ?>">
                                        <?= $cantidad >= 1
                                            ? number_format($cantidad, 2) . ' ' . $p['unidad_reporte']
                                            : number_format($p['stock'], 2) . ' ' . $p['unidad_medida']
                                            ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['almacen_nombre'] ?? 'N/A') ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-warning btn-sm"
                                        onclick="editarProducto(<?= $p['id'] ?>, <?= $p['almacen_id'] ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =========================================================
         LIBRERÍAS JS (UNA SOLA VEZ, EN ORDEN CORRECTO)
         ========================================================= -->

    <!-- 1. jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 2. Bootstrap JS (necesario para los modales) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 3. Select2 JS -->
    <script src="https://unpkg.com/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- 4. i18n ES (después de Select2) -->
    <script src="https://unpkg.com/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>

    <!-- 5. SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- 6. DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- 7. Scripts propios -->
    <script src="/myvet/app/backend/js/filtros_almacen.js"></script>

    <!-- 8. Modales (UNA SOLA VEZ) -->
    <?php require_once __DIR__ . '/almacenes/ModalAgregarCantidadesIniciales.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalCategoria.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalTraspasos.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalAgregarProducto.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalEditarProducto.php'; ?>
    <?php require_once __DIR__ . '/productos/modalMedidasAdicionales.php' ?>
    <?php require_once __DIR__ . '/productos/modalListaMedidas.php' ?>

    <script>
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const params = new URLSearchParams(window.location.search);

            if (params.get('abrirTraspasos') === '1') {
                await cargarTraspasos();
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        async function exportarProductosCSV() {
            const id = $('#filtroAlmacen').val();

            if (!id) {
                alert("Por favor seleccione un almacén válido para exportar el inventario de productos.");
                return;
            }

            const url = `/myvet/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${id}`;

            try {
                const resp = await fetch(url);
                const textoServidor = await resp.text();

                let res;
                try {
                    res = JSON.parse(textoServidor);
                } catch (errJson) {
                    console.error("❌ EL SERVIDOR NO DEVOLVIÓ JSON VÁLIDO al exportar productos.");
                    alert("Error en el formato de respuesta del servidor.");
                    return;
                }

                if (!res.success || !Array.isArray(res.data) || res.data.length === 0) {
                    alert("No se encontraron productos disponibles para exportar en este almacén.");
                    return;
                }

                const headers = [
                    "ID Producto",
                    "SKU",
                    "Nombre del Producto",
                    "Unidad Medida",
                    "Unidad Reporte",
                    "Stock Actual",
                    "Factor Conversión",
                    "Precio Minorista ($)",
                    "Precio Mayorista ($)",
                    "Precio Distribuidor ($)"
                ];

                const escapeCSV = (str) => {
                    if (str === null || str === undefined) return '""';
                    let val = String(str).replace(/"/g, '""');
                    return `"${val}"`;
                };

                const rows = res.data.map(pr => {
                    const stock = parseFloat(pr.stock || 0);
                    const factor = parseFloat(pr.factor_conversion || 1);
                    const preMin = parseFloat(pr.precio_minorista || 0);
                    const preMat = parseFloat(pr.precio_mayorista || 0);
                    const preDis = parseFloat(pr.precio_distribuidor || 0);

                    return [
                        escapeCSV(pr.producto_id),
                        escapeCSV(pr.sku || 'N/A'),
                        escapeCSV(pr.nombre || 'Sin nombre'),
                        escapeCSV(pr.unidad_medida || ''),
                        escapeCSV(pr.unidad_reporte || ''),
                        stock,
                        factor,
                        preMin.toFixed(2),
                        preMat.toFixed(2),
                        preDis.toFixed(2)
                    ].join(',');
                });

                const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const downloadUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');

                const hoy = new Date().toISOString().split('T')[0];
                link.setAttribute('href', downloadUrl);
                link.setAttribute('download', `Reporte_Productos_Almacen_${id}_${hoy}.csv`);
                document.body.appendChild(link);

                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(downloadUrl);

            } catch (error) {
                console.error("Error al exportar productos a CSV:", error);
                alert("Ocurrió un error al procesar la exportación de productos.");
            }
        }
    </script>

    <script>
        // Diagnóstico de librerías al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            console.log('📦 Verificación de librerías:');
            console.log('  jQuery:', typeof window.jQuery !== 'undefined' ? '✅ ' + window.jQuery.fn.jquery : '❌ NO');
            console.log('  Select2:', typeof window.jQuery !== 'undefined' && !!window.jQuery.fn.select2 ? '✅ SÍ' : '❌ NO');
            console.log('  Bootstrap:', typeof window.bootstrap !== 'undefined' ? '✅ SÍ' : '❌ NO');
        });
    </script>
    <!-- Refuerzo: registrar Select2 en el jQuery final -->
    <script src="https://unpkg.com/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        console.log('✅ Refuerzo Select2:', typeof window.jQuery.fn.select2);
        async function descargarProductos() {
            const URL_ACCESO = '/myvet/app/controllers/accesoController.php';
            const almacen_cargar = $('#filtroAlmacen').val();
            // 1. Realizar la petición a tu controlador de Cf System
            const resp = await fetch(
                `${URL_ACCESO}?action=obtenerProductosAlmacen&id=${encodeURIComponent(almacen_cargar)}`
            );


            const res = await resp.json();
            console.log(res.data);
            exportarProductosAExcel(res.data)

        }
        async function exportarProductosAExcel(productos) {
            // El nombre se conserva por compatibilidad con el onclick existente,
            // pero ahora genera CSV.

            if (!Array.isArray(productos) || productos.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Sin datos', text: 'No hay productos para exportar.' });
                return;
            }

            const almacenCargar = document.getElementById('filtroAlmacen')?.value || '';

            // 1. Cabeceras (mismo orden que tenías en Excel)
            const headers = [
                'ID Producto',
                'SKU',
                'Almacén',
                'Nombre del Producto',
                'U. Reporte',
                'Factor Conv. (1/factor)',
                'P. Minorista',
                'P. Mayorista',
                'P. Distribuidor',
                'Existencias',
                'Costo Lote'
            ];

            // 2. Helper para escapar campos CSV (comillas, comas, saltos de línea)
            const escCSV = (v) => {
                const s = String(v ?? '');
                // Si contiene coma, comilla o salto de línea → envolver en comillas y duplicar comillas internas
                if (/[",\n\r]/.test(s)) {
                    return '"' + s.replace(/"/g, '""') + '"';
                }
                return s;
            };

            // 3. Construir filas
            const lineas = [headers.map(escCSV).join(',')];

            productos.forEach((prod) => {
                const factor = parseFloat(prod.factor_conversion || 0);

                const fila = [
                    prod.producto_id ?? '',
                    prod.sku ?? '',
                    almacenCargar,
                    prod.nombre ?? '',
                    prod.unidad_reporte ?? '',
                    factor, // número crudo; Excel lo formatea
                    parseFloat(prod.precio_minorista || 0),
                    parseFloat(prod.precio_mayorista || 0),
                    parseFloat(prod.precio_distribuidor || 0),
                    0, // Existencias (para llenar)
                    0  // Costo Lote (para llenar)
                ];

                lineas.push(fila.map(escCSV).join(','));
            });

            // 4. Añadir BOM UTF-8 para que Excel respete tildes/ñ
            const csvContent = '\uFEFF' + lineas.join('\r\n');

            // 5. Descargar
            const filename = 'Reporte_Productos_Inventario.csv';
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            setTimeout(() => URL.revokeObjectURL(link.href), 1000);
        }

    </script>
    <script>
        /**
     * Procesa el CSV descargado (editado por el usuario en Existencias y Costo Lote)
     * y envía cada fila al backend usando la MISMA lógica que enviarFormularioProducto:
     *  - producto_id
     *  - sku
     *  - factor_conversion (real, no el inverso del Excel)
     *  - precio_adquisicion (costo unitario = Costo Lote / Existencias)
     *  - almacenes[ID][stock] = Existencias * factor
     *  - almacenes[ID][stock_minimo] = 0
     *  - almacenes[ID][precio_minorista / mayorista / distribuidor]
     *
     * El CSV trae: ID Producto, SKU, Almacén, Nombre, U. Reporte,
     * Factor Conv. (1/factor), P. Minorista, P. Mayorista, P. Distribuidor,
     * Existencias, Costo Lote
     */
        async function procesarCSVExistencias(file) {
            if (!file) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Debes seleccionar un archivo CSV.' });
                return;
            }

            // 1. Leer archivo
            const texto = await leerArchivoComoTexto(file);

            // 2. Parsear CSV
            const filas = parseCSV(texto);
            if (filas.length < 2) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'El CSV está vacío o no tiene datos.' });
                return;
            }

            // 3. Mapear cabeceras
            const headers = filas[0].map(h => String(h).trim());
            const idx = {
                producto_id: headers.findIndex(h => /id\s*producto/i.test(h)),
                sku: headers.findIndex(h => /^sku$/i.test(h)),
                almacen_id: headers.findIndex(h => /almac[eé]n/i.test(h)),
                factor: headers.findIndex(h => /factor/i.test(h)),
                precio_minorista: headers.findIndex(h => /minorista/i.test(h)),
                precio_mayorista: headers.findIndex(h => /mayorista/i.test(h)),
                precio_distribuidor: headers.findIndex(h => /distribuidor/i.test(h)),
                existencias: headers.findIndex(h => /existencias/i.test(h)),
                costo_lote: headers.findIndex(h => /costo/i.test(h))
            };

            const faltantes = Object.entries(idx).filter(([, v]) => v === -1).map(([k]) => k);
            if (faltantes.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'CSV inválido',
                    text: `Faltan columnas: ${faltantes.join(', ')}`
                });
                return;
            }

            // 4. Construir filas válidas
            const filasValidas = [];
            const omitidas = [];   // 👈 filas con existencias = 0
            const almacen_usu = <?= (int) ($_SESSION['almacen_id'] ?? 0) ?>;


            for (let i = 1; i < filas.length; i++) {
                const row = filas[i];
                if (!row || row.length === 0 || row.every(c => !String(c).trim())) continue;

                const productoId = String(row[idx.producto_id] || '').trim();
                const almacenId = almacen_usu == 0
                    ? String(row[idx.almacen_id] || '').trim()
                    : almacen_usu;
                if (!productoId || !almacenId) continue;

                const factorReal = parseNumero(row[idx.factor]) || 1;
                const existencias = parseNumero(row[idx.existencias]);
                const costoLote = parseNumero(row[idx.costo_lote]);
                const precioMin = parseNumero(row[idx.precio_minorista]);
                const precioMay = parseNumero(row[idx.precio_mayorista]);
                const precioDis = parseNumero(row[idx.precio_distribuidor]);

                // Stock total = Existencias × Factor
                const stockTotal = existencias * factorReal;

                // 👇 Si existencias es 0 (o el stockTotal da 0), NO se procesa
                if (existencias === 0 || stockTotal === 0) {
                    omitidas.push({ producto_id: productoId, almacen_id: almacenId, motivo: 'existencias = 0' });
                    console.warn(`[Fila ${i}] OMITIDA prod=${productoId} alm=${almacenId} (existencias = 0)`);
                    continue;
                }

                // 👇 Se manda el COSTO TOTAL del lote; el backend lo divide por el stock
                const precioAdquisicion = costoLote;

                // Diagnóstico (quítalo cuando confirmes)
                console.log(`[Fila ${i}] prod=${productoId} alm=${almacenId} ` +
                    `factor=${factorReal} exist=${existencias} ` +
                    `stock=${stockTotal} costo_lote=${costoLote} ` +
                    `precio_adquisicion=${precioAdquisicion} ` +
                    `pMin=${precioMin} pMay=${precioMay} pDis=${precioDis}`);

                filasValidas.push({
                    producto_id: productoId,
                    sku: String(row[idx.sku] || '').trim(),
                    almacen_id: almacenId,
                    factor_conversion: factorReal,
                    precio_adquisicion: precioAdquisicion,
                    stock: stockTotal,
                    stock_minimo: 0,
                    precio_minorista: precioMin,
                    precio_mayorista: precioMay,
                    precio_distribuidor: precioDis
                });
            }

            // 👇 Si todas las filas fueron omitidas
            if (filasValidas.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nada que procesar',
                    html: `No se encontraron filas con existencias mayores a 0.
                   <br><br>Filas omitidas: <b>${omitidas.length}</b>`
                });
                return;
            }

            // 5. Confirmar (mostrando también las omitidas, si hay)
            const resumenOmitidas = omitidas.length
                ? `<br><br><span class="text-warning">Se omitirán <b>${omitidas.length}</b> fila(s) con existencias = 0.</span>`
                : '';

            const confirm = await Swal.fire({
                icon: 'question',
                title: '¿Procesar CSV?',
                html: `Se procesarán <b>${filasValidas.length}</b> fila(s).${resumenOmitidas}`,
                showCancelButton: true,
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd'
            });
            if (!confirm.isConfirmed) return;

            // 6. Procesar una por una
            let ok = 0, fail = 0;
            const errores = [];

            Swal.fire({
                title: 'Procesando...',
                html: `<b>0</b> / ${filasValidas.length}`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            for (let i = 0; i < filasValidas.length; i++) {
                const f = filasValidas[i];

                const formData = new FormData();
                formData.append('producto_id', f.producto_id);
                formData.append('sku', f.sku);
                formData.append('factor_conversion', f.factor_conversion);
                formData.append('precio_adquisicion', f.precio_adquisicion);

                formData.append(`almacenes[${f.almacen_id}][stock]`, f.stock);
                formData.append(`almacenes[${f.almacen_id}][stock_minimo]`, f.stock_minimo);
                formData.append(`almacenes[${f.almacen_id}][precio_minorista]`, f.precio_minorista);
                formData.append(`almacenes[${f.almacen_id}][precio_mayorista]`, f.precio_mayorista);
                formData.append(`almacenes[${f.almacen_id}][precio_distribuidor]`, f.precio_distribuidor);

                try {
                    const resp = await fetch('/myvet/app/controllers/almacenes.php?action=guardarCantidadesIniciales', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await resp.json();
                    if (data.status === 'success') {
                        ok++;
                    } else {
                        fail++;
                        errores.push(`Prod ${f.producto_id} / Alm ${f.almacen_id}: ${data.message || 'error'}`);
                    }
                } catch (e) {
                    fail++;
                    errores.push(`Prod ${f.producto_id} / Alm ${f.almacen_id}: ${e.message}`);
                }

                Swal.update({ html: `<b>${i + 1}</b> / ${filasValidas.length}` });
            }

            // 7. Resultado
            if (fail === 0) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    html: `Se procesaron correctamente <b>${ok}</b> fila(s).
                   ${omitidas.length ? `<br><br><small class="text-muted">Omitidas por existencias = 0: <b>${omitidas.length}</b></small>` : ''}`,
                    confirmButtonColor: '#0d6efd'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Procesado con errores',
                    html: `OK: <b>${ok}</b><br>Fallidos: <b>${fail}</b>
                   ${omitidas.length ? `<br>Omitidas (exist. = 0): <b>${omitidas.length}</b>` : ''}
                   <br><br>
                   <small style="text-align:left;display:block;max-height:200px;overflow:auto;">
                   ${errores.slice(0, 15).map(e => `• ${e}`).join('<br>')}
                   ${errores.length > 15 ? '<br>...' : ''}
                   </small>`,
                    confirmButtonColor: '#dc3545'
                });
            }
        } /* ============================================================
           UTILIDADES
           ============================================================ */

        function parseNumero(v) {
            if (v === null || v === undefined) return 0;
            let s = String(v).trim();
            if (!s) return 0;

            s = s.replace(/[$€\s]/g, '');

            if (s.includes(',') && s.includes('.')) {
                s = s.replace(/,/g, '');
            } else if (s.includes(',')) {
                const partes = s.split(',');
                if (partes[partes.length - 1].length === 3) {
                    s = s.replace(/,/g, '');
                } else {
                    s = s.replace(',', '.');
                }
            }

            const n = parseFloat(s);
            return isNaN(n) ? 0 : n;
        }

        function leerArchivoComoTexto(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => resolve(e.target.result);
                reader.onerror = reject;
                reader.readAsText(file, 'UTF-8');
            });
        }

        function parseCSV(texto) {
            if (texto.charCodeAt(0) === 0xFEFF) texto = texto.slice(1);

            const primeraLinea = texto.split(/\r?\n/)[0] || '';

            let sep = ',';
            if (primeraLinea.includes('\t')) sep = '\t';
            else if ((primeraLinea.match(/;/g) || []).length > (primeraLinea.match(/,/g) || []).length) sep = ';';

            const filas = [];
            let fila = [], campo = '', enComillas = false;

            for (let i = 0; i < texto.length; i++) {
                const c = texto[i], next = texto[i + 1];

                if (enComillas) {
                    if (c === '"' && next === '"') { campo += '"'; i++; }
                    else if (c === '"') { enComillas = false; }
                    else { campo += c; }
                } else {
                    if (c === '"') enComillas = true;
                    else if (c === sep) { fila.push(campo); campo = ''; }
                    else if (c === '\n') { fila.push(campo); filas.push(fila); fila = []; campo = ''; }
                    else if (c === '\r') { /* ignorar */ }
                    else { campo += c; }
                }
            }
            if (campo !== '' || fila.length) { fila.push(campo); filas.push(fila); }

            return filas;
        }
        function leerArchivoComoTexto(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => resolve(e.target.result);
                reader.onerror = reject;
                reader.readAsText(file, 'UTF-8');
            });
        }

        function parseCSV(texto) {
            const primeraLinea = texto.split(/\r?\n/)[0] || '';
            const sep = (primeraLinea.match(/;/g) || []).length > (primeraLinea.match(/,/g) || []).length ? ';' : ',';

            const filas = [];
            let fila = [], campo = '', enComillas = false;

            for (let i = 0; i < texto.length; i++) {
                const c = texto[i], next = texto[i + 1];
                if (enComillas) {
                    if (c === '"' && next === '"') { campo += '"'; i++; }
                    else if (c === '"') { enComillas = false; }
                    else campo += c;
                } else {
                    if (c === '"') enComillas = true;
                    else if (c === sep) { fila.push(campo); campo = ''; }
                    else if (c === '\n') { fila.push(campo); filas.push(fila); fila = []; campo = ''; }
                    else if (c === '\r') { /* ignorar */ }
                    else campo += c;
                }
            }
            if (campo !== '' || fila.length) { fila.push(campo); filas.push(fila); }
            return filas;
        }
        function abrirModalCargarCSV() {
            Swal.fire({
                title: 'Cargar CSV de Existencias',
                html: `
            <div class="text-start">
                <p class="mb-2 text-muted" style="font-size: .9rem;">
                    Selecciona el archivo CSV descargado y editado (Existencias y Costo Lote).
                </p>
                <input type="file" id="swalCsvFile" accept=".csv" class="form-control">
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Procesar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                didOpen: () => {
                    // Forzar estilos para que el input se vea bien dentro de Swal
                    const input = document.getElementById('swalCsvFile');
                    input.style.width = '100%';
                },
                preConfirm: () => {
                    const input = document.getElementById('swalCsvFile');
                    const file = input?.files?.[0];
                    if (!file) {
                        Swal.showValidationMessage('Debes seleccionar un archivo CSV.');
                        return false;
                    }
                    // Validar extensión
                    if (!/\.csv$/i.test(file.name)) {
                        Swal.showValidationMessage('El archivo debe ser .csv');
                        return false;
                    }
                    return file;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    procesarCSVExistencias(result.value);
                }
            });
        }
    </script>

</body>

</html>