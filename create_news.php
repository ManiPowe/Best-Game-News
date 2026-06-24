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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $short_description = trim($_POST['short_description']);
    $category = $_POST['category'];
    $game_id = !empty($_POST['game_id']) ? (int) $_POST['game_id'] : null;
    if ($category === 'games' && !$game_id) {
        $error = "Для категории 'Игры' необходимо выбрать конкретную игру!";
    }
    $tags = trim($_POST['tags']);
    $status = $_POST['status'];
    $author_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $error = "Заголовок и содержание обязательны!";
    } else {
        $image_path = null;
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
                    $image_path = 'assets/Media/news/' . $unique_name;
                }
            }
        }

        $sql = "INSERT INTO news (title, content, short_description, image, category, game_id, tags, status, author_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssissi", $title, $content, $short_description, $image_path, $category, $game_id, $tags, $status, $author_id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Новость успешно создана!";
        } else {
            $error = "Ошибка при сохранении: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать новость - Best Game News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/create_news.css">
        <link rel="stylesheet" href="css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <script src="/assets/JS/category_games.js"></script>
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
                <a href="#">Новости</a>
                <a href="#">Статьи</a>
                <a href="#">Видео</a>
                <a href="#">Прохождения</a>
                <a href="#">Помощь</a>
            </nav>
            <div class="search-wrap">
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
        <div class="create-news-container">
            <h1><i class="fas fa-newspaper"></i> Создание новости</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <a href="index.php" style="color: #4CAF50; font-weight: bold; margin-left: 10px;">На главную</a>
                </div>
            <?php endif; ?>

            <form action="create_news.php" method="POST" enctype="multipart/form-data" class="create-news-form">
                <div class="form-group">
                    <label for="title">Заголовок *</label>
                    <input type="text" id="title" name="title" required maxlength="255"
                        placeholder="Введите заголовок новости">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Категория</label>
                        <select id="category" name="category" onchange="toggleGameSelect()">
                            <option value="news">Новости</option>
                            <option value="games">Игры</option>
                            <option value="articles">Статьи</option>
                            <option value="videos">Видео</option>
                        </select>
                    </div>

                    <div class="form-group" id="game-select-group" style="display: none;">
                        <label for="game_id">Выберите игру</label>
                        <select id="game_id" name="game_id">
                            <option value="">-- Выберите игру --</option>
                            <?php
                            $games_sql = "SELECT id, name FROM games ORDER BY name ASC";
                            $games_result = mysqli_query($conn, $games_sql);
                            while ($game = mysqli_fetch_assoc($games_result)):
                                ?>
                                <option value="<?= $game['id'] ?>">
                                    <?= htmlspecialchars($game['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Статус</label>
                        <select id="status" name="status">
                            <option value="draft">Черновик (сохранить и редактировать позже)</option>
                            <option value="pending">Отправить на проверку модератору</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="short_description">Краткое описание (для превью)</label>
                    <textarea id="short_description" name="short_description" rows="3"
                        placeholder="Краткое описание новости (1-2 предложения)"></textarea>
                </div>

                <div class="form-group">
                    <label for="content">Содержание *</label>
                    <textarea id="content" name="content" rows="15" required
                        placeholder="Полный текст новости..."></textarea>
                </div>

                <div class="form-group">
                    <label for="tags">Теги (через запятую)</label>
                    <input type="text" id="tags" name="tags" placeholder="например: DOTA 2, ивент, коллаборация">
                </div>

                <div class="form-group">
                    <label for="cover">Обложка новости</label>
                    <input type="file" id="cover" name="cover" accept="image/*">
                    <small style="color: #b0b0b0;">Рекомендуемый размер: 1200x630px</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Создать новость
                    </button>
                    <a href="index.php" class="cancel-btn">Отмена</a>
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
    <script src="/assets/js/theme.js"></script>
</body>

</html>