<?php
require_once __DIR__ . '/../conexao.php';
$id_pedido = (int)($_GET['id_pedido'] ?? 0);
$id_item   = (int)($_GET['id_item'] ?? 0);
if ($id_pedido && $id_item){
  $d = $conn->prepare("DELETE FROM pedido_item WHERE id_pedido=? AND id_item=?");
  $d->bind_param("ii",$id_pedido,$id_item);
  $d->execute();
}
header("Location: /Projetos/projeto-caf-moderno/php/pedido_item/index.php?id_pedido=".$id_pedido);
exit;
