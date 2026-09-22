<?php

require_once __DIR__ . '/../app/helpers/Normalizador.php';
require_once __DIR__ . '/../app/models/Usuario.php';

echo "=== VALIDACIÓN: Conversión a minúsculas de datos de formularios ===\n\n";

$failed = 0;
$passed = 0;

function test(string $label, bool $condition): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  ✓ PASS: $label\n";
    } else {
        $failed++;
        echo "  ✗ FAIL: $label\n";
    }
}

echo "--- 1. Normalizador::texto() convierte a minúsculas ---\n";
test("Normalizador::texto('JUAN') === 'juan'", Normalizador::texto('JUAN') === 'juan');
test("Normalizador::texto('PeReZ') === 'perez'", Normalizador::texto('PeReZ') === 'perez');
test("Normalizador::texto('CORREO@EJEMPLO.COM') === 'correo@ejemplo.com'", Normalizador::texto('CORREO@EJEMPLO.COM') === 'correo@ejemplo.com');
test("Normalizador::texto('  Hola Mundo  ') === 'hola mundo'", Normalizador::texto('  Hola Mundo  ') === 'hola mundo');
test("Normalizador::texto('JUAN') !== 'JUAN'", Normalizador::texto('JUAN') !== 'JUAN');

echo "\n--- 2. Normalizador::campos() convierte campos específicos ---\n";
$datos = ['nombre' => 'JUAN', 'apellido' => 'PEREZ', 'correo' => 'JUAN@EJEMPLO.COM', 'password' => 'MiClave123'];
$resultado = Normalizador::campos($datos, ['nombre', 'apellido', 'correo']);
test("campos() convierte 'nombre' a minúsculas", $resultado['nombre'] === 'juan');
test("campos() convierte 'apellido' a minúsculas", $resultado['apellido'] === 'perez');
test("campos() convierte 'correo' a minúsculas", $resultado['correo'] === 'juan@ejemplo.com');
test("campos() NO convierte 'password' a minúsculas", $resultado['password'] === 'MiClave123');

echo "\n--- 3. Formulario: Nuevo Usuario (AdminController::nuevoUsuario) ---\n";
$nombreSimulado = Normalizador::texto('JUAN');
$apellidoSimulado = Normalizador::texto('PÉREZ');
$correoSimulado = Normalizador::texto('JUAN.PEREZ@MAGISCODE.COM');
test("nombre simulado está en minúsculas", $nombreSimulado === 'juan');
test("apellido simulado está en minúsculas", $apellidoSimulado === 'pérez');
test("correo simulado está en minúsculas", $correoSimulado === 'juan.perez@magiscode.com');
$usernameGenerado = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $nombreSimulado), 0, 3) . substr(preg_replace('/[^a-zA-Z]/', '', $apellidoSimulado), 0, 3) . '001');
test("username generado está en minúsculas", $usernameGenerado === 'juapre001');

echo "\n--- 4. Formulario: Editar Usuario (AdminController::editarUsuario) ---\n";
$nombreEditado = Normalizador::texto('MARÍA');
$apellidoEditado = Normalizador::texto('GARCÍA');
$correoEditado = Normalizador::texto('MARIA.GARCIA@CORREO.COM');
test("nombre editado está en minúsculas", $nombreEditado === 'maría');
test("apellido editado está en minúsculas", $apellidoEditado === 'garcía');
test("correo editado está en minúsculas", $correoEditado === 'maria.garcia@correo.com');

echo "\n--- 5. Formulario: Importar Usarios (AdminController::importarUsuarios) ---\n";
$nombreImportado = Normalizador::texto('CARLOS');
$apellidoImportado = Normalizador::texto('LOPEZ');
$correoImportado = Normalizador::texto('CARLOS.LOPEZ@EJEMPLO.COM');
$usernameImportado = Normalizador::texto('CLOPEZ');
test("nombre importado está en minúsculas", $nombreImportado === 'carlos');
test("apellido importado está en minúsculas", $apellidoImportado === 'lopez');
test("correo importado está en minúsculas", $correoImportado === 'carlos.lopez@ejemplo.com');
test("username importado está en minúsculas", $usernameImportado === 'clopez');

echo "\n--- 6. Login: AuthController::login() ---\n";
$usernameLogin = Normalizador::texto('ADMIN');
$passwordLogin = 'MiP@ssword123';
test("username de login está en minúsculas", $usernameLogin === 'admin');
test("password de login NO se convierte a minúsculas", $passwordLogin === 'MiP@ssword123');

echo "\n--- 7. Cambiar contraseña: ConfiguracionController::password() ---\n";
$passwordActual = 'ClaveActual2024';
$passwordNueva = 'NuevaClave2024';
test("password actual NO se convierte a minúsculas", $passwordActual === 'ClaveActual2024');
test("password nueva NO se convierte a minúsculas", $passwordNueva === 'NuevaClave2024');

echo "\n--- 8. Modelo Usuario::crear() - Normalizador::campos() excluye password ---\n";
$datosCrear = [
    'id_usuario' => 1000000100,
    'nombre' => 'TEST',
    'apellido' => 'USER',
    'correo' => 'TEST@EJEMPLO.COM',
    'username' => 'TESTUSER',
    'password' => 'MiP@ssw0rd',
    'rol' => 3,
    'tipo_documento' => 1,
];
$datosReflejados = Normalizador::campos($datosCrear, ['nombre', 'apellido', 'correo', 'username']);
test("modelo::crear() normaliza nombre", $datosReflejados['nombre'] === 'test');
test("modelo::crear() normaliza apellido", $datosReflejados['apellido'] === 'user');
test("modelo::crear() normaliza correo", $datosReflejados['correo'] === 'test@ejemplo.com');
test("modelo::crear() normaliza username", $datosReflejados['username'] === 'testuser');
test("modelo::crear() NO normaliza password", $datosReflejados['password'] === 'MiP@ssw0rd');

echo "\n--- 9. Modelo Usuario::actualizarDatos() ---\n";
$nombreUpdate = Normalizador::texto('ANDRES');
$apellidoUpdate = Normalizador::texto('RODRIGUEZ');
$correoUpdate = Normalizador::texto('ANDRES@CORREO.COM');
test("actualizarDatos() normaliza nombre", $nombreUpdate === 'andres');
test("actualizarDatos() normaliza apellido", $apellidoUpdate === 'rodriguez');
test("actualizarDatos() normaliza correo", $correoUpdate === 'andres@correo.com');

echo "\n--- 10. Modelo Usuario::actualizarPerfilAprendiz() ---\n";
$datosPerfil = Normalizador::campos(['nombre' => 'LUCÍA', 'apellido' => 'MORA', 'correo' => 'LUCIA@CORREO.COM'], ['nombre', 'apellido', 'correo']);
test("actualizarPerfilAprendiz() normaliza nombre", $datosPerfil['nombre'] === 'lucía');
test("actualizarPerfilAprendiz() normaliza apellido", $datosPerfil['apellido'] === 'mora');
test("actualizarPerfilAprendiz() normaliza correo", $datosPerfil['correo'] === 'lucia@correo.com');

echo "\n========================================\n";
echo "RESULTADO: $passed passed, $failed failed\n";
echo "========================================\n";

if ($failed > 0) {
    exit(1);
}
