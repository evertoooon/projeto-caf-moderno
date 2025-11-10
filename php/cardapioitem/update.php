<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

function slug($s) {
  $s = iconv('UTF-8','ASCII//TRANSLIT',$s);
  $s = preg_replace('/[^a-zA-Z0-9]+/','-', $s);
  $s = trim($s, '-');
  return strtolower($s);
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id_item, nome, descricao, preco, categoria, imagem FROM cardapioitem WHERE id_item=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $categoria = trim($_POST['categoria'] ?? '');
  $preco = (float)($_POST['preco'] ?? 0);
  $descricao = trim($_POST['descricao'] ?? '');
  $imgAtual = trim($_POST['imagem_atual'] ?? '');

  if ($nome && $categoria && $preco >= 0) {
    $imgName = $imgAtual ?: null;

    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
      if (!in_array($ext, ['jpg','jpeg','png'])) {
        $erro = "Formato inválido. Use JPG ou PNG.";
      } else {
        $imgName = slug($nome) . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        $dest = realpath(__DIR__ . '/../../img_cardapio');
        if (!$dest) $dest = __DIR__ . '/../../img_cardapio';
        move_uploaded_file($_FILES['imagem']['tmp_name'], $dest . DIRECTORY_SEPARATOR . $imgName);
      }
    }

    if (!$erro) {
      $stmtU = $conn->prepare("UPDATE cardapioitem SET nome=?, descricao=?, preco=?, categoria=?, imagem=? WHERE id_item=?");
      $stmtU->bind_param("ssdssi", $nome, $descricao, $preco, $categoria, $imgName, $id);
      if ($stmtU->execute()) {
        header("Location: /Projetos/projeto-caf-moderno/php/cardapioitem/read.php?id=".$id);
        exit;
      } else {
        $erro = "Erro ao atualizar: " . $stmtU->error;
      }
    }
  } else {
    $erro = "Preencha nome, categoria e preço.";
  }

  // espelha o formulário com os novos dados em caso de erro
  $item = ['id_item'=>$id, 'nome'=>$nome, 'descricao'=>$descricao, 'preco'=>$preco, 'categoria'=>$categoria, 'imagem'=>$imgName ?? $imgAtual];
}
?>
<div class="container">
  <h1>Editar Item do Cardápio</h1>
  <?php if(!$item): ?>
    <p>Item não encontrado.</p>
  <?php else: ?>
    <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php
      $action = "/Projetos/projeto-caf-moderno/php/cardapioitem/update.php?id=".$id;
      $method = "POST";
      $data = $item;
      include __DIR__ . "/form.php";
    ?>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
