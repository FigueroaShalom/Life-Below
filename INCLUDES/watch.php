<?php
require_once __DIR__ . '/../database/Conexion_base.php';

function isYoutube($url) {
    return preg_match('/youtube\.com|youtu\.be/', $url);
}
function getYoutubeId($url) {
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) return $m[1];
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) return $m[1];
    return null;
}
function getYoutubeThumbnail($url) {
    $id = getYoutubeId($url);
    return $id ? 'https://img.youtube.com/vi/' . $id . '/maxresdefault.jpg' : null;
}

function findRelatedArticle($conn, $title, $description, $category) {
    $search = trim($title . ' ' . $description . ' ' . $category);
    if (!$search) return null;
    $safe = $conn->real_escape_string($search);
    $sql = "
        SELECT id
        FROM publicaciones
        WHERE titulo LIKE '%$safe%'
           OR contenido LIKE '%$safe%'
           OR categoria LIKE '%$safe%'
        ORDER BY fecha_creacion DESC
        LIMIT 1
    ";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['id'];
    }
    return null;
}

$cat_filter   = $_GET['cat'] ?? '';
$search_query = trim($_GET['q'] ?? '');
$sql = "
    SELECT v.id, v.titulo, v.descripcion, v.video_url, v.categoria, v.fecha_publicacion,
           u.user AS autor,
           (SELECT COUNT(*) FROM likes WHERE id_publicacion = v.id) AS total_likes
    FROM videos v JOIN usuarios u ON v.id_autor = u.id
";
$where = [];
if ($cat_filter) {
    $cat_safe = $conn->real_escape_string($cat_filter);
    $where[] = "v.categoria = '$cat_safe'";
}
if ($search_query) {
    $query_safe = $conn->real_escape_string($search_query);
    $where[] = "(
        v.titulo LIKE '%$query_safe%' OR
        v.descripcion LIKE '%$query_safe%' OR
        v.categoria LIKE '%$query_safe%' OR
        u.user LIKE '%$query_safe%'
    )";
}
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= " ORDER BY v.fecha_publicacion DESC";
$videos = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
$categorias = ['general', 'peces', 'mamiferos', 'conservacion', 'documental'];
$total = count($videos);
?>

<script src="https://www.youtube.com/iframe_api"></script>

<style>
.watch-page {
    display: flex;
    background: #000d1a;
    min-height: calc(100vh - 70px);
}

