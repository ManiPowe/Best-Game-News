// ==========================================
// ПЕРЕЗАГРУЗКА СТРАНИЦЫ ПРИ НАВИГАЦИИ "НАЗАД"
// Файл: assets/js/no-cache.js
// ==========================================

(function() {
    // Отключаем кэш браузера при навигации назад
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Страница загружена из bfcache (кэш истории) — перезагружаем
            window.location.reload();
        }
    });

    // Дополнительная защита — метка времени в URL при переходе
    // Предотвращает возврат к старой версии через историю
    if (window.performance && window.performance.navigation.type === 2) {
        // type === 2 означает навигацию через "Назад/Вперёд"
        window.location.reload();
    }
})();