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

<!-- Modal de Detalhes da Venda -->
<div id="detalheModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background-color:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;"></div>

<!-- Modal de Pagamento -->
<div id="pagamentoModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background-color:rgba(0,0,0,0.5);z-index:1100;align-items:center;justify-content:center;">
    <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;max-width:400px;width:90%;">
        <h3 style="font-size:18px;font-weight:600;margin-bottom:16px;">Registrar Pagamento</h3>
        <div style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:8px;font-size:14px;font-weight:500;color:var(--text-muted);">Valor *</label>
            <input type="number" id="pagValor" step="0.01" required style="width:100%;padding:10px;background:var(--bg-color);border:1px solid var(--border-color);border-radius:8px;color:var(--text-color);">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:8px;font-size:14px;font-weight:500;color:var(--text-muted);">Forma de Pagamento</label>
            <select id="pagForma" style="width:100%;padding:10px;background:var(--bg-color);border:1px solid var(--border-color);border-radius:8px;color:var(--text-color);">
                <option value="dinheiro">Dinheiro</option>
                <option value="pix">PIX</option>
                <option value="transferencia">Transferência</option>
                <option value="cartao">Cartão</option>
            </select>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="button" class="btn" style="flex:1;padding:10px;background:var(--border-color);color:var(--text-color);border-radius:8px;cursor:pointer;" onclick="closePagamentoModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" style="flex:1;padding:10px;border-radius:8px;cursor:pointer;" onclick="savePagamento()">Confirmar</button>
        </div>
    </div>
</div>

<!-- Modal de Multa -->
<div id="multaModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background-color:rgba(0,0,0,0.5);z-index:1100;align-items:center;justify-content:center;">
    <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;max-width:400px;width:90%;">
        <h3 style="font-size:18px;font-weight:600;margin-bottom:16px;">Aplicar Multa</h3>
        <div style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:8px;font-size:14px;font-weight:500;color:var(--text-muted);">Valor da Multa *</label>
            <input type="number" id="multaValor" step="0.01" required style="width:100%;padding:10px;background:var(--bg-color);border:1px solid var(--border-color);border-radius:8px;color:var(--text-color);">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:8px;font-size:14px;font-weight:500;color:var(--text-muted);">Motivo</label>
            <textarea id="multaMotivo" style="width:100%;padding:10px;background:var(--bg-color);border:1px solid var(--border-color);border-radius:8px;color:var(--text-color);resize:vertical;min-height:60px;"></textarea>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="button" class="btn" style="flex:1;padding:10px;background:var(--border-color);color:var(--text-color);border-radius:8px;cursor:pointer;" onclick="closeMultaModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" style="flex:1;padding:10px;border-radius:8px;cursor:pointer;" onclick="saveMulta()">Aplicar</button>
        </div>
    </div>
</div>

