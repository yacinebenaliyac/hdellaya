<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'status') {
        $allowed = ['nouveau','en_cours','livre','annule'];
        if (in_array($_POST['status'] ?? '', $allowed, true)) {
            $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'], $id]);
            flash('ok', 'Statut mis à jour.');
        }
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM orders WHERE id=?')->execute([$id]);
        flash('ok', 'Commande supprimée.');
    }
    redirect(url('orders.php'));
}

$filter = $_GET['status'] ?? '';
$sql = 'SELECT * FROM orders';
$params = [];
if (in_array($filter, ['nouveau','en_cours','livre','annule'], true)) {
    $sql .= ' WHERE status = ?';
    $params[] = $filter;
}
$sql .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$counts = ['tous'=>0,'nouveau'=>0,'en_cours'=>0,'livre'=>0,'annule'=>0];
foreach ($pdo->query('SELECT status, COUNT(*) n FROM orders GROUP BY status')->fetchAll() as $r) {
    $counts[$r['status']] = (int)$r['n'];
    $counts['tous'] += (int)$r['n'];
}

$page_title = 'Commandes — Admin';
$body_class = 'admin';
include __DIR__ . '/header.php';
?>
<main class="admin-main">
  <div class="admin-container">

    <div class="page-head">
      <div>
        <span class="page-tag">Commandes</span>
        <h1>Boîte de réception <span class="count"><?= $counts['tous'] ?></span></h1>
      </div>
    </div>

    <?php if ($m = flash('ok')): ?><div class="flash ok">✅ <?= e($m) ?></div><?php endif; ?>

    <div class="order-tabs">
      <a href="?" class="otab <?= $filter===''?'active':'' ?>">Toutes <b><?= $counts['tous'] ?></b></a>
      <a href="?status=nouveau" class="otab <?= $filter==='nouveau'?'active':'' ?>">🔔 Nouvelles <b><?= $counts['nouveau'] ?></b></a>
      <a href="?status=en_cours" class="otab <?= $filter==='en_cours'?'active':'' ?>">⏳ En cours <b><?= $counts['en_cours'] ?></b></a>
      <a href="?status=livre" class="otab <?= $filter==='livre'?'active':'' ?>">✅ Livrées <b><?= $counts['livre'] ?></b></a>
      <a href="?status=annule" class="otab <?= $filter==='annule'?'active':'' ?>">✖ Annulées <b><?= $counts['annule'] ?></b></a>
    </div>

    <?php if (!$orders): ?>
      <div class="panel"><p class="muted">Aucune commande ici.</p></div>
    <?php else: ?>
      <div class="orders-list">
        <?php foreach ($orders as $o): ?>
          <div class="order-row <?= $o['status']==='nouveau'?'new':'' ?>">
            <div class="order-head">
              <div class="order-client">
                <div class="order-avatar"><?= strtoupper(mb_substr($o['name'], 0, 1)) ?></div>
                <div>
                  <b><?= e($o['name']) ?></b>
                  <span class="order-date"><?= e(date('d/m/Y · H:i', strtotime($o['created_at']))) ?></span>
                </div>
              </div>
              <span class="pill <?= e($o['status']) ?>"><?= e(status_label($o['status'])) ?></span>
            </div>

            <div class="order-body">
              <div class="order-line"><span>📞</span><a href="tel:<?= e($o['phone']) ?>"><?= e($o['phone']) ?></a></div>
              <?php if ($o['address']): ?><div class="order-line"><span>📍</span><?= e($o['address']) ?></div><?php endif; ?>
              <?php if ($o['items']): ?><div class="order-line"><span>🎁</span><pre class="order-items"><?= e(rtrim($o['items'])) ?></pre></div><?php endif; ?>
              <?php if ($o['notes']): ?><div class="order-line"><span>📝</span><i><?= nl2br(e($o['notes'])) ?></i></div><?php endif; ?>
              <?php if ($o['total'] > 0): ?><div class="order-line total"><span>💰</span><b>Total : <?= money($o['total']) ?></b></div><?php endif; ?>
            </div>

            <div class="order-actions">
              <form method="post">
                <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
                <input type="hidden" name="action" value="status">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <select name="status" onchange="this.form.submit()">
                  <option value="nouveau"  <?= $o['status']==='nouveau'?'selected':'' ?>>🔔 Nouveau</option>
                  <option value="en_cours" <?= $o['status']==='en_cours'?'selected':'' ?>>⏳ En cours</option>
                  <option value="livre"    <?= $o['status']==='livre'?'selected':'' ?>>✅ Livré</option>
                  <option value="annule"   <?= $o['status']==='annule'?'selected':'' ?>>✖ Annulé</option>
                </select>
              </form>
              <form method="post" onsubmit="return confirm('Supprimer cette commande ?');">
                <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <button class="btn-mini danger" type="submit">🗑 Supprimer</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>