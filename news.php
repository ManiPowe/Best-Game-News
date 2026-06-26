<?php
session_start();
require_once 'assets/app/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$news_id = (int) $_GET['id'];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$session_id = session_id();
$user_id_for_view = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

$check_view_sql = "SELECT id FROM news_views WHERE news_id = ? AND session_id = ?";
$stmt_check_view = mysqli_prepare($conn, $check_view_sql);
mysqli_stmt_bind_param($stmt_check_view, "is", $news_id, $session_id);
mysqli_stmt_execute($stmt_check_view);
mysqli_stmt_store_result($stmt_check_view);

if (mysqli_stmt_num_rows($stmt_check_view) === 0) {
    $insert_view_sql = "INSERT INTO news_views (news_id, session_id, user_id, ip_address) VALUES (?, ?, ?, ?)";
    $stmt_insert_view = mysqli_prepare($conn, $insert_view_sql);
    mysqli_stmt_bind_param($stmt_insert_view, "isis", $news_id, $session_id, $user_id_for_view, $ip_address);
    mysqli_stmt_execute($stmt_insert_view);

    mysqli_query($conn, "UPDATE news SET views = views + 1 WHERE id = $news_id");
}

$sql = "SELECT n.*, u.login as author_login, u.avatar as author_avatar, g.name as game_name, g.icon as game_icon
        FROM news n 
        JOIN users u ON n.author_id = u.id 
        LEFT JOIN games g ON n.game_id = g.id
        WHERE n.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $news_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$news = mysqli_fetch_assoc($result);

