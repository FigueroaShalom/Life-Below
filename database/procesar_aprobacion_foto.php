<?php
session_start();
require_once 'Conexion_base.php';

if(!isset($_SESSION['id']) || $_SESSION['rol'] != 'administrador'){
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_usuario = $_POST['id_usuario'] ?? '';
$accion = $_POST['accion'] ?? '';

header('Content-Type: application/json');

if ($accion == 'aprobar') {
    // Obtener la foto pendiente
    $stmt = $conn->prepare("SELECT foto_pendiente FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $foto = $stmt->get_result()->fetch_assoc()['foto_pendiente'];

    if ($foto) {
        $stmt = $conn->prepare("UPDATE usuarios SET foto = ?, foto_pendiente = NULL, estado_foto = 'aprobada' WHERE id = ?");
        $stmt->bind_param("si", $foto, $id_usuario);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No hay foto pendiente']);
    }
} 
elseif ($accion == 'rechazar') {
    $stmt = $conn->prepare("UPDATE usuarios SET foto_pendiente = NULL, estado_foto = 'rechazada' WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    echo json_encode(['success' => true]);
}
?>
