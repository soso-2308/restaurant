<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\Categorie;

class CategorieRepository extends Repository
{
    protected string $table = 'categories';
    protected string $entityClass = Categorie::class;

    // Supprimez toute méthode findAll() surchargée
    // Utilisez celle du parent (Core\Repository)

    public function findAllWithCount(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as total_plats 
                FROM {$this->table} c
                LEFT JOIN plats p ON c.id = p.categorie_id AND p.disponible = 1
                GROUP BY c.id
                ORDER BY c.ordre ASC";
        $rows = $this->query($sql)->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findActive(): array
    {
        $sql = "SELECT DISTINCT c.* 
                FROM {$this->table} c
                INNER JOIN plats p ON c.id = p.categorie_id AND p.disponible = 1
                ORDER BY c.ordre ASC";
        $rows = $this->query($sql)->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    protected function hydrate(array $row): Categorie
    {
        $categorie = new Categorie();
        $categorie->setId((int)$row['id'])
                  ->setNom($row['nom'])
                  ->setDescription($row['description'] ?? null)
                  ->setOrdre((int)$row['ordre'])
                  ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s'));
        return $categorie;
    }
}