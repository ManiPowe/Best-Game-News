// Модальное окно отклонения новости

function openRejectModal(newsId, title) {
    const modal = document.getElementById('reject-modal-' + newsId);
    if (modal) {
        modal.style.display = 'flex';
        // Фокус на textarea
        const textarea = modal.querySelector('textarea');
        if (textarea) {
            setTimeout(() => textarea.focus(), 100);
        }
    }
}

function closeRejectModal(newsId) {
    const modal = document.getElementById('reject-modal-' + newsId);
    if (modal) {
        modal.style.display = 'none';
        // Очищаем textarea
        const textarea = modal.querySelector('textarea');
        if (textarea) {
            textarea.value = '';
        }
    }
}

// Закрытие по клику вне модального окна
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('reject-modal')) {
        e.target.style.display = 'none';
        // Очищаем textarea
        const textarea = e.target.querySelector('textarea');
        if (textarea) {
            textarea.value = '';
        }
    }
});

// Закрытие по Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.reject-modal').forEach(modal => {
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
                const textarea = modal.querySelector('textarea');
                if (textarea) {
                    textarea.value = '';
                }
            }
        });
    }
});