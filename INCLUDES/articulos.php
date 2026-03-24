<?php
// Conexión
require_once __DIR__ . '/../database/Conexion_base.php';

$selected_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;

if ($selected_id):
    // ── DETALLE DEL ARTÍCULO ──────────────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT p.*, u.user AS autor,
               (SELECT COUNT(*) FROM likes    WHERE id_publicacion = p.id) AS total_likes,
               (SELECT COUNT(*) FROM comentarios WHERE id_publicacion = p.id) AS total_comentarios
        FROM publicaciones p
        JOIN usuarios u ON p.id_autor = u.id
        WHERE p.id = ?
    ");
    $stmt->bind_param("i", $selected_id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();

    if (!$post):
        echo '<p>Artículo no encontrado. <a href="?section=articulos">← Volver</a></p>';
    else:
        // ¿El usuario ya dio like?
        $ya_dio_like = false;
        if (isset($_SESSION['user_id'])) {
            $lk = $conn->prepare("SELECT id FROM likes WHERE id_publicacion=? AND id_usuario=?");
            $lk->bind_param("ii", $selected_id, $_SESSION['user_id']);
            $lk->execute();
            $ya_dio_like = $lk->get_result()->num_rows > 0;
        }

        // Comentarios
        $cstmt = $conn->prepare("
            SELECT c.comentario, c.fecha, u.user
            FROM comentarios c
            JOIN usuarios u ON c.id_usuario = u.id
            WHERE c.id_publicacion = ?
            ORDER BY c.fecha ASC
        ");
        $cstmt->bind_param("i", $selected_id);
        $cstmt->execute();
        $comentarios = $cstmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
    <div class="article-detail">
        <a href="?section=articulos" class="back-btn">← Volver a artículos</a>

        <?php if (!empty($post['imagen'])): ?>
            <img src="<?php echo htmlspecialchars($post['imagen']); ?>"
                 alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                 class="article-detail-img">
        <?php endif; ?>

        <h2 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h2>

        <div class="article-meta">
            <span>👤 <?php echo htmlspecialchars($post['autor']); ?></span>
            <span>📅 <?php echo date('d/m/Y', strtotime($post['fecha_creacion'])); ?></span>
            <span>🏷️ <?php echo htmlspecialchars($post['categoria']); ?></span>
            <span>❤️ <?php echo $post['total_likes']; ?> likes</span>
            <span>💬 <?php echo $post['total_comentarios']; ?> comentarios</span>
        </div>

        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($post['contenido'])); ?>
        </div>

        <!-- LIKES -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="database/procesar_like.php" class="like-form">
                <input type="hidden" name="id_publicacion" value="<?php echo $selected_id; ?>">
                <input type="hidden" name="redirect" value="?section=articulos&post=<?php echo $selected_id; ?>">
                <button type="submit" class="like-btn <?php echo $ya_dio_like ? 'liked' : ''; ?>">
                    <?php echo $ya_dio_like ? '❤️ Quitar like' : '🤍 Me gusta'; ?>
                    (<?php echo $post['total_likes']; ?>)
                </button>
            </form>
        <?php else: ?>
            <p style="margin:1rem 0;">
                <a href="?section=login">Inicia sesión</a> para dar like y comentar.
            </p>
        <?php endif; ?>

        <!-- COMENTARIOS -->
        <div class="comments-section">
            <h3>Comentarios (<?php echo count($comentarios); ?>)</h3>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="database/procesar_comentario.php" class="comment-form">
                    <input type="hidden" name="id_publicacion" value="<?php echo $selected_id; ?>">
                    <input type="hidden" name="redirect" value="?section=articulos&post=<?php echo $selected_id; ?>">
                    <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea>
                    <button type="submit" class="btn">Comentar</button>
                </form>
            <?php endif; ?>

            <div class="comments-list">
                <?php foreach ($comentarios as $c): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <span class="comment-user">👤 <?php echo htmlspecialchars($c['user']); ?></span>
                            <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($c['fecha'])); ?></span>
                        </div>
                        <p class="comment-text"><?php echo nl2br(htmlspecialchars($c['comentario'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php
    endif; // fin if post

else:
    // ── LISTADO DE ARTÍCULOS ──────────────────────────────────────────────
    $cat_filter = $_GET['cat'] ?? '';

    $sql = "
        SELECT p.id, p.titulo, p.contenido, p.categoria, p.imagen, p.fecha_creacion,
               u.user AS autor,
               (SELECT COUNT(*) FROM likes WHERE id_publicacion = p.id) AS total_likes
        FROM publicaciones p
        JOIN usuarios u ON p.id_autor = u.id
    ";
    if ($cat_filter) {
        $cat_safe = $conn->real_escape_string($cat_filter);
        $sql .= " WHERE p.categoria = '$cat_safe'";
    }
    $sql .= " ORDER BY p.fecha_creacion DESC";

    $posts = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

    $categorias = ['peces', 'mamiferos', 'moluscos', 'crustaceos', 'conservacion'];
?>

<h1 class="section-title">Artículos sobre vida marina</h1>

<!-- Filtro por categoría -->
<div style="text-align:center; margin-bottom:1.5rem;">
    <a href="?section=articulos"
       class="news-category-btn <?php echo !$cat_filter ? 'active' : ''; ?>">Todos</a>
    <?php foreach ($categorias as $cat): ?>
        <a href="?section=articulos&cat=<?php echo $cat; ?>"
           class="news-category-btn <?php echo $cat_filter === $cat ? 'active' : ''; ?>">
            <?php echo ucfirst($cat); ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($posts)): ?>
    <p style="text-align:center; padding:3rem;">No hay artículos publicados aún.</p>
<?php else: ?>
    <div class="posts-grid">
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <?php if (!empty($post['imagen'])): ?>
                    <img src="<?php echo htmlspecialchars($post['imagen']); ?>"
                         alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                <?php else: ?>
                    <div style="height:180px;background:linear-gradient(135deg,#e6f3ff,#b3e0ff);display:flex;align-items:center;justify-content:center;font-size:3rem;">🌊</div>
                <?php endif; ?>
                <div class="post-content">
                    <span class="post-category"><?php echo htmlspecialchars($post['categoria']); ?></span>
                    <h3 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h3>
                    <p class="post-excerpt">
                        <?php echo substr(htmlspecialchars($post['contenido']), 0, 150) . '...'; ?>
                    </p>
                    <div class="post-meta">
                        <span>👤 <?php echo htmlspecialchars($post['autor']); ?></span>
                        <span>❤️ <?php echo $post['total_likes']; ?></span>
                        <span>📅 <?php echo date('d/m/Y', strtotime($post['fecha_creacion'])); ?></span>
                    </div>
                    <a href="?section=articulos&post=<?php echo $post['id']; ?>" class="read-more-btn">
                        Leer artículo →
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php endif; // fin listado vs detalle ?>