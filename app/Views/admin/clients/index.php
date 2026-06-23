<?php $active_page = 'clients'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
    <h2><i class="fas fa-users"></i> Gestion des clients</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?php echo BASE_URL; ?>/admin/clients/export/pdf?<?php echo http_build_query($filters); ?>" 
           style="background: #dc3545; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/clients/export/excel?<?php echo http_build_query($filters); ?>" 
           style="background: #28a745; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-file-excel"></i> Excel
        </a>
    </div>
</div>

<!-- Statistiques rapides -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
    <div style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <strong style="font-size: 24px;"><?php echo $stats['total']; ?></strong>
        <div style="color: #666;">Total clients</div>
    </div>
    <div style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <strong style="font-size: 24px;"><?php echo $stats['avec_reservations']; ?></strong>
        <div style="color: #666;">Avec réservations</div>
    </div>
    <div style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <strong style="font-size: 24px;"><?php echo $stats['top_telephone']; ?></strong>
        <div style="color: #666;">Téléphone le plus utilisé</div>
    </div>
</div>

<!-- Formulaire de filtre -->
<form method="GET" style="display: flex; flex-wrap: wrap; gap: 15px; background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; align-items: flex-end;">
    <div>
        <label>Recherche (nom/téléphone/email)</label>
        <input type="text" name="search" value="<?php echo $filters['search'] ?? ''; ?>" placeholder="Jean" style="padding: 8px; border-radius: 6px;">
    </div>
    <div>
        <label>Date début</label>
        <input type="date" name="date_debut" value="<?php echo $filters['date_debut'] ?? ''; ?>" style="padding: 8px; border-radius: 6px;">
    </div>
    <div>
        <label>Date fin</label>
        <input type="date" name="date_fin" value="<?php echo $filters['date_fin'] ?? ''; ?>" style="padding: 8px; border-radius: 6px;">
    </div>
    <div>
        <label>Min réservations</label>
        <input type="number" name="min_reservations" value="<?php echo $filters['min_reservations'] ?? ''; ?>" placeholder="2" style="padding: 8px; border-radius: 6px; width: 80px;">
    </div>
    <div>
        <label>Trier par</label>
        <select name="sort" style="padding: 8px; border-radius: 6px;">
            <option value="created_at" <?php echo ($filters['sort'] ?? 'created_at') == 'created_at' ? 'selected' : ''; ?>>Inscription</option>
            <option value="nom" <?php echo ($filters['sort'] ?? '') == 'nom' ? 'selected' : ''; ?>>Nom</option>
            <option value="telephone" <?php echo ($filters['sort'] ?? '') == 'telephone' ? 'selected' : ''; ?>>Téléphone</option>
        </select>
        <select name="dir" style="padding: 8px; border-radius: 6px;">
            <option value="ASC" <?php echo ($filters['dir'] ?? '') == 'ASC' ? 'selected' : ''; ?>>Croissant</option>
            <option value="DESC" <?php echo ($filters['dir'] ?? 'DESC') == 'DESC' ? 'selected' : ''; ?>>Décroissant</option>
        </select>
    </div>
    <div>
        <button type="submit" style="padding: 8px 20px; background: #e8a87c; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Filtrer</button>
        <a href="<?php echo BASE_URL; ?>/admin/clients" style="padding: 8px 20px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none;">Réinitialiser</a>
    </div>
</form>

<!-- Table des clients -->
<div class="table-container">
    <?php if (empty($clients)): ?>
        <p style="color: #999; text-align: center; padding: 40px 0;">Aucun client trouvé</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Réservations</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo $client->getId(); ?></td>
                        <td><strong><?php echo htmlspecialchars($client->getNom()); ?></strong></td>
                        <td><?php echo htmlspecialchars($client->getTelephone()); ?></td>
                        <td><?php echo htmlspecialchars($client->getEmail() ?? '-'); ?></td>
                        <td><?php echo $client->reservationsCount(); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($client->getCreatedAt())); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/admin/clients/show/<?php echo $client->getId(); ?>" 
                               style="background: #007bff; color: white; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 13px;">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <a href="?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>" 
               style="padding: 6px 12px; background: <?php echo $i == $pagination['current_page'] ? '#e8a87c' : '#f0f0f0'; ?>; 
                      color: <?php echo $i == $pagination['current_page'] ? '#1a1a1a' : '#666'; ?>; 
                      border-radius: 4px; text-decoration: none;">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>