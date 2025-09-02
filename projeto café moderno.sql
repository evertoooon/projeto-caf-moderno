-- =========================================================
-- Café de Bairro Moderno — MODELO FÍSICO (PostgreSQL)
-- =========================================================

BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'status_reserva') THEN
    CREATE TYPE status_reserva AS ENUM ('pendente','confirmada','cancelada','no_show');
  END IF;

  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'status_pedido') THEN
    CREATE TYPE status_pedido AS ENUM ('em_preparo','pronto','entregue','cancelado');
  END IF;

  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'categoria_item') THEN
    CREATE TYPE categoria_item AS ENUM ('bebida','sobremesa','lanche','cafe','outros');
  END IF;
END $$;



CREATE TABLE IF NOT EXISTS cliente (
  id_cliente     BIGSERIAL PRIMARY KEY,
  nome           VARCHAR(120) NOT NULL,
  email          VARCHAR(160) NOT NULL UNIQUE,
  telefone       VARCHAR(20),
  created_at     TIMESTAMP NOT NULL DEFAULT NOW()
);


CREATE TABLE IF NOT EXISTS mesa (
  id_mesa     BIGSERIAL PRIMARY KEY,
  numero      INTEGER NOT NULL UNIQUE,
  capacidade  INTEGER NOT NULL CHECK (capacidade > 0),
  ativa       BOOLEAN NOT NULL DEFAULT TRUE
);


CREATE TABLE IF NOT EXISTS cardapio_item (
  id_item    BIGSERIAL PRIMARY KEY,
  nome       VARCHAR(120) NOT NULL,
  descricao  TEXT,
  preco      NUMERIC(10,2) NOT NULL CHECK (preco >= 0),
  categoria  categoria_item NOT NULL DEFAULT 'cafe',
  ativo      BOOLEAN NOT NULL DEFAULT TRUE
);



CREATE TABLE IF NOT EXISTS reserva (
  id_reserva   BIGSERIAL PRIMARY KEY,
  inicio       TIMESTAMP NOT NULL,
  fim          TIMESTAMP NOT NULL,
  qtd_pessoas  INTEGER  NOT NULL CHECK (qtd_pessoas > 0),
  status       status_reserva NOT NULL DEFAULT 'pendente',

  id_mesa      BIGINT NOT NULL REFERENCES mesa(id_mesa)
                  ON UPDATE CASCADE ON DELETE RESTRICT,
  id_cliente   BIGINT NOT NULL REFERENCES cliente(id_cliente)
                  ON UPDATE CASCADE ON DELETE RESTRICT,

  CHECK (fim > inicio)
);


CREATE TABLE IF NOT EXISTS pedido (
  id_pedido   BIGSERIAL PRIMARY KEY,
  data_hora   TIMESTAMP NOT NULL DEFAULT NOW(),
  observacao  TEXT,
  status      status_pedido NOT NULL DEFAULT 'em_preparo',

  id_cliente  BIGINT NOT NULL REFERENCES cliente(id_cliente)
                ON UPDATE CASCADE ON DELETE RESTRICT
);


CREATE TABLE IF NOT EXISTS pedido_item (
  id_pedido   BIGINT NOT NULL REFERENCES pedido(id_pedido)
                ON UPDATE CASCADE ON DELETE CASCADE,
  id_item     BIGINT NOT NULL REFERENCES cardapio_item(id_item)
                ON UPDATE CASCADE ON DELETE RESTRICT,
  quantidade  INTEGER NOT NULL CHECK (quantidade > 0),

  CONSTRAINT pedido_item_pk PRIMARY KEY (id_pedido, id_item)
);


CREATE TABLE IF NOT EXISTS recomendacao (
  id_recomendacao  BIGSERIAL PRIMARY KEY,
  descricao        TEXT NOT NULL,
  id_item          BIGINT NOT NULL REFERENCES cardapio_item(id_item)
                      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE INDEX IF NOT EXISTS idx_reserva_mesa     ON reserva(id_mesa);
CREATE INDEX IF NOT EXISTS idx_reserva_cliente  ON reserva(id_cliente);
CREATE INDEX IF NOT EXISTS idx_pedido_cliente   ON pedido(id_cliente);
CREATE INDEX IF NOT EXISTS idx_pedido_item_item ON pedido_item(id_item);
