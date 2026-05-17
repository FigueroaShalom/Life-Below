<?php
// Archivo de diagnóstico temporal - ELIMINAR DESPUÉS DE USAR
// Acceder en: https://life-bel0w.mx/diagnostico.php

echo "<pre style='font-family:monospace; background:#111; color:#0f0; padding:20px; font-size:14px;'>";
echo "=== DIAGNÓSTICO HYDRON - IONOS ===\n\n";

// 1. Versión de PHP
echo "PHP VERSION: " . phpversion() . "\n\n";

// 2. Intentar incluir la conexión
echo "--- Cargando Conexion_base.php ---\n";
try {
    require_once __DIR__ . '/database/Conexion_base.php';
    echo "Conexion_base.php: OK\n";
    echo "Host usado: $host\n";
    echo "Usuario usado: $usuario\n";
    echo "Base de datos: $base\n\n";
} catch (Throwable $e) {
    echo "ERROR al cargar conexion: " . $e->getMessage() . "\n\n";
}

// 3. Estado de la conexión
echo "--- Estado de la conexión MySQL ---\n";
if (isset($conn) && !$conn->connect_error) {
    echo "Conexión MySQL: EXITOSA ✅\n\n";

    // 4. Tablas existentes
    echo "--- Tablas en la base de datos ---\n";
    $r = $conn->query("SHOW TABLES");
    if ($r && $r->num_rows > 0) {
        while ($row = $r->fetch_row()) {
            echo "  ✅ " . $row[0] . "\n";
        }
    } else {
        echo "  ⚠️  Sin tablas o error: " . $conn->error . "\n";
    }
} else {
    $err = isset($conn) ? $conn->connect_error : 'Variable $conn no definida';
    echo "Conexión MySQL: FALLÓ ❌\n";
    echo "Error: " . $err . "\n";
}

// 5. Variables de sesión (sin valores sensibles)
echo "\n--- Sesión activa ---\n";
session_start();
echo "Session ID: " . session_id() . "\n";
echo "Claves de sesión: " . (empty($_SESSION) ? "(vacía)" : implode(', ', array_keys($_SESSION))) . "\n";

// 6. config_db.php
echo "\n--- Archivo config_db.php ---\n";
$cfg = __DIR__ . '/database/config_db.php';
echo file_exists($cfg) ? "config_db.php: EXISTE ✅\n" : "config_db.php: NO ENCONTRADO ❌\n";

// 7. .env
$env = __DIR__ . '/.env';
echo file_exists($env) ? ".env: EXISTE ✅\n" : ".env: NO ENCONTRADO (normal en producción)\n";

echo "\n=== FIN DEL DIAGNÓSTICO ===";
echo "</pre>";
?>
