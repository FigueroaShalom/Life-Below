<?php
/**
 * logout.php — HYDRON Auth v2
 * Cierra la sesión de forma segura: destruye sesión, elimina cookie remember me.
 */

session_start();
require_once __DIR__ . '/config/db.php';

/* ── Limpiar cookie "Recordarme" de BD ── */
if (!empty($_COOKIE['hydron_remember'])) {
    [$token, $userId] = explode(':', $_COOKIE['hydron_remember']) + [null, null];
    if ($userId) {
        $pdo->prepare('UPDATE usuarios SET reset_token = NULL, reset_token_exp = NULL WHERE id = ?')
            ->execute([(int)$userId]);
    }
    // Borrar cookie
    setcookie('hydron_remember', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/* ── Destruir sesión ── */
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

/* ── Redirigir al login ── */
header('Location: index.php?section=login');
exit;