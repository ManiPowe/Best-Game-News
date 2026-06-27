<?php
require_once __DIR__ . '/assets/app/router.php';

// Главная - редирект на /home
Router::add('/', function() {
    header('Location: /home');
    exit;
});

// Главная страница
Router::add('/home', function() {
    include 'index.php';
});

// Новости
Router::add('/news/:id', function($params) {
    $_GET['id'] = $params['id'];
    include 'news.php';
});

// Профиль
Router::add('/profile/:id', function($params) {
    $_GET['id'] = $params['id'];
    include 'profile.php';
});

// Категории
Router::add('/category/:type', function($params) {
    $_GET['type'] = $params['type'];
    include 'category.php';
});

Router::add('/category/:type/:game_id', function($params) {
    $_GET['type'] = $params['type'];
    $_GET['game_id'] = $params['game_id'];
    include 'category.php';
});

// Тикет
Router::add('/ticket/:id', function($params) {
    $_GET['id'] = $params['id'];
    include 'ticket.php';
});

// Редактирование новости
Router::add('/edit/:id', function($params) {
    $_GET['id'] = $params['id'];
    include 'edit_news.php';
});

// Простые страницы
Router::add('/help', function() { include 'help.php'; });
Router::add('/login', function() { include 'login.php'; });
Router::add('/reg', function() { include 'reg.php'; });
Router::add('/cab', function() { include 'cab.php'; });
Router::add('/search', function() { include 'search.php'; });
Router::add('/create', function() { include 'create_news.php'; });

// Запускаем роутер
Router::run();