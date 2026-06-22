<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin - RYOHA'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            color: #2d2d2d;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* SIDEBAR */
        .admin-sidebar {
            width: 280px;
            background: #1a1a1a;
            color: #f5e6d3;
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-sidebar .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: #e8a87c;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .admin-sidebar .logo span {
            color: #f5e6d3;
        }
        
        .admin-sidebar .user-info {
            text-align: center;
            padding: 15px;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .admin-sidebar .user-info .name {
            font-weight: 600;
            font-size: 16px;
        }
        
        .admin-sidebar .user-info .role {
            font-size: 12px;
            color: #e8a87c;
            text-transform: uppercase;
        }
        
        .admin-sidebar nav ul {
            list-style: none;
        }
        
        .admin-sidebar nav li {
            margin-bottom: 5px;
        }
        
        .admin-sidebar nav a {
            color: #aaa;
            text-decoration: none;
            padding: 12px 15px;
            display: block;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .admin-sidebar nav a:hover,
        .admin-sidebar nav a.active {
            background: rgba(232, 168, 124, 0.15);
            color: #e8a87c;
        }
        
        .admin-sidebar nav a i {
            margin-right: 12px;
            width: 20px;
        }
        
        .admin-sidebar .logout-link {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 20px;
        }
        
        .admin-sidebar .logout-link a {
            color: #ff6b6b;
        }
        
        .admin-sidebar .logout-link a:hover {
            background: rgba(255, 107, 107, 0.1);
        }
        
        /* CONTENU */
        .admin-content {
            margin-left: 280px;
            padding: 30px;
            width: 100%;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .admin-header h1 {
            font-size: 24px;
            font-weight: 600;
        }
        
        .admin-header .date {
            color: #666;
            font-size: 14px;
        }
        
        /* FLASH */
        .flash-message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .flash-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
        }
        
        .stat-card .label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .stat-card .icon {
            float: right;
            font-size: 28px;
            color: #e8a87c;
            opacity: 0.5;
        }
        
        /* TABLE */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        
        .table-container h2 {
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th {
            text-align: left;
            padding: 12px;
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #eee;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        table tr:hover {
            background: #faf8f5;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-confirmee { background: #d4edda; color: #155724; }
        .badge-annulee { background: #f8d7da; color: #721c24; }
        .badge-terminee { background: #cce5ff; color: #004085; }
        
        .btn {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 15px;
            }
            .admin-content {
                margin-left: 0;
                padding: 20px;
            }
            .admin-sidebar nav ul {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            .admin-sidebar nav li {
                flex: 1;
                min-width: 100px;
            }
            .admin-sidebar .logout-link {
                margin-top: 10px;
                border-top: none;
                padding-top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <a href="/restaurant-ryoha/admin" class="logo">RYOHA <span>Admin</span></a>
            
            <div class="user-info">
                <div class="name">
                    <i class="fas fa-user-circle"></i> 
                    <?php echo $_SESSION['user_name'] ?? 'Admin'; ?>
                </div>
                <div class="role">
                    <?php echo $_SESSION['user_role'] ?? 'Administrateur'; ?>
                </div>
            </div>
            
            <nav>
                <ul>
                    <li><a href="/restaurant-ryoha/admin" class="<?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i> Dashboard
                    </a></li>
                    <li><a href="/restaurant-ryoha/admin/reservations" class="<?php echo $active_page === 'reservations' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i> Réservations
                    </a></li>
                    <li><a href="/restaurant-ryoha/admin/menu" class="<?php echo $active_page === 'menu' ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i> Menu
                    </a></li>
                    <li><a href="/restaurant-ryoha/admin/config" class="<?php echo $active_page === 'config' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> Configuration
                    </a></li>
                </ul>
            </nav>
            
            <div class="logout-link">
                <a href="/restaurant-ryoha/admin/logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </aside>
        
        <!-- CONTENU -->
        <main class="admin-content">
            <div class="admin-header">
                <h1><?php echo $title ?? 'Administration'; ?></h1>
                <div class="date">
                    <i class="far fa-calendar-alt"></i> 
                    <?php echo date('d/m/Y H:i'); ?>
                </div>
            </div>
            
            <?php if ($this->session->get('flash_message')): ?>
                <div class="flash-message flash-<?php echo $this->session->get('flash_type', 'success'); ?>">
                    <?php echo $this->session->get('flash_message'); ?>
                    <?php $this->session->remove('flash_message'); $this->session->remove('flash_type'); ?>
                </div>
            <?php endif; ?>
            
            <?php echo $content ?? ''; ?>
        </main>
    </div>
</body>
</html>