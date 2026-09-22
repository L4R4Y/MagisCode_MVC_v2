<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../helpers/Normalizador.php';
require_once __DIR__ . '/../helpers/Correo.php';

class AdminController
{
    private function ok(): void
    {
        if ((int) $_SESSION['rol_id'] !== 1) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    public function usuarios(): void
    {
        $this->ok();
        $q = trim($_GET['q'] ?? '');
        $rol = trim($_GET['rol'] ?? '');
        $usuarios = (new Usuario())->listar($q, $rol);

        require __DIR__ . '/../views/admin/usuarios.php';
    }

    public function editarUsuario(): void
    {
        $this->ok();
        $modelo = new Usuario();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location:index.php?route=usuarios');
            exit;
        }

        $u = $modelo->buscarPorId($id);
        if (!$u) {
            header('Location:index.php?route=usuarios');
            exit;
        }

        $roles = $modelo->roles();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = Normalizador::texto($_POST['nombre'] ?? '');
            $apellido = Normalizador::texto($_POST['apellido'] ?? '');
            $correo = Normalizador::texto($_POST['correo'] ?? '');
            $rol = (int) $_POST['rol'];

            if ($nombre === '' || !preg_match('/^[\p{L}\s]{2,300}$/u', $nombre)) {
                $error = 'El nombre es obligatorio (solo letras, mínimo 2 caracteres).';
            } elseif ($apellido === '' || !preg_match('/^[\p{L}\s]{2,300}$/u', $apellido)) {
                $error = 'El apellido es obligatorio (solo letras, mínimo 2 caracteres).';
            } elseif ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = 'Ingresa un correo electrónico válido.';
            } else {
                try {
                    $modelo->actualizarDatos($id, $nombre, $apellido, $correo, $rol);
                    header('Location:index.php?route=usuarios&editado=1');
                    exit;
                } catch (Throwable $e) {
                    $error = 'No fue posible actualizar el usuario. Verifica que el correo no esté en uso por otra cuenta.';
                }
            }

