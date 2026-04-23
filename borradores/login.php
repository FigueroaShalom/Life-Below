<?php
include("conexion.php");
session_start();

if(isset($_SESSION['user'])){
    header("Location: perfil.php");
    exit();
}

if(isset($_POST['login'])){

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Buscar SOLO por usuario
    $sql = "SELECT * FROM usuarios WHERE user='$usuario'";
    $resultado = $conexion->query($sql);

    if($resultado->num_rows > 0){

        $datos = $resultado->fetch_assoc();

        // 🔐 Verificar contraseña encriptada
        if(password_verify($password, $datos['password'])){

            $_SESSION['id'] = $datos['id'];
            $_SESSION['user'] = $datos['user'];
            $_SESSION['email'] = $datos['email'];
            $_SESSION['rol'] = $datos['rol'];

            header("Location: perfil.php");
            exit();

        } else {
            $error = "Contraseña incorrecta";
        }

    } else {
        $error = "Usuario no existe";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Iniciar Sesión</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f2f2f2;
height:100vh;
display:flex;
align-items:center;
justify-content:center;
}

.login-box{
width:400px;
}

</style>

</head>

<body>

<div class="card login-box shadow">

<div class="card-body">

<h3 class="text-center mb-4">Iniciar Sesión</h3>

<?php if(isset($error)){ ?>
<div class="alert alert-danger">
<?php echo $error; ?>
</div>
<?php } ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Nombre de Usuario</label>
<input type="text" name="usuario" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Contraseña</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="d-grid">
<button type="submit" name="login" class="btn btn-primary">
Entrar
</button>
</div>

</form>

</div>
</div>

</body>
</html>
