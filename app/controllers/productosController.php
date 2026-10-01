<?php

// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/mermasModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/almacen/productosModel.php';

$productosModel = new ProductoModel($conexion);
$mermasModel = new MermasModel($conexion);
$almacenModel = new AlmacenModel($conexion);


// ========================================
// ACTION
// ========================================

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // ========================================
    // GUARDAR OPCIÓN DE MEDIDA
    // ========================================

    case 'guardarOpcionMedida':

        while (ob_get_level())
            ob_end_clean();

        header('Content-Type: application/json; charset=utf-8');

        try {

            $data = [
                'producto_id' => intval($_POST['producto_id'] ?? 0),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'equivalencia' => floatval($_POST['equivalencia'] ?? 0)
            ];

            if ($data['producto_id'] <= 0) {
                throw new Exception("Producto inválido");
            }

            if ($data['nombre'] === '') {
                throw new Exception("Nombre requerido");
            }

            if ($data['equivalencia'] <= 0) {
                throw new Exception("Equivalencia inválida");
            }

            $resultado = $productosModel->guardarOpcionMedida($data);

            echo json_encode($resultado);

        } catch (Exception $e) {

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;



    // ========================================
    // OBTENER DETALLE PRODUCTO
    // ========================================

    case 'obtenerProductoDetalle':

        while (ob_get_level())
            ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id = intval($_GET['id'] ?? 0);
            $almacen_id = intval($_GET['almacen_id'] ?? 0);

            if ($id <= 0 || $almacen_id <= 0) {

                echo json_encode([
                    'status' => 'error',
                    'message' => 'Parámetros incompletos'
                ]);

                exit;
            }

            $resultado = $productosModel
                ->obtenerProductoPorAlmacen($id, $almacen_id);

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

        } catch (Exception $e) {

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        exit;

    case 'guardarProducto':
        while (ob_get_level())
            ob_end_clean(); // Asegurar respuesta limpia, quitar despues para un solo almacen ya que este es para cf
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
            'precio_distribuidor' => $pdi,
            'almacen' => $_POST['select_almacen_id'] ?? 1
        ];

        if (empty($datos['sku']) || empty($datos['nombre'])) {
            echo json_encode(['status' => 'error', 'message' => 'SKU y Nombre son obligatorios']);
            exit;
        }
        //$nuevoId = $this->productoModel->guardarCompleto($datos);//este es el original para un solo almacen
        $nuevoId = $productosModel->guardarCompletoMultiALmacen($datos);

        if ($nuevoId) {
            echo json_encode(['status' => 'success', 'message' => 'Producto registrado exitosamente', 'id' => $nuevoId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el producto.']);
        }
        exit;

    case 'getCategoriasJSON':
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        try {
            $almacen = intval($_GET['almacenId'] ?? 1);
            $categorias = $almacenModel->getCategoriasAlmacen($almacen);
            echo json_encode($categorias ?: []);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;

    case 'getUnidadesMedidaJSON':
        while (ob_get_level())
            ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        try {
            $unidadesMedida = $almacenModel->getUnidadesMedida();
            echo json_encode($unidadesMedida ?: []);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;

    // Añade este método antes del final de la llave de la clase }


    case 'obtnerMedidas':

        while (ob_get_level())
            ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {

                echo json_encode([
                    'status' => 'error',
                    'message' => 'Parámetros incompletos'
                ]);

                exit;
            }

            $medidas = $productosModel
                ->listarMedidas($id);

            if ($medidas['status']) {

                echo json_encode([
                    'status' => 'success',
                    'producto' => $medidas
                ]);

            } else {

                echo json_encode([
                    'status' => 'error',
                    'message' => $medidas['msg']
                ]);
            }

        } catch (Exception $e) {

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        exit;

    // ========================================
    // ACTUALIZAR MEDIDA
    // ========================================

    case 'actualizarMedidaAdicional':

        while (ob_get_level())
            ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id = intval($_POST['id'] ?? 0);
            $producto_id = intval($_POST['producto_id'] ?? 0);
            $nombre = trim($_POST['nombre_edit'] ?? '');
            $rawEquiv = floatval($_POST['equivalencia'] ?? 0);
            $base = floatval($_POST['base'] ?? 0);
            $equivalencia = ($rawEquiv != 0) ? (1 / $rawEquiv) : 0;

            if ($id <= 0) {
                throw new Exception("ID inválido");
            }

            if ($producto_id <= 0) {
                throw new Exception("Producto inválido");
            }

            if ($nombre === '') {
                throw new Exception("Nombre requerido");
            }

            if ($equivalencia <= 0) {
                throw new Exception("Equivalencia inválida");
            }
            if ($base > 1) {
                $equivalenciaFinal = 1 / $base;

            } else {
                $equivalenciaFinal = 1 / $equivalencia;

            }

            $resultado = $productosModel->actualizarMedidaAdicional(
                $id,
                $producto_id,
                $nombre,
                $equivalenciaFinal
            );

            echo json_encode($resultado);

        } catch (Exception $e) {

            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;



    // ========================================
    // ELIMINAR MEDIDA
    // ========================================

    case 'eliminarMedidaAdicional':

        while (ob_get_level())
            ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception("ID inválido");
            }

            $resultado = $productosModel->eliminarMedidaAdicional($id);

            echo json_encode($resultado);

        } catch (Exception $e) {

            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;



    // ========================================
    // DEFAULT
    // ========================================

    default:

        while (ob_get_level())
            ob_end_clean();

        header('Content-Type: application/json');

        echo json_encode([
            'status' => false,
            'message' => 'Acción no válida'
        ]);

        exit;
}
