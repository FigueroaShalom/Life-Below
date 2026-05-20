<?php
// Configuración de Base de Datos
// Se detecta si estamos en local o en producción

if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
    // Local (XAMPP)
    $host     = "localhost";
    $usuario  = "root";
    $password = "";
    $base     = "life_below_blog";
} else {
    // Producción (IONOS)
    $host     = "db5020481855.hosting-data.io";
    $usuario  = "dbu928705";
    $password = "Hydron2025!";
    $base     = "dbs15685592";
}
?>
