<div class="login-container">
    <div class="login-box">
        <h2> Acceso para exploradores</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">Usuario o contraseña incorrectos</div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Usuario:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn">Ingresar</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: #7f8c8d;">
            Usuario: marino | Contraseña: oceano123
        </p>
    </div>
</div>