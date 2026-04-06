<?php
/**
 * Dashboard Administrativo (Whitesystem)
 * Redireciona para o dashboard completo
 */

require_once __DIR__ . '/../../config/app.php';

requireAuth();

// Verificar se é admin
if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/pages/user/dashboard.php');
    exit;
}

// Redirecionar para o dashboard completo
header('Location: ' . BASE_URL . '/pages/admin/dashboard_completo.php');
exit;
?>
