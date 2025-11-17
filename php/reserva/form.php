<?php

$method = $method ?? 'POST';
$data = $data ?? [
  'id_cliente' => '',
  'id_mesa'    => '',
  'inicio'     => '',
  'fim'        => '',
  'qtd_pessoas'=> '',
  'status'     => 'pendente',
];


function toInputDT($ts) {
  if (!$ts) return '';
  $t = strtotime($ts);
  if ($t === false) return '';
  return date('Y-m-d\TH:i', $t);
}
$statuses = ['pendente','confirmada','cancelada'];


require_once __DIR__ . '/../conexao.php';
$cli = $conn->query("SELECT id_cliente, nome FROM cliente ORDER BY nome");
$mes = $conn->query("SELECT id_mesa, numero, capacidade FROM mesa ORDER BY numero");
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
      <label>Mesa (nº — capacidade)</label>
      <select name="id_mesa" required>
        <option value="">— Selecione —</option>
        <?php while($m = $mes->fetch_assoc()): ?>
          <option value="<?= $m['id_mesa'] ?>"
            <?= (string)$data['id_mesa']===(string)$m['id_mesa']?'selected':'' ?>>
            <?= (int)$m['numero'] ?> — cap. <?= (int)$m['capacidade'] ?>
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
  </div>

  <div class="row-3">
    <div>
      <label>Início</label>
      <input type="datetime-local" name="inicio" required
             value="<?= toInputDT($data['inicio']) ?>">
    </div>
    <div>
      <label>Fim</label>
      <input type="datetime-local" name="fim" required
             value="<?= toInputDT($data['fim']) ?>">
    </div>
    <div>
      <label>Qtd. Pessoas</label>
      <input type="number" name="qtd_pessoas" min="1" required
             value="<?= htmlspecialchars($data['qtd_pessoas']) ?>">
    </div>
  </div>

  <div class="actions">
    <button class="btn">Salvar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/reserva/index.php">Cancelar</a>
  </div>
</form>
