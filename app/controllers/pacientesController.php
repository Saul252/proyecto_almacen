<?php
/**
 * mascotasController.php
 * Controlador para la gestión de Mascotas (CRUD, Estado y Subida de Imágenes)
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/LayoutController.php';
require_once __DIR__ . '/../models/mascotasModel.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

$clientesModel = new ClientesModel($conexion);
$almacenMo = new AlmacenModel($conexion);
$mascotasModel = new MascotasModel($conexion);
$paginaActual = 'pacientes';

// --- ACCIÓN: GUARDAR / ACTUALIZAR MASCOTA (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        // En el HTML le pusimos 'mascota_id' al campo oculto
        $id = intval($_POST['mascota_id'] ?? 0);
        
        $datos = [
            'cliente_id'         => intval($_POST['cliente_id'] ?? 0),
            'nombre'             => trim($_POST['nombre'] ?? ''),
            'especie'            => trim($_POST['especie'] ?? ''),
            'raza'               => trim($_POST['raza'] ?? ''),
            'fecha_nacimiento'   => !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null,
            'sexo'               => $_POST['sexo'] ?? 'Desconocido',
            'peso'               => !empty($_POST['peso']) ? floatval($_POST['peso']) : null,
            'color'              => trim($_POST['color'] ?? ''),
            'senas_particulares' => trim($_POST['senas_particulares'] ?? '')
        ];

        // Validaciones básicas
        if (empty($datos['cliente_id']) || empty($datos['nombre']) || empty($datos['especie'])) {
            throw new Exception("El cliente, nombre y especie son campos obligatorios.");
        }

        // Manejo de la subida de fotografía
        if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
            // Ruta donde se guardarán (asegúrate de que la carpeta exista y tenga permisos)
            $directorio_destino =  $_SERVER['DOCUMENT_ROOT'] . '/myvet/uploads/compras/';
            
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            // Generar nombre único para evitar sobreescritura de imágenes con el mismo nombre
            $extension = pathinfo($_FILES['fotografia']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid('pet_') . '.' . $extension;
            $ruta_absoluta = $directorio_destino . $nombre_archivo;

            if (move_uploaded_file($_FILES['fotografia']['tmp_name'], $ruta_absoluta)) {
                // Guardamos la ruta pública en el array de datos para la BD
                $datos['fotografia'] = 'uploads/compras/' . $nombre_archivo;
            } else {
                throw new Exception("Error al procesar y guardar la fotografía en el servidor.");
            }
        }

        // Determinar si es Insert o Update
        if ($id > 0) {
            $resultado = $mascotasModel->actualizar($id, $datos);
            $mensaje = "Mascota actualizada correctamente.";
        } else {
            $resultado = $mascotasModel->guardar($datos);
            $mensaje = "Mascota registrada correctamente.";
        }

        echo json_encode(['success' => true, 'message' => $mensaje]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: CAMBIAR ESTADO (Baja lógica) ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        $estado = intval($_POST['estado'] ?? 0);

        if ($id <= 0) throw new Exception("ID de mascota no válido.");

        $resultado = $mascotasModel->cambiarEstado($id, $estado);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado con éxito.']);
        } else {
            throw new Exception("No se pudo actualizar el estado.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: OBTENER DATOS POR ID (Para llenar el modal de Editar) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        $mascota = $mascotasModel->obtenerPorId($id);
        
        if ($mascota) {
            echo json_encode(['success' => true, 'data' => $mascota]);
        } else {
            throw new Exception('Mascota no encontrada.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: LISTADO AJAX (Con filtros) ---
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
       $data = $mascotasModel->obtenerTodos();
        echo json_encode($data);

    } catch (Throwable $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'obtenerExpediente') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        $mascota = $mascotasModel->obtenerExpedientePorId($id);
       

$data = [];

while ($row = $mascota->fetch_assoc()) {
    $data[] = $row;
}


        if ($mascota) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            throw new Exception($id);
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'filtrar_mascotas') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // 1. Captura el consultorio (acepta 'consultorio' o 'almacen_id' desde la URL o la Sesión)
        $consultorio = $_GET['consultorio'] ?? $_GET['almacen_id'] ?? $_SESSION['almacen_id'] ?? 0;

        // 2. Captura el cliente_id enviado por GET
        $cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;

        // 3. Pasa las variables reales al modelo (SIN números fijos)
        $mascotas = $mascotasModel->listarTodos((int)$consultorio, (int)$cliente_id);

        echo json_encode([
            'status' => true,
            'data'   => $mascotas
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        http_response_code(500); // Marca error HTTP de servidor
        echo json_encode([
            'status'  => false,
            'message' => 'Error al obtener datos: ' . $e->getMessage()
        ]);
        exit;
    }
}
// --- CARGA DE LA VISTA PRINCIPAL (GET normal sin action) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $consultorio=$_SESSION['almacen_id'];
        $almacen_usuario=$_SESSION['almacen_id'];
          $almacenes= $almacenMo->getAlmacenes($consultorio);
        // Obtenemos todas las mascotas para mostrarlas en la tabla. 
        // El modelo ya hace el JOIN para traer el nombre del dueño.
        $mascotas = $mascotasModel->listarTodos($consultorio,0);
        $clientes=$clientesModel->listarTodos($consultorio);
        
        $tituloPagina = "Administración de Mascotas";
        
        // Renderizamos la vista correspondiente
        require_once __DIR__ . '/../views/mascotas_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }
}
?>