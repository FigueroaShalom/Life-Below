//Conexion de base de datos life_below_blog
<?php

$host = "localhost";
$usuario = "root";
$password = "";
$base = "life_below_blog";

$conn = new mysqli($host, $usuario, $password, $base);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>