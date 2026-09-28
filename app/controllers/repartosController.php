<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// Modelos necesarios
require_once __DIR__ . '/../models/RepartosModel.php';
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/entregasModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('repartos'); 
$paginaActual = 'repartos';

$repartoM    = new RepartoModel($conexion);
$trabajadorM = new TrabajadorModel($conexion);
$vehiculoM   = new VehiculoModel($conexion);
$entregaM    = new EntregaModel($conexion);
$almacenModel = new AlmacenModel($conexion);

if (isset($_GET['action']) || isset($_POST['action'])) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    $action = $_REQUEST['action'] ?? '';

    try {
        /**
         * -----------------------------------------------------------
         * BLOQUE 1: ACCIONES DEL MODAL DE DESPACHO
         * -----------------------------------------------------------
         */

        if ($action === 'get_recursos_reparto') {
            $movimiento_id = $_REQUEST['id'] ?? 0;
            $detalle = $entregaM->getDetalleParaDespacho($movimiento_id);
            
            if (!$detalle) {
                echo json_encode(["success" => false, "message" => "Movimiento no encontrado"]);
                exit;
            }
            echo json_encode(["success" => true, "data" => ["entrega" => $detalle]]);
            exit; 
        }
        

        if ($action === 'get_recursos_sucursal') {
            $almacen_id = intval($_GET['almacen_id'] ?? 0);
            echo json_encode([
                "success"  => true,
                "unidades" => $vehiculoM->listarPorAlmacen($almacen_id),
                "choferes" => $trabajadorM->listarPorAlmacen($almacen_id)
            ]);
            exit;
        }

        if ($action === 'guardar_reparto') {
            if (empty($_POST['vehiculo_id']) || empty($_POST['chofer_id']) || empty($_POST['movimiento_id'])) {
                throw new Exception("Faltan datos obligatorios.");
            }

            $vehiculo_id = intval($_POST['vehiculo_id']);
            $rutaActiva = $repartoM->buscarRutaAbierta($vehiculo_id);

            if ($rutaActiva) {
                $_POST['folio_viaje'] = $rutaActiva['viaje_folio'];
            } else {
                $_POST['folio_viaje'] = "RUT-" . date('ymd') . "-" . str_pad($vehiculo_id, 2, "0", STR_PAD_LEFT) . "-" . rand(10, 99);
                if (method_exists($vehiculoM, 'actualizarEstado')) {
                    $vehiculoM->actualizarEstado($vehiculo_id, 'en_ruta');
                }
            }

            $reparto_id = $repartoM->iniciarReparto($_POST);
            echo json_encode([
                'success' => true, 
                'message' => '¡Logística confirmada!',
                'folio'   => $_POST['folio_viaje']
            ]);
            exit;
        }
        /**
         * -----------------------------------------------------------
         * BLOQUE: DETALLE EXTENDIDO PARA MONITOR (MODAL FLECHITA)
         * -----------------------------------------------------------
         */
   // --- ACCIÓN: OBTENER DETALLE DE TRAZABILIDAD (MONITOR) ---
if ($action === 'get_detalle_trazabilidad') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    $tipo = $_GET['tipo'] ?? 'MOSTRADOR';
    $id   = intval($_GET['id']);

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
                "success"        => true,
                "tipo_procesado" => $tipo,
                "data"           => $data
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
}
/**
         * -----------------------------------------------------------
         * BLOQUE: DETALLE PARA HOJA DE RUTA (NUEVO MODAL AZUL)
         * -----------------------------------------------------------
         */
        if ($action === 'get_ruta_entrega_venta') {

    if (ob_get_level()) ob_clean();

    header('Content-Type: application/json');

    $idVenta = (int)($_REQUEST['idVenta'] ?? 0);

    // folio ruta
    $id = trim($_REQUEST['id'] ?? '');

    if ($idVenta <= 0 || empty($id)) {

        echo json_encode([
            "success" => false,
            "message" => "Datos de ruta inválidos."
        ]);

        exit;
    }

    try {

        $data = $repartoM->obtenerRutaDeEntregaDeVenta(
            $idVenta,
            $id
        );

        if (!empty($data)) {

            echo json_encode([
                "success" => true,
                "data" => $data
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "No se encontraron entregas para el folio: " . $id
            ]);
        }

    } catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "message" => "Error en el servidor: " . $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'get_ruta_entrega_por_despacho') {

    if (ob_get_level()) ob_clean();

    header('Content-Type: application/json');

    $entrega_id = (int)($_REQUEST['entrega_id'] ?? 0);

    // folio ruta
    $id = trim($_REQUEST['id'] ?? '');

    if ($entrega_id <= 0 || empty($id)) {

        echo json_encode([
            "success" => false,
            "message" => "Datos de ruta inválidos."
        ]);

        exit;
    }

    try {

        $data = $repartoM->obtenerRutaDeEntregaPorEntrega (
            $entrega_id,
            $id
        );

        if (!empty($data)) {

            echo json_encode([
                "success" => true,
                "data" => $data
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "No se encontraron entregas para el folio: " . $id
            ]);
        }

    } catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "message" => "Error en el servidor: " . $e->getMessage()
        ]);
    }

    exit;
}
            if ($action === 'get_repartos_venta') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    // Cambiamos a string porque el folio de viaje (RUT-XXXX) no es un entero
    $id= trim($_REQUEST['id'] ?? '');

    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "Folio de viaje no válido."]);
        exit;
    }

    try {
        // El modelo busca por el string del folio
        $data = $repartoM->obtenerEntregasPorVenta($id);

        if (!empty($data)) {
            echo json_encode([
                "success" => true,
                "data"    => $data
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se encontraron entregas para el folio: " . $id
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error en el servidor: " . $e->getMessage()
        ]);
    }
    exit;
}
   if ($action === 'get_repartos_entrega') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    // Cambiamos a string porque el folio de viaje (RUT-XXXX) no es un entero
    $id= trim($_REQUEST['id'] ?? '');

    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "Folio de viaje no válido."]);
        exit;
    }

    try {
        // El modelo busca por el string del folio
        $data = $repartoM->obtenerEntregas($id);

        if (!empty($data)) {
            echo json_encode([
                "success" => true,
                "data"    => $data
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se encontraron entregas para el folio: " . $id
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error en el servidor: " . $e->getMessage()
        ]);
    }
    exit;
}
        if ($action === 'get_detalle_ruta_completa') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    // Cambiamos a string porque el folio de viaje (RUT-XXXX) no es un entero
    $folio_viaje = trim($_REQUEST['id'] ?? '');

    if (empty($folio_viaje)) {
        echo json_encode(["success" => false, "message" => "Folio de viaje no válido."]);
        exit;
    }

    try {
        // El modelo busca por el string del folio
        $data = $repartoM->obtenerViajesLogistica($folio_viaje);

        if (!empty($data)) {
            echo json_encode([
                "success" => true,
                "data"    => $data
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se encontraron entregas para el folio: " . $folio_viaje
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error en el servidor: " . $e->getMessage()
        ]);
    }
    exit;
}
/**
         * NUEVA ACCIÓN: Obtener el resumen de quién y cómo entregó la mercancía
         * Se activa desde el botón del "Ojito" en la tabla.
         */
       if ($action === 'get_resumen_despacho') {
    $movimiento_id = intval($_REQUEST['id'] ?? 0);
    
    // 1. Obtenemos el resumen completo (que ya trae cliente y tripulantes básicos)
    $resumen = $repartoM->obtenerHistorialFisico($movimiento_id);
    
    if ($resumen) {
        // 2. IMPORTANTE: Solo sobreescribe tripulantes si el array viene vacío 
        // y realmente es un reparto de logística.
        if (empty($resumen['tripulantes']) && !empty($resumen['reparto_id'])) {
            $resumen['tripulantes'] = $repartoM->getTripulantesPorReparto($resumen['reparto_id']);
        }

        // 3. Forzamos el header para que el JS reciba JSON puro
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "data" => $resumen]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "No se encontraron registros de salida física."]);
    }
    exit;
}
/**
         * -----------------------------------------------------------
         * BLOQUE: MONITOR DE ENTREGAS COMPLETADAS
         * -----------------------------------------------------------
         */
