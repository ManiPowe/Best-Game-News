document.addEventListener('DOMContentLoaded', function() {
    // Лайки комментариев
    const commentBtns = document.querySelectorAll('.comment-like-btn');
    commentBtns.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const commentId = this.dataset.commentId;
            const countSpan = this.querySelector('.comment-like-count');
            const btn = this;
            
            if (!commentId) return;
            
            fetch('/assets/app/toggle_comment_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ comment_id: parseInt(commentId) })
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
                }
            })
            .catch(error => console.error('Ошибка лайка комментария:', error));
        });
    });

    // Лайки отзывов
    const reviewBtns = document.querySelectorAll('.review-like-btn');
    reviewBtns.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const reviewId = this.dataset.reviewId;
            const countSpan = this.querySelector('.review-like-count');
            const btn = this;
            
            if (!reviewId) return;
            
            fetch('/assets/app/toggle_review_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ review_id: parseInt(reviewId) })
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
                }
            })
            .catch(error => console.error('Ошибка лайка отзыва:', error));
        });
    });
});