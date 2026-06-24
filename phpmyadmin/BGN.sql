-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 24 2026 г., 06:36
-- Версия сервера: 5.6.51
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `BGN`
--

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `likes` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `news_id`, `user_id`, `text`, `likes`, `created_at`) VALUES
(1, 11, 2, 'фыва', 0, '2026-06-22 11:50:22'),
(3, 11, 2, 'Имба', 0, '2026-06-22 11:59:41'),
(5, 14, 1, 'ыфпв', 0, '2026-06-22 21:59:41'),
(6, 14, 1, 'фыа', 0, '2026-06-22 21:59:42'),
(7, 14, 1, 'фыа', 0, '2026-06-22 21:59:43'),
(8, 14, 1, 'фыа', 0, '2026-06-22 21:59:44'),
(9, 14, 1, 'фыа', 0, '2026-06-22 21:59:46'),
(10, 14, 1, 'фаы', 0, '2026-06-22 21:59:47'),
(11, 14, 1, 'фыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфафыаыфвафафыафыафыафыафыафыаыфа', 0, '2026-06-22 21:59:57'),
(12, 17, 2, 'Я дотер, а мой папа шахматист, доказанно!!!!!!', 0, '2026-06-23 23:53:52');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `news_id`, `created_at`) VALUES
(6, 1, 11, '2026-06-22 21:12:29'),
(10, 2, 11, '2026-06-23 00:17:31'),
(17, 2, 14, '2026-06-23 19:19:03'),
(24, 2, 15, '2026-06-23 19:26:02'),
(138, 2, 16, '2026-06-23 20:53:46'),
(140, 2, 17, '2026-06-23 23:53:39');

-- --------------------------------------------------------

--
-- Структура таблицы `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `games`
--

INSERT INTO `games` (`id`, `name`, `slug`, `icon`, `created_at`) VALUES
(1, 'DOTA 2', 'dota-2', '/assets/Media/ico/icons8-дота-2.svg', '2026-06-18 15:38:02'),
(2, 'Counter-Strike 2', 'counter-strike-2', '/assets/Media/ico/icons8-counter-strike.svg', '2026-06-18 15:38:02'),
(3, 'Valorant', 'valorant', '/assets/Media/ico/icons8-valorant.svg', '2026-06-18 15:38:02'),
(4, 'Apex Legends', 'apex-legends', '/assets/Media/ico/icons8-riot-games.svg', '2026-06-18 15:38:02'),
(5, 'Fortnite', 'fortnite', '/assets/Media/ico/icons8-fortnite.svg', '2026-06-18 15:38:02'),
(6, 'Call of Duty', 'call-of-duty', '/assets/Media/ico/icons8-call-of-duty-black-ops-3.svg', '2026-06-18 15:38:02'),
(7, 'League of Legends', 'league-of-legends', '/assets/Media/ico/icons8-адский-дракон-league-of-legends.svg', '2026-06-18 15:38:02'),
(8, 'Overwatch 2', 'overwatch-2', '/assets/Media/ico/icons8-overwatch.svg', '2026-06-18 15:38:02'),
(9, 'Genshin Impact', 'genshin-impact', '/assets/Media/ico/icons8-genshin-impact-logo.svg', '2026-06-18 15:38:02'),
(10, 'World of Warcraft', 'world-of-warcraft', '/assets/Media/ico/icons8-world-of-warcraft.svg', '2026-06-18 15:38:02'),
(11, 'Among Us', 'among-us', '/assets/Media/ico/icons8-among-us.svg', '2026-06-18 15:38:02'),
(12, 'Minecraft', 'minecraft', '/assets/Media/ico/icons8-куб-травы-из-minecraft.svg', '2026-06-18 15:38:02'),
(13, 'Grand Theft Auto V', 'gta-v', '/assets/Media/ico/icons8-grand-theft-auto-v.svg', '2026-06-18 15:38:02'),
(14, 'Red Dead Redemption 2', 'red-dead-redemption-2', '/assets/Media/ico/red-dead-redemption-2-wordmark-light.svg', '2026-06-18 15:38:02'),
(15, 'The Witcher 3', 'the-witcher-3', '/assets/Media/ico/icons8-ведьмак-2.svg', '2026-06-18 15:38:02');

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `news_id`, `created_at`) VALUES
(10, 2, 14, '2026-06-23 22:19:03'),
(14, 2, 16, '2026-06-23 22:20:43'),
(20, 2, 15, '2026-06-23 22:26:02'),
(96, 1, 14, '2026-06-23 22:56:47'),
(97, 1, 15, '2026-06-23 22:56:48');

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `tags` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','pending','published') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `is_featured` tinyint(1) DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` enum('games','news','articles','videos') COLLATE utf8mb4_unicode_ci DEFAULT 'news',
  `game_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `views` int(11) DEFAULT '0',
  `likes_count` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `short_description`, `tags`, `status`, `is_featured`, `image`, `category`, `game_id`, `author_id`, `views`, `likes_count`, `created_at`, `updated_at`) VALUES
(11, 'Платные услуги', 'fasfasfasfasf', 'asfasfasdfsa', 'asfasfasfasf', 'published', 0, 'assets/Media/news/news_6a35161b596f4_ded59c00.png', 'games', 4, 2, 31, 2, '2026-06-19 10:12:43', '2026-06-24 02:19:05'),
(14, 'Противодействие коррупции', 'фаыфыа', 'фыа', 'фыа', 'published', 0, NULL, 'news', NULL, 1, 6, 1, '2026-06-22 21:59:15', '2026-06-24 02:35:23'),
(15, 'asd', 'asd', 'asd', 'asd', 'published', 0, 'assets/Media/news/news_6a39d0819ff2c_aa67b837.jpg', 'games', 6, 2, 3, 1, '2026-06-23 00:17:05', '2026-06-23 20:46:31'),
(16, 'Платные услуги', 'щддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщо', 'жщддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщощддлоьзлрзщдорхзщшлорзшозшщозщшозщо', 'фвы', 'published', 0, 'assets/Media/news/news_6a3ad36707e67_dea5e8e4.jpg', 'news', NULL, 2, 4, 1, '2026-06-23 18:41:43', '2026-06-24 02:17:32'),
(17, 'Хуй', 'У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами) У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)', 'У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)', 'У игроков DOTA 2 член меньше чем у шахматистов, (доказано врачами)', 'published', 0, 'assets/Media/news/news_6a3b1c555d774_8afcdaa0.png', 'games', 1, 2, 2, 1, '2026-06-23 23:52:53', '2026-06-24 02:17:53');

-- --------------------------------------------------------

--
-- Структура таблицы `news_likes`
--

CREATE TABLE `news_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `news_likes`
--

