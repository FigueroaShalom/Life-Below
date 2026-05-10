<?php
session_start();
require_once __DIR__ . '/Conexion_base.php';

header('Content-Type: application/json');

$video_id = (int)($_GET['video_id'] ?? 0);

if ($video_id <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Video no válido']);
    exit;
}

$stmt = $conn->prepare("
    SELECT cv.id, cv.contenido, cv.fecha, u.user AS autor
    FROM comentarios_videos cv
    JOIN usuarios u ON cv.usuario_id = u.id
    WHERE cv.video_id = ?
    ORDER BY cv.fecha DESC
    LIMIT 50
");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($row = $result->fetch_assoc()) {
    $comentarios[] = [
        'autor' => $row['autor'],
        'contenido' => $row['contenido'],
        'fecha' => date('d/m/Y H:i', strtotime($row['fecha']))
    ];
}

echo json_encode([
    'ok' => true,
    'comentarios' => $comentarios
]);
?>
