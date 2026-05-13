<style>
    /* Convertir textos blancos a oscuros */
    .hy-hero-title, .hy-hero-sub { color: var(--navy) !important; }
    
    /* 1. Letras "ciencia y la sociedad" */
    .hy-hero-accent {
        background: none !important;
        -webkit-text-fill-color: initial !important;
        color: #4DA8DA !important;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /* 2. Botones primarios (Crear cuenta) */
    .hy-cta-primary {
        background-color: #4DA8DA !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(77, 168, 218, 0.4) !important;
        transition: all 0.3s ease !important;
    }

    .hy-cta-primary:hover {
        background-color: #3892c2 !important;
        transform: translateY(-2px) !important;
        color: #ffffff !important;
    }

   
    /* Convertir barra de llamada a la acción en cápsula de cristal */
    .hy-cta-section {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        margin: 4rem auto !important;
        max-width: 1280px;
        box-shadow: 0 8px 32px rgba(0, 40, 80, 0.1);
    }

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
    .hy-carousel-slide.fade-in {
        animation: cFadeIn .5s ease both;
    }
    @keyframes cFadeIn {
        from { opacity: 0; transform: scale(1.02); }
        to   { opacity: 1; transform: scale(1); }
    }
    .hy-dot {
        cursor: pointer; border: none;
        background: rgba(0,0,0,0.1);
    }
    .hy-dot.active { background: #00c4d8; }
    .hy-arrow {
        cursor: pointer; border: none;
        font-family: inherit;
        background: rgba(255,255,255,0.4);
    }
    .hy-arrow:hover { background: rgba(255,255,255,0.8); }
</style>

<section class="hy-hero">
    <div class="hy-hero-bg"></div>
    <div class="hy-hero-content">
        <span class="hy-hero-tag" style="background: rgba(255,255,255,0.4); border-color: white;">Vida Submarina · Conocimiento & Ciencia</span>
        <h1 class="hy-hero-title">
            Un mar de ideas,<br>un punto entre la<br>
            <span class="hy-hero-accent">ciencia y la sociedad</span>
        </h1>
        <p class="hy-hero-sub">
            Descubre, aprende y actúa. Life Below transforma el conocimiento científico oceánico en contenido accesible para todos.
        </p>
    </div>
    <div class="hy-hero-visual">
        <div class="hy-hero-img-wrap">
            <img src="uploads/carrusel_hero.jpg" alt="Vida marina">
        </div>
    </div>
</section>


<section class="hy-section">
    <div class="hy-section-header">
        <h2 class="hy-section-title">Últimos descubrimientos</h2>
        <a href="index.php?section=noticias" class="hy-link-more">Ver todos →</a>
    </div>

    <div class="hy-carousel" id="mainCarousel">
        <div class="hy-carousel-track" id="apiCarouselTrack">
            <div style="padding: 5rem; text-align: center; color: var(--navy);">
                Cargando noticias desde la API...
            </div>
        </div>

        <div class="hy-carousel-controls" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(8px);">
            <div class="hy-carousel-dots" id="apiCarouselDots">
                </div>
            <div class="hy-carousel-arrows">
                <button class="hy-arrow" id="carouselPrev" aria-label="Anterior" style="color: var(--navy); border: 1px solid rgba(0,0,0,0.1);">‹</button>
                <button class="hy-arrow" id="carouselNext" aria-label="Siguiente" style="color: var(--navy); border: 1px solid rgba(0,0,0,0.1);">›</button>
            </div>
        </div>
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
            <a href="index.php?section=login"    class="hy-cta-secondary">Ya tengo cuenta</a>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
(function() {
    const API_KEY = 'd5b5320a0accff00272ab27733ba94ce'; 
    const query = 'vida submarina OR oceanos OR biodiversidad';
    
    const url = `https://gnews.io/api/v4/search?q=${encodeURIComponent(query)}&lang=es&max=3&token=${API_KEY}`;
    
    const track = document.getElementById('apiCarouselTrack');
    const dotsContainer = document.getElementById('apiCarouselDots');

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.articles && data.articles.length > 0) {
                let slidesHtml = '';
                let dotsHtml = '';

                data.articles.forEach((article, index) => {
                    const isActive = index === 0 ? 'active' : '';
                    const imgUrl = article.image || 'uploads/carrusel1.jpg';
                    
                    slidesHtml += `
                        <div class="hy-carousel-slide ${isActive}" data-index="${index}">
                            <img src="${imgUrl}" alt="${article.title}">
                            <div class="hy-carousel-caption">
                                <h3>${article.title}</h3>
                                <p>${article.description}</p>
                                <a href="${article.url}" target="_blank" class="hy-cta-white" style="color:var(--navy); background: rgba(255,255,255,0.7); border: none;">Leer más →</a>
                            </div>
                        </div>
                    `;

                    dotsHtml += `
                        <button class="hy-dot ${isActive}" data-index="${index}" aria-label="Slide ${index+1}" style="border: 1px solid rgba(0,0,0,0.2);"></button>
                    `;
                });

                track.innerHTML = slidesHtml;
                dotsContainer.innerHTML = dotsHtml;
                
                // Iniciar la logica del carrusel una vez que se cargan los datos
                initCarouselLogic();
            } else {
                track.innerHTML = '<div style="padding: 5rem; text-align: center; color: var(--navy);">No se encontraron noticias recientes.</div>';
            }
        })
        .catch(err => {
            console.error('Error cargando API:', err);
            track.innerHTML = '<div style="padding: 5rem; text-align: center; color: var(--navy);">No se pudo conectar con el servidor de noticias.</div>';
        });

    function initCarouselLogic() {
        const slides   = document.querySelectorAll('#mainCarousel .hy-carousel-slide');
        const dots     = document.querySelectorAll('#mainCarousel .hy-dot');
        const btnPrev  = document.getElementById('carouselPrev');
        const btnNext  = document.getElementById('carouselNext');
        const INTERVAL = 5000;

        let current = 0;
        let timer   = null;

        function goTo(index) {
            slides[current].classList.remove('active', 'fade-in');
            dots[current].classList.remove('active');
            current = (index + slides.length) % slides.length;
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

        btnNext.addEventListener('click', function() { next(); startAuto(); });
        btnPrev.addEventListener('click', function() { prev(); startAuto(); });

        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                goTo(parseInt(this.dataset.index));
                startAuto();
            });
        });

        document.getElementById('mainCarousel').addEventListener('mouseenter', stopAuto);
        document.getElementById('mainCarousel').addEventListener('mouseleave', startAuto);

        startAuto();
    }
})();
</script>