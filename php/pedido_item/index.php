<?php
require_once __DIR__ . '/../conexao.php';
$currentTitle = 'Cardápio';
include_once __DIR__ . '/../partials/header.php';

$id_pedido = (int)($_GET['id_pedido'] ?? 0);
if ($id_pedido <= 0) {
  header("Location: /Projetos/projeto-caf-moderno/php/pedido/index.php");
  exit;
}


// pedido + cliente (para header)
$h = $conn->prepare("SELECT p.id_pedido, p.status, c.nome AS cliente
                     FROM pedido p JOIN cliente c ON c.id_cliente=p.id_cliente
                     WHERE p.id_pedido=?");
$h->bind_param("i",$id_pedido);
$h->execute();
$pedido = $h->get_result()->fetch_assoc();

// itens
$stmt = $conn->prepare("SELECT pi.id_item, pi.quantidade,
                               ci.nome, ci.preco, (pi.quantidade*ci.preco) AS total_item
                        FROM pedido_item pi
                        JOIN cardapioitem ci ON ci.id_item = pi.id_item
                        WHERE pi.id_pedido=?
                        ORDER BY ci.nome");
$stmt->bind_param("i",$id_pedido);
$stmt->execute();
$itens = $stmt->get_result();

function moneyf($v){ return number_format($v,2,',','.'); }

$tot = $conn->prepare("SELECT COALESCE(SUM(pi.quantidade*ci.preco),0)
                       FROM pedido_item pi JOIN cardapioitem ci ON ci.id_item=pi.id_item
                       WHERE pi.id_pedido=?");
$tot->bind_param("i",$id_pedido);
$tot->execute(); $tot->bind_result($totalPedido); $tot->fetch(); $tot->close();
?>
<div class="container">
  <h1>Itens do Pedido #<?= $id_pedido ?></h1>
  <?php if(!$pedido): ?>
    <p>Pedido não encontrado.</p>
  <?php else: ?>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?> — <strong>Status:</strong> <?= $pedido['status'] ?></p>

    <div class="actions" style="margin:10px 0;">
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido_item/create.php?id_pedido=<?= $id_pedido ?>">+ Adicionar Item</a>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido/read.php?id=<?= $id_pedido ?>">Voltar ao Pedido</a>
    </div>

    <table class="table">
      <thead><tr><th>Item</th><th>Preço</th><th>Qtd</th><th>Total</th><th>Ações</th></tr></thead>
      <tbody>
      <?php while($r = $itens->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($r['nome']) ?></td>
          <td><?= moneyf($r['preco']) ?></td>
          <td><?= (int)$r['quantidade'] ?></td>
          <td><?= moneyf($r['total_item']) ?></td>
          <td class="actions">
            <a href="/Projetos/projeto-caf-moderno/php/pedido_item/update.php?id_pedido=<?= $id_pedido ?>&id_item=<?= $r['id_item'] ?>">Editar</a>
            <a class="danger"
               onclick="return confirm('Remover este item do pedido?')"
               href="/Projetos/projeto-caf-moderno/php/pedido_item/delete.php?id_pedido=<?= $id_pedido ?>&id_item=<?= $r['id_item'] ?>">Excluir</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr><th colspan="3" style="text-align:right">Total</th><th><?= moneyf($totalPedido) ?></th><th></th></tr>
      </tfoot>
    </table>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
