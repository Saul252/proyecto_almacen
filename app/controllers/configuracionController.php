<?php
// 1. Reporte de errores para desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/configuracionModel.php';
protegerPagina('Configuracion'); 
class ConfiguracionController {
    private $model;
    private $conexion;

    public function __construct($conexion) {
        if (!$conexion) {
            die("Error Fatal: No hay conexión a la base de datos.");
        }
        $this->conexion = $conexion;
        $this->model = new ConfiguracionModel($conexion);
    }

    /**
     * Carga la vista principal con los datos necesarios
     */
    public function index() {
        try {
           
            // Obtenemos datos desde el modelo
            $roles = $this->model->obtenerRoles();
            $modulosData = $this->model->obtenerModulos();

            // Cargamos la vista
             $paginaActual = 'configuracionController';
            
            $rutaVista = __DIR__ . '/../views/configuracion_view.php';
            
            if (!file_exists($rutaVista)) {
                throw new Exception("No se encontró el archivo de la vista en: $rutaVista");
            }

            require_once $rutaVista;

        } catch (Exception $e) {
            $this->mostrarError($e->getMessage());
        }
    }

    /**
     * Maneja las peticiones AJAX (POST)
     */
    public function router() {
        // Aseguramos respuesta JSON y limpiamos cualquier salida previa
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $accion = $_POST['accion'] ?? '';
        $res = ['status' => 'error', 'message' => 'Acción no definida'];

        try {
            switch ($accion) {
                case 'guardar_permisos':
                    // PHP recibe permisos[rol_id][] como un array asociativo
                    $permisos = $_POST['permisos'] ?? [];
                    
                    // Si no hay permisos marcados, enviamos un array vacío al modelo
                    $exito = $this->model->actualizarMatrizPermisos($permisos);
                    $res = $exito ? ['status' => 'success'] : ['status' => 'error', 'message' => 'No se pudo actualizar la matriz de seguridad'];
                    break;
                    
                case 'guardar_rol':
                    $nombre = trim($_POST['nombre_rol'] ?? '');
                    $id = !empty($_POST['id_rol']) ? intval($_POST['id_rol']) : null;
                    
                    if(empty($nombre)) throw new Exception("El nombre del rol es obligatorio.");
                    
                    $exito = $this->model->guardarRol($nombre, $id);
                    $res = $exito ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Error al guardar el rol'];
                    break;

                case 'eliminar_rol':
                    $id = intval($_POST['id'] ?? 0);
                    // Protección para el Administrador (asumiendo ID 1)
                    if($id === 1) throw new Exception("El rol Administrador no puede ser eliminado por seguridad.");
                    
                    $exito = $this->model->eliminarRol($id);
                    $res = $exito ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Error al eliminar el rol'];
                    break;

                case 'guardar_modulo':
                    if(empty($_POST['nombre']) || empty($_POST['identificador'])) {
                        throw new Exception("Nombre e Identificador son obligatorios.");
                    }
                    $exito = $this->model->guardarModulo($_POST);
                    $res = $exito ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Error al guardar el módulo'];
                    break;

                case 'eliminar_modulo':
                    $id = intval($_POST['id'] ?? 0);
                    $exito = $this->model->eliminarModulo($id);
                    $res = $exito ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Error al desactivar el módulo'];
                    break;
            }
        } catch (Exception $e) {
            $res = ['status' => 'error', 'message' => $e->getMessage()];
        }

        echo json_encode($res);
        exit;
    }

    private function mostrarError($msj) {
        echo "<div style='color:#721c24; border:1px solid #f5c6cb; padding:15px; background:#f8d7da; border-radius:5px; margin:20px;'>";
        echo "<strong>Error del Sistema:</strong> " . htmlspecialchars($msj);
        echo "</div>";
    }
}

// --- INSTANCIACIÓN Y EJECUCIÓN ---
// Se asume que $conexion viene de config/conexion.php
if (isset($conexion)) {
    $instancia = new ConfiguracionController($conexion);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $instancia->router();
    } else {
        $instancia->index();
    }
} else {
    echo "Error: La conexión a la base de datos no está definida.";
}