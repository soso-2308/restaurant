<?php
namespace App\Controllers\Visitor;

use App\Core\Controller;
use App\Helpers\Csrf;
use App\Helpers\Security;

class TestController extends Controller
{
    public function index(): void
    {
        $token = Csrf::generateToken();
        
        echo "<h1>Test des Helpers</h1>";
        echo "<p><strong>Token CSRF :</strong> $token</p>";
        echo "<p><strong>Mot de passe hashé :</strong> " . Security::hashPassword('test123') . "</p>";
        echo "<p><strong>Chaîne aléatoire :</strong> " . Security::randomString(16) . "</p>";
        echo "<p><a href='/restaurant-ryoha/'>Retour à l'accueil</a></p>";
    }
}