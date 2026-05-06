<?php
session_start();

// Cargar variables de entorno
$env_path = __DIR__ . '/.env';
$env_vars = [];
if (file_exists($env_path)) {
    $env_vars = parse_ini_file($env_path);
}

// Headers de seguridad HTTP
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

define('SITE_NAME', 'HYDRON');
define('SITE_URL', 'http://localhost/HYDRON/'); // Esto podría ir al .env luego
define('UPLOADS_DIR', __DIR__ . '/uploads/');
define('ADMIN_USER', $env_vars['ADMIN_USER'] ?? 'admin');
define('ADMIN_PASS', $env_vars['ADMIN_PASS'] ?? 'admin123');

// Crear carpeta uploads si no existe
if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0777, true);
}
?>