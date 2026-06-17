<?php
require_once 'check_auth.php';
require_once 'db.php';

$news_id = (int)$_POST['news_id'];
$comment_text = trim($_POST['comment_text']);
$user_id = $_SESSION['user_id'];

if (empty($comment_text)) {
    $_SESSION['comment_error'] = "Комментарий не может быть пустым!";
    header("Location: ../../news.php?id=" . $news_id);
    exit;
}

$sql = "INSERT INTO comments (news_id, user_id, text) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $news_id, $user_id, $comment_text);

if (mysqli_stmt_execute($stmt)) {
    // Увеличиваем счётчик комментариев у пользователя
    mysqli_query($conn, "UPDATE users SET comments_count = comments_count + 1 WHERE id = $user_id");
    
    header("Location: ../../news.php?id=" . $news_id);
    exit;
} else {
    die("Ошибка: " . mysqli_error($conn));
}
?>