<?php
$carousel_images = [
    [
        'img'   => 'uploads/carrusel1.jpg',
        'title' => 'Aumenta la población de lobos marinos en el Pacífico',
        'desc'  => 'Científicos encuentran un población de lobos marinos, aumentando su presencia en la región.'
    ],
    [
        'img'   => 'uploads/carrusel2.jpg',
        'title' => 'El gigante gentil',
        'desc'  => 'Los manatíes regresan a las costas protegidas.'
    ],
    [
        'img'   => 'uploads/carrusel3.jpg',
        'title' => 'Proyecto de limpieza oceánica récord',
        'desc'  => 'Remueven 100 toneladas de plástico del Océano Pacífico.'
    ],
];
?>

<!-- ══ HERO ══ -->
<section class="hy-hero">
    <div class="hy-hero-bg"></div>
    <div class="hy-hero-content">
        <span class="hy-hero-tag">🌊 ODS 14 · Vida Submarina </span>
        <h1 class="hy-hero-title">
            Un mar de ideas,<br>un punto entre la<br>
            <span class="hy-hero-accent">ciencia y la sociedad</span>
        </h1>
        <p class="hy-hero-sub">
            Descubre, aprende y actúa. Life Below transforma el conocimiento científico oceánico en contenido accesible para todos.
        </p>
        <div class="hy-hero-actions">
            <a href="index.php?section=articulos" class="hy-cta-primary">Explorar artículos</a>
            <a href="index.php?section=watch"     class="hy-cta-secondary">▶ Ver videos</a>
        </div>
    </div>
    <div class="hy-hero-visual">
        <div class="hy-hero-img-wrap">
            <img src="uploads/carrusel_hero.jpg" alt="Vida marina">
            <div class="hy-hero-img-badge"><span>🐢</span><p>Vida Submarina</p></div>
        </div>
    </div>
</section>

<!-- ══ STATS ══ -->
<section class="hy-stats">
    <div class="hy-stats-inner">
        <div class="hy-stat"><div class="hy-stat-num">+500</div><div class="hy-stat-label">Especies documentadas</div></div>
        <div class="hy-stat"><div class="hy-stat-num">7</div><div class="hy-stat-label">Océanos y mares</div></div>
        <div class="hy-stat"><div class="hy-stat-num">71%</div><div class="hy-stat-label">Del planeta es agua</div></div>
        <div class="hy-stat"><div class="hy-stat-num">ODS 14</div><div class="hy-stat-label">Vida Submarina</div></div>
    </div>
</section>

<!-- ══ CARRUSEL ══ -->
<section class="hy-section">
    <div class="hy-section-header">
        <h2 class="hy-section-title">Últimos descubrimientos</h2>
        <a href="index.php?section=noticias" class="hy-link-more">Ver todos →</a>
    </div>

    <div class="hy-carousel" id="mainCarousel">

        <!-- Slides -->
        <div class="hy-carousel-track">
            <?php foreach ($carousel_images as $i => $slide): ?>
            <div class="hy-carousel-slide <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>">
                <img src="<?php echo $slide['img']; ?>"
                     alt="<?php echo htmlspecialchars($slide['title']); ?>"
                     loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>">
                <div class="hy-carousel-caption">
                    <h3><?php echo htmlspecialchars($slide['title']); ?></h3>
                    <p><?php echo htmlspecialchars($slide['desc']); ?></p>
                    <a href="index.php?section=noticias" class="hy-cta-white">Leer más →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Controles -->
        <div class="hy-carousel-controls">
            <div class="hy-carousel-dots" id="carouselDots">
                <?php foreach ($carousel_images as $i => $slide): ?>
                    <button class="hy-dot <?php echo $i === 0 ? 'active' : ''; ?>"
                            data-index="<?php echo $i; ?>"
                            aria-label="Slide <?php echo $i+1; ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="hy-carousel-arrows">
                <button class="hy-arrow" id="carouselPrev">❮</button>
                <button class="hy-arrow" id="carouselNext">❯</button>
            </div>
        </div>

    </div>
</section>

