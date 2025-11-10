<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

function slug($s) {
  $s = iconv('UTF-8','ASCII//TRANSLIT',$s);
  $s = preg_replace('/[^a-zA-Z0-9]+/','-', $s);
  $s = trim($s, '-');
  return strtolower($s);
}

$busca = $_GET['q'] ?? '';
$categoria = $_GET['cat'] ?? '';

$sql = "SELECT id_item, nome, descricao, preco, categoria, imagem FROM cardapioitem";
$params = [];
$w = [];

if ($busca !== '') {
  $w[] = "(nome LIKE ? OR descricao LIKE ?)";
  $like = "%$busca%";
  $params[] = $like; $params[] = $like;
}
if ($categoria !== '') {
  $w[] = "categoria = ?";
  $params[] = $categoria;
}
if ($w) $sql .= ' WHERE ' . implode(' AND ', $w);
$sql .= " ORDER BY categoria, nome";

$stmt = $conn->prepare($sql);
if ($params) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="container">
  <h1>Cardápio</h1>

  <form method="get" style="margin:12px 0; display:flex; gap:8px; flex-wrap:wrap;">
    <input type="text" name="q" placeholder="Buscar por nome/descrição" value="<?= htmlspecialchars($busca) ?>">
    <select name="cat">
      <option value="">Todas as categorias</option>
      <?php foreach (['bebida','sobremesa','lanche','outro'] as $cat): ?>
        <option value="<?= $cat ?>" <?= $categoria===$cat?'selected':'' ?>><?= ucfirst($cat) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn">Buscar</button>
    <a class="btn" href="/Projetos/projeto-caf-moderno/php/cardapioitem/create.php">+ Novo Item</a>
  </form>

  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Imagem</th>
        <th>Nome</th>
        <th>Categoria</th>
        <th>Preço (R$)</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $res->fetch_assoc()): 
        // imagem: usa a coluna; se vazia, tenta derivar: slug(nome).jpg
        $file = $row['imagem'] ?: (slug($row['nome']).'.jpg');
        $imgUrl = "/Projetos/projeto-caf-moderno/img_cardapio/" . $file;
      ?>
        <tr>
          <td><?= $row['id_item'] ?></td>
          <td>
            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($row['nome']) ?>" width="64" height="64" style="object-fit:cover;border-radius:8px;">
          </td>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td><span class="badge"><?= htmlspecialchars($row['categoria']) ?></span></td>
          <td><?= number_format((float)$row['preco'],2,',','.') ?></td>
          <td class="actions">
            <a href="/Projetos/projeto-caf-moderno/php/cardapioitem/read.php?id=<?= $row['id_item'] ?>">Ver</a>
            <a href="/Projetos/projeto-caf-moderno/php/cardapioitem/update.php?id=<?= $row['id_item'] ?>">Editar</a>
            <a class="danger" onclick="return confirm('Excluir este item?')" href="/Projetos/projeto-caf-moderno/php/cardapioitem/delete.php?id=<?= $row['id_item'] ?>">Excluir</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
