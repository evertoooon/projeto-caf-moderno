<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_cliente = (int)($_POST['id_cliente'] ?? 0);
  $status     = $_POST['status'] ?? 'em_preparo';
  $obs        = trim($_POST['observacao'] ?? '');

  if (!$id_cliente) {
    $erro = "Selecione o cliente.";
  } else {
    $stmt = $conn->prepare("INSERT INTO pedido (observacao,status,id_cliente) VALUES (?,?,?)");
    $stmt->bind_param("ssi", $obs, $status, $id_cliente);
    if ($stmt->execute()) {
      header("Location: /Projetos/projeto-caf-moderno/php/pedido/read.php?id=".$conn->insert_id);
      exit;
    } else {
      $erro = "Erro ao inserir: " . $stmt->error;
    }
  }
}
$data = [
  'id_cliente' => $_POST['id_cliente'] ?? '',
  'status'     => $_POST['status']     ?? 'em_preparo',
  'observacao' => $_POST['observacao'] ?? ''
];
?>
<div class="container">
  <h1>Novo Pedido</h1>
  <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <?php
    $action = "/Projetos/projeto-caf-moderno/php/pedido/create.php";
    $method = "POST";
    include __DIR__ . "/form.php";
  ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
