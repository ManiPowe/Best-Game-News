<?php
// Получаем статистику
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

// Считаем пользователей онлайн (активность за последние 5 минут)
$online_sql = "SELECT COUNT(*) as c FROM users WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
$online_result = mysqli_query($conn, $online_sql);
$users_online = mysqli_fetch_assoc($online_result)['c'] ?? 0;

// Топ авторов по количеству новостей
$top_authors_sql = "SELECT u.*, COUNT(n.id) as posts_count, COALESCE(SUM(n.likes_count), 0) as total_likes 
                    FROM users u 
                    LEFT JOIN news n ON u.id = n.author_id AND n.status = 'published' 
                    GROUP BY u.id 
                    HAVING posts_count > 0 
                    ORDER BY total_likes DESC 
                    LIMIT 10";
$top_authors_result = mysqli_query($conn, $top_authors_sql);
?>

<div class="content-header">
    <h2><i class="fas fa-chart-bar"></i> Статистика сайта</h2>
    <a href="tabs/stats_pdf.php" class="btn-export-pdf" target="_blank">
        <i class="fas fa-file-pdf"></i> Экспорт в PDF
    </a>
</div>

<div class="admin-stats-layout">
    <!-- ЛЕВАЯ ШИРОКАЯ ЧАСТЬ -->
    <div class="stats-main-area">
        <!-- Основные карточки в 2 колонки -->
        <div class="stats-main-grid">
            <div class="stat-card-main news-stat">
                <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['news_total'] ?></div>
                    <div class="stat-label">Всего постов</div>
                    <div class="stat-change">+<?= $stats['news_today'] ?> сегодня</div>
                </div>
            </div>

            <div class="stat-card-main users-stat">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['users_total'] ?></div>
                    <div class="stat-label">Пользователей</div>
                    <div class="stat-change">+<?= $stats['users_today'] ?> новых</div>
                </div>
            </div>

            <div class="stat-card-main comments-stat">
                <div class="stat-icon"><i class="fas fa-comments"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['comments_total'] ?></div>
                    <div class="stat-label">Комментариев</div>
                    <div class="stat-change">+<?= $stats['comments_today'] ?> сегодня</div>
                </div>
            </div>

            <div class="stat-card-main moderation-stat">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['pending_news'] ?></div>
                    <div class="stat-label">На модерации</div>
                    <div class="stat-change"><?= $stats['featured_news'] ?> в слайдере</div>
                </div>
            </div>
        </div>

        <!-- Активность и показатели в 2 колонки -->
        <!-- Графики вместо текстовых блоков -->
        <div class="stats-detail-grid">
            <div class="stat-detail-card">
                <h3><i class="fas fa-chart-line"></i> Активность постов</h3>
                <canvas id="activityChart" style="max-height: 250px;"></canvas>
            </div>

            <div class="stat-detail-card">
                <h3><i class="fas fa-chart-pie"></i> Распределение контента</h3>
                <canvas id="categoryChart" style="max-height: 250px;"></canvas>
            </div>
        </div>

        <!-- Подключаем Chart.js -->
        <script src="/assets/js/chart.min.js"></script>
        <script>
            // Настройка для тёмной темы
            Chart.defaults.color = '#ffffff';
            Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

            // График активности (последние 7 дней)
            fetch('/admin/ajax/stats.php?action=activity')
                .then(res => res.json())
                .then(data => {
                    new Chart(document.getElementById('activityChart'), {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Постов',
                                data: data.data,
                                borderColor: '#df1b1b',
                                backgroundColor: 'rgba(223, 27, 27, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#df1b1b',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: '#df1b1b',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#b0b0b0'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#b0b0b0'
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                });

            // График категорий
            fetch('/admin/ajax/stats.php?action=categories')
                .then(res => res.json())
                .then(data => {
                    new Chart(document.getElementById('categoryChart'), {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: data.colors,
                                borderWidth: 0,
                                hoverOffset: 15
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        color: '#fff',
                                        font: { size: 12 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: '#df1b1b',
                                    borderWidth: 1
                                }
                            }
                        }
                    });
                });
        </script>
    </div>

    <!-- ПРАВАЯ УЗКАЯ ЧАСТЬ: ТОП АВТОРОВ -->
    <div class="stats-sidebar">
        <div class="stats-section">
            <h3><i class="fas fa-trophy"></i> Топ авторов</h3>
            <div class="top-authors-list">
                <?php
                $medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
                $counter = 0;
                while ($author = mysqli_fetch_assoc($top_authors_result)):
                    $counter++;
                    ?>
                    <div class="top-author-card">
                        <div class="author-rank"><?= $medals[$counter - 1] ?? $counter ?></div>
                        <img src="../<?= htmlspecialchars($author['avatar']) ?>"
                            alt="<?= htmlspecialchars($author['login']) ?>">
                        <div class="top-author-info">
                            <h4><?= htmlspecialchars($author['login']) ?></h4>
                            <span><?= $author['posts_count'] ?>
                                <?= $author['posts_count'] == 1 ? 'пост' : ($author['posts_count'] < 5 ? 'поста' : 'постов') ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>