<?php
namespace App\Helpers;

class Csrf
{
    private static Session $session;

    /**
     * Initialiser la session
     */
    private static function init(): void
    {
        if (!isset(self::$session)) {
            self::$session = new Session();
        }
    }

    /**
     * Générer un token CSRF
     */
    public static function generateToken(): string
    {
        self::init();
        
        if (!self::$session->has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            self::$session->set('csrf_token', $token);
        }
        
        return self::$session->get('csrf_token');
    }

    /**
     * Vérifier un token CSRF
     */
    public static function verifyToken(?string $token): bool
    {
        self::init();
        
        if (empty($token) || !self::$session->has('csrf_token')) {
            return false;
        }
        
        return hash_equals(self::$session->get('csrf_token'), $token);
    }

    /**
     * Générer un champ input caché pour un formulaire
     */
    public static function input(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Régénérer le token (après validation)
     */
    public static function regenerate(): void
    {
        self::init();
        $token = bin2hex(random_bytes(32));
        self::$session->set('csrf_token', $token);
    }
}