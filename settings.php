<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $fields = ['brand','phone','phone_intl','instagram','tiktok','location',
               'hero_kicker','hero_title','hero_lead',
               'ig_posts','ig_followers','ig_following','announce'];
    $stmt = $pdo->prepare('REPLACE INTO settings (`key`, `value`) VALUES (?, ?)');
    foreach ($fields as $f) if (isset($_POST[$f])) $stmt->execute([$f, trim($_POST[$f])]);
    flash('ok', 'Paramètres enregistrés.');
    redirect(url('settings.php'));
}

$s = [];
foreach ($pdo->query('SELECT `key`, `value` FROM settings')->fetchAll() as $r) $s[$r['key']] = $r['value'];

$page_title = 'Paramètres — Admin';
$body_class = 'admin';
include __DIR__ . '/header.php';
?>
<main class="admin-main">
  <div class="admin-container">

    <div class="page-head">
      <div>
        <span class="page-tag">Configuration</span>
        <h1>Paramètres du site</h1>
      </div>
    </div>

    <?php if ($m = flash('ok')): ?><div class="flash ok">✅ <?= e($m) ?></div><?php endif; ?>

    <form method="post" class="form-grid">
      <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">

      <div class="panel">
        <div class="panel-head"><h2>🏷️ Identité</h2></div>
        <div class="form-row">
          <label>Nom de la marque
            <input type="text" name="brand" value="<?= e($s['brand'] ?? '') ?>">
          </label>
          <label>Localisation
            <input type="text" name="location" value="<?= e($s['location'] ?? '') ?>">
          </label>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>📞 Contact & commande</h2></div>
        <div class="form-row">
          <label>Téléphone affiché
            <input type="text" name="phone" value="<?= e($s['phone'] ?? '') ?>">
          </label>
          <label>Téléphone international (SMS)
            <input type="text" name="phone_intl" placeholder="+213696352703" value="<?= e($s['phone_intl'] ?? '') ?>">
            <small class="hint">Format : + indicatif pays + numéro, sans espaces.</small>
          </label>
        </div>
        <div class="form-row">
          <label>Instagram (URL)
            <input type="text" name="instagram" value="<?= e($s['instagram'] ?? '') ?>">
          </label>
          <label>TikTok (URL)
            <input type="text" name="tiktok" value="<?= e($s['tiktok'] ?? '') ?>">
          </label>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>✨ Section héro</h2></div>
        <label>Bandeau annonce (haut du site)
          <input type="text" name="announce" value="<?= e($s['announce'] ?? '') ?>">
        </label>
        <label>Kicker
          <input type="text" name="hero_kicker" value="<?= e($s['hero_kicker'] ?? '') ?>">
        </label>
        <label>Titre <small class="hint">Entoure un mot de * pour l'afficher en italique rose.</small>
          <input type="text" name="hero_title" value="<?= e($s['hero_title'] ?? '') ?>">
        </label>
        <label>Texte
          <textarea name="hero_lead" rows="3"><?= e($s['hero_lead'] ?? '') ?></textarea>
        </label>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>📊 Statistiques Instagram</h2></div>
        <div class="form-row-3">
          <label>Publications
            <input type="text" name="ig_posts" value="<?= e($s['ig_posts'] ?? '') ?>">
          </label>
          <label>Abonnés
            <input type="text" name="ig_followers" value="<?= e($s['ig_followers'] ?? '') ?>">
          </label>
          <label>Suivi(e)s
            <input type="text" name="ig_following" value="<?= e($s['ig_following'] ?? '') ?>">
          </label>
        </div>
      </div>

      <div class="form-actions sticky-actions">
        <button type="submit" class="btn-primary">💾 Enregistrer les paramètres</button>
      </div>
    </form>

  </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>