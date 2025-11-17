<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$busca  = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$where  = [];
$params = [];
$types  = '';

$sql = "SELECT r.id_reserva, r.inicio, r.fim, r.qtd_pessoas, r.status,
               c.id_cliente, c.nome  AS cliente,
               m.id_mesa,   m.numero AS mesa
        FROM reserva r
        JOIN cliente c ON c.id_cliente = r.id_cliente
        JOIN mesa    m ON m.id_mesa    = r.id_mesa";

if ($busca !== '') {
  $where[] = "(c.nome LIKE ? OR m.numero = ?)";
  $params[] = "%{$busca}%";
  $types   .= 'si';
  
  $params[] = ctype_digit($busca) ? (int)$busca : 0;
}

if ($status !== '') {
  $where[] = "r.status = ?";
  $params[] = $status;
  $types   .= 's';
}

if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY r.inicio DESC";

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();

function fmtdt($ts) { return date('d/m/Y H:i', strtotime($ts)); }

$statuses = ['pendente','confirmada','cancelada'];
?>
<div class="container">
  <h1>Reservas</h1>

  <form method="get" style="margin:12px 0; display:flex; gap:8px; align-items:center;">
    <input type="text" name="q" value="<?= htmlspecialchars($busca) ?>"
           placeholder="Cliente ou nº da mesa" />
    <select name="status">
      <option value="">— Status —</option>
      <?php foreach ($statuses as $st): ?>
        <option value="<?= $st ?>" <?= $status===$st?'selected':'' ?>><?= $st ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn">Filtrar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/reserva/create.php">+ Nova Reserva</a>
  </form>

  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Cliente</th>
        <th>Mesa</th>
        <th>Início</th>
        <th>Fim</th>
        <th>Pessoas</th>
        <th>Status</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php while($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $r['id_reserva'] ?></td>
        <td><?= htmlspecialchars($r['cliente']) ?></td>
        <td><?= (int)$r['mesa'] ?></td>
        <td><?= fmtdt($r['inicio']) ?></td>
        <td><?= fmtdt($r['fim']) ?></td>
        <td><?= (int)$r['qtd_pessoas'] ?></td>
        <td><span class="badge"><?= $r['status'] ?></span></td>
        <td class="actions">
          <a href="/Projetos/projeto-caf-moderno/php/reserva/read.php?id=<?= $r['id_reserva'] ?>">Ver</a>
          <a href="/Projetos/projeto-caf-moderno/php/reserva/update.php?id=<?= $r['id_reserva'] ?>">Editar</a>
          <a class="danger"
             onclick="return confirm('Excluir esta reserva?')"
             href="/Projetos/projeto-caf-moderno/php/reserva/delete.php?id=<?= $r['id_reserva'] ?>">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
