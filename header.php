<?php
$current_section = $_GET['section'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Vida Marina</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<header class="hy-header">
    <div class="hy-nav-inner">
        <a href="index.php?section=inicio" class="hy-logo">
            <img src="uploads/logo.png" alt="<?php echo SITE_NAME; ?>" class="hy-logo-img">
        </a>

        <nav class="hy-nav">
            <a href="index.php?section=inicio"   class="hy-nav-link <?php echo $current_section==='inicio'   ? 'hy-active':'' ?>">Inicio</a>
            <a href="index.php?section=watch"    class="hy-nav-link <?php echo $current_section==='watch'    ? 'hy-active':'' ?>">▶ Watch</a>
            <a href="index.php?section=galeria"  class="hy-nav-link <?php echo $current_section==='galeria'  ? 'hy-active':'' ?>">Galería</a>
            <a href="index.php?section=noticias" class="hy-nav-link <?php echo $current_section==='noticias' ? 'hy-active':'' ?>">Noticias</a>
            <a href="index.php?section=articulos"class="hy-nav-link <?php echo $current_section==='articulos'? 'hy-active':'' ?>">Artículos</a>
        </nav>

        <div class="hy-nav-actions">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="hy-user-chip">👤 <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <a href="Perfil(dashboard)/perfil.php" class="hy-btn-outline <?php echo $current_section==='dashboard'?'hy-active':'' ?>">Dashboard</a>
                <a href="index.php?logout=1" class="hy-btn-solid">Salir</a>
            <?php else: ?>
                <a href="index.php?section=login"    class="hy-btn-outline">Iniciar sesión</a>
                <a href="index.php?section=registro" class="hy-btn-solid">Regístrate</a>
            <?php endif; ?>
        </div>

        <!-- Hamburger mobile -->
        <button class="hy-hamburger" id="hyHamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Mobile menu -->
    <div class="hy-mobile-menu" id="hyMobileMenu">
        <a href="index.php?section=inicio">Inicio</a>
        <a href="index.php?section=watch">▶ Watch</a>
        <a href="index.php?section=galeria">Galería</a>
        <a href="index.php?section=noticias">Noticias</a>
        <a href="index.php?section=articulos">Artículos</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="index.php?section=dashboard">Dashboard</a>
            <a href="index.php?logout=1">Salir</a>
        <?php else: ?>
            <a href="index.php?section=login">Iniciar sesión</a>
            <a href="index.php?section=registro">Regístrate</a>
        <?php endif; ?>
    </div>
</header>

<main class="hy-main">