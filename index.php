<?php
require_once 'config.php';

// Manejo de login
if (isset($_POST['login'])) {
    if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
        $_SESSION['user'] = ADMIN_USER;
        header('Location: index.php?section=inicio');
        exit;
    }
}

// Manejo de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php?section=inicio');
    exit;
}

$current_section = $_GET['section'] ?? 'inicio';
include 'header.php';

// Si es la sección de inicio, mostramos el layout completo con carrusel
if ($current_section === 'inicio') {
?>
    <!-- Hero Section moderno -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">VIDA SUBMARINA · CONOCIMIENTO & CIENCIA</div>
            <h1>Un mar de ideas, un punto entre la <span class="text-gradient">ciencia y la sociedad</span></h1>
            <p>Descubre, aprende y actúa. Life Below transforma el conocimiento científico oceánico en contenido accesible para todos.</p>
            <div class="hero-buttons">
                <a href="index.php?section=articulos" class="btn-primary"><i class="fas fa-newspaper"></i> Explorar artículos</a>
                <a href="index.php?section=watch" class="btn-secondary"><i class="fas fa-play-circle"></i> Ver videos</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-card-glass">
                <i class="fas fa-whale"></i>
                <h3>ODS 14: Vida submarina</h3>
                <p>Conservación y uso sostenible de los océanos</p>
                <div class="stat"><i class="fas fa-chart-line"></i> +1200 especies protegidas</div>
            </div>
        </div>
    </section>

    <!-- CARRUSEL DE NOTICIAS CON API REAL -->
    <section class="news-section">
        <div class="section-header">
            <div>
                <h2><i class="fas fa-water"></i> Olas de ciencia | Noticias marinas</h2>
                <p>Actualidad sobre conservación oceánica, biodiversidad y vida submarina</p>
            </div>
        </div>
        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">
                <div class="loading-placeholder" id="loadingNews">
                    <i class="fas fa-spinner fa-pulse"></i> Cargando noticias del océano...
                </div>
            </div>
            <div class="carousel-controls">
                <button class="ctrl-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                <button class="ctrl-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <!-- Grid de exploración -->
    <section class="explore-section">
        <div class="section-header">
            <h2>Explora el azul profundo</h2>
            <p>Contenido multimedia y educación marina</p>
        </div>
        <div class="explore-grid">
            <div class="explore-card"><i class="fas fa-chalkboard-user"></i><h3>Educación</h3><p>Recursos para escuelas</p></div>
            <div class="explore-card"><i class="fas fa-chart-simple"></i><h3>Datos científicos</h3><p>Informes oceánicos</p></div>
            <div class="explore-card"><i class="fas fa-hands-helping"></i><h3>Voluntariado</h3><p>Limpieza de costas</p></div>
            <div class="explore-card"><i class="fas fa-globe-americas"></i><h3>ODS 14 Hub</h3><p>Metas globales</p></div>
        </div>
    </section>

    <script>
        // ============================================================
        // INTEGRACIÓN DE API REAL: GNEWS
        // IMPORTANTE: Reemplaza 'AQUI_VA_TU_API_KEY' con tu API Key
        // Obtén una gratis en: https://gnews.io/
        // ============================================================
        (function() {
            // ⚠️ COLOQUE SU API KEY DE GNEWS AQUÍ ⚠️
            const API_KEY = 'AQUI_VA_TU_API_KEY'; 
            
            const query = '(marine life OR ocean conservation OR "vida marina" OR "conservación oceánica" OR biodiversidad marina)';
            const url = `https://gnews.io/api/v4/search?q=${encodeURIComponent(query)}&lang=es&country=es&max=12&token=${API_KEY}`;
            
            const track = document.getElementById('carouselTrack');
            let articles = [];

            function renderCarousel() {
                if (!track) return;
                if (!articles.length) {
                    track.innerHTML = '<div class="loading-placeholder">🌊 No se encontraron noticias marinas. Intenta más tarde.</div>';
                    return;
                }
                const itemsToShow = articles.slice(0, 8);
                track.innerHTML = itemsToShow.map(article => `
                    <div class="news-card">
                        <div class="news-img" style="background-image: url('${article.image || 'https://images.unsplash.com/photo-1583212292454-1fe6227503d6?fit=crop&w=600&h=400'}');"></div>
                        <div class="news-content">
                            <div class="news-source"><i class="fas fa-globe"></i> ${article.source?.name || 'Fuente Marina'}</div>
                            <h3 class="news-title">${article.title || 'Noticia Oceánica'}</h3>
                            <p class="news-desc">${article.description || 'Descubre las últimas investigaciones sobre conservación marina.'}</p>
                            <a href="${article.url}" target="_blank" rel="noopener noreferrer" class="read-link">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                `).join('');
            }

            function fetchNews() {
                if (!API_KEY || API_KEY === 'AQUI_VA_TU_API_KEY') {
                    track.innerHTML = '<div class="loading-placeholder">⚠️ Configura tu API Key de GNews en index.php (busca "AQUI_VA_TU_API_KEY"). Obtén una gratis en gnews.io</div>';
                    return;
                }
                
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        if (data.articles && data.articles.length) {
                            articles = data.articles;
                        } else {
                            // Fallback con datos de ejemplo
                            articles = [
                                { title: 'Arrecifes de coral en recuperación', description: 'Nuevas técnicas de restauración en el Caribe mexicano.', image: 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?fit=crop&w=600&h=400', url: '#', source: { name: 'Ocean Science' } },
                                { title: 'Reducción de plásticos en el Pacífico', description: 'Gracias a políticas de conservación, se ha logrado una disminución del 15%.', image: 'https://images.unsplash.com/photo-1582967788606-a171c1080cb0?fit=crop&w=600&h=400', url: '#', source: { name: 'Blue News' } },
                                { title: 'Nueva especie de cetáceo descubierta', description: 'Un hallazgo que redefine la biodiversidad marina en aguas profundas.', image: 'https://images.unsplash.com/photo-1566140967404-b8b3932483f5?fit=crop&w=600&h=400', url: '#', source: { name: 'BioMar' } }
                            ];
                        }
                        renderCarousel();
                    })
                    .catch(err => {
                        console.error('Error al cargar noticias:', err);
                        track.innerHTML = '<div class="loading-placeholder">🌱 Conecta a internet para ver noticias en vivo sobre conservación marina.</div>';
                    });
            }

            // Control del carrusel (scroll horizontal)
            function scrollCarousel(direction) {
                if (!track) return;
                const cardWidth = track.children[0]?.clientWidth + 24 || 320;
                track.scrollBy({ left: direction === 'next' ? cardWidth : -cardWidth, behavior: 'smooth' });
            }

            document.getElementById('prevBtn')?.addEventListener('click', () => scrollCarousel('prev'));
            document.getElementById('nextBtn')?.addEventListener('click', () => scrollCarousel('next'));

            fetchNews();
        })();
    </script>

    <script>
        // Menú hamburguesa
        const btn = document.getElementById('hyHamburger');
        const menu = document.getElementById('hyMobileMenu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('open');
                btn.classList.toggle('open');
            });
        }
        // Efecto scroll en header
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.glass-nav');
            if (header) header.classList.toggle('scrolled', window.scrollY > 20);
        });
    </script>
<?php 
} else {
    // Si no es inicio, cargar las subsecciones
    $section_file = 'includes/' . $current_section . '.php';
    if (file_exists($section_file)) {
        include $section_file;
    } else {
        echo '<div class="hy-section"><h1>🌊 Sección en construcción</h1><p>Pronto más contenido sobre conservación marina.</p></div>';
    }
}

include 'footer.php';
?>