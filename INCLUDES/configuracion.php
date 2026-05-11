<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../database/Conexion_base.php';

$id_user = $_SESSION['id'];
$stmt = $conn->prepare("SELECT user, email, foto, estado_foto FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<div class="config-container">
    <div class="d-flex align-items-center mb-4">
        <h3 class="fw-bold mb-0">⚙️ Configuración de Cuenta</h3>
    </div>

    <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">Editar Perfil</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">Seguridad</button>
        </li>
    </ul>

    <div class="tab-content" id="configTabsContent">
        <!-- TAB PERFIL -->
        <div class="tab-pane fade show active" id="profile">
            <div class="row">
                <div class="col-md-4 text-center border-end">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?php echo htmlspecialchars($user['foto'] ?: 'https://cdn-icons-png.flaticon.com/512/149/149071.png'); ?>" 
                             id="preview-foto" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--ocean);">
                        
                        <?php if($user['estado_foto'] == 'pendiente'): ?>
                            <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle">Pendiente...</span>
                        <?php endif; ?>
                    </div>
                    <form id="form-foto" enctype="multipart/form-data">
                        <input type="file" name="foto_perfil" id="input-foto" class="d-none" accept="image/*">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-2" onclick="document.getElementById('input-foto').click()">Seleccionar Foto</button>
                        <p class="text-muted small">Tu nueva foto deberá ser aprobada por un administrador.</p>
                        <button type="submit" class="btn btn-primary w-100" id="btn-save-foto" disabled>Subir para Revisión</button>
                    </form>
                </div>
                <div class="col-md-8 ps-md-4">
                    <form id="form-info">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de usuario</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['user']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <span class="text-muted small">El correo no puede ser cambiado por seguridad.</span>
                        </div>
                        <div class="alert alert-info py-2">
                            <i class="bi bi-info-circle me-2"></i> Para cambiar tu nombre o correo, contacta con soporte técnico.
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TAB SEGURIDAD -->
        <div class="tab-pane fade" id="security">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div id="step-1">
                        <h5>Cambiar Contraseña</h5>
                        <p class="text-muted">Para cambiar tu contraseña, primero debemos enviarte un código de verificación a tu correo registrado.</p>
                        <button class="btn btn-ocean w-100 py-2 fw-bold" onclick="solicitarCodigo()">Enviar código de verificación</button>
                    </div>

                    <div id="step-2" class="d-none">
                        <div class="alert alert-success">
                            Código enviado a <strong><?php echo $user['email']; ?></strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Código de verificación</label>
                            <input type="text" id="verify-code" class="form-control text-center fs-4 fw-bold" placeholder="000000" maxlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva Contraseña</label>
                            <input type="password" id="new-pass" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                            <input type="password" id="confirm-pass" class="form-control">
                        </div>
                        <button class="btn btn-success w-100 py-2 fw-bold" onclick="cambiarPassword()">Actualizar Contraseña</button>
                        <button class="btn btn-link w-100 mt-2 text-muted" onclick="volverStep1()">No recibí el código</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Manejo de Preview de Foto
document.getElementById('input-foto').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-foto').src = e.target.result;
            document.getElementById('btn-save-foto').disabled = false;
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Subir Foto
document.getElementById('form-foto').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('accion', 'subir_foto');

    fetch('database/procesar_configuracion.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Foto subida. Un administrador debe aprobarla.');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
};

// Fix para pestañas (Bootstrap JS a veces no detecta contenido dinámico)
document.querySelectorAll('#configTabs button').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remover activo de todos los botones y paneles
        document.querySelectorAll('#configTabs .nav-link').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => {
            p.classList.remove('show', 'active');
        });
        
        // Activar el actual
        this.classList.add('active');
        const target = document.querySelector(this.getAttribute('data-bs-target'));
        if(target) target.classList.add('show', 'active');
    });
});

// Solicitar Código de Seguridad
function solicitarCodigo() {
    const btn = event.target;
    btn.disabled = true;
    btn.innerText = 'Enviando...';

    fetch('database/procesar_configuracion.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'accion=solicitar_codigo'
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('step-1').classList.add('d-none');
            document.getElementById('step-2').classList.remove('d-none');
        } else {
            alert('Error: ' + data.error);
            btn.disabled = false;
            btn.innerText = 'Enviar código de verificación';
        }
    });
}

function volverStep1() {
    document.getElementById('step-1').classList.remove('d-none');
    document.getElementById('step-2').classList.add('d-none');
}

// Cambiar Password
function cambiarPassword() {
    const code = document.getElementById('verify-code').value;
    const pass = document.getElementById('new-pass').value;
    const confirm = document.getElementById('confirm-pass').value;

    if(pass !== confirm) {
        alert('Las contraseñas no coinciden');
        return;
    }

    fetch('database/procesar_configuracion.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `accion=cambiar_password&codigo=${code}&password=${pass}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Contraseña actualizada correctamente.');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}
</script>

<style>
.nav-tabs .nav-link { color: var(--navy); font-weight: 700; border: none; padding: 12px 25px; }
.nav-tabs .nav-link.active { color: var(--ocean); border-bottom: 3px solid var(--ocean); background: transparent; }
.btn-ocean { background: var(--ocean); color: #fff; }
.btn-ocean:hover { background: var(--navy); color: #fff; }
</style>
