<?php
// Función para obtener todas las publicaciones
function getPosts() {
    $posts = [];
    if (file_exists(POSTS_DIR)) {
        $files = glob(POSTS_DIR . '*.txt');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $post = unserialize($content);
            if ($post) {
                $post['filename'] = basename($file);
                // Obtener likes
                $likes_file = LIKES_DIR . basename($file, '.txt') . '_likes.txt';
                if (file_exists($likes_file)) {
                    $likes = unserialize(file_get_contents($likes_file));
                    $post['likes'] = $likes['count'] ?? 0;
                } else {
                    $post['likes'] = 0;
                }
                $posts[] = $post;
            }
        }
        // Ordenar por fecha (más reciente primero)
        usort($posts, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
    }
    return $posts;
}

// Función para obtener una publicación específica
function getPost($filename) {
    $file_path = POSTS_DIR . $filename;
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        return unserialize($content);
    }
    return null;
}

// Función para guardar una publicación
function savePost($post_data, $filename = null) {
    if ($filename === null) {
        $filename = 'post_' . date('Ymd_His') . '_' . uniqid() . '.txt';
    }
    $file_path = POSTS_DIR . $filename;
    return file_put_contents($file_path, serialize($post_data));
}

// Función para eliminar una publicación
function deletePost($filename) {
    $file_path = POSTS_DIR . $filename;
    if (file_exists($file_path)) {
        unlink($file_path);
        // Eliminar también likes y comentarios asociados
        $likes_file = LIKES_DIR . basename($filename, '.txt') . '_likes.txt';
        if (file_exists($likes_file)) unlink($likes_file);
        $comments_file = COMMENTS_DIR . basename($filename, '.txt') . '_comments.txt';
        if (file_exists($comments_file)) unlink($comments_file);
        return true;
    }
    return false;
}

// Función para obtener comentarios
function getComments($post_filename) {
    $comments_file = COMMENTS_DIR . basename($post_filename, '.txt') . '_comments.txt';
    if (file_exists($comments_file)) {
        return unserialize(file_get_contents($comments_file)) ?? [];
    }
    return [];
}

// Función para agregar un comentario
function addComment($post_filename, $user, $comment) {
    $comments_file = COMMENTS_DIR . basename($post_filename, '.txt') . '_comments.txt';
    if (file_exists($comments_file)) {
        $comments = unserialize(file_get_contents($comments_file));
    } else {
        $comments = [];
    }
    
    $comments[] = [
        'user' => $user,
        'comment' => htmlspecialchars($comment),
        'date' => date('Y-m-d H:i:s')
    ];
    
    return file_put_contents($comments_file, serialize($comments));
}

// Función para manejar likes
function handleLike($post_filename, $username) {
    $likes_file = LIKES_DIR . basename($post_filename, '.txt') . '_likes.txt';
    $user_likes_file = LIKES_DIR . 'user_likes.txt';
    
    // Cargar likes del post
    if (file_exists($likes_file)) {
        $likes = unserialize(file_get_contents($likes_file));
    } else {
        $likes = ['count' => 0, 'users' => []];
    }
    
    // Cargar likes de usuarios
    if (file_exists($user_likes_file)) {
        $user_likes = unserialize(file_get_contents($user_likes_file));
    } else {
        $user_likes = [];
    }
    
    $like_key = $username . '_' . $post_filename;
    
    if (!in_array($like_key, $user_likes)) {
        // Agregar like
        $likes['count']++;
        $likes['users'][] = $username;
        $user_likes[] = $like_key;
    } else {
        // Quitar like
        $likes['count']--;
        $likes['users'] = array_diff($likes['users'], [$username]);
        $user_likes = array_diff($user_likes, [$like_key]);
    }
    
    file_put_contents($likes_file, serialize($likes));
    file_put_contents($user_likes_file, serialize($user_likes));
    
    return $likes['count'];
}
?>