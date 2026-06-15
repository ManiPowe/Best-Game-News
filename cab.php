<?php
session_start();
require_once('assets/app/db.php');

// В будущем здесь будет проверка $_SESSION['user_id']
// Пока для теста захардкодим ID = 1
if (!isset($_SESSION['user_id'])) {
    $user_id = 1;
} else {
    $user_id = $_SESSION['user_id'];
}

// Получаем данные пользователя из базы
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("Пользователь не найден!");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Best Game News</title>
    <link rel="stylesheet" href="css/cab.css">
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
                    <a href="#">
                        <button class="icon-btn" type="button" aria-label="Комментарии">
                            <img src="/assets/Media/Photo/comm.png" alt="Комментарии">
                        </button>
                    </a>
                    <a href="cab.php">
                        <button class="icon-btn" type="button" aria-label="Профиль">
                            <img src="/assets/Media/Photo/man.png" alt="Профиль">
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="dashboard">
            <aside class="sidebar">
                <h3>Мой аккаунт</h3>
                <a href="#" class="menu-item active">
                    <i class="fas fa-user"></i>
                    <span>Профиль</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Настройки</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-history"></i>
                    <span>Активность</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-bookmark"></i>
                    <span>Избранное</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-comments"></i>
                    <span>Комментарии</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-bell"></i>
                    <span>Уведомления</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Выход</span>
                </a>
            </aside>

            <div class="content-area">
                <div class="content-header">
                    <h2>Профиль пользователя</h2>
                    <button><i class="fas fa-save"></i> Сохранить</button>
                </div>

                <div class="profile-header">
                    <div class="avatar">
                        <?php if ($user['avatar'] === 'assets/Media/Photo/man.png' || empty($user['avatar'])): ?>
                            <i class="fas fa-user"></i>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    
                    <!-- ФОРМА ЗАГРУЗКИ АВАТАРА -->
                    <form action="assets/app/upload_avatar.php" method="POST" enctype="multipart/form-data" style="margin-top: 10px;">
                        <input type="file" name="avatar" accept="image/*" required style="color: white; margin-bottom: 5px;">
                        <button type="submit" class="save-btn" style="padding: 5px 10px; font-size: 14px;">Загрузить аватар</button>
                    </form>
                    
                    <div class="profile-info">
                        <h2><?= htmlspecialchars($user['login']) ?></h2>
                        <p><i class="fas fa-envelope"></i><?= htmlspecialchars($user['email']) ?></p>
                        <p><i class="fas fa-phone"></i><?= htmlspecialchars($user['phone']) ?></p>
                        <p><i class="fas fa-calendar"></i> Участник с <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                        
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?= $user['posts_count'] ?? 0 ?></div>
                                <div class="stat-label">Постов</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?= $user['comments_count'] ?? 0 ?></div>
                                <div class="stat-label">Комментариев</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?= $user['top_liked_comment'] ?? 0 ?></div>
                                <div class="stat-label">Лайков</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-form">
                    <div class="form-group">
                        <label for="username">Имя пользователя</label>
                        <input type="text" id="username" value="<?= htmlspecialchars($user['login']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">О себе</label>
                        <textarea id="bio" rows="3">Узник 2к птс, ищу бустеров</textarea>
                    </div>
                </div>

                <button class="save-btn">Сохранить изменения</button>
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