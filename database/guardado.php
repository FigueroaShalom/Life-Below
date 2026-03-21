<?php
include("Conexion_base.php");

$user = $_POST['user'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "INSERT INTO usuarios (user, contraseña, email, fecha_de_registro) 
VALUES ('$user', '$password', '$email', NOW())";

if ($conn->query($sql) === TRUE) {
    echo "Usuario guardado correctamente";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>