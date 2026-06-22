<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\User;

class UserRepository extends Repository
{
    protected string $table = 'users';
    protected string $entityClass = User::class;

    public function findByUsername(string $username): ?User
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        $row = $this->query($sql, ['username' => $username])->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $row = $this->query($sql, ['email' => $email])->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    protected function hydrate(array $row): User
    {
        $user = new User();
        $user->setId((int)$row['id'])
             ->setUsername($row['username'])
             ->setPasswordHash($row['password_hash'])
             ->setEmail($row['email'])
             ->setRole($row['role'])
             ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s')); // ← string directe
        return $user;
    }
}