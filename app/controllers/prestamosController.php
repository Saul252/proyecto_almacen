<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/prestamosModel.php';
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/tesoreriaModel.php';
require_once __DIR__ . '/../models/corteCajaModel.php';
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/egresos/gastosModel.php';
protegerPagina('prestamos');

$usuario_id = $_SESSION['usuario_id'] ?? 0;

/* =========================
   MODELOS
========================= */
$prestamosModel    = new PrestamosModel($conexion);
$trabajadoresModel = new TrabajadorModel($conexion);
$almacenModel      = new AlmacenModel($conexion);
$tesoreria         = new tesoreriaModel($conexion);
$corteCaja         = new CorteCajaModel($conexion);
$egreso            = new EgresoModel($conexion);
$gastosModel       = new GastoModel($conexion);

/* =========================
   SESIÓN
========================= */
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

/* =========================
   INPUT FILTROS
========================= */
$periodo  = $_GET['periodo'] ?? 'hoy';
$f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
$f_fin    = $_GET['f_fin'] ?? date('Y-m-d');
$almacen_id_req = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;

/* =========================
   FECHAS AUTOMÁTICAS
========================= */
// Solo aplicar periodo si NO vienen fechas manuales
$usaFechasManual = !empty($_GET['f_inicio']) || !empty($_GET['f_fin']);

if (!$usaFechasManual) {

    if ($periodo === 'hoy') {
        $f_inicio = $f_fin = date('Y-m-d');
    } 
    elseif ($periodo === 'ayer') {
        $f_inicio = $f_fin = date('Y-m-d', strtotime("-1 day"));
    }
    elseif ($periodo === 'semana') {
        $f_inicio = date('Y-m-d', strtotime('-7 days'));
        $f_fin = date('Y-m-d');
    }
    elseif ($periodo === 'mes') {
        $f_inicio = date('Y-m-01');
        $f_fin = date('Y-m-d');
    }

}

/* =========================
   ALMACÉN ACTIVO
========================= */
$target = ($almacen_usuario != 0)
    ? $almacen_usuario
    : ($almacen_id_req ?: 0);

/* =========================
   AJAX
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'ajax') {

    header('Content-Type: application/json');

    try {

        // 🔥 LEER LO QUE ENVÍAS DESDE JS
        $almacen_id = intval($_GET['almacen_id'] ?? 0);
        $f_inicio   = $_GET['f_inicio'] ?? null;
        $f_fin      = $_GET['f_fin'] ?? null;

        // 👇 ESTE ES TU TARGET REAL
        $target = $almacen_id;

        // 🔥 CONSULTAS
        $prestamos = $prestamosModel->listarPrestamos($target, $f_inicio, $f_fin);
        $deuda     = $prestamosModel->obtenerTotalDeuda($target, $f_inicio, $f_fin);
        $trabajadores = $trabajadoresModel->listarTrabajadores($target);

        echo json_encode([
            'status' => 'success',
            'prestamos' => $prestamos,
            'deuda' => $deuda,
            'trabajadores' => $trabajadores
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'deudaTrabajador') {

    header('Content-Type: application/json');

    try {

        $trabajador_id = intval($_GET['trabajador_id'] ?? 0);

        $deudaTrabajador = $prestamosModel->obtenerDeudaTrabajador($trabajador_id);

        echo json_encode([
            'status' => 'success',
            'deuda' => $deudaTrabajador
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
/* =========================
   CREAR PRÉSTAMO + GASTO
========================= */

