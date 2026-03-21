<?php
session_start();
include("Conexion_base.php");

if (isset($_POST['login'])) {

    $user = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE user='$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $fila = $result->fetch_assoc();

        if ($password == $fila['contraseña']) {

            $_SESSION['user'] = $fila['user'];

            header("Location: ../index.php");
            exit();

        } else {
            echo "Contraseña incorrecta";
        }

    } else {
        echo "Usuario no encontrado";
    }
}
?>