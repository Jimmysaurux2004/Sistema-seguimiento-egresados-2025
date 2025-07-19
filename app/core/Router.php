<?php
/**
 * Simple router class for handling HTTP requests
 * Maps URLs to controller actions
 */

class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = $basePath;
    }
    
    /**
     * Add GET route
     */
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }
    
    /**
     * Add POST route
     */
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }
    
    /**
     * Add route to routes array
     */
    private function addRoute($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }
    
    /**
     * Match current request to routes
     */
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string and base path
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = str_replace($this->basePath, '', $path);
        $path = $path ?: '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                if ($this->matchPattern($route['pattern'], $path, $matches)) {
                    return $this->callAction($route['callback'], $matches);
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        require VIEW_PATH . '/errors/404.php';
    }
    
    /**
     * Match URL pattern
     */
    private function matchPattern($pattern, $path, &$matches) {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $path, $matches);
    }
    
    /**
     * Call controller action
     */
    private function callAction($callback, $matches = []) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, array_slice($matches, 1));
        }
        
        if (is_string($callback)) {
            list($controller, $action) = explode('@', $callback);
            
            $controllerFile = APP_PATH . '/controllers/' . $controller . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controller)) {
                    $instance = new $controller();
                    if (method_exists($instance, $action)) {
                        return call_user_func_array([$instance, $action], array_slice($matches, 1));
                    }
                }
            }
        }
        
        throw new Exception("Route callback not found");
    }
}
?>