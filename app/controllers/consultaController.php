<?php
/**
 * clientesController.php
 * Controlador para la gestión de Clientes (CRUD y Estado)
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/LayoutController.php';
require_once __DIR__ . '/../models/mascotasModel.php';
require_once __DIR__ . '/../models/clientesModel.php';

$clientesModel = new ClientesModel($conexion);

$mascotasModel = new MascotasModel($conexion);
require_once __DIR__ . '/../models/consultaModel.php';

$consultaModel = new ConsultaModel ($conexion);

$paginaActual = 'consultas';

// --- ACCIÓN: GUARDAR / ACTUALIZAR CLIENTE (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['cliente_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? 1;

        $datos = [
            'nombre_comercial' => trim($_POST['nombre_comercial'] ?? ''),
            'razon_social'     => trim($_POST['razon_social'] ?? ''),
            'rfc'              => strtoupper(trim($_POST['rfc'] ?? '')),
            'regimen_fiscal'   => $_POST['regimen_fiscal'] ?? '',
            'codigo_postal'    => $_POST['codigo_postal'] ?? '',
            'correo'           => $_POST['correo'] ?? '',
            'telefono'         => $_POST['telefono'] ?? '',
            'direccion'        => $_POST['direccion'] ?? '',
            'uso_cfdi'         => $_POST['uso_cfdi'] ?? 'G03'
        ];

        if (empty($datos['nombre_comercial']) || empty($datos['rfc'])) {
            throw new Exception("Nombre comercial y RFC son campos obligatorios.");
        }

        if ($id > 0) {
            $resultado = $clientesModel->actualizar($id, $datos);
            $mensaje = "Cliente actualizado correctamente.";
        } else {
            $resultado = $clientesModel->guardar($datos);
            $mensaje = "Cliente registrado correctamente.";
        }

        echo json_encode(['success' => true, 'message' => $mensaje]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'guardarConsulta') {

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido.');
        }


        // =============================================
        // USUARIO
        // =============================================

        $usuario_id = (int)(
            $_SESSION['usuario_id']
            ?? $_SESSION['user_id']
            ?? $_SESSION['id']
            ?? 0
        );

        if ($usuario_id <= 0) {
            throw new Exception(
                'No se pudo identificar al usuario de la sesión.'
            );
        }


        // =============================================
        // DATOS PRINCIPALES
        // =============================================

        $mascota_id = (int)($_POST['paciente'] ?? 0);

        $motivo_consulta = trim(
            $_POST['motivo_consulta'] ?? ''
        );

        $sintomas = trim(
            $_POST['sintomas'] ?? ''
        );

        $diagnostico = trim(
            $_POST['explicacion']
            ?? $_POST['diagnostico']
            ?? ''
        );

        $tratamiento = trim(
            $_POST['tratamiento'] ?? ''
        );


        // =============================================
        // SIGNOS VITALES
        // =============================================

        $peso_kg = isset($_POST['peso_kg'])
            && $_POST['peso_kg'] !== ''
            ? (float)$_POST['peso_kg']
            : null;

        $temperatura_c = isset($_POST['temperatura_c'])
            && $_POST['temperatura_c'] !== ''
            ? (float)$_POST['temperatura_c']
            : null;

        $frecuencia_cardiaca = isset($_POST['frecuencia_cardiaca'])
            && $_POST['frecuencia_cardiaca'] !== ''
            ? (int)$_POST['frecuencia_cardiaca']
            : null;

        $frecuencia_respiratoria = isset($_POST['frecuencia_respiratoria'])
            && $_POST['frecuencia_respiratoria'] !== ''
            ? (int)$_POST['frecuencia_respiratoria']
            : null;


        // =============================================
        // OTROS DATOS
        // =============================================

        $observaciones = trim(
            $_POST['observaciones'] ?? ''
        );

        $costo = isset($_POST['costo'])
            && $_POST['costo'] !== ''
            ? (float)$_POST['costo']
            : 0;


        // =============================================
        // VALIDACIONES
        // =============================================

        if ($mascota_id <= 0) {
            throw new Exception(
                'No se recibió el paciente.'
            );
        }

        if ($motivo_consulta === '') {
            throw new Exception(
                'El motivo de consulta es obligatorio.'
            );
        }

        if ($sintomas === '') {
            throw new Exception(
                'Los síntomas son obligatorios.'
            );
        }

        if ($diagnostico === '') {
            throw new Exception(
                'El diagnóstico es obligatorio.'
            );
        }

        if ($tratamiento === '') {
            throw new Exception(
                'El tratamiento es obligatorio.'
            );
        }


        // =============================================
        // DATOS PARA EL MODELO
        // =============================================

        $datos = [

            'mascota_id' => $mascota_id,

            'usuario_id' => $usuario_id,

            'motivo_consulta' => $motivo_consulta,

            'sintomas' => $sintomas,

            'diagnostico' => $diagnostico,

            'tratamiento' => $tratamiento,

            'peso_kg' => $peso_kg,

            'temperatura_c' => $temperatura_c,

            'frecuencia_cardiaca' =>
                $frecuencia_cardiaca,

            'frecuencia_respiratoria' =>
                $frecuencia_respiratoria,

            'observaciones' => $observaciones,

            'costo' => $costo,

            'estado' => 1
        ];


        // =============================================
        // GUARDAR
        // =============================================

        $idHistorial = $mascotasModel->guardarConsulta($datos);


        // =============================================
        // RESPUESTA
        // =============================================

        echo json_encode([
            'success' => true,
            'message' => 'Consulta registrada correctamente.',
            'id' => $idHistorial
        ]);

    } catch (Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}if (isset($_GET['action']) && $_GET['action'] === 'pacientes') {

    if (ob_get_level()) ob_clean();

    header('Content-Type: application/json; charset=utf-8');

    try {

        $consultorio = $_GET['consultorio'] ?? $_GET['almacen_id'] ?? $_SESSION['almacen_id'] ?? 0;
        $cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;

        $resMascotas = $mascotasModel->listarTodos((int)$consultorio, (int)$cliente_id);
        $resClientes = $clientesModel->listarTodos(0);

        // Si es un objeto mysqli_result, convertimos a array con fetch_all. 
        // Si ya fuera un array, lo dejamos como está.
        $mascotas = is_a($resMascotas, 'mysqli_result') ? $resMascotas->fetch_all(MYSQLI_ASSOC) : ($resMascotas ?? []);
        $clientes = is_a($resClientes, 'mysqli_result') ? $resClientes->fetch_all(MYSQLI_ASSOC) : ($resClientes ?? []);

        echo json_encode([
            'success' => true,
            'pacientes' => array_values($mascotas),
            'propietarios' => array_values($clientes)
        ]);

    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

// --- ACCIÓN: CAMBIAR ESTADO ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        $estado = intval($_POST['estado'] ?? 0);

        if ($id <= 0) throw new Exception("ID de cliente no válido.");

        $resultado = $clientesModel->cambiarEstado($id, $estado);
        
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

// --- ACCIÓN: OBTENER DATOS POR ID (Editar) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        $cliente = $clientesModel->obtenerPorId($id);
        
        if ($cliente) {
            echo json_encode(['success' => true, 'data' => $cliente]);
        } else {
            throw new Exception('Cliente no encontrado.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método de solicitud no permitido.');
        }

        $idConsulta = $consultaModel->guardar($_POST);
        
        if ($idConsulta) {
            echo json_encode([
                'success' => true, 
                'message' => 'Consulta registrada correctamente.',
                'id' => $idConsulta
            ]);
        } else {
            throw new Exception('No se pudo guardar el registro de la consulta.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        // 1. Capturamos el almacén del usuario logueado (0 si es Admin)
        $almacen_sesion = $_SESSION['almacen_id'] ?? 0;

        // 2. Pasamos el ID a la función para que filtre automáticamente
        $clientes = $clientesModel->listarTodos($almacen_sesion);
        
        $tituloPagina = "Administración de Clientes";
        require_once __DIR__ . '/../views/consulta_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }

}