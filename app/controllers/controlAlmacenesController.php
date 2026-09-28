<?php
/**
 * almacenesController.php
 * Controlador para la gestión de Almacenes (CRUD completo, registro nuevo, cambios independientes y contraseña)
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/controlAlmacenesModel.php';

$almacenesModel = new controlAlmacenesModel($conexion);
$paginaActual = 'almacenes';

// --- ACCIÓN: GUARDAR / CREAR NUEVO O ACTUALIZAR ALMACÉN (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['almacen_id'] ?? 0);

        $datos = [
            'codigo'                 => trim($_POST['codigo'] ?? ''),
            'nombre'                 => trim($_POST['nombre'] ?? ''),
            'hora_cierre_programada' => $_POST['hora_cierre_programada'] ?? '22:00:00',
            'ubicacion'              => trim($_POST['ubicacion'] ?? ''),
            'activo'                 => intval($_POST['activo'] ?? 1),
            'tipo_plan'              => intval($_POST['tipo_plan'] ?? 1),
            'pago'                   => $_POST['pago'] ?? 'al_dia'
        ];

        if (empty($datos['codigo']) || empty($datos['nombre'])) {
            throw new Exception("El código y el nombre del almacén son campos obligatorios.");
        }

        $password = trim($_POST['password'] ?? '');

        if ($id > 0) {
            // Actualización de Almacén Existente
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    throw new Exception("La contraseña debe tener al menos 6 caracteres.");
                }
                $datos['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $resultado = $almacenesModel->actualizar($id, $datos);
            $mensaje = "Almacén actualizado correctamente.";
        } else {
            // Creación de Nuevo Almacén
            if (empty($password) || strlen($password) < 6) {
                throw new Exception("Debe ingresar una contraseña de al menos 6 caracteres para el nuevo almacén.");
            }
            $datos['password'] = password_hash($password, PASSWORD_BCRYPT);

            $resultado = $almacenesModel->guardar($datos);
            $mensaje = "Nuevo almacén registrado correctamente.";
        }

        echo json_encode(['success' => true, 'message' => $mensaje]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: CAMBIAR ESTADO (ACTIVAR / DESACTIVAR CUENTA) ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        $estado = intval($_POST['estado'] ?? 0);

        if ($id <= 0) throw new Exception("ID de almacén no válido.");

        $resultado = $almacenesModel->cambiarEstado($id, $estado);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Estado del almacén actualizado con éxito.']);
        } else {
            throw new Exception("No se pudo actualizar el estado.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: CAMBIAR ESTADO DE PAGO (INDEPENDIENTE) ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstadoPago') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        $pago = trim($_POST['pago'] ?? '');

        if ($id <= 0) throw new Exception("ID de almacén no válido.");
        if (!in_array($pago, ['al_dia', 'pendiente', 'vencido'])) {
            throw new Exception("Estado de pago no válido.");
        }

        $resultado = $almacenesModel->cambiarEstadoPago($id, $pago);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Estado de pago actualizado correctamente.']);
        } else {
            throw new Exception("No se pudo actualizar el estado de pago.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: CAMBIAR TIPO DE PLAN (INDEPENDIENTE) ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarTipoPlan') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        $tipo_plan = intval($_POST['tipo_plan'] ?? 0);

        if ($id <= 0) throw new Exception("ID de almacén no válido.");
        if ($tipo_plan <= 0) throw new Exception("Debe seleccionar un plan válido.");

        $resultado = $almacenesModel->cambiarTipoPlan($id, $tipo_plan);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Tipo de plan actualizado correctamente.']);
        } else {
            throw new Exception("No se pudo actualizar el tipo de plan.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: CAMBIAR NOMBRE DEL ALMACÉN (INDEPENDIENTE) ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarNombre') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($id <= 0) throw new Exception("ID de almacén no válido.");
        if (empty($nombre)) throw new Exception("El nombre no puede estar vacío.");

        $resultado = $almacenesModel->cambiarNombre($id, $nombre);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Nombre del almacén actualizado correctamente.']);
        } else {
            throw new Exception("No se pudo actualizar el nombre.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: CAMBIAR CONTRASEÑA DE LA CUENTA ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarPassword') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['almacen_id'] ?? 0);
        $password = $_POST['password'] ?? '';

        if ($id <= 0) throw new Exception("ID de almacén no válido.");
        if (empty($password) || strlen($password) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres.");
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $resultado = $almacenesModel->actualizarPassword($id, $passwordHash);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
        } else {
            throw new Exception("No se pudo actualizar la contraseña.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: OBTENER DATOS POR ID (Editar) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        $almacen = $almacenesModel->obtenerPorId($id);
        
        if ($almacen) {
            echo json_encode(['success' => true, 'data' => $almacen]);
        } else {
            throw new Exception('Almacén no encontrado.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGAR VISTA PRINCIPAL ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $almacen_sesion = $_SESSION['almacen_id'] ?? 0;
        $almacenes = $almacenesModel->listarTodos($almacen_sesion);
        $planes =$almacenesModel->planes();
        
        $tituloPagina = "Administración de Almacenes";
        require_once __DIR__ . '/../views/controlAlmacenes_views.php';
    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }
}