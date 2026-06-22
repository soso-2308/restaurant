<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\Client;

class ClientRepository extends Repository
{
    protected string $table = 'clients';
    protected string $entityClass = Client::class;

    public function findByTelephone(string $telephone): ?Client
    {
        $sql = "SELECT * FROM {$this->table} WHERE telephone = :telephone";
        $row = $this->query($sql, ['telephone' => $telephone])->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    protected function hydrate(array $row): Client
    {
        $client = new Client();
        $client->setId((int)$row['id'])
               ->setNom($row['nom'])
               ->setTelephone($row['telephone'])
               ->setEmail($row['email'] ?? null)
               ->setMessage($row['message'] ?? null)
               ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s'));
        return $client;
    }
}