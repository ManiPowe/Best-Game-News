<?php
// Получаем новости на модерации
$pending_news_sql = "SELECT n.*, u.login as author_login, u.avatar as author_avatar, g.name as game_name, g.icon as game_icon 
                     FROM news n 
                     JOIN users u ON n.author_id = u.id 
                     LEFT JOIN games g ON n.game_id = g.id 
                     WHERE n.status = 'pending' 
                     ORDER BY n.created_at DESC";
$pending_news_result = mysqli_query($conn, $pending_news_sql);
?>

<div class="moderation-header">
    <div class="moderation-title">
        <h2><i class="fas fa-user-shield"></i> Модерация новостей</h2>
        <p>На проверке: <strong><?= mysqli_num_rows($pending_news_result) ?></strong> новостей</p>
    </div>
</div>

<?php if (mysqli_num_rows($pending_news_result) > 0): ?>
    <div class="moderation-cards">
        <?php while ($news = mysqli_fetch_assoc($pending_news_result)): ?>
            <div class="mod-card">
                <!-- Верхняя панель с кнопками -->
                <div class="mod-card-actions">
                    <div class="mod-card-info">
                        <?php if ($news['game_name']): ?>
                            <span class="game-badge">
                                <?php if ($news['game_icon']): ?>
                                    <img src="../<?= htmlspecialchars($news['game_icon']) ?>" alt="">
                                <?php endif; ?>
                                <?= htmlspecialchars($news['game_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="mod-buttons">
                        <form action="/admin/actions/approve_news.php" method="POST" style="display: inline;">
                            <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                            <button type="submit" class="btn-approve-small" title="Одобрить">
                                <i class="fas fa-check"></i> Одобрить
                            </button>
                        </form>
                        <form action="/admin/actions/reject_news.php" method="POST" style="display: inline;"
                            class="reject-form">
                            <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                            <button type="button" class="btn-reject-small" title="Отклонить"
                                onclick="openRejectModal(<?= $news['id'] ?>, '<?= htmlspecialchars(addslashes($news['title'])) ?>')">
                                <i class="fas fa-times"></i> Отклонить
                            </button>
                        </form>

                        <!-- Модальное окно для ввода причины -->
                        <div id="reject-modal-<?= $news['id'] ?>" class="reject-modal" style="display: none;">
                            <div class="reject-modal-content">
                                <h3><i class="fas fa-times-circle"></i> Отклонить новость</h3>
                                <p class="reject-news-title">Новость: <strong><?= htmlspecialchars($news['title']) ?></strong>
                                </p>
                                <form action="/admin/actions/reject_news.php" method="POST">
                                    <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                                    <div class="form-group">
                                        <label for="reason-<?= $news['id'] ?>">Укажите причину отклонения:</label>
                                        <textarea name="reason" id="reason-<?= $news['id'] ?>" required
                                            placeholder="Например: Новость не соответствует тематике сайта..." minlength="10"
                                            maxlength="500" rows="4"></textarea>
                                        <small class="form-hint">Минимум 10 символов. Эта причина будет отправлена
                                            автору.</small>
                                    </div>
                                    <div class="modal-actions">
                                        <button type="button" class="btn-cancel"
                                            onclick="closeRejectModal(<?= $news['id'] ?>)">Отмена</button>
                                        <button type="submit" class="btn-reject-confirm">
                                            <i class="fas fa-times"></i> Отклонить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <a href="/news.php?id=<?= $news['id'] ?>" class="btn-view-small" target="_blank" title="Просмотреть">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                <!-- Контент новости -->
                <div class="mod-card-content">
                    <?php if ($news['image']): ?>
                        <div class="mod-card-image">
                            <img src="../<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                        </div>
                    <?php endif; ?>

                    <div class="mod-card-text">
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p class="mod-description"><?= nl2br(htmlspecialchars(mb_substr($news['content'], 0, 100000000))) ?>

                        </p>

                        <?php if ($news['tags']): ?>
                            <div class="mod-tags">
                                <?php
                                $tags = explode(',', $news['tags']);
                                foreach (array_slice($tags, 0, 5) as $tag):
                                    ?>
                                    <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mod-card-footer">
                            <div class="mod-author">
                                <img src="../<?= htmlspecialchars($news['author_avatar']) ?>" alt="">
                                <span>Автор: <?= htmlspecialchars($news['author_login']) ?></span>
                            </div>
                            <div class="mod-date">
                                <i class="fas fa-clock"></i> <?= date('d.m.Y H:i', strtotime($news['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <script src="/assets/js/reject-modal.js"></script>
<?php else: ?>
    <div class="empty-moderation">
        <i class="fas fa-check-circle"></i>
        <h3>Все новости проверены!</h3>
        <p>Нет новостей, ожидающих модерации</p>
    </div>
<?php endif; ?>