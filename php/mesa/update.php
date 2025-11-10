<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

mysqli_report(MYSQLI_REPORT_OFF);

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id_mesa, numero, capacidade FROM mesa WHERE id_mesa=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mesa = $stmt->get_result()->fetch_assoc();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $numero = (int)($_POST['numero'] ?? 0);
  $capacidade = (int)($_POST['capacidade'] ?? 0);

  if ($numero > 0 && $capacidade > 0) {
    $stmtU = $conn->prepare("UPDATE mesa SET numero=?, capacidade=? WHERE id_mesa=?");
    $stmtU->bind_param("iii", $numero, $capacidade, $id);
    if ($stmtU->execute()) {
      header("Location: /Projetos/projeto-caf-moderno/php/mesa/read.php?id=".$id);
      exit;
    } else {
      if ($conn->errno == 1062) {
        $erro = "Já existe uma mesa com o número {$numero}.";
      } else {
        $erro = "Erro ao atualizar: ({$conn->errno}) " . $conn->error;
      }
    }
  } else {
    $erro = "Informe número e capacidade válidos (maiores que zero).";
  }

  $mesa = ['id_mesa'=>$id, 'numero'=>$numero, 'capacidade'=>$capacidade];
}
?>
<div class="container">
  <h1>Editar Mesa</h1>
  <?php if(!$mesa): ?>
    <p>Mesa não encontrada.</p>
  <?php else: ?>
    <?php if($erro): ?><div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php
      $action = "/Projetos/projeto-caf-moderno/php/mesa/update.php?id=".$id;
      $method = "POST";
      $data = ['numero'=>$mesa['numero'] ?? '', 'capacidade'=>$mesa['capacidade'] ?? ''];
      include __DIR__ . "/form.php";
    ?>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
