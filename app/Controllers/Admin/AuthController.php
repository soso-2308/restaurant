<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Helpers\Validator;
use App\Helpers\Csrf;

class AuthController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        parent::__construct();
        $this->userRepo = $userRepo;
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function loginForm(): void
    {
        // Si déjà connecté, rediriger vers le dashboard
        if ($this->session->get('user_id')) {
            $this->redirect('/admin');
        }

        $this->render('admin/auth/login', [
            'title' => 'Connexion Admin - RYOHA',
            'layout' => 'admin',
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

        $user = $this->userRepo->findByUsername($username);

        if ($user === null) {
            $this->session->setFlash('Identifiants incorrects', 'error');
            $this->redirect('/admin/login');
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            $this->session->setFlash('Identifiants incorrects', 'error');
            $this->redirect('/admin/login');
        }

        // Stocker directement dans $_SESSION
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_name'] = $user->getUsername();
        $_SESSION['user_role'] = $user->getRole();

        // Forcer l'écriture de la session
        session_write_close();

        $this->session->setFlash('Bienvenue ' . $user->getUsername() . ' !', 'success');
        $this->redirect('/admin');
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        $this->session->destroy();
        $this->session->setFlash('Vous êtes déconnecté', 'success');
        $this->redirect('/admin/login');
    }
}