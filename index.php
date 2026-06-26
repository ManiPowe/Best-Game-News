<?php
session_start();
require_once 'assets/app/db.php';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/light-theme.css">
    <link rel="icon" href="/assets/Media/Photo/asd.png">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
    <title>Best Game News</title>
</head>

<body>
    <script src="/assets/js/theme-init.js"></script>
    <script src="/assets/js/no-cache.js"></script>
    <script src="/assets/js/theme.js" defer></script>
    <script src="/assets/js/news_actions.js"></script>
    <script src="/assets/JS/main_slider.js"></script>

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

                <?php if ($user_role === 'creator' || $user_role === 'moderator' || $user_role === 'admin'): ?>
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
                <?php
                require_once __DIR__ . '/assets/app/notifications.php';
                $unread_count = isset($_SESSION['user_id']) ? getUnreadCount($conn, $_SESSION['user_id']) : 0;
                ?>

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
        <div class="main-container">
            <div class="content-wrapper">
                <div class="left-sidebar">
                    <div class="popular-authors">
                        <h3>Популярные авторы</h3>
                        <?php
                        $sql = "SELECT u.id, u.login, u.avatar, u.comments_count,
                        (SELECT COUNT(*) FROM news n WHERE n.author_id = u.id AND n.status = 'published') as posts_count,
                        (SELECT MAX(n.likes_count) FROM news n WHERE n.author_id = u.id AND n.status = 'published') as top_liked_post
                        FROM users u
                        WHERE u.role IN ('creator', 'moderator', 'admin')
                        HAVING posts_count > 0
                        ORDER BY posts_count DESC 
                        LIMIT 10";

                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0):
                            while ($author = mysqli_fetch_assoc($result)):
                                $top_likes = $author['top_liked_post'] ?? 0;
                                ?>
                                <a href="/profile/<?= $author['id'] ?>" class="author-card">
                                    <img src="<?= htmlspecialchars($author['avatar']) ?>"
                                        alt="Фото <?= htmlspecialchars($author['login']) ?>">
                                    <div class="author-info">
                                        <h4><?= htmlspecialchars($author['login']) ?></h4>
                                        <p>Постов: <?= $author['posts_count'] ?> | Комментариев:
                                            <?= $author['comments_count'] ?>
                                        </p>
                                        <p>Самый залайканный пост: <?= $top_likes ?> лайков</p>
                                    </div>
                                </a>
                                <?php
                            endwhile;
                        else:
                            ?>
                            <p style="color: #b0b0b0; text-align: center; padding: 10px;">Пока нет авторов</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="main-content">
                    <div class="hero-section">
                        <div class="hero-slider">
                            <div class="slider-container">
                                <?php
                                // Получаем избранные новости для слайдера
                                $slider_sql = "SELECT n.*, u.login as author_login 
                           FROM news n 
                           JOIN users u ON n.author_id = u.id 
                           WHERE n.is_featured = 1 AND n.status = 'published' 
                           ORDER BY n.created_at DESC 
                           LIMIT 15";
                                $slider_result = mysqli_query($conn, $slider_sql);

                                $has_featured = $slider_result && mysqli_num_rows($slider_result) > 0;
                                $slide_index = 0;

                                if ($has_featured):
                                    while ($slide = mysqli_fetch_assoc($slider_result)):
                                        $image_path = $slide['image'] ?? '';
                                        $image_exists = $image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path);
                                        $display_image = $image_exists ? '/' . ltrim($image_path, '/') : '/assets/Media/Photo/Заглушка.jpg';
                                        $active_class = $slide_index === 0 ? 'active' : '';
                                        ?>
                                        <div class="hero-card slide <?= $active_class ?>">
                                            <img src="<?= htmlspecialchars($display_image) ?>" loading="lazy"
                                                alt="<?= htmlspecialchars($slide['title']) ?>">
                                            <div class="hero-text">
                                                <h2>
                                                    <?= htmlspecialchars($slide['title']) ?>
                                                </h2>
                                                <p>
                                                    <?= htmlspecialchars(mb_substr($slide['short_description'] ?? $slide['content'], 0, 150)) ?>...
                                                </p>
                                                <a href="news.php?id=<?= $slide['id'] ?>">Подробнее...</a>
                                            </div>
                                        </div>
                                        <?php
                                        $slide_index++;
                                    endwhile;
                                else:
                                    // Если избранных нет — показываем дефолтные слайды
                                    ?>
                                    <div class="hero-card slide active">
                                        <img src="/assets/Media/Photo/Заглушка.jpg" loading="lazy" alt="DOTA 2">
                                        <div class="hero-text">
                                            <h2>Добро пожаловать!</h2>
                                            <p>Добавьте новости в слайдер через админ-панель, чтобы они появились здесь</p>
                                            <a href="admin/admin.php?tab=featured">Перейти в админку</a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button class="slider-btn prev-btn">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="slider-btn next-btn">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <div class="slider-indicators"></div>
                        </div>
                    </div>

                    <div class="news-container">
                        <div class="section-header">
                            <div class="todays-news">
                                <h2>Новости дня</h2>

                                <?php
                                $user_id = $_SESSION['user_id'] ?? 0;

                                $today_sql = "SELECT n.id, n.title, n.short_description, n.image, n.category, n.game_id, 
                 n.likes_count, n.views, n.created_at,
                 u.login as author_login, u.avatar as author_avatar,
                 g.name as game_name, g.icon as game_icon,
                 (SELECT COUNT(*) FROM favorites WHERE news_id = n.id) as favorites_count,
                 (SELECT COUNT(*) FROM news_likes WHERE news_id = n.id AND user_id = ?) as user_liked,
                 (SELECT COUNT(*) FROM favorites WHERE news_id = n.id AND user_id = ?) as user_favorited
          FROM news n
          JOIN users u ON n.author_id = u.id
          LEFT JOIN games g ON n.game_id = g.id
          WHERE n.status = 'published' 
            AND DATE(n.created_at) = CURDATE()
          ORDER BY n.created_at DESC";

                                $stmt = mysqli_prepare($conn, $today_sql);
                                mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
                                mysqli_stmt_execute($stmt);
                                $today_result = mysqli_stmt_get_result($stmt);

                                if ($today_result && mysqli_num_rows($today_result) > 0): ?>
                                    <div class="todays-news-list">
                                        <?php while ($news = mysqli_fetch_assoc($today_result)): ?>
                                            <article class="todays-card"
                                                onclick="window.location.href='news.php?id=<?= $news['id'] ?>'"
                                                style="cursor: pointer;">
                                                <div class="todays-image">
                                                    <?php
                                                    // Проверяем существует ли файл картинки
                                                    $image_path = $news['image'] ?? '';
                                                    $image_exists = $image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path);
                                                    $display_image = $image_exists ? $image_path : '/assets/Media/Photo/Заглушка.jpg';
                                                    ?>
                                                    <img src="<?= htmlspecialchars($display_image) ?>"
                                                        alt="<?= htmlspecialchars($news['title']) ?>">

                                                    <?php if ($news['game_name']): ?>
                                                        <div class="game-badge">
                                                            <?php if ($news['game_icon']): ?>
                                                                <img src="<?= htmlspecialchars($news['game_icon']) ?>"
                                                                    alt="<?= htmlspecialchars($news['game_name']) ?>">
                                                            <?php endif; ?>
                                                            <span><?= htmlspecialchars($news['game_name']) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="todays-content">
                                                    <h3 class="todays-title"><?= htmlspecialchars($news['title']) ?></h3>

                                                    <?php if ($news['short_description']): ?>
                                                        <p class="todays-description">
                                                            <?= htmlspecialchars($news['short_description']) ?>
                                                        </p>
                                                    <?php endif; ?>

                                                    <div class="todays-meta">
                                                        <div class="todays-author">
                                                            <img src="<?= htmlspecialchars($news['author_avatar']) ?>"
                                                                alt="Автор">
                                                            <span>Автор: <?= htmlspecialchars($news['author_login']) ?></span>
                                                        </div>
                                                        <span class="todays-date">
                                                            Опубликовано: <?= date('d.m.Y', strtotime($news['created_at'])) ?>
                                                        </span>
                                                    </div>

                                                    <div class="todays-actions">
                                                        <a href="news.php?id=<?= $news['id'] ?>" class="read-more"
                                                            onclick="event.stopPropagation()">Читать далее</a>
                                                        <div class="todays-stats">
                                                            <button
                                                                class="stat-btn like-btn <?= $news['user_liked'] ? 'active' : '' ?>"
                                                                onclick="event.stopPropagation(); toggleLike(<?= $news['id'] ?>, this)">
                                                                <i class="fas fa-heart"></i>
                                                                <span><?= $news['likes_count'] ?? 0 ?></span>
                                                            </button>
                                                            <button
                                                                class="stat-btn favorite-btn <?= $news['user_favorited'] ? 'active' : '' ?>"
                                                                onclick="event.stopPropagation(); toggleFavorite(<?= $news['id'] ?>, this)">
                                                                <i class="fas fa-bookmark"></i>
                                                                <span><?= $news['favorites_count'] ?? 0 ?></span>
                                                            </button>
                                                            <span class="stat">
                                                                <i class="fas fa-eye"></i>
                                                                <span><?= $news['views'] ?></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-news">
                                        <i class="fas fa-newspaper"></i>
                                        <p>Сегодня новостей ещё нет. Загляните позже!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="right-sidebar">
                    <div class="game-marquee">
                        <h3>Популярные игры</h3>

                        <button class="marquee-btn up-btn" aria-label="Прокрутить вверх">▲</button>

                        <div class="marquee-viewport">
                            <div class="marquee-content">
                                <?php
                                $popular_games_sql = "SELECT id, name, icon FROM games ORDER BY id ASC LIMIT 15";
                                $popular_games_result = mysqli_query($conn, $popular_games_sql);

                                if ($popular_games_result && mysqli_num_rows($popular_games_result) > 0):
                                    while ($game = mysqli_fetch_assoc($popular_games_result)):
                                        ?>
                                        <a href="/category/games?game_id=<?= $game['id'] ?>" class="marquee-item">
                                            <span class="game-name"><?= htmlspecialchars($game['name']) ?></span>
                                            <?php if ($game['icon']): ?>
                                                <img class="game-icon" src="<?= htmlspecialchars($game['icon']) ?>"
                                                    alt="<?= htmlspecialchars($game['name']) ?>">
                                            <?php endif; ?>
                                        </a>
                                        <?php
                                    endwhile;
                                else:
                                    ?>
                                    <p style="color: #777; text-align: center; padding: 20px;">Нет популярных игр</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button class="marquee-btn down-btn" aria-label="Прокрутить вниз">▼</button>
                    </div>
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
</body>

</html>