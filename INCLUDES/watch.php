<?php
require_once __DIR__ . '/../database/Conexion_base.php';

// ── Helpers ───────────────────────────────────────────────────────────────────

function isYoutube($url) {
    return preg_match('/youtube\.com|youtu\.be/', $url);
}

function getEmbedUrl($url) {
    // enablejsapi=1 permite control via YT IFrame API
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1] . '&controls=0&modestbranding=1&rel=0&enablejsapi=1';
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1] . '&controls=0&modestbranding=1&rel=0&enablejsapi=1';
    return $url;
}

function getYoutubeThumbnail($url) {
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://img.youtube.com/vi/' . $m[1] . '/maxresdefault.jpg';
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://img.youtube.com/vi/' . $m[1] . '/maxresdefault.jpg';
    return null;
}

// ── Cargar videos ─────────────────────────────────────────────────────────────
$cat_filter = $_GET['cat'] ?? '';
$sql = "
    SELECT v.id, v.titulo, v.descripcion, v.video_url, v.categoria, v.fecha_publicacion,
           u.user AS autor,
           (SELECT COUNT(*) FROM likes WHERE id_publicacion = v.id) AS total_likes
    FROM videos v
    JOIN usuarios u ON v.id_autor = u.id
";
if ($cat_filter) {
    $cat_safe = $conn->real_escape_string($cat_filter);
    $sql .= " WHERE v.categoria = '$cat_safe'";
}
$sql .= " ORDER BY v.fecha_publicacion DESC";
$videos = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$categorias = ['general', 'peces', 'mamiferos', 'conservacion', 'documental'];
?>

<style>
/* ── WATCH PAGE ─────────────────────────────────────────────────────────── */
.watch-page {
    background: #000d1a;
    min-height: 100vh;
    padding: 0;
    margin: -0px;
}

.watch-header {
    padding: 2.5rem 2rem 1.5rem;
    text-align: center;
    position: relative;
    z-index: 10;
}

.watch-header h1 {
    font-family: 'Nunito', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 900;
    color: #fff;
    letter-spacing: -1px;
    margin-bottom: .4rem;
}

.watch-header h1 span {
    color: #00c4d8;
}

.watch-header p {
    color: rgba(255,255,255,0.45);
    font-family: 'Nunito', sans-serif;
    font-size: .9rem;
}

/* Filtros */
.watch-filters {
    display: flex;
    justify-content: center;
    gap: .5rem;
    flex-wrap: wrap;
    padding: 0 1rem 2rem;
}

.watch-filter-btn {
    padding: 7px 18px;
    border-radius: 50px;
    border: 1.5px solid rgba(0,196,216,0.25);
    color: rgba(255,255,255,0.6);
    font-weight: 700;
    font-size: .8rem;
    text-decoration: none;
    background: rgba(255,255,255,0.04);
    transition: all .2s;
    font-family: 'Nunito', sans-serif;
    letter-spacing: .5px;
}

.watch-filter-btn:hover,
.watch-filter-btn.active {
    background: #00c4d8;
    color: #000d1a;
    border-color: #00c4d8;
}

/* ── FEED REELS ─────────────────────────────────────────────────────────── */
.reels-outer {
    display: flex;
    justify-content: center;
    padding: 0 1rem 4rem;
}

.reels-feed {
    width: 100%;
    max-width: 420px;
    height: 78vh;
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    scroll-behavior: smooth;
    border-radius: 24px;
    box-shadow: 0 0 80px rgba(0,196,216,0.15), 0 0 0 1px rgba(0,196,216,0.1);
    background: #000;
    position: relative;
    scrollbar-width: none;
}
.reels-feed::-webkit-scrollbar { display: none; }

