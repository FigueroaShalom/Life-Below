<?php


if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/Conexion_base.php';

header('Content-Type: application/json');

// ── Función helper: responder JSON y salir ────────────────────────────────────
function responder(bool $ok, string $mensaje): void {
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    exit;
}

// ── Verificar sesión ──────────────────────────────────────────────────────────
if (!isset($_SESSION['id'])) {
    responder(false, 'Sesión no válida. Inicia sesión nuevamente.');
}

$accion    = $_POST['accion'] ?? '';
$id_autor  = (int)$_SESSION['id'];

// ── Helper: subir archivo de video ────────────────────────────────────────────
function subirVideoArchivo(string $file_input): array {
    if (!isset($_FILES[$file_input]) || $_FILES[$file_input]['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Error al recibir el archivo.'];
    }

    $file       = $_FILES[$file_input];
    $maxSize    = 200 * 1024 * 1024; // 200 MB
    $allowed    = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'];
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

    $filename = uniqid('vid_', true) . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['ok' => false, 'error' => 'No se pudo guardar el archivo en el servidor.'];
    }

    return ['ok' => true, 'url' => 'uploads/videos/' . $filename];
}

// ── Helper: resolver URL del video ───────────────────────────────────────────
function resolverVideoUrl(): array {
    $fuente = $_POST['fuente'] ?? 'youtube';

    if ($fuente === 'local') {
        $result = subirVideoArchivo('video_file');
        return $result['ok']
            ? ['ok' => true,  'url' => $result['url']]
            : ['ok' => false, 'error' => $result['error']];
    }

    $url = trim($_POST['video_url'] ?? '');
    if (empty($url)) {
        return ['ok' => false, 'error' => 'La URL del video es obligatoria.'];
    }
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return ['ok' => false, 'error' => 'La URL no es válida.'];
    }
    return ['ok' => true, 'url' => $url];
}

// ── Switch de acciones ────────────────────────────────────────────────────────
switch ($accion) {

    // ── CREAR VIDEO ───────────────────────────────────────────────────────
    case 'crear_video':
        $titulo      = trim($_POST['titulo']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria   = trim($_POST['categoria']   ?? 'general');

        if (empty($titulo)) {
            responder(false, 'El título es obligatorio.');
        }

        $urlResult = resolverVideoUrl();
        if (!$urlResult['ok']) {
            responder(false, $urlResult['error']);
        }

        $video_url = $urlResult['url'];
        $stmt = $conn->prepare("
            INSERT INTO videos (titulo, descripcion, video_url, categoria, id_autor)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $titulo, $descripcion, $video_url, $categoria, $id_autor);

        $stmt->execute()
            ? responder(true,  '✅ Video publicado correctamente.')
            : responder(false, 'Error al guardar el video: ' . $conn->error);
        break;

    // ── ACTUALIZAR VIDEO ──────────────────────────────────────────────────
    case 'actualizar_video':
        $id          = (int)($_POST['id_video']   ?? 0);
        $titulo      = trim($_POST['titulo']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria   = trim($_POST['categoria']   ?? 'general');

        if (empty($titulo)) {
            responder(false, 'El título es obligatorio.');
        }

        $fuente       = $_POST['fuente'] ?? 'youtube';
        $cambiarVideo = ($fuente === 'local'   && isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK)
                     || ($fuente === 'youtube' && !empty(trim($_POST['video_url'] ?? '')));

        if ($cambiarVideo) {
            $urlResult = resolverVideoUrl();
            if (!$urlResult['ok']) {
                responder(false, $urlResult['error']);
            }
            $video_url = $urlResult['url'];
            $stmt = $conn->prepare("
                UPDATE videos SET titulo=?, descripcion=?, video_url=?, categoria=?
                WHERE id=? AND id_autor=?
            ");
            $stmt->bind_param("ssssii", $titulo, $descripcion, $video_url, $categoria, $id, $id_autor);
        } else {
            $stmt = $conn->prepare("
                UPDATE videos SET titulo=?, descripcion=?, categoria=?
                WHERE id=? AND id_autor=?
            ");
            $stmt->bind_param("sssii", $titulo, $descripcion, $categoria, $id, $id_autor);
        }

        $stmt->execute()
            ? responder(true,  '✅ Video actualizado correctamente.')
            : responder(false, 'Error al actualizar: ' . $conn->error);
        break;

    // ── ELIMINAR VIDEO ────────────────────────────────────────────────────
    case 'eliminar_video':
        $id = (int)($_POST['id_video'] ?? 0);

        // Eliminar archivo físico si es local
        $check = $conn->prepare("SELECT video_url FROM videos WHERE id=? AND id_autor=?");
        $check->bind_param("ii", $id, $id_autor);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();

        if ($row && !preg_match('/youtube\.com|youtu\.be/', $row['video_url'])) {
            $filePath = __DIR__ . '/../' . $row['video_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $conn->prepare("DELETE FROM videos WHERE id=? AND id_autor=?");
        $stmt->bind_param("ii", $id, $id_autor);

        $stmt->execute()
            ? responder(true,  '✅ Video eliminado correctamente.')
            : responder(false, 'Error al eliminar el video.');
        break;

    default:
        responder(false, 'Acción no reconocida.');
}

$conn->close();