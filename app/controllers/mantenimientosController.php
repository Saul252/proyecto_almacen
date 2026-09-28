<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas de archivos base
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mantenimientos_model.php'; // Necesario para el selector de admin

require_once __DIR__ . '/../models/egresos/insumosModel.php';
protegerPagina('mantenimientos'); 

$vehiculoModel = new VehiculoModel($conexion);
$mantenimientosModel = new MantenimientosModel($conexion);

$insumosModel = new InsumosModel($conexion);
$almacenModel = new AlmacenModel($conexion); // Instanciamos para obtener la lista de sucursales
$paginaActual = 'mantenimientos';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'subirDocumento') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {

        $id = intval($_POST['vehiculo_id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("Vehículo inválido");
        }

        $documento = $_FILES['documento'] ?? null;

        if (!$documento || $documento['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir archivo");
        }

        // Carpeta destino
        $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/uploads/vehiculos_document/";

        if (!is_dir($ruta_carpeta)) {
            mkdir($ruta_carpeta, 0777, true);
        }

        if (!is_writable($ruta_carpeta)) {
            throw new Exception("La carpeta no tiene permisos de escritura");
        }

        // Nombre del archivo
        $ext = strtolower(pathinfo($documento['name'], PATHINFO_EXTENSION));
        $base = pathinfo($documento['name'], PATHINFO_FILENAME);

        $nombre = "vehiculo_" .
            preg_replace('/[^a-zA-Z0-9]/', '_', $base) .
            "_" .
            time() .
            "." .
            $ext;

        $destino = $ruta_carpeta . $nombre;

        if (!move_uploaded_file($documento['tmp_name'], $destino)) {
            throw new Exception("No se pudo guardar el archivo");
        }

        // Ruta que se guarda en BD
        $documento_url = "uploads/vehiculos_document/" . $nombre;

        // Guardar en BD
        $resultado = $vehiculoModel->subirDocumentoCompra(
            $id,
            $documento['name'],
            $documento_url
        );

        echo json_encode([
            'success' => true,
            'url' => $documento_url,
            'documento_id' => $resultado['documento_id'] ?? 0
        ]);

    } catch (Throwable $e) {

        error_log("ERROR SUBIR DOCUMENTO VEHICULO: " . $e->getMessage());

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'eliminarDocumento') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {

        
        
        $id = intval($_POST['id'] ?? 0);
        

        if ($id <= 0) {
            throw new Exception("Elemento inválida");
        }

       

        // 🔥 ELIMINAR EN BD
        $ok = $vehiculoModel->eliminarDocumento($id);

        if (!$ok) {
            throw new Exception("Error al guardar en BD");
        }

        echo json_encode([
            'success' => true,
            'url' => $id
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'insumok') {

  
    // resto del código...
   // 1. Activamos un búfer limpio y apagamos la salida automática de errores en pantalla
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    ob_start();

    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Mexico_City');
    
    try {
        // Validación de datos fijos
       
        $vehiculo_id = intval($_POST['carro_id'] ?? 0); $mantenimiento_id = intval($_POST['mantenimiento_id'] ?? 0); 
        $usuario_id  = intval($_SESSION['usuario_id'] ?? 1); 
        
       
        if ($vehiculo_id <= 0) {
            throw new Exception("Debe seleccionar un vehículo válido.");
        }

        // Recuperar arreglos dinámicos
        $items_insumos = $_POST['msign_items'] ?? [];
        $cantidades    = $_POST['msign_cant'] ?? [];

        if (empty($items_insumos) || empty($cantidades)) {
            throw new Exception("Debe seleccionar al menos un insumo con su cantidad.");
        }

        // 🚀 Ejecutar proceso PEPS en el Modelo
        // Asegúrate de que $mantenimientosModel esté instanciado antes de este bloque
        $entrega = $mantenimientosModel->registrarSalidaMantenimientoPEPS(
            $usuario_id, 
            $items_insumos, 
            $cantidades, 
            $vehiculo_id,$mantenimiento_id
        );
     
        if ($entrega && isset($entrega['success']) && $entrega['success'] === true) {
            // Limpiamos cualquier warning basura previo del búfer antes de imprimir
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => '¡Insumos asignados al mantenimiento con éxito aplicando PEPS!',
                'entrega' => $entrega['id'] ?? null
            ]);
        } else {
            throw new Exception("Error al procesar el inventario PEPS en la base de datos.");
        }

    } catch (Throwable $e) {
        // En caso de error, limpiamos el búfer y enviamos el JSON de error controlado
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    // Cerramos el flujo de ejecución inmediatamente para que nada más ensucie la respuesta
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $filtros = [
            'search'   => $_GET['f_search'] ?? '',
            
            'rango'    => $_GET['f_rango'] ?? 'todos',
            'inicio'   => $_GET['f_inicio'] ?? '',
            'fin'      => $_GET['f_fin'] ?? '',
            'almacen'  => $_GET['f_almacen'] ?? 0,
            'vehiculo'  => $_GET['f_vehiculo'] ?? 0,
            

        ];
        

        $rol_id = $_SESSION['rol_id'] ?? 2;
$rol = $_SESSION['rol_id'] ?? 2;
        $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;
 if($_SESSION['rol_id']==1||$_SESSION['rol_id']==3)
    {
        $id_almacen_usuario=0;
        $rol_id=1;
    }
        $data = $mantenimientosModel->obtenerMantenimientosFiltrados($filtros, $rol_id, $id_almacen_usuario);
        echo json_encode($data);

    } catch (Throwable $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'listarProximoMantenimiento') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
       
     $almacenSesion = intval($_SESSION['almacen_id'] ?? 0);
        $data = $mantenimientosModel->listarProximoMantenimiento($almacenSesion);
        echo json_encode($data);

    } catch (Throwable $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'obtenerInsumosSelect') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
         $almacen = $_GET['almacen'];
if($almacen>0)
    {
      $insumos= $insumosModel-> listarTodo($almacen);

        echo json_encode([
            'success' => true,
            'data' => $insumos,
            'almacen'=>$almacen
        ]);
    }
    else
    {
      

        echo json_encode([
            'success' => true,
            'data' => [],
            'almacen'=>$almacen
        ]);
    }

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
    
     
}

if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {
   if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        // 1. Validar que el ID exista y sea un número entero válido
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            http_response_code(400); // Bad Request
            throw new Exception("El ID de mantenimiento proporcionado no es válido.");
        }

        // 2. Consultar al modelo
        $data = $mantenimientosModel->obtenerMantenimiento($id);
        
        // 3. Validar si el modelo realmente encontró el registro
        if (!$data) {
            http_response_code(404); // Not Found
            throw new Exception("No se encontró el mantenimiento solicitado.");
        }

        // Si todo va bien, devolvemos la data con éxito
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);

    } catch (Throwable $e) {
        // Si el código de respuesta sigue siendo 200 (éxito), lo cambiamos a 400
        if (http_response_code() === 200) {
            http_response_code(400);
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
// --- CARGA DE LA VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $almacenSesion = intval($_SESSION['almacen_id'] ?? 0);

        // Si es admin global (0), ve todos. Si no, solo los de su sucursal.
        if($almacenSesion>0)
            {
                $vehiculos = $vehiculoModel->listarPorAlmacen($almacenSesion);

            }
            else{
                  $vehiculos = $vehiculoModel->listar();

            }
        
            
            $almacenes = $almacenModel->getAlmacenes($almacenSesion); // Para el select del modal
        $tipo=$almacenSesion==0?1:0;
 $insumos= $insumosModel-> listarTodo($almacenSesion);
        $tituloPagina = "Control de Flota";
        $vistaRuta = __DIR__ . '/../views/mantenimientos_view.php';
        
        if (file_exists($vistaRuta)) {
            require_once $vistaRuta;
        } else {
            throw new Exception("La vista 'vehiculos_view.php' no existe.");
        }
    } catch (Exception $e) {
        die("Error al cargar la flota: " . $e->getMessage());
    }

// 

}
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean();

    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Mexico_City');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            throw new Exception("No se recibieron datos.");
        }

        $almacen_id = intval($input['almacen_id'] ?? 0);
        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén no válido.");
        }

        // Validación básica de datos obligatorios
        $vehiculo_id = intval($input['vehiculo_id'] ?? 0);
        if ($vehiculo_id <= 0) {
            throw new Exception("ID de vehículo no válido.");
        }

        $usuario_id          = intval($_SESSION['usuario_id'] ?? 1); 
        $monto               = floatval($input['monto_depositado'] ?? 0);
        $referencia          = trim($input['referencia'] ?? '');
        $fecha_mantenimiento = $input['fecha_mantenimiento'] ?? date('Y-m-d');
        $fecha_proximo       = $input['fecha_proximo'] ?? date('Y-m-d');
        $metodo              = trim($input['metodo'] ?? 'efectivo');
        $razon               = trim($input['razon'] ?? '');
        $taller              = trim($input['taller'] ?? '');
        $tipo                = trim($input['tipo'] ?? '');
        $kilometraje         = intval($input['kilometraje'] ?? 0); // Mejor tratarlo como entero

        // Ejecutar el guardado (El modelo debe retornar el ID generado)
        $id_mantenimiento = $mantenimientosModel->guardar(
            $almacen_id, $usuario_id, $vehiculo_id, $monto, $referencia, 
            $fecha_mantenimiento, $fecha_proximo, $metodo, $razon, $taller, $tipo, $kilometraje
        );
       
        // Si el modelo retorna un ID válido (mayor a 0)
        if ($id_mantenimiento && $id_mantenimiento > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => '¡Mantenimiento guardado con éxito!',
                'id_mantenimiento' => $id_mantenimiento
            ]);
        } else {
            throw new Exception("Error al guardar el mantenimiento en la base de datos.");
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
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean();

    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Mexico_City');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            throw new Exception("No se recibieron datos.");
        }

        $almacen_id = intval($input['almacen_id'] ?? 0);
        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén no válido.");
        }

        // Validación básica de datos obligatorios
        $vehiculo_id = intval($input['vehiculo_id'] ?? 0);
        if ($vehiculo_id <= 0) {
            throw new Exception("ID de vehículo no válido.");
        }

        $usuario_id          = intval($_SESSION['usuario_id'] ?? 1); 
        $monto               = floatval($input['monto_depositado'] ?? 0);
        $referencia          = trim($input['referencia'] ?? '');
        $fecha_mantenimiento = $input['fecha_mantenimiento'] ?? date('Y-m-d');
        $fecha_proximo       = $input['fecha_proximo'] ?? date('Y-m-d');
        $metodo              = trim($input['metodo'] ?? 'efectivo');
        $razon               = trim($input['razon'] ?? '');
        $taller              = trim($input['taller'] ?? '');
        $tipo                = trim($input['tipo'] ?? '');
        $kilometraje         = intval($input['kilometraje'] ?? 0); // Mejor tratarlo como entero

        // Ejecutar el guardado (El modelo debe retornar el ID generado)
        $id_mantenimiento = $mantenimientosModel->guardar(
            $almacen_id, $usuario_id, $vehiculo_id, $monto, $referencia, 
            $fecha_mantenimiento, $fecha_proximo, $metodo, $razon, $taller, $tipo, $kilometraje
        );
       
        // Si el modelo retorna un ID válido (mayor a 0)
        if ($id_mantenimiento && $id_mantenimiento > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => '¡Mantenimiento guardado con éxito!',
                'id_mantenimiento' => $id_mantenimiento
            ]);
        } else {
            throw new Exception("Error al guardar el mantenimiento en la base de datos.");
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'asignarInsumos') {

    die(json_encode([
        'success' => true,
        'mensaje' => 'Entró al bloque'
    ]));

    // resto del código...
   // 1. Activamos un búfer limpio y apagamos la salida automática de errores en pantalla
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    ob_start();

    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set('America/Mexico_City');
    
    try {
        // Validación de datos fijos
       
        $vehiculo_id = intval($_POST['carro_id'] ?? 0); 
        $usuario_id  = intval($_SESSION['usuario_id'] ?? 1); 
        
       
        if ($vehiculo_id <= 0) {
            throw new Exception("Debe seleccionar un vehículo válido.");
        }

        // Recuperar arreglos dinámicos
        $items_insumos = $_POST['msign_items'] ?? [];
        $cantidades    = $_POST['msign_cant'] ?? [];

        if (empty($items_insumos) || empty($cantidades)) {
            throw new Exception("Debe seleccionar al menos un insumo con su cantidad.");
        }

        // 🚀 Ejecutar proceso PEPS en el Modelo
        // Asegúrate de que $mantenimientosModel esté instanciado antes de este bloque
        $entrega = $mantenimientosModel->registrarSalidaMantenimientoPEPS(
            $usuario_id, 
            $items_insumos, 
            $cantidades, 
            $vehiculo_id
        );
     
        if ($entrega && isset($entrega['success']) && $entrega['success'] === true) {
            // Limpiamos cualquier warning basura previo del búfer antes de imprimir
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => '¡Insumos asignados al mantenimiento con éxito aplicando PEPS!',
                'entrega' => $entrega['id'] ?? null
            ]);
        } else {
            throw new Exception("Error al procesar el inventario PEPS en la base de datos.");
        }

    } catch (Throwable $e) {
        // En caso de error, limpiamos el búfer y enviamos el JSON de error controlado
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    // Cerramos el flujo de ejecución inmediatamente para que nada más ensucie la respuesta
    exit;
}