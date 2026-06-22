<?php
namespace App\Helpers;

class Security
{
    /**
     * Hasher un mot de passe
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Vérifier un mot de passe
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Générer une chaîne aléatoire
     */
    public static function randomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Nettoyer l'URL d'éventuels caractères dangereux
     */
    public static function sanitizeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Valider une adresse IP
     */
    public static function validateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Protection contre les injections SQL (déjà gérée par PDO)
     * Mais cette méthode est utile pour les valeurs utilisées dans des requêtes
     * non préparées (à éviter).
     */
    public static function escape($value, \PDO $db)
    {
        if (is_string($value)) {
            return substr($db->quote($value), 1, -1);
        }
        return $value;
    }
}