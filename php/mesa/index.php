<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$busca = $_GET['q'] ?? '';
$sql = "SELECT id_mesa, numero, capacidade FROM mesa";
$params = [];
if ($busca !== '') {
  $sql .= " WHERE CAST(numero AS CHAR) LIKE ? OR CAST(capacidade AS CHAR) LIKE ?";
  $like = "%$busca%";
  $params = [$like, $like];
}
$sql .= " ORDER BY numero ASC";

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param("ss", ...$params); }
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="container">
  <h1>Mesas</h1>

  <?php if (!empty($_GET['erro'])): ?>
    <div class="badge" style="background:#b41323;color:#fff"><?= htmlspecialchars($_GET['erro']) ?></div>
  <?php endif; ?>

  <form method="get" style="margin: 12px 0;">
    <input type="text" name="q" placeholder="Buscar por número/capacidade" value="<?= htmlspecialchars($busca) ?>" />
    <button class="btn">Buscar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/mesa/create.php">+ Nova Mesa</a>
  </form>

  <table class="table">
    <thead>
      <tr><th>ID</th><th>Número</th><th>Capacidade</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id_mesa'] ?></td>
        <td><?= htmlspecialchars($row['numero']) ?></td>
        <td><?= htmlspecialchars($row['capacidade']) ?></td>
        <td class="actions">
          <a href="/Projetos/projeto-caf-moderno/php/mesa/read.php?id=<?= $row['id_mesa'] ?>">Ver</a>
          <a href="/Projetos/projeto-caf-moderno/php/mesa/update.php?id=<?= $row['id_mesa'] ?>">Editar</a>
          <a class="danger" onclick="return confirm('Excluir esta mesa?')" href="/Projetos/projeto-caf-moderno/php/mesa/delete.php?id=<?= $row['id_mesa'] ?>">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
