<!DOCTYPE html>
<html>
<head>
<title>Registro</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="login-container">

<div class="login-box">

<h2>Registro</h2>

<form action="database/guardado.php" method="POST">

<div class="form-group">
<label>Usuario</label>
<input type="text" name="user" placeholder="Usuario" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" placeholder="Email" required>
</div>

<div class="form-group">
<label>Contraseña</label>
<input type="password" name="password" placeholder="Contraseña" required>
</div>

<button class="btn" type="submit">Registrarse</button>

</form>

</div>

</div>

</body>
</html>