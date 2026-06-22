<?php
namespace App\Entities;

use App\Core\Model;

class Client extends Model
{
    protected ?int $id = null;
    private string $nom;
    private string $telephone;
    private ?string $email = null;
    private ?string $message = null;
    private string $created_at;   // ← maintenant en string ou datetime, on utilisera string

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getNom(): string { return $this->nom; }
    public function getTelephone(): string { return $this->telephone; }
    public function getEmail(): ?string { return $this->email; }
    public function getMessage(): ?string { return $this->message; }
    public function getCreatedAt(): string { return $this->created_at; }

    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }
    public function setMessage(?string $message): self { $this->message = $message; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
}