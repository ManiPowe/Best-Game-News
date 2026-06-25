<?php
session_start();
require_once __DIR__ . '/../../assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

$role_sql = "SELECT role FROM users WHERE id = ?";
$role_stmt = mysqli_prepare($conn, $role_sql);
mysqli_stmt_bind_param($role_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($role_stmt);
$user_role = mysqli_fetch_assoc(mysqli_stmt_get_result($role_stmt))['role'] ?? '';

if (!in_array($user_role, ['admin', 'moderator'])) {
    header("Location: /index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = (int) $_POST['ticket_id'];
    
    // Получаем информацию о тикете
    $ticket_sql = "SELECT user_id, subject FROM tickets WHERE id = ?";
    $ticket_stmt = mysqli_prepare($conn, $ticket_sql);
    mysqli_stmt_bind_param($ticket_stmt, "i", $ticket_id);
    mysqli_stmt_execute($ticket_stmt);
    $ticket = mysqli_fetch_assoc(mysqli_stmt_get_result($ticket_stmt));
    
    // Закрываем тикет
    $update_sql = "UPDATE tickets SET status = 'closed' WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "i", $ticket_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        if ($ticket) {
            require_once __DIR__ . '/../../assets/app/notifications.php';
            sendNotification(
                $conn, 
                $ticket['user_id'], 
                'ticket_closed', 
                "Ваше обращение «{$ticket['subject']}» было закрыто."
            );
        }
        $_SESSION['success'] = "Обращение закрыто";
    }
    
    header("Location: /admin/admin.php?tab=ticket_view&id=$ticket_id");
    exit;
}

header("Location: /admin/admin.php?tab=support");
exit;
?>