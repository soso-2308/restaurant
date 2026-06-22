<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - RYOHA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 420px;
        }
        .login-container .logo {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .login-container .logo span { color: #e8a87c; }
        .login-container .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #e8a87c;
            outline: none;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #e8a87c;
            color: #1a1a1a;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover { background: #d4946a; }
        .flash-message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .flash-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { text-align: center; margin-top: 20px; font-size: 14px; }
        .back-link a { color: #666; text-decoration: none; }
        .back-link a:hover { color: #e8a87c; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">RYOHA <span>Admin</span></div>
        <div class="subtitle"><i class="fas fa-lock"></i> Espace de gestion</div>
        
        <?php if ($this->session->get('flash_message')): ?>
            <div class="flash-message flash-<?php echo $this->session->get('flash_type', 'success'); ?>">
                <?php echo $this->session->get('flash_message'); ?>
                <?php $this->session->remove('flash_message'); $this->session->remove('flash_type'); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/restaurant-ryoha/admin/login">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                <input type="text" id="username" name="username" placeholder="admin" required autofocus>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-key"></i> Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>
        
        <div class="back-link">
            <a href="/restaurant-ryoha/"><i class="fas fa-arrow-left"></i> Retour au site</a>
        </div>
    </div>
</body>
</html>