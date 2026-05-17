<?php
require_once __DIR__ . '/../database/Conexion_base.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 🔒 Solo admin
if (!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador") {
    echo "<div class='alert alert-danger'>Acceso no autorizado</div>";
    exit();
}

// ── Procesar Aprobación/Rechazo de Solicitud de Rol ───────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    if ($accion === 'aceptar_solicitud' || $accion === 'rechazar_solicitud') {
        header('Content-Type: application/json');
        $solicitud_id = (int)$_POST['solicitud_id'];
        
        // Obtener detalles de la solicitud
        $stmt_detalles = $conn->prepare("SELECT usuario_id, rol_solicitado FROM solicitudes_rol WHERE id = ?");
        $stmt_detalles->bind_param("i", $solicitud_id);
        $stmt_detalles->execute();
        $solicitud_data = $stmt_detalles->get_result()->fetch_assoc();
        $stmt_detalles->close();
        
        if ($solicitud_data) {
            $usuario_id = $solicitud_data['usuario_id'];
            $rol_solicitado = $solicitud_data['rol_solicitado'];
            
            if ($accion === 'aceptar_solicitud') {
                // 1. Actualizar rol en usuarios
                $stmt_up_rol = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
                $stmt_up_rol->bind_param("si", $rol_solicitado, $usuario_id);
                $stmt_up_rol->execute();
                $stmt_up_rol->close();
                
                // 2. Marcar solicitud como aceptada
                $stmt_up_sol = $conn->prepare("UPDATE solicitudes_rol SET estado = 'aceptada' WHERE id = ?");
                $stmt_up_sol->bind_param("i", $solicitud_id);
                $stmt_up_sol->execute();
                $stmt_up_sol->close();
                
                echo json_encode(['success' => true, 'mensaje' => 'Solicitud aprobada con éxito. El usuario ahora es ' . ucfirst($rol_solicitado) . '.']);
            } else {
                // Marcar solicitud como rechazada
                $stmt_up_sol = $conn->prepare("UPDATE solicitudes_rol SET estado = 'rechazada' WHERE id = ?");
                $stmt_up_sol->bind_param("i", $solicitud_id);
                $stmt_up_sol->execute();
                $stmt_up_sol->close();
                
                echo json_encode(['success' => true, 'mensaje' => 'Solicitud rechazada con éxito.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Solicitud no encontrada.']);
        }
        exit;
    }
}

// ── Procesar POST (Edición) ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar_usuario') {
    $id_edit = $_POST['id_usuario'];
    $nuevo_user = $_POST['user'];
    $nuevo_email = $_POST['email'];
    $nuevo_rol = $_POST['rol'];
    $nueva_pass = $_POST['nueva_password'];

    if (!empty($nueva_pass)) {
        $hash = password_hash($nueva_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET user = ?, email = ?, rol = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nuevo_user, $nuevo_email, $nuevo_rol, $hash, $id_edit);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET user = ?, email = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nuevo_user, $nuevo_email, $nuevo_rol, $id_edit);
    }
    $stmt->execute();
    $stmt->close();
    // Continuamos para recargar la lista
}

