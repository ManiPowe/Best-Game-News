<?php
session_start();
require_once 'assets/app/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$target_user_id = (int) $_GET['id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $target_user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("Пользователь не найден!");
}

$is_logged_in = isset($_SESSION['user_id']);
$is_own_profile = ($is_logged_in && $_SESSION['user_id'] == $user['id']);
$is_creator = ($user['role'] === 'creator' || $user['role'] === 'admin');

$sql_reviews = "SELECT pr.*, u.login as author_login, u.avatar as author_avatar 
                FROM profile_reviews pr 
                JOIN users u ON pr.author_id = u.id 
                WHERE pr.target_user_id = ? 
                ORDER BY pr.created_at DESC";
$stmt_reviews = mysqli_prepare($conn, $sql_reviews);
mysqli_stmt_bind_param($stmt_reviews, "i", $target_user_id);
mysqli_stmt_execute($stmt_reviews);
$result_reviews = mysqli_stmt_get_result($stmt_reviews);
$reviews = [];
while ($review = mysqli_fetch_assoc($result_reviews)) {
    $reviews[] = $review;
}

$has_reviewed = false;
if ($is_logged_in && !$is_own_profile) {
    $sql_check = "SELECT id FROM profile_reviews WHERE author_id = ? AND target_user_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $_SESSION['user_id'], $target_user_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    $has_reviewed = (mysqli_stmt_num_rows($stmt_check) > 0);
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль <?= htmlspecialchars($user['login']) ?> - Best Game News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/light-theme.css">
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
                    <?php if ($is_logged_in): ?>
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
        <main>
            <div class="profile-container">
                <?php if ($is_creator): ?>
                    <div class="profile-layout">
                        <div class="profile-card">
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
                                </div>

                                <div class="profile-header-center">
                                    <div class="profile-info">
                                        <h2><?= htmlspecialchars($user['login']) ?></h2>

                                        <?php if ($user['role'] === 'creator' || $user['role'] === 'admin'): ?>
                                            <div class="creator-badge">
                                                <i class="fas fa-star"></i>
                                                <span>Создатель контента</span>
                                            </div>
                                        <?php endif; ?>

                                        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></p>
                                        <p><i class="fas fa-calendar"></i> Участник с
                                            <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                                    </div>
                                </div>

                                <div class="profile-header-right">
                                    <div class="profile-stats">
                                        <div class="stat-item">
                                            <div class="stat-value"><?= $posts_count ?? 0 ?></div>
                                            <div class="stat-label">Посты</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value"><?= $total_likes ?? 0 ?></div>
                                            <div class="stat-label">Лайки</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value"><?= $user['comments_count'] ?? 0 ?></div>
                                            <div class="stat-label">Комментарии</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($user['bio'])): ?>
                                <div class="profile-bio">
                                    <h3>О себе</h3>
                                    <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="posts-section">
                            <h3>Посты</h3>

                            <?php
                            $is_author = ($is_logged_in && $_SESSION['user_id'] == $target_user_id);

                            $posts_sql = "SELECT id, title, image, status, created_at 
              FROM news 
              WHERE author_id = ?";

                            if (!$is_author) {
                                $posts_sql .= " AND status = 'published'";
                            }

                            $posts_sql .= " ORDER BY created_at DESC";

                            $posts_stmt = mysqli_prepare($conn, $posts_sql);
                            mysqli_stmt_bind_param($posts_stmt, "i", $target_user_id);
                            mysqli_stmt_execute($posts_stmt);
                            $posts_result = mysqli_stmt_get_result($posts_stmt);
                            $posts = [];
                            while ($post = mysqli_fetch_assoc($posts_result)) {
                                $posts[] = $post;
                            }
                            ?>

                            <?php if (!empty($posts)): ?>
                                <div class="user-posts-list">
                                    <?php foreach ($posts as $post): ?>
                                        <div class="user-post-card-wrapper">
                                            <a href="news.php?id=<?= $post['id'] ?>" class="user-post-card">
                                                <?php if ($post['image']): ?>
                                                    <img src="<?= htmlspecialchars($post['image']) ?>"
                                                        alt="<?= htmlspecialchars($post['title']) ?>" class="post-cover">
                                                <?php else: ?>
                                                    <div class="post-cover-placeholder">
                                                        <i class="fas fa-newspaper"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="post-info">
                                                    <h4><?= htmlspecialchars($post['title']) ?></h4>

                                                    <?php if ($is_author): ?>
                                                        <span class="post-status status-<?= $post['status'] ?>">
                                                            <?php
                                                            switch ($post['status']) {
                                                                case 'published':
                                                                    echo 'Опубликовано';
                                                                    break;
                                                                case 'pending':
                                                                    echo 'На проверке';
                                                                    break;
                                                                case 'draft':
                                                                    echo 'Черновик';
                                                                    break;
                                                            }
                                                            ?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <span
                                                        class="post-date"><?= date('d.m.Y', strtotime($post['created_at'])) ?></span>
                                                </div>
                                            </a>

                                            <?php if ($is_author): ?>
                                                <div class="post-actions">
                                                    <?php if ($post['status'] === 'draft'): ?>
                                                        <form action="assets/app/publish_news.php" method="POST" style="display: inline;">
                                                            <input type="hidden" name="news_id" value="<?= $post['id'] ?>">
                                                            <button type="submit" class="action-btn publish-btn"
                                                                title="Отправить на проверку">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <a href="edit_news.php?id=<?= $post['id'] ?>" class="action-btn edit-btn"
                                                        title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="assets/app/delete_news.php" method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Удалить эту новость?');">
                                                        <input type="hidden" name="news_id" value="<?= $post['id'] ?>">
                                                        <button type="submit" class="action-btn delete-btn" title="Удалить">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-posts">Пользователь ещё не создал ни одного поста.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="avatar">
                                <?php if ($user['avatar'] === 'assets/Media/Photo/man.png' || empty($user['avatar'])): ?>
                                    <i class="fas fa-user"></i>
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар">
                                <?php endif; ?>
                            </div>
                            <div class="profile-info">
                                <h2><?= htmlspecialchars($user['login']) ?></h2>
                                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                                <p><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></p>
                                <p><i class="fas fa-calendar"></i> Участник с
                                    <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                                </p>
                                <?php if (!empty($user['bio'])): ?>
                                    <p class="profile-bio-text"><i class="fas fa-info-circle"></i>
                                        <?= nl2br(htmlspecialchars($user['bio'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($user['bio'])): ?>
                            <div class="profile-bio">
                                <h3>О себе</h3>
                                <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($is_creator): ?>
                    <div class="reviews-section">
                        <h3>Отзывы (<?= count($reviews) ?>)</h3>

                        <?php if (isset($_SESSION['review_success'])): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($_SESSION['review_success']) ?>
                            </div>
                            <?php unset($_SESSION['review_success']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['review_error'])): ?>
                            <div class="alert alert-error">
                                <?= $_SESSION['review_error'] ?>
                            </div>
                            <?php unset($_SESSION['review_error']); ?>
                        <?php endif; ?>

                        <?php if ($is_logged_in && !$is_own_profile && !$has_reviewed): ?>
                            <form action="assets/app/add_review.php" method="POST" class="review-form">
                                <input type="hidden" name="target_user_id" value="<?= $user['id'] ?>">
                                <textarea name="review_text" placeholder="Напишите ваш отзыв о создателе..." required
                                    maxlength="1000"></textarea>
                                <button type="submit" class="submit-review-btn">
                                    <i class="fas fa-paper-plane"></i> Отправить отзыв
                                </button>
                            </form>
                        <?php elseif (!$is_logged_in): ?>
                            <div class="auth-prompt">
                                <p>Хотите оставить отзыв? <a href="login.php">Войдите</a> или <a
                                        href="reg.php">зарегистрируйтесь</a>!</p>
                            </div>
                        <?php elseif ($is_own_profile): ?>
                            <p class="self-note">Вы не можете оставлять отзывы самому себе.</p>
                        <?php elseif ($has_reviewed): ?>
                            <p class="already-reviewed">Вы уже оставляли отзыв этому пользователю.</p>
                        <?php endif; ?>

                        <?php if (!empty($reviews)): ?>
                            <div class="reviews-list">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="review-card">
                                        <div class="review-header">
                                            <img src="<?= htmlspecialchars($review['author_avatar']) ?>" alt="Аватар"
                                                class="review-avatar">
                                            <div class="review-author">
                                                <a href="profile.php?id=<?= $review['author_id'] ?>" class="review-author-name">
                                                    <?= htmlspecialchars($review['author_login']) ?>
                                                </a>
                                                <span
                                                    class="review-date"><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></span>
                                            </div>
                                        </div>
                                        <div class="review-text">
                                            <?= nl2br(htmlspecialchars($review['text'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-reviews">Пока нет отзывов. Будьте первым!</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="user-comments-section">
                    <h3>Последние комментарии</h3>
                    <?php
                    $sql_comments = "SELECT c.*, n.title as news_title, n.id as news_id 
                                FROM comments c 
                                JOIN news n ON c.news_id = n.id 
                                WHERE c.user_id = ? 
                                ORDER BY c.created_at DESC 
                                LIMIT 10";
                    $stmt_comments = mysqli_prepare($conn, $sql_comments);
                    mysqli_stmt_bind_param($stmt_comments, "i", $user['id']);
                    mysqli_stmt_execute($stmt_comments);
                    $result_comments = mysqli_stmt_get_result($stmt_comments);
                    $comments = [];
                    while ($comment = mysqli_fetch_assoc($result_comments)) {
                        $comments[] = $comment;
                    }
                    ?>

                    <?php if (!empty($comments)): ?>
                        <div class="user-comments-list">
                            <?php foreach ($comments as $comment): ?>
                                <div class="user-comment-card">
                                    <div class="comment-header">
                                        <a href="news.php?id=<?= $comment['news_id'] ?>" class="comment-news-link">
                                            <i class="fas fa-newspaper"></i> <?= htmlspecialchars($comment['news_title']) ?>
                                        </a>
                                        <span
                                            class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                                    </div>
                                    <div class="comment-text">
                                        <?= nl2br(htmlspecialchars($comment['text'])) ?>
                                    </div>
                                    <div class="comment-actions">
                                        <span class="comment-likes">
                                            <i class="fas fa-heart"></i> <?= $comment['likes'] ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-activity">Пользователь пока не оставлял комментариев.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
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