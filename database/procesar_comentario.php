<?php
session_start();
require_once __DIR__ . '/Conexion_base.php';

// Solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?section=login');
    exit;
}

$id_pub   = (int)($_POST['id_publicacion'] ?? 0);
$redirect = $_POST['redirect'] ?? '../index.php?section=articulos';
$id_user  = (int)$_SESSION['user_id'];
$texto    = trim($_POST['comentario'] ?? '');

if ($id_pub <= 0 || empty($texto)) {
    header('Location: ../' . $redirect);
    exit;
}

// Limitar longitud
$texto = substr($texto, 0, 1000);

$stmt = $conn->prepare("
    INSERT INTO comentarios (id_publicacion, id_usuario, comentario)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iis", $id_pub, $id_user, $texto);
$stmt->execute();

$conn->close();
header('Location: ../' . $redirect);
exit;