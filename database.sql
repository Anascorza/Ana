-- ═══════════════════════════════════════════════════════════════════
--  NOVO BANCO DE DADOS — R.CRÉDITOS & WHITESYSTEM
--  Compatível com InfinityFree (MySQL 5.6+)
-- ═══════════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- ── Tabela: usuarios ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`        INT           NOT NULL AUTO_INCREMENT,
  `nome`      VARCHAR(150)  NOT NULL,
  `email`     VARCHAR(191)  NOT NULL,
  `senha`     VARCHAR(255)  NOT NULL,
  `tipo`      ENUM('admin','operador') NOT NULL DEFAULT 'operador',
  `admin_id`  INT           DEFAULT NULL,
  `ativo`     TINYINT(1)    NOT NULL DEFAULT 1,
  `criado_em` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin padrão — senha: admin123 (bcrypt)
INSERT IGNORE INTO `usuarios` (`nome`, `email`, `senha`, `tipo`) VALUES
('Administrador', 'admin@rcreditos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ── Tabela: rutas ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rutas` (
  `id`            INT           NOT NULL AUTO_INCREMENT,
  `usuario_id`    INT           NOT NULL,
  `nome`          VARCHAR(255)  NOT NULL,
  `descricao`     TEXT          DEFAULT NULL,
  `capital_base`  DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `ativa`         TINYINT(1)    NOT NULL DEFAULT 1,
  `criado_em`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: clientes ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `clientes` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `usuario_id`     INT          NOT NULL,
  `nome`           VARCHAR(255) NOT NULL,
  `id_numerico`    VARCHAR(50)  DEFAULT NULL,
  `nome_comercial` VARCHAR(255) DEFAULT NULL,
  `cpf`            VARCHAR(20)  DEFAULT NULL,
  `rg`             VARCHAR(20)  DEFAULT NULL,
  `celular`        VARCHAR(30)  DEFAULT NULL,
  `tipo_endereco`  VARCHAR(20)  DEFAULT 'residencial',
  `endereco_res`   TEXT         DEFAULT NULL,
  `endereco_com`   TEXT         DEFAULT NULL,
  `obs`            TEXT         DEFAULT NULL,
  `foto_thumb`     LONGTEXT     DEFAULT NULL,
  `criado_em`      DATE         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_cli` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: vendas ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `vendas` (
  `id`                 INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`         INT           NOT NULL,
  `cliente_id`         INT           NOT NULL,
  `valor_emprestimo`   DECIMAL(12,2) NOT NULL,
  `juros_pct`          DECIMAL(8,4)  DEFAULT 0,
  `juros_valor`        DECIMAL(12,2) DEFAULT 0,
  `total_com_juros`    DECIMAL(12,2) NOT NULL,
  `num_parcelas`       INT           NOT NULL,
  `tipo_venc`          VARCHAR(20)   DEFAULT 'mensal',
  `parcelas_pagas`     INT           DEFAULT 0,
  `saldo_parcial_pago` DECIMAL(12,2) DEFAULT 0,
  `valor_parcela`      DECIMAL(12,2) DEFAULT 0,
  `data_inicio`        DATE          DEFAULT NULL,
  `dias_multa`         INT           DEFAULT 2,
  `valor_multa`        DECIMAL(12,2) DEFAULT 0,
  `ultimo_pagamento`   DATE          DEFAULT NULL,
  `ultima_forma_pag`   VARCHAR(30)   DEFAULT NULL,
  `edicoes_restantes`  INT           DEFAULT 1,
  `criado_em`          DATE          DEFAULT NULL,
  KEY `idx_usuario_ven` (`usuario_id`),
  KEY `idx_cliente_ven` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: parcelas ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `parcelas` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `venda_id`        INT           NOT NULL,
  `cliente_id`      INT           NOT NULL,
  `numero`          INT           NOT NULL,
  `data_vencimento` DATE          DEFAULT NULL,
  `valor`           DECIMAL(12,2) DEFAULT 0,
  `valor_pago`      DECIMAL(12,2) DEFAULT NULL,
  `status`          VARCHAR(30)   DEFAULT 'futuro',
  `tipo`            VARCHAR(30)   DEFAULT 'normal',
  `motivo`          TEXT          DEFAULT NULL,
  `data_pagamento`  DATE          DEFAULT NULL,
  `forma_pagamento` VARCHAR(30)   DEFAULT NULL,
  `atualizado_em`   DATE          DEFAULT NULL,
  KEY `idx_venda_par` (`venda_id`),
  KEY `idx_cliente_par` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: multas ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `multas` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `venda_id`       INT           NOT NULL,
  `cliente_id`     INT           NOT NULL,
  `parcela_ref`    INT           DEFAULT NULL,
  `valor`          DECIMAL(12,2) NOT NULL,
  `motivo`         TEXT          DEFAULT NULL,
  `origem`         VARCHAR(20)   DEFAULT 'auto',
  `status`         VARCHAR(20)   DEFAULT 'aberto',
  `criado_em`      DATE          DEFAULT NULL,
  `pago_em`        DATE          DEFAULT NULL,
  `convertido_em`  DATE          DEFAULT NULL,
  KEY `idx_venda_mul` (`venda_id`),
  KEY `idx_cliente_mul` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: pagamentos ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pagamentos` (
  `id`                    INT AUTO_INCREMENT PRIMARY KEY,
  `venda_id`              INT           NOT NULL,
  `cliente_id`            INT           NOT NULL,
  `tipo`                  VARCHAR(30)   DEFAULT 'pagamento',
  `valor_pago`            DECIMAL(12,2) NOT NULL,
  `forma_pagamento`       VARCHAR(30)   DEFAULT NULL,
  `motivo`                TEXT          DEFAULT NULL,
  `pagas_int_antes`       INT           DEFAULT 0,
  `pagas_int_agora`       INT           DEFAULT 0,
  `saldo_parcial_antes`   DECIMAL(12,2) DEFAULT 0,
  `saldo_parcial_depois`  DECIMAL(12,2) DEFAULT 0,
  `quantidade_parcelas`   INT           DEFAULT 0,
  `data`                  DATE          NOT NULL,
  `data_pagamento`        DATETIME      DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_venda_pag` (`venda_id`),
  KEY `idx_cliente_pag` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: logs_acoes ──────────────────────────────────────────────
-- Tabela simplificada para compatibilidade máxima (Sem tipo JSON, usando LONGTEXT)
CREATE TABLE IF NOT EXISTS `logs_acoes` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`    INT NOT NULL,
  `admin_id`      INT DEFAULT NULL,
  `ruta_id`       INT DEFAULT NULL,
  `acao`          VARCHAR(100) NOT NULL,
  `tabela`        VARCHAR(50) DEFAULT NULL,
  `item_id`       INT DEFAULT NULL,
  `descricao`     TEXT,
  `dados_antigos` LONGTEXT DEFAULT NULL,
  `dados_novos`   LONGTEXT DEFAULT NULL,
  `ip_address`    VARCHAR(45) DEFAULT NULL,
  `user_agent`    TEXT,
  `criado_em`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_usuario_log` (`usuario_id`),
  KEY `idx_admin_log` (`admin_id`),
  KEY `idx_ruta_log` (`ruta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: movimentacoes ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `movimentacoes` (
  `id`              INT NOT NULL AUTO_INCREMENT,
  `ruta_id`         INT NOT NULL,
  `usuario_id`      INT NOT NULL,
  `tipo`            VARCHAR(50) NOT NULL,
  `valor`           DECIMAL(14,2) NOT NULL,
  `descricao`       TEXT DEFAULT NULL,
  `cliente_nome`    VARCHAR(255) DEFAULT NULL,
  `referencia_id`   VARCHAR(64) DEFAULT NULL,
  `data_mov`        DATE NOT NULL,
  `criado_em`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ruta_mov` (`ruta_id`),
  KEY `idx_usuario_mov` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela: fechamentos ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `fechamentos` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`      INT NOT NULL,
  `data_fechamento` DATE NOT NULL,
  `criado_em`       DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_data_fechamento` (`data_fechamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET foreign_key_checks = 1;
