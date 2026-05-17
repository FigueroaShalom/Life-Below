<?php
session_start();
include("Conexion_base.php");

function validarPassword($pass) {
    $errores = [];
    if (strlen($pass) < 8) $errores[] = "al menos 8 caracteres";
    if (!preg_match('/[A-Z]/', $pass)) $errores[] = "una mayúscula";
    if (!preg_match('/[0-9]/', $pass) && !preg_match('/[^a-zA-Z0-9]/', $pass)) $errores[] = "un número o símbolo especial";
    return empty($errores) ? true : implode(", ", $errores);
}

$action = $_POST['action'] ?? '';

// 🔍 VALIDAR USUARIO
if ($action === "validar_user") {

    $user = trim($_POST['user']);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE user = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    echo ($result->num_rows > 0) ? "existe" : "disponible";
}

// 🔍 VALIDAR EMAIL
elseif ($action === "validar_email") {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    echo ($result->num_rows > 0) ? "existe" : "disponible";
}

// 🚀 REGISTRO (PASO 1: Generación y Envío de Código)
elseif ($action === "registro") {
    $csrf = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || $csrf !== $_SESSION['csrf_token']) {
        echo "error_csrf";
        exit();
    }

    $user = trim($_POST['user']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($user) || empty($email) || empty($password)) {
        echo "vacio";
        exit();
    }

    // 🔍 VALIDAR CONTRASEÑA
    $passCheck = validarPassword($password);
    if ($passCheck !== true) {
        echo "password_weak: " . $passCheck;
        exit();
    }

    // 🔍 VALIDAR USER
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE user = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "user";
        exit();
    }

    // 🔍 VALIDAR EMAIL
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "email";
        exit();
    }

    // 🔐 ENCRIPTAR CONTRASEÑA
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // GENERAR CÓDIGO DE VERIFICACIÓN ALEATORIO
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // GUARDAR EN SESIÓN TEMPORAL
    $_SESSION['temp_registro'] = [
        'user' => $user,
        'email' => $email,
        'password' => $passwordHash,
        'codigo' => $code,
        'expira' => time() + 600 // 10 minutos
    ];

    // ENVIAR EMAIL DE VERIFICACIÓN (HTML Premium)
    $to = $email;
    $subject = "Código de verificación - HYDRON";
    $message = "
    <html>
    <head>
        <title>Verifica tu cuenta en HYDRON</title>
        <style>
            body { font-family: 'Nunito', Arial, sans-serif; background-color: #f0f8ff; color: #1a2a3a; padding: 20px; }
            .container { max-width: 500px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 16px; border: 1.5px solid rgba(0,120,190,0.15); box-shadow: 0 4px 24px rgba(0,40,80,0.08); }
            .header { text-align: center; margin-bottom: 20px; }
            .logo { font-size: 24px; font-weight: 900; color: #0077be; text-transform: uppercase; letter-spacing: 2px; }
            .title { font-size: 20px; font-weight: 800; text-align: center; color: #002a44; margin-bottom: 10px; }
            .code-box { background: linear-gradient(135deg, #0077be, #009aaa); color: #ffffff; font-size: 32px; font-weight: 900; text-align: center; padding: 15px; border-radius: 12px; letter-spacing: 5px; margin: 25px 0; box-shadow: 0 4px 16px rgba(0,119,190,0.2); }
            .footer { font-size: 12px; color: #5a7a9a; text-align: center; margin-top: 30px; border-top: 1px solid rgba(0,120,190,0.1); padding-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='logo'>🌊 HYDRON</div>
            </div>
            <div class='title'>¡Hola, $user!</div>
            <p style='text-align: center; line-height: 1.6;'>Gracias por unirte a la exploración marina. Para activar tu cuenta, ingresa el siguiente código de verificación de 6 dígitos en la página de registro:</p>
            <div class='code-box'>$code</div>
            <p style='text-align: center; font-size: 13px; color: #5a7a9a;'>Este código es válido por 10 minutos.</p>
            <div class='footer'>
                <p>HYDRON · Exploración y Conservación de la Vida Marina</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: HYDRON <soport@life-bel0w.mx>" . "\r\n";

    @mail($to, $subject, $message, $headers);

    // Escribir localmente para pruebas del desarrollador en localhost
    file_put_contents(__DIR__ . '/../debug_email_code.txt', "Email sent to $email ($user). Verification code: $code\n");

    echo "codigo_enviado";
}

// 🚀 VERIFICAR CÓDIGO (PASO 2: Confirmación, Inserción y Auto-Login)
elseif ($action === "verificar_codigo") {
    $csrf = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || $csrf !== $_SESSION['csrf_token']) {
        echo "error_csrf";
        exit();
    }

    $code_input = trim($_POST['code'] ?? '');

    if (empty($_SESSION['temp_registro'])) {
        echo "session_invalida";
        exit();
    }

    $temp = $_SESSION['temp_registro'];

    if (time() > $temp['expira']) {
        echo "expirado";
        exit();
    }

    if ($code_input !== $temp['codigo']) {
        echo "codigo_incorrecto";
        exit();
    }

    $user = $temp['user'];
    $email = $temp['email'];
    $passwordHash = $temp['password'];
    $rol = "usuario";

    // Doble check: verificar si el usuario o email se registraron mientras tanto
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE user = ? OR email = ?");
    $stmt->bind_param("ss", $user, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "ya_registrado";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (user, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user, $email, $passwordHash, $rol);

    if ($stmt->execute()) {
        $inserted_id = $stmt->insert_id;

        // ✅ AUTO LOGIN AL VERIFICAR CON ÉXITO
        $_SESSION['user_id'] = $inserted_id;
        $_SESSION['id'] = $inserted_id;
        $_SESSION['user'] = $user;
        $_SESSION['email'] = $email;
        $_SESSION['rol'] = $rol;

        // Limpiar datos de registro temporal
        unset($_SESSION['temp_registro']);

        // Eliminar el archivo de debug local si existe
        if (file_exists(__DIR__ . '/../debug_email_code.txt')) {
            @unlink(__DIR__ . '/../debug_email_code.txt');
        }

        echo "verificado_ok";
    } else {
        echo "error";
    }
}

$conn->close();
?>