<?php
/**
 * R.Créditos — Configuração Global Definitiva (v11)
 * Otimizado para InfinityFree - Senha: hollaphy
 * Correção: Compatibilidade Legada MySQL (LONGTEXT em vez de JSON)
 */

// 1. Silenciamento Total de Erros (Prevenção de Erro 500)
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 2. Definição da Raíz do Sistema (Absoluta)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/..');
}

// 3. Constantes Globais
define('APP_NAME',    'R.Créditos');
define('APP_VERSION', '11.0');

// 4. URL Base Dinâmica (Caminho Relativo)
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $base_path);

// 5. Banco de Dados (Credenciais Atualizadas)
define('DB_HOST',    'sql302.infinityfree.com');
define('DB_PORT',    3306);
define('DB_NAME',    'if0_41425409_rcreditos');
define('DB_USER',    'if0_41425409');
define('DB_PASS',    'hollaphy'); 
define('DB_CHARSET', 'utf8mb4');

// 6. Timezone e Sessão
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 7. Conexão PDO (Compatibilidade Máxima)
function getDB() {
    static $db = null;
    if ($db !== null) return $db;

    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    try {
        $db = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
    } catch (PDOException $e) {
        error_log('[DB ERROR] ' . $e->getMessage());
        die('Erro de Conexão com o Banco de Dados. Verifique o painel do InfinityFree.');
    }
    return $db;
}

// 8. Funções Auxiliares de Sistema
function executeQuery($sql, $params = []) {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (Exception $e) {
        error_log('[QUERY ERROR] ' . $e->getMessage());
        return null;
    }
}

/**
 * Log de Ações Profissional (Rastreabilidade Total)
 * Compatível com MySQL Legado (Usa LONGTEXT para dados serializados)
 */
function logAcao($acao, $descricao, $tabela = null, $itemId = null, $dadosAntigos = null, $dadosNovos = null) {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) return;

    // Buscar admin_id e ruta_id se for operador
    $adminId = null;
    $rutaId = null;
    
    if ($_SESSION['user_role'] === 'admin') {
        $adminId = $userId;
    } else {
        // Se for operador, buscar o admin_id dele
        $db = getDB();
        $stmt = $db->prepare("SELECT admin_id FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $u = $stmt->fetch();
        $adminId = $u['admin_id'] ?? null;
        
        // Buscar a ruta ativa do operador
        $stmt = $db->prepare("SELECT id FROM rutas WHERE usuario_id = ? AND ativa = 1 LIMIT 1");
        $stmt->execute([$userId]);
        $r = $stmt->fetch();
        $rutaId = $r['id'] ?? null;
    }

    $sql = "INSERT INTO logs_acoes (usuario_id, admin_id, ruta_id, acao, tabela, item_id, descricao, dados_antigos, dados_novos, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $userId,
        $adminId,
        $rutaId,
        $acao,
        $tabela,
        $itemId,
        $descricao,
        $dadosAntigos ? json_encode($dadosAntigos, JSON_UNESCAPED_UNICODE) : null,
        $dadosNovos ? json_encode($dadosNovos, JSON_UNESCAPED_UNICODE) : null,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ];

    executeQuery($sql, $params);
}

function currentUser() {
    if (!isset($_SESSION['user_id'])) return null;
    return [
        'id'   => $_SESSION['user_id'],
        'nome' => $_SESSION['user_name'] ?? 'Usuário',
        'tipo' => $_SESSION['user_role'] ?? 'operador'
    ];
}

function isRouteClosed() {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) return false;
    $today = date('Y-m-d');
    $stmt = executeQuery("SELECT id FROM fechamentos WHERE usuario_id = ? AND data_fechamento = ?", [$userId, $today]);
    $res = $stmt ? $stmt->fetch() : null;
    return $res || (int)date('H') < 8;
}

function formatMoney($v) { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }
function formatDate($d) { return $d ? date('d/m/Y', strtotime($d)) : ''; }

function calculateRutaBalance($rutaId) {
    $stmt = executeQuery("
        SELECT
            (SELECT COALESCE(SUM(valor),0) FROM movimentacoes WHERE ruta_id = ? AND tipo IN ('aporte','pagamento','multa')) -
            (SELECT COALESCE(SUM(ABS(valor)),0) FROM movimentacoes WHERE ruta_id = ? AND tipo IN ('emprestimo','retirada')) AS saldo
    ", [$rutaId, $rutaId]);
    $res = $stmt ? $stmt->fetch() : null;
    return (float)($res['saldo'] ?? 0);
}
