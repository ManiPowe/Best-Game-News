<?php
// Получаем все тикеты с информацией о пользователях
$filter_status = $_GET['status'] ?? 'all';

$where_clause = "";
$params = [];
$types = "";

if ($filter_status !== 'all') {
    $where_clause = "WHERE t.status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

$tickets_sql = "SELECT t.*, u.login, u.email,
                (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id) as messages_count,
                (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id AND is_admin = 0 AND created_at > COALESCE(
                    (SELECT created_at FROM ticket_messages WHERE ticket_id = t.id AND is_admin = 1 ORDER BY created_at DESC LIMIT 1),
                    '1970-01-01'
                )) as unread_count
                FROM tickets t 
                JOIN users u ON t.user_id = u.id 
                $where_clause
                ORDER BY 
                    CASE t.status 
                        WHEN 'open' THEN 1 
                        WHEN 'answered' THEN 2 
                        WHEN 'closed' THEN 3 
                    END,
                    t.updated_at DESC";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $tickets_sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $tickets_result = mysqli_stmt_get_result($stmt);
} else {
    $tickets_result = mysqli_query($conn, $tickets_sql);
}

// Статистика
$stats = [
    'total' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tickets"))['c'],
    'open' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tickets WHERE status = 'open'"))['c'],
    'answered' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tickets WHERE status = 'answered'"))['c'],
    'closed' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tickets WHERE status = 'closed'"))['c'],
];
?>

<div class="support-section">
    <div class="content-header">
        <h2><i class="fas fa-life-ring"></i> Поддержка</h2>
    </div>

    <!-- Статистика -->
    <div class="support-stats">
        <a href="?tab=support&status=all" class="stat-card <?= $filter_status === 'all' ? 'active' : '' ?>">
            <div class="stat-number"><?= $stats['total'] ?></div>
            <div class="stat-label">Всего</div>
        </a>
        <a href="?tab=support&status=open" class="stat-card stat-open <?= $filter_status === 'open' ? 'active' : '' ?>">
            <div class="stat-number"><?= $stats['open'] ?></div>
            <div class="stat-label">Ожидают</div>
        </a>
        <a href="?tab=support&status=answered" class="stat-card stat-answered <?= $filter_status === 'answered' ? 'active' : '' ?>">
            <div class="stat-number"><?= $stats['answered'] ?></div>
            <div class="stat-label">Отвечено</div>
        </a>
        <a href="?tab=support&status=closed" class="stat-card stat-closed <?= $filter_status === 'closed' ? 'active' : '' ?>">
            <div class="stat-number"><?= $stats['closed'] ?></div>
            <div class="stat-label">Закрыто</div>
        </a>
    </div>

    <!-- Список тикетов -->
    <?php if (mysqli_num_rows($tickets_result) > 0): ?>
        <div class="tickets-admin-list">
            <?php while ($ticket = mysqli_fetch_assoc($tickets_result)): ?>
                <a href="?tab=ticket_view&id=<?= $ticket['id'] ?>" class="ticket-admin-card">
                    <div class="ticket-admin-left">
                        <div class="ticket-admin-header">
                            <span class="ticket-id">#<?= $ticket['id'] ?></span>
                            <span class="ticket-status status-<?= $ticket['status'] ?>">
                                <?php
                                switch ($ticket['status']) {
                                    case 'open': echo '<i class="fas fa-clock"></i> Ожидает'; break;
                                    case 'answered': echo '<i class="fas fa-check"></i> Отвечено'; break;
                                    case 'closed': echo '<i class="fas fa-lock"></i> Закрыт'; break;
                                }
                                ?>
                            </span>
                        </div>
                        <h3 class="ticket-admin-title"><?= htmlspecialchars($ticket['subject']) ?></h3>
                        <div class="ticket-admin-user">
                            <i class="fas fa-user"></i>
                            <span><?= htmlspecialchars($ticket['login']) ?></span>
                            <span class="ticket-admin-email"><?= htmlspecialchars($ticket['email']) ?></span>
                        </div>
                    </div>
                    <div class="ticket-admin-right">
                        <div class="ticket-admin-meta">
                            <span><i class="fas fa-comments"></i> <?= $ticket['messages_count'] ?></span>
                            <span><i class="fas fa-calendar"></i> <?= date('d.m.Y', strtotime($ticket['created_at'])) ?></span>
                        </div>
                        <div class="ticket-admin-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Нет обращений в этой категории</p>
        </div>
    <?php endif; ?>
</div>