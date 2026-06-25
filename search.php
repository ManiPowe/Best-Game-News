<?php
session_start();
require_once 'assets/app/db.php';

// Получаем роль и аватарку для хедера (как в category.php)
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/assets/js/theme-init.js"></script>
</head>

<body>
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="index.php">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="index.php">Главная</a>
                <a href="category.php?type=games">Игры</a>
                <a href="category.php?type=news">Новости</a>
                <a href="category.php?type=articles">Статьи</a>
                <a href="category.php?type=videos">Видео</a>
                <a href="category.php?type=walkthroughs">Прохождения</a>
                <a href="help.php">Помощь</a>

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
                <form action="search.php" method="get" class="search-form">
                    <input type="search" name="q" class="search-input" placeholder=" Поиск..."
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit" class="search-btn">
                        <img src="/assets/Media/Photo/search.png" alt="Поиск">
                    </button>
                </form>
                <div class="auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="cab.php" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'assets/Media/Photo/man.png') ?>"
                                alt="Профиль" class="header-avatar">
                        </a>
                    <?php else: ?>
                        <a href="login.php">
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
            <div class="content-area search-page">

                <?php if ($query === ''): ?>
                    <div class="search-empty">
                        <i class="fas fa-search"></i>
                        <h2>Введите запрос для поиска</h2>
                        <p>Ищите новости, авторов или игры</p>
                    </div>
                <?php else: ?>
                    <h1 class="search-title">Результаты поиска: <span>"<?= htmlspecialchars($query) ?>"</span></h1>

                    <!-- Новости -->
                    <?php if ($results['news'] && mysqli_num_rows($results['news']) > 0): ?>
                        <section class="search-section">
                            <h2><i class="fas fa-newspaper"></i> Новости (<?= mysqli_num_rows($results['news']) ?>)</h2>
                            <div class="news-grid">
                                <?php while ($news = mysqli_fetch_assoc($results['news'])): ?>
                                    <div class="news-card">
                                        <a href="news.php?id=<?= $news['id'] ?>" class="news-image-wrap">
                                            <?php
                                            $image_path = $news['image'] ?? '';
                                            $image_exists = $image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path);
                                            $display_image = $image_exists ? $image_path : '/assets/Media/Photo/Заглушка.jpg';
                                            ?>
                                            <img src="<?= htmlspecialchars($display_image) ?>"
                                                alt="<?= htmlspecialchars($news['title']) ?>">

                                            <?php if ($news['game_name']): ?>
                                                <a href="category.php?type=games&game_id=<?= $news['game_id'] ?>&sort=new"
                                                    class="game-badge-overlay">
                                                    <?php if ($news['game_icon']): ?>
                                                        <img src="<?= htmlspecialchars($news['game_icon']) ?>" alt="">
                                                    <?php endif; ?>
                                                    <span><?= htmlspecialchars($news['game_name']) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        </a>
                                        <div class="news-content">
                                            <h3><a href="news.php?id=<?= $news['id'] ?>"><?= htmlspecialchars($news['title']) ?></a>
                                            </h3>
                                            <p class="news-desc"><?= htmlspecialchars(mb_substr($news['content'], 0, 150)) ?>...</p>
                                            <div class="news-footer">
                                                <div class="news-author">
                                                    <img src="<?= htmlspecialchars($news['author_avatar']) ?>" alt=""
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

                    <!-- Авторы -->
                    <?php if ($results['users'] && mysqli_num_rows($results['users']) > 0): ?>
                        <section class="search-section">
                            <h2><i class="fas fa-users"></i> Авторы (<?= mysqli_num_rows($results['users']) ?>)</h2>
                            <div class="search-results-grid">
                                <?php while ($user = mysqli_fetch_assoc($results['users'])): ?>
                                    <a href="profile.php?id=<?= $user['id'] ?>" class="result-card user-card">
                                        <img src="<?= htmlspecialchars($user['avatar']) ?>"
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

                    <!-- Игры -->
                    <?php if ($results['games'] && mysqli_num_rows($results['games']) > 0): ?>
                        <section class="search-section">
                            <h2><i class="fas fa-gamepad"></i> Игры (<?= mysqli_num_rows($results['games']) ?>)</h2>
                            <div class="search-results-grid">
                                <?php while ($game = mysqli_fetch_assoc($results['games'])): ?>
                                    <a href="category.php?type=games&game_id=<?= $game['id'] ?>&sort=new"
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

                    <!-- Если ничего не найдено -->
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

                <?php endif; ?>

            </div>
        </div>
    </main>

    <script src="/assets/js/theme.js" defer></script>
</body>

</html>