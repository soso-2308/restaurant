<?php
namespace App\Models;

use App\Core\Model;

class ClientModel extends Model
{
    protected string $table = 'clients';
    protected string $primaryKey = 'id';

    protected ?int $id = null;
    private string $nom;
    private string $telephone;
    private ?string $email = null;
    private ?string $message = null;
    private string $created_at;

    // Getters et setters
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $message): self { $this->message = $message; return $this; }

    public function getCreatedAt(): string { return $this->created_at; }

    /**
     * Relations : Récupère les réservations de ce client
     * @return array|Reservation[]
     */
    public function reservations(): array
    {
        return $this->hasMany(Reservation::class, 'client_id')->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Compte les réservations
     */
    public function reservationsCount(): int
    {
        return $this->hasMany(Reservation::class, 'client_id')->count();
    }

    /**
     * Recherche avancée pour l'admin
     */
    public static function adminList(array $filters = [], ?string $search = null)
    {
        $query = new static();
        if ($search) {
            $query->search(['nom', 'telephone', 'email'], $search);
        }
        // Filtre par date de création (plage)
        if (!empty($filters['date_debut']) && !empty($filters['date_fin'])) {
            $query->where('created_at', '>=', $filters['date_debut'] . ' 00:00:00')
                  ->where('created_at', '<=', $filters['date_fin'] . ' 23:59:59');
        }
        // Filtre par nombre minimum de réservations (ex: clients avec au moins 2 réservations)
        if (isset($filters['min_reservations'])) {
            $query->queryWhere[] = "(
                SELECT COUNT(*) FROM reservations WHERE reservations.client_id = clients.id
            ) >= :min_res";
            $query->queryBindings['min_res'] = (int)$filters['min_reservations'];
        }
        return $query->orderBy('created_at', 'DESC');
    }

    /**
     * Export des données formatées pour Excel/PDF
     */
    public static function exportData(array $filters = [], ?string $search = null): array
    {
        $clients = self::adminList($filters, $search)->get();
        $data = [];
        foreach ($clients as $client) {
            $data[] = [
                'ID' => $client->getId(),
                'Nom' => $client->getNom(),
                'Téléphone' => $client->getTelephone(),
                'Email' => $client->getEmail() ?? '',
                'Réservations' => $client->reservationsCount(),
                'Inscrit le' => date('d/m/Y H:i', strtotime($client->getCreatedAt()))
            ];
        }
        return $data;
    }
}