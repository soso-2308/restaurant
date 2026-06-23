<?php
namespace App\Controllers\Web\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\Csrf;
use App\Middlewares\GuestMiddleware;

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        (new GuestMiddleware())->handle(); // empêche l'accès si déjà connecté
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function loginForm(): void
    {
        $this->render('admin/auth/login', [
            'title' => 'Connexion Admin - RYOHA',
            'layout' => false, // Pas de layout admin, page standalone
            'csrf_token' => Csrf::generateToken()
        ]);
    }

    /**
     * Traiter la connexion
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/login');
        }

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('Erreur de sécurité', 'error');
            $this->redirect('/admin/login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->session->setFlash('Veuillez remplir tous les champs', 'error');
            $this->redirect('/admin/login');
        }

        $user = (new User())->where('username', $username)->first();

        if (!$user || !password_verify($password, $user->getPasswordHash())) {
            $this->session->setFlash('Identifiants incorrects', 'error');
            $this->redirect('/admin/login');
        }

        // Connexion
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_name'] = $user->getUsername();
        $_SESSION['user_role'] = $user->getRole();

        $this->session->setFlash('Bienvenue ' . $user->getUsername() . ' !', 'success');
        $this->redirect('/admin');
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        session_destroy();
        $this->session->setFlash('Vous êtes déconnecté', 'success');
        $this->redirect('/admin/login');
    }
}