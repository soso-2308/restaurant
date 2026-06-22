<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;

// Charger le .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();
    echo "✅ Connexion à la base de données réussie !<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "👤 Nombre d'utilisateurs : " . $count;
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}