<?php
session_start();
require 'C:/xampp/htdocs/MagisCode_MVC_v2/config/config.php';
require 'C:/xampp/htdocs/MagisCode_MVC_v2/config/database.php';
require 'C:/xampp/htdocs/MagisCode_MVC_v2/app/models/Curso.php';

$_SESSION['usuario'] = 888888888;
$_SESSION['rol_id'] = 3;

$modelo = new Curso();

echo "=== Testing recursosVistosCurso ===\n";
$vistos = $modelo->recursosVistosCurso(1, 888888888);
echo "Result: ";
var_dump($vistos);

echo "\n=== Testing calcularAvance ===\n";
$avance = $modelo->calcularAvance(1, 888888888);
echo "Avance: $avance\n";

echo "\n=== Testing marcarRecursoVisto ===\n";
$result = $modelo->marcarRecursoVisto(75, 888888888);
echo "Resultado: ";
var_dump($result);

echo "\n=== Testing recursosVistosCurso after ===\n";
$vistos2 = $modelo->recursosVistosCurso(1, 888888888);
echo "Result: ";
var_dump($vistos2);

echo "\n=== Testing calcularAvance after ===\n";
$avance2 = $modelo->calcularAvance(1, 888888888);
echo "Avance: $avance2\n";
