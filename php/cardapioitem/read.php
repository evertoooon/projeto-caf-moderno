<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

function slug($s) {
  $s = iconv('UTF-8','ASCII//TRANSLIT',$s);
  $s = preg_replace('/[^a-zA-Z0-9]+/','-', $s);
  $s = trim($s, '-');
  return strtolower($s);
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id_item, nome, descricao, preco, categoria, imagem FROM cardapioitem WHERE id_item=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

$imgFile = $item ? ($item['imagem'] ?: (slug($item['nome']).'.jpg')) : '';
$imgUrl  = $item ? "/Projetos/projeto-caf-moderno/img_cardapio/$imgFile" : '';
?>
<div class="container">
  <h1>Detalhes do Item</h1>
  <?php if(!$item): ?>
    <p>Item não encontrado.</p>
  <?php else: ?>
    <p><strong>ID:</strong> <?= $item['id_item'] ?></p>
    <p><strong>Nome:</strong> <?= htmlspecialchars($item['nome']) ?></p>
    <p><strong>Categoria:</strong> <?= htmlspecialchars($item['categoria']) ?></p>
    <p><strong>Preço:</strong> R$ <?= number_format((float)$item['preco'],2,',','.') ?></p>
    <?php if($imgFile): ?>
      <p><img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($item['nome']) ?>" width="220" height="220" style="object-fit:cover;border-radius:12px;"></p>
    <?php endif; ?>
    <p><strong>Descrição:</strong><br><?= nl2br(htmlspecialchars($item['descricao'])) ?></p>
    <div class="actions">
      <a href="/Projetos/projeto-caf-moderno/php/cardapioitem/update.php?id=<?= $item['id_item'] ?>">Editar</a>
      <a class="danger" onclick="return confirm('Excluir este item?')" href="/Projetos/projeto-caf-moderno/php/cardapioitem/delete.php?id=<?= $item['id_item'] ?>">Excluir</a>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/cardapioitem/index.php">Voltar</a>
    </div>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
