<?php
// INCLUDES/noticias.php
if (!file_exists(__DIR__ . '/../config_keys.php')) {
    define('GNEWS_API_KEY', 'd5b5320a0accff00272ab27733ba94ce');
} else {
    require_once __DIR__ . '/../config_keys.php';
}

define('CACHE_TTL', 3600); 

function fetchNoticias($query, $page = 1) {
    if (!defined('GNEWS_API_KEY') || GNEWS_API_KEY === 'TU_GNEWS_API_KEY_AQUI') {
        return ['articles' => [], 'error' => true, 'message' => 'API GNews no configurada.'];
    }

    $cache_key  = md5($query . $page);
    $cache_file = __DIR__ . '/../data/gnews_cache_' . $cache_key . '.json';

    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < CACHE_TTL) {
        return json_decode(file_get_contents($cache_file), true);
    }

    if (!file_exists(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
    }

    // Si no hay búsqueda, priorizar México
    $final_query = $query;
    if (empty(trim($query)) || $query === '"vida marina" OR "biología marina" OR "arrecifes de coral"') {
        $final_query = '(ocean OR "vida marina" OR conservacion) AND Mexico';
    }

    $url = 'https://gnews.io/api/v4/search?' . http_build_query([
        'q' => $final_query,
        'lang'   => 'es', 
        'max' => 12,
        'token' => GNEWS_API_KEY,
    ]);

    $ctx = stream_context_create(['http' => [
        'timeout' => 12,
        'header'  => 'User-Agent: HYDRON/1.0',
    ]]);

    $response = @file_get_contents($url, false, $ctx);
    if (!$response) return ['articles' => [], 'error' => true];

    $data = json_decode($response, true);
    file_put_contents($cache_file, json_encode($data));
    return $data;
}

// Eventos curados
function fetchEventosCurados($page = 1) {
    $eventos = [
        [
            'title'       => 'Festival Internacional de Ballenas',
            'description' => 'Avistamiento de ballenas grises en su ruta de migración por el Pacífico mexicano.',
            'date'        => 'Enero – Marzo',
            'location'    => 'Bahía Magdalena, BCS',
            'url'         => '#',
            'type'        => 'Conservación',
            'img'         => 'https://images.unsplash.com/photo-1568430462989-44163eb1752f?w=600&q=80',
        ],
        [
            'title'       => 'Limpieza de Playa · Costa Pacífico',
            'description' => 'Jornadas comunitarias de limpieza costera en playas de Jalisco y Nayarit.',
            'date'        => 'Trimestral',
            'location'    => 'Jalisco, México',
            'url'         => '#',
            'type'        => 'Voluntariado',
            'img'         => 'https://images.unsplash.com/photo-1618477461853-cf6ed80fafa5?w=600&q=80',
        ],
        [
            'title'       => 'Semana del Océano · Cancún',
            'description' => 'Conferencias y talleres de buceo científico en el Caribe mexicano.',
            'date'        => 'Junio',
            'location'    => 'Cancún, QRoo',
            'url'         => '#',
            'type'        => 'Educación',
            'img'         => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&q=80',
        ],
    ];

    $per_page = 6;
    $offset   = ($page - 1) * $per_page;
    $slice    = array_slice($eventos, $offset, $per_page);

    return [
        'events'   => $slice,
        'total'    => count($eventos),
        'per_page' => $per_page,
    ];
}

$sub          = $_GET['sub']  ?? 'noticias';
$page         = max(1, (int)($_GET['page'] ?? 1));
$search_query = trim($_GET['q'] ?? '');

if ($sub === 'eventos') {
    $eventos_data = fetchEventosCurados($page);
    $eventos      = $eventos_data['events'] ?? [];
} else {
    $base_query = $search_query ?: '"vida marina" OR "biología marina" OR "arrecifes de coral"';
    $data     = fetchNoticias($base_query, $page);
    $articles = $data['articles'] ?? [];
    $has_error = empty($articles) && !empty($data['error']);
}
?>

