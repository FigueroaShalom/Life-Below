<?php
// ════════════════════════════════════════════════════════════
// GALERÍA DE VIDA MARINA — Pexels API
// ════════════════════════════════════════════════════════════

$pexels_token = '1WF26Si56hRMDSEqmVbQkBr9fRpO87YbFAOmJE3L8OBgi9hIR52tQ9ic';

$busqueda = trim($_GET['q'] ?? 'ocean life marine');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;

// Taxonomías de iNaturalist para vida marina
$categorias = [
    ''           => ['label' => 'Todas',        'icon' => '≈', 'taxon_id' => 1],
    'peces'      => ['label' => 'Peces',         'icon' => '•', 'taxon_id' => 47178],
    'mamiferos'  => ['label' => 'Mamíferos',     'icon' => '◆', 'taxon_id' => 40151],
    'moluscos'   => ['label' => 'Moluscos',      'icon' => '⊗', 'taxon_id' => 47115],
    'crustaceos' => ['label' => 'Crustáceos',    'icon' => '◈', 'taxon_id' => 85493],
    'corales'    => ['label' => 'Corales',       'icon' => '❈', 'taxon_id' => 57774],
    'tortugas'   => ['label' => 'Tortugas',      'icon' => '◊', 'taxon_id' => 26487],
    'tiburones'  => ['label' => 'Tiburones',     'icon' => '▶', 'taxon_id' => 47273],
];

// Función para llamar a iNaturalist
function fetchINaturalist($params) {
    $cache_key  = md5(json_encode($params));
    $cache_file = __DIR__ . '/../data/inaturalist_' . $cache_key . '.json';
    $cache_ttl  = 3600;

    if (!file_exists(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
    }

    // Usar caché si existe
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
        $cached = @file_get_contents($cache_file);
        if ($cached) return json_decode($cached, true);
    }

    $url = 'https://api.inaturalist.org/v1/observations?' . http_build_query($params);

    // Usar cURL con límite de tamaño
    if (!function_exists('curl_init')) {
        return ['results' => [], 'total_results' => 0, 'error' => true];
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'HYDRON/1.0',
        CURLOPT_BUFFERSIZE     => 1024 * 512, // 512KB buffer
        CURLOPT_MAXFILESIZE    => 1024 * 1024 * 8, // máx 8MB
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if (!$response || $error) {
        return ['results' => [], 'total_results' => 0, 'error' => true];
    }

    $data = json_decode($response, true);
    if (!$data) return ['results' => [], 'total_results' => 0, 'error' => true];

    // Guardar solo lo necesario en caché (no el objeto completo)
    $slim = [
        'total_results' => $data['total_results'] ?? 0,
        'results'       => array_map(function($obs) {
            return [
                'id'            => $obs['id'] ?? '',
                'quality_grade' => $obs['quality_grade'] ?? '',
                'faves_count'   => $obs['faves_count'] ?? 0,
                'place_guess'   => $obs['place_guess'] ?? '',
                'photos'        => array_slice($obs['photos'] ?? [], 0, 1),
                'taxon'         => [
                    'name'                  => $obs['taxon']['name'] ?? '',
                    'preferred_common_name' => $obs['taxon']['preferred_common_name'] ?? '',
                ],
            ];
        }, $data['results'] ?? []),
    ];

    file_put_contents($cache_file, json_encode($slim));
    return $slim;
}

// Construir parámetros
$params = [
    'photos'        => 'true',
    'photo_license' => 'cc-by,cc-by-nc,cc-by-sa,cc-by-nc-sa,cc0',
    'quality_grade' => 'research',
    'order'         => 'desc',
    'order_by'      => 'votes',
    'per_page'      => $per_page,
    'page'          => $page,
    'place_id'      => 97394,
    'only_id'       => 'false',
    'fields'        => 'id,quality_grade,faves_count,place_guess,photos,taxon',
];

// Filtro por categoría
if ($categoria && isset($categorias[$categoria])) {
    $params['taxon_id'] = $categorias[$categoria]['taxon_id'];
} else {
    // Sin categoría: buscar en taxa marinos principales
    $params['taxon_id'] = '47178,40151,47115,85493,57774,26487,47273';
}

// Búsqueda por texto
if ($busqueda) {
    $params['taxon_name'] = $busqueda;
    unset($params['taxon_id']);
    unset($params['place_id']);
}

$data      = fetchINaturalist($params);
$resultados = $data['results']       ?? [];
$total      = $data['total_results'] ?? 0;
$has_error  = !empty($data['error']);
$total_pages = ceil(min($total, 500) / $per_page); // iNaturalist max 500
?>

<style>
.hy-gallery-wrap { max-width: 1280px; margin: 0 auto; padding: 3rem 2rem; }

/* Header */
.hy-gallery-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;
}
.hy-gallery-header h1 {
    font-family: 'Nunito', sans-serif; font-weight: 900;
    font-size: 2rem; color: #001828; letter-spacing: -.5px;
}
.hy-gallery-header p {
    font-family: 'Nunito', sans-serif; font-size: .9rem; color: #5a7a9a; margin-top: .2rem;
}

