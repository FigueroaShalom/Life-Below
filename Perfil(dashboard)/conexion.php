<?php
$conexion = new mysqli("localhost", "root", "", "life_below_blog");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


?>