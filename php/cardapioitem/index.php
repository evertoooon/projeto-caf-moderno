<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$busca     = trim($_GET['q']   ?? '');
$categoria = trim($_GET['cat'] ?? '');
$ord       = $_GET['ord']      ?? 'id';

$sql    = "SELECT id_item, nome, categoria, preco, imagem FROM cardapioitem";
$conds  = [];
$params = [];
$types  = "";

// filtro por texto (nome/categoria)
if ($busca !== '') {
  $conds[]  = "(nome LIKE ? OR categoria LIKE ?)";
  $like     = "%{$busca}%";
  $params[] = $like; $params[] = $like;
  $types   .= "ss";
}

// filtro por categoria exata
if ($categoria !== '') {
  $conds[]  = "categoria = ?";
  $params[] = $categoria;
  $types   .= "s";
}

// aplica WHERE apenas se tiver condição
if ($conds) {
  $sql .= " WHERE " . implode(" AND ", $conds);
}

// ordenação
if ($ord === 'categoria') {
  $sql .= " ORDER BY categoria ASC, nome ASC";
} else {
  $sql .= " ORDER BY id_item ASC";
}

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="container">
  <h1>Cardápio</h1>

  <form method="get" style="margin:12px 0; display:flex; gap:8px; flex-wrap:wrap;">
    <input type="text" name="q" placeholder="Buscar por nome/categoria" value="<?= htmlspecialchars($busca) ?>">
    <select name="cat">
      <option value="">Todas as categorias</option>
      <?php foreach (['bebida','sobremesa','lanche','outro'] as $c): ?>
        <option value="<?= $c ?>" <?= $categoria===$c?'selected':'' ?>><?= ucfirst($c) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="ord">
      <option value="id" <?= $ord==='id'?'selected':'' ?>>Ordenar por ID</option>
      <option value="categoria" <?= $ord==='categoria'?'selected':'' ?>>Ordenar por categoria</option>
    </select>
    <button class="btn">Filtrar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/cardapioitem/create.php">+ Novo Item</a>
  </form>

  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Imagem</th>
        <th>Nome</th>
        <th>Categoria</th>
        <th>Preço</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): 
      $file = $row['imagem'] ?? '';
      $src  = $file
        ? "/Projetos/projeto-caf-moderno/img_cardapio/{$file}"
        : "/Projetos/projeto-caf-moderno/img_cardapio/placeholder.jpg";
    ?>
      <tr>
        <td><?= $row['id_item'] ?></td>
        <td>
          <img src="<?= htmlspecialchars($src) ?>"
               alt="<?= htmlspecialchars($row['nome']) ?>"
               style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
        </td>
        <td><?= htmlspecialchars($row['nome']) ?></td>
        <td><span class="badge"><?= htmlspecialchars($row['categoria']) ?></span></td>
        <td><?= number_format((float)$row['preco'], 2, ',', '.') ?></td>
        <td class="actions">
          <a href="/Projetos/projeto-caf-moderno/php/cardapioitem/read.php?id=<?= $row['id_item'] ?>">Ver</a>
          <a href="/Projetos/projeto-caf-moderno/php/cardapioitem/update.php?id=<?= $row['id_item'] ?>">Editar</a>
          <a class="danger"
             onclick="return confirm('Excluir este item do cardápio?')"
             href="/Projetos/projeto-caf-moderno/php/cardapioitem/delete.php?id=<?= $row['id_item'] ?>">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
