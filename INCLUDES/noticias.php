<?php
// ── CONFIGURACIÓN ──────────────────────────────────────────
if (!file_exists(__DIR__ . '/../config_keys.php')) {
    define('GNEWS_API_KEY', 'd5b5320a0accff00272ab27733ba94ce');
} else {
    require_once __DIR__ . '/../config_keys.php';
}

define('CACHE_TTL', 3600); // 1 hora


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
        'lang'   => 'es', // <-- CANDADO 1: Solo español (adiós portugués)
        'in'     => 'title,description', // <-- CANDADO 2: Solo busca en el título o resumen
         'sortby' => 'publishedAt',
         'max' => 10,
         'from' => date('Y-m-d', strtotime('-60 days')),
         'apikey' => GNEWS_API_KEY,
]);

    $ctx = stream_context_create(['http' => [
        'timeout' => 12,
        'header'  => 'User-Agent: HYDRON/1.0',
    ]]);

    $response = @file_get_contents($url, false, $ctx);

    if (!$response) {
        return ['articles' => [], 'error' => true];
    }

    $data = json_decode($response, true);
    file_put_contents($cache_file, json_encode($data));

    return $data;
}

// ── FUNCIÓN: eventos marinos en México ────────────────────
function fetchEventos($page = 1) {
    if (defined('TICKETMASTER_API_KEY') && TICKETMASTER_API_KEY !== 'TU_TICKETMASTER_KEY_AQUI') {
        $cache_key  = 'eventos_mx_' . $page;
        $cache_file = __DIR__ . '/../data/eventos_cache_' . $cache_key . '.json';

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < CACHE_TTL) {
            return json_decode(file_get_contents($cache_file), true);
        }

        $url = 'https://app.ticketmaster.com/discovery/v2/events.json?' . http_build_query([
            'keyword'           => 'ocean conservation playa marina',
            'countryCode'       => 'MX',
            'classificationName'=> 'Miscellaneous',
            'size'              => 9,
            'page'              => $page - 1,
            'apikey'            => TICKETMASTER_API_KEY,
        ]);

        $ctx      = stream_context_create(['http' => ['timeout' => 10, 'header' => 'User-Agent: HYDRON/1.0']]);
        $response = @file_get_contents($url, false, $ctx);

        if ($response) {
            $data   = json_decode($response, true);
            $events = $data['_embedded']['events'] ?? [];
            if (!empty($events)) {
                file_put_contents($cache_file, json_encode($data));
                return ['events' => $events, 'ticketmaster' => true];
            }
        }
    }

    return fetchEventosCurados($page);
}


