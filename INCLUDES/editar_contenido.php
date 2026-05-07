<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id'])) {
    die("Debes iniciar sesión para editar.");
}

// ✅ Ruta corregida
require_once __DIR__ . '/../database/Conexion_base.php';

$id_post = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_post) {
    echo "<div class='alert alert-danger'>No se proporcionó un ID válido.</div>";
    exit;
}

// Solo el autor puede editar
$stmt = $conn->prepare("SELECT * FROM publicaciones WHERE id = ? AND id_autor = ?");
$stmt->bind_param("ii", $id_post, $_SESSION['id']);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    echo "<div class='alert alert-danger'>No tienes permiso para editar esto o el artículo no existe.</div>";
    exit;
}

$cats = ['peces', 'mamiferos', 'moluscos', 'crustaceos', 'conservacion'];
?>

<div class="dashboard-card">
    <h2>✏️ Editar Artículo</h2>

    <div id="alerta-editar" class="alert d-none"></div>

    <form id="form-editar-contenido">
        <input type="hidden" name="id_publicacion" value="<?php echo $post['id']; ?>">
        <input type="hidden" name="accion" value="editar_articulo">

        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" required
                   value="<?php echo htmlspecialchars($post['titulo']); ?>">
        </div>

        <div class="form-group">
            <label>Categoría</label>
            <select name="categoria" class="form-control">
                <?php foreach ($cats as $cat): ?>
                    <option value="<?php echo $cat; ?>"
                        <?php echo $post['categoria'] === $cat ? 'selected' : ''; ?>>
                        <?php echo ucfirst($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>URL de imagen (opcional)</label>
            <input type="text" name="imagen" class="form-control"
                   value="<?php echo htmlspecialchars($post['imagen'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Contenido</label>
            <textarea name="contenido" rows="10" class="form-control" required><?php
                echo htmlspecialchars($post['contenido']);
            ?></textarea>
        </div>

        <div style="display:flex; gap:10px; margin-top:1rem;">
            <button type="submit" class="btn">✅ Guardar Cambios</button>
            <button type="button" onclick="cargar('mis_Publicaciones')" class="btn" style="background:#95a5a6;">Cancelar</button>
        </div>
    </form>
</div>

<script>
document.getElementById('form-editar-contenido').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('../database/procesar_crear_contenido.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        const alerta = document.getElementById('alerta-editar');
        alerta.classList.remove('d-none', 'alert-success', 'alert-danger');

        // procesar_crear_contenido devuelve un <script> con alert() al editar
        // lo ejecutamos insertándolo en el DOM
        if (data.includes('Cambios guardados')) {
            alerta.classList.add('alert-success');
            alerta.innerText = '✅ ¡Cambios guardados correctamente!';
            // Recargar mis publicaciones tras 1.5s
            setTimeout(() => cargar('mis_Publicaciones'), 1500);
        } else {
            alerta.classList.add('alert-danger');
            alerta.innerText = '❌ Error al guardar: ' + data;
        }
    })
    .catch(err => {
        console.error(err);
    });
});
</script>