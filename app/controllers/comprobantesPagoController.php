<?php
// 1. Reporte de errores para debug (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/almacen/productosModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/cotizacionesModel.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/comprobantesPagoModel.php';

// Instanciamos los modelos
$almacenesModel = new AlmacenModel($conexion);
$clientesModel = new ClientesModel($conexion);
$productosModel = new ProductoModel($conexion);
$cotizacionesModel = new cotizacionesModel($conexion);
$comprobantesPagoModel = new comprobantesPagoModel($conexion);
$ventasModel = new VentasModel();

protegerPagina('comprobantes'); 
$paginaActual = 'comprobantes'; 
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin = ($_SESSION['rol_id'] == 1 || $almacen_usuario == 0);

// =========================================================================
// 1. ACCIÓN: OBTENER PRODUCTOS
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerProductos') {
    header('Content-Type: application/json');

    $productos = $productosModel->obtenerTodosProductos($almacen_usuario);
    $medidasAdicionales = $productosModel->obtenerMedidas();

    $medidasPorProducto = [];
    foreach ($medidasAdicionales as $medida) {
        $producto_id = $medida['producto_id'];
        if (!isset($medidasPorProducto[$producto_id])) {
            $medidasPorProducto[$producto_id] = [];
        }
        $medidasPorProducto[$producto_id][] = $medida;
    }

    foreach ($productos as &$producto) {
        $idProducto = $producto['producto_id'];
        $producto['medidas_adicionales'] = $medidasPorProducto[$idProducto] ?? [];
    }
    unset($producto);

    echo json_encode([
        'success' => true,
        'data' => $productos
    ]);
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'listarComprobantes') {

    header('Content-Type: application/json; charset=utf-8');

    try {
$almacen = !empty($_GET['almacen']) ? (int)$_GET['almacen'] : 0;
$vendedor= !empty($_GET['vendedor']) ? (int)$_GET['vendedor'] : null;

$fechaInicio = !empty($_GET['fechaInicio'])
    ? $_GET['fechaInicio']
    : null;

$fechaFin = !empty($_GET['fechaFin'])
    ? $_GET['fechaFin']
    : null;

$estado = !empty($_GET['estado'])
    ? $_GET['estado']
    : null;

$buscador = !empty($_GET['buscador'])
    ? trim($_GET['buscador'])
    : null;
 $comprobantes = $comprobantesPagoModel->listarPorFechas(
       $es_admin,
    $almacen,
    $fechaInicio,
    $fechaFin,
    $estado,
    $buscador,$vendedor
);

        echo json_encode([
            'status' => 'success',
            'data' => $comprobantes
        ]);

    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// =========================================================================
// 2. ACCIÓN: GUARDAR DEPÓSITO
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean();

    header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            throw new Exception("No se recibieron datos.");
        }

        $almacen_id = intval($input['almacen_id'] ?? 0);
        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén no válido.");
        }

        $usuario_id = intval($_SESSION['usuario_id'] ?? 1); 
        $cliente_id = intval($input['cliente_id'] ?? 0);
        $monto = floatval($input['monto_depositado'] ?? 0);
        $referencia = $input['referencia'] ?? '';
        $fecha_deposito = $input['fecha'] ?? date('Y-m-d');
        $metodo=$input['metodo'] ?? 'efectivo';
        $numero_ventas=$input['numeroventa'] ?? '';

        $resultado = $comprobantesPagoModel->agregarDeposito($cliente_id, $monto, $usuario_id, $fecha_deposito, $referencia, $almacen_id,$metodo,$numero_ventas);

        if ($resultado > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => '¡Depósito guardado con éxito!',
                'id_comprobante' => $resultado
            ]);
        } else {
            throw new Exception("Error al guardar el depósito en la base de datos.");
        }
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================================
// 3. ACCIÓN: ACTUALIZAR COTIZACIÓN
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'actualizar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        
        // CORRECCIÓN 1: Manejar como String limpiando espacios extra, no como Entero
        
        
        if ($id <= 0) throw new Exception("ID no válido.");
        
        // Opcional: Validar que la recibido no vaya vacía si es obligatoria
         if ($comprobantesPagoModel->actualizar($id)) {
            // CORRECCIÓN 2: Mensaje correcto para la acción de actualizar
            echo json_encode(['status' => 'success', 'message' => 'Comprobante actualizado correctamente.']);
        } else {
            throw new Exception("Error al actualizar el comprobante en la base de datos.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}if (isset($_GET['action']) && $_GET['action'] === 'actualizarAplicado') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        $cantidadAplicada=$_POST['cantidadAplicada'] ?? 0;
        
        // CORRECCIÓN 1: Manejar como String limpiando espacios extra, no como Entero
        
        
        if ($id <= 0) throw new Exception("ID no válido.");
        
        // Opcional: Validar que la recibido no vaya vacía si es obligatoria
         if ($comprobantesPagoModel->actualizarAplicado($id,$cantidadAplicada)) {
            // CORRECCIÓN 2: Mensaje correcto para la acción de actualizar
            echo json_encode(['status' => 'success', 'message' => 'Comprobante actualizado correctamente.']);
        } else {
            throw new Exception("Error al actualizar el comprobante en la base de datos.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// =========================================================================
// 4. ACCIÓN: OBTENER DETALLE (CORREGIDO PARA TU JAVASCRIPT)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');

    try {
        $id = intval($_GET['id'] ?? 0);
        
        $detalle = $comprobantesPagoModel->obtenerDetalle($id);
        
        if (!$detalle) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron datos para el ID ' . $id
            ]);
            exit;
        }

        // Estructura adaptada al 'datos.status' y 'datos.data' que busca tu JS
        echo json_encode([
            'status' => 'success',
            'data'   => $detalle
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error en servidor: ' . $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================================
// 5. ACCIÓN: ELIMINAR / CANCELAR
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'eliminar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");
        if ($comprobantesPagoModel->cancelarOrden($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Eliminado.']);
        } else {
            throw new Exception("Error al eliminar.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// =========================================================================
// 6. ACCIÓN: PROCESAR VENTA (POST GENERAL)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (ob_get_level()) ob_clean();

    try {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !is_array($input)) {
            throw new Exception("Datos inválidos");
        }

        $id_usuario = $_SESSION['usuario_id'] ?? 1;
        $items = $input['data'] ?? [];

        if (empty($items)) {
            throw new Exception("No hay productos en la venta");
        }

        $ventaData = [];
        foreach ($items as $item) {
            $ventaData[] = [
                'producto_id'        => intval($item['producto_id'] ?? 0),
                'cantidad'           => floatval($item['cantidadR'] ?? 0),
                'entrega_hoy'        => floatval($item['entrega_hoy'] ?? 0),
                'precio_unitario'    => floatval($item['precio_unitario'] ?? 0),
                'subtotal'           => floatval($item['subtotal'] ?? 0),
                'almacen_id'         => intval($item['almacen_origen_id'] ?? 0),
                'almacen_origen_id'  => intval($item['almacen_origen_id'] ?? 0),
                'cliente_id'         => intval($item['cliente_id'] ?? 0),
                'usuario_id'         => $id_usuario,
                'unidadMedida'       => intval($item['unidadMedida'] ?? 0),
                'observaciones'      => $item['observaciones'] ?? '',
                'tipo_precio'        => $item['tipo_precio'] ?? '',
                'monto_pagado'       => floatval($item['monto_pagado'] ?? 0),
                'metodo_pago'        => $item['metodo_pago'] ?? 'Efectivo',
                'referencia'         => $item['referencia'] ?? '',
                'efectivoPagado'     => floatval($item['efectivoPagado'] ?? 0),
                'descuento'          => floatval($item['descuento'] ?? 0),
                'monto_usado_favor'  => floatval($item['monto_usado_favor'] ?? 0),
                'usar_saldo_favor'   => intval($item['usar_saldo_favor'] ?? 0),
                'total'              => floatval($item['total'] ?? 0)
            ];
        }

        $ventaData['descuento']         = floatval($input['descuento'] ?? 0);
        $ventaData['observaciones']     = $input['observaciones'] ?? '';
        $ventaData['monto_pagado']      = floatval($input['monto_pagado'] ?? 0);
        $ventaData['metodo_pago']       = $input['metodo_pago'] ?? 'Efectivo';
        $ventaData['referencia']        = $input['referencia'] ?? '';
        $ventaData['efectivoPagado']    = floatval($input['efectivoPagado'] ?? 0);
        $ventaData['monto_usado_favor'] = floatval($input['monto_usado_favor'] ?? 0);
        $ventaData['usar_saldo_favor']  = intval($input['usar_saldo_favor'] ?? 0);

        $resultado = $ventasModel->procesarVentaDesdeCotizacion($conexion, $ventaData, $id_usuario);

        echo json_encode($resultado);
        $cotizacionesModel->completarC(intval($input['idCotizacion'] ?? 0));

    } catch (Exception $e) {
        error_log("CF_SYSTEM_LOG: ERROR: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// =========================================================================
// 7. CARGA DE LA VISTA POR DEFECTO (Único bloque al final)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $almacen_id = intval($_SESSION['almacen_id'] ?? 1); 
    try {
         $almacenes = $almacenesModel->getAlmacenes($almacen_usuario);
        $clientes = $clientesModel->listarTodos($almacen_usuario);
        $rolAct= $_SESSION['rol_id'];

        $tituloPagina = "Comprobantes de pago";
        
        // El HTML se incluye ÚNICAMENTE aquí, cuando no se pide ninguna acción AJAX.
        require_once __DIR__ . '/../views/comprobantes_pago.php';
        
    } catch (Exception $e) {
        die("Error fatal: " . $e->getMessage());
    }
}