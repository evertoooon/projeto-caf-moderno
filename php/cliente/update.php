<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id_cliente, nome, email, telefone FROM cliente WHERE id_cliente=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cli = $stmt->get_result()->fetch_assoc();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $telefone = trim($_POST['telefone'] ?? '');
  if ($nome && $email) {
    $stmtU = $conn->prepare("UPDATE cliente SET nome=?, email=?, telefone=? WHERE id_cliente=?");
    $stmtU->bind_param("sssi", $nome,$email,$telefone,$id);
    if ($stmtU->execute()) {
      header("Location: /Projetos/projeto-caf-moderno/php/cliente/read.php?id=".$id);
      exit;
    } else {
      $erro = "Erro ao atualizar: " . $stmtU->error;
    }
  } else {
    $erro = "Preencha nome e email.";
  }
  $cli = ['nome'=>$nome,'email'=>$email,'telefone'=>$telefone,'id_cliente'=>$id];
}
?>
<div class="container">
  <h1>Editar Cliente</h1>
  <?php if(!$cli): ?>
    <p>Cliente não encontrado.</p>
  <?php else: ?>
    <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php
      $action = "/Projetos/projeto-caf-moderno/php/cliente/update.php?id=".$id;
      $method = "POST";
      $data = $cli;
      include __DIR__ . "/form.php";
    ?>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
