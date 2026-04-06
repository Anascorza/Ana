<?php
/**
 * Dashboard do Usuário (Operador)
 * Versão Profissional Completa (v12) - Dados Reais
 */

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

require_once __DIR__ . '/../../includes/header.php';
requireAuth();
?>

<div class="stats-grid">
    <div class="stat-card" id="clientesCard">
        <span class="stat-label">Clientes Ativos</span>
        <span class="stat-value">-</span>
    </div>
    <div class="stat-card" id="vendasCard">
        <span class="stat-label">Total de Vendas</span>
        <span class="stat-value">-</span>
    </div>
    <div class="stat-card" id="recebidoCard">
        <span class="stat-label">Total Recebido</span>
        <span class="stat-value positive">-</span>
    </div>
    <div class="stat-card" id="lucroCard">
        <span class="stat-label">Lucro Estimado</span>
        <span class="stat-value text-warning">-</span>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600;">Últimas Vendas</h2>
        <a href="<?= BASE_URL ?>/vendas" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Ver Todas</a>
    </div>
    
    <div class="table-container">
        <table id="vendasTable">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Parcelas</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="vendasTableBody">
                <tr><td colspan="5" style="text-align: center; padding: 20px;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function api(action, dados = {}) {
    return fetch('<?= BASE_URL ?>/app/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, ...dados })
    }).then(r => r.json());
}

function loadDashboard() {
    api('dashboard.resumo').then(res => {
        if (res.sucesso && res.dados) {
            const d = res.dados;
            document.getElementById('clientesCard').querySelector('.stat-value').textContent = d.total_clientes || 0;
            document.getElementById('vendasCard').querySelector('.stat-value').textContent = d.total_vendas || 0;
            document.getElementById('recebidoCard').querySelector('.stat-value').textContent = formatMoney(d.total_recebido || 0);
            document.getElementById('lucroCard').querySelector('.stat-value').textContent = formatMoney(d.lucro || 0);
        }
    });
    
    api('vendas.listar').then(res => {
        const tbody = document.getElementById('vendasTableBody');
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">Nenhuma venda encontrada.</td></tr>';
            return;
        }
        
        // Mostrar apenas as 5 últimas vendas
        const vendas = res.dados.slice(0, 5);
        tbody.innerHTML = vendas.map(v => {
            const status = v.parcelas_pagas >= v.num_parcelas ? 'Finalizado' : 'Ativo';
            const statusColor = status === 'Ativo' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(59, 130, 246, 0.1)';
            const statusColorText = status === 'Ativo' ? '#10b981' : '#3b82f6';
            
            return `
                <tr>
                    <td>${escapeHtml(v.cliente_nome || 'Cliente #' + v.cliente_id)}</td>
                    <td>${formatMoney(v.valor_emprestimo)}</td>
                    <td>${v.parcelas_pagas || 0}/${v.num_parcelas}</td>
                    <td>${formatDate(v.data_inicio)}</td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background-color: ${statusColor}; color: ${statusColorText};">
                            ${status}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');
    });
}

function formatMoney(value) {
    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('pt-BR');
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Carregar dashboard ao abrir a página
document.addEventListener('DOMContentLoaded', loadDashboard);
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
