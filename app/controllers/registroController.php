<?php
/**
 * registroController.php
 * Controlador público para el registro de nuevos almacenes / negocios vía AJAX
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/controlAlmacenesModel.php';

$almacenesModel = new controlAlmacenesModel($conexion);

// --- ACCIÓN: REGISTRAR NUEVO ALMACÉN (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'registrar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $nombreResponsable = trim($_POST['nombre_responsable'] ?? '');
        $nombreNegocio     = trim($_POST['nombre'] ?? '');
        $codigo            = trim($_POST['codigo'] ?? '');
        $ubicacion         = trim($_POST['ubicacion'] ?? '');
        $tipoPlan          = intval($_POST['tipo_plan'] ?? 0);
        $password          = trim($_POST['password'] ?? '');

        if (empty($nombreNegocio) || empty($codigo)) {
            throw new Exception("El nombre del negocio y la clave/código son obligatorios.");
        }

        if ($tipoPlan <= 0) {
            throw new Exception("Debes seleccionar un plan de suscripción.");
        }

        if (empty($password) || strlen($password) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres.");
        }

        // Estructurar datos para el modelo
        $datos = [
            'codigo'                 => strtoupper($codigo),
            'nombre'                 => $nombreNegocio,
            'hora_cierre_programada' => '22:00:00',
            'ubicacion'              => $ubicacion,
            'tipo_plan'              => $tipoPlan,
            'pago'                   => 'pendiente',
            'password'               => password_hash($password, PASSWORD_BCRYPT)
        ];

        $id = $almacenesModel->guardar($datos);

        echo json_encode([
            'success' => true, 
            'message' => '¡Registro recibido con éxito!',
            'nombre_responsable' => $nombreResponsable,
            'nombre_negocio' => $nombreNegocio
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}