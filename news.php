<?php
session_start();
require_once 'assets/app/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$news_id = (int)$_GET['id'];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$session_id = session_id();
$user_id_for_view = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/news.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <a href="profile.php?id=<?= $news['author_id'] ?>" class="news-author">
                        <img src="<?= htmlspecialchars($news['author_avatar']) ?>" alt="Автор">
                        <span><?= htmlspecialchars($news['author_login']) ?></span>
                    </a>
                    <span class="news-date"><?= date('d.m.Y H:i', strtotime($news['created_at'])) ?></span>
                    <span class="news-views"><i class="fas fa-eye"></i> <?= $news['views'] ?></span>
                </div>

                <?php if ($news['image']): ?>
                    <img src="<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>"
                        class="news-image">
                <?php endif; ?>

                <div class="news-content">
                    <?= nl2br(htmlspecialchars($news['content'])) ?>
                </div>

                <div class="news-actions">
                    <button class="action-btn like-btn" data-news-id="<?= $news['id'] ?>">
                        <i class="fas fa-heart"></i> Нравится
                    </button>
                    <button class="action-btn favorite-btn" data-news-id="<?= $news['id'] ?>">
                        <i class="fas fa-bookmark"></i> В избранное
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
                        <p>Хотите оставить комментарий? <a href="login.php">Войдите</a> или <a
                                href="reg.php">зарегистрируйтесь</a>!</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($comments)): ?>
                    <div class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-card">
                                <div class="comment-header">
                                    <a href="profile.php?id=<?= $comment['user_id'] ?>" class="comment-author">
                                        <img src="<?= htmlspecialchars($comment['avatar']) ?>" alt="Автор">
                                        <span><?= htmlspecialchars($comment['login']) ?></span>
                                    </a>
                                    <span
                                        class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                                </div>
                                <div class="comment-text">
                                    <?= nl2br(htmlspecialchars($comment['text'])) ?>
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
</body>

</html>