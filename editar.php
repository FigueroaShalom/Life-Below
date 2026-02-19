<?php
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Obtener el ID de la publicación
$id = $_GET['id'] ?? '';
$file_path = POSTS_DIR . $id;

if (!file_exists($file_path)) {
    header('Location: dashboard.php');
    exit;
}

// Cargar la publicación existente
$post = json_decode(file_get_contents($file_path), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Eliminar publicación
        if (unlink($file_path)) {
            header('Location: dashboard.php?message=eliminado');
            exit;
        } else {
            $error = 'Error al eliminar la publicación';
        }
    } else {
        // Actualizar publicación
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $image = $_POST['image'] ?? '';
        
        if (empty($title) || empty($content)) {
            $error = 'El título y el contenido son obligatorios';
        } else {
            $post['title'] = $title;
            $post['content'] = $content;
            $post['image'] = $image;
            $post['updated_at'] = date('Y-m-d H:i:s');
            
            if (file_put_contents($file_path, json_encode($post, JSON_PRETTY_PRINT))) {
                $message = 'Publicación actualizada exitosamente';
            } else {
                $error = 'Error al actualizar la publicación';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicación - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo"><?php echo SITE_NAME; ?></div>
            <div class="nav-links">
                <a href="index.php">Inicio</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="crear_publicacion.php">Crear Post</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h1>Editar Publicación</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="title">Título:</label>
                        <input type="text" id="title" name="title" 
                               value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Contenido:</label>
                        <textarea id="content" name="content" rows="10" required><?php 
                            echo htmlspecialchars($post['content']); 
                        ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">URL de la imagen:</label>
                        <input type="url" id="image" name="image" 
                               value="<?php echo htmlspecialchars($post['image'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn">Actualizar</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                </form>
                
                <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta publicación?');" 
                      style="margin-top: 1rem;">
                    <input type="hidden" name="delete" value="1">
                    <button type="submit" class="btn btn-danger">Eliminar Publicación</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>