/* ── SIDEBAR ── */
.watch-sidebar {
    width: 210px;
    flex-shrink: 0;
    padding: 2rem 1.2rem;
    display: flex;
    flex-direction: column;
    gap: 1.8rem;
    border-right: 1px solid rgba(255,255,255,0.06);
    position: sticky;
    top: 0;
    height: calc(100vh - 70px);
    overflow-y: auto;
    scrollbar-width: none;
}
.watch-sidebar::-webkit-scrollbar { display: none; }
.watch-sidebar-title {
    font-family: 'Nunito', sans-serif;
    font-size: 1.4rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.5px;
}
.watch-sidebar-title span { color: #00c4d8; }
.watch-sidebar-sub {
    font-family: 'Nunito', sans-serif;
    font-size: .72rem;
    color: rgba(255,255,255,0.3);
    margin-top: .2rem;
}
.watch-counter-box {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    padding: .9rem;
    text-align: center;
}
.watch-counter-num {
    font-family: 'Nunito', sans-serif;
    font-size: 1.8rem;
    font-weight: 900;
    color: #00c4d8;
    line-height: 1;
}
.watch-counter-label {
    font-family: 'Nunito', sans-serif;
    font-size: .68rem;
    color: rgba(255,255,255,0.3);
    margin-top: .3rem;
}
.watch-search {
    display: grid;
    gap: .6rem;
}
.watch-search input {
    width: 100%;
    padding: .75rem 1rem;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.04);
    color: #fff;
    font-family: 'Nunito', sans-serif;
    font-size: .85rem;
}
.watch-search input::placeholder {
    color: rgba(255,255,255,0.42);
}
.watch-search button,
.watch-search-clear {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    padding: .7rem 1rem;
    border-radius: 12px;
    font-family: 'Nunito', sans-serif;
    font-size: .82rem;
    font-weight: 800;
    text-decoration: none;
    border: none;
}
.watch-search button {
    background: #00c4d8;
    color: #04121e;
    cursor: pointer;
}
.watch-search-clear {
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.75);
    border: 1px solid rgba(255,255,255,0.1);
}
.watch-filters { display: flex; flex-direction: column; gap: .35rem; }
.watch-filters-label {
    font-family: 'Nunito', sans-serif;
    font-size: .62rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.28);
    margin-bottom: .2rem;
}
.watch-filter-btn {
    padding: 7px 12px;
    border-radius: 9px;
    border: 1px solid rgba(255,255,255,0.07);
    color: rgba(255,255,255,0.45);
    font-weight: 700;
    font-size: .78rem;
    text-decoration: none;
    background: transparent;
    transition: all .2s;
    font-family: 'Nunito', sans-serif;
    display: block;
}
.watch-filter-btn:hover {
    background: rgba(255,255,255,0.05);
    color: rgba(255,255,255,0.75);
}
.watch-filter-btn.active {
    background: rgba(0,196,216,0.12);
    color: #00c4d8;
    border-color: rgba(0,196,216,0.25);
}
.watch-keyboard-hint { display: flex; flex-direction: column; gap: .45rem; }
.watch-keyboard-hint-title {
    font-family: 'Nunito', sans-serif;
    font-size: .62rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.28);
}
.watch-key-row {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-family: 'Nunito', sans-serif;
    font-size: .72rem;
    color: rgba(255,255,255,0.38);
}
.watch-key {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 4px;
    padding: 1px 7px;
    font-size: .65rem;
    color: rgba(255,255,255,0.5);
    font-family: monospace;
    white-space: nowrap;
}

/* ── CENTRO ── */
.watch-center {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}

/* ── VIEWPORT (contiene todas las cards apiladas) ── */
.reel-viewport {
    width: min(100%, 900px);
    max-width: 900px;
    height: auto;
    aspect-ratio: 16 / 9;
    max-height: calc(100vh - 110px);
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 0 60px rgba(0,196,216,0.1), 0 0 0 1px rgba(0,196,216,0.07);
    background: #000;
}

/* ── CARDS (apiladas, solo la active visible) ── */
.reel-card {
    position: absolute;
    inset: 0;
    background: #000;
    opacity: 0;
    pointer-events: none;
    transition: opacity .3s ease;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
.reel-card.active {
    opacity: 1;
    pointer-events: all;
}

.reel-video-local {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}
.reel-yt-wrap {
    position: absolute; inset: 0; overflow: hidden;
}
.reel-yt-wrap > div,
.reel-yt-wrap > div iframe,
.reel-yt-wrap iframe {
    position: absolute;
    inset: 0;
    width: 100% !important;
    height: 100% !important;
    max-width: none !important;
    max-height: none !important;
}

.reel-thumb {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    transition: opacity .5s ease; z-index: 1;
}
.reel-thumb-placeholder {
    position: absolute; inset: 0;
    background: linear-gradient(135deg,#001828,#003a5c);
    display: flex; align-items: center; justify-content: center;
    z-index: 1;
}
.reel-gradient {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.88) 0%, rgba(0,0,0,.15) 45%, transparent 70%);
    pointer-events: none; z-index: 2;
}

/* Info y acciones: ocultas, visibles en hover o show-ui */
.reel-info, .reel-actions {
    opacity: 0;
    transition: opacity .3s ease;
}
.reel-card.active:hover .reel-info,
.reel-card.active:hover .reel-actions,
.reel-card.show-ui .reel-info,
.reel-card.show-ui .reel-actions { opacity: 1; }

