<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$busca  = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$where = [];
$params = [];
$types = '';

$sql = "SELECT p.id_pedido, p.observacao, p.status, p.data_hora,
               c.id_cliente, c.nome AS cliente
        FROM pedido p
        JOIN cliente c ON c.id_cliente = p.id_cliente";

if ($busca !== '') {
  $where[] = "c.nome LIKE ?";
  $params[] = "%{$busca}%";
  $types   .= 's';
}
if ($status !== '') {
  $where[] = "p.status = ?";
  $params[] = $status;
  $types   .= 's';
}
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY p.id_pedido DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$statuses = ['em_preparo','pronto','entregue','cancelado'];
function fmt($ts){ return date('d/m/Y H:i', strtotime($ts)); }
?>
<div class="container">
  <h1>Pedidos</h1>

  <form method="get" style="margin:12px 0; display:flex; gap:8px; align-items:center;">
    <input type="text" name="q" placeholder="Cliente" value="<?= htmlspecialchars($busca) ?>">
    <select name="status">
      <option value="">— Status —</option>
      <?php foreach($statuses as $st): ?>
        <option value="<?= $st ?>" <?= $status===$st?'selected':'' ?>><?= $st ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn">Filtrar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido/create.php">+ Novo Pedido</a>
  </form>

  <table class="table">
    <thead>
    <tr>
      <th>#</th><th>Cliente</th><th>Status</th><th>Data/Hora</th><th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php while($p = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $p['id_pedido'] ?></td>
        <td><?= htmlspecialchars($p['cliente']) ?></td>
        <td><span class="badge"><?= $p['status'] ?></span></td>
        <td><?= fmt($p['data_hora']) ?></td>
        <td class="actions">
        <a href="/Projetos/projeto-caf-moderno/php/pedido/read.php?id=<?= $p['id_pedido'] ?>">Ver</a>
        <a href="/Projetos/projeto-caf-moderno/php/pedido/update.php?id=<?= $p['id_pedido'] ?>">Editar</a>
        <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido_item/index.php?id_pedido=<?= $p['id_pedido'] ?>">Itens</a>
        <a class="danger" onclick="return confirm('Excluir este pedido (itens serão apagados)?')"
        href="/Projetos/projeto-caf-moderno/php/pedido/delete.php?id=<?= $p['id_pedido'] ?>">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
