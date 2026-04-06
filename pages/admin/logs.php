<?php
/**
 * Página de Visualização de Logs (Admin)
 * Versão Profissional Completa (v12.3) - Auditoria Completa
 */

$pageTitle = 'Logs de Ações';
$activePage = 'logs';

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
        <h2 style="font-size: 18px; font-weight: 600;">Logs de Ações do Sistema</h2>
    </div>
    
    <!-- Filtros -->
    <div style="background-color: var(--bg-color); padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid var(--border-color);">
        <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">Filtros de Auditoria</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 12px;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted);">Usuário</label>
                <select id="filtroUsuario" style="width: 100%; padding: 8px; background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 12px;">
                    <option value="">Todos os Usuários</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted);">RUTA</label>
                <select id="filtroRuta" style="width: 100%; padding: 8px; background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 12px;">
                    <option value="">Todas as RUTAS</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted);">Tipo de Ação</label>
                <select id="filtroAcao" style="width: 100%; padding: 8px; background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 12px;">
                    <option value="">Todas as Ações</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted);">Data Inicial</label>
                <input type="date" id="filtroDataInicio" style="width: 100%; padding: 8px; background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 12px;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted);">Data Final</label>
                <input type="date" id="filtroDataFim" style="width: 100%; padding: 8px; background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-color); font-size: 12px;">
            </div>
        </div>
        
        <div style="display: flex; gap: 8px;">
            <button class="btn btn-primary" onclick="aplicarFiltros()" style="padding: 8px 16px; font-size: 12px;">Aplicar Filtros</button>
            <button class="btn" style="padding: 8px 16px; font-size: 12px; background-color: var(--border-color); color: var(--text-color); border-radius: 4px; cursor: pointer; border: none;" onclick="limparFiltros()">Limpar</button>
        </div>
    </div>
    
    <!-- Tabela de Logs -->
    <div class="table-container">
        <table id="logsTable">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>RUTA</th>
                    <th>Ação</th>
                    <th>Tabela</th>
                    <th>Item ID</th>
                    <th>Descrição</th>
                    <th>IP</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="logsTableBody">
                <tr><td colspan="8" style="text-align: center; padding: 20px;">Carregando logs...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para detalhes do log -->
<div id="logModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; max-width: 800px; width: 95%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 18px; font-weight: 600;">Detalhes da Auditoria</h3>
            <button onclick="closeLogModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        
        <div id="logContent" style="font-size: 13px; line-height: 1.6;">
            <!-- Conteúdo preenchido via JS -->
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 16px;">
            <button class="btn btn-primary" onclick="closeLogModal()" style="flex: 1; padding: 10px; border-radius: 8px; cursor: pointer;">Fechar Detalhes</button>
        </div>
    </div>
</div>

<script>
let allLogs = [];

function loadFiltros() {
    apiAdmin('logs.filtros').then(res => {
        if (res.sucesso && res.dados) {
            const selectUsuario = document.getElementById('filtroUsuario');
            res.dados.usuarios.forEach(u => {
                const option = document.createElement('option');
                option.value = u.id;
                option.textContent = u.nome;
                selectUsuario.appendChild(option);
            });
            
            const selectRuta = document.getElementById('filtroRuta');
            res.dados.rutas.forEach(r => {
                const option = document.createElement('option');
                option.value = r.id;
                option.textContent = r.nome;
                selectRuta.appendChild(option);
            });
            
            const selectAcao = document.getElementById('filtroAcao');
            res.dados.acoes.forEach(a => {
                const option = document.createElement('option');
                option.value = a;
                option.textContent = a;
                selectAcao.appendChild(option);
            });
        }
    });
}

function loadLogs() {
    const filtros = {
        usuario_id: document.getElementById('filtroUsuario').value || null,
        ruta_id: document.getElementById('filtroRuta').value || null,
        acao: document.getElementById('filtroAcao').value || null,
        data_inicio: document.getElementById('filtroDataInicio').value || null,
        data_fim: document.getElementById('filtroDataFim').value || null
    };
    
    const tbody = document.getElementById('logsTableBody');
    tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;">Buscando logs no servidor...</td></tr>';
    
    apiAdmin('logs.listar', { filtros }).then(res => {
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;">Nenhum registro de log encontrado para os filtros aplicados.</td></tr>';
            return;
        }
        
        allLogs = res.dados;
        
        tbody.innerHTML = res.dados.map(log => `
            <tr>
                <td style="white-space: nowrap;">${formatDateTime(log.criado_em)}</td>
                <td><strong>${escapeHtml(log.usuario_nome || 'Usuário #' + log.usuario_id)}</strong></td>
                <td>${escapeHtml(log.ruta_nome || '-')}</td>
                <td><span style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">${escapeHtml(log.acao)}</span></td>
                <td>${escapeHtml(log.tabela || '-')}</td>
                <td>${log.item_id || '-'}</td>
                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(log.descricao || '')}">${escapeHtml(log.descricao || '-')}</td>
                <td style="font-size: 11px; color: var(--text-muted);">${escapeHtml(log.ip_address || '-')}</td>
                <td>
                    <button class="btn" style="padding: 4px 10px; font-size: 11px; background-color: #10b981; color: white; border-radius: 4px; cursor: pointer; border: none;" onclick="viewLogDetails(${log.id})">Detalhes</button>
                </td>
            </tr>
        `).join('');
    });
}

