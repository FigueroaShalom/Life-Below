<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/Conexion_base.php';

header('Content-Type: application/json');

// 1. Verificar autenticación
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no válida o expirada.']);
    exit;
}

$id_user = (int)$_SESSION['id'];
$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'crear_solicitud':
        $rol_solicitado = trim($_POST['rol_solicitado'] ?? '');

        // Obtener rol actual del usuario desde la base de datos
        $stmt_user = $conn->prepare("SELECT rol FROM usuarios WHERE id = ?");
        $stmt_user->bind_param("i", $id_user);
        $stmt_user->execute();
        $res_user = $stmt_user->get_result()->fetch_assoc();
        $stmt_user->close();

        if (!$res_user) {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
            exit;
        }

        $rol_actual = $res_user['rol'];

        // Administradores no necesitan solicitar rol
        if ($rol_actual === 'administrador') {
            echo json_encode(['success' => false, 'error' => 'Los administradores ya poseen todos los privilegios.']);
            exit;
        }

        // Restricción estricta de roles solicitados
        $roles_permitidos = ['autor', 'editor'];
        if (!in_array($rol_solicitado, $roles_permitidos)) {
            echo json_encode(['success' => false, 'error' => 'El rol solicitado no es válido o está restringido.']);
            exit;
        }

        // No solicitar su propio rol
        if ($rol_solicitado === $rol_actual) {
            echo json_encode(['success' => false, 'error' => 'Ya posees el rol de ' . htmlspecialchars($rol_solicitado) . '.']);
            exit;
        }

        // Verificar si ya tiene una solicitud pendiente
        $stmt_check = $conn->prepare("SELECT id FROM solicitudes_rol WHERE usuario_id = ? AND estado = 'pendiente'");
        $stmt_check->bind_param("i", $id_user);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result()->fetch_assoc();
        $stmt_check->close();

        if ($res_check) {
            echo json_encode(['success' => false, 'error' => 'Ya tienes una solicitud de ascenso en revisión.']);
            exit;
        }

        // Insertar la solicitud
        $stmt_insert = $conn->prepare("INSERT INTO solicitudes_rol (usuario_id, rol_solicitado, estado) VALUES (?, ?, 'pendiente')");
        $stmt_insert->bind_param("is", $id_user, $rol_solicitado);
        
        if ($stmt_insert->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Tu solicitud para ser ' . ucfirst($rol_solicitado) . ' ha sido enviada con éxito.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar la solicitud: ' . $conn->error]);
        }
        $stmt_insert->close();
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción no reconocida.']);
        break;
}

$conn->close();
?>
