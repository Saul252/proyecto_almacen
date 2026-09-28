<?php
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../../config/conexion.php';

class ModulosController {
    private $model;

    public function __construct($conexion) {
        $this->model = new ConfiguracionModel($conexion);
    }

    public function listarModulos() {
        $modulosBD = $this->model->obtenerModulos();

        // Archivo actual (para active)
        $archivoActual = basename($_SERVER['PHP_SELF']);

        // 🔥 Mapeo al formato que ya usas
        $modulos = array_map(function($mod) use ($archivoActual) {

            return [
                'id' => $mod['identificador'],
                
                // ⚠️ Aquí armas la URL dinámicamente
                'url' => '/myvet/app/controllers/' . $mod['identificador'] . 'Controller.php',
                
                'icon' => $mod['icono'] ?: 'bi-circle',
                
                // label = nombre visible
                'label' => $mod['nombre'],
                
                // activo dinámico
                'active' => ($archivoActual == $mod['identificador'] . 'Controller.php')
            ];

        }, $modulosBD);

        return $modulos;
    }
}

// Uso directo (sin vista)
$controller = new ModulosController($conexion);
$modulos = $controller->listarModulos();

// Puedes devolver JSON si lo necesitas
header('Content-Type: application/json');
echo json_encode($modulos);