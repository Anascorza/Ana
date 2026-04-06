<?php
/**
 * Página de Logout
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';

if (isAuthenticated()) {
    logAcao('LOGOUT', 'Usuário realizou logout do sistema', 'usuarios', $_SESSION['user_id']);
}

logout();
?>
