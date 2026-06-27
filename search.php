<?php
session_start();
require_once 'assets/app/db.php';

// Получаем роль и аватарку для хедера
$user_role = null;
if (isset($_SESSION['user_id'])) {
    $check_role_sql = "SELECT role FROM users WHERE id = ?";
    $stmt_role = mysqli_prepare($conn, $check_role_sql);
    mysqli_stmt_bind_param($stmt_role, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_role);
    $result_role = mysqli_stmt_get_result($stmt_role);
    $user_role = mysqli_fetch_assoc($result_role)['role'] ?? null;

    if (empty($_SESSION['avatar'])) {
        $avatar_sql = "SELECT avatar FROM users WHERE id = ?";
        $avatar_stmt = mysqli_prepare($conn, $avatar_sql);
        mysqli_stmt_bind_param($avatar_stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($avatar_stmt);
        $avatar_result = mysqli_stmt_get_result($avatar_stmt);
        if ($avatar_row = mysqli_fetch_assoc($avatar_result)) {
            $_SESSION['avatar'] = $avatar_row['avatar'];
        }
    }
}

// Обработка запроса
$query = trim($_GET['q'] ?? '');
$results = ['news' => null, 'users' => null, 'games' => null];

if ($query !== '') {
    $search_term = '%' . $query . '%';

    // 1. Поиск новостей (по заголовку, тексту и тегам)
    $sql_news = "SELECT n.*, u.login as author_login, u.avatar as author_avatar, 
                        g.name as game_name, g.icon as game_icon,
                        (SELECT COUNT(*) FROM favorites WHERE news_id = n.id) as favorites_count
                 FROM news n 
                 JOIN users u ON n.author_id = u.id 
                 LEFT JOIN games g ON n.game_id = g.id 
                 WHERE n.status = 'published' AND (n.title LIKE ? OR n.content LIKE ? OR n.tags LIKE ?)
                 LIMIT 20";
    $stmt_news = mysqli_prepare($conn, $sql_news);
    mysqli_stmt_bind_param($stmt_news, "sss", $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt_news);
    $results['news'] = mysqli_stmt_get_result($stmt_news);

    // 2. Поиск авторов
    $sql_users = "SELECT id, login, avatar, role FROM users WHERE login LIKE ? LIMIT 10";
    $stmt_users = mysqli_prepare($conn, $sql_users);
    mysqli_stmt_bind_param($stmt_users, "s", $search_term);
    mysqli_stmt_execute($stmt_users);
    $results['users'] = mysqli_stmt_get_result($stmt_users);

    // 3. Поиск игр
    $sql_games = "SELECT id, name, icon FROM games WHERE name LIKE ? LIMIT 10";
    $stmt_games = mysqli_prepare($conn, $sql_games);
    mysqli_stmt_bind_param($stmt_games, "s", $search_term);
    mysqli_stmt_execute($stmt_games);
    $results['games'] = mysqli_stmt_get_result($stmt_games);
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск: <?= htmlspecialchars($query) ?> - Best Game News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
    <script src="/assets/js/theme-init.js"></script>
</head>

<body>
    <!-- хедер -->
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="/home">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="/home">Главная</a>
                <a href="/category/games">Игры</a>
                <a href="/category/news">Новости</a>
                <a href="/category/articles">Статьи</a>
                <a href="/category/videos">Видео</a>
                <a href="/category/walkthroughs">Прохождения</a>
                <a href="/help">Помощь</a>

                <?php if ($user_role === 'admin' || $user_role === 'moderator'): ?>
                    <a href="/admin/admin.php" class="admin-link">
                        <i class="fas fa-shield-alt"></i> Админ панель
                    </a>
                <?php endif; ?>

                <?php if ($user_role === 'creator' || $user_role === 'moderator' || $user_role === 'admin'): ?>
                    <a href="/create" class="create-news-btn">
                        <i class="fas fa-plus"></i> Создать пост
                    </a>
                <?php endif; ?>
            </nav>
            <div class="search-wrap">
                <form action="/search" method="get" class="search-form">
                    <input type="search" name="q" class="search-input" placeholder=" Поиск..."
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit" class="search-btn">
                        <img src="/assets/Media/Photo/search.png" alt="Поиск">
                    </button>
                </form>
                <div class="auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                        $header_avatar = $_SESSION['avatar'] ?? '/assets/Media/Photo/man.png';
                        if (strpos($header_avatar, 'http') !== 0 && strpos($header_avatar, '/') !== 0) {
                            $header_avatar = '/' . $header_avatar;
                        }
                        ?>
                        <a href="/cab" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($header_avatar) ?>"
                                alt="Профиль" class="header-avatar"
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
    <!-- /хедер -->

    <!-- основной контент -->
    <main class="main-container">
        <div class="content-wrapper">
            <div class="content-area search-page">

                <?php if ($query === ''): ?>
                    <!-- пустой запрос -->
                    <div class="search-empty">
                        <i class="fas fa-search"></i>
                        <h2>Введите запрос для поиска</h2>
                        <p>Ищите новости, авторов или игры</p>
                    </div>
                <?php else: ?>
                    <h1 class="search-title">Результаты поиска: <span>"<?= htmlspecialchars($query) ?>"</span></h1>

                    <!-- результаты: новости -->
                    <?php if ($results['news'] && mysqli_num_rows($results['news']) > 0): ?>
                        <section class="search-section">
                            <h2><i class="fas fa-newspaper"></i> Новости (<?= mysqli_num_rows($results['news']) ?>)</h2>
                            <div class="news-grid">
                                <?php while ($news = mysqli_fetch_assoc($results['news'])): ?>
                                    <div class="news-card">
                                        <a href="/news/<?= $news['id'] ?>" class="news-image-wrap">
                                            <?php
                                            $image_path = $news['image'] ?? '';
                                            $image_exists = $image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path);
                                            $display_image = $image_exists ? $image_path : '/assets/Media/Photo/Заглушка.jpg';
                                            ?>
                                            <img src="<?= htmlspecialchars($display_image) ?>"
                                                alt="<?= htmlspecialchars($news['title']) ?>">

                                            <?php if ($news['game_name']): ?>
                                                <a href="/category/games/<?= $news['game_id'] ?>"
                                                    class="game-badge-overlay">
                                                    <?php if ($news['game_icon']): ?>
                                                        <img src="<?= htmlspecialchars($news['game_icon']) ?>" alt="">
                                                    <?php endif; ?>
                                                    <span><?= htmlspecialchars($news['game_name']) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        </a>
                                        <div class="news-content">
                                            <h3><a href="/news/<?= $news['id'] ?>"><?= htmlspecialchars($news['title']) ?></a>
                                            </h3>
                                            <p class="news-desc"><?= htmlspecialchars(mb_substr($news['content'], 0, 150)) ?>...</p>
                                            <div class="news-footer">
                                                <div class="news-author">
                                                    <?php
                                                    $author_avatar = $news['author_avatar'] ?? '/assets/Media/Photo/man.png';
                                                    if (strpos($author_avatar, 'http') !== 0 && strpos($author_avatar, '/') !== 0) {
                                                        $author_avatar = '/' . $author_avatar;
                                                    }
                                                    ?>
                                                    <img src="<?= htmlspecialchars($author_avatar) ?>" alt=""
                                                        class="author-avatar">
                                                    <span><?= htmlspecialchars($news['author_login']) ?></span>
                                                </div>
                                                <div class="news-stats">
                                                    <span class="stat-item"><i class="fas fa-eye"></i> <?= $news['views'] ?></span>
                                                    <span class="stat-item"><i class="fas fa-heart"></i>
                                                        <?= $news['likes_count'] ?></span>
                                                    <span class="stat-item"><i class="fas fa-bookmark"></i>
                                                        <?= $news['favorites_count'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    <!-- /результаты: новости -->

                    <!-- результаты: авторы -->
                    <?php if ($results['users'] && mysqli_num_rows($results['users']) > 0): ?>
                        <section class="search-section">
                            <h2><i class="fas fa-users"></i> Авторы (<?= mysqli_num_rows($results['users']) ?>)</h2>
                            <div class="search-results-grid">
                                <?php while ($user = mysqli_fetch_assoc($results['users'])): ?>
                                    <?php
                                    $user_avatar = $user['avatar'] ?? '/assets/Media/Photo/man.png';
                                    if (strpos($user_avatar, 'http') !== 0 && strpos($user_avatar, '/') !== 0) {
                                        $user_avatar = '/' . $user_avatar;
                                    }
                                    ?>
                                    <a href="/profile/<?= $user['id'] ?>" class="result-card user-card">
                                        <img src="<?= htmlspecialchars($user_avatar) ?>"
                                            alt="<?= htmlspecialchars($user['login']) ?>">
                                        <div class="result-info">
                                            <h4><?= htmlspecialchars($user['login']) ?></h4>
                                            <span
                                                class="role-badge role-<?= $user['role'] ?>"><?= htmlspecialchars($user['role']) ?></span>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    <!-- /результаты: авторы -->

                    <!-- результаты: игры -->
                    <?php if ($results['games'] && mysqli_num_rows($results['games']) > 0): ?>
                        <section class="search-section">
                            <h2><i class="fas fa-gamepad"></i> Игры (<?= mysqli_num_rows($results['games']) ?>)</h2>
                            <div class="search-results-grid">
                                <?php while ($game = mysqli_fetch_assoc($results['games'])): ?>
                                    <a href="/category/games?game_id=<?= $game['id'] ?>"
                                        class="result-card game-card">
                                        <?php if ($game['icon']): ?>
                                            <img src="<?= htmlspecialchars($game['icon']) ?>"
                                                alt="<?= htmlspecialchars($game['name']) ?>">
                                        <?php else: ?>
                                            <div class="no-icon"><i class="fas fa-gamepad"></i></div>
                                        <?php endif; ?>
                                        <div class="result-info">
                                            <h4><?= htmlspecialchars($game['name']) ?></h4>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    <!-- /результаты: игры -->

                    <!-- ничего не найдено -->
                    <?php if (
                        (!$results['news'] || mysqli_num_rows($results['news']) === 0) &&
                        (!$results['users'] || mysqli_num_rows($results['users']) === 0) &&
                        (!$results['games'] || mysqli_num_rows($results['games']) === 0)
                    ): ?>
                        <div class="search-empty">
                            <i class="fas fa-search-minus"></i>
                            <h2>Ничего не найдено</h2>
                            <p>Попробуйте изменить запрос или проверить правильность написания</p>
                        </div>
                    <?php endif; ?>
                    <!-- /ничего не найдено -->

                <?php endif; ?>

            </div>
        </div>
    </main>
    <!-- /основной контент -->

    <!-- футер -->
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
    <!-- /футер -->
    <script src="/assets/js/theme.js" defer></script>
</body>

</html>