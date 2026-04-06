/**
 * JavaScript Principal — R.Créditos & Whitesystem
 * Funções auxiliares e consumo de API
 */

// URLs da API (usando BASE_URL absoluto para funcionar em qualquer rota)
const API_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/app/api';
const API_ADMIN_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/app/api_admin';

/**
 * Realiza uma chamada à API (retorna objeto completo {sucesso, dados, mensagem})
 */
function api(action, dados = {}) {
    return fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, ...dados })
    }).then(r => r.json());
}

/**
 * Realiza uma chamada à API Admin (retorna objeto completo {sucesso, dados, mensagem})
 */
function apiAdmin(action, dados = {}) {
    return fetch(API_ADMIN_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, ...dados })
    }).then(r => r.json());
}

/**
 * Exibe uma notificação ao usuário
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 8px;
        background-color: ${type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#3b82f6'};
        color: white;
        font-weight: 600;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Formata um valor monetário
 */
function formatMoney(value) {
    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Formata uma data para o formato brasileiro
 */
function formatDate(date) {
    if (!date) return '';
    const d = new Date(date + 'T00:00:00');
    return d.toLocaleDateString('pt-BR');
}

/**
 * Formata uma data para o formato ISO (YYYY-MM-DD)
 */
function toISODate(date) {
    if (!date) return '';
    const d = new Date(date);
    return d.toISOString().split('T')[0];
}

/**
 * Valida um CPF
 */
function isValidCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11) return false;
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    let sum = 0;
    let remainder;
    
    for (let i = 1; i <= 9; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }
    
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(9, 10))) return false;
    
    sum = 0;
    for (let i = 1; i <= 10; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }
    
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(10, 11))) return false;
    
    return true;
}

/**
 * Máscara para CPF (XXX.XXX.XXX-XX)
 */
function maskCPF(value) {
    value = value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{2})$/, '$1-$2');
    return value;
}

/**
 * Máscara para telefone ((XX) XXXXX-XXXX)
 */
function maskPhone(value) {
    value = value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    return value;
}

/**
 * Máscara para moeda
 */
function maskMoney(value) {
    value = value.replace(/\D/g, '');
    value = (parseInt(value) / 100).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    return 'R$ ' + value;
}

/**
 * Remove máscara de moeda
 */
function unmaskMoney(value) {
    return parseFloat(value.replace(/[^\d,]/g, '').replace(',', '.'));
}

/**
 * Animação de slide in
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
