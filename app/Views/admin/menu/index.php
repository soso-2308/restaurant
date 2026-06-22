<?php $active_page = 'menu'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
    <h2 style="font-size: 18px;">
        <i class="fas fa-utensils"></i> Gestion du menu
    </h2>
    <a href="/restaurant-ryoha/admin/menu/create" 
       style="background: #28a745; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
        <i class="fas fa-plus"></i> Ajouter un plat
    </a>
</div>

<div class="table-container">
    <?php if (empty($plats)): ?>
        <p style="color: #999; text-align: center; padding: 40px 0;">
            <i class="fas fa-utensils" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
            Aucun plat dans le menu
            <br>
            <a href="/restaurant-ryoha/admin/menu/create" style="color: #e8a87c;">Ajouter votre premier plat</a>
        </p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Disponible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plats as $plat): ?>
                    <tr>
                        <td>
                            <img src="<?php echo $plat->getImageUrl() ?? 'https://via.placeholder.com/50x50/1a1a1a/f5e6d3?text=' . urlencode(substr($plat->getNom(), 0, 2)); ?>" 
                                 alt="<?php echo htmlspecialchars($plat->getNom()); ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        </td>
                        <td><strong><?php echo htmlspecialchars($plat->getNom()); ?></strong></td>
                        <td><?php echo htmlspecialchars($plat->getCategorieId() ?? 'Non catégorisé'); ?></td>
                        <td><strong><?php echo number_format($plat->getPrix(), 0, ',', ' '); ?> FBu</strong></td>
                        <td>
                            <span style="color: <?php echo $plat->isDisponible() ? '#28a745' : '#dc3545'; ?>;">
                                <?php echo $plat->isDisponible() ? '✅ Oui' : '❌ Non'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="/restaurant-ryoha/admin/menu/edit/<?php echo $plat->getId(); ?>" 
                               style="background: #007bff; color: white; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 13px;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="supprimerPlat(<?php echo $plat->getId(); ?>)" 
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

<script>
function supprimerPlat(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce plat ?')) {
        return;
    }
    
    $.ajax({
        url: '/restaurant-ryoha/admin/menu/delete',
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