.reel-info {
    position: absolute;
    bottom: 68px; left: 14px; right: 58px;
    z-index: 4; color: #fff;
}
.reel-cat-badge {
    display: inline-block;
    background: rgba(0,196,216,0.15);
    border: 1px solid rgba(0,196,216,0.3);
    color: #00c4d8;
    font-size: .58rem; font-weight: 800;
    letter-spacing: 1px; text-transform: uppercase;
    padding: 2px 8px; border-radius: 50px;
    font-family: 'Nunito', sans-serif; margin-bottom: .35rem;
}
.reel-autor {
    font-size: .68rem; font-weight: 800;
    letter-spacing: .8px; text-transform: uppercase;
    color: rgba(255,255,255,0.5);
    font-family: 'Nunito', sans-serif;
    margin-bottom: .2rem;
    display: flex; align-items: center; gap: 4px;
}
.reel-info h3 {
    font-family: 'Nunito', sans-serif;
    font-size: .92rem; font-weight: 900;
    line-height: 1.3; margin: 0 0 .25rem;
    text-shadow: 0 1px 8px rgba(0,0,0,.9);
}
.reel-desc {
    font-family: 'Nunito', sans-serif;
    font-size: .72rem; color: rgba(255,255,255,.55);
    line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
}

.reel-actions {
    position: absolute;
    bottom: 68px; right: 10px;
    z-index: 4;
    display: flex; flex-direction: column;
    gap: 10px; align-items: center;
}
.reel-action-btn {
    display: flex; flex-direction: column;
    align-items: center; gap: 3px;
    background: none; border: none;
    cursor: pointer; padding: 0; text-decoration: none;
}
.reel-action-icon {
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
    color: #fff; transition: transform .15s, background .2s;
}
.reel-action-btn:hover .reel-action-icon {
    background: rgba(255,255,255,0.2);
    transform: scale(1.08);
}
.reel-action-label {
    font-family: 'Nunito', sans-serif;
    font-size: .58rem; font-weight: 700;
    color: rgba(255,255,255,.55);
}

.reel-type-badge {
    position: absolute; top: 11px; left: 11px; z-index: 5;
    font-size: .58rem; font-weight: 800;
    letter-spacing: 1px; text-transform: uppercase;
    padding: 3px 8px; border-radius: 50px;
    font-family: 'Nunito', sans-serif;
    opacity: 0; transition: opacity .3s;
    display: flex; align-items: center; gap: 4px;
}
.reel-card.active:hover .reel-type-badge,
.reel-card.show-ui .reel-type-badge { opacity: 1; }
.reel-type-badge.youtube {
    background: rgba(255,0,0,0.1);
    border: 1px solid rgba(255,80,80,0.2); color: #ff8080;
}
.reel-type-badge.local {
    background: rgba(0,196,216,0.1);
    border: 1px solid rgba(0,196,216,0.2); color: #00c4d8;
}

.reel-play-overlay {
    position: absolute; inset: 0; z-index: 5;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
}
.reel-play-icon {
    width: 58px; height: 58px; border-radius: 50%;
    background: rgba(0,0,0,.5); backdrop-filter: blur(6px);
    border: 1.5px solid rgba(255,255,255,.22);
    display: flex; align-items: center; justify-content: center;
    color: #fff; opacity: 0; transform: scale(.5);
    transition: opacity .25s, transform .25s;
}
.reel-card.show-pause .reel-play-icon { opacity: 1; transform: scale(1); }

.reel-progress {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 2px; background: rgba(255,255,255,.1); z-index: 6;
}
.reel-progress-bar {
    height: 100%; background: #00c4d8; width: 0%;
    transition: width .1s linear;
}

/* Navegación inferior */
.watch-nav {
    position: absolute; bottom: 10px; left: 50%;
    transform: translateX(-50%); z-index: 6;
    display: flex; align-items: center; gap: 8px;
}
.watch-nav-btn {
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff; display: flex; align-items: center;
    justify-content: center; cursor: pointer;
    transition: background .2s;
}
.watch-nav-btn:hover:not(:disabled) { background: rgba(255,255,255,0.2); }
.watch-nav-btn:disabled { opacity: .3; cursor: default; }
.watch-nav-indicator {
    font-family: 'Nunito', sans-serif;
    font-size: .7rem; font-weight: 800;
    color: rgba(255,255,255,.4);
    min-width: 38px; text-align: center;
}

