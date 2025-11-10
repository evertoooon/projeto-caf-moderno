<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

mysqli_report(MYSQLI_REPORT_OFF); // evitar exception automática

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $numero = (int)($_POST['numero'] ?? 0);
  $capacidade = (int)($_POST['capacidade'] ?? 0);

  if ($numero > 0 && $capacidade > 0) {
    $stmt = $conn->prepare("INSERT INTO mesa (numero, capacidade) VALUES (?, ?)");
    $stmt->bind_param("ii", $numero, $capacidade);
    if ($stmt->execute()) {
      header("Location: /Projetos/projeto-caf-moderno/php/mesa/index.php");
      exit;
    } else {
      if ($conn->errno == 1062) {
        $erro = "Já existe uma mesa com o número {$numero}.";
      } else {
        $erro = "Erro ao inserir: ({$conn->errno}) " . $conn->error;
      }
    }
  } else {
    $erro = "Informe número e capacidade válidos (maiores que zero).";
  }
}

$data = [
  'numero' => $_POST['numero'] ?? '',
  'capacidade' => $_POST['capacidade'] ?? '',
];
?>
<div class="container">
  <h1>Nova Mesa</h1>
  <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <?php
    $action = "/Projetos/projeto-caf-moderno/php/mesa/create.php";
    $method = "POST";
    include __DIR__ . "/form.php";
  ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
