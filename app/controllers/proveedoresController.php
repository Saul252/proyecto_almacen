<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/proveedoresModel.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('proveedores');

$model = new ProveedoresModel($conexion);
$almacenModel = new AlmacenModel($conexion);

$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$usuario_id     = $_SESSION['id_usuario'] ?? 0;


/* =====================================================
   💾 GUARDAR (CREATE / UPDATE)
===================================================== */
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'guardarProveedor') {

//     header('Content-Type: application/json');

//     try {

//         $id                = intval($_POST['id'] ?? 0);
//         $nombre_comercial  = trim($_POST['nombre_comercial'] ?? '');
//         $razon_social      = trim($_POST['razon_social'] ?? '');
//         $rfc               = trim($_POST['rfc'] ?? 'XAXX010101000');
//         $correo            = trim($_POST['correo'] ?? '');
//         $telefono          = trim($_POST['telefono'] ?? '');
//         $direccion         = trim($_POST['direccion'] ?? '');
//         $almacen_id        = intval($_POST['almacen_id'] ?? 0);

//         // 🔒 Forzar almacén si no es admin
//         if ($almacen_sesion != 0) {
//             $almacen_id = $almacen_sesion;
//         }

//         // VALIDACIONES
//         if ($nombre_comercial === '') {
//             throw new Exception("El nombre comercial es obligatorio");
//         }

//         if ($almacen_id <= 0) {
//             throw new Exception("Almacén inválido");
//         }

//         $datos = [
//             'nombre_comercial' => $nombre_comercial,
//             'razon_social'     => $razon_social,
//             'rfc'              => $rfc,
//             'correo'           => $correo,
//             'telefono'         => $telefono,
//             'direccion'        => $direccion,
//             'almacen_id'       => $almacen_id,
//         ];

//         if ($id > 0) {
//             $model->actualizar($id, $datos);
//             $msg = "Proveedor actualizado";
//         } else {
//             $id = $model->crear($datos);
//             $msg = "Proveedor creado";
//         }

//         echo json_encode([
//             'status' => 'success',
//             'message' => $msg,
//             'id' => $id
//         ]);

//     } catch (Exception $e) {
//         echo json_encode([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ]);
//     }

//     exit;
// }


/* =====================================================
   ❌ ELIMINAR
===================================================== */

if (isset($_GET['action']) && $_GET['action'] == 'eliminarProveedor') {

    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = intval($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("ID inválido.");
        }

        // 🔥 ejecutar toggle en el modelo
        $resultado = $model->eliminarProveedor($id);

        if (!$resultado) {
            throw new Exception("No se pudo cambiar el estado.");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

/* =====================================================
   🔍 CONSULTA AJAX (LISTAR + FILTROS)
===================================================== */
if (isset($_GET['ajax'])) {

    header('Content-Type: application/json');

    try {

        $nombre = trim($_GET['nombre'] ?? '');
        $almacen_id_req = intval($_GET['almacen_id'] ?? 0);

        // 🔒 lógica tipo corteCaja
        $target =  $almacen_id_req;
        $data = $model->listarTodosProveedores( $target);

        echo json_encode([
            'status' => 'success',
            'data'   => $data,
            'es_lista' => ($target == 0)
        ]);

    } catch (Exception $e) {

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}


/* =====================================================
   🔍 OBTENER UNO
===================================================== */
if (isset($_GET['action']) && $_GET['action'] === 'obtenerProveedor') {

    header('Content-Type: application/json');

    try {

        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("ID inválido");
        }

        $data = $model->obtenerPorId($id);

        if (!$data) {
            throw new Exception("Proveedor no encontrado");
        }

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'actualizarProveedor') {

    while (ob_get_level()) ob_end_clean();

    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = intval($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("ID de proveedor inválido.");
        }

        // 🔥 TEXTOS
        $nombre_comercial = trim($_POST['nombre_comercial'] ?? '');
        $razon_social     = trim($_POST['razon_social'] ?? '');
        $rfc              = trim($_POST['rfc'] ?? 'XAXX010101000');
        $correo           = trim($_POST['correo'] ?? '');
        $direccion        = trim($_POST['direccion'] ?? '');
        $colonia          = trim($_POST['colonia'] ?? '');
        $ciudad           = trim($_POST['ciudad'] ?? '');

        // 🔥 ENTEROS
        $telefono   = !empty($_POST['telefono']) ? intval($_POST['telefono']) : 0;
        $telefono2  = !empty($_POST['telefono2']) ? intval($_POST['telefono2']) : 0;
        $extencion  = !empty($_POST['extencion']) ? intval($_POST['extencion']) : 0;

        // 🔥 NUMEROS EXTERIOR/INTERIOR
        $numeroExt = trim($_POST['numeroExt'] ?? '');
        $numeroInt = trim($_POST['numeroInt'] ?? '');

        // 🔥 IDS
        $almacen_id = intval($_POST['almacen_id'] ?? 0);
        $activo     = intval($_POST['activo'] ?? 1);

        // VALIDACIONES
        if ($nombre_comercial === '') {
            throw new Exception("El nombre comercial es obligatorio.");
        }

        if ($almacen_id <= 0) {
            throw new Exception("Selecciona un almacén válido.");
        }

        // ARRAY
        $datos = [
            'nombre_comercial' => $nombre_comercial,
            'razon_social'     => $razon_social,
            'rfc'              => $rfc,
            'correo'           => $correo,
            'telefono'         => $telefono,
            'telefono2'        => $telefono2,
            'extencion'        => $extencion,
            'direccion'        => $direccion,
            'colonia'          => $colonia,
            'ciudad'           => $ciudad,
            'numeroExt'        => $numeroExt,
            'numeroInt'        => $numeroInt,
            'almacen_id'       => $almacen_id,
            'activo'           => $activo
        ];

        $resultado = $model->actualizar($id, $datos);

        if (!$resultado) {
            throw new Exception("Error al actualizar proveedor.");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Proveedor actualizado correctamente'
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

/* =====================================================
   📄 CARGA NORMAL
===================================================== */

//$proveedores = $model->listarTodosProveedores($almacen_sesion);
$proveedores = $model->listarTodosProveedores($almacen_sesion);
$almacenes = $almacenModel->getAlmacenes($almacen_sesion);

$tituloPagina = "Catálogo de Proveedores";

require_once __DIR__ . '/../views/proveedores_view.php';