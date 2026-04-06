# Guia de Implantação - R.Créditos v12

**Versão:** 12.0  
**Data:** 02 de Abril de 2026  
**Compatibilidade:** InfinityFree, Hostinger, Locaweb, etc.

---

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.6+ ou MariaDB 10.3+
- Extensão PDO MySQL
- Suporte a `.htaccess` (Apache)
- Acesso ao cPanel ou File Manager

---

## 🚀 Passo 1: Preparar o Banco de Dados

### 1.1 Criar Banco de Dados

1. Acesse o **cPanel** da sua hospedagem
2. Vá para **MySQL Databases** ou **Gerenciador de Banco de Dados**
3. Crie um novo banco com o nome desejado (ex: `rcreditos`)
4. Anote o nome do banco

### 1.2 Criar Usuário do Banco

1. Em **MySQL Users**, crie um novo usuário
2. Defina uma senha forte
3. Anote o nome do usuário e a senha

### 1.3 Atribuir Permissões

1. Na seção **Add User to Database**, selecione:
   - Usuário criado
   - Banco criado
2. Clique em "Add"
3. Marque **ALL PRIVILEGES**

### 1.4 Importar Schema

1. Acesse **phpMyAdmin** (geralmente em `seu-dominio.com/phpmyadmin`)
2. Selecione o banco criado
3. Clique na aba **Import**
4. Selecione o arquivo `database.sql` do projeto
5. Clique em **Go** para importar

---

## 🔧 Passo 2: Configurar o Projeto

### 2.1 Editar Credenciais do Banco

1. Abra o arquivo `config/app.php` em um editor de texto
2. Localize as linhas:
   ```php
   define('DB_HOST',    'seu-host-aqui');
   define('DB_NAME',    'seu-banco-aqui');
   define('DB_USER',    'seu-usuario-aqui');
   define('DB_PASS',    'sua-senha-aqui');
   ```

3. Substitua pelos valores do seu banco:
   - **DB_HOST:** Host do banco (geralmente fornecido pelo cPanel)
   - **DB_NAME:** Nome do banco criado
   - **DB_USER:** Nome do usuário criado
   - **DB_PASS:** Senha do usuário

4. Salve o arquivo

### 2.2 Exemplo para InfinityFree

```php
define('DB_HOST',    'sql302.infinityfree.com');
define('DB_PORT',    3306);
define('DB_NAME',    'if0_41425409_rcreditos');
define('DB_USER',    'if0_41425409');
define('DB_PASS',    'sua_senha_aqui');
define('DB_CHARSET', 'utf8mb4');
```

---

## 📤 Passo 3: Fazer Upload dos Arquivos

### 3.1 Via File Manager (Recomendado)

1. Acesse o **File Manager** no cPanel
2. Navegue até a pasta **public_html**
3. Faça upload de todos os arquivos do projeto
4. Mantenha a estrutura de pastas

### 3.2 Via FTP

1. Use um cliente FTP (FileZilla, WinSCP, etc.)
2. Conecte-se ao servidor FTP
3. Navegue até **public_html**
4. Faça upload de todos os arquivos
5. Mantenha a estrutura de pastas

### 3.3 Estrutura Esperada

```
public_html/
├── index.php
├── database.sql
├── .htaccess
├── config/
│   └── app.php
├── app/
│   ├── api.php
│   └── api_admin.php
├── pages/
│   ├── user/
│   │   ├── dashboard.php
│   │   ├── clientes.php
│   │   └── vendas.php
│   └── admin/
│       ├── dashboard_completo.php
│       ├── usuarios.php
│       ├── rutas.php
│       ├── movimentacoes.php
│       └── logs.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── auth.php
│   └── db.php
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── auth/
│   ├── login.php
│   └── logout.php
└── uploads/
```

---

## 🔐 Passo 4: Configurar Permissões

### 4.1 Permissões de Pasta

Via File Manager ou FTP, defina as permissões:

