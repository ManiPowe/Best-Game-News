<?php
// Получаем новости на модерации
$pending_sql = "SELECT n.*, u.login as author_login 
                FROM news n 
                JOIN users u ON n.author_id = u.id 
                WHERE n.status = 'pending' 
                ORDER BY n.created_at DESC";
$pending_result = mysqli_query($conn, $pending_sql);
?>

<div class="content-header">
    <h2><i class="fas fa-newspaper"></i> Модерация новостей</h2>
</div>

<?php if (mysqli_num_rows($pending_result) > 0): ?>
    <div class="moderation-list">
        <?php while ($news = mysqli_fetch_assoc($pending_result)): ?>
            <div class="moderation-card">
                <div class="moderation-image">
                    <?php if ($news['image']): ?>
                        <img src="<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    <?php else: ?>
                        <div class="no-image"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                </div>
                
                <div class="moderation-content">
                    <h3><?= htmlspecialchars($news['title']) ?></h3>
                    <p class="moderation-desc"><?= htmlspecialchars(mb_substr($news['short_description'] ?? '', 0, 150)) ?>...</p>
                    
                    <div class="moderation-meta">
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($news['author_login']) ?></span>
                        <span><i class="fas fa-clock"></i> <?= date('d.m.Y H:i', strtotime($news['created_at'])) ?></span>
                    </div>
                    
                    <div class="moderation-actions">
                        <a href="../news.php?id=<?= $news['id'] ?>" class="btn-view">
                            <i class="fas fa-eye"></i> Просмотр
                        </a>
                        <a href="actions/approve_news.php?id=<?= $news['id'] ?>" class="btn-approve">
                            <i class="fas fa-check"></i> Одобрить
                        </a>
                        <a href="actions/reject_news.php?id=<?= $news['id'] ?>" class="btn-reject" onclick="return confirm('Отклонить новость?')">
                            <i class="fas fa-times"></i> Отклонить
                        </a>
                        <a href="actions/delete_news.php?id=<?= $news['id'] ?>" class="btn-delete" onclick="return confirm('Удалить новость безвозвратно?')">
                            <i class="fas fa-trash"></i> Удалить
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-check-circle"></i>
        <p>Нет новостей на модерации</p>
    </div>
<?php endif; ?>