# 🚀 R.Créditos v12.3 - Auditoria e Lógica Real

Esta versão foca na **rastreabilidade total** e na **funcionalidade real** do sistema, garantindo que cada ação seja registrada e que as APIs reflitam o estado real do banco de dados.

## 💎 Principais Melhorias

### 1. 🛡️ Sistema de Logs Centralizado (Auditoria)
- **Registro Automático:** Todas as ações de escrita (CRIAR, EDITAR, EXCLUIR) agora registram automaticamente:
  - Quem realizou a ação (ID e Nome do Usuário).
  - O que foi feito (Tipo de ação e descrição amigável).
  - Onde foi feito (Tabela e ID do item afetado).
  - Dados detalhados (JSON com valores antigos e novos para comparação).
  - Contexto técnico (IP do usuário e User Agent).
- **Interface Administrativa:** Nova página de **Logs de Ações** com filtros avançados por usuário, tipo de ação e período de data.

### 2. ⚙️ APIs com Lógica Real
- **API do Operador (`api.php`):**
  - Dashboard com estatísticas reais do banco.
  - CRUD completo de Clientes e Vendas.
  - Geração automática de parcelas ao criar uma venda.
  - Registro real de pagamentos com atualização de saldo.
- **API do Admin (`api_admin.php`):**
  - Dashboard executivo com visão geral de todos os operadores.
  - Gestão completa de Operadores e Rotas.
  - Registro de movimentações financeiras (Aportes, Retiradas, etc.).
  - Endpoints dedicados para o sistema de logs.

### 3. 🖥️ Frontend Sincronizado
- **Páginas do Operador:** Clientes e Vendas agora utilizam 100% a API, eliminando dados estáticos.
- **Páginas do Admin:** Movimentações e Logs agora são totalmente dinâmicos.
- **Compatibilidade:** Ajuste de nomes de campos (camelCase vs snake_case) para garantir comunicação perfeita entre JS e PHP.

### 4. ☁️ Otimização InfinityFree
- **Banco de Dados:** Schema compatível com MySQL 5.6+ (uso de `LONGTEXT` para JSON).
- **Roteamento:** `.htaccess` e `index.php` otimizados para evitar erros 403/404 na pasta `htdocs`.
- **Caminhos:** Uso consistente de `ROOT_PATH` e `BASE_URL` para evitar quebra de links e assets.

## 📂 Arquivos Modificados/Criados
- `app/api.php`: Implementação de lógica real e logs.
- `app/api_admin.php`: Implementação de lógica real, logs e filtros.
- `pages/admin/logs.php`: Nova interface de auditoria.
- `pages/admin/movimentacoes.php`: Refatorada para usar a API real.
- `config/app.php`: Refinamento da função `logAcao`.
- `database.sql`: Garantia da tabela `logs_acoes`.

---
**Status:** ✅ Pronto para Produção (v12.3)