<script>
function loadVendas() {
    api('vendas.listar').then(res => {
        const tbody = document.getElementById('vendasTableBody');
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">Nenhuma venda encontrada.</td></tr>';
            return;
        }
        
        tbody.innerHTML = res.dados.map(v => `
            <tr>
                <td>${escapeHtml(String(v.id))}</td>
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
        if (!res.sucesso || !res.dados) { alert('Erro ao carregar venda'); return; }
        const v = res.dados;
        
        let html = `<div style="max-width:700px;width:95%;background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;max-height:90vh;overflow-y:auto;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:18px;font-weight:600;">Venda #${v.id}</h3>
                <button onclick="closeDetalheModal()" style="background:none;border:none;font-size:24px;cursor:pointer;color:var(--text-muted);">&times;</button>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                <div style="background:var(--bg-color);padding:12px;border-radius:8px;"><strong>Valor:</strong> ${formatMoney(v.valor_emprestimo)}</div>
                <div style="background:var(--bg-color);padding:12px;border-radius:8px;"><strong>Total c/ Juros:</strong> ${formatMoney(v.total_com_juros)}</div>
                <div style="background:var(--bg-color);padding:12px;border-radius:8px;"><strong>Parcelas:</strong> ${v.parcelas_pagas||0}/${v.num_parcelas}</div>
                <div style="background:var(--bg-color);padding:12px;border-radius:8px;"><strong>Início:</strong> ${formatDate(v.data_inicio)}</div>
            </div>
            <h4 style="margin-bottom:8px;">Parcelas</h4>
            <div id="detalheParcelasBody" style="margin-bottom:16px;">Carregando...</div>
            <h4 style="margin-bottom:8px;">Multas</h4>
            <div id="detalheMultasBody" style="margin-bottom:16px;">Carregando...</div>
            <div style="display:flex;gap:8px;margin-top:16px;">
                <button class="btn btn-primary" style="padding:8px 16px;" onclick="openPagamentoModal(${v.id})">Registrar Pagamento</button>
                <button class="btn" style="padding:8px 16px;background:#f59e0b;color:white;border:none;border-radius:4px;cursor:pointer;" onclick="openMultaModal(${v.id})">Aplicar Multa</button>
            </div>
        </div>`;
        
        document.getElementById('detalheModal').innerHTML = html;
        document.getElementById('detalheModal').style.display = 'flex';
        
        // Load parcelas
        api('parcelas.listar', { venda_id: v.id }).then(pr => {
            const el = document.getElementById('detalheParcelasBody');
            if (!pr.sucesso || !pr.dados || pr.dados.length === 0) { el.innerHTML = '<p style="color:var(--text-muted);">Nenhuma parcela.</p>'; return; }
            const today = new Date().toISOString().split('T')[0];
            el.innerHTML = '<table style="width:100%;font-size:13px;"><thead><tr><th>#</th><th>Vencimento</th><th>Valor</th><th>Status</th><th>Ação</th></tr></thead><tbody>' +
                pr.dados.map(p => {
                    let status = p.status;
                    let color = '#6b7280';
                    if (p.status === 'pago') { color = '#10b981'; }
                    else if (p.data_vencimento < today && p.status !== 'pago') { status = 'atrasado'; color = '#ef4444'; }
                    else if (p.status === 'futuro') { color = '#3b82f6'; }
                    const canPay = p.status !== 'pago';
                    return `<tr>
                        <td>${p.numero}</td>
                        <td>${formatDate(p.data_vencimento)}</td>
                        <td>${formatMoney(p.valor)}</td>
                        <td><span style="padding:2px 8px;border-radius:3px;font-size:11px;font-weight:600;color:${color};">${status}</span></td>
                        <td>${canPay ? `<button class="btn" style="padding:2px 6px;font-size:11px;background:#10b981;color:white;border:none;border-radius:3px;cursor:pointer;" onclick="pagarParcela(${v.id},${p.id},${p.valor})">Pagar</button>` : '-'}</td>
                    </tr>`;
                }).join('') + '</tbody></table>';
        });
        
        // Load multas
        api('multas.listar', { venda_id: v.id }).then(mr => {
            const el = document.getElementById('detalheMultasBody');
            if (!mr.sucesso || !mr.dados || mr.dados.length === 0) { el.innerHTML = '<p style="color:var(--text-muted);">Nenhuma multa.</p>'; return; }
            el.innerHTML = '<table style="width:100%;font-size:13px;"><thead><tr><th>Valor</th><th>Motivo</th><th>Status</th><th>Ação</th></tr></thead><tbody>' +
                mr.dados.map(m => {
                    const canPay = m.status === 'aberto';
                    return `<tr>
                        <td>${formatMoney(m.valor)}</td>
                        <td>${escapeHtml(m.motivo || '-')}</td>
                        <td><span style="color:${m.status === 'pago' ? '#10b981' : '#ef4444'};">${m.status}</span></td>
                        <td>${canPay ? `<button class="btn" style="padding:2px 6px;font-size:11px;background:#10b981;color:white;border:none;border-radius:3px;cursor:pointer;" onclick="pagarMulta(${v.id},${m.id},${m.valor})">Pagar</button>` : '-'}</td>
                    </tr>`;
                }).join('') + '</tbody></table>';
        });
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
    if (text === null || text === undefined) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
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

// === Detalhe Modal ===
function closeDetalheModal() {
    document.getElementById('detalheModal').style.display = 'none';
}

function pagarParcela(vendaId, parcelaId, valor) {
    if (!confirm('Confirmar pagamento de ' + formatMoney(valor) + '?')) return;
    api('pagamentos.registrar', { dados: { venda_id: vendaId, parcela_id: parcelaId, valor_pago: valor, forma_pagamento: 'dinheiro' } }).then(res => {
        if (res.sucesso) { showNotification('Parcela paga com sucesso!', 'success'); viewVenda(vendaId); loadVendas(); }
        else alert('Erro: ' + res.mensagem);
    });
}

function pagarMulta(vendaId, multaId, valor) {
    if (!confirm('Confirmar pagamento da multa de ' + formatMoney(valor) + '?')) return;
    api('pagamentos.registrar', { dados: { venda_id: vendaId, multa_id: multaId, valor_pago: valor, forma_pagamento: 'dinheiro' } }).then(res => {
        if (res.sucesso) { showNotification('Multa paga com sucesso!', 'success'); viewVenda(vendaId); loadVendas(); }
        else alert('Erro: ' + res.mensagem);
    });
}

// === Pagamento Modal ===
let currentVendaIdPag = null;
function openPagamentoModal(vendaId) {
    currentVendaIdPag = vendaId;
    document.getElementById('pagValor').value = '';
    document.getElementById('pagForma').value = 'dinheiro';
    document.getElementById('pagamentoModal').style.display = 'flex';
}
function closePagamentoModal() { document.getElementById('pagamentoModal').style.display = 'none'; }
function savePagamento() {
    const valor = parseFloat(document.getElementById('pagValor').value);
    const forma = document.getElementById('pagForma').value;
    if (!valor || valor <= 0) { alert('Informe um valor válido'); return; }
    api('pagamentos.registrar', { dados: { venda_id: currentVendaIdPag, valor_pago: valor, forma_pagamento: forma } }).then(res => {
        if (res.sucesso) { closePagamentoModal(); showNotification('Pagamento registrado!', 'success'); viewVenda(currentVendaIdPag); loadVendas(); }
        else alert('Erro: ' + res.mensagem);
    });
}

// === Multa Modal ===
let currentVendaIdMulta = null;
function openMultaModal(vendaId) {
    currentVendaIdMulta = vendaId;
    document.getElementById('multaValor').value = '';
    document.getElementById('multaMotivo').value = '';
    document.getElementById('multaModal').style.display = 'flex';
}
function closeMultaModal() { document.getElementById('multaModal').style.display = 'none'; }
function saveMulta() {
    const valor = parseFloat(document.getElementById('multaValor').value);
    const motivo = document.getElementById('multaMotivo').value || 'Multa manual';
    if (!valor || valor <= 0) { alert('Informe um valor válido'); return; }
    api('multas.aplicar', { dados: { venda_id: currentVendaIdMulta, valor: valor, motivo: motivo } }).then(res => {
        if (res.sucesso) { closeMultaModal(); showNotification('Multa aplicada!', 'success'); viewVenda(currentVendaIdMulta); }
        else alert('Erro: ' + res.mensagem);
    });
}

// Fechar modais ao clicar fora
document.getElementById('vendaModal').addEventListener('click', function(e) { if (e.target === this) closeVendaModal(); });
document.getElementById('detalheModal').addEventListener('click', function(e) { if (e.target === this) closeDetalheModal(); });
document.getElementById('pagamentoModal').addEventListener('click', function(e) { if (e.target === this) closePagamentoModal(); });
document.getElementById('multaModal').addEventListener('click', function(e) { if (e.target === this) closeMultaModal(); });

// Carregar vendas ao abrir a página
document.addEventListener('DOMContentLoaded', loadVendas);
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
