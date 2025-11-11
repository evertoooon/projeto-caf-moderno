<?php
  $ano = date('Y');
?>
<footer class="site-footer">
  <div class="footer-grid">
    <div class="foot-brand">
      <h3>Café Moderno</h3>
      <p>Gestão simples para um atendimento delicioso.</p>
    </div>

    <nav class="foot-nav">
      <h4>Navegação</h4>
      <ul>
        <li><a href="/Projetos/projeto-caf-moderno/web/index.php">Home</a></li>
        <li><a href="/Projetos/projeto-caf-moderno/php/cliente/index.php">Clientes</a></li>
        <li><a href="/Projetos/projeto-caf-moderno/php/mesa/index.php">Mesas</a></li>
        <li><a href="/Projetos/projeto-caf-moderno/php/cardapioitem/index.php">Cardápio</a></li>
        <li><a href="/Projetos/projeto-caf-moderno/php/reserva/index.php">Reservas</a></li>
        <li><a href="/Projetos/projeto-caf-moderno/php/pedido/index.php">Pedidos</a></li>
      </ul>
    </nav>

    <div class="foot-contact">
      <h4>Contato</h4>
      <ul>
        <li>Email: <a href="mailto:contato@cafemoderno.com">contato@cafemoderno.com</a></li>
        <li>Telefone: (54) 90000-0000</li>
        <li>Endereço: Rua do Café, 123 – Centro</li>
      </ul>
    </div>

    <div class="foot-social">
      <h4>GitHub</h4>
      <a href="https://github.com/evertoooon/projeto-caf-moderno"
         target="_blank" rel="noopener noreferrer"
         class="github-icon" title="Ver projeto no GitHub">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
             width="28" height="28" fill="#fff">
          <path d="M8 0C3.58 0 0 3.58 0 8a8.003 8.003 0 0 0 5.47 7.59c.4.07.55-.17.55-.38 
                   0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13
                   -.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.2 1.87.85 
                   2.33.65.07-.52.28-.85.51-1.05-1.78-.2-3.64-.89-3.64-3.95 
                   0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 
                   0 0 .67-.21 2.2.82a7.6 7.6 0 0 1 2-.27 7.6 7.6 0 0 1 2 .27 
                   c1.53-1.03 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12 
                   .51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95 
                   .29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 
                   0 .21.15.46.55.38A8.003 8.003 0 0 0 16 8c0-4.42-3.58-8-8-8Z"/>
        </svg>
      </a>
    </div>
  </div>

  <div class="footbar">
    <div class="footbar-content">
      <span>© <?= $ano ?> Café Moderno — Todos os direitos reservados.</span>
      <span class="made">Feito com ☕ e PHP + MySQL</span>
    </div>
  </div>
</footer>

<script>
  document.getElementById('toTop')?.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
</body>
</html>
