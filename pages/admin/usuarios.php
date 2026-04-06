<?php
/**
 * Gerenciamento de Operadores (Usuários vinculados ao Admin)
 */

$pageTitle = 'Gerenciar Operadores';
$activePage = 'usuarios';

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
        <h2 style="font-size: 18px; font-weight: 600;">Operadores</h2>
        <button class="btn btn-primary" onclick="openUsuarioModal()" style="padding: 6px 12px; font-size: 12px;">+ Novo Operador</button>
    </div>
    
    <div class="table-container">
        <table id="usuariosTable">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="usuariosTableBody">
                <tr><td colspan="5" style="text-align: center; padding: 20px;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para criar/editar operador -->
<div id="usuarioModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="usuarioModalTitle">Novo Operador</h3>
            <span class="close" onclick="closeUsuarioModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="usuarioForm">
                <input type="hidden" id="usuarioId" value="">
                
                <div class="form-group">
                    <label for="usuarioNome">Nome:</label>
                    <input type="text" id="usuarioNome" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group">
                    <label for="usuarioEmail">Email:</label>
                    <input type="email" id="usuarioEmail" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" id="senhaGroup">
                    <label for="usuarioSenha">Senha:</label>
                    <input type="password" id="usuarioSenha" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <small style="color: #666;">Deixe em branco para manter a senha atual (apenas em edições)</small>
                </div>
                
                <div class="form-group">
                    <label for="usuarioAtivo">
                        <input type="checkbox" id="usuarioAtivo" checked>
                        Ativo
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer" style="display: flex; gap: 10px; justify-content: flex-end; padding: 15px; border-top: 1px solid #eee;">
            <button class="btn btn-secondary" onclick="closeUsuarioModal()" style="padding: 6px 12px;">Cancelar</button>
            <button class="btn btn-primary" onclick="saveUsuario()" style="padding: 6px 12px;">Salvar</button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: #fefefe;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 500px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.modal-header h3 {
    margin: 0;
    font-size: 16px;
}

.close {
    font-size: 28px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    font-size: 14px;
}

.btn {
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background-color: #2563eb;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.btn-danger {
    background-color: #ef4444;
    color: white;
    padding: 4px 8px;
    font-size: 12px;
}

.btn-danger:hover {
    background-color: #dc2626;
}

.btn-edit {
    background-color: #10b981;
    color: white;
    padding: 4px 8px;
    font-size: 12px;
}

.btn-edit:hover {
    background-color: #059669;
}

.status-ativo {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    background-color: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-inativo {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}
</style>

<script>
function api(action, dados = {}) {
    return fetch('<?= BASE_URL ?>/app/api_admin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, ...dados })
    }).then(r => r.json());
}

function loadUsuarios() {
    api('operadores.listar').then(res => {
        const tbody = document.getElementById('usuariosTableBody');
        if (!res.sucesso || !res.dados) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">Nenhum operador encontrado.</td></tr>';
            return;
        }
        
        tbody.innerHTML = res.dados.map(u => `
            <tr>
                <td>${escapeHtml(u.nome)}</td>
                <td>${escapeHtml(u.email)}</td>
                <td>
                    <span class="${u.ativo ? 'status-ativo' : 'status-inativo'}">
                        ${u.ativo ? 'Ativo' : 'Inativo'}
                    </span>
                </td>
                <td>${formatarData(u.criado_em)}</td>
                <td>
                    <button class="btn btn-edit" onclick="editUsuario(${u.id})">Editar</button>
                    <button class="btn btn-danger" onclick="deleteUsuario(${u.id})">Excluir</button>
                </td>
            </tr>
        `).join('');
    });
}

function openUsuarioModal() {
    document.getElementById('usuarioId').value = '';
    document.getElementById('usuarioNome').value = '';
    document.getElementById('usuarioEmail').value = '';
    document.getElementById('usuarioSenha').value = '';
    document.getElementById('usuarioAtivo').checked = true;
    document.getElementById('usuarioModalTitle').textContent = 'Novo Operador';
    document.getElementById('senhaGroup').style.display = 'block';
    document.getElementById('usuarioModal').style.display = 'flex';
}

function closeUsuarioModal() {
    document.getElementById('usuarioModal').style.display = 'none';
}

function editUsuario(id) {
    api('operadores.buscar', { id }).then(res => {
        if (!res.sucesso || !res.dados) {
            alert('Erro ao carregar operador');
            return;
        }
        
        const u = res.dados;
        document.getElementById('usuarioId').value = u.id;
        document.getElementById('usuarioNome').value = u.nome;
        document.getElementById('usuarioEmail').value = u.email;
        document.getElementById('usuarioSenha').value = '';
        document.getElementById('usuarioAtivo').checked = u.ativo == 1;
        document.getElementById('usuarioModalTitle').textContent = 'Editar Operador';
        document.getElementById('senhaGroup').style.display = 'block';
        document.getElementById('usuarioModal').style.display = 'flex';
    });
}

function saveUsuario() {
    const id = document.getElementById('usuarioId').value;
    const dados = {
        id: id ? parseInt(id) : null,
        nome: document.getElementById('usuarioNome').value,
        email: document.getElementById('usuarioEmail').value,
        ativo: document.getElementById('usuarioAtivo').checked ? 1 : 0
    };
    
    const senha = document.getElementById('usuarioSenha').value;
    if (senha) {
        dados.senha = senha;
    }
    
    if (!dados.nome || !dados.email) {
        alert('Preencha todos os campos obrigatórios');
        return;
    }
    
    api('operadores.salvar', { dados }).then(res => {
        if (res.sucesso) {
            closeUsuarioModal();
            loadUsuarios();
            alert('Operador salvo com sucesso!');
        } else {
            alert('Erro: ' + res.mensagem);
        }
    });
}

function deleteUsuario(id) {
    if (!confirm('Tem certeza que deseja excluir este operador?')) return;
    
    api('operadores.excluir', { id }).then(res => {
        if (res.sucesso) {
            loadUsuarios();
            alert('Operador excluído com sucesso!');
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

function formatarData(data) {
    if (!data) return '';
    const d = new Date(data);
    return d.toLocaleDateString('pt-BR');
}

// Carregar operadores ao abrir a página
document.addEventListener('DOMContentLoaded', loadUsuarios);

// Fechar modal ao clicar fora dele
window.onclick = function(event) {
    const modal = document.getElementById('usuarioModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
