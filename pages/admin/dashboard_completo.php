<?php
/**
 * Dashboard Administrativo Completo — Sistema Branco
 * Visão Geral Financeira e Operacional (v10)
 */
require_once ROOT_PATH . '/includes/header.php';

// Garantir que apenas administradores acessem
if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/dashboard');
    exit;
}

$db = getDB();
$adminId = $_SESSION['user_id'];

// 1. DADOS GERAIS FINANCEIROS (Otimizado para MySQL 5.7+)
$stmt = $db->prepare("
    SELECT 
        COALESCE(SUM(v.valor_emprestimo), 0) as total_emprestado,
        COALESCE(SUM(v.total_com_juros), 0) as total_a_receber_bruto,
        COALESCE(SUM(v.juros_valor), 0) as total_juros
    FROM vendas v
    JOIN usuarios u ON v.usuario_id = u.id
    WHERE u.admin_id = ? OR u.id = ?
");
$stmt->execute([$adminId, $adminId]);
$financeiroBase = $stmt->fetch();

// Total Recebido via Pagamentos
$stmtRec = $db->prepare("
    SELECT COALESCE(SUM(p.valor_pago), 0) as total_recebido
    FROM pagamentos p
    JOIN vendas v ON p.venda_id = v.id
    JOIN usuarios u ON v.usuario_id = u.id
    WHERE u.admin_id = ? OR u.id = ?
");
$stmtRec->execute([$adminId, $adminId]);
$recebido = $stmtRec->fetch();

// Multas
$stmtMultas = $db->prepare("
    SELECT 
        COALESCE(SUM(CASE WHEN m.status = 'aberto' THEN m.valor ELSE 0 END), 0) as total_multas_abertas,
        COALESCE(SUM(CASE WHEN m.status = 'pago' THEN m.valor ELSE 0 END), 0) as total_multas_pagas
    FROM multas m
    JOIN vendas v ON m.venda_id = v.id
    JOIN usuarios u ON v.usuario_id = u.id
    WHERE u.admin_id = ? OR u.id = ?
");
$stmtMultas->execute([$adminId, $adminId]);
$multas = $stmtMultas->fetch();

$total_recebido = (float)$recebido['total_recebido'];
$total_a_receber = (float)$financeiroBase['total_a_receber_bruto'] + (float)$multas['total_multas_abertas'] + (float)$multas['total_multas_pagas'] - $total_recebido;
$lucro_total = (float)$financeiroBase['total_juros'] + (float)$multas['total_multas_pagas'];

// 2. DADOS POR RUTA
$stmtRutas = $db->prepare("
    SELECT 
        r.id, r.nome, r.capital_base,
        (SELECT COALESCE(SUM(valor_emprestimo), 0) FROM vendas v WHERE v.usuario_id = r.usuario_id) as emprestado,
        (SELECT COALESCE(SUM(valor_pago), 0) FROM pagamentos p JOIN vendas v2 ON p.venda_id = v2.id WHERE v2.usuario_id = r.usuario_id) as recebido
    FROM rutas r
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE u.admin_id = ? OR u.id = ?
");
$stmtRutas->execute([$adminId, $adminId]);
$rutas = $stmtRutas->fetchAll();

// 3. STATUS DE CLIENTES
$stmtClientes = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN EXISTS (SELECT 1 FROM parcelas p WHERE p.cliente_id = c.id AND p.status = 'atrasado') THEN 1 ELSE 0 END) as atrasados,
        SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM parcelas p2 WHERE p2.cliente_id = c.id AND p2.status != 'pago') THEN 1 ELSE 0 END) as pagos
    FROM clientes c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE u.admin_id = ? OR u.id = ?
");
$stmtClientes->execute([$adminId, $adminId]);
$clientesStatus = $stmtClientes->fetch();
?>

<div class="content-wrapper">
    <!-- 1. CARDS FINANCEIROS GERAIS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Emprestado</div>
            <div class="stat-value"><?= formatMoney($financeiroBase['total_emprestado']) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total a Receber</div>
            <div class="stat-value text-primary"><?= formatMoney($total_a_receber) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Recebido</div>
            <div class="stat-value text-success"><?= formatMoney($total_recebido) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Lucro Total (Juros + Multas)</div>
            <div class="stat-value text-warning"><?= formatMoney($lucro_total) ?></div>
        </div>
    </div>

    <div class="dashboard-row" style="margin-top: 24px; display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- 2. TABELA DE RUTAS -->
        <div class="card">
            <div class="card-header">
                <h3>📍 Desempenho por RUTA</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome da RUTA</th>
                            <th>Capital Base</th>
                            <th>Emprestado</th>
                            <th>Recebido</th>
                            <th>Em Rua</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rutas as $r): 
                            $saldoRua = (float)$r['emprestado'] - (float)$r['recebido'];
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['nome']) ?></strong></td>
                            <td><?= formatMoney($r['capital_base']) ?></td>
                            <td><?= formatMoney($r['emprestado']) ?></td>
                            <td><?= formatMoney($r['recebido']) ?></td>
                            <td class="text-danger"><?= formatMoney($saldoRua) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. STATUS DE CLIENTES -->
        <div class="card">
            <div class="card-header">
                <h3>👥 Status dos Clientes</h3>
            </div>
            <div class="card-body">
                <div class="client-stat-item">
                    <span>Total de Clientes:</span>
                    <strong><?= (int)$clientesStatus['total'] ?></strong>
                </div>
                <div class="client-stat-item">
                    <span class="text-success">Clientes Pagos:</span>
                    <strong><?= (int)$clientesStatus['pagos'] ?></strong>
                </div>
                <div class="client-stat-item">
                    <span class="text-danger">Clientes em Atraso:</span>
                    <strong><?= (int)$clientesStatus['atrasados'] ?></strong>
                </div>
                <div class="client-stat-item">
                    <span class="text-warning">Pendentes:</span>
                    <strong><?= (int)$clientesStatus['total'] - (int)$clientesStatus['pagos'] - (int)$clientesStatus['atrasados'] ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. LOG DE AÇÕES RECENTES (RASTREABILIDADE) -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3>🔄 Rastreabilidade Total (Logs de Ações)</h3>
        </div>
        <div class="card-body table-responsive">
            <?php
            $stmtLogs = $db->prepare("
                SELECT l.*, u.nome as usuario_nome 
                FROM logs_acoes l
                JOIN usuarios u ON l.usuario_id = u.id
                WHERE u.admin_id = ? OR u.id = ?
                ORDER BY l.criado_em DESC LIMIT 15
            ");
            $stmtLogs->execute([$adminId, $adminId]);
            $logs = $stmtLogs->fetchAll();
            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="4" style="text-align: center;">Nenhum log registrado ainda.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($logs as $l): ?>
                    <tr>
                        <td style="white-space: nowrap;"><?= date('d/m/Y H:i', strtotime($l['criado_em'])) ?></td>
                        <td><?= htmlspecialchars($l['usuario_nome']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($l['acao']) ?></span></td>
                        <td><?= htmlspecialchars($l['descricao']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
.stat-card { background: var(--card-bg); padding: 24px; border-radius: 12px; border: 1px solid var(--border-color); }
.stat-title { font-size: 14px; color: var(--text-muted); margin-bottom: 8px; }
.stat-value { font-size: 24px; font-weight: 700; }
.client-stat-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color); }
.text-success { color: #10b981; }
.text-danger { color: #ef4444; }
.text-warning { color: #f59e0b; }
.badge { background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 12px; color: #333; }
.table-responsive { overflow-x: auto; }
</style>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
