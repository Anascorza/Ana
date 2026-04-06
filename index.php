<?php
/**
 * R.Créditos - Roteador Central (v12.4)
 * Otimizado para InfinityFree - Compatibilidade Máxima
 */

// 1. Definição da Raiz do Sistema
define('ROOT_PATH', __DIR__);

// 2. Carregar configurações globais e autenticação
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/includes/auth.php';

// 3. Captura da URL (compatível com .htaccess)
$url = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$url = str_replace('.php', '', $url);

// 4. Redirecionamento de Login
if (!isAuthenticated()) {
    if ($url !== 'auth/login' && strpos($url, 'app/api') === false && $url !== 'auth/logout') {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}

// 5. Mapa de Rotas
$routes = [
    '' => '/pages/user/dashboard.php',
    'dashboard' => '/pages/user/dashboard.php',
    'auth/login' => '/auth/login.php',
    'auth/logout' => '/auth/logout.php',
    'app/api' => '/app/api.php',
    'app/api_admin' => '/app/api_admin.php',
    'pages/admin/rutas' => '/pages/admin/rutas.php',
    'pages/admin/movimentacoes' => '/pages/admin/movimentacoes.php',
    'pages/admin/usuarios' => '/pages/admin/usuarios.php',
    'pages/admin/dashboard_completo' => '/pages/admin/dashboard_completo.php',
    'pages/admin/logs' => '/pages/admin/logs.php',
    'pages/user/dashboard' => '/pages/user/dashboard.php',
    'pages/user/clientes' => '/pages/user/clientes.php',
    'pages/user/vendas' => '/pages/user/vendas.php',
    'admin/rutas' => '/pages/admin/rutas.php',
    'admin/movimentacoes' => '/pages/admin/movimentacoes.php',
    'admin/usuarios' => '/pages/admin/usuarios.php',
    'admin/logs' => '/pages/admin/logs.php',
    'admin/dashboard' => '/pages/admin/dashboard_completo.php',
    'clientes' => '/pages/user/clientes.php',
    'vendas' => '/pages/user/vendas.php'
];

// 6. Lógica de Roteamento
if ($url === '' || $url === 'dashboard' || $url === 'index') {
    if (isAuthenticated()) {
        $dest = isAdmin() ? '/pages/admin/dashboard_completo.php' : '/pages/user/dashboard.php';
        require_once ROOT_PATH . $dest;
    } else {
        require_once ROOT_PATH . '/auth/login.php';
    }
    exit;
}

// 7. Roteamento via Mapa
if (isset($routes[$url])) {
    require_once ROOT_PATH . $routes[$url];
    exit;
}

// 8. Fallback para Arquivos Físicos (Assets)
$file = ROOT_PATH . '/' . $url;
if (file_exists($file) && is_file($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    if ($ext === 'php') {
        require_once $file;
        exit;
    }
    
    $mimes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'json' => 'application/json',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf'
    ];
    
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    readfile($file);
    exit;
}

// 9. Erro 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página Não Encontrada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            text-align: center;
        }
        h1 { font-size: 48px; color: #667eea; margin-bottom: 10px; }
        p { color: #666; margin-bottom: 20px; font-size: 16px; }
        .url { background: #f5f5f5; padding: 12px; border-radius: 6px; margin: 20px 0; word-break: break-all; font-family: monospace; color: #333; font-size: 12px; }
        a { display: inline-block; background: #667eea; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; margin-top: 20px; transition: background 0.3s; }
        a:hover { background: #764ba2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Página Não Encontrada</p>
        <p>O caminho solicitado não existe no sistema R.Créditos.</p>
        <div class="url"><?php echo htmlspecialchars($url); ?></div>
        <a href="<?php echo BASE_URL; ?>/dashboard">← Voltar ao Dashboard</a>
    </div>
</body>
</html>
