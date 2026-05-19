<?php
/**
 * auth/google_auth.php — HYDRON Auth v2
 * Autenticación con Google OAuth 2.0
 *
 * REQUISITOS:
 * 1. Crear proyecto en https://console.cloud.google.com
 * 2. Habilitar Google+ API / People API
 * 3. Crear credenciales OAuth 2.0 (tipo "Web Application")
 * 4. Agregar URI de redirección autorizado: https://tu-dominio.com/auth/google_callback.php
 * 5. Instalar librería: composer require league/oauth2-google
 *    o usa el flujo manual de curl que está aquí abajo.
 */

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