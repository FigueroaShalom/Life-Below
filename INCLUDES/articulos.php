<h1>Artículos sobre vida marina</h1>

<?php
$selected_post = $_GET['post'] ?? '';

if ($selected_post):
    $post = getPost($selected_post);
    if ($post):
        $comments = getComments($selected_post);
?>
        <div class="article-detail">
            <a href="?section=articulos" class="back-btn">← Volver a artículos</a>
            
            <?php if (!empty($post['image'])): ?>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                     class="article-detail-img">
            <?php endif; ?>
            
            <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
            
            <div class="article-meta">
                <span>👤 <?php echo htmlspecialchars($post['author']); ?></span>
                <span> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                <span>🏷️ <?php echo htmlspecialchars($post['category']); ?></span>
                <span>❤️ <?php echo $post['likes'] ?? 0; ?> likes</span>
            </div>
            
            <div class="article-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
            
            <?php if (isset($_SESSION['user'])): ?>
                <form method="POST" class="like-form">
                    <input type="hidden" name="post_id" value="<?php echo $selected_post; ?>">
                    <button type="submit" name="like_post" class="like-btn">
                        ❤️ <?php echo $post['likes'] ?? 0; ?> Me gusta
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="comments-section">
                <h3>Comentarios (<?php echo count($comments); ?>)</h3>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <form method="POST" class="comment-form">
                        <input type="hidden" name="post_id" value="<?php echo $selected_post; ?>">
                        <textarea name="comment" placeholder="Escribe tu comentario..." required></textarea>
                        <button type="submit" name="add_comment" class="btn">Comentar</button>
                    </form>
                <?php else: ?>
                    <p><a href="?section=login">Inicia sesión</a> para comentar</p>
                <?php endif; ?>
                
                <div class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <span class="comment-user">👤 <?php echo htmlspecialchars($comment['user']); ?></span>
                                <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['date'])); ?></span>
                            </div>
                            <p class="comment-text"><?php echo htmlspecialchars($comment['comment']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
<?php
    endif;
else:
    $posts = getPosts();
?>
    <div class="posts-grid">
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <?php if (!empty($post['image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php endif; ?>
                <div class="post-content">
                    <span class="post-category"><?php echo htmlspecialchars($post['category']); ?></span>
                    <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="post-excerpt">
                        <?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?>
                    </p>
                    <div class="post-meta">
                        <span>👤 <?php echo htmlspecialchars($post['author']); ?></span>
                        <span>❤️ <?php echo $post['likes'] ?? 0; ?></span>
                        <span> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                    </div>
                    <a href="?section=articulos&post=<?php echo urlencode($post['filename']); ?>" class="read-more-btn">
                        Leer artículo
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>