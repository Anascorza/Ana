<?php
/**
 * API REST Central — R.Créditos & Whitesystem
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

// 2. Autenticação Obrigatória
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autenticado.']);
    exit;
}

// 3. Inicialização de Variáveis
$user      = currentUser();
$usuarioId = $user['id'] ?? null;
$db        = getDB();
$isClosed  = isRouteClosed();

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
            (SELECT COUNT(*) FROM clientes WHERE usuario_id = ?) as total_clientes,
            (SELECT COUNT(*) FROM vendas WHERE usuario_id = ?) as total_vendas,
            (SELECT COALESCE(SUM(valor_emprestimo), 0) FROM vendas WHERE usuario_id = ?) as total_emprestado,
            (SELECT COALESCE(SUM(valor_pago), 0) FROM pagamentos WHERE venda_id IN (SELECT id FROM vendas WHERE usuario_id = ?)) as total_recebido,
            (SELECT COALESCE(SUM(juros_valor), 0) FROM vendas WHERE usuario_id = ?) as total_juros,
            (SELECT COALESCE(SUM(valor), 0) FROM multas WHERE status = "aberto" AND venda_id IN (SELECT id FROM vendas WHERE usuario_id = ?)) as total_multas_abertas,
            (SELECT COALESCE(SUM(valor), 0) FROM multas WHERE status = "pago" AND venda_id IN (SELECT id FROM vendas WHERE usuario_id = ?)) as total_multas_pagas
    ');
    $stmt->execute([$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId]);
    $resumo = $stmt->fetch();
    
    // Calcular lucro
    $resumo['lucro'] = (float)$resumo['total_juros'] + (float)$resumo['total_multas_pagas'];
    
    resposta(true, '', $resumo);
}

// ═══════════════════════════════════════════════════════════════════
// CLIENTES (CRUD Completo)
// ═══════════════════════════════════════════════════════════════════

if ($action === 'clientes.listar') {
    $stmt = $db->prepare('SELECT * FROM clientes WHERE usuario_id = ? ORDER BY nome');
    $stmt->execute([$usuarioId]);
    resposta(true, '', $stmt->fetchAll());
}

if ($action === 'clientes.buscar') {
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID do cliente não fornecido.');
    
    $stmt = $db->prepare('SELECT * FROM clientes WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuarioId]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) resposta(false, 'Cliente não encontrado.');
    resposta(true, '', $cliente);
}

if ($action === 'clientes.salvar') {
    if ($isClosed) resposta(false, 'Operações bloqueadas: Rota fechada ou antes das 08:00.');
    
    $d = $body['dados'] ?? [];
    
    if (!$d['nome']) {
        resposta(false, 'Nome do cliente é obrigatório.');
    }
    
    $id = $d['id'] ?? null;
    
    if ($id) {
        // EDITAR CLIENTE
        $stmt = $db->prepare('SELECT * FROM clientes WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$id, $usuarioId]);
        $clienteAntigo = $stmt->fetch();

        if (!$clienteAntigo) resposta(false, 'Cliente não encontrado.');

        $stmt = $db->prepare('UPDATE clientes SET nome=?, id_numerico=?, nome_comercial=?, cpf=?, rg=?, celular=?, tipo_endereco=?, endereco_res=?, endereco_com=?, obs=?, foto_thumb=? WHERE id=? AND usuario_id=?');
        $stmt->execute([
            $d['nome'],
            $d['id_numerico'] ?? $d['idNumerico'] ?? null,
            $d['nome_comercial'] ?? $d['nomeComercial'] ?? null,
            $d['cpf'] ?? null,
            $d['rg'] ?? null,
            $d['celular'] ?? null,
            $d['tipo_endereco'] ?? $d['tipoEndereco'] ?? 'residencial',
            $d['endereco_res'] ?? $d['enderecoRes'] ?? null,
            $d['endereco_com'] ?? $d['enderecoCom'] ?? null,
            $d['obs'] ?? null,
            $d['foto_thumb'] ?? $d['_fotoThumb'] ?? null,
            $id,
            $usuarioId
        ]);
        
        logAcao('EDITAR_CLIENTE', "Editou o cliente: " . $d['nome'], 'clientes', $id, $clienteAntigo, $d);
        resposta(true, 'Cliente atualizado com sucesso.');
    } else {
        // CRIAR CLIENTE
        $stmt = $db->prepare('INSERT INTO clientes (usuario_id, nome, id_numerico, nome_comercial, cpf, rg, celular, tipo_endereco, endereco_res, endereco_com, obs, foto_thumb, criado_em) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $usuarioId,
            $d['nome'],
            $d['id_numerico'] ?? $d['idNumerico'] ?? null,
            $d['nome_comercial'] ?? $d['nomeComercial'] ?? null,
            $d['cpf'] ?? null,
            $d['rg'] ?? null,
            $d['celular'] ?? null,
            $d['tipo_endereco'] ?? $d['tipoEndereco'] ?? 'residencial',
            $d['endereco_res'] ?? $d['enderecoRes'] ?? null,
            $d['endereco_com'] ?? $d['enderecoCom'] ?? null,
            $d['obs'] ?? null,
            $d['foto_thumb'] ?? $d['_fotoThumb'] ?? null,
            $d['criado_em'] ?? $d['criadoEm'] ?? date('Y-m-d')
        ]);
        
        $newId = $db->lastInsertId();
        logAcao('CRIAR_CLIENTE', "Cadastrou novo cliente: " . $d['nome'], 'clientes', $newId, null, $d);
        resposta(true, 'Cliente criado com sucesso.', ['id' => $newId]);
    }
}

if ($action === 'clientes.excluir') {
    if ($isClosed) resposta(false, 'Operações bloqueadas: Rota fechada ou antes das 08:00.');
    
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID do cliente não fornecido.');
    
    $stmt = $db->prepare('SELECT * FROM clientes WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuarioId]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) resposta(false, 'Cliente não encontrado.');
    
    // Verificar se há vendas associadas
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM vendas WHERE cliente_id = ?');
    $stmt->execute([$id]);
    $vendas = $stmt->fetch();
    
    if ($vendas['count'] > 0) {
        resposta(false, 'Não é possível excluir cliente com vendas associadas.');
    }
    
    // Deletar cliente
    $stmt = $db->prepare('DELETE FROM clientes WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuarioId]);
    
    logAcao('EXCLUIR_CLIENTE', "Excluiu o cliente: " . $cliente['nome'], 'clientes', $id, $cliente, null);
    resposta(true, 'Cliente excluído com sucesso.');
}

// ═══════════════════════════════════════════════════════════════════
// VENDAS (CRUD Completo)
// ═══════════════════════════════════════════════════════════════════

if ($action === 'vendas.listar') {
    $stmt = $db->prepare('
        SELECT v.*, c.nome as cliente_nome
        FROM vendas v
        LEFT JOIN clientes c ON v.cliente_id = c.id
        WHERE v.usuario_id = ?
        ORDER BY v.criado_em DESC
    ');
    $stmt->execute([$usuarioId]);
    resposta(true, '', $stmt->fetchAll());
}

if ($action === 'vendas.buscar') {
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID da venda não fornecido.');
    
    $stmt = $db->prepare('SELECT * FROM vendas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuarioId]);
    $venda = $stmt->fetch();
    
    if (!$venda) resposta(false, 'Venda não encontrada.');
    resposta(true, '', $venda);
}

if ($action === 'vendas.salvar') {
    if ($isClosed) resposta(false, 'Operações bloqueadas: Rota fechada ou antes das 08:00.');
    
    $d = $body['dados'] ?? [];
    
    $cliente_id = $d['cliente_id'] ?? $d['clienteId'] ?? null;
    $valor_emprestimo = $d['valor_emprestimo'] ?? $d['valorEmprestimo'] ?? null;
    $num_parcelas = $d['num_parcelas'] ?? $d['numParcelas'] ?? null;
    
    if (!$cliente_id || !$valor_emprestimo || !$num_parcelas) {
        resposta(false, 'Cliente, valor e número de parcelas são obrigatórios.');
    }
    
    // Verificar se o cliente pertence a este usuário
    $stmt = $db->prepare('SELECT id FROM clientes WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$cliente_id, $usuarioId]);
    if (!$stmt->fetch()) resposta(false, 'Cliente não encontrado.');
    
    $id = $d['id'] ?? null;
    
    if ($id) {
        // EDITAR VENDA
        $stmt = $db->prepare('SELECT * FROM vendas WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$id, $usuarioId]);
        $vendaAntiga = $stmt->fetch();

        if (!$vendaAntiga) resposta(false, 'Venda não encontrada.');
        
        if ((int)$vendaAntiga['edicoes_restantes'] <= 0) {
            resposta(false, 'Limite de edições atingido para esta venda.');
        }

        $stmt = $db->prepare('
            UPDATE vendas 
            SET cliente_id=?, valor_emprestimo=?, juros_pct=?, juros_valor=?, total_com_juros=?, 
                num_parcelas=?, tipo_venc=?, valor_parcela=?, data_inicio=?, dias_multa=?, valor_multa=?,
                edicoes_restantes = edicoes_restantes - 1
            WHERE id=? AND usuario_id=?
        ');
        $stmt->execute([
            $cliente_id,
            $valor_emprestimo,
            $d['juros_pct'] ?? $d['jurosPct'] ?? 0,
            $d['juros_valor'] ?? $d['jurosValor'] ?? 0,
            $d['total_com_juros'] ?? $d['totalComJuros'] ?? 0,
            $num_parcelas,
            $d['tipo_venc'] ?? $d['tipoVenc'] ?? 'mensal',
            $d['valor_parcela'] ?? $d['valorParcela'] ?? 0,
            $d['data_inicio'] ?? $d['dataInicio'] ?? date('Y-m-d'),
            $d['dias_multa'] ?? $d['diasMulta'] ?? 2,
            $d['valor_multa'] ?? $d['valorMulta'] ?? 0,
            $id,
            $usuarioId
        ]);
        
        logAcao('EDITAR_VENDA', "Alterou valores da venda ID: " . $id, 'vendas', $id, $vendaAntiga, $d);
        resposta(true, 'Venda atualizada com sucesso.');
    } else {
        // CRIAR VENDA
        $stmt = $db->prepare('
            INSERT INTO vendas (usuario_id, cliente_id, valor_emprestimo, juros_pct, juros_valor, total_com_juros, 
                               num_parcelas, tipo_venc, parcelas_pagas, saldo_parcial_pago, valor_parcela, 
                               data_inicio, dias_multa, valor_multa, criado_em, edicoes_restantes)
            VALUES (?,?,?,?,?,?,?,?,0,0,?,?,?,?,?,3)
        ');
        $stmt->execute([
            $usuarioId,
            $cliente_id,
            $valor_emprestimo,
            $d['juros_pct'] ?? $d['jurosPct'] ?? 0,
            $d['juros_valor'] ?? $d['jurosValor'] ?? 0,
            $d['total_com_juros'] ?? $d['totalComJuros'] ?? 0,
            $num_parcelas,
            $d['tipo_venc'] ?? $d['tipoVenc'] ?? 'mensal',
            $d['valor_parcela'] ?? $d['valorParcela'] ?? 0,
            $d['data_inicio'] ?? $d['dataInicio'] ?? date('Y-m-d'),
            $d['dias_multa'] ?? $d['diasMulta'] ?? 2,
            $d['valor_multa'] ?? $d['valorMulta'] ?? 0,
            $d['criado_em'] ?? $d['criadoEm'] ?? date('Y-m-d')
        ]);
        
        $newId = $db->lastInsertId();
        
        // Gerar parcelas automaticamente
        $valorParcela = $d['valor_parcela'] ?? $d['valorParcela'] ?? 0;
        $dataVenc = $d['data_inicio'] ?? $d['dataInicio'] ?? date('Y-m-d');
        $tipoVenc = $d['tipo_venc'] ?? $d['tipoVenc'] ?? 'mensal';
        
        for ($i = 1; $i <= $num_parcelas; $i++) {
            $stmt = $db->prepare('INSERT INTO parcelas (venda_id, cliente_id, numero, data_vencimento, valor, status) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$newId, $cliente_id, $i, $dataVenc, $valorParcela, 'futuro']);
            
            // Incrementar data
            if ($tipoVenc === 'diario') $dataVenc = date('Y-m-d', strtotime($dataVenc . ' +1 day'));
            elseif ($tipoVenc === 'semanal') $dataVenc = date('Y-m-d', strtotime($dataVenc . ' +1 week'));
            else $dataVenc = date('Y-m-d', strtotime($dataVenc . ' +1 month'));
        }
        
        logAcao('CRIAR_VENDA', "Realizou nova venda ID: " . $newId, 'vendas', $newId, null, $d);
        resposta(true, 'Venda criada com sucesso.', ['id' => $newId]);
    }
}

if ($action === 'vendas.excluir') {
    if ($isClosed) resposta(false, 'Operações bloqueadas: Rota fechada ou antes das 08:00.');
    
    $id = $body['id'] ?? null;
    if (!$id) resposta(false, 'ID da venda não fornecido.');
    
    $stmt = $db->prepare('SELECT * FROM vendas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuarioId]);
    $venda = $stmt->fetch();
    
    if (!$venda) resposta(false, 'Venda não encontrada.');
    
    // Deletar parcelas, multas e pagamentos associados
    $db->prepare('DELETE FROM parcelas WHERE venda_id = ?')->execute([$id]);
    $db->prepare('DELETE FROM multas WHERE venda_id = ?')->execute([$id]);
    $db->prepare('DELETE FROM pagamentos WHERE venda_id = ?')->execute([$id]);
    
    // Deletar venda
    $stmt = $db->prepare('DELETE FROM vendas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuarioId]);
    
    logAcao('EXCLUIR_VENDA', "Excluiu a venda ID: " . $id, 'vendas', $id, $venda, null);
    resposta(true, 'Venda excluída com sucesso.');
}

// ═══════════════════════════════════════════════════════════════════
// PARCELAS E MULTAS
// ═══════════════════════════════════════════════════════════════════

if ($action === 'parcelas.listar') {
    $venda_id = $body['venda_id'] ?? $body['vendaId'] ?? null;
    if (!$venda_id) resposta(false, 'ID da venda não fornecido.');
    
    $stmt = $db->prepare('SELECT * FROM parcelas WHERE venda_id = ? ORDER BY numero');
    $stmt->execute([$venda_id]);
    resposta(true, '', $stmt->fetchAll());
}

if ($action === 'multas.listar') {
    $venda_id = $body['venda_id'] ?? $body['vendaId'] ?? null;
    if (!$venda_id) {
        $stmt = $db->prepare('SELECT m.*, c.nome as cliente_nome FROM multas m LEFT JOIN clientes c ON m.cliente_id = c.id WHERE m.venda_id IN (SELECT id FROM vendas WHERE usuario_id = ?) ORDER BY m.criado_em DESC');
        $stmt->execute([$usuarioId]);
    } else {
        $stmt = $db->prepare('SELECT * FROM multas WHERE venda_id = ? ORDER BY criado_em DESC');
        $stmt->execute([$venda_id]);
    }
    resposta(true, '', $stmt->fetchAll());
}

if ($action === 'multas.aplicar') {
    if ($isClosed) resposta(false, 'Operações bloqueadas.');
    $d = $body['dados'] ?? [];
    $venda_id = $d['venda_id'] ?? null;
    $valor = $d['valor'] ?? 0;
    
    if (!$venda_id || $valor <= 0) resposta(false, 'Dados inválidos.');
    
    $stmt = $db->prepare('SELECT cliente_id FROM vendas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$venda_id, $usuarioId]);
    $venda = $stmt->fetch();
    if (!$venda) resposta(false, 'Venda não encontrada.');
    
    $stmt = $db->prepare('INSERT INTO multas (venda_id, cliente_id, valor, motivo, origem, status, criado_em) VALUES (?, ?, ?, ?, "manual", "aberto", ?)');
    $stmt->execute([$venda_id, $venda['cliente_id'], $valor, $d['motivo'] ?? 'Multa manual', date('Y-m-d')]);
    
    logAcao('APLICAR_MULTA', "Aplicou multa de R$ $valor na venda ID: $venda_id", 'multas', $db->lastInsertId(), null, $d);
    resposta(true, 'Multa aplicada com sucesso.');
}

// ═══════════════════════════════════════════════════════════════════
// PAGAMENTOS (Lógica Refatorada)
// ═══════════════════════════════════════════════════════════════════

if ($action === 'pagamentos.registrar') {
    if ($isClosed) resposta(false, 'Operações bloqueadas: Rota fechada ou antes das 08:00.');
    
    $d = $body['dados'] ?? [];
    $venda_id = $d['venda_id'] ?? $d['vendaId'] ?? null;
    $valor_pago = (float)($d['valor_pago'] ?? $d['valorPago'] ?? 0);
    $parcela_id = $d['parcela_id'] ?? $d['parcelaId'] ?? null;
    $multa_id = $d['multa_id'] ?? $d['multaId'] ?? null;
    
    if (!$venda_id || $valor_pago <= 0) {
        resposta(false, 'Venda e valor pago são obrigatórios.');
    }
    
    $stmt = $db->prepare('SELECT * FROM vendas WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$venda_id, $usuarioId]);
    $venda = $stmt->fetch();
    
    if (!$venda) resposta(false, 'Venda não encontrada.');
    
    $db->beginTransaction();
    try {
        // 1. Registrar o pagamento na tabela de pagamentos
        $stmt = $db->prepare('INSERT INTO pagamentos (venda_id, cliente_id, valor_pago, forma_pagamento, data, motivo) VALUES (?,?,?,?,?,?)');
        $stmt->execute([
            $venda_id,
            $venda['cliente_id'],
            $valor_pago,
            $d['forma_pagamento'] ?? $d['formaPagamento'] ?? 'dinheiro',
            date('Y-m-d'),
            $d['motivo'] ?? 'Pagamento de parcela/multa'
        ]);
        $pagamentoId = $db->lastInsertId();

        // 2. Se for pagamento de uma parcela específica
        if ($parcela_id) {
            $stmt = $db->prepare('UPDATE parcelas SET valor_pago = ?, status = "pago", data_pagamento = ?, forma_pagamento = ? WHERE id = ? AND venda_id = ?');
            $stmt->execute([$valor_pago, date('Y-m-d'), $d['forma_pagamento'] ?? 'dinheiro', $parcela_id, $venda_id]);
            
            // Atualizar contador na venda
            $db->prepare('UPDATE vendas SET parcelas_pagas = parcelas_pagas + 1, ultimo_pagamento = ?, ultima_forma_pag = ? WHERE id = ?')->execute([date('Y-m-d'), $d['forma_pagamento'] ?? 'dinheiro', $venda_id]);
        } 
        // 3. Se for pagamento de uma multa específica
        elseif ($multa_id) {
            $stmt = $db->prepare('UPDATE multas SET status = "pago", pago_em = ? WHERE id = ? AND venda_id = ?');
            $stmt->execute([date('Y-m-d'), $multa_id, $venda_id]);
        }
        // 4. Pagamento genérico (abater saldo ou marcar próxima parcela)
        else {
            $db->prepare('UPDATE vendas SET saldo_parcial_pago = saldo_parcial_pago + ?, ultimo_pagamento = ? WHERE id = ?')->execute([$valor_pago, date('Y-m-d'), $venda_id]);
        }

        // 5. Registrar movimentação financeira para a RUTA
        $stmt = $db->prepare("SELECT id FROM rutas WHERE usuario_id = ? AND ativa = 1 LIMIT 1");
        $stmt->execute([$usuarioId]);
        $ruta = $stmt->fetch();
        if ($ruta) {
            $stmt = $db->prepare('INSERT INTO movimentacoes (ruta_id, usuario_id, tipo, valor, descricao, cliente_nome, data_mov) VALUES (?, ?, "pagamento", ?, ?, ?, ?)');
            $stmt->execute([
                $ruta['id'],
                $usuarioId,
                $valor_pago,
                "Recebimento Venda ID: $venda_id",
                $d['cliente_nome'] ?? 'Cliente',
                date('Y-m-d')
            ]);
        }

        $db->commit();
        logAcao('REGISTRAR_PAGAMENTO', "Registrou pagamento de R$ $valor_pago na venda ID: $venda_id", 'pagamentos', $pagamentoId, null, $d);
        resposta(true, 'Pagamento registrado com sucesso.');
    } catch (Exception $e) {
        $db->rollBack();
        resposta(false, 'Erro ao registrar pagamento: ' . $e->getMessage());
    }
}

resposta(false, 'Ação inválida.');

resposta(false, 'Ação inválida.');
