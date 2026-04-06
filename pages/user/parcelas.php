<?php
/**
 * Página de Gerenciamento de Parcelas (Usuário/Operador)
 * Listagem completa com cálculo de atraso e integração com multas
 */

$pageTitle = 'Parcelas';
$activePage = 'parcelas';

require_once __DIR__ . '/../../includes/header.php';
requireAuth();
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600;">Minhas Parcelas</h2>
        <div style="display: flex; gap: 8px;">
            <select id="filtroStatus" style="padding: 8px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 13px;">
                <option value="">Todos os Status</option>
                <option value="futuro">Futuras</option>
                <option value="atrasado">Atrasadas</option>
                <option value="pago">Pagas</option>
            </select>
            <select id="filtroVenda" style="padding: 8px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 13px;">
                <option value="">Todas as Vendas</option>
            </select>
            <button class="btn btn-primary" onclick="loadParcelas()" style="padding: 8px 16px; font-size: 13px;">Filtrar</button>
        </div>
    </div>
    
    <!-- Resumo -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Total Parcelas</div>
            <div id="totalParcelas" style="font-size: 20px; font-weight: 700;">-</div>
        </div>
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Pagas</div>
            <div id="totalPagas" style="font-size: 20px; font-weight: 700; color: #10b981;">-</div>
        </div>
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Atrasadas</div>
            <div id="totalAtrasadas" style="font-size: 20px; font-weight: 700; color: #ef4444;">-</div>
        </div>
        <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; color: var(--text-muted);">Valor Atrasado</div>
            <div id="valorAtrasado" style="font-size: 20px; font-weight: 700; color: #ef4444;">-</div>
        </div>
    </div>
    
    <div class="table-container">
        <table id="parcelasTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Venda</th>
                    <th>Cliente</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Dias Atraso</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="parcelasTableBody">
                <tr><td colspan="8" style="text-align: center; padding: 20px;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function loadVendasFiltro() {
    api('vendas.listar').then(res => {
        const select = document.getElementById('filtroVenda');
        if (res.sucesso && res.dados) {
            res.dados.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = 'Venda #' + v.id + ' - ' + (v.cliente_nome || 'Cliente');
                select.appendChild(opt);
            });
        }
    });
}