function fetchEventosCurados($page = 1) {
    $eventos = [
        [
            'title'       => 'Festival Internacional de Ballenas',
            'description' => 'Avistamiento de ballenas grises en su ruta de migración por el Pacífico mexicano. Kayak, snorkel y talleres de conservación marina.',
            'date'        => 'Enero – Marzo (anual)',
            'location'    => 'Bahía Magdalena, Baja California Sur',
            'url'         => 'https://es.golapaz.com/que-hacer/atracciones-y-tours/avistamiento-de-ballenas/',
            'type'        => 'Avistamiento · Conservación',
            'img'         => 'https://assets.simpleviewinc.com/simpleview/image/upload/c_fill,f_jpg,h_709,q_65,w_1260/v1/clients/lapazmexico/tour_ballena_gris_fitupaz_9__1c43e243-c6ac-4917-ab24-cff18395c3a5.jpg', // <-- CAMBIA ESTA IMAGEN
        ],
        [
            'title'       => 'Limpieza de Playa · Costa Pacífico',
            'description' => 'Jornadas comunitarias de limpieza costera en playas de Jalisco, Nayarit y Guerrero coordinadas por organizaciones ambientales.',
            'date'        => 'Cada trimestre',
            'location'    => 'Costas de Jalisco, Nayarit y Guerrero',
            'url'         => 'https://mejoratuplaya.com/', 
            'type'        => 'Voluntariado · Playa',
            'img'         => 'https://mejoratuplaya.com/cdn/shop/files/limpieza-playa-la-pesca-tochito-fest-mejora-tu-playa-2025_37.jpg?v=1751651250&width=2048',
        ],
        [
            'title'       => 'Semana del Océano · Cancún',
            'description' => 'Conferencias, talleres de buceo científico y exposición fotográfica submarina en el Caribe mexicano.',
            'date'        => 'Junio (Día Mundial del Océano)',
            'location'    => 'Cancún, Quintana Roo',
            'url'         => 'https://www.festivaldelosoceanos.org.mx/', 
            'type'        => 'Conferencia · Educación',
            'img'         => 'https://static.wixstatic.com/media/e15f4b_49e3aad001df46b093b7db7f300d5c34~mv2.jpg/v1/fill/w_1131,h_1002,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/e15f4b_49e3aad001df46b093b7db7f300d5c34~mv2.jpg',
        ],
        [
            'title'       => 'Monitoreo y Liberación de Tortugas',
            'description' => 'En Manzanillo, la conservación y liberación de tortugas marinas se gestiona principalmente a través del Tortugario Manzanillo (ubicado en Playa Azul) y el Centro Ecológico El Tortugario (en Cuyutlán, Armería), en colaboración con la Universidad de Colima y la Secretaría de Marina..',
            'date'        => 'Mayo – Octubre (temporada de anidación)',
            'location'    => 'Playa Azul, Manzanillo, Colima',
            'url'         => 'https://www.entornoturistico.com/2-tortugarios-en-colima-para-liberar-tortugas/', 
            'type'        => 'Conservación · Voluntariado',
            'img'         => 'https://programadestinosmexico.com/wp-content/uploads/2023/11/Liberacion-de-tortugas-en-Manzanillo1.jpg',
        ],
        [
            'title'       => 'Congreso Mexicano de Arrecifes de Coral',
            'description' => 'Encuentro científico anual para investigadores, gestores de Áreas Naturales Protegidas y sociedad civil sobre el estado de los arrecifes mesoamericanos.',
            'date'        => 'Agosto (anual)',
            'location'    => 'Puerto Morelos, Quintana Roo',
            'url'         => 'https://somac.org.mx/', 
            'type'        => 'Congreso · Ciencia',
            'img'         => 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?w=600&q=80', 
        ],
        [
            'title'       => 'Festival Vive el Mar · Mazatlán',
            'description' => 'Feria de educación ambiental marina con exhibiciones, charlas y actividades para niños y familias en el puerto de Mazatlán.',
            'date'        => 'Octubre',
            'location'    => 'Mazatlán, Sinaloa',
            'url'         => 'https://punto.mx/2026/04/22/mazatlan-alza-velas-regata-abre-camino-al-festival-del-mar/', 
            'type'        => 'Festival · Educación',
            'img'         => 'https://ba8a95d4aa7b000bdb27-a4e6f9e1710496f0fd0180f011e6f4e2.ssl.cf1.rackcdn.com/responsive/Native/1200px/u/blogs-assets/Mazatlan_City_9-X2.jpg',
        ],
        [
            'title'       => 'Expedición de Buceo Científico',
            'description' => 'Expedición de documentación submarina en el "acuario del mundo". Cupos limitados para buceadores certificados con enfoque en biodiversidad.',
            'date'        => 'Noviembre',
            'location'    => 'La Paz, Baja California Sur',
            'url'         => 'https://mexicotraveladventure.com/es/aventura/buceo-la-paz/', 
            'type'        => 'Expedición · Ciencia',
            'img'         => 'https://mexicotraveladventure.com/wp-content/uploads/2025/11/semana-de-buceo-en-la-paz-y-cabo-pulmo-2048x1365.webp',
        ],
        [
            'title'       => 'Festival Costero del Papalote: el cielo de Manzanillo cobra vida',
            'description' => 'En Manzanillo, el cielo también es parte del paisaje. Cada año, este puerto colimense se convierte en el escenario del Festival Costero del Papalote, una celebración que llena de color, movimiento y alegría la costa del Pacífico.',
            'date'        => 'Mayo-Junio',
            'location'    => 'Playa Azul, Manzanillo, Colima',
            'url'         => 'https://visitmanzanillo.mx/festival-costero-del-papalote-el-cielo-de-manzanillo-cobra-vida/', 
            'type'        => 'Festival · turismo Sostenible',
            'img'         => 'https://visitmanzanillo.mx/wp-content/uploads/2025/07/Papalote-dron-Domingo-10.jpg',
        ],
        
    ];

    $per_page = 6;
    $offset   = ($page - 1) * $per_page;
    $slice    = array_slice($eventos, $offset, $per_page);

    return [
        'events'   => $slice,
        'curated'  => true,
        'total'    => count($eventos),
        'per_page' => $per_page,
    ];
}

