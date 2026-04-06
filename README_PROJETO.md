# R.Créditos - Sistema de Gestão de Empréstimos

**Versão:** 2.0.0  
**Compatibilidade:** PHP 7.4+, MySQL 5.7+, MariaDB 10.3+  
**Hospedagem:** InfinityFree, Hostinger, Locaweb, etc.

## 📋 Descrição

R.Créditos é um sistema completo de gestão de empréstimos com dois níveis de acesso:

- **Painel Administrativo (Sistema Branco):** Supervisão geral, gerenciamento de operadores, relatórios financeiros
- **Sistema de Operador:** Gestão de clientes, vendas, parcelas e pagamentos

## 🎯 Funcionalidades Principais

### Painel Administrativo

- ✅ Dashboard com resumo executivo
- ✅ Gerenciamento de operadores
- ✅ Gerenciamento de rotas/carteiras
- ✅ Visualização de clientes por operador
- ✅ Edição de vendas e clientes
- ✅ Relatórios de lucro por ruta
- ✅ Status de clientes (pagos, pendentes, atrasados)
- ✅ Histórico de movimentações
- ✅ Cálculo automático de lucro (juros + multas)

### Sistema de Operador

- ✅ Dashboard com saldo da ruta
- ✅ Cadastro e gerenciamento de clientes
- ✅ Registro de vendas/empréstimos
- ✅ Controle de parcelas
- ✅ Registro de pagamentos
- ✅ Aplicação de multas
- ✅ Histórico de movimentações

## 🏗️ Arquitetura

### Estrutura de Pastas

```
projeto/
├── app/                      # APIs REST
│   ├── api.php              # API para operadores
│   └── api_admin.php        # API para administradores
├── assets/                  # Recursos estáticos
│   ├── css/
│   │   ├── main.css         # Estilos gerais
│   │   └── admin.css        # Estilos do admin
│   ├── js/
│   │   ├── main.js          # JavaScript geral
│   │   └── admin.js         # JavaScript do admin
│   └── img/                 # Imagens
├── auth/                    # Autenticação
│   ├── login.php            # Página de login
│   └── logout.php           # Logout
├── config/                  # Configurações
│   └── app.php              # Configurações globais
├── includes/                # Includes compartilhados
│   ├── auth.php             # Funções de autenticação
│   ├── db.php               # Conexão com banco
│   ├── header.php           # Cabeçalho HTML
│   └── footer.php           # Rodapé HTML
├── pages/                   # Páginas
│   ├── admin/               # Páginas do admin
│   │   ├── dashboard_completo.php
│   │   ├── usuarios.php
│   │   ├── rutas.php
│   │   └── movimentacoes.php
│   └── user/                # Páginas do operador
│       ├── dashboard.php
│       ├── clientes.php
│       └── vendas.php
├── uploads/                 # Uploads de usuários
├── .htaccess                # Rewrite rules
├── index.php                # Ponto de entrada
├── database.sql             # Schema do banco
└── INSTALACAO_INFINITYFREE.md
```

## 🔐 Segurança

### Implementado

- ✅ Autenticação por sessão
- ✅ Hash de senha com bcrypt
- ✅ Prepared statements (proteção contra SQL injection)
- ✅ Validação de entrada
- ✅ Escape de saída HTML
- ✅ Proteção contra path traversal
- ✅ CORS headers
- ✅ X-Frame-Options (clickjacking)
- ✅ X-Content-Type-Options (MIME sniffing)
- ✅ Content-Security-Policy

### Recomendações

- 🔒 Use HTTPS em produção
- 🔒 Altere a senha do admin padrão imediatamente
- 🔒 Faça backups regulares
- 🔒 Mantenha o PHP atualizado
- 🔒 Configure firewall apropriadamente
- 🔒 Use senhas fortes para o banco de dados

## 📊 Banco de Dados

### Tabelas Principais

| Tabela | Descrição |
|--------|-----------|
| `usuarios` | Usuários (admin e operadores) |
| `rutas` | Rotas/carteiras de empréstimo |
| `clientes` | Clientes dos operadores |
| `vendas` | Empréstimos realizados |
| `parcelas` | Parcelas dos empréstimos |
| `pagamentos` | Registros de pagamentos |
| `multas` | Multas aplicadas |
| `movimentacoes` | Histórico de movimentações |
| `logs` | Auditoria de ações |

### Relacionamentos

