<?php
session_start();
require_once __DIR__ . '/Conexion_base.php';

// Solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?section=login');
    exit;
}

$accion   = $_POST['accion'] ?? '';
$redirect = '../index.php?section=dashboard';

switch ($accion) {

    // ── CREAR ─────────────────────────────────────────────────────────────
    case 'crear_video':
        $titulo      = trim($_POST['titulo']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $video_url   = trim($_POST['video_url']   ?? '');
        $categoria   = trim($_POST['categoria']   ?? 'general');

        if (empty($titulo) || empty($video_url)) {
            header('Location: ' . $redirect . '&error=El título y la URL del video son obligatorios');
            exit;
        }

        $stmt = $conn->prepare("
            INSERT INTO videos (titulo, descripcion, video_url, categoria, id_autor)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $titulo, $descripcion, $video_url, $categoria, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header('Location: ' . $redirect . '&ok=Video publicado correctamente');
        } else {
            header('Location: ' . $redirect . '&error=Error al guardar el video');
        }
        break;

    // ── ACTUALIZAR ────────────────────────────────────────────────────────
    case 'actualizar_video':
        $id          = (int)($_POST['id_video']      ?? 0);
        $titulo      = trim($_POST['titulo']          ?? '');
        $descripcion = trim($_POST['descripcion']     ?? '');
        $video_url   = trim($_POST['video_url']       ?? '');
        $categoria   = trim($_POST['categoria']       ?? 'general');

        if (empty($titulo) || empty($video_url)) {
            header('Location: ' . $redirect . '&error=El título y la URL del video son obligatorios');
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE videos SET titulo=?, descripcion=?, video_url=?, categoria=?
            WHERE id=? AND id_autor=?
        ");
        $stmt->bind_param("ssssii", $titulo, $descripcion, $video_url, $categoria, $id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header('Location: ' . $redirect . '&ok=Video actualizado correctamente');
        } else {
            header('Location: ' . $redirect . '&error=Error al actualizar el video');
        }
        break;

    // ── ELIMINAR ──────────────────────────────────────────────────────────
    case 'eliminar_video':
        $id = (int)($_POST['id_video'] ?? 0);

        $stmt = $conn->prepare("DELETE FROM videos WHERE id=? AND id_autor=?");
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header('Location: ' . $redirect . '&ok=Video eliminado correctamente');
        } else {
            header('Location: ' . $redirect . '&error=Error al eliminar el video');
        }
        break;

    default:
        header('Location: ' . $redirect);
        break;
}

$conn->close();
exit;