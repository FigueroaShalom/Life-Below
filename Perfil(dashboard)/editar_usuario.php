<?php
include("conexion.php");
session_start();

// 🔒 Solo admin
if(!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador"){
    echo "Acceso no autorizado";
    exit();
}

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conexion->prepare("SELECT user, email, rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows == 0){
    header("Location: dashboard.php");
    exit();
}

$usuario = $resultado->fetch_assoc();
?>

<style>
body{
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    margin:0;
}

/* CONTENEDOR */
.container{
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

/* CARD */
.card{
    background:#fff;
    padding:2rem;
    border-radius:15px;
    width:400px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    animation:fadeIn .5s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

h3{
    text-align:center;
    margin-bottom:1.5rem;
}

/* INPUTS */
.form-group{
    margin-bottom:1rem;
}

label{
    display:block;
    margin-bottom:.3rem;
    font-weight:600;
}

input, select{
    width:100%;
    padding:.6rem;
    border-radius:8px;
    border:1px solid #ccc;
    outline:none;
    transition:.3s;
}

input:focus, select:focus{
    border-color:#667eea;
    box-shadow:0 0 5px rgba(102,126,234,0.5);
}

/* BOTONES */
.btn{
    padding:.6rem 1rem;
    border:none;
    border-radius:8px;
    cursor:pointer;
    transition:.3s;
}

.btn-primary{
    background:#667eea;
    color:white;
}

.btn-primary:hover{
    background:#5a67d8;
}

.btn-secondary{
    background:#ccc;
    color:#333;
    text-decoration:none;
    display:inline-block;
    text-align:center;
}

.btn-secondary:hover{
    background:#bbb;
}

/* ACCIONES */
.actions{
    display:flex;
    justify-content:space-between;
    margin-top:1rem;
}
</style>

<div class="container">

<div class="card">

<h3>Editar Usuario</h3>

<form action="actualizar_usuario.php" method="POST">

<input type="hidden" name="id" value="<?php echo $id; ?>">

<div class="form-group">
    <label>Usuario</label>
    <input type="text" name="user" value="<?php echo $usuario['user']; ?>" required>
</div>

<div class="form-group">
    <label>Email</label>
    <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
</div>

<div class="form-group">
    <label>Rol</label>
    <select name="rol">
        <option value="usuario" <?php if($usuario['rol']=="usuario") echo "selected"; ?>>Usuario</option>
        <option value="autor" <?php if($usuario['rol']=="autor") echo "selected"; ?>>autor</option>
        <option value="editor" <?php if($usuario['rol']=="editor") echo "selected"; ?>>editor</option>
        <option value="administrador" <?php if($usuario['rol']=="administrador") echo "selected"; ?>>Administrador</option>
    </select>
</div>

<div class="actions">
    <button type="submit" name="actualizar" class="btn btn-primary">
        Guardar
    </button>

    <a href="perfil.php?modulo=administrar_usuarios" class="btn btn-secondary">
        Cancelar
    </a>
</div>

</form>

</div>
</div>