<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$busca = $_GET['q'] ?? '';
$sql = "SELECT id_cliente, nome, email, telefone FROM cliente";
$params = [];
if ($busca !== '') {
  $sql .= " WHERE nome LIKE ? OR email LIKE ? OR telefone LIKE ?";
  $like = "%$busca%";
  $params = [$like,$like,$like];
}
$sql .= " ORDER BY id_cliente ASC";

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param("sss", ...$params); }
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="container">
  <h1>Clientes</h1>

  <form method="get" style="margin: 12px 0;">
    <input type="text" name="q" placeholder="Buscar por nome/email/telefone" value="<?= htmlspecialchars($busca) ?>" />
    <button class="btn">Buscar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/cliente/create.php">+ Novo Cliente</a>
  </form>

  <table class="table">
    <thead>
      <tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id_cliente'] ?></td>
        <td><?= htmlspecialchars($row['nome']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['telefone']) ?></td>
        <td class="actions">
          <a href="/Projetos/projeto-caf-moderno/php/cliente/read.php?id=<?= $row['id_cliente'] ?>">Ver</a>
          <a href="/Projetos/projeto-caf-moderno/php/cliente/update.php?id=<?= $row['id_cliente'] ?>">Editar</a>
          <a class="danger" onclick="return confirm('Excluir este cliente?')" href="/Projetos/projeto-caf-moderno/php/cliente/delete.php?id=<?= $row['id_cliente'] ?>">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
