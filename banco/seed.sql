-- ===============================
-- SEED · Café de Bairro Moderno
-- (PostgreSQL)
-- ===============================

SET search_path TO cafe, public;


-- Clientes
INSERT INTO cliente (nome, email, telefone) VALUES
('Ana Souza',     'ana.souza@cafe.com',     '(54) 99999-1111'),
('Carlos Pereira','carlos.pereira@cafe.com','(54) 98888-2222'),
('Mariana Lima',  'mariana.lima@cafe.com',  '(54) 97777-3333');

-- Mesas
INSERT INTO mesa (numero, capacidade) VALUES
(1, 2),
(2, 4),
(3, 6);

-- Cardápio
INSERT INTO cardapioitem (nome, descricao, preco, categoria) VALUES
('Café Expresso',   'Café curto e intenso',         5.00,  'bebida'),
('Cappuccino',      'Café, leite e espuma',         8.50,  'bebida'),
('Latte',           'Café com leite vaporizado',    9.00,  'bebida'),
('Brownie',         'Chocolate com nozes',          7.00,  'sobremesa'),
('Cheesecake',      'Cobertura de frutas vermelhas',9.50,  'sobremesa'),
('Sanduíche Natural','Frango, cenoura e maionese', 12.00,  'lanche');

-- 4) Reservas
INSERT INTO reserva (inicio, fim, qtd_pessoas, status, id_cliente, id_mesa) VALUES
('2025-09-08 15:00','2025-09-08 16:30', 2, 'confirmada',
  (SELECT id_cliente FROM cliente WHERE email='ana.souza@cafe.com'),
  (SELECT id_mesa     FROM mesa    WHERE numero=1)
),
('2025-09-08 18:00','2025-09-08 19:30', 4, 'pendente',
  (SELECT id_cliente FROM cliente WHERE email='carlos.pereira@cafe.com'),
  (SELECT id_mesa     FROM mesa    WHERE numero=2)
);

-- Pedidos + Itens
-- Pedido 1 (Ana)
WITH novo_pedido AS (
  INSERT INTO pedido (observacao, status, id_cliente)
  VALUES ('Sem açúcar', 'em_preparo',
          (SELECT id_cliente FROM cliente WHERE email='ana.souza@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido, id_item, quantidade)
SELECT np.id_pedido, ci.id_item, x.qtd
FROM novo_pedido np
JOIN LATERAL (
  VALUES ('Café Expresso', 2),
         ('Brownie',       1)
) AS x(nome, qtd) ON TRUE
JOIN cardapioitem ci ON ci.nome = x.nome;

-- Pedido 2 (Carlos)
WITH novo_pedido AS (
  INSERT INTO pedido (observacao, status, id_cliente)
  VALUES ('Mesa 2 pediu rápido', 'pronto',
          (SELECT id_cliente FROM cliente WHERE email='carlos.pereira@cafe.com'))
  RETURNING id_pedido
)
INSERT INTO pedido_item (id_pedido, id_item, quantidade)
SELECT np.id_pedido, ci.id_item, x.qtd
FROM novo_pedido np
JOIN LATERAL (
  VALUES ('Cappuccino',       1),
         ('Sanduíche Natural',2)
) AS x(nome, qtd) ON TRUE
JOIN cardapioitem ci ON ci.nome = x.nome;

-- ===============================
-- CONSULTAS DE DEMONSTRAÇÃO
-- ===============================

-- Clientes
SELECT * FROM cliente ORDER BY id_cliente;

-- Reservas com nome do cliente e nº da mesa
SELECT r.id_reserva, c.nome AS cliente, m.numero AS mesa, r.inicio, r.fim, r.status
FROM reserva r
JOIN cliente c ON c.id_cliente = r.id_cliente
JOIN mesa    m ON m.id_mesa    = r.id_mesa
ORDER BY r.inicio;

-- Pedidos com cliente e status
SELECT p.id_pedido, c.nome AS cliente, p.status, p.data_hora
FROM pedido p
JOIN cliente c ON c.id_cliente = p.id_cliente
ORDER BY p.id_pedido;

-- Itens de um pedido (ex.: último pedido inserido)
SELECT pi.id_pedido, ci.nome, pi.quantidade, ci.preco,
       (pi.quantidade * ci.preco) AS total_item
FROM pedido_item pi
JOIN cardapioitem ci ON ci.id_item = pi.id_item
WHERE pi.id_pedido = (SELECT MAX(id_pedido) FROM pedido)
ORDER BY ci.nome;

-- Cardápio por categoria
SELECT categoria, nome, preco
FROM cardapioitem
ORDER BY categoria, nome;

-- Total por pedido
SELECT p.id_pedido,
       SUM(pi.quantidade * ci.preco) AS total_pedido
FROM pedido p
JOIN pedido_item pi ON pi.id_pedido = p.id_pedido
JOIN cardapioitem ci ON ci.id_item  = pi.id_item
GROUP BY p.id_pedido
ORDER BY p.id_pedido;
