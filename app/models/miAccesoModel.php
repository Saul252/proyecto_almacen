<?php
class MiAccesoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Obtiene los datos del almacén/sucursal según el ID de la sesión
     */
    public function obtenerPorId($almacen_id) {
        $sql = "SELECT id, codigo, nombre, hora_cierre_programada, ubicacion, activo, fecha_creacion, logo,ico, tipo_plan 
                FROM almacenes 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Actualiza la información del almacén y procesa el logo si fue subido
     */
    public function actualizar($id, $datos, $archivoLogo = null, $archivoIco = null) {
        // 1. Manejo de la imagen del Logo
        $rutaLogo = $datos['logo_actual'] ?? null;
        $rutaIco = $datos['ico_actual'] ?? null;

        if ($archivoLogo && $archivoLogo['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($archivoLogo['name'], PATHINFO_EXTENSION));
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $extensionesPermitidas)) {
                $directorio = $_SERVER['DOCUMENT_ROOT'] .  '/myvet/uploads/compras/logos/';
                
                if (!is_dir($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $nombreArchivo = 'logo_almacen_' . $id . '_' . time() . '.' . $ext;
                $rutaDestino = $directorio . $nombreArchivo;

                if (move_uploaded_file($archivoLogo['tmp_name'], $rutaDestino)) {
                    // Borrar logo anterior si existe
                    if (!empty($rutaLogo) && file_exists(__DIR__ . '/../../' . $rutaLogo)) {
                        @unlink(__DIR__ . '/../../' . $rutaLogo);
                    }
                    $rutaLogo = 'uploads/compras/logos/' . $nombreArchivo;
                }
            }
        }
  if ($archivoIco && $archivoIco['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($archivoIco['name'], PATHINFO_EXTENSION));
    
    // 1. Permitir extensión 'ico' (y opcionalmente png/jpg por si suben favicon en ese formato)
    $extensionesPermitidas = ['ico', 'png', 'jpg', 'jpeg'];

    if (in_array($ext, $extensionesPermitidas)) {
        $directorio = $_SERVER['DOCUMENT_ROOT'] . '/myvet/uploads/compras/logos/';
        
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombreArchivo = 'ico_almacen_' . $id . '_' . time() . '.' . $ext;
        $rutaDestino = $directorio . $nombreArchivo;

        if (move_uploaded_file($archivoIco['tmp_name'], $rutaDestino)) {
            // 2. Eliminar el archivo ICO anterior (usando la misma estructura de ruta con DOCUMENT_ROOT)
            if (!empty($rutaIco)) {
                $archivoAnterior = $_SERVER['DOCUMENT_ROOT'] . '/myvet/' . $rutaIco;
                if (file_exists($archivoAnterior)) {
                    @unlink($archivoAnterior);
                }
            }
            
            // 3. Asignar a la variable correcta ($rutaIco)
            $rutaIco = 'uploads/compras/logos/' . $nombreArchivo;
        }
    }
}

        // 2. Actualización en Base de Datos
        $sql = "UPDATE almacenes 
                SET nombre = ?, 
                    hora_cierre_programada = ?, 
                    ubicacion = ?, 
                    logo = ? ,
                    ico=?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "sssssi", 
            $datos['nombre'], 
            $datos['hora_cierre_programada'], 
            $datos['ubicacion'], 
            $rutaLogo,
            $rutaIco, 
            $id
        );

        return $stmt->execute();
    }
}