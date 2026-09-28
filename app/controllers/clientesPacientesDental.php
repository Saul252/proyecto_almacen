<?php
/**
 * clientesController.php 
 * Ajustado solo con lo necesario para el almacén
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
protegerPagina('clientes');
$clientesModel = new ClientesModel($conexion);
$paginaActual = 'clientes';
// Capturamos el almacén de la sesión para las consultas
$almacen_id = $_SESSION['almacen_id'] ?? 0;
$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

if (isset($_GET['action']) && $_GET['action'] === 'obtenerEstadoCuenta') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0)
            throw new Exception("ID de cliente no válido.");

        // 1. Obtener el resumen rápido (Saldo total)
        $resumen = ClientesModel::obtenerSaldoActual($conexion, $id);

        // 2. Obtener el historial detallado desde el LOG
        $historial_res = ClientesModel::obtenerHistorialLog($conexion, $id);
        $movimientos = [];

        if ($historial_res) {
            while ($row = $historial_res->fetch_assoc()) {
                $movimientos[] = $row;
            }
        }

        echo json_encode([
            'success' => true,
            'resumen' => $resumen,
            'movimientos' => $movimientos
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIÓN: GUARDAR / ACTUALIZAR CLIENTE (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_POST['cliente_id'] ?? 0);

        $datos = [
            'nombre_comercial' => trim($_POST['nombre_comercial'] ?? ''),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'rfc' => strtoupper(trim($_POST['rfc'] ?? '')),
            'regimen_fiscal' => $_POST['regimen_fiscal'] ?? '',
            'codigo_postal' => $_POST['codigo_postal'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'contacto' => $_POST['contacto'] ?? '',
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? date('Y-m-d'),
            'sexo' => $_POST['sexo'] ?? 'HUMANO',
            'calle' => $_POST['calle'] ?? '',
            'colonia' => $_POST['colonia'] ?? '',
            'pueblo' => $_POST['pueblo'] ?? '',
            'ciudad' => $_POST['ciudad'] ?? '',
            'direccion' =>
                'calle ' . ($_POST['calle'] ?? '') . ', ' .
                'col ' . ($_POST['colonia'] ?? '') . ', ' .
                'pueblo ' . ($_POST['pueblo'] ?? '') . ', ' .
                'ciudad ' . ($_POST['ciudad'] ?? ''),

            'uso_cfdi' => $_POST['uso_cfdi'] ?? 'G03',

            'almacen_id' => $_POST['almacen_id'] ?? null
        ];

        if (empty($datos['nombre_comercial']) || empty($datos['rfc'])) {
            throw new Exception("Nombre comercial y RFC son campos obligatorios.");
        }

        if ($id > 0) {
            $resultado = $clientesModel->actualizar($id, $datos);
            // Si es actualización, devolvemos el mismo ID que recibimos
            echo json_encode([
                'success' => true,
                'message' => "Cliente actualizado correctamente.",
                'id' => $id
            ]);
        } else {
            // Guardar devuelve un array: ['success' => true, 'id' => ..., 'api_token' => ...]
            $resultado = $clientesModel->guardar($datos);

            if ($resultado && isset($resultado['id'])) {
                echo json_encode([
                    'success' => true,
                    'message' => "Cliente registrado correctamente.",
                    'id' => $resultado['id'] // ESTO ES LO QUE NECESITA TU JS
                ]);
            } else {
                throw new Exception("Cliente ya existente");
            }
        }

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIÓN: CAMBIAR ESTADO ---
if (isset($_GET['action']) && $_GET['action'] === 'cambiarEstado') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_POST['id'] ?? 0);
        $estado = intval($_POST['estado'] ?? 0);
        // Usamos el $almacen_id que viene de tu sesión/layout
        $almacen_sesion = $almacen_usuario ?? 0;

        if ($id <= 0)
            throw new Exception("ID de cliente no válido.");

        // Pasamos el almacén para que el modelo valide si tiene permiso
        $resultado = $clientesModel->cambiarEstado($id, $estado, $almacen_sesion);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
        } else {
            throw new Exception("No se pudo actualizar el estado o no tiene permisos.");
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: OBTENER DATOS POR ID (Editar) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerPorId') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? 0);
        $almacen_sesion = $almacen_usuario ?? 0;

        // El modelo ya debería filtrar por almacén internamente con lo que hablamos antes
        $cliente = $clientesModel->obtenerPorId($id, $almacen_sesion);

        if ($cliente) {
            // Lógica de validación: 
            // Si soy admin ($almacen_sesion == 0) pasa siempre.
            // Si soy sucursal, el modelo ya filtró que sea de mi ID.
            echo json_encode(['success' => true, 'data' => $cliente]);
        } else {
            throw new Exception('Cliente no encontrado o acceso denegado.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE VISTA (GET) ---
// --- CARGA DE VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        // Cambiamos el nombre de la variable para que coincida con la vista
        $almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        // Pasamos el ID a la función para que filtre automáticamente//de momento sera asi
        //la original no listaera esta $clientes = $clientesModel->listarTodosViewClientes($almacen_usuario);
        $clientes = $clientesModel->listarTodos($almacen_usuario);
        $nclientes = $clientesModel->getResumenClientes($almacen_usuario);

        $tituloPagina = "Administración de Clientes";
        require_once __DIR__ . '/../views/clientesPacientesDental_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }
}