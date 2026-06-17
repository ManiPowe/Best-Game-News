<?php
require_once 'check_auth.php';
require_once 'db.php';

$target_user_id = (int)$_POST['target_user_id'];
$review_text = trim($_POST['review_text']);
$author_id = $_SESSION['user_id'];

$errors = [];

if (empty($review_text)) {
    $errors[] = "Отзыв не может быть пустым!";
} elseif (strlen($review_text) > 1000) {
    $errors[] = "Отзыв слишком длинный! Максимум 1000 символов.";
}

if ($target_user_id === $author_id) {
    $errors[] = "Нельзя оставлять отзыв самому себе!";
}

$sql_check = "SELECT role FROM users WHERE id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt_check, "i", $target_user_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$target_user = mysqli_fetch_assoc($result_check);

if (!$target_user) {
    $errors[] = "Пользователь не найден!";
} elseif ($target_user['role'] !== 'creator' && $target_user['role'] !== 'admin') {
    $errors[] = "Отзывы можно оставлять только создателям контента!";
}

if (!empty($errors)) {
    $_SESSION['review_error'] = implode('<br>', $errors);
    header("Location: ../../profile.php?id=" . $target_user_id);
    exit;
}

$sql_duplicate = "SELECT id FROM profile_reviews WHERE author_id = ? AND target_user_id = ?";
$stmt_duplicate = mysqli_prepare($conn, $sql_duplicate);
mysqli_stmt_bind_param($stmt_duplicate, "ii", $author_id, $target_user_id);
mysqli_stmt_execute($stmt_duplicate);
mysqli_stmt_store_result($stmt_duplicate);

if (mysqli_stmt_num_rows($stmt_duplicate) > 0) {
    $_SESSION['review_error'] = "Вы уже оставляли отзыв этому пользователю!";
    header("Location: ../../profile.php?id=" . $target_user_id);
    exit;
}

$sql_insert = "INSERT INTO profile_reviews (author_id, target_user_id, text) VALUES (?, ?, ?)";
$stmt_insert = mysqli_prepare($conn, $sql_insert);
mysqli_stmt_bind_param($stmt_insert, "iis", $author_id, $target_user_id, $review_text);

if (mysqli_stmt_execute($stmt_insert)) {
    
    $_SESSION['review_success'] = "Отзыв успешно добавлен!";
    header("Location: ../../profile.php?id=" . $target_user_id);
    exit;
} else {
    $_SESSION['review_error'] = "Ошибка при добавлении отзыва: " . mysqli_error($conn);
    header("Location: ../../profile.php?id=" . $target_user_id);
    exit;
}
?>