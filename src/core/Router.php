<?php

namespace PetFactory\Core;

class Router
{
    private $routes = [];
    private $currentRoute = null;

    public function get($path, $controller, $action)
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post($path, $controller, $action)
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    private function addRoute($method, $path, $controller, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch($url)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $url = $this->parseUrl($url);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches);
                $this->currentRoute = $route;
                return $this->executeController($route['controller'], $route['action'], $matches);
            }
        }

        $this->notFound();
    }

    private function parseUrl($url)
    {
        if (empty($url)) {
            return '/';
        }
        return '/' . trim($url, '/');
    }

    private function convertToRegex($path)
    {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $path . '$#';
    }

    private function executeController($controllerName, $action, $params = [])
    {
        $controllerClass = "PetFactory\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }

    private function notFound()
    {
        http_response_code(404);
        require_once VIEWS_PATH . '/pages/404.php';
        exit;
    }
}
