-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 26 2026 г., 04:20
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
  `likes_count` int(11) DEFAULT '0',
  `likes` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `news_id`, `user_id`, `text`, `likes_count`, `likes`, `created_at`) VALUES
(100, 100, 2, 'Наконец-то TI снова станет грандиозным событием! Жду не дождусь финала в Копенгагене!', 12, 0, '2026-06-20 08:15:00'),
(101, 100, 1, '40 лямов - это конечно круто, но почему до сих пор нет прямых трансляций на русском?', 1, 0, '2026-06-20 09:30:00'),
(102, 100, 6, 'Team Spirit в этом году просто машина! Ставлю на них все деньги!', 3, 0, '2026-06-20 11:00:00'),
(103, 101, 1, 'PA опять nerfнули... Когда уже перестанут портить моего любимого героя?', 1, 0, '2026-06-21 12:00:00'),
(104, 101, 2, 'Наконец-то Anti-Mage получил buff! Время фармить и тащить!', 9, 0, '2026-06-21 13:30:00'),
(105, 102, 6, 'Team Spirit покажет класс! Россия вперед! 🇷🇺', 3, 0, '2026-06-22 07:00:00'),
(106, 102, 1, 'Heroic сильны, но Spirit на пике формы. Буду болеть за наших!', 1, 0, '2026-06-22 08:30:00'),
(107, 103, 2, 'AK-47 Cyber Force просто огонь! Уже выбил, красота нереальная!', 18, 0, '2026-06-19 14:00:00'),
(108, 103, 6, '150 рублей за кейс? Это грабеж! Лучше подожду пока подешевеет.', 1, 0, '2026-06-19 15:00:00'),
(109, 104, 1, 'Neon выглядит просто невероятно! Наконец-то быстрый агент, люблю спринтеров!', 1, 0, '2026-06-23 09:00:00'),
(110, 104, 2, 'Еще один дуэлянт... Когда уже нормального контроллера добавят?', 5, 0, '2026-06-23 10:00:00'),
(111, 106, 6, 'Apex каждый сезон радует! Catalyst выглядит имбово, надо тестить!', 1, 0, '2026-06-24 06:00:00'),
(112, 108, 1, 'Minecraft жив и процветает! 1.21 выглядит потрясающе, особенно новый mob Breeze.', 1, 0, '2026-06-22 10:00:00'),
(113, 108, 2, 'Наконец-то автоматический крафт! Сколько можно было ждать эту фичу?', 13, 0, '2026-06-22 11:00:00'),
(114, 109, 6, 'GTA VI в 2027?! Это же так долго ждать... Но скриншоты просто космос!', 3, 0, '2026-06-23 15:00:00'),
(115, 109, 1, 'Vice City возвращается! Ностальгия по GTA Vice City накрывает с головой.', 1, 0, '2026-06-23 16:00:00'),
(116, 109, 2, 'Lucia - первая женская протагонистка в 3D вселенной! Это историческое событие!', 19, 0, '2026-06-23 17:00:00'),
(117, 110, 6, 'Ведьмак 4 с Цири в главной роли - это мечта! CDPR знают как сделать RPG!', 3, 0, '2026-06-21 18:00:00'),
(200, 106, 6, 'Apex каждый сезон радует! Catalyst выглядит имбово, надо тестить!', 1, 0, '2026-06-24 15:30:51'),
(201, 207, 7, 'Я СПИЛСЯ ПОСЛЕ ТОГО КАК ПРОЧЕЛ ЭТО, ЛОСЬ', 0, 0, '2026-06-25 15:03:39');

-- --------------------------------------------------------

