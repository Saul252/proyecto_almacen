<?php
// 1. Reporte de errores para debug (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// 2. Carga de Modelos
require_once __DIR__ . '/../models/solicitudCompraModel.php'; 
require_once __DIR__ . '/../models/productosModel.php';
require_once __DIR__ . '/../models/proveedoresModel.php';
require_once __DIR__ . '/../models/almacen_model.php'; 
require_once __DIR__ . '/../models/egresos/comprasModel.php';

require_once __DIR__ . '/../models/egresos_model.php';


protegerPagina('solicitudesCompra'); 

$solicitudModel = new SolicitudCompra($conexion);
$productosModel = new ProductosModel($conexion);
$almacenModel   = new AlmacenModel($conexion);
$egresoModel   = new EgresoModel($conexion);
$proveedorModel = new ProveedoresModel($conexion);
$comprasModel = new CompraModel($conexion);
$paginaActual = 'solicitudesCompra'; 
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin = ($_SESSION['rol_id'] == 1 || $almacen_usuario == 0);

// --- ACCIÓN: GUARDAR (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {

        // 🔹 Validación de almacén
        $almacen_id = $es_admin 
            ? intval($_POST['almacen_id'] ?? 0) 
            : intval($almacen_usuario);

        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén no válido.");
        }

        // 🔹 Cabecera
        $data = [
            'usuario_id'   => intval($_SESSION['usuario_id']),
            'almacen_id'   => $almacen_id,
            'proveedor_id' => intval($_POST['proveedor_id'] ?? 0)
        ];

        if ($data['proveedor_id'] <= 0) {
            throw new Exception("Debe seleccionar un proveedor.");
        }

        // 🔹 Procesar items
        $items_post = $_POST['items'] ?? [];
        $items_procesados = [];

        foreach ($items_post as $id_producto => $campos) {

            $id_producto = intval($id_producto);

            $cant   = floatval($campos['cant'] ?? 0);
            $factor = floatval($campos['unidad'] ?? 1);

            // 🔥 CORRECCIÓN CLAVE
            $costo  = floatval($campos['precio'] ?? 0);

            $total_base = $cant * $factor;

            // 🔥 permitir guardar aunque uno sea 0
            if ($total_base > 0 || $costo > 0) {

                $items_procesados[$id_producto] = [
                    'cantidad' => $total_base,
                    'costo'    => $costo
                ];
            }
        }

        if (empty($items_procesados)) {
            throw new Exception("No hay productos válidos en la lista.");
        }

        // 🔹 Guardar
        $resultado = $solicitudModel->crear($data, $items_procesados);

        if ($resultado >0 ) {
            echo json_encode([
                'status' => 'success',
                'message' => '¡Solicitud guardada con éxito!',
                'id'=>$resultado
            ]);
        } else {
            throw new Exception($resultado ?: "Error en la base de datos.");
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

// --- ACCIÓN: ELIMINAR --- (Sin cambios, está correcta)
if (isset($_GET['action']) && $_GET['action'] === 'eliminar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");
        if ($solicitudModel->cancelarOrden($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Eliminado.']);
        } else {
            throw new Exception("Error al eliminar.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'getSiguienteFolio') {
    $almacen = ($_GET['almacen_id'] ?? 0);
    header('Content-Type: application/json');
    $siguiente = $comprasModel->generarSiguienteFolio($almacen);
    
    echo json_encode(['success' => true, 'folio' => $siguiente]);
    exit;
}
// --- CARGA DE VISTA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        // Nota: Verifica que sea listarTodo() o listarTodos() según tu ProductosModel
        $productos   = $productosModel->listarTodo(); 
        $proveedores = $proveedorModel->listarTodos();
          $listaProductos= $productosModel->listarTodo();
        $almacenes   = $almacenModel->getAlmacenes($almacen_usuario); 
        $unidadesMedida= $almacenModel->getUnidadesMedida();

        $tituloPagina = "Solicitudes de Compra";
      
        require_once __DIR__ . '/../views/solicitudesCompra_view.php';
        
    } catch (Exception $e) {
        die("Error fatal: " . $e->getMessage());
    }
}
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = (int)($_GET['id'] ?? 0);

        $detalle = $solicitudModel->obtenerDetalle($id);

        if (!$detalle) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sin datos'
            ]);
            exit;
        }

        $proveedor_id = $detalle[0]['proveedor_id'] ?? 0;

        $deudas = $proveedorModel->ProveedorYDeudaSuma($proveedor_id);
        $costo_total = $solicitudModel->obtenerCostoTotal($id);

        echo json_encode([
            'status' => 'success',
            'data' => $detalle,
            'deuda' => $deudas,
            'costo' => $costo_total
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'listarSolicitudes') {

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

 $solicitudes = $solicitudModel->listarPorFechas($es_admin,
       
    
    $almacen,
    $fechaInicio,
    $fechaFin,
    $estado,
    $buscador
);

        echo json_encode([
            'status' => 'success',
            'data' => $solicitudes
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
if (isset($_POST['action']) && $_POST['action'] === 'guardarCompraCompleta') {

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {

        // 🔥 VALIDAR EXISTENCIA
        if (!isset($_POST['items'])) {
            throw new Exception("No se recibieron productos.");
        }

        // 🔥 DECODIFICAR JSON
        $items = json_decode($_POST['items'], true);

        // 🔥 VALIDAR JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(
                "Error en JSON items: " . json_last_error_msg()
            );
        }

        // 🔥 VALIDAR ARRAY
        if (!is_array($items) || empty($items)) {
            throw new Exception("El arreglo de productos está vacío.");
        }

        $almacen_id = intval($_POST['almacen_id'] ?? 0);

        $solicitud_id = intval($_POST['solicitud_id'] ?? 0);

        $metodo_pago = trim(
            $_POST['metodo_pago'] ?? 'Efectivo'
        );

        // 🔥 PROVEEDOR
        $proveedorNombre = trim(
            $_POST['proveedor'] ?? ''
        );

        $proveedorData = $proveedorModel
            ->obtenerProveedorPorNombre($proveedorNombre);

        $proveedor = intval(
            $proveedorData['id'] ?? 0
        );

        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén inválido.");
        }

        if ($proveedor <= 0) {
            throw new Exception("Proveedor inválido.");
        }

        // 🔥 DEBUG OPCIONAL
        // file_put_contents(
        //     'debug_items.txt',
        //     print_r($items, true)
        // );

        // =====================================================
        // 🔥 GUARDAR COMPRA COMPLETA
        // =====================================================

        $resultado = $comprasModel->guardarCompraCompleta(

            $items,

            $_POST['folio'] ?? '',

            $proveedor,

            $_FILES['evidencia_compra'] ?? null,

            $almacen_id,

            $_SESSION['usuario_id'] ?? 0,

            $metodo_pago

        );

        // 🔥 VALIDAR RESPUESTA DEL MODELO
        if (
            !$resultado ||
            !isset($resultado['success'])
        ) {
            throw new Exception(
                "Respuesta inválida del modelo de compras."
            );
        }

        // =====================================================
        // 🔥 ACTUALIZAR SOLICITUD
        // =====================================================

        
if (
    $resultado['success'] === true &&
    $solicitud_id > 0
) {
    $id_generado = intval($resultado['compra_id'] ?? 0);

    $id_generado = intval($_POST['folio'] ?? 0);

    // 🔥 SOLO ACTUALIZAR SI EXISTE COMPRA VÁLIDA
    if ($id_generado > 0) {

        $solicitudModel->actualizarEstado(
            $solicitud_id,
            $almacen_id,
            'recibido',
            $id_generado??'0'
        );

        $resultado['message'] .=
            " (Solicitud #{$solicitud_id} completada)";
    }
}

        // =====================================================
        // 🔥 PAGO DE DEUDAS
        // =====================================================

        $saldo = floatval(
            $_POST['saldo_a_pagar'] ?? 0
        );

        if ($saldo > 0) {

            $proveedor_id = $proveedor;

            if ($proveedor_id <= 0) {
                throw new Exception(
                    "Proveedor inválido para pago."
                );
            }

            $deudas = $proveedorModel
                ->ProveedorYDeuda($proveedor_id);

            if (empty($deudas)) {
                throw new Exception(
                    "El proveedor no tiene deudas pendientes."
                );
            }

            foreach ($deudas as $deuda) {

                if ($saldo <= 0) {
                    break;
                }

                $cuenta_id = intval(
                    $deuda['compra_id'] ?? 0
                );

                $pendiente = floatval(
                    $deuda['pendiente'] ?? 0
                );

                if (
                    $cuenta_id <= 0 ||
                    $pendiente <= 0
                ) {
                    continue;
                }

                $pago_aplicado = min(
                    $saldo,
                    $pendiente
                );

                // 🔥 DESCONTAR DEUDA
                $res = $egresoModel->pagarDeudaCompra(
                    $cuenta_id,
                    $pago_aplicado
                );

                if (
                    !$res ||
                    (
                        isset($res['success']) &&
                        !$res['success']
                    )
                ) {
                    throw new Exception(
                        "Error al descontar saldo de deuda ID: {$cuenta_id}"
                    );
                }

                // 🔥 HISTORIAL DE PAGO
                $desc =
                    'Pago de deuda (Compra #' .
                    $cuenta_id .
                    ') por $' .
                    number_format($pago_aplicado, 2);

                $ref = "PC-" . $cuenta_id;

                $egresoModel->registrarPagoCuentaPorPagar(

                    $almacen_id,

                    $proveedor_id,

                    $cuenta_id,

                    $pago_aplicado,

                    $metodo_pago,

                    $ref,

                    $_SESSION['usuario_id'] ?? 0,

                    $desc

                );

                $saldo -= $pago_aplicado;
            }
        }

        // =====================================================
        // 🔥 RESPUESTA FINAL
        // =====================================================

        echo json_encode($resultado);

    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([

            'success' => false,

            'message' => $e->getMessage()

        ]);
    }

    exit;
}