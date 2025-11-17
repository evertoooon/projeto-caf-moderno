<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id = (int)($_GET['id'] ?? 0);

$sql = "SELECT r.*, 
               c.nome AS cliente, 
               m.numero AS mesa, 
               m.capacidade
        FROM reserva r
        JOIN cliente c ON c.id_cliente = r.id_cliente
        JOIN mesa    m ON m.id_mesa    = r.id_mesa
        WHERE r.id_reserva = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

function fmtdt($ts){
  return date('d/m/Y H:i', strtotime($ts));
}
?>
<div class="container">
  <h1>Detalhes da Reserva</h1>

  <?php if(!$res): ?>
    <p>Reserva não encontrada.</p>

  <?php else: ?>

    <?php
      // === Estilos de status (mesmo padrão do index) ===
      $clsStatus = 'badge';
      if ($res['status'] === 'confirmada') {
        $clsStatus .= ' success';
      } elseif ($res['status'] === 'cancelada') {
        $clsStatus .= ' badge-cancelada';
      }
    ?>

    <div class="form-entity" style="max-width:600px;">

      <p><strong>ID:</strong> <?= $res['id_reserva'] ?></p>

      <p><strong>Cliente:</strong> 
        <?= htmlspecialchars($res['cliente']) ?>
      </p>

      <p><strong>Mesa:</strong> 
        <?= (int)$res['mesa'] ?> 
        <small>(cap. <?= (int)$res['capacidade'] ?>)</small>
      </p>

      <p><strong>Início:</strong> <?= fmtdt($res['inicio']) ?></p>
      <p><strong>Fim:</strong> <?= fmtdt($res['fim']) ?></p>

      <p><strong>Pessoas:</strong> <?= (int)$res['qtd_pessoas'] ?></p>

      <p><strong>Status:</strong> 
        <span class="<?= $clsStatus ?>"><?= htmlspecialchars($res['status']) ?></span>
      </p>

      <div class="actions" style="margin-top:25px;">
        <a class="btn" 
           href="/Projetos/projeto-caf-moderno/php/reserva/update.php?id=<?= $res['id_reserva'] ?>">
          Editar
        </a>

        <a class="btn danger"
           onclick="return confirm('Excluir esta reserva?')"
           href="/Projetos/projeto-caf-moderno/php/reserva/delete.php?id=<?= $res['id_reserva'] ?>">
          Excluir
        </a>

        <a class="btn" href="/Projetos/projeto-caf-moderno/php/reserva/index.php">
          Voltar
        </a>
      </div>
    </div>

  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
