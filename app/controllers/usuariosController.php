<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/usuariosModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('usuarios');

$modelo = new UsuarioModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$validacion = $almacen_usuario == 0 ? true : false;
$usuario = $_SESSION['usuario_id'] ?? 0;
$tipo = $almacen_usuario == 0 ? 1 : 0;
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

/* ============================================================
   HELPER: Convertir a MAYÚSCULAS
============================================================ */
function aMayusculas($valor)
{
    return mb_strtoupper(trim($valor), 'UTF-8');
}

/* ============================================================
   GET: LISTAR USUARIOS
============================================================ */
if (isset($_GET['action']) && $_GET['action'] === 'obtenerUsuarios') {

    // 1. Limpiar cualquier buffer previo y evitar que warnings ensucien el JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');

    try {
        // 2. Leer variables de forma segura
        $rol = (int) ($_SESSION['rol_id'] ?? 0);
        $almacen_usuario = (int) ($almacen_usuario ?? 0);
        $id_usuario = (int) ($_GET['id'] ?? 0);
        $almacen = (int) ($_GET['almacen_id'] ?? $almacen_usuario);

        // 3. Inicializar SIEMPRE como array
        $data = [];

        if ($rol >= 3) {
            $data = $modelo->listarUsuariosPorId($id_usuario);
        } else {
            if ($almacen === 0) {
                $data = $modelo->listarTodosUsuarios(0);
            } else {
                $data = $modelo->listarUsuariosAlmacen($almacen);

                if ($almacen_usuario === 0) {
                    $admins = $modelo->listarTodosUsuariosAdmin(0);

                    // 4. Fusionar sin duplicados usando el id como clave
                    $indexado = [];
                    foreach (array_merge($data, $admins) as $u) {
                        $indexado[$u['id']] = $u;
                    }
                    $data = array_values($indexado);
                }
            }
        }

        // 5. Ordenar por nombre (opcional pero recomendado)
        usort($data, fn($a, $b) => strcasecmp($a['nombre'] ?? '', $b['nombre'] ?? ''));

        // 6. Respuesta consistente
        echo json_encode([
            'success' => true,
            'total' => count($data),
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);

    } catch (Throwable $e) {
        // 7. Asegurar que la respuesta de error también sea JSON limpio
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

/* ============================================================
   POST: GUARDAR / ELIMINAR
============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /* --------------------------------------------------------
       GUARDAR (crear o actualizar)
    -------------------------------------------------------- */
    if ($accion === 'guardar') {
        $id = intval($_POST['id'] ?? 0);

        // --- Campos que SÍ van en MAYÚSCULAS ---
        $nombre = aMayusculas($_POST['nombre'] ?? '');

        // --- Campos que NO se convierten ---
        $username = trim($_POST['username'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $password = $_POST['password'] ?? '';

        // --- Otros campos ---
        $rol_id = intval($_POST['rol_id'] ?? 0);
        $almacen_id = (!empty($_POST['almacen_id'])) ? intval($_POST['almacen_id']) : null;

        try {
            if ($id === 0) {
                // --- NUEVO USUARIO ---
                if ($modelo->existeUsername($username)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'El nombre de usuario ya está en uso'
                    ]);
                    exit;
                }

                $ok = $modelo->crear($nombre, $username, $password, $rol_id, $almacen_id, $telefono);

                $mensajeOk = '¡Logrado! Usuario agregado correctamente';
                $mensajeError = 'No se pudo agregar el usuario';

            } else {
                // --- EDITAR USUARIO ---
                $ok = $modelo->actualizar($id, $nombre, $username, $password, $rol_id, $almacen_id, $telefono);

                $mensajeOk = '¡Logrado! Usuario actualizado correctamente';
                $mensajeError = 'No se pudo actualizar el usuario';
            }

            if ($ok) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $mensajeOk
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $mensajeError . ': ' . $modelo->getError()
                ]);
            }

        } catch (Throwable $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /* --------------------------------------------------------
       ELIMINAR (switch activo/inactivo)
    -------------------------------------------------------- */
    if ($accion === 'eliminar') {
        $id = intval($_POST['id'] ?? 0);

        try {
            $ok = $modelo->toggleActivo($id);

            if ($ok) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Estado actualizado'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se pudo actualizar el estado'
                ]);
            }

        } catch (Throwable $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

}

// Variables para la vista
$paginaActual = 'Usuarios';

$rolesArray = $modelo->getRoles();
$almacenesArray = $almacenModel->getAlmacenes($_SESSION['almacen_id']);

// Cargar la vista (archivo HTML)
include __DIR__ . '/../views/usuarios_view.php';