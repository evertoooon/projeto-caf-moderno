-- ===============================
-- SEED · Café de Bairro Moderno (10 linhas por tabela)
-- PostgreSQL – requer schema.sql já executado
-- ===============================

SET search_path TO cafe, public;

-- (Opcional) reset geral:
-- TRUNCATE TABLE pedido_item, pedido, reserva, cardapioitem, mesa, cliente
--   RESTART IDENTITY CASCADE;

-- ===============================
-- 1) CLIENTES (10)
-- ===============================
INSERT INTO cliente (nome, email, telefone) VALUES
('Ana Souza','ana.souza@cafe.com','(54) 99999-1111'),
('Carlos Pereira','carlos.pereira@cafe.com','(54) 98888-2222'),
('Mariana Lima','mariana.lima@cafe.com','(54) 97777-3333'),
('João Oliveira','joao.oliveira@cafe.com','(54) 96666-4444'),
('Beatriz Costa','beatriz.costa@cafe.com','(54) 95555-5555'),
('Rafael Martins','rafael.martins@cafe.com','(54) 94444-6666'),
('Paula Fernandes','paula.fernandes@cafe.com','(54) 93333-7777'),
('Lucas Rocha','lucas.rocha@cafe.com','(54) 92222-8888'),
('Julia Carvalho','julia.carvalho@cafe.com','(54) 91111-9999'),
('Fernando Alves','fernando.alves@cafe.com','(54) 90000-0000');

-- ===============================
-- 2) MESAS (10)
-- ===============================
INSERT INTO mesa (numero, capacidade) VALUES
(1,2),(2,2),(3,2),
(4,4),(5,4),(6,4),
(7,6),(8,6),(9,6),
(10,8);

-- ===============================
-- 3) CARDÁPIO (10)  -- 'bebida','sobremesa','lanche','outro'
-- ===============================
INSERT INTO cardapioitem (nome, descricao, preco, categoria) VALUES
('Café Expresso','Café curto e forte',5.00,'bebida'),
('Cappuccino','Café com leite e espuma',8.50,'bebida'),
('Latte','Café com leite vaporizado',9.00,'bebida'),
('Mocha','Café com chocolate e chantilly',10.00,'bebida'),
('Chá Gelado','Chá preto gelado com limão',7.00,'bebida'),
('Brownie','Chocolate com nozes',7.50,'sobremesa'),
('Cheesecake','Frutas vermelhas',9.50,'sobremesa'),
('Torta de Maçã','Clássica com canela',8.00,'sobremesa'),
('Sanduíche Natural','Frango, cenoura e maionese',12.00,'lanche'),
('Pão de Queijo','Porção com 6 unidades',9.00,'lanche');

