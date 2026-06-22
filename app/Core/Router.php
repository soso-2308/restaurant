<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $basePath = '/restaurant-ryoha';
    private $container;

    public function __construct(Container $container = null)
    {
        $this->container = $container ?? new Container();
    }

    /**
     * Ajouter une route GET
     */
    public function get(string $path, string $handler): self
    {
        return $this->add('GET', $path, $handler);
    }

    /**
     * Ajouter une route POST
     */
    public function post(string $path, string $handler): self
    {
        return $this->add('POST', $path, $handler);
    }

    /**
     * Ajouter une route avec une méthode spécifique
     */
    public function add(string $method, string $path, string $handler): self
    {
        $this->routes[strtoupper($method)][$this->basePath . $path] = $handler;
        return $this;
    }

    /**
     * Grouper des routes avec des middlewares
     */
    public function group(array $options, callable $callback): void
    {
        $this->middlewares[] = $options['middleware'] ?? null;
        $callback($this);
        array_pop($this->middlewares);
    }

    /**
     * Lancer le routeur
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Vérifier les routes exactes
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            $this->execute($handler);
            return;
        }

        // Vérifier les routes avec paramètres (ex: /admin/menu/edit/5)
        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = preg_replace('/\{[a-z]+\}/', '([0-9]+)', $route);
            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches);
                $this->execute($handler, $matches);
                return;
            }
        }

        // Route non trouvée
        http_response_code(404);
        echo "404 - Page non trouvée";
    }

    /**
     * Exécuter un contrôleur
     */
    private function execute(string $handler, array $params = []): void
    {
        // Appliquer les middlewares
        foreach ($this->middlewares as $middleware) {
            if ($middleware && class_exists($middleware)) {
                $instance = new $middleware();
                $instance->handle();
            }
        }

        list($controller, $method) = explode('@', $handler);
        $controllerClass = 'App\\Controllers\\' . $controller;

        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller '$controllerClass' not found");
        }

        // Utiliser le conteneur pour instancier le contrôleur
        $instance = $this->container->get($controllerClass);
        
        if (!method_exists($instance, $method)) {
            throw new \Exception("Method '$method' not found in '$controllerClass'");
        }

        call_user_func_array([$instance, $method], $params);
    }
}