<?php
include("conexion.php");
session_start();

// 🔒 Solo admin
if(!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador"){
    echo "Acceso no autorizado";
    exit();
}

if($_POST){

    // ✅ NUEVOS CAMPOS
    $user = trim($_POST['user']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // 🔐 Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 🔍 Validar usuario o correo duplicado
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE user = ? OR email = ?");
    $stmt->bind_param("ss", $user, $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){
        echo "
        <script>
            alert('El usuario o correo ya existe');
            window.location.href='perfil.php';
        </script>
        ";
        exit();
    }

    // ✅ Insertar usuario
    $stmt = $conexion->prepare("INSERT INTO usuarios (user, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user, $email, $passwordHash, $rol);

    if($stmt->execute()){
        echo "
        <script>
            alert('Usuario creado correctamente');
            window.location.href='perfil.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('Error al crear usuario');
            window.location.href='perfil.php';
        </script>
        ";
    }

    $stmt->close();

} else {
    echo "
    <script>
        alert('No se recibieron datos');
        window.location.href='perfil.php';
    </script>
    ";
}
?>