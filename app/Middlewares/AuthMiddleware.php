<?php
namespace App\Middlewares;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header('Location: /restaurant-ryoha/admin/login');
            exit;
        }
    }

    public function requireRole(string $role): void
    {
        if (($_SESSION['user_role'] ?? '') !== $role) {
            http_response_code(403);
            die('Accès non autorisé');
        }
    }
}