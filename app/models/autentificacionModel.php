<?php
class AuthModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los datos del usuario por username uniendo con la tabla roles
     */
    public function obtenerPorUsername(string $username): ?array
    {
        $sql = "SELECT u.id, u.nombre, u.username, u.password, u.rol_id, u.almacen_id, u.activo, r.nombre AS rol, almacen.tipo_plan as plan, almacen.logo as logo, almacen.ico as ico,almacen.pago as pago, almacen.nombre as nombre_almacen
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                left join almacenes almacen on almacen.id=u.almacen_id
                WHERE u.username = ? 
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return ($resultado && $resultado->num_rows === 1) ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene la hora de cierre de un almacén específico
     */
    public function obtenerHoraCierreAlmacen(int $almacenId): string
    {
        $sql = "SELECT hora_cierre_programada FROM almacenes WHERE id = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return "11:58";
        }

        $stmt->bind_param("i", $almacenId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        return $res['hora_cierre_programada'] ?? "11:58";
    }

    /**
     * Busca la coincidencia de un trabajador por su nombre limpio
     */
    public function buscarTrabajadorPorNombre(string $nombreLimpio): ?array
    {
        $sql = "SELECT id, almacen_id FROM trabajadores WHERE nombre LIKE ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $busqueda = $nombreLimpio . "%";
        $stmt->bind_param("s", $busqueda);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        return $res ?: null;
    }
    /**
     * Registra una entrada exitosa al sistema en la tabla de auditoría.
     *
     * @param int    $usuarioId  ID del usuario que inicia sesión
     * @param string $username   Nombre de usuario
     * @param int    $rolId      ID del rol
     * @param string $rol        Nombre del rol
     * @param int    $almacenId  ID del almacén activo
     * @param string $ip         IP real del cliente
     * @param string $userAgent  User-Agent del navegador
     * @return bool              true si el INSERT fue exitoso
     */

    public function registrarEntrada(
        ?int $usuarioId,
        string $usernameIntentado,
        string $ip,
        string $userAgent,
        bool $exito,
        string $motivo
    ): bool {

        $sql = "INSERT INTO audit_login (
                usuario_id,
                username_intentado,
                exito,
                ip,
                user_agent,
                fecha_hora,
                motivo
            ) VALUES (?, ?, ?, ?, ?, NOW(), ?)";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Error al preparar auditoría de login: " .
                $this->conexion->error
            );
        }

        $exitoInt = $exito ? 1 : 0;

        $stmt->bind_param(
            "isisss",
            $usuarioId,
            $usernameIntentado,
            $exitoInt,
            $ip,
            $userAgent,
            $motivo
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                "Error al registrar auditoría de login: " . $error
            );
        }

        $stmt->close();

        return true;
    }
    /* ============================================================
       RECUPERACIÓN DE CONTRASEÑA
    ============================================================ */

    /**
     * Verifica identidad (telefono + username + correo).
     * Si coincide, genera una nueva contraseña, la hashea,
     * la guarda en BD y devuelve la contraseña en texto plano.
     *
     * IMPORTANTE en este sistema:
     *   - username = correo electrónico (login)
     *   - nombre   = nombre real del usuario
     *   - telefono = teléfono (VARCHAR, con + y espacios)
     *
     * El teléfono se normaliza en AMBOS lados (PHP + SQL) para
     * que funcione sin importar el formato:
     *   "+52 55 2378 9029"  ↔  "+525523789029"
     *
     * @param string $telefono
     * @param string $username   ← este ES el correo
     * @param string $correo     ← redundante, debe ser igual a username
     * @return array
     */
    public function recuperarPassword(string $telefono, string $username, string $correo): array
    {
        try {
            // ------------------------------------------------
            // 1) Normalizar entradas
            // ------------------------------------------------
            $username = trim($username);
            $correo = trim($correo);



            // Teléfono: solo dígitos
            $telefonoNormalizado = preg_replace('/\D/', '', $telefono);

            if ($telefonoNormalizado === '' || strlen($telefonoNormalizado) < 8) {
                return [
                    'success' => false,
                    'message' => 'El teléfono debe tener al menos 8 dígitos.',
                    'password_nueva' => null,
                    'usuario' => null
                ];
            }

            // ------------------------------------------------
            // 2) Buscar al usuario por username + telefono
            //    - LOWER() para ignorar mayúsculas/minúsculas
            //    - REPLACE anidados para normalizar el teléfono de la BD
            // ------------------------------------------------
            $sql = "SELECT id, nombre, username, telefono, activo
                    FROM usuarios
                    WHERE LOWER(username) = LOWER(?)
                      AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                              telefono,
                              ' ', ''),
                              '-', ''),
                              '(', ''),
                              ')', ''),
                              '+', ''
                          ) = ?
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception(
                    'Error al preparar la consulta: ' . $this->conexion->error
                );
            }

            // ss → username, telefonoNormalizado
            $stmt->bind_param("ss", $correo, $telefonoNormalizado);
            $stmt->execute();
            $res = $stmt->get_result();
            $usuario = $res->fetch_assoc();
            $stmt->close();

            // ------------------------------------------------
            // 3) ¿Existe el usuario?
            // ------------------------------------------------
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Los datos no coinciden con ningún usuario registrado.',
                    'password_nueva' => null,
                    'usuario' => null
                ];
            }

            // ------------------------------------------------
            // 4) ¿Está activo?
            // ------------------------------------------------
            if ((int) $usuario['activo'] !== 1) {
                return [
                    'success' => false,
                    'message' => 'La cuenta está inactiva. Contacta al administrador.',
                    'password_nueva' => null,
                    'usuario' => null
                ];
            }

            // ------------------------------------------------
            // 5) Generar nueva contraseña
            // ------------------------------------------------
            $passwordNueva = $this->generarPasswordRecuperacion(
                $usuario['nombre'],
                $usuario['telefono'],
                $usuario['username']
            );

            // ------------------------------------------------
            // 6) Hashear y actualizar en BD
            // ------------------------------------------------
            $passHash = password_hash($passwordNueva, PASSWORD_DEFAULT);

            $stmt = $this->conexion->prepare(
                "UPDATE usuarios SET password = ? WHERE id = ?"
            );

            if (!$stmt) {
                throw new Exception(
                    'Error al preparar la actualización: ' . $this->conexion->error
                );
            }

            $stmt->bind_param("si", $passHash, $usuario['id']);
            $ok = $stmt->execute();
            $stmt->close();

            if (!$ok) {
                return [
                    'success' => false,
                    'message' => 'No se pudo actualizar la contraseña. Intenta nuevamente.',
                    'password_nueva' => null,
                    'usuario' => null
                ];
            }

            // ------------------------------------------------
            // 7) Devolver éxito + nueva contraseña en claro
            // ------------------------------------------------
            return [
                'success' => true,
                'message' => 'Contraseña restablecida correctamente.',
                'password_nueva' => $passwordNueva,
                'usuario' => [
                    'id' => (int) $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'username' => $usuario['username'],
                    'correo' => $usuario['username'],   // alias para el frontend
                    'telefono' => $usuario['telefono']
                ]
            ];

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage(),
                'password_nueva' => null,
                'usuario' => null
            ];
        }
    }

    /**
     * Genera una contraseña aleatoria legible basada en:
     *   - primeras letras del nombre
     *   - últimos 4 dígitos del teléfono
     *   - primeras letras del correo (username)
     *   - sufijo aleatorio
     *
     * Ejemplo: "JUA_9029_sau#7K9X"
     */
    private function generarPasswordRecuperacion(
        string $nombre,
        string $telefono,
        string $correo,
        int $longitudAleatoria = 4
    ): string {

        // --- Prefijo del nombre (3 letras mayúsculas) ---
        $nombreLimpio = preg_replace('/[^A-Za-z]/', '', $nombre);
        $prefijoNombre = strtoupper(substr($nombreLimpio, 0, 3));
        if ($prefijoNombre === '') {
            $prefijoNombre = 'USR';
        }

        // --- Últimos 4 dígitos del teléfono ---
        $soloDigitos = preg_replace('/\D/', '', $telefono);
        $sufijoTelefono = substr($soloDigitos, -4);
        if (strlen($sufijoTelefono) < 4) {
            $sufijoTelefono = str_pad($sufijoTelefono, 4, '0', STR_PAD_LEFT);
        }

        // --- Prefijo del correo (3 letras minúsculas, antes del @) ---
        $correoLimpio = preg_replace(
            '/[^A-Za-z]/',
            '',
            strstr($correo, '@', true) ?: $correo
        );
        $prefijoCorreo = strtolower(substr($correoLimpio, 0, 3));
        if ($prefijoCorreo === '') {
            $prefijoCorreo = 'mail';
        }

        // --- Sufijo aleatorio (letras + números) ---
        $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $aleatorio = '';
        $max = strlen($caracteres) - 1;

        for ($i = 0; $i < $longitudAleatoria; $i++) {
            $aleatorio .= $caracteres[random_int(0, $max)];
        }

        // --- Carácter especial aleatorio ---
        $especiales = ['#', '@', '!', '$', '%', '&', '*'];
        $especial = $especiales[random_int(0, count($especiales) - 1)];

        // --- Ensamblar: NOMBRE_telefono_correo#ALEATORIO ---
        return $prefijoNombre
            . '_'
            . $sufijoTelefono
            . '_'
            . $prefijoCorreo
            . $especial
            . $aleatorio;
    }
}