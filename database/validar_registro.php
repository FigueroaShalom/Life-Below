<?php
include("Conexion_base.php");

// 🧠 SABER QUÉ HACER
$action = $_POST['action'] ?? '';

// 🔍 VALIDAR USUARIO
if ($action === "validar_user") {

    $user = trim($_POST['user']);

    $result = $conn->query("SELECT id FROM usuarios WHERE user='$user'");

    if ($result->num_rows > 0) {
        echo "existe";
    } else {
        echo "disponible";
    }
}

// 🔍 VALIDAR EMAIL
elseif ($action === "validar_email") {

    $email = trim($_POST['email']);

    $result = $conn->query("SELECT id FROM usuarios WHERE email='$email'");

    if ($result->num_rows > 0) {
        echo "existe";
    } else {
        echo "disponible";
    }
}

// 🚀 REGISTRO
elseif ($action === "registro") {

    $user = trim($_POST['user']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($user) || empty($email) || empty($password)) {
        echo "vacio";
        exit();
    }

    // VALIDAR USER
    $checkUser = $conn->query("SELECT id FROM usuarios WHERE user='$user'");
    if ($checkUser->num_rows > 0) {
        echo "user";
        exit();
    }

    // VALIDAR EMAIL
    $checkEmail = $conn->query("SELECT id FROM usuarios WHERE email='$email'");
    if ($checkEmail->num_rows > 0) {
        echo "email";
        exit();
    }

    // ENCRIPTAR
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // INSERTAR
    $sql = "INSERT INTO usuarios (user, contraseña, email, fecha_de_registro)
            VALUES ('$user','$passwordHash','$email',NOW())";

    if ($conn->query($sql)) {
        echo "ok";
    } else {
        echo "error";
    }
}

$conn->close();