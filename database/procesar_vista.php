<?php
session_start();
require_once __DIR__ . '/Conexion_base.php';

// Solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    exit;
}

$video_id = (int)($_POST['video_id'] ?? 0);
$user_id  = (int)$_SESSION['user_id'];

if ($video_id <= 0) {
    exit;
}

// Insertar vista
$ins = $conn->prepare("INSERT INTO video_views (user_id, video_id) VALUES (?, ?)");
$ins->bind_param("ii", $user_id, $video_id);
$ins->execute();

$conn->close();
?>