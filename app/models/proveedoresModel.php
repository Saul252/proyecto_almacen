<?php
class ProveedoresModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }
public function listarTodosProveedores($almacen_id = 0) {

    $sql = "SELECT * FROM proveedores";

    if ($almacen_id != 0) {
        $sql .= " Where almacen_id = ?";
    }

    $sql .= " ORDER BY nombre_comercial ASC";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        die("Error en prepare: " . $this->db->error);
    }

    if ($almacen_id != 0) {
        $stmt->bind_param("i", $almacen_id);
    }

    $stmt->execute();

    // 🔥 SIN get_result (100% compatible)
    $result = [];
    $meta = $stmt->result_metadata();

    if ($meta) {

        $fields = [];
        $row = [];

        while ($field = $meta->fetch_field()) {
            $fields[] = &$row[$field->name];
        }

        call_user_func_array([$stmt, 'bind_result'], $fields);

        while ($stmt->fetch()) {
            $temp = [];
            foreach ($row as $key => $val) {
                $temp[$key] = $val;
            }
            $result[] = $temp;
        }
    }

    $stmt->close();

    return $result;
}
    public function listarTodos() {
        $sql = "SELECT * FROM proveedores ORDER BY activo DESC, nombre_comercial ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function listarTodosAlmacen($almacen_id = null) {
    if (!empty($almacen_id)) {
        // Si se recibe un almacen_id, se filtra con consulta preparada
        $sql = "SELECT * FROM proveedores WHERE almacen_id = ? ORDER BY activo DESC, nombre_comercial ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        // Si no se especifica almacén, retorna todos
        $sql = "SELECT * FROM proveedores ORDER BY activo DESC, nombre_comercial ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
public function listarTodosProveedorsYDeuda($almacen_id = 0) {

    if ($almacen_id == 0) {

        $sql = "SELECT 
                    p.*,
                    cpp.id AS deuda_id,
                    cpp.id_referencia_origen,
                    cpp.monto_total,
                    cpp.monto_pagado,
                    cpp.estado,
                    cpp.fecha_registro,
                    (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) AS pendiente
                FROM proveedores p
                LEFT JOIN cuentas_por_pagar cpp 
                    ON cpp.id_proveedor = p.id
                    AND cpp.estado != 'cancelado'
                WHERE p.activo = 1
                ORDER BY p.activo DESC, p.nombre_comercial ASC";

        $stmt = $this->db->prepare($sql);

    } else {

        $sql = "SELECT 
                    p.*,
                    cpp.id AS deuda_id,
                    cpp.id_referencia_origen,
                    cpp.monto_total,
                    cpp.monto_pagado,
                    cpp.estado,
                    cpp.fecha_registro,
                    (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) AS pendiente
                FROM proveedores p
                LEFT JOIN cuentas_por_pagar cpp 
                    ON cpp.id_proveedor = p.id
                    AND cpp.estado != 'cancelado'
                WHERE p.activo = 1 AND p.almacen_id = ?
                ORDER BY p.activo DESC, p.nombre_comercial ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // 🔥 AGRUPAR EN PHP
    $proveedores = [];

    foreach ($rows as $row) {

        $pid = $row['id'];

        if (!isset($proveedores[$pid])) {
            $proveedores[$pid] = $row;
            $proveedores[$pid]['total_deuda'] = 0;
            $proveedores[$pid]['detalle_deudas'] = [];
        }

        if (!empty($row['deuda_id']) && $row['estado'] != 'pagado') {

            $pendiente = max($row['pendiente'], 0);

            if ($pendiente > 0) {
                $proveedores[$pid]['total_deuda'] += $pendiente;

                $proveedores[$pid]['detalle_deudas'][] = [
                    'compra_id'   => $row['id_referencia_origen'],
                    'monto_total' => $row['monto_total'],
                    'monto_pagado'=> $row['monto_pagado'],
                    'pendiente'   => $pendiente,
                    'estado'      => $row['estado'],
                    'fecha'       => $row['fecha_registro']
                ];
            }
        }
    }

    return array_values($proveedores);
}
public function ProveedorYDeuda($id) {

    $sql = "SELECT 
            cpp.id_referencia_origen AS compra_id,
            (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) AS pendiente

        FROM cuentas_por_pagar cpp

        WHERE cpp.id_proveedor = ?
        AND cpp.estado NOT IN ('pagado', 'cancelado')
        AND (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) > 0

        ORDER BY cpp.fecha_registro ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function ProveedorYDeudaSuma($id) {

    $sql = "SELECT 
            cpp.id_referencia_origen AS compra_id,

            SUM(
                cpp.monto_total - IFNULL(cpp.monto_pagado,0)
            ) AS pendiente

        FROM cuentas_por_pagar cpp

        WHERE cpp.id_proveedor = ?
        AND cpp.estado NOT IN ('pagado', 'cancelado')
        AND (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) > 0

        GROUP BY cpp.id_proveedor
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function validarPosibleDuplicado($datos)
{
     $sql = "SELECT
                id,
                nombre_comercial,
                rfc,
                contacto,
                telefono,
                telefono2
            FROM proveedores
            WHERE activo = 1";

    $result = $this->db->query($sql);

    while ($row = $result->fetch_assoc()) {

        $coincidencias = 0;

        if (
            !empty($datos['rfc']) &&
            strtoupper(trim($datos['rfc'])) === strtoupper(trim($row['rfc']))
        ) {
            $coincidencias++;
        }

        if (
            !empty($datos['nombre_comercial']) &&
            strtoupper(trim($datos['nombre_comercial'])) === strtoupper(trim($row['nombre_comercial']))
        ) {
            $coincidencias++;
        }

        if (
            !empty($datos['contacto']) &&
            strtoupper(trim($datos['contacto'])) === strtoupper(trim($row['contacto']))
        ) {
            $coincidencias++;
        }

        if (
            !empty($datos['telefono']) &&
            (string)$datos['telefono'] === (string)$row['telefono']
        ) {
            $coincidencias++;
        }

        if (
            !empty($datos['telefono2']) &&
            (
                (string)$datos['telefono2'] === (string)$row['telefono'] ||
                (string)$datos['telefono2'] === (string)$row['telefono2']
            )
        ) {
            $coincidencias++;
        }

        if ($coincidencias >= 2) {
            return [
                'success' => false,
                'message' => 'Posible proveedor duplicado detectado.'
            ];
        }
    }

    return [
        'success' => true
    ];
}
public function obtenerProveedorPorNombre($nombre) {
    $sql = "
        SELECT 
            p.id
        FROM proveedores p
        WHERE p.nombre_comercial LIKE ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);

    // 🔥 Importante: agregamos los % aquí
    $busqueda = "%" . $nombre . "%";

    $stmt->bind_param("s", $busqueda);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc(); // devuelve ['id' => ...] o null
}
public function guardar($datos) {

    // 🔹 Sanitizar enteros
    $telefono  = isset($datos['telefono'])  && $datos['telefono']  !== '' ? (int)$datos['telefono']  : 0;
    $telefono2 = isset($datos['telefono2']) && $datos['telefono2'] !== '' ? (int)$datos['telefono2'] : 0;
    $extencion = isset($datos['extencion']) && $datos['extencion'] !== '' ? (int)$datos['extencion'] : 0;

    // 🔹 SQL actualizado
    $sql = "INSERT INTO proveedores 
    (
        nombre_comercial,
        razon_social,
        rfc,
        contacto,
        correo,
        telefono,
        telefono2,
        extencion,
        direccion,
        colonia,
        ciudad,
        numeroExt,
        numeroInt,
        activo,
        creado_at,
        almacen_id
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            "success" => false,
            "message" => "Error en prepare: " . $this->db->error
        ];
    }

    // 🔹 Tipos correctos
    $stmt->bind_param(
        "sssssiiisssssi",
        $datos['nombre_comercial'], // s
        $datos['razon_social'],     // s
        $datos['rfc'],    
        $datos['contacto'],          // s
        $datos['correo'],           // s
        $telefono,                  // i
        $telefono2,                 // i
        $extencion,                 // i
        $datos['direccion'],        // s
        $datos['colonia'],          // s
        $datos['ciudad'],           // s
        $datos['numeroExt'],        // s
        $datos['numeroInt'],        // s
        $datos['almacen_id']        // i
    );

    // 🔹 Ejecutar
    if ($stmt->execute()) {
        return [
            "success" => true,
            "id" => $stmt->insert_id
        ];
    } else {
        return [
            "success" => false,
            "message" => "Error al insertar: " . $stmt->error
        ];
    }
}
public function actualizar($id, $datos) {

    $sql = "UPDATE proveedores 
            SET nombre_comercial = ?, 
                razon_social = ?, 
                contacto =?,
                rfc = ?, 
                correo = ?, 
                telefono = ?, 
                telefono2 = ?,
                extencion = ?,
                direccion = ?, 
                colonia = ?,
                ciudad = ?,
                numeroExt = ?,
                numeroInt = ?,
                almacen_id = ?,
                activo = ?
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param(
        "sssssssssssssiii",
        $datos['nombre_comercial'],
        $datos['razon_social'],
        $datos['rfc'],
        $datos['correo'],
        $datos['correo'],
        $datos['telefono'],
        $datos['telefono2'],
        $datos['extencion'],
        $datos['direccion'],
        $datos['colonia'],
        $datos['ciudad'],
        $datos['numeroExt'],
        $datos['numeroInt'],
        $datos['almacen_id'],
        $datos['activo'],
        $id
    );

    return $stmt->execute();
}
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE proveedores SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute();
    }
    public function eliminarProveedor($id) {
    $stmt = $this->db->prepare("
        UPDATE proveedores 
        SET activo = IF(activo = 1, 0, 1) 
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
    public function getResumenProveedores() {
    // Contamos solo los que están activos para que el número sea real
    $sql = "SELECT COUNT(*) as total FROM proveedores WHERE activo = 1";
    $query = $this->db->query($sql);
    $res = ($query) ? $query->fetch_assoc() : ['total' => 0];
    
    return [
        "total" => intval($res['total'] ?? 0),
        "etiqueta" => "Global"
    ];
}
}