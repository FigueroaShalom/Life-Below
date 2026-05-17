<?php
// Valores por defecto para localhost (XAMPP)
$host = "localhost";
$usuario = "root";
$password = "";
$base = "life_below_blog";

// 1. Intentar cargar desde un archivo de configuración PHP estándar (ideal para hostings que ocultan .env)
$config_path = __DIR__ . '/config_db.php';
if (file_exists($config_path)) {
    include $config_path;
} else {
    // 2. Si no existe, intentar cargar desde el archivo .env local
    $env_path = __DIR__ . '/../.env';
    if (file_exists($env_path)) {
        $env_vars = parse_ini_file($env_path);
        if ($env_vars) {
            $host = $env_vars['DB_HOST'] ?? $host;
            $usuario = $env_vars['DB_USER'] ?? $usuario;
            $password = $env_vars['DB_PASS'] ?? $password;
            $base = $env_vars['DB_NAME'] ?? $base;
        }
    }
}

$conn = new mysqli($host, $usuario, $password, $base);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Auto-instalación de la tabla solicitudes_rol si no existe
$conn->query("CREATE TABLE IF NOT EXISTS `solicitudes_rol` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) NOT NULL,
  `rol_solicitado` VARCHAR(50) NOT NULL,
  `estado` ENUM('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_solicitudes_rol_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
?>
