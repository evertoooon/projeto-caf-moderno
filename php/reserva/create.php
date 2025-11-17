<?php 
require_once __DIR__ . '/../conexao.php';
include_once __DIR__ . '/../partials/header.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_cliente  = (int)($_POST['id_cliente'] ?? 0);
  $id_mesa     = (int)($_POST['id_mesa'] ?? 0);
  $inicio_in   = trim($_POST['inicio'] ?? '');
  $fim_in      = trim($_POST['fim'] ?? '');
  $qtd         = (int)($_POST['qtd_pessoas'] ?? 0);
  $status      = $_POST['status'] ?? 'pendente';

  // validação básica dos campos
  if (!$id_cliente || !$id_mesa || !$inicio_in || !$fim_in || $qtd <= 0) {
    $erro = "Preencha todos os campos obrigatórios.";
  } else {

    // converte datas para formato datetime do banco
    $inicio = date('Y-m-d H:i:s', strtotime($inicio_in));
    $fim    = date('Y-m-d H:i:s', strtotime($fim_in));

    if (strtotime($fim) <= strtotime($inicio)) {
      $erro = "O fim deve ser depois do início.";
    } else {
      // verifica capacidade da mesa
      $cap = 0;
      $stmtCap = $conn->prepare("SELECT capacidade FROM mesa WHERE id_mesa = ?");
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

        // ===== VERIFICAÇÃO DE CONFLITO DE HORÁRIO NA MESMA MESA =====
        // Regra: existe conflito se houver alguma reserva da mesma mesa,
        // com status diferente de 'cancelada',
        // cujo intervalo [inicio_existente, fim_existente]
        // se sobrepõe ao novo intervalo [inicio, fim].
        //
        // Condição de sobreposição:
        // inicio_existente < novo_fim  AND  fim_existente > novo_inicio
        $sqlConf = "
          SELECT COUNT(*) 
          FROM reserva
          WHERE id_mesa = ?
            AND status <> 'cancelada'
            AND inicio < ?
            AND fim > ?
        ";
        $stmtConf = $conn->prepare($sqlConf);
        $stmtConf->bind_param("iss", $id_mesa, $fim, $inicio);
        $stmtConf->execute();
        $stmtConf->bind_result($qtdeConflitos);
        $stmtConf->fetch();
        $stmtConf->close();

        if ($qtdeConflitos > 0) {
          // se já existe reserva ativa na mesma faixa de horário
          $erro = "Já existe uma reserva ativa para essa mesa nesse horário.";
        } else {
          // ===== INSERÇÃO DA RESERVA =====
          $stmt = $conn->prepare("
            INSERT INTO reserva (inicio, fim, qtd_pessoas, status, id_cliente, id_mesa)
            VALUES (?, ?, ?, ?, ?, ?)
          ");
          $stmt->bind_param("ssisii", $inicio, $fim, $qtd, $status, $id_cliente, $id_mesa);

          if ($stmt->execute()) {
            header("Location: /Projetos/projeto-caf-moderno/php/reserva/index.php");
            exit;
          } else {
            $erro = "Erro ao inserir: " . $stmt->error;
          }
        }
      }
    }
  }
}

$data = [
  'id_cliente'  => $_POST['id_cliente']  ?? '',
  'id_mesa'     => $_POST['id_mesa']     ?? '',
  'inicio'      => $_POST['inicio']      ?? '',
  'fim'         => $_POST['fim']         ?? '',
  'qtd_pessoas' => $_POST['qtd_pessoas'] ?? '',
  'status'      => $_POST['status']      ?? 'pendente',
];
?>
<div class="container">
  <h1>Nova Reserva</h1>
  <?php if($erro): ?>
    <div class="badge" style="background:#b41323;color:#fff">
      <?= htmlspecialchars($erro) ?>
    </div>
  <?php endif; ?>

  <?php
    $action = "/Projetos/projeto-caf-moderno/php/reserva/create.php";
    $method = "POST";
    include __DIR__ . "/form.php";
  ?>
</div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
