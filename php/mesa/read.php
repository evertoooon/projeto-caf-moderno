<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id_mesa, numero, capacidade FROM mesa WHERE id_mesa=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mesa = $stmt->get_result()->fetch_assoc();
?>
<div class="container">
  <h1>Detalhes da Mesa</h1>
  <?php if(!$mesa): ?>
    <p>Mesa não encontrada.</p>
  <?php else: ?>
    <p><strong>ID:</strong> <?= $mesa['id_mesa'] ?></p>
    <p><strong>Número:</strong> <?= htmlspecialchars($mesa['numero']) ?></p>
    <p><strong>Capacidade:</strong> <?= htmlspecialchars($mesa['capacidade']) ?></p>
    <div class="actions">
      <a href="/Projetos/projeto-caf-moderno/php/mesa/update.php?id=<?= $mesa['id_mesa'] ?>">Editar</a>
      <a class="danger" onclick="return confirm('Excluir esta mesa?')" href="/Projetos/projeto-caf-moderno/php/mesa/delete.php?id=<?= $mesa['id_mesa'] ?>">Excluir</a>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/mesa/index.php">Voltar</a>
    </div>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
