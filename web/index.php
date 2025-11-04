<?php
// web/index.php
include_once __DIR__ . '/../php/partials/header.php';
?>
<div class="container">
  <h1>Café Moderno — Sistema</h1>
  <p>Bem-vindo! Use os atalhos abaixo ou a busca rápida de clientes.</p>

  <!-- Busca rápida de clientes (redireciona para o CRUD) -->
  <form class="form-entity" method="get" action="/Projetos/projeto-caf-moderno/php/cliente/index.php" style="margin:16px 0;">
    <div class="row">
      <div>
        <label>Buscar cliente</label>
        <input type="text" name="q" placeholder="Nome, e-mail ou telefone">
      </div>
    </div>
    <div class="actions">
      <button class="btn">Buscar</button>
      <a class="btn" href="/Projetos/projeto-caf-moderno/php/cliente/create.php">+ Novo Cliente</a>
    </div>
  </form>

  <!-- Atalhos principais -->
  <div class="form-entity">
    <div class="row-3">
      <div>
        <h3>Clientes</h3>
        <p>Gerencie cadastros de clientes.</p>
        <div class="actions">
          <a class="btn" href="/Projetos/projeto-caf-moderno/php/cliente/index.php">Abrir</a>
        </div>
      </div>
      <div>
        <h3>Mesas</h3>
        <p>Controle numeração e capacidade.</p>
        <div class="actions">
          <a class="btn" href="/Projetos/projeto-caf-moderno/php/mesa/index.php">Abrir</a>
        </div>
      </div>
      <div>
        <h3>Cardápio</h3>
        <p>Itens, preços e categorias.</p>
        <div class="actions">
          <a class="btn" href="/Projetos/projeto-caf-moderno/php/cardapioitem/index.php">Abrir</a>
        </div>
      </div>
    </div>

    <div class="row-3" style="margin-top:12px;">
      <div>
        <h3>Reservas</h3>
        <p>Agendamentos por mesa/cliente.</p>
        <div class="actions">
          <a class="btn" href="/Projetos/projeto-caf-moderno/php/reserva/index.php">Abrir</a>
        </div>
      </div>
      <div>
        <h3>Pedidos</h3>
        <p>Status e observações por cliente.</p>
        <div class="actions">
          <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido/index.php">Abrir</a>
        </div>
      </div>
      <div>
        <h3>Itens do Pedido</h3>
        <p>Relação N:N de pedidos e itens.</p>
        <div class="actions">
          <a class="btn" href="/Projetos/projeto-caf-moderno/php/pedido_item/index.php">Abrir</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once __DIR__ . '/../php/partials/footer.php'; ?>
