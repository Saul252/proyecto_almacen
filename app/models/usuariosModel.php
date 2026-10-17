<?php
class UsuarioModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    /* ============================================================
       LISTADOS
    ============================================================ */
    public function listarUsuarios($id = 0)
    {
        $sql = "SELECT u.*,
               r.nombre AS rol_nombre,
               IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
        FROM usuarios u
        LEFT JOIN roles r ON u.rol_id = r.id
        LEFT JOIN almacenes a ON u.almacen_id = a.id
        WHERE 1=1";

        $params = [];
        $types = "";

        if ($id > 0) {
            $sql .= " AND u.almacen_id = ? and a.nombre IS NOT NULL";
            $types .= "i";
            $params[] = $id;
        }

        $sql .= " ORDER BY u.nombre ASC";

        $data = [];

        if ($id > 0) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            $res = $this->db->query($sql);
        }

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }
    public function listarTodosUsuarios($id = 0)
    {
        $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE 1=1";

        $params = [];
        $types = "";

        if ($id > 0) {
            $sql .= " AND u.almacen_id = ?";
            $types .= "i";
            $params[] = $id;
        }

        $sql .= " ORDER BY u.nombre ASC";

        $data = [];

        if ($id > 0) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            $res = $this->db->query($sql);
        }

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }
    public function listarUsuariosAlmacen($id = 0)
    {
        $sql = "SELECT u.*,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE 1=1";

        $params = [];
        $types = "";

        if ($id > 0) {
            $sql .= " AND u.almacen_id = ?";
            $types .= "i";
            $params[] = $id;
        }

        $sql .= " ORDER BY u.nombre ASC";

        $data = [];

        if ($id > 0) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            $res = $this->db->query($sql);
        }

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function listarUsuariosPorId($usuario_id = 0)
    {
        $usuario_id = intval($usuario_id);

        $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE 1=1";

        if ($usuario_id > 0) {
            $sql .= " AND u.id = ?";
        }

        $sql .= " ORDER BY u.nombre ASC";

        $data = [];

        if ($usuario_id > 0) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $stmt->close();
        } else {
            $res = $this->db->query($sql);
        }

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function listarTodosUsuariosAdmin($id = 0)
    {
        $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE u.almacen_id IS NULL";

        $params = [];
        $types = "";

        if ($id > 0) {
            $sql .= " AND u.id = ?";
            $types .= "i";
            $params[] = $id;
        }

        $sql .= " ORDER BY u.nombre ASC";

        $data = [];

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            $res = $this->db->query($sql);
        }

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /* ============================================================
       CATÁLOGOS
    ============================================================ */

    public function getRoles()
    {
        return $this->db->query("SELECT id, nombre FROM roles ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function getAlmacenes()
    {
        return $this->db->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }

    /* ============================================================
       CRUD DE USUARIOS
    ============================================================ */

    /**
     * Verifica si un nombre de usuario ya existe.
     * Si se pasa $excluir_id, excluye ese usuario de la búsqueda
     * (útil al editar para no marcar error contra uno mismo).
     *
     * @param string $username
     * @param int    $excluir_id
     * @return bool
     */
    public function existeUsername($username, $excluir_id = 0)
    {
        $sql = "SELECT id FROM usuarios WHERE username = ?";
        $params = [$username];
        $types = "s";

        if ($excluir_id > 0) {
            $sql .= " AND id != ?";
            $types .= "i";
            $params[] = $excluir_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    /**
     * Crea un nuevo usuario.
     *
     * @param string   $nombre
     * @param string   $username
     * @param string   $password
     * @param int      $rol_id
     * @param int|null $almacen_id
     * @return bool
     */
    public function crear($nombre, $username, $password, $rol_id, $almacen_id, $telefono)
    {
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nombre, username, telefono, password, rol_id, almacen_id, activo)
             VALUES (?, ?, ?, ?, ?, ?, 1)"
        );

        // s = string, i = integer
        // nombre(s), username(s), telefono(s), password(s), rol_id(i), almacen_id(i)
        $stmt->bind_param(
            "ssssii",
            $nombre,
            $username,
            $telefono,
            $passHash,
            $rol_id,
            $almacen_id
        );

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Actualiza un usuario existente (con o sin cambio de contraseña).
     *
     * @param int      $id
     * @param string   $nombre
     * @param string   $username
     * @param string   $telefono   ← NUEVO
     * @param string   $password
     * @param int      $rol_id
     * @param int|null $almacen_id
     * @return bool
     */
    public function actualizar($id, $nombre, $username, $password, $rol_id, $almacen_id, $telefono)
    {
        if (!empty($password)) {
            $passHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                "UPDATE usuarios
                 SET nombre=?, username=?, telefono=?, password=?, rol_id=?, almacen_id=?
                 WHERE id=?"
            );

            // ssssiii → nombre, username, telefono, passHash, rol_id, almacen_id, id
            $stmt->bind_param(
                "ssssiii",
                $nombre,
                $username,
                $telefono,
                $passHash,
                $rol_id,
                $almacen_id,
                $id
            );
        } else {
            $stmt = $this->db->prepare(
                "UPDATE usuarios
                 SET nombre=?, username=?, telefono=?, rol_id=?, almacen_id=?
                 WHERE id=?"
            );

            // sssiii → nombre, username, telefono, rol_id, almacen_id, id
            $stmt->bind_param(
                "sssiii",
                $nombre,
                $username,
                $telefono,
                $rol_id,
                $almacen_id,
                $id
            );
        }

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Alterna el estado activo/inactivo de un usuario.
     *
     * @param int $id
     * @return bool
     */
    public function toggleActivo($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET activo = (CASE WHEN activo = 1 THEN 0 ELSE 1 END) WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Obtiene el error de la conexión (para mensajes de BD).
     *
     * @return string
     */
    public function getError()
    {
        return $this->db->error;
    }
    /* ============================================================
   RECUPERACIÓN DE CONTRASEÑA
============================================================ */

    /**
     * Verifica identidad (telefono + username + correo) y si coincide,
     * genera una nueva contraseña, la hashea, la guarda en la BD
     * y devuelve la nueva contraseña en texto plano.
     *
     * @param string $telefono
     * @param string $username
     * @param string $correo
     * @return array
     *   [
     *     'success'           => bool,
     *     'message'           => string,
     *     'password_nueva'    => string|null,   // solo si success = true
     *     'usuario'           => array|null     // datos básicos del usuario
     *   ]
     */
    public function recuperarPassword($telefono, $username, $correo)
    {
        try {
            // ------------------------------------------------
            // 1) Buscar coincidencia exacta de los 3 campos
            // ------------------------------------------------
            $stmt = $this->db->prepare(
                "SELECT id, nombre, username, telefono, correo, activo
                 FROM usuarios
                 WHERE username = ?
                   AND correo   = ?
                   AND telefono = ?
                 LIMIT 1"
            );

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->db->error);
            }

            // sss → username, correo, telefono
            $stmt->bind_param("sss", $username, $correo, $telefono);
            $stmt->execute();
            $res = $stmt->get_result();
            $usuario = $res->fetch_assoc();
            $stmt->close();

            // ------------------------------------------------
            // 2) ¿Existe?
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
            // 3) ¿Está activo?
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
            // 4) Generar nueva contraseña
            //    Basada en: nombre + telefono + correo + aleatorio
            // ------------------------------------------------
            $passwordNueva = $this->generarPasswordRecuperacion(
                $usuario['nombre'],
                $usuario['telefono'],
                $usuario['correo']
            );

            // ------------------------------------------------
            // 5) Hashear y actualizar en BD
            // ------------------------------------------------
            $passHash = password_hash($passwordNueva, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                "UPDATE usuarios SET password = ? WHERE id = ?"
            );

            if (!$stmt) {
                throw new Exception('Error al preparar la actualización: ' . $this->db->error);
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
            // 6) Devolver éxito + nueva contraseña en claro
            // ------------------------------------------------
            return [
                'success' => true,
                'message' => 'Contraseña restablecida correctamente.',
                'password_nueva' => $passwordNueva,
                'usuario' => [
                    'id' => (int) $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'username' => $usuario['username'],
                    'correo' => $usuario['correo'],
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
     *   - primeras letras del correo
     *   - sufijo aleatorio
     *
     * Ejemplo: "JUA_4321_mar#7K9X"
     *
     * @param string $nombre
     * @param string $telefono
     * @param string $correo
     * @param int    $longitudAleatoria
     * @return string
     */
    private function generarPasswordRecuperacion($nombre, $telefono, $correo, $longitudAleatoria = 4)
    {
        // --- Prefijo del nombre (3 letras en mayúsculas, sin espacios) ---
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

        // --- Prefijo del correo (3 letras en minúsculas) ---
        $correoLimpio = preg_replace('/[^A-Za-z]/', '', strstr($correo, '@', true) ?: $correo);
        $prefijoCorreo = strtolower(substr($correoLimpio, 0, 3));
        if ($prefijoCorreo === '') {
            $prefijoCorreo = 'mail';
        }

        // --- Sufijo aleatorio (letras + números, seguro) ---
        $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $aleatorio = '';
        $max = strlen($caracteres) - 1;

        for ($i = 0; $i < $longitudAleatoria; $i++) {
            $aleatorio .= $caracteres[random_int(0, $max)];
        }

        // --- Un carácter especial en el medio ---
        $especiales = ['#', '@', '!', '$', '%', '&', '*'];
        $especial = $especiales[random_int(0, count($especiales) - 1)];

        // --- Ensamblar ---
        // Formato: NOMBRE_telefono_correo#ALEATORIO
        $password = $prefijoNombre
            . '_'
            . $sufijoTelefono
            . '_'
            . $prefijoCorreo
            . $especial
            . $aleatorio;

        return $password;
    }
}