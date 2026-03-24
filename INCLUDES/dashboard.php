<?php
// Solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href="?section=login";</script>';
    exit;
}

require_once __DIR__ . '/../database/Conexion_base.php';

$msg_ok  = $_GET['ok']    ?? '';
$msg_err = $_GET['error'] ?? '';

// ── PROCESAR ARTÍCULOS ────────────────────────────────────────────────────────

if (isset($_POST['crear_articulo'])) {
    $titulo    = trim($_POST['titulo']    ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $categoria = $_POST['categoria']      ?? 'peces';
    $imagen    = trim($_POST['imagen']    ?? '');

    if (empty($titulo) || empty($contenido)) {
        $msg_err = 'El título y el contenido son obligatorios.';
    } else {
        $stmt = $conn->prepare("INSERT INTO publicaciones (titulo, contenido, categoria, imagen, id_autor) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssssi", $titulo, $contenido, $categoria, $imagen, $_SESSION['user_id']);
        $stmt->execute() ? $msg_ok = '✅ Artículo publicado.' : $msg_err = '❌ Error: ' . $conn->error;
    }
}

if (isset($_POST['eliminar_articulo'])) {
    $id = (int)$_POST['id_publicacion'];
    $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id=? AND id_autor=?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute() ? $msg_ok = '✅ Artículo eliminado.' : $msg_err = '❌ No se pudo eliminar.';
}

if (isset($_POST['actualizar_articulo'])) {
    $id        = (int)$_POST['id_publicacion'];
    $titulo    = trim($_POST['titulo']    ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $categoria = $_POST['categoria']      ?? 'peces';
    $imagen    = trim($_POST['imagen']    ?? '');

    if (empty($titulo) || empty($contenido)) {
        $msg_err = 'El título y el contenido son obligatorios.';
    } else {
        $stmt = $conn->prepare("UPDATE publicaciones SET titulo=?,contenido=?,categoria=?,imagen=? WHERE id=? AND id_autor=?");
        $stmt->bind_param("ssssii", $titulo, $contenido, $categoria, $imagen, $id, $_SESSION['user_id']);
        $stmt->execute() ? $msg_ok = '✅ Artículo actualizado.' : $msg_err = '❌ Error al actualizar.';
    }
}

// ── CARGAR DATOS ──────────────────────────────────────────────────────────────

$stmt = $conn->prepare("
    SELECT p.id, p.titulo, p.categoria, p.fecha_creacion,
           (SELECT COUNT(*) FROM likes       WHERE id_publicacion = p.id) AS likes,
           (SELECT COUNT(*) FROM comentarios WHERE id_publicacion = p.id) AS comentarios
    FROM publicaciones p WHERE p.id_autor = ?
    ORDER BY p.fecha_creacion DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$mis_articulos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt2 = $conn->prepare("
    SELECT id, titulo, categoria, video_url, descripcion, fecha_publicacion
    FROM videos WHERE id_autor = ?
    ORDER BY fecha_publicacion DESC
");
$stmt2->bind_param("i", $_SESSION['user_id']);
$stmt2->execute();
$mis_videos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Artículo a editar
$editando_art = null;
if (isset($_GET['editar_art'])) {
    $eid = (int)$_GET['editar_art'];
    $est = $conn->prepare("SELECT * FROM publicaciones WHERE id=? AND id_autor=?");
    $est->bind_param("ii", $eid, $_SESSION['user_id']);
    $est->execute();
    $editando_art = $est->get_result()->fetch_assoc();
}

// Video a editar
$editando_vid = null;
if (isset($_GET['editar_vid'])) {
    $eid = (int)$_GET['editar_vid'];
    $est = $conn->prepare("SELECT * FROM videos WHERE id=? AND id_autor=?");
    $est->bind_param("ii", $eid, $_SESSION['user_id']);
    $est->execute();
    $editando_vid = $est->get_result()->fetch_assoc();
}

$cats_art = ['peces', 'mamiferos', 'moluscos', 'crustaceos', 'conservacion'];
$cats_vid = ['general', 'peces', 'mamiferos', 'conservacion', 'documental'];
$tab      = $_GET['tab'] ?? 'articulos';
?>

<h1 class="section-title">Dashboard del explorador</h1>

<?php if ($msg_ok):  ?><div class="alert alert-success"><?php echo htmlspecialchars($msg_ok);  ?></div><?php endif; ?>
<?php if ($msg_err): ?><div class="alert alert-error"><?php  echo htmlspecialchars($msg_err); ?></div><?php endif; ?>

<!-- TABS -->
<div style="display:flex; gap:1rem; margin-bottom:2rem;">
    <a href="?section=dashboard&tab=articulos"
       class="news-category-btn <?php echo $tab === 'articulos' ? 'active' : ''; ?>">
        📰 Artículos
    </a>
    <a href="?section=dashboard&tab=videos"
       class="news-category-btn <?php echo $tab === 'videos' ? 'active' : ''; ?>">
        🎬 Videos
    </a>
</div>

<?php if ($tab === 'articulos'): ?>
<!-- ── TAB ARTÍCULOS ─────────────────────────────────────────────────────── -->
<div class="dashboard-grid">

    <div class="dashboard-card">
        <h2><?php echo $editando_art ? '✏️ Editar artículo' : '➕ Nuevo artículo'; ?></h2>
        <form method="POST">
            <?php if ($editando_art): ?>
                <input type="hidden" name="id_publicacion" value="<?php echo $editando_art['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Título</label>
                <input type="text" name="titulo" required
                       value="<?php echo htmlspecialchars($editando_art['titulo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria">
                    <?php foreach ($cats_art as $cat): ?>
                        <option value="<?php echo $cat; ?>"
                            <?php echo ($editando_art['categoria'] ?? '') === $cat ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Contenido</label>
                <textarea name="contenido" rows="6" required><?php
                    echo htmlspecialchars($editando_art['contenido'] ?? '');
                ?></textarea>
            </div>
            <div class="form-group">
                <label>URL de imagen (opcional)</label>
                <input type="url" name="imagen"
                       value="<?php echo htmlspecialchars($editando_art['imagen'] ?? ''); ?>">
            </div>
            <?php if ($editando_art): ?>
                <button type="submit" name="actualizar_articulo" class="btn">Actualizar</button>
                <a href="?section=dashboard&tab=articulos" class="btn" style="background:#95a5a6; margin-top:0.5rem;">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="crear_articulo" class="btn">Publicar artículo</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="dashboard-card">
        <h2>📋 Mis artículos (<?php echo count($mis_articulos); ?>)</h2>
        <?php if (empty($mis_articulos)): ?>
            <p style="color:#7f8c8d; padding:1rem 0;">Aún no has publicado artículos.</p>
        <?php endif; ?>
        <?php foreach ($mis_articulos as $art): ?>
            <div class="post-item">
                <h3><?php echo htmlspecialchars($art['titulo']); ?></h3>
                <div class="post-meta" style="margin:0.3rem 0;">
                    <span>🏷️ <?php echo htmlspecialchars($art['categoria']); ?></span>
                    <span>❤️ <?php echo $art['likes']; ?></span>
                    <span>💬 <?php echo $art['comentarios']; ?></span>
                    <span>📅 <?php echo date('d/m/Y', strtotime($art['fecha_creacion'])); ?></span>
                </div>
                <div class="post-actions">
                    <a href="?section=articulos&post=<?php echo $art['id']; ?>" class="btn-small">Ver</a>
                    <a href="?section=dashboard&tab=articulos&editar_art=<?php echo $art['id']; ?>" class="btn-small">Editar</a>
                    <form method="POST" style="display:inline;"
                          onsubmit="return confirm('¿Eliminar este artículo?')">
                        <input type="hidden" name="id_publicacion" value="<?php echo $art['id']; ?>">
                        <button type="submit" name="eliminar_articulo" class="btn-small danger">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php elseif ($tab === 'videos'): ?>
<!-- ── TAB VIDEOS ────────────────────────────────────────────────────────── -->
<div class="dashboard-grid">

    <div class="dashboard-card">
        <h2><?php echo $editando_vid ? '✏️ Editar video' : '➕ Nuevo video'; ?></h2>
        <form method="POST" action="database/procesar_video.php">
            <?php if ($editando_vid): ?>
                <input type="hidden" name="id_video" value="<?php echo $editando_vid['id']; ?>">
                <input type="hidden" name="accion"   value="actualizar_video">
            <?php else: ?>
                <input type="hidden" name="accion" value="crear_video">
            <?php endif; ?>
            <div class="form-group">
                <label>Título</label>
                <input type="text" name="titulo" required
                       value="<?php echo htmlspecialchars($editando_vid['titulo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria">
                    <?php foreach ($cats_vid as $cat): ?>
                        <option value="<?php echo $cat; ?>"
                            <?php echo ($editando_vid['categoria'] ?? 'general') === $cat ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>URL del video (YouTube)</label>
                <input type="url" name="video_url" required
                       placeholder="https://www.youtube.com/watch?v=..."
                       value="<?php echo htmlspecialchars($editando_vid['video_url'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Descripción (opcional)</label>
                <textarea name="descripcion" rows="4"><?php
                    echo htmlspecialchars($editando_vid['descripcion'] ?? '');
                ?></textarea>
            </div>
            <?php if ($editando_vid): ?>
                <button type="submit" class="btn">Actualizar video</button>
                <a href="?section=dashboard&tab=videos" class="btn" style="background:#95a5a6; margin-top:0.5rem;">Cancelar</a>
            <?php else: ?>
                <button type="submit" class="btn">Publicar video</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="dashboard-card">
        <h2>🎬 Mis videos (<?php echo count($mis_videos); ?>)</h2>
        <?php if (empty($mis_videos)): ?>
            <p style="color:#7f8c8d; padding:1rem 0;">Aún no has publicado videos.</p>
        <?php endif; ?>
        <?php foreach ($mis_videos as $vid): ?>
            <div class="post-item">
                <h3><?php echo htmlspecialchars($vid['titulo']); ?></h3>
                <div class="post-meta" style="margin:0.3rem 0;">
                    <span>🏷️ <?php echo htmlspecialchars($vid['categoria'] ?? 'general'); ?></span>
                    <span>📅 <?php echo date('d/m/Y', strtotime($vid['fecha_publicacion'])); ?></span>
                </div>
                <div class="post-actions">
                    <a href="?section=watch&video=<?php echo $vid['id']; ?>" class="btn-small">Ver</a>
                    <a href="?section=dashboard&tab=videos&editar_vid=<?php echo $vid['id']; ?>" class="btn-small">Editar</a>
                    <form method="POST" action="database/procesar_video.php" style="display:inline;"
                          onsubmit="return confirm('¿Eliminar este video?')">
                        <input type="hidden" name="id_video" value="<?php echo $vid['id']; ?>">
                        <input type="hidden" name="accion"   value="eliminar_video">
                        <button type="submit" class="btn-small danger">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php endif; ?>