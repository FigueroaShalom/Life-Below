<?php
// Asegurarnos de que las variables de entorno están cargadas
$env_path = __DIR__ . '/../.env';
$env_vars = [];
if (file_exists($env_path)) {
    $env_vars = parse_ini_file($env_path);
}

$host = $env_vars['DB_HOST'] ?? "localhost";
$usuario = $env_vars['DB_USER'] ?? "root";
$password = $env_vars['DB_PASS'] ?? "";
$base = $env_vars['DB_NAME'] ?? "life_below_blog";

$conn = new mysqli($host, $usuario, $password, $base);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
