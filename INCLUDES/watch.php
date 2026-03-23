<?php
require_once __DIR__ . '/../database/Conexion_base.php';

$selected_id = isset($_GET['video']) ? (int)$_GET['video'] : 0;

if ($selected_id):
    // ── DETALLE DEL VIDEO ─────────────────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT v.*, u.user AS autor
        FROM videos v
        JOIN usuarios u ON v.id_autor = u.id
        WHERE v.id = ?
    ");
    $stmt->bind_param("i", $selected_id);
    $stmt->execute();
    $video = $stmt->get_result()->fetch_assoc();

    if (!$video):
        echo '<p>Video no encontrado. <a href="?section=watch">← Volver</a></p>';
    else:

        // Convertir URL de YouTube a embed
        function getEmbedUrl($url) {
            // Formato: youtube.com/watch?v=ID
            if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) {
                return 'https://www.youtube.com/embed/' . $m[1];
            }
            // Formato: youtu.be/ID
            if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
                return 'https://www.youtube.com/embed/' . $m[1];
            }
            // Ya es embed u otro formato
            return $url;
        }

        $embed_url = getEmbedUrl($video['video_url']);
?>
    <div class="article-detail">
        <a href="?section=watch" class="back-btn">← Volver a videos</a>

        <!-- REPRODUCTOR -->
        <div class="video-player">
            <iframe
                src="<?php echo htmlspecialchars($embed_url); ?>"
                title="<?php echo htmlspecialchars($video['titulo']); ?>"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>

        <h2 class="post-title"><?php echo htmlspecialchars($video['titulo']); ?></h2>

        <div class="article-meta">
            <span>👤 <?php echo htmlspecialchars($video['autor']); ?></span>
            <span>📅 <?php echo date('d/m/Y', strtotime($video['fecha_publicacion'])); ?></span>
            <span>🏷️ <?php echo htmlspecialchars($video['categoria'] ?? 'general'); ?></span>
        </div>

        <?php if (!empty($video['descripcion'])): ?>
            <div class="article-content">
                <?php echo nl2br(htmlspecialchars($video['descripcion'])); ?>
            </div>
        <?php endif; ?>

        <div style="margin-top:1.5rem;">
            <a href="?section=watch" class="read-more-btn">← Ver más videos</a>
        </div>
    </div>

<?php
    endif;

else:
    // ── LISTADO DE VIDEOS ─────────────────────────────────────────────────
    $cat_filter = $_GET['cat'] ?? '';

    $sql = "
        SELECT v.id, v.titulo, v.descripcion, v.video_url, v.categoria, v.fecha_publicacion,
               u.user AS autor
        FROM videos v
        JOIN usuarios u ON v.id_autor = u.id
    ";
    if ($cat_filter) {
        $cat_safe = $conn->real_escape_string($cat_filter);
        $sql .= " WHERE v.categoria = '$cat_safe'";
    }
    $sql .= " ORDER BY v.fecha_publicacion DESC";

    $videos = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

    // Función para obtener thumbnail de YouTube
    function getYoutubeThumbnail($url) {
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
        }
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
        }
        return null;
    }

    $categorias = ['general', 'peces', 'mamiferos', 'conservacion', 'documental'];
?>

<h1 class="section-title">▶ WATCH — Videos sobre vida marina</h1>

<!-- Filtro por categoría -->
<div style="text-align:center; margin-bottom:1.5rem;">
    <a href="?section=watch"
       class="news-category-btn <?php echo !$cat_filter ? 'active' : ''; ?>">Todos</a>
    <?php foreach ($categorias as $cat): ?>
        <a href="?section=watch&cat=<?php echo $cat; ?>"
           class="news-category-btn <?php echo $cat_filter === $cat ? 'active' : ''; ?>">
            <?php echo ucfirst($cat); ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($videos)): ?>
    <p style="text-align:center; padding:3rem; background:white; border-radius:15px;">
        No hay videos publicados aún.
    </p>
<?php else: ?>
    <div class="posts-grid">
        <?php foreach ($videos as $v):
            $thumb = getYoutubeThumbnail($v['video_url']);
        ?>
            <div class="post-card">
                <!-- Thumbnail -->
                <div class="video-thumb" onclick="window.location.href='?section=watch&video=<?php echo $v['id']; ?>'">
                    <?php if ($thumb): ?>
                        <img src="<?php echo $thumb; ?>"
                             alt="<?php echo htmlspecialchars($v['titulo']); ?>">
                    <?php else: ?>
                        <div class="video-thumb-placeholder">🎬</div>
                    <?php endif; ?>
                    <div class="video-play-icon">▶</div>
                </div>

                <div class="post-content">
                    <span class="post-category"><?php echo htmlspecialchars($v['categoria'] ?? 'general'); ?></span>
                    <h3 class="post-title"><?php echo htmlspecialchars($v['titulo']); ?></h3>

                    <?php if (!empty($v['descripcion'])): ?>
                        <p class="post-excerpt">
                            <?php echo substr(htmlspecialchars($v['descripcion']), 0, 120) . '...'; ?>
                        </p>
                    <?php endif; ?>

                    <div class="post-meta">
                        <span>👤 <?php echo htmlspecialchars($v['autor']); ?></span>
                        <span>📅 <?php echo date('d/m/Y', strtotime($v['fecha_publicacion'])); ?></span>
                    </div>

                    <a href="?section=watch&video=<?php echo $v['id']; ?>" class="read-more-btn">
                        ▶ Ver video
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.video-thumb {
    position: relative;
    cursor: pointer;
    overflow: hidden;
    height: 200px;
    background: #0a1a2e;
}
.video-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.video-thumb:hover img {
    transform: scale(1.05);
}
.video-thumb-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    background: linear-gradient(135deg, #0a2040, #0d3060);
}
.video-play-icon {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,119,190,0.85);
    color: white;
    width: 52px; height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: background 0.2s, transform 0.2s;
    pointer-events: none;
}
.video-thumb:hover .video-play-icon {
    background: #0077be;
    transform: translate(-50%, -50%) scale(1.1);
}
.video-player {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
    margin-bottom: 1.5rem;
    border-radius: 12px;
    overflow: hidden;
    background: #000;
}
.video-player iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    border-radius: 12px;
}
</style>

<?php endif; ?>