```
usuarios (admin)
    ├── usuarios (operadores) via admin_id
    │   ├── rutas
    │   │   └── movimentacoes
    │   └── clientes
    │       ├── vendas
    │       │   ├── parcelas
    │       │   ├── pagamentos
    │       │   └── multas
    │       └── fotos
```

## 🚀 Instalação

### Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou MariaDB 10.3
- Extensão PDO MySQL
- Suporte a `.htaccess` (Apache)

### Passos

1. **Preparar o banco de dados:**
   - Criar banco de dados
   - Importar `database.sql`

2. **Configurar credenciais:**
   - Editar `config/app.php`
   - Adicionar host, nome, usuário e senha do banco

3. **Fazer upload dos arquivos:**
   - Upload para `public_html` via FTP ou File Manager

4. **Configurar permissões:**
   - Pasta `uploads`: 755
   - Arquivo `config/app.php`: 644

5. **Acessar o sistema:**
   - `https://seudominio.com`
   - Login: `admin@rcreditos.com`
   - Senha: `password`

Para instruções detalhadas, veja [INSTALACAO_INFINITYFREE.md](INSTALACAO_INFINITYFREE.md)

## 🔄 Fluxo de Dados

### Operador cria uma venda:

```
1. Operador acessa /pages/user/vendas.php
2. Preenche formulário de venda
3. JavaScript envia POST para /app/api.php (action: vendas.salvar)
4. API valida dados e insere em vendas + parcelas
5. API retorna sucesso
6. JavaScript atualiza tabela
```

### Admin visualiza relatório:

```
1. Admin acessa /pages/admin/dashboard_completo.php
2. JavaScript carrega dados via /app/api_admin.php
3. API executa queries complexas com JOINs
4. Retorna dados agregados
5. JavaScript renderiza gráficos e tabelas
```

## 📱 Responsividade

- ✅ Desktop (1920px+)
- ✅ Tablet (768px - 1024px)
- ✅ Mobile (320px - 767px)

## 🎨 Design

- **Cores:** Azul (#3b82f6), Verde (#10b981), Vermelho (#ef4444), Laranja (#f59e0b)
- **Tipografia:** Noto Sans (Google Fonts)
- **Ícones:** SVG inline
- **Layout:** Sidebar + Main content

## 🔗 APIs

### API de Operador (`/app/api.php`)

```javascript
// Listar clientes
POST /app/api.php
{ action: 'clientes.listar' }

// Salvar venda
POST /app/api.php
{ action: 'vendas.salvar', dados: { clienteId, valorEmprestimo, ... } }

// Registrar pagamento
POST /app/api.php
{ action: 'pagamentos.salvar', dados: { vendaId, valorPago, ... } }
```

### API de Admin (`/app/api_admin.php`)

```javascript
// Dashboard resumo
POST /app/api_admin.php
{ action: 'dashboard.resumo' }

// Listar operadores
POST /app/api_admin.php
{ action: 'operadores.listar' }

// Relatório de lucro por ruta
POST /app/api_admin.php
{ action: 'relatorio.lucroRutas' }
```

## 🐛 Troubleshooting

### Erro: "Erro de conexão com o banco"
- Verifique credenciais em `config/app.php`
- Confirme que o banco existe
- Teste no phpMyAdmin

### Erro: "404 - Página não encontrada"
- Verifique se `.htaccess` foi feito upload
- Confirme que `mod_rewrite` está ativado
- Teste acesso direto a `index.php`

### Erro: "Tela em branco"
- Verifique `error_log` no cPanel
- Ative debug em `config/app.php`
- Procure por erros de sintaxe

## 📞 Suporte

Para suporte, consulte:
- Documentação: [INSTALACAO_INFINITYFREE.md](INSTALACAO_INFINITYFREE.md)
- Logs: `error_log` no cPanel
- Banco de dados: phpMyAdmin

## 📄 Licença

Este projeto é fornecido como está, para uso pessoal e comercial.

## 🔄 Histórico de Versões

### v2.0.0 (2026-04-02)
- ✨ Painel administrativo completo
- ✨ API REST para admin
- ✨ Gerenciamento de operadores
- ✨ Relatórios financeiros
- ✨ Compatibilidade total com InfinityFree
- 🔧 Correção de bugs de roteamento
- 🔧 Melhorias de segurança

### v1.0.0 (2026-03-15)
- 🎉 Versão inicial
- Sistema de operador funcional
- Gestão de clientes e vendas

---

**Desenvolvido por:** Manus AI  
**Última atualização:** 2026-04-02
