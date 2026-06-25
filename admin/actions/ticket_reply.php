<?php
session_start();
require_once __DIR__ . '/../../assets/app/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

// Получаем роль из БД
$role_sql = "SELECT role FROM users WHERE id = ?";
$role_stmt = mysqli_prepare($conn, $role_sql);
mysqli_stmt_bind_param($role_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($role_stmt);
$user_role = mysqli_fetch_assoc(mysqli_stmt_get_result($role_stmt))['role'] ?? '';

// Только admin и moderator
if (!in_array($user_role, ['admin', 'moderator'])) {
    header("Location: /index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = (int) $_POST['ticket_id'];
    $message = trim($_POST['message'] ?? '');
    
    if (strlen($message) < 5) {
        $_SESSION['error'] = "Сообщение слишком короткое (минимум 5 символов)";
        header("Location: /admin/admin.php?tab=ticket_view&id=$ticket_id");
        exit;
    }
    
    // Получаем информацию о тикете
    $ticket_sql = "SELECT user_id, subject FROM tickets WHERE id = ?";
    $ticket_stmt = mysqli_prepare($conn, $ticket_sql);
    mysqli_stmt_bind_param($ticket_stmt, "i", $ticket_id);
    mysqli_stmt_execute($ticket_stmt);
    $ticket = mysqli_fetch_assoc(mysqli_stmt_get_result($ticket_stmt));
    
    if (!$ticket) {
        header("Location: /admin/admin.php?tab=support");
        exit;
    }
    
    // Добавляем сообщение от админа
    $msg_sql = "INSERT INTO ticket_messages (ticket_id, user_id, message, is_admin) VALUES (?, ?, ?, 1)";
    $msg_stmt = mysqli_prepare($conn, $msg_sql);
    mysqli_stmt_bind_param($msg_stmt, "iis", $ticket_id, $_SESSION['user_id'], $message);
    
    if (mysqli_stmt_execute($msg_stmt)) {
        // Обновляем статус тикета
        mysqli_query($conn, "UPDATE tickets SET status = 'answered' WHERE id = $ticket_id");
        
        // Отправляем уведомление пользователю
        require_once __DIR__ . '/../../assets/app/notifications.php';
        $short_message = mb_substr($message, 0, 100) . (mb_strlen($message) > 100 ? '...' : '');
        sendNotification(
            $conn, 
            $ticket['user_id'], 
            'ticket_reply', 
            "Получен ответ на ваше обращение «{$ticket['subject']}»:\n\n{$short_message}"
        );
        
        $_SESSION['success'] = "Ответ отправлен!";
    } else {
        $_SESSION['error'] = "Ошибка при отправке ответа";
    }
    
    header("Location: /admin/admin.php?tab=ticket_view&id=$ticket_id");
    exit;
}

header("Location: /admin/admin.php?tab=support");
exit;
?>