<?php
// index.php
require_once 'config.php';

// Manejo de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php?section=inicio');
    exit;
}

// Secciones permitidas (Whitelist para seguridad)
$secciones_permitidas = [
    'inicio', 'watch', 'mapa_dinamico', 'galeria', 'noticias', 'articulos', 'login', 'registro', 'dashboard'
];

$current_section = $_GET['section'] ?? 'inicio';

// Validar que la sección exista en el whitelist
if (!in_array($current_section, $secciones_permitidas)) {
    $current_section = '404';
}

$section_file = 'INCLUDES/' . $current_section . '.php';

include 'header.php';

if ($current_section === '404') {
    echo '<div style="text-align:center; padding: 5rem 2rem; min-height: 60vh; display:flex; flex-direction:column; justify-content:center; align-items:center;">';
    echo '<h1 style="font-size: 5rem; color: #0077be; font-family: \'Nunito\', sans-serif; margin-bottom: 0;">404</h1>';
    echo '<h2 style="font-size: 1.5rem; color: #001828; font-family: \'Nunito\', sans-serif;">Página no encontrada</h2>';
    echo '<p style="color: #5a7a9a; font-family: \'Nunito\', sans-serif; margin-top: 1rem;">Parece que te has perdido en las profundidades del océano.</p>';
    echo '<a href="index.php?section=inicio" style="margin-top: 2rem; padding: 10px 24px; background: #0077be; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; font-family: \'Nunito\', sans-serif; transition: background 0.3s;">Volver a la superficie</a>';
    echo '</div>';
} elseif (file_exists($section_file)) {
    include $section_file;
} else {
    echo '<h1 style="text-align:center; padding: 5rem;">Error interno: archivo no encontrado</h1>';
}

include 'footer.php';
?>