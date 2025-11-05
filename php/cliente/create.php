<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $telefone = trim($_POST['telefone'] ?? '');
  if ($nome && $email) {
    $stmt = $conn->prepare("INSERT INTO cliente (nome,email,telefone) VALUES (?,?,?)");
    $stmt->bind_param("sss", $nome,$email,$telefone);
    if ($stmt->execute()) {
      header("Location: /Projetos/projeto-caf-moderno/php/cliente/index.php");
      exit;
    } else {
      $erro = "Erro ao inserir: " . $stmt->error;
    }
  } else {
    $erro = "Preencha nome e email.";
  }
}

$data = ['nome'=>$_POST['nome']??'','email'=>$_POST['email']??'','telefone'=>$_POST['telefone']??''];
?>
<div class="container">
  <h1>Novo Cliente</h1>
  <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <?php
    $action = "/Projetos/projeto-caf-moderno/php/cliente/create.php";
    $method = "POST";
    include __DIR__ . "/form.php";
  ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
