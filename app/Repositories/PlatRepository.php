<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\Plat;

class PlatRepository extends Repository
{
    protected string $table = 'plats';
    protected string $entityClass = Plat::class;

    public function findAllWithCategories(): array
    {
        $sql = "SELECT p.*, c.nom as categorie_nom 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.categorie_id = c.id
                ORDER BY c.ordre ASC, p.nom ASC";
        $rows = $this->query($sql)->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findByCategorie(int $categorieId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE categorie_id = :categorie_id AND disponible = 1 ORDER BY nom ASC";
        $rows = $this->query($sql, ['categorie_id' => $categorieId])->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function search(string $keyword): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nom LIKE :keyword OR description LIKE :keyword
                AND disponible = 1
                ORDER BY nom ASC";
        $rows = $this->query($sql, ['keyword' => "%$keyword%"])->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function getPopulaires(int $limit = 6): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE disponible = 1 ORDER BY popularite DESC, nom ASC LIMIT :limit";
        $rows = $this->query($sql, ['limit' => $limit])->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    protected function hydrate(array $row): Plat
    {
        $plat = new Plat();
        $plat->setId((int)$row['id'])
             ->setNom($row['nom'])
             ->setDescription($row['description'] ?? null)
             ->setPrix((float)$row['prix'])
             ->setCategorieId($row['categorie_id'] ? (int)$row['categorie_id'] : null)
             ->setImageUrl($row['image_url'] ?? null)
             ->setDisponible((bool)$row['disponible'])
             ->setPopularite((int)$row['popularite'])
             ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s'))
             ->setUpdatedAt($row['updated_at'] ?? date('Y-m-d H:i:s'));
        return $plat;
    }
}