function aplicarFiltros() {
    loadLogs();
}

function limparFiltros() {
    document.getElementById('filtroUsuario').value = '';
    document.getElementById('filtroRuta').value = '';
    document.getElementById('filtroAcao').value = '';
    document.getElementById('filtroDataInicio').value = '';
    document.getElementById('filtroDataFim').value = '';
    loadLogs();
}

function viewLogDetails(logId) {
    const log = allLogs.find(l => l.id == logId);
    if (!log) return;
    
    const content = document.getElementById('logContent');
    
    let dadosAntigos = null;
    let dadosNovos = null;
    
    try {
        if (log.dados_antigos) dadosAntigos = JSON.parse(log.dados_antigos);
        if (log.dados_novos) dadosNovos = JSON.parse(log.dados_novos);
    } catch (e) {
        console.error("Erro ao processar JSON de logs", e);
    }
    
    content.innerHTML = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div style="background: var(--bg-color); padding: 12px; border-radius: 8px;">
                <p><strong>Data/Hora:</strong> ${formatDateTime(log.criado_em)}</p>
                <p><strong>Usuário:</strong> ${escapeHtml(log.usuario_nome || 'ID: ' + log.usuario_id)}</p>
                <p><strong>Ação:</strong> ${escapeHtml(log.acao)}</p>
            </div>
            <div style="background: var(--bg-color); padding: 12px; border-radius: 8px;">
                <p><strong>Tabela:</strong> ${escapeHtml(log.tabela || '-')}</p>
                <p><strong>Item ID:</strong> ${log.item_id || '-'}</p>
                <p><strong>IP:</strong> ${escapeHtml(log.ip_address || '-')}</p>
            </div>
        </div>
        
        <div style="margin-bottom: 16px;">
            <strong>Descrição da Ação:</strong>
            <div style="background: var(--bg-color); padding: 12px; border-radius: 8px; margin-top: 5px; border-left: 4px solid #3b82f6;">
                ${escapeHtml(log.descricao || 'Sem descrição detalhada.')}
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: ${dadosAntigos && dadosNovos ? '1fr 1fr' : '1fr'}; gap: 15px;">
            ${dadosAntigos ? `
                <div>
                    <strong style="color: #ef4444;">Dados Anteriores:</strong>
                    <pre style="background-color: #1e293b; color: #f8fafc; padding: 12px; border-radius: 6px; overflow-x: auto; font-size: 11px; margin-top: 8px; max-height: 300px;">${JSON.stringify(dadosAntigos, null, 2)}</pre>
                </div>
            ` : ''}
            
            ${dadosNovos ? `
                <div>
                    <strong style="color: #10b981;">Novos Dados:</strong>
                    <pre style="background-color: #1e293b; color: #f8fafc; padding: 12px; border-radius: 6px; overflow-x: auto; font-size: 11px; margin-top: 8px; max-height: 300px;">${JSON.stringify(dadosNovos, null, 2)}</pre>
                </div>
            ` : ''}
        </div>
        
        <div style="margin-top: 15px;">
            <strong>User Agent:</strong>
            <div style="font-size: 11px; color: var(--text-muted); background: var(--bg-color); padding: 8px; border-radius: 4px; margin-top: 5px;">
                ${escapeHtml(log.user_agent || '-')}
            </div>
        </div>
    `;
    
    document.getElementById('logModal').style.display = 'flex';
}

function closeLogModal() {
    document.getElementById('logModal').style.display = 'none';
}

function formatDateTime(datetime) {
    if (!datetime) return '';
    const d = new Date(datetime);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR');
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

document.getElementById('logModal').addEventListener('click', function(e) {
    if (e.target === this) closeLogModal();
});

document.addEventListener('DOMContentLoaded', function() {
    loadFiltros();
    loadLogs();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
