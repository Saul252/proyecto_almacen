<?php
/**
 * ventasHistorialController.php
 * Controlador para la gestión de Entregas y Abonos (Historial de Ventas)
 */

require_once __DIR__ . '/../../includes/auth.php';
 // Tu función de seguridad
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventasHistorialModel.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/RepartosModel.php';

protegerPagina('ventashistorial');
$ventasModel = new VentaHistorialModel($conexion);
$clientesModel = new ClientesModel($conexion);
$repartosModel = new RepartoModel($conexion);
$paginaActual = 'ventashistorial';

// --- ACCIÓN: LISTADO AJAX (Con filtros) ---
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $filtros = [
            'search'   => $_GET['f_search'] ?? '',
            'status'   => $_GET['f_status'] ?? '',
            'pago'     => $_GET['f_pago'] ?? '',
            'rango'    => $_GET['f_rango'] ?? 'todos',
            'inicio'   => $_GET['f_inicio'] ?? '',
            'fin'      => $_GET['f_fin'] ?? '',
            'almacen'  => $_GET['f_almacen'] ?? 0
        ];

        $rol_id = $_SESSION['rol_id'] ?? 2;
        $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        $data = $ventasModel->obtenerVentasFiltradas($filtros, $rol_id, $id_almacen_usuario);
        echo json_encode($data);

    } catch (Throwable $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'guardarEntrega') {
    // Limpiamos cualquier salida previa para que solo salga el JSON
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        if (empty($_POST['venta_id'])) throw new Exception("ID de venta no recibido.");
        
        $venta_id = intval($_POST['venta_id']);
        $productos = $_POST['productos'] ?? [];
        $usuario_id = $_SESSION['usuario_id'] ?? 1;

        $resultado = $ventasModel->procesarEntrega($venta_id, $productos, $usuario_id);
        
        echo json_encode(['status' => 'success', 'message' => 'Entrega procesada correctamente']);

    } catch (Exception $e) {
        // Importante: Mandar el mensaje real de la excepción (ej. "Stock insuficiente...")
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } catch (Throwable $t) {
        echo json_encode(['status' => 'error', 'message' => 'Error crítico en el servidor']);
    }
    exit;
}