-- ===============================
-- 4) RESERVAS (10)  -- 'pendente','confirmada','cancelada'
-- ===============================
INSERT INTO reserva (inicio,fim,qtd_pessoas,status,id_cliente,id_mesa) VALUES
('2025-09-08 12:00','2025-09-08 13:00',2,'confirmada',
 (SELECT id_cliente FROM cliente WHERE email='ana.souza@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=1)),
('2025-09-08 13:30','2025-09-08 14:30',4,'pendente',
 (SELECT id_cliente FROM cliente WHERE email='carlos.pereira@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=5)),
('2025-09-08 15:00','2025-09-08 16:30',3,'confirmada',
 (SELECT id_cliente FROM cliente WHERE email='mariana.lima@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=4)),
('2025-09-08 18:00','2025-09-08 19:00',2,'cancelada',
 (SELECT id_cliente FROM cliente WHERE email='joao.oliveira@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=2)),
('2025-09-09 10:00','2025-09-09 11:00',2,'pendente',
 (SELECT id_cliente FROM cliente WHERE email='beatriz.costa@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=3)),
('2025-09-09 11:30','2025-09-09 12:30',6,'confirmada',
 (SELECT id_cliente FROM cliente WHERE email='rafael.martins@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=9)),
('2025-09-09 15:00','2025-09-09 16:00',4,'pendente',
 (SELECT id_cliente FROM cliente WHERE email='paula.fernandes@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=6)),
('2025-09-09 16:30','2025-09-09 17:30',2,'confirmada',
 (SELECT id_cliente FROM cliente WHERE email='lucas.rocha@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=1)),
('2025-09-10 09:00','2025-09-10 10:00',2,'confirmada',
 (SELECT id_cliente FROM cliente WHERE email='julia.carvalho@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=7)),
('2025-09-10 19:00','2025-09-10 20:00',8,'pendente',
 (SELECT id_cliente FROM cliente WHERE email='fernando.alves@cafe.com'),
 (SELECT id_mesa FROM mesa WHERE numero=10));

-- ===============================
-- 5) PEDIDOS (10) + PEDIDO_ITEM (10)
-- cada pedido terá 1 item (PK composta: id_pedido + id_item)
-- ===============================

-- P1 (Ana) -> Expresso x2
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Sem açúcar','em_preparo',(SELECT id_cliente FROM cliente WHERE email='ana.souza@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 2
FROM np JOIN cardapioitem ci ON ci.nome='Café Expresso';

-- P2 (Carlos) -> Cappuccino x1
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Mesa 5','pronto',(SELECT id_cliente FROM cliente WHERE email='carlos.pereira@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 1
FROM np JOIN cardapioitem ci ON ci.nome='Cappuccino';

-- P3 (Mariana) -> Brownie x1
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Levar guardanapo','entregue',(SELECT id_cliente FROM cliente WHERE email='mariana.lima@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 1
FROM np JOIN cardapioitem ci ON ci.nome='Brownie';

-- P4 (João) -> Latte x1
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Sem canela','em_preparo',(SELECT id_cliente FROM cliente WHERE email='joao.oliveira@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 1
FROM np JOIN cardapioitem ci ON ci.nome='Latte';

-- P5 (Beatriz) -> Cheesecake x2
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Sobremesa para viagem','pronto',(SELECT id_cliente FROM cliente WHERE email='beatriz.costa@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 2
FROM np JOIN cardapioitem ci ON ci.nome='Cheesecake';

-- P6 (Rafael) -> Sanduíche Natural x2
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Trocar maionese por azeite','entregue',(SELECT id_cliente FROM cliente WHERE email='rafael.martins@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 2
FROM np JOIN cardapioitem ci ON ci.nome='Sanduíche Natural';

-- P7 (Paula) -> Mocha x1
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Com chantilly','em_preparo',(SELECT id_cliente FROM cliente WHERE email='paula.fernandes@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 1
FROM np JOIN cardapioitem ci ON ci.nome='Mocha';

-- P8 (Lucas) -> Chá Gelado x1
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Pouco gelo','pronto',(SELECT id_cliente FROM cliente WHERE email='lucas.rocha@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 1
FROM np JOIN cardapioitem ci ON ci.nome='Chá Gelado';

-- P9 (Julia) -> Pão de Queijo x3
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Bem quentinho','entregue',(SELECT id_cliente FROM cliente WHERE email='julia.carvalho@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 3
FROM np JOIN cardapioitem ci ON ci.nome='Pão de Queijo';

-- P10 (Fernando) -> Torta de Maçã x1
WITH np AS (
  INSERT INTO pedido (observacao,status,id_cliente)
  VALUES ('Com canela extra','cancelado',(SELECT id_cliente FROM cliente WHERE email='fernando.alves@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido,id_item,quantidade)
SELECT np.id_pedido, ci.id_item, 1
FROM np JOIN cardapioitem ci ON ci.nome='Torta de Maçã';

-- ===============================
-- CONSULTAS (com enunciado)
-- ===============================

-- PERGUNTA: Quais são todos os clientes cadastrados (ordenados pelo ID)?
SELECT * FROM cliente ORDER BY id_cliente;

-- PERGUNTA: Quais reservas existem, com o nome do cliente e o número da mesa?
SELECT r.id_reserva, c.nome AS cliente, m.numero AS mesa, r.inicio, r.fim, r.status
FROM reserva r
JOIN cliente c ON c.id_cliente = r.id_cliente
JOIN mesa    m ON m.id_mesa    = r.id_mesa
ORDER BY r.inicio;

-- PERGUNTA: Quais pedidos foram feitos, com o nome do cliente, status e data/hora?
SELECT p.id_pedido, c.nome AS cliente, p.status, p.data_hora
FROM pedido p
JOIN cliente c ON c.id_cliente = p.id_cliente
ORDER BY p.id_pedido;

-- PERGUNTA: Quais itens compõem um pedido específico (ex.: o último) e o total por item?
SELECT pi.id_pedido, ci.nome, pi.quantidade, ci.preco,
       (pi.quantidade * ci.preco) AS total_item
FROM pedido_item pi
JOIN cardapioitem ci ON ci.id_item = pi.id_item
WHERE pi.id_pedido = (SELECT MAX(id_pedido) FROM pedido)
ORDER BY ci.nome;

-- PERGUNTA: Como está organizado o cardápio por categoria e nome?
SELECT categoria, nome, preco
FROM cardapioitem
ORDER BY categoria, nome;

-- PERGUNTA: Qual é o total (R$) de cada pedido?
SELECT p.id_pedido,
       SUM(pi.quantidade * ci.preco) AS total_pedido
FROM pedido p
JOIN pedido_item pi ON pi.id_pedido = p.id_pedido
JOIN cardapioitem ci ON ci.id_item  = pi.id_item
GROUP BY p.id_pedido
ORDER BY p.id_pedido;

-- PERGUNTA: Quantos clientes existem no total?
SELECT COUNT(*) AS total_clientes FROM cliente;

-- PERGUNTA: Qual é o preço médio dos itens do cardápio?
SELECT ROUND(AVG(preco), 2) AS preco_medio FROM cardapioitem;

-- PERGUNTA: Qual é a maior e a menor capacidade entre as mesas?
SELECT MAX(capacidade) AS maior_capacidade, MIN(capacidade) AS menor_capacidade
FROM mesa;
