<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id_pedido = (int)($_GET['id_pedido'] ?? 0);
$id_item   = (int)($_GET['id_item'] ?? 0);

$stmt = $conn->prepare("SELECT pi.id_item, pi.quantidade, ci.nome
                        FROM pedido_item pi JOIN cardapioitem ci ON ci.id_item=pi.id_item
                        WHERE pi.id_pedido=? AND pi.id_item=?");
$stmt->bind_param("ii",$id_pedido,$id_item);
$stmt->execute();
$atual = $stmt->get_result()->fetch_assoc();

$erro='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $qtd = (int)($_POST['quantidade'] ?? 0);
  if ($qtd<=0){
    $erro = "Quantidade inválida.";
  } else {
    $u = $conn->prepare("UPDATE pedido_item SET quantidade=? WHERE id_pedido=? AND id_item=?");
    $u->bind_param("iii",$qtd,$id_pedido,$id_item);
    if ($u->execute()){
      header("Location: /Projetos/projeto-caf-moderno/php/pedido_item/index.php?id_pedido=".$id_pedido);
      exit;
    } else {
      $erro = "Erro ao atualizar: ".$u->error;
    }
  }
  $atual['quantidade']=$qtd;
}
$data = ['id_item'=>$id_item,'quantidade'=>$atual['quantidade']??1,'nome_item'=>$atual['nome']??''];
?>
<div class="container">
  <h1>Editar Item do Pedido #<?= $id_pedido ?></h1>
  <?php if(!$atual): ?>
    <p>Item não encontrado.</p>
  <?php else: ?>
    <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php
      $action = "/Projetos/projeto-caf-moderno/php/pedido_item/update.php?id_pedido={$id_pedido}&id_item={$id_item}";
      $method = "POST";
      $lockItem = true; 
      include __DIR__ . "/form.php";
    ?>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
