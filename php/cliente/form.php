<?php
// Espera variáveis: $action (URL), $data (array com campos) e $method (POST)
$method = $method ?? 'POST';
$data = $data ?? ['nome'=>'','email'=>'','telefone'=>''];
?>
<form class="form-entity" method="<?= $method ?>" action="<?= $action ?>">
  <div class="row">
    <div>
      <label>Nome</label>
      <input name="nome" maxlength="120" required value="<?= htmlspecialchars($data['nome']) ?>">
    </div>
    <div>
      <label>Email</label>
      <input name="email" type="email" maxlength="254" required value="<?= htmlspecialchars($data['email']) ?>">
    </div>
  </div>
  <div>
    <label>Telefone</label>
    <input name="telefone" maxlength="20" value="<?= htmlspecialchars($data['telefone']) ?>">
  </div>
  <div class="actions">
    <button class="btn">Salvar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/cliente/index.php">Cancelar</a>
  </div>
</form>
