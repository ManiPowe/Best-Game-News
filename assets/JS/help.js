// FAQ — раскрытие вопросов
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', function() {
        const item = this.parentElement;
        item.classList.toggle('active');
    });
});