// ── PARÁMETROS ─────────────────────────────────────────────
$sub          = $_GET['sub']  ?? 'noticias';
$page         = max(1, (int)($_GET['page'] ?? 1));
$search_query = trim($_GET['q'] ?? '');

// ── LÓGICA ─────────────────────────────────────────────────
$articles     = [];
$eventos      = [];
$has_error    = false;
$eventos_data = [];

if ($sub === 'eventos') {
    $eventos_data = fetchEventos($page);
    $eventos      = $eventos_data['events'] ?? [];
} else {
    // ── LÓGICA PARA QUE EL USUARIO NO SE SALGA DEL TEMA ──
    if ($search_query !== '') {
        // Forzamos a que su búsqueda TENGA que incluir palabras acuáticas
        $base_query = '(' . $search_query . ') AND (mar OR océano OR marina OR submarino)';
    } else {
        // Noticias por defecto: súper estrictas
        $base_query = '"vida marina" OR "biología marina" OR "arrecifes de coral" OR "especies marinas"';
    }

    $data     = fetchNoticias($base_query, $page);
    $raw      = $data['articles'] ?? [];
    $articles = array_values(array_filter($raw, fn($a) =>
        !empty($a['title']) && $a['title'] !== '[Removed]'
    ));

    $has_error = empty($articles) && !empty($data['error']);
}
?>

<style>
:root {
    --ocean-deep:   #001828;
    --ocean-mid:    #003a5c;
    --ocean-blue:   #0077be;
    --ocean-teal:   #009aaa;
    --ocean-light:  #e8f4fd;
    --text-body:    #5a7a9a;
    --border-soft:  rgba(0,120,190,0.13);
    --card-shadow:  0 4px 28px rgba(0,40,80,0.10);
    --card-hover:   0 14px 44px rgba(0,40,80,0.18);
    --radius-card:  18px;
    --font-main:    'Nunito', sans-serif;
}

.hy-section-header {
    display: flex; align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;
}
.hy-section-title {
    font-family: var(--font-main); font-weight: 900;
    font-size: 2rem; color: var(--ocean-deep);
    letter-spacing: -.5px; line-height: 1.1; margin: 0;
}
.hy-section-sub {
    color: var(--text-body); font-family: var(--font-main);
    font-size: .9rem; margin-top: .3rem;
}

.hy-tabs-row {
    display: flex; align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;
}
.hy-tabs { display: flex; gap: .45rem; flex-wrap: wrap; }
.hy-tab {
    padding: 9px 22px; border-radius: 50px;
    border: 1.5px solid var(--border-soft);
    color: var(--ocean-blue); font-weight: 800;
    font-size: .87rem; text-decoration: none;
    background: #fff; transition: all .22s;
    font-family: var(--font-main);
}
.hy-tab:hover, .hy-tab.active {
    background: var(--ocean-blue); color: #fff;
    border-color: var(--ocean-blue);
    box-shadow: 0 4px 16px rgba(0,119,190,0.22);
}

