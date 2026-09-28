<?php
// 1. Reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Seguridad y Sesión
require_once __DIR__ . '/../../includes/auth.php';

// 3. Carga de dependencias
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/almacen/productosModel.php';
require_once __DIR__ . '/../models/almacen/categoriasModel.php';

require_once __DIR__ . '/LayoutController.php';

// Protegemos la página leyendo la sesión
protegerPagina('almacenes');

// ==========================================================
// 🔓 LECTURA Y CIERRE INMEDIATO DE SESIÓN
// ==========================================================
// Extraemos las variables de sesión que se usan a lo largo del controlador
$session_almacen_id = intval($_SESSION['almacen_id'] ?? 0);
$session_usuario_id = intval($_SESSION['usuario_id'] ?? 1);

// Liberamos el candado del archivo de sesión inmediatamente
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

class AlmacenController
{
    private $model;
    private $productoModel;
    private $categoriaModel;
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        $this->model = new AlmacenModel($conexion);
        $this->productoModel = new ProductoModel($conexion);
        $this->categoriaModel = new CategoriaModel($conexion);
    }

    public function index()
    {
        global $session_almacen_id;

        $paginaActual = 'almacenes';
        // Mantenemos el ID de sesión usando nuestra variable local extraída
        $almacen_usuario = $session_almacen_id;

        try {
            // 1. Cargamos el catálogo y almacenes para los selectores/tablas
            $categorias = $this->model->getCategorias();
            $almacenes = $this->model->getAlmacenes($almacen_usuario);

            $inversion = $this->model->inversion($almacen_usuario);
            $todosLosAlmacenes = $this->model->getAlmacenesDestino($almacen_usuario);

            // 2. Cargamos el inventario detallado para el DataTable
            $productos = $this->model->getInventario($almacen_usuario);

            $unidadesMedida = $this->model->getUnidadesMedida();
            $unidadesMedidam = $this->model->getUnidadesMedida();

            // --- 3. RESUMEN AUTOMÁTICO PARA LAS TARJETAS ---
            $resumenData = $this->model->getResumenStock($almacen_usuario);

            // Validaciones de seguridad para evitar errores en la vista
            if ($categorias === null)
                $categorias = [];
            if ($almacenes === null)
                $almacenes = [];
            if ($productos === null)
                $productos = [];
            if ($resumenData === null) {
                $resumenData = [
                    'tipo' => 'error',
                    'nombre' => 'No disponible',
                    'mis_productos' => 0,
                    'total_sistema' => 0
                ];
            }

            // 4. Renderizamos la vista (ya lleva $resumenData inyectado)
            $tituloPagina = 'Almacenes';
            require_once __DIR__ . '/../views/almacenes_view2.php';

        } catch (Exception $e) {
            error_log("Error en AlmacenController: " . $e->getMessage());
            die("Lo sentimos, hubo un problema al cargar el inventario. Por favor, intenta más tarde.");
        }
    }

    /**
     * AJAX: Obtener lista completa de productos para refrescar Selects en Compras
     */

    public function guardarCantidadesIniciales()
    {
        while (ob_get_level())
            ob_end_clean();

        ini_set('display_errors', 0);
        error_reporting(0);

        header('Content-Type: application/json');

        try {
            // 🔹 1. Mapear y estructurar estrictamente los almacenes que vienen por POST
            $almacenes_estructurados = [];

            if (isset($_POST['almacenes']) && is_array($_POST['almacenes'])) {
                foreach ($_POST['almacenes'] as $almacen_id => $datos) {
                    // Definimos únicamente la estructura que tu función lee dentro de $datos
                    $almacenes_estructurados[$almacen_id] = [
                        'stock' => floatval($datos['stock'] ?? 0),
                        'stock_minimo' => floatval($datos['stock_minimo'] ?? 0),
                        'precio_minorista' => floatval($datos['precio_minorista'] ?? 0),
                        'precio_mayorista' => floatval($datos['precio_mayorista'] ?? 0),
                        'precio_distribuidor' => floatval($datos['precio_distribuidor'] ?? 0)
                    ];
                }
            }

            // 🔹 2. Armar el arreglo $data con la estructura exacta y limpia
            $data = [
                'sku' => trim($_POST['sku'] ?? ''),
                'factor_conversion' => floatval($_POST['factorConversionProducto'] ?? 1),
                'precio_adquisicion' => floatval($_POST['precio_adquisicion'] ?? 0),
                'usuario_id' => $_SESSION['usuario_id'] ?? 1,
                'almacenes' => $almacenes_estructurados
            ];

            // 🔹 3. Validaciones obligatorias
            if (empty($data['sku'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'El SKU es obligatorio.'
                ]);
                exit;
            }

            if (empty($data['almacenes'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'El arreglo de almacenes es obligatorio y no puede estar vacío.'
                ]);
                exit;
            }

            $producto_id = intval($_POST['producto_id'] ?? 0);
            if ($producto_id <= 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'El ID del producto es inválido.'
                ]);
                exit;
            }

            // 🔹 4. Llamada directa a tu función
            $resultado = $this->productoModel->agregarCantidadesIniciales($producto_id, $data);

            if ($resultado) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cantidades iniciales registradas correctamente.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se pudieron registrar las cantidades iniciales.'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error en guardarCantidadesIniciales: " . $e->getMessage());

            echo json_encode([
                'status' => 'error',
                'message' => 'Error interno del servidor.'
            ]);
        }

        exit;
    }
    public function getListaProductosJson()
    {
        while (ob_get_level())
            ob_end_clean(); // Limpiar búfer para JSON puro
        header('Content-Type: application/json; charset=utf-8');
        try {
            $productos = $this->productoModel->getProductos();
            echo json_encode($productos ?: []);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function getListaProductosJsonAlmacen()
    {
        global $session_almacen_id;

        while (ob_get_level())
            ob_end_clean(); // Limpiar búfer para JSON puro
        header('Content-Type: application/json; charset=utf-8');
        try {
            $almacen_usuario = $_GET['almacen'] ?? $session_almacen_id;
            $productos = $this->productoModel->obtenerProductosAlmacen($almacen_usuario);
            echo json_encode($productos ?: []);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function guardarCategoria()
    {
        header('Content-Type: application/json');
        $nombre = trim($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre es obligatorio']);
            return;
        }
        try {
            if ($this->categoriaModel->existe($nombre)) {
                echo json_encode(['status' => 'error', 'message' => 'Esta categoría ya existe']);
                return;
            }
            $id = $this->categoriaModel->guardar($nombre);
            echo json_encode(['status' => 'success', 'id' => $id, 'nombre' => $nombre]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function getCategoriasJSON()
    {
        global $session_almacen_id;

        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        try {
            $almacen_usuario = $session_almacen_id;
            $categorias = $this->model->getCategoriasAlmacen($almacen_usuario);
            echo json_encode($categorias ?: []);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function getUnidadesMedidaJSON()
    {
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        try {
            $unidadesMedida = $this->model->getUnidadesMedida();
            echo json_encode($unidadesMedida ?: []);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function guardarProducto()
    {
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json');

        $factor_conversion = floatval($_POST['factor_conversion'] ?? 1);
        if ($factor_conversion <= 0)
            $factor_conversion = 1;

        $p_minorista = floatval($_POST['precio_minorista'] ?? 0);
        $p_mayorista = floatval($_POST['precio_mayorista'] ?? 0);
        $p_distribuidor = floatval($_POST['precio_distribuidor'] ?? 0);

        $pmin = $p_minorista > 0 ? ($p_minorista / $factor_conversion) : 0;
        $pmay = $p_mayorista > 0 ? ($p_mayorista / $factor_conversion) : 0;
        $pdi = $p_distribuidor > 0 ? ($p_distribuidor / $factor_conversion) : 0;

        $datos = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'categoria_id' => $_POST['categoria_id'] ?? null,
            'unidad_medida' => $_POST['unidad_medida'] ?? 'PZA',
            'unidad_reporte' => $_POST['unidad_reporte'] ?? '',
            'factor_conversion' => floatval($_POST['factor_conversion'] ?? 1),
            'precio_adquisicion' => 0,
            'impuesto_iva' => floatval($_POST['impuesto_iva'] ?? 16.00),
            'descripcion' => $_POST['description'] ?? '',
            'fiscal_clave_prod' => $_POST['fiscal_clave_prod'] ?? '',
            'fiscal_clave_unidad' => $_POST['fiscal_clave_unidad'] ?? '',
            'precio_minorista' => $pmin,
            'precio_mayorista' => $pmay,
            'precio_distribuidor' => $pdi
        ];

        if (empty($datos['sku']) || empty($datos['nombre'])) {
            echo json_encode(['status' => 'error', 'message' => 'SKU y Nombre son obligatorios']);
            exit;
        }

        $nuevoId = $this->productoModel->guardarCompletoMultiALmacen($datos);

        if ($nuevoId) {
            echo json_encode(['status' => 'success', 'message' => 'Producto registrado exitosamente', 'id' => $nuevoId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el producto.']);
        }
        exit;
    }

    public function guardarProductoUnsoloAlmacen()
    {
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json');

        $datos = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'categoria_id' => $_POST['categoria_id'] ?? null,
            'unidad_medida' => $_POST['unidad_medida'] ?? 'PZA',
            'unidad_reporte' => $_POST['unidad_reporte'] ?? '',
            'factor_conversion' => floatval($_POST['factor_conversion'] ?? 1),
            'precio_adquisicion' => 0,
            'impuesto_iva' => floatval($_POST['impuesto_iva'] ?? 16.00),
            'descripcion' => $_POST['description'] ?? '',
            'fiscal_clave_prod' => $_POST['fiscal_clave_prod'] ?? '',
            'fiscal_clave_unidad' => $_POST['fiscal_clave_unidad'] ?? '',
            'precio_minorista' => floatval($_POST['precio_minorista'] ?? 0),
            'precio_mayorista' => floatval($_POST['precio_mayorista'] ?? 0),
            'precio_distribuidor' => floatval($_POST['precio_distribuidor'] ?? 0)
        ];

        if (empty($datos['sku']) || empty($datos['nombre'])) {
            echo json_encode(['status' => 'error', 'message' => 'SKU y Nombre son obligatorios']);
            exit;
        }

        $nuevoId = $this->productoModel->guardarCompleto($datos);

        if ($nuevoId) {
            echo json_encode(['status' => 'success', 'message' => 'Producto registrado exitosamente', 'id' => $nuevoId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el producto.']);
        }
        exit;
    }

    public function obtenerListaAlmacenes()
    {
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $almacenes = $this->model->getAlmacenes(0);

            if (!$almacenes) {
                echo json_encode([]);
            } else {
                echo json_encode($almacenes);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function guardarProductoCompleto()
    {
        global $session_usuario_id;

        while (ob_get_level())
            ob_end_clean();

        ini_set('display_errors', 0);
        error_reporting(0);

        header('Content-Type: application/json');
        try {

            $data = [
                'sku' => trim($_POST['sku'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'descripcion' => $_POST['description'] ?? '',
                'categoria_id' => !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null,
                'unidad_medida' => $_POST['unidad_medida'] ?? 'PZA',
                'unidad_reporte' => $_POST['unidad_reporte'] ?? null,
                'factor_conversion' => floatval($_POST['factor_conversion'] ?? 1),
                'precio_adquisicion' => floatval($_POST['precio_adquisicion'] ?? 0),
                'fiscal_clave_prod' => $_POST['fiscal_clave_prod'] ?? null,
                'fiscal_clave_unit' => $_POST['fiscal_clave_unit'] ?? null,
                'impuesto_iva' => floatval($_POST['impuesto_iva'] ?? 16),
                'almacenes' => $_POST['almacenes'] ?? [],
                'usuario_id' => $session_usuario_id
            ];

            if (empty($data['sku']) || empty($data['nombre'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'SKU y Nombre son obligatorios'
                ]);
                exit;
            }

            $resultado = $this->productoModel->crearProductoMultiAlmacen($data);

            if ($resultado['status']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Producto guardado correctamente'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $resultado['msg']
                ]);
            }

        } catch (Exception $e) {
            error_log($e->getMessage());

            echo json_encode([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ]);
        }

        exit;
    }

    public function obtenerProductoDetalle()
    {
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? 0;
        $almacen_id = $_GET['almacen_id'] ?? 0;

        if (!$id || !$almacen_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Parámetros incompletos'
            ]);
            exit;
        }

        $resultado = $this->productoModel->obtenerProductoPorAlmacen($id, $almacen_id);

        if ($resultado['status']) {
            echo json_encode([
                'status' => 'success',
                'producto' => $resultado['data']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $resultado['msg']
            ]);
        }

        exit;
    }

    public function actualizarProducto()
    {
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json');

        try {
            $factor_conversion = floatval($_POST['factor_conversion'] ?? 1);
            if ($factor_conversion <= 0)
                $factor_conversion = 1;

            $p_minorista = floatval($_POST['precio_minorista'] ?? 0);
            $p_mayorista = floatval($_POST['precio_mayorista'] ?? 0);
            $p_distribuidor = floatval($_POST['precio_distribuidor'] ?? 0);

            $pmin = $p_minorista > 0 ? ($p_minorista / $factor_conversion) : 0;
            $pmay = $p_mayorista > 0 ? ($p_mayorista / $factor_conversion) : 0;
            $pdi = $p_distribuidor > 0 ? ($p_distribuidor / $factor_conversion) : 0;

            $data = [
                'id' => $_POST['producto_id'] ?? 0,
                'almacen_id' => $_POST['almacen_actual_id'] ?? 0,
                'sku' => $_POST['sku'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'categoria_id' => $_POST['categoria_id'] ?? null,
                'fiscal_clave_prod' => $_POST['fiscal_clave_prod'] ?? '',
                'fiscal_clave_unit' => $_POST['fiscal_clave_unidad'] ?? '',
                'impuesto_iva' => floatval($_POST['impuesto_iva'] ?? 0),
                'unidad_reporte' => $_POST['unidad_reporte'] ?? '',
                'factor_conversion' => floatval($_POST['factor_conversion'] ?? 1),
                'unidad_medida' => $_POST['unidad_medida'] ?? '',
                'precio_minorista' => $pmin,
                'precio_mayorista' => $pmay,
                'precio_distribuidor' => $pdi,
                'stock' => floatval($_POST['stock'] ?? 0),
                'stock_minimo' => floatval($_POST['stock_minimo'] ?? 0),
                'aplicar_global' => isset($_POST['aplicar_global'])
            ];

            if (!$data['id']) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ID inválido'
                ]);
                exit;
            }

            $res = $this->productoModel->actualizarProductoCompleto($data);

            if ($res['status']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Producto actualizado correctamente'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $res['msg']
                ]);
            }

        } catch (Exception $e) {
            error_log($e->getMessage());

            echo json_encode([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ]);
        }

        exit;
    }
}

/**
 * LÓGICA DE ENRUTAMIENTO
 */
if (isset($conexion)) {
    $controller = new AlmacenController($conexion);
    $action = $_GET['action'] ?? 'index';

    switch ($action) {
        case 'guardar':
            $controller->guardarProducto();
            break;
        case 'guardarCompleto':
            $controller->guardarProductoCompleto();
        case 'guardarCantidadesIniciales':
            $controller->guardarCantidadesIniciales();
            break;
        case 'guardarCategoria':
            $controller->guardarCategoria();
            break;
        case 'getCategoriasJSON':
            $controller->getCategoriasJSON();
            break;
        case 'getUnidadesMedidaJSON':
            $controller->getUnidadesMedidaJSON();
            break;
        case 'getListaProductosJson':
            $controller->getListaProductosJson();
            break;
        case 'getListaProductosJsonAlmacen':
            $controller->getListaProductosJsonAlmacen();
            break;
        case 'getProducto':
            $controller->obtenerProductoDetalle();
            break;
        case 'actualizarProducto':
            $controller->actualizarProducto();
            break;
        case 'getAlmacenesJSON':
            $controller->obtenerListaAlmacenes();
            break;
        default:
            $controller->index();
            break;
    }
} else {
    die("Error: No se pudo establecer la conexión.");
}