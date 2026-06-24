function toggleLike(newsId, btn) {
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
            btn.querySelector('span').textContent = data.likes_count;
            
            // Переключаем класс active
            if (data.liked) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка сети');
    });
}

function toggleFavorite(newsId, btn) {
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
            btn.querySelector('span').textContent = data.favorites_count;
            
            // Переключаем класс active
            if (data.favorited) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка сети');
    });
}