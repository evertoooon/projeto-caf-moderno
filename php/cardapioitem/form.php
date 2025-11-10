<?php

$method = $method ?? 'POST';
$data = $data ?? ['nome'=>'','descricao'=>'','preco'=>'','categoria'=>'bebida','imagem'=>''];
$categorias = ['bebida','sobremesa','lanche','outro'];

function slug($s) {
  $s = iconv('UTF-8','ASCII//TRANSLIT',$s);
  $s = preg_replace('/[^a-zA-Z0-9]+/','-', $s);
  $s = trim($s, '-');
  return strtolower($s);
}

$imgFile = $data['imagem'] ?: (slug($data['nome']).'.jpg');
$imgUrl  = "/Projetos/projeto-caf-moderno/img_cardapio/" . $imgFile;
?>
<form class="form-entity" method="<?= $method ?>" action="<?= $action ?>" enctype="multipart/form-data">
  <div class="row">
    <div>
      <label>Nome</label>
      <input name="nome" maxlength="120" required value="<?= htmlspecialchars($data['nome']) ?>">
    </div>
    <div>
      <label>Categoria</label>
      <select name="categoria" required>
        <?php foreach($categorias as $c): ?>
          <option value="<?= $c ?>" <?= ($data['categoria']===$c)?'selected':'' ?>><?= ucfirst($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="row">
    <div>
      <label>Preço (R$)</label>
      <input name="preco" type="number" step="0.01" min="0" required value="<?= htmlspecialchars($data['preco']) ?>">
    </div>
    <div>
      <label>Imagem (JPG/PNG) — opcional</label>
      <input type="file" name="imagem" accept=".jpg,.jpeg,.png">
      <?php if($data['nome']): ?>
        <div style="margin-top:8px;">
          <img src="<?= htmlspecialchars($imgUrl) ?>" alt="preview" width="120" height="120" style="object-fit:cover;border-radius:10px;">
        </div>
      <?php endif; ?>
      <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($data['imagem']) ?>">
    </div>
  </div>

  <div>
    <label>Descrição</label>
    <textarea name="descricao" rows="4"><?= htmlspecialchars($data['descricao']) ?></textarea>
  </div>

  <div class="actions">
    <button class="btn">Salvar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/cardapioitem/index.php">Cancelar</a>
  </div>
</form>
