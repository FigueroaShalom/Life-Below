<?php
/**
 * database/validar_login.php — HYDRON Auth v2
 * Login con usuario o email, remember me, bloqueo por intentos, CSRF.
 */

session_start();
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/Conexion_base.php';

function respond(string $msg): void { echo $msg; exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond('error_method');

// CSRF
$csrf = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) respond('error_csrf');

$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password']        ?? '';
$remember   = ($_POST['remember'] ?? '0') === '1';

if ($identifier === '' || $password === '') respond('empty_fields');

/* ── Protección fuerza bruta ── */
$maxIntentos = 5;
$bloqueoSeg  = 300;

if (!isset($_SESSION['login_intentos'])) $_SESSION['login_intentos'] = 0;

if (!empty($_SESSION['login_bloqueado_hasta'])) {
    $diff = $_SESSION['login_bloqueado_hasta'] - time();
    if ($diff > 0) respond('locked:' . $diff);
    else unset($_SESSION['login_intentos'], $_SESSION['login_bloqueado_hasta']);
}

/* ── Buscar usuario (por email o username) ── */
$campo = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'user';
$stmt  = $conn->prepare("SELECT id, user, password, rol, verified FROM usuarios WHERE {$campo} = ? LIMIT 1");
$stmt->bind_param('s', $identifier);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    $_SESSION['login_intentos']++;
    if ($_SESSION['login_intentos'] >= $maxIntentos)
        $_SESSION['login_bloqueado_hasta'] = time() + $bloqueoSeg;
    respond('error_user');
}

/* ── Verificar contraseña ── */
if (!password_verify($password, $usuario['password'])) {
    $_SESSION['login_intentos']++;
    if ($_SESSION['login_intentos'] >= $maxIntentos)
        $_SESSION['login_bloqueado_hasta'] = time() + $bloqueoSeg;
    respond('error_password');
}

/* ── Verificación de correo obligatoria ── */
if ((int)$usuario['verified'] !== 1) respond('not_verified');

/* ── Login exitoso ── */
unset($_SESSION['login_intentos'], $_SESSION['login_bloqueado_hasta']);
session_regenerate_id(true);

$_SESSION['user_id']   = $usuario['id'];
$_SESSION['username']  = $usuario['user'];
$_SESSION['user_rol']  = $usuario['rol'];
$_SESSION['logged_in'] = true;

/* ── Actualizar last_login ── */
$upd = $conn->prepare('UPDATE usuarios SET last_login = NOW() WHERE id = ?');
$upd->bind_param('i', $usuario['id']);
$upd->execute();
$upd->close();

/* ── Remember me (cookie 30 días) ── */
if ($remember) {
    $token = bin2hex(random_bytes(32));
    $hash  = hash('sha256', $token);
    $exp   = date('Y-m-d H:i:s', time() + (30 * 24 * 3600));

    $rm = $conn->prepare('UPDATE usuarios SET reset_token = ?, reset_token_exp = ? WHERE id = ?');
    $rm->bind_param('ssi', $hash, $exp, $usuario['id']);
    $rm->execute();
    $rm->close();

    setcookie('hydron_remember', $token . ':' . $usuario['id'], [
        'expires'  => time() + (30 * 24 * 3600),
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

respond('ok');