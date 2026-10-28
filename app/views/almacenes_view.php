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
    <style>
        /* El modal de categoría debe estar por encima de todo (Bootstrap usa 1055 por defecto) */
        #modalNuevaCategoria {
            z-index: 1070 !important;
        }

        /* El fondo oscuro (backdrop) del segundo modal también debe subir de nivel */
        #modalNuevaCategoria.modal.show~.modal-backdrop {
            z-index: 1065 !important;
        }

        /* Evitar que el primer modal pierda el scroll si el segundo es muy largo */
        .modal {
            overflow-y: auto !important;
        }

        /* Contenedor mini para la esquina superior */
        .ios-mini-container {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            padding: 10px;
        }

        .ios-micro-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            padding: 8px 12px;
            min-width: 110px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
        }

        .ios-micro-label {
            color: #8e8e93;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            line-height: 1;
            margin-bottom: 4px;
        }

        .ios-micro-value {
            color: #1c1c1e;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .ios-micro-footer {
            font-size: 0.65rem;
            color: #aeaeb2;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
        }

        /* Colores de acento sutiles en el borde izquierdo */
        .border-blue {
            border-left: 3px solid #007aff;
        }

        .border-purple {
            border-left: 3px solid #5856d6;
        }

        .border-green {
            border-left: 3px solid #34c759;
        }
    </style>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h2 class="fw-bold mb-0 card-title-text" style="letter-spacing: -0.02em;">
                <i class="bi bi-box-seam card-title-text"></i> Módulo de Almacén
            </h2>

            <?php
            $rData = $resumenData ?? ['tipo' => 'error', 'nombre' => 'No disponible', 'mis_productos' => 0, 'total_sistema' => 0];
            $cant_prod = $rData['mis_productos'];
            $total_cat = $rData['total_sistema'];
            $cobertura = ($total_cat > 0) ? round(($cant_prod / $total_cat) * 100, 1) : 0;
            ?>

            <div class="d-flex align-items-center" style="gap: 8px;">

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
            </div>
        </div>

        <style>
            /* Estilos ultra-finos para no afectar el main-content */
            .ios-micro-card {

                border-radius: 12px;
                border: 1px solid rgba(0, 0, 0, 0.05);
                padding: 5px 10px;
                min-width: 90px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .ios-micro-label {
                color: #8e8e93;
                font-size: 0.55rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                line-height: 1.2;
            }

            .ios-micro-value {
                color: #1c1c1e;
                font-size: 1rem;
                font-weight: 700;
                letter-spacing: -0.02em;
                line-height: 1;
                margin-top: 2px;
            }

            .ios-micro-footer {
                font-size: 0.6rem;
                color: #aeaeb2;
                margin-top: 2px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .border-blue {
                border-left: 3px solid #007aff;
            }

            .border-purple {
                border-left: 3px solid #5856d6;
            }

            .border-green {
                border-left: 3px solid #34c759;
            }
        </style>

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
                                <td><span
                                        class="border border-subtle  "><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin Categoría') ?></span>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = ($p['stock'] > 20) ? 'bg-success' : (($p['stock'] > 5) ? 'bg-warning text-dark' : 'bg-danger');
                                    ?>
                                    <span class="badge <?= $badgeClass ?> badge-stock"><?= $p['stock'] ?></span>
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

    <div class="modal fade" id="modalTraspaso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-arrow-left-right"></i> Nuevo Traspaso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTraspaso" action="/myvet/app/backend/almacen/procesar_traspaso.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Almacén de Origen</label>
                            <select name="almacen_origen_id" id="origen_id" class="form-select border-primary" required
                                onchange="filtrarProductosPorOrigen()">
                                <option value="">Seleccione donde sale la mercancía...</option>
                                <?php foreach ($almacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Producto a mover</label>
                            <select name="producto_id" id="traspaso_producto" class="form-select" required disabled
                                onchange="actualizarMaximo()">
                                <option value="">Primero seleccione un origen...</option>
                            </select>
                            <div id="info_stock" class="form-text text-primary fw-bold"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">3. Almacén Destino</label>
                                <select name="almacen_destino_id" id="destino_id" class="form-select" required>
                                    <option value="">¿A dónde va la mercancía?</option>
                                    <?php foreach ($todosLosAlmacenes as $alm_dest): ?>
                                        <?php if ($almacen_usuario > 0 && $alm_dest['id'] == $almacen_usuario)
                                            continue; ?>
                                        <option value="<?= $alm_dest['id'] ?>"><?= htmlspecialchars($alm_dest['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">4. Cantidad a Traspasar</label>
                                <div class="input-group">
                                    <input type="number" id="traspaso_factor_input" class="form-control text-center"
                                        placeholder="0" min="0">
                                    <span class="input-group-text" id="label_unidad_reporte"
                                        style="min-width: 80px;">Unid.</span>

                                    <input type="number" id="traspaso_piezas_input" class="form-control text-center"
                                        placeholder="0" min="0" step="any">
                                    <span class="input-group-text">Pzas.</span>
                                </div>

                                <input type="hidden" name="cantidad" id="cantidad_traspaso_final" required>

                                <div id="resumen_conversion"
                                    class="mt-2 p-2 rounded border border-subtle border-start border-4 border-primary"
                                    style="display:none; font-size: 0.9rem;">
                                    <strong>Movimiento total:</strong> <span id="txt_total_pzas">0</span> piezas.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control text-uppercase" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarTraspaso">Solicitar
                            Movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalTraspasosGestion" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Gestión de Traspasos entre Almacenes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($_SESSION['rol_id'] == 1): ?>
                        <div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ver movimientos del Almacén:</label>
                                <select id="admin_filtro_almacen" class="form-select" onchange="cargarTraspasos()">
                                    <option value="">Seleccione un almacén para autorizar...</option>
                                    <?php foreach ($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-arribos">
                                📥 Arribos (Por Recibir)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-envios">
                                📤 Envíos (En Tránsito)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-arribos">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Producto</th>
                                            <th>Cant.</th>
                                            <th>Origen</th>
                                            <th>Enviado por</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contenedor-arribos">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-envios">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Producto</th>
                                            <th>Cant.</th>
                                            <th>Destino</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contenedor-envios">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarProducto" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow-lg ">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i> Nuevo Producto y Entrada de Almacén</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formAgregarProducto" action="/myvet/app/backend/almacen/guardar_producto.php" method="POST">
                    <div class="modal-body p-4">

                        <h6 class="fw-bold mb-3 text-success border-bottom pb-2">Información General</h6>
                        <div class="row mb-3 g-3">
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">SKU</label>
                                <input type="text" name="sku" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Nombre del Producto</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <select name="categoria_id" id="edit_categoria" class="form-select">
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-outline-success" type="button"
                                        onclick="abrirSubModalCategoria()">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Unidad de Medida (Venta/Base)</label>
                                <input type="text" name="unidad_medida" class="form-control"
                                    placeholder="Ej: Bulto, PZA" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Descripción Corta</label>
                                <input type="text" name="description" class="form-control"
                                    placeholder="Detalles adicionales del producto...">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">Precio de Compra de el lote</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger text-white border-danger">$</span>
                                    <input type="number" name="precio_adquisicion"
                                        class="form-control border-danger fw-bold" step="0.01" placeholder="0.00"
                                        required>
                                </div>
                                <small class="text-body-secondary" style="font-size: 0.7rem;">Este valor define el costo
                                    real del lote para tus ganancias.</small>
                            </div>
                        </div>



                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Información Fiscal (SAT)</h6>
                        <div class="row mb-4 g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Clave SAT (Producto/Servicio)</label>
                                <input type="text" name="fiscal_clave_prod" class="form-control"
                                    placeholder="Ej: 43231500">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Clave Unidad SAT</label>
                                <input type="text" name="fiscal_clave_unit" class="form-control" placeholder="Ej: H87">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">IVA %</label>
                                <select name="impuesto_iva" class="form-select">
                                    <option value="16.00">16%</option>
                                    <option value="8.00">8%</option>
                                    <option value="0.00">0%</option>
                                    <option value="exento">Exento</option>
                                </select>
                            </div>
                        </div>

                        <div class="card border border-subtle border-warning mb-4 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calculator-fill text-warning"></i>
                                    Control de Entrada y Conversión</h6>

                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="small fw-bold">Unidad de Compra (Reporte):</label>
                                        <input type="text" name="unidad_reporte" class="form-control border-warning"
                                            placeholder="Ej: Tonelada, Millar">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="small fw-bold text-primary">Factor (Equivalencia):</label>
                                        <input type="number" id="inputFactor" name="factor_conversion"
                                            class="form-control border-primary fw-bold" value="1" step="0.01"
                                            oninput="actualizarLimiteMaestro()">
                                        <small class="text-body-secondary" style="font-size: 0.6rem;">Ej: 40 bultos por
                                            Ton.</small>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="small fw-bold text-danger">Cantidad Recibida:</label>
                                        <input type="number" id="inputLlegadaMaestra"
                                            class="form-control border-danger fw-bold" step="0.01" placeholder="0.00"
                                            oninput="actualizarLimiteMaestro()">
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="p-2 border rounded border border-subtle shadow-sm border-dark">
                                            <span class="small text-body-secondary d-block text-uppercase fw-bold"
                                                style="font-size: 0.6rem;">Total Unidades Base a Repartir</span>
                                            <span id="displayLimiteBultos" class="fw-bold fs-4 text-dark">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-houses-fill"></i> Distribución por
                                Almacén</h6>
                            <div class="badge bg-secondary p-2 shadow-sm" style="font-size: 0.9rem;">
                                Asignado: <span id="displayAsignado" class="fw-bold">0.00</span> |
                                Restante: <span id="displayRestante" class="fw-bold">0.00</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-dark small text-center">
                                    <tr>
                                        <th width="40">Act.</th>
                                        <th>Almacén</th>
                                        <th width="130">Stock Inicial</th>
                                        <th width="100">Stock Mín.</th>
                                        <th width="110">P. Minorista</th>
                                        <th width="110">P. Mayorista</th>
                                        <th width="110">P. Distribuidor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($almacenes as $a): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="almacenes[<?= $a['id'] ?>][activo]" value="1"
                                                    class="form-check-input" checked>
                                            </td>
                                            <td class="small fw-bold"><?= htmlspecialchars($a['nombre']) ?></td>
                                            <td>
                                                <input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][stock]"
                                                    class="form-control form-control-sm input-calculo border-primary fw-bold text-center"
                                                    oninput="validarReparto()" value="0">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    name="almacenes[<?= $a['id'] ?>][stock_minimo]"
                                                    class="form-control form-control-sm text-center" placeholder="0">
                                            </td>
                                            <td><input type="number" step="0.01"
                                                    name="almacenes[<?= $a['id'] ?>][precio_minorista]"
                                                    class="form-control form-control-sm" placeholder="$"></td>
                                            <td><input type="number" step="0.01"
                                                    name="almacenes[<?= $a['id'] ?>][precio_mayorista]"
                                                    class="form-control form-control-sm" placeholder="$"></td>
                                            <td><input type="number" step="0.01"
                                                    name="almacenes[<?= $a['id'] ?>][precio_distribuidor]"
                                                    class="form-control form-control-sm" placeholder="$"></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="modal-footer border border-subtle">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnGuardarProducto" class="btn btn-success px-5 fw-bold shadow">
                            <i class="bi bi-save me-2"></i> GUARDAR PRODUCTO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 20px; overflow: hidden;">

                <div class="modal-header  bg-warning bg-gradient p-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="bi bi-pencil-square fs-4 me-2"></i>
                        <span>Editar Producto:</span>
                        <span id="edit_nombre_titulo" class="ms-2 opacity-75 fw-normal"></span>
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="formEditarProducto">
                    <input type="hidden" name="producto_id" id="edit_id">
                    <input type="hidden" name="almacen_actual_id" id="edit_almacen_id">

                    <div class="modal-body p-4 border border-subtle">
                        <div class="row g-4">

                            <div class="col-md-4">
                                <div class="p-3 rounded-4 border border-subtle border border-opacity-10 h-100">
                                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i> Datos Generales
                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">SKU / Código</label>
                                        <input type="text" name="sku" id="edit_sku" class="form-control  shadow-sm"
                                            style="border-radius: 10px;" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Nombre del
                                            Producto</label>
                                        <input type="text" name="nombre" id="edit_nombre"
                                            class="form-control  shadow-sm" style="border-radius: 10px;" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Categoría</label>
                                        <div class="input-group shadow-sm"
                                            style="border-radius: 10px; overflow: hidden;">
                                            <select name="categoria_id" id="edit_categoria_idx" class="form-select ">
                                                <option value="">Seleccione...</option>
                                                <?php foreach ($categorias as $cat): ?>
                                                    <option value="<?= trim($cat['id']) ?>">
                                                        <?= htmlspecialchars($cat['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-white  text-success" type="button"
                                                onclick="abrirSubModalCategoria()">
                                                <i class="bi bi-plus-circle-fill"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Descripción</label>
                                        <textarea name="descripcion" id="edit_descripcion"
                                            class="form-control  shadow-sm" style="border-radius: 10px;"
                                            rows="3"></textarea>
                                    </div>

                                    <h6 class="fw-bold text-info mt-4 mb-3 d-flex align-items-center">
                                        <i class="bi bi-shield-check me-2"></i> Datos SAT
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="small text-body-secondary">Clave Prod.</label>
                                            <input type="text" name="fiscal_clave_prod" id="edit_fiscal_clave_prod"
                                                class="form-control form-control-sm  shadow-sm">
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-body-secondary">Clave Unidad</label>
                                            <input type="text" name="fiscal_clave_unidad" id="edit_fiscal_clave_unidad"
                                                class="form-control form-control-sm  shadow-sm">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="small text-body-secondary">IVA (%)</label>
                                            <input type="number" step="0.01" name="impuesto_iva" id="edit_impuesto_iva"
                                                class="form-control form-control-sm  shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">

                                <div
                                    class="d-flex justify-content-between align-items-center p-3 mb-4 rounded-4 bg-dark text-white shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                                        <span class="small opacity-75 me-2">Editando en:</span>
                                        <span id="edit_almacen_nombre" class="fw-bold"></span>
                                    </div>
                                    <?php if ($almacen_usuario == 0): ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="check_todos_almacenes"
                                                name="aplicar_global">
                                            <label class="form-check-label small fw-bold"
                                                for="check_todos_almacenes">¿Aplicar a todos los almacenes?</label>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <h6 class="fw-bold text-success mb-3">Gestión de Precios</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="card  shadow-sm p-3 rounded-4">
                                            <label class="small fw-bold text-body-secondary mb-2">P. Minorista</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-transparent  text-success fw-bold">$</span>
                                                <input type="number" step="0.01" name="precio_minorista" id="edit_p_min"
                                                    class="form-control  fw-bold fs-5 p-0 shadow-none">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card  shadow-sm p-3 rounded-4">
                                            <label class="small fw-bold text-body-secondary mb-2">P. Mayorista</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-transparent  text-success fw-bold">$</span>
                                                <input type="number" step="0.01" name="precio_mayorista" id="edit_p_may"
                                                    class="form-control  fw-bold fs-5 p-0 shadow-none">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card  shadow-sm p-3 rounded-4">
                                            <label class="small fw-bold text-body-secondary mb-2">P.
                                                Distribuidor</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-transparent  text-success fw-bold">$</span>
                                                <input type="number" step="0.01" name="precio_distribuidor"
                                                    id="edit_p_dist" class="form-control  fw-bold fs-5 p-0 shadow-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark mb-3">Configuración de Unidades</h6>
                                <div class="row g-3 p-3 rounded-4 border border-subtle border mb-4">
                                    <div class="col-md-4">
                                        <label class="small fw-bold text-secondary">Unidad Compra</label>
                                        <input type="text" name="unidad_reporte" id="edit_unidad_reporte"
                                            class="form-control  shadow-sm text-center fw-bold" placeholder="CAJA">
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <label class="small fw-bold text-primary">Unidades que componen Unidad de
                                            Compra</label>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="bi bi-arrow-repeat fs-4 text-primary me-2"></i>
                                            <input type="number" step="0.01" name="factor_conversion"
                                                id="edit_factor_conversion"
                                                class="form-control  shadow-sm text-center fw-bold fs-5 text-primary"
                                                style="width: 100px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold text-secondary">Unidad Venta (Base)</label>
                                        <input type="text" name="unidad_medida" id="edit_unidad_medida"
                                            class="form-control  shadow-sm text-center fw-bold" placeholder="PIEZA">
                                    </div>
                                </div>

                                <h6 class="fw-bold text-secondary mb-3">Control de Inventario</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div
                                            class="p-3 rounded-4 border-start border-4 border-success border border-subtle shadow-sm">
                                            <label class="small fw-bold text-body-secondary">Stock Actual</label>
                                            <div class="d-flex align-items-center mt-1">
                                                <i class="bi bi-box-fill text-success me-2"></i>
                                                <input type="number" step="0.01" name="stock" id="edit_stock"
                                                    class="form-control  fw-bold fs-4 p-0 shadow-none" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div
                                            class="p-3 rounded-4 border-start border-4 border-danger border border-subtle shadow-sm">
                                            <label class="small fw-bold text-body-secondary">Mínimo Permitido</label>
                                            <div class="d-flex align-items-center mt-1">
                                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                                <input type="number" step="0.01" name="stock_minimo" id="edit_s_min"
                                                    class="form-control  fw-bold fs-4 p-0 shadow-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer  p-4 border border-subtle d-flex justify-content-between">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none fw-bold"
                            data-bs-dismiss="modal">Descartar</button>
                        <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm"
                            style="border-radius: 12px;">
                            Actualizar Producto <i class="bi bi-check-lg ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h6 class="modal-title">Nueva Categoría</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formRapidoCategoria">
                        <div class="mb-3">
                            <label class="form-label small">Nombre de la Categoría</label>
                            <input type="text" id="nombre_cat_rapida" class="form-control"
                                placeholder="Ej: Herramientas" required>
                        </div>
                        <button type="button" onclick="guardarCategoriaRapida()" class="btn btn-success w-100">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
        function abrirSubModalCategoria() {
            // Simplemente abrimos el modal de categoría sin cerrar el anterior
            const myModal = new bootstrap.Modal(document.getElementById('modalNuevaCategoria'), {
                backdrop: 'static', // Evita que se cierre el de atrás si haces clic fuera
                keyboard: false
            });
            myModal.show();
        }

        function guardarCategoriaRapida() {

            const input = document.getElementById('nombre_cat_rapida');
            const nombre = input.value.trim();

            if (!nombre) {
                return Swal.fire('Error', 'Escribe un nombre', 'error');
            }

            // 🔥 USAR FORMDATA (compatible con PHP $_POST)
            const formData = new FormData();
            formData.append('nombre', nombre);

            fetch('/myvet/app/controllers/almacenes.php?action=guardarCategoria', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {

                    console.log(data);

                    if (data.status === 'success') {

                        const id = data.id; // 🔥 CORRECTO

                        // 🔥 ACTUALIZAR TODOS LOS SELECTS
                        document.querySelectorAll('select[name="categoria_id"]').forEach(select => {

                            // evitar duplicados
                            const existe = Array.from(select.options)
                                .some(opt => opt.value == id);

                            if (!existe) {
                                const nuevaOpcion = new Option(data.nombre, id);
                                select.add(nuevaOpcion);
                            }

                            // seleccionar nueva categoría
                            select.value = String(id);
                        });

                        // 🔥 CERRAR MODAL
                        const modal = bootstrap.Modal.getOrCreateInstance(
                            document.getElementById('modalNuevaCategoria')
                        );
                        modal.hide();

                        // 🔥 limpiar input
                        input.value = '';

                        // 🔥 asegurar scroll del modal padre
                        setTimeout(() => {
                            if (document.querySelectorAll('.modal.show').length > 0) {
                                document.body.classList.add('modal-open');
                            }
                        }, 300);

                        // 🔥 mensaje
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Categoría guardada y seleccionada.',
                            icon: 'success',
                            timer: 1200,
                            showConfirmButton: false
                        });

                    } else {
                        Swal.fire('Error', data.message || 'Error desconocido', 'error');
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire('Error', 'No se pudo procesar la categoría', 'error');
                });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/myvet/app/backend/js/filtros_almacen.js"></script>
    <script src="/myvet/app/backend/js/guardar_producto.js"></script>

    <script src="/myvet/app/backend/js/informacion_productos_envio.js"></script>
    <script src="/myvet/app/backend/js/cargar_traspasos.js"></script>
    <script src="/myvet/app/backend/js/aceptar_arribo.js"></script>
    <!-- --- Lógica de Control de Conversión --- -->
    <script src="/myvet/app/backend/js/calculo_de_conversion.js"></script>
    <script src="/myvet/app/backend/js/editar_producto.js"></script>

    <script src="/myvet/app/backend/js/actualizar_producto.js"></script>
    <script>
        // --- MÓDULO DE COMPRA DISTRIBUIDA ---

        function abrirModalCompra() {
            $('#formNuevaCompra')[0].reset();
            $('#cuerpoTablaCompra').empty();
            agregarFilaCompraPrincipal();
            $('#modalAgregarCompra').modal('show');
        }

        function agregarFilaCompraPrincipal(prod = null) {
            const idFila = Date.now();
            let filaHtml = `
    <tr class="table-light">
        <td colspan="8" class="p-0">
            <div class="card m-2 border-primary">
                <div class="card-body">
                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-md-5">
                            <label class="small fw-bold">Producto</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Buscar..." oninput="buscarEnCompra(this)" value="${prod ? prod.nombre : ''}">
                            <input type="hidden" name="items[${idFila}][producto_id]" class="p-id" value="${prod ? prod.id : ''}">
                            <div class="res-ajax shadow" style="display:none; position:absolute; z-index:1000; background:white; width:300px;"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="small fw-bold">Unidad Entrada</label>
                            <select name="items[${idFila}][usa_conversion]" class="form-select form-select-sm select-unidad" onchange="calcularDistribucion('${idFila}')">
                                <option value="0">Piezas (Base)</option>
                                <option value="1">Unidad Reporte</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small fw-bold">Cant. Facturada</label>
                            <input type="number" name="items[${idFila}][cantidad_factura]" class="form-control form-control-sm cant-factura" value="0" oninput="calcularDistribucion('${idFila}')">
                            <input type="hidden" class="val-factor" value="${prod ? prod.factor_conversion : '1'}">
                        </div>
                        <div class="col-md-2">
                            <label class="small fw-bold text-primary">Total Piezas</label>
                            <input type="text" class="form-control form-control-sm fw-bold total-piezas-h" readonly value="0">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm mt-3" onclick="$(this).closest('tr').remove()"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered border border-subtle small">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Act.</th>
                                    <th>Almacén</th>
                                    <th>Ingresar Stock</th>
                                    <th>P. Minorista</th>
                                    <th>P. Mayorista</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($almacenes as $a): ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" name="items[${idFila}][almacenes][<?= $a['id'] ?>][activo]" value="1" checked></td>
                                    <td><?= $a['nombre'] ?></td>
                                    <td><input type="number" name="items[${idFila}][almacenes][<?= $a['id'] ?>][stock]" class="form-control form-control-sm input-stock-dist" value="0"></td>
                                    <td><input type="number" name="items[${idFila}][almacenes][<?= $a['id'] ?>][p_min]" class="form-control form-control-sm" placeholder="$"></td>
                                    <td><input type="number" name="items[${idFila}][almacenes][<?= $a['id'] ?>][p_may]" class="form-control form-control-sm" placeholder="$"></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </td>
    </tr>`;
            $('#cuerpoTablaCompra').append(filaHtml);
        }

        function calcularDistribucion(id) {
            let card = $(`input[name="items[${id}][producto_id]"]`).closest('.card');
            let cant = parseFloat(card.find('.cant-factura').val()) || 0;
            let usaConversion = card.find('.select-unidad').val();
            let factor = parseFloat(card.find('.val-factor').val()) || 1;

            let totalPiezas = (usaConversion == "1") ? (cant * factor) : cant;
            card.find('.total-piezas-h').val(totalPiezas.toFixed(2));
        }
        async function descargarProductos() {

            // 1. Realizar la petición a tu controlador de Cf System
            const resp = await fetch(
                `/myvet/app/controllers/cotizacionesController.php?action=obtenerProductos`
            );


            const res = await resp.json();
            console.log(res.data);
            exportarProductosAExcel(res.data)

        }
        async function exportarProductosAExcel(productos) {
            // 1. Crear un nuevo libro de trabajo
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Inventario Productos');
            const almacen_cargar = $('#filtroAlmacen').val();

            // 2. Definir las columnas de la tabla y sus anchos
            worksheet.columns = [
                { header: 'ID Producto', key: 'producto_id', width: 12 },
                { header: 'SKU', key: 'sku', width: 15 },
                { header: 'Almacén', key: 'almacen_id', width: 12 },
                { header: 'Nombre del Producto', key: 'nombre', width: 30 },
                { header: 'U. Reporte', key: 'unidad_reporte', width: 12 },
                { header: 'Factor Conv. (1/factor)', key: 'factor_conversion', width: 18 },
                { header: 'P. Minorista', key: 'precio_minorista', width: 15 },
                { header: 'P. Mayorista', key: 'precio_mayorista', width: 15 },
                { header: 'P. Distribuidor', key: 'precio_distribuidor', width: 15 },
                { header: 'Existencias', key: 'existencias', width: 12 },
                { header: 'Costo Lote', key: 'costo_lote', width: 15 }
            ];

            // Estilo para la cabecera (Fondo azul desaturado profesional, texto blanco en negrita)
            const headerRow = worksheet.getRow(1);
            headerRow.font = { name: 'Segoe UI', size: 11, bold: true, color: { argb: 'FFFFFF' } };
            headerRow.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: '2E5B82' }
            };
            headerRow.alignment = { vertical: 'middle', horizontal: 'center' };
            headerRow.height = 25;

            // 3. Iterar con un forEach sobre los datos para ir agregando las filas
            productos.forEach((prod, index) => {

                // --- Factor de conversión e inversa ---
                const factor = parseFloat(prod.factor_conversion || 0);
                const factorInverso = factor === 0 ? 0 : 1 / factor;

                // Añadimos la fila al worksheet mapeando los tipos numéricos correctos
                const row = worksheet.addRow({
                    producto_id: prod.producto_id,
                    sku: prod.sku,
                    almacen_id: almacen_cargar || '',
                    nombre: prod.nombre,
                    unidad_reporte: prod.unidad_reporte || '',
                    factor_conversion: factorInverso,
                    precio_minorista: parseFloat(prod.precio_minorista || 0),
                    precio_mayorista: parseFloat(prod.precio_mayorista || 0),
                    precio_distribuidor: parseFloat(prod.precio_distribuidor || 0),
                    existencias: 0,
                    costo_lote: 0
                });

                // Formatear celdas de números y precios para que Excel los reconozca nativamente
                row.getCell('producto_id').alignment = { horizontal: 'center' };
                row.getCell('sku').alignment = { horizontal: 'center' };
                row.getCell('almacen_id').alignment = { horizontal: 'center' };
                row.getCell('unidad_reporte').alignment = { horizontal: 'center' };
                row.getCell('existencias').alignment = { horizontal: 'center' };

                // Formatos numéricos monetarios y decimales
                row.getCell('factor_conversion').numFmt = '#,##0.0000';
                row.getCell('precio_minorista').numFmt = '$#,##0.00';
                row.getCell('precio_mayorista').numFmt = '$#,##0.00';
                row.getCell('precio_distribuidor').numFmt = '$#,##0.00';
                row.getCell('existencias').numFmt = '#,##0.00';
                row.getCell('costo_lote').numFmt = '$#,##0.00';

                // Aplicar bordes delgados gris claro a las filas
                row.eachCell((cell) => {
                    cell.font = { name: 'Segoe UI', size: 10 };
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'E0E0E0' } },
                        bottom: { style: 'thin', color: { argb: 'E0E0E0' } },
                        left: { style: 'thin', color: { argb: 'E0E0E0' } },
                        right: { style: 'thin', color: { argb: 'E0E0E0' } }
                    };

                    // Zebra striping alternado (efecto visual gris claro en filas impares)
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

            const filename = 'Reporte_Productos_Inventario.xlsx';

            // --- Procesamiento y descarga en el Navegador ---

            // Generar el buffer del archivo Excel
            const buffer = await workbook.xlsx.writeBuffer();

            // Crear un objeto Blob con el tipo MIME correcto para archivos XLSX
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

            // Crear un enlace temporal en el DOM
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;

            // Añadir al documento, hacer click de forma virtual y removerlo
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Liberar memoria del objeto URL creado
            URL.revokeObjectURL(link.href);
        }    </script>
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

            for (let i = 1; i < filas.length; i++) {
                const row = filas[i];
                if (!row || row.length === 0 || row.every(c => !String(c).trim())) continue;

                const productoId = String(row[idx.producto_id] || '').trim();
                const almacenId = String(row[idx.almacen_id] || '').trim();
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
                    const resp = await fetch('/cfsistem/app/controllers/almacenes.php?action=guardarCantidadesIniciales', {
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