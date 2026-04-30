<?php
session_start();

// Headers de seguridad HTTP
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

define('SITE_NAME', 'LIFEBELOW');
define('SITE_URL', 'http://localhost/HYDRON/');
define('UPLOADS_DIR', __DIR__ . '/uploads/');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// Crear carpeta uploads si no existe
if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0777, true);
}
?>