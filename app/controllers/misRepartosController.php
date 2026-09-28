<?php
/**
 * CF SYSTEM - Módulo de Logística (Controlador Integrado)
 * Maneja la vista de "Mis Repartos" para Choferes y el Panel Global para Admin.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/almacen_model.php';
// Modelos necesarios
require_once __DIR__ . '/../models/RepartosModel.php';

// --- CONFIGURACIÓN DE IDENTIDAD ---
$username = $_SESSION['username'] ?? '';
$trabajador_id = intval($_SESSION['trabajador_id'] ?? 0);
$rol_nombre = $_SESSION['rol'] ?? '';
$almacenModel = new AlmacenModel($conexion);
/**
 * Lógica de Supervisor:
 * Es supervisor si el rol es 'administrador' O si el username NO contiene 'Trabajador'.
 */
$es_supervisor = ($rol_nombre === 'administrador' || strpos($username, 'Trabajador') === false);

// Protegemos la página: usa el permiso que ya tienes configurado


$repartoM = new RepartoModel($conexion);

// --- PROCESAMIENTO DE ACCIONES AJAX ---
if (isset($_REQUEST['action'])) {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $action = $_REQUEST['action'];

    try {
        /**
         * ACCIÓN: get_historial_viajes
         * Carga los viajes ACTIVOS y TERMINADOS.
         */
        if ($action === 'get_historial_viajes') {
            // Si es supervisor, pasamos NULL para ver todos los viajes del sistema.
            // Si es chofer, pasamos su trabajador_id para que solo vea los suyos.
            $filtro = $es_supervisor ? null : $trabajador_id;
            $viajes = $repartoM->getViajesLogistica($filtro);

            echo json_encode([
                "success" => true,
                "data" => $viajes,
                "modo" => $es_supervisor ? 'admin' : 'trabajador'
            ]);
            exit;
        }
        if ($action === 'get_monitor_entregas') {
            header('Content-Type: application/json');

            // 🔹 1. ALMACÉN
            $almacen_id = isset($_GET['almacen_id'])
                ? intval($_GET['almacen_id'])
                : intval($_SESSION['almacen_id'] ?? 0);

            // 🔹 2. FECHAS
            $fecha_inicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
            $fecha_fin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

            // 🔹 3. PAGINACIÓN
            $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 15;
            $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

            if ($pagina < 1)
                $pagina = 1;

            $inicio = ($pagina - 1) * $limite;

            // 🔥 4. CONSULTA PRINCIPAL (YA CON FECHAS)
            $registros = $repartoM->getMonitorEntregasRuta(
                $almacen_id,
                $fecha_inicio,
                $fecha_fin,
                $inicio,
                $limite
            );

            // 🔥 5. TOTAL (IMPORTANTE: también con fechas)
            $total_records = $repartoM->contarTotalEntregasRuta(
                $almacen_id,
                $fecha_inicio,
                $fecha_fin

            );

            if (is_array($registros)) {

                $total_pages = ($limite > 0)
                    ? ceil($total_records / $limite)
                    : 1;

                echo json_encode([
                    "success" => true,
                    "data" => $registros,
                    "total_records" => $total_records,
                    "total_pages" => $total_pages,
                    "current_page" => $pagina,
                    "limite" => $limite,
                    "filtros" => [
                        "almacen_id" => $almacen_id,
                        "fecha_inicio" => $fecha_inicio,
                        "fecha_fin" => $fecha_fin
                    ]
                ]);

            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al obtener la trazabilidad de entregas."
                ]);
            }

            exit;
        }


        if ($action === 'listar_viajes_activos') {
            // 1. Limpiamos cualquier salida previa para evitar JSON corrupto
            if (ob_get_length())
                ob_clean();

            // 2. Definimos el ID por defecto
            $almacen_id = 0;

            // 3. PRIORIDAD TOTAL: Si el JS envía algo, eso es lo que manda (incluso si es 0)
            if (isset($_GET['almacen_id'])) {
                $almacen_id = intval($_GET['almacen_id']);
            }
            // 4. RESPALDO: Si no viene en el GET, usamos la sesión del usuario
            else if (isset($_SESSION['almacen_id'])) {
                $almacen_id = intval($_SESSION['almacen_id']);
            }

            // 5. LLAMADA AL MODELO
            // Asegúrate de que tu modelo soporte recibir el 0 para "ver todos"
            $viajes = $repartoM->listarViajesActivos($almacen_id);

            // 6. RESPUESTA LIMPIA
            header('Content-Type: application/json; charset=utf-8');
            // Evitar que el navegador guarde en caché una respuesta vacía
            header('Cache-Control: no-cache, must-revalidate');

            echo json_encode([
                'success' => true,
                'data' => $viajes,
                'count' => count($viajes),
                'debug' => [
                    'almacen_solicitado' => $almacen_id,
                    'metodo' => isset($_GET['almacen_id']) ? 'GET' : 'SESSION'
                ]
            ]);
            exit;
        }  // ... dentro de tu switch o bloques if de action ...
        if ($action === 'get_evidencias_por_folio') {

            header('Content-Type: application/json');

            $folio = trim($_GET['folio'] ?? '');

            if ($folio === '') {

                echo json_encode([
                    "success" => false,
                    "message" => "Folio de viaje no proporcionado"
                ]);

                exit;
            }

            $data = $repartoM->getEvidenciasPorFolioRuta($folio);


            echo json_encode([
                "success" => true,
                "total" => 0,
                "data" => $data
            ]);

            exit;
        }
        if ($action === 'get_evidencias_por_venta') {

            header('Content-Type: application/json');

            $folio = trim($_GET['folio'] ?? '');

            if ($folio === '') {

                echo json_encode([
                    "success" => false,
                    "message" => "Folio de viaje no proporcionado"
                ]);

                exit;
            }

            $data = $repartoM->evidenciaEntregaVenta($folio);


            echo json_encode([
                "success" => true,
                "total" => 0,
                "data" => $data
            ]);

            exit;
        }
        if ($action === 'get_evidencias_por_entrega_id') {

            header('Content-Type: application/json');

            $folio = intval($_GET['folio'] ?? '');

            if ($folio < 1) {

                echo json_encode([
                    "success" => false,
                    "message" => "Folio de viaje no proporcionado"
                ]);

                exit;
            }

            $data = $repartoM->evidenciaEntregaPorid($folio);


            echo json_encode([
                "success" => true,
                "total" => 0,
                "data" => $data
            ]);

            exit;
        }
        // --- ACCIÓN: OBTENER DETALLE DE TRAZABILIDAD (MONITOR) ---
        if ($action === 'get_detalle_trazabilidad') {
            if (ob_get_level())
                ob_clean();
            header('Content-Type: application/json');

            $tipo = $_GET['tipo'] ?? 'MOSTRADOR';
            $id = intval($_GET['id']);

            if ($id <= 0) {
                echo json_encode(["success" => false, "message" => "ID de seguimiento no válido."]);
                exit;
            }

            try {
                $data = null;

                if ($tipo === 'RUTA') {
                    // getDetalleRutaMonitor recibe reparto_id
                    // devuelve: viaje_folio, vehiculo, placas, chofer, usuario_asigno_sistema,
                    //           lista_productos[] (producto, cantidad, cliente_destino, ticket),
                    //           tripulantes[] (nombre)
                    $data = $repartoM->obtenerViajesLogistica($id);

                } else {
                    // getDetalleMovimientoNormal recibe movimiento_id
                    // devuelve: movimiento_id, cantidad, fecha_salida, producto, folio_venta,
                    //           cliente, usuario_asigno_sistema, usuario_patio, fecha_patio
                    $data = $repartoM->getDetalleMovimientoNormal($id);
                }

                if ($data) {
                    echo json_encode([
                        "success" => true,
                        "tipo_procesado" => $tipo,
                        "data" => $data
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "No se encontró información para este registro."
                    ]);
                }

            } catch (Exception $e) {
                echo json_encode([
                    "success" => false,
                    "message" => "Error interno: " . $e->getMessage()
                ]);
            }
            exit;
        }  /**
           * ACCIÓN: get_mi_ruta_activa (O detalle de un viaje específico)
           * Carga los materiales/puntos de entrega de un viaje seleccionado.
           */
        if ($action === 'get_mi_ruta_activa') {
            $viaje_id = intval($_GET['viaje_id'] ?? 0);

            if ($viaje_id <= 0) {
                throw new Exception("ID de viaje no proporcionado.");
            }

            // El modelo busca los puntos de entrega asociados a ese ID de viaje
            $puntos = $repartoM->getCargaPendienteChofer(null, $viaje_id);

            echo json_encode([
                "success" => true,
                "data" => $puntos
            ]);
            exit;
        }
        if ($action === 'subir_evidencia_reparto') {
            // Forzamos que la salida sea JSON desde el inicio
            header('Content-Type: application/json');

            try {
                $movimiento_id = intval($_POST['id_movimiento'] ?? 0);

                // Validación básica de ID
                if ($movimiento_id <= 0) {
                    throw new Exception("ID de movimiento no válido.");
                }

                $relPath = "uploads/evidencias/" . date('Y/m/d') . "/";
                $targetDir = dirname(__DIR__, 2) . "/" . $relPath;

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $procesarFoto = function ($inputName, $prefijo) use ($movimiento_id, $targetDir, $relPath) {
                    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
                        $nombre = $prefijo . "_" . $movimiento_id . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetDir . $nombre)) {
                            return $relPath . $nombre;
                        }
                    }
                    return null;
                };

                $foto_entrega = $procesarFoto('evidencia_foto', 'MAT');
                $foto_nota = $procesarFoto('evidencia_nota', 'NOT');

                $datos = [
                    'id_movimiento' => $movimiento_id,
                    'id_venta' => intval($_POST['id_venta'] ?? 0),
                    'trabajador_id' => $id_ejecutor ?? 0, // Asegúrate que esta variable exista
                    'vehiculo_id' => intval($_POST['vehiculo_id'] ?? 0),
                    'folio' => $_POST['folio'] ?? '',
                    'fotografia_entrega' => $foto_entrega,
                    'fotografia_nota' => $foto_nota,
                    'estatus_entrega' => $_POST['estatus_entrega'] ?? 'Entregado',
                    'comentario' => $_POST['comentario'] ?? ''
                ];

                if ($repartoM->registrarEntregaMovimiento($datos)) {
                    echo json_encode(["success" => true, "message" => "Evidencias guardadas correctamente"]);
                } else {
                    // ERROR CRÍTICO: Si el modelo falla, hay que avisar
                    echo json_encode(["success" => false, "message" => "Error al insertar en la base de datos."]);
                }

            } catch (Exception $e) {
                // No enviamos 400 para que el fetch no truene antes de leer el JSON si prefieres manejarlo por success:false
                echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
            }
            exit;
        }
        /**
         * ACCIÓN: eliminar_evidencia
         * Borra el registro de confirmación y permite que el punto de ruta quede pendiente de nuevo.
         */
        if ($action === 'eliminar_evidencia') {
            header('Content-Type: application/json');

            // Validamos que sea supervisor, ya que un chofer no debería borrar evidencias
            if (!$es_supervisor) {
                echo json_encode(["success" => false, "message" => "No tiene permisos para eliminar evidencias."]);
                exit;
            }

            $id_movimiento = intval($_POST['id_movimiento'] ?? 0);

            if ($id_movimiento <= 0) {
                echo json_encode(["success" => false, "message" => "ID de evidencia no válido."]);
                exit;
            }

            // Llamamos al modelo (la función que ya ajustamos)
            if ($repartoM->eliminarEvidencia($id_movimiento)) {
                echo json_encode([
                    "success" => true,
                    "message" => "Evidencia eliminada correctamente. El punto de ruta ahora está pendiente."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo eliminar la evidencia de la base de datos."
                ]);
            }
            exit;
        }

    } catch (Exception $e) {
        if (ob_get_level())
            ob_clean();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}


// --- CARGA DE VISTA ---
$paginaActual = 'misRepartos';
protegerPagina('misRepartos');
$tituloPagina = $es_supervisor ? "Monitor Global de Logística" : "Mis Repartos";
$es_admin = $_SESSION['almacen_id'] === 0 ? true : false;

$listaAlmacenes = $almacenModel->getAlmacenes($_SESSION['almacen_id']);
require_once __DIR__ . '/../views/misRepartos_view.php';