-- =====================================================================
--  PLATAFORMA DE DELIVERY  —  Esquema da Base de Dados (MySQL 5.7+/8.0)
--  Português de Moçambique
--  Codificação: utf8mb4 para suportar acentuação e emojis
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `delivery_mz`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `delivery_mz`;

-- ---------------------------------------------------------------------
--  UTILIZADORES (autenticação comum a todos os perfis)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`          VARCHAR(150)  NOT NULL,
  `email`         VARCHAR(150)  NOT NULL,
  `telefone`      VARCHAR(20)   NOT NULL,
  `password_hash` VARCHAR(255)  NOT NULL,
  `perfil`        ENUM('admin','cliente','loja','entregador') NOT NULL,
  `genero`        ENUM('masculino','feminino','outro') DEFAULT NULL,
  `bloqueado`     TINYINT(1)    NOT NULL DEFAULT 0,
  `token_recuperacao`         VARCHAR(64) DEFAULT NULL,
  `token_recuperacao_expira`  DATETIME    DEFAULT NULL,
  `criado_em`     DATETIME      NOT NULL,
  `atualizado_em` DATETIME      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_email` (`email`),
  KEY `idx_usuarios_perfil` (`perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  CATEGORIAS DE LOJA  (Restaurante, Supermercado, Farmácia, ...)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `categorias_loja`;
CREATE TABLE `categorias_loja` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`  VARCHAR(80) NOT NULL,
  `icone` VARCHAR(80) DEFAULT NULL,
  `ativo` TINYINT(1)  NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categorias_loja_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  CLIENTES
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_clientes_usuario` (`usuario_id`),
  CONSTRAINT `fk_clientes_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  ENDEREÇOS DO CLIENTE (múltiplos endereços + GPS)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE `enderecos` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` INT UNSIGNED NOT NULL,
  `etiqueta`   VARCHAR(60)  NOT NULL DEFAULT 'Casa',
  `endereco`   VARCHAR(255) NOT NULL,
  `latitude`   DECIMAL(10,7) DEFAULT NULL,
  `longitude`  DECIMAL(10,7) DEFAULT NULL,
  `principal`  TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_enderecos_cliente` (`cliente_id`),
  CONSTRAINT `fk_enderecos_cliente` FOREIGN KEY (`cliente_id`)
    REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  LOJAS / RESTAURANTES / SUPERMERCADOS
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `lojas`;
CREATE TABLE `lojas` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`    INT UNSIGNED NOT NULL,
  `categoria_id`  INT UNSIGNED NOT NULL,
  `nome_empresa`  VARCHAR(150) NOT NULL,
  `nuit`          VARCHAR(20)  NOT NULL,
  `telefone`      VARCHAR(20)  NOT NULL,
  `email`         VARCHAR(150) DEFAULT NULL,
  `endereco`      VARCHAR(255) NOT NULL,
  `horario`       VARCHAR(120) DEFAULT NULL,
  `taxa_entrega`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `logotipo`      VARCHAR(255) DEFAULT NULL,
  `latitude`      DECIMAL(10,7) DEFAULT NULL,
  `longitude`     DECIMAL(10,7) DEFAULT NULL,
  `ativo`         TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lojas_usuario` (`usuario_id`),
  KEY `idx_lojas_categoria` (`categoria_id`),
  CONSTRAINT `fk_lojas_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lojas_categoria` FOREIGN KEY (`categoria_id`)
    REFERENCES `categorias_loja` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  CATEGORIAS DE PRODUTO (por loja)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `categorias_produto`;
CREATE TABLE `categorias_produto` (
  `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `loja_id` INT UNSIGNED NOT NULL,
  `nome`    VARCHAR(80)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cat_produto_loja` (`loja_id`),
  CONSTRAINT `fk_cat_produto_loja` FOREIGN KEY (`loja_id`)
    REFERENCES `lojas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  PRODUTOS
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `produtos`;
CREATE TABLE `produtos` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `loja_id`             INT UNSIGNED NOT NULL,
  `categoria_produto_id` INT UNSIGNED DEFAULT NULL,
  `nome`                VARCHAR(150)  NOT NULL,
  `descricao`           TEXT          DEFAULT NULL,
  `preco`               DECIMAL(10,2) NOT NULL,
  `preco_promocional`   DECIMAL(10,2) DEFAULT NULL,
  `imagem`              VARCHAR(255)  DEFAULT NULL,
  `stock`               INT           NOT NULL DEFAULT 0,
  `ativo`               TINYINT(1)    NOT NULL DEFAULT 1,
  `criado_em`           DATETIME      NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_produtos_loja` (`loja_id`),
  KEY `idx_produtos_categoria` (`categoria_produto_id`),
  CONSTRAINT `fk_produtos_loja` FOREIGN KEY (`loja_id`)
    REFERENCES `lojas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`categoria_produto_id`)
    REFERENCES `categorias_produto` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  ENTREGADORES
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `entregadores`;
CREATE TABLE `entregadores` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`     INT UNSIGNED NOT NULL,
  -- Dados pessoais
  `bi_passaporte`  VARCHAR(40)  NOT NULL,
  `foto_perfil`    VARCHAR(255) DEFAULT NULL,
  `selfie`         VARCHAR(255) DEFAULT NULL,
  -- Dados do veículo
  `tipo_veiculo`   VARCHAR(40)  DEFAULT NULL,
  `marca`          VARCHAR(60)  DEFAULT NULL,
  `modelo`         VARCHAR(60)  DEFAULT NULL,
  `cor`            VARCHAR(40)  DEFAULT NULL,
  `matricula`      VARCHAR(20)  DEFAULT NULL,
  `ano_fabrico`    SMALLINT     DEFAULT NULL,
  -- Documentação (carta de condução)
  `carta_numero`   VARCHAR(40)  DEFAULT NULL,
  `carta_categoria` VARCHAR(10) DEFAULT NULL,
  `carta_validade` DATE         DEFAULT NULL,
  `carta_frente`   VARCHAR(255) DEFAULT NULL,
  `carta_verso`    VARCHAR(255) DEFAULT NULL,
  `seguro`         VARCHAR(255) DEFAULT NULL,
  -- Estado operacional
  `documentos_aprovados` TINYINT(1) NOT NULL DEFAULT 0,
  `disponivel`     TINYINT(1)   NOT NULL DEFAULT 0,
  `latitude`       DECIMAL(10,7) DEFAULT NULL,
  `longitude`      DECIMAL(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_entregadores_usuario` (`usuario_id`),
  CONSTRAINT `fk_entregadores_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  PEDIDOS
--  Estados do fluxo: criado, recebido, confirmado, preparacao,
--                    entregador_aceite, saiu_entrega, entregue,
--                    finalizado, cancelado
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`      INT UNSIGNED NOT NULL,
  `loja_id`         INT UNSIGNED NOT NULL,
  `entregador_id`   INT UNSIGNED DEFAULT NULL,
  `endereco_entrega` VARCHAR(255) NOT NULL,
  `latitude`        DECIMAL(10,7) DEFAULT NULL,
  `longitude`       DECIMAL(10,7) DEFAULT NULL,
  `estado`          ENUM('criado','recebido','confirmado','preparacao',
                         'entregador_aceite','saiu_entrega','entregue',
                         'finalizado','cancelado') NOT NULL DEFAULT 'criado',
  `subtotal`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `taxa_entrega`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pagamento` ENUM('mpesa','emola','dinheiro','cartao') NOT NULL DEFAULT 'dinheiro',
  `observacoes`     VARCHAR(255) DEFAULT NULL,
  `criado_em`       DATETIME      NOT NULL,
  `atualizado_em`   DATETIME      DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedidos_cliente` (`cliente_id`),
  KEY `idx_pedidos_loja` (`loja_id`),
  KEY `idx_pedidos_entregador` (`entregador_id`),
  KEY `idx_pedidos_estado` (`estado`),
  CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`)
    REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_pedidos_loja` FOREIGN KEY (`loja_id`)
    REFERENCES `lojas` (`id`),
  CONSTRAINT `fk_pedidos_entregador` FOREIGN KEY (`entregador_id`)
    REFERENCES `entregadores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  ITENS DO PEDIDO
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `pedido_itens`;
CREATE TABLE `pedido_itens` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id`   INT UNSIGNED NOT NULL,
  `produto_id`  INT UNSIGNED DEFAULT NULL,
  `nome`        VARCHAR(150)  NOT NULL,
  `preco_unitario` DECIMAL(10,2) NOT NULL,
  `quantidade`  INT           NOT NULL,
  `subtotal`    DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itens_pedido` (`pedido_id`),
  CONSTRAINT `fk_itens_pedido` FOREIGN KEY (`pedido_id`)
    REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_itens_produto` FOREIGN KEY (`produto_id`)
    REFERENCES `produtos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  HISTÓRICO DE ESTADOS DO PEDIDO (rastreamento)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `pedido_historico`;
CREATE TABLE `pedido_historico` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id`   INT UNSIGNED NOT NULL,
  `estado`      VARCHAR(30)  NOT NULL,
  `usuario_id`  INT UNSIGNED DEFAULT NULL,
  `criado_em`   DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hist_pedido` (`pedido_id`),
  CONSTRAINT `fk_hist_pedido` FOREIGN KEY (`pedido_id`)
    REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  PAGAMENTOS
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `pagamentos`;
CREATE TABLE `pagamentos` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id`  INT UNSIGNED NOT NULL,
  `metodo`     ENUM('mpesa','emola','dinheiro','cartao') NOT NULL,
  `valor`      DECIMAL(10,2) NOT NULL,
  `estado`     ENUM('pendente','pago','falhado','reembolsado') NOT NULL DEFAULT 'pendente',
  `referencia` VARCHAR(80)  DEFAULT NULL,
  `criado_em`  DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pagamentos_pedido` (`pedido_id`),
  CONSTRAINT `fk_pagamentos_pedido` FOREIGN KEY (`pedido_id`)
    REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  AVALIAÇÕES (loja + entregador)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `avaliacoes`;
CREATE TABLE `avaliacoes` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id`     INT UNSIGNED NOT NULL,
  `cliente_id`    INT UNSIGNED NOT NULL,
  `loja_id`       INT UNSIGNED NOT NULL,
  `entregador_id` INT UNSIGNED DEFAULT NULL,
  `nota_loja`     TINYINT      DEFAULT NULL,
  `nota_entregador` TINYINT    DEFAULT NULL,
  `comentario`    VARCHAR(255) DEFAULT NULL,
  `criado_em`     DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_avaliacao_pedido` (`pedido_id`),
  CONSTRAINT `fk_avaliacao_pedido` FOREIGN KEY (`pedido_id`)
    REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  FAVORITOS (cliente ↔ loja)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE `favoritos` (
  `cliente_id` INT UNSIGNED NOT NULL,
  `loja_id`    INT UNSIGNED NOT NULL,
  `criado_em`  DATETIME     NOT NULL,
  PRIMARY KEY (`cliente_id`,`loja_id`),
  CONSTRAINT `fk_fav_cliente` FOREIGN KEY (`cliente_id`)
    REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_loja` FOREIGN KEY (`loja_id`)
    REFERENCES `lojas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  PROMOÇÕES
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `promocoes`;
CREATE TABLE `promocoes` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `loja_id`    INT UNSIGNED NOT NULL,
  `produto_id` INT UNSIGNED DEFAULT NULL,
  `descricao`  VARCHAR(150) NOT NULL,
  `preco_promocional` DECIMAL(10,2) DEFAULT NULL,
  `inicio`     DATE DEFAULT NULL,
  `fim`        DATE DEFAULT NULL,
  `ativo`      TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_promo_loja` (`loja_id`),
  CONSTRAINT `fk_promo_loja` FOREIGN KEY (`loja_id`)
    REFERENCES `lojas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