            $u['nombre'] = $nombre;
            $u['apellido'] = $apellido;
            $u['correo'] = $correo;
            $u['id_rol_u'] = $rol;
        }

        require __DIR__ . '/../views/admin/editar_usuario.php';
    }

    public function nuevoUsuario(): void
    {
        $this->ok();
        $error = '';
        $modelo = new Usuario();

        $roles = $modelo->roles();
        $tiposDocumento = $modelo->tiposDocumento();

        $d = [
            'id_usuario' => '',
            'nombre' => '',
            'apellido' => '',
            'correo' => '',
            'rol' => 3,
            'tipo_documento' => 1,
        ];
        $usernameGenerado = '';
        $passwordGenerado = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $d['id_usuario'] = trim($_POST['id_usuario'] ?? '');
            $d['nombre'] = Normalizador::texto($_POST['nombre'] ?? '');
            $d['apellido'] = Normalizador::texto($_POST['apellido'] ?? '');
            $d['correo'] = Normalizador::texto($_POST['correo'] ?? '');
            $d['rol'] = (int) $_POST['rol'];
            $d['tipo_documento'] = (int) ($_POST['tipo_documento'] ?? 1);

            $baseN = preg_replace('/[^a-zA-Z]/', '', $d['nombre']);
            $baseA = preg_replace('/[^a-zA-Z]/', '', $d['apellido']);
            $usernameGenerado = strtolower(substr($baseN, 0, 3) . substr($baseA, 0, 3) . substr($d['id_usuario'], -3));

            $passwordGenerado = bin2hex(random_bytes(4));

            $errores = [];
            if ($d['id_usuario'] === '' || !ctype_digit($d['id_usuario'])) {
                $errores[] = 'El documento es obligatorio y debe contener solo números.';
            }
            if ($d['nombre'] === '' || !preg_match('/^[\p{L}\s]{2,300}$/u', $d['nombre'])) {
                $errores[] = 'El nombre es obligatorio (solo letras, mínimo 2 caracteres).';
            }
            if ($d['apellido'] === '' || !preg_match('/^[\p{L}\s]{2,300}$/u', $d['apellido'])) {
                $errores[] = 'El apellido es obligatorio (solo letras, mínimo 2 caracteres).';
            }
            if ($d['correo'] === '' || !filter_var($d['correo'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo es obligatorio y debe ser un email válido.';
            }

            if (!empty($errores)) {
                $error = implode(' ', $errores);
            } else {
                try {
                    $modelo->crear([
                        'id_usuario' => (int) $d['id_usuario'],
                        'nombre' => $d['nombre'],
                        'apellido' => $d['apellido'],
                        'correo' => $d['correo'],
                        'username' => $usernameGenerado,
                        'password' => $passwordGenerado,
                        'rol' => $d['rol'],
                        'tipo_documento' => $d['tipo_documento'],
                    ]);

                    $correoEnviado = Correo::enviarCuentaCreada(
                        $d['correo'],
                        $d['nombre'] . ' ' . $d['apellido'],
                        $usernameGenerado,
                        $passwordGenerado
                    );

                if ($correoEnviado) {
                    header('Location:index.php?route=usuarios&ok=1');   
                } else{
                    header('Location:index.php?route=usuarios&ok=1&correo=0');
                }
                exit;
                }
                catch (Throwable $e) {
                    $error = 'No fue posible crear el usuario. Verifica que documento, correo y usuario no estén repetidos.';
                }
            }
        }

        require __DIR__ . '/../views/admin/nuevo_usuario.php';
    }

    public function asignar(): void
    {
        $this->ok();
        $m = new Curso();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $m->asignar((int) $_POST['curso'], (int) $_POST['aprendiz']);
                header('Location:index.php?route=asignar&ok=1');
                exit;
            } catch (Throwable $e) {
                $error = 'El aprendiz ya tiene asignado ese curso.';
            }
        }

        $c = $m->todos();
        $apr = (new Usuario())->aprendices();
        require __DIR__ . '/../views/admin/asignar.php';
    }

    public function estado(): void
    {
        $this->ok();
        (new Usuario())->actualizarEstado((int) $_POST['id'], (int) $_POST['estado']);
        header('Location:index.php?route=usuarios');
        exit;
    }

    public function importarUsuarios(): void
    {
        $this->ok();
        $modelo = new Usuario();
        $roles = $rolmap = [];
        foreach ($modelo->roles() as $r) {
            $roles[] = $r;
            $rolmap[strtolower($r['nombre_rol'])] = (int) $r['id_rol'];
            $rolmap[(string) $r['id_rol']] = (int) $r['id_rol'];
        }
        $tiposDocumento = $modelo->tiposDocumento();
        $tipoMap = [];
        foreach ($tiposDocumento as $t) {
            $tipoMap[strtolower($t['nombre_tipo'])] = (int) $t['id_tipo_documento'];
            $tipoMap[(string) $t['id_tipo_documento']] = (int) $t['id_tipo_documento'];
        }

        $error = '';
        $resultado = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
            $archivo = $_FILES['archivo'];

            if ($archivo['error'] !== UPLOAD_ERR_OK) {
                $error = 'No se pudo cargar el archivo. Inténtalo de nuevo.';
            } elseif ($archivo['size'] > 2 * 1024 * 1024) {
                $error = 'El archivo es demasiado grande (máx. 2 MB).';
            } else {
                $rutaTemp = $archivo['tmp_name'];
                $resultado = ['total' => 0, 'creados' => 0, 'errores' => []];
                $ultimoId = $modelo->ultimoId();

                if (($fh = fopen($rutaTemp, 'r')) !== false) {
                    $encabezados = fgetcsv($fh, 0, ';');
                    if ($encabezados === false) {
                        $encabezados = fgetcsv($fh, 0, ',');
                        fclose($fh);
                        $fh = fopen($rutaTemp, 'r');
                        $encabezadoSeparador = ',';
                    } else {
                        $encabezadoSeparador = ';';
                    }

                    $mapaCol = [];
                    foreach ($encabezados as $i => $nombre) {
                        $mapaCol[strtolower(trim($nombre))] = $i;
                    }

                    while (($fila = fgetcsv($fh, 0, $encabezadoSeparador)) !== false) {
                        $resultado['total']++;
                        $row = fn($k) => $fila[$mapaCol[$k] ?? -1] ?? '';

                        $nombre = Normalizador::texto($row('nombre'));
                        $apellido = Normalizador::texto($row('apellido'));
                        $correo = Normalizador::texto($row('correo'));
                        $username = Normalizador::texto($row('username') ?? $row('usuario'));
                        $rolRaw = strtolower(trim($row('rol')));
                        $tipoDocRaw = strtolower(trim($row('tipo_documento')));
                        $docRaw = trim($row('documento') ?? $row('id_usuario'));
                        $estadoRaw = strtolower(trim($row('estado')));

                        if ($nombre === '' || $apellido === '' || $correo === '' || $username === '') {
                            $resultado['errores'][] = "Fila {$resultado['total']}: faltan campos obligatorios (nombre, apellido, correo, username).";
                            continue;
                        }

                        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                            $resultado['errores'][] = "Fila {$resultado['total']}: correo inválido ({$correo}).";
                            continue;
                        }

                        if ($modelo->existeCorreo($correo)) {
                            $resultado['errores'][] = "Fila {$resultado['total']}: correo ya existe ({$correo}).";
                            continue;
                        }

                        if ($modelo->existeUsername($username)) {
                            $resultado['errores'][] = "Fila {$resultado['total']}: username ya existe ({$username}).";
                            continue;
                        }

                        if (isset($rolmap[$rolRaw])) {
                            $rolId = $rolmap[$rolRaw];
                        } else {
                            $resultado['errores'][] = "Fila {$resultado['total']}: rol inválido ({$rolRaw}).";
                            continue;
                        }

                        $tipoDocId = isset($tipoMap[$tipoDocRaw]) ? $tipoMap[$tipoDocRaw] : 1;

                        if ($docRaw !== '') {
                            $docId = (int) $docRaw;
                        } else {
                            $ultimoId++;
                            $docId = $ultimoId;
                        }

                        $passwordTemp = bin2hex(random_bytes(4));

                        try {
                            $modelo->crear([
                                'id_usuario' => $docId,
                                'nombre' => $nombre,
                                'apellido' => $apellido,
                                'correo' => $correo,
                                'username' => $username,
                                'password' => $passwordTemp,
                                'rol' => $rolId,
                                'tipo_documento' => $tipoDocId,
                        ]);

                        $correoEnviado = Correo::enviarCuentaCreada(
                            $correo,
                            $nombre . ' ' . $apellido,
                            $username,
                            $passwordTemp
                        );

                        $resultado['creados']++;

                        if (!$correoEnviado) {
                            $resultado['errores'][] =
                                "Fila {$resultado['total']}: usuario creado, pero no se pudo enviar el correo.";
                        }

                        } catch (Throwable $e) {
                            $resultado['errores'][] = "Fila {$resultado['total']}: " . $e->getMessage();
                        }
                    }

                    fclose($fh);
                } else {
                    $error = 'No se pudo leer el archivo.';
                }
            }
        }

        require __DIR__ . '/../views/admin/importar_usuarios.php';
    }
}
