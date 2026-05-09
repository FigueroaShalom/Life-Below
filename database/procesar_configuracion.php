<?php
session_start();
require_once 'Conexion_base.php';

if(!isset($_SESSION['id'])){
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_user = $_SESSION['id'];
$accion = $_POST['accion'] ?? '';

header('Content-Type: application/json');

if ($accion == 'subir_foto') {
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = 'perfil_' . $id_user . '_' . time() . '.' . $ext;
        $ruta_destino = '../uploads/perfiles/' . $nombre_archivo;
        $ruta_bd = 'uploads/perfiles/' . $nombre_archivo;

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_tmp_name'] ?? $_FILES['foto_perfil']['tmp_name'], $ruta_destino)) {
            // Guardar como pendiente
            $stmt = $conn->prepare("UPDATE usuarios SET foto_pendiente = ?, estado_foto = 'pendiente' WHERE id = ?");
            $stmt->bind_param("si", $ruta_bd, $id_user);
            $stmt->execute();
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al mover el archivo']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se recibió el archivo']);
    }
}

elseif ($accion == 'solicitar_codigo') {
    $codigo = rand(100000, 999999);
    $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $stmt = $conn->prepare("UPDATE usuarios SET reset_token = ?, reset_token_exp = ? WHERE id = ?");
    $stmt->bind_param("ssi", $codigo, $expira, $id_user);
    $stmt->execute();

    // Intentar enviar mail
    $user_email = $_SESSION['email'];
    $asunto = "Codigo de seguridad - HYDRON";
    $mensaje = "Tu codigo para cambiar la contraseña es: " . $codigo;
    $headers = "From: no-reply@hydron.com";

    // Debug local (guardar en archivo si falla mail)
    $debug_file = '../uploads/debug_mail.txt';
    file_put_contents($debug_file, "Destinatario: $user_email\nCodigo: $codigo\nFecha: ".date('Y-m-d H:i:s')."\n---\n", FILE_APPEND);

    @mail($user_email, $asunto, $mensaje, $headers);

    echo json_encode(['success' => true, 'debug' => 'El codigo fue enviado (revisa uploads/debug_mail.txt si estas en localhost)']);
}

elseif ($accion == 'cambiar_password') {
    $codigo = $_POST['codigo'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($codigo) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit();
    }

    $stmt = $conn->prepare("SELECT reset_token, reset_token_exp FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res && $res['reset_token'] == $codigo && strtotime($res['reset_token_exp']) > time()) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, reset_token_exp = NULL WHERE id = ?");
        $stmt->bind_param("si", $hash, $id_user);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Codigo invalido o expirado']);
    }
}
?>
