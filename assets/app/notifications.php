<?php
/**
 * Отправка уведомления пользователю
 */
function sendNotification($conn, $user_id, $type, $message) {
    $sql = "INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $type, $message);
    return mysqli_stmt_execute($stmt);
}

/**
 * Получение непрочитанных уведомлений
 */
function getUnreadNotifications($conn, $user_id) {
    $sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 20";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Получение количества непрочитанных
 */
function getUnreadCount($conn, $user_id) {
    $sql = "SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res)['c'];
}

/**
 * Отметить все как прочитанные
 */
function markAllAsRead($conn, $user_id) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    return mysqli_stmt_execute($stmt);
}
?>