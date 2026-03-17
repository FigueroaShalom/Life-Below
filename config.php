<?php
session_start();

// Configuración del sitio
define('SITE_NAME', 'HYDRON');
define('SITE_URL', 'http://localhost/HYDRON/');
define('DATA_DIR', __DIR__ . '/data/');
define('POSTS_DIR', DATA_DIR . 'posts/');
define('COMMENTS_DIR', DATA_DIR . 'comments/');
define('LIKES_DIR', DATA_DIR . 'likes/');
define('UPLOADS_DIR', __DIR__ . '/uploads/');

// Usuario administrador
define('ADMIN_USER', 'isis');
define('ADMIN_PASS', 'oceano123');

// Crear directorios si no existen
$directories = [DATA_DIR, POSTS_DIR, COMMENTS_DIR, LIKES_DIR, UPLOADS_DIR];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Incluir funciones
require_once 'funciones.php';
?>