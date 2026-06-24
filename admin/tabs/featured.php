<?php
// Получаем все новости
$featured_sql = "SELECT n.*, u.login as author_login 
                 FROM news n 
                 JOIN users u ON n.author_id = u.id 
                 WHERE n.status = 'published' 
                 ORDER BY n.is_featured DESC, n.created_at DESC";
$featured_result = mysqli_query($conn, $featured_sql);
?>

<div class="content-header">
    <h2><i class="fas fa-star"></i> Новости недели</h2>
    <p class="tab-description">Выберите новости, которые будут отображаться в главном слайдере на главной странице</p>
</div>

<?php if (mysqli_num_rows($featured_result) > 0): ?>
    <div class="featured-list">
        <?php while ($news = mysqli_fetch_assoc($featured_result)): ?>
            <div class="featured-card <?= $news['is_featured'] ? 'is-featured' : '' ?>">
                <div class="featured-image">
                    <?php
                    $img_path = $news['image'] ?? '';
                    // Если путь есть и не начинается с / или http — добавляем ../
                    if ($img_path && strpos($img_path, '/') !== 0 && strpos($img_path, 'http') !== 0) {
                        $img_path = '../' . $img_path;
                    }
                    ?>
                    <?php if ($img_path): ?>
                        <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    <?php else: ?>
                        <div class="no-image"><i class="fas fa-image"></i></div>
                    <?php endif; ?>

                    <?php if ($news['is_featured']): ?>
                        <div class="featured-badge">
                            <i class="fas fa-star"></i> В слайдере
                        </div>
                    <?php endif; ?>
                </div>

                <div class="featured-content">
                    <h3><?= htmlspecialchars($news['title']) ?></h3>
                    <p class="featured-desc"><?= htmlspecialchars(mb_substr($news['short_description'] ?? '', 0, 100)) ?>...</p>

                    <div class="featured-meta">
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($news['author_login']) ?></span>
                        <span><i class="fas fa-eye"></i> <?= $news['views'] ?> просмотров</span>
                        <span><i class="fas fa-heart"></i> <?= $news['likes_count'] ?> лайков</span>
                    </div>

                    <div class="featured-actions">
                        <a href="../news.php?id=<?= $news['id'] ?>" class="btn-view">
                            <i class="fas fa-eye"></i> Просмотр
                        </a>
                        <a href="actions/toggle_featured.php?id=<?= $news['id'] ?>"
                            class="btn-<?= $news['is_featured'] ? 'reject' : 'approve' ?>">
                            <i class="fas fa-star"></i>
                            <?= $news['is_featured'] ? 'Убрать из слайдера' : 'Добавить в слайдер' ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-newspaper"></i>
        <p>Нет опубликованных новостей</p>
    </div>
<?php endif; ?>