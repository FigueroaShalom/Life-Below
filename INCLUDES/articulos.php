<?php
// Conexión
require_once __DIR__ . '/../database/Conexion_base.php';

$selected_id   = isset($_GET['post']) ? (int)$_GET['post'] : 0;
$search_query = trim($_GET['q'] ?? '');
?>
<style>
.hy-articles-wrap {
    max-width: 1280px;
    margin: 0 auto;
    padding: 3rem 2rem;
}

/* Header */
.hy-articles-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 0.5rem;
    margin-bottom: 2.5rem;
}
.hy-articles-title {
    font-family: var(--font);
    font-weight: 900;
    font-size: 2.2rem;
    color: var(--header-text);
    letter-spacing: -.5px;
    line-height: 1.1;
    margin: 0;
}
.hy-articles-sub {
    color: var(--muted);
    font-family: var(--font);
    font-size: .95rem;
    margin-top: .3rem;
}

/* Controls (Tabs + Search) */
.hy-articles-controls-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.hy-articles-tabs {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.hy-articles-tab {
    padding: 9px 22px;
    border-radius: 50px;
    border: 1.5px solid var(--border);
    color: var(--ocean);
    font-weight: 800;
    font-size: .87rem;
    text-decoration: none;
    background: var(--card-bg);
    transition: all .22s ease;
    font-family: var(--font);
}

.hy-articles-tab:hover, 
.hy-articles-tab.active {
    background: var(--ocean);
    color: #fff;
    border-color: var(--ocean);
    box-shadow: 0 4px 16px rgba(0,119,190,0.18);
}

/* Search Form */
.hy-articles-search {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    align-items: center;
}

.hy-articles-search-input {
    padding: .72rem 1.1rem;
    border-radius: 50px;
    border: 1.5px solid var(--border);
    font-size: .92rem;
    min-width: 240px;
    font-family: var(--font);
    color: var(--ocean);
    background: var(--card-bg);
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    font-weight: 700;
}

.hy-articles-search-input::placeholder {
    color: var(--ocean);
    opacity: 0.6;
}

.hy-articles-search-input:focus {
    border-color: var(--ocean);
    box-shadow: 0 0 0 3px rgba(0,119,190,0.12);
}

.hy-articles-btn {
    padding: 9px 20px;
    border-radius: 50px;
    border: 1.5px solid var(--border);
    color: var(--ocean);
    font-weight: 800;
    font-size: .87rem;
    text-decoration: none;
    background: var(--card-bg);
    cursor: pointer;
    transition: all .22s ease;
    font-family: var(--font);
}

.hy-articles-btn:hover {
    background: var(--ocean);
    color: #fff;
    border-color: var(--ocean);
}

.hy-articles-btn-primary {
    background: var(--ocean);
    color: #fff;
    border-color: var(--ocean);
}

.hy-articles-btn-primary:hover {
    background: var(--teal);
    border-color: var(--teal);
}

/* Posts Grid */
.hy-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 1.8rem;
    margin-bottom: 2rem;
}

/* Post Card */
.hy-post-card {
    background: var(--card-bg);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 28px rgba(0,40,80,0.06);
    border: 1.5px solid var(--border);
    transition: transform .25s ease, box-shadow .25s ease;
    display: flex;
    flex-direction: column;
}

.hy-post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 44px rgba(0,40,80,0.15);
}

.hy-post-card-img-wrap {
    position: relative;
    width: 100%;
    height: 190px;
    overflow: hidden;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--navy), var(--deep));
}

.hy-post-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}

.hy-post-card:hover .hy-post-card-img {
    transform: scale(1.04);
}

.hy-post-card-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--navy), var(--deep));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}

