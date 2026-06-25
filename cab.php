<?php
session_start();
require_once 'assets/app/db.php';
// Подключаем функции уведомлений
require_once 'assets/app/notifications.php';
$unread_count = getUnreadCount($conn, $user_id);

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
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
    header("Location: /login");
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
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
</head>

<body>
    <script src="/assets/js/theme-init.js"></script>
    <script src="/assets/js/no-cache.js"></script>
    <script src="/assets/js/file_input.js"></script>
    <?php
    // Убедись что в самом начале файла есть:
// session_start();
// require_once 'assets/app/db.php';
    
    // Получаем роль пользователя ОДИН раз
    $user_role = null;
    if (isset($_SESSION['user_id'])) {
        $check_role_sql = "SELECT role FROM users WHERE id = ?";
        $stmt_role = mysqli_prepare($conn, $check_role_sql);
        mysqli_stmt_bind_param($stmt_role, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt_role);
        $result_role = mysqli_stmt_get_result($stmt_role);
        $user_role = mysqli_fetch_assoc($result_role)['role'] ?? null;
    }
    ?>

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
                <form action="search.php" method="get" class="search-form">
                    <input type="search" name="q" class="search-input" placeholder=" Поиск..."
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit" class="search-btn">
                        <img src="/assets/Media/Photo/search.png" alt="Поиск">
                    </button>
                </form>
                <div class="auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/cab" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'assets/Media/Photo/man.png') ?>"
                                alt="Профиль" class="header-avatar">
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

    <main>
        <div class="dashboard">
            <aside class="sidebar">
                <h3>Мой аккаунт</h3>
                <a href="/profile/<?= $user_id ?>" class="menu-item">
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
                    <?php if ($unread_count > 0): ?>
                        <span class="menu-badge"><?= $unread_count ?></span>
                    <?php endif; ?>
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
                                <?php foreach ($favorites as $fav):
                                    // Проверяем существует ли файл картинки
                                    $image_path = $fav['image'] ?? '';
                                    $image_exists = $image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path);
                                    $display_image = $image_exists ? $image_path : '/assets/Media/Photo/Заглушка.jpg';
                                    ?>
                                    <a href="news.php?id=<?= $fav['id'] ?>" class="favorite-card">
                                        <div class="favorite-cover">
                                            <img src="<?= htmlspecialchars($display_image) ?>"
                                                alt="<?= htmlspecialchars($fav['title']) ?>">

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
                        <h2><i class="fas fa-comments"></i> Комментарии</h2>
                    </div>

                    <?php
                    // Получаем комментарии пользователя
                    $my_comments_sql = "SELECT c.*, n.title as news_title, n.id as news_id 
                        FROM comments c 
                        JOIN news n ON c.news_id = n.id 
                        WHERE c.user_id = ? 
                        ORDER BY c.created_at DESC 
                        LIMIT 50";
                    $my_comments_stmt = mysqli_prepare($conn, $my_comments_sql);
                    mysqli_stmt_bind_param($my_comments_stmt, "i", $user_id);
                    mysqli_stmt_execute($my_comments_stmt);
                    $my_comments_result = mysqli_stmt_get_result($my_comments_stmt);
                    $my_comments = [];
                    while ($comment = mysqli_fetch_assoc($my_comments_result)) {
                        $my_comments[] = $comment;
                    }

                    // Получаем комментарии на посты пользователя (от других пользователей)
                    $replies_sql = "SELECT c.*, u.login as author_login, u.avatar as author_avatar, 
                           n.title as news_title, n.id as news_id 
                    FROM comments c 
                    JOIN users u ON c.user_id = u.id 
                    JOIN news n ON c.news_id = n.id 
                    WHERE n.author_id = ? AND c.user_id != ? 
                    ORDER BY c.created_at DESC 
                    LIMIT 50";
                    $replies_stmt = mysqli_prepare($conn, $replies_sql);
                    mysqli_stmt_bind_param($replies_stmt, "ii", $user_id, $user_id);
                    mysqli_stmt_execute($replies_stmt);
                    $replies_result = mysqli_stmt_get_result($replies_stmt);
                    $replies = [];
                    while ($reply = mysqli_fetch_assoc($replies_result)) {
                        $replies[] = $reply;
                    }
                    ?>

                    <div class="comments-tabs">
                        <button class="comments-tab-btn active" data-tab="my-comments">
                            <i class="fas fa-comment"></i> Мои комментарии (<?= count($my_comments) ?>)
                        </button>
                        <button class="comments-tab-btn" data-tab="my-replies">
                            <i class="fas fa-reply"></i> Ответы на мои посты (<?= count($replies) ?>)
                        </button>
                    </div>

                    <!-- Мои комментарии -->
                    <div class="comments-section active" id="my-comments">
                        <?php if (!empty($my_comments)): ?>
                            <div class="comments-list">
                                <?php foreach ($my_comments as $comment): ?>
                                    <div class="comment-card">
                                        <div class="comment-header">
                                            <a href="news.php?id=<?= $comment['news_id'] ?>" class="comment-news-link">
                                                <i class="fas fa-newspaper"></i>
                                                <?= htmlspecialchars($comment['news_title']) ?>
                                            </a>
                                            <span class="comment-date">
                                                <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                                            </span>
                                        </div>
                                        <div class="comment-text">
                                            <?= nl2br(htmlspecialchars($comment['text'])) ?>
                                        </div>
                                        <div class="comment-actions">
                                            <span class="comment-likes">
                                                <i class="fas fa-heart"></i>
                                                <span><?= $comment['likes_count'] ?? 0 ?></span>
                                            </span>
                                            <a href="news.php?id=<?= $comment['news_id'] ?>#comment-<?= $comment['id'] ?>"
                                                class="view-btn">
                                                <i class="fas fa-external-link-alt"></i> Перейти к новости
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-comments">
                                <i class="fas fa-comment-slash"></i>
                                <p>Вы пока не оставляли комментариев</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Ответы на мои посты -->
                    <div class="comments-section" id="my-replies">
                        <?php if (!empty($replies)): ?>
                            <div class="comments-list">
                                <?php foreach ($replies as $reply): ?>
                                    <div class="comment-card reply-card">
                                        <div class="comment-header">
                                            <div class="reply-author">
                                                <img src="<?= htmlspecialchars($reply['author_avatar']) ?>" alt="Автор"
                                                    class="reply-avatar">
                                                <span class="reply-name"><?= htmlspecialchars($reply['author_login']) ?></span>
                                            </div>
                                            <a href="news.php?id=<?= $reply['news_id'] ?>" class="comment-news-link">
                                                <i class="fas fa-newspaper"></i>
                                                <?= htmlspecialchars($reply['news_title']) ?>
                                            </a>
                                        </div>
                                        <div class="comment-text">
                                            <?= nl2br(htmlspecialchars($reply['text'])) ?>
                                        </div>
                                        <div class="comment-actions">
                                            <span class="comment-date">
                                                <?= date('d.m.Y H:i', strtotime($reply['created_at'])) ?>
                                            </span>
                                            <a href="news.php?id=<?= $reply['news_id'] ?>#comment-<?= $reply['id'] ?>"
                                                class="view-btn">
                                                <i class="fas fa-external-link-alt"></i> Перейти к новости
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-comments">
                                <i class="fas fa-comment-slash"></i>
                                <p>На ваши посты пока никто не комментировал</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <script>
                        // Переключение вкладок комментариев
                        document.querySelectorAll('.comments-tab-btn').forEach(btn => {
                            btn.addEventListener('click', function () {
                                // Убираем active у всех кнопок
                                document.querySelectorAll('.comments-tab-btn').forEach(b => b.classList.remove('active'));
                                // Убираем active у всех секций
                                document.querySelectorAll('.comments-section').forEach(s => s.classList.remove('active'));

                                // Добавляем active к нажатой кнопке
                                this.classList.add('active');
                                // Показываем соответствующую секцию
                                const tabId = this.getAttribute('data-tab');
                                document.getElementById(tabId).classList.add('active');
                            });
                        });
                    </script>

                <?php elseif ($current_tab === 'notifications'): ?>
                    <!-- ========================================== -->
                    <!-- ВКЛАДКА УВЕДОМЛЕНИЙ -->
                    <!-- ========================================== -->
                    <div class="content-header">
                        <h2><i class="fas fa-bell"></i> Уведомления</h2>
                    </div>

                    <?php
                    // Получаем все уведомления пользователя
                    $notifications_sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
                    $notif_stmt = mysqli_prepare($conn, $notifications_sql);
                    mysqli_stmt_bind_param($notif_stmt, "i", $user_id);
                    mysqli_stmt_execute($notif_stmt);
                    $notifications_result = mysqli_stmt_get_result($notif_stmt);
                    $notifications = [];
                    while ($notif = mysqli_fetch_assoc($notifications_result)) {
                        $notifications[] = $notif;
                    }

                    // Отмечаем все как прочитанные
                    if (!empty($notifications)) {
                        markAllAsRead($conn, $user_id);
                    }
                    ?>

                    <div class="notifications-container">
                        <?php if (!empty($notifications)): ?>
                            <div class="notifications-list">
                                <?php foreach ($notifications as $notif): ?>
                                    <div class="notification-card <?= $notif['is_read'] ? '' : 'unread' ?>">
                                        <div class="notification-icon">
                                            <?php
                                            switch ($notif['type']) {
                                                case 'post_approved':
                                                    echo '<i class="fas fa-check-circle" style="color: #4CAF50;"></i>';
                                                    break;
                                                case 'post_rejected':
                                                    echo '<i class="fas fa-times-circle" style="color: #f44336;"></i>';
                                                    break;
                                                case 'post_deleted':
                                                    echo '<i class="fas fa-trash" style="color: #ff9800;"></i>';
                                                    break;
                                                case 'ticket_reply':
                                                    echo '<i class="fas fa-reply" style="color: #2196F3;"></i>';
                                                    break;
                                                case 'ticket_closed':
                                                    echo '<i class="fas fa-lock" style="color: #9e9e9e;"></i>';
                                                    break;
                                                case 'warning':
                                                    echo '<i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i>';
                                                    break;
                                                default:
                                                    echo '<i class="fas fa-bell" style="color: #2196F3;"></i>';
                                            }
                                            ?>
                                        </div>
                                        <div class="notification-content">
                                            <p><?= htmlspecialchars($notif['message']) ?></p>
                                            <span class="notification-date">
                                                <?= date('d.m.Y H:i', strtotime($notif['created_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <p>У вас пока нет уведомлений</p>
                            </div>
                        <?php endif; ?>
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