<?php
/**
 * Cabeçalho Principal
 * Otimizado para InfinityFree (v9) - Revisão de Links e Assets
 */
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/includes/auth.php';

// Garantir autenticação em todas as páginas que incluem o header
requireAuth();

$user = currentUser();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — Sistema de Créditos</title>
    
    <!-- Fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Configuração Global para JavaScript -->
    <script>const BASE_URL = '<?= BASE_URL ?>';</script>
    
    <!-- CSS Principal (Caminhos Absolutos via BASE_URL) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <?php if (isAdmin()): ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    <?php endif; ?>
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">R</div>
                <span class="app-title"><?= APP_NAME ?></span>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Links Amigáveis para o Roteador -->
                <a href="<?= BASE_URL ?>/dashboard" class="nav-link">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
                
                <?php if (isAdmin()): ?>
                <div class="nav-section">ADMINISTRAÇÃO</div>
                <a href="<?= BASE_URL ?>/admin/dashboard" class="nav-link">
                    <span class="nav-icon">📈</span> Visão Geral
                </a>
                <a href="<?= BASE_URL ?>/admin/usuarios" class="nav-link">
                    <span class="nav-icon">👥</span> Operadores
                </a>
                <a href="<?= BASE_URL ?>/admin/rutas" class="nav-link">
                    <span class="nav-icon">🗺️</span> Rutas
                </a>
                <a href="<?= BASE_URL ?>/admin/movimentacoes" class="nav-link">
                    <span class="nav-icon">💸</span> Movimentações
                </a>
                <a href="<?= BASE_URL ?>/admin/logs" class="nav-link">
                    <span class="nav-icon">📋</span> Logs de Ações
                </a>
                <?php endif; ?>
                
                <div class="nav-section">OPERAÇÃO</div>
                <a href="<?= BASE_URL ?>/clientes" class="nav-link">
                    <span class="nav-icon">👤</span> Clientes
                </a>
                <a href="<?= BASE_URL ?>/vendas" class="nav-link">
                    <span class="nav-icon">💰</span> Vendas / Cobranças
                </a>
                
                <div class="sidebar-footer">
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($user['nome']) ?></div>
                        <div class="user-role"><?= ucfirst($user['tipo']) ?></div>
                    </div>
                    <a href="<?= BASE_URL ?>/auth/logout" class="btn-logout" title="Sair">🚪</a>
                </div>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="top-bar">
                <div class="page-info">
                    <h1 class="page-title" id="page-title">Dashboard</h1>
                </div>
                <div class="top-bar-actions">
                    <span class="date-display"><?= date('d/m/Y') ?></span>
                </div>
            </header>
