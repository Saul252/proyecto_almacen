<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 1. Requerimientos de sesión y conexión
require_once __DIR__ . '/../../includes/auth.php'; 
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/finanzasModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('finanzas');

$finanzasModel = new FinanzasModel($conexion);
$almacenModel  = new AlmacenModel($conexion);

// --- 2. MANEJO DE PETICIONES AJAX ---// --- 1. RESPUESTA AJAX EXCLUSIVA PARA FILTROS ---
if (isset($_GET['action']) && $_GET['action'] === 'get_dashboard_data') {
    while (ob_get_level()) { ob_end_clean(); } // Limpia cualquier buffer o HTML residual
    header('Content-Type: application/json; charset=utf-8');

    try {
        $almacen_id   = !empty($_GET['almacen_id']) ? intval($_GET['almacen_id']) : null;
        $fecha_inicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
        $fecha_fin    = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');

        $kpis         = $finanzasModel->getKPIs($almacen_id, $fecha_inicio, $fecha_fin);
        $resAlmacenes = $finanzasModel->getStockAlmacenes($almacen_id);
        $resTopProd   = $finanzasModel->getTopProductos($almacen_id, $fecha_inicio, $fecha_fin);
        $resCritico   = $finanzasModel->getStockCritico($almacen_id);
        $pendientes   = $finanzasModel->getPendientes($almacen_id, $fecha_inicio, $fecha_fin);
        $resUsuarios  = $finanzasModel->getUsuariosActivos($almacen_id);

        $almacenesArr = [];
        if ($resAlmacenes) {
            while ($row = $resAlmacenes->fetch_assoc()) { $almacenesArr[] = $row; }
        }

        $topProductosArr = [];
        if ($resTopProd) {
            while ($row = $resTopProd->fetch_assoc()) { $topProductosArr[] = $row; }
        }

        $stockCriticoArr = [];
        if ($resCritico) {
            while ($row = $resCritico->fetch_assoc()) { $stockCriticoArr[] = $row; }
        }

        $ventas  = floatval($kpis['ventas_mes'] ?? 0);
        $egresos = floatval($kpis['compras_mes'] ?? 0) + floatval($kpis['gastos_mes'] ?? 0);

        echo json_encode([
            'success' => true,
            'balance' => [
                'totalVentas'  => $ventas,
                'totalEgresos' => $egresos,
                'utilidad'     => $ventas - $egresos
            ],
            'pendientes'    => [
                'traspasos' => intval($pendientes['traspasos'] ?? 0),
                'compras'   => intval($pendientes['compras'] ?? 0)
            ],
            'totalUsuarios' => $resUsuarios ? $resUsuarios->num_rows : 0,
            'stockCritico'  => $stockCriticoArr,
            'almacenes'     => $almacenesArr,
            'topProductos'  => $topProductosArr
        ], JSON_UNESCAPED_UNICODE);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit; // Detiene la ejecución para no cargar el HTML de la vista
}
// --- 3. CARGA DE LA VISTA (GET INICIAL) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
      
// --- 2. RENDERIZADO DE LA VISTA (CARGA INICIAL DE LA PÁGINA) ---
$paginaActual   = 'finanzas';
$tituloPagina   = 'Finanzas y Estadísticas';
$fecha_inicio   = date('Y-m-01');
$fecha_fin      = date('Y-m-d');

$listaAlmacenes = $almacenModel->getAlmacenes(0);
$kpis           = $finanzasModel->getKPIs(null, $fecha_inicio, $fecha_fin);
$resAlmacenes   = $finanzasModel->getStockAlmacenes(null);
$resTopProd     = $finanzasModel->getTopProductos(null, $fecha_inicio, $fecha_fin);
$resCritico     = $finanzasModel->getStockCritico(null);
$pendientes     = $finanzasModel->getPendientes(null, $fecha_inicio, $fecha_fin);
$resUsuarios    = $finanzasModel->getUsuariosActivos(null);

$totalVentas    = floatval($kpis['ventas_mes'] ?? 0);
$totalEgresos   = floatval($kpis['compras_mes'] ?? 0) + floatval($kpis['gastos_mes'] ?? 0);
$utilidad       = $totalVentas - $totalEgresos;
$totalUsuarios  = $resUsuarios ? $resUsuarios->num_rows : 0;

// Preparar arrays para pasar datos limpios a JavaScript en el primer render
$dataAlmacenesJS = [];
if ($resAlmacenes) { while ($row = $resAlmacenes->fetch_assoc()) { $dataAlmacenesJS[] = $row; } }

$dataTopProdJS = [];
if ($resTopProd) { while ($row = $resTopProd->fetch_assoc()) { $dataTopProdJS[] = $row; } }

$dataCriticoJS = [];
if ($resCritico) { while ($row = $resCritico->fetch_assoc()) { $dataCriticoJS[] = $row; } }

require_once __DIR__ . '/../views/finanzas_view.php';
    } catch (Exception $e) {
        die("Error al cargar la página de finanzas: " . $e->getMessage());
    }
}