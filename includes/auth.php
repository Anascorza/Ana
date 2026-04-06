<?php
/**
 * Funções de Autenticação e Autorização
 * Otimizado para InfinityFree (v9) - Revisão de Redirecionamentos Absolutos
 */

// 1. Garantir que as configurações globais estejam carregadas (Usa ROOT_PATH)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
require_once ROOT_PATH . '/config/app.php';

/**
 * Verifica se o usuário está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verifica se o usuário é administrador
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Verifica se o usuário é operador
 */
function isOperator() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'operador';
}

/**
 * Redireciona para o login se não estiver autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}

/**
 * Redireciona para o dashboard se já estiver autenticado
 */
function redirectIfAuthenticated() {
    if (isAuthenticated()) {
        $dashboard = isAdmin() ? '/admin/dashboard' : '/dashboard';
        header('Location: ' . BASE_URL . $dashboard);
        exit;
    }
}

/**
 * Realiza o login do usuário
 */
function login($email, $password) {
    $sql = "SELECT id, nome, senha, tipo FROM usuarios WHERE email = ? AND ativo = 1";
    $stmt = executeQuery($sql, [$email]);
    $user = $stmt ? $stmt->fetch() : null;

    if ($user && password_verify($password, $user['senha'])) {
        // Iniciar sessão com ID regenerado por segurança
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nome'];
        $_SESSION['user_role'] = $user['tipo'];
        return true;
    }
    return false;
}

/**
 * Realiza o logout do usuário
 */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}
