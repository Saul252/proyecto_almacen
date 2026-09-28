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
require_once __DIR__ . '/../models/almacen/productosModel.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/ventas_model.php';

// Instanciamos el modelo una sola vez
$almacenesModel = new AlmacenModel($conexion);
$clientesModel = new ClientesModel($conexion);
$productosModel = new ProductoModel($conexion);
$cotizacionesModel= new cotizacionesModel($conexion);
$ventasModel= new VentasModel();



protegerPagina('cotizaciones'); 
$paginaActual = 'cotizaciones'; 
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin = ($_SESSION['rol_id'] == 1 || $almacen_usuario == 0);
   $rol = $_SESSION['rol_id'];
   $puede=false;

 if( $rol<3)
            {
                $puede=true;
            }


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

        // AQUÍ
        $idProducto = $producto['producto_id'];

        $producto['medidas_adicionales'] =
            $medidasPorProducto[$idProducto] ?? [];
    }

    unset($producto);

    echo json_encode([
        'success' => true,
        'data' => $productos
    ]);

    exit;
}
// --- CARGA DE VISTA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        
      
        // Nota: Verifica que sea listarTodo() o listarTodos() según tu ProductosModel
        $almacenes=$almacenesModel->getAlmacenes($almacen_usuario);
        $clientes=$clientesModel->listarTodos($almacen_usuario);
        
       

        $tituloPagina = "Solicitudes de Compra";
      
        require_once __DIR__ . '/../views/cotizaciones_view.php';
        
    } catch (Exception $e) {
        die("Error fatal: " . $e->getMessage());
    }
}
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {

        // LEER JSON
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            throw new Exception("No se recibieron datos.");
        }

        $almacen_id = intval($input['almacen_id'] ?? 0);

        if ($input['vendedor']<= 0) {
            throw new Exception("ID de vendedor no válido.");
        }
     
        $data = [
            'usuario_id' => intval($_SESSION['usuario_id']),
            'vendedor'=> intval($input['vendedor'] ?? $_SESSION['usuario_id']),
            'almacen_id' => $almacen_id,
            'cliente_id' => intval($input['cliente_id'] ?? 0),
             'totalCotizacion' => intval($input['totalCotizacion'] ?? 0)
        ];

        if ($data['cliente_id'] <= 0) {
            throw new Exception("Debe seleccionar un cliente.");
        }

        $items_post = $input['items'] ?? [];
        $items_procesados = [];

        foreach ($items_post as $item) {

            $id_producto = intval($item['producto_id']);

            $items_procesados[$id_producto] = [
                'cantidad'        => floatval($item['cantidad']),
                'unidad'          => floatval($item['unidad']),
                'tipo_precio'     => $item['tipoPrecio']??'minorista',
                'precio_unitario' => floatval($item['precioUnitario']),
                'subtotal'        => floatval($item['precio'])
            ];
        }

        if (empty($items_procesados)) {
            throw new Exception("No hay productos válidos.");
        }

        $resultado = $cotizacionesModel->crear($data, $items_procesados);

        if ($resultado === true) {

    echo json_encode([
        'status' => 'success',
        'message' => '¡Cotización guardada con éxito!'
    ]);

} else {

    throw new Exception($resultado ?: "Error al guardar cotización");
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
if (isset($_GET['action']) && $_GET['action'] === 'actualizar') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {

        // LEER JSON DESDE EL PAYLOAD ENVIADO POR AJAX
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            throw new Exception("No se recibieron datos para actualizar.");
        }

        $cotizacion_id = intval($input['cotizacion_id'] ?? 0);
        if ($cotizacion_id <= 0) {
            throw new Exception("ID de cotización no válido para actualizar.");
        }

        $almacen_id = intval($input['almacen_id'] ?? 0);
        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén no válido.");
        }

        $cliente_id = intval($input['cliente_id'] ?? 0); 
        $vendedor_id = intval($input['vendedor'] ?? 0);
        if ($cliente_id <= 0) {
            throw new Exception("Debe seleccionar un cliente.");
        }

        // Estructuramos la cabecera con el ID de la cotización a modificar
        $data = [
            'cotizacion_id'   => $cotizacion_id,
            'usuario_id'      => intval($_SESSION['usuario_id']),
            'almacen_id'      => $almacen_id,
            'cliente_id'      => $cliente_id,
            'vendedor_id'      => $vendedor_id,
            'totalCotizacion' => floatval($input['totalCotizacion'] ?? 0) // Cambiado a floatval por si lleva decimales
        ];

        $items_post = $input['items'] ?? [];
        $items_procesados = [];

        foreach ($items_post as $item) {

            $id_producto = intval($item['producto_id']);

            // Mantenemos el formato plano indexado numéricamente para que coincida con el foreach del modelo
            $items_procesados[] = [
                'producto_id'     => $id_producto,
                'cantidad'        => floatval($item['cantidad']),
                'unidad'          => intval($item['unidad']),
                'tipoPrecio'      => $item['tipoPrecio'] ?? 'minorista',
                'precioUnitario'  => floatval($item['precioUnitario']),
                'precio'          => floatval($item['precio'])
            ];
        }

        if (empty($items_procesados)) {
            throw new Exception("Debe incluir al menos un producto en la cotización.");
        }

        // Llamamos al nuevo método independiente del modelo
        $resultado = $cotizacionesModel->actualizar($data, $items_procesados);

        if ($resultado === true) {
            echo json_encode([
                'status' => 'success',
                'message' => '¡Cotización actualizada con éxito!'
            ]);
        } else {
            throw new Exception($resultado ?: "Error al actualizar la cotización.");
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
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = (int)($_GET['id'] ?? 0);

        $detalle = $cotizacionesModel->obtenerDetalle($id);

        if (!$detalle) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sin datos'
            ]);
            exit;
        }

       
      

        echo json_encode([
            'status' => 'success',
            'data' => $detalle
            
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'listarCotizaciones') {

    header('Content-Type: application/json; charset=utf-8');

    try {
$almacen = !empty($_GET['almacen']) ? (int)$_GET['almacen'] : 0;

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
    
if($puede==true)
    {
$vendedor = !empty($_GET['vendedor'])
    ? trim($_GET['vendedor'])
    : null;
    }
    else{
        $vendedor=$_SESSION['usuario_id'];
    }
$cotizaciones = $cotizacionesModel->listarPorFechas(
    $es_admin,
    $almacen,
    $fechaInicio,
    $fechaFin,
    $estado,
    $buscador,
    $vendedor
);

        echo json_encode([
            'status' => 'success',
            'data' => $cotizaciones
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
if (isset($_GET['action']) && $_GET['action'] === 'eliminar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");
        if ($cotizacionesModel->cancelarOrden($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Eliminado.']);
        } else {
            throw new Exception("Error al eliminar.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');
    if (ob_get_level()) ob_clean();

    try {

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !is_array($input)) {
            throw new Exception("Datos inválidos");
        }

        $id_usuario = $_SESSION['usuario_id'] ?? 1;
  
        // ============================
        // ITEMS DEL CARRITO
        // ============================
        $items = $input['data'] ?? [];

        if (empty($items)) {
            throw new Exception("No hay productos en la venta");
        }

        $primerItem = $items[0] ?? [];

        // ============================
        // NORMALIZAR ITEMS
        // ============================
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

        // ============================
        // CAMPOS GLOBALES (DEL PAYLOAD)
        // ============================
        $ventaData['descuento']         = floatval($input['descuento'] ?? 0);
        $ventaData['observaciones']     = $input['observaciones'] ?? '';
        $ventaData['monto_pagado']      = floatval($input['monto_pagado'] ?? 0);
        $ventaData['metodo_pago']       = $input['metodo_pago'] ?? 'Efectivo';
        $ventaData['referencia']        = $input['referencia'] ?? '';
        $ventaData['efectivoPagado']    = floatval($input['efectivoPagado'] ?? 0);
        $ventaData['monto_usado_favor'] = floatval($input['monto_usado_favor'] ?? 0);
        $ventaData['usar_saldo_favor']  = intval($input['usar_saldo_favor'] ?? 0);
 $ventaData['vendedor']  = intval($input['vendedor'] ?? 0);

        // ============================
        // PROCESAR
        // ============================
        $resultado = $ventasModel->procesarVentaDesdeCotizacion(
            $conexion,
            $ventaData,
            $id_usuario,
            
        );

        echo json_encode($resultado);
        $competado=$cotizacionesModel->completarC(intval($input['idCotizacion'] ?? 0));

    } catch (Exception $e) {

        error_log("CF_SYSTEM_LOG: ERROR: " . $e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {

//     header('Content-Type: application/json; charset=utf-8');

//     try {

//         $id = (int)($_GET['id'] ?? 0);

//         $detalle = $solicitudModel->obtenerDetalle($id);

//         if (!$detalle) {
//             echo json_encode([
//                 'status' => 'error',
//                 'message' => 'Sin datos'
//             ]);
//             exit;
//         }

//         $proveedor_id = $detalle[0]['proveedor_id'] ?? 0;

//         $deudas = $proveedorModel->ProveedorYDeudaSuma($proveedor_id);
//         $costo_total = $solicitudModel->obtenerCostoTotal($id);

//         echo json_encode([
//             'status' => 'success',
//             'data' => $detalle,
//             'deuda' => $deudas,
//             'costo' => $costo_total
//         ]);

//     } catch (Throwable $e) {

//         echo json_encode([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ]);
//     }

//     exit;
// }