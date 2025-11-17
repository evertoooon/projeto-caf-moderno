-- ===============================
-- MODELO FÍSICO · Café Moderno
-- MySQL 8.0 · InnoDB · utf8mb4
-- ===============================



SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- Ordem de drop para respeitar FKs
DROP TABLE IF EXISTS pedido_item;
DROP TABLE IF EXISTS pedido;
DROP TABLE IF EXISTS reserva;
DROP TABLE IF EXISTS cardapioitem;
DROP TABLE IF EXISTS mesa;
DROP TABLE IF EXISTS cliente;

-- Cliente (1)───(N) Reserva; (1)───(N) Pedido
CREATE TABLE cliente (
  id_cliente INT AUTO_INCREMENT PRIMARY KEY,
  nome       VARCHAR(120)  NOT NULL,
  email      VARCHAR(254)  NOT NULL UNIQUE,
  telefone   VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mesa (1)───(N) Reserva
CREATE TABLE mesa (
  id_mesa     INT AUTO_INCREMENT PRIMARY KEY,
  numero      INT         NOT NULL UNIQUE,
  capacidade  SMALLINT UNSIGNED NOT NULL,
  CONSTRAINT chk_mesa_capacidade CHECK (capacidade > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CardapioItem (lado N da N:N com Pedido via Pedido_Item)
CREATE TABLE cardapioitem (
  id_item    INT AUTO_INCREMENT PRIMARY KEY,
  nome       VARCHAR(120) NOT NULL,
  descricao  TEXT,
  preco      DECIMAL(10,2) NOT NULL,
  categoria  VARCHAR(30)   NOT NULL,
  CONSTRAINT chk_cardapio_categoria
    CHECK (categoria IN ('bebida','sobremesa','lanche','outro')),
  CONSTRAINT chk_cardapio_preco CHECK (preco >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE reserva (
  id_reserva   INT AUTO_INCREMENT PRIMARY KEY,
  inicio       TIMESTAMP  NOT NULL,
  fim          TIMESTAMP  NOT NULL,
  qtd_pessoas  SMALLINT UNSIGNED NOT NULL,
  status       ENUM('pendente','confirmada','cancelada') NOT NULL DEFAULT 'pendente',
 
  id_cliente   INT NOT NULL,
 
  id_mesa      INT NOT NULL,

  CONSTRAINT chk_reserva_intervalo CHECK (fim > inicio),
  CONSTRAINT chk_reserva_qtd CHECK (qtd_pessoas > 0),

  CONSTRAINT fk_reserva_cliente
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
    ON UPDATE CASCADE ON DELETE CASCADE,

  CONSTRAINT fk_reserva_mesa
    FOREIGN KEY (id_mesa)    REFERENCES mesa(id_mesa)
    ON UPDATE CASCADE ON DELETE RESTRICT


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE pedido (
  id_pedido   INT AUTO_INCREMENT PRIMARY KEY,
  observacao  TEXT,
  status      ENUM('em_preparo','pronto','entregue','cancelado') NOT NULL DEFAULT 'em_preparo',
  data_hora   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  
  id_cliente  INT NOT NULL,

  CONSTRAINT fk_pedido_cliente
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE pedido_item (
  id_pedido   INT NOT NULL,
  id_item     INT NOT NULL,
  quantidade  INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_pedido, id_item),

  CONSTRAINT chk_pedido_item_qtd CHECK (quantidade > 0),

  CONSTRAINT fk_pedidoitem_pedido
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido)
    ON UPDATE CASCADE ON DELETE CASCADE,

  CONSTRAINT fk_pedidoitem_item
    FOREIGN KEY (id_item)   REFERENCES cardapioitem(id_item)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE INDEX idx_reserva_id_cliente    ON reserva (id_cliente);
CREATE INDEX idx_reserva_id_mesa       ON reserva (id_mesa);
CREATE INDEX idx_pedido_id_cliente     ON pedido  (id_cliente);
CREATE INDEX idx_pedidoitem_id_pedido  ON pedido_item (id_pedido);
CREATE INDEX idx_pedidoitem_id_item    ON pedido_item (id_item);
