<?php
session_start();
require_once __DIR__ . '/Conexion_base.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Debes iniciar sesión']);
    exit;
}

$video_id = (int)($_POST['video_id'] ?? 0);
$usuario_id = (int)$_SESSION['user_id'];
$contenido = trim($_POST['contenido'] ?? '');

if ($video_id <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Video no válido']);
    exit;
}

if (empty($contenido)) {
    echo json_encode(['ok' => false, 'mensaje' => 'El comentario no puede estar vacío']);
    exit;
}

if (strlen($contenido) > 500) {
    echo json_encode(['ok' => false, 'mensaje' => 'El comentario es muy largo (máximo 500 caracteres)']);
    exit;
}

// Verificar que el video existe
$checkVid = $conn->prepare("SELECT id FROM videos WHERE id = ?");
$checkVid->bind_param("i", $video_id);
$checkVid->execute();
if ($checkVid->get_result()->num_rows === 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'El video no existe']);
    exit;
}

// Insertar comentario
$stmt = $conn->prepare("
    INSERT INTO comentarios_videos (video_id, usuario_id, contenido)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iis", $video_id, $usuario_id, $contenido);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Comentario publicado']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar el comentario']);
}
?>