/* Buscador */
.hy-gallery-search {
    display: flex; gap: .6rem; margin-bottom: 1.5rem;
}
.hy-gallery-search input {
    flex: 1; padding: 11px 18px;
    border: 1.5px solid rgba(0,120,190,0.2); border-radius: 50px;
    font-family: 'Nunito', sans-serif; font-size: .95rem; color: #001828;
    outline: none; transition: border-color .2s;
}
.hy-gallery-search input:focus { border-color: #0077be; }
.hy-gallery-search button {
    padding: 11px 22px; border-radius: 50px;
    background: #0077be; color: #fff; border: none;
    font-family: 'Nunito', sans-serif; font-weight: 800; font-size: .9rem;
    cursor: pointer; transition: background .2s;
}
.hy-gallery-search button:hover { background: #009aaa; }

/* Tabs categorías */
.hy-gallery-tabs {
    display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 2rem;
}
.hy-gallery-tab {
    padding: 8px 18px; border-radius: 50px;
    border: 1.5px solid rgba(0,120,190,0.2);
    color: #0077be; font-weight: 700; font-size: .85rem;
    text-decoration: none; background: #fff;
    transition: all .2s; font-family: 'Nunito', sans-serif;
    display: flex; align-items: center; gap: 5px;
}
.hy-gallery-tab:hover,
.hy-gallery-tab.active { background: #0077be; color: #fff; border-color: #0077be; }

/* Stats */
.hy-gallery-stats {
    display: flex; align-items: center; gap: 1rem;
    margin-bottom: 1.5rem; flex-wrap: wrap;
}
.hy-gallery-count {
    font-family: 'Nunito', sans-serif; font-size: .88rem; color: #5a7a9a;
}
.hy-inaturalist-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(116,195,101,0.12); border: 1px solid rgba(116,195,101,0.3);
    color: #3a7a2a; font-size: .75rem; font-weight: 700;
    padding: 4px 12px; border-radius: 50px; font-family: 'Nunito', sans-serif;
}
.hy-inaturalist-badge::before {
    content: ''; width: 7px; height: 7px; border-radius: 50%;
    background: #74c365; animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

/* Grid */
.hy-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.2rem;
}
.hy-gallery-card {
    background: #fff; border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,40,80,0.1);
    border: 1.5px solid rgba(0,120,190,0.08);
    transition: transform .25s, box-shadow .25s;
    position: relative;
}
.hy-gallery-card:hover { transform: translateY(-5px); box-shadow: 0 12px 40px rgba(0,40,80,0.16); }

.hy-gallery-card-img-wrap {
    position: relative; height: 210px; overflow: hidden; background: #001828;
}
.hy-gallery-card-img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s ease;
}
.hy-gallery-card:hover .hy-gallery-card-img { transform: scale(1.06); }

.hy-gallery-card-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(transparent 50%, rgba(0,10,30,0.75));
    opacity: 0; transition: opacity .3s;
    display: flex; align-items: flex-end; padding: 1rem;
}
.hy-gallery-card:hover .hy-gallery-card-overlay { opacity: 1; }
.hy-gallery-card-overlay a {
    color: #fff; font-family: 'Nunito', sans-serif; font-weight: 800;
    font-size: .82rem; text-decoration: none;
    background: rgba(0,119,190,0.85); padding: 6px 14px; border-radius: 50px;
}

