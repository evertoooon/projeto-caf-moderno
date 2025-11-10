<?php
require_once __DIR__ . '/../conexao.php';
mysqli_report(MYSQLI_REPORT_OFF);

$id = (int)($_GET['id'] ?? 0);
$erro = '';
if ($id) {
  $stmt = $conn->prepare("DELETE FROM mesa WHERE id_mesa=?");
  $stmt->bind_param("i", $id);
  if (!$stmt->execute()) {
    if ($conn->errno == 1451) {
      $erro = "Não é possível excluir: há reservas vinculadas a esta mesa.";
    } else {
      $erro = "Erro ao excluir: ({$conn->errno}) " . $conn->error;
    }
  }
}
$dest = "/Projetos/projeto-caf-moderno/php/mesa/index.php";
if ($erro) { $dest .= "?erro=" . urlencode($erro); }
header("Location: {$dest}");
exit;
