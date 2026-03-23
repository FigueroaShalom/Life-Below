<?php
session_start();

define('SITE_NAME', 'HYDRON');
define('SITE_URL', 'http://localhost/HYDRON/');
define('UPLOADS_DIR', __DIR__ . '/uploads/');

// Crear carpeta uploads si no existe
if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0777, true);
}
?>