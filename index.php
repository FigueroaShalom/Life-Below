<?php
require_once 'config.php';

// Manejo de login
if (isset($_POST['login'])) {
    if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
        $_SESSION['user'] = ADMIN_USER;
        header('Location: index.php?section=inicio');
        exit;
    }
}

// Manejo de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php?section=inicio');
    exit;
}

// Manejo de likes
if (isset($_POST['like_post']) && isset($_SESSION['user'])) {
    $post_id = $_POST['post_id'];
    $new_count = handleLike($post_id, $_SESSION['user']);
    header('Location: index.php?section=articulos&post=' . urlencode($post_id));
    exit;
}

// Manejo de comentarios
if (isset($_POST['add_comment']) && isset($_SESSION['user'])) {
    $post_id = $_POST['post_id'];
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        addComment($post_id, $_SESSION['user'], $comment);
    }
    header('Location: index.php?section=articulos&post=' . urlencode($post_id));
    exit;
}

// Manejo de creación/edición de publicaciones
$message = '';
$error = '';

if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_post'])) {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category = $_POST['category'] ?? 'general';
        $image = $_POST['image'] ?? '';
        
        if (empty($title) || empty($content)) {
            $error = 'El título y el contenido son obligatorios';
        } else {
            $post = [
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'image' => $image,
                'author' => $_SESSION['user'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if (savePost($post)) {
                $message = 'Publicación creada exitosamente';
            } else {
                $error = 'Error al crear la publicación';
            }
        }
    }
    
    if (isset($_POST['update_post']) && isset($_POST['post_id'])) {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category = $_POST['category'] ?? 'general';
        $image = $_POST['image'] ?? '';
        $filename = $_POST['post_id'];
        
        if (empty($title) || empty($content)) {
            $error = 'El título y el contenido son obligatorios';
        } else {
            $existing = getPost($filename);
            if ($existing) {
                $post = [
                    'title' => $title,
                    'content' => $content,
                    'category' => $category,
                    'image' => $image,
                    'author' => $existing['author'],
                    'created_at' => $existing['created_at'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if (savePost($post, $filename)) {
                    $message = 'Publicación actualizada';
                } else {
                    $error = 'Error al actualizar';
                }
            }
        }
    }
    
    if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
        if (deletePost($_POST['post_id'])) {
            $message = 'Publicación eliminada';
        } else {
            $error = 'Error al eliminar';
        }
    }
}

// Determinar qué sección incluir
$current_section = $_GET['section'] ?? 'inicio';
$section_file = 'includes/' . $current_section . '.php';

include 'header.php';

if (file_exists($section_file)) {
    include $section_file;
} else {
    echo '<h1>Página no encontrada</h1>';
}

include 'footer.php';
?>