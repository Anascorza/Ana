<?php
/**
 * API REST para Painel Administrativo — R.Créditos Whitesystem
 * Versão Profissional Completa (v12.3) - Todas as Ações + Logs Integrados
 * Otimizada para InfinityFree
 */

// 1. Garantir que as configurações globais e autenticação estejam carregadas
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

// 2. Autenticação e Verificação de Admin
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autenticado.']);
    exit;
}

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Apenas administradores.']);
    exit;
}

// 3. Inicialização de Variáveis
$user      = currentUser();
$adminId   = $user['id'] ?? null;
$db        = getDB();

// 4. Lê Body JSON
$raw    = file_get_contents('php://input');
$body   = json_decode($raw, true) ?? [];
$action = $body['action'] ?? '';

// 5. Função de Resposta Padronizada
function resposta($ok, $msg = '', $dados = null) {
    echo json_encode(['sucesso' => $ok, 'mensagem' => $msg, 'dados' => $dados]);
    exit;
}

// ═══════════════════════════════════════════════════════════════════
// DASHBOARD
// ═══════════════════════════════════════════════════════════════════

if ($action === 'dashboard.resumo') {
    $stmt = $db->prepare('
        SELECT 
            (SELECT COUNT(*) FROM usuarios WHERE tipo = "operador" AND admin_id = ?) as total_operadores,
            (SELECT COUNT(*) FROM rutas WHERE usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?)) as total_rutas,
            (SELECT COUNT(*) FROM clientes WHERE usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?)) as total_clientes,
            (SELECT COALESCE(SUM(valor_emprestimo), 0) FROM vendas WHERE usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?)) as total_emprestado,
            (SELECT COALESCE(SUM(valor_pago), 0) FROM pagamentos WHERE venda_id IN (SELECT id FROM vendas WHERE usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?))) as total_recebido,
            (SELECT COALESCE(SUM(juros_valor), 0) FROM vendas WHERE usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?)) as total_juros,
            (SELECT COALESCE(SUM(valor), 0) FROM multas WHERE status = "aberto" AND venda_id IN (SELECT id FROM vendas WHERE usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?))) as total_multas_abertas
    ');
    $stmt->execute([$adminId, $adminId, $adminId, $adminId, $adminId, $adminId, $adminId]);
    $resumo = $stmt->fetch();
    resposta(true, '', $resumo);
}

// ═══════════════════════════════════════════════════════════════════
// OPERADORES (CRUD Completo)
// ═══════════════════════════════════════════════════════════════════

if ($action === 'operadores.listar') {
    $stmt = $db->prepare('SELECT id, nome, email, tipo, ativo, criado_em FROM usuarios WHERE admin_id = ? ORDER BY nome');
    $stmt->execute([$adminId]);
    resposta(true, '', $stmt->fetchAll());
}

if ($action === 'operadores.buscar') {
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID do operador não fornecido.');
    
    $stmt = $db->prepare('SELECT id, nome, email, tipo, ativo, criado_em FROM usuarios WHERE id = ? AND admin_id = ?');
    $stmt->execute([$id, $adminId]);
    $operador = $stmt->fetch();
    
    if (!$operador) resposta(false, 'Operador não encontrado.');
    resposta(true, '', $operador);
}

if ($action === 'operadores.salvar') {
    $d = $body['dados'] ?? [];
    
    if (!$d['nome'] || !$d['email']) {
        resposta(false, 'Nome e email são obrigatórios.');
    }
    
    if (!empty($d['id'])) {
        // EDITAR OPERADOR
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ? AND admin_id = ?');
        $stmt->execute([$d['id'], $adminId]);
        $operadorAntigo = $stmt->fetch();
        
        if (!$operadorAntigo) resposta(false, 'Operador não encontrado.');
        
        $stmt = $db->prepare('UPDATE usuarios SET nome = ?, email = ?, ativo = ? WHERE id = ? AND admin_id = ?');
        $stmt->execute([$d['nome'], $d['email'], $d['ativo'] ?? 1, $d['id'], $adminId]);
        
        logAcao('EDITAR_OPERADOR', "Editou operador: " . $d['nome'], 'usuarios', $d['id'], $operadorAntigo, $d);
        resposta(true, 'Operador atualizado com sucesso.');
    } else {
        // CRIAR OPERADOR
        if (!$d['senha']) {
            resposta(false, 'Senha é obrigatória para novo operador.');
        }
        
        $senha = password_hash($d['senha'], PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha, tipo, admin_id, ativo) VALUES (?, ?, ?, "operador", ?, 1)');
        $stmt->execute([$d['nome'], $d['email'], $senha, $adminId]);
        
        $newId = $db->lastInsertId();
        logAcao('CRIAR_OPERADOR', "Criou novo operador: " . $d['nome'], 'usuarios', $newId, null, $d);
        resposta(true, 'Operador criado com sucesso.', ['id' => $newId]);
    }
}

