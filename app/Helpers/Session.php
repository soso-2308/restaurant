<?php
namespace App\Helpers;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Définir une valeur en session
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Récupérer une valeur de session
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifier si une clé existe en session
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Supprimer une clé de la session
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Détruire toute la session
     */
    public function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Définir un message flash
     */
    public function setFlash(string $message, string $type = 'success'): void
    {
        $this->set('flash_message', $message);
        $this->set('flash_type', $type);
    }

    /**
     * Récupérer et effacer le message flash
     */
    public function getFlash(): ?array
    {
        if (!$this->has('flash_message')) {
            return null;
        }
        $flash = [
            'message' => $this->get('flash_message'),
            'type' => $this->get('flash_type', 'success')
        ];
        $this->remove('flash_message');
        $this->remove('flash_type');
        return $flash;
    }
}