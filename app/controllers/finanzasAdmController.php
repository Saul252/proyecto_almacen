<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/corteCajaModel.php'; 
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('finanzas_admin');
$egresoModel = new EgresoModel($conexion);
$modelo = new CorteCajaModel($conexion);
$almacenModel = new AlmacenModel($conexion);
date_default_timezone_set('America/Mexico_City');

// 1. Identificar jerarquía del usuario
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$usuario_id     = $_SESSION['id_usuario'] ?? 0;

// --- LÓGICA DE GUARDADO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardarCorte') {
    header('Content-Type: application/json');
    if (session_status() === PHP_SESSION_NONE) session_start();

    $usuario_id = $_SESSION['usuario_id'] ?? 0;
    $fecha_corte = $_POST['fecha_corte'] ?? date('Y-m-d');
    $target_save = intval($_POST['almacen_id'] ?? 0);

    try {
        // 1. Recepción de Datos
        $s_ini_efec = floatval($_POST['saldo_inicial_efectivo'] ?? 0);
        $s_ini_tarj = floatval($_POST['saldo_inicial_tarjeta'] ?? 0);
        $s_ini_tran = floatval($_POST['saldo_inicial_transferencia'] ?? 0);
        $saldo_inicial_total = $s_ini_efec + $s_ini_tarj + $s_ini_tran;
       $total_efectivo= floatval($_POST['total_efectivo'] ?? 0);
        $efectivo_real = floatval($_POST['total_efectivo'] ?? 0);
        $tarjeta       = floatval($_POST['total_tarjeta'] ?? 0);
        $transferencia = floatval($_POST['total_transferencia'] ?? 0);
        $ingresos_dia  = $efectivo_real + $tarjeta + $transferencia;

        $g_efec = floatval($_POST['gasto_efectivo'] ?? 0);
        $g_tarj = floatval($_POST['gasto_tarjeta'] ?? 0);
        $g_tran = floatval($_POST['gasto_transferencia'] ?? 0);
      $c_efec = floatval($_POST['compras_efectivo'] ?? 0);
        $c_tarj = floatval($_POST['compras_tarjeta'] ?? 0);
        $c_tran = floatval($_POST['compras_transferencia'] ?? 0);
        $saldo_favor=floatval($_POST['saldo_favor']??0);
       
        $gastos_totales  = $g_efec + $g_tarj + $g_tran;
        $compras_totales  = $c_efec + $c_tarj + $c_tran;
        $egresos_dia     = $gastos_totales + $compras_totales;

        // 2. Cálculos Finales
        $gran_total_ingresos = ($saldo_inicial_total + $ingresos_dia) - $egresos_dia;
        $abonoEfectivo=floatval($_POST['abono_efectivo'] ?? 0);
         $abonoTarjeta=floatval($_POST['abono_tarjeta']?? 0);
         $abonoTransferencia=floatval($_POST['abono_transferencia'] ?? 0);
        
        $abonos_totales = floatval($_POST['abono_efectivo'] ?? 0) + floatval($_POST['abono_tarjeta'] ?? 0) + floatval($_POST['abono_transferencia'] ?? 0);
        $deuda_pendiente = floatval($_POST['deuda_pendiente'] ?? 0);
        $venta_bruta_calculada = ($ingresos_dia + $deuda_pendiente) - $abonos_totales;
        $efectivoEnCaja = ($efectivo_real + $s_ini_efec + $abonoEfectivo)-($g_efec + $c_efec+$saldo_favor);
        $TarjetaEnCaja= ($tarjeta+$s_ini_tarj+$abonoTarjeta)-($g_tarj + $c_tarj);
        $TransferencianCaja= ($transferencia+$s_ini_tran+$abonoTransferencia)-($g_tran+$c_tran);


        // ===============================================
        // 🔴 ERROR LOG DE SEGURIDAD
        // ===============================================
        error_log("---------- DEBUG CORTE CAJA ----------");
        error_log("Almacen: $target_save | Usuario: $usuario_id");
        error_log("Saldos Iniciales: EF:$s_ini_efec, TJ:$s_ini_tarj, TR:$s_ini_tran (Total: $saldo_inicial_total)");
        error_log("Ingresos Turno: EF:$efectivo_real, TJ:$tarjeta, TR:$transferencia (Total: $ingresos_dia)");
        error_log("Gastos: EF:$g_efec, TJ:$g_tarj, TR:$g_tran, Compras: $compras_totales");
        error_log("RESULTADO FINAL CAJA: $gran_total_ingresos");
        error_log("---------------------------------------");

        // 3. Preparar array para el Modelo
        $datosParaGuardar = [
            'fecha_corte'         => $fecha_corte,
            'almacen_id'          => $target_save,
            'usuario_id'          => $usuario_id,
            'venta_bruta'         => $venta_bruta_calculada,

            'total_efectivo'      => $efectivoEnCaja,
            'total_transferencia' => $TransferencianCaja,
            'total_tarjeta'       => $TarjetaEnCaja,
            'abono_efectivo'      => floatval($_POST['abono_efectivo'] ?? 0),
            'abono_tarjeta'       => floatval($_POST['abono_tarjeta'] ?? 0),
            'abono_transferencia' => floatval($_POST['abono_transferencia'] ?? 0),
            'abonos_totales'      => $abonos_totales,
            'saldo_favor'         => $saldo_favor,
            'cobrado_total'       => $ingresos_dia,
            'gastos_totales'      => $gastos_totales,
            'compras_totales'     => $compras_totales,
            'gran_total_ingresos' => $gran_total_ingresos,
            'deuda_pendiente'     => $deuda_pendiente,
            'observaciones'       => $_POST['observaciones'] ?? 'Corte manual'
        ];

        $resultado = $modelo->agregarCorteManual($datosParaGuardar);

        if ($resultado['status'] === 'success') {
            $desglose_historial = [
                'efectivo'      => $efectivoEnCaja,
                'tarjeta'       => $TarjetaEnCaja,
                'transferencia' => $TransferencianCaja
            ];
            $modelo->registrarAperturaDesdeCierre($target_save, $usuario_id, $desglose_historial, $fecha_corte);
        }

        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- LÓGICA DE CONSULTA AJAX (GET) ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    try {

        
        $almacen_id_req = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;
$periodo  = $_GET['periodo'] ?? 'hoy';
$f_inicio = $_GET['f_inicio'] ?? '';
$f_fin    = $_GET['f_fin'] ?? '';

switch ($periodo) {

    case 'hoy':
        $f_inicio = $f_fin = date('Y-m-d');
        break;

    case 'ayer':
        $f_inicio = $f_fin = date('Y-m-d', strtotime('-1 day'));
        break;

    case 'semana':
        // 🔥 Lunes → hoy
        $f_inicio = date('Y-m-d', strtotime('monday this week'));
        $f_fin    = date('Y-m-d');
        break;

    case 'mes':
        // 🔥 Inicio de mes → hoy
        $f_inicio = date('Y-m-01');
        $f_fin    = date('Y-m-d');
        break;

    case 'personalizado':
        // usa lo que venga
        if (empty($f_inicio) || empty($f_fin)) {
            throw new Exception("Fechas requeridas");
        }
        break;

    default:
        $f_inicio = $f_fin = date('Y-m-d');
}
        $target = ($almacen_sesion != 0) ? $almacen_sesion : $almacen_id_req;

        $esUnSoloDia = ($f_inicio === $f_fin);

        $detalles = $modelo->obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $target);
        $totales  = $modelo->obtenerSumasCorte($periodo, $f_inicio, $f_fin, $target);

        // 🔥 DEBUG REAL
        $comprasTotales = $egresoModel->obtenerSumaEgresos($f_inicio, $f_fin, $target, 'compra');
        $gastosTotales  = $egresoModel->obtenerSumaEgresos($f_inicio, $f_fin, $target, 'gasto');
        $egresos = $egresoModel->obtenerTodosLosEgresosFiltros(
            $f_inicio,
            $f_fin,
            $target,
            'todos'
        );

        $compras = [];
        $gastos  = [];

        foreach ($egresos as $e) {
            if (($e['tipo'] ?? '') === 'compra'|| $e['tipo'] == 'pago_deuda') {
                $compras[] = $e;
            } else {
                $gastos[] = $e;
            }
        }

        
         $gastosTotaleM  = $egresoModel->obtenerGastosPorMetodo($f_inicio, $f_fin, $target);
         $comprasTotaleM  = $egresoModel->obtenerComprasPorMetodo($f_inicio, $f_fin, $target);

        

        // 🔥 FORZAR VALOR CORRECTO
        $comprasTotales = is_array($comprasTotales) ? ($comprasTotales['total'] ?? 0) : $comprasTotales;
        $gastosTotales  = is_array($gastosTotales) ? ($gastosTotales['total'] ?? 0) : $gastosTotales;

        $comprasTotales = floatval($comprasTotales);
        $gastosTotales  = floatval($gastosTotales);

        $saldo_data = null;
        if ($esUnSoloDia) {
            $saldo_data = $modelo->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);
        }

     echo json_encode([
    'status'          => 'success',
    'detalles'        => $detalles,
    'totales'         => $totales,
    'saldo_inicial'   => $saldo_data,
    'es_lista'        => ($target == 0),
    'mostrar_saldo'   => $esUnSoloDia,

    'comprasTotales'  => $comprasTotales,
    'gastosTotales'   => $gastosTotales,

    // 🔥 AQUÍ EL FIX
    'gastos'          => $gastos,
    'compras'         => $compras,

    'gastosMetodo'    => $gastosTotaleM,
    'comprasMetodo'   => $comprasTotaleM
]);

    } catch (Exception $e) {

        // 🔥 AQUÍ VAS A VER EL ERROR REAL
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// --- CARGA INICIAL DE LA VISTA ---
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 
$paginaActual = 'finanzas admin';
require_once __DIR__ . '/../views/finanzasAdministrador_view.php';