--
-- Структура таблицы `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `comment_likes`
--

INSERT INTO `comment_likes` (`id`, `comment_id`, `user_id`, `created_at`) VALUES
(1, 100, 6, '2026-06-20 11:20:00'),
(2, 100, 1, '2026-06-20 11:25:00'),
(3, 102, 1, '2026-06-20 14:10:00'),
(4, 102, 2, '2026-06-20 14:15:00'),
(5, 105, 1, '2026-06-22 10:05:00'),
(6, 105, 2, '2026-06-22 10:10:00'),
(7, 107, 6, '2026-06-19 17:10:00'),
(8, 107, 1, '2026-06-19 17:20:00'),
(9, 114, 1, '2026-06-23 18:05:00'),
(10, 114, 2, '2026-06-23 18:10:00'),
(12, 117, 1, '2026-06-21 21:10:00'),
(13, 117, 2, '2026-06-21 21:15:00'),
(17, 111, 6, '2026-06-24 18:30:39'),
(18, 200, 6, '2026-06-24 18:30:53'),
(19, 114, 6, '2026-06-24 19:46:18'),
(20, 105, 6, '2026-06-24 19:46:19'),
(21, 117, 6, '2026-06-24 19:46:20'),
(22, 102, 6, '2026-06-24 19:46:21'),
(23, 108, 6, '2026-06-24 19:46:21'),
(25, 115, 6, '2026-06-25 16:03:11'),
(27, 109, 6, '2026-06-25 16:03:12'),
(28, 112, 6, '2026-06-25 16:03:13'),
(29, 106, 6, '2026-06-25 16:03:14'),
(30, 103, 6, '2026-06-25 16:03:15'),
(31, 101, 6, '2026-06-25 16:03:15');

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
(1, 1, 100, '2026-06-20 08:30:00'),
(2, 1, 109, '2026-06-23 15:30:00'),
(3, 1, 110, '2026-06-21 18:30:00'),
(4, 2, 102, '2026-06-22 07:30:00'),
(5, 2, 104, '2026-06-23 09:30:00'),
(6, 2, 106, '2026-06-24 06:30:00'),
(7, 6, 100, '2026-06-20 11:30:00'),
(8, 6, 108, '2026-06-22 10:30:00'),
(9, 6, 109, '2026-06-23 16:30:00'),
(18, 6, 106, '2026-06-24 15:36:45'),
(21, 6, 201, '2026-06-24 16:57:26'),
(22, 6, 200, '2026-06-24 16:57:30'),
(24, 2, 203, '2026-06-25 09:19:52'),
(26, 7, 207, '2026-06-25 15:03:17'),
(27, 6, 210, '2026-06-26 00:12:34');

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
  `featured_date` date DEFAULT NULL,
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

INSERT INTO `news` (`id`, `title`, `content`, `short_description`, `tags`, `status`, `is_featured`, `featured_date`, `image`, `category`, `game_id`, `author_id`, `views`, `likes_count`, `created_at`, `updated_at`) VALUES
(100, 'The International 2026: Призовой фонд превысил $40 миллионов', 'Организаторы чемпионата The International 2026 объявили о новом рекорде призового фонда. Благодаря продаже боевых пропусков и внутриигровых предметов, общая сумма призов превысила 40 миллионов долларов, что стало абсолютным рекордом в истории киберспорта.\r\n\r\nФинальный турнир пройдет в Копенгагене с 15 по 25 октября 2026 года. 20 лучших команд мира сойдутся в борьбе за главный трофей - Aegis of Champions.\r\n\r\nПо словам представителей Valve, интерес к DOTA 2 продолжает расти, а зрительская аудитория чемпионата обещает стать самой большой за всю историю турнира. Team Spirit, ставшие чемпионами TI10, снова считаются фаворитами.', 'Призовой фонд TI2026 побил все рекорды киберспорта', 'киберспорт, турнир, The International, DOTA 2', '', 1, NULL, 'assets/Media/news/news_6a3bf9c60bd13_3deb6ce3.jpg', 'games', 1, 6, 3, 3, '2026-06-20 07:30:00', '2026-06-24 15:37:42'),
(101, 'Патч 7.36b: Полная переработка меты в DOTA 2', 'Valve выпустила масштабное обновление 7.36b, которое кардинально меняет игровую мету. Разработчики переработали баланс более 50 героев, добавили новые предметы и изменили механику некоторых способностей.\n\nОсновные изменения:\n- Увеличена стоимость выкупа на 200 золота\n- Переработана система опыта\n- Добавлено 3 новых предмета: Harpoon, Staff of the Headmaster и Phoenix Feather\n- Nerf Phantom Assassin и Buff Anti-Mage\n- Изменена механика курьеров\n\nПрофессиональные игроки уже тестируют обновление на паблик матчах. Первые впечатления - игра стала более динамичной и зрелищной.', 'Масштабный баланс-патч меняет мету', 'патч, баланс, обновление, DOTA 2', 'published', 0, NULL, '/assets/Media/news/news_patch736.jpg', 'games', 1, 1, 2, 3, '2026-06-21 11:15:00', '2026-06-25 15:08:52'),
(102, 'CS2 Major Copenhagen: Финал Team Spirit vs Heroic', 'В захватывающем финале Copenhagen Major 2026 встретятся Team Spirit и Heroic. Российская команда показала великолепную игру на протяжении всего турнира, не проиграв ни одной карты в плей-офф.\r\n\r\nПолуфинальные матчи:\r\n- Team Spirit 2:0 FaZe Clan\r\n- Heroic 2:1 Natus Vincere\r\n\r\nФинал начнется сегодня в 20:00 по московскому времени. Призовой фонд турнира составляет $1,250,000.\r\n\r\nБукмекеры отдают небольшое преимущество Team Spirit с коэффициентом 1.75. Эксперты прогнозируют одну из самых зрелищных серий года.', 'Финал мейджора уже сегодня!', 'киберспорт, турнир, финал, CS2', '', 1, NULL, 'assets/Media/news/news_6a3bf9bcf3568_a732738e.jpg', 'games', 2, 6, 3, 3, '2026-06-22 06:00:00', '2026-06-24 15:37:32'),
(103, 'Новый кейс Revolution Collection уже в CS2', 'Valve добавила в CS2 новый кейс Revolution Collection с 17 скинами оружия и редким ножом-бабочкой. Особое внимание привлекают скины AK-47 \"Cyber Force\" и AWP \"Neon Rider\".\n\nШансы выпадения:\n- Арканное (редкое): 0.64%\n- Запретное: 3.2%\n- Тайное: 15.98%\n- Засекреченное: 79.92%\n\nЦена кейса на торговой площадке Steam сейчас составляет около 150 рублей. Аналитики предсказывают рост цены в ближайшие недели из-за высокого спроса.', 'Новые скины и нож-бабочка', 'кейс, скины, предметы, CS2', 'published', 0, NULL, '/assets/Media/news/news_cs2case.jpg', 'games', 2, 1, 0, 2, '2026-06-19 13:45:00', '2026-06-25 15:08:55'),
(104, 'Valorant: Riot Games представила 25-го агента Neon', 'Riot Games официально анонсировала 25-го агента в Valorant. Новый герой под кодовым именем \"Neon\" - это спринтер из Филиппин с электрическими способностями.\n\nСпособности Neon:\n- Q: Быстрый рывок с уроном врагам на пути\n- E: Статическое поле, замедляющее противников\n- C: Молниеносная граната с оглушением\n- X (Ultimate): Режим овердрайва с увеличенной скоростью и уроном\n\nАгент станет доступен для игры уже на следующей неделе после обновления 5.0. Профессиональные игроки уже называют Neon \"game-changer\" для соревновательной сцены.', 'Новый дуэлянт с молниеносными способностями', 'агент, обновление, персонаж, Valorant', 'published', 0, NULL, '/assets/Media/news/news_valorant.jpg', 'games', 3, 2, 1, 3, '2026-06-23 08:20:00', '2026-06-25 15:08:49'),
(105, 'VCT Masters Reykjavik: Team Liquid побеждают Sentinels', 'Team Liquid сенсационно обыграли действующих чемпионов Sentinels со счетом 3:2 в гранд-финале VCT Masters Reykjavik. Это первая крупная победа европейской команды над североамериканскими титанами.\r\n\r\nСчет по картам:\r\n- Haven: 13-11 (Liquid)\r\n- Bind: 9-13 (Sentinels)\r\n- Ascent: 13-8 (Liquid)\r\n- Split: 10-13 (Sentinels)\r\n- Icebox: 13-7 (Liquid)\r\n\r\nMVP турнира признан ScreaM с incredible K/D 1.47. Призовые Team Liquid составили $400,000.', 'Европейский триумф в Исландии', 'турнир, киберспорт, чемпионат, Valorant', '', 0, NULL, 'assets/Media/news/news_6a3bf9d54600a_58c2bdb2.jpg', 'games', 3, 6, 0, 0, '2026-06-18 16:30:00', '2026-06-24 15:37:57'),
(106, 'Apex Legends Season 18: Новый легенда Catalyst', 'Respawn Entertainment анонсировала начало 18 сезона Apex Legends под названием \"Resurrection\". Главные нововведения:\n\nНовая легенда - Catalyst (поддержка):\n- Пассивка: Ускоренное лечение\n- Q: Создание барьера из ferrofluid\n- Ultimate: Стена, замедляющая врагов\n\nИзменения карты:\n- World\'s Edge получает масштабный ремейк\n- Добавлены новые POI: Lava Siphon и The Core\n\nСтарт сезона - 1 июля 2026 года.', 'Масштабное обновление сезона', 'сезон, легенда, карта, Apex', 'published', 0, NULL, '/assets/Media/news/news_apex.jpg', 'games', 4, 1, 3, 3, '2026-06-24 05:00:00', '2026-06-25 15:08:45'),
(107, 'Fortnite Глава 4 Сезон 3: Джунгли захватывают остров', 'Epic Games запустила третий сезон четвертой главы Fortnite под названием \"Wilds\". Игроков ждут:\n\nНовые локации:\n- Rumble Ruins - древние храмы в центре карты\n- Shady Stilts - затопленная деревня\n- Jungle Biome занимает 40% карты\n\nОружие и предметы:\n- Возвращение Pump Shotgun\n- Новый SMG \"Jungle Drum\"\n- Kinetic Blade для ближнего боя\n\nBattle Pass включает скин Indiana Jones (бонусный). Цена - 950 V-Bucks.', 'Джунгли и сокровища', 'сезон, обновление, карта, Fortnite', 'published', 0, NULL, '/assets/Media/news/news_fortnite.jpg', 'games', 5, 2, 1, 0, '2026-06-20 12:00:00', '2026-06-25 15:08:54'),
(108, 'Minecraft 1.21: Дата выхода обновления Tricky Trials', 'Mojang объявила, что обновление 1.21 \"Tricky Trials\" выйдет 15 июля 2026 года. Патч добавит:\r\n\r\nНовые структуры:\r\n- Trial Chambers - процедурно генерируемые подземелья\r\n- Breeze спавнятся только в новых структурах\r\n\r\nПредметы:\r\n- Wind Charge (бросок с отталкиванием)\r\n- Mace (новое оружие ближнего боя)\r\n- Copper Bulb (освещение)\r\n- Crafter (автоматический крафт)\r\n\r\nТакже улучшена система испытаний и добавлены новые достижения.', 'Лето 2026 - время испытаний', 'обновление, версия, патч, Minecraft', '', 1, NULL, 'assets/Media/news/news_6a3bf9b243939_9d661b04.jpg', 'games', 12, 6, 1, 1, '2026-06-22 09:00:00', '2026-06-24 15:38:16'),
(109, 'GTA VI: Первые скриншоты и дата релиза', 'Rockstar Games наконец-то поделилась новыми деталями о GTA VI. Официальный релиз назначен на осень 2027 года для PlayStation 5 и Xbox Series X|S.\n\nЧто известно:\n- Действие происходит в Vice City (Майами) и окрестностях\n- Два играбельных персонажа: Lucia и Jason\n- Улучшенная физика и графика на RAGE engine\n- Онлайн режим GTA VI Online с самого запуска\n- Размер карты в 3 раза больше GTA V\n\nПредзаказы откроются в январе 2027 года.', 'Наконец-то официальные данные!', 'GTA 6, релиз, новости, Rockstar', 'published', 0, NULL, '/assets/Media/news/news_gta6.jpg', 'games', 13, 1, 4, 3, '2026-06-23 14:00:00', '2026-06-25 15:08:48'),
(110, 'The Witcher 4: CD Projekt RED показала геймплей', 'На The Game Awards 2026 студия CD Projekt RED представила 15 минут геймплея следующей части Ведьмака.\n\nОсновные фишки:\n- Новая героиня - Ciri (играбельный персонаж)\n- Открытый мир больше чем в Witcher 3 в 2 раза\n- Улучшенная боевая система с парированием\n- Магия играет большую роль\n- Выбор диалогов влияет на сюжет\n\nТехнические детали:\n- Движок Unreal Engine 5\n- Поддержка 4K 60fps на консолях\n- Ray Tracing из коробки\n\nРелиз запланирован на 2028 год.', 'Сирі возвращается!', 'Ведьмак, геймплей, RPG, CDPR', 'published', 0, NULL, '/assets/Media/news/news_witcher4.jpg', 'games', 15, 2, 1, 3, '2026-06-21 17:00:00', '2026-06-25 15:08:51'),
(111, 'RDR 2 PC: Обновление с улучшенной графикой', 'Rockstar выпустила крупное обновление для Red Dead Redemption 2 на PC с поддержкой новых технологий:\r\n\r\nЧто добавлено:\r\n- DLSS 3.0 от NVIDIA\r\n- FSR 3.0 от AMD\r\n- Улучшенные текстуры в 4K\r\n- Ray Tracing для теней и отражений\r\n- Поддержка Ultrawide мониторов 32:9\r\n\r\nИсправления:\r\n- Улучшена оптимизация\r\n- Уменьшено потребление VRAM\r\n- Исправлены вылеты на Windows 11\r\n\r\nОбновление весит 12 GB и доступно бесплатно.', 'Технологический апгрейд вестерна', 'обновление, графика, PC, RDR2', '', 0, NULL, 'assets/Media/news/news_6a3bf9ce5a370_c79d11d0.jpg', 'games', 14, 6, 0, 0, '2026-06-19 07:30:00', '2026-06-24 15:37:50'),
(200, 'Осторожно мошенники!', 'афывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфыватьафывабпхфыдвптьожыйлбвайптоьджфльыаптждэлфываптоьэжлдфывать', 'ж.юж.южэюжбюжжбжбюфажбфыжабфыжабфыжабфыжабфыжабфыжабжфываб', 'обновление, версия, патч, Minecraft', 'published', 0, NULL, 'assets/Media/news/news_6a3bfa0500919_7f4c9605.jpg', 'games', 11, 6, 2, 1, '2026-06-24 15:38:45', '2026-06-25 19:26:00'),
(201, 'Выездной прием граждан', 'Содержание *Содержание *Содержание *Содержание *Содержание *Содержание *Содержание *Содержание *Содержание *Содержание *Содержание *', 'Краткое описание (для превью)', 'Дота, говно, дота говно', 'published', 0, NULL, 'assets/Media/news/news_6a3c094bd1173_a406a11f.png', 'news', NULL, 6, 1, 1, '2026-06-24 16:43:55', '2026-06-25 19:26:02'),
(202, 'Противодействие коррупции', 'Содержание *Содержание *Содержание *Содержание *Содержание *', 'Краткое описание (для превью)Краткое описание (для превью)Краткое описание (для превью)Краткое описание (для превью)Краткое описание (для превью)', 'обновление, версия, патч, Minecraft', 'draft', 0, NULL, 'assets/Media/news/news_6a3c09c0154c4_1520b82e.png', 'games', 3, 6, 0, 0, '2026-06-24 16:45:52', '2026-06-24 16:45:59'),
(203, 'Платные услуги', 'Этот пост тестовый Этот пост тестовый Этот пост тестовый Этот пост тестовый Этот пост тестовый Этот пост тестовый Этот пост тестовый Этот пост тестовый Этот пост тестовыйЭтот пост тестовый Этот пост тестовыйЭтот пост тестовыйЭтот пост тестовыйЭтот пост тестовыйЭтот пост тестовыйЭтот пост тестовый', 'Этот пост тестовый', '', 'published', 0, NULL, NULL, 'articles', NULL, 6, 3, 1, '2026-06-25 08:27:03', '2026-06-25 15:16:08'),
(206, 'Осторожно мошенники!', 'Иди нахуй админ', 'Админ хуесос', 'Дота, говно, дота говно', 'draft', 0, NULL, 'assets/Media/news/news_6a3d0153750fb_e0839e1e.png', 'games', 4, 6, 0, 0, '2026-06-25 10:22:11', '2026-06-25 10:22:39'),
(207, 'как побеждать в дота 2 если ты любишь не пла', 'для избегания присутствия злого духа фантома необходимо закинуть его в бан, но не только в игре но и в своем разуме, запретить разговоры о нем в семье и в кругу семьи, поставить свечку за его упокой в церкви, обязательно обговорить с мамой о важности не произношения имени фантом лансер в доме, обозначить как харам. Идем дальше, все мы знаем как в кайф играется на спектре на бладсикере и остальных нн-ах но когда ты видишь эту страшную фигуру в игре... скажем... желание играть на \"особых\" персонажах отпадает, приходится пикать какую то скучную сильную фигуру(такие персонажи как: пудж, снайпер, алхимик, вичдоктор) но как же играть в этого пл-а не на крутых персонажах??? Ответ прост, берите лохов, НО МОЛИТЕСЬ НА СВОЮ ТРОЙКУ, ОТ НЕГО ЗАВИСИТ ВАША ПОБЕДА.( за тройку тоже можно поставить иконку). И последний аспект гайда, моральная поддержка, в первую очередь стоит обратиться в церковно приходскую церковь имени Ганса Ландерсона, далее обратитесь в клуб поддержки малого бизнеса, там вам помогут найти смысл... а в прочем, лучше не стоит, далее по поводу семьи обязательно обращаемся в центр по контролю диких животных, есть два исхода но оба они прекрасны для вас( игроков доты) либо вас заберут как скотину в загон(компьютер вам дадут) либо вашу семью, одни плюсы. \r\nТАК мы решили вашу проблему связанную с моральным недомоганием мозга, если вы это прочли, знрачит =у вас еще есть шанс на реабилитацию, желаю успехов в доте и мешьне встречь с иудой П-Л-О-М.', 'всех достал фантом лансер, в этом гайде мы научимся избегать его присутствия и играть против него на не контр пиках(контр пиков у него нет) и объясним куда податься в случае морального унижения из за пл-а, где найти поддержку и как преодолеть желание бросить жену  с детьми после катки с пл-ом.', 'ДОТА, ГАБЕН СЫН ШЛЮХИ, МОРАЛЬНАЯ ПОМОЩЬ, ОСОБОЕ РУКОВОДСТВО, ПОМОЩЬ БОЛЬНЫМ ДУШЕВНО', 'published', 1, NULL, 'assets/Media/news/news_6a3d427663291_c5e634b5.jpg', 'games', 1, 7, 1, 1, '2026-06-25 15:00:06', '2026-06-25 15:09:01'),
(208, 'АВЫЫВКАЕУЦ', 'ЯЧСФЫЯВЧСАСЯСЯЧЧЯс', 'ЯЯЧ СЧЯЧСЯЧЯС', 'ЯЧСЧЯЯЧС', 'draft', 0, NULL, NULL, 'games', 4, 7, 0, 0, '2026-06-25 15:04:24', '2026-06-25 15:04:54'),
(209, 'В игре Dota 2 вышло долгожданное обновление !', 'В игре Dota 2 вышло обновление коллаборация Monster Hunter! В игре Dota 2 вышло обновление коллаборация Monster Hunter! В игре Dota 2 вышло обновление коллаборация Monster Hunter! В игре Dota 2 вышло обновление коллаборация Monster Hunter! В игре Dota 2 вышло обновление коллаборация Monster Hunter! В игре Dota 2 вышло обновление коллаборация Monster Hunter! В игре Dota 2 вышло обновление коллаборация Monster Hunter! В игре Dota 2 вышло обновление коллаборация Monster Hunter!', 'В игре Dota 2 вышло обновление коллаборация Monster Hunter!', 'dota, dota 2, обновление, патч', 'published', 1, NULL, 'assets/Media/news/news_6a3d80afeb659_e9c3dda3.png', 'games', 1, 7, 2, 0, '2026-06-25 19:25:35', '2026-06-26 00:11:12'),
(210, 'Тестовая новость', 'Тестовая новость Тестовая новость Тестовая новость Тестовая новость', 'Тестовая новость Тестовая новость Тестовая новость', 'Тестовая новость', 'published', 0, NULL, 'assets/Media/news/news_6a3dc3e19241d_e9ad9574.jpg', 'news', NULL, 6, 0, 1, '2026-06-26 00:12:17', '2026-06-26 00:12:34'),
(211, 'Это тестовый пост', 'Это тестовый пост Это тестовый пост Это тестовый пост Это тестовый пост', 'Это тестовый пост', 'Это тестовый пост', 'draft', 0, NULL, 'assets/Media/news/news_6a3dc5d8c43ad_16059526.jpg', 'games', 6, 6, 0, 0, '2026-06-26 00:20:40', '2026-06-26 00:20:40'),
(212, 'Это тестовый пост', 'Это тестовый пост Это тестовый пост Это тестовый пост', 'Это тестовый пост Это тестовый пост', 'Это тестовый пост', 'pending', 0, NULL, 'assets/Media/news/news_6a3dc5f9a8d8e_f62250e2.jpg', 'games', 6, 6, 0, 0, '2026-06-26 00:21:13', '2026-06-26 00:21:13');

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
(1, 1, 100, '2026-06-20 08:00:00'),
(2, 2, 100, '2026-06-20 09:00:00'),
(3, 6, 100, '2026-06-20 10:00:00'),
(4, 1, 101, '2026-06-21 12:00:00'),
(5, 2, 101, '2026-06-21 13:00:00'),
(6, 6, 101, '2026-06-21 14:00:00'),
(7, 1, 102, '2026-06-22 07:00:00'),
(8, 2, 102, '2026-06-22 08:00:00'),
(9, 6, 102, '2026-06-22 09:00:00'),
(10, 1, 103, '2026-06-19 14:00:00'),
(11, 2, 103, '2026-06-19 15:00:00'),
(12, 1, 104, '2026-06-23 09:00:00'),
(13, 2, 104, '2026-06-23 10:00:00'),
(14, 6, 104, '2026-06-23 11:00:00'),
(15, 1, 106, '2026-06-24 06:00:00'),
(16, 2, 106, '2026-06-24 07:00:00'),
(18, 1, 109, '2026-06-23 15:00:00'),
(19, 2, 109, '2026-06-23 16:00:00'),
(20, 6, 109, '2026-06-23 17:00:00'),
(21, 1, 110, '2026-06-21 18:00:00'),
(22, 2, 110, '2026-06-21 19:00:00'),
(23, 6, 110, '2026-06-21 20:00:00'),
(29, 6, 106, '2026-06-24 15:36:45'),
(30, 6, 108, '2026-06-24 15:38:16'),
(35, 6, 201, '2026-06-24 16:57:23'),
(36, 6, 200, '2026-06-24 16:57:32'),
(38, 2, 203, '2026-06-25 09:19:52'),
(40, 7, 207, '2026-06-25 15:03:16'),
(41, 6, 210, '2026-06-26 00:12:34');

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
(1, 100, 'sess_ti_1', 1, '127.0.0.1', '2026-06-20 07:35:00'),
(2, 100, 'sess_ti_2', 2, '127.0.0.1', '2026-06-20 08:00:00'),
(3, 100, 'sess_ti_3', 6, '127.0.0.1', '2026-06-20 09:00:00'),
(4, 102, 'sess_cs_1', 1, '127.0.0.1', '2026-06-22 06:30:00'),
(5, 102, 'sess_cs_2', 2, '127.0.0.1', '2026-06-22 07:00:00'),
(6, 102, 'sess_cs_3', 6, '127.0.0.1', '2026-06-22 08:00:00'),
(7, 109, 'sess_gta_1', 1, '127.0.0.1', '2026-06-23 14:30:00'),
(8, 109, 'sess_gta_2', 2, '127.0.0.1', '2026-06-23 15:00:00'),
(9, 109, 'sess_gta_3', 6, '127.0.0.1', '2026-06-23 16:00:00'),
(11, 101, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 15:24:45'),
(12, 106, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 15:26:00'),
(13, 108, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 15:38:14'),
(14, 110, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 15:52:24'),
(17, 104, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 15:52:41'),
(19, 107, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 15:52:49'),
(21, 200, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 15:58:17'),
(22, 201, '38doevl9028o2eb6t9c7iv7entl4tgjv', 6, '127.0.0.1', '2026-06-24 16:53:52'),
(23, 200, '9l111ur653foiefarp3d8gg6hf6nu82i', 6, '127.0.0.1', '2026-06-25 07:09:53'),
(25, 109, '9l111ur653foiefarp3d8gg6hf6nu82i', 6, '127.0.0.1', '2026-06-25 08:08:16'),
(28, 106, 'vs4gud05qp8goprko0pfp29gs1jsfkv6', 6, '127.0.0.1', '2026-06-25 10:13:01'),
(30, 106, 'dgj5ps5fh50rvnu2vlinf4fff6l3s6ji', 6, '127.0.0.1', '2026-06-25 13:03:25'),
(31, 203, 'dgj5ps5fh50rvnu2vlinf4fff6l3s6ji', 6, '127.0.0.1', '2026-06-25 14:22:42'),
(33, 203, '0cnbtofdj06shkqdqmj4ej87efsf1hc8', NULL, '127.0.0.1', '2026-06-25 14:35:21'),
(35, 101, '0cnbtofdj06shkqdqmj4ej87efsf1hc8', NULL, '127.0.0.1', '2026-06-25 14:36:08'),
(36, 207, 't1457b6ns6p3s3q60m3fene2ion27adk', 7, '127.0.0.1', '2026-06-25 15:00:22'),
(37, 203, 't1457b6ns6p3s3q60m3fene2ion27adk', 7, '127.0.0.1', '2026-06-25 15:16:08'),
(38, 209, 't1457b6ns6p3s3q60m3fene2ion27adk', 7, '127.0.0.1', '2026-06-25 19:25:39'),
(41, 209, 'm212i86h6s02s2an4nmsq9p0pluc15fc', 6, '127.0.0.1', '2026-06-26 00:11:12');

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'post_approved', 'Ваша новость «Платные услуги» была опубликована!', 1, '2026-06-25 09:39:08'),
(2, 6, 'post_rejected', 'Ваша новость «Осторожно мошенники!» была отклонена.\n\nПричина: Не обзывайся э', 1, '2026-06-25 10:22:39'),
(3, 6, 'ticket_closed', 'Ваше обращение «КраткоеНаименованиеМО_ИТ_И_ИБ» было закрыто.', 1, '2026-06-25 10:57:16'),
(4, 4, 'ticket_reply', 'Получен ответ на ваше обращение «Помогите паже»:\n\nНе не не не', 1, '2026-06-25 11:03:36'),
(5, 7, 'post_approved', 'Ваша новость «как побеждать в дота 2 если ты любишь не пла» была опубликована!', 1, '2026-06-25 15:00:25'),
(6, 7, 'post_rejected', 'Ваша новость «АВЫЫВКАЕУЦ» была отклонена.\n\nПричина: ОТКЛАНЕНО, ТЫ ХУЙНЮ ПИШЕШЬ', 1, '2026-06-25 15:04:54'),
(7, 7, 'post_approved', 'Ваша новость «В игре Dota 2 вышло долгожданное обновление !» была опубликована!', 1, '2026-06-25 19:25:47'),
(8, 6, 'post_approved', 'Ваша новость «Тестовая новость» была опубликована!', 0, '2026-06-26 00:12:21');

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
(2, 6, 1, 'аааа', '2026-06-24 02:35:16'),
(3, 6, 2, 'фаы', '2026-06-24 12:03:57'),
(4, 2, 1, 'Отличный автор! Всегда интересные и актуальные новости. Особенно нравятся обзоры патчей DOTA 2.', '2026-06-21 07:00:00'),
(5, 6, 1, 'Хорошо пишет про киберспорт, ждем больше статей про турниры!', '2026-06-22 12:00:00'),
(6, 1, 2, 'Маня - топ! Её статьи про Valorant всегда на высоте. Спасибо за качественный контент!', '2026-06-23 09:00:00'),
(7, 6, 2, 'Согласен, отличные материалы по шутерам. Продолжай в том же духе!', '2026-06-23 15:00:00'),
(8, 1, 6, 'Админ делает огромную работу для сайта. Много полезных фич и быстрая модерация!', '2026-06-24 07:00:00'),
(9, 2, 6, 'Лучший модератор! Всегда справедливый и адекватный. Респект!', '2026-06-24 11:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `review_likes`
--

CREATE TABLE `review_likes` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `review_likes`
--

INSERT INTO `review_likes` (`id`, `review_id`, `user_id`, `created_at`) VALUES
(1, 1, 6, '2026-06-21 10:30:00'),
(2, 1, 1, '2026-06-21 11:00:00'),
(3, 2, 1, '2026-06-22 15:30:00'),
(4, 3, 6, '2026-06-23 12:30:00'),
(5, 3, 1, '2026-06-23 13:00:00'),
(6, 4, 1, '2026-06-23 18:30:00'),
(7, 5, 2, '2026-06-24 10:30:00'),
(8, 5, 1, '2026-06-24 11:00:00'),
(9, 6, 1, '2026-06-24 14:30:00'),
(10, 6, 2, '2026-06-24 15:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','closed','answered') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `subject`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, 'КраткоеНаименованиеМО_ИТ_И_ИБ', 'closed', '2026-06-25 10:42:00', '2026-06-25 10:57:16'),
(2, 4, 'Помогите паже', 'answered', '2026-06-25 11:03:07', '2026-06-25 11:03:36'),
(3, 7, 'Помогите паже', 'answered', '2026-06-25 15:05:52', '2026-06-25 15:06:55');

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `ticket_messages`
--

INSERT INTO `ticket_messages` (`id`, `ticket_id`, `user_id`, `message`, `is_admin`, `created_at`) VALUES
(1, 1, 6, 'Болит фывфвфыв', 0, '2026-06-25 10:42:00'),
(2, 1, 6, 'фывфывфывфыв', 0, '2026-06-25 10:56:54'),
(3, 2, 4, 'Помогите пажеПомогите пажеПомогите паже', 0, '2026-06-25 11:03:07'),
(4, 2, 6, 'Не не не не', 1, '2026-06-25 11:03:36'),
(5, 2, 4, 'Ну лан', 0, '2026-06-25 11:06:43'),
(6, 3, 7, 'ЗАБРОКОВАЛИ ПОСТ НА ЩИТПОСТИНГ ШО МНЕ ДЕЛАТЬ Я В МЕСТЕ ПО ОТЛОВУ СКОТИНЫ', 0, '2026-06-25 15:05:52'),
(7, 3, 7, 'ТЫ ИШАК, СКАЗАЛ ДУЙ', 0, '2026-06-25 15:06:55'),
(8, 3, 7, 'Здравствуйте', 0, '2026-06-25 20:28:33');

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
(1, 'WomiPowe', '$2y$10$CoXeJFrQb8mSOUK4zv0oaeARwFsPHRW28pUWi6O8Kx5ntqNuObM6q', 'Дмитрий', 'dmitri_guba@bk.ru', '+79020954393', 'фвы', NULL, 'assets/Media/avatars/avatar_6a340a8a3fcca_4fba8841.png', 'creator', 4, 14, 0, 12, '2026-06-15 14:53:04', '2026-06-24 18:24:05'),
(2, 'ManiPower', '$2y$10$1RCP6d8RQKUECW6wmd8aFOPRgbs0ypkMDc61FCP4G94MhKx2RhAXW', 'Александра', 'dmitri_guba@hotmail.com', '+79020954393', 'ждфывопэжлфыавопэжлфавыопрэжлфыоваэжлрпофвэждларпопэжлфваорэжлываоэжрлоываэжлроэжылваорэжлфваоьрэждлыовфаэржлофваэжлрофэжвлаорпэждлфвоарэжлофваэжлро', NULL, 'assets/Media/avatars/avatar_6a3ac3a25359d_9e85036b.jpg', 'moderator', 2, 9, 0, 12, '2026-06-15 14:54:53', '2026-06-25 13:05:46'),
(3, 'Ka1zeR', '$2y$10$lWYcftjwaNw8xlH8UoS6Ie1SKdrbcV0eyyqEpLBfII5ZhsISMZQQ2', 'Илья', 'Kaizer907@bk.ru', '+79023149745', '', NULL, 'assets/Media/avatars/avatar_6a32559e296b8_9f9d6d08.jpg', 'moderator', 0, 0, 0, 0, '2026-06-17 07:45:17', NULL),
(4, '13412412412', '$2y$10$BH4cDik4Ow7dLsey5Xjjvelo24jXb/Ok2EauMJBeQryefMBESdP4K', '124124124214', 'K.a.1.z.e.r@yandex.ru', '+79375348986', '', NULL, 'assets/Media/avatars/avatar_6a3438b15c86d_e6ca4f96.png', 'user', 0, 0, 0, 0, '2026-06-17 23:23:04', '2026-06-25 14:35:57'),
(5, 'admin', 'admin', '', 'admin890@bk.ru', '8(902)095 43 93', 'Я Модератор сайта BGN', NULL, 'assets/Media/Photo/man.png', 'user', 0, 0, 0, 0, '2026-06-23 20:39:36', NULL),
(6, 'admin123', '$2y$10$6mtXglje8fd2qDMKDHify.t5m9v3yjOGEn3yub5H7wLBpaaDewCxC', 'Дмитрий', 'Kaizer907@aa.ru', '+79020954393', 'Модератор и админ сайта BGN', NULL, 'assets/Media/avatars/avatar_6a3af0593d720_685860ac.png', 'admin', 4, 7, 0, 6, '2026-06-23 20:42:20', '2026-06-26 03:28:44'),
(7, 'specter', '$2y$10$nKlrLoGXh5dRUPic7JjWWOfPpUVIqHu4kx9NnNxKfbcYkFh2bVl92', 'ваня', 'evanmesh@bk.ru', '89996102397', NULL, NULL, 'assets/Media/avatars/avatar_6a3d3dd31427b_a17c8402.png', 'admin', 0, 1, 0, 0, '2026-06-25 14:39:16', '2026-06-26 03:06:45');

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
-- Индексы таблицы `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`comment_id`,`user_id`),
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
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`);

--
-- Индексы таблицы `profile_reviews`
--
ALTER TABLE `profile_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `target_user_id` (`target_user_id`);

--
-- Индексы таблицы `review_likes`
--
ALTER TABLE `review_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`review_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Индексы таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT для таблицы `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT для таблицы `news_likes`
--
ALTER TABLE `news_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT для таблицы `news_views`
--
ALTER TABLE `news_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `profile_reviews`
--
ALTER TABLE `profile_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `review_likes`
--
ALTER TABLE `review_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Ограничения внешнего ключа таблицы `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `comment_likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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

--
-- Ограничения внешнего ключа таблицы `review_likes`
--
ALTER TABLE `review_likes`
  ADD CONSTRAINT `review_likes_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `profile_reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
