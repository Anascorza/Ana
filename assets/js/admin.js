/**
 * JavaScript para Painel Administrativo
 * R.Créditos - Whitesystem
 */

// Formatação de moeda
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

// Formatação de data
function formatarData(data) {
    if (!data) return '';
    const d = new Date(data);
    return d.toLocaleDateString('pt-BR');
}

// Escape HTML para segurança
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Mostrar notificação
function mostrarNotificacao(mensagem, tipo = 'sucesso') {
    const notif = document.createElement('div');
    notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    
    if (tipo === 'sucesso') {
        notif.style.backgroundColor = '#10b981';
    } else if (tipo === 'erro') {
        notif.style.backgroundColor = '#ef4444';
    } else if (tipo === 'aviso') {
        notif.style.backgroundColor = '#f59e0b';
    }
    
    notif.textContent = mensagem;
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.remove();
    }, 3000);
}

// Confirmar ação
function confirmar(mensagem) {
    return confirm(mensagem);
}

// Validar email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Validar CPF
function validarCPF(cpf) {
    if (!cpf) return true; // Opcional
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11) return false;
    
    let soma = 0;
    let resto;
    
    for (let i = 1; i <= 9; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }
    
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;
    
    soma = 0;
    for (let i = 1; i <= 10; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }
    
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(10, 11))) return false;
    
    return true;
}

// Máscara para CPF
function mascaraCPF(cpf) {
    return cpf
        .replace(/\D/g, '')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

// Máscara para telefone
function mascaraTelefone(tel) {
    return tel
        .replace(/\D/g, '')
        .replace(/(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{4})(\d)/, '$1-$2')
        .replace(/(\d{4})-(\d)(\d{4})$/, '$1-$2$3');
}

// Máscara para moeda
function mascaraMoeda(valor) {
    valor = valor.replace(/\D/g, '');
    valor = (valor / 100).toFixed(2);
    return valor.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Exportar tabela para CSV
function exportarCSV(tableId, nomeArquivo) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const linhas = table.querySelectorAll('tr');
    
    linhas.forEach(linha => {
        const colunas = linha.querySelectorAll('td, th');
        const dados = [];
        colunas.forEach(coluna => {
            dados.push('"' + coluna.textContent.trim().replace(/"/g, '""') + '"');
        });
        csv.push(dados.join(','));
    });
    
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = nomeArquivo + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Imprimir tabela
function imprimirTabela(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const janela = window.open('', '', 'height=400,width=600');
    janela.document.write('<html><head><title>Impressão</title>');
    janela.document.write('<style>');
    janela.document.write('table { border-collapse: collapse; width: 100%; }');
    janela.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
    janela.document.write('th { background-color: #f2f2f2; }');
    janela.document.write('</style></head><body>');
    janela.document.write(table.outerHTML);
    janela.document.write('</body></html>');
    janela.document.close();
    janela.print();
}

// Inicializar tooltips
function inicializarTooltips() {
    const elementos = document.querySelectorAll('[data-tooltip]');
    elementos.forEach(el => {
        el.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.cssText = `
                position: absolute;
                background: #333;
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 9999;
                white-space: nowrap;
            `;
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            
            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            });
        });
    });
}

// Inicializar ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    inicializarTooltips();
});

// Fechar modais ao clicar fora
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// Tecla ESC para fechar modais
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modais = document.querySelectorAll('.modal');
        modais.forEach(modal => {
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
            }
        });
    }
});
