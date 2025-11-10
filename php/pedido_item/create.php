<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id_pedido = (int)($_GET['id_pedido'] ?? $_POST['id_pedido'] ?? 0);
$erro = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id_item = (int)($_POST['id_item'] ?? 0);
  $qtd     = (int)($_POST['quantidade'] ?? 0);

  if (!$id_item || $qtd <= 0) {
    $erro = "Selecione o item e uma quantidade válida.";
  } else {
    $stmt = $conn->prepare("INSERT INTO pedido_item (id_pedido,id_item,quantidade) VALUES (?,?,?)
                            ON DUPLICATE KEY UPDATE quantidade = quantidade + VALUES(quantidade)");
    $stmt->bind_param("iii", $id_pedido, $id_item, $qtd);
    if ($stmt->execute()){
      header("Location: /Projetos/projeto-caf-moderno/php/pedido_item/index.php?id_pedido=".$id_pedido);
      exit;
    } else {
      $erro = "Erro ao inserir: ".$stmt->error;
    }
  }
}

$data = ['id_item'=>$_POST['id_item']??'','quantidade'=>$_POST['quantidade']??1];
?>
<div class="container">
  <h1>Adicionar Item ao Pedido #<?= $id_pedido ?></h1>
  <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <?php
    $action = "/Projetos/projeto-caf-moderno/php/pedido_item/create.php?id_pedido=".$id_pedido;
    $method = "POST";
    include __DIR__ . "/form.php";
  ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
