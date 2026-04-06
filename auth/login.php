<?php
/**
 * Página de Login
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';

// Redirecionar se já estiver logado
redirectIfAuthenticated();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        redirectIfAuthenticated();
    } else {
        $error = 'E-mail ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-color);
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-weight: 700;
            font-size: 28px;
            margin: 0 auto 16px;
        }
        .login-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .login-subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-color);
            font-size: 15px;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            margin-top: 12px;
            font-size: 16px;
        }
        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">R</div>
            <h1 class="login-title">Bem-vindo</h1>
            <p class="login-subtitle">Faça login para acessar sua conta</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/login.php" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="seu@email.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>
