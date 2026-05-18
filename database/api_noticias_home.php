<?php
/**
 * Proxy PHP para GNews API - Carousel del Home
 * Consulta noticias del mar / vida submarina en servidor para evitar CORS
 */
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('GNEWS_API_KEY', 'd5b5320a0accff00272ab27733ba94ce');
define('CACHE_TTL_HOME', 3600); // 1 hora

$cache_dir  = __DIR__ . '/../data';
$cache_file = $cache_dir . '/gnews_home_carousel.json';

// Servir desde caché si existe y es reciente
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < CACHE_TTL_HOME) {
    echo file_get_contents($cache_file);
    exit;
}

// Crear directorio de caché si no existe
if (!file_exists($cache_dir)) {
    mkdir($cache_dir, 0777, true);
}

$query = 'vida submarina OR oceanos OR conservacion marina OR especies marinas OR arrecife coral';

$url = 'https://gnews.io/api/v4/search?' . http_build_query([
    'q'      => $query,
    'lang'   => 'es',
    'in'     => 'title,description',
    'sortby' => 'publishedAt',
    'max'    => 5,
    'from'   => date('Y-m-d', strtotime('-60 days')),
    'apikey' => GNEWS_API_KEY,
]);

$ctx = stream_context_create(['http' => [
    'timeout' => 12,
    'header'  => 'User-Agent: HYDRON/1.0',
]]);

$response = @file_get_contents($url, false, $ctx);

if (!$response) {
    echo json_encode(['articles' => [], 'error' => true, 'message' => 'No se pudo conectar con la API de noticias.']);
    exit;
}

// Guardar en caché y retornar
file_put_contents($cache_file, $response);
echo $response;
