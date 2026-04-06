<?php
/**
 * Página de Gerenciamento de Multas (Usuário/Operador)
 * Cálculo automático, registro no banco, pagamento separado
 */

$pageTitle = 'Multas';
$activePage = 'multas';

require_once __DIR__ . '/../../includes/header.php';
requireAuth();
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600;">Multas</h2>
        <div style="display: flex; gap: 8px;">
            <select id="filtroStatus" style="padding: 8px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 13px;">
                <option value="">Todos os Status</option>
                <option value="aberto">Abertas</option>
                <option value="pago">Pagas</option>
            </select>
            <select id="filtroVenda" style="padding: 8px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 13px;">
                <option value="">Todas as Vendas</option>
            </select>
            <button class="btn btn-primary" onclick="loadMultas()" style="padding: 8px 16px; font-size: 13px;">Filtrar</button>
        </div>
    </div>

    <!-- Resumo -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Total Multas</div>
            <div id="totalMultas" style="font-size: 20px; font-weight: 700;">-</div>
        </div>
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Valor Total</div>
            <div id="valorTotal" style="font-size: 20px; font-weight: 700; color: #f59e0b;">-</div>
        </div>
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Abertas</div>
            <div id="totalAbertas" style="font-size: 20px; font-weight: 700; color: #ef4444;">-</div>
        </div>
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Pagas</div>
            <div id="totalPagas" style="font-size: 20px; font-weight: 700; color: #10b981;">-</div>
        </div>
    </div>

    <div class="table-container">
        <table id="multasTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Venda</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Motivo</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="multasTableBody">
                <tr><td colspan="8" style="text-align: center; padding: 20px;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Aplicar Multa -->
<div id="multaModal" class="modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background-color:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;max-width:450px;width:90%;">
        <h3 style="font-size:18px;font-weight:600;margin-bottom:16px;">Aplicar Nova Multa</h3>
        <div style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:8px;font-size:14px;font-weight:500;color:var(--text-muted);">Venda *</label>
            <select id="multaVendaId" style="width:100%;padding:10px;background:var(--bg-color);border:1px solid var(--border-color);border-radius:8px;color:var(--text-color);">
                <option value="">Selecione uma venda</option>
            </select>
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:8px;font-size:14px;font-weight:500;color:var(--text-muted);">Valor da Multa *</label>
            <input type="number" id="multaValor" step="0.01" required style="width:100%;padding:10px;background:var(--bg-color);border:1px solid var(--border-color);border-radius:8px;color:var(--text-color);">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:8px;font-size:14px;font-weight:500;color:var(--text-muted);">Motivo</label>
            <textarea id="multaMotivo" style="width:100%;padding:10px;background:var(--bg-color);border:1px solid var(--border-color);border-radius:8px;color:var(--text-color);resize:vertical;min-height:60px;"></textarea>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="button" class="btn" style="flex:1;padding:10px;background:var(--border-color);color:var(--text-color);border-radius:8px;cursor:pointer;border:none;" onclick="closeMultaModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" style="flex:1;padding:10px;border-radius:8px;cursor:pointer;" onclick="saveMulta()">Aplicar</button>
        </div>
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

function loadVendasFiltro() {
    api('vendas.listar').then(res => {
        if (!res.sucesso || !res.dados) return;
        const select = document.getElementById('filtroVenda');
        const multaSelect = document.getElementById('multaVendaId');
        res.dados.forEach(v => {
            const label = 'Venda #' + v.id + ' - ' + (v.cliente_nome || 'Cliente');
            let opt1 = document.createElement('option');
            opt1.value = v.id; opt1.textContent = label;
            select.appendChild(opt1);
            let opt2 = document.createElement('option');
            opt2.value = v.id; opt2.textContent = label;
            multaSelect.appendChild(opt2);
        });
    });
}

