<?php
require_once __DIR__ . '/../conexao.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
  $stmt = $conn->prepare("DELETE FROM reserva WHERE id_reserva=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}
header("Location: /Projetos/projeto-caf-moderno/php/reserva/index.php");
exit;
