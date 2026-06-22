<?php
namespace App\Services;

use App\Entities\Plat;
use App\Repositories\PlatRepository;
use App\Repositories\CategorieRepository;

class MenuService
{
    private PlatRepository $platRepo;
    private CategorieRepository $categorieRepo;

    public function __construct(PlatRepository $platRepo, CategorieRepository $categorieRepo)
    {
        $this->platRepo = $platRepo;
        $this->categorieRepo = $categorieRepo;
    }

    /**
     * Récupérer toutes les catégories actives (avec au moins un plat disponible)
     */
    public function getCategoriesActives(): array
    {
        return $this->categorieRepo->findActive();
    }

    /**
     * Récupérer tous les plats avec leurs catégories
     */
    public function getTousLesPlats(): array
    {
        return $this->platRepo->findAllWithCategories();
    }

    /**
     * Récupérer les plats d'une catégorie
     */
    public function getPlatsParCategorie(int $categorieId): array
    {
        return $this->platRepo->findByCategorie($categorieId);
    }

    /**
     * Rechercher des plats par mot-clé
     */
    public function rechercherPlats(string $keyword): array
    {
        return $this->platRepo->search($keyword);
    }

    /**
     * Récupérer les plats les plus populaires
     */
    public function getPlatsPopulaires(int $limit = 6): array
    {
        return $this->platRepo->getPopulaires($limit);
    }
    /**
     * Récupérer un plat par son ID
     */
    public function getPlatById(int $id): ?Plat
    {
        return $this->platRepo->find($id);
    }

    /**
     * Récupérer un plat par son ID
     */
    public function getPlat(int $id): ?Plat
    {
        return $this->platRepo->find($id);
    }

    /**
     * Créer un plat
     */
    public function creerPlat(array $data, ?string $imageUrl = null): int
    {
        $plat = new Plat();
        $plat->setNom($data['nom'])
            ->setDescription($data['description'] ?? '')
            ->setPrix($data['prix'])
            ->setCategorieId($data['categorie_id'])
            ->setDisponible((bool)$data['disponible']);
        
        if ($imageUrl) {
            $plat->setImageUrl($imageUrl);
        }

        return $this->platRepo->save($plat);
    }

    /**
     * Modifier un plat
     */
    public function modifierPlat(int $id, array $data, ?string $imageUrl = null): void
    {
        $plat = $this->platRepo->find($id);
        if (!$plat) {
            throw new \Exception('Plat non trouvé');
        }

        $plat->setNom($data['nom'])
            ->setDescription($data['description'] ?? '')
            ->setPrix($data['prix'])
            ->setCategorieId($data['categorie_id'])
            ->setDisponible((bool)$data['disponible']);

        if ($imageUrl) {
            // Supprimer l'ancienne image
            if ($plat->getImageUrl()) {
                $uploadService = new UploadService();
                $uploadService->deleteImage($plat->getImageUrl());
            }
            $plat->setImageUrl($imageUrl);
        }

        $this->platRepo->save($plat);
    }

    /**
     * Supprimer un plat
     */
    public function supprimerPlat(int $id): void
    {
        $plat = $this->platRepo->find($id);
        if (!$plat) {
            throw new \Exception('Plat non trouvé');
        }

        // Supprimer l'image
        if ($plat->getImageUrl()) {
            $uploadService = new UploadService();
            $uploadService->deleteImage($plat->getImageUrl());
        }

        $this->platRepo->delete($id);
    }
}