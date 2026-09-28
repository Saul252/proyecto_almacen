<?php
/**
 * Cf System - Controlador de Tesorería
 * Maneja el flujo de capital, bancos y caja fuerte.
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/tesoreriaModel.php';
require_once __DIR__ . '/../models/corteCajaModel.php';

// Verificación de acceso
protegerPagina('tesoreria');
date_default_timezone_set('America/Mexico_City');

/** * Detectar variable de conexión. 
 * El modelo usa MySQLi según la función que proporcionaste.
 */
$db_conn = $conexion ?? $db; 

$tesoreria = new tesoreriaModel($db_conn);
$corteCaja= new CorteCajaModel($db_conn);
$almacenModel = new AlmacenModel($db_conn);

// Identificar la acción solicitada
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- BLOQUE 1: PROCESAMIENTO DE PETICIONES AJAX ---
if (!empty($action)) {
    header('Content-Type: application/json');
    
    // Limpiar buffers para asegurar salida JSON limpia
    if (ob_get_length()) ob_clean(); 

    try {
        switch ($action) {
           case 'registrar':
    // 1. Recolección de datos básicos del POST
    $categoria_id   = intval($_POST['categoria_id'] ?? 0);
    $almacen_id     = intval($_POST['almacen_id'] ?? 0);
    $usuario_id     = intval($_SESSION['usuario_id'] ?? 0);
    $monto_input    = floatval($_POST['monto'] ?? 0);
    $concepto       = trim($_POST['conceptos'] ?? '');
    $metodo         = $_POST['metodo_pago'] ?? 'efectivo';
    $fecha_sel      = $_POST['fecha_movimiento'] ?? date('Y-m-d');
    
    // Destinos opcionales
    $almacen_destino_id = intval($_POST['almacen_destino_id'] ?? 0);
    $caja_fuerte_id     = intval($_POST['caja_fuerte_id'] ?? 0);
    $banco_id           = intval($_POST['banco_id'] ?? 0);

    // 2. OBTENER TIPO DE OPERACIÓN (Crucial para los cálculos siguientes)
    $sql_cat = "SELECT tipo_operacion FROM capital_categorias WHERE id = ?";
    $stmt_c = $db_conn->prepare($sql_cat);
    $stmt_c->bind_param("i", $categoria_id);
    $stmt_c->execute();
    $cat_info = $stmt_c->get_result()->fetch_assoc();
    $tipo_op = $cat_info['tipo_operacion'] ?? 'entrada';

    // 3. REVISIÓN DE SALDOS ACTUALES
    $saldos_actuales = $corteCaja->obtenerSaldoInicialMonitor($almacen_id, '2000-01-01', $fecha_sel);

    // 4. LÓGICA DE CÁLCULO DE MOVIMIENTO
    $operador = ($tipo_op === 'salida' || $tipo_op === 'traspaso') ? -1 : 1;
    $cambio = $monto_input * $operador;

    // 5. CALCULAR NUEVO DESGLOSE
    $nuevo_desglose = [
        'efectivo'       => $saldos_actuales['monto_efectivo'],
        'tarjeta'        => $saldos_actuales['monto_tarjeta'],
        'transferencia'  => $saldos_actuales['monto_transferencia']
    ];

    // Aplicamos el cambio al método correspondiente si existe en el array
    if (array_key_exists($metodo, $nuevo_desglose)) {
        $nuevo_desglose[$metodo] += $cambio;
    }

    // 6. PREPARACIÓN DE DATA (Ahora que ya tenemos $tipo_op y $nuevo_desglose)
    $data = [
        'almacen_id'         => $almacen_id,
        'usuario_id'         => $usuario_id,
        'categoria_id'       => $categoria_id,
        'monto'              => $monto_input,
        'metodo_pago'        => $metodo,
        'fecha_movimiento'   => $fecha_sel, // Usamos la fecha seleccionada
        'concepto'           => $concepto,
        'tipo_operacion'     => $tipo_op,
        'monto_efectivo'     => $nuevo_desglose['efectivo'],
        'monto_tarjeta'      => $nuevo_desglose['tarjeta'],
        'monto_transferencia'=> $nuevo_desglose['transferencia'],
        'almacen_destino_id' => $almacen_destino_id ?: null,
        'caja_fuerte_id'     => $caja_fuerte_id ?: null,
        'banco_id'           => $banco_id ?: null
    ];

    // 7. GUARDAR EL REGISTRO
    // Nota: Asegúrate que $fecha_param esté definida o usa $fecha_sel
    $res = $corteCaja->registrarAperturaDesdeCierreConcepto($data );

    echo json_encode([
        "status" => $res ? "success" : "error", 
        "message" => $res ? "Movimiento de $tipo_op registrado correctamente" : "Error al guardar en el historial"
    ]);
    break;

            case 'listar':
                $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) :  $_SESSION['almacen_id'];
                
                // Normalización de fechas para el rango completo del día
                $f_inicio = $_GET['f_inicio'] ?? $_GET['fecha'] ?? date('Y-m-d');
                $f_fin    = $_GET['f_fin']    ?? $_GET['fecha'] ?? date('Y-m-d');

                // 1. Obtener movimientos del historial (Detalle de la tabla)
               

                // 2. Obtener Saldo Inicial (Apertura) con tu función personalizada
                $movimientos = $corteCaja->obtenerSaldoInicialMonitor($almacen_id, $f_inicio, $f_fin);
                 $movimientosHistorial = $corteCaja->obtenerSaldoInicialMonitorTabla($almacen_id, $f_inicio, $f_fin);

                echo json_encode([
                    'status'        => 'success',
                    'data'          => $movimientos,
                    'movimientosHistorial'=> $movimientosHistorial,
                    'saldo_inicial' => $saldo_inicial,
                    'es_lista'      => ($almacen_id == 0)
                ]);
                break;
case 'obtener_saldos_sucursal':
    $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;
    $fecha_sel  = $_GET['fecha'] ?? date('Y-m-d');

    // Usamos tu función existente para traer la "foto" de los saldos a esa fecha
    // Ponemos una fecha de inicio muy antigua para asegurar que traiga el acumulado
    $saldos = $corteCaja->obtenerSaldoInicialMonitor($almacen_id, $f_inicio, $fecha_sel);

    echo json_encode([
        'status' => 'success',
        'saldos' => $saldos
    ]);
    break;
            case 'catalogos_modal':
    // Obtenemos el ID del almacén seleccionado en el modal (0 si es global/admin)
    $almacen_id = 0;

    // Carga de selects para el modal en una sola respuesta
    echo json_encode([
        "status"        => "success",
        "categorias"    => $tesoreria->getCategorias(), // Las categorías suelen ser globales
        "cajas_fuertes" => $tesoreria->getCajasFuertes($almacen_id),
        "bancos"        => $tesoreria->getCuentasBancarias($almacen_id)
    ]);
    break;

            case 'cancelar':
                $id = intval($_POST['id']);
                $user_id = intval($_SESSION['usuario_id'] ?? 0);
                $res = $tesoreria->cancelarMovimiento($id, $user_id);
                echo json_encode(["status" => $res ? "success" : "error"]);
                break;

            default:
                echo json_encode(["status" => "error", "message" => "Acción no definida"]);
                break;
        }
    } catch (Exception $e) {
        // Captura de errores graves para evitar el Error 500 en blanco
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit; // Terminar ejecución para no enviar el HTML de la vista
}

// --- BLOQUE 2: CARGA INICIAL DE LA VISTA ---

// Configuración de variables para el Layout y la Vista
$categoriasCapital= $tesoreria->getCategorias();
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 
$saldoCajas=$corteCaja->saldoCajaFuerte($almacen_sesion);
$saldosCuentasBancarias=$corteCaja->saldoCuentasBancarias($almacen_sesion);
$paginaActual   = 'tesoreria';

// Carga de la vista HTML
require_once __DIR__ . '/../views/tesoreria_view.php';