<?php
namespace App\Entities;

use App\Core\Model;

class Config extends Model
{
    protected ?int $id = null;
    private string $cle;
    private string $valeur;
    private string $created_at;

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getCle(): string { return $this->cle; }
    public function getValeur(): string { return $this->valeur; }
    public function getCreatedAt(): string { return $this->created_at; }

    public function setCle(string $cle): self { $this->cle = $cle; return $this; }
    public function setValeur(string $valeur): self { $this->valeur = $valeur; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
}