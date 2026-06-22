<?php
namespace App\Entities;

use App\Core\Model;

class User extends Model
{
    protected ?int $id = null;
    private string $username;
    private string $password_hash;
    private string $email;
    private string $role = 'admin';
    private string $created_at;

    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getUsername(): string { return $this->username; }
    public function getPasswordHash(): string { return $this->password_hash; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getCreatedAt(): string { return $this->created_at; }

    public function setUsername(string $username): self { $this->username = $username; return $this; }
    public function setPasswordHash(string $password_hash): self { $this->password_hash = $password_hash; return $this; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setRole(string $role): self { $this->role = $role; return $this; }
    public function setCreatedAt(string $created_at): self { $this->created_at = $created_at; return $this; }
}