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
    if (isset($_FILES['foto_perfil'])) {
        $fileError = $_FILES['foto_perfil']['error'];
        if ($fileError === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            $nombre_archivo = 'perfil_' . $id_user . '_' . time() . '.' . $ext;
            
            $dir_destino = '../uploads/perfiles';
            if (!is_dir($dir_destino)) {
                mkdir($dir_destino, 0777, true);
            }
            
            $ruta_destino = $dir_destino . '/' . $nombre_archivo;
            $ruta_bd = 'uploads/perfiles/' . $nombre_archivo;

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_destino)) {
                // Obtener rol del usuario
                $rol = $_SESSION['rol'] ?? 'usuario';
                
                if ($rol === 'administrador') {
                    // Si es administrador, se aprueba e instala inmediatamente
                    $stmt = $conn->prepare("UPDATE usuarios SET foto = ?, foto_pendiente = NULL, estado_foto = 'aprobada' WHERE id = ?");
                    $stmt->bind_param("si", $ruta_bd, $id_user);
                    $stmt->execute();
                    
                    // Actualizar la foto en la sesión
                    $_SESSION['foto'] = $ruta_bd;
                    
                    echo json_encode(['success' => true, 'mensaje' => '¡Foto de perfil actualizada de forma inmediata por ser Administrador!']);
                } else {
                    // Si no es administrador, pasa al flujo de aprobación
                    $stmt = $conn->prepare("UPDATE usuarios SET foto_pendiente = ?, estado_foto = 'pendiente' WHERE id = ?");
                    $stmt->bind_param("si", $ruta_bd, $id_user);
                    $stmt->execute();
                    
                    echo json_encode(['success' => true, 'mensaje' => 'Foto de perfil subida con éxito. Un administrador debe aprobarla.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'No se pudo guardar la imagen en el servidor. Verifica los permisos de la carpeta uploads.']);
            }
        } else {
            $errorMsgs = [
                UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por el servidor (upload_max_filesize).',
                UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo permitido por el formulario.',
                UPLOAD_ERR_PARTIAL    => 'El archivo se subió solo parcialmente.',
                UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal en el servidor.',
                UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco del servidor.',
                UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida del archivo.'
            ];
            $msg = $errorMsgs[$fileError] ?? 'Error de subida desconocido (Código: ' . $fileError . ')';
            echo json_encode(['success' => false, 'error' => $msg]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se recibió ningún archivo de imagen.']);
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

    if (empty($password)) {
        echo json_encode(['success' => false, 'error' => 'La nueva contraseña no puede estar vacía']);
        exit();
    }

    $rol = $_SESSION['rol'] ?? 'usuario';
    if ($rol === 'administrador') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, reset_token_exp = NULL WHERE id = ?");
        $stmt->bind_param("si", $hash, $id_user);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
        exit();
    }

    // Para usuarios normales se requiere el código
    if (empty($codigo)) {
        echo json_encode(['success' => false, 'error' => 'Código de verificación incompleto']);
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
        echo json_encode(['success' => false, 'error' => 'Código inválido o expirado']);
    }
}
?>