if ($action === 'get_monitor_entregas') {
    header('Content-Type: application/json');

    $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : intval($_SESSION['almacen_id'] ?? 0);
    $inicio = isset($_GET['inicio']) ? intval($_GET['inicio']) : 0;
    $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 25;
    
    // CORRECCIÓN: "Y" mayúscula para asegurar 4 dígitos (ej: 2026-06-10)
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin    = $_GET['fecha_fin'] ?? date('Y-m-d');
    
    $registros = $repartoM->getMonitorEntregas($almacen_id, $inicio, $limite, $fecha_inicio, $fecha_fin);
    
    if (is_array($registros)) {
        echo json_encode([
            "success" => true, 
            "data" => $registros,
            "count" => count($registros),
            "offset" => $inicio
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Error al obtener la trazabilidad de entregas."
        ]);
    }
    exit;
}
        /**
         * NOTA: Para el modal del "Ojito" en el monitor, 
         * usaremos la acción 'get_resumen_despacho' que ya tienes arriba, 
         * solo asegúrate de que tu función 'obtenerHistorialFisico' en el modelo 
         * reciba el 'movimiento_id' y retorne los datos de la ruta si el movimiento 
         * está asociado a una.
         */
        /**
         * -----------------------------------------------------------
         * BLOQUE 2: GESTIÓN DE VISTA Y FILTROS
         * -----------------------------------------------------------
         */
       if ($action === 'cancelar_viaje_completo') {
    $folio_viaje = $_REQUEST['folio'] ?? '';
    $vehiculo_id = intval($_REQUEST['vehiculo_id'] ?? 0);

    if (empty($folio_viaje) || $vehiculo_id === 0) {
        throw new Exception("Datos de viaje insuficientes para cancelar.");
    }

    $repartoM->cancelarViajeCompleto($folio_viaje, $vehiculo_id, $_SESSION['usuario_id']);

    if (method_exists($vehiculoM, 'actualizarEstado')) {
        $vehiculoM->actualizarEstado($vehiculo_id, 'disponible');
    }

    // 🛠️ SOLUCIÓN DE DEPURACIÓN:
    // Limpia cualquier 'echo', espacio en blanco o basura que se haya colado antes
    if (ob_get_length()) ob_clean(); 
    
    // Forzamos al navegador a entender que es un JSON estricto
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => true, 
        'message' => 'Ruta anulada y unidades liberadas correctamente.'
    ]);
    exit;
}
if ($action === 'get_detalles_viaje') {
    $folio = $_GET['folio'] ?? '';
    $detalles = $repartoM->getDetallesViaje($folio);
    
    if ($detalles) {
        echo json_encode(["success" => true, "data" => $detalles]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontró información"]);
    }
    exit;
}




/**
 * -----------------------------------------------------------
 * BLOQUE: EDICIÓN DINÁMICA DE RUTA (MODAL EDITAR)
 * -----------------------------------------------------------
 */



if ($action === 'guardar_cambios_viaje') {
    // Limpieza radical para asegurar JSON puro
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json; charset=utf-8');

    try {
        $folio = $_POST['viaje_folio'] ?? '';
        $vehiculo_id = intval($_POST['vehiculo_id'] ?? 0);
        $chofer_id = intval($_POST['chofer_id'] ?? 0);
        $tripulantes = intval($_POST['tripulantes'] ?? 0);

        // Validaciones críticas
        if (empty($folio)) throw new Exception("El folio del viaje es requerido.");
        if ($vehiculo_id <= 0) throw new Exception("Seleccione un vehículo válido.");
        if ($chofer_id <= 0) throw new Exception("Debe asignar un chofer responsable.");

        // Aseguramos que tripulantes sea siempre un array (aunque venga vacío)
        

        $datos = [
            'viaje_folio' => $folio,
            'usuario'     =>$_SESSION['usuario_id'],
            'chofer_id'   => $chofer_id,
            'vehiculo_id' => $vehiculo_id,
            'tripulantes' => $tripulantes,
            'destinos'    => []
        ];

        // Procesar el JSON de direcciones enviado desde el JS
        if (!empty($_POST['destinos_data'])) {
            // Quitamos posibles escapes de comillas que PHP añade a veces
            $json_raw = stripslashes($_POST['destinos_data']);
            $datos['destinos'] = json_decode($json_raw, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error en el formato de los destinos: " . json_last_error_msg());
            }
        }

        // Ejecución en el Modelo
        $resultado = $repartoM->guardarCambiosViaje($datos);

        if ($resultado) {
            echo json_encode([
                "success" => true, 
                "message" => "Logística actualizada correctamente. Folio: $folio"
            ]);
        } else {
            throw new Exception("No se pudieron aplicar los cambios en la base de datos.");
        }

    } catch (Exception $e) {
        if (ob_get_level()) ob_clean();
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage()
        ]);
    }
    exit; 
}

