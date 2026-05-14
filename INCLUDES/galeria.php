<?php
// ════════════════════════════════════════════════════════════
// GALERÍA DE VIDA MARINA — Pexels API
// ════════════════════════════════════════════════════════════

$pexels_token = '1WF26Si56hRMDSEqmVbQkBr9fRpO87YbFAOmJE3L8OBgi9hIR52tQ9ic';

$busqueda = trim($_GET['q'] ?? '');
$categoria = $_GET['category'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 15;

// Categorías para Pexels
$categorias = [
    ''           => ['label' => 'Todas',        'icon' => '≈', 'query' => 'ocean life marine'],
    'peces'      => ['label' => 'Peces',         'icon' => '•', 'query' => 'fish ocean underwater'],
    'mamiferos'  => ['label' => 'Mamíferos',     'icon' => '◆', 'query' => 'whale dolphin seal marine mammal'],
    'moluscos'   => ['label' => 'Moluscos',      'icon' => '⊗', 'query' => 'octopus squid mollusk ocean'],
    'crustaceos' => ['label' => 'Crustáceos',    'icon' => '◈', 'query' => 'crab lobster crustacean ocean'],
    'corales'    => ['label' => 'Corales',       'icon' => '❈', 'query' => 'coral reef underwater'],
    'tortugas'   => ['label' => 'Tortugas',      'icon' => '◊', 'query' => 'sea turtle ocean'],
    'tiburones'  => ['label' => 'Tiburones',     'icon' => '▶', 'query' => 'shark underwater'],
];

// Función para llamar a Pexels
function fetchPexels($params, $token) {
    $cache_key  = md5(json_encode($params));
    $cache_file = __DIR__ . '/../data/pexels_' . $cache_key . '.json';
    $cache_ttl  = 3600;

    if (!file_exists(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
    }

    // Usar caché si existe
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
        $cached = @file_get_contents($cache_file);
        if ($cached) return json_decode($cached, true);
    }

    $url = 'https://api.pexels.com/v1/search?' . http_build_query($params);

    if (!function_exists('curl_init')) {
        return ['photos' => [], 'total_results' => 0, 'error' => true];
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: ' . $token],
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'HYDRON/1.0',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if (!$response || $error) {
        return ['photos' => [], 'total_results' => 0, 'error' => true];
    }

    $data = json_decode($response, true);
    if (!$data) return ['photos' => [], 'total_results' => 0, 'error' => true];

    file_put_contents($cache_file, json_encode($data));
    return $data;
}

// Determinar el término de búsqueda final
$final_query = $busqueda;
if (!$final_query) {
    $final_query = $categorias[$categoria]['query'] ?? $categorias['']['query'];
}

$params = [
    'query'    => $final_query,
    'per_page' => $per_page,
    'page'     => $page,
];

$data        = fetchPexels($params, $pexels_token);
$resultados  = $data['photos'] ?? [];
$total       = $data['total_results'] ?? 0;
$has_error   = !empty($data['error']);
$total_pages = ceil($total / $per_page);
?>

<style>
.hy-gallery-wrap { 
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 2rem 2rem;
}

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
/* Nuevo contenedor para separar tabs y buscador */
.hy-gallery-controls-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
    margin-top: 1rem;
}

/* Buscador modificado (sin margen inferior) */
.hy-gallery-search {
    display: flex; gap: .6rem; margin-bottom: 0;
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

/* Tabs categorías modificados (sin margen inferior) */
.hy-gallery-tabs {
    display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 0;
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
.hy-pexels-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(5,160,129,0.12); border: 1px solid rgba(5,160,129,0.3);
    color: #05a081; font-size: .75rem; font-weight: 700;
    padding: 4px 12px; border-radius: 50px; font-family: 'Nunito', sans-serif;
}
.hy-pexels-badge::before {
    content: ''; width: 7px; height: 7px; border-radius: 50%;
    background: #05a081; animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

/* Grid */
.hy-gallery-grid {
    column-count: 5;
    column-gap: 1rem;
    width: 100%;
}

@media (max-width: 1400px) {
    .hy-gallery-grid { column-count: 4; }
}

@media (max-width: 1100px) {
    .hy-gallery-grid { column-count: 3; }
}

@media (max-width: 768px) {
    .hy-gallery-grid { column-count: 2; }
}

@media (max-width: 500px) {
    .hy-gallery-grid { column-count: 1; }
}

.hy-gallery-card {
    display: inline-block;
    width: 100%;
    margin-bottom: 1rem;
    break-inside: avoid;
    transition: transform .3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.hy-gallery-card:hover { transform: translateY(-8px); }

.hy-gallery-card-img-wrap {
    position: relative;
    overflow: hidden;
    border-radius: 16px;
    background: #001828;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.hy-gallery-card-img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
    transition: transform .5s;
}
.hy-gallery-card:hover .hy-gallery-card-img { transform: scale(1.08); }

.hy-gallery-card-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(transparent 40%, rgba(0,10,30,0.85));
    opacity: 0; transition: opacity .3s;
    display: flex; align-items: flex-end; padding: 1.2rem;
}
.hy-gallery-card:hover .hy-gallery-card-overlay { opacity: 1; }
.hy-gallery-card-overlay a {
    color: #fff; font-family: 'Nunito', sans-serif; font-weight: 800;
    font-size: .82rem; text-decoration: none;
    background: #05a081; padding: 7px 16px; border-radius: 50px;
    box-shadow: 0 4px 12px rgba(5,160,129,0.3);
}

.hy-gallery-card-body { padding: 0.8rem 0.2rem; }
.hy-gallery-card-name {
    font-family: 'Nunito', sans-serif; font-weight: 800;
    font-size: 0.95rem; color: #001828; margin-bottom: .2rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.hy-gallery-card-meta {
    display: flex; align-items: center; justify-content: space-between;
}
.hy-gallery-card-loc {
    font-size: .78rem; color: #5a7a9a; font-family: 'Nunito', sans-serif;
    display: flex; align-items: center; gap: 4px;
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
    grid-column: 1/-1; width: 100%;
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
   <div class="hy-gallery-controls-row">
        
        <div class="hy-gallery-tabs">
            <?php foreach ($categorias as $key => $cat): ?>
                <a href="?section=galeria&category=<?php echo $key; ?>"
                   class="hy-gallery-tab <?php echo $categoria===$key?'active':''; ?>">
                    <?php echo $cat['icon'] . ' ' . $cat['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form method="GET" action="index.php" class="hy-gallery-search">
            <input type="hidden" name="section" value="galeria">
            <?php if ($categoria): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoria); ?>">
            <?php endif; ?>
            <input type="text" name="q"
                   value="<?php echo htmlspecialchars($busqueda); ?>"
                   placeholder=" Buscar imágenes...">
            <button type="submit">Buscar</button>
        </form>

    </div>

    <!-- Stats -->
    <div class="hy-gallery-stats">
        <span class="hy-gallery-count">
            <?php if ($has_error): ?>
                 Error de conexión
            <?php else: ?>
                <?php echo number_format($total); ?> imágenes encontradas
                — página <?php echo $page; ?> de <?php echo max(1, $total_pages); ?>
            <?php endif; ?>
        </span>
    </div>

    <!-- Grid -->
    <div class="hy-gallery-grid">

        <?php if ($has_error): ?>
            <div class="hy-gallery-empty">
                <h3> No se pudo conectar</h3>
                <p>Verifica tu conexión a internet. La API de Pexels puede estar temporalmente no disponible.</p>
            </div>

        <?php elseif (empty($resultados)): ?>
            <div class="hy-gallery-empty">
                <h3> Sin resultados...</h3>
                <p>Prueba con otra categoría o término de búsqueda.</p>
            </div>

        <?php else: ?>
            <?php foreach ($resultados as $photo):
                $img_url  = $photo['src']['medium'] ?? '';
                $name     = $photo['alt'] ?: 'Vida Marina';
                $photographer = $photo['photographer'] ?? 'Anónimo';
                $photo_url = $photo['url'] ?? '#';
                $avg_color = $photo['avg_color'] ?? '#001828';
            ?>
            <div class="hy-gallery-card">
                <div class="hy-gallery-card-img-wrap" style="background: <?php echo $avg_color; ?>;">
                    <?php if ($img_url): ?>
                        <img src="<?php echo htmlspecialchars($img_url); ?>"
                             alt="<?php echo htmlspecialchars($name); ?>"
                             class="hy-gallery-card-img"
                             loading="lazy"
                             onerror="this.parentElement.innerHTML='<div class=\'hy-gallery-placeholder\'></div>'">
                    <?php else: ?>
                        <div class="hy-gallery-placeholder"></div>
                    <?php endif; ?>

                    <div class="hy-gallery-card-overlay">
                        <a href="<?php echo htmlspecialchars($photo_url); ?>"
                           target="_blank" rel="noopener">Ver en Pexels →</a>
                    </div>
                </div>

                <div class="hy-gallery-card-body">
                    <div class="hy-gallery-card-name"><?php echo htmlspecialchars(ucfirst($name)); ?></div>
                    <div class="hy-gallery-card-meta">
                        <span class="hy-gallery-card-loc">📸 <?php echo htmlspecialchars($photographer); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- Paginación -->
    <?php if ($total_pages > 1 && !$has_error): ?>
    <div class="hy-pagination">
        <a href="?section=galeria&category=<?php echo urlencode($categoria); ?>&q=<?php echo urlencode($busqueda); ?>&page=<?php echo max(1,$page-1); ?>"
           class="hy-page-btn <?php echo $page<=1?'disabled':''; ?>">← Anterior</a>

        <?php 
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?section=galeria&category=<?php echo urlencode($categoria); ?>&q=<?php echo urlencode($busqueda); ?>&page=<?php echo $i; ?>"
               class="hy-page-btn <?php echo $i===$page?'active':''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <a href="?section=galeria&category=<?php echo urlencode($categoria); ?>&q=<?php echo urlencode($busqueda); ?>&page=<?php echo min($total_pages,$page+1); ?>"
           class="hy-page-btn <?php echo $page>=$total_pages?'disabled':''; ?>">Siguiente →</a>
    </div>
    <?php endif; ?>

    <!-- Créditos -->
    <p style="text-align:center;margin-top:2rem;font-family:'Nunito',sans-serif;font-size:.78rem;color:#5a7a9a;">
        Imágenes proporcionadas por <a href="https://www.pexels.com" target="_blank" style="color:#0077be;">Pexels</a>
        bajo su licencia de uso gratuito.
    </p>

</div>