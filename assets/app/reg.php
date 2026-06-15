<?php
    require_once('db.php');

    // 1. Получаем данные из формы
    $login = $_POST['login'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // 2. ПРОВЕРКА: Существует ли уже пользователь с таким логином или email?
    // Используем подготовленный запрос (?) для защиты от SQL-инъекций
    $check_sql = "SELECT id FROM users WHERE login = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "ss", $login, $email); // "ss" означает две строки
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        die("Пользователь с таким логином или email уже существует! <a href='reg.html'>Назад</a>");
    }

    // 3. ХЭШИРОВАНИЕ ПАРОЛЯ
    // password_hash() создает уникальный хэш. Даже если два пользователя поставят пароль "123",
    // их хэши будут разными (из-за случайной "соли").
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. Вставляем пользователя в базу (снова подготовленный запрос)
    $sql = "INSERT INTO users (login, password, name, email, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    // Привязываем переменные к параметрам запроса
    mysqli_stmt_bind_param($stmt, "sssss", $login, $hashed_password, $name, $email, $phone);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../login.html"); // Перенаправляем на страницу входа
        exit;
    } else {
        echo "Ошибка при регистрации: " . mysqli_error($conn);
    }
?>