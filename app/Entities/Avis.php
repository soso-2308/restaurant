<?php
namespace App\Entities;

use App\Core\Model;

class Avis extends Model
{
    protected ?int $id = null;
    private ?int $reservation_id = null;
    private string $nom;
    private int $note;
    private ?string $commentaire = null;
    private string $statut = 'en_attente';
    private string $created_at;

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    // Getters
    public function getReservationId(): ?int { return $this->reservation_id; }
    public function getNom(): string { return $this->nom; }
    public function getNote(): int { return $this->note; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function getStatut(): string { return $this->statut; }
    public function getCreatedAt(): string { return $this->created_at; }

    // Setters
    public function setReservationId(?int $reservation_id): self { $this->reservation_id = $reservation_id; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setNote(int $note): self { $this->note = $note; return $this; }
    public function setCommentaire(?string $commentaire): self { $this->commentaire = $commentaire; return $this; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
}