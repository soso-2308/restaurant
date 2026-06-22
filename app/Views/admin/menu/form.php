<?php $active_page = 'menu'; ?>

<h2 style="font-size: 18px; margin-bottom: 20px;">
    <i class="fas <?php echo $plat ? 'fa-edit' : 'fa-plus'; ?>"></i> 
    <?php echo $plat ? 'Modifier le plat' : 'Ajouter un plat'; ?>
</h2>

<form method="POST" enctype="multipart/form-data" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Nom -->
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Nom du plat *</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($plat ? $plat->getNom() : ''); ?>" required
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
        </div>
        
        <!-- Prix -->
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Prix (FBu) *</label>
            <input type="number" name="prix" step="100" min="0" value="<?php echo $plat ? $plat->getPrix() : 0; ?>" required
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
        </div>
        
        <!-- Catégorie -->
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Catégorie *</label>
            <select name="categorie_id" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat->getId(); ?>" 
                            <?php echo ($plat && $plat->getCategorieId() == $cat->getId()) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat->getNom()); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Disponible -->
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Statut</label>
            <div style="padding-top: 8px;">
                <label>
                    <input type="checkbox" name="disponible" <?php echo ($plat && $plat->isDisponible()) || !$plat ? 'checked' : ''; ?>> 
                    Disponible
                </label>
            </div>
        </div>
        
        <!-- Image -->
        <div style="grid-column: 1/-1;">
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Image du plat</label>
            <?php if ($plat && $plat->getImageUrl()): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?php echo $plat->getImageUrl(); ?>" alt="Image actuelle" style="max-width: 100px; border-radius: 8px;">
                    <p style="font-size: 12px; color: #666;">Image actuelle</p>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                   style="width: 100%; padding: 10px; border: 2px dashed #ddd; border-radius: 8px;">
            <p style="font-size: 12px; color: #999; margin-top: 5px;">Formats : JPG, PNG, GIF, WebP (max 5MB)</p>
        </div>
        
        <!-- Description -->
        <div style="grid-column: 1/-1;">
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Description</label>
            <textarea name="description" rows="4" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; resize: vertical;"><?php echo htmlspecialchars($plat ? $plat->getDescription() ?? '' : ''); ?></textarea>
        </div>
    </div>
    
    <div style="margin-top: 30px; display: flex; gap: 15px;">
        <button type="submit" style="background: #e8a87c; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
            <i class="fas fa-save"></i> <?php echo $plat ? 'Mettre à jour' : 'Ajouter'; ?>
        </button>
        <a href="/restaurant-ryoha/admin/menu" style="background: #6c757d; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>
</form>