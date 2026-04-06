# Guia de Instalação - InfinityFree (R.Créditos)

Este sistema foi otimizado para rodar no ambiente restrito do **InfinityFree**, corrigindo erros comuns de redirecionamento (404) e erros fatais (500).

## 🚀 Passos para Instalação

1.  **Banco de Dados:**
    *   Crie o banco de dados no painel do InfinityFree.
    *   Importe o arquivo `database.sql` (se disponível) via phpMyAdmin.
    *   As credenciais já foram pré-configuradas em `config/app.php` conforme fornecido.

2.  **Upload de Arquivos:**
    *   Envie todo o conteúdo deste pacote para a pasta `htdocs` do seu FTP.
    *   Certifique-se de que o arquivo `.htaccess` foi enviado corretamente.

3.  **Configurações Importantes:**
    *   O sistema utiliza um **Roteador Central** (`index.php`). Isso significa que URLs amigáveis como `/dashboard` ou `/pages/admin/rutas` funcionarão automaticamente.
    *   **Não remova o .htaccess**, ele é vital para o funcionamento das rotas no InfinityFree.

## 🛠️ Correções Aplicadas

*   **currentUser()**: Função implementada globalmente em `config/app.php` para evitar erro 500 nas APIs.
*   **Rotas Amigáveis**: Todos os links no `header.php` foram ajustados para passar pelo roteador, evitando erros 404.
*   **Path Traversal**: Substituído o uso de `realpath()` (que falha no InfinityFree) por uma verificação de caminho manual no `index.php`.
*   **Segurança .htaccess**: Bloqueio de pastas sensíveis (`config/`, `includes/`) ajustado para ser compatível com o servidor Apache do InfinityFree.
*   **Inclusão de Auth**: Garantido que `includes/auth.php` seja carregado em todos os pontos de entrada das APIs.

---
*Otimizado por Manus AI*
