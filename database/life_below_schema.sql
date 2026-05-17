-- ========================================================
-- BD Schema - LIFE BELOW
-- Script de Creación de Tablas Completo para phpMyAdmin (IONOS / Localhost)
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tabla: usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `fecha_de_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `rol` VARCHAR(25) NOT NULL DEFAULT 'usuario',
  `foto` VARCHAR(255) DEFAULT NULL,
  `foto_pendiente` VARCHAR(255) DEFAULT NULL,
  `estado_foto` ENUM('aprobada','pendiente','rechazada') NOT NULL DEFAULT 'aprobada',
  `reset_token` VARCHAR(50) DEFAULT NULL,
  `reset_token_exp` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2. Tabla: publicaciones
DROP TABLE IF EXISTS `publicaciones`;
CREATE TABLE `publicaciones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `categoria` VARCHAR(100) NOT NULL,
  `imagen` VARCHAR(255) DEFAULT NULL,
  `contenido` TEXT NOT NULL,
  `id_autor` INT(11) NOT NULL,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `estado` ENUM('publicado','borrador') NOT NULL DEFAULT 'publicado',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_publicaciones_autor` FOREIGN KEY (`id_autor`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 3. Tabla: comentarios
DROP TABLE IF EXISTS `comentarios`;
CREATE TABLE `comentarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_publicacion` INT(11) NOT NULL,
  `id_usuario` INT(11) NOT NULL,
  `comentario` TEXT NOT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_comentarios_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comentarios_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 4. Tabla: likes
DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_publicacion` INT(11) NOT NULL,
  `id_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_like_unico` (`id_publicacion`, `id_usuario`),
  CONSTRAINT `fk_likes_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_likes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 5. Tabla: videos
DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `video_url` VARCHAR(255) NOT NULL,
  `id_autor` INT(11) NOT NULL,
  `related_publicacion_id` INT(11) DEFAULT NULL,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_videos_autor` FOREIGN KEY (`id_autor`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_videos_publicacion` FOREIGN KEY (`related_publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 6. Tabla: video_categorias
DROP TABLE IF EXISTS `video_categorias`;
CREATE TABLE `video_categorias` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `video_id` INT(11) NOT NULL,
  `categoria` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_video_categoria` (`video_id`, `categoria`),
  CONSTRAINT `fk_video_categorias_video` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 7. Tabla: comentarios_videos
DROP TABLE IF EXISTS `comentarios_videos`;
CREATE TABLE `comentarios_videos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `video_id` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `contenido` TEXT NOT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_comentarios_videos_video` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comentarios_videos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 8. Tabla: video_views
DROP TABLE IF EXISTS `video_views`;
CREATE TABLE `video_views` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `video_id` INT(11) NOT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_video_views_usuario` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_video_views_video` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
