<h1>Noticias y Eventos</h1>

<div class="news-categories">
    <a href="?section=noticias&sub=eventos" class="news-category-btn <?php echo ($_GET['sub'] ?? '') === 'eventos' ? 'active' : ''; ?>">
         EVENTOS
    </a>
    <a href="?section=noticias&sub=proyectos" class="news-category-btn <?php echo ($_GET['sub'] ?? '') === 'proyectos' ? 'active' : ''; ?>">
        PROYECTOS
    </a>
    <a href="?section=noticias&sub=noticias" class="news-category-btn <?php echo ($_GET['sub'] ?? '') === 'noticias' ? 'active' : ''; ?>">
         NOTICIAS
    </a>
</div>

<div class="news-content">
    <?php if (($_GET['sub'] ?? '') === 'eventos'): ?>
        <div class="events-grid">
            <div class="event-card">
                <div class="event-date">15 MAR 2024</div>
                <h3>Limpieza de playas</h3>
                <p>Únete a nuestra jornada de limpieza en la costa</p>
                <p class="event-location">📍 Playa Central</p>
            </div>
            <div class="event-card">
                <div class="event-date">22 MAR 2024</div>
                <h3>Charla: Conservación marina</h3>
                <p>Expertos discutirán sobre protección de océanos</p>
                <p class="event-location">📍 Auditorio Principal</p>
            </div>
            <div class="event-card">
                <div class="event-date">05 ABR 2024</div>
                <h3>Exposición fotográfica</h3>
                <p>Las mejores fotos de vida marina</p>
                <p class="event-location">📍 Museo de Ciencias</p>
            </div>
        </div>
    <?php elseif (($_GET['sub'] ?? '') === 'proyectos'): ?>
        <div class="projects-grid">
            <div class="project-card">
                <h3>Proyecto Coral</h3>
                <p>Restauración de arrecifes de coral en el Caribe</p>
                <div class="project-progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <p style="text-align: right;"> completado</p>
            </div>
            <div class="project-card">
                <h3> Seguimiento de ballenas</h3>
                <p>Monitoreo satelital de migraciones</p>
                <div class="project-progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <p style="text-align: right;">completado</p>
            </div>
            <div class="project-card">
                <h3>♻️ Reducción de plásticos</h3>
                <p>Iniciativa para reducir plásticos de un solo uso</p>
                <div class="project-progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <p style="text-align: right;"> completado</p>
            </div>
        </div>
    <?php else: ?>
        <div class="news-grid">
            <div class="news-article">
                <h3>Descubren nueva especie en la Fosa de las Marianas</h3>
                <p class="news-date">12 Feb 2024</p>
                <p>Un equipo de investigadores ha descubierto una nueva especie de pez abisal...</p>
                <a href="#" class="read-more-btn">Leer más</a>
            </div>
            <div class="news-article">
                <h3>La Gran Barrera de Coral muestra signos de recuperación</h3>
                <p class="news-date">05 Feb 2024</p>
                <p>Los últimos estudios muestran un aumento en la cobertura de coral...</p>
                <a href="#" class="read-more-btn">Leer más</a>
            </div>
        </div>
    <?php endif; ?>
</div>