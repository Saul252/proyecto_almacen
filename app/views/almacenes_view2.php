<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenes | Sistema</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <link href="/myvet/css/almacenes.css" rel="stylesheet">
    <?php
    // Llamamos a la función que imprime Bootstrap y layout.css
    if (function_exists('cargarEstilos')) {
        cargarEstilos();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>
    <script>
        // Asegúrate de que el JSON incluya factor_conversion y unidad_reporte
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

            <div class="d-flex align-items-center" style="gap: 8px;">

                <div class="ios-micro-card border-blue">
                    <span class="ios-micro-label">Total de innversion</span>
                    <div class="ios-micro-value">$<?= number_format($inversion, 2, '.', ',') ?></div>

                </div>

                <div class="ios-micro-card border-blue">
                    <span class="ios-micro-label"><?= ($rData['tipo'] == 'admin') ? 'Global' : 'Stock' ?></span>
                    <div class="ios-micro-value"><?= number_format($cant_prod) ?></div>
                    <div class="ios-micro-footer text-truncate" style="max-width: 80px;"
                        title="<?= $rData['nombre'] ?>">
                        <?= $rData['nombre'] ?>
                    </div>
                </div>

                <div class="ios-micro-card border-purple">
                    <span class="ios-micro-label">Catálogo</span>
                    <div class="ios-micro-value"><?= number_format($total_cat) ?></div>
                    <div class="ios-micro-footer">Items</div>
                </div>

                <div class="ios-micro-card border-green">
                    <span class="ios-micro-label">Cobertura</span>
                    <div class="d-flex align-items-baseline">
                        <span class="ios-micro-value"><?= $cobertura ?></span>
                        <span style="font-size: 0.6rem; font-weight: 700; color: #1c1c1e; margin-left: 1px;">%</span>
                    </div>
                    <div class="progress"
                        style="height: 3px; background-color: #f2f2f7; border-radius: 10px; margin-top: 4px; width: 100%;">
                        <div class="progress-bar" style="width: <?= $cobertura ?>%; background-color: #34c759;"></div>
                    </div>

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
            <div class="row mb-3 g-2 align-items-center">
                <div class="col-md-2">
                    <select id="filtroCategoria" class="form-select">
                        <option value="">Categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <select id="filtroAlmacen" class="form-select" <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                        <?php if ($almacen_usuario == 0): ?>
                            <option value="">Todos los Almacenes</option>
                        <?php endif; ?>

                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>" <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($alm['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="buscador" class="form-control" placeholder="🔎 Buscar...">
                </div>

                <div class="col-md-5">
                    <div class="d-flex gap-2">
                        <button class="btn btn-success w-100 flex-fill" data-bs-toggle="modal"
                            data-bs-target="#modalAgregarProducto">
                            <i class="bi bi-plus-lg"></i> Producto
                        </button>
                        <button
                            class="btn btn-sm btn-info text-white px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2 transition-all"
                            data-bs-toggle="modal" onclick="abrirModalInventarioInicial()">
                            <i class="bi bi-sliders fs-6"></i> Iniciales
                        </button>

                        <button class="btn btn-dark w-100 flex-fill" data-bs-toggle="modal"
                            data-bs-target="#modalTraspaso">
                            <i class="bi bi-arrow-left-right"></i> Traspaso
                        </button>

                        <button class="btn btn-primary w-100 flex-fill" data-bs-toggle="modal"
                            data-bs-target="#modalTraspasosGestion" onclick="cargarTraspasos()">
                            <i class="bi bi-shield-check"></i> Autorizar
                        </button>

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
                                <td class="fw-bold"><?= $p['sku'] ?></td>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['descripcion'] ?? 'sin descripcion') ?></td>

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
    border-radius: var(--bs-badge-border-radius);"
                                        class="   card-title-text "><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin Categoría') ?></span>
                                </td>
                                <td>
                                    <?php
                                    $cantidad = $p['stock'] / $p['factor_conversion'];

                                    if ($cantidad <= 0) {
                                        $color = 'bg-danger';       // Sin stock
                                    } elseif ($cantidad <= 5) {
                                        $color = 'bg-warning text-dark'; // Stock bajo
                                    } elseif ($cantidad <= 20) {
                                        $color = 'bg-info text-dark';    // Stock medio
                                    } else {
                                        $color = 'bg-success';      // Stock alto
                                    }
                                    ?>

                                    <span class="badge <?= $color ?>">
                                        <?= $cantidad >= 1
                                            ? number_format($cantidad, 2) . ' ' . $p['unidad_reporte']
                                            : number_format($p['stock'], 2) . ' ' . $p['unidad_medida']
                                            ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($p['almacen_nombre'] ?? 'N/A') ?></td>
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
    <!-- Scripts Externos -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="/myvet/app/backend/js/filtros_almacen.js"></script>

    <!-- --- Lógica de Control de Conversión --- -->
    <?php require_once __DIR__ . '/almacenes/ModalAgregarCantidadesIniciales.php'; ?>

    <?php require_once __DIR__ . '/almacenes/ModalCategoria.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalTraspasos.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalAgregarProducto.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalEditarProducto.php'; ?>
    <?php require_once __DIR__ . '/productos/modalMedidasAdicionales.php' ?>
    <?php require_once __DIR__ . '/productos/modalListaMedidas.php' ?>

    <script>
        // Selecciona todos los inputs de texto y también los textareas
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                // Convierte el valor a mayúsculas en tiempo real
                this.value = this.value.toUpperCase();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {

            const params = new URLSearchParams(window.location.search);

            if (params.get('abrirTraspasos') === '1') {

                await cargarTraspasos();

                // limpiar URL
                window.history.replaceState(
                    {},
                    document.title,
                    window.location.pathname
                );
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

                // Encabezados del archivo CSV
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

                // Función para sanitizar texto en formato CSV
                const escapeCSV = (str) => {
                    if (str === null || str === undefined) return '""';
                    let val = String(str).replace(/"/g, '""');
                    return `"${val}"`;
                };

                // Construir cada fila sanitizada
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

                // Ensamblar contenido CSV agregando el BOM UTF-8 para Excel
                const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');

                // Generar descarga del archivo
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
</body>

</html>