/* ── REEL CARD ──────────────────────────────────────────────────────────── */
.reel-card {
    scroll-snap-align: start;
    height: 78vh;
    position: relative;
    background: #000;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Video local */
.reel-video-local {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    cursor: pointer;
}

/* YouTube iframe container */
.reel-yt-wrap {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
}
/* Técnica para cubrir sin recortar: centrar iframe 16:9 dentro del contenedor */
.reel-yt-wrap iframe {
    position: absolute;
    top: 50%;
    left: 50%;
    /* Calculamos el tamaño mínimo para cubrir el contenedor manteniendo 16:9 */
    width: max(100%, calc(100vh * 16 / 9));
    height: max(100%, calc(100vw * 9 / 16));
    transform: translate(-50%, -50%);
    border: none;
}

/* Thumbnail placeholder (YouTube antes de activar) */
.reel-thumb {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    transition: opacity .4s ease;
}

.reel-thumb-placeholder {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #001828, #003a5c);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
}

/* Overlay gradiente */
.reel-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(0,0,0,0.85) 0%,
        rgba(0,0,0,0.2) 40%,
        rgba(0,0,0,0) 70%
    );
    pointer-events: none;
    z-index: 2;
}

/* Info y acciones: ocultas por defecto */
.reel-info,
.reel-actions {
    opacity: 0;
    transition: opacity .35s ease;
}

/* Visibles al hover o cuando el feed está scrolleando */
.reel-card:hover .reel-info,
.reel-card:hover .reel-actions,
.reel-card.controls-visible .reel-info,
.reel-card.controls-visible .reel-actions {
    opacity: 1;
}

/* Info del video */
.reel-info {
    position: absolute;
    bottom: 80px;
    left: 16px;
    right: 72px;
    z-index: 3;
    color: #fff;
}

.reel-info .reel-autor {
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #00c4d8;
    font-family: 'Nunito', sans-serif;
    margin-bottom: .3rem;
}

.reel-info h3 {
    font-family: 'Nunito', sans-serif;
    font-size: 1rem;
    font-weight: 900;
    line-height: 1.3;
    margin-bottom: .4rem;
    text-shadow: 0 1px 8px rgba(0,0,0,0.8);
}

.reel-info .reel-desc {
    font-family: 'Nunito', sans-serif;
    font-size: .78rem;
    color: rgba(255,255,255,0.65);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.reel-cat-badge {
    display: inline-block;
    background: rgba(0,196,216,0.2);
    border: 1px solid rgba(0,196,216,0.4);
    color: #00c4d8;
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 50px;
    font-family: 'Nunito', sans-serif;
    margin-bottom: .5rem;
    display: block;
    width: fit-content;
}

/* Acciones laterales */
.reel-actions {
    position: absolute;
    bottom: 80px;
    right: 12px;
    z-index: 3;
    display: flex;
    flex-direction: column;
    gap: 16px;
    align-items: center;
}

.reel-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.reel-action-icon {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    border: 1.5px solid rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    transition: transform .15s, background .2s;
    color: #fff;
}

.reel-action-btn:hover .reel-action-icon {
    background: rgba(255,255,255,0.22);
    transform: scale(1.1);
}

.reel-action-btn.liked .reel-action-icon {
    background: rgba(220,50,80,0.3);
    border-color: rgba(220,50,80,0.5);
}

.reel-action-label {
    font-family: 'Nunito', sans-serif;
    font-size: .65rem;
    font-weight: 700;
    color: rgba(255,255,255,0.7);
}

/* Play/Pause overlay (local) */
.reel-play-overlay {
    position: absolute;
    inset: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}

.reel-play-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(6px);
    border: 2px solid rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    color: #fff;
    opacity: 0;
    transform: scale(.6);
    transition: opacity .25s, transform .25s;
}

.reel-card.show-pause .reel-play-icon {
    opacity: 1;
    transform: scale(1);
}

/* Indicador de tipo */
.reel-type-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    z-index: 5;
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 50px;
    font-family: 'Nunito', sans-serif;
    opacity: 0;
    transition: opacity .3s;
}

