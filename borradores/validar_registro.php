
/**
 * database/validar_registro.php — HYDRON Auth v2
 * VERSIÓN LOCALHOST — verificación automática sin correo
 */

session_start();
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/Conexion_base.php';

function respond(string $msg): void { echo $msg; exit; }

function validarPassword(string $p): bool|string {
    $err = [];
    if (strlen($p) < 8)                    $err[] = 'mínimo 8 caracteres';
    if (!preg_match('/[A-Z]/', $p))        $err[] = 'una mayúscula';
    if (!preg_match('/[a-z]/', $p))        $err[] = 'una minúscula';
    if (!preg_match('/[0-9]/', $p))        $err[] = 'un número';
    if (!preg_match('/[^a-zA-Z0-9]/', $p)) $err[] = 'un símbolo especial';
    return empty($err) ? true : implode(', ', $err);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond('error_method');

$action = $_POST['action'] ?? '';

/* ══════════════════════════════════════
   validar_user
══════════════════════════════════════ */
if ($action === 'validar_user') {
    $user = trim($_POST['user'] ?? '');
    if ($user === '') respond('empty');
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE user = ? LIMIT 1');
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $stmt->store_result();
    respond($stmt->num_rows > 0 ? 'existe' : 'disponible');
}

/* ══════════════════════════════════════
   validar_email
══════════════════════════════════════ */
if ($action === 'validar_email') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respond('invalid');
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    respond($stmt->num_rows > 0 ? 'existe' : 'disponible');
}

/* ══════════════════════════════════════
   registro — LOCALHOST (verified=1 directo)
══════════════════════════════════════ */
if ($action === 'registro') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) respond('error_csrf');

   
    $user     = trim($_POST['user']      ?? '');
    $email    = trim($_POST['email']     ?? '');
    $password = $_POST['password']       ?? '';

    if (!$user || !$email || !$password) respond('empty_fields');
    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $user)) respond('user_invalid');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))    respond('email_invalid');

    $passCheck = validarPassword($password);
    if ($passCheck !== true) respond('password_weak:' . $passCheck);

    // Verificar duplicados
    $s1 = $conn->prepare('SELECT id FROM usuarios WHERE user = ? LIMIT 1');
    $s1->bind_param('s', $user);
    $s1->execute();
    $s1->store_result();
    if ($s1->num_rows > 0) respond('user');
    $s1->close();

    $s2 = $conn->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $s2->bind_param('s', $email);
    $s2->execute();
    $s2->store_result();
    if ($s2->num_rows > 0) respond('email');
    $s2->close();

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $rol  = 'user';

    // ── Insertar con verified=1 directamente (localhost sin correo) ──
$ins = $conn->prepare(
    'INSERT INTO usuarios (user, email, password, rol, verified, fecha_de_registro)
     VALUES (?, ?, ?, ?, 1, NOW())'
);
$ins->bind_param('ssss', $user, $email, $hash, $rol);
    $ins->execute();
    $newId = (int)$conn->insert_id;
    $ins->close();

    // Iniciar sesión automáticamente
    session_regenerate_id(true);
    $_SESSION['user_id']   = $newId;
    $_SESSION['username']  = $user;
    $_SESSION['user_rol']  = $rol;
    $_SESSION['logged_in'] = true;

    respond('ok_direct');
}

/* ══════════════════════════════════════
   verificar_cuenta (no se usa en localhost
   pero se mantiene por compatibilidad)
══════════════════════════════════════ */
if ($action === 'verificar_cuenta') {
    respond('ok');
}

/* ══════════════════════════════════════
   reenviar_codigo (no aplica en localhost)
══════════════════════════════════════ */
if ($action === 'reenviar_codigo') {
    respond('ok');
}

respond('unknown_action');