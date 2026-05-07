<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id'])) {
    echo '<p style="color:red;">Sesión no válida.</p>';
    exit;
}

require_once __DIR__ . '/../database/Conexion_base.php';

// ── Agregar columna estado si no existe (solo una vez) ────────────────────────
$conn->query("
    ALTER TABLE publicaciones
    ADD COLUMN IF NOT EXISTS estado ENUM('publicado','borrador') NOT NULL DEFAULT 'publicado'
");

$msg_ok  = '';
$msg_err = '';

// ── Guardar como borrador ─────────────────────────────────────────────────────
if (isset($_POST['guardar_borrador'])) {
    $titulo    = trim($_POST['titulo']    ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $categoria = $_POST['categoria']      ?? 'peces';
    $imagen    = trim($_POST['imagen']    ?? '');

    if (empty($titulo)) {
        $msg_err = 'El título es obligatorio para guardar el borrador.';
    } else {
        $stmt = $conn->prepare("INSERT INTO publicaciones (titulo, contenido, categoria, imagen, id_autor, estado) VALUES (?,?,?,?,?,'borrador')");
        $stmt->bind_param("ssssi", $titulo, $contenido, $categoria, $imagen, $_SESSION['id']);
        $stmt->execute() ? $msg_ok = '✅ Borrador guardado.' : $msg_err = '❌ Error: ' . $conn->error;
    }
}

// ── Publicar borrador ─────────────────────────────────────────────────────────
if (isset($_POST['publicar_borrador'])) {
    $id = (int)$_POST['id_borrador'];
    $stmt = $conn->prepare("UPDATE publicaciones SET estado='publicado' WHERE id=? AND id_autor=?");
    $stmt->bind_param("ii", $id, $_SESSION['id']);
    $stmt->execute() ? $msg_ok = '✅ Borrador publicado.' : $msg_err = '❌ Error al publicar.';
}

// ── Eliminar borrador ─────────────────────────────────────────────────────────
if (isset($_POST['eliminar_borrador'])) {
    $id = (int)$_POST['id_borrador'];
    $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id=? AND id_autor=? AND estado='borrador'");
    $stmt->bind_param("ii", $id, $_SESSION['id']);
    $stmt->execute() ? $msg_ok = '✅ Borrador eliminado.' : $msg_err = '❌ Error al eliminar.';
}

// ── Actualizar borrador ───────────────────────────────────────────────────────
if (isset($_POST['actualizar_borrador'])) {
    $id        = (int)$_POST['id_borrador'];
    $titulo    = trim($_POST['titulo']    ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $categoria = $_POST['categoria']      ?? 'peces';
    $imagen    = trim($_POST['imagen']    ?? '');
    if (empty($titulo)) {
        $msg_err = 'El título es obligatorio.';
    } else {
        $stmt = $conn->prepare("UPDATE publicaciones SET titulo=?,contenido=?,categoria=?,imagen=? WHERE id=? AND id_autor=? AND estado='borrador'");
        $stmt->bind_param("ssssii", $titulo, $contenido, $categoria, $imagen, $id, $_SESSION['id']);
        $stmt->execute() ? $msg_ok = '✅ Borrador actualizado.' : $msg_err = '❌ Error al actualizar.';
    }
}

// ── Cargar borradores ─────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT id, titulo, contenido, categoria, imagen, fecha_creacion
    FROM publicaciones
    WHERE id_autor = ? AND estado = 'borrador'
    ORDER BY fecha_creacion DESC
");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$borradores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Borrador a editar
$editando = null;
if (isset($_GET['editar_borrador'])) {
    $eid = (int)$_GET['editar_borrador'];
    $est = $conn->prepare("SELECT * FROM publicaciones WHERE id=? AND id_autor=? AND estado='borrador'");
    $est->bind_param("ii", $eid, $_SESSION['id']);
    $est->execute();
    $editando = $est->get_result()->fetch_assoc();
}

$cats = ['peces', 'mamiferos', 'moluscos', 'crustaceos', 'conservacion'];
?>

<h1 class="section-title">Mis Borradores</h1>

<?php if ($msg_ok): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg_ok); ?></div><?php endif; ?>
<?php if ($msg_err): ?><div class="alert alert-error"><?php echo htmlspecialchars($msg_err); ?></div><?php endif; ?>

<div class="dashboard-grid">

    <!-- FORMULARIO BORRADOR -->
    <div class="dashboard-card">
        <h2><?php echo $editando ? '✏️ Editar borrador' : '📝 Nuevo borrador'; ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id_borrador" value="<?php echo $editando['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Título</label>
                <input type="text" name="titulo" required
                       value="<?php echo htmlspecialchars($editando['titulo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria">
                    <?php foreach ($cats as $cat): ?>
                        <option value="<?php echo $cat; ?>"
                            <?php echo ($editando['categoria'] ?? '') === $cat ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Contenido</label>
                <textarea name="contenido" rows="6"><?php
                    echo htmlspecialchars($editando['contenido'] ?? '');
                ?></textarea>
            </div>
            <div class="form-group">
                <label>URL de imagen (opcional)</label>
                <input type="url" name="imagen"
                       value="<?php echo htmlspecialchars($editando['imagen'] ?? ''); ?>">
            </div>

            <?php if ($editando): ?>
                <button type="submit" name="actualizar_borrador" class="btn">💾 Actualizar borrador</button>
                <button type="submit" name="publicar_borrador" class="btn" style="background:#27ae60;margin-top:.5rem;">🚀 Publicar ahora</button>
                <button type="button" onclick="cargar('mis_borradores')" class="btn" style="background:#95a5a6;margin-top:.5rem;">Cancelar</button>
            <?php else: ?>
                <button type="submit" name="guardar_borrador" class="btn">💾 Guardar borrador</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- LISTA DE BORRADORES -->
    <div class="dashboard-card">
        <h2>📂 Borradores guardados (<?php echo count($borradores); ?>)</h2>

        <?php if (empty($borradores)): ?>
            <p style="color:#7f8c8d;padding:1rem 0;">No tienes borradores guardados.</p>
        <?php endif; ?>

        <?php foreach ($borradores as $b): ?>
            <div class="post-item">
                <h3><?php echo htmlspecialchars($b['titulo']); ?></h3>
                <div class="post-meta" style="margin:.3rem 0;">
                    <span>🏷️ <?php echo htmlspecialchars($b['categoria']); ?></span>
                    <span>📅 <?php echo date('d/m/Y', strtotime($b['fecha_creacion'])); ?></span>
                    <span style="background:rgba(255,180,0,0.15);color:#f39c12;padding:2px 8px;border-radius:50px;font-size:.75rem;">Borrador</span>
                </div>
                <div class="post-actions">
                    <a href="mis_borradores.php?editar_borrador=<?php echo $b['id']; ?>" class="btn-small">Editar</a>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Publicar este borrador?')">
                        <input type="hidden" name="id_borrador" value="<?php echo $b['id']; ?>">
                        <button type="submit" name="publicar_borrador" class="btn-small" style="background:#27ae60;color:#fff;">Publicar</button>
                    </form>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este borrador?')">
                        <input type="hidden" name="id_borrador" value="<?php echo $b['id']; ?>">
                        <button type="submit" name="eliminar_borrador" class="btn-small danger">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>