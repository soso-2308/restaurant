<?php
namespace App\Core;

use App\Config\Database;

abstract class Repository
{
    protected \PDO $db;
    protected string $table;
    protected string $entityClass;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function find(int $id): ?object
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $row = $this->query($sql, ['id' => $id])->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(array $conditions = [], string $orderBy = null): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        $rows = $this->query($sql, $conditions)->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function save(object $entity): int
    {
        $data = $entity->toArray();
        unset($data['id']);

        $id = $entity->getId();

        if ($id) {
            $sets = [];
            foreach ($data as $key => $value) {
                $sets[] = "$key = :$key";
            }
            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id";
            $data['id'] = $id;
            $this->query($sql, $data);
            return $id;
        } else {
            $keys = array_keys($data);
            $columns = implode(', ', $keys);
            $placeholders = ':' . implode(', :', $keys);
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            $this->query($sql, $data);
            return (int)$this->db->lastInsertId();
        }
    }

    public function delete(int $id): void
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $this->query($sql, ['id' => $id]);
    }

    abstract protected function hydrate(array $row): object;
}