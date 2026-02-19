<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$file = $_GET['file'] ?? '';
$file_path = POSTS_DIR . $file;

if (file_exists($file_path)) {
    $post = json_decode(file_get_contents($file_path), true);
    header('Content-Type: application/json');
    echo json_encode($post);
} else {
    http_response_code(404);
}
?>