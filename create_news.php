<?php
session_start();
require_once 'assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

$check_role_sql = "SELECT role FROM users WHERE id = ?";
$stmt_role = mysqli_prepare($conn, $check_role_sql);
mysqli_stmt_bind_param($stmt_role, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt_role);
$result_role = mysqli_stmt_get_result($stmt_role);
$user_role = mysqli_fetch_assoc($result_role)['role'];

if ($user_role !== 'creator' && $user_role !== 'moderator' && $user_role !== 'admin') {
    header("Location: /home");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === ОТЛАДКА ===
    error_log("=== POST ЗАПРОС ===");
    error_log("Title: " . ($_POST['title'] ?? 'НЕ ПЕРЕДАН'));
    error_log("Content length: " . strlen($_POST['content'] ?? ''));
    error_log("Content preview: " . mb_substr($_POST['content'] ?? '', 0, 100));
    error_log("Category: " . ($_POST['category'] ?? 'НЕ ПЕРЕДАН'));
    error_log("===================");
    // === КОНЕЦ ОТЛАДКИ ===
    
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $short_description = trim($_POST['short_description']);
    $category = $_POST['category'];
    $game_id = !empty($_POST['game_id']) ? (int) $_POST['game_id'] : null;
    $tags = trim($_POST['tags']);
    $status = $_POST['status'];
    $author_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $error = "Заголовок и содержание обязательны!";
    } elseif ($category === 'games' && !$game_id) {
        $error = "Для категории 'Игры' необходимо выбрать конкретную игру!";
    } elseif ($category === 'walkthroughs' && !$game_id) {
        $error = "Для категории 'Прохождения' необходимо выбрать конкретную игру!";
    } else {
        $image_path = null;
        $video_path = null;

        // === ЗАГРУЗКА ВИДЕО + ОБЛОЖКИ (для категории videos) ===
        if ($category === 'videos') {
            // Загрузка видео
            if (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
                $error = "Необходимо загрузить видео!";
            } else {
                $file = $_FILES['video_file'];
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_video = ['mp4', 'webm', 'mov', 'avi'];
                $max_size = 200 * 1024 * 1024; // 200 МБ

                if (!in_array($extension, $allowed_video)) {
                    $error = "Допустимые форматы видео: " . implode(', ', $allowed_video);
                } elseif ($file['size'] > $max_size) {
                    $error = "Размер видео не должен превышать 200 МБ";
                } else {
                    $unique_name = 'video_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                    $upload_dir = __DIR__ . '/assets/Media/videos/';

                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $destination = $upload_dir . $unique_name;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $video_path = 'assets/Media/videos/' . $unique_name;
                    } else {
                        $error = "Ошибка при загрузке видео";
                    }
                }
            }

            // Загрузка обложки для видео
            if (empty($error) && isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['cover'];
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($extension, $allowed)) {
                    $unique_name = 'cover_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
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
        }
        // === ЗАГРУЗКА ОБЛОЖКИ (для остальных категорий) ===
        elseif (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
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

        if (empty($error)) {
            $sql = "INSERT INTO news (title, content, short_description, image, video_path, category, game_id, tags, status, author_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssissi", $title, $content, $short_description, $image_path, $video_path, $category, $game_id, $tags, $status, $author_id);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Материал успешно создан!";
            } else {
                $error = "Ошибка при сохранении: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать материал - Best Game News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/create_news.css">
    <link rel="stylesheet" href="css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
</head>

<body>
    <script src="/assets/tinymce/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '#content',
                height: 500,
                plugins: 'image media link table lists code fullscreen paste',
                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | ' +
                    'bullist numlist | link image media | table | code | fullscreen',
                menubar: 'edit insert view format table',
                skin: 'oxide-dark',
                content_css: '/css/tinymce-content.css',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto; font-size: 16px; color: #fff; background: #1a1a1a; }',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                paste_data_images: true,
                images_upload_url: '/assets/app/upload_handler.php',
                images_upload_base_path: '/assets/Media/uploads/',
                setup: function (editor) {
                    // Сохраняем при каждом изменении
                    editor.on('change', function () {
                        editor.save();
                    });

                    // Сохраняем перед отправкой формы
                    editor.on('submit', function () {
                        editor.save();
                    });
                }
            });

            // Принудительное сохранение перед отправкой формы
            document.querySelector('.create-news-form').addEventListener('submit', function (e) {
                // Сохраняем все редакторы TinyMCE
                tinymce.triggerSave();

                // Проверяем что content не пустой
                const content = document.getElementById('content').value;
                if (!content.trim()) {
                    e.preventDefault();
                    alert('Содержание новости обязательно!');
                    return false;
                }
            });
        });
    </script>
    <script src="/assets/js/theme-init.js"></script>
    <script src="/assets/js/no-cache.js"></script>

    <?php
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

    <!-- хедер -->
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="/home">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="/home">Главная</a>
                <a href="/category/games">Игры</a>
                <a href="/category/news">Новости</a>
                <a href="/category/articles">Статьи</a>
                <a href="/category/videos">Видео</a>
                <a href="/category/walkthroughs">Прохождения</a>
                <a href="/help">Помощь</a>

                <?php if ($user_role === 'admin' || $user_role === 'moderator'): ?>
                    <a href="/admin/admin.php" class="admin-link">
                        <i class="fas fa-shield-alt"></i> Админ панель
                    </a>
                <?php endif; ?>

               <?php if ($user_role === 'creator' || $user_role === 'moderator' || $user_role === 'admin'): ?>
                    <a href="/create" class="create-news-btn">
                        <i class="fas fa-plus"></i> Создать пост
                    </a>
                <?php endif; ?>
            </nav>
            <div class="search-wrap">
                <form action="/search" method="get" class="search-form">
                    <input type="search" name="q" class="search-input" placeholder=" Поиск..."
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit" class="search-btn">
                        <img src="/assets/Media/Photo/search.png" alt="Поиск">
                    </button>
                </form>
                <div class="auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/cab" class="user-avatar-link">
                            <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? '/assets/Media/Photo/man.png') ?>"
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
    <!-- /хедер -->

    <!-- основной контент -->
    <main>
        <div class="create-news-container">
            <h1><i class="fas fa-plus-circle"></i> Создание новости</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <a href="/home" style="color: #4CAF50; font-weight: bold; margin-left: 10px;">На главную</a>
                </div>
            <?php endif; ?>

            <!-- форма создания новости -->
            <form action="/create" method="POST" enctype="multipart/form-data" class="create-news-form">
                <div class="form-group">
                    <label for="title">Заголовок *</label>
                    <input type="text" id="title" name="title" required maxlength="255" placeholder="Введите заголовок">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Тип новости</label>
                        <select id="category" name="category" onchange="toggleFields()">
                            <option value="news">Новость</option>
                            <option value="games">Игры</option>
                            <option value="articles">Статья</option>
                            <option value="videos">Видео</option>
                            <option value="walkthroughs">Прохождение</option>
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

                <!-- Поле для загрузки видео (только для videos) -->
                <div class="form-group" id="video-upload-group" style="display: none;">
                    <label for="video_file">Видеофайл *</label>
                    <input type="file" id="video_file" name="video_file"
                        accept="video/mp4,video/webm,video/quicktime,video/x-msvideo">
                    <small style="color: #b0b0b0;">
                        Допустимые форматы: MP4, WebM, MOV, AVI. Максимальный размер: 200 МБ
                    </small>
                </div>

                <div class="form-group">
                    <label for="short_description">Краткое описание (для превью)</label>
                    <textarea id="short_description" name="short_description" rows="3"
                        placeholder="Краткое описание (1-2 предложения)"></textarea>
                </div>

                <div class="form-group">
                    <label for="content">Содержание *</label>
                    <textarea id="content" name="content" rows="15" required
                        placeholder="Полный текст материала..."></textarea>
                </div>

                <div class="form-group">
                    <label for="tags">Теги (через запятую)</label>
                    <input type="text" id="tags" name="tags" placeholder="например: DOTA 2, ивент, коллаборация">
                </div>

                <!-- Обложка (для всех категорий) -->
                <div class="form-group" id="cover-group">
                    <label for="cover">Обложка</label>
                    <input type="file" id="cover" name="cover" accept="image/*">
                    <small style="color: #b0b0b0;">Рекомендуемый размер: 1200x630px</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Создать материал
                    </button>
                    <a href="/home" class="cancel-btn">Отмена</a>
                </div>
            </form>
            <!-- /форма создания новости -->
        </div>
    </main>
    <!-- /основной контент -->

    <!-- футер -->
    <footer>
        <div class="footer">
            <div class="footer-logo">
                <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                <p>Best Game News</p>
            </div>
            <div class="prav">
                <p>2026 © Все права защищены Best Game News</p>
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
    <!-- /футер -->

    <script src="/assets/js/theme.js"></script>
    <script>
        function toggleFields() {
            const category = document.getElementById('category').value;
            const gameSelect = document.getElementById('game-select-group');
            const videoUploadGroup = document.getElementById('video-upload-group');
            const coverGroup = document.getElementById('cover-group');

            // Сбрасываем
            gameSelect.style.display = 'none';
            videoUploadGroup.style.display = 'none';
            coverGroup.style.display = 'block';

            // Для игр и прохождений — выбор игры
            if (category === 'games' || category === 'walkthroughs') {
                gameSelect.style.display = 'block';
            }

            // Для видео — загрузка файла + обложка
            if (category === 'videos') {
                videoUploadGroup.style.display = 'block';
                coverGroup.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</body>

</html>