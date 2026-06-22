<?php
namespace App\Services;

use App\Repositories\ConfigRepository;

class ConfigService
{
    private ConfigRepository $configRepo;

    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * Récupérer une configuration
     */
    public function get(string $key, $default = null)
    {
        return $this->configRepo->get($key, $default);
    }

    /**
     * Définir une configuration
     */
    public function set(string $key, string $value): void
    {
        $this->configRepo->set($key, $value);
    }

    /**
     * Récupérer toutes les configurations
     */
    public function getAll(): array
    {
        return $this->configRepo->getAll();
    }

    /**
     * Récupérer les horaires formatés
     */
    public function getHoraires(): array
    {
        return [
            'midi' => [
                'ouverture' => $this->get('heure_ouverture_midi', '12:00'),
                'fermeture' => $this->get('heure_fermeture_midi', '14:30')
            ],
            'soir' => [
                'ouverture' => $this->get('heure_ouverture_soir', '19:00'),
                'fermeture' => $this->get('heure_fermeture_soir', '22:00')
            ],
            'jours_fermeture' => json_decode($this->get('jours_fermeture', '["Dimanche"]'), true)
        ];
    }
}