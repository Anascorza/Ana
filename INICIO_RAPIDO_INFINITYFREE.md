# 🚀 Início Rápido - R.Créditos v12 no InfinityFree

**Tempo estimado:** 15 minutos

---

## 📋 Informações do Seu Banco

```
Host:     sql302.infinityfree.com
Usuário:  if0_41425409
Senha:    hollaphy
Banco:    if0_41425409_rcreditos
Porta:    3306
```

✅ **Já configurado em:** `config/app.php`

---

## ⚡ 3 Passos Rápidos

### 1️⃣ Criar Banco de Dados (2 minutos)

**Via cPanel:**
1. Acesse cPanel → MySQL Databases
2. Crie banco: `if0_41425409_rcreditos`
3. Pronto!

**Via phpMyAdmin:**
1. Acesse: https://sql302.infinityfree.com/phpmyadmin/
2. Login: `if0_41425409` / `hollaphy`
3. Clique em **New** → Digite `if0_41425409_rcreditos` → **Create**

### 2️⃣ Importar Banco de Dados (3 minutos)

**Via phpMyAdmin:**
1. Selecione banco `if0_41425409_rcreditos`
2. Aba **Import**
3. Selecione arquivo `database.sql`
4. Clique **Go**

**Resultado esperado:**
```
✅ Import has been successfully finished, 8 queries executed.
```

### 3️⃣ Fazer Upload dos Arquivos (10 minutos)

**Via File Manager (cPanel):**
1. Abra **File Manager**
2. Navegue até `public_html`
3. Faça upload de todos os arquivos do projeto
4. Mantenha a estrutura de pastas

**Via FTP:**
1. Use FileZilla ou WinSCP
2. Conecte ao servidor FTP
3. Navegue até `public_html`
4. Faça upload de todos os arquivos

---

## 🔑 Login Padrão

```
Email:    admin@rcreditos.com
Senha:    password
```

⚠️ **Altere a senha imediatamente após o primeiro login!**

---

## ✅ Verificação Rápida

Após fazer upload, acesse seu domínio:

```
https://seu-dominio.com
```

Você deve ver:
- ✅ Página de login
- ✅ Login funciona
- ✅ Dashboard carrega
- ✅ Sem erros 404 ou 500

---

## 🐛 Se Não Funcionar

### Erro: "Erro de Conexão com o Banco"
- Verifique se o banco foi criado
- Verifique se o arquivo `database.sql` foi importado
- Teste via phpMyAdmin

### Erro: "404 - Página não encontrada"
- Verifique se o arquivo `.htaccess` foi enviado
- Verifique se `mod_rewrite` está ativado
- Contate suporte do InfinityFree

### Erro: "Tela em branco"
- Verifique o arquivo `error_log` no cPanel
- Verifique se todos os arquivos foram enviados
- Verifique permissões de pasta

---

## 📚 Documentação Completa

Para informações detalhadas, consulte:

- **INSTRUCOES_INFINITYFREE_BANCO.md** - Importar banco de dados
- **GUIA_IMPLANTACAO_V12.md** - Guia completo de implantação
- **IMPLEMENTACOES_V12.md** - Detalhes técnicos

---

## 🎉 Pronto!

Seu sistema R.Créditos está funcionando no InfinityFree!

**Próximas etapas:**
1. Alterar senha do admin
2. Criar operadores
3. Criar rotas
4. Começar a usar o sistema

---

**Desenvolvido por:** Manus AI  
**Versão:** 12.0
