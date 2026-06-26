<?php
session_start();
require_once 'assets/app/db.php';

// Получаем роль пользователя
$user_role = null;
if (isset($_SESSION['user_id'])) {
    $check_role_sql = "SELECT role FROM users WHERE id = ?";
    $stmt_role = mysqli_prepare($conn, $check_role_sql);
    mysqli_stmt_bind_param($stmt_role, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_role);
    $result_role = mysqli_stmt_get_result($stmt_role);
    $user_role = mysqli_fetch_assoc($result_role)['role'] ?? null;
}

// Получаем аватарку, если её нет в сессии
if (isset($_SESSION['user_id']) && empty($_SESSION['avatar'])) {
    $avatar_sql = "SELECT avatar FROM users WHERE id = ?";
    $avatar_stmt = mysqli_prepare($conn, $avatar_sql);
    mysqli_stmt_bind_param($avatar_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($avatar_stmt);
    $avatar_result = mysqli_stmt_get_result($avatar_stmt);
    if ($avatar_row = mysqli_fetch_assoc($avatar_result)) {
        $_SESSION['avatar'] = $avatar_row['avatar'];
    }
}

// 1. Определяем категорию (белый список для безопасности)
$allowed_types = ['games', 'news', 'articles', 'videos', 'walkthroughs'];
$type = in_array($_GET['type'] ?? '', $allowed_types) ? $_GET['type'] : 'all';

// 2. Определяем сортировку
$allowed_sorts = ['new', 'popular'];
$sort = in_array($_GET['sort'] ?? '', $allowed_sorts) ? $_GET['sort'] : 'new';

// 3. Формируем заголовок страницы
$titles = [
    'all' => 'Все материалы',
    'games' => 'Игры',
    'news' => 'Новости',
    'articles' => 'Статьи',
    'videos' => 'Видео',
    'walkthroughs' => 'Прохождения'
];
$page_title = $titles[$type] ?? 'Категория';

// Получаем game_id из URL
$game_id = isset($_GET['game_id']) ? (int) $_GET['game_id'] : 0;

// Получаем список всех игр для фильтров
$games_list_sql = "SELECT id, name, icon FROM games ORDER BY name ASC";
$games_list_result = mysqli_query($conn, $games_list_sql);

// 4. Формируем SQL запрос (без подзапроса favorites)
$sql = "SELECT n.*, u.login as author_login, u.avatar as author_avatar, 
               g.name as game_name, g.icon as game_icon
        FROM news n 
        JOIN users u ON n.author_id = u.id 
        LEFT JOIN games g ON n.game_id = g.id 
        WHERE n.status = 'published'";

// Добавляем фильтр по категории
if ($type !== 'all') {
    $sql .= " AND n.category = ?";
}

// Добавляем фильтр по игре
if ($game_id > 0) {
    $sql .= " AND n.game_id = ?";
}

// Добавляем сортировку
if ($sort === 'popular') {
    $sql .= " ORDER BY n.views DESC";
} else {
    $sql .= " ORDER BY n.created_at DESC";
}

$sql .= " LIMIT 50";

// Подготавливаем запрос
$stmt = mysqli_prepare($conn, $sql);

// Если prepare не удался — выводим ошибку для отладки
if (!$stmt) {
    die("SQL Error: " . mysqli_error($conn) . "<br>Query: " . $sql);
}

// Привязываем параметры
if ($type !== 'all' && $game_id > 0) {
    mysqli_stmt_bind_param($stmt, "si", $type, $game_id);
} elseif ($type !== 'all') {
    mysqli_stmt_bind_param($stmt, "s", $type);
} elseif ($game_id > 0) {
    mysqli_stmt_bind_param($stmt, "i", $game_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Best Game News</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
</head>

<body>
    <script src="/assets/js/theme-init.js"></script>
    <script src="/assets/js/no-cache.js"></script>
    <script src="/assets/js/theme.js" defer></script>
    <script src="/assets/js/news_actions.js"></script>
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="/index.php">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="index.php">Главная</a>
                <a href="/category/games">Игры</a>
                <a href="/category/news">Новости</a>
                <a href="/category/articles">Статьи</a>
                <a href="/category/videos">Видео</a>
                <a href="/category/walkthroughs">Прохождения</a>
                <a href="/help">Помощь</a>

                <?php if ($user_role === 'admin' || $user_role === 'moderator'): ?>
                    <a href="admin/admin.php" class="admin-link">
                        <i class="fas fa-shield-alt"></i> Админ панель
                    </a>
                <?php endif; ?>

                <?php if ($user_role === 'creator' || $user['role'] === 'moderator' || $user_role === 'admin'): ?>
                    <a href="create_news.php" class="create-news-btn">
                        <i class="fas fa-plus"></i> Создать пост
                    </a>
                <?php endif; ?>
            </nav>
            <div class="search-wrap">
                <form action="../search.php" method="get" class="search-form">
                    <input type="search" name="q" class="search-input" placeholder=" Поиск..."
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit" class="search-btn">
                        <img src="/assets/Media/Photo/search.png" alt="Поиск">
                    </button>
                </form>
                <div class="auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="header-notifications">
                            <a href="/cab?tab=notifications" class="notification-bell">
                                <i class="fas fa-bell"></i>
                                <?php if ($unread_count > 0): ?>
                                    <span class="notification-badge"><?= $unread_count ?></span>
                                <?php endif; ?>
                            </a>
                        </div>

                        <?php
                        $header_avatar = $_SESSION['avatar'] ?? '/assets/Media/Photo/man.png';
                        if (strpos($header_avatar, 'http') !== 0 && strpos($header_avatar, '/') !== 0) {
                            $header_avatar = '/' . $header_avatar;
                        }
                        ?>
                        <a href="/cab" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($header_avatar) ?>" alt="Профиль" class="header-avatar"
                                onerror="this.src='/assets/Media/Photo/man.png'">
                        </a>
                    <?php else: ?>
                        <a href="/login">
                            <button class="icon-btn" type="button" aria-label="Вход">
                                <img src="/assets/Media/Photo/man.png" alt="Вход">
                            </button>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="main-container">
        <div class="content-wrapper">
            <div class="content-area" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 20px;">

                <div class="category-header">
                    <h1>
                        <?= $page_title ?>
                    </h1>

                    <div class="sort-buttons">
                        <a href="?type=<?= $type ?>&sort=new<?= $game_id > 0 ? '&game_id=' . $game_id : '' ?>"
                            class="sort-btn <?= $sort === 'new' ? 'active' : '' ?>">
                            <i class="fas fa-clock"></i> Новые
                        </a>
                        <a href="?type=<?= $type ?>&sort=popular<?= $game_id > 0 ? '&game_id=' . $game_id : '' ?>"
                            class="sort-btn <?= $sort === 'popular' ? 'active' : '' ?>">
                            <i class="fas fa-fire"></i> Популярные
                        </a>
                    </div>
                </div>

                <?php if ($type == 'games' && mysqli_num_rows($games_list_result) > 0): ?>
                    <div class="games-filter">
                        <a href="/category/games?sort=<?= $sort ?>" class="filter-btn <?= $game_id == 0 ? 'active' : '' ?>">
                            Все игры
                        </a>
                        <?php
                        mysqli_data_seek($games_list_result, 0);
                        while ($game = mysqli_fetch_assoc($games_list_result)):
                            ?>
                            <a href="/category/games?game_id=<?= $game['id'] ?>?sort=<?= $sort ?>"
                                class="filter-btn <?= $game_id == $game['id'] ? 'active' : '' ?>">
                                <?php if ($game['icon']): ?>
                                    <img src="<?= htmlspecialchars($game['icon']) ?>" alt="<?= htmlspecialchars($game['name']) ?>">
                                <?php endif; ?>
                                <?= htmlspecialchars($game['name']) ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="news-grid">
                        <?php while ($news = mysqli_fetch_assoc($result)): ?>
                            <div class="news-card">
                                <a href="/news/<?= $news['id'] ?>" class="news-image-wrap">
                                    <?php
                                    $image_path = $news['image'] ?? '';

                                    // Делаем путь абсолютным (начинается с /)
                                    if ($image_path && strpos($image_path, 'http') !== 0 && strpos($image_path, '/') !== 0) {
                                        $image_path = '/' . $image_path;
                                    }

                                    // Проверяем существование файла
                                    $file_path = $_SERVER['DOCUMENT_ROOT'] . $image_path;
                                    $image_exists = $image_path && file_exists($file_path);
                                    $display_image = $image_exists ? $image_path : '/assets/Media/Photo/Заглушка.jpg';
                                    ?>
                                    <img src="<?= htmlspecialchars($display_image) ?>"
                                        alt="<?= htmlspecialchars($news['title']) ?>">

                                    <?php if ($news['game_name']): ?>
                                        <div class="game-badge-overlay">
                                            <?php
                                            $game_icon_path = $news['game_icon'] ?? '';
                                            if ($game_icon_path && strpos($game_icon_path, 'http') !== 0 && strpos($game_icon_path, '/') !== 0) {
                                                $game_icon_path = '/' . $game_icon_path;
                                            }
                                            ?>
                                            <?php if ($game_icon_path): ?>
                                                <img src="<?= htmlspecialchars($game_icon_path) ?>"
                                                    alt="<?= htmlspecialchars($news['game_name']) ?>">
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($news['game_name']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <div class="news-content">
                                    <h3><a href="/news/<?= $news['id'] ?>"><?= htmlspecialchars($news['title']) ?></a></h3>

                                    <p class="news-desc">
                                        <?= htmlspecialchars(mb_substr($news['short_description'] ?? $news['content'], 0, 150)) ?>...
                                    </p>

                                    <div class="news-footer">
                                        <div class="news-author">
                                            <?php
                                            $author_avatar = $news['author_avatar'] ?? '';
                                            if ($author_avatar && strpos($author_avatar, 'http') !== 0 && strpos($author_avatar, '/') !== 0) {
                                                $author_avatar = '/' . $author_avatar;
                                            }
                                            ?>
                                            <img src="<?= htmlspecialchars($author_avatar ?: '/assets/Media/Photo/man.png') ?>"
                                                alt="<?= htmlspecialchars($news['author_login']) ?>" class="author-avatar">
                                            <span><?= htmlspecialchars($news['author_login']) ?></span>
                                        </div>

                                        <div class="news-stats">
                                            <span class="stat-item">
                                                <i class="fas fa-eye"></i> <?= $news['views'] ?>
                                            </span>
                                            <span class="stat-item">
                                                <i class="fas fa-heart"></i> <?= $news['likes_count'] ?>
                                            </span>
                                            <span class="stat-item">
                                                <i class="fas fa-bookmark"></i> <?= $news['favorites_count'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h3>В этой категории пока пусто</h3>
                        <p>Загляните позже, мы скоро добавим материалы!</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <footer>
        <div class="footer">
            <div class="footer-logo">
                <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                <p>Best Game News</p>
            </div>
            <div class="prav">
                <p>2026 © Все права защищенны Best Game News</p>
            </div>
            <div class="social-links">
                <a href="#" aria-label="ВКонтакте">
                    <button type="button"><img src="/assets/Media/Photo/vk.png" alt="ВКонтакте"></button>
                </a>
                <a href="#" aria-label="Discord">
                    <button type="button"><img src="/assets/Media/Photo/discord.png" alt="Discord"></button>
                </a>
                <a href="#" aria-label="YouTube">
                    <button type="button"><img src="/assets/Media/Photo/youtube.png" alt="YouTube"></button>
                </a>
                <a href="#" aria-label="Telegram">
                    <button type="button"><img src="/assets/Media/Photo/tellegram.png" alt="Telegram"></button>
                </a>
                <a href="#" aria-label="Steam">
                    <button type="button"><img src="/assets/Media/Photo/steam.png" alt="Steam"></button>
                </a>
                <a href="#" aria-label="Twitch">
                    <button type="button"><img src="/assets/Media/Photo/twitch.png" alt="Twitch"></button>
                </a>
            </div>
        </div>
    </footer>

    <script src="/assets/js/theme.js" defer></script>
</body>

</html>