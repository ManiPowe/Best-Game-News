-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 17 2026 г., 12:21
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `news_id` int NOT NULL,
  `user_id` int NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `likes` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `news_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `news` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` int NOT NULL,
  `views` int DEFAULT '0',
  `likes` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `news` (`id`, `title`, `content`, `image`, `author_id`, `views`, `likes`, `created_at`) VALUES
(1, 'DOTA 2: Новый ивент', 'В игре DOTA 2 стартовал новый ивент, приуроченный коллаборацией DOTA 2 X Monster Hunter!', '/assets/Media/Photo/dota2.png', 1, 0, 0, '2026-06-17 08:25:13'),
(2, 'Atomic Heart: DLC', 'Mundfish показала парочку скриншотов грядущего дополнения DLC для Atomic Heart', '/assets/Media/Photo/atomic.jpg', 1, 0, 0, '2026-06-17 08:25:13'),
(3, 'Call of Duty', 'Вышел новый трейлер MWIII', '/assets/Media/Photo/Calofduty.jpg', 1, 0, 0, '2026-06-17 08:25:13');

CREATE TABLE `news_likes` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `news_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `profile_reviews` (
  `id` int NOT NULL,
  `author_id` int NOT NULL,
  `target_user_id` int NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `profile_reviews` (`id`, `author_id`, `target_user_id`, `text`, `created_at`) VALUES
(1, 1, 3, 'Ты хуево играешь в роблокс', '2026-06-17 08:08:45'),
(2, 2, 3, 'ManiPowe? Ебанный пиздабол, ты ахуенно играешь в роблокс', '2026-06-17 08:11:26');

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'assets/Media/Photo/man.png',
  `role` enum('user','creator','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `posts_count` int DEFAULT '0',
  `comments_count` int DEFAULT '0',
  `top_liked_comment` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `login`, `password`, `name`, `email`, `phone`, `bio`, `avatar`, `role`, `posts_count`, `comments_count`, `top_liked_comment`, `created_at`) VALUES
(1, 'ManiPowe?', '$2y$10$CoXeJFrQb8mSOUK4zv0oaeARwFsPHRW28pUWi6O8Kx5ntqNuObM6q', 'Дмитрий', 'dmitri_guba@bk.ru', '+79020954393', NULL, 'assets/Media/avatars/avatar_6a3255e1be8a7_abbc3f3e.png', 'user', 0, 0, 0, '2026-06-15 14:53:04'),
(2, 'ManiPower', '$2y$10$1RCP6d8RQKUECW6wmd8aFOPRgbs0ypkMDc61FCP4G94MhKx2RhAXW', 'Александра', 'dmitri_guba@hotmail.com', '+79020954393', 'Я гей', 'assets/Media/avatars/avatar_6a324fd1afc4d_ae055a09.jpg', 'user', 0, 0, 0, '2026-06-15 14:54:53'),
(3, 'Хуесос 228', '$2y$10$lWYcftjwaNw8xlH8UoS6Ie1SKdrbcV0eyyqEpLBfII5ZhsISMZQQ2', 'Илья', 'Kaizer907@bk.ru', '+79023149745', '', 'assets/Media/avatars/avatar_6a32559e296b8_9f9d6d08.jpg', 'creator', 0, 0, 0, '2026-06-17 07:45:17');

ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

ALTER TABLE `news_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

ALTER TABLE `profile_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `target_user_id` (`target_user_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `news_likes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `profile_reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `news_likes`
  ADD CONSTRAINT `news_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_likes_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

ALTER TABLE `profile_reviews`
  ADD CONSTRAINT `profile_reviews_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profile_reviews_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
