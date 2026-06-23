<?php $active_page = 'categories'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
    <h2><i class="fas fa-tags"></i> Gestion des catégories</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?php echo BASE_URL; ?>/admin/categories/create" class="btn btn-success">
            <i class="fas fa-plus"></i> Ajouter
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/categories/export/pdf?<?php echo http_build_query($filters); ?>" 
           style="background: #dc3545; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/categories/export/excel?<?php echo http_build_query($filters); ?>" 
           style="background: #28a745; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-file-excel"></i> Excel
        </a>
    </div>
</div>

<!-- Formulaire de filtre -->
<form method="GET" style="display: flex; flex-wrap: wrap; gap: 15px; background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; align-items: flex-end;">
    <div>
        <label>Recherche (nom/description)</label>
        <input type="text" name="search" value="<?php echo $filters['search'] ?? ''; ?>" placeholder="Ex: Entrée" style="padding: 8px; border-radius: 6px;">
    </div>
    <div>
        <label>Ordre exact</label>
        <input type="number" name="ordre" value="<?php echo $filters['ordre'] ?? ''; ?>" placeholder="Ex: 1" style="padding: 8px; border-radius: 6px; width: 80px;">
    </div>
    <div>
        <label>Trier par</label>
        <select name="sort" style="padding: 8px; border-radius: 6px;">
            <option value="ordre" <?php echo ($filters['sort'] ?? 'ordre') == 'ordre' ? 'selected' : ''; ?>>Ordre</option>
            <option value="nom" <?php echo ($filters['sort'] ?? '') == 'nom' ? 'selected' : ''; ?>>Nom</option>
            <option value="created_at" <?php echo ($filters['sort'] ?? '') == 'created_at' ? 'selected' : ''; ?>>Création</option>
        </select>
        <select name="dir" style="padding: 8px; border-radius: 6px;">
            <option value="ASC" <?php echo ($filters['dir'] ?? 'ASC') == 'ASC' ? 'selected' : ''; ?>>Croissant</option>
            <option value="DESC" <?php echo ($filters['dir'] ?? '') == 'DESC' ? 'selected' : ''; ?>>Décroissant</option>
        </select>
    </div>
    <div>
        <button type="submit" style="padding: 8px 20px; background: #e8a87c; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Filtrer</button>
        <a href="<?php echo BASE_URL; ?>/admin/categories" style="padding: 8px 20px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none;">Réinitialiser</a>
    </div>
</form>

<!-- Table -->
<div class="table-container">
    <?php if (empty($categories)): ?>
        <p style="color: #999; text-align: center; padding: 40px 0;">Aucune catégorie trouvée</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Ordre</th>
                    <th>Plats</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat->getId(); ?></td>
                        <td><strong><?php echo htmlspecialchars($cat->getNom()); ?></strong></td>
                        <td><?php echo htmlspecialchars($cat->getDescription() ?? ''); ?></td>
                        <td><?php echo $cat->getOrdre(); ?></td>
                        <td><?php echo $cat->platsCount(); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/admin/categories/edit/<?php echo $cat->getId(); ?>" 
                               style="background: #007bff; color: white; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 13px;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="supprimer(<?php echo $cat->getId(); ?>)" 
                                    style="background: #dc3545; color: white; padding: 4px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px;">
                                <i class="fas fa-trash"></i>
                            </button>
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

<script>
function supprimer(id) {
    if (!confirm('Supprimer cette catégorie ?')) return;
    $.ajax({
        url: '<?php echo BASE_URL; ?>/admin/categories/delete',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('✅ ' + response.message);
                location.reload();
            } else {
                alert('❌ ' + response.message);
            }
        },
        error: function() {
            alert('Erreur lors de la suppression');
        }
    });
}
</script>