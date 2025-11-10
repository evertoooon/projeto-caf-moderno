<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id = (int)($_GET['id'] ?? 0);

$sql = "SELECT p.*, c.nome AS cliente
        FROM pedido p JOIN cliente c ON c.id_cliente = p.id_cliente
        WHERE p.id_pedido=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();

$it = $conn->prepare("SELECT pi.id_item, pi.quantidade,
                             ci.nome, ci.preco, (pi.quantidade*ci.preco) AS total_item
                      FROM pedido_item pi
                      JOIN cardapioitem ci ON ci.id_item = pi.id_item
                      WHERE pi.id_pedido=?
                      ORDER BY ci.nome");
$it->bind_param("i",$id);
$it->execute();
$items = $it->get_result();

$tot = $conn->prepare("SELECT COALESCE(SUM(pi.quantidade*ci.preco),0)
                       FROM pedido_item pi JOIN cardapioitem ci ON ci.id_item=pi.id_item
                       WHERE pi.id_pedido=?");
$tot->bind_param("i",$id);
$tot->execute();
$tot->bind_result($totalPedido);
$tot->fetch();
$tot->close();

function fmt($ts){ return date('d/m/Y H:i', strtotime($ts)); }
?>
<div class="container">
  <h1>Pedido #<?= $id ?></h1>
  <?php if(!$p): ?>
    <p>Pedido não encontrado.</p>
  <?php else: ?>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($p['cliente']) ?></p>
    <p><strong>Status:</strong> <span class="badge"><?= $p['status'] ?></span></p>
    <p><strong>Data/Hora:</strong> <?= fmt($p['data_hora']) ?></p>
    <p><strong>Observação:</strong> <?= htmlspecialchars($p['observacao'] ?? '') ?></p>

    <div class="actions" style="margin:10px 0;">
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido/update.php?id=<?= $id ?>">Editar Pedido</a>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido_item/index.php?id_pedido=<?= $id ?>">Gerenciar Itens</a>
      <a class="danger" onclick="return confirm('Excluir este pedido?')"
         href="/Projetos/projeto-caf-moderno/php/pedido/delete.php?id=<?= $id ?>">Excluir</a>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido/index.php">Voltar</a>
    </div>

    <h2>Itens</h2>
    <table class="table">
      <thead><tr><th>Item</th><th>Preço</th><th>Qtd</th><th>Total</th></tr></thead>
      <tbody>
      <?php while($row = $items->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td><?= number_format($row['preco'],2,',','.') ?></td>
          <td><?= (int)$row['quantidade'] ?></td>
          <td><?= number_format($row['total_item'],2,',','.') ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr><th colspan="3" style="text-align:right">Total do pedido</th>
            <th><?= number_format($totalPedido,2,',','.') ?></th></tr>
      </tfoot>
    </table>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
