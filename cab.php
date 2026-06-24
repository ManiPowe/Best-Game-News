<?php
session_start();
require_once 'assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$_SESSION['avatar'] = $user['avatar'];

$likes_sql = "SELECT COALESCE(SUM(likes_count), 0) as total_likes 
              FROM news 
              WHERE author_id = ? AND status = 'published'";
$likes_stmt = mysqli_prepare($conn, $likes_sql);
mysqli_stmt_bind_param($likes_stmt, "i", $user_id);
mysqli_stmt_execute($likes_stmt);
$likes_result = mysqli_stmt_get_result($likes_stmt);
$likes_data = mysqli_fetch_assoc($likes_result);
$total_likes = $likes_data['total_likes'];

$posts_sql = "SELECT COUNT(*) as posts_count 
              FROM news 
              WHERE author_id = ? AND status = 'published'";
$posts_stmt = mysqli_prepare($conn, $posts_sql);
mysqli_stmt_bind_param($posts_stmt, "i", $user_id);
mysqli_stmt_execute($posts_stmt);
$posts_result = mysqli_stmt_get_result($posts_stmt);
$posts_data = mysqli_fetch_assoc($posts_result);
$posts_count = $posts_data['posts_count'];

$current_tab = $_GET['tab'] ?? 'settings';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Best Game News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cab.css">
    <link rel="stylesheet" href="css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <script src="/assets/js/file_input.js"></script>
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="index.php">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="#">Игры</a>
                <a href="#">Новости</a>
                <a href="#">Статьи</a>
                <a href="#">Видео</a>
                <a href="#">Прохождения</a>
                <a href="#">Помощь</a>
            </nav>
            <div class="search-wrap">
                <form action="#" method="get">
                    <input type="search" name="text" class="search-input" placeholder=" Поиск...">
                    <button type="submit" class="search-btn">
                        <img src="/assets/Media/Photo/search.png" alt="Поиск">
                    </button>
                </form>
                <div class="auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="cab.php" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Профиль" class="header-avatar">
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

    <main>
        <div class="dashboard">
            <aside class="sidebar">
                <h3>Мой аккаунт</h3>
                <a href="profile.php?id=<?= $user_id ?>" class="menu-item">
                    <i class="fas fa-user"></i>
                    <span>Мой профиль</span>
                </a>
                <a href="?tab=settings" class="menu-item <?= $current_tab === 'settings' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Настройки</span>
                </a>
                <a href="?tab=favorites" class="menu-item <?= $current_tab === 'favorites' ? 'active' : '' ?>">
                    <i class="fas fa-bookmark"></i>
                    <span>Избранное</span>
                </a>
                <a href="?tab=comments" class="menu-item <?= $current_tab === 'comments' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i>
                    <span>Комментарии</span>
                </a>
                <a href="?tab=notifications" class="menu-item <?= $current_tab === 'notifications' ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i>
                    <span>Уведомления</span>
                </a>
                <a href="assets/app/logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Выход</span>
                </a>
            </aside>

            <div class="content-area">
                <?php if ($current_tab === 'settings'): ?>
                    <!-- ========================================== -->
                    <!-- ВКЛАДКА НАСТРОЕК (ПРОФИЛЬ) -->
                    <!-- ========================================== -->
                    <div class="content-header">
                        <h2>Профиль пользователя</h2>
                    </div>

                    <div class="profile-header">
                        <div class="profile-header-left">
                            <div class="avatar">
                                <?php if ($user['avatar'] === 'assets/Media/Photo/man.png' || empty($user['avatar'])): ?>
                                    <i class="fas fa-user"></i>
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар"
                                        style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                <?php endif; ?>
                            </div>

                            <form action="assets/app/upload_avatar.php" method="POST" enctype="multipart/form-data"
                                class="avatar-upload-form">
                                <input type="file" name="avatar" accept="image/*" required>
                                <button type="submit" class="save-btn upload-avatar-btn">Загрузить аватар</button>
                            </form>
                        </div>

                        <div class="profile-header-right">
                            <div class="profile-info">
                                <h2><?= htmlspecialchars($user['login']) ?></h2>
                                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                                <p><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></p>
                                <p><i class="fas fa-calendar"></i> Участник с
                                    <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                                </p>
                            </div>

                            <div class="profile-stats">
                                <div class="stat-item">
                                    <div class="stat-value"><?= $posts_count ?? 0 ?></div>
                                    <div class="stat-label">Постов</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $user['comments_count'] ?? 0 ?></div>
                                    <div class="stat-label">Комментариев</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $total_likes ?? 0 ?></div>
                                    <div class="stat-label">Лайков</div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['profile_message'])): ?>
                        <div class="alert alert-<?= $_SESSION['profile_message_type'] ?? 'success' ?>"
                            style="margin-bottom: 20px;">
                            <?= $_SESSION['profile_message'] ?>
                        </div>
                        <?php
                        unset($_SESSION['profile_message']);
                        unset($_SESSION['profile_message_type']);
                        ?>
                    <?php endif; ?>

                    <form action="assets/app/update_profile.php" method="POST" class="profile-form-wrapper">
                        <div class="profile-form">

                            <div class="form-group">
                                <label for="login">Имя пользователя</label>
                                <input type="text" id="login" name="login" value="<?= htmlspecialchars($user['login']) ?>"
                                    required minlength="6">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="bio">О себе</label>
                                <textarea id="bio" name="bio"
                                    rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="theme-switcher-block">
                            <h3><i class="fas fa-palette"></i> Внешний вид</h3>

                            <!-- Переключатель темы -->
                            <div class="theme-toggle-container">
                                <span class="theme-label"><i class="fas fa-moon"></i> Тёмная</span>
                                <label class="switch">
                                    <input type="checkbox" id="theme-toggle">
                                    <span class="slider round"></span>
                                </label>
                                <span class="theme-label"><i class="fas fa-sun"></i> Светлая</span>
                            </div>

                            <!-- Выбор фона -->
                            <div class="background-selector">
                                <h4><i class="fas fa-image"></i> Фон страницы</h4>
                                <div class="background-options" id="background-options">
                                    <!-- Опции генерируются через JS -->
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="save-btn">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>

                    </form>

                <?php elseif ($current_tab === 'favorites'): ?>
                    <!-- ========================================== -->
                    <!-- ВКЛАДКА ИЗБРАННОГО -->
                    <!-- ========================================== -->
                    <div class="content-header">
                        <h2><i class="fas fa-bookmark"></i> Избранные новости</h2>
                    </div>

                    <div class="favorites-container">
                        <?php
                        // Получаем избранные новости пользователя
                        $fav_sql = "SELECT n.id, n.title, n.short_description, n.image, n.category, n.game_id, 
                                           n.likes_count, n.views, n.created_at,
                                           u.login as author_login, u.avatar as author_avatar,
                                           g.name as game_name, g.icon as game_icon,
                                           (SELECT COUNT(*) FROM comments WHERE news_id = n.id) as comments_count,
                                           (SELECT COUNT(*) FROM favorites WHERE news_id = n.id) as favorites_count
                                    FROM favorites f
                                    JOIN news n ON f.news_id = n.id
                                    JOIN users u ON n.author_id = u.id
                                    LEFT JOIN games g ON n.game_id = g.id
                                    WHERE f.user_id = ?
                                    ORDER BY f.created_at DESC";

                        $fav_stmt = mysqli_prepare($conn, $fav_sql);
                        mysqli_stmt_bind_param($fav_stmt, "i", $user_id);
                        mysqli_stmt_execute($fav_stmt);
                        $fav_result = mysqli_stmt_get_result($fav_stmt);
                        $favorites = [];
                        while ($fav = mysqli_fetch_assoc($fav_result)) {
                            $favorites[] = $fav;
                        }
                        ?>

                        <?php if (!empty($favorites)): ?>
                            <div class="favorites-list">
                                <?php foreach ($favorites as $fav): ?>
                                    <a href="news.php?id=<?= $fav['id'] ?>" class="favorite-card">
                                        <div class="favorite-cover">
                                            <?php if ($fav['image']): ?>
                                                <img src="<?= htmlspecialchars($fav['image']) ?>"
                                                    alt="<?= htmlspecialchars($fav['title']) ?>">
                                            <?php else: ?>
                                                <div class="cover-placeholder">
                                                    <i class="fas fa-newspaper"></i>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($fav['game_name']): ?>
                                                <div class="game-badge">
                                                    <?php if ($fav['game_icon']): ?>
                                                        <img src="<?= htmlspecialchars($fav['game_icon']) ?>"
                                                            alt="<?= htmlspecialchars($fav['game_name']) ?>">
                                                    <?php endif; ?>
                                                    <span><?= htmlspecialchars($fav['game_name']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="favorite-info">
                                            <h3><?= htmlspecialchars($fav['title']) ?></h3>

                                            <?php if ($fav['short_description']): ?>
                                                <p class="favorite-description"><?= htmlspecialchars($fav['short_description']) ?></p>
                                            <?php endif; ?>

                                            <div class="favorite-meta">
                                                <div class="meta-author">
                                                    <img src="<?= htmlspecialchars($fav['author_avatar']) ?>" alt="Автор">
                                                    <span><?= htmlspecialchars($fav['author_login']) ?></span>
                                                </div>
                                                <span class="meta-date"><?= date('d.m.Y', strtotime($fav['created_at'])) ?></span>
                                            </div>

                                            <div class="favorite-stats">
                                                <span class="stat-item">
                                                    <i class="fas fa-heart"></i>
                                                    <span><?= $fav['likes_count'] ?? 0 ?></span>
                                                </span>
                                                <span class="stat-item">
                                                    <i class="fas fa-bookmark"></i>
                                                    <span><?= $fav['favorites_count'] ?></span>
                                                </span>
                                                <span class="stat-item">
                                                    <i class="fas fa-comment"></i>
                                                    <span><?= $fav['comments_count'] ?></span>
                                                </span>
                                                <span class="stat-item">
                                                    <i class="fas fa-eye"></i>
                                                    <span><?= $fav['views'] ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-favorites">
                                <i class="fas fa-bookmark"></i>
                                <p>У вас пока нет избранных новостей</p>
                                <a href="index.php" class="browse-btn">Перейти к новостям</a>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($current_tab === 'comments'): ?>
                    <!-- ========================================== -->
                    <!-- ВКЛАДКА КОММЕНТАРИЕВ -->
                    <!-- ========================================== -->
                    <div class="content-header">
                        <h2><i class="fas fa-comments"></i> Мои комментарии</h2>
                    </div>
                    <div class="empty-tab">
                        <i class="fas fa-comments"></i>
                        <p>Раздел в разработке</p>
                    </div>

                <?php elseif ($current_tab === 'notifications'): ?>
                    <!-- ========================================== -->
                    <!-- ВКЛАДКА УВЕДОМЛЕНИЙ -->
                    <!-- ========================================== -->
                    <div class="content-header">
                        <h2><i class="fas fa-bell"></i> Уведомления</h2>
                    </div>
                    <div class="empty-tab">
                        <i class="fas fa-bell"></i>
                        <p>Раздел в разработке</p>
                    </div>
                <?php else: ?>
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
    <script src="/assets/js/theme.js"></script>
</body>

</html>