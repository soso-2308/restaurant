<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\Reservation;
use App\Entities\Client;
use App\Entities\Creneau;

class ReservationRepository extends Repository
{
    protected string $table = 'reservations';
    protected string $entityClass = Reservation::class;

    public function findByDate(string $date): array
    {
        $sql = "SELECT r.*, c.nom as client_nom, c.telephone, c.email,
                cr.date_reservation, cr.heure_debut, cr.heure_fin
                FROM {$this->table} r
                JOIN clients c ON r.client_id = c.id
                JOIN creneaux cr ON r.creneau_id = cr.id
                WHERE cr.date_reservation = :date
                ORDER BY cr.heure_debut ASC";
        $rows = $this->query($sql, ['date' => $date])->fetchAll();
        $results = [];
        foreach ($rows as $row) {
            $client = new Client();
            $client->setId((int)$row['client_id'])
                   ->setNom($row['client_nom'])
                   ->setTelephone($row['telephone'])
                   ->setEmail($row['email'] ?? null);

            $creneau = new Creneau();
            $creneau->setId((int)$row['creneau_id'])
                    ->setDateReservation($row['date_reservation'])
                    ->setHeureDebut($row['heure_debut'])
                    ->setHeureFin($row['heure_fin']);

            $reservation = $this->hydrate($row);
            // On ne peut pas directement setter les objets, on garde les IDs
            $reservation->setClientId((int)$row['client_id'])
                       ->setCreneauId((int)$row['creneau_id']);
            $results[] = $reservation;
        }
        return $results;
    }

    public function findByClient(int $clientId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE client_id = :client_id ORDER BY created_at DESC";
        $rows = $this->query($sql, ['client_id' => $clientId])->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    protected function hydrate(array $row): Reservation
    {
        $reservation = new Reservation();
        $reservation->setId((int)$row['id'])
                    ->setClientId((int)$row['client_id'])
                    ->setCreneauId((int)$row['creneau_id'])
                    ->setNombrePersonnes((int)$row['nombre_personnes'])
                    ->setStatut($row['statut'])
                    ->setCommentaire($row['commentaire'] ?? null)
                    ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s'));
        return $reservation;
    }

    public function findByDateRange(string $dateDebut, string $dateFin): array
    {
        $sql = "SELECT r.*, c.nom as client_nom, c.telephone, c.email,
                cr.date_reservation, cr.heure_debut, cr.heure_fin
                FROM {$this->table} r
                JOIN clients c ON r.client_id = c.id
                JOIN creneaux cr ON r.creneau_id = cr.id
                WHERE cr.date_reservation BETWEEN :debut AND :fin
                AND r.statut = 'confirmee'
                ORDER BY cr.date_reservation ASC, cr.heure_debut ASC";
        $rows = $this->query($sql, ['debut' => $dateDebut, 'fin' => $dateFin])->fetchAll();
        $results = [];
        foreach ($rows as $row) {
            $reservation = $this->hydrate($row);
            $results[] = $reservation;
        }
        return $results;
    }
    // app/Repositories/ReservationRepository.php
    public function updateStatut(int $id, string $statut): void
    {
        $sql = "UPDATE {$this->table} SET statut = :statut WHERE id = :id";
        $this->query($sql, ['statut' => $statut, 'id' => $id]);
    }
}