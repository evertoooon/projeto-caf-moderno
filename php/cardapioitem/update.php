<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

function listarImagensCardapio(): array {
  $dirFs = __DIR__ . '/../../img_cardapio';
  if (!is_dir($dirFs)) return [];
  $ok = [];
  $exts = ['jpg','jpeg','png','webp','gif'];
  foreach (scandir($dirFs) as $f) {
    if ($f === '.' || $f === '..') continue;
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (in_array($ext, $exts, true)) $ok[] = $f;
  }
  sort($ok, SORT_NATURAL | SORT_FLAG_CASE);
  return $ok;
}

$categorias = ['bebida','sobremesa','lanche','outro'];
$imagens = listarImagensCardapio();
$erro = '';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id_item, nome, descricao, preco, categoria, imagem FROM cardapioitem WHERE id_item=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
  echo "<div class='container'><p>Item não encontrado.</p></div>";
  include_once __DIR__ . '/../partials/footer.php';
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome  = trim($_POST['nome'] ?? '');
  $desc  = trim($_POST['descricao'] ?? '');
  $cat   = trim($_POST['categoria'] ?? '');
  $preco = str_replace(',', '.', trim($_POST['preco'] ?? '0'));
  $img   = trim($_POST['imagem'] ?? '');

  if ($nome === '' || !is_numeric($preco) || $preco < 0 || !in_array($cat, $categorias, true)) {
    $erro = 'Preencha os campos corretamente.';
  } else {
    if ($img !== '' && !in_array($img, $imagens, true)) {
      $erro = 'Arquivo de imagem não encontrado em /img_cardapio.';
    } else {
      $sql = "UPDATE cardapioitem SET nome=?, descricao=?, preco=?, categoria=?, imagem=? WHERE id_item=?";
      $stmtU = $conn->prepare($sql);
      $stmtU->bind_param("ssdssi", $nome, $desc, $preco, $cat, $img, $id);
      if ($stmtU->execute()) {
        header("Location: /Projetos/projeto-caf-moderno/php/cardapioitem/read.php?id=".$id);
        exit;
      }
      $erro = "Erro ao atualizar: " . $stmtU->error;
    }
  }
  // reflete valores de POST no form
  $item = [
    'id_item'   => $id,
    'nome'      => $nome,
    'descricao' => $desc,
    'preco'     => $preco,
    'categoria' => $cat,
    'imagem'    => $img,
  ];
}
?>
<div class="container">
  <h1>Editar Item do Cardápio</h1>
  <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

  <form class="form-entity" method="post" action="">
    <div class="row">
      <div>
        <label>Nome</label>
        <input name="nome" maxlength="120" required value="<?= htmlspecialchars($item['nome']) ?>">
      </div>
      <div>
        <label>Categoria</label>
        <select name="categoria" required>
          <?php foreach($categorias as $c): ?>
            <option value="<?= $c ?>" <?= ($item['categoria']===$c)?'selected':'' ?>><?= ucfirst($c) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="row">
      <div>
        <label>Preço (ex.: 9,50)</label>
        <input name="preco" inputmode="decimal" required value="<?= htmlspecialchars(str_replace('.', ',', (string)$item['preco'])) ?>">
      </div>
      <div>
        <label>Imagem (opcional) – arquivos em /img_cardapio</label>
        <select name="imagem">
          <option value="" <?= ($item['imagem']==='')?'selected':'' ?>(sem imagem)</option>
          <?php foreach($imagens as $f): ?>
            <option value="<?= $f ?>" <?= ($item['imagem']===$f)?'selected':'' ?>><?= $f ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div>
      <label>Descrição</label>
      <textarea name="descricao" rows="3"><?= htmlspecialchars($item['descricao']) ?></textarea>
    </div>

    <div class="actions">
      <button class="btn">Salvar</button>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/cardapioitem/read.php?id=<?= $item['id_item'] ?>">Cancelar</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
