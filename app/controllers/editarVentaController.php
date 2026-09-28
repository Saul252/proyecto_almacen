<?php
/**
 * editarVentaController.php
 * Controlador para la gestión y edición de ventas existentes.
 */

// 1. Verificación de Rutas (Asegúrate de que estas rutas sean correctas en tu servidor)
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php'; 
require_once __DIR__ . '/../models/ventasEditarModel.php'; 
require_once __DIR__ . '/../models/clientesModel.php'; 
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/almacen/productosModel.php';

protegerPagina('ventas'); 
$paginaActual = 'ventas';
class VentaHistorialController {
    private $model;
    private $clientesModel;
    private $productosModel;

    public function __construct($db) {
        $this->model = new VentaHistorialModel($db);
        $this->clientesModel = new clientesModel($db);
        $this->productosModel = new ProductoModel($db);

    }

    /**
     * Carga los datos para visualizar la venta antes de cualquier edición.
     * Invocado por: cargarDatosVenta() en el JS
     */
    public function cargarDetalleVenta() {
        header('Content-Type: application/json');
        try {
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) throw new Exception("ID de venta no proporcionado o inválido.");
            
            $detalle = $this->model->obtenerDetalleCompleto($id);
            
            if (!$detalle) {
                echo json_encode(["status" => "error", "message" => "No se encontró la información para la venta #$id"]);
            } else {
                echo json_encode($detalle);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
      public function opcionesMedida() {
        header('Content-Type: application/json');
        try {
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) throw new Exception("ID de venta no proporcionado o inválido.");
            
           $medidasAdicionales =  $this->productosModel->obtenerMedidasPorProducto($id);
            
            if (!$medidasAdicionales) {
                echo json_encode(["status" => "error", "message" => "No se encontró la información para la venta #$id"]);
            } else {
                echo json_encode($medidasAdicionales);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Procesa la edición de cantidades y totales (Cambio de contrato).
     * Invocado por: enviarEdicion() en el JS
     */
     public function guardarEdicionVenta() {
        header('Content-Type: application/json');
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) throw new Exception("Error al decodificar los datos de edición.");

            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $data['usuario_id'] = $_SESSION['usuario_id'] ?? 1;

            $res = $this->model->recalcularYEditarVenta2($data);
            echo json_encode($res);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
public function guardarEdicionVentaSaldoAFavor() {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) throw new Exception("JSON_DECODE_ERROR: No se recibió un JSON válido.");

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $u_id = $_SESSION['usuario_id'] ?? 1;
        $data['usuario_id'] = $u_id;

        // 1. Ejecutamos la edición
        $res = $this->model->recalcularYEditarVenta($data);

        if ($res['status'] !== 'success') {
            throw new Exception("MODEL_EDIT_ERROR: " . ($res['message'] ?? 'Error desconocido en el modelo de edición.'));
        }

        // 2. Afectamos los saldos
        if (isset($res['financiero'])) {
            $fin   = $res['financiero'];
            $dif   = floatval($fin['diferencia']);
            $v_id  = intval($fin['venta_id']);
            $c_id  = intval($fin['id_cliente']);
            $fecha = date('Y-m-d H:i:s');

            if ($dif != 0) {
                $monto_ajuste = ($dif * -1);
                $tipo_log = ($dif > 0) ? 'CARGO_EDICION' : 'ABONO_EDICION';

                // PRUEBA DE EXISTENCIA DE MODELO
                if (!isset($this->clientesModel)) {
                    throw new Exception("OBJETO_NULL: \$this->clientesModel no está instanciado en el constructor.");
                }

                // PRUEBA DE LOG
                if (!method_exists($this->clientesModel, 'abono_saldos_log')) {
                    throw new Exception("METODO_MISSING: No existe abono_saldos_log en ClientesModel.");
                }
                $this->clientesModel->abono_saldos_log($c_id, $v_id, abs($dif), $u_id, $tipo_log, $fecha);

                // PRUEBA DE SALDOS A FAVOR
                if (!method_exists($this->clientesModel, 'abono_saldosAFavor')) {
                    throw new Exception("METODO_MISSING: No existe abono_saldosAFavor en ClientesModel.");
                }
                
                $ejecutado = $this->clientesModel->abono_saldosAFavor($c_id, $monto_ajuste, $v_id, $fecha);

                if (!$ejecutado) {
                    throw new Exception("DB_EXECUTE_ERROR: La consulta de saldos devolvió false.");
                }

                $res['mensaje_financiero'] = "Saldo actualizado correctamente.";
            }
        }

        echo json_encode($res);

    } catch (Exception $e) {
        // Devolvemos el error detallado para el console.log
        echo json_encode([
            "status" => "error", 
            "message" => $e->getMessage(),
            "debug_trace" => "Error en línea " . $e->getLine() . " de " . $e->getFile()
        ]);
    }
    exit;
}
    /**
     * Registra un nuevo pago/abono a la venta.
     */
    public function procesarSaldoEdicion()
{
    header('Content-Type: application/json');

    try {

        $venta_id   = intval($_POST['venta_id'] ?? 0);
        $cliente_id = intval($_POST['cliente_id'] ?? 0);
        $diferencia = floatval($_POST['diferencia'] ?? 0);

        if ($venta_id <= 0) {
            throw new Exception("ID de venta inválido.");
        }

        if ($cliente_id <= 0) {
            throw new Exception("ID de cliente inválido.");
        }

        $usuario_id = $_SESSION['usuario_id'] ?? 1;
        $fecha      = date('Y-m-d H:i:s');

        if ($diferencia != 0) {

            $monto_ajuste = $diferencia * -1;
            $tipo_log = ($diferencia > 0)
                ? 'CARGO_EDICION'
                : 'ABONO_EDICION';

            $this->clientesModel->abono_saldos_log(
                $cliente_id,
                $venta_id,
                abs($diferencia),
                $usuario_id,
                $tipo_log,
                $fecha
            );

            $ok = $this->clientesModel->abono_saldosAFavor(
                $cliente_id,
                $monto_ajuste,
                $venta_id,
                $fecha
            );

            if (!$ok) {
                throw new Exception("No fue posible actualizar el saldo del cliente.");
            }
        }

        echo json_encode([
            "status"  => "success",
            "message" => "Saldo actualizado correctamente."
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status"  => "error",
            "message" => $e->getMessage()
        ]);
    }
}
    public function procesarEdicionVentaRegresarSaldo()
{
    header('Content-Type: application/json');

    try {

        $venta_id   = intval($_POST['venta_id'] ?? 0);
        $cliente_id = intval($_POST['cliente_id'] ?? 0);
        $diferencia = floatval($_POST['diferencia'] ?? 0);

       
        echo json_encode([
            "status"  => "success",
            "message" => "Saldo actualizado correctamente."
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status"  => "error",
            "message" => $e->getMessage()
        ]);
    }
}
    
    /**
     * Registra la salida física de mercancía (Entrega parcial).
     */
    public function registrarEntrega() {
        header('Content-Type: application/json');
        try {
            $venta_id = intval($_POST['venta_id'] ?? 0);
            $productosRaw = $_POST['productos'] ?? []; 
            
            if (empty($productosRaw)) throw new Exception("No se seleccionaron productos para entregar.");

            $productosArr = [];
            foreach ($productosRaw as $dv_id => $cantidad) {
                if ($cantidad > 0) {
                    $productosArr[] = [
                        'detalle_venta_id' => $dv_id,
                        'cantidad_a_entregar' => $cantidad
                    ];
                }
            }

            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $usuario_id = $_SESSION['usuario_id'] ?? 1;

            $res = $this->model->procesarEntregaParcial($venta_id, $productosArr, $usuario_id);
            echo json_encode($res);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    

/**
     * Obtiene el catálogo de productos disponibles para el almacén de la venta.
     * Invocado por: cargarCatalogoProductos() o similar en el JS.
     */
  public function cargarProductosAlmacen() {

    header('Content-Type: application/json');

    try {

        $almacen_id = intval($_GET['almacen_id'] ?? 0);

        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén no proporcionado.");
        }

        // Productos base
        $productos = $this->model->obtenerProductosAlmacen($almacen_id);

       foreach ($productos as $i => $producto) {

    $productos[$i]['medidas'] =
        $this->productosModel
            ->obtenerMedidasPorProducto($producto['id']);
}

        echo json_encode([
            "status" => "success",
            "data"   => $productos
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status"  => "error",
            "message" => $e->getMessage()
        ]);
    }
}

}
// --- RUTEADOR DE ACCIONES AJAX ---
// La variable $conexion viene de config/conexion.php
$controller = new VentaHistorialController($conexion);

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'obtenerDetalle': 
            $controller->cargarDetalleVenta(); 
            break;
        case 'obtenerProductos': // <-- NUEVO CASO
            $controller->cargarProductosAlmacen();
            break;
        case 'guardarEdicion': 
            $controller->guardarEdicionVenta();
            break;
          case 'guardarEdicionVentaSaldoAFavor': 
            $controller->guardarEdicionVentaSaldoAFavor();
            break;
        case 'guardarComoAbono':
            $controller->procesarSaldoEdicion();
            break;
        case 'guardarEntrega':
            $controller->registrarEntrega();
            break;
            case 'opcionesMedida':
            $controller->opcionesMedida();
            break;
            
        default:
            echo json_encode(["status" => "error", "message" => "Acción '{$_GET['action']}' no reconocida."]);
            break;
    }
    exit; 
}

// Si no hay 'action', cargamos la interfaz visual
require_once __DIR__ . '/../views/editarVentas.php';