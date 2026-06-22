<?php
namespace App\Entities;

use App\Core\Model;

class Creneau extends Model
{
    protected ?int $id = null;
    private string $date_reservation;
    private string $heure_debut;
    private string $heure_fin;
    private int $capacite_max = 50;
    private int $couverts_disponibles = 50;
    private int $est_ferme = 0;   // booléen en int (0/1)
    private string $created_at;

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getDateReservation(): string { return $this->date_reservation; }
    public function getHeureDebut(): string { return $this->heure_debut; }
    public function getHeureFin(): string { return $this->heure_fin; }
    public function getCapaciteMax(): int { return $this->capacite_max; }
    public function getCouvertsDisponibles(): int { return $this->couverts_disponibles; }
    public function isEstFerme(): bool { return (bool)$this->est_ferme; }
    public function getCreatedAt(): string { return $this->created_at; }

    public function setDateReservation(string $date_reservation): self { $this->date_reservation = $date_reservation; return $this; }
    public function setHeureDebut(string $heure_debut): self { $this->heure_debut = $heure_debut; return $this; }
    public function setHeureFin(string $heure_fin): self { $this->heure_fin = $heure_fin; return $this; }
    public function setCapaciteMax(int $capacite_max): self { $this->capacite_max = $capacite_max; return $this; }
    public function setCouvertsDisponibles(int $couverts_disponibles): self { $this->couverts_disponibles = $couverts_disponibles; return $this; }
    public function setEstFerme(bool $est_ferme): self { $this->est_ferme = (int)$est_ferme; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
}