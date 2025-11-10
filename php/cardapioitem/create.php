<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

function slug($s) {
  $s = iconv('UTF-8','ASCII//TRANSLIT',$s);
  $s = preg_replace('/[^a-zA-Z0-9]+/','-', $s);
  $s = trim($s, '-');
  return strtolower($s);
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $categoria = trim($_POST['categoria'] ?? '');
  $preco = (float)($_POST['preco'] ?? 0);
  $descricao = trim($_POST['descricao'] ?? '');
  $imgAtual = trim($_POST['imagem_atual'] ?? '');

  if ($nome && $categoria && $preco >= 0) {
    // upload opcional
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
      $stmt = $conn->prepare("INSERT INTO cardapioitem (nome, descricao, preco, categoria, imagem) VALUES (?,?,?,?,?)");
      $stmt->bind_param("ssdss", $nome, $descricao, $preco, $categoria, $imgName);
      if ($stmt->execute()) {
        header("Location: /Projetos/projeto-caf-moderno/php/cardapioitem/index.php");
        exit;
      } else {
        $erro = "Erro ao inserir: " . $stmt->error;
      }
    }
  } else {
    $erro = "Preencha nome, categoria e preço.";
  }
}

$data = [
  'nome' => $_POST['nome'] ?? '',
  'descricao' => $_POST['descricao'] ?? '',
  'preco' => $_POST['preco'] ?? '',
  'categoria' => $_POST['categoria'] ?? 'bebida',
  'imagem' => $_POST['imagem_atual'] ?? ''
];
?>
<div class="container">
  <h1>Novo Item do Cardápio</h1>
  <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <?php
    $action = "/Projetos/projeto-caf-moderno/php/cardapioitem/create.php";
    $method = "POST";
    include __DIR__ . "/form.php";
  ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