/* Badge de calidad */
.hy-quality-badge {
    position: absolute; top: 10px; right: 10px;
    background: rgba(0,200,100,0.9); color: #fff;
    font-size: .68rem; font-weight: 800; font-family: 'Nunito', sans-serif;
    padding: 3px 9px; border-radius: 50px; letter-spacing: .5px;
}

.hy-gallery-card-body { padding: 1.1rem; }
.hy-gallery-card-name {
    font-family: 'Nunito', sans-serif; font-weight: 900;
    font-size: 1rem; color: #001828; margin-bottom: .2rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.hy-gallery-card-sci {
    font-family: 'Nunito', sans-serif; font-style: italic;
    font-size: .82rem; color: #5a7a9a; margin-bottom: .6rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.hy-gallery-card-meta {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .4rem;
}
.hy-gallery-card-loc {
    font-size: .75rem; color: #5a7a9a; font-family: 'Nunito', sans-serif;
    display: flex; align-items: center; gap: 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px;
}
.hy-gallery-card-votes {
    font-size: .75rem; color: #0077be; font-family: 'Nunito', sans-serif;
    font-weight: 700; display: flex; align-items: center; gap: 3px;
}

/* Placeholder */
.hy-gallery-placeholder {
    width: 100%; height: 210px;
    background: linear-gradient(135deg, #001828, #003a5c);
    display: flex; align-items: center; justify-content: center; font-size: 3.5rem;
}

/* Vacío / Error */
.hy-gallery-empty {
    text-align: center; padding: 4rem 2rem;
    background: #fff; border-radius: 16px;
    border: 1.5px solid rgba(0,120,190,0.1);
    grid-column: 1/-1;
}
.hy-gallery-empty h3 { font-family:'Nunito',sans-serif; font-size:1.3rem; color:#001828; margin-bottom:.5rem; }
.hy-gallery-empty p  { font-family:'Nunito',sans-serif; color:#5a7a9a; }

/* Paginación */
.hy-pagination {
    display: flex; justify-content: center; gap: .6rem;
    margin-top: 3rem; flex-wrap: wrap;
}
.hy-page-btn {
    padding: 9px 18px; border-radius: 50px;
    border: 1.5px solid rgba(0,120,190,0.2);
    color: #0077be; font-weight: 700; font-size: .88rem;
    text-decoration: none; background: #fff;
    transition: all .2s; font-family: 'Nunito', sans-serif;
}
.hy-page-btn:hover,
.hy-page-btn.active { background: #0077be; color: #fff; border-color: #0077be; }
.hy-page-btn.disabled { opacity: .4; pointer-events: none; }
</style>

<div class="hy-gallery-wrap">

    <!-- Header -->
    <div class="hy-gallery-header">
        <div>
            <h1>Galería de Vida Marina</h1>
            <p>Observaciones verificadas por la comunidad científica global</p>
        </div>
        <span class="hy-inaturalist-badge">Live · iNaturalist</span>
    </div>

    <!-- Buscador -->
    <form method="GET" action="index.php" class="hy-gallery-search">
        <input type="hidden" name="section" value="galeria">
        <?php if ($categoria): ?>
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoria); ?>">
        <?php endif; ?>
        <input type="text" name="q"
               value="<?php echo htmlspecialchars($busqueda); ?>"
               placeholder="🔍 Buscar especie... (ej: Amphiprion, Tursiops)">
        <button type="submit">Buscar</button>
    </form>

    <!-- Tabs categorías -->
    <div class="hy-gallery-tabs">
        <?php foreach ($categorias as $key => $cat): ?>
            <a href="?section=galeria&category=<?php echo $key; ?>"
               class="hy-gallery-tab <?php echo $categoria===$key?'active':''; ?>">
                <?php echo $cat['icon'] . ' ' . $cat['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Stats -->
    <div class="hy-gallery-stats">
        <span class="hy-gallery-count">
            <?php if ($has_error): ?>
                ⚠️ Error de conexión
            <?php else: ?>
                <?php echo number_format($total); ?> observaciones encontradas
                — página <?php echo $page; ?> de <?php echo max(1, $total_pages); ?>
            <?php endif; ?>
        </span>
    </div>

    <!-- Grid -->
    <div class="hy-gallery-grid">

        <?php if ($has_error): ?>
            <div class="hy-gallery-empty">
                <h3>⚠️ No se pudo conectar</h3>
                <p>Verifica tu conexión a internet. iNaturalist puede estar temporalmente no disponible.</p>
            </div>

        <?php elseif (empty($resultados)): ?>
            <div class="hy-gallery-empty">
                <h3>🌊 Sin resultados</h3>
                <p>Prueba con otra categoría o término de búsqueda.</p>
            </div>

        <?php else: ?>
            <?php foreach ($resultados as $obs):
                $taxon    = $obs['taxon'] ?? [];
                $photos   = $obs['photos'] ?? [];
                $photo    = $photos[0] ?? null;
                $img_url  = $photo ? str_replace('square', 'medium', $photo['url'] ?? '') : '';
                $name     = $taxon['preferred_common_name'] ?? $taxon['name'] ?? 'Especie desconocida';
                $sci_name = $taxon['name'] ?? '';
                $place    = $obs['place_guess'] ?? '';
                $votes    = $obs['quality_grade'] === 'research' ? '✓ Verificada' : '';
                $faves    = $obs['faves_count'] ?? 0;
                $obs_url  = 'https://www.inaturalist.org/observations/' . ($obs['id'] ?? '');
                $attr     = $photo['attribution'] ?? '';
            ?>
            <div class="hy-gallery-card">
                <div class="hy-gallery-card-img-wrap">
                    <?php if ($img_url): ?>
                        <img src="<?php echo htmlspecialchars($img_url); ?>"
                             alt="<?php echo htmlspecialchars($name); ?>"
                             class="hy-gallery-card-img"
                             loading="lazy"
                             onerror="this.parentElement.innerHTML='<div class=\'hy-gallery-placeholder\'>🌊</div>'">
                    <?php else: ?>
                        <div class="hy-gallery-placeholder">🌊</div>
                    <?php endif; ?>

                    <?php if ($votes): ?>
                        <span class="hy-quality-badge">✓ Verificada</span>
                    <?php endif; ?>

                    <div class="hy-gallery-card-overlay">
                        <a href="<?php echo htmlspecialchars($obs_url); ?>"
                           target="_blank" rel="noopener">Ver en iNaturalist →</a>
                    </div>
                </div>

                <div class="hy-gallery-card-body">
                    <div class="hy-gallery-card-name"><?php echo htmlspecialchars(ucfirst($name)); ?></div>
                    <?php if ($sci_name && $sci_name !== $name): ?>
                        <div class="hy-gallery-card-sci"><?php echo htmlspecialchars($sci_name); ?></div>
                    <?php endif; ?>
                    <div class="hy-gallery-card-meta">
                        <?php if ($place): ?>
                            <span class="hy-gallery-card-loc">📍 <?php echo htmlspecialchars($place); ?></span>
                        <?php endif; ?>
                        <?php if ($faves > 0): ?>
                            <span class="hy-gallery-card-votes">⭐ <?php echo $faves; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- Paginación -->
    <?php if ($total_pages > 1 && !$has_error): ?>
    <div class="hy-pagination">
        <a href="?section=galeria&category=<?php echo $categoria; ?>&q=<?php echo urlencode($busqueda); ?>&page=<?php echo max(1,$page-1); ?>"
           class="hy-page-btn <?php echo $page<=1?'disabled':''; ?>">← Anterior</a>

        <?php for ($i = max(1,$page-2); $i <= min($total_pages,$page+2); $i++): ?>
            <a href="?section=galeria&category=<?php echo $categoria; ?>&q=<?php echo urlencode($busqueda); ?>&page=<?php echo $i; ?>"
               class="hy-page-btn <?php echo $i===$page?'active':''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <a href="?section=galeria&category=<?php echo $categoria; ?>&q=<?php echo urlencode($busqueda); ?>&page=<?php echo min($total_pages,$page+1); ?>"
           class="hy-page-btn <?php echo $page>=$total_pages?'disabled':''; ?>">Siguiente →</a>
    </div>
    <?php endif; ?>

    <!-- Créditos -->
    <p style="text-align:center;margin-top:2rem;font-family:'Nunito',sans-serif;font-size:.78rem;color:#5a7a9a;">
        Datos de <a href="https://www.inaturalist.org" target="_blank" style="color:#0077be;">iNaturalist</a>
        bajo licencias Creative Commons · Solo observaciones verificadas por la comunidad científica
    </p>

</div>