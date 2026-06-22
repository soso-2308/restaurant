<?php $active_page = 'menu'; ?>

<div class="container">
    <h1 class="page-title">Notre <span>Menu</span></h1>
    
    <!-- Filtres -->
    <div class="menu-filters">
        <form method="GET" class="search-form">
            <input type="text" name="recherche" placeholder="Rechercher un plat..." 
                   value="<?php echo htmlspecialchars($recherche ?? ''); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        
        <div class="filter-tags">
            <a href="/restaurant-ryoha/menu" class="<?php echo !$categorie_active ? 'active' : ''; ?>">Tous</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/restaurant-ryoha/menu?categorie=<?php echo $cat->getId(); ?>" 
                   class="<?php echo $categorie_active == $cat->getId() ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat->getNom()); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Liste des plats -->
    <?php if (empty($plats)): ?>
        <div class="empty-state">
            <i class="fas fa-utensils"></i>
            <p>Aucun plat trouvé</p>
        </div>
    <?php else: ?>
        <div class="plats-grid">
            <?php foreach ($plats as $plat): ?>
                <div class="plat-card">
                    <img src="<?php echo $plat->getImageUrl() ?? 'https://via.placeholder.com/300x200/1a1a1a/f5e6d3?text=' . urlencode($plat->getNom()); ?>" 
                         alt="<?php echo htmlspecialchars($plat->getNom()); ?>">
                    <div class="plat-card-body">
                        <h3><?php echo htmlspecialchars($plat->getNom()); ?></h3>
                        <p><?php echo htmlspecialchars($plat->getDescription() ?? ''); ?></p>
                        <div class="plat-card-footer">
                            <span class="prix"><?php echo number_format($plat->getPrix(), 0, ',', ' '); ?> FBu</span>
                            <a href="https://wa.me/25779123456?text=Bonjour%20RYOHA%2C%20je%20souhaite%20commander%20:%20<?php echo urlencode($plat->getNom()); ?>" 
                               target="_blank" class="btn-whatsapp">
                                <i class="fab fa-whatsapp"></i> Commander
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>