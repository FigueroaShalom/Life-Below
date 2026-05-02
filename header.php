<?php
$current_section = $_GET['section'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo SITE_NAME; ?> - Vida Submarina · Ciencia Oceánica</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ========== RESET & VARIABLES ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --navy: #0a2b3b;
            --ocean: #1d6f8c;
            --aqua: #2fa4c4;
            --cyan: #48cae4;
            --glass-bg: rgba(10, 43, 59, 0.85);
            --glass-border: rgba(72, 202, 228, 0.3);
            --transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #eef5fa 0%, #d9e9f2 100%);
            color: #102b36;
            overflow-x: hidden;
        }

        /* ----- HEADER GLASS (flotante) ----- */
        .glass-nav {
            position: sticky;
            top: 20px;
            z-index: 1000;
            max-width: 1300px;
            margin: 0 auto;
            width: calc(100% - 2rem);
            background: var(--glass-bg);
            backdrop-filter: blur(14px) saturate(180%);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 60px;
            border: 1px solid var(--glass-border);
            transition: var(--transition);
        }
        .glass-nav.scrolled {
            top: 12px;
            background: rgba(8, 35, 48, 0.95);
            backdrop-filter: blur(18px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 2rem;
            flex-wrap: wrap;
        }
        .logo-area { display: flex; align-items: center; gap: 0.7rem; }
        .logo-icon { font-size: 1.8rem; color: var(--cyan); filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
        .logo-text { 
            font-weight: 800; 
            font-size: 1.4rem; 
            background: linear-gradient(135deg, #fff, #b9e6ff); 
            -webkit-background-clip: text; 
            background-clip: text; 
            color: transparent; 
        }
        .nav-links { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        .nav-link {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 40px;
            transition: var(--transition);
            font-size: 0.9rem;
        }
        .nav-link i { margin-right: 6px; }
        .nav-link:hover, .hy-active { 
            background: rgba(72, 202, 228, 0.25); 
            color: white; 
            transform: translateY(-2px); 
        }
        .btn-outline-glass {
            border: 1.5px solid rgba(72, 202, 228, 0.8);
            background: transparent;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.85rem;
        }
        .btn-outline-glass:hover { background: var(--aqua); transform: scale(1.02); border-color: transparent; }
        .btn-solid {
            background: linear-gradient(105deg, #2fa4c4, #1c7ea0);
            padding: 0.55rem 1.4rem;
            border-radius: 40px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.85rem;
        }
        .btn-solid:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(31,131,163,0.4); }
        .user-badge {
            background: rgba(0,0,0,0.3);
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
        }
        .hy-hamburger { display: none; flex-direction: column; gap: 5px; background: none; border: none; cursor: pointer; }
        .hy-hamburger span { width: 24px; height: 2px; background: white; transition: 0.3s; border-radius: 2px; }
        .hy-hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .hy-hamburger.open span:nth-child(2) { opacity: 0; }
        .hy-hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }
        .hy-mobile-menu { display: none; flex-direction: column; background: #0a2b3b; padding: 1rem 2rem; border-top: 1px solid rgba(255,255,255,0.1); border-radius: 0 0 30px 30px; }
        .hy-mobile-menu.open { display: flex; }
        .hy-mobile-menu a { color: white; text-decoration: none; padding: 0.6rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); font-weight: 500; }
        .hy-mobile-menu a:last-child { border-bottom: none; }

        /* ----- MAIN CONTENT ----- */
        .hy-main { min-height: calc(100vh - 80px); }

        /* ----- HERO SECTION ----- */
        .hero {
            max-width: 1300px;
            margin: 2rem auto;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 3rem;
            flex-wrap: wrap;
        }
        .hero-content { flex: 1.2; min-width: 280px; }
        .hero-badge {
            display: inline-block;
            background: rgba(31,112,127,0.15);
            backdrop-filter: blur(4px);
            padding: 0.3rem 1.2rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #166b82;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(47,164,196,0.3);
        }
        .hero h1 { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; line-height: 1.2; color: #0a2b3b; margin-bottom: 1rem; }
        .text-gradient { background: linear-gradient(125deg, #1d6f8c, #0f98b5, #2fa4c4); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .hero p { font-size: 1.1rem; color: #1b4e60; margin-bottom: 2rem; max-width: 550px; line-height: 1.5; }
        .hero-buttons { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-primary {
            background: linear-gradient(95deg, #1f7f9c, #126b85);
            padding: 0.9rem 2rem;
            border-radius: 50px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover { transform: translateY(-3px); background: linear-gradient(95deg, #3099b9, #1882a0); box-shadow: 0 10px 25px rgba(31,131,163,0.3); }
        .btn-secondary {
            background: transparent;
            border: 2px solid #2589ad;
            padding: 0.85rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            color: #126b85;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-secondary:hover { background: rgba(37,137,173,0.1); transform: translateY(-2px); border-color: #1f7f9c; }
        .hero-visual { flex: 1; display: flex; justify-content: center; }
        .hero-card-glass {
            background: rgba(255,255,255,0.3);
            backdrop-filter: blur(12px);
            border-radius: 2rem;
            padding: 2rem;
            text-align: center;
            max-width: 320px;
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.2);
        }
        .hero-card-glass i { font-size: 3rem; color: #1a8dae; margin-bottom: 1rem; }
        .hero-card-glass h3 { font-size: 1.3rem; margin-bottom: 0.5rem; color: #0a2b3b; }
        .stat { margin-top: 1rem; font-weight: 600; color: #0f5a74; }

        /* ----- SECCIÓN NOTICIAS + CARRUSEL ----- */
        .news-section { max-width: 1300px; margin: 2rem auto; padding: 0 2rem; }
        .section-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.8rem; flex-wrap: wrap; }
        .section-header h2 { font-size: 1.8rem; font-weight: 800; color: #0f3b4a; }
        .section-header p { color: #2f7b94; font-weight: 500; }
        .carousel-container { 
            position: relative; 
            background: rgba(255,255,255,0.5); 
            backdrop-filter: blur(4px); 
            border-radius: 2rem; 
            padding: 1rem; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }
        .carousel-track { 
            display: flex; 
            overflow-x: auto; 
            gap: 1.5rem; 
            padding: 1rem 0.5rem; 
            scroll-behavior: smooth; 
            scrollbar-width: thin;
            scroll-snap-type: x mandatory;
        }
        .carousel-track::-webkit-scrollbar { height: 6px; }
        .carousel-track::-webkit-scrollbar-track { background: #cbdde6; border-radius: 10px; }
        .carousel-track::-webkit-scrollbar-thumb { background: #2f94b0; border-radius: 10px; }
        .news-card { 
            flex: 0 0 300px; 
            scroll-snap-align: start;
            background: white; 
            border-radius: 1.5rem; 
            overflow: hidden; 
            transition: var(--transition); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .news-card:hover { transform: translateY(-8px); box-shadow: 0 25px 35px -12px rgba(0,0,0,0.2); }
        .news-img { height: 170px; background-size: cover; background-position: center; background-color: #aad4e6; }
        .news-content { padding: 1.2rem; }
        .news-source { font-size: 0.7rem; font-weight: 800; color: #1d6f8c; text-transform: uppercase; letter-spacing: 0.5px; }
        .news-title { font-size: 1rem; font-weight: 800; margin: 0.5rem 0; line-height: 1.3; color: #0a2b3b; }
        .news-desc { font-size: 0.85rem; color: #2e5f70; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5; }
        .read-link { text-decoration: none; font-weight: 700; font-size: 0.8rem; color: #1f7f9c; transition: 0.2s; display: inline-flex; align-items: center; gap: 4px; }
        .read-link:hover { gap: 8px; color: #0f5a74; }
        .ctrl-btn {
            background: #ffffffcc;
            backdrop-filter: blur(8px);
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 60px;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 1rem;
            font-size: 1.2rem;
            color: #0a2b3b;
        }
        .ctrl-btn:hover { background: #2fa4c4; color: white; transform: scale(1.05); }
        .carousel-controls { display: flex; justify-content: flex-end; gap: 1rem; }
        .loading-placeholder { text-align: center; padding: 2.5rem; color: #1d6f8c; font-weight: 600; }

        /* ----- EXPLORE GRID ----- */
        .explore-section { max-width: 1300px; margin: 4rem auto; padding: 0 2rem; }
        .explore-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.8rem; margin-top: 2rem; }
        .explore-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(6px);
            border-radius: 1.5rem;
            padding: 1.8rem 1rem;
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(47,164,196,0.3);
            cursor: pointer;
        }
        .explore-card:hover { transform: translateY(-6px); background: white; border-color: #2fa4c4; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .explore-card i { font-size: 2.5rem; color: #1882a0; margin-bottom: 1rem; }
        .explore-card h3 { font-weight: 800; margin-bottom: 0.5rem; color: #0a2b3b; }
        .explore-card p { font-size: 0.85rem; color: #2e5f70; }

        /* ----- FOOTER ----- */
        .hy-footer { background: #0b2f3b; margin-top: 4rem; padding: 3rem 2rem 1rem; color: #c7e2ec; }
        .hy-footer-inner { max-width: 1300px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 2rem; }
        .hy-footer-brand p { font-size: 0.85rem; margin-top: 0.5rem; opacity: 0.8; line-height: 1.6; }
        .hy-footer-social { display: flex; gap: 1rem; margin-top: 1rem; }
        .hy-footer-social a { color: white; font-size: 1.2rem; transition: 0.2s; }
        .hy-footer-social a:hover { transform: translateY(-3px); color: var(--cyan); }
        .hy-footer-col h4 { color: white; margin-bottom: 1rem; font-size: 0.9rem; font-weight: 700; letter-spacing: 1px; }
        .hy-footer-col a { display: block; color: #c7e2ec; text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; transition: 0.2s; }
        .hy-footer-col a:hover { color: var(--cyan); transform: translateX(3px); }
        .hy-ods-badge { display: inline-block; margin-top: 1rem; background: rgba(0,180,220,0.2); padding: 5px 12px; border-radius: 40px; font-size: 0.7rem; font-weight: 600; }
        .hy-footer-bottom { text-align: center; padding-top: 2rem; margin-top: 2rem; border-top: 1px solid #216277; font-size: 0.75rem; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; }

        /* ----- RESPONSIVE ----- */
        @media (max-width: 850px) {
            .nav-links { display: none; }
            .hy-hamburger { display: flex; }
            .hero { flex-direction: column; text-align: center; }
            .hero-buttons { justify-content: center; }
            .hero p { margin-left: auto; margin-right: auto; }
            .hy-footer-inner { grid-template-columns: 1fr 1fr; gap: 1.5rem; }
            .carousel-controls { justify-content: center; }
            .hero-visual { margin-top: 1rem; }
        }
        @media (max-width: 560px) {
            .hy-footer-inner { grid-template-columns: 1fr; text-align: center; }
            .carousel-track { gap: 1rem; }
            .news-card { flex: 0 0 260px; }
            .hero-card-glass { margin-top: 1rem; }
            .hy-footer-bottom { flex-direction: column; text-align: center; }
            .section-header { flex-direction: column; gap: 0.5rem; text-align: center; }
        }
    </style>
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