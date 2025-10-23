<?php
include('conexao.php');
$r = $conn->query("SELECT COUNT(*) AS total FROM cliente");
$d = $r->fetch_assoc();
echo "Conexão OK — Clientes encontrados: " . $d['total'];
?>
