# R.Créditos - Guia de Instalação na InfinityFree

Este guia detalha o processo completo de instalação do sistema R.Créditos (Painel Administrativo + Sistema Principal) na hospedagem InfinityFree.

## 📋 Pré-requisitos

- Conta ativa na [InfinityFree](https://www.infinityfree.com)
- Acesso ao painel de controle (cPanel)
- Acesso ao phpMyAdmin
- Conhecimento básico de FTP ou File Manager

## 🚀 Passo 1: Preparar o Banco de Dados

### 1.1 Acessar o phpMyAdmin

1. Faça login no cPanel da InfinityFree
2. Procure por **phpMyAdmin** e clique para abrir
3. Você será automaticamente logado com suas credenciais

### 1.2 Criar um Novo Banco de Dados (Opcional)

Se você não tem um banco de dados criado:

1. Clique em **Novo** no painel esquerdo
2. Digite o nome do banco (ex: `rcreditos`)
3. Selecione **utf8mb4_unicode_ci** como collation
4. Clique em **Criar**

### 1.3 Importar o Schema do Banco de Dados

1. Abra o banco de dados criado
2. Clique na aba **Importar**
3. Clique em **Escolher arquivo** e selecione `database.sql`
4. Certifique-se de que **UTF-8** está selecionado
5. Clique em **Executar**

**Resultado esperado:** As tabelas serão criadas e o usuário admin padrão será inserido.

## 🔑 Passo 2: Configurar as Credenciais do Banco de Dados

### 2.1 Obter as Credenciais

1. No cPanel, procure por **Contas MySQL** ou **Bancos de Dados MySQL**
2. Você verá uma lista com seus bancos de dados
3. Anote:
   - **Host:** `sqlXXX.infinityfree.com` (exemplo: `sql300.infinityfree.com`)
   - **Nome do Banco:** `if0_XXXXXX_db` (exemplo: `if0_41425409_rcreditos`)
   - **Usuário:** `if0_XXXXXX` (exemplo: `if0_41425409`)
   - **Senha:** A senha que você definiu

### 2.2 Atualizar o Arquivo de Configuração

1. Abra o arquivo `config/app.php` em um editor de texto
2. Localize as linhas:

```php
define('DB_HOST', 'sqlXXX.infinityfree.com');  // Substitua aqui
define('DB_NAME', 'if0_XXXXXX_db');            // Substitua aqui
define('DB_USER', 'if0_XXXXXX');               // Substitua aqui
define('DB_PASS', 'sua_senha_aqui');           // Substitua aqui
```

3. Substitua pelos valores obtidos no passo anterior
4. Salve o arquivo

**Exemplo:**

```php
define('DB_HOST', 'sql300.infinityfree.com');
define('DB_NAME', 'if0_41425409_rcreditos');
define('DB_USER', 'if0_41425409');
define('DB_PASS', 'minha_senha_segura');
```

## 📁 Passo 3: Fazer Upload dos Arquivos

### 3.1 Acessar o File Manager

1. No cPanel, clique em **File Manager**
2. Navegue até a pasta **public_html**
3. Esta é a pasta raiz do seu site

### 3.2 Fazer Upload dos Arquivos

**Opção 1: Upload via File Manager (Recomendado)**

1. Selecione todos os arquivos do projeto (exceto `database.sql`)
2. Clique em **Fazer Upload**
3. Aguarde a conclusão

**Opção 2: Upload via FTP**

1. Use um cliente FTP (ex: FileZilla)
2. Conecte-se com as credenciais FTP fornecidas pela InfinityFree
3. Navegue até `public_html`
4. Faça upload de todos os arquivos

### 3.3 Estrutura de Pastas Esperada

Após o upload, a estrutura deve ser:

```
public_html/
├── app/
│   ├── api.php
│   └── api_admin.php
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js
│   │   └── admin.js
│   └── img/
├── auth/
│   ├── login.php
│   └── logout.php
├── config/
│   └── app.php
├── includes/
│   ├── auth.php
│   ├── db.php
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── dashboard_completo.php
│   │   ├── rutas.php
│   │   ├── movimentacoes.php
│   │   └── usuarios.php
│   └── user/
│       ├── dashboard.php
│       ├── clientes.php
│       └── vendas.php
├── uploads/
├── .htaccess
├── index.php
└── database.sql (opcional)
```

## 🔐 Passo 4: Verificar Permissões

### 4.1 Pasta de Uploads

1. Clique com botão direito na pasta `uploads`
2. Selecione **Permissões**
3. Defina para **755** (leitura e escrita)

### 4.2 Arquivo de Configuração

1. Clique com botão direito em `config/app.php`
2. Selecione **Permissões**
3. Defina para **644** (leitura apenas)

## 🧪 Passo 5: Testar a Instalação

### 5.1 Acessar o Sistema

1. Abra seu navegador
2. Digite: `https://seudominio.com` (substitua pelo seu domínio)
3. Você deve ser redirecionado para a página de login

### 5.2 Credenciais Padrão

**Email:** `admin@rcreditos.com`  
**Senha:** `password`

⚠️ **IMPORTANTE:** Altere a senha imediatamente após o primeiro login!

### 5.3 Testar Funcionalidades

1. ✅ Fazer login com o usuário admin
2. ✅ Acessar o Dashboard Administrativo
3. ✅ Criar um novo operador
4. ✅ Criar uma nova ruta
5. ✅ Visualizar os relatórios

## 🛠️ Solução de Problemas

### Erro: "Erro de conexão com o banco de dados"

**Causa:** Credenciais incorretas do banco de dados

**Solução:**
1. Verifique as credenciais em `config/app.php`
2. Confirme que o banco de dados existe no phpMyAdmin
3. Teste a conexão no phpMyAdmin

### Erro: "404 - Página Não Encontrada"

**Causa:** Arquivo `.htaccess` não está funcionando

**Solução:**
1. Verifique se o arquivo `.htaccess` foi feito upload
2. Certifique-se de que `mod_rewrite` está ativado (geralmente está)
3. Tente acessar `index.php` diretamente: `https://seudominio.com/index.php`

### Erro: "Tela em branco"

**Causa:** Erro PHP não exibido

**Solução:**
1. Verifique o arquivo `error_log` no cPanel
2. Ative o modo de debug em `config/app.php`
3. Procure por erros de sintaxe nos arquivos PHP

### Erro: "Permissão negada ao fazer upload"

**Causa:** Pasta `uploads` sem permissões de escrita

**Solução:**
1. Defina permissões da pasta `uploads` para **755**
2. Verifique se o proprietário é o usuário correto

## 📊 Estrutura de Dados

### Tabelas Principais

- **usuarios:** Usuários do sistema (admin e operadores)
- **rutas:** Rotas/carteiras de empréstimo
- **clientes:** Clientes dos operadores
- **vendas:** Empréstimos realizados
- **parcelas:** Parcelas dos empréstimos
- **pagamentos:** Registros de pagamentos
- **multas:** Multas aplicadas
- **movimentacoes:** Histórico de movimentações financeiras
- **logs:** Auditoria de ações do sistema

## 🔄 Backup e Manutenção

### Fazer Backup do Banco de Dados

1. Abra o phpMyAdmin
2. Selecione o banco de dados
3. Clique em **Exportar**
4. Escolha **SQL** como formato
5. Clique em **Executar**

### Fazer Backup dos Arquivos

1. Use o File Manager ou FTP
2. Baixe todos os arquivos da pasta `public_html`
3. Armazene em local seguro

## 📞 Suporte

Se encontrar problemas:

1. Verifique o arquivo `error_log` no cPanel
2. Consulte a documentação do InfinityFree
3. Verifique os logs do servidor

## ✅ Checklist de Instalação

- [ ] Banco de dados criado e schema importado
- [ ] Credenciais do banco atualizadas em `config/app.php`
- [ ] Todos os arquivos feitos upload para `public_html`
- [ ] Permissões configuradas corretamente
- [ ] Arquivo `.htaccess` presente
- [ ] Sistema acessível via navegador
- [ ] Login funcionando com credenciais padrão
- [ ] Senha do admin alterada
- [ ] Backup do banco de dados realizado

---

**Versão:** 2.0.0  
**Compatibilidade:** InfinityFree (MySQL 5.7+, PHP 7.4+)  
**Última atualização:** 2026-04-02
