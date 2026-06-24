// ==========================================
// КАСТОМНЫЙ FILE INPUT
// Файл: assets/js/file_input.js
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="file"]').forEach(input => {
        // Создаём кастомный label
        const label = document.createElement('label');
        label.className = 'custom-file-input';
        label.textContent = 'Выберите файл';
        
        // Оборачиваем input в div
        const wrapper = document.createElement('div');
        wrapper.className = 'file-input-wrapper';
        wrapper.appendChild(label);
        
        // Вставляем wrapper перед input
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        
        // Обновляем текст при выборе файла
        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                label.textContent = this.files[0].name;
                label.classList.add('has-file');
            } else {
                label.textContent = 'Выберите файл';
                label.classList.remove('has-file');
            }
        });
    });
});