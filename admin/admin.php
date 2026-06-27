<?php
require_once 'check_admin.php';

$active_tab = $_GET['tab'] ?? 'moderation';

$user_role = null;
if (isset($_SESSION['user_id'])) {
    $role_sql = "SELECT role FROM users WHERE id = ?";
    $role_stmt = mysqli_prepare($conn, $role_sql);
    mysqli_stmt_bind_param($role_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($role_stmt);
    $role_res = mysqli_stmt_get_result($role_stmt);
    $user_role = mysqli_fetch_assoc($role_res)['role'] ?? null;
}

$stats = [
    'pending_news' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news WHERE status = 'pending'"))['c'],
    'total_news' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news"))['c'],
    'total_users' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'],
    'total_comments' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM comments"))['c'],
];
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Best Game News</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/cab.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admin-support.css">
    <link rel="stylesheet" href="../css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
</head>

<body>
    <script src="/assets/js/theme-init.js"></script>
    <script src="/assets/js/no-cache.js"></script>

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
                        $avatar_path = $_SESSION['avatar'] ?? '/assets/Media/Photo/man.png';
                        if (strpos($avatar_path, 'http') !== 0 && strpos($avatar_path, '/') !== 0) {
                            $avatar_path = '/' . $avatar_path;
                        }
                        ?>
                        <a href="/profile/<?= htmlspecialchars($_SESSION['user_id']) ?>" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($avatar_path) ?>" alt="Профиль" class="header-avatar"
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
    <main>
        <div class="main-container">
            <div class="content-wrapper">
                <!-- сайдбар -->
                <div class="sidebar">
                    <h3><i class="fas fa-shield-alt"></i> Админ панель</h3>

                    <a href="?tab=moderation" class="menu-item <?= $active_tab === 'moderation' ? 'active' : '' ?>">
                        <i class="fas fa-newspaper"></i> Модерация
                        <?php if ($stats['pending_news'] > 0): ?>
                            <span
                                style="background:#df1b1b; color:white; padding:2px 8px; border-radius:10px; font-size:12px; margin-left:auto;">
                                <?= $stats['pending_news'] ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <a href="?tab=stats" class="menu-item <?= $active_tab === 'stats' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i> Статистика
                    </a>

                    <?php if ($user_role === 'admin'): ?>
                        <a href="?tab=users" class="menu-item <?= $active_tab === 'users' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i> Пользователи
                        </a>
                    <?php endif; ?>

                    <a href="?tab=support" class="menu-item <?= $active_tab === 'support' ? 'active' : '' ?>">
                        <i class="fas fa-life-ring"></i> Поддержка
                    </a>

                    <?php if (in_array($user_role, ['admin', 'moderator'])): ?>
                        <a href="?tab=featured" class="menu-item <?= $active_tab === 'featured' ? 'active' : '' ?>">
                            <i class="fas fa-star"></i> Новости недели
                        </a>
                    <?php endif; ?>

                    <a href="/cab" class="menu-item">
                        <i class="fas fa-arrow-left"></i> Вернуться в кабинет
                    </a>
                </div>
                <!-- /сайдбар -->

                <!-- контент -->
                <div class="content-area">
                    <?php
                    // Подключаем нужную вкладку
                    $tab_file = "tabs/{$active_tab}.php";
                    if (file_exists($tab_file)) {
                        include $tab_file;
                    } else {
                        echo '<h2>Вкладка не найдена</h2>';
                    }
                    ?>
                </div>
                <!-- /контент -->
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
    <script src="/assets/js/no-cache.js"></script>
    <script src="/assets/js/theme.js"></script>
</body>

</html>