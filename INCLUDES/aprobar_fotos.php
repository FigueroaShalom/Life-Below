<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if ($_SESSION['rol'] != 'administrador') {
    echo "No autorizado";
    exit();
}
require_once __DIR__ . '/../database/Conexion_base.php';

$stmt = $conn->prepare("SELECT id, user, foto, foto_pendiente FROM usuarios WHERE estado_foto = 'pendiente'");
$stmt->execute();
$pendientes = $stmt->get_result();
?>

<div class="approval-container">
    <h3 class="fw-bold mb-4">🖼️ Aprobación de Fotos de Perfil</h3>

    <?php if($pendientes->num_rows == 0): ?>
        <div class="alert alert-light border text-center py-5">
            <i class="bi bi-check2-circle fs-1 text-success d-block mb-3"></i>
            <p class="mb-0">No hay fotos pendientes de aprobación.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while($row = $pendientes->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <span class="fw-bold text-navy">Usuario: <?php echo htmlspecialchars($row['user']); ?></span>
                        </div>
                        <div class="card-body text-center d-flex align-items-center justify-content-around">
                            <div class="text-center">
                                <span class="small text-muted d-block mb-1">Actual</span>
                                <img src="<?php echo htmlspecialchars($row['foto'] ?: 'https://cdn-icons-png.flaticon.com/512/149/149071.png'); ?>" 
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; opacity: 0.5;">
                            </div>
                            <div class="fs-3 text-muted">➜</div>
                            <div class="text-center">
                                <span class="small text-primary fw-bold d-block mb-1">Nueva</span>
                                <img src="<?php echo htmlspecialchars($row['foto_pendiente']); ?>" 
                                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid var(--ocean);">
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1" onclick="procesarFoto(<?php echo $row['id']; ?>, 'aprobar')">Aprobar</button>
                            <button class="btn btn-danger btn-sm flex-grow-1" onclick="procesarFoto(<?php echo $row['id']; ?>, 'rechazar')">Rechazar</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function procesarFoto(userId, accion) {
    if(!confirm(`¿Estás seguro de ${accion} esta foto?`)) return;

    fetch('./database/procesar_aprobacion_foto.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_usuario=${userId}&accion=${accion}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Acción completada.');
            cargar('aprobar_fotos');
        } else {
            alert('Error: ' + data.error);
        }
    });
}
</script>
