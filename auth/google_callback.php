<?php
/**
 * auth/google_callback.php — HYDRON Auth v2
 * Callback de Google OAuth 2.0
 */

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/Conexion_base.php';
/* ── Si ya hay sesión activa, cerrarla para poder iniciar con Google ── */
if (!empty($_SESSION['logged_in'])) {
    // Limpiar sesión anterior sin destruir google_oauth_state
    $savedState = $_SESSION['google_oauth_state'] ?? '';
    session_unset();
    $_SESSION['google_oauth_state'] = $savedState;
}

function redirectWithError(string $msg): void {
    $_SESSION['auth_error'] = $msg;
    header('Location: ../index.php?section=login');
    exit;
}

/* ── Verificar state ── */
$state = $_GET['state'] ?? '';
if (empty($_SESSION['google_oauth_state']) || !hash_equals($_SESSION['google_oauth_state'], $state)) {
    redirectWithError('Estado de OAuth inválido. Intenta de nuevo.');
}
unset($_SESSION['google_oauth_state']);

/* ── Verificar code ── */
$code = $_GET['code'] ?? '';
if (!$code) redirectWithError('No se recibió código de Google.');

/* ── Intercambiar code por access_token ── */
$tokenUrl  = 'https://oauth2.googleapis.com/token';
$tokenData = http_build_query([
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
]);

$ch = curl_init($tokenUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $tokenData,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_SSL_VERIFYPEER => false, // false en localhost, true en producción
]);
$tokenResponse = curl_exec($ch);
curl_close($ch);

$tokenJson = json_decode($tokenResponse, true);
if (empty($tokenJson['access_token'])) {
    redirectWithError('Error al obtener token de Google.');
}

/* ── Obtener datos del usuario de Google ── */
$ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenJson['access_token']],
    CURLOPT_SSL_VERIFYPEER => false, // false en localhost, true en producción
]);
$userInfo = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($userInfo['email'])) {
    redirectWithError('No se pudo obtener información del usuario.');
}

$email    = $userInfo['email'] ?? '';

/* ── Buscar usuario en BD (mysqli) ── */
$stmt = $conn->prepare('SELECT id, user, rol FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    /* ── Crear nuevo usuario desde Google ── */
    $username = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $email)[0]));

    /* Asegurar username único */
    $base = $username;
    $i    = 1;
    while (true) {
        $chk = $conn->prepare('SELECT id FROM usuarios WHERE user = ? LIMIT 1');
        $chk->bind_param('s', $username);
        $chk->execute();
        $chk->store_result();
        $existe = $chk->num_rows > 0;
        $chk->close();
        if (!$existe) break;
        $username = $base . $i++;
    }

    $passAleatoria = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
    $rol           = 'user';

    $ins = $conn->prepare(
    'INSERT INTO usuarios (user, email, password, rol, verified, fecha_de_registro, last_login)
     VALUES (?, ?, ?, ?, 1, NOW(), NOW())'
);
    $ins->bind_param('ssss', $username, $email, $passAleatoria, $rol);
    $ins->execute();
    $ins->close();

    $userId        = (int)$conn->insert_id;
    $userRole      = 'user';

} else {
    $userId        = $usuario['id'];
    $userRole      = $usuario['rol'];
    $username      = $usuario['user'];

    /* Asegurar cuenta verificada + actualizar last_login */
    $upd = $conn->prepare('UPDATE usuarios SET verified = 1, last_login = NOW() WHERE id = ?');
    $upd->bind_param('i', $userId);
    $upd->execute();
    $upd->close();
}

/* ── Iniciar sesión ── */
session_regenerate_id(true);
$_SESSION['user_id']   = $userId;
$_SESSION['username']  = $username;
$_SESSION['user_rol']  = $userRole;
$_SESSION['logged_in'] = true;

header('Location: ../index.php?section=inicio');
exit;