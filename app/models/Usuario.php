<?php

require_once __DIR__ . '/Model.php';

class Usuario extends Model
{
    public function buscarPorUsername(string $username): ?array
    {
        $sql = 'SELECT u.*, r.nombre_rol, e.nombre_estado
                FROM usuario u
                JOIN rol r ON r.id_rol = u.id_rol_u
                JOIN estado_usuario e ON e.id_estado = u.id_estado_u
                WHERE u.username = ?
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);

        return $stmt->fetch() ?: null;
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT u.*, r.nombre_rol, e.nombre_estado, t.nombre_tipo
                FROM usuario u
                JOIN rol r ON r.id_rol = u.id_rol_u
                JOIN estado_usuario e ON e.id_estado = u.id_estado_u
                JOIN tipo_documento t ON t.id_tipo_documento = u.id_tipo_documento_u
                WHERE u.id_usuario = ?
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Permite al Aprendiz corregir/actualizar únicamente los datos
     * personales de su perfil (nombre, apellido y correo).
     * Documento, username, rol, estado y contraseña NO se tocan aquí:
     * solo un administrador puede modificarlos.
     * El filtro id_rol_u = 3 evita que este método se use para editar
     * cuentas que no sean de Aprendiz, aunque se manipule el id recibido.
     */
    public function actualizarPerfilAprendiz(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE usuario
             SET nombre = ?, apellido = ?, correo = ?
             WHERE id_usuario = ? AND id_rol_u = 3'
        );

        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'],
            $datos['correo'],
            $id,
        ]);
    }

    public function registrarIntentoFallido(int $id): int
    {
        $stmt = $this->db->prepare(
            'UPDATE usuario
             SET intentos_fallidos = intentos_fallidos + 1
             WHERE id_usuario = ?'
        );
        $stmt->execute([$id]);

        $stmt = $this->db->prepare(
            'SELECT intentos_fallidos FROM usuario WHERE id_usuario = ?'
        );
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn();
    }

    public function reiniciarIntentos(int $id): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE usuario SET intentos_fallidos = 0 WHERE id_usuario = ?'
        );

        return $stmt->execute([$id]);
    }

    public function listar(string $q = '', string $rol = ''): array
    {
        $sql = 'SELECT u.*, r.nombre_rol, e.nombre_estado, t.nombre_tipo
                FROM usuario u
                JOIN rol r ON r.id_rol = u.id_rol_u
                JOIN estado_usuario e ON e.id_estado = u.id_estado_u
                JOIN tipo_documento t ON t.id_tipo_documento = u.id_tipo_documento_u
                WHERE 1';
        $parametros = [];

        if ($q !== '') {
            $sql .= ' AND (u.nombre LIKE ? OR u.apellido LIKE ? OR u.correo LIKE ?
                       OR u.username LIKE ? OR r.nombre_rol LIKE ?)';
            $busqueda = "%$q%";
            array_push($parametros, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
        }

        if ($rol !== '') {
            $sql .= ' AND u.id_rol_u = ?';
            $parametros[] = (int) $rol;
        }

        $sql .= ' ORDER BY u.nombre, u.apellido';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public function roles(): array
    {
        return $this->db->query(
            "SELECT id_rol, nombre_rol
             FROM rol
             ORDER BY id_rol"
        )->fetchAll();
    }

    public function tiposDocumento(): array
    {
        return $this->db->query(
            "SELECT id_tipo_documento, nombre_tipo
             FROM tipo_documento
             ORDER BY id_tipo_documento"
        )->fetchAll();
    }

    public function actualizarDatos(int $id, string $nombre, string $apellido, string $correo, int $rol): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE usuario SET nombre = ?, apellido = ?, correo = ?, id_rol_u = ? WHERE id_usuario = ?'
        );

        return $stmt->execute([$nombre, $apellido, $correo, $rol, $id]);
    }

    public function ultimoId(): int
    {
        return (int) $this->db->query('SELECT MAX(id_usuario) FROM usuario')->fetchColumn();
    }

    public function existeCorreo(string $correo): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM usuario WHERE correo = ? LIMIT 1');
        $stmt->execute([$correo]);
        return (bool) $stmt->fetchColumn();
    }

    public function existeUsername(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM usuario WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        return (bool) $stmt->fetchColumn();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO usuario
             (id_usuario, nombre, apellido, correo, username, hash_contrasena,
              id_rol_u, id_estado_u, id_tipo_documento_u)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $datos['id_usuario'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['correo'],
            $datos['username'],
            password_hash($datos['password'], PASSWORD_DEFAULT),
            $datos['rol'],
            1,
            $datos['tipo_documento']
        ]);
    }

    public function actualizarEstado(int $id, int $estado): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE usuario SET id_estado_u = ? WHERE id_usuario = ?'
        );

        return $stmt->execute([$estado, $id]);
    }

    public function cambiarPassword(int $id, string $password): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE usuario
             SET hash_contrasena = ?, intentos_fallidos = 0
             WHERE id_usuario = ?'
        );

        return $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $id
        ]);
    }

    public function aprendices(): array
    {
        return $this->db->query(
            "SELECT id_usuario, nombre, apellido, correo, username, fecha_creacion
             FROM usuario
             WHERE id_rol_u = 3 AND id_estado_u = 1
             ORDER BY nombre, apellido"
        )->fetchAll();
    }

    public function instructores(): array
    {
        return $this->db->query(
            "SELECT id_usuario, nombre, apellido, correo, username, fecha_creacion
             FROM usuario
             WHERE id_rol_u = 2 AND id_estado_u = 1
             ORDER BY nombre, apellido"
        )->fetchAll();
    }
}
