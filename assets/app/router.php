<?php
/**
 * Простой PHP-роутер
 */

class Router {
    private static $routes = [];
    private static $params = [];
    
    /**
     * Добавить маршрут
     */
    public static function add($pattern, $handler) {
        self::$routes[] = [
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    /**
     * Запустить роутер
     */
    public static function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        
        foreach (self::$routes as $route) {
            $pattern = $route['pattern'];
            $handler = $route['handler'];
            
            // Преобразуем :param в регулярку
            $regex = preg_replace('/:[a-zA-Z]+/', '([0-9a-zA-Z_-]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // Убираем полное совпадение
                
                // Извлекаем имена параметров
                preg_match_all('/:([a-zA-Z]+)/', $pattern, $paramNames);
                $params = [];
                foreach ($paramNames[1] as $i => $name) {
                    $params[$name] = $matches[$i] ?? null;
                }
                
                // Вызываем обработчик
                if (is_callable($handler)) {
                    call_user_func($handler, $params);
                } elseif (is_string($handler) && file_exists($handler)) {
                    extract($params);
                    include $handler;
                }
                return;
            }
        }
        
        // 404
        http_response_code(404);
        include 'views/404.php';
    }
}