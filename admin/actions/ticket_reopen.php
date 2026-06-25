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
    
    $update_sql = "UPDATE tickets SET status = 'open' WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "i", $ticket_id);
    mysqli_stmt_execute($update_stmt);
    
    $_SESSION['success'] = "Обращение открыто снова";
    
    header("Location: /admin/admin.php?tab=ticket_view&id=$ticket_id");
    exit;
}

header("Location: /admin/admin.php?tab=support");
exit;
?>