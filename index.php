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