INSERT INTO `news_likes` (`id`, `user_id`, `news_id`, `created_at`) VALUES
(4, 2, 11, '2026-06-22 21:11:08'),
(6, 1, 11, '2026-06-22 21:12:29'),
(10, 2, 14, '2026-06-23 00:15:48'),
(11, 2, 15, '2026-06-23 00:17:29'),
(32, 2, 16, '2026-06-23 20:53:46'),
(34, 2, 17, '2026-06-23 23:53:39');

-- --------------------------------------------------------

--
-- Структура таблицы `news_views`
--

CREATE TABLE `news_views` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `viewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `news_views`
--

INSERT INTO `news_views` (`id`, `news_id`, `session_id`, `user_id`, `ip_address`, `viewed_at`) VALUES
(2, 11, 'fpgfbar91sdq9atf673s69du5f76kdcg', NULL, '127.0.0.1', '2026-06-22 11:34:29'),
(3, 11, '2csvfn71j5cb903ki6jtimjjihs8tlmi', 2, '127.0.0.1', '2026-06-22 11:36:35'),
(5, 11, 'eu746jmqor3etefu5p9qdk49ki09107f', 1, '127.0.0.1', '2026-06-22 21:12:27'),
(7, 14, 'eu746jmqor3etefu5p9qdk49ki09107f', 1, '127.0.0.1', '2026-06-22 21:59:32'),
(8, 14, 'g4hf860n18preoes9jr8nai9a9755iki', 2, '127.0.0.1', '2026-06-23 00:15:47'),
(9, 15, 'g4hf860n18preoes9jr8nai9a9755iki', 2, '127.0.0.1', '2026-06-23 00:17:27'),
(10, 11, 'g4hf860n18preoes9jr8nai9a9755iki', 2, '127.0.0.1', '2026-06-23 00:17:30'),
(11, 11, 'j7qfmiccc8cjebjb7j8va4cjcdenbian', 2, '127.0.0.1', '2026-06-23 17:08:27'),
(12, 15, 'j7qfmiccc8cjebjb7j8va4cjcdenbian', 2, '127.0.0.1', '2026-06-23 18:32:41'),
(13, 14, 'j7qfmiccc8cjebjb7j8va4cjcdenbian', 2, '127.0.0.1', '2026-06-23 18:38:14'),
(14, 16, 'j7qfmiccc8cjebjb7j8va4cjcdenbian', 2, '127.0.0.1', '2026-06-23 18:46:42'),
(15, 15, 'io6ebjq4lcceuvlv4l89ees84k3o63ff', 1, '127.0.0.1', '2026-06-23 19:26:23'),
(16, 16, 'io6ebjq4lcceuvlv4l89ees84k3o63ff', 1, '127.0.0.1', '2026-06-23 19:28:25'),
(17, 14, 'io6ebjq4lcceuvlv4l89ees84k3o63ff', 1, '127.0.0.1', '2026-06-23 19:38:14'),
(18, 16, '7r9tcaaag01o56hmlo288rn3unocuv1s', 2, '127.0.0.1', '2026-06-23 20:53:42'),
(19, 14, '7r9tcaaag01o56hmlo288rn3unocuv1s', 2, '127.0.0.1', '2026-06-23 20:54:38'),
(20, 17, '7r9tcaaag01o56hmlo288rn3unocuv1s', 2, '127.0.0.1', '2026-06-23 23:53:33'),
(21, 16, 'bv4qbf2lujov51ifbf9m11q6vk2g0sft', 6, '127.0.0.1', '2026-06-24 02:17:32'),
(22, 17, 'bv4qbf2lujov51ifbf9m11q6vk2g0sft', 6, '127.0.0.1', '2026-06-24 02:17:40'),
(23, 11, 'bv4qbf2lujov51ifbf9m11q6vk2g0sft', 6, '127.0.0.1', '2026-06-24 02:19:05'),
(24, 14, 'bv4qbf2lujov51ifbf9m11q6vk2g0sft', 6, '127.0.0.1', '2026-06-24 02:35:23');

-- --------------------------------------------------------

--
-- Структура таблицы `profile_reviews`
--

CREATE TABLE `profile_reviews` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `target_user_id` int(11) NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `profile_reviews`
--

INSERT INTO `profile_reviews` (`id`, `author_id`, `target_user_id`, `text`, `created_at`) VALUES
(1, 2, 1, 'Еблан еще тот', '2026-06-23 19:13:22'),
(2, 6, 1, 'аааа', '2026-06-24 02:35:16');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `custom_background` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/Media/Photo/man.png',
  `role` enum('user','creator','moderator','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `posts_count` int(11) DEFAULT '0',
  `comments_count` int(11) DEFAULT '0',
  `top_liked_comment` int(11) DEFAULT '0',
  `total_likes` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `email`, `phone`, `bio`, `custom_background`, `avatar`, `role`, `posts_count`, `comments_count`, `top_liked_comment`, `total_likes`, `created_at`, `last_activity`) VALUES
(1, 'WomiPowe', '$2y$10$CoXeJFrQb8mSOUK4zv0oaeARwFsPHRW28pUWi6O8Kx5ntqNuObM6q', 'Дмитрий', 'dmitri_guba@bk.ru', '+79020954393', 'фвы', NULL, 'assets/Media/avatars/avatar_6a340a8a3fcca_4fba8841.png', 'creator', 1, 8, 0, 0, '2026-06-15 14:53:04', NULL),
(2, 'ManiPower', '$2y$10$1RCP6d8RQKUECW6wmd8aFOPRgbs0ypkMDc61FCP4G94MhKx2RhAXW', 'Александра', 'dmitri_guba@hotmail.com', '+79020954393', 'ждфывопэжлфыавопэжлфавыопрэжлфыоваэжлрпофвэждларпопэжлфваорэжлываоэжрлоываэжлроэжылваорэжлфваоьрэждлыовфаэржлофваэжлрофэжвлаорпэждлфвоарэжлофваэжлро', NULL, 'assets/Media/avatars/avatar_6a3ac3a25359d_9e85036b.jpg', 'creator', 1, 8, 0, 0, '2026-06-15 14:54:53', NULL),
(3, 'Ka1zeR', '$2y$10$lWYcftjwaNw8xlH8UoS6Ie1SKdrbcV0eyyqEpLBfII5ZhsISMZQQ2', 'Илья', 'Kaizer907@bk.ru', '+79023149745', '', NULL, 'assets/Media/avatars/avatar_6a32559e296b8_9f9d6d08.jpg', 'moderator', 0, 0, 0, 0, '2026-06-17 07:45:17', NULL),
(4, '13412412412', '$2y$10$BH4cDik4Ow7dLsey5Xjjvelo24jXb/Ok2EauMJBeQryefMBESdP4K', '124124124214', 'K.a.1.z.e.r@yandex.ru', '+79375348986', '', NULL, 'assets/Media/avatars/avatar_6a3438b15c86d_e6ca4f96.png', 'user', 0, 0, 0, 0, '2026-06-17 23:23:04', NULL),
(5, 'admin', 'admin', '', 'admin890@bk.ru', '8(902)095 43 93', 'Я Модератор сайта BGN', NULL, 'assets/Media/Photo/man.png', 'user', 0, 0, 0, 0, '2026-06-23 20:39:36', NULL),
(6, 'admin123', '$2y$10$6mtXglje8fd2qDMKDHify.t5m9v3yjOGEn3yub5H7wLBpaaDewCxC', 'Дмитрий', 'Kaizer907@aa.ru', '+79020954393', 'Модератор и админ сайта BGN', NULL, 'assets/Media/avatars/avatar_6a3af0593d720_685860ac.png', 'admin', 0, 0, 0, 0, '2026-06-23 20:42:20', '2026-06-24 06:33:55');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Индексы таблицы `news_likes`
--
ALTER TABLE `news_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `news_views`
--
ALTER TABLE `news_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_view` (`news_id`,`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `profile_reviews`
--
ALTER TABLE `profile_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `target_user_id` (`target_user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT для таблицы `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `news_likes`
--
ALTER TABLE `news_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблицы `news_views`
--
ALTER TABLE `news_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `profile_reviews`
--
ALTER TABLE `profile_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `news_likes`
--
ALTER TABLE `news_likes`
  ADD CONSTRAINT `news_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_likes_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news_views`
--
ALTER TABLE `news_views`
  ADD CONSTRAINT `news_views_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `profile_reviews`
--
ALTER TABLE `profile_reviews`
  ADD CONSTRAINT `profile_reviews_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profile_reviews_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
