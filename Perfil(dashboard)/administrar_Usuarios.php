<?php
include("conexion.php");
session_start();

// 🔒 Solo admin
if(!isset($_SESSION['id']) || $_SESSION['rol'] != "administrador"){
    echo "<div class='alert alert-danger'>Acceso no autorizado</div>";
    exit();
}

$idActual = $_SESSION['id'];

// 🔐 Consulta segura
$stmt = $conexion->prepare("SELECT id, user, email, rol, fecha_de_registro FROM usuarios WHERE id != ?");
$stmt->bind_param("i", $idActual);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<h3 class="mb-4">Administrar Usuarios</h3>

<table class="table table-bordered table-hover bg-white">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Usuario</th>
    <th>Email</th>
    <th>Rol</th>
    <th>Registro</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>

<?php while($row = $resultado->fetch_assoc()){ ?>

<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['user']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo ucfirst($row['rol']); ?></td>
    <td><?php echo $row['fecha_de_registro']; ?></td>

    <td>
        <button class="btn btn-warning btn-sm">Editar</button>

        <a href="eliminar_usuario.php?id=<?php echo $row['id']; ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('¿Seguro que deseas eliminar esta cuenta?')">
           Eliminar
        </a>
    </td>
</tr>

<?php } ?>

</tbody>
</table>

<?php $stmt->close(); ?>