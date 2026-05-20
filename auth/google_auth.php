<?php

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/Conexion_base.php';

/* ── Generar state anti-CSRF ── */
$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;

/* ── Construir URL de autorización ── */
$params = http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $state,
    'access_type'   => 'online',
    'prompt'        => 'select_account',
]);

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
exit;