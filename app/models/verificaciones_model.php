<?php
class VerificacionesModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar todos (Para Admin Global)
    public function listar() {
        $sql = "SELECT tvh.*,
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
    FROM documentos_vehiculos dvh
    WHERE dvh.vehiculo_id = tvh.id and dvh.activo=1
) AS documentos_url
FROM transporte_vehiculos tvh
                WHERE activo = 1 
                ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }   
    
    
   public function listarProximasVerificaciones($almacen_id = 0) {
    $sql = "SELECT
                v_reg.id,
                v_reg.id_vehiculo,
                v.nombre AS vehiculo,
                v.placas,
                v_reg.fecha,
                v_reg.proxima_verificacion,
                DATEDIFF(v_reg.proxima_verificacion, CURDATE()) AS dias_restantes,
                CASE
                    WHEN DATEDIFF(v_reg.proxima_verificacion, CURDATE()) < 0 THEN
                        CONCAT('Vencido hace ', ABS(DATEDIFF(v_reg.proxima_verificacion, CURDATE())), ' días')
                    WHEN DATEDIFF(v_reg.proxima_verificacion, CURDATE()) = 0 THEN
                        'Vence hoy'
                    WHEN DATEDIFF(v_reg.proxima_verificacion, CURDATE()) = 1 THEN
                        'Vence mañana'
                    ELSE
                        CONCAT('Faltan ', DATEDIFF(v_reg.proxima_verificacion, CURDATE()), ' días')
                END AS estado
            FROM verificaciones v_reg
            INNER JOIN (
                SELECT id_vehiculo, MAX(id) AS ultimo_id
                FROM verificaciones
                GROUP BY id_vehiculo
            ) ult ON ult.ultimo_id = v_reg.id
            INNER JOIN transporte_vehiculos v ON v.id = v_reg.id_vehiculo
            WHERE v_reg.proxima_verificacion <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";

    $params = [];
    $types = "";

    // Si $almacen_id es mayor a 0, aplica el filtro; si es 0 trae todo
    if (!empty($almacen_id) && $almacen_id > 0) {
        $sql .= " AND v.almacen_id = ?";
        $params[] = (int)$almacen_id;
        $types .= "i";
    }

    $sql .= " ORDER BY v_reg.proxima_verificacion ASC";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [];
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
    public function subirDocumentoCompra($id, $nombre_evidencia, $documento_url)
{
    $sql = "INSERT INTO documentos_vehiculos
            (vehiculo_id, nombre, direccion)
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
public function eliminarDocumento( $id_documento) {

    $sql = "UPDATE documentos_vehiculos
            SET activo = 0
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("i", $id_documento);

    return $stmt->execute();
}
    // NUEVO: Listar vehículos por almacén específico
    
  public function guardar(
    $vehiculo_id, 
    $fecha,
    $proxima_verificacion
) {
    // Se elimina la coma sobrante en 'proxima_verificacion' y se corrige el posible typo
    $sql = "INSERT INTO verificaciones (
                id_vehiculo, 
                fecha, 
                proxima_verificacion
            ) VALUES (?, ?, ?)";

    $stmt = $this->db->prepare($sql);
    
    if (!$stmt) {
        return false; // Evita fallos si la preparación de la consulta falla
    }

    $stmt->bind_param("iss", $vehiculo_id, $fecha, $proxima_verificacion);
    return $stmt->execute();
}
  
public function eliminar($id) {
    $sql = "DELETE FROM verificaciones WHERE id = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    
    $stmt->close(); // Liberamos la sentencia preparada

    return $resultado;
}
   

     public function obtenerVerificacionesFiltradas($filtros, $rol_id=1, $almacen_sesion=0) {
        $where = " WHERE 1=1";
        
        // Seguridad por Almacén
        $rol_id=1;
        if ($rol_id != 1) { 
            $where .= " AND v.almacen_id = $almacen_sesion "; 
        } elseif (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }
       // Buscador (Folio o Cliente)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (v.nombre LIKE '%$s%' OR v.id LIKE '%$s%'OR v.placas LIKE '%$s%') ";
        }
        // Estatus Entrega      
         
          // Estatus Entrega
        if (!empty($filtros['vehiculo'])) {
            $st = $this->db->real_escape_string($filtros['vehiculo']);
            $where .= " AND v.vehiculo_id = '$st' ";
        }

        // Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros);
        }

        // Filtro por Estado de Pago (Saldo)
        

        $sql = "SELECT m.*,a.nombre as almacen ,v.nombre as vehiculo, v.id as id_v,v.placas as placas,v.modelo_año as modelo
                 FROM verificaciones m
                join transporte_vehiculos v on v.id=m.id_vehiculo
                JOIN almacenes a ON v.almacen_id = a.id 
                $where ORDER BY m.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } 
  
   
       private function construirFiltroFecha($f) {
        switch($f['rango']) {
            case 'hoy': return " AND DATE(m.fecha) = CURDATE() ";
            case 'ayer': return " AND DATE(m.fecha) = SUBDATE(CURDATE(),1) ";
            case 'semana': return " AND YEARWEEK(m.fecha, 1) = YEARWEEK(CURDATE(), 1) ";
            case 'mes': return " AND MONTH(m.fecha) = MONTH(CURDATE()) AND YEAR(m.fecha) = YEAR(CURDATE()) ";
            case 'personalizado':
                $ini = $this->db->real_escape_string($f['inicio']);
                $fin = $this->db->real_escape_string($f['fin']);
                return " AND DATE(m.fecha) BETWEEN '$ini' AND '$fin' ";
            default: return "";
        }
    }
   
}