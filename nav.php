<?php
require_once __DIR__ . '/config.php';
$logged = is_logged_in();
?>
<header class="site-header">
  <div class="nav-inner">
    <a href="<?= url('index.php') ?>" class="brand">
      <svg viewBox="0 0 24 24" fill="none"><path d="M12 2c1 4-3 5-3 9a3 3 0 006 0c0-2-1-3-1-5 2 1 3 4 3 6a5 5 0 01-10 0c0-5 3-6 5-10z" fill="#C75F92"/></svg>
      H-DELLAYA
    </a>
    <button class="menu-toggle" aria-label="Menu" id="menuToggle">
      <span></span><span></span><span></span>
    </button>
    <nav class="nav-links" id="navLinks">
      <?php if ($logged): ?>
        <a href="<?= url('dashboard.php') ?>">Tableau de bord</a>
        <a href="<?= url('products.php') ?>">Produits</a>
        <a href="<?= url('categories.php') ?>">Catégories</a>
        <a href="<?= url('orders.php') ?>">Commandes</a>
        <a href="<?= url('settings.php') ?>">Paramètres</a>
        <a href="<?= url('index.php') ?>" target="_blank">Voir le site ↗</a>
        <a href="<?= url('logout.php') ?>" class="nav-danger">Déconnexion</a>
      <?php else: ?>
        <a href="<?= url('index.php') ?>#catalogue">Créations</a>
        <a href="<?= url('index.php') ?>#instagram">Instagram</a>
        <a href="<?= url('index.php') ?>#avis">Avis</a>
        <a href="<?= url('index.php') ?>#commander">Commander</a>
        <a href="<?= url('login.php') ?>" class="nav-admin">Admin</a>
      <?php endif; ?>
    </nav>
  </div>
</header>