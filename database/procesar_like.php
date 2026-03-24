<?php
session_start();
require_once __DIR__ . '/Conexion_base.php';

// Solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?section=login');
    exit;
}

$id_pub    = (int)($_POST['id_publicacion'] ?? 0);
$redirect  = $_POST['redirect'] ?? '../index.php?section=articulos';
$id_user   = (int)$_SESSION['user_id'];

if ($id_pub <= 0) {
    header('Location: ' . $redirect);
    exit;
}

// ¿Ya existe el like?
$check = $conn->prepare("SELECT id FROM likes WHERE id_publicacion = ? AND id_usuario = ?");
$check->bind_param("ii", $id_pub, $id_user);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    // Quitar like
    $del = $conn->prepare("DELETE FROM likes WHERE id_publicacion = ? AND id_usuario = ?");
    $del->bind_param("ii", $id_pub, $id_user);
    $del->execute();
} else {
    // Dar like
    $ins = $conn->prepare("INSERT INTO likes (id_publicacion, id_usuario) VALUES (?, ?)");
    $ins->bind_param("ii", $id_pub, $id_user);
    $ins->execute();
}

$conn->close();
header('Location: ../' . $redirect);
exit;