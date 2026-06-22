<?php $active_page = 'home'; ?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1>Bienvenue chez <span>RYOHA</span></h1>
        <p>Découvrez une cuisine raffinée dans une ambiance chaleureuse.</p>
        <div class="hero-buttons">
            <a href="/restaurant-ryoha/reservation" class="btn btn-primary">
                <i class="fas fa-calendar-check"></i> Réserver
            </a>
            <a href="/restaurant-ryoha/menu" class="btn btn-secondary">
                <i class="fas fa-utensils"></i> Voir le menu
            </a>
        </div>
    </div>
</section>

<!-- Plats populaires -->
<?php if (!empty($plats_populaires)): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Nos plats <span>populaires</span></h2>
        <div class="plats-grid">
            <?php foreach ($plats_populaires as $plat): ?>
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
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Catégories -->
<?php if (!empty($categories)): ?>
<section class="section section-light">
    <div class="container">
        <h2 class="section-title">Explorez notre <span>carte</span></h2>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="/restaurant-ryoha/menu?categorie=<?php echo $cat->getId(); ?>" class="categorie-card">
                    <i class="fas fa-utensils"></i>
                    <span><?php echo htmlspecialchars($cat->getNom()); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Avis clients -->
<?php if (!empty($avis_recents)): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Ce que disent nos <span>clients</span></h2>
        <div class="avis-grid">
            <?php foreach ($avis_recents as $avis): ?>
                <div class="avis-card">
                    <div class="avis-header">
                        <strong><?php echo htmlspecialchars($avis->getNom()); ?></strong>
                        <span class="etoiles">
                            <?php echo str_repeat('⭐', $avis->getNote()); ?>
                        </span>
                    </div>
                    <p>"<?php echo htmlspecialchars($avis->getCommentaire() ?? ''); ?>"</p>
                    <small><?php echo date('d/m/Y', strtotime($avis->getCreatedAt())); ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>