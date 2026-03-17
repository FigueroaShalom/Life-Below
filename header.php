<?php
$current_section = $_GET['section'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Aprendiendo sobre la vida marina</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <div class="logo">
             <a href="index.php?section=inicio">
             <img src="uploads/logo.png"alt="<?php echo SITE_NAME; ?>" class="site-logo">
             </a>
            </div>

            
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php?section=inicio" class="<?php echo $current_section === 'inicio' ? 'active' : ''; ?>"> Inicio</a></li>
                    <li><a href="index.php?section=watch" class="<?php echo $current_section === 'watch' ? 'active' : ''; ?>">▶ WATCH</a></li>
                    <li><a href="index.php?section=galeria" class="<?php echo $current_section === 'galeria' ? 'active' : ''; ?>"> GALERÍA</a></li>
                    <li><a href="index.php?section=noticias" class="<?php echo $current_section === 'noticias' ? 'active' : ''; ?>"> NOTICIAS</a></li>
                    <li><a href="index.php?section=articulos" class="<?php echo $current_section === 'articulos' ? 'active' : ''; ?>">ARTÍCULOS</a></li>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="index.php?section=dashboard" class="<?php echo $current_section === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                        <li><a href="index.php?logout=1">Salir</a></li>
                    <?php else: ?>
                        <li><a href="index.php?section=login" class="<?php echo $current_section === 'login' ? 'active' : ''; ?>">🔑 Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">