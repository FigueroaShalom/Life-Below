<?php

$host = "localhost";
$usuario = "root";
$password = "";
$base = "curso1";

$conn = new mysqli($host, $usuario, $password, $base);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>