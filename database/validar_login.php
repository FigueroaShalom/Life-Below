<?php
session_start();
include("Conexion_base.php");

$user = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($user) || empty($password)) { echo "error_vacio"; exit(); }

$stmt = $conn->prepare("SELECT id, user, contraseña FROM usuarios WHERE user = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $fila = $result->fetch_assoc();
    if (password_verify($password, $fila['contraseña'])) {
        $_SESSION['user'] = $fila['user'];
        $_SESSION['user_id'] = $fila['id']; // ID para las relaciones de la BD
        echo "ok";
    } else { echo "error_password"; }
} else { echo "error_user"; }
$conn->close();
?>