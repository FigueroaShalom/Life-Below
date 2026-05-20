<?php
/**
 * database/recuperar_password.php — HYDRON Auth v2
 * Acciones: enviar_codigo, verificar_codigo, nueva_password
 */

session_start();
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/Conexion_base.php';

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

function enviarCodigoRecuperacion(string $email, string $codigo): bool {
    $cuerpo = "
    <div style='font-family:Arial,sans-serif;background:#001e33;padding:32px;border-radius:16px;max-width:480px;margin:0 auto;'>
      <h2 style='color:#00d4e8;margin-bottom:8px;'>🔑 HYDRON · Recupera tu contraseña</h2>
      <p style='color:#e4f4ff;font-size:15px;'>
        Recibimos una solicitud para restablecer tu contraseña.<br>
        Usa este código (válido por <strong>15 minutos</strong>):
      </p>
      <div style='text-align:center;margin:28px 0;'>
        <span style='background:#0a2a45;color:#00d4e8;font-size:2.2rem;font-weight:900;
                     letter-spacing:12px;padding:16px 28px;border-radius:14px;
                     border:2px solid rgba(0,200,220,0.3);display:inline-block;'>
          {$codigo}
        </span>
      </div>
      <p style='color:rgba(140,190,215,0.7);font-size:13px;'>
        Si no solicitaste esto, ignora este mensaje.
      </p>
      <hr style='border-color:rgba(0,160,200,0.15);margin:20px 0;'>
      <p style='color:rgba(100,160,200,0.5);font-size:12px;text-align:center;'>© " . date('Y') . " HYDRON</p>
    </div>";

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'lifebelow5of@gmail.com';
        $mail->Password   = 'noiw voss xahn lqjx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom('lifebelow5of@gmail.com', 'HYDRON');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Recupera tu contraseña - HYDRON';
        $mail->Body    = $cuerpo;
        $mail->AltBody = "Tu código de recuperación es: {$codigo} (expira en 15 minutos)";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer Error (recuperación): ' . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond('error_method');

$action = $_POST['action'] ?? '';

/* ══════════════════════════════════════
   enviar_codigo
══════════════════════════════════════ */
if ($action === 'enviar_codigo') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respond('invalid_email');

    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) respond('not_found');
    $stmt->close();

    $codigo = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $exp    = date('Y-m-d H:i:s', time() + 900);

    $upd = $conn->prepare('UPDATE usuarios SET reset_token = ?, reset_token_exp = ? WHERE email = ?');
    $upd->bind_param('sss', $codigo, $exp, $email);
    $upd->execute();
    $upd->close();

    $_SESSION['fp_email'] = $email;
    $_SESSION['fp_exp']   = time() + 900;

    enviarCodigoRecuperacion($email, $codigo);
    respond('ok');
}

/* ══════════════════════════════════════
   verificar_codigo
══════════════════════════════════════ */
if ($action === 'verificar_codigo') {
    $email = trim($_POST['email'] ?? '');
    $code  = trim($_POST['code']  ?? '');

    if (!$email || !$code) respond('empty_fields');

    $stmt = $conn->prepare('SELECT id, reset_token, reset_token_exp FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row)                                        respond('not_found');
    if (strtotime($row['reset_token_exp']) < time())  respond('expired');
    if (!hash_equals((string)$row['reset_token'], $code)) respond('invalid');

    // Generar token seguro para paso 3
    $token    = bin2hex(random_bytes(32));
    $tokenExp = date('Y-m-d H:i:s', time() + 600);

    $upd = $conn->prepare('UPDATE usuarios SET reset_token = ?, reset_token_exp = ? WHERE email = ?');
    $upd->bind_param('sss', $token, $tokenExp, $email);
    $upd->execute();
    $upd->close();

    $_SESSION['fp_token']     = $token;
    $_SESSION['fp_token_exp'] = time() + 600;
    $_SESSION['fp_email']     = $email;

    respond('ok:' . $token);
}

/* ══════════════════════════════════════
   nueva_password
══════════════════════════════════════ */
if ($action === 'nueva_password') {
    $token    = trim($_POST['token']    ?? '');
    $password = $_POST['password'] ?? '';

    if (!$token || !$password) respond('empty_fields');

    if (empty($_SESSION['fp_token']) || !hash_equals($_SESSION['fp_token'], $token)) respond('invalid_token');
    if (empty($_SESSION['fp_token_exp']) || $_SESSION['fp_token_exp'] < time())      respond('expired');

    $email = $_SESSION['fp_email'] ?? '';
    if (!$email) respond('session_error');

    $passCheck = validarPassword($password);
    if ($passCheck !== true) respond('password_weak:' . $passCheck);

    // Verificar token en BD también
    $stmt = $conn->prepare('SELECT id, reset_token, reset_token_exp FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row)                                            respond('not_found');
    if (strtotime($row['reset_token_exp']) < time())      respond('expired');
    if (!hash_equals((string)$row['reset_token'], $token)) respond('invalid_token');

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $upd = $conn->prepare('UPDATE usuarios SET password = ?, reset_token = NULL, reset_token_exp = NULL WHERE email = ?');
    $upd->bind_param('ss', $hash, $email);
    $upd->execute();
    $upd->close();

    unset($_SESSION['fp_token'], $_SESSION['fp_token_exp'], $_SESSION['fp_email'], $_SESSION['fp_exp']);

    respond('ok');
}

respond('unknown_action');