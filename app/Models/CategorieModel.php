<?php
namespace App\Models;

use App\Core\Model;
use App\Models\Plat;

class Categorie extends Model
{
    protected string $table = 'categories';
    protected string $primaryKey = 'id';
    protected bool $usesSoftDelete = false; // Pas de soft delete pour les catégories

    // Colonnes autorisées pour l'assignation massive (facultatif)
    protected array $fillable = ['nom', 'description', 'ordre'];

    // Propriétés de l'entité
    protected ?int $id = null;
    private string $nom;
    private ?string $description = null;
    private int $ordre = 0;
    private string $created_at;

    // --- Getters / Setters ---
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getOrdre(): int { return $this->ordre; }
    public function setOrdre(int $ordre): self { $this->ordre = $ordre; return $this; }

    public function getCreatedAt(): string { return $this->created_at; }

    /**
     * Relation : Récupère tous les plats de cette catégorie (disponibles uniquement)
     * @return Plat[] (tableau d'objets Plat)
     */
    public function plats(): array
    {
        return $this->hasMany(Plat::class, 'categorie_id')
                    ->where('disponible', 1)
                    ->orderBy('nom', 'ASC')
                    ->get();
    }

    /**
     * Relation : Récupère le nombre de plats disponibles
     */
    public function platsCount(): int
    {
        return $this->hasMany(Plat::class, 'categorie_id')
                    ->where('disponible', 1)
                    ->count();
    }

    /**
     * Scope : Catégories actives (qui ont au moins un plat disponible)
     */
    public static function findActive(): array
    {
        $instance = new static();
        $sql = "SELECT DISTINCT c.* 
                FROM categories c
                INNER JOIN plats p ON c.id = p.categorie_id AND p.disponible = 1
                ORDER BY c.ordre ASC";
        $stmt = $instance->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => (new static())->hydrate($row), $rows);
    }

    /**
     * Scope : Catégories avec le nombre de plats associés
     */
    public static function findWithPlatsCount(): array
    {
        $instance = new static();
        $sql = "SELECT c.*, COUNT(p.id) as total_plats 
                FROM categories c
                LEFT JOIN plats p ON c.id = p.categorie_id AND p.disponible = 1
                GROUP BY c.id
                ORDER BY c.ordre ASC";
        $stmt = $instance->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return $rows; // Retourne les données brutes car "total_plats" n'est pas une propriété de l'entité
    }

    /**
     * Récupère une catégorie avec ses plats en une seule requête (exemple d'optimisation)
     */
    public static function findWithPlats(int $id): ?array
    {
        $categorie = (new static())->find($id);
        if (!$categorie) return null;
        return [
            'categorie' => $categorie,
            'plats' => $categorie->plats()
        ];
    }

    /**
     * Vérifie si une catégorie peut être supprimée (pas de plats associés)
     */
    public function isDeletable(): bool
    {
        return $this->platsCount() === 0;
    }

    /**
     * Supprime la catégorie uniquement si elle n'a pas de plats
     */
    public function safeDelete(): bool
    {
        if (!$this->isDeletable()) {
            throw new \Exception("Impossible de supprimer la catégorie '{$this->nom}' car elle contient des plats.");
        }
        return $this->delete();
    }
}