<?php


$menu = [
  'Home'     => '/Projetos/projeto-caf-moderno/web/index.php',
  'Clientes' => '/Projetos/projeto-caf-moderno/php/cliente/index.php',
  'Mesas'    => '/Projetos/projeto-caf-moderno/php/mesa/index.php',
  'Cardápio' => '/Projetos/projeto-caf-moderno/php/cardapioitem/index.php',
  'Reservas' => '/Projetos/projeto-caf-moderno/php/reserva/index.php',
  'Pedidos'  => '/Projetos/projeto-caf-moderno/php/pedido/index.php',
];

$active = $currentTitle ?? 'Home';
?>

<div class="menu-top">
  <div class="container">
    <nav class="crumbbar" aria-label="Trilha de navegação">
      <?php foreach ($menu as $label => $href): 
        $isLast = ($label === $active);
        $tag = $isLast || !$href ? 'span' : 'a';
        $cls = 'crumb' . ($isLast ? ' is-last' : '');
        ?>
        <<?= $tag ?> class="<?= $cls ?>" <?= !$isLast && $href ? 'href="'.$href.'"' : '' ?>>
          <?php if ($label === 'Home'): ?>
            <span class="icon">🏠</span>
          <?php endif; ?>
          <?= htmlspecialchars($label) ?>
        </<?= $tag ?>>
      <?php endforeach; ?>
    </nav>
  </div>
</div>