function loadMultas() {
    const filtroStatus = document.getElementById('filtroStatus').value;
    const filtroVenda = document.getElementById('filtroVenda').value;

    api('vendas.listar').then(res => {
        if (!res.sucesso || !res.dados) return;
        const vendas = res.dados;

        const promises = vendas
            .filter(v => !filtroVenda || v.id == filtroVenda)
            .map(v => api('multas.listar', { venda_id: v.id }).then(mr => {
                if (mr.sucesso && mr.dados) {
                    return mr.dados.map(m => ({...m, venda: v}));
                }
                return [];
            }));

        Promise.all(promises).then(results => {
            let allMultas = results.flat();

            if (filtroStatus) {
                allMultas = allMultas.filter(m => m.status === filtroStatus);
            }

            // Sort: open first, then by date desc
            allMultas.sort((a, b) => {
                if (a.status === 'aberto' && b.status !== 'aberto') return -1;
                if (b.status === 'aberto' && a.status !== 'aberto') return 1;
                return (b.created_at || '').localeCompare(a.created_at || '');
            });

            // Stats
            const total = allMultas.length;
            const valorTotal = allMultas.reduce((s, m) => s + parseFloat(m.valor || 0), 0);
            const abertas = allMultas.filter(m => m.status === 'aberto').length;
            const pagas = allMultas.filter(m => m.status === 'pago').length;

            document.getElementById('totalMultas').textContent = total;
            document.getElementById('valorTotal').textContent = formatMoney(valorTotal);
            document.getElementById('totalAbertas').textContent = abertas;
            document.getElementById('totalPagas').textContent = pagas;

            const tbody = document.getElementById('multasTableBody');
            if (allMultas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">Nenhuma multa encontrada.</td></tr>';
                return;
            }

            tbody.innerHTML = allMultas.map(m => `<tr>
                <td>${m.id}</td>
                <td>Venda #${m.venda_id}</td>
                <td>${escapeHtml(m.venda.cliente_nome || 'Cliente')}</td>
                <td>${formatMoney(m.valor)}</td>
                <td>${escapeHtml(m.motivo || '-')}</td>
                <td><span style="padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;color:${m.status === 'pago' ? '#10b981' : '#ef4444'};">${m.status}</span></td>
                <td>${formatDate(m.created_at)}</td>
                <td>${m.status === 'aberto' ? `<button class="btn" style="padding:4px 8px;font-size:12px;background:#10b981;color:white;border-radius:4px;cursor:pointer;border:none;" onclick="pagarMulta(${m.venda_id},${m.id},${m.valor})">Pagar</button>` : '-'}</td>
            </tr>`).join('');
        });
    });
}

function pagarMulta(vendaId, multaId, valor) {
    if (!confirm('Confirmar pagamento da multa de ' + formatMoney(valor) + '?')) return;
    api('pagamentos.registrar', { dados: { venda_id: vendaId, multa_id: multaId, valor_pago: valor, forma_pagamento: 'dinheiro' } }).then(res => {
        if (res.sucesso) { showNotification('Multa paga com sucesso!', 'success'); loadMultas(); }
        else alert('Erro: ' + res.mensagem);
    });
}

function openMultaModal() {
    document.getElementById('multaValor').value = '';
    document.getElementById('multaMotivo').value = '';
    document.getElementById('multaVendaId').value = '';
    document.getElementById('multaModal').style.display = 'flex';
}
function closeMultaModal() { document.getElementById('multaModal').style.display = 'none'; }

function saveMulta() {
    const vendaId = document.getElementById('multaVendaId').value;
    const valor = parseFloat(document.getElementById('multaValor').value);
    const motivo = document.getElementById('multaMotivo').value || 'Multa manual';
    if (!vendaId) { alert('Selecione uma venda'); return; }
    if (!valor || valor <= 0) { alert('Informe um valor válido'); return; }
    api('multas.aplicar', { dados: { venda_id: vendaId, valor: valor, motivo: motivo } }).then(res => {
        if (res.sucesso) { closeMultaModal(); showNotification('Multa aplicada!', 'success'); loadMultas(); }
        else alert('Erro: ' + res.mensagem);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}
function formatMoney(value) {
    return 'R$ ' + parseFloat(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('pt-BR');
}
function showNotification(msg, type) {
    const div = document.createElement('div');
    div.style.cssText = `position:fixed;top:20px;right:20px;padding:12px 20px;background-color:${type === 'success' ? '#10b981' : '#ef4444'};color:white;border-radius:4px;z-index:2000;`;
    div.textContent = msg;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

// Fechar modal ao clicar fora
document.getElementById('multaModal').addEventListener('click', function(e) { if (e.target === this) closeMultaModal(); });

document.addEventListener('DOMContentLoaded', function() {
    loadVendasFiltro();
    loadMultas();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
