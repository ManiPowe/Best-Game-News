<?php
session_start();
require_once 'db.php';

// ==========================================
// ШАГ 1: ПРОВЕРКА АВТОРИЗАЦИИ
// ==========================================
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ==========================================
// ШАГ 2: ПОЛУЧАЕМ ДАННЫЕ ИЗ ФОРМЫ
// ==========================================
// trim() удаляет пробелы в начале и конце строки
$new_login = trim($_POST['login']);
$new_email = trim($_POST['email']);
$new_phone = trim($_POST['phone']);
$new_bio = trim($_POST['bio']);

// ==========================================
// ШАГ 3: ВАЛИДАЦИЯ ДАННЫХ
// ==========================================
$errors = [];

// Проверка логина
if (strlen($new_login) < 6) {
    $errors[] = "Логин должен быть не менее 6 символов!";
}

// Проверка email
// filter_var() с FILTER_VALIDATE_EMAIL проверяет формат email по стандарту RFC
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некорректный email!";
}

// Проверка телефона (разрешаем цифры, +, -, пробелы, скобки)
// preg_match() проверяет строку по регулярному выражению
if (!preg_match('/^[\d\s\+\-\(\)]+$/', $new_phone)) {
    $errors[] = "Некорректный телефон!";
}

// Если есть ошибки — возвращаемся с сообщением
if (!empty($errors)) {
    $_SESSION['profile_message'] = implode('<br>', $errors);
    $_SESSION['profile_message_type'] = 'error';
    header("Location: ../../cab.php");
    exit;
}

// ==========================================
// ШАГ 4: ПРОВЕРКА НА ДУБЛИКАТЫ
// ==========================================
// Проверяем, не занят ли новый логин или email ДРУГИМ пользователем
// "AND id != ?" — исключаем текущего пользователя из проверки
$check_sql = "SELECT id FROM users WHERE (login = ? OR email = ?) AND id != ?";
$stmt_check = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt_check, "ssi", $new_login, $new_email, $user_id);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if (mysqli_stmt_num_rows($stmt_check) > 0) {
    $_SESSION['profile_message'] = "Этот логин или email уже используется другим пользователем!";
    $_SESSION['profile_message_type'] = 'error';
    header("Location: ../../cab.php");
    exit;
}

// ==========================================
// ШАГ 5: ОБНОВЛЕНИЕ В БАЗЕ ДАННЫХ
// ==========================================
$sql_update = "UPDATE users SET login = ?, email = ?, phone = ?, bio = ? WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $sql_update);
// "ssssi" означает: 4 строки (s) + 1 число (i)
mysqli_stmt_bind_param($stmt_update, "ssssi", $new_login, $new_email, $new_phone, $new_bio, $user_id);

if (mysqli_stmt_execute($stmt_update)) {
    // ✅ Успех! Обновляем сессию, чтобы изменения сразу отобразились
    $_SESSION['login'] = $new_login;
    
    $_SESSION['profile_message'] = "Профиль успешно обновлён!";
    $_SESSION['profile_message_type'] = 'success';
    
    header("Location: ../../cab.php");
    exit;
} else {
    $_SESSION['profile_message'] = "Ошибка при сохранении: " . mysqli_error($conn);
    $_SESSION['profile_message_type'] = 'error';
    header("Location: ../../cab.php");
    exit;
}
?>