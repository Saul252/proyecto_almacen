<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/categoriasModel.php';

require_once __DIR__ . '/../models/almacen/productosModel.php';

// Instanciamos el modelo una sola vez
$clientesModel = new ClientesModel($conexion);

$productosModel = new ProductoModel($conexion);

// --- ACCIONES POST (Guardar Venta) ---
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// ... (Tus require_once y la instancia de $clientesModel se quedan igual arriba) ...
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['accion'])) {
    header('Content-Type: application/json');
    if (ob_get_level()) ob_clean(); 

    try {
        $id_usuario = $_SESSION['usuario_id'] ?? 1;

        if ($data['accion'] === 'guardar_venta') {
            error_log("CF_SYSTEM_LOG: Iniciando guardado de venta");
            
            // 1. CAPTURA DE VALORES
            $usar_check  = isset($data['usar_saldo_favor']) ? intval($data['usar_saldo_favor']) : 0;
            $monto_favor = isset($data['monto_usado_favor']) ? floatval($data['monto_usado_favor']) : 0;
            $efectivo    = floatval($data['monto_pagado'] ?? 0); 
            $total_nota  = floatval($data['total_venta'] ?? 0); 

            // 2. CONSOLIDACIÓN PARA EL MODELO DE VENTAS
            if ($usar_check === 1 && $monto_favor > 0) {
                $data['monto_pagado'] = $efectivo + $monto_favor;
            } else {
                $data['monto_pagado'] = $efectivo;
                $monto_favor = 0; 
            }

            error_log("CF_SYSTEM_LOG: Pago total reportado: " . $data['monto_pagado']);

            // 3. PROCESAR LA VENTA
            $resultado = VentasModel::procesarVenta($conexion, $data, $id_usuario);
            
            if ($resultado['status'] === 'success') {
                
                $id_venta     = $resultado['id_venta'] ?? 0;
                $id_cliente   = intval($data['id_cliente'] ?? 0);
                $fecha_actual = date('Y-m-d H:i:s');

                // --- ACCIÓN A: RESTAR DEL FAVOR (Bolsa Ahorro) ---
                if ($usar_check === 1 && $monto_favor > 0) {
                    $clientesModel->agregar_saldo_a_favor($id_cliente, ($monto_favor * -1), $id_venta, $fecha_actual);
                    $clientesModel->abono_saldos_log($id_cliente, $id_venta, $monto_favor, $id_usuario, 'USO_SALDO_A_FAVOR', $fecha_actual);
                    error_log("CF_SYSTEM_LOG: Saldo a favor restado: {$monto_favor}");
                }

                // --- ACCIÓN B: AUMENTAR DEUDA (Bolsa Contra) ---
                $pago_total_entregado = $efectivo + ($usar_check === 1 ? $monto_favor : 0);

                if ($pago_total_entregado < $total_nota) {
                    $falta_pagar = $total_nota - $pago_total_entregado;
                    $clientesModel->agregar_saldo_en_contra($id_cliente, $falta_pagar, $id_venta, $fecha_actual);
                    $clientesModel->abono_saldos_log($id_cliente, $id_venta, $falta_pagar, $id_usuario, 'CARGO_DEUDA_VENTA', $fecha_actual);
                    error_log("CF_SYSTEM_LOG: Deuda generada: {$falta_pagar}");
                }

            }
            VentasModel::actualizarEntregasCompletas(
    $conexion,
    $resultado['id_venta']
);

            echo json_encode($resultado);
        }
    } catch (Exception $e) {
        error_log("CF_SYSTEM_LOG: ERROR CRÍTICO: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// ... (Tus require_once y la instancia de $clientesModel se quedan igual arriba) ...
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['accion'])) {
    header('Content-Type: application/json');
    if (ob_get_level()) ob_clean(); 

    try {
        $id_usuario = $_SESSION['usuario_id'] ?? 1;

        if ($data['accion'] === 'agregarDeposito') {
            error_log("CF_SYSTEM_LOG: Iniciando guardado de deposito");
            
            // 1. CAPTURA DE VALORES CORREGIDA
            // Primero evaluamos si la clave existe en $data usando el operador de fusión de nulos (??) 
            // y después aplicamos la conversión numérica.
            $id_cliente = intval($data['id_cliente'] ?? 0);
            $monto      = floatval($data['monto'] ?? 0);

            // 2. CONSOLIDACIÓN PARA EL MODELO DE VENTAS
            error_log("CF_SYSTEM_LOG: Pago total reportado: " . $monto);

            // 3. PROCESAR LA VENTA / DEPÓSITO
            $resultado = VentasModel::agregarDeposito($conexion, $id_cliente, $monto, $id_usuario);
            
            // 4. RETORNAR RESPUESTA
            // Si tu modelo devuelve un array con ['status' => 'success'], pasará directo.
            // Si solo devuelve el ID numérico, podrías envolverlo en un json_encode(['status' => 'success', 'id' => $resultado]);
            echo json_encode($resultado);
        }
    } catch (Exception $e) {
        error_log("CF_SYSTEM_LOG: ERROR CRÍTICO: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIONES GET (Estado de Cuenta) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerEstadoCuenta') {
    header('Content-Type: application/json');
    if (ob_get_level()) ob_clean();
    
    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID de cliente no válido.");

        // Obtenemos datos del modelo
        $resumen = ClientesModel::obtenerSaldoActual($conexion, $id);
        $historial_res = ClientesModel::obtenerHistorialLog($conexion, $id);
        
        $movimientos = [];
        if ($historial_res) {
            while ($row = $historial_res->fetch_assoc()) {
                $movimientos[] = $row;
            }
        }

        echo json_encode([
            'success' => true,
            'resumen' => $resumen,
            'movimientos' => $movimientos
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;


    }






    
if (isset($_GET['action']) && $_GET['action'] === 'obtenerEstatusCliente') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception("ID de cliente no válido.");
        }

        // 1. Obtenemos los datos directamente (ya vienen como array desde el modelo)
        $datos = ClientesModel::obtenerEstatus($conexion, $id);

        if (!$datos) {
            throw new Exception("No se encontraron registros de saldo para este cliente.");
        }

        // 2. Mapeamos los datos para que el JS los reciba sin romperse
        // Usamos los nombres de columna reales de la tabla clientes_saldos
        echo json_encode([
            'success'            => true,
            'id'                 => $id,
            'nombre_comercial'   => $datos['nombre_comercial'] ?? 'Cliente',
            'saldo_neto'         => floatval($datos['saldo_neto'] ?? 0),
            'saldo_en_contra'    => floatval($datos['saldo_en_contra'] ?? 0),
            'saldo_a_favor'      => floatval($datos['saldo_a_favor'] ?? 0),
            'estatus_financiero' => $datos['estatus_financiero'] ?? 'AL DIA',
            // Mantenemos estos por compatibilidad si otros scripts los usan:
            'resumen' => [
                'saldo_total' => floatval($datos['saldo_neto'] ?? 0),
                'condicion'   => $datos['estatus_financiero'] ?? 'AL DIA'
            ]
        ]);

    } catch (Throwable $e) {
        error_log("Error en obtenerEstatusCliente: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'listarProductos') {

    header('Content-Type: application/json; charset=utf-8');

    try {
$almacen = !empty($_GET['almacen']) ? (int)$_GET['almacen'] : 0;


$categoria = !empty($_GET['categoria'])
    ? trim($_GET['categoria'])
    : null;

$productos_res = VentasModel::obtenerProductosFiltrados($conexion,$almacen, $categoria);
$productos = ($productos_res) ? $productos_res->fetch_all(MYSQLI_ASSOC) : [];


$medidasAdicionales = $productosModel->obtenerMedidas();


// ========================================
// AGRUPAR MEDIDAS POR PRODUCTO
// ========================================

$medidasPorProducto = [];

foreach ($medidasAdicionales as $medida) {

    $producto_id = $medida['producto_id'];

    if (!isset($medidasPorProducto[$producto_id])) {

        $medidasPorProducto[$producto_id] = [];
    }

    $medidasPorProducto[$producto_id][] = $medida;
}


// ========================================
// FUSIONAR MEDIDAS A PRODUCTOS
// ========================================

foreach ($productos as &$producto) {

    $idProducto = $producto['id'];

    $producto['medidas_adicionales'] =
        $medidasPorProducto[$idProducto] ?? [];
}

unset($producto);

    


        echo json_encode([
            'status' => 'success',
            'data' => $productos
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
// --- CARGA DE VISTA ---
protegerPagina('ventas'); 
$paginaActual = 'ventas';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

// Almacenes
$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

// Categorías
$categorias_res = CategoriasModel::listar($conexion);
$categorias = ($categorias_res) ? $categorias_res->fetch_all(MYSQLI_ASSOC) : [];

// Productos

// Clientes (Asegúrate de que listarTodos traiga rfc, razon_social, regimen_fiscal)
$clientes_res = $clientesModel->listarTodosCF($almacen_usuario); 
$clientes = ($clientes_res) ? $clientes_res->fetch_all(MYSQLI_ASSOC) : [];

include __DIR__ . '/../views/requisiciones_view.php';