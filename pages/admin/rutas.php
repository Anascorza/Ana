<?php
/**
 * Página de Gerenciamento de Rutas (Admin)
 * Versão Profissional Completa (v12) - Funcionalidade Real
 */

$pageTitle = 'Rutas';
$activePage = 'rutas';

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
        <h2 style="font-size: 18px; font-weight: 600;">Gerenciar Rutas</h2>
        <button class="btn btn-primary" onclick="openRutaModal()" style="padding: 8px 16px; font-size: 14px;">+ Nova Ruta</button>
    </div>
    
    <div class="table-container">
        <table id="rutasTable">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Operador</th>
                    <th>Descrição</th>
                    <th>Capital Base</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="rutasTableBody">
                <tr><td colspan="6" style="text-align: center; padding: 20px;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para criar/editar ruta -->
<div id="rutaModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <h3 id="rutaModalTitle" style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Nova Ruta</h3>
        
        <form id="rutaForm">
            <input type="hidden" id="rutaId" value="">
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Operador *</label>
                <select id="rutaOperador" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                    <option value="">Carregando operadores...</option>
                </select>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Nome *</label>
                <input type="text" id="rutaNome" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Descrição</label>
                <textarea id="rutaDescricao" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color); resize: vertical; min-height: 80px;"></textarea>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Capital Base</label>
                <input type="number" id="rutaCapital" step="0.01" value="0" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">
                    <input type="checkbox" id="rutaAtiva" checked>
                    Ativa
                </label>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn" style="flex: 1; padding: 10px; background-color: var(--border-color); color: var(--text-color); border-radius: 8px; cursor: pointer;" onclick="closeRutaModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" style="flex: 1; padding: 10px; border-radius: 8px; cursor: pointer;" onclick="saveRuta()">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function loadOperadores() {
    apiAdmin('operadores.listar').then(res => {
        const select = document.getElementById('rutaOperador');
        select.innerHTML = '<option value="">Selecione um operador</option>';
        
        if (res.sucesso && res.dados) {
            res.dados.forEach(op => {
                const option = document.createElement('option');
                option.value = op.id;
                option.textContent = op.nome;
                select.appendChild(option);
            });
        }
    });
}

function loadRutas() {
    apiAdmin('rutas.listar').then(res => {
        const tbody = document.getElementById('rutasTableBody');
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Nenhuma ruta encontrada.</td></tr>';
            return;
        }
        
        tbody.innerHTML = res.dados.map(r => `
            <tr>
                <td>${escapeHtml(r.nome)}</td>
                <td>${escapeHtml(r.usuario_nome || 'Operador #' + r.usuario_id)}</td>
                <td>${escapeHtml((r.descricao || '-').substring(0, 30))}</td>
                <td>${formatMoney(r.capital_base)}</td>
                <td>
                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background-color: ${r.ativa ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; color: ${r.ativa ? '#10b981' : '#ef4444'};">
                        ${r.ativa ? 'Ativa' : 'Inativa'}
                    </span>
                </td>
                <td>
                    <button class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #10b981; color: white; border-radius: 4px; cursor: pointer; border: none;" onclick="editRuta(${r.id})">Editar</button>
                    <button class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #ef4444; color: white; border-radius: 4px; cursor: pointer; border: none; margin-left: 4px;" onclick="deleteRuta(${r.id})">Deletar</button>
                </td>
            </tr>
        `).join('');
    });
}

function openRutaModal() {
    document.getElementById('rutaId').value = '';
    document.getElementById('rutaForm').reset();
    document.getElementById('rutaModalTitle').textContent = 'Nova Ruta';
    document.getElementById('rutaAtiva').checked = true;
    loadOperadores();
    document.getElementById('rutaModal').style.display = 'flex';
}

function closeRutaModal() {
    document.getElementById('rutaModal').style.display = 'none';
}

function editRuta(id) {
    apiAdmin('rutas.buscar', { id }).then(res => {
        if (!res.sucesso || !res.dados) {
            alert('Erro ao carregar ruta');
            return;
        }
        
        const r = res.dados;
        document.getElementById('rutaId').value = r.id;
        document.getElementById('rutaOperador').value = r.usuario_id;
        document.getElementById('rutaNome').value = r.nome;
        document.getElementById('rutaDescricao').value = r.descricao || '';
        document.getElementById('rutaCapital').value = r.capital_base || 0;
        document.getElementById('rutaAtiva').checked = r.ativa == 1;
        document.getElementById('rutaModalTitle').textContent = 'Editar Ruta';
        loadOperadores();
        document.getElementById('rutaModal').style.display = 'flex';
    });
}

function deleteRuta(id) {
    if (!confirm('Tem certeza que deseja deletar esta ruta?')) return;
    
    apiAdmin('rutas.excluir', { id }).then(res => {
        if (res.sucesso) {
            showNotification('Ruta deletada com sucesso!', 'success');
            loadRutas();
        } else {
            alert('Erro: ' + res.mensagem);
        }
    });
}

function saveRuta() {
    const id = document.getElementById('rutaId').value;
    const usuarioId = parseInt(document.getElementById('rutaOperador').value);
    const nome = document.getElementById('rutaNome').value;
    
    if (!usuarioId || !nome) {
        alert('Preencha todos os campos obrigatórios');
        return;
    }
    
    const dados = {
        id: id ? parseInt(id) : null,
        usuario_id: usuarioId,
        nome: nome,
        descricao: document.getElementById('rutaDescricao').value || null,
        capital_base: parseFloat(document.getElementById('rutaCapital').value) || 0,
        ativa: document.getElementById('rutaAtiva').checked ? 1 : 0
    };
    
    apiAdmin('rutas.salvar', { dados }).then(res => {
        if (res.sucesso) {
            closeRutaModal();
            loadRutas();
            showNotification('Ruta salva com sucesso!', 'success');
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
document.getElementById('rutaModal').addEventListener('click', function(e) {
    if (e.target === this) closeRutaModal();
});

// Carregar rutas ao abrir a página
document.addEventListener('DOMContentLoaded', loadRutas);
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