.hy-search-form { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; }
.hy-search-input {
    padding: .72rem 1.1rem; border-radius: 50px;
    border: 1.5px solid var(--border-soft);
    font-size: .92rem; min-width: 220px;
    font-family: var(--font-main); color: var(--ocean-deep);
    background: #fff; outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.hy-search-input:focus {
    border-color: var(--ocean-blue);
    box-shadow: 0 0 0 3px rgba(0,119,190,0.12);
}
.hy-btn {
    padding: 9px 20px; border-radius: 50px;
    border: 1.5px solid var(--border-soft);
    color: var(--ocean-blue); font-weight: 800;
    font-size: .87rem; text-decoration: none;
    background: #fff; cursor: pointer;
    transition: all .22s; font-family: var(--font-main);
}
.hy-btn:hover { background: var(--ocean-blue); color: #fff; border-color: var(--ocean-blue); }
.hy-btn-primary { background: var(--ocean-blue); color: #fff; border-color: var(--ocean-blue); }
.hy-btn-primary:hover { background: var(--ocean-teal); border-color: var(--ocean-teal); }

/* ── Noticias ─────────────────────────────────────────── */
.hy-news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}
.hy-news-card {
    background: #fff; border-radius: var(--radius-card);
    overflow: hidden; box-shadow: var(--card-shadow);
    border: 1.5px solid var(--border-soft);
    transition: transform .25s, box-shadow .25s;
    display: flex; flex-direction: column;
}
.hy-news-card:hover { transform: translateY(-5px); box-shadow: var(--card-hover); }
.hy-news-card-img-wrap {
    position: relative; width: 100%; height: 190px;
    overflow: hidden; flex-shrink: 0;
    background: linear-gradient(135deg, var(--ocean-deep), var(--ocean-mid));
}
.hy-news-card-img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s ease;
}
.hy-news-card:hover .hy-news-card-img { transform: scale(1.04); }
.hy-news-card-img-placeholder {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, #001828, #003a5c);
}
.hy-lang-badge {
    position: absolute; top: 10px; right: 10px;
    padding: 3px 10px; border-radius: 50px;
    font-size: .68rem; font-weight: 900;
    letter-spacing: .6px; text-transform: uppercase;
    font-family: var(--font-main);
}
.hy-lang-badge.es { background: rgba(0,68,130,0.85); color: #fff; }
.hy-lang-badge.en { background: rgba(0,100,60,0.85);  color: #fff; }

.hy-news-card-body { padding: 1.3rem; flex: 1; display: flex; flex-direction: column; }
.hy-news-card-source {
    font-size: .71rem; font-weight: 900; letter-spacing: 1.4px;
    text-transform: uppercase; color: var(--ocean-blue);
    margin-bottom: .45rem; font-family: var(--font-main);
}
.hy-news-card-title {
    font-size: 1rem; font-weight: 900; color: var(--ocean-deep);
    line-height: 1.4; margin-bottom: .55rem; flex: 1;
    font-family: var(--font-main);
    display: -webkit-box; -webkit-line-clamp: 3;
    -webkit-box-orient: vertical; overflow: hidden;
}
.hy-news-card-desc {
    font-size: .84rem; color: var(--text-body); line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
    font-family: var(--font-main);
}
.hy-news-card-footer {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-top: auto; padding-top: .8rem;
    border-top: 1px solid var(--border-soft);
}
.hy-news-card-date { font-size: .74rem; color: var(--text-body); font-family: var(--font-main); }
.hy-news-card-link {
    display: inline-block; padding: 7px 18px;
    background: var(--ocean-blue); color: #fff;
    border-radius: 50px; font-size: .78rem; font-weight: 900;
    text-decoration: none; transition: background .2s, transform .15s;
    font-family: var(--font-main);
}
.hy-news-card-link:hover { background: var(--ocean-teal); transform: translateX(2px); }

/* ── Eventos ──────────────────────────────────────────── */
.hy-events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 1.5rem;
}
.hy-event-card {
    background: #fff; border-radius: var(--radius-card);
    overflow: hidden; box-shadow: var(--card-shadow);
    border: 1.5px solid var(--border-soft);
    transition: transform .25s, box-shadow .25s;
    display: flex; flex-direction: column;
}
.hy-event-card:hover { transform: translateY(-5px); box-shadow: var(--card-hover); }

.hy-event-img-wrap {
    position: relative; width: 100%; height: 200px;
    overflow: hidden; flex-shrink: 0;
    background: linear-gradient(135deg, var(--ocean-deep), var(--ocean-mid));
}
.hy-event-img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s ease;
}
.hy-event-card:hover .hy-event-img { transform: scale(1.04); }
.hy-event-img-placeholder {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, #001828, #003a5c);
}
.hy-event-type-badge {
    position: absolute; bottom: 10px; left: 12px;
    padding: 4px 13px; border-radius: 50px;
    background: rgba(0,20,40,0.72);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.18);
    color: #fff; font-size: .68rem; font-weight: 800;
    letter-spacing: .7px; text-transform: uppercase;
    font-family: var(--font-main);
}
.hy-event-card-body { padding: 1.3rem; flex: 1; display: flex; flex-direction: column; }
.hy-event-title {
    font-size: 1.05rem; font-weight: 900; color: var(--ocean-deep);
    line-height: 1.35; margin-bottom: .6rem; font-family: var(--font-main);
}
.hy-event-desc {
    font-size: .85rem; color: var(--text-body); line-height: 1.6;
    margin-bottom: 1rem; flex: 1; font-family: var(--font-main);
    display: -webkit-box; -webkit-line-clamp: 3;
    -webkit-box-orient: vertical; overflow: hidden;
}
.hy-event-meta {
    display: flex; flex-direction: column; gap: .35rem;
    margin-bottom: 1rem; padding: .8rem;
    background: var(--ocean-light); border-radius: 10px;
}
.hy-event-meta-item {
    display: flex; align-items: flex-start; gap: .5rem;
    font-size: .8rem; color: var(--ocean-mid);
    font-family: var(--font-main); font-weight: 700;
}
.hy-event-meta-icon { flex-shrink: 0; font-size: .9rem; }
.hy-event-link {
    display: block; text-align: center; padding: 10px 20px;
    background: var(--ocean-blue); color: #fff; border-radius: 50px;
    font-size: .82rem; font-weight: 900; text-decoration: none;
    transition: background .2s; font-family: var(--font-main); margin-top: auto;
}
.hy-event-link:hover { background: var(--ocean-teal); }

