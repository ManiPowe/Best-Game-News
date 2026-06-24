<?php
require_once 'check_admin.php';

// Определяем активную вкладку
$active_tab = $_GET['tab'] ?? 'moderation';

// Получаем статистику для дашборда
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
    <link rel="stylesheet" href="../css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="../index.php">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="../index.php">Главная</a>
                <a href="#">Игры</a>
                <a href="#">Новости</a>
                <a href="#">Статьи</a>
                <a href="#">Видео</a>
                <a href="#">Прохождения</a>
                <a href="#">Помощь</a>
                <a href="../cab.php">Кабинет</a>
                <a href="admin.php" class="admin-link">
                    <i class="fas fa-shield-alt"></i> Админ панель
                    <span class="admin-badge badge-<?= $admin_user['role'] ?>">
                        <?= $admin_user['role'] === 'admin' ? 'Админ' : 'Модератор' ?>
                    </span>
                </a>
            </nav>
            <div class="search-wrap">
                <form action="#" method="get">
                    <input type="search" name="text" class="search-input" placeholder=" Поиск...">
                    <button type="submit" class="search-btn">
                        <img src="/assets/Media/Photo/search.png" alt="Поиск">
                    </button>
                </form>
                <div class="auth">
                    <a href="../cab.php?id=<?= $admin_user['id'] ?>" class="user-avatar-link">
                        <img src="../<?= htmlspecialchars($admin_user['avatar']) ?>" alt="Профиль" class="header-avatar"
                            onerror="this.src='../assets/Media/Photo/man.png'">
                    </a>
                </div>
            </div>
    </header>

    <main>
        <div class="main-container">
            <div class="content-wrapper">
                <!-- Боковое меню -->
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

                    <?php if ($admin_user['role'] === 'admin'): ?>
                        <a href="?tab=users" class="menu-item <?= $active_tab === 'users' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i> Пользователи
                        </a>
                    <?php endif; ?>

                    <?php if (in_array($admin_user['role'], ['admin', 'moderator'])): ?>
                        <a href="?tab=featured" class="menu-item <?= $active_tab === 'featured' ? 'active' : '' ?>">
                            <i class="fas fa-star"></i> Новости недели
                        </a>
                    <?php endif; ?>

                    <a href="../cab.php" class="menu-item">
                        <i class="fas fa-arrow-left"></i> Вернуться в кабинет
                    </a>
                </div>

                <!-- Контент -->
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
    <script src="../assets/js/theme.js"></script>
</body>

</html>