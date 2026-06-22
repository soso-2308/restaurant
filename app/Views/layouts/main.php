<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Restaurant RYOHA'; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="/restaurant-ryoha/assets/css/app.css">
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <a href="/restaurant-ryoha/" class="logo">
                RYOHA <span>Restaurant</span>
            </a>
            <nav class="nav">
                <ul>
                    <li><a href="/restaurant-ryoha/" class="<?php echo $active_page === 'home' ? 'active' : ''; ?>">Accueil</a></li>
                    <li><a href="/restaurant-ryoha/menu" class="<?php echo $active_page === 'menu' ? 'active' : ''; ?>">Menu</a></li>
                    <li><a href="/restaurant-ryoha/reservation" class="<?php echo $active_page === 'reservation' ? 'active' : ''; ?>">Réserver</a></li>
                    <li><a href="/restaurant-ryoha/contact" class="<?php echo $active_page === 'contact' ? 'active' : ''; ?>">Contact</a></li>
                </ul>
            </nav>
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- MESSAGES FLASH -->
    <?php if ($this->session->get('flash_message')): ?>
        <div class="flash-message flash-<?php echo $this->session->get('flash_type', 'success'); ?>">
            <div class="container">
                <?php echo $this->session->get('flash_message'); ?>
                <?php $this->session->remove('flash_message'); $this->session->remove('flash_type'); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- CONTENU PRINCIPAL -->
    <main class="main">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>RYOHA</h3>
                    <p>Une expérience culinaire unique au cœur de Bujumbura.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Horaires</h4>
                    <p><strong>Midi :</strong> 12h00 - 14h30</p>
                    <p><strong>Soir :</strong> 19h00 - 22h00</p>
                    <p><em>Fermé le dimanche</em></p>
                </div>
                <div class="footer-col">
                    <h4>Contact</h4>
                    <p><i class="fas fa-phone"></i> +257 79 123 456</p>
                    <p><i class="fas fa-envelope"></i> contact@ryoha.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Bujumbura, Burundi</p>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; <?php echo date('Y'); ?> Restaurant RYOHA - Tous droits réservés
            </div>
        </div>
    </footer>

    <!-- WHATSAPP FLOTTANT -->
    <a href="https://wa.me/25779123456?text=Bonjour%20RYOHA%2C%20je%20souhaite%20passer%20une%20commande." 
       class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JavaScript personnalisé -->
    <script src="/restaurant-ryoha/assets/js/app.js"></script>
</body>
</html>