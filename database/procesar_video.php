<?php
session_start();
require_once __DIR__ . '/Conexion_base.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?section=login');
    exit;
}

$accion   = $_POST['accion'] ?? '';
$redirect = '../index.php?section=dashboard&tab=videos';

// ── Helper: subir archivo de video ────────────────────────────────────────────
function subirVideoArchivo($file_input) {
    if (!isset($_FILES[$file_input]) || $_FILES[$file_input]['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Error al recibir el archivo.'];
    }

    $file     = $_FILES[$file_input];
    $maxSize  = 200 * 1024 * 1024; // 200 MB
    $allowed  = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'];
    $allowedExt = ['mp4', 'webm', 'mov', 'avi'];

    if ($file['size'] > $maxSize) {
        return ['ok' => false, 'error' => 'El archivo supera los 200 MB.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return ['ok' => false, 'error' => 'Formato no permitido. Usa MP4, WebM, MOV o AVI.'];
    }

    // Verificar MIME real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed)) {
        return ['ok' => false, 'error' => 'El archivo no es un video válido.'];
    }

    // Crear carpeta si no existe
    $uploadDir = __DIR__ . '/../uploads/videos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Nombre único
    $filename = uniqid('vid_', true) . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['ok' => false, 'error' => 'No se pudo guardar el archivo en el servidor.'];
    }

    return ['ok' => true, 'url' => 'uploads/videos/' . $filename];
}

// ── Helper: detectar tipo de fuente ──────────────────────────────────────────
function resolverVideoUrl($accion_nombre) {
    $fuente = $_POST['fuente'] ?? 'youtube'; // 'youtube' o 'local'

    if ($fuente === 'local') {
        $result = subirVideoArchivo('video_file');
        if (!$result['ok']) {
            return ['ok' => false, 'error' => $result['error']];
        }
        return ['ok' => true, 'url' => $result['url']];
    } else {
        $url = trim($_POST['video_url'] ?? '');
        if (empty($url)) {
            return ['ok' => false, 'error' => 'La URL del video es obligatoria.'];
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['ok' => false, 'error' => 'La URL no es válida.'];
        }
        return ['ok' => true, 'url' => $url];
    }
}

switch ($accion) {

    // ── CREAR ─────────────────────────────────────────────────────────────
    case 'crear_video':
        $titulo      = trim($_POST['titulo']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria   = trim($_POST['categoria']   ?? 'general');

        if (empty($titulo)) {
            header('Location: ' . $redirect . '&error=' . urlencode('El título es obligatorio.'));
            exit;
        }

        $urlResult = resolverVideoUrl('crear_video');
        if (!$urlResult['ok']) {
            header('Location: ' . $redirect . '&error=' . urlencode($urlResult['error']));
            exit;
        }

        $video_url = $urlResult['url'];
        $stmt = $conn->prepare("
            INSERT INTO videos (titulo, descripcion, video_url, categoria, id_autor)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $titulo, $descripcion, $video_url, $categoria, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header('Location: ' . $redirect . '&ok=' . urlencode('✅ Video publicado correctamente.'));
        } else {
            header('Location: ' . $redirect . '&error=' . urlencode('Error al guardar el video: ' . $conn->error));
        }
        break;

    // ── ACTUALIZAR ────────────────────────────────────────────────────────
    case 'actualizar_video':
        $id          = (int)($_POST['id_video']   ?? 0);
        $titulo      = trim($_POST['titulo']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria   = trim($_POST['categoria']   ?? 'general');

        if (empty($titulo)) {
            header('Location: ' . $redirect . '&error=' . urlencode('El título es obligatorio.'));
            exit;
        }

        // Si sube archivo nuevo o cambia URL, actualizar; si no, conservar la actual
        $fuente = $_POST['fuente'] ?? 'youtube';
        $cambiarVideo = ($fuente === 'local' && isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK)
                     || ($fuente === 'youtube' && !empty(trim($_POST['video_url'] ?? '')));

        if ($cambiarVideo) {
            $urlResult = resolverVideoUrl('actualizar_video');
            if (!$urlResult['ok']) {
                header('Location: ' . $redirect . '&error=' . urlencode($urlResult['error']));
                exit;
            }
            $video_url = $urlResult['url'];
            $stmt = $conn->prepare("
                UPDATE videos SET titulo=?, descripcion=?, video_url=?, categoria=?
                WHERE id=? AND id_autor=?
            ");
            $stmt->bind_param("ssssii", $titulo, $descripcion, $video_url, $categoria, $id, $_SESSION['user_id']);
        } else {
            // Solo actualizar metadatos
            $stmt = $conn->prepare("
                UPDATE videos SET titulo=?, descripcion=?, categoria=?
                WHERE id=? AND id_autor=?
            ");
            $stmt->bind_param("sssii", $titulo, $descripcion, $categoria, $id, $_SESSION['user_id']);
        }

        if ($stmt->execute()) {
            header('Location: ' . $redirect . '&ok=' . urlencode('✅ Video actualizado correctamente.'));
        } else {
            header('Location: ' . $redirect . '&error=' . urlencode('Error al actualizar: ' . $conn->error));
        }
        break;

    // ── ELIMINAR ──────────────────────────────────────────────────────────
    case 'eliminar_video':
        $id = (int)($_POST['id_video'] ?? 0);

        // Si es video local, eliminar el archivo físico
        $check = $conn->prepare("SELECT video_url FROM videos WHERE id=? AND id_autor=?");
        $check->bind_param("ii", $id, $_SESSION['user_id']);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();

        if ($row && !preg_match('/youtube\.com|youtu\.be/', $row['video_url'])) {
            $filePath = __DIR__ . '/../' . $row['video_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $conn->prepare("DELETE FROM videos WHERE id=? AND id_autor=?");
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header('Location: ' . $redirect . '&ok=' . urlencode('✅ Video eliminado correctamente.'));
        } else {
            header('Location: ' . $redirect . '&error=' . urlencode('Error al eliminar.'));
        }
        break;

    default:
        header('Location: ' . $redirect);
        break;
}

$conn->close();
exit;