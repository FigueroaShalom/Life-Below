<?php
// ── CONFIGURACIÓN ──────────────────────────────────────────
// Cargar config de keys (si existe, si no, usar valor por defecto vacío)
if (!file_exists(__DIR__ . '/../config_keys.php')) {
    // Si no existe, usar una key de demostración (limitada)
    define('NEWS_API_KEY', 'demo_key_aqui');
} else {
    require_once __DIR__ . '/../config_keys.php';
}

define('NEWS_CACHE',   __DIR__ . '/../data/noticias_cache.json');
define('CACHE_TTL',    3600); // 1 hora

// ── FUNCIÓN: obtener noticias con caché ────────────────────
function fetchNoticias($query = 'ocean marine life', $lang = 'es', $page = 1) {
    // Verificar si la API key está configurada
    if (NEWS_API_KEY === 'demo_key_aqui') {
        return ['articles' => [], 'error' => true, 'message' => 'API no configurada. Consulta config_keys.example.php'];
    }
    
    // Intentar caché
    $cache_key = md5($query . $lang . $page);
    $cache_file = __DIR__ . '/../data/cache_' . $cache_key . '.json';

    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < CACHE_TTL) {
        return json_decode(file_get_contents($cache_file), true);
    }

    // Crear carpeta data si no existe
    if (!file_exists(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
    }

    $url = 'https://newsapi.org/v2/everything?' . http_build_query([
        'q'        => $query,
        'language' => $lang,
        'sortBy'   => 'publishedAt',
        'pageSize' => 9,
        'page'     => $page,
        'apiKey'   => NEWS_API_KEY,
    ]);

    $ctx = stream_context_create(['http' => [
        'timeout' => 8,
        'header'  => 'User-Agent: HYDRON/1.0',
    ]]);

    $response = @file_get_contents($url, false, $ctx);

    if (!$response) {
        // Si falla, intentar en inglés como fallback
        $url2 = 'https://newsapi.org/v2/everything?' . http_build_query([
            'q'        => 'ocean conservation marine',
            'language' => 'en',
            'sortBy'   => 'publishedAt',
            'pageSize' => 9,
            'page'     => $page,
            'apiKey'   => NEWS_API_KEY,
        ]);
        $response = @file_get_contents($url2, false, $ctx);
    }

    if (!$response) return ['articles' => [], 'error' => true];

    $data = json_decode($response, true);

    // Guardar caché
    file_put_contents($cache_file, json_encode($data));

    return $data;
}

// ── PARÁMETROS ─────────────────────────────────────────────
$sub      = $_GET['sub']  ?? 'noticias';
$page     = max(1, (int)($_GET['page'] ?? 1));

$queries = [
    'noticias'  => 'ocean sea marine conservation',
    'eventos'   => 'ocean environment event conference',
    'proyectos' => 'ocean cleanup project conservation initiative',
];

$query  = $queries[$sub] ?? $queries['noticias'];
$data   = fetchNoticias($query, 'es', $page);

// Si no hay resultados en español, buscar en inglés
if (empty($data['articles'])) {
    $data = fetchNoticias($query, 'en', $page);
}

$articles = array_filter($data['articles'] ?? [], function($a) {
    return !empty($a['title']) && $a['title'] !== '[Removed]';
});
$articles = array_values($articles);
$has_error = !empty($data['error']) || isset($data['status']) && $data['status'] === 'error';
?>

<style>
.hy-news-header { margin-bottom: 2rem; }
.hy-news-tabs {
    display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 2rem;
}
.hy-news-tab {
    padding: 9px 22px; border-radius: 50px;
    border: 1.5px solid var(--border, rgba(0,120,190,0.2));
    color: #0077be; font-weight: 700; font-size: .88rem;
    text-decoration: none; background: #fff;
    transition: all .2s; font-family: 'Nunito', sans-serif;
}
.hy-news-tab:hover,
.hy-news-tab.active {
    background: #0077be; color: #fff; border-color: #0077be;
}