.reel-type-badge.local {
    background: rgba(0,196,216,0.2);
    border: 1px solid rgba(0,196,216,0.4);
    color: #00c4d8;
}

.reel-type-badge.youtube {
    background: rgba(255,0,0,0.15);
    border: 1px solid rgba(255,0,0,0.3);
    color: #ff6b6b;
}

.reel-card:hover .reel-type-badge { opacity: 1; }

/* Barra de progreso (solo local) */
.reel-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: rgba(255,255,255,0.1);
    z-index: 5;
}

.reel-progress-bar {
    height: 100%;
    background: #00c4d8;
    width: 0%;
    transition: width .1s linear;
}

/* Estado vacío */
.watch-empty {
    text-align: center;
    padding: 5rem 2rem;
    color: rgba(255,255,255,0.4);
    font-family: 'Nunito', sans-serif;
}

.watch-empty .watch-empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.watch-empty h3 {
    font-size: 1.2rem;
    font-weight: 800;
    color: rgba(255,255,255,0.6);
    margin-bottom: .5rem;
}

/* Contador de videos */
.reel-counter {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 5;
    font-family: 'Nunito', sans-serif;
    font-size: .7rem;
    font-weight: 800;
    color: rgba(255,255,255,0.5);
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(4px);
    padding: 4px 10px;
    border-radius: 50px;
}

/* ── RESPONSIVE ─────────────────────────────────────────────────────────── */
@media (max-width: 480px) {
    .reels-feed {
        max-width: 100%;
        border-radius: 16px;
        height: 82vh;
    }
    .reel-card { height: 82vh; }
}
</style>

