<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id_cliente, nome, email, telefone FROM cliente WHERE id_cliente=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cli = $stmt->get_result()->fetch_assoc();
?>
<div class="container">
  <h1>Detalhes do Cliente</h1>
  <?php if(!$cli): ?>
    <p>Cliente não encontrado.</p>
  <?php else: ?>
    <p><strong>ID:</strong> <?= $cli['id_cliente'] ?></p>
    <p><strong>Nome:</strong> <?= htmlspecialchars($cli['nome']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($cli['email']) ?></p>
    <p><strong>Telefone:</strong> <?= htmlspecialchars($cli['telefone']) ?></p>
    <div class="actions">
      <a href="/Projetos/projeto-caf-moderno/php/cliente/update.php?id=<?= $cli['id_cliente'] ?>">Editar</a>
      <a class="danger" onclick="return confirm('Excluir este cliente?')" href="/Projetos/projeto-caf-moderno/php/cliente/delete.php?id=<?= $cli['id_cliente'] ?>">Excluir</a>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/cliente/index.php">Voltar</a>
    </div>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
