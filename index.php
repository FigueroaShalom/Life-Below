<?php
session_start();

// Configuración
define('SITE_NAME', 'HYDRON');
define('POSTS_DIR', 'posts/');

// Crear directorio de posts si no existe
if (!file_exists(POSTS_DIR)) {
    mkdir(POSTS_DIR, 0777, true);
}

// Crear directorio para comentarios
if (!file_exists(POSTS_DIR . 'comments/')) {
    mkdir(POSTS_DIR . 'comments/', 0777, true);
}

// Usuario único para pruebas
define('ADMIN_USER', 'marino');
define('ADMIN_PASS', 'oceano123');

// Función para obtener todas las publicaciones
function getPosts() {
    $posts = [];
    if (file_exists(POSTS_DIR)) {
        $files = glob(POSTS_DIR . 'post_*.json');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $post = json_decode($content, true);
            if ($post) {
                $post['filename'] = basename($file);
                // Cargar likes
                $likes_file = POSTS_DIR . 'likes/' . basename($file, '.json') . '_likes.json';
                if (file_exists($likes_file)) {
                    $likes = json_decode(file_get_contents($likes_file), true);
                    $post['likes'] = $likes['count'] ?? 0;
                } else {
                    $post['likes'] = 0;
                }
                $posts[] = $post;
            }
        }
        // Ordenar por fecha
        usort($posts, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
    }
    return $posts;
}

// Función para obtener comentarios de una publicación
function getComments($post_filename) {
    $comments_file = POSTS_DIR . 'comments/' . basename($post_filename, '.json') . '_comments.json';
    if (file_exists($comments_file)) {
        return json_decode(file_get_contents($comments_file), true) ?? [];
    }
    return [];
}

// Manejo de login
if (isset($_POST['login'])) {
    if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
        $_SESSION['user'] = ADMIN_USER;
    }
}

// Manejo de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Manejo de likes
if (isset($_POST['like_post']) && isset($_SESSION['user'])) {
    $post_id = $_POST['post_id'];
    $likes_dir = POSTS_DIR . 'likes/';
    if (!file_exists($likes_dir)) {
        mkdir($likes_dir, 0777, true);
    }
    
    $likes_file = $likes_dir . basename($post_id, '.json') . '_likes.json';
    $user_likes_file = $likes_dir . 'user_likes.json';
    
    // Cargar likes del post
    if (file_exists($likes_file)) {
        $likes = json_decode(file_get_contents($likes_file), true);
    } else {
        $likes = ['count' => 0, 'users' => []];
    }
    
    // Cargar likes de usuarios
    if (file_exists($user_likes_file)) {
        $user_likes = json_decode(file_get_contents($user_likes_file), true);
    } else {
        $user_likes = [];
    }
    
    $username = $_SESSION['user'];
    $like_key = $username . '_' . $post_id;
    
    if (!in_array($like_key, $user_likes)) {
        // Agregar like
        $likes['count']++;
        $likes['users'][] = $username;
        $user_likes[] = $like_key;
    } else {
        // Quitar like
        $likes['count']--;
        $likes['users'] = array_diff($likes['users'], [$username]);
        $user_likes = array_diff($user_likes, [$like_key]);
    }
    
    file_put_contents($likes_file, json_encode($likes));
    file_put_contents($user_likes_file, json_encode($user_likes));
    
    header('Location: index.php?section=articulos');
    exit;
}

