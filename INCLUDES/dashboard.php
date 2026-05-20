<?php
// INCLUDES/dashboard.php
if (session_status() === PHP_SESSION_NONE) session_start();

if(empty($_SESSION['logged_in'])){
    echo "<script>window.location.href='index.php?section=login';</script>";
    exit();
}
$id_user = $_SESSION['user_id'];

require_once __DIR__ . '/../database/Conexion_base.php';
$id_user = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT user, email, rol, foto FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

$user  = $user_data['user'];
$email = $user_data['email'];
$rol   = $user_data['rol'];
$foto  = $user_data['foto'] ?: 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

// ✅ Sincronizar de forma segura la sesión con el estado real de la base de datos
$_SESSION['rol']   = $rol;
$_SESSION['user']  = $user;
$_SESSION['email'] = $email;
?>

<style>
/* Ajustes específicos dashboard HYDRON */
.hy-dashboard {
    display: flex;
    min-height: calc(100vh - 72px); /* Ajustado por el header */
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
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    margin: 0 auto .6rem;
    object-fit: cover;
    border: 2px solid var(--ocean);
}

.hy-user-name {
    font-weight: 800;
    color: #fff;
    text-decoration: none;
    display: block;
    margin-top: 5px;
}

.hy-user-email {
    font-size: .8rem;
    color: rgba(255,255,255,0.6);
    word-break: break-all;
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
    display: block;
    text-align: center;
    text-decoration: none;
}

.hy-menu button {
    border: none;
    border-radius: 50px;
    padding: 10px;
    font-weight: 700;
    background: rgba(255,255,255,0.08);
    color: #fff;
    transition: all .2s;
    cursor: pointer;
}

.hy-menu button:hover, .hy-menu button.active {
    background: var(--ocean);
}

.hy-menu .admin {
    background: rgba(255,180,0,0.15);
    color: #ffc107;
}

.hy-menu .logout {
    background: rgba(220,50,50,0.15);
    color: #ff6b6b;
    border-radius: 50px;
    padding: 10px;
    font-weight: 700;
}

.hy-content {
    flex: 1;
    padding: 2rem;
    background: var(--bg) !important;
    color: var(--text-color) !important;
}

.hy-content-box {
    background: var(--card-bg) !important;
    color: var(--text-color) !important;
    border-radius: var(--radius);
    padding: 2.5rem;
    box-shadow: var(--shadow);
    border: 1.5px solid var(--border) !important;
}

</style>

<div class="hy-dashboard">

<!-- SIDEBAR -->
<div class="hy-sidebar">

<div class="hy-user-box">
<img src="<?php echo htmlspecialchars($foto); ?>" alt="Perfil">

<div class="hy-user-name"><?php echo htmlspecialchars($user); ?></div>
<div class="hy-user-email"><?php echo htmlspecialchars($email); ?></div>
<div class="hy-user-role"><?php echo ucfirst($rol); ?></div>
</div>

<div class="hy-menu">
<button onclick="cargar('configuracion')">Configuración</button>

<?php if($rol == "administrador"){ ?>
<button onclick="cargar('crear_contenido')">Crear Contenido</button>
<button onclick="cargar('mis_Publicaciones')">Mis Publicaciones</button>
<button onclick="cargar('mis_borradores')">Mis Borradores</button>
<button class="admin" onclick="cargar('aprobar_fotos')">Aprobar Fotos</button>
<button class="admin" onclick="cargar('Perfil(dashboard)/administrar_Usuarios')">Administrar usuarios</button>
<button class="admin" onclick="cargar('Perfil(dashboard)/crear_Usuarios')">Crear Usuario</button>

<?php } elseif($rol == "editor"){ ?>
<button onclick="cargar('crear_contenido')">Crear Contenido</button>
<button onclick="cargar('mis_Publicaciones')">Mis Publicaciones</button>
<button onclick="cargar('mis_borradores')">Mis Borradores</button>
<button onclick="cargar('publicaciones_Revision')">En revisión</button>

<?php } elseif($rol == "autor"){ ?>
<button onclick="cargar('crear_contenido')">Crear Contenido</button>
<button onclick="cargar('mis_Publicaciones')">Mis Publicaciones</button>
<button onclick="cargar('mis_borradores')">Mis Borradores</button>

<?php } else { ?>
<p style="color:white;text-align:center;">Sin permisos</p>
<?php } ?>

<a href="index.php?logout=1" class="logout btn">Cerrar sesión</a>

</div>
</div>

<!-- CONTENIDO -->
<div class="hy-content">
<div class="hy-content-box" id="contenido">
    <!-- Se carga configuración por defecto -->
    <script>document.addEventListener('DOMContentLoaded', () => cargar('configuracion'));</script>
</div>
</div>

</div>

<script>
// Módulos en includes/ — con soporte para ?params
const rutasIncludes = {
    'configuracion':     './INCLUDES/configuracion.php',
    'crear_contenido':   './INCLUDES/crear_contenido.php',
    'mis_publicaciones': './INCLUDES/mis_Publicaciones.php',
    'mis_borradores':    './INCLUDES/mis_borradores.php',
    'editar_contenido':  './INCLUDES/editar_contenido.php',
    'aprobar_fotos':     './INCLUDES/aprobar_fotos.php',
};

function cargar(modulo) {
    const [nombre, params] = modulo.split('?');
    const nombreLower = nombre.toLowerCase();

    // Si tiene / asumimos ruta completa (como Perfil(dashboard)/administrar_Usuarios)
    let base = rutasIncludes[nombreLower];
    if(!base) {
        if(nombre.includes('/')) {
            base = './' + nombre + '.php';
        } else {
            base = './INCLUDES/' + nombre + '.php';
        }
    }
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
</script>

