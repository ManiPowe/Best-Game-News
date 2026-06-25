// ==========================================
// ИНИЦИАЛИЗАЦИЯ ТЕМЫ И ФОНА ДО ЗАГРУЗКИ DOM
// Файл: assets/js/theme-init.js
// ==========================================

(function() {
    // Получаем сохранённые настройки
    const savedTheme = localStorage.getItem('theme') || 'dark';
    let savedBg = localStorage.getItem('background_' + savedTheme);
    if (!savedBg) {
        savedBg = localStorage.getItem('background') || (savedTheme === 'light' ? 'light_1' : 'dark_1');
    }

    // Применяем тему СРАЗУ, до рендеринга
    if (savedTheme === 'light') {
        document.documentElement.classList.add('light-theme');
        document.body.classList.add('light-theme');
    }

    // Применяем фон СРАЗУ
    const bgPaths = {
        dark: {
            'none': null,
            'dark_1': '/assets/Media/Backgrounds/dark_1.jpg',
            'dark_2': '/assets/Media/Backgrounds/dark_2.jpg',
            'dark_3': '/assets/Media/Backgrounds/dark_3.jpg'
        },
        light: {
            'none': null,
            'light_1': '/assets/Media/Backgrounds/light_1.jpg',
            'light_2': '/assets/Media/Backgrounds/light_2.jpg',
            'light_3': '/assets/Media/Backgrounds/light_3.jpg'
        }
    };

    const bgPath = bgPaths[savedTheme] && bgPaths[savedTheme][savedBg];
    
    if (bgPath) {
        document.documentElement.style.setProperty('--bg-image', `url('${bgPath}')`);
    } else {
        document.documentElement.style.setProperty('--bg-image', 'none');
    }
})();