<?php
namespace App\Models;

use App\Core\Model;

class Reservation extends Model
{
    protected string $table = 'reservations';
    protected string $primaryKey = 'id';

    protected ?int $id = null;
    private int $client_id;
    private int $creneau_id;
    private int $nombre_personnes;
    private string $statut = 'confirmee';
    private ?string $commentaire = null;
    private string $created_at;

    // Getters / Setters...

    /**
     * Scope de filtrage avancé pour l'admin
     */
    public static function adminList(array $filters = [], ?string $search = null)
    {
        $query = new static();
        
        // Appliquer la recherche sur le client (jointure)
        if ($search) {
            $query->queryWhere[] = "EXISTS (
                SELECT 1 FROM clients WHERE clients.id = reservations.client_id 
                AND (clients.nom LIKE :search OR clients.telephone LIKE :search)
            )";
            $query->queryBindings['search'] = '%' . $search . '%';
        }

        // Appliquer les filtres (statut, date)
        return $query->filter($filters)
                     ->orderBy('created_at', 'DESC');
    }

    /**
     * Relation : récupère le client associé
     */
    public function client(): ?Client
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Relation : récupère le créneau associé
     */
    public function creneau(): ?Creneau
    {
        return $this->belongsTo(Creneau::class, 'creneau_id');
    }

    // Export des données formatées pour Excel / PDF
    public static function exportData(array $filters = [], ?string $search = null): array
    {
        $reservations = self::adminList($filters, $search)->get();
        $data = [];
        foreach ($reservations as $res) {
            $client = $res->client();
            $creneau = $res->creneau();
            $data[] = [
                'ID' => $res->getId(),
                'Date' => $creneau ? $creneau->getDateReservation() : '',
                'Heure' => $creneau ? substr($creneau->getHeureDebut(), 0, 5) : '',
                'Client' => $client ? $client->getNom() : '',
                'Téléphone' => $client ? $client->getTelephone() : '',
                'Personnes' => $res->getNombrePersonnes(),
                'Statut' => $res->getStatut()
            ];
        }
        return $data;
    }
}