<?php
session_start();
require_once 'assets/app/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = trim($_POST['login_input']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE login = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $login_input, $login_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['avatar'] = $user['avatar'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Неверный логин/email или пароль!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reg.css">  

    <link rel="icon" href="/assets/Media/Photo/asd.png">
    <title>Вход - Best Game News</title>
</head>
<body>
    <header>
        <div class="header">
            <div class="logo-wrap">
                <a class="logo-link" href="index.php">
                    <img src="/assets/Media/Photo/Logo.png" alt="Логотип Best Game News">
                </a>
                <div class="logo">Best Game News</div>
            </div>
            <nav class="nav">
                <a href="index.php">Главная</a>
                <a href="reg.php">Регистрация</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="main-container">
            <div class="auth-container">
                <h2>Вход</h2>
                <?php if ($error): ?>
                    <p style="color: #ff4444; text-align: center; margin-bottom: 15px; background: rgba(255,68,68,0.1); padding: 8px; border-radius: 5px;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <form class="auth-form" method="POST" action="login.php">
                    <div class="form-group">
                        <label for="login-username">Имя пользователя или Email</label>
                        <input type="text" id="login-username" name="login_input" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Пароль</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <button type="submit" class="submit-btn">Войти</button>
                </form>
                <p class="auth-link">Нет аккаунта? <a href="reg.php">Зарегистрироваться</a></p>
            </div>
        </div>
    </main>

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
</body>
</html>