.watch-empty {
    text-align: center; padding: 4rem 2rem;
    color: rgba(255,255,255,.35);
    font-family: 'Nunito', sans-serif;
}
.watch-empty h3 {
    font-size: 1.1rem; font-weight: 800;
    color: rgba(255,255,255,.5); margin-bottom: .4rem;
}

@media (max-width: 768px) {
    .watch-sidebar { display: none; }
    .watch-center { padding: .5rem; }
    .reel-viewport {
        width: 100%;
        max-width: 100%;
        height: auto;
        aspect-ratio: 16 / 9;
        max-height: calc(100vh - 80px);
        border-radius: 12px;
    }
}
</style>

<div class="watch-page">

    <!-- SIDEBAR -->
    <div class="watch-sidebar">
        <div>
            <div class="watch-sidebar-title">▶ <span>WATCH</span></div>
            <div class="watch-sidebar-sub">Vida marina en video</div>
        </div>

        <form class="watch-search" action="?section=watch" method="GET">
            <input type="hidden" name="section" value="watch">
            <?php if ($cat_filter): ?><input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat_filter); ?>"><?php endif; ?>
            <input type="text" name="q" value="<?php echo htmlspecialchars($search_query); ?>"
                   placeholder="Buscar temas, categorías o autores...">
            <button type="submit">Buscar</button>
            <?php if ($search_query): ?>
                <a class="watch-search-clear" href="?section=watch<?php echo $cat_filter ? '&cat='.urlencode($cat_filter) : ''; ?>">Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="watch-counter-box">
            <div class="watch-counter-num" id="watchCounterCurrent">1</div>
            <div class="watch-counter-label">de <?php echo $total; ?> videos</div>
        </div>

        <div class="watch-filters">
            <div class="watch-filters-label">Categorías</div>
            <a href="?section=watch<?php echo $search_query ? '&q='.urlencode($search_query) : ''; ?>"
               class="watch-filter-btn <?php echo !$cat_filter ? 'active' : ''; ?>">Todos</a>
            <?php foreach ($categorias as $cat): ?>
                <a href="?section=watch&cat=<?php echo $cat; ?><?php echo $search_query ? '&q='.urlencode($search_query) : ''; ?>"
                   class="watch-filter-btn <?php echo $cat_filter === $cat ? 'active' : ''; ?>">
                    <?php echo ucfirst($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="watch-keyboard-hint">
            <div class="watch-keyboard-hint-title">Controles</div>
            <div class="watch-key-row"><span class="watch-key">↑ ↓</span><span>Cambiar video</span></div>
            <div class="watch-key-row"><span class="watch-key">Space</span><span>Pausar / Reanudar</span></div>
            <div class="watch-key-row"><span class="watch-key">M</span><span>Silenciar</span></div>
        </div>
    </div>

    <!-- FEED -->
    <div class="watch-center">
        <?php if (empty($videos)): ?>
            <div class="watch-empty">
                <div style="font-size:3rem;margin-bottom:1rem;">🌊</div>
                <h3>No hay videos aún</h3>
                <p>Publica desde tu dashboard.</p>
            </div>
        <?php else: ?>
        <div class="reel-viewport" id="reelViewport">

            <?php foreach ($videos as $i => $v):
                $isYt  = isYoutube($v['video_url']);
                $ytId  = $isYt ? getYoutubeId($v['video_url']) : null;
                $thumb = $isYt ? getYoutubeThumbnail($v['video_url']) : null;
                $localUrl = !$isYt ? htmlspecialchars($v['video_url']) : null;
                $relatedArticleId = findRelatedArticle($conn, $v['titulo'], $v['descripcion'], $v['categoria']);
                $relatedQuery     = rawurlencode(trim($v['titulo'] . ' ' . $v['categoria']));
            ?>
            <div class="reel-card <?php echo $i === 0 ? 'active' : ''; ?>"
                 data-index="<?php echo $i; ?>"
                 data-id="<?php echo $v['id']; ?>"
                 data-type="<?php echo $isYt ? 'youtube' : 'local'; ?>"
                 data-ytid="<?php echo $ytId ?? ''; ?>">

                <div class="reel-type-badge <?php echo $isYt ? 'youtube' : 'local'; ?>">
                    <?php if ($isYt): ?>
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>YouTube
                    <?php else: ?>
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Local
                    <?php endif; ?>
                </div>

                <?php if ($isYt): ?>
                    <?php if ($thumb): ?>
                        <div class="reel-thumb" id="thumb-<?php echo $v['id']; ?>"
                             style="background-image:url('<?php echo htmlspecialchars($thumb); ?>')"></div>
                    <?php else: ?>
                        <div class="reel-thumb-placeholder" id="thumb-<?php echo $v['id']; ?>">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="1"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        </div>
                    <?php endif; ?>
                    <div class="reel-yt-wrap" id="yt-wrap-<?php echo $v['id']; ?>"></div>
                <?php else: ?>
                    <video class="reel-video-local"
                           src="<?php echo $localUrl; ?>"
                           muted loop preload="metadata" playsinline></video>
                <?php endif; ?>

                <div class="reel-gradient"></div>

                <div class="reel-info">
                    <span class="reel-cat-badge"><?php echo htmlspecialchars($v['categoria'] ?? 'general'); ?></span>
                    <div class="reel-autor">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        <?php echo htmlspecialchars($v['autor']); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($v['titulo']); ?></h3>
                    <?php if (!empty($v['descripcion'])): ?>
                        <div class="reel-desc"><?php echo htmlspecialchars($v['descripcion']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="reel-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="database/procesar_like.php">
                        <input type="hidden" name="id_publicacion" value="<?php echo $v['id']; ?>">
                        <input type="hidden" name="redirect" value="?section=watch<?php echo $cat_filter ? '&cat='.$cat_filter : ''; ?>">
                        <button type="submit" class="reel-action-btn" title="Like">
                            <div class="reel-action-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                            </div>
                            <span class="reel-action-label"><?php echo $v['total_likes']; ?></span>
                        </button>
                    </form>
                    <?php else: ?>
                    <a href="?section=login" class="reel-action-btn" title="Like">
                        <div class="reel-action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                        </div>
                        <span class="reel-action-label"><?php echo $v['total_likes']; ?></span>
                    </a>
                    <?php endif; ?>

                    <a href="<?php echo $relatedArticleId ? '?section=articulos&post='.$relatedArticleId.'&scroll=1' : '?section=articulos&q='.$relatedQuery; ?>"
                       class="reel-action-btn" title="Aprender más">
                        <div class="reel-action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19h16M4 5h16M4 12h16"/></svg>
                        </div>
                        <span class="reel-action-label">Aprender</span>
                    </a>
                    <a href="?section=noticias&q=<?php echo $relatedQuery; ?>" class="reel-action-btn" title="Noticias relacionadas">
                        <div class="reel-action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        <span class="reel-action-label">Noticias</span>
                    </a>

                    <button class="reel-action-btn reel-sound-btn" title="Sonido">
                        <div class="reel-action-icon">
                            <svg class="icon-mute" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                            <svg class="icon-sound" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="display:none;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 010 7.07"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                        </div>
                        <span class="reel-action-label">Sonido</span>
                    </button>

                    <button class="reel-action-btn reel-share-btn"
                            data-title="<?php echo htmlspecialchars($v['titulo']); ?>" title="Compartir">
                        <div class="reel-action-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </div>
                        <span class="reel-action-label">Compartir</span>
                    </button>
                </div>

                <div class="reel-play-overlay">
                    <div class="reel-play-icon">
                        <svg class="icon-pause" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                        <svg class="icon-play" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="display:none;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                </div>

                <?php if (!$isYt): ?>
                <div class="reel-progress"><div class="reel-progress-bar"></div></div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>

            <div class="watch-nav">
                <button class="watch-nav-btn" id="btnPrev" disabled>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                </button>
                <span class="watch-nav-indicator" id="navIndicator">1 / <?php echo $total; ?></span>
                <button class="watch-nav-btn" id="btnNext" <?php echo $total <= 1 ? 'disabled' : ''; ?>>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
            </div>

        </div>
        <?php endif; ?>
    </div>

</div>

<script>
(function () {
    const cards = [...document.querySelectorAll('.reel-card')];
    const total = cards.length;
    if (!total) return;

    let current     = 0;
    let globalMuted = false; // estado global de mute, persiste entre videos

    const ytPlayers = {};
    window._ytQueue = [];

    window.onYouTubeIframeAPIReady = function () {
        window._ytApiReady = true;
        window._ytQueue.forEach(fn => fn());
        window._ytQueue = [];
    };

    // ── Crear YT Player ───────────────────────────────────────────────────
    function createYTPlayer(card) {
        const wrap  = card.querySelector('.reel-yt-wrap');
        const ytId  = card.dataset.ytid;
        const vidId = card.dataset.id;
        if (!wrap || !ytId || ytPlayers[vidId]) return;

        const div = document.createElement('div');
        div.id = 'ytp-' + vidId;
        wrap.appendChild(div);

        function build() {
            ytPlayers[vidId] = new YT.Player('ytp-' + vidId, {
                width: '100%',
                height: '100%',
                videoId: ytId,
                playerVars: {
                    autoplay: 1, mute: 0, loop: 1,
                    playlist: ytId, controls: 0,
                    modestbranding: 1, rel: 0, playsinline: 1
                },
                events: {
                    onReady: function(e) {
                        // Aplicar estado global de mute
                        globalMuted ? e.target.mute() : e.target.unMute();
                        e.target.playVideo();
                        // Ocultar thumbnail
                        const thumb = document.getElementById('thumb-' + vidId);
                        if (thumb) thumb.style.opacity = '0';
                        syncSoundIcon(card);
                    }
                }
            });
        }

        window._ytApiReady ? build() : window._ytQueue.push(build);
    }

    function destroyYTPlayer(card) {
        const vidId = card.dataset.id;
        if (ytPlayers[vidId]) {
            try { ytPlayers[vidId].destroy(); } catch(e) {}
            delete ytPlayers[vidId];
        }
        const wrap = card.querySelector('.reel-yt-wrap');
        if (wrap) wrap.innerHTML = '';
        const thumb = document.getElementById('thumb-' + vidId);
        if (thumb) thumb.style.opacity = '1';
    }

    // ── Sincronizar icono de sonido ───────────────────────────────────────
    function syncSoundIcon(card) {
        const btn = card.querySelector('.reel-sound-btn');
        if (!btn) return;
        btn.querySelector('.icon-mute').style.display  = globalMuted  ? '' : 'none';
        btn.querySelector('.icon-sound').style.display = !globalMuted ? '' : 'none';
    }

    // ── Cambiar video activo ──────────────────────────────────────────────
    function goTo(index) {
        const prev = cards[current];
        const next = cards[index];

        // Detener anterior
        prev.classList.remove('active', 'show-ui', 'show-pause');
        if (prev.dataset.type === 'local') {
            const vid = prev.querySelector('.reel-video-local');
            if (vid) vid.pause();
        } else {
            destroyYTPlayer(prev);
        }

        current = index;

        // Iniciar siguiente
        next.classList.add('active');
        if (next.dataset.type === 'local') {
            const vid = next.querySelector('.reel-video-local');
            if (vid) {
                vid.muted = globalMuted;
                vid.play().catch(() => {});
            }
            syncSoundIcon(next);
        } else {
            createYTPlayer(next);
            // syncSoundIcon se llama en onReady
        }

        updateNav();
        showUIBriefly(next);
    }

    let uiTimer;
    function showUIBriefly(card) {
        card.classList.add('show-ui');
        clearTimeout(uiTimer);
        uiTimer = setTimeout(() => card.classList.remove('show-ui'), 2000);
    }

    function updateNav() {
        document.getElementById('navIndicator').textContent = (current + 1) + ' / ' + total;
        document.getElementById('watchCounterCurrent').textContent = current + 1;
        document.getElementById('btnPrev').disabled = current === 0;
        document.getElementById('btnNext').disabled = current === total - 1;
    }

    // ── Navegación ────────────────────────────────────────────────────────
    document.getElementById('btnPrev').addEventListener('click', () => { if (current > 0) goTo(current - 1); });
    document.getElementById('btnNext').addEventListener('click', () => { if (current < total - 1) goTo(current + 1); });

    document.addEventListener('keydown', (e) => {
        if (['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) return;
        switch(e.key) {
            case 'ArrowDown': case 'ArrowRight':
                e.preventDefault(); if (current < total - 1) goTo(current + 1); break;
            case 'ArrowUp': case 'ArrowLeft':
                e.preventDefault(); if (current > 0) goTo(current - 1); break;
            case ' ': e.preventDefault(); togglePlayPause(); break;
            case 'm': case 'M': e.preventDefault(); toggleMute(); break;
        }
    });

    // Swipe
    let touchY = 0;
    const vp = document.getElementById('reelViewport');
    if (vp) {
        vp.addEventListener('touchstart', e => { touchY = e.touches[0].clientY; }, {passive:true});
        vp.addEventListener('touchend', e => {
            const diff = touchY - e.changedTouches[0].clientY;
            if (Math.abs(diff) > 50) {
                if (diff > 0 && current < total - 1) goTo(current + 1);
                if (diff < 0 && current > 0)         goTo(current - 1);
            }
        });
    }

    // ── Play / Pause ──────────────────────────────────────────────────────
    cards.forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.closest('.reel-actions') || e.target.closest('.watch-nav')) return;
            togglePlayPause();
        });
    });

    function togglePlayPause() {
        const card    = cards[current];
        const overlay = card.querySelector('.reel-play-icon');
        const iconP   = overlay ? overlay.querySelector('.icon-pause') : null;
        const iconPl  = overlay ? overlay.querySelector('.icon-play')  : null;

        if (card.dataset.type === 'local') {
            const vid = card.querySelector('.reel-video-local');
            if (!vid) return;
            if (vid.paused) {
                vid.play();
                if (iconP)  iconP.style.display  = '';
                if (iconPl) iconPl.style.display = 'none';
            } else {
                vid.pause();
                if (iconP)  iconP.style.display  = 'none';
                if (iconPl) iconPl.style.display = '';
            }
        } else {
            const player = ytPlayers[card.dataset.id];
            if (!player) return;
            if (player.getPlayerState() === YT.PlayerState.PLAYING) {
                player.pauseVideo();
                if (iconP)  iconP.style.display  = 'none';
                if (iconPl) iconPl.style.display = '';
            } else {
                player.playVideo();
                if (iconP)  iconP.style.display  = '';
                if (iconPl) iconPl.style.display = 'none';
            }
        }
        card.classList.add('show-pause');
        setTimeout(() => card.classList.remove('show-pause'), 700);
    }

    // ── Mute — global, persiste entre videos ──────────────────────────────
    function toggleMute() {
        globalMuted = !globalMuted;
        const card = cards[current];

        if (card.dataset.type === 'local') {
            const vid = card.querySelector('.reel-video-local');
            if (vid) vid.muted = globalMuted;
        } else {
            const player = ytPlayers[card.dataset.id];
            if (player) globalMuted ? player.mute() : player.unMute();
        }
        syncSoundIcon(card);
    }

    document.querySelectorAll('.reel-sound-btn').forEach(btn => {
        btn.addEventListener('click', (e) => { e.stopPropagation(); toggleMute(); });
    });

    // Compartir
    document.querySelectorAll('.reel-share-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const title = btn.dataset.title;
            if (navigator.share) {
                navigator.share({ title, url: window.location.href });
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => alert('¡Enlace copiado!'));
            }
        });
    });

    // Progreso local
    cards.forEach(card => {
        if (card.dataset.type !== 'local') return;
        const vid = card.querySelector('.reel-video-local');
        const bar = card.querySelector('.reel-progress-bar');
        if (!vid || !bar) return;
        vid.addEventListener('timeupdate', () => {
            if (vid.duration) bar.style.width = (vid.currentTime / vid.duration * 100) + '%';
        });
    });

    // ── Arrancar primer video ─────────────────────────────────────────────
    const first = cards[0];
    if (first.dataset.type === 'local') {
        const vid = first.querySelector('.reel-video-local');
        if (vid) { vid.muted = false; vid.play().catch(() => {}); }
        syncSoundIcon(first);
    } else {
        createYTPlayer(first);
    }
    updateNav();
    showUIBriefly(first);

})();
</script>