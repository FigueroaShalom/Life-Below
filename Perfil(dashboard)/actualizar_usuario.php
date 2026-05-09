<?php
include("conexion.php");
session_start();

// 🔒 Solo admin
if(!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador"){
    echo "Acceso no autorizado";
    exit();
}

if(isset($_POST['actualizar'])){

    $id = $_POST['id'];
    $user = $_POST['user'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    // 🔐 Update seguro
    $stmt = $conexion->prepare("UPDATE usuarios SET user=?, email=?, rol=? WHERE id=?");
    $stmt->bind_param("sssi", $user, $email, $rol, $id);

    if($stmt->execute()){
        // ✅ REDIRECCIÓN CORRECTA
        header("Location: perfil.php?mensaje=actualizado");
        exit();
    } else {
        echo "Error al actualizar";
    }

    $stmt->close();
}
?>