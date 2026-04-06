# 🔐 Configuração do Banco de Dados - R.Créditos

## ⚠️ Erro: "Access denied for user ... (using password: NO)"

Este erro significa que o sistema está tentando se conectar ao banco de dados **sem uma senha**, ou que as credenciais no arquivo `config/app.php` ainda estão com os valores padrão.

## 📋 Solução Passo a Passo

### Passo 1: Obter as Credenciais do Banco de Dados na InfinityFree

#### 1.1 Acessar o cPanel

1. Faça login em sua conta InfinityFree: https://www.infinityfree.com
2. Clique em **"Manage"** ao lado de seu domínio
3. Você será redirecionado para o **cPanel**

#### 1.2 Localizar as Credenciais do MySQL

1. No cPanel, procure pela seção **"MySQL Databases"**
2. Você verá uma tabela com seus bancos de dados
3. Procure pela linha com seu banco (ex: `if0_41425409_rcreditos`)
4. **Anote os seguintes dados:**

| Campo | Valor | Exemplo |
|-------|-------|---------|
| **MySQL Hostname** | `DB_HOST` | `sql300.infinityfree.com` |
| **MySQL Database Name** | `DB_NAME` | `if0_41425409_rcreditos` |
| **MySQL User Name** | `DB_USER` | `if0_41425409` |
| **Senha do MySQL** | `DB_PASS` | *(sua senha)* |

> **⚠️ IMPORTANTE:** A senha é a mesma que você usou ao criar o banco de dados. Se esqueceu, você pode redefinir clicando em **"Change Password"** na seção MySQL.

### Passo 2: Editar o Arquivo `config/app.php`

#### 2.1 Acessar o File Manager

1. No cPanel, clique em **"File Manager"**
2. Navegue até a pasta **`public_html`**
3. Abra a pasta **`config`**
4. Clique com botão direito em **`app.php`**
5. Selecione **"Edit"** (ou **"Edit Code"**)

#### 2.2 Localizar as Linhas de Configuração

Procure pelas linhas (aproximadamente linha 33-36):

```php
define('DB_HOST', getenv('DB_HOST') ?: 'sqlXXX.infinityfree.com');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_XXXXXX_db');
define('DB_USER', getenv('DB_USER') ?: 'if0_XXXXXX');
define('DB_PASS', getenv('DB_PASS') ?: 'sua_senha_aqui');
```

#### 2.3 Substituir os Valores

Substitua os valores pelos dados obtidos no Passo 1:

**ANTES:**
```php
define('DB_HOST', getenv('DB_HOST') ?: 'sqlXXX.infinityfree.com');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_XXXXXX_db');
define('DB_USER', getenv('DB_USER') ?: 'if0_XXXXXX');
define('DB_PASS', getenv('DB_PASS') ?: 'sua_senha_aqui');
```

**DEPOIS (exemplo):**
```php
define('DB_HOST', getenv('DB_HOST') ?: 'sql300.infinityfree.com');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_41425409_rcreditos');
define('DB_USER', getenv('DB_USER') ?: 'if0_41425409');
define('DB_PASS', getenv('DB_PASS') ?: 'minha_senha_segura_123');
```

> **⚠️ IMPORTANTE:** 
> - **NÃO deixe `DB_PASS` em branco!** Isso causará o erro "using password: NO"
> - **NÃO use aspas simples dentro da string** (ex: `'senha com 'aspas'` causará erro)
> - Se sua senha contém caracteres especiais, certifique-se de escapá-los corretamente

#### 2.4 Salvar o Arquivo

1. Clique em **"Save"** ou **"Save Changes"**
2. Feche o editor
3. Aguarde alguns segundos para que as mudanças sejam aplicadas

### Passo 3: Testar a Conexão

#### 3.1 Acessar o Sistema

1. Abra seu navegador
2. Digite: `https://seudominio.com` (substitua pelo seu domínio real)
3. Você deve ser redirecionado para a página de login

#### 3.2 Se Ainda Houver Erro

Se o erro persistir, você verá uma mensagem detalhada com instruções. Neste caso:

1. **Verifique se a senha está correta:**
   - Acesse o cPanel
   - Vá em "MySQL Databases"
   - Se necessário, clique em "Change Password" para redefinir a senha
   - Atualize o arquivo `config/app.php` com a nova senha

2. **Verifique se o banco de dados existe:**
   - Acesse o phpMyAdmin (geralmente em `https://seudominio.com/phpmyadmin`)
   - Verifique se o banco de dados está listado
   - Se não estiver, crie um novo banco de dados

3. **Verifique se o schema foi importado:**
   - Abra o banco de dados no phpMyAdmin
   - Verifique se as tabelas (usuarios, rutas, clientes, etc.) existem
   - Se não existirem, importe o arquivo `database.sql`

### Passo 4: Importar o Schema do Banco (Se Necessário)

Se as tabelas não existem no banco de dados:

#### 4.1 Acessar o phpMyAdmin

1. No cPanel, clique em **"phpMyAdmin"**
2. Você será automaticamente logado
3. Clique no banco de dados no painel esquerdo

#### 4.2 Importar o Schema

1. Clique na aba **"Importar"** (ou **"Import"**)
2. Clique em **"Escolher arquivo"** (ou **"Choose File"**)
3. Selecione o arquivo **`database.sql`** do seu projeto
4. Certifique-se de que **"UTF-8"** está selecionado
5. Clique em **"Executar"** (ou **"Go"**)

**Resultado esperado:** As tabelas serão criadas e o usuário admin padrão será inserido.

### Passo 5: Fazer Login

Após confirmar que a conexão está funcionando:

1. Acesse `https://seudominio.com`
2. **Email:** `admin@rcreditos.com`
3. **Senha:** `password`

> **⚠️ IMPORTANTE:** Altere a senha do admin imediatamente após o primeiro login!

## 🔍 Verificação de Segurança

Após configurar o banco de dados:

- [ ] Senha do admin foi alterada
- [ ] Arquivo `config/app.php` tem permissões 644 (leitura apenas)
- [ ] Pasta `uploads` tem permissões 755 (leitura e escrita)
- [ ] Backup do banco de dados foi realizado

## 🆘 Problemas Comuns

### Erro: "SQLSTATE[HY000]: General error: 1030 Got error"

**Causa:** Banco de dados cheio ou problema de espaço em disco

**Solução:**
1. Verifique o espaço disponível no cPanel
2. Verifique o tamanho do banco de dados
3. Contate o suporte da InfinityFree se necessário

### Erro: "SQLSTATE[42000]: Syntax error"

**Causa:** Versão do MySQL incompatível ou erro no schema

**Solução:**
1. Verifique a versão do MySQL no cPanel
2. Reimporte o arquivo `database.sql`
3. Verifique se não há caracteres especiais na senha

### Erro: "Access denied for user ... @ ..."

**Causa:** Credenciais incorretas ou usuário não tem permissões

**Solução:**
1. Verifique as credenciais novamente
2. Redefinir a senha do banco de dados no cPanel
3. Verifique se o usuário tem permissões para o banco de dados

## 📞 Suporte

Se os problemas persistirem:

1. Verifique o arquivo `error_log` no cPanel
2. Consulte a documentação do InfinityFree: https://docs.infinityfree.com
3. Contate o suporte da InfinityFree

---

**Versão:** 2.0.0  
**Última atualização:** 2026-04-02
