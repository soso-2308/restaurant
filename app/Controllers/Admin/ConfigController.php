<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ConfigService;
use App\Middlewares\AuthMiddleware;
use App\Helpers\Csrf;

class ConfigController extends Controller
{
    private ConfigService $configService;

    public function __construct(ConfigService $configService)
    {
        parent::__construct();
        
        $auth = new AuthMiddleware();
        $auth->handle();
        
        $this->configService = $configService;
    }

    /**
     * Afficher la page de configuration
     */
    public function index(): void
    {
        $configs = $this->configService->getAll();
        $horaires = $this->configService->getHoraires();
        
        // Transformer en tableau associatif pour la vue
        $configsAssoc = [];
        foreach ($configs as $config) {
            $configsAssoc[$config->getCle()] = $config->getValeur();
        }

        $this->render('admin/config/index', [
            'title' => 'Configuration - RYOHA',
            'layout' => 'admin',
            'active_page' => 'config',
            'configs' => $configsAssoc,
            'horaires' => $horaires,
            'csrf_token' => Csrf::generateToken()
        ]);
    }

    /**
     * Mettre à jour la configuration
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/config');
        }

        // Vérifier CSRF
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('Erreur de sécurité', 'error');
            $this->redirect('/admin/config');
        }

        $data = $_POST;
        unset($data['csrf_token']);

        // Traiter les jours de fermeture
        if (isset($data['jours_fermeture']) && is_array($data['jours_fermeture'])) {
            $data['jours_fermeture'] = json_encode(array_values($data['jours_fermeture']));
        } else {
            $data['jours_fermeture'] = json_encode([]);
        }

        $errors = [];
        $success = 0;

        foreach ($data as $cle => $valeur) {
            try {
                $this->configService->set($cle, $valeur);
                $success++;
            } catch (\Exception $e) {
                $errors[] = "Erreur pour $cle : " . $e->getMessage();
            }
        }

        if ($success > 0) {
            $this->session->setFlash('Configuration mise à jour avec succès !', 'success');
        } else {
            $this->session->setFlash('Erreurs : ' . implode(', ', $errors), 'error');
        }

        $this->redirect('/admin/config');
    }
}