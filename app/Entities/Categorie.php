<?php
namespace App\Entities;

use App\Core\Model;

class Categorie extends Model
{
    protected ?int $id = null;
    private string $nom;
    private ?string $description = null;
    private int $ordre = 0;
    private string $created_at;

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getNom(): string { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }
    public function getOrdre(): int { return $this->ordre; }
    public function getCreatedAt(): string { return $this->created_at; }

    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function setOrdre(int $ordre): self { $this->ordre = $ordre; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
}