/* Grid de noticias */
.hy-news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}
.hy-news-card {
    background: #fff; border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,40,80,0.1);
    border: 1.5px solid rgba(0,120,190,0.1);
    transition: transform .25s, box-shadow .25s;
    display: flex; flex-direction: column;
}
.hy-news-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,40,80,0.16); }

.hy-news-card-img {
    width: 100%; height: 190px; object-fit: cover;
    background: linear-gradient(135deg, #001828, #003a5c);
}
.hy-news-card-img-placeholder {
    width: 100%; height: 190px;
    background: linear-gradient(135deg, #001828, #003a5c);
    display: flex; align-items: center; justify-content: center;
    font-size: 3rem;
}
.hy-news-card-body { padding: 1.3rem; flex: 1; display: flex; flex-direction: column; }
.hy-news-card-source {
    font-size: .72rem; font-weight: 800; letter-spacing: 1.5px;
    text-transform: uppercase; color: #0077be; margin-bottom: .5rem;
    font-family: 'Nunito', sans-serif;
}
.hy-news-card-title {
    font-size: 1rem; font-weight: 800; color: #001828;
    line-height: 1.4; margin-bottom: .6rem; flex: 1;
    font-family: 'Nunito', sans-serif;
    display: -webkit-box; -webkit-line-clamp: 3;
    -webkit-box-orient: vertical; overflow: hidden;
}
.hy-news-card-desc {
    font-size: .84rem; color: #5a7a9a; line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
    font-family: 'Nunito', sans-serif;
}
.hy-news-card-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: auto; padding-top: .8rem;
    border-top: 1px solid rgba(0,120,190,0.1);
}
.hy-news-card-date { font-size: .75rem; color: #5a7a9a; font-family: 'Nunito', sans-serif; }
.hy-news-card-link {
    display: inline-block; padding: 6px 16px;
    background: #0077be; color: #fff; border-radius: 50px;
    font-size: .78rem; font-weight: 800; text-decoration: none;
    transition: background .2s; font-family: 'Nunito', sans-serif;
}
.hy-news-card-link:hover { background: #009aaa; }

/* Paginación */
.hy-pagination {
    display: flex; justify-content: center; gap: .6rem;
    margin-top: 3rem; flex-wrap: wrap;
}
.hy-page-btn {
    padding: 9px 18px; border-radius: 50px;
    border: 1.5px solid rgba(0,120,190,0.25);
    color: #0077be; font-weight: 700; font-size: .88rem;
    text-decoration: none; background: #fff;
    transition: all .2s; font-family: 'Nunito', sans-serif;
}
.hy-page-btn:hover,
.hy-page-btn.active { background: #0077be; color: #fff; border-color: #0077be; }

/* Estado vacío / error */
.hy-news-empty {
    text-align: center; padding: 4rem 2rem;
    background: #fff; border-radius: 16px;
    border: 1.5px solid rgba(0,120,190,0.1);
}
.hy-news-empty h3 { font-size: 1.3rem; color: #001828; margin-bottom: .5rem; font-family: 'Nunito', sans-serif; }
.hy-news-empty p  { color: #5a7a9a; font-family: 'Nunito', sans-serif; }

/* Badge API live */
.hy-api-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(0,200,100,0.1); border: 1px solid rgba(0,200,100,0.25);
    color: #006640; font-size: .75rem; font-weight: 700;
    padding: 4px 12px; border-radius: 50px;
    font-family: 'Nunito', sans-serif;
}
.hy-api-badge::before {
    content: ''; width: 7px; height: 7px; border-radius: 50%;
    background: #00c864;
    animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%,100% { opacity: 1; } 50% { opacity: .3; }
}
</style>

<!-- ══ PÁGINA ══ -->
<div style="max-width:1280px;margin:0 auto;padding:3rem 2rem;">

    <!-- Header -->
    <div class="hy-news-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
        <div>
            <h1 style="font-family:'Nunito',sans-serif;font-weight:900;font-size:2rem;color:#001828;letter-spacing:-.5px;">
                Noticias del Océano
            </h1>
            <p style="color:#5a7a9a;font-family:'Nunito',sans-serif;font-size:.9rem;margin-top:.3rem;">
                Últimas noticias sobre conservación marina y vida submarina
            </p>
        </div>
        <span class="hy-api-badge">Live · NewsAPI</span>
    </div>

    <!-- Tabs -->
    <div class="hy-news-tabs">
        <a href="?section=noticias&sub=noticias"
           class="hy-news-tab <?php echo $sub==='noticias'?'active':''; ?>">📰 Noticias</a>
        <a href="?section=noticias&sub=eventos"
           class="hy-news-tab <?php echo $sub==='eventos'?'active':''; ?>">📅 Eventos</a>
        <a href="?section=noticias&sub=proyectos"
           class="hy-news-tab <?php echo $sub==='proyectos'?'active':''; ?>">🌱 Proyectos</a>
    </div>

    <?php if ($has_error): ?>
        <div class="hy-news-empty">
            <h3>⚠️ No se pudo conectar a la API</h3>
            <p>Verifica tu conexión a internet o intenta de nuevo más tarde.</p>
        </div>

    <?php elseif (empty($articles)): ?>
        <div class="hy-news-empty">
            <h3>🌊 No hay noticias disponibles</h3>
            <p>Intenta con otra categoría o vuelve más tarde.</p>
        </div>

    <?php else: ?>
        <div class="hy-news-grid">
            <?php foreach ($articles as $article): ?>
                <?php
                $date = !empty($article['publishedAt'])
                    ? date('d M Y', strtotime($article['publishedAt']))
                    : '';
                $source = $article['source']['name'] ?? 'Fuente desconocida';
                $img    = $article['urlToImage'] ?? '';
                $title  = $article['title']       ?? '';
                $desc   = $article['description'] ?? '';
                $url    = $article['url']          ?? '#';
                ?>
                <div class="hy-news-card">
                    <?php if ($img): ?>
                        <img src="<?php echo htmlspecialchars($img); ?>"
                             alt="<?php echo htmlspecialchars($title); ?>"
                             class="hy-news-card-img"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="hy-news-card-img-placeholder" style="display:none;">🌊</div>
                    <?php else: ?>
                        <div class="hy-news-card-img-placeholder">🌊</div>
                    <?php endif; ?>

                    <div class="hy-news-card-body">
                        <div class="hy-news-card-source"><?php echo htmlspecialchars($source); ?></div>
                        <div class="hy-news-card-title"><?php echo htmlspecialchars($title); ?></div>
                        <?php if ($desc): ?>
                            <div class="hy-news-card-desc"><?php echo htmlspecialchars($desc); ?></div>
                        <?php endif; ?>
                        <div class="hy-news-card-footer">
                            <span class="hy-news-card-date">📅 <?php echo $date; ?></span>
                            <a href="<?php echo htmlspecialchars($url); ?>"
                               target="_blank" rel="noopener"
                               class="hy-news-card-link">Leer →</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <div class="hy-pagination">
            <?php if ($page > 1): ?>
                <a href="?section=noticias&sub=<?php echo $sub; ?>&page=<?php echo $page-1; ?>"
                   class="hy-page-btn">← Anterior</a>
            <?php endif; ?>
            <?php for ($i = max(1,$page-2); $i <= $page+2; $i++): ?>
                <a href="?section=noticias&sub=<?php echo $sub; ?>&page=<?php echo $i; ?>"
                   class="hy-page-btn <?php echo $i===$page?'active':''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <a href="?section=noticias&sub=<?php echo $sub; ?>&page=<?php echo $page+1; ?>"
               class="hy-page-btn">Siguiente →</a>
        </div>

    <?php endif; ?>

</div>