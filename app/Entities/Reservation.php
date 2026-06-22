<?php
namespace App\Entities;

use App\Core\Model;

class Reservation extends Model
{
    protected ?int $id = null;
    private int $client_id;
    private int $creneau_id;
    private int $nombre_personnes;
    private string $statut = 'confirmee';
    private ?string $commentaire = null;
    private string $created_at;

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getClientId(): int { return $this->client_id; }
    public function getCreneauId(): int { return $this->creneau_id; }
    public function getNombrePersonnes(): int { return $this->nombre_personnes; }
    public function getStatut(): string { return $this->statut; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function getCreatedAt(): string { return $this->created_at; }

    public function setClientId(int $client_id): self { $this->client_id = $client_id; return $this; }
    public function setCreneauId(int $creneau_id): self { $this->creneau_id = $creneau_id; return $this; }
    public function setNombrePersonnes(int $nombre_personnes): self { $this->nombre_personnes = $nombre_personnes; return $this; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function setCommentaire(?string $commentaire): self { $this->commentaire = $commentaire; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
}