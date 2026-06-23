<?php
namespace App\Core;

use App\Core\Database;

abstract class Model
{
    // --- Propriétés de base ---
    protected \PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected ?int $id = null;
    protected array $fillable = [];
    protected array $hidden = [];

    // --- Propriétés Query Builder (pour le chaînage) ---
    private array $queryWhere = [];
    private array $queryBindings = [];
    private array $queryOrderBy = [];
    private ?int $queryLimit = null;
    private ?int $queryOffset = null;
    private array $querySelect = ['*'];

    // --- Propriétés Soft Delete ---
    protected bool $usesSoftDelete = false;
    protected string $deletedAtColumn = 'deleted_at';

    // --- Constructeur ---
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ========================
    // CŒUR DU QUERY BUILDER
    // ========================

    /**
     * Sélectionner des colonnes spécifiques
     */
    public function select(array $columns): self
    {
        $this->querySelect = $columns;
        return $this;
    }

    /**
     * Ajouter une condition WHERE (ex: where('prix', '>', 1000))
     */
    public function where(string $column, string $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $key = str_replace('.', '_', $column) . '_' . count($this->queryWhere);
        $this->queryWhere[] = "$column $operator :$key";
        $this->queryBindings[$key] = $value;
        return $this;
    }

    /**
     * Ajouter un OR WHERE
     */
    public function orWhere(string $column, string $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $key = 'or_' . str_replace('.', '_', $column) . '_' . count($this->queryWhere);
        $this->queryWhere[] = "OR $column $operator :$key";
        $this->queryBindings[$key] = $value;
        return $this;
    }

    /**
     * Ajouter une condition WHERE IN
     */
    public function whereIn(string $column, array $values): self
    {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $key = $column . '_in_' . $i;
            $placeholders[] = ':' . $key;
            $this->queryBindings[$key] = $value;
        }
        $this->queryWhere[] = "$column IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }

    /**
     * Ajouter un ORDER BY
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->queryOrderBy[] = "$column $direction";
        return $this;
    }

    /**
     * Ajouter une limite
     */
    public function limit(int $limit): self
    {
        $this->queryLimit = $limit;
        return $this;
    }

    /**
     * Ajouter un OFFSET
     */
    public function offset(int $offset): self
    {
        $this->queryOffset = $offset;
        return $this;
    }

