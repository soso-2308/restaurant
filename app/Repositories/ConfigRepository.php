<?php
namespace App\Repositories;

use App\Core\Repository;
use App\Entities\Config;

class ConfigRepository extends Repository
{
    protected string $table = 'config';
    protected string $entityClass = Config::class;

    public function get(string $key, $default = null)
    {
        $sql = "SELECT valeur FROM {$this->table} WHERE cle = :cle";
        $row = $this->query($sql, ['cle' => $key])->fetch();
        return $row ? $row['valeur'] : $default;
    }

    public function set(string $key, string $value): void
    {
        $exists = $this->get($key, null);
        if ($exists !== null) {
            $sql = "UPDATE {$this->table} SET valeur = :valeur WHERE cle = :cle";
            $this->query($sql, ['cle' => $key, 'valeur' => $value]);
        } else {
            $sql = "INSERT INTO {$this->table} (cle, valeur) VALUES (:cle, :valeur)";
            $this->query($sql, ['cle' => $key, 'valeur' => $value]);
        }
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY cle ASC";
        $rows = $this->query($sql)->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    protected function hydrate(array $row): Config
    {
        $config = new Config();
        $config->setId((int)$row['id'])
               ->setCle($row['cle'])
               ->setValeur($row['valeur'])
               ->setCreatedAt($row['created_at'] ?? date('Y-m-d H:i:s'));
        return $config;
    }
}