<?php

$method = $method ?? 'POST';
$data = $data ?? ['id_item'=>'','quantidade'=>1];
$lockItem = $lockItem ?? false;

require_once __DIR__ . '/../conexao.php';
$it = $conn->query("SELECT id_item, nome, preco FROM cardapioitem ORDER BY nome");
?>
<form class="form-entity" method="<?= $method ?>" action="<?= $action ?>">
  <div class="row-3">
    <div>
      <label>Item do Cardápio</label>
      <?php if($lockItem): ?>
        <input type="hidden" name="id_item" value="<?= htmlspecialchars($data['id_item']) ?>">
        <input disabled value="<?= htmlspecialchars($data['nome_item'] ?? '') ?>">
      <?php else: ?>
        <select name="id_item" required>
          <option value="">— Selecione —</option>
          <?php while($c = $it->fetch_assoc()): ?>
            <option value="<?= $c['id_item'] ?>"
              <?= (string)$data['id_item']===(string)$c['id_item']?'selected':'' ?>>
              <?= htmlspecialchars($c['nome']) ?> — R$ <?= number_format($c['preco'],2,',','.') ?>
            </option>
          <?php endwhile; ?>
        </select>
      <?php endif; ?>
    </div>
    <div>
      <label>Quantidade</label>
      <input type="number" name="quantidade" min="1" required
             value="<?= (int)$data['quantidade'] ?>">
    </div>
  </div>

  <div class="actions">
    <button class="btn">Salvar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido_item/index.php?id_pedido=<?= (int)($_GET['id_pedido'] ?? $_POST['id_pedido'] ?? 0) ?>">Cancelar</a>
  </div>
</form>