// ── Listar usuarios ───────────────────────────────────────────────────────────
$idActual = $_SESSION['id'];
$stmt = $conn->prepare("SELECT id, user, email, rol, fecha_de_registro FROM usuarios WHERE id != ? ORDER BY fecha_de_registro DESC");
$stmt->bind_param("i", $idActual);
$stmt->execute();
$usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Listar solicitudes de rol pendientes ────────────────────────────────────
$stmt_peticiones = $conn->prepare("
    SELECT s.id, s.rol_solicitado, s.fecha_solicitud, u.user, u.email, u.rol AS rol_actual
    FROM solicitudes_rol s
    JOIN usuarios u ON s.usuario_id = u.id
    WHERE s.estado = 'pendiente'
    ORDER BY s.fecha_solicitud ASC
");
$stmt_peticiones->execute();
$peticiones = $stmt_peticiones->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_peticiones->close();
?>

<h3 class="mb-4">Administrar Usuarios</h3>

<div id="msg-admin"></div>

<!-- ══ PANEL DE SOLICITUDES DE ROL PENDIENTES ══ -->
<?php if (!empty($peticiones)): ?>
<div class="card shadow-sm mb-5 border-0 rounded-4 overflow-hidden" style="border: 1px solid rgba(0,119,190,0.15) !important;">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #0077b6, #0096c7); color: white;">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <span>⏳</span> Solicitudes de Cambio de Rol Pendientes
            <span class="badge bg-warning text-dark rounded-pill ms-2 fs-6 fw-bold"><?php echo count($peticiones); ?></span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="background: #fafbff;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Usuario</th>
                        <th>Email</th>
                        <th>Rol Actual</th>
                        <th>Rol Solicitado</th>
                        <th>Fecha</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($peticiones as $pet): ?>
                    <tr id="solicitud-<?php echo $pet['id']; ?>">
                        <td class="ps-4 fw-bold text-navy"><?php echo htmlspecialchars($pet['user']); ?></td>
                        <td><?php echo htmlspecialchars($pet['email']); ?></td>
                        <td>
                            <span class="badge bg-secondary text-white rounded-pill px-3 py-1 fw-bold"><?php echo ucfirst(htmlspecialchars($pet['rol_actual'])); ?></span>
                        </td>
                        <td>
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold"><?php echo ucfirst(htmlspecialchars($pet['rol_solicitado'])); ?></span>
                        </td>
                        <td><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($pet['fecha_solicitud'])); ?></small></td>
                        <td class="text-end pe-4">
                            <button onclick="responderSolicitud(<?php echo $pet['id']; ?>, 'aceptar_solicitud')" class="btn btn-success btn-sm fw-bold px-3 me-1 rounded-pill shadow-sm">Aceptar</button>
                            <button onclick="responderSolicitud(<?php echo $pet['id']; ?>, 'rechazar_solicitud')" class="btn btn-outline-danger btn-sm fw-bold px-3 rounded-pill">Rechazar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<table class="table table-bordered table-hover bg-white shadow-sm">
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
        <span style="<?php echo $rolColor; ?>;padding:4px 14px;border-radius:50px;font-size:.8rem;font-weight:700;">
            <?php echo ucfirst(htmlspecialchars($row['rol'] ?: 'sin rol')); ?>
        </span>
    </td>
    <td><small><?php echo htmlspecialchars($row['fecha_de_registro']); ?></small></td>
    <td>
        <button onclick="abrirModalEditar(<?php echo $row['id']; ?>, '<?php echo addslashes($row['user']); ?>', '<?php echo addslashes($row['email']); ?>', '<?php echo $row['rol']; ?>')" 
                class="btn btn-warning btn-sm fw-bold">Editar</button>

        <button onclick="eliminarUsuario(<?php echo $row['id']; ?>, '<?php echo addslashes($row['user']); ?>')"
                class="btn btn-danger btn-sm fw-bold">Eliminar</button>
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
    fetch('./Perfil(dashboard)/administrar_Usuarios.php', {
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

    fetch('./Perfil(dashboard)/eliminar_usuario.php?id=' + id)
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

// Responder a Solicitudes de Rol (Aceptar o Rechazar)
function responderSolicitud(solicitudId, accion) {
    const confirmMsg = accion === 'aceptar_solicitud' 
        ? '¿Estás seguro de que deseas APROBAR esta solicitud de ascenso de rol?' 
        : '¿Estás seguro de que deseas RECHAZAR esta solicitud de ascenso de rol?';
        
    if (!confirm(confirmMsg)) return;

    const formData = new FormData();
    formData.append('accion', accion);
    formData.append('solicitud_id', solicitudId);

    fetch('./Perfil(dashboard)/administrar_Usuarios.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Desvanecer y remover la fila de la solicitud
            const fila = document.getElementById('solicitud-' + solicitudId);
            if (fila) {
                fila.style.transition = 'all 0.4s ease';
                fila.style.opacity = '0';
                fila.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    fila.remove();
                    // Recargar la pantalla de administrar usuarios para refrescar también la tabla principal
                    cargar('Perfil(dashboard)/administrar_Usuarios');
                }, 400);
            }
            mostrarMensaje('✅ ' + data.mensaje, 'success');
        } else {
            mostrarMensaje('❌ Error: ' + data.error, 'danger');
        }
    })
    .catch(() => {
        mostrarMensaje('❌ Error de red al procesar la solicitud.', 'danger');
    });
}
</script>