// Manejo de comentarios
if (isset($_POST['add_comment']) && isset($_SESSION['user'])) {
    $post_id = $_POST['post_id'];
    $comment = trim($_POST['comment']);
    
    if (!empty($comment)) {
        $comments_file = POSTS_DIR . 'comments/' . basename($post_id, '.json') . '_comments.json';
        
        if (file_exists($comments_file)) {
            $comments = json_decode(file_get_contents($comments_file), true);
        } else {
            $comments = [];
        }
        
        $comments[] = [
            'user' => $_SESSION['user'],
            'comment' => htmlspecialchars($comment),
            'date' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($comments_file, json_encode($comments, JSON_PRETTY_PRINT));
    }
    
    header('Location: index.php?section=articulos&post=' . urlencode($post_id));
    exit;
}

// Manejo de creación/edición de publicaciones
$message = '';
$error = '';

if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_post']) || isset($_POST['update_post'])) {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category = $_POST['category'] ?? 'general';
        $image = $_POST['image'] ?? '';
        $author = $_SESSION['user'];
        
        if (empty($title) || empty($content)) {
            $error = 'El título y el contenido son obligatorios';
        } else {
            $post = [
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'image' => $image,
                'author' => $author,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if (isset($_POST['update_post']) && isset($_POST['post_id'])) {
                // Actualizar publicación existente
                $filename = POSTS_DIR . $_POST['post_id'];
                if (file_exists($filename)) {
                    $existing = json_decode(file_get_contents($filename), true);
                    $post['created_at'] = $existing['created_at'];
                    $post['updated_at'] = date('Y-m-d H:i:s');
                    file_put_contents($filename, json_encode($post, JSON_PRETTY_PRINT));
                    $message = 'Publicación actualizada';
                }
            } else {
                // Crear nueva publicación
                $filename = POSTS_DIR . 'post_' . uniqid() . '.json';
                file_put_contents($filename, json_encode($post, JSON_PRETTY_PRINT));
                $message = 'Publicación creada';
            }
        }
    }
    
    // Eliminar publicación
    if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
        $filename = POSTS_DIR . $_POST['post_id'];
        if (file_exists($filename)) {
            unlink($filename);
            $message = 'Publicación eliminada';
        }
    }
}

