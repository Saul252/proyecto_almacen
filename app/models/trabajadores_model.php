<?php
class TrabajadorModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar todos (Solo para Admin Global)
    public function listar() {
        $sql = "SELECT * FROM trabajadores ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
     public function subirDocumentoCompra($id, $nombre_evidencia, $documento_url)
{
    $sql = "INSERT INTO documentos_trabajadores
            (trabajador_id, nombre, direccion)
            VALUES (?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $this->db->error);
    }

    $stmt->bind_param(
        "iss",
        $id,
        $nombre_evidencia,
        $documento_url
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar documento: " . $stmt->error);
    }

    $documento_id = $stmt->insert_id;

    $stmt->close();

    return [
        'success' => true,
        'documento_id' => $documento_id,
        'message' => 'Documento guardado correctamente'
    ];
}
public function listarTrabajadores($almacen_id = 0) {

    if ($almacen_id == 0) {
        // 🔥 ADMIN → todos
        $sql = "SELECT t.*, a.nombre as nombreAlmacen,
          ( SELECT GROUP_CONCAT(
        CONCAT(
            IFNULL(nombre, ''),
            '|||',
            IFNULL(direccion, ''),
            '|||',
            IFNULL(id, '')
        )
        SEPARATOR ';;;'
    )
    FROM documentos_trabajadores dt
    WHERE dt.trabajador_id = t.id and dt.activo=1
) AS documentos_url
        FROM trabajadores t
        Join almacenes a on t.almacen_id =a.id
        ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
    } else {
        // 🔒 SUCURSAL → solo su almacén
        $sql = "SELECT t.*, a.nombre as nombreAlmacen,
         ( SELECT GROUP_CONCAT(
        CONCAT(
            IFNULL(nombre, ''),
            '|||',
            IFNULL(direccion, ''),
            '|||',
            IFNULL(id, '')
        )
        SEPARATOR ';;;'
    )
    FROM documentos_trabajadores dt
    WHERE dt.trabajador_id = t.id and dt.activo=1
) AS documentos_url
FROM trabajadores t
JOIN almacenes a ON t.almacen_id = a.id
WHERE t.almacen_id = ?
ORDER BY nombre ASC;";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

    // NUEVO: Listar por almacén específico
    public function listarPorAlmacen($almacen_id) {
        $id = intval($almacen_id);
        $sql = "SELECT * FROM trabajadores WHERE almacen_id  = $id AND rol!='Administrador'ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
     public function listarPorAlmacenEncargado($almacen_id) {
        $id = intval($almacen_id);
        $sql = "SELECT * FROM trabajadores WHERE almacen_id  = $id AND rol='Administrador'ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
 public function listarTrabajadoresDisponiblesPorAlmacen($almacen_id) {
    $id = intval($almacen_id);

    $sql = "        SELECT t.*
        FROM trabajadores t
        WHERE t.almacen_id = $id
        AND rol!='Administrador'
        AND t.id NOT IN (

            -- Encargados en rutas activas
            SELECT rm.usuario_encargado_id
            FROM transporte_repartos_maestro rm
            WHERE rm.estado_reparto = 'en_transito'
            AND rm.usuario_encargado_id IS NOT NULL

            UNION

            -- Tripulantes en rutas activas
            SELECT td.usuario_id
            FROM transporte_tripulantes_detalle td
            INNER JOIN transporte_repartos_maestro rm2 
                ON rm2.id = td.reparto_id
            WHERE rm2.estado_reparto = 'en_transito'
        )
        ORDER BY t.nombre ASC
    ";

    $res = $this->db->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
    public function guardar($d) {
        $nombre = $this->db->real_escape_string($d['nombre']);
        $tel    = $this->db->real_escape_string($d['telefono']);
        $rol    = $this->db->real_escape_string($d['rol']);
        $estado = $this->db->real_escape_string($d['estado']); 
        $salario = $this->db->real_escape_string($d['salario']);
        $complemento = $this->db->real_escape_string($d['complemento']);
          $fecha_ingreso = $this->db->real_escape_string($d['fecha_ingreso']);
        $alm_id = intval($d['almacen_id']); // Nueva columna crítica

        if (!empty($d['id'])) {
            // EDITAR: Incluimos almacen_id por si el admin global lo mueve de sucursal
            $id = intval($d['id']);
            $sql = "UPDATE trabajadores 
                    SET nombre='$nombre', telefono='$tel', rol='$rol', estado='$estado', almacen_id=$alm_id ,salario='$salario',complemento_pago='$complemento',fecha_ingreso='$fecha_ingreso'
                    WHERE id=$id";
        } else {
            // INSERTAR: Obligatorio asignar el almacén desde el inicio
            $sql = "INSERT INTO trabajadores (nombre, telefono, rol, estado, almacen_id,salario,complemento_pago,fecha_ingreso) 
                    VALUES ('$nombre', '$tel', '$rol', '$estado', $alm_id,$salario,$complemento,$fecha_ingreso)";
        }
        return $this->db->query($sql);
    }

    public function eliminar($id) {
        $id = intval($id);
        return $this->db->query("DELETE FROM trabajadores WHERE id = $id");
    }
       public function listarPersonal($almacen_id = 0) {
        // Si mandas 0, busca en todos (opcional), si no, filtra por sucursal
        $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = " . intval($almacen_id) : "";
        
        $sql = "SELECT id, nombre, rol 
                FROM trabajadores 
                WHERE estado = 'activo' 
              
                $whereAlmacen
                ORDER BY nombre ASC";
                
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Ajustado para logística filtrando por almacén
    public function listarPersonalLogistica($almacen_id = 0) {
        // Si mandas 0, busca en todos (opcional), si no, filtra por sucursal
        $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = " . intval($almacen_id) : "";
        
        $sql = "SELECT id, nombre, rol 
                FROM trabajadores 
                WHERE estado = 'activo' 
                AND rol IN ('chofer', 'cargador') 
                $whereAlmacen
                ORDER BY nombre ASC";
                
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
public function nombreTrabajador($id)
{
    $sql = "SELECT nombre FROM trabajadores WHERE id = ?";
    $stmt = $this->db->prepare($sql);

    if (!$stmt) return null;

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    return $row['nombre'] ?? null;
}

public function eliminarDocumento( $id_documento) {

    $sql = "UPDATE documentos_trabajadores
            SET activo = 0
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("i", $id_documento);

    return $stmt->execute();
}
    // NUEVO: Listar vehículos por almacén específico
}