/* ── Paginación ───────────────────────────────────────── */
.hy-pagination {
    display: flex; justify-content: center;
    gap: .5rem; margin-top: 3rem; flex-wrap: wrap;
}
.hy-page-btn {
    padding: 9px 18px; border-radius: 50px;
    border: 1.5px solid var(--border-soft);
    color: var(--ocean-blue); font-weight: 800;
    font-size: .87rem; text-decoration: none;
    background: #fff; transition: all .2s; font-family: var(--font-main);
}
.hy-page-btn:hover, .hy-page-btn.active {
    background: var(--ocean-blue); color: #fff; border-color: var(--ocean-blue);
}

/* ── Vacío / Error ────────────────────────────────────── */
.hy-empty {
    text-align: center; padding: 4rem 2rem; background: #fff;
    border-radius: var(--radius-card); border: 1.5px solid var(--border-soft);
}
.hy-empty h3 { font-size: 1.3rem; color: var(--ocean-deep); margin-bottom: .5rem; font-family: var(--font-main); }
.hy-empty p  { color: var(--text-body); font-family: var(--font-main); }

@media (max-width: 640px) {
    .hy-tabs-row      { flex-direction: column; align-items: flex-start; }
    .hy-search-input  { min-width: 160px; }
    .hy-section-title { font-size: 1.6rem; }
}
</style>

