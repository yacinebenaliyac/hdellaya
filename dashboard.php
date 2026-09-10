<?php
require_once __DIR__ . '/auth.php';
require_login();

$stats = [
    'products'   => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'categories' => (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'orders'     => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'new_orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='nouveau'")->fetchColumn(),
    'revenue'    => (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='livre'")->fetchColumn(),
];
$latest = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 6')->fetchAll();

$page_title = 'Tableau de bord — Admin';
$body_class = 'admin';
include __DIR__ . '/header.php';
?>
<main class="admin-main">
  <div class="admin-container">

    <div class="page-head">
      <div>
        <span class="page-tag">Vue d'ensemble</span>
        <h1>Tableau de bord</h1>
      </div>
      <a href="<?= url('products.php') ?>" class="btn btn-primary">+ Nouveau produit</a>
    </div>

    <?php if ($m = flash('ok')): ?><div class="flash ok">✅ <?= e($m) ?></div><?php endif; ?>

    <div class="stats-grid">
      <a href="<?= url('products.php') ?>" class="stat-card">
        <div class="stat-icon">🛍️</div>
        <div class="stat-num"><?= $stats['products'] ?></div>
        <div class="stat-label">Produits</div>
      </a>
      <a href="<?= url('categories.php') ?>" class="stat-card">
        <div class="stat-icon">📂</div>
        <div class="stat-num"><?= $stats['categories'] ?></div>
        <div class="stat-label">Catégories</div>
      </a>
      <a href="<?= url('orders.php') ?>" class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-num"><?= $stats['orders'] ?></div>
        <div class="stat-label">Commandes</div>
      </a>
      <a href="<?= url('orders.php') ?>" class="stat-card highlight">
        <div class="stat-icon">🔔</div>
        <div class="stat-num"><?= $stats['new_orders'] ?></div>
        <div class="stat-label">Nouvelles commandes</div>
      </a>
      <div class="stat-card money">
        <div class="stat-icon">💰</div>
        <div class="stat-num"><?= money($stats['revenue']) ?></div>
        <div class="stat-label">Chiffre livré</div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <h2>Dernières commandes</h2>
        <a href="<?= url('orders.php') ?>" class="btn-ghost">Tout voir →</a>
      </div>
      <?php if (!$latest): ?>
        <p class="muted">Aucune commande pour l'instant.</p>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>Date</th><th>Client</th><th>Téléphone</th><th>Total</th><th>Statut</th></tr></thead>
            <tbody>
              <?php foreach ($latest as $o): ?>
                <tr>
                  <td class="nowrap"><?= e(date('d/m H:i', strtotime($o['created_at']))) ?></td>
                  <td><b><?= e($o['name']) ?></b></td>
                  <td><a href="tel:<?= e($o['phone']) ?>"><?= e($o['phone']) ?></a></td>
                  <td><b><?= money($o['total']) ?></b></td>
                  <td><span class="pill <?= e($o['status']) ?>"><?= e(status_label($o['status'])) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>