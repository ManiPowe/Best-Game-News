// ==========================================
// ПЕРЕКЛЮЧЕНИЕ ТЕМЫ И ВЫБОРА ФОНА
// Файл: assets/js/theme.js
// ==========================================

// Доступные фоны
const backgrounds = {
    dark: [
        { id: 'none', name: 'Без фона', path: null },
        { id: 'dark_1', name: 'Красные линии', path: '/assets/Media/Backgrounds/dark_1.jpg' },
        { id: 'dark_2', name: 'Тёмный градиент', path: '/assets/Media/Backgrounds/dark_2.jpg' },
        { id: 'dark_3', name: 'Космос', path: '/assets/Media/Backgrounds/dark_3.jpg' }
    ],
    light: [
        { id: 'none', name: 'Без фона', path: null },
        { id: 'light_1', name: 'Красные линии', path: '/assets/Media/Backgrounds/light_1.jpg' },
        { id: 'light_2', name: 'Минимализм', path: '/assets/Media/Backgrounds/light_2.jpg' },
        { id: 'light_3', name: 'Геометрия', path: '/assets/Media/Backgrounds/light_3.jpg' }
    ]
};

// Ждём полной загрузки DOM
document.addEventListener('DOMContentLoaded', function () {
    const savedTheme = localStorage.getItem('theme') || 'dark';

    // Загружаем фон именно для текущей темы (с запасным вариантом на старый ключ)
    let savedBg = localStorage.getItem('background_' + savedTheme);
    if (!savedBg) {
        savedBg = localStorage.getItem('background') || (savedTheme === 'light' ? 'light_1' : 'dark_1');
    }

    // Применяем тему
    if (savedTheme === 'light') {
        document.documentElement.classList.add('light-theme');
        document.body.classList.add('light-theme');
    }

    // Применяем фон
    applyBackground(savedBg, savedTheme);

    const themeToggle = document.getElementById('theme-toggle');
    const bgOptionsContainer = document.getElementById('background-options');

    if (!themeToggle) return;

    // Устанавливаем положение ползунка
    themeToggle.checked = savedTheme === 'light';

    // Рендерим опции фонов
    renderBackgroundOptions(bgOptionsContainer, savedTheme, savedBg);

    // Обработчик переключения темы
    themeToggle.addEventListener('change', function () {
        const newTheme = this.checked ? 'light' : 'dark';
        const oldTheme = newTheme === 'light' ? 'dark' : 'light';

        // 1. Получаем текущий фон (который был до переключения)
        let currentBg = localStorage.getItem('background_' + oldTheme);
        if (!currentBg) {
            currentBg = localStorage.getItem('background') || (oldTheme === 'light' ? 'light_1' : 'dark_1');
        }

        // 2. Сохраняем его в память для СТАРОЙ темы
        localStorage.setItem('background_' + oldTheme, currentBg);

        // 3. Получаем сохранённый фон для НОВОЙ темы
        let newBg = localStorage.getItem('background_' + newTheme);
        if (!newBg) {
            newBg = newTheme === 'light' ? 'light_1' : 'dark_1';
        }

        // 4. Переключаем классы темы
        if (newTheme === 'light') {
            document.body.classList.add('light-theme');
            document.documentElement.classList.add('light-theme');
        } else {
            document.body.classList.remove('light-theme');
            document.documentElement.classList.remove('light-theme');
        }

        localStorage.setItem('theme', newTheme);

        // 5. Применяем фон для новой темы и сохраняем его
        localStorage.setItem('background_' + newTheme, newBg);
        applyBackground(newBg, newTheme);
        renderBackgroundOptions(bgOptionsContainer, newTheme, newBg);
    });
});

// Рендер опций фонов
function renderBackgroundOptions(container, currentTheme, currentBg) {
    if (!container) return;

    container.innerHTML = '';
    const bgList = backgrounds[currentTheme];

    bgList.forEach(bg => {
        const option = document.createElement('div');
        option.className = 'background-option' + (bg.id === currentBg ? ' active' : '');

        if (bg.path === null) {
            option.innerHTML = `<div class="no-bg-preview"><i class="fas fa-ban"></i></div>`;
        } else {
            option.innerHTML = `<img src="${bg.path}" alt="${bg.name}" loading="lazy">`;
        }

        option.addEventListener('click', () => {
            // Сохраняем выбранный фон для ТЕКУЩЕЙ темы
            localStorage.setItem('background_' + currentTheme, bg.id);
            applyBackground(bg.id, currentTheme);
            renderBackgroundOptions(container, currentTheme, bg.id);
        });

        container.appendChild(option);
    });
}

// Применение фона
function applyBackground(bgId, theme) {
    const bgList = backgrounds[theme];
    const bg = bgList.find(b => b.id === bgId);

    if (bg) {
        if (bg.path === null) {
            document.body.style.setProperty('--bg-image', 'none');
        } else {
            const img = new Image();
            img.onload = function () {
                document.body.style.setProperty('--bg-image', `url('${bg.path}')`);
            };
            img.onerror = function () {
                document.body.style.setProperty('--bg-image', 'none');
            };
            img.src = bg.path;
        }
    }
}