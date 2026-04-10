<?php
session_start();

// SOLO ADMIN
if(!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador"){
    echo "<div class='alert alert-danger'>Acceso no autorizado</div>";
    exit();
}
?>

<div class="card shadow">
<div class="card-body">

<h4 class="mb-4">Crear Usuario</h4>

<form method="POST" action="confirmarUsuario.php">

<div class="row">

<div class="col-md-6 mb-3">
<label>Nombre de usuario</label>
<input type="text" name="user" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Correo</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Contraseña</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Rol</label>
<select name="rol" class="form-control" required>
<option value="">Seleccionar</option>
<option value="administrador">Administrador</option>
<option value="editor">Editor</option>
<option value="autor">Autor</option>
<option value="autor">Usuario</option>
</select>
</div>

</div>

<div class="d-grid">
<button type="submit" name="crear" class="btn btn-success">
Crear Usuario
</button>
</div>

</form>

</div>
</div>