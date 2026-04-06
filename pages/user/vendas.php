<?php
/**
 * Página de Vendas (Usuário/Operador)
 * Versão Profissional Completa (v12) - Funcionalidade Real
 */

$pageTitle = 'Vendas';
$activePage = 'vendas';

require_once __DIR__ . '/../../includes/header.php';
requireAuth();
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600;">Minhas Vendas</h2>
        <button class="btn btn-primary" onclick="openVendaModal()" style="padding: 8px 16px; font-size: 14px;">+ Nova Venda</button>
    </div>
    
    <div class="table-container">
        <table id="vendasTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Valor Empréstimo</th>
                    <th>Total com Juros</th>
                    <th>Parcelas</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="vendasTableBody">
                <tr><td colspan="7" style="text-align: center; padding: 20px;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para criar venda -->
<div id="vendaModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Nova Venda</h3>
        
        <form id="vendaForm">
            <input type="hidden" id="vendaId" value="">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Cliente *</label>
                    <select id="vendaCliente" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                        <option value="">Carregando clientes...</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Valor Empréstimo *</label>
                    <input type="number" id="vendaValor" step="0.01" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Juros (%)</label>
                    <input type="number" id="vendaJuros" step="0.01" value="0" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Número de Parcelas *</label>
                    <input type="number" id="vendaParcelas" min="1" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Tipo de Vencimento</label>
                    <select id="vendaTipo" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                        <option value="mensal">Mensal</option>
                        <option value="semanal">Semanal</option>
                        <option value="diario">Diário</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Data de Início *</label>
                    <input type="date" id="vendaData" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Dias para Multa</label>
                    <input type="number" id="vendaDiasMulta" value="2" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Valor da Multa</label>
                    <input type="number" id="vendaValorMulta" step="0.01" value="0" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn" style="flex: 1; padding: 10px; background-color: var(--border-color); color: var(--text-color); border-radius: 8px; cursor: pointer;" onclick="closeVendaModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" style="flex: 1; padding: 10px; border-radius: 8px; cursor: pointer;" onclick="saveVenda()">Salvar</button>
            </div>
        </form>
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

function loadVendas() {
    api('vendas.listar').then(res => {
        const tbody = document.getElementById('vendasTableBody');
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">Nenhuma venda encontrada.</td></tr>';
            return;
        }
        
        tbody.innerHTML = res.dados.map(v => `
            <tr>
                <td>${escapeHtml(v.id)}</td>
                <td>${escapeHtml(v.cliente_nome || 'Cliente #' + v.cliente_id)}</td>
                <td>${formatMoney(v.valor_emprestimo)}</td>
                <td>${formatMoney(v.total_com_juros)}</td>
                <td>${v.parcelas_pagas || 0}/${v.num_parcelas}</td>
                <td>${formatDate(v.data_inicio)}</td>
                <td>
                    <button class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #3b82f6; color: white; border-radius: 4px; cursor: pointer; border: none;" onclick="viewVenda(${v.id})">Ver</button>
                    <button class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #ef4444; color: white; border-radius: 4px; cursor: pointer; border: none; margin-left: 4px;" onclick="deleteVenda(${v.id})">Deletar</button>
                </td>
            </tr>
        `).join('');
    });
}

function loadClientes() {
    api('clientes.listar').then(res => {
        const select = document.getElementById('vendaCliente');
        select.innerHTML = '<option value="">Selecione um cliente</option>';
        
        if (res.sucesso && res.dados) {
            res.dados.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = c.nome;
                select.appendChild(option);
            });
        }
    });
}

function openVendaModal() {
    document.getElementById('vendaId').value = '';
    document.getElementById('vendaForm').reset();
    document.getElementById('vendaData').value = new Date().toISOString().split('T')[0];
    loadClientes();
    document.getElementById('vendaModal').style.display = 'flex';
}

function closeVendaModal() {
    document.getElementById('vendaModal').style.display = 'none';
}

function viewVenda(id) {
    api('vendas.buscar', { id }).then(res => {
        if (res.sucesso && res.dados) {
            const v = res.dados;
            alert(`Venda #${v.id}\nCliente: ${v.cliente_id}\nValor: R$ ${v.valor_emprestimo}\nParcelas: ${v.parcelas_pagas}/${v.num_parcelas}`);
        } else {
            alert('Erro ao carregar venda');
        }
    });
}

function deleteVenda(id) {
    if (!confirm('Tem certeza que deseja deletar esta venda?')) return;
    
    api('vendas.excluir', { id }).then(res => {
        if (res.sucesso) {
            showNotification('Venda deletada com sucesso!', 'success');
            loadVendas();
        } else {
            alert('Erro: ' + res.mensagem);
        }
    });
}

function saveVenda() {
    const clienteId = parseInt(document.getElementById('vendaCliente').value);
    const valor = parseFloat(document.getElementById('vendaValor').value);
    const juros = parseFloat(document.getElementById('vendaJuros').value) || 0;
    const numParcelas = parseInt(document.getElementById('vendaParcelas').value);
    
    if (!clienteId || !valor || !numParcelas) {
        alert('Preencha todos os campos obrigatórios');
        return;
    }
    
    const jurosValor = (valor * juros) / 100;
    const totalComJuros = valor + jurosValor;
    const valorParcela = totalComJuros / numParcelas;
    
    const dados = {
        clienteId: clienteId,
        valorEmprestimo: valor,
        jurosPct: juros,
        jurosValor: jurosValor,
        totalComJuros: totalComJuros,
        numParcelas: numParcelas,
        tipoVenc: document.getElementById('vendaTipo').value,
        valorParcela: valorParcela,
        dataInicio: document.getElementById('vendaData').value,
        diasMulta: parseInt(document.getElementById('vendaDiasMulta').value),
        valorMulta: parseFloat(document.getElementById('vendaValorMulta').value)
    };
    
    api('vendas.salvar', { dados }).then(res => {
        if (res.sucesso) {
            closeVendaModal();
            loadVendas();
            showNotification('Venda criada com sucesso!', 'success');
        } else {
            alert('Erro: ' + res.mensagem);
        }
    });
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

function formatMoney(value) {
    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('pt-BR');
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

// Fechar modal ao clicar fora
document.getElementById('vendaModal').addEventListener('click', function(e) {
    if (e.target === this) closeVendaModal();
});

// Carregar vendas ao abrir a página
document.addEventListener('DOMContentLoaded', loadVendas);
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
