<?php
class UsuarioModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }
    public function listarUsuarios($id = 0)
    {
        // 1. Estructura base de la consulta (WHERE va antes de ORDER BY)
        $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE 1=1";

        $params = [];
        $types = "";

        // 2. Filtro dinámico
        if ($id > 0) {
            $sql .= " AND u.id = ?";
            $types .= "i";
            $params[] = $id;
        }

        // 3. El ordenamiento se concatena al final de todo
        $sql .= " ORDER BY u.nombre ASC";

        // 4. Ejecución segura con Query Prepared Statements
        $data = [];

        if ($id > 0) {
            // Si hay parámetros, preparamos la consulta para evitar Inyección SQL
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            // Si no hay parámetros, se ejecuta directo de forma segura
            $res = $this->db->query($sql);
        }

        // 5. Llenado del array de resultados
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }
    public function listarTodosUsuarios($id = 0)
    {
        // 1. Estructura base de la consulta (WHERE va antes de ORDER BY)
        $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE 1=1";

        $params = [];
        $types = "";

        // 2. Filtro dinámico
        if ($id > 0) {
            $sql .= " AND u.almacen_id = ?";
            $types .= "i";
            $params[] = $id;
        }

        // 3. El ordenamiento se concatena al final de todo
        $sql .= " ORDER BY u.nombre ASC";

        // 4. Ejecución segura con Query Prepared Statements
        $data = [];

        if ($id > 0) {
            // Si hay parámetros, preparamos la consulta para evitar Inyección SQL
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            // Si no hay parámetros, se ejecuta directo de forma segura
            $res = $this->db->query($sql);
        }

        // 5. Llenado del array de resultados
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }
    /**
     * Lista usuarios. Si se pasa $usuario_id > 0, trae ÚNICAMENTE la fila de ese usuario.
     *
     * @param int $usuario_id ID del usuario específico (0 = todos)
     * @return array Lista de usuarios
     */
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
        // 1. Estructura base filtrando por valores NULL en almacen_id
        $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE u.almacen_id IS NULL";

        $params = [];
        $types = "";

        // 2. Si se pasa un ID específico de usuario para filtrar
        if ($id > 0) {
            $sql .= " AND u.id = ?";
            $types .= "i";
            $params[] = $id;
        }

        // 3. Ordenamiento al final
        $sql .= " ORDER BY u.nombre ASC";

        // 4. Ejecución de la consulta
        $data = [];

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            $res = $this->db->query($sql);
        }

        // 5. Llenado de resultados
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }
    public function getRoles()
    {
        return $this->db->query("SELECT id, nombre FROM roles ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function getAlmacenes()
    {
        return $this->db->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }
}