-- ===============================
-- MODELO FÍSICO · Café Moderno
-- PostgreSQL (schema: cafe)
-- ===============================

-- 1) Schema
CREATE SCHEMA IF NOT EXISTS cafe;
SET search_path TO cafe, public;

-- 2) Tipos enumerados (status)
DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'reserva_status') THEN
    CREATE TYPE reserva_status AS ENUM ('pendente','confirmada','cancelada');
  END IF;

  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'pedido_status') THEN
    CREATE TYPE pedido_status AS ENUM ('em_preparo','pronto','entregue','cancelado');
  END IF;
END $$;

-- 3) Tabelas

-- Cliente (1)───(N) Reserva; (1)───(N) Pedido
CREATE TABLE IF NOT EXISTS cliente (
  id_cliente SERIAL PRIMARY KEY,
  nome       VARCHAR(120)  NOT NULL,
  email      VARCHAR(254)  NOT NULL UNIQUE,
  telefone   VARCHAR(20)
);

-- Mesa (1)───(N) Reserva
CREATE TABLE IF NOT EXISTS mesa (
  id_mesa     SERIAL PRIMARY KEY,
  numero      INTEGER   NOT NULL UNIQUE,
  capacidade  SMALLINT  NOT NULL CHECK (capacidade > 0)
);

-- CardapioItem (lado N da N:N com Pedido via Pedido_Item)
CREATE TABLE IF NOT EXISTS cardapioitem (
  id_item    SERIAL PRIMARY KEY,
  nome       VARCHAR(120) NOT NULL,
  descricao  TEXT,
  preco      NUMERIC(10,2) NOT NULL CHECK (preco >= 0),
  categoria  VARCHAR(30)   NOT NULL,
  CONSTRAINT chk_cardapio_categoria
    CHECK (categoria IN ('bebida','sobremesa','lanche','outro'))
);

-- Reserva (N)───(1) Mesa, (N)───(1) Cliente
CREATE TABLE IF NOT EXISTS reserva (
  id_reserva   SERIAL PRIMARY KEY,
  inicio       TIMESTAMP  NOT NULL,
  fim          TIMESTAMP  NOT NULL,
  qtd_pessoas  SMALLINT   NOT NULL CHECK (qtd_pessoas > 0),
  status       reserva_status NOT NULL DEFAULT 'pendente',

  -- Decisão de negócio: ao excluir o cliente, apagar suas reservas
  id_cliente   INTEGER NOT NULL REFERENCES cliente(id_cliente) ON DELETE CASCADE,
  -- Não permitir excluir mesa se houver reservas referenciando-a
  id_mesa      INTEGER NOT NULL REFERENCES mesa(id_mesa)       ON DELETE RESTRICT,

  CHECK (fim > inicio)
  -- Opcional: evitar mesma mesa no mesmo intervalo EXATO
  -- , UNIQUE (id_mesa, inicio, fim)
);

-- Pedido (N)───(1) Cliente
CREATE TABLE IF NOT EXISTS pedido (
  id_pedido   SERIAL PRIMARY KEY,
  observacao  TEXT,
  status      pedido_status NOT NULL DEFAULT 'em_preparo',
  data_hora   TIMESTAMP     NOT NULL DEFAULT now(),

  -- Corrigido: id_cliente (sem 'I' no final)
  id_cliente  INTEGER NOT NULL REFERENCES cliente(id_cliente) ON DELETE CASCADE
);

-- Pedido_Item (N:N Pedido x CardapioItem)
CREATE TABLE IF NOT EXISTS pedido_item (
  id_pedido  INTEGER NOT NULL REFERENCES pedido(id_pedido)         ON DELETE CASCADE,
  id_item    INTEGER NOT NULL REFERENCES cardapioitem(id_item)     ON DELETE RESTRICT,
  quantidade INTEGER NOT NULL CHECK (quantidade > 0),
  PRIMARY KEY (id_pedido, id_item)
);

-- 4) Índices auxiliares (FKs)
CREATE INDEX IF NOT EXISTS idx_reserva_id_cliente   ON reserva (id_cliente);
CREATE INDEX IF NOT EXISTS idx_reserva_id_mesa      ON reserva (id_mesa);
CREATE INDEX IF NOT EXISTS idx_pedido_id_cliente    ON pedido  (id_cliente);
CREATE INDEX IF NOT EXISTS idx_pedidoitem_id_pedido ON pedido_item (id_pedido);
CREATE INDEX IF NOT EXISTS idx_pedidoitem_id_item   ON pedido_item (id_item);
