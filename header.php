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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="uploads/logooo.jpg">
    
    <!-- Theme script to prevent flash -->
    <script>
        (function() {
            const isLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
            const savedTheme = isLoggedIn ? (localStorage.getItem('theme') || 'dark') : 'dark';
            
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
                document.addEventListener('DOMContentLoaded', () => document.body.classList.add('dark-mode'));
            } else {
                document.documentElement.classList.remove('dark-mode');
                document.addEventListener('DOMContentLoaded', () => document.body.classList.remove('dark-mode'));
            }
        })();
    </script>
    
    <style>
        .hy-header {
            background: var(--header-blur-bg) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--header-border) !important;
            transition: all 0.3s ease;
        }
        
       .hy-header.scrolled {
            background: var(--header-scroll-bg) !important; 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--header-border) !important;
            box-shadow: 0 4px 30px rgba(0, 40, 80, 0.05);
        }

        .hy-nav-link {
            color: var(--header-text) !important; 
        }
        
        .hy-btn-outline, .hy-btn-solid {
            font-family: var(--font) !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            border-radius: 50px !important;
            transition: all 0.22s ease-in-out !important;
            white-space: nowrap !important;
        }
        
        .hy-btn-outline {
            padding: 8px 18px !important;
            font-size: 0.85rem !important;
            background: transparent !important;
            border: 1.5px solid var(--ocean) !important;
            color: var(--ocean) !important;
        }
        
        .hy-btn-outline:hover {
            background: var(--ocean) !important;
            color: #fff !important;
            border-color: var(--ocean) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0,119,190,0.3) !important;
        }
        
        .hy-btn-solid {
            padding: 8px 20px !important;
            font-size: 0.85rem !important;
            background: linear-gradient(135deg, var(--ocean), var(--teal)) !important;
            border: 1.5px solid transparent !important;
            color: #fff !important;
            box-shadow: 0 3px 10px rgba(0,119,190,0.2) !important;
        }
        
        .hy-btn-solid:hover {
            background: linear-gradient(135deg, var(--teal), var(--cyan)) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 5px 16px rgba(0,154,170,0.4) !important;
        }

        .hy-dropdown-menu, .hy-mobile-menu {
            background: var(--header-menu-bg) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--header-border);
            box-shadow: 0 8px 32px rgba(0, 40, 80, 0.1);
        }
        .hy-mobile-menu a, .hy-dropdown-header, .hy-dropdown-menu a {
            color: var(--header-text) !important;
        }
        .hy-hamburger span {
            background: var(--header-text) !important; 
        }
    </style>
</head>
<body>

<header class="hy-header">
    <div class="hy-nav-inner">
        <a href="index.php?section=inicio" class="hy-logo">
            <img src="uploads/logooo.jpg" alt="<?php echo SITE_NAME; ?>" class="hy-logo-img stylized-logo">
        </a>

        <nav class="hy-nav">
            <a href="index.php?section=inicio"   class="hy-nav-link <?php echo $current_section==='inicio'   ? 'hy-active':'' ?>">Inicio</a>
            <a href="index.php?section=watch"    class="hy-nav-link <?php echo $current_section==='watch'    ? 'hy-active':'' ?>">HydroWatch</a>
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
                    <button class="hy-profile-trigger" id="profileTrigger" title="Mi Cuenta" style="background: transparent; border: none; cursor: pointer;">
                        <img src="<?php echo htmlspecialchars($foto_head); ?>" alt="Perfil" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--header-border);">
                    </button>
                    <div class="hy-dropdown-menu" id="profileMenu">
                        <div class="hy-dropdown-header">
                            <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>
                        </div>
                        <a href="index.php?section=dashboard">Perfil</a>
                        <a href="#" id="themeToggle" style="cursor: pointer;"><span class="theme-text">Oscuro</span></a>
                        <a href="index.php?logout=1" class="logout">Salir</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="index.php?section=login"    class="hy-btn-outline">Iniciar sesión</a>
                <a href="index.php?section=registro" class="hy-btn-solid">Regístrate</a>
            <?php endif; ?>
        </div>

        <button class="hy-hamburger" id="hyHamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>

    <div class="hy-mobile-menu" id="hyMobileMenu">
        <a href="index.php?section=inicio">Inicio</a>
        <a href="index.php?section=watch">HydroWatch</a>
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

<script>
    // Theme Toggle Logic
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    const themeText = themeToggle ? themeToggle.querySelector('.theme-text') : null;

    function updateThemeUI(isDark) {
        if (themeText) {
            themeText.textContent = isDark ? 'Claro' : 'Oscuro';
        }
    }

    // Initial UI state
    updateThemeUI(body.classList.contains('dark-mode'));

    if (themeToggle) {
        themeToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const isDark = body.classList.toggle('dark-mode');
            document.documentElement.classList.toggle('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeUI(isDark);
        });
    }

    // Reset theme to dark on logout
    document.querySelectorAll('a[href*="logout"]').forEach(link => {
        link.addEventListener('click', () => {
            localStorage.removeItem('theme');
        });
    });
</script>