if (isset($_GET['action']) && $_GET['action'] === 'crear') {
    header('Content-Type: application/json');

    try {

        $almacen        = intval($_POST['almacen_id'] ?? 0);
        $trabajador_id  = intval($_POST['trabajador_id'] ?? 0);
        $monto          = floatval($_POST['monto_total'] ?? 0);
        $metodo_pago    = $_POST['metodo_pago'] ?? 'efectivo';
        $descripcion    = trim($_POST['descripcion'] ?? '');

        if ($trabajador_id <= 0 || $monto <= 0) {
            throw new Exception("Datos inválidos");
        }

        // =========================
        // 1. OBTENER NOMBRE
        // =========================
        $nombreTrabajador = $trabajadoresModel->nombreTrabajador($trabajador_id);

        // =========================
        // 2. CREAR GASTO (PRIMERO 🔥)
        // =========================
        $folio = $gastosModel->generarSiguienteFolioGasto($almacen);

        $concepto = "Préstamo a {$nombreTrabajador}" . 
                    ($descripcion ? " - {$descripcion}" : "");

        $cabecera = [
            'folio'            => $folio,
            'fecha'            => date('Y-m-d'),
            'almacen_id'       => $almacen,
            'categoria_id'     => 8, // préstamos
            'usuario_id'       => $usuario_id,
            'beneficiario'     => $nombreTrabajador,
            'metodo_pago'      => $metodo_pago,
            'total'            => $monto,
            'documento_url'    => '',
            'observaciones'    => $concepto
        ];

        $descripciones = [$concepto];
        $cantidades    = [1];
        $precios       = [$monto];

        $resultGasto = $egreso->registrarGasto(
            $cabecera,
            $descripciones,
            $cantidades,
            $precios
        );

        if (!$resultGasto || empty($resultGasto['success'])) {
            throw new Exception("Error al registrar gasto");
        }

        // 🔥 IMPORTANTE: obtener ID del gasto
        $gasto_id = $folio ?? 0;

        if ($gasto_id <= 0) {
            throw new Exception("No se obtuvo el ID del gasto");
        }

        // =========================
        // 3. CREAR PRÉSTAMO
        // =========================
        $data = [
            'trabajador_id' => $trabajador_id,
            'almacen_id'    => $almacen,
            'monto_total'   => $monto,
            'estado'        => 'activo',
            'descripcion'   => $descripcion,
            'gasto_id'      => $gasto_id // 🔥 vínculo clave
        ];

        $ok = $prestamosModel->crearPrestamo($data);

        if (!$ok) {
            throw new Exception("Error al registrar préstamo");
        }

        // =========================
        // RESPUESTA
        // =========================
        echo json_encode([
            'success' => true,
            'message' => 'Préstamo y gasto registrados correctamente'
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
/* =========================
   ABONAR
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'abonar') {

    while (ob_get_level()) ob_end_clean();
    ob_start();

    header('Content-Type: application/json');

    try {

        error_log("--- INICIANDO ABONO ---");
        error_log("POST: " . print_r($_POST, true));

        // =========================
        // VALIDACIONES BASE
        // =========================
        if (!isset($prestamosModel)) {
            throw new Exception("prestamosModel no instanciado");
        }

        if (!isset($corteCaja)) {
            throw new Exception("corteCaja no instanciado");
        }

        // =========================
        // DATOS
        // =========================
        $id_almacen   = intval($_POST['almacen_id'] ?? 0);
        $id_prestamo  = intval($_POST['prestamo_id'] ?? 0);
        $monto        = floatval($_POST['monto_abono'] ?? 0);

        $metodo_pago  = $_POST['metodo_pago'] ?? 'efectivo';
        $observaciones = trim($_POST['observaciones'] ?? '');

        $caja_fuerte_id = intval($_POST['caja_fuerte_id'] ?? 0);
        $banco_id       = intval($_POST['banco_id'] ?? 0);

        $usuario_id = intval($usuario_id ?? 0);

        // =========================
        // VALIDACIONES
        // =========================
        if ($id_almacen <= 0) throw new Exception("Almacén inválido");
        if ($id_prestamo <= 0) throw new Exception("Préstamo inválido");
        if ($monto <= 0) throw new Exception("Monto inválido");

        // =========================
        // REGISTRAR ABONO
        // =========================
        $data = [
            'almacen_id'    => $id_almacen,
            'prestamo_id'   => $id_prestamo,
            'monto_abono'   => $monto,
            'metodo_pago'   => $metodo_pago,
            'usuario_id'    => $usuario_id,
            'observaciones' => $observaciones
        ];

        $ok = $prestamosModel->registrarAbono($data);

        if (!$ok) {
            throw new Exception("Error en registrarAbono()");
        }

        // =========================
        // CERRAR PRÉSTAMO SI APLICA
        // =========================
        $prestamosModel->cerrarPrestamoSiPagado($id_prestamo);

        // =========================
        // 🔥 DECISIÓN REAL DE DESTINO
        // =========================
        $usado_caja_o_banco = false;

        // CAJA FUERTE
        if (!empty($caja_fuerte_id)) {

            $corteCaja->actualizarSaldoCajaFuerte($caja_fuerte_id, $monto);
            $usado_caja_o_banco = true;
        }

        // BANCO
        elseif (!empty($banco_id)) {

            $corteCaja->actualizarSaldoBanco($banco_id, $monto);
            $usado_caja_o_banco = true;
        }

        // =========================
        // SOLO SI NO ES CAJA NI BANCO
        // =========================
        if (!$usado_caja_o_banco) {

            $data2 = [
                'almacen_id'         => $id_almacen,
                'usuario_id'         => $usuario_id,
                'categoria_id'       => 13,
                'monto'              => $monto,
                'metodo_pago'        => $metodo_pago,
                'fecha_movimiento'   => date('Y-m-d H:i:s'),

                'concepto'           => "Abono a préstamo ID: $id_prestamo",
                'tipo_operacion'     => 'entrada',

                'monto_efectivo'      => ($metodo_pago === 'efectivo') ? $monto : 0,
                'monto_tarjeta'       => ($metodo_pago === 'tarjeta') ? $monto : 0,
                'monto_transferencia' => ($metodo_pago === 'transferencia') ? $monto : 0,

                'almacen_destino_id'  => $id_almacen,
                'caja_fuerte_id'      => null,
                'banco_id'            => null
            ];

            $corteCaja->registrarAperturaDesdeCierreConceptoAbono($data2);
        }

        // =========================
        // RESPUESTA
        // =========================
        ob_end_clean();

        echo json_encode([
            'success' => true,
            'message' => 'Abono registrado correctamente',
            'debug'   => [
                'caja_fuerte_id' => $caja_fuerte_id,
                'banco_id'       => $banco_id,
                'usado_directo'  => $usado_caja_o_banco
            ]
        ]);

    } catch (Throwable $e) {

        if (ob_get_level()) ob_end_clean();

        error_log("ERROR ABONO: " . $e->getMessage());

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine()
        ]);
    }

    exit;
}
/* =========================
   LISTAR
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    header('Content-Type: application/json');

    try {

        $data = $prestamosModel->listarPrestamos($target, $f_inicio, $f_fin);

        echo json_encode([
            'success' => true,
            'data' => $data,
            'almacen_activo' => $target
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

/* =========================
   DETALLE
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'detalle') {
    header('Content-Type: application/json');

    try {

        $id = intval($_GET['id'] ?? 0);

        $prestamo = $prestamosModel->obtenerPrestamo($id);
        $abonos   = $prestamosModel->listarAbonos($id);

        echo json_encode([
            'success' => true,
            'prestamo' => $prestamo,
            'abonos' => $abonos
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'eliminarPrestamo') {
    header('Content-Type: application/json');

    try {

        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("ID inválido");
        }

        // 🔥 Validar si tiene abonos
        if ($prestamosModel->tieneAbonos($id)) {
            throw new Exception("No puedes eliminar, el préstamo tiene abonos");
        }

        // 🔥 Eliminar gasto (folio → id → delete)
        $okGasto = $prestamosModel->eliminarGastoPorPrestamo($id);

        if (!$okGasto) {
            throw new Exception("Error al eliminar el gasto");
        }

        // 🔥 Eliminar préstamo
        $okPrestamo = $prestamosModel->eliminarPrestamo($id);

        if (!$okPrestamo) {
            throw new Exception("Error al eliminar el préstamo");
        }

        echo json_encode([
            'success'  => true,
            'message'  => 'Préstamo eliminado correctamente',
            'debug'    => [
                'gasto'     => $okGasto,
                'prestamo'  => $okPrestamo
            ]
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
/* =========================
   VISTA
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {

    $prestamos = $prestamosModel->listarPrestamos($target, $f_inicio, $f_fin);
    $deuda       =$prestamosModel->obtenerTotalDeuda($target, $f_inicio, $f_fin);
    $trabajadores = $trabajadoresModel->listarTrabajadores($target);
    $cajasFuertes = $tesoreria->getCajasFuertes($target);
    $saldo = $corteCaja->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);

    if (!isset($saldo[0])) {
        $saldo = [[
            'idAlmacen' => $target,
            'almacen'   => 'Sucursal',
            'monto'     => $saldo['monto'] ?? 0
        ]];
    }

    $almacenes = $almacenModel->getAlmacenes($almacen_usuario);
    $paginaActual = 'prestamos';

    require_once __DIR__ . '/../views/prestamos_view.php';
}