function loadParcelas() {
    const filtroStatus = document.getElementById('filtroStatus').value;
    const filtroVenda = document.getElementById('filtroVenda').value;
    
    api('vendas.listar').then(res => {
        if (!res.sucesso || !res.dados) return;
        
        const vendas = res.dados;
        const vendasMap = {};
        vendas.forEach(v => { vendasMap[v.id] = v; });
        
        // Load all parcelas for each venda
        const promises = vendas
            .filter(v => !filtroVenda || v.id == filtroVenda)
            .map(v => api('parcelas.listar', { venda_id: v.id }).then(pr => {
                if (pr.sucesso && pr.dados) {
                    return pr.dados.map(p => ({...p, venda: v}));
                }
                return [];
            }));
        
        Promise.all(promises).then(results => {
            let allParcelas = results.flat();
            const today = new Date().toISOString().split('T')[0];
            
            // Calculate delay days
            allParcelas = allParcelas.map(p => {
                let diasAtraso = 0;
                let statusReal = p.status;
                if (p.status !== 'pago' && p.data_vencimento < today) {
                    const venc = new Date(p.data_vencimento);
                    const now = new Date();
                    diasAtraso = Math.floor((now - venc) / (1000 * 60 * 60 * 24));
                    statusReal = 'atrasado';
                }
                return {...p, diasAtraso, statusReal};
            });
            
            // Filter by status
            if (filtroStatus) {
                if (filtroStatus === 'atrasado') {
                    allParcelas = allParcelas.filter(p => p.statusReal === 'atrasado');
                } else {
                    allParcelas = allParcelas.filter(p => p.status === filtroStatus);
                }
            }
            
            // Sort: atrasadas first, then by date
            allParcelas.sort((a, b) => {
                if (a.statusReal === 'atrasado' && b.statusReal !== 'atrasado') return -1;
                if (b.statusReal === 'atrasado' && a.statusReal !== 'atrasado') return 1;
                return (a.data_vencimento || '').localeCompare(b.data_vencimento || '');
            });
            
            // Stats
            const totalParcelas = allParcelas.length;
            const pagas = allParcelas.filter(p => p.status === 'pago').length;
            const atrasadas = allParcelas.filter(p => p.statusReal === 'atrasado').length;
            const valorAtrasado = allParcelas.filter(p => p.statusReal === 'atrasado').reduce((s, p) => s + parseFloat(p.valor || 0), 0);
            
            document.getElementById('totalParcelas').textContent = totalParcelas;
            document.getElementById('totalPagas').textContent = pagas;
            document.getElementById('totalAtrasadas').textContent = atrasadas;
            document.getElementById('valorAtrasado').textContent = formatMoney(valorAtrasado);
            
            const tbody = document.getElementById('parcelasTableBody');
            if (allParcelas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">Nenhuma parcela encontrada.</td></tr>';
                return;
            }
            
            tbody.innerHTML = allParcelas.map(p => {
                let color = '#6b7280';
                if (p.status === 'pago') color = '#10b981';
                else if (p.statusReal === 'atrasado') color = '#ef4444';
                else if (p.status === 'futuro') color = '#3b82f6';
                const canPay = p.status !== 'pago';
                
                return `<tr>
                    <td>${p.numero}</td>
                    <td>Venda #${p.venda_id}</td>
                    <td>${escapeHtml(p.venda.cliente_nome || 'Cliente #' + p.cliente_id)}</td>
                    <td>${formatDate(p.data_vencimento)}</td>
                    <td>${formatMoney(p.valor)}</td>
                    <td style="color:${p.diasAtraso > 0 ? '#ef4444' : 'inherit'}; font-weight:${p.diasAtraso > 0 ? '600' : 'normal'};">${p.diasAtraso > 0 ? p.diasAtraso + ' dias' : '-'}</td>
                    <td><span style="padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;color:${color};">${p.statusReal}</span></td>
                    <td>
                        ${canPay ? `<button class="btn" style="padding:4px 8px;font-size:12px;background:#10b981;color:white;border-radius:4px;cursor:pointer;border:none;" onclick="pagarParcela(${p.venda_id},${p.id},${p.valor})">Pagar</button>` : '-'}
                        ${p.statusReal === 'atrasado' ? `<button class="btn" style="padding:4px 8px;font-size:12px;background:#f59e0b;color:white;border-radius:4px;cursor:pointer;border:none;margin-left:4px;" onclick="aplicarMultaAtraso(${p.venda_id},${p.id},${p.diasAtraso},${p.valor})">Multar</button>` : ''}
                    </td>
                </tr>`;
            }).join('');
        });
    });
}

function pagarParcela(vendaId, parcelaId, valor) {
    if (!confirm('Confirmar pagamento de ' + formatMoney(valor) + '?')) return;
    api('pagamentos.registrar', { dados: { venda_id: vendaId, parcela_id: parcelaId, valor_pago: valor, forma_pagamento: 'dinheiro' } }).then(res => {
        if (res.sucesso) { showNotification('Parcela paga com sucesso!', 'success'); loadParcelas(); }
        else alert('Erro: ' + res.mensagem);
    });
}

function aplicarMultaAtraso(vendaId, parcelaId, diasAtraso, valorParcela) {
    const valorMulta = parseFloat((valorParcela * 0.02 * diasAtraso).toFixed(2));
    if (!confirm('Aplicar multa automática de ' + formatMoney(valorMulta) + ' (' + diasAtraso + ' dias de atraso)?')) return;
    api('multas.aplicar', { dados: { venda_id: vendaId, valor: valorMulta, motivo: 'Multa por atraso de ' + diasAtraso + ' dias - Parcela ref: ' + parcelaId } }).then(res => {
        if (res.sucesso) { showNotification('Multa aplicada com sucesso!', 'success'); loadParcelas(); }
        else alert('Erro: ' + res.mensagem);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
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
    div.style.cssText = `position:fixed;top:20px;right:20px;padding:12px 20px;background-color:${type === 'success' ? '#10b981' : '#ef4444'};color:white;border-radius:4px;z-index:2000;`;
    div.textContent = msg;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    loadVendasFiltro();
    loadParcelas();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
