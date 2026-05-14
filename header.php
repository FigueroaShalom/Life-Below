<?php
$current_section = $_GET['section'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo ucfirst(str_replace('_', ' ', $current_section)); ?></title>
    <meta name="description" content="Explora el océano con HYDRON: noticias, artículos, mapas dinámicos y galerías sobre conservación y vida marina.">
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - <?php echo ucfirst(str_replace('_', ' ', $current_section)); ?>">
    <meta property="og:description" content="Explora el océano con HYDRON: conservación, mapas y vida marina.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>uploads/logo.svg">
    <meta property="og:type" content="website">
    <?php if ($current_section === 'dashboard'): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    <link rel="stylesheet" href="style.css?v=1.1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="uploads/logooo.jpg">
</head>
<body>

<header class="hy-header">
    <div class="hy-nav-inner">
        <a href="index.php?section=inicio" class="hy-logo">
            <img src="uploads/logooo.jpg" alt="<?php echo SITE_NAME; ?>" class="hy-logo-img stylized-logo">
        </a>

        <nav class="hy-nav">
            <a href="index.php?section=inicio"   class="hy-nav-link <?php echo $current_section==='inicio'   ? 'hy-active':'' ?>">Inicio</a>
            <a href="index.php?section=watch"    class="hy-nav-link <?php echo $current_section==='watch'    ? 'hy-active':'' ?>">Videos</a>
            <a href="index.php?section=mapa_dinamico" class="hy-nav-link <?php echo $current_section==='mapa_dinamico' ? 'hy-active':'' ?>">Ocean Map</a>
            <a href="index.php?section=galeria"  class="hy-nav-link <?php echo $current_section==='galeria'  ? 'hy-active':'' ?>">Galería</a>
            <a href="index.php?section=noticias" class="hy-nav-link <?php echo $current_section==='noticias' ? 'hy-active':'' ?>">Noticias</a>
            <a href="index.php?section=articulos"class="hy-nav-link <?php echo $current_section==='articulos'? 'hy-active':'' ?>">Artículos</a>
        </nav>

        <div class="hy-nav-actions">
            <?php if (isset($_SESSION['user'])): 
                require_once __DIR__ . '/database/Conexion_base.php';
                $stmt_head = $conn->prepare("SELECT foto FROM usuarios WHERE id = ?");
                $stmt_head->bind_param("i", $_SESSION['id']);
                $stmt_head->execute();
                $res_head = $stmt_head->get_result()->fetch_assoc();
                $foto_head = (!empty($res_head['foto'])) ? $res_head['foto'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
            ?>
                <div class="hy-profile-dropdown">
                    <button class="hy-profile-trigger" id="profileTrigger" title="Mi Cuenta">
                        <img src="<?php echo htmlspecialchars($foto_head); ?>" alt="Perfil" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid #eee;">
                    </button>
                    <div class="hy-dropdown-menu" id="profileMenu">
                        <div class="hy-dropdown-header">
                            <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>
                        </div>
                        <a href="index.php?section=dashboard">⚙️ Mi Perfil</a>
                        <hr>
                        <a href="index.php?logout=1" class="logout">Logout</a>
                    </div>
                </div>
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
        <a href="index.php?section=mapa_dinamico">Ocean Map</a>
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