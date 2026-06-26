<?php
// Подключение к БД (поднимаемся на 2 уровня вверх: tabs → admin → корень)
require_once __DIR__ . '/../../assets/app/db.php';

// Подключение Dompdf (поднимаемся на 2 уровня вверх к корню проекта)
require_once __DIR__ . '/../../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ... дальше весь код генерации PDF

$stats = [
    'news_today' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news WHERE DATE(created_at) = CURDATE()"))['c'],
    'news_week' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"))['c'],
    'news_month' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"))['c'],
    'news_total' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news"))['c'],
    'users_total' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'],
    'users_today' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE DATE(created_at) = CURDATE()"))['c'],
    'comments_total' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM comments"))['c'],
    'comments_today' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM comments WHERE DATE(created_at) = CURDATE()"))['c'],
    'pending_news' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news WHERE status = 'pending'"))['c'],
    'featured_news' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM news WHERE is_featured = 1"))['c'],
];

$online_sql = "SELECT COUNT(*) as c FROM users WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
$users_online = mysqli_fetch_assoc(mysqli_query($conn, $online_sql))['c'] ?? 0;

$top_authors_sql = "SELECT u.id, u.login, u.avatar, COUNT(n.id) as posts_count, COALESCE(SUM(n.likes_count), 0) as total_likes 
                    FROM users u 
                    LEFT JOIN news n ON u.id = n.author_id AND n.status = 'published' 
                    GROUP BY u.id 
                    HAVING posts_count > 0 
                    ORDER BY total_likes DESC 
                    LIMIT 10";
$top_authors_result = mysqli_query($conn, $top_authors_sql);

$comments_per_post = $stats['news_total'] > 0 ? round($stats['comments_total'] / $stats['news_total'], 1) : 0;

$generated_at = date('d.m.Y H:i');

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            color: #333;
            margin: 20px;
        }
        .pdf-header {
            text-align: center;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .pdf-header h1 {
            color: #4a90e2;
            margin: 0;
            font-size: 22px;
        }
        .pdf-header .date {
            color: #888;
            font-size: 11px;
            margin-top: 5px;
        }
        
        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-row {
            display: flex;
            margin-bottom: 10px;
        }
        .stat-box {
            width: 24%;
            margin-right: 1%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            box-sizing: border-box;
        }
        .stat-box:last-child { margin-right: 0; }
        .stat-box .value {
            font-size: 22px;
            font-weight: bold;
            color: #4a90e2;
        }
        .stat-box .label {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        .stat-box .sub {
            font-size: 10px;
            color: #28a745;
            margin-top: 2px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #4a90e2;
            border-left: 3px solid #4a90e2;
            padding-left: 8px;
            margin: 20px 0 10px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #4a90e2;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        table tr:nth-child(even) td {
            background: #f9f9f9;
        }
        .medal { font-size: 14px; }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <h1>📊 Статистика сайта</h1>
        <div class="date">Отчёт сформирован: ' . $generated_at . '</div>
    </div>
    
    <div class="section-title">Основные показатели</div>
    <table>
        <tr>
            <td class="stat-box">
                <div class="value">' . $stats['news_total'] . '</div>
                <div class="label">Всего постов</div>
                <div class="sub">+' . $stats['news_today'] . ' сегодня</div>
            </td>
            <td class="stat-box">
                <div class="value">' . $stats['users_total'] . '</div>
                <div class="label">Пользователей</div>
                <div class="sub">+' . $stats['users_today'] . ' новых</div>
            </td>
            <td class="stat-box">
                <div class="value">' . $stats['comments_total'] . '</div>
                <div class="label">Комментариев</div>
                <div class="sub">+' . $stats['comments_today'] . ' сегодня</div>
            </td>
            <td class="stat-box">
                <div class="value">' . $stats['pending_news'] . '</div>
                <div class="label">На модерации</div>
                <div class="sub">' . $stats['featured_news'] . ' в слайдере</div>
            </td>
        </tr>
    </table>
    
    <div class="section-title">Активность и показатели</div>
    <table>
        <tr>
            <th>Показатель</th>
            <th>Значение</th>
        </tr>
        <tr><td>Постов за неделю</td><td><b>' . $stats['news_week'] . '</b></td></tr>
        <tr><td>Постов за месяц</td><td><b>' . $stats['news_month'] . '</b></td></tr>
        <tr><td>Постов сегодня</td><td><b>' . $stats['news_today'] . '</b></td></tr>
        <tr><td>Комментариев на пост (среднее)</td><td><b>' . $comments_per_post . '</b></td></tr>
        <tr><td>Пользователей онлайн</td><td><b>' . $users_online . '</b></td></tr>
    </table>
    
    <div class="section-title">Топ авторов</div>
    <table>
        <tr>
            <th style="width: 40px;">#</th>
            <th>Автор</th>
            <th style="text-align: center;">Постов</th>
            <th style="text-align: center;">Лайков</th>
        </tr>';

$medals = ['🥇', '🥈', '🥉'];
$counter = 0;
while ($author = mysqli_fetch_assoc($top_authors_result)) {
    $counter++;
    $rank = $medals[$counter - 1] ?? $counter;
    $html .= '
        <tr>
            <td class="medal">' . $rank . '</td>
            <td>' . htmlspecialchars($author['login']) . '</td>
            <td style="text-align: center;">' . $author['posts_count'] . '</td>
            <td style="text-align: center;">' . $author['total_likes'] . '</td>
        </tr>';
}

$html .= '
    </table>
    
    <div class="footer">
        Документ сгенерирован автоматически • ' . $generated_at . '
    </div>
</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Отдаём PDF на скачивание
$filename = 'stats_' . date('Y-m-d_H-i') . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);