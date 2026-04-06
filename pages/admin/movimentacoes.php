<?php
/**
 * Página de Movimentações (Admin)
 * Versão Profissional Completa (v12.3) - Funcionalidade Real + Logs
 */

$pageTitle = 'Movimentações';
$activePage = 'movimentacoes';

require_once __DIR__ . '/../../includes/header.php';
requireAuth();

// Verificar se é admin
if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/dashboard');
    exit;
}
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600;">Movimentações Financeiras</h2>
        <button class="btn btn-primary" onclick="openMovimentacaoModal()">+ Nova Movimentação</button>
    </div>
    
    <div class="table-container">
        <table id="movimentacoesTable">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Ruta / Operador</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody id="movimentacoesTableBody">
                <tr><td colspan="6" style="text-align: center; padding: 20px;">Carregando movimentações...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para criar movimentação -->
<div id="movimentacaoModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Nova Movimentação</h3>
        
        <form id="movimentacaoForm">
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Ruta / Operador *</label>
                <select id="movRuta" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                    <option value="">Carregando rutas...</option>
                </select>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Tipo *</label>
                <select id="movTipo" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                    <option value="">Selecione um tipo</option>
                    <option value="aporte">Aporte (Entrada)</option>
                    <option value="retirada">Retirada (Saída)</option>
                    <option value="emprestimo">Empréstimo (Saída)</option>
                    <option value="pagamento">Pagamento (Entrada)</option>
                    <option value="multa">Multa (Entrada)</option>
                    <option value="ajuste">Ajuste</option>
                </select>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Valor *</label>
                <input type="number" id="movValor" step="0.01" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Cliente (opcional)</label>
                <input type="text" id="movCliente" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Descrição</label>
                <textarea id="movDescricao" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color); resize: vertical; min-height: 80px;"></textarea>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Data *</label>
                <input type="date" id="movData" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn" style="flex: 1; padding: 10px; background-color: var(--border-color); color: var(--text-color); border-radius: 8px; cursor: pointer;" onclick="closeMovimentacaoModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" style="flex: 1; padding: 10px; border-radius: 8px; cursor: pointer;" onclick="saveMovimentacao()">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
const tiposMap = {
    'emprestimo': 'Empréstimo',
    'pagamento': 'Pagamento',
    'multa': 'Multa',
    'aporte': 'Aporte',
    'retirada': 'Retirada',
    'ajuste': 'Ajuste'
};

function loadMovimentacoes() {
    apiAdmin('movimentacoes.listar').then(res => {
        const tbody = document.getElementById('movimentacoesTableBody');
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Nenhuma movimentação encontrada.</td></tr>';
            return;
        }
        
        tbody.innerHTML = res.dados.map(m => `
            <tr>
                <td>${formatDate(m.data_mov)}</td>
                <td>
                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background-color: ${['pagamento', 'multa', 'aporte'].includes(m.tipo) ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; color: ${['pagamento', 'multa', 'aporte'].includes(m.tipo) ? '#10b981' : '#ef4444'};">
                        ${escapeHtml(tiposMap[m.tipo] || m.tipo)}
                    </span>
                </td>
                <td>
                    <strong>${escapeHtml(m.ruta_nome || 'Ruta #' + m.ruta_id)}</strong><br>
                    <small style="color: var(--text-muted);">${escapeHtml(m.usuario_nome || 'Operador #' + m.usuario_id)}</small>
                </td>
                <td>${escapeHtml(m.cliente_nome || '-')}</td>
                <td style="color: ${parseFloat(m.valor) >= 0 ? '#10b981' : '#ef4444'}; font-weight: 600;">
                    ${formatMoney(m.valor)}
                </td>
                <td>${escapeHtml(m.descricao || '-')}</td>
            </tr>
        `).join('');
    });
}

function loadRutas() {
    apiAdmin('rutas.listar').then(res => {
        const select = document.getElementById('movRuta');
        select.innerHTML = '<option value="">Selecione uma ruta</option>';
        
        if (res.sucesso && res.dados) {
            res.dados.forEach(r => {
                const option = document.createElement('option');
                option.value = r.id;
                option.textContent = `${r.nome} (${r.usuario_nome})`;
                select.appendChild(option);
            });
        }
    });
}

function openMovimentacaoModal() {
    document.getElementById('movimentacaoForm').reset();
    document.getElementById('movData').value = new Date().toISOString().split('T')[0];
    loadRutas();
    document.getElementById('movimentacaoModal').style.display = 'flex';
}

function closeMovimentacaoModal() {
    document.getElementById('movimentacaoModal').style.display = 'none';
}

function saveMovimentacao() {
    const rutaId = document.getElementById('movRuta').value;
    const tipo = document.getElementById('movTipo').value;
    const valor = document.getElementById('movValor').value;
    const dataMov = document.getElementById('movData').value;
    
    if (!rutaId || !tipo || !valor || !dataMov) {
        alert('Preencha todos os campos obrigatórios');
        return;
    }
    
    const dados = {
        ruta_id: parseInt(rutaId),
        tipo: tipo,
        valor: parseFloat(valor),
        cliente_nome: document.getElementById('movCliente').value || null,
        descricao: document.getElementById('movDescricao').value || null,
        data_mov: dataMov
    };
    
    apiAdmin('movimentacoes.salvar', { dados }).then(res => {
        if (res.sucesso) {
            closeMovimentacaoModal();
            loadMovimentacoes();
            showNotification('Movimentação registrada com sucesso!', 'success');
        } else {
            alert('Erro: ' + res.mensagem);
        }
    });
}

function formatMoney(value) {
    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('pt-BR');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function showNotification(msg, type) {
    const div = document.createElement('div');
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background-color: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        border-radius: 4px;
        z-index: 2000;
    `;
    div.textContent = msg;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

document.getElementById('movimentacaoModal').addEventListener('click', function(e) {
    if (e.target === this) closeMovimentacaoModal();
});

document.addEventListener('DOMContentLoaded', loadMovimentacoes);
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
