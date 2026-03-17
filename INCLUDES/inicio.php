<div class="hero-section">
    <h1><?php echo SITE_NAME; ?>LES DA LA BIENVENIDA</h1>
    <p class="hero-subtitle">Descubre la fascinante vida que habita en nuestros océanos</p>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon"></div>
        <h3>+500 </h3>
        <p>Descubre la increíble diversidad de la vida marina</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"></div>
        <h3>7 Mares</h3>
        <p>Explora los diferentes ecosistemas marinos</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"></div>
        <h3>Ciencia Ciudadana</h3>
        <p>Contribuye a la investigación marina</p>
    </div>
</div>

<h2 class="section-title">Últimos descubrimientos</h2>

<div class="news-carousel">
    <?php
    $carousel_images = [
        ['img' => 'https://images.unsplash.com/photo-1582967788606-a171d1080cb0?w=800', 
         'title' => 'Descubren nueva especie de coral en el Pacífico',
         'desc' => 'Científicos encuentran un arrecife prístino en Galápagos'],
        ['img' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800',
         'title' => 'Ballenas jorobadas regresan al Ártico',
         'desc' => 'Avistan 50 ejemplares en su migración anual'],
        ['img' => 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=800',
         'title' => 'Proyecto de limpieza oceánica récord',
         'desc' => 'Remueven 100 toneladas de plástico del Pacífico']
    ];
    
    $current = $_GET['slide'] ?? 1;
    $current = min(max(1, intval($current)), count($carousel_images));
    $img = $carousel_images[$current - 1];
    ?>
    
    <div class="carousel-slide">
        <img src="<?php echo $img['img']; ?>" alt="Noticia">
        <div class="carousel-caption">
            <h3><?php echo $img['title']; ?></h3>
            <p><?php echo $img['desc']; ?></p>
        </div>
    </div>
    
    <div class="carousel-nav">
        <?php if ($current > 1): ?>
            <a href="?section=inicio&slide=<?php echo $current - 1; ?>">❮ Anterior</a>
        <?php endif; ?>
        <?php if ($current < count($carousel_images)): ?>
            <a href="?section=inicio&slide=<?php echo $current + 1; ?>">Siguiente ❯</a>
        <?php endif; ?>
    </div>
</div>