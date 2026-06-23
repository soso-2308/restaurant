<?php
namespace App\Core;

use App\Helpers\Session;

abstract class Controller
{
    protected Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Rendre une vue avec layout
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        ob_start();
        $viewPath = __DIR__ . "/../Views/$view.php";
        if (!file_exists($viewPath)) {
            throw new \Exception("View '$view' not found");
        }
        include $viewPath;
        $content = ob_get_clean();

        $layoutPath = __DIR__ . "/../Views/layouts/$layout.php";
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Redirection
     */
    protected function redirect(string $url): void
    {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    /**
     * Réponse JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Vérifier si la requête est AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}