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

    $url = 'https://gnews.io/api/v4/search?' . http_build_query([
        'q' => $query,
        'lang'   => 'es', 
        'max' => 10,
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
.hy-tab { padding: 9px 22px; border-radius: 50px; border: 1.5px solid rgba(0,120,190,0.1); color: #0077be; font-weight: 800; text-decoration: none; background: #fff; transition: all .2s; }
.hy-tab.active { background: #0077be; color: #fff; border-color: #0077be; }
.hy-news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
.hy-news-card { background: #fff; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1.5px solid rgba(0,120,190,0.1); display: flex; flex-direction: column; }
.hy-news-card-img { width: 100%; height: 190px; object-fit: cover; background: #001828; }
.hy-news-card-body { padding: 1.3rem; flex: 1; display: flex; flex-direction: column; }
.hy-news-card-title { font-size: 1rem; font-weight: 900; color: #001828; line-height: 1.4; margin-bottom: .6rem; }
.hy-news-card-link { display: inline-block; padding: 7px 18px; background: #0077be; color: #fff; border-radius: 50px; font-size: .78rem; font-weight: 900; text-decoration: none; }
</style>

<div style="max-width:1280px; margin:0 auto; padding:3rem 2rem;">
    <h1 style="font-family:'Nunito',sans-serif;font-weight:900;font-size:2rem;color:#001828;margin-bottom:1.5rem;">
        <?php echo $sub === 'eventos' ? 'Eventos Marinos' : 'Noticias del Océano'; ?>
    </h1>

    <div style="display:flex; gap:.5rem; margin-bottom:2rem;">
        <a href="?section=noticias&sub=noticias" class="hy-tab <?php echo $sub==='noticias'?'active':''; ?>">Noticias</a>
        <a href="?section=noticias&sub=eventos" class="hy-tab <?php echo $sub==='eventos'?'active':''; ?>">Eventos</a>
    </div>

    <?php if ($sub === 'eventos'): ?>
        <div class="hy-news-grid">
            <?php foreach ($eventos as $ev): ?>
                <div class="hy-news-card">
                    <img src="<?php echo $ev['img']; ?>" class="hy-news-card-img">
                    <div class="hy-news-card-body">
                        <span style="font-size:.7rem; color:#0077be; font-weight:900; text-transform:uppercase;"><?php echo $ev['type']; ?></span>
                        <h3 class="hy-news-card-title"><?php echo $ev['title']; ?></h3>
                        <p style="font-size:.85rem; color:#5a7a9a;"><?php echo $ev['description']; ?></p>
                        <div style="margin-top:auto; font-size:.8rem; color:#001828;">📍 <?php echo $ev['location']; ?> | 📅 <?php echo $ev['date']; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="hy-news-grid">
            <?php foreach ($articles as $art): ?>
                <div class="hy-news-card">
                    <?php if(!empty($art['image'])): ?>
                        <img src="<?php echo $art['image']; ?>" class="hy-news-card-img">
                    <?php endif; ?>
                    <div class="hy-news-card-body">
                        <span style="font-size:.7rem; color:#0077be; font-weight:900;"><?php echo $art['source']['name']; ?></span>
                        <h3 class="hy-news-card-title"><?php echo $art['title']; ?></h3>
                        <p style="font-size:.85rem; color:#5a7a9a;"><?php echo $art['description']; ?></p>
                        <a href="<?php echo $art['url']; ?>" target="_blank" class="hy-news-card-link">Leer más →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>