/// --- ACCIÓN: GUARDAR ABONO ---
if (isset($_GET['action']) && $_GET['action'] === 'guardarAbono') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $conexion->begin_transaction(); // <--- INICIO DE PROTECCIÓN

        // --- 0. CAPTURA DE DATOS ---
        $v_id = intval($_POST['venta_id'] ?? 0);
        $amt  = floatval($_POST['monto'] ?? 0);
        $met  = $_POST['metodo_pago'] ?? 'Efectivo'; 
        $ref =$_POST['referencia'] ?? '';
        $u_id = $_SESSION['usuario_id'] ?? 1;
        $fec  = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d H:i:s');
        $c_id = intval($_POST['cliente_id'] ?? 0);

        // --- 1. VALIDACIÓN ---
        if ($amt <= 0) throw new Exception("El monto debe ser mayor a 0.");
        
        if (!$c_id && $v_id > 0) {
            $c_id = $ventasModel->obtenerClientePorVenta($conexion, $v_id);
        }
        if (!$c_id) throw new Exception("No se halló cliente para procesar el abono.");

        // --- 2. LÓGICA DE SALDOS ---
        if ($met === 'Saldo a Favor') {
            // [Opcional] Validar aquí si el cliente tiene saldo suficiente en la BD 
            // antes de proceder, para mayor seguridad.

            // Restar del "Ahorro" y de la "Deuda"
            $clientesModel->agregar_saldo_a_favor($c_id, ($amt * -1), $v_id, $fec);
            $clientesModel->agregar_saldo_en_contra($c_id, ($amt * -1), $v_id, $fec);

            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, 'USO_SALDO_A_FAVOR', $fec);
        } else {
            // Abono normal: solo resta de la deuda
            $clientesModel->agregar_saldo_en_contra($c_id, ($amt * -1), $v_id, $fec);
            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, "ABONO_" . str_replace(' ', '_', $met), $fec);
        }

        // --- 3. REGISTRO EN HISTORIAL ---
        if (!$ventasModel->registrarAbono($v_id, $amt, $u_id, $met, $fec,$ref)) {
            throw new Exception("Error al registrar el movimiento en el historial.");
        }

        // --- 4. ÉXITO ---
        $conexion->commit(); // <--- SE GUARDAN LOS CAMBIOS REALMENTE

        echo json_encode([
            'status'   => 'success', 
            'message'  => 'Abono procesado correctamente.',
            'detalles' => ['monto' => number_format($amt, 2), 'metodo' => $met]
        ]);

    } catch (Throwable $e) {
        // SI ALGO FALLA, DESHACEMOS TODO LO QUE SE HIZO EN LAS TABLAS
        if ($conexion->connect_errno == 0) { 
            $conexion->rollback(); 
        }
        
        error_log("FALLO EN GUARDAR ABONO: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIÓN: OBTENER DETALLE ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? 0);
        
        // 1. Obtener el detalle completo de la venta
        $detalle = $ventasModel->obtenerDetalleCompleto($id);
        
        // 2. Extraer el id_cliente de la información obtenida
        // Accedemos a ['info'] y luego a ['id_cliente']
        $id_cliente = intval($detalle['info']['id_cliente'] ?? 0);

        // 3. Consultar el estatus del cliente usando ese ID
        if ($id_cliente > 0) {
            $estatusCliente = $clientesModel->obtenerEstatus($conexion, $id_cliente);
            // Agregamos el estatus al objeto detalle para que viaje al frontend
            $detalle['info']['estatus_cliente'] = $estatusCliente;
        }

        // 4. Imprimir el JSON final
        echo json_encode($detalle);

    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPagoFaltante') {

    // limpiar cualquier salida previa
    if (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID inválido'
            ]);
            exit;
        }

        // consulta al modelo
        $porPagar = $ventasModel->faltantePago($id);

        // seguridad: si viene null o false
        if (!$porPagar) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron datos'
            ]);
            exit;
        }

        // validar JSON
        $json = json_encode($porPagar);

        if ($json === false) {
            echo json_encode([
                'status' => 'error',
                'message' => json_last_error_msg()
            ]);
            exit;
        }

        echo $json;

    } catch (Throwable $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// --- ACCIÓN: CANCELAR VENTA (POST) ---

// --- ACCIÓN: CANCELAR VENTA (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'cancelarVentaSinSaldo') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        // Leemos el cuerpo de la petición (JSON)
        $input = json_decode(file_get_contents("php://input"), true);
        
        $venta_id   = intval($input['id_venta'] ?? 0);
        $motivo     = trim($input['motivo'] ?? 'Cancelación desde historial');
        $usuario_id = $_SESSION['usuario_id'] ?? 1;

        if ($venta_id <= 0) {
            throw new Exception("ID de venta no proporcionado o inválido.");
        }
       $repartosactivos = $repartosModel->contarEntregasActivasPorVenta($venta_id);

if ($repartosactivos > 0) {
    // Mensaje descriptivo y real
    throw new Exception("No es posible procesar la solicitud: Esta venta cuenta con $repartosactivos despacho(s) activo(s) en el módulo de logística.");
}

        // Ejecutamos la lógica en el modelo
        $resultado = VentasModel::cancelarVenta($conexion, $venta_id, $usuario_id, $motivo);
        
        echo json_encode($resultado);

    } catch (Throwable $e) {
        echo json_encode([
            'status'  => 'error', 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// --- ACCIÓN: CANCELAR VENTA (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'cancelarVenta') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $input = json_decode(file_get_contents("php://input"), true);
        $venta_id   = intval($input['id_venta'] ?? 0);
        $motivo     = trim($input['motivo'] ?? 'Cancelación de venta');
        $usuario_id = $_SESSION['usuario_id'] ?? 1;
        $fecha_act  = date('Y-m-d H:i:s');

        if ($venta_id <= 0) throw new Exception("ID de venta no válido.");
       $repartosactivos = $repartosModel->contarEntregasActivasPorVenta($venta_id);

if ($repartosactivos > 0) {
    // Mensaje descriptivo y real
    throw new Exception("No es posible procesar la solicitud: Esta venta cuenta con $repartosactivos despacho(s) activo(s) en el módulo de logística.");
}
        // --- PASO 1: OBTENER DETALLE COMPLETO ---
        // Aprovechamos tu función que ya suma los pagos automáticamente
        $detalle = $ventasModel->obtenerDetalleCompleto($venta_id);
        
        if (!$detalle || empty($detalle['info'])) {
            throw new Exception("No se encontró la información de la venta #$venta_id");
        }

       // 1. Datos base
$infoVenta      = $detalle['info'];
$cliente_id     = intval($infoVenta['id_cliente']);
$total_venta    = floatval($infoVenta['total'] ?? 0);        // Ej: 20
$total_pagado   = floatval($infoVenta['total_pagado'] ?? 0); // Ej: 10
$pendiente_pago = $total_venta - $total_pagado;            // Ej: 10 (Lo que aún debe)

error_log("Cancelación Especial - Venta: $venta_id. Pagado: $total_pagado, Deuda a limpiar: $pendiente_pago");

// --- PASO A: DEVOLVER LO PAGADO AL SALDO A FAVOR ---
if ($total_pagado > 0) {
    $clientesModel->abono_saldos_log(
        $cliente_id, 
        $venta_id, 
        $total_pagado, 
        $usuario_id, 
        'DEVOLUCION_PAGO_CANCELACION', 
        $fecha_act
    );

    // Sumamos lo pagado: tu función lo pondrá en saldo_a_favor (o reducirá otras deudas)
    $clientesModel->abono_saldosAFavor($cliente_id, $total_pagado, $venta_id, $fecha_act);
}

// --- PASO B: LIMPIAR LA DEUDA PENDIENTE DE ESTA VENTA ---
if ($pendiente_pago > 0) {
    $clientesModel->abono_saldos_log(
        $cliente_id, 
        $venta_id, 
        $pendiente_pago, 
        $usuario_id, 
        'LIMPIEZA_DEUDA_CANCELACION', 
        $fecha_act
    );

    /**
     * Al sumar el 'pendiente_pago' como positivo, tu función abono_saldosAFavor 
     * subirá el Neto exactamente lo necesario para que la deuda de ESTA venta
     * en el Saldo en Contra global se vuelva 0.
     */
    $clientesModel->abono_saldosAFavor($cliente_id, $pendiente_pago, $venta_id, $fecha_act);
}

        // --- PASO 3: CANCELAR LA VENTA ---
        // Cambiamos el estado a 'cancelada' en la tabla ventas
        $resultado = VentasModel::cancelarVenta($conexion, $venta_id, $usuario_id, $motivo);

        // Agregamos el monto devuelto a la respuesta para que el Front-end avise al usuario
        if ($resultado['status'] === 'success') {
            $resultado['monto_devuelto'] = $total_pagado;
        }

        echo json_encode($resultado);

    } catch (Throwable $e) {
        error_log("Error en cancelación de venta: " . $e->getMessage());
        echo json_encode([
            'status'  => 'error', 
            'message' => 'Error al cancelar: ' . $e->getMessage()
        ]);
    }
    exit;
}
// --- CARGA DE VISTA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $tituloPagina = "Control de Entregas";
    require_once __DIR__ . '/../views/registrar_pagos_view.php';
}