/**
 * 2. QUITAR ENTREGA DE RUTA
 * Elimina un material de la logística y lo libera para nueva programación
 */
if ($action === 'quitar_entrega_ruta') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        $mov_id = intval($_POST['movimiento_id'] ?? 0);

        if ($mov_id <= 0) {
            throw new Exception("ID de movimiento (entrega_venta_id) no válido.");
        }

        // Llamada al Modelo
        $repartoM->quitarEntregaDeRuta($mov_id);

        echo json_encode([
            "success" => true, 
            "message" => "Material removido y disponible para nueva asignación."
        ]);

    } catch (Exception $e) {
        if (ob_get_level()) ob_clean();
        echo json_encode([
            "success" => false, 
            "message" => "No se pudo remover el material: " . $e->getMessage()
        ]);
    }
    exit;
}





if ($action === 'actualizar_logistica_completa') {
    // Esta es la función que procesa los cambios del modal (Chofer, Ayudantes y Destinos)
    $res = $repartoM->actualizarLogisticaCompleta($_POST);
    echo json_encode(["success" => true, "message" => "Ruta actualizada correctamente"]);
    exit;
}
// --- 
       if ($action === 'listar_pendientes_ruta') {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('y-m-01');
$fecha_fin    = $_GET['fecha_fin'] ?? date('y-m-d');
    // Tomamos el almacen_id del GET (filtro) o de la sesión (por defecto)
    $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : intval($_SESSION['almacen_id'] ?? 0);
    
    // El modelo ya devuelve el array con 'cantidad_display' incluido
    $lista = $entregaM->listarSoloDespachadosPatioFecha($almacen_id, $fecha_inicio,
    $fecha_fin
);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'data' => $lista
    ]);
    exit;
}
  if ($action === 'listar_viajes_activos') {
    // 1. Limpiamos cualquier salida previa para evitar JSON corrupto
    if (ob_get_length()) ob_clean();
    
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
        'data'    => $viajes,
        'count'   => count($viajes),
        'debug'   => [
            'almacen_solicitado' => $almacen_id,
            'metodo' => isset($_GET['almacen_id']) ? 'GET' : 'SESSION'
        ]
    ]);
    exit;
}
        if ($action === 'finalizar_viaje') {
            $v_id = intval($_POST['vehiculo_id']);
            $viaje_folio = $_POST['viaje_folio'];
            $repartoM->finalizarViajeLogistica($v_id, $viaje_folio);
            $vehiculoM->actualizarEstado($v_id, 'disponible');
            echo json_encode(['success' => true, 'message' => '¡Viaje finalizado!']);
            exit;
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA INICIAL PARA EL RENDERING ---
$almacen_sesion = intval($_SESSION['almacen_id'] ?? 0);
$unidadesLibres = $vehiculoM->listarPorAlmacen($almacen_sesion);
$totalUnidadesLibres = count($unidadesLibres);
$es_admin =  $_SESSION['almacen_id'] === 0 ?true:false;

// Obtener lista de almacenes para el select del Administrador
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 

$tituloPagina = "Gestión de Repartos y Logística";

require_once __DIR__ . '/../views/repartos_view.php';