<!-- ══ EXPLORE GRID ══ -->
<section class="hy-section">
    <div class="hy-section-header">
        <h2 class="hy-section-title">Explora LIFE BELOW</h2>
    </div>
    <div class="hy-explore-grid">
        <a href="index.php?section=articulos" class="hy-explore-card" style="--c1:#0077be;--c2:#00b4d8;">
            <div class="hy-explore-icon">📰</div>
            <h3>Artículos</h3>
            <p>Investigaciones y textos sobre conservación marina escritos por nuestra comunidad.</p>
            <span class="hy-explore-link">Leer artículos →</span>
        </a>
        <a href="index.php?section=watch" class="hy-explore-card" style="--c1:#005f9e;--c2:#0097c7;">
            <div class="hy-explore-icon">🎬</div>
            <h3>Watch</h3>
            <p>Videos exclusivos sobre la vida submarina, ecosistemas y conservación oceánica.</p>
            <span class="hy-explore-link">Ver videos →</span>
        </a>
        <a href="index.php?section=galeria" class="hy-explore-card" style="--c1:#007a50;--c2:#00b47a;">
            <div class="hy-explore-icon">🐠</div>
            <h3>Galería</h3>
            <p>Imágenes de especies marinas clasificadas por categoría y hábitat.</p>
            <span class="hy-explore-link">Ver galería →</span>
        </a>
        <a href="index.php?section=noticias" class="hy-explore-card" style="--c1:#6a0080;--c2:#9c27b0;">
            <div class="hy-explore-icon">📡</div>
            <h3>Noticias</h3>
            <p>Eventos, proyectos y noticias recientes sobre los océanos del mundo.</p>
            <span class="hy-explore-link">Ver noticias →</span>
        </a>
    </div>
</section>

<?php if (!isset($_SESSION['user'])): ?>
<section class="hy-cta-section">
    <div class="hy-cta-inner">
        <div class="hy-cta-text">
            <h2>Únete a la exploración</h2>
            <p>Crea tu cuenta gratis y accede a artículos, videos, likes y comentarios.</p>
        </div>
        <div class="hy-cta-btns">
            <a href="index.php?section=registro" class="hy-cta-primary">Crear cuenta gratis</a>
            <a href="index.php?section=login"    class="hy-cta-white">Ya tengo cuenta</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══ CARRUSEL CSS ══ -->
<style>
.hy-carousel-track { position: relative; }
.hy-carousel-slide {
    display: none;
    position: relative;
    height: 420px;
}
.hy-carousel-slide.active { display: block; }
.hy-carousel-slide img {
    width: 100%; height: 100%; object-fit: cover;
    transition: opacity .4s ease;
}
/* Animación fade */
.hy-carousel-slide.fade-in {
    animation: cFadeIn .5s ease both;
}
@keyframes cFadeIn {
    from { opacity: 0; transform: scale(1.02); }
    to   { opacity: 1; transform: scale(1); }
}
/* Botones como button, no como a */
.hy-dot {
    cursor: pointer; border: none;
    background: rgba(255,255,255,0.25);
}
.hy-dot.active { background: #00c4d8; }
.hy-arrow {
    cursor: pointer; border: none;
    font-family: inherit;
}
/* Barra de progreso */
.hy-carousel-progress {
    height: 3px;
    background: rgba(255,255,255,0.1);
    position: relative; overflow: hidden;
}
.hy-carousel-progress-bar {
    height: 100%;
    background: #00c4d8;
    width: 0%;
    transition: width linear;
}
</style>

<!-- ══ CARRUSEL JS ══ -->
<script>
(function() {
    const slides   = document.querySelectorAll('#mainCarousel .hy-carousel-slide');
    const dots     = document.querySelectorAll('#mainCarousel .hy-dot');
    const btnPrev  = document.getElementById('carouselPrev');
    const btnNext  = document.getElementById('carouselNext');
    const INTERVAL = 5000; // 5 segundos

    let current = 0;
    let timer   = null;

    function goTo(index) {
        // Quitar active del slide y dot actuales
        slides[current].classList.remove('active', 'fade-in');
        dots[current].classList.remove('active');

        // Calcular nuevo índice (circular)
        current = (index + slides.length) % slides.length;

        // Activar nuevo slide y dot
        slides[current].classList.add('active', 'fade-in');
        dots[current].classList.add('active');
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function startAuto() {
        stopAuto();
        timer = setInterval(next, INTERVAL);
    }

    function stopAuto() {
        if (timer) { clearInterval(timer); timer = null; }
    }

    // Botones flechas
    btnNext.addEventListener('click', function() { next(); startAuto(); });
    btnPrev.addEventListener('click', function() { prev(); startAuto(); });

    // Dots
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            goTo(parseInt(this.dataset.index));
            startAuto();
        });
    });

    // Pausar al hover
    document.getElementById('mainCarousel').addEventListener('mouseenter', stopAuto);
    document.getElementById('mainCarousel').addEventListener('mouseleave', startAuto);

    // Swipe en móvil
    let touchStartX = 0;
    document.getElementById('mainCarousel').addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
    });
    document.getElementById('mainCarousel').addEventListener('touchend', function(e) {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? next() : prev();
            startAuto();
        }
    });

    // Arrancar
    startAuto();
})();
</script>