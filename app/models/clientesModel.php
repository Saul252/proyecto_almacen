<?php
class ClientesModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function listarTodos($almacen_id)
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
            AND almacen_id = ?
            ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
        return $stmt->get_result();
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
    public function listarTodosViewClientes($almacen_id)
    {
        if ($almacen_id == 0) {
            // ADMIN: Trae todos, pero agrupados por RFC para no ver 4 veces "Público General"
            // O simplemente todos si quieres ver el detalle de a qué almacén pertenecen.
            $sql = "SELECT * FROM clientes 
              
                ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";
            return $this->db->query($sql);
        }

        // VENDEDOR: Filtro ESTRICTO.
        // Solo trae los clientes cuyo almacen_id coincida EXACTAMENTE con el del usuario.
        $sql = "SELECT * FROM clientes 
           
            WHERE almacen_id = ?
            ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $almacen_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function guardar($datos)
    {
        // 1. Lógica de asignación de almacén
        $almacen_id_sesion = $_SESSION['almacen_id'] ?? 0;

        if ($almacen_id_sesion == 0) {
            $almacen_id_insertar = !empty($datos['almacen_id'])
                ? intval($datos['almacen_id'])
                : null;
        } else {
            $almacen_id_insertar = $almacen_id_sesion;
        }

        // 2. Limpieza de datos
        $nombre_comercial = trim($datos['nombre_comercial'] ?? '');
        $contacto = trim($datos['contacto'] ?? '');
        $razon_social = trim($datos['razon_social'] ?? '');
        $rfc = trim($datos['rfc'] ?? '');
        $regimen_fiscal = !empty($datos['regimen_fiscal']) ? $datos['regimen_fiscal'] : null;
        $codigo_postal = !empty($datos['codigo_postal']) ? $datos['codigo_postal'] : null;
        $correo = !empty($datos['correo']) ? $datos['correo'] : null;
        $telefono = trim($datos['telefono'] ?? '');
        $fecha_nacimiento = !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : date('Y-m-d');
        $sexo = !empty($datos['sexo']) ? $datos['sexo'] : 'HUMANO';
        $direccion = !empty($datos['direccion']) ? $datos['direccion'] : null;
        $uso_cfdi = !empty($datos['uso_cfdi']) ? $datos['uso_cfdi'] : 'G03';

        // 3. Validar RFC duplicado (Única validación activa)
        $checkSql = "SELECT id
                   FROM clientes
                   WHERE rfc = ?
                   AND almacen_id = ?
                   AND activo = 1";

        $stmtCheck = $this->db->prepare($checkSql);

        if (!$stmtCheck) {
            throw new Exception("Error al preparar validación RFC: " . $this->db->error);
        }

        $stmtCheck->bind_param(
            "si",
            $rfc,
            $almacen_id_insertar
        );

        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'El RFC ya está registrado en la sucursal seleccionada.'
            ];
        }

        // 4. Generar token y activo
        $api_token = bin2hex(random_bytes(16));
        $activo = 1;

        // 5. Insertar cliente directamente
        $sql = "INSERT INTO clientes (
                nombre_comercial,
                contacto,
                razon_social,
                rfc,
                regimen_fiscal,
                codigo_postal,
                correo,
                telefono,
                fecha_nacimiento,
                sexo,
                direccion,
                uso_cfdi,
                almacen_id,
                api_token,
                activo
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar INSERT: " . $this->db->error);
        }

        $stmt->bind_param(
            "ssssssssssssisi",
            $nombre_comercial,
            $contacto,
            $razon_social,
            $rfc,
            $regimen_fiscal,
            $codigo_postal,
            $correo,
            $telefono,
            $fecha_nacimiento,
            $sexo,
            $direccion,
            $uso_cfdi,
            $almacen_id_insertar,
            $api_token,
            $activo
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar cliente: " . $stmt->error);
        }

        return [
            'success' => true,
            'id' => $this->db->insert_id,
            'api_token' => $api_token,
            'message' => 'Cliente guardado correctamente'
        ];
    }
    public function guardarconComprobacionRFC($datos)
    {
        // 1. Lógica de asignación de almacén
        $almacen_id_sesion = $_SESSION['almacen_id'] ?? 0;

        if ($almacen_id_sesion == 0) {
            $almacen_id_insertar = !empty($datos['almacen_id'])
                ? intval($datos['almacen_id'])
                : null;
        } else {
            $almacen_id_insertar = $almacen_id_sesion;
        }

        // 2. Validar RFC duplicado
        $checkSql = "SELECT id
                 FROM clientes
                 WHERE rfc = ?
                 AND almacen_id = ?
                 AND activo = 1";

        $stmtCheck = $this->db->prepare($checkSql);

        if (!$stmtCheck) {
            throw new Exception("Error al preparar validación RFC: " . $this->db->error);
        }

        $stmtCheck->bind_param(
            "si",
            $datos['rfc'],
            $almacen_id_insertar
        );

        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'El RFC ya está registrado en la sucursal seleccionada.'
            ];
        }

        // 3. Generar token
        $api_token = bin2hex(random_bytes(16));
        $activo = 1;

        // 4. Limpieza de datos
        $nombre_comercial = trim($datos['nombre_comercial'] ?? '');
        $contacto = trim($datos['contacto'] ?? '');
        $razon_social = trim($datos['razon_social'] ?? '');
        $rfc = trim($datos['rfc'] ?? '');
        $regimen_fiscal = !empty($datos['regimen_fiscal']) ? $datos['regimen_fiscal'] : null;
        $codigo_postal = !empty($datos['codigo_postal']) ? $datos['codigo_postal'] : null;
        $correo = !empty($datos['correo']) ? $datos['correo'] : null;
        $telefono = trim($datos['telefono'] ?? '');
        $direccion = !empty($datos['direccion']) ? $datos['direccion'] : null;
        $uso_cfdi = !empty($datos['uso_cfdi']) ? $datos['uso_cfdi'] : 'G03';

        // Evitar LIKE '%%'
        $likeNombre = $nombre_comercial != '' ? "%{$nombre_comercial}%" : "__SIN_COINCIDENCIA__";
        $likeContacto = $contacto != '' ? "%{$contacto}%" : "__SIN_COINCIDENCIA__";

        // 5. Buscar posibles duplicados
        $sqlCoincidencias = "
    SELECT
        c.nombre_comercial,
        CASE
            WHEN c.telefono = ?
                 AND c.nombre_comercial LIKE ?
                THEN 'Coincide teléfono y nombre comercial'

            WHEN c.telefono = ?
                THEN 'Coincide teléfono'

            WHEN c.nombre_comercial LIKE ?
                THEN 'Coincide nombre comercial'

            WHEN c.contacto LIKE ?
                THEN 'Coincide contacto'

            ELSE 'Sin coincidencias'
        END AS motivo
    FROM clientes c
    WHERE
        c.activo = 1
        AND (
            c.telefono = ?
            OR c.nombre_comercial LIKE ?
            OR c.contacto LIKE ?
        )
    LIMIT 1";

        $coincidencias = $this->db->prepare($sqlCoincidencias);

        if (!$coincidencias) {
            throw new Exception("Error al preparar validación de coincidencias: " . $this->db->error);
        }

        $coincidencias->bind_param(
            "ssssssss",
            $telefono,
            $likeNombre,
            $telefono,
            $likeNombre,
            $likeContacto,
            $telefono,
            $likeNombre,
            $likeContacto
        );

        $coincidencias->execute();

        $resultadoCoincidencias = $coincidencias->get_result();

        if ($resultadoCoincidencias->num_rows > 0) {

            $fila = $resultadoCoincidencias->fetch_assoc();

            return [
                'success' => false,
                'message' => 'Datos existentes: ' .
                    $fila['nombre_comercial'] .
                    ' (' . $fila['motivo'] . ').'
            ];
        }

        // 6. Insertar cliente
        $sql = "INSERT INTO clientes (
                nombre_comercial,
                contacto,
                razon_social,
                rfc,
                regimen_fiscal,
                codigo_postal,
                correo,
                telefono,
                direccion,
                uso_cfdi,
                almacen_id,
                api_token,
                activo
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar INSERT: " . $this->db->error);
        }

        $stmt->bind_param(
            "ssssssssssisi",
            $nombre_comercial,
            $contacto,
            $razon_social,
            $rfc,
            $regimen_fiscal,
            $codigo_postal,
            $correo,
            $telefono,
            $direccion,
            $uso_cfdi,
            $almacen_id_insertar,
            $api_token,
            $activo
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar cliente: " . $stmt->error);
        }

        return [
            'success' => true,
            'id' => $this->db->insert_id,
            'api_token' => $api_token,
            'message' => 'Cliente guardado correctamente'
        ];
    }
    public function actualizar($id, $datos)
    {

        $almacen_id_sesion = $_SESSION['almacen_id'] ?? 0;

        // Validar RFC duplicado
        $checkSql = "SELECT id
                   FROM clientes
                   WHERE rfc = ?
                   AND id != ?
                   AND activo = 1";

        if ($almacen_id_sesion > 0) {
            $checkSql .= " AND almacen_id = " . intval($almacen_id_sesion);
        }

        $stmtCheck = $this->db->prepare($checkSql);

        if (!$stmtCheck) {
            throw new Exception("Error al preparar validación RFC: " . $this->db->error);
        }

        $stmtCheck->bind_param(
            "si",
            $datos['rfc'],
            $id
        );

        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {
            throw new Exception("El RFC ingresado ya está registrado con otro cliente.");
        }

        // Limpieza de datos
        $contacto = !empty($datos['contacto']) ? $datos['contacto'] : null;
        $razon_social = !empty($datos['razon_social']) ? $datos['razon_social'] : null;
        $regimen_fiscal = !empty($datos['regimen_fiscal']) ? $datos['regimen_fiscal'] : null;
        $correo = !empty($datos['correo']) ? $datos['correo'] : null;
        $telefono = !empty($datos['telefono']) ? $datos['telefono'] : null;
        $fecha_nacimiento = !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : date('Y-m-d');
        $sexo = !empty($datos['sexo']) ? $datos['sexo'] : 'HUMANO';
        $direccion = !empty($datos['direccion']) ? $datos['direccion'] : null;
        $uso_cfdi = !empty($datos['uso_cfdi']) ? $datos['uso_cfdi'] : 'G03';

        // Campos base con fecha_nacimiento y sexo agregados
        $campos = [
            "nombre_comercial = ?",
            "contacto = ?",
            "razon_social = ?",
            "rfc = ?",
            "regimen_fiscal = ?",
            "codigo_postal = ?",
            "correo = ?",
            "telefono = ?",
            "fecha_nacimiento = ?",
            "sexo = ?",
            "direccion = ?",
            "uso_cfdi = ?"
        ];

        $params = [
            $datos['nombre_comercial'],
            $contacto,
            $razon_social,
            $datos['rfc'],
            $regimen_fiscal,
            $datos['codigo_postal'],
            $correo,
            $telefono,
            $fecha_nacimiento,
            $sexo,
            $direccion,
            $uso_cfdi
        ];

        $tipos = "ssssssssssss"; // Actualizado a 12 strings correspondientes

        // Solo admin puede cambiar almacén
        if ($almacen_id_sesion == 0 && isset($datos['almacen_id'])) {

            $campos[] = "almacen_id = ?";
            $params[] = intval($datos['almacen_id']);
            $tipos .= "i";
        }

        $sql = "UPDATE clientes
            SET " . implode(", ", $campos) . "
            WHERE id = ?";

        if ($almacen_id_sesion > 0) {
            $sql .= " AND almacen_id = " . intval($almacen_id_sesion);
        }

        $params[] = $id;
        $tipos .= "i";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar UPDATE: " . $this->db->error);
        }

        $stmt->bind_param($tipos, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar cliente: " . $stmt->error);
        }

        return [
            'success' => true,
            'message' => 'Cliente actualizado correctamente'
        ];
    }
    public function cambiarEstado($id, $estado, $almacen_id = 0)
    {
        // SQL con candado de seguridad por almacén
        $sql = "UPDATE clientes SET activo = ? WHERE id = ?";
        if ($almacen_id > 0) {
            $sql .= " AND almacen_id = " . intval($almacen_id);
        }

        // Aquí es donde fallaba: $this->db debe ser el objeto de conexión
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $estado, $id);

        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        }
        return false;
    }

    public function obtenerPorId($id, $almacen_id = 0)
    {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        if ($almacen_id > 0) {
            $sql .= " AND (almacen_id = " . intval($almacen_id) . " OR rfc = 'XAXX010101000')";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getResumenClientes($almacen_id)
    {
        // 1. Limpiamos el ID
        $id = intval($almacen_id);

        // 2. CONTEO GLOBAL (Esto es lo que ve el Admin)
        // Contamos todos los clientes activos sin importar a qué almacén pertenecen
        $sqlGlobal = "SELECT COUNT(*) as total FROM clientes ";
        $queryGlobal = $this->db->query($sqlGlobal);
        $totalSistema = ($queryGlobal) ? intval($queryGlobal->fetch_assoc()['total']) : 0;

        // 3. RETORNO DE LÓGICA
        if ($id === 0) {
            // --- CASO ADMINISTRADOR ---
            // Para el Admin, "mis_clientes" es igual al "total_sistema"
            return [
                "tipo" => "admin",
                "nombre" => "Control Global",
                "mis_clientes" => $totalSistema,
                "total_sistema" => $totalSistema
            ];
        } else {
            // --- CASO VENDEDOR (ID > 0) ---
            // Solo contamos los que le pertenecen a SU almacén
            $sqlLocal = "SELECT COUNT(*) as total FROM clientes WHERE almacen_id = $id ";
            $queryLocal = $this->db->query($sqlLocal);
            $totalLocal = ($queryLocal) ? intval($queryLocal->fetch_assoc()['total']) : 0;

            // Traemos el nombre del almacén para el footer del widget
            $sqlNom = "SELECT nombre FROM almacenes WHERE id = $id LIMIT 1";
            $resNom = $this->db->query($sqlNom)->fetch_assoc();

            return [
                "tipo" => "vendedor",
                "nombre" => $resNom['nombre'] ?? 'Sucursal',
                "mis_clientes" => $totalLocal,
                "total_sistema" => $totalSistema
            ];
        }
    }
    /**
     * Obtiene el resumen actual de deuda o saldo a favor de un cliente
     */
    /**
     * Obtiene todos los movimientos registrados en el LOG de saldos de un cliente
     * Ideal para el visor de estado de cuenta.
     */
    public static function obtenerHistorialLog($conexion, $id_cliente)
    {
        $sql = "SELECT 
                l.id,
                l.fecha_registro,
                l.tipo_movimiento, 
                l.monto AS monto_afectacion,
                l.monto_operacion_total,
                l.monto_pagado_momento,
                l.referencia_tipo,
                l.observaciones,
                v.folio AS folio_venta,
                u.usuario AS responsable
            FROM clientes_saldos_log l
            LEFT JOIN ventas v ON l.venta_id = v.id
            LEFT JOIN usuarios u ON l.usuario_id = u.id
            WHERE l.cliente_id = ?
            ORDER BY l.fecha_registro DESC, l.id DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        return $stmt->get_result();
    }
    /**
     * Obtiene el historial completo de movimientos de la cuenta del cliente
     */
    /**
     * Obtiene el saldo total acumulado y la fecha del último movimiento
     */
    public static function obtenerSaldoActual($conexion, $id_cliente)
    {
        $sql = "SELECT 
                c.nombre AS cliente_nombre,
                COALESCE(s.saldo_en_contra, 0) AS saldo_total,
                s.actualizado_en,
                (SELECT folio FROM ventas WHERE id = s.ultima_venta_id) AS ultimo_folio
            FROM clientes c
            LEFT JOIN clientes_saldos s ON c.id = s.cliente_id
            WHERE c.id = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }



    public static function obtenerEstatus($conexion, $id)
    {
        // Consultamos directamente la tabla maestra unida con el nombre del cliente
        $sql = "SELECT 
        c.nombre_comercial,
        s.saldo_a_favor,
        s.saldo_en_contra,
        -- Calculamos el saldo neto para que el JS siga funcionando igual
        -- Si es positivo: debe (saldo_en_contra)
        -- Si es negativo: tiene a favor (saldo_a_favor)
        ( s.saldo_a_favor) AS saldo_neto,
        
        CASE 
            WHEN s.saldo_en_contra > s.saldo_a_favor THEN 'CON DEUDA'
            WHEN s.saldo_a_favor > s.saldo_en_contra THEN 'SALDO A FAVOR'
            ELSE 'AL DIA'
        END AS estatus_financiero
    FROM clientes c
    LEFT JOIN clientes_saldos s ON c.id = s.cliente_id
    WHERE c.id = ?";

        try {
            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                error_log("Error preparando estatus simplificado: " . $conexion->error);
                return null;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                // Valores por defecto si la fila en clientes_saldos aún no existe
                $data['saldo_a_favor'] = floatval($data['saldo_a_favor'] ?? 0);
                $data['saldo_en_contra'] = floatval($data['saldo_en_contra'] ?? 0);
                $data['saldo_neto'] = floatval($data['saldo_neto'] ?? 0);

                // Para que tu Widget de iOS siga mostrando el monto sin signos raros
                $data['saldo_neto_abs'] = abs($data['saldo_neto']);
            } else {
                // Si el cliente no tiene registro en la tabla de saldos todavía
                return [
                    'nombre_comercial' => 'Cliente Nuevo',
                    'saldo_a_favor' => 0,
                    'saldo_en_contra' => 0,
                    'saldo_neto' => 0,
                    'saldo_neto_abs' => 0,
                    'estatus_financiero' => 'AL DIA'
                ];
            }

            return $data;

        } catch (Exception $e) {
            error_log("Excepción en obtenerEstatus (Tabla Maestra): " . $e->getMessage());
            return null;
        }
    }

    public function abono_saldos($cliente_id, $monto_ajuste, $venta_id, $fecha_pago)
    {
        // 1. CONSULTAR SALDO ACTUAL
        $stmtSelect = $this->db->prepare("SELECT saldo_a_favor, saldo_en_contra FROM clientes_saldos WHERE cliente_id = ?");
        $stmtSelect->bind_param("i", $cliente_id);
        $stmtSelect->execute();
        $res = $stmtSelect->get_result()->fetch_assoc();

        // Si no existe el registro, inicializamos en 0
        $saldo_f_actual = floatval($res['saldo_a_favor'] ?? 0);
        $saldo_c_actual = floatval($res['saldo_en_contra'] ?? 0);

        /**
         * 2. CALCULAR NUEVO ESTADO
         * * Recordatorio: 
         * - Si $monto_ajuste es POSITIVO (bajó el precio), es un abono/saldo a favor.
         * - Si $monto_ajuste es NEGATIVO (subió el precio), es un cargo/deuda.
         * * Neto actual = Saldo a favor - Saldo en contra
         */
        $neto_actual = $saldo_f_actual - $saldo_c_actual;
        $nuevo_neto = $neto_actual + $monto_ajuste;

        $nuevo_saldo_a_favor = 0;
        $nuevo_saldo_en_contra = 0;

        if ($nuevo_neto >= 0) {
            // El cliente tiene dinero a su favor
            $nuevo_saldo_a_favor = $nuevo_neto;
            $nuevo_saldo_en_contra = 0;
        } else {
            // El cliente debe dinero (neto negativo)
            $nuevo_saldo_a_favor = 0;
            $nuevo_saldo_en_contra = abs($nuevo_neto);
        }

        // 3. ACTUALIZAR O INSERTAR (Upsert)
        $sqlMaestra = "INSERT INTO `clientes_saldos` (
        `cliente_id`, 
        `saldo_a_favor`, 
        `saldo_en_contra`, 
        `ultima_venta_id`, 
        `ultima_actualizacion`
    ) VALUES (?, ?, ?, ?, ?) 
    ON DUPLICATE KEY UPDATE 
        `saldo_a_favor` = VALUES(`saldo_a_favor`),
        `saldo_en_contra` = VALUES(`saldo_en_contra`),
        `ultima_venta_id` = VALUES(`ultima_venta_id`),
        `ultima_actualizacion` = VALUES(`ultima_actualizacion`)";

        $stmtMaestra = $this->db->prepare($sqlMaestra);
        $stmtMaestra->bind_param(
            "iddis",
            $cliente_id,
            $nuevo_saldo_a_favor,
            $nuevo_saldo_en_contra,
            $venta_id,
            $fecha_pago
        );

        return $stmtMaestra->execute();
    }
    public function abono_saldosAFavor($cliente_id, $monto_ajuste, $venta_id, $fecha_pago)
    {
        // 1. CONSULTAR SALDO ACTUAL
        $stmtSelect = $this->db->prepare("SELECT saldo_a_favor, saldo_en_contra FROM clientes_saldos WHERE cliente_id = ?");
        $stmtSelect->bind_param("i", $cliente_id);
        $stmtSelect->execute();
        $res = $stmtSelect->get_result()->fetch_assoc();

        // Si no existe el registro, inicializamos en 0
        $saldo_f_actual = floatval($res['saldo_a_favor'] ?? 0);
        $saldo_c_actual = floatval($res['saldo_en_contra'] ?? 0);

        /**
         * 2. CALCULAR NUEVO ESTADO
         * * Recordatorio: 
         * - Si $monto_ajuste es POSITIVO (bajó el precio), es un abono/saldo a favor.
         * - Si $monto_ajuste es NEGATIVO (subió el precio), es un cargo/deuda.
         * * Neto actual = Saldo a favor - Saldo en contra
         */
        $neto_actual = $saldo_f_actual - $saldo_c_actual;
        $nuevo_neto = $neto_actual + $monto_ajuste;

        $nuevo_saldo_a_favor = 0;
        $nuevo_saldo_en_contra = 0;

        if ($nuevo_neto >= 0) {
            // El cliente tiene dinero a su favor
            $nuevo_saldo_a_favor = $nuevo_neto;
            $nuevo_saldo_en_contra = 0;
        } else {
            // El cliente debe dinero (neto negativo)
            $nuevo_saldo_a_favor = 0;
            $nuevo_saldo_en_contra = abs($nuevo_neto);
        }

        // 3. ACTUALIZAR O INSERTAR (Upsert)
        $sqlMaestra = "INSERT INTO `clientes_saldos` (
        `cliente_id`, 
        `saldo_a_favor`, 
        `saldo_en_contra`, 
        `ultima_venta_id`, 
        `ultima_actualizacion`
    ) VALUES (?, ?, ?, ?, ?) 
    ON DUPLICATE KEY UPDATE 
        `saldo_a_favor` = VALUES(`saldo_a_favor`),
        `saldo_en_contra` = VALUES(`saldo_en_contra`),
        `ultima_venta_id` = VALUES(`ultima_venta_id`),
        `ultima_actualizacion` = VALUES(`ultima_actualizacion`)";

        $stmtMaestra = $this->db->prepare($sqlMaestra);
        $stmtMaestra->bind_param(
            "iddis",
            $cliente_id,
            $nuevo_saldo_a_favor,
            $nuevo_saldo_en_contra,
            $venta_id,
            $fecha_pago
        );

        return $stmtMaestra->execute();
    }
    public function abono_saldos_log($cliente_id, $venta_id, $monto, $usuario_id, $metodo_pago, $fecha_pago)
    {
        // 1. Definir la consulta SQL (12 columnas, 8 marcadores '?')
        $sqlLog = "INSERT INTO `clientes_saldos_log` (
        `id`, 
        `cliente_id`, 
        `venta_id`, 
        `tipo_movimiento`, 
        `monto`, 
        `monto_operacion_total`, 
        `monto_pagado_momento`, 
        `referencia_tipo`, 
        `referencia_id`, 
        `observaciones`, 
        `fecha_registro`, 
        `usuario_id`
    ) VALUES (
        NULL, 
        ?,        -- 1. cliente_id (i)
        ?,        -- 2. venta_id (i)
        'abono',  -- (Fijo)
        ?,        -- 3. monto (d)
        '0.00',   -- (Fijo)
        ?,        -- 4. monto_pagado_momento (d)
        'PAGO_MANUAL', -- (Fijo)
        ?,        -- 5. referencia_id (i)
        ?,        -- 6. observaciones (s)
        ?,        -- 7. fecha_registro (s)
        ?         -- 8. usuario_id (i)
    )";

        $stmtLog = $this->db->prepare($sqlLog);
        $obs = "Abono manual vía $metodo_pago. Ref Venta: #$venta_id";

        // 8 letras "iiddissi" para 8 variables
        $stmtLog->bind_param(
            "iiddissi",
            $cliente_id,   // 1
            $venta_id,     // 2
            $monto,        // 3
            $monto,        // 4 (mismo que monto)
            $venta_id,     // 5 (referencia_id)
            $obs,          // 6
            $fecha_pago,   // 7
            $usuario_id    // 8
        );

        return $stmtLog->execute();
    }
    /**
     * Afecta una sola columna de saldo sin realizar cruces automáticos (Netos).
     * * @param int    $cliente_id  ID del cliente
     * @param float  $monto       Monto a sumar (positivo) o restar (negativo)
     * @param int    $venta_id    Referencia de la venta
     * @param string $fecha       Fecha del movimiento
     * @param string $columna     'favor' para saldo_a_favor, 'contra' para saldo_en_contra
     */
    public function agregar_saldo_a_favor($cliente_id, $monto, $venta_id, $fecha)
    {
        $sql = "INSERT INTO `clientes_saldos` (
                `cliente_id`, 
                `saldo_a_favor`, 
                `ultima_venta_id`, 
                `ultima_actualizacion`
            ) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                `saldo_a_favor` = `saldo_a_favor` + ?, 
                `ultima_venta_id` = ?, 
                `ultima_actualizacion` = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "idisdis",
            $cliente_id, // 1 - i
            $monto,      // 2 - d  INSERT
            $venta_id,   // 3 - i  INSERT
            $fecha,      // 4 - s  INSERT
            $monto,      // 5 - d  UPDATE
            $venta_id,   // 6 - i  UPDATE
            $fecha       // 7 - s  UPDATE
        );

        return $stmt->execute();
    }

    public function agregar_saldo_en_contra($cliente_id, $monto, $venta_id, $fecha)
    {
        $sql = "INSERT INTO `clientes_saldos` (
                `cliente_id`, 
                `saldo_en_contra`, 
                `ultima_venta_id`, 
                `ultima_actualizacion`
            ) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                `saldo_en_contra` = `saldo_en_contra` + ?, 
                `ultima_venta_id` = ?, 
                `ultima_actualizacion` = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "idisdis",
            $cliente_id, // 1 - i
            $monto,      // 2 - d  INSERT
            $venta_id,   // 3 - i  INSERT
            $fecha,      // 4 - s  INSERT
            $monto,      // 5 - d  UPDATE
            $venta_id,   // 6 - i  UPDATE
            $fecha       // 7 - s  UPDATE
        );

        return $stmt->execute();
    }
}
