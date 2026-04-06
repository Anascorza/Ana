# 📊 Instruções: Importar Banco de Dados no InfinityFree

**Data:** 02 de Abril de 2026  
**Versão:** 12.0  
**Compatibilidade:** MySQL 5.6+ (InfinityFree)

---

## 🔐 Credenciais do Seu Banco de Dados

| Informação | Valor |
|---|---|
| **Host MySQL** | `sql302.infinityfree.com` |
| **Porta** | `3306` |
| **Nome de Usuário** | `if0_41425409` |
| **Senha** | `hollaphy` |
| **Nome do Banco** | `if0_41425409_rcreditos` |

⚠️ **IMPORTANTE:** Estas credenciais já estão configuradas no arquivo `config/app.php` do projeto.

---

## 📋 Passo 1: Criar o Banco de Dados

### Opção A: Via cPanel (Recomendado)

1. Acesse o **cPanel** da sua conta InfinityFree
2. Procure por **MySQL Databases** ou **Gerenciador de Banco de Dados**
3. Na seção **Create New Database**, preencha:
   - **Database Name:** `if0_41425409_rcreditos`
4. Clique em **Create Database**

### Opção B: Via phpMyAdmin

1. Acesse `https://sql302.infinityfree.com/phpmyadmin/`
2. Faça login com:
   - **Usuário:** `if0_41425409`
   - **Senha:** `hollaphy`
3. Clique em **New** (lado esquerdo)
4. Digite o nome do banco: `if0_41425409_rcreditos`
5. Clique em **Create**

---

## 📤 Passo 2: Importar o Schema do Banco

### Via phpMyAdmin (Mais Fácil)

1. Acesse `https://sql302.infinityfree.com/phpmyadmin/`
2. Faça login com as credenciais acima
3. Selecione o banco `if0_41425409_rcreditos` (lado esquerdo)
4. Clique na aba **Import**
5. Clique em **Choose File** e selecione o arquivo `database.sql` do projeto
6. Deixe as opções padrão
7. Clique em **Go** ou **Import**

**Resultado esperado:**
```
Import has been successfully finished, 8 queries executed.
```

### Via Linha de Comando (Avançado)

Se tiver acesso SSH:

```bash
mysql -h sql302.infinityfree.com -u if0_41425409 -p if0_41425409_rcreditos < database.sql
```

Quando pedir a senha, digite: `hollaphy`

---

## ✅ Passo 3: Verificar a Importação

### Via phpMyAdmin

1. Acesse phpMyAdmin
2. Selecione o banco `if0_41425409_rcreditos`
3. Você deve ver as seguintes tabelas:

| Tabela | Status |
|---|---|
| `usuarios` | ✅ |
| `rutas` | ✅ |
| `clientes` | ✅ |
| `vendas` | ✅ |
| `parcelas` | ✅ |
| `multas` | ✅ |
| `pagamentos` | ✅ |
| `logs_acoes` | ✅ |
| `movimentacoes` | ✅ |
| `fechamentos` | ✅ |

### Verificar Dados Iniciais

1. Clique na tabela `usuarios`
2. Você deve ver um usuário:
   - **Nome:** Administrador
   - **Email:** admin@rcreditos.com
   - **Tipo:** admin

---

## 🔑 Passo 4: Verificar Credenciais em config/app.php

O arquivo `config/app.php` já está configurado com as credenciais corretas:

```php
define('DB_HOST',    'sql302.infinityfree.com');
define('DB_PORT',    3306);
define('DB_NAME',    'if0_41425409_rcreditos');
define('DB_USER',    'if0_41425409');
define('DB_PASS',    'hollaphy');
define('DB_CHARSET', 'utf8mb4');
```

✅ **Nenhuma alteração necessária!**

---

## 🧪 Passo 5: Testar a Conexão

### Teste 1: Via PHP

1. Crie um arquivo `teste_banco.php` na raiz do projeto:

```php
<?php
require_once 'config/app.php';

try {
    $db = getDB();
    echo "✅ Conexão com banco de dados estabelecida com sucesso!<br>";
    
    // Contar usuários
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "Total de usuários: " . $result['total'] . "<br>";
    
    // Listar tabelas
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Tabelas no banco:<br>";
    foreach ($tables as $table) {
        echo "- " . $table['Tables_in_if0_41425409_rcreditos'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
```

2. Acesse: `https://seu-dominio.com/teste_banco.php`
3. Você deve ver:
   - ✅ Conexão estabelecida
   - Total de usuários: 1
   - Lista de 10 tabelas

### Teste 2: Via phpMyAdmin

1. Acesse phpMyAdmin
2. Selecione o banco
3. Clique em **SQL** (aba superior)
4. Execute a query:
   ```sql
   SELECT COUNT(*) as total FROM usuarios;
   ```
5. Resultado esperado: `1`

---

## 🔄 Passo 6: Fazer Login no Sistema

### Credenciais Padrão

- **Email:** `admin@rcreditos.com`
- **Senha:** `password`

### Procedimento

1. Acesse seu domínio: `https://seu-dominio.com`
2. Você será redirecionado para a página de login
3. Preencha as credenciais acima
4. Clique em **Entrar**
5. Você deve ser redirecionado para o Dashboard do Admin

---

## 🐛 Troubleshooting

