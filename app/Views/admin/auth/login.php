<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Connexion Admin - RYOHA'; ?></title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================
           LOGIN STYLES (standalone)
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0d0d0d 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Effet de fond (optionnel) */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('<?php echo BASE_URL; ?>/images/restaurant-bg.jpg') center/cover no-repeat;
            opacity: 0.08;
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-logo {
            font-size: 32px;
            font-weight: 800;
            color: #f5e6d3;
            letter-spacing: -1px;
        }

        .login-logo span {
            color: #e8a87c;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            margin-top: 6px;
            font-weight: 400;
        }

        .login-subtitle i {
            color: #e8a87c;
            margin-right: 6px;
        }

        /* Flash messages */
        .flash-message {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            text-align: center;
        }
        .flash-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .flash-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Formulaire */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 6px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.2);
            font-size: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #f5e6d3;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #e8a87c;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(232, 168, 124, 0.1);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #e8a87c;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: #d4946a;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(232, 168, 124, 0.3);
        }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.2);
        }

        .login-footer a {
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            transition: color 0.3s;
        }

        .login-footer a:hover {
            color: #e8a87c;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
            }
            .login-logo {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">RYOHA <span>Admin</span></div>
            <div class="login-subtitle"><i class="fas fa-lock"></i> Espace de gestion</div>
        </div>

        <?php if ($this->session->get('flash_message')): ?>
            <div class="flash-message flash-<?php echo $this->session->get('flash_type', 'success'); ?>">
                <?php echo $this->session->get('flash_message'); ?>
                <?php $this->session->remove('flash_message'); $this->session->remove('flash_type'); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>/admin/login">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="admin" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-key"></i> Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-key"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>

        <div class="login-footer">
            <a href="<?php echo BASE_URL; ?>/"><i class="fas fa-arrow-left"></i> Retour au site</a>
        </div>
    </div>

</body>
</html>