.hy-post-card-body {
    padding: 1.4rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.hy-post-card-category {
    display: inline-block;
    padding: 3px 12px;
    background: rgba(0,120,190,0.08);
    color: var(--ocean);
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: .6rem;
    width: fit-content;
}

.hy-post-card-title {
    font-size: 1.1rem;
    font-weight: 900;
    color: var(--header-text);
    margin-bottom: .6rem;
    line-height: 1.35;
    font-family: var(--font);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hy-post-card-excerpt {
    font-size: .88rem;
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 1.2rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hy-post-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: .8rem;
    border-top: 1px solid var(--border);
}

.hy-post-card-meta {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: .78rem;
    color: var(--muted);
}

.hy-post-card-link {
    display: inline-block;
    padding: 7px 18px;
    background: var(--ocean);
    color: #fff;
    border-radius: 50px;
    font-size: .78rem;
    font-weight: 900;
    text-decoration: none;
    transition: background .2s, transform .15s;
    font-family: var(--font);
}

.hy-post-card-link:hover {
    background: var(--teal);
    transform: translateX(2px);
}

/* Detail View Enhancements */
.hy-article-detail {
    background: var(--card-bg);
    border-radius: 18px;
    padding: 3rem 2.5rem;
    box-shadow: 0 8px 32px rgba(0,40,80,0.06);
    border: 1.5px solid var(--border);
    margin: 1.5rem 0;
}

.hy-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--ocean);
    font-weight: 800;
    text-decoration: none;
    margin-bottom: 2rem;
    font-size: .95rem;
    transition: all .2s ease;
}

.hy-back-btn:hover {
    color: var(--teal);
    transform: translateX(-3px);
}

.hy-article-detail-img {
    width: 100%;
    max-height: 460px;
    object-fit: cover;
    border-radius: 14px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.05);
}

.hy-post-title-detail {
    font-size: 2.2rem;
    font-weight: 900;
    color: var(--header-text);
    margin-bottom: 1rem;
    line-height: 1.25;
}

.hy-article-meta-detail {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    font-size: .88rem;
    color: var(--muted);
    padding: 1.2rem 0;
    border-bottom: 1.5px solid var(--border);
    margin-bottom: 2rem;
    font-weight: 600;
}

.hy-article-content-detail {
    line-height: 1.8;
    font-size: 1.05rem;
    color: var(--text-color);
    margin-bottom: 2.5rem;
}

.hy-like-form {
    display: inline-block;
    margin-bottom: 2.5rem;
}

.hy-like-btn {
    padding: 10px 24px;
    border-radius: 50px;
    background: rgba(255,60,100,0.08);
    color: #e0305a;
    border: 1.5px solid rgba(255,60,100,0.25);
    font-family: var(--font);
    font-weight: 800;
    font-size: .9rem;
    cursor: pointer;
    transition: all .22s ease;
}

.hy-like-btn:hover, 
.hy-like-btn.liked {
    background: rgba(255,60,100,0.16);
    border-color: rgba(255,60,100,0.45);
    box-shadow: 0 4px 15px rgba(224,48,90,0.15);
}

.hy-comments-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1.5px solid var(--border);
}

.hy-comments-section h3 {
    font-size: 1.3rem;
    font-weight: 900;
    color: var(--header-text);
    margin-bottom: 1.5rem;
}

.hy-comment-form textarea {
    width: 100%;
    padding: 14px 18px;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    font-family: var(--font);
    font-size: .95rem;
    resize: vertical;
    outline: none;
    margin-bottom: 1rem;
    background: var(--input-bg);
    color: var(--text-color);
    transition: border-color .2s, box-shadow .2s;
}

.hy-comment-form textarea:focus {
    border-color: var(--ocean);
    box-shadow: 0 0 0 3px rgba(0,119,190,0.12);
}

.hy-comments-list {
    margin-top: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.hy-comment-card {
    background: var(--input-bg);
    border-radius: 12px;
    padding: 1.2rem 1.4rem;
    border: 1px solid var(--border);
    transition: border-color .2s ease;
}

.hy-comment-card:hover {
    border-color: rgba(0,119,190,0.3);
}

.hy-comment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .6rem;
}

.hy-comment-user {
    font-weight: 800;
    color: var(--ocean);
    font-size: .9rem;
}

.hy-comment-date {
    font-size: .78rem;
    color: var(--muted);
}

.hy-comment-text {
    font-size: .92rem;
    color: var(--text-color);
    line-height: 1.6;
}