### Erro: "Erro de Conexão com o Banco de Dados"

**Causa:** Credenciais incorretas ou banco não criado

**Solução:**
1. Verifique se o banco `if0_41425409_rcreditos` foi criado
2. Verifique as credenciais em `config/app.php`
3. Teste a conexão via phpMyAdmin
4. Verifique se o usuário `if0_41425409` tem permissões no banco

### Erro: "Tabelas não encontradas"

**Causa:** Schema não foi importado

**Solução:**
1. Verifique se o arquivo `database.sql` foi importado
2. Verifique se todas as 10 tabelas aparecem no phpMyAdmin
3. Se não, importe novamente o arquivo `database.sql`

### Erro: "Acesso negado para usuário"

**Causa:** Senha incorreta ou usuário não tem permissões

**Solução:**
1. Verifique a senha em `config/app.php`: `hollaphy`
2. No cPanel, verifique se o usuário `if0_41425409` está vinculado ao banco
3. Se não, adicione o usuário ao banco com todas as permissões

### Erro: "Banco de dados não existe"

**Causa:** Nome do banco incorreto

**Solução:**
1. No phpMyAdmin, verifique o nome exato do banco
2. Atualize em `config/app.php` se necessário
3. O nome deve ser: `if0_41425409_rcreditos`

---

## 📊 Estrutura das Tabelas

### Tabela: usuarios
```sql
- id (INT, PK)
- nome (VARCHAR)
- email (VARCHAR, UNIQUE)
- senha (VARCHAR - bcrypt)
- tipo (ENUM: admin, operador)
- admin_id (INT, FK)
- ativo (TINYINT)
- criado_em (DATETIME)
```

### Tabela: rutas
```sql
- id (INT, PK)
- usuario_id (INT, FK)
- nome (VARCHAR)
- descricao (TEXT)
- capital_base (DECIMAL)
- ativa (TINYINT)
- criado_em (DATETIME)
- atualizado_em (DATETIME)
```

### Tabela: clientes
```sql
- id (INT, PK)
- usuario_id (INT, FK)
- nome (VARCHAR)
- cpf (VARCHAR)
- celular (VARCHAR)
- endereco_res (TEXT)
- obs (TEXT)
- criado_em (DATE)
```

### Tabela: vendas
```sql
- id (INT, PK)
- usuario_id (INT, FK)
- cliente_id (INT, FK)
- valor_emprestimo (DECIMAL)
- juros_pct (DECIMAL)
- total_com_juros (DECIMAL)
- num_parcelas (INT)
- tipo_venc (VARCHAR)
- parcelas_pagas (INT)
- data_inicio (DATE)
- dias_multa (INT)
- valor_multa (DECIMAL)
```

### Tabela: parcelas
```sql
- id (INT, PK)
- venda_id (INT, FK)
- cliente_id (INT, FK)
- numero (INT)
- data_vencimento (DATE)
- valor (DECIMAL)
- valor_pago (DECIMAL)
- status (VARCHAR)
- data_pagamento (DATE)
```

### Tabela: logs_acoes
```sql
- id (INT, PK)
- usuario_id (INT, FK)
- acao (VARCHAR)
- tabela (VARCHAR)
- item_id (INT)
- descricao (TEXT)
- dados_antigos (LONGTEXT - JSON)
- dados_novos (LONGTEXT - JSON)
- ip_address (VARCHAR)
- user_agent (TEXT)
- criado_em (DATETIME)
```

---

## 🔐 Segurança

### 1. Alterar Senha do Admin

Após o primeiro login, altere a senha do admin:

1. Acesse phpMyAdmin
2. Selecione o banco
3. Vá para a tabela `usuarios`
4. Edite o usuário `admin@rcreditos.com`
5. No campo `senha`, use a função:
   ```sql
   PASSWORD('sua_nova_senha')
   ```
   Ou gere um hash bcrypt em: https://bcrypt-generator.com/

### 2. Proteger Arquivo de Configuração

O arquivo `config/app.php` contém as credenciais. Proteja-o:

1. Via cPanel, defina permissões: `644`
2. Via FTP, defina permissões: `644`

### 3. Fazer Backup Regular

1. Via phpMyAdmin, clique em **Export**
2. Selecione o banco
3. Clique em **Go** para fazer download do SQL

---

## ✅ Checklist Final

- [ ] Banco de dados `if0_41425409_rcreditos` criado
- [ ] Arquivo `database.sql` importado
- [ ] 10 tabelas aparecem no phpMyAdmin
- [ ] Usuário admin existe na tabela `usuarios`
- [ ] Credenciais em `config/app.php` estão corretas
- [ ] Teste de conexão passou
- [ ] Login funciona com `admin@rcreditos.com` / `password`
- [ ] Dashboard carrega sem erros
- [ ] Logs de ações aparecem
- [ ] Senha do admin foi alterada

---

## 📞 Suporte

Se encontrar problemas:

1. Verifique o arquivo `error_log` no cPanel
2. Teste a conexão via phpMyAdmin
3. Verifique as credenciais em `config/app.php`
4. Consulte a documentação em `GUIA_IMPLANTACAO_V12.md`

---

**Desenvolvido por:** Manus AI  
**Versão:** 12.0  
**Data:** 02 de Abril de 2026
