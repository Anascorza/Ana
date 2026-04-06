# 🔧 Resolvendo Erro 404 no InfinityFree

O erro **404 Not Found** ocorre quando o servidor não consegue encontrar a página solicitada. No InfinityFree, isso geralmente está relacionado ao roteamento de URLs amigáveis.

## ✅ Checklist de Resolução

### 1. Verificar Estrutura de Arquivos na `htdocs`

Acesse o Gerenciador de Arquivos do InfinityFree e verifique se a estrutura está correta:

```
/htdocs/
  ├── index.php              ✅ DEVE estar aqui
  ├── .htaccess              ✅ DEVE estar aqui
  ├── config/
  ├── app/
  ├── pages/
  ├── includes/
  ├── assets/
  ├── auth/
  ├── database.sql
  └── [outros arquivos]
```

**Se os arquivos estiverem em uma subpasta (ex: `/htdocs/rcreditos_project/`):**

1. Abra o Gerenciador de Arquivos
2. Entre na pasta `rcreditos_project`
3. Selecione **TODOS** os arquivos (Ctrl+A)
4. Clique em **Cut** (Cortar)
5. Volte para a pasta `htdocs`
6. Clique em **Paste** (Colar)

### 2. Verificar Permissões de Arquivo

As permissões devem estar configuradas corretamente para que o servidor possa ler os arquivos:

**Arquivos:**
- `index.php` → Permissão **644**
- `.htaccess` → Permissão **644**

**Pastas:**
- `config/` → Permissão **755**
- `app/` → Permissão **755**
- `pages/` → Permissão **755**
- `includes/` → Permissão **755**
- `assets/` → Permissão **755**
- `auth/` → Permissão **755**
- `uploads/` → Permissão **755**

**Como alterar permissões no Gerenciador de Arquivos:**

1. Clique com o botão direito no arquivo/pasta
2. Selecione **Change Permissions** (Alterar Permissões)
3. Defina o valor correto (644 para arquivos, 755 para pastas)
4. Clique em **Change** (Alterar)

### 3. Verificar se o `.htaccess` Está Ativo

O `.htaccess` é responsável por redirecionar as URLs amigáveis para o `index.php`. Se o arquivo não estiver sendo lido, o roteamento não funcionará.

**Teste de Verificação:**

1. Tente acessar: `https://seu-dominio.infinityfree.me/index.php`
   - Se funcionar, o servidor está lendo PHP corretamente.

2. Tente acessar: `https://seu-dominio.infinityfree.me/dashboard`
   - Se retornar 404, o `.htaccess` pode não estar ativo.

### 4. Verificar se `mod_rewrite` Está Ativado

O módulo `mod_rewrite` do Apache é necessário para o `.htaccess` funcionar. Se não estiver ativado, você verá erros 404.

**Se o `mod_rewrite` não estiver ativado:**

1. Acesse o painel de controle do InfinityFree
2. Procure por **Apache Modules** ou **Server Configuration**
3. Ative **mod_rewrite**
4. Reinicie o servidor

Se não conseguir ativar via painel, entre em contato com o suporte do InfinityFree.

### 5. Limpar Cache do Navegador

Às vezes, o navegador cacheia respostas antigas (incluindo erros 404). Limpe o cache:

- **Chrome/Edge:** Ctrl+Shift+Delete
- **Firefox:** Ctrl+Shift+Delete
- **Safari:** Cmd+Shift+Delete

Depois, tente acessar o site novamente.

## 🧪 Testes de Diagnóstico

Tente acessar estes URLs um por um e anote qual funciona e qual retorna erro:

1. `https://seu-dominio.infinityfree.me/` (Raiz)
2. `https://seu-dominio.infinityfree.me/index.php` (Arquivo direto)
3. `https://seu-dominio.infinityfree.me/auth/login` (Rota amigável)
4. `https://seu-dominio.infinityfree.me/dashboard` (Rota amigável)
5. `https://seu-dominio.infinityfree.me/assets/css/main.css` (Asset)

**Resultados esperados:**

- URLs 1, 3, 4 devem redirecionar para o login (ou dashboard se autenticado)
- URL 2 deve exibir a página de login
- URL 5 deve carregar o arquivo CSS

## 🆘 Se Nada Funcionar

Se após seguir todos os passos acima o erro 404 persistir, tente estas alternativas:

### Alternativa 1: Usar URL com `index.php`

Modifique o `config/app.php` para usar URLs com `index.php`:

```php
// Em config/app.php, mude:
define('BASE_URL', '/index.php');
```

Isso fará com que todas as URLs sejam do tipo `https://seu-dominio.infinityfree.me/index.php?route=dashboard`

### Alternativa 2: Contatar Suporte do InfinityFree

Se o `mod_rewrite` não estiver ativado e você não conseguir ativar via painel, entre em contato com o suporte do InfinityFree explicando que precisa de `mod_rewrite` ativado para URLs amigáveis.

### Alternativa 3: Usar Hosting Diferente

Se o InfinityFree não oferecer suporte adequado para URLs amigáveis, considere migrar para um hosting que ofereça melhor suporte a `.htaccess` e `mod_rewrite`.

---

**Versão:** 12.4  
**Última Atualização:** Abril 2026
