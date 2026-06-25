function redirectToLogin() {
    sessionStorage.setItem('redirect_after_login', window.location.href);
    window.location.href = '//login?error=need_auth';
}

document.addEventListener('DOMContentLoaded', function () {

    // Обработчик лайков
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            const newsId = this.dataset.newsId;
            const countSpan = this.querySelector('.action-count');
            const btn = this;

            fetch('/assets/app/toggle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ news_id: newsId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем счётчик
                        countSpan.textContent = data.likes_count;

                        // Переключаем класс active
                        if (data.liked) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    } else {
                        alert(data.message || 'Ошибка');
                        if (data.message === 'Необходимо войти в систему') {
                            redirectToLogin();
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при обработке запроса');
                });
        });
    });

    // Обработчик избранного
    document.querySelectorAll('.favorite-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            const newsId = this.dataset.newsId;
            const countSpan = this.querySelector('.action-count');
            const btn = this;

            fetch('/assets/app/toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ news_id: newsId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем счётчик
                        countSpan.textContent = data.favorites_count;

                        // Переключаем класс active
                        if (data.favorited) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    } else {
                        alert(data.message || 'Ошибка');
                        if (data.message === 'Необходимо войти в систему') {
                            redirectToLogin();
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при обработке запроса');
                });
        });
    });

});


// Лайки комментариев (для news.php и profile.php)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.comment-like-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            const commentId = this.dataset.commentId;
            const countSpan = this.querySelector('.comment-like-count');
            const btn = this;

            fetch('/assets/app/toggle_comment_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ comment_id: commentId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        countSpan.textContent = data.likes_count;

                        if (data.liked) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    } else {
                        alert(data.message || 'Ошибка');
                        if (data.message === 'Необходимо войти в систему') {
                            redirectToLogin();
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при обработке запроса');
                });
        });
    });
});