$posts = getPosts();
$current_section = $_GET['section'] ?? 'inicio';
$selected_post = $_GET['post'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Aprendiendo sobre la vida marina</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header con navegación -->
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <!--aqui tenemos que poner nuestro logo -->
                    <span class="logo-icon"></span>
                    <?php echo SITE_NAME; ?>
                </div>
                
                <!-- Menú de navegación principal -->
                <div class="nav-menu">
                    <a href="?section=inicio" class="nav-link <?php echo $current_section === 'inicio' ? 'active' : ''; ?>">
                        <span></span> Inicio
                    </a>
                    <a href="?section=watch" class="nav-link <?php echo $current_section === 'watch' ? 'active' : ''; ?>">
                        <span>▶</span> WATCH
                    </a>
                    <a href="?section=galeria" class="nav-link <?php echo $current_section === 'galeria' ? 'active' : ''; ?>">
                        <span></span> GALERÍA
                    </a>
                    <a href="?section=noticias" class="nav-link <?php echo $current_section === 'noticias' ? 'active' : ''; ?>">
                        <span></span> NOTICIAS
                    </a>
                    <a href="?section=articulos" class="nav-link <?php echo $current_section === 'articulos' ? 'active' : ''; ?>">
                        <span></span> ARTÍCULOS
                    </a>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="?section=dashboard" class="nav-link <?php echo $current_section === 'dashboard' ? 'active' : ''; ?>">
                            <span></span> Dashboard
                        </a>
                        <a href="?logout=1" class="nav-link">
                            <span></span> Salir
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Botón para login (si no está logueado) -->
                <?php if (!isset($_SESSION['user']) && $current_section !== 'login'): ?>
                    <a href="?section=login" class="login-btn">LOGIN</a>
                     <a href="?section=register" class="login-btn">REGISTER</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <!-- Sección de Inicio -->
<?php if ($current_section === 'inicio'): ?>
    <div class="hero-section" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=1200'); background-size: cover; background-position: center;">
        <h1> <?php echo SITE_NAME; ?> LES DA LA BIENVENIDA</h1>
        <p class="hero-subtitle">Descubre la fascinante vida que habita en nuestros océanos</p>
    </div>
    
    <div class="features-grid">
        <div class="feature-card" style="background-image: linear-gradient(rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.67)), url('https://images.unsplash.com/photo-1560275619-4662e36fa65c?w=400'); background-size: cover;">
            <div class="feature-icon"></div>
            <h3>+500 Especies</h3>
            <p>Descubre la increíble diversidad de la vida marina</p>
        </div>
        <div class="feature-card" style="background-image: linear-gradient(rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.66)), url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400'); background-size: cover;">
            <div class="feature-icon"></div>
            <h3>7 Mares</h3>
            <p>Explora los diferentes ecosistemas marinos</p>
        </div>
        <div class="feature-card" style="background-image: linear-gradient(rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.63)), url('https://unsplash.com/es/fotos/un-monton-de-medusas-flotando-en-el-agua-99yyNMHUgGk'); background-size: cover;">
            <div class="feature-icon"></div>
            <h3>Ciencia Ciudadana</h3>
            <p>Contribuye a la investigación marina</p>
        </div>
    </div>
    
    <h2 class="section-title">Últimos descubrimientos</h2>
    
    <!-- Carrusel de noticias -->
    <div class="news-carousel">
        <div class="carousel-container">
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1582967788606-a171d1080cb0?w=800" alt="Noticia 1">
                <div class="carousel-caption">
                    <h3>Descubren nueva especie de coral en el Pacífico</h3>
                    <p>Científicos encuentran un arrecife prístino en Galápagos</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800" alt="Noticia 2">
                <div class="carousel-caption">
                    <h3>Ballenas jorobadas regresan al Ártico</h3>
                    <p>Avistan 50 ejemplares en su migración anual</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=800" alt="Noticia 3">
                <div class="carousel-caption">
                    <h3>Proyecto de limpieza oceánica récord</h3>
                    <p>Remueven 100 toneladas de plástico del Pacífico</p>
                    <p>HOLAAAAAAAAAAAAAAAAAAAAA</p>
                </div>
            </div>
        </div>
        <button class="carousel-btn prev" onclick="moveSlide(-1)">❮</button>
        <button class="carousel-btn next" onclick="moveSlide(1)">❯</button>
        <div class="carousel-dots">
            <span class="dot active" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </div>
<?php endif; ?>


            <!-- Sección WATCH -->
            <?php if ($current_section === 'watch'): ?>
                <h1>WATCH - Videos sobre vida marina</h1>
                <div class="watch-grid">
                    <!-- Esta sección está vacía como solicitaste -->
                </div>
            <?php endif; ?>

            <!-- Sección GALERÍA -->
            <?php if ($current_section === 'galeria'): ?>
                <h1>Galería de especies marinas</h1>
                
                <div class="gallery-filters">
                    <select id="categoryFilter" class="gallery-select" onchange="filterGallery()">
                        <option value="">Todas las categorías</option>
                        <option value="peces">Peces</option>
                        <option value="mamiferos">Mamíferos</option>
                        <option value="moluscos">Moluscos</option>
                        <option value="crustaceos">Crustáceos</option>
                        <option value="corales">Corales</option>
                    </select>
                    
                    <select id="speciesFilter" class="gallery-select" onchange="filterGallery()">
                        <option value="">Todas las especies</option>
                        <optgroup label="Peces">
                            <option value="pez_payaso">Pez Payaso</option>
                            <option value="pez_angel">Pez Ángel</option>
                            <option value="pez_mariposa">Pez Mariposa</option>
                        </optgroup>
                        <optgroup label="Mamíferos">
                            <option value="delfin">Delfín</option>
                            <option value="ballena">Ballena</option>
                            <option value="orca">Orca</option>
                        </optgroup>
                    </select>
                </div>
                
                <div class="gallery-grid">
                    <div class="gallery-item" data-category="peces" data-species="pez_payaso">
                        <img src="https://images.unsplash.com/photo-1561553580-b27900e8edb5?w=400" alt="Pez Payaso">
                        <div class="gallery-info">
                            <h3>Pez Payaso</h3>
                            <p>Amphiprioninae</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item" data-category="mamiferos" data-species="delfin">
                        <img src="https://images.unsplash.com/photo-1560275619-4662e36fa65c?w=400" alt="Delfín">
                        <div class="gallery-info">
                            <h3>Delfín Mular</h3>
                            <p>Tursiops truncatus</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item" data-category="mamiferos" data-species="ballena">
                        <img src="https://images.unsplash.com/photo-1568430462989-44163eb1752f?w=400" alt="Ballena">
                        <div class="gallery-info">
                            <h3>Ballena Jorobada</h3>
                            <p>Megaptera novaeangliae</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item" data-category="peces" data-species="pez_angel">
                        <img src="https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?w=400" alt="Pez Ángel">
                        <div class="gallery-info">
                            <h3>Pez Ángel</h3>
                            <p>Pomacanthidae</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item" data-category="corales">
                        <img src="https://images.unsplash.com/photo-1582967788606-a171d1080cb0?w=400" alt="Coral">
                        <div class="gallery-info">
                            <h3>Coral Cerebro</h3>
                            <p>Diploria labyrinthiformis</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item" data-category="moluscos">
                        <img src="https://images.unsplash.com/photo-1559586619-99fbb9a8e1d8?w=400" alt="Pulpo">
                        <div class="gallery-info">
                            <h3>Pulpo Común</h3>
                            <p>Octopus vulgaris</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Sección NOTICIAS -->
            <?php if ($current_section === 'noticias'): ?>
                <h1>Noticias y Eventos</h1>
                
                <div class="news-categories">
                    <a href="?section=noticias&sub=eventos" class="news-category-btn <?php echo ($_GET['sub'] ?? '') === 'eventos' ? 'active' : ''; ?>">
                         EVENTOS
                    </a>
                    <a href="?section=noticias&sub=proyectos" class="news-category-btn <?php echo ($_GET['sub'] ?? '') === 'proyectos' ? 'active' : ''; ?>">
                         PROYECTOS
                    </a>
                    <a href="?section=noticias&sub=noticias" class="news-category-btn <?php echo ($_GET['sub'] ?? '') === 'noticias' ? 'active' : ''; ?>">
                         NOTICIAS
                    </a>
                </div>
                
                <div class="news-content">
                    <?php if (($_GET['sub'] ?? '') === 'eventos'): ?>
                        <div class="events-grid">
                            <div class="event-card">
                                <div class="event-date">15 MAR 2024</div>
                                <h3>Limpieza de playas</h3>
                                <p>Únete a nuestra jornada de limpieza en la costa</p>
                                <span class="event-location">📍 Playa Central</span>
                            </div>
                            <div class="event-card">
                                <div class="event-date">22 MAR 2024</div>
                                <h3>Charla: Conservación marina</h3>
                                <p>Expertos discutirán sobre protección de océanos</p>
                                <span class="event-location">📍 Auditorio Principal</span>
                            </div>
                            <div class="event-card">
                                <div class="event-date">05 ABR 2024</div>
                                <h3>Exposición fotográfica</h3>
                                <p>Las mejores fotos de vida marina</p>
                                <span class="event-location">📍 Museo de Ciencias</span>
                            </div>
                        </div>
                    <?php elseif (($_GET['sub'] ?? '') === 'proyectos'): ?>
                        <div class="projects-grid">
                            <div class="project-card">
                                <h3> Proyecto Coral</h3>
                                <p>Restauración de arrecifes de coral en el Caribe</p>
                                <div class="project-progress">
                                    <div class="progress-bar" style="width: 75%"></div>
                                    <span>75% completado</span>
                                </div>
                            </div>
                            <div class="project-card">
                                <h3> Seguimiento de ballenas</h3>
                                <p>Monitoreo satelital de migraciones</p>
                                <div class="project-progress">
                                    <div class="progress-bar" style="width: 45%"></div>
                                    <span>45% completado</span>
                                </div>
                            </div>
                            <div class="project-card">
                                <h3> Reducción de plásticos</h3>
                                <p>Iniciativa para reducir plásticos de un solo uso</p>
                                <div class="project-progress">
                                    <div class="progress-bar" style="width: 90%"></div>
                                    <span>90% completado</span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="news-grid">
                            <div class="news-article">
                                <img src="https://images.unsplash.com/photo-1582967788606-a171d1080cb0?w=400" alt="Noticia">
                                <div class="news-content">
                                    <h3>Descubren nueva especie en la Fosa de las Marianas</h3>
                                    <p class="news-date">12 Feb 2024</p>
                                    <p>Un equipo de investigadores ha descubierto una nueva especie de pez abisal...</p>
                                    <a href="#" class="read-more">Leer más →</a>
                                </div>
                            </div>
                            <div class="news-article">
                                <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=400" alt="Noticia">
                                <div class="news-content">
                                    <h3>La Gran Barrera de Coral muestra signos de recuperación</h3>
                                    <p class="news-date">05 Feb 2024</p>
                                    <p>Los últimos estudios muestran un aumento en la cobertura de coral...</p>
                                    <a href="#" class="read-more">Leer más →</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Sección ARTÍCULOS -->
            <?php if ($current_section === 'articulos'): ?>
                <h1>Artículos sobre vida marina</h1>
                
                <?php if ($selected_post): ?>
                    <!-- Vista detallada de un artículo -->
                    <?php 
                    $selected_post_data = null;
                    foreach ($posts as $post) {
                        if ($post['filename'] === $selected_post) {
                            $selected_post_data = $post;
                            break;
                        }
                    }
                    if ($selected_post_data): 
                        $comments = getComments($selected_post_data['filename']);
                    ?>
                        <div class="article-detail">
                            <a href="?section=articulos" class="back-btn">← Volver a artículos</a>
                            
                            <?php if (!empty($selected_post_data['image'])): ?>
                                <img src="<?php echo htmlspecialchars($selected_post_data['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($selected_post_data['title']); ?>" 
                                     class="article-detail-img">
                            <?php endif; ?>
                            
                            <h2 class="article-detail-title"><?php echo htmlspecialchars($selected_post_data['title']); ?></h2>
                            
                            <div class="article-meta">
                                <span> <?php echo htmlspecialchars($selected_post_data['author']); ?></span>
                                <span><?php echo date('d/m/Y', strtotime($selected_post_data['created_at'])); ?></span>
                                <span><?php echo htmlspecialchars($selected_post_data['category']); ?></span>
                            </div>
                            
                            <div class="article-content">
                                <?php echo nl2br(htmlspecialchars($selected_post_data['content'])); ?>
                            </div>
                            
                            <!-- Sistema de likes -->
                            <?php if (isset($_SESSION['user'])): ?>
                                <div class="article-interactions">
                                    <form method="POST" class="like-form">
                                        <input type="hidden" name="post_id" value="<?php echo $selected_post_data['filename']; ?>">
                                        <button type="submit" name="like_post" class="like-btn">
                                            ❤️ <span class="like-count"><?php echo $selected_post_data['likes'] ?? 0; ?></span> Me gusta
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Sección de comentarios -->
                            <div class="comments-section">
                                <h3>Comentarios (<?php echo count($comments); ?>)</h3>
                                
                                <?php if (isset($_SESSION['user'])): ?>
                                    <form method="POST" class="comment-form">
                                        <input type="hidden" name="post_id" value="<?php echo $selected_post_data['filename']; ?>">
                                        <textarea name="comment" placeholder="Escribe tu comentario..." required></textarea>
                                        <button type="submit" name="add_comment" class="btn">Comentar</button>
                                    </form>
                                <?php else: ?>
                                    <p class="login-to-comment">Inicia sesión para comentar</p>
                                <?php endif; ?>
                                
                                <div class="comments-list">
                                    <?php foreach ($comments as $comment): ?>
                                        <div class="comment">
                                            <div class="comment-header">
                                                <span class="comment-user"> <?php echo htmlspecialchars($comment['user']); ?></span>
                                                <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['date'])); ?></span>
                                            </div>
                                            <p class="comment-text"><?php echo htmlspecialchars($comment['comment']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Lista de artículos -->
                    <div class="posts-grid">
                        <?php foreach ($posts as $post): ?>
                            <div class="post-card">
                                <?php if (!empty($post['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php endif; ?>
                                <div class="post-content">
                                    <span class="post-category"><?php echo htmlspecialchars($post['category']); ?></span>
                                    <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <p class="post-excerpt">
                                        <?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?>
                                    </p>
                                    <div class="post-meta">
                                        <span><?php echo htmlspecialchars($post['author']); ?></span>
                                        <span>❤️ <?php echo $post['likes'] ?? 0; ?></span>
                                    </div>
                                    <a href="?section=articulos&post=<?php echo urlencode($post['filename']); ?>" class="read-more-btn">
                                        Leer artículo completo
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Sección de Login -->
            <?php if ($current_section === 'login' && !isset($_SESSION['user'])): ?>
                <div class="login-container">
                    <div class="login-box">
                        <h2>Acceso para exploradores</h2>

                        <?php
                        if(isset($error)){
                            echo "<p style='color:red;'>$error</p>";
                        }
                        ?>

                        <form action="database/login.php" method="POST">
                            <div class="form-group">
                                <label>Usuario:</label>
                                <input type="text" name="username" required>
                            </div>

                            <div class="form-group">
                                <label>Contraseña:</label>
                                <input type="password" name="password" required>
                            </div>

                            <button type="submit" name="login" class="btn">Ingresar</button>
                        </form>

                    </div>
                </div>
            <?php endif; ?>

            <!-- Dashboard (solo para usuarios logueados) -->
            <?php if ($current_section === 'dashboard' && isset($_SESSION['user'])): ?>
                <h1>Dashboard del explorador</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="dashboard-grid">
                    <div class="dashboard-card create-post">
                        <h2>➕ Crear nuevo artículo</h2>
                        <form method="POST">
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Categoría:</label>
                                <select name="category">
                                    <option value="peces">Peces</option>
                                    <option value="mamiferos">Mamíferos</option>
                                    <option value="moluscos">Moluscos</option>
                                    <option value="crustaceos">Crustáceos</option>
                                    <option value="conservacion">Conservación</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Contenido:</label>
                                <textarea name="content" rows="5" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>URL de imagen:</label>
                                <input type="url" name="image">
                            </div>
                            
                            <button type="submit" name="create_post" class="btn">Publicar</button>
                        </form>
                    </div>
                    
                    <div class="dashboard-card manage-posts">
                        <h2> Mis artículos</h2>
                        <?php foreach ($posts as $post): ?>
                            <div class="post-item">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <div class="post-actions">
                                    <button onclick="editPost('<?php echo $post['filename']; ?>')" class="btn-small">Editar</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?php echo $post['filename']; ?>">
                                        <button type="submit" name="delete_post" class="btn-small danger" 
                                                onclick="return confirm('¿Eliminar esta publicación?')">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Formulario de edición (oculto por defecto) -->
                <div id="editForm" style="display: none;">
                    <h2> Editar artículo</h2>
                    <form method="POST">
                        <input type="hidden" name="post_id" id="edit_post_id">
                        <div class="form-group">
                            <label>Título:</label>
                            <input type="text" name="title" id="edit_title" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Categoría:</label>
                            <select name="category" id="edit_category">
                                <option value="peces">Peces</option>
                                <option value="mamiferos">Mamíferos</option>
                                <option value="moluscos">Moluscos</option>
                                <option value="crustaceos">Crustáceos</option>
                                <option value="conservacion">Conservación</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Contenido:</label>
                            <textarea name="content" id="edit_content" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>URL de imagen:</label>
                            <input type="url" name="image" id="edit_image">
                        </div>
                        
                        <button type="submit" name="update_post" class="btn">Actualizar</button>
                        <button type="button" onclick="hideEditForm()" class="btn-secondary">Cancelar</button>
                    </form>
                </div>
            // Seccion de registro
            <?php endif; ?>
            <?php if ($current_section === 'register'): ?>
            <div class="login-container">
                <div class="login-box">
                    <h2>Crear cuenta</h2>

                    <form action="../database/guardado.php" method="POST">

                        <div class="form-group">
                            <label>Usuario:</label>
                            <input type="text" name="user" required>
                        </div>

                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label>Contraseña:</label>
                            <input type="password" name="password" required>
                        </div>

                        <button type="submit" class="btn">Registrarse</button>

                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo SITE_NAME; ?></h3>
                <p>Explorando y protegiendo nuestros océanos</p>
            </div>
            <div class="footer-section">
                <h3>Enlaces rápidos</h3>
                <a href="?section=galeria">Galería</a>
                <a href="?section=watch">Watch</a>
                <a href="?section=noticias">Noticias</a>
                <a href="?section=articulos">Artículos</a>
            </div>
            <div class="footer-section">
                <h3>Síguenos</h3>
                <div class="social-links">
                    
                    <span>📷 Instagram</span>
                    
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Exploradores del Mar - Todos los derechos reservados</p>
        </div>
    </footer>

    <script>
        // Variables para el carrusel
        let slideIndex = 1;
        
        function moveSlide(n) {
            showSlide(slideIndex += n);
        }
        
        function currentSlide(n) {
            showSlide(slideIndex = n);
        }
        
        function showSlide(n) {
            const slides = document.getElementsByClassName("carousel-slide");
            const dots = document.getElementsByClassName("dot");
            
            if (n > slides.length) { slideIndex = 1; }
            if (n < 1) { slideIndex = slides.length; }
            
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            
            for (let i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " active";
        }
        
        // Auto-play del carrusel
        if (document.getElementsByClassName("carousel-slide").length > 0) {
            showSlide(slideIndex);
            setInterval(() => moveSlide(1), 5000);
        }
        
        // Función para filtrar galería
        function filterGallery() {
            const category = document.getElementById('categoryFilter').value;
            const species = document.getElementById('speciesFilter').value;
            const items = document.getElementsByClassName('gallery-item');
            
            for (let item of items) {
                let show = true;
                if (category && item.dataset.category !== category) {
                    show = false;
                }
                if (species && item.dataset.species !== species) {
                    show = false;
                }
                item.style.display = show ? 'block' : 'none';
            }
        }
        
        // Funciones para editar posts
        function editPost(filename) {
            fetch('get_post.php?file=' + filename)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editForm').style.display = 'block';
                    document.getElementById('edit_post_id').value = filename;
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_category').value = data.category;
                    document.getElementById('edit_content').value = data.content;
                    document.getElementById('edit_image').value = data.image || '';
                });
        }
        
        function hideEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>