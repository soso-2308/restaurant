<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $basePath;
    private ?Container $container = null;

    public function __construct(?Container $container = null)
    {
        $this->container = $container;
        $this->basePath = defined('BASE_URL') ? BASE_URL : '/restaurant';
    }

    public function get(string $path, string $handler): self
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): self
    {
        return $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, string $handler): self
    {
        $this->routes[strtoupper($method)][$this->basePath . $path] = $handler;
        return $this;
    }

    public function group(array $options, callable $callback): void
    {
        $this->middlewares[] = $options['middleware'] ?? null;
        $callback($this);
        array_pop($this->middlewares);
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (isset($this->routes[$method][$uri])) {
            $this->execute($this->routes[$method][$uri]);
            return;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = preg_replace('/\{[a-z]+\}/', '([0-9]+)', $route);
            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches);
                $this->execute($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page non trouvée";
    }

    private function execute(string $handler, array $params = []): void
    {
        foreach ($this->middlewares as $mw) {
            if ($mw && class_exists($mw)) {
                (new $mw())->handle();
            }
        }

        [$controller, $method] = explode('@', $handler);
        $class = 'App\\Controllers\\' . $controller;

        if (!class_exists($class)) {
            throw new \Exception("Controller '$class' not found");
        }

        $instance = $this->container ? $this->container->get($class) : new $class();

        if (!method_exists($instance, $method)) {
            throw new \Exception("Method '$method' not found in '$class'");
        }

        call_user_func_array([$instance, $method], $params);
    }
}