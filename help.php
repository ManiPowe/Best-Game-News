<?php
session_start();
require_once 'assets/app/db.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : 0;

// Получаем роль пользователя
$user_role = null;
if ($is_logged_in) {
    $role_sql = "SELECT role FROM users WHERE id = ?";
    $role_stmt = mysqli_prepare($conn, $role_sql);
    mysqli_stmt_bind_param($role_stmt, "i", $user_id);
    mysqli_stmt_execute($role_stmt);
    $user_role = mysqli_fetch_assoc(mysqli_stmt_get_result($role_stmt))['role'] ?? null;
}

// Обработка создания тикета
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in && isset($_POST['create_ticket'])) {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (strlen($subject) >= 5 && strlen($message) >= 10) {
        // Создаём тикет
        $ticket_sql = "INSERT INTO tickets (user_id, subject) VALUES (?, ?)";
        $ticket_stmt = mysqli_prepare($conn, $ticket_sql);
        mysqli_stmt_bind_param($ticket_stmt, "is", $user_id, $subject);

        if (mysqli_stmt_execute($ticket_stmt)) {
            $ticket_id = mysqli_insert_id($conn);

            // Добавляем первое сообщение
            $msg_sql = "INSERT INTO ticket_messages (ticket_id, user_id, message) VALUES (?, ?, ?)";
            $msg_stmt = mysqli_prepare($conn, $msg_sql);
            mysqli_stmt_bind_param($msg_stmt, "iis", $ticket_id, $user_id, $message);
            mysqli_stmt_execute($msg_stmt);

            $_SESSION['ticket_success'] = "Обращение успешно создано! Мы ответим вам в ближайшее время.";
        }
    } else {
        $_SESSION['ticket_error'] = "Заполните все поля корректно (тема мин. 5 символов, сообщение мин. 10 символов)";
    }

    header("Location: /help");
    exit;
}

// Получаем тикеты пользователя
$tickets = [];
if ($is_logged_in) {
    $tickets_sql = "SELECT t.*, 
                    (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id) as messages_count,
                    (SELECT created_at FROM ticket_messages WHERE ticket_id = t.id ORDER BY created_at DESC LIMIT 1) as last_message_date
                    FROM tickets t 
                    WHERE t.user_id = ? 
                    ORDER BY t.updated_at DESC";
    $tickets_stmt = mysqli_prepare($conn, $tickets_sql);
    mysqli_stmt_bind_param($tickets_stmt, "i", $user_id);
    mysqli_stmt_execute($tickets_stmt);
    $tickets_result = mysqli_stmt_get_result($tickets_stmt);
    while ($ticket = mysqli_fetch_assoc($tickets_result)) {
        $tickets[] = $ticket;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Помощь и поддержка - Best Game News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/help.css">
    <link rel="stylesheet" href="css/light-theme.css">
    <link rel="shortcut icon" href="/assets/Media/Photo/asd.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
</head>

<body>
    <script src="/assets/js/theme-init.js"></script>

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

    <main class="help-main">
        <div class="help-container">
            <div class="help-header">
                <h1></i> Помощь и поддержка</h1>
                <p>Мы всегда готовы помочь! Создайте обращение, и наша команда ответит вам в ближайшее время.</p>
            </div>

            <?php if (isset($_SESSION['ticket_success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['ticket_success'] ?>
                </div>
                <?php unset($_SESSION['ticket_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['ticket_error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['ticket_error'] ?>
                </div>
                <?php unset($_SESSION['ticket_error']); ?>
            <?php endif; ?>

            <div class="help-grid">
                <!-- Форма создания тикета -->
                <div class="help-section">
                    <h2><i class="fas fa-plus-circle"></i> Создать обращение</h2>

                    <?php if ($is_logged_in): ?>
                        <form action="/help" method="POST" class="ticket-form">
                            <input type="hidden" name="create_ticket" value="1">

                            <div class="form-group">
                                <label for="subject">Тема обращения</label>
                                <input type="text" id="subject" name="subject" required minlength="5"
                                    placeholder="Например: Проблема с публикацией новости">
                            </div>

                            <div class="form-group">
                                <label for="message">Сообщение</label>
                                <textarea id="message" name="message" required minlength="10" rows="6"
                                    placeholder="Опишите вашу проблему или вопрос подробно..."></textarea>
                            </div>

                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane"></i> Отправить обращение
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="login-prompt">
                            <i class="fas fa-sign-in-alt"></i>
                            <p>Чтобы создать обращение, необходимо <a href="/login">войти в аккаунт</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Контакты -->
                <div class="help-section contacts-section">
                    <h2><i class="fas fa-phone-alt"></i> Свяжитесь с нами</h2>

                    <div class="contacts-list">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong>
                                <p>support@bestgamenews.ru</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fab fa-telegram"></i>
                            <div>
                                <strong>Telegram</strong>
                                <p>@bestgamenews_support</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fab fa-discord"></i>
                            <div>
                                <strong>Discord</strong>
                                <p>Best Game News#1234</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Время работы</strong>
                                <p>Пн-Пт: 10:00 - 19:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-section-full">
                <h2><i class="fas fa-question-circle"></i> Частые вопросы</h2>

                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            <span>Как опубликовать новость?</span>
                        </div>
                        <div class="faq-answer">
                            Создайте новость в разделе "Создать пост", заполните все поля и отправьте на проверку.
                            Модератор рассмотрит её в течение 24 часов.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            <span>Почему мою новость отклонили?</span>
                        </div>
                        <div class="faq-answer">
                            Причина отклонения указана в уведомлении. Отредактируйте новость согласно замечаниям и
                            отправьте снова.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            <span>Как стать автором?</span>
                        </div>
                        <div class="faq-answer">
                            Напишите нам в поддержку, и мы выдадим вам эту роль.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            <span>Как изменить тему оформления?</span>
                        </div>
                        <div class="faq-answer">
                            Зайдите в личный кабинет → Настройки → прокрутите вниз до переключателя темы.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            <span>Сколько времени занимает модерация?</span>
                        </div>
                        <div class="faq-answer">
                            Обычно модерация занимает от 1 до 24 часов. В редких случаях срок может быть увеличен.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Список тикетов -->
            <?php if ($is_logged_in && !empty($tickets)): ?>
                <div class="tickets-section">
                    <h2><i class="fas fa-list"></i> Мои обращения (<?= count($tickets) ?>)</h2>

                    <div class="tickets-list">
                        <?php foreach ($tickets as $ticket): ?>
                            <a href="/ticket/<?= $ticket['id'] ?>" class="ticket-card">
                                <div class="ticket-header">
                                    <div class="ticket-title">
                                        <h3><?= htmlspecialchars($ticket['subject']) ?></h3>
                                        <span class="ticket-date">
                                            Создано: <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                                        </span>
                                    </div>
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
                                </div>

                                <div class="ticket-meta">
                                    <span><i class="fas fa-comments"></i> <?= $ticket['messages_count'] ?> сообщений</span>
                                    <?php if ($ticket['last_message_date']): ?>
                                        <span><i class="fas fa-clock"></i> Последний ответ:
                                            <?= date('d.m.Y H:i', strtotime($ticket['last_message_date'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
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
    <script src="/assets/js/help.js"></script>
</body>

</html>