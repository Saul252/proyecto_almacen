<?php
class comprobantesPagoModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function listarDepositos($almacen_id)
    {
        $almacen_id = 0;//borra despues
        if ($almacen_id == 0) {
            // ADMIN: Trae todos, pero agrupados por RFC para no ver 4 veces "Público General"
            // O simplemente todos si quieres ver el detalle de a qué almacén pertenecen.
            $sql = "SELECT cp.*, c.nombre_comercial,u.nombre as usuario,a.nombre as almacen
 FROM comprobantes_de_pago cp
        join clientes c on cp.id_cliente =c.id
        join usuarios u on u.id=cp.usuario_recibe
        join almacenes a on a.id =cp.almacen_id
        
        
    ";
            return $this->db->query($sql);
        }



        // VENDEDOR: Filtro ESTRICTO.
        // Solo trae los clientes cuyo almacen_id coincida EXACTAMENTE con el del usuario.
        $sql = "SELECT cp.*, c.nombre_comercial,u.nombre as usuario,a.nombre as almacen
 FROM comprobantes_de_pago cp
        join clientes c on cp.id_cliente =c.id
        join usuarios u on u.id=cp.usuario_recibe
        join almacenes a on a.id =cp.almacen_id
        
            where almacen_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function listarPorFechas(
        $es_admin = 0,
        $almacen_id = null,
        $fecha_inicio = null,
        $fecha_fin = null,
        $estado = null,
        $buscador = null,
        $vendedor = null,
    ) {
        // 1. Consulta base adaptada a comprobantes_de_pago y sus relaciones correspondientes
        $sql = "SELECT 
                cp.*, 
                c.nombre_comercial, 
                u.nombre AS usuario, 
                a.nombre AS almacen
            FROM comprobantes_de_pago cp
            JOIN clientes c ON cp.id_cliente = c.id
            JOIN usuarios u ON u.id = cp.usuario_recibe
            JOIN almacenes a ON a.id = cp.almacen_id
            WHERE 1=1"; // Permite concatenar filtros dinámicamente con "AND"

        $params = [];
        $types = "";

        // 2. Control de seguridad por Rol: Si no es admin, ignoramos lo que venga en el filtro 
        // y lo obligamos a ver únicamente el almacén que tiene asignado.
        if ($es_admin == 0) {
            $sql .= " AND cp.almacen_id = ?";
            $types .= "i";
            $params[] = $almacen_id;
        } else {
            // Si es admin, puede elegir filtrar por un almacén específico o ver todos (si viene vacío)
            if (isset($almacen_id) && $almacen_id !== '' && $almacen_id !== 0) {
                $sql .= " AND cp.almacen_id = ?";
                $types .= "i";
                $params[] = $almacen_id;
            }
        }

        // 3. Filtro de Fecha Inicial (Usa el campo cp.fecha)
        if (!empty($fecha_inicio)) {
            $sql .= " AND DATE(cp.fecha) >= ?";
            $types .= "s";
            $params[] = $fecha_inicio;
        }
        // if (!empty($vendedor)) {
        //     $sql .= " AND (vendedor.id) = ?";
        //     $types .= "i";
        //     $params[] = $vendedor;
        // }

        // 4. Filtro de Fecha Final
        if (!empty($fecha_fin)) {
            $sql .= " AND DATE(cp.fecha) <= ?";
            $types .= "s";
            $params[] = $fecha_fin;
        }

        // 5. Filtro por Estado (cp.estado)
        if (!empty($estado)) {
            $sql .= " AND cp.estado = ?";
            $types .= "s";
            $params[] = $estado;
        }

        // 6. Buscador General inteligente por Nombre Comercial del Cliente o Folio (cp.id)
        if (!empty($buscador)) {
            $sql .= " AND (
                    c.nombre_comercial LIKE ?
                    OR cp.id LIKE ? or cp.referencia LIKE ?
                  )";
            $types .= "sss";

            $like = "%{$buscador}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        // 7. Ordenamiento final descendente por fecha de emisión del pago
        $sql .= " ORDER BY cp.fecha DESC";

        // Preparación segura de la sentencia contra inyecciones SQL
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        // Enlace dinámico de variables si existen parámetros activos
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Retorna el array asociativo mapeado de MySQLi listo para procesarse en el JSON de tu AJAX
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function cancelarOrden($id)
    {
        try {
            // Verificar estado actual
            $stmtCheck = $this->db->prepare("
            SELECT estado 
            FROM comprobantes_de_pago
            WHERE id = ?
        ");

            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();

            $res = $stmtCheck->get_result()->fetch_assoc();

            // Solo permitir cancelar si está pendiente

            // Cambiar estado a cancelado
            $stmt = $this->db->prepare("UPDATE comprobantes_de_pago 
            SET estado = 'cancelado' 
            WHERE id = ?
        ");

            $stmt->bind_param("i", $id);

            return $stmt->execute();

        } catch (Exception $e) {
            return false;
        }
    }// 1. Quitamos $recibido de los parámetros de la función
    public function actualizar($id)
    {
        try {
            // 1. Verificar existencia o estado actual
            $stmtCheck = $this->db->prepare("
            SELECT recibido 
            FROM comprobantes_de_pago
            WHERE id = ?
        ");

            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            $res = $stmtCheck->get_result()->fetch_assoc();
            $stmtCheck->close();

            if (!$res) {
                return false; // No existe el registro
            }

            // 2. CORRECCIÓN: La consulta SQL ahora invierte el valor internamente
            // Si 'recibido' es '1', lo cambia a '0'. Si es cualquier otra cosa, lo cambia a '1'.
            $stmt = $this->db->prepare("
            UPDATE comprobantes_de_pago 
            SET recibido = IF(recibido = '1', '0', '1') 
            WHERE id = ?
        ");

            // Ahora solo pasas el $id ya que la base de datos se encarga de 'recibido'
            $stmt->bind_param("i", $id);
            $ejecutado = $stmt->execute();
            $stmt->close();

            return $ejecutado;

        } catch (Exception $e) {
            return false;
        }
    }
    public function actualizarAplicado($id, $aplicado)
    {
        try {
            // 1. Obtener 'aplicado' y 'monto' en una sola consulta limpia
            $stmtCheck = $this->db->prepare("
            SELECT aplicado, monto
            FROM comprobantes_de_pago
            WHERE id = ?
        ");

            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            $resultado = $stmtCheck->get_result()->fetch_assoc();
            $stmtCheck->close();

            // Si no existe el comprobante en la BD
            if (!$resultado) {
                return false;
            }

            $caplicadoActual = floatval($resultado['aplicado']);

            $montoTotal = floatval($resultado['monto']);

            // Validación: Si lo acumulado/aplicado ya es igual o mayor al monto total
            if ($caplicadoActual >= $montoTotal) {
                return false;
            }

            // 2. Actualizar el campo 'aplicado'
            $stmt = $this->db->prepare("
            UPDATE comprobantes_de_pago 
            SET aplicado = ? 
            WHERE id = ?
        ");

            // "d" = Double/Decimal/Float ($aplicado), "i" = Integer ($id)
            $nuevoAplicado = floatval($aplicado + $caplicadoActual);
            $idComprobante = intval($id);

            $stmt->bind_param("di", $nuevoAplicado, $idComprobante);
            $ejecutado = $stmt->execute();
            $stmt->close();

            return $ejecutado;

        } catch (Exception $e) {
            error_log("Error en actualizarAplicado: " . $e->getMessage());
            return false;
        }
    }
    public function obtenerDetalle($id)
    {
        $sql = "SELECT cp.*, c.nombre_comercial,c.correo, u.nombre as usuario, a.nombre as nombre_almacen
            FROM comprobantes_de_pago cp
            JOIN clientes c ON cp.id_cliente = c.id
            JOIN usuarios u ON u.id = cp.usuario_recibe
            JOIN almacenes a ON a.id = cp.almacen_id
            WHERE cp.id = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            // Si la consulta SQL tiene un error de sintaxis o columnas, esto lo atrapará el try/catch del controlador
            throw new Exception("Error en la preparación del SQL: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        // CORRECCIÓN: Volvemos a extraer los datos como un array asociativo limpio
        $data = $result->fetch_assoc();

        $stmt->close();

        // Si no se encuentra el ID, $data será null, lo cual está bien porque el controlador lo maneja
        return $data;
    }
    public function listarTodosCF($almacen_id)
    {
        if ($almacen_id == 0) {
            // ADMIN: Trae todos, pero agrupados por RFC para no ver 4 veces "Público General"
            // O simplemente todos si quieres ver el detalle de a qué almacén pertenecen.
            $sql = "SELECT * FROM clientes 
                WHERE activo = 1 
                ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";
            return $this->db->query($sql);
        }

        // VENDEDOR: Filtro ESTRICTO.
        // Solo trae los clientes cuyo almacen_id coincida EXACTAMENTE con el del usuario.
        $sql = "SELECT * FROM clientes 
        WHERE activo = 1
        AND (
            rfc != 'XAXX010101000'
            OR (rfc = 'XAXX010101000' AND almacen_id = ?)
        )
        ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function agregarDeposito($id_cliente, $monto, $usuario, $fecha, $referencia, $almacen_id, $metodo, $numero_ventas)
    {
        // Consulta SQL para obtener el folio máximo del almacén
        $sqlMax = "SELECT COALESCE(MAX(folio), 0) AS max_folio 
           FROM comprobantes_de_pago 
           WHERE almacen_id = ?";

        $stmtMax = $this->db->prepare($sqlMax);
        $stmtMax->bind_param("i", $almacen_id);
        $stmtMax->execute();
        $result = $stmtMax->get_result();
        $row = $result->fetch_assoc();

        // Siguiente folio autoincrementado por almacén
        $siguiente_folio = $row['max_folio'] + 1;
        $sqlA = "INSERT INTO comprobantes_de_pago (id_cliente, monto, usuario_recibe, fecha, referencia,almacen_id,metodo_pago,numero_ventas,folio) 
             VALUES (?, ?, ?,?, ?, ?,?,?,?)";

        // Asegúrate de que tu clase db use "prepare" o mapee "p" correctamente a un statement de mysqli
        $stmtA = $this->db->prepare($sqlA);

        if (!$stmtA) {
            return false;
        }

        // CORREGIDO: "i" (cliente), "d" (monto float), "i" o "s" (usuario), "s" (fecha), "s" (referencia)
        // Cambié el tipo de monto a 'd' para soportar los decimales del floatval
        $stmtA->bind_param("idsssissi", $id_cliente, $monto, $usuario, $fecha, $referencia, $almacen_id, $metodo, $numero_ventas, $siguiente_folio);

        if ($stmtA->execute()) {
            // Obtenemos el ID generado
            $id_comprobante = $this->db->insert_id;
            $stmtA->close();
            return $id_comprobante; // Retorna el número (ej: 12), evaluado como > 0 en el controlador
        } else {
            return false;
        }
    }
}