if ($action === 'operadores.excluir') {
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID do operador não fornecido.');
    
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ? AND admin_id = ?');
    $stmt->execute([$id, $adminId]);
    $operador = $stmt->fetch();
    
    if (!$operador) resposta(false, 'Operador não encontrado.');
    
    // Desativar em vez de deletar (melhor para auditoria)
    $stmt = $db->prepare('UPDATE usuarios SET ativo = 0 WHERE id = ? AND admin_id = ?');
    $stmt->execute([$id, $adminId]);
    
    logAcao('EXCLUIR_OPERADOR', "Desativou operador: " . $operador['nome'], 'usuarios', $id, $operador, null);
    resposta(true, 'Operador excluído com sucesso.');
}

// ═══════════════════════════════════════════════════════════════════
// ROTAS (CRUD Completo)
// ═══════════════════════════════════════════════════════════════════

if ($action === 'rutas.listar') {
    $stmt = $db->prepare('
        SELECT r.*, u.nome as usuario_nome 
        FROM rutas r 
        LEFT JOIN usuarios u ON r.usuario_id = u.id 
        WHERE u.admin_id = ? 
        ORDER BY r.nome
    ');
    $stmt->execute([$adminId]);
    resposta(true, '', $stmt->fetchAll());
}

if ($action === 'rutas.buscar') {
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID da ruta não fornecido.');
    
    $stmt = $db->prepare('
        SELECT r.*, u.nome as usuario_nome 
        FROM rutas r 
        LEFT JOIN usuarios u ON r.usuario_id = u.id 
        WHERE r.id = ? AND u.admin_id = ?
    ');
    $stmt->execute([$id, $adminId]);
    $ruta = $stmt->fetch();
    
    if (!$ruta) resposta(false, 'Ruta não encontrada.');
    resposta(true, '', $ruta);
}

if ($action === 'rutas.salvar') {
    $d = $body['dados'] ?? [];
    
    if (!$d['nome'] || !$d['usuario_id']) {
        resposta(false, 'Nome e operador são obrigatórios.');
    }
    
    // Verificar se o operador pertence a este admin
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE id = ? AND admin_id = ?');
    $stmt->execute([$d['usuario_id'], $adminId]);
    if (!$stmt->fetch()) resposta(false, 'Operador não encontrado.');
    
    if (!empty($d['id'])) {
        // EDITAR RUTA
        $stmt = $db->prepare('SELECT * FROM rutas WHERE id = ? AND usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?)');
        $stmt->execute([$d['id'], $adminId]);
        $rutaAntiga = $stmt->fetch();
        
        if (!$rutaAntiga) resposta(false, 'Ruta não encontrada.');
        
        $stmt = $db->prepare('UPDATE rutas SET usuario_id = ?, nome = ?, descricao = ?, capital_base = ?, ativa = ? WHERE id = ?');
        $stmt->execute([$d['usuario_id'], $d['nome'], $d['descricao'] ?? null, $d['capital_base'] ?? 0, $d['ativa'] ?? 1, $d['id']]);
        
        logAcao('EDITAR_RUTA', "Editou ruta: " . $d['nome'], 'rutas', $d['id'], $rutaAntiga, $d);
        resposta(true, 'Ruta atualizada com sucesso.');
    } else {
        // CRIAR RUTA
        $stmt = $db->prepare('INSERT INTO rutas (usuario_id, nome, descricao, capital_base, ativa) VALUES (?, ?, ?, ?, 1)');
        $stmt->execute([$d['usuario_id'], $d['nome'], $d['descricao'] ?? null, $d['capital_base'] ?? 0]);
        
        $newId = $db->lastInsertId();
        logAcao('CRIAR_RUTA', "Criou nova ruta: " . $d['nome'], 'rutas', $newId, null, $d);
        resposta(true, 'Ruta criada com sucesso.', ['id' => $newId]);
    }
}

if ($action === 'rutas.excluir') {
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID da ruta não fornecido.');
    
    $stmt = $db->prepare('SELECT * FROM rutas WHERE id = ? AND usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?)');
    $stmt->execute([$id, $adminId]);
    $ruta = $stmt->fetch();
    
    if (!$ruta) resposta(false, 'Ruta não encontrada.');
    
    // Desativar em vez de deletar
    $stmt = $db->prepare('UPDATE rutas SET ativa = 0 WHERE id = ?');
    $stmt->execute([$id]);
    
    logAcao('EXCLUIR_RUTA', "Desativou ruta: " . $ruta['nome'], 'rutas', $id, $ruta, null);
    resposta(true, 'Ruta excluída com sucesso.');
}

// ═══════════════════════════════════════════════════════════════════
// MOVIMENTAÇÕES (Listar e Criar)
// ═══════════════════════════════════════════════════════════════════

if ($action === 'movimentacoes.listar') {
    $stmt = $db->prepare('
        SELECT m.*, u.nome as usuario_nome, r.nome as ruta_nome
        FROM movimentacoes m
        LEFT JOIN usuarios u ON m.usuario_id = u.id
        LEFT JOIN rutas r ON m.ruta_id = r.id
        WHERE u.admin_id = ?
        ORDER BY m.data_mov DESC, m.criado_em DESC
        LIMIT 500
    ');
    $stmt->execute([$adminId]);
    resposta(true, '', $stmt->fetchAll());
}

if ($action === 'movimentacoes.salvar') {
    $d = $body['dados'] ?? [];
    
    // Mapear camelCase para snake_case se necessário
    $ruta_id = $d['ruta_id'] ?? $d['rutaId'] ?? null;
    $tipo = $d['tipo'] ?? null;
    $valor = $d['valor'] ?? null;
    $data_mov = $d['data_mov'] ?? $d['dataMov'] ?? null;
    $descricao = $d['descricao'] ?? null;
    $cliente_nome = $d['cliente_nome'] ?? $d['clienteNome'] ?? null;
    
    if (!$ruta_id || !$tipo || !$valor || !$data_mov) {
        resposta(false, 'Ruta, tipo, valor e data são obrigatórios.');
    }
    
    // Verificar se a ruta pertence a um operador deste admin
    $stmt = $db->prepare('SELECT usuario_id FROM rutas WHERE id = ? AND usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ?)');
    $stmt->execute([$ruta_id, $adminId]);
    $rutaRow = $stmt->fetch();
    
    if (!$rutaRow) resposta(false, 'Ruta não encontrada.');
    
    $usuarioId = $rutaRow['usuario_id'];
    
    $stmt = $db->prepare('
        INSERT INTO movimentacoes (ruta_id, usuario_id, tipo, valor, descricao, cliente_nome, data_mov)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $ruta_id,
        $usuarioId,
        $tipo,
        $valor,
        $descricao,
        $cliente_nome,
        $data_mov
    ]);
    
    $newId = $db->lastInsertId();
    logAcao('CRIAR_MOVIMENTACAO', "Registrou movimentação: " . $tipo . " de R$ " . $valor, 'movimentacoes', $newId, null, $d);
    resposta(true, 'Movimentação registrada com sucesso.', ['id' => $newId]);
}

// ═══════════════════════════════════════════════════════════════════
// LOGS (Visualização e Filtros)
// ═══════════════════════════════════════════════════════════════════

if ($action === 'logs.filtros') {
    // Buscar usuários (operadores e o próprio admin)
    $stmt = $db->prepare('SELECT id, nome FROM usuarios WHERE admin_id = ? OR id = ? ORDER BY nome');
    $stmt->execute([$adminId, $adminId]);
    $usuarios = $stmt->fetchAll();
    
    // Buscar rutas
    $stmt = $db->prepare('SELECT id, nome FROM rutas WHERE usuario_id IN (SELECT id FROM usuarios WHERE admin_id = ? OR id = ?) ORDER BY nome');
    $stmt->execute([$adminId, $adminId]);
    $rutas = $stmt->fetchAll();
    
    // Buscar tipos de ações únicas
    $stmt = $db->prepare('SELECT DISTINCT acao FROM logs_acoes WHERE admin_id = ? OR usuario_id = ? ORDER BY acao');
    $stmt->execute([$adminId, $adminId]);
    $acoes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    resposta(true, '', [
        'usuarios' => $usuarios,
        'rutas' => $rutas,
        'acoes' => $acoes
    ]);
}

if ($action === 'logs.listar') {
    $f = $body['filtros'] ?? [];
    
    $sql = "SELECT l.*, u.nome as usuario_nome, r.nome as ruta_nome 
            FROM logs_acoes l 
            LEFT JOIN usuarios u ON l.usuario_id = u.id 
            LEFT JOIN rutas r ON l.ruta_id = r.id
            WHERE (l.admin_id = ? OR l.usuario_id = ?)";
    $params = [$adminId, $adminId];
    
    if (!empty($f['usuario_id'])) {
        $sql .= " AND l.usuario_id = ?";
        $params[] = $f['usuario_id'];
    }
    
    if (!empty($f['ruta_id'])) {
        $sql .= " AND l.ruta_id = ?";
        $params[] = $f['ruta_id'];
    }
    
    if (!empty($f['acao'])) {
        $sql .= " AND l.acao = ?";
        $params[] = $f['acao'];
    }
    
    if (!empty($f['data_inicio'])) {
        $sql .= " AND l.criado_em >= ?";
        $params[] = $f['data_inicio'] . ' 00:00:00';
    }
    
    if (!empty($f['data_fim'])) {
        $sql .= " AND l.criado_em <= ?";
        $params[] = $f['data_fim'] . ' 23:59:59';
    }
    
    $sql .= " ORDER BY l.criado_em DESC LIMIT 1000";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    resposta(true, '', $stmt->fetchAll());
}

resposta(false, 'Ação administrativa inválida.');
