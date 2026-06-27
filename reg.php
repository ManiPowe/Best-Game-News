<?php
session_start();
require_once 'assets/app/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_raw = $_POST['phone'] ?? '';
    $phone = preg_replace('/[^0-9+]/', '', $phone_raw);

    if (strlen($phone) !== 12) {
        $error = "Введите корректный номер телефона";
    } elseif (strlen($password) < 8) {
        $error = "Пароль должен быть не менее 8 символов!";
    } else {
        $check_sql = "SELECT id FROM users WHERE login = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "ss", $login, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Пользователь с таким логином или email уже существует!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (login, password, name, email, phone, avatar) VALUES (?, ?, ?, ?, ?, 'assets/Media/Photo/man.png')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $login, $hashed_password, $name, $email, $phone);

            if (mysqli_stmt_execute($stmt)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['login'] = $login;
                $_SESSION['avatar'] = 'assets/Media/Photo/man.png';

                header("Location: /home");
                exit;
            } else {
                $error = "Ошибка при регистрации: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/reg.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="assets/Media/Photo/asd.png">
    <title>Регистрация - Best Game News</title>
</head>

<body>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/jquery.inputmask.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#phone').inputmask('+7 (999) 999-99-99', {
                clearIncomplete: true,
                showMaskOnHover: false,
                showMaskOnFocus: true
            });

            $('input[name="phone"]').inputmask('+7 (999) 999-99-99');
        });
    </script>
    <script src="/assets/js/theme-init.js"></script>
    <script src="/assets/js/no-cache.js"></script>
    
    <!-- хедер -->
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="/home">
                    <img src="assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="/home">Главная</a>
                <a href="/login">Вход</a>
            </nav>
        </div>
    </header>
    <!-- /хедер -->

    <!-- основной контент -->
    <main>
        <div class="main-container">
            <div class="auth-container">
                <h2>Регистрация</h2>
                <?php if ($error): ?>
                    <p
                        style="color: #ff4444; text-align: center; margin-bottom: 15px; background: rgba(255,68,68,0.1); padding: 8px; border-radius: 5px;">
                        <?= htmlspecialchars($error) ?>
                    </p>
                <?php endif; ?>
                <form class="auth-form" method="POST">
                    <div class="form-group">
                        <label>Логин</label>
                        <input type="text" name="login" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label>Имя</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="tel" id="phone" name="phone" placeholder="+7 (___) ___-__-__" required>
                    </div>
                    <button type="submit" class="submit-btn">Зарегистрироваться</button>
                </form>
                <p class="auth-link">Уже есть аккаунт? <a href="/login">Войти</a></p>
            </div>
        </div>
    </main>
    <!-- /основной контент -->

    <!-- футер -->
    <footer>
        <div class="footer">
            <div class="footer-logo">
                <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                <p>Best Game News</p>
            </div>
            <div class="prav">
                <p>2026 © Все права защищенны Best Game News</p>
            </div>
            <div class="social-links">
                <a href="#" aria-label="ВКонтакте">
                    <button type="button"><img src="/assets/Media/Photo/vk.png" alt="ВКонтакте"></button>
                </a>
                <a href="#" aria-label="Discord">
                    <button type="button"><img src="/assets/Media/Photo/discord.png" alt="Discord"></button>
                </a>
                <a href="#" aria-label="YouTube">
                    <button type="button"><img src="/assets/Media/Photo/youtube.png" alt="YouTube"></button>
                </a>
                <a href="#" aria-label="Telegram">
                    <button type="button"><img src="/assets/Media/Photo/tellegram.png" alt="Telegram"></button>
                </a>
                <a href="#" aria-label="Steam">
                    <button type="button"><img src="/assets/Media/Photo/steam.png" alt="Steam"></button>
                </a>
                <a href="#" aria-label="Twitch">
                    <button type="button"><img src="/assets/Media/Photo/twitch.png" alt="Twitch"></button>
                </a>
            </div>
        </div>
    </footer>
    <!-- /футер -->
</body>

</html>