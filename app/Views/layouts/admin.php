<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Administration - RYOHA'; ?></title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Admin -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/CSS/admin.css">
</head>
<body>

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="<?php echo BASE_URL; ?>/admin" class="brand-link">
                <span class="brand-icon"><i class="fas fa-utensils"></i></span>
                <span class="brand-text">RYOHA Admin</span>
            </a>
        </div>
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></div>
                <div class="user-role"><?php echo $_SESSION['user_role'] ?? 'Administrateur'; ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin" class="<?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/reservations" class="<?php echo $active_page === 'reservations' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i> <span>Réservations</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/clients" class="<?php echo $active_page === 'clients' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> <span>Clients</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/menu" class="<?php echo $active_page === 'menu' ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i> <span>Menu</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/categories" class="<?php echo $active_page === 'categories' ? 'active' : ''; ?>">
                        <i class="fas fa-tags"></i> <span>Catégories</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/config" class="<?php echo $active_page === 'config' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> <span>Configuration</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?php echo BASE_URL; ?>/admin/logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-content" id="mainContent">
        <!-- HEADER -->
        <header class="admin-header">
            <div class="header-left">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h1><?php echo $title ?? 'Administration'; ?></h1>
            </div>
            <div class="header-right">
                <span class="header-date">
                    <i class="far fa-calendar-alt"></i> 
                    <?php echo date('d/m/Y H:i'); ?>
                </span>
                <a href="<?php echo BASE_URL; ?>/" target="_blank" class="header-link" title="Voir le site">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </header>

        <!-- MESSAGES FLASH -->
        <?php if ($this->session->get('flash_message')): ?>
            <div class="flash-message flash-<?php echo $this->session->get('flash_type', 'success'); ?>">
                <?php echo $this->session->get('flash_message'); ?>
                <?php $this->session->remove('flash_message'); $this->session->remove('flash_type'); ?>
            </div>
        <?php endif; ?>

        <!-- CONTENU DYNAMIQUE -->
        <?php echo $content ?? ''; ?>
    </div>

    <!-- ==================== FOOTER ==================== -->
    <footer class="admin-footer">
        &copy; <?php echo date('Y'); ?> <span>RYOHA</span> Restaurant - Tous droits réservés
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JS Admin -->
    <script src="<?php echo BASE_URL; ?>/JS/admin.js"></script>
</body>
</html>