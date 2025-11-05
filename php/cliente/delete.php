<?php
require_once __DIR__ . '/../conexao.php';
$id = (int)($_GET['id'] ?? 0);
if ($id) {
  $stmt = $conn->prepare("DELETE FROM cliente WHERE id_cliente=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}
header("Location: /Projetos/projeto-caf-moderno/php/cliente/index.php");
exit;