- **uploads/** → 755 (leitura e escrita)
- **config/app.php** → 644 (apenas leitura)

### 4.2 Ativar mod_rewrite

1. Verifique se o arquivo `.htaccess` foi enviado
2. Se não, crie um arquivo `.htaccess` com o conteúdo:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteBase /
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
   </IfModule>
   ```

---

## 🧪 Passo 5: Testar a Instalação

### 5.1 Acessar o Sistema

1. Abra seu navegador
2. Acesse: `https://seu-dominio.com`
3. Você deve ser redirecionado para a página de login

### 5.2 Fazer Login

**Credenciais Padrão:**
- Email: `admin@rcreditos.com`
- Senha: `password`

⚠️ **IMPORTANTE:** Altere a senha imediatamente após o primeiro login!

### 5.3 Testar Funcionalidades

#### Como Admin:
1. ✅ Acesse **Operadores** e crie um novo operador
2. ✅ Acesse **Rotas** e crie uma nova ruta
3. ✅ Acesse **Logs de Ações** e verifique se as ações foram registradas
4. ✅ Aplique filtros nos logs

#### Como Operador:
1. ✅ Faça logout e login com o operador criado
2. ✅ Acesse **Clientes** e crie um novo cliente
3. ✅ Acesse **Vendas** e crie uma nova venda
4. ✅ Verifique se as parcelas foram criadas automaticamente
5. ✅ Faça login como admin e verifique os logs

---

## 🔄 Passo 6: Alterar Senha do Admin

### 6.1 Via Banco de Dados

Se precisar alterar a senha do admin via phpMyAdmin:

1. Acesse **phpMyAdmin**
2. Selecione o banco
3. Vá para a tabela **usuarios**
4. Localize o usuário `admin@rcreditos.com`
5. Clique em **Edit**
6. No campo **senha**, insira a nova senha com a função:
   ```sql
   PASSWORD('sua_nova_senha')
   ```
   Ou use bcrypt online: https://bcrypt-generator.com/

7. Clique em **Go**

### 6.2 Via Interface (Recomendado)

Infelizmente, a interface atual não possui página de alterar senha. Você pode:

1. Criar um novo admin via banco
2. Ou usar a função de reset de senha (se implementada)

---

## 📊 Passo 7: Verificar Logs

### 7.1 Acessar Logs

1. Faça login como admin
2. No menu lateral, clique em **Logs de Ações**
3. Você verá todas as ações do sistema

### 7.2 Usar Filtros

1. Selecione um **Usuário** (operador)
2. Selecione um **Tipo de Ação**
3. Defina um intervalo de **Data**
4. Clique em **Filtrar**

### 7.3 Visualizar Detalhes

1. Clique em **Ver** em qualquer log
2. Uma modal exibirá:
   - Informações gerais
   - Dados anteriores (JSON)
   - Dados novos (JSON)
   - IP e User-Agent

---

## 🐛 Troubleshooting

### Erro: "Erro de Conexão com o Banco"

**Causa:** Credenciais incorretas ou banco não acessível

**Solução:**
1. Verifique as credenciais em `config/app.php`
2. Teste a conexão no phpMyAdmin
3. Verifique se o banco foi criado
4. Verifique se o usuário tem permissões

### Erro: "404 - Página não encontrada"

**Causa:** Arquivo `.htaccess` não foi enviado ou mod_rewrite não está ativado

**Solução:**
1. Verifique se `.htaccess` existe em `public_html`
2. Tente acessar diretamente: `seu-dominio.com/index.php`
3. Se funcionar, o problema é o `.htaccess`
4. Contate o suporte da hospedagem para ativar mod_rewrite

### Erro: "Tela em branco"

**Causa:** Erro de PHP não exibido

**Solução:**
1. Ative debug em `config/app.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Verifique o arquivo `error_log` no cPanel
3. Procure por erros de sintaxe

### Erro: "Não autenticado"

**Causa:** Sessão expirou ou não iniciou

**Solução:**
1. Faça login novamente
2. Limpe cookies do navegador
3. Verifique se `session_start()` está em `config/app.php`

### Logs não aparecem

**Causa:** Tabela `logs_acoes` não existe ou função não está sendo chamada

**Solução:**
1. Verifique se a tabela existe no phpMyAdmin
2. Se não existir, importe `database.sql` novamente
3. Verifique se `logAcao()` está sendo chamada em cada ação

---

## 🔒 Segurança Pós-Implantação

### 1. Alterar Senha do Admin
```
Email: admin@rcreditos.com
Senha: ALTERAR IMEDIATAMENTE
```

### 2. Configurar HTTPS
- Ativar SSL/TLS no cPanel
- Redirecionar HTTP para HTTPS

### 3. Fazer Backup
- Banco de dados (semanal)
- Arquivos do projeto (mensal)

### 4. Monitorar Logs
- Revisar logs regularmente
- Investigar atividades suspeitas

### 5. Atualizar Dependências
- Manter PHP atualizado
- Manter MySQL atualizado

---

## 📈 Próximas Etapas

### 1. Criar Operadores
1. Acesse **Administração > Operadores**
2. Clique em **+ Novo Operador**
3. Preencha os dados
4. Clique em **Salvar**

### 2. Criar Rotas
1. Acesse **Administração > Rotas**
2. Clique em **+ Nova Ruta**
3. Selecione o operador
4. Preencha os dados
5. Clique em **Salvar**

### 3. Operadores Começam a Trabalhar
1. Operadores fazem login
2. Criam clientes
3. Registram vendas
4. Registram pagamentos
5. Admin acompanha via logs

---

## 📞 Suporte

### Problemas Técnicos
- Verifique o arquivo `error_log` no cPanel
- Consulte a documentação em `IMPLEMENTACOES_V12.md`
- Teste em diferentes navegadores

### Dúvidas de Funcionalidade
- Consulte o `README_PROJETO.md`
- Revise os logs de ações
- Teste as APIs diretamente

### Relatório de Bugs
- Documente o erro exato
- Anote os passos para reproduzir
- Inclua prints de tela

---

## ✅ Checklist de Implantação

- [ ] Banco de dados criado
- [ ] Usuário do banco criado
- [ ] Permissões atribuídas
- [ ] Schema importado
- [ ] Credenciais configuradas em `config/app.php`
- [ ] Arquivos enviados via FTP/File Manager
- [ ] Estrutura de pastas mantida
- [ ] Permissões de pasta configuradas (755 para uploads)
- [ ] `.htaccess` enviado
- [ ] mod_rewrite ativado
- [ ] Sistema acessível em `seu-dominio.com`
- [ ] Login funciona com credenciais padrão
- [ ] Criar novo operador funciona
- [ ] Criar nova ruta funciona
- [ ] Logs de ações aparecem
- [ ] Filtros de logs funcionam
- [ ] Senha do admin alterada
- [ ] HTTPS configurado
- [ ] Backup inicial feito
- [ ] Documentação revisada

---

## 🎉 Parabéns!

Seu sistema R.Créditos está pronto para uso em produção!

**Próximas recomendações:**
1. Fazer backup semanal do banco
2. Revisar logs regularmente
3. Manter sistema atualizado
4. Treinar operadores
5. Monitorar performance

---

**Desenvolvido por:** Manus AI  
**Versão:** 12.0  
**Data:** 02 de Abril de 2026
