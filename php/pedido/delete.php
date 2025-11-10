<?php
require_once __DIR__ . '/../conexao.php';
$id = (int)($_GET['id'] ?? 0);
if ($id){
  
  $conn->query("DELETE FROM pedido_item WHERE id_pedido=".$id);
  $stmt = $conn->prepare("DELETE FROM pedido WHERE id_pedido=?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
}
header("Location: /Projetos/projeto-caf-moderno/php/pedido/index.php");
exit;
