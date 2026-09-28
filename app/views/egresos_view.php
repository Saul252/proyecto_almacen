<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egresos | Sistema Almacén</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
    :root {
        --nav-height: 65px;
        --sidebar-width: 0px;
        --primary-radius: 12px;
    }

    /* --- ESTRUCTURA BASE --- */
    .main-content {
        
      
        padding: 1.5rem ;
        
        min-height: calc(100vh - var(--nav-height));
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: block;
    }

    /* --- COMPONENTES --- */
    .card-kpi {
        
        border-radius: var(--primary-radius);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .table-responsive {
        border-radius: var(--primary-radius);
       
        border: 1px solid #e2e8f0;
        /* Evita que la tabla rompa el layout en móvil */
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* --- RESPONSIVE (MÓVIL Y TABLET) --- */
    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem 0.75rem;
            /* Menos padding en los lados para ganar espacio */
        }

        /* Ajuste de títulos para que no se corten */
        h2 {
            font-size: 1.5rem;
        }

        /* Botones de acción en móvil: se apilan si es necesario */
        .d-md-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }

        /* Mejora táctil para inputs y selects */
        .form-control,
        .form-select,
        .btn {
            min-height: 44px;
            /* Tamaño recomendado para dedos */
        }
    }

    /* --- GESTIÓN DE MODALES (Z-INDEX) --- */
    /* Nivel 1: Principales */
    #modalGasto,
    #modalNuevaCompra,
    #compraDetalle_seccionImpresion,
    #compraDetalle_modalPrincipal,
    #gastoDetalle_seccionImpresion,
    #gastoDetalle_modalPrincipal,
    #modalAjusteFaltante {
        z-index: 1060 !important;
    }

    /* Nivel 2: Secundarios (Productos, Proveedores) */
    #modalAgregarProducto,
    #modalNuevoProveedorRapido,#modalConfirmarExcedente {
        z-index: 1110 !important;
    }

    /* Nivel 3: Terciarios (Categorías) */
    #modalAgregarCategoria,#modalNuevaCategoriaGasto {
        z-index: 1160 !important;
    }

    /* Backdrops forzados para modales anidados */
    .modal-backdrop:nth-of-type(1) {
        z-index: 1055 !important;
    }

    .modal-backdrop:nth-of-type(2) {
        z-index: 1105 !important;
    }

    .modal-backdrop:nth-of-type(3) {
        z-index: 1155 !important;
    }

    /* Select2: Debe estar por encima de todos los modales anteriores */
    .select2-container--open {
        z-index: 9999 !important;
    }

    /* Ajuste específico para Select2 en Móvil */
    .select2-container .select2-selection--single {
        height: 38px !important;
        display: flex;
        align-items: center;
    }
    </style>
</head>

