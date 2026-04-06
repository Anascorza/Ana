<?php
/**
 * Página de Gerenciamento de Clientes (Usuário/Operador)
 * Versão Profissional Completa (v12) - Funcionalidade Real
 */

$pageTitle = 'Clientes';
$activePage = 'clientes';

require_once __DIR__ . '/../../includes/header.php';
requireAuth();
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600;">Meus Clientes</h2>
        <button class="btn btn-primary" onclick="openClienteModal()" style="padding: 8px 16px; font-size: 14px;">+ Novo Cliente</button>
    </div>
    
    <div class="table-container">
        <table id="clientesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Celular</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="clientesTableBody">
                <tr><td colspan="6" style="text-align: center; padding: 20px;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para criar/editar cliente -->
<div id="clienteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <h3 id="clienteModalTitle" style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Novo Cliente</h3>
        
        <form id="clienteForm">
            <input type="hidden" id="clienteId" value="">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Nome *</label>
                    <input type="text" id="clienteNome" required style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">ID Numérico</label>
                    <input type="text" id="clienteIdNumerico" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">CPF</label>
                    <input type="text" id="clienteCpf" placeholder="000.000.000-00" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">RG</label>
                    <input type="text" id="clienteRg" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Celular</label>
                    <input type="tel" id="clienteCelular" placeholder="(00) 00000-0000" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Nome Comercial</label>
                    <input type="text" id="clienteNomeComercial" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color);">
                </div>
            </div>
            
            <div style="margin-top: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Endereço Residencial</label>
                <textarea id="clienteEnderecoRes" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color); resize: vertical; min-height: 60px;"></textarea>
            </div>
            
            <div style="margin-top: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Endereço Comercial</label>
                <textarea id="clienteEnderecoCom" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color); resize: vertical; min-height: 60px;"></textarea>
            </div>
            
            <div style="margin-top: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-muted);">Observações</label>
                <textarea id="clienteObs" style="width: 100%; padding: 10px; background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-color); resize: vertical; min-height: 60px;"></textarea>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn" style="flex: 1; padding: 10px; background-color: var(--border-color); color: var(--text-color); border-radius: 8px; cursor: pointer;" onclick="closeClienteModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" style="flex: 1; padding: 10px; border-radius: 8px; cursor: pointer;" onclick="saveCliente()">Salvar</button>
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

function loadClientes() {
    api('clientes.listar').then(res => {
        const tbody = document.getElementById('clientesTableBody');
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Nenhum cliente encontrado.</td></tr>';
            return;
        }
        
        tbody.innerHTML = res.dados.map(c => `
            <tr>
                <td>${escapeHtml(c.id_numerico || c.id)}</td>
                <td>${escapeHtml(c.nome)}</td>
                <td>${escapeHtml(c.cpf || '-')}</td>
                <td>${escapeHtml(c.celular || '-')}</td>
                <td>${escapeHtml((c.endereco_res || '-').substring(0, 30))}</td>
                <td>
                    <button class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #10b981; color: white; border-radius: 4px; cursor: pointer; border: none;" onclick="editCliente(${c.id})">Editar</button>
                    <button class="btn" style="padding: 4px 8px; font-size: 12px; background-color: #ef4444; color: white; border-radius: 4px; cursor: pointer; border: none; margin-left: 4px;" onclick="deleteCliente(${c.id})">Deletar</button>
                </td>
            </tr>
        `).join('');
    });
}

function openClienteModal() {
    document.getElementById('clienteId').value = '';
    document.getElementById('clienteForm').reset();
    document.getElementById('clienteModalTitle').textContent = 'Novo Cliente';
    document.getElementById('clienteModal').style.display = 'flex';
}

function closeClienteModal() {
    document.getElementById('clienteModal').style.display = 'none';
}

function editCliente(id) {
    api('clientes.buscar', { id }).then(res => {
        if (!res.sucesso || !res.dados) {
            alert('Erro ao carregar cliente');
            return;
        }
        
        const c = res.dados;
        document.getElementById('clienteId').value = c.id;
        document.getElementById('clienteNome').value = c.nome;
        document.getElementById('clienteIdNumerico').value = c.id_numerico || '';
        document.getElementById('clienteCpf').value = c.cpf || '';
        document.getElementById('clienteRg').value = c.rg || '';
        document.getElementById('clienteCelular').value = c.celular || '';
        document.getElementById('clienteNomeComercial').value = c.nome_comercial || '';
        document.getElementById('clienteEnderecoRes').value = c.endereco_res || '';
        document.getElementById('clienteEnderecoCom').value = c.endereco_com || '';
        document.getElementById('clienteObs').value = c.obs || '';
        document.getElementById('clienteModalTitle').textContent = 'Editar Cliente';
        document.getElementById('clienteModal').style.display = 'flex';
    });
}

function deleteCliente(id) {
    if (!confirm('Tem certeza que deseja deletar este cliente?')) return;
    
    api('clientes.excluir', { id }).then(res => {
        if (res.sucesso) {
            showNotification('Cliente deletado com sucesso!', 'success');
            loadClientes();
        } else {
            alert('Erro: ' + res.mensagem);
        }
    });
}

function saveCliente() {
    const id = document.getElementById('clienteId').value;
    const nome = document.getElementById('clienteNome').value;
    
    if (!nome) {
        alert('Nome é obrigatório');
        return;
    }
    
    const dados = {
        id: id ? parseInt(id) : null,
        nome: nome,
        idNumerico: document.getElementById('clienteIdNumerico').value || null,
        cpf: document.getElementById('clienteCpf').value || null,
        rg: document.getElementById('clienteRg').value || null,
        celular: document.getElementById('clienteCelular').value || null,
        nomeComercial: document.getElementById('clienteNomeComercial').value || null,
        tipoEndereco: 'residencial',
        enderecoRes: document.getElementById('clienteEnderecoRes').value || null,
        enderecoCom: document.getElementById('clienteEnderecoCom').value || null,
        obs: document.getElementById('clienteObs').value || null
    };
    
    api('clientes.salvar', { dados }).then(res => {
        if (res.sucesso) {
            closeClienteModal();
            loadClientes();
            showNotification('Cliente salvo com sucesso!', 'success');
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

// Aplicar máscaras
document.getElementById('clienteCpf').addEventListener('input', function() {
    this.value = maskCPF(this.value);
});

document.getElementById('clienteCelular').addEventListener('input', function() {
    this.value = maskPhone(this.value);
});

function maskCPF(value) {
    return value.replace(/\D/g, '').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2').substring(0, 14);
}

function maskPhone(value) {
    return value.replace(/\D/g, '').replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2').substring(0, 15);
}

// Fechar modal ao clicar fora
document.getElementById('clienteModal').addEventListener('click', function(e) {
    if (e.target === this) closeClienteModal();
});

// Carregar clientes ao abrir a página
document.addEventListener('DOMContentLoaded', loadClientes);
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