<style>
.hy-news-header { background: linear-gradient(135deg, var(--navy), #004466); padding: 4rem 2rem; color: #fff; text-align: center; border-radius: 0 0 40px 40px; margin-bottom: 3rem; }
.hy-search-container { max-width: 600px; margin: 1.5rem auto 0; position: relative; }
.hy-search-input { width: 100%; padding: 14px 24px; padding-right: 60px; border-radius: 50px; border: none; font-size: 1rem; font-family: var(--font); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.hy-search-btn { position: absolute; right: 8px; top: 8px; background: var(--ocean); color: #fff; border: none; width: 42px; height: 42px; border-radius: 50%; cursor: pointer; transition: background 0.2s; }
.hy-search-btn:hover { background: var(--teal); }

.hy-tab-nav { display: flex; justify-content: center; gap: .8rem; margin-bottom: 2.5rem; }
.hy-tab { padding: 10px 24px; border-radius: 50px; border: 1.5px solid var(--border); color: var(--ocean); font-weight: 800; text-decoration: none; background: #fff; transition: all .2s; }
.hy-tab.active { background: var(--ocean); color: #fff; border-color: var(--ocean); }

.hy-news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; }
.hy-news-card { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow); border: 1.5px solid var(--border); display: flex; flex-direction: column; transition: transform 0.3s; }
.hy-news-card:hover { transform: translateY(-5px); }
.hy-news-card-img { width: 100%; height: 200px; object-fit: cover; background: #eee; }
.hy-news-card-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
.hy-news-card-source { font-size: .75rem; color: var(--ocean); font-weight: 800; text-transform: uppercase; margin-bottom: .5rem; }
.hy-news-card-title { font-size: 1.1rem; font-weight: 900; color: var(--navy); line-height: 1.4; margin-bottom: .8rem; }
.hy-news-card-desc { font-size: .9rem; color: var(--muted); line-height: 1.6; margin-bottom: 1.2rem; }
.hy-news-card-link { margin-top: auto; display: inline-block; padding: 8px 20px; background: var(--navy); color: #fff; border-radius: 50px; font-size: .85rem; font-weight: 700; text-decoration: none; align-self: flex-start; }
</style>

<div class="hy-news-header">
    <h1 style="font-size: 2.5rem; font-weight: 900;"><?php echo $sub === 'eventos' ? 'Eventos Marinos' : 'Noticias del Océano'; ?></h1>
    <p style="opacity: 0.8; margin-top: 0.5rem;">Explora los últimos acontecimientos de la conservación marina<?php echo empty($search_query) ? ' en México' : ''; ?>.</p>
    
    <?php if ($sub !== 'eventos'): ?>
    <form action="index.php" method="GET" class="hy-search-container">
        <input type="hidden" name="section" value="noticias">
        <input type="text" name="q" class="hy-search-input" placeholder="Buscar noticias (ej: arrecifes, tiburones...)" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit" class="hy-search-btn"><i class="fas fa-search"></i></button>
    </form>
    <?php endif; ?>
</div>

<div style="max-width:1280px; margin:0 auto; padding:0 2rem 4rem;">
    <div class="hy-tab-nav">
        <a href="?section=noticias&sub=noticias" class="hy-tab <?php echo $sub==='noticias'?'active':''; ?>">Noticias</a>
        <a href="?section=noticias&sub=eventos" class="hy-tab <?php echo $sub==='eventos'?'active':''; ?>">Eventos</a>
    </div>

    <?php if ($sub === 'eventos'): ?>
        <div class="hy-news-grid">
            <?php foreach ($eventos as $ev): ?>
                <div class="hy-news-card">
                    <img src="<?php echo $ev['img']; ?>" class="hy-news-card-img" alt="<?php echo $ev['title']; ?>">
                    <div class="hy-news-card-body">
                        <span class="hy-news-card-source"><?php echo $ev['type']; ?></span>
                        <h3 class="hy-news-card-title"><?php echo $ev['title']; ?></h3>
                        <p class="hy-news-card-desc"><?php echo $ev['description']; ?></p>
                        <div style="margin-top:auto; font-size:.85rem; color:var(--navy); font-weight: 700;">
                            <span>📍 <?php echo $ev['location']; ?></span><br>
                            <span>📅 <?php echo $ev['date']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php if ($has_error): ?>
            <div style="text-align:center; padding:3rem; background:#fff; border-radius:20px; border:1.5px solid var(--border);">
                <i class="fas fa-exclamation-circle" style="font-size:3rem; color:var(--ocean); margin-bottom:1rem;"></i>
                <h3>No pudimos encontrar noticias</h3>
                <p>Intenta con otros términos de búsqueda o revisa tu conexión.</p>
            </div>
        <?php else: ?>
            <div class="hy-news-grid">
                <?php foreach ($articles as $art): ?>
                    <div class="hy-news-card">
                        <?php if(!empty($art['image'])): ?>
                            <img src="<?php echo $art['image']; ?>" class="hy-news-card-img" alt="Noticia">
                        <?php else: ?>
                            <div class="hy-news-card-img" style="display:flex; align-items:center; justify-content:center; background:#f0f8ff; color:var(--ocean);">
                                <i class="fas fa-water fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        <div class="hy-news-card-body">
                            <span class="hy-news-card-source"><?php echo $art['source']['name'] ?? 'Fuente'; ?></span>
                            <h3 class="hy-news-card-title"><?php echo $art['title']; ?></h3>
                            <p class="hy-news-card-desc"><?php echo $art['description']; ?></p>
                            <a href="<?php echo $art['url']; ?>" target="_blank" class="hy-news-card-link">Leer noticia →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>