<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id'])) {
    echo '<p style="color:red;">Sesión no válida.</p>';
    exit;
}

require_once __DIR__ . '/../database/Conexion_base.php';

// Artículos del usuario
$stmt = $conn->prepare("
    SELECT p.id, p.titulo, p.categoria, p.imagen, p.fecha_creacion,
           (SELECT COUNT(*) FROM likes       WHERE id_publicacion = p.id) AS likes,
           (SELECT COUNT(*) FROM comentarios WHERE id_publicacion = p.id) AS comentarios
    FROM publicaciones p
    WHERE p.id_autor = ?
    ORDER BY p.fecha_creacion DESC
");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$articulos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Videos del usuario
$stmt2 = $conn->prepare("
    SELECT id, titulo, categoria, video_url, fecha_publicacion
    FROM videos
    WHERE id_autor = ?
    ORDER BY fecha_publicacion DESC
");
$stmt2->bind_param("i", $_SESSION['id']);
$stmt2->execute();
$videos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="section-title">Mis Publicaciones</h1>

<!-- TABS JS -->
<div style="display:flex; gap:1rem; margin-bottom:2rem;">
    <button onclick="switchTabPub('articulos')" id="pub-btn-articulos" class="news-category-btn active">
        📰 Artículos (<?php echo count($articulos); ?>)
    </button>
    <button onclick="switchTabPub('videos')" id="pub-btn-videos" class="news-category-btn">
        🎬 Videos (<?php echo count($videos); ?>)
    </button>
</div>

<!-- ── ARTÍCULOS ─────────────────────────────────────────────────────────── -->
<div id="pub-articulos">
<?php if (empty($articulos)): ?>
    <div style="text-align:center;padding:3rem;background:#fff;border-radius:16px;border:1.5px solid rgba(0,120,190,0.1);">
        <p style="font-size:3rem;">📝</p>
        <h3>Aún no has publicado artículos</h3>
        <button onclick="cargar('crear_Contenido')" class="btn" style="margin-top:1rem;">Crear mi primer artículo</button>
    </div>
<?php else: ?>
    <div class="posts-grid">
        <?php foreach ($articulos as $art): ?>
            <div class="post-card">
                <?php if (!empty($art['imagen'])): ?>
                    <img src="../<?php echo htmlspecialchars($art['imagen']); ?>"
                         alt="<?php echo htmlspecialchars($art['titulo']); ?>">
                <?php else: ?>
                    <div style="height:140px;background:linear-gradient(135deg,#e6f3ff,#b3e0ff);display:flex;align-items:center;justify-content:center;font-size:2.5rem;">🌊</div>
                <?php endif; ?>
                <div class="post-content">
                    <span class="post-category"><?php echo htmlspecialchars($art['categoria']); ?></span>
                    <h3 class="post-title"><?php echo htmlspecialchars($art['titulo']); ?></h3>
                    <div class="post-meta">
                        <span>❤️ <?php echo $art['likes']; ?></span>
                        <span>💬 <?php echo $art['comentarios']; ?></span>
                        <span>📅 <?php echo date('d/m/Y', strtotime($art['fecha_creacion'])); ?></span>
                    </div>
                    <div class="post-actions" style="margin-top:.8rem;">
                        <a href="../index.php?section=articulos&post=<?php echo $art['id']; ?>" class="btn-small" target="_blank">Ver</a>
                        <a href="javascript:void(0)" onclick="cargar('editar_contenido?id=<?php echo $art['id']; ?>')" class="btn-small">Editar</a>
                        <form method="POST" action="../database/procesar_crear_contenido.php" style="display:inline;" 
      onsubmit="return confirm('¿Eliminar este artículo?')">
    
    <input type="hidden" name="id_publicacion" value="<?php echo $art['id']; ?>">
    
    <input type="hidden" name="accion" value="eliminar_articulo">
    
    <button type="submit" class="btn-small danger">Eliminar</button>
</form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>

<!-- ── VIDEOS ─────────────────────────────────────────────────────────────── -->
<div id="pub-videos" style="display:none;">
<?php if (empty($videos)): ?>
    <div style="text-align:center;padding:3rem;background:#fff;border-radius:16px;border:1.5px solid rgba(0,120,190,0.1);">
        <p style="font-size:3rem;">🎬</p>
        <h3>Aún no has publicado videos</h3>
        <button onclick="cargar('crear_Contenido')" class="btn" style="margin-top:1rem;">Subir mi primer video</button>
    </div>
<?php else: ?>
    <div class="posts-grid">
        <?php foreach ($videos as $vid): ?>
            <?php
            // Convertir URL de YouTube a embed
            $yt_url = $vid['video_url'];
            $yt_id  = '';
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $yt_url, $m)) {
                $yt_id = $m[1];
            }
            ?>
            <div class="post-card">
                <?php if ($yt_id): ?>
                    <img src="https://img.youtube.com/vi/<?php echo $yt_id; ?>/hqdefault.jpg"
                         alt="<?php echo htmlspecialchars($vid['titulo']); ?>"
                         style="width:100%;height:160px;object-fit:cover;">
                <?php else: ?>
                    <div style="height:160px;background:linear-gradient(135deg,#001828,#003a5c);display:flex;align-items:center;justify-content:center;font-size:2.5rem;">🎬</div>
                <?php endif; ?>
                <div class="post-content">
                    <span class="post-category"><?php echo htmlspecialchars($vid['categoria'] ?? 'general'); ?></span>
                    <h3 class="post-title"><?php echo htmlspecialchars($vid['titulo']); ?></h3>
                    <div class="post-meta">
                        <span>📅 <?php echo date('d/m/Y', strtotime($vid['fecha_publicacion'])); ?></span>
                    </div>
                    <div class="post-actions" style="margin-top:.8rem;">
                        <a href="../index.php?section=watch&video=<?php echo $vid['id']; ?>" class="btn-small" target="_blank">Ver</a>
                        <a href="crear_contenido.php?editar_vid=<?php echo $vid['id']; ?>" class="btn-small">Editar</a>
                        <form method="POST" action="../database/procesar_video.php" style="display:inline;"
                              onsubmit="return confirm('¿Eliminar este video?')">
                            <input type="hidden" name="id_video"  value="<?php echo $vid['id']; ?>">
                            <input type="hidden" name="accion"    value="eliminar_video">
                            <button type="submit" class="btn-small danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>

<script>
function switchTabPub(tab) {
    document.getElementById('pub-articulos').style.display = tab === 'articulos' ? 'block' : 'none';
    document.getElementById('pub-videos').style.display    = tab === 'videos'    ? 'block' : 'none';
    document.getElementById('pub-btn-articulos').classList.toggle('active', tab === 'articulos');
    document.getElementById('pub-btn-videos').classList.toggle('active',    tab === 'videos');
}
</script>