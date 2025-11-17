<?php
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$id = (int)($_GET['id'] ?? 0);

// carrega atual
$stmt = $conn->prepare("SELECT * FROM reserva WHERE id_reserva=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$atual = $stmt->get_result()->fetch_assoc();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_cliente  = (int)($_POST['id_cliente'] ?? 0);
  $id_mesa     = (int)($_POST['id_mesa'] ?? 0);
  $inicio_in   = trim($_POST['inicio'] ?? '');
  $fim_in      = trim($_POST['fim'] ?? '');
  $qtd         = (int)($_POST['qtd_pessoas'] ?? 0);
  $status      = $_POST['status'] ?? 'pendente';

  if (!$id_cliente || !$id_mesa || !$inicio_in || !$fim_in || $qtd <= 0) {
    $erro = "Preencha todos os campos obrigatórios.";
  } else {
    $inicio = date('Y-m-d H:i:s', strtotime($inicio_in));
    $fim    = date('Y-m-d H:i:s', strtotime($fim_in));

    if (strtotime($fim) <= strtotime($inicio)) {
      $erro = "O fim deve ser depois do início.";
    } else {
      // verifica capacidade da mesa
      $cap = 0;
      $stmtCap = $conn->prepare("SELECT capacidade FROM mesa WHERE id_mesa=?");
      $stmtCap->bind_param("i", $id_mesa);
      $stmtCap->execute();
      $stmtCap->bind_result($cap);
      $stmtCap->fetch();
      $stmtCap->close();

      if ($cap <= 0) {
        $erro = "Mesa inválida.";
      } elseif ($qtd > $cap) {
        $erro = "Quantidade excede a capacidade da mesa (cap. {$cap}).";
      } else {

        // ===== VERIFICAÇÃO DE CONFLITO DE HORÁRIO NA MESMA MESA (EDITAR) =====
        // Ignora a própria reserva (id_reserva <> ?)
        $sqlConf = "
          SELECT COUNT(*)
          FROM reserva
          WHERE id_mesa = ?
            AND status <> 'cancelada'
            AND id_reserva <> ?
            AND inicio < ?
            AND fim > ?
        ";
        $stmtConf = $conn->prepare($sqlConf);
        $stmtConf->bind_param("iiss", $id_mesa, $id, $fim, $inicio);
        $stmtConf->execute();
        $stmtConf->bind_result($qtdeConflitos);
        $stmtConf->fetch();
        $stmtConf->close();

        if ($qtdeConflitos > 0) {
          $erro = "Já existe uma reserva ativa para essa mesa nesse horário.";
        } else {
          // ===== ATUALIZAÇÃO DA RESERVA =====
          $stmtU = $conn->prepare("UPDATE reserva
                                   SET inicio=?, fim=?, qtd_pessoas=?, status=?, id_cliente=?, id_mesa=?
                                   WHERE id_reserva=?");
          $stmtU->bind_param("ssisiii", $inicio, $fim, $qtd, $status, $id_cliente, $id_mesa, $id);
          if ($stmtU->execute()) {
            header("Location: /Projetos/projeto-caf-moderno/php/reserva/read.php?id=" . $id);
            exit;
          } else {
            $erro = "Erro ao atualizar: " . $stmtU->error;
          }
        }
      }
    }
  }

  // se deu erro, mantém o que o usuário digitou
  $atual = [
    'id_cliente'  => $id_cliente,
    'id_mesa'     => $id_mesa,
    'inicio'      => $inicio_in,
    'fim'         => $fim_in,
    'qtd_pessoas' => $qtd,
    'status'      => $status,
    'id_reserva'  => $id,
  ];
}

$data = $atual ?: [];
?>
<div class="container">
  <h1>Editar Reserva</h1>
  <?php if(!$atual): ?>
    <p>Reserva não encontrada.</p>
  <?php else: ?>
    <?php if($erro): ?>
      <div class="badge" style="background:#b41323;color:#fff">
        <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>
    <?php
      $action = "/Projetos/projeto-caf-moderno/php/reserva/update.php?id=" . $id;
      $method = "POST";
      include __DIR__ . "/form.php";
    ?>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
