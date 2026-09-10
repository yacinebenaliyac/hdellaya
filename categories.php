<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['name'] ?? '');
        $emoji = trim($_POST['emoji'] ?? '🌸');
        $slug  = trim($_POST['slug'] ?? '');
        if ($slug === '') {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $name)));
            $slug = trim($slug, '-');
        }
        $pos = (int)($_POST['position'] ?? 0);

        if ($name === '' || $slug === '') {
            flash('ok', 'Nom obligatoire.');
        } elseif ($id > 0) {
            $pdo->prepare('UPDATE categories SET slug=?, name=?, emoji=?, position=? WHERE id=?')
                ->execute([$slug, $name, $emoji, $pos, $id]);
            flash('ok', 'Catégorie mise à jour.');
        } else {
            $pdo->prepare('INSERT INTO categories (slug, name, emoji, position) VALUES (?, ?, ?, ?)')
                ->execute([$slug, $name, $emoji, $pos]);
            flash('ok', 'Catégorie ajoutée.');
        }
        redirect(url('categories.php'));
    }

    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([(int)$_POST['id']]);
        flash('ok', 'Catégorie supprimée.');
        redirect(url('categories.php'));
    }
}

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$categories = $pdo->query(
    'SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) AS n
     FROM categories c ORDER BY position, id'
)->fetchAll();

$page_title = 'Catégories — Admin';
$body_class = 'admin';
include __DIR__ . '/header.php';
?>
<main class="admin-main">
  <div class="admin-container">

    <div class="page-head">
      <div>
        <span class="page-tag">Organisation</span>
        <h1>Catégories <span class="count"><?= count($categories) ?></span></h1>
      </div>
    </div>

    <?php if ($m = flash('ok')): ?><div class="flash ok">✅ <?= e($m) ?></div><?php endif; ?>

    <div class="panel">
      <div class="panel-head">
        <h2><?= $edit ? '✏️ Modifier' : '➕ Nouvelle catégorie' ?></h2>
        <?php if ($edit): ?><a href="<?= url('categories.php') ?>" class="btn-ghost">Annuler</a><?php endif; ?>
      </div>
      <form method="post" class="form-grid">
        <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">

        <div class="form-row">
          <label>Nom affiché *
            <input type="text" name="name" required value="<?= e($edit['name'] ?? '') ?>" placeholder="Ex: Bouquets en cire">
          </label>
          <label>Emoji
            <input type="text" name="emoji" maxlength="4" value="<?= e($edit['emoji'] ?? '🌸') ?>">
          </label>
        </div>
        <div class="form-row">
          <label>Slug (URL — laisser vide pour auto)
            <input type="text" name="slug" value="<?= e($edit['slug'] ?? '') ?>" placeholder="bouquets-cire">
          </label>
          <label>Position
            <input type="number" name="position" value="<?= (int)($edit['position'] ?? 0) ?>">
          </label>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn-primary"><?= $edit ? '💾 Mettre à jour' : '✨ Ajouter' ?></button>
        </div>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head"><h2>Liste</h2></div>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Pos.</th><th>Catégorie</th><th>Slug</th><th>Produits</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($categories as $c): ?>
              <tr>
                <td class="muted"><?= (int)$c['position'] ?></td>
                <td><b><?= e($c['emoji']) ?> <?= e($c['name']) ?></b></td>
                <td><code><?= e($c['slug']) ?></code></td>
                <td><span class="pill soft"><?= (int)$c['n'] ?></span></td>
                <td class="row-actions">
                  <a href="?edit=<?= (int)$c['id'] ?>" class="btn-mini">✏️</a>
                  <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ?');">
                    <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                    <button class="btn-mini danger" type="submit">🗑</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>