<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /admin/admin.php?tab=support");
    exit;
}

$ticket_id = (int) $_GET['id'];

// Получаем тикет
$ticket_sql = "SELECT t.*, u.login, u.email, u.avatar 
               FROM tickets t 
               JOIN users u ON t.user_id = u.id 
               WHERE t.id = ?";
$ticket_stmt = mysqli_prepare($conn, $ticket_sql);
mysqli_stmt_bind_param($ticket_stmt, "i", $ticket_id);
mysqli_stmt_execute($ticket_stmt);
$ticket = mysqli_fetch_assoc(mysqli_stmt_get_result($ticket_stmt));

if (!$ticket) {
    header("Location: /admin/admin.php?tab=support");
    exit;
}

// Получаем сообщения
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
?>

<div class="ticket-view-section">
    <div class="ticket-view-header">
        <a href="?tab=support" class="back-link">
            <i class="fas fa-arrow-left"></i> Назад к списку
        </a>
        <div class="ticket-view-title">
            <h2><i class="fas fa-ticket-alt"></i> Обращение #<?= $ticket_id ?></h2>
            <span class="ticket-status status-<?= $ticket['status'] ?>">
                <?php
                switch ($ticket['status']) {
                    case 'open':
                        echo '<i class="fas fa-clock"></i> Ожидает';
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
        <h3 class="ticket-subject"><?= htmlspecialchars($ticket['subject']) ?></h3>
        <div class="ticket-user-info">
            <?php
            $user_avatar = $ticket['avatar'] ?: '/assets/Media/Photo/man.png';
            if (strpos($user_avatar, 'http') !== 0 && strpos($user_avatar, '/') !== 0) {
                $user_avatar = '../' . $user_avatar;
            }
            ?>
            <img src="<?= htmlspecialchars($user_avatar) ?>" alt="Аватар" class="user-avatar-small"
                onerror="this.src='../assets/Media/Photo/man.png'">
            <div>
                <strong><?= htmlspecialchars($ticket['login']) ?></strong>
                <span><?= htmlspecialchars($ticket['email']) ?></span>
            </div>
            <span class="ticket-created">
                <i class="fas fa-calendar"></i>
                <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
            </span>
        </div>
    </div>

    <!-- Сообщения -->
    <div class="messages-section">
        <div class="messages-list">
            <?php foreach ($messages as $msg):
                $is_admin_msg = $msg['is_admin'] || in_array($msg['role'], ['admin', 'moderator']);

                // Исправляем путь к аватарке
                $msg_avatar = $msg['avatar'] ?: '/assets/Media/Photo/man.png';
                if (strpos($msg_avatar, 'http') !== 0 && strpos($msg_avatar, '/') !== 0) {
                    $msg_avatar = '../' . $msg_avatar;
                }
                ?>
                <div class="message-card <?= $is_admin_msg ? 'admin-message' : 'user-message' ?>">
                    <div class="message-header">
                        <div class="message-author">
                            <img src="<?= htmlspecialchars($msg_avatar) ?>" alt="Аватар" class="message-avatar"
                                onerror="this.src='../assets/Media/Photo/man.png'">
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

    <!-- Действия и форма ответа -->
    <div class="ticket-actions-section">
        <div class="ticket-actions-buttons">
            <?php if ($ticket['status'] !== 'closed'): ?>
                <form action="/admin/actions/ticket_close.php" method="POST" style="display: inline;">
                    <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
                    <button type="submit" class="btn-close-ticket" onclick="return confirm('Закрыть это обращение?')">
                        <i class="fas fa-lock"></i> Закрыть обращение
                    </button>
                </form>
            <?php else: ?>
                <form action="/admin/actions/ticket_reopen.php" method="POST" style="display: inline;">
                    <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
                    <button type="submit" class="btn-reopen-ticket">
                        <i class="fas fa-unlock"></i> Открыть снова
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($ticket['status'] !== 'closed'): ?>
            <form action="/admin/actions/ticket_reply.php" method="POST" class="reply-form">
                <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
                <h3><i class="fas fa-reply"></i> Ответить пользователю</h3>
                <textarea name="message" required minlength="5" rows="5"
                    placeholder="Напишите ответ пользователю..."></textarea>
                <button type="submit" class="btn-send-reply">
                    <i class="fas fa-paper-plane"></i> Отправить ответ
                </button>
            </form>
        <?php else: ?>
            <div class="closed-notice">
                <i class="fas fa-lock"></i>
                <p>Обращение закрыто</p>
            </div>
        <?php endif; ?>
    </div>
</div>