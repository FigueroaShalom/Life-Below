<?php
session_start();

// SOLO ADMIN
if(!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador"){
    echo "<div class='alert alert-danger'>Acceso no autorizado</div>";
    exit();
}
?>

<div class="card shadow border-0">
    <div class="card-body p-4">
        <h4 class="fw-bold mb-4" style="color: var(--text-color);">Crear Nuevo Usuario</h4>

        <form method="POST" action="Perfil(dashboard)/confirmarUsuario.php" id="form-crear-usuario">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nombre de usuario</label>
                    <input type="text" name="user" id="admin-create-user" class="form-control" placeholder="Ej. juan_perez" required>
                    <small id="admin-create-user-msg" class="d-block mt-1 fw-bold"></small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="email" id="admin-create-email" class="form-control" placeholder="Ej. juan@correo.com" required>
                    <small id="admin-create-email-msg" class="d-block mt-1 fw-bold"></small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Contraseña</label>
                    <input type="password" name="password" id="admin-create-password" class="form-control" placeholder="Mínimo 8 caracteres, 1 mayúscula y 1 número/símbolo" required>
                    <small id="admin-create-pass-msg" class="d-block mt-1 fw-bold"></small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="">Seleccionar Rol</option>
                        <option value="administrador">Administrador</option>
                        <option value="editor">Editor</option>
                        <option value="autor">Autor</option>
                        <option value="usuario">Usuario</option>
                    </select>
                </div>
            </div>

            <div class="d-grid mt-3">
                <button type="submit" name="crear" id="btn-crear-usuario" class="btn btn-success py-2 fw-bold text-uppercase" style="letter-spacing: 1px;">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    let userOk = false;
    let emailOk = false;
    let passOk = false;

    const btnSubmit = document.getElementById("btn-crear-usuario");

    function updateSubmitButton() {
        btnSubmit.disabled = !(userOk && emailOk && passOk);
    }

    // Inicializar deshabilitado hasta que las validaciones pasen
    updateSubmitButton();

    function validarPassword(pass) {
        let errores = [];
        if (pass.length < 8) errores.push("al menos 8 caracteres");
        if (!/[A-Z]/.test(pass)) errores.push("una mayúscula");
        if (!/[0-9]/.test(pass) && !/[^a-zA-Z0-9]/.test(pass)) errores.push("un número o símbolo especial");
        return errores.length === 0 ? true : "Falta: " + errores.join(", ");
    }

    document.getElementById("admin-create-user").addEventListener("input", function() {
        const msg = document.getElementById("admin-create-user-msg");
        const val = this.value.trim();
        if (!val) { msg.textContent=''; userOk = false; updateSubmitButton(); return; }
        
        fetch("database/validar_registro.php", {
            method: "POST", 
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "action=validar_user&user=" + encodeURIComponent(val)
        })
        .then(r => r.text())
        .then(d => {
            const res = d.trim();
            if (res === "existe") {
                msg.style.color = "#ff6b6b";
                msg.textContent = "❌ Usuario ya existe";
                userOk = false;
            } else {
                msg.style.color = "#00e676";
                msg.textContent = "✅ Disponible";
                userOk = true;
            }
            updateSubmitButton();
        });
    });

    document.getElementById("admin-create-email").addEventListener("input", function() {
        const msg = document.getElementById("admin-create-email-msg");
        const val = this.value.trim();
        if (!val) { msg.textContent=''; emailOk = false; updateSubmitButton(); return; }
        
        fetch("database/validar_registro.php", {
            method: "POST", 
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "action=validar_email&email=" + encodeURIComponent(val)
        })
        .then(r => r.text())
        .then(d => {
            const res = d.trim();
            if (res === "existe") {
                msg.style.color = "#ff6b6b";
                msg.textContent = "❌ Email ya registrado";
                emailOk = false;
            } else {
                msg.style.color = "#00e676";
                msg.textContent = "✅ Disponible";
                emailOk = true;
            }
            updateSubmitButton();
        });
    });

    document.getElementById("admin-create-password").addEventListener("input", function() {
        const msg = document.getElementById("admin-create-pass-msg");
        const pass = this.value.trim();
        if (!pass) { msg.textContent=''; passOk = false; updateSubmitButton(); return; }
        
        const check = validarPassword(pass);
        if (check === true) {
            msg.style.color = "#00e676";
            msg.textContent = "✅ Contraseña segura";
            passOk = true;
        } else {
            msg.style.color = "#ffaa00";
            msg.textContent = "⚠️ " + check;
            passOk = false;
        }
        updateSubmitButton();
    });
})();
</script>