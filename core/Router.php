<?php

class Router {
    private $routes = [];

    public function get($uri, $callback) {
        $this->routes['GET'][$uri] = $callback;
    }

    public function post($uri, $callback) {
        $this->routes['POST'][$uri] = $callback;
    }

    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        // Handle Windows backslash
        $basePath = str_replace('\\', '/', $basePath);
        
        if ($basePath === '/') {
            $basePath = '';
        }

        // Remove base path from URI if it exists
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        if ($uri === '' || $uri === null) {
            $uri = '/';
        }

        // Auth Middleware
        $isLoggedIn = isset($_SESSION['user_id']);
        $guestRoutes = ['/', '/login', '/register', '/verify', '/forgot-password', '/reset-password'];
        
        // Allow logout to be accessed only when logged in
        if ($isLoggedIn && in_array($uri, $guestRoutes)) {
            header('Location: ' . $basePath . '/dashboard');
            exit;
        }

        if (!$isLoggedIn && !in_array($uri, $guestRoutes)) {
            header('Location: ' . $basePath . '/login');
            exit;
        }

        $callback = $this->routes[$method][$uri] ?? false;

        if ($callback === false) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        if (is_array($callback)) {
            // $callback is [ControllerClass, 'methodName']
            $controller = new $callback[0]();
            $callback[0] = $controller;
        }

        call_user_func($callback);
    }
}