<div style="max-width:1280px; margin:0 auto; padding:3rem 2rem;">

    <div class="hy-section-header">
        <div>
            <h1 class="hy-section-title">
                <?php echo $sub === 'eventos' ? 'Eventos Marinos en México' : 'Noticias del Océano'; ?>
            </h1>
            <p class="hy-section-sub">
                <?php if ($sub === 'eventos'): ?>
                    Eventos de conservación marina, playas y medio ambiente en México
                <?php else: ?>
                    Últimas noticias sobre vida marina y conservación de océanos
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="hy-tabs-row">
        <div class="hy-tabs">
            <a href="?section=noticias&sub=noticias"
               class="hy-tab <?php echo $sub === 'noticias' ? 'active' : ''; ?>">Noticias</a>
            <a href="?section=noticias&sub=eventos"
               class="hy-tab <?php echo $sub === 'eventos' ? 'active' : ''; ?>">Eventos en México</a>
        </div>

        <?php if ($sub !== 'eventos'): ?>
        <form action="?section=noticias" method="GET" class="hy-search-form">
            <input type="hidden" name="section" value="noticias">
            <input type="hidden" name="sub"     value="<?php echo htmlspecialchars($sub); ?>">
            <input type="text" name="q"
                   value="<?php echo htmlspecialchars($search_query); ?>"
                   placeholder="Buscar sobre vida marina..."
                   class="hy-search-input" autocomplete="off">
            <button type="submit" class="hy-btn hy-btn-primary">Buscar</button>
            <?php if ($search_query): ?>
                <a href="?section=noticias&sub=<?php echo htmlspecialchars($sub); ?>"
                   class="hy-btn">✕ Limpiar</a>
            <?php endif; ?>
        </form>
        <?php endif; ?>
    </div>

    <?php if ($sub !== 'eventos'): ?>

        <?php if ($has_error): ?>
            <div class="hy-empty">
                <h3>No se pudo conectar a GNews</h3>
                <p>Verifica tu API Key en <code>config_keys.php</code> o intenta más tarde.</p>
            </div>
        <?php elseif (empty($articles)): ?>
            <div class="hy-empty">
                <h3>No hay noticias disponibles</h3>
                <p>Intenta con otro término de búsqueda o vuelve más tarde.</p>
            </div>
        <?php else: ?>

            <div class="hy-news-grid">
            <?php foreach ($articles as $article):
                $date     = !empty($article['publishedAt'])
                            ? date('d M Y', strtotime($article['publishedAt'])) : '';
                $source   = $article['source']['name'] ?? 'Fuente desconocida';
                $img      = $article['image']          ?? '';
                $title    = $article['title']          ?? '';
                $desc     = $article['description']    ?? '';
                $url_art  = $article['url']            ?? '#';
                $lang_art = $article['language']       ?? 'es';
            ?>
                <div class="hy-news-card">
                    <div class="hy-news-card-img-wrap">
                        <?php if ($img): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>"
                                 alt="<?php echo htmlspecialchars($title); ?>"
                                 class="hy-news-card-img"
                                 onerror="this.style.display='none'">
                        <?php else: ?>
                            <div class="hy-news-card-img-placeholder"></div>
                        <?php endif; ?>
                        <span class="hy-lang-badge <?php echo htmlspecialchars($lang_art); ?>">
                            <?php echo strtoupper($lang_art); ?>
                        </span>
                    </div>
                    <div class="hy-news-card-body">
                        <div class="hy-news-card-source"><?php echo htmlspecialchars($source); ?></div>
                        <div class="hy-news-card-title"><?php echo htmlspecialchars($title); ?></div>
                        <?php if ($desc): ?>
                            <div class="hy-news-card-desc"><?php echo htmlspecialchars($desc); ?></div>
                        <?php endif; ?>
                        <div class="hy-news-card-footer">
                            <span class="hy-news-card-date"> <?php echo $date; ?></span>
                            <a href="<?php echo htmlspecialchars($url_art); ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="hy-news-card-link">Leer artículo →</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

            <div class="hy-pagination">
                <?php if ($page > 1): ?>
                    <a href="?section=noticias&sub=<?php echo $sub; ?>&page=<?php echo $page-1; ?><?php echo $search_query ? '&q='.urlencode($search_query) : ''; ?>"
                       class="hy-page-btn">← Anterior</a>
                <?php endif; ?>
                <?php for ($i = max(1,$page-2); $i <= $page+2; $i++): ?>
                    <a href="?section=noticias&sub=<?php echo $sub; ?>&page=<?php echo $i; ?><?php echo $search_query ? '&q='.urlencode($search_query) : ''; ?>"
                       class="hy-page-btn <?php echo $i===$page?'active':''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <a href="?section=noticias&sub=<?php echo $sub; ?>&page=<?php echo $page+1; ?><?php echo $search_query ? '&q='.urlencode($search_query) : ''; ?>"
                   class="hy-page-btn">Siguiente →</a>
            </div>

        <?php endif; ?>
    <?php endif; ?>


    <?php if ($sub === 'eventos'): ?>

        <?php if (empty($eventos)): ?>
            <div class="hy-empty">
                <h3>No hay eventos disponibles</h3>
                <p>Pronto se agregarán nuevos eventos. ¡Vuelve pronto!</p>
            </div>
        <?php else: ?>

            <div class="hy-events-grid">
            <?php foreach ($eventos as $ev):
                if (!empty($ev['title'])) {
                    $ev_title = $ev['title'];
                    $ev_desc  = $ev['description'];
                    $ev_date  = $ev['date'];
                    $ev_loc   = $ev['location'];
                    $ev_url   = $ev['url'];
                    $ev_type  = $ev['type'];
                    $ev_img   = $ev['img'] ?? '';
                } else {
                    $ev_title = $ev['name']                      ?? 'Evento marino';
                    $ev_desc  = $ev['info'] ?? $ev['pleaseNote'] ?? '';
                    $ev_date  = !empty($ev['dates']['start']['localDate'])
                                ? date('d M Y', strtotime($ev['dates']['start']['localDate']))
                                : 'Fecha por confirmar';
                    $ev_loc   = ($ev['_embedded']['venues'][0]['name'] ?? '')
                              . ', ' . ($ev['_embedded']['venues'][0]['city']['name'] ?? 'México');
                    $ev_url   = $ev['url'] ?? '#';
                    $ev_type  = $ev['classifications'][0]['segment']['name'] ?? 'Evento';
                    $tm_imgs  = $ev['images'] ?? [];
                    $ev_img   = '';
                    foreach ($tm_imgs as $timg) {
                        if (($timg['width'] ?? 0) >= 400) { $ev_img = $timg['url']; break; }
                    }
                }
            ?>
                <div class="hy-event-card">
                    <div class="hy-event-img-wrap">
                        <?php if ($ev_img): ?>
                            <img src="<?php echo htmlspecialchars($ev_img); ?>"
                                 alt="<?php echo htmlspecialchars($ev_title); ?>"
                                 class="hy-event-img"
                                 onerror="this.style.display='none'">
                        <?php else: ?>
                            <div class="hy-event-img-placeholder"></div>
                        <?php endif; ?>
                        <span class="hy-event-type-badge"><?php echo htmlspecialchars($ev_type); ?></span>
                    </div>
                    <div class="hy-event-card-body">
                        <h3 class="hy-event-title"><?php echo htmlspecialchars($ev_title); ?></h3>
                        <?php if ($ev_desc): ?>
                            <p class="hy-event-desc"><?php echo htmlspecialchars($ev_desc); ?></p>
                        <?php endif; ?>
                        <div class="hy-event-meta">
                            <div class="hy-event-meta-item">
                                <span class="hy-event-meta-icon"></span>
                                <span><?php echo htmlspecialchars($ev_date); ?></span>
                            </div>
                            <div class="hy-event-meta-item">
                                <span class="hy-event-meta-icon">📍</span>
                                <span><?php echo htmlspecialchars($ev_loc); ?></span>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($ev_url); ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="hy-event-link">Ver detalles →</a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

            <?php
            $total_ev    = $eventos_data['total']    ?? 0;
            $per_ev      = $eventos_data['per_page'] ?? 6;
            $total_pages = $total_ev > 0 ? (int)ceil($total_ev / $per_ev) : 0;
            if ($total_pages > 1):
            ?>
            <div class="hy-pagination">
                <?php if ($page > 1): ?>
                    <a href="?section=noticias&sub=eventos&page=<?php echo $page-1; ?>" class="hy-page-btn">← Anterior</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?section=noticias&sub=eventos&page=<?php echo $i; ?>"
                       class="hy-page-btn <?php echo $i===$page?'active':''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?section=noticias&sub=eventos&page=<?php echo $page+1; ?>" class="hy-page-btn">Siguiente →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    <?php endif; ?>

</div>