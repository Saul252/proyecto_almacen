<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas de archivos base
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mantenimientos_model.php'; // Necesario para el selector de admin

require_once __DIR__ . '/../models/egresos/insumosModel.php';
protegerPagina('mantenimientos'); 

$vehiculoModel = new VehiculoModel($conexion);
$mantenimientosModel = new MantenimientosModel($conexion);

$insumosModel = new InsumosModel($conexion);
$almacenModel = new AlmacenModel($conexion); // Instanciamos para obtener la lista de sucursales
$paginaActual = 'mantenimientos';
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin    = $_GET['fecha_fin'] ?? date('Y-m-t');
$almacenSesion = intval($_SESSION['almacen_id'] ?? 0);
 $almacenes = $almacenModel->getAlmacenes($almacenSesion); // Para el select del modal
  $tipo=$almacenSesion==0?1:0;
       
if (isset($_GET['action'])) {
    if (ob_get_level()) ob_clean(); // Limpiar basura de salida
    header('Content-Type: application/json');
    
    try {
        switch ($_GET['action']) {
         case 'getEstadoCuentaCliente':

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {


        // 🆕 FILTROS DE FECHA (NUEVO)
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin    = $_GET['fecha_fin'] ?? null;
         $almacen    = $_GET['almacen'] ?? null;

        // VALIDACIÓN FLEXIBLE
        if ($fecha_inicio && !strtotime($fecha_inicio)) {
            throw new Exception("Fecha inicio inválida.");
        }

        if ($fecha_fin && !strtotime($fecha_fin)) {
            throw new Exception("Fecha fin inválida.");
        }


        // 🆕 EXPEDIENTE AHORA FILTRABLE
        $expediente = $insumosModel->obtenerExpedienteCompletoFecha(
            
            $fecha_inicio,
            $fecha_fin,$almacen
        );


        
        echo json_encode([
            'status' => 'success',
            
            
            'expediente' => $expediente,
           
            'filtros' => [
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]
        ]);

    } catch (Throwable $e) {

        error_log("ERROR getEstadoCuentaCliente: " . $e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;  case 'getInsumo':

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {

        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("Id inválido.");
        }

        $entregaInsumo = $insumosModel->obtenerEntregaInsumo($id);

        echo json_encode([
            'status' => 'success',
            'entregaInsumo' => $entregaInsumo
        ]);

    } catch (Throwable $e) {

        error_log("ERROR getInsumo: " . $e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
        
  case 'guardarAbono':
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    $conexion->begin_transaction();

    try {
        // --- 0. CAPTURA DE DATOS ---
        // Usamos floatval y intval para asegurar limpieza de datos
        $v_id = intval($_POST['venta_id'] ?? 0);
        $amt  = floatval($_POST['monto'] ?? 0); // Este es el monto que el usuario tecleó
        $met  = $_POST['metodo_pago'] ?? 'Efectivo'; 
        $u_id = $_SESSION['usuario_id'] ?? 1;
        $fec  = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d H:i:s');
        $referencia=$_POST['referencia'] ?? '';
        $c_id = intval($_POST['cliente_id'] ?? 0);

        // --- 1. VALIDACIÓN ---
        if ($amt <= 0) throw new Exception("El monto del abono debe ser mayor a 0.");
        
        // Si no viene el cliente, lo buscamos
        if (!$c_id && $v_id > 0) {
            $c_id = $ventasModel->obtenerClientePorVenta($conexion, $v_id);
        }
        if (!$c_id) throw new Exception("No se halló cliente para procesar el abono.");

        // --- 2. LÓGICA DE SALDOS QUIRÚRGICA ---
        
        if ($met === 'Saldo a Favor') {
            /**
             * ESCENARIO A: EL CLIENTE PAGA USANDO SU "AHORRO"
             * 1. Restamos de su bolsa de FAVOR (Monto negativo)
             * 2. Restamos de su bolsa de CONTRA (Monto negativo)
             */
            
            // Restar del "Ahorro" (Bolsa Favor)
            $clientesModel->agregar_saldo_a_favor($c_id, ($amt * -1), $v_id, $fec);
            
            // Restar de la "Deuda" (Bolsa Contra)
            $clientesModel->agregar_saldo_en_contra($c_id, ($amt * -1), $v_id, $fec);

            // Log específico para rastreo
            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, 'USO_SALDO_A_FAVOR', $fec);
            error_log("CF_SYSTEM_LOG: Abono con Saldo a Favor. Cliente: $c_id, Monto: $amt");

        } else {
            /**
             * ESCENARIO B: ABONO NORMAL (Efectivo, Transferencia, etc.)
             * Solo restamos de la bolsa de CONTRA (Deuda). El saldo a favor NO se toca.
             */
            
            $clientesModel->agregar_saldo_en_contra($c_id, ($amt * -1), $v_id, $fec);

            // Log del abono según el método (Ej: ABONO_Efectivo)
            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, "ABONO_" . str_replace(' ', '_', $met), $fec);
            error_log("CF_SYSTEM_LOG: Abono normal ($met). Cliente: $c_id, Monto: $amt");
        }

        // --- 3. REGISTRO EN HISTORIAL Y ACTUALIZACIÓN DE NOTA ---
        
        // Registrar en la tabla de pagos/abonos de la venta
        if (!$ventasModel->registrarAbono($v_id, $amt, $u_id, $met, $fec,$referencia)) {
            throw new Exception("Error al registrar el movimiento en el historial de pagos.");
        }

        
        // --- 4. ÉXITO ---
        $conexion->commit();

        echo json_encode([
            'status'   => 'success', 
            'message'  => 'Abono procesado correctamente.',
            'detalles' => [
                'monto'  => number_format($amt, 2),
                'metodo' => $met,
                'cliente' => $c_id
            ]
        ]);

    } catch (Throwable $e) {
        if (isset($conexion)) $conexion->rollback();
        error_log("CF_SYSTEM_LOG: ERROR CRÍTICO EN ABONO: " . $e->getMessage());
        echo json_encode([
            'status'  => 'error',
            'message' => "Error al procesar: " . $e->getMessage()
        ]);
    }
    exit;
    break;
        }
    } catch (Exception $e) {
        error_log("Error AJAX CF System: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// -

$expediente = $insumosModel->obtenerExpedienteCompletoFecha(
            $fecha_inicio,
            $fecha_fin);

require_once __DIR__ . '/../views/mantenimientos/expedienteDetalle_view.php';