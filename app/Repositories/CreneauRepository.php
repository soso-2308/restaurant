<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\Creneau;

class CreneauRepository extends Repository
{
    protected string $table = 'creneaux';
    protected string $entityClass = Creneau::class;

    public function findDisponibles(string $date, int $minimumCouverts = 1): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE date_reservation = :date 
                AND est_ferme = 0 
                AND couverts_disponibles >= :min
                ORDER BY heure_debut ASC";
        $rows = $this->query($sql, ['date' => $date, 'min' => $minimumCouverts])->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function decrementerCouverts(int $creneauId, int $nbPersonnes): void
    {
        $sql = "UPDATE {$this->table} 
                SET couverts_disponibles = couverts_disponibles - :nb 
                WHERE id = :id AND couverts_disponibles >= :nb";
        $this->query($sql, ['nb' => $nbPersonnes, 'id' => $creneauId]);
    }

    public function incrementerCouverts(int $creneauId, int $nbPersonnes): void
    {
        $sql = "UPDATE {$this->table} 
                SET couverts_disponibles = couverts_disponibles + :nb 
                WHERE id = :id";
        $this->query($sql, ['nb' => $nbPersonnes, 'id' => $creneauId]);
    }

    protected function hydrate(array $row): Creneau
    {
        $creneau = new Creneau();
        $creneau->setId((int)$row['id'])
                ->setDateReservation($row['date_reservation'])
                ->setHeureDebut($row['heure_debut'])
                ->setHeureFin($row['heure_fin'])
                ->setCapaciteMax((int)$row['capacite_max'])
                ->setCouvertsDisponibles((int)$row['couverts_disponibles'])
                ->setEstFerme((bool)$row['est_ferme'])
                ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s'));
        return $creneau;
    }
}