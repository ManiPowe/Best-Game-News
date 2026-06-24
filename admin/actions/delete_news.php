<?php
session_start();
require_once '../../assets/app/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['admin_role'] ?? '', ['admin', 'moderator'])) {
    header("Location: ../../index.php");
    exit;
}

$news_id = intval($_GET['id'] ?? 0);
if (!$news_id) {
    header("Location: ../admin.php?tab=moderation");
    exit;
}

$delete_sql = "DELETE FROM news WHERE id = ?";
$stmt = mysqli_prepare($conn, $delete_sql);
mysqli_stmt_bind_param($stmt, "i", $news_id);
mysqli_stmt_execute($stmt);

header("Location: ../admin.php?tab=moderation&msg=deleted");
exit;
?>