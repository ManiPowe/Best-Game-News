<?php
session_start();
require_once 'assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Проверяем ID тикета
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /help");
    exit;
}

$ticket_id = (int) $_GET['id'];

// Получаем тикет (проверяем что он принадлежит текущему пользователю)
$ticket_sql = "SELECT t.*, u.login, u.email 
               FROM tickets t 
               JOIN users u ON t.user_id = u.id 
               WHERE t.id = ? AND t.user_id = ?";
$ticket_stmt = mysqli_prepare($conn, $ticket_sql);
mysqli_stmt_bind_param($ticket_stmt, "ii", $ticket_id, $user_id);
mysqli_stmt_execute($ticket_stmt);
$ticket = mysqli_fetch_assoc(mysqli_stmt_get_result($ticket_stmt));

if (!$ticket) {
    header("Location: /help");
    exit;
}

// Обработка отправки сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $message = trim($_POST['message'] ?? '');

    if (strlen($message) >= 5) {
        $msg_sql = "INSERT INTO ticket_messages (ticket_id, user_id, message, is_admin) VALUES (?, ?, ?, 0)";
        $msg_stmt = mysqli_prepare($conn, $msg_sql);
        mysqli_stmt_bind_param($msg_stmt, "iis", $ticket_id, $user_id, $message);

        if (mysqli_stmt_execute($msg_stmt)) {
            // Обновляем статус тикета на "отвечено"
            mysqli_query($conn, "UPDATE tickets SET status = 'answered' WHERE id = $ticket_id");

            $_SESSION['message_success'] = "Сообщение отправлено!";
        }
    } else {
        $_SESSION['message_error'] = "Сообщение слишком короткое (минимум 5 символов)";
    }

    header("Location: /ticket/$ticket_id");
    exit;
}

// Получаем все сообщения тикета
$messages_sql = "SELECT tm.*, u.login, u.avatar, u.role 
                 FROM ticket_messages tm 
                 JOIN users u ON tm.user_id = u.id 
                 WHERE tm.ticket_id = ? 
                 ORDER BY tm.created_at ASC";
$messages_stmt = mysqli_prepare($conn, $messages_sql);
mysqli_stmt_bind_param($messages_stmt, "i", $ticket_id);
mysqli_stmt_execute($messages_stmt);
$messages_result = mysqli_stmt_get_result($messages_stmt);
$messages = [];
while ($msg = mysqli_fetch_assoc($messages_result)) {
    $messages[] = $msg;
}

// Получаем роль пользователя
$role_sql = "SELECT role FROM users WHERE id = ?";
$role_stmt = mysqli_prepare($conn, $role_sql);
mysqli_stmt_bind_param($role_stmt, "i", $user_id);
mysqli_stmt_execute($role_stmt);
$user_role = mysqli_fetch_assoc(mysqli_stmt_get_result($role_stmt))['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обращение #<?= $ticket_id ?> - Best Game News</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/help.css">
    <link rel="stylesheet" href="/css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
</head>

<body>
    <script src="/assets/js/theme-init.js"></script>

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
                    <a href="/admin/admin.php" class="admin-link">
                        <i class="fas fa-shield-alt"></i> Админ панель
                    </a>
                <?php endif; ?>

                <?php if ($user_role === 'creator' || $user_role === 'moderator' || $user_role === 'admin'): ?>
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


    <main class="ticket-main">
        <div class="ticket-container">
            <div class="ticket-header-info">
                <a href="/help" class="back-link">
                    <i class="fas fa-arrow-left"></i> Назад к обращениям
                </a>
                <h1>
                    <i class="fas fa-ticket-alt"></i>
                    Обращение #<?= $ticket_id ?>
                </h1>
                <div class="ticket-meta-info">
                    <h2><?= htmlspecialchars($ticket['subject']) ?></h2>
                    <div class="ticket-details">
                        <span class="ticket-status status-<?= $ticket['status'] ?>">
                            <?php
                            switch ($ticket['status']) {
                                case 'open':
                                    echo '<i class="fas fa-clock"></i> Ожидает ответа';
                                    break;
                                case 'answered':
                                    echo '<i class="fas fa-check"></i> Отвечено';
                                    break;
                                case 'closed':
                                    echo '<i class="fas fa-lock"></i> Закрыт';
                                    break;
                            }
                            ?>
                        </span>
                        <span class="ticket-date">
                            <i class="fas fa-calendar"></i>
                            Создано: <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['message_success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['message_success'] ?>
                </div>
                <?php unset($_SESSION['message_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['message_error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['message_error'] ?>
                </div>
                <?php unset($_SESSION['message_error']); ?>
            <?php endif; ?>

            <!-- Переписка -->
            <div class="messages-section">
                <div class="messages-list">
                    <?php foreach ($messages as $msg):
                        $is_admin_msg = $msg['is_admin'] || in_array($msg['role'], ['admin', 'moderator']);
                        ?>
                        <div class="message-card <?= $is_admin_msg ? 'admin-message' : 'user-message' ?>">
                            <div class="message-header">
                                <div class="message-author">
                                    <?php
                                    $msg_avatar = $msg['avatar'] ?: '/assets/Media/Photo/man.png';
                                    if (strpos($msg_avatar, 'http') !== 0 && strpos($msg_avatar, '/') !== 0) {
                                        $msg_avatar = '/' . $msg_avatar;
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($msg_avatar) ?>" alt="Аватар" class="message-avatar"
                                        onerror="this.src='/assets/Media/Photo/man.png'">
                                    <div>
                                        <strong><?= htmlspecialchars($msg['login']) ?></strong>
                                        <?php if ($is_admin_msg): ?>
                                            <span class="admin-badge-small">
                                                <i class="fas fa-shield-alt"></i> Поддержка
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="message-date">
                                    <?= date('d.m.Y H:i', strtotime($msg['created_at'])) ?>
                                </span>
                            </div>
                            <div class="message-text">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Форма ответа -->
            <?php if ($ticket['status'] !== 'closed'): ?>
                <div class="reply-section">
                    <h3><i class="fas fa-reply"></i> Ответить</h3>
                    <form action="/ticket/<?= $ticket_id ?>" method="POST" class="reply-form">
                        <input type="hidden" name="send_message" value="1">
                        <textarea name="message" required minlength="5" rows="5"
                            placeholder="Напишите ваш ответ..."></textarea>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Отправить
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="closed-notice">
                    <i class="fas fa-lock"></i>
                    <p>Это обращение закрыто. Вы не можете отправлять сообщения.</p>
                </div>
            <?php endif; ?>
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
    <script src="/assets/js/no-cache.js"></script>
    <script src="/assets/js/theme.js"></script>
</body>

</html>