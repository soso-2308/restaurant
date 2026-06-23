<?php $active_page = 'categories'; ?>
<?php $isEdit = isset($categorie); ?>

<h2 style="font-size: 18px; margin-bottom: 20px;">
    <i class="fas <?php echo $isEdit ? 'fa-edit' : 'fa-plus'; ?>"></i> 
    <?php echo $isEdit ? 'Modifier' : 'Ajouter'; ?> une catégorie
</h2>

<form method="POST" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Nom *</label>
            <input type="text" name="nom" value="<?php echo $isEdit ? htmlspecialchars($categorie->getNom()) : ''; ?>" required
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
        </div>
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Ordre</label>
            <input type="number" name="ordre" value="<?php echo $isEdit ? $categorie->getOrdre() : 0; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
        </div>
        <div style="grid-column: 1/-1;">
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Description</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;"><?php echo $isEdit ? htmlspecialchars($categorie->getDescription() ?? '') : ''; ?></textarea>
        </div>
    </div>
    
    <div style="margin-top: 30px; display: flex; gap: 15px;">
        <button type="submit" style="background: #e8a87c; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
            <i class="fas fa-save"></i> <?php echo $isEdit ? 'Mettre à jour' : 'Ajouter'; ?>
        </button>
        <a href="<?php echo BASE_URL; ?>/admin/categories" style="background: #6c757d; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>
</form>