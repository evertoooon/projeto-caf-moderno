<?php
$host = 'localhost';
$user = 'root';        // ou o usuário que você criou
$pass = '';            // senha do MySQL (se houver)
$db   = 'cafemoderno';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die('Erro na conexão: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
