<?php
$current_section = $_GET['section'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Vida Submarina · Ciencia Oceánica</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="glass-nav" id="glassNav">
    <div class="nav-container">
        <div class="logo-area">
            <i class="fas fa-fish logo-icon"></i>
            <span class="logo-text">LIFEBELOW</span>
        </div>
        <div class="nav-links">
            <a href="index.php?section=inicio" class="nav-link <?php echo $current_section === 'inicio' ? 'hy-active' : ''; ?>"><i class="fas fa-home"></i> Inicio</a>
            <a href="index.php?section=watch" class="nav-link <?php echo $current_section === 'watch' ? 'hy-active' : ''; ?>"><i class="fas fa-video"></i> Videos</a>
            <a href="index.php?section=mapa_dinamico" class="nav-link <?php echo $current_section === 'mapa_dinamico' ? 'hy-active' : ''; ?>"><i class="fas fa-map"></i> Mapa</a>
            <a href="index.php?section=galeria" class="nav-link <?php echo $current_section === 'galeria' ? 'hy-active' : ''; ?>"><i class="fas fa-images"></i> Galería</a>
            <a href="index.php?section=noticias" class="nav-link <?php echo $current_section === 'noticias' ? 'hy-active' : ''; ?>"><i class="fas fa-newspaper"></i> Noticias</a>
            <a href="index.php?section=articulos" class="nav-link <?php echo $current_section === 'articulos' ? 'hy-active' : ''; ?>"><i class="fas fa-book-open"></i> Artículos</a>
            
            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-badge"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user']); ?></div>
                <a href="index.php?logout=1" class="btn-outline-glass"><i class="fas fa-sign-out-alt"></i> Salir</a>
            <?php else: ?>
                <a href="index.php?section=login" class="btn-outline-glass"><i class="fas fa-user"></i> Iniciar sesión</a>
                <a href="index.php?section=registro" class="btn-solid"><i class="fas fa-user-plus"></i> Regístrate</a>
            <?php endif; ?>
        </div>
        <button class="hy-hamburger" id="hyHamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>
    <div class="hy-mobile-menu" id="hyMobileMenu">
        <a href="index.php?section=inicio">Inicio</a>
        <a href="index.php?section=watch">Videos</a>
        <a href="index.php?section=mapa_dinamico">Mapa</a>
        <a href="index.php?section=galeria">Galería</a>
        <a href="index.php?section=noticias">Noticias</a>
        <a href="index.php?section=articulos">Artículos</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="index.php?logout=1">Salir</a>
        <?php else: ?>
            <a href="index.php?section=login">Iniciar sesión</a>
            <a href="index.php?section=registro">Regístrate</a>
        <?php endif; ?>
    </div>
</header>

<main class="hy-main">