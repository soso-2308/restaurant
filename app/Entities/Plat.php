<?php
namespace App\Entities;

use App\Core\Model;

class Plat extends Model
{
    protected ?int $id = null;
    private string $nom;
    private ?string $description = null;
    private float $prix;
    private ?int $categorie_id = null;
    private ?string $image_url = null;
    private int $disponible = 1;
    private int $popularite = 0;
    private string $created_at;
    private string $updated_at;

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function getNom(): string { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }
    public function getPrix(): float { return $this->prix; }
    public function getCategorieId(): ?int { return $this->categorie_id; }
    public function getImageUrl(): ?string { return $this->image_url; }
    public function isDisponible(): bool { return (bool)$this->disponible; }
    public function getPopularite(): int { return $this->popularite; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): string { return $this->updated_at; }

    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }
    public function setCategorieId(?int $categorie_id): self { $this->categorie_id = $categorie_id; return $this; }
    public function setImageUrl(?string $image_url): self { $this->image_url = $image_url; return $this; }
    public function setDisponible(bool $disponible): self { $this->disponible = (int)$disponible; return $this; }
    public function setPopularite(int $popularite): self { $this->popularite = $popularite; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
    public function setUpdatedAt(string $updated_at): self { $this->updated_at = $updated_at; return $this; }
}