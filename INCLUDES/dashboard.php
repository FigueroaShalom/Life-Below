<h1>Dashboard del explorador</h1>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <div class="dashboard-card create-post">
        <h2>➕ Crear nuevo artículo</h2>
        <form method="POST">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Categoría:</label>
                <select name="category">
                    <option value="peces">Peces</option>
                    <option value="mamiferos">Mamíferos</option>
                    <option value="moluscos">Moluscos</option>
                    <option value="crustaceos">Crustáceos</option>
                    <option value="conservacion">Conservación</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Contenido:</label>
                <textarea name="content" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label>URL de imagen (opcional):</label>
                <input type="url" name="image">
            </div>
            
            <button type="submit" name="create_post" class="btn">Publicar</button>
        </form>
    </div>
    
    <div class="dashboard-card manage-posts">
        <h2> Mis artículos</h2>
        <?php
        $posts = getPosts();
        foreach ($posts as $post):
        ?>
            <div class="post-item">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <div class="post-meta">
                    <span> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                    <span>❤️ <?php echo $post['likes'] ?? 0; ?></span>
                </div>
                <div class="post-actions">
                    <a href="?section=dashboard&edit=<?php echo urlencode($post['filename']); ?>" class="btn-small">Editar</a>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="post_id" value="<?php echo $post['filename']; ?>">
                        <button type="submit" name="delete_post" class="btn-small danger" 
                                onclick="return confirm('¿Eliminar esta publicación?')">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if (isset($_GET['edit'])): 
    $edit_post = getPost($_GET['edit']);
    if ($edit_post):
?>
    <div class="dashboard-card" style="margin-top: 2rem;">
        <h2>Editar artículo</h2>
        <form method="POST">
            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($_GET['edit']); ?>">
            
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_post['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Categoría:</label>
                <select name="category">
                    <?php
                    $categories = ['peces', 'mamiferos', 'moluscos', 'crustaceos', 'conservacion'];
                    foreach ($categories as $cat):
                    ?>
                        <option value="<?php echo $cat; ?>" <?php echo $edit_post['category'] == $cat ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Contenido:</label>
                <textarea name="content" rows="5" required><?php echo htmlspecialchars($edit_post['content']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>URL de imagen:</label>
                <input type="url" name="image" value="<?php echo htmlspecialchars($edit_post['image'] ?? ''); ?>">
            </div>
            
            <button type="submit" name="update_post" class="btn">Actualizar</button>
            <a href="?section=dashboard" class="btn" style="background: #95a5a6;">Cancelar</a>
        </form>
    </div>
<?php 
    endif;
endif; 
?>