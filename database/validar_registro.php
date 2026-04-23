<?php
include("Conexion_base.php");

function validarPassword($pass) {
    $errores = [];
    if (strlen($pass) < 8) $errores[] = "al menos 8 caracteres";
    if (!preg_match('/[A-Z]/', $pass)) $errores[] = "una mayúscula";
    if (!preg_match('/[0-9]/', $pass) && !preg_match('/[^a-zA-Z0-9]/', $pass)) $errores[] = "un número o símbolo especial";
    return empty($errores) ? true : implode(", ", $errores);
}

$action = $_POST['action'] ?? '';

// 🔍 VALIDAR USUARIO
if ($action === "validar_user") {

    $user = trim($_POST['user']);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE user = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    echo ($result->num_rows > 0) ? "existe" : "disponible";
}

// 🔍 VALIDAR EMAIL
elseif ($action === "validar_email") {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    echo ($result->num_rows > 0) ? "existe" : "disponible";
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

    // 🔍 VALIDAR CONTRASEÑA
    $passCheck = validarPassword($password);
    if ($passCheck !== true) {
        echo "password_weak: " . $passCheck;
        exit();
    }

    // 🔍 VALIDAR USER
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE user = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "user";
        exit();
    }

    // 🔍 VALIDAR EMAIL
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "email";
        exit();
    }

    // 🔐 ENCRIPTAR
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 🔥 ROL AUTOMÁTICO
    $rol = "usuario";

    // ✅ INSERTAR CORRECTO
    $stmt = $conn->prepare("INSERT INTO usuarios (user, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user, $email, $passwordHash, $rol);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error";
    }
}

$conn->close();
?>