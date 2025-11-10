<?php
// Espera: $action, $method, $data
$method = $method ?? 'POST';
$data = $data ?? [
  'id_cliente' => '',
  'status'     => 'em_preparo',
  'observacao' => ''
];
$statuses = ['em_preparo','pronto','entregue','cancelado'];

require_once __DIR__ . '/../conexao.php';
$cli = $conn->query("SELECT id_cliente, nome FROM cliente ORDER BY nome");
?>
<form class="form-entity" method="<?= $method ?>" action="<?= $action ?>">
  <div class="row-3">
    <div>
      <label>Cliente</label>
      <select name="id_cliente" required>
        <option value="">— Selecione —</option>
        <?php while($c = $cli->fetch_assoc()): ?>
          <option value="<?= $c['id_cliente'] ?>"
            <?= (string)$data['id_cliente']===(string)$c['id_cliente']?'selected':'' ?>>
            <?= htmlspecialchars($c['nome']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label>Status</label>
      <select name="status" required>
        <?php foreach($statuses as $st): ?>
          <option value="<?= $st ?>" <?= $data['status']===$st?'selected':'' ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>Observação</label>
      <input name="observacao" maxlength="255"
             value="<?= htmlspecialchars($data['observacao']) ?>">
    </div>
  </div>

  <div class="actions">
    <button class="btn">Salvar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido/index.php">Cancelar</a>
  </div>
</form>
