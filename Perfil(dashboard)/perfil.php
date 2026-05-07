<?php
session_start();

if(!isset($_SESSION['id'])){
    header("Location: ../index.php");
    exit();
}

/* anti cache */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$user = $_SESSION['user'];
$email = $_SESSION['email'];
$rol = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- TU CSS HYDRON -->
<link rel="stylesheet" href="../style.css">

<style>
/* Ajustes específicos dashboard HYDRON */
.hy-dashboard {
    display: flex;
    min-height: 100vh;
}

.hy-sidebar {
    width: 260px;
    background: var(--navy);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.hy-user-box {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: var(--radius);
    padding: 1.2rem;
    text-align: center;
}

.hy-user-box img {
    width: 70px;
    border-radius: 50%;
    margin-bottom: .6rem;
}

.hy-user-name {
    font-weight: 800;
    color: #fff;
}

.hy-user-email {
    font-size: .8rem;
    color: rgba(255,255,255,0.6);
}

.hy-user-role {
    margin-top: .4rem;
    font-size: .75rem;
    background: rgba(0,200,240,0.15);
    color: var(--cyan);
    padding: 3px 10px;
    border-radius: 50px;
    display: inline-block;
}

.hy-menu {
    margin-top: 1rem;
}

.hy-menu button,
.hy-menu a {
    width: 100%;
    margin-bottom: .5rem;
}

.hy-menu button {
    border: none;
    border-radius: 50px;
    padding: 10px;
    font-weight: 700;
    background: rgba(255,255,255,0.08);
    color: #fff;
    transition: all .2s;
}

.hy-menu button:hover {
    background: var(--ocean);
}

.hy-menu .admin {
    background: rgba(255,180,0,0.15);
    color: #ffc107;
}

.hy-menu .logout {
    background: rgba(220,50,50,0.15);
    color: #ff6b6b;
}

.hy-content {
    flex: 1;
    padding: 2rem;
    background: var(--off-white);
}

.hy-content-box {
    background: #fff;
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    border: 1.5px solid var(--border);
}
</style>
</head>

<body>

<div class="hy-dashboard">

<!-- SIDEBAR -->
<div class="hy-sidebar">

<div class="hy-user-box">
<img src="https://cdn-icons-png.flaticon.com/512/149/149071.png">

<div class="hy-user-name"><?php echo $user; ?></div>
<div class="hy-user-email"><?php echo $email; ?></div>
<div class="hy-user-role"><?php echo ucfirst($rol); ?></div>
</div>

<div class="hy-menu">

<?php if($rol == "administrador"){ ?>
<button onclick="cargar('crear_contenido')">Crear Contenido</button>
<button onclick="cargar('mis_Publicaciones')">Mis Publicaciones</button>
<button onclick="cargar('mis_borradores')">Mis Borradores</button>
<button onclick="cargar('mis_publicacionePendientes')">Pendientes</button>
<button onclick="cargar('publicaciones_Revision')">En revisión</button>
<button class="admin" onclick="cargar('administrar_Usuarios')">Administrar usuarios</button>
<button class="admin" onclick="cargar('crear_Usuarios')">Crear Usuario</button>

<?php } elseif($rol == "editor"){ ?>
<button onclick="cargar('crear_Contenido')">Crear Contenido</button>
<button onclick="cargar('mis_Publicaciones')">Mis Publicaciones</button>
<button onclick="cargar('mis_borradores')">Mis Borradores</button>
<button onclick="cargar('mis_publicacionePendientes')">Pendientes</button>
<button onclick="cargar('publicaciones_Revision')">En revisión</button>

<?php } elseif($rol == "autor"){ ?>
<button onclick="cargar('crear_Contenido')">Crear Contenido</button>
<button onclick="cargar('mis_Publicaciones')">Mis Publicaciones</button>
<button onclick="cargar('mis_borradores')">Mis Borradores</button>
<button onclick="cargar('mis_publicacionePendientes')">Pendientes</button>

<?php } else { ?>
<p style="color:white;text-align:center;">Sin permisos</p>
<?php } ?>

<a href="logout.php" class="logout btn">Cerrar sesión</a>

</div>
</div>

<!-- CONTENIDO -->
<div class="hy-content">
<div class="hy-content-box" id="contenido">
<h4>Bienvenido <?php echo $user; ?> 👋</h4>
<p>Selecciona una opción del menú</p>
</div>
</div>

</div>

<script>
// Módulos en ../includes/ — con soporte para ?params
const rutasIncludes = {
    'crear_contenido':   '../includes/crear_contenido.php',
    'mis_publicaciones': '../includes/mis_Publicaciones.php',
    'mis_borradores':    '../includes/mis_borradores.php',
    'editar_contenido':  '../includes/editar_contenido.php',
};

function cargar(modulo) {
    // Separar nombre del módulo de sus parámetros: 'editar_contenido?id=5'
    const [nombre, params] = modulo.split('?');
    const nombreLower = nombre.toLowerCase();

    const base = rutasIncludes[nombreLower] ?? (nombre + '.php');
    const ruta = params ? base + '?' + params : base;

    fetch(ruta)
    .then(res => res.text())
    .then(data => {
        const contenedor = document.getElementById("contenido");
        contenedor.innerHTML = data;

        // Ejecutar scripts del fragmento cargado
        contenedor.querySelectorAll("script").forEach(oldScript => {
            const newScript = document.createElement("script");
            newScript.text = oldScript.text;
            document.body.appendChild(newScript).parentNode.removeChild(newScript);
        });
    })
    .catch(err => console.error('Error al cargar módulo:', err));
}


window.onpageshow = function(e){
    if(e.persisted) {
        console.log('Página cargada desde cache, no recargamos');
        // location.reload(); // <-- comenta esto mientras depuras
    }
};
</script>

</body>
</html>