<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM pedido WHERE id_pedido=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$atual = $stmt->get_result()->fetch_assoc();

$erro='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id_cliente = (int)($_POST['id_cliente'] ?? 0);
  $status     = $_POST['status'] ?? 'em_preparo';
  $obs        = trim($_POST['observacao'] ?? '');

  if (!$id_cliente){
    $erro = "Selecione o cliente.";
  } else {
    $u = $conn->prepare("UPDATE pedido SET observacao=?, status=?, id_cliente=? WHERE id_pedido=?");
    $u->bind_param("ssii", $obs, $status, $id_cliente, $id);
    if ($u->execute()){
      header("Location: /Projetos/projeto-caf-moderno/php/pedido/read.php?id=".$id);
      exit;
    } else {
      $erro = "Erro ao atualizar: ".$u->error;
    }
  }
  $atual = ['id_cliente'=>$id_cliente,'status'=>$status,'observacao'=>$obs,'id_pedido'=>$id];
}
$data = $atual ?: [];
?>
<div class="container">
  <h1>Editar Pedido</h1>
  <?php if(!$atual): ?>
    <p>Pedido não encontrado.</p>
  <?php else: ?>
    <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php
      $action = "/Projetos/projeto-caf-moderno/php/pedido/update.php?id=".$id;
      $method = "POST";
      include __DIR__ . "/form.php";
    ?>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
