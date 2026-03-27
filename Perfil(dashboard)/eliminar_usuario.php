<?php
include("conexion.php");
session_start();

// 🔒 Verificar sesión
if(!isset($_SESSION['id'])){
    echo "Acceso no autorizado";
    exit();
}

// 🔒 SOLO ADMIN puede eliminar
if($_SESSION['rol'] != "administrador"){
    echo "No tienes permisos para eliminar usuarios";
    exit();
}

if(isset($_GET['id'])){

    $idEliminar = intval($_GET['id']); // 🔥 sanitizar
    $idActual = $_SESSION['id'];

    // 🚫 evitar que se elimine a sí mismo
    if($idEliminar == $idActual){
        echo "No puedes eliminar tu propia cuenta";
        exit();
    }

    // 🔐 consulta segura
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $idEliminar);

    if($stmt->execute()){
        header("Location: perfil.php");
        exit();
    } else {
        echo "Error al eliminar";
    }

    $stmt->close();

} else {
    echo "ID no válido";
}
?>