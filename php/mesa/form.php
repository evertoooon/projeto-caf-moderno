<?php

$method = $method ?? 'POST';
$data = $data ?? ['numero'=>'','capacidade'=>''];
?>
<form class="form-entity" method="<?= $method ?>" action="<?= $action ?>">
  <div class="row">
    <div>
      <label>Número</label>
      <input name="numero" type="number" min="1" required value="<?= htmlspecialchars($data['numero']) ?>">
    </div>
    <div>
      <label>Capacidade</label>
      <input name="capacidade" type="number" min="1" required value="<?= htmlspecialchars($data['capacidade']) ?>">
    </div>
  </div>

  <div class="actions">
    <button class="btn">Salvar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/mesa/index.php">Cancelar</a>
  </div>
</form>
