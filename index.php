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
    <link rel="icon" href="/assets/Media/Photo/asd.png">
    <title>Best Game News</title>
</head>

<body>
    <script src="/assets/JS/main_slider.js"></script>
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
                <a href="#">Игры</a>
                <a href="">Новости</a>
                <a href="#">Статьи</a>
                <a href="#">Видео</a>
                <a href="#">Прохождения</a>
                <a href="#">Помощь</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $check_role_sql = "SELECT role FROM users WHERE id = ?";
                    $stmt_role = mysqli_prepare($conn, $check_role_sql);
                    mysqli_stmt_bind_param($stmt_role, "i", $_SESSION['user_id']);
                    mysqli_stmt_execute($stmt_role);
                    $result_role = mysqli_stmt_get_result($stmt_role);
                    $user_role = mysqli_fetch_assoc($result_role)['role'];

                    if ($user_role === 'creator' || $user_role === 'admin'):
                        ?>
                        <a href="create_news.php" class="create-news-btn"> Создать новость
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
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
        <div class="main-container">
            <div class="content-wrapper">
                <div class="left-sidebar">
                    <div class="popular-authors">
                        <h3>Популярные авторы</h3>
                        <?php
                        require_once('assets/app/db.php');

                        $sql = "SELECT id, login, avatar, posts_count, comments_count, top_liked_comment 
                FROM users 
                WHERE role IN ('creator', 'admin')
                ORDER BY posts_count DESC 
                LIMIT 10";

                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0):
                            while ($author = mysqli_fetch_assoc($result)):
                                ?>
                                <a href="profile.php?id=<?= $author['id'] ?>" class="author-card">
                                    <img src="<?= htmlspecialchars($author['avatar']) ?>"
                                        alt="Фото <?= htmlspecialchars($author['login']) ?>">
                                    <div class="author-info">
                                        <h4><?= htmlspecialchars($author['login']) ?></h4>
                                        <p>Постов: <?= $author['posts_count'] ?> | Комментариев:
                                            <?= $author['comments_count'] ?>
                                        </p>
                                        <p>Самый залайканный комментарий: <?= $author['top_liked_comment'] ?> лайков</p>
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
                                <div class="hero-card slide active">
                                    <img src="/assets/Media/Photo/dota2.png" loading="lazy" alt="DOTA 2">
                                    <div class="hero-text">
                                        <h2>DOTA 2</h2>
                                        <p>В игре DOTA 2 стартовал новый ивент, приуроченный коллаборацией DOTA
                                            2 X
                                            Monster Hunter!</p>
                                        <a href="#">Подробнее...</a>
                                    </div>
                                </div>
                                <div class="hero-card slide">
                                    <img src="/assets/Media/Photo/atomic_heart_sl.jpg" loading="lazy"
                                        alt="Atomic Heart">
                                    <div class="hero-text">
                                        <h2>Atomic Heart</h2>
                                        <p>Mundfish показала парочку скриншотов грядущего дополнения DLC для
                                            Atomic
                                            Heart</p>
                                        <a href="#">Подробнее...</a>
                                    </div>
                                </div>
                                <div class="hero-card slide">
                                    <img src="/assets/Media/Photo/Calofduty.jpg" loading="lazy" alt="Call of Duty">
                                    <div class="hero-text">
                                        <h2>Call of Duty</h2>
                                        <p>Вышел новый трейлер MWIII</p>
                                        <a href="#">Подробнее...</a>
                                    </div>
                                </div>
                            </div>
                            <button class="slider-btn prev-btn" aria-label="Предыдущий слайд">&lt;</button>
                            <button class="slider-btn next-btn" aria-label="Следующий слайд">&gt;</button>
                            <div class="slider-indicators">
                            </div>
                        </div>
                    </div>
                    <div class="news-container">
                        <div class="section-header">
                            <h2>Новости дня</h2>
                        </div>
                        <div class="news-list">
                            <article>
                                <img src="/assets/Media/Photo/atomic.jpg" alt="Atomic Heart">
                                <div class="news-content">
                                    <div class="content-main">
                                        <h3>Atomic Heart</h3>
                                        <p>Mundfish показала парочку скриншотов<br>грядущего дополнения DLC для
                                            Atomic
                                            Heart</p>
                                        <a href="#">Читать далее</a>
                                    </div>
                                    <div class="content-sidebar">
                                        <div class="news-meta">
                                            <div class="meta-item">
                                                <span class="meta-label">Автор:</span>
                                                <span class="meta-value">ManiPowe?</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="meta-label">Опубликовано:</span>
                                                <span class="meta-value">26.10.2025</span>
                                            </div>
                                        </div>
                                        <div class="news-actions">
                                            <div class="action-btn like-btn">
                                                <img src="/assets/Media/Photo/icons8-лайк-с-заливкой-50.png"
                                                    alt="Нравится">
                                                <span class="action-count">17</span>
                                            </div>
                                            <div class="action-btn favorite-btn">
                                                <img src="/assets/Media/Photo/icons8-звезда-50.png" alt="В избранное">
                                                <span class="action-count">3</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <article>
                                <img src="/assets/Media/Photo/atomic.jpg" alt="Atomic Heart">
                                <div class="news-content">
                                    <div class="content-main">
                                        <h3>Atomic Heart</h3>
                                        <p>Mundfish показала парочку скриншотов<br>грядущего дополнения DLC для
                                            Atomic
                                            Heart</p>
                                        <a href="#">Читать далее</a>
                                    </div>
                                    <div class="content-sidebar">
                                        <div class="news-meta">
                                            <div class="meta-item">
                                                <span class="meta-label">Автор:</span>
                                                <span class="meta-value">ManiPowe?</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="meta-label">Опубликовано:</span>
                                                <span class="meta-value">26.10.2025</span>
                                            </div>
                                        </div>
                                        <div class="news-actions">
                                            <div class="action-btn like-btn">
                                                <img src="/assets/Media/Photo/icons8-лайк-с-заливкой-50.png"
                                                    alt="Нравится">
                                                <span class="action-count">17</span>
                                            </div>
                                            <div class="action-btn favorite-btn">
                                                <img src="/assets/Media/Photo/icons8-звезда-50.png" alt="В избранное">
                                                <span class="action-count">3</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <article>
                                <img src="/assets/Media/Photo/atomic.jpg" alt="Atomic Heart">
                                <div class="news-content">
                                    <div class="content-main">
                                        <h3>Atomic Heart</h3>
                                        <p>Mundfish показала парочку скриншотов<br>грядущего дополнения DLC для
                                            Atomic
                                            Heart</p>
                                        <a href="#">Читать далее</a>
                                    </div>
                                    <div class="content-sidebar">
                                        <div class="news-meta">
                                            <div class="meta-item">
                                                <span class="meta-label">Автор:</span>
                                                <span class="meta-value">ManiPowe?</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="meta-label">Опубликовано:</span>
                                                <span class="meta-value">26.10.2025</span>
                                            </div>
                                        </div>
                                        <div class="news-actions">
                                            <div class="action-btn like-btn">
                                                <img src="/assets/Media/Photo/icons8-лайк-с-заливкой-50.png"
                                                    alt="Нравится">
                                                <span class="action-count">17</span>
                                            </div>
                                            <div class="action-btn favorite-btn">
                                                <img src="/assets/Media/Photo/icons8-звезда-50.png" alt="В избранное">
                                                <span class="action-count">3</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>

                <div class="right-sidebar">
                    <div class="game-marquee">
                        <h3>Популярные игры</h3>

                        <button class="marquee-btn up-btn" aria-label="Прокрутить вверх">▲</button>

                        <div class="marquee-viewport">
                            <div class="marquee-content">
                                <a href="#" class="marquee-item">
                                    <span class="game-name">DOTA 2</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-дота-2.svg" alt="DOTA 2">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Counter-Strike 2</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-counter-strike.svg"
                                        alt="Counter-Strike 2">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Valorant</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-valorant.svg" alt="Valorant">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Apex Legends</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-riot-games.svg"
                                        alt="Apex Legends">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Fortnite</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-fortnite.svg" alt="Fortnite">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Call of Duty</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-call-of-duty-black-ops-3.svg"
                                        alt="Call of Duty">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">League of Legends</span>
                                    <img class="game-icon"
                                        src="/assets/Media/ico/icons8-адский-дракон-league-of-legends.svg"
                                        alt="League of Legends">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Overwatch 2</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-overwatch.svg"
                                        alt="Overwatch 2">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Genshin Impact</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-genshin-impact-logo.svg"
                                        alt="Genshin Impact">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">World of Warcraft</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-world-of-warcraft.svg"
                                        alt="World of Warcraft">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Among Us</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-among-us.svg" alt="Among Us">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Minecraft</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-куб-травы-из-minecraft.svg"
                                        alt="Minecraft">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Grand Theft Auto V</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-grand-theft-auto-v.svg"
                                        alt="Grand Theft Auto V">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">Red Dead Redemption 2</span>
                                    <img class="game-icon"
                                        src="/assets/Media/ico/red-dead-redemption-2-wordmark-light.svg"
                                        alt="Red Dead Redemption 2">
                                </a>
                                <a href="#" class="marquee-item">
                                    <span class="game-name">The Witcher 3</span>
                                    <img class="game-icon" src="/assets/Media/ico/icons8-ведьмак-2.svg"
                                        alt="The Witcher 3">
                                </a>
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