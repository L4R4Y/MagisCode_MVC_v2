<?php
require 'C:/xampp/htdocs/MagisCode_MVC_v2/config/config.php';
require 'C:/xampp/htdocs/MagisCode_MVC_v2/config/database.php';

$pdo = Database::getConnection();

echo "=== Videos MP4 en recursos ===\n";
$r = $pdo->query("SELECT id_recurso, nombre_recurso, tipo_recurso, ruta_archivo, duracion FROM recurso WHERE tipo_recurso = 'MP4'");
foreach ($r as $row) {
    echo "ID: {$row['id_recurso']} | Nombre: {$row['nombre_recurso']} | Ruta: {$row['ruta_archivo']} | Duracion: {$row['duracion']}\n";
}

echo "\n=== Tabla recurso_visto (registros existentes) ===\n";
$r2 = $pdo->query("SELECT * FROM recurso_visto LIMIT 10");
if ($r2->rowCount() === 0) {
    echo "No hay registros todavía.\n";
} else {
    foreach ($r2 as $row) {
        echo "ID: {$row['id_registro']} | recurso: {$row['id_recurso']} | usuario: {$row['id_usuario']} | fecha: {$row['fecha_visto']}\n";
    }
}

echo "\n=== Usuarios (aprendices) ===\n";
$r3 = $pdo->query("SELECT id_usuario, nombre, apellido, id_rol_u FROM usuario WHERE id_rol_u = 3 LIMIT 5");
foreach ($r3 as $row) {
    echo "ID: {$row['id_usuario']} | Nombre: {$row['nombre']} {$row['apellido']} | Rol: {$row['id_rol_u']}\n";
}
