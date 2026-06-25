<?php
session_start();
require_once 'assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$check_role_sql = "SELECT role FROM users WHERE id = ?";
$stmt_role = mysqli_prepare($conn, $check_role_sql);
mysqli_stmt_bind_param($stmt_role, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt_role);
$result_role = mysqli_stmt_get_result($stmt_role);
$user_role = mysqli_fetch_assoc($result_role)['role'];

if ($user_role !== 'creator' && $user_role !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$news_id = (int) $_GET['id'];

$check_sql = "SELECT * FROM news WHERE id = ? AND author_id = ?";
$stmt_check = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt_check, "ii", $news_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$news = mysqli_fetch_assoc($result_check);

if (!$news) {
    die("Новость не найдена или у вас нет прав!");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $short_description = trim($_POST['short_description']);
    $category = $_POST['category'];
    $game_id = !empty($_POST['game_id']) ? (int) $_POST['game_id'] : null;
    $tags = trim($_POST['tags']);
    $status = $_POST['status'];

    if (empty($title) || empty($content)) {
        $error = "Заголовок и содержание обязательны!";
    } elseif ($category === 'games' && !$game_id) {
        $error = "Для категории 'Игры' необходимо выбрать конкретную игру!";
    } else {
        $image_path = $news['image'];
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['cover'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($extension, $allowed)) {
                $unique_name = 'news_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $upload_dir = __DIR__ . '/assets/Media/news/';

                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $destination = $upload_dir . $unique_name;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if ($news['image'] && file_exists($news['image'])) {
                        unlink($news['image']);
                    }
                    $image_path = 'assets/Media/news/' . $unique_name;
                }
            }
        }

        $sql = "UPDATE news SET title = ?, content = ?, short_description = ?, image = ?, category = ?, game_id = ?, tags = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssisii", $title, $content, $short_description, $image_path, $category, $game_id, $tags, $status, $news_id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Новость успешно обновлена!";
            $news['title'] = $title;
            $news['content'] = $content;
            $news['short_description'] = $short_description;
            $news['image'] = $image_path;
            $news['category'] = $category;
            $news['game_id'] = $game_id;
            $news['tags'] = $tags;
            $news['status'] = $status;
        } else {
            $error = "Ошибка при сохранении: " . mysqli_error($conn);
        }
    }
}

$games_sql = "SELECT id, name FROM games ORDER BY name ASC";
$games_result = mysqli_query($conn, $games_sql);
$games = [];
while ($game = mysqli_fetch_assoc($games_result)) {
    $games[] = $game;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать новость - Best Game News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/create_news.css">
    <link rel="stylesheet" href="css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a class="logo-link" href="index.php">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="index.php">Главная</a>
                <a href="category.php?type=games">Игры</a>
                <a href="category.php?type=news">Новости</a>
                <a href="category.php?type=articles">Статьи</a>
                <a href="category.php?type=videos">Видео</a>
                <a href="category.php?type=walkthroughs">Прохождения</a>
                <a href="help.php">Помощь</a>

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
                        <a href="cab.php" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'assets/Media/Photo/man.png') ?>"
                                alt="Профиль" class="header-avatar">
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
        <div class="create-news-container">
            <h1><i class="fas fa-edit"></i> Редактирование новости</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="edit_news.php?id=<?= $news_id ?>" method="POST" enctype="multipart/form-data"
                class="create-news-form">
                <div class="form-group">
                    <label for="title">Заголовок *</label>
                    <input type="text" id="title" name="title" required maxlength="255"
                        value="<?= htmlspecialchars($news['title']) ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Категория</label>
                        <select id="category" name="category" onchange="toggleGameSelect()">
                            <option value="news" <?= $news['category'] === 'news' ? 'selected' : '' ?>>Новости</option>
                            <option value="games" <?= $news['category'] === 'games' ? 'selected' : '' ?>>Игры</option>
                            <option value="articles" <?= $news['category'] === 'articles' ? 'selected' : '' ?>>Статьи
                            </option>
                            <option value="videos" <?= $news['category'] === 'videos' ? 'selected' : '' ?>>Видео</option>
                        </select>
                    </div>

                    <div class="form-group" id="game-select-group"
                        style="display: <?= $news['category'] === 'games' ? 'block' : 'none' ?>;">
                        <label for="game_id">Выберите игру</label>
                        <select id="game_id" name="game_id">
                            <option value="">-- Выберите игру --</option>
                            <?php foreach ($games as $game): ?>
                                <option value="<?= $game['id'] ?>" <?= $news['game_id'] == $game['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($game['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Статус</label>
                        <select id="status" name="status">
                            <option value="draft" <?= $news['status'] === 'draft' ? 'selected' : '' ?>>Черновик</option>
                            <option value="pending" <?= $news['status'] === 'pending' ? 'selected' : '' ?>>На проверке
                            </option>
                            <?php if ($news['status'] === 'published'): ?>
                                <option value="published" selected>Опубликовано</option>
                            <?php endif; ?>
                        </select>
                        <?php if ($news['status'] === 'published'): ?>
                            <small style="color: #4CAF50;">Новость уже опубликована. Статус можно изменить только через
                                админ-панель.</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="short_description">Краткое описание</label>
                    <textarea id="short_description" name="short_description"
                        rows="3"><?= htmlspecialchars($news['short_description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="content">Содержание *</label>
                    <textarea id="content" name="content" rows="15"
                        required><?= htmlspecialchars($news['content']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="tags">Теги (через запятую)</label>
                    <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($news['tags'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="cover">Обложка новости</label>
                    <?php if ($news['image']): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= htmlspecialchars($news['image']) ?>" alt="Текущая обложка"
                                style="max-width: 300px; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="cover" name="cover" accept="image/*">
                    <small style="color: #b0b0b0;">Оставьте пустым, чтобы не менять обложку</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                    <a href="profile.php?id=<?= $_SESSION['user_id'] ?>" class="cancel-btn">Отмена</a>
                </div>
            </form>
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
    <script src="/assets/js/game-edit-news"></script>
    <script src="/assets/js/theme.js"></script>
</body>

</html>