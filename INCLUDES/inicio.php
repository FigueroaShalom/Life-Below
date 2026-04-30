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

<!-- HERO (basado en diseño de imagen) -->
<section class="hero">
    <div class="hero-left">
        <div class="hero-badge">
            <i class="fas fa-water"></i> VIDA SUBMARINA · CONOCIMIENTO & CIENCIA
        </div>
        <h1>Un mar de ideas,<br> un punto entre la <span class="hero-accent">ciencia y la sociedad</span></h1>
        <p class="hero-desc">Descubre, aprende y actúa. Life Below transforma el conocimiento científico oceánico en contenido accesible para todos.</p>
        <div class="hero-buttons">
            <a href="#" class="btn-primary"><i class="fas fa-newspaper"></i> Explorar artículos</a>
            <a href="#" class="btn-secondary"><i class="fas fa-play-circle"></i> Ver videos</a>
        </div>
    </div>
    <div class="hero-right">
        <div class="hero-card">
            <i class="fas fa-whale"></i>
            <h3>ODS 14: Vida submarina</h3>
            <p style="font-size:0.85rem; margin-top: 6px;">Conservación y uso sostenible de los océanos</p>
            <div style="margin-top: 1rem;"><i class="fas fa-chart-line"></i> +1200 especies protegidas</div>
        </div>
    </div>
</section>

<!-- ══ STATS ══ -->
<section class="hy-stats">
    <div class="hy-stats-inner">
        <div class="hy-stat"><div class="hy-stat-num">+500</div><div class="hy-stat-label">Especies documentadas</div></div>
        <div class="hy-stat"><div class="hy-stat-num">7</div><div class="hy-stat-label">Océanos y mares</div></div>
        <div class="hy-stat"><div class="hy-stat-num">71%</div><div class="hy-stat-label">Del planeta es agua</div></div>
        <div class="hy-stat"><div class="hy-stat-num">∞</div><div class="hy-stat-label">Por descubrir</div></div>
    </div>
</section>

<!-- CARRUSEL DE NOTICIAS (API integrada: NEWS API / Gnews) -->
<section class="news-section">
    <div class="section-header">
        <div>
            <h2><i class="fas fa-newspaper"></i> Olas de ciencia | Noticias marinas</h2>
            <p>Actualidad sobre conservación oceánica, biodiversidad y vida submarina</p>
        </div>
    </div>
    <div class="carousel-container">
        <div class="carousel-track" id="carouselTrack">
            <div class="loading-placeholder" id="loadingNews">
                <i class="fas fa-spinner fa-pulse"></i> Cargando noticias sobre océanos...
            </div>
        </div>
        <div class="carousel-controls">
            <button class="ctrl-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
            <button class="ctrl-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
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
            <div class="hy-explore-icon"></div>
            <h3>Artículos</h3>
            <p>Investigaciones y textos sobre conservación marina escritos por nuestra comunidad.</p>
            <span class="hy-explore-link">Leer artículos →</span>
        </a>
        <a href="index.php?section=watch" class="hy-explore-card" style="--c1:#005f9e;--c2:#0097c7;">
            <div class="hy-explore-icon">▶</div>
            <h3>Videos</h3>
            <p>Videos exclusivos sobre la vida submarina, ecosistemas y conservación oceánica.</p>
            <span class="hy-explore-link">Ver videos →</span>
        </a>
        <a href="index.php?section=galeria" class="hy-explore-card" style="--c1:#007a50;--c2:#00b47a;">
            <div class="hy-explore-icon">■</div>
            <h3>Galería</h3>
            <p>Imágenes de especies marinas clasificadas por categoría y hábitat.</p>
            <span class="hy-explore-link">Ver galería →</span>
        </a>
        <a href="index.php?section=noticias" class="hy-explore-card" style="--c1:#6a0080;--c2:#9c27b0;">
            <div class="hy-explore-icon">◆</div>
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