<?php
// Проверяем, что пользователь — админ (модерам нельзя сюда)
if (!isset($user_role) || $user_role !== 'admin') {
    echo '<div class="access-denied">';
    echo '<i class="fas fa-lock" style="color: #4CAF50; font-size: 60px;"></i>';
    echo '<h2>Доступ запрещён</h2>';
    echo '<p>Эта страница доступна только администраторам</p>';
    echo '</div>';
    return; // Прерываем выполнение, не показываем контент
}

// Поиск пользователей
$search = $_GET['search'] ?? '';
$search_sql = $search ? "WHERE u.login LIKE ? OR u.email LIKE ?" : "";
$search_params = $search ? ["%{$search}%", "%{$search}%"] : [];

$users_sql = "SELECT u.id, u.login, u.email, u.avatar, u.role, u.created_at,
              (SELECT COUNT(*) FROM news n WHERE n.author_id = u.id) as news_count,
              (SELECT COUNT(*) FROM comments c WHERE c.user_id = u.id) as comments_count
              FROM users u
              {$search_sql}
              ORDER BY u.created_at DESC
              LIMIT 50";

$stmt = mysqli_prepare($conn, $users_sql);
if ($search) {
    mysqli_stmt_bind_param($stmt, "ss", ...$search_params);
}
mysqli_stmt_execute($stmt);
$users_result = mysqli_stmt_get_result($stmt);
?>

<div class="content-header">
    <h2><i class="fas fa-users"></i> Управление пользователями</h2>
</div>

<div class="users-search">
    <form method="GET" action="">
        <input type="hidden" name="tab" value="users">
        <input type="text" name="search" placeholder="Поиск по логину или email..."
            value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i> Найти</button>
    </form>
</div>

<?php if (mysqli_num_rows($users_result) > 0): ?>
    <div class="users-table-wrapper">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Новости</th>
                    <th>Комментарии</th>
                    <th>Дата регистрации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <a href="..//profile/<?= $user['id'] ?>" class="user-profile-link">
                                    <img src="../<?= htmlspecialchars($user['avatar']) ?>"
                                        alt="<?= htmlspecialchars($user['login']) ?>"
                                        onerror="this.src='../assets/Media/Photo/man.png'">
                                    <span><?= htmlspecialchars($user['login']) ?></span>
                                </a>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php
                            $role_names = [
                                'admin' => 'Админ',
                                'moderator' => 'Модератор',
                                'creator' => 'Креатор',
                                'user' => 'Пользователь'
                            ];
                            $current_role_name = $role_names[$user['role']] ?? 'Пользователь';
                            ?>
                            <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                <?= htmlspecialchars($current_role_name) ?>
                            </span>
                        </td>
                        <td><?= $user['news_count'] ?></td>
                        <td><?= $user['comments_count'] ?></td>
                        <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <form action="actions/change_role.php" method="POST" class="role-form">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
                                    <option value="creator" <?= $user['role'] === 'creator' ? 'selected' : '' ?>>Креатор</option>
                                    <option value="moderator" <?= $user['role'] === 'moderator' ? 'selected' : '' ?>>Модератор
                                    </option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Админ</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-user-slash"></i>
        <p>Пользователи не найдены</p>
    </div>
<?php endif; ?>