if (!$news) {
    die("Новость не найдена!");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();
$user_id_for_view = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

$check_view_sql = "SELECT id FROM news_views WHERE news_id = ? AND session_id = ?";
$stmt_check_view = mysqli_prepare($conn, $check_view_sql);
mysqli_stmt_bind_param($stmt_check_view, "is", $news_id, $session_id);
mysqli_stmt_execute($stmt_check_view);
mysqli_stmt_store_result($stmt_check_view);

if (mysqli_stmt_num_rows($stmt_check_view) === 0) {
    $insert_view_sql = "INSERT INTO news_views (news_id, session_id, user_id, ip_address) VALUES (?, ?, ?, ?)";
    $stmt_insert_view = mysqli_prepare($conn, $insert_view_sql);
    mysqli_stmt_bind_param($stmt_insert_view, "isis", $news_id, $session_id, $user_id_for_view, $ip_address);
    mysqli_stmt_execute($stmt_insert_view);

    mysqli_query($conn, "UPDATE news SET views = views + 1 WHERE id = $news_id");
}

$sql_comments = "SELECT c.*, u.login, u.avatar 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.news_id = ? 
                ORDER BY c.created_at DESC";
$stmt_comments = mysqli_prepare($conn, $sql_comments);
mysqli_stmt_bind_param($stmt_comments, "i", $news_id);
mysqli_stmt_execute($stmt_comments);
$result_comments = mysqli_stmt_get_result($stmt_comments);
$comments = [];
while ($comment = mysqli_fetch_assoc($result_comments)) {
    $comments[] = $comment;
}

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - Best Game News</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/news.css">
    <link rel="stylesheet" href="/css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
</head>

<body>
    <script src="/assets/js/theme-init.js"></script>
    <script src="/assets/js/no-cache.js"></script>
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
                <a class="logo-link" href="/index.php">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="/index.php">Главная</a>
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
                    <a href="/create_news.php" class="create-news-btn">
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

    <main>
        <div class="news-container">
            <article class="news-article">
                <h1><?= htmlspecialchars($news['title']) ?></h1>

                <?php if ($news['game_name']): ?>
                    <div class="news-game-badge">
                        <?php if ($news['game_icon']): ?>
                            <img src="<?= htmlspecialchars($news['game_icon']) ?>"
                                alt="<?= htmlspecialchars($news['game_name']) ?>">
                        <?php endif; ?>
                        <span>
                            <?= htmlspecialchars($news['game_name']) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <div class="news-meta">
                    <a href="/profile/<?= $news['author_id'] ?>" class="news-author">
                        <?php
                        $author_avatar = $news['author_avatar'] ?? '/assets/Media/Photo/man.png';
                        if (strpos($author_avatar, 'http') !== 0 && strpos($author_avatar, '/') !== 0) {
                            $author_avatar = '/' . $author_avatar;
                        }
                        ?>
                        <img src="<?= htmlspecialchars($author_avatar) ?>" alt="Автор"
                            onerror="this.src='/assets/Media/Photo/man.png'">
                        <span><?= htmlspecialchars($news['author_login']) ?></span>
                    </a>
                    <span class="news-date"><?= date('d.m.Y H:i', strtotime($news['created_at'])) ?></span>
                    <span class="news-views"><i class="fas fa-eye"></i> <?= $news['views'] ?></span>
                </div>

                <?php
                $news_image = $news['image'] ?? '';
                if ($news_image && strpos($news_image, 'http') !== 0 && strpos($news_image, '/') !== 0) {
                    $news_image = '/' . $news_image;
                }
                $image_file = $_SERVER['DOCUMENT_ROOT'] . $news_image;
                $image_exists = $news_image && file_exists($image_file);
                $display_image = $image_exists ? $news_image : '/assets/Media/Photo/Заглушка.jpg';
                ?>
                <img src="<?= htmlspecialchars($display_image) ?>" alt="<?= htmlspecialchars($news['title']) ?>"
                    class="news-image" onerror="this.src='/assets/Media/Photo/Заглушка.jpg'">

                <div class="news-content">
                    <?= nl2br(htmlspecialchars($news['content'])) ?>
                </div>

                <?php
                $is_liked = false;
                $is_favorited = false;

                if ($is_logged_in) {
                    $like_check_sql = "SELECT id FROM news_likes WHERE user_id = ? AND news_id = ?";
                    $like_check_stmt = mysqli_prepare($conn, $like_check_sql);
                    mysqli_stmt_bind_param($like_check_stmt, "ii", $_SESSION['user_id'], $news_id);
                    mysqli_stmt_execute($like_check_stmt);
                    mysqli_stmt_store_result($like_check_stmt);
                    $is_liked = (mysqli_stmt_num_rows($like_check_stmt) > 0);

                    $fav_check_sql = "SELECT id FROM favorites WHERE user_id = ? AND news_id = ?";
                    $fav_check_stmt = mysqli_prepare($conn, $fav_check_sql);
                    mysqli_stmt_bind_param($fav_check_stmt, "ii", $_SESSION['user_id'], $news_id);
                    mysqli_stmt_execute($fav_check_stmt);
                    mysqli_stmt_store_result($fav_check_stmt);
                    $is_favorited = (mysqli_stmt_num_rows($fav_check_stmt) > 0);
                }

                $likes_count = $news['likes_count'] ?? 0;

                $fav_count_sql = "SELECT COUNT(*) as favorites_count FROM favorites WHERE news_id = ?";
                $fav_count_stmt = mysqli_prepare($conn, $fav_count_sql);
                mysqli_stmt_bind_param($fav_count_stmt, "i", $news_id);
                mysqli_stmt_execute($fav_count_stmt);
                $fav_count_result = mysqli_stmt_get_result($fav_count_stmt);
                $favorites_count = mysqli_fetch_assoc($fav_count_result)['favorites_count'] ?? 0;
                ?>

                <div class="news-actions">
                    <button class="action-btn like-btn <?= $is_liked ? 'active' : '' ?>"
                        data-news-id="<?= $news['id'] ?>">
                        <i class="fas fa-heart"></i>
                        <span class="action-count"><?= $likes_count ?></span>
                    </button>

                    <button class="action-btn favorite-btn <?= $is_favorited ? 'active' : '' ?>"
                        data-news-id="<?= $news['id'] ?>">
                        <i class="fas fa-bookmark"></i>
                        <span class="action-count"><?= $favorites_count ?></span>
                    </button>
                </div>
            </article>

            <div class="comments-section">
                <h3>Комментарии (<?= count($comments) ?>)</h3>

                <?php if ($is_logged_in): ?>
                    <form action="assets/app/add_comment.php" method="POST" class="comment-form">
                        <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                        <textarea name="comment_text" placeholder="Напишите комментарий..." required></textarea>
                        <button type="submit">Отправить</button>
                    </form>
                <?php else: ?>
                    <div class="auth-prompt">
                        <p>Хотите оставить комментарий? <a href="/login">Войдите</a> или <a
                                href="/reg">зарегистрируйтесь</a>!</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($comments)): ?>
                    <div class="comments-list">
                        <?php foreach ($comments as $comment):
                            // Проверяем, лайкал ли текущий пользователь
                            $is_comment_liked = false;
                            if ($is_logged_in) {
                                $comment_like_check = mysqli_prepare($conn, "SELECT id FROM comment_likes WHERE user_id = ? AND comment_id = ?");
                                if ($comment_like_check) {
                                    mysqli_stmt_bind_param($comment_like_check, "ii", $_SESSION['user_id'], $comment['id']);
                                    mysqli_stmt_execute($comment_like_check);
                                    mysqli_stmt_store_result($comment_like_check);
                                    $is_comment_liked = mysqli_stmt_num_rows($comment_like_check) > 0;
                                    mysqli_stmt_close($comment_like_check);
                                }
                            }
                            ?>
                            <div class="comment-card">
                                <div class="comment-header">
                                    <a href="/profile/<?= $comment['user_id'] ?>" class="comment-author">
                                        <?php
                                        $comment_avatar = $comment['avatar'] ?? '/assets/Media/Photo/man.png';
                                        if (strpos($comment_avatar, 'http') !== 0 && strpos($comment_avatar, '/') !== 0) {
                                            $comment_avatar = '/' . $comment_avatar;
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($comment_avatar) ?>" alt="Автор"
                                            onerror="this.src='/assets/Media/Photo/man.png'">
                                        <span><?= htmlspecialchars($comment['login']) ?></span>
                                    </a>
                                    <span
                                        class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                                </div>
                                <div class="comment-text">
                                    <?= nl2br(htmlspecialchars($comment['text'])) ?>
                                </div>
                                <div class="comment-actions">
                                    <button class="comment-like-btn <?= $is_comment_liked ? 'active' : '' ?>"
                                        data-comment-id="<?= $comment['id'] ?>">
                                        <i class="fas fa-heart"></i>
                                        <span class="comment-like-count"><?= $comment['likes_count'] ?? 0 ?></span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-comments">Пока нет комментариев. Будьте первым!</p>
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
    <script src="/assets/js/like_favorite.js"></script>
    <script src="/assets/js/theme.js"></script>
</body>

</html>