<div class="watch-page">

    <div class="watch-header">
        <h1>▶ <span>WATCH</span></h1>
        <p>Vida marina en video · scroll para explorar</p>
    </div>

    <!-- Filtros -->
    <div class="watch-filters">
        <a href="?section=watch"
           class="watch-filter-btn <?php echo !$cat_filter ? 'active' : ''; ?>">Todos</a>
        <?php foreach ($categorias as $cat): ?>
            <a href="?section=watch&cat=<?php echo $cat; ?>"
               class="watch-filter-btn <?php echo $cat_filter === $cat ? 'active' : ''; ?>">
                <?php echo ucfirst($cat); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($videos)): ?>
        <div class="watch-empty">
            <div class="watch-empty-icon">🌊</div>
            <h3>No hay videos aún</h3>
            <p>Sé el primero en publicar desde tu dashboard.</p>
        </div>
    <?php else: ?>

    <div class="reels-outer">
        <div class="reels-feed" id="reelsFeed">
            <?php foreach ($videos as $i => $v):
                $isYt    = isYoutube($v['video_url']);
                $thumb   = $isYt ? getYoutubeThumbnail($v['video_url']) : null;
                $embedUrl     = $isYt ? getEmbedUrl($v['video_url'], true)  : null;
                $embedUrlSound = $isYt ? getEmbedUrl($v['video_url'], false) : null;
                $localUrl = !$isYt ? htmlspecialchars($v['video_url']) : null;
                $total   = count($videos);
            ?>
            <div class="reel-card"
                 data-id="<?php echo $v['id']; ?>"
                 data-type="<?php echo $isYt ? 'youtube' : 'local'; ?>"
                 data-embed="<?php echo $isYt ? htmlspecialchars($embedUrl) : ''; ?>"
                 data-embed-sound="<?php echo $isYt ? htmlspecialchars($embedUrlSound) : ''; ?>"
                 data-index="<?php echo $i; ?>">

                <!-- Tipo badge -->
                <div class="reel-type-badge <?php echo $isYt ? 'youtube' : 'local'; ?>">
                    <?php if ($isYt): ?>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg> YouTube
                    <?php else: ?>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Local
                    <?php endif; ?>
                </div>

                <!-- Contador -->
                <div class="reel-counter"><?php echo $i+1; ?> / <?php echo $total; ?></div>

                <?php if ($isYt): ?>
                    <!-- YouTube: thumbnail + iframe lazy -->
                    <?php if ($thumb): ?>
                        <div class="reel-thumb" style="background-image:url('<?php echo htmlspecialchars($thumb); ?>')"></div>
                    <?php else: ?>
                    <div class="reel-thumb-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                    <?php endif; ?>
                    <!-- iframe se inyecta por JS al entrar al viewport -->
                    <div class="reel-yt-wrap" id="yt-<?php echo $v['id']; ?>"></div>
                <?php else: ?>
                    <!-- Video local -->
                    <video class="reel-video-local"
                           src="<?php echo $localUrl; ?>"
                           muted loop preload="metadata" playsinline></video>
                <?php endif; ?>

                <!-- Gradiente overlay -->
                <div class="reel-gradient"></div>

                <!-- Play overlay (local y YouTube) -->
                <div class="reel-play-overlay">
                    <div class="reel-play-icon">
                        <svg class="icon-pause" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                        <svg class="icon-play"  width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="display:none;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                </div>

                <!-- Info -->
                <div class="reel-info">
                    <span class="reel-cat-badge"><?php echo htmlspecialchars($v['categoria'] ?? 'general'); ?></span>
                    <div class="reel-autor">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg><?php echo htmlspecialchars($v['autor']); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($v['titulo']); ?></h3>
                    <?php if (!empty($v['descripcion'])): ?>
                        <div class="reel-desc"><?php echo htmlspecialchars($v['descripcion']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Acciones -->
                <div class="reel-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="database/procesar_like.php" class="reel-like-form">
                        <input type="hidden" name="id_publicacion" value="<?php echo $v['id']; ?>">
                        <input type="hidden" name="redirect" value="?section=watch<?php echo $cat_filter ? '&cat='.$cat_filter : ''; ?>">
                        <button type="submit" class="reel-action-btn" title="Like">
                            <div class="reel-action-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                            </div>
                            <span class="reel-action-label"><?php echo $v['total_likes']; ?></span>
                        </button>
                    </form>
                    <?php else: ?>
                    <a href="?section=login" class="reel-action-btn" title="Like">
                        <div class="reel-action-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                        </div>
                        <span class="reel-action-label"><?php echo $v['total_likes']; ?></span>
                    </a>
                    <?php endif; ?>

                    <!-- Botón sonido: local y YouTube -->
                    <button class="reel-action-btn reel-sound-btn" title="Sonido">
                        <div class="reel-action-icon">
                            <svg class="icon-mute" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                            <svg class="icon-sound" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="display:none;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 010 7.07"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                        </div>
                        <span class="reel-action-label">Sonido</span>
                    </button>

                    <button class="reel-action-btn reel-share-btn"
                            data-title="<?php echo htmlspecialchars($v['titulo']); ?>"
                            title="Compartir">
                        <div class="reel-action-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </div>
                        <span class="reel-action-label">Compartir</span>
                    </button>
                </div>

                <!-- Barra de progreso (solo local) -->
                <?php if (!$isYt): ?>
                <div class="reel-progress">
                    <div class="reel-progress-bar"></div>
                </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php endif; ?>

</div><!-- /watch-page -->

<script>
// ── YouTube IFrame API ────────────────────────────────────────────────────────
var tag = document.createElement('script');
tag.src = 'https://www.youtube.com/iframe_api';
document.head.appendChild(tag);

// Mapa de players YT: cardId -> YT.Player
var ytPlayers = {};

// Callback global requerido por la API
function onYouTubeIframeAPIReady() {
    // Los players se crean en injectYT cuando la API ya está lista
    window._ytApiReady = true;
    // Si hay cards pendientes de inicializar, hacerlo ahora
    if (window._ytPending) {
        window._ytPending.forEach(fn => fn());
        window._ytPending = [];
    }
}

(function () {
    const feed  = document.getElementById('reelsFeed');
    if (!feed) return;
    const cards = [...feed.querySelectorAll('.reel-card')];

    // ── Crear YT Player con la API ────────────────────────────────────────
    function injectYT(card) {
        const wrap = card.querySelector('.reel-yt-wrap');
        if (!wrap || wrap.dataset.injected) return;
        wrap.dataset.injected = '1';

        const cardId = card.dataset.id;
        const embedUrl = card.dataset.embed; // mute=1 inicial
        // Extraer video ID de la URL embed
        const match = embedUrl.match(/embed\/([a-zA-Z0-9_-]+)/);
        if (!match) return;
        const videoId = match[1];

        // Crear div contenedor para el player
        const div = document.createElement('div');
        div.id = 'yt-player-' + cardId;
        wrap.appendChild(div);

        const thumb = card.querySelector('.reel-thumb, .reel-thumb-placeholder');

        function createPlayer() {
            ytPlayers[cardId] = new YT.Player('yt-player-' + cardId, {
                videoId: videoId,
                playerVars: {
                    autoplay: 1,
                    mute: 1,
                    loop: 1,
                    playlist: videoId,
                    controls: 0,
                    modestbranding: 1,
                    rel: 0,
                    playsinline: 1
                },
                events: {
                    onReady: function(e) {
                        e.target.playVideo();
                        if (thumb) thumb.style.opacity = '0';
                        // Actualizar icono: arranca muteado
                        const soundBtn = card.querySelector('.reel-sound-btn');
                        if (soundBtn) {
                            soundBtn.querySelector('.icon-mute').style.display  = '';
                            soundBtn.querySelector('.icon-sound').style.display = 'none';
                        }
                    }
                }
            });
        }

        if (window._ytApiReady) {
            createPlayer();
        } else {
            window._ytPending = window._ytPending || [];
            window._ytPending.push(createPlayer);
        }
    }

    function removeYT(card) {
        const wrap = card.querySelector('.reel-yt-wrap');
        if (!wrap) return;
        const cardId = card.dataset.id;
        if (ytPlayers[cardId]) {
            ytPlayers[cardId].stopVideo();
            ytPlayers[cardId].destroy();
            delete ytPlayers[cardId];
        }
        wrap.innerHTML = '';
        delete wrap.dataset.injected;
        const thumb = card.querySelector('.reel-thumb, .reel-thumb-placeholder');
        if (thumb) thumb.style.opacity = '1';
    }

    // Inyectar primer video YT inmediatamente
    if (cards.length > 0 && cards[0].dataset.type === 'youtube') {
        injectYT(cards[0]);
    }

    // ── Intersection Observer — viewport real ─────────────────────────────
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const card = entry.target;
            const type = card.dataset.type;
            if (entry.isIntersecting) {
                if (type === 'local') {
                    const vid = card.querySelector('.reel-video-local');
                    if (vid) vid.play().catch(() => {});
                } else {
                    injectYT(card);
                }
            } else {
                if (type === 'local') {
                    const vid = card.querySelector('.reel-video-local');
                    if (vid) vid.pause();
                } else {
                    removeYT(card);
                }
            }
        });
    }, { threshold: 0.5 });

    cards.forEach(c => observer.observe(c));

    // ── Barra de progreso (videos locales) ───────────────────────────────
    cards.forEach(card => {
        if (card.dataset.type !== 'local') return;
        const vid = card.querySelector('.reel-video-local');
        const bar = card.querySelector('.reel-progress-bar');
        if (!vid || !bar) return;
        vid.addEventListener('timeupdate', () => {
            if (vid.duration) bar.style.width = (vid.currentTime / vid.duration * 100) + '%';
        });
    });

    // ── Play / Pause al hacer click ───────────────────────────────────────
    cards.forEach(card => {
        const type    = card.dataset.type;
        const overlay = card.querySelector('.reel-play-icon');

        card.addEventListener('click', (e) => {
            if (e.target.closest('.reel-actions')) return;

            if (type === 'local') {
                const vid = card.querySelector('.reel-video-local');
                if (!vid) return;
                const iconPause = overlay ? overlay.querySelector('.icon-pause') : null;
                const iconPlay  = overlay ? overlay.querySelector('.icon-play')  : null;
                if (vid.paused) {
                    vid.play();
                    if (iconPause) iconPause.style.display = '';
                    if (iconPlay)  iconPlay.style.display  = 'none';
                } else {
                    vid.pause();
                    if (iconPause) iconPause.style.display = 'none';
                    if (iconPlay)  iconPlay.style.display  = '';
                }
                card.classList.add('show-pause');
                setTimeout(() => card.classList.remove('show-pause'), 700);

            } else {
                // YouTube: pausar/reanudar con la API
                const cardId = card.dataset.id;
                const player = ytPlayers[cardId];
                if (!player) return;
                const state = player.getPlayerState();
                // 1 = playing, 2 = paused
                if (state === 1) {
                    player.pauseVideo();
                    if (overlay) {
                        overlay.querySelector('.icon-pause').style.display = 'none';
                        overlay.querySelector('.icon-play').style.display  = '';
                        card.classList.add('show-pause');
                        setTimeout(() => card.classList.remove('show-pause'), 700);
                    }
                } else {
                    player.playVideo();
                    if (overlay) {
                        overlay.querySelector('.icon-pause').style.display = '';
                        overlay.querySelector('.icon-play').style.display  = 'none';
                        card.classList.add('show-pause');
                        setTimeout(() => card.classList.remove('show-pause'), 700);
                    }
                }
            }
        });
    });

    // ── Sonido ────────────────────────────────────────────────────────────
    document.querySelectorAll('.reel-sound-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const card   = btn.closest('.reel-card');
            const type   = card.dataset.type;
            const iconM  = btn.querySelector('.icon-mute');
            const iconS  = btn.querySelector('.icon-sound');

            if (type === 'local') {
                const vid = card.querySelector('.reel-video-local');
                if (!vid) return;
                vid.muted = !vid.muted;
                iconM.style.display = vid.muted  ? '' : 'none';
                iconS.style.display = !vid.muted ? '' : 'none';
            } else {
                // YouTube: mute/unmute sin recargar
                const cardId = card.dataset.id;
                const player = ytPlayers[cardId];
                if (!player) return;
                if (player.isMuted()) {
                    player.unMute();
                    iconM.style.display = 'none';
                    iconS.style.display = '';
                } else {
                    player.mute();
                    iconM.style.display = '';
                    iconS.style.display = 'none';
                }
            }
        });
    });

    // ── Controls visibles temporalmente al hacer scroll ───────────────────
    let scrollTimer;
    feed.addEventListener('scroll', () => {
        cards.forEach(c => c.classList.add('controls-visible'));
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
            cards.forEach(c => c.classList.remove('controls-visible'));
        }, 1800);
    });

    // ── Compartir ─────────────────────────────────────────────────────────
    document.querySelectorAll('.reel-share-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const title = btn.dataset.title;
            if (navigator.share) {
                navigator.share({ title, url: window.location.href });
            } else {
                navigator.clipboard.writeText(window.location.href)
                    .then(() => alert('¡Enlace copiado!'));
            }
        });
    });

    // ── Swipe táctil ─────────────────────────────────────────────────────
    let touchY = 0;
    feed.addEventListener('touchstart', e => { touchY = e.touches[0].clientY; });
    feed.addEventListener('touchend', e => {
        const diff = touchY - e.changedTouches[0].clientY;
        if (Math.abs(diff) > 60) {
            const h = feed.clientHeight;
            feed.scrollBy({ top: diff > 0 ? h : -h, behavior: 'smooth' });
        }
    });

})();
</script>