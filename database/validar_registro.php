<?php


session_start();
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/Conexion_base.php';
require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ── Configuración Gmail ── */
define('GMAIL_USER', 'lifebelow5of@gmail.com');        // ← cambia esto
define('GMAIL_PASS', 'noiw voss xahn lqjx'); // ← contraseña de aplicación Gmail (16 caracteres)
define('GMAIL_NAME', 'LifeBelow');

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

function enviarCodigoVerificacion(string $email, string $codigo): bool {
    $cuerpo = "
    <div style='font-family:Arial,sans-serif;background:#001e33;padding:32px;border-radius:16px;max-width:480px;margin:0 auto;'>
      <h2 style='color:#00d4e8;margin-bottom:8px;'>🌊 HYDRON · Vida Marina</h2>
      <p style='color:#e4f4ff;font-size:15px;'>
        Gracias por registrarte. Usa este código para activar tu cuenta:
      </p>
      <div style='text-align:center;margin:28px 0;'>
        <span style='background:#0a2a45;color:#00d4e8;font-size:2.2rem;font-weight:900;
                     letter-spacing:12px;padding:16px 28px;border-radius:14px;
                     border:2px solid rgba(0,200,220,0.3);display:inline-block;'>
          {$codigo}
        </span>
      </div>
      <p style='color:rgba(140,190,215,0.7);font-size:13px;'>
        Este código expira en <strong>15 minutos</strong>.<br>
        Si no realizaste esta acción, ignora este mensaje.
      </p>
      <hr style='border-color:rgba(0,160,200,0.15);margin:20px 0;'>
      <p style='color:rgba(100,160,200,0.5);font-size:12px;text-align:center;'>© " . date('Y') . " HYDRON</p>
    </div>";

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = GMAIL_USER;
        $mail->Password   = GMAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(GMAIL_USER, GMAIL_NAME);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = '🌊 Verifica tu cuenta en HYDRON';
        $mail->Body    = $cuerpo;
        $mail->AltBody = "Tu código de verificación es: {$codigo} (expira en 15 minutos)";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer Error: ' . $e->getMessage());
        return false;
    }
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
   registro — PRODUCCIÓN (con verificación)
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

    // Generar código verificación
    $codigo         = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $codigoExp      = date('Y-m-d H:i:s', time() + 900);
    $codigoGuardado = $codigo . '|' . $codigoExp;

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $rol  = 'user';

    // Insertar con verified=0
    $ins = $conn->prepare(
        'INSERT INTO usuarios (user, email, password, rol, verified, verification_code, fecha_de_registro)
         VALUES (?, ?, ?, ?, 0, ?, NOW())'
    );
    $ins->bind_param('sssss', $user, $email, $hash, $rol, $codigoGuardado);
    $ins->execute();
    $ins->close();

    $_SESSION['reg_pending_email'] = $email;
    $_SESSION['reg_pending_exp']   = time() + 900;

    $enviado = enviarCodigoVerificacion($email, $codigo);
    if (!$enviado) respond('mail_error');

    respond('ok_verify');
}

/* ══════════════════════════════════════
   verificar_cuenta
══════════════════════════════════════ */
if ($action === 'verificar_cuenta') {
    $email = trim($_POST['email'] ?? '');
    $code  = trim($_POST['code']  ?? '');

    if (!$email || !$code) respond('empty_fields');

    $stmt = $conn->prepare('SELECT id, verification_code, user, rol FROM usuarios WHERE email = ? AND verified = 0 LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) respond('not_found');

    $partes     = explode('|', $row['verification_code']);
    $storedCode = $partes[0] ?? '';
    $storedExp  = $partes[1] ?? '';

    if (strtotime($storedExp) < time())       respond('expired');
    if (!hash_equals($storedCode, $code))     respond('invalid');

    // Activar cuenta
    $upd = $conn->prepare('UPDATE usuarios SET verified = 1, verification_code = NULL WHERE id = ?');
    $upd->bind_param('i', $row['id']);
    $upd->execute();
    $upd->close();

    $ll = $conn->prepare('UPDATE usuarios SET last_login = NOW() WHERE id = ?');
    $ll->bind_param('i', $row['id']);
    $ll->execute();
    $ll->close();

    session_regenerate_id(true);
    $_SESSION['user_id']   = $row['id'];
    $_SESSION['username']  = $row['user'];
    $_SESSION['user_rol']  = $row['rol'];
    $_SESSION['logged_in'] = true;
    unset($_SESSION['reg_pending_email'], $_SESSION['reg_pending_exp']);

    respond('ok');
}

/* ══════════════════════════════════════
   reenviar_codigo
══════════════════════════════════════ */
if ($action === 'reenviar_codigo') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) respond('empty_fields');

    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND verified = 0 LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) respond('not_found');
    $stmt->close();

    $codigo         = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $codigoExp      = date('Y-m-d H:i:s', time() + 900);
    $codigoGuardado = $codigo . '|' . $codigoExp;

    $upd = $conn->prepare('UPDATE usuarios SET verification_code = ? WHERE email = ?');
    $upd->bind_param('ss', $codigoGuardado, $email);
    $upd->execute();
    $upd->close();

    $enviado = enviarCodigoVerificacion($email, $codigo);
    if (!$enviado) respond('mail_error');

    respond('ok');
}

respond('unknown_action');