<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\Avis;

class AvisRepository extends Repository
{
    protected string $table = 'avis';
    protected string $entityClass = Avis::class;

    /**
     * Récupérer les avis approuvés (sans jointure)
     */
    public function getApprouves(int $limit = 10): array
    {
        // On sélectionne directement depuis la table avis
        $sql = "SELECT * FROM {$this->table} 
                WHERE statut = 'approuve'
                ORDER BY created_at DESC
                LIMIT :limit";
        $rows = $this->query($sql, ['limit' => $limit])->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    /**
     * Calculer la note moyenne
     */
    public function getNoteMoyenne(): array
    {
        $sql = "SELECT AVG(note) as moyenne, COUNT(*) as total 
                FROM {$this->table} WHERE statut = 'approuve'";
        $row = $this->query($sql)->fetch();
        return [
            'moyenne' => round($row['moyenne'] ?? 0, 1),
            'total' => (int)($row['total'] ?? 0)
        ];
    }

    protected function hydrate(array $row): Avis
    {
        $avis = new Avis();
        $avis->setId((int)$row['id'])
             ->setReservationId($row['reservation_id'] ?? null)
             ->setNom($row['nom'] ?? 'Anonyme')
             ->setNote((int)$row['note'])
             ->setCommentaire($row['commentaire'] ?? null)
             ->setStatut($row['statut'] ?? 'en_attente')
             ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s'));
        return $avis;
    }
}