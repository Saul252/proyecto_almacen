<?php
/**
 * CF SYSTEM - Controlador de Evidencias
 */

// Desactivar la visualización de errores directos para no romper el JSON, 
// pero mantener el log interno.
ini_set('display_errors', 0); 
error_reporting(E_ALL);

// 1. RUTAS CORREGIDAS (Asegúrate de que existan)
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/RepartosModel.php';

$usuario_id    = $_SESSION['usuario_id'] ?? 0;
$trabajador_id = intval($_SESSION['trabajador_id'] ?? 0);
$rol_nombre    = $_SESSION['rol'] ?? '';

// Si es admin, le permitimos usar su usuario_id como trabajador_id si este es 0
$id_ejecutor = ($trabajador_id > 0) ? $trabajador_id : $usuario_id;

$repartoM = new RepartoModel($conexion);

if (isset($_REQUEST['action'])) {
    // Limpiamos cualquier salida previa (espacios en blanco, warnings) para que el JSON sea puro
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    
    $action = $_REQUEST['action'];

    try {
        // ACCIÓN: OBTENER LISTADO
        if ($action === 'get_entregas_folio') {
    // 1. Forzar encabezado JSON
    header('Content-Type: application/json; charset=utf-8');

    try {
        $folio = $_GET['folio'] ?? '';
        
        if (empty($folio)) {
            throw new Exception("Folio no proporcionado.");
        }
        
        $entregas = $repartoM->obtenerViajesLogisticaParaEntrega($folio); 

        // 2. Usar flags para evitar fallos por caracteres raros en UTF-8
        $json = json_encode([
            "success" => true, 
            "data" => $entregas
        ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

        if ($json === false) {
            echo json_encode(["success" => false, "error" => json_last_error_msg()]);
        } else {
            echo $json;
        }

    } catch (Exception $e) {
        // En lugar de que la excepción deje la pantalla en blanco:
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    exit;
}
 
        // ACCIÓN: GUARDAR EVIDENCIA
  if ($action === 'subir_evidencia_reparto') {
    try {
        
        $movimiento_id = intval($_POST['id_movimiento'] ?? 0);
      
        $relPath = "uploads/evidencias/" . date('Y/m/d') . "/";
        $targetDir = dirname(__DIR__, 2) . "/" . $relPath;
        
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        // Función auxiliar interna para procesar cada imagen
        $procesarFoto = function($inputName, $prefijo) use ($movimiento_id, $targetDir, $relPath) {
            if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
                $nombre = $prefijo . "_" . $movimiento_id . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetDir . $nombre)) {
                    return $relPath . $nombre;
                }
            }
            return null;
        };

        $foto_entrega = $procesarFoto('evidencia_foto', 'MAT'); // MAT de Material
        $foto_nota    = $procesarFoto('evidencia_nota', 'NOT');  // NOT de Nota

        $datos = [
            'id_movimiento'      => $movimiento_id,
            'id_venta'           => intval($_POST['id_venta'] ?? 0),
            'trabajador_id'      => $id_ejecutor,
            'vehiculo_id'        => intval($_POST['vehiculo_id'] ?? 0),
            'fotografia_entrega' => $foto_entrega,
            'fotografia_nota'    => $foto_nota,
            'folio'              =>$_POST['folio'] ?? '',
            'estatus_entrega'    => $_POST['estatus_entrega'] ?? 'Entregado',
            'comentario'         => $_POST['comentario'] ?? ''
        ];
       

        if ($repartoM->registrarEntregaMovimiento($datos)) {
            echo json_encode(["success" => true, "message" => "Evidencias guardadas correctamente"]);
        }

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}
    } catch (Exception $e) {
        http_response_code(400); // Para que el fetch de JS entre al .catch() o detecte el error
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

// Si no hay acción, cargamos la vista (Ruta corregida)
$folio_viaje = $_GET['folio'] ?? '';
require_once __DIR__ . '/../views/gestionarRepartos.php';