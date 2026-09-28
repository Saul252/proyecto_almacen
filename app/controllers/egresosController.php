<?php
/**
 * egresosController.php
 * Controlador para la gestión unificada de Egresos (Compras y Gastos)
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/egresos/comprasModel.php';
require_once __DIR__ . '/../models/proveedoresModel.php';
require_once __DIR__ . '/../models/almacen/categoriasModel.php';
require_once __DIR__ . '/../models/egresos/gastosModel.php';
require_once __DIR__ . '/../models/egresos/insumosModel.php';
require_once __DIR__ . '/../models/categoriasGastosModel.php';

require_once __DIR__ . '/../models/almacen_model.php';

require_once __DIR__ . '/../models/productosModel.php';

$productosModel = new ProductosModel($conexion);

protegerPagina('compras'); 
$almacenMo = new AlmacenModel($conexion);
$egresoModel = new EgresoModel($conexion);
$comprasModel = new CompraModel($conexion);
$insumosModel = new InsumosModel($conexion);
$gastosModel = new GastoModel($conexion);
$categoriasModel = new CategoriaModel($conexion);
$gastosCategorias = new CategoriasGasto($conexion);
$proveedorModel = new ProveedoresModel($conexion);

// --- CORRECCIÓN DE WARNINGS: Definición global de $action ---
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$paginaActual = 'compras';

// =========================================================================
// --- NUEVAS ACCIONES: CRUD CATEGORÍAS DE GASTOS ---
// =========================================================================

if ($action === 'get_categorias_egresos') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $res = $gastosCategorias->listarTodas();
        echo json_encode(["success" => true, "data" => $res]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

if ($action === 'guardar_categoria_gasto') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) throw new Exception("El nombre de la categoría es obligatorio.");

        if ($id > 0) {
            $resultado = $gastosCategorias->actualizar($id, $nombre, $descripcion);
            $mensaje = "Categoría actualizada correctamente";
            $id_final = $id;
        } else {
            $id_final = $gastosCategorias->guardar($nombre, $descripcion);
            $resultado = $id_final ? true : false;
            $mensaje = "Categoría creada correctamente";
        }
        echo json_encode(["success" => $resultado, "message" => $mensaje, "id_insertado" => $id_final]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}
if ($action === 'guardar_nuevo_insumo') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        // ✅ CORRECCIÓN: Mensaje coherente con la acción
        if (empty($nombre)) {
            throw new Exception("El nombre del insumo es obligatorio.");
        }

        // Determinar si es edición o inserción nueva
        if ($id > 0) {
            // ✅ Usa el modelo correcto para insumos si manejas edición aquí
            $resultado = $gastosCategorias->actualizar($id, $nombre, $descripcion); 
            $mensaje = "Insumo actualizado correctamente";
            $id_final = $id;
        } else {
            // ✅ Cambiar $gastosCategorias por tu modelo real de insumos/egresos si aplica
            $id_final = $gastosCategorias->guardarInsumo($nombre, $descripcion);
            $resultado = $id_final ? true : false;
            $mensaje = "Insumo creado correctamente";
        }
        
        echo json_encode([
            "success" => $resultado, 
            "message" => $mensaje, 
            "id_insertado" => $id_final
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage()
        ]);
    }
    exit;
}
if ($action === 'eliminar_categoria') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");
        $resultado = $gastosCategorias->eliminar($id);
        echo json_encode(["success" => $resultado]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

// =========================================================================
// --- ACCIONES ORIGINALES ---
// =========================================================================

if ($action === 'guardarCompraInventario') {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');

    try {
        $user_id = $_SESSION['usuario_id'] ?? 1;
        $rol_id  = $_SESSION['rol_id'] ?? 0;

        // 1. Determinar Almacén
        $almacen_principal = ($rol_id == 1 && isset($_POST['almacen_id_cabecera'])) 
            ? intval($_POST['almacen_id_cabecera']) 
            : ($_SESSION['almacen_id'] ?? null);

        if (!$almacen_principal) throw new Exception("No se pudo determinar el almacén de cargo.");

        // 2. Guardar Compra Principal
        $resultado = $comprasModel->guardarCompraCompleta(
            $_POST['items'] ?? [],
            $_POST['folio'] ?? 'S/F',
            $_POST['proveedor'] ?? 'Sin Proveedor',
            
            (isset($_FILES['evidencia_compra']) && $_FILES['evidencia_compra']['error'] === UPLOAD_ERR_OK) ? $_FILES['evidencia_compra'] : null,
            $almacen_principal,
            $user_id,
            $_POST['metodo_pago'] ?? 'Efectivo'
        );

        if (!$resultado['success']) throw new Exception($resultado['message']);

        // 3. Procesar Saldo / Pago de Deuda
        $saldo = floatval($_POST['saldo_a_pagar'] ?? 0);
        if ($saldo > 0) {
            $proveedor_id = intval($_POST['proveedor'] ?? 0);
            if ($proveedor_id <= 0) throw new Exception("Proveedor inválido para pago de deuda");

            $deudas = $proveedorModel->ProveedorYDeuda($proveedor_id);
            if (empty($deudas)) throw new Exception("El proveedor no tiene deudas pendientes");

            foreach ($deudas as $deuda) {
                if ($saldo <= 0) break;

                $cuenta_id = intval($deuda['compra_id']);
                $pendiente = floatval($deuda['pendiente']);
                $metodoPago = $_POST['metodo_pago'] ?? 'Efectivo';

                if ($cuenta_id <= 0 || $pendiente <= 0) continue;

                $pago_aplicado = min($saldo, $pendiente);

                // A. Actualizar saldo en la tabla de deuda
                $res = $egresoModel->pagarDeudaCompra($cuenta_id, $pago_aplicado);
                $proveedorNombre=$proveedorModel->obtenerPorId($proveedor_id);
                
                // B. Registrar en historial de pagos
                $desc = 'Pago de deuda (Compra #' . $cuenta_id . ') por $' . number_format($pago_aplicado, 2);
                $ref = "PC-" . $cuenta_id; // Evitar string vacío

                $regPago = $egresoModel->registrarPagoCuentaPorPagar(
                    $almacen_principal,
                    $proveedor_id,
                    $cuenta_id,
                    $pago_aplicado,
                    $metodoPago,
                    $ref,
                    $user_id,
                    $desc
                );

                if (!$res || (isset($res['success']) && !$res['success'])) {
                    throw new Exception("Error al descontar saldo de la deuda ID: $cuenta_id");
                }
                
                $saldo -= $pago_aplicado;
            }
        }

        // Devolvemos el resultado final
        echo json_encode([
            'success' => true,
            'message' => 'Compra guardada y saldos actualizados correctamente.',
            'compra_id' => $resultado['compra_id'] ?? null
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error Crítico: ' . $e->getMessage()
        ]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'subirDocumento') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {

        
        
        $id = intval($_POST['compra_id'] ?? 0);
        $folio = $_POST['folio'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        if ($id <= 0) {
            throw new Exception("Compra inválida");
        }

        $documento = $_FILES['documento'] ?? null;

        if (!$documento || $documento['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir archivo");
        }

        // 🔥 SUBIDA
        $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/uploads/compras/";
        if (!is_dir($ruta_carpeta)) mkdir($ruta_carpeta, 0777, true);

        $ext = pathinfo($documento['name'], PATHINFO_EXTENSION);
        $nombre = $tipo . preg_replace('/[^a-zA-Z0-9]/', '_', $folio) . "_" . time() . "." . $ext;

        $destino = $ruta_carpeta . $nombre;


        if (!move_uploaded_file($documento['tmp_name'], $destino)) {
            throw new Exception("No se pudo guardar el archivo");
        }

        $documento_url = "uploads/compras/" . $nombre;

        // 🔥 GUARDAR EN BD
        $ok = $comprasModel->subirDocumentoCompra($tipo,$id,$nombre, $documento_url);

        if (!$ok) {
            throw new Exception("Error al guardar en BD");
        }

        echo json_encode([
            'success' => true,
            'url' => $documento_url
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'eliminarDocumento') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {

        
        
        $id = intval($_POST['id'] ?? 0);
        

        if ($id <= 0) {
            throw new Exception("Elemento inválida");
        }

       

        // 🔥 ELIMINAR EN BD
        $ok = $comprasModel->eliminarDocumento($id);

        if (!$ok) {
            throw new Exception("Error al guardar en BD");
        }

        echo json_encode([
            'success' => true,
            'url' => $id
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'guardarGasto') {
    header('Content-Type: application/json');
    try {
        $rol_id = $_SESSION['rol_id'] ?? 0;
        $almacen_final = ($rol_id == 1) ? intval($_POST['almacen_id'] ?? 0) : intval($_SESSION['almacen_id'] ?? 0);
        if ($almacen_final <= 0) throw new Exception("Almacén no válido.");

        $urlDocumento = '';
    
        $cabecera = [
            'folio'        => $_POST['folio'] ?? 'S/F',
            'fecha'        => $_POST['fecha'] ?? date('Y-m-d'),
            'almacen_id'   => $almacen_final,
            'categoria_id' => $_POST['categoria_id'] ?? null, 
            'usuario_id'   => $_SESSION['usuario_id'] ?? 1,
            'beneficiario' => $_POST['beneficiario'] ?? '',
            'metodo_pago'  => $_POST['metodo_pago'] ?? 'Efectivo',
            'total'        => $_POST['total_final'] ?? 0,
            'documento_url'=> $urlDocumento,
            'observaciones'=> $_POST['observaciones'] ?? ''
        ];

        // 1. Registrar el gasto en la BD
        $res = $egresoModel->registrarGasto($cabecera, $_POST['desc'] ?? [], $_POST['cant'] ?? [], $_POST['precio'] ?? []);
        
        // Validar que se haya obtenido un ID correcto
        $id = $res['id'] ?? 0;
        if ($id <= 0) {
            throw new Exception("No se pudo registrar el gasto en la base de datos.");
        }
            
        $folio = $_POST['folio'] ?? 'SF';
        $tipo = $_POST['gasto'] ?? 'gasto'; // Asegúrate de recibir este valor o por defecto 'gasto'
        $documento = $_FILES['documento'] ?? null;

        // 2. Procesar el archivo SOLO si el usuario realmente subió uno
        if ($documento && $documento['error'] === UPLOAD_ERR_OK) {
            
            $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/uploads/compras/";
            if (!is_dir($ruta_carpeta)) {
                mkdir($ruta_carpeta, 0777, true);
            }

            $ext = pathinfo($documento['name'], PATHINFO_EXTENSION);
            // Limpiamos el folio para evitar caracteres raros en el nombre de archivo
            $folio_limpio = preg_replace('/[^a-zA-Z0-9]/', '_', $folio);
            $nombre = $tipo . "_" . $folio_limpio . "_" . time() . "." . $ext;
            $destino = $ruta_carpeta . $nombre;

            // Mover archivo temporal al destino real
            if (!move_uploaded_file($documento['tmp_name'], $destino)) {
                throw new Exception("Gasto registrado, pero no se pudo guardar el archivo físico.");
            }

            $documento_url = "uploads/compras/" . $nombre;

            // 3. Guardar la relación del documento en la BD
            $ok = $comprasModel->subirDocumentoCompra($tipo, $id, $nombre, $documento_url);
            if (!$ok) {
                throw new Exception("Gasto registrado, pero falló al vincular el documento en la base de datos.");
            }
        } 
        // Descomenta las siguientes líneas si para ti subir el archivo es OBLIGATORIO:
        // else {
        //     throw new Exception("El documento digital es obligatorio.");
        // }

        // 4. RESPUESTA ÚNICA DE ÉXITO (Al final del flujo)
        echo json_encode(['success' => true, 'message' => 'Gasto guardado correctamente','id'=>$id]);
        
    } catch (Exception $e) {
        // Si algo falla, el catch se encarga de mandar la respuesta limpia de error
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}if ($action === 'guardarGastoInsumo') {
    header('Content-Type: application/json');
    try {
        $rol_id = $_SESSION['rol_id'] ?? 0;
        $almacen_final = ($rol_id == 1) ? intval($_POST['almacen_id'] ?? 0) : intval($_SESSION['almacen_id'] ?? 0);
        if ($almacen_final <= 0) throw new Exception("Almacén no válido.");

        $urlDocumento = '';
        $desc=$_POST['precio'];
       
      
        $cabecera = [
            'folio'        => $_POST['folio'] ?? 'S/F',
            'fecha'        => $_POST['fecha'] ?? date('Y-m-d'),
            'almacen_id'   => $almacen_final,
            'categoria_id' => $_POST['categoria_id'] ?? null, 
            'usuario_id'   => $_SESSION['usuario_id'] ?? 1,
            'beneficiario' => $_POST['beneficiario'] ?? '',
            'metodo_pago'  => $_POST['metodo_pago'] ?? 'Efectivo',
            'total'        => $_POST['total_final'] ?? 0,
            'documento_url'=> $urlDocumento,
            'observaciones'=> $_POST['observaciones'] ?? ''
        ];
        // 1. Registrar el gasto en la BD
        $res = $egresoModel->registrarGastoInsumo($cabecera, $_POST['desc'] ?? [], $_POST['cant'] ?? [], $_POST['precio'] ?? [],$_POST['items'] ?? []);
        
        // Validar que se haya obtenido un ID correcto

        $id = $res['id'] ?? 0;
        if ($id <= 0) {
            throw new Exception("No se pudo registrar el gasto en la base de datos.");
        }
            
        $folio = $_POST['folio'] ?? 'SF';
        $tipo = $_POST['gasto'] ?? 'gasto'; // Asegúrate de recibir este valor o por defecto 'gasto'
        $documento = $_FILES['documento'] ?? null;

        // 2. Procesar el archivo SOLO si el usuario realmente subió uno
        if ($documento && $documento['error'] === UPLOAD_ERR_OK) {
            
            $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/myvet/uploads/compras/";
            if (!is_dir($ruta_carpeta)) {
                mkdir($ruta_carpeta, 0777, true);
            }

            $ext = pathinfo($documento['name'], PATHINFO_EXTENSION);
            // Limpiamos el folio para evitar caracteres raros en el nombre de archivo
            $folio_limpio = preg_replace('/[^a-zA-Z0-9]/', '_', $folio);
            $nombre = $tipo . "_" . $folio_limpio . "_" . time() . "." . $ext;
            $destino = $ruta_carpeta . $nombre;

            // Mover archivo temporal al destino real
            if (!move_uploaded_file($documento['tmp_name'], $destino)) {
                throw new Exception("Gasto registrado, pero no se pudo guardar el archivo físico.");
            }

            $documento_url = "uploads/compras/" . $nombre;

            // 3. Guardar la relación del documento en la BD
            $ok = $comprasModel->subirDocumentoCompra($tipo, $id, $nombre, $documento_url);
            if (!$ok) {
                throw new Exception("Gasto registrado, pero falló al vincular el documento en la base de datos.");
            }
        } 
        // Descomenta las siguientes líneas si para ti subir el archivo es OBLIGATORIO:
        // else {
        //     throw new Exception("El documento digital es obligatorio.");
        // }

        // 4. RESPUESTA ÚNICA DE ÉXITO (Al final del flujo)
        echo json_encode(['success' => true, 'message' => 'Gasto guardado correctamente']);
        
    } catch (Exception $e) {
        // Si algo falla, el catch se encarga de mandar la respuesta limpia de error
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'buscarProductos') {
    header('Content-Type: application/json');
    $termino = $_GET['q'] ?? '';
    $productos = $comprasModel->obtenerProductos($termino);
    echo json_encode($productos);
    exit;
}

if ($action === 'obtenerFaltantes') {
    header('Content-Type: application/json');
    $compra_id = intval($_GET['compra_id'] ?? 0);
    $faltantes = $comprasModel->obtenerDetalleFaltantes($compra_id);
    echo json_encode($faltantes);
    exit;
}
if ($action === 'aplicarFaltantesCompras') {
    header('Content-Type: application/json');
    $compra_id = intval($_GET['compra_id'] ?? 0);
    $faltantes = $comprasModel->aplicarFaltantesCompra($compra_id);
    echo json_encode($faltantes);
    exit;
}
if ($action === 'actualizarExcedente') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $user_id = $_SESSION['usuario_id'] ?? 1;

        // ✅ Cambiar $_GET por $_POST
        $compra_id   = intval($_POST['compra_id'] ?? 0);
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $excedente   = floatval($_POST['excedente'] ?? 0);

        if ($compra_id <= 0 || $producto_id <= 0) {
            throw new Exception("Datos de compra o producto inválidos.");
        }

        if ($excedente < 0) {
            throw new Exception("La cantidad excedente no puede ser negativa.");
        }

        $ok = $comprasModel->actualizarExcedenteProducto($compra_id, $producto_id, $excedente, $user_id);

        if (!$ok) {
            throw new Exception("Error al actualizar la cantidad excedente en la BD.");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Cantidad excedente actualizada correctamente.'
        ]);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'procesarAjusteFaltante') {
    header('Content-Type: application/json');
    try {
        $compra_id = intval($_POST['compra_id'] ?? 0);
        $distribucion = $_POST['distribucion'] ?? [];
        $user_id = $_SESSION['usuario_id'] ?? 1;
        if ($compra_id <= 0 || empty($distribucion)) throw new Exception("Datos no válidos.");
        $res = $comprasModel->procesarAjusteFaltante($compra_id, $distribucion, $user_id);
        echo json_encode($res);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'getSiguienteFolioGasto') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    try {
        $almacen=$_GET['almacen'] ??  $_SESSION['almacen_id'];
        $siguiente = $gastosModel->generarSiguienteFolioGasto($almacen);
        echo json_encode(['success' => true, 'folio' => $siguiente]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'getSiguienteFolio') {
    header('Content-Type: application/json');
     $almacen=$_GET['almacen_id'] ??  $_SESSION['almacen_id'];
    $siguiente = $comprasModel->generarSiguienteFolio($almacen);
    echo json_encode(['success' => true, 'folio' => $siguiente]);
    exit;
}

if ($action === 'obtenerDetalleMovimiento') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    $tipo = $_GET['tipo'] ?? '';
    $id = intval($_GET['id'] ?? 0);
    try {
        $resultado = $egresoModel->obtenerDetalleCompleto($tipo, $id);
        if ($resultado && $resultado['cabecera']) {
            echo json_encode(['success' => true, 'cabecera' => $resultado['cabecera'], 'items' => $resultado['items']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el registro.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'obtenerDistribucionProducto') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    $producto = $_GET['tipo'] ?? '';
    $id = intval($_GET['id'] ?? 0);
    try {
        $resultado = $egresoModel->obtenerDistribucionCompra($id, $producto);
        if ($resultado && $resultado['cabecera']) {
            echo json_encode(['success' => true, 'cabecera' => $resultado['cabecera'], 'items' => $resultado['items']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el registro.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'obtenerDetallePago') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');

    $id = intval($_GET['id'] ?? 0);

    try {
        $resultado = $egresoModel->obtenerDetalleCompletoPago($id);

        if (!empty($resultado)) {
            echo json_encode([
                'success' => true,
                'data' => $resultado // 🔥 TODO viene aquí
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró el registro.'
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'obtenerDetalleMovimientoConProveedores') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    $tipo = $_GET['tipo'] ?? '';
    $id = intval($_GET['id'] ?? 0);
    try {
        $resultado = $egresoModel->obtenerDetalleCompletoConProveedores($tipo, $id);
        if ($resultado && $resultado['cabecera']) {
            echo json_encode(['success' => true, 'cabecera' => $resultado['cabecera'], 'items' => $resultado['items']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el registro.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'getProveedoresJSON') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        $lista = $proveedorModel->listarTodos();
        echo json_encode($lista ?: []);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'getProveedoresJSONAlmacen') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        $almacen_actual=$_GET['almacen']?? $_SESSION['almacen_id'];
        $lista = $proveedorModel->listarTodosProveedorsYDeuda($almacen_actual);
        echo json_encode($lista ?: []);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'guardarProveedor') {

    while (ob_get_level()) ob_end_clean();

    header('Content-Type: application/json; charset=utf-8');

    try {

        $datos = [
            'nombre_comercial' => trim($_POST['nombre_comercial'] ?? ''),
            'razon_social'     => trim($_POST['razon_social'] ?? ''),
            'rfc'              => trim($_POST['rfc'] ?? 'XAXX010101000'),
            'correo'           => trim($_POST['correo'] ?? ''),
            'contacto'         => trim($_POST['contacto'] ?? ''),
            'telefono'         => trim($_POST['telefono'] ?? '0'),
            'telefono2'        => trim($_POST['telefono2'] ?? '0'),
            'extencion'        => trim($_POST['extencion'] ?? '0'),
            'almacen_id'       => trim($_POST['almacen_id'] ?? ''),
            'direccion'        => trim($_POST['direccion'] ?? ''),
            'numeroExt'        => trim($_POST['numeroext'] ?? '0'),
            'numeroInt'        => trim($_POST['numeroint'] ?? '0'),
            'colonia'          => trim($_POST['colonia'] ?? ''),
            'ciudad'           => trim($_POST['ciudad'] ?? '')
        ];

        if (empty($datos['nombre_comercial'])) {
            throw new Exception("El nombre comercial es obligatorio.");
        }

        // guardar respuesta
        $respuesta = $proveedorModel->guardar($datos);

        // validar success
        if ($respuesta['success']) {

            echo json_encode([
                'success'       => true,
                'message'       => 'Proveedor guardado',
                'nuevo_nombre'  => $datos['nombre_comercial'],
                'id'            => $respuesta['id']
            ]);

        } else {

            throw new Exception(
                $respuesta['message'] ?? 'Error interno al registrar.'
            );
        }

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'cancelarCompra') {
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        if (!isset($_SESSION['usuario_id'])) throw new Exception("Sesión expirada.");
        $id_compra = intval($_POST['id'] ?? 0);
        $id_usuario = $_SESSION['usuario_id'];
        if ($id_compra <= 0) throw new Exception("ID de compra inválido.");
        $resultado = $comprasModel->cancelarCompra($id_compra, $id_usuario);
        $cancelarCuentaPorPagar = $egresoModel->cancelarDeuda($id_compra);

        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'cancelarGasto') {
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        if (!isset($_SESSION['usuario_id'])) throw new Exception("Sesión expirada.");
        $id_gasto = intval($_POST['id'] ?? 0);
        $id_usuario = $_SESSION['usuario_id'];
        $razon = trim($_POST['razon'] ?? '');
        if ($id_gasto <= 0) throw new Exception("ID de gasto inválido.");
        if (empty($razon)) throw new Exception("Es obligatorio proporcionar una razón.");
        $resultado = $gastosModel->cancelarGastoConRazon($id_gasto, $id_usuario, $razon);
        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- DENTRO DE: if ($_SERVER['REQUEST_METHOD'] === 'POST') ---
// Cambié $accion por $action para que coincida con tu declaración superior// Busca la línea donde recibes la acción
$action = $_GET['action'] ?? $_POST['action'] ?? $_POST['ajax'] ?? '';

if ($action === 'guardar_categoria_egreso') {
    // 1. Limpieza total de búfer para que no haya ni un espacio en blanco antes del {
    while (ob_get_level()) ob_end_clean(); 
    
    header('Content-Type: application/json; charset=utf-8');

    try {
        $nombre = trim($_POST['nombre'] ?? '');
        
        if (empty($nombre)) {
            throw new Exception("El nombre de la categoría es obligatorio.");
        }

        // Usamos la instancia que ya definiste al inicio de tu controller
        // Si tu modelo devuelve el ID insertado:
        $id_final = $gastosCategorias->guardar($nombre, ''); 

        if ($id_final) {
            echo json_encode([
                "success" => true, 
                "id_insertado" => $id_final, 
                "message" => "Categoría creada correctamente"
            ]);
        } else {
            throw new Exception("Error al guardar en la base de datos.");
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage()
        ]);
    }
    // 2. OBLIGATORIO: Detener el script aquí
    exit; 
}
if ($action === 'guardar_insumo') {
    // 1. Limpieza total de búfer para que no haya ni un espacio en blanco antes del {
    while (ob_get_level()) ob_end_clean(); 
    
    header('Content-Type: application/json; charset=utf-8');

    try {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $uma=trim($_POST['uma'] ?? '');
        $umi=trim($_POST['umi'] ?? '');
        $factor=($_POST['factor'] ?? 1);
        
        if (empty($nombre)) {
            throw new Exception("El nombre de la categoría es obligatorio.");
        }

        // Usamos la instancia que ya definiste al inicio de tu controller
        // Si tu modelo devuelve el ID insertado:
        $id_final = $gastosCategorias->guardarInsumo($nombre, $descripcion,$uma,$umi,$factor); 

        if ($id_final) {
            echo json_encode([
                "success" => true, 
                "id_insertado" => $id_final, 
                "message" => "Categoría creada correctamente"
            ]);
        } else {
            throw new Exception("Error al guardar en la base de datos.");
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage()
        ]);
    }
    // 2. OBLIGATORIO: Detener el script aquí
    exit; 
}
// Acción para registrar la deuda nacida de un exceso en compras o gastos
if ($action === 'registrarDeudaPorExceso') {
    // Limpiamos el buffer para evitar que espacios en blanco rompan el JSON de salida
    while (ob_get_level()) ob_end_clean(); 
    
    // Definimos cabecera para respuesta JSON
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // 1. Recolección de datos desde el Formulario y Sesión
        $datos = [
            // Priorizamos el almacén de la operación original enviado desde el modal
            'id_almacen'           => !empty($_POST['id_almacen']) ? intval($_POST['id_almacen']) : ($_SESSION['id_almacen'] ?? null),
            'id_proveedor'         => !empty($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : null,
            'beneficiario'         => trim($_POST['beneficiario'] ?? ''),
            'id_referencia_origen' => trim($_POST['id_referencia_origen'] ?? ''),
            'origen_tipo'          => trim($_POST['origen_tipo'] ?? 'compra'),
            'monto_total'          => floatval($_POST['monto_total'] ?? 0),
            'montopagado'          => 0,
            'tipo_deuda'           => 'excedente_material', // Categoría fija para este proceso
            'notas'                => "Ajuste generado por exceso en " . ($_POST['origen_tipo'] ?? 'operación') . " #" . ($_POST['id_referencia_origen'] ?? 'S/N')
        ];

        // 2. Validaciones Críticas
        if (!$datos['id_almacen']) {
            throw new Exception("Error: No se pudo identificar el almacén de origen.");
        }
        if (empty($datos['beneficiario'])) {
            throw new Exception("El nombre del beneficiario es obligatorio para el registro.");
        }
        if ($datos['monto_total'] <= 0) {
            throw new Exception("La cantidad excedente debe generar un monto mayor a $0.00.");
        }
        if (empty($datos['id_referencia_origen'])) {
            throw new Exception("Falta la referencia (ID) de la operación de origen.");
        }

        // 3. Llamada al Modelo para insertar la obligación financiera
        // Asumiendo que instanciaste el modelo como $cuentasPagarModel
        $resultado = $egresoModel->registrarObligacionFinanciera($datos);

        if ($resultado['success']) {
            // Respuesta exitosa para el SweetAlert del JS
            echo json_encode([
                'success' => true, 
                'message' => 'Cuenta por pagar registrada correctamente.',
                'id_deuda' => $resultado['id']
            ]);
        } else {
            // Error devuelto por el SQL o el Modelo
            throw new Exception($resultado['message'] ?? "Error interno al procesar el registro.");
        }

    } catch (Exception $e) {
        // Respuesta en caso de error o excepción
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
// if ($action === 'listarCuentasPorPagar') {

//     while (ob_get_level()) ob_end_clean();
//     header('Content-Type: application/json');

//     try {

//         $filtros = [
//             'busqueda'     => $_GET['busqueda'] ?? '',
//             'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
//             'fecha_fin'    => $_GET['fecha_fin'] ?? '',
//             'limit'        => $_GET['limit'] ?? 10,
//             'offset'       => $_GET['offset'] ?? 0
//         ];

//         $res = $cuentasPagarModel->listarCuentasPorPagar($filtros);

//         echo json_encode([
//             "success" => true,
//             "data" => $res['data'],
//             "total" => $res['total']
//         ]);

//     } catch (Exception $e) {
//         echo json_encode([
//             "success" => false,
//             "message" => $e->getMessage()
//         ]);
//     }

//     exit;
// }
if ($action === 'obtenerDeudaCompra') {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $id = intval($_GET['id'] ?? 0);

        $data = $egresoModel->obtenerDeudaPorCompra($id);

        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró la deuda'
            ]);
            exit;
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
if ($action === 'pagarDeudaCompra') {

    header('Content-Type: application/json');

    $cuenta_id = intval($_POST['cuenta_id'] ?? 0);
    $monto     = floatval($_POST['monto'] ?? 0);

    if ($cuenta_id <= 0 || $monto <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Datos inválidos'
        ]);
        exit;
    }


   

    $result = $egresoModel->pagarDeudaCompra($cuenta_id, $monto);

    echo json_encode($result);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'obtenerProveedores') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
       
       
            $proveedores = $proveedorModel->listarTodosProveedores(0); 
        
        
        
        if ($proveedores) {
            echo json_encode(['success' => true, 'data' => $proveedores]);
        } else {
            throw new Exception('Usuarios no encontrado.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'obtenerProductosSelect') {

    header('Content-Type: application/json');

    try {

        $listaProductos= $productosModel->listarTodo();

        echo json_encode([
            'success' => true,
            'data' => $listaProductos
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'obtenerInsumosSelect') {

    header('Content-Type: application/json');

    try {

      $insumos= $insumosModel-> listarTodo();

        echo json_encode([
            'success' => true,
            'data' => $insumos
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
if ($action === 'guardarCategoria') {

    // 🔥 LIMPIAR CUALQUIER SALIDA (evita romper JSON)
    while (ob_get_level()) ob_end_clean();

    header('Content-Type: application/json; charset=utf-8');

    try {

        // 🔥 VALIDAR QUE EL MODELO EXISTA
        if (!isset($categoriasModel)) {
            throw new Exception("Modelo de categorías no inicializado");
        }

        // 🔥 OBTENER Y LIMPIAR DATO
        $nombre = trim($_POST['nombre'] ?? '');
        
         $almacen = ($_POST['almacen'] ?? 10);

        if ($nombre === '') {
            throw new Exception("El nombre es obligatorio");
        }

        // 🔥 VALIDAR DUPLICADO
        if ($categoriasModel->existe($nombre,$almacen)) {
            throw new Exception("Esta categoría ya existe");
        }

        // 🔥 GUARDAR
        $id = $categoriasModel->guardar($nombre,$almacen);

        if (!$id) {
            throw new Exception("No se pudo guardar la categoría");
        }

        // 🔥 RESPUESTA EXITOSA
        echo json_encode([
            'status' => 'success',
            'id' => $id,
            'nombre' => $nombre
        ]);

    } catch (Throwable $e) {

        // 🔥 RESPUESTA DE ERROR LIMPIA
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($action)) {
    date_default_timezone_set('America/Mexico_City');

    // 1. Lógica de Fechas Dinámica (Filtro Rápido)
    $periodo_sel = $_GET['periodo_filtro'] ?? 'mes';
    $fecha_desde = $_GET['desde'] ?? date('Y-m-01');
    $fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');

    // Si el usuario eligió un periodo predefinido, calculamos las fechas aquí
    if ($periodo_sel !== 'personalizado') {
        switch ($periodo_sel) {
            case 'hoy':
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = date('Y-m-d');
                break;
            case 'ayer':
                $fecha_desde = date('Y-m-d', strtotime('-1 day'));
                $fecha_hasta = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'semana':
                $fecha_desde = date('Y-m-d', strtotime('monday this week'));
                $fecha_hasta = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'mes':
                $fecha_desde = date('Y-m-01');
                $fecha_hasta = date('Y-m-t');
                break;
        }
    }

    // 2. Filtros de Categoría y Tipo
    $tipo_filtro = $_GET['tipo_filtro'] ?? 'todos'; 
    $categoria_gasto_id = isset($_GET['categoria_gasto_filtro']) ? intval($_GET['categoria_gasto_filtro']) : 0;

    // 3. Seguridad por Almacén y Rol
    $rol_id = $_SESSION['rol_id'] ?? 0;
    $mi_almacen_id = $_SESSION['almacen_id'] ?? 0;

    $almacen_a_consultar = ($rol_id == 1)
        ? (isset($_GET['almacen_filtro']) ? intval($_GET['almacen_filtro']) : 0)
        : $mi_almacen_id;

    // 4. Filtros Financieros
    $deuda_filtro  = $_GET['deuda_filtro'] ?? 'todos';
    $metodo_filtro = $_GET['metodo_filtro'] ?? 'todos';

    // 5. Consulta al Modelo
    $egresos = $egresoModel->obtenerTodosLosEgresosFiltros(
        $fecha_desde,
        $fecha_hasta,
        $almacen_a_consultar,
        $tipo_filtro,
        $categoria_gasto_id,
        $deuda_filtro,
        $metodo_filtro
    );

    // 6. Cálculos de Totales
    $totalSumCompras = 0;
    $totalSumGastos = 0;

    foreach ($egresos as $e) {
        if ($e['tipo'] == 'compra' || $e['tipo'] == 'pago_deuda') {
            $totalSumCompras += $e['total'];
        } else {
            $totalSumGastos += $e['total'];
        }
    }

    $granTotalEgresos = $totalSumCompras + $totalSumGastos;
$almacen_actual= $_SESSION['almacen_id'];
    // 7. Carga de Catálogos para la Vista
    $listaCategoriasGastos = $gastosCategorias->listarTodas();
    $almacenes = $egresoModel->obtenerAlmacenesActivos();
    $almacenUsuario= $almacenMo->getAlmacenes($almacen_actual);
    $productos = $comprasModel->obtenerProductosAlmacen(1);
   $insumos= $insumosModel-> listarTodo();
    $listaProductos= $productosModel->listarTodoAlmacen($almacen_actual);
    $proveedores = $proveedorModel->listarTodosProveedorsYDeuda($almacen_actual); 
$unidadesMedida= $almacenMo->getUnidadesMedida();

    $tituloPagina = "Gestión de Egresos";

    // Pasar variables adicionales a la vista para mantener el estado de los inputs
    // $periodo_sel, $fecha_desde, $fecha_hasta ya están listas
    
    require_once __DIR__ . '/../views/egresos_view.php';
    exit;
}