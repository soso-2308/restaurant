<?php
namespace App\Core;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\Csrf;

abstract class Controller
{
    protected Session $session;
    protected Validator $validator;

    public function __construct()
    {
        $this->session = new Session();
        $this->validator = new Validator();
    }

    /**
     * Rendre une vue avec layout
     */
    protected function render(string $view, array $data = [], $layout = 'main'): void
    {
        extract($data);
        ob_start();
        $viewPath = __DIR__ . "/../Views/$view.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View '$view' not found");
        }
        $content = ob_get_clean();

        if ($layout === false) {
            echo $content;
            return;
        }

        $layoutPath = __DIR__ . "/../Views/layouts/$layout.php";
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }

    // Les autres méthodes restent identiques
    protected function redirect(string $url): void
    {
        $basePath = '/restaurant-ryoha'; // ou utilisez une constante
        header("Location: $basePath$url");
        exit;
    }
    protected function json(array $data, int $statusCode = 200): void { /* ... */ }
    protected function isAjax(): bool { /* ... */ }
}