    /**
     * Récupérer les résultats (retourne un tableau d'objets)
     */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->queryBindings);
        $rows = $stmt->fetchAll();
        $this->resetQueryBuilder();
        return array_map(fn($row) => (new static())->hydrate($row), $rows);
    }

    /**
     * Récupérer le premier résultat
     */
    public function first(): ?self
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Compte le nombre de résultats
     */
    public function count(): int
    {
        $sql = $this->buildSelectQuery(true);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->queryBindings);
        $this->resetQueryBuilder();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Vérifie si un enregistrement existe
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Pagination simple
     */
    public function paginate(int $perPage = 10, int $page = 1): array
    {
        $total = $this->count();
        $lastPage = ceil($total / $perPage);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        $data = (clone $this)->limit($perPage)->offset($offset)->get();

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ];
    }

    // ========================
    // RELATIONS (HAS MANY / BELONGS TO)
    // ========================

    /**
     * Définit une relation hasMany
     * Exemple : $categorie->hasMany(Plat::class, 'categorie_id')->get();
     */
    public function hasMany(string $relatedModel, string $foreignKey): self
    {
        /** @var Model $instance */
        $instance = new $relatedModel();
        return $instance->where($foreignKey, $this->getId());
    }

    /**
     * Définit une relation belongsTo
     * Exemple : $plat->belongsTo(Categorie::class, 'categorie_id')->first();
     */
    public function belongsTo(string $relatedModel, string $foreignKey): ?self
    {
        /** @var Model $instance */
        $instance = new $relatedModel();
        return $instance->where($instance->getPrimaryKey(), $this->$foreignKey)->first();
    }

    // ========================
    // SOFT DELETE
    // ========================

    public function delete(): bool
    {
        if ($this->usesSoftDelete) {
            $this->{$this->deletedAtColumn} = date('Y-m-d H:i:s');
            return $this->save();
        }
        if (!$this->id) return false;
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $this->id]);
    }

    public function forceDelete(): bool
    {
        if (!$this->id) return false;
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $this->id]);
    }

    public function withTrashed(): self
    {
        // Si soft delete est actif, on ignore le filtre deleted_at
        return $this;
    }

    // ========================
    // MÉTHODES EXISTANTES (find, save, hydrate, etc.)
    // ========================

    // ... (gardez vos méthodes find, findAll, save, hydrate, toArray, etc. telles quelles,
    // mais modifiez findAll pour utiliser le Query Builder si possible)

    public function find(int $id): ?self
    {
        return (clone $this)->where($this->primaryKey, $id)->first();
    }

    public function findAll(array $conditions = [], ?string $orderBy = null): array
    {
        $instance = clone $this;
        foreach ($conditions as $key => $value) {
            $instance->where($key, $value);
        }
        if ($orderBy) {
            $instance->orderBy($orderBy);
        }
        return $instance->get();
    }

    public function save(): bool
    {
        $data = $this->toArray();
        unset($data['db'], $data['table'], $data['primaryKey'], $data['fillable'], $data['hidden'],
              $data['queryWhere'], $data['queryBindings'], $data['queryOrderBy'],
              $data['queryLimit'], $data['queryOffset'], $data['querySelect'],
              $data['usesSoftDelete'], $data['deletedAtColumn']);

        if ($this->id) {
            unset($data[$this->primaryKey]);
            $sets = [];
            foreach ($data as $key => $value) {
                $sets[] = "$key = :$key";
            }
            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = :id";
            $data['id'] = $this->id;
        } else {
            $keys = array_keys($data);
            $columns = implode(', ', $keys);
            $placeholders = ':' . implode(', :', $keys);
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        }

        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($data);
        if (!$this->id && $success) {
            $this->id = (int)$this->db->lastInsertId();
        }
        return $success;
    }

    // ========================
    // MÉTHODES UTILITAIRES
    // ========================

    protected function buildSelectQuery(bool $countOnly = false): string
    {
        $columns = $countOnly ? 'COUNT(*)' : implode(', ', $this->querySelect);
        $sql = "SELECT $columns FROM {$this->table}";

        // WHERE
        if (!empty($this->queryWhere)) {
            $sql .= " WHERE " . implode(' ', $this->queryWhere);
        }

        // ORDER BY
        if (!$countOnly && !empty($this->queryOrderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->queryOrderBy);
        }

        // LIMIT & OFFSET
        if (!$countOnly && $this->queryLimit !== null) {
            $sql .= " LIMIT " . (int)$this->queryLimit;
            if ($this->queryOffset !== null) {
                $sql .= " OFFSET " . (int)$this->queryOffset;
            }
        }

        return $sql;
    }

    protected function resetQueryBuilder(): void
    {
        $this->queryWhere = [];
        $this->queryBindings = [];
        $this->queryOrderBy = [];
        $this->queryLimit = null;
        $this->queryOffset = null;
        $this->querySelect = ['*'];
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    // Getters / Setters communs
    public function getId(): ?int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }


    /**
 * Applique un tableau de filtres dynamiques (égalité, date range, LIKE)
 * Exemple: $model->filter(['statut' => 'confirmee', 'date' => ['from' => '2026-06-01', 'to' => '2026-06-30']])
 */
public function filter(array $filters): self
{
    $clone = clone $this;
    foreach ($filters as $column => $value) {
        // Ignorer les filtres vides
        if ($value === null || $value === '' || (is_array($value) && empty(array_filter($value)))) {
            continue;
        }

        // Filtre "BETWEEN" (ex: date range)
        if (is_array($value) && isset($value['from']) && isset($value['to'])) {
            $clone->where($column, '>=', $value['from'])
                  ->where($column, '<=', $value['to']);
        }
        // Filtre "LIKE" (recherche partielle)
        elseif (is_array($value) && isset($value['like'])) {
            $clone->where($column, 'LIKE', '%' . $value['like'] . '%');
        }
        // Filtre "IN" (ex: plusieurs statuts)
        elseif (is_array($value) && isset($value['in'])) {
            $clone->whereIn($column, $value['in']);
        }
        // Filtre simple (égalité)
        else {
            $clone->where($column, $value);
        }
    }
    return $clone;
}

/**
 * Recherche avancée sur plusieurs colonnes (LIKE sur chaque)
 * Exemple: $model->search(['nom', 'description', 'adresse'], 'Poulet')
 */
public function search(array $columns, string $keyword): self
{
    $clone = clone $this;
    if (empty($keyword)) return $clone;

    $conditions = [];
    foreach ($columns as $col) {
        $conditions[] = "$col LIKE :search";
    }
    $clone->queryWhere[] = '(' . implode(' OR ', $conditions) . ')';
    $clone->queryBindings['search'] = '%' . $keyword . '%';
    return $clone;
}
}