<body class="">
<style>
    /* Estructura contenedor cristal */
    .glass-container {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 16px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    /* Botón base tipo cristal */
    .glass-btn {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        color: #334155;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .glass-btn:hover {
        background: rgba(255, 255, 255, 0.85);
        border-color: rgba(255, 255, 255, 1) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        color: #0f172a;
    }

    /* Variantes de color para acciones primarias (Cristal Tintado) */
    .glass-btn-warning {
        background: rgba(255, 193, 7, 0.18);
        border: 1px solid rgba(255, 193, 7, 0.35) !important;
        color: #856404;
    }

    .glass-btn-warning:hover {
        background: rgba(255, 193, 7, 0.3);
        border-color: rgba(255, 193, 7, 0.5) !important;
        color: #533f03;
    }

    .glass-btn-primary {
        background: rgba(13, 110, 253, 0.15);
        border: 1px solid rgba(13, 110, 253, 0.3) !important;
        color: #0d6efd;
    }

    .glass-btn-primary:hover {
        background: rgba(13, 110, 253, 0.25);
        border-color: rgba(13, 110, 253, 0.5) !important;
        color: #0a58ca;
    }
</style>

    <?php renderizarLayout($tituloPagina); ?>

    <main class="main-content">
        <div class="container-fluid">

            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h2 class="fw-bold card-title-text mb-1" style="letter-spacing: -0.5px;">Compras y Gastos</h2>
                    <p class="text-body-secondary mb-0 small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                        <i class="bi bi-layers-half"></i> Gestión de flujo de caja e inventario
                    </p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <div class="col-md-5 d-flex justify-content-end">
                             <div class="d-flex align-items-center gap-2">
        <button type="button" 
                class="btn btn-sm glass-btn px-3 py-2 d-inline-flex align-items-center gap-2" 
                onclick="imprimirTablaPDF('egresosTabla')" 
                title="Imprimir PDF">
            <i class="bi bi-printer-fill text-danger fs-6"></i>
            <span>PDF</span>
        </button>

        <button type="button" 
                class="btn btn-sm glass-btn px-3 py-2 d-inline-flex align-items-center gap-2" 
                onclick="exportarTablaCSV('egresosTabla','')" 
                title="Exportar a CSV">
            <i class="bi bi-filetype-csv text-success fs-6"></i>
            <span>CSV</span>
        </button>
    </div>

    <div class="dropdown">
        <button 
            class="btn btn-add dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="border-radius: 10px; background: #127717; color: #ffffff;">
            
            <i class="bi bi-gear me-2"></i> Solicitudes de compra
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow  rounded-3">

            <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                   onclick="nuevaSolicitud()">
                    <i class="bi bi-plus-lg text-success"></i>
                    Crear Solicitud
                </a>
            </li>

            <li>
                <a class="dropdown-item d-flex align-items-center gap-2"
                   href="/myvet/app/controllers/solicitudesCompraController.php">
                    <i class="bi bi-list-ul text-primary"></i>
                   Gestionar Solicitudes
                </a>
            </li>

        </ul>
    </div>

</div>
                <!-- CSS incorporado para el estilo Glassmorphism (Efecto Cristal) -->

<!-- Barra de herramientas con efecto Cristal -->
<div class=" gap-3 p-2.5 glass-container">
    
    <!-- Grupo Exportación/Impresión (Cristal Neutro) -->
   

    <!-- Grupo de Nuevos Registros (Cristal Tintado) -->
    <div class="d-flex align-items-center gap-2">
        <button type="button" 
                class="btn btn-sm glass-btn glass-btn-warning px-3 py-2 d-inline-flex align-items-center gap-2" 
                onclick="abrirModalGasto()">
            <i class="bi bi-cash-stack fs-6"></i>
            <span>Nuevo Gasto</span>
        </button>

        <button type="button" 
                class="btn btn-sm glass-btn glass-btn-primary px-3 py-2 d-inline-flex align-items-center gap-2" 
                onclick="abrirModalCompra()">
            <i class="bi bi-cart-plus-fill fs-6"></i>
            <span>Nueva Compra</span>
        </button>
    </div>

</div>
                       
                    </div>
                   
                </div>
            </div>

            <div class="card mb-4 shadow-sm " style="border-radius: 15px;">
                <div class="card-body p-4">
                    <?php 
            $periodo_sel = $_GET['periodo_filtro'] ?? 'mes'; 
            $tipo_sel    = $_GET['tipo_filtro'] ?? 'todos';
            // Asegúrate de usar la variable correcta que viene del controller
            $categoria_gasto_id = $_GET['categoria_gasto_filtro'] ?? 0;
        ?>
                    <form id="formFiltros" method="GET" action="">
                        <div class="row g-3 align-items-end">

                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">
                                    <i class="bi bi-calendar3 me-1"></i> Periodo
                                </label>
                                <select id="filtro_rapido" name="periodo_filtro"
                                    class="form-select  border border-subtle fw-bold" style="border-radius: 10px;">
                                    <option value="hoy" <?= ($periodo_sel == 'hoy') ? 'selected' : '' ?>>Hoy</option>
                                    <option value="ayer" <?= ($periodo_sel == 'ayer') ? 'selected' : '' ?>>Ayer</option>
                                    <option value="semana" <?= ($periodo_sel == 'semana') ? 'selected' : '' ?>>Esta
                                        Semana</option>
                                    <option value="mes" <?= ($periodo_sel == 'mes') ? 'selected' : '' ?>>Este Mes
                                    </option>
                                    <option value="personalizado"
                                        <?= ($periodo_sel == 'personalizado') ? 'selected' : '' ?>>📅 Personalizado
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2 div-fechas <?= ($periodo_sel !== 'personalizado') ? 'd-none' : '' ?>">
                                <label class="form-label fw-bold small text-uppercase text-body-secondary">Desde</label>
                                <input type="date" name="desde" id="fecha_desde" class="form-control  border border-subtle"
                                    style="border-radius: 10px;" value="<?= $fecha_desde ?>"
                                    <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                            </div>

                            <div class="col-md-2 div-fechas <?= ($periodo_sel !== 'personalizado') ? 'd-none' : '' ?>">
                                <label class="form-label fw-bold small text-uppercase text-body-secondary">Hasta</label>
                                <input type="date" name="hasta" id="fecha_hasta" class="form-control  border border-subtle"
                                    style="border-radius: 10px;" value="<?= $fecha_hasta ?>"
                                    <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Mostrar</label>
                                <select name="tipo_filtro" id="tipo_filtro"
                                    class="form-select fw-bold  border border-subtle" style="border-radius: 10px;">
                                    <option value="todos" <?= ($tipo_sel == 'todos') ? 'selected' : '' ?>>📁 Todos
                                    </option>
                                    <option value="compra" <?= ($tipo_sel == 'compra') ? 'selected' : '' ?>>🛒 Compras
                                    </option>
                                    <option value="gasto" <?= ($tipo_sel == 'gasto') ? 'selected' : '' ?>>💸 Gastos
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Metodo de
                                    pago</label>
                                <select name="metodo_filtro" id="metodo_filtro" class="form-select">
    <option value="todos" <?= $metodo_filtro == 'todos' ? 'selected' : '' ?>>Todos</option>
    <option value="efectivo" <?= $metodo_filtro == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
    <option value="transferencia" <?= $metodo_filtro == 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
    <option value="tarjeta" <?= $metodo_filtro == 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
</select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Exeso de
                                    material</label>
                                <select name="deuda_filtro" id="deuda_filtro" class="form-select">
    <option value="todos" <?= $deuda_filtro == 'todos' ? 'selected' : '' ?>>Todos</option>
    <option value="1" <?= $deuda_filtro == '1' ? 'selected' : '' ?>>Con deuda</option>
    <option value="0" <?= $deuda_filtro == '0' ? 'selected' : '' ?>>Sin deuda</option>
</select>
                            </div>

                            <div class="col-md-2 d-none animate__animated animate__fadeIn" id="contenedor_categoria">
                                <label class="form-label fw-bold small text-uppercase text-body-secondary">Categoría</label>
                                <select id="categoria_gasto_filtro" name="categoria_gasto_filtro"
                                    class="form-select  border border-subtle" style="border-radius: 10px;">
                                    <option value="0">-- Todas --</option>
                                    <?php foreach ($listaCategoriasGastos as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= ($categoria_gasto_id == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ($_SESSION['rol_id'] == 1): ?>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-body-secondary">Almacén</label>
                                <select id="almacen_filtro" name="almacen_filtro" class="form-select  border border-subtle"
                                    style="border-radius: 10px;">
                                    <option value="0">🌐 Todos</option>
                                    <?php foreach ($almacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"
                                        <?= (isset($_GET['almacen_filtro']) && $_GET['almacen_filtro'] == $alm['id']) ? 'selected' : '' ?>>
                                        📍 <?= $alm['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-auto">
                                <button type="submit"
                                    class="btn btn-primary shadow-sm d-flex align-items-center justify-content-center"
                                    style="border-radius: 12px; width: 45px; height: 40px; transition: all 0.3s;">
                                    <i class="bi bi-funnel-fill" style="font-size: 1.1rem;"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-primary border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-body-secondary small fw-bold mb-1">TOTAL COMPRAS</p>
                            <h3 id="kpi_compras" class="fw-bold mb-0 text-primary">
                                $ <?= number_format($totalSumCompras, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-warning border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-body-secondary small fw-bold mb-1">GASTOS OPERATIVOS</p>
                            <h3 id="kpi_gastos" class="fw-bold mb-0 text-warning">
                                $ <?= number_format($totalSumGastos, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-danger border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-danger small fw-bold mb-1">TOTAL EGRESOS</p>
                            <h3 id="kpi_total" class="fw-bold mb-0 card-title-text">
                                $ <?= number_format($granTotalEgresos, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        
           <div class="card  shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="egresosTabla">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #f1f3f5;">
                <tr class="text-secondary">
                    <th class="ps-4 py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">ID</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Almacén</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Fecha</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Folio</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tipo</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Entidad</th>
                    <th class="py-3 fw-bold text-uppercase text-center" style="font-size: 0.75rem; letter-spacing: 0.5px;">Deuda</th>
                    <th class="py-3 fw-bold text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total</th>
                    <th class="py-3 fw-bold text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.5px;">Método</th>
                    <th class="py-3 fw-bold text-uppercase text-center" style="font-size: 0.75rem; letter-spacing: 0.5px;">Estado</th>
                    <th class="py-3 fw-bold text-uppercase text-center" style="font-size: 0.75rem; letter-spacing: 0.5px;">Doc</th>
                    <th class="py-3 fw-bold text-uppercase text-end pe-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">Acciones</th>
                </tr>
            </thead>

            <tbody class="border-top-0">
                <?php if(!empty($egresos)): ?>
                <?php foreach($egresos as $e): ?>
                <tr class="border-bottom" style="transition: all 0.2s ease;">

                    <td class="ps-4">
                        <span class=" border border-subtle card-title-text border fw-medium">#<?= $e['id'] ?></span>
                    </td>

                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                <i class="bi bi-geo-alt text-danger" style="font-size: 0.75rem;"></i>
                            </div>
                            <span class="fw-medium card-title-text small"><?= htmlspecialchars($e['almacen_nombre'] ?? 'N/A') ?></span>
                        </div>
                    </td>

                    <td class="text-secondary small">
                        <?= date('d/m/Y', strtotime($e['fecha'])) ?>
                    </td>

                    <td>
                        <span class="fw-bold card-title-text" style="letter-spacing: -0.3px;">
                            <?= ($e['tipo'] == 'compra' ? 'FC-' : ($e['tipo'] == 'gasto' ? 'FG-' : 'PD-')) . $e['folio'] ?>
                        </span>
                    </td>

                    <td>
                        <?php 
                            $bg_tipo = match($e['tipo']) {
                                'compra' => 'bg-info bg-opacity-10 text-info border-info',
                                'gasto' => 'bg-warning bg-opacity-10 text-warning-emphasis border-warning',
                                'pago_deuda' => 'bg-purple bg-opacity-10 text-purple border-purple', // Requiere CSS para purple o usar primary
                                default => 'bg-secondary bg-opacity-10 text-secondary'
                            };
                            // Fallback para pago_deuda si no tienes purple en tu CSS
                            if($e['tipo'] == 'pago_deuda') $bg_tipo = 'bg-primary bg-opacity-10 text-primary border-primary';
                        ?>
                        <span class=" border py-1.5 px-2 fw-semibold text-uppercase <?= $bg_tipo ?>" style="font-size: 0.65rem;">
                            <?= str_replace('_', ' ', strtoupper($e['tipo'])) ?>
                        </span>
                    </td>

                    <td>
                        <div class="card-title-text fw-medium small text-truncate" style="max-width: 150px;">
                            <?= htmlspecialchars($e['entidad']) ?>
                        </div>
                    </td>

                    <td class="text-center">
                        <?php if(($e['tiene_deuda'] ?? 0) == 1): ?>
                            <span class=" bg-danger rounded-circle p-1" title="Pendiente de pago">
                                <i class="bi bi-clock-history"></i>
                            </span>
                        <?php else: ?>
                            <i class="bi bi-dash text-body-secondary"></i>
                        <?php endif; ?>
                    </td>

                    <td class="fw-bold text-end card-title-text">
                        $<?= number_format($e['total'], 2) ?>
                    </td>

                    <?php
                        $metodo = strtoupper($e['metodo_pago'] ?? 'EFECTIVO');
                        $dot_color = 'text-secondary';
                        if (str_contains($metodo, 'EFECT')) $dot_color = 'card-title-text';
                        elseif (str_contains($metodo, 'TARJ')) $dot_color = 'text-primary';
                        elseif (str_contains($metodo, 'TRANS')) $dot_color = 'text-warning';
                    ?>
                    <td class="text-end">
                        <span class="small fw-semibold text-secondary">
                            <i class="bi bi-circle-fill me-1 <?= $dot_color ?>" style="font-size: 0.5rem;"></i>
                            <?= $metodo ?>
                        </span>
                    </td>

                    <td class="text-center">
                        <?php if($e['tipo'] == 'compra'): ?>
                            <?php if(($e['piezas_faltantes'] ?? 0) > 0): ?>
                                <span class=" bg-white text-danger border border-danger fw-bold shadow-sm" style="font-size: 0.7rem;">
                                    - <?= number_format($e['piezas_faltantes'], 2) ?>
                                </span>
                            <?php else: ?>
                                <span class=" bg-success bg-opacity-10 text-success border border-success rounded-circle">
                                    <i class="bi bi-check"></i>
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-body-secondary opacity-50 small">N/A</span>
                        <?php endif; ?>
                    </td>

                 <td class="text-center">

    <div class="d-flex justify-content-center align-items-center gap-1">

        <?php if (!empty($e['documento_url'])): ?>

            <?php $documentos = explode(';;;', $e['documento_url']); ?>

            <div class="dropdown">

                <button
                    class="btn btn-sm btn-light border position-relative"
                    type="button"
                    data-bs-toggle="dropdown">

                    <i class="bi bi-folder2-open text-success"></i>

                    <span class="position-absolute top-0 start-100 translate-middle  rounded-pill bg-primary">
                        <?= count($documentos) ?>
                    </span>

                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow " style="min-width:320px;">

                    <li>
                        <h6 class="dropdown-header">
                            <i class="bi bi-files me-1"></i>
                            Documentos adjuntos
                              <button class="btn btn-sm btn-outline-primary rounded-pill"
    onclick="subirDocumentoCompra(
        <?= $e['id'] ?>, 
        '<?= $e['folio'] ?? ''?>', 
        '<?= $e['documento_url'] ?? ''?>',
        '<?= $e['tipo'] ?? ''?>'
    )">
   Agregar Nuevo <i class="bi bi-upload"></i>
</button>
                        </h6>
                    </li>

                    <?php foreach ($documentos as $doc): ?>

                        <?php
                        $partes = explode('|||', $doc);

                        $nombre = $partes[0] ?? '';
                        $direccion = $partes[1] ?? '';
                        $idDoc = $partes[2] ?? 0;

                        if (empty($direccion)) continue;
                        ?>

                        <li>
                            <div class="dropdown-item d-flex justify-content-between align-items-center py-2">

                                <a href="../../<?= $direccion ?>"
                                   target="_blank"
                                   class="text-decoration-none card-title-text flex-grow-1">

                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>

                                    <span class="small">
                                        <?= htmlspecialchars($nombre) ?>
                                    </span>

                                </a>

                                <button
                                    class="btn btn-sm btn-outline-danger "
                                    title="Eliminar documento"
                                    onclick="eliminarDocumento(<?= $idDoc ?>)">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </div>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>

        <?php if ($e['tipo'] == 'compra' || $e['tipo'] == 'gasto'): ?>
  <?php if (empty($e['documento_url'])): ?>
           <button class="btn btn-sm btn-outline-primary rounded-pill"
    onclick="subirDocumentoCompra(
        <?= $e['id'] ?>, 
        '<?= $e['folio'] ?? ''?>', 
        '<?= $e['documento_url'] ?? ''?>',
        '<?= $e['tipo'] ?? ''?>'
    )">
  Agregar  <i class="bi bi-upload"></i>
</button>
<?php endif; ?>

        <?php endif; ?>

    </div>

</td>

                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                              <?php if ($e['tipo'] == 'compra' && ($e['piezas_faltantes'] ?? 0) > 0): ?>
                            <button class="btn btn-sm btn-outline-danger py-0 px-2" 
                                    onclick="abrirModalAjuste(<?= $e['id'] ?>, '<?= $e['folio'] ?>')">
                                <i class="bi bi-wrench-adjustable"></i>
                            </button>
                        <?php endif; ?>
                         <?php if ($e['tipo'] == 'pago_deuda'): ?>
                           
                                    <button class="btn btn-sm btn-dark" onclick="abrirDetallePago(<?=$e['id']  ?>)">
    <i class="bi bi-eye"></i>
</button>
                            </button>
                        <?php endif; ?>
                       

                            <?php if($e['tiene_deuda'] == 1): ?>
                                <button class="btn btn-sm btn-danger shadow-sm px-2" onclick="abrirDeudaCompra(<?= $e['id'] ?>)" title="Pagar Deuda">
                                    <i class="bi bi-wallet2"></i>
                                </button>
                            <?php endif; ?>

                            <?php if(($e['pagado_cpp'] ?? 0) == 1): ?>
                                <button class="btn btn-sm btn-success shadow-sm px-2" disabled>
                                    <i class="bi bi-patch-check-fill"></i>
                                </button>
                            <?php endif; ?>
                            <?php if ($e['tipo'] != 'pago_deuda'): ?>
                                 <?php if ($e['tipo'] != 'gasto'): ?>
                            <button class="btn btn-sm btn-light border shadow-sm px-2 text-primary" onclick="verDetalle('<?= $e['tipo'] ?>', <?= $e['id'] ?>)">
                                <i class="bi bi-eye-fill"></i>
                            </button> 
<?php endif; ?>
                             
                            <?php if ($e['tipo'] == 'gasto'): ?>
                            <button class="btn btn-sm btn-light border shadow-sm px-2 text-primary" onclick="gastoDetalle_cargarVista('gasto', <?= $e['id'] ?>)">
                                <i class="bi bi-eye-fill"></i>
                            </button>
<?php endif; ?>
                            <button class="btn btn-sm btn-light border shadow-sm px-2 text-danger" 
                                onclick="<?= ($e['tipo']=='compra') ? "confirmarCancelacionCompra" : "confirmarCancelacionGasto" ?>('<?= $e['id'] ?>','<?= $e['folio'] ?>')">
                                <i class="bi bi-trash3"></i>
                            </button>
                             
                        <?php endif; ?>

                          
                        </div>
                    </td>

                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="12" class="text-center py-5 text-body-secondary">
                        <i class="bi bi-inbox h1 d-block opacity-25"></i>
                        No se encontraron movimientos registrados.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
        </div>
    </main>



    <?php 
$ruta = __DIR__ . '/egresosComponets/modalCompra.php';
if (!file_exists($ruta)) {
    echo "<script>console.error('ERROR: El archivo del modal no existe en: $ruta');</script>";
}
require_once $ruta;

?>


    <?php require_once __DIR__ . '/egresosComponets/modalCompra.php'; ?>
   
    <?php require_once __DIR__ . '/egresosComponets/modalAjuste.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalDetalles.php'; ?><?php require_once __DIR__ . '/egresosComponets/modalDetalleGasto.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalGasto.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/cuentasPendientes.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/historialCuentasPorPagar.php'; ?>
 <?php require_once __DIR__ . '/egresosComponets/modalDetallePago.php'; ?>





    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <?php require_once __DIR__ . '/solicitudesCompra/ModalSolicitud.php'; ?>
    <script>
    // Forzamos que sea global con window.
    window.DATA_COMPRAS = {
        productos: <?php echo json_encode($productos); ?>,
        almacenes: <?php echo json_encode($almacenes); ?>
    };
    // Imprime esto en la consola para que verifiques si hay datos
    console.log("Productos cargados:", window.DATA_COMPRAS.productos);
    </script>
<script>
    /**
 * Abre una nueva página con la tabla estilizada únicamente con CSS y lanza el diálogo de impresión / guardar en PDF.
 * @param {string} tablaId - ID del elemento <table> a imprimir (ej: 'egresosTabla')
 */
/**
 * Genera una vista de impresión compacta y ajustada a 1 sola página A4 Horizontal
 * @param {string} tablaId - ID del elemento <table> (ej: 'egresosTabla')
 */
function imprimirTablaPDF(tablaId = 'egresosTabla') {
    const tablaOriginal = document.getElementById(tablaId);
    
    if (!tablaOriginal) {
        console.error(`No se encontró la tabla con el ID: ${tablaId}`);
        return;
    }

    // Clonar la tabla para no modificar el DOM activo
    const tablaClonada = tablaOriginal.cloneNode(true);

    // 1. Remover la columna de "Acciones" (última columna)
    tablaClonada.querySelectorAll('tr').forEach(row => {
        if (row.lastElementChild) {
            row.lastElementChild.remove();
        }
    });

    // 2. Limpiar elementos innecesarios dentro de las celdas clonadas (iconos sobrantes, botones de docs, etc.)
    tablaClonada.querySelectorAll('button, .dropdown-menu, script').forEach(el => el.remove());

    const ventanaImpresion = window.open('', '_blank', 'width=1000,height=750');

    const contenidoHTML = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Egresos</title>
            <style>
                /* Configuración de Hoja A4 Horizontal */
                @page {
                    size: A4 landscape;
                    margin: 8mm; /* Margen estrecho para maximizar área de impresión */
                }

                * {
                    box-sizing: border-box;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                body {
                    font-family: Arial, Helvetica, sans-serif;
                    color: #111;
                    margin: 0;
                    padding: 0;
                    font-size: 8.5pt; /* Tamaño de fuente compacto */
                    line-height: 1.1;
                }

                /* Encabezado compacto */
                .header-reporte {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    border-bottom: 1.5pt solid #2c3e50;
                    padding-bottom: 4px;
                    margin-bottom: 8px;
                }

                .header-reporte h2 {
                    margin: 0;
                    color: #2c3e50;
                    font-size: 13pt;
                    text-transform: uppercase;
                    letter-spacing: -0.3px;
                }

                .header-reporte .meta-info {
                    font-size: 8pt;
                    color: #555;
                }

                /* Control estricto del ancho de la tabla */
                table {
                    width: 100% !important;
                    max-width: 100% !important;
                    table-layout: fixed; /* Fuerza a las columnas a respetar el ancho disponible */
                    border-collapse: collapse;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }

                th, td {
                    padding: 4px 3px !important; /* Contexto ultra-compacto */
                    vertical-align: middle;
                    border-bottom: 1px solid #d1d5db;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap; /* Evita saltos de línea innecesarios */
                }

                th {
                    background-color: #f1f5f9 !important;
                    color: #1e293b;
                    font-size: 7.5pt;
                    font-weight: bold;
                    text-transform: uppercase;
                    border-bottom: 1.5pt solid #94a3b8;
                }

                /* Anchos proporcionales asignados por columna para encajar perfecto */
                th:nth-child(1), td:nth-child(1) { width: 4%; text-align: center; } /* ID */
                th:nth-child(2), td:nth-child(2) { width: 12%; }                   /* Almacén */
                th:nth-child(3), td:nth-child(3) { width: 8%; text-align: center; } /* Fecha */
                th:nth-child(4), td:nth-child(4) { width: 9%; }                   /* Folio */
                th:nth-child(5), td:nth-child(5) { width: 8%; text-align: center; } /* Tipo */
                th:nth-child(6), td:nth-child(6) { width: 22%; }                  /* Entidad / Proveedor */
                th:nth-child(7), td:nth-child(7) { width: 5%; text-align: center; } /* Deuda */
                th:nth-child(8), td:nth-child(8) { width: 10%; text-align: right; } /* Total */
                th:nth-child(9), td:nth-child(9) { width: 9%; text-align: center; } /* Método */
                th:nth-child(10), td:nth-child(10) { width: 7%; text-align: center; }/* Faltantes */
                th:nth-child(11), td:nth-child(11) { width: 6%; text-align: center; }/* Docs */

                /* Filas alternadas */
                tbody tr:nth-child(even) {
                    background-color: #f8fafc !important;
                }

                /* Utilidades de alineación */
                .text-center { text-align: center !important; }
                .text-end { text-align: right !important; }
                .fw-bold { font-weight: bold !important; }

                /* Ocultar cualquier elemento interactivo restante */
                .btn, .dropdown, button, i.bi-folder2-open {
                    display: none !important;
                }

                /* Pie de página dinámico */
                .footer-reporte {
                    margin-top: 10px;
                    display: flex;
                    justify-content: space-between;
                    font-size: 7.5pt;
                    color: #64748b;
                }

                /* Optimización para diálogo de impresión */
                @media print {
                    html, body {
                        width: 100%;
                        height: 100%;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header-reporte">
                <div>
                    <h2>Reporte de Movimientos de Egreso</h2>
                </div>
                <div class="meta-info">
                    Impreso el: ${new Date().toLocaleDateString('es-MX')} ${new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })}
                </div>
            </div>

            ${tablaClonada.outerHTML}

            <div class="footer-reporte">
                <span>cfsistem - Control Financiero</span>
                <span>Página 1 de 1</span>
            </div>

            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 300);
                };
            <\/script>
        </body>
        </html>
    `;

    ventanaImpresion.document.open();
    ventanaImpresion.document.write(contenidoHTML);
    ventanaImpresion.document.close();
}
/**
 * Exporta el contenido de una tabla HTML a un archivo .csv
 * @param {string} tablaId - ID de la tabla (ej. 'egresosTabla')
 * @param {string} nombreArchivo - Nombre base del archivo a descargar
 */
function exportarTablaCSV(tablaId = 'egresosTabla', nombreArchivo = 'Reporte_Egresos') {
    const tablaOriginal = document.getElementById(tablaId);

    if (!tablaOriginal) {
        console.error(`No se encontró la tabla con el ID: ${tablaId}`);
        return;
    }

    const filas = tablaOriginal.querySelectorAll('tr');
    const lineasCSV = [];

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('th, td');
        
        // Si no hay celdas (fila vacía), ignorar
        if (celdas.length === 0) return;

        const valoresFila = [];

        // Recorrer todas las celdas EXCEPTO la última (Columna de Acciones)
        for (let i = 0; i < celdas.length - 1; i++) {
            let texto = celdas[i].innerText || celdas[i].textContent || '';

            // 1. Limpieza de caracteres: eliminar saltos de línea y tabulaciones innecesarias
            texto = texto.replace(/\r?\n|\r/g, ' ').replace(/\s+/g, ' ').trim();

            // 2. Escapar comillas dobles internas duplicándolas ("" -> """")
            texto = texto.replace(/"/g, '""');

            // 3. Envolver entre comillas dobles para proteger comas y caracteres especiales
            valoresFila.push(`"${texto}"`);
        }

        // Unir las celdas de la fila separadas por coma
        lineasCSV.push(valoresFila.join(','));
    });

    // Unir todas las filas con salto de línea
    const contenidoCSV = lineasCSV.join('\n');

    // Crear el Blob con el BOM UTF-8 (\uFEFF) para que Excel reconozca acentos y caracteres especiales
    const blob = new Blob(['\uFEFF' + contenidoCSV], {
        type: 'text/csv;charset=utf-8;'
    });

    // Descargar el archivo dinámicamente
    const url = URL.createObjectURL(blob);
    const enlace = document.createElement('a');
    const fecha = new Date().toISOString().slice(0, 10);
    
    enlace.href = url;
    enlace.download = `${nombreArchivo}_${fecha}.csv`;
    
    document.body.appendChild(enlace);
    enlace.click();

    // Limpiar memoria
    document.body.removeChild(enlace);
    URL.revokeObjectURL(url);
}
/**
 * SISTEMA DE FILTROS Y UI
 * Gestiona el envío automático, visibilidad de fechas y categorías.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const f = {
            form: document.getElementById('formFiltros'),
            periodo: document.getElementById('filtro_rapido'),
            desde: document.getElementById('fecha_desde'),
            hasta: document.getElementById('fecha_hasta'),
            almacen: document.getElementById('almacen_filtro'),
            tipo: document.getElementById('tipo_filtro'),
            categoria: document.getElementById('categoria_gasto_filtro'),
            deuda: document.getElementById('deuda_filtro'),
            metodo: document.getElementById('metodo_filtro'),
            cont_cat: document.getElementById('contenedor_categoria')
        };

        const enviar = () => {
            if (!f.form) return;
            // Aseguramos que las fechas se envíen (PHP no recibe campos disabled)
            if (f.desde) f.desde.disabled = false;
            if (f.hasta) f.hasta.disabled = false;
            f.form.submit();
        };

        // --- 1. Lógica de Categorías (Mostrar/Ocultar) ---
        const toggleCategoria = () => {
            if (!f.tipo || !f.cont_cat) return;
            if (f.tipo.value === 'gasto') {
                f.cont_cat.classList.remove('d-none');
            } else {
                f.cont_cat.classList.add('d-none');
                if (f.categoria) f.categoria.value = "0"; // Reset si no es gasto
            }
        };

        // --- 2. Lógica de Periodos (Rápido vs Personalizado) ---
        if (f.periodo) {
            f.periodo.addEventListener('change', function() {
                const esPerso = this.value === 'personalizado';
                const divs = document.querySelectorAll('.div-fechas');
                const inputs = document.querySelectorAll('.div-fechas input');

                divs.forEach(div => esPerso ? div.classList.remove('d-none') : div.classList.add('d-none'));
                inputs.forEach(i => i.disabled = !esPerso);

                if (!esPerso) enviar();
            });
        }

        // --- 3. Eventos de Cambio Directo ---
        // Almacén, Deuda, Método: Envían al cambiar
        [f.almacen, f.deuda, f.metodo, f.categoria].forEach(el => {
            if (el) el.addEventListener('change', enviar);
        });

        // Tipo: Cambia visibilidad de categoría y envía
        if (f.tipo) {
            f.tipo.addEventListener('change', () => {
                toggleCategoria();
                enviar();
            });
        }

        // --- 4. Fechas Manuales ---
        [f.desde, f.hasta].forEach(el => {
            if (!el) return;
            el.addEventListener('change', () => {
                if (f.periodo) f.periodo.value = 'personalizado';
                if (f.desde.value && f.hasta.value) enviar();
            });
        });

        // Ejecución inicial para restaurar estado tras recarga
        toggleCategoria();
    });
})();

/**
 * PARCHE PARA MODALES
 * Corrige el scroll y el backdrop en modales anidados o cierres rápidos.
 */
(function() {
    $(document).on('hidden.bs.modal', '.modal', function() {
        if ($('.modal.show').length === 0) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
        } else {
            $('body').addClass('modal-open');
        }
    });
})();

/**
 * ACCIONES: CANCELACIONES (AJAX + SWEETALERT2)
 */
function confirmarCancelacionCompra(id, folio) {
    Swal.fire({
        title: `¿Anular Compra ${folio}?`,
        text: "Se restará el stock y se eliminarán los lotes. Acción irreversible.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: '../controllers/egresosController.php?action=cancelarCompra',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('¡Anulada!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Atención', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Error', 'No se pudo procesar la cancelación.', 'error');
                }
            });
        }
    });
}

function confirmarCancelacionGasto(id, folio) {
    Swal.fire({
        title: `¿Anular Gasto: ${folio}?`,
        text: "Por favor, escribe la razón de la cancelación:",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Escribe aquí la razón...',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Regresar',
        inputValidator: (value) => { if (!value) return '¡La razón es obligatoria!'; }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: '../controllers/egresosController.php?action=cancelarGasto',
                method: 'POST',
                data: { id: id, razon: result.value },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('¡Anulado!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Error Crítico', 'Consulta la consola (F12).', 'error');
                }
            });
        }
    });
}
function subirDocumentoCompra(compra_id, folio, documento_actual = '',tipo) {
    
                console.log('gasto');
           

    Swal.fire({
        title: 'Documento de Compra',
        html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar documento</label>
                <input type="file" id="swal_file_doc" class="form-control mb-2" accept=".pdf,image/*">
                
               
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#198754',
        focusConfirm: false,

        preConfirm: async () => {

            const fileInput = document.getElementById('swal_file_doc');
            const file = fileInput?.files[0];

            if (!file) {
                Swal.showValidationMessage('Selecciona un archivo');
                return false;
            }

            const formData = new FormData();
            formData.append('action', 'subirDocumento');
            formData.append('compra_id', compra_id);
            formData.append('folio', folio);
            formData.append('documento', file);
             formData.append('tipo', tipo);
             

            try {
                const response = await fetch('/myvet/app/controllers/egresosController.php?action=subirDocumento', {
                    method: 'POST',
                    body: formData
                });

                // 🔥 LEEMOS COMO TEXTO PRIMERO (ANTI "Unexpected token <")
                const text = await response.text();
                console.log('RESPUESTA CRUDA:', text);

                let res;
                try {
                    res = JSON.parse(text);
                } catch (e) {
                    throw new Error('El servidor no devolvió JSON válido');
                }

                if (!res.success) {
                    throw new Error(res.message || 'Error al subir archivo');
                }

                return res;

            } catch (err) {
                Swal.showValidationMessage(err.message);
                return false;
            }
        }

    }).then(result => {

        if (!result.isConfirmed || !result.value) return;

       Swal.fire({
    icon: 'success',
    title: 'Guardado',
    text: 'Documento actualizado correctamente',
    timer: 1800,
    showConfirmButton: false
}).then(() => {
    location.reload();
});
        if (typeof cargarCompras === 'function') {
            cargarCompras();
        }
    });
}

function eliminarDocumento(id) {
    
                console.log('gasto');
           

    Swal.fire({
        title: 'Eliminar Documento',
        
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#ed0909',
        focusConfirm: false,

        preConfirm: async () => {

         

            const formData = new FormData();
            
             formData.append('id', id);
             

            try {
                const response = await fetch('/myvet/app/controllers/egresosController.php?action=eliminarDocumento', {
                    method: 'POST',
                    body: formData
                });

                // 🔥 LEEMOS COMO TEXTO PRIMERO (ANTI "Unexpected token <")
                const text = await response.text();
                console.log('RESPUESTA CRUDA:', text);

                let res;
                try {
                    res = JSON.parse(text);
                } catch (e) {
                    throw new Error('El servidor no devolvió JSON válido');
                }

                if (!res.success) {
                    throw new Error(res.message || 'Error al subir archivo');
                }

                return res;

            } catch (err) {
                Swal.showValidationMessage(err.message);
                return false;
            }
        }

    }).then(result => {

        if (!result.isConfirmed || !result.value) return;

       Swal.fire({
    icon: 'success',
    title: 'Eliminado',
    text: 'Documento eliminado correctamente',
    timer: 1800,
    showConfirmButton: false
}).then(() => {
    location.reload();
});
        if (typeof cargarCompras === 'function') {
            cargarCompras();
        }
    });
}
</script>

</body>

</html>