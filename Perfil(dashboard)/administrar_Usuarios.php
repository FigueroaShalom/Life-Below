<?php
require_once __DIR__ . '/../database/Conexion_base.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 🔒 Solo admin
if (!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador") {
    echo "<div class='alert alert-danger'>Acceso no autorizado</div>";
    exit();
}

// ── Listar usuarios ───────────────────────────────────────────────────────────
$idActual = $_SESSION['id'];
$stmt = $conn->prepare("SELECT id, user, email, rol, fecha_de_registro FROM usuarios WHERE id != ? ORDER BY fecha_de_registro DESC");
$stmt->bind_param("i", $idActual);
$stmt->execute();
$usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<h3 class="mb-4">Administrar Usuarios</h3>

<div id="msg-admin"></div>

<table class="table table-bordered table-hover bg-white">
<thead class="table-dark">
<tr>
    <th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Registro</th><th>Acciones</th>
</tr>
</thead>
<tbody id="tabla-usuarios">
<?php foreach ($usuarios as $row): ?>
<tr id="fila-<?php echo $row['id']; ?>">
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['user']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td>
        <?php
        $rolColor = match($row['rol']) {
            'administrador' => 'background:rgba(255,193,7,.15);color:#856404',
            'editor'        => 'background:rgba(13,110,253,.15);color:#084298',
            'autor'         => 'background:rgba(25,135,84,.15);color:#0a3622',
            default         => 'background:rgba(108,117,125,.15);color:#495057'
        };
        ?>
        <span style="<?php echo $rolColor; ?>;padding:3px 12px;border-radius:50px;font-size:.8rem;font-weight:700;">
            <?php echo ucfirst(htmlspecialchars($row['rol'] ?: 'sin rol')); ?>
        </span>
    </td>
    <td><?php echo htmlspecialchars($row['fecha_de_registro']); ?></td>
    <td>
        <button class="btn btn-warning btn-sm"
                onclick="abrirModalEditar(
                    <?php echo $row['id']; ?>,
                    '<?php echo htmlspecialchars(addslashes($row['user'])); ?>',
                    '<?php echo htmlspecialchars(addslashes($row['email'])); ?>',
                    '<?php echo htmlspecialchars($row['rol']); ?>'
                )">
            Editar
        </button>
        <button class="btn btn-danger btn-sm"
                onclick="eliminarUsuario(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['user'])); ?>')">
            Eliminar
        </button>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($usuarios)): ?>
<tr><td colspan="6" class="text-center text-muted">No hay otros usuarios.</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- ══ MODAL EDITAR ══ -->
<div id="modal-editar-usuario" style="display:none;position:fixed;inset:0;z-index:9999;
     background:rgba(0,0,0,.55);align-items:center;justify-content:center;">
<div style="background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:460px;
            box-shadow:0 20px 60px rgba(0,0,0,.3);position:relative;margin:1rem;">

    <button onclick="cerrarModal()"
            style="position:absolute;top:1rem;right:1rem;background:none;border:none;
                   font-size:1.4rem;cursor:pointer;color:#666;">&#x2715;</button>

    <h4 style="margin-bottom:1.5rem;color:#03045e;font-weight:800;">Editar Usuario</h4>

    <!-- ✅ SIN action ni method — lo manejamos con fetch -->
    <form id="form-editar-usuario">
        <input type="hidden" name="accion"     value="editar_usuario">
        <input type="hidden" name="id_usuario" id="edit-id">

        <div class="mb-3">
            <label class="form-label fw-bold">Nombre de usuario</label>
            <input type="text" name="user" id="edit-user" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Correo electrónico</label>
            <input type="email" name="email" id="edit-email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Rol</label>
            <select name="rol" id="edit-rol" class="form-select" required>
                <option value="">Seleccionar</option>
                <option value="administrador">Administrador</option>
                <option value="editor">Editor</option>
                <option value="autor">Autor</option>
                <option value="usuario">Usuario</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">
                Nueva contraseña
                <small class="text-muted fw-normal">(vacío = no cambiar)</small>
            </label>
            <input type="password" name="nueva_password" class="form-control" placeholder="••••••••">
        </div>
        <div style="display:flex;gap:.8rem;">
            <button type="submit" id="btn-guardar" class="btn btn-primary" style="background:#0077b6;border:none;flex:1;">
                Guardar Cambios
            </button>
            <button type="button" onclick="cerrarModal()" class="btn btn-secondary" style="flex:1;">
                Cancelar
            </button>
        </div>
    </form>
</div>
</div>

<script>
// ── Abrir modal ───────────────────────────────────────────────────────────────
function abrirModalEditar(id, user, email, rol) {
    document.getElementById('edit-id').value    = id;
    document.getElementById('edit-user').value  = user;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-rol').value   = rol;
    document.querySelector('[name="nueva_password"]').value = '';
    const m = document.getElementById('modal-editar-usuario');
    m.style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modal-editar-usuario').style.display = 'none';
}

document.getElementById('modal-editar-usuario').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

// ── Enviar edición con fetch (sin recargar la página) ─────────────────────────
document.getElementById('form-editar-usuario').addEventListener('submit', function(e) {
    e.preventDefault(); // ← evita la navegación tradicional

    const btn = document.getElementById('btn-guardar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const formData = new FormData(this);

    // Ruta al procesador PHP — ajusta si tu carpeta se llama diferente
    fetch('../Perfil(dashboard)/administrar_Usuarios.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        // Recargar el fragmento completo con los datos actualizados
        document.getElementById('contenido').innerHTML = html;

        // Re-ejecutar scripts del fragmento
        document.getElementById('contenido').querySelectorAll('script').forEach(old => {
            const s = document.createElement('script');
            s.text = old.text;
            document.body.appendChild(s).parentNode.removeChild(s);
        });

        mostrarMensaje('✅ Usuario actualizado correctamente.', 'success');
    })
    .catch(() => {
        mostrarMensaje('❌ Error de conexión al guardar.', 'danger');
        btn.disabled = false;
        btn.textContent = 'Guardar Cambios';
    });
});

// ── Eliminar usuario con fetch ────────────────────────────────────────────────
function eliminarUsuario(id, nombre) {
    if (!confirm('¿Seguro que deseas eliminar a ' + nombre + '?')) return;

    const formData = new FormData();
    formData.append('id', id);

    fetch('../Perfil(dashboard)/eliminar_usuario.php?id=' + id)
    .then(res => res.text())
    .then(() => {
        // Quitar la fila de la tabla sin recargar
        const fila = document.getElementById('fila-' + id);
        if (fila) fila.remove();
        mostrarMensaje('✅ Usuario eliminado.', 'success');
    })
    .catch(() => mostrarMensaje('❌ Error al eliminar.', 'danger'));
}

// ── Mostrar mensaje en el dashboard ──────────────────────────────────────────
function mostrarMensaje(texto, tipo) {
    const div = document.getElementById('msg-admin');
    div.innerHTML = '<div class="alert alert-' + tipo + '">' + texto + '</div>';
    setTimeout(() => div.innerHTML = '', 4000);
}
</script>