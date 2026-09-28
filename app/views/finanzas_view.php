<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas y Estadísticas | G-M SISTEM</title>
    <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

     <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" /><link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        :root { --sidebar-width: 0px; --accent: #4361ee; }
      
        .main-content {  padding: 30px; padding-top: 80px; transition: all 0.3s; }
        .card-glass { background: rgba(255, 255, 255, 0.9);  border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); transition: transform 0.3s; }
        .card-glass:hover { transform: translateY(-3px); }
        .kpi-card { border-radius: 20px; color: white; position: relative; overflow: hidden; }
        .kpi-icon { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.15; }
        .alert-item { border-left: 4px solid #ef4444; background: #fff5f5; border-radius: 10px; margin-bottom: 10px; }
        .filter-card { background: #ffffff; border-radius: 15px; border: 1px solid #e2e8f0; }
        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual ?? 'finanzas'); 
    } ?>

    <main class="main-content">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
            <div>
                <h2 class="fw-bold m-0 text-dark">Panel de Inteligencia</h2>
                <p class="text-body-secondary small m-0">Análisis financiero y logístico detallado</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-white text-primary border shadow-sm p-2 rounded-pill">
                    <i class="bi bi-truck me-1"></i> <span id="badgeTraspasos"><?= intval($pendientes['traspasos'] ?? 0) ?></span> Traspasos
                </span>
                <span class="badge bg-white text-info border shadow-sm p-2 rounded-pill">
                    <i class="bi bi-box-seam me-1"></i> <span id="badgeCompras"><?= intval($pendientes['compras'] ?? 0) ?></span> Compras
                </span>
            </div>
        </div>

        <!-- Filtros Dinámicos -->
        <div class="card filter-card p-3 mb-4 shadow-sm" data-aos="fade-up">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label small fw-bold text-secondary">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" value="<?= $fecha_inicio ?>" class="form-control form-control-sm rounded-3">
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label small fw-bold text-secondary">Fecha Fin</label>
                    <input type="date" id="fecha_fin" value="<?= $fecha_fin ?>" class="form-control form-control-sm rounded-3">
                </div>
                <div class="col-md-4">
                    <label for="almacen_id" class="form-label small fw-bold text-secondary">Almacén</label>
                    <select id="almacen_id" class="form-select form-select-sm rounded-3">
                     <?php if ($_SESSION['almacen_id']==0): ?>
                                                <option value="">Seleccionar ubicación...</option>
                                                <?php endif; ?>
                        <?php if (isset($listaAlmacenes) && (is_array($listaAlmacenes) || is_object($listaAlmacenes))): ?>
                            <?php foreach($listaAlmacenes as $almacen): ?>
                                <option value="<?= $almacen['id'] ?>"><?= htmlspecialchars($almacen['nombre']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tarjetas KPI -->
        <div class="row g-3 mb-4">
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <div class="card kpi-card bg-primary p-4 shadow-sm">
                    <small class="opacity-75 uppercase fw-bold">Ventas del Mes</small>
                    <h2 class="fw-bold mb-0" id="kpiVentas">$<?= number_format($totalVentas, 2) ?></h2>
                    <i class="bi bi-graph-up kpi-icon"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="card kpi-card bg-danger p-4 shadow-sm">
                    <small class="opacity-75 uppercase fw-bold">Egresos del Mes</small>
                    <h2 class="fw-bold mb-0" id="kpiEgresos">$<?= number_format($totalEgresos, 2) ?></h2>
                    <i class="bi bi-cart-dash kpi-icon"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="card kpi-card p-4 shadow-sm <?= $utilidad >= 0 ? 'bg-success' : 'bg-warning' ?>" id="cardUtilidad">
                    <small class="opacity-75 uppercase fw-bold">Utilidad Bruta</small>
                    <h2 class="fw-bold mb-0" id="kpiUtilidad">$<?= number_format($utilidad, 2) ?></h2>
                    <i class="bi bi-coin kpi-icon"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="card kpi-card bg-dark p-4 shadow-sm">
                    <small class="opacity-75 uppercase fw-bold">Equipo Activo</small>
                    <h2 class="fw-bold mb-0" id="kpiUsuarios"><?= $totalUsuarios ?></h2>
                    <i class="bi bi-people kpi-icon"></i>
                </div>
            </div>
        </div>

        <!-- Gráficos y Listas -->
        <div class="row g-4">
            <div class="col-lg-8" data-aos="fade-right">
                <div class="card card-glass p-4 h-100">
                    <h6 class="fw-bold mb-4">Balance Semanal de Movimientos</h6>
                    <canvas id="chartBalance" style="max-height: 350px;"></canvas>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-left">
                <div class="card card-glass p-4 h-100">
                    <h6 class="fw-bold text-danger mb-4"><i class="bi bi-exclamation-octagon-fill me-2"></i>Stock Crítico</h6>
                    <div class="alert-container" id="contenedorCritico">
                        <?php if (!empty($dataCriticoJS)): ?>
                            <?php foreach ($dataCriticoJS as $item): ?>
                                <div class="p-3 alert-item shadow-sm">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold small"><?= htmlspecialchars($item['producto']) ?></span>
                                        <span class="badge bg-danger">Faltan: <?= $item['stock_minimo'] - $item['stock'] ?></span>
                                    </div>
                                    <div class="text-body-secondary" style="font-size: 11px;"><?= htmlspecialchars($item['almacen']) ?> - Actual: <?= $item['stock'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5 text-body-secondary small">Todo el stock está correcto</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6" data-aos="fade-up">
                <div class="card card-glass p-4">
                    <h6 class="fw-bold mb-3">Valoración por Almacén ($)</h6>
                    <canvas id="chartAlmacenes"></canvas>
                </div>
            </div>

            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card card-glass p-4">
                    <h6 class="fw-bold mb-3">Ranking de Productos</h6>
                    <canvas id="chartProductos"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    AOS.init();

    let chartBalanceInstance = null;
    let chartAlmacenesInstance = null;
    let chartProductosInstance = null;

    const formatoMoneda = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val || 0);

    // Renderizado inicial con los datos del servidor PHP
    document.addEventListener('DOMContentLoaded', () => {
        cargarDatosFinanzas();
        renderChartBalance(<?= $totalVentas ?>, <?= $totalEgresos ?>);
        renderChartAlmacenes(<?= json_encode($dataAlmacenesJS) ?>);
        renderChartProductos(<?= json_encode($dataTopProdJS) ?>);

        document.getElementById('almacen_id').addEventListener('change', cargarDatosFinanzas);
        document.getElementById('fecha_inicio').addEventListener('change', cargarDatosFinanzas);
        document.getElementById('fecha_fin').addEventListener('change', cargarDatosFinanzas);
    });

    async function cargarDatosFinanzas() {
        const almacenId = document.getElementById('almacen_id').value;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        const params = new URLSearchParams({
            action: 'get_dashboard_data',
            almacen_id: almacenId,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        });

        try {
            const response = await fetch(`?${params.toString()}`);
            if (!response.ok) throw new Error('Error al obtener datos');

            const data = await response.json();

            if (!data.success) {
                console.error('Error:', data.message);
                return;
            }

            document.getElementById('badgeTraspasos').textContent = data.pendientes.traspasos || 0;
            document.getElementById('badgeCompras').textContent = data.pendientes.compras || 0;

            document.getElementById('kpiVentas').textContent = formatoMoneda(data.balance.totalVentas);
            document.getElementById('kpiEgresos').textContent = formatoMoneda(data.balance.totalEgresos);
            document.getElementById('kpiUtilidad').textContent = formatoMoneda(data.balance.utilidad);
            document.getElementById('kpiUsuarios').textContent = data.totalUsuarios || 0;

            const cardUtilidad = document.getElementById('cardUtilidad');
            if (data.balance.utilidad >= 0) {
                cardUtilidad.classList.remove('bg-warning');
                cardUtilidad.classList.add('bg-success');
            } else {
                cardUtilidad.classList.remove('bg-success');
                cardUtilidad.classList.add('bg-warning');
            }

            const contenedorCritico = document.getElementById('contenedorCritico');
            contenedorCritico.innerHTML = '';

            if (data.stockCritico && data.stockCritico.length > 0) {
                data.stockCritico.forEach(item => {
                    const faltante = item.stock_minimo - item.stock;
                    contenedorCritico.innerHTML += `
                        <div class="p-3 alert-item shadow-sm">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold small">${item.producto}</span>
                                <span class="badge bg-danger">Faltan: ${faltante}</span>
                            </div>
                            <div class="text-body-secondary" style="font-size: 11px;">${item.almacen} - Actual: ${item.stock}</div>
                        </div>
                    `;
                });
            } else {
                contenedorCritico.innerHTML = '<div class="text-center py-5 text-body-secondary small">Todo el stock está correcto</div>';
            }

            renderChartBalance(data.balance.totalVentas, data.balance.totalEgresos);
            renderChartAlmacenes(data.almacenes || []);
            renderChartProductos(data.topProductos || []);

        } catch (error) {
            console.error('Error en consulta AJAX:', error);
        }
    }

    function renderChartBalance(totalVentas, totalEgresos) {
        if (chartBalanceInstance) chartBalanceInstance.destroy();

        chartBalanceInstance = new Chart(document.getElementById('chartBalance'), {
            type: 'line',
            data: {
                labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                datasets: [{
                    label: 'Ventas',
                    data: [totalVentas * 0.2, totalVentas * 0.3, totalVentas * 0.25, totalVentas * 0.25],
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Egresos',
                    data: [totalEgresos * 0.3, totalEgresos * 0.2, totalEgresos * 0.3, totalEgresos * 0.2],
                    borderColor: '#ef4444',
                    tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    function renderChartAlmacenes(almacenes) {
        if (chartAlmacenesInstance) chartAlmacenesInstance.destroy();

        const labels = almacenes.map(a => a.nombre);
        const dataValues = almacenes.map(a => parseFloat(a.valor_total) || 0);

        chartAlmacenesInstance = new Chart(document.getElementById('chartAlmacenes'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Valor Total',
                    data: dataValues,
                    backgroundColor: '#10b981',
                    borderRadius: 10
                }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } } }
        });
    }

    function renderChartProductos(productos) {
        if (chartProductosInstance) chartProductosInstance.destroy();

        const labels = productos.map(p => p.nombre);
        const dataValues = productos.map(p => parseInt(p.total_vendido) || 0);

        chartProductosInstance = new Chart(document.getElementById('chartProductos'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#4361ee', '#3f37c9', '#4895ef', '#4cc9f0', '#480ca8']
                }]
            },
            options: { plugins: { legend: { position: 'right' } } }
        });
    }
    </script>
</body>
</html>