@media (max-width: 768px) {
    .hy-articles-controls-row {
        flex-direction: column;
        align-items: stretch;
    }
    .hy-articles-tabs {
        justify-content: center;
    }
    .hy-articles-search {
        justify-content: center;
    }
    .hy-articles-search-input {
        min-width: 100%;
        flex: 1;
    }
    .hy-article-detail {
        padding: 2rem 1.5rem;
    }
    .hy-post-title-detail {
        font-size: 1.8rem;
    }
}
</style>
<?php
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
    <div class="hy-articles-wrap">
        <div class="hy-article-detail">
            <a href="?section=articulos" class="hy-back-btn">← Volver a artículos</a>

            <?php if (!empty($post['imagen'])): ?>
                <img src="<?php echo htmlspecialchars($post['imagen']); ?>"
                     alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                     class="hy-article-detail-img" loading="lazy">
            <?php endif; ?>

            <h2 class="hy-post-title-detail"><?php echo htmlspecialchars($post['titulo']); ?></h2>

            <div class="hy-article-meta-detail">
                <span>👤 <?php echo htmlspecialchars($post['autor']); ?></span>
                <span>📅 <?php echo date('d/m/Y', strtotime($post['fecha_creacion'])); ?></span>
                <span>🏷️ <?php echo htmlspecialchars($post['categoria']); ?></span>
                <span>❤️ <?php echo $post['total_likes']; ?> likes</span>
                <span>💬 <?php echo $post['total_comentarios']; ?> comentarios</span>
            </div>

            <div class="hy-article-content-detail" id="article-content">
                <?php echo nl2br(htmlspecialchars($post['contenido'])); ?>
            </div>

            <?php if (!empty($_GET['scroll'])): ?>
            <script>
            window.addEventListener('load', function() {
                const target = document.getElementById('article-content');
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            });
            </script>
            <?php endif; ?>

            <!-- LIKES -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="database/procesar_like.php" class="hy-like-form">
                    <input type="hidden" name="id_publicacion" value="<?php echo $selected_id; ?>">
                    <input type="hidden" name="redirect" value="?section=articulos&post=<?php echo $selected_id; ?>">
                    <button type="submit" class="hy-like-btn <?php echo $ya_dio_like ? 'liked' : ''; ?>">
                        <?php echo $ya_dio_like ? '❤️ Quitar like' : '🤍 Me gusta'; ?>
                        (<?php echo $post['total_likes']; ?>)
                    </button>
                </form>
            <?php else: ?>
                <p style="margin:1.5rem 0; font-family: var(--font); color: var(--muted);">
                    <a href="?section=login" style="color: var(--ocean); font-weight: 800; text-decoration: none;">Inicia sesión</a> para dar like y comentar.
                </p>
            <?php endif; ?>

            <!-- COMENTARIOS -->
            <div class="hy-comments-section">
                <h3>Comentarios (<?php echo count($comentarios); ?>)</h3>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="database/procesar_comentario.php" class="hy-comment-form">
                        <input type="hidden" name="id_publicacion" value="<?php echo $selected_id; ?>">
                        <input type="hidden" name="redirect" value="?section=articulos&post=<?php echo $selected_id; ?>">
                        <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea>
                        <button type="submit" class="hy-articles-btn hy-articles-btn-primary">Comentar</button>
                    </form>
                <?php endif; ?>

                <div class="hy-comments-list">
                    <?php foreach ($comentarios as $c): ?>
                        <div class="hy-comment-card">
                            <div class="hy-comment-header">
                                <span class="hy-comment-user">👤 <?php echo htmlspecialchars($c['user']); ?></span>
                                <span class="hy-comment-date"><?php echo date('d/m/Y H:i', strtotime($c['fecha'])); ?></span>
                            </div>
                            <p class="hy-comment-text"><?php echo nl2br(htmlspecialchars($c['comentario'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

<?php
    endif; // fin if post

else:
    // ── LISTADO DE ARTÍCULOS ──────────────────────────────────────────────
    $cat_filter = $_GET['cat'] ?? '';

    $where = [];
    if ($cat_filter) {
        $cat_safe = $conn->real_escape_string($cat_filter);
        $where[] = "p.categoria = '$cat_safe'";
    }
    if ($search_query) {
        $query_safe = $conn->real_escape_string($search_query);
        $where[] = "(
            p.titulo LIKE '%$query_safe%' OR
            p.contenido LIKE '%$query_safe%' OR
            p.categoria LIKE '%$query_safe%' OR
            u.user LIKE '%$query_safe%'
        )";
    }

    $sql = "
        SELECT p.id, p.titulo, p.contenido, p.categoria, p.imagen, p.fecha_creacion,
               u.user AS autor,
               (SELECT COUNT(*) FROM likes WHERE id_publicacion = p.id) AS total_likes
        FROM publicaciones p
        JOIN usuarios u ON p.id_autor = u.id
    ";
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= " ORDER BY p.fecha_creacion DESC";

    $posts = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

    $categorias = ['peces', 'mamiferos', 'moluscos', 'crustaceos', 'conservacion'];
?>
<div class="hy-articles-wrap">

    <div class="hy-articles-header">
        <h1 class="hy-articles-title">Artículos sobre vida marina</h1>
        <p class="hy-articles-sub">Explora publicaciones, investigaciones y lecturas de conservación marina y costera</p>
    </div>

    <div class="hy-articles-controls-row">
        <!-- Filtro por categoría -->
        <div class="hy-articles-tabs">
            <a href="?section=articulos<?php echo $search_query ? '&q='.urlencode($search_query) : ''; ?>"
               class="hy-articles-tab <?php echo !$cat_filter ? 'active' : ''; ?>">Todos</a>
            <?php foreach ($categorias as $cat): ?>
                <a href="?section=articulos&cat=<?php echo $cat; ?><?php echo $search_query ? '&q='.urlencode($search_query) : ''; ?>"
                   class="hy-articles-tab <?php echo $cat_filter === $cat ? 'active' : ''; ?>">
                    <?php echo ucfirst($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form action="?section=articulos" method="GET" class="hy-articles-search">
            <input type="hidden" name="section" value="articulos">
            <?php if ($cat_filter): ?><input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat_filter); ?>"><?php endif; ?>
            <input type="text" name="q" value="<?php echo htmlspecialchars($search_query); ?>"
                   placeholder="Buscar artículos..."
                   class="hy-articles-search-input">
            <button type="submit" class="hy-articles-btn hy-articles-btn-primary">Buscar</button>
            <?php if ($search_query): ?>
                <a href="?section=articulos<?php echo $cat_filter ? '&cat='.urlencode($cat_filter) : ''; ?>" class="hy-articles-btn">✕ Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($search_query): ?>
        <p style="text-align:center; color: var(--muted); font-weight: 700; margin-top: -1.5rem; margin-bottom: 2.5rem; font-family: var(--font);">
            Resultados para "<?php echo htmlspecialchars($search_query); ?>"
        </p>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div style="text-align:center; padding: 4rem 2rem; background: var(--card-bg); border-radius: 18px; border: 1.5px solid var(--border);">
            <h3 style="font-size:1.3rem; color: var(--header-text); margin-bottom:.5rem; font-family: var(--font); font-weight: 900;">No se encontraron artículos</h3>
            <p style="color: var(--muted); font-family: var(--font); margin-bottom: 1.5rem;">Intenta con otro término de búsqueda o categoría.</p>
            <?php if ($search_query): ?>
                <a href="?section=noticias&q=<?php echo urlencode($search_query); ?>"
                   style="color: var(--ocean); font-weight: 800; font-family: var(--font); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                   Buscar noticias relacionadas →
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="hy-posts-grid">
            <?php foreach ($posts as $post): ?>
                <div class="hy-post-card">
                    <div class="hy-post-card-img-wrap">
                        <?php if (!empty($post['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($post['imagen']); ?>"
                                 alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                                 class="hy-post-card-img" loading="lazy">
                        <?php else: ?>
                            <div class="hy-post-card-placeholder">🌊</div>
                        <?php endif; ?>
                    </div>
                    <div class="hy-post-card-body">
                        <span class="hy-post-card-category"><?php echo htmlspecialchars($post['categoria']); ?></span>
                        <h3 class="hy-post-card-title"><?php echo htmlspecialchars($post['titulo']); ?></h3>
                        <p class="hy-post-card-excerpt">
                            <?php echo substr(htmlspecialchars($post['contenido']), 0, 140) . '...'; ?>
                        </p>
                        <div class="hy-post-card-footer">
                            <div class="hy-post-card-meta">
                                <span>👤 <?php echo htmlspecialchars($post['autor']); ?></span>
                                <span>❤️ <?php echo $post['total_likes']; ?></span>
                            </div>
                            <a href="?section=articulos&post=<?php echo $post['id']; ?>" class="hy-post-card-link">
                                Leer →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
<?php endif; // fin listado vs detalle ?>