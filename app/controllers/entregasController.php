<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/entregasModel.php'; 
// Asegúrate de que estos nombres de archivo de modelo sean los correctos en tu carpeta models
require_once __DIR__ . '/../models/RepartosModel.php'; 
require_once __DIR__ . '/../models/vehiculos_model.php'; 
require_once __DIR__ . '/../models/trabajadores_model.php'; 
require_once __DIR__ . '/../controllers/LayoutController.php';

protegerPagina('entregas'); 
$paginaActual = 'entregas';

// Instanciación de modelos
$modelo      = new EntregaModel($conexion);
$repartoM    = new RepartoModel($conexion);
$vehiculoM   = new VehiculoModel($conexion);
$trabajadorM = new TrabajadorModel($conexion);

// Datos de sesión
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin        = (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1); 

if(!isset($_SESSION['id_usuario']) && isset($_SESSION['usuario_id'])){
    $_SESSION['id_usuario'] = $_SESSION['usuario_id'];
}

// --- MANEJO DE PETICIONES AJAX (GET) ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    try {
        switch ($_GET['ajax']) {
            case 'listar':
                $entregas = $modelo->listarSalidasPendientes($_GET, $almacen_usuario, $es_admin);
                $totales  = $modelo->obtenerTotalesSalidas($_GET, $almacen_usuario, $es_admin);
                echo json_encode(
                ['data' => $entregas,
                'ganancias'=> $totales]);
                break;

            case 'get_recursos_reparto':
                $movimiento_id = $_GET['id'] ?? 0;
                $detalle = $modelo->getDetalleParaDespacho($movimiento_id);
                if (!$detalle) {
                    echo json_encode(["success" => false, "message" => "Movimiento no encontrado"]);
                } else {
                    echo json_encode(["success" => true, "data" => ["entrega" => $detalle]]);
                }
                break;
case 'get_datos_vehiculo':

    header('Content-Type: application/json'); // 🔥 IMPORTANTE

    $vehiculo_id = $_GET['vehiculo_id'] ?? 0;

    if (!$vehiculo_id) {
        echo json_encode([
            "success" => false,
            "message" => "Vehículo no válido"
        ]);
        exit; // 🔥 corta ejecución
    }

    $data = $modelo->getDatosPorVehiculo($vehiculo_id);

    if (!$data) {
        echo json_encode([
            "success" => false,
            "message" => "Sin datos para este vehículo"
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "data" => $data
        ]);
    }

    exit; // 🔥 MUY IMPORTANTE
break;;
            case 'get_recursos_sucursal':
                $almacen_id = intval($_GET['almacen_id'] ?? 0);
                echo json_encode([
                    "success"  => true,
                    "unidades" => $vehiculoM->listarPorAlmacen($almacen_id),
                    "choferes" => $trabajadorM->listarPorAlmacen($almacen_id),
                    "trabajadoresDisponibles" => $trabajadorM->listarTrabajadoresDisponiblesPorAlmacen($almacen_id),
                ]);
                break;
  case 'obtener_id_almacen':
                $id = intval($_GET['id'] ?? 0);
                echo json_encode([
                    "success"  => true,
                    "almacen" => $modelo->obtener_almecen_id($id)
                       ]);
                break;
            case 'simular':
                $id = intval($_GET['id'] ?? 0);
                echo json_encode($modelo->simularDespachoLotes($id));
                break;
case 'get_resumen_despacho':
                $movimiento_id = intval($_GET['id'] ?? 0);
                $resumen = $repartoM->obtenerHistorialFisico($movimiento_id);
                
                if ($resumen) {
                    if (empty($resumen['tripulantes']) && !empty($resumen['reparto_id'])) {
                        $resumen['tripulantes'] = $repartoM->getTripulantesPorReparto($resumen['reparto_id']);
                    }
                    echo json_encode(["success" => true, "data" => $resumen]);
                } else {
                    echo json_encode(["success" => false, "message" => "No se encontró registro."]);
                }
                break;
                case 'get_ids_pendientes_venta':
        // Obtiene solo los IDs [193, 194...] de una venta para procesarlos
        $venta_id = intval($_GET['venta_id'] ?? 0);
        $ids = $repartoM->listarIdsPendientesPorVenta($venta_id);
        echo json_encode(['success' => true, 'ids' => $ids]);
        break;
        case 'get_productos_para_despacho':
        // Obtiene solo los IDs [193, 194...] de una venta para procesarlos
        $venta_id = intval($_GET['venta_id'] ?? 0);
        $ids = $repartoM->listarProductos($venta_id);
        echo json_encode(['success' => true, 'ids' => $ids]);
        break;
        // ... dentro del switch ($_GET['ajax']) ...
case 'entregas_pendientes':
    try {
        // 1. Validar entrada
        $venta_id = intval($_GET['venta_id'] ?? 0);
        if ($venta_id <= 0) {
            throw new Exception("ID de venta no válido.");
        }

        // 2. Llamar a tu función del modelo
        $pendientes = $repartoM->listarDespachosPendientesPorVenta($venta_id);
        
        // 3. Responder según el resultado
        if (empty($pendientes)) {
            echo json_encode([
                'success' => false, 
                'message' => "No hay productos pendientes para despacho en esta venta (o ya están en ruta)."
            ]);
        } else {
            // Extraemos solo los IDs en un array simple [193, 194, ...] si así lo prefiere tu JS
            // o enviamos el array de objetos completo.
            echo json_encode([
                'success' => true, 
                'ids' => array_column($pendientes, 'id'), // Array simple de IDs
                'data' => $pendientes,                    // Datos completos
                'total' => count($pendientes)
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    break;

    case 'simular_masivo':
        // Muestra qué lotes se verán afectados antes de hacer el movimiento real
        // Recibe: entregasController.php?ajax=simular_masivo&ids[]=193&ids[]=194
        $ids = $_GET['ids'] ?? [];
        if (empty($ids)) throw new Exception("No hay IDs para simular.");
        echo json_encode($repartoM->simularDespachoLotesMasivo($ids));
        break;
         case 'despachar':
             
              $prodId = $_GET['prodId'] ?? [];
                $almacen = $_GET['almacen'] ?? [];
        // Muestra qué lotes se verán afectados antes de hacer el movimiento real
        // Recibe: entregasController.php?ajax=simular_masivo&ids[]=193&ids[]=194
        
        
        echo json_encode($repartoM->DespachoLotesMasivo($prodId,$almacen));
        break;
case 'obtenerAuditoriaVenta':
    try {
        // 1. Limpieza de salida para evitar errores de JSON
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');

        // 2. Validamos el ID de la venta (ahora buscamos por Venta, no por Movimiento)
        $idVenta = intval($_GET['id_venta'] ?? 0);
        if ($idVenta <= 0) {
            throw new Exception("ID de venta no válido para la auditoría.");
        }

        // 3. Llamamos al método que creamos en el modelo
        // Este ya regresa el array con ['productos', 'gran_total_costo', etc.]
        $reporte = $modelo->obtenerAuditoriaFinancieraVenta($idVenta);

        if (empty($reporte['productos'])) {
            throw new Exception("No se encontraron productos o lotes despachados para esta venta.");
        }

        // 4. Respuesta exitosa
        echo json_encode([
            'success' => true,
            'data'    => $reporte
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
    break;

           case 'imprimirGanancia':
case 'imprimir': // Unificamos lógica si ambos devuelven JSON de impresión
    try {
        // Limpiamos cualquier salida previa para asegurar un JSON puro
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');

        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception("ID de movimiento no válido.");
        }

        // Decidimos qué método del modelo llamar según el parámetro 'ajax'
        if ($_GET['ajax'] === 'imprimirGanancia') {
            $datos = $modelo->obtenerDatosVentaGananciaImpresion($id);
        } else {
            $datos = $modelo->obtenerDatosImpresion($id);
        }

        if (!$datos) {
            throw new Exception("No se encontraron registros para el movimiento #$id.");
        }

        // Devolvemos el éxito con los datos procesados
        echo json_encode([
            'success' => true, 
            'data'    => $datos
        ]);

    } catch (Exception $e) {
        // En caso de error, devolvemos un 400 o 500 y el mensaje JSON
        http_response_code(400); 
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    exit; // Importante para detener la ejecución del resto del script
    break;
    
                
            default:
                throw new Exception("Acción AJAX no reconocida.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit; 
}

// --- MANEJO DE PETICIONES AJAX (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['ajax'] ?? '';
    header('Content-Type: application/json');

    try {
        // Validación centralizada de sesión
        $id_usuario_sesion = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? 0;
        if ($id_usuario_sesion <= 0) {
            throw new Exception("Sesión expirada o usuario no identificado.");
        }

        /// 1. DESPACHO MASIVO POR VENTA (Lógica procesarDespachoFisicoMasivo)
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['ajax'] ?? '';
    header('Content-Type: application/json');

    try {
        // Validación centralizada de sesión
        $id_usuario_sesion = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? 0;
        if ($id_usuario_sesion <= 0) {
            throw new Exception("Sesión expirada o usuario no identificado.");
        }

        /// 1. DESPACHO MASIVO POR VENTA (Lógica procesarDespachoFisicoMasivo)
        if ($accion === 'despachar_venta_completa') {
            // ✅ Headers ya están arriba, ob_clean opcional si no hay output previo
            
            try {
                $ids = $_POST['ids_movimientos'] ?? [];
                $tipoLogistica = $_POST['tipo_logistica'] ?? 'patio';
                $vehiculoId = intval($_POST['vehiculo_id'] ?? 0);
                $choferId = intval($_POST['chofer_id'] ?? 0);
                $usuarioSistemaId = $id_usuario_sesion;
                $direccion = $_POST['direccion'] ?? 'Entrega en Obra';
                $tripulantes = $_POST['tripulantes'] ?? [];

                if (empty($ids)) {
                    throw new Exception("No se seleccionaron productos para el despacho masivo.");
                }

                $resultadoDespacho = $repartoM->procesarDespachoFisicoMasivo($ids);

                if ($resultadoDespacho['success']) {
                    $folioViaje = "";

                    if ($tipoLogistica === 'ruta') {
                        $rutaActiva = $repartoM->buscarRutaAbierta($vehiculoId);
                        
                        if ($rutaActiva) {
                            $folioViaje = $rutaActiva['viaje_folio'];
                        } else {
                            $folioViaje = "RUT-" . date('ymd') . "-" . str_pad($vehiculoId, 2, "0", STR_PAD_LEFT) . "-" . rand(10, 99);
                            
                            if (method_exists($vehiculoM, 'actualizarEstado')) {
                                $vehiculoM->actualizarEstado($vehiculoId, 'en_ruta');
                            }
                        }
                    }

                    foreach ($ids as $idMov) {
                        $datosReparto = [
                            'movimiento_id'      => $idMov,
                            'vehiculo_id'        => $vehiculoId,
                            'chofer_id'          => $choferId,
                            'direccion_entrega'  => $direccion, 
                            'tripulantes'        => $tripulantes,
                            'folio_viaje'        => $folioViaje,
                            'usuario_sistema_id' => $usuarioSistemaId,
                            'observaciones'      => 'Entrega Directa en Patio (Despacho Masivo)'
                        ];

                        try {
                            if ($tipoLogistica === 'ruta') {
                                $repartoM->iniciarReparto($datosReparto);
                            } else {
                                $datosReparto['vehiculo_id'] = 999;
                                $repartoM->entregarEnPatioCliente($datosReparto);
                            }
                        } catch (Exception $e) {
                            error_log("Error en logística individual MovID {$idMov}: " . $e->getMessage());
                            continue; 
                        }
                    }

                    echo json_encode([
                        'success' => true,
                        'message' => '¡Logística masiva confirmada!',
                        'folio'   => $folioViaje
                    ]);
                } else {
                    echo json_encode($resultadoDespacho);
                }

            } catch (Throwable $t) {
                echo json_encode([
                    'success' => false,
                    'message' => "Error de servidor: " . $t->getMessage()
                ]);
            }
            exit; 
        }
if ($accion === 'despachar_venta_completaConLotes') {
    // ✅ Headers ya están arriba, ob_clean opcional si no hay output previo
    
    try {
        $ids = $_POST['ids_movimientos'] ?? [];
        $lotes = $_POST['lotes'] ?? [];
        $tipoLogistica = $_POST['tipo_logistica'] ?? 'patio';
        $vehiculoId = intval($_POST['vehiculo_id'] ?? 0);
        $choferId = intval($_POST['chofer_id'] ?? 0);
        $usuarioSistemaId = $id_usuario_sesion;
        $direccion = $_POST['direccion'] ?? 'Entrega en Obra';
        
        // 🔹 CAMBIO: Ahora tripulantes se castea directamente a entero (ID)
        $tripulanteId = intval($_POST['tripulante_id'] ?? 0);
          
         
        if (empty($ids)) {
            throw new Exception("No se seleccionaron productos para el despacho masivo.");
        }
       

        $resultadoDespacho = $repartoM->procesarDespachoFisicoMasivoConlotes($ids, $lotes);

        if ($resultadoDespacho['success']) {
            $folioViaje = "";

            if ($tipoLogistica === 'ruta') {
                $rutaActiva = $repartoM->buscarRutaAbierta($vehiculoId);
                
                if ($rutaActiva) {
                    $folioViaje = $rutaActiva['viaje_folio'];
                } else {
                    $folioViaje = "RUT-" . date('ymd') . "-" . str_pad($vehiculoId, 2, "0", STR_PAD_LEFT) . "-" . rand(10, 99);
                    
                    if (method_exists($vehiculoM, 'actualizarEstado')) {
                        $vehiculoM->actualizarEstado($vehiculoId, 'en_ruta');
                    }
                }
            }   

            foreach ($ids as $idMov) {
                $datosReparto = [
                    'movimiento_id'      => $idMov,
                    'vehiculo_id'        => $vehiculoId,
                    'chofer_id'          => $choferId,
                    'direccion_entrega'  => $direccion, 
                   
                    'folio_viaje'        => $folioViaje,
                    'usuario_sistema_id' => $usuarioSistemaId,
                    'observaciones'      => 'Entrega Directa en Patio (Despacho Masivo)'
                ];
 

               
                    if ($tipoLogistica === 'ruta') {
                       
                       $reparto_id_generado = $repartoM->iniciarReparto($datosReparto);
        
        // 2. AHORA PUEDES USARLO PARA LO QUE NECESITES
        // (Por ejemplo, guardar el tripulante desde aquí afuera)
     
        if ($tripulanteId > 0 && $tripulanteId !== $choferId) {
            
             $repartoM->guardarTripulante($reparto_id_generado, $tripulanteId,$choferId);
        }
                    } else {
                        
                    $folioViaje = "EN_PAT" . date('ymd') . "-" . $idMov;
                    
                        $datosReparto['vehiculo_id'] = 999;$datosReparto['folio_viaje'] = $folioViaje;
                        $repartoM->iniciarRepartoPatio($datosReparto);
                    }
               
            }

            echo json_encode([
                'success' => true,
                'message' => '¡Logística masiva confirmada!',
                'folio'   => $folioViaje
            ]);
        } else {
            echo json_encode($resultadoDespacho);
        }

    } catch (Throwable $t) {
        echo json_encode([
            'success' => false,
            'message' => "Error de servidor: " . $t->getMessage()
        ]);
    }
    exit; 
}








       if ($accion === 'despachar_venta_completaFaltantesEntrega') {
    while (ob_get_level()) ob_end_clean(); // ✅ LIMPIEZA CRÍTICA
    
    try {
        $ids = $_POST['ids_movimientos'] ?? [];
        $tipoLogistica = $_POST['tipo_logistica'] ?? 'patio';
        $vehiculoId = intval($_POST['vehiculo_id'] ?? 0);
        $choferId = intval($_POST['chofer_id'] ?? 0);
        $usuarioSistemaId = $id_usuario_sesion;
        $direccion = $_POST['direccion'] ?? 'Entrega en Obra';
        $tripulantes = $_POST['tripulantes'] ?? []; // ✅ Array de IDs

        if (empty($ids)) {
            throw new Exception("No se seleccionaron productos para asignar logística.");
        }

        $folioViaje = "";
        if ($tipoLogistica === 'ruta') {
            $rutaActiva = $repartoM->buscarRutaAbierta($vehiculoId);
            if ($rutaActiva) {
                $folioViaje = $rutaActiva['viaje_folio'];
            } else {
                $folioViaje = "RUT-" . date('ymd') . "-" . str_pad($vehiculoId, 2, "0", STR_PAD_LEFT) . "-" . rand(10, 99);
                if (method_exists($vehiculoM, 'actualizarEstado')) {
                    $vehiculoM->actualizarEstado($vehiculoId, 'en_ruta');
                }
            }
        }

        foreach ($ids as $idMov) {
            $datosReparto = [
                'movimiento_id'      => intval($idMov),
                'vehiculo_id'        => $vehiculoId,
                'chofer_id'          => $choferId,
                'direccion_entrega'  => $direccion,
                'tripulantes'        => $tripulantes, // ✅ Array pasa directo
                'folio_viaje'        => $folioViaje,
                'usuario_sistema_id' => $usuarioSistemaId,
                'observaciones'      => 'Asignación Logística (Faltantes)'
            ];

            try {
                if ($tipoLogistica === 'ruta') {
                    $repartoM->iniciarReparto($datosReparto);
                } else {
                    $datosReparto['vehiculo_id'] = 999;
                    $repartoM->entregarEnPatioCliente($datosReparto);
                }
            } catch (Exception $e) {
                error_log("Error MovID {$idMov}: " . $e->getMessage());
                continue;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Logística de faltantes confirmada correctamente.',
            'folio'   => $folioViaje
        ]);

    } catch (Throwable $t) {
        echo json_encode([
            'success' => false,
            'message' => "Error de servidor: " . $t->getMessage()
        ]);
    }
    exit;
}

        // 2. DESPACHO INDIVIDUAL (Lógica original)
        elseif ($accion === 'despachar') {
            $id_movimiento = intval($_POST['id_movimiento'] ?? 0);
            if ($id_movimiento <= 0) {
                throw new Exception("ID de movimiento no válido.");
            }
            echo json_encode($modelo->procesarDespachoFisico($id_movimiento));
        } 
        
        // 3. LOGÍSTICA Y RUTAS DE REPARTO
        elseif ($accion === 'guardar_reparto') {
            if (empty($_POST['vehiculo_id']) || empty($_POST['chofer_id']) || empty($_POST['movimiento_id'])) {
                throw new Exception("Faltan datos obligatorios para el reparto.");
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

            $repartoM->iniciarReparto($_POST);
            echo json_encode([
                'success' => true, 
                'message' => '¡Logística confirmada!',
                'folio'   => $_POST['folio_viaje']
            ]);
        }

        // 4. ENTREGA DIRECTA AL CLIENTE (PATIO)
        elseif ($accion === 'entregar_en_patio') {
            if (empty($_POST['movimiento_id']) || empty($_POST['chofer_id'])) {
                throw new Exception("Error: Debe indicar el movimiento y el personal responsable de la entrega.");
            }

            // Datos forzados para la consistencia de la base de datos
            $_POST['usuario_sistema_id'] = $id_usuario_sesion;
            $_POST['vehiculo_id'] = 999; // Vehículo virtual (Patio)

            $resultado = $repartoM->entregarEnPatioCliente($_POST);
            echo json_encode($resultado);
        }

        // 5. CANCELAR DESPACHO FÍSICO (REVERSA DE STOCK)
        elseif ($accion === 'cancelarDespachoFisico') {
            while (ob_get_level()) ob_end_clean();

            $id_movimiento = intval($_POST['id_movimiento'] ?? 0);
            
            if ($id_movimiento <= 0) {
                throw new Exception("ID de movimiento no válido para la reversa.");
            }

            $resultado = $modelo->cancelarDespachoFisico($id_movimiento);
            
            echo json_encode($resultado);
            exit;
        }

        // CASO POR DEFECTO
        else {
            throw new Exception("Acción POST '$accion' no reconocida.");
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($accion === 'despachar_venta_completaFaltantesEntrega') {
    // 1. Limpieza absoluta de salida para asegurar JSON puro
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    try {
        $ids = $_POST['ids_movimientos'] ?? [];
        $tipoLogistica = $_POST['tipo_logistica'] ?? 'patio';
        $vehiculoId = intval($_POST['vehiculo_id'] ?? 0);
        $choferId = intval($_POST['chofer_id'] ?? 0);
        $usuarioSistemaId = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? 0;
        $direccion = $_POST['direccion'] ?? 'Entrega en Obra';
        $tripulantes = $_POST['tripulantes'] ?? [];

        if (empty($ids)) {
            throw new Exception("No se seleccionaron productos para asignar logística.");
        }

        // --- A. LÓGICA DE FOLIO DE VIAJE ---
        $folioViaje = "";
        if ($tipoLogistica === 'ruta') {
            $rutaActiva = $repartoM->buscarRutaAbierta($vehiculoId);
            
            if ($rutaActiva) {
                $folioViaje = $rutaActiva['viaje_folio'];
            } else {
                $folioViaje = "RUT-" . date('ymd') . "-" . str_pad($vehiculoId, 2, "0", STR_PAD_LEFT) . "-" . rand(10, 99);
                if (method_exists($vehiculoM, 'actualizarEstado')) {
                    $vehiculoM->actualizarEstado($vehiculoId, 'en_ruta');
                }
            }
        }

        // --- B. BUCLE DE REGISTRO LOGÍSTICO ---
        foreach ($ids as $idMov) {
            $datosReparto = [
                'movimiento_id'      => intval($idMov),
                'vehiculo_id'        => $vehiculoId,
                'chofer_id'          => $choferId,
                'direccion_entrega'  => $direccion, 
                'tripulantes'        => $tripulantes,
                'folio_viaje'        => $folioViaje,
                'usuario_sistema_id' => $usuarioSistemaId,
                'observaciones'      => 'Asignación Logística (Faltantes)'
            ];

            try {
                if ($tipoLogistica === 'ruta') {
                    $repartoM->iniciarReparto($datosReparto);
                } else {
                    $datosReparto['vehiculo_id'] = 999; // ID Virtual para Patio
                    $repartoM->entregarEnPatioCliente($datosReparto);
                }
            } catch (Exception $e) {
                // Si falla un ID (ej. ya está en ruta), registramos el error y seguimos con los demás
                error_log("Error en logística individual MovID {$idMov}: " . $e->getMessage());
                continue; 
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Logística de faltantes confirmada correctamente.',
            'folio'   => $folioViaje
        ]);

    } catch (Throwable $t) {
        echo json_encode([
            'success' => false,
            'message' => "Error de servidor: " . $t->getMessage()
        ]);
    }
    exit; 
}
        // 2. DESPACHO INDIVIDUAL (Lógica original)
        elseif ($accion === 'despachar') {
            $id_movimiento = intval($_POST['id_movimiento'] ?? 0);
            if ($id_movimiento <= 0) {
                throw new Exception("ID de movimiento no válido.");
            }
            echo json_encode($modelo->procesarDespachoFisico($id_movimiento));
        } 
        
        // 3. LOGÍSTICA Y RUTAS DE REPARTO
        elseif ($accion === 'guardar_reparto') {
            if (empty($_POST['vehiculo_id']) || empty($_POST['chofer_id']) || empty($_POST['movimiento_id'])) {
                throw new Exception("Faltan datos obligatorios para el reparto.");
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

            $repartoM->iniciarReparto($_POST);
            echo json_encode([
                'success' => true, 
                'message' => '¡Logística confirmada!',
                'folio'   => $_POST['folio_viaje']
            ]);
        }

        // 4. ENTREGA DIRECTA AL CLIENTE (PATIO)
        elseif ($accion === 'entregar_en_patio') {
            if (empty($_POST['movimiento_id']) || empty($_POST['chofer_id'])) {
                throw new Exception("Error: Debe indicar el movimiento y el personal responsable de la entrega.");
            }

            // Datos forzados para la consistencia de la base de datos
            $_POST['usuario_sistema_id'] = $id_usuario_sesion;
            $_POST['vehiculo_id'] = 999; // Vehículo virtual (Patio)

            $resultado = $repartoM->entregarEnPatioCliente($_POST);
            echo json_encode($resultado);
        }
        // 5. CANCELAR DESPACHO FÍSICO (REVERSA DE STOCK)
        elseif ($accion === 'cancelarDespachoFisico') {
            // Limpieza absoluta para asegurar JSON puro
            while (ob_get_level()) ob_end_clean();
            header('Content-Type: application/json');

            $id_movimiento = intval($_POST['id_movimiento'] ?? 0);
            
            if ($id_movimiento <= 0) {
                throw new Exception("ID de movimiento no válido para la reversa.");
            }

            // Llamamos al método del modelo que devuelve el stock a los lotes
            $resultado = $modelo->cancelarDespachoFisico($id_movimiento);
            
            echo json_encode($resultado);
            exit;
        }

        // CASO POR DEFECTO
        else {
            throw new Exception("Acción POST '$accion' no reconocida.");
        }

    } catch (Exception $e) {
        // Respuesta unificada para errores capturados
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- CARGA NORMAL DE LA VISTA ---
$paginaActual = 'Entregas';
require_once __DIR__ . '/../views/entregas_view.php';