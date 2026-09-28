<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventas_model.php'; 
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/categoriasModel.php';
require_once __DIR__ . '/../models/cajaRapidaModel.php'; 
require_once __DIR__ . '/../models/entregasModel.php';
require_once __DIR__ . '/../models/movimientosModel.php';
require_once __DIR__ . '/../models/vehiculos_model.php'; 
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/RepartosModel.php'; 
require_once __DIR__ . '/../models/almacen/productosModel.php';

protegerPagina('cajaRapida'); 
$paginaActual = 'Caja Rapida';
  $paginaTitulo='Caja_Rapida';

// Instancias
$modeloEntrega    = new EntregaModel($conexion);
$modeloMovimiento = new MovimientoModel($conexion);
$vehiculoM        = new VehiculoModel($conexion);
$trabajadorM      = new TrabajadorModel($conexion);
$repartoM         = new RepartoModel($conexion); // Sincronizado con el nombre del modelo
$productosModel = new ProductoModel($conexion);
// --- 1. LÓGICA DE PETICIONES AJAX (GET) ---
$action = $_GET['action'] ?? $_POST['action'] ?? '';


if ($action === 'get_recursos_sucursal') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    echo json_encode([
        "success"  => true,
        "unidades" => $vehiculoM->listarPorAlmacen($almacen_id),
        "choferes" => $trabajadorM->listarPorAlmacenEncargado($almacen_id)
    ]);
    exit;
}
// --- 2. LÓGICA DE GUARDADO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. LIMPIEZA TOTAL: Evita que cualquier Warning previo ensucie el JSON
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    // 2. LEER EL PAYLOAD
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if ($data) {
        try {
            // --- VALIDACIÓN DE INSTANCIAS ---
            // Asegúrate de que estas variables existan. Si no, lánzamos error controlado.
            if (!isset($modeloMovimiento) || !isset($modeloEntrega)) {
                 throw new Exception("Error interno: Los modelos de logística no están instanciados.");
            }

            // --- PASO 1: GUARDAR LA VENTA ---
            $resultado = cajaRapidaModel::guardarVentaRapida($conexion, $data);
            
            if ($resultado['status'] === 'success') {
                $ventaid = $resultado['id_venta']; 
                
                // --- PASO 2: OBTENER MOVIMIENTOS ---
                $listaMovs = $modeloMovimiento->obtenerIdMovimientoPorVenta($ventaid);

                if (!empty($listaMovs) && is_array($listaMovs)) {
                    
                    // Identificar al usuario (Blindaje de sesión)
                    $usuarioSistemaId = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? 0;

                    foreach ($listaMovs as $idMov) {
                        // --- PASO 3: LOGÍSTICA AUTOMÁTICA ---
                        try {
                            
                             $datosReparto = [
                    'movimiento_id'      => $idMov,
                    'vehiculo_id'        => 999,
                    'chofer_id'          => $data['chofer_id'] ?? 1,
                    'direccion_entrega'  => "Ventas Mostrador", 
                   
                    'folio_viaje'        => "EN_PAT" . date('ymd') . "-" . $idMov,
                    'usuario_sistema_id' => $usuarioSistemaId,
                    'observaciones'      => 'Entrega Directa en Patio (Despacho Masivo)'
                ];

                            $modeloEntrega->iniciarRepartoPatio($datosReparto);

                        } catch (Exception $e) {
                            error_log("Error en logística MovID {$idMov}: " . $e->getMessage());
                        }

                        // --- PASO 4: DESPACHO FÍSICO (LOTES) ---
                        $resDespacho = $modeloEntrega->procesarDespachoFisico($idMov);
                        if (!$resDespacho['success']) {
                            error_log("Error despacho lotes ID: " . $idMov . " - " . $resDespacho['message']);
                        }
                    }
                }

                $resultado['message'] = ($resultado['total_entregado'] < $resultado['total_pedido']) 
                    ? "⚠️ Venta {$resultado['folio']} parcial por falta de stock. Logística registrada." 
                    : "✅ Venta {$resultado['folio']} completada. Stock descontado y personal asignado.";
                
                $resultado['movimientos_ids'] = $listaMovs;
            }
            
            echo json_encode($resultado);
            exit;

        } catch (Exception $ex) {
            // Si algo falla, devolvemos un JSON de error en lugar de dejar que PHP muera
            echo json_encode(["status" => "error", "message" => $ex->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No se recibieron datos válidos."]);
        exit;
    }
}

// --- 3. LÓGICA DE CARGA DE VISTA ---
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

$categorias_res = CategoriasModel::listar($conexion);
$categorias = ($categorias_res) ? $categorias_res->fetch_all(MYSQLI_ASSOC) : [];

$productos_res = cajaRapidaModel::obtenerProductos($conexion, $almacen_usuario);
$productos = [];
if ($productos_res) {
    while($row = $productos_res->fetch_assoc()){ $productos[] = $row; }
}


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

$clientesModel = new ClientesModel($conexion);
$clientes_res = $clientesModel->listarTodosCF($almacen_usuario); 
$clientes = []; 
if ($clientes_res) {
    while($row = $clientes_res->fetch_assoc()){ $clientes[] = $row; }
}

include __DIR__ . '/../views/venta2.php';