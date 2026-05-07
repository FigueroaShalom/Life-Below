<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'Conexion_base.php';

if (!isset($_SESSION['id'])) {
    exit('Acceso denegado');
}

$id_usuario = $_SESSION['id'];
$accion = $_POST['accion'] ?? '';

// --- 1. ELIMINAR ---
if ($accion === 'eliminar_articulo') {
    $id_post = (int)$_POST['id_publicacion'];
    $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id = ? AND id_autor = ?");
    $stmt->bind_param("ii", $id_post, $id_usuario);

    if ($stmt->execute()) {
        echo 'deleted';
    } else {
        echo 'error: ' . $conn->error;
    }
    exit;
}

// --- 2. EDITAR ---
elseif ($accion === 'editar_articulo') {
    $id_post   = (int)$_POST['id_publicacion'];
    $titulo    = trim($_POST['titulo']    ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $imagen    = trim($_POST['imagen']    ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    $stmt = $conn->prepare("UPDATE publicaciones SET titulo=?, categoria=?, imagen=?, contenido=? WHERE id=? AND id_autor=?");
    $stmt->bind_param("ssssii", $titulo, $categoria, $imagen, $contenido, $id_post, $id_usuario);

    if ($stmt->execute()) {
        echo 'Cambios guardados';
    } else {
        echo 'error: ' . $conn->error;
    }
    exit;
}

// --- 3. CREAR ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $imagen    = trim($_POST['imagen']    ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if (empty($titulo) || empty($contenido)) {
        echo 'error: título y contenido son obligatorios';
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO publicaciones (titulo, categoria, imagen, contenido, id_autor, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssi", $titulo, $categoria, $imagen, $contenido, $